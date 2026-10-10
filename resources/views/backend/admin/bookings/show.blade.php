@extends('backend.admin.layouts.app')

@section('title', 'Booking #' . $booking->booking_reference . ' — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-200">
            <i class="fa-solid fa-ticket"></i> Passenger Reservation Record
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Booking #{{ $booking->booking_reference }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Reserved on {{ $booking->created_at ? $booking->created_at->format('l, d M Y · h:i A') : '—' }}
        </p>
    </div>
    <div class="flex items-center gap-2.5">
        @if ($booking->isConfirmed())
            <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to cancel this booking and return seats?');"
                  class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-extrabold transition-colors border border-rose-200">
                    <i class="fa-solid fa-ban"></i> Cancel Booking
                </button>
            </form>
        @endif
        <a href="{{ route('admin.bookings.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> All Bookings
        </a>
    </div>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Booking Overview (2 Columns) --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Ticket Header Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-wider block">Booking Reference</span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-teal-400 tracking-wider block mt-0.5">
                        {{ $booking->booking_reference }}
                    </span>
                    <span class="text-xs text-slate-400 block mt-1">
                        System Record ID: #{{ $booking->id }}
                    </span>
                </div>

                <div>
                    @php $st = strtolower($booking->booking_status ?? $booking->status); @endphp
                    @if ($st === 'confirmed')
                        <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Confirmed Reservation
                        </span>
                    @elseif ($st === 'cancelled')
                        <span class="px-3.5 py-1.5 rounded-full bg-rose-500/20 border border-rose-400/40 text-rose-300 font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span> Cancelled
                        </span>
                    @else
                        <span class="px-3.5 py-1.5 rounded-full bg-slate-800 border border-slate-700 text-slate-300 font-black text-xs uppercase tracking-wider">
                            {{ ucfirst($st) }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-6">
                
                {{-- Route & Scheduled Timings --}}
                <div class="space-y-3 pb-6 border-b border-slate-100">
                    <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-wider block">Journey & Schedule</span>
                    
                    <div class="text-lg font-black text-slate-900">
                        {{ $booking->trip->route->name ?? 'Standard Route' }}
                    </div>

                    <div class="flex items-center gap-2 text-sm font-bold text-teal-700">
                        <span>{{ $booking->fromStop->name ?? $booking->trip->route->origin ?? 'Origin' }}</span>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                        <span>{{ $booking->toStop->name ?? $booking->trip->route->destination ?? 'Destination' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Travel Date</span>
                            <span class="font-extrabold text-slate-900 block mt-0.5">
                                {{ $booking->trip->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, d M Y') : '—' }}
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Departure Time</span>
                            <span class="font-extrabold font-mono text-slate-900 block mt-0.5">
                                {{ $booking->trip->departure_time ? \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') : '—' }}
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Pickup Point</span>
                            <span class="font-bold text-slate-800 block mt-0.5 truncate" title="{{ $booking->trip->pickup_point ?? 'Standard Terminal' }}">
                                {{ $booking->trip->pickup_point ?? 'Standard Terminal' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Seat & Fare Summary --}}
                <div class="space-y-3">
                    <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-wider block">Reservation & Fare</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-4 rounded-xl bg-teal-50/60 border border-teal-100 text-xs">
                            <span class="text-teal-700 text-[10px] font-bold uppercase tracking-wider block">Reserved Seats</span>
                            <span class="text-2xl font-black font-mono text-teal-800 block mt-1">
                                {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }}
                            </span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Per Seat Fare</span>
                            <span class="text-2xl font-black text-slate-800 block mt-1">
                                Rs. {{ number_format($booking->trip->fare ?? 0) }}
                            </span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Fare</span>
                            <span class="text-2xl font-black text-slate-900 block mt-1">
                                Rs. {{ number_format($booking->total_fare ?? 0) }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Sidebar: Passenger, Driver & Vehicle (1 Column) --}}
    <div class="space-y-6">

        {{-- Passenger Details --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user text-teal-600"></i> Passenger Details
            </h4>

            @if ($booking->user)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-700 font-extrabold flex items-center justify-center text-sm border border-teal-100 shrink-0">
                        {{ strtoupper(substr($booking->user->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900 text-sm">{{ $booking->user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $booking->user->email }}</div>
                    </div>
                </div>

                <div class="space-y-2 text-xs border-t border-slate-100 pt-3">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Phone:</span>
                        <span class="font-bold text-slate-700">{{ $booking->user->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Account Type:</span>
                        <span class="font-bold text-slate-700">Passenger (0)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">CNIC:</span>
                        <span class="font-bold text-slate-700">{{ $booking->user->cnic ?? '—' }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.users.show', $booking->user->id) }}"
                       class="text-xs font-bold text-teal-600 hover:underline flex items-center gap-1">
                        View User Profile <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            @else
                <p class="text-xs text-slate-400 italic">User record not found.</p>
            @endif
        </div>

        {{-- Assigned Driver & Vehicle --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-teal-600"></i> Driver & Vehicle
            </h4>

            @if ($booking->trip?->driver)
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-extrabold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr($booking->trip->driver->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900 text-sm">{{ $booking->trip->driver->name }}</div>
                        <div class="text-[11px] text-teal-600 font-bold">Approved Driver</div>
                    </div>
                </div>
                <div class="text-xs text-slate-500 mb-3">
                    <span>Contact: </span><strong>{{ $booking->trip->driver->phone ?? '—' }}</strong>
                </div>
            @endif

            @if ($booking->trip?->vehicle)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                    <div class="font-mono font-black text-slate-900">{{ $booking->trip->vehicle->registration_no }}</div>
                    <div class="text-slate-500">{{ $booking->trip->vehicle->type }} · {{ $booking->trip->vehicle->model }}</div>
                    <div class="text-slate-400 text-[10px]">Total Capacity: {{ $booking->trip->vehicle->total_seats }} seats</div>
                </div>
            @endif

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.trips.show', $booking->trip_id) }}"
                   class="text-xs font-bold text-teal-600 hover:underline flex items-center gap-1">
                    View Trip Operational Sheet <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

</div>

@endsection
