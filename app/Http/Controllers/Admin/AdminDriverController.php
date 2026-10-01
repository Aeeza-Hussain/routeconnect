<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminDriverController extends Controller
{
    /**
     * Display ALL driver applications with optional status filter.
     *
     * URL: /admin/drivers/applications?status=pending|approved|rejected|all
     *
     * user_type = 2 means DRIVER.
     * driver_status = pending | approved | rejected
     */
    public function applications(Request $request)
    {
        $status = $request->query('status', 'pending'); // default: pending

        $query = User::where('user_type', 2);

        if ($status !== 'all') {
            $query->where('driver_status', $status);
        }

        $applications = $query->latest()->get();

        // Counts for tab badges
        $counts = [
            'all'      => User::where('user_type', 2)->count(),
            'pending'  => User::where('user_type', 2)->where('driver_status', 'pending')->count(),
            'approved' => User::where('user_type', 2)->where('driver_status', 'approved')->count(),
            'rejected' => User::where('user_type', 2)->where('driver_status', 'rejected')->count(),
        ];

        return view('backend.admin.drivers.applications', compact('applications', 'status', 'counts'));
    }

    /**
     * Display Approved Drivers List
     * URL: /admin/drivers
     */
    public function index()
    {
        $drivers = User::where('user_type', 2)
            ->where('driver_status', 'approved')
            ->latest()
            ->get();

        return view('backend.admin.drivers.index', compact('drivers'));
    }

    /**
     * Show Full Driver Application Details Page
     * URL: /admin/drivers/applications/{id}
     */
    public function show($id)
    {
        $driver = User::where('user_type', 2)->findOrFail($id);
        return view('backend.admin.drivers.show', compact('driver'));
    }

    /**
     * Approve a Driver Application
     * POST /admin/drivers/{id}/approve
     *
     * Sets driver_status = 'approved'
     * user_type remains = 2 (driver) — NEVER changed
     */
    public function approve($id)
    {
        $driver = User::where('user_type', 2)->findOrFail($id);

        $driver->update([
            'driver_status' => 'approved',
        ]);

        return redirect()->route('admin.drivers.applications')
            ->with('success', "Driver '{$driver->name}' has been approved successfully.");
    }

    /**
     * Reject a Driver Application
     * POST /admin/drivers/{id}/reject
     *
     * Sets driver_status = 'rejected'
     * user_type remains = 2 (driver) — NEVER changed
     */
    public function reject($id)
    {
        $driver = User::where('user_type', 2)->findOrFail($id);

        $driver->update([
            'driver_status' => 'rejected',
        ]);

        return redirect()->route('admin.drivers.applications')
            ->with('success', "Driver '{$driver->name}' application has been rejected.");
    }
}
