@extends('backend.admin.layouts.app')

@section('title', 'Approved Drivers — RouteConnect Admin')

@section('content')

{{-- ───────────────────────────── PAGE HEADER ───────────────────────────── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-user-check"></i> Driver Registry
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Approved Drivers</h2>
        <p class="text-sm text-slate-500 mt-1">
            Verified drivers who have active access to the RouteConnect Driver Console.
        </p>
    </div>
    <a href="{{ route('admin.drivers.applications', ['status' => 'pending']) }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200 transition-colors">
        <i class="fa-solid fa-clock"></i> Pending Applications
    </a>
</div>

{{-- ─────────────────────── FLASH MESSAGES ─────────────────────── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ─────────────────────── DRIVERS TABLE ──────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Table Sub-header --}}
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-bold text-emerald-700">
            <i class="fa-solid fa-circle-check mr-1"></i>
            {{ $drivers->count() }} Approved {{ Str::plural('Driver', $drivers->count()) }}
        </div>
    </div>

    @if ($drivers->isEmpty())
        {{-- Empty State --}}
        <div class="p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-users-slash"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700 mb-1">No approved drivers yet.</div>
            <p class="text-xs text-slate-500 mb-4">Approve pending applications to see drivers here.</p>
            <a href="{{ route('admin.drivers.applications', ['status' => 'pending']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200 transition-colors">
                <i class="fa-solid fa-clock"></i> Review Pending Applications
            </a>
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
                        <th class="px-6 py-3">Joined On</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($drivers as $i => $driver)
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
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-sm border border-emerald-200">
                                            {{ strtoupper(substr($driver->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-extrabold text-slate-900">{{ $driver->name }}</div>
                                        @if ($driver->gender)
                                            <div class="text-[10px] text-slate-400 capitalize mt-0.5">{{ $driver->gender }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-600">{{ $driver->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $driver->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $driver->created_at->format('d M Y') }}</td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Approved
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.drivers.show', $driver->id) }}"
                                       class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold transition-colors inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye text-[10px]"></i> View
                                    </a>
                                    <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Revoke access for {{ $driver->name }}?')"
                                                class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold transition-colors inline-flex items-center gap-1 border border-rose-200">
                                            <i class="fa-solid fa-ban text-[10px]"></i> Revoke
                                        </button>
                                    </form>
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
