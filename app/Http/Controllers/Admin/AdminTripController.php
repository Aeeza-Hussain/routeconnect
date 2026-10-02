<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route as RouteModel;

class AdminTripController extends Controller
{
    /**
     * Display a listing of trips with filters.
     *
     * GET /admin/trips
     */
    public function index(Request $request)
    {
        $query = Trip::with(['driver', 'vehicle', 'route']);

        // Filter by Driver
        if ($request->filled('driver_id')) {
            $query->where('user_id', $request->driver_id);
        }

        // Filter by Route
        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('trip_date', $request->date);
        }

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }

        $trips = $query->latest('trip_date')
            ->latest('departure_time')
            ->paginate(15)
            ->withQueryString();

        // Stat counters
        $totalTrips     = Trip::count();
        $scheduledTrips = Trip::whereRaw('LOWER(status) = ?', ['scheduled'])->count();
        $completedTrips = Trip::whereRaw('LOWER(status) = ?', ['completed'])->count();
        $cancelledTrips = Trip::whereRaw('LOWER(status) = ?', ['cancelled'])->count();

        // Data for filter dropdowns
        $drivers = User::where('user_type', 2)
            ->where('driver_status', 'approved')
            ->orderBy('name')
            ->get();

        $routes = RouteModel::orderBy('name')->get();

        return view('backend.admin.trips.index', compact(
            'trips',
            'totalTrips',
            'scheduledTrips',
            'completedTrips',
            'cancelledTrips',
            'drivers',
            'routes'
        ));
    }

    /**
     * Show the form for creating a new trip.
     *
     * GET /admin/trips/create
     */
    public function create()
    {
        // Only approved drivers can be assigned to trips
        $drivers = User::where('user_type', 2)
            ->where('driver_status', 'approved')
            ->with(['vehicles' => function ($q) {
                $q->where('status', 'Active');
            }])
            ->orderBy('name')
            ->get();

        // Active vehicles belonging to approved drivers
        $vehicles = Vehicle::whereIn('user_id', $drivers->pluck('id'))
            ->where('status', 'Active')
            ->orderBy('registration_no')
            ->get();

        // Only active routes can be scheduled
        $routes = RouteModel::where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('backend.admin.trips.create', compact('drivers', 'vehicles', 'routes'));
    }

    /**
     * Store a newly created trip in storage.
     *
     * POST /admin/trips
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'         => ['required', 'integer', 'exists:users,id'],
            'vehicle_id'      => ['required', 'integer', 'exists:vehicles,id'],
            'route_id'        => ['required', 'integer', 'exists:routes,id'],
            'trip_date'       => ['required', 'date'],
            'departure_time'  => ['required'],
            'available_seats' => ['required', 'integer', 'min:1'],
            'status'          => ['required', 'in:Scheduled,Completed,Cancelled,scheduled,completed,cancelled'],
            'fare'            => ['nullable', 'numeric', 'min:0'],
            'pickup_point'    => ['nullable', 'string', 'max:255'],
        ]);

        // 1. Verify Driver is Approved
        $driver = User::where('id', $request->user_id)
            ->where('user_type', 2)
            ->where('driver_status', 'approved')
            ->first();

        if (!$driver) {
            return back()->withInput()->withErrors([
                'user_id' => 'The selected driver is not an approved driver. Only approved drivers can be assigned to trips.',
            ]);
        }

        // 2. Verify Vehicle belongs to Driver
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', $driver->id)
            ->first();

        if (!$vehicle) {
            return back()->withInput()->withErrors([
                'vehicle_id' => 'The selected vehicle does not belong to the chosen driver.',
            ]);
        }

        // 3. Verify Route is Active
        $route = RouteModel::where('id', $request->route_id)->first();
        if (!$route || strtolower($route->status) !== 'active') {
            return back()->withInput()->withErrors([
                'route_id' => 'The selected route must be active to schedule trips.',
            ]);
        }

        // 4. Verify Available Seats do not exceed vehicle capacity
        if ((int) $request->available_seats > (int) $vehicle->total_seats) {
            return back()->withInput()->withErrors([
                'available_seats' => "Available seats ({$request->available_seats}) cannot exceed vehicle total capacity ({$vehicle->total_seats}).",
            ]);
        }

        $trip = Trip::create([
            'user_id'         => $driver->id,
            'vehicle_id'      => $vehicle->id,
            'route_id'        => $route->id,
            'trip_date'       => $request->trip_date,
            'departure_time'  => $request->departure_time,
            'available_seats' => (int) $request->available_seats,
            'fare'            => $request->fare,
            'pickup_point'    => $request->pickup_point,
            'status'          => ucfirst(strtolower($request->status)),
        ]);

        return redirect()->route('admin.trips.index')
            ->with('success', "Trip #{$trip->id} ({$route->name}) scheduled successfully.");
    }

    /**
     * Display the specified trip.
     *
     * GET /admin/trips/{id}
     */
    public function show($id)
    {
        $trip = Trip::with(['driver', 'vehicle', 'route.routeStops.stop'])->findOrFail($id);

        return view('backend.admin.trips.show', compact('trip'));
    }

    /**
     * Show the form for editing the specified trip.
     *
     * GET /admin/trips/{id}/edit
     */
    public function edit($id)
    {
        $trip = Trip::with(['driver', 'vehicle', 'route'])->findOrFail($id);

        $drivers = User::where('user_type', 2)
            ->where('driver_status', 'approved')
            ->with(['vehicles' => function ($q) {
                $q->where('status', 'Active');
            }])
            ->orderBy('name')
            ->get();

        $vehicles = Vehicle::whereIn('user_id', $drivers->pluck('id'))
            ->orderBy('registration_no')
            ->get();

        // Active routes plus the current route (in case it became inactive)
        $routes = RouteModel::where('status', 'Active')
            ->orWhere('id', $trip->route_id)
            ->orderBy('name')
            ->get();

        return view('backend.admin.trips.edit', compact('trip', 'drivers', 'vehicles', 'routes'));
    }

    /**
     * Update the specified trip in storage.
     *
     * PUT /admin/trips/{id}
     */
    public function update(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $request->validate([
            'user_id'         => ['required', 'integer', 'exists:users,id'],
            'vehicle_id'      => ['required', 'integer', 'exists:vehicles,id'],
            'route_id'        => ['required', 'integer', 'exists:routes,id'],
            'trip_date'       => ['required', 'date'],
            'departure_time'  => ['required'],
            'available_seats' => ['required', 'integer', 'min:1'],
            'status'          => ['required', 'in:Scheduled,Completed,Cancelled,scheduled,completed,cancelled'],
            'fare'            => ['nullable', 'numeric', 'min:0'],
            'pickup_point'    => ['nullable', 'string', 'max:255'],
        ]);

        // 1. Verify Driver is Approved
        $driver = User::where('id', $request->user_id)
            ->where('user_type', 2)
            ->where('driver_status', 'approved')
            ->first();

        if (!$driver) {
            return back()->withInput()->withErrors([
                'user_id' => 'The selected driver is not an approved driver. Only approved drivers can be assigned to trips.',
            ]);
        }

        // 2. Verify Vehicle belongs to Driver
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', $driver->id)
            ->first();

        if (!$vehicle) {
            return back()->withInput()->withErrors([
                'vehicle_id' => 'The selected vehicle does not belong to the chosen driver.',
            ]);
        }

        // 3. Verify Route exists
        $route = RouteModel::findOrFail($request->route_id);

        // 4. Verify Available Seats do not exceed vehicle capacity
        if ((int) $request->available_seats > (int) $vehicle->total_seats) {
            return back()->withInput()->withErrors([
                'available_seats' => "Available seats ({$request->available_seats}) cannot exceed vehicle total capacity ({$vehicle->total_seats}).",
            ]);
        }

        $trip->update([
            'user_id'         => $driver->id,
            'vehicle_id'      => $vehicle->id,
            'route_id'        => $route->id,
            'trip_date'       => $request->trip_date,
            'departure_time'  => $request->departure_time,
            'available_seats' => (int) $request->available_seats,
            'fare'            => $request->fare,
            'pickup_point'    => $request->pickup_point,
            'status'          => ucfirst(strtolower($request->status)),
        ]);

        return redirect()->route('admin.trips.index')
            ->with('success', "Trip #{$trip->id} updated successfully.");
    }

    /**
     * Remove the specified trip from storage.
     *
     * DELETE /admin/trips/{id}
     */
    public function destroy($id)
    {
        $trip = Trip::findOrFail($id);
        $tripId = $trip->id;

        $trip->delete();

        return redirect()->route('admin.trips.index')
            ->with('success', "Trip #{$tripId} deleted successfully.");
    }
}
