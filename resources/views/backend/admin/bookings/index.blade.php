@extends('backend.admin.layouts.app')

@section('title', 'Passenger Bookings Management — RouteConnect Admin')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-ticket"></i> Passenger Reservations
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Passenger Bookings</h2>
        <p class="text-sm text-slate-500 mt-1">
            Monitor real-time passenger reservations, seat allocations, fare transactions, and status.
        </p>
    </div>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if (session('error'))
    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- ─── Search & Filters Bar ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
    <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by reference (e.g. RC-...), passenger name, or email..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
        </div>

        <select name="status" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Bookings ({{ $counts['all'] }})</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed ({{ $counts['confirmed'] }})</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled ({{ $counts['cancelled'] }})</option>
        </select>

        <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-bold transition-colors inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if (request('search') || (request('status') && request('status') !== 'all'))
            <a href="{{ route('admin.bookings.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-bold transition-colors text-center inline-flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- ─── Bookings Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-bold text-slate-700">
            {{ $bookings->total() }} {{ \Illuminate\Support\Str::plural('booking', $bookings->total()) }} found
        </div>
        <div class="text-xs text-slate-400">
            Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}
        </div>
    </div>

    @if ($bookings->isEmpty())
        <div class="p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700 mb-1">No bookings found</div>
            <p class="text-xs text-slate-500">No passenger reservations matched your search criteria.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <th class="px-6 py-3.5">Reference</th>
                        <th class="px-6 py-3.5">Passenger</th>
                        <th class="px-6 py-3.5">Route / Service</th>
                        <th class="px-6 py-3.5">Travel Date & Time</th>
                        <th class="px-6 py-3.5">Seats</th>
                        <th class="px-6 py-3.5">Total Fare</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($bookings as $booking)
                        @php
                            $st = strtolower($booking->booking_status ?? $booking->status);
                            $isConfirmed = $st === 'confirmed';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- Reference --}}
                            <td class="px-6 py-4">
                                <span class="font-mono font-black text-emerald-700 text-xs block">
                                    {{ $booking->booking_reference }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    Trip #{{ $booking->trip_id }}
                                </span>
                            </td>

                            {{-- Passenger --}}
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-slate-900 block text-xs">
                                    {{ $booking->user->name ?? 'Guest Passenger' }}
                                </span>
                                <span class="text-[10px] text-slate-400 block font-mono">
                                    {{ $booking->user->email ?? '' }}
                                </span>
                            </td>

                            {{-- Route --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block text-xs">
                                    {{ $booking->trip->route->name ?? 'Direct Service' }}
                                </span>
                                <span class="text-[10px] text-slate-500 block">
                                    {{ $booking->fromStop->name ?? $booking->trip->route?->start_location ?? 'Origin' }} →
                                    {{ $booking->toStop->name ?? $booking->trip->route?->end_location ?? 'Destination' }}
                                </span>
                            </td>

                            {{-- Date & Time --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block">
                                    {{ $booking->trip->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('d M Y') : '—' }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono block">
                                    {{ $booking->trip->departure_time ? \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') : '—' }}
                                </span>
                            </td>

                            {{-- Seats --}}
                            <td class="px-6 py-4">
                                <span class="font-mono font-black text-slate-800">
                                    {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }}
                                </span>
                            </td>

                            {{-- Total Fare --}}
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-900">
                                    Rs. {{ number_format($booking->total_fare ?? 0, 2) }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @if ($isConfirmed)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Confirmed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold border border-rose-200">
                                        <i class="fa-solid fa-ban text-[9px]"></i> Cancelled
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                       title="View Ticket"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold transition-colors">
                                        <i class="fa-solid fa-eye text-[11px]"></i>
                                    </a>

                                    @if ($isConfirmed)
                                        <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST"
                                              onsubmit="return confirm('Cancel booking #{{ $booking->booking_reference }}? This will return {{ $booking->seats }} seats to the scheduled trip.')">
                                            @csrf
                                            <button type="submit"
                                                    title="Cancel Booking & Restore Seats"
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold transition-colors border border-rose-200">
                                                <i class="fa-solid fa-ban text-[11px]"></i>
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

        @if ($bookings->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
