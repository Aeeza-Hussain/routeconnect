@extends('backend.driver.layouts.app')

@section('title', 'Trip Details #' . $trip->id . ' — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-route"></i> Trip Operational Details
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
            Trip #{{ $trip->id }} — {{ $trip->route->origin ?? 'Origin' }} → {{ $trip->route->destination ?? 'Destination' }}
        </h2>
        <p class="text-sm text-slate-400 mt-1">
            Departure scheduled for {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('l, d M Y') : '—' }} at {{ $trip->departure_time }}
        </p>
    </div>
    <div class="flex items-center gap-2.5">
        <a href="{{ route('driver.trips.edit', $trip->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-all shadow-lg shadow-teal-600/20">
            <i class="fa-solid fa-pen"></i> Edit Trip Status & Details
        </a>
        <a href="{{ route('driver.trips') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> All Trips
        </a>
    </div>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-800 text-emerald-300 text-sm rounded-2xl font-semibold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

{{-- ─── Trip Status Banner ─── --}}
<div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 mb-8">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1 font-bold text-[10px] uppercase tracking-wider">Current Status</span>
            @php $st = strtolower($trip->status); @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black uppercase
                {{ $st === 'scheduled' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : '' }}
                {{ $st === 'boarding' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                {{ $st === 'departed' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : '' }}
                {{ $st === 'delayed' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                {{ $st === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                {{ $st === 'cancelled' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}">
                <span class="w-2 h-2 rounded-full
                    {{ $st === 'scheduled' ? 'bg-sky-400' : '' }}
                    {{ $st === 'boarding' ? 'bg-amber-400 animate-pulse' : '' }}
                    {{ $st === 'departed' ? 'bg-indigo-400' : '' }}
                    {{ $st === 'delayed' ? 'bg-rose-400' : '' }}
                    {{ $st === 'completed' ? 'bg-emerald-400' : '' }}
                    {{ $st === 'cancelled' ? 'bg-red-400' : '' }}"></span>
                {{ $trip->status }}
            </span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1 font-bold text-[10px] uppercase tracking-wider">Available / Total Seats</span>
            <span class="font-extrabold text-white text-sm font-mono">
                <span class="text-teal-400">{{ $trip->available_seats }}</span> / {{ $trip->vehicle->total_seats ?? '—' }} Seats
            </span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1 font-bold text-[10px] uppercase tracking-wider">Fare Per Seat</span>
            <span class="font-extrabold text-emerald-400 text-sm font-mono">
                Rs. {{ number_format($trip->fare ?? 0, 2) }}
            </span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
            <span class="text-slate-500 block mb-1 font-bold text-[10px] uppercase tracking-wider">Pickup Point</span>
            <span class="font-extrabold text-white text-xs truncate block" title="{{ $trip->pickup_point ?? 'Standard Terminal' }}">
                {{ $trip->pickup_point ?? 'Standard Terminal' }}
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    {{-- Left 2 Columns: Trip Stops & Passenger Reservations --}}
    <div class="lg:col-span-2 space-y-8">

        {{-- ─── TRIP STOPS & EXPECTED TIMINGS ─── --}}
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                        <i class="fa-solid fa-map-pin text-teal-400"></i> Trip Stops & Expected Timings
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Sequential route stops with driver expected arrival times.</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-mono text-teal-400 font-bold">
                    {{ $trip->tripStops->count() }} {{ \Illuminate\Support\Str::plural('Stop', $trip->tripStops->count()) }}
                </span>
            </div>

            @if ($trip->tripStops->isEmpty())
                <div class="p-8 text-center rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <i class="fa-solid fa-route text-slate-600 text-2xl mb-2 block"></i>
                    <p class="text-xs font-semibold text-slate-300">Direct Route Departure</p>
                    <p class="text-[11px] text-slate-500 mt-1">No intermediate waypoint stops configured for this journey.</p>
                </div>
            @else
                <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                    @foreach ($trip->tripStops as $index => $tripStop)
                        <div class="relative flex items-start gap-4">
                            {{-- Sequence Dot --}}
                            <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-slate-950 border-2 border-teal-500 flex items-center justify-center">
                                <div class="w-1.5 h-1.5 rounded-full bg-teal-400"></div>
                            </div>

                            <div class="flex-1 p-4 rounded-xl bg-slate-900 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-teal-500/10 text-teal-400 text-[10px] font-extrabold border border-teal-500/20">
                                            Stop {{ $tripStop->stop_order ?? ($index + 1) }}
                                        </span>
                                        <h4 class="font-bold text-white text-sm">
                                            {{ $tripStop->stop->name ?? 'Stop Location' }}
                                        </h4>
                                    </div>
                                    @if ($tripStop->stop && $tripStop->stop->location)
                                        <p class="text-[11px] text-slate-400 mt-1">
                                            <i class="fa-solid fa-location-dot text-[10px] text-slate-500 mr-1"></i>
                                            {{ $tripStop->stop->location }}
                                        </p>
                                    @endif
                                </div>

                                <div class="sm:text-right">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Expected Arrival</span>
                                    <span class="font-mono font-bold text-teal-400 text-sm">
                                        <i class="fa-solid fa-clock text-xs mr-1 text-slate-400"></i>
                                        {{ $tripStop->expected_time ? \Carbon\Carbon::parse($tripStop->expected_time)->format('h:i A') : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ─── BOOKED PASSENGERS LIST ─── --}}
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 overflow-hidden">
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-teal-400"></i> Passenger Reservations
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Confirmed passenger manifests for this trip.</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-mono text-teal-400 font-bold">
                    {{ $trip->bookings->count() }} {{ \Illuminate\Support\Str::plural('Booking', $trip->bookings->count()) }}
                </span>
            </div>

            @if ($trip->bookings->isEmpty())
                <div class="p-8 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-ticket text-slate-600 text-2xl mb-2 block"></i>
                    No passenger seats booked yet for this trip.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-900/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-5 py-3">Reference</th>
                                <th class="px-5 py-3">Passenger</th>
                                <th class="px-5 py-3">Seats</th>
                                <th class="px-5 py-3">Pickup / Dropoff</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-slate-300">
                            @foreach ($trip->bookings as $booking)
                                <tr class="hover:bg-slate-900/40 transition-colors">
                                    <td class="px-5 py-3.5 font-mono font-bold text-teal-400">
                                        #{{ $booking->booking_reference ?? $booking->id }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-white">{{ $booking->user->name ?? 'Passenger' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $booking->user->phone ?? 'No phone' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-bold text-white">{{ $booking->seats }}</span> {{ \Illuminate\Support\Str::plural('seat', $booking->seats) }}
                                        @if($booking->seat_numbers)
                                            <div class="text-[10px] text-teal-400 font-mono">({{ $booking->seat_numbers }})</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-300">
                                        {{ $booking->fromStop->name ?? 'Start' }} → {{ $booking->toStop->name ?? 'End' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- Right Column: Vehicle Info & Trip Broadcast Box --}}
    <div class="space-y-6">

        {{-- Assigned Vehicle Details --}}
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-van-shuttle text-teal-400"></i> Allocated Vehicle
            </h3>

            @if ($trip->vehicle)
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-black text-white font-mono">{{ $trip->vehicle->registration_no }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            {{ $trip->vehicle->status }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-300">{{ $trip->vehicle->model }} · {{ $trip->vehicle->type }}</div>
                    <div class="text-xs text-slate-400 pt-2 border-t border-slate-800 flex justify-between">
                        <span>Total Capacity</span>
                        <span class="font-bold text-teal-400">{{ $trip->vehicle->total_seats }} Seats</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-500">No vehicle attached.</p>
            @endif
        </div>

        {{-- Post Quick Message for THIS Trip --}}
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-teal-400"></i> Broadcast Passenger Update
            </h3>
            <p class="text-xs text-slate-400 mb-4">Post real-time status update to booked passengers on this trip.</p>

            <form action="{{ route('driver.messages.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                <div>
                    <textarea name="message"
                              rows="3"
                              required
                              placeholder="e.g. Leaving Gilgit on schedule at 8:00 AM..."
                              class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500"></textarea>
                </div>

                <div class="flex flex-wrap gap-1.5 mb-2">
                    <button type="button" onclick="document.querySelector('textarea[name=message]').value='Leaving {{ $trip->route->origin ?? 'origin' }} on schedule at {{ $trip->departure_time }}.'" class="px-2 py-1 rounded bg-slate-900 hover:bg-slate-800 text-[10px] text-slate-300 font-semibold border border-slate-800">
                        "Leaving now"
                    </button>
                    <button type="button" onclick="document.querySelector('textarea[name=message]').value='Trip delayed by 15 minutes due to traffic conditions.'" class="px-2 py-1 rounded bg-slate-900 hover:bg-slate-800 text-[10px] text-slate-300 font-semibold border border-slate-800">
                        "Delayed 15m"
                    </button>
                    <button type="button" onclick="document.querySelector('textarea[name=message]').value='Arrived at scheduled stop. 10-minute break.'" class="px-2 py-1 rounded bg-slate-900 hover:bg-slate-800 text-[10px] text-slate-300 font-semibold border border-slate-800">
                        "Reached stop"
                    </button>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Broadcast Update</span>
                </button>
            </form>
        </div>

        {{-- Past Broadcasts on This Trip --}}
        @if ($trip->tripMessages->isNotEmpty())
            <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Broadcast History</h4>
                <div class="space-y-3">
                    @foreach ($trip->tripMessages as $msg)
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                            <p class="text-slate-200">{{ $msg->message }}</p>
                            <span class="text-[10px] text-slate-500 mt-1.5 block">
                                {{ $msg->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</div>

@endsection
