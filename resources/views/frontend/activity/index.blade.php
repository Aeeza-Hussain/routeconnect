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
                    {{ $isDriver ? 'Manage your scheduled trips, vehicle listings, passenger requests, bids, and confirmed bookings.' : 'Track your seat bookings, private rentals, custom travel requests, and driver offers.' }}
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
                {{-- PASSENGER TABS --}}
                {{-- 1. My Seat Bookings --}}
                <a href="{{ route('activity.index', ['tab' => 'seat-bookings']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'seat-bookings' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-chair"></i>
                    <span>My Seat Bookings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'seat-bookings' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['seat_bookings'] ?? 0 }}
                    </span>
                </a>

                {{-- 2. My Private Vehicle Bookings --}}
                <a href="{{ route('activity.index', ['tab' => 'private-bookings']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'private-bookings' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-car-side"></i>
                    <span>My Private Vehicle Bookings</span>
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

                {{-- 4. Applications Received --}}
                <a href="{{ route('activity.index', ['tab' => 'applications-received']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'applications-received' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Applications Received (Driver Offers)</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'applications-received' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['applications_received'] ?? 0 }}
                    </span>
                </a>

                {{-- 5. Confirmed Arrangements --}}
                <a href="{{ route('activity.index', ['tab' => 'confirmed-arrangements']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'confirmed-arrangements' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Confirmed Arrangements</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'confirmed-arrangements' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['confirmed_arrangements'] ?? 0 }}
                    </span>
                </a>

                {{-- 6. Completed Trips --}}
                <a href="{{ route('activity.index', ['tab' => 'completed-trips']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'completed-trips' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>Completed Trips</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'completed-trips' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['completed_trips'] ?? 0 }}
                    </span>
                </a>

                {{-- 7. Cancelled and Expired Records --}}
                <a href="{{ route('activity.index', ['tab' => 'cancelled-expired']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'cancelled-expired' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-ban text-rose-500"></i>
                    <span>Cancelled & Expired (Completed & Cancelled)</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'cancelled-expired' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['cancelled_expired'] ?? 0 }}
                    </span>
                </a>
            @else
                {{-- DRIVER TABS --}}
                {{-- 1. My Shared Ride Listings --}}
                <a href="{{ route('activity.index', ['tab' => 'shared-rides']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'shared-rides' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-route"></i>
                    <span>Shared Ride Listings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'shared-rides' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['shared_rides'] ?? 0 }}
                    </span>
                </a>

                {{-- 2. My Private Vehicle Listings --}}
                <a href="{{ route('activity.index', ['tab' => 'my-vehicles']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'my-vehicles' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-van-shuttle"></i>
                    <span>My Vehicles</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'my-vehicles' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['my_vehicles'] ?? 0 }}
                    </span>
                </a>

                {{-- 3. My Bookings (Received) --}}
                <a href="{{ route('activity.index', ['tab' => 'my-bookings']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'my-bookings' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>My Bookings & Private Bookings</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'my-bookings' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['my_bookings'] ?? 0 }}
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

                {{-- 5. My Submitted Applications --}}
                <a href="{{ route('activity.index', ['tab' => 'my-applications']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'my-applications' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>My Applications</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'my-applications' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['my_applications'] ?? 0 }}
                    </span>
                </a>

                {{-- 6. Confirmed Arrangements --}}
                <a href="{{ route('activity.index', ['tab' => 'confirmed-arrangements']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'confirmed-arrangements' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Confirmed Arrangements</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'confirmed-arrangements' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['confirmed_arrangements'] ?? 0 }}
                    </span>
                </a>

                {{-- 7. Completed Trips --}}
                <a href="{{ route('activity.index', ['tab' => 'completed-trips']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'completed-trips' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>Completed Trips</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'completed-trips' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['completed_trips'] ?? 0 }}
                    </span>
                </a>

                {{-- 8. Cancelled & Expired Records --}}
                <a href="{{ route('activity.index', ['tab' => 'cancelled-expired']) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $activeTab === 'cancelled-expired' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <i class="fa-solid fa-ban text-rose-500"></i>
                    <span>Cancelled & Expired</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'cancelled-expired' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $counts['cancelled_expired'] ?? 0 }}
                    </span>
                </a>
            @endif
        </div>

        {{-- ─── TAB CONTENT PANELS ─── --}}

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- PASSENGER PANELS --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @if(!$isDriver)

            {{-- PASSENGER PANEL 1: Seat Bookings --}}
            @if($activeTab === 'seat-bookings')
                <div class="space-y-4">
                    @forelse($seatBookings as $b)
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
                                        {{ $b->trip?->trip_date ? \Carbon\Carbon::parse($b->trip->trip_date)->format('D, d M Y') : 'Upcoming' }}
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
                                    <span><i class="fa-solid fa-van-shuttle text-indigo-500 mr-1"></i> {{ $b->trip?->vehicle?->model ?? 'Vehicle' }} ({{ $b->trip?->vehicle?->registration_no ?? 'N/A' }})</span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Fare</span>
                                    <span class="font-mono text-xl font-black text-slate-900 dark:text-white">Rs. {{ number_format($b->total_fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('passenger.bookings.show', $b->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold text-xs transition-colors">
                                        View Details
                                    </a>
                                    @if($b->trip?->driver)
                                        <a href="{{ route('messages.start') }}?target_driver_id={{ $b->trip->driver->id }}" class="px-3.5 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold text-xs hover:bg-emerald-100 transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-comments"></i>
                                            <span>Message Driver</span>
                                        </a>
                                    @endif
                                    @if(!$b->isCancelled() && !$b->isCompleted())
                                        <form action="{{ route('activity.bookings.cancel', $b->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold text-xs hover:bg-rose-100">
                                                Cancel Booking
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-chair text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No active seat bookings</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Explore scheduled shared trips to find affordable seats for your next journey.</p>
                            <a href="{{ url('/trips') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">
                                <span>Find a Ride</span>
                            </a>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PASSENGER PANEL 2: Private Vehicle Bookings --}}
            @if($activeTab === 'private-bookings')
                <div class="space-y-4">
                    @forelse($privateBookings as $b)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="font-mono text-xs font-black text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-3 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                                        #{{ $b->booking_reference }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-500/10 text-indigo-500">
                                        Private Rental · {{ ucfirst($b->booking_status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        {{ $b->start_datetime ? $b->start_datetime->format('d M Y') : 'Start Date' }}
                                        @if($b->end_datetime) → {{ $b->end_datetime->format('d M Y') }} ({{ $b->days }} day(s)) @endif
                                    </span>
                                </div>

                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <i class="fa-solid fa-van-shuttle text-teal-500 text-sm"></i>
                                    <span>{{ $b->vehicle?->model ?? 'Private Fleet Vehicle' }}</span>
                                    <span class="text-xs font-mono text-slate-400">({{ $b->vehicle?->registration_no ?? 'N/A' }})</span>
                                </h3>

                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> {{ $b->pickup_location ?? 'Origin' }} → {{ $b->destination_location ?? 'Destination' }}</span>
                                    <span><i class="fa-solid fa-user-tie text-teal-500 mr-1"></i> Driver: {{ $b->vehicle?->driver?->name ?? $b->driver?->name ?? 'Verified Partner' }}</span>
                                    <span><i class="fa-solid fa-users text-indigo-500 mr-1"></i> {{ $b->vehicle?->total_seats ?? $b->seats }} Seats Capacity</span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Rental Fare</span>
                                    <span class="font-mono text-xl font-black text-slate-900 dark:text-white">Rs. {{ number_format($b->total_fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('passenger.bookings.show', $b->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                                        View Details
                                    </a>
                                    @if($b->vehicle_id)
                                        <a href="{{ route('messages.start') }}?vehicle_id={{ $b->vehicle_id }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                                            <i class="fa-solid fa-comments"></i>
                                            <span>Message Driver</span>
                                        </a>
                                    @endif
                                    @if(!$b->isCancelled() && !$b->isCompleted())
                                        <form action="{{ route('activity.bookings.cancel', $b->id) }}" method="POST" onsubmit="return confirm('Cancel this private booking?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold text-xs hover:bg-rose-100">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-car-side text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No active private bookings</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Need a private vehicle for your family or tour group? Browse our private fleet.</p>
                            <a href="{{ route('vehicles.book') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">
                                <span>Book a Private Vehicle</span>
                            </a>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PASSENGER PANEL 3: My Travel Requests --}}
            @if($activeTab === 'travel-requests')
                <div class="space-y-4">
                    @forelse($myTravelRequests as $req)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $req->isOpen() ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-teal-500/10 text-teal-400' }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                        <span class="text-xs text-slate-400">
                                            <i class="fa-regular fa-calendar text-[11px] mr-1"></i>
                                            {{ $req->travel_date->format('d M Y') }} ({{ $req->travel_time ?: 'Flexible Time' }})
                                        </span>
                                        <span class="text-xs text-slate-400">
                                            <i class="fa-solid fa-users text-[11px] mr-1"></i>
                                            {{ $req->seats_needed }} Passenger(s)
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                        <span>{{ $req->pickup_location }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs text-emerald-500"></i>
                                        <span>{{ $req->destination_location }}</span>
                                    </h3>
                                </div>

                                <div class="flex items-center gap-2 self-start sm:self-center flex-wrap">
                                    @if($req->isOpen())
                                        <button type="button"
                                                onclick='openEditRequestModal(@json($req))'
                                                class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center gap-1.5 transition-colors">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>Edit Request</span>
                                        </button>
                                        <form action="{{ route('activity.requests.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this travel request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold text-xs hover:bg-rose-100 flex items-center gap-1.5 transition-colors">
                                                <i class="fa-solid fa-xmark"></i>
                                                <span>Cancel</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Metrics strip --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Vehicle Preference</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $req->preferred_vehicle_type ?: 'Any Vehicle' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Budget</span>
                                    <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $req->budget ? 'Rs. ' . number_format($req->budget, 2) : 'Negotiable' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Received Offers</span>
                                    <span class="font-bold text-teal-600 dark:text-teal-400">{{ $req->applications->count() }} Driver(s) Applied</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Selected Driver</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $req->selectedApplication?->driver?->name ?? 'Pending Selection' }}</span>
                                </div>
                            </div>

                            @if(!empty($req->intermediate_stops))
                                <div class="flex items-center gap-2 flex-wrap text-xs">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">Via Stops:</span>
                                    @foreach($req->intermediate_stops as $st)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $st }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if($req->notes)
                                <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                                    <i class="fa-solid fa-comment-dots text-slate-400 mr-1"></i> "{{ $req->notes }}"
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-bullhorn text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No travel requests yet</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Can't find a ride for your specific schedule? Post a custom travel request and approved drivers will make offers.</p>
                            <button type="button" onclick="openRequestModal()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">
                                <span>Post Travel Request</span>
                            </button>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PASSENGER PANEL 4: Applications Received --}}
            @if($activeTab === 'applications-received')
                <div class="space-y-4">
                    @forelse($applicationsReceived as $app)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $app->status === 'accepted' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-teal-500/10 text-teal-400' }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        For Request: <strong>{{ $app->travelRequest?->pickup_location }} → {{ $app->travelRequest?->destination_location }}</strong>
                                    </span>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-teal-500/20 text-teal-400 font-black text-sm flex items-center justify-center">
                                        {{ strtoupper(substr($app->driver?->name ?? 'D', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                            <span>{{ $app->driver?->name }}</span>
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-teal-500/10 text-teal-400">Verified Driver</span>
                                        </h4>
                                        <p class="text-xs text-slate-400">
                                            Vehicle: {{ $app->vehicle?->model ?? 'Assigned Vehicle' }} ({{ $app->available_seats ?? 'N/A' }} seats)
                                        </p>
                                    </div>
                                </div>

                                @if($app->message)
                                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 text-xs text-slate-600 dark:text-slate-300 italic border border-slate-100 dark:border-slate-800">
                                        "{{ $app->message }}"
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Driver's Quote</span>
                                    <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($app->offered_fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('messages.start') }}?driver_application_id={{ $app->id }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs flex items-center gap-1.5 border border-slate-700">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>Message Driver</span>
                                    </a>
                                    @if($app->travelRequest && !$app->travelRequest->selected_application_id && $app->status === 'pending')
                                        <form action="{{ route('activity.requests.select-application', [$app->travelRequest->id, $app->id]) }}" method="POST" onsubmit="return confirm('Accept this driver offer? Other offers will be closed.');">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md shadow-emerald-600/20">
                                                Accept Driver
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-hand-holding-dollar text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No driver offers received yet</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When verified drivers submit ride proposals for your travel requests, they will appear here for comparison.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PASSENGER PANEL 5: Confirmed Arrangements --}}
            @if($activeTab === 'confirmed-arrangements')
                <div class="space-y-4">
                    @forelse($confirmedArrangements as $conv)
                        @php
                            $details = $conv->arrangement_details ?? [];
                            $driverUser = $conv->otherUser($user);
                        @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-emerald-500/40 p-6 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-600 text-white">
                                    <i class="fa-solid fa-lock mr-1"></i> Confirmed & Locked Arrangement
                                </span>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                    {{ $details['route'] ?? $conv->title }}
                                </h3>
                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-id-badge text-teal-500 mr-1"></i> Driver: <strong>{{ $driverUser?->name }}</strong></span>
                                    <span><i class="fa-regular fa-calendar text-emerald-500 mr-1"></i> Date: <strong>{{ $details['date'] ?? 'Agreed Date' }}</strong></span>
                                    <span><i class="fa-solid fa-chair text-indigo-500 mr-1"></i> Seats: <strong>{{ $details['seats'] ?? 1 }}</strong></span>
                                    @if($conv->confirmed_booking_id)
                                        <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">Booking Ref: #{{ $conv->confirmedBooking?->booking_reference ?? $conv->confirmed_booking_id }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Locked Fare</span>
                                    <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($details['fare'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('messages.show', $conv->id) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>Open Chat</span>
                                    </a>
                                    <form action="{{ route('messages.cancel', $conv->id) }}" method="POST" onsubmit="return confirm('Cancel this confirmed arrangement?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold text-xs hover:bg-rose-100">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-handshake text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No confirmed arrangements</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When you and a driver agree on travel terms in direct chat, confirm the arrangement to lock your seats.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- PASSENGER PANEL 6: Completed Trips --}}
            @if($activeTab === 'completed-trips')
                <div class="space-y-4">
                    @php $hasCompleted = false; @endphp

                    {{-- Completed Bookings --}}
                    @foreach($completedBookings as $b)
                        @php $hasCompleted = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Completed Booking
                                    </span>
                                    <span class="font-mono text-xs text-slate-400">#{{ $b->booking_reference }}</span>
                                    <span class="text-xs text-slate-400">{{ $b->start_datetime ? $b->start_datetime->format('d M Y') : ($b->trip?->trip_date ? \Carbon\Carbon::parse($b->trip->trip_date)->format('d M Y') : 'Finished') }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>{{ $b->fromStop?->name ?? $b->pickup_location ?? 'Origin' }}</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-emerald-500"></i>
                                    <span>{{ $b->toStop?->name ?? $b->destination_location ?? 'Destination' }}</span>
                                </h3>
                                <p class="text-xs text-slate-400">
                                    Vehicle: {{ $b->vehicle?->model ?? $b->trip?->vehicle?->model ?? 'Commercial Fleet' }} · Driver: {{ $b->vehicle?->driver?->name ?? $b->trip?->driver?->name ?? 'Verified Partner' }}
                                </p>
                            </div>
                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <span class="font-mono text-lg font-black text-slate-900 dark:text-white">Rs. {{ number_format($b->total_fare, 2) }}</span>
                                <a href="{{ route('passenger.bookings.show', $b->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                                    View Receipt
                                </a>
                            </div>
                        </div>
                    @endforeach

                    {{-- Completed Requests --}}
                    @foreach($completedRequests as $req)
                        @php $hasCompleted = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-500/10 text-teal-400 border border-teal-500/20">
                                    <i class="fa-solid fa-flag-checkered mr-1"></i> Completed Custom Request
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $req->pickup_location }} → {{ $req->destination_location }}
                                </h3>
                                <p class="text-xs text-slate-400">Date: {{ $req->travel_date->format('d M Y') }} · Driver: {{ $req->selectedApplication?->driver?->name ?? 'Assigned Driver' }}</p>
                            </div>
                            <span class="font-mono text-base font-black text-emerald-600 dark:text-emerald-400">
                                Rs. {{ number_format($req->selectedApplication?->offered_fare ?? $req->budget ?? 0, 2) }}
                            </span>
                        </div>
                    @endforeach

                    @if(!$hasCompleted)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-circle-check text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No completed trips yet</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When your journeys conclude successfully, they are permanently archived here for your records.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- PASSENGER PANEL 7: Cancelled & Expired Records --}}
            @if($activeTab === 'cancelled-expired')
                <div class="space-y-4">
                    @php $hasCancelled = false; @endphp

                    {{-- Cancelled Bookings --}}
                    @foreach($cancelledBookings as $b)
                        @php $hasCancelled = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 opacity-75">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-500">
                                        <i class="fa-solid fa-ban mr-1"></i> Cancelled Booking
                                    </span>
                                    <span class="font-mono text-xs text-slate-400">#{{ $b->booking_reference }}</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $b->fromStop?->name ?? $b->pickup_location ?? 'Origin' }} → {{ $b->toStop?->name ?? $b->destination_location ?? 'Destination' }}
                                </h3>
                                <p class="text-xs text-slate-400">Fare: Rs. {{ number_format($b->total_fare, 2) }} · Recorded in database history</p>
                            </div>
                            <span class="text-xs font-mono text-slate-400">Cancelled</span>
                        </div>
                    @endforeach

                    {{-- Cancelled / Expired Requests --}}
                    @foreach($cancelledRequests as $req)
                        @php $hasCancelled = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 opacity-75">
                            <div class="space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-800 text-slate-400">
                                    {{ $req->isExpired() ? 'Expired Request' : 'Cancelled Request' }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $req->pickup_location }} → {{ $req->destination_location }}
                                </h3>
                                <p class="text-xs text-slate-400">Date: {{ $req->travel_date->format('d M Y') }}</p>
                            </div>
                            <span class="text-xs font-mono text-slate-400">Archived</span>
                        </div>
                    @endforeach

                    @if(!$hasCancelled)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-circle-info text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No cancelled or expired records</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">All of your bookings and travel requests are currently active or completed.</p>
                        </div>
                    @endif
                </div>
            @endif

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- DRIVER PANELS --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @else

            {{-- DRIVER PANEL 1: My Shared Ride Listings --}}
            @if($activeTab === 'shared-rides')
                <div class="space-y-4">
                    @forelse($sharedRideListings as $trip)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-500/10 text-teal-400 border border-teal-500/20">
                                        {{ ucfirst($trip->status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        <i class="fa-regular fa-calendar text-[11px] mr-1"></i>
                                        {{ \Carbon\Carbon::parse($trip->trip_date)->format('D, d M Y') }} ({{ $trip->departure_time ?: 'Scheduled Time' }})
                                    </span>
                                </div>

                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>{{ $trip->route?->start_location ?? 'Origin' }}</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-teal-500"></i>
                                    <span>{{ $trip->route?->end_location ?? 'Destination' }}</span>
                                </h3>

                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-van-shuttle text-teal-500 mr-1"></i> {{ $trip->vehicle?->model ?? 'Vehicle' }} ({{ $trip->vehicle?->registration_no ?? 'N/A' }})</span>
                                    <span><i class="fa-solid fa-chair text-emerald-500 mr-1"></i> Available: <strong>{{ $trip->available_seats }} / {{ $trip->vehicle?->total_seats ?? 'N/A' }}</strong> Seats</span>
                                    <span><i class="fa-solid fa-ticket text-indigo-500 mr-1"></i> Bookings: <strong>{{ $trip->bookings->count() }}</strong></span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Base Fare</span>
                                    <span class="font-mono text-xl font-black text-teal-600 dark:text-teal-400">Rs. {{ number_format($trip->fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('driver.trips.show', $trip->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                                        View Trip
                                    </a>
                                    <a href="{{ route('driver.bookings') }}?trip_id={{ $trip->id }}" class="px-3.5 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs">
                                        Manage Bookings
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-route text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No active shared ride listings</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Schedule commercial trips between regional stops to accept passenger seat reservations.</p>
                            <a href="{{ route('driver.trips.create') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-teal-500 text-slate-950 font-bold text-xs">
                                <span>Schedule a Trip</span>
                            </a>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 2: My Private Vehicle Listings --}}
            @if($activeTab === 'my-vehicles')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($myVehicles as $vehicle)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm flex flex-col justify-between transition-colors">
                            <div>
                                @if($vehicle->image)
                                    <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->model }}" class="w-full h-44 object-cover">
                                @else
                                    <div class="w-full h-44 bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-van-shuttle text-4xl"></i>
                                    </div>
                                @endif

                                <div class="p-6 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $vehicle->isApproved() ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                            {{ $vehicle->status }}
                                        </span>
                                        <span class="text-xs font-mono font-bold text-slate-400">{{ $vehicle->registration_no }}</span>
                                    </div>

                                    <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $vehicle->model }}</h3>

                                    <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                        <p><i class="fa-solid fa-car text-teal-500 mr-1.5 w-4"></i> Type: {{ $vehicle->type }}</p>
                                        <p><i class="fa-solid fa-chair text-emerald-500 mr-1.5 w-4"></i> Capacity: {{ $vehicle->total_seats }} Seats</p>
                                        <p><i class="fa-solid fa-coins text-amber-500 mr-1.5 w-4"></i> Rental: <strong>Rs. {{ number_format($vehicle->rental_rate_per_day, 2) }}</strong> / day</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                                <a href="{{ route('vehicles.show', $vehicle->id) }}" class="text-xs font-bold text-teal-600 dark:text-teal-400 hover:underline">
                                    Public Listing
                                </a>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('driver.vehicles.edit', $vehicle->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-300">
                                        Edit
                                    </a>
                                    <a href="{{ route('driver.vehicles.unavailability.index', $vehicle->id) }}" class="px-3 py-1.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-bold">
                                        Calendar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-van-shuttle text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No registered vehicles</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Register commercial vehicles to offer private rentals or schedule shared rides.</p>
                            <a href="{{ route('driver.vehicles.create') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-teal-500 text-slate-950 font-bold text-xs">
                                <span>Register Vehicle</span>
                            </a>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 3: My Bookings (Received Bookings) --}}
            @if($activeTab === 'my-bookings')
                <div class="space-y-4">
                    {{-- Filter tabs: All, Shared, Private --}}
                    <div class="flex items-center gap-2 text-xs">
                        <a href="{{ route('activity.index', ['tab' => 'my-bookings']) }}" class="px-3 py-1.5 rounded-lg {{ !request('booking_type_filter') ? 'bg-teal-500 text-slate-950 font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold' }}">
                            All Bookings
                        </a>
                        <a href="{{ route('activity.index', ['tab' => 'my-bookings', 'booking_type_filter' => 'shared']) }}" class="px-3 py-1.5 rounded-lg {{ request('booking_type_filter') === 'shared' ? 'bg-teal-500 text-slate-950 font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold' }}">
                            Shared Seat Bookings
                        </a>
                        <a href="{{ route('activity.index', ['tab' => 'my-bookings', 'booking_type_filter' => 'private']) }}" class="px-3 py-1.5 rounded-lg {{ request('booking_type_filter') === 'private' ? 'bg-teal-500 text-slate-950 font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold' }}">
                            Private Rentals
                        </a>
                    </div>

                    @forelse($myBookings as $b)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="font-mono text-xs font-black text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-3 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                                        #{{ $b->booking_reference }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $b->isConfirmed() ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                        {{ $b->isPrivate() ? 'Private Rental' : 'Shared Seat' }} · {{ ucfirst($b->booking_status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        {{ $b->start_datetime ? $b->start_datetime->format('d M Y') : ($b->trip?->trip_date ? \Carbon\Carbon::parse($b->trip->trip_date)->format('d M Y') : 'Scheduled') }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>{{ $b->fromStop?->name ?? $b->pickup_location ?? 'Origin' }}</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-teal-500"></i>
                                    <span>{{ $b->toStop?->name ?? $b->destination_location ?? 'Destination' }}</span>
                                </h3>

                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-user text-teal-500 mr-1"></i> Passenger: <strong>{{ $b->user?->name ?? 'Guest User' }}</strong></span>
                                    <span><i class="fa-solid fa-chair text-emerald-500 mr-1"></i> {{ $b->seats }} Seat(s)</span>
                                    <span><i class="fa-solid fa-van-shuttle text-indigo-500 mr-1"></i> {{ $b->vehicle?->model ?? $b->trip?->vehicle?->model ?? 'Vehicle' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Received</span>
                                    <span class="font-mono text-xl font-black text-slate-900 dark:text-white">Rs. {{ number_format($b->total_fare, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('driver.bookings.show', $b->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                                        View Details
                                    </a>
                                    @if($b->user_id)
                                        <a href="{{ route('messages.start') }}?target_driver_id={{ $b->user_id }}" class="px-3.5 py-2 rounded-xl bg-teal-500/10 text-teal-400 font-bold text-xs hover:bg-teal-500/20 flex items-center gap-1.5 border border-teal-500/20">
                                            <i class="fa-solid fa-comments"></i>
                                            <span>Message Passenger</span>
                                        </a>
                                    @endif
                                    @if(!$b->isCancelled() && !$b->isCompleted())
                                        <form action="{{ route('activity.bookings.cancel', $b->id) }}" method="POST" onsubmit="return confirm('Cancel this customer booking?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold text-xs hover:bg-rose-100">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-receipt text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No active bookings received</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Bookings made by passengers for your scheduled trips and private fleet will appear here.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 4: Passenger Requests --}}
            @if($activeTab === 'passenger-requests')
                <div class="space-y-4">
                    {{-- Filters Bar --}}
                    <form method="GET" action="{{ route('activity.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                        <input type="hidden" name="tab" value="passenger-requests">
                        <div>
                            <input type="text" name="filter_route" placeholder="Filter by route/city..." value="{{ request('filter_route') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-medium">
                        </div>
                        <div>
                            <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-medium">
                        </div>
                        <div>
                            <select name="filter_vehicle" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-medium">
                                <option value="">All Vehicles</option>
                                <option value="Car" {{ request('filter_vehicle') === 'Car' ? 'selected' : '' }}>Car</option>
                                <option value="Van" {{ request('filter_vehicle') === 'Van' ? 'selected' : '' }}>Van</option>
                                <option value="Bus" {{ request('filter_vehicle') === 'Bus' ? 'selected' : '' }}>Bus</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold">Filter</button>
                            @if(request('filter_route') || request('filter_date') || request('filter_vehicle'))
                                <a href="{{ route('activity.index', ['tab' => 'passenger-requests']) }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold">Reset</a>
                            @endif
                        </div>
                    </form>

                    @forelse($passengerRequests as $req)
                        @php
                            $myApp = $req->applications->first();
                        @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-500">
                                            Open Request
                                        </span>
                                        <span class="text-xs text-slate-400">
                                            Passenger: <strong>{{ $req->user?->name }}</strong>
                                        </span>
                                        <span class="text-xs text-slate-400">
                                            Date: <strong>{{ $req->travel_date->format('d M Y') }}</strong> ({{ $req->travel_time ?: 'Flexible' }})
                                        </span>
                                        <span class="text-xs text-slate-400">
                                            Seats: <strong>{{ $req->seats_needed }}</strong>
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                        <span>{{ $req->pickup_location }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs text-teal-500"></i>
                                        <span>{{ $req->destination_location }}</span>
                                    </h3>
                                </div>

                                <div class="flex items-center gap-2 self-start sm:self-center flex-wrap">
                                    @if($myApp)
                                        <span class="px-3.5 py-2 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 font-bold text-xs inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Offered: Rs. {{ number_format($myApp->offered_fare, 2) }}</span>
                                        </span>
                                        <a href="{{ route('messages.start') }}?travel_request_id={{ $req->id }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-teal-400 font-bold text-xs inline-flex items-center gap-1.5 border border-teal-500/30">
                                            <i class="fa-solid fa-comments"></i>
                                            <span>Message</span>
                                        </a>
                                    @else
                                        <button type="button"
                                                onclick="openApplyModal({{ $req->id }}, '{{ addslashes($req->pickup_location) }}', '{{ addslashes($req->destination_location) }}', '{{ $req->budget ?? 0 }}')"
                                                class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs shadow-md shadow-teal-500/20 flex items-center gap-1.5">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            <span>Offer Ride / Apply</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($req->notes)
                                <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                                    "{{ $req->notes }}"
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-users text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No open passenger requests</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">There are currently no open passenger requests matching your filters.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 5: My Submitted Applications --}}
            @if($activeTab === 'my-applications')
                <div class="space-y-4">
                    @forelse($myApplications as $app)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $app->status === 'accepted' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-teal-500/10 text-teal-400' }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        Passenger: <strong>{{ $app->travelRequest?->user?->name ?? 'Passenger' }}</strong>
                                    </span>
                                </div>

                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>{{ $app->travelRequest?->pickup_location ?? 'Origin' }}</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-teal-500"></i>
                                    <span>{{ $app->travelRequest?->destination_location ?? 'Destination' }}</span>
                                </h3>

                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-coins text-amber-500 mr-1"></i> Proposed Fare: <strong>Rs. {{ number_format($app->offered_fare, 2) }}</strong></span>
                                    <span><i class="fa-regular fa-clock text-teal-500 mr-1"></i> Time: {{ $app->proposed_time ?: 'Flexible' }}</span>
                                    <span><i class="fa-solid fa-van-shuttle text-indigo-500 mr-1"></i> Vehicle: {{ $app->vehicle?->model ?? 'Vehicle' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <a href="{{ route('messages.start') }}?driver_application_id={{ $app->id }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-comments"></i>
                                    <span>Chat with Passenger</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-paper-plane text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No applications submitted</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Explore open passenger requests to offer your commercial ride services.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 6: Confirmed Arrangements --}}
            @if($activeTab === 'confirmed-arrangements')
                <div class="space-y-4">
                    @forelse($confirmedArrangements as $conv)
                        @php
                            $details = $conv->arrangement_details ?? [];
                            $passengerUser = $conv->otherUser($user);
                        @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-teal-500/40 p-6 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-2">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-teal-500 text-slate-950">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Confirmed Agreement
                                </span>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                    {{ $details['route'] ?? $conv->title }}
                                </h3>
                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-4 flex-wrap">
                                    <span><i class="fa-solid fa-user text-emerald-500 mr-1"></i> Passenger: <strong>{{ $passengerUser?->name }}</strong></span>
                                    <span><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> Date: <strong>{{ $details['date'] ?? 'N/A' }}</strong></span>
                                    <span><i class="fa-solid fa-chair text-teal-500 mr-1"></i> Seats: <strong>{{ $details['seats'] ?? 1 }}</strong></span>
                                </div>
                            </div>

                            <div class="flex items-center md:flex-col md:items-end justify-between gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Agreed Fare</span>
                                    <span class="font-mono text-xl font-black text-teal-600 dark:text-teal-400">Rs. {{ number_format($details['fare'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('messages.show', $conv->id) }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs flex items-center gap-1.5">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>Open Chat</span>
                                    </a>
                                    <form action="{{ route('messages.complete', $conv->id) }}" method="POST" onsubmit="return confirm('Mark this arrangement as completed?');">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                                            Mark Completed
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-handshake text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No confirmed arrangements yet</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When you confirm travel arrangements in direct chat, they will show up here.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- DRIVER PANEL 7: Completed Trips --}}
            @if($activeTab === 'completed-trips')
                <div class="space-y-4">
                    @php $hasDriverCompleted = false; @endphp

                    {{-- Completed Shared Trips --}}
                    @foreach($driverCompletedTrips as $trip)
                        @php $hasDriverCompleted = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-500">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Completed Scheduled Trip
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $trip->route?->name ?? 'Commercial Route' }}
                                </h3>
                                <p class="text-xs text-slate-400">Date: {{ \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') }} · Vehicle: {{ $trip->vehicle?->model }} · {{ $trip->bookings->count() }} Booking(s)</p>
                            </div>
                            <span class="font-mono text-lg font-black text-emerald-600 dark:text-emerald-400">
                                Rs. {{ number_format($trip->fare, 2) }} / seat
                            </span>
                        </div>
                    @endforeach

                    {{-- Completed Bookings --}}
                    @foreach($driverCompletedBookings as $b)
                        @php $hasDriverCompleted = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors">
                            <div class="space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-500/10 text-teal-400">
                                    <i class="fa-solid fa-check-double mr-1"></i> Completed Booking #{{ $b->booking_reference }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $b->fromStop?->name ?? $b->pickup_location ?? 'Origin' }} → {{ $b->toStop?->name ?? $b->destination_location ?? 'Destination' }}
                                </h3>
                                <p class="text-xs text-slate-400">Passenger: {{ $b->user?->name }} · {{ $b->seats }} Seat(s)</p>
                            </div>
                            <span class="font-mono text-lg font-black text-emerald-600 dark:text-emerald-400">
                                Rs. {{ number_format($b->total_fare, 2) }}
                            </span>
                        </div>
                    @endforeach

                    @if(!$hasDriverCompleted)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-circle-check text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No completed trips yet</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When your scheduled trips or confirmed bookings are completed, they will appear here.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- DRIVER PANEL 8: Cancelled & Expired Records --}}
            @if($activeTab === 'cancelled-expired')
                <div class="space-y-4">
                    @php $hasDriverCancelled = false; @endphp

                    @foreach($driverCancelledTrips as $trip)
                        @php $hasDriverCancelled = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 opacity-75">
                            <div class="space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-500">
                                    Cancelled Trip
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $trip->route?->name ?? 'Trip' }}
                                </h3>
                                <p class="text-xs text-slate-400">Date: {{ \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') }}</p>
                            </div>
                            <span class="text-xs font-mono text-slate-400">Archived</span>
                        </div>
                    @endforeach

                    @foreach($driverCancelledBookings as $b)
                        @php $hasDriverCancelled = true; @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 opacity-75">
                            <div class="space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-500">
                                    Cancelled Booking #{{ $b->booking_reference }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $b->fromStop?->name ?? $b->pickup_location ?? 'Origin' }} → {{ $b->toStop?->name ?? $b->destination_location ?? 'Destination' }}
                                </h3>
                                <p class="text-xs text-slate-400">Passenger: {{ $b->user?->name }} · Rs. {{ number_format($b->total_fare, 2) }}</p>
                            </div>
                            <span class="text-xs font-mono text-slate-400">Cancelled</span>
                        </div>
                    @endforeach

                    @if(!$hasDriverCancelled)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                            <i class="fa-solid fa-circle-info text-3xl text-slate-400"></i>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">No cancelled or expired records</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">All of your commercial listings and bookings are active or completed.</p>
                        </div>
                    @endif
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Preferred Vehicle Type</label>
                    <select name="preferred_vehicle_type" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                        <option value="">Any Vehicle Type</option>
                        <option value="Car">Car / Sedan</option>
                        <option value="Van">Van / HiAce</option>
                        <option value="Daba">Daba / Carry</option>
                        <option value="Bus">Coaster / Bus</option>
                        <option value="Other">Other / 4x4 Jeep</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Estimated Budget (PKR, Optional)</label>
                    <input type="number" name="budget" placeholder="e.g. 1500" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Intermediate Stops (Optional, comma-separated)</label>
                <input type="text" name="intermediate_stops" placeholder="e.g. Nagar, Aliabad, Passu" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Additional Instructions / Notes</label>
                <textarea name="notes" rows="2" placeholder="Any specific requirements, stops along the way, or contact notes..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeRequestModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Cancel</button>
                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black shadow-md">Post Request</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal 2: Edit Travel Request ─── --}}
<div id="editRequestModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-emerald-600"></i>
                <span>Edit Travel Request</span>
            </h3>
            <button type="button" onclick="closeEditRequestModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" id="editRequestForm" action="" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pickup Location *</label>
                    <input type="text" name="pickup_location" id="editPickupLocation" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Destination *</label>
                    <input type="text" name="destination_location" id="editDestinationLocation" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Travel Date *</label>
                    <input type="date" name="travel_date" id="editTravelDate" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Time</label>
                    <input type="time" name="travel_time" id="editTravelTime" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Seats Needed *</label>
                    <input type="number" name="seats_needed" id="editSeatsNeeded" min="1" max="50" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Preferred Vehicle Type</label>
                    <select name="preferred_vehicle_type" id="editVehicleType" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                        <option value="">Any Vehicle Type</option>
                        <option value="Car">Car / Sedan</option>
                        <option value="Van">Van / HiAce</option>
                        <option value="Daba">Daba / Carry</option>
                        <option value="Bus">Coaster / Bus</option>
                        <option value="Other">Other / 4x4 Jeep</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Estimated Budget (PKR)</label>
                    <input type="number" name="budget" id="editBudget" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Intermediate Stops (comma-separated)</label>
                <input type="text" name="intermediate_stops" id="editIntermediateStops" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Notes</label>
                <textarea name="notes" id="editNotes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditRequestModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Cancel</button>
                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black shadow-md">Update Request</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal 3: Driver Apply / Offer Ride ─── --}}
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
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Available Seats (Optional)</label>
                    <input type="number" name="available_seats" min="1" max="50" placeholder="e.g. 4" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Select Approved Vehicle</label>
                <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                    <option value="">Select a Vehicle (Optional)</option>
                    @if($isDriver)
                        @foreach($myVehicles as $veh)
                            @if($veh->isApproved())
                                <option value="{{ $veh->id }}">{{ $veh->model }} ({{ $veh->registration_no }}) · {{ $veh->type }} ({{ $veh->total_seats }} seats)</option>
                            @endif
                        @endforeach
                    @endif
                </select>
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

function openEditRequestModal(req) {
    const modal = document.getElementById('editRequestModal');
    const form = document.getElementById('editRequestForm');
    if (!modal || !form || !req) return;

    form.action = `/my-activity/requests/${req.id}`;
    document.getElementById('editPickupLocation').value = req.pickup_location || '';
    document.getElementById('editDestinationLocation').value = req.destination_location || '';
    document.getElementById('editTravelDate').value = req.travel_date ? req.travel_date.substring(0, 10) : '';
    document.getElementById('editTravelTime').value = req.travel_time || '';
    document.getElementById('editSeatsNeeded').value = req.seats_needed || 1;
    document.getElementById('editVehicleType').value = req.preferred_vehicle_type || '';
    document.getElementById('editBudget').value = req.budget ? Math.round(req.budget) : '';
    
    let stops = '';
    if (Array.isArray(req.intermediate_stops)) {
        stops = req.intermediate_stops.join(', ');
    } else if (typeof req.intermediate_stops === 'string') {
        stops = req.intermediate_stops;
    }
    document.getElementById('editIntermediateStops').value = stops;
    document.getElementById('editNotes').value = req.notes || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditRequestModal() {
    const modal = document.getElementById('editRequestModal');
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
