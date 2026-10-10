@extends('frontend.layouts.app')

@section('title', 'Search Scheduled Trips — RouteConnect')

@section('content')

{{-- ─── 1. HERO SEARCH BANNER ─── --}}
<section class="relative bg-slate-950 text-white pt-10 pb-16 lg:pt-14 lg:pb-24 overflow-hidden border-b border-slate-800">
    <!-- Subtle gradient background overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950/40 opacity-90"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                <i class="fa-solid fa-bus text-emerald-400"></i> Public Route Search
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                Find Scheduled Trips
            </h1>
            <p class="text-slate-300 text-sm sm:text-base font-medium">
                Search verified commercial vehicles passing through your start and destination stops with real-time seat availability.
            </p>
        </div>
    </div>
</section>


{{-- ─── 2. SEARCH FILTER BAR ─── --}}
<section class="relative z-20 -mt-10 sm:-mt-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/90 dark:border-slate-800 text-slate-900 dark:text-slate-100 transition-colors">
        
        <form action="{{ url('/trips') }}" method="GET" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                
                {{-- 1. From Stop --}}
                <div class="lg:col-span-3">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="from" class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-dot text-emerald-600 dark:text-emerald-400 text-[10px]"></i>
                            <span>From</span>
                        </label>
                        <button type="button" onclick="swapTripStops()" class="text-[11px] text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 font-bold flex items-center gap-1 cursor-pointer" title="Swap stops">
                            <i class="fa-solid fa-right-left text-[9px]"></i> <span>Swap</span>
                        </button>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text"
                               name="from"
                               id="from"
                               list="fromStopsList"
                               value="{{ request('from', $from) }}"
                               placeholder="e.g. Nagar or Gilgit"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <datalist id="fromStopsList">
                            @foreach ($allStops as $s)
                                <option value="{{ $s->name }}">{{ $s->name }} ({{ $s->location ?? 'Stop' }})</option>
                            @endforeach
                            @if($allStops->isEmpty())
                                <option value="Gilgit">Gilgit</option>
                                <option value="Nagar">Nagar</option>
                                <option value="Hunza">Hunza</option>
                                <option value="Aliabad">Aliabad</option>
                                <option value="Danyor">Danyor</option>
                                <option value="Nomal">Nomal</option>
                            @endif
                        </datalist>
                    </div>
                </div>

                {{-- 2. To Stop --}}
                <div class="lg:col-span-3">
                    <label for="to" class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-pin text-rose-500 text-[10px]"></i>
                        <span>To</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text"
                               name="to"
                               id="to"
                               list="toStopsList"
                               value="{{ request('to', $to) }}"
                               placeholder="e.g. Hunza or Aliabad"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <datalist id="toStopsList">
                            @foreach ($allStops as $s)
                                <option value="{{ $s->name }}">{{ $s->name }} ({{ $s->location ?? 'Stop' }})</option>
                            @endforeach
                            @if($allStops->isEmpty())
                                <option value="Hunza">Hunza</option>
                                <option value="Aliabad">Aliabad</option>
                                <option value="Nagar">Nagar</option>
                                <option value="Gilgit">Gilgit</option>
                                <option value="Danyor">Danyor</option>
                                <option value="Nomal">Nomal</option>
                            @endif
                        </datalist>
                    </div>
                </div>

                {{-- 3. Date --}}
                <div class="lg:col-span-2">
                    <label for="date" class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar text-emerald-600 dark:text-emerald-400 text-[10px]"></i>
                        <span>Date</span>
                    </label>
                    <div class="relative">
                        <input type="date"
                               name="date"
                               id="date"
                               value="{{ request('date', $date) }}"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                    </div>
                </div>

                {{-- 4. Optional Preferred Time --}}
                <div class="lg:col-span-2">
                    <label for="time" class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-emerald-600 dark:text-emerald-400 text-[10px]"></i>
                        <span>Preferred Time</span>
                    </label>
                    <div class="relative">
                        <select name="time"
                                id="time"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                            <option value="">Any Time</option>
                            <option value="morning"   {{ request('time', $time) === 'morning'   ? 'selected' : '' }}>Morning (05:00 AM – 11:59 AM)</option>
                            <option value="afternoon" {{ request('time', $time) === 'afternoon' ? 'selected' : '' }}>Afternoon (12:00 PM – 04:59 PM)</option>
                            <option value="evening"   {{ request('time', $time) === 'evening'   ? 'selected' : '' }}>Evening (05:00 PM – 11:59 PM)</option>
                        </select>
                    </div>
                </div>

                {{-- Search Button --}}
                <div class="lg:col-span-2">
                    <button type="submit"
                            id="searchTripsBtn"
                            class="w-full py-3 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 transition-all hover:scale-[1.02] cursor-pointer">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search Trips</span>
                    </button>
                </div>

            </div>

            {{-- Quick Chips & Reset --}}
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 text-xs border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-bold text-slate-400 dark:text-slate-500 text-[11px]">Popular Corridors:</span>
                    <a href="{{ url('/trips?from=Gilgit&to=Hunza') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 text-[11px] font-semibold transition-colors">
                        Gilgit → Hunza
                    </a>
                    <a href="{{ url('/trips?from=Nagar&to=Hunza') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 text-[11px] font-semibold transition-colors">
                        Nagar → Hunza
                    </a>
                    <a href="{{ url('/trips?from=Gilgit&to=Aliabad') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 text-[11px] font-semibold transition-colors">
                        Gilgit → Aliabad
                    </a>
                </div>

                @if(request()->hasAny(['from', 'to', 'date', 'time']))
                    <a href="{{ url('/trips') }}" class="text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        <span>Reset Filters</span>
                    </a>
                @endif
            </div>

        </form>

    </div>
</section>


{{-- ─── 3. RESULTS SECTION ─── --}}
<section class="py-12 bg-slate-50 dark:bg-[#070D18] transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Query Summary Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <span>Trip Search Results</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black">
                        {{ $trips->count() }} {{ \Illuminate\Support\Str::plural('trip', $trips->count()) }} found
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    @if(!empty($from) || !empty($to) || !empty($date))
                        Showing scheduled transport routes matching 
                        @if(!empty($from))<strong>"{{ $from }}"</strong>@endif
                        @if(!empty($to)) to <strong>"{{ $to }}"</strong>@endif
                        @if(!empty($date)) on <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>@endif
                    @else
                        All currently active scheduled departures.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                <span>Real-time Seat Manifests</span>
            </div>
        </div>

        {{-- NO TRIPS FOUND MESSAGE --}}
        @if ($trips->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 sm:p-16 border border-slate-200/90 dark:border-slate-800 shadow-sm text-center max-w-2xl mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 border border-amber-200/60 dark:border-amber-900 flex items-center justify-center text-3xl mx-auto mb-4 shadow-sm">
                    <i class="fa-solid fa-route"></i>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">No trips found</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto mb-6">
                    No scheduled trips match your specified boarding stop, destination, and departure date criteria.
                    Try selecting an alternative date, clearing your time preference, or checking another nearby corridor stop.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ url('/trips') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold transition-colors">
                        View All Available Departures
                    </a>
                    <a href="{{ url('/') }}#searchCard" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors">
                        Modify Search Query
                    </a>
                </div>
            </div>

        {{-- MATCHING TRIPS LIST --}}
        @else
            <div class="space-y-5">
                @foreach ($trips as $trip)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
                        
                        {{-- Card Top Row: Route & Status & Fare --}}
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        {{ ucfirst($trip->status) }}
                                    </span>

                                    @if ($trip->route && $trip->route->name)
                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold">
                                            {{ $trip->route->name }}
                                        </span>
                                    @endif

                                    <span class="px-2.5 py-1 rounded-full bg-teal-50 dark:bg-teal-950/40 text-teal-800 dark:text-teal-300 text-[11px] font-bold font-mono">
                                        Trip #{{ $trip->id }}
                                    </span>
                                </div>

                                {{-- Main Route Title --}}
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                                    <span>{{ $trip->route->origin ?? 'Origin' }}</span>
                                    <i class="fa-solid fa-arrow-right text-sm text-emerald-600 dark:text-emerald-400"></i>
                                    <span>{{ $trip->route->destination ?? 'Destination' }}</span>
                                </h3>
                            </div>

                            {{-- Fare & Booking Action --}}
                            <div class="flex items-center lg:flex-col lg:items-end justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block lg:text-right">Ticket Fare</span>
                                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                                        Rs. {{ number_format($trip->fare ?? 0, 2) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block lg:text-right">/ passenger seat</span>
                                </div>
                            </div>
                        </div>

                        {{-- Card Middle Grid: Key Trip Attributes --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 py-5 text-xs">
                            
                            {{-- 1. Departure Date & Time --}}
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                                <div>
                                    <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Departure</span>
                                    <span class="font-extrabold text-slate-900 dark:text-white text-sm block">
                                        {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">
                                        {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('D, d M Y') : '—' }}
                                    </span>
                                </div>
                            </div>

                            {{-- 2. Driver --}}
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                                <div>
                                    <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Assigned Driver</span>
                                    <span class="font-extrabold text-slate-900 dark:text-white text-sm block truncate max-w-[150px]">
                                        {{ $trip->driver->name ?? 'Commercial Driver' }}
                                    </span>
                                    <span class="text-emerald-700 dark:text-emerald-400 font-bold text-[10px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-shield-halved text-[9px]"></i> Verified Driver
                                    </span>
                                </div>
                            </div>

                            {{-- 3. Vehicle --}}
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-van-shuttle"></i>
                                </div>
                                <div>
                                    <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Vehicle</span>
                                    <span class="font-mono font-extrabold text-slate-900 dark:text-white text-sm block">
                                        {{ $trip->vehicle->registration_no ?? 'Fleet Bus' }}
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px] truncate block max-w-[150px]">
                                        {{ $trip->vehicle->model ?? '' }} ({{ $trip->vehicle->type ?? 'Van' }})
                                    </span>
                                </div>
                            </div>

                            {{-- 4. Available Seats --}}
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-chair"></i>
                                </div>
                                <div>
                                    <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Seat Availability</span>
                                    <span class="font-mono font-black text-emerald-700 dark:text-emerald-400 text-base block">
                                        {{ $trip->available_seats }} Open {{ \Illuminate\Support\Str::plural('Seat', $trip->available_seats) }}
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">
                                        out of {{ $trip->vehicle->total_seats ?? '—' }} total seats
                                    </span>
                                </div>
                            </div>

                        </div>

                        {{-- EXPECTED STOP TIMES (Timeline / Highlight) --}}
                        <div class="mt-2 p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 text-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                <span class="font-extrabold text-slate-800 dark:text-slate-200 text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock text-emerald-600 dark:text-emerald-400"></i> Expected Stop Timings
                                </span>
                                
                                @if (isset($trip->search_from_stop) && isset($trip->search_to_stop))
                                    <span class="text-emerald-800 dark:text-emerald-300 font-bold text-[11px]">
                                        Your query segment: 
                                        <strong>{{ $trip->search_from_stop['name'] }}</strong> ({{ $trip->search_from_stop['expected_time'] ? \Carbon\Carbon::parse($trip->search_from_stop['expected_time'])->format('h:i A') : $trip->departure_time }}) 
                                        → 
                                        <strong>{{ $trip->search_to_stop['name'] }}</strong> ({{ $trip->search_to_stop['expected_time'] ? \Carbon\Carbon::parse($trip->search_to_stop['expected_time'])->format('h:i A') : 'Arrival' }})
                                    </span>
                                @endif
                            </div>

                            {{-- Sequential Stops Timeline --}}
                            @php
                                $orderedStops = $trip->search_ordered_stops ?? [];
                            @endphp

                            @if (!empty($orderedStops))
                                <div class="overflow-x-auto pb-1">
                                    <div class="flex items-center gap-2 min-w-max">
                                        @foreach ($orderedStops as $idx => $s)
                                            <div class="flex items-center gap-2">
                                                <div class="p-2 rounded-xl {{ (isset($trip->search_from_stop) && $trip->search_from_stop['name'] === $s['name']) || (isset($trip->search_to_stop) && $trip->search_to_stop['name'] === $s['name']) ? 'bg-emerald-600 text-white font-extrabold shadow-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200' }}">
                                                    <div class="text-[11px] font-bold">{{ $s['name'] }}</div>
                                                    <div class="text-[10px] {{ (isset($trip->search_from_stop) && $trip->search_from_stop['name'] === $s['name']) || (isset($trip->search_to_stop) && $trip->search_to_stop['name'] === $s['name']) ? 'text-emerald-100' : 'text-emerald-700 dark:text-emerald-400' }} font-mono font-semibold">
                                                        {{ !empty($s['expected_time']) ? \Carbon\Carbon::parse($s['expected_time'])->format('h:i A') : ($idx === 0 ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : 'En route') }}
                                                    </div>
                                                </div>

                                                @if ($idx < count($orderedStops) - 1)
                                                    <i class="fa-solid fa-chevron-right text-slate-300 dark:text-slate-600 text-[10px]"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-slate-500 dark:text-slate-400 text-[11px]">
                                    Direct service with planned departure from {{ $trip->route->origin ?? 'terminal' }} at {{ \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') }}.
                                </div>
                            @endif
                        </div>

                        {{-- Card Bottom Row: Action Buttons --}}
                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                @if($trip->pickup_point)
                                    <span><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> Pickup: <strong>{{ $trip->pickup_point }}</strong></span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2.5">
                                {{-- View Details Button --}}
                                <button type="button"
                                        onclick="toggleTripDetails({{ $trip->id }})"
                                        id="viewDetailsBtn-{{ $trip->id }}"
                                        class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-circle-info text-slate-400"></i>
                                    <span>View Details</span>
                                </button>

                                {{-- Book Seats CTA --}}
                                <a href="{{ route('booking.index', ['trip_id' => $trip->id]) }}"
                                   class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all hover:scale-[1.02] flex items-center gap-1.5">
                                    <i class="fa-solid fa-ticket"></i>
                                    <span>Book Seat</span>
                                </a>
                            </div>
                        </div>

                        {{-- Expandable View Details Drawer --}}
                        <div id="tripDetailsDrawer-{{ $trip->id }}" class="hidden mt-5 pt-5 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 p-5 rounded-2xl space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                    <i class="fa-solid fa-list-check text-emerald-600 dark:text-emerald-400"></i>
                                    Complete Journey Stops & Details for Trip #{{ $trip->id }}
                                </h4>
                                <a href="{{ route('trips.show', $trip->id) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 text-xs font-bold">
                                    Open Dedicated Page →
                                </a>
                            </div>

                            {{-- Detailed Stops Table --}}
                            @if (!empty($orderedStops))
                                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-800">
                                            <tr>
                                                <th class="px-4 py-2.5">Stop #</th>
                                                <th class="px-4 py-2.5">Stop Name</th>
                                                <th class="px-4 py-2.5">Expected Arrival</th>
                                                <th class="px-4 py-2.5">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                            @foreach ($orderedStops as $sIdx => $s)
                                                <tr class="{{ (isset($trip->search_from_stop) && $trip->search_from_stop['name'] === $s['name']) || (isset($trip->search_to_stop) && $trip->search_to_stop['name'] === $s['name']) ? 'bg-emerald-50/80 dark:bg-emerald-950/40 font-bold' : '' }}">
                                                    <td class="px-4 py-2 font-mono text-slate-500 dark:text-slate-400">{{ $s['order'] > 0 ? $s['order'] : ($sIdx + 1) }}</td>
                                                    <td class="px-4 py-2 text-slate-900 dark:text-white font-bold">{{ $s['name'] }}</td>
                                                    <td class="px-4 py-2 font-mono text-emerald-700 dark:text-emerald-400 font-bold">
                                                        {{ !empty($s['expected_time']) ? \Carbon\Carbon::parse($s['expected_time'])->format('h:i A') : ($sIdx === 0 ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : 'En route') }}
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        @if (isset($trip->search_from_stop) && $trip->search_from_stop['name'] === $s['name'])
                                                            <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px]">Your Boarding Stop</span>
                                                        @elseif (isset($trip->search_to_stop) && $trip->search_to_stop['name'] === $s['name'])
                                                            <span class="px-2 py-0.5 rounded-full bg-emerald-800 text-white text-[10px]">Your Dropoff Stop</span>
                                                        @else
                                                            <span class="text-slate-400 text-[10px]">Intermediate Stop</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            {{-- Recent Driver Broadcasts on this Trip --}}
                            @if ($trip->tripMessages && $trip->tripMessages->isNotEmpty())
                                <div class="p-3.5 bg-white rounded-xl border border-slate-200">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Driver Live Broadcast</span>
                                    @foreach ($trip->tripMessages->take(2) as $m)
                                        <div class="text-xs text-slate-800 font-medium flex items-start gap-2 mb-1.5">
                                            <i class="fa-solid fa-bullhorn text-emerald-600 text-[10px] mt-1 shrink-0"></i>
                                            <div>
                                                <span>{{ $m->message }}</span>
                                                <span class="text-[10px] text-slate-400 ml-1">({{ $m->created_at->diffForHumans() }})</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
</section>

{{-- Interactive Details Toggle Script --}}
<script>
    function toggleTripDetails(tripId) {
        const drawer = document.getElementById('tripDetailsDrawer-' + tripId);
        const btn = document.getElementById('viewDetailsBtn-' + tripId);
        if (drawer) {
            const isHidden = drawer.classList.contains('hidden');
            drawer.classList.toggle('hidden');
            if (btn) {
                btn.classList.toggle('bg-slate-100', isHidden);
            }
        }
    function swapTripStops() {
        const from = document.getElementById('from');
        const to = document.getElementById('to');
        if (from && to) {
            const temp = from.value;
            from.value = to.value;
            to.value = temp;
        }
    }
</script>

@endsection
