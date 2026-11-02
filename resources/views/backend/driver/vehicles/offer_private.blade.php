@extends('backend.driver.layouts.app')

@section('title', 'Offer a Private Vehicle — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-car-side"></i> Private Vehicle Offer
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Offer a Private Vehicle</h2>
        <p class="text-sm text-slate-400 mt-1">
            Publish whole-vehicle rental availability for custom passenger bookings, tourism, or charter journeys.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('driver.vehicles.private') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-list-check"></i> Manage Private Listings
        </a>
        <a href="{{ route('driver.trips.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-users"></i> Offer a Shared Ride
        </a>
    </div>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs sm:text-sm space-y-1">
        <div class="flex items-center gap-2 font-bold text-rose-200">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside pl-1 space-y-0.5 text-xs">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($vehicles->isEmpty())
    {{-- Warning: No approved vehicles --}}
    <div class="max-w-2xl bg-amber-950/40 border border-amber-800/60 rounded-3xl p-8 text-center space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-3xl mx-auto border border-amber-500/20">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-lg font-extrabold text-white">No Approved Vehicle Available</h3>
        <p class="text-xs text-amber-200/80 max-w-md mx-auto leading-relaxed">
            You must have at least one <strong>approved vehicle</strong> before you can publish private vehicle availability.
        </p>
        <div class="pt-2 flex items-center justify-center gap-3">
            <a href="{{ route('driver.vehicles.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-teal-500/20">
                <i class="fa-solid fa-square-plus"></i> Register a Vehicle
            </a>
            <a href="{{ route('driver.vehicles.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700">
                <span>View Vehicle Fleet</span>
            </a>
        </div>
    </div>
@else
    <div class="max-w-4xl">
        <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl">
            <form method="POST" action="{{ route('driver.vehicles.offer_private.store') }}" class="space-y-6">
                @csrf

                {{-- 1. Select Approved Vehicle --}}
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
                    <label for="vehicle_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Select Approved Vehicle <span class="text-rose-400">*</span>
                    </label>
                    <select id="vehicle_id"
                            name="vehicle_id"
                            required
                            onchange="onVehicleChanged(this)"
                            class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 font-medium">
                        <option value="">-- Choose an approved vehicle from your fleet --</option>
                        @foreach ($vehicles as $veh)
                            <option value="{{ $veh->id }}"
                                    data-daily="{{ $veh->rental_rate_per_day ?: 4000 }}"
                                    data-hourly="{{ $veh->rental_rate_per_hour ?: '' }}"
                                    data-pickup="{{ $veh->pickup_location ?: '' }}"
                                    data-service="{{ $veh->service_area ?: '' }}"
                                    data-from="{{ $veh->available_from ? $veh->available_from->format('Y-m-d\TH:i') : '' }}"
                                    data-to="{{ $veh->available_to ? $veh->available_to->format('Y-m-d\TH:i') : '' }}"
                                    data-rental="{{ $veh->is_available_for_rental ? '1' : '0' }}"
                                    data-desc="{{ $veh->description ?: '' }}"
                                    {{ (old('vehicle_id', request('vehicle_id')) == $veh->id) || ($selectedVehicle && $selectedVehicle->id == $veh->id) ? 'selected' : '' }}>
                                {{ $veh->registration_no }} — {{ $veh->model }} ({{ $veh->type }} · {{ $veh->total_seats }} Seats)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400">
                        Only verified commercial vehicles approved by platform administrators can be published.
                    </p>
                </div>

                {{-- 2. Rental Pricing (Daily & Hourly) --}}
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-teal-400 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave"></i> Rental Pricing
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rental_rate_per_day" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Daily Rental Rate (PKR) <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-xs text-slate-500 font-bold font-mono">PKR</span>
                                <input type="number"
                                       id="rental_rate_per_day"
                                       name="rental_rate_per_day"
                                       value="{{ old('rental_rate_per_day', $selectedVehicle?->rental_rate_per_day ?: 4000) }}"
                                       min="500"
                                       step="50"
                                       required
                                       placeholder="4000"
                                       class="w-full pl-14 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono font-bold text-sm focus:outline-none focus:border-teal-400">
                            </div>
                            <span class="text-[10px] text-slate-500 mt-1 block">Full 24-hour block price.</span>
                        </div>

                        <div>
                            <label for="rental_rate_per_hour" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Hourly Rental Rate (PKR) <span class="text-slate-500 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-xs text-slate-500 font-bold font-mono">PKR</span>
                                <input type="number"
                                       id="rental_rate_per_hour"
                                       name="rental_rate_per_hour"
                                       value="{{ old('rental_rate_per_hour', $selectedVehicle?->rental_rate_per_hour) }}"
                                       min="50"
                                       step="25"
                                       placeholder="500"
                                       class="w-full pl-14 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono font-bold text-sm focus:outline-none focus:border-teal-400">
                            </div>
                            <span class="text-[10px] text-slate-500 mt-1 block">Applied for short duration city trips under 24 hours.</span>
                        </div>
                    </div>
                </div>

                {{-- 3. Pickup Location & Service Areas --}}
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-teal-400 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot"></i> Service & Coverage Areas
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pickup_location" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Primary Pickup Location / Base
                            </label>
                            <input type="text"
                                   id="pickup_location"
                                   name="pickup_location"
                                   value="{{ old('pickup_location', $selectedVehicle?->pickup_location) }}"
                                   placeholder="e.g. Gilgit Airport / Serena Hotel"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-teal-400">
                            <span class="text-[10px] text-slate-500 mt-1 block">Where passengers can board or expect pickup.</span>
                        </div>

                        <div>
                            <label for="service_area" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Service Coverage Areas
                            </label>
                            <input type="text"
                                   id="service_area"
                                   name="service_area"
                                   value="{{ old('service_area', $selectedVehicle?->service_area) }}"
                                   placeholder="e.g. Gilgit, Hunza, Skardu, Nagar"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-teal-400">
                            <span class="text-[10px] text-slate-500 mt-1 block">Cities, districts, or valleys eligible for rental.</span>
                        </div>
                    </div>
                </div>

                {{-- 4. Active Availability Date/Time Ranges (Optional bounds) --}}
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-teal-400 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-days"></i> Availability Window <span class="text-slate-500 font-normal">(Optional)</span>
                        </h4>
                        <span class="text-[11px] text-slate-400">Leave blank for open-ended ongoing availability</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="available_from" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Available From
                            </label>
                            <input type="datetime-local"
                                   id="available_from"
                                   name="available_from"
                                   value="{{ old('available_from', $selectedVehicle?->available_from ? $selectedVehicle->available_from->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-teal-400">
                        </div>

                        <div>
                            <label for="available_to" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Available Until
                            </label>
                            <input type="datetime-local"
                                   id="available_to"
                                   name="available_to"
                                   value="{{ old('available_to', $selectedVehicle?->available_to ? $selectedVehicle->available_to->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-teal-400">
                        </div>
                    </div>
                </div>

                {{-- 5. Description & Amenities --}}
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Vehicle Amenities & Rental Notes <span class="text-slate-500 font-normal">(Optional)</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="e.g. 4x4 off-road capable, dual AC, large luggage carrier, high-altitude heating, sound system..."
                              class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-teal-400">{{ old('description', $selectedVehicle?->description) }}</textarea>
                </div>

                {{-- 6. Publishing Toggle --}}
                <div class="p-4 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <input type="checkbox"
                               id="is_available_for_rental"
                               name="is_available_for_rental"
                               value="1"
                               {{ old('is_available_for_rental', $selectedVehicle ? $selectedVehicle->is_available_for_rental : true) ? 'checked' : '' }}
                               class="w-5 h-5 rounded text-teal-500 bg-slate-900 border-slate-700 focus:ring-teal-500">
                        <div>
                            <label for="is_available_for_rental" class="text-xs font-extrabold text-white cursor-pointer block">
                                Publish Listing Immediately
                            </label>
                            <span class="text-[11px] text-teal-300/80">
                                When enabled, passengers can discover this vehicle in the "Find a Ride" marketplace and request bookings.
                            </span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/30 shrink-0">
                        <i class="fa-solid fa-globe text-[9px]"></i> Public Marketplace
                    </span>
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('driver.vehicles.private') }}"
                       class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-teal-500/20 flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Publish Private Vehicle Listing</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
@endif

<script>
function onVehicleChanged(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    if (opt.dataset.daily) {
        document.getElementById('rental_rate_per_day').value = opt.dataset.daily;
    }
    if (opt.dataset.hourly) {
        document.getElementById('rental_rate_per_hour').value = opt.dataset.hourly;
    }
    if (opt.dataset.pickup) {
        document.getElementById('pickup_location').value = opt.dataset.pickup;
    }
    if (opt.dataset.service) {
        document.getElementById('service_area').value = opt.dataset.service;
    }
    if (opt.dataset.from) {
        document.getElementById('available_from').value = opt.dataset.from;
    }
    if (opt.dataset.to) {
        document.getElementById('available_to').value = opt.dataset.to;
    }
    if (opt.dataset.desc) {
        document.getElementById('description').value = opt.dataset.desc;
    }
    if (opt.dataset.rental !== undefined) {
        document.getElementById('is_available_for_rental').checked = opt.dataset.rental === '1';
    }
}
</script>

@endsection

