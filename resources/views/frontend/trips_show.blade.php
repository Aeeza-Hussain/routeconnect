@extends('frontend.layouts.app')

@section('title', ($trip->route->name ?? 'Trip #' . $trip->id) . ' — ' . ($trip->route->origin ?? 'Origin') . ' to ' . ($trip->route->destination ?? 'Destination') . ' — RouteConnect')

@section('content')

{{-- ─── Hero / Header ─── --}}
<section class="bg-slate-950 text-white py-10 sm:py-14 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.15),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Navigation breadcrumb / Back link --}}
        <div class="mb-4">
            <a href="{{ url('/trips') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-emerald-400 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Trip Search</span>
            </a>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-bus text-emerald-400"></i> Trip Details #{{ $trip->id }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold">
                        <i class="fa-solid fa-route text-emerald-400"></i> Route: {{ $trip->route->name ?? 'Standard Route' }}
                    </span>
                    @if ($trip->available_seats <= 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-400/30 text-rose-300 text-xs font-bold uppercase tracking-wider">
                            <i class="fa-solid fa-ban text-rose-400"></i> Fully Booked
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                            <i class="fa-solid fa-check-circle text-emerald-400"></i> Seats Available
                        </span>
                    @endif
                </div>

                {{-- Route Name & Path --}}
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight flex flex-wrap items-center gap-3">
                        <span class="text-emerald-400">{{ $trip->route->name ?? 'Route' }}</span>
                    </h1>
                    <div class="flex items-center gap-2.5 text-base sm:text-xl font-bold text-slate-200">
                        <span>{{ $trip->route->origin ?? 'Origin' }}</span>
                        <i class="fa-solid fa-arrow-right text-emerald-400 text-sm"></i>
                        <span>{{ $trip->route->destination ?? 'Destination' }}</span>
                    </div>
                </div>

                {{-- Departure Date & Time --}}
                <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-300 pt-1">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-emerald-400"></i>
                        <span><strong>Date:</strong> {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('l, d M Y') : '—' }} ({{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('Y-m-d') : '' }})</span>
                    </div>
                    <span class="text-slate-600 hidden sm:inline">•</span>
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-clock text-emerald-400"></i>
                        <span><strong>Departure:</strong> {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Top CTA Button --}}
            <div class="flex items-center gap-3 self-start lg:self-center">
                @if ($trip->available_seats > 0)
                    <a href="{{ url('/booking/' . $trip->id) }}" data-booking-url="{{ route('booking.index', ['trip_id' => $trip->id]) }}" id="top-book-seats-btn" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-black transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Book Seats</span>
                    </a>
                @else
                    <button type="button" disabled id="top-book-seats-btn" class="px-6 py-3 rounded-xl bg-slate-800 text-slate-400 border border-slate-700 text-xs sm:text-sm font-black cursor-not-allowed flex items-center gap-2">
                        <i class="fa-solid fa-ban"></i>
                        <span>Fully Booked</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ─── Main Details Section ─── --}}
<section class="py-10 bg-slate-50 dark:bg-[#070D18] min-h-screen transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- 4 Stat Badges --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            {{-- Route Info --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <span class="text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider block mb-1">Route Name</span>
                <span class="text-base font-extrabold text-slate-900 dark:text-white block truncate" title="{{ $trip->route->name ?? 'Standard Route' }}">
                    {{ $trip->route->name ?? 'Standard Route' }}
                </span>
                <span class="text-slate-500 dark:text-slate-400 text-[11px] block mt-0.5 truncate">
                    {{ $trip->route->origin ?? 'Origin' }} → {{ $trip->route->destination ?? 'Destination' }}
                </span>
            </div>

            {{-- Departure Date/Time --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <span class="text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider block mb-1">Departure Date & Time</span>
                <span class="text-base font-extrabold text-slate-900 dark:text-white font-mono block">
                    {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}
                </span>
                <span class="text-slate-500 dark:text-slate-400 text-[11px] block mt-0.5">
                    {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('M d, Y') : '—' }}
                </span>
            </div>

            {{-- Seat Availability --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <span class="text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider block mb-1">Available Seats</span>
                @if ($trip->available_seats > 0)
                    <span class="text-xl font-black text-emerald-700 dark:text-emerald-400 font-mono block">
                        {{ $trip->available_seats }} Open {{ \Illuminate\Support\Str::plural('Seat', $trip->available_seats) }}
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 text-[11px] block mt-0.5">out of {{ $trip->vehicle->total_seats ?? '—' }} vehicle seats</span>
                @else
                    <span class="text-xl font-black text-rose-600 dark:text-rose-400 font-mono block">
                        0 Seats
                    </span>
                    <span class="text-rose-500 dark:text-rose-400 text-[11px] font-bold block mt-0.5">Fully Booked</span>
                @endif
            </div>

            {{-- Fare --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <span class="text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider block mb-1">Ticket Fare</span>
                <span class="text-xl font-black text-slate-900 dark:text-white font-mono block">
                    Rs. {{ number_format($trip->fare ?? 0, 2) }}
                </span>
                <span class="text-slate-500 dark:text-slate-400 text-[11px] block mt-0.5">Pickup: {{ $trip->pickup_point ?? 'General Bus Terminal' }}</span>
            </div>
        </div>

        {{-- 2-Column Content Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ── Left 2 Columns: Complete Ordered Stops & Driver's Trip Messages ── --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- 1. Complete Ordered Stops Table --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-2">
                        <div>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-map-location-dot text-emerald-600 dark:text-emerald-400"></i>
                                <span>Complete Ordered Stops</span>
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Chronological itinerary with expected arrival time for each stop.</p>
                        </div>
                        <span class="self-start sm:self-center px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-mono text-xs font-bold">
                            {{ count($orderedStops) }} Stops
                        </span>
                    </div>

                    @if (empty($orderedStops))
                        <div class="py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-road text-slate-300 dark:text-slate-600 text-3xl mb-2 block"></i>
                            <p>Direct non-stop service between origin and destination.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th class="px-4 py-3.5">Sequence</th>
                                        <th class="px-4 py-3.5">Stop Location</th>
                                        <th class="px-4 py-3.5">Expected Arrival Time</th>
                                        <th class="px-4 py-3.5">Role</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                    @foreach ($orderedStops as $idx => $s)
                                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors">
                                            <td class="px-4 py-3.5 font-mono font-bold text-slate-600 dark:text-slate-400">
                                                Stop #{{ $s['order'] > 0 ? $s['order'] : ($idx + 1) }}
                                            </td>
                                            <td class="px-4 py-3.5 font-extrabold text-slate-900 dark:text-white text-sm">
                                                {{ $s['name'] }}
                                            </td>
                                            <td class="px-4 py-3.5 font-mono font-black text-emerald-700 dark:text-emerald-400 text-sm">
                                                <i class="fa-regular fa-clock mr-1 text-xs text-slate-400"></i>
                                                {{ !empty($s['expected_time']) ? \Carbon\Carbon::parse($s['expected_time'])->format('h:i A') : ($idx === 0 && !empty($trip->departure_time) ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : 'En route') }}
                                            </td>
                                            <td class="px-4 py-3.5 text-[11px]">
                                                @if ($idx === 0)
                                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-black">Origin Terminal</span>
                                                @elseif ($idx === count($orderedStops) - 1)
                                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-black">Final Destination</span>
                                                @else
                                                    <span class="px-2.5 py-0.5 rounded-full bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 font-semibold border border-sky-100 dark:border-sky-900">Waystation</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- 2. Driver's Trip Messages Section --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-bullhorn text-emerald-600 dark:text-emerald-400"></i>
                                <span>Driver's Trip Messages</span>
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Live broadcast updates, road condition alerts, and departure notes from the driver.</p>
                        </div>
                        <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 font-mono text-slate-700 dark:text-slate-300 text-xs font-bold">
                            {{ $trip->tripMessages->count() }} {{ \Illuminate\Support\Str::plural('Message', $trip->tripMessages->count()) }}
                        </span>
                    </div>

                    @if ($trip->tripMessages->isEmpty())
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400 space-y-2">
                            <i class="fa-regular fa-comment-dots text-slate-300 dark:text-slate-600 text-3xl"></i>
                            <p class="font-medium text-slate-600 dark:text-slate-300">No trip messages or updates posted yet by the driver.</p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Any announcements regarding route delays, weather, or terminal changes will appear here.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($trip->tripMessages as $msg)
                                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 space-y-2 transition-all hover:border-emerald-300 dark:hover:border-emerald-600">
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-[11px]">
                                                <i class="fa-solid fa-user-tie"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-900 dark:text-white">{{ $msg->user->name ?? $trip->driver->name ?? 'Driver' }}</span>
                                                <span class="ml-1 text-[10px] text-emerald-700 dark:text-emerald-300 font-bold bg-emerald-100 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full">Driver</span>
                                            </div>
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                            {{ $msg->created_at ? $msg->created_at->format('M d, Y · h:i A') : 'Recently' }}
                                        </div>
                                    </div>
                                    <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed pl-9">
                                        {{ $msg->message }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Right Column: Driver, Vehicle & Reservation Box ── --}}
            <div class="space-y-6">

                {{-- Booking CTA Card --}}
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 rounded-3xl p-6 sm:p-7 text-white space-y-5 shadow-xl border border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Seat Reservation</span>
                        <span class="px-2.5 py-0.5 rounded-full {{ $trip->available_seats > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }} text-[10px] font-black uppercase">
                            {{ $trip->available_seats > 0 ? 'Active Booking' : 'Sold Out' }}
                        </span>
                    </div>

                    <div>
                        <div class="text-3xl font-black font-mono text-white">
                            Rs. {{ number_format($trip->fare ?? 0, 2) }}
                            <span class="text-xs font-normal text-slate-400">/ seat</span>
                        </div>
                        <div class="text-xs text-slate-400 mt-1">
                            Available: <strong class="{{ $trip->available_seats > 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $trip->available_seats }} seats</strong>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 space-y-2 text-xs text-slate-300">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Departure</span>
                            <span class="font-bold text-white">{{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Date</span>
                            <span class="font-bold text-white">{{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Boarding</span>
                            <span class="font-bold text-white truncate max-w-[160px]" title="{{ $trip->pickup_point ?? 'Terminal' }}">{{ $trip->pickup_point ?? 'Main Bus Stand' }}</span>
                        </div>
                    </div>

                    {{-- Conditional Action Button --}}
                    @if ($trip->available_seats > 0)
                        <a href="{{ url('/booking/' . $trip->id) }}" data-booking-url="{{ route('booking.index', ['trip_id' => $trip->id]) }}" id="book-seats-btn" class="w-full py-3.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Book Seats</span>
                        </a>
                    @else
                        <button type="button" disabled id="book-seats-btn" class="w-full py-3.5 px-4 rounded-xl bg-slate-800 text-slate-500 font-black text-xs sm:text-sm flex items-center justify-center gap-2 cursor-not-allowed border border-slate-700">
                            <i class="fa-solid fa-ban"></i>
                            <span>Fully Booked</span>
                        </button>
                        <p class="text-[11px] text-rose-400 text-center font-medium">This trip has no available seats remaining.</p>
                    @endif
                </div>

                {{-- Driver Card --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Assigned Driver</h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold">
                            <i class="fa-solid fa-check text-[9px] mr-0.5"></i> Verified
                        </span>
                    </div>

                    <div class="flex items-center gap-3.5">
                        <div class="w-13 h-13 w-12 h-12 rounded-2xl bg-slate-900 text-emerald-400 font-extrabold flex items-center justify-center text-lg shadow-inner">
                            {{ strtoupper(substr($trip->driver->name ?? 'D', 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-black text-slate-900 dark:text-white text-sm sm:text-base">{{ $trip->driver->name ?? 'Commercial Driver' }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                <i class="fa-solid fa-id-card text-emerald-600 dark:text-emerald-400"></i>
                                <span>Licensed Commercial Operator</span>
                            </div>
                        </div>
                    </div>

                    @if ($trip->driver && $trip->driver->phone)
                        <div class="pt-2 text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-phone text-slate-400"></i>
                            <span class="font-mono">{{ $trip->driver->phone }}</span>
                        </div>
                    @endif

                    @if ($trip->driver && $trip->driver->bio)
                        <p class="text-xs text-slate-500 dark:text-slate-400 pt-3 border-t border-slate-100 dark:border-slate-800 leading-relaxed">{{ $trip->driver->bio }}</p>
                    @endif
                </div>

                {{-- Vehicle Card --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Vehicle Information</h3>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold font-mono">
                            {{ $trip->vehicle->status ?? 'Active' }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-400 font-semibold">Registration Number</span>
                            <span class="font-mono font-black text-slate-900 dark:text-white text-sm bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                                {{ $trip->vehicle->registration_no ?? 'N/A' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Make & Model</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $trip->vehicle->model ?? 'Commercial Passenger Van' }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Vehicle Type</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $trip->vehicle->type ?? 'Van' }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Total Seating Capacity</span>
                            <span class="font-mono font-black text-slate-900 dark:text-white">{{ $trip->vehicle->total_seats ?? 0 }} Passenger Seats</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
