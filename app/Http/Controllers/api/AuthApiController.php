<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    /**
     * Register a new passenger or driver account.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'string', 'min:8'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'user_type' => ['nullable', 'integer', 'in:0,2'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userType = (int) ($request->user_type ?? 0);

        $user = User::create([
            'name'          => trim($request->name),
            'email'         => strtolower(trim($request->email)),
            'password'      => Hash::make($request->password),
            'phone'         => $request->phone ? trim($request->phone) : null,
            'user_type'     => $userType,
            'driver_status' => ($userType === 2) ? 'pending' : 'not_applied',
        ]);

        $token = $user->createToken('routeconnect-mobile')->plainTextToken;

        $formattedUser = $this->formatUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => $formattedUser,
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => $formattedUser,
            ],
        ], 201);
    }

    /**
     * Authenticate user and issue Sanctum token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ], 401);
        }

        $token = $user->createToken('routeconnect-mobile')->plainTextToken;

        $formattedUser = $this->formatUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $formattedUser,
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => $formattedUser,
            ],
        ], 200);
    }

    /**
     * Revoke active token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $formatted = $this->formatUser($user);

        return response()->json([
            'success' => true,
            'user'    => $formatted,
            'data'    => [
                'user' => $formatted,
            ],
        ], 200);
    }

    /**
     * Update user profile details.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'                    => ['nullable', 'string', 'max:255'],
            'phone'                   => ['nullable', 'string', 'max:30'],
            'bio'                     => ['nullable', 'string', 'max:500'],
            'gender'                  => ['nullable', 'string', 'in:male,female,other'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'profile_photo'           => ['nullable', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->only([
            'name', 'phone', 'bio', 'gender',
            'emergency_contact_name', 'emergency_contact_phone'
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $data['photo'] = $path;
            $data['profile_photo'] = $path;
        }

        $user->update(array_filter($data, fn($v) => !is_null($v)));

        $formatted = $this->formatUser($user->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user'    => $formatted,
            'data'    => [
                'user' => $formatted,
            ],
        ], 200);
    }

    /**
     * Submit driver license and verification documents for approval.
     */
    public function applyDriver(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'license_no'          => ['required', 'string', 'max:100'],
            'license_issue_date'  => ['nullable', 'date'],
            'license_expiry_date' => ['nullable', 'date', 'after:today'],
            'cnic'                => ['nullable', 'string', 'max:30'],
            'cnic_front_image'    => ['nullable', 'image', 'max:5120'],
            'cnic_back_image'     => ['nullable', 'image', 'max:5120'],
            'license_front_image' => ['nullable', 'image', 'max:5120'],
            'license_back_image'  => ['nullable', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $fields = [
            'license_no'          => $request->license_no,
            'license_issue_date'  => $request->license_issue_date,
            'license_expiry_date' => $request->license_expiry_date,
            'cnic'                => $request->cnic,
            'user_type'           => 2, // switch to driver role
            'driver_status'       => 'pending',
        ];

        // Store sensitive driver documents privately or in driver_documents
        foreach (['cnic_front_image', 'cnic_back_image', 'license_front_image', 'license_back_image'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $fields[$fileKey] = $request->file($fileKey)->store('driver_documents', 'local');
            }
        }

        $user->update($fields);

        return response()->json([
            'success' => true,
            'message' => 'Driver application submitted successfully. Verification is pending approval.',
            'user'    => $this->formatUser($user->fresh()),
        ], 200);
    }

    /**
     * Format user record for mobile app consumption.
     */
    protected function formatUser(User $user): array
    {
        return [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'user_type'     => (int) $user->user_type,
            'role'          => $user->isDriver() ? 'driver' : ($user->isAdmin() ? 'admin' : 'passenger'),
            'role_label'    => $user->isDriver() ? 'Driver' : ($user->isAdmin() ? 'Admin' : 'Passenger'),
            'driver_status' => $user->driver_status,
            'is_approved_driver' => $user->isApprovedDriver(),
            'photo_url'     => $user->photo ? asset('storage/' . $user->photo) : null,
            'bio'           => $user->bio,
            'created_at'    => $user->created_at?->toIso8601String(),
        ];
    }
}
