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
            Update real-time journey status, departure timing, pickup terminal, and seat availability.
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

<div class="max-w-3xl space-y-6">

    {{-- Route Summary Reference --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-map text-teal-400"></i> Route & Vehicle Reference (Locked)
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
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
                        <option value="scheduled" {{ old('status', $trip->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="boarding"  {{ old('status', $trip->status) === 'boarding'  ? 'selected' : '' }}>Boarding</option>
                        <option value="departed"  {{ old('status', $trip->status) === 'departed'  ? 'selected' : '' }}>Departed</option>
                        <option value="delayed"   {{ old('status', $trip->status) === 'delayed'   ? 'selected' : '' }}>Delayed</option>
                        <option value="completed" {{ old('status', $trip->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $trip->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <span class="text-[11px] text-slate-500 mt-1 block">Updating status informs passengers and updates the live tracker.</span>
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
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                    <span class="text-[11px] text-slate-500 mt-1 block">Updated expected departure time from starting terminal.</span>
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
                    <span class="text-[11px] text-slate-500 mt-1 block">Specific departure bay, gate, or terminal location.</span>
                </div>

            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                <a href="{{ route('driver.trips.show', $trip->id) }}" class="text-xs text-slate-400 hover:text-white font-bold">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-all shadow-lg shadow-teal-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Changes</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
