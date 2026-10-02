@extends('backend.admin.layouts.app')

@section('title', 'Edit Stop — ' . $stop->name . ' — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-pen"></i> Edit Transit Stop
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            Edit: {{ $stop->name }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">Update stop details, address description, or operational status.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.stops.show', $stop->id) }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-eye"></i> View Details
        </a>
        <a href="{{ route('admin.stops.index') }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Stops
        </a>
    </div>
</div>

{{-- ─── Form Card ─── --}}
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <form action="{{ route('admin.stops.update', $stop->id) }}" method="POST" class="p-6 space-y-6">
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

            {{-- Stop Name --}}
            <div>
                <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Stop Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $stop->name) }}" required
                       class="w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Location --}}
            <div>
                <label for="location" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Location / Area Description
                </label>
                <input type="text" id="location" name="location"
                       value="{{ old('location', $stop->location) }}"
                       class="w-full px-4 py-3 bg-slate-50 border @error('location') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                @error('location')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Stop Status <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('status', $stop->status) === 'Active' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Active" class="sr-only"
                               {{ old('status', $stop->status) === 'Active' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50','border-rose-500','bg-rose-50'));this.closest('label').classList.add('border-emerald-500','bg-emerald-50')">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Active</div>
                            <div class="text-[11px] text-slate-500">Available to assign to routes</div>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('status', $stop->status) === 'Inactive' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-slate-300 bg-slate-50' }}">
                        <input type="radio" name="status" value="Inactive" class="sr-only"
                               {{ old('status', $stop->status) === 'Inactive' ? 'checked' : '' }}
                               onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-emerald-500','bg-emerald-50','border-rose-500','bg-rose-50'));this.closest('label').classList.add('border-rose-500','bg-rose-50')">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                            <i class="fa-solid fa-circle-pause"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Inactive</div>
                            <div class="text-[11px] text-slate-500">Temporarily out of service</div>
                        </div>
                    </label>
                </div>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.stops.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Stop
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
