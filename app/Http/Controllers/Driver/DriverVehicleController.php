<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Notification;
use App\Models\User;

class DriverVehicleController extends Controller
{
    /**
     * Display a listing of vehicles belonging to the authenticated driver.
     */
    public function index()
    {
        $driver   = auth()->user();
        $vehicles = Vehicle::where('user_id', $driver->id)->latest()->get();

        return view('backend.driver.vehicles.index', compact('driver', 'vehicles'));
    }

    /**
     * Show form for registering a new vehicle.
     */
    public function create()
    {
        $driver = auth()->user();
        if (!$driver || !$driver->isApprovedDriver()) {
            abort(403, 'Unauthorized access. Only approved drivers can register vehicles.');
        }
        return view('backend.driver.vehicles.create', compact('driver'));
    }

    /**
     * Store a new vehicle registered by the driver.
     * Starts with status 'pending' until Admin approval.
     */
    public function store(Request $request)
    {
        $driver = auth()->user();
        if (!$driver || !$driver->isApprovedDriver()) {
            abort(403, 'Unauthorized access. Only approved drivers can register vehicles.');
        }

        $request->validate([
            'registration_no' => ['required', 'string', 'max:50', 'unique:vehicles,registration_no'],
            'type'            => ['required', 'in:Van,Daba,Car,Bus,Other'],
            'model'           => ['required', 'string', 'max:100'],
            'total_seats'     => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'registration_no.required' => 'Vehicle registration number is required.',
            'registration_no.unique'   => 'This vehicle registration number is already registered in the system.',
            'total_seats.min'          => 'Total seats must be at least 1.',
        ]);

        $vehicle = Vehicle::create([
            'user_id'         => auth()->id(),
            'registration_no' => strtoupper(trim($request->registration_no)),
            'type'            => $request->type,
            'model'           => trim($request->model),
            'total_seats'     => (int) $request->total_seats,
            'status'          => 'pending',
        ]);

        // Notify Admins
        $admins = User::where('user_type', 1)->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'message' => "Driver " . auth()->user()->name . " registered a new vehicle '{$vehicle->registration_no}' ({$vehicle->model}). Pending approval.",
                'is_read' => false,
            ]);
        }

        return redirect()->route('driver.vehicles.index')
            ->with('success', "Vehicle '{$vehicle->registration_no}' submitted successfully! It is pending admin approval before it can be assigned to trips.");
    }

    /**
     * View vehicle details.
     */
    public function show($id)
    {
        $vehicle = Vehicle::where('user_id', auth()->id())->findOrFail($id);
        return view('backend.driver.vehicles.show', compact('vehicle'));
    }
}
