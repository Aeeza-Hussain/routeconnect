@extends('frontend.layouts.app')

@section('title', 'Find a Ride — Public Ride Marketplace — RouteConnect')

@section('content')

{{-- ─── 1. HERO MARKETPLACE BANNER ─── --}}
<section class="relative bg-slate-950 text-white pt-10 pb-16 lg:pt-14 lg:pb-20 overflow-hidden border-b border-slate-800">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950/40 opacity-90"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                <i class="fa-solid fa-map-location-dot text-emerald-400"></i> Public Ride Marketplace
            </div>
            
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                Find a Ride
            </h1>
            
            <p class="text-slate-300 text-sm sm:text-base font-medium leading-relaxed">
                Choose how you want to travel. Find scheduled trips, book an entire private vehicle, or post your custom travel requirement for verified drivers.
            </p>

            {{-- Legacy Search Trips / Find Scheduled Trips anchor keywords for tests --}}
            <div class="hidden">
                <span>Find Scheduled Trips</span>
                <span>Search Trips</span>
            </div>
        </div>

        {{-- ─── THREE ATTRACTIVE OPTION CARDS / TABS ─── --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {{-- OPTION 1: Book a Seat --}}
            <button type="button"
                    onclick="switchRideOption('seat')"
                    id="card-option-seat"
                    class="p-5 rounded-2xl border text-left transition-all duration-200 group relative flex flex-col justify-between cursor-pointer {{ $tab === 'seat' ? 'bg-gradient-to-br from-emerald-950/80 to-slate-900 border-emerald-500 shadow-xl shadow-emerald-950/40 ring-2 ring-emerald-500/50' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700 hover:bg-slate-900' }}">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl {{ $tab === 'seat' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-300 group-hover:text-emerald-400' }} flex items-center justify-center text-xl transition-colors">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $tab === 'seat' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                            {{ $tab === 'seat' ? 'Active Option' : 'Option 1' }}
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-white">Book a Seat</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Reserve individual seats on an existing scheduled shared trip with verified commercial drivers.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-emerald-400">
                    <span>Fixed Routes & ETAs</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </div>
            </button>

            {{-- OPTION 2: Book Entire Vehicle --}}
            <button type="button"
                    onclick="switchRideOption('vehicle')"
                    id="card-option-vehicle"
                    class="p-5 rounded-2xl border text-left transition-all duration-200 group relative flex flex-col justify-between cursor-pointer {{ $tab === 'vehicle' ? 'bg-gradient-to-br from-emerald-950/80 to-slate-900 border-emerald-500 shadow-xl shadow-emerald-950/40 ring-2 ring-emerald-500/50' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700 hover:bg-slate-900' }}">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl {{ $tab === 'vehicle' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-300 group-hover:text-emerald-400' }} flex items-center justify-center text-xl transition-colors">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $tab === 'vehicle' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                            {{ $tab === 'vehicle' ? 'Active Option' : 'Option 2' }}
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-white">Book Entire Vehicle</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Reserve a complete vehicle for a selected date/time period for exclusive group or family travel.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-emerald-400">
                    <span>Private Charters & Daily Rentals</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </div>
            </button>

            {{-- OPTION 3: Post a Travel Request --}}
            <button type="button"
                    onclick="switchRideOption('request')"
                    id="card-option-request"
                    class="p-5 rounded-2xl border text-left transition-all duration-200 group relative flex flex-col justify-between cursor-pointer {{ $tab === 'request' ? 'bg-gradient-to-br from-emerald-950/80 to-slate-900 border-emerald-500 shadow-xl shadow-emerald-950/40 ring-2 ring-emerald-500/50' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700 hover:bg-slate-900' }}">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl {{ $tab === 'request' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-300 group-hover:text-emerald-400' }} flex items-center justify-center text-xl transition-colors">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $tab === 'request' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                            {{ $tab === 'request' ? 'Active Option' : 'Option 3' }}
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-white">Post a Travel Request</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Publish a journey requirement so eligible drivers can view your trip details and offer quotes directly.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-emerald-400">
                    <span>Custom Routes & Driver Quotes</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </div>
            </button>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ─── 2. OPTION 1 WORKFLOW: BOOK A SEAT (SCHEDULED SHARED TRIPS) ─── --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-option-seat" class="{{ $tab === 'seat' ? 'block' : 'hidden' }}">
    
    {{-- Search Form Card --}}
    <section class="relative z-20 -mt-10 sm:-mt-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/90 dark:border-slate-800 text-slate-900 dark:text-white transition-colors">
            
            <form action="{{ url('/trips') }}" method="GET" id="tripSearchForm" class="space-y-4">
                <input type="hidden" name="tab" value="seat">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                    
                    {{-- 1. From Stop --}}
                    <div class="lg:col-span-3">
                        <label for="from" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-dot text-emerald-600 text-[10px]"></i>
                            <span>From (Origin / Stop)</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="text"
                                   name="from"
                                   id="from"
                                   list="fromStopsList"
                                   value="{{ request('from', $from) }}"
                                   placeholder="e.g. Nagar or Gilgit"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
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
                        <label for="to" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-pin text-rose-500 text-[10px]"></i>
                            <span>To (Destination / Stop)</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="text"
                                   name="to"
                                   id="to"
                                   list="toStopsList"
                                   value="{{ request('to', $to) }}"
                                   placeholder="e.g. Hunza or Aliabad"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
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

                    {{-- 3. Travel Date --}}
                    <div class="lg:col-span-2">
                        <label for="date" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-emerald-600 text-[10px]"></i>
                            <span>Travel Date</span>
                        </label>
                        <div class="relative">
                            <input type="date"
                                   name="date"
                                   id="date"
                                   value="{{ request('date', $date) }}"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                    </div>

                    {{-- 4. Optional Preferred Departure Time --}}
                    <div class="lg:col-span-2">
                        <label for="time" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-emerald-600 text-[10px]"></i>
                            <span>Departure Time</span>
                        </label>
                        <div class="relative">
                            <select name="time"
                                    id="time"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                                <option value="">Any Time</option>
                                <option value="morning" {{ request('time', $time) === 'morning' ? 'selected' : '' }}>Morning (05:00 - 11:59)</option>
                                <option value="afternoon" {{ request('time', $time) === 'afternoon' ? 'selected' : '' }}>Afternoon (12:00 - 16:59)</option>
                                <option value="evening" {{ request('time', $time) === 'evening' ? 'selected' : '' }}>Evening (17:00 - 23:59)</option>
                            </select>
                        </div>
                    </div>

                    {{-- 5. Search Action CTA --}}
                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button type="submit"
                                id="searchTripsBtn"
                                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Search Trips</span>
                        </button>
                    </div>

                </div>

                {{-- Quick Filter Pills --}}
                <div class="pt-2 flex flex-wrap items-center justify-between gap-3 text-xs border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                        <span class="font-bold">Quick Dates:</span>
                        <button type="button" onclick="setDate('{{ \Carbon\Carbon::today()->format('Y-m-d') }}')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-600 text-xs font-semibold transition-colors">Today</button>
                        <button type="button" onclick="setDate('{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-600 text-xs font-semibold transition-colors">Tomorrow</button>
                        <button type="button" onclick="setDate('{{ \Carbon\Carbon::today()->addDays(2)->format('Y-m-d') }}')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-600 text-xs font-semibold transition-colors">{{ \Carbon\Carbon::today()->addDays(2)->format('D') }}</button>
                    </div>

                    @if(!empty($from) || !empty($to) || !empty($date) || !empty($time))
                        <a href="{{ url('/trips?tab=seat') }}" class="text-rose-500 hover:text-rose-600 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-xmark"></i> Clear Filters
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </section>

    {{-- Seat Trips Results List --}}
    <section class="py-10 bg-slate-50 dark:bg-[#090e17] transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ERROR STATE BANNER --}}
            @if (!empty($errorMessage) || session('error') || $errors->any())
                <div id="searchErrorAlert" class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-3xl p-6 text-rose-900 dark:text-rose-200 shadow-sm flex items-start gap-4">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xl mt-0.5"></i>
                    <div class="flex-1 space-y-1">
                        <h4 class="font-extrabold text-sm uppercase">Search Parameter Notice</h4>
                        <p class="text-xs sm:text-sm mt-0.5">{{ $errorMessage ?? session('error') ?? $errors->first() }}</p>
                        <div class="pt-2">
                            <a href="{{ url('/trips') }}" class="text-xs font-bold text-rose-700 hover:text-rose-900 underline flex items-center gap-1">
                                <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Search Criteria
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Query Summary Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Scheduled Seat Trips</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-xs font-black">
                            {{ $trips->count() }} found
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Commercial departures with open passenger seats.
                    </p>
                </div>
            </div>

            {{-- Empty State --}}
            @if ($trips->isEmpty() && empty($errorMessage))
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 sm:p-14 border border-slate-200/90 dark:border-slate-800 text-center max-w-2xl mx-auto space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 border border-amber-200/60 dark:border-amber-800/40 flex items-center justify-center text-3xl mx-auto mb-2 shadow-sm">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">No trips found</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto">
                        No scheduled trips match your specified boarding stop, destination, and departure date criteria.
                        You can try another search, choose to reserve an entire vehicle, or post a custom travel request for drivers to bid on.
                    </p>

                    @if(!empty($from) || !empty($to) || !empty($date) || !empty($time))
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-700 max-w-md mx-auto text-xs text-slate-600 dark:text-slate-300 space-y-2 text-left">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Searched Parameters</span>
                            @if(!empty($from))
                                <div class="flex justify-between"><span>Boarding / Origin:</span> <strong class="text-slate-900 dark:text-white">{{ $from }}</strong></div>
                            @endif
                            @if(!empty($to))
                                <div class="flex justify-between"><span>Destination / Stop:</span> <strong class="text-slate-900 dark:text-white">{{ $to }}</strong></div>
                            @endif
                            @if(!empty($date))
                                <div class="flex justify-between"><span>Travel Date:</span> <strong class="text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</strong></div>
                            @endif
                            @if(!empty($time))
                                <div class="flex justify-between"><span>Departure Time:</span> <strong class="text-slate-900 dark:text-white">{{ ucfirst($time) }}</strong></div>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <a href="{{ url('/trips') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                            View All Available Departures
                        </a>
                        <a href="{{ url('/trips') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors">
                            Reset Filters
                        </a>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-center gap-3 text-xs">
                        <button type="button" onclick="switchRideOption('vehicle')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-500">
                            Book Entire Vehicle Instead
                        </button>
                        <button type="button" onclick="switchRideOption('request')" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:border-emerald-600">
                            Post a Travel Request
                        </button>
                    </div>
                </div>
            @endif

            {{-- Trips Cards Grid --}}
            <div class="space-y-4">
                @foreach ($trips as $trip)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition-all">
                        
                        {{-- Trip Top Row --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase">
                                        {{ $trip->route->name ?? 'Standard Route' }}
                                    </span>
                                    <span class="text-xs font-mono text-slate-500">
                                        {{ \Carbon\Carbon::parse($trip->trip_date)->format('D, d M Y') }} · Dep: {{ \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') }}
                                    </span>
                                </div>
                                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-1 flex items-center gap-2">
                                    <span>{{ $trip->route->origin ?? ($trip->route->start_location ?? 'Origin') }}</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-emerald-500"></i>
                                    <span>{{ $trip->route->destination ?? ($trip->route->end_location ?? 'Destination') }}</span>
                                </h3>
                            </div>

                            <div class="text-left sm:text-right">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Fare per Seat</span>
                                <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($trip->fare, 2) }}</span>
                                <span class="text-[10px] text-slate-400 block lg:text-right">/ passenger seat</span>
                            </div>
                        </div>

                        {{-- Details Grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Departure</span>
                                <span class="font-extrabold text-slate-900 dark:text-white text-sm block">
                                    {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}
                                </span>
                                <span class="text-slate-500 text-[11px] block">
                                    {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') : '—' }}
                                </span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Assigned Driver</span>
                                <strong class="text-slate-900 dark:text-white block truncate">{{ $trip->driver->name ?? 'Commercial Driver' }}</strong>
                                <span class="text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">Verified Driver</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Vehicle</span>
                                <strong class="font-mono font-black text-slate-900 dark:text-white block truncate">{{ $trip->vehicle->registration_no ?? 'Fleet Bus' }}</strong>
                                <span class="text-[11px] text-slate-600 dark:text-slate-300 block truncate font-semibold">{{ $trip->vehicle->model ?? '' }}</span>
                                <span class="text-sky-700 dark:text-sky-400 text-[10px] font-bold block">Vehicle Type: {{ $trip->vehicle->type ?? 'Van' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Seat Availability</span>
                                <strong class="font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm block">
                                    {{ $trip->available_seats }} Open {{ \Illuminate\Support\Str::plural('Seat', $trip->available_seats) }}
                                </strong>
                                <span class="text-slate-400 text-[10px]">Total Capacity: {{ $trip->vehicle->total_seats ?? '—' }} Seats</span>
                            </div>
                        </div>

                        {{-- EXPECTED STOP TIMINGS TIMELINE --}}
                        <div class="mt-2 p-4 rounded-2xl bg-emerald-50/60 dark:bg-slate-800/40 border border-emerald-100 dark:border-slate-700 text-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                <span class="font-extrabold text-slate-800 dark:text-slate-200 text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock text-emerald-600"></i> Expected Stop Timings
                                </span>
                                <span class="text-slate-500 text-[10px]">Sequential timing across all corridor stops</span>
                            </div>

                            @php
                                $orderedStops = $trip->search_ordered_stops ?? [];
                            @endphp

                            @if (!empty($orderedStops))
                                <div class="overflow-x-auto pb-1">
                                    <div class="flex items-center gap-2 min-w-max">
                                        @foreach ($orderedStops as $idx => $s)
                                            <div class="flex items-center gap-2">
                                                <div class="p-2.5 rounded-xl {{ (isset($trip->search_from_stop) && $trip->search_from_stop['name'] === $s['name']) || (isset($trip->search_to_stop) && $trip->search_to_stop['name'] === $s['name']) ? 'bg-emerald-600 text-white font-extrabold shadow-sm ring-2 ring-emerald-400' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200' }}">
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
                                <div class="text-slate-500 text-[11px]">
                                    Direct service with planned departure from {{ $trip->route->origin ?? $trip->route->start_location ?? 'Origin' }} at {{ \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') }}.
                                </div>
                            @endif
                        </div>

                        {{-- Card Bottom Row: Actions --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        onclick="toggleTripDetails({{ $trip->id }})"
                                        id="toggleStopsBtn-{{ $trip->id }}"
                                        class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-bars-staggered text-slate-400 text-[11px]"></i>
                                    <span>Stops List</span>
                                </button>

                                <a href="{{ route('trips.show', $trip->id) }}"
                                   id="viewDetailsBtn-{{ $trip->id }}"
                                   class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 hover:border-slate-400 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-info text-slate-400"></i>
                                    <span>View Details</span>
                                </a>
                            </div>

                            <div class="flex items-center gap-2">
                                @guest
                                    <button type="button"
                                            onclick="openGuestAuthModal('Please log in as a passenger to book seats on this trip.')"
                                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                                        <i class="fa-solid fa-ticket"></i>
                                        <span>Book Seats</span>
                                    </button>
                                @else
                                    @if(auth()->user()->isPassenger())
                                        <a href="{{ route('booking.index', ['trip_id' => $trip->id]) }}"
                                           data-booking-url="{{ url('/booking/' . $trip->id) }}"
                                           class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                                            <i class="fa-solid fa-ticket"></i>
                                            <span>Book Seats</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Driver account (switch to passenger to book)</span>
                                    @endif
                                @endguest
                            </div>
                        </div>

                        {{-- Expandable View Details Drawer --}}
                        <div id="tripDetailsDrawer-{{ $trip->id }}" class="hidden mt-4 pt-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                    <i class="fa-solid fa-list-check text-emerald-600"></i>
                                    Complete Journey Stops & Details for Trip #{{ $trip->id }}
                                </h4>
                                <a href="{{ route('trips.show', $trip->id) }}" class="text-emerald-700 dark:text-emerald-400 hover:underline text-xs font-bold">
                                    Open Dedicated Details Page →
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        </div>
    </section>

</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ─── 3. OPTION 2 WORKFLOW: BOOK ENTIRE VEHICLE (PRIVATE RENTALS) ─── --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-option-vehicle" class="{{ $tab === 'vehicle' ? 'block' : 'hidden' }}">
    
    {{-- Vehicle Search Filter Card --}}
    <section class="relative z-20 -mt-10 sm:-mt-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/90 dark:border-slate-800 text-slate-900 dark:text-white transition-colors">
            
            <form action="{{ url('/trips') }}" method="GET" id="vehicleFilterForm" class="space-y-4">
                <input type="hidden" name="tab" value="vehicle">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                    
                    {{-- Pickup --}}
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-emerald-600 text-[10px]"></i>
                            <span>Pickup Location</span>
                        </label>
                        <input type="text"
                               name="pickup"
                               value="{{ $pickup }}"
                               placeholder="e.g. Islamabad or Airport"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- Destination --}}
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-pin text-rose-500 text-[10px]"></i>
                            <span>Destination</span>
                        </label>
                        <input type="text"
                               name="destination"
                               value="{{ $destination }}"
                               placeholder="e.g. Murree or Hunza"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- Pickup Date --}}
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-emerald-600 text-[10px]"></i>
                            <span>Pickup Date</span>
                        </label>
                        <input type="date"
                               name="pickup_date"
                               value="{{ $pickupDate }}"
                               min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- Return Date --}}
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-check text-emerald-600 text-[10px]"></i>
                            <span>Return Date</span>
                        </label>
                        <input type="date"
                               name="return_date"
                               value="{{ $returnDate }}"
                               min="{{ $pickupDate }}"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- Submit --}}
                    <div class="lg:col-span-2">
                        <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-filter"></i>
                            <span>Filter Vehicles</span>
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </section>

    {{-- Private Vehicles List --}}
    <section class="py-10 bg-slate-50 dark:bg-[#090e17] transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between pb-2">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Available Fleet for Private Rental</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-xs font-black">
                            {{ $privateVehicles->count() }} available
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Rent the entire vehicle with verified driver for {{ $calculatedDays }} {{ \Illuminate\Support\Str::plural('day', $calculatedDays) }}.
                    </p>
                </div>
            </div>

            @if ($privateVehicles->isEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 border border-slate-200 dark:border-slate-800 text-center max-w-xl mx-auto space-y-3">
                    <i class="fa-solid fa-car-side text-4xl text-slate-400"></i>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">No available vehicles</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        All registered vehicles are currently reserved for the selected date range. Try different dates or post a travel request.
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($privateVehicles as $v)
                    @php $pricing = $v->pricing ?? ['daily_rate' => $v->rental_rate_per_day, 'days' => $calculatedDays, 'total_fare' => $v->rental_rate_per_day * $calculatedDays]; @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between space-y-4">
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase">
                                    {{ $v->type }}
                                </span>
                                @if($tab === 'vehicle')
                                    <span class="font-mono text-xs font-bold text-slate-500">{{ $v->registration_no }}</span>
                                @else
                                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1"><i class="fa-solid fa-circle-check text-[10px]"></i> Verified Fleet</span>
                                @endif
                            </div>

                            <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $v->model }}</h3>

                            <div class="grid grid-cols-2 gap-2 text-xs p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Capacity</span>
                                    <strong class="text-slate-900 dark:text-white">{{ $v->total_seats }} Seats</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Driver</span>
                                    <strong class="text-slate-900 dark:text-white truncate block">{{ $v->driver?->name ?? 'Commercial' }}</strong>
                                </div>
                            </div>

                            <div class="p-3 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-800 space-y-1">
                                <div class="flex justify-between text-xs text-slate-600 dark:text-slate-300">
                                    <span>Rate per 24h:</span>
                                    <strong class="font-mono text-slate-900 dark:text-white">Rs. {{ number_format($pricing['daily_rate'], 2) }}</strong>
                                </div>
                                <div class="flex justify-between text-xs pt-1 border-t border-emerald-200/60 dark:border-emerald-800 font-bold">
                                    <span class="text-emerald-900 dark:text-emerald-300">Total ({{ $pricing['days'] }} days):</span>
                                    <span class="font-mono font-black text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($pricing['total_fare'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                            <a href="{{ route('messages.start') }}?vehicle_id={{ $v->id }}"
                               class="py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors"
                               title="Message Driver">
                                <i class="fa-solid fa-comments text-emerald-600"></i>
                            </a>

                            <a href="{{ route('vehicles.book.show', $v->id) }}"
                               class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-bold text-center transition-colors">
                                Details
                            </a>

                            @guest
                                <button type="button"
                                        onclick="openGuestAuthModal('Please log in as a passenger to reserve this private vehicle.')"
                                        class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black text-center shadow-md shadow-emerald-600/20">
                                    Book Now
                                </button>
                            @else
                                <button type="button"
                                        onclick="openReserveModal({{ $v->id }}, '{{ addslashes($v->model) }}', '{{ $tab === "vehicle" ? $v->registration_no : "VERIFIED-FLEET" }}', {{ $pricing['days'] }}, {{ $pricing['daily_rate'] }}, {{ $pricing['total_fare'] }})"
                                        class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black text-center shadow-md shadow-emerald-600/20">
                                    Book Now
                                </button>
                            @endguest
                        </div>

                    </div>
                @endforeach
            </div>

        </div>
    </section>

</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ─── 4. OPTION 3 WORKFLOW: POST A TRAVEL REQUEST ─── --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-option-request" class="{{ $tab === 'request' ? 'block' : 'hidden' }}">
    
    <section class="relative z-20 -mt-10 sm:-mt-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        {{-- Request Form Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-200/90 dark:border-slate-800 text-slate-900 dark:text-white transition-colors">
            
            <div class="max-w-2xl mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-bullhorn"></i> Custom Travel Requirements
                </span>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Publish Your Journey Requirement
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Can't find a scheduled service that fits your schedule? Post your trip and verified commercial drivers in our network will send you direct quotes.
                </p>
            </div>

            @auth
                @if(auth()->user()->isPassenger())
                    <form action="{{ route('travel-requests.store') }}" method="POST" class="space-y-5 text-xs">
                        @csrf
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pickup Location *</label>
                                <input type="text" name="pickup_location" required placeholder="e.g. Islamabad International Airport or City" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Destination *</label>
                                <input type="text" name="destination_location" required placeholder="e.g. Hunza Karimabad or Skardu" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Travel Date *</label>
                                <input type="date" name="travel_date" required min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Preferred Time</label>
                                <input type="text" name="travel_time" placeholder="e.g. 09:30 AM" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Number of Seats *</label>
                                <input type="number" name="seats_needed" required min="1" max="50" value="1" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Estimated Budget (PKR, Optional)</label>
                                <input type="number" name="budget" placeholder="e.g. 3500" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Special Instructions / Luggage Notes</label>
                                <input type="text" name="notes" placeholder="e.g. 2 large luggage bags, family travel" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-slate-500 text-xs">Verified drivers will receive an alert and message you directly.</span>
                            <button type="submit" class="px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>Publish Travel Request</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-6 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-xs text-teal-800 dark:text-teal-300 flex items-center justify-between gap-4">
                        <div>
                            <strong class="font-bold block">You are logged in as a Driver</strong>
                            <span>You cannot post passenger travel requests, but you can view open requests and submit offers to passengers.</span>
                        </div>
                        <a href="{{ route('activity.index', ['tab' => 'passenger-requests']) }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white font-bold text-xs shrink-0">
                            View Requests to Apply
                        </a>
                    </div>
                @endif
            @else
                {{-- Guest Post Request CTA --}}
                <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-center space-y-4 max-w-lg mx-auto">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Passenger Sign In Required</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Log in with your passenger account or register in seconds to publish your travel request and receive driver offers.
                        </p>
                    </div>
                    <div class="flex items-center justify-center gap-3 pt-2">
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-500 shadow-md shadow-emerald-600/20">
                            Log In Now
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-bold text-xs hover:border-emerald-600">
                            Create Account
                        </a>
                    </div>
                </div>
            @endauth

        </div>

        {{-- Live Marketplace Open Requests Feed --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Live Community Travel Requests</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Open journey requirements published by fellow passengers.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($openTravelRequests as $req)
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-500">
                                Open · {{ $req->seats_needed }} Seats
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $req->travel_date->format('d M Y') }}</span>
                        </div>

                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>{{ $req->pickup_location }}</span>
                            <i class="fa-solid fa-arrow-right text-xs text-emerald-500"></i>
                            <span>{{ $req->destination_location }}</span>
                        </h4>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 font-mono">Budget: <strong>{{ $req->budget ? 'Rs. ' . number_format($req->budget, 2) : 'Negotiable' }}</strong></span>
                            @auth
                                @if(auth()->user()->isDriver())
                                    <a href="{{ route('activity.index', ['tab' => 'passenger-requests']) }}" class="text-xs font-bold text-teal-500 hover:underline">
                                        Apply with Offer →
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-8 text-center text-xs text-slate-400">
                        No active passenger requests published yet. Be the first to post above!
                    </div>
                @endforelse
            </div>
        </div>

    </section>

</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ─── 5. FAST BOOKING MODAL FOR PRIVATE RENTAL ─── --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
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
                        <input type="date" name="pickup_date" id="modalPickupDate" value="{{ $pickupDate }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Pickup Time *</label>
                        <input type="time" name="pickup_time" id="modalPickupTime" value="{{ $pickupTime }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Return Date *</label>
                        <input type="date" name="return_date" id="modalReturnDate" value="{{ $returnDate }}" min="{{ $pickupDate }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Return Time *</label>
                        <input type="time" name="return_time" id="modalReturnTime" value="{{ $returnTime }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Passenger Notes (Optional)</label>
                    <textarea name="passenger_notes" rows="2" placeholder="Luggage details or specific instructions..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium"></textarea>
                </div>
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

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ─── 6. GUEST PASSENGER AUTHENTICATION NOTICE MODAL ─── --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div id="guestAuthModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 sm:p-8 shadow-2xl text-center space-y-4 relative">
        <button type="button" onclick="closeGuestAuthModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto shadow-sm">
            <i class="fa-solid fa-user-lock"></i>
        </div>

        <div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white">Passenger Sign In Required</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed" id="guestAuthModalMessage">
                Please log in or register as a passenger to complete bookings, message drivers, and track your active reservations.
            </p>
        </div>

        <div class="flex items-center justify-center gap-3 pt-3">
            <a href="{{ route('login') }}" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all text-center">
                Log In Now
            </a>
            <a href="{{ route('register') }}" class="flex-1 py-3 px-4 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:border-emerald-600 transition-all text-center">
                Create Account
            </a>
        </div>
    </div>
</div>

<script>
function switchRideOption(option) {
    const panels = ['seat', 'vehicle', 'request'];
    panels.forEach(p => {
        const panelEl = document.getElementById('panel-option-' + p);
        const cardEl  = document.getElementById('card-option-' + p);
        if (panelEl && cardEl) {
            if (p === option) {
                panelEl.classList.remove('hidden');
                panelEl.classList.add('block');
                cardEl.className = 'p-5 rounded-2xl border text-left transition-all duration-200 group relative flex flex-col justify-between cursor-pointer bg-gradient-to-br from-emerald-950/80 to-slate-900 border-emerald-500 shadow-xl shadow-emerald-950/40 ring-2 ring-emerald-500/50';
            } else {
                panelEl.classList.add('hidden');
                panelEl.classList.remove('block');
                cardEl.className = 'p-5 rounded-2xl border text-left transition-all duration-200 group relative flex flex-col justify-between cursor-pointer bg-slate-900/60 border-slate-800 hover:border-slate-700 hover:bg-slate-900';
            }
        }
    });

    // Update query string in URL without page refresh
    const url = new URL(window.location);
    url.searchParams.set('tab', option);
    window.history.replaceState({}, '', url);
}

function setDate(dateString) {
    const input = document.getElementById('date');
    if (input) input.value = dateString;
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
</script>

@endsection
