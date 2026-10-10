<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Route as RouteModel;

class AdminDashboardController extends Controller
{
    /**
     * Admin Dashboard Overview
     * Shows real database counts for all summary cards and recent records.
     *
     * user_type = 0  → Passenger
     * user_type = 1  → Admin
     * user_type = 2  → Driver
     */
    public function index()
    {
        // ── User Statistics ──────────────────────────────────────────
        $totalUsers      = User::count();
        $totalPassengers = User::where('user_type', 0)->count();
        $totalDrivers    = User::where('user_type', 2)->count();
        $approvedDrivers = User::where('user_type', 2)->where('driver_status', 'approved')->count();
        $pendingDrivers  = User::where('user_type', 2)->where('driver_status', 'pending')->count();
        $rejectedDrivers = User::where('user_type', 2)->where('driver_status', 'rejected')->count();

        // ── Platform Statistics ───────────────────────────────────────
        $totalVehicles = Vehicle::count();
        $totalRoutes   = RouteModel::count();
        $totalTrips    = Trip::count();
        $totalBookings = Booking::count();

        // ── Recent Pending Applications (latest 5) ────────────────────
        $recentApplications = User::where('user_type', 2)
            ->where('driver_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // ── Recent Bookings (latest 5) ────────────────────────────────
        $recentBookings = Booking::with(['user', 'trip.route', 'fromStop', 'toStop'])
            ->latest()
            ->take(5)
            ->get();

        return view('backend.admin.dashboard', compact(
            'totalUsers',
            'totalPassengers',
            'totalDrivers',
            'approvedDrivers',
            'pendingDrivers',
            'rejectedDrivers',
            'totalVehicles',
            'totalRoutes',
            'totalTrips',
            'totalBookings',
            'recentApplications',
            'recentBookings'
        ));
    }

    /**
     * Placeholder handlers for upcoming modules to ensure zero broken links.
     */

    public function placeholderBookings()
    {
        return view('backend.admin.placeholder', [
            'title'       => 'Bookings Management',
            'subtitle'    => 'Review passenger ticket reservations, seat allocations, and payment statuses.',
            'icon'        => 'fa-ticket',
            'iconBg'      => 'bg-rose-50 text-rose-600 border-rose-100',
            'moduleName'  => 'Bookings',
            'tableCount'  => Booking::count(),
            'itemLabel'   => 'Total Bookings',
        ]);
    }

    public function settings()
    {
        return view('backend.admin.settings.index');
    }
}
