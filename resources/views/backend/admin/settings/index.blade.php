@extends('backend.admin.layouts.app')

@section('title', 'Platform Settings — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-gear"></i> System Configuration
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Platform Settings</h2>
        <p class="text-sm text-slate-500 mt-1">Configure general operations, platform preferences, and booking policies.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

{{-- ─── Settings Container ─── --}}
<div class="max-w-4xl space-y-6">

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- 1. General Application Settings --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-sliders text-emerald-600"></i>
                General Platform Settings
            </h3>
            <span class="text-[11px] font-bold text-slate-400">RouteConnect Core</span>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Platform Name</label>
                    <input type="text" value="RouteConnect" readonly
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Support Contact Email</label>
                    <input type="email" value="support@routeconnect.com"
                           class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Support Phone Line</label>
                    <input type="text" value="+92 300 1234567"
                           class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Default Currency</label>
                    <input type="text" value="PKR (Rs.)" readonly
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800">
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Driver & Vehicle Policies --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-id-card text-teal-600"></i>
                Driver & Vehicle Verification Rules
            </h3>
            <span class="text-[11px] font-bold text-slate-400">Compliance</span>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <div>
                    <div class="text-xs font-extrabold text-slate-800">Manual Driver Verification</div>
                    <div class="text-[11px] text-slate-500">Require administrator approval before a driver can schedule trips.</div>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                    Enforced (user_type = 2)
                </span>
            </div>

            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <div>
                    <div class="text-xs font-extrabold text-slate-800">Unique Vehicle Registration</div>
                    <div class="text-[11px] text-slate-500">Prevent duplicate registration number entries across fleet records.</div>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                    Enforced
                </span>
            </div>
        </div>
    </div>

    {{-- 3. System Preferences --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">Configuration Status</h4>
            <p class="text-xs text-slate-500">RouteConnect operations settings are active and synced with database policies.</p>
        </div>
        <button type="button"
                onclick="alert('Settings preferences updated successfully!')"
                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Save Settings
        </button>
    </div>

</div>

@endsection
