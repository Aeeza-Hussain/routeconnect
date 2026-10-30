<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ConversationReport;
use App\Models\DriverApplication;
use App\Models\Message;
use App\Models\Notification;
use App\Models\TravelRequest;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * Display the messaging inbox with conversation list and active chat.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get all conversations where current user is a participant
        $conversations = Conversation::with([
            'participants.user',
            'latestMessage.sender',
            'confirmedBooking.vehicle',
        ])
        ->whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->latest('updated_at')
        ->get();

        $activeConversation = null;
        $messages = collect();

        if ($request->filled('conversation_id')) {
            $activeConversation = $conversations->firstWhere('id', (int) $request->conversation_id);
        }

        if (!$activeConversation && $conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
        }

        if ($activeConversation) {
            // Mark conversation as read for current user
            $participant = $activeConversation->participants->firstWhere('user_id', $user->id);
            if ($participant) {
                $participant->update(['last_read_at' => now()]);
            }

            // Mark received messages as read
            Message::where('conversation_id', $activeConversation->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            $messages = $activeConversation->messages()->with('sender')->get();
        }

        return view('frontend.messages.index', compact('conversations', 'activeConversation', 'messages', 'user'));
    }

    /**
     * Show a specific conversation.
     */
    public function show(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        // Strict authorization check
        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. You are not a participant in this conversation.');
        }

        // Mark as read
        $participant = $conversation->participants()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'conversation' => $conversation->load(['participants.user', 'confirmedBooking.vehicle']),
                'messages'     => $conversation->messages()->with('sender')->get(),
            ]);
        }

        return redirect()->route('messages.index', ['conversation_id' => $conversation->id]);
    }

    /**
     * Start or locate a conversation from:
     * 1. Vehicle Listing (Passenger -> Driver)
     * 2. Travel Request (Driver -> Passenger or Passenger -> Request)
     * 3. Driver Application (Passenger <-> Driver)
     */
    public function start(Request $request)
    {
        $user = auth()->user();

        // Case 1: From Vehicle Listing
        if ($request->filled('vehicle_id')) {
            $vehicle = Vehicle::with('driver')->findOrFail($request->vehicle_id);

            if ($vehicle->user_id === $user->id) {
                return redirect()->back()->with('error', 'You cannot message your own vehicle.');
            }

            $driver = $vehicle->driver;
            if (!$driver) {
                return redirect()->back()->with('error', 'Vehicle has no assigned driver.');
            }

            // Find existing conversation to prevent duplicates
            $conversation = Conversation::where('subject_type', 'vehicle')
                ->where('subject_id', $vehicle->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $driver->id))
                ->first();

            if (!$conversation) {
                $conversation = DB::transaction(function () use ($user, $driver, $vehicle) {
                    $conv = Conversation::create([
                        'title'               => "Vehicle: {$vehicle->model} ({$vehicle->registration_no})",
                        'subject_type'        => 'vehicle',
                        'subject_id'          => $vehicle->id,
                        'status'              => 'active',
                        'arrangement_status'  => 'negotiating',
                        'arrangement_details' => [
                            'vehicle_id'  => $vehicle->id,
                            'model'       => $vehicle->model,
                            'seats'       => $vehicle->total_seats,
                            'daily_rate'  => (float) $vehicle->rental_rate_per_day,
                        ],
                        'created_by'          => $user->id,
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id'         => $user->id,
                        'last_read_at'    => now(),
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id'         => $driver->id,
                        'last_read_at'    => null,
                    ]);

                    // Greeting system message
                    Message::create([
                        'conversation_id' => $conv->id,
                        'sender_id'       => $user->id,
                        'body'            => "Hello {$driver->name}! I am interested in booking your {$vehicle->model} ({$vehicle->registration_no}).",
                        'type'            => 'text',
                    ]);

                    Notification::create([
                        'user_id' => $driver->id,
                        'message' => "Passenger {$user->name} started an inquiry for your vehicle {$vehicle->registration_no}.",
                        'is_read' => false,
                    ]);

                    return $conv;
                });
            }

            return redirect()->route('messages.index', ['conversation_id' => $conversation->id]);
        }

        // Case 2: From Travel Request
        if ($request->filled('travel_request_id')) {
            $travelRequest = TravelRequest::with('user')->findOrFail($request->travel_request_id);

            $targetUserId = ($travelRequest->user_id === $user->id)
                ? (int) $request->input('target_driver_id')
                : $travelRequest->user_id;

            if (!$targetUserId || $targetUserId === $user->id) {
                return redirect()->back()->with('error', 'Invalid conversation target.');
            }

            $conversation = Conversation::where('subject_type', 'travel_request')
                ->where('subject_id', $travelRequest->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $targetUserId))
                ->first();

            if (!$conversation) {
                $conversation = DB::transaction(function () use ($user, $targetUserId, $travelRequest) {
                    $conv = Conversation::create([
                        'title'               => "Ride: {$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                        'subject_type'        => 'travel_request',
                        'subject_id'          => $travelRequest->id,
                        'status'              => 'active',
                        'arrangement_status'  => 'negotiating',
                        'arrangement_details' => [
                            'route' => "{$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                            'date'  => $travelRequest->travel_date->format('Y-m-d'),
                            'seats' => $travelRequest->seats_needed,
                            'fare'  => (float) ($travelRequest->budget ?? 0),
                        ],
                        'created_by'          => $user->id,
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id'         => $user->id,
                        'last_read_at'    => now(),
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id'         => $targetUserId,
                        'last_read_at'    => null,
                    ]);

                    Message::create([
                        'conversation_id' => $conv->id,
                        'sender_id'       => $user->id,
                        'body'            => "Hello! Re: Travel Request from {$travelRequest->pickup_location} to {$travelRequest->destination_location}.",
                        'type'            => 'text',
                    ]);

                    return $conv;
                });
            }

            return redirect()->route('messages.index', ['conversation_id' => $conversation->id]);
        }

        // Case 3: From Driver Application
        if ($request->filled('driver_application_id')) {
            $application = DriverApplication::with(['travelRequest', 'driver'])->findOrFail($request->driver_application_id);

            $targetUserId = ($application->driver_id === $user->id)
                ? $application->travelRequest->user_id
                : $application->driver_id;

            $conversation = Conversation::where('subject_type', 'driver_application')
                ->where('subject_id', $application->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $targetUserId))
                ->first();

            if (!$conversation) {
                $conversation = DB::transaction(function () use ($user, $targetUserId, $application) {
                    $conv = Conversation::create([
                        'title'               => "Offer: {$application->travelRequest?->pickup_location} → {$application->travelRequest?->destination_location}",
                        'subject_type'        => 'driver_application',
                        'subject_id'          => $application->id,
                        'status'              => 'active',
                        'arrangement_status'  => 'negotiating',
                        'arrangement_details' => [
                            'fare'  => (float) $application->offered_fare,
                            'time'  => $application->proposed_time,
                        ],
                        'created_by'          => $user->id,
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id'         => $user->id,
                        'last_read_at'    => now(),
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id'         => $targetUserId,
                        'last_read_at'    => null,
                    ]);

                    return $conv;
                });
            }

            return redirect()->route('messages.index', ['conversation_id' => $conversation->id]);
        }

        return redirect()->route('messages.index')->with('error', 'Invalid messaging request.');
    }

    /**
     * Store and send a message within a conversation.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        if ($conversation->isBlocked()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'This conversation is blocked.'], 403);
            }
            return redirect()->back()->with('error', 'This conversation is blocked.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $message = DB::transaction(function () use ($conversation, $user, $validated) {
            $msg = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'body'            => trim($validated['body']),
                'type'            => 'text',
                'is_read'         => false,
            ]);

            $conversation->touch();

            // Update sender's last_read_at
            $conversation->participants()
                ->where('user_id', $user->id)
                ->update(['last_read_at' => now()]);

            // Notify recipient
            $otherParticipant = $conversation->participants()
                ->where('user_id', '!=', $user->id)
                ->first();

            if ($otherParticipant) {
                Notification::create([
                    'user_id' => $otherParticipant->user_id,
                    'message' => "New message from {$user->name}: " . Str::limit($msg->body, 50),
                    'is_read' => false,
                ]);
            }

            return $msg;
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->route('messages.index', ['conversation_id' => $conversation->id]);
    }

    /**
     * API / polling fetch messages for auto-refresh in active chat.
     */
    public function fetchMessages(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            abort(403);
        }

        // Update read marker
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $sinceId = $request->query('since_id');

        $query = $conversation->messages()->with('sender');
        if ($sinceId) {
            $query->where('id', '>', (int) $sinceId);
        }

        $messages = $query->get();

        return response()->json([
            'messages'            => $messages,
            'arrangement_status'  => $conversation->arrangement_status,
            'arrangement_details' => $conversation->arrangement_details,
            'is_blocked'          => $conversation->isBlocked(),
        ]);
    }

    /**
     * Propose an arrangement with specific terms (route, date, fare, seats).
     */
    public function proposeArrangement(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'route' => ['required', 'string', 'max:255'],
            'date'  => ['required', 'date', 'after_or_equal:today'],
            'time'  => ['nullable', 'string', 'max:50'],
            'fare'  => ['required', 'numeric', 'min:50'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $details = [
            'route'       => $validated['route'],
            'date'        => $validated['date'],
            'time'        => $validated['time'] ?? 'Flexible',
            'fare'        => (float) $validated['fare'],
            'seats'       => (int) ($validated['seats'] ?? 1),
            'notes'       => $validated['notes'] ?? '',
            'proposed_by' => $user->name,
            'proposed_at' => now()->toDateTimeString(),
        ];

        DB::transaction(function () use ($conversation, $user, $details) {
            $conversation->update([
                'arrangement_status'  => 'negotiating',
                'arrangement_details' => $details,
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'body'            => "Proposed Arrangement: {$details['route']} on {$details['date']} ({$details['time']}) for Rs. " . number_format($details['fare'], 2) . " ({$details['seats']} seat(s)).",
                'type'            => 'arrangement_proposal',
                'metadata'        => $details,
            ]);
        });

        return redirect()->route('messages.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Arrangement proposal submitted! Either party can now confirm.');
    }

    /**
     * Explicit Confirm Arrangement action.
     * Prevents overlapping conflicts, creates/updates booking, sets status to 'confirmed'.
     */
    public function confirmArrangement(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            abort(403);
        }

        if ($conversation->arrangement_status === 'confirmed') {
            return redirect()->back()->with('error', 'This arrangement has already been confirmed.');
        }

        $details = $conversation->arrangement_details ?? [];
        $vehicle = null;

        // Determine vehicle if associated
        if ($conversation->subject_type === 'vehicle' && $conversation->subject_id) {
            $vehicle = Vehicle::find($conversation->subject_id);
        }

        $route = $details['route'] ?? 'Private Travel Arrangement';
        $dateStr = $details['date'] ?? Carbon::today()->format('Y-m-d');
        $timeStr = $details['time'] ?? '09:00';
        $fare = (float) ($details['fare'] ?? 1000);
        $seats = (int) ($details['seats'] ?? 1);

        try {
            DB::transaction(function () use ($conversation, $user, $vehicle, $route, $dateStr, $timeStr, $fare, $seats, $details) {
                // If a vehicle is involved, prevent conflicting bookings
                $booking = null;
                if ($vehicle) {
                    $start = Carbon::parse("{$dateStr} {$timeStr}");
                    $end   = (clone $start)->addDay();

                    $conflict = $vehicle->getConflictReason($start, $end);
                    if ($conflict) {
                        throw new \DomainException($conflict);
                    }

                    // Create confirmed booking reference
                    do {
                        $reference = 'RC-ARR-' . strtoupper(Str::random(4)) . '-' . mt_rand(1000, 9999);
                    } while (Booking::where('booking_reference', $reference)->exists());

                    $passenger = $conversation->users->firstWhere('user_type', 0) ?? $user;

                    $booking = Booking::create([
                        'user_id'              => $passenger->id,
                        'vehicle_id'           => $vehicle->id,
                        'trip_id'              => null,
                        'booking_type'         => 'private',
                        'pickup_location'      => explode('→', $route)[0] ?? $route,
                        'destination_location' => explode('→', $route)[1] ?? $route,
                        'start_datetime'       => $start,
                        'end_datetime'         => $end,
                        'days'                 => 1,
                        'daily_rate'           => $fare,
                        'total_fare'           => $fare,
                        'seats'                => $seats,
                        'booking_reference'    => $reference,
                        'passenger_notes'      => 'Confirmed via RouteConnect Chat Arrangement',
                        'status'               => 'confirmed',
                        'booking_status'       => 'confirmed',
                    ]);
                }

                // If travel request, update status
                if ($conversation->subject_type === 'travel_request' && $conversation->subject_id) {
                    $tr = TravelRequest::find($conversation->subject_id);
                    if ($tr) {
                        $tr->update(['status' => 'confirmed']);
                    }
                }

                $conversation->update([
                    'arrangement_status'   => 'confirmed',
                    'confirmed_booking_id' => $booking?->id,
                ]);

                // Post confirmed system message
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id'       => $user->id,
                    'body'            => "🎉 Arrangement Confirmed! Both parties have agreed to the trip ({$route}) for Rs. " . number_format($fare, 2) . ". Status is now locked and confirmed.",
                    'type'            => 'arrangement_confirmed',
                    'metadata'        => [
                        'confirmed_by'      => $user->name,
                        'booking_reference' => $booking?->booking_reference ?? 'DIRECT-PASS',
                        'fare'              => $fare,
                    ],
                ]);

                // Notify all participants
                foreach ($conversation->participants as $p) {
                    Notification::create([
                        'user_id' => $p->user_id,
                        'message' => "Arrangement confirmed for '{$conversation->title}' by {$user->name}. Check My Activity for details.",
                        'is_read' => false,
                    ]);
                }
            });
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', 'Cannot confirm arrangement: ' . $e->getMessage());
        }

        return redirect()->route('messages.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Trip arrangement has been confirmed successfully! It is now recorded in My Activity.');
    }

    /**
     * Cancel an arrangement.
     */
    public function cancelArrangement(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            abort(403);
        }

        DB::transaction(function () use ($conversation, $user) {
            if ($conversation->confirmed_booking_id) {
                $booking = Booking::find($conversation->confirmed_booking_id);
                if ($booking) {
                    $booking->update([
                        'status'         => 'cancelled',
                        'booking_status' => 'cancelled',
                    ]);
                }
            }

            if ($conversation->subject_type === 'travel_request' && $conversation->subject_id) {
                $tr = TravelRequest::find($conversation->subject_id);
                if ($tr && $tr->status === 'confirmed') {
                    $tr->update(['status' => 'open']);
                }
            }

            $conversation->update(['arrangement_status' => 'cancelled']);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'body'            => "Arrangement was cancelled by {$user->name}.",
                'type'            => 'system',
            ]);
        });

        return redirect()->route('messages.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Arrangement has been marked as cancelled.');
    }

    /**
     * Block / unblock participant in conversation.
     */
    public function toggleBlock(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user)) {
            abort(403);
        }

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $otherParticipant = $conversation->participants()->where('user_id', '!=', $user->id)->first();

        if ($participant->is_blocked || ($otherParticipant && $otherParticipant->is_blocked)) {
            // Unblock
            $conversation->participants()->update([
                'is_blocked' => false,
                'blocked_by' => null,
            ]);
            $msg = 'User has been unblocked.';
        } else {
            // Block
            if ($otherParticipant) {
                $otherParticipant->update([
                    'is_blocked' => true,
                    'blocked_by' => $user->id,
                ]);
            }
            $participant->update([
                'is_blocked' => true,
                'blocked_by' => $user->id,
            ]);
            $msg = 'User has been blocked. They can no longer send messages in this chat.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Report conversation or user.
     */
    public function report(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user)) {
            abort(403);
        }

        $validated = $request->validate([
            'reason'  => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $otherUser = $conversation->otherUser($user);

        ConversationReport::create([
            'conversation_id'  => $conversation->id,
            'reported_by'      => $user->id,
            'reported_user_id' => $otherUser ? $otherUser->id : $user->id,
            'reason'           => trim($validated['reason']),
            'details'          => $validated['details'] ? trim($validated['details']) : null,
            'status'           => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your report has been submitted to platform moderators for review.');
    }
}

