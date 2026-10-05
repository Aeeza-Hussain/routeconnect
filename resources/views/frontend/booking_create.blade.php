@extends('frontend.layouts.app')

@section('title', 'Book Seats — ' . ($trip->route->name ?? 'Trip #' . $trip->id) . ' — RouteConnect')

@section('content')

{{-- ─── Hero Header ─── --}}
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.15),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-4">
            <a href="{{ url('/trips/' . $trip->id) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-emerald-400 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Trip Details</span>
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-ticket text-emerald-400"></i> Passenger Seat Booking
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Reserve Your Seats
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    Route: <strong class="text-emerald-400">{{ $trip->route->name ?? 'Direct Service' }}</strong> • {{ $trip->route->origin ?? 'Origin' }} to {{ $trip->route->destination ?? 'Destination' }}
                </p>
            </div>

            <div class="self-start sm:self-center">
                @if ($trip->available_seats <= 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-400/30 text-rose-300 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-ban text-rose-400"></i> Fully Booked
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-black uppercase tracking-wider">
                        <i class="fa-solid fa-chair text-emerald-400"></i> {{ $trip->available_seats }} {{ \Illuminate\Support\Str::plural('Seat', $trip->available_seats) }} Left
                    </span>
                @endif
            </div>
        </div>

    </div>
</section>

{{-- ─── Main Content ─── --}}
<section class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Validation Error Alerts --}}
        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-rose-900">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>Please correct the following:</span>
                </div>
                <ul class="list-disc list-inside pl-1 space-y-0.5 text-xs text-rose-700">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($trip->available_seats <= 0)
            <div class="p-6 rounded-3xl bg-rose-50 border border-rose-200 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xl mx-auto">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <h3 class="text-base font-extrabold text-rose-900">This Trip is Fully Booked</h3>
                <p class="text-xs text-rose-700 max-w-md mx-auto">
                    All seats on this scheduled trip have been reserved. Please check other departure dates or alternative routes.
                </p>
                <div class="pt-2">
                    <a href="{{ url('/trips') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search Other Trips</span>
                    </a>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left 2 Columns: Booking Form & Passenger Details --}}
            <div class="lg:col-span-2 space-y-6">

                <form method="POST" action="{{ url('/booking/' . $trip->id) }}" id="booking-form" class="space-y-6">
                    @csrf

                    {{-- Card: Number of Seats --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                    <i class="fa-solid fa-couch text-emerald-600"></i>
                                    <span>Select Number of Seats</span>
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Choose how many passenger seats you wish to reserve.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-mono text-xs font-bold">
                                Rs. {{ number_format($trip->fare ?? 0, 2) }} / seat
                            </span>
                        </div>

                        <div>
                            <label for="seats" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Number of Seats to Book <span class="text-rose-500">*</span>
                            </label>

                            <div class="relative">
                                <input 
                                    type="number" 
                                    id="seats" 
                                    name="seats" 
                                    value="{{ old('seats', 1) }}" 
                                    min="1" 
                                    max="{{ $trip->available_seats }}" 
                                    step="1"
                                    {{ $trip->available_seats <= 0 ? 'disabled' : '' }}
                                    required
                                    class="w-full px-4 py-3.5 rounded-2xl border {{ $errors->has('seats') ? 'border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:ring-emerald-500' }} text-slate-900 font-mono text-lg font-extrabold focus:outline-none focus:ring-2 bg-slate-50/50"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 pointer-events-none">
                                    Max: {{ $trip->available_seats }}
                                </span>
                            </div>

                            <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-slate-400"></i>
                                <span>Seats must be at least 1 and cannot exceed {{ $trip->available_seats }} available seats.</span>
                            </p>
                        </div>

                        {{-- Total Calculation Bar --}}
                        <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Estimated Total Fare</span>
                                <span class="text-xs text-emerald-700">Calculated based on selected seat count</span>
                            </div>
                            <div class="text-right">
                                <span id="calculated-total" class="text-2xl font-black font-mono text-emerald-900 block">
                                    Rs. {{ number_format(($trip->fare ?? 0) * (int) old('seats', 1), 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Submit Booking Button --}}
                        <div>
                            @if ($trip->available_seats > 0)
                                <button type="submit" id="submit-booking-btn" class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.01] active:scale-[0.99]">
                                    <i class="fa-solid fa-shield-check"></i>
                                    <span>Confirm & Book Seats</span>
                                </button>
                            @else
                                <button type="button" disabled id="submit-booking-btn" class="w-full py-4 px-6 rounded-2xl bg-slate-200 text-slate-400 font-extrabold text-sm flex items-center justify-center gap-2 cursor-not-allowed">
                                    <i class="fa-solid fa-ban"></i>
                                    <span>Fully Booked</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Passenger Information Card --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-4">
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Authenticated Passenger</h3>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                Verified Passenger
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] font-bold uppercase block">Passenger Name</span>
                                <span class="font-extrabold text-slate-900 text-sm block mt-0.5">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] font-bold uppercase block">Contact Email</span>
                                <span class="font-mono text-slate-800 text-xs block mt-0.5 truncate">{{ auth()->user()->email }}</span>
                            </div>
                            @if (auth()->user()->phone)
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                                    <span class="text-slate-400 text-[10px] font-bold uppercase block">Contact Phone</span>
                                    <span class="font-mono text-slate-800 text-xs block mt-0.5">{{ auth()->user()->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                </form>

            </div>

            {{-- Right Column: Trip Summary Card --}}
            <div class="space-y-6">

                <div class="bg-gradient-to-br from-slate-900 to-slate-950 rounded-3xl p-6 sm:p-7 text-white space-y-5 shadow-xl border border-slate-800">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Trip Summary</span>
                        <span class="text-xs font-mono font-bold text-slate-400">#{{ $trip->id }}</span>
                    </div>

                    {{-- Route --}}
                    <div class="space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Route</span>
                        <div class="text-base font-extrabold text-white">
                            {{ $trip->route->name ?? 'Standard Route' }}
                        </div>
                        <div class="text-xs text-slate-300 flex items-center gap-1.5 pt-0.5">
                            <span>{{ $trip->route->origin ?? 'Origin' }}</span>
                            <i class="fa-solid fa-arrow-right text-emerald-400 text-[10px]"></i>
                            <span>{{ $trip->route->destination ?? 'Destination' }}</span>
                        </div>
                    </div>

                    {{-- Date & Time --}}
                    <div class="pt-3 border-t border-slate-800/80 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Date</span>
                            <span class="font-bold text-white block mt-0.5">
                                {{ $trip->trip_date ? \Carbon\Carbon::parse($trip->trip_date)->format('d M Y') : '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Departure</span>
                            <span class="font-bold text-white block mt-0.5 font-mono">
                                {{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('h:i A') : '—' }}
                            </span>
                        </div>
                    </div>

                    {{-- Boarding Point --}}
                    <div class="pt-3 border-t border-slate-800/80 text-xs">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Boarding Terminal</span>
                        <span class="font-semibold text-slate-200 block mt-0.5 truncate" title="{{ $trip->pickup_point ?? 'Main Bus Terminal' }}">
                            {{ $trip->pickup_point ?? 'Main Bus Terminal' }}
                        </span>
                    </div>

                    {{-- Vehicle & Driver --}}
                    <div class="pt-3 border-t border-slate-800/80 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Driver</span>
                            <span class="font-bold text-white">{{ $trip->driver->name ?? 'Commercial Driver' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Vehicle</span>
                            <span class="font-mono font-bold text-white">{{ $trip->vehicle->registration_no ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Available Seats</span>
                            <span class="font-mono font-black {{ $trip->available_seats > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $trip->available_seats }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

{{-- Realtime Fare Calculator Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const seatsInput = document.getElementById('seats');
        const totalSpan = document.getElementById('calculated-total');
        const unitFare = {{ (float) ($trip->fare ?? 0) }};

        if (seatsInput && totalSpan) {
            function updateTotal() {
                const count = parseInt(seatsInput.value, 10) || 0;
                const total = (count * unitFare).toFixed(2);
                totalSpan.textContent = 'Rs. ' + Number(total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            seatsInput.addEventListener('input', updateTotal);
            seatsInput.addEventListener('change', updateTotal);
        }
    });
</script>

@endsection
