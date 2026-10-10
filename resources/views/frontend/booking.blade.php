@extends('frontend.layouts.app')

@section('title', 'Passenger Booking Portal — RouteConnect')

@section('content')

{{-- ─── Hero Header ─── --}}
<section class="py-12 lg:py-16 bg-slate-950 text-white relative overflow-hidden border-b border-slate-800">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(13,148,136,0.2),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-teal-400 bg-teal-950/80 px-3 py-1 rounded-full border border-teal-800/80 mb-3">
            <i class="fa-solid fa-ticket text-teal-400"></i> Passenger Services
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Passenger Booking Portal</h1>
        <p class="text-slate-300 text-sm mt-1 max-w-xl">
            Find scheduled transport departures, check stop timings, reserve guaranteed seats, and access your digital passes.
        </p>
    </div>
</section>

{{-- ─── Portal Main Cards ─── --}}
<section class="py-12 bg-slate-50 dark:bg-[#070D18] min-h-[500px] transition-colors">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Card 1: Find & Reserve Seats --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-xl border border-teal-100 dark:border-teal-800">
                        <i class="fa-solid fa-bus"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Find Scheduled Departures</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Search vehicles operating along your corridor. View live seat availability, expected stop arrival times, and reserve seats in seconds.
                        </p>
                    </div>

                    <div class="space-y-2 pt-2 text-xs text-slate-600 dark:text-slate-300">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-teal-600 dark:text-teal-400 text-[11px]"></i>
                            <span>Verified commercial vehicles & licensed drivers</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-teal-600 dark:text-teal-400 text-[11px]"></i>
                            <span>Guaranteed seat reservations</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-teal-600 dark:text-teal-400 text-[11px]"></i>
                            <span>Expected stop arrival timetables</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ url('/trips') }}" class="w-full py-3 px-5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs shadow-sm shadow-teal-600/20 flex items-center justify-center gap-2 transition-all">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search Trips & Book Now</span>
                    </a>
                </div>
            </div>

            {{-- Card 2: Existing Passes / Manage Bookings --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center font-bold text-xl border border-slate-200 dark:border-slate-700">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">My Bookings & Seat Passes</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Already booked a seat? Sign in to view your digital boarding pass, check pickup points, or manage cancellations.
                        </p>
                    </div>

                    @auth
                        @if(auth()->user()->user_type == 0)
                            <div class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-100 dark:border-teal-800 text-teal-900 dark:text-teal-200 text-xs font-semibold">
                                You are signed in as <strong>{{ auth()->user()->name }}</strong>. You can view all your confirmed reservations directly.
                            </div>
                        @else
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                You are signed in as <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->user_type == 1 ? 'Administrator' : 'Driver' }}).
                            </div>
                        @endif
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs">
                            <span class="font-bold text-slate-800 dark:text-white block mb-0.5">Passenger Authentication</span>
                            Please log in with your registered email and password to access your personalized travel passes.
                        </div>
                    @endauth
                </div>

                <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800">
                    @auth
                        @if(auth()->user()->user_type == 0)
                            <a href="{{ route('passenger.bookings.index') }}" class="w-full py-3 px-5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-extrabold text-xs shadow-sm flex items-center justify-center gap-2 transition-all">
                                <i class="fa-solid fa-ticket"></i>
                                <span>Go to My Bookings</span>
                            </a>
                        @elseif(auth()->user()->user_type == 1)
                            <a href="{{ route('admin.bookings.index') }}" class="w-full py-3 px-5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-extrabold text-xs shadow-sm flex items-center justify-center gap-2 transition-all">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Open Admin Bookings Panel</span>
                            </a>
                        @else
                            <a href="{{ route('driver.bookings') }}" class="w-full py-3 px-5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-extrabold text-xs shadow-sm flex items-center justify-center gap-2 transition-all">
                                <i class="fa-solid fa-gauge"></i>
                                <span>Open Driver Bookings</span>
                            </a>
                        @endif
                    @else
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="flex-1 py-3 px-4 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-extrabold text-xs text-center transition-all">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="flex-1 py-3 px-4 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-extrabold text-xs text-center transition-all">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

        </div>

    </div>
</section>

@endsection
