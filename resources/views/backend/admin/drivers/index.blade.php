@extends('backend.admin.layouts.app')

@section('title', 'Approved Drivers List — RouteConnect Admin')

@section('content')

<!-- Header Title -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-user-check"></i> Driver Registry
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Approved Drivers</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Verified drivers who have access to the RouteConnect Driver Console.</p>
    </div>
</div>

<!-- Table Container -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    
    @if ($drivers->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-xl mx-auto">
                <i class="fa-solid fa-users-slash"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700">No records found.</div>
            <p class="text-xs text-slate-500">There are currently no approved drivers in the system.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="p-4">Driver Name</th>
                        <th class="p-4">Email Address</th>
                        <th class="p-4">Phone Number</th>
                        <th class="p-4">Approval Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold">
                    @foreach ($drivers as $driver)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-4 font-extrabold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($driver->name, 0, 2)) }}
                                    </div>
                                    <span>{{ $driver->name }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-slate-600">{{ $driver->email }}</td>
                            <td class="p-4 text-slate-600">{{ $driver->phone ?? 'N/A' }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                                    Approved
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Suspend/Reject driver access?')" class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold transition-colors">
                                        Suspend Access
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

@endsection
