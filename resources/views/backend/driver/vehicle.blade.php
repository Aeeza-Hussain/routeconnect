@extends('backend.driver.layouts.app')

@section('title', 'My Vehicle — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-van-shuttle"></i> Fleet Management
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Assigned Vehicle</h2>
        <p class="text-sm text-slate-400 mt-1">
            Review allocated commercial transport vehicle details, passenger capacity, and status.
        </p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

<div class="max-w-4xl space-y-6">

    @if ($vehicle)
        {{-- Vehicle Showcase Card --}}
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-800/80 pb-6 mb-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-3xl border border-teal-500/20 shrink-0 shadow-lg shadow-teal-500/5">
                        <i class="fa-solid fa-van-shuttle"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-2xl sm:text-3xl font-black text-white font-mono tracking-wide">{{ $vehicle->registration_no }}</h3>
                            @php $isActive = strtolower($vehicle->status) === 'active'; @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold border
                                {{ $isActive ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400' }}"></span>
                                {{ $vehicle->status }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-400 font-semibold mt-1">
                            {{ $vehicle->model }} · {{ $vehicle->type }}
                        </p>
                    </div>
                </div>

                <div class="flex sm:flex-col sm:items-end gap-2 text-xs">
                    <span class="text-slate-500">Fleet ID #{{ $vehicle->id }}</span>
                    <span class="text-teal-400 font-semibold">Registered: {{ $vehicle->created_at->format('M Y') }}</span>
                </div>
            </div>

            {{-- 5 Required Specifications Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs mb-6">
                {{-- 1. Vehicle Number --}}
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">1. Vehicle Number</span>
                    <span class="font-black text-base text-white font-mono">{{ $vehicle->registration_no }}</span>
                </div>

                {{-- 2. Vehicle Type --}}
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">2. Vehicle Type</span>
                    <span class="font-extrabold text-base text-white">{{ $vehicle->type }}</span>
                </div>

                {{-- 3. Model --}}
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">3. Model</span>
                    <span class="font-extrabold text-base text-white">{{ $vehicle->model }}</span>
                </div>

                {{-- 4. Total Seats --}}
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">4. Total Seats</span>
                    <span class="font-extrabold text-base text-teal-400 font-mono">{{ $vehicle->total_seats }} Seats</span>
                </div>

                {{-- 5. Status --}}
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">5. Status</span>
                    <span class="font-extrabold text-base {{ $isActive ? 'text-emerald-400' : 'text-slate-300' }}">{{ $vehicle->status }}</span>
                </div>

                {{-- Driver Assignment --}}
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Assigned Driver</span>
                    <span class="font-extrabold text-base text-white truncate block">{{ $driver->name }}</span>
                </div>
            </div>

            {{-- Security Policy Notice --}}
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 text-xs text-slate-400 flex items-start gap-3">
                <i class="fa-solid fa-shield-halved text-teal-400 text-sm mt-0.5 shrink-0"></i>
                <div class="leading-relaxed">
                    <strong class="text-slate-300 font-bold">Fleet Management Policy:</strong>
                    Vehicle allocations and technical records are centrally managed by RouteConnect Operations. You may operate and schedule trips using this vehicle, but driver reassignments are restricted to authorized fleet managers.
                </div>
            </div>

        </div>

    @else
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-3xl mx-auto mb-4 border border-amber-500/20">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
            <h3 class="text-lg font-extrabold text-white mb-2">No Vehicle Assigned Yet</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto mb-6 leading-relaxed">
                The RouteConnect operations team has not yet linked a commercial vehicle to your driver profile. Once assigned, your vehicle registration, seating capacity, and status will be visible here.
            </p>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-400 font-semibold">
                <i class="fa-solid fa-circle-info text-teal-400"></i> Contact admin at support@routeconnect.pk
            </div>
        </div>
    @endif

</div>

@endsection
