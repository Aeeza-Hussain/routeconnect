<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\TripMessage;

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

        // Available Vehicle Seats
        $availableVehicleSeats = $vehicle ? (int) $vehicle->total_seats : 0;

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

        return view('backend.driver.dashboard', compact(
            'driver',
            'vehicle',
            'totalTrips',
            'scheduledTripsCount',
            'completedTripsCount',
            'bookingsCount',
            'availableVehicleSeats',
            'recentTrips',
            'recentBookings',
            'recentMessages'
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
