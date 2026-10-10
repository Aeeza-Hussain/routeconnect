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
     * Display a listing of bookings with search and status filters.
     */
    public function index(Request $request)
    {
        $query = Booking::with([
            'user',
            'trip.route',
            'trip.driver',
            'trip.vehicle',
            'fromStop',
            'toStop',
        ]);

        // Search by booking reference, passenger name, or email
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status (confirmed / cancelled)
        if ($request->filled('status') && $request->status !== 'all') {
            $status = strtolower($request->status);
            $query->where(function ($q) use ($status) {
                $q->where('booking_status', $status)
                  ->orWhere('status', $status);
            });
        }

        // Filter by specific trip ID
        if ($request->filled('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        // Filter by booking date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        // Statistics for summary badges
        $totalBookings     = Booking::count();
        $confirmedBookings = Booking::where(function ($q) {
            $q->where('booking_status', 'confirmed')->orWhere('status', 'confirmed');
        })->count();
        $cancelledBookings = Booking::where(function ($q) {
            $q->where('booking_status', 'cancelled')->orWhere('status', 'cancelled');
        })->count();
        $totalSeatsBooked  = Booking::where(function ($q) {
            $q->where('booking_status', 'confirmed')->orWhere('status', 'confirmed');
        })->sum('seats');

        return view('backend.admin.bookings.index', compact(
            'bookings',
            'totalBookings',
            'confirmedBookings',
            'cancelledBookings',
            'totalSeatsBooked'
        ));
    }

    /**
     * Display the specified booking details.
     */
    public function show($id)
    {
        $booking = Booking::with([
            'user',
            'trip.route.startStop',
            'trip.route.endStop',
            'trip.driver',
            'trip.vehicle',
            'trip.tripStops.stop',
            'fromStop',
            'toStop',
        ])->findOrFail($id);

        return view('backend.admin.bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking as administrator and safely restore trip seats.
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->isCancelled()) {
            return redirect()->back()->with('error', 'This booking has already been cancelled.');
        }

        DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->firstOrFail();

            if ($lockedBooking->isCancelled()) {
                return;
            }

            // Restore seats on trip
            $lockedTrip = Trip::with('vehicle')->where('id', $lockedBooking->trip_id)->lockForUpdate()->first();
            if ($lockedTrip) {
                $seatsToRestore    = max(0, (int) $lockedBooking->seats);
                $maxCapacity       = $lockedTrip->vehicle?->total_seats;
                $newAvailableSeats = $lockedTrip->available_seats + $seatsToRestore;

                if ($maxCapacity && $newAvailableSeats > $maxCapacity) {
                    $newAvailableSeats = $maxCapacity;
                }

                $lockedTrip->available_seats = $newAvailableSeats;
                $lockedTrip->save();
            }

            $lockedBooking->booking_status = 'cancelled';
            $lockedBooking->status         = 'cancelled';
            $lockedBooking->save();

            // Notification for passenger
            Notification::create([
                'user_id' => $lockedBooking->user_id,
                'trip_id' => $lockedBooking->trip_id,
                'message' => "Your booking #{$lockedBooking->booking_reference} has been cancelled by system administration.",
                'is_read' => false,
            ]);
        });

        return redirect()->back()->with('success', "Booking #{$booking->booking_reference} was cancelled and seats have been restored.");
    }
}
