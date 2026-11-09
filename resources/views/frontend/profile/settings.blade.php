@extends('frontend.layouts.app')

@section('title', 'Account Settings — RouteConnect')

@section('content')
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-emerald-400 transition-colors mb-3">
            <i class="fa-solid fa-arrow-left"></i> Back to Profile
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Account Settings</h1>
        <p class="text-sm text-slate-400 mt-1">Manage your account security and display preferences.</p>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm font-bold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Appearance Theme Setting --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
            <i class="fa-solid fa-circle-half-stroke text-emerald-600"></i> Appearance Theme
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Choose your preferred visual theme across RouteConnect.</p>

        <div class="grid grid-cols-3 gap-3">
            <button type="button" onclick="window.RouteConnectTheme.setTheme('light')" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 text-center transition-all group">
                <i class="fa-solid fa-sun text-amber-500 text-lg mb-1"></i>
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Light</div>
            </button>
            <button type="button" onclick="window.RouteConnectTheme.setTheme('dark')" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 text-center transition-all group">
                <i class="fa-solid fa-moon text-indigo-400 text-lg mb-1"></i>
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Dark</div>
            </button>
            <button type="button" onclick="window.RouteConnectTheme.setTheme('system')" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 text-center transition-all group">
                <i class="fa-solid fa-desktop text-emerald-500 text-lg mb-1"></i>
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">System</div>
            </button>
        </div>
    </div>

    {{-- Security / Change Password --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
            <i class="fa-solid fa-lock text-emerald-600"></i> Security & Password
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Ensure your account uses a secure password of at least 8 characters.</p>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-semibold space-y-1">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('profile.settings.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Current Password *</label>
                <input type="password" name="current_password" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">New Password *</label>
                    <input type="password" name="new_password" required minlength="8" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Confirm New Password *</label>
                    <input type="password" name="new_password_confirmation" required minlength="8" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
