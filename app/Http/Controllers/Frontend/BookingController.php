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
use Carbon\Carbon;

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
            'tripStops.stop',
            'driver',
            'vehicle',
        ]);

        $orderedStops = $trip->getOrderedStops();
        $numStops = count($orderedStops);

        // Segment parameters from query
        $fromStopId = $request->query('from_stop_id') ?: $request->query('from');
        $toStopId   = $request->query('to_stop_id') ?: $request->query('to');

        $fromIdx = $fromStopId !== null ? $trip->findStopIndex($fromStopId, $orderedStops) : 0;
        $toIdx   = $toStopId !== null ? $trip->findStopIndex($toStopId, $orderedStops) : ($numStops > 0 ? $numStops - 1 : 0);

        if ($fromIdx === null) $fromIdx = 0;
        if ($toIdx === null)   $toIdx = max(0, $numStops - 1);

        $selectedFromStop = $orderedStops[$fromIdx] ?? null;
        $selectedToStop   = $orderedStops[$toIdx] ?? null;

        $fromStopIdentifier = $selectedFromStop['stop_id'] ?? $selectedFromStop['name'] ?? null;
        $toStopIdentifier   = $selectedToStop['stop_id'] ?? $selectedToStop['name'] ?? null;

        $segmentAvailableSeats = $trip->getSegmentAvailableSeats($fromStopIdentifier, $toStopIdentifier);
        $occupiedSeatNumbers   = $trip->getOccupiedSeatNumbersForSegment($fromStopIdentifier, $toStopIdentifier);
        $totalCapacity         = (int) ($trip->total_seats ?: ($trip->vehicle?->total_seats ?: 14));

        return view('frontend.booking_create', compact(
            'trip',
            'orderedStops',
            'fromStopId',
            'toStopId',
            'fromIdx',
            'toIdx',
            'selectedFromStop',
            'selectedToStop',
            'segmentAvailableSeats',
            'occupiedSeatNumbers',
            'totalCapacity'
        ));
    }

    /**
     * Process seat booking for a trip.
     * Prevents overbooking with DB transaction & pessimistic row locking.
     */
    public function store(Request $request, Trip $trip)
    {
        $request->validate([
            'seats'           => ['required', 'integer', 'min:1'],
            'from_stop_id'    => ['nullable'],
            'to_stop_id'      => ['nullable'],
            'seat_numbers'    => ['nullable', 'string', 'max:255'],
            'passenger_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'seats.required' => 'Please select the number of seats to book.',
            'seats.min'      => 'You must book at least 1 seat.',
            'seats.integer'  => 'Number of seats must be a valid integer.',
        ]);

        $seatsToBook = (int) $request->input('seats');

        // Execute inside database transaction with row lock to prevent race conditions & overbooking
        $booking = DB::transaction(function () use ($trip, $seatsToBook, $request) {
            // Pessimistic write lock
            $lockedTrip = Trip::with(['route.routeStops', 'tripStops', 'driver', 'vehicle'])->where('id', $trip->id)->lockForUpdate()->firstOrFail();

            // 1. Check trip status
            if ($lockedTrip->isCancelled()) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip has been cancelled and cannot be booked.',
                ]);
            }

            if ($lockedTrip->isCompleted()) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip has already been completed and cannot be booked.',
                ]);
            }

            if (!$lockedTrip->isScheduled()) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip is no longer active or scheduled for booking.',
                ]);
            }

            // Check if driver is approved
            if ($lockedTrip->driver && !$lockedTrip->driver->isApprovedDriver()) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip is unavailable because the assigned driver is not approved.',
                ]);
            }

            // Check if vehicle is approved
            if ($lockedTrip->vehicle && !$lockedTrip->vehicle->isApproved()) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip is unavailable because the assigned vehicle is not approved.',
                ]);
            }

            // 1b. Check if trip is fully booked
            if ($lockedTrip->available_seats !== null && $lockedTrip->available_seats <= 0) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip is fully booked. No seats are available.',
                ]);
            }

            // 2. Resolve segment stops and order
            $orderedStops = $lockedTrip->getOrderedStops();
            $numStops     = count($orderedStops);

            $reqFromId = $request->input('from_stop_id');
            $reqToId   = $request->input('to_stop_id');

            $fromIdx = $reqFromId !== null ? $lockedTrip->findStopIndex($reqFromId, $orderedStops) : 0;
            $toIdx   = $reqToId !== null ? $lockedTrip->findStopIndex($reqToId, $orderedStops) : (count($orderedStops) - 1);

            if ($fromIdx === null) $fromIdx = 0;
            if ($toIdx === null)   $toIdx = count($orderedStops) - 1;

            if ($numStops >= 2 && $fromIdx >= $toIdx) {
                throw ValidationException::withMessages([
                    'from_stop_id' => 'Selected boarding stop must be located before the drop-off stop in route sequence.',
                ]);
            }

            $fromStop = $orderedStops[$fromIdx] ?? null;
            $toStop   = $orderedStops[$toIdx] ?? null;

            $fromIdentifier = $fromStop['stop_id'] ?? $fromStop['name'] ?? null;
            $toIdentifier   = $toStop['stop_id'] ?? $toStop['name'] ?? null;

            // 3. Segment available seats check
            if ($numStops <= 2) {
                $availableOnSegment = (int) $lockedTrip->available_seats;
            } else {
                $availableOnSegment = $lockedTrip->getSegmentAvailableSeats($fromIdentifier, $toIdentifier);
            }

            if ($availableOnSegment <= 0) {
                throw ValidationException::withMessages([
                    'seats' => 'This trip is fully booked. No seats are available.',
                ]);
            }

            if ($seatsToBook > $availableOnSegment) {
                throw ValidationException::withMessages([
                    'seats' => "Cannot book {$seatsToBook} seats. Only {$availableOnSegment} seat(s) available.",
                ]);
            }

            // 4. Resolve Seat Numbers
            $totalCapacity = (int) ($lockedTrip->total_seats ?: ($lockedTrip->vehicle?->total_seats ?: 14));
            $occupiedSeatNums = $lockedTrip->getOccupiedSeatNumbersForSegment($fromIdentifier, $toIdentifier);

            $requestedSeatNumbers = [];
            if ($request->filled('seat_numbers')) {
                $rawSeats = is_array($request->input('seat_numbers'))
                    ? $request->input('seat_numbers')
                    : explode(',', $request->input('seat_numbers'));

                $requestedSeatNumbers = array_filter(array_map('trim', $rawSeats));

                // Verify none of the requested seat numbers are already occupied
                foreach ($requestedSeatNumbers as $sNum) {
                    if (in_array((string) $sNum, $occupiedSeatNums, true)) {
                        throw ValidationException::withMessages([
                            'seat_numbers' => "Seat #{$sNum} is already occupied on this segment. Please choose another seat.",
                        ]);
                    }
                }
            }

            // Auto-allocate seat numbers if not fully provided
            if (count($requestedSeatNumbers) < $seatsToBook) {
                for ($s = 1; $s <= $totalCapacity && count($requestedSeatNumbers) < $seatsToBook; $s++) {
                    $sStr = (string) $s;
                    if (!in_array($sStr, $occupiedSeatNums, true) && !in_array($sStr, $requestedSeatNumbers, true)) {
                        $requestedSeatNumbers[] = $sStr;
                    }
                }
            }

            $seatNumbersString = !empty($requestedSeatNumbers) ? implode(', ', $requestedSeatNumbers) : null;

            // 5. Generate unique booking reference
            do {
                $reference = 'RC-' . strtoupper(Str::random(4)) . '-' . mt_rand(1000, 9999);
            } while (Booking::where('booking_reference', $reference)->exists());

            // 6. Calculate total fare
            $totalFare = $seatsToBook * ($lockedTrip->fare ?? 0);

            // 7. Create booking record with confirmed status
            $newBooking = Booking::create([
                'user_id'              => auth()->id(),
                'driver_id'            => $lockedTrip->user_id,
                'trip_id'              => $lockedTrip->id,
                'vehicle_id'           => $lockedTrip->vehicle_id,
                'booking_type'         => 'shared',
                'from_stop_id'         => $fromStop['stop_id'] ?? null,
                'to_stop_id'           => $toStop['stop_id'] ?? null,
                'pickup_location'      => $fromStop['name'] ?? $lockedTrip->pickup_point,
                'destination_location' => $toStop['name'] ?? 'Destination',
                'seats'                => $seatsToBook,
                'seat_numbers'         => $seatNumbersString,
                'total_fare'           => $totalFare,
                'booking_reference'    => $reference,
                'passenger_notes'      => $request->passenger_notes ? trim($request->passenger_notes) : null,
                'status'               => 'confirmed',
                'booking_status'       => 'confirmed',
            ]);

            // 8. Update available seats
            if ($numStops <= 2) {
                $lockedTrip->available_seats = max(0, (int) $lockedTrip->available_seats - $seatsToBook);
                $lockedTrip->save();
            } else {
                $lockedTrip->recalculateAvailableSeats();
            }

            // 9. Notifications
            Notification::create([
                'user_id' => auth()->id(),
                'trip_id' => $lockedTrip->id,
                'message' => "Your booking #{$reference} for {$seatsToBook} seat(s) on trip #{$lockedTrip->id} ({$lockedTrip->route?->name}) has been confirmed.",
                'is_read' => false,
            ]);

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
            'vehicle.driver',
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
            'vehicle.driver',
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
            'vehicle.driver',
            'fromStop',
            'toStop',
            'user',
        ]);

        return view('frontend.booking_details', compact('booking'));
    }

    /**
     * Cancel a booking and restore seats to the trip (for shared trips)
     * or free up the vehicle schedule (for private rentals).
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

        // Check if trip is completed
        if ($booking->trip && $booking->trip->isCompleted()) {
            return redirect()->back()->with('error', 'Cannot cancel a booking for a trip that has already been completed.');
        }

        // 3. Atomically restore seats and update booking status
        try {
            DB::transaction(function () use ($booking) {
                $lockedBooking = Booking::with('vehicle')->where('id', $booking->id)->lockForUpdate()->firstOrFail();

                if ($lockedBooking->isCancelled()) {
                    throw new \DomainException('This booking has already been cancelled.');
                }

                // Update booking status first to reflect cancelled state immediately
                $lockedBooking->booking_status = 'cancelled';
                $lockedBooking->status         = 'cancelled';
                $lockedBooking->save();

                // If this is a shared trip booking, restore seats
                if ($lockedBooking->trip_id) {
                    $lockedTrip = Trip::with('vehicle')->where('id', $lockedBooking->trip_id)->lockForUpdate()->first();

                    if ($lockedTrip) {
                        $orderedStops = $lockedTrip->getOrderedStops();
                        if (count($orderedStops) > 2) {
                            $lockedTrip->recalculateAvailableSeats();
                        } else {
                            $seatsToRestore = max(0, (int) $lockedBooking->seats);
                            $maxCapacity    = (int) ($lockedTrip->vehicle?->total_seats ?: ($lockedTrip->total_seats ?: 14));

                            $newAvailableSeats = (int) $lockedTrip->available_seats + $seatsToRestore;

                            // Prevent invalid / overcapacity restoration
                            if ($maxCapacity && $newAvailableSeats > $maxCapacity) {
                                $newAvailableSeats = $maxCapacity;
                            }

                            $lockedTrip->available_seats = $newAvailableSeats;
                            $lockedTrip->save();
                        }
                    }

                    // Notification for trip driver
                    if ($lockedTrip && $lockedTrip->user_id && $lockedTrip->user_id !== $lockedBooking->user_id) {
                        Notification::create([
                            'user_id' => $lockedTrip->user_id,
                            'trip_id' => $lockedBooking->trip_id,
                            'message' => "Booking cancellation: #{$lockedBooking->booking_reference} ({$lockedBooking->seats} seats) on trip #{$lockedTrip->id} was cancelled by passenger.",
                            'is_read' => false,
                        ]);
                    }
                } else {
                    // For private vehicle bookings, notify vehicle owner
                    $vehicleOwnerId = $lockedBooking->vehicle?->user_id;
                    if ($vehicleOwnerId && $vehicleOwnerId !== $lockedBooking->user_id) {
                        Notification::create([
                            'user_id' => $vehicleOwnerId,
                            'message' => "Private vehicle reservation #{$lockedBooking->booking_reference} for {$lockedBooking->vehicle?->registration_no} was cancelled by passenger.",
                            'is_read' => false,
                        ]);
                    }
                }

                // Notification for passenger
                Notification::create([
                    'user_id' => $lockedBooking->user_id,
                    'trip_id' => $lockedBooking->trip_id,
                    'message' => "Your booking #{$lockedBooking->booking_reference} has been cancelled successfully.",
                    'is_read' => false,
                ]);
            });
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $redirectRoute = route('passenger.bookings.index');
        return redirect($redirectRoute)
            ->with('success', 'Booking #' . $booking->booking_reference . ' has been cancelled successfully.');
    }

    /**
     * Display passenger's booking history at /booking
     * Includes tabs for Upcoming, Completed, and Cancelled bookings.
     */
    public function index(Request $request)
    {
        if ($request->filled('trip_id')) {
            return redirect()->route('booking.create', ['trip' => $request->trip_id]);
        }

        $tab = $request->query('tab', 'all');
        $counts = ['all' => 0, 'upcoming' => 0, 'completed' => 0, 'cancelled' => 0];
        $bookings = collect();

        if (auth()->check()) {
            $baseQuery = Booking::with([
                'trip.route',
                'trip.driver',
                'trip.vehicle',
                'vehicle.driver',
                'fromStop',
                'toStop',
            ])
            ->where('user_id', auth()->id())
            ->latest();

            $all = (clone $baseQuery)->get();
            $now = now();

            $counts['all'] = $all->count();
            $counts['cancelled'] = $all->filter(fn($b) => $b->isCancelled())->count();
            $counts['upcoming'] = $all->filter(function ($b) use ($now) {
                if ($b->isCancelled()) return false;
                if ($b->isPrivate()) {
                    return $b->end_datetime ? $b->end_datetime->greaterThanOrEqualTo($now) : true;
                }
                return $b->trip && $b->trip->trip_date ? Carbon::parse($b->trip->trip_date)->endOfDay()->greaterThanOrEqualTo($now) : true;
            })->count();

            $counts['completed'] = $all->filter(function ($b) use ($now) {
                if ($b->isCancelled()) return false;
                if (strtolower($b->booking_status) === 'completed') return true;
                if ($b->isPrivate()) {
                    return $b->end_datetime && $b->end_datetime->lessThan($now);
                }
                return $b->trip && $b->trip->trip_date && Carbon::parse($b->trip->trip_date)->endOfDay()->lessThan($now);
            })->count();

            $query = (clone $baseQuery);

            if ($tab === 'upcoming') {
                $query->whereNotIn(DB::raw('LOWER(booking_status)'), ['cancelled'])
                      ->where(function ($q) use ($now) {
                          $q->where(function ($q2) use ($now) {
                              $q2->where('booking_type', 'private')
                                 ->where(function ($q3) use ($now) {
                                     $q3->whereNull('end_datetime')->orWhere('end_datetime', '>=', $now);
                                 });
                          })->orWhere(function ($q2) use ($now) {
                              $q2->where(function ($q3) {
                                  $q3->whereNull('booking_type')->orWhere('booking_type', 'shared');
                              })->whereHas('trip', function ($qt) use ($now) {
                                  $qt->where('trip_date', '>=', $now->toDateString());
                              });
                          });
                      });
            } elseif ($tab === 'completed') {
                $query->where(function ($q) use ($now) {
                    $q->where(DB::raw('LOWER(booking_status)'), 'completed')
                      ->orWhere(function ($q2) use ($now) {
                          $q2->whereNotIn(DB::raw('LOWER(booking_status)'), ['cancelled'])
                             ->where(function ($q3) use ($now) {
                                 $q3->where(function ($qp) use ($now) {
                                     $qp->where('booking_type', 'private')
                                        ->where('end_datetime', '<', $now);
                                 })->orWhere(function ($qs) use ($now) {
                                     $qs->where(function ($q4) {
                                         $q4->whereNull('booking_type')->orWhere('booking_type', 'shared');
                                     })->whereHas('trip', function ($qt) use ($now) {
                                         $qt->where('trip_date', '<', $now->toDateString());
                                     });
                                 });
                             });
                      });
                });
            } elseif ($tab === 'cancelled') {
                $query->where(DB::raw('LOWER(booking_status)'), 'cancelled');
            }

            $bookings = $query->paginate(10)->withQueryString();
        }

        return view('frontend.booking', compact('bookings', 'counts', 'tab'));
    }
}
