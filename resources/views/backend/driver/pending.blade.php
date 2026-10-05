<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Driver Application Status</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
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
</head>
<body class="bg-[#0B132B] text-slate-100 antialiased font-sans flex flex-col min-h-screen">
    
    <!-- ─── Navbar ─── -->
    <nav class="bg-[#070E1E] border-b border-slate-800/80 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center text-white shadow-md shadow-teal-500/20">
                <i class="fa-solid fa-car-side text-sm"></i>
            </div>
            <span class="font-extrabold text-lg text-white">Route<span class="text-teal-400">Driver</span> Workspace</span>
        </a>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs font-bold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-3.5 py-2 rounded-xl transition-all border border-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </nav>

    <!-- ─── Main Content Card ─── -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-lg bg-slate-900/90 rounded-3xl border border-slate-800 p-8 sm:p-10 text-center shadow-2xl relative overflow-hidden backdrop-blur-md">
            
            <div class="absolute -top-20 -right-20 w-44 h-44 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

            @if (auth()->user()->driver_status === 'pending')
                <div class="w-20 h-20 rounded-3xl bg-amber-500/10 text-amber-400 mx-auto flex items-center justify-center text-3xl font-bold mb-5 border border-amber-500/20 shadow-lg shadow-amber-500/10">
                    <i class="fa-solid fa-hourglass-half animate-pulse"></i>
                </div>
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-4 py-1.5 rounded-full border border-amber-500/30 inline-block mb-3">
                    Application Status: Pending Review
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Application Under Review</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-3 leading-relaxed max-w-md mx-auto">
                    Hello <strong class="text-white">{{ auth()->user()->name }}</strong>,<br>
                    Your commercial driver profile has been registered and is currently queued for administrator verification.
                </p>
                <div class="mt-6 p-4 bg-slate-950/70 rounded-2xl border border-slate-800/80 text-xs text-slate-400 text-left space-y-2">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-teal-400 mt-0.5"></i>
                        <span>Once approved by the RouteConnect administrator, this screen will unlock access to your full <strong>Driver Operations Dashboard</strong>, trips schedule, vehicle records, and passenger bookings.</span>
                    </div>
                </div>
            @elseif (auth()->user()->driver_status === 'rejected')
                <div class="w-20 h-20 rounded-3xl bg-rose-500/10 text-rose-400 mx-auto flex items-center justify-center text-3xl font-bold mb-5 border border-rose-500/20 shadow-lg shadow-rose-500/10">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-400 bg-rose-500/10 px-4 py-1.5 rounded-full border border-rose-500/30 inline-block mb-3">
                    Application Status: Not Approved
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Application Not Approved</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-3 leading-relaxed max-w-md mx-auto">
                    Hello <strong class="text-white">{{ auth()->user()->name }}</strong>,<br>
                    Your driver registration was reviewed and could not be approved at this time.
                </p>
                <div class="mt-6 p-4 bg-slate-950/70 rounded-2xl border border-slate-800/80 text-xs text-slate-400 text-left space-y-2">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-headset text-rose-400 mt-0.5"></i>
                        <span>Please reach out to support at <strong class="text-slate-300">support@routeconnect.com</strong> if you have questions or wish to re-submit supporting documentation.</span>
                    </div>
                </div>
            @endif

            <div class="mt-8 pt-5 border-t border-slate-800 flex items-center justify-center gap-4 text-xs font-bold text-slate-400">
                <a href="{{ url('/') }}" class="hover:text-teal-400 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Return to Public Website</span>
                </a>
            </div>
        </div>
    </main>

    <!-- ─── Footer ─── -->
    <footer class="py-4 text-center text-xs text-slate-500 border-t border-slate-800/60">
        © 2026 RouteConnect Transport Platform. Secure driver portal.
    </footer>

</body>
</html>
