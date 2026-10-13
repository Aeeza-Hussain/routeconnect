@extends('backend.admin.layouts.app')

@section('title', 'Driver Application Details — RouteConnect Admin')

@section('content')

{{-- ───────────────────────────── PAGE HEADER ───────────────────────────── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-id-card"></i> Application Detail
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            {{ $driver->name }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">Full driver profile and application status management.</p>
    </div>
    <a href="{{ route('admin.drivers.applications') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Back to Applications
    </a>
</div>

{{-- ─────────────────────── FLASH MESSAGES ─────────────────────── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="max-w-4xl space-y-6">

    {{-- ─────────────────── PROFILE CARD ─────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Card Header with avatar and status --}}
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                {{-- Profile Photo --}}
                @if ($driver->profile_photo)
                    <img src="{{ asset($driver->profile_photo) }}"
                         alt="{{ $driver->name }}"
                         class="w-20 h-20 rounded-2xl object-cover border-4 border-white/20 shadow-lg">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-2xl border-4 border-white/20 shadow-lg">
                        {{ strtoupper(substr($driver->name, 0, 2)) }}
                    </div>
                @endif

                {{-- Name + Info --}}
                <div class="flex-1">
                    <h3 class="text-xl font-extrabold text-white">{{ $driver->name }}</h3>
                    <p class="text-sm text-slate-400 mt-0.5">{{ $driver->email }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Registered: {{ $driver->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>

                {{-- Status Badge --}}
                <div>
                    @if ($driver->driver_status === 'approved')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-300 text-sm font-extrabold border border-emerald-500/30">
                            <i class="fa-solid fa-circle-check"></i> Approved
                        </span>
                    @elseif ($driver->driver_status === 'pending')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500/20 text-amber-300 text-sm font-extrabold border border-amber-500/30">
                            <i class="fa-solid fa-clock"></i> Pending Review
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-500/20 text-rose-300 text-sm font-extrabold border border-rose-500/30">
                            <i class="fa-solid fa-circle-xmark"></i> Rejected
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Driver Info Grid --}}
        <div class="p-6">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Personal & Commercial Credentials</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->name }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->email }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Phone Number</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $driver->phone ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Driving License No.</div>
                    <div class="text-sm font-extrabold text-indigo-700 font-mono">{{ $driver->license_no ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">CNIC Number</div>
                    <div class="text-sm font-extrabold text-slate-900 font-mono">{{ $driver->cnic ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</div>
                    <div class="text-sm font-extrabold text-slate-900 capitalize">{{ $driver->gender ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Date of Birth</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        {{ $driver->dob ? \Carbon\Carbon::parse($driver->dob)->format('d M Y') : 'Not Provided' }}
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">User Type</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        Driver (user_type = {{ $driver->user_type }})
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Current Status</div>
                    <div class="text-sm font-extrabold capitalize
                        {{ $driver->driver_status === 'approved' ? 'text-emerald-600' : ($driver->driver_status === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                        {{ ucfirst($driver->driver_status) }}
                    </div>
                </div>

            </div>

            {{-- Bio / About --}}
            @if ($driver->bio)
                <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Bio / Experience</div>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $driver->bio }}</p>
                </div>
            @endif

            {{-- Prior Rejection Reason Alert if rejected --}}
            @if ($driver->driver_status === 'rejected' && $driver->rejection_reason)
                <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                    <div class="font-extrabold text-rose-900 flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>Reason for Rejection:</span>
                    </div>
                    <p class="leading-relaxed">{{ $driver->rejection_reason }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ─────────────────── REGISTERED FLEET VEHICLES ─────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-van-shuttle text-indigo-600"></i>
                    Registered Fleet Vehicles ({{ $driver->vehicles->count() }})
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Commercial transport vehicles registered by this driver. Each vehicle requires independent review.</p>
            </div>
            <a href="{{ route('admin.vehicles.create') }}?user_id={{ $driver->id }}"
               class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 transition-colors inline-flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-[10px]"></i> Add Vehicle
            </a>
        </div>

        @if ($driver->vehicles->isEmpty())
            <div class="p-8 text-center rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-500">
                <i class="fa-solid fa-car-side text-2xl text-slate-300 mb-2 block"></i>
                This driver has not registered any vehicles yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Vehicle Number</th>
                            <th class="px-4 py-3">Model & Type</th>
                            <th class="px-4 py-3">Capacity</th>
                            <th class="px-4 py-3">Approval Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($driver->vehicles as $veh)
                            @php
                                $st = strtolower($veh->status);
                                $isApproved = in_array($st, ['approved', 'active']);
                                $isPending  = $st === 'pending';
                                $isRejected = $st === 'rejected';
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-3.5 font-mono font-extrabold text-slate-900">
                                    {{ $veh->registration_no }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-700 font-medium">
                                    {{ $veh->model }} <span class="text-slate-400">({{ $veh->type }})</span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 font-bold">
                                    {{ $veh->total_seats }} Seats
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($isApproved)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Approved
                                        </span>
                                    @elseif ($isPending)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold border border-amber-200">
                                            <i class="fa-solid fa-hourglass-half text-amber-600 animate-pulse"></i> Pending Review
                                        </span>
                                    @elseif ($isRejected)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold border border-rose-200" title="{{ $veh->rejection_reason }}">
                                            <i class="fa-solid fa-circle-xmark text-rose-600"></i> Rejected
                                        </span>
                                        @if ($veh->rejection_reason)
                                            <div class="text-[10px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $veh->rejection_reason }}">
                                                Reason: {{ $veh->rejection_reason }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold border border-slate-200">
                                            {{ ucfirst($veh->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.vehicles.show', $veh->id) }}"
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-[11px] transition-colors">
                                            <i class="fa-solid fa-eye text-[10px]"></i> View
                                        </a>
                                        @if (!$isApproved)
                                            <form action="{{ route('admin.vehicles.approve', $veh->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition-colors inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-check text-[10px]"></i> Approve
                                                </button>
                                            </form>
                                        @endif
                                        @if (!$isRejected)
                                            <form action="{{ route('admin.vehicles.reject', $veh->id) }}" method="POST" class="inline"
                                                  onsubmit="const r = prompt('Optional rejection reason for vehicle {{ $veh->registration_no }}:'); if (r === null) return false; this.rejection_reason.value = r;">
                                                @csrf
                                                <input type="hidden" name="rejection_reason" value="">
                                                <button type="submit"
                                                        class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200 transition-colors inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-xmark text-[10px]"></i> Reject
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ─────────────────── APPROVAL ACTION CARD ─────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Driver Application Decision</h4>

        @if ($driver->driver_status === 'approved')
            {{-- Already Approved --}}
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-2xl"></i>
                <div>
                    <div class="font-extrabold text-emerald-800 text-sm">This driver is currently approved.</div>
                    <p class="text-xs text-emerald-700 mt-0.5">They have verified operational access to register vehicles, view trips, and schedule public routes.</p>
                </div>
            </div>

            {{-- Revoke Access Section with Optional Reason --}}
            <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="space-y-3 p-4 rounded-xl bg-rose-50/50 border border-rose-200">
                @csrf
                <label for="revoke_reason" class="block text-xs font-bold text-rose-900">
                    Revoke Driver Access (Reject Application)
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text"
                           id="revoke_reason"
                           name="rejection_reason"
                           placeholder="Optional reason for revocation / rejection..."
                           class="flex-1 px-3.5 py-2.5 rounded-xl border border-rose-200 bg-white text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <button type="submit"
                            onclick="return confirm('Revoke this driver\'s access? Their driver status will become rejected.')"
                            class="shrink-0 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-all shadow-sm inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-ban"></i> Revoke & Reject Driver
                    </button>
                </div>
            </form>

        @elseif ($driver->driver_status === 'rejected')
            {{-- Already Rejected --}}
            <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl mb-4">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-2xl"></i>
                <div>
                    <div class="font-extrabold text-rose-800 text-sm">This driver application was rejected.</div>
                    <p class="text-xs text-rose-700 mt-0.5">They cannot schedule trips or access approved driver features.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all shadow-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i> Re-Approve Driver Application
                    </button>
                </form>
            </div>

        @else
            {{-- Pending Review --}}
            <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-5">
                <i class="fa-solid fa-clock text-amber-600 text-2xl"></i>
                <div>
                    <div class="font-extrabold text-amber-800 text-sm">Application Pending Administrator Review</div>
                    <p class="text-xs text-amber-700 mt-0.5">Please review the driver's CNIC, driving license, and credentials before making an approval decision.</p>
                </div>
            </div>

            <div class="space-y-4">
                {{-- Reject Form with Optional Reason --}}
                <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                    @csrf
                    <label for="rejection_reason" class="block text-xs font-bold text-slate-700">
                        Optional Rejection Reason:
                    </label>
                    <textarea id="rejection_reason"
                              name="rejection_reason"
                              rows="2"
                              placeholder="e.g. Invalid license document, CNIC mismatch, or missing commercial endorsement..."
                              class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                        <button type="submit"
                                onclick="return confirm('Reject this driver application?')"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-all border border-rose-200 inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-xmark"></i> Reject Application
                        </button>

                        <button type="submit"
                                formaction="{{ route('admin.drivers.approve', $driver->id) }}"
                                class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition-all shadow-sm shadow-emerald-600/20 inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i> Approve Driver Application
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

</div>

@endsection
