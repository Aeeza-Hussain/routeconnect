@extends('backend.driver.layouts.app')

@section('title', 'Driver Settings — RouteConnect')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-gear"></i> Preferences & Security
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Driver Account Settings</h2>
        <p class="text-sm text-slate-400 mt-1">
            Manage your account security password and operational notification preferences.
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
            <i class="fa-solid fa-triangle-exclamation"></i> Security validation failed:
        </div>
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="max-w-3xl space-y-8">

    {{-- Change Password Section --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 sm:p-8">
        <h3 class="text-base font-extrabold text-white mb-1 flex items-center gap-2">
            <i class="fa-solid fa-lock text-teal-400"></i> Account Security & Password
        </h3>
        <p class="text-xs text-slate-400 mb-6">
            Ensure your account is using a long, random password to stay secure.
        </p>

        <form action="{{ route('driver.settings.password') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Current Password <span class="text-rose-400">*</span>
                </label>
                <input type="password"
                       name="current_password"
                       id="current_password"
                       required
                       class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        New Password <span class="text-rose-400">*</span>
                    </label>
                    <input type="password"
                           name="new_password"
                           id="new_password"
                           required
                           minlength="8"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Confirm New Password <span class="text-rose-400">*</span>
                    </label>
                    <input type="password"
                           name="new_password_confirmation"
                           id="new_password_confirmation"
                           required
                           minlength="8"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-all shadow-lg shadow-teal-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-key"></i>
                    <span>Update Password</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Notification Preferences --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 sm:p-8 space-y-4">
        <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-bell text-teal-400"></i>
            Operational Notifications
        </h3>
        <p class="text-xs text-slate-400">Choose when and how you want to be alerted regarding trips and passengers.</p>

        <div class="space-y-3 pt-2">
            <label class="flex items-center justify-between p-4 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-slate-700 transition-colors">
                <div>
                    <span class="text-xs font-bold text-white block">New Passenger Booking Alerts</span>
                    <span class="text-[11px] text-slate-500">Receive instant push notifications when a passenger books a seat on your trip.</span>
                </div>
                <input type="checkbox" checked class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 bg-slate-800 border-slate-700">
            </label>

            <label class="flex items-center justify-between p-4 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-slate-700 transition-colors">
                <div>
                    <span class="text-xs font-bold text-white block">Trip Departure Reminders</span>
                    <span class="text-[11px] text-slate-500">Receive schedule countdown alerts 1 hour before planned departure.</span>
                </div>
                <input type="checkbox" checked class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 bg-slate-800 border-slate-700">
            </label>

            <label class="flex items-center justify-between p-4 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-slate-700 transition-colors">
                <div>
                    <span class="text-xs font-bold text-white block">Administrative Broadcasts</span>
                    <span class="text-[11px] text-slate-500">Receive road alerts, landslide warnings, and terminal management advisories.</span>
                </div>
                <input type="checkbox" checked class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 bg-slate-800 border-slate-700">
            </label>
        </div>
    </div>

</div>

@endsection
