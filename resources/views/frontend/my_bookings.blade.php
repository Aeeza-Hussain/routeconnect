@extends('frontend.layouts.app')

@section('title', 'My Bookings — RouteConnect')

@section('content')

{{-- ─── Hero Header ─── --}}
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.15),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-ticket text-emerald-400"></i> Passenger Portal
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    My Bookings
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    Manage your reserved trips, view digital passes, and track seat status.
                </p>
            </div>

            <div class="self-start sm:self-center">
                <a href="{{ url('/trips') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Find More Trips</span>
                </a>
            </div>
        </div>

    </div>
</section>

{{-- ─── Main Content ─── --}}
<section class="py-10 bg-slate-50 dark:bg-[#070D18] min-h-screen transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Flash Success & Error Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Bookings Table Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-emerald-600 dark:text-emerald-400"></i>
                        <span>Travel Reservation History</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">All scheduled and completed bookings under your account.</p>
                </div>
                <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 font-mono text-slate-700 dark:text-slate-300 text-xs font-bold self-start sm:self-center">
                    {{ $bookings->total() }} {{ \Illuminate\Support\Str::plural('Booking', $bookings->total()) }}
                </span>
            </div>

            @if ($bookings->isEmpty())
                <div class="py-16 text-center space-y-4 px-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-regular fa-calendar-xmark"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-200">No Bookings Found</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                            You have not booked any passenger seats yet. Browse scheduled trips to reserve your transport.
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('/trips') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold transition-all">
                            <i class="fa-solid fa-bus"></i>
                            <span>Search Scheduled Trips</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-4">Booking Ref</th>
                                <th class="px-5 py-4">Route</th>
                                <th class="px-5 py-4">Driver</th>
                                <th class="px-5 py-4">Vehicle</th>
                                <th class="px-5 py-4">Travel Date</th>
                                <th class="px-5 py-4">Departure Time</th>
                                <th class="px-5 py-4 text-center">Seats</th>
                                <th class="px-5 py-4">Booking Status</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            @foreach ($bookings as $booking)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors">
                                    {{-- 1. Booking Reference --}}
                                    <td class="px-5 py-4 font-mono font-black text-slate-900 dark:text-white">
                                        <a href="{{ url('/my-bookings/' . $booking->id) }}" class="text-emerald-700 dark:text-emerald-400 hover:underline">
                                            {{ $booking->booking_reference }}
                                        </a>
                                    </td>

                                    {{-- 2. Route --}}
                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                                            {{ $booking->trip->route->name ?? 'Standard Route' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                            <span>{{ $booking->trip->route->origin ?? 'Origin' }}</span>
                                            <i class="fa-solid fa-arrow-right text-[9px] text-slate-400 dark:text-slate-500"></i>
                                            <span>{{ $booking->trip->route->destination ?? 'Destination' }}</span>
                                        </div>
                                    </td>

                                    {{-- 3. Driver --}}
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $booking->trip->driver->name ?? 'Commercial Driver' }}
                                        </div>
                                        <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 dark:text-emerald-400 font-semibold">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Verified
                                        </span>
                                    </td>

                                    {{-- 4. Vehicle --}}
                                    <td class="px-5 py-4">
                                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-[11px] block w-fit">
                                            {{ $booking->trip->vehicle->registration_no ?? '—' }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block truncate max-w-[130px]">
                                            {{ $booking->trip->vehicle->model ?? 'Vehicle' }}
                                        </span>
                                    </td>

                                    {{-- 5. Travel Date --}}
                                    <td class="px-5 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $booking->trip->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, d M Y') : '—' }}
                                    </td>

                                    {{-- 6. Departure Time --}}
                                    <td class="px-5 py-4 font-mono font-extrabold text-emerald-700 dark:text-emerald-400">
                                        <i class="fa-regular fa-clock text-[10px] text-slate-400 dark:text-slate-500 mr-1"></i>
                                        {{ $booking->trip->departure_time ? \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') : '—' }}
                                    </td>

                                    {{-- 7. Seats --}}
                                    <td class="px-5 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 font-mono font-black text-slate-900 dark:text-slate-200 text-xs">
                                            {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }}
                                        </span>
                                    </td>

                                    {{-- 8. Booking Status --}}
                                    <td class="px-5 py-4">
                                        @php
                                            $st = strtolower($booking->booking_status ?? $booking->status);
                                        @endphp
                                        @if ($st === 'confirmed')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-black text-[11px] uppercase tracking-wide">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Confirmed
                                            </span>
                                        @elseif ($st === 'cancelled')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-black text-[11px] uppercase tracking-wide">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Cancelled
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-black text-[11px] uppercase tracking-wide">
                                                {{ ucfirst($st) }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 9. Actions (View Details & Cancel Booking) --}}
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- View Details --}}
                                            <a href="{{ url('/my-bookings/' . $booking->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold transition-all flex items-center gap-1">
                                                <i class="fa-solid fa-eye text-slate-500"></i>
                                                <span>View Details</span>
                                            </a>

                                            {{-- Cancel Booking --}}
                                            @if ($st === 'confirmed')
                                                <form method="POST" action="{{ url('/my-bookings/' . $booking->id . '/cancel') }}" onsubmit="return confirm('Are you sure you want to cancel this booking (#{{ $booking->booking_reference }})? Your reserved seats will be returned to the trip schedule.');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-bold transition-all flex items-center gap-1">
                                                        <i class="fa-solid fa-xmark text-rose-500"></i>
                                                        <span>Cancel</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="px-3 py-1.5 rounded-lg bg-slate-50 text-slate-400 text-[11px] font-semibold border border-slate-100 cursor-not-allowed">
                                                    Cancelled
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if ($bookings->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $bookings->links() }}
                    </div>
                @endif
            @endif

        </div>

    </div>
</section>

@endsection
