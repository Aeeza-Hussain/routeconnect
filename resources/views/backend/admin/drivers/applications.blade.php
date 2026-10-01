@extends('backend.admin.layouts.app')

@section('title', 'Driver Applications — RouteConnect Admin')

@section('content')

{{-- ───────────────────────────── PAGE HEADER ───────────────────────────── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-id-card"></i> Application Management
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Driver Applications</h2>
        <p class="text-sm text-slate-500 mt-1">
            Review, approve, or reject driver applications registered on RouteConnect.
        </p>
    </div>
    <a href="{{ route('admin.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
        <i class="fa-solid fa-gauge"></i> Back to Dashboard
    </a>
</div>

{{-- ───────────────────────── FLASH MESSAGES ───────────────────────── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ─────────────────────── STATUS FILTER TABS ──────────────────────── --}}
<div class="flex flex-wrap gap-2 mb-6">

    {{-- All Tab --}}
    <a href="{{ route('admin.drivers.applications', ['status' => 'all']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border transition-all
              {{ $status === 'all' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300' }}">
        <i class="fa-solid fa-list"></i>
        All Applications
        <span class="px-2 py-0.5 rounded-full {{ $status === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }} text-[10px] font-extrabold">
            {{ $counts['all'] }}
        </span>
    </a>

    {{-- Pending Tab --}}
    <a href="{{ route('admin.drivers.applications', ['status' => 'pending']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border transition-all
              {{ $status === 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-amber-200' }}">
        <i class="fa-solid fa-clock"></i>
        Pending
        <span class="px-2 py-0.5 rounded-full {{ $status === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }} text-[10px] font-extrabold">
            {{ $counts['pending'] }}
        </span>
    </a>

    {{-- Approved Tab --}}
    <a href="{{ route('admin.drivers.applications', ['status' => 'approved']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border transition-all
              {{ $status === 'approved' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-emerald-200' }}">
        <i class="fa-solid fa-circle-check"></i>
        Approved
        <span class="px-2 py-0.5 rounded-full {{ $status === 'approved' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }} text-[10px] font-extrabold">
            {{ $counts['approved'] }}
        </span>
    </a>

    {{-- Rejected Tab --}}
    <a href="{{ route('admin.drivers.applications', ['status' => 'rejected']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border transition-all
              {{ $status === 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-rose-200' }}">
        <i class="fa-solid fa-circle-xmark"></i>
        Rejected
        <span class="px-2 py-0.5 rounded-full {{ $status === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700' }} text-[10px] font-extrabold">
            {{ $counts['rejected'] }}
        </span>
    </a>

</div>

{{-- ─────────────────────── APPLICATIONS TABLE ──────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Table Sub-header --}}
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-bold text-slate-700">
            @if ($status === 'all')
                All Drivers ({{ $applications->count() }})
            @elseif ($status === 'pending')
                <span class="text-amber-600">Pending Applications ({{ $applications->count() }})</span>
            @elseif ($status === 'approved')
                <span class="text-emerald-600">Approved Drivers ({{ $applications->count() }})</span>
            @else
                <span class="text-rose-600">Rejected Applications ({{ $applications->count() }})</span>
            @endif
        </div>
    </div>

    @if ($applications->isEmpty())
        {{-- Empty State --}}
        <div class="p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700 mb-1">No records found</div>
            <p class="text-xs text-slate-500">
                @if ($status === 'pending')
                    There are no pending driver applications at this time.
                @elseif ($status === 'approved')
                    No approved drivers found.
                @elseif ($status === 'rejected')
                    No rejected applications found.
                @else
                    No driver applications found in the system.
                @endif
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 w-10">#</th>
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">Email Address</th>
                        <th class="px-6 py-3">Phone Number</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Registration Date</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($applications as $i => $driver)
                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- Row Number --}}
                            <td class="px-6 py-4 text-slate-400 font-bold">{{ $i + 1 }}</td>

                            {{-- Driver Name + Avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($driver->profile_photo)
                                        <img src="{{ asset($driver->profile_photo) }}"
                                             alt="{{ $driver->name }}"
                                             class="w-10 h-10 rounded-xl object-cover border-2 border-slate-200 shadow-xs">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-sm border border-indigo-200">
                                            {{ strtoupper(substr($driver->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-extrabold text-slate-900">{{ $driver->name }}</div>
                                        @if ($driver->cnic)
                                            <div class="text-[10px] text-slate-400 mt-0.5">CNIC: {{ $driver->cnic }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-slate-600">{{ $driver->email }}</td>

                            {{-- Phone --}}
                            <td class="px-6 py-4 text-slate-600">{{ $driver->phone ?? '—' }}</td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @if ($driver->driver_status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Approved
                                    </span>
                                @elseif ($driver->driver_status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold border border-amber-200">
                                        <i class="fa-solid fa-clock text-amber-500"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-extrabold border border-rose-200">
                                        <i class="fa-solid fa-circle-xmark text-rose-500"></i> Rejected
                                    </span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 text-slate-500">{{ $driver->created_at->format('d M Y') }}</td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View Details --}}
                                    <a href="{{ route('admin.drivers.show', $driver->id) }}"
                                       class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold transition-colors inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye text-[10px]"></i> Details
                                    </a>

                                    {{-- Approve Button (only if not already approved) --}}
                                    @if ($driver->driver_status !== 'approved')
                                        <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors inline-flex items-center gap-1">
                                                <i class="fa-solid fa-check text-[10px]"></i> Approve
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reject Button (only if not already rejected) --}}
                                    @if ($driver->driver_status !== 'rejected')
                                        <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Reject this driver application?')"
                                                    class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold transition-colors inline-flex items-center gap-1">
                                                <i class="fa-solid fa-xmark text-[10px]"></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

@endsection
