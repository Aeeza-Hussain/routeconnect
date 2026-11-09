@extends('frontend.layouts.app')

@section('title', 'My Profile — RouteConnect')

@section('content')
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.2),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20 mb-2">
                    <i class="fa-solid fa-user-circle"></i> Passenger Profile
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">My Profile</h1>
                <p class="text-sm text-slate-400 mt-1">Manage your passenger details, contact information, and travel preferences.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-user-pen"></i> Edit Profile
                </a>
                <a href="{{ route('profile.settings') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-gear"></i> Settings
                </a>
            </div>
        </div>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm font-bold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Profile Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs text-center">
            <div class="w-24 h-24 rounded-full bg-emerald-600 text-white font-extrabold text-3xl flex items-center justify-center mx-auto shadow-lg shadow-emerald-600/20 border-4 border-white dark:border-slate-800">
                {{ collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') ?: 'RC' }}
            </div>
            <h2 class="mt-4 text-lg font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
            <span class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                Passenger Account
            </span>

            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3 text-left text-xs">
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                    <span>Member Since</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $user->created_at?->format('M Y') ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                    <span>Phone</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $user->phone ?? 'Not provided' }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                    <span>Gender</span>
                    <span class="font-bold text-slate-900 dark:text-white capitalize">{{ $user->gender ?? 'Not specified' }}</span>
                </div>
            </div>
        </div>

        {{-- Details & Actions --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-emerald-600"></i> Personal Details
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400 font-bold">Full Name</dt>
                        <dd class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400 font-bold">Email Address</dt>
                        <dd class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400 font-bold">Contact Phone</dt>
                        <dd class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $user->phone ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400 font-bold">Emergency Contact</dt>
                        <dd class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ $user->emergency_contact_name ? $user->emergency_contact_name . ' (' . $user->emergency_contact_phone . ')' : 'None added' }}
                        </dd>
                    </div>
                </dl>
                @if($user->bio)
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 font-bold">About Me</dt>
                        <dd class="mt-1 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $user->bio }}</dd>
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Travel History & Requests</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">View your current trips, booked seats, and driver proposals in My Activity.</p>
                </div>
                <a href="{{ route('activity.index') }}" class="shrink-0 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 font-bold text-xs transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left"></i> My Activity
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
