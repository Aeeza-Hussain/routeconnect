@extends('backend.admin.layouts.app')

@section('title', 'Passenger Bookings — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-200">
            <i class="fa-solid fa-ticket"></i> Passenger Manifest
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Bookings Management</h2>
        <p class="text-sm text-slate-500 mt-1">Review passenger ticket reservations, seat allocations, and travel statuses.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

{{-- ─── Flash Messages ─── --}}
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
            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

{{-- ─── KPI Stats Row ─── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Bookings</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalBookings }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">All registered passes</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-ticket"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">Confirmed</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $confirmedBookings }}</p>
            <p class="text-[11px] text-emerald-500 mt-0.5">Active reservations</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-100">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-rose-600">Cancelled</p>
            <p class="text-2xl font-black text-rose-600 mt-1">{{ $cancelledBookings }}</p>
            <p class="text-[11px] text-rose-500 mt-0.5">Seats returned</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg border border-rose-100">
            <i class="fa-solid fa-ban"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-teal-600">Seats Reserved</p>
            <p class="text-2xl font-black text-teal-700 mt-1">{{ $totalSeatsBooked }}</p>
            <p class="text-[11px] text-teal-600 mt-0.5">Across confirmed trips</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg border border-teal-100">
            <i class="fa-solid fa-chair"></i>
        </div>
    </div>
</div>

{{-- ─── Search & Filter Bar ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 mb-6">
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
        
        {{-- Search Input --}}
        <div class="lg:col-span-5">
            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                Search Reference or Passenger
            </label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="e.g. RC- or Passenger Name, Email..."
                       class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
        </div>

        {{-- Status Filter --}}
        <div class="lg:col-span-3">
            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                Booking Status
            </label>
            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="all">All Statuses</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        {{-- Date Filter --}}
        <div class="lg:col-span-2">
            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                Date Booked
            </label>
            <input type="date"
                   name="date"
                   value="{{ request('date') }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        {{-- Submit & Reset Buttons --}}
        <div class="lg:col-span-2 flex items-center gap-2">
            <button type="submit"
                    class="flex-1 py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-filter"></i>
                <span>Filter</span>
            </button>
            @if (request()->hasAny(['search', 'status', 'date', 'trip_id']))
                <a href="{{ route('admin.bookings.index') }}"
                   class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors"
                   title="Reset filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>

    </form>
</div>

{{-- ─── Bookings Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
            <i class="fa-solid fa-list-check text-teal-600"></i>
            <span>All Bookings</span>
        </h3>
        <span class="text-xs font-bold text-slate-500">
            Showing {{ $bookings->firstItem() ?? 0 }}–{{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} records
        </span>
    </div>

    @if ($bookings->isEmpty())
        <div class="p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h4 class="text-base font-extrabold text-slate-800">No Bookings Found</h4>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                @if (request()->hasAny(['search', 'status', 'date', 'trip_id']))
                    No booking records match your selected filters. Try broadening your search.
                @else
                    Passenger bookings will appear here once trips are reserved.
                @endif
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Reference</th>
                        <th class="px-5 py-3.5">Passenger</th>
                        <th class="px-5 py-3.5">Trip & Route</th>
                        <th class="px-5 py-3.5">Stops</th>
                        <th class="px-5 py-3.5 text-center">Seats</th>
                        <th class="px-5 py-3.5">Total Fare</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Booked At</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach ($bookings as $b)
                        @php
                            $st = strtolower($b->booking_status ?? $b->status);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Reference --}}
                            <td class="px-5 py-4 font-mono font-extrabold text-teal-700">
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="hover:underline">
                                    {{ $b->booking_reference }}
                                </a>
                            </td>

                            {{-- Passenger --}}
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">{{ $b->user->name ?? 'User #' . $b->user_id }}</div>
                                <div class="text-[11px] text-slate-500">{{ $b->user->email ?? '—' }}</div>
                                @if($b->user?->phone)
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $b->user->phone }}</div>
                                @endif
                            </td>

                            {{-- Trip & Route --}}
                            <td class="px-5 py-4">
                                @if ($b->trip)
                                    <a href="{{ route('admin.trips.show', $b->trip->id) }}" class="font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                        {{ $b->trip->route->name ?? 'Trip #' . $b->trip->id }}
                                    </a>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $b->trip->trip_date ? \Carbon\Carbon::parse($b->trip->trip_date)->format('d M Y') : '—' }} ·
                                        {{ $b->trip->departure_time ? \Carbon\Carbon::parse($b->trip->departure_time)->format('h:i A') : '' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Trip removed</span>
                                @endif
                            </td>

                            {{-- Stops --}}
                            <td class="px-5 py-4 text-[11px]">
                                <div class="font-semibold text-slate-800">
                                    {{ $b->fromStop->name ?? 'Origin' }}
                                </div>
                                <div class="text-slate-400 flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-down text-[9px]"></i>
                                    <span>{{ $b->toStop->name ?? 'Destination' }}</span>
                                </div>
                            </td>

                            {{-- Seats --}}
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-mono font-extrabold text-slate-800 text-xs">
                                    {{ $b->seats }}
                                </span>
                            </td>

                            {{-- Fare --}}
                            <td class="px-5 py-4 font-extrabold text-slate-900">
                                Rs. {{ number_format($b->total_fare ?? 0) }}
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4">
                                @if ($st === 'confirmed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-extrabold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Confirmed
                                    </span>
                                @elseif ($st === 'cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-800 border border-rose-200 text-[11px] font-extrabold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-extrabold">
                                        {{ ucfirst($st) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Booked At --}}
                            <td class="px-5 py-4 text-[11px] text-slate-500">
                                {{ $b->created_at ? $b->created_at->format('d M Y, h:i A') : '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.bookings.show', $b->id) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors"
                                       title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if ($st === 'confirmed')
                                        <form action="{{ route('admin.bookings.cancel', $b->id) }}" method="POST"
                                              onsubmit="return confirm('Cancel this booking #{{ $b->booking_reference }} and return {{ $b->seats }} seat(s) to the trip schedule?');"
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors"
                                                    title="Cancel Booking">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($bookings->hasPages())
            <div class="p-5 border-t border-slate-100">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif

</div>

@endsection
