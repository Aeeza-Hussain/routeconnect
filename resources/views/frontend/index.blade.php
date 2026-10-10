@extends('frontend.layouts.app')

@section('title', 'RouteConnect — Smart Transport Scheduling & Seat Booking')

@section('content')

{{-- ─── 1. HERO SECTION ─── --}}
<section class="relative bg-slate-950 text-white overflow-hidden pt-12 pb-24 lg:pt-18 lg:pb-32">
    {{-- Background Image with Deep Navy Transport Overlay --}}
    <div class="absolute inset-0 z-0">
        <img src="/images/hero.jpg" alt="RouteConnect Highway Transit" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/85 to-slate-900/60"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl space-y-6">
            
            {{-- Transport-Tech Status Pill --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-extrabold uppercase tracking-wider backdrop-blur-xs">
                <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                <span>Scheduled Transport Network</span>
            </div>

            {{-- Main Headline --}}
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                Travel with confidence. <br>
                <span class="text-teal-400">
                    Know your route before you leave.
                </span>
            </h1>

            {{-- Subtitle --}}
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-medium">
                Find scheduled passenger vehicles, inspect expected stop arrival timings, see real-time seat availability, and reserve guaranteed seats in advance.
            </p>

            {{-- Hero Quick Actions --}}
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="#searchCard" class="px-6 py-3.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-extrabold text-xs shadow-lg shadow-teal-600/30 flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-magnifying-glass"></i> Search Available Trips
                </a>
                <a href="{{ route('register', ['role' => 'driver']) }}" class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs border border-white/20 backdrop-blur-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-id-card text-teal-300"></i> Register as Driver
                </a>
            </div>

        </div>
    </div>
</section>


{{-- ─── 2. PROMINENT TRIP SEARCH CARD ─── --}}
<section id="searchCard" class="relative z-20 -mt-14 sm:-mt-18 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200/90 dark:border-slate-800 text-slate-900 dark:text-slate-100 transition-colors">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm border border-teal-100 dark:border-teal-900/50">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white">Find Your Scheduled Journey</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Search route schedules across all verified stops</p>
                </div>
            </div>

            <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold flex items-center gap-2">
                <i class="fa-solid fa-check-double text-teal-600 dark:text-teal-400"></i>
                <span>Direct stop-order verification</span>
            </div>
        </div>

        <form action="{{ url('/trips') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            
            {{-- From Stop --}}
            <div class="lg:col-span-3">
                <label for="heroFromInput" class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-dot text-teal-600 dark:text-teal-400 text-[10px]"></i>
                    <span>From (Boarding Stop)</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                    <input type="text"
                           name="from"
                           id="heroFromInput"
                           list="heroFromList"
                           placeholder="e.g. Gilgit or Nagar"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                    <datalist id="heroFromList">
                        @foreach ($allStops as $s)
                            <option value="{{ $s->name }}">{{ $s->name }} ({{ $s->location ?? 'Stop' }})</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            {{-- Swap Button (Mobile hidden, desktop inline) --}}
            <div class="hidden lg:flex lg:col-span-1 items-center justify-center pb-2">
                <button type="button"
                        onclick="swapStops()"
                        title="Swap Origin and Destination"
                        class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-700 dark:hover:text-teal-400 text-slate-500 dark:text-slate-400 transition-colors border border-slate-200 dark:border-slate-700 cursor-pointer">
                    <i class="fa-solid fa-right-left text-xs"></i>
                </button>
            </div>

            {{-- To Stop --}}
            <div class="lg:col-span-3">
                <label for="heroToInput" class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-location-pin text-rose-500 text-[10px]"></i>
                    <span>To (Destination Stop)</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                    <input type="text"
                           name="to"
                           id="heroToInput"
                           list="heroToList"
                           placeholder="e.g. Hunza or Aliabad"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                    <datalist id="heroToList">
                        @foreach ($allStops as $s)
                            <option value="{{ $s->name }}">{{ $s->name }} ({{ $s->location ?? 'Stop' }})</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            {{-- Date --}}
            <div class="lg:col-span-3">
                <label for="heroDateInput" class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar text-teal-600 dark:text-teal-400 text-[10px]"></i>
                    <span>Departure Date</span>
                </label>
                <div class="relative">
                    <input type="date"
                           name="date"
                           id="heroDateInput"
                           value="{{ date('Y-m-d') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
            </div>

            {{-- Search Button --}}
            <div class="lg:col-span-2">
                <button type="submit"
                        class="w-full py-3 px-5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs shadow-md shadow-teal-600/20 flex items-center justify-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Search Trips</span>
                </button>
            </div>

        </form>

        {{-- Quick Corridor Chips --}}
        <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
            <span class="font-extrabold text-slate-400 dark:text-slate-500 text-[11px]">Popular Routes:</span>
            <a href="{{ url('/trips?from=Gilgit&to=Hunza') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 text-[11px] font-bold transition-colors">
                Gilgit → Hunza
            </a>
            <a href="{{ url('/trips?from=Nagar&to=Hunza') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 text-[11px] font-bold transition-colors">
                Nagar → Hunza
            </a>
            <a href="{{ url('/trips?from=Gilgit&to=Aliabad') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 text-[11px] font-bold transition-colors">
                Gilgit → Aliabad
            </a>
            <a href="{{ url('/trips') }}" class="ml-auto text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 font-extrabold flex items-center gap-1 text-[11px]">
                <span>View All Departures</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

    </div>
</section>


{{-- ─── 3. FEATURED SCHEDULED TRIPS ─── --}}
<section class="py-16 bg-slate-50 dark:bg-[#070D18] transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-200 dark:border-teal-800">
                    <i class="fa-solid fa-calendar-check text-teal-600 dark:text-teal-400"></i> Live Departures
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Upcoming Scheduled Trips</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Verified vehicles operating on active corridors with confirmed departure dates.</p>
            </div>
            <div>
                <a href="{{ url('/trips') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-extrabold border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <span>Full Schedule Timetable</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        @if ($featuredTrips->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($featuredTrips as $ft)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                        
                        <div class="space-y-3">
                            {{-- Top pill row --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-[10px] font-black border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ ucfirst($ft->status) }}
                                </span>
                                <span class="text-[11px] font-mono font-bold text-slate-400 dark:text-slate-500">
                                    #{{ $ft->id }}
                                </span>
                            </div>

                            {{-- Route Origin → Destination --}}
                            <div>
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                    {{ $ft->route->name ?? 'Corridor Route' }}
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                    <span class="truncate max-w-[100px]">{{ $ft->route->origin ?? 'Origin' }}</span>
                                    <i class="fa-solid fa-arrow-right text-[10px] text-teal-600 dark:text-teal-400"></i>
                                    <span class="truncate max-w-[100px]">{{ $ft->route->destination ?? 'Destination' }}</span>
                                </div>
                            </div>

                            {{-- Schedule --}}
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-1">
                                <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                                    <span class="text-slate-400 dark:text-slate-500 font-medium">Date:</span>
                                    <span class="font-bold">{{ $ft->trip_date ? \Carbon\Carbon::parse($ft->trip_date)->format('D, d M') : '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                                    <span class="text-slate-400 dark:text-slate-500 font-medium">Departure:</span>
                                    <span class="font-bold font-mono">{{ $ft->departure_time ? \Carbon\Carbon::parse($ft->departure_time)->format('h:i A') : '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                                    <span class="text-slate-400 dark:text-slate-500 font-medium">Available Seats:</span>
                                    <span class="font-black text-teal-700 dark:text-teal-400 font-mono">{{ $ft->available_seats }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer CTA --}}
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-bold block">Fare</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white">Rs. {{ number_format($ft->fare ?? 0) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('trips.show', $ft->id) }}" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors" title="View Stops">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('booking.create', $ft->id) }}" class="px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs transition-colors">
                                    Book Seat
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 border border-slate-200 dark:border-slate-800 text-center max-w-lg mx-auto">
                <i class="fa-solid fa-bus text-slate-400 dark:text-slate-500 text-2xl mb-2 block"></i>
                <h4 class="font-extrabold text-slate-800 dark:text-slate-200 text-sm">Scheduled Departures Operating Daily</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4">Use the search tool above to find journeys for your preferred travel dates.</p>
                <a href="{{ url('/trips') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 dark:bg-slate-800 text-white text-xs font-bold">
                    <span>Search Scheduled Departures</span>
                </a>
            </div>
        @endif

    </div>
</section>


{{-- ─── 4. CORE PLATFORM PILLARS ─── --}}
<section class="py-16 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-xl mx-auto mb-12">
            <span class="text-xs font-extrabold uppercase tracking-wider text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/40 px-3 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                Predictable Transit
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-3">Everything you need for a smoother journey</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Built specifically for commercial public transport routes and stop certainty.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Pillar 1 --}}
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center text-base font-bold mb-4">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Scheduled Trips</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Find commercial vehicles operating on verified routes according to published departure timetables.
                </p>
            </div>

            {{-- Pillar 2 --}}
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-base font-bold mb-4">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Expected Stop Times</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Know when your vehicle is expected to reach each intermediate stop before you leave home.
                </p>
            </div>

            {{-- Pillar 3 --}}
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-base font-bold mb-4">
                    <i class="fa-solid fa-chair"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Seat Availability</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Check real-time remaining seat counts with atomic locking that prevents overbooking.
                </p>
            </div>

            {{-- Pillar 4 --}}
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center text-base font-bold mb-4">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Instant Digital Passes</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Reserve seats in advance, generate verified digital booking references, and manage tickets anytime.
                </p>
            </div>

        </div>

    </div>
</section>


{{-- ─── 5. HOW ROUTECONNECT WORKS ─── --}}
<section id="about" class="py-16 lg:py-20 bg-slate-50 dark:bg-[#070D18] border-t border-slate-200/80 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-8 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/40 px-3 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                        Process
                    </span>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-3">How RouteConnect Works</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Simple, predictable transit booking in three straightforward steps.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-black text-xs flex items-center justify-center">01</span>
                            <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 flex items-center justify-center text-xs font-bold border border-teal-100 dark:border-teal-800">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                        </div>
                        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Search Corridor</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Input your boarding stop, destination, and travel date.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-black text-xs flex items-center justify-center">02</span>
                            <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 flex items-center justify-center text-xs font-bold border border-teal-100 dark:border-teal-800">
                                <i class="fa-solid fa-route"></i>
                            </div>
                        </div>
                        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Compare Schedule</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Inspect expected arrival times per stop and vehicle specs.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-black text-xs flex items-center justify-center">03</span>
                            <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 flex items-center justify-center text-xs font-bold border border-teal-100 dark:border-teal-800">
                                <i class="fa-solid fa-ticket"></i>
                            </div>
                        </div>
                        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Confirm & Ride</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Reserve your seats and access your digital boarding pass.
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative rounded-3xl overflow-hidden shadow-xl border border-slate-800 text-white min-h-[300px] flex flex-col justify-end p-8">
                <img src="/images/hero.jpg" alt="Mountain Transit Highway" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-950/40"></div>

                <div class="relative z-10 space-y-3">
                    <div class="w-8 h-1 bg-teal-400 rounded-full"></div>
                    <h3 class="text-xl font-extrabold text-white">Stop waiting without knowing.</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Instead of reaching a bus stop without knowing when a vehicle is expected or if seats are open, RouteConnect provides verified vehicle manifests and stop timetables before you leave home.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>


{{-- ─── 6. DRIVER PARTNER BANNER ─── --}}
<section class="py-12 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900 dark:bg-slate-950 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-8">
            
            <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
                <div class="w-20 h-20 rounded-2xl overflow-hidden border border-slate-700 shrink-0 shadow-sm">
                    <img src="/images/driver.jpg" alt="Commercial Driver" class="w-full h-full object-cover">
                </div>

                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 text-xs text-teal-400 font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-id-card"></i> Driver Partner Program
                    </div>
                    <h3 class="font-extrabold text-white text-xl">Drive with RouteConnect</h3>
                    <p class="text-xs text-slate-300 max-w-md leading-relaxed">
                        Own a commercial vehicle and want to offer scheduled transport? Submit your license and vehicle details for admin approval and start publishing trips.
                    </p>
                </div>
            </div>

            <div>
                <a href="{{ route('register', ['role' => 'driver']) }}" class="px-6 py-3.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs shadow-md transition-all flex items-center gap-2">
                    <span>Register as Driver</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- Client script to swap origin and destination --}}
<script>
    function swapStops() {
        const fromInput = document.getElementById('heroFromInput');
        const toInput = document.getElementById('heroToInput');
        if (fromInput && toInput) {
            const temp = fromInput.value;
            fromInput.value = toInput.value;
            toInput.value = temp;
        }
    }
</script>

@endsection
