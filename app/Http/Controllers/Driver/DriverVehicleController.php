<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     * Dedicated Action: Offer a Private Vehicle.
     * Select approved vehicle, set pricing (daily & hourly), date ranges, pickup & service areas, and publish availability.
     */
    public function offerPrivate(Request $request)
    {
        $driver = auth()->user();
        if (!$driver || !$driver->isApprovedDriver()) {
            abort(403, 'Unauthorized access. Only approved drivers can offer private vehicles.');
        }

        $vehicles = Vehicle::where('user_id', $driver->id)
            ->whereIn('status', ['approved', 'Approved', 'Active', 'active'])
            ->get();

        $selectedVehicleId = $request->query('vehicle_id');
        $selectedVehicle   = null;
        if ($selectedVehicleId) {
            $selectedVehicle = $vehicles->firstWhere('id', (int) $selectedVehicleId);
        }

        return view('backend.driver.vehicles.offer_private', compact('driver', 'vehicles', 'selectedVehicle'));
    }

    /**
     * Store / Publish Private Vehicle Availability Listing.
     */
    public function storePrivateListing(Request $request)
    {
        $driver = auth()->user();
        if (!$driver || !$driver->isApprovedDriver()) {
            abort(403, 'Unauthorized access. Only approved drivers can publish private vehicles.');
        }

        $request->validate([
            'vehicle_id'              => ['required', 'integer', 'exists:vehicles,id'],
            'rental_rate_per_day'     => ['required', 'numeric', 'min:500', 'max:500000'],
            'rental_rate_per_hour'    => ['nullable', 'numeric', 'min:50', 'max:50000'],
            'pickup_location'         => ['nullable', 'string', 'max:255'],
            'service_area'            => ['nullable', 'string', 'max:255'],
            'available_from'          => ['nullable', 'date'],
            'available_to'            => ['nullable', 'date', 'after_or_equal:available_from'],
            'is_available_for_rental' => ['nullable', 'boolean'],
            'description'             => ['nullable', 'string', 'max:1000'],
        ], [
            'vehicle_id.required'          => 'Please select an approved vehicle from your fleet.',
            'rental_rate_per_day.required' => 'Daily rental price is required.',
            'rental_rate_per_day.min'      => 'Daily rate must be at least PKR 500.',
            'available_to.after_or_equal'  => 'Available To date must be on or after Available From date.',
        ]);

        $vehicle = Vehicle::where('user_id', $driver->id)->findOrFail($request->vehicle_id);

        if (!$vehicle->isApproved()) {
            return back()->withInput()->withErrors([
                'vehicle_id' => 'This vehicle is not approved yet. Only approved vehicles can be offered for private rental.',
            ]);
        }

        $isAvailable = $request->has('is_available_for_rental')
            ? $request->boolean('is_available_for_rental')
            : true;

        $vehicle->update([
            'rental_rate_per_day'     => $request->rental_rate_per_day,
            'rental_rate_per_hour'    => $request->rental_rate_per_hour ?: null,
            'pickup_location'         => $request->pickup_location ? trim($request->pickup_location) : null,
            'service_area'            => $request->service_area ? trim($request->service_area) : null,
            'available_from'          => $request->available_from ? Carbon::parse($request->available_from) : null,
            'available_to'            => $request->available_to ? Carbon::parse($request->available_to) : null,
            'is_available_for_rental' => $isAvailable,
            'description'             => $request->description ? trim($request->description) : null,
        ]);

        return redirect()->route('driver.vehicles.private')
            ->with('success', "Private vehicle listing for {$vehicle->model} ({$vehicle->registration_no}) published successfully!");
    }

    /**
     * Manage Private Vehicle Listings & Availability.
     */
    public function privateListings(Request $request)
    {
        $driver = auth()->user();
        $vehicles = Vehicle::with([
            'unavailabilities' => fn($q) => $q->orderBy('start_date'),
            'bookings' => fn($q) => $q->where('booking_type', 'private')->latest(),
        ])
        ->where('user_id', $driver->id)
        ->latest()
        ->get();

        return view('backend.driver.vehicles.private_listings', compact('driver', 'vehicles'));
    }

    /**
     * One-click toggle for vehicle private rental availability (Active / Deactivated).
     */
    public function toggleRentalAvailability(Request $request, $id)
    {
        $vehicle = Vehicle::where('user_id', auth()->id())->findOrFail($id);

        if (!$vehicle->isApproved()) {
            return back()->with('error', 'Only approved vehicles can be published or activated.');
        }

        $vehicle->is_available_for_rental = !$vehicle->is_available_for_rental;
        $vehicle->save();

        $statusText = $vehicle->is_available_for_rental ? 'Active & Published' : 'Deactivated';
        return back()->with('success', "Vehicle {$vehicle->registration_no} listing is now {$statusText}.");
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
            'rental_rate_per_hour'    => ['nullable', 'numeric', 'min:50', 'max:50000'],
            'pickup_location'         => ['nullable', 'string', 'max:255'],
            'service_area'            => ['nullable', 'string', 'max:255'],
            'available_from'          => ['nullable', 'date'],
            'available_to'            => ['nullable', 'date', 'after_or_equal:available_from'],
            'is_available_for_rental' => ['nullable', 'boolean'],
            'description'             => ['nullable', 'string', 'max:1000'],
        ], [
            'rental_rate_per_day.required' => 'Please set a daily rental rate for this vehicle.',
            'rental_rate_per_day.min'      => 'Daily rental rate must be at least PKR 500.',
            'available_to.after_or_equal'  => 'Available To date must be on or after Available From date.',
        ]);

        $vehicle->update([
            'rental_rate_per_day'     => $request->rental_rate_per_day,
            'rental_rate_per_hour'    => $request->rental_rate_per_hour ?: null,
            'pickup_location'         => $request->pickup_location ? trim($request->pickup_location) : null,
            'service_area'            => $request->service_area ? trim($request->service_area) : null,
            'available_from'          => $request->available_from ? Carbon::parse($request->available_from) : null,
            'available_to'            => $request->available_to ? Carbon::parse($request->available_to) : null,
            'is_available_for_rental' => $request->boolean('is_available_for_rental'),
            'description'             => $request->description ? trim($request->description) : null,
        ]);

        return redirect()->back()->with('success', "Rental configuration for {$vehicle->registration_no} updated successfully!");
    }

    /**
     * Block unavailable dates or time periods for a vehicle.
     * Prevents overlapping vehicle schedules.
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

        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);

        // 1. Prevent overlapping blocked periods for the same vehicle
        $existingBlock = $vehicle->unavailabilities()
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start)
            ->first();

        if ($existingBlock) {
            return redirect()->back()->withInput()->with('error', "Vehicle already has a blocked schedule from {$existingBlock->start_date->format('d M Y h:i A')} to {$existingBlock->end_date->format('d M Y h:i A')}.");
        }

        // 2. Prevent blocking periods with conflicting active private bookings
        $conflictBooking = $vehicle->bookings()
            ->whereNotIn(DB::raw('LOWER(booking_status)'), ['cancelled'])
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start)
            ->first();

        if ($conflictBooking) {
            return redirect()->back()->withInput()->with('error', "Cannot block this period: Vehicle already has a confirmed private reservation (#{$conflictBooking->booking_reference}) from {$conflictBooking->start_datetime->format('d M Y h:i A')} to {$conflictBooking->end_datetime->format('d M Y h:i A')}.");
        }

        // 3. Prevent blocking periods with conflicting scheduled public trips
        $conflictTrip = $vehicle->trips()
            ->whereNotIn(DB::raw('LOWER(status)'), ['cancelled', 'completed'])
            ->get()
            ->first(fn($t) => $start->lessThan($t->getEndDateTime()) && $end->greaterThan($t->getStartDateTime()));

        if ($conflictTrip) {
            return redirect()->back()->withInput()->with('error', "Cannot block this period: Vehicle is scheduled for public trip #{$conflictTrip->id} ({$conflictTrip->route?->name}) during this timeframe.");
        }

        $vehicle->unavailabilities()->create([
            'start_date' => $start,
            'end_date'   => $end,
            'reason'     => $request->reason ? trim($request->reason) : 'Driver blocked period',
        ]);

        return redirect()->back()->with('success', "Vehicle {$vehicle->registration_no} marked as unavailable from {$start->format('d M Y h:i A')} to {$end->format('d M Y h:i A')}.");
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
