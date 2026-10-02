@extends('backend.admin.layouts.app')

@section('title', 'Stop Details — ' . $stop->name . ' — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-map-pin"></i> Transit Stop Details
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            {{ $stop->name }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            {{ $stop->location ?? 'No location description specified' }}
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.stops.edit', $stop->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-sm transition-colors">
            <i class="fa-solid fa-pen"></i> Edit Stop
        </a>
        <a href="{{ route('admin.stops.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Stops
        </a>
    </div>
</div>

{{-- Success / Error Alerts --}}
@if (session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

{{-- ─── Details Grid ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Stop Info (2 Cols) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-extrabold text-slate-900 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-teal-600"></i> Stop Information
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Stop Name</div>
                <div class="text-lg font-extrabold text-slate-900">{{ $stop->name }}</div>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Location</div>
                <div class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-teal-500"></i>
                    {{ $stop->location ?? 'Not specified' }}
                </div>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status</div>
                @if ($stop->status === 'Active')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactive
                    </span>
                @endif
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Created At</div>
                <div class="text-sm font-semibold text-slate-700">
                    {{ $stop->created_at ? $stop->created_at->format('M d, Y h:i A') : '—' }}
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-900">Delete Stop</div>
                <p class="text-xs text-slate-500">Remove this stop from the transit network and routes.</p>
            </div>
            <form action="{{ route('admin.stops.destroy', $stop->id) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete stop &quot;{{ $stop->name }}&quot;?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold text-xs transition-colors inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can"></i> Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Quick Summary Card (1 Col) --}}
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-xl mb-4">
                <i class="fa-solid fa-route"></i>
            </div>
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Routes Connected</div>
            <div class="text-3xl font-black mt-1 mb-2">{{ $stop->routeStops->count() }}</div>
            <p class="text-xs text-slate-400 leading-relaxed">
                Number of active or inactive bus and van routes that pass through this stop.
            </p>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-700">
            <a href="{{ route('admin.routes.index') }}"
               class="inline-flex items-center gap-2 text-xs font-bold text-teal-400 hover:text-teal-300 transition-colors">
                <i class="fa-solid fa-arrow-right"></i> Browse all routes
            </a>
        </div>
    </div>

</div>

{{-- ─── Connected Routes Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-route text-teal-600"></i> Routes Passing Through This Stop
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">All routes configured with this stop in their sequence.</p>
        </div>
        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-teal-100 text-teal-800">
            {{ $stop->routeStops->count() }} {{ Str::plural('Route', $stop->routeStops->count()) }}
        </span>
    </div>

    @if ($stop->routeStops->isEmpty())
        <div class="p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-route"></i>
            </div>
            <div class="font-bold text-slate-800 text-sm">Not assigned to any routes yet</div>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                You can add this stop to any route by going to the route's detail page and adding it to the stops sequence.
            </p>
            <a href="{{ route('admin.routes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 text-white text-xs font-bold hover:bg-teal-700">
                <i class="fa-solid fa-route"></i> View Routes
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold bg-slate-50/50">
                        <th class="py-3 px-5">Route Name</th>
                        <th class="py-3 px-5">Start Location</th>
                        <th class="py-3 px-5">End Location</th>
                        <th class="py-3 px-5 text-center">Stop Sequence</th>
                        <th class="py-3 px-5 text-center">Route Status</th>
                        <th class="py-3 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($stop->routeStops as $rStop)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-5">
                                @if ($rStop->route)
                                    <a href="{{ route('admin.routes.show', $rStop->route->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                        {{ $rStop->route->name }}
                                    </a>
                                @else
                                    <span class="text-rose-500 font-bold">Deleted Route #{{ $rStop->route_id }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-slate-700 font-medium">
                                {{ $rStop->route->start_location ?? '—' }}
                            </td>
                            <td class="py-4 px-5 text-slate-700 font-medium">
                                {{ $rStop->route->end_location ?? '—' }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="w-7 h-7 rounded-full bg-teal-100 text-teal-800 text-xs font-bold inline-flex items-center justify-center">
                                    {{ $rStop->stop_order }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if ($rStop->route && $rStop->route->status === 'Active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $rStop->route->status ?? 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if ($rStop->route)
                                    <a href="{{ route('admin.routes.show', $rStop->route->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                                        <i class="fa-solid fa-eye"></i> View Route
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
