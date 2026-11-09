<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ConversationReport;
use App\Models\DriverApplication;
use App\Models\Message;
use App\Models\Notification;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConversationApiController extends Controller
{
    /**
     * Return list of conversations for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = Conversation::with([
            'participants.user',
            'latestMessage.sender',
            'confirmedBooking.vehicle',
        ])
        ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
        ->latest('updated_at')
        ->paginate($request->input('per_page', 20));

        return response()->json([
            'success'      => true,
            'message'      => 'Conversations retrieved successfully.',
            'unread_total' => $user->unreadMessagesCount(),
            'data'         => $conversations,
        ]);
    }

    /**
     * Get specific conversation details.
     */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. You are not a participant in this conversation.',
            ], 403);
        }

        // Mark as read for this user
        $participant = $conversation->participants()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation loaded successfully.',
            'data'    => $conversation->load(['participants.user', 'confirmedBooking.vehicle', 'latestMessage.sender']),
        ]);
    }

    /**
     * Return messages for a specific conversation.
     */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        // Mark participant last_read_at & unread messages as read
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $query = $conversation->messages()->with('sender');

        if ($request->filled('since_id')) {
            $query->where('id', '>', (int) $request->since_id);
        }

        $messages = $query->orderBy('created_at', 'asc')->get()->map(function ($msg) use ($conversation) {
            $arr = $msg->toArray();
            $arr['attachment_url'] = $msg->hasAttachment() ? url("/api/conversations/{$conversation->id}/messages/{$msg->id}/attachment") : null;
            $arr['attachment_name'] = $msg->attachment_name;
            $arr['attachment_size'] = $msg->formattedAttachmentSize();
            $arr['is_image_attachment'] = $msg->isImageAttachment();
            return $arr;
        });

        return response()->json([
            'success'             => true,
            'message'             => 'Messages retrieved successfully.',
            'conversation'        => $conversation->load('participants.user'),
            'arrangement_status'  => $conversation->arrangement_status,
            'arrangement_details' => $conversation->arrangement_details,
            'is_blocked'          => $conversation->isBlocked(),
            'data'                => $messages,
        ]);
    }

    /**
     * Start or locate a conversation from:
     * - vehicle_id
     * - travel_request_id (and optional target_driver_id)
     * - driver_application_id
     * - trip_id
     * - booking_id
     * - user_id / driver_id
     */
    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Vehicle inquiry
        if ($request->filled('vehicle_id')) {
            $vehicle = Vehicle::with('driver')->findOrFail($request->vehicle_id);

            if ($vehicle->user_id === $user->id) {
                return response()->json(['success' => false, 'message' => 'You cannot message your own vehicle.'], 422);
            }

            $driver = $vehicle->driver;
            if (!$driver) {
                return response()->json(['success' => false, 'message' => 'Vehicle has no assigned driver.'], 404);
            }

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
                            'vehicle_id' => $vehicle->id,
                            'model'      => $vehicle->model,
                            'seats'      => $vehicle->total_seats,
                            'daily_rate' => (float) $vehicle->rental_rate_per_day,
                        ],
                        'created_by'          => $user->id,
                    ]);

                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $user->id, 'last_read_at' => now()]);
                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $driver->id, 'last_read_at' => null]);

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

            return response()->json([
                'success' => true,
                'message' => 'Conversation retrieved or created.',
                'data'    => $conversation->load(['participants.user', 'latestMessage.sender']),
            ]);
        }

        // 2. Travel Request inquiry
        if ($request->filled('travel_request_id')) {
            $travelRequest = TravelRequest::with('user')->findOrFail($request->travel_request_id);

            $targetUserId = ($travelRequest->user_id === $user->id)
                ? (int) $request->input('target_driver_id')
                : $travelRequest->user_id;

            if (!$targetUserId || $targetUserId === $user->id) {
                return response()->json(['success' => false, 'message' => 'Invalid conversation target.'], 422);
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
                        'travel_request_id'   => $travelRequest->id,
                        'status'              => 'active',
                        'arrangement_status'  => 'negotiating',
                        'arrangement_details' => [
                            'route' => "{$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                            'date'  => $travelRequest->travel_date ? $travelRequest->travel_date->format('Y-m-d') : null,
                            'seats' => $travelRequest->seats_needed,
                            'fare'  => (float) ($travelRequest->budget ?? 0),
                        ],
                        'created_by'          => $user->id,
                    ]);

                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $user->id, 'last_read_at' => now()]);
                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $targetUserId, 'last_read_at' => null]);

                    Message::create([
                        'conversation_id' => $conv->id,
                        'sender_id'       => $user->id,
                        'body'            => "Hello! Re: Travel Request from {$travelRequest->pickup_location} to {$travelRequest->destination_location}.",
                        'type'            => 'text',
                    ]);

                    return $conv;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation retrieved or created.',
                'data'    => $conversation->load(['participants.user', 'latestMessage.sender']),
            ]);
        }

        // 3. Driver Application inquiry
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
                        'title'                 => "Offer: {$application->travelRequest?->pickup_location} → {$application->travelRequest?->destination_location}",
                        'subject_type'          => 'driver_application',
                        'subject_id'            => $application->id,
                        'travel_request_id'     => $application->travel_request_id,
                        'driver_application_id' => $application->id,
                        'status'                => 'active',
                        'arrangement_status'    => 'negotiating',
                        'arrangement_details'   => [
                            'fare' => (float) $application->offered_fare,
                            'time' => $application->proposed_time,
                        ],
                        'created_by'            => $user->id,
                    ]);

                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $user->id, 'last_read_at' => now()]);
                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $targetUserId, 'last_read_at' => null]);

                    return $conv;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation retrieved or created.',
                'data'    => $conversation->load(['participants.user', 'latestMessage.sender']),
            ]);
        }

        // 4. Trip inquiry
        if ($request->filled('trip_id')) {
            $trip = Trip::with(['driver', 'route'])->findOrFail($request->trip_id);

            if ($trip->user_id === $user->id) {
                return response()->json(['success' => false, 'message' => 'You cannot message your own trip.'], 422);
            }

            $driver = $trip->driver;
            if (!$driver) {
                return response()->json(['success' => false, 'message' => 'Trip has no assigned driver.'], 404);
            }

            $conversation = Conversation::where('subject_type', 'trip')
                ->where('subject_id', $trip->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $driver->id))
                ->first();

            if (!$conversation) {
                $conversation = DB::transaction(function () use ($user, $driver, $trip) {
                    $conv = Conversation::create([
                        'title'              => "Trip: " . ($trip->route?->name ?? "Scheduled Ride #{$trip->id}"),
                        'subject_type'       => 'trip',
                        'subject_id'         => $trip->id,
                        'trip_id'            => $trip->id,
                        'status'             => 'active',
                        'arrangement_status' => 'negotiating',
                        'created_by'         => $user->id,
                    ]);

                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $user->id, 'last_read_at' => now()]);
                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $driver->id, 'last_read_at' => null]);

                    Message::create([
                        'conversation_id' => $conv->id,
                        'sender_id'       => $user->id,
                        'body'            => "Hello {$driver->name}! I have a question regarding your scheduled trip on " . ($trip->trip_date ? $trip->trip_date->format('d M Y') : 'upcoming date') . ".",
                        'type'            => 'text',
                    ]);

                    return $conv;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation retrieved or created.',
                'data'    => $conversation->load(['participants.user', 'latestMessage.sender']),
            ]);
        }

        // 5. Booking inquiry
        if ($request->filled('booking_id')) {
            $booking = Booking::with(['user', 'vehicle.driver', 'trip.driver'])->findOrFail($request->booking_id);

            $driver = $booking->vehicle?->driver ?? $booking->trip?->driver;
            $passenger = $booking->user;

            $isPassenger = ($booking->user_id === $user->id);
            $isDriver = ($driver && $driver->id === $user->id);

            if (!$isPassenger && !$isDriver && !$user->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to this booking conversation.'], 403);
            }

            $otherUserId = $isPassenger ? ($driver?->id) : $passenger?->id;
            if (!$otherUserId) {
                return response()->json(['success' => false, 'message' => 'Unable to find recipient for this booking.'], 404);
            }

            $conversation = Conversation::where('subject_type', 'booking')
                ->where('subject_id', $booking->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $otherUserId))
                ->first();

            if (!$conversation) {
                $conversation = DB::transaction(function () use ($user, $otherUserId, $booking) {
                    $conv = Conversation::create([
                        'title'               => "Booking #{$booking->booking_reference}",
                        'subject_type'        => 'booking',
                        'subject_id'          => $booking->id,
                        'booking_id'          => $booking->id,
                        'status'              => 'active',
                        'arrangement_status'  => 'confirmed',
                        'arrangement_details' => [
                            'reference' => $booking->booking_reference,
                            'route'     => ($booking->pickup_location ?? 'Pickup') . ' → ' . ($booking->destination_location ?? 'Destination'),
                            'fare'      => (float) $booking->total_fare,
                            'seats'     => (int) $booking->seats,
                        ],
                        'created_by'          => $user->id,
                    ]);

                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $user->id, 'last_read_at' => now()]);
                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $otherUserId, 'last_read_at' => null]);

                    return $conv;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation retrieved or created.',
                'data'    => $conversation->load(['participants.user', 'latestMessage.sender']),
            ]);
        }

        // 6. Direct User / Driver ID
        if ($request->filled('user_id') || $request->filled('driver_id')) {
            $targetId = (int) ($request->user_id ?: $request->driver_id);
            $targetUser = User::findOrFail($targetId);

            if ($targetUser->id === $user->id) {
                return response()->json(['success' => false, 'message' => 'You cannot message yourself.'], 422);
            }

            $conversation = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $targetUser->id))
                ->latest()
                ->first();

            if (!$conversation) {
                $conversation = DB::transaction(function () use ($user, $targetUser) {
                    $conv = Conversation::create([
                        'title'              => "Chat with {$targetUser->name}",
                        'subject_type'       => 'direct',
                        'subject_id'         => $targetUser->id,
                        'status'             => 'active',
                        'arrangement_status' => 'negotiating',
                        'created_by'         => $user->id,
                    ]);

                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $user->id, 'last_read_at' => now()]);
                    ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $targetUser->id, 'last_read_at' => null]);

                    Message::create([
                        'conversation_id' => $conv->id,
                        'sender_id'       => $user->id,
                        'body'            => "Hello {$targetUser->name}! I'd like to discuss travel arrangements with you.",
                        'type'            => 'text',
                    ]);

                    return $conv;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation retrieved or created.',
                'data'    => $conversation->load(['participants.user', 'latestMessage.sender']),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid parameters to start conversation.'], 422);
    }

    /**
     * Send message from Flutter app or web client.
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        if ($conversation->isBlocked()) {
            return response()->json(['success' => false, 'message' => 'This conversation is blocked.'], 403);
        }

        $request->validate([
            'body'       => ['nullable', 'string', 'max:3000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx,txt'],
        ]);

        $attachmentData = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $safeName = Str::slug(substr($originalName, 0, 50)) . '.' . $extension;
            if (empty($safeName) || $safeName === '.') {
                $safeName = 'attachment_' . time() . '.' . $extension;
            }

            $path = $file->store('chat_attachments', 'local');

            $attachmentData = [
                'attachment_path' => $path,
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_size' => $file->getSize(),
                'attachment_type' => $file->getClientMimeType(),
            ];
        }

        $msg = Message::create(array_merge([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'body'            => $request->filled('body') ? trim($request->body) : '',
            'type'            => $attachmentData ? 'attachment' : 'text',
            'is_read'         => false,
        ], $attachmentData ?? []));

        $conversation->touch();

        // Notify other participants
        $otherParticipants = $conversation->participants()->where('user_id', '!=', $user->id)->get();
        foreach ($otherParticipants as $p) {
            Notification::create([
                'user_id' => $p->user_id,
                'message' => "New message from {$user->name}: " . Str::limit($msg->body ?: 'Sent an attachment', 50),
                'is_read' => false,
            ]);
        }

        $responseMsg = $msg->load('sender')->toArray();
        $responseMsg['attachment_url'] = $msg->hasAttachment() ? url("/api/conversations/{$conversation->id}/messages/{$msg->id}/attachment") : null;
        $responseMsg['attachment_name'] = $msg->attachment_name;
        $responseMsg['attachment_size'] = $msg->formattedAttachmentSize();
        $responseMsg['is_image_attachment'] = $msg->isImageAttachment();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data'    => $responseMsg,
        ], 201);
    }

    /**
     * Download or stream attachment.
     */
    public function downloadAttachment(Request $request, Conversation $conversation, Message $message)
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        if ($message->conversation_id !== $conversation->id) {
            return response()->json(['success' => false, 'message' => 'Message does not belong to this conversation.'], 404);
        }

        if (!$message->hasAttachment() || !Storage::disk('local')->exists($message->attachment_path)) {
            return response()->json(['success' => false, 'message' => 'Attachment file not found.'], 404);
        }

        $fullPath = Storage::disk('local')->path($message->attachment_path);
        $mime = $message->attachment_type ?: mime_content_type($fullPath) ?: 'application/octet-stream';
        $fileName = $message->attachment_name ?: basename($message->attachment_path);

        if ($request->boolean('preview') || ($message->isImageAttachment() && !$request->boolean('download'))) {
            return response()->file($fullPath, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($fileName) . '"',
            ]);
        }

        return Storage::disk('local')->download($message->attachment_path, $fileName);
    }

    /**
     * Propose an arrangement.
     */
    public function proposeArrangement(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
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

        return response()->json([
            'success' => true,
            'message' => 'Arrangement proposal submitted! Either party can now confirm.',
            'data'    => $conversation->fresh()->load('latestMessage'),
        ]);
    }

    /**
     * Confirm arrangement.
     */
    public function confirmArrangement(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($conversation->arrangement_status === 'confirmed') {
            return response()->json(['success' => false, 'message' => 'This arrangement has already been confirmed.'], 422);
        }

        $details = $conversation->arrangement_details ?? [];
        $vehicle = null;
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
                $booking = null;
                if ($vehicle) {
                    $start = Carbon::parse("{$dateStr} {$timeStr}");
                    $end   = (clone $start)->addDay();

                    $conflict = Booking::where('vehicle_id', $vehicle->id)
                        ->where('booking_type', 'private')
                        ->whereIn('booking_status', ['confirmed', 'paid', 'approved'])
                        ->where(function ($q) use ($start, $end) {
                            $q->whereBetween('booking_date', [$start, $end]);
                        })
                        ->exists();

                    if ($conflict) {
                        throw new \DomainException('The vehicle is already booked for the selected date and time.');
                    }

                    $booking = Booking::create([
                        'user_id'              => $user->id,
                        'vehicle_id'           => $vehicle->id,
                        'booking_reference'    => 'ARR-' . strtoupper(Str::random(8)),
                        'booking_type'         => 'private',
                        'pickup_location'      => explode('→', $route)[0] ?? 'Custom Route',
                        'destination_location' => explode('→', $route)[1] ?? 'Destination',
                        'booking_date'         => $start,
                        'seats'                => $seats,
                        'total_fare'           => $fare,
                        'status'               => 'confirmed',
                        'booking_status'       => 'confirmed',
                        'is_entire_vehicle'    => true,
                    ]);
                }

                $conversation->update([
                    'arrangement_status'   => 'confirmed',
                    'confirmed_booking_id' => $booking?->id,
                ]);

                if ($conversation->subject_type === 'travel_request' && $conversation->subject_id) {
                    $tr = TravelRequest::find($conversation->subject_id);
                    if ($tr && $tr->status === 'open') {
                        $tr->update(['status' => 'confirmed']);
                    }
                }

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

                foreach ($conversation->participants as $p) {
                    Notification::create([
                        'user_id' => $p->user_id,
                        'message' => "Arrangement confirmed for '{$conversation->title}' by {$user->name}. Check My Activity for details.",
                        'is_read' => false,
                    ]);
                }
            });
        } catch (\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Trip arrangement confirmed successfully! It is now recorded in My Activity.',
            'data'    => $conversation->fresh()->load(['confirmedBooking', 'latestMessage']),
        ]);
    }

    /**
     * Mark an arrangement as completed.
     */
    public function completeArrangement(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($conversation->arrangement_status !== 'confirmed') {
            return response()->json(['success' => false, 'message' => 'Only confirmed arrangements can be marked as completed.'], 422);
        }

        DB::transaction(function () use ($conversation, $user) {
            $conversation->update(['arrangement_status' => 'completed']);

            if ($conversation->confirmed_booking_id) {
                $booking = Booking::find($conversation->confirmed_booking_id);
                if ($booking) {
                    $booking->update([
                        'status'         => 'completed',
                        'booking_status' => 'completed',
                    ]);
                }
            }

            if ($conversation->subject_type === 'travel_request' && $conversation->subject_id) {
                $tr = TravelRequest::find($conversation->subject_id);
                if ($tr && $tr->status === 'confirmed') {
                    $tr->update(['status' => 'completed']);
                }
            }

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'body'            => "🏁 Trip arrangement has been successfully completed! Thank you for traveling with RouteConnect.",
                'type'            => 'system',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Arrangement marked as completed.',
            'data'    => $conversation->fresh(),
        ]);
    }

    /**
     * Cancel an arrangement.
     */
    public function cancelArrangement(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user) && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
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

        return response()->json([
            'success' => true,
            'message' => 'Arrangement has been cancelled.',
            'data'    => $conversation->fresh(),
        ]);
    }

    /**
     * Block / unblock participant.
     */
    public function toggleBlock(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $otherParticipant = $conversation->participants()->where('user_id', '!=', $user->id)->first();

        $isCurrentlyBlocked = $participant->is_blocked || ($otherParticipant && $otherParticipant->is_blocked);

        if ($isCurrentlyBlocked) {
            $conversation->participants()->update([
                'is_blocked' => false,
                'blocked_by' => null,
            ]);
            $msg = 'User has been unblocked.';
            $blocked = false;
        } else {
            if ($otherParticipant) {
                $otherParticipant->update(['is_blocked' => true, 'blocked_by' => $user->id]);
            }
            $participant->update(['is_blocked' => true, 'blocked_by' => $user->id]);
            $msg = 'User has been blocked. They can no longer send messages in this chat.';
            $blocked = true;
        }

        return response()->json([
            'success'    => true,
            'message'    => $msg,
            'is_blocked' => $blocked,
        ]);
    }

    /**
     * Report user / conversation.
     */
    public function report(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'reason'  => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $otherUser = $conversation->otherUser($user);

        $report = ConversationReport::create([
            'conversation_id'  => $conversation->id,
            'reported_by'      => $user->id,
            'reported_user_id' => $otherUser ? $otherUser->id : $user->id,
            'reason'           => trim($validated['reason']),
            'details'          => !empty($validated['details']) ? trim($validated['details']) : null,
            'status'           => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your report has been submitted to platform moderators for review.',
            'data'    => $report,
        ]);
    }

    /**
     * Return user's unread message count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'unread_count' => $request->user()->unreadMessagesCount(),
            ],
        ]);
    }
}
