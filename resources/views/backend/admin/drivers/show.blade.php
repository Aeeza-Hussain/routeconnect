@extends('backend.admin.layouts.app')

@section('title', 'Driver Application Details — RouteConnect Admin')

@section('content')

{{-- ───────────────────────────── PAGE HEADER ───────────────────────────── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-id-card"></i> Application Detail
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            {{ $driver->name }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">Full driver profile and application status management.</p>
    </div>
    <a href="{{ route('admin.drivers.applications') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Applications
    </a>
</div>

{{-- ─────────────────────── FLASH MESSAGES ─────────────────────── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="max-w-4xl space-y-6">

    {{-- ─────────────────── PROFILE CARD ─────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Card Header with avatar and status --}}
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                {{-- Profile Photo --}}
                @if ($driver->profile_photo)
                    <img src="{{ asset($driver->profile_photo) }}"
                         alt="{{ $driver->name }}"
                         class="w-20 h-20 rounded-2xl object-cover border-4 border-white/20 shadow-lg">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-2xl border-4 border-white/20 shadow-lg">
                        {{ strtoupper(substr($driver->name, 0, 2)) }}
                    </div>
                @endif

                {{-- Name + Info --}}
                <div class="flex-1">
                    <h3 class="text-xl font-extrabold text-white">{{ $driver->name }}</h3>
                    <p class="text-sm text-slate-400 mt-0.5">{{ $driver->email }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Registered: {{ $driver->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>

                {{-- Status Badge --}}
                <div>
                    @if ($driver->driver_status === 'approved')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-300 text-sm font-extrabold border border-emerald-500/30">
                            <i class="fa-solid fa-circle-check"></i> Approved
                        </span>
                    @elseif ($driver->driver_status === 'pending')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500/20 text-amber-300 text-sm font-extrabold border border-amber-500/30">
                            <i class="fa-solid fa-clock"></i> Pending Review
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-500/20 text-rose-300 text-sm font-extrabold border border-rose-500/30">
                            <i class="fa-solid fa-circle-xmark"></i> Rejected
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Driver Info Grid --}}
        <div class="p-6">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Personal Information</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->name }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->email }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Phone Number</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->phone ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">CNIC Number</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->cnic ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</div>
                    <div class="text-sm font-extrabold text-slate-900 capitalize">{{ $driver->gender ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Date of Birth</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        {{ $driver->dob ? \Carbon\Carbon::parse($driver->dob)->format('d M Y') : 'Not Provided' }}
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">User Type</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        Driver (user_type = {{ $driver->user_type }})
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Current Status</div>
                    <div class="text-sm font-extrabold capitalize
                        {{ $driver->driver_status === 'approved' ? 'text-emerald-600' : ($driver->driver_status === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                        {{ ucfirst($driver->driver_status) }}
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Registration Date</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->created_at->format('d M Y, h:i A') }}</div>
                </div>

            </div>

            {{-- Bio / About --}}
            @if ($driver->bio)
                <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Bio / About</div>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $driver->bio }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ─────────────────── APPROVAL ACTION CARD ─────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Approval Actions</h4>

        @if ($driver->driver_status === 'approved')
            {{-- Already Approved --}}
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <i class="fa-solid fa-circle-check text-emerald-600 text-2xl"></i>
                <div>
                    <div class="font-extrabold text-emerald-800 text-sm">This driver is currently approved.</div>
                    <p class="text-xs text-emerald-700 mt-0.5">They have active access to the Driver Dashboard.</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Revoke this driver\'s access? They will be rejected.')"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm transition-all shadow-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-ban"></i> Revoke Access (Reject)
                    </button>
                </form>
            </div>

        @elseif ($driver->driver_status === 'rejected')
            {{-- Already Rejected --}}
            <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-2xl"></i>
                <div>
                    <div class="font-extrabold text-rose-800 text-sm">This driver application was rejected.</div>
                    <p class="text-xs text-rose-700 mt-0.5">They do not have access to the Driver Dashboard.</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all shadow-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i> Re-Approve Driver
                    </button>
                </form>
            </div>

        @else
            {{-- Pending — Show both Approve and Reject --}}
            <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-5">
                <i class="fa-solid fa-clock text-amber-600 text-2xl"></i>
                <div>
                    <div class="font-extrabold text-amber-800 text-sm">Pending Review</div>
                    <p class="text-xs text-amber-700 mt-0.5">This application is waiting for your decision. Please review the details above before acting.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Reject this driver application?')"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-sm transition-all border border-rose-200 inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-xmark"></i> Reject Application
                    </button>
                </form>
                <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all shadow-sm shadow-emerald-600/20 inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Approve Driver Application
                    </button>
                </form>
            </div>
        @endif

    </div>

</div>

@endsection
