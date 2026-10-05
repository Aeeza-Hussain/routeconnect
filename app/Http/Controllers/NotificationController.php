<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display the notification center for the authenticated user.
     */
    public function index(Request $request)
    {
        $status = $request->query('filter', 'all');

        $query = auth()->user()->notifications()->with('trip.route')->latest();

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->paginate(15);

        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
        $totalCount  = auth()->user()->notifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'status'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Unauthorized to modify this notification.');
        }

        $notification->update([
            'is_read' => true,
        ]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
