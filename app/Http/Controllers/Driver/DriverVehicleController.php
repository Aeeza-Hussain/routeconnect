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
     * View vehicle details, rental rate configuration, blocked dates, and reservations.
     */
    public function show($id)
    {
        $vehicle = Vehicle::with([
            'trips.route',
            'bookings' => function ($q) {
                $q->where('booking_type', 'private')->with('user')->latest();
            },
            'unavailabilities' => function ($q) {
                $q->orderBy('start_date');
            },
        ])->where('user_id', auth()->id())->findOrFail($id);

        return view('backend.driver.vehicles.show', compact('vehicle'));
    }

    /**
     * Update vehicle private rental availability toggle and daily rate.
     */
    public function updateRentalSettings(Request $request, $id)
    {
        $vehicle = Vehicle::where('user_id', auth()->id())->findOrFail($id);

        if (!$vehicle->isApproved()) {
            return redirect()->back()->with('error', 'Only approved vehicles can be configured for private rentals.');
        }

        $request->validate([
            'rental_rate_per_day'     => ['required', 'numeric', 'min:500', 'max:500000'],
            'is_available_for_rental' => ['nullable', 'boolean'],
            'description'             => ['nullable', 'string', 'max:1000'],
        ], [
            'rental_rate_per_day.required' => 'Please set a daily rental rate for this vehicle.',
            'rental_rate_per_day.min'      => 'Daily rental rate must be at least PKR 500.',
        ]);

        $vehicle->update([
            'rental_rate_per_day'     => $request->rental_rate_per_day,
            'is_available_for_rental' => $request->boolean('is_available_for_rental'),
            'description'             => $request->description ? trim($request->description) : null,
        ]);

        return redirect()->back()->with('success', "Rental configuration for {$vehicle->registration_no} updated successfully!");
    }

    /**
     * Block unavailable dates or time periods for a vehicle.
     */
    public function storeUnavailability(Request $request, $id)
    {
        $vehicle = Vehicle::where('user_id', auth()->id())->findOrFail($id);

        if (!$vehicle->isApproved()) {
            return redirect()->back()->with('error', 'Only approved vehicles can manage availability schedules.');
        }

        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'reason'     => ['nullable', 'string', 'max:255'],
        ], [
            'start_date.required' => 'Please provide a start date.',
            'end_date.required'   => 'Please provide an end date.',
            'end_date.after'      => 'End date must be after the start date.',
        ]);

        $vehicle->unavailabilities()->create([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'reason'     => $request->reason ? trim($request->reason) : 'Driver blocked period',
        ]);

        return redirect()->back()->with('success', "Vehicle {$vehicle->registration_no} marked as unavailable from {$request->start_date} to {$request->end_date}.");
    }

    /**
     * Remove / unblock a blocked date period.
     */
    public function destroyUnavailability($id, $unavailabilityId)
    {
        $vehicle = Vehicle::where('user_id', auth()->id())->findOrFail($id);
        $period = $vehicle->unavailabilities()->findOrFail($unavailabilityId);
        $period->delete();

        return redirect()->back()->with('success', "Blocked schedule period removed successfully. Vehicle is now available for those dates.");
    }
}
