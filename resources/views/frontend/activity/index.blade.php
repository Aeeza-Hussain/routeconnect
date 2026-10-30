@extends('frontend.layouts.app')

@section('title', 'My Activity — RouteConnect')

@section('content')

{{-- ─── Hero Header ─── --}}
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.2),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-400"></i>
                    <span>{{ $isDriver ? 'Driver Portal Activity' : 'Passenger Activity Hub' }}</span>
                </span>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    My Activity
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    {{ $isDriver ? 'Manage your fleet vehicles, scheduled trips, private reservations, and passenger requests.' : 'Track your seat bookings, private rentals, custom travel requests, and driver offers.' }}
                </p>
            </div>

            <div class="flex items-center gap-3 self-start sm:self-center flex-wrap">
                @if(!$isDriver)
                    <button type="button"
                            onclick="openRequestModal()"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Post Travel Request</span>
                    </button>
                    <a href="{{ url('/trips') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Find a Ride</span>
                    </a>
                @else
                    <a href="{{ route('driver.trips.create') }}" class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-black transition-all shadow-lg shadow-teal-500/30 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Schedule a Trip</span>
                    </a>
                    <a href="{{ route('driver.vehicles.create') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-van-shuttle"></i>
                        <span>Register Vehicle</span>
                    </a>
                @endif
                <a href="{{ route('messages.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-comments text-emerald-400"></i>
                    <span>Open Messages</span>
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

        {{-- ─── Navigation Tabs Bar ─── --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 dark:border-slate-800 scrollbar-none">
            @if(!$isDriver)
                {{-- 1. Seat Bookings --}}
                <a href="{{ route('activity.index', ['tab' => 'seat-bookings']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'seat-bookings' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-chair"></i>
                    <span>Seat Bookings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'seat-bookings' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['seat_bookings'] ?? 0 }}
                    </span>
                </a>

                {{-- 2. Private Vehicle Bookings --}}
                <a href="{{ route('activity.index', ['tab' => 'private-bookings']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'private-bookings' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-car-side"></i>
                    <span>Private Vehicle Bookings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'private-bookings' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['private_bookings'] ?? 0 }}
                    </span>
                </a>

                {{-- 3. My Travel Requests --}}
                <a href="{{ route('activity.index', ['tab' => 'travel-requests']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'travel-requests' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span>My Travel Requests</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'travel-requests' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['travel_requests'] ?? 0 }}
                    </span>
                </a>

                {{-- 4. Driver Applications / Offers --}}
                <a href="{{ route('activity.index', ['tab' => 'driver-offers']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'driver-offers' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Driver Offers</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'driver-offers' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['driver_offers'] ?? 0 }}
                    </span>
                </a>

                {{-- 5. Confirmed Arrangements --}}
                <a href="{{ route('activity.index', ['tab' => 'arrangements']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'arrangements' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Confirmed Arrangements</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'arrangements' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['arrangements'] ?? 0 }}
                    </span>
                </a>

                {{-- 6. History --}}
                <a href="{{ route('activity.index', ['tab' => 'history']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'history' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-history"></i>
                    <span>Completed & Cancelled</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'history' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['history'] ?? 0 }}
                    </span>
                </a>
            @else
                {{-- ─── Driver Navigation Tabs ─── --}}
                {{-- 1. My Vehicle Listings --}}
                <a href="{{ route('activity.index', ['tab' => 'my-vehicles']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'my-vehicles' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-van-shuttle"></i>
                    <span>My Vehicles</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'my-vehicles' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['my_vehicles'] ?? 0 }}
                    </span>
                </a>

                {{-- 2. Shared Ride Listings --}}
                <a href="{{ route('activity.index', ['tab' => 'shared-rides']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'shared-rides' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-route"></i>
                    <span>Shared Ride Listings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'shared-rides' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['shared_rides'] ?? 0 }}
                    </span>
                </a>

                {{-- 3. Private Vehicle Bookings --}}
                <a href="{{ route('activity.index', ['tab' => 'private-bookings']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'private-bookings' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-car-side"></i>
                    <span>Private Bookings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'private-bookings' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['private_bookings'] ?? 0 }}
                    </span>
                </a>

                {{-- 4. Passenger Requests --}}
                <a href="{{ route('activity.index', ['tab' => 'passenger-requests']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'passenger-requests' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Passenger Requests</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'passenger-requests' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['passenger_requests'] ?? 0 }}
                    </span>
                </a>

                {{-- 5. My Applications --}}
                <a href="{{ route('activity.index', ['tab' => 'my-applications']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'my-applications' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>My Applications</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'my-applications' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['my_applications'] ?? 0 }}
                    </span>
                </a>

                {{-- 6. Confirmed Arrangements --}}
                <a href="{{ route('activity.index', ['tab' => 'arrangements']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'arrangements' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Confirmed Arrangements</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'arrangements' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['arrangements'] ?? 0 }}
                    </span>
                </a>

                {{-- 7. History --}}
                <a href="{{ route('activity.index', ['tab' => 'history']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'history' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-history"></i>
                    <span>Completed & Cancelled</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'history' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['history'] ?? 0 }}
                    </span>
                </a>
            @endif
        </div>

        {{-- ─── TAB CONTENT PANELS ─── --}}

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- PASSENGER PANELS --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @if(!$isDriver)

            {{-- PANEL 1: Seat Bookings --}}
            @if($activeTab === 'seat-bookings')
                <div class="space-y-4">
                    @forelse($seatBookings->where('booking_status', '!=', 'cancelled') as $b)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="font-mono text-xs font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full border border-emerald-200 dark:border-emerald-800">
                                        #{{ $b->booking_reference }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $b->isConfirmed() ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                        {{ $b->booking_status }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        <i class="fa-regular fa-calendar text-[11px] mr-1"></i>
                                        {{ $b->trip?->trip_date ? \Carbon\Carbon::parse($b->trip->trip_date)->format('D, d M Y') : 'Scheduled Date' }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>{{ $b->fromStop?->name ?? 'Origin' }}</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-emerald-500"></i>
                                    <span>{{ $b->toStop?->name ?? 'Destination' }}</span>
                                </h3>

                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-chair text-emerald-600 mr-1"></i> {{ $b->seats }} Seat(s)</span>
                                    <span><i class="fa-solid fa-id-badge text-teal-500 mr-1"></i> Driver: {{ $b->trip?->driver?->name ?? 'Assigned Driver' }}</span>
                                    <span><i class="fa-solid fa-van-shuttle text-indigo-500 mr-1"></i> {{ $b->trip?->vehicle?->model ?? 'Commercial Vehicle' }} ({{ $b->trip?->vehicle?->registration_no ?? 'N/A' }})</span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Fare</span>
                                    <span class="font-mono text-xl font-black text-slate-900 dark:text-white">Rs. {{ number_format($b->total_fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('booking.confirmation', $b->id) }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors">
                                        View Pass
                                    </a>
                                    @if($b->trip?->driver)
                                        <a href="{{ route('messages.start') }}?target_driver_id={{ $b->trip->driver->id }}" class="px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold text-xs hover:bg-emerald-100 transition-colors">
                                            <i class="fa-solid fa-comment"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-chair text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No active seat bookings</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">You have not booked any seats on scheduled commercial routes yet.</p>
                            <a href="{{ url('/trips') }}" class="inline-block mt-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs">Browse Trips</a>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PANEL 2: Private Vehicle Bookings --}}
            @if($activeTab === 'private-bookings')
                <div class="space-y-4">
                    @forelse($privateBookings->where('booking_status', '!=', 'cancelled') as $b)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="font-mono text-xs font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-3 py-1 rounded-full border border-indigo-200 dark:border-indigo-800">
                                        #{{ $b->booking_reference }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-500">
                                        Private Rental Confirmed
                                    </span>
                                </div>

                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">
                                    {{ $b->vehicle?->model ?? 'Private Vehicle' }} <span class="font-mono text-sm text-slate-400">({{ $b->vehicle?->registration_no ?? 'N/A' }})</span>
                                </h3>

                                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                    <p><i class="fa-solid fa-location-dot text-emerald-600 mr-1.5"></i> {{ $b->pickup_location }} → {{ $b->destination_location }}</p>
                                    <p><i class="fa-regular fa-clock text-slate-400 mr-1.5"></i> {{ $b->start_datetime?->format('d M Y h:i A') }} to {{ $b->end_datetime?->format('d M Y h:i A') }} ({{ $b->days }} day(s))</p>
                                    <p><i class="fa-solid fa-user text-teal-500 mr-1.5"></i> Driver: {{ $b->vehicle?->driver?->name ?? 'Verified Driver' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Fare</span>
                                    <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($b->total_fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($b->vehicle_id)
                                        <a href="{{ route('messages.start') }}?vehicle_id={{ $b->vehicle_id }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5">
                                            <i class="fa-solid fa-comments"></i>
                                            <span>Message Driver</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('booking.confirmation', $b->id) }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-car-side text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No private vehicle reservations</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">You have not booked any full vehicles yet. Explore available fleet vehicles.</p>
                            <a href="{{ url('/book-vehicle') }}" class="inline-block mt-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs">Book a Vehicle</a>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PANEL 3: My Travel Requests --}}
            @if($activeTab === 'travel-requests')
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Active Travel Requests</h2>
                        <button type="button" onclick="openRequestModal()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span>New Request</span>
                        </button>
                    </div>

                    @forelse($myTravelRequests as $tr)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $tr->isOpen() ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-400' }}">
                                            Status: {{ ucfirst($tr->status) }}
                                        </span>
                                        <span class="text-xs text-slate-400">
                                            Posted on {{ $tr->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                                        <span>{{ $tr->pickup_location }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs text-emerald-500"></i>
                                        <span>{{ $tr->destination_location }}</span>
                                    </h3>
                                </div>

                                <div class="flex items-center gap-2">
                                    <form action="{{ route('activity.requests.destroy', $tr->id) }}" method="POST" onsubmit="return confirm('Close and delete this travel request?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="reason" value="Resolved / Got a driver">
                                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 font-bold text-xs hover:bg-rose-100 transition-colors">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Close Request
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-3 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Date & Time</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tr->travel_date->format('d M Y') }} ({{ $tr->travel_time ?: 'Flexible' }})</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Seats Needed</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tr->seats_needed }} Seats</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Budget</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ $tr->budget ? 'Rs. ' . number_format($tr->budget, 2) : 'Negotiable' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Driver Offers</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $tr->applications->count() }} Offers</span>
                                </div>
                            </div>

                            @if($tr->notes)
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-xs text-slate-600 dark:text-slate-300">
                                    <strong>Notes:</strong> {{ $tr->notes }}
                                </div>
                            @endif

                            {{-- Offers Received on This Request --}}
                            @if($tr->applications->isNotEmpty())
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider block">Driver Offers Received:</span>
                                    <div class="space-y-2">
                                        @foreach($tr->applications as $app)
                                            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                                <div>
                                                    <span class="font-bold text-slate-900 dark:text-white">{{ $app->driver?->name ?? 'Driver' }}</span>
                                                    <span class="text-slate-400 mx-1">·</span>
                                                    <span class="font-mono font-bold text-emerald-600">Rs. {{ number_format($app->offered_fare, 2) }}</span>
                                                    @if($app->proposed_time)
                                                        <span class="text-slate-400 ml-2">({{ $app->proposed_time }})</span>
                                                    @endif
                                                    @if($app->message)
                                                        <p class="text-slate-500 mt-1 italic">"{{ $app->message }}"</p>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('messages.start') }}?driver_application_id={{ $app->id }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1 shadow-sm">
                                                        <i class="fa-solid fa-comments"></i>
                                                        <span>Chat & Negotiate</span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-bullhorn text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No travel requests posted</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Need a ride for a custom route or date? Post a request and drivers will offer rides directly.</p>
                            <button type="button" onclick="openRequestModal()" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs">Post Request Now</button>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PANEL 4: Driver Offers --}}
            @if($activeTab === 'driver-offers')
                <div class="space-y-4">
                    @forelse($driverOffers as $offer)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-500/10 text-teal-400">
                                        Driver Offer
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $offer->created_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                    From: <span class="text-emerald-600">{{ $offer->driver?->name }}</span>
                                </h3>
                                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                    <p>Trip: <strong>{{ $offer->travelRequest?->pickup_location }} → {{ $offer->travelRequest?->destination_location }}</strong></p>
                                    <p>Proposed Time: <strong>{{ $offer->proposed_time ?: 'Flexible' }}</strong></p>
                                    @if($offer->message)
                                        <p class="italic text-slate-600 dark:text-slate-300">"{{ $offer->message }}"</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Offered Fare</span>
                                    <span class="font-mono text-xl font-black text-emerald-600">Rs. {{ number_format($offer->offered_fare, 2) }}</span>
                                </div>
                                <a href="{{ route('messages.start') }}?driver_application_id={{ $offer->id }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-comments"></i>
                                    <span>Chat with Driver</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-hand-holding-dollar text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No pending driver offers</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When drivers apply to your travel requests, their offers and fares will appear here.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PANEL 5: Confirmed Arrangements --}}
            @if($activeTab === 'arrangements')
                <div class="space-y-4">
                    @forelse($confirmedArrangements as $conv)
                        @php
                            $details = $conv->arrangement_details ?? [];
                            $otherUser = $conv->otherUser($user);
                        @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-emerald-500/40 dark:border-emerald-500/30 p-6 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500 text-white shadow-xs">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Confirmed Arrangement
                                    </span>
                                    @if($conv->confirmed_booking_id)
                                        <span class="font-mono text-xs font-bold text-slate-500">
                                            Pass #{{ $conv->confirmedBooking?->booking_reference }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                    {{ $details['route'] ?? $conv->title }}
                                </h3>
                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-user-tie text-teal-500 mr-1"></i> Partner: <strong>{{ $otherUser?->name ?? 'Driver' }}</strong></span>
                                    <span><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> Date: <strong>{{ $details['date'] ?? 'N/A' }}</strong></span>
                                    <span><i class="fa-solid fa-chair text-emerald-500 mr-1"></i> Seats: <strong>{{ $details['seats'] ?? 1 }}</strong></span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Agreed Fare</span>
                                    <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($details['fare'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('messages.show', $conv->id) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>Open Chat</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-handshake text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No confirmed arrangements yet</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When you and a driver reach an agreement in chat and click 'Confirm Arrangement', it will be shown here.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PANEL 6: Completed & Cancelled History --}}
            @if($activeTab === 'history')
                <div class="space-y-4">
                    @forelse($completedBookings as $h)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 opacity-80">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ strtolower($h->booking_status) === 'cancelled' ? 'bg-rose-500/10 text-rose-500' : 'bg-slate-500/10 text-slate-500' }}">
                                    {{ ucfirst($h->booking_status) }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-1">
                                    #{{ $h->booking_reference }} · {{ $h->booking_type === 'private' ? ($h->vehicle?->model ?? 'Private Rental') : ($h->trip?->route?->name ?? 'Trip') }}
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $h->created_at->format('d M Y') }} · Rs. {{ number_format($h->total_fare, 2) }}</p>
                            </div>
                            <span class="text-xs text-slate-400">Archived Record</span>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-history text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No history records</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">Your completed and cancelled trip logs will be archived here.</p>
                        </div>
                    @endforelse
                </div>
            @endif

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- DRIVER PANELS --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @else

            {{-- DRIVER PANEL 1: My Vehicle Listings --}}
            @if($activeTab === 'my-vehicles')
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-white">Registered Fleet Vehicles</h2>
                        <a href="{{ route('driver.vehicles.create') }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span>Register Vehicle</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($myVehicles as $v)
                            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 space-y-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $v->model }}</h3>
                                        <span class="font-mono text-xs text-teal-600 dark:text-teal-400 font-bold">{{ $v->registration_no }} · {{ $v->type }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $v->isApproved() ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                        {{ $v->status }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 gap-2 text-xs p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Capacity</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $v->total_seats }} Seats</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Daily Rate</span>
                                        <span class="font-bold text-emerald-600 font-mono">Rs. {{ number_format($v->rental_rate_per_day, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Rental Ready</span>
                                        <span class="font-bold {{ $v->is_available_for_rental ? 'text-emerald-500' : 'text-slate-400' }}">
                                            {{ $v->is_available_for_rental ? 'Available' : 'Disabled' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <a href="{{ route('driver.vehicles.show', $v->id) }}" class="text-xs font-bold text-teal-500 hover:underline">
                                        Configure Availability & Rates →
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                                <i class="fa-solid fa-van-shuttle text-3xl text-slate-400"></i>
                                <h4 class="text-base font-extrabold text-white">No vehicles registered</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">Register your commercial vehicles to schedule trips and receive private bookings.</p>
                                <a href="{{ route('driver.vehicles.create') }}" class="inline-block mt-2 px-5 py-2.5 rounded-xl bg-teal-500 text-slate-950 font-bold text-xs">Register Vehicle</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- DRIVER PANEL 2: Shared Ride Listings --}}
            @if($activeTab === 'shared-rides')
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-white">Scheduled Public Trips</h2>
                        <a href="{{ route('driver.trips.create') }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>Schedule New Trip</span>
                        </a>
                    </div>

                    @forelse($sharedRideListings as $trip)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $trip->status === 'scheduled' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-500/10 text-slate-400' }}">
                                        {{ $trip->status }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') }} at {{ $trip->departure_time }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-white">
                                    {{ $trip->route?->name ?? 'Trip Route' }}
                                </h3>
                                <div class="text-xs text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-van-shuttle text-teal-400 mr-1"></i> {{ $trip->vehicle?->model }} ({{ $trip->vehicle?->registration_no }})</span>
                                    <span><i class="fa-solid fa-chair text-emerald-400 mr-1"></i> {{ $trip->available_seats }} of {{ $trip->vehicle?->total_seats }} seats open</span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Fare per Seat</span>
                                    <span class="font-mono text-xl font-black text-teal-400">Rs. {{ number_format($trip->fare, 2) }}</span>
                                </div>
                                <a href="{{ route('driver.trips.show', $trip->id) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition-colors">
                                    View Manifest
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-route text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-white">No scheduled trips</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">Schedule commercial trips along verified routes to sell passenger seats.</p>
                            <a href="{{ route('driver.trips.create') }}" class="inline-block mt-2 px-5 py-2.5 rounded-xl bg-teal-500 text-slate-950 font-bold text-xs">Schedule a Trip</a>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 3: Private Vehicle Bookings --}}
            @if($activeTab === 'private-bookings')
                <div class="space-y-4">
                    @forelse($receivedPrivateBookings as $b)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-teal-400 bg-teal-950/60 px-3 py-1 rounded-full border border-teal-800">
                                        #{{ $b->booking_reference }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400">
                                        {{ $b->booking_status }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-white">
                                    Passenger: {{ $b->user?->name ?? 'Passenger' }} · <span class="text-slate-400 font-mono text-xs">{{ $b->user?->phone ?? 'Contact on chat' }}</span>
                                </h3>
                                <div class="text-xs text-slate-400 space-y-1">
                                    <p><i class="fa-solid fa-location-dot text-emerald-400 mr-1.5"></i> {{ $b->pickup_location }} → {{ $b->destination_location }}</p>
                                    <p><i class="fa-regular fa-clock text-slate-400 mr-1.5"></i> {{ $b->start_datetime?->format('d M Y h:i A') }} to {{ $b->end_datetime?->format('d M Y h:i A') }} ({{ $b->days }} day(s))</p>
                                    <p><i class="fa-solid fa-van-shuttle text-teal-400 mr-1.5"></i> Assigned Vehicle: {{ $b->vehicle?->model }} ({{ $b->vehicle?->registration_no }})</p>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Gross Fare</span>
                                    <span class="font-mono text-xl font-black text-teal-400">Rs. {{ number_format($b->total_fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($b->user_id)
                                        <a href="{{ route('messages.start') }}?target_driver_id={{ $b->user_id }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs flex items-center gap-1.5">
                                            <i class="fa-solid fa-comments"></i>
                                            <span>Chat with Passenger</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-car-side text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-white">No private rental reservations yet</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">When passengers reserve your vehicles on Book a Vehicle, their bookings will appear here.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 4: Passenger Requests --}}
            @if($activeTab === 'passenger-requests')
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-white">Open Passenger Ride Requests</h2>
                        <span class="text-xs text-slate-400">Apply with your fare offer & vehicle</span>
                    </div>

                    @forelse($passengerRequests as $req)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400">
                                            Open Request
                                        </span>
                                        <span class="text-xs text-slate-400">Posted by {{ $req->user?->name ?? 'Passenger' }} · {{ $req->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="text-lg font-black text-white flex items-center gap-2">
                                        <span>{{ $req->pickup_location }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs text-teal-400"></i>
                                        <span>{{ $req->destination_location }}</span>
                                    </h3>
                                </div>

                                <button type="button"
                                        onclick="openApplyModal({{ $req->id }}, '{{ addslashes($req->pickup_location) }}', '{{ addslashes($req->destination_location) }}', '{{ $req->budget ?? 0 }}')"
                                        class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs flex items-center gap-2 shadow-lg shadow-teal-500/20">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Offer Ride / Apply</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs p-3 rounded-2xl bg-slate-950/60 border border-slate-800">
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Travel Date</span>
                                    <span class="font-bold text-white">{{ $req->travel_date->format('d M Y') }} ({{ $req->travel_time ?: 'Flexible' }})</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Seats Required</span>
                                    <span class="font-bold text-white">{{ $req->seats_needed }} Seats</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Passenger Budget</span>
                                    <span class="font-bold text-emerald-400 font-mono">{{ $req->budget ? 'Rs. ' . number_format($req->budget, 2) : 'Negotiable' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Current Offers</span>
                                    <span class="font-bold text-teal-400">{{ $req->applications()->count() }} Drivers Applied</span>
                                </div>
                            </div>

                            @if($req->notes)
                                <p class="text-xs text-slate-400 italic">"{{ $req->notes }}"</p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-users text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-white">No open passenger requests</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">Check back soon for new travel requests submitted by passengers.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 5: My Applications --}}
            @if($activeTab === 'my-applications')
                <div class="space-y-4">
                    @forelse($myApplications as $app)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $app->status === 'accepted' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-500/10 text-slate-400' }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $app->created_at->format('d M Y') }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-white">
                                    {{ $app->travelRequest?->pickup_location }} → {{ $app->travelRequest?->destination_location }}
                                </h3>
                                <p class="text-xs text-slate-400">
                                    Passenger: {{ $app->travelRequest?->user?->name }} · {{ $app->travelRequest?->seats_needed }} Seats
                                </p>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Offered Fare</span>
                                    <span class="font-mono text-xl font-black text-teal-400">Rs. {{ number_format($app->offered_fare, 2) }}</span>
                                </div>
                                <a href="{{ route('messages.start') }}?driver_application_id={{ $app->id }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-comments"></i>
                                    <span>Chat with Passenger</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-paper-plane text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-white">No applications submitted</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">Explore open passenger requests to offer your services.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 6: Confirmed Arrangements --}}
            @if($activeTab === 'arrangements')
                <div class="space-y-4">
                    @forelse($confirmedArrangements as $conv)
                        @php
                            $details = $conv->arrangement_details ?? [];
                            $otherUser = $conv->otherUser($user);
                        @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-teal-500/40 p-6 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-teal-500 text-slate-950">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Confirmed Agreement
                                </span>
                                <h3 class="text-lg font-black text-white">
                                    {{ $details['route'] ?? $conv->title }}
                                </h3>
                                <div class="text-xs text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-user text-emerald-400 mr-1"></i> Passenger: <strong>{{ $otherUser?->name }}</strong></span>
                                    <span><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> Date: <strong>{{ $details['date'] ?? 'N/A' }}</strong></span>
                                    <span><i class="fa-solid fa-chair text-teal-400 mr-1"></i> Seats: <strong>{{ $details['seats'] ?? 1 }}</strong></span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Agreed Fare</span>
                                    <span class="font-mono text-xl font-black text-teal-400">Rs. {{ number_format($details['fare'] ?? 0, 2) }}</span>
                                </div>
                                <a href="{{ route('messages.show', $conv->id) }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-comments"></i>
                                    <span>Open Chat</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-handshake text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-white">No confirmed arrangements yet</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">When you confirm travel arrangements in direct chat, they will show up here.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 7: History --}}
            @if($activeTab === 'history')
                <div class="space-y-4">
                    @forelse($driverHistory as $dh)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex items-center justify-between opacity-80">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-800 text-slate-400">
                                    {{ ucfirst($dh->status) }}
                                </span>
                                <h3 class="text-base font-bold text-white mt-1">{{ $dh->route?->name ?? 'Trip' }}</h3>
                                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($dh->trip_date)->format('d M Y') }} · Rs. {{ number_format($dh->fare, 2) }}</p>
                            </div>
                            <span class="text-xs text-slate-500">Archived</span>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-history text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-white">No history records</h4>
                        </div>
                    @endforelse
                </div>
            @endif

        @endif

    </div>
</section>

{{-- ─── Modal 1: Passenger Post Travel Request ─── --}}
<div id="requestModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-emerald-600"></i>
                <span>Post a Travel Request</span>
            </h3>
            <button type="button" onclick="closeRequestModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('activity.requests.store') }}" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pickup Location *</label>
                    <input type="text" name="pickup_location" required placeholder="e.g. Gilgit Airport or City" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Destination *</label>
                    <input type="text" name="destination_location" required placeholder="e.g. Hunza Karimabad" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Travel Date *</label>
                    <input type="date" name="travel_date" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Time (Optional)</label>
                    <input type="time" name="travel_time" value="09:00" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Seats Needed *</label>
                    <input type="number" name="seats_needed" min="1" max="50" value="1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Estimated Budget (PKR, Optional)</label>
                <input type="number" name="budget" placeholder="e.g. 1500" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Notes / Luggage Details</label>
                <textarea name="notes" rows="2" placeholder="Any specific requirements, stops along the way, or contact notes..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeRequestModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Cancel</button>
                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black shadow-md">Post Request</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal 2: Driver Apply / Offer Ride ─── --}}
<div id="applyModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-teal-500"></i>
                <span>Submit Ride Offer</span>
            </h3>
            <button type="button" onclick="closeApplyModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="text-xs text-slate-400" id="applyModalRouteText"></p>

        <form method="POST" id="applyForm" action="" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Your Offered Total Fare (PKR) *</label>
                <input type="number" name="offered_fare" id="applyOfferedFare" required min="50" placeholder="e.g. 2000" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Proposed Departure Time</label>
                    <input type="text" name="proposed_time" placeholder="e.g. 09:30 AM" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Assign Fleet Vehicle</label>
                    <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                        <option value="">Select a Vehicle (Optional)</option>
                        @if($isDriver)
                            @foreach($myVehicles as $veh)
                                <option value="{{ $veh->id }}">{{ $veh->model }} ({{ $veh->registration_no }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Message to Passenger</label>
                <textarea name="message" rows="3" placeholder="Hello! I am traveling around that time and have comfortable seats available..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeApplyModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Cancel</button>
                <button type="submit" class="px-6 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black shadow-md">Send Offer & Start Chat</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRequestModal() {
    const modal = document.getElementById('requestModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeRequestModal() {
    const modal = document.getElementById('requestModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openApplyModal(requestId, from, to, budget) {
    const modal = document.getElementById('applyModal');
    const form = document.getElementById('applyForm');
    const routeText = document.getElementById('applyModalRouteText');
    const fareInput = document.getElementById('applyOfferedFare');

    if (modal && form) {
        form.action = `/my-activity/requests/${requestId}/apply`;
        if (routeText) routeText.innerText = `Route: ${from} → ${to}`;
        if (fareInput && budget > 0) fareInput.value = budget;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeApplyModal() {
    const modal = document.getElementById('applyModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>

@endsection

