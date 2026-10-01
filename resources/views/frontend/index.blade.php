@extends('frontend.layouts.app')

@section('title', 'RouteConnect — Smart Transport Scheduling & Booking System')

@section('content')

<!-- ========================================== -->
<!-- 1. HERO SECTION & PROMINENT TRIP SEARCH    -->
<!-- ========================================== -->
<section class="relative bg-slate-900 text-white overflow-hidden py-12 lg:py-20">
    <!-- Hero Background Image Overlay -->
    <div class="absolute inset-0 z-0 opacity-30 mix-blend-overlay">
        <img src="/images/hero.jpg" alt="RouteConnect Highway Transport" class="w-full h-full object-cover">
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/95 to-slate-900/80"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Text Content -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Small Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-bus text-emerald-400"></i> SMART TRANSPORT SCHEDULING
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Travel with confidence. <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-indigo-300 bg-clip-text text-transparent">
                        Know your route before you leave.
                    </span>
                </h1>

                <!-- Supporting Text -->
                <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                    Find scheduled vehicles, check expected stop timings, see available seats, and book your journey in advance.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#searchSection" class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition-all hover:-translate-y-0.5">
                        <i class="fa-solid fa-magnifying-glass"></i> Search Trips
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm border border-white/20 flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-user-plus"></i> Register Account
                    </a>
                </div>
            </div>

            <!-- Right Visual Graphic Showcase -->
            <div class="lg:col-span-5 relative">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-white/10 group">
                    <img src="/images/hero.jpg" alt="Transport Bus on Mountain Route" class="w-full h-72 sm:h-80 lg:h-96 object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                    
                    <!-- Floating Info Card 1 -->
                    <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-md rounded-2xl p-4 text-slate-900 border border-white/50 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-bus text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-extrabold text-slate-900">Gilgit ➔ Hunza Express</div>
                                    <div class="text-[10px] text-slate-500 font-semibold">Scheduled Stop: Nagar (09:00 AM)</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">12 Seats Free</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- SEARCH SECTION CARD -->
        <div id="searchSection" class="mt-12 bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 text-slate-900">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-emerald-600"></i> Find Your Trip
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Search scheduled vehicles passing through your start and destination stops.</p>
                </div>
            </div>

            <form action="{{ url('/trips') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- From -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Starting Stop (From)</label>
                    <div class="relative">
                        <select name="from" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                            <option value="Nagar" selected>Nagar</option>
                            <option value="Gilgit">Gilgit</option>
                            <option value="Danyor">Danyor</option>
                            <option value="Nomal">Nomal</option>
                            <option value="Aliabad">Aliabad</option>
                            <option value="Hunza">Hunza</option>
                        </select>
                    </div>
                </div>

                <!-- To -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Destination Stop (To)</label>
                    <div class="relative">
                        <select name="to" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                            <option value="Hunza" selected>Hunza</option>
                            <option value="Aliabad">Aliabad</option>
                            <option value="Nagar">Nagar</option>
                            <option value="Nomal">Nomal</option>
                            <option value="Danyor">Danyor</option>
                            <option value="Gilgit">Gilgit</option>
                        </select>
                    </div>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Travel Date</label>
                    <input type="date" name="date" value="2026-10-01" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full py-3 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 transition-all hover:scale-[1.02]">
                        <i class="fa-solid fa-magnifying-glass"></i> Search Trips
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-emerald-600"></i>
                <span><strong>Intermediate Stop Search:</strong> Searching <em>Nagar ➔ Hunza</em> finds all scheduled vehicles starting from Gilgit that stop at Nagar before Hunza.</span>
            </div>
        </div>
    </div>
</section>


<!-- ========================================== -->
<!-- 2. QUICK VALUE SECTION                     -->
<!-- ========================================== -->
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-100">Key Features</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">Everything you need for a smoother journey</h2>
            <p class="text-slate-600 mt-3 text-sm sm:text-base">RouteConnect provides accurate transport schedules and seat availability so you travel without uncertainty.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold mb-4">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <h3 class="font-extrabold text-lg text-slate-900">Scheduled Trips</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Find vehicles running on your route and travel according to their scheduled departure.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl font-bold mb-4">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h3 class="font-extrabold text-lg text-slate-900">Expected Stop Times</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Know when your vehicle is expected to reach your pickup location.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold mb-4">
                    <i class="fa-solid fa-chair"></i>
                </div>
                <h3 class="font-extrabold text-lg text-slate-900">Seat Availability</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Check available seats before leaving home so you don't travel to empty stops.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold mb-4">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3 class="font-extrabold text-lg text-slate-900">Easy Booking</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Book your seat quickly online and keep digital track of your journey details.
                </p>
            </div>

        </div>

    </div>
</section>


<!-- ========================================== -->
<!-- 3. HOW IT WORKS SECTION                    -->
<!-- ========================================== -->
<section class="py-16 lg:py-24 bg-slate-50 border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-600 bg-emerald-100 px-3.5 py-1 rounded-full">Simple Workflow</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">How RouteConnect Works</h2>
            <p class="text-slate-600 mt-2 text-sm">Book your journey seat in three simple steps.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            
            <!-- Step 1 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm relative space-y-4">
                <div class="text-4xl font-extrabold text-emerald-600 font-mono">01</div>
                <h3 class="text-xl font-extrabold text-slate-900">Search</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Enter your starting point, destination, and travel date to find matching scheduled transport.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm relative space-y-4">
                <div class="text-4xl font-extrabold text-teal-600 font-mono">02</div>
                <h3 class="text-xl font-extrabold text-slate-900">Choose</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Compare available scheduled trips, expected arrival times at your stop, and open seat counts.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm relative space-y-4">
                <div class="text-4xl font-extrabold text-indigo-600 font-mono">03</div>
                <h3 class="text-xl font-extrabold text-slate-900">Book</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Select your seats and confirm your booking. Receive instant digital ticket information.
                </p>
            </div>

        </div>

    </div>
</section>


<!-- ========================================== -->
<!-- 4. WHY ROUTECONNECT (PROBLEM SOLVED)       -->
<!-- ========================================== -->
<section id="about" class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-600 bg-rose-50 px-3.5 py-1 rounded-full border border-rose-100">The Problem Solved</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    Stop waiting without knowing.
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Passengers traveling between cities and towns often arrive at transport stops without knowing which vehicle is traveling to their destination, what time it will reach their pickup location, or whether seats are available.
                </p>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    RouteConnect gives both drivers and passengers one platform to publish, search, and check scheduled trips before leaving home.
                </p>
            </div>

            <div class="lg:col-span-6 bg-slate-900 text-white rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
                <h3 class="text-xl font-extrabold text-white">Before RouteConnect vs With RouteConnect</h3>
                
                <div class="space-y-4 text-xs">
                    <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700 flex gap-3">
                        <div class="text-rose-400 font-bold text-base">❌</div>
                        <div>
                            <div class="font-bold text-slate-300">Uncertain Waiting at Stops</div>
                            <div class="text-slate-400 mt-0.5">Reaching stops early and waiting indefinitely for an unscheduled vehicle.</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-700/60 flex gap-3">
                        <div class="text-emerald-400 font-bold text-base">✅</div>
                        <div>
                            <div class="font-bold text-emerald-300">RouteConnect Smart Timetable</div>
                            <div class="text-emerald-200/80 mt-0.5">Check exact expected stop timings and reserve your seat before heading to the stop.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ========================================== -->
<!-- 5. DRIVER CTA SECTION                      -->
<!-- ========================================== -->
<section class="py-16 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl mx-auto space-y-6">
        <div class="w-14 h-14 rounded-2xl bg-white/10 text-emerald-400 mx-auto flex items-center justify-center text-2xl font-bold border border-white/20">
            <i class="fa-solid fa-id-card"></i>
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Drive with RouteConnect</h2>
        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
            Have a vehicle and want to offer scheduled transport? Register as a driver during sign-up and submit your application for administrator approval.
        </p>
        <div>
            <a href="{{ route('register', ['role' => 'driver']) }}" class="inline-block px-8 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm shadow-xl transition-all hover:scale-105">
                Register as Driver
            </a>
        </div>
    </div>
</section>

@endsection
