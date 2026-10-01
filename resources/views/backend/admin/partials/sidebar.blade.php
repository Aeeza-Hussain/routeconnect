{{-- ──────────── Admin Sidebar Navigation ──────────── --}}
<aside id="adminSidebar" class="w-64 bg-[#0B1929] text-slate-300 min-h-screen flex flex-col shrink-0 border-r border-slate-800/80 shadow-xl">

    {{-- ── Logo Header ── --}}
    <div class="h-20 flex items-center px-6 border-b border-slate-800/70">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg">
                <i class="fa-solid fa-shield-halved text-sm"></i>
            </div>
            <span class="font-extrabold text-xl tracking-tight text-white">
                Route<span class="text-emerald-400">Admin</span>
            </span>
        </a>
    </div>

    {{-- ── Navigation Items ── --}}
    <nav class="flex-1 py-5 px-3 space-y-0.5 overflow-y-auto">

        {{-- Section label: Main --}}
        <div class="px-3 pt-1 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-slate-600">
            Main
        </div>

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fa-solid fa-gauge text-sm w-4 text-center"></i>
            <span>Dashboard</span>
        </a>

        {{-- Section label: Driver Management --}}
        <div class="px-3 pt-4 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-slate-600">
            Driver Management
        </div>

        {{-- Driver Applications --}}
        <a href="{{ route('admin.drivers.applications') }}"
           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.drivers.applications*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-id-card text-sm w-4 text-center"></i>
                <span>Applications</span>
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

        {{-- Approved Drivers --}}
        <a href="{{ route('admin.drivers.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('admin.drivers.index') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fa-solid fa-user-check text-sm w-4 text-center"></i>
            <span>Approved Drivers</span>
        </a>

        {{-- Section label: Platform --}}
        <div class="px-3 pt-4 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-slate-600">
            Platform
        </div>

        {{-- Users --}}
        <a href="{{ url('/admin/users') }}"
           onclick="event.preventDefault(); alert('Users CRUD will be built in a future step.');"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-800/50 hover:text-slate-300 transition-all cursor-not-allowed opacity-60">
            <i class="fa-solid fa-users text-sm w-4 text-center"></i>
            <span>Users</span>
            <span class="ml-auto text-[10px] text-slate-600 font-bold">Soon</span>
        </a>

        {{-- Vehicles --}}
        <a href="{{ url('/admin/vehicles') }}"
           onclick="event.preventDefault(); alert('Vehicles CRUD will be built in a future step.');"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-800/50 hover:text-slate-300 transition-all cursor-not-allowed opacity-60">
            <i class="fa-solid fa-van-shuttle text-sm w-4 text-center"></i>
            <span>Vehicles</span>
            <span class="ml-auto text-[10px] text-slate-600 font-bold">Soon</span>
        </a>

        {{-- Routes --}}
        <a href="{{ url('/admin/routes') }}"
           onclick="event.preventDefault(); alert('Routes CRUD will be built in a future step.');"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-800/50 hover:text-slate-300 transition-all cursor-not-allowed opacity-60">
            <i class="fa-solid fa-route text-sm w-4 text-center"></i>
            <span>Routes</span>
            <span class="ml-auto text-[10px] text-slate-600 font-bold">Soon</span>
        </a>

        {{-- Stops --}}
        <a href="{{ url('/admin/stops') }}"
           onclick="event.preventDefault(); alert('Stops CRUD will be built in a future step.');"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-800/50 hover:text-slate-300 transition-all cursor-not-allowed opacity-60">
            <i class="fa-solid fa-location-dot text-sm w-4 text-center"></i>
            <span>Stops</span>
            <span class="ml-auto text-[10px] text-slate-600 font-bold">Soon</span>
        </a>

        {{-- Trips --}}
        <a href="{{ url('/admin/trips') }}"
           onclick="event.preventDefault(); alert('Trips CRUD will be built in a future step.');"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-800/50 hover:text-slate-300 transition-all cursor-not-allowed opacity-60">
            <i class="fa-solid fa-bus text-sm w-4 text-center"></i>
            <span>Trips</span>
            <span class="ml-auto text-[10px] text-slate-600 font-bold">Soon</span>
        </a>

        {{-- Bookings --}}
        <a href="{{ url('/admin/bookings') }}"
           onclick="event.preventDefault(); alert('Bookings CRUD will be built in a future step.');"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-800/50 hover:text-slate-300 transition-all cursor-not-allowed opacity-60">
            <i class="fa-solid fa-ticket text-sm w-4 text-center"></i>
            <span>Bookings</span>
            <span class="ml-auto text-[10px] text-slate-600 font-bold">Soon</span>
        </a>

    </nav>

    {{-- ── User Info + Logout at Bottom ── --}}
    <div class="p-4 border-t border-slate-800/70 space-y-3">
        {{-- Admin info pill --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-slate-800/50 rounded-xl">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xs">
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
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-rose-900/40 text-slate-300 hover:text-rose-400 text-xs font-bold transition-all border border-slate-700 hover:border-rose-800/50">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
