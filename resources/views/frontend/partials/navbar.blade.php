<!-- RouteConnect Professional Navigation Bar -->
@php
    $currentRoute = request()->route()?->getName() ?? '';
    $currentPath  = request()->path();

    $isHome              = ($currentRoute === 'home' || $currentPath === '/');
    $isFindRide          = ($currentRoute === 'find-a-ride' || $currentRoute === 'trips.index' || $currentRoute === 'vehicles.book' || str_starts_with($currentPath, 'trips') || str_starts_with($currentPath, 'find-a-ride') || str_starts_with($currentPath, 'book-vehicle'));
    $isActivity          = ($currentRoute === 'activity.index' || str_starts_with($currentPath, 'my-activity') || str_starts_with($currentPath, 'booking') || str_starts_with($currentPath, 'my-bookings'));
    $isDriverDashboard   = ($currentRoute === 'driver.dashboard' || str_starts_with($currentPath, 'driver/dashboard'));
    $isPassengerRequests = ($currentRoute === 'driver.passenger-requests' || str_starts_with($currentPath, 'driver/passenger-requests'));
    $isMessages          = ($currentRoute === 'messages.index' || $currentRoute === 'messages.show' || str_starts_with($currentPath, 'messages'));

    $user = auth()->user();
    $unreadMessageCount = 0;
    $unreadNotifCount   = 0;
    $navbarConversations = collect();
    $navbarNotifications = collect();

    if ($user) {
        $unreadMessageCount = $user->unreadMessagesCount();
        $unreadNotifCount   = $user->notifications()->where('is_read', false)->count();

        $navbarConversations = \App\Models\Conversation::with([
            'participants.user',
            'latestMessage.sender',
        ])
        ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
        ->latest('updated_at')
        ->take(8)
        ->get();

        $navbarNotifications = $user->notifications()->latest()->take(6)->get();
    }
@endphp

<header id="siteNavbar" class="sticky top-0 z-40 {{ $isFindRide ? 'bg-[#070e1b] border-slate-800 text-white' : 'bg-white/95 dark:bg-[#0f172a]/95 border-slate-200/80 dark:border-slate-800/80 text-slate-900 dark:text-white' }} backdrop-blur-md border-b shadow-xs transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- LEFT: RouteConnect Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-location-dot text-lg"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight {{ $isFindRide ? 'text-white' : 'text-slate-900 dark:text-white' }} group-hover:text-emerald-400 transition-colors">
                        Route<span class="text-emerald-500 dark:text-emerald-400">Connect</span>
                    </span>
                </a>
            </div>

            <!-- CENTER: Role-Conditioned Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-bold {{ $isFindRide ? 'text-slate-300' : 'text-slate-600 dark:text-slate-300' }}">

                {{-- 1. GUEST NAVBAR --}}
                @guest
                    {{-- Home --}}
                    <a href="{{ url('/') }}" class="relative py-2 {{ $isHome ? 'text-emerald-600 dark:text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors">
                        Home
                        @if($isHome)
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                        @endif
                    </a>

                    {{-- Find a Ride --}}
                    <a href="{{ url('/trips') }}" class="relative py-2 {{ $isFindRide ? 'text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors">
                        Find a Ride
                        @if($isFindRide)
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-400 rounded-full"></span>
                        @endif
                    </a>

                    {{-- About --}}
                    <a href="{{ url('/#about') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">About</a>
                @endguest

                {{-- 2. PASSENGER NAVBAR --}}
                @auth
                    @if($user->user_type == 0)
                        {{-- Home --}}
                        <a href="{{ url('/') }}" class="relative py-2 {{ $isHome ? 'text-emerald-600 dark:text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors">
                            Home
                            @if($isHome)
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                            @endif
                        </a>

                        {{-- Find a Ride --}}
                        <a href="{{ url('/trips') }}" class="relative py-2 {{ $isFindRide ? 'text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors">
                            Find a Ride
                            @if($isFindRide)
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-400 rounded-full"></span>
                            @endif
                        </a>

                        {{-- My Activity --}}
                        <a href="{{ route('activity.index') }}" class="relative py-2 {{ $isActivity ? 'text-emerald-600 dark:text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors inline-flex items-center gap-1.5">
                            My Activity
                            @if($isActivity)
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                            @endif
                        </a>
                    @endif

                    {{-- 3. DRIVER NAVBAR --}}
                    @if($user->user_type == 2)
                        {{-- Home --}}
                        <a href="{{ url('/') }}" class="relative py-2 {{ $isHome ? 'text-emerald-600 dark:text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors">
                            Home
                            @if($isHome)
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                            @endif
                        </a>

                        {{-- Dashboard --}}
                        <a href="{{ route('driver.dashboard') }}" class="relative py-2 {{ $isDriverDashboard ? 'text-emerald-600 dark:text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors">
                            Dashboard
                            @if($isDriverDashboard)
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                            @endif
                        </a>

                        {{-- Passenger Requests --}}
                        <a href="{{ route('driver.passenger-requests') }}" class="relative py-2 {{ $isPassengerRequests ? 'text-emerald-600 dark:text-emerald-400' : 'hover:text-emerald-600 dark:hover:text-emerald-400' }} transition-colors">
                            Passenger Requests
                            @if($isPassengerRequests)
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                            @endif
                        </a>
                    @endif

                    {{-- 4. ADMIN USER (Clean shortcut to Admin Console, no clutter in main nav) --}}
                    @if($user->user_type == 1)
                        <a href="{{ url('/') }}" class="py-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Home</a>
                        <a href="{{ route('admin.dashboard') }}" class="py-2 text-amber-500 hover:text-amber-400 font-extrabold flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                            <span>Admin Portal</span>
                        </a>
                    @endif
                @endauth

            </nav>

            <!-- RIGHT: Actions, Inbox Icon, Notification Icon & Profile Dropdown -->
            <div class="hidden md:flex items-center gap-3">
                
                <!-- Theme Switcher -->
                @include('partials.theme_switcher')

                @auth
                    {{-- ─── MESSAGE ICON WITH UNREAD COUNT (Opens Slide-out Inbox Panel) ─── --}}
                    <div class="relative" id="messagesInboxContainer">
                        <button type="button"
                                id="messagesInboxToggleBtn"
                                onclick="toggleMessagesInbox()"
                                title="Messages"
                                aria-label="Messages"
                                class="relative p-2.5 rounded-xl {{ $isMessages ? 'text-emerald-600 dark:text-emerald-400 bg-slate-100 dark:bg-slate-800' : ($isFindRide ? 'text-slate-300 hover:text-white hover:bg-slate-800/80' : 'text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800') }} transition-colors focus:outline-none">
                            <i class="fa-solid fa-comments text-base"></i>
                            <span class="sr-only">Messages</span>
                            @if($isMessages)
                                <span class="absolute bottom-0 left-2 right-2 h-0.5 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                            @endif
                            <span id="navbarUnreadMessageBadge" class="{{ $unreadMessageCount > 0 ? '' : 'hidden' }} absolute top-1 right-1 px-1.5 py-0.2 rounded-full bg-emerald-500 text-white text-[9px] font-black leading-tight">
                                {{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}
                            </span>
                        </button>

                        {{-- Quick Messages Inbox Dropdown Panel --}}
                        <div id="messagesInboxPanel"
                             class="hidden absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 shadow-2xl py-2 z-50 transition-all text-xs">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-sm text-slate-900 dark:text-white">Messages</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">
                                        {{ $unreadMessageCount }} unread
                                    </span>
                                </div>
                                <a href="{{ route('messages.index') }}" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    Full Inbox
                                </a>
                            </div>

                            {{-- Conversations List --}}
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60" id="inboxDropdownList">
                                @forelse($navbarConversations as $conv)
                                    @php
                                        $otherUser = $conv->otherUser($user);
                                        $latestMsg = $conv->latestMessage;
                                        $part = $conv->participants->firstWhere('user_id', $user->id);
                                        $isUnread = $latestMsg && $latestMsg->sender_id != $user->id && (!$part?->last_read_at || $latestMsg->created_at > $part->last_read_at);
                                    @endphp
                                    <button type="button"
                                            onclick="openInPageChat({{ $conv->id }}, '{{ addslashes($otherUser?->name ?? $conv->title) }}')"
                                            class="w-full text-left px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors flex items-start gap-3 {{ $isUnread ? 'bg-emerald-50/50 dark:bg-emerald-950/20' : '' }}">
                                        <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-extrabold flex items-center justify-center text-xs shrink-0 border border-slate-300 dark:border-slate-600">
                                            {{ $otherUser ? strtoupper(substr($otherUser->name, 0, 2)) : 'RC' }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">
                                                    {{ $otherUser?->name ?? $conv->title }}
                                                </h4>
                                                <span class="text-[10px] text-slate-400 shrink-0">
                                                    {{ $latestMsg?->created_at?->diffForHumans(null, true) ?? '' }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                                {{ $latestMsg ? Str::limit($latestMsg->body ?: 'Sent an attachment', 40) : 'No messages yet' }}
                                            </p>
                                        </div>
                                        @if($isUnread)
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 mt-1.5"></span>
                                        @endif
                                    </button>
                                @empty
                                    <div class="p-6 text-center text-slate-400">
                                        <i class="fa-regular fa-comment-dots text-2xl mb-2 text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-xs">No conversations yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ─── NOTIFICATION ICON WITH UNREAD COUNT ─── --}}
                    <div class="relative" id="notificationsDropdownContainer">
                        <button type="button"
                                id="notificationsDropdownBtn"
                                onclick="toggleNotificationsDropdown()"
                                title="Notifications"
                                class="relative p-2.5 rounded-xl {{ $isFindRide ? 'text-slate-300 hover:text-white hover:bg-slate-800/80' : 'text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-colors focus:outline-none">
                            <i class="fa-solid fa-bell text-base"></i>
                            <span id="navbarUnreadNotifBadge" class="{{ $unreadNotifCount > 0 ? '' : 'hidden' }} absolute top-1 right-1 px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-black leading-tight">
                                {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
                            </span>
                        </button>

                        {{-- Notifications Dropdown Menu --}}
                        <div id="notificationsDropdownMenu"
                             class="hidden absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 shadow-2xl py-2 z-50 transition-all text-xs">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-sm text-slate-900 dark:text-white">Notifications</span>
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 text-[10px] font-bold">
                                        {{ $unreadNotifCount }} new
                                    </span>
                                </div>
                                @if($unreadNotifCount > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                            Mark all read
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                                @forelse($navbarNotifications as $notif)
                                    <div class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors flex items-start gap-3 {{ !$notif->is_read ? 'bg-slate-50/80 dark:bg-slate-800/40' : '' }}">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                            <i class="fa-solid fa-bell"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-slate-800 dark:text-slate-200 leading-snug">
                                                {{ $notif->message }}
                                            </p>
                                            <span class="text-[10px] text-slate-400 mt-1 block">
                                                {{ $notif->created_at?->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-slate-400">
                                        <i class="fa-regular fa-bell-slash text-2xl mb-2 text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-xs">No notifications right now.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-800 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    View all notifications →
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ─── PROFILE DROPDOWN (Passenger / Driver / Admin) ─── --}}
                    <div class="relative pl-2 border-l {{ $isFindRide ? 'border-slate-800' : 'border-slate-200 dark:border-slate-800' }}" id="profileDropdownContainer">
                        <button type="button"
                                id="profileDropdownBtn"
                                onclick="toggleProfileDropdown()"
                                class="flex items-center gap-2.5 p-1.5 pr-3 rounded-full {{ $isFindRide ? 'hover:bg-slate-800/80 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-100' }} transition-colors focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-slate-900 dark:bg-emerald-950 text-white dark:text-emerald-300 flex items-center justify-center text-xs font-black shadow-sm shrink-0 border border-slate-700/60">
                                {{ collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') ?: 'RC' }}
                            </div>
                            <div class="text-left leading-tight hidden lg:block">
                                <div class="text-xs font-extrabold {{ $isFindRide ? 'text-white' : 'text-slate-800 dark:text-slate-100' }} max-w-[120px] truncate">
                                    {{ $user->name }}
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" id="profileDropdownChevron"></i>
                        </button>

                        {{-- Profile Dropdown Menu --}}
                        <div id="profileDropdownMenu"
                             class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 shadow-xl py-2 z-50 transition-all text-xs">
                            
                            {{-- Header User Info --}}
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ $user->name }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                                <span class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $user->user_type == 1 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($user->user_type == 2 ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                                    @if($user->user_type == 1) Platform Admin
                                    @elseif($user->user_type == 2) Verified Driver
                                    @else Passenger Account
                                    @endif
                                </span>
                            </div>

                            {{-- Menu Items According to Role --}}
                            <div class="py-1">
                                @if($user->user_type == 2)
                                    {{-- Driver Profile Dropdown Links --}}
                                    <a href="{{ route('driver.profile') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-user text-slate-400 w-4 text-center"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="{{ route('driver.profile') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-user-pen text-slate-400 w-4 text-center"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                    <a href="{{ route('driver.settings') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-gear text-slate-400 w-4 text-center"></i>
                                        <span>Settings</span>
                                    </a>
                                @elseif($user->user_type == 1)
                                    {{-- Admin Profile Dropdown Links --}}
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-gauge text-slate-400 w-4 text-center"></i>
                                        <span>Admin Dashboard</span>
                                    </a>
                                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-user text-slate-400 w-4 text-center"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-gear text-slate-400 w-4 text-center"></i>
                                        <span>Settings</span>
                                    </a>
                                @else
                                    {{-- Passenger Profile Dropdown Links --}}
                                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-user text-slate-400 w-4 text-center"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-user-pen text-slate-400 w-4 text-center"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="flex items-center gap-2.5 px-4 py-2 font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                        <i class="fa-solid fa-gear text-slate-400 w-4 text-center"></i>
                                        <span>Settings</span>
                                    </a>
                                @endif
                            </div>

                            {{-- Logout --}}
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
                    {{-- Guest Buttons: Login & Register Primary --}}
                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-full border {{ $isFindRide ? 'border-[#1e2d44] bg-[#0c1626] text-slate-200 hover:border-emerald-500 hover:text-white' : 'border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-600 dark:hover:border-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-400' }} text-xs font-bold transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full {{ $isFindRide ? 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black' : 'bg-emerald-600 hover:bg-emerald-700 text-white font-bold' }} text-xs shadow-md shadow-emerald-600/20 transition-all hover:shadow-lg hover:-translate-y-0.5">
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

                @auth
                    {{-- Mobile Message Icon Button --}}
                    <button type="button"
                            onclick="toggleMessagesInbox()"
                            class="relative p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 focus:outline-none"
                            title="Messages">
                        <i class="fa-solid fa-comments text-base"></i>
                        @if($unreadMessageCount > 0)
                            <span class="absolute top-1 right-1 px-1.5 py-0.2 rounded-full bg-emerald-500 text-white text-[9px] font-black">
                                {{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}
                            </span>
                        @endif
                    </button>
                @endauth

                <button id="mobileMenuBtn" type="button" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-3 pb-6 space-y-3 transition-colors">
        {{-- Home (Common) --}}
        <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ $isHome ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Home</a>

        @guest
            <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ $isFindRide ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Find a Ride</a>
            <a href="{{ url('/#about') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">About</a>
        @endguest

        @auth
            @if($user->user_type == 0)
                {{-- Passenger Links --}}
                <a href="{{ url('/trips') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ $isFindRide ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Find a Ride</a>
                <a href="{{ route('activity.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ $isActivity ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">My Activity</a>
            @elseif($user->user_type == 2)
                {{-- Driver Links --}}
                <a href="{{ route('driver.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ $isDriverDashboard ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Dashboard</a>
                <a href="{{ route('driver.passenger-requests') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ $isPassengerRequests ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Passenger Requests</a>
            @endif
        @endauth

        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
            @auth
                <div class="px-3 py-1 text-xs text-slate-500 dark:text-slate-400 font-bold flex items-center justify-between">
                    <span>Logged in: {{ $user->name }}</span>
                    <span class="text-emerald-600 dark:text-emerald-400 uppercase font-black text-[10px]">
                        @if($user->user_type == 1) Admin @elseif($user->user_type == 2) Driver @else Passenger @endif
                    </span>
                </div>
                @if($user->user_type == 2)
                    <a href="{{ route('driver.profile') }}" class="block px-3 py-2 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">My Profile</a>
                    <a href="{{ route('driver.settings') }}" class="block px-3 py-2 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Settings</a>
                @else
                    <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">My Profile</a>
                    <a href="{{ route('profile.settings') }}" class="block px-3 py-2 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Settings</a>
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

{{-- ─── IN-PAGE CHAT DRAWER / OVERLAY (Opens seamlessly without leaving the page) ─── --}}
@auth
<div id="inPageChatDrawerOverlay" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden transition-opacity" style="display: none;" onclick="closeInPageChat()"></div>
<div id="inPageChatDrawer" class="fixed top-0 right-0 h-full bg-white dark:bg-[#0f172a] border-l border-slate-200 dark:border-slate-800 shadow-2xl z-50 flex flex-col hidden" style="display: none; width: 100%; max-width: 440px;">
    {{-- Drawer Header --}}
    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/80">
        <div class="flex items-center gap-3 min-w-0">
            <div id="chatDrawerAvatar" class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                RC
            </div>
            <div class="min-w-0">
                <h3 id="chatDrawerTitle" class="font-extrabold text-sm text-slate-900 dark:text-white truncate">Conversation</h3>
                <span id="chatDrawerStatus" class="inline-block text-[10px] font-bold text-emerald-600 dark:text-emerald-400 capitalize">Online</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a id="chatDrawerFullViewLink" href="{{ route('messages.index') }}" title="Open full chat page" class="p-2 text-slate-400 hover:text-emerald-500 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors text-xs">
                <i class="fa-solid fa-up-right-from-square"></i>
            </a>
            <button type="button" onclick="closeInPageChat()" class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    {{-- Messages Scroll Area --}}
    <div id="chatDrawerMessages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-white dark:bg-[#0b1120]">
        <div class="text-center py-8 text-slate-400 text-xs">
            <i class="fa-solid fa-spinner fa-spin text-base mb-2"></i>
            <p>Loading messages...</p>
        </div>
    </div>

    {{-- Drawer Input Area --}}
    <form id="chatDrawerForm" onsubmit="sendChatDrawerMessage(event)" class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90 flex items-center gap-2">
        <input type="text"
               id="chatDrawerInput"
               placeholder="Type a message..."
               autocomplete="off"
               class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        <button type="submit"
                id="chatDrawerSendBtn"
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>
@endauth

{{-- ─── JAVASCRIPT FOR DROPDOWNS & CHAT OVERLAY ─── --}}
<script>
let currentActiveConversationId = null;
let chatDrawerPollInterval = null;

function toggleProfileDropdown() {
    const menu = document.getElementById('profileDropdownMenu');
    const chevron = document.getElementById('profileDropdownChevron');
    closeAllNavbarDropdowns('profile');
    if (!menu) return;
    menu.classList.toggle('hidden');
    if (chevron) chevron.classList.toggle('rotate-180');
}

function toggleMessagesInbox() {
    const panel = document.getElementById('messagesInboxPanel');
    closeAllNavbarDropdowns('messages');
    if (!panel) return;
    panel.classList.toggle('hidden');
}

function toggleNotificationsDropdown() {
    const menu = document.getElementById('notificationsDropdownMenu');
    closeAllNavbarDropdowns('notifications');
    if (!menu) return;
    menu.classList.toggle('hidden');
}

function closeAllNavbarDropdowns(except = '') {
    if (except !== 'profile') {
        const p = document.getElementById('profileDropdownMenu');
        if (p) p.classList.add('hidden');
        const c = document.getElementById('profileDropdownChevron');
        if (c) c.classList.remove('rotate-180');
    }
    if (except !== 'messages') {
        const m = document.getElementById('messagesInboxPanel');
        if (m) m.classList.add('hidden');
    }
    if (except !== 'notifications') {
        const n = document.getElementById('notificationsDropdownMenu');
        if (n) n.classList.add('hidden');
    }
}

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    const profileContainer = document.getElementById('profileDropdownContainer');
    const messagesContainer = document.getElementById('messagesInboxContainer');
    const notifContainer = document.getElementById('notificationsDropdownContainer');

    if (profileContainer && !profileContainer.contains(e.target) &&
        messagesContainer && !messagesContainer.contains(e.target) &&
        notifContainer && !notifContainer.contains(e.target)) {
        closeAllNavbarDropdowns();
    }
});

// ─── In-Page Chat Overlay Functions ───
function openInPageChat(conversationId, title = 'Conversation') {
    currentActiveConversationId = conversationId;
    closeAllNavbarDropdowns();

    const overlay = document.getElementById('inPageChatDrawerOverlay');
    const drawer  = document.getElementById('inPageChatDrawer');
    const titleEl = document.getElementById('chatDrawerTitle');
    const avatarEl = document.getElementById('chatDrawerAvatar');
    const fullLink = document.getElementById('chatDrawerFullViewLink');

    if (overlay && drawer) {
        overlay.classList.remove('hidden');
        overlay.style.display = 'block';
        drawer.classList.remove('hidden');
        drawer.style.display = 'flex';
    }

    if (titleEl) titleEl.innerText = title;
    if (avatarEl) avatarEl.innerText = title.substring(0, 2).toUpperCase();
    if (fullLink) fullLink.href = `/messages/${conversationId}`;

    fetchDrawerMessages(conversationId);

    if (chatDrawerPollInterval) clearInterval(chatDrawerPollInterval);
    chatDrawerPollInterval = setInterval(() => {
        if (currentActiveConversationId) {
            fetchDrawerMessages(currentActiveConversationId, true);
        }
    }, 3500);
}

function closeInPageChat() {
    currentActiveConversationId = null;
    if (chatDrawerPollInterval) clearInterval(chatDrawerPollInterval);

    const overlay = document.getElementById('inPageChatDrawerOverlay');
    const drawer  = document.getElementById('inPageChatDrawer');
    if (overlay && drawer) {
        overlay.classList.add('hidden');
        overlay.style.display = 'none';
        drawer.classList.add('hidden');
        drawer.style.display = 'none';
    }
}

function fetchDrawerMessages(conversationId, isBackgroundPoll = false) {
    const container = document.getElementById('chatDrawerMessages');
    if (!container) return;

    fetch(`/messages/${conversationId}/fetch`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(r => r.json())
    .then(data => {
        const messages = data.messages || [];
        const statusEl = document.getElementById('chatDrawerStatus');
        if (statusEl && data.arrangement_status) {
            statusEl.innerText = 'Arrangement: ' + data.arrangement_status;
        }

        if (messages.length === 0) {
            container.innerHTML = `
                <div class="text-center py-10 text-slate-400 text-xs">
                    <i class="fa-regular fa-comment-dots text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                    <p>No messages in this conversation yet.</p>
                    <p class="text-[10px] text-slate-400 mt-1">Send a greeting below!</p>
                </div>
            `;
            return;
        }

        const authUserId = {{ auth()->id() ?? 0 }};
        let html = '';
        messages.forEach(msg => {
            const isMe = msg.sender_id === authUserId;
            const bubbleBg = isMe
                ? 'bg-emerald-600 text-white rounded-br-xs'
                : 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded-bl-xs border border-slate-200 dark:border-slate-700/60';
            const align = isMe ? 'justify-end' : 'justify-start';

            html += `
                <div class="flex ${align}">
                    <div class="max-w-[85%] px-3.5 py-2.5 rounded-2xl ${bubbleBg} shadow-xs text-xs space-y-1">
                        ${!isMe && msg.sender ? `<div class="font-extrabold text-[10px] text-emerald-600 dark:text-emerald-400">${msg.sender.name}</div>` : ''}
                        <div class="break-words">${escapeHtml(msg.body || '')}</div>
                        ${msg.attachment_url ? `
                            <div class="pt-1">
                                <a href="${msg.attachment_url}" target="_blank" class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-black/20 hover:bg-black/30 text-[10px] font-bold underline">
                                    <i class="fa-solid fa-paperclip"></i> Attachment
                                </a>
                            </div>
                        ` : ''}
                        <div class="text-[9px] text-right ${isMe ? 'text-emerald-100' : 'text-slate-400'}">
                            ${msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        if (!isBackgroundPoll) {
            container.scrollTop = container.scrollHeight;
        }
    })
    .catch(() => {});
}

function sendChatDrawerMessage(e) {
    e.preventDefault();
    if (!currentActiveConversationId) return;

    const input = document.getElementById('chatDrawerInput');
    const text = input ? input.value.trim() : '';
    if (!text) return;

    input.value = '';

    fetch(`/messages/${currentActiveConversationId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ body: text })
    })
    .then(r => r.json())
    .then(() => {
        fetchDrawerMessages(currentActiveConversationId);
    })
    .catch(() => {});
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.innerText = str;
    return div.innerHTML;
}

// Global hook to launch chat drawer from anywhere (e.g. Message buttons on cards)
window.openRouteConnectChat = openInPageChat;
</script>
