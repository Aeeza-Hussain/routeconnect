@extends('backend.driver.layouts.app')

@section('title', 'Schedule a Trip — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-calendar-plus"></i> Trip Operations
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Schedule a New Trip</h2>
        <p class="text-sm text-slate-400 mt-1">
            Publish a scheduled commercial journey with origin, intermediate stops, timings, and seat pricing.
        </p>
    </div>
    <a href="{{ route('driver.trips') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> My Scheduled Trips
    </a>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs sm:text-sm space-y-1">
        <div class="flex items-center gap-2 font-bold text-rose-200">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>Please resolve the following:</span>
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
            You must have at least one <strong>approved vehicle</strong> before you can schedule public trips. Newly registered vehicles require administrator verification.
        </p>
        <div class="pt-2 flex items-center justify-center gap-3">
            <a href="{{ route('driver.vehicles.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-teal-500/20">
                <i class="fa-solid fa-square-plus"></i> Register a Vehicle
            </a>
            <a href="{{ route('driver.vehicles.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700">
                <span>View Vehicle Status</span>
            </a>
        </div>
    </div>
@else
    <div class="max-w-4xl">
        <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl">
            <form method="POST" action="{{ route('driver.trips.store') }}" class="space-y-6" id="scheduleTripForm">
                @csrf

                {{-- 1. Vehicle Selection (Approved Vehicles Only) --}}
                <div>
                    <label for="vehicle_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Select Approved Vehicle <span class="text-rose-400">*</span>
                    </label>
                    <select id="vehicle_id"
                            name="vehicle_id"
                            required
                            onchange="updateVehicleCapacity(this)"
                            class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                        <option value="">Choose an approved vehicle from your fleet</option>
                        @foreach ($vehicles as $veh)
                            <option value="{{ $veh->id }}"
                                    data-seats="{{ $veh->total_seats }}"
                                    {{ old('vehicle_id', request('vehicle_id')) == $veh->id ? 'selected' : '' }}>
                                {{ $veh->registration_no }} — {{ $veh->model }} ({{ $veh->type }} · Max {{ $veh->total_seats }} Seats)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5" id="vehicleCapacityHint">
                        Only verified & approved vehicles belonging to your profile are eligible.
                    </p>
                </div>

                {{-- 2. Route & Origin / Destination --}}
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-teal-400 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot"></i> Route, Origin & Destination
                        </h4>
                        <span class="text-[11px] text-slate-400">Pick an active route or enter custom points</span>
                    </div>

                    {{-- Pre-configured route selector --}}
                    <div>
                        <label for="route_id" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                            Active Service Route
                        </label>
                        <select id="route_id"
                                name="route_id"
                                onchange="onRouteSelected(this)"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-teal-400">
                            <option value="">-- Select Pre-configured Route (or specify custom points below) --</option>
                            @foreach ($routes as $r)
                                <option value="{{ $r->id }}"
                                        data-origin="{{ $r->start_location ?? $r->startStop?->name }}"
                                        data-destination="{{ $r->end_location ?? $r->endStop?->name }}"
                                        data-stops='@json($r->routeStops->map(fn($rs) => ["stop_id" => $rs->stop_id, "name" => $rs->stop?->name, "stop_order" => $rs->stop_order]))'
                                        {{ old('route_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} ({{ $r->start_location ?? $r->startStop?->name ?? 'Origin' }} → {{ $r->end_location ?? $r->endStop?->name ?? 'Destination' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Origin & Destination Inputs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="origin" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Origin / Starting City <span class="text-rose-400">*</span>
                            </label>
                            <input type="text"
                                   id="origin"
                                   name="origin"
                                   value="{{ old('origin') }}"
                                   placeholder="e.g. Gilgit"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-teal-400">
                        </div>

                        <div>
                            <label for="destination" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                Destination / Final City <span class="text-rose-400">*</span>
                            </label>
                            <input type="text"
                                   id="destination"
                                   name="destination"
                                   value="{{ old('destination') }}"
                                   placeholder="e.g. Hunza"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-teal-400">
                        </div>
                    </div>
                </div>

                {{-- 3. Date, Departure & Expected Arrival Timings --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Departure Date --}}
                    <div>
                        <label for="trip_date" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Departure Date <span class="text-rose-400">*</span>
                        </label>
                        <input type="date"
                               id="trip_date"
                               name="trip_date"
                               value="{{ old('trip_date', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                    </div>

                    {{-- Departure Time --}}
                    <div>
                        <label for="departure_time" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Departure Time <span class="text-rose-400">*</span>
                        </label>
                        <input type="time"
                               id="departure_time"
                               name="departure_time"
                               value="{{ old('departure_time', '09:00') }}"
                               required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                    </div>

                    {{-- Expected Arrival Time at Destination --}}
                    <div>
                        <label for="expected_arrival_time" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Expected Arrival Time
                        </label>
                        <input type="time"
                               id="expected_arrival_time"
                               name="expected_arrival_time"
                               value="{{ old('expected_arrival_time') }}"
                               placeholder="13:00"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                        <p class="text-[10px] text-slate-500 mt-1">Arrival at destination. Overnight trips crossing midnight are supported.</p>
                    </div>
                </div>

                {{-- 4. Ordered Intermediate Stops & Expected Arrival Times --}}
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-teal-400 flex items-center gap-2">
                                <i class="fa-solid fa-signs-post"></i> Ordered Intermediate Stops
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Add intermediate waypoints in travel sequence with their expected arrival times.
                            </p>
                        </div>
                        <button type="button"
                                onclick="addIntermediateStopRow()"
                                class="shrink-0 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-teal-400 hover:text-teal-300 text-xs font-bold border border-slate-700 flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Add Stop</span>
                        </button>
                    </div>

                    {{-- Container for stops rows --}}
                    <div id="intermediateStopsContainer" class="space-y-3">
                        <div id="noStopsPlaceholder" class="p-4 rounded-xl bg-slate-950/40 border border-slate-800 text-center text-xs text-slate-500">
                            No intermediate stops added yet. Click <strong>"Add Stop"</strong> above or select a pre-configured route.
                        </div>
                    </div>
                </div>

                {{-- 5. Fare & Capacity --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Fare per seat --}}
                    <div>
                        <label for="fare" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Fare per Passenger Seat (Rs.) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 font-mono text-xs">
                                Rs.
                            </span>
                            <input type="number"
                                   id="fare"
                                   name="fare"
                                   value="{{ old('fare', 800) }}"
                                   min="0"
                                   step="10"
                                   required
                                   placeholder="800"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                        </div>
                    </div>

                    {{-- Trip-specific Passenger Capacity --}}
                    <div>
                        <label for="available_seats" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Trip Passenger Seats <span class="text-rose-400">*</span>
                        </label>
                        <input type="number"
                               id="available_seats"
                               name="available_seats"
                               value="{{ old('available_seats', 10) }}"
                               min="1"
                               max="100"
                               required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                        <p class="text-[11px] text-slate-400 mt-1" id="seatsCapInfo">
                            Seats available for this scheduled trip (cannot exceed vehicle total capacity).
                        </p>
                    </div>
                </div>

                {{-- 6. Pickup Point & Notes --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="pickup_point" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Boarding Terminal / Pickup Point
                        </label>
                        <input type="text"
                               id="pickup_point"
                               name="pickup_point"
                               value="{{ old('pickup_point', 'Main Bus Terminal') }}"
                               placeholder="e.g. Gilgit Main Terminal, Danyor Chowk"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Trip Notes & Information (Optional)
                        </label>
                        <input type="text"
                               id="notes"
                               name="notes"
                               value="{{ old('notes') }}"
                               placeholder="e.g. AC service, luggage space, punctuality required"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                    </div>
                </div>

                {{-- Schedule Overlap Notice --}}
                <div class="p-4 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-xs text-slate-300 flex items-start gap-3">
                    <i class="fa-solid fa-shield-halved text-teal-400 text-lg mt-0.5 shrink-0"></i>
                    <div class="space-y-1">
                        <strong class="text-white block font-bold">Schedule Overlap Validation</strong>
                        <p class="text-slate-400 leading-relaxed">
                            Drivers with multiple vehicles cannot be assigned to overlapping journeys. The system checks departures and destination arrivals (including midnight crossing trips) on the server.
                        </p>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3.5 px-6 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-sm transition-all shadow-lg shadow-teal-500/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Publish Scheduled Trip</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- Dynamic JavaScript for Vehicle Capacity & Stops --}}
<script>
    let stopCounter = 0;

    function updateVehicleCapacity(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        const seatsInput = document.getElementById('available_seats');
        const hint = document.getElementById('vehicleCapacityHint');
        const capInfo = document.getElementById('seatsCapInfo');

        if (option && option.getAttribute('data-seats')) {
            const maxSeats = parseInt(option.getAttribute('data-seats'), 10);
            seatsInput.max = maxSeats;
            if (parseInt(seatsInput.value, 10) > maxSeats || !seatsInput.value) {
                seatsInput.value = maxSeats;
            }
            hint.textContent = `Selected vehicle maximum capacity: ${maxSeats} seats.`;
            capInfo.textContent = `Cannot exceed ${maxSeats} seats for this vehicle.`;
        }
    }

    function onRouteSelected(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        if (!option || !option.value) return;

        const origin = option.getAttribute('data-origin');
        const destination = option.getAttribute('data-destination');

        if (origin) document.getElementById('origin').value = origin;
        if (destination) document.getElementById('destination').value = destination;

        const rawStops = option.getAttribute('data-stops');
        if (rawStops) {
            try {
                const stops = JSON.parse(rawStops);
                const container = document.getElementById('intermediateStopsContainer');
                container.innerHTML = '';
                stopCounter = 0;

                if (stops.length === 0) {
                    container.innerHTML = `
                        <div id="noStopsPlaceholder" class="p-4 rounded-xl bg-slate-950/40 border border-slate-800 text-center text-xs text-slate-500">
                            Direct route without pre-configured intermediate stops. Click <strong>"Add Stop"</strong> to add any.
                        </div>
                    `;
                } else {
                    stops.forEach((st, idx) => {
                        addIntermediateStopRow(st.name, st.stop_id, idx + 1);
                    });
                }
            } catch (e) {
                console.error('Error parsing route stops JSON', e);
            }
        }
    }

    function addIntermediateStopRow(name = '', stopId = '', order = null) {
        const container = document.getElementById('intermediateStopsContainer');
        const placeholder = document.getElementById('noStopsPlaceholder');
        if (placeholder) placeholder.remove();

        stopCounter++;
        const currentOrder = order !== null ? order : stopCounter;

        const row = document.createElement('div');
        row.className = 'flex flex-col sm:flex-row items-center gap-3 p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs stop-row';
        row.id = `stop_row_${stopCounter}`;

        row.innerHTML = `
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="px-2.5 py-1 rounded-lg bg-teal-500/10 text-teal-400 font-extrabold text-[10px] border border-teal-500/20 shrink-0">
                    Stop #${currentOrder}
                </span>
                <input type="hidden" name="stops[${stopCounter}][stop_order]" value="${currentOrder}">
                <input type="hidden" name="stops[${stopCounter}][stop_id]" value="${stopId || ''}">
            </div>

            <div class="flex-1 w-full sm:w-auto">
                <input type="text"
                       name="stops[${stopCounter}][name]"
                       value="${name}"
                       placeholder="Stop name / waypoint (e.g. Aliabad, Nagar)"
                       required
                       class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-teal-400">
            </div>

            <div class="w-full sm:w-44">
                <div class="relative">
                    <input type="time"
                           name="stops[${stopCounter}][expected_time]"
                           placeholder="Arrival time"
                           class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs focus:outline-none focus:border-teal-400">
                </div>
            </div>

            <div class="shrink-0 w-full sm:w-auto text-right">
                <button type="button"
                        onclick="removeStopRow('${row.id}')"
                        class="p-2 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-colors">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;

        container.appendChild(row);
    }

    function removeStopRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) row.remove();

        const container = document.getElementById('intermediateStopsContainer');
        if (container.querySelectorAll('.stop-row').length === 0) {
            container.innerHTML = `
                <div id="noStopsPlaceholder" class="p-4 rounded-xl bg-slate-950/40 border border-slate-800 text-center text-xs text-slate-500">
                    No intermediate stops added yet. Click <strong>"Add Stop"</strong> above or select a pre-configured route.
                </div>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const vehicleSelect = document.getElementById('vehicle_id');
        if (vehicleSelect && vehicleSelect.value) {
            updateVehicleCapacity(vehicleSelect);
        }
        const routeSelect = document.getElementById('route_id');
        if (routeSelect && routeSelect.value && !document.getElementById('origin').value) {
            onRouteSelected(routeSelect);
        }
    });
</script>

@endsection
