<!-- Public Professional Footer -->
<footer class="bg-slate-900 text-slate-300 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-12">
            
            <!-- Col 1: Brand Info -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-lg">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight text-white">
                        Route<span class="text-emerald-400">Connect</span>
                    </span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                    "Know your route. Know your time. Travel smarter."
                </p>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Smart transport scheduling and seat booking platform. Helping passengers search scheduled vehicles, check stop timings, and book seats in advance.
                </p>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold text-white uppercase tracking-wider">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-emerald-400 transition-colors">Home</a></li>
                    <li><a href="{{ url('/trips') }}" class="hover:text-emerald-400 transition-colors">Search Trips</a></li>
                    <li><a href="{{ url('/#about') }}" class="hover:text-emerald-400 transition-colors">About Us</a></li>
                    <li><a href="{{ route('driver.register') }}" class="hover:text-emerald-400 transition-colors">Become a Driver</a></li>
                </ul>
            </div>

            <!-- Col 3: For Passengers & Drivers -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold text-white uppercase tracking-wider">For Users</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/trips') }}" class="hover:text-emerald-400 transition-colors">Search Vehicles</a></li>
                    <li><a href="{{ url('/booking') }}" class="hover:text-emerald-400 transition-colors">My Bookings</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Passenger Login</a></li>
                    <li><a href="{{ route('driver.register') }}" class="hover:text-emerald-400 transition-colors">Driver Application</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact Info -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold text-white uppercase tracking-wider">Contact Info</h4>
                <ul class="space-y-2 text-xs text-slate-400">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-emerald-400"></i>
                        <span>Gilgit-Baltistan, Pakistan</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-emerald-400"></i>
                        <span>info@routeconnect.com</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-phone text-emerald-400"></i>
                        <span>+92 5811 554321</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>
                © 2026 RouteConnect. All rights reserved.
            </div>
            <div>
                Scheduled Transport & Seat Booking Platform
            </div>
        </div>
    </div>
</footer>
