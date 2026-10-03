<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="RouteConnect Admin Dashboard — System administration and operations control">
    <title>@yield('title', 'Admin Dashboard — RouteConnect')</title>

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
        /* Custom scrollbar for modern sleek feel */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.7); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased h-screen overflow-hidden flex flex-row">

    <!-- Mobile sidebar overlay -->
    <div id="sidebarOverlay"
         class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden transition-opacity"
         onclick="closeSidebar()"></div>

    <!-- Admin Sidebar (Fixed height on desktop, drawer on mobile) -->
    @include('backend.admin.partials.sidebar')

    <!-- Main Content Area (independent scrollable view) -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">

        <!-- Admin Top Header (pinned at top) -->
        <div class="shrink-0">
            @include('backend.admin.partials.navbar')
        </div>

        <!-- Page Content (ONLY this area scrolls vertically) -->
        <main class="flex-1 p-5 sm:p-8 overflow-y-auto">
            @yield('content')
        </main>

        <!-- Admin Footer (pinned at bottom) -->
        <footer class="shrink-0 py-3.5 px-6 sm:px-8 bg-white border-t border-slate-200 text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>© {{ date('Y') }} RouteConnect — Admin Operations Panel</div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                System Online · Version 1.0
            </div>
        </footer>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle  = document.getElementById('adminSidebarToggle');

        function openSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        if (toggle) {
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }
    </script>

    @yield('scripts')
</body>
</html>
