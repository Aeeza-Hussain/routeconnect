<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Driver Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 antialiased font-sans flex flex-col min-h-screen">
    
    <nav class="bg-slate-800 border-b border-slate-700 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold text-emerald-400">RouteConnect Driver Portal</a>
        <div class="flex items-center gap-4">
            <span class="text-xs text-slate-300">Logged in as: <strong>{{ auth()->user()->name }}</strong></span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-slate-300 hover:text-white bg-slate-700 px-3 py-1.5 rounded-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="flex-1 max-w-4xl mx-auto my-12 p-8 bg-slate-800 rounded-2xl border border-slate-700 shadow-xl w-full">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 bg-emerald-950 px-3 py-1 rounded-full border border-emerald-800">Approved Driver Console</span>
        
        <h1 class="text-3xl font-extrabold text-white mt-4">Welcome, {{ auth()->user()->name }}</h1>
        
        <div class="mt-2 text-sm text-emerald-400 font-bold flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Driver Status: Approved
        </div>

        <p class="text-base text-slate-300 mt-4 leading-relaxed">
            You are now able to manage your scheduled transport trips.
        </p>

        <div class="mt-8 p-6 bg-slate-900 rounded-xl border border-slate-700">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Driver Account Overview</h3>
            <div class="mt-3 text-xs text-slate-300 space-y-1">
                <div>Email: <strong>{{ auth()->user()->email }}</strong></div>
                <div>Phone: <strong>{{ auth()->user()->phone ?? 'N/A' }}</strong></div>
                <div>Registered: <strong>{{ auth()->user()->created_at->format('d M Y') }}</strong></div>
            </div>
        </div>
    </main>

</body>
</html>
