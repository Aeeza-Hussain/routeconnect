<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route as RouteModel;
use App\Models\Stop;
use App\Models\RouteStop;

class AdminRouteController extends Controller
{
    /**
     * Display a listing of routes with search and status filter.
     *
     * GET /admin/routes
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', 'all');

        $query = RouteModel::withCount(['stops', 'trips']);

        // Search by route name, start location, or end location
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('start_location', 'like', '%' . $search . '%')
                  ->orWhere('end_location', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($status !== 'all' && in_array(strtolower($status), ['active', 'inactive'])) {
            $query->where('status', ucfirst(strtolower($status)));
        }

        $routes = $query->latest()->paginate(15)->withQueryString();

        // Stat counts
        $counts = [
            'all'      => RouteModel::count(),
            'active'   => RouteModel::where('status', 'Active')->count(),
            'inactive' => RouteModel::where('status', 'Inactive')->count(),
        ];

        return view('backend.admin.routes.index', compact('routes', 'search', 'status', 'counts'));
    }

    /**
     * Show the form for creating a new route.
     *
     * GET /admin/routes/create
     */
    public function create()
    {
        return view('backend.admin.routes.create');
    }

    /**
     * Store a newly created route in storage.
     *
     * POST /admin/routes
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'start_location' => ['required', 'string', 'max:255'],
            'end_location'   => ['required', 'string', 'max:255'],
            'status'         => ['required', 'in:Active,Inactive'],
        ]);

        $route = RouteModel::create([
            'name'           => trim($request->name),
            'start_location' => trim($request->start_location),
            'end_location'   => trim($request->end_location),
            'status'         => $request->status,
        ]);

        return redirect()->route('admin.routes.index')
            ->with('success', "Route '{$route->name}' created successfully.");
    }

    /**
     * Display the specified route along with assigned stops.
     *
     * GET /admin/routes/{id}
     */
    public function show($id)
    {
        $route = RouteModel::with(['routeStops.stop', 'trips'])->findOrFail($id);

        // All active stops available to add
        $assignedStopIds = $route->routeStops->pluck('stop_id')->toArray();
        $availableStops  = Stop::whereNotIn('id', $assignedStopIds)->orderBy('name')->get();

        $nextOrder = ($route->routeStops->max('stop_order') ?? 0) + 1;

        return view('backend.admin.routes.show', compact('route', 'availableStops', 'nextOrder'));
    }

    /**
     * Show the form for editing the specified route.
     *
     * GET /admin/routes/{id}/edit
     */
    public function edit($id)
    {
        $route = RouteModel::findOrFail($id);
        return view('backend.admin.routes.edit', compact('route'));
    }

    /**
     * Update the specified route in storage.
     *
     * PUT /admin/routes/{id}
     */
    public function update(Request $request, $id)
    {
        $route = RouteModel::findOrFail($id);

        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'start_location' => ['required', 'string', 'max:255'],
            'end_location'   => ['required', 'string', 'max:255'],
            'status'         => ['required', 'in:Active,Inactive'],
        ]);

        $route->update([
            'name'           => trim($request->name),
            'start_location' => trim($request->start_location),
            'end_location'   => trim($request->end_location),
            'status'         => $request->status,
        ]);

        return redirect()->route('admin.routes.index')
            ->with('success', "Route '{$route->name}' updated successfully.");
    }

    /**
     * Remove the specified route from storage.
     *
     * DELETE /admin/routes/{id}
     */
    public function destroy($id)
    {
        $route = RouteModel::findOrFail($id);
        $name  = $route->name;

        $route->delete();

        return redirect()->route('admin.routes.index')
            ->with('success', "Route '{$name}' deleted successfully.");
    }

    /**
     * Assign a stop to this route with order.
     * Prevents duplicate stops on the same route.
     *
     * POST /admin/routes/{id}/stops
     */
    public function addStop(Request $request, $id)
    {
        $route = RouteModel::findOrFail($id);

        $request->validate([
            'stop_id'    => ['required', 'exists:stops,id'],
            'stop_order' => ['nullable', 'integer', 'min:1'],
        ]);

        // Prevent duplicate stop on the same route
        $exists = RouteStop::where('route_id', $route->id)
            ->where('stop_id', $request->stop_id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.routes.show', $route->id)
                ->with('error', 'This stop is already assigned to this route.');
        }

        $order = $request->stop_order;
        if (empty($order)) {
            $order = ($route->routeStops()->max('stop_order') ?? 0) + 1;
        }

        RouteStop::create([
            'route_id'   => $route->id,
            'stop_id'    => $request->stop_id,
            'stop_order' => (int) $order,
        ]);

        $stop = Stop::find($request->stop_id);

        return redirect()->route('admin.routes.show', $route->id)
            ->with('success', "Stop '{$stop->name}' assigned to route with order #{$order}.");
    }

    /**
     * Update the order of an assigned stop on this route.
     *
     * PUT /admin/routes/{id}/stops/{stopId}
     */
    public function updateStopOrder(Request $request, $id, $stopId)
    {
        $request->validate([
            'stop_order' => ['required', 'integer', 'min:1'],
        ]);

        $routeStop = RouteStop::where('route_id', $id)
            ->where(function ($q) use ($stopId) {
                $q->where('id', $stopId)->orWhere('stop_id', $stopId);
            })
            ->firstOrFail();

        $routeStop->update([
            'stop_order' => (int) $request->stop_order,
        ]);

        return redirect()->route('admin.routes.show', $id)
            ->with('success', 'Stop order updated successfully.');
    }

    /**
     * Remove an assigned stop from this route.
     *
     * DELETE /admin/routes/{id}/stops/{stopId}
     */
    public function removeStop($id, $stopId)
    {
        $routeStop = RouteStop::where('route_id', $id)
            ->where(function ($q) use ($stopId) {
                $q->where('id', $stopId)->orWhere('stop_id', $stopId);
            })
            ->firstOrFail();

        $routeStop->delete();

        return redirect()->route('admin.routes.show', $id)
            ->with('success', 'Stop removed from route successfully.');
    }
}
