<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TripApiController extends Controller
{
    /**
     * Search and list scheduled shared rides.
     */
    public function index(Request $request)
    {
        $query = Trip::with(['route.startStop', 'route.endStop', 'vehicle', 'driver'])
            ->where(DB::raw('LOWER(status)'), 'scheduled')
            ->where('trip_date', '>=', Carbon::today()->format('Y-m-d'));

        // Search by from_stop_id and to_stop_id
        if ($request->filled('from_stop_id')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('start_stop_id', $request->from_stop_id);
            });
        }

        if ($request->filled('to_stop_id')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('end_stop_id', $request->to_stop_id);
            });
        }

        // Search by location keywords
        if ($request->filled('from')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('start_location', 'like', '%' . $request->from . '%');
            });
        }

        if ($request->filled('to')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('end_location', 'like', '%' . $request->to . '%');
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('trip_date', $request->date);
        }

        // Seats filter
        if ($request->filled('seats')) {
            $query->where('available_seats', '>=', (int) $request->seats);
        }

        // Price range
        if ($request->filled('min_fare')) {
            $query->where('fare', '>=', (float) $request->min_fare);
        }
        if ($request->filled('max_fare')) {
            $query->where('fare', '<=', (float) $request->max_fare);
        }

        $trips = $query->orderBy('trip_date')->orderBy('departure_time')->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $trips,
        ], 200);
    }

    /**
     * View detailed trip record.
     */
    public function show($id)
    {
        $trip = Trip::with([
            'route.startStop',
            'route.endStop',
            'route.stops',
            'vehicle',
            'driver',
        ])->find($id);

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'trip'    => $trip,
        ], 200);
    }

    /**
     * Driver schedules a new shared commercial trip.
     */
    public function store(Request $request)
    {
        $driver = $request->user();

        if (!$driver->isApprovedDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Only verified and approved drivers can schedule trips.',
            ], 403);
        }

        $input = $request->all();
        if (!isset($input['fare']) && isset($input['price_per_seat'])) {
            $input['fare'] = $input['price_per_seat'];
        }

        $validator = Validator::make($input, [
            'route_id'       => ['required', 'exists:routes,id'],
            'vehicle_id'     => ['required', 'exists:vehicles,id'],
            'trip_date'      => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['required', 'string'],
            'fare'           => ['required', 'numeric', 'min:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Verify vehicle belongs to driver and is approved
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', $driver->id)
            ->whereIn(DB::raw('LOWER(status)'), ['approved', 'active'])
            ->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Selected vehicle is not approved or does not belong to your account.',
            ], 422);
        }

        $fare = (float) ($input['fare'] ?? 0);

        $trip = Trip::create([
            'user_id'         => $driver->id,
            'route_id'        => $request->route_id,
            'vehicle_id'      => $vehicle->id,
            'trip_date'       => $request->trip_date,
            'departure_time'  => $request->departure_time,
            'fare'            => $fare,
            'available_seats' => $request->filled('available_seats') ? (int) $request->available_seats : $vehicle->total_seats,
            'status'          => 'scheduled',
        ]);

        $loadedTrip = $trip->load(['route', 'vehicle']);

        return response()->json([
            'success' => true,
            'message' => 'Trip scheduled successfully.',
            'trip'    => $loadedTrip,
            'data'    => $loadedTrip,
        ], 201);
    }

    /**
     * Passenger books seat(s) on a trip.
     */
    public function book(Request $request, Trip $trip)
    {
        $passenger = $request->user();

        if ($trip->user_id === $passenger->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot book seats on your own trip.',
            ], 422);
        }

        if (strtolower($trip->status) !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'This trip is no longer open for seat bookings.',
            ], 422);
        }

        $input = $request->all();
        if (!isset($input['from_stop_id']) && isset($input['pickup_stop_id'])) {
            $input['from_stop_id'] = $input['pickup_stop_id'];
        }
        if (!isset($input['to_stop_id']) && isset($input['drop_stop_id'])) {
            $input['to_stop_id'] = $input['drop_stop_id'];
        }

        $validator = Validator::make($input, [
            'seats'                => ['required', 'integer', 'min:1', 'max:10'],
            'from_stop_id'         => ['nullable', 'exists:stops,id'],
            'to_stop_id'           => ['nullable', 'exists:stops,id'],
            'pickup_location'      => ['nullable', 'string', 'max:255'],
            'destination_location' => ['nullable', 'string', 'max:255'],
            'passenger_notes'      => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $seatsRequested = (int) $input['seats'];

        try {
            $booking = DB::transaction(function () use ($trip, $passenger, $request, $input, $seatsRequested) {
                // Lock trip row to prevent concurrent overbooking
                $lockedTrip = Trip::where('id', $trip->id)->lockForUpdate()->firstOrFail();

                if ($lockedTrip->available_seats < $seatsRequested) {
                    throw new \DomainException("Only {$lockedTrip->available_seats} seat(s) are available on this trip.");
                }

                $totalFare = (float) $lockedTrip->fare * $seatsRequested;

                do {
                    $reference = 'RC-SH-' . strtoupper(Str::random(4)) . '-' . mt_rand(1000, 9999);
                } while (Booking::where('booking_reference', $reference)->exists());

                $fromStopId = $input['from_stop_id'] ?? null;
                $toStopId = $input['to_stop_id'] ?? null;

                $startStop = $fromStopId ? Stop::find($fromStopId) : $lockedTrip->route?->startStop;
                $endStop = $toStopId ? Stop::find($toStopId) : $lockedTrip->route?->endStop;

                $dateStr = $lockedTrip->trip_date instanceof \DateTimeInterface
                    ? $lockedTrip->trip_date->format('Y-m-d')
                    : substr((string) $lockedTrip->trip_date, 0, 10);

                $newBooking = Booking::create([
                    'user_id'              => $passenger->id,
                    'driver_id'            => $lockedTrip->user_id,
                    'trip_id'              => $lockedTrip->id,
                    'vehicle_id'           => $lockedTrip->vehicle_id,
                    'booking_type'         => 'shared',
                    'from_stop_id'         => $startStop?->id,
                    'to_stop_id'           => $endStop?->id,
                    'pickup_location'      => $request->pickup_location ?: $startStop?->name,
                    'destination_location' => $request->destination_location ?: $endStop?->name,
                    'start_datetime'       => Carbon::parse("{$dateStr} {$lockedTrip->departure_time}"),
                    'seats'                => $seatsRequested,
                    'total_fare'           => $totalFare,
                    'booking_reference'    => $reference,
                    'passenger_notes'      => $request->passenger_notes,
                    'status'               => 'confirmed',
                    'booking_status'       => 'confirmed',
                ]);

                // Atomically deduct seats
                $lockedTrip->available_seats -= $seatsRequested;
                $lockedTrip->save();

                // Notify driver
                Notification::create([
                    'user_id' => $lockedTrip->user_id,
                    'message' => "New seat booking #{$reference}: {$passenger->name} booked {$seatsRequested} seat(s).",
                    'is_read' => false,
                ]);

                return $newBooking;
            });
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $loadedBooking = $booking->load(['trip.route', 'vehicle', 'fromStop', 'toStop']);

        return response()->json([
            'success' => true,
            'message' => 'Seat booking confirmed successfully.',
            'booking' => $loadedBooking,
            'data'    => $loadedBooking,
        ], 201);
    }

    /**
     * Driver cancels a scheduled trip.
     */
    public function cancel(Request $request, Trip $trip)
    {
        $driver = $request->user();

        if ($trip->user_id !== $driver->id && !$driver->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this trip.',
            ], 403);
        }

        if (strtolower($trip->status) === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Trip is already cancelled.',
            ], 422);
        }

        DB::transaction(function () use ($trip, $driver) {
            $trip->update(['status' => 'cancelled']);

            // Cancel any pending/confirmed bookings for this trip
            $bookings = Booking::where('trip_id', $trip->id)
                ->whereNotIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled'])
                ->get();

            foreach ($bookings as $b) {
                $b->update([
                    'booking_status' => 'cancelled',
                    'status'         => 'cancelled',
                ]);

                Notification::create([
                    'user_id' => $b->user_id,
                    'message' => "Your booking #{$b->booking_reference} was cancelled because driver {$driver->name} cancelled the trip.",
                    'is_read' => false,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Trip cancelled and booked passengers notified.',
        ], 200);
    }
}
