<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RouteConnect — Smart Transport Scheduling & Booking System</title>
    <meta name="description" content="Know your route. Know your time. Travel smarter. Local and intercity scheduled transport booking platform.">

    <!-- PWA Manifest & App Theme -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Local FontAwesome 6 Icons (Works Completely Offline) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Local Offline-First Stylesheets -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Google Fonts (With zero-delay local system font fallback) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .gradient-brand {
            background: linear-gradient(135deg, #059669 0%, #0d9488 50%, #4f46e5 100%);
        }

        .gradient-subtle {
            background: linear-gradient(180deg, #f0fdf4 0%, #f8fafc 100%);
        }

        .seat-btn {
            transition: all 0.2s ease-in-out;
        }

        .seat-btn:hover:not(:disabled) {
            transform: scale(1.08);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 antialiased">

    <!-- Top Announcement Notification Banner -->
    <div id="topNotificationBar" class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 text-white text-xs md:text-sm py-2 px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2 overflow-hidden">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white uppercase tracking-wider shrink-0">
                    <i class="fa-solid fa-bullhorn text-[10px] me-1"></i> Live Update
                </span>
                <span id="liveBannerText" class="truncate">
                    Driver standard notice: Bus #GB-8921 (Gilgit ➔ Hunza) is currently <strong>Boarding at Gilgit Terminal</strong>. Departure in 15 mins.
                </span>
            </div>
            <button onclick="dismissBanner()" class="text-white/80 hover:text-white ms-2">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Logo & Tagline -->
                <div class="flex items-center gap-3">
                    <a href="#" onclick="switchRole('passenger')" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-route text-lg"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-emerald-700 via-teal-600 to-indigo-700 bg-clip-text text-transparent">
                                    RouteConnect
                                </span>
                                <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    v1.0 SRS
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 hidden sm:block font-medium">Know your route. Know your time. Travel smarter.</p>
                        </div>
                    </a>
                </div>

                <!-- Global Role Switcher Pills -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button id="btnRolePassenger" onclick="switchRole('passenger')" class="role-tab-btn flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all bg-white text-emerald-700 shadow-xs">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Passenger</span>
                    </button>
                    <button id="btnRoleDriver" onclick="switchRole('driver')" class="role-tab-btn flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-bus"></i>
                        <span>Driver Console</span>
                    </button>
                    <button id="btnRoleAdmin" onclick="switchRole('admin')" class="role-tab-btn flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Admin Panel</span>
                    </button>
                </div>

                <!-- User Profile & Notifications -->
                <div class="flex items-center gap-2">
                    <!-- Notification Dropdown Toggle -->
                    <div class="relative">
                        <button onclick="toggleNotifications()" class="relative p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                            <i class="fa-regular fa-bell text-lg"></i>
                            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white"></span>
                        </button>

                        <!-- Notifications Drawer Popup -->
                        <div id="notificationsMenu" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 p-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-bell text-emerald-600"></i> Trip Notifications
                                </h4>
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">2 New</span>
                            </div>
                            <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto my-1">
                                <div class="py-3 flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-clock flex"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-800">Trip Delayed: Gilgit ➔ Hunza</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Driver Zahid updated: "15 min delay at Danyor stop due to bridge maintenance."</p>
                                        <span class="text-[10px] text-slate-400 mt-1 inline-block">10 mins ago</span>
                                    </div>
                                </div>
                                <div class="py-3 flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-800">Booking Confirmed</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Seats #4, #5 booked successfully for Nagar ➔ Hunza segment.</p>
                                        <span class="text-[10px] text-slate-400 mt-1 inline-block">1 hour ago</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-100 text-center">
                                <button onclick="toggleNotifications()" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Close</button>
                            </div>
                        </div>
                    </div>

                    <!-- Active User Card -->
                    <div class="hidden md:flex items-center gap-3 pl-2 border-l border-slate-200">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-bold text-sm border border-slate-300">
                            <span id="navAvatarInitials">AK</span>
                        </div>
                        <div class="text-left leading-tight">
                            <div id="navUserName" class="text-xs font-bold text-slate-800">Ali Khan</div>
                            <div id="navUserRoleBadge" class="text-[10px] font-semibold text-emerald-600">Passenger</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- MAIN BODY CONTENT AREA -->
    <main class="flex-1 pb-16">

        <!-- ========================================== -->
        <!-- 1. PASSENGER PORTAL VIEW                   -->
        <!-- ========================================== -->
        <div id="viewPassenger" class="view-panel transition-all">
            
            <!-- Hero Banner & Trip Search Section -->
            <section class="relative bg-slate-900 text-white overflow-hidden">
                <!-- Visual Background Image Overlay -->
                <div class="absolute inset-0 z-0 opacity-40 mix-blend-overlay">
                    <img src="/images/hero.jpg" alt="RouteConnect Highway Transport" class="w-full h-full object-cover">
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-emerald-950/80"></div>

                <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                    <div class="max-w-3xl mb-8">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-4">
                            <i class="fa-solid fa-compass"></i> Local & Intercity Scheduled Transport
                        </div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                            Find Scheduled Vehicles. <br class="hidden sm:inline">Never Wait at Stops Again.
                        </h1>
                        <p class="mt-3 text-base sm:text-lg text-slate-300">
                            Search trips passing through your stop (e.g. Nagar ➔ Hunza), view exact expected arrival times per stop, check seat availability, and book online.
                        </p>
                    </div>

                    <!-- Search Form Card -->
                    <div class="glass-card rounded-2xl p-4 sm:p-6 shadow-2xl text-slate-900">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- From Stop Dropdown -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    <i class="fa-solid fa-location-dot text-emerald-600 me-1"></i> Starting Stop (From)
                                </label>
                                <select id="searchFromStop" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-semibold text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none">
                                    <option value="Gilgit">Gilgit (Main Terminal)</option>
                                    <option value="Danyor">Danyor</option>
                                    <option value="Nomal">Nomal</option>
                                    <option value="Nagar" selected>Nagar</option>
                                    <option value="Aliabad">Aliabad</option>
                                    <option value="Hunza">Hunza</option>
                                    <option value="Skardu">Skardu</option>
                                </select>
                            </div>

                            <!-- To Stop Dropdown -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    <i class="fa-solid fa-flag-checkered text-indigo-600 me-1"></i> Destination Stop (To)
                                </label>
                                <select id="searchToStop" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-semibold text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none">
                                    <option value="Hunza" selected>Hunza</option>
                                    <option value="Aliabad">Aliabad</option>
                                    <option value="Nagar">Nagar</option>
                                    <option value="Nomal">Nomal</option>
                                    <option value="Danyor">Danyor</option>
                                    <option value="Gilgit">Gilgit</option>
                                    <option value="Skardu">Skardu</option>
                                </select>
                            </div>

                            <!-- Travel Date -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    <i class="fa-regular fa-calendar-days text-emerald-600 me-1"></i> Travel Date
                                </label>
                                <input type="date" id="searchDate" value="2026-10-01" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-semibold text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none">
                            </div>

                            <!-- Search Action Button -->
                            <div class="flex items-end">
                                <button onclick="executeTripSearch()" class="w-full gradient-brand hover:opacity-95 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 transition-transform active:scale-95">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <span>Search Scheduled Trips</span>
                                </button>
                            </div>
                        </div>

                        <!-- Intermediate Stops Search Hint -->
                        <div class="mt-3 flex items-center gap-2 text-xs text-slate-500 bg-emerald-50/70 p-2.5 rounded-lg border border-emerald-100">
                            <i class="fa-solid fa-circle-info text-emerald-600"></i>
                            <span><strong>Smart Intermediate Search (FR-06):</strong> If a vehicle travels Gilgit ➔ Danyor ➔ Nomal ➔ Nagar ➔ Aliabad ➔ Hunza, searching <em>Nagar ➔ Hunza</em> will find this vehicle and show its exact expected arrival time at Nagar stop!</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Results & Active Passenger Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
                
                <!-- Passenger Tabs: Available Trips | My Bookings -->
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-6">
                    <div class="flex items-center gap-4">
                        <button id="pTabTrips" onclick="togglePassengerTab('trips')" class="font-bold text-base border-b-2 border-emerald-600 text-emerald-700 pb-3 -mb-3 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-bus-simple"></i> Matching Scheduled Trips
                            <span id="tripCountBadge" class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full font-bold">2</span>
                        </button>
                        <button id="pTabBookings" onclick="togglePassengerTab('bookings')" class="font-bold text-base text-slate-500 hover:text-slate-800 pb-3 -mb-3 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-ticket"></i> My Bookings & Tickets
                            <span id="bookingCountBadge" class="bg-slate-200 text-slate-700 text-xs px-2 py-0.5 rounded-full font-bold">1</span>
                        </button>
                    </div>

                    <div class="text-xs text-slate-500 font-medium hidden sm:block">
                        Sorted by pickup arrival time (FR-05)
                    </div>
                </div>

                <!-- SUB-SECTION 1: SEARCH RESULTS LIST -->
                <div id="passengerTripsContainer" class="space-y-6">
                    
                    <!-- Trip Card 1 (Sample Data: Gilgit -> Hunza via Nagar) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        
                        <!-- Top Header info -->
                        <div class="bg-slate-50 px-5 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-bus text-emerald-600"></i> Coaster Bus #GB-8921
                                </span>
                                <span class="text-xs font-semibold text-slate-500">
                                    Driver: <strong>Zahid Ahmed</strong> (⭐ 4.9)
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <!-- Status Badge -->
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200 flex items-center gap-1.5 animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Status: BOARDING AT GILGIT
                                </span>
                                <span class="text-xs font-bold text-slate-700 bg-slate-200/80 px-2.5 py-1 rounded-md">
                                    Fare: Rs. 600 / seat
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                            
                            <!-- Left: Journey Overview & Stop Timings Timeline -->
                            <div class="lg:col-span-8 space-y-4">
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs uppercase font-bold text-slate-400">Full Route Schedule</div>
                                        <div class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                                            Gilgit Terminal <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i> Hunza Express
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-slate-500">Available Seats</div>
                                        <div class="text-sm font-extrabold text-emerald-600 flex items-center justify-end gap-1">
                                            <i class="fa-solid fa-chair text-emerald-500"></i> <span id="seatsLeft-1">12</span> / 24 Seats Available
                                        </div>
                                    </div>
                                </div>

                                <!-- Stop-Wise Expected Times Timeline (FR-07 / SRS Table Example) -->
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/70">
                                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                                        <span><i class="fa-solid fa-clock text-emerald-600 me-1"></i> Intermediate Stops & Expected Arrival (ETA)</span>
                                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded">Your Journey Segment Highlighted</span>
                                    </div>

                                    <div class="relative flex items-center justify-between py-2 px-1">
                                        <!-- Timeline connecting line -->
                                        <div class="absolute left-4 right-4 top-1/2 h-1 bg-slate-200 -z-0"></div>
                                        <div class="absolute left-[50%] right-[10%] top-1/2 h-1 bg-emerald-500 z-0"></div>

                                        <!-- Stop Node 1 -->
                                        <div class="relative z-10 text-center bg-white p-1 rounded-lg border border-slate-200">
                                            <div class="w-6 h-6 mx-auto rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center">1</div>
                                            <div class="text-xs font-bold text-slate-700 mt-1">Gilgit</div>
                                            <div class="text-[10px] font-semibold text-slate-500">08:00 AM</div>
                                        </div>

                                        <!-- Stop Node 2 -->
                                        <div class="relative z-10 text-center bg-white p-1 rounded-lg border border-slate-200">
                                            <div class="w-6 h-6 mx-auto rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center">2</div>
                                            <div class="text-xs font-bold text-slate-700 mt-1">Danyor</div>
                                            <div class="text-[10px] font-semibold text-slate-500">08:15 AM</div>
                                        </div>

                                        <!-- Stop Node 3 -->
                                        <div class="relative z-10 text-center bg-white p-1 rounded-lg border border-slate-200">
                                            <div class="w-6 h-6 mx-auto rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center">3</div>
                                            <div class="text-xs font-bold text-slate-700 mt-1">Nomal</div>
                                            <div class="text-[10px] font-semibold text-slate-500">08:30 AM</div>
                                        </div>

                                        <!-- Stop Node 4 (Your Start) -->
                                        <div class="relative z-10 text-center bg-emerald-50 p-1 rounded-lg border-2 border-emerald-500 shadow-xs">
                                            <div class="w-6 h-6 mx-auto rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center">4</div>
                                            <div class="text-xs font-bold text-emerald-800 mt-1">Nagar (Pickup)</div>
                                            <div class="text-[10px] font-extrabold text-emerald-700">09:00 AM</div>
                                        </div>

                                        <!-- Stop Node 5 -->
                                        <div class="relative z-10 text-center bg-white p-1 rounded-lg border border-slate-200">
                                            <div class="w-6 h-6 mx-auto rounded-full bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center">5</div>
                                            <div class="text-xs font-bold text-slate-700 mt-1">Aliabad</div>
                                            <div class="text-[10px] font-semibold text-slate-500">10:00 AM</div>
                                        </div>

                                        <!-- Stop Node 6 (Your Destination) -->
                                        <div class="relative z-10 text-center bg-emerald-50 p-1 rounded-lg border-2 border-emerald-500 shadow-xs">
                                            <div class="w-6 h-6 mx-auto rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center">6</div>
                                            <div class="text-xs font-bold text-emerald-800 mt-1">Hunza (Drop)</div>
                                            <div class="text-[10px] font-extrabold text-emerald-700">10:30 AM</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Latest Driver Update (FR-16) -->
                                <div class="bg-amber-50/90 border border-amber-200 rounded-xl p-3 flex items-start gap-3">
                                    <div class="text-amber-600 mt-0.5">
                                        <i class="fa-solid fa-comment-dots text-base"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-amber-900">Latest Driver Message</span>
                                            <span class="text-[10px] text-amber-700">Posted 12 mins ago</span>
                                        </div>
                                        <p class="text-xs text-amber-800 mt-0.5">
                                            "Passengers at Nagar stop please be ready by 08:55 AM. Vehicle has left Danyor stop on time."
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <!-- Right: Booking Action Box -->
                            <div class="lg:col-span-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-center space-y-3">
                                <div class="text-xs text-slate-500 uppercase font-bold">Selected Journey</div>
                                <div class="text-lg font-extrabold text-slate-800">
                                    Nagar <i class="fa-solid fa-arrow-right text-xs text-emerald-600"></i> Hunza
                                </div>
                                <div class="text-xs text-slate-600">
                                    Estimated Pickup: <strong class="text-slate-900">09:00 AM</strong> <br>
                                    Estimated Arrival: <strong class="text-slate-900">10:30 AM</strong>
                                </div>
                                <div class="pt-2">
                                    <button onclick="openSeatSelector('Gilgit ➔ Hunza', 'Zahid Ahmed', 'Coaster #GB-8921', 'Nagar', 'Hunza', '09:00 AM', 600)" class="w-full gradient-brand hover:opacity-95 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 transition-all">
                                        <i class="fa-solid fa-chair"></i>
                                        <span>Select Seats & Book</span>
                                    </button>
                                </div>
                                <p class="text-[11px] text-slate-400">Instant confirmation • Free cancellation up to 1 hr before departure</p>
                            </div>

                        </div>
                    </div>

                    <!-- Trip Card 2 (HiAce Van: Skardu -> Gilgit) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <div class="bg-slate-50 px-5 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-van-shuttle text-indigo-600"></i> HiAce Executive Van #LES-4410
                                </span>
                                <span class="text-xs font-semibold text-slate-500">
                                    Driver: <strong>Tariq Mahmood</strong> (⭐ 4.8)
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Status: SCHEDULED
                                </span>
                                <span class="text-xs font-bold text-slate-700 bg-slate-200/80 px-2.5 py-1 rounded-md">
                                    Fare: Rs. 900 / seat
                                </span>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                            <div class="lg:col-span-8 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs uppercase font-bold text-slate-400">Full Route Schedule</div>
                                        <div class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                                            Gilgit Terminal <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i> Skardu Junction
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-slate-500">Available Seats</div>
                                        <div class="text-sm font-extrabold text-emerald-600 flex items-center justify-end gap-1">
                                            <i class="fa-solid fa-chair text-emerald-500"></i> 6 / 15 Seats Available
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/70">
                                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                        <i class="fa-solid fa-clock text-indigo-600 me-1"></i> Intermediate Stops Timetable
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-center text-xs">
                                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                                            <div class="font-bold text-slate-800">Gilgit</div>
                                            <div class="text-[11px] text-slate-500">11:00 AM</div>
                                        </div>
                                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                                            <div class="font-bold text-slate-800">Danyor</div>
                                            <div class="text-[11px] text-slate-500">11:20 AM</div>
                                        </div>
                                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                                            <div class="font-bold text-slate-800">Astak</div>
                                            <div class="text-[11px] text-slate-500">01:30 PM</div>
                                        </div>
                                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                                            <div class="font-bold text-slate-800">Skardu</div>
                                            <div class="text-[11px] text-slate-500">03:45 PM</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-center space-y-3">
                                <div class="text-xs text-slate-500 uppercase font-bold">Selected Journey</div>
                                <div class="text-lg font-extrabold text-slate-800">
                                    Gilgit <i class="fa-solid fa-arrow-right text-xs text-indigo-600"></i> Skardu
                                </div>
                                <div class="text-xs text-slate-600">
                                    Departure: <strong class="text-slate-900">11:00 AM</strong>
                                </div>
                                <div class="pt-2">
                                    <button onclick="openSeatSelector('Gilgit ➔ Skardu', 'Tariq Mahmood', 'HiAce #LES-4410', 'Gilgit', 'Skardu', '11:00 AM', 900)" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md flex items-center justify-center gap-2 transition-all">
                                        <i class="fa-solid fa-chair"></i>
                                        <span>Select Seats & Book</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- SUB-SECTION 2: PASSENGER BOOKING HISTORY (HIDDEN BY DEFAULT) -->
                <div id="passengerBookingsContainer" class="hidden space-y-4">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-extrabold text-lg text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-ticket text-emerald-600"></i> My Active Bookings
                            </h3>
                            <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-3 py-1 rounded-full">1 Confirmed</span>
                        </div>

                        <!-- Ticket Item Card -->
                        <div id="myTicketCard" class="border border-slate-200 rounded-xl p-5 bg-slate-50 flex flex-wrap items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-500">Booking Ref:</span>
                                    <span class="text-xs font-extrabold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">RC-883921</span>
                                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full uppercase">Confirmed</span>
                                </div>
                                <div class="text-base font-extrabold text-slate-800 mt-1">
                                    Nagar Stop <i class="fa-solid fa-arrow-right text-xs text-emerald-600 mx-1"></i> Hunza
                                </div>
                                <div class="text-xs text-slate-600">
                                    Vehicle: <strong>Coaster Bus #GB-8921</strong> | Driver: <strong>Zahid Ahmed</strong>
                                </div>
                                <div class="text-xs text-slate-600">
                                    Date: <strong>01 Oct 2026</strong> | Pickup Time: <strong>09:00 AM</strong> | Seat No: <strong>#4, #5</strong>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <button onclick="viewDigitalPass('RC-883921', 'Nagar', 'Hunza', '09:00 AM', 'Coaster Bus #GB-8921', 'Zahid Ahmed', '#4, #5', 'Rs. 1,200')" class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-800 text-xs font-bold shadow-xs flex items-center gap-2">
                                    <i class="fa-solid fa-qrcode text-emerald-600"></i> View Digital Ticket
                                </button>
                                <button onclick="cancelBooking('RC-883921')" class="px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-xmark"></i> Cancel Booking
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!-- ========================================== -->
        <!-- 2. DRIVER CONSOLE VIEW                     -->
        <!-- ========================================== -->
        <div id="viewDriver" class="view-panel hidden transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                
                <!-- Driver Header & Quick Actions -->
                <div class="flex flex-wrap items-center justify-between gap-4 mb-8 bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-6 rounded-2xl shadow-lg">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">Verified Driver Workspace</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Driver Console: Zahid Ahmed</h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1">
                            Manage vehicles, schedule planned trips with intermediate stop ETAs, update status, and broadcast passenger messages.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button onclick="openCreateTripModal()" class="gradient-brand hover:opacity-95 text-white font-bold py-2.5 px-5 rounded-xl shadow-md flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-plus"></i> Schedule New Trip
                        </button>
                        <button onclick="openVehicleModal()" class="bg-white/10 hover:bg-white/20 text-white font-bold py-2.5 px-4 rounded-xl border border-white/20 text-sm">
                            <i class="fa-solid fa-van-shuttle"></i> Manage Fleet
                        </button>
                    </div>
                </div>

                <!-- Driver Overview Metrics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-500 uppercase">Active Scheduled Trip</div>
                            <div class="text-2xl font-extrabold text-slate-900 mt-1">Gilgit ➔ Hunza</div>
                            <div class="text-xs text-amber-600 font-bold mt-1">Status: Boarding</div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-route"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-500 uppercase">Booked Seats Today</div>
                            <div class="text-2xl font-extrabold text-emerald-600 mt-1">12 / 24</div>
                            <div class="text-xs text-slate-500 mt-1">50% Occupancy</div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-500 uppercase">Registered Fleet</div>
                            <div class="text-2xl font-extrabold text-slate-900 mt-1">2 Vehicles</div>
                            <div class="text-xs text-emerald-600 font-bold mt-1">Coaster & HiAce</div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-bus"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-500 uppercase">Total Trip Messages</div>
                            <div class="text-2xl font-extrabold text-slate-900 mt-1">4 Broadcasts</div>
                            <div class="text-xs text-slate-500 mt-1">Passengers Notified</div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                    </div>
                </div>

                <!-- DRIVER LIVE TRIP MANAGEMENT PANEL (User's Core Requirement) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                    
                    <div class="bg-slate-900 text-white px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-lg">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-lg">Live Trip Control Desk — Coaster Bus #GB-8921</h3>
                                <p class="text-xs text-slate-300">Gilgit Terminal ➔ Hunza Express | Departure: 08:00 AM</p>
                            </div>
                        </div>

                        <!-- Trip Status Selector Dropdown (FR-17) -->
                        <div class="flex items-center gap-2 bg-slate-800 p-2 rounded-xl border border-slate-700">
                            <label class="text-xs font-bold text-slate-300 uppercase">Trip Status:</label>
                            <select id="driverTripStatusSelect" onchange="updateTripStatus(this.value)" class="bg-slate-900 text-white font-bold text-xs rounded-lg px-3 py-1.5 border border-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                <option value="SCHEDULED">Scheduled</option>
                                <option value="BOARDING" selected>Boarding in Progress</option>
                                <option value="DEPARTED">Departed / On Route</option>
                                <option value="DELAYED">Delayed</option>
                                <option value="COMPLETED">Completed</option>
                                <option value="CANCELLED">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Left: Publish Driver Message Box (FR-16) -->
                        <div class="lg:col-span-6 space-y-4">
                            <div class="border border-emerald-200 bg-emerald-50/50 rounded-2xl p-5">
                                <h4 class="font-extrabold text-slate-800 text-sm flex items-center gap-2 mb-2">
                                    <i class="fa-solid fa-paper-plane text-emerald-600"></i> Broadcast Passenger Message (FR-16)
                                </h4>
                                <p class="text-xs text-slate-600 mb-3">
                                    Send instant website updates to all passengers who booked seats or are viewing this trip schedule.
                                </p>
                                
                                <div class="space-y-3">
                                    <textarea id="driverUpdateInput" rows="3" placeholder="e.g., We are currently boarding at Gilgit Terminal. Delayed 10 mins due to passenger luggage loading..." class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                                    <div class="flex items-center justify-between">
                                        <div class="flex gap-2">
                                            <button onclick="fillPresetMessage('Boarding has started at main terminal.')" class="text-[11px] bg-white border border-slate-200 px-2 py-1 rounded text-slate-600 hover:bg-slate-100">+ Boarding</button>
                                            <button onclick="fillPresetMessage('15 min delay at Danyor due to traffic.')" class="text-[11px] bg-white border border-slate-200 px-2 py-1 rounded text-slate-600 hover:bg-slate-100">+ Delay</button>
                                        </div>
                                        <button onclick="publishDriverUpdate()" class="gradient-brand text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs flex items-center gap-1.5">
                                            <i class="fa-solid fa-bullhorn"></i> Broadcast Notice
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Feed of Published Messages -->
                            <div class="border border-slate-200 rounded-2xl p-4 bg-white">
                                <h5 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Recent Messages Sent</h5>
                                <div id="driverMessageHistory" class="space-y-2 text-xs">
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-slate-800">"Passengers at Nagar stop please be ready by 08:55 AM. Vehicle has left Danyor stop on time."</p>
                                            <span class="text-[10px] text-slate-400">Sent 12 mins ago</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Sent</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Passenger Manifest & Seat View (FR-18) -->
                        <div class="lg:col-span-6 space-y-4">
                            <div class="border border-slate-200 rounded-2xl p-5 bg-white">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-users text-indigo-600"></i> Passenger Bookings Manifest (FR-18)
                                    </h4>
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">12 Seats Booked</span>
                                </div>

                                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                                    
                                    <!-- Passenger 1 -->
                                    <div class="py-3 flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">Ali Khan (0300-9876543)</div>
                                            <div class="text-[11px] text-slate-500">Segment: <strong>Nagar ➔ Hunza</strong></div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Seats #4, #5</span>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Paid: Rs. 1,200</div>
                                        </div>
                                    </div>

                                    <!-- Passenger 2 -->
                                    <div class="py-3 flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">Sara Ahmed (0312-5551234)</div>
                                            <div class="text-[11px] text-slate-500">Segment: <strong>Gilgit ➔ Aliabad</strong></div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-extrabold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">Seats #1, #2, #3</span>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Paid: Rs. 1,800</div>
                                        </div>
                                    </div>

                                    <!-- Passenger 3 -->
                                    <div class="py-3 flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">Usman Tariq (0333-1122334)</div>
                                            <div class="text-[11px] text-slate-500">Segment: <strong>Danyor ➔ Hunza</strong></div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Seats #8, #9</span>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Paid: Rs. 1,200</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <!-- ========================================== -->
        <!-- 3. ADMIN CONTROL PANEL VIEW                -->
        <!-- ========================================== -->
        <div id="viewAdmin" class="view-panel hidden transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-bold uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-shield-halved"></i> Platform Administration
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">System Admin Dashboard</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Verify driver registrations, build master transport routes & stops, and monitor active bookings.
                        </p>
                    </div>
                </div>

                <!-- Admin Metrics Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                        <div class="text-xs text-slate-500 font-bold uppercase">Total Passengers</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">1,420</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                        <div class="text-xs text-slate-500 font-bold uppercase">Active Drivers</div>
                        <div class="text-2xl font-extrabold text-emerald-600 mt-1">48 Approved</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                        <div class="text-xs text-slate-500 font-bold uppercase">Pending Verification</div>
                        <div class="text-2xl font-extrabold text-amber-600 mt-1">2 Drivers</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                        <div class="text-xs text-slate-500 font-bold uppercase">Active Routes</div>
                        <div class="text-2xl font-extrabold text-indigo-600 mt-1">14 Routes</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                        <div class="text-xs text-slate-500 font-bold uppercase">Total Bookings</div>
                        <div class="text-2xl font-extrabold text-teal-600 mt-1">3,890</div>
                    </div>
                </div>

                <!-- DRIVER VERIFICATION QUEUE (FR-20) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                                <i class="fa-solid fa-user-check text-emerald-600"></i> Driver Registrations & Verification Queue (FR-20)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Review license documents and approve or reject driver accounts.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-4">Driver Name</th>
                                    <th class="p-4">Contact</th>
                                    <th class="p-4">License #</th>
                                    <th class="p-4">Vehicle Type</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="p-4 font-bold text-slate-800 flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold">ZA</div>
                                        Zahid Ahmed
                                    </td>
                                    <td class="p-4 text-slate-600">0300-1234567</td>
                                    <td class="p-4 font-mono text-slate-700">PK-LIC-99821</td>
                                    <td class="p-4 text-slate-700">Coaster Bus (#GB-8921)</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">Approved</span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button onclick="alert('Driver profile already approved.')" class="text-xs text-slate-400 font-semibold cursor-not-allowed">Approved</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-4 font-bold text-slate-800 flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-bold">KH</div>
                                        Kamran Hassan
                                    </td>
                                    <td class="p-4 text-slate-600">0315-9988776</td>
                                    <td class="p-4 font-mono text-slate-700">PK-LIC-44120</td>
                                    <td class="p-4 text-slate-700">HiAce Van (#SKD-1102)</td>
                                    <td class="p-4">
                                        <span id="status-kh" class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold">Pending Review</span>
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <button onclick="approveDriver('kh')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                        <button onclick="rejectDriver('kh')" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs">
                                            <i class="fa-solid fa-xmark"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ROUTE & STOP MASTER MANAGEMENT (FR-21 / FR-22) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                                <i class="fa-solid fa-map-location-dot text-indigo-600"></i> Master Transport Routes & Stops Manager (FR-21)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Define stop sequences used by drivers to schedule intermediate ETAs.</p>
                        </div>
                        <button onclick="alert('Route Creator Tool initialized.')" class="bg-slate-900 text-white font-bold text-xs px-4 py-2 rounded-xl">
                            + Add New Route
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div>
                                <div class="font-extrabold text-slate-800 text-sm">Route #1: Gilgit ➔ Hunza Highway</div>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                    <span class="font-bold text-slate-700">Ordered Stops:</span>
                                    <span>Gilgit (Stop 1) ➔ Danyor (Stop 2) ➔ Nomal (Stop 3) ➔ Nagar (Stop 4) ➔ Aliabad (Stop 5) ➔ Hunza (Stop 6)</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full">6 Defined Stops</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </main>


    <!-- ========================================== -->
    <!-- INTERACTIVE MODALS                         -->
    <!-- ========================================== -->

    <!-- MODAL 1: INTERACTIVE SEAT SELECTOR MODAL -->
    <div id="modalSeatSelector" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 id="modalTripTitle" class="font-extrabold text-lg">Gilgit ➔ Hunza Express</h3>
                    <p id="modalTripSub" class="text-xs text-slate-300">Coaster #GB-8921 | Driver: Zahid Ahmed | Segment: Nagar ➔ Hunza</p>
                </div>
                <button onclick="closeModal('modalSeatSelector')" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6">
                <!-- Seat Legend -->
                <div class="flex items-center justify-center gap-6 mb-6 text-xs font-bold text-slate-600 bg-slate-50 py-2.5 rounded-xl border border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-md bg-slate-200 border border-slate-300 inline-block"></span> Available
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-md gradient-brand text-white inline-block flex items-center justify-center text-[10px]"><i class="fa-solid fa-check"></i></span> Selected
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-md bg-slate-400 text-white inline-block"></span> Booked
                    </div>
                </div>

                <!-- Bus Layout Cabin Grid -->
                <div class="bg-slate-100 p-6 rounded-2xl border border-slate-300 max-w-md mx-auto relative">
                    <!-- Driver Wheel Icon -->
                    <div class="flex justify-between items-center pb-4 mb-4 border-b-2 border-dashed border-slate-300 text-slate-500 text-xs font-bold">
                        <span><i class="fa-solid fa-door-open me-1"></i> Passenger Door</span>
                        <span><i class="fa-solid fa-steering-wheel text-lg text-slate-700"></i> Driver Cabin</span>
                    </div>

                    <!-- 2x2 Bus Seat Grid -->
                    <div id="busSeatGrid" class="grid grid-cols-5 gap-3">
                        <!-- JS generated seats will populate here -->
                    </div>
                </div>

                <!-- Selection Summary & Passenger Info -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xs text-slate-500 font-bold uppercase">Selected Seats</div>
                        <div id="selectedSeatsText" class="text-lg font-extrabold text-emerald-600">None Selected</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 font-bold uppercase">Total Fare</div>
                        <div id="selectedSeatsFare" class="text-2xl font-extrabold text-slate-900">Rs. 0</div>
                    </div>
                    <button id="btnConfirmBooking" onclick="processSeatBooking()" disabled class="gradient-brand opacity-50 cursor-not-allowed text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all">
                        Confirm & Generate Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL 2: DIGITAL TICKET / PASS MODAL -->
    <div id="modalDigitalPass" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 text-white p-6 text-center relative">
                <div class="w-12 h-12 rounded-2xl bg-white/20 text-white mx-auto flex items-center justify-center text-xl font-bold mb-2">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3 class="font-extrabold text-xl">RouteConnect Travel Pass</h3>
                <p class="text-xs text-emerald-100 mt-0.5">Show this pass to driver during vehicle boarding</p>
                <button onclick="closeModal('modalDigitalPass')" class="absolute top-4 right-4 text-white/80 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center">
                    <div class="text-xs font-bold text-slate-400 uppercase">Booking Reference</div>
                    <div id="ticketPassRef" class="text-xl font-extrabold font-mono text-emerald-700">RC-883921</div>
                    <div class="mt-3 flex justify-center">
                        <!-- Simulated QR Code SVG -->
                        <div class="p-2 bg-white rounded-xl border border-slate-300">
                            <svg class="w-28 h-28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="100" height="100" fill="white"/>
                                <path d="M10 10H40V40H10V10ZM20 20V30H30V20H20Z" fill="black"/>
                                <path d="M60 10H90V40H60V10ZM70 20V30H80V20H70Z" fill="black"/>
                                <path d="M10 60H40V90H10V60ZM20 70V80H30V70H20Z" fill="black"/>
                                <rect x="50" y="50" width="15" height="15" fill="black"/>
                                <rect x="70" y="65" width="20" height="25" fill="black"/>
                                <rect x="50" y="75" width="15" height="15" fill="black"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Passenger Name:</span>
                        <strong id="ticketPassName" class="text-slate-800">Ali Khan</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Route Segment:</span>
                        <strong id="ticketPassRoute" class="text-slate-800">Nagar ➔ Hunza</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Pickup Expected:</span>
                        <strong id="ticketPassTime" class="text-slate-800">09:00 AM</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Seats Reserved:</span>
                        <strong id="ticketPassSeats" class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-extrabold">#4, #5</strong>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Vehicle & Driver:</span>
                        <strong id="ticketPassVehicle" class="text-slate-800">Coaster #GB-8921 (Zahid)</strong>
                    </div>
                </div>

                <div class="pt-2">
                    <button onclick="closeModal('modalDigitalPass')" class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-slate-800">
                        Done & Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL 3: DRIVER CREATE TRIP MODAL (FR-14 / FR-15) -->
    <div id="modalCreateTrip" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-lg">Schedule New Transport Trip</h3>
                    <p class="text-xs text-slate-300">Set route, departure time, vehicle, and intermediate stop ETAs</p>
                </div>
                <button onclick="closeModal('modalCreateTrip')" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Select Route</label>
                        <select class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold">
                            <option>Gilgit ➔ Hunza Highway Express</option>
                            <option>Gilgit ➔ Skardu Express</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Assigned Vehicle</label>
                        <select class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold">
                            <option>Coaster Bus #GB-8921 (24 Seats)</option>
                            <option>HiAce Executive Van #LES-4410 (15 Seats)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Departure Date & Time</label>
                        <input type="datetime-local" value="2026-10-01T08:00" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Seat Base Price (Rs.)</label>
                        <input type="number" value="600" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold">
                    </div>
                </div>

                <!-- Intermediate Stop ETAs Builder (FR-15) -->
                <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50">
                    <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-clock text-emerald-600 me-1"></i> Intermediate Stops & Expected Arrival Times (ETA)
                    </h5>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-slate-200">
                            <span class="font-bold text-slate-800">Stop 1: Gilgit (Origin)</span>
                            <input type="time" value="08:00" class="border border-slate-300 rounded px-2 py-0.5 font-bold">
                        </div>
                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-slate-200">
                            <span class="font-bold text-slate-800">Stop 2: Danyor</span>
                            <input type="time" value="08:15" class="border border-slate-300 rounded px-2 py-0.5 font-bold">
                        </div>
                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-slate-200">
                            <span class="font-bold text-slate-800">Stop 3: Nomal</span>
                            <input type="time" value="08:30" class="border border-slate-300 rounded px-2 py-0.5 font-bold">
                        </div>
                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-slate-200">
                            <span class="font-bold text-slate-800">Stop 4: Nagar</span>
                            <input type="time" value="09:00" class="border border-slate-300 rounded px-2 py-0.5 font-bold">
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button onclick="closeModal('modalCreateTrip')" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">Cancel</button>
                    <button onclick="saveNewTrip()" class="gradient-brand text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md">Publish Trip</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold">
                    <i class="fa-solid fa-route"></i>
                </div>
                <span class="font-extrabold text-slate-800">RouteConnect Platform</span>
                <span>— Smart Transport Scheduling & Booking System</span>
            </div>
            <div>
                © 2026 RouteConnect SRS Specification Implementation.
            </div>
        </div>
    </footer>


    <!-- INTERACTIVE JAVASCRIPT STATE ENGINE -->
    <script>
        // State variables
        let selectedSeats = [];
        let seatPricePerTicket = 600;
        let currentTripData = {};

        // 1. Role Switcher Logic
        function switchRole(role) {
            // Hide all views
            document.querySelectorAll('.view-panel').forEach(el => el.classList.add('hidden'));
            
            // Reset tab button styles
            document.querySelectorAll('.role-tab-btn').forEach(btn => {
                btn.className = 'role-tab-btn flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-all';
            });

            if (role === 'passenger') {
                document.getElementById('viewPassenger').classList.remove('hidden');
                const btn = document.getElementById('btnRolePassenger');
                btn.className = 'role-tab-btn flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all bg-white text-emerald-700 shadow-xs';
                
                document.getElementById('navUserName').innerText = 'Ali Khan';
                document.getElementById('navUserRoleBadge').innerText = 'Passenger';
                document.getElementById('navAvatarInitials').innerText = 'AK';
            } else if (role === 'driver') {
                document.getElementById('viewDriver').classList.remove('hidden');
                const btn = document.getElementById('btnRoleDriver');
                btn.className = 'role-tab-btn flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all bg-emerald-700 text-white shadow-xs';
                
                document.getElementById('navUserName').innerText = 'Zahid Ahmed';
                document.getElementById('navUserRoleBadge').innerText = 'Verified Driver';
                document.getElementById('navAvatarInitials').innerText = 'ZA';
            } else if (role === 'admin') {
                document.getElementById('viewAdmin').classList.remove('hidden');
                const btn = document.getElementById('btnRoleAdmin');
                btn.className = 'role-tab-btn flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all bg-indigo-700 text-white shadow-xs';
                
                document.getElementById('navUserName').innerText = 'Admin Portal';
                document.getElementById('navUserRoleBadge').innerText = 'System Administrator';
                document.getElementById('navAvatarInitials').innerText = 'AD';
            }
        }

        // 2. Notification Drawer Toggle
        function toggleNotifications() {
            const menu = document.getElementById('notificationsMenu');
            menu.classList.toggle('hidden');
        }

        function dismissBanner() {
            document.getElementById('topNotificationBar').classList.add('hidden');
        }

        // 3. Passenger Tab Switcher
        function togglePassengerTab(tab) {
            const btnTrips = document.getElementById('pTabTrips');
            const btnBookings = document.getElementById('pTabBookings');
            const containerTrips = document.getElementById('passengerTripsContainer');
            const containerBookings = document.getElementById('passengerBookingsContainer');

            if (tab === 'trips') {
                containerTrips.classList.remove('hidden');
                containerBookings.classList.add('hidden');
                btnTrips.className = 'font-bold text-base border-b-2 border-emerald-600 text-emerald-700 pb-3 -mb-3 transition-colors flex items-center gap-2';
                btnBookings.className = 'font-bold text-base text-slate-500 hover:text-slate-800 pb-3 -mb-3 transition-colors flex items-center gap-2';
            } else {
                containerTrips.classList.add('hidden');
                containerBookings.classList.remove('hidden');
                btnBookings.className = 'font-bold text-base border-b-2 border-emerald-600 text-emerald-700 pb-3 -mb-3 transition-colors flex items-center gap-2';
                btnTrips.className = 'font-bold text-base text-slate-500 hover:text-slate-800 pb-3 -mb-3 transition-colors flex items-center gap-2';
            }
        }

        // 4. Smart Intermediate Stop Search
        function executeTripSearch() {
            const from = document.getElementById('searchFromStop').value;
            const to = document.getElementById('searchToStop').value;

            if (from === to) {
                alert('Please select different From and To stops for your journey search.');
                return;
            }

            alert(`Searching scheduled trips passing through ${from} ➔ ${to} on 01 Oct 2026...\n\nFound matching vehicles carrying intermediate stop route segments!`);
            togglePassengerTab('trips');
        }

        // 5. Interactive Seat Selector Modal
        function openSeatSelector(routeTitle, driverName, vehicleName, pickupStop, dropStop, pickupTime, fare) {
            currentTripData = { routeTitle, driverName, vehicleName, pickupStop, dropStop, pickupTime, fare };
            seatPricePerTicket = fare;
            selectedSeats = [];

            document.getElementById('modalTripTitle').innerText = routeTitle;
            document.getElementById('modalTripSub').innerText = `${vehicleName} | Driver: ${driverName} | Segment: ${pickupStop} ➔ ${dropStop}`;
            
            // Build seat grid (24 Seats for Bus)
            const grid = document.getElementById('busSeatGrid');
            grid.innerHTML = '';

            const bookedSeatNumbers = [1, 2, 3, 8, 9]; // Pre-booked mock seats

            for (let i = 1; i <= 24; i++) {
                const isBooked = bookedSeatNumbers.includes(i);
                
                const btn = document.createElement('button');
                btn.className = `seat-btn p-2.5 rounded-xl font-extrabold text-xs flex flex-col items-center justify-center transition-all ${
                    isBooked 
                        ? 'bg-slate-300 text-slate-500 cursor-not-allowed border border-slate-400' 
                        : 'bg-white text-slate-800 border-2 border-slate-300 hover:border-emerald-500 hover:shadow-sm'
                }`;
                btn.innerHTML = `<i class="fa-solid fa-chair text-sm mb-0.5"></i> #${i}`;
                btn.disabled = isBooked;

                if (!isBooked) {
                    btn.onclick = () => toggleSeatSelection(i, btn);
                }

                grid.appendChild(btn);
            }

            updateSeatSelectionUI();
            document.getElementById('modalSeatSelector').classList.remove('hidden');
        }

        function toggleSeatSelection(seatNum, btn) {
            if (selectedSeats.includes(seatNum)) {
                selectedSeats = selectedSeats.filter(s => s !== seatNum);
                btn.className = 'seat-btn p-2.5 rounded-xl font-extrabold text-xs flex flex-col items-center justify-center transition-all bg-white text-slate-800 border-2 border-slate-300 hover:border-emerald-500 hover:shadow-sm';
            } else {
                selectedSeats.push(seatNum);
                btn.className = 'seat-btn p-2.5 rounded-xl font-extrabold text-xs flex flex-col items-center justify-center transition-all gradient-brand text-white border-2 border-emerald-600 shadow-md scale-105';
            }
            updateSeatSelectionUI();
        }

        function updateSeatSelectionUI() {
            const textEl = document.getElementById('selectedSeatsText');
            const fareEl = document.getElementById('selectedSeatsFare');
            const confirmBtn = document.getElementById('btnConfirmBooking');

            if (selectedSeats.length === 0) {
                textEl.innerText = 'None Selected';
                fareEl.innerText = 'Rs. 0';
                confirmBtn.disabled = true;
                confirmBtn.className = 'gradient-brand opacity-50 cursor-not-allowed text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all';
            } else {
                textEl.innerText = selectedSeats.map(s => `#${s}`).join(', ');
                fareEl.innerText = `Rs. ${(selectedSeats.length * seatPricePerTicket).toLocaleString()}`;
                confirmBtn.disabled = false;
                confirmBtn.className = 'gradient-brand hover:opacity-95 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all active:scale-95';
            }
        }

        function processSeatBooking() {
            closeModal('modalSeatSelector');
            const seatsFormatted = selectedSeats.map(s => `#${s}`).join(', ');
            const totalCost = `Rs. ${(selectedSeats.length * seatPricePerTicket).toLocaleString()}`;

            // Update remaining seats badge
            const elLeft = document.getElementById('seatsLeft-1');
            if (elLeft) {
                const currentLeft = parseInt(elLeft.innerText);
                elLeft.innerText = Math.max(0, currentLeft - selectedSeats.length);
            }

            viewDigitalPass('RC-' + Math.floor(100000 + Math.random() * 900000), currentTripData.pickupStop, currentTripData.dropStop, currentTripData.pickupTime, currentTripData.vehicleName, currentTripData.driverName, seatsFormatted, totalCost);
        }

        function viewDigitalPass(ref, from, to, time, vehicle, driver, seats, cost) {
            document.getElementById('ticketPassRef').innerText = ref;
            document.getElementById('ticketPassName').innerText = 'Ali Khan';
            document.getElementById('ticketPassRoute').innerText = `${from} ➔ ${to}`;
            document.getElementById('ticketPassTime').innerText = time;
            document.getElementById('ticketPassSeats').innerText = seats;
            document.getElementById('ticketPassVehicle').innerText = `${vehicle} (${driver})`;

            document.getElementById('modalDigitalPass').classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function cancelBooking(ref) {
            if (confirm(`Are you sure you want to cancel booking ${ref}? Available seats will be released.`)) {
                document.getElementById('myTicketCard').remove();
                document.getElementById('bookingCountBadge').innerText = '0';
                alert('Booking cancelled successfully.');
            }
        }

        // 6. Driver Control Desk Logic
        function updateTripStatus(status) {
            const statusTextMap = {
                'SCHEDULED': 'SCHEDULED',
                'BOARDING': 'BOARDING AT GILGIT',
                'DEPARTED': 'DEPARTED / ON ROUTE',
                'DELAYED': 'DELAYED',
                'COMPLETED': 'COMPLETED',
                'CANCELLED': 'CANCELLED'
            };

            const bannerText = document.getElementById('liveBannerText');
            if (bannerText) {
                bannerText.innerHTML = `Driver standard notice: Bus #GB-8921 status updated to <strong>${statusTextMap[status]}</strong>.`;
            }

            alert(`Trip Status changed to: ${status}. Passengers notified!`);
        }

        function fillPresetMessage(msg) {
            document.getElementById('driverUpdateInput').value = msg;
        }

        function publishDriverUpdate() {
            const input = document.getElementById('driverUpdateInput');
            const msg = input.value.trim();
            if (!msg) {
                alert('Please enter a message to broadcast.');
                return;
            }

            const history = document.getElementById('driverMessageHistory');
            const item = document.createElement('div');
            item.className = 'p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex justify-between items-start';
            item.innerHTML = `
                <div>
                    <p class="font-medium text-slate-800">"${msg}"</p>
                    <span class="text-[10px] text-slate-400">Sent just now</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Sent</span>
            `;
            history.prepend(item);

            // Update banner notice
            document.getElementById('liveBannerText').innerText = `Driver update: "${msg}"`;

            input.value = '';
            alert('Message published successfully to all passengers on this trip!');
        }

        function openCreateTripModal() {
            document.getElementById('modalCreateTrip').classList.remove('hidden');
        }

        function saveNewTrip() {
            closeModal('modalCreateTrip');
            alert('New Scheduled Trip created successfully with intermediate stop ETAs!');
        }

        function openVehicleModal() {
            alert('Vehicle Fleet Manager initialized. You have 2 registered vehicles.');
        }

        // 7. Admin Driver Verification Logic
        function approveDriver(id) {
            document.getElementById(`status-${id}`).innerText = 'Approved';
            document.getElementById(`status-${id}`).className = 'px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold';
            alert('Driver account approved successfully!');
        }

        function rejectDriver(id) {
            document.getElementById(`status-${id}`).innerText = 'Rejected';
            document.getElementById(`status-${id}`).className = 'px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-bold';
            alert('Driver application rejected.');
        }
    </script>
    @include('partials.offline_indicator')
</body>
</html>
