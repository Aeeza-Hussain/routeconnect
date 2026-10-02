@extends('backend.driver.layouts.app')

@section('title', 'Driver Settings — RouteConnect')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-gear"></i> Preferences
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Driver Account Settings</h2>
        <p class="text-sm text-slate-400 mt-1">Manage notification preferences, profile details, and account security.</p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

<div class="max-w-3xl space-y-6">

    {{-- Notification Settings --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 space-y-4">
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-bell text-teal-400"></i>
            Booking & Journey Notifications
        </h3>

        <div class="space-y-3 pt-2">
            <label class="flex items-center justify-between p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
                <div>
                    <span class="text-xs font-bold text-white block">New Passenger Booking Alerts</span>
                    <span class="text-[11px] text-slate-500">Receive instant notifications when a passenger books a seat.</span>
                </div>
                <input type="checkbox" checked class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 bg-slate-800 border-slate-700">
            </label>

            <label class="flex items-center justify-between p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
                <div>
                    <span class="text-xs font-bold text-white block">Trip Schedule Reminders</span>
                    <span class="text-[11px] text-slate-500">Receive reminders 1 hour before scheduled departure time.</span>
                </div>
                <input type="checkbox" checked class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 bg-slate-800 border-slate-700">
            </label>
        </div>
    </div>

    {{-- Security Section --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
        <h3 class="text-sm font-extrabold text-white mb-2 flex items-center gap-2">
            <i class="fa-solid fa-lock text-teal-400"></i>
            Account Security
        </h3>
        <p class="text-xs text-slate-400 mb-4">Password was last set during registration. Contact admin if you need a password reset.</p>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
                <i class="fa-solid fa-shield-halved mr-1"></i> Account Protected
            </span>
        </div>
    </div>

</div>

@endsection
