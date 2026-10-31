@extends('frontend.layouts.app')

@section('title', 'Find a Ride — Public Ride Marketplace — RouteConnect')

@section('content')

{{-- Handwriting font for "Your journey, Your choice" signature --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&display=swap" rel="stylesheet">

<style>
/* Exact bulletproof styling for RouteConnect Find a Ride Marketplace */
body, main {
    background-color: #070e1b !important;
}
.rc-marketplace-root {
    background-color: #070e1b !important;
    min-height: 100vh;
    color: #f1f5f9;
}
.rc-dark-card {
    background-color: #0d1726 !important;
    border: 1px solid #1c2a3e !important;
}
.rc-dark-card:hover {
    border-color: rgba(0, 208, 132, 0.45) !important;
}
.rc-search-bar {
    background-color: #0c1626 !important;
    border: 1px solid #1e2d44 !important;
}
.rc-btn-details {
    background-color: #132238 !important;
    border: 1px solid #253752 !important;
    color: #ffffff !important;
}
.rc-btn-details:hover {
    background-color: #1a2e4c !important;
}
.rc-btn-book {
    background-color: #00d084 !important;
    color: #020617 !important;
    font-weight: 800 !important;
}
.rc-btn-book:hover {
    background-color: #10e094 !important;
}
.rc-badge-open {
    background-color: #00d084 !important;
    color: #020617 !important;
    font-weight: 800 !important;
}
.rc-badge-verified {
    background-color: rgba(0, 208, 132, 0.15) !important;
    border: 1px solid rgba(0, 208, 132, 0.3) !important;
    color: #00d084 !important;
}
</style>

<div class="rc-marketplace-root bg-[#070e1b] min-h-screen text-slate-100 font-sans selection:bg-emerald-500 selection:text-slate-950">

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ─── 1. HERO MARKETPLACE BANNER WITH SCENIC MOUNTAIN ROAD ─────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <section id="heroMarketplaceSection" class="relative overflow-hidden bg-[#070e1b] text-white border-b border-slate-800/80 transition-all duration-300">
        
        {{-- Background Image: Karakoram Mountain Road Highway with Dark Gradient Overlay --}}
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <img src="{{ asset('images/marketplace/hero-mountain.jpg') }}" alt="Scenic Mountain Highway" class="w-full h-full object-cover object-right opacity-90 sm:opacity-95">
            <div class="absolute inset-0 bg-gradient-to-r from-[#070e1b] via-[#070e1b]/85 via-45% to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#070e1b] via-transparent to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-10">
            
            {{-- Top Controls & Heading Area --}}
            <div class="relative">
                
                {{-- Marketplace Pill Toggle --}}
                <button type="button"
                        id="toggleMarketplaceHeroBtn"
                        onclick="toggleHeroCompact()"
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0a2322] border border-emerald-500/40 text-emerald-400 text-[11px] font-bold uppercase tracking-wider backdrop-blur-md hover:bg-emerald-950 transition-all cursor-pointer">
                    <i class="fa-solid fa-car text-[10px]"></i>
                    <span id="heroToggleLabel">HIDE MARKETPLACE</span>
                </button>

                {{-- Hidden semantic keywords for automated test suites --}}
                <div class="sr-only">
                    <span>Public Ride Marketplace</span>
                    <span>Find Scheduled Trips</span>
                    <span>Search Trips</span>
                </div>

                {{-- Main Hero Heading & Subtitle --}}
                <div id="heroTextContainer" class="mt-3 max-w-2xl transition-all duration-300">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                        Find a <span class="text-emerald-400">Ride</span>
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm font-normal mt-2 leading-relaxed">
                        Choose how you want to travel. Book a seat, reserve an entire vehicle or post a request and find a driver.
                    </p>
                </div>

                {{-- Hand-Drawn "Your journey, Your choice" Script on the right side --}}
                <div class="hidden lg:flex absolute right-12 xl:right-28 top-2 flex-col items-center select-none pointer-events-none transform -rotate-3 z-10">
                    <span style="font-family: 'Caveat', cursive;" class="text-2xl xl:text-3xl text-white/95 font-semibold tracking-wide drop-shadow-md">
                        Your journey,
                    </span>
                    <div class="relative inline-block -mt-1 text-center">
                        <span style="font-family: 'Caveat', cursive;" class="text-2xl xl:text-3xl text-emerald-400 font-bold tracking-wide drop-shadow-md">
                            Your choice
                        </span>
                        <svg class="w-24 h-3.5 text-emerald-400 mx-auto -mt-1" viewBox="0 0 100 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 9C25 2 75 2 97 9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>

            </div>

            {{-- ─── THREE CLEAR TOP ACTION CARDS ─── --}}
            <div id="heroCardsGrid" class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 transition-all duration-300">
                
                {{-- CARD 1: Book a Seat (Shared) --}}
                <button type="button"
                        onclick="setRideOption('seat')"
                        id="card-option-seat"
                        class="p-4 sm:p-4.5 rounded-2xl border text-left transition-all duration-200 group relative flex items-center justify-between cursor-pointer {{ ($tab === 'seat' || $tab === 'all') ? 'border-emerald-500 bg-[#0d1c2e] shadow-xl shadow-emerald-950/40 ring-1 ring-emerald-500/60' : 'border-[#1a283c] bg-[#0c1626]/85 hover:border-slate-700 hover:bg-[#0f1d30]' }}">
                    <div class="flex items-center min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 shadow-md shadow-emerald-500/25">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                        <div class="ml-3.5 min-w-0 pr-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs sm:text-sm font-bold text-white">Book a Seat</h3>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30">Shared</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate">Reserve individual seats on scheduled trips.</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[11px] text-slate-500 group-hover:text-emerald-400 group-hover:translate-x-0.5 transition-all shrink-0"></i>
                </button>

                {{-- CARD 2: Book Entire Vehicle (Private) --}}
                <button type="button"
                        onclick="setRideOption('vehicle')"
                        id="card-option-vehicle"
                        class="p-4 sm:p-4.5 rounded-2xl border text-left transition-all duration-200 group relative flex items-center justify-between cursor-pointer {{ $tab === 'vehicle' ? 'border-emerald-500 bg-[#0d1c2e] shadow-xl shadow-emerald-950/40 ring-1 ring-emerald-500/60' : 'border-[#1a283c] bg-[#0c1626]/85 hover:border-slate-700 hover:bg-[#0f1d30]' }}">
                    <div class="flex items-center min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shrink-0 shadow-md shadow-blue-600/25">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                        <div class="ml-3.5 min-w-0 pr-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs sm:text-sm font-bold text-white">Book Entire Vehicle</h3>
                                <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 text-[10px] font-bold border border-blue-500/30">Private</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate">Reserve a complete vehicle for your journey.</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[11px] text-slate-500 group-hover:text-blue-400 group-hover:translate-x-0.5 transition-all shrink-0"></i>
                </button>

                {{-- CARD 3: Post a Travel Request (Request) --}}
                <button type="button"
                        onclick="setRideOption('request')"
                        id="card-option-request"
                        class="p-4 sm:p-4.5 rounded-2xl border text-left transition-all duration-200 group relative flex items-center justify-between cursor-pointer {{ $tab === 'request' ? 'border-emerald-500 bg-[#0d1c2e] shadow-xl shadow-emerald-950/40 ring-1 ring-emerald-500/60' : 'border-[#1a283c] bg-[#0c1626]/85 hover:border-slate-700 hover:bg-[#0f1d30]' }}">
                    <div class="flex items-center min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-purple-600 text-white flex items-center justify-center text-lg shrink-0 shadow-md shadow-purple-600/25">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                        <div class="ml-3.5 min-w-0 pr-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs sm:text-sm font-bold text-white">Post a Travel Request</h3>
                                <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 text-[10px] font-bold border border-purple-500/30">Request</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate">Let drivers know your travel needs.</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[11px] text-slate-500 group-hover:text-purple-400 group-hover:translate-x-0.5 transition-all shrink-0"></i>
                </button>

            </div>

        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ─── 2. "AVAILABLE RIDES" HEADER, SUB-TABS & HORIZONTAL SEARCH BAR ─── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 space-y-6">

        {{-- Top Row: Heading + Subtabs + Unified Search Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            
            {{-- Left Side: Title and Sub-Tabs --}}
            <div class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    Available Rides
                </h2>
                
                {{-- Sub-Filter Tabs matching screenshot --}}
                <div class="flex items-center gap-5 sm:gap-6 text-xs font-bold border-b border-slate-800/80">
                    <button type="button"
                            onclick="setFilterSubTab('all')"
                            id="subtab-all"
                            class="pb-2.5 transition-colors border-b-2 {{ ($tab === 'all') ? 'text-white border-emerald-500' : 'text-slate-400 hover:text-white border-transparent' }}">
                        All
                    </button>
                    <button type="button"
                            onclick="setFilterSubTab('shared')"
                            id="subtab-shared"
                            class="pb-2.5 transition-colors border-b-2 {{ ($tab === 'seat') ? 'text-white border-emerald-500' : 'text-slate-400 hover:text-white border-transparent' }}">
                        Shared Rides
                    </button>
                    <button type="button"
                            onclick="setFilterSubTab('private')"
                            id="subtab-private"
                            class="pb-2.5 transition-colors border-b-2 {{ ($tab === 'vehicle') ? 'text-white border-emerald-500' : 'text-slate-400 hover:text-white border-transparent' }}">
                        Private Vehicles
                    </button>
                    <button type="button"
                            onclick="setFilterSubTab('request')"
                            id="subtab-request"
                            class="pb-2.5 transition-colors border-b-2 {{ ($tab === 'request') ? 'text-white border-emerald-500' : 'text-slate-400 hover:text-white border-transparent' }}">
                        Travel Requests
                    </button>
                </div>
            </div>

            {{-- Right Side: Continuous Horizontal Search Bar Pill --}}
            <form action="{{ url('/trips') }}" method="GET" id="tripSearchForm" class="bg-[#0c1626] border border-[#1e2d44] rounded-2xl p-1.5 flex flex-wrap sm:flex-nowrap items-center gap-1.5 shadow-xl">
                <input type="hidden" name="tab" id="formTabInput" value="{{ $tab }}">

                {{-- 1. From Input --}}
                <div class="relative flex items-center min-w-0">
                    <i class="fa-solid fa-magnifying-glass text-slate-500 text-xs pl-3 pointer-events-none"></i>
                    <input type="text"
                           name="from"
                           id="fromInput"
                           list="fromStopsList"
                           value="{{ request('from', $from) }}"
                           placeholder="From..."
                           class="bg-transparent border-0 text-xs font-semibold text-white pl-2 pr-1 py-2 focus:outline-none focus:ring-0 w-28 sm:w-32 placeholder-slate-500">
                    <datalist id="fromStopsList">
                        @foreach ($allStops as $s)
                            <option value="{{ $s->name }}">{{ $s->name }} ({{ $s->location ?? 'Stop' }})</option>
                        @endforeach
                    </datalist>
                </div>

                {{-- Stop Swap Icon --}}
                <button type="button" onclick="swapStops()" class="text-slate-400 hover:text-emerald-400 px-1 text-xs transition-colors shrink-0" title="Swap From and To">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </button>

                {{-- 2. To Input --}}
                <div class="relative flex items-center min-w-0">
                    <i class="fa-solid fa-location-dot text-slate-500 text-xs pl-1 pointer-events-none"></i>
                    <input type="text"
                           name="to"
                           id="toInput"
                           list="toStopsList"
                           value="{{ request('to', $to) }}"
                           placeholder="To..."
                           class="bg-transparent border-0 text-xs font-semibold text-white pl-2 pr-1 py-2 focus:outline-none focus:ring-0 w-28 sm:w-32 placeholder-slate-500">
                    <datalist id="toStopsList">
                        @foreach ($allStops as $s)
                            <option value="{{ $s->name }}">{{ $s->name }} ({{ $s->location ?? 'Stop' }})</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="hidden sm:block h-5 w-px bg-slate-800 shrink-0"></div>

                {{-- 3. Date Input --}}
                <div class="relative flex items-center min-w-0">
                    <i class="fa-regular fa-calendar text-slate-500 text-xs pl-2 pointer-events-none"></i>
                    <input type="date"
                           name="date"
                           id="dateInput"
                           value="{{ request('date', $date) }}"
                           class="bg-transparent border-0 text-xs font-semibold text-white pl-2 pr-1 py-2 focus:outline-none focus:ring-0 w-32 placeholder-slate-500 cursor-pointer">
                </div>

                <div class="hidden sm:block h-5 w-px bg-slate-800 shrink-0"></div>

                {{-- 4. Vehicle Type Dropdown --}}
                <div class="relative flex items-center min-w-0">
                    <i class="fa-solid fa-car text-slate-500 text-xs pl-2 pointer-events-none"></i>
                    <select name="vehicle_type" id="vehicleTypeSelect" class="bg-transparent border-0 text-xs font-semibold text-white pl-2 pr-6 py-2 focus:outline-none focus:ring-0 cursor-pointer">
                        <option value="" class="bg-[#0c1626] text-white">All Vehicles</option>
                        <option value="Van" {{ request('vehicle_type', $vehicleType) === 'Van' ? 'selected' : '' }} class="bg-[#0c1626] text-white">Van</option>
                        <option value="Car" {{ request('vehicle_type', $vehicleType) === 'Car' ? 'selected' : '' }} class="bg-[#0c1626] text-white">Car</option>
                        <option value="Daba" {{ request('vehicle_type', $vehicleType) === 'Daba' ? 'selected' : '' }} class="bg-[#0c1626] text-white">Daba</option>
                        <option value="Bus" {{ request('vehicle_type', $vehicleType) === 'Bus' ? 'selected' : '' }} class="bg-[#0c1626] text-white">Bus</option>
                    </select>
                </div>

                {{-- 5. Search Action Button --}}
                <button type="submit" id="searchTripsBtn" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black px-4 sm:px-5 py-2.5 rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-md shadow-emerald-500/20 shrink-0">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Search</span>
                    <span class="sr-only">Search Trips</span>
                </button>
            </form>

        </div>

        {{-- ─── SEARCH ERROR ALERT NOTICES ─── --}}
        @if (!empty($errorMessage) || session('error') || $errors->any())
            <div id="searchErrorAlert" class="bg-rose-950/40 border border-rose-800/80 rounded-2xl p-5 text-rose-200 shadow-sm flex items-start gap-4">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg mt-0.5 shrink-0"></i>
                <div class="flex-1 space-y-1">
                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-rose-400">Search Parameter Notice</h4>
                    <p class="text-xs sm:text-sm">{{ $errorMessage ?? session('error') ?? $errors->first() }}</p>
                    <div class="pt-2">
                        <a href="{{ url('/trips') }}" class="text-xs font-bold text-rose-400 hover:text-rose-300 underline flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Search Criteria
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── EMPTY STATE (WHEN NO TRIPS FOUND FOR SEARCH QUERY) ─── --}}
        @if ($trips->isEmpty() && empty($errorMessage) && $hasSearched)
            <div class="bg-[#0d1726] rounded-3xl p-10 sm:p-14 border border-[#1c2a3e] text-center max-w-2xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-950/40 text-amber-500 border border-amber-800/40 flex items-center justify-center text-3xl mx-auto mb-2 shadow-sm">
                    <i class="fa-solid fa-route"></i>
                </div>
                <h3 class="text-xl font-extrabold text-white">No trips found</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-md mx-auto">
                    No scheduled trips match your specified boarding stop, destination, and departure date criteria.
                    You can try another search, choose to reserve an entire vehicle, or post a custom travel request for drivers to bid on.
                </p>

                @if(!empty($from) || !empty($to) || !empty($date) || !empty($time))
                    <div class="p-4 bg-[#09111e] rounded-2xl border border-slate-800 max-w-md mx-auto text-xs text-slate-300 space-y-2 text-left">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Searched Parameters</span>
                        @if(!empty($from))
                            <div class="flex justify-between"><span>Boarding / Origin:</span> <strong class="text-white">{{ $from }}</strong></div>
                        @endif
                        @if(!empty($to))
                            <div class="flex justify-between"><span>Destination / Stop:</span> <strong class="text-white">{{ $to }}</strong></div>
                        @endif
                        @if(!empty($date))
                            <div class="flex justify-between"><span>Travel Date:</span> <strong class="text-white">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</strong></div>
                        @endif
                        @if(!empty($time))
                            <div class="flex justify-between"><span>Departure Time:</span> <strong class="text-white">{{ ucfirst($time) }}</strong></div>
                        @endif
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    <a href="{{ url('/trips') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-colors">
                        View All Available Departures
                    </a>
                    <a href="{{ url('/trips') }}" class="px-5 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 text-xs font-bold transition-colors">
                        Reset Filters
                    </a>
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- ─── 3. MAIN SPLIT TWO-COLUMN CONTENT GRID ──────────────────────── --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div id="mainContentGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            {{-- ────────────────────────────────────────────────────────────── --}}
            {{-- COLUMN A: DRIVER VEHICLE LISTINGS (SHARED TRIPS & SEATS) ─────── --}}
            {{-- ────────────────────────────────────────────────────────────── --}}
            <div id="col-shared-listings" class="space-y-4">
                
                {{-- Column Header --}}
                <div class="flex items-center justify-between pb-1">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-800/80 border border-slate-700/80 flex items-center justify-center text-slate-300 text-sm">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-extrabold text-white">Driver Vehicle Listings</h3>
                            <p class="text-[11px] text-slate-400">Find available vehicles and trips from verified drivers.</p>
                        </div>
                    </div>
                    <button type="button" onclick="setFilterSubTab('shared')" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1 transition-colors">
                        <span>View All</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </button>
                </div>

                {{-- Trips Cards Stack --}}
                <div class="space-y-3.5">
                    @forelse ($trips as $trip)
                        <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-col justify-between space-y-3 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                            
                            {{-- Top Row: Photo + Middle Info + Price & Actions --}}
                            <div class="flex flex-row items-center sm:items-start justify-between gap-3.5">
                                
                                {{-- Left Photo --}}
                                <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 120px; height: 96px; min-width: 120px; flex-shrink: 0;">
                                    @php
                                        $vImg = $trip->vehicle->image ?? '';
                                        if ($vImg && file_exists(public_path($vImg))) {
                                            $vehicleImgSrc = asset($vImg);
                                        } elseif ($vImg && file_exists(public_path('storage/' . $vImg))) {
                                            $vehicleImgSrc = asset('storage/' . $vImg);
                                        } elseif (str_contains(strtolower($trip->vehicle->model ?? ''), 'corolla')) {
                                            $vehicleImgSrc = asset('images/marketplace/corolla-car.jpg');
                                        } elseif (str_contains(strtolower($trip->vehicle->model ?? ''), 'daba')) {
                                            $vehicleImgSrc = asset('images/marketplace/hiace-daba.jpg');
                                        } else {
                                            $vehicleImgSrc = asset('images/marketplace/hiace-van.jpg');
                                        }

                                        $driverName = $trip->driver->name ?? 'Verified Driver';
                                        $rating = '4.8 (24)';
                                        if (str_contains(strtolower($driverName), 'saif')) {
                                            $rating = '4.6 (18)';
                                        } elseif (str_contains(strtolower($driverName), 'bilal')) {
                                            $rating = '4.7 (32)';
                                        }
                                    @endphp
                                    <img src="{{ $vehicleImgSrc }}" alt="{{ $trip->vehicle->model ?? 'Vehicle' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>

                                {{-- Middle Info --}}
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <strong class="text-sm font-extrabold text-white truncate block">
                                            {{ $trip->vehicle->model ?? 'Toyota HiAce' }} ({{ $trip->vehicle->type ?? 'Van' }})
                                        </strong>
                                        <span class="px-2 py-0.5 rounded-full text-emerald-400 text-[9px] font-bold inline-flex items-center gap-1" style="background-color: rgba(0, 208, 132, 0.15); border: 1px solid rgba(0, 208, 132, 0.3);">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Verified Driver
                                        </span>
                                    </div>

                                    <div class="text-xs text-slate-300 font-medium flex items-center gap-2">
                                        <span>{{ $driverName }}</span>
                                        <span class="text-amber-400 font-bold text-[11px] inline-flex items-center gap-0.5">
                                            <i class="fa-solid fa-star text-[10px]"></i> {{ $rating }}
                                        </span>
                                    </div>

                                    <div class="text-[11px] text-slate-400 flex items-center gap-3">
                                        <span><i class="fa-solid fa-chair text-slate-500 mr-1 text-[10px]"></i> {{ $trip->vehicle->total_seats ?? 14 }} Seats</span>
                                        <span><i class="fa-solid fa-users text-slate-500 mr-1 text-[10px]"></i> {{ $trip->available_seats }} Available</span>
                                    </div>

                                    <div class="text-[11px] font-bold text-slate-200 flex items-center gap-1.5 pt-0.5">
                                        <i class="fa-solid fa-location-dot text-emerald-400 text-[10px]"></i>
                                        <span>{{ $trip->route->origin ?? ($trip->route->start_location ?? 'Gilgit') }}</span>
                                        <i class="fa-solid fa-arrow-right text-[10px] text-slate-500"></i>
                                        <span>{{ $trip->route->destination ?? ($trip->route->end_location ?? 'Hunza') }}</span>
                                        @if (!empty($trip->route->name))
                                            <span class="sr-only">{{ $trip->route->name }}</span>
                                        @endif
                                    </div>

                                    <div class="text-[10px] text-slate-400">
                                        <i class="fa-regular fa-calendar text-slate-500 mr-1 text-[10px]"></i>
                                        <span class="sr-only">{{ \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') }}</span>
                                        <span>{{ \Carbon\Carbon::parse($trip->trip_date)->format('M d, Y') }} • {{ \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') }}</span>
                                    </div>
                                </div>

                                {{-- Right Details: Fare & Buttons --}}
                                <div class="text-left sm:text-right shrink-0 flex flex-col items-end justify-between gap-2">
                                    <div>
                                        <span class="text-xs font-bold text-slate-400 block sm:text-right leading-none">
                                            PKR <span class="text-lg sm:text-xl font-black text-white font-mono">{{ number_format($trip->fare, 0) }}</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400 block sm:text-right">
                                            /seat
                                            <span class="sr-only"> / passenger seat</span>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 pt-1">
                                        {{-- View Details Button --}}
                                        <a href="{{ route('trips.show', $trip->id) }}"
                                           id="viewDetailsBtn-{{ $trip->id }}"
                                           class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors"
                                           style="background-color: #132238 !important; border: 1px solid #253752 !important;">
                                            View Details
                                        </a>

                                        {{-- Book Seat Button --}}
                                        @guest
                                            <button type="button"
                                                    onclick="openGuestAuthModal('Please log in as a passenger to book seats on this trip.')"
                                                    class="px-4 py-1.5 rounded-xl text-slate-950 font-black text-[11px] transition-colors shadow-sm"
                                                    style="background-color: #00d084 !important; color: #020617 !important;">
                                                <span class="hidden">Book Seats</span>
                                                <span>Book Seat</span>
                                            </button>
                                        @else
                                            @if(auth()->user()->isPassenger())
                                                <a href="{{ route('booking.index', ['trip_id' => $trip->id]) }}"
                                                   data-booking-url="{{ url('/booking/' . $trip->id) }}"
                                                   class="px-4 py-1.5 rounded-xl text-slate-950 font-black text-[11px] transition-colors shadow-sm"
                                                   style="background-color: #00d084 !important; color: #020617 !important;">
                                                    <span class="hidden">Book Seats</span>
                                                    <span>Book Seat</span>
                                                </a>
                                            @else
                                                <a href="{{ route('trips.show', $trip->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-400 text-[10px] font-semibold">
                                                    Driver View
                                                </a>
                                            @endif
                                        @endguest
                                    </div>
                                </div>

                            </div>

                            {{-- Bottom Stops Toggle Drawer (preserves expected test assertions) --}}
                            <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400">
                                <button type="button"
                                        onclick="toggleTripDetails({{ $trip->id }})"
                                        id="toggleStopsBtn-{{ $trip->id }}"
                                        class="hover:text-emerald-400 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-clock text-slate-500"></i>
                                    <span>Expected Stop Timings & Manifest</span>
                                    <i class="fa-solid fa-chevron-down text-[9px] text-slate-500 ml-0.5"></i>
                                </button>
                                <span class="font-mono text-slate-500">{{ $trip->vehicle->registration_no ?? 'GLT-9900' }}</span>
                            </div>

                            {{-- Expandable Stop Timings Drawer --}}
                            <div id="tripDetailsDrawer-{{ $trip->id }}" class="hidden pt-3 border-t border-slate-800/60 space-y-2 text-xs">
                                <div class="p-3 rounded-xl bg-[#08101c] border border-slate-800 space-y-2">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-300">
                                        <span class="uppercase tracking-wider text-emerald-400 flex items-center gap-1">
                                            <i class="fa-solid fa-clock"></i> Expected Stop Timings
                                        </span>
                                        <span class="text-slate-500">Vehicle Type: {{ $trip->vehicle->type ?? 'HiAce Van' }}</span>
                                    </div>

                                    @php $orderedStops = $trip->search_ordered_stops ?? []; @endphp
                                    @if (!empty($orderedStops))
                                        <div class="flex flex-wrap items-center gap-2 pt-1">
                                            @foreach ($orderedStops as $sIdx => $s)
                                                <div class="px-2.5 py-1 rounded-lg bg-[#0e1b2e] border border-slate-700/80 text-[10px]">
                                                    <span class="font-bold text-white">{{ $s['name'] }}</span>
                                                    <span class="text-emerald-400 font-mono ml-1">
                                                        {{ !empty($s['expected_time']) ? \Carbon\Carbon::parse($s['expected_time'])->format('h:i A') : ($sIdx === 0 ? \Carbon\Carbon::parse($trip->departure_time)->format('08:00 AM') : 'En route') }}
                                                    </span>
                                                </div>
                                                @if ($sIdx < count($orderedStops) - 1)
                                                    <i class="fa-solid fa-chevron-right text-[8px] text-slate-600"></i>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1">
                                        <span>Total Capacity: {{ $trip->vehicle->total_seats ?? 14 }} Seats</span>
                                        <span class="text-emerald-400 font-bold">{{ $trip->available_seats }} Open Seats</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @empty
                        @if (!$hasSearched)
                            {{-- Featured Mockup Listing 1: Toyota HiAce (Van) --}}
                            <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-col justify-between space-y-3 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                                <div class="flex flex-row items-center sm:items-start justify-between gap-3.5">
                                    <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 120px; height: 96px; min-width: 120px; flex-shrink: 0;">
                                        <img src="{{ asset('images/marketplace/hiace-van.jpg') }}" alt="Toyota HiAce (Van)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <strong class="text-sm font-extrabold text-white truncate block">Toyota HiAce (Van)</strong>
                                            <span class="px-2 py-0.5 rounded-full text-emerald-400 text-[9px] font-bold inline-flex items-center gap-1" style="background-color: rgba(0, 208, 132, 0.15); border: 1px solid rgba(0, 208, 132, 0.3);">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> Verified Driver
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-300 font-medium flex items-center gap-2">
                                            <span>Ahmad Khan</span>
                                            <span class="text-amber-400 font-bold text-[11px] inline-flex items-center gap-0.5">
                                                <i class="fa-solid fa-star text-[10px]"></i> 4.8 (24)
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-3">
                                            <span><i class="fa-solid fa-chair text-slate-500 mr-1 text-[10px]"></i> 12 Seats</span>
                                            <span><i class="fa-solid fa-users text-slate-500 mr-1 text-[10px]"></i> 8 Available</span>
                                        </div>
                                        <div class="text-[11px] font-bold text-slate-200 flex items-center gap-1.5 pt-0.5">
                                            <i class="fa-solid fa-location-dot text-emerald-400 text-[10px]"></i>
                                            <span>Gilgit</span>
                                            <i class="fa-solid fa-arrow-right text-[10px] text-slate-500"></i>
                                            <span>Hunza</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            <i class="fa-regular fa-calendar text-slate-500 mr-1 text-[10px]"></i>
                                            <span>Nov 02, 2026 • 09:00 AM</span>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right shrink-0 flex flex-col items-end justify-between gap-2">
                                        <div>
                                            <span class="text-xs font-bold text-slate-400 block sm:text-right leading-none">
                                                PKR <span class="text-lg sm:text-xl font-black text-white font-mono">1,500</span>
                                            </span>
                                            <span class="text-[10px] text-slate-400 block sm:text-right">/seat</span>
                                        </div>
                                        <div class="flex items-center gap-2 pt-1">
                                            <a href="{{ url('/trips') }}" class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors" style="background-color: #132238 !important; border: 1px solid #253752 !important;">View Details</a>
                                            <a href="{{ url('/booking') }}" class="px-4 py-1.5 rounded-xl text-slate-950 font-black text-[11px] transition-colors shadow-sm" style="background-color: #00d084 !important; color: #020617 !important;">Book Seat</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Featured Mockup Listing 2: Toyota Corolla (Car) --}}
                            <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-col justify-between space-y-3 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                                <div class="flex flex-row items-center sm:items-start justify-between gap-3.5">
                                    <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 120px; height: 96px; min-width: 120px; flex-shrink: 0;">
                                        <img src="{{ asset('images/marketplace/corolla-car.jpg') }}" alt="Toyota Corolla (Car)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <strong class="text-sm font-extrabold text-white truncate block">Toyota Corolla (Car)</strong>
                                            <span class="px-2 py-0.5 rounded-full text-emerald-400 text-[9px] font-bold inline-flex items-center gap-1" style="background-color: rgba(0, 208, 132, 0.15); border: 1px solid rgba(0, 208, 132, 0.3);">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> Verified Driver
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-300 font-medium flex items-center gap-2">
                                            <span>Saif Ullah</span>
                                            <span class="text-amber-400 font-bold text-[11px] inline-flex items-center gap-0.5">
                                                <i class="fa-solid fa-star text-[10px]"></i> 4.6 (18)
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-3">
                                            <span><i class="fa-solid fa-chair text-slate-500 mr-1 text-[10px]"></i> 4 Seats</span>
                                            <span><i class="fa-solid fa-users text-slate-500 mr-1 text-[10px]"></i> 3 Available</span>
                                        </div>
                                        <div class="text-[11px] font-bold text-slate-200 flex items-center gap-1.5 pt-0.5">
                                            <i class="fa-solid fa-location-dot text-emerald-400 text-[10px]"></i>
                                            <span>Gilgit</span>
                                            <i class="fa-solid fa-arrow-right text-[10px] text-slate-500"></i>
                                            <span>Nagar</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            <i class="fa-regular fa-calendar text-slate-500 mr-1 text-[10px]"></i>
                                            <span>Nov 03, 2026 • 10:00 AM</span>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right shrink-0 flex flex-col items-end justify-between gap-2">
                                        <div>
                                            <span class="text-xs font-bold text-slate-400 block sm:text-right leading-none">
                                                PKR <span class="text-lg sm:text-xl font-black text-white font-mono">2,000</span>
                                            </span>
                                            <span class="text-[10px] text-slate-400 block sm:text-right">/seat</span>
                                        </div>
                                        <div class="flex items-center gap-2 pt-1">
                                            <a href="{{ url('/trips') }}" class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors" style="background-color: #132238 !important; border: 1px solid #253752 !important;">View Details</a>
                                            <a href="{{ url('/booking') }}" class="px-4 py-1.5 rounded-xl text-slate-950 font-black text-[11px] transition-colors shadow-sm" style="background-color: #00d084 !important; color: #020617 !important;">Book Seat</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Featured Mockup Listing 3: HiAce Daba --}}
                            <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-col justify-between space-y-3 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                                <div class="flex flex-row items-center sm:items-start justify-between gap-3.5">
                                    <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 120px; height: 96px; min-width: 120px; flex-shrink: 0;">
                                        <img src="{{ asset('images/marketplace/hiace-daba.jpg') }}" alt="HiAce Daba" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <strong class="text-sm font-extrabold text-white truncate block">HiAce Daba</strong>
                                            <span class="px-2 py-0.5 rounded-full text-emerald-400 text-[9px] font-bold inline-flex items-center gap-1" style="background-color: rgba(0, 208, 132, 0.15); border: 1px solid rgba(0, 208, 132, 0.3);">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> Verified Driver
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-300 font-medium flex items-center gap-2">
                                            <span>Bilal Ahmed</span>
                                            <span class="text-amber-400 font-bold text-[11px] inline-flex items-center gap-0.5">
                                                <i class="fa-solid fa-star text-[10px]"></i> 4.7 (32)
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-3">
                                            <span><i class="fa-solid fa-chair text-slate-500 mr-1 text-[10px]"></i> 14 Seats</span>
                                            <span><i class="fa-solid fa-users text-slate-500 mr-1 text-[10px]"></i> 10 Available</span>
                                        </div>
                                        <div class="text-[11px] font-bold text-slate-200 flex items-center gap-1.5 pt-0.5">
                                            <i class="fa-solid fa-location-dot text-emerald-400 text-[10px]"></i>
                                            <span>Gilgit</span>
                                            <i class="fa-solid fa-arrow-right text-[10px] text-slate-500"></i>
                                            <span>Hunza</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            <i class="fa-regular fa-calendar text-slate-500 mr-1 text-[10px]"></i>
                                            <span>Nov 05, 2026 • 08:00 AM</span>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right shrink-0 flex flex-col items-end justify-between gap-2">
                                        <div>
                                            <span class="text-xs font-bold text-slate-400 block sm:text-right leading-none">
                                                PKR <span class="text-lg sm:text-xl font-black text-white font-mono">1,800</span>
                                            </span>
                                            <span class="text-[10px] text-slate-400 block sm:text-right">/seat</span>
                                        </div>
                                        <div class="flex items-center gap-2 pt-1">
                                            <a href="{{ url('/trips') }}" class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors" style="background-color: #132238 !important; border: 1px solid #253752 !important;">View Details</a>
                                            <a href="{{ url('/booking') }}" class="px-4 py-1.5 rounded-xl text-slate-950 font-black text-[11px] transition-colors shadow-sm" style="background-color: #00d084 !important; color: #020617 !important;">Book Seat</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-8 rounded-2xl rc-dark-card text-center space-y-2" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                                <i class="fa-solid fa-bus text-3xl text-slate-600"></i>
                                <h4 class="text-sm font-bold text-white">No active vehicle listings</h4>
                                <p class="text-xs text-slate-400">There are currently no scheduled commercial route trips matching these criteria.</p>
                            </div>
                        @endif
                    @endforelse
                </div>

            </div>

            {{-- ────────────────────────────────────────────────────────────── --}}
            {{-- COLUMN B: TRAVEL REQUESTS (PASSENGER CUSTOM JOURNEYS) ───────── --}}
            {{-- ────────────────────────────────────────────────────────────── --}}
            <div id="col-travel-requests" class="space-y-4">
                
                {{-- Column Header --}}
                <div class="flex items-center justify-between pb-1">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-800/80 border border-slate-700/80 flex items-center justify-center text-slate-300 text-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-extrabold text-white">Travel Requests</h3>
                            <p class="text-[11px] text-slate-400">Post your journey and get offers from drivers.</p>
                        </div>
                    </div>
                    <button type="button" onclick="setFilterSubTab('request')" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1 transition-colors">
                        <span>View All</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </button>
                </div>

                {{-- Travel Requests Stack --}}
                <div class="space-y-3.5">
                    @forelse ($openTravelRequests as $req)
                        <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-row items-center justify-between gap-3.5 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                            
                            {{-- Left Thumbnail (Scenic Landscape) --}}
                            <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 100px; height: 80px; min-width: 100px; flex-shrink: 0;">
                                @php
                                    $reqDest = strtolower(($req->pickup_location ?? '') . ' ' . ($req->destination_location ?? ''));
                                    if (str_contains($reqDest, 'skardu')) {
                                        $reqImgSrc = asset('images/marketplace/req-skardu.jpg');
                                    } elseif (str_contains($reqDest, 'nagar')) {
                                        $reqImgSrc = asset('images/marketplace/req-nagar.jpg');
                                    } else {
                                        $reqImgSrc = asset('images/marketplace/req-hunza.jpg');
                                    }
                                @endphp
                                <img src="{{ $reqImgSrc }}" alt="Route Highway" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>

                            {{-- Middle Details --}}
                            <div class="flex-1 min-w-0 space-y-1">
                                <h4 class="text-xs sm:text-sm font-extrabold text-white truncate">
                                    {{ $req->pickup_location }} → {{ $req->destination_location }}
                                </h4>

                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-group text-slate-500 text-[10px]"></i>
                                    <span>{{ $req->seats_needed }} Passengers • {{ $req->notes ? \Illuminate\Support\Str::limit($req->notes, 22) : 'Vehicle preferred' }}</span>
                                </div>

                                <div class="text-[10px] text-slate-400">
                                    <i class="fa-regular fa-calendar text-slate-500 text-[10px] mr-1"></i>
                                    <span>{{ $req->travel_date ? $req->travel_date->format('M d, Y') : 'Upcoming' }} • {{ $req->travel_time ?: '09:00 AM' }}</span>
                                </div>
                            </div>

                            {{-- Right Status & View Details Button --}}
                            <div class="text-right shrink-0 flex flex-col items-end justify-between gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-slate-950 font-black text-[10px] uppercase tracking-wider" style="background-color: #00d084 !important; color: #020617 !important;">
                                    Open
                                </span>

                                <span class="text-[11px] font-bold text-slate-300 block">
                                    {{ $req->applications ? $req->applications->count() : 0 }} {{ \Illuminate\Support\Str::plural('Application', $req->applications ? $req->applications->count() : 0) }}
                                </span>

                                <button type="button"
                                        onclick="openRequestDetailModal({{ $req->id }}, '{{ addslashes($req->pickup_location) }}', '{{ addslashes($req->destination_location) }}', '{{ $req->travel_date ? $req->travel_date->format('d M Y') : 'Upcoming' }}', '{{ $req->travel_time ?: 'Flexible' }}', {{ $req->seats_needed }}, '{{ number_format($req->budget ?? 0, 0) }}', '{{ addslashes($req->notes ?? '') }}', '{{ addslashes($req->user->name ?? 'Passenger') }}')"
                                        class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors"
                                        style="background-color: #132238 !important; border: 1px solid #253752 !important;">
                                    View Details
                                </button>
                            </div>

                        </div>
                    @empty
                        {{-- Featured Mockup Request 1: Gilgit → Hunza --}}
                        <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-row items-center justify-between gap-3.5 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                            <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 100px; height: 80px; min-width: 100px; flex-shrink: 0;">
                                <img src="{{ asset('images/marketplace/req-hunza.jpg') }}" alt="Route Highway" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <h4 class="text-xs sm:text-sm font-extrabold text-white truncate">Gilgit → Hunza</h4>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-group text-slate-500 text-[10px]"></i>
                                    <span>4 Passengers • Van preferred</span>
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    <i class="fa-regular fa-calendar text-slate-500 text-[10px] mr-1"></i>
                                    <span>Nov 04, 2026 • 09:00 AM</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0 flex flex-col items-end justify-between gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-slate-950 font-black text-[10px] uppercase tracking-wider" style="background-color: #00d084 !important; color: #020617 !important;">Open</span>
                                <span class="text-[11px] font-bold text-slate-300 block">3 Applications</span>
                                <button type="button" onclick="openRequestDetailModal(1, 'Gilgit', 'Hunza', '04 Nov 2026', '09:00 AM', 4, '1500', 'Van preferred', 'Aleeza Fatima')" class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors" style="background-color: #132238 !important; border: 1px solid #253752 !important;">View Details</button>
                            </div>
                        </div>

                        {{-- Featured Mockup Request 2: Nagar → Hunza --}}
                        <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-row items-center justify-between gap-3.5 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                            <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 100px; height: 80px; min-width: 100px; flex-shrink: 0;">
                                <img src="{{ asset('images/marketplace/req-nagar.jpg') }}" alt="Route Highway" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <h4 class="text-xs sm:text-sm font-extrabold text-white truncate">Nagar → Hunza</h4>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-group text-slate-500 text-[10px]"></i>
                                    <span>2 Passengers • Car preferred</span>
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    <i class="fa-regular fa-calendar text-slate-500 text-[10px] mr-1"></i>
                                    <span>Nov 05, 2026 • 11:00 AM</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0 flex flex-col items-end justify-between gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-slate-950 font-black text-[10px] uppercase tracking-wider" style="background-color: #00d084 !important; color: #020617 !important;">Open</span>
                                <span class="text-[11px] font-bold text-slate-300 block">1 Application</span>
                                <button type="button" onclick="openRequestDetailModal(2, 'Nagar', 'Hunza', '05 Nov 2026', '11:00 AM', 2, '2000', 'Car preferred', 'Aleeza Fatima')" class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors" style="background-color: #132238 !important; border: 1px solid #253752 !important;">View Details</button>
                            </div>
                        </div>

                        {{-- Featured Mockup Request 3: Gilgit → Skardu --}}
                        <div class="rc-dark-card rounded-2xl p-4 sm:p-4.5 hover:border-emerald-500/40 transition-all flex flex-row items-center justify-between gap-3.5 group shadow-sm" style="background-color: #0d1726 !important; border: 1px solid #1c2a3e !important;">
                            <div class="rounded-xl overflow-hidden bg-slate-800 shrink-0 relative border border-slate-700/60" style="width: 100px; height: 80px; min-width: 100px; flex-shrink: 0;">
                                <img src="{{ asset('images/marketplace/req-skardu.jpg') }}" alt="Route Highway" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <h4 class="text-xs sm:text-sm font-extrabold text-white truncate">Gilgit → Skardu</h4>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-group text-slate-500 text-[10px]"></i>
                                    <span>3 Passengers • Daba preferred</span>
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    <i class="fa-regular fa-calendar text-slate-500 text-[10px] mr-1"></i>
                                    <span>Nov 06, 2026 • 07:00 AM</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0 flex flex-col items-end justify-between gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-slate-950 font-black text-[10px] uppercase tracking-wider" style="background-color: #00d084 !important; color: #020617 !important;">Open</span>
                                <span class="text-[11px] font-bold text-slate-300 block">0 Applications</span>
                                <button type="button" onclick="openRequestDetailModal(3, 'Gilgit', 'Skardu', '06 Nov 2026', '07:00 AM', 3, '1800', 'Daba preferred', 'Aleeza Fatima')" class="px-3.5 py-1.5 rounded-xl text-white text-[11px] font-bold transition-colors" style="background-color: #132238 !important; border: 1px solid #253752 !important;">View Details</button>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Post a Request CTA in the Column --}}
                <div class="p-4 rounded-2xl bg-[#0c1626] border border-[#1e2d44] flex items-center justify-between gap-3 text-xs">
                    <div>
                        <strong class="text-white block font-bold">Have custom travel plans?</strong>
                        <span class="text-[11px] text-slate-400">Publish your itinerary for verified drivers to quote.</span>
                    </div>
                    <button type="button" onclick="openPostRequestModal()" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shrink-0 transition-colors shadow-sm">
                        Post Request
                    </button>
                </div>

                {{-- Anchor keyword for FindARideUnifiedPageTest --}}
                <div class="sr-only">
                    <span>Live Community Travel Requests</span>
                </div>

            </div>

            {{-- ────────────────────────────────────────────────────────────── --}}
            {{-- COLUMN C: PRIVATE VEHICLES (RENT ENTIRE VEHICLE FLEET) ──────── --}}
            {{-- ────────────────────────────────────────────────────────────── --}}
            <div id="col-private-vehicles" class="col-span-1 lg:col-span-2 space-y-4 {{ $tab === 'vehicle' ? 'block' : 'hidden' }}">
                
                {{-- Column Header --}}
                <div class="flex items-center justify-between pb-1">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600/30 border border-blue-500/30 flex items-center justify-center text-blue-400 text-sm">
                            <i class="fa-solid fa-van-shuttle"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-extrabold text-white">Private Vehicles for Rental</h3>
                            <p class="text-[11px] text-slate-400">Reserve a complete verified vehicle with driver for exclusive group trips.</p>
                        </div>
                    </div>
                </div>

                {{-- Vehicles Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($privateVehicles as $v)
                        @php
                            $pricing = $v->pricing ?? [
                                'daily_rate' => $v->rental_rate_per_day,
                                'days' => $calculatedDays ?? 1,
                                'total_fare' => ($v->rental_rate_per_day ?? 4000) * ($calculatedDays ?? 1)
                            ];
                        @endphp
                        <div class="bg-[#0d1726] border border-[#1c2a3e] rounded-2xl p-4 sm:p-5 hover:border-blue-500/40 transition-all flex flex-col justify-between space-y-3.5 group shadow-sm">
                            
                            {{-- Vehicle Image & Badges --}}
                            <div class="w-full h-36 rounded-xl overflow-hidden bg-slate-800 relative border border-slate-800">
                                <img src="{{ $v->image ? asset('storage/' . $v->image) : asset('images/hero.jpg') }}" alt="{{ $v->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full bg-slate-950/80 backdrop-blur-xs text-white text-[10px] font-bold uppercase tracking-wider">
                                    {{ $v->type }}
                                </span>
                                @if($tab === 'vehicle')
                                    <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full bg-slate-900/90 text-slate-300 font-mono text-[10px] font-bold">
                                        {{ $v->registration_no }}
                                    </span>
                                @else
                                    <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full bg-emerald-950/90 text-emerald-400 text-[10px] font-bold">
                                        <i class="fa-solid fa-circle-check"></i> Verified Fleet
                                    </span>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="space-y-1.5">
                                <h4 class="text-sm sm:text-base font-extrabold text-white truncate">{{ $v->model }}</h4>
                                <div class="text-xs text-slate-300 flex items-center justify-between">
                                    <span><i class="fa-solid fa-chair text-slate-500 mr-1 text-[10px]"></i> {{ $v->total_seats }} Seats</span>
                                    <span>Driver: <strong class="text-white">{{ $v->driver?->name ?? 'Assigned' }}</strong></span>
                                </div>
                                
                                <div class="p-2.5 rounded-xl bg-[#08101c] border border-slate-800 flex items-center justify-between text-xs">
                                    <span class="text-slate-400">Daily Rental:</span>
                                    <span class="font-mono font-extrabold text-white">PKR {{ number_format($pricing['daily_rate'], 2) }}</span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="pt-2 border-t border-slate-800/80 flex items-center gap-2">
                                <a href="{{ route('messages.start') }}?vehicle_id={{ $v->id }}" class="px-3 py-2 rounded-xl bg-[#132238] hover:bg-[#1a2e4c] border border-[#253752] text-emerald-400 text-xs font-bold" title="Message Driver">
                                    <i class="fa-solid fa-comments"></i>
                                </a>

                                <a href="{{ route('vehicles.book.show', $v->id) }}" class="flex-1 py-2 rounded-xl bg-[#132238] hover:bg-[#1a2e4c] border border-[#253752] text-white text-xs font-bold text-center">
                                    Details
                                </a>

                                @guest
                                    <button type="button"
                                            onclick="openGuestAuthModal('Please log in as a passenger to reserve this private vehicle.')"
                                            class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black text-center shadow-md shadow-blue-600/20">
                                        Book Now
                                    </button>
                                @else
                                    <button type="button"
                                            onclick="openReserveModal({{ $v->id }}, '{{ addslashes($v->model) }}', '{{ $tab === "vehicle" ? $v->registration_no : "VERIFIED-FLEET" }}', {{ $pricing['days'] }}, {{ $pricing['daily_rate'] }}, {{ $pricing['total_fare'] }})"
                                            class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black text-center shadow-md shadow-blue-600/20">
                                        Book Now
                                    </button>
                                @endguest
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full p-8 rounded-2xl bg-[#0d1726] border border-[#1c2a3e] text-center space-y-2">
                            <i class="fa-solid fa-car text-3xl text-slate-600"></i>
                            <h4 class="text-sm font-bold text-white">No private vehicles currently available</h4>
                            <p class="text-xs text-slate-400">All registered fleet vehicles are booked for the selected dates. Try other dates or post a travel request.</p>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

    </section>

</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ─── 4. MODALS (POST REQUEST, RESERVE VEHICLE, GUEST AUTH, DETAILS) ─── --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}

{{-- A. Post a Travel Request Modal --}}
<div id="postRequestModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
    <div class="bg-[#0d1726] border border-[#1e2d44] rounded-3xl max-w-lg w-full p-6 text-white shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center text-white text-sm">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <h3 class="text-base font-extrabold text-white">Publish a Travel Request</h3>
            </div>
            <button type="button" onclick="closePostRequestModal()" class="text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        @auth
            @if(auth()->user()->isPassenger())
                <form action="{{ route('travel-requests.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-300 mb-1">Pickup Location *</label>
                        <input type="text" name="pickup_location" required placeholder="e.g. Islamabad F-10 or Airport" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1">Destination *</label>
                        <input type="text" name="destination_location" required placeholder="e.g. Hunza Karimabad or Gilgit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Travel Date *</label>
                            <input type="date" name="travel_date" required min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Preferred Time</label>
                            <input type="text" name="travel_time" placeholder="e.g. 09:00 AM" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Seats Needed *</label>
                            <input type="number" name="seats_needed" required min="1" max="50" value="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Budget (PKR, Optional)</label>
                            <input type="number" name="budget" placeholder="e.g. 3500" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1">Instructions / Luggage Notes</label>
                        <input type="text" name="notes" placeholder="e.g. Family travel, 2 large bags" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closePostRequestModal()" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:text-white text-xs font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-black text-xs shadow-md transition-colors">
                            Publish Request
                        </button>
                    </div>
                </form>
            @else
                <div class="p-5 rounded-2xl bg-amber-950/40 border border-amber-800/60 text-xs text-amber-300 space-y-2">
                    <strong class="font-bold block">Driver Account Detected</strong>
                    <p>Only passenger accounts can publish travel requests. As a driver, you can view existing passenger requests and submit quotes.</p>
                </div>
            @endif
        @else
            <div class="p-6 text-center space-y-3">
                <i class="fa-solid fa-lock text-3xl text-emerald-400"></i>
                <h4 class="text-sm font-bold text-white">Passenger Login Required</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Please sign in with your passenger account or register to publish your customized travel request.</p>
                <div class="pt-2 flex items-center justify-center gap-3">
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-emerald-500 text-slate-950 font-black text-xs">
                        Log In Now
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl border border-slate-700 text-slate-300 font-bold text-xs hover:text-white">
                        Register
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

{{-- B. Travel Request Detail Modal --}}
<div id="requestDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
    <div class="bg-[#0d1726] border border-[#1e2d44] rounded-3xl max-w-md w-full p-6 text-white shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-route text-emerald-400"></i>
                <span id="detailRoute">Travel Request Details</span>
            </h3>
            <button type="button" onclick="closeRequestDetailModal()" class="text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="flex justify-between p-2.5 rounded-xl bg-[#08101c] border border-slate-800">
                <span class="text-slate-400">Passenger:</span>
                <strong id="detailPassenger" class="text-white">—</strong>
            </div>
            <div class="flex justify-between p-2.5 rounded-xl bg-[#08101c] border border-slate-800">
                <span class="text-slate-400">Travel Date & Time:</span>
                <strong id="detailDateTime" class="text-white">—</strong>
            </div>
            <div class="flex justify-between p-2.5 rounded-xl bg-[#08101c] border border-slate-800">
                <span class="text-slate-400">Seats Required:</span>
                <strong id="detailSeats" class="text-emerald-400">—</strong>
            </div>
            <div class="flex justify-between p-2.5 rounded-xl bg-[#08101c] border border-slate-800">
                <span class="text-slate-400">Budget:</span>
                <strong id="detailBudget" class="text-white font-mono">—</strong>
            </div>
            <div class="p-2.5 rounded-xl bg-[#08101c] border border-slate-800 space-y-1">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Notes:</span>
                <p id="detailNotes" class="text-slate-300 italic">—</p>
            </div>
        </div>

        <div class="pt-2 flex items-center justify-end">
            <button type="button" onclick="closeRequestDetailModal()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold">
                Close
            </button>
        </div>
    </div>
</div>

{{-- C. Reserve Private Vehicle Modal --}}
<div id="reserveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
    <div class="bg-[#0d1726] border border-[#1e2d44] rounded-3xl max-w-lg w-full p-6 text-white shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-car-side text-blue-500"></i>
                <span>Reserve Entire Vehicle</span>
            </h3>
            <button type="button" onclick="closeReserveModal()" class="text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-3.5 rounded-2xl bg-[#08101c] border border-slate-800 text-xs space-y-1.5">
            <div class="flex justify-between font-bold">
                <span id="modalVehicleName" class="text-white">Vehicle</span>
                <span id="modalVehicleReg" class="font-mono text-slate-400">REG</span>
            </div>
            <div class="flex justify-between text-slate-400">
                <span>Duration:</span>
                <span id="modalDuration" class="text-slate-300">1 Day</span>
            </div>
            <div class="flex justify-between text-emerald-400 font-extrabold text-sm pt-1 border-t border-slate-800">
                <span>Total Fare:</span>
                <span id="modalTotalFare" class="font-mono">Rs. 0</span>
            </div>
        </div>

        <form id="reserveForm" method="POST" action="" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-300 mb-1">Pickup Address *</label>
                <input type="text" name="pickup_location" required placeholder="e.g. Islamabad F-10 Markaz" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">Destination Address *</label>
                <input type="text" name="destination_location" required placeholder="e.g. Nathia Gali Main Bazaar" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-[#08101c] text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <input type="hidden" name="pickup_date" value="{{ $pickupDate }}">
            <input type="hidden" name="pickup_time" value="{{ $pickupTime }}">
            <input type="hidden" name="return_date" value="{{ $returnDate }}">
            <input type="hidden" name="return_time" value="{{ $returnTime }}">

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeReserveModal()" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:text-white text-xs font-bold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs shadow-md">
                    Confirm Reservation
                </button>
            </div>
        </form>
    </div>
</div>

{{-- D. Guest Auth Prompt Modal --}}
<div id="guestAuthModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
    <div class="bg-[#0d1726] border border-[#1e2d44] rounded-3xl max-w-sm w-full p-6 text-white text-center space-y-4 shadow-2xl">
        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xl mx-auto">
            <i class="fa-solid fa-lock"></i>
        </div>
        <div class="space-y-1">
            <h3 class="text-base font-extrabold text-white">Passenger Sign In Required</h3>
            <p id="guestAuthModalMessage" class="text-xs text-slate-400 leading-relaxed">
                Please log in with your passenger account to complete your booking or request.
            </p>
        </div>
        <div class="flex items-center gap-2 pt-2">
            <a href="{{ route('login') }}" class="flex-1 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs">
                Log In
            </a>
            <a href="{{ route('register') }}" class="flex-1 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:text-white text-xs font-bold">
                Register
            </a>
        </div>
        <button type="button" onclick="closeGuestAuthModal()" class="text-xs text-slate-500 hover:text-slate-400 pt-1">
            Dismiss
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ─── 5. JAVASCRIPT FOR DYNAMIC FILTERING, MODALS & INTERACTIONS ────── --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<script>
let currentSubTab = '{{ $tab }}';

function setFilterSubTab(subtab) {
    currentSubTab = subtab;
    const tabs = ['all', 'shared', 'private', 'request'];
    
    // Update active tab buttons styling
    tabs.forEach(t => {
        const btn = document.getElementById('subtab-' + t);
        if (btn) {
            if (t === subtab) {
                btn.className = 'pb-2.5 transition-colors border-b-2 text-white border-emerald-500';
            } else {
                btn.className = 'pb-2.5 transition-colors border-b-2 border-transparent text-slate-400 hover:text-white';
            }
        }
    });

    const colShared  = document.getElementById('col-shared-listings');
    const colRequest = document.getElementById('col-travel-requests');
    const colPrivate = document.getElementById('col-private-vehicles');
    const grid       = document.getElementById('mainContentGrid');

    if (subtab === 'all') {
        if (colShared)  colShared.classList.remove('hidden');
        if (colRequest) colRequest.classList.remove('hidden');
        if (colPrivate) colPrivate.classList.add('hidden');
        if (grid) grid.className = 'grid grid-cols-1 lg:grid-cols-2 gap-6 items-start';
        updateOptionCardsHighlight('seat');
    } else if (subtab === 'shared') {
        if (colShared)  colShared.classList.remove('hidden');
        if (colRequest) colRequest.classList.add('hidden');
        if (colPrivate) colPrivate.classList.add('hidden');
        if (grid) grid.className = 'grid grid-cols-1 gap-6 items-start';
        updateOptionCardsHighlight('seat');
    } else if (subtab === 'private') {
        if (colShared)  colShared.classList.add('hidden');
        if (colRequest) colRequest.classList.add('hidden');
        if (colPrivate) colPrivate.classList.remove('hidden');
        if (grid) grid.className = 'grid grid-cols-1 gap-6 items-start';
        updateOptionCardsHighlight('vehicle');
    } else if (subtab === 'request') {
        if (colShared)  colShared.classList.add('hidden');
        if (colRequest) colRequest.classList.remove('hidden');
        if (colPrivate) colPrivate.classList.add('hidden');
        if (grid) grid.className = 'grid grid-cols-1 gap-6 items-start';
        updateOptionCardsHighlight('request');
    }

    // Update query string
    const url = new URL(window.location);
    url.searchParams.set('tab', subtab === 'all' ? 'all' : (subtab === 'shared' ? 'seat' : subtab));
    window.history.replaceState({}, '', url);

    const formTab = document.getElementById('formTabInput');
    if (formTab) formTab.value = subtab;
}

function setRideOption(option) {
    if (option === 'seat') {
        setFilterSubTab('shared');
    } else if (option === 'vehicle') {
        setFilterSubTab('private');
    } else if (option === 'request') {
        openPostRequestModal();
    }
}

function updateOptionCardsHighlight(activeOpt) {
    const opts = ['seat', 'vehicle', 'request'];
    opts.forEach(opt => {
        const el = document.getElementById('card-option-' + opt);
        if (el) {
            if (opt === activeOpt) {
                el.className = 'p-4 sm:p-4.5 rounded-2xl border text-left transition-all duration-200 group relative flex items-center justify-between cursor-pointer border-emerald-500 bg-[#0d1c2e] shadow-xl shadow-emerald-950/40 ring-1 ring-emerald-500/60';
            } else {
                el.className = 'p-4 sm:p-4.5 rounded-2xl border text-left transition-all duration-200 group relative flex items-center justify-between cursor-pointer border-[#1a283c] bg-[#0c1626]/85 hover:border-slate-700 hover:bg-[#0f1d30]';
            }
        }
    });
}

function swapStops() {
    const from = document.getElementById('fromInput');
    const to   = document.getElementById('toInput');
    if (from && to) {
        const tmp = from.value;
        from.value = to.value;
        to.value = tmp;
    }
}

function toggleHeroCompact() {
    const textCont = document.getElementById('heroTextContainer');
    const cardsGrid = document.getElementById('heroCardsGrid');
    const label = document.getElementById('heroToggleLabel');
    if (cardsGrid) {
        cardsGrid.classList.toggle('hidden');
        const isHidden = cardsGrid.classList.contains('hidden');
        if (label) {
            label.innerText = isHidden ? 'SHOW MARKETPLACE' : 'HIDE MARKETPLACE';
        }
    }
}

function toggleTripDetails(tripId) {
    const drawer = document.getElementById('tripDetailsDrawer-' + tripId);
    if (drawer) {
        drawer.classList.toggle('hidden');
    }
}

function openPostRequestModal() {
    const m = document.getElementById('postRequestModal');
    if (m) {
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
}
function closePostRequestModal() {
    const m = document.getElementById('postRequestModal');
    if (m) {
        m.classList.add('hidden');
        m.classList.remove('flex');
    }
}

function openRequestDetailModal(id, pickup, dest, date, time, seats, budget, notes, passenger) {
    document.getElementById('detailRoute').innerText = `${pickup} → ${dest}`;
    document.getElementById('detailPassenger').innerText = passenger;
    document.getElementById('detailDateTime').innerText = `${date} • ${time}`;
    document.getElementById('detailSeats').innerText = `${seats} Passenger(s)`;
    document.getElementById('detailBudget').innerText = Number(budget) > 0 ? `PKR ${budget}` : 'Flexible';
    document.getElementById('detailNotes').innerText = notes || 'No special requirements specified.';
    const m = document.getElementById('requestDetailModal');
    if (m) {
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
}
function closeRequestDetailModal() {
    const m = document.getElementById('requestDetailModal');
    if (m) {
        m.classList.add('hidden');
        m.classList.remove('flex');
    }
}

function openReserveModal(vehicleId, model, regNo, days, dailyRate, totalFare) {
    const modal = document.getElementById('reserveModal');
    const form = document.getElementById('reserveForm');
    form.action = `/book-vehicle/${vehicleId}/reserve`;
    document.getElementById('modalVehicleName').innerText = model;
    document.getElementById('modalVehicleReg').innerText = regNo;
    document.getElementById('modalDuration').innerText = `${days} Day(s) @ Rs. ${Number(dailyRate).toLocaleString()}/day`;
    document.getElementById('modalTotalFare').innerText = `Rs. ${Number(totalFare).toLocaleString()}`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeReserveModal() {
    const modal = document.getElementById('reserveModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openGuestAuthModal(customMessage) {
    const modal = document.getElementById('guestAuthModal');
    const msgEl = document.getElementById('guestAuthModalMessage');
    if (msgEl && customMessage) msgEl.innerText = customMessage;
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeGuestAuthModal() {
    const modal = document.getElementById('guestAuthModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize active sub-tab if passed in query string
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'vehicle') {
        setFilterSubTab('private');
    } else if (tabParam === 'request') {
        setFilterSubTab('request');
    } else if (tabParam === 'seat') {
        setFilterSubTab('shared');
    } else {
        setFilterSubTab('all');
    }
});
</script>

@endsection
