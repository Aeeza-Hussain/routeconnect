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
        $defaultTab = $isDriver ? 'my-vehicles' : 'seat-bookings';
        $activeTab = $request->query('tab', $defaultTab);

        // Common Counts
        $counts = [];

        if (!$isDriver) {
            // ─── Passenger Activity Data ───
            $seatBookings = Booking::with(['trip.route', 'trip.driver', 'trip.vehicle', 'fromStop', 'toStop'])
                ->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereNull('booking_type')->orWhere('booking_type', 'shared');
                })
                ->latest()
                ->get();

            $privateBookings = Booking::with(['vehicle.driver'])
                ->where('user_id', $user->id)
                ->where('booking_type', 'private')
                ->latest()
                ->get();

            $myTravelRequests = TravelRequest::with(['applications.driver', 'applications.vehicle'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            $driverOffers = DriverApplication::with(['travelRequest', 'driver', 'vehicle'])
                ->whereHas('travelRequest', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->latest()
                ->get();

            $confirmedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle.driver'])
                ->whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('arrangement_status', 'confirmed')
                ->latest()
                ->get();

            // History (Completed & Cancelled)
            $completedBookings = Booking::with(['trip.route', 'vehicle.driver'])
                ->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled'])
                      ->orWhereIn(DB::raw('LOWER(status)'), ['completed', 'cancelled']);
                })
                ->latest()
                ->get();

            $counts = [
                'seat_bookings'    => $seatBookings->where('booking_status', '!=', 'cancelled')->count(),
                'private_bookings' => $privateBookings->where('booking_status', '!=', 'cancelled')->count(),
                'travel_requests'  => $myTravelRequests->where('status', 'open')->count(),
                'driver_offers'    => $driverOffers->where('status', 'pending')->count(),
                'arrangements'     => $confirmedArrangements->count(),
                'history'          => $completedBookings->count(),
            ];

            return view('frontend.activity.index', compact(
                'user',
                'isDriver',
                'activeTab',
                'counts',
                'seatBookings',
                'privateBookings',
                'myTravelRequests',
                'driverOffers',
                'confirmedArrangements',
                'completedBookings'
            ));
        }

        // ─── Driver Activity Data ───
        $myVehicles = Vehicle::with(['unavailabilities'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $sharedRideListings = Trip::with(['route', 'vehicle'])
            ->where('user_id', $user->id)
            ->latest('trip_date')
            ->get();

        $receivedPrivateBookings = Booking::with(['user', 'vehicle'])
            ->whereHas('vehicle', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('booking_type', 'private')
            ->latest()
            ->get();

        $passengerRequests = TravelRequest::with('user')
            ->where('status', 'open')
            ->latest()
            ->get();

        $myApplications = DriverApplication::with(['travelRequest.user', 'vehicle'])
            ->where('driver_id', $user->id)
            ->latest()
            ->get();

        $confirmedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('arrangement_status', 'confirmed')
            ->latest()
            ->get();

        $driverHistory = Trip::with(['route', 'vehicle'])
            ->where('user_id', $user->id)
            ->whereIn(DB::raw('LOWER(status)'), ['completed', 'cancelled'])
            ->latest('trip_date')
            ->get();

        $counts = [
            'my_vehicles'        => $myVehicles->count(),
            'shared_rides'       => $sharedRideListings->where('status', 'scheduled')->count(),
            'private_bookings'   => $receivedPrivateBookings->where('booking_status', 'confirmed')->count(),
            'passenger_requests' => $passengerRequests->count(),
            'my_applications'    => $myApplications->count(),
            'arrangements'       => $confirmedArrangements->count(),
            'history'            => $driverHistory->count(),
        ];

        return view('frontend.activity.index', compact(
            'user',
            'isDriver',
            'activeTab',
            'counts',
            'myVehicles',
            'sharedRideListings',
            'receivedPrivateBookings',
            'passengerRequests',
            'myApplications',
            'confirmedArrangements',
            'driverHistory'
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
            'pickup_location'      => ['required', 'string', 'max:255'],
            'destination_location' => ['required', 'string', 'max:255'],
            'travel_date'          => ['required', 'date', 'after_or_equal:today'],
            'travel_time'          => ['nullable', 'string', 'max:50'],
            'seats_needed'         => ['required', 'integer', 'min:1', 'max:50'],
            'budget'               => ['nullable', 'numeric', 'min:0'],
            'notes'                => ['nullable', 'string', 'max:1000'],
        ]);

        $travelRequest = TravelRequest::create([
            'user_id'              => $user->id,
            'pickup_location'      => trim($validated['pickup_location']),
            'destination_location' => trim($validated['destination_location']),
            'travel_date'          => $validated['travel_date'],
            'travel_time'          => $validated['travel_time'] ?? null,
            'seats_needed'         => (int) $validated['seats_needed'],
            'budget'               => $validated['budget'] ?? null,
            'notes'                => $validated['notes'] ? trim($validated['notes']) : null,
            'status'               => 'open',
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
            ->with('success', 'Travel request has been closed: ' . $reason);
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

        $validated = $request->validate([
            'offered_fare'  => ['required', 'numeric', 'min:50'],
            'proposed_time' => ['nullable', 'string', 'max:50'],
            'vehicle_id'    => ['nullable', 'exists:vehicles,id'],
            'message'       => ['nullable', 'string', 'max:1000'],
        ]);

        $application = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $driver->id,
            'vehicle_id'        => $validated['vehicle_id'] ?? null,
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
                    'title'               => "Ride: {$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                    'subject_type'        => 'travel_request',
                    'subject_id'          => $travelRequest->id,
                    'status'              => 'active',
                    'arrangement_status'  => 'negotiating',
                    'arrangement_details' => [
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
}

