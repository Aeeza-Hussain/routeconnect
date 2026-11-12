<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminContactMessageController extends Controller
{
    /**
     * Display a paginated listing of contact messages with search & filtering.
     *
     * GET /admin/contact-messages
     */
    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $status = $request->input('status', 'all'); // 'all', 'unread', 'read'

        $query = ContactMessage::with('user');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        $unreadCount = ContactMessage::where('is_read', false)->count();
        $readCount   = ContactMessage::where('is_read', true)->count();
        $totalCount  = ContactMessage::count();

        return view('backend.admin.contact_messages.index', compact(
            'messages',
            'search',
            'status',
            'unreadCount',
            'readCount',
            'totalCount'
        ));
    }

    /**
     * Display the specified contact message details and mark as read.
     *
     * GET /admin/contact-messages/{id}
     */
    public function show(int $id): View
    {
        $message = ContactMessage::with('user')->findOrFail($id);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('backend.admin.contact_messages.show', compact('message'));
    }

    /**
     * Toggle read/unread status of a contact message.
     *
     * PATCH /admin/contact-messages/{id}/toggle-read
     */
    public function toggleRead(int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => !$message->is_read]);

        $statusText = $message->is_read ? 'marked as read' : 'marked as unread';

        return back()->with('success', "Message from {$message->name} has been {$statusText}.");
    }

    /**
     * Delete a contact message from the database.
     *
     * DELETE /admin/contact-messages/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);
        $name    = $message->name;
        $message->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('success', "Contact message from {$name} has been permanently deleted.");
    }
}

