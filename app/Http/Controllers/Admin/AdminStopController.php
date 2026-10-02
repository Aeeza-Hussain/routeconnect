<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stop;

class AdminStopController extends Controller
{
    /**
     * Display a listing of stops with search and status filter.
     *
     * GET /admin/stops
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', 'all');

        $query = Stop::withCount('routes');

        // Search by stop name or location
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($status !== 'all' && in_array(strtolower($status), ['active', 'inactive'])) {
            $query->where('status', ucfirst(strtolower($status)));
        }

        $stops = $query->latest()->paginate(15)->withQueryString();

        $totalStops    = Stop::count();
        $activeStops   = Stop::where('status', 'Active')->count();
        $inactiveStops = Stop::where('status', 'Inactive')->count();

        return view('backend.admin.stops.index', compact('stops', 'search', 'status', 'totalStops', 'activeStops', 'inactiveStops'));
    }

    /**
     * Show the form for creating a new stop.
     *
     * GET /admin/stops/create
     */
    public function create()
    {
        return view('backend.admin.stops.create');
    }

    /**
     * Store a newly created stop in storage.
     *
     * POST /admin/stops
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status'   => ['required', 'in:Active,Inactive'],
        ]);

        $stop = Stop::create([
            'name'     => trim($request->name),
            'location' => trim($request->location),
            'status'   => $request->status,
        ]);

        return redirect()->route('admin.stops.index')
            ->with('success', "Stop '{$stop->name}' created successfully.");
    }

    /**
     * Display the specified stop and routes passing through it.
     *
     * GET /admin/stops/{id}
     */
    public function show($id)
    {
        $stop = Stop::with(['routeStops.route'])->findOrFail($id);
        return view('backend.admin.stops.show', compact('stop'));
    }

    /**
     * Show the form for editing the specified stop.
     *
     * GET /admin/stops/{id}/edit
     */
    public function edit($id)
    {
        $stop = Stop::findOrFail($id);
        return view('backend.admin.stops.edit', compact('stop'));
    }

    /**
     * Update the specified stop in storage.
     *
     * PUT /admin/stops/{id}
     */
    public function update(Request $request, $id)
    {
        $stop = Stop::findOrFail($id);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status'   => ['required', 'in:Active,Inactive'],
        ]);

        $stop->update([
            'name'     => trim($request->name),
            'location' => trim($request->location),
            'status'   => $request->status,
        ]);

        return redirect()->route('admin.stops.index')
            ->with('success', "Stop '{$stop->name}' updated successfully.");
    }

    /**
     * Remove the specified stop from storage.
     *
     * DELETE /admin/stops/{id}
     */
    public function destroy($id)
    {
        $stop = Stop::findOrFail($id);
        $name = $stop->name;

        $stop->delete();

        return redirect()->route('admin.stops.index')
            ->with('success', "Stop '{$name}' deleted successfully.");
    }
}
