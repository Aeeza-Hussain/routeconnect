@extends('backend.driver.layouts.app')

@section('title', 'My Profile — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-user"></i> Driver Account
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Commercial Driver Profile</h2>
        <p class="text-sm text-slate-400 mt-1">Your verified personal and professional driver credentials.</p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

<div class="max-w-3xl space-y-6">

    {{-- Profile Banner Card --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 flex flex-col sm:flex-row sm:items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-teal-600 text-white font-extrabold flex items-center justify-center text-2xl shadow-lg shrink-0">
            {{ strtoupper(substr($driver->name, 0, 2)) }}
        </div>
        <div class="flex-1">
            <h3 class="text-xl font-extrabold text-white flex items-center gap-2">
                {{ $driver->name }}
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-extrabold border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Verified
                </span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ $driver->email }}</p>
            <p class="text-[11px] text-slate-500 mt-1">
                Account registered on {{ $driver->created_at->format('d M Y') }}
            </p>
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Credentials & Attributes</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Phone Number</span>
                <span class="font-extrabold text-white">{{ $driver->phone ?? 'Not provided' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">CNIC Number</span>
                <span class="font-extrabold text-white">{{ $driver->cnic ?? 'Not provided' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Commercial License No</span>
                <span class="font-extrabold text-teal-400 font-mono">{{ $driver->license_no ?? 'DL-VERIFIED' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Account Role</span>
                <span class="font-extrabold text-white">Commercial Driver (user_type = 2)</span>
            </div>
        </div>

        @if ($driver->bio)
            <div class="mt-4 p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Driver Bio & Route Knowledge</span>
                <p class="text-xs text-slate-300 leading-relaxed">{{ $driver->bio }}</p>
            </div>
        @endif
    </div>

</div>

@endsection
