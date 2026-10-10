<!-- Admin Top Header Navigation -->
<header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800/80 px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs transition-colors duration-200">
    
    <!-- Left Toggle & Title -->
    <div class="flex items-center gap-4">
        <button id="adminSidebarToggle" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors lg:hidden">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white leading-tight">RouteConnect Admin</h1>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold">System Administration & Operations Control</p>
        </div>
    </div>

    <!-- Right Side Profile, Theme Switcher & Actions -->
    <div class="flex items-center gap-4">
        
        <!-- Public Website Link -->
        <a href="{{ url('/') }}" target="_blank" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors border border-transparent dark:border-slate-700">
            <i class="fa-solid fa-globe text-slate-500 dark:text-slate-400"></i>
            <span>View Website</span>
        </a>

        <!-- Dark / Light Theme Toggle -->
        @include('partials.theme_toggle')

        <!-- Admin Profile Dropdown Badge -->
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-800">
            <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-extrabold flex items-center justify-center text-xs border border-emerald-300 dark:border-emerald-700">
                AD
            </div>
            <div class="text-left leading-tight hidden sm:block">
                <div class="text-xs font-extrabold text-slate-900 dark:text-slate-100">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase">System Admin</div>
            </div>

            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Logout" class="p-2 rounded-xl text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </form>
        </div>

    </div>

</header>
