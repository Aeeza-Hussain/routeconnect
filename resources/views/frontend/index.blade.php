@extends('frontend.layouts.app')

@section('title', 'RouteConnect — Smart Transport Scheduling & Booking System')

@section('content')

<!-- ========================================== -->
<!-- 1. HERO SECTION & BACKGROUND IMAGE         -->
<!-- ========================================== -->
<section class="relative bg-slate-950 text-white overflow-hidden pt-12 pb-24 lg:pt-16 lg:pb-32">
    <!-- Full-Width High Quality Mountain Highway Bus Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="/images/hero.jpg" alt="RouteConnect Scenic Mountain Highway Bus" class="w-full h-full object-cover">
        <!-- Dark Gradient Overlay for Readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/80 to-slate-950/30"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl space-y-6">
            
            <!-- Small Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                <i class="fa-solid fa-bus text-emerald-400"></i> SMART TRANSPORT SCHEDULING
            </div>

            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                Travel with confidence. <br>
                <span class="text-emerald-400">
                    Know your route before you leave.
                </span>
            </h1>

            <!-- Supporting Paragraph -->
            <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-medium">
                Find scheduled vehicles, check expected stop timings, see available seats, and book your journey in advance.
            </p>

            <!-- Hero Action Buttons -->
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="#searchCard" class="px-6 py-3.5 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg shadow-emerald-500/30 flex items-center gap-2 transition-all hover:-translate-y-0.5">
                    <i class="fa-solid fa-magnifying-glass"></i> Search Trips
                </a>
                <a href="{{ route('register', ['role' => 'driver']) }}" class="px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-user"></i> Become a Driver
                </a>
            </div>

        </div>
    </div>
</section>


<!-- ========================================== -->
<!-- 2. FLOATING TRIP SEARCH CARD               -->
<!-- ========================================== -->
<section id="searchCard" class="relative z-20 -mt-16 sm:-mt-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200/80 text-slate-900">
        
        <div class="flex items-center gap-2 mb-6">
            <i class="fa-solid fa-magnifying-glass text-emerald-600 text-lg"></i>
            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Find Your Trip</h2>
        </div>

        <form action="{{ url('/trips') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            
            <!-- From Stop -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">From</label>
                <div class="relative">
                    <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                    <select name="from" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                        <option value="Nagar" selected>e.g. Nagar</option>
                        <option value="Gilgit">Gilgit</option>
                        <option value="Danyor">Danyor</option>
                        <option value="Nomal">Nomal</option>
                        <option value="Aliabad">Aliabad</option>
                        <option value="Hunza">Hunza</option>
                    </select>
                </div>
            </div>

            <!-- To Stop -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">To</label>
                <div class="relative">
                    <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                    <select name="to" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                        <option value="Hunza" selected>e.g. Hunza</option>
                        <option value="Aliabad">Aliabad</option>
                        <option value="Nagar">Nagar</option>
                        <option value="Nomal">Nomal</option>
                        <option value="Danyor">Danyor</option>
                        <option value="Gilgit">Gilgit</option>
                    </select>
                </div>
            </div>

            <!-- Date -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Date</label>
                <div class="relative">
                    <i class="fa-regular fa-calendar absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                    <input type="date" name="date" value="2026-10-01" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                </div>
            </div>

            <!-- Preferred Time (Optional) -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Preferred Time (Optional)</label>
                <div class="relative">
                    <i class="fa-regular fa-clock absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                    <select name="time" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                        <option value="">Select time</option>
                        <option value="morning">Morning (08:00 AM)</option>
                        <option value="afternoon">Afternoon (01:00 PM)</option>
                    </select>
                </div>
            </div>

            <!-- Search Button -->
            <div class="lg:col-span-2">
                <button type="submit" class="w-full py-3 px-5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-500/20 flex items-center justify-center gap-2 transition-all hover:scale-[1.02]">
                    <i class="fa-solid fa-magnifying-glass"></i> Search Trips
                </button>
            </div>

        </form>
    </div>
</section>


<!-- ========================================== -->
<!-- 3. QUICK VALUE SECTION                     -->
<!-- ========================================== -->
<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Everything you need for a smoother journey</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Feature 1: Scheduled Trips -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Scheduled Trips</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Find vehicles running on your route and travel according to their scheduled departure.
                </p>
            </div>

            <!-- Feature 2: Expected Stop Times -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Expected Stop Times</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Know when your vehicle is expected to reach your stop.
                </p>
            </div>

            <!-- Feature 3: Seat Availability -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-chair"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Seat Availability</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Check available seats before you leave home.
                </p>
            </div>

            <!-- Feature 4: Easy Booking -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Easy Booking</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Book your seat quickly and keep track of your journey.
                </p>
            </div>

        </div>

    </div>
</section>


<!-- ========================================== -->
<!-- 4. HOW IT WORKS & WHY ROUTECONNECT GRID    -->
<!-- ========================================== -->
<section id="about" class="py-16 lg:py-20 bg-slate-50/60 border-t border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left: How RouteConnect Works (7 Cols) -->
            <div class="lg:col-span-7 bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-8 flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">How RouteConnect Works</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 relative">
                    <!-- Step 1 -->
                    <div class="space-y-3 relative">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-500 text-white font-extrabold text-xs flex items-center justify-center">01</span>
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-emerald-600 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                        </div>
                        <h3 class="font-extrabold text-sm text-slate-900">Search</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Enter your starting point, destination and travel date.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="space-y-3 relative">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-500 text-white font-extrabold text-xs flex items-center justify-center">02</span>
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-teal-600 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                        </div>
                        <h3 class="font-extrabold text-sm text-slate-900">Choose</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Compare available scheduled trips, timings and seats.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="space-y-3 relative">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-500 text-white font-extrabold text-xs flex items-center justify-center">03</span>
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-ticket"></i>
                            </div>
                        </div>
                        <h3 class="font-extrabold text-sm text-slate-900">Book</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Select your seats and confirm your booking.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right: Why RouteConnect / Problem Card (5 Cols) -->
            <div class="lg:col-span-5 relative rounded-3xl overflow-hidden shadow-xl border border-slate-800 text-white min-h-[300px] flex flex-col justify-end p-8">
                <!-- Mountain Backdrop -->
                <img src="/images/hero.jpg" alt="Mountain Transport Route" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-950/40"></div>

                <div class="relative z-10 space-y-3">
                    <div class="w-8 h-1 bg-emerald-400 rounded-full"></div>
                    <h3 class="text-xl font-extrabold text-white">Stop waiting without knowing.</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Instead of reaching a stop without knowing which vehicle is coming or when it is expected, RouteConnect lets passengers check scheduled trips and expected stop timings before leaving home.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>


<!-- ========================================== -->
<!-- 5. DRIVER CTA BANNER                       -->
<!-- ========================================== -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-50 rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-8">
            
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Driver Wheel Photo -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border border-slate-200 shrink-0 shadow-sm">
                    <img src="/images/driver.jpg" alt="Driver behind steering wheel" class="w-full h-full object-cover">
                </div>

                <div class="space-y-2 text-center sm:text-left">
                    <div class="flex items-center justify-center sm:justify-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-lg">Drive with RouteConnect</h3>
                    </div>
                    <p class="text-xs text-slate-600 max-w-md leading-relaxed">
                        Have a vehicle and want to offer scheduled transport? Register as a driver and submit your application for approval.
                    </p>
                </div>
            </div>

            <div>
                <a href="{{ route('register', ['role' => 'driver']) }}" class="px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-500/20 flex items-center gap-2 transition-all hover:scale-105">
                    <span>Become a Driver</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

        </div>
    </div>
</section>

@endsection
