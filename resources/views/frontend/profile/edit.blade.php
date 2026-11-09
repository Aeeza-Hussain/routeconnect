@extends('frontend.layouts.app')

@section('title', 'Edit Profile — RouteConnect')

@section('content')
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-emerald-400 transition-colors mb-3">
            <i class="fa-solid fa-arrow-left"></i> Back to Profile
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Edit Profile</h1>
        <p class="text-sm text-slate-400 mt-1">Update your personal information and contact details.</p>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-semibold space-y-1">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Contact Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 03001234567" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Gender</label>
                <select name="gender" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" placeholder="Name of contact" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Emergency Contact Phone</label>
                <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" placeholder="Phone number" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bio / Travel Preferences</label>
            <textarea name="bio" rows="3" placeholder="Share a few words about your travel habits or preferences..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('bio', $user->bio) }}</textarea>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
            <a href="{{ route('profile.show') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
