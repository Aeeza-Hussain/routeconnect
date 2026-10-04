{{-- ──────────── Driver Sidebar Navigation ──────────── --}}
<aside id="driverSidebar"
       class="fixed lg:static inset-y-0 left-0 z-50 w-64 h-screen bg-[#070E1E] text-slate-300 flex flex-col shrink-0 border-r border-slate-800/80 shadow-2xl transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">

    {{-- ── Logo Header (Pinned Top) ── --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/70 shrink-0">
        <a href="{{ route('driver.dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-teal-500/20">
                <i class="fa-solid fa-car-side text-sm"></i>
            </div>
            <span class="font-extrabold text-xl tracking-tight text-white">
                Route<span class="text-teal-400">Driver</span>
            </span>
        </a>

        {{-- Mobile close button --}}
        <button type="button" onclick="closeDriverSidebar()" class="lg:hidden p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- ── Navigation Items (Scrolls Internally If Needed) ── --}}
    <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">

        {{-- Section label: Overview --}}
        <div class="px-3 pt-1 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
            Operations
        </div>

        {{-- 1. Dashboard --}}
        <a href="{{ route('driver.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('driver.dashboard') ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-gauge text-sm w-4 text-center"></i>
            <span>Dashboard</span>
        </a>

        {{-- 2. Profile --}}
        <a href="{{ route('driver.profile') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('driver.profile*') ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-user text-sm w-4 text-center"></i>
            <span>Profile</span>
        </a>

        {{-- Section label: Fleet & Journeys --}}
        <div class="px-3 pt-3.5 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
            Fleet & Journeys
        </div>

        {{-- 3. Vehicle --}}
        <a href="{{ route('driver.vehicle') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('driver.vehicle*') ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-van-shuttle text-sm w-4 text-center"></i>
            <span>Vehicle</span>
        </a>

        {{-- 4. Trips --}}
        <a href="{{ route('driver.trips') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('driver.trips*') ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-route text-sm w-4 text-center"></i>
            <span>Trips</span>
        </a>

        {{-- 5. Bookings --}}
        <a href="{{ route('driver.bookings') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('driver.bookings*') ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-ticket text-sm w-4 text-center"></i>
            <span>Bookings</span>
        </a>

        {{-- 6. Messages --}}
        <a href="{{ route('driver.messages') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('driver.messages*') ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-comments text-sm w-4 text-center"></i>
            <span>Messages</span>
        </a>

        {{-- Section label: Account --}}
        <div class="px-3 pt-3.5 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
            Account
        </div>

        {{-- 7. Settings --}}
        <a href="{{ route('driver.settings') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all
                  {{ request()->routeIs('driver.settings*') ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-xs' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-gear text-sm w-4 text-center"></i>
            <span>Settings</span>
        </a>

    </nav>

    {{-- ── User Info + Logout (Pinned Bottom) ── --}}
    <div class="p-4 border-t border-slate-800/70 space-y-2.5 shrink-0">
        {{-- Driver info pill --}}
        <div class="flex items-center gap-3 px-3 py-2 bg-slate-900/80 rounded-xl border border-slate-800">
            <div class="w-8 h-8 rounded-lg bg-teal-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Driver' }}</div>
                <div class="text-[10px] text-teal-400 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Approved
                </div>
            </div>
        </div>

        {{-- Logout button --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-rose-950/40 text-slate-400 hover:text-rose-400 text-xs font-bold transition-all border border-slate-800 hover:border-rose-900/50">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
