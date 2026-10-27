@extends('frontend.layouts.app')

@section('title', 'My Bookings — RouteConnect')

@section('content')

{{-- ─── Hero Header ─── --}}
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.15),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-ticket text-emerald-400"></i> Passenger Portal
                </span>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    My Bookings
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    Manage your reserved trips, private vehicle rentals, and digital booking passes.
                </p>
            </div>

            <div class="flex items-center gap-3 self-start sm:self-center">
                <a href="{{ url('/book-vehicle') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-car-side"></i>
                    <span>Book a Vehicle</span>
                </a>
                <a href="{{ url('/trips') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-bus"></i>
                    <span>Search Trips</span>
                </a>
            </div>
        </div>

    </div>
</section>

{{-- ─── Main Content ─── --}}
<section class="py-10 bg-slate-50 dark:bg-[#090e17] min-h-screen transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-300 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        @guest
            {{-- Guest Notice Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 sm:p-12 text-center shadow-sm">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Sign In to View Your Bookings</h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-2 leading-relaxed">
                    Please log in to your passenger account to track your confirmed seats, view private vehicle reservations, and manage cancellations.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all">
                        Log In Now
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-600 text-xs font-bold transition-all">
                        Create Account
                    </a>
                    <a href="{{ url('/book-vehicle') }}" class="px-6 py-2.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-bold transition-all">
                        Browse Vehicles
                    </a>
                </div>
            </div>
        @else
            {{-- Category Filter Tabs --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 dark:border-slate-800">
                <a href="{{ url('/booking?tab=all') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ ($tab ?? 'all') === 'all' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-list-check"></i>
                    <span>All Bookings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ ($tab ?? 'all') === 'all' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['all'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ url('/booking?tab=upcoming') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ ($tab ?? 'all') === 'upcoming' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Upcoming</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ ($tab ?? 'all') === 'upcoming' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['upcoming'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ url('/booking?tab=completed') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ ($tab ?? 'all') === 'completed' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-flag-checkered"></i>
                    <span>Completed</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ ($tab ?? 'all') === 'completed' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['completed'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ url('/booking?tab=cancelled') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ ($tab ?? 'all') === 'cancelled' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-ban"></i>
                    <span>Cancelled</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ ($tab ?? 'all') === 'cancelled' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['cancelled'] ?? 0 }}
                    </span>
                </a>
            </div>

            {{-- Bookings Table Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-emerald-600 dark:text-emerald-400"></i>
                            <span>Travel Reservation History</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Showing {{ ucfirst($tab ?? 'all') }} passenger bookings and vehicle reservations.
                        </p>
                    </div>
                    @if(isset($bookings) && method_exists($bookings, 'total'))
                        <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 font-mono text-slate-700 dark:text-slate-300 text-xs font-bold self-start sm:self-center">
                            {{ $bookings->total() }} {{ \Illuminate\Support\Str::plural('Booking', $bookings->total()) }}
                        </span>
                    @endif
                </div>

                @if ($bookings->isEmpty())
                    {{-- Helpful Empty State --}}
                    <div class="py-16 text-center space-y-4 px-4">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                            <i class="fa-regular fa-calendar-xmark"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">No Bookings Found</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                                You have no {{ $tab !== 'all' ? $tab : '' }} bookings registered on your account yet. Explore our scheduled shared routes or reserve an entire vehicle privately.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                            <a href="{{ url('/trips') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold transition-all border border-slate-700">
                                <i class="fa-solid fa-bus text-emerald-400"></i>
                                <span>Search Scheduled Trips</span>
                            </a>
                            <a href="{{ url('/book-vehicle') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20">
                                <i class="fa-solid fa-car-side"></i>
                                <span>Book a Vehicle</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-5 py-4">Booking Ref</th>
                                    <th class="px-5 py-4">Type</th>
                                    <th class="px-5 py-4">Route / Location</th>
                                    <th class="px-5 py-4">Vehicle & Driver</th>
                                    <th class="px-5 py-4">Schedule / Dates</th>
                                    <th class="px-5 py-4 text-center">Seats / Duration</th>
                                    <th class="px-5 py-4">Total Fare</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                @foreach ($bookings as $booking)
                                    @php
                                        $st = strtolower($booking->booking_status ?? $booking->status);
                                        $isPrivate = $booking->isPrivate();
                                        $vehicle = $isPrivate ? $booking->vehicle : $booking->trip?->vehicle;
                                        $driver  = $isPrivate ? $booking->vehicle?->driver : $booking->trip?->driver;
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                        {{-- 1. Reference --}}
                                        <td class="px-5 py-4 font-mono font-black text-slate-900 dark:text-white">
                                            <a href="{{ url('/my-bookings/' . $booking->id) }}" class="text-emerald-700 dark:text-emerald-400 hover:underline">
                                                {{ $booking->booking_reference }}
                                            </a>
                                        </td>

                                        {{-- 2. Booking Type --}}
                                        <td class="px-5 py-4">
                                            @if($isPrivate)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 font-extrabold text-[10px] uppercase tracking-wider">
                                                    <i class="fa-solid fa-car-side text-[9px]"></i> Private Rental
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] uppercase tracking-wider">
                                                    <i class="fa-solid fa-bus text-[9px]"></i> Shared Trip
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 3. Route / Location --}}
                                        <td class="px-5 py-4">
                                            @if($isPrivate)
                                                <div class="font-extrabold text-slate-900 dark:text-white text-xs">
                                                    {{ $booking->pickup_location ?: 'Custom Pickup' }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                    <i class="fa-solid fa-arrow-right text-[9px] text-slate-400"></i>
                                                    <span>{{ $booking->destination_location ?: 'Custom Destination' }}</span>
                                                </div>
                                            @else
                                                <div class="font-extrabold text-slate-900 dark:text-white text-xs">
                                                    {{ $booking->trip?->route?->name ?? 'Standard Route' }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                    <span>{{ $booking->trip?->route?->origin ?? 'Origin' }}</span>
                                                    <i class="fa-solid fa-arrow-right text-[9px] text-slate-400"></i>
                                                    <span>{{ $booking->trip?->route?->destination ?? 'Destination' }}</span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 4. Vehicle & Driver --}}
                                        <td class="px-5 py-4">
                                            <div class="font-mono font-bold text-slate-900 dark:text-slate-200">
                                                {{ $vehicle?->registration_no ?? '—' }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">
                                                {{ $vehicle?->model ?? 'Vehicle' }} ({{ $vehicle?->type ?? '' }})
                                            </div>
                                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">
                                                <i class="fa-solid fa-user-check text-[9px]"></i> {{ $driver?->name ?? 'Driver' }}
                                            </div>
                                        </td>

                                        {{-- 5. Schedule / Dates --}}
                                        <td class="px-5 py-4">
                                            @if($isPrivate)
                                                <div class="font-semibold text-slate-900 dark:text-slate-200 text-xs">
                                                    {{ $booking->start_datetime ? $booking->start_datetime->format('d M Y') : '—' }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                                    to {{ $booking->end_datetime ? $booking->end_datetime->format('d M Y') : '—' }}
                                                </div>
                                            @else
                                                <div class="font-semibold text-slate-900 dark:text-slate-200 text-xs">
                                                    {{ $booking->trip?->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, d M Y') : '—' }}
                                                </div>
                                                <div class="text-[11px] text-emerald-700 dark:text-emerald-400 font-mono font-bold">
                                                    <i class="fa-regular fa-clock text-[9px] mr-0.5"></i>
                                                    {{ $booking->trip?->departure_time ? \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') : '—' }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 6. Seats / Duration --}}
                                        <td class="px-5 py-4 text-center">
                                            @if($isPrivate)
                                                <span class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 font-mono font-bold text-indigo-700 dark:text-indigo-300 text-xs">
                                                    {{ $booking->days ?: 1 }} {{ \Illuminate\Support\Str::plural('Day', $booking->days ?: 1) }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 font-mono font-bold text-slate-900 dark:text-white text-xs">
                                                    {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 7. Fare --}}
                                        <td class="px-5 py-4 font-mono font-black text-emerald-700 dark:text-emerald-400">
                                            Rs. {{ number_format($booking->total_fare ?? 0, 2) }}
                                        </td>

                                        {{-- 8. Status --}}
                                        <td class="px-5 py-4">
                                            @if ($st === 'confirmed')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-black text-[10px] uppercase tracking-wide">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Confirmed
                                                </span>
                                            @elseif ($st === 'cancelled')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-black text-[10px] uppercase tracking-wide">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Cancelled
                                                </span>
                                            @elseif ($st === 'completed')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-black text-[10px] uppercase tracking-wide">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-black text-[10px] uppercase tracking-wide">
                                                    {{ ucfirst($st) }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 9. Actions --}}
                                        <td class="px-5 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ url('/my-bookings/' . $booking->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-[11px] font-bold transition-all flex items-center gap-1">
                                                    <i class="fa-solid fa-eye text-slate-500"></i>
                                                    <span>Details</span>
                                                </a>

                                                @if ($st === 'confirmed')
                                                    <form method="POST" action="{{ url('/my-bookings/' . $booking->id . '/cancel') }}" onsubmit="return confirm('Are you sure you want to cancel this booking (#{{ $booking->booking_reference }})?');">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-[11px] font-bold transition-all flex items-center gap-1">
                                                            <i class="fa-solid fa-xmark text-rose-500"></i>
                                                            <span>Cancel</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($bookings->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                @endif

            </div>
        @endguest

        {{-- Theme Settings Card --}}
        <div class="mt-8">
            @include('partials.theme_settings_card')
        </div>

    </div>
</section>

@endsection
