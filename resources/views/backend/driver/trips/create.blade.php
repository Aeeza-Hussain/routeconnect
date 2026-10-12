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
            Publish a scheduled commercial journey for passenger seat search and booking.
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
    <div class="max-w-3xl">
        <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl">
            <form method="POST" action="{{ route('driver.trips.store') }}" class="space-y-6">
                @csrf

                {{-- 1. Vehicle Selection (Approved Vehicles Only) --}}
                <div>
                    <label for="vehicle_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Select Approved Vehicle <span class="text-rose-400">*</span>
                    </label>
                    <select id="vehicle_id"
                            name="vehicle_id"
                            required
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
                    <p class="text-[11px] text-slate-400 mt-1.5">Only approved commercial vehicles belonging to your profile are eligible.</p>
                </div>

                {{-- 2. Route Selection --}}
                <div>
                    <label for="route_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Service Route <span class="text-rose-400">*</span>
                    </label>
                    <select id="route_id"
                            name="route_id"
                            required
                            class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                        <option value="">Select official travel route</option>
                        @foreach ($routes as $route)
                            <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->name }} ({{ $route->start_location ?? 'Origin' }} → {{ $route->end_location ?? 'Destination' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Date & Time Row --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Trip Date --}}
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
                </div>

                {{-- 4. Expected Arrival Time --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
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
                        <p class="text-[11px] text-slate-400 mt-1">Expected destination arrival. Supports overnight trips crossing midnight.</p>
                    </div>

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
                </div>

                {{-- 5. Seat Capacity & Pickup Point --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Trip Capacity --}}
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
                        <p class="text-[11px] text-slate-400 mt-1">Seats available for this scheduled trip (cannot exceed vehicle total capacity).</p>
                    </div>

                    {{-- Pickup / Boarding Point --}}
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
                </div>

                {{-- 6. Trip Notes --}}
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Trip Notes & Passenger Information (Optional)
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="2"
                              placeholder="e.g. AC service, luggage space available, punctuality required."
                              class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">{{ old('notes') }}</textarea>
                </div>

                {{-- Schedule Overlap Notice --}}
                <div class="p-4 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-xs text-slate-300 flex items-start gap-3">
                    <i class="fa-solid fa-clock text-teal-400 text-lg mt-0.5 shrink-0"></i>
                    <div class="space-y-1">
                        <strong class="text-white block font-bold">Schedule Overlap Protection</strong>
                        <p class="text-slate-400 leading-relaxed">
                            The system automatically verifies that you do not have any overlapping trips scheduled during this departure and arrival window, even across different vehicles you own.
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

@endsection
