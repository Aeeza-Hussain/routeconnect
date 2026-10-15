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
            Real-time operations center for trips, fleet status, passenger reservations, and passenger updates.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Driver Status: Approved
        </span>
        <a href="{{ route('driver.trips.create') }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-teal-500/20 flex items-center gap-2">
            <i class="fa-solid fa-calendar-plus"></i>
            <span>Schedule Trip</span>
        </a>
        <a href="{{ route('driver.trips') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-route"></i>
            <span>My Trips</span>
        </a>
    </div>
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

{{-- ─── Statistics Grid (5 Required KPIs) ─── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

    {{-- 1. Total Trips --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all flex flex-col justify-between">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Trips</p>
                <p class="text-3xl font-black text-white mt-2">{{ $totalTrips }}</p>
                <p class="text-[11px] text-slate-500 mt-1">All allocated trips</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-lg border border-teal-500/20">
                <i class="fa-solid fa-route"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.trips') }}" class="text-[11px] font-bold text-teal-400 hover:text-teal-300 transition-colors flex items-center justify-between">
                <span>View All</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    {{-- 2. Scheduled Trips --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all flex flex-col justify-between">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Scheduled Trips</p>
                <p class="text-3xl font-black text-white mt-2">{{ $scheduledTripsCount }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Upcoming departures</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg border border-sky-500/20">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.trips', ['status' => 'scheduled']) }}" class="text-[11px] font-bold text-sky-400 hover:text-sky-300 transition-colors flex items-center justify-between">
                <span>Filter Scheduled</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    {{-- 3. Completed Trips --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all flex flex-col justify-between">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Completed Trips</p>
                <p class="text-3xl font-black text-white mt-2">{{ $completedTripsCount }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Successfully finished</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/20">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.trips', ['status' => 'completed']) }}" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors flex items-center justify-between">
                <span>Filter Completed</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    {{-- 4. Total Bookings --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all flex flex-col justify-between">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Bookings</p>
                <p class="text-3xl font-black text-white mt-2">{{ $bookingsCount }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Passenger reservations</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-lg border border-indigo-500/20">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.bookings') }}" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center justify-between">
                <span>Manage Bookings</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    {{-- 5. Available Vehicle Seats --}}
    <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition-all flex flex-col justify-between">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Available Vehicle Seats</p>
                <p class="text-3xl font-black text-white mt-2">{{ $availableVehicleSeats }}</p>
                <p class="text-[11px] text-slate-500 mt-1">
                    {{ $vehicle ? $vehicle->model : 'No vehicle assigned' }}
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg border border-amber-500/20">
                <i class="fa-solid fa-chair"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80">
            <a href="{{ route('driver.vehicle') }}" class="text-[11px] font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center justify-between">
                <span>Vehicle Details</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

</div>

{{-- ─── Vehicle Overview & Quick Actions Banner ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Fleet & Vehicle Approval Status Card --}}
    <div class="lg:col-span-1 bg-slate-950/60 rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-van-shuttle text-teal-400"></i> Fleet Approval Status
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Driver Approved
                </span>
            </div>

            {{-- Fleet Approval Counters --}}
            <div class="grid grid-cols-3 gap-2 text-center mb-4">
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-xs font-extrabold text-emerald-400 block">{{ $approvedVehiclesCount }}</span>
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block mt-0.5">Approved</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-xs font-extrabold text-amber-400 block">{{ $pendingVehiclesCount }}</span>
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block mt-0.5">Pending</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-xs font-extrabold text-rose-400 block">{{ $rejectedVehiclesCount }}</span>
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block mt-0.5">Rejected</span>
                </div>
            </div>

            @if ($pendingVehiclesCount > 0)
                <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-300 mb-3 flex items-start gap-2">
                    <i class="fa-solid fa-hourglass-half text-amber-400 mt-0.5 shrink-0"></i>
                    <span><strong>{{ $pendingVehiclesCount }} vehicle(s)</strong> awaiting administrator approval before trips can be scheduled.</span>
                </div>
            @endif

            @if ($rejectedVehiclesCount > 0)
                <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-[11px] text-rose-300 mb-3 flex items-start gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 mt-0.5 shrink-0"></i>
                    <span><strong>{{ $rejectedVehiclesCount }} vehicle(s)</strong> rejected. Review details under My Vehicles.</span>
                </div>
            @endif

            @if ($vehicle)
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-black text-white">{{ $vehicle->registration_no }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize
                            {{ $vehicle->isApproved() ? 'bg-emerald-500/10 text-emerald-400' : ($vehicle->isRejected() ? 'bg-rose-500/10 text-rose-400' : 'bg-amber-500/10 text-amber-400') }}">
                            {{ $vehicle->status }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">{{ $vehicle->model }} · {{ $vehicle->total_seats }} Seats</div>
                </div>
            @else
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-center py-4 mb-4">
                    <i class="fa-solid fa-car-side text-slate-500 text-xl mb-1"></i>
                    <p class="text-xs text-slate-300 font-semibold">No vehicles registered yet</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Register a vehicle to begin scheduling trips.</p>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('driver.vehicles.index') }}" class="flex-1 text-center px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-bold border border-slate-800 transition-all">
                My Fleet ({{ $vehiclesCount }}) →
            </a>
            <a href="{{ route('driver.vehicles.create') }}" class="px-3 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all">
                <i class="fa-solid fa-plus"></i> Add
            </a>
        </div>
    </div>

    {{-- Quick Shortcuts --}}
    <div class="lg:col-span-2 bg-slate-950/60 rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
        <div>
            <h3 class="font-extrabold text-white text-sm mb-4 flex items-center gap-2">
                <i class="fa-solid fa-bolt text-teal-400"></i>
                Operations Quick Shortcuts
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <a href="{{ route('driver.trips.create') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calendar-plus text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-white block">Schedule a Trip</span>
                    <span class="text-[10px] text-slate-500">Plan new departure</span>
                </a>

                <a href="{{ route('driver.trips') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-route text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-white block">My Trips</span>
                    <span class="text-[10px] text-slate-500">View & edit trips</span>
                </a>

                <a href="{{ route('driver.bookings') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-ticket text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-white block">Passenger Bookings</span>
                    <span class="text-[10px] text-slate-500">Seat reservations</span>
                </a>

                <a href="{{ route('driver.messages') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-comments text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-white block">Trip Messages</span>
                    <span class="text-[10px] text-slate-500">Broadcast updates</span>
                </a>

                <a href="{{ route('driver.vehicle') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-van-shuttle text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-white block">My Vehicle</span>
                    <span class="text-[10px] text-slate-500">Assigned fleet</span>
                </a>

                <a href="{{ route('driver.profile') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-white block">Profile</span>
                    <span class="text-[10px] text-slate-500">Driver credentials</span>
                </a>

                <a href="{{ route('driver.settings') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-teal-500/50 hover:bg-slate-800/80 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-gear text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-white block">Settings</span>
                    <span class="text-[10px] text-slate-500">Security & alerts</span>
                </a>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
            <span>RouteConnect Commercial Driver License</span>
            <span class="font-mono font-bold text-teal-400">{{ $driver->license_no ?? 'DL-VERIFIED' }}</span>
        </div>
    </div>

</div>

{{-- ─── Recent Trips & Bookings Tables ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Recent Scheduled Trips --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-route text-teal-400"></i> Recent Trips
            </h3>
            <a href="{{ route('driver.trips') }}" class="text-xs font-bold text-teal-400 hover:text-teal-300">
                View All ({{ $totalTrips }}) →
            </a>
        </div>

        @if ($recentTrips->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-route text-slate-600 text-2xl mb-2 block"></i>
                No trips scheduled yet for your account.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3">Route</th>
                            <th class="px-5 py-3">Date & Time</th>
                            <th class="px-5 py-3">Seats</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach ($recentTrips as $trip)
                            <tr class="hover:bg-slate-900/50 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-white">
                                    {{ $trip->route->origin ?? 'Origin' }} → {{ $trip->route->destination ?? 'Destination' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div>{{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') : '—' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $trip->departure_time }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-teal-400">{{ $trip->available_seats }}</span> seats
                                </td>
                                <td class="px-5 py-3.5">
                                    @php $st = strtolower($trip->status); @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold
                                        {{ $st === 'scheduled' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : '' }}
                                        {{ $st === 'boarding' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                        {{ $st === 'departed' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : '' }}
                                        {{ $st === 'delayed' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                                        {{ $st === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                        {{ $st === 'cancelled' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}">
                                        {{ ucfirst($trip->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('driver.trips.show', $trip->id) }}" class="text-teal-400 hover:text-teal-300 font-bold text-[11px]">
                                        Details →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Recent Bookings --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-ticket text-teal-400"></i> Recent Passenger Bookings
            </h3>
            <a href="{{ route('driver.bookings') }}" class="text-xs font-bold text-teal-400 hover:text-teal-300">
                View All ({{ $bookingsCount }}) →
            </a>
        </div>

        @if ($recentBookings->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-ticket text-slate-600 text-2xl mb-2 block"></i>
                No passenger bookings recorded yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3">Booking Ref</th>
                            <th class="px-5 py-3">Passenger</th>
                            <th class="px-5 py-3">Seats</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach ($recentBookings as $b)
                            <tr class="hover:bg-slate-900/50 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-teal-400">
                                    #{{ $b->booking_reference ?? $b->id }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-white">
                                    {{ $b->user->name ?? 'Passenger' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    {{ $b->seats }} {{ \Illuminate\Support\Str::plural('seat', $b->seats) }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-400">
                                    {{ $b->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ ucfirst($b->status) }}
                                    </span>
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
