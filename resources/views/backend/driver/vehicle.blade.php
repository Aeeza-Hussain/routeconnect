@extends('backend.driver.layouts.app')

@section('title', 'My Vehicle — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-van-shuttle"></i> Assigned Fleet
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Assigned Vehicle</h2>
        <p class="text-sm text-slate-400 mt-1">Vehicle allocated to you for scheduling and conducting passenger trips.</p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

<div class="max-w-3xl space-y-6">

    @if ($vehicle)
        {{-- Vehicle Card --}}
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5 mb-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-2xl border border-teal-500/20 shrink-0">
                        <i class="fa-solid fa-van-shuttle"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-white font-mono">{{ $vehicle->registration_no }}</h3>
                        <p class="text-xs text-slate-400 font-semibold">{{ $vehicle->model }} · {{ $vehicle->type }}</p>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-extrabold border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> {{ $vehicle->status }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1">Vehicle Type</span>
                    <span class="font-extrabold text-white">{{ $vehicle->type }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1">Total Capacity</span>
                    <span class="font-extrabold text-teal-400">{{ $vehicle->total_seats }} Passenger Seats</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1">Registration Date</span>
                    <span class="font-extrabold text-white">{{ $vehicle->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    @else
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
            <h3 class="text-base font-extrabold text-white mb-1">No Vehicle Assigned Yet</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">
                The administrator has not yet assigned a vehicle to your account. Vehicles will appear here once allocated.
            </p>
        </div>
    @endif

</div>

@endsection
