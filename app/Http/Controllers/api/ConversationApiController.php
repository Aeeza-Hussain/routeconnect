<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TravelRequest;
use Illuminate\Http\Request;

class ConversationApiController extends Controller
{
    /**
     * Return list of conversations for authenticated user (for Flutter app).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::with([
            'participants.user',
            'latestMessage.sender',
            'confirmedBooking.vehicle',
        ])
        ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
        ->latest('updated_at')
        ->get();

        return response()->json([
            'success'       => true,
            'unread_total'  => $user->unreadMessagesCount(),
            'conversations' => $conversations,
        ]);
    }

    /**
     * Return messages for a specific conversation.
     */
    public function messages(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user)) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $sinceId = $request->query('since_id');
        $query = $conversation->messages()->with('sender');
        if ($sinceId) {
            $query->where('id', '>', (int) $sinceId);
        }

        return response()->json([
            'success'             => true,
            'conversation'        => $conversation,
            'arrangement_status'  => $conversation->arrangement_status,
            'arrangement_details' => $conversation->arrangement_details,
            'is_blocked'          => $conversation->isBlocked(),
            'messages'            => $query->get(),
        ]);
    }

    /**
     * Send message from Flutter app.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if (!$conversation->isParticipant($user)) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        if ($conversation->isBlocked()) {
            return response()->json(['error' => 'Conversation is blocked.'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:3000',
        ]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'body'            => trim($request->body),
            'type'            => 'text',
        ]);

        $conversation->touch();

        return response()->json([
            'success' => true,
            'message' => $msg->load('sender'),
        ], 201);
    }
}

