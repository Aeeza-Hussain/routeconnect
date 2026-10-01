<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Driver Application Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 antialiased font-sans flex flex-col min-h-screen">
    
    <nav class="bg-slate-800 border-b border-slate-700 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold text-amber-400">RouteConnect Driver Workspace</a>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs font-bold text-slate-300 hover:text-white bg-slate-700 px-3 py-1.5 rounded-lg">Logout</button>
        </form>
    </nav>

    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-lg bg-slate-800 rounded-2xl border border-slate-700 p-8 text-center shadow-2xl">
            
            @if (auth()->user()->driver_status === 'pending')
                <div class="w-16 h-16 rounded-full bg-amber-500/20 text-amber-400 mx-auto flex items-center justify-center text-2xl font-bold mb-4">
                    ⏳
                </div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400 bg-amber-950 px-3 py-1 rounded-full border border-amber-800">Application Status: Pending</span>
                <h1 class="text-2xl font-extrabold text-white mt-4">Driver Application Under Review</h1>
                <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                    Hello <strong>{{ auth()->user()->name }}</strong>,<br>
                    Your driver application is pending admin approval.
                </p>
                <div class="mt-6 p-4 bg-slate-900 rounded-xl border border-slate-700 text-xs text-slate-400">
                    Once the system administrator reviews and approves your account, you will automatically gain access to the Driver Console to manage your scheduled transport trips.
                </div>
            @elseif (auth()->user()->driver_status === 'rejected')
                <div class="w-16 h-16 rounded-full bg-rose-500/20 text-rose-400 mx-auto flex items-center justify-center text-2xl font-bold mb-4">
                    ❌
                </div>
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400 bg-rose-950 px-3 py-1 rounded-full border border-rose-800">Application Status: Rejected</span>
                <h1 class="text-2xl font-extrabold text-white mt-4">Driver Application Not Approved</h1>
                <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                    Hello <strong>{{ auth()->user()->name }}</strong>,<br>
                    Your driver application was reviewed and rejected by the administrator.
                </p>
            @endif

            <div class="mt-8 pt-4 border-t border-slate-700">
                <a href="{{ url('/') }}" class="inline-block text-xs font-bold text-slate-400 hover:text-white">← Return to Public Website</a>
            </div>
        </div>
    </main>

</body>
</html>
