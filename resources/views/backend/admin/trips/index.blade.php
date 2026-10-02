@extends('backend.admin.layouts.app')

@section('title', 'Trips Management — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 text-violet-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-bus"></i> Operations & Scheduling
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Trips Management</h2>
        <p class="text-sm text-slate-500 mt-1">Schedule journeys, assign drivers and vehicles to active routes, and track trip statuses.</p>
    </div>
    <a href="{{ route('admin.trips.create') }}"
       class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold shadow-sm shadow-violet-600/20 transition-all">
        <i class="fa-solid fa-plus"></i> Schedule Trip
    </a>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

@if (session('error'))
    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-500 text-base shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

{{-- ─── Stat Counters ─── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-bus"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Trips</div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalTrips) }}</div>
        </div>
    </div>

    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Scheduled</div>
            <div class="text-2xl font-black text-sky-600">{{ number_format($scheduledTrips) }}</div>
        </div>
    </div>

    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Completed</div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($completedTrips) }}</div>
        </div>
    </div>

    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-ban"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cancelled</div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($cancelledTrips) }}</div>
        </div>
    </div>
</div>

{{-- ─── Filters & Search ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.trips.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        {{-- Driver Filter --}}
        <div>
            <select name="driver_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                <option value="">All Drivers</option>
                @foreach ($drivers as $d)
                    <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Route Filter --}}
        <div>
            <select name="route_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                <option value="">All Routes</option>
                @foreach ($routes as $r)
                    <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->name }} ({{ $r->start_location }} → {{ $r->end_location }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date Filter --}}
        <div>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
        </div>

        {{-- Status Filter --}}
        <div>
            <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                <option value="">All Statuses</option>
                <option value="Scheduled" {{ strtolower(request('status')) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="Completed" {{ strtolower(request('status')) === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ strtolower(request('status')) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if (request()->anyFilled(['driver_id', 'route_id', 'date', 'status']))
                <a href="{{ route('admin.trips.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ─── Trips Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold bg-slate-50/50">
                    <th class="py-3.5 px-5">Trip #</th>
                    <th class="py-3.5 px-5">Route</th>
                    <th class="py-3.5 px-5">Driver</th>
                    <th class="py-3.5 px-5">Vehicle</th>
                    <th class="py-3.5 px-5">Departure</th>
                    <th class="py-3.5 px-5 text-center">Seats</th>
                    <th class="py-3.5 px-5 text-center">Status</th>
                    <th class="py-3.5 px-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($trips as $trip)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-4 px-5">
                            <span class="font-extrabold text-slate-900">#{{ $trip->id }}</span>
                        </td>
                        <td class="py-4 px-5">
                            @if ($trip->route)
                                <a href="{{ route('admin.trips.show', $trip->id) }}" class="font-extrabold text-slate-900 hover:text-violet-600 transition-colors block">
                                    {{ $trip->route->name }}
                                </a>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $trip->route->start_location }}</span>
                                    <i class="fa-solid fa-arrow-right text-[9px] text-violet-500"></i>
                                    <span>{{ $trip->route->end_location }}</span>
                                </div>
                            @else
                                <span class="text-rose-500 italic">Route unassigned</span>
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            @if ($trip->driver)
                                <div class="font-bold text-slate-800">{{ $trip->driver->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $trip->driver->phone ?? $trip->driver->email }}</div>
                            @else
                                <span class="text-slate-400 italic">No driver</span>
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            @if ($trip->vehicle)
                                <div class="font-extrabold text-slate-800">{{ $trip->vehicle->registration_no }}</div>
                                <div class="text-[11px] text-slate-500">{{ $trip->vehicle->type }} · {{ $trip->vehicle->total_seats }} seats</div>
                            @else
                                <span class="text-slate-400 italic">No vehicle</span>
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            <div class="font-bold text-slate-800">
                                {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('M d, Y') : '—' }}
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-clock text-[10px] text-slate-400"></i>
                                {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}
                            </div>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-chair text-[10px] text-slate-400"></i>
                                {{ $trip->available_seats }} / {{ $trip->vehicle->total_seats ?? '?' }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @php $st = strtolower($trip->status); @endphp
                            @if ($st === 'scheduled')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Scheduled
                                </span>
                            @elseif ($st === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Completed
                                </span>
                            @elseif ($st === 'cancelled')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ ucfirst($trip->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.trips.show', $trip->id) }}"
                                   title="View Trip Details"
                                   class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.trips.edit', $trip->id) }}"
                                   title="Edit Trip"
                                   class="w-8 h-8 rounded-lg bg-violet-50 hover:bg-violet-600 text-violet-600 hover:text-white flex items-center justify-center text-xs transition-colors">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.trips.destroy', $trip->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete Trip #{{ $trip->id }}?');"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete Trip"
                                            class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center text-xs transition-colors">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 px-4 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-2xl mx-auto mb-3">
                                <i class="fa-solid fa-bus"></i>
                            </div>
                            <div class="font-extrabold text-slate-800 text-base">No trips found</div>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                                @if (request()->anyFilled(['driver_id', 'route_id', 'date', 'status']))
                                    No scheduled journeys match the selected filter criteria.
                                @else
                                    No trips have been scheduled yet. Plan and assign your first trip journey now.
                                @endif
                            </p>
                            @if (request()->anyFilled(['driver_id', 'route_id', 'date', 'status']))
                                <a href="{{ route('admin.trips.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200">
                                    Reset Filters
                                </a>
                            @else
                                <a href="{{ route('admin.trips.create') }}" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-xs font-bold hover:bg-violet-700">
                                    <i class="fa-solid fa-plus mr-1"></i> Schedule First Trip
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($trips->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $trips->links() }}
        </div>
    @endif
</div>

@endsection
