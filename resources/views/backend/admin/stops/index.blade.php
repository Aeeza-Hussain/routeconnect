@extends('backend.admin.layouts.app')

@section('title', 'Stops Management — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-map-pin"></i> Transit Network
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Stops Management
        </h2>
        <p class="text-sm text-slate-500 mt-1">Manage stations, roadside stops, and pickup points across all routes.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.routes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-route"></i> Routes
        </a>
        <a href="{{ route('admin.stops.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-sm transition-all">
            <i class="fa-solid fa-plus"></i> Add Stop
        </a>
    </div>
</div>

{{-- ─── Stat Counters ─── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-location-dot"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Stops</div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalStops) }}</div>
        </div>
    </div>
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Stops</div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($activeStops) }}</div>
        </div>
    </div>
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-circle-pause"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inactive Stops</div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($inactiveStops) }}</div>
        </div>
    </div>
</div>

{{-- ─── Filters & Search ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.stops.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
        {{-- Search Input --}}
        <div class="flex-1 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by stop name or location..."
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        {{-- Status Filter --}}
        <div class="w-full md:w-44">
            <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.stops.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ─── Stops Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold bg-slate-50/50">
                    <th class="py-3.5 px-5">#</th>
                    <th class="py-3.5 px-5">Stop Name</th>
                    <th class="py-3.5 px-5">Location</th>
                    <th class="py-3.5 px-5 text-center">Assigned Routes</th>
                    <th class="py-3.5 px-5 text-center">Status</th>
                    <th class="py-3.5 px-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($stops as $stop)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-4 px-5 text-xs text-slate-400 font-bold">
                            {{ $stop->id }}
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="fa-solid fa-map-pin"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.stops.show', $stop->id) }}" class="font-extrabold text-slate-900 hover:text-teal-600 transition-colors">
                                        {{ $stop->name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400">Added {{ $stop->created_at ? $stop->created_at->format('M d, Y') : '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-slate-700 font-medium">
                            {{ $stop->location ?? '—' }}
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-route text-[10px] text-slate-400"></i>
                                {{ $stop->route_stops_count }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if ($stop->status === 'Active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.stops.show', $stop->id) }}"
                                   title="View Stop"
                                   class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.stops.edit', $stop->id) }}"
                                   title="Edit Stop"
                                   class="w-8 h-8 rounded-lg bg-teal-50 hover:bg-teal-600 text-teal-600 hover:text-white flex items-center justify-center text-xs transition-colors">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.stops.destroy', $stop->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete stop &quot;{{ $stop->name }}&quot;?');"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete Stop"
                                            class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center text-xs transition-colors">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl mx-auto mb-3">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="font-extrabold text-slate-800 text-base">No stops found</div>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                                @if(request()->anyFilled(['search', 'status']))
                                    No stops match your search criteria. Try clearing the filter.
                                @else
                                    No transit stops or pickup points created yet.
                                @endif
                            </p>
                            @if(request()->anyFilled(['search', 'status']))
                                <a href="{{ route('admin.stops.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200">
                                    Clear Search
                                </a>
                            @else
                                <a href="{{ route('admin.stops.create') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-xs font-bold hover:bg-teal-700">
                                    <i class="fa-solid fa-plus mr-1"></i> Add First Stop
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($stops->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $stops->links() }}
        </div>
    @endif
</div>

@endsection
