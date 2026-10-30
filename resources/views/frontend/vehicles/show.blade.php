@extends('frontend.layouts.app')

@section('title', 'Vehicle Details: ' . $vehicle->model . ' (' . $vehicle->registration_no . ') — RouteConnect')

@section('content')

<section class="py-12 bg-slate-50 dark:bg-[#090e17] min-h-screen transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Top Back Navigation --}}
        <div>
            <a href="{{ url('/book-vehicle') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to All Available Vehicles</span>
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-300 text-xs sm:text-sm space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>Please resolve the following issues:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-2">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Vehicle Showcase Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            
            {{-- Header Banner --}}
            <div class="p-6 sm:p-8 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-wider">
                        <i class="fa-solid fa-car-side"></i> Private Rental Vehicle
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        {{ $vehicle->model }}
                    </h1>
                    <div class="text-xs text-slate-400 font-mono">
                        Registration: <strong class="text-emerald-400">{{ $vehicle->registration_no }}</strong> · {{ $vehicle->type }}
                    </div>
                </div>

                <div class="text-right flex flex-col items-end gap-2">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Standard Daily Rate</span>
                        <span class="text-2xl font-black font-mono text-emerald-400">
                            Rs. {{ number_format($vehicle->rental_rate_per_day ?: 4000, 2) }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">per 24-hour cycle</span>
                    </div>
                    <a href="{{ route('messages.start') }}?vehicle_id={{ $vehicle->id }}"
                       class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-emerald-600/20 transition-all">
                        <i class="fa-solid fa-comments"></i>
                        <span>Message Driver</span>
                    </a>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-8">
                
                {{-- Specifications Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Vehicle Type</span>
                        <strong class="text-base text-slate-900 dark:text-white mt-0.5 block">{{ $vehicle->type }}</strong>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Passenger Capacity</span>
                        <strong class="text-base text-slate-900 dark:text-white mt-0.5 block">{{ $vehicle->total_seats }} Seats</strong>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Assigned Driver</span>
                        <strong class="text-base text-slate-900 dark:text-white mt-0.5 block truncate">{{ $vehicle->driver?->name ?? 'Commercial Driver' }}</strong>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Approval Status</span>
                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-extrabold mt-1">
                            <i class="fa-solid fa-circle-check text-xs"></i> Verified & Active
                        </span>
                    </div>
                </div>

                {{-- Availability Banner --}}
                @if($isAvailable)
                    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-emerald-950 dark:text-emerald-200">Vehicle Available for Selected Dates</h4>
                                <p class="text-emerald-800 dark:text-emerald-300 text-[11px] mt-0.5">
                                    {{ $startDatetime->format('d M Y h:i A') }} to {{ $endDatetime->format('d M Y h:i A') }} ({{ $pricing['days'] }} {{ \Illuminate\Support\Str::plural('day', $pricing['days']) }}).
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-emerald-700 dark:text-emerald-400 uppercase font-bold block">Estimated Fare</span>
                            <span class="font-mono font-black text-emerald-900 dark:text-emerald-200 text-base">Rs. {{ number_format($pricing['total_fare'], 2) }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xl shrink-0"></i>
                        <div>
                            <h4 class="font-extrabold text-rose-900 dark:text-rose-200">Vehicle Unavailable for Selected Range</h4>
                            <p class="text-rose-700 dark:text-rose-300 text-[11px] mt-0.5">{{ $conflictReason }}</p>
                        </div>
                    </div>
                @endif

                {{-- Reservation Form --}}
                <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                        <span>Confirm Reservation Details</span>
                    </h3>

                    <form method="POST" action="{{ route('vehicles.book.reserve', $vehicle->id) }}" class="space-y-5 text-xs">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">Pickup Location *</label>
                                <input type="text"
                                       name="pickup_location"
                                       value="{{ old('pickup_location', $pickup) }}"
                                       required
                                       placeholder="e.g. Islamabad International Airport or Address"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">Destination *</label>
                                <input type="text"
                                       name="destination_location"
                                       value="{{ old('destination_location', $destination) }}"
                                       required
                                       placeholder="e.g. Murree Mall Road or City"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">Pickup Date *</label>
                                <input type="date"
                                       name="pickup_date"
                                       id="pickup_date"
                                       value="{{ old('pickup_date', $pickupDate) }}"
                                       min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                       required
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">Pickup Time *</label>
                                <input type="time"
                                       name="pickup_time"
                                       value="{{ old('pickup_time', $pickupTime) }}"
                                       required
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">Return Date *</label>
                                <input type="date"
                                       name="return_date"
                                       id="return_date"
                                       value="{{ old('return_date', $returnDate) }}"
                                       min="{{ $pickupDate }}"
                                       required
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">Return Time *</label>
                                <input type="time"
                                       name="return_time"
                                       value="{{ old('return_time', $returnTime) }}"
                                       required
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Special Instructions / Luggage Notes (Optional)</label>
                            <textarea name="passenger_notes"
                                      rows="2"
                                      placeholder="Provide any specific route requirements, extra luggage notes, or pickup landmarks..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium">{{ old('passenger_notes') }}</textarea>
                        </div>

                        @guest
                            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-300 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-lock text-base"></i>
                                    <span>You must be logged in to reserve this private vehicle.</span>
                                </div>
                                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-500 transition-colors">
                                    Log In Now
                                </a>
                            </div>
                        @endguest

                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="text-xs text-slate-500">
                                <span>No payment required right now. Booking locks vehicle immediately.</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('messages.start') }}?vehicle_id={{ $vehicle->id }}"
                                   class="px-5 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs flex items-center gap-1.5 transition-colors">
                                    <i class="fa-solid fa-comments text-emerald-600"></i>
                                    <span>Chat with Driver First</span>
                                </a>

                                <button type="submit"
                                        id="submit-reserve-btn"
                                        class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Complete Private Booking</span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
