<!-- Driver Top Header Navigation -->
<header class="h-20 bg-slate-950/80 border-b border-slate-800/80 px-6 flex items-center justify-between sticky top-0 z-30 backdrop-blur-md">

    <!-- Left Toggle & Title -->
    <div class="flex items-center gap-4">
        <button id="driverSidebarToggle" class="p-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors lg:hidden">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <div>
            <h1 class="text-lg font-extrabold text-white leading-tight flex items-center gap-2">
                <span>Driver Operations Console</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-extrabold border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Verified
                </span>
            </h1>
            <p class="text-[11px] text-slate-400 font-medium">RouteConnect Fleet & Trip Management</p>
        </div>
    </div>

    <!-- Right Side Profile & Actions -->
    <div class="flex items-center gap-4">

        <!-- Public Website Link -->
        <a href="{{ url('/') }}" target="_blank" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-globe text-slate-400"></i>
            <span>View Website</span>
        </a>

        <!-- Dark / Light Theme Toggle -->
        @include('partials.theme_toggle')

        <!-- Driver Profile Dropdown Badge -->
        <div class="flex items-center gap-3 pl-3 border-l border-slate-800">
            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 2)) }}
            </div>
            <div class="text-left leading-tight hidden sm:block">
                <div class="text-xs font-extrabold text-white">{{ auth()->user()->name ?? 'Driver' }}</div>
                <div class="text-[10px] text-emerald-400 font-bold uppercase">Commercial Driver</div>
            </div>

            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Logout" class="p-2 rounded-xl text-slate-400 hover:bg-rose-950/40 hover:text-rose-400 transition-colors">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </form>
        </div>

    </div>

</header>
