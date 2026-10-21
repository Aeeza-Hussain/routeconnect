<!-- Quick Theme Switcher Dropdown Component -->
<div class="relative inline-block text-left" id="themeSwitcherDropdownWrapper">
    <button type="button"
            id="themeSwitcherBtn"
            onclick="toggleThemeDropdown(event)"
            class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold border transition-all duration-200
                   bg-white hover:bg-slate-50 text-slate-700 border-slate-200 shadow-xs
                   dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:border-slate-700 dark:shadow-none"
            aria-expanded="false"
            aria-haspopup="true"
            title="Switch Theme (Light / Dark / System)">
        <i data-theme-current-icon class="fa-solid fa-circle-half-stroke text-emerald-500 text-sm"></i>
        <span data-theme-current-label class="hidden sm:inline">Theme</span>
        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dark:text-slate-500 transition-transform duration-200" id="themeChevron"></i>
    </button>

    <!-- Dropdown Menu -->
    <div id="themeDropdownMenu"
         class="hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl z-50 p-1.5 focus:outline-none transition-all duration-150 transform opacity-0 scale-95"
         role="menu"
         aria-orientation="vertical">
        <div class="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
            Appearance
        </div>

        <!-- Light Option -->
        <button type="button"
                data-theme-option="light"
                onclick="window.RouteConnectTheme.setTheme('light'); closeThemeDropdown();"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors
                       text-slate-700 hover:bg-slate-100 hover:text-slate-900
                       dark:text-slate-200 dark:hover:bg-slate-700/70"
                role="menuitem">
            <span class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-sun"></i>
                </span>
                <span>Light</span>
            </span>
            <i data-theme-check class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs hidden"></i>
        </button>

        <!-- Dark Option -->
        <button type="button"
                data-theme-option="dark"
                onclick="window.RouteConnectTheme.setTheme('dark'); closeThemeDropdown();"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors
                       text-slate-700 hover:bg-slate-100 hover:text-slate-900
                       dark:text-slate-200 dark:hover:bg-slate-700/70"
                role="menuitem">
            <span class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-moon"></i>
                </span>
                <span>Dark</span>
            </span>
            <i data-theme-check class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs hidden"></i>
        </button>

        <!-- System Option -->
        <button type="button"
                data-theme-option="system"
                onclick="window.RouteConnectTheme.setTheme('system'); closeThemeDropdown();"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors
                       text-slate-700 hover:bg-slate-100 hover:text-slate-900
                       dark:text-slate-200 dark:hover:bg-slate-700/70"
                role="menuitem">
            <span class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-desktop"></i>
                </span>
                <span>System</span>
            </span>
            <i data-theme-check class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs hidden"></i>
        </button>
    </div>
</div>

<script>
    function toggleThemeDropdown(e) {
        if (e) e.stopPropagation();
        const menu = document.getElementById('themeDropdownMenu');
        const chevron = document.getElementById('themeChevron');
        const btn = document.getElementById('themeSwitcherBtn');
        if (!menu) return;

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            requestAnimationFrame(() => {
                menu.classList.remove('opacity-0', 'scale-95');
                menu.classList.add('opacity-100', 'scale-100');
            });
            if (chevron) chevron.classList.add('rotate-180');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        } else {
            closeThemeDropdown();
        }
    }

    function closeThemeDropdown() {
        const menu = document.getElementById('themeDropdownMenu');
        const chevron = document.getElementById('themeChevron');
        const btn = document.getElementById('themeSwitcherBtn');
        if (!menu || menu.classList.contains('hidden')) return;

        menu.classList.remove('opacity-100', 'scale-100');
        menu.classList.add('opacity-0', 'scale-95');
        if (chevron) chevron.classList.remove('rotate-180');
        if (btn) btn.setAttribute('aria-expanded', 'false');

        setTimeout(() => {
            menu.classList.add('hidden');
        }, 150);
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('themeSwitcherDropdownWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeThemeDropdown();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeThemeDropdown();
        }
    });
</script>
