<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminDriverController extends Controller
{
    /**
     * Display Pending Driver Applications List
     */
    public function applications()
    {
        $applications = User::where('role', 'driver')
            ->where('driver_status', 'pending')
            ->latest()
            ->get();

        return view('backend.admin.drivers.applications', compact('applications'));
    }

    /**
     * Display Approved Drivers List
     */
    public function index()
    {
        $drivers = User::where('role', 'driver')
            ->where('driver_status', 'approved')
            ->latest()
            ->get();

        return view('backend.admin.drivers.index', compact('drivers'));
    }

    /**
     * Show Driver Application Details Page
     */
    public function show($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        return view('backend.admin.drivers.show', compact('driver'));
    }

    /**
     * Approve Driver Application
     */
    public function approve($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        $driver->update([
            'driver_status' => 'approved',
        ]);

        return redirect()->route('admin.drivers.applications')->with('success', 'Driver application approved successfully.');
    }

    /**
     * Reject Driver Application
     */
    public function reject($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        $driver->update([
            'driver_status' => 'rejected',
        ]);

        return redirect()->route('admin.drivers.applications')->with('success', 'Driver application rejected.');
    }
}
