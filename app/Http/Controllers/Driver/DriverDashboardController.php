<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\Booking;

class DriverDashboardController extends Controller
{
    /**
     * Approved Driver Dashboard Overview
     */
    public function index()
    {
        $driver  = auth()->user();
        $vehicle = Vehicle::where('user_id', $driver->id)->first();
        $tripsCount    = Trip::where('user_id', $driver->id)->count();
        $bookingsCount = Booking::whereHas('trip', function ($q) use ($driver) {
            $q->where('user_id', $driver->id);
        })->count();

        return view('backend.driver.dashboard', compact('driver', 'vehicle', 'tripsCount', 'bookingsCount'));
    }

    /**
     * Driver Profile Page
     */
    public function profile()
    {
        $driver = auth()->user();
        return view('backend.driver.profile', compact('driver'));
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
     * Driver Trips Page
     */
    public function trips()
    {
        $driver = auth()->user();
        $trips  = Trip::where('user_id', $driver->id)->with('route')->latest()->get();
        return view('backend.driver.trips', compact('driver', 'trips'));
    }

    /**
     * Driver Bookings Page
     */
    public function bookings()
    {
        $driver   = auth()->user();
        $bookings = Booking::whereHas('trip', function ($q) use ($driver) {
            $q->where('user_id', $driver->id);
        })->with(['user', 'trip.route'])->latest()->get();

        return view('backend.driver.bookings', compact('driver', 'bookings'));
    }

    /**
     * Driver Messages Page
     */
    public function messages()
    {
        $driver = auth()->user();
        return view('backend.driver.messages', compact('driver'));
    }

    /**
     * Driver Settings Page
     */
    public function settings()
    {
        $driver = auth()->user();
        return view('backend.driver.settings', compact('driver'));
    }

    /**
     * Driver Application Pending/Status Page
     */
    public function pending()
    {
        $user = auth()->user();
        return view('backend.driver.pending', compact('user'));
    }
}
