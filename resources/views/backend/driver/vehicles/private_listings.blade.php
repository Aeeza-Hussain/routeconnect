@extends('backend.driver.layouts.app')

@section('title', 'Private Vehicle Listings — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-car-side"></i> Vehicle Availability Management
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Private Vehicle Listings</h2>
        <p class="text-sm text-slate-400 mt-1">
            Manage your fleet's private rental availability, daily & hourly pricing, service areas, and blocked dates.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('driver.vehicles.offer_private') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-teal-500/20">
            <i class="fa-solid fa-plus"></i> Offer a Private Vehicle
        </a>
        <a href="{{ route('driver.trips.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700">
            <i class="fa-solid fa-users"></i> Offer a Shared Ride
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800 text-emerald-300 text-xs sm:text-sm font-semibold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-rose-950/40 border border-rose-800 text-rose-300 text-xs sm:text-sm font-semibold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-400 text-base shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

@if ($vehicles->isEmpty())
    <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-12 text-center space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-slate-900 text-slate-500 flex items-center justify-center text-3xl mx-auto border border-slate-800">
            <i class="fa-solid fa-car-side"></i>
        </div>
        <h3 class="text-lg font-extrabold text-white">No Vehicles Registered</h3>
        <p class="text-xs text-slate-400 max-w-md mx-auto">
            You have not registered any vehicles yet. Register a commercial vehicle to request administrator approval and publish private rental listings.
        </p>
        <div class="pt-2">
            <a href="{{ route('driver.vehicles.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-md">
                <i class="fa-solid fa-plus"></i> Register Fleet Vehicle
            </a>
        </div>
    </div>
@else
    {{-- KPI Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Vehicles</span>
                <span class="text-2xl font-black text-white mt-1 block">{{ $vehicles->count() }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-slate-400 flex items-center justify-center text-lg border border-slate-800">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Active Private Listings</span>
                <span class="text-2xl font-black text-emerald-400 mt-1 block">
                    {{ $vehicles->where('is_available_for_rental', true)->filter->isApproved()->count() }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/20">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Active Blocked Periods</span>
                <span class="text-2xl font-black text-amber-400 mt-1 block">
                    {{ $vehicles->sum(fn($v) => $v->unavailabilities->count()) }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg border border-amber-500/20">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
        </div>
    </div>

    {{-- Vehicles Listing Grid --}}
    <div class="space-y-4">
        @foreach ($vehicles as $vehicle)
            <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 shadow-xl hover:border-slate-700 transition-all flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                {{-- Left: Vehicle Info --}}
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-2xl border border-teal-500/20 shrink-0">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-lg font-black text-white font-mono">{{ $vehicle->registration_no }}</h3>
                            <span class="text-xs text-slate-300 font-bold">({{ $vehicle->model }})</span>

                            {{-- Status Pill --}}
                            @if (!$vehicle->isApproved())
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20 capitalize">
                                    {{ $vehicle->status }} (Pending Admin Approval)
                                </span>
                            @elseif ($vehicle->is_available_for_rental)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Published & Active
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-800 text-slate-400 border border-slate-700">
                                    Deactivated
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-400 flex items-center gap-4 flex-wrap pt-1">
                            <span><strong class="text-slate-300">Type:</strong> {{ $vehicle->type }}</span>
                            <span><strong class="text-slate-300">Capacity:</strong> {{ $vehicle->total_seats }} Seats</span>
                            <span><strong class="text-slate-300">Daily:</strong> <span class="text-teal-400 font-bold font-mono">PKR {{ number_format($vehicle->rental_rate_per_day ?: 4000, 2) }}</span></span>
                            @if ($vehicle->rental_rate_per_hour)
                                <span><strong class="text-slate-300">Hourly:</strong> <span class="text-teal-400 font-mono">PKR {{ number_format($vehicle->rental_rate_per_hour, 2) }}</span></span>
                            @endif
                        </div>

                        @if ($vehicle->pickup_location || $vehicle->service_area)
                            <div class="text-[11px] text-slate-400 flex items-center gap-3 pt-1">
                                @if ($vehicle->pickup_location)
                                    <span><i class="fa-solid fa-location-dot text-teal-400 mr-1"></i> Pickup: <strong class="text-slate-300">{{ $vehicle->pickup_location }}</strong></span>
                                @endif
                                @if ($vehicle->service_area)
                                    <span><i class="fa-solid fa-map text-sky-400 mr-1"></i> Coverage: <strong class="text-slate-300">{{ $vehicle->service_area }}</strong></span>
                                @endif
                            </div>
                        @endif

                        @if ($vehicle->available_from || $vehicle->available_to)
                            <div class="text-[11px] text-slate-500 pt-0.5 font-mono">
                                Availability Window: {{ $vehicle->available_from ? $vehicle->available_from->format('d M Y') : 'Start' }} to {{ $vehicle->available_to ? $vehicle->available_to->format('d M Y') : 'Open' }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Actions & Availability Controls --}}
                <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap shrink-0">
                    @if ($vehicle->isApproved())
                        {{-- Quick Active/Deactive Toggle --}}
                        <form method="POST" action="{{ route('driver.vehicles.toggle_rental', $vehicle->id) }}">
                            @csrf
                            <button type="submit"
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all border flex items-center gap-2
                                    {{ $vehicle->is_available_for_rental ? 'bg-slate-900 text-rose-300 border-rose-500/30 hover:bg-rose-950/40' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/20' }}">
                                <i class="fa-solid {{ $vehicle->is_available_for_rental ? 'fa-pause' : 'fa-play' }} text-[10px]"></i>
                                <span>{{ $vehicle->is_available_for_rental ? 'Deactivate' : 'Activate' }}</span>
                            </button>
                        </form>

                        {{-- Edit Private Listing --}}
                        <a href="{{ route('driver.vehicles.offer_private', ['vehicle_id' => $vehicle->id]) }}"
                           class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition-colors flex items-center gap-1.5"
                           title="Edit Pricing & Coverage">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                            <span>Edit Listing</span>
                        </a>

                        {{-- Block Dates & Details --}}
                        <a href="{{ route('driver.vehicles.show', $vehicle->id) }}"
                           class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 text-xs font-bold border border-slate-800 transition-colors flex items-center gap-1.5"
                           title="Manage Schedule & Blocked Dates">
                            <i class="fa-solid fa-calendar-xmark text-amber-400 text-[11px]"></i>
                            <span>Blocked Dates ({{ $vehicle->unavailabilities->count() }})</span>
                        </a>
                    @else
                        <span class="text-xs text-amber-400/90 italic">
                            Awaiting admin verification before listing can be activated.
                        </span>
                    @endif
                </div>

            </div>
        @endforeach
    </div>
@endif

@endsection

