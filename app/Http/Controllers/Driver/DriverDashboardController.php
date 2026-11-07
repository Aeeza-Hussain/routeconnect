<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\TripMessage;
use App\Models\TravelRequest;
use App\Models\DriverApplication;
use Illuminate\Support\Facades\DB;

class DriverDashboardController extends Controller
{
    /**
     * Approved Driver Dashboard Overview
     */
    public function index()
    {
        $driver  = auth()->user();
        $vehicle = Vehicle::where('user_id', $driver->id)->first();

        // Real database statistics
        $totalTrips          = Trip::where('user_id', $driver->id)->count();
        $scheduledTripsCount = Trip::where('user_id', $driver->id)->whereRaw('LOWER(status) = ?', ['scheduled'])->count();
        $completedTripsCount = Trip::where('user_id', $driver->id)->whereRaw('LOWER(status) = ?', ['completed'])->count();
        $bookingsCount       = Booking::whereHas('trip', function ($q) use ($driver) {
            $q->where('user_id', $driver->id);
        })->count();

        // Vehicle counts and fleet
        $availableVehicleSeats      = $vehicle ? (int) $vehicle->total_seats : 0;
        $vehiclesCount              = Vehicle::where('user_id', $driver->id)->count();
        $approvedVehiclesCount      = Vehicle::where('user_id', $driver->id)->whereIn('status', ['Active', 'active', 'approved', 'Approved'])->count();
        $pendingVehiclesCount       = Vehicle::where('user_id', $driver->id)->where('status', 'pending')->count();
        $rejectedVehiclesCount      = Vehicle::where('user_id', $driver->id)->where('status', 'rejected')->count();
        $activePrivateListingsCount = Vehicle::where('user_id', $driver->id)->whereIn('status', ['Active', 'active', 'approved', 'Approved'])->where('is_available_for_rental', true)->count();
        $driverVehicles             = Vehicle::where('user_id', $driver->id)->latest()->get();

        // Recent trips and bookings for dashboard display
        $recentTrips = Trip::where('user_id', $driver->id)
            ->with(['route', 'vehicle'])
            ->latest()
            ->take(5)
            ->get();

        $recentBookings = Booking::whereHas('trip', function ($q) use ($driver) {
            $q->where('user_id', $driver->id);
        })->with(['user', 'trip.route'])->latest()->take(5)->get();

        $recentMessages = TripMessage::where('user_id', $driver->id)
            ->with('trip.route')
            ->latest()
            ->take(3)
            ->get();

        // Open passenger requests count & recent requests
        $passengerRequestsCount = TravelRequest::where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count();

        $recentPassengerRequests = TravelRequest::with(['user', 'applications'])
            ->where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->take(4)
            ->get();

        return view('backend.driver.dashboard', compact(
            'driver',
            'vehicle',
            'totalTrips',
            'scheduledTripsCount',
            'completedTripsCount',
            'bookingsCount',
            'availableVehicleSeats',
            'vehiclesCount',
            'approvedVehiclesCount',
            'pendingVehiclesCount',
            'rejectedVehiclesCount',
            'activePrivateListingsCount',
            'driverVehicles',
            'recentTrips',
            'recentBookings',
            'recentMessages',
            'passengerRequestsCount',
            'recentPassengerRequests'
        ));
    }

    /**
     * Dedicated Driver Passenger Requests section:
     * Browse, filter, inspect, and apply to open passenger travel requests using approved vehicles.
     */
    public function passengerRequests(Request $request)
    {
        $driver = auth()->user();

        $query = TravelRequest::with(['user', 'applications.driver'])
            ->where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        // Filter by Route (pickup or destination)
        if ($request->filled('route')) {
            $route = trim($request->query('route'));
            $query->where(function ($q) use ($route) {
                $q->where('pickup_location', 'like', "%{$route}%")
                  ->orWhere('destination_location', 'like', "%{$route}%");
            });
        }

        // Filter by Travel Date
        if ($request->filled('date')) {
            $query->whereDate('travel_date', $request->query('date'));
        }

        // Filter by Preferred Vehicle Type
        if ($request->filled('vehicle_type') && $request->query('vehicle_type') !== 'all') {
            $query->where('preferred_vehicle_type', $request->query('vehicle_type'));
        }

        // Filter by Seats needed
        if ($request->filled('seats')) {
            $query->where('seats_needed', '<=', (int) $request->query('seats'));
        }

        $requests = $query->latest()->paginate(12)->withQueryString();

        // Driver's approved vehicles available for submitting offers
        $driverVehicles = Vehicle::where('user_id', $driver->id)
            ->whereIn(DB::raw('LOWER(status)'), ['approved', 'active'])
            ->get();

        // Driver's existing applications keyed by travel_request_id to show status
        $myApplications = DriverApplication::where('driver_id', $driver->id)
            ->get()
            ->keyBy('travel_request_id');

        return view('backend.driver.passenger_requests', compact(
            'driver',
            'requests',
            'driverVehicles',
            'myApplications'
        ));
    }

    /**
     * Driver Vehicle Page
     */
    public function vehicle()
    {
        $driver  = auth()->user();
        $vehicle = Vehicle::where('user_id', $driver->id)->first();
        return view('backend.driver.vehicle', compact('driver', 'vehicle'));
    }

    /**
     * Driver Application Pending/Status Page
     */
    public function pending()
    {
        $user = auth()->user();
        if ($user->user_type != 2) {
            abort(403, 'Unauthorized access.');
        }
        if ($user->driver_status === 'approved') {
            return redirect()->route('driver.dashboard');
        }
        return view('backend.driver.pending', compact('user'));
    }
}
