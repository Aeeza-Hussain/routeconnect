<!-- Admin Top Header Navigation -->
<header class="h-20 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
    
    <!-- Left Toggle & Title -->
    <div class="flex items-center gap-4">
        <button id="adminSidebarToggle" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors lg:hidden">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <div>
            <h1 class="text-lg font-extrabold text-slate-900 leading-tight">RouteConnect Admin</h1>
            <p class="text-[11px] text-slate-500 font-semibold">System Administration & Operations Control</p>
        </div>
    </div>

    <!-- Right Side Profile & Actions -->
    <div class="flex items-center gap-4">
        
        <!-- Public Website Link -->
        <a href="{{ url('/') }}" target="_blank" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
            <i class="fa-solid fa-globe text-slate-500"></i>
            <span>View Website</span>
        </a>

        <!-- Admin Profile Dropdown Badge -->
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-xs border border-emerald-300">
                AD
            </div>
            <div class="text-left leading-tight hidden sm:block">
                <div class="text-xs font-extrabold text-slate-900">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="text-[10px] text-emerald-600 font-bold uppercase">System Admin</div>
            </div>

            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Logout" class="p-2 rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </form>
        </div>

    </div>

</header>
