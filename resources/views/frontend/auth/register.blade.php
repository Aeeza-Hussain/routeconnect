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
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col justify-between">
    
    <!-- Top Header -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 py-3.5 px-6">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-xl font-extrabold text-emerald-700 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-route"></i>
                </span>
                <span>Route<span class="text-slate-900">Connect</span></span>
            </a>
            
            <div class="flex items-center gap-3 text-xs font-bold">
                <span class="text-slate-500 hidden sm:inline">Already have an account?</span>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-xs">
                    Log In
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-10 px-4 flex items-center justify-center">
        <div class="w-full max-w-3xl bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-10">
            
            <!-- Form Header Title -->
            <div class="text-center mb-8">
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full border border-emerald-100">
                    User Registration
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2.5">Create Your Profile</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">Fill out the registration details below to join RouteConnect as a passenger or driver.</p>
            </div>

            <!-- Validation Error Alert -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl p-4 mb-6 space-y-1 shadow-xs">
                    <div class="font-bold flex items-center gap-1.5 text-rose-800 mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Please fix the following errors:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-600 font-semibold pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- SECTION 1: ACCOUNT TYPE SELECTOR -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                        1. Choose Account Role
                    </label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Option 1: Passenger -->
                        <label id="labelPassenger" class="relative flex items-center gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all border-emerald-500 bg-emerald-50/70 shadow-xs">
                            <input type="radio" name="role" value="passenger" {{ ($selectedRole ?? 'passenger') === 'passenger' ? 'checked' : '' }} onchange="toggleRoleSelection('passenger')" class="sr-only">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shrink-0">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-xs text-slate-900">Passenger Account</span>
                                    <span id="iconCheckPassenger" class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-bold">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Search scheduled routes & book bus seats</p>
                            </div>
                        </label>

                        <!-- Option 2: Driver -->
                        <label id="labelDriver" class="relative flex items-center gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all border-slate-200 bg-white hover:border-slate-300">
                            <input type="radio" name="role" value="driver" {{ ($selectedRole ?? '') === 'driver' ? 'checked' : '' }} onchange="toggleRoleSelection('driver')" class="sr-only">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shrink-0">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-xs text-slate-900">Driver Application</span>
                                    <span id="iconCheckDriver" class="hidden w-4 h-4 rounded-full bg-indigo-600 text-white text-[10px] items-center justify-center font-bold">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Operate routes & manage scheduled trips</p>
                            </div>
                        </label>
                    </div>

                    <!-- Driver Application Note -->
                    <div id="driverNoticeText" class="{{ ($selectedRole ?? '') === 'driver' ? '' : 'hidden' }} mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0 mt-0.5"></i>
                        <span><strong>Driver Approval Workflow:</strong> Driver applications will be submitted to the Admin Panel. You will gain access to your driver dashboard once approved by an administrator.</span>
                    </div>
                </div>

                <!-- SECTION 2: PROFILE PHOTO UPLOAD -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                        2. Profile Avatar / Photo
                    </label>
                    
                    <div class="flex items-center gap-4">
                        <div class="relative w-16 h-16 rounded-2xl bg-slate-200 border-2 border-slate-300 overflow-hidden flex items-center justify-center shrink-0 shadow-xs group">
                            <img id="avatarPreview" src="" alt="Avatar Preview" class="hidden w-full h-full object-cover">
                            <i id="avatarPlaceholder" class="fa-solid fa-camera text-slate-400 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="image" id="imageInput" accept="image/*" onchange="previewImage(this)" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Upload JPG, PNG or WEBP (Max: 4MB).</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: PERSONAL INFORMATION -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-4">
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                        3. Personal Details
                    </label>

                    <!-- Row 1: Full Name & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Full Name <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Ali Khan" required class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Email Address <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Phone / Contact & CNIC -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Contact Number <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0300-1234567" required class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">CNIC Number</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-id-card"></i>
                                </span>
                                <input type="text" name="cnic" value="{{ old('cnic') }}" placeholder="71501-1234567-1" class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Gender & Date of Birth -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Gender</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-venus-mars"></i>
                                </span>
                                <select name="gender" class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Date of Birth (DOB)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-calendar"></i>
                                </span>
                                <input type="date" name="dob" value="{{ old('dob') }}" class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bio / About -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Bio / About Yourself</label>
                        <textarea name="bio" rows="2" placeholder="Write a short summary about yourself..." class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('bio') }}</textarea>
                    </div>
                </div>

                <!-- SECTION 4: SECURITY / PASSWORD -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-4">
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                        4. Security & Credentials
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Confirm Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                <input type="password" name="password_confirmation" required placeholder="Repeat password" class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" id="btnSubmit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 rounded-2xl shadow-lg shadow-emerald-600/20 text-sm transition-all hover:scale-[1.005] flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Register Account</span>
                </button>
            </form>

            <div class="mt-8 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
                Already have a RouteConnect account? 
                <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline ml-1">Log in here</a>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-400">
        © {{ date('Y') }} RouteConnect Transport System. All rights reserved.
    </footer>

    <!-- Interactive JS Script -->
    <script>
        function toggleRoleSelection(role) {
            const labelP = document.getElementById('labelPassenger');
            const labelD = document.getElementById('labelDriver');
            const iconP = document.getElementById('iconCheckPassenger');
            const iconD = document.getElementById('iconCheckDriver');
            const notice = document.getElementById('driverNoticeText');
            const btn = document.getElementById('btnSubmit');

            if (role === 'passenger') {
                labelP.className = 'relative flex items-center gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all border-emerald-500 bg-emerald-50/70 shadow-xs';
                labelD.className = 'relative flex items-center gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all border-slate-200 bg-white hover:border-slate-300';
                iconP.classList.remove('hidden');
                iconP.classList.add('flex');
                iconD.classList.add('hidden');
                iconD.classList.remove('flex');
                notice.classList.add('hidden');
                btn.className = 'w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 rounded-2xl shadow-lg shadow-emerald-600/20 text-sm transition-all hover:scale-[1.005] flex items-center justify-center gap-2';
                btn.innerHTML = '<i class="fa-solid fa-user-plus"></i><span>Register Passenger Account</span>';
            } else {
                labelD.className = 'relative flex items-center gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all border-indigo-500 bg-indigo-50/70 shadow-xs';
                labelP.className = 'relative flex items-center gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all border-slate-200 bg-white hover:border-slate-300';
                iconD.classList.remove('hidden');
                iconD.classList.add('flex');
                iconP.classList.add('hidden');
                iconP.classList.remove('flex');
                notice.classList.remove('hidden');
                btn.className = 'w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-4 rounded-2xl shadow-lg shadow-indigo-600/20 text-sm transition-all hover:scale-[1.005] flex items-center justify-center gap-2';
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

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const initialRole = "{{ $selectedRole ?? 'passenger' }}";
            toggleRoleSelection(initialRole);
        });
    </script>
</body>
</html>
