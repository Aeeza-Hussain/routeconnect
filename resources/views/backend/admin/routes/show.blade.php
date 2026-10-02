@extends('backend.admin.layouts.app')

@section('title', 'Route Details — ' . $route->name . ' — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-route"></i> Route Details
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            {{ $route->name }}
        </h2>
        <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
            <span class="font-semibold text-slate-700">{{ $route->start_location }}</span>
            <i class="fa-solid fa-arrow-right text-xs text-indigo-500"></i>
            <span class="font-semibold text-slate-700">{{ $route->end_location }}</span>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.routes.edit', $route->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-colors">
            <i class="fa-solid fa-pen"></i> Edit Route
        </a>
        <a href="{{ route('admin.routes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Routes
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

@if (session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

{{-- ─── Route Overview Card ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Route Name</div>
            <div class="text-lg font-extrabold text-slate-900">{{ $route->name }}</div>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Origin (Start)</div>
            <div class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-dot text-emerald-500 text-sm"></i>
                {{ $route->start_location }}
            </div>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Destination (End)</div>
            <div class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-rose-500 text-sm"></i>
                {{ $route->end_location }}
            </div>
        </div>
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status</div>
            @if ($route->status === 'Active')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactive
                </span>
            @endif
        </div>
    </div>

    {{-- Route Journey Sequence Visual --}}
    <div class="mt-6 pt-6 border-t border-slate-100">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Full Journey Flow</div>
        <div class="flex flex-wrap items-center gap-2 text-sm font-semibold">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl">
                <i class="fa-solid fa-play text-xs text-emerald-600"></i> {{ $route->start_location }}
            </span>

            @forelse ($route->routeStops as $rStop)
                <i class="fa-solid fa-arrow-right text-xs text-slate-300"></i>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded-xl">
                    <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] flex items-center justify-center font-bold">
                        {{ $rStop->stop_order }}
                    </span>
                    {{ $rStop->stop->name ?? 'Stop #' . $rStop->stop_id }}
                </span>
            @empty
                <i class="fa-solid fa-arrow-right text-xs text-slate-300"></i>
                <span class="text-xs italic text-slate-400">No intermediate stops assigned yet</span>
            @endforelse

            <i class="fa-solid fa-arrow-right text-xs text-slate-300"></i>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl">
                <i class="fa-solid fa-flag-checkered text-xs text-rose-600"></i> {{ $route->end_location }}
            </span>
        </div>
    </div>
</div>

{{-- ─── Route Stops Management ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Stops List (2 Cols) --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-map-pin text-indigo-600"></i> Assigned Stops
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ordered sequence of stops along this route.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-800">
                    {{ $route->routeStops->count() }} {{ Str::plural('Stop', $route->routeStops->count()) }}
                </span>
            </div>

            @if ($route->routeStops->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mx-auto mb-3">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </div>
                    <div class="font-bold text-slate-800 text-sm">No intermediate stops assigned</div>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                        This route currently only has start and end locations. Use the form on the right to assign stops like pickup points or towns.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold bg-slate-50/50">
                                <th class="py-3 px-4">Order</th>
                                <th class="py-3 px-4">Stop Name</th>
                                <th class="py-3 px-4">Location</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($route->routeStops as $rStop)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    {{-- Order Update Form --}}
                                    <td class="py-3 px-4">
                                        <form action="{{ route('admin.routes.stops.update-order', [$route->id, $rStop->id]) }}" method="POST" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="stop_order" value="{{ $rStop->stop_order }}" min="1" max="999"
                                                   class="w-16 px-2.5 py-1 text-center bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <button type="submit" title="Save Order"
                                                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-600 flex items-center justify-center text-xs transition-colors">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    </td>

                                    {{-- Stop Name --}}
                                    <td class="py-3 px-4">
                                        @if ($rStop->stop)
                                            <a href="{{ route('admin.stops.show', $rStop->stop->id) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                                                {{ $rStop->stop->name }}
                                            </a>
                                        @else
                                            <span class="text-rose-500 font-bold">Deleted Stop #{{ $rStop->stop_id }}</span>
                                        @endif
                                    </td>

                                    {{-- Stop Location --}}
                                    <td class="py-3 px-4 text-xs text-slate-600 font-medium">
                                        {{ $rStop->stop->location ?? '—' }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3 px-4 text-center">
                                        @if ($rStop->stop && $rStop->stop->status === 'Active')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                                {{ $rStop->stop->status ?? 'Inactive' }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Remove Stop Form --}}
                                    <td class="py-3 px-4 text-right">
                                        <form action="{{ route('admin.routes.stops.remove', [$route->id, $rStop->id]) }}" method="POST"
                                              onsubmit="return confirm('Remove stop &quot;{{ $rStop->stop->name ?? 'this stop' }}&quot; from this route?');"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Remove from Route"
                                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center text-xs transition-colors">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Add Stop Form (1 Col) --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 mb-1">
                <i class="fa-solid fa-circle-plus text-indigo-600"></i> Add Stop to Route
            </h3>
            <p class="text-xs text-slate-500 mb-5">Assign an existing stop and position it in the journey sequence.</p>

            @if ($availableStops->isEmpty())
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 text-center">
                    <i class="fa-solid fa-info-circle text-indigo-500 text-base mb-1 block"></i>
                    All active stops are already assigned, or no active stops exist.
                    <div class="mt-3">
                        <a href="{{ route('admin.stops.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:underline">
                            <i class="fa-solid fa-plus"></i> Create New Stop
                        </a>
                    </div>
                </div>
            @else
                <form action="{{ route('admin.routes.stops.add', $route->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="stop_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Select Stop <span class="text-rose-500">*</span>
                        </label>
                        <select name="stop_id" id="stop_id" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Stop --</option>
                            @foreach ($availableStops as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->name }} ({{ $s->location }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stop_order" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Stop Order / Sequence <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="stop_order" id="stop_order" min="1" max="999"
                               value="{{ old('stop_order', $nextOrder) }}" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="mt-1 text-[11px] text-slate-400">Order number (1 = 1st intermediate stop, etc.)</p>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i> Assign Stop
                    </button>
                </form>
            @endif

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Need a stop not in the list?</span>
                <a href="{{ route('admin.stops.create') }}" class="font-bold text-indigo-600 hover:underline">
                    + Add New Stop
                </a>
            </div>
        </div>

        {{-- Route Actions / Delete --}}
        <div class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Danger Zone</h4>
            <p class="text-xs text-slate-500 mb-4">Deleting this route will also unlink any intermediate stops assigned to it.</p>
            <form action="{{ route('admin.routes.destroy', $route->id) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete route &quot;{{ $route->name }}&quot;?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold text-sm transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> Delete Route
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
