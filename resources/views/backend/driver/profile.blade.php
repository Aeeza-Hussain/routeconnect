@extends('backend.driver.layouts.app')

@section('title', 'My Profile — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-user"></i> Driver Account
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Commercial Driver Profile</h2>
        <p class="text-sm text-slate-400 mt-1">
            Manage your personal profile credentials, contact details, and commercial driver information.
        </p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-800 text-emerald-300 text-sm rounded-2xl font-semibold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-950/40 border border-rose-800 text-rose-300 text-xs font-semibold space-y-1">
        <div class="font-bold flex items-center gap-2 mb-1 text-sm text-rose-400">
            <i class="fa-solid fa-triangle-exclamation"></i> Please correct the following errors:
        </div>
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="max-w-4xl space-y-8">

    {{-- Profile Banner Card --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-teal-600 text-white font-extrabold flex items-center justify-center text-2xl shadow-lg shadow-teal-600/20 shrink-0">
                {{ strtoupper(substr($driver->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-white flex items-center gap-2.5">
                    {{ $driver->name }}
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-extrabold border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Verified Driver
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $driver->email }}</p>
                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1.5">
                    <span>License: <strong class="text-teal-400 font-mono">{{ $driver->license_no ?? 'DL-VERIFIED' }}</strong></span>
                    <span>•</span>
                    <span>Joined: {{ $driver->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <div class="sm:text-right">
            <span class="text-xs text-slate-500 block">Account Role</span>
            <span class="text-xs font-bold text-white">Commercial Driver (user_type = 2)</span>
        </div>
    </div>

    {{-- Update Profile Form --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 sm:p-8">
        <h3 class="text-base font-extrabold text-white mb-1 flex items-center gap-2">
            <i class="fa-solid fa-user-pen text-teal-400"></i> Update Profile Information
        </h3>
        <p class="text-xs text-slate-400 mb-6">Modify your personal contact details, CNIC, and driver bio.</p>

        <form action="{{ route('driver.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Full Name --}}
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Full Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           required
                           value="{{ old('name', $driver->name) }}"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                </div>

                {{-- Email Address (Read-only) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Email Address (Read-only)
                    </label>
                    <input type="email"
                           value="{{ $driver->email }}"
                           disabled
                           class="w-full bg-slate-900/50 border border-slate-800/80 rounded-xl px-4 py-2.5 text-xs text-slate-500 cursor-not-allowed">
                    <span class="text-[10px] text-slate-500 mt-1 block">Contact administration to update email address.</span>
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Phone Number
                    </label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           value="{{ old('phone', $driver->phone) }}"
                           placeholder="0300-1234567"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                </div>

                {{-- CNIC Number --}}
                <div>
                    <label for="cnic" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        CNIC Number
                    </label>
                    <input type="text"
                           name="cnic"
                           id="cnic"
                           value="{{ old('cnic', $driver->cnic) }}"
                           placeholder="71501-1234567-1"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                </div>

                {{-- Commercial License No --}}
                <div class="sm:col-span-2">
                    <label for="license_no" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Commercial Driving License Number
                    </label>
                    <input type="text"
                           name="license_no"
                           id="license_no"
                           value="{{ old('license_no', $driver->license_no) }}"
                           placeholder="DL-GB-8921"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-teal-500">
                </div>

                {{-- Bio --}}
                <div class="sm:col-span-2">
                    <label for="bio" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Driver Bio & Mountain Route Experience
                    </label>
                    <textarea name="bio"
                              id="bio"
                              rows="3"
                              placeholder="Brief description of your driving experience, routes navigated, safety records..."
                              class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">{{ old('bio', $driver->bio) }}</textarea>
                </div>

            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-all shadow-lg shadow-teal-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Profile Changes</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
