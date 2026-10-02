@extends('backend.admin.layouts.app')

@section('title', 'Edit Vehicle — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-pen"></i> Edit Vehicle
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Edit: <span class="font-mono text-indigo-600">{{ $vehicle->registration_no }}</span>
        </h2>
        <p class="text-sm text-slate-500 mt-1">Update vehicle details, seat capacity, or assigned driver.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.vehicles.show', $vehicle->id) }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-eye"></i> View
        </a>
        <a href="{{ route('admin.vehicles.index') }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- ─── Form Card ─── --}}
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

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
                <select id="user_id" name="user_id" required
                        class="w-full px-4 py-3 bg-slate-50 border @error('user_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select an approved driver...</option>
                    @foreach ($approvedDrivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('user_id', $vehicle->user_id) == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }} ({{ $driver->email }}) — {{ $driver->phone ?? 'No Phone' }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Vehicle Number + Model --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="registration_no" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Vehicle Number (Reg No) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="registration_no" name="registration_no"
                           value="{{ old('registration_no', $vehicle->registration_no) }}" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('registration_no') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('registration_no')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="model" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Model / Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="model" name="model"
                           value="{{ old('model', $vehicle->model) }}" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('model') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('model')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Type + Seats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="type" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Vehicle Type <span class="text-rose-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-3 bg-slate-50 border @error('type') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Van"   {{ old('type', $vehicle->type) == 'Van'   ? 'selected' : '' }}>Van</option>
                        <option value="Daba"  {{ old('type', $vehicle->type) == 'Daba'  ? 'selected' : '' }}>Daba</option>
                        <option value="Car"   {{ old('type', $vehicle->type) == 'Car'   ? 'selected' : '' }}>Car</option>
                        <option value="Bus"   {{ old('type', $vehicle->type) == 'Bus'   ? 'selected' : '' }}>Bus</option>
                        <option value="Other" {{ old('type', $vehicle->type) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="total_seats" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Total Passenger Seats <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="total_seats" name="total_seats"
                           value="{{ old('total_seats', $vehicle->total_seats) }}" min="1" max="100" required
                           class="w-full px-4 py-3 bg-slate-50 border @error('total_seats') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('total_seats')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Status <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('status', $vehicle->status) === 'Active' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Active" class="sr-only"
                               {{ old('status', $vehicle->status) === 'Active' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50','border-rose-500','bg-rose-50'));this.closest('label').classList.add('border-emerald-500','bg-emerald-50')">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Active</div>
                            <div class="text-[11px] text-slate-500">Available for trips</div>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('status', $vehicle->status) === 'Inactive' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Inactive" class="sr-only"
                               {{ old('status', $vehicle->status) === 'Inactive' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50','border-rose-500','bg-rose-50'));this.closest('label').classList.add('border-rose-500','bg-rose-50')">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-circle-pause"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Inactive</div>
                            <div class="text-[11px] text-slate-500">Temporarily unavailable</div>
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
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Vehicle
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
