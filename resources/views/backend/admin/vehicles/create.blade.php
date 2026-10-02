@extends('backend.admin.layouts.app')

@section('title', 'Add Vehicle — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-van-shuttle"></i> New Vehicle
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Add Vehicle</h2>
        <p class="text-sm text-slate-500 mt-1">Register a new transport vehicle and assign it to an approved driver.</p>
    </div>
    <a href="{{ route('admin.vehicles.index') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Vehicles
    </a>
</div>

{{-- ─── Form Card ─── --}}
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-emerald-500"></i>
                Only verified, approved drivers (user_type = 2 & status = approved) can be assigned.
            </h3>
        </div>

        <form action="{{ route('admin.vehicles.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold space-y-1">
                    <div class="font-extrabold text-sm mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:
                    </div>
                    <ul class="list-disc ml-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Assigned Driver --}}
            <div>
                <label for="user_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Assigned Driver <span class="text-rose-500">*</span>
                </label>
                @if ($approvedDrivers->isEmpty())
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                        <p class="font-bold mb-1 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                            No approved drivers available!
                        </p>
                        <p class="text-slate-600 mb-3">
                            A vehicle requires an approved driver. Please review pending driver applications first.
                        </p>
                        <a href="{{ route('admin.drivers.applications') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 transition-colors">
                            <i class="fa-solid fa-id-card"></i> Review Driver Applications
                        </a>
                    </div>
                @else
                    <select id="user_id" name="user_id" required
                            class="w-full px-4 py-3 bg-slate-50 border @error('user_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select an approved driver...</option>
                        @foreach ($approvedDrivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('user_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }} ({{ $driver->email }}) — {{ $driver->phone ?? 'No Phone' }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('user_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Vehicle Number + Model Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="registration_no" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Vehicle Number (Reg No) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="registration_no" name="registration_no"
                           value="{{ old('registration_no') }}" required
                           placeholder="e.g. LEE-2024 or ISB-5544"
                           class="w-full px-4 py-3 bg-slate-50 border @error('registration_no') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('registration_no')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-[11px] text-slate-400">Official license plate / registration number</p>
                </div>

                <div>
                    <label for="model" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Model / Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="model" name="model"
                           value="{{ old('model') }}" required
                           placeholder="e.g. Toyota HiAce 2022"
                           class="w-full px-4 py-3 bg-slate-50 border @error('model') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('model')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-[11px] text-slate-400">Make, model, and year</p>
                </div>
            </div>

            {{-- Vehicle Type + Seats Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="type" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Vehicle Type <span class="text-rose-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-3 bg-slate-50 border @error('type') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select type...</option>
                        <option value="Van"   {{ old('type') == 'Van'   ? 'selected' : '' }}>Van (HiAce, etc.)</option>
                        <option value="Daba"  {{ old('type') == 'Daba'  ? 'selected' : '' }}>Daba (Carry / Bolan)</option>
                        <option value="Car"   {{ old('type') == 'Car'   ? 'selected' : '' }}>Car (Sedan / Hatchback)</option>
                        <option value="Bus"   {{ old('type') == 'Bus'   ? 'selected' : '' }}>Bus (Coaster / Minibus)</option>
                        <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="total_seats" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Total Passenger Seats <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="total_seats" name="total_seats"
                           value="{{ old('total_seats', 14) }}" min="1" max="100" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('total_seats') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('total_seats')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-[11px] text-slate-400">Available passenger capacity (excluding driver)</p>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Status <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('status', 'Active') === 'Active' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Active" class="sr-only"
                               {{ old('status', 'Active') === 'Active' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50','border-rose-500','bg-rose-50'));this.closest('label').classList.add('border-emerald-500','bg-emerald-50')">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Active</div>
                            <div class="text-[11px] text-slate-500">Available for scheduling trips</div>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('status') === 'Inactive' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Inactive" class="sr-only"
                               {{ old('status') === 'Inactive' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50','border-rose-500','bg-rose-50'));this.closest('label').classList.add('border-rose-500','bg-rose-50')">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-circle-pause"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Inactive</div>
                            <div class="text-[11px] text-slate-500">Under maintenance / unavailable</div>
                        </div>
                    </label>
                </div>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.vehicles.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        @if ($approvedDrivers->isEmpty()) disabled class="px-6 py-2.5 rounded-xl bg-slate-300 text-slate-500 font-bold text-sm cursor-not-allowed inline-flex items-center gap-2"
                        @else class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition-all inline-flex items-center gap-2"
                        @endif>
                    <i class="fa-solid fa-plus"></i> Save Vehicle
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
