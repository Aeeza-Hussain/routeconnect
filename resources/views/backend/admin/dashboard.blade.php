<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 antialiased font-sans flex flex-col min-h-screen">
    
    <nav class="bg-slate-800 border-b border-slate-700 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-400">RouteConnect Admin Portal</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.drivers.index') }}" class="text-xs font-bold text-indigo-300 hover:text-white bg-indigo-950 border border-indigo-700 px-3 py-1.5 rounded-lg">Driver Applications ({{ $pendingDrivers }})</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-slate-300 hover:text-white bg-slate-700 px-3 py-1.5 rounded-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="flex-1 max-w-5xl mx-auto my-12 p-8 bg-slate-800 rounded-2xl border border-slate-700 shadow-xl w-full">
        <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 bg-indigo-950 px-3 py-1 rounded-full border border-indigo-800">System Administration</span>
        
        <h1 class="text-3xl font-extrabold text-white mt-4">Welcome, Admin</h1>
        <p class="text-sm text-slate-300 mt-1">RouteConnect Platform Overview & System Controls.</p>

        @if (session('success'))
            <div class="mt-4 p-3 bg-emerald-950 border border-emerald-700 text-emerald-300 text-xs rounded-xl font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="p-6 bg-slate-900 rounded-xl border border-slate-700">
                <div class="text-xs text-slate-400 font-bold uppercase">Pending Driver Applications</div>
                <div class="text-3xl font-extrabold text-amber-400 mt-2">{{ $pendingDrivers }}</div>
                <div class="mt-3">
                    <a href="{{ route('admin.drivers.index') }}" class="text-xs text-amber-300 font-bold underline hover:text-amber-200">Review Applications →</a>
                </div>
            </div>

            <div class="p-6 bg-slate-900 rounded-xl border border-slate-700">
                <div class="text-xs text-slate-400 font-bold uppercase">Total Users</div>
                <div class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $totalUsers }}</div>
                <div class="text-xs text-slate-500 mt-3">All registered accounts</div>
            </div>

            <div class="p-6 bg-slate-900 rounded-xl border border-slate-700">
                <div class="text-xs text-slate-400 font-bold uppercase">Total Drivers</div>
                <div class="text-3xl font-extrabold text-indigo-400 mt-2">{{ $totalDrivers }}</div>
                <div class="text-xs text-slate-500 mt-3">Pending & approved drivers</div>
            </div>
        </div>
    </main>

</body>
</html>
