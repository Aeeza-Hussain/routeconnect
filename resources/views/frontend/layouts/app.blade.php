@php
    $isMarketplacePage = request()->is('trips*') || request()->is('find-a-ride*');
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth {{ $isMarketplacePage ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'RouteConnect — Smart Transport Scheduling & Booking System')</title>
    <meta name="description" content="Know your route. Know your time. Travel smarter. Find scheduled transport vehicles, expected stop timings, and book seats in advance.">

    <!-- PWA Manifest & App Theme -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="{{ $isMarketplacePage ? '#070e1b' : '#059669' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Local FontAwesome 6 Icons (Works Completely Offline) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Local Offline-First Stylesheets -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Google Fonts (Enhanced typography when online, zero layout-shift offline) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

    <!-- RouteConnect Theme Engine (Zero FOUC) -->
    @include('partials.theme_init')
    @if ($isMarketplacePage)
        <script>
            document.documentElement.classList.add('dark');
        </script>
    @endif
    @stack('styles')
</head>
<body class="{{ $isMarketplacePage ? 'bg-[#070e1b]' : 'bg-slate-50 dark:bg-[#090e17]' }} text-slate-900 dark:text-slate-100 font-sans antialiased flex flex-col min-h-screen transition-colors duration-200" style="{{ $isMarketplacePage ? 'background-color: #070e1b !important;' : '' }}">

    <!-- Navigation Header -->
    @include('frontend.partials.navbar')

    <!-- Main View Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('frontend.partials.footer')

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('mobileMenuBtn');
            const menu = document.getElementById('mobileMenu');
            if (btn && menu) {
                btn.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
    @include('partials.offline_indicator')
</body>
</html>
