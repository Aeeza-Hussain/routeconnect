<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverProfileController extends Controller
{
    /**
     * Driver Profile Page
     */
    public function profile()
    {
        $driver = auth()->user();
        return view('backend.driver.profile', compact('driver'));
    }

    /**
     * Update Driver Profile Information
     */
    public function updateProfile(Request $request)
    {
        $driver = auth()->user();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'cnic'       => 'nullable|string|max:30',
            'license_no' => 'nullable|string|max:50',
            'bio'        => 'nullable|string|max:1000',
        ]);

        $driver->update($validated);

        return redirect()->back()->with('success', 'Profile information updated successfully.');
    }

    /**
     * Driver Settings Page
     */
    public function settings()
    {
        $driver = auth()->user();
        return view('backend.driver.settings', compact('driver'));
    }

    /**
     * Update Driver Account Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $driver = auth()->user();
        $driver->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}
