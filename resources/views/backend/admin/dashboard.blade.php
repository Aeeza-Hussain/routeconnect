@extends('backend.admin.layouts.app')

@section('title', 'Dashboard Overview — RouteConnect Admin')

@section('content')

{{-- ───────────────────────────── PAGE HEADER ───────────────────────────── --}}
<div class="mb-8">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-gauge"></i> Control Panel
    </div>
    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
        Welcome back, {{ auth()->user()->name }}
    </h2>
    <p class="text-sm text-slate-500 mt-1">
        RouteConnect platform overview — all statistics pulled live from the database.
    </p>
</div>

{{-- ───────────────────────── FLASH MESSAGES ───────────────────────── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ─────────────────── STATISTICS GRID (7 CARDS) ─────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- 1. Total Users --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Users</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalUsers }}</p>
                <p class="text-[11px] text-slate-400 mt-1">All registered accounts</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl border border-emerald-100 group-hover:bg-emerald-100 transition-colors">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span>{{ $totalPassengers }} Passengers</span>
            <span>{{ $totalDrivers }} Drivers</span>
        </div>
    </div>

    {{-- 2. Pending Applications --}}
    <div class="bg-white p-6 rounded-2xl border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Pending Applications</p>
                <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $pendingDrivers }}</p>
                <p class="text-[11px] text-amber-500 mt-1">Awaiting admin review</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl border border-amber-200 group-hover:bg-amber-100 transition-colors">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-amber-100">
            <a href="{{ route('admin.drivers.applications') }}" class="text-[11px] font-bold text-amber-700 hover:text-amber-900 transition-colors">
                Review Applications →
            </a>
        </div>
    </div>

    {{-- 3. Approved Drivers --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Approved Drivers</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $approvedDrivers }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Active on platform</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl border border-teal-100 group-hover:bg-teal-100 transition-colors">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-500">
            <span class="text-rose-500">{{ $rejectedDrivers }} Rejected</span>
            <a href="{{ route('admin.drivers.index') }}" class="text-teal-600 font-bold hover:text-teal-800">View All →</a>
        </div>
    </div>

    {{-- 4. Total Vehicles --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Vehicles</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalVehicles }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Fleet registered by drivers</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl border border-indigo-100 group-hover:bg-indigo-100 transition-colors">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100">
            <span class="text-[11px] text-slate-400 font-semibold">Coming soon: Vehicle CRUD</span>
        </div>
    </div>

    {{-- 5. Total Routes --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Routes</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalRoutes }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Defined transport routes</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl border border-sky-100 group-hover:bg-sky-100 transition-colors">
                <i class="fa-solid fa-route"></i>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100">
            <span class="text-[11px] text-slate-400 font-semibold">Coming soon: Routes CRUD</span>
        </div>
    </div>

    {{-- 6. Total Trips --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Trips</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalTrips }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Scheduled vehicle journeys</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl border border-violet-100 group-hover:bg-violet-100 transition-colors">
                <i class="fa-solid fa-bus"></i>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100">
            <span class="text-[11px] text-slate-400 font-semibold">Coming soon: Trips CRUD</span>
        </div>
    </div>

    {{-- 7. Total Bookings --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Bookings</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalBookings }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Passenger seat reservations</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl border border-rose-100 group-hover:bg-rose-100 transition-colors">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100">
            <span class="text-[11px] text-slate-400 font-semibold">Coming soon: Bookings CRUD</span>
        </div>
    </div>

</div>

{{-- ──────────────── RECENT PENDING DRIVER APPLICATIONS TABLE ─────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Table Header --}}
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <span class="w-2 h-5 bg-amber-500 rounded-full inline-block"></span>
                Recent Pending Driver Applications
            </h3>
            <p class="text-xs text-slate-500 mt-0.5 ml-4">Latest driver registrations awaiting your review.</p>
        </div>
        @if ($pendingDrivers > 0)
            <a href="{{ route('admin.drivers.applications') }}"
               class="shrink-0 px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs border border-amber-200 transition-colors inline-flex items-center gap-2">
                <i class="fa-solid fa-eye"></i>
                View All {{ $pendingDrivers }} Pending
            </a>
        @else
            <a href="{{ route('admin.drivers.applications') }}"
               class="shrink-0 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs border border-slate-200 transition-colors inline-flex items-center gap-2">
                <i class="fa-solid fa-list"></i>
                All Applications
            </a>
        @endif
    </div>

    @if ($recentApplications->isEmpty())
        {{-- Empty State --}}
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-400 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700">All clear — no pending applications!</div>
            <p class="text-xs text-slate-500 mt-1">All driver applications have been reviewed.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Applied On</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($recentApplications as $driver)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- Driver name + avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($driver->profile_photo)
                                        <img src="{{ asset($driver->profile_photo) }}"
                                             alt="{{ $driver->name }}"
                                             class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-xs border border-indigo-200">
                                            {{ strtoupper(substr($driver->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="font-extrabold text-slate-900">{{ $driver->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $driver->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $driver->phone ?? '—' }}</td>
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

        @if ($pendingDrivers > 5)
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 font-semibold">
                Showing 5 of {{ $pendingDrivers }} pending applications.
                <a href="{{ route('admin.drivers.applications') }}" class="text-emerald-600 font-bold hover:underline ml-1">View all →</a>
            </div>
        @endif
    @endif
</div>

@endsection
