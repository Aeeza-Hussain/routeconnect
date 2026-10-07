<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Driver Applications Management</title>
    <!-- PWA Manifest & App Theme -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Local FontAwesome 6 Icons (Fully Offline) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Local Offline-First Stylesheets -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 antialiased font-sans flex flex-col min-h-screen">
    
    <nav class="bg-slate-800 border-b border-slate-700 py-4 px-6 flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-indigo-400">RouteConnect Admin Portal</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-300 hover:text-white">Dashboard Overview</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-slate-300 hover:text-white bg-slate-700 px-3 py-1.5 rounded-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="flex-1 max-w-5xl mx-auto my-12 p-8 bg-slate-800 rounded-2xl border border-slate-700 shadow-xl w-full">
        <div class="flex items-center justify-between mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 bg-indigo-950 px-3 py-1 rounded-full border border-indigo-800">Admin Approval Queue</span>
                <h1 class="text-2xl font-extrabold text-white mt-2">Driver Applications</h1>
                <p class="text-xs text-slate-400 mt-1">Review driver registrations and approve or reject access.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-300 hover:text-white bg-slate-700 px-3 py-2 rounded-xl">← Back to Dashboard</a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-3 bg-emerald-950 border border-emerald-700 text-emerald-300 text-xs rounded-xl font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-slate-900 rounded-xl border border-slate-700 overflow-hidden">
            @if ($drivers->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">
                    No driver applications submitted yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-800 text-slate-400 font-bold uppercase border-b border-slate-700">
                            <tr>
                                <th class="p-4">Name</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Phone</th>
                                <th class="p-4">Registration Date</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($drivers as $driver)
                                <tr>
                                    <td class="p-4 font-bold text-white">{{ $driver->name }}</td>
                                    <td class="p-4 text-slate-300">{{ $driver->email }}</td>
                                    <td class="p-4 text-slate-300">{{ $driver->phone ?? 'N/A' }}</td>
                                    <td class="p-4 text-slate-400">{{ $driver->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="p-4">
                                        @if ($driver->driver_status === 'approved')
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800 font-bold">Approved</span>
                                        @elseif ($driver->driver_status === 'pending')
                                            <span class="px-2.5 py-1 rounded-full bg-amber-950 text-amber-400 border border-amber-800 font-bold">Pending Review</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-rose-950 text-rose-400 border border-rose-800 font-bold">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        @if ($driver->driver_status !== 'approved')
                                            <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if ($driver->driver_status !== 'rejected')
                                            <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </main>

    @include('partials.offline_indicator')
</body>
</html>
