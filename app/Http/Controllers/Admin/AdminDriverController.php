<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminDriverController extends Controller
{
    /**
     * Display Driver Applications List
     */
    public function index()
    {
        $drivers = User::where('role', 'driver')->latest()->get();
        return view('backend.admin.drivers', compact('drivers'));
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

        return back()->with('success', "Driver '{$driver->name}' has been approved!");
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

        return back()->with('success', "Driver '{$driver->name}' application has been rejected.");
    }
}
