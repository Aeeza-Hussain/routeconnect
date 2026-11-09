<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingApiController extends Controller
{
    /**
     * List bookings for authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isDriver()) {
            $query = Booking::with(['user', 'trip.route', 'vehicle', 'fromStop', 'toStop'])
                ->where(function ($q) use ($user) {
                    $q->where('driver_id', $user->id)
                      ->orWhereHas('trip', fn($t) => $t->where('user_id', $user->id))
                      ->orWhereHas('vehicle', fn($v) => $v->where('user_id', $user->id));
                });
        } else {
            $query = Booking::with(['vehicle.driver', 'trip.driver', 'trip.route', 'fromStop', 'toStop'])
                ->where('user_id', $user->id);
        }

        if ($request->filled('type')) {
            if ($request->type === 'private') {
                $query->where('booking_type', 'private');
            } elseif ($request->type === 'shared') {
                $query->where(function ($q) {
                    $q->whereNull('booking_type')->orWhere('booking_type', 'shared');
                });
            }
        }

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(booking_status) = ?', [strtolower($request->status)]);
        }

        $bookings = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $bookings,
        ], 200);
    }

    /**
     * View single booking details.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $booking = Booking::with(['user', 'driver', 'trip.route', 'trip.driver', 'vehicle.driver', 'fromStop', 'toStop'])
            ->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $isPassenger = ($booking->user_id === $user->id);
        $isDriver = ($booking->driver_id === $user->id)
            || ($booking->trip && $booking->trip->user_id === $user->id)
            || ($booking->vehicle && $booking->vehicle->user_id === $user->id);

        if (!$isPassenger && !$isDriver && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this booking record.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking,
        ], 200);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        $user = $request->user();

        $isPassenger = ($booking->user_id === $user->id);
        $isDriver = ($booking->driver_id === $user->id)
            || ($booking->trip && $booking->trip->user_id === $user->id)
            || ($booking->vehicle && $booking->vehicle->user_id === $user->id);

        if (!$isPassenger && !$isDriver && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        if ($booking->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is already cancelled.',
            ], 422);
        }

        if ($booking->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Completed bookings cannot be cancelled.',
            ], 422);
        }

        $reason = $request->input('reason', 'Cancelled via Mobile App');

        DB::transaction(function () use ($booking, $user, $isPassenger, $reason) {
            if ($booking->trip_id) {
                $lockedTrip = Trip::where('id', $booking->trip_id)->lockForUpdate()->first();
                if ($lockedTrip) {
                    $seatsToRestore = max(0, (int) $booking->seats);
                    $lockedTrip->available_seats += $seatsToRestore;
                    $lockedTrip->save();
                }
            }

            $booking->update([
                'booking_status' => 'cancelled',
                'status'         => 'cancelled',
            ]);

            $recipientId = $isPassenger ? ($booking->driver_id ?? $booking->trip?->user_id ?? $booking->vehicle?->user_id) : $booking->user_id;
            if ($recipientId) {
                Notification::create([
                    'user_id' => $recipientId,
                    'message' => "Booking #{$booking->booking_reference} was cancelled by {$user->name}. Reason: {$reason}",
                    'is_read' => false,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Booking #{$booking->booking_reference} cancelled successfully.",
            'booking' => $booking->fresh(),
        ], 200);
    }
}
