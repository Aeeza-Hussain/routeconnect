@extends('frontend.layouts.app')

@section('title', 'Booking Confirmed — ' . $booking->booking_reference . ' — RouteConnect')

@section('content')

<section class="py-12 bg-slate-50 dark:bg-[#070D18] min-h-screen">
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
                Seats Booked Successfully!
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100 max-w-lg mx-auto">
                Your reservation has been confirmed and registered in the RouteConnect schedule. Please present your booking reference upon boarding.
            </p>
        </div>

        {{-- Ticket & Booking Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
            
            {{-- Top Reference Header --}}
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Unique Booking Reference</span>
                    <span class="text-xl sm:text-2xl font-black font-mono text-emerald-400 tracking-wider block mt-0.5">
                        {{ $booking->booking_reference }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 font-bold text-xs font-mono uppercase">
                        {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }} Reserved
                    </span>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Route & Trip Schedule --}}
                <div class="space-y-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Route</span>
                        <div class="text-lg font-black text-slate-900 dark:text-white mt-0.5">
                            {{ $booking->trip->route->name ?? 'Standard Route' }}
                        </div>
                        <div class="flex items-center gap-2 text-sm font-bold text-emerald-700 dark:text-emerald-400 mt-1">
                            <span>{{ $booking->trip->route->origin ?? 'Origin' }}</span>
                            <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                            <span>{{ $booking->trip->route->destination ?? 'Destination' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-2">
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
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Fare</span>
                            <span class="font-black font-mono text-emerald-700 dark:text-emerald-400 text-sm block mt-0.5">
                                Rs. {{ number_format($booking->total_fare ?? 0, 2) }}
                            </span>
                        </div>
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
                        <div class="font-bold text-slate-900 dark:text-white">{{ $booking->trip->driver->name ?? 'Commercial Driver' }}</div>
                        <div class="text-slate-600 dark:text-slate-300 font-mono">{{ $booking->trip->vehicle->registration_no ?? 'Vehicle' }} ({{ $booking->trip->vehicle->model ?? '' }})</div>
                        <div class="text-slate-400">Boarding at: {{ $booking->trip->pickup_point ?? 'General Bus Terminal' }}</div>
                    </div>
                </div>

                {{-- Action Links --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <a href="{{ url('/trips') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all text-center">
                        <i class="fa-solid fa-magnifying-glass mr-1.5"></i>
                        <span>Search More Trips</span>
                    </a>
                    <a href="{{ url('/trips/' . $booking->trip_id) }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white text-xs font-bold transition-all text-center">
                        <i class="fa-solid fa-circle-info mr-1.5"></i>
                        <span>View Trip Details</span>
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
