<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Vehicle;
use App\Models\User;

class AdminVehicleController extends Controller
{
    /**
     * Display a listing of vehicles with search and filtering.
     *
     * GET /admin/vehicles
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $type   = $request->input('type', 'all');
        $status = $request->input('status', 'all');

        $query = Vehicle::with('driver');

        // Search by vehicle number (registration_no) or model
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhereHas('driver', function ($dq) use ($search) {
                      $dq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by vehicle type
        if ($type !== 'all' && in_array($type, ['Van', 'Daba', 'Car', 'Bus', 'Other'])) {
            $query->where('type', $type);
        }

        // Filter by status
        if ($status !== 'all' && in_array(strtolower($status), ['active', 'inactive'])) {
            $query->where('status', ucfirst(strtolower($status)));
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();

        // Stat counts
        $counts = [
            'all'      => Vehicle::count(),
            'active'   => Vehicle::where('status', 'Active')->count(),
            'inactive' => Vehicle::where('status', 'Inactive')->count(),
            'van'      => Vehicle::where('type', 'Van')->count(),
            'daba'     => Vehicle::where('type', 'Daba')->count(),
            'car'      => Vehicle::where('type', 'Car')->count(),
            'bus'      => Vehicle::where('type', 'Bus')->count(),
        ];

        return view('backend.admin.vehicles.index', compact('vehicles', 'search', 'type', 'status', 'counts'));
    }

    /**
     * Show the form for creating a new vehicle.
     *
     * GET /admin/vehicles/create
     */
    public function create()
    {
        // Only approved drivers (user_type == 2 AND driver_status == 'approved')
        $approvedDrivers = User::where('user_type', 2)
            ->where('driver_status', 'approved')
            ->orderBy('name')
            ->get();

        return view('backend.admin.vehicles.create', compact('approvedDrivers'));
    }

    /**
     * Store a newly created vehicle.
     *
     * POST /admin/vehicles
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('user_type', 2)->where('driver_status', 'approved');
                }),
            ],
            'registration_no' => ['required', 'string', 'max:50', 'unique:vehicles,registration_no'],
            'type'            => ['required', 'in:Van,Daba,Car,Bus,Other'],
            'model'           => ['required', 'string', 'max:100'],
            'total_seats'     => ['required', 'integer', 'min:1', 'max:100'],
            'status'          => ['required', 'in:Active,Inactive'],
        ], [
            'user_id.required'         => 'Please select an assigned driver.',
            'user_id.exists'           => 'The selected driver must be an approved driver.',
            'registration_no.required' => 'The vehicle number is required.',
            'registration_no.unique'   => 'This vehicle number is already registered in the system.',
            'total_seats.min'          => 'Total seats must be at least 1.',
        ]);

        $vehicle = Vehicle::create([
            'user_id'         => $request->user_id,
            'registration_no' => strtoupper(trim($request->registration_no)),
            'type'            => $request->type,
            'model'           => trim($request->model),
            'total_seats'     => (int) $request->total_seats,
            'status'          => $request->status,
        ]);

        return redirect()->route('admin.vehicles.index')
            ->with('success', "Vehicle '{$vehicle->registration_no}' ({$vehicle->model}) has been added successfully.");
    }

    /**
     * Display the specified vehicle.
     *
     * GET /admin/vehicles/{id}
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['driver', 'trips.route'])->findOrFail($id);
        return view('backend.admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     *
     * GET /admin/vehicles/{id}/edit
     */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Fetch approved drivers, and also include the current owner if needed
        $approvedDrivers = User::where(function ($q) use ($vehicle) {
            $q->where(function ($sub) {
                $sub->where('user_type', 2)->where('driver_status', 'approved');
            })->orWhere('id', $vehicle->user_id);
        })->orderBy('name')->get();

        return view('backend.admin.vehicles.edit', compact('vehicle', 'approvedDrivers'));
    }

    /**
     * Update the specified vehicle.
     *
     * PUT /admin/vehicles/{id}
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('user_type', 2)->where('driver_status', 'approved');
                }),
            ],
            'registration_no' => ['required', 'string', 'max:50', 'unique:vehicles,registration_no,' . $id],
            'type'            => ['required', 'in:Van,Daba,Car,Bus,Other'],
            'model'           => ['required', 'string', 'max:100'],
            'total_seats'     => ['required', 'integer', 'min:1', 'max:100'],
            'status'          => ['required', 'in:Active,Inactive'],
        ], [
            'user_id.required'         => 'Please select an assigned driver.',
            'user_id.exists'           => 'The selected driver must be an approved driver.',
            'registration_no.required' => 'The vehicle number is required.',
            'registration_no.unique'   => 'This vehicle number is already registered by another vehicle.',
            'total_seats.min'          => 'Total seats must be at least 1.',
        ]);

        $vehicle->update([
            'user_id'         => $request->user_id,
            'registration_no' => strtoupper(trim($request->registration_no)),
            'type'            => $request->type,
            'model'           => trim($request->model),
            'total_seats'     => (int) $request->total_seats,
            'status'          => $request->status,
        ]);

        return redirect()->route('admin.vehicles.index')
            ->with('success', "Vehicle '{$vehicle->registration_no}' updated successfully.");
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * DELETE /admin/vehicles/{id}
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $number  = $vehicle->registration_no;

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', "Vehicle '{$number}' has been deleted.");
    }
}
