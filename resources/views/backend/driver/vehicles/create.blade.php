@extends('backend.driver.layouts.app')

@section('title', 'Register Vehicle — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-van-shuttle"></i> Fleet Registration
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Register Commercial Vehicle</h2>
        <p class="text-sm text-slate-400 mt-1">
            Submit your commercial transport vehicle details for administrator review and verification.
        </p>
    </div>
    <a href="{{ route('driver.vehicles.index') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Back to My Vehicles
    </a>
</div>

{{-- Error Messages --}}
@if ($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs sm:text-sm space-y-1">
        <div class="flex items-center gap-2 font-bold text-rose-200">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside pl-1 space-y-0.5 text-xs">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-2xl">
    <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-xl">
        <form method="POST" action="{{ route('driver.vehicles.store') }}" class="space-y-6">
            @csrf

            {{-- Vehicle Registration Number --}}
            <div>
                <label for="registration_no" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Vehicle Registration Number <span class="text-rose-400">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-hashtag"></i>
                    </span>
                    <input type="text"
                           id="registration_no"
                           name="registration_no"
                           value="{{ old('registration_no') }}"
                           required
                           placeholder="e.g. GLT-1234 or LEA-5566"
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-sm placeholder-slate-500 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 uppercase">
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">Official government registration number printed on vehicle number plate and registration book.</p>
            </div>

            {{-- Vehicle Type & Total Seats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Vehicle Type --}}
                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Vehicle Type <span class="text-rose-400">*</span>
                    </label>
                    <select id="type"
                            name="type"
                            required
                            class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                        <option value="">Select Vehicle Type</option>
                        <option value="Van" {{ old('type') == 'Van' ? 'selected' : '' }}>HiAce Van</option>
                        <option value="Bus" {{ old('type') == 'Bus' ? 'selected' : '' }}>Coaster Bus</option>
                        <option value="Daba" {{ old('type') == 'Daba' ? 'selected' : '' }}>Suzuki Bolan (Daba)</option>
                        <option value="Car" {{ old('type') == 'Car' ? 'selected' : '' }}>Passenger Car (Sedan)</option>
                        <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other Commercial</option>
                    </select>
                </div>

                {{-- Total Seats --}}
                <div>
                    <label for="total_seats" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Total Passenger Seat Capacity <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chair"></i>
                        </span>
                        <input type="number"
                               id="total_seats"
                               name="total_seats"
                               value="{{ old('total_seats', 14) }}"
                               min="1"
                               max="100"
                               required
                               placeholder="14"
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Max passenger seats excluding the driver.</p>
                </div>
            </div>

            {{-- Vehicle Make and Model --}}
            <div>
                <label for="model" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Make and Model <span class="text-rose-400">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-car"></i>
                    </span>
                    <input type="text"
                           id="model"
                           name="model"
                           value="{{ old('model') }}"
                           required
                           placeholder="e.g. Toyota HiAce Grand Cabin 2022"
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400">
                </div>
            </div>

            {{-- Verification Notice --}}
            <div class="p-4 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-xs text-slate-300 flex items-start gap-3">
                <i class="fa-solid fa-shield-halved text-teal-400 text-lg mt-0.5 shrink-0"></i>
                <div class="space-y-1">
                    <strong class="text-white block font-bold">Independent Vehicle Verification</strong>
                    <p class="text-slate-400 leading-relaxed">
                        After submitting, this vehicle will be listed in your fleet with <strong>Pending Approval</strong> status. Once reviewed and approved by the administrator, you will be able to schedule commercial trips with it.
                    </p>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-sm transition-all shadow-lg shadow-teal-500/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Submit Vehicle for Approval</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
