<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Account Login</title>

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
<body class="bg-gradient-to-br from-slate-50 to-slate-100 text-slate-800 antialiased font-sans flex flex-col min-h-screen">

    <!-- ─── Navbar ─── -->
    <nav class="bg-white border-b border-slate-200 py-4 px-6 flex items-center justify-between shadow-sm">
        <a href="{{ url('/') }}" class="text-xl font-extrabold text-emerald-700 tracking-tight">
            Route<span class="text-slate-800">Connect</span>
        </a>
        <div class="flex items-center gap-5 text-sm font-semibold">
            <a href="{{ url('/') }}" class="text-slate-500 hover:text-emerald-600 transition-colors">Home</a>
            <a href="{{ route('login') }}" class="text-emerald-700 font-bold">Login</a>
            <a href="{{ route('register') }}" class="text-slate-500 hover:text-emerald-600 transition-colors">Register</a>
        </div>
    </nav>

    <!-- ─── Login Form ─── -->
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md">

            <!-- Card -->
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/80 overflow-hidden">

                <!-- Card Top Banner -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-7">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-right-to-bracket text-white text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-white">Account Login</h1>
                    <p class="text-sm text-emerald-100 mt-1">Sign in to access your RouteConnect account.</p>
                </div>

                <!-- Form Body -->
                <div class="p-8">

                    <!-- Error Message -->
                    @if ($errors->any())
                        <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-2xl flex items-start gap-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-base mt-0.5 shrink-0"></i>
                            <div>
                                <div class="font-bold mb-0.5">Login Failed</div>
                                <div class="text-xs">{{ $errors->first() }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Success Flash -->
                    @if (session('success'))
                        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-2xl flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-base mt-0.5 shrink-0"></i>
                            <div class="text-xs font-semibold">{{ session('success') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-envelope text-slate-400 text-sm"></i>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    placeholder="you@example.com"
                                    class="w-full pl-10 pr-4 py-3 bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                >
                            </div>
                        </div>

                        <!-- Password Field with Eye Toggle -->
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                >
                                <!-- Eye Toggle Button -->
                                <button
                                    type="button"
                                    id="togglePassword"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition-colors focus:outline-none"
                                    title="Show / Hide password"
                                >
                                    <i id="eyeIcon" class="fa-solid fa-eye text-sm"></i>
                                </button>
                            </div>
                            <p class="mt-1.5 text-[11px] text-slate-400">Click the <i class="fa-solid fa-eye text-slate-400"></i> icon to show/hide your password.</p>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center gap-2">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="text-sm text-slate-600 font-semibold cursor-pointer select-none">
                                Remember Me
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-3.5 rounded-xl shadow-md shadow-emerald-600/20 text-sm transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Log In to My Account
                        </button>

                    </form>

                    <!-- Footer links -->
                    <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500 space-y-2">
                        <div>
                            New here?
                            <a href="{{ route('register') }}" class="text-emerald-600 font-bold hover:underline ml-1">
                                Create a Passenger Account
                            </a>
                        </div>
                        <div>
                            Want to drive?
                            <a href="{{ route('driver.register') }}" class="text-indigo-600 font-bold hover:underline ml-1">
                                Apply as a Driver
                            </a>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </main>

    <!-- Password Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon       = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>
