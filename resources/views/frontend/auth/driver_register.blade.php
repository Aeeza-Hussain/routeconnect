<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Driver Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex flex-col min-h-screen">
    
    <nav class="bg-white border-b border-slate-200 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold text-emerald-700">RouteConnect</a>
        <div class="space-x-4 text-sm font-semibold">
            <a href="{{ url('/') }}" class="text-slate-600 hover:text-emerald-600">Home</a>
            <a href="{{ route('login') }}" class="text-slate-600 hover:text-emerald-600">Login</a>
            <a href="{{ route('register') }}" class="text-slate-600 hover:text-emerald-600">Passenger Register</a>
            <a href="{{ route('driver.register') }}" class="text-indigo-600">Become a Driver</a>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-indigo-200 p-8">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">Driver Application</span>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-2 mb-1">Apply to Become a Driver</h1>
            <p class="text-xs text-slate-500 mb-6">Submit your registration. Driver applications require Admin approval before workspace access is granted.</p>

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3 mb-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('driver.register.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0300-9876543" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-xs text-sm transition-all">
                    Submit Driver Application
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                Already registered? <a href="{{ route('login') }}" class="text-indigo-600 font-bold hover:underline">Log in</a>
            </div>
        </div>
    </main>

</body>
</html>
