<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trip;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Display the booking form for a trip.
     * Accessible only by authenticated passengers (user_type == 0).
     */
    public function create(Request $request, Trip $trip)
    {
        $trip->load([
            'route.startStop',
            'route.endStop',
            'route.routeStops.stop',
            'driver',
            'vehicle',
        ]);

        return view('frontend.booking_create', compact('trip'));
    }

    /**
     * Process seat booking for a trip.
     * Prevents overbooking with DB transaction & pessimistic row locking.
     */
    public function store(Request $request, Trip $trip)
    {
        $request->validate([
            'seats' => ['required', 'integer', 'min:1'],
        ], [
            'seats.required' => 'Please select the number of seats to book.',
            'seats.min'      => 'You must book at least 1 seat.',
            'seats.integer'  => 'Number of seats must be a valid integer.',
        ]);

        $seatsToBook = (int) $request->input('seats');

        // Execute inside database transaction with row lock to prevent race conditions & overbooking
        $booking = DB::transaction(function () use ($trip, $seatsToBook, $request) {
            // Pessimistic write lock
            $lockedTrip = Trip::with(['route.routeStops'])->where('id', $trip->id)->lockForUpdate()->firstOrFail();

            // 1. Check if fully booked
            if ($lockedTrip->available_seats <= 0) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip is fully booked. No seats are available.',
                ]);
            }

            // 2. Check if sufficient seats are available
            if ($seatsToBook > $lockedTrip->available_seats) {
                throw ValidationException::withMessages([
                    'seats' => "Cannot book {$seatsToBook} seats. Only {$lockedTrip->available_seats} seat(s) available.",
                ]);
            }

            // 3. Decrement available seats
            $lockedTrip->available_seats -= $seatsToBook;
            $lockedTrip->save();

            // 4. Generate unique booking reference
            do {
                $reference = 'RC-' . strtoupper(Str::random(4)) . '-' . mt_rand(1000, 9999);
            } while (Booking::where('booking_reference', $reference)->exists());

            // 5. Determine pickup & drop-off stops
            $fromStopId = $request->input('from_stop_id') 
                ?: ($lockedTrip->route?->start_stop_id ?: $lockedTrip->route?->routeStops->first()?->stop_id);
            $toStopId = $request->input('to_stop_id') 
                ?: ($lockedTrip->route?->end_stop_id ?: $lockedTrip->route?->routeStops->last()?->stop_id);

            // 6. Calculate total fare
            $totalFare = $seatsToBook * ($lockedTrip->fare ?? 0);

            // 7. Create booking record with confirmed status
            $newBooking = Booking::create([
                'user_id'           => auth()->id(),
                'trip_id'           => $lockedTrip->id,
                'from_stop_id'      => $fromStopId,
                'to_stop_id'        => $toStopId,
                'seats'             => $seatsToBook,
                'total_fare'        => $totalFare,
                'booking_reference' => $reference,
                'status'            => 'confirmed',
                'booking_status'    => 'confirmed',
            ]);

            // 8. Notification for passenger
            Notification::create([
                'user_id' => auth()->id(),
                'trip_id' => $lockedTrip->id,
                'message' => "Your booking #{$reference} for {$seatsToBook} seat(s) on trip #{$lockedTrip->id} ({$lockedTrip->route?->name}) has been confirmed.",
                'is_read' => false,
            ]);

            // Notification for driver (if distinct)
            if ($lockedTrip->user_id && $lockedTrip->user_id !== auth()->id()) {
                Notification::create([
                    'user_id' => $lockedTrip->user_id,
                    'trip_id' => $lockedTrip->id,
                    'message' => "New passenger reservation: " . auth()->user()->name . " booked {$seatsToBook} seat(s) on trip #{$lockedTrip->id}.",
                    'is_read' => false,
                ]);
            }

            return $newBooking;
        });

        return redirect()->route('booking.confirmation', $booking->id)
            ->with('success', 'Your seats have been booked successfully!');
    }

    /**
     * Show booking confirmation.
     */
    public function confirmation(Booking $booking)
    {
        // Ensure user is authorized to view this booking confirmation
        if ($booking->user_id !== auth()->id() && (!auth()->user() || !auth()->user()->isAdmin())) {
            abort(403, 'Unauthorized to view this booking confirmation.');
        }

        $booking->load([
            'trip.route',
            'trip.driver',
            'trip.vehicle',
            'fromStop',
            'toStop',
            'user',
        ]);

        return view('frontend.booking_confirmation', compact('booking'));
    }

    /**
     * Display the passenger's My Bookings page (/my-bookings).
     */
    public function myBookings(Request $request)
    {
        $bookings = Booking::with([
            'trip.route',
            'trip.driver',
            'trip.vehicle',
            'fromStop',
            'toStop',
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(10);

        return view('frontend.my_bookings', compact('bookings'));
    }

    /**
     * View detailed information for a single booking (/my-bookings/{booking}).
     */
    public function showBooking(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() && (!auth()->user() || !auth()->user()->isAdmin())) {
            abort(403, 'Unauthorized to view this booking.');
        }

        $booking->load([
            'trip.route.startStop',
            'trip.route.endStop',
            'trip.driver',
            'trip.vehicle',
            'fromStop',
            'toStop',
            'user',
        ]);

        return view('frontend.booking_details', compact('booking'));
    }

    /**
     * Cancel a booking and restore seats to the trip.
     */
    public function cancel(Request $request, Booking $booking)
    {
        // 1. Authorization: Only the booking owner can cancel
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. Only the booking owner can cancel this booking.');
        }

        // 2. Check if already cancelled
        if ($booking->isCancelled()) {
            return redirect()->back()->with('error', 'This booking has already been cancelled.');
        }

        // 3. Atomically restore seats and update booking status
        DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->firstOrFail();

            if ($lockedBooking->isCancelled()) {
                throw new \DomainException('This booking has already been cancelled.');
            }

            // Lock the trip to prevent race conditions & overbooking
            $lockedTrip = Trip::with('vehicle')->where('id', $lockedBooking->trip_id)->lockForUpdate()->first();

            if ($lockedTrip) {
                $seatsToRestore = max(0, (int) $lockedBooking->seats);
                $maxCapacity    = $lockedTrip->vehicle?->total_seats;

                $newAvailableSeats = $lockedTrip->available_seats + $seatsToRestore;

                // Prevent invalid / overcapacity restoration
                if ($maxCapacity && $newAvailableSeats > $maxCapacity) {
                    $newAvailableSeats = $maxCapacity;
                }

                $lockedTrip->available_seats = $newAvailableSeats;
                $lockedTrip->save();
            }

            // Update booking status
            $lockedBooking->booking_status = 'cancelled';
            $lockedBooking->status         = 'cancelled';
            $lockedBooking->save();

            // Notification for passenger
            Notification::create([
                'user_id' => $lockedBooking->user_id,
                'trip_id' => $lockedBooking->trip_id,
                'message' => "Your booking #{$lockedBooking->booking_reference} for {$lockedBooking->seats} seat(s) has been cancelled.",
                'is_read' => false,
            ]);

            // Notification for driver (if distinct)
            if ($lockedTrip && $lockedTrip->user_id && $lockedTrip->user_id !== $lockedBooking->user_id) {
                Notification::create([
                    'user_id' => $lockedTrip->user_id,
                    'trip_id' => $lockedBooking->trip_id,
                    'message' => "Booking cancellation: #{$lockedBooking->booking_reference} ({$lockedBooking->seats} seats) on trip #{$lockedTrip->id} was cancelled by passenger.",
                    'is_read' => false,
                ]);
            }
        });

        return redirect()->route('passenger.bookings.index')
            ->with('success', 'Booking #' . $booking->booking_reference . ' has been cancelled successfully. Your reserved seats have been returned.');
    }

    /**
     * Index or redirect helper for generic /booking route
     */
    public function index(Request $request)
    {
        if ($request->filled('trip_id')) {
            return redirect()->route('booking.create', ['trip' => $request->trip_id]);
        }

        if (auth()->check() && (int) auth()->user()->user_type === 0) {
            return redirect()->route('passenger.bookings.index');
        }

        $bookings = collect();
        return view('frontend.booking', compact('bookings'));
    }
}
