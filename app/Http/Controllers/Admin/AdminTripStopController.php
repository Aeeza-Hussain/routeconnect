<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\RouteStop;

class AdminTripStopController extends Controller
{
    /**
     * Display the trip stops management page.
     *
     * GET /admin/trips/{trip}/stops
     */
    public function index($tripId)
    {
        $trip = Trip::with([
            'route.routeStops.stop',
            'tripStops.stop',
            'driver',
            'vehicle',
        ])->findOrFail($tripId);

        // Route stops ordered by stop_order (to populate the "add" dropdown)
        $routeStops = $trip->route?->routeStops()->with('stop')->orderBy('stop_order')->get() ?? collect();

        // Already-assigned stop IDs for this trip (to exclude from the "add" dropdown)
        $assignedStopIds = $trip->tripStops->pluck('stop_id')->toArray();

        // Available stops = route stops NOT yet assigned to this trip
        $availableRouteStops = $routeStops->filter(
            fn ($rs) => !in_array($rs->stop_id, $assignedStopIds)
        )->values();

        return view('backend.admin.trips.stops', compact(
            'trip',
            'routeStops',
            'availableRouteStops',
            'assignedStopIds'
        ));
    }

    /**
     * Add a stop to the trip with an expected arrival time.
     *
     * POST /admin/trips/{trip}/stops
     */
    public function store(Request $request, $tripId)
    {
        $trip = Trip::with('route.routeStops')->findOrFail($tripId);

        $request->validate([
            'stop_id'       => ['required', 'integer', 'exists:stops,id'],
            'expected_time' => ['required', 'date_format:H:i'],
        ]);

        $stopId = (int) $request->stop_id;

        // 1. Prevent duplicates within the same trip
        $alreadyExists = TripStop::where('trip_id', $tripId)
            ->where('stop_id', $stopId)
            ->exists();

        if ($alreadyExists) {
            return back()->withInput()->withErrors([
                'stop_id' => 'This stop has already been added to this trip.',
            ]);
        }

        // 2. Ensure stop belongs to the trip's route
        $routeStop = RouteStop::where('route_id', $trip->route_id)
            ->where('stop_id', $stopId)
            ->first();

        if (!$routeStop) {
            return back()->withInput()->withErrors([
                'stop_id' => 'The selected stop does not belong to the route assigned to this trip.',
            ]);
        }

        TripStop::create([
            'trip_id'       => $tripId,
            'stop_id'       => $stopId,
            'stop_order'    => $routeStop->stop_order,
            'expected_time' => $request->expected_time . ':00',
        ]);

        return redirect()
            ->route('admin.trips.stops.index', $tripId)
            ->with('success', 'Stop added to trip successfully.');
    }

    /**
     * Update the expected arrival time (and optionally the order) of a trip stop.
     *
     * PUT /admin/trips/{trip}/stops/{tripStop}
     */
    public function update(Request $request, $tripId, $tripStopId)
    {
        $tripStop = TripStop::where('trip_id', $tripId)->findOrFail($tripStopId);

        $request->validate([
            'expected_time' => ['required', 'date_format:H:i'],
            'stop_order'    => ['required', 'integer', 'min:1'],
        ]);

        $tripStop->update([
            'expected_time' => $request->expected_time . ':00',
            'stop_order'    => (int) $request->stop_order,
        ]);

        return redirect()
            ->route('admin.trips.stops.index', $tripId)
            ->with('success', 'Stop timing updated successfully.');
    }

    /**
     * Remove a stop from the trip.
     *
     * DELETE /admin/trips/{trip}/stops/{tripStop}
     */
    public function destroy($tripId, $tripStopId)
    {
        $tripStop = TripStop::where('trip_id', $tripId)->findOrFail($tripStopId);
        $tripStop->delete();

        return redirect()
            ->route('admin.trips.stops.index', $tripId)
            ->with('success', 'Stop removed from trip.');
    }
}
