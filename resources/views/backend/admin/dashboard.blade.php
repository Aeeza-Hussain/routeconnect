@extends('backend.admin.layouts.app')

@section('title', 'Dashboard Overview — RouteConnect Admin')

@section('content')

<!-- Header Title -->
<div class="mb-8">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
        <i class="fa-solid fa-gauge"></i> Overview
    </div>
    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Welcome, {{ auth()->user()->name }}</h2>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">RouteConnect platform metrics and pending application management.</p>
</div>

<!-- Flash Success Message -->
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-2xl font-bold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Statistics Grid (6 Cards) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    
    <!-- 1. Total Users -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Users</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalUsers }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Registered passenger & driver accounts</div>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold border border-emerald-100">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <!-- 2. Total Drivers -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Drivers</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalDrivers }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Drivers registered on platform</div>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold border border-indigo-100">
            <i class="fa-solid fa-user-check"></i>
        </div>
    </div>

    <!-- 3. Pending Driver Applications -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pending Applications</div>
            <div class="text-3xl font-extrabold text-amber-600 mt-2">{{ $pendingDrivers }}</div>
            <div class="text-[11px] text-amber-700 font-bold mt-1">
                <a href="{{ route('admin.drivers.applications') }}" class="underline hover:text-amber-800">Review Applications →</a>
            </div>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold border border-amber-100">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>

    <!-- 4. Total Vehicles -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Vehicles</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalVehicles }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Fleet registered by drivers</div>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-bold border border-teal-100">
            <i class="fa-solid fa-van-shuttle"></i>
        </div>
    </div>

    <!-- 5. Total Trips -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Trips</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalTrips }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Scheduled vehicle journeys</div>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl font-bold border border-sky-100">
            <i class="fa-solid fa-bus"></i>
        </div>
    </div>

    <!-- 6. Total Bookings -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Bookings</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalBookings }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Passenger seat reservations</div>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl font-bold border border-purple-100">
            <i class="fa-solid fa-ticket"></i>
        </div>
    </div>

</div>

<!-- Recent Pending Applications Quick Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-clock text-amber-500"></i> Recent Pending Driver Applications
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Driver registrations waiting for administrator approval.</p>
        </div>
        <a href="{{ route('admin.drivers.applications') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 underline">
            View All Applications ({{ $pendingDrivers }}) →
        </a>
    </div>

    @if ($recentApplications->isEmpty())
        <div class="p-8 text-center text-slate-500 text-xs">
            No records found.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-4">Driver Name</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Registration Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold">
                    @foreach ($recentApplications as $driver)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-4 font-extrabold text-slate-900">{{ $driver->name }}</td>
                            <td class="p-4 text-slate-600">{{ $driver->email }}</td>
                            <td class="p-4 text-slate-600">{{ $driver->phone ?? 'N/A' }}</td>
                            <td class="p-4 text-slate-500">{{ $driver->created_at->format('d M Y, h:i A') }}</td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('admin.drivers.show', $driver->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition-colors">
                                    Details
                                </a>
                                <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
