@extends('backend.admin.layouts.app')

@section('title', 'Admin Dashboard Overview — RouteConnect')

@section('content')

{{-- ───────────────────────────── PAGE HEADER ───────────────────────────── --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-gauge"></i> Operations Control Panel
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
            Welcome back, {{ auth()->user()->name }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Real-time RouteConnect platform metrics and operations control.
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.drivers.applications') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-extrabold shadow-sm transition-all">
            <i class="fa-solid fa-id-card"></i>
            <span>Pending Applications</span>
            @if ($pendingDrivers > 0)
                <span class="px-2 py-0.5 rounded-full bg-black/20 text-white text-[10px] font-black">
                    {{ $pendingDrivers }}
                </span>
            @endif
        </a>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-sm shadow-emerald-600/20 transition-all">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add User</span>
        </a>
    </div>
</div>

{{-- ───────────────────────── FLASH MESSAGES ───────────────────────── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if (session('error'))
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- ─────────────────── STATISTICS GRID (7 CARDS) ─────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">

    {{-- 1. Total Users --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Users</p>
                <p class="text-3xl font-black text-slate-900 mt-2">{{ $totalUsers }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">All registered accounts</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl border border-emerald-100 group-hover:bg-emerald-100 transition-colors">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span>{{ $totalPassengers }} Pass. · {{ $totalDrivers }} Driv.</span>
            <a href="{{ route('admin.users.index') }}" class="text-emerald-600 font-extrabold hover:text-emerald-800 transition-colors">Manage →</a>
        </div>
    </div>

    {{-- 2. Pending Drivers --}}
    <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-amber-600 uppercase tracking-wider">Pending Drivers</p>
                <p class="text-3xl font-black text-amber-600 mt-2">{{ $pendingDrivers }}</p>
                <p class="text-[11px] text-amber-500 mt-0.5">Awaiting verification</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl border border-amber-200 group-hover:bg-amber-100 transition-colors">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-amber-100 flex items-center justify-between text-[11px] font-semibold text-amber-600">
            <span>Requires action</span>
            <a href="{{ route('admin.drivers.applications') }}" class="text-amber-700 font-extrabold hover:text-amber-900 transition-colors">Review →</a>
        </div>
    </div>

    {{-- 3. Approved Drivers --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Approved Drivers</p>
                <p class="text-3xl font-black text-teal-600 mt-2">{{ $approvedDrivers }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Active verified drivers</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl border border-teal-100 group-hover:bg-teal-100 transition-colors">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span class="text-rose-500">{{ $rejectedDrivers }} Rejected</span>
            <a href="{{ route('admin.drivers.index') }}" class="text-teal-600 font-extrabold hover:text-teal-800 transition-colors">View All →</a>
        </div>
    </div>

    {{-- 4. Total Vehicles --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Vehicles</p>
                <p class="text-3xl font-black text-indigo-600 mt-2">{{ $totalVehicles }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Fleet registered</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl border border-indigo-100 group-hover:bg-indigo-100 transition-colors">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span>Fleet capacity</span>
            <a href="{{ route('admin.vehicles.index') }}" class="text-indigo-600 font-extrabold hover:text-indigo-800 transition-colors">Manage →</a>
        </div>
    </div>

    {{-- 5. Total Routes --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Routes</p>
                <p class="text-3xl font-black text-sky-600 mt-2">{{ $totalRoutes }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Transit paths defined</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl border border-sky-100 group-hover:bg-sky-100 transition-colors">
                <i class="fa-solid fa-route"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span>Intercity & Urban</span>
            <a href="{{ route('admin.routes.index') }}" class="text-sky-600 font-extrabold hover:text-sky-800 transition-colors">Manage →</a>
        </div>
    </div>

    {{-- 6. Total Trips --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Trips</p>
                <p class="text-3xl font-black text-violet-600 mt-2">{{ $totalTrips }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Scheduled journeys</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl border border-violet-100 group-hover:bg-violet-100 transition-colors">
                <i class="fa-solid fa-bus"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span>Active departures</span>
            <a href="{{ route('admin.trips.index') }}" class="text-violet-600 font-extrabold hover:text-violet-800 transition-colors">Manage →</a>
        </div>
    </div>

    {{-- 7. Total Bookings --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Bookings</p>
                <p class="text-3xl font-black text-rose-600 mt-2">{{ $totalBookings }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Passenger reservations</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl border border-rose-100 group-hover:bg-rose-100 transition-colors">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span>Confirmed tickets</span>
            <a href="{{ route('admin.bookings.index') }}" class="text-rose-600 font-extrabold hover:text-rose-800 transition-colors">Manage →</a>
        </div>
    </div>

</div>

{{-- ────────────────────────── QUICK ACTION BUTTONS ────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-bolt text-emerald-500"></i>
                Quick Actions
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Direct shortcuts to manage RouteConnect platform operations.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">

        {{-- 1. Driver Applications --}}
        <a href="{{ route('admin.drivers.applications') }}"
           class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-amber-200 bg-amber-50/50 hover:bg-amber-100/70 hover:border-amber-300 text-amber-800 transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <span class="text-xs font-bold leading-tight">Driver Apps</span>
            <span class="text-[10px] text-amber-600 font-semibold mt-0.5">{{ $pendingDrivers }} Pending</span>
        </a>

        {{-- 2. Users --}}
        <a href="{{ route('admin.users.index') }}"
           class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-slate-800 hover:text-emerald-800 transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-200/80 group-hover:bg-emerald-100 text-slate-700 group-hover:text-emerald-700 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="text-xs font-bold leading-tight">Users</span>
            <span class="text-[10px] text-slate-500 group-hover:text-emerald-600 font-semibold mt-0.5">{{ $totalUsers }} Accounts</span>
        </a>

        {{-- 3. Vehicles --}}
        <a href="{{ route('admin.vehicles.index') }}"
           class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 text-slate-800 hover:text-indigo-800 transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-200/80 group-hover:bg-indigo-100 text-slate-700 group-hover:text-indigo-700 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
            <span class="text-xs font-bold leading-tight">Vehicles</span>
            <span class="text-[10px] text-slate-500 group-hover:text-indigo-600 font-semibold mt-0.5">{{ $totalVehicles }} Fleet</span>
        </a>

        {{-- 4. Routes --}}
        <a href="{{ route('admin.routes.index') }}"
           class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50 hover:border-sky-200 text-slate-800 hover:text-sky-800 transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-200/80 group-hover:bg-sky-100 text-slate-700 group-hover:text-sky-700 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-route"></i>
            </div>
            <span class="text-xs font-bold leading-tight">Routes</span>
            <span class="text-[10px] text-slate-500 group-hover:text-sky-600 font-semibold mt-0.5">{{ $totalRoutes }} Active</span>
        </a>

        {{-- 5. Stops --}}
        <a href="{{ route('admin.stops.index') }}"
           class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-teal-50 hover:border-teal-200 text-slate-800 hover:text-teal-800 transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-200/80 group-hover:bg-teal-100 text-slate-700 group-hover:text-teal-700 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <span class="text-xs font-bold leading-tight">Stops</span>
            <span class="text-[10px] text-slate-500 group-hover:text-teal-600 font-semibold mt-0.5">Stations</span>
        </a>

        {{-- 6. Trips --}}
        <a href="{{ route('admin.trips.index') }}"
           class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-violet-50 hover:border-violet-200 text-slate-800 hover:text-violet-800 transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-200/80 group-hover:bg-violet-100 text-slate-700 group-hover:text-violet-700 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-bus"></i>
            </div>
            <span class="text-xs font-bold leading-tight">Trips</span>
            <span class="text-[10px] text-slate-500 group-hover:text-violet-600 font-semibold mt-0.5">{{ $totalTrips }} Scheduled</span>
        </a>

        {{-- 7. Bookings --}}
        <a href="{{ route('admin.bookings.index') }}"
           class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-rose-50 hover:border-rose-200 text-slate-800 hover:text-rose-800 transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-200/80 group-hover:bg-rose-100 text-slate-700 group-hover:text-rose-700 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <span class="text-xs font-bold leading-tight">Bookings</span>
            <span class="text-[10px] text-slate-500 group-hover:text-rose-600 font-semibold mt-0.5">{{ $totalBookings }} Orders</span>
        </a>

    </div>
</div>

{{-- ────────────────────── TWO TABLES SECTION ────────────────────── --}}
<div class="space-y-8">

    {{-- ── 1. RECENT DRIVER APPLICATIONS TABLE ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-amber-500 rounded-full inline-block"></span>
                    Recent Pending Driver Applications
                </h3>
                <p class="text-xs text-slate-500 mt-0.5 ml-4">Latest driver registrations awaiting verification.</p>
            </div>
            <a href="{{ route('admin.drivers.applications') }}"
               class="shrink-0 px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-extrabold text-xs border border-amber-200 transition-colors inline-flex items-center gap-2">
                <i class="fa-solid fa-eye"></i>
                View All {{ $pendingDrivers }} Pending
            </a>
        </div>

        @if ($recentApplications->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="text-sm font-extrabold text-slate-700">All clear — no pending driver applications!</div>
                <p class="text-xs text-slate-400 mt-1">All driver registrations have been reviewed.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Driver</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Phone</th>
                            <th class="px-6 py-3">License No</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Applied Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentApplications as $driver)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($driver->profile_photo)
                                            <img src="{{ asset($driver->profile_photo) }}"
                                                 alt="{{ $driver->name }}"
                                                 class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 font-extrabold flex items-center justify-center text-xs border border-amber-200">
                                                {{ strtoupper(substr($driver->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-extrabold text-slate-900 block">{{ $driver->name }}</span>
                                            <span class="text-[10px] text-slate-400">user_type = 2</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">{{ $driver->email }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $driver->phone ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-600 font-mono text-[11px]">{{ $driver->license_no ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold border border-amber-200">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $driver->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.drivers.show', $driver->id) }}"
                                           class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold transition-colors inline-flex items-center gap-1">
                                            <i class="fa-solid fa-eye text-[10px]"></i> View
                                        </a>
                                        <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors inline-flex items-center gap-1">
                                                <i class="fa-solid fa-check text-[10px]"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold transition-colors inline-flex items-center gap-1">
                                                <i class="fa-solid fa-xmark text-[10px]"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── 2. RECENT BOOKINGS TABLE ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-rose-500 rounded-full inline-block"></span>
                    Recent Bookings
                </h3>
                <p class="text-xs text-slate-500 mt-0.5 ml-4">Latest passenger seat reservations across scheduled routes.</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}"
               class="shrink-0 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs border border-slate-200 transition-colors inline-flex items-center gap-2">
                <i class="fa-solid fa-list"></i>
                All Bookings ({{ $totalBookings }})
            </a>
        </div>

        @if ($recentBookings->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-400 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div class="text-sm font-extrabold text-slate-700">No bookings recorded yet</div>
                <p class="text-xs text-slate-400 mt-1">Passenger seat reservations will appear here in real-time.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Booking Ref</th>
                            <th class="px-6 py-3">Passenger</th>
                            <th class="px-6 py-3">Route / Trip</th>
                            <th class="px-6 py-3">Seats</th>
                            <th class="px-6 py-3">Total Fare</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Booked At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentBookings as $booking)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                    #{{ $booking->booking_reference ?? $booking->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-900">
                                        {{ $booking->user->name ?? 'Guest Passenger' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $booking->user->email ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-700 font-medium">
                                    @if ($booking->trip && $booking->trip->route)
                                        {{ $booking->trip->route->origin }} → {{ $booking->trip->route->destination }}
                                    @else
                                        Trip #{{ $booking->trip_id }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $booking->seats }} {{ Str::plural('seat', $booking->seats) }}
                                </td>
                                <td class="px-6 py-4 font-extrabold text-emerald-600">
                                    Rs. {{ number_format($booking->total_fare, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($booking->status === 'confirmed')
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                                            Confirmed
                                        </span>
                                    @elseif ($booking->status === 'cancelled')
                                        <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-extrabold border border-rose-200">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold border border-amber-200 capitalize">
                                            {{ $booking->status ?? 'Pending' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $booking->created_at->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@endsection
