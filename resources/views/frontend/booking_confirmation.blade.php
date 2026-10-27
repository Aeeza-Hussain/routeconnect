@extends('frontend.layouts.app')

@section('title', 'Booking Confirmed — ' . $booking->booking_reference . ' — RouteConnect')

@section('content')

<section class="py-12 bg-slate-50 dark:bg-[#090e17] min-h-screen transition-colors duration-200">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Success Banner --}}
        <div class="p-6 sm:p-8 rounded-3xl bg-emerald-600 text-white text-center shadow-lg shadow-emerald-600/20 space-y-3 relative overflow-hidden">
            <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur text-white flex items-center justify-center text-3xl mx-auto mb-2 border border-white/30">
                <i class="fa-solid fa-check"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-[11px] font-black uppercase tracking-wider">
                Booking Status: {{ ucfirst($booking->booking_status ?? $booking->status) }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ $booking->isPrivate() ? 'Private Vehicle Reserved Successfully!' : 'Seats Booked Successfully!' }}
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100 max-w-lg mx-auto">
                Your reservation has been confirmed and registered in the RouteConnect system. Please keep your booking reference for verification upon boarding or pickup.
            </p>
        </div>

        {{-- Ticket & Booking Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            
            {{-- Top Reference Header --}}
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Unique Booking Reference</span>
                    <span class="text-xl sm:text-2xl font-black font-mono text-emerald-400 tracking-wider block mt-0.5">
                        {{ $booking->booking_reference }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    @if($booking->isPrivate())
                        <span class="px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-300 font-bold text-xs font-mono uppercase">
                            Private Vehicle Rental ({{ $booking->days ?: 1 }} {{ \Illuminate\Support\Str::plural('Day', $booking->days ?: 1) }})
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 font-bold text-xs font-mono uppercase">
                            {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }} Reserved
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                @php
                    $isPrivate = $booking->isPrivate();
                    $vehicle = $isPrivate ? $booking->vehicle : $booking->trip?->vehicle;
                    $driver  = $isPrivate ? $booking->vehicle?->driver : $booking->trip?->driver;
                @endphp

                {{-- Route & Trip Schedule --}}
                <div class="space-y-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                            {{ $isPrivate ? 'Rental Route / Locations' : 'Route & Stops' }}
                        </span>
                        <div class="text-lg font-black text-slate-900 dark:text-white mt-0.5">
                            @if($isPrivate)
                                {{ $booking->pickup_location ?: 'Custom Pickup Point' }}
                            @else
                                {{ $booking->trip?->route?->name ?? 'Standard Route' }}
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-sm font-bold text-emerald-700 dark:text-emerald-400 mt-1">
                            @if($isPrivate)
                                <span>{{ $booking->pickup_location ?: 'Pickup Point' }}</span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                                <span>{{ $booking->destination_location ?: 'Destination' }}</span>
                            @else
                                <span>{{ $booking->trip?->route?->origin ?? 'Origin' }}</span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                                <span>{{ $booking->trip?->route?->destination ?? 'Destination' }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-2">
                        @if($isPrivate)
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Pickup Date & Time</span>
                                <span class="font-extrabold text-slate-900 dark:text-white block mt-0.5">
                                    {{ $booking->start_datetime ? $booking->start_datetime->format('d M Y · h:i A') : '—' }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Return Date & Time</span>
                                <span class="font-extrabold font-mono text-slate-900 dark:text-white block mt-0.5">
                                    {{ $booking->end_datetime ? $booking->end_datetime->format('d M Y · h:i A') : '—' }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Rental Fare</span>
                                <span class="font-black font-mono text-emerald-700 dark:text-emerald-400 text-sm block mt-0.5">
                                    Rs. {{ number_format($booking->total_fare ?? 0, 2) }}
                                    <span class="text-[10px] font-normal text-slate-500 dark:text-slate-400 block">({{ $booking->days ?: 1 }} days @ Rs. {{ number_format($booking->daily_rate ?? 0, 2) }}/day)</span>
                                </span>
                            </div>
                        @else
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Travel Date</span>
                                <span class="font-extrabold text-slate-900 dark:text-white block mt-0.5">
                                    {{ $booking->trip?->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, d M Y') : '—' }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Departure Time</span>
                                <span class="font-extrabold font-mono text-slate-900 dark:text-white block mt-0.5">
                                    {{ $booking->trip?->departure_time ? \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') : '—' }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Fare</span>
                                <span class="font-black font-mono text-emerald-700 dark:text-emerald-400 text-sm block mt-0.5">
                                    Rs. {{ number_format($booking->total_fare ?? 0, 2) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Passenger & Driver Details --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-slate-100 dark:border-slate-800 text-xs">
                    <div class="space-y-2">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Passenger Details</span>
                        <div class="font-bold text-slate-900 dark:text-white">{{ $booking->user->name ?? 'Passenger' }}</div>
                        <div class="text-slate-500 dark:text-slate-400 font-mono">{{ $booking->user->email ?? '—' }}</div>
                        @if ($booking->user && $booking->user->phone)
                            <div class="text-slate-500 dark:text-slate-400 font-mono">{{ $booking->user->phone }}</div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Vehicle & Driver</span>
                        <div class="font-bold text-slate-900 dark:text-white">{{ $driver?->name ?? 'Commercial Driver' }}</div>
                        <div class="text-slate-600 dark:text-slate-300 font-mono">{{ $vehicle?->registration_no ?? 'Vehicle' }} ({{ $vehicle?->model ?? '' }})</div>
                        @if(!$isPrivate)
                            <div class="text-slate-400">Boarding at: {{ $booking->trip?->pickup_point ?? 'General Bus Terminal' }}</div>
                        @else
                            <div class="text-slate-400">Capacity: {{ $vehicle?->total_seats ?? '—' }} Seats</div>
                        @endif
                    </div>
                </div>

                {{-- Action Links --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <a href="{{ url('/booking') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black transition-all text-center flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Go to My Bookings</span>
                    </a>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <a href="{{ url('/book-vehicle') }}" class="flex-1 sm:flex-none px-4 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all text-center">
                            <i class="fa-solid fa-car-side mr-1"></i>
                            <span>Book Another</span>
                        </a>
                        @if(!$isPrivate && $booking->trip_id)
                            <a href="{{ url('/trips/' . $booking->trip_id) }}" class="flex-1 sm:flex-none px-4 py-3 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold transition-all text-center">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                <span>Trip Itinerary</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
