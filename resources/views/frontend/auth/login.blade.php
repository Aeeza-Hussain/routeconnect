<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Account Login</title>

    <!-- PWA Manifest & App Theme -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Local FontAwesome 6 Icons (Fully Offline) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Local Offline-First Stylesheets -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Google Fonts (With system fallback) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

    <!-- RouteConnect Theme Engine (Zero FOUC) -->
    @include('partials.theme_init')
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-[#090e17] dark:to-[#0f172a] text-slate-800 dark:text-slate-100 antialiased font-sans flex flex-col min-h-screen transition-colors duration-200">

    <!-- ─── Navbar ─── -->
    <nav class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 py-4 px-6 flex items-center justify-between shadow-xs transition-colors">
        <a href="{{ url('/') }}" class="text-xl font-extrabold text-emerald-700 dark:text-emerald-400 tracking-tight flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm shadow-sm">
                <i class="fa-solid fa-location-dot"></i>
            </span>
            <span>Route<span class="text-slate-900 dark:text-white">Connect</span></span>
        </a>
        <div class="flex items-center gap-4 text-sm font-semibold">
            <a href="{{ url('/') }}" class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors hidden sm:inline">Home</a>
            <a href="{{ route('login') }}" class="text-emerald-700 dark:text-emerald-400 font-bold">Login</a>
            <a href="{{ route('register') }}" class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Register</a>
            <div class="pl-2 border-l border-slate-200 dark:border-slate-700">
                @include('partials.theme_switcher')
            </div>
        </div>
    </nav>

    <!-- ─── Login Form ─── -->
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md">

            <!-- Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200/80 dark:border-slate-800 overflow-hidden transition-colors">

                <!-- Card Top Banner -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-7">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center mb-4 shadow-inner">
                        <i class="fa-solid fa-right-to-bracket text-white text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-white">Account Login</h1>
                    <p class="text-sm text-emerald-100 mt-1">Sign in to access your RouteConnect account.</p>
                </div>

                <!-- Form Body -->
                <div class="p-8">

                    <!-- Error Message -->
                    @if ($errors->any())
                        <div class="mb-5 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm rounded-2xl flex items-start gap-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-base mt-0.5 shrink-0"></i>
                            <div>
                                <div class="font-bold mb-0.5">Login Failed</div>
                                <div class="text-xs">{{ $errors->first() }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Success Flash -->
                    @if (session('success'))
                        <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm rounded-2xl flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-base mt-0.5 shrink-0"></i>
                            <div class="text-xs font-semibold">{{ session('success') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-envelope text-slate-400 dark:text-slate-500 text-sm"></i>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    placeholder="you@example.com"
                                    class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800/80 border @error('email') border-rose-400 @else border-slate-200 dark:border-slate-700 @enderror rounded-xl text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                >
                            </div>
                        </div>

                        <!-- Password Field with Eye Toggle -->
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-lock text-slate-400 dark:text-slate-500 text-sm"></i>
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-12 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                >
                                <!-- Eye Toggle Button -->
                                <button
                                    type="button"
                                    id="togglePassword"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors focus:outline-none"
                                    title="Show / Hide password"
                                >
                                    <i id="eyeIcon" class="fa-solid fa-eye text-sm"></i>
                                </button>
                            </div>
                            <p class="mt-1.5 text-[11px] text-slate-400 dark:text-slate-500">Click the <i class="fa-solid fa-eye text-slate-400"></i> icon to show/hide your password.</p>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center gap-2">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 bg-slate-50 dark:bg-slate-800"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="text-sm text-slate-600 dark:text-slate-300 font-semibold cursor-pointer select-none">
                                Remember Me
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-3.5 rounded-xl shadow-md shadow-emerald-600/20 text-sm transition-all flex items-center justify-center gap-2 cursor-pointer hover:shadow-lg"
                        >
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Log In to My Account
                        </button>

                    </form>

                    <!-- Quick Admin Login Helper -->
                    <div class="mt-4 p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between text-xs text-slate-600 dark:text-slate-300 transition-colors">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-600 dark:text-emerald-400"></i>
                            <span>Admin: <strong class="text-slate-800 dark:text-white">admin@routeconnect.com</strong></span>
                        </div>
                        <button type="button" onclick="fillAdminCredentials()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold hover:underline cursor-pointer">
                            Quick Fill
                        </button>
                    </div>

                    <!-- Footer links -->
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400 space-y-2">
                        <div>
                            New here?
                            <a href="{{ route('register') }}" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline ml-1">
                                Create a Passenger Account
                            </a>
                        </div>
                        <div>
                            Want to drive?
                            <a href="{{ route('driver.register') }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline ml-1">
                                Apply as a Driver
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon       = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        function fillAdminCredentials() {
            document.getElementById('email').value = 'admin@routeconnect.com';
            document.getElementById('password').value = 'admin123456';
        }

        // If tab was idle or backgrounded for more than 30 minutes, refresh on return to get fresh CSRF token
        let lastVisibleTime = Date.now();
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                if (Date.now() - lastVisibleTime > 30 * 60 * 1000) {
                    window.location.reload();
                }
                lastVisibleTime = Date.now();
            } else {
                lastVisibleTime = Date.now();
            }
        });
    </script>

    @include('partials.offline_indicator')
</body>
</html>
