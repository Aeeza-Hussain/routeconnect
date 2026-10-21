<!-- Public Navigation Bar -->
<header class="sticky top-0 z-50 bg-white/95 dark:bg-[#0f172a]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 shadow-xs transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- LEFT: Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-location-dot text-lg"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Route<span class="text-emerald-600 dark:text-emerald-400">Connect</span>
                    </span>
                </a>
            </div>

            <!-- CENTER: Navigation Links -->
            @php
                $currentRoute = request()->route()?->getName() ?? '';
                $currentPath  = request()->path();

                $isHome     = ($currentRoute === 'home'        || $currentPath === '/');
                $isTrips    = ($currentRoute === 'trips.index' || str_starts_with($currentPath, 'trips'));
                $isBookings = ($currentRoute === 'booking.index' || str_starts_with($currentPath, 'booking') || str_starts_with($currentPath, 'my-bookings'));
            @endphp

            <nav class="hidden md:flex items-center gap-8 text-sm font-bold text-slate-600 dark:text-slate-300">

                {{-- HOME --}}
                @if($isHome)
                    <a href="{{ url('/') }}" class="relative py-2 text-emerald-600 dark:text-emerald-400 transition-colors">
                        Home
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                    </a>
                @else
                    <a href="{{ url('/') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Home</a>
                @endif

                {{-- SEARCH TRIPS --}}
                @if($isTrips)
                    <a href="{{ url('/trips') }}" class="relative py-2 text-emerald-600 dark:text-emerald-400 transition-colors">
                        Search Trips
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                    </a>
                @else
                    <a href="{{ url('/trips') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Search Trips</a>
                @endif

                {{-- MY BOOKINGS --}}
                @if($isBookings)
                    <a href="{{ url('/booking') }}" class="relative py-2 text-emerald-600 dark:text-emerald-400 transition-colors">
                        My Bookings
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                    </a>
                @else
                    <a href="{{ url('/booking') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">My Bookings</a>
                @endif

                {{-- ABOUT --}}
                <a href="{{ url('/#about') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">About</a>

            </nav>

            <!-- RIGHT SIDE: Theme Switcher & Auth Buttons -->
            <div class="hidden md:flex items-center gap-3">
                
                <!-- Quick Theme Switcher -->
                @include('partials.theme_switcher')

                @auth
                    @php
                        $userUnreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}"
                       class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                       title="Notifications">
                        <i class="fa-solid fa-bell text-base"></i>
                        @if($userUnreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 px-1.5 py-0.2 rounded-full bg-emerald-600 text-white text-[9px] font-black leading-tight">
                                {{ $userUnreadCount > 9 ? '9+' : $userUnreadCount }}
                            </span>
                        @endif
                    </a>

                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
                        <div class="text-right leading-tight">
                            <div class="text-xs font-extrabold text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase">
                                @if(auth()->user()->user_type == 1) Admin
                                @elseif(auth()->user()->user_type == 2) Driver
                                @else Passenger
                                @endif
                            </div>
                        </div>

                        <!-- Dashboard Shortcut for Admin/Driver -->
                        @if(auth()->user()->user_type == 1)
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors">
                                Dashboard
                            </a>
                        @elseif(auth()->user()->user_type == 2)
                            <a href="{{ route('driver.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 text-xs font-bold border border-teal-200 dark:border-teal-800 hover:bg-teal-100 dark:hover:bg-teal-900/60 transition-colors">
                                Dashboard
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-full border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-600 dark:hover:border-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-400 text-xs font-bold transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all hover:shadow-lg hover:-translate-y-0.5">
                        Register
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button & Theme Icon -->
            <div class="flex items-center gap-2 md:hidden">
                <button type="button"
                        onclick="window.RouteConnectTheme.toggle()"
                        class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none"
                        title="Toggle theme">
                    <i data-theme-current-icon class="fa-solid fa-circle-half-stroke text-emerald-500 text-base"></i>
                </button>

                <button id="mobileMenuBtn" type="button" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-3 pb-6 space-y-3 transition-colors">

        @if($isHome)
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40">Home</a>
        @else
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Home</a>
        @endif

        @if($isTrips)
            <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40">Search Trips</a>
        @else
            <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Search Trips</a>
        @endif

        @if($isBookings)
            <a href="{{ url('/booking') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40">My Bookings</a>
        @else
            <a href="{{ url('/booking') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">My Bookings</a>
        @endif

        <a href="{{ url('/#about') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">About</a>
        
        <!-- Mobile Theme Picker Row -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between px-3 py-2">
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Appearance</span>
            <div class="inline-flex rounded-xl p-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <button type="button" data-theme-option="light" onclick="window.RouteConnectTheme.setTheme('light')" class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600">
                    <i class="fa-solid fa-sun text-amber-500"></i>
                </button>
                <button type="button" data-theme-option="dark" onclick="window.RouteConnectTheme.setTheme('dark')" class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600">
                    <i class="fa-solid fa-moon text-indigo-400"></i>
                </button>
                <button type="button" data-theme-option="system" onclick="window.RouteConnectTheme.setTheme('system')" class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600">
                    <i class="fa-solid fa-desktop text-emerald-500"></i>
                </button>
            </div>
        </div>

        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
            @auth
                <div class="px-3 py-1 text-xs text-slate-500 dark:text-slate-400 font-bold">Logged in as: {{ auth()->user()->name }}</div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-center px-4 py-2.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-sm">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full text-center px-4 py-2.5 rounded-full border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-bold text-sm">
                    Login
                </a>
                <a href="{{ route('register') }}" class="w-full text-center px-4 py-2.5 rounded-full bg-emerald-600 text-white font-bold text-sm shadow-md">
                    Register
                </a>
            @endauth
        </div>
    </div>
</header>
