<!-- Admin Sidebar Navigation -->
<aside id="adminSidebar" class="w-64 bg-[#0B192C] text-slate-300 min-h-screen flex flex-col shrink-0 transition-all duration-300 border-r border-slate-800">
    
    <!-- Sidebar Logo Header -->
    <div class="h-20 flex items-center px-6 border-b border-slate-800/80">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold text-xs">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <span class="font-extrabold text-xl tracking-tight text-white">
                Route<span class="text-emerald-400">Admin</span>
            </span>
        </a>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 py-6 px-4 space-y-1.5 overflow-y-auto">
        
        <!-- Section: Main -->
        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Navigation</div>

        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-gauge text-sm w-4"></i>
            <span>Dashboard</span>
        </a>

        <!-- Users -->
        <a href="{{ url('/admin/users') }}" onclick="event.preventDefault(); alert('Users CRUD module will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-users text-sm w-4"></i>
            <span>Users</span>
        </a>

        <!-- Driver Applications -->
        <a href="{{ route('admin.drivers.applications') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.drivers.applications*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-id-card text-sm w-4"></i>
                <span>Driver Applications</span>
            </div>
            @php
                $pendingCount = \App\Models\User::where('role', 'driver')->where('driver_status', 'pending')->count();
            @endphp
            @if ($pendingCount > 0)
                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-[10px] font-extrabold border border-amber-500/30">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>

        <!-- Drivers -->
        <a href="{{ route('admin.drivers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.drivers.index') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
            <i class="fa-solid fa-user-check text-sm w-4"></i>
            <span>Drivers</span>
        </a>

        <!-- Vehicles -->
        <a href="{{ url('/admin/vehicles') }}" onclick="event.preventDefault(); alert('Vehicles CRUD module will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-van-shuttle text-sm w-4"></i>
            <span>Vehicles</span>
        </a>

        <!-- Routes -->
        <a href="{{ url('/admin/routes') }}" onclick="event.preventDefault(); alert('Routes CRUD module will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-route text-sm w-4"></i>
            <span>Routes</span>
        </a>

        <!-- Stops -->
        <a href="{{ url('/admin/stops') }}" onclick="event.preventDefault(); alert('Stops CRUD module will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-location-dot text-sm w-4"></i>
            <span>Stops</span>
        </a>

        <!-- Trips -->
        <a href="{{ url('/admin/trips') }}" onclick="event.preventDefault(); alert('Trips CRUD module will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-bus text-sm w-4"></i>
            <span>Trips</span>
        </a>

        <!-- Bookings -->
        <a href="{{ url('/admin/bookings') }}" onclick="event.preventDefault(); alert('Bookings CRUD module will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-ticket text-sm w-4"></i>
            <span>Bookings</span>
        </a>

        <div class="pt-4 border-t border-slate-800/80 px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">System</div>

        <!-- Notifications -->
        <a href="{{ url('/admin/notifications') }}" onclick="event.preventDefault(); alert('Notifications module will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-bell text-sm w-4"></i>
            <span>Notifications</span>
        </a>

        <!-- Settings -->
        <a href="{{ url('/admin/settings') }}" onclick="event.preventDefault(); alert('Settings page will be implemented in a future step.');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all">
            <i class="fa-solid fa-gear text-sm w-4"></i>
            <span>Settings</span>
        </a>

    </div>

    <!-- Sidebar Footer / Logout -->
    <div class="p-4 border-t border-slate-800/80">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-rose-900/40 text-slate-300 hover:text-rose-400 text-xs font-bold transition-all border border-slate-700 hover:border-rose-800">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
