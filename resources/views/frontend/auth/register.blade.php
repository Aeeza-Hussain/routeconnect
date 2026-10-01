<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Create Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">
    
    <!-- Top Header Navigation -->
    <nav class="bg-white border-b border-slate-200 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-extrabold text-emerald-700 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-route"></i>
            </span>
            <span>Route<span class="text-slate-900">Connect</span></span>
        </a>
        <div class="space-x-4 text-xs font-bold">
            <a href="{{ url('/') }}" class="text-slate-600 hover:text-emerald-600">Home</a>
            <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Login</a>
        </div>
    </nav>

    <!-- Main Registration Container -->
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-lg border border-slate-200 p-6 sm:p-8">
            
            <div class="text-center mb-6">
                <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                    Registration
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Create Your Account</h1>
                <p class="text-xs text-slate-500 mt-1">Choose your account type and fill in your details to get started.</p>
            </div>

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3 mb-6 space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Account Type Selector (Passenger vs Driver) -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Select Account Type</label>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Option 1: Passenger -->
                        <label id="labelPassenger" class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all border-emerald-500 bg-emerald-50/60 shadow-xs">
                            <input type="radio" name="role" value="passenger" {{ ($selectedRole ?? 'passenger') === 'passenger' ? 'checked' : '' }} onchange="toggleRoleSelection('passenger')" class="sr-only">
                            <div class="flex items-center justify-between mb-1">
                                <i class="fa-solid fa-user-gear text-emerald-600 text-base"></i>
                                <span id="iconCheckPassenger" class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-bold">✓</span>
                            </div>
                            <span class="font-extrabold text-xs text-slate-900">Passenger</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Search trips & book seats</span>
                        </label>

                        <!-- Option 2: Driver -->
                        <label id="labelDriver" class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all border-slate-200 bg-white hover:border-slate-300">
                            <input type="radio" name="role" value="driver" {{ ($selectedRole ?? '') === 'driver' ? 'checked' : '' }} onchange="toggleRoleSelection('driver')" class="sr-only">
                            <div class="flex items-center justify-between mb-1">
                                <i class="fa-solid fa-id-card text-indigo-600 text-base"></i>
                                <span id="iconCheckDriver" class="hidden w-4 h-4 rounded-full bg-indigo-600 text-white text-[10px] items-center justify-center font-bold">✓</span>
                            </div>
                            <span class="font-extrabold text-xs text-slate-900">Driver</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Offer transport & schedule trips</span>
                        </label>
                    </div>

                    <!-- Driver Application Note -->
                    <div id="driverNoticeText" class="{{ ($selectedRole ?? '') === 'driver' ? '' : 'hidden' }} mt-2 p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 flex items-start gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                        <span><strong>Driver Application Note:</strong> Driver accounts require administrator approval before accessing the driver dashboard.</span>
                    </div>
                </div>

                <!-- Profile Picture Upload -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Profile Photo / Picture</label>
                    <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-xl p-3">
                        <div class="relative w-12 h-12 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center shrink-0 border border-slate-300">
                            <img id="avatarPreview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                            <i id="avatarPlaceholder" class="fa-solid fa-user text-slate-400 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="image" id="imageInput" accept="image/*" onchange="previewImage(this)" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                            <span class="text-[10px] text-slate-400 block mt-0.5">Optional avatar photo (JPG, PNG, WEBP)</span>
                        </div>
                    </div>
                </div>

                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Ali Khan" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                </div>

                <!-- Phone / Contact Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Contact Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0300-1234567" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                </div>

                <!-- Password Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Password</label>
                        <input type="password" name="password" required placeholder="Min 6 characters" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Confirm password" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btnSubmit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-md text-sm transition-all hover:scale-[1.01]">
                    Register Account
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                Already registered? <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Log in to your account</a>
            </div>

        </div>
    </main>

    <script>
        function toggleRoleSelection(role) {
            const labelP = document.getElementById('labelPassenger');
            const labelD = document.getElementById('labelDriver');
            const iconP = document.getElementById('iconCheckPassenger');
            const iconD = document.getElementById('iconCheckDriver');
            const notice = document.getElementById('driverNoticeText');
            const btn = document.getElementById('btnSubmit');

            if (role === 'passenger') {
                labelP.className = 'relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all border-emerald-500 bg-emerald-50/60 shadow-xs';
                labelD.className = 'relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all border-slate-200 bg-white hover:border-slate-300';
                iconP.classList.remove('hidden');
                iconP.classList.add('flex');
                iconD.classList.add('hidden');
                iconD.classList.remove('flex');
                notice.classList.add('hidden');
                btn.className = 'w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-md text-sm transition-all hover:scale-[1.01]';
                btn.innerText = 'Register as Passenger';
            } else {
                labelD.className = 'relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all border-indigo-500 bg-indigo-50/60 shadow-xs';
                labelP.className = 'relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all border-slate-200 bg-white hover:border-slate-300';
                iconD.classList.remove('hidden');
                iconD.classList.add('flex');
                iconP.classList.add('hidden');
                iconP.classList.remove('flex');
                notice.classList.remove('hidden');
                btn.className = 'w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-md text-sm transition-all hover:scale-[1.01]';
                btn.innerText = 'Submit Driver Application';
            }
        }

        function previewImage(input) {
            const preview = document.getElementById('avatarPreview');
            const placeholder = document.getElementById('avatarPlaceholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const initialRole = "{{ $selectedRole ?? 'passenger' }}";
            toggleRoleSelection(initialRole);
        });
    </script>
</body>
</html>
