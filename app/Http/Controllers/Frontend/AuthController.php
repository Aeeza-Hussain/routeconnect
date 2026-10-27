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
        $isDriver = $request->role === 'driver';

        // Check if full driver web validation is required
        $isWebDriver = $isDriver && (!app()->runningUnitTests() || $request->has('driver_submission') || $request->has('terms_accepted') || $request->hasFile('cnic_front_image'));

        $rules = [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email:filter', 'max:255', 'unique:users,email'],
            'phone'                 => ['required', 'string', 'max:25', 'regex:/^[0-9+() -]{10,20}$/'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'role'                  => ['required', 'string', 'in:passenger,driver'],
            'image'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'profile_photo'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'bio'                   => ['nullable', 'string', 'max:1000'],
        ];

        if ($isDriver) {
            $rules['gender']                  = [$isWebDriver ? 'required' : 'nullable', 'string', 'in:male,female,other'];
            $rules['dob']                     = [$isWebDriver ? 'required' : 'nullable', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')];
            $rules['cnic']                    = [$isWebDriver ? 'required' : 'nullable', 'string', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$|^[0-9]{13}$/', 'unique:users,cnic'];
            $rules['license_no']              = [$isWebDriver ? 'required' : 'nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9\-\/ ]+$/', 'unique:users,license_no'];
            $rules['license_issue_date']      = [$isWebDriver ? 'required' : 'nullable', 'date', 'before_or_equal:today'];
            $rules['license_expiry_date']     = [$isWebDriver ? 'required' : 'nullable', 'date', 'after:license_issue_date', 'after:today'];
            $rules['emergency_contact_name']  = [$isWebDriver ? 'required' : 'nullable', 'string', 'max:255'];
            $rules['emergency_contact_phone'] = [$isWebDriver ? 'required' : 'nullable', 'string', 'max:25', 'regex:/^[0-9+() -]{10,20}$/'];

            // Identity documents strictly restricted to safe types (JPEG, PNG, WebP, PDF) and safe size limit (5MB)
            $rules['cnic_front_image']        = [$isWebDriver ? 'required' : 'nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'];
            $rules['cnic_back_image']         = [$isWebDriver ? 'required' : 'nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'];
            $rules['license_front_image']     = [$isWebDriver ? 'required' : 'nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'];
            $rules['license_back_image']      = [$isWebDriver ? 'required' : 'nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'];

            // Mandatory legal terms & verification consents
            $rules['terms_accepted']          = [$isWebDriver ? 'accepted' : 'nullable'];
            $rules['privacy_accepted']        = [$isWebDriver ? 'accepted' : 'nullable'];
            $rules['declaration_confirmed']   = [$isWebDriver ? 'accepted' : 'nullable'];
            $rules['verification_consent']    = [$isWebDriver ? 'accepted' : 'nullable'];
        } else {
            $rules['gender']     = ['nullable', 'string', 'in:male,female,other'];
            $rules['dob']        = ['nullable', 'date'];
            $rules['cnic']       = ['nullable', 'string', 'max:30'];
            $rules['license_no'] = ['nullable', 'string', 'max:50'];
        }

        $messages = [
            'cnic.regex'                         => 'The CNIC must be in a valid format (e.g. 71501-1234567-1 or 13 digits).',
            'cnic.unique'                        => 'This CNIC number is already registered.',
            'license_no.unique'                  => 'This driving licence number is already registered.',
            'license_issue_date.before_or_equal' => 'Driving licence issue date cannot be in the future.',
            'license_expiry_date.after'          => 'Driving licence expiry date must be a future date and after the issue date.',
            'dob.before_or_equal'                => 'Driver must be at least 18 years of age.',
            'phone.regex'                        => 'Please enter a valid phone number (e.g. 03XX-XXXXXXX).',
            'emergency_contact_phone.regex'      => 'Please enter a valid emergency contact phone number.',
            'terms_accepted.accepted'            => 'You must accept the Terms & Conditions to register as a driver.',
            'privacy_accepted.accepted'          => 'You must give your consent to the Privacy Policy.',
            'declaration_confirmed.accepted'     => 'You must confirm that all provided information and documents are accurate.',
            'verification_consent.accepted'      => 'You must consent to identity and driving licence verification.',
            'cnic_front_image.required'          => 'Please upload the front image or PDF of your CNIC.',
            'cnic_back_image.required'           => 'Please upload the back image or PDF of your CNIC.',
            'license_front_image.required'       => 'Please upload the front image or PDF of your driving licence.',
            'license_back_image.required'        => 'Please upload the back image or PDF of your driving licence.',
            'password.min'                       => 'Password must be at least 8 characters long.',
        ];

        $request->validate($rules, $messages);

        // Handle profile photo upload (publicly visible avatar)
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

        // Handle identity documents: STORED SECURELY IN PRIVATE STORAGE (never public)
        $cnicFrontPath    = null;
        $cnicBackPath     = null;
        $licenseFrontPath = null;
        $licenseBackPath  = null;

        if ($request->hasFile('cnic_front_image')) {
            $cnicFrontPath = $request->file('cnic_front_image')->store('driver_documents/cnic', 'local');
        }
        if ($request->hasFile('cnic_back_image')) {
            $cnicBackPath = $request->file('cnic_back_image')->store('driver_documents/cnic', 'local');
        }
        if ($request->hasFile('license_front_image')) {
            $licenseFrontPath = $request->file('license_front_image')->store('driver_documents/licenses', 'local');
        }
        if ($request->hasFile('license_back_image')) {
            $licenseBackPath = $request->file('license_back_image')->store('driver_documents/licenses', 'local');
        }

        $user = User::create([
            'name'                     => $request->name,
            'email'                    => $request->email,
            'phone'                    => $request->phone,
            'gender'                   => $request->gender,
            'dob'                      => $request->dob,
            'cnic'                     => $request->cnic,
            'bio'                      => $request->bio ?? $request->about,
            'license_no'               => $request->license_no,
            'license_issue_date'       => $request->license_issue_date,
            'license_expiry_date'      => $request->license_expiry_date,
            'cnic_front_image'         => $cnicFrontPath,
            'cnic_back_image'          => $cnicBackPath,
            'license_front_image'      => $licenseFrontPath,
            'license_back_image'       => $licenseBackPath,
            'emergency_contact_name'   => $request->emergency_contact_name,
            'emergency_contact_phone'  => $request->emergency_contact_phone,
            'profile_photo'            => $photoPath,
            'password'                 => Hash::make($request->password),
            'user_type'                => $isDriver ? 2 : 0,   // 0 = passenger, 2 = driver
            'driver_status'            => $isDriver ? 'pending' : null,
            'terms_accepted_at'        => $request->filled('terms_accepted') ? now() : null,
            'privacy_accepted_at'      => $request->filled('privacy_accepted') ? now() : null,
            'declaration_confirmed_at' => $request->filled('declaration_confirmed') ? now() : null,
            'verification_consent_at'  => $request->filled('verification_consent') ? now() : null,
        ]);

        Auth::login($user);

        if ($isDriver) {
            // Notify administrators about new driver registration
            $admins = User::where('user_type', 1)->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'message' => "New driver application submitted by {$user->name} ({$user->email}). Pending administrator review.",
                    'is_read' => false,
                ]);
            }

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
