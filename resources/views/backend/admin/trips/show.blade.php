@extends('backend.admin.layouts.app')

@section('title', 'Trip #' . $trip->id . ' Details — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 text-violet-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-bus"></i> Journey Details
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Trip #{{ $trip->id }}
        </h2>
        <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
            <span class="font-bold text-slate-800">{{ $trip->route->name ?? 'Route' }}</span>
            @if ($trip->route)
                <span>({{ $trip->route->start_location }}</span>
                <i class="fa-solid fa-arrow-right text-[10px] text-violet-500"></i>
                <span>{{ $trip->route->end_location }})</span>
            @endif
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.trips.edit', $trip->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold shadow-sm transition-colors">
            <i class="fa-solid fa-pen"></i> Edit Trip
        </a>
        <a href="{{ route('admin.trips.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Trips
        </a>
    </div>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

{{-- ─── Trip Overview Cards ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Main Trip Information (2 cols) --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Schedule & Vehicle Overview --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-extrabold text-slate-900 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-clock text-violet-600"></i> Schedule & Operational Info
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Departure Date</div>
                    <div class="text-base font-extrabold text-slate-900">
                        {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('M d, Y (l)') : '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Departure Time</div>
                    <div class="text-base font-extrabold text-slate-900">
                        {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Trip Status</div>
                    @php $st = strtolower($trip->status); @endphp
                    @if ($st === 'scheduled')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Scheduled
                        </span>
                    @elseif ($st === 'completed')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Completed
                        </span>
                    @elseif ($st === 'cancelled')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ ucfirst($trip->status) }}
                        </span>
                    @endif
                </div>

                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Available Seats</div>
                    <div class="text-base font-black text-slate-900">
                        {{ $trip->available_seats }} / {{ $trip->vehicle->total_seats ?? '—' }}
                        <span class="text-xs font-normal text-slate-400">seats open</span>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Ticket Fare</div>
                    <div class="text-base font-black text-emerald-600">
                        {{ $trip->fare ? 'PKR ' . number_format($trip->fare, 2) : 'Free / Not set' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Boarding Location</div>
                    <div class="text-sm font-semibold text-slate-800">
                        {{ $trip->pickup_point ?? 'Standard Origin Terminal' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Route Journey Display --}}
        @if ($trip->route)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-route text-violet-600"></i> Route Journey: {{ $trip->route->name }}
                    </h3>
                    <a href="{{ route('admin.routes.show', $trip->route->id) }}" class="text-xs font-bold text-violet-600 hover:underline">
                        View Route →
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-sm font-semibold">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl">
                        <i class="fa-solid fa-play text-xs text-emerald-600"></i> {{ $trip->route->start_location }}
                    </span>

                    @forelse ($trip->route->routeStops as $rStop)
                        <i class="fa-solid fa-arrow-right text-xs text-slate-300"></i>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-50 text-violet-800 border border-violet-200 rounded-xl text-xs">
                            <span class="w-4 h-4 rounded-full bg-violet-600 text-white text-[9px] flex items-center justify-center font-bold">
                                {{ $rStop->stop_order }}
                            </span>
                            {{ $rStop->stop->name ?? 'Stop' }}
                        </span>
                    @empty
                        <i class="fa-solid fa-arrow-right text-xs text-slate-300"></i>
                    @endforelse

                    <i class="fa-solid fa-arrow-right text-xs text-slate-300"></i>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl">
                        <i class="fa-solid fa-flag-checkered text-xs text-rose-600"></i> {{ $trip->route->end_location }}
                    </span>
                </div>
            </div>
        @endif

    </div>

    {{-- Driver & Vehicle Sidebar Cards (1 col) --}}
    <div class="space-y-6">

        {{-- Assigned Driver --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-violet-600"></i> Assigned Driver
            </h4>

            @if ($trip->driver)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center font-extrabold text-base shrink-0">
                        {{ strtoupper(substr($trip->driver->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900">{{ $trip->driver->name }}</div>
                        <div class="text-xs text-emerald-600 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> Approved Driver
                        </div>
                    </div>
                </div>

                <div class="space-y-2 text-xs border-t border-slate-100 pt-3">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="font-semibold text-slate-700">{{ $trip->driver->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Contact:</span>
                        <span class="font-semibold text-slate-700">{{ $trip->driver->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">License:</span>
                        <span class="font-semibold text-slate-700 font-mono">{{ $trip->driver->license_no ?? '—' }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.drivers.show', $trip->driver->id) }}"
                       class="text-xs font-bold text-violet-600 hover:underline flex items-center gap-1">
                        View Driver Profile <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            @else
                <p class="text-xs text-slate-400 italic">No driver assigned.</p>
            @endif
        </div>

        {{-- Assigned Vehicle --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-van-shuttle text-violet-600"></i> Assigned Vehicle
            </h4>

            @if ($trip->vehicle)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-van-shuttle"></i>
                    </div>
                    <div>
                        <div class="font-black text-slate-900 text-base font-mono">{{ $trip->vehicle->registration_no }}</div>
                        <div class="text-xs text-slate-500">{{ $trip->vehicle->type }} · {{ $trip->vehicle->model ?? 'Standard' }}</div>
                    </div>
                </div>

                <div class="space-y-2 text-xs border-t border-slate-100 pt-3">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Total Seats:</span>
                        <span class="font-bold text-slate-800">{{ $trip->vehicle->total_seats }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Vehicle Status:</span>
                        <span class="font-bold text-emerald-600">{{ $trip->vehicle->status }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.vehicles.show', $trip->vehicle->id) }}"
                       class="text-xs font-bold text-violet-600 hover:underline flex items-center gap-1">
                        View Vehicle Details <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            @else
                <p class="text-xs text-slate-400 italic">No vehicle assigned.</p>
            @endif
        </div>

        {{-- Danger Zone / Delete Trip --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Danger Zone</h4>
            <p class="text-xs text-slate-500 mb-4">Deleting this trip will permanently remove it from the schedule.</p>
            <form action="{{ route('admin.trips.destroy', $trip->id) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete Trip #{{ $trip->id }}?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold text-xs transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> Delete Trip
                </button>
            </form>
        </div>

    </div>

</div>

@endsection
