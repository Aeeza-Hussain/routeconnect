@extends('backend.admin.layouts.app')

@section('title', 'Routes Management — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-route"></i> Transit Paths
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Platform Routes</h2>
        <p class="text-sm text-slate-500 mt-1">Manage transport routes, origins, destinations, and assigned waypoints.</p>
    </div>
    <a href="{{ route('admin.routes.create') }}"
       class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm shadow-emerald-600/20 transition-all">
        <i class="fa-solid fa-plus"></i> Add New Route
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
    <form action="{{ route('admin.routes.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">

        {{-- Search input --}}
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </div>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by route name, start location, or end location..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
        </div>

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

        @if ($search || $status !== 'all')
            <a href="{{ route('admin.routes.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-bold transition-colors text-center inline-flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- ─── Routes Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Table header count --}}
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-bold text-slate-700">
            {{ $routes->total() }} {{ Str::plural('route', $routes->total()) }} found
        </div>
        <div class="text-xs text-slate-400">
            Page {{ $routes->currentPage() }} of {{ $routes->lastPage() }}
        </div>
    </div>

    @if ($routes->isEmpty())
        <div class="p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-route"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700 mb-1">No routes found</div>
            <p class="text-xs text-slate-500 mb-4">
                @if ($search || $status !== 'all')
                    Try adjusting your search criteria or clear the filters.
                @else
                    Start by defining the first transport route for RouteConnect.
                @endif
            </p>
            @if (!$search && $status === 'all')
                <a href="{{ route('admin.routes.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                    <i class="fa-solid fa-plus"></i> Add Route
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 w-8">#</th>
                        <th class="px-6 py-3">Route Name</th>
                        <th class="px-6 py-3">Journey Path</th>
                        <th class="px-6 py-3">Waypoints</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($routes as $i => $route)
                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- Row # --}}
                            <td class="px-6 py-4 text-slate-400 font-bold">
                                {{ ($routes->currentPage() - 1) * $routes->perPage() + $i + 1 }}
                            </td>

                            {{-- Name --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100 shrink-0">
                                        <i class="fa-solid fa-route"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.routes.show', $route->id) }}" class="font-extrabold text-slate-900 text-sm hover:text-emerald-600 transition-colors block">
                                            {{ $route->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400">ID: #{{ $route->id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Path (Start -> End) --}}
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-2 font-bold text-slate-800">
                                    <span class="text-slate-900">{{ $route->start_location }}</span>
                                    <i class="fa-solid fa-arrow-right text-[10px] text-emerald-500"></i>
                                    <span class="text-slate-900">{{ $route->end_location }}</span>
                                </div>
                            </td>

                            {{-- Stops Count --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.routes.show', $route->id) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition-colors">
                                    <i class="fa-solid fa-location-dot text-slate-400"></i>
                                    {{ $route->stops_count }} {{ Str::plural('stop', $route->stops_count) }}
                                </a>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @if ($route->isActive())
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
                                {{ $route->created_at->format('d M Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.routes.show', $route->id) }}"
                                       title="Manage route & stops"
                                       class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold transition-colors inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye text-[10px]"></i> View
                                    </a>
                                    <a href="{{ route('admin.routes.edit', $route->id) }}"
                                       title="Edit route"
                                       class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold transition-colors border border-indigo-200">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </a>
                                    <form action="{{ route('admin.routes.destroy', $route->id) }}" method="POST"
                                          onsubmit="return confirm('Delete route \'{{ addslashes($route->name) }}\'? This will also detach all assigned stops.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Delete route"
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
        @if ($routes->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $routes->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
