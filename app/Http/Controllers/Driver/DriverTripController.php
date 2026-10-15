<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\TripStop;
use App\Models\RouteStop;
use App\Models\Notification;
use App\Models\TripMessage;

class DriverTripController extends Controller
{
    /**
     * Display a listing of trips belonging to the authenticated driver.
     */
    public function index(Request $request)
    {
        $driver = auth()->user();
        $query  = Trip::where('user_id', $driver->id)->with(['route', 'vehicle', 'bookings']);

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('route', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('start_location', 'like', "%{$search}%")
                  ->orWhere('end_location', 'like', "%{$search}%");
            });
        }

        $trips = $query->latest('trip_date')->latest('departure_time')->get();

        return view('backend.driver.trips.index', compact('driver', 'trips'));
    }

    /**
     * Display trip details including route stops, passenger bookings, and expected timings.
     * Enforces that the driver can only view their own trips.
     */
    public function show($id)
    {
        $trip = Trip::with([
            'route.startStop',
            'route.endStop',
            'vehicle',
            'tripStops.stop',
            'bookings.user',
            'bookings.fromStop',
            'bookings.toStop',
            'tripMessages.user'
        ])->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only view your own trips.');
        }

        return view('backend.driver.trips.show', compact('trip'));
    }

    /**
     * Show form to edit trip where appropriate.
     * Enforces that the driver can only edit their own trips and non-finalized trips.
     */
    public function edit($id)
    {
        $trip = Trip::with(['route', 'vehicle', 'bookings', 'tripStops.stop'])->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only edit your own trips.');
        }

        return view('backend.driver.trips.edit', compact('trip'));
    }

    /**
     * Update trip operational details (status, timings, capacity, notes, fare, intermediate stops).
     * Enforces ownership, seat capacity limits, booking preservation, and schedule conflict validation.
     */
    public function update(Request $request, $id)
    {
        $trip = Trip::with(['vehicle', 'bookings', 'route', 'tripStops'])->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only update your own trips.');
        }

        // Cannot modify completed or cancelled trips
        if ($trip->isCancelled() || $trip->isCompleted()) {
            return back()->with('error', "Cannot modify a trip that is already {$trip->status}.");
        }

        $maxSeats = $trip->vehicle ? $trip->vehicle->total_seats : 100;

        $validated = $request->validate([
            'status'                => 'required|string|in:scheduled,boarding,departed,delayed,completed,cancelled',
            'trip_date'             => 'nullable|date',
            'departure_time'        => 'required',
            'expected_arrival_time' => 'nullable',
            'pickup_point'          => 'nullable|string|max:255',
            'fare'                  => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string|max:1000',
            'available_seats'       => "required|integer|min:0|max:{$maxSeats}",
            'stops'                 => 'nullable|array',
            'stops.*.id'            => 'nullable|integer|exists:trip_stops,id',
            'stops.*.expected_time' => 'nullable',
        ]);

        $tripDate       = $request->filled('trip_date') ? $request->trip_date : $trip->trip_date->format('Y-m-d');
        $departureTime  = $request->departure_time;
        $arrivalDest    = $request->expected_arrival_time ?: $trip->expected_arrival_time;

        // 1. Check schedule overlap for this driver (excluding this trip) if schedule or active status applies
        if (strtolower($request->status) !== 'cancelled') {
            $overlap = Trip::checkDriverTripOverlap(
                $trip->user_id,
                $tripDate,
                $departureTime,
                $arrivalDest,
                $trip->id
            );

            if ($overlap) {
                return back()->withInput()->withErrors([
                    'departure_time' => "Schedule conflict: You have another trip #{$overlap->id} ({$overlap->route?->name}) scheduled during this time window.",
                ]);
            }
        }

        // 2. Respect existing bookings when updating capacity
        $bookedSeats = (int) $trip->bookings->whereNotIn('status', ['cancelled'])->sum('seats');
        if (((int) $request->available_seats + $bookedSeats) > $maxSeats) {
            return back()->withInput()->withErrors([
                'available_seats' => "Available seats ({$request->available_seats}) plus {$bookedSeats} existing booking(s) exceeds vehicle maximum capacity ({$maxSeats}).",
            ]);
        }

        // 3. Handle cancellation via status change
        $newStatus = strtolower($request->status);
        if ($newStatus === 'cancelled') {
            $this->cancelBookingsForTrip($trip);
        }

        // 4. Update the Trip
        $trip->update([
            'status'                => $newStatus,
            'trip_date'             => $tripDate,
            'departure_time'        => $departureTime,
            'expected_arrival_time' => $arrivalDest,
            'available_seats'       => (int) $request->available_seats,
            'fare'                  => $request->has('fare') ? $request->fare : $trip->fare,
            'pickup_point'          => $request->pickup_point,
            'notes'                 => $request->notes,
        ]);

        // 5. Update intermediate stop timings if provided
        if ($request->has('stops') && is_array($request->stops)) {
            foreach ($request->stops as $stopData) {
                if (!empty($stopData['id']) && !empty($stopData['expected_time'])) {
                    TripStop::where('id', $stopData['id'])
                        ->where('trip_id', $trip->id)
                        ->update(['expected_time' => $stopData['expected_time']]);
                }
            }
        }

        return redirect()->route('driver.trips.show', $trip->id)
            ->with('success', 'Trip updated successfully.');
    }

    /**
     * Show form for approved driver to schedule a new trip.
     */
    public function create()
    {
        $driver = auth()->user();

        // Enforce approved driver access
        if (!$driver || !$driver->isApprovedDriver()) {
            abort(403, 'Unauthorized access. Only approved drivers can schedule trips.');
        }

        // Only approved vehicles belonging to this driver
        $vehicles = Vehicle::where('user_id', $driver->id)
            ->whereIn('status', ['Active', 'active', 'approved', 'Approved'])
            ->get();

        $routes = Route::where('status', 'Active')
            ->with(['routeStops.stop', 'startStop', 'endStop'])
            ->orderBy('name')
            ->get();

        $allStops = Stop::where('status', 'Active')->orderBy('name')->get();

        return view('backend.driver.trips.create', compact('driver', 'vehicles', 'routes', 'allStops'));
    }

    /**
     * Store a newly scheduled trip by an approved driver.
     */
    public function store(Request $request)
    {
        $driver = auth()->user();

        // Enforce approved driver check
        if (!$driver || !$driver->isApprovedDriver()) {
            abort(403, 'Unauthorized access. Only approved drivers can schedule trips.');
        }

        $request->validate([
            'vehicle_id'            => ['required', 'integer', 'exists:vehicles,id'],
            'route_id'              => ['nullable', 'integer', 'exists:routes,id'],
            'origin'                => ['nullable', 'string', 'max:150'],
            'destination'           => ['nullable', 'string', 'max:150'],
            'trip_date'             => ['required', 'date', 'after_or_equal:today'],
            'departure_time'        => ['required'],
            'expected_arrival_time' => ['nullable'],
            'available_seats'       => ['required', 'integer', 'min:1'],
            'fare'                  => ['required', 'numeric', 'min:0'],
            'pickup_point'          => ['nullable', 'string', 'max:255'],
            'notes'                 => ['nullable', 'string', 'max:1000'],
            'stops'                 => ['nullable', 'array'],
            'stops.*.name'          => ['nullable', 'string', 'max:150'],
            'stops.*.stop_id'       => ['nullable', 'integer', 'exists:stops,id'],
            'stops.*.stop_order'    => ['nullable', 'integer', 'min:1'],
            'stops.*.expected_time' => ['nullable'],
        ], [
            'vehicle_id.required'      => 'Please select an approved vehicle.',
            'trip_date.after_or_equal' => 'Trip date cannot be in the past.',
            'available_seats.min'      => 'Available seats must be at least 1.',
            'fare.required'            => 'Fare per passenger seat is required.',
            'fare.min'                 => 'Fare cannot be negative.',
        ]);

        // 1. Verify vehicle belongs to driver and is approved
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', $driver->id)
            ->first();

        if (!$vehicle) {
            return back()->withInput()->withErrors([
                'vehicle_id' => 'The selected vehicle does not belong to you.',
            ]);
        }

        if (!$vehicle->isApproved()) {
            return back()->withInput()->withErrors([
                'vehicle_id' => 'The selected vehicle is not approved yet. You can only schedule trips with approved vehicles.',
            ]);
        }

        // 2. Verify passenger capacity cannot exceed approved vehicle's capacity
        if ((int) $request->available_seats > (int) $vehicle->total_seats) {
            return back()->withInput()->withErrors([
                'available_seats' => "Trip passenger capacity ({$request->available_seats}) cannot exceed your vehicle's total capacity of {$vehicle->total_seats} seats.",
            ]);
        }

        // 3. Resolve Route (either selected existing route or origin + destination)
        $route = null;
        if ($request->filled('route_id')) {
            $route = Route::with('routeStops')->where('id', $request->route_id)->first();
            if (!$route || strtolower($route->status) !== 'active') {
                return back()->withInput()->withErrors([
                    'route_id' => 'The selected route must be active to schedule trips.',
                ]);
            }
        } elseif ($request->filled('origin') && $request->filled('destination')) {
            $origin = trim($request->origin);
            $destination = trim($request->destination);

            $route = Route::where('status', 'Active')
                ->where(function ($q) use ($origin, $destination) {
                    $q->where(function ($sq) use ($origin, $destination) {
                        $sq->where('start_location', $origin)->where('end_location', $destination);
                    })->orWhere(function ($sq) use ($origin, $destination) {
                        $sq->whereHas('startStop', fn($s) => $s->where('name', $origin))
                           ->whereHas('endStop', fn($s) => $s->where('name', $destination));
                    });
                })->first();

            if (!$route) {
                // Find or create start and end stops
                $startStop = Stop::firstOrCreate(
                    ['name' => $origin],
                    ['location' => $origin, 'status' => 'Active']
                );
                $endStop = Stop::firstOrCreate(
                    ['name' => $destination],
                    ['location' => $destination, 'status' => 'Active']
                );

                $route = Route::create([
                    'name'           => "{$origin} to {$destination}",
                    'start_location' => $origin,
                    'end_location'   => $destination,
                    'start_stop_id'  => $startStop->id,
                    'end_stop_id'    => $endStop->id,
                    'status'         => 'Active',
                ]);
            }
        } else {
            return back()->withInput()->withErrors([
                'route_id' => 'Please select an existing route or specify both origin and destination.',
            ]);
        }

        // Determine destination arrival time or derive from stops
        $expectedArrival = $request->expected_arrival_time;
        if (!$expectedArrival && !empty($request->stops) && is_array($request->stops)) {
            foreach (array_reverse($request->stops) as $st) {
                if (!empty($st['expected_time'])) {
                    $expectedArrival = $st['expected_time'];
                    break;
                }
            }
        }

        // 4. Overlap validation for this driver (prevent simultaneous trips across all owned vehicles)
        $overlap = Trip::checkDriverTripOverlap(
            $driver->id,
            $request->trip_date,
            $request->departure_time,
            $expectedArrival
        );

        if ($overlap) {
            return back()->withInput()->withErrors([
                'departure_time' => "Schedule conflict: You are already scheduled for Trip #{$overlap->id} ({$overlap->route?->name}) overlapping with this time period. Drivers cannot operate multiple trips simultaneously.",
            ]);
        }

        // 5. Create trip
        $trip = Trip::create([
            'user_id'               => $driver->id,
            'vehicle_id'            => $vehicle->id,
            'route_id'              => $route->id,
            'trip_date'             => $request->trip_date,
            'departure_time'        => $request->departure_time,
            'expected_arrival_time' => $expectedArrival,
            'available_seats'       => (int) $request->available_seats,
            'fare'                  => $request->fare,
            'pickup_point'          => $request->pickup_point,
            'notes'                 => $request->notes,
            'status'                => 'scheduled',
        ]);

        // 6. Save ordered intermediate stops
        if ($request->has('stops') && is_array($request->stops) && count($request->stops) > 0) {
            $orderIndex = 1;
            foreach ($request->stops as $stopData) {
                $stopName = trim($stopData['name'] ?? '');
                $stopId   = !empty($stopData['stop_id']) ? (int) $stopData['stop_id'] : null;

                if (!$stopId && !empty($stopName)) {
                    $stop = Stop::firstOrCreate(
                        ['name' => $stopName],
                        ['location' => $stopName, 'status' => 'Active']
                    );
                    $stopId = $stop->id;
                }

                if ($stopId) {
                    $stopOrder = !empty($stopData['stop_order']) ? (int) $stopData['stop_order'] : $orderIndex;
                    $expectedTime = !empty($stopData['expected_time']) ? $stopData['expected_time'] : $trip->departure_time;

                    TripStop::create([
                        'trip_id'       => $trip->id,
                        'stop_id'       => $stopId,
                        'stop_order'    => $stopOrder,
                        'expected_time' => $expectedTime,
                    ]);

                    // Ensure route stop exists so search queries work seamlessly
                    RouteStop::firstOrCreate(
                        ['route_id' => $route->id, 'stop_id' => $stopId],
                        ['stop_order' => $stopOrder]
                    );

                    $orderIndex++;
                }
            }
        } elseif ($route->routeStops && $route->routeStops->isNotEmpty()) {
            // Inherit route intermediate stops if any were pre-configured
            foreach ($route->routeStops as $rs) {
                TripStop::create([
                    'trip_id'       => $trip->id,
                    'stop_id'       => $rs->stop_id,
                    'stop_order'    => $rs->stop_order,
                    'expected_time' => $request->input("stop_time_{$rs->stop_id}") ?: $trip->departure_time,
                ]);
            }
        }

        return redirect()->route('driver.trips.show', $trip->id)
            ->with('success', "Trip #{$trip->id} ({$route->name}) scheduled successfully and is now published for passenger booking.");
    }

    /**
     * Cancel a driver trip and notify all booked passengers.
     */
    public function cancel($id)
    {
        $trip = Trip::where('user_id', auth()->id())->with('bookings')->findOrFail($id);

        if ($trip->isCancelled()) {
            return back()->with('error', 'This trip is already cancelled.');
        }

        if ($trip->isCompleted()) {
            return back()->with('error', 'Completed trips cannot be cancelled.');
        }

        $this->cancelBookingsForTrip($trip);

        $trip->update(['status' => 'cancelled']);

        return redirect()->route('driver.trips.show', $trip->id)
            ->with('success', 'Trip has been cancelled and booked passengers have been notified.');
    }

    /**
     * Helper to cancel active bookings and notify passengers when a trip is cancelled.
     */
    protected function cancelBookingsForTrip(Trip $trip): void
    {
        $activeBookings = $trip->bookings()->whereNotIn('status', ['cancelled'])->get();

        foreach ($activeBookings as $booking) {
            $booking->update([
                'status'         => 'cancelled',
                'booking_status' => 'cancelled',
            ]);

            // Notify passenger
            Notification::create([
                'user_id' => $booking->user_id,
                'trip_id' => $trip->id,
                'message' => "Your booking #{$booking->booking_reference} for trip #{$trip->id} has been cancelled by the driver.",
            ]);
        }

        // Post broadcast update
        TripMessage::create([
            'trip_id' => $trip->id,
            'user_id' => auth()->id(),
            'message' => "Trip has been cancelled by the driver.",
        ]);
    }
}
