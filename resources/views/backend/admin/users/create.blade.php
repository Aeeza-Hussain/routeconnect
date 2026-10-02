@extends('backend.admin.layouts.app')

@section('title', 'Create User — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-user-plus"></i> New User
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Create User Account</h2>
        <p class="text-sm text-slate-500 mt-1">Add a new passenger or driver account to the platform.</p>
    </div>
    <a href="{{ route('admin.users.index') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Users
    </a>
</div>

{{-- ─── Form Card ─── --}}
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-slate-400"></i>
                Fill in the details below. Admin (user_type = 1) cannot be selected here.
            </h3>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold space-y-1">
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
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Profile Photo (Optional)</label>
                <div class="flex items-center gap-4">
                    <div id="photoPreview" class="w-16 h-16 rounded-xl bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center text-slate-400 overflow-hidden">
                        <i class="fa-solid fa-camera text-xl"></i>
                    </div>
                    <div>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                               onchange="previewPhoto(this)"
                               class="text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="text-[11px] text-slate-400 mt-1">JPG, PNG, WEBP · Max 4MB</p>
                    </div>
                </div>
            </div>

            {{-- Name + Email Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Ahmed Ali"
                           class="w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                    @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="user@example.com"
                           class="w-full px-4 py-3 bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Phone + Gender --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Contact Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="03001234567"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>
                <div>
                    <label for="gender" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Gender</label>
                    <select id="gender" name="gender"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                        <option value="">Select gender</option>
                        <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            {{-- User Type --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    User Type <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('user_type', '0') == '0' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="user_type" value="0" class="sr-only"
                               {{ old('user_type', '0') == '0' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50'));this.closest('label').classList.add('border-emerald-500','bg-emerald-50')">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-person"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Passenger</div>
                            <div class="text-[11px] text-slate-500">user_type = 0</div>
                        </div>
                    </label>
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('user_type') == '2' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="user_type" value="2" class="sr-only"
                               {{ old('user_type') == '2' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50','border-indigo-500','bg-indigo-50'));this.closest('label').classList.add('border-indigo-500','bg-indigo-50')">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Driver</div>
                            <div class="text-[11px] text-slate-500">user_type = 2 (status: pending)</div>
                        </div>
                    </label>
                </div>
                @error('user_type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                <p class="mt-2 text-[11px] text-slate-400">Admin accounts cannot be created from this form.</p>
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               placeholder="Min 6 characters"
                               class="w-full px-4 py-3 bg-slate-50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 pr-10">
                        <button type="button" onclick="togglePwd('password','eyeCreate1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                            <i id="eyeCreate1" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Confirm Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="Repeat password"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 pr-10">
                        <button type="button" onclick="togglePwd('password_confirmation','eyeCreate2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                            <i id="eyeCreate2" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Bio --}}
            <div>
                <label for="bio" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Bio / About (Optional)</label>
                <textarea id="bio" name="bio" rows="3" placeholder="Short bio..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none">{{ old('bio') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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
