@extends('backend.driver.layouts.app')

@section('title', 'Vehicle Details: ' . $vehicle->registration_no . ' — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-van-shuttle"></i> Vehicle Record
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-mono">{{ $vehicle->registration_no }}</h2>
        <p class="text-sm text-slate-400 mt-1">
            {{ $vehicle->model }} · {{ $vehicle->type }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('driver.vehicles.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> Back to My Vehicles
        </a>
        @if ($vehicle->isApproved())
            <a href="{{ route('driver.trips.create') }}?vehicle_id={{ $vehicle->id }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-teal-500/20">
                <i class="fa-solid fa-calendar-plus"></i> Schedule Trip
            </a>
        @endif
    </div>
</div>

<div class="max-w-3xl space-y-6">

    {{-- ─── Vehicle Card ─── --}}
    <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-800/80 pb-6 mb-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-3xl border border-teal-500/20 shrink-0">
                    <i class="fa-solid fa-van-shuttle"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-2xl font-black text-white font-mono tracking-wide">{{ $vehicle->registration_no }}</h3>
                        @if ($vehicle->isApproved())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Approved
                            </span>
                        @elseif ($vehicle->isPending())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <i class="fa-solid fa-hourglass-half text-[10px] animate-pulse"></i> Pending Approval
                            </span>
                        @elseif ($vehicle->isRejected())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Rejected
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-400 font-semibold mt-1">
                        {{ $vehicle->model }} · {{ $vehicle->type }}
                    </p>
                </div>
            </div>

            <div class="text-xs text-slate-500 sm:text-right">
                <div>Registered: {{ $vehicle->created_at->format('d M Y') }}</div>
                <div class="text-teal-400 font-mono mt-0.5">Fleet ID #{{ $vehicle->id }}</div>
            </div>
        </div>

        {{-- Specs Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs mb-6">
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Vehicle Registration</span>
                <span class="font-black text-base text-white font-mono">{{ $vehicle->registration_no }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Vehicle Type</span>
                <span class="font-extrabold text-base text-white">{{ $vehicle->type }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Model</span>
                <span class="font-extrabold text-base text-white">{{ $vehicle->model }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Total Passenger Capacity</span>
                <span class="font-extrabold text-base text-teal-400 font-mono">{{ $vehicle->total_seats }} Seats</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Approval Status</span>
                <span class="font-extrabold text-base capitalize {{ $vehicle->isApproved() ? 'text-emerald-400' : ($vehicle->isRejected() ? 'text-rose-400' : 'text-amber-400') }}">
                    {{ $vehicle->status }}
                </span>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1 font-bold uppercase tracking-wider text-[10px]">Associated Trips</span>
                <span class="font-extrabold text-base text-white font-mono">{{ $vehicle->trips->count() }} Trips</span>
            </div>
        </div>

        {{-- Status Notification Banners --}}
        @if ($vehicle->isPending())
            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300 flex items-start gap-3">
                <i class="fa-solid fa-hourglass-half text-amber-400 text-lg mt-0.5 shrink-0 animate-pulse"></i>
                <div class="space-y-1">
                    <strong class="text-white block font-bold">Vehicle Under Review</strong>
                    <p class="text-amber-200/80 leading-relaxed">
                        This vehicle has been queued for administrator verification. You will be able to schedule commercial trips with this vehicle immediately once approved.
                    </p>
                </div>
            </div>
        @elseif ($vehicle->isRejected())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 space-y-2">
                <div class="flex items-center gap-2 font-bold text-rose-200">
                    <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                    <span>Vehicle Registration Rejected</span>
                </div>
                @if ($vehicle->rejection_reason)
                    <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-800/80 text-rose-200">
                        <strong>Reason:</strong> {{ $vehicle->rejection_reason }}
                    </div>
                @endif
                <p class="text-rose-300/80 text-[11px]">
                    If you believe this was an error, please reach out to platform support with updated vehicle documentation.
                </p>
            </div>
        @elseif ($vehicle->isApproved())
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300 flex items-start gap-3">
                <i class="fa-solid fa-circle-check text-emerald-400 text-lg mt-0.5 shrink-0"></i>
                <div class="space-y-1">
                    <strong class="text-white block font-bold">Verified & Active</strong>
                    <p class="text-emerald-200/80 leading-relaxed">
                        This vehicle is verified by RouteConnect administrators and eligible for scheduling public passenger trips and receiving private vehicle rentals.
                    </p>
                </div>
            </div>
        @endif
    </div>

    @if ($vehicle->isApproved())
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-teal-300 text-xs flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-teal-400 text-base"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- ─── 1. Private Rental Configuration ─── --}}
        <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-car-side text-teal-400"></i>
                        <span>Private Rental Settings</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Configure daily rental pricing and private reservation availability for passengers.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('driver.vehicles.rental_settings', $vehicle->id) }}" class="space-y-5 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-300">Daily Rental Rate (PKR) *</label>
                        <input type="number"
                               name="rental_rate_per_day"
                               value="{{ old('rental_rate_per_day', $vehicle->rental_rate_per_day ?: 4000) }}"
                               step="50"
                               min="500"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono font-bold focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <span class="text-[10px] text-slate-500">Charged per 24-hour rental window.</span>
                    </div>

                    <div class="space-y-1.5 flex flex-col justify-center">
                        <label class="block font-bold text-slate-300 mb-2">Private Rental Availability</label>
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="is_available_for_rental"
                                   value="1"
                                   {{ old('is_available_for_rental', $vehicle->is_available_for_rental) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-teal-500 bg-slate-900 border-slate-700 focus:ring-teal-500">
                            <span class="text-xs text-slate-200 font-bold">Accept Private Passenger Bookings</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-300">Vehicle Description / Amenities (Optional)</label>
                    <textarea name="description"
                              rows="2"
                              placeholder="AC, luxury seats, luggage capacity, sound system..."
                              class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $vehicle->description) }}</textarea>
                </div>

                <div class="text-right">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs transition-all shadow-md shadow-teal-500/20">
                        Save Rental Configuration
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── 2. Availability Schedule & Blocked Dates ─── --}}
        <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-calendar-xmark text-amber-400"></i>
                        <span>Manage Unavailable / Blocked Dates</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Block maintenance periods or personal time off to prevent passenger bookings.
                    </p>
                </div>
            </div>

            {{-- Form to Add Blocked Period --}}
            <form method="POST" action="{{ route('driver.vehicles.unavailabilities.store', $vehicle->id) }}" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-300">From Date *</label>
                        <input type="datetime-local" name="start_date" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-300">To Date *</label>
                        <input type="datetime-local" name="end_date" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-300">Reason (Optional)</label>
                        <input type="text" name="reason" placeholder="e.g. Scheduled Service / Routine Check" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-300 hover:bg-amber-500/30 font-bold text-xs transition-colors">
                        <i class="fa-solid fa-ban mr-1"></i> Block Selected Schedule
                    </button>
                </div>
            </form>

            {{-- List of Current Blocked Periods --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Current Blocked Periods ({{ $vehicle->unavailabilities->count() }})</h4>
                @if($vehicle->unavailabilities->isEmpty())
                    <p class="text-xs text-slate-500 italic">No unavailable dates currently recorded. Vehicle is open for rental reservations.</p>
                @else
                    <div class="divide-y divide-slate-800 border border-slate-800 rounded-2xl overflow-hidden text-xs">
                        @foreach($vehicle->unavailabilities as $blocked)
                            <div class="p-3.5 bg-slate-900/60 flex items-center justify-between gap-3">
                                <div>
                                    <span class="font-mono font-bold text-white">
                                        {{ $blocked->start_date ? $blocked->start_date->format('d M Y h:i A') : '' }}
                                        to
                                        {{ $blocked->end_date ? $blocked->end_date->format('d M Y h:i A') : '' }}
                                    </span>
                                    <span class="text-slate-400 block text-[11px] mt-0.5">{{ $blocked->reason ?: 'Driver blocked period' }}</span>
                                </div>
                                <form method="POST" action="{{ route('driver.vehicles.unavailabilities.destroy', [$vehicle->id, $blocked->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-300 text-xs font-bold" title="Remove Block">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── 3. Upcoming Private Reservations ─── --}}
        <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-teal-400"></i>
                        <span>Recent Private Passenger Reservations</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Private vehicle bookings requested by passengers for this vehicle.
                    </p>
                </div>
            </div>

            @if($vehicle->bookings->isEmpty())
                <p class="text-xs text-slate-500 italic py-2">No private reservations created for this vehicle yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-[10px] text-slate-400 uppercase font-mono border-b border-slate-800">
                            <tr>
                                <th class="pb-2">Reference</th>
                                <th class="pb-2">Passenger</th>
                                <th class="pb-2">Routing</th>
                                <th class="pb-2">Rental Period</th>
                                <th class="pb-2">Total Fare</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-slate-300">
                            @foreach($vehicle->bookings as $res)
                                <tr>
                                    <td class="py-3 font-mono font-bold text-teal-400">{{ $res->booking_reference }}</td>
                                    <td class="py-3 font-bold text-white">{{ $res->user?->name ?? 'Passenger' }}</td>
                                    <td class="py-3">
                                        {{ $res->pickup_location ?: 'Pickup' }}
                                        <i class="fa-solid fa-arrow-right text-[9px] text-slate-500 mx-1"></i>
                                        {{ $res->destination_location ?: 'Destination' }}
                                    </td>
                                    <td class="py-3 font-mono text-[11px]">
                                        {{ $res->start_datetime ? $res->start_datetime->format('d M') : '' }}
                                        -
                                        {{ $res->end_datetime ? $res->end_datetime->format('d M Y') : '' }}
                                        ({{ $res->days ?: 1 }}d)
                                    </td>
                                    <td class="py-3 font-mono font-bold text-white">Rs. {{ number_format($res->total_fare ?? 0, 2) }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $res->isCancelled() ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                            {{ $res->booking_status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

</div>

@endsection
