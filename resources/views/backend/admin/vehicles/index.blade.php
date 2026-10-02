@extends('backend.admin.layouts.app')

@section('title', 'Vehicles Management — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-van-shuttle"></i> Fleet Management
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Registered Vehicles</h2>
        <p class="text-sm text-slate-500 mt-1">Manage all passenger vehicles, coasters, vans, dabas, and cars.</p>
    </div>
    <a href="{{ route('admin.vehicles.create') }}"
       class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm shadow-emerald-600/20 transition-all">
        <i class="fa-solid fa-plus"></i> Add New Vehicle
    </a>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base shrink-0"></i>
        {{ session('error') }}
    </div>
@endif

{{-- ─── Search + Filter Bar ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
    <form action="{{ route('admin.vehicles.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">

        {{-- Search input --}}
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </div>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by vehicle number (e.g. LEE-2024), model, or driver..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
        </div>

        {{-- Vehicle Type filter --}}
        <select name="type" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="all"  {{ $type === 'all'  ? 'selected' : '' }}>All Types ({{ $counts['all'] }})</option>
            <option value="Van"  {{ $type === 'Van'  ? 'selected' : '' }}>Van ({{ $counts['van'] }})</option>
            <option value="Daba" {{ $type === 'Daba' ? 'selected' : '' }}>Daba ({{ $counts['daba'] }})</option>
            <option value="Car"  {{ $type === 'Car'  ? 'selected' : '' }}>Car ({{ $counts['car'] }})</option>
            <option value="Bus"  {{ $type === 'Bus'  ? 'selected' : '' }}>Bus ({{ $counts['bus'] }})</option>
            <option value="Other"{{ $type === 'Other'? 'selected' : '' }}>Other</option>
        </select>

        {{-- Status filter --}}
        <select name="status" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="all"      {{ $status === 'all'      ? 'selected' : '' }}>All Status</option>
            <option value="active"   {{ strtolower($status) === 'active'   ? 'selected' : '' }}>Active ({{ $counts['active'] }})</option>
            <option value="inactive" {{ strtolower($status) === 'inactive' ? 'selected' : '' }}>Inactive ({{ $counts['inactive'] }})</option>
        </select>

        <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-bold transition-colors inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if ($search || $type !== 'all' || $status !== 'all')
            <a href="{{ route('admin.vehicles.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-bold transition-colors text-center inline-flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- ─── Vehicles Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Table header count --}}
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-bold text-slate-700">
            {{ $vehicles->total() }} {{ Str::plural('vehicle', $vehicles->total()) }} found
        </div>
        <div class="text-xs text-slate-400">
            Page {{ $vehicles->currentPage() }} of {{ $vehicles->lastPage() }}
        </div>
    </div>

    @if ($vehicles->isEmpty())
        <div class="p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700 mb-1">No vehicles found</div>
            <p class="text-xs text-slate-500 mb-4">
                @if ($search || $type !== 'all' || $status !== 'all')
                    Try adjusting your search criteria or clear the filters.
                @else
                    Start by registering the first vehicle for an approved driver.
                @endif
            </p>
            @if (!$search && $type === 'all' && $status === 'all')
                <a href="{{ route('admin.vehicles.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                    <i class="fa-solid fa-plus"></i> Add Vehicle
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 w-8">#</th>
                        <th class="px-6 py-3">Vehicle Number</th>
                        <th class="px-6 py-3">Model</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Assigned Driver</th>
                        <th class="px-6 py-3">Capacity</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Registered</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($vehicles as $i => $vehicle)
                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- Row # --}}
                            <td class="px-6 py-4 text-slate-400 font-bold">
                                {{ ($vehicles->currentPage() - 1) * $vehicles->perPage() + $i + 1 }}
                            </td>

                            {{-- Vehicle Number --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm border border-indigo-100 shrink-0">
                                        @if ($vehicle->type === 'Bus')
                                            <i class="fa-solid fa-bus"></i>
                                        @elseif ($vehicle->type === 'Car')
                                            <i class="fa-solid fa-car"></i>
                                        @else
                                            <i class="fa-solid fa-van-shuttle"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-mono font-extrabold text-slate-900 text-sm block">
                                            {{ $vehicle->registration_no }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Model --}}
                            <td class="px-6 py-4 text-slate-700 font-bold">
                                {{ $vehicle->model }}
                            </td>

                            {{-- Vehicle Type --}}
                            <td class="px-6 py-4">
                                @php
                                    $typeStyles = [
                                        'Van'   => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'Daba'  => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'Car'   => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'Bus'   => 'bg-violet-100 text-violet-800 border-violet-200',
                                        'Other' => 'bg-slate-100 text-slate-800 border-slate-200',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $typeStyles[$vehicle->type] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $vehicle->type }}
                                </span>
                            </td>

                            {{-- Driver --}}
                            <td class="px-6 py-4">
                                @if ($vehicle->driver)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 font-extrabold flex items-center justify-center text-[10px] border border-teal-200">
                                            {{ strtoupper(substr($vehicle->driver->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $vehicle->driver->id) }}" class="font-extrabold text-slate-900 hover:text-emerald-600 transition-colors block">
                                                {{ $vehicle->driver->name }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 block">{{ $vehicle->driver->phone ?? $vehicle->driver->email }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">No Driver Assigned</span>
                                @endif
                            </td>

                            {{-- Seats --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-slate-700 font-extrabold">
                                    <i class="fa-solid fa-chair text-slate-400 text-[10px]"></i>
                                    {{ $vehicle->total_seats }} seats
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @if ($vehicle->isActive())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-extrabold border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $vehicle->created_at->format('d M Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View --}}
                                    <a href="{{ route('admin.vehicles.show', $vehicle->id) }}"
                                       title="View details"
                                       class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold transition-colors">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}"
                                       title="Edit vehicle"
                                       class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold transition-colors border border-indigo-200">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST"
                                          onsubmit="return confirm('Delete vehicle \'{{ addslashes($vehicle->registration_no) }}\'? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Delete vehicle"
                                                class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold transition-colors border border-rose-200">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($vehicles->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $vehicles->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
