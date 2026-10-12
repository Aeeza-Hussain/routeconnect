@extends('backend.admin.layouts.app')

@section('title', 'Booking #' . $booking->booking_reference . ' — RouteConnect Admin')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-ticket"></i> Reservation Details
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Booking #{{ $booking->booking_reference }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Registered on {{ $booking->created_at ? $booking->created_at->format('M d, Y · h:i A') : '—' }}
        </p>
    </div>
    <a href="{{ route('admin.bookings.index') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> All Bookings
    </a>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@php
    $st = strtolower($booking->booking_status ?? $booking->status);
    $isConfirmed = $st === 'confirmed';
@endphp

<div class="max-w-4xl space-y-6">

    {{-- Ticket Card --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Banner --}}
        <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Booking Reference</span>
                <span class="text-2xl font-black font-mono text-emerald-400 tracking-wider block mt-0.5">
                    {{ $booking->booking_reference }}
                </span>
            </div>
            <div>
                @if ($isConfirmed)
                    <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 font-extrabold text-xs uppercase tracking-wider">
                        Confirmed & Active
                    </span>
                @else
                    <span class="px-3.5 py-1.5 rounded-full bg-rose-500/20 border border-rose-400/40 text-rose-300 font-extrabold text-xs uppercase tracking-wider">
                        Cancelled
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            {{-- Service & Route --}}
            <div class="pb-6 border-b border-slate-100 space-y-3">
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Route Details</span>
                <div class="text-lg font-black text-slate-900">
                    {{ $booking->trip->route->name ?? 'Standard Route' }}
                </div>
                <div class="flex items-center gap-2 text-sm font-bold text-emerald-700">
                    <span>{{ $booking->fromStop->name ?? $booking->trip->route?->start_location ?? 'Origin' }}</span>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                    <span>{{ $booking->toStop->name ?? $booking->trip->route?->end_location ?? 'Destination' }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs pt-3">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Travel Date</span>
                        <span class="font-extrabold text-slate-900 block mt-0.5">
                            {{ $booking->trip->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, d M Y') : '—' }}
                        </span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Departure Time</span>
                        <span class="font-extrabold font-mono text-slate-900 block mt-0.5">
                            {{ $booking->trip->departure_time ? \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') : '—' }}
                        </span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Seats Reserved</span>
                        <span class="font-black font-mono text-slate-900 block mt-0.5">
                            {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }}
                        </span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Fare</span>
                        <span class="font-black font-mono text-emerald-700 text-sm block mt-0.5">
                            Rs. {{ number_format($booking->total_fare ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Passenger & Driver Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-slate-100 text-xs">
                {{-- Passenger Card --}}
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Passenger Information</span>
                    <div class="font-black text-slate-900 text-sm">{{ $booking->user->name ?? 'Guest Passenger' }}</div>
                    <div class="text-slate-600 font-mono">{{ $booking->user->email ?? '—' }}</div>
                    @if ($booking->user && $booking->user->phone)
                        <div class="text-slate-600 font-mono"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $booking->user->phone }}</div>
                    @endif
                </div>

                {{-- Driver & Vehicle Card --}}
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Vehicle & Driver Assigned</span>
                    <div class="font-black text-slate-900 text-sm">{{ $booking->trip->driver->name ?? 'Commercial Driver' }}</div>
                    <div class="font-mono text-slate-700 font-bold">
                        {{ $booking->trip->vehicle->registration_no ?? 'Vehicle' }} ({{ $booking->trip->vehicle->model ?? '' }})
                    </div>
                    <div class="text-slate-500 text-[11px]">
                        Pickup: {{ $booking->trip->pickup_point ?? 'General Bus Terminal' }}
                    </div>
                </div>
            </div>

            {{-- Footer Action --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.trips.show', $booking->trip_id) }}" class="text-xs font-bold text-slate-700 hover:text-emerald-600 inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-route"></i> View Scheduled Trip Details
                </a>

                @if ($isConfirmed)
                    <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel booking #{{ $booking->booking_reference }}? This will return {{ $booking->seats }} seats to the scheduled trip.')">
                        @csrf
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-black text-xs transition-all shadow-md shadow-rose-600/20 inline-flex items-center gap-2">
                            <i class="fa-solid fa-ban"></i> Cancel Booking & Restore Seats
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection
