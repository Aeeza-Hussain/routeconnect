<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="RouteConnect Driver Portal — Real-time trip, vehicle, and booking operations">
    <title>@yield('title', 'Driver Portal — RouteConnect')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.25); border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.5); }
    </style>
</head>
<body class="bg-[#0B132B] text-slate-100 font-sans antialiased h-screen overflow-hidden flex flex-row">

    <!-- Mobile sidebar overlay -->
    <div id="driverSidebarOverlay"
         class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden transition-opacity"
         onclick="closeDriverSidebar()"></div>

    <!-- Driver Sidebar (Fixed height on desktop, drawer on mobile) -->
    @include('backend.driver.partials.sidebar')

    <!-- Main Content Area (independent scrollable view) -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-slate-900">

        <!-- Driver Top Header (pinned at top) -->
        <div class="shrink-0">
            @include('backend.driver.partials.navbar')
        </div>

        <!-- Page Content (ONLY this area scrolls vertically) -->
        <main class="flex-1 p-5 sm:p-8 overflow-y-auto">
            @yield('content')
        </main>

        <!-- Driver Footer (pinned at bottom) -->
        <footer class="shrink-0 py-3.5 px-6 sm:px-8 bg-slate-950/80 border-t border-slate-800 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>© {{ date('Y') }} RouteConnect — Verified Driver Console</div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                <span class="text-emerald-400 font-bold">Driver Status: Approved</span>
            </div>
        </footer>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        const driverSidebar = document.getElementById('driverSidebar');
        const driverOverlay = document.getElementById('driverSidebarOverlay');
        const driverToggle  = document.getElementById('driverSidebarToggle');

        function openDriverSidebar() {
            if (!driverSidebar || !driverOverlay) return;
            driverSidebar.classList.remove('-translate-x-full');
            driverSidebar.classList.add('translate-x-0');
            driverOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDriverSidebar() {
            if (!driverSidebar || !driverOverlay) return;
            driverSidebar.classList.add('-translate-x-full');
            driverSidebar.classList.remove('translate-x-0');
            driverOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        if (driverToggle) {
            driverToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                if (driverSidebar.classList.contains('-translate-x-full')) {
                    openDriverSidebar();
                } else {
                    closeDriverSidebar();
                }
            });
        }
    </script>
</body>
</html>
