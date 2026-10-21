<!-- RouteConnect Dedicated Appearance & Theme Settings Card -->
<div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors duration-200">
    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-800/40 flex items-center justify-between">
        <div>
            <h3 class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-palette"></i>
                </span>
                Appearance & Theme
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Customize your visual experience across RouteConnect on all screens and devices.
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-[11px] font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Live Preview
        </span>
    </div>

    <div class="p-6 sm:p-8">
        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-4">
            Select Interface Theme
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- 1. LIGHT MODE CARD -->
            <button type="button"
                    data-theme-option="light"
                    onclick="window.RouteConnectTheme.setTheme('light')"
                    class="theme-card-option text-left p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 hover:border-emerald-500/50 transition-all duration-200 relative group cursor-pointer focus:outline-none">
                <!-- Preview Mockup -->
                <div class="h-24 rounded-xl bg-slate-100 border border-slate-200 p-2.5 mb-3.5 flex flex-col justify-between overflow-hidden shadow-inner">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-2.5 bg-emerald-600 rounded-sm"></div>
                        <div class="flex gap-1">
                            <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                            <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <div class="w-full h-3 bg-white rounded border border-slate-200 flex items-center px-1.5">
                            <div class="w-3/4 h-1.5 bg-slate-300 rounded-sm"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5">
                            <div class="h-4 bg-white rounded border border-slate-200"></div>
                            <div class="h-4 bg-emerald-500/20 rounded border border-emerald-300"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-sun"></i>
                        </span>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900 dark:text-white">Light Mode</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Crisp, clean & bright</div>
                        </div>
                    </div>
                    <i data-theme-check class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg hidden"></i>
                </div>
            </button>

            <!-- 2. DARK MODE CARD -->
            <button type="button"
                    data-theme-option="dark"
                    onclick="window.RouteConnectTheme.setTheme('dark')"
                    class="theme-card-option text-left p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 hover:border-emerald-500/50 transition-all duration-200 relative group cursor-pointer focus:outline-none">
                <!-- Preview Mockup -->
                <div class="h-24 rounded-xl bg-[#0b1120] border border-slate-800 p-2.5 mb-3.5 flex flex-col justify-between overflow-hidden shadow-inner">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-2.5 bg-emerald-500 rounded-sm"></div>
                        <div class="flex gap-1">
                            <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                            <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <div class="w-full h-3 bg-slate-900 rounded border border-slate-800 flex items-center px-1.5">
                            <div class="w-3/4 h-1.5 bg-slate-700 rounded-sm"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5">
                            <div class="h-4 bg-slate-900 rounded border border-slate-800"></div>
                            <div class="h-4 bg-emerald-950/80 rounded border border-emerald-700/60"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-moon"></i>
                        </span>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900 dark:text-white">Dark Mode</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Deep slate & comfortable</div>
                        </div>
                    </div>
                    <i data-theme-check class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg hidden"></i>
                </div>
            </button>

            <!-- 3. SYSTEM DEFAULT CARD -->
            <button type="button"
                    data-theme-option="system"
                    onclick="window.RouteConnectTheme.setTheme('system')"
                    class="theme-card-option text-left p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 hover:border-emerald-500/50 transition-all duration-200 relative group cursor-pointer focus:outline-none">
                <!-- Preview Mockup (Split) -->
                <div class="h-24 rounded-xl border border-slate-200 dark:border-slate-700 mb-3.5 flex overflow-hidden shadow-inner">
                    <!-- Left side: light -->
                    <div class="w-1/2 bg-slate-100 p-2 flex flex-col justify-between border-r border-slate-300">
                        <div class="w-8 h-2 bg-emerald-600 rounded-sm"></div>
                        <div class="w-full h-3 bg-white rounded border border-slate-200"></div>
                        <div class="h-3 bg-slate-200 rounded"></div>
                    </div>
                    <!-- Right side: dark -->
                    <div class="w-1/2 bg-[#0b1120] p-2 flex flex-col justify-between">
                        <div class="w-8 h-2 bg-emerald-500 rounded-sm ml-auto"></div>
                        <div class="w-full h-3 bg-slate-900 rounded border border-slate-800"></div>
                        <div class="h-3 bg-slate-800 rounded"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-desktop"></i>
                        </span>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900 dark:text-white">System Default</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Syncs with OS preference</div>
                        </div>
                    </div>
                    <i data-theme-check class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg hidden"></i>
                </div>
            </button>

        </div>

        <div class="mt-5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 flex items-start gap-3 text-xs text-slate-600 dark:text-slate-400">
            <i class="fa-solid fa-circle-info text-emerald-500 text-sm mt-0.5 shrink-0"></i>
            <div>
                <strong class="text-slate-800 dark:text-slate-200">Theme persistence:</strong> Your preference is automatically stored in your browser and applies instantly across all passenger, driver, and admin views without needing a page refresh or restart.
            </div>
        </div>
    </div>
</div>
