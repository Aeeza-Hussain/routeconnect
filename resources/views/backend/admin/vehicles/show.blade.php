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
                        @if ($vehicle->isActive())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-extrabold border border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 text-xs font-extrabold border border-rose-500/30">
                                <span class="w-2 h-2 rounded-full bg-rose-400"></span> Inactive
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
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Fleet Status</div>
                    <div class="text-sm font-extrabold {{ $vehicle->isActive() ? 'text-emerald-600' : 'text-rose-600' }}">
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
        </div>
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
                        <a href="{{ route('admin.users.show', $vehicle->driver->id) }}"
                           class="font-extrabold text-slate-900 text-sm hover:text-emerald-600 transition-colors">
                            {{ $vehicle->driver->name }}
                        </a>
                        <p class="text-xs text-slate-500">{{ $vehicle->driver->email }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Phone: {{ $vehicle->driver->phone ?? 'Not provided' }} · License: {{ $vehicle->driver->license_no ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.show', $vehicle->driver->id) }}"
                       class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-[10px]"></i> View Driver Profile
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
