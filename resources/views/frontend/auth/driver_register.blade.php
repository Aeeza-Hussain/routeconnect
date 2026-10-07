<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Commercial Driver Application</title>

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
</head>
<body class="bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 text-slate-800 antialiased font-sans flex flex-col min-h-screen">
    
    <!-- ─── Navbar ─── -->
    <nav class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 py-4 px-6 flex items-center justify-between shadow-xs sticky top-0 z-30">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-600/20">
                <i class="fa-solid fa-location-dot text-base"></i>
            </div>
            <span class="font-extrabold text-xl tracking-tight text-slate-900 group-hover:text-emerald-600 transition-colors">
                Route<span class="text-emerald-600">Connect</span>
            </span>
        </a>
        <div class="flex items-center gap-5 text-xs sm:text-sm font-semibold">
            <a href="{{ url('/') }}" class="text-slate-600 hover:text-emerald-600 transition-colors">Home</a>
            <a href="{{ route('login') }}" class="text-slate-600 hover:text-emerald-600 transition-colors">Login</a>
            <a href="{{ route('register') }}" class="text-slate-600 hover:text-emerald-600 transition-colors">Passenger Register</a>
            <a href="{{ route('driver.register') }}" class="text-emerald-700 font-bold">Become a Driver</a>
        </div>
    </nav>

    <!-- ─── Driver Application Form Container ─── -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-xl">

            <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">

                <!-- Card Top Banner -->
                <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-emerald-950 px-8 py-7 text-white relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-emerald-500/20 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[11px] font-bold uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-id-card text-emerald-400"></i> Driver Application
                            </span>
                            <h1 class="text-2xl font-black text-white">Apply as Commercial Driver</h1>
                            <p class="text-xs text-slate-300 mt-1 max-w-md">
                                Register your driver credentials. Applications undergo Admin review before console access is unlocked.
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 text-xl shrink-0">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-6 sm:p-8 space-y-5">

                    @if ($errors->any())
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-2xl p-4 space-y-1">
                            <div class="font-bold flex items-center gap-2 text-rose-900">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>Please resolve the following issues:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('driver.register.submit') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-user text-slate-400 text-[11px]"></i>
                                <span>Full Name</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Karim Ullah"
                                   required
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-envelope text-slate-400 text-[11px]"></i>
                                    <span>Email Address</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="driver@example.com"
                                       required
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-slate-400 text-[11px]"></i>
                                    <span>Phone Number</span>
                                </label>
                                <input type="text"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       placeholder="0300-1234567"
                                       required
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-lock text-slate-400 text-[11px]"></i>
                                    <span>Password</span>
                                </label>
                                <input type="password"
                                       name="password"
                                       placeholder="••••••••"
                                       required
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-shield-check text-slate-400 text-[11px]"></i>
                                    <span>Confirm Password</span>
                                </label>
                                <input type="password"
                                       name="password_confirmation"
                                       placeholder="••••••••"
                                       required
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3 rounded-xl shadow-md shadow-emerald-600/20 text-sm transition-all hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>Submit Driver Application</span>
                            </button>
                        </div>
                    </form>

                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                        <span>Already registered as a driver?</span>
                        <a href="{{ route('login') }}" class="text-emerald-700 font-bold hover:underline">
                            Log into your account →
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- ─── Footer ─── -->
    <footer class="py-4 text-center text-xs text-slate-500">
        © 2026 RouteConnect Platform. Safe, verified transit system.
    </footer>

    @include('partials.offline_indicator')
</body>
</html>
