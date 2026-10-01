<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show Login Form
     */
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    /**
     * Process User Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Role-based Redirect Logic
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isDriver()) {
                if ($user->isApprovedDriver()) {
                    return redirect()->route('driver.dashboard');
                }
                return redirect()->route('driver.pending');
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show Unified Registration Form
     */
    public function showRegisterForm(Request $request)
    {
        $selectedRole = $request->get('role', 'passenger');
        return view('frontend.auth.register', compact('selectedRole'));
    }

    /**
     * Process Unified Registration (Passenger OR Driver)
     */
    public function register(Request $request)
    {
        $request->merge(['role' => $request->input('role', 'passenger')]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:passenger,driver'],
        ]);

        $isDriver = $request->role === 'driver';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $isDriver ? 'driver' : 'passenger',
            'driver_status' => $isDriver ? 'pending' : null,
        ]);

        Auth::login($user);

        if ($isDriver) {
            return redirect()->route('driver.pending')->with('success', 'Driver application submitted! Pending admin approval.');
        }

        return redirect()->route('home')->with('success', 'Registration successful!');
    }

    /**
     * Show Driver Registration Alias (Redirects to unified register with role=driver)
     */
    public function showDriverRegisterForm()
    {
        return redirect()->route('register', ['role' => 'driver']);
    }

    /**
     * Process Driver Registration Alias
     */
    public function registerDriver(Request $request)
    {
        $request->merge(['role' => 'driver']);
        return $this->register($request);
    }

    /**
     * Logout User
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
