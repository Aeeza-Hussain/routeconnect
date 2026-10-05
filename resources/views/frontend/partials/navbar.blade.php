<!-- Public Navigation Bar -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- LEFT: Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-location-dot text-lg"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight text-slate-900 group-hover:text-emerald-600 transition-colors">
                        Route<span class="text-emerald-600">Connect</span>
                    </span>
                </a>
            </div>

            <!-- CENTER: Navigation Links -->
            @php
                $currentRoute = request()->route()?->getName() ?? '';
                $currentPath  = request()->path();

                $isHome     = ($currentRoute === 'home'        || $currentPath === '/');
                $isTrips    = ($currentRoute === 'trips.index' || str_starts_with($currentPath, 'trips'));
                $isBookings = ($currentRoute === 'booking.index' || str_starts_with($currentPath, 'booking'));
            @endphp

            <nav class="hidden md:flex items-center gap-8 text-sm font-bold text-slate-600">

                {{-- HOME --}}
                @if($isHome)
                    <a href="{{ url('/') }}" class="relative py-2 text-emerald-600 transition-colors">
                        Home
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 rounded-full"></span>
                    </a>
                @else
                    <a href="{{ url('/') }}" class="py-2 hover:text-emerald-600 transition-colors">Home</a>
                @endif

                {{-- SEARCH TRIPS --}}
                @if($isTrips)
                    <a href="{{ url('/trips') }}" class="relative py-2 text-emerald-600 transition-colors">
                        Search Trips
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 rounded-full"></span>
                    </a>
                @else
                    <a href="{{ url('/trips') }}" class="py-2 hover:text-emerald-600 transition-colors">Search Trips</a>
                @endif

                {{-- MY BOOKINGS --}}
                @if($isBookings)
                    <a href="{{ url('/booking') }}" class="relative py-2 text-emerald-600 transition-colors">
                        My Bookings
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 rounded-full"></span>
                    </a>
                @else
                    <a href="{{ url('/booking') }}" class="py-2 hover:text-emerald-600 transition-colors">My Bookings</a>
                @endif

                {{-- ABOUT --}}
                <a href="{{ url('/#about') }}" class="py-2 hover:text-emerald-600 transition-colors">About</a>

            </nav>

            <!-- RIGHT SIDE: Auth Buttons / Logged-in User Info -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @php
                        $userUnreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl text-slate-600 hover:text-emerald-600 hover:bg-slate-100 transition-colors" title="Notifications">
                        <i class="fa-solid fa-bell text-base"></i>
                        @if($userUnreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 px-1.5 py-0.2 rounded-full bg-emerald-600 text-white text-[9px] font-black leading-tight">
                                {{ $userUnreadCount > 9 ? '9+' : $userUnreadCount }}
                            </span>
                        @endif
                    </a>
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div class="text-right leading-tight">
                            <div class="text-xs font-extrabold text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-emerald-600 font-bold uppercase">
                                @if(auth()->user()->user_type == 1) Admin
                                @elseif(auth()->user()->user_type == 2) Driver
                                @else Passenger
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-full border border-slate-300 text-slate-700 hover:border-emerald-600 hover:text-emerald-600 text-xs font-bold transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all hover:shadow-lg hover:-translate-y-0.5">
                        Register
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex md:hidden">
                <button id="mobileMenuBtn" type="button" class="p-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 focus:outline-none">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3">

        @if($isHome)
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 bg-emerald-50">Home</a>
        @else
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">Home</a>
        @endif

        @if($isTrips)
            <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 bg-emerald-50">Search Trips</a>
        @else
            <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">Search Trips</a>
        @endif

        @if($isBookings)
            <a href="{{ url('/booking') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 bg-emerald-50">My Bookings</a>
        @else
            <a href="{{ url('/booking') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">My Bookings</a>
        @endif

        <a href="{{ url('/#about') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">About</a>
        
        <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
            @auth
                <div class="px-3 py-1 text-xs text-slate-500 font-bold">Logged in as: {{ auth()->user()->name }}</div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-center px-4 py-2.5 rounded-full bg-slate-100 text-slate-800 font-bold text-sm">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full text-center px-4 py-2.5 rounded-full border border-slate-300 text-slate-800 font-bold text-sm">
                    Login
                </a>
                <a href="{{ route('register') }}" class="w-full text-center px-4 py-2.5 rounded-full bg-emerald-600 text-white font-bold text-sm shadow-md">
                    Register
                </a>
            @endauth
        </div>
    </div>
</header>
