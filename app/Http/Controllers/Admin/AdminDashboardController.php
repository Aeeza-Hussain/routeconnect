<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\Booking;

class AdminDashboardController extends Controller
{
    /**
     * Admin Dashboard Overview
     */
    public function index()
    {
        $totalUsers    = User::count();
        $totalDrivers  = User::where('user_type', 2)->count();
        $pendingDrivers = User::where('user_type', 2)->where('driver_status', 'pending')->count();
        $totalVehicles = Vehicle::count();
        $totalTrips    = Trip::count();
        $totalBookings = Booking::count();

        $recentApplications = User::where('user_type', 2)
            ->where('driver_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('backend.admin.dashboard', compact(
            'totalUsers',
            'totalDrivers',
            'pendingDrivers',
            'totalVehicles',
            'totalTrips',
            'totalBookings',
            'recentApplications'
        ));
    }
}
