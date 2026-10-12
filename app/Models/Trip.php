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
            'available_seats' => 'integer',
            'fare'            => 'decimal:2',
        ];
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
}
