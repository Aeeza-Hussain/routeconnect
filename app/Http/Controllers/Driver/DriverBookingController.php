<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Trip;

class DriverBookingController extends Controller
{
    /**
     * Display a listing of bookings belonging to the authenticated driver's trips.
     */
    public function index(Request $request)
    {
        $driver = auth()->user();

        $query = Booking::whereHas('trip', function ($q) use ($driver) {
            $q->where('user_id', $driver->id);
        })->with(['user', 'trip.route', 'trip.vehicle', 'fromStop', 'toStop']);

        if ($request->filled('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }

        $bookings = $query->latest()->get();

        // For filter dropdown
        $driverTrips = Trip::where('user_id', $driver->id)->with('route')->latest()->get();

        return view('backend.driver.bookings.index', compact('driver', 'bookings', 'driverTrips'));
    }

    /**
     * Display single booking details.
     * Enforces that the driver can only view bookings for their own trips.
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'trip.route', 'trip.vehicle', 'fromStop', 'toStop'])
            ->findOrFail($id);

        if ($booking->trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only view bookings for your own trips.');
        }

        return view('backend.driver.bookings.show', compact('booking'));
    }
}
