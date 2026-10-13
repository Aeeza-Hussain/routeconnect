@extends('backend.admin.layouts.app')

@section('title', 'Vehicle Details: ' . $vehicle->registration_no . ' — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-van-shuttle"></i> Vehicle Overview
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono">
            {{ $vehicle->registration_no }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">{{ $vehicle->model }} · {{ $vehicle->type }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-bold border border-indigo-200 transition-colors">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
        <a href="{{ route('admin.vehicles.index') }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="max-w-3xl space-y-6">

    {{-- ─── Vehicle Header Banner ─── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Dark banner --}}
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/10 text-white flex items-center justify-center text-3xl border border-white/20 shrink-0">
                    @if ($vehicle->type === 'Bus')
                        <i class="fa-solid fa-bus"></i>
                    @elseif ($vehicle->type === 'Car')
                        <i class="fa-solid fa-car"></i>
                    @else
                        <i class="fa-solid fa-van-shuttle"></i>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-2xl font-black text-white font-mono tracking-wide">
                            {{ $vehicle->registration_no }}
                        </h3>
                        @if ($vehicle->isPending())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-extrabold border border-amber-500/30">
                                <i class="fa-solid fa-hourglass-half animate-pulse"></i> Pending Review
                            </span>
                        @elseif ($vehicle->isRejected())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 text-xs font-extrabold border border-rose-500/30">
                                <i class="fa-solid fa-circle-xmark"></i> Rejected
                            </span>
                        @elseif ($vehicle->isApproved())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-extrabold border border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Approved (Active)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-500/20 text-slate-300 text-xs font-extrabold border border-slate-500/30">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-300 text-sm font-semibold mt-1">{{ $vehicle->model }}</p>
                    <p class="text-slate-400 text-xs mt-0.5">
                        Registered on {{ $vehicle->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Vehicle Specs Grid --}}
        <div class="p-6">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Specifications</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Vehicle Number</div>
                    <div class="text-base font-black text-slate-900 font-mono">{{ $vehicle->registration_no }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Model</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $vehicle->model }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Vehicle Type</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $vehicle->type }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Passenger Seats</div>
                    <div class="text-sm font-extrabold text-emerald-600 flex items-center gap-1.5">
                        <i class="fa-solid fa-chair text-xs"></i>
                        {{ $vehicle->total_seats }} Seats
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Approval Status</div>
                    <div class="text-sm font-extrabold capitalize {{ $vehicle->isApproved() ? 'text-emerald-600' : ($vehicle->isRejected() ? 'text-rose-600' : 'text-amber-600') }}">
                        {{ $vehicle->status }}
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Associated Trips</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        {{ $vehicle->trips->count() }} Trips
                    </div>
                </div>

            </div>

            {{-- Rejection Reason Alert if rejected --}}
            @if ($vehicle->isRejected() && $vehicle->rejection_reason)
                <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                    <div class="font-extrabold text-rose-900 flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>Vehicle Rejection Reason:</span>
                    </div>
                    <p class="leading-relaxed">{{ $vehicle->rejection_reason }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Approval & Verification Decision Card ─── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Independent Vehicle Approval Decision</h4>

        @if ($vehicle->isApproved())
            {{-- Approved --}}
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-4">
                <i class="fa-solid fa-circle-check text-emerald-600 text-2xl shrink-0"></i>
                <div>
                    <div class="font-extrabold text-emerald-800 text-sm">This vehicle is approved and active.</div>
                    <p class="text-xs text-emerald-700 mt-0.5">Eligible for the assigned driver to operate and schedule public commercial trips.</p>
                </div>
            </div>

            <form action="{{ route('admin.vehicles.reject', $vehicle->id) }}" method="POST" class="p-4 rounded-xl bg-rose-50/50 border border-rose-200 space-y-3">
                @csrf
                <label for="veh_revoke_reason" class="block text-xs font-bold text-rose-900">
                    Revoke Approval (Reject Vehicle):
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text"
                           id="veh_revoke_reason"
                           name="rejection_reason"
                           placeholder="Optional reason for revoking approval..."
                           class="flex-1 px-3.5 py-2.5 rounded-xl border border-rose-200 bg-white text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <button type="submit"
                            onclick="return confirm('Revoke approval for vehicle \'{{ addslashes($vehicle->registration_no) }}\'?')"
                            class="shrink-0 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-all shadow-sm inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-ban"></i> Revoke & Reject
                    </button>
                </div>
            </form>

        @elseif ($vehicle->isRejected())
            {{-- Rejected --}}
            <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl mb-4">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-2xl shrink-0"></i>
                <div>
                    <div class="font-extrabold text-rose-800 text-sm">This vehicle registration was rejected.</div>
                    <p class="text-xs text-rose-700 mt-0.5">This vehicle cannot be assigned to public trips.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <form action="{{ route('admin.vehicles.approve', $vehicle->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all shadow-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Re-Approve Vehicle
                    </button>
                </form>
            </div>

        @else
            {{-- Pending Review --}}
            <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-5">
                <i class="fa-solid fa-clock text-amber-600 text-2xl shrink-0"></i>
                <div>
                    <div class="font-extrabold text-amber-800 text-sm">Vehicle Pending Verification</div>
                    <p class="text-xs text-amber-700 mt-0.5">Please review the vehicle registration number, passenger seating, and assigned driver details.</p>
                </div>
            </div>

            <form action="{{ route('admin.vehicles.reject', $vehicle->id) }}" method="POST" class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                @csrf
                <label for="veh_rejection_reason" class="block text-xs font-bold text-slate-700">
                    Optional Rejection Reason:
                </label>
                <textarea id="veh_rejection_reason"
                          name="rejection_reason"
                          rows="2"
                          placeholder="e.g. Expired vehicle inspection, incorrect passenger capacity, or invalid number plate..."
                          class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit"
                            onclick="return confirm('Reject this vehicle registration?')"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-all border border-rose-200 inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-xmark"></i> Reject Vehicle
                    </button>

                    <button type="submit"
                            formaction="{{ route('admin.vehicles.approve', $vehicle->id) }}"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition-all shadow-sm shadow-emerald-600/20 inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Approve Vehicle
                    </button>
                </div>
            </form>
        @endif

    </div>

    {{-- ─── Assigned Driver Card ─── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-id-card text-emerald-500"></i>
            Assigned Driver Information
        </h4>

        @if ($vehicle->driver)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                <div class="flex items-center gap-4">
                    @if ($vehicle->driver->profile_photo)
                        <img src="{{ asset($vehicle->driver->profile_photo) }}"
                             alt="{{ $vehicle->driver->name }}"
                             class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-800 font-extrabold flex items-center justify-center text-sm border border-teal-200">
                            {{ strtoupper(substr($vehicle->driver->name, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.drivers.show', $vehicle->driver->id) }}"
                               class="font-extrabold text-slate-900 text-sm hover:text-emerald-600 transition-colors">
                                {{ $vehicle->driver->name }}
                            </a>
                            @if ($vehicle->driver->driver_status === 'approved')
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Driver Approved</span>
                            @elseif ($vehicle->driver->driver_status === 'pending')
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Driver Pending</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">Driver Rejected</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500">{{ $vehicle->driver->email }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Phone: {{ $vehicle->driver->phone ?? 'Not provided' }} · License: {{ $vehicle->driver->license_no ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.drivers.show', $vehicle->driver->id) }}"
                       class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-id-card text-[10px]"></i> View Driver Application
                    </a>
                </div>
            </div>
        @else
            <div class="p-6 rounded-xl bg-slate-50 border border-slate-100 text-center text-slate-400 text-xs">
                No driver currently assigned to this vehicle.
            </div>
        @endif
    </div>

    {{-- ─── Actions Card ─── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-sm text-slate-500 font-semibold">
            <i class="fa-solid fa-gear mr-1 text-slate-400"></i>
            Manage vehicle record:
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}"
               class="px-5 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-sm border border-indigo-200 transition-colors inline-flex items-center gap-2">
                <i class="fa-solid fa-pen"></i> Edit Vehicle
            </a>

            <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST"
                  onsubmit="return confirm('Delete vehicle \'{{ addslashes($vehicle->registration_no) }}\'? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-sm border border-rose-200 transition-colors inline-flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i> Delete Vehicle
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
