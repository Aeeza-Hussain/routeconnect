@extends('backend.admin.layouts.app')

@section('title', 'Edit Trip #' . $trip->id . ' — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 text-violet-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-pen"></i> Edit Journey
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Edit Trip #{{ $trip->id }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            {{ $trip->route->name ?? 'Route' }} · {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('M d, Y') : '' }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.trips.show', $trip->id) }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-eye"></i> View Trip
        </a>
        <a href="{{ route('admin.trips.index') }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Trips
        </a>
    </div>
</div>

{{-- ─── Form Card ─── --}}
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST" class="p-6 space-y-6" id="tripForm">
            @csrf
            @method('PUT')

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold space-y-1">
                    <div class="font-extrabold text-sm mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:
                    </div>
                    <ul class="list-disc ml-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. Route Selection --}}
            <div>
                <label for="route_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Route <span class="text-rose-500">*</span>
                </label>
                <select name="route_id" id="route_id" required
                        class="w-full px-4 py-3 bg-slate-50 border @error('route_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="">-- Choose Route (Start → End) --</option>
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $trip->route_id) == $route->id ? 'selected' : '' }}>
                            {{ $route->name }} ({{ $route->start_location }} → {{ $route->end_location }})
                        </option>
                    @endforeach
                </select>
                @error('route_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- 2. Driver & Vehicle Selection --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Driver Selection (Approved Only) --}}
                <div>
                    <label for="user_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Assigned Driver <span class="text-rose-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required onchange="filterVehiclesByDriver(this.value)"
                            class="w-full px-4 py-3 bg-slate-50 border @error('user_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                        <option value="">-- Select Approved Driver --</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('user_id', $trip->user_id) == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }} ({{ $driver->phone ?? 'Driver #' . $driver->id }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-400">Only verified & approved drivers appear here.</p>
                    @error('user_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Vehicle Selection (Belonging to Driver) --}}
                <div>
                    <label for="vehicle_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Assigned Vehicle <span class="text-rose-500">*</span>
                    </label>
                    <select name="vehicle_id" id="vehicle_id" required onchange="handleVehicleChange()"
                            class="w-full px-4 py-3 bg-slate-50 border @error('vehicle_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                        <option value="">-- Select Vehicle --</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}"
                                    data-driver-id="{{ $vehicle->user_id }}"
                                    data-total-seats="{{ $vehicle->total_seats }}"
                                    {{ old('vehicle_id', $trip->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_no }} — {{ $vehicle->type }} ({{ $vehicle->total_seats }} seats)
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-400" id="vehicleHelp">Must belong to the selected driver.</p>
                    @error('vehicle_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- 3. Schedule & Seats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                {{-- Departure Date --}}
                <div>
                    <label for="trip_date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Departure Date <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $tripDateVal = old('trip_date', $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('Y-m-d') : '');
                    @endphp
                    <input type="date" id="trip_date" name="trip_date"
                           value="{{ $tripDateVal }}" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('trip_date') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    @error('trip_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Departure Time --}}
                <div>
                    <label for="departure_time" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Departure Time <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $depTimeVal = old('departure_time', $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('H:i') : '');
                    @endphp
                    <input type="time" id="departure_time" name="departure_time"
                           value="{{ $depTimeVal }}" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('departure_time') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    @error('departure_time')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Available Seats --}}
                <div>
                    <label for="available_seats" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Available Seats <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="available_seats" name="available_seats" min="1" max="{{ $trip->vehicle->total_seats ?? 100 }}"
                           value="{{ old('available_seats', $trip->available_seats) }}" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('available_seats') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <p class="mt-1 text-[11px] text-slate-400" id="seatsCapacityHint">
                        Max capacity: {{ $trip->vehicle->total_seats ?? '?' }} seats.
                    </p>
                    @error('available_seats')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- 4. Fare & Pickup Point --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="fare" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Ticket Fare (PKR)
                    </label>
                    <input type="number" step="0.01" min="0" id="fare" name="fare"
                           value="{{ old('fare', $trip->fare) }}" placeholder="e.g. 1500.00"
                           class="w-full px-4 py-3 bg-slate-50 border @error('fare') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <p class="mt-1 text-[11px] text-slate-400">Base seat fare for the trip</p>
                    @error('fare')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="pickup_point" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Default Pickup Point / Terminal
                    </label>
                    <input type="text" id="pickup_point" name="pickup_point"
                           value="{{ old('pickup_point', $trip->pickup_point) }}" placeholder="e.g. Gilgit Main Terminal, Bay 3"
                           class="w-full px-4 py-3 bg-slate-50 border @error('pickup_point') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <p class="mt-1 text-[11px] text-slate-400">Boarding location instructions</p>
                    @error('pickup_point')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- 5. Trip Status --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Trip Status <span class="text-rose-500">*</span>
                </label>
                @php $curStatus = ucfirst(strtolower(old('status', $trip->status))); @endphp
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all
                                  {{ $curStatus === 'Scheduled' ? 'border-sky-500 bg-sky-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Scheduled" class="sr-only"
                               {{ $curStatus === 'Scheduled' ? 'checked' : '' }}
                               onchange="updateStatusCardStyles(this)">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900">Scheduled</div>
                            <div class="text-[10px] text-slate-500">Ready for booking</div>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all
                                  {{ $curStatus === 'Completed' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Completed" class="sr-only"
                               {{ $curStatus === 'Completed' ? 'checked' : '' }}
                               onchange="updateStatusCardStyles(this)">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900">Completed</div>
                            <div class="text-[10px] text-slate-500">Journey arrived</div>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all
                                  {{ $curStatus === 'Cancelled' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Cancelled" class="sr-only"
                               {{ $curStatus === 'Cancelled' ? 'checked' : '' }}
                               onchange="updateStatusCardStyles(this)">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900">Cancelled</div>
                            <div class="text-[10px] text-slate-500">Trip suspended</div>
                        </div>
                    </label>
                </div>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.trips.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Trip
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Dynamic Filtering JavaScript --}}
<script>
    function filterVehiclesByDriver(driverId) {
        const vehicleSelect = document.getElementById('vehicle_id');
        const options = vehicleSelect.querySelectorAll('option');

        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = 'block';
                return;
            }
            const optDriver = opt.getAttribute('data-driver-id');
            if (!driverId || optDriver === driverId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
                if (opt.selected) opt.selected = false;
            }
        });

        handleVehicleChange();
    }

    function handleVehicleChange() {
        const vehicleSelect = document.getElementById('vehicle_id');
        const selected = vehicleSelect.options[vehicleSelect.selectedIndex];
        const seatsInput = document.getElementById('available_seats');
        const hint = document.getElementById('seatsCapacityHint');

        if (selected && selected.getAttribute('data-total-seats')) {
            const maxSeats = parseInt(selected.getAttribute('data-total-seats'), 10);
            seatsInput.max = maxSeats;
            hint.textContent = `Max capacity: ${maxSeats} seats based on selected vehicle.`;
            if (parseInt(seatsInput.value, 10) > maxSeats) {
                seatsInput.value = maxSeats;
            }
        }
    }

    function updateStatusCardStyles(input) {
        input.closest('.grid').querySelectorAll('label').forEach(lbl => {
            lbl.className = 'relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all border-slate-200 hover:border-slate-300 bg-slate-50';
        });

        const activeClassMap = {
            'Scheduled': 'border-sky-500 bg-sky-50',
            'Completed': 'border-emerald-500 bg-emerald-50',
            'Cancelled': 'border-rose-500 bg-rose-50'
        };

        const activeClass = activeClassMap[input.value] || 'border-violet-500 bg-violet-50';
        input.closest('label').className = `relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all ${activeClass}`;
    }

    // Run on page load with the current driver
    document.addEventListener('DOMContentLoaded', function () {
        const driverSelect = document.getElementById('user_id');
        if (driverSelect && driverSelect.value) {
            filterVehiclesByDriver(driverSelect.value);
        }
    });
</script>

@endsection
