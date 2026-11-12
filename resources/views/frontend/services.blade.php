@extends('frontend.layouts.app')

@section('title', 'Our Services — RouteConnect')

@section('content')
<div class="min-h-screen bg-[#F5F8FF] dark:bg-[#070e1b] transition-colors duration-200">

    <!-- ================================================================= -->
    <!-- A. HERO SECTION                                                   -->
    <!-- ================================================================= -->
    <section class="relative bg-[#102347] text-white overflow-hidden py-20 lg:py-28">
        <!-- Professional Travel-Related Background Image with Gradient Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero.jpg') }}" alt="RouteConnect Highway Transport" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-r from-[#102347] via-[#102347]/90 to-[#075BEA]/60"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left">
            <div class="max-w-3xl space-y-5">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#075BEA]/20 border border-[#075BEA]/40 text-blue-200 text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                    <i class="fa-solid fa-shield-halved text-[#10B981]"></i> Verified Inter-City Transit
                </div>
                
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    Our Services
                </h1>
                
                <p class="text-base sm:text-lg text-slate-200 leading-relaxed font-medium">
                    Smart, flexible and reliable transportation solutions for every journey.
                </p>

                <div class="flex flex-wrap items-center gap-4 pt-3 justify-center sm:justify-start">
                    <a href="{{ route('trips.index') }}"
                       class="px-7 py-3.5 rounded-full bg-[#075BEA] hover:bg-blue-600 text-white font-extrabold text-xs shadow-lg shadow-[#075BEA]/30 flex items-center gap-2.5 transition-all hover:-translate-y-0.5">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Find a Ride</span>
                    </a>
                    <a href="{{ route('register', ['role' => 'driver']) }}"
                       class="px-7 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs border border-white/20 backdrop-blur-xs flex items-center gap-2.5 transition-all">
                        <i class="fa-solid fa-id-card"></i>
                        <span>Become a Driver</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- B. MAIN SERVICES (4 DISTINCT CARDS)                               -->
    <!-- ================================================================= -->
    <section class="py-16 lg:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
            <span class="text-xs font-black uppercase tracking-wider text-[#075BEA] dark:text-blue-400">Tailored Transit Modes</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-[#102347] dark:text-white">Everything You Need To Travel Smarter</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Whether you want to share a seat, charter an entire vehicle, or post a custom itinerary, RouteConnect has you covered.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">

            <!-- Service Card 1: Shared Ride Booking -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-7 shadow-sm hover:shadow-xl border border-slate-200/80 dark:border-slate-800 transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-[#075BEA] dark:text-blue-400 flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chair"></i>
                    </div>
                    <div class="space-y-2">
                        <div class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-[#10B981] text-[10px] font-black uppercase">
                            Cost-Effective
                        </div>
                        <h3 class="text-lg font-extrabold text-[#102347] dark:text-white">Shared Ride Booking</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Passengers can search scheduled trips and reserve individual seats in vehicles travelling along their selected route.
                    </p>
                </div>
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 mt-6">
                    <a href="{{ route('trips.index', ['tab' => 'shared']) }}"
                       class="inline-flex items-center gap-2 text-xs font-bold text-[#075BEA] dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        <span>Search Shared Rides</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Service Card 2: Private Vehicle Booking -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-7 shadow-sm hover:shadow-xl border border-slate-200/80 dark:border-slate-800 transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    <div class="space-y-2">
                        <div class="inline-block px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-[#075BEA] text-[10px] font-black uppercase">
                            Private & Groups
                        </div>
                        <h3 class="text-lg font-extrabold text-[#102347] dark:text-white">Private Vehicle Booking</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Passengers can request an entire vehicle, such as a car or van, for private transportation.
                    </p>
                </div>
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 mt-6">
                    <a href="{{ route('trips.index', ['tab' => 'vehicles']) }}"
                       class="inline-flex items-center gap-2 text-xs font-bold text-[#075BEA] dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        <span>Browse Available Vehicles</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Service Card 3: Travel Requests -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-7 shadow-sm hover:shadow-xl border border-slate-200/80 dark:border-slate-800 transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div class="space-y-2">
                        <div class="inline-block px-2.5 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/60 text-purple-600 text-[10px] font-black uppercase">
                            Custom Itinerary
                        </div>
                        <h3 class="text-lg font-extrabold text-[#102347] dark:text-white">Travel Requests</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Passengers can post their travel requirements when they cannot find a suitable ride. Available drivers can review requests and submit offers.
                    </p>
                </div>
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 mt-6">
                    <a href="{{ route('trips.index', ['tab' => 'requests']) }}"
                       class="inline-flex items-center gap-2 text-xs font-bold text-[#075BEA] dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        <span>Explore Travel Requests</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Service Card 4: Scheduled Trips -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-7 shadow-sm hover:shadow-xl border border-slate-200/80 dark:border-slate-800 transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-[#10B981] flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="space-y-2">
                        <div class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-[#10B981] text-[10px] font-black uppercase">
                            For Drivers
                        </div>
                        <h3 class="text-lg font-extrabold text-[#102347] dark:text-white">Scheduled Trips</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Approved drivers can publish trips with departure times, destinations, intermediate stops, and available seats.
                    </p>
                </div>
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 mt-6">
                    @auth
                        @if(auth()->user()->user_type == 2)
                            <a href="{{ route('driver.trips.create') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#10B981] hover:text-emerald-700 transition-colors">
                                <span>Schedule a Trip Now</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        @else
                            <a href="{{ route('driver.register') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#075BEA] dark:text-blue-400 hover:text-blue-700 transition-colors">
                                <span>Apply as Driver</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('driver.register') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#075BEA] dark:text-blue-400 hover:text-blue-700 transition-colors">
                            <span>Register as Driver</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @endauth
                </div>
            </div>

        </div>

    </section>

    <!-- ================================================================= -->
    <!-- C. HOW IT WORKS                                                   -->
    <!-- ================================================================= -->
    <section class="py-16 lg:py-24 bg-white dark:bg-slate-900/60 border-y border-slate-200/80 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
                <span class="text-xs font-black uppercase tracking-wider text-[#10B981]">Seamless Process</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-[#102347] dark:text-white">How RouteConnect Works</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Designed to be straightforward, safe, and transparent from start to destination.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                
                <!-- Step 1 -->
                <div class="p-6 rounded-2xl bg-[#F5F8FF] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-3 relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#075BEA] text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                            01
                        </div>
                        <i class="fa-solid fa-user-plus text-slate-400 dark:text-slate-500 text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-[#102347] dark:text-white">Register or Log In</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Create an account as a passenger or register as a verified driver with vehicle documents.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-2xl bg-[#F5F8FF] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-3 relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-teal-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                            02
                        </div>
                        <i class="fa-solid fa-route text-slate-400 dark:text-slate-500 text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-[#102347] dark:text-white">Find a Ride or Offer a Vehicle</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Search upcoming scheduled trips by origin and destination, charter a private vehicle, or publish your available seats.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-2xl bg-[#F5F8FF] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-3 relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#10B981] text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                            03
                        </div>
                        <i class="fa-solid fa-sliders text-slate-400 dark:text-slate-500 text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-[#102347] dark:text-white">Select a Suitable Travel Option</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Review intermediate pickup stops, expected arrival times, seat pricing, and driver ratings.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="p-6 rounded-2xl bg-[#F5F8FF] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-3 relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#102347] dark:bg-slate-700 text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                            04
                        </div>
                        <i class="fa-solid fa-circle-check text-slate-400 dark:text-slate-500 text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-[#102347] dark:text-white">Confirm Booking & Travel</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        Secure your seats atomically, coordinate details with your driver via in-page chat, and enjoy a safe journey.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ================================================================= -->
    <!-- D. CALL-TO-ACTION SECTION                                         -->
    <!-- ================================================================= -->
    <section class="py-16 lg:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-gradient-to-r from-[#102347] via-[#075BEA] to-[#102347] text-white p-8 sm:p-14 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                    <i class="fa-solid fa-compass"></i> Ready to Travel?
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                    Experience Smarter, Safer, and Connected Regional Travel.
                </h2>
                <p class="text-sm sm:text-base text-blue-100 leading-relaxed font-medium">
                    Join thousands of passengers and verified drivers across Gilgit-Baltistan and regional corridors. Reserve your seat or register your vehicle today.
                </p>
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('trips.index') }}"
                       class="px-8 py-3.5 rounded-full bg-white text-[#102347] hover:bg-slate-100 font-extrabold text-xs shadow-lg transition-all hover:scale-105">
                        <i class="fa-solid fa-magnifying-glass mr-1.5 text-[#075BEA]"></i> Find a Ride
                    </a>
                    <a href="{{ route('register', ['role' => 'driver']) }}"
                       class="px-8 py-3.5 rounded-full bg-[#10B981] hover:bg-emerald-600 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all hover:scale-105">
                        <i class="fa-solid fa-car mr-1.5"></i> Become a Driver
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

