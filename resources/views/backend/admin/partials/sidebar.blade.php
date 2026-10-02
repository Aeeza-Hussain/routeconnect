{{-- ──────────── Admin Sidebar Navigation ──────────── --}}
<aside id="adminSidebar"
       class="fixed lg:static inset-y-0 left-0 z-50 w-64 h-screen bg-[#0B1929] text-slate-300 flex flex-col shrink-0 border-r border-slate-800/80 shadow-2xl transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">

    {{-- ── Logo Header (Pinned Top) ── --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/70 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-shield-halved text-sm"></i>
            </div>
            <span class="font-extrabold text-xl tracking-tight text-white">
                Route<span class="text-emerald-400">Admin</span>
            </span>
        </a>

        {{-- Mobile close button --}}
        <button type="button" onclick="closeSidebar()" class="lg:hidden p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- ── Navigation Items (Scrolls Internally If Needed) ── --}}
    <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">

        {{-- Section label: Overview --}}
        <div class="px-3 pt-1 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
            Overview
        </div>

        {{-- 1. Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-gauge text-sm w-4 text-center"></i>
            <span>Dashboard</span>
        </a>

        {{-- Section label: Driver Management --}}
        <div class="px-3 pt-3.5 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
            Driver Management
        </div>

        {{-- 2. Driver Applications --}}
        <a href="{{ route('admin.drivers.applications') }}"
           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.drivers.applications*') || request()->routeIs('admin.drivers.show*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-id-card text-sm w-4 text-center"></i>
                <span>Driver Applications</span>
            </div>
            @php
                $pendingCount = \App\Models\User::where('user_type', 2)->where('driver_status', 'pending')->count();
            @endphp
            @if ($pendingCount > 0)
                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-[10px] font-extrabold border border-amber-500/30">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>

        {{-- 3. Approved Drivers --}}
        <a href="{{ route('admin.drivers.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.drivers.index*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-user-check text-sm w-4 text-center"></i>
            <span>Approved Drivers</span>
        </a>

        {{-- Section label: Platform --}}
        <div class="px-3 pt-3.5 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
            Platform Management
        </div>

        {{-- 4. Users --}}
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.users.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-users text-sm w-4 text-center"></i>
            <span>Users</span>
        </a>

        {{-- 5. Vehicles --}}
        <a href="{{ route('admin.vehicles.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.vehicles.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-van-shuttle text-sm w-4 text-center"></i>
            <span>Vehicles</span>
        </a>

        {{-- 6. Routes --}}
        <a href="{{ route('admin.routes.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.routes.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-route text-sm w-4 text-center"></i>
            <span>Routes</span>
        </a>

        {{-- 7. Stops --}}
        <a href="{{ route('admin.stops.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.stops.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-location-dot text-sm w-4 text-center"></i>
            <span>Stops</span>
        </a>

        {{-- 8. Trips --}}
        <a href="{{ route('admin.trips.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.trips.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-bus text-sm w-4 text-center"></i>
            <span>Trips</span>
        </a>

        {{-- 9. Bookings --}}
        <a href="{{ route('admin.bookings.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.bookings.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-ticket text-sm w-4 text-center"></i>
            <span>Bookings</span>
        </a>

        {{-- Section label: System --}}
        <div class="px-3 pt-3.5 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
            System
        </div>

        {{-- 10. Settings --}}
        <a href="{{ route('admin.settings.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.settings.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-gear text-sm w-4 text-center"></i>
            <span>Settings</span>
        </a>

    </nav>

    {{-- ── User Info + Logout (Pinned Bottom) ── --}}
    <div class="p-4 border-t border-slate-800/70 space-y-2.5 shrink-0">
        {{-- Admin info pill --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-slate-800/50 rounded-xl">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="text-[10px] text-emerald-400 font-bold">Administrator</div>
            </div>
        </div>

        {{-- Logout button --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-900/40 text-slate-300 hover:text-rose-400 text-xs font-bold transition-all border border-slate-700/80 hover:border-rose-800/50">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
