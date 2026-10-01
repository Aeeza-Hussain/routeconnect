<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex flex-col min-h-screen">
    
    <nav class="bg-white border-b border-slate-200 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold text-emerald-700">RouteConnect</a>
        <div class="space-x-4 text-sm font-semibold">
            <a href="{{ url('/') }}" class="text-slate-600 hover:text-emerald-600">Home</a>
            <a href="{{ route('login') }}" class="text-emerald-700">Login</a>
            <a href="{{ route('register') }}" class="text-slate-600 hover:text-emerald-600">Passenger Register</a>
            <a href="{{ route('driver.register') }}" class="text-emerald-600 hover:text-emerald-800">Become a Driver</a>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Account Login</h1>
            <p class="text-xs text-slate-500 mb-6">Enter your email and password to log in to RouteConnect.</p>

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600">
                        <span>Remember Me</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-xs text-sm transition-all">
                    Log In
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500 space-y-1">
                <div>Need a passenger account? <a href="{{ route('register') }}" class="text-emerald-600 font-bold hover:underline">Register here</a></div>
                <div>Want to drive with us? <a href="{{ route('driver.register') }}" class="text-indigo-600 font-bold hover:underline">Apply as a Driver</a></div>
            </div>
        </div>
    </main>

</body>
</html>
