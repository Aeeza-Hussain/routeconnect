<!-- Public Professional Footer matching mockup design -->
<footer class="bg-[#0B192C] text-slate-300 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-12">
            
            <!-- Col 1: Brand Info -->
            <div class="lg:col-span-2 space-y-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold text-sm">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight text-white">
                        Route<span class="text-emerald-400">Connect</span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium">
                    Know your route. Know your time. Travel smarter.
                </p>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Quick Links</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ url('/') }}" class="text-slate-400 hover:text-white transition-colors">Home</a></li>
                    <li><a href="{{ url('/trips') }}" class="text-slate-400 hover:text-white transition-colors">Search Trips</a></li>
                    <li><a href="{{ url('/#about') }}" class="text-slate-400 hover:text-white transition-colors">About</a></li>
                    <li><a href="{{ route('register', ['role' => 'driver']) }}" class="text-slate-400 hover:text-white transition-colors">Become a Driver</a></li>
                </ul>
            </div>

            <!-- Col 3: For Passengers -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">For Passengers</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ url('/trips') }}" class="text-slate-400 hover:text-white transition-colors">Search Trips</a></li>
                    <li><a href="{{ url('/booking') }}" class="text-slate-400 hover:text-white transition-colors">My Bookings</a></li>
                    <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors">Login</a></li>
                    <li><a href="{{ route('register') }}" class="text-slate-400 hover:text-white transition-colors">Register</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact Info -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Contact</h4>
                <ul class="space-y-2 text-xs text-slate-400">
                    <li class="flex items-center gap-2">
                        <i class="fa-regular fa-envelope text-slate-400"></i>
                        <span>info@routeconnect.com</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-slate-400"></i>
                        <span>Gilgit-Baltistan, Pakistan</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="mt-12 pt-6 border-t border-slate-800/80 text-center text-[11px] text-slate-500">
            © 2026 RouteConnect. All rights reserved.
        </div>
    </div>
</footer>
