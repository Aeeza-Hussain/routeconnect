@extends('backend.admin.layouts.app')

@section('title', 'Trip #' . $trip->id . ' — Manage Stops — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 text-violet-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-map-pin"></i> Trip Stop Timings
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Manage Stops — Trip #{{ $trip->id }}
        </h2>
        <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
            <span class="font-bold text-slate-700">{{ $trip->route->name ?? 'Route' }}</span>
            @if($trip->route)
                <i class="fa-solid fa-arrow-right text-[10px] text-violet-500"></i>
                <span>{{ $trip->route->start_location }}</span>
                <i class="fa-solid fa-arrow-right text-[10px] text-slate-300"></i>
                <span>{{ $trip->route->end_location }}</span>
            @endif
            <span class="text-slate-300">·</span>
            <span>{{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('M d, Y') : '—' }}</span>
            <span class="text-slate-300">·</span>
            <span>Dep. {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}</span>
        </div>
    </div>
    <a href="{{ route('admin.trips.show', $trip->id) }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Trip
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

@if ($errors->any())
    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-start gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base mt-0.5 shrink-0"></i>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ─── LEFT: Current Trip Stops Table ─── --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Assigned Stops Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-list-ol text-violet-600"></i>
                    Scheduled Stops &amp; Expected Times
                </h3>
                <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">
                    {{ $trip->tripStops->count() }} / {{ $routeStops->count() }} stops assigned
                </span>
            </div>

            @if ($trip->tripStops->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 rounded-full bg-violet-50 text-violet-400 flex items-center justify-center text-2xl mx-auto mb-4">
                        <i class="fa-solid fa-map-pin"></i>
                    </div>
                    <p class="text-slate-500 font-semibold text-sm">No stops assigned yet.</p>
                    <p class="text-slate-400 text-xs mt-1">Use the form on the right to add stops and set expected arrival times.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-5 py-3 text-left text-xs font-extrabold text-slate-500 uppercase tracking-wider">Order</th>
                                <th class="px-5 py-3 text-left text-xs font-extrabold text-slate-500 uppercase tracking-wider">Stop Name</th>
                                <th class="px-5 py-3 text-left text-xs font-extrabold text-slate-500 uppercase tracking-wider">Location</th>
                                <th class="px-5 py-3 text-left text-xs font-extrabold text-slate-500 uppercase tracking-wider">Expected Time</th>
                                <th class="px-5 py-3 text-center text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($trip->tripStops as $tripStop)
                                <tr class="hover:bg-slate-50 transition-colors" id="stop-row-{{ $tripStop->id }}">
                                    <td class="px-5 py-3.5">
                                        <span class="w-7 h-7 rounded-full bg-violet-600 text-white text-xs font-extrabold flex items-center justify-center">
                                            {{ $tripStop->stop_order }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-extrabold text-slate-900">{{ $tripStop->stop->name ?? '—' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500">
                                        {{ $tripStop->stop->location ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200">
                                            <i class="fa-solid fa-clock text-emerald-600"></i>
                                            {{ \Carbon\Carbon::parse($tripStop->expected_time)->format('h:i A') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Edit Button (opens inline edit row) --}}
                                            <button type="button"
                                                    onclick="toggleEditRow({{ $tripStop->id }})"
                                                    class="p-2 rounded-lg bg-violet-50 hover:bg-violet-100 text-violet-700 text-xs font-bold transition-colors"
                                                    title="Edit timing">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            {{-- Delete Button --}}
                                            <form action="{{ route('admin.trips.stops.destroy', [$trip->id, $tripStop->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Remove {{ addslashes($tripStop->stop->name ?? 'stop') }} from this trip?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors"
                                                        title="Remove stop">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Inline Edit Row (hidden by default) --}}
                                <tr id="edit-row-{{ $tripStop->id }}" class="hidden bg-violet-50">
                                    <td colspan="5" class="px-5 py-4">
                                        <form action="{{ route('admin.trips.stops.update', [$trip->id, $tripStop->id]) }}"
                                              method="POST"
                                              class="flex flex-wrap items-end gap-4">
                                            @csrf
                                            @method('PUT')

                                            <div>
                                                <label class="block text-xs font-bold text-slate-600 mb-1">Stop Order</label>
                                                <input type="number"
                                                       name="stop_order"
                                                       value="{{ $tripStop->stop_order }}"
                                                       min="1"
                                                       class="w-24 px-3 py-2 rounded-lg border border-slate-300 text-sm font-semibold bg-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none"
                                                       required>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-slate-600 mb-1">Expected Arrival Time</label>
                                                <input type="time"
                                                       name="expected_time"
                                                       value="{{ \Carbon\Carbon::parse($tripStop->expected_time)->format('H:i') }}"
                                                       class="px-3 py-2 rounded-lg border border-slate-300 text-sm font-semibold bg-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none"
                                                       required>
                                            </div>

                                            <div class="flex gap-2">
                                                <button type="submit"
                                                        class="px-4 py-2 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold transition-colors">
                                                    <i class="fa-solid fa-check mr-1"></i> Save
                                                </button>
                                                <button type="button"
                                                        onclick="toggleEditRow({{ $tripStop->id }})"
                                                        class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-colors">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Route stops reference (all stops in correct order) --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-route text-violet-600"></i>
                Full Route Stop Order Reference
            </h3>
            @if ($routeStops->isEmpty())
                <p class="text-xs text-slate-400 italic">No stops have been defined on this route yet. <a href="{{ route('admin.routes.show', $trip->route_id) }}" class="text-violet-600 underline">Manage route stops →</a></p>
            @else
                <div class="flex flex-col gap-2">
                    @foreach ($routeStops as $rs)
                        @php $isAssigned = in_array($rs->stop_id, $assignedStopIds); @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full text-[10px] font-extrabold flex items-center justify-center shrink-0
                                {{ $isAssigned ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                {{ $rs->stop_order }}
                            </span>
                            <span class="text-sm font-semibold {{ $isAssigned ? 'text-emerald-700' : 'text-slate-600' }}">
                                {{ $rs->stop->name ?? '—' }}
                            </span>
                            @if ($isAssigned)
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    <i class="fa-solid fa-check mr-1"></i>Assigned
                                </span>
                            @endif
                        </div>
                        @if (!$loop->last)
                            <div class="ml-3 w-px h-3 bg-slate-200"></div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ─── RIGHT: Add Stop Form ─── --}}
    <div class="space-y-6">

        {{-- Add Stop Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-plus text-violet-600"></i> Add Stop to Trip
            </h3>

            @if ($availableRouteStops->isEmpty())
                <div class="p-4 bg-slate-50 rounded-xl text-center">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-2"></i>
                    <p class="text-sm font-semibold text-slate-700">All route stops assigned!</p>
                    <p class="text-xs text-slate-400 mt-1">Every stop on this route has been added to the trip.</p>
                </div>
            @else
                <form action="{{ route('admin.trips.stops.store', $trip->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="stop_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Select Stop <span class="text-rose-500">*</span>
                        </label>
                        <select id="stop_id"
                                name="stop_id"
                                required
                                class="w-full px-3 py-2.5 rounded-xl border @error('stop_id') border-rose-400 @else border-slate-300 @enderror bg-slate-50 text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                            <option value="">— Choose a stop —</option>
                            @foreach ($availableRouteStops as $rs)
                                <option value="{{ $rs->stop_id }}" {{ old('stop_id') == $rs->stop_id ? 'selected' : '' }}>
                                    #{{ $rs->stop_order }} — {{ $rs->stop->name ?? 'Stop ' . $rs->stop_id }}
                                    @if($rs->stop?->location) ({{ $rs->stop->location }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('stop_id')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expected_time" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Expected Arrival Time <span class="text-rose-500">*</span>
                        </label>
                        <input type="time"
                               id="expected_time"
                               name="expected_time"
                               value="{{ old('expected_time') }}"
                               required
                               class="w-full px-3 py-2.5 rounded-xl border @error('expected_time') border-rose-400 @else border-slate-300 @enderror bg-slate-50 text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                        <p class="mt-1 text-[11px] text-slate-400">Set the expected arrival time at this stop.</p>
                        @error('expected_time')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm transition-all shadow-md shadow-violet-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i> Add Stop
                    </button>
                </form>
            @endif
        </div>

        {{-- Trip Info Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Trip Info</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Trip #</span>
                    <span class="font-bold text-slate-900">#{{ $trip->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Date</span>
                    <span class="font-bold text-slate-900">{{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('M d, Y') : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Departure</span>
                    <span class="font-bold text-violet-600">{{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Route</span>
                    <span class="font-bold text-slate-900">{{ $trip->route->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Driver</span>
                    <span class="font-bold text-slate-900">{{ $trip->driver->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status</span>
                    @php $st = strtolower($trip->status); @endphp
                    @if($st === 'scheduled')
                        <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 text-xs font-bold">Scheduled</span>
                    @elseif($st === 'completed')
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">Completed</span>
                    @elseif($st === 'cancelled')
                        <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">Cancelled</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">{{ ucfirst($trip->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.trips.show', $trip->id) }}"
                   class="w-full py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-eye"></i> View Full Trip Details
                </a>
            </div>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<script>
    function toggleEditRow(tripStopId) {
        const editRow = document.getElementById('edit-row-' + tripStopId);
        editRow.classList.toggle('hidden');
    }
</script>
@endsection
