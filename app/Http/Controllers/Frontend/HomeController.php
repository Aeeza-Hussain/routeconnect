<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Stop;
use App\Models\Vehicle;
use App\Models\TravelRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Public Homepage
     */
    public function index()
    {
        $allStops = \Illuminate\Support\Facades\Schema::hasTable('stops')
            ? Stop::where('status', 'Active')->orderBy('name')->get()
            : collect();
        return view('frontend.index', compact('allStops'));
    }

    /**
     * Trip Search & Listing Page
     * 
     * Search scheduled trips where:
     * - From stop occurs before To stop in the route order.
     * - Trip date matches selected date (if provided).
     * - Trip status is Scheduled.
     * - Available seats > 0.
     */
    public function trips(Request $request)
    {
        $from = trim((string) $request->input('from', ''));
        $to   = trim((string) $request->input('to', ''));
        $date = $request->input('date');
        $time = $request->input('time');
        $errorMessage = null;

        // Validation: Identical origin and destination
        if ($from !== '' && $to !== '' && strcasecmp($from, $to) === 0) {
            $errorMessage = 'Origin and destination cannot be the same stop. Please select different boarding and arrival locations.';
        }

        // Validation: Valid calendar date format
        if (!empty($date)) {
            try {
                Carbon::parse($date);
            } catch (\Exception $e) {
                $errorMessage = 'Invalid travel date format provided. Please select a valid calendar date.';
                $date = null;
            }
        }

        // 1. Base Query: Only scheduled trips with available seats > 0 belonging to approved drivers with approved vehicles
        $query = Trip::with([
            'route.routeStops.stop',
            'tripStops.stop',
            'driver',
            'vehicle',
            'tripMessages'
        ])
        ->whereRaw('LOWER(status) = ?', ['scheduled'])
        ->where('available_seats', '>', 0)
        ->whereHas('driver', function ($q) {
            $q->where('user_type', 2)->where('driver_status', 'approved');
        })
        ->whereHas('vehicle', function ($q) {
            $q->whereIn('status', ['Active', 'active', 'approved', 'Approved']);
        });

        // 2. Date match
        if (!empty($date) && !$errorMessage) {
            $query->whereDate('trip_date', $date);
        }

        // 3. Optional Preferred Time filter
        if (!empty($time) && !$errorMessage) {
            if ($time === 'morning') {
                $query->whereTime('departure_time', '>=', '05:00:00')
                      ->whereTime('departure_time', '<=', '11:59:59');
            } elseif ($time === 'afternoon') {
                $query->whereTime('departure_time', '>=', '12:00:00')
                      ->whereTime('departure_time', '<=', '16:59:59');
            } elseif ($time === 'evening') {
                $query->whereTime('departure_time', '>=', '17:00:00')
                      ->whereTime('departure_time', '<=', '23:59:59');
            } elseif (preg_match('/^\d{1,2}:\d{2}/', $time)) {
                $query->whereTime('departure_time', '>=', $time);
            }
        }

        if ($errorMessage) {
            $candidateTrips = collect();
        } else {
            $candidateTrips = $query->orderBy('trip_date')->orderBy('departure_time')->get();
        }

        // 4. Route Stop Order Filter:
        // Filter candidate trips ensuring From stop occurs before To stop in route order.
        $trips = $candidateTrips->filter(function ($trip) use ($from, $to) {
            $orderedStops = $this->getTripOrderedStops($trip);
            $trip->search_ordered_stops = $orderedStops;

            // Compute intermediate stops (all stops strictly between origin and destination)
            $trip->intermediate_stops = count($orderedStops) > 2
                ? array_values(array_slice($orderedStops, 1, -1))
                : [];

            // If neither 'from' nor 'to' is specified, include trip
            if ($from === '' && $to === '') {
                return true;
            }

            // If only 'from' is provided
            if ($from !== '' && $to === '') {
                $fromMatch = $this->findStopInList($orderedStops, $from);
                if ($fromMatch) {
                    $trip->search_from_stop = $fromMatch;
                    return true;
                }
                return false;
            }

            // If only 'to' is provided
            if ($from === '' && $to !== '') {
                $toMatch = $this->findStopInList($orderedStops, $to);
                if ($toMatch) {
                    $trip->search_to_stop = $toMatch;
                    return true;
                }
                return false;
            }

            // Both 'from' and 'to' are provided
            $fromMatch = $this->findStopInList($orderedStops, $from);
            $toMatch   = $this->findStopInList($orderedStops, $to);

            if (!$fromMatch || !$toMatch) {
                return false;
            }

            // CRITICAL REQUIREMENT: From stop must occur before To stop in the route order
            if ($fromMatch['order'] < $toMatch['order']) {
                $trip->search_from_stop = $fromMatch;
                $trip->search_to_stop   = $toMatch;
                return true;
            }

            return false;
        })->values();

        // Load active stops for dropdowns
        $allStops = \Illuminate\Support\Facades\Schema::hasTable('stops')
            ? Stop::where('status', 'Active')->orderBy('name')->get()
            : collect();

        // Flag if a search was performed for seats
        $hasSearched = $request->has('from') || $request->has('to') || $request->has('date') || $request->has('time');

        // ─── Unified Find a Ride Option Mode ───
        $tab = $request->query('tab', $request->query('mode') === 'private' ? 'vehicle' : 'seat');
        if (!in_array($tab, ['seat', 'vehicle', 'request'])) {
            $tab = 'seat';
        }

        // ─── Option 2 Data: Book Entire Vehicle (Private Rentals) ───
        $vehicleTypes = ['Car', 'Van', 'Daba', 'Bus', 'Other'];
        $pickup       = trim((string) $request->query('pickup', ''));
        $destination  = trim((string) $request->query('destination', ''));
        $pickupDate   = $request->query('pickup_date', Carbon::today()->format('Y-m-d'));
        $pickupTime   = $request->query('pickup_time', '09:00');
        $returnDate   = $request->query('return_date', Carbon::today()->addDays(1)->format('Y-m-d'));
        $returnTime   = $request->query('return_time', '18:00');
        $vehicleType  = trim((string) $request->query('vehicle_type', ''));
        $passengers   = max(1, (int) $request->query('passengers', 1));

        $todayStr = Carbon::today()->format('Y-m-d');
        if ($pickupDate && $pickupDate < $todayStr) {
            $pickupDate = $todayStr;
        }
        if ($returnDate && $returnDate < $pickupDate) {
            $returnDate = $pickupDate;
        }

        try {
            $startDatetime = Carbon::parse("{$pickupDate} {$pickupTime}");
            $endDatetime   = Carbon::parse("{$returnDate} {$returnTime}");
            if ($endDatetime->lessThanOrEqualTo($startDatetime)) {
                $endDatetime = (clone $startDatetime)->addDay();
            }
        } catch (\Exception $e) {
            $startDatetime = Carbon::today()->setTime(9, 0);
            $endDatetime   = Carbon::today()->addDay()->setTime(18, 0);
        }

        $vehicleQuery = Vehicle::with(['driver', 'unavailabilities'])
            ->whereIn(DB::raw('LOWER(status)'), ['approved', 'active'])
            ->where('is_available_for_rental', true)
            ->whereNotNull('rental_rate_per_day')
            ->where('rental_rate_per_day', '>', 0)
            ->whereHas('driver', function ($q) {
                $q->where('driver_status', 'approved');
            });

        if ($vehicleType && $vehicleType !== 'all') {
            $vehicleQuery->where('type', $vehicleType);
        }

        if ($passengers > 1) {
            $vehicleQuery->where('total_seats', '>=', $passengers);
        }

        if ($pickup) {
            $vehicleQuery->where(function ($q) use ($pickup) {
                $q->where('model', 'like', "%{$pickup}%")
                  ->orWhere('description', 'like', "%{$pickup}%");
            });
        }

        $candidateVehicles = $vehicleQuery->latest()->get();

        $privateVehicles = $candidateVehicles->filter(function (Vehicle $v) use ($startDatetime, $endDatetime) {
            return $v->isAvailableForRange($startDatetime, $endDatetime);
        })->values();

        foreach ($privateVehicles as $v) {
            $v->pricing = $v->calculateRentalPrice($startDatetime, $endDatetime);
        }

        $diffHours = $startDatetime->diffInHours($endDatetime);
        $calculatedDays = (int) ceil(max(1, $diffHours / 24));

        // ─── Option 3 Data: Post a Travel Request / Marketplace Activity ───
        $openTravelRequests = TravelRequest::with('user')
            ->where('status', 'open')
            ->latest()
            ->take(8)
            ->get();

        return view('frontend.trips', compact(
            'tab',
            'trips',
            'allStops',
            'from',
            'to',
            'date',
            'time',
            'hasSearched',
            'errorMessage',
            'privateVehicles',
            'vehicleTypes',
            'pickup',
            'destination',
            'pickupDate',
            'pickupTime',
            'returnDate',
            'returnTime',
            'vehicleType',
            'passengers',
            'calculatedDays',
            'openTravelRequests'
        ));
    }

    /**
     * Public Trip Details View
     */
    public function showTrip($id)
    {
        $trip = Trip::with([
            'route.routeStops.stop',
            'tripStops.stop',
            'driver',
            'vehicle',
            'tripMessages.user'
        ])
        ->whereHas('driver', function ($q) {
            $q->where('user_type', 2)->where('driver_status', 'approved');
        })
        ->whereHas('vehicle', function ($q) {
            $q->whereIn('status', ['Active', 'active', 'approved', 'Approved']);
        })
        ->findOrFail($id);

        $orderedStops = $this->getTripOrderedStops($trip);
        $trip->intermediate_stops = count($orderedStops) > 2
            ? array_values(array_slice($orderedStops, 1, -1))
            : [];

        return view('frontend.trips_show', compact('trip', 'orderedStops'));
    }

    /**
     * Build unified ordered stop sequence for a given trip.
     */
    protected function getTripOrderedStops(Trip $trip): array
    {
        $ordered = [];

        // 1. Gather stops from route_stops
        if ($trip->route && $trip->route->routeStops) {
            foreach ($trip->route->routeStops as $rs) {
                $stopId = $rs->stop_id;
                $name   = $rs->stop?->name ?? '';
                $order  = (int) $rs->stop_order;
                $ordered[$stopId] = [
                    'stop_id'       => $stopId,
                    'name'          => $name,
                    'order'         => $order,
                    'expected_time' => null,
                ];
            }
        }

        // 2. Add route start and end endpoints if not already in list
        if ($trip->route) {
            $startId   = $trip->route->start_stop_id;
            $startName = $trip->route->start_location ?? $trip->route->startStop?->name;

            if ($startId && !isset($ordered[$startId])) {
                $ordered[$startId] = [
                    'stop_id'       => $startId,
                    'name'          => $startName ?? '',
                    'order'         => 0,
                    'expected_time' => $trip->departure_time,
                ];
            } elseif ($startName && !$this->findStopByNameInList($ordered, $startName)) {
                $ordered['start_loc'] = [
                    'stop_id'       => $startId,
                    'name'          => $startName,
                    'order'         => 0,
                    'expected_time' => $trip->departure_time,
                ];
            }

            $endId    = $trip->route->end_stop_id;
            $endName  = $trip->route->end_location ?? $trip->route->endStop?->name;
            $maxOrder = empty($ordered) ? 10 : (max(array_column($ordered, 'order')) + 1);

            if ($endId && !isset($ordered[$endId])) {
                $ordered[$endId] = [
                    'stop_id'       => $endId,
                    'name'          => $endName ?? '',
                    'order'         => $maxOrder,
                    'expected_time' => null,
                ];
            } elseif ($endName && !$this->findStopByNameInList($ordered, $endName)) {
                $ordered['end_loc'] = [
                    'stop_id'       => $endId,
                    'name'          => $endName,
                    'order'         => $maxOrder,
                    'expected_time' => null,
                ];
            }
        }

        // 3. Overlay and enrich with trip_stops (which contain expected_time)
        if ($trip->tripStops) {
            foreach ($trip->tripStops as $ts) {
                $stopId = $ts->stop_id;
                $name   = $ts->stop?->name ?? '';
                $order  = (int) $ts->stop_order;
                $time   = $ts->expected_time;

                if (isset($ordered[$stopId])) {
                    $ordered[$stopId]['expected_time'] = $time ?? $ordered[$stopId]['expected_time'];
                    if ($order > 0) {
                        $ordered[$stopId]['order'] = $order;
                    }
                } else {
                    $ordered[$stopId] = [
                        'stop_id'       => $stopId,
                        'name'          => $name,
                        'order'         => $order,
                        'expected_time' => $time,
                    ];
                }
            }
        }

        // Sort sequentially by stop_order
        usort($ordered, fn ($a, $b) => $a['order'] <=> $b['order']);

        // Set departure time for the first stop if missing
        if (!empty($ordered) && empty($ordered[0]['expected_time'])) {
            $ordered[0]['expected_time'] = $trip->departure_time;
        }

        return $ordered;
    }

    /**
     * Check if a stop name exists in an associative list.
     */
    protected function findStopByNameInList(array $stops, string $name): bool
    {
        $lower = strtolower(trim($name));
        foreach ($stops as $s) {
            if (strtolower(trim($s['name'])) === $lower) {
                return true;
            }
        }
        return false;
    }

    /**
     * Find matching stop in an ordered stop list by ID or name.
     */
    protected function findStopInList(array $stops, string $query): ?array
    {
        $query = trim($query);
        if ($query === '') {
            return null;
        }

        // 1. If numeric stop_id
        if (is_numeric($query)) {
            $id = (int) $query;
            foreach ($stops as $s) {
                if (isset($s['stop_id']) && $s['stop_id'] === $id) {
                    return $s;
                }
            }
        }

        $lowerQuery = strtolower($query);

        // 2. Exact match by name (case-insensitive)
        foreach ($stops as $s) {
            if (strtolower($s['name']) === $lowerQuery) {
                return $s;
            }
        }

        // 3. Partial match (e.g. "Nagar" in "Nagar Stop" or vice-versa)
        foreach ($stops as $s) {
            $lowerName = strtolower($s['name']);
            if (stripos($lowerName, $lowerQuery) !== false || stripos($lowerQuery, $lowerName) !== false) {
                return $s;
            }
        }

        return null;
    }

    /**
     * Passenger Booking Page
     */
    public function booking()
    {
        return view('frontend.booking');
    }
}
