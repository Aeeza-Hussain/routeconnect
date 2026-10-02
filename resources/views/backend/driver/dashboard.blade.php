@extends('backend.driver.layouts.app')

@section('title', 'Driver Dashboard — RouteConnect')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-gauge"></i> Driver Console
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
            Welcome back, {{ $driver->name }}
        </h2>
        <p class="text-sm text-slate-400 mt-1">
            Manage your transport journeys, passenger bookings, and fleet status.
        </p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Account Approved & Active
        </span>
    </div>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-800 text-emerald-300 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ─── Statistics Grid (4 Cards) ─── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- 1. Assigned Vehicle --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Assigned Vehicle</p>
                @if ($vehicle)
                    <p class="text-xl font-black text-white font-mono mt-2">{{ $vehicle->registration_no }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $vehicle->model }}</p>
                @else
                    <p class="text-sm font-bold text-slate-500 mt-2">No Vehicle</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Contact admin to assign</p>
                @endif
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-lg border border-teal-500/20">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.vehicle') }}" class="text-[11px] font-bold text-teal-400 hover:text-teal-300 transition-colors">
                Vehicle Details →
            </a>
        </div>
    </div>

    {{-- 2. Total Trips --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Scheduled Trips</p>
                <p class="text-3xl font-black text-white mt-2">{{ $tripsCount }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Your journey routes</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-lg border border-indigo-500/20">
                <i class="fa-solid fa-route"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.trips') }}" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors">
                View Trips →
            </a>
        </div>
    </div>

    {{-- 3. Total Bookings --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Passenger Bookings</p>
                <p class="text-3xl font-black text-white mt-2">{{ $bookingsCount }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Confirmed reservations</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg border border-sky-500/20">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.bookings') }}" class="text-[11px] font-bold text-sky-400 hover:text-sky-300 transition-colors">
                View Bookings →
            </a>
        </div>
    </div>

    {{-- 4. Status --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Verification</p>
                <p class="text-xl font-extrabold text-emerald-400 mt-2">Approved</p>
                <p class="text-[11px] text-slate-400 mt-0.5">License: {{ $driver->license_no ?? 'Verified' }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/20">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.profile') }}" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors">
                My Profile →
            </a>
        </div>
    </div>

</div>

{{-- ─── Quick Shortcuts ─── --}}
<div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 mb-8">
    <h3 class="font-extrabold text-white text-sm mb-4 flex items-center gap-2">
        <i class="fa-solid fa-bolt text-teal-400"></i>
        Quick Shortcuts
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
        <a href="{{ route('driver.profile') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all text-center">
            <i class="fa-solid fa-user text-teal-400 text-lg mb-2"></i>
            <span class="text-xs font-bold text-white block">Profile</span>
        </a>
        <a href="{{ route('driver.vehicle') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all text-center">
            <i class="fa-solid fa-van-shuttle text-teal-400 text-lg mb-2"></i>
            <span class="text-xs font-bold text-white block">Vehicle</span>
        </a>
        <a href="{{ route('driver.trips') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all text-center">
            <i class="fa-solid fa-route text-teal-400 text-lg mb-2"></i>
            <span class="text-xs font-bold text-white block">Trips</span>
        </a>
        <a href="{{ route('driver.bookings') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all text-center">
            <i class="fa-solid fa-ticket text-teal-400 text-lg mb-2"></i>
            <span class="text-xs font-bold text-white block">Bookings</span>
        </a>
        <a href="{{ route('driver.messages') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all text-center">
            <i class="fa-solid fa-comments text-teal-400 text-lg mb-2"></i>
            <span class="text-xs font-bold text-white block">Messages</span>
        </a>
        <a href="{{ route('driver.settings') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all text-center">
            <i class="fa-solid fa-gear text-teal-400 text-lg mb-2"></i>
            <span class="text-xs font-bold text-white block">Settings</span>
        </a>
    </div>
</div>

{{-- ─── Driver Profile Summary Card ─── --}}
<div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
        <i class="fa-solid fa-id-card text-teal-400"></i>
        Driver Account Overview
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1">Full Name</span>
            <span class="font-extrabold text-white">{{ $driver->name }}</span>
        </div>
        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1">Email</span>
            <span class="font-extrabold text-white">{{ $driver->email }}</span>
        </div>
        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1">Contact</span>
            <span class="font-extrabold text-white">{{ $driver->phone ?? 'Not provided' }}</span>
        </div>
        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1">Registered Since</span>
            <span class="font-extrabold text-white">{{ $driver->created_at->format('d M Y') }}</span>
        </div>
    </div>
</div>

@endsection
