@extends('backend.driver.layouts.app')

@section('title', 'My Vehicles — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-van-shuttle"></i> Fleet Management
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">My Vehicles</h2>
        <p class="text-sm text-slate-400 mt-1">
            Register and manage your commercial fleet vehicles. Every vehicle is individually verified by administrators.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('driver.vehicles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-teal-500/20">
            <i class="fa-solid fa-square-plus"></i> Register New Vehicle
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs sm:text-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs sm:text-sm flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- ─── Vehicles Listing ─── --}}
@if ($vehicles->isEmpty())
    <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-12 text-center max-w-xl mx-auto space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-3xl mx-auto border border-teal-500/20">
            <i class="fa-solid fa-car-side"></i>
        </div>
        <h3 class="text-lg font-extrabold text-white">No Vehicles Registered Yet</h3>
        <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
            You have not registered any vehicles yet. Submit your commercial vehicle details to request administrator approval and begin scheduling trips.
        </p>
        <div class="pt-2">
            <a href="{{ route('driver.vehicles.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all">
                <i class="fa-solid fa-square-plus"></i> Register Your First Vehicle
            </a>
        </div>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($vehicles as $veh)
            @php
                $st = strtolower($veh->status);
                $isApproved = in_array($st, ['approved', 'active']);
                $isPending  = $st === 'pending';
                $isRejected = $st === 'rejected';
            @endphp
            <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 flex flex-col justify-between hover:border-slate-700 transition-all shadow-lg">
                <div>
                    {{-- Card Header --}}
                    <div class="flex items-start justify-between gap-3 border-b border-slate-800/80 pb-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl border border-teal-500/20 shrink-0">
                                <i class="fa-solid fa-van-shuttle"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white font-mono tracking-wide">{{ $veh->registration_no }}</h3>
                                <p class="text-xs text-slate-400 font-semibold">{{ $veh->model }}</p>
                            </div>
                        </div>

                        {{-- Approval Badge --}}
                        @if ($isApproved)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Approved
                            </span>
                        @elseif ($isPending)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <i class="fa-solid fa-hourglass-half text-[10px] animate-pulse"></i> Pending Approval
                            </span>
                        @elseif ($isRejected)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Rejected
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                {{ ucfirst($veh->status) }}
                            </span>
                        @endif
                    </div>

                    {{-- Spec Details --}}
                    <div class="grid grid-cols-2 gap-3 text-xs mb-4">
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800/80">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Type</span>
                            <span class="text-white font-bold mt-0.5 block">{{ $veh->type }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800/80">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Total Passenger Seats</span>
                            <span class="text-teal-400 font-mono font-black mt-0.5 block">{{ $veh->total_seats }} Seats</span>
                        </div>
                    </div>

                    {{-- Rejection Note if Rejected --}}
                    @if ($isRejected && $veh->rejection_reason)
                        <div class="p-3 rounded-xl bg-rose-950/40 border border-rose-800/50 text-[11px] text-rose-300 mb-4">
                            <strong class="text-rose-200 block mb-0.5"><i class="fa-solid fa-circle-exclamation mr-1"></i> Rejection Reason:</strong>
                            <span>{{ $veh->rejection_reason }}</span>
                        </div>
                    @endif

                    @if ($isPending)
                        <div class="p-3 rounded-xl bg-amber-950/30 border border-amber-800/40 text-[11px] text-amber-300 mb-4">
                            <i class="fa-solid fa-clock-rotate-left mr-1"></i> Awaiting administrator review. You will be able to schedule trips with this vehicle once approved.
                        </div>
                    @endif
                </div>

                {{-- Action Footer --}}
                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                    <span class="text-slate-500 text-[11px]">Registered: {{ $veh->created_at->format('M d, Y') }}</span>
                    @if ($isApproved)
                        <a href="{{ route('driver.trips.create') }}?vehicle_id={{ $veh->id }}"
                           class="text-teal-400 hover:text-teal-300 font-bold inline-flex items-center gap-1.5 transition-colors">
                            <span>Schedule Trip</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @else
                        <span class="text-slate-500 text-[11px] italic">Not Eligible for Trips</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
