@extends('backend.admin.layouts.app')

@section('title', 'Driver Application Details — RouteConnect Admin')

@section('content')

<!-- Header Title -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-id-card"></i> Application Details
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Driver Application: {{ $driver->name }}</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Review full registration details and update application approval status.</p>
    </div>

    <a href="{{ route('admin.drivers.applications') }}" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition-colors inline-flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Back to Applications
    </a>
</div>

<!-- Details Card -->
<div class="max-w-3xl bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
    
    <div class="flex items-center justify-between pb-6 border-b border-slate-100">
        <div class="flex items-center gap-4">
            @if ($driver->profile_photo)
                <img src="{{ asset($driver->profile_photo) }}" alt="{{ $driver->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-indigo-200 shadow-xs">
            @else
                <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-xl shadow-xs">
                    {{ strtoupper(substr($driver->name, 0, 2)) }}
                </div>
            @endif
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">{{ $driver->name }}</h3>
                <span class="text-xs text-slate-500">Registered Driver Application</span>
            </div>
        </div>

        <div>
            @if ($driver->driver_status === 'approved')
                <span class="px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold border border-emerald-200">Approved</span>
            @elseif ($driver->driver_status === 'pending')
                <span class="px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold border border-amber-200">Pending Review</span>
            @else
                <span class="px-3.5 py-1.5 rounded-full bg-rose-100 text-rose-800 text-xs font-extrabold border border-rose-200">Rejected</span>
            @endif
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold">
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <div class="text-[11px] font-bold text-slate-400 uppercase">Full Name</div>
            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ $driver->name }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <div class="text-[11px] font-bold text-slate-400 uppercase">Email Address</div>
            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ $driver->email }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <div class="text-[11px] font-bold text-slate-400 uppercase">Phone Number</div>
            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ $driver->phone ?? 'N/A' }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <div class="text-[11px] font-bold text-slate-400 uppercase">CNIC Number</div>
            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ $driver->cnic ?? 'N/A' }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <div class="text-[11px] font-bold text-slate-400 uppercase">Gender</div>
            <div class="text-sm font-extrabold text-slate-900 mt-1 capitalize">{{ $driver->gender ?? 'N/A' }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <div class="text-[11px] font-bold text-slate-400 uppercase">Date of Birth (DOB)</div>
            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ $driver->dob ? \Carbon\Carbon::parse($driver->dob)->format('d M Y') : 'N/A' }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
            <div class="text-[11px] font-bold text-slate-400 uppercase">Registration Date</div>
            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ $driver->created_at->format('d M Y, h:i A') }}</div>
        </div>

        @if ($driver->bio)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Bio / About</div>
                <div class="text-xs font-medium text-slate-700 mt-1 leading-relaxed">{{ $driver->bio }}</div>
            </div>
        @endif
    </div>

    <!-- Actions Area -->
    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
        @if ($driver->driver_status !== 'approved')
            <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all">
                    Approve Driver Application
                </button>
            </form>
        @endif

        @if ($driver->driver_status !== 'rejected')
            <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition-all">
                    Reject Driver Application
                </button>
            </form>
        @endif
    </div>

</div>

@endsection
