<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DriverApplication;
use App\Models\Message;
use App\Models\Notification;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Display the role-based My Activity page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isDriver = $user->isDriver();

        $defaultTab = $isDriver ? 'shared-rides' : 'seat-bookings';
        $activeTab = $request->query('tab', $defaultTab);

        // Aliases for tab links
        if ($activeTab === 'driver-offers') $activeTab = 'applications-received';
        if ($activeTab === 'arrangements') $activeTab = 'confirmed-arrangements';
        if ($activeTab === 'history') $activeTab = 'completed-trips';
        if ($activeTab === 'private-bookings' && $isDriver && !$request->has('tab')) $activeTab = 'shared-rides';

        $counts = [];

        if (!$isDriver) {
            // ══════════════════════════════════════════════════════════
            // 1. PASSENGER ACTIVITY DATA
            // ══════════════════════════════════════════════════════════

            // 1.1 My Seat Bookings (Shared)
            $seatBookings = Booking::with(['trip.route', 'trip.driver', 'trip.vehicle', 'fromStop', 'toStop'])
                ->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereNull('booking_type')->orWhere('booking_type', 'shared');
                })
                ->whereNotIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled'])
                ->latest()
                ->get();

            // 1.2 My Private Vehicle Bookings
            $privateBookings = Booking::with(['vehicle.driver', 'driver'])
                ->where('user_id', $user->id)
                ->where('booking_type', 'private')
                ->whereNotIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled'])
                ->latest()
                ->get();

            // 1.3 My Travel Requests (Active / Open / Confirmed)
            $myTravelRequests = TravelRequest::with(['applications.driver', 'applications.vehicle', 'selectedApplication.driver'])
                ->where('user_id', $user->id)
                ->whereNotIn(DB::raw('LOWER(status)'), ['completed', 'cancelled'])
                ->latest()
                ->get();

            // 1.4 Applications Received on Passenger Requests
            $applicationsReceived = DriverApplication::with(['travelRequest', 'driver', 'vehicle'])
                ->whereHas('travelRequest', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereNotIn(DB::raw('LOWER(status)'), ['rejected', 'cancelled'])
                ->latest()
                ->get();

            // 1.5 Confirmed Arrangements
            $confirmedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle.driver'])
                ->whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('arrangement_status', 'confirmed')
                ->latest()
                ->get();

            // 1.6 Completed Trips
            $completedBookings = Booking::with(['trip.route', 'trip.driver', 'vehicle.driver', 'fromStop', 'toStop'])
                ->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(booking_status)'), ['completed'])
                      ->orWhereIn(DB::raw('LOWER(status)'), ['completed']);
                })
                ->latest()
                ->get();

            $completedRequests = TravelRequest::with(['selectedApplication.driver', 'selectedApplication.vehicle'])
                ->where('user_id', $user->id)
                ->where(DB::raw('LOWER(status)'), 'completed')
                ->latest()
                ->get();

            $completedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle.driver'])
                ->whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('arrangement_status', 'completed')
                ->latest()
                ->get();

            // 1.7 Cancelled and Expired Records
            $cancelledBookings = Booking::with(['trip.route', 'trip.driver', 'vehicle.driver', 'fromStop', 'toStop'])
                ->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(booking_status)'), ['cancelled', 'rejected'])
                      ->orWhereIn(DB::raw('LOWER(status)'), ['cancelled', 'rejected']);
                })
                ->latest()
                ->get();

            $cancelledRequests = TravelRequest::with(['applications.driver'])
                ->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(status)'), ['cancelled', 'expired'])
                      ->orWhere(function ($inner) {
                          $inner->where('status', 'open')
                                ->whereNotNull('expires_at')
                                ->where('expires_at', '<', now());
                      });
                })
                ->latest()
                ->get();

            $counts = [
                'seat_bookings'          => $seatBookings->count(),
                'private_bookings'       => $privateBookings->count(),
                'travel_requests'        => $myTravelRequests->count(),
                'applications_received'  => $applicationsReceived->count(),
                'confirmed_arrangements' => $confirmedArrangements->count(),
                'completed_trips'        => $completedBookings->count() + $completedRequests->count() + $completedArrangements->count(),
                'cancelled_expired'      => $cancelledBookings->count() + $cancelledRequests->count(),
                // Backwards compat keys
                'driver_offers'          => $applicationsReceived->count(),
                'arrangements'           => $confirmedArrangements->count(),
                'history'                => $completedBookings->count() + $completedRequests->count(),
            ];

            return view('frontend.activity.index', compact(
                'user',
                'isDriver',
                'activeTab',
                'counts',
                'seatBookings',
                'privateBookings',
                'myTravelRequests',
                'applicationsReceived',
                'confirmedArrangements',
                'completedBookings',
                'completedRequests',
                'completedArrangements',
                'cancelledBookings',
                'cancelledRequests'
            ));
        }

        // ══════════════════════════════════════════════════════════
        // 2. DRIVER ACTIVITY DATA
        // ══════════════════════════════════════════════════════════

        // 2.1 My Shared Ride Listings
        $sharedRideListings = Trip::with(['route', 'vehicle', 'bookings'])
            ->where('user_id', $user->id)
            ->whereNotIn(DB::raw('LOWER(status)'), ['completed', 'cancelled'])
            ->latest('trip_date')
            ->get();

        // 2.2 My Private Vehicle Listings
        $myVehicles = Vehicle::with(['unavailabilities'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // 2.3 My Bookings (Shared seat bookings & private rentals received by driver)
        $myBookingsQuery = Booking::with(['user', 'trip.route', 'vehicle', 'fromStop', 'toStop'])
            ->where(function ($q) use ($user) {
                $q->where('driver_id', $user->id)
                  ->orWhereHas('trip', fn($t) => $t->where('user_id', $user->id))
                  ->orWhereHas('vehicle', fn($v) => $v->where('user_id', $user->id));
            })
            ->whereNotIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled']);

        if ($request->filled('booking_type_filter')) {
            if ($request->booking_type_filter === 'private') {
                $myBookingsQuery->where('booking_type', 'private');
            } elseif ($request->booking_type_filter === 'shared') {
                $myBookingsQuery->where(function ($q) {
                    $q->whereNull('booking_type')->orWhere('booking_type', 'shared');
                });
            }
        }
        $myBookings = $myBookingsQuery->latest()->get();

        // 2.4 Passenger Requests (Open requests in marketplace)
        $passengerRequests = TravelRequest::with(['user', 'applications' => fn($q) => $q->where('driver_id', $user->id)])
            ->where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->when($request->query('filter_date'), fn($q, $d) => $q->whereDate('travel_date', $d))
            ->when($request->query('filter_vehicle'), fn($q, $v) => $q->where('preferred_vehicle_type', $v))
            ->when($request->query('filter_route'), function ($q, $route) {
                $q->where(function ($inner) use ($route) {
                    $inner->where('pickup_location', 'like', "%{$route}%")
                          ->orWhere('destination_location', 'like', "%{$route}%");
                });
            })
            ->latest()
            ->get();

        // 2.5 My Submitted Applications
        $myApplications = DriverApplication::with(['travelRequest.user', 'vehicle'])
            ->where('driver_id', $user->id)
            ->whereNotIn(DB::raw('LOWER(status)'), ['cancelled', 'rejected'])
            ->latest()
            ->get();

        // 2.6 Confirmed Arrangements
        $confirmedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle', 'confirmedBooking.user'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('arrangement_status', 'confirmed')
            ->latest()
            ->get();

        // 2.7 Completed Trips
        $driverCompletedTrips = Trip::with(['route', 'vehicle', 'bookings.user'])
            ->where('user_id', $user->id)
            ->where(DB::raw('LOWER(status)'), 'completed')
            ->latest('trip_date')
            ->get();

        $driverCompletedBookings = Booking::with(['user', 'trip.route', 'vehicle'])
            ->where(function ($q) use ($user) {
                $q->where('driver_id', $user->id)
                  ->orWhereHas('trip', fn($t) => $t->where('user_id', $user->id))
                  ->orWhereHas('vehicle', fn($v) => $v->where('user_id', $user->id));
            })
            ->where(function ($q) {
                $q->whereIn(DB::raw('LOWER(booking_status)'), ['completed'])
                  ->orWhereIn(DB::raw('LOWER(status)'), ['completed']);
            })
            ->latest()
            ->get();

        $driverCompletedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle', 'confirmedBooking.user'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('arrangement_status', 'completed')
            ->latest()
            ->get();

        // 2.8 Cancelled and Expired Records
        $driverCancelledTrips = Trip::with(['route', 'vehicle'])
            ->where('user_id', $user->id)
            ->where(DB::raw('LOWER(status)'), 'cancelled')
            ->latest('trip_date')
            ->get();

        $driverCancelledBookings = Booking::with(['user', 'trip.route', 'vehicle'])
            ->where(function ($q) use ($user) {
                $q->where('driver_id', $user->id)
                  ->orWhereHas('trip', fn($t) => $t->where('user_id', $user->id))
                  ->orWhereHas('vehicle', fn($v) => $v->where('user_id', $user->id));
            })
            ->where(function ($q) {
                $q->whereIn(DB::raw('LOWER(booking_status)'), ['cancelled', 'rejected'])
                  ->orWhereIn(DB::raw('LOWER(status)'), ['cancelled', 'rejected']);
            })
            ->latest()
            ->get();

        $driverCancelledApplications = DriverApplication::with(['travelRequest.user', 'vehicle'])
            ->where('driver_id', $user->id)
            ->whereIn(DB::raw('LOWER(status)'), ['cancelled', 'rejected'])
            ->latest()
            ->get();

        $counts = [
            'shared_rides'           => $sharedRideListings->count(),
            'my_vehicles'            => $myVehicles->count(),
            'my_bookings'            => $myBookings->count(),
            'passenger_requests'     => $passengerRequests->count(),
            'my_applications'        => $myApplications->count(),
            'confirmed_arrangements' => $confirmedArrangements->count(),
            'completed_trips'        => $driverCompletedTrips->count() + $driverCompletedBookings->count() + $driverCompletedArrangements->count(),
            'cancelled_expired'      => $driverCancelledTrips->count() + $driverCancelledBookings->count() + $driverCancelledApplications->count(),
            // Backwards compat keys
            'private_bookings'       => $myBookings->where('booking_type', 'private')->count(),
            'arrangements'           => $confirmedArrangements->count(),
            'history'                => $driverCompletedTrips->count() + $driverCompletedBookings->count(),
        ];

        return view('frontend.activity.index', compact(
            'user',
            'isDriver',
            'activeTab',
            'counts',
            'sharedRideListings',
            'myVehicles',
            'myBookings',
            'passengerRequests',
            'myApplications',
            'confirmedArrangements',
            'driverCompletedTrips',
            'driverCompletedBookings',
            'driverCompletedArrangements',
            'driverCancelledTrips',
            'driverCancelledBookings',
            'driverCancelledApplications'
        ));
    }

    /**
     * Store a new passenger travel request.
     */
    public function storeRequest(Request $request)
    {
        $user = auth()->user();
        if ($user->isDriver()) {
            return redirect()->back()->with('error', 'Drivers cannot post travel requests. Use Schedule Trip instead.');
        }

        $validated = $request->validate([
            'pickup_location'        => ['required', 'string', 'max:255'],
            'destination_location'   => ['required', 'string', 'max:255'],
            'travel_date'            => ['required', 'date', 'after_or_equal:today'],
            'travel_time'            => ['nullable', 'string', 'max:50'],
            'seats_needed'           => ['required', 'integer', 'min:1', 'max:50'],
            'preferred_vehicle_type' => ['nullable', 'string', 'max:50'],
            'intermediate_stops'     => ['nullable'],
            'budget'                 => ['nullable', 'numeric', 'min:0'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
            'expires_at'             => ['nullable', 'date'],
        ]);

        $stops = null;
        if (!empty($validated['intermediate_stops'])) {
            $stops = is_array($validated['intermediate_stops']) 
                ? $validated['intermediate_stops'] 
                : array_filter(array_map('trim', explode(',', (string) $validated['intermediate_stops'])));
        }

        $travelRequest = TravelRequest::create([
            'user_id'                => $user->id,
            'pickup_location'        => trim($validated['pickup_location']),
            'destination_location'   => trim($validated['destination_location']),
            'intermediate_stops'     => $stops,
            'travel_date'            => $validated['travel_date'],
            'travel_time'            => $validated['travel_time'] ?? null,
            'seats_needed'           => (int) $validated['seats_needed'],
            'preferred_vehicle_type' => $validated['preferred_vehicle_type'] ?? null,
            'budget'                 => $validated['budget'] ?? null,
            'notes'                  => $validated['notes'] ? trim($validated['notes']) : null,
            'status'                 => 'open',
            'expires_at'             => !empty($validated['expires_at']) ? \Carbon\Carbon::parse($validated['expires_at']) : null,
        ]);

        // Notify active drivers
        $drivers = \App\Models\User::where('user_type', 2)
            ->where('driver_status', 'approved')
            ->limit(20)
            ->get();

        foreach ($drivers as $driver) {
            Notification::create([
                'user_id' => $driver->id,
                'message' => "New passenger request: {$travelRequest->pickup_location} to {$travelRequest->destination_location} ({$travelRequest->seats_needed} seats). View in My Activity to apply.",
                'is_read' => false,
            ]);
        }

        return redirect()->route('activity.index', ['tab' => 'travel-requests'])
            ->with('success', 'Your travel request has been posted successfully! Drivers can now view it and submit offers.');
    }

    /**
     * Update an open passenger travel request.
     */
    public function updateRequest(Request $request, TravelRequest $travelRequest)
    {
        $user = auth()->user();

        if ($travelRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized. You can only edit your own travel requests.');
        }

        if (!$travelRequest->isOpen()) {
            return redirect()->back()->with('error', 'Only open travel requests can be edited.');
        }

        $validated = $request->validate([
            'pickup_location'        => ['required', 'string', 'max:255'],
            'destination_location'   => ['required', 'string', 'max:255'],
            'travel_date'            => ['required', 'date', 'after_or_equal:today'],
            'travel_time'            => ['nullable', 'string', 'max:50'],
            'seats_needed'           => ['required', 'integer', 'min:1', 'max:50'],
            'preferred_vehicle_type' => ['nullable', 'string', 'max:50'],
            'intermediate_stops'     => ['nullable'],
            'budget'                 => ['nullable', 'numeric', 'min:0'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ]);

        $stops = null;
        if (!empty($validated['intermediate_stops'])) {
            $stops = is_array($validated['intermediate_stops']) 
                ? $validated['intermediate_stops'] 
                : array_filter(array_map('trim', explode(',', (string) $validated['intermediate_stops'])));
        }

        $travelRequest->update([
            'pickup_location'        => trim($validated['pickup_location']),
            'destination_location'   => trim($validated['destination_location']),
            'intermediate_stops'     => $stops,
            'travel_date'            => $validated['travel_date'],
            'travel_time'            => $validated['travel_time'] ?? null,
            'seats_needed'           => (int) $validated['seats_needed'],
            'preferred_vehicle_type' => $validated['preferred_vehicle_type'] ?? null,
            'budget'                 => $validated['budget'] ?? null,
            'notes'                  => $validated['notes'] ? trim($validated['notes']) : null,
        ]);

        return redirect()->route('activity.index', ['tab' => 'travel-requests'])
            ->with('success', 'Your travel request has been updated successfully!');
    }

    /**
     * Cancel or delete a passenger travel request.
     */
    public function destroyRequest(Request $request, TravelRequest $travelRequest)
    {
        if ($travelRequest->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. You can only manage your own travel requests.');
        }

        $reason = $request->input('reason', 'Passenger resolved or cancelled');
        $travelRequest->update(['status' => 'cancelled']);

        return redirect()->route('activity.index', ['tab' => 'travel-requests'])
            ->with('success', 'Travel request has been marked as cancelled: ' . $reason);
    }

    /**
     * Cancel a booking (accessible by passenger or associated driver).
     */
    public function cancelBooking(Request $request, Booking $booking)
    {
        $user = auth()->user();

        $isPassenger = ($booking->user_id === $user->id);
        $isDriver = ($booking->driver_id === $user->id)
            || ($booking->trip && $booking->trip->user_id === $user->id)
            || ($booking->vehicle && $booking->vehicle->user_id === $user->id);

        if (!$isPassenger && !$isDriver && !$user->isAdmin()) {
            abort(403, 'Unauthorized. You can only cancel bookings where you are the passenger or assigned driver.');
        }

        if ($booking->isCancelled()) {
            return redirect()->back()->with('error', 'This booking has already been cancelled.');
        }

        if ($booking->isCompleted()) {
            return redirect()->back()->with('error', 'Cannot cancel a booking that has already been completed.');
        }

        $reason = $request->input('reason', 'Cancelled from My Activity');

        DB::transaction(function () use ($booking, $user, $isPassenger, $reason) {
            // Restore seats on trip if shared booking
            if ($booking->trip_id) {
                $lockedTrip = Trip::with('vehicle')->where('id', $booking->trip_id)->lockForUpdate()->first();
                if ($lockedTrip) {
                    $seatsToRestore = max(0, (int) $booking->seats);
                    $maxCapacity    = $lockedTrip->vehicle?->total_seats;
                    $newAvailable   = $lockedTrip->available_seats + $seatsToRestore;
                    if ($maxCapacity && $newAvailable > $maxCapacity) {
                        $newAvailable = $maxCapacity;
                    }
                    $lockedTrip->available_seats = $newAvailable;
                    $lockedTrip->save();
                }
            }

            $booking->update([
                'booking_status' => 'cancelled',
                'status'         => 'cancelled',
            ]);

            // Notify counterpart
            $recipientId = $isPassenger ? ($booking->driver_id ?? $booking->trip?->user_id ?? $booking->vehicle?->user_id) : $booking->user_id;
            if ($recipientId) {
                Notification::create([
                    'user_id' => $recipientId,
                    'message' => "Booking #{$booking->booking_reference} was cancelled by {$user->name}. Reason: {$reason}",
                    'is_read' => false,
                ]);
            }
        });

        return redirect()->route('activity.index', ['tab' => 'cancelled-expired'])
            ->with('success', "Booking #{$booking->booking_reference} has been cancelled successfully.");
    }

    /**
     * Driver submits an application / offer to a passenger travel request.
     */
    public function applyToRequest(Request $request, TravelRequest $travelRequest)
    {
        $driver = auth()->user();
        if (!$driver->isApprovedDriver()) {
            return redirect()->back()->with('error', 'Only approved drivers can submit ride offers.');
        }

        if ($travelRequest->user_id === $driver->id) {
            return redirect()->back()->with('error', 'You cannot apply to your own request.');
        }

        if (!$travelRequest->isOpen()) {
            return redirect()->back()->with('error', 'This travel request is no longer open for offers.');
        }

        // Prevent duplicate applications from the same driver to the same request
        $existingApplication = DriverApplication::where('travel_request_id', $travelRequest->id)
            ->where('driver_id', $driver->id)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You have already submitted an offer for this travel request.');
        }

        $validated = $request->validate([
            'offered_fare'    => ['required', 'numeric', 'min:50'],
            'proposed_time'   => ['nullable', 'string', 'max:50'],
            'vehicle_id'      => ['nullable', 'exists:vehicles,id'],
            'available_seats' => ['nullable', 'integer', 'min:1', 'max:50'],
            'message'         => ['nullable', 'string', 'max:1000'],
        ]);

        // Ensure the chosen vehicle belongs to the applying driver and is approved (security check)
        if (!empty($validated['vehicle_id'])) {
            $chosenVehicle = Vehicle::where('id', $validated['vehicle_id'])
                ->where('user_id', $driver->id)
                ->whereIn(DB::raw('LOWER(status)'), ['approved', 'active'])
                ->first();
            if (!$chosenVehicle) {
                return redirect()->back()->with('error', 'The selected vehicle does not belong to you or is not approved.');
            }
        }

        $availableSeats = $validated['available_seats'] ?? null;
        if (!$availableSeats && !empty($validated['vehicle_id'])) {
            $chosenVehicle = $chosenVehicle ?? Vehicle::find($validated['vehicle_id']);
            $availableSeats = $chosenVehicle?->total_seats;
        }

        $application = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $driver->id,
            'vehicle_id'        => $validated['vehicle_id'] ?? null,
            'available_seats'   => $availableSeats,
            'offered_fare'      => $validated['offered_fare'],
            'proposed_time'     => $validated['proposed_time'] ?? null,
            'message'           => $validated['message'] ? trim($validated['message']) : null,
            'status'            => 'pending',
        ]);

        // Passenger Notification
        Notification::create([
            'user_id' => $travelRequest->user_id,
            'message' => "Driver {$driver->name} offered a ride for your request ({$travelRequest->pickup_location} → {$travelRequest->destination_location}) at Rs. {$application->offered_fare}.",
            'is_read' => false,
        ]);

        // Automatically start or find conversation
        $conversation = Conversation::where('subject_type', 'travel_request')
            ->where('subject_id', $travelRequest->id)
            ->whereHas('participants', fn($q) => $q->where('user_id', $driver->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $travelRequest->user_id))
            ->first();

        if (!$conversation) {
            $conversation = DB::transaction(function () use ($driver, $travelRequest, $application, $validated) {
                $conv = Conversation::create([
                    'title'                 => "Ride: {$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                    'subject_type'          => 'travel_request',
                    'subject_id'            => $travelRequest->id,
                    'travel_request_id'     => $travelRequest->id,
                    'driver_application_id' => $application->id,
                    'status'                => 'active',
                    'arrangement_status'    => 'negotiating',
                    'arrangement_details'   => [
                        'route' => "{$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                        'date'  => $travelRequest->travel_date->format('Y-m-d'),
                        'time'  => $validated['proposed_time'] ?? $travelRequest->travel_time ?? 'Flexible',
                        'seats' => $travelRequest->seats_needed,
                        'fare'  => (float) $validated['offered_fare'],
                    ],
                    'created_by'          => $driver->id,
                ]);

                ConversationParticipant::create([
                    'conversation_id' => $conv->id,
                    'user_id'         => $driver->id,
                    'last_read_at'    => now(),
                ]);

                ConversationParticipant::create([
                    'conversation_id' => $conv->id,
                    'user_id'         => $travelRequest->user_id,
                    'last_read_at'    => null,
                ]);

                $note = $validated['message'] ?: "Hello! I can take your trip from {$travelRequest->pickup_location} to {$travelRequest->destination_location} for Rs. " . number_format($validated['offered_fare'], 2) . ".";
                Message::create([
                    'conversation_id' => $conv->id,
                    'sender_id'       => $driver->id,
                    'body'            => $note,
                    'type'            => 'arrangement_proposal',
                    'metadata'        => [
                        'fare'          => $validated['offered_fare'],
                        'proposed_time' => $validated['proposed_time'] ?? null,
                    ],
                ]);

                return $conv;
            });
        }

        return redirect()->route('messages.show', $conversation->id)
            ->with('success', 'Your offer has been submitted and a direct chat opened with the passenger!');
    }

    /**
     * Passenger accepts a single driver application for a travel request.
     * Enforces single-driver selection constraint.
     */
    public function selectApplication(Request $request, TravelRequest $travelRequest, DriverApplication $application)
    {
        $user = auth()->user();
        if ($travelRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized. You can only select drivers for your own travel requests.');
        }

        if ($application->travel_request_id !== $travelRequest->id) {
            abort(404, 'Driver offer does not belong to this travel request.');
        }

        if ($travelRequest->selected_application_id) {
            return redirect()->back()->with('error', 'A driver has already been selected for this travel request.');
        }

        $travelRequest->selectApplication($application);

        // Notify selected driver
        Notification::create([
            'user_id' => $application->driver_id,
            'message' => "Congratulations! {$user->name} accepted your offer for the journey from {$travelRequest->pickup_location} to {$travelRequest->destination_location}.",
            'is_read' => false,
        ]);

        return redirect()->route('activity.index', ['tab' => 'travel-requests'])
            ->with('success', 'Driver offer selected successfully!');
    }
}
