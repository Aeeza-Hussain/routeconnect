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
     *
     * Redirects based on user_type:
     *   user_type = 1  → /admin/dashboard
     *   user_type = 2  → /driver/dashboard (if approved) or /driver/pending
     *   user_type = 0  → public homepage
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Admin (user_type = 1)
            if ($user->user_type == 1) {
                return redirect()->route('admin.dashboard');
            }

            // Driver (user_type = 2)
            if ($user->user_type == 2) {
                if ($user->driver_status === 'approved') {
                    return redirect()->route('driver.dashboard');
                }
                return redirect()->route('driver.pending');
            }

            // Passenger (user_type = 0)
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
     *
     * Passenger → user_type = 0, driver_status = NULL
     * Driver    → user_type = 2, driver_status = pending
     */
    public function register(Request $request)
    {
        // Merge default role if not provided
        $request->merge(['role' => $request->input('role', 'passenger')]);

        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'             => ['required', 'string', 'max:20'],
            'gender'            => ['nullable', 'string', 'in:male,female,other'],
            'dob'               => ['nullable', 'date'],
            'cnic'              => ['nullable', 'string', 'max:30'],
            'bio'               => ['nullable', 'string', 'max:1000'],
            'image'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'profile_photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'password'          => ['required', 'string', 'min:6', 'confirmed'],
            'role'              => ['required', 'string', 'in:passenger,driver'],
        ]);

        $isDriver = $request->role === 'driver';

        // Handle profile photo upload
        $photoPath = null;
        $imageFile = $request->file('image') ?? $request->file('profile_photo');
        if ($imageFile) {
            $filename        = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/profiles');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $imageFile->move($destinationPath, $filename);
            $photoPath = 'uploads/profiles/' . $filename;
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'dob'           => $request->dob,
            'cnic'          => $request->cnic,
            'bio'           => $request->bio ?? $request->about,
            'profile_photo' => $photoPath,
            'password'      => Hash::make($request->password),
            'user_type'     => $isDriver ? 2 : 0,   // 0 = passenger, 2 = driver
            'driver_status' => $isDriver ? 'pending' : null,
        ]);

        Auth::login($user);

        if ($isDriver) {
            return redirect()->route('driver.pending')
                ->with('success', 'Driver application submitted! Pending admin approval.');
        }

        return redirect()->route('home')->with('success', 'Registration successful! Welcome to RouteConnect.');
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
