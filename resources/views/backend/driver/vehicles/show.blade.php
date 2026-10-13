@extends('backend.driver.layouts.app')

@section('title', 'Vehicle Details: ' . $vehicle->registration_no . ' — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-van-shuttle"></i> Vehicle Record
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-mono">{{ $vehicle->registration_no }}</h2>
        <p class="text-sm text-slate-400 mt-1">
            {{ $vehicle->model }} · {{ $vehicle->type }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('driver.vehicles.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> Back to My Vehicles
        </a>
        @if ($vehicle->isApproved())
            <a href="{{ route('driver.trips.create') }}?vehicle_id={{ $vehicle->id }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-teal-500/20">
                <i class="fa-solid fa-calendar-plus"></i> Schedule Trip
            </a>
        @endif
    </div>
</div>

<div class="max-w-3xl space-y-6">

    {{-- ─── Vehicle Card ─── --}}
    <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-800/80 pb-6 mb-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-3xl border border-teal-500/20 shrink-0">
                    <i class="fa-solid fa-van-shuttle"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-2xl font-black text-white font-mono tracking-wide">{{ $vehicle->registration_no }}</h3>
                        @if ($vehicle->isApproved())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Approved
                            </span>
                        @elseif ($vehicle->isPending())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <i class="fa-solid fa-hourglass-half text-[10px] animate-pulse"></i> Pending Approval
                            </span>
                        @elseif ($vehicle->isRejected())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Rejected
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-400 font-semibold mt-1">
                        {{ $vehicle->model }} · {{ $vehicle->type }}
                    </p>
                </div>
            </div>

            <div class="text-xs text-slate-500 sm:text-right">
                <div>Registered: {{ $vehicle->created_at->format('d M Y') }}</div>
                <div class="text-teal-400 font-mono mt-0.5">Fleet ID #{{ $vehicle->id }}</div>
            </div>
        </div>

        {{-- Specs Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs mb-6">
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Vehicle Registration</span>
                <span class="font-black text-base text-white font-mono">{{ $vehicle->registration_no }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Vehicle Type</span>
                <span class="font-extrabold text-base text-white">{{ $vehicle->type }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Model</span>
                <span class="font-extrabold text-base text-white">{{ $vehicle->model }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Total Passenger Capacity</span>
                <span class="font-extrabold text-base text-teal-400 font-mono">{{ $vehicle->total_seats }} Seats</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Approval Status</span>
                <span class="font-extrabold text-base capitalize {{ $vehicle->isApproved() ? 'text-emerald-400' : ($vehicle->isRejected() ? 'text-rose-400' : 'text-amber-400') }}">
                    {{ $vehicle->status }}
                </span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Associated Trips</span>
                <span class="font-extrabold text-base text-white font-mono">{{ $vehicle->trips->count() }} Trips</span>
            </div>
        </div>

        {{-- Status Notification Banners --}}
        @if ($vehicle->isPending())
            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300 flex items-start gap-3">
                <i class="fa-solid fa-hourglass-half text-amber-400 text-lg mt-0.5 shrink-0 animate-pulse"></i>
                <div class="space-y-1">
                    <strong class="text-white block font-bold">Vehicle Under Review</strong>
                    <p class="text-amber-200/80 leading-relaxed">
                        This vehicle has been queued for administrator verification. You will be able to schedule commercial trips with this vehicle immediately once approved.
                    </p>
                </div>
            </div>
        @elseif ($vehicle->isRejected())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 space-y-2">
                <div class="flex items-center gap-2 font-bold text-rose-200">
                    <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                    <span>Vehicle Registration Rejected</span>
                </div>
                @if ($vehicle->rejection_reason)
                    <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-800/80 text-rose-200">
                        <strong>Reason:</strong> {{ $vehicle->rejection_reason }}
                    </div>
                @endif
                <p class="text-rose-300/80 text-[11px]">
                    If you believe this was an error, please reach out to platform support with updated vehicle documentation.
                </p>
            </div>
        @elseif ($vehicle->isApproved())
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300 flex items-start gap-3">
                <i class="fa-solid fa-circle-check text-emerald-400 text-lg mt-0.5 shrink-0"></i>
                <div class="space-y-1">
                    <strong class="text-white block font-bold">Verified & Active</strong>
                    <p class="text-emerald-200/80 leading-relaxed">
                        This vehicle is verified by RouteConnect administrators and eligible for scheduling public passenger trips.
                    </p>
                </div>
            </div>
        @endif
    </div>

</div>

@endsection
