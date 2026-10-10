@extends('frontend.layouts.app')

@section('title', 'Booking #' . $booking->booking_reference . ' — RouteConnect')

@section('content')

<section class="py-12 bg-slate-50 dark:bg-[#070D18] min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-900 dark:text-rose-200 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Top Back Link --}}
        <div>
            <a href="{{ url('/my-bookings') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to My Bookings</span>
            </a>
        </div>

        {{-- Ticket & Booking Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
            
            {{-- Header with Reference and Status --}}
            <div class="p-6 sm:p-8 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Booking Reference</span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-emerald-400 tracking-wider block">
                        {{ $booking->booking_reference }}
                    </span>
                    <span class="text-xs text-slate-400 block">
                        Booked on {{ $booking->created_at ? $booking->created_at->format('M d, Y · h:i A') : '—' }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    @php
                        $st = strtolower($booking->booking_status ?? $booking->status);
                    @endphp
                    @if ($st === 'confirmed')
                        <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 font-black text-xs uppercase tracking-wider">
                            Confirmed
                        </span>
                    @elseif ($st === 'cancelled')
                        <span class="px-3.5 py-1.5 rounded-full bg-rose-500/20 border border-rose-400/40 text-rose-300 font-black text-xs uppercase tracking-wider">
                            Cancelled
                        </span>
                    @else
                        <span class="px-3.5 py-1.5 rounded-full bg-slate-800 border border-slate-700 text-slate-300 font-black text-xs uppercase tracking-wider">
                            {{ ucfirst($st) }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-8">

                {{-- Route & Schedule --}}
                <div class="space-y-3 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Route & Trip Details</span>
                    
                    <div class="text-xl font-black text-slate-900 dark:text-white">
                        {{ $booking->trip->route->name ?? 'Standard Route' }}
                    </div>

                    <div class="flex items-center gap-3 text-base font-bold text-emerald-700 dark:text-emerald-400">
                        <span>{{ $booking->trip->route->origin ?? 'Origin' }}</span>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                        <span>{{ $booking->trip->route->destination ?? 'Destination' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs pt-3">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Travel Date</span>
                            <span class="font-extrabold text-slate-900 dark:text-white block mt-0.5">
                                {{ $booking->trip->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, d M Y') : '—' }}
                            </span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Departure Time</span>
                            <span class="font-extrabold font-mono text-slate-900 dark:text-white block mt-0.5">
                                {{ $booking->trip->departure_time ? \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') : '—' }}
                            </span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Seats Booked</span>
                            <span class="font-black font-mono text-slate-900 dark:text-white block mt-0.5">
                                {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }}
                            </span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Fare</span>
                            <span class="font-black font-mono text-emerald-700 dark:text-emerald-400 text-sm block mt-0.5">
                                Rs. {{ number_format($booking->total_fare ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Driver, Vehicle & Boarding Point --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-slate-100 dark:border-slate-800 text-xs">
                    
                    {{-- Driver Card --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Driver</span>
                        <div class="font-black text-slate-900 dark:text-white text-sm">{{ $booking->trip->driver->name ?? 'Commercial Driver' }}</div>
                        <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 dark:text-emerald-400 font-bold">
                            <i class="fa-solid fa-circle-check text-[9px]"></i> Licensed Commercial Operator
                        </span>
                        @if ($booking->trip->driver && $booking->trip->driver->phone)
                            <div class="text-slate-500 dark:text-slate-400 font-mono pt-1">
                                <i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $booking->trip->driver->phone }}
                            </div>
                        @endif
                    </div>

                    {{-- Vehicle Card --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Vehicle</span>
                        <div class="font-mono font-black text-slate-900 dark:text-white text-sm">
                            {{ $booking->trip->vehicle->registration_no ?? 'Vehicle' }}
                        </div>
                        <div class="text-slate-600 dark:text-slate-300 font-medium">
                            {{ $booking->trip->vehicle->model ?? '' }} ({{ $booking->trip->vehicle->type ?? 'Van' }})
                        </div>
                        <div class="text-slate-500 dark:text-slate-400 text-[11px] pt-1">
                            <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> Pickup: {{ $booking->trip->pickup_point ?? 'General Bus Terminal' }}
                        </div>
                    </div>

                </div>

                {{-- Footer Actions --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="{{ url('/my-bookings') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-all text-center">
                            <i class="fa-solid fa-arrow-left mr-1.5"></i>
                            <span>All My Bookings</span>
                        </a>
                        <a href="{{ url('/trips/' . $booking->trip_id) }}" class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white text-xs font-bold transition-all text-center">
                            <i class="fa-solid fa-circle-info mr-1.5"></i>
                            <span>Trip Itinerary</span>
                        </a>
                    </div>

                    @if ($st === 'confirmed')
                        <form method="POST" action="{{ url('/my-bookings/' . $booking->id . '/cancel') }}" onsubmit="return confirm('Are you sure you want to cancel booking #{{ $booking->booking_reference }}? This will return your {{ $booking->seats }} reserved seats to the trip schedule.');">
                            @csrf
                            <button type="submit" id="cancel-booking-btn" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black transition-all shadow-md shadow-rose-600/20 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-ban"></i>
                                <span>Cancel This Booking</span>
                            </button>
                        </form>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 font-bold text-xs border border-rose-200 dark:border-rose-900/50">
                            Booking Cancelled
                        </span>
                    @endif
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
