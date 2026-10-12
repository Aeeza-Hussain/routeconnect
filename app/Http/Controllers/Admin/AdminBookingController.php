<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Trip;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    /**
     * Display a listing of all passenger bookings with filters.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'trip.route', 'trip.driver', 'trip.vehicle', 'fromStop', 'toStop']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = strtolower($request->status);
            $query->where(function ($q) use ($status) {
                $q->whereRaw('LOWER(status) = ?', [$status])
                  ->orWhereRaw('LOWER(booking_status) = ?', [$status]);
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'       => Booking::count(),
            'confirmed' => Booking::whereRaw("LOWER(COALESCE(booking_status, status)) = 'confirmed'")->count(),
            'cancelled' => Booking::whereRaw("LOWER(COALESCE(booking_status, status)) = 'cancelled'")->count(),
        ];

        return view('backend.admin.bookings.index', compact('bookings', 'counts'));
    }

    /**
     * View detailed passenger booking ticket.
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'trip.route', 'trip.driver', 'trip.vehicle', 'fromStop', 'toStop'])->findOrFail($id);
        return view('backend.admin.bookings.show', compact('booking'));
    }

    /**
     * Admin cancel booking and atomically restore seats.
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->isCancelled()) {
            return back()->with('error', 'This booking has already been cancelled.');
        }

        DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->firstOrFail();
            if ($lockedBooking->isCancelled()) {
                return;
            }

            $lockedTrip = Trip::with('vehicle')->where('id', $lockedBooking->trip_id)->lockForUpdate()->first();
            if ($lockedTrip) {
                $seatsToRestore = max(0, (int) $lockedBooking->seats);
                $maxCapacity    = $lockedTrip->vehicle?->total_seats;
                $newAvailable   = $lockedTrip->available_seats + $seatsToRestore;
                if ($maxCapacity && $newAvailable > $maxCapacity) {
                    $newAvailable = $maxCapacity;
                }
                $lockedTrip->available_seats = $newAvailable;
                $lockedTrip->save();
            }

            $lockedBooking->booking_status = 'cancelled';
            $lockedBooking->status         = 'cancelled';
            $lockedBooking->save();

            Notification::create([
                'user_id' => $lockedBooking->user_id,
                'trip_id' => $lockedBooking->trip_id,
                'message' => "Your booking #{$lockedBooking->booking_reference} has been cancelled by system administrator. Reserved seats were restored.",
                'is_read' => false,
            ]);
        });

        return back()->with('success', "Booking #{$booking->booking_reference} has been cancelled and {$booking->seats} seat(s) restored to the trip.");
    }
}
