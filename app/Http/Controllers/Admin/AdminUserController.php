<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Display all users with optional search & filter.
     *
     * GET /admin/users
     *
     * Query params:
     *   ?search=     → search by name or email
     *   ?type=       → filter by user_type (0, 1, 2, or 'all')
     *   ?status=     → filter by driver_status (pending, approved, rejected, or 'all')
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $type   = $request->input('type', 'all');
        $status = $request->input('status', 'all');

        $query = User::query();

        // Search by name or email
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter by user_type
        if ($type !== 'all' && in_array($type, ['0', '1', '2'])) {
            $query->where('user_type', (int) $type);
        }

        // Filter by driver_status (only relevant for drivers)
        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('driver_status', $status);
        }

        // Paginate results — 15 per page, keep query params in pagination links
        $users = $query->latest()->paginate(15)->withQueryString();

        // Count badges for filter tabs
        $counts = [
            'all'       => User::count(),
            'passenger' => User::where('user_type', 0)->count(),
            'driver'    => User::where('user_type', 2)->count(),
            'admin'     => User::where('user_type', 1)->count(),
        ];

        return view('backend.admin.users.index', compact('users', 'search', 'type', 'status', 'counts'));
    }

    /**
     * Show the form for creating a new user.
     *
     * GET /admin/users/create
     */
    public function create()
    {
        return view('backend.admin.users.create');
    }

    /**
     * Store a newly created user.
     *
     * POST /admin/users
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
            'user_type'             => ['required', 'in:0,2'],  // Only Passenger(0) or Driver(2) — NOT Admin
            'gender'                => ['nullable', 'in:male,female,other'],
            'dob'                   => ['nullable', 'date'],
            'cnic'                  => ['nullable', 'string', 'max:30'],
            'bio'                   => ['nullable', 'string', 'max:1000'],
            'profile_photo'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        // Handle profile photo upload
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $file            = $request->file('profile_photo');
            $filename        = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/profiles');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $file->move($destinationPath, $filename);
            $photoPath = 'uploads/profiles/' . $filename;
        }

        $isDriver = (int) $request->user_type === 2;

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'user_type'     => (int) $request->user_type,
            'driver_status' => $isDriver ? 'pending' : null,
            'gender'        => $request->gender,
            'dob'           => $request->dob,
            'cnic'          => $request->cnic,
            'bio'           => $request->bio,
            'profile_photo' => $photoPath,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$request->name}' created successfully.");
    }

    /**
     * Show a user profile details page.
     *
     * GET /admin/users/{id}
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('backend.admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing a user.
     *
     * GET /admin/users/{id}/edit
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.admin.users.edit', compact('user'));
    }

    /**
     * Update a user.
     *
     * PUT /admin/users/{id}
     *
     * IMPORTANT: Password is optional on edit.
     * If left blank, existing password is preserved.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone'         => ['nullable', 'string', 'max:20'],
            'user_type'     => ['required', 'in:0,1,2'],
            'driver_status' => ['nullable', 'in:pending,approved,rejected'],
            'gender'        => ['nullable', 'in:male,female,other'],
            'dob'           => ['nullable', 'date'],
            'cnic'          => ['nullable', 'string', 'max:30'],
            'bio'           => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $file            = $request->file('profile_photo');
            $filename        = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/profiles');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $file->move($destinationPath, $filename);
            $user->profile_photo = 'uploads/profiles/' . $filename;
        }

        // Update fields
        $user->name          = $request->name;
        $user->email         = $request->email;
        $user->phone         = $request->phone;
        $user->user_type     = (int) $request->user_type;
        $user->driver_status = ($user->user_type == 2) ? $request->driver_status : null;
        $user->gender        = $request->gender;
        $user->dob           = $request->dob;
        $user->cnic          = $request->cnic;
        $user->bio           = $request->bio;

        // Only update password if a new one was provided
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Delete a user.
     *
     * DELETE /admin/users/{id}
     *
     * IMPORTANT: Prevent admin from deleting their own account.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' has been deleted.");
    }
}
