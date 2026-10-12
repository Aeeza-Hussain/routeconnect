<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;

class DriverTripController extends Controller
{
    /**
     * Display a listing of trips belonging to the authenticated driver.
     */
    public function index(Request $request)
    {
        $driver = auth()->user();
        $query  = Trip::where('user_id', $driver->id)->with(['route', 'vehicle']);

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
     * Display trip details including route stops and expected timings.
     * Enforces that the driver can only view their own trips.
     */
    public function show($id)
    {
        $trip = Trip::with([
            'route',
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
     * Enforces that the driver can only edit their own trips.
     */
    public function edit($id)
    {
        $trip = Trip::with(['route', 'vehicle'])->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only edit your own trips.');
        }

        return view('backend.driver.trips.edit', compact('trip'));
    }

    /**
     * Update trip operational details (status, departure time, pickup point, available seats).
     * Enforces ownership validation and seat capacity limits.
     */
    public function update(Request $request, $id)
    {
        $trip = Trip::with('vehicle')->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only update your own trips.');
        }

        $maxSeats = $trip->vehicle ? $trip->vehicle->total_seats : 100;

        $validated = $request->validate([
            'status'          => 'required|string|in:scheduled,boarding,departed,delayed,completed,cancelled',
            'departure_time'  => 'required',
            'pickup_point'    => 'nullable|string|max:255',
            'available_seats' => "required|integer|min:0|max:{$maxSeats}",
        ]);

        $trip->update($validated);

        return redirect()->route('driver.trips.show', $trip->id)
            ->with('success', 'Trip updated successfully.');
    }

    /**
     * Show form for driver to schedule a new trip.
     */
    public function create()
    {
        $driver = auth()->user();

        // Only approved vehicles belonging to this driver
        $vehicles = \App\Models\Vehicle::where('user_id', $driver->id)
            ->whereIn('status', ['Active', 'active', 'approved', 'Approved'])
            ->get();

        $routes = \App\Models\Route::where('status', 'Active')
            ->with(['routeStops.stop', 'startStop', 'endStop'])
            ->orderBy('name')
            ->get();

        return view('backend.driver.trips.create', compact('driver', 'vehicles', 'routes'));
    }

    /**
     * Store a newly scheduled trip by the driver.
     */
    public function store(Request $request)
    {
        $driver = auth()->user();

        $request->validate([
            'vehicle_id'            => ['required', 'integer', 'exists:vehicles,id'],
            'route_id'              => ['required', 'integer', 'exists:routes,id'],
            'trip_date'             => ['required', 'date', 'after_or_equal:today'],
            'departure_time'        => ['required'],
            'expected_arrival_time' => ['nullable'],
            'available_seats'       => ['required', 'integer', 'min:1'],
            'fare'                  => ['required', 'numeric', 'min:0'],
            'pickup_point'          => ['nullable', 'string', 'max:255'],
            'notes'                 => ['nullable', 'string', 'max:1000'],
        ], [
            'vehicle_id.required'     => 'Please select an approved vehicle.',
            'route_id.required'       => 'Please select a route for this trip.',
            'trip_date.after_or_equal'=> 'Trip date cannot be in the past.',
            'available_seats.min'     => 'Available seats must be at least 1.',
        ]);

        // 1. Verify vehicle belongs to driver and is approved
        $vehicle = \App\Models\Vehicle::where('id', $request->vehicle_id)
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

        // 3. Overlap validation for this driver
        $overlap = Trip::checkDriverTripOverlap(
            $driver->id,
            $request->trip_date,
            $request->departure_time,
            $request->expected_arrival_time
        );

        if ($overlap) {
            return back()->withInput()->withErrors([
                'departure_time' => "Schedule conflict: You are already scheduled for Trip #{$overlap->id} ({$overlap->route?->name}) overlapping with this time period. Drivers cannot operate multiple trips simultaneously.",
            ]);
        }

        // 4. Verify route is active
        $route = \App\Models\Route::with('routeStops')->where('id', $request->route_id)->first();
        if (!$route || strtolower($route->status) !== 'active') {
            return back()->withInput()->withErrors([
                'route_id' => 'The selected route must be active to schedule trips.',
            ]);
        }

        // 5. Create trip
        $trip = Trip::create([
            'user_id'               => $driver->id,
            'vehicle_id'            => $vehicle->id,
            'route_id'              => $route->id,
            'trip_date'             => $request->trip_date,
            'departure_time'        => $request->departure_time,
            'expected_arrival_time' => $request->expected_arrival_time,
            'available_seats'       => (int) $request->available_seats,
            'fare'                  => $request->fare,
            'pickup_point'          => $request->pickup_point,
            'notes'                 => $request->notes,
            'status'                => 'scheduled',
        ]);

        // 6. Inherit route intermediate stops if any
        if ($route->routeStops && $route->routeStops->isNotEmpty()) {
            foreach ($route->routeStops as $rs) {
                \App\Models\TripStop::create([
                    'trip_id'       => $trip->id,
                    'stop_id'       => $rs->stop_id,
                    'stop_order'    => $rs->stop_order,
                    'expected_time' => $request->input("stop_time_{$rs->stop_id}") ?: $trip->departure_time,
                ]);
            }
        }

        return redirect()->route('driver.trips.show', $trip->id)
            ->with('success', "Trip #{$trip->id} ({$route->name}) scheduled successfully and is now active for passenger search.");
    }

    /**
     * Cancel a driver trip.
     */
    public function cancel($id)
    {
        $trip = Trip::where('user_id', auth()->id())->findOrFail($id);

        if ($trip->isCancelled()) {
            return back()->with('error', 'This trip is already cancelled.');
        }

        $trip->update(['status' => 'cancelled']);

        return redirect()->route('driver.trips.show', $trip->id)
            ->with('success', 'Trip has been cancelled.');
    }
}
