<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'route_id',
        'trip_date',
        'departure_time',
        'expected_arrival_time',
        'total_seats',
        'available_seats',
        'fare',
        'pickup_point',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'trip_date'       => 'date',
            'total_seats'     => 'integer',
            'available_seats' => 'integer',
            'fare'            => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Trip $trip) {
            if ($trip->total_seats === null) {
                $trip->total_seats = $trip->available_seats !== null
                    ? (int) $trip->available_seats
                    : ($trip->vehicle?->total_seats ?: 14);
            }
        });
    }

    public function isScheduled(): bool
    {
        return strtolower($this->status) === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return strtolower($this->status) === 'completed';
    }

    public function isCancelled(): bool
    {
        return strtolower($this->status) === 'cancelled';
    }

    public function getStartDateTime(): \Carbon\Carbon
    {
        $dateStr = $this->trip_date ? $this->trip_date->format('Y-m-d') : date('Y-m-d');
        return \Carbon\Carbon::parse($dateStr . ' ' . $this->departure_time);
    }

    public function getEndDateTime(): \Carbon\Carbon
    {
        $start = $this->getStartDateTime();

        if ($this->expected_arrival_time) {
            $dateStr = $this->trip_date ? $this->trip_date->format('Y-m-d') : date('Y-m-d');
            $arrTimeStr = is_string($this->expected_arrival_time) ? substr($this->expected_arrival_time, 0, 8) : $this->expected_arrival_time;
            $end = \Carbon\Carbon::parse($dateStr . ' ' . $arrTimeStr);

            // If expected arrival is less than or equal to departure time, it crosses midnight
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
            return $end;
        }

        // Check if there are trip stops with expected times
        $lastStop = $this->relationLoaded('tripStops') 
            ? $this->tripStops->sortByDesc('stop_order')->first()
            : $this->tripStops()->orderByDesc('stop_order')->first();

        if ($lastStop && $lastStop->expected_time) {
            $dateStr = $this->trip_date ? $this->trip_date->format('Y-m-d') : date('Y-m-d');
            $end = \Carbon\Carbon::parse($dateStr . ' ' . $lastStop->expected_time);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
            return $end;
        }

        // Default estimated duration: 2 hours
        return (clone $start)->addHours(2);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function tripStops(): HasMany
    {
        return $this->hasMany(TripStop::class)->orderBy('stop_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function tripMessages(): HasMany
    {
        return $this->hasMany(TripMessage::class)->latest();
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Check if a driver has an overlapping trip schedule.
     * Returns the conflicting Trip instance if an overlap is found, or null.
     */
    public static function checkDriverTripOverlap($driverId, $tripDate, $departureTime, $expectedArrivalTime = null, $excludeTripId = null): ?self
    {
        $dateStr = is_string($tripDate) ? $tripDate : ($tripDate instanceof \DateTimeInterface ? $tripDate->format('Y-m-d') : (string) $tripDate);
        $depTimeStr = is_string($departureTime) ? substr($departureTime, 0, 8) : (string) $departureTime;

        $start1 = \Carbon\Carbon::parse($dateStr . ' ' . $depTimeStr);

        if ($expectedArrivalTime) {
            $arrTimeStr = is_string($expectedArrivalTime) ? substr($expectedArrivalTime, 0, 8) : (string) $expectedArrivalTime;
            $end1 = \Carbon\Carbon::parse($dateStr . ' ' . $arrTimeStr);
            if ($end1->lessThanOrEqualTo($start1)) {
                $end1->addDay();
            }
        } else {
            $end1 = (clone $start1)->addHours(2);
        }

        $existingTrips = self::where('user_id', $driverId)
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['cancelled', 'completed'])
            ->when($excludeTripId, function ($q) use ($excludeTripId) {
                $q->where('id', '!=', $excludeTripId);
            })
            ->with(['route', 'tripStops'])
            ->get();

        foreach ($existingTrips as $trip) {
            $start2 = $trip->getStartDateTime();
            $end2   = $trip->getEndDateTime();

            // Intervals overlap if ($start1 < $end2) AND ($end1 > $start2)
            if ($start1->lessThan($end2) && $end1->greaterThan($start2)) {
                return $trip;
            }
        }

        return null;
    }

    /**
     * Get sequentially ordered stops along this trip's complete journey.
     * Normalized array with sequential indices 0 to N-1.
     */
    public function getOrderedStops(): array
    {
        $ordered = [];

        // 1. Gather stops from pre-configured route
        $route = $this->relationLoaded('route')
            ? $this->route
            : $this->route()->with(['routeStops.stop', 'startStop', 'endStop'])->first();

        if ($route) {
            $startId   = $route->start_stop_id;
            $startName = $route->start_location ?? $route->startStop?->name ?? 'Origin';

            $ordered[] = [
                'stop_id'       => $startId,
                'name'          => $startName,
                'location'      => $route->startStop?->location ?? $startName,
                'order'         => 0,
                'expected_time' => $this->departure_time,
            ];

            if ($route->routeStops && $route->routeStops->isNotEmpty()) {
                foreach ($route->routeStops->sortBy('stop_order') as $rs) {
                    $ordered[] = [
                        'stop_id'       => $rs->stop_id,
                        'name'          => $rs->stop?->name ?? 'Stop #' . $rs->stop_id,
                        'location'      => $rs->stop?->location ?? '',
                        'order'         => (int) $rs->stop_order,
                        'expected_time' => null,
                    ];
                }
            }

            $endId    = $route->end_stop_id;
            $endName  = $route->end_location ?? $route->endStop?->name ?? 'Destination';
            $maxOrder = empty($ordered) ? 10 : (max(array_column($ordered, 'order')) + 1);

            $ordered[] = [
                'stop_id'       => $endId,
                'name'          => $endName,
                'location'      => $route->endStop?->location ?? $endName,
                'order'         => $maxOrder,
                'expected_time' => $this->expected_arrival_time,
            ];
        } else {
            $ordered[] = [
                'stop_id'       => null,
                'name'          => $this->pickup_point ?: 'Origin',
                'location'      => $this->pickup_point ?: 'Origin',
                'order'         => 0,
                'expected_time' => $this->departure_time,
            ];
            $ordered[] = [
                'stop_id'       => null,
                'name'          => 'Destination',
                'location'      => 'Destination',
                'order'         => 10,
                'expected_time' => $this->expected_arrival_time,
            ];
        }

        // 2. Overlay TripStops if configured specifically on this trip
        $tripStops = $this->relationLoaded('tripStops')
            ? $this->tripStops
            : $this->tripStops()->with('stop')->orderBy('stop_order')->get();

        if ($tripStops && $tripStops->isNotEmpty()) {
            foreach ($tripStops as $ts) {
                $matched = false;
                foreach ($ordered as $k => $item) {
                    if ($ts->stop_id && $item['stop_id'] == $ts->stop_id) {
                        $ordered[$k]['expected_time'] = $ts->expected_time ?: $ordered[$k]['expected_time'];
                        if ($ts->stop_order) {
                            $ordered[$k]['order'] = (int) $ts->stop_order;
                        }
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    $ordered[] = [
                        'stop_id'       => $ts->stop_id,
                        'name'          => $ts->stop?->name ?? 'Waypoint',
                        'location'      => $ts->stop?->location ?? '',
                        'order'         => (int) ($ts->stop_order ?: 5),
                        'expected_time' => $ts->expected_time,
                    ];
                }
            }
        }

        // 3. Deduplicate by stop_id or name
        $unique = [];
        $seenIds = [];
        $seenNames = [];
        foreach ($ordered as $stop) {
            $idKey   = $stop['stop_id'];
            $nameKey = strtolower(trim($stop['name']));
            if (($idKey && in_array($idKey, $seenIds, true)) || in_array($nameKey, $seenNames, true)) {
                continue;
            }
            if ($idKey) {
                $seenIds[] = $idKey;
            }
            $seenNames[] = $nameKey;
            $unique[]    = $stop;
        }

        // 4. Sort sequentially by 'order'
        usort($unique, fn($a, $b) => $a['order'] <=> $b['order']);

        // Normalize indices from 0 to N-1
        $final = [];
        foreach ($unique as $idx => $s) {
            $s['index'] = $idx;
            $final[]    = $s;
        }

        return $final;
    }

    /**
     * Find the sequential stop index (0 to N-1) for a given stop identifier.
     * Identifier can be a numeric stop_id or a stop name string.
     */
    public function findStopIndex($identifier, ?array $orderedStops = null): ?int
    {
        if ($identifier === null || $identifier === '') {
            return null;
        }

        $stops = $orderedStops ?? $this->getOrderedStops();

        // 1. By numeric stop_id
        if (is_numeric($identifier)) {
            $id = (int) $identifier;
            foreach ($stops as $idx => $stop) {
                if (isset($stop['stop_id']) && (int) $stop['stop_id'] === $id) {
                    return $idx;
                }
            }
        }

        // 2. By exact name (case-insensitive)
        $clean = strtolower(trim((string) $identifier));
        foreach ($stops as $idx => $stop) {
            if (strtolower(trim($stop['name'])) === $clean) {
                return $idx;
            }
        }

        // 3. By partial substring match (e.g. "Nagar" in "Nagar Terminal")
        foreach ($stops as $idx => $stop) {
            $stopName = strtolower(trim($stop['name']));
            if (stripos($stopName, $clean) !== false || stripos($clean, $stopName) !== false) {
                return $idx;
            }
        }

        return null;
    }

    /**
     * Calculate available seats specifically for the requested route segment.
     *
     * Example: If a passenger books Nagar to Hunza, those seats are occupied only
     * on the Nagar-Hunza segment and remain available on the Gilgit-Nagar segment.
     */
    public function getSegmentAvailableSeats($fromIdentifier = null, $toIdentifier = null): int
    {
        $orderedStops = $this->getOrderedStops();
        $numStops     = count($orderedStops);

        // Active confirmed bookings
        $activeBookings = $this->bookings()
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(booking_status)'), ['cancelled', 'rejected'])
            ->get();

        $activeBookedSum = (int) $activeBookings->sum('seats');

        // Determine baseline total capacity
        if ($this->total_seats !== null) {
            $totalCapacity = (int) $this->total_seats;
        } elseif ($activeBookedSum === 0) {
            $totalCapacity = (int) ($this->available_seats ?? ($this->vehicle?->total_seats ?: 14));
        } else {
            $totalCapacity = (int) ($this->vehicle?->total_seats ?: (($this->available_seats ?? 0) + $activeBookedSum));
        }

        // If trip was specifically configured with available_seats = 0 and has 0 bookings, it is fully booked
        if (($this->available_seats !== null && (int) $this->available_seats <= 0 && $activeBookedSum === 0) || $totalCapacity <= 0) {
            return 0;
        }

        if ($numStops < 2) {
            return max(0, $totalCapacity - $activeBookedSum);
        }

        $numSegments = $numStops - 1;

        // Resolve requested boarding index and drop-off index
        $reqFromIdx = $fromIdentifier !== null ? $this->findStopIndex($fromIdentifier, $orderedStops) : 0;
        $reqToIdx   = $toIdentifier !== null ? $this->findStopIndex($toIdentifier, $orderedStops) : ($numStops - 1);

        if ($reqFromIdx === null) {
            $reqFromIdx = 0;
        }
        if ($reqToIdx === null) {
            $reqToIdx = $numStops - 1;
        }

        if ($reqFromIdx >= $reqToIdx) {
            return 0; // Invalid travel direction or identical stops
        }

        // Calculate occupied seats for each elementary segment k (between stop k and stop k+1)
        $occupiedPerSegment = array_fill(0, $numSegments, 0);

        foreach ($activeBookings as $b) {
            $bFrom = $b->from_stop_id
                ? $this->findStopIndex($b->from_stop_id, $orderedStops)
                : ($b->pickup_location ? $this->findStopIndex($b->pickup_location, $orderedStops) : 0);

            $bTo = $b->to_stop_id
                ? $this->findStopIndex($b->to_stop_id, $orderedStops)
                : ($b->destination_location ? $this->findStopIndex($b->destination_location, $orderedStops) : ($numStops - 1));

            if ($bFrom === null) {
                $bFrom = 0;
            }
            if ($bTo === null) {
                $bTo = $numStops - 1;
            }

            if ($bFrom >= $bTo) {
                $bFrom = 0;
                $bTo   = $numStops - 1;
            }

            // Occupy all segments covered by this booking
            for ($k = $bFrom; $k < $bTo && $k < $numSegments; $k++) {
                $occupiedPerSegment[$k] += (int) $b->seats;
            }
        }

        // Find the minimum available capacity along requested path [reqFromIdx, reqToIdx)
        $minAvailable = $totalCapacity;
        for ($k = $reqFromIdx; $k < $reqToIdx && $k < $numSegments; $k++) {
            $avail = max(0, $totalCapacity - $occupiedPerSegment[$k]);
            if ($avail < $minAvailable) {
                $minAvailable = $avail;
            }
        }

        return $minAvailable;
    }

    /**
     * Get list of occupied seat numbers on a given segment.
     */
    public function getOccupiedSeatNumbersForSegment($fromIdentifier = null, $toIdentifier = null): array
    {
        $orderedStops = $this->getOrderedStops();
        $numStops     = count($orderedStops);

        $reqFromIdx = $fromIdentifier !== null ? $this->findStopIndex($fromIdentifier, $orderedStops) : 0;
        $reqToIdx   = $toIdentifier !== null ? $this->findStopIndex($toIdentifier, $orderedStops) : ($numStops - 1);

        if ($reqFromIdx === null) {
            $reqFromIdx = 0;
        }
        if ($reqToIdx === null) {
            $reqToIdx = $numStops - 1;
        }

        $occupiedNumbers = [];

        $activeBookings = $this->bookings()
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(booking_status)'), ['cancelled', 'rejected'])
            ->get();

        foreach ($activeBookings as $b) {
            $bFrom = $b->from_stop_id
                ? $this->findStopIndex($b->from_stop_id, $orderedStops)
                : ($b->pickup_location ? $this->findStopIndex($b->pickup_location, $orderedStops) : 0);

            $bTo = $b->to_stop_id
                ? $this->findStopIndex($b->to_stop_id, $orderedStops)
                : ($b->destination_location ? $this->findStopIndex($b->destination_location, $orderedStops) : ($numStops - 1));

            if ($bFrom === null) {
                $bFrom = 0;
            }
            if ($bTo === null) {
                $bTo = $numStops - 1;
            }

            // Interval overlap check: max(reqFromIdx, bFrom) < min(reqToIdx, bTo)
            if (max($reqFromIdx, $bFrom) < min($reqToIdx, $bTo)) {
                if ($b->seat_numbers) {
                    $nums = array_map('trim', explode(',', $b->seat_numbers));
                    foreach ($nums as $n) {
                        if ($n !== '') {
                            $occupiedNumbers[] = $n;
                        }
                    }
                }
            }
        }

        return array_values(array_unique($occupiedNumbers));
    }

    /**
     * Recalculate and persist the global available_seats for this trip.
     * Sets available_seats to the minimum available across all segments.
     */
    public function recalculateAvailableSeats(): int
    {
        $orderedStops = $this->getOrderedStops();
        $numStops     = count($orderedStops);

        $activeBookings = $this->bookings()
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(booking_status)'), ['cancelled', 'rejected'])
            ->get();
        $occupied = (int) $activeBookings->sum('seats');

        if ($this->total_seats !== null) {
            $totalCapacity = (int) $this->total_seats;
        } elseif ($occupied === 0) {
            $totalCapacity = (int) ($this->available_seats ?? ($this->vehicle?->total_seats ?: 14));
        } else {
            $totalCapacity = (int) ($this->vehicle?->total_seats ?: (($this->available_seats ?? 0) + $occupied));
        }

        if ($numStops < 2) {
            $avail = max(0, $totalCapacity - $occupied);
        } else {
            $avail = $this->getSegmentAvailableSeats(
                $orderedStops[0]['stop_id'] ?? $orderedStops[0]['name'],
                $orderedStops[$numStops - 1]['stop_id'] ?? $orderedStops[$numStops - 1]['name']
            );
        }

        $this->available_seats = $avail;
        $this->save();

        return $avail;
    }
}
