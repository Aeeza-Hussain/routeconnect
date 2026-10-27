@extends('frontend.layouts.app')

@section('title', 'Book a Vehicle — RouteConnect')

@section('content')

{{-- ─── Hero Section with Heading & Mode Selector ─── --}}
<section class="bg-slate-950 text-white py-12 sm:py-16 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.2),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-black uppercase tracking-wider">
            <i class="fa-solid fa-car-side text-emerald-400"></i> Vehicle Booking Service
        </span>

        <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
            Book a Vehicle
        </h1>
        <p class="text-sm sm:text-lg text-slate-300 max-w-2xl mx-auto font-medium leading-relaxed">
            Find the right ride for your journey. Choose between booking individual seats on scheduled routes or reserving a private vehicle for your exclusive trip.
        </p>

        {{-- Dual Booking Mode Selectable Cards --}}
        <div class="max-w-2xl mx-auto pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            {{-- Mode B: Private Vehicle --}}
            <a href="{{ url('/book-vehicle?mode=private' . (request()->has('pickup') ? '&pickup=' . urlencode(request('pickup')) : '') . (request()->has('destination') ? '&destination=' . urlencode(request('destination')) : '')) }}"
               class="p-5 rounded-2xl border text-left transition-all duration-200 group relative {{ $mode === 'private' ? 'bg-gradient-to-br from-emerald-950/80 to-slate-900 border-emerald-500 shadow-xl shadow-emerald-950/40 ring-2 ring-emerald-500/50' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700 hover:bg-slate-900' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl {{ $mode === 'private' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-300 group-hover:text-emerald-400' }} flex items-center justify-center text-xl transition-colors">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    @if($mode === 'private')
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-wider">Active</span>
                    @endif
                </div>
                <h3 class="text-lg font-black text-white">Private Vehicle</h3>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Reserve the entire vehicle for days or hours. Flexible schedules and private transit.
                </p>
            </a>

            {{-- Mode A: Shared Ride --}}
            <a href="{{ url('/book-vehicle?mode=shared' . (request()->has('pickup') ? '&pickup=' . urlencode(request('pickup')) : '') . (request()->has('destination') ? '&destination=' . urlencode(request('destination')) : '')) }}"
               class="p-5 rounded-2xl border text-left transition-all duration-200 group relative {{ $mode === 'shared' ? 'bg-gradient-to-br from-emerald-950/80 to-slate-900 border-emerald-500 shadow-xl shadow-emerald-950/40 ring-2 ring-emerald-500/50' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700 hover:bg-slate-900' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl {{ $mode === 'shared' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-300 group-hover:text-emerald-400' }} flex items-center justify-center text-xl transition-colors">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    @if($mode === 'shared')
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-wider">Active</span>
                    @endif
                </div>
                <h3 class="text-lg font-black text-white">Shared Ride</h3>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Book individual passenger seats on scheduled fixed-route commercial trips.
                </p>
            </a>

        </div>

    </div>
</section>

{{-- ─── Search Form & Results Section ─── --}}
<section class="py-10 bg-slate-50 dark:bg-[#090e17] min-h-screen transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-300 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Search Form Box --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-sm transition-colors">
            
            <form method="GET" action="{{ url('/book-vehicle') }}" id="searchForm" class="space-y-6">
                <input type="hidden" name="mode" value="{{ $mode }}">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-2">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-emerald-600"></i>
                            <span>{{ $mode === 'private' ? 'Search Available Private Rentals' : 'Search Scheduled Shared Trips' }}</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Filter by journey route, dates, passenger capacity, and vehicle type.
                        </p>
                    </div>

                    {{-- Quick Date Shortcuts --}}
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-400 text-[11px] font-bold">Quick Dates:</span>
                        <button type="button" onclick="setDateShortcut(0)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 text-slate-700 dark:text-slate-300 font-bold transition-colors">Today</button>
                        <button type="button" onclick="setDateShortcut(1)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 text-slate-700 dark:text-slate-300 font-bold transition-colors">Tomorrow</button>
                        <button type="button" onclick="setDateShortcut(2)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 text-slate-700 dark:text-slate-300 font-bold transition-colors">+2 Days</button>
                    </div>
                </div>

                {{-- Input Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    {{-- 1. Pickup Location --}}
                    <div class="space-y-1.5">
                        <label for="pickup" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i> Pickup Location
                        </label>
                        <input type="text"
                               name="pickup"
                               id="pickup"
                               value="{{ $pickup }}"
                               placeholder="City, station or address"
                               list="stopSuggestions"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- 2. Destination --}}
                    <div class="space-y-1.5">
                        <label for="destination" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-flag-checkered text-emerald-600 mr-1"></i> Destination
                        </label>
                        <input type="text"
                               name="destination"
                               id="destination"
                               value="{{ $destination }}"
                               placeholder="Destination city or stop"
                               list="stopSuggestions"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- 3. Pickup Date --}}
                    <div class="space-y-1.5">
                        <label for="pickup_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            <i class="fa-regular fa-calendar text-emerald-600 mr-1"></i> Pickup Date
                        </label>
                        <input type="date"
                               name="pickup_date"
                               id="pickup_date"
                               value="{{ $pickupDate }}"
                               min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                               onchange="validateDateRange()"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- 4. Mode-Dependent: Return Date (Private) or Seats (Shared) --}}
                    @if($mode === 'private')
                        <div class="space-y-1.5">
                            <label for="return_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                <i class="fa-regular fa-calendar-check text-emerald-600 mr-1"></i> Return Date (Rental End)
                            </label>
                            <input type="date"
                                   name="return_date"
                                   id="return_date"
                                   value="{{ $returnDate }}"
                                   min="{{ $pickupDate ?: \Carbon\Carbon::today()->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    @else
                        <div class="space-y-1.5">
                            <label for="passengers" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-users text-emerald-600 mr-1"></i> Passengers / Seats
                            </label>
                            <select name="passengers"
                                    id="passengers"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $passengers == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ \Illuminate\Support\Str::plural('Seat', $i) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    @endif

                </div>

                {{-- Second Row: Vehicle Type Filter & Time Selection --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    
                    {{-- Vehicle Type --}}
                    <div class="space-y-1.5">
                        <label for="vehicle_type" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Vehicle Type Filter
                        </label>
                        <select name="vehicle_type"
                                id="vehicle_type"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">All Vehicle Types</option>
                            @foreach($vehicleTypes as $vt)
                                <option value="{{ $vt }}" {{ $vehicleType === $vt ? 'selected' : '' }}>{{ $vt }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($mode === 'private')
                        {{-- Pickup Time --}}
                        <div class="space-y-1.5">
                            <label for="pickup_time" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Pickup Time
                            </label>
                            <input type="time"
                                   name="pickup_time"
                                   id="pickup_time"
                                   value="{{ $pickupTime }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        {{-- Return Time --}}
                        <div class="space-y-1.5">
                            <label for="return_time" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Return Time
                            </label>
                            <input type="time"
                                   name="return_time"
                                   id="return_time"
                                   value="{{ $returnTime }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    @else
                        {{-- Passengers filter for shared mode --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Booking Mode
                            </label>
                            <div class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 font-bold flex items-center justify-between">
                                <span>Shared Seat Pass</span>
                                <span class="text-[10px] text-emerald-600 font-extrabold uppercase">Fixed Route</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Active Date
                            </label>
                            <div class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 font-bold">
                                {{ \Carbon\Carbon::parse($pickupDate)->format('D, d M Y') }}
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Action Row --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        @if($mode === 'private')
                            <span>Selected rental duration: <strong class="text-slate-900 dark:text-white font-mono">{{ $calculatedDays }} {{ \Illuminate\Support\Str::plural('day', $calculatedDays) }}</strong></span>
                        @else
                            <span>Available seats guaranteed via live seat-reservation locks.</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="{{ url('/book-vehicle?mode=' . $mode) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all">
                            Reset Filters
                        </a>
                        <button type="submit" id="search-vehicles-btn" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black transition-all shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Search Available Vehicles</span>
                        </button>
                    </div>
                </div>

            </form>

            {{-- Stop Autocomplete Datalist --}}
            <datalist id="stopSuggestions">
                @foreach($stops as $st)
                    <option value="{{ $st->city }} — {{ $st->name }}"></option>
                @endforeach
            </datalist>

        </div>

        {{-- ─── Search Results Section ─── --}}
        <div>
            
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-car-rear text-emerald-600"></i>
                        <span>
                            @if($mode === 'private')
                                Available Private Vehicles ({{ $privateVehicles->count() }})
                            @else
                                Matching Scheduled Trips ({{ $sharedTrips->count() }})
                            @endif
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        @if($mode === 'private')
                            Verified vehicles available from {{ $startDatetime ? $startDatetime->format('d M Y h:i A') : '' }} to {{ $endDatetime ? $endDatetime->format('d M Y h:i A') : '' }}.
                        @else
                            Scheduled departures for {{ \Carbon\Carbon::parse($pickupDate)->format('d M Y') }}.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Mode B: Private Vehicle Results Grid --}}
            @if($mode === 'private')
                @if($privateVehicles->isEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                            <i class="fa-solid fa-car-burst"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No Private Vehicles Available</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                                No approved private vehicles are free for the requested date/time range. Some vehicles may have scheduled commercial trips or driver blocked dates.
                            </p>
                        </div>
                        <div class="flex items-center justify-center gap-3 pt-2">
                            <a href="{{ url('/book-vehicle?mode=private') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 text-white text-xs font-bold">
                                Clear Date Filter
                            </a>
                            <a href="{{ url('/book-vehicle?mode=shared') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                Check Shared Trips
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($privateVehicles as $vehicle)
                            @php
                                $priceInfo = $vehicle->pricing;
                            @endphp
                            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between transition-all hover:shadow-lg hover:border-emerald-500/50 group">
                                
                                <div>
                                    {{-- Card Top Banner / Image --}}
                                    <div class="h-44 bg-gradient-to-br from-slate-800 to-slate-950 relative flex items-center justify-center overflow-hidden">
                                        @if($vehicle->image)
                                            <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="text-center space-y-2">
                                                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-2xl mx-auto">
                                                    @if(strtolower($vehicle->type) === 'car')
                                                        <i class="fa-solid fa-car"></i>
                                                    @elseif(strtolower($vehicle->type) === 'van' || strtolower($vehicle->type) === 'daba')
                                                        <i class="fa-solid fa-van-shuttle"></i>
                                                    @else
                                                        <i class="fa-solid fa-bus"></i>
                                                    @endif
                                                </div>
                                                <span class="text-[11px] font-mono text-slate-400 font-bold block">{{ $vehicle->registration_no }}</span>
                                            </div>
                                        @endif

                                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-slate-950/80 backdrop-blur border border-white/10 text-white text-[10px] font-extrabold uppercase tracking-wider">
                                            {{ $vehicle->type }}
                                        </span>

                                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-emerald-500/90 text-slate-950 text-[10px] font-black uppercase tracking-wider">
                                            <i class="fa-solid fa-circle-check text-[9px] mr-1"></i> Available
                                        </span>
                                    </div>

                                    {{-- Body Specs --}}
                                    <div class="p-5 space-y-4">
                                        <div>
                                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-emerald-600 transition-colors">
                                                {{ $vehicle->model }}
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Fleet Reg: <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $vehicle->registration_no }}</span>
                                            </p>
                                        </div>

                                        {{-- Driver & Specs Grid --}}
                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Capacity</span>
                                                <span class="font-black text-slate-900 dark:text-white mt-0.5 block">
                                                    <i class="fa-solid fa-users text-emerald-600 text-[10px] mr-1"></i>
                                                    {{ $vehicle->total_seats }} Seats
                                                </span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Commercial Driver</span>
                                                <span class="font-bold text-slate-900 dark:text-white mt-0.5 block truncate">
                                                    <i class="fa-solid fa-id-badge text-teal-500 text-[10px] mr-1"></i>
                                                    {{ $vehicle->driver?->name ?? 'Verified Driver' }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Rental Pricing Breakdown --}}
                                        <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800 space-y-1">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-slate-600 dark:text-slate-300 font-medium">Daily Rate:</span>
                                                <span class="font-mono font-bold text-slate-900 dark:text-white">Rs. {{ number_format($priceInfo['daily_rate'], 2) }}/day</span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs pt-1 border-t border-emerald-200/60 dark:border-emerald-800/60">
                                                <span class="text-emerald-900 dark:text-emerald-300 font-extrabold">Total ({{ $priceInfo['days'] }} {{ \Illuminate\Support\Str::plural('day', $priceInfo['days']) }}):</span>
                                                <span class="font-mono font-black text-emerald-700 dark:text-emerald-400 text-sm">Rs. {{ number_format($priceInfo['total_fare'], 2) }}</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- Card Footer Actions --}}
                                <div class="p-5 pt-0 flex items-center gap-2">
                                    <a href="{{ route('vehicles.book.show', $vehicle->id) }}?pickup_date={{ $pickupDate }}&pickup_time={{ $pickupTime }}&return_date={{ $returnDate }}&return_time={{ $returnTime }}&pickup={{ urlencode($pickup) }}&destination={{ urlencode($destination) }}"
                                       class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold text-center transition-colors">
                                        View Details
                                    </a>

                                    <button type="button"
                                            onclick="openReserveModal({{ $vehicle->id }}, '{{ addslashes($vehicle->model) }}', '{{ $vehicle->registration_no }}', {{ $priceInfo['days'] }}, {{ $priceInfo['daily_rate'] }}, {{ $priceInfo['total_fare'] }})"
                                            class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black text-center shadow-md shadow-emerald-600/20 transition-all">
                                        Book Now
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif

            {{-- Mode A: Shared Ride Results --}}
            @else
                @if($sharedTrips->isEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                            <i class="fa-solid fa-bus"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No Scheduled Trips Found</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                                No scheduled trips match your requested route, passenger count, or date. You can reserve an entire vehicle privately or search across all upcoming trips.
                            </p>
                        </div>
                        <div class="flex items-center justify-center gap-3 pt-2">
                            <a href="{{ url('/trips') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 text-white text-xs font-bold">
                                Browse All Public Trips
                            </a>
                            <a href="{{ url('/book-vehicle?mode=private') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                Switch to Private Rental
                            </a>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($sharedTrips as $trip)
                            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6 transition-all hover:shadow-md">
                                
                                {{-- Route & Journey --}}
                                <div class="space-y-3 flex-1">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] uppercase tracking-wider">
                                            Trip #{{ $trip->id }} · {{ $trip->vehicle?->type ?? 'Van' }}
                                        </span>
                                        <span class="text-xs text-slate-400 font-medium">
                                            {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('D, d M Y') : 'Scheduled' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3 text-lg font-black text-slate-900 dark:text-white">
                                        <span>{{ $trip->route?->origin ?? 'Origin' }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                                        <span>{{ $trip->route?->destination ?? 'Destination' }}</span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                                        <span class="flex items-center gap-1 font-mono text-emerald-700 dark:text-emerald-400 font-extrabold">
                                            <i class="fa-regular fa-clock"></i>
                                            Departure: {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}
                                        </span>
                                        @if($trip->expected_arrival_time)
                                            <span class="flex items-center gap-1 font-mono">
                                                Arrival: {{ \Carbon\Carbon::parse($trip->expected_arrival_time)->format('h:i A') }}
                                            </span>
                                        @endif
                                        <span>
                                            Pickup: <strong>{{ $trip->pickup_point ?: 'Station' }}</strong>
                                        </span>
                                    </div>
                                </div>

                                {{-- Vehicle & Driver Meta --}}
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700 min-w-[200px] space-y-1 text-xs">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $trip->driver?->name ?? 'Commercial Driver' }}
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-[10px] ml-1"></i>
                                    </div>
                                    <div class="font-mono text-slate-600 dark:text-slate-300">
                                        {{ $trip->vehicle?->registration_no }} ({{ $trip->vehicle?->model }})
                                    </div>
                                    <div class="text-[11px] text-teal-600 dark:text-teal-400 font-extrabold pt-1">
                                        <i class="fa-solid fa-chair text-[10px] mr-1"></i>
                                        {{ $trip->available_seats }} Available {{ \Illuminate\Support\Str::plural('Seat', $trip->available_seats) }}
                                    </div>
                                </div>

                                {{-- Pricing & Action --}}
                                <div class="flex sm:flex-col items-center sm:items-end justify-between gap-3 min-w-[170px] border-t sm:border-t-0 pt-4 sm:pt-0 border-slate-100 dark:border-slate-800">
                                    <div class="text-right">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Fare Per Seat</span>
                                        <span class="font-mono font-black text-emerald-700 dark:text-emerald-400 text-lg">
                                            Rs. {{ number_format($trip->fare ?? 0, 2) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('trips.show', $trip->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors">
                                            Details
                                        </a>
                                        <a href="{{ route('booking.create', $trip->id) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black transition-all shadow-md shadow-emerald-600/20">
                                            Book Seats
                                        </a>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

        </div>

    </div>
</section>

{{-- ─── Modal: Fast Booking Confirmation for Private Rental ─── --}}
<div id="reserveModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 relative">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Confirm Reservation</span>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white" id="modalVehicleName">Vehicle</h3>
            </div>
            <button type="button" onclick="closeReserveModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" id="reserveForm" action="">
            @csrf

            <div class="space-y-4 text-xs">
                
                {{-- Summary Pill --}}
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 space-y-1.5">
                    <div class="flex justify-between text-slate-700 dark:text-slate-300 font-semibold">
                        <span>Vehicle Reg:</span>
                        <strong class="font-mono text-slate-900 dark:text-white" id="modalVehicleReg">—</strong>
                    </div>
                    <div class="flex justify-between text-slate-700 dark:text-slate-300 font-semibold">
                        <span>Duration:</span>
                        <strong class="font-mono text-slate-900 dark:text-white" id="modalDuration">—</strong>
                    </div>
                    <div class="flex justify-between text-emerald-900 dark:text-emerald-300 font-bold pt-1 border-t border-emerald-200 dark:border-emerald-800 text-sm">
                        <span>Calculated Rental Total:</span>
                        <span class="font-mono font-black" id="modalTotalFare">Rs. 0.00</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Pickup Location *</label>
                    <input type="text" name="pickup_location" id="modalPickup" value="{{ $pickup }}" required placeholder="e.g. Islamabad International Airport or Address" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Destination *</label>
                    <input type="text" name="destination_location" id="modalDestination" value="{{ $destination }}" required placeholder="e.g. Murree Mall Road or City" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Pickup Date *</label>
                        <input type="date" name="pickup_date" id="modalPickupDate" value="{{ $pickupDate }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Pickup Time *</label>
                        <input type="time" name="pickup_time" id="modalPickupTime" value="{{ $pickupTime }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Return Date *</label>
                        <input type="date" name="return_date" id="modalReturnDate" value="{{ $returnDate }}" min="{{ $pickupDate }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Return Time *</label>
                        <input type="time" name="return_time" id="modalReturnTime" value="{{ $returnTime }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Passenger Notes (Optional)</label>
                    <textarea name="passenger_notes" rows="2" placeholder="Luggage details, flight number, or specific instructions..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium"></textarea>
                </div>

                @guest
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[11px] text-amber-800 dark:text-amber-300">
                        <i class="fa-solid fa-lock mr-1"></i> You will be prompted to log in or create an account before your booking is finalized.
                    </div>
                @endguest

            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800 mt-4">
                <button type="button" onclick="closeReserveModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs">
                    Cancel
                </button>
                <button type="submit" id="confirm-reserve-btn" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md shadow-emerald-600/20">
                    Confirm & Reserve Vehicle
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function setDateShortcut(daysOffset) {
        const today = new Date();
        today.setDate(today.getDate() + daysOffset);
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const dateStr = `${yyyy}-${mm}-${dd}`;

        const pickupInput = document.getElementById('pickup_date');
        if (pickupInput) {
            pickupInput.value = dateStr;
            validateDateRange();
        }
    }

    function validateDateRange() {
        const pickup = document.getElementById('pickup_date');
        const returnDate = document.getElementById('return_date');
        if (pickup && returnDate) {
            returnDate.min = pickup.value;
            if (returnDate.value < pickup.value) {
                returnDate.value = pickup.value;
            }
        }
    }

    function openReserveModal(vehicleId, model, regNo, days, dailyRate, totalFare) {
        const modal = document.getElementById('reserveModal');
        const form = document.getElementById('reserveForm');
        form.action = `/book-vehicle/${vehicleId}/reserve`;

        document.getElementById('modalVehicleName').innerText = model;
        document.getElementById('modalVehicleReg').innerText = regNo;
        document.getElementById('modalDuration').innerText = `${days} day(s) @ Rs. ${Number(dailyRate).toLocaleString()}/day`;
        document.getElementById('modalTotalFare').innerText = `Rs. ${Number(totalFare).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeReserveModal() {
        const modal = document.getElementById('reserveModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

@endsection
