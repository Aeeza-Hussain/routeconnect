<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show Passenger/User profile.
     */
    public function show()
    {
        $user = auth()->user();

        if ($user->isDriver()) {
            return redirect()->route('driver.profile');
        }

        return view('frontend.profile.show', compact('user'));
    }

    /**
     * Show Passenger profile edit form.
     */
    public function edit()
    {
        $user = auth()->user();

        if ($user->isDriver()) {
            return redirect()->route('driver.profile');
        }

        return view('frontend.profile.edit', compact('user'));
    }

    /**
     * Update Passenger profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'phone'                   => ['nullable', 'string', 'max:30'],
            'bio'                     => ['nullable', 'string', 'max:500'],
            'gender'                  => ['nullable', 'string', 'in:male,female,other'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'profile_photo'           => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $validated['profile_photo'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Settings page (Theme, security, password).
     */
    public function settings()
    {
        $user = auth()->user();

        if ($user->isDriver()) {
            return redirect()->route('driver.settings');
        }

        return view('frontend.profile.settings', compact('user'));
    }

    /**
     * Update account password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Account password updated successfully.');
    }
}
