@extends('backend.driver.layouts.app')

@section('title', 'Passenger Bookings — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-ticket"></i> Passenger Reservations
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Passenger Bookings</h2>
        <p class="text-sm text-slate-400 mt-1">
            Confirmed passenger reservations and seat allocations for your scheduled trips.
        </p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

{{-- ─── Filters ─── --}}
<div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-4 mb-6">
    <form method="GET" action="{{ route('driver.bookings') }}" class="flex flex-col sm:flex-row items-center gap-3">

        {{-- Filter by Trip --}}
        <div class="flex-1 w-full">
            <select name="trip_id" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
                <option value="">All My Trips</option>
                @foreach ($driverTrips as $t)
                    <option value="{{ $t->id }}" {{ request('trip_id') == $t->id ? 'selected' : '' }}>
                        Trip #{{ $t->id }}: {{ $t->route->origin ?? 'Origin' }} → {{ $t->route->destination ?? 'Destination' }} ({{ $t->trip_date ? \Carbon\Carbon::parse($t->trip_date)->format('d M') : '' }} · {{ $t->departure_time }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter by Booking Status --}}
        <div class="w-full sm:w-48">
            <select name="status" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
                <option value="">All Booking Statuses</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
            Filter
        </button>

        @if(request()->hasAny(['trip_id', 'status']))
            <a href="{{ route('driver.bookings') }}" class="w-full sm:w-auto text-center px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- ─── Bookings Table ─── --}}
<div class="space-y-6">

    @if ($bookings->isEmpty())
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h3 class="text-base font-extrabold text-white mb-1">No Bookings Found</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                @if(request()->hasAny(['trip_id', 'status']))
                    No reservations match the specified filter criteria.
                @else
                    Passenger reservations on your scheduled trips will appear here in real-time.
                @endif
            </p>
        </div>
    @else
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Booking Reference</th>
                            <th class="px-6 py-4">Passenger Name</th>
                            <th class="px-6 py-4">Trip</th>
                            <th class="px-6 py-4">Seats</th>
                            <th class="px-6 py-4">Booking Status</th>
                            <th class="px-6 py-4">Booking Date</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach ($bookings as $booking)
                            <tr class="hover:bg-slate-900/40 transition-colors">
                                {{-- 1. Booking Reference --}}
                                <td class="px-6 py-4 font-mono font-bold text-teal-400 text-sm">
                                    #{{ $booking->booking_reference ?? $booking->id }}
                                </td>

                                {{-- 2. Passenger Name --}}
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-white text-sm">
                                        {{ $booking->user->name ?? 'Passenger' }}
                                    </div>
                                    @if ($booking->user && $booking->user->phone)
                                        <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-phone text-[9px] text-slate-500"></i>
                                            {{ $booking->user->phone }}
                                        </div>
                                    @endif
                                </td>

                                {{-- 3. Trip --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white">
                                        {{ $booking->trip->route->origin ?? 'Origin' }} → {{ $booking->trip->route->destination ?? 'Destination' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $booking->trip->trip_date ? \Carbon\Carbon::parse($booking->trip->trip_date)->format('d M Y') : '—' }} · {{ $booking->trip->departure_time }}
                                    </div>
                                    @if ($booking->fromStop && $booking->toStop)
                                        <div class="text-[10px] text-teal-400 mt-0.5">
                                            {{ $booking->fromStop->name }} → {{ $booking->toStop->name }}
                                        </div>
                                    @endif
                                </td>

                                {{-- 4. Seats --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 font-mono font-bold text-teal-400">
                                        <i class="fa-solid fa-chair text-[10px]"></i>
                                        {{ $booking->seats }} {{ \Illuminate\Support\Str::plural('seat', $booking->seats) }}
                                    </span>
                                    @if($booking->seat_numbers)
                                        <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                            Seats: {{ $booking->seat_numbers }}
                                        </div>
                                    @endif
                                </td>

                                {{-- 5. Booking Status --}}
                                <td class="px-6 py-4">
                                    @php $st = strtolower($booking->status); @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                        {{ $st === 'confirmed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                        {{ $st === 'completed' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : '' }}
                                        {{ $st === 'cancelled' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st === 'confirmed' ? 'bg-emerald-400' : ($st === 'completed' ? 'bg-sky-400' : 'bg-rose-400') }}"></span>
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>

                                {{-- 6. Booking Date --}}
                                <td class="px-6 py-4 text-slate-400">
                                    <div>{{ $booking->created_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $booking->created_at->format('h:i A') }}</div>
                                </td>

                                {{-- Action --}}
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('driver.bookings.show', $booking->id) }}"
                                       class="px-3 py-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/20 text-xs font-bold transition-all inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                        <span>View</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection
