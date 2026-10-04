<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;

class DriverTripController extends Controller
{
    /**
     * Display a listing of trips belonging to the authenticated driver.
     */
    public function index(Request $request)
    {
        $driver = auth()->user();
        $query  = Trip::where('user_id', $driver->id)->with(['route', 'vehicle']);

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('route', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('start_location', 'like', "%{$search}%")
                  ->orWhere('end_location', 'like', "%{$search}%");
            });
        }

        $trips = $query->latest('trip_date')->latest('departure_time')->get();

        return view('backend.driver.trips.index', compact('driver', 'trips'));
    }

    /**
     * Display trip details including route stops and expected timings.
     * Enforces that the driver can only view their own trips.
     */
    public function show($id)
    {
        $trip = Trip::with([
            'route',
            'vehicle',
            'tripStops.stop',
            'bookings.user',
            'bookings.fromStop',
            'bookings.toStop',
            'tripMessages.user'
        ])->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only view your own trips.');
        }

        return view('backend.driver.trips.show', compact('trip'));
    }

    /**
     * Show form to edit trip where appropriate.
     * Enforces that the driver can only edit their own trips.
     */
    public function edit($id)
    {
        $trip = Trip::with(['route', 'vehicle'])->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only edit your own trips.');
        }

        return view('backend.driver.trips.edit', compact('trip'));
    }

    /**
     * Update trip operational details (status, departure time, pickup point, available seats).
     * Enforces ownership validation and seat capacity limits.
     */
    public function update(Request $request, $id)
    {
        $trip = Trip::with('vehicle')->findOrFail($id);

        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only update your own trips.');
        }

        $maxSeats = $trip->vehicle ? $trip->vehicle->total_seats : 100;

        $validated = $request->validate([
            'status'          => 'required|string|in:scheduled,boarding,departed,delayed,completed,cancelled',
            'departure_time'  => 'required',
            'pickup_point'    => 'nullable|string|max:255',
            'available_seats' => "required|integer|min:0|max:{$maxSeats}",
        ]);

        $trip->update($validated);

        return redirect()->route('driver.trips.show', $trip->id)
            ->with('success', 'Trip updated successfully.');
    }
}
