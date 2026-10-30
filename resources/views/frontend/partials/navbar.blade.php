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

                $isHome     = ($currentRoute === 'home' || $currentPath === '/');
                $isFindRide = ($currentRoute === 'find-a-ride' || $currentRoute === 'trips.index' || $currentRoute === 'vehicles.book' || str_starts_with($currentPath, 'trips') || str_starts_with($currentPath, 'find-a-ride') || str_starts_with($currentPath, 'book-vehicle'));
                $isActivity = ($currentRoute === 'activity.index' || str_starts_with($currentPath, 'my-activity') || str_starts_with($currentPath, 'booking') || str_starts_with($currentPath, 'my-bookings'));
                $isMessages = ($currentRoute === 'messages.index' || $currentRoute === 'messages.show' || str_starts_with($currentPath, 'messages'));

                $unreadMessageCount = auth()->check() ? auth()->user()->unreadMessagesCount() : 0;
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

                {{-- FIND A RIDE --}}
                @if($isFindRide)
                    <a href="{{ url('/trips') }}" class="relative py-2 text-emerald-600 dark:text-emerald-400 transition-colors">
                        Find a Ride
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                    </a>
                @else
                    <a href="{{ url('/trips') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Find a Ride</a>
                @endif

                @auth
                    {{-- MY ACTIVITY --}}
                    @if($isActivity)
                        <a href="{{ route('activity.index') }}" class="relative py-2 text-emerald-600 dark:text-emerald-400 transition-colors">
                            My Activity
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                        </a>
                    @else
                        <a href="{{ route('activity.index') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">My Activity</a>
                    @endif

                    {{-- MESSAGES --}}
                    @if($isMessages)
                        <a href="{{ route('messages.index') }}" class="relative py-2 text-emerald-600 dark:text-emerald-400 transition-colors flex items-center gap-1.5">
                            <span>Messages</span>
                            @if($unreadMessageCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full bg-emerald-600 text-white text-[10px] font-black leading-tight shadow-xs">
                                    {{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}
                                </span>
                            @endif
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                        </a>
                    @else
                        <a href="{{ route('messages.index') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                            <span>Messages</span>
                            @if($unreadMessageCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full bg-emerald-600 text-white text-[10px] font-black leading-tight shadow-xs">
                                    {{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endauth

                {{-- ABOUT --}}
                <a href="{{ url('/#about') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">About</a>

            </nav>

            <!-- RIGHT SIDE: Theme Switcher & Auth Buttons / Profile Dropdown -->
            <div class="hidden md:flex items-center gap-3">
                
                <!-- Quick Theme Switcher -->
                @include('partials.theme_switcher')

                @auth
                    @php
                        $userUnreadNotifCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp

                    {{-- Notifications --}}
                    <a href="{{ route('notifications.index') }}"
                       class="relative p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                       title="Notifications">
                        <i class="fa-solid fa-bell text-base"></i>
                        @if($userUnreadNotifCount > 0)
                            <span class="absolute 1 top-1 right-1 px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-black leading-tight">
                                {{ $userUnreadNotifCount > 9 ? '9+' : $userUnreadNotifCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Profile Dropdown --}}
                    <div class="relative pl-2 border-l border-slate-200 dark:border-slate-800" id="profileDropdownContainer">
                        <button type="button"
                                id="profileDropdownBtn"
                                onclick="toggleProfileDropdown()"
                                class="flex items-center gap-2.5 p-1.5 pr-3 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800/80 transition-colors focus:outline-none">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-xs font-black shadow-sm shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left leading-tight hidden lg:block">
                                <div class="text-xs font-extrabold text-slate-800 dark:text-slate-100 max-w-[110px] truncate">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                    @if(auth()->user()->user_type == 1) Admin
                                    @elseif(auth()->user()->user_type == 2) Driver
                                    @else Passenger
                                    @endif
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" id="profileDropdownChevron"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div id="profileDropdownMenu"
                             class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 shadow-xl py-2 z-50 transition-all text-xs">
                            
                            {{-- Header Info --}}
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ auth()->user()->user_type == 1 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : (auth()->user()->user_type == 2 ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                                    @if(auth()->user()->user_type == 1) Platform Admin
                                    @elseif(auth()->user()->user_type == 2) Verified Driver
                                    @else Passenger Account
                                    @endif
                                </span>
                            </div>

                            {{-- Role-specific Dashboard shortcut --}}
                            <div class="py-1">
                                @if(auth()->user()->user_type == 1)
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-gauge text-slate-400 w-4 text-center"></i>
                                        <span>Admin Dashboard</span>
                                    </a>
                                @elseif(auth()->user()->user_type == 2)
                                    <a href="{{ route('driver.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-gauge text-slate-400 w-4 text-center"></i>
                                        <span>Driver Dashboard</span>
                                    </a>
                                    <a href="{{ route('driver.vehicles.index') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-van-shuttle text-slate-400 w-4 text-center"></i>
                                        <span>My Vehicles</span>
                                    </a>
                                    <a href="{{ route('driver.trips') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-route text-slate-400 w-4 text-center"></i>
                                        <span>My Scheduled Trips</span>
                                    </a>
                                @endif

                                <a href="{{ route('activity.index') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                    <i class="fa-solid fa-clock-rotate-left text-slate-400 w-4 text-center"></i>
                                    <span>My Activity</span>
                                </a>

                                <a href="{{ route('messages.index') }}" class="flex items-center justify-between px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-comments text-slate-400 w-4 text-center"></i>
                                        <span>Messages</span>
                                    </div>
                                    @if($unreadMessageCount > 0)
                                        <span class="px-1.5 py-0.2 rounded-full bg-emerald-600 text-white text-[10px] font-black">
                                            {{ $unreadMessageCount }}
                                        </span>
                                    @endif
                                </a>

                                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-bell text-slate-400 w-4 text-center"></i>
                                        <span>Notifications</span>
                                    </div>
                                    @if($userUnreadNotifCount > 0)
                                        <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-black">
                                            {{ $userUnreadNotifCount }}
                                        </span>
                                    @endif
                                </a>
                            </div>

                            {{-- Logout Item --}}
                            <div class="pt-1 border-t border-slate-100 dark:border-slate-800">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-left transition-colors">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
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

        @if($isFindRide)
            <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40">Find a Ride</a>
        @else
            <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Find a Ride</a>
        @endif

        @auth
            @if($isActivity)
                <a href="{{ route('activity.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40">My Activity</a>
            @else
                <a href="{{ route('activity.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">My Activity</a>
            @endif

            @if($isMessages)
                <a href="{{ route('messages.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40">
                    <span>Messages</span>
                    @if($unreadMessageCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-xs font-black">{{ $unreadMessageCount }}</span>
                    @endif
                </a>
            @else
                <a href="{{ route('messages.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <span>Messages</span>
                    @if($unreadMessageCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-xs font-black">{{ $unreadMessageCount }}</span>
                    @endif
                </a>
            @endif
        @endauth

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
                <div class="px-3 py-1 text-xs text-slate-500 dark:text-slate-400 font-bold flex items-center justify-between">
                    <span>Logged in: {{ auth()->user()->name }}</span>
                    <span class="text-emerald-600 dark:text-emerald-400 uppercase font-black text-[10px]">
                        @if(auth()->user()->user_type == 1) Admin @elseif(auth()->user()->user_type == 2) Driver @else Passenger @endif
                    </span>
                </div>
                @if(auth()->user()->user_type == 1)
                    <a href="{{ route('admin.dashboard') }}" class="w-full text-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs">
                        Admin Dashboard
                    </a>
                @elseif(auth()->user()->user_type == 2)
                    <a href="{{ route('driver.dashboard') }}" class="w-full text-center px-4 py-2 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold text-xs">
                        Driver Dashboard
                    </a>
                @endif
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

<script>
function toggleProfileDropdown() {
    const menu = document.getElementById('profileDropdownMenu');
    const chevron = document.getElementById('profileDropdownChevron');
    if (!menu) return;

    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        if (chevron) chevron.classList.add('rotate-180');
    } else {
        menu.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
    }
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const container = document.getElementById('profileDropdownContainer');
    const menu = document.getElementById('profileDropdownMenu');
    const chevron = document.getElementById('profileDropdownChevron');
    if (container && menu && !container.contains(e.target)) {
        menu.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
    }
});
</script>
