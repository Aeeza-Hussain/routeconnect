{{-- RouteConnect Dark / Light Mode Switcher --}}
<button type="button"
        onclick="toggleTheme()"
        aria-label="Toggle light and dark mode"
        title="Toggle Theme (Light / Dark)"
        class="theme-toggle-btn inline-flex items-center justify-center w-9 h-9 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-amber-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all focus:outline-none cursor-pointer">
    {{-- Moon for Light Mode --}}
    <i class="fa-solid fa-moon text-sm dark:hidden"></i>
    {{-- Sun for Dark Mode --}}
    <i class="fa-solid fa-sun text-sm hidden dark:inline-block text-amber-400"></i>
</button>

<script>
    if (typeof window.toggleTheme !== 'function') {
        window.toggleTheme = function() {
            var html = document.documentElement;
            var isDark = html.classList.toggle('dark');
            try {
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            } catch (e) {}
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: isDark ? 'dark' : 'light' } }));
        };
    }
</script>
