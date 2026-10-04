@extends('backend.driver.layouts.app')

@section('title', 'My Trips — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-route"></i> Journeys
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Scheduled Trips</h2>
        <p class="text-sm text-slate-400 mt-1">
            Review your assigned route departures, timings, vehicle capacity, and live status.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('driver.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
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

{{-- ─── Search & Status Filters ─── --}}
<div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-4 mb-6">
    <form method="GET" action="{{ route('driver.trips') }}" class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search route origin or destination..."
                   class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-10 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
        </div>

        <div class="w-full sm:w-48">
            <select name="status" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
                <option value="">All Trip Statuses</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="boarding" {{ request('status') === 'boarding' ? 'selected' : '' }}>Boarding</option>
                <option value="departed" {{ request('status') === 'departed' ? 'selected' : '' }}>Departed</option>
                <option value="delayed" {{ request('status') === 'delayed' ? 'selected' : '' }}>Delayed</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
            Filter
        </button>

        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('driver.trips') }}" class="w-full sm:w-auto text-center px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- ─── Trips Table ─── --}}
<div class="space-y-6">

    @if ($trips->isEmpty())
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-route"></i>
            </div>
            <h3 class="text-base font-extrabold text-white mb-1">No Trips Found</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                @if(request()->hasAny(['search', 'status']))
                    No trips match the specified filters. Try resetting the search filters.
                @else
                    Trips assigned to your vehicle and driver profile will appear here.
                @endif
            </p>
        </div>
    @else
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Route</th>
                            <th class="px-6 py-4">Departure Date</th>
                            <th class="px-6 py-4">Departure Time</th>
                            <th class="px-6 py-4">Vehicle</th>
                            <th class="px-6 py-4">Available Seats</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach ($trips as $trip)
                            <tr class="hover:bg-slate-900/40 transition-colors">
                                {{-- Route --}}
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-white text-sm">
                                        {{ $trip->route->origin ?? 'Origin' }} → {{ $trip->route->destination ?? 'Destination' }}
                                    </div>
                                    @if ($trip->route && $trip->route->name)
                                        <div class="text-[11px] text-teal-400 font-medium">{{ $trip->route->name }}</div>
                                    @endif
                                </td>

                                {{-- Departure Date --}}
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white">
                                        {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') : '—' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500">
                                        {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('l') : '' }}
                                    </div>
                                </td>

                                {{-- Departure Time --}}
                                <td class="px-6 py-4 font-mono font-semibold text-slate-200">
                                    {{ $trip->departure_time }}
                                </td>

                                {{-- Vehicle --}}
                                <td class="px-6 py-4">
                                    @if ($trip->vehicle)
                                        <span class="font-mono font-bold text-white">{{ $trip->vehicle->registration_no }}</span>
                                        <div class="text-[10px] text-slate-400">{{ $trip->vehicle->model }}</div>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>

                                {{-- Available Seats --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 font-mono font-bold text-teal-400">
                                        <i class="fa-solid fa-chair text-[10px]"></i>
                                        {{ $trip->available_seats }} seats
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    @php $st = strtolower($trip->status); @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                        {{ $st === 'scheduled' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : '' }}
                                        {{ $st === 'boarding' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                        {{ $st === 'departed' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : '' }}
                                        {{ $st === 'delayed' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                                        {{ $st === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                        {{ $st === 'cancelled' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            {{ $st === 'scheduled' ? 'bg-sky-400' : '' }}
                                            {{ $st === 'boarding' ? 'bg-amber-400 animate-pulse' : '' }}
                                            {{ $st === 'departed' ? 'bg-indigo-400' : '' }}
                                            {{ $st === 'delayed' ? 'bg-rose-400' : '' }}
                                            {{ $st === 'completed' ? 'bg-emerald-400' : '' }}
                                            {{ $st === 'cancelled' ? 'bg-red-400' : '' }}"></span>
                                        {{ ucfirst($trip->status) }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('driver.trips.show', $trip->id) }}"
                                           class="px-3 py-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/20 text-xs font-bold transition-all flex items-center gap-1.5">
                                            <i class="fa-solid fa-eye text-[10px]"></i>
                                            <span>Details</span>
                                        </a>
                                        <a href="{{ route('driver.trips.edit', $trip->id) }}"
                                           class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition-all flex items-center gap-1.5">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection
