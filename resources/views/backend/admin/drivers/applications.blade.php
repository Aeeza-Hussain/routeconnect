@extends('backend.admin.layouts.app')

@section('title', 'Pending Driver Applications — RouteConnect Admin')

@section('content')

<!-- Header Title -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-clock"></i> Approval Queue
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Pending Driver Applications</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Review registrations from users who applied to become drivers on RouteConnect.</p>
    </div>
</div>

<!-- Flash Message -->
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-2xl font-bold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Table Container -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    
    @if ($applications->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-xl mx-auto">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700">No pending driver applications.</div>
            <p class="text-xs text-slate-500">There are currently no pending driver applications awaiting review.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="p-4">Driver Name</th>
                        <th class="p-4">Email Address</th>
                        <th class="p-4">Phone Number</th>
                        <th class="p-4">Registration Date</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold">
                    @foreach ($applications as $driver)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-4 font-extrabold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($driver->name, 0, 2)) }}
                                    </div>
                                    <span>{{ $driver->name }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-slate-600">{{ $driver->email }}</td>
                            <td class="p-4 text-slate-600">{{ $driver->phone ?? 'N/A' }}</td>
                            <td class="p-4 text-slate-500">{{ $driver->created_at->format('d M Y, h:i A') }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold border border-amber-200">
                                    Pending Approval
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('admin.drivers.show', $driver->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition-colors inline-block">
                                    Details
                                </a>
                                
                                <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors">
                                        Approve
                                    </button>
                                </form>

                                <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-colors">
                                        Reject
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
