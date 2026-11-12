<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the RouteConnect Contact Us page.
     */
    public function index(): View
    {
        $user = auth()->user();

        return view('frontend.contact', [
            'prefillName'  => $user?->name ?? '',
            'prefillEmail' => $user?->email ?? '',
        ]);
    }

    /**
     * Store a submitted contact message.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
        ], [
            'name.required'    => 'Please enter your full name.',
            'email.required'   => 'Please provide a valid email address so we can reply.',
            'email.email'      => 'Please enter a valid email format.',
            'subject.required' => 'Please provide a subject for your inquiry.',
            'message.required' => 'Please enter your message.',
            'message.min'      => 'Your message should be at least 5 characters long.',
            'message.max'      => 'Your message may not exceed 3,000 characters.',
        ]);

        ContactMessage::create([
            'user_id' => auth()->id(),
            'name'    => trim($validated['name']),
            'email'   => strtolower(trim($validated['email'])),
            'subject' => trim($validated['subject']),
            'message' => trim($validated['message']),
            'is_read' => false,
        ]);

        return redirect()
            ->route('contact')
            ->with('success', 'Thank you for reaching out to RouteConnect! Your message has been received and our team will get back to you shortly.');
    }
}

