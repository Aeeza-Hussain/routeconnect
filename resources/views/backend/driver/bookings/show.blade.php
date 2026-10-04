@extends('backend.driver.layouts.app')

@section('title', 'Booking #' . ($booking->booking_reference ?? $booking->id) . ' — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-ticket"></i> Passenger Reservation Details
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
            Booking #{{ $booking->booking_reference ?? $booking->id }}
        </h2>
        <p class="text-sm text-slate-400 mt-1">
            Confirmed reservation for trip: {{ $booking->trip->route->origin ?? 'Origin' }} → {{ $booking->trip->route->destination ?? 'Destination' }}
        </p>
    </div>
    <div class="flex items-center gap-2.5">
        <a href="{{ route('driver.trips.show', $booking->trip_id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-all shadow-lg shadow-teal-600/20">
            <i class="fa-solid fa-route"></i> Trip Operational View
        </a>
        <a href="{{ route('driver.bookings') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> All Bookings
        </a>
    </div>
</div>

<div class="max-w-4xl space-y-6">

    {{-- Main Booking Overview Card --}}
    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800 mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Booking Reference</span>
                <span class="text-2xl font-black text-white font-mono">#{{ $booking->booking_reference ?? $booking->id }}</span>
            </div>
            <div>
                @php $st = strtolower($booking->status); @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase
                    {{ $st === 'confirmed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                    {{ $st === 'completed' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : '' }}
                    {{ $st === 'cancelled' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20' }}">
                    <span class="w-2 h-2 rounded-full {{ $st === 'confirmed' ? 'bg-emerald-400' : ($st === 'completed' ? 'bg-sky-400' : 'bg-rose-400') }}"></span>
                    {{ $booking->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-xs mb-6">
            {{-- Passenger Information --}}
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Passenger</span>
                <div class="font-extrabold text-white text-sm">{{ $booking->user->name ?? 'Passenger' }}</div>
                <div class="text-slate-400 mt-1">{{ $booking->user->email ?? 'No email' }}</div>
                <div class="text-teal-400 mt-0.5">{{ $booking->user->phone ?? 'No phone number' }}</div>
            </div>

            {{-- Seats & Allocation --}}
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Seat Allocation</span>
                <div class="font-extrabold text-white text-sm font-mono text-teal-400">
                    {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('Seat', $booking->seats) }}
                </div>
                @if ($booking->seat_numbers)
                    <div class="text-slate-400 mt-1 font-mono">Seat Numbers: {{ $booking->seat_numbers }}</div>
                @endif
                <div class="text-emerald-400 mt-1 font-bold">Total Fare: Rs. {{ number_format($booking->total_fare ?? 0, 2) }}</div>
            </div>

            {{-- Journey Segment --}}
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Journey Segment</span>
                <div class="font-bold text-white text-xs">
                    {{ $booking->fromStop->name ?? 'Boarding Stop' }}
                </div>
                <div class="text-slate-500 text-[10px] my-0.5">to</div>
                <div class="font-bold text-white text-xs">
                    {{ $booking->toStop->name ?? 'Destination Stop' }}
                </div>
            </div>
        </div>

        {{-- Associated Trip Info --}}
        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-xs">
            <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Trip Journey Details</h4>
            <div class="flex flex-wrap items-center justify-between gap-4 text-slate-300">
                <div>
                    <span class="text-slate-500">Route:</span>
                    <strong class="text-white">{{ $booking->trip->route->origin ?? 'Origin' }} → {{ $booking->trip->route->destination ?? 'Destination' }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Departure:</span>
                    <strong class="text-white">{{ $booking->trip->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('d M Y') : '—' }} at {{ $booking->trip->departure_time }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Vehicle:</span>
                    <strong class="text-teal-400 font-mono">{{ $booking->trip->vehicle->registration_no ?? 'Assigned Vehicle' }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Booked On:</span>
                    <strong class="text-white">{{ $booking->created_at->format('d M Y, h:i A') }}</strong>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
