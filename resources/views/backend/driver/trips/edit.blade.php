@extends('backend.driver.layouts.app')

@section('title', 'Edit Trip #' . $trip->id . ' — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-pen"></i> Operational Update
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
            Edit Trip #{{ $trip->id }}
        </h2>
        <p class="text-sm text-slate-400 mt-1">
            Update real-time journey status, departure & arrival timing, intermediate stop timings, and available seats.
        </p>
    </div>
    <div class="flex items-center gap-2.5">
        <a href="{{ route('driver.trips.show', $trip->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-eye"></i> View Trip
        </a>
        <a href="{{ route('driver.trips') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> My Trips
        </a>
    </div>
</div>

{{-- ─── Error Messages ─── --}}
@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-950/40 border border-rose-800 text-rose-300 text-xs font-semibold space-y-1">
        <div class="font-bold flex items-center gap-2 mb-1 text-sm text-rose-400">
            <i class="fa-solid fa-triangle-exclamation"></i> Please correct the following errors:
        </div>
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

@if ($trip->isCancelled() || $trip->isCompleted())
    <div class="mb-6 p-5 rounded-2xl bg-slate-900 border border-slate-800 text-slate-300 text-xs flex items-center gap-3">
        <i class="fa-solid fa-lock text-amber-400 text-lg shrink-0"></i>
        <div>
            <strong class="text-white block font-bold">This trip is {{ strtoupper($trip->status) }}</strong>
            <p class="text-slate-400 mt-0.5">Trips marked completed or cancelled can no longer be edited.</p>
        </div>
    </div>
@endif

<div class="max-w-3xl space-y-6">

    {{-- Route Summary Reference --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-map text-teal-400"></i> Route & Vehicle Reference (Locked)
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Route</span>
                <span class="font-bold text-white">{{ $trip->route->origin ?? 'Origin' }} → {{ $trip->route->destination ?? 'Destination' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Trip Date</span>
                <span class="font-bold text-white">{{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') : '—' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Vehicle</span>
                <span class="font-mono font-bold text-teal-400">{{ $trip->vehicle->registration_no ?? 'No Vehicle' }}</span>
                <span class="text-slate-500 text-[10px]"> (Max {{ $trip->vehicle->total_seats ?? 0 }} seats)</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Active Bookings</span>
                @php $bookedCount = $trip->bookings->whereNotIn('status', ['cancelled'])->sum('seats'); @endphp
                <span class="font-mono font-bold text-emerald-400">{{ $bookedCount }} Seats Booked</span>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 sm:p-8">
        <form action="{{ route('driver.trips.update', $trip->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Trip Status <span class="text-rose-400">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                        <option value="scheduled" {{ old('status', strtolower($trip->status)) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="boarding"  {{ old('status', strtolower($trip->status)) === 'boarding'  ? 'selected' : '' }}>Boarding</option>
                        <option value="departed"  {{ old('status', strtolower($trip->status)) === 'departed'  ? 'selected' : '' }}>Departed</option>
                        <option value="delayed"   {{ old('status', strtolower($trip->status)) === 'delayed'   ? 'selected' : '' }}>Delayed</option>
                        <option value="completed" {{ old('status', strtolower($trip->status)) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', strtolower($trip->status)) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <span class="text-[11px] text-slate-500 mt-1 block">Updating status informs passengers and updates the live tracker.</span>
                </div>

                {{-- Available Seats --}}
                <div>
                    <label for="available_seats" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Available Seats <span class="text-rose-400">*</span>
                    </label>
                    <input type="number"
                           name="available_seats"
                           id="available_seats"
                           min="0"
                           max="{{ $trip->vehicle ? $trip->vehicle->total_seats : 100 }}"
                           required
                           value="{{ old('available_seats', $trip->available_seats) }}"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                    <span class="text-[11px] text-slate-500 mt-1 block">Remaining open seats available for passenger booking.</span>
                </div>

                {{-- Departure Time --}}
                <div>
                    <label for="departure_time" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Departure Time <span class="text-rose-400">*</span>
                    </label>
                    <input type="time"
                           name="departure_time"
                           id="departure_time"
                           required
                           value="{{ old('departure_time', \Carbon\Carbon::parse($trip->departure_time)->format('H:i')) }}"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-teal-500">
                    <span class="text-[11px] text-slate-500 mt-1 block">Scheduled departure time from starting terminal.</span>
                </div>

                {{-- Expected Destination Arrival Time --}}
                <div>
                    <label for="expected_arrival_time" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Expected Destination Arrival Time
                    </label>
                    <input type="time"
                           name="expected_arrival_time"
                           id="expected_arrival_time"
                           value="{{ old('expected_arrival_time', $trip->expected_arrival_time ? \Carbon\Carbon::parse($trip->expected_arrival_time)->format('H:i') : '') }}"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-teal-500">
                    <span class="text-[11px] text-slate-500 mt-1 block">Estimated destination arrival (supports midnight crossings).</span>
                </div>

                {{-- Fare Per Seat --}}
                <div>
                    <label for="fare" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Fare Per Passenger Seat (Rs.)
                    </label>
                    <input type="number"
                           step="1"
                           min="0"
                           name="fare"
                           id="fare"
                           value="{{ old('fare', (int) $trip->fare) }}"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-teal-500">
                </div>

                {{-- Pickup Point --}}
                <div>
                    <label for="pickup_point" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Pickup Point / Terminal
                    </label>
                    <input type="text"
                           name="pickup_point"
                           id="pickup_point"
                           value="{{ old('pickup_point', $trip->pickup_point) }}"
                           placeholder="e.g. Gilgit General Bus Stand, Bay #4"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                </div>

            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Trip Notes & Passenger Information
                </label>
                <input type="text"
                       name="notes"
                       id="notes"
                       value="{{ old('notes', $trip->notes) }}"
                       placeholder="e.g. AC service, luggage space available, punctuality required"
                       class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
            </div>

            {{-- Intermediate Stops Timings (if configured) --}}
            @if ($trip->tripStops && $trip->tripStops->isNotEmpty())
                <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-400 flex items-center gap-2">
                            <i class="fa-solid fa-clock"></i> Intermediate Stop Expected Timings
                        </span>
                        <span class="text-[10px] text-slate-500">Waypoints</span>
                    </div>

                    <div class="space-y-2">
                        @foreach ($trip->tripStops as $index => $ts)
                            <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg bg-slate-950/60 border border-slate-800 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-teal-500/10 text-teal-400 text-[10px] font-bold">
                                        #{{ $ts->stop_order }}
                                    </span>
                                    <span class="font-bold text-white">{{ $ts->stop->name ?? 'Stop' }}</span>
                                </div>
                                <input type="hidden" name="stops[{{ $index }}][id]" value="{{ $ts->id }}">
                                <div class="w-36">
                                    <input type="time"
                                           name="stops[{{ $index }}][expected_time]"
                                           value="{{ $ts->expected_time ? \Carbon\Carbon::parse($ts->expected_time)->format('H:i') : '' }}"
                                           class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs focus:outline-none focus:border-teal-400">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                <a href="{{ route('driver.trips.show', $trip->id) }}" class="text-xs text-slate-400 hover:text-white font-bold">
                    Cancel
                </a>
                <button type="submit"
                        {{ ($trip->isCancelled() || $trip->isCompleted()) ? 'disabled' : '' }}
                        class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 disabled:opacity-50 text-white text-xs font-bold transition-all shadow-lg shadow-teal-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Changes</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
