@extends('backend.admin.layouts.app')

@section('title', 'Edit User — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-pen"></i> Edit User
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Edit: {{ $user->name }}</h2>
        <p class="text-sm text-slate-500 mt-1">Update user account information. Leave password blank to keep existing.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.users.show', $user->id) }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-eye"></i> View
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- ─── Form Card ─── --}}
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                    <div class="font-extrabold text-sm mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:
                    </div>
                    <ul class="list-disc ml-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Profile Photo --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Profile Photo</label>
                <div class="flex items-center gap-4">
                    <div id="photoPreview" class="w-16 h-16 rounded-xl overflow-hidden border-2 border-slate-200">
                        @if ($user->profile_photo)
                            <img src="{{ asset($user->profile_photo) }}" class="w-full h-full object-cover" id="previewImg">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400 text-xl font-extrabold">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                               onchange="previewPhoto(this)"
                               class="text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-[11px] text-slate-400 mt-1">Upload new photo to replace current</p>
                    </div>
                </div>
            </div>

            {{-- Name + Email --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Phone + Gender --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Contact Number</label>
                    <input type="text" id="phone" name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="gender" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Gender</label>
                    <select id="gender" name="gender"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select gender</option>
                        <option value="male"   {{ old('gender', $user->gender) == 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender', $user->gender) == 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            {{-- User Type --}}
            <div>
                <label for="user_type" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    User Type <span class="text-rose-500">*</span>
                </label>
                <select id="user_type" name="user_type" onchange="toggleDriverStatus()"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0" {{ old('user_type', $user->user_type) == 0 ? 'selected' : '' }}>Passenger (user_type = 0)</option>
                    <option value="1" {{ old('user_type', $user->user_type) == 1 ? 'selected' : '' }}>Admin (user_type = 1)</option>
                    <option value="2" {{ old('user_type', $user->user_type) == 2 ? 'selected' : '' }}>Driver (user_type = 2)</option>
                </select>
                @error('user_type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Driver Status (only shown when user_type = 2) --}}
            <div id="driverStatusField" class="{{ old('user_type', $user->user_type) == 2 ? '' : 'hidden' }}">
                <label for="driver_status" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Driver Status
                </label>
                <select id="driver_status" name="driver_status"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="pending"  {{ old('driver_status', $user->driver_status) == 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ old('driver_status', $user->driver_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('driver_status', $user->driver_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('driver_status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Password (optional on edit) --}}
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                <div class="text-xs font-bold text-amber-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-lock"></i>
                    Change Password (leave blank to keep current password)
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">New Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                   placeholder="Leave blank to keep current"
                                   class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 pr-10">
                            <button type="button" onclick="togglePwd('password','eyeEdit1')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                                <i id="eyeEdit1" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   placeholder="Repeat new password"
                                   class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 pr-10">
                            <button type="button" onclick="togglePwd('password_confirmation','eyeEdit2')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                                <i id="eyeEdit2" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bio --}}
            <div>
                <label for="bio" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Bio / About</label>
                <textarea id="bio" name="bio" rows="3"
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('bio', $user->bio) }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleDriverStatus() {
        const type  = document.getElementById('user_type').value;
        const field = document.getElementById('driverStatusField');
        field.classList.toggle('hidden', type !== '2');
    }

    function togglePwd(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function previewPhoto(input) {
        const preview = document.getElementById('photoPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
