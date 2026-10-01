<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteConnect — Join Our Community</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Caveat:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-script { font-family: 'Caveat', cursive; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <!-- Split-Screen Registration Container -->
    <div class="w-full max-w-6xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[640px] border border-slate-200/60">
        
        <!-- LEFT HALF: Hero Visual Banner -->
        <div class="lg:col-span-5 relative bg-slate-900 text-white p-8 sm:p-10 flex flex-col justify-between overflow-hidden min-h-[400px] lg:min-h-full">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/hero.jpg') }}" alt="RouteConnect Highway Bus" class="w-full h-full object-cover opacity-85 scale-105 transition-transform duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/60 to-slate-900/40"></div>
            </div>

            <!-- Top Header Badge -->
            <div class="relative z-10">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-extrabold tracking-wide border border-white/30 shadow-xs">
                    <i class="fa-solid fa-user-group text-emerald-400"></i> Join Our Community
                </span>
            </div>

            <!-- Middle Content: Headline & 2x2 Feature Grid -->
            <div class="relative z-10 my-auto py-8">
                <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight tracking-tight text-white mb-3">
                    Travel together,<br>
                    <span class="text-emerald-400">go further.</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-200/90 leading-relaxed max-w-sm mb-8 font-medium">
                    Create your account and start exploring scheduled trips, book your seats and travel with confidence.
                </p>

                <!-- 2x2 Feature Highlights -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Feature 1 -->
                    <div class="flex items-start gap-3 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm border border-emerald-400/30">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-xs">Safe & Reliable</h4>
                            <p class="text-[10px] text-slate-300 mt-0.5 leading-snug">Verified drivers & trips.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-start gap-3 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm border border-emerald-400/30">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-xs">On-Time Travel</h4>
                            <p class="text-[10px] text-slate-300 mt-0.5 leading-snug">Expected stop timings.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-start gap-3 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm border border-emerald-400/30">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-xs">Comfortable Rides</h4>
                            <p class="text-[10px] text-slate-300 mt-0.5 leading-snug">Seat availability view.</p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="flex items-start gap-3 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm border border-emerald-400/30">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-xs">Explore More</h4>
                            <p class="text-[10px] text-slate-300 mt-0.5 leading-snug">New routes & stops.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Branding -->
            <div class="relative z-10 pt-4 border-t border-white/15 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-route"></i>
                    </span>
                    <span class="text-sm font-extrabold tracking-tight">Route<span class="text-emerald-400">Connect</span></span>
                </div>
                <p class="text-[10px] text-slate-300 font-medium hidden sm:block">Know your route. Know your time.</p>
            </div>
        </div>

        <!-- RIGHT HALF: Registration Form -->
        <div class="lg:col-span-7 p-8 sm:p-10 lg:p-12 flex flex-col justify-between bg-white overflow-y-auto max-h-[90vh] lg:max-h-none">
            <div>
                <!-- Form Top Header Badge & Title -->
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shadow-xs">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <span class="text-xs font-extrabold tracking-wide text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                            Create Account
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Join RouteConnect</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Fill in your details to create your account and start your journey.</p>
                </div>

                <!-- Validation Error Alert -->
                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl p-3.5 mb-6 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-rose-800 mb-0.5">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Please fix the errors below:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-600 font-semibold pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Role Selector Pill Tabs -->
                    <div class="bg-slate-100 p-1.5 rounded-2xl grid grid-cols-2 gap-1.5 border border-slate-200/80 mb-5">
                        <!-- User / Passenger Role Tab -->
                        <label id="tabUser" class="relative flex items-center justify-center gap-2 py-3 px-4 rounded-xl cursor-pointer font-bold text-xs transition-all bg-emerald-700 text-white shadow-md shadow-emerald-700/20">
                            <input type="radio" name="role" value="passenger" {{ ($selectedRole ?? 'passenger') === 'passenger' ? 'checked' : '' }} onchange="toggleRoleSelection('passenger')" class="sr-only">
                            <i class="fa-solid fa-user text-xs"></i>
                            <span>Register as a User</span>
                        </label>

                        <!-- Driver Role Tab -->
                        <label id="tabDriver" class="relative flex items-center justify-center gap-2 py-3 px-4 rounded-xl cursor-pointer font-bold text-xs transition-all text-slate-600 hover:text-slate-900 hover:bg-white/60">
                            <input type="radio" name="role" value="driver" {{ ($selectedRole ?? '') === 'driver' ? 'checked' : '' }} onchange="toggleRoleSelection('driver')" class="sr-only">
                            <i class="fa-solid fa-steering-wheel text-xs"></i>
                            <span>Register as a Driver</span>
                        </label>
                    </div>

                    <!-- Driver Application Note -->
                    <div id="driverNoticeText" class="{{ ($selectedRole ?? '') === 'driver' ? '' : 'hidden' }} p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5 mb-4">
                        <i class="fa-solid fa-circle-info text-amber-600 text-sm mt-0.5 shrink-0"></i>
                        <span><strong>Driver Application:</strong> Please complete all driver verification fields below. Driver accounts require administrator approval before logging into the driver portal.</span>
                    </div>

                    <!-- Input Grid: Basic Fields (Used for both Passenger and Driver) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Phone Number <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="03XX-XXXXXXX" required class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
                            </div>
                        </div>

                        <!-- Profile Picture Upload -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Profile Picture</label>
                            <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200 rounded-xl p-1.5">
                                <div class="w-8 h-8 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center shrink-0 border border-slate-300">
                                    <img id="avatarPreview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                                    <i id="avatarPlaceholder" class="fa-solid fa-camera text-slate-400 text-xs"></i>
                                </div>
                                <input type="file" name="image" accept="image/*" onchange="previewImage(this)" class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- EXTRA DRIVER APPLICATION FIELDS (CNIC, Gender, DOB, Bio/About) -->
                    <!-- Shown ONLY when Register as a Driver tab is selected -->
                    <div id="driverExtraFields" class="{{ ($selectedRole ?? '') === 'driver' ? '' : 'hidden' }} space-y-4 pt-2 border-t border-slate-100">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg inline-block border border-indigo-100 mb-1">
                            <i class="fa-solid fa-id-card"></i> Driver Profile Attributes
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- CNIC Number -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">CNIC Number <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                        <i class="fa-solid fa-id-card"></i>
                                    </span>
                                    <input type="text" name="cnic" value="{{ old('cnic') }}" placeholder="71501-1234567-1" class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                                </div>
                            </div>

                            <!-- Gender Selection -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Gender</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                        <i class="fa-solid fa-venus-mars"></i>
                                    </span>
                                    <select name="gender" class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Date of Birth -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Date of Birth (DOB)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                        <i class="fa-solid fa-calendar"></i>
                                    </span>
                                    <input type="date" name="dob" value="{{ old('dob') }}" class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Bio / About -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Bio / About Experience</label>
                            <textarea name="bio" rows="2" placeholder="Briefly describe your commercial driving experience & route knowledge..." class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">{{ old('bio') }}</textarea>
                        </div>
                    </div>

                    <!-- Password & Confirm Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="password" required placeholder="Enter your password" class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Confirm Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                <input type="password" name="password_confirmation" required placeholder="Confirm your password" class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-3 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btnSubmit" class="w-full mt-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-emerald-600/25 text-xs tracking-wide transition-all hover:scale-[1.005] flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Create Account</span>
                    </button>
                </form>

                <!-- OR Divider -->
                <div class="relative my-5 text-center">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <span class="relative bg-white px-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">OR</span>
                </div>

                <!-- Google Auth Button Placeholder -->
                <button type="button" onclick="alert('Google Auth integration placeholder')" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 rounded-xl border border-slate-200 text-xs transition-all flex items-center justify-center gap-2.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Continue with Google</span>
                </button>
            </div>

            <!-- Footer Login Link -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-emerald-600 font-extrabold hover:underline ml-1">Login</a>
            </div>
        </div>

    </div>

    <!-- Script for Tab Switching & Image Preview -->
    <script>
        function toggleRoleSelection(role) {
            const tabUser = document.getElementById('tabUser');
            const tabDriver = document.getElementById('tabDriver');
            const notice = document.getElementById('driverNoticeText');
            const extraDriverFields = document.getElementById('driverExtraFields');
            const btn = document.getElementById('btnSubmit');

            if (role === 'passenger') {
                tabUser.className = 'relative flex items-center justify-center gap-2 py-3 px-4 rounded-xl cursor-pointer font-bold text-xs transition-all bg-emerald-700 text-white shadow-md shadow-emerald-700/20';
                tabDriver.className = 'relative flex items-center justify-center gap-2 py-3 px-4 rounded-xl cursor-pointer font-bold text-xs transition-all text-slate-600 hover:text-slate-900 hover:bg-white/60';
                notice.classList.add('hidden');
                extraDriverFields.classList.add('hidden');
                btn.className = 'w-full mt-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-emerald-600/25 text-xs tracking-wide transition-all hover:scale-[1.005] flex items-center justify-center gap-2';
                btn.innerHTML = '<i class="fa-solid fa-user-plus"></i><span>Create Account</span>';
            } else {
                tabDriver.className = 'relative flex items-center justify-center gap-2 py-3 px-4 rounded-xl cursor-pointer font-bold text-xs transition-all bg-indigo-700 text-white shadow-md shadow-indigo-700/20';
                tabUser.className = 'relative flex items-center justify-center gap-2 py-3 px-4 rounded-xl cursor-pointer font-bold text-xs transition-all text-slate-600 hover:text-slate-900 hover:bg-white/60';
                notice.classList.remove('hidden');
                extraDriverFields.classList.remove('hidden');
                btn.className = 'w-full mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-indigo-600/25 text-xs tracking-wide transition-all hover:scale-[1.005] flex items-center justify-center gap-2';
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i><span>Submit Driver Application</span>';
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

        document.addEventListener('DOMContentLoaded', function() {
            const initialRole = "{{ $selectedRole ?? 'passenger' }}";
            toggleRoleSelection(initialRole);
        });
    </script>
</body>
</html>
