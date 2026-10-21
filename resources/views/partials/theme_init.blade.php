<!-- RouteConnect Theme Initializer (FOUC Prevention & Global Theme Engine) -->
<script>
    (function () {
        'use strict';

        const STORAGE_KEY = 'routeconnect_theme';

        function getStoredTheme() {
            try {
                return localStorage.getItem(STORAGE_KEY) || 'system';
            } catch (e) {
                return 'system';
            }
        }

        function prefersDark() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        function resolveIsDark(theme) {
            if (theme === 'dark') return true;
            if (theme === 'light') return false;
            return prefersDark();
        }

        function applyTheme(theme, animate = false) {
            const isDark = resolveIsDark(theme);
            const root = document.documentElement;

            if (animate) {
                root.classList.add('theme-transitioning');
            }

            if (isDark) {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }

            root.setAttribute('data-theme', theme);

            // Update meta theme-color for mobile browsers & PWA
            const metaTheme = document.querySelector('meta[name="theme-color"]');
            if (metaTheme) {
                metaTheme.setAttribute('content', isDark ? '#090e17' : '#059669');
            }

            // Sync all theme picker buttons / UI elements on page
            updateThemePickersUI(theme, isDark);

            if (animate) {
                setTimeout(() => {
                    root.classList.remove('theme-transitioning');
                }, 300);
            }

            // Dispatch global event for custom components
            window.dispatchEvent(new CustomEvent('routeconnect:theme-changed', {
                detail: { theme, isDark }
            }));
        }

        function updateThemePickersUI(theme, isDark) {
            // Update quick toggle icon and text
            const icons = document.querySelectorAll('[data-theme-current-icon]');
            icons.forEach(el => {
                if (theme === 'light') {
                    el.className = 'fa-solid fa-sun text-amber-500';
                } else if (theme === 'dark') {
                    el.className = 'fa-solid fa-moon text-indigo-400';
                } else {
                    el.className = 'fa-solid fa-circle-half-stroke text-emerald-500';
                }
            });

            const labels = document.querySelectorAll('[data-theme-current-label]');
            labels.forEach(el => {
                if (theme === 'light') el.textContent = 'Light';
                else if (theme === 'dark') el.textContent = 'Dark';
                else el.textContent = 'System (' + (isDark ? 'Dark' : 'Light') + ')';
            });

            // Update active state on option buttons
            const optionBtns = document.querySelectorAll('[data-theme-option]');
            optionBtns.forEach(btn => {
                const opt = btn.getAttribute('data-theme-option');
                const isSelected = opt === theme;
                btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');

                // Toggle active borders and checkmarks
                const check = btn.querySelector('[data-theme-check]');
                if (check) {
                    if (isSelected) {
                        check.classList.remove('hidden');
                    } else {
                        check.classList.add('hidden');
                    }
                }

                if (btn.classList.contains('theme-card-option')) {
                    if (isSelected) {
                        btn.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500');
                    } else {
                        btn.classList.remove('ring-2', 'ring-emerald-500', 'border-emerald-500');
                    }
                }
            });
        }

        // Global Theme API
        window.RouteConnectTheme = {
            getTheme: function () {
                return getStoredTheme();
            },
            isDark: function () {
                return resolveIsDark(getStoredTheme());
            },
            setTheme: function (theme) {
                if (!['light', 'dark', 'system'].includes(theme)) return;
                try {
                    localStorage.setItem(STORAGE_KEY, theme);
                } catch (e) {}
                applyTheme(theme, true);
            },
            toggle: function () {
                const current = getStoredTheme();
                const next = current === 'light' ? 'dark' : (current === 'dark' ? 'system' : 'light');
                this.setTheme(next);
            }
        };

        // Immediate application before DOM paint
        const initialTheme = getStoredTheme();
        applyTheme(initialTheme, false);

        // Listen for OS system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
                if (getStoredTheme() === 'system') {
                    applyTheme('system', true);
                }
            });
        }

        // Sync UI on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function () {
            updateThemePickersUI(getStoredTheme(), resolveIsDark(getStoredTheme()));
        });
    })();
</script>
