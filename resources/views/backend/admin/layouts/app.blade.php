<!DOCTYPE html>
<html lang="en">
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
        /* Custom scrollbar for sidebar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100,116,139,0.3); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100,116,139,0.5); }

        /* Sidebar mobile overlay transition */
        #adminSidebar { transition: transform 0.25s ease; }
        #adminSidebar.sidebar-hidden { transform: translateX(-100%); }

        @media (max-width: 1023px) {
            #adminSidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                z-index: 50;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex">

    <!-- Mobile sidebar overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

    <!-- Admin Sidebar -->
    @include('backend.admin.partials.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:ml-0">

        <!-- Admin Top Header -->
        @include('backend.admin.partials.navbar')

        <!-- Page Content -->
        <main class="flex-1 p-5 sm:p-8 overflow-y-auto">
            @yield('content')
        </main>

        <!-- Admin Footer -->
        <footer class="py-4 px-8 bg-white border-t border-slate-200 text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>© {{ date('Y') }} RouteConnect — Admin Operations Panel</div>
            <div class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
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
            sidebar.classList.remove('sidebar-hidden');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('sidebar-hidden');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                if (sidebar.classList.contains('sidebar-hidden')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }

        // On small screens, start with sidebar hidden
        if (window.innerWidth < 1024) {
            sidebar.classList.add('sidebar-hidden');
        }
    </script>
</body>
</html>
