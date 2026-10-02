@extends('backend.driver.layouts.app')

@section('title', 'Passenger Bookings — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-ticket"></i> Reservations
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Passenger Bookings</h2>
        <p class="text-sm text-slate-400 mt-1">Confirmed passenger seat reservations for your trips.</p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

<div class="max-w-4xl space-y-6">

    @if ($bookings->isEmpty())
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h3 class="text-base font-extrabold text-white mb-1">No Bookings Yet</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Passenger reservations on your scheduled trips will appear here in real-time.
            </p>
        </div>
    @else
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Booking #</th>
                            <th class="px-6 py-3">Passenger</th>
                            <th class="px-6 py-3">Seats</th>
                            <th class="px-6 py-3">Fare</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-white">#{{ $booking->booking_reference }}</td>
                                <td class="px-6 py-4">{{ $booking->user->name ?? 'Passenger' }}</td>
                                <td class="px-6 py-4">{{ $booking->seats }} seats</td>
                                <td class="px-6 py-4 text-emerald-400 font-bold">Rs. {{ number_format($booking->total_fare, 2) }}</td>
                                <td class="px-6 py-4">{{ $booking->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection
