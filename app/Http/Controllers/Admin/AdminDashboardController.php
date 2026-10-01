<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminDashboardController extends Controller
{
    /**
     * Admin Dashboard Overview
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalDrivers = User::where('role', 'driver')->count();
        $pendingDrivers = User::where('role', 'driver')->where('driver_status', 'pending')->count();

        return view('backend.admin.dashboard', compact('totalUsers', 'totalDrivers', 'pendingDrivers'));
    }
}
