@extends('backend.admin.layouts.app')

@section('title', $title . ' — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid {{ $icon }}"></i> Platform Module
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $title }}</h2>
        <p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>
    </div>
    <a href="{{ route('admin.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

{{-- ─── Module Card ─── --}}
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-8 text-center">
        <div class="w-20 h-20 rounded-2xl {{ $iconBg }} flex items-center justify-center text-3xl mx-auto mb-5 border shadow-xs">
            <i class="fa-solid {{ $icon }}"></i>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200 mb-3">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Database Schema Ready
        </span>

        <h3 class="text-xl font-extrabold text-slate-900 mb-2">{{ $title }}</h3>
        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
            {{ $subtitle }} Full management operations for {{ strtolower($moduleName) }} will be active in the next step.
        </p>

        {{-- Database count preview --}}
        <div class="inline-flex items-center gap-4 px-5 py-3 rounded-xl bg-slate-50 border border-slate-200 text-left mb-6">
            <div class="text-2xl font-black text-slate-900">{{ $tableCount }}</div>
            <div class="text-xs text-slate-500 font-semibold">
                <span class="font-bold text-slate-700 block">{{ $itemLabel }}</span>
                Currently recorded in database
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-gauge"></i> Return to Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-users"></i> Manage Users
            </a>
        </div>
    </div>
</div>

@endsection
