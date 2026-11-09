<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Vehicle;
use App\Models\VehicleUnavailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VehicleApiController extends Controller
{
    /**
     * List approved vehicles available for rental/rides.
     */
    public function index(Request $request)
    {
        $query = Vehicle::with(['driver', 'unavailabilities'])
            ->whereIn(DB::raw('LOWER(status)'), ['approved', 'active']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('seats')) {
            $query->where('total_seats', '>=', (int) $request->seats);
        }

        if ($request->filled('max_rate')) {
            $query->where('rental_rate_per_day', '<=', (float) $request->max_rate);
        }

        // If date range provided, filter out vehicles with unavailabilities or bookings
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $query->whereDoesntHave('unavailabilities', function ($q) use ($start, $end) {
                $q->where(function ($sub) use ($start, $end) {
                    $sub->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($overlap) use ($start, $end) {
                            $overlap->where('start_date', '<=', $start)
                                    ->where('end_date', '>=', $end);
                        });
                });
            });

            $query->whereDoesntHave('bookings', function ($q) use ($start, $end) {
                $q->whereNotIn(DB::raw('LOWER(booking_status)'), ['cancelled', 'rejected'])
                  ->where(function ($sub) use ($start, $end) {
                      $sub->whereBetween('start_datetime', [$start, $end])
                          ->orWhereBetween('end_datetime', [$start, $end])
                          ->orWhere(function ($overlap) use ($start, $end) {
                              $overlap->where('start_datetime', '<=', $start)
                                      ->where('end_datetime', '>=', $end);
                          });
                  });
            });
        }

        $vehicles = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $vehicles,
        ], 200);
    }

    /**
     * Get single vehicle details.
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['driver', 'unavailabilities'])->find($id);

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'vehicle' => $vehicle,
        ], 200);
    }

    /**
     * Check vehicle availability for date range.
     */
    public function checkAvailability(Request $request, Vehicle $vehicle)
    {
        $startDate = $request->input('start_date', $request->input('date', Carbon::today()->format('Y-m-d')));
        $endDate   = $request->input('end_date', $startDate);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $conflict = $vehicle->getConflictReason($start, $end);
        $isAvailable = is_null($conflict);

        return response()->json([
            'success'      => true,
            'is_available' => $isAvailable,
            'available'    => $isAvailable,
            'conflict'     => $conflict,
        ], 200);
    }

    /**
     * Register a new vehicle by driver.
     */
    public function store(Request $request)
    {
        $driver = $request->user();

        if (!$driver->isDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Only driver accounts can register vehicles.',
            ], 403);
        }

        $input = $request->all();
        if (!isset($input['type']) && isset($input['vehicle_type'])) {
            $input['type'] = $input['vehicle_type'];
        }

        $validator = Validator::make($input, [
            'model'                   => ['required', 'string', 'max:255'],
            'type'                    => ['required', 'string', 'max:50'],
            'registration_no'         => ['required', 'string', 'max:50', 'unique:vehicles'],
            'total_seats'             => ['required', 'integer', 'min:1', 'max:70'],
            'rental_rate_per_day'     => ['required', 'numeric', 'min:0'],
            'image'                   => ['nullable', 'image', 'max:5120'],
            'is_available_for_rental' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vehicles', 'public');
        }

        $vehicle = Vehicle::create([
            'user_id'                 => $driver->id,
            'model'                   => trim($input['model']),
            'type'                    => trim($input['type']),
            'registration_no'         => strtoupper(trim($input['registration_no'])),
            'total_seats'             => (int) $input['total_seats'],
            'rental_rate_per_day'     => (float) $input['rental_rate_per_day'],
            'image'                   => $imagePath,
            'is_available_for_rental' => $request->boolean('is_available_for_rental', true),
            'status'                  => 'pending', // requires admin approval
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle registered successfully and submitted for admin approval.',
            'vehicle' => $vehicle,
            'data'    => $vehicle,
        ], 201);
    }

    /**
     * Reserve an entire private vehicle.
     */
    public function bookPrivate(Request $request, Vehicle $vehicle)
    {
        $passenger = $request->user();

        if ($vehicle->user_id === $passenger->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot book your own vehicle.',
            ], 422);
        }

        if (!in_array(strtolower($vehicle->status), ['approved', 'active'])) {
            return response()->json([
                'success' => false,
                'message' => 'This vehicle is not approved for rental.',
            ], 422);
        }

        $input = $request->all();
        if (!isset($input['start_datetime']) && isset($input['booking_date'])) {
            $time = $input['booking_time'] ?? '09:00';
            $input['start_datetime'] = Carbon::parse("{$input['booking_date']} {$time}")->toDateTimeString();
            $days = (int) ($input['days'] ?? 1);
            $input['end_datetime'] = Carbon::parse($input['start_datetime'])->addDays($days)->toDateTimeString();
        }

        $validator = Validator::make($input, [
            'start_datetime'       => ['required', 'date', 'after_or_equal:today'],
            'end_datetime'         => ['required', 'date', 'after_or_equal:start_datetime'],
            'pickup_location'      => ['required', 'string', 'max:255'],
            'destination_location' => ['required', 'string', 'max:255'],
            'passenger_notes'      => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $start = Carbon::parse($input['start_datetime']);
        $end = Carbon::parse($input['end_datetime']);

        $conflict = $vehicle->getConflictReason($start, $end);
        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle unavailable for selected dates: ' . $conflict,
            ], 422);
        }

        $days = max(1, (int) ceil($start->diffInHours($end) / 24));
        $totalFare = (float) $vehicle->rental_rate_per_day * $days;

        do {
            $reference = 'RC-PV-' . strtoupper(Str::random(4)) . '-' . mt_rand(1000, 9999);
        } while (Booking::where('booking_reference', $reference)->exists());

        $booking = Booking::create([
            'user_id'              => $passenger->id,
            'driver_id'            => $vehicle->user_id,
            'vehicle_id'           => $vehicle->id,
            'trip_id'              => null,
            'booking_type'         => 'private',
            'pickup_location'      => trim($request->pickup_location),
            'destination_location' => trim($request->destination_location),
            'start_datetime'       => $start,
            'end_datetime'         => $end,
            'days'                 => $days,
            'daily_rate'           => (float) $vehicle->rental_rate_per_day,
            'total_fare'           => $totalFare,
            'seats'                => $vehicle->total_seats,
            'booking_reference'    => $reference,
            'passenger_notes'      => $request->passenger_notes,
            'status'               => 'confirmed',
            'booking_status'       => 'confirmed',
        ]);

        // Notify driver
        Notification::create([
            'user_id' => $vehicle->user_id,
            'message' => "New private rental #{$reference}: {$passenger->name} booked {$vehicle->model} for {$days} day(s).",
            'is_read' => false,
        ]);

        $loadedBooking = $booking->load(['vehicle.driver', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Private vehicle rental booked successfully.',
            'booking' => $loadedBooking,
            'data'    => $loadedBooking,
        ], 201);
    }
}
