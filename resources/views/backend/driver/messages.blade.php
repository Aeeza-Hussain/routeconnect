@extends('backend.driver.layouts.app')

@section('title', 'Passenger Messages — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-comments"></i> Communications
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Passenger Trip Messages</h2>
        <p class="text-sm text-slate-400 mt-1">Direct communication with passengers regarding pickup and schedule updates.</p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

<div class="max-w-3xl space-y-6">

    <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-12 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
            <i class="fa-solid fa-comments"></i>
        </div>
        <h3 class="text-base font-extrabold text-white mb-1">No Active Messages</h3>
        <p class="text-xs text-slate-400 max-w-sm mx-auto">
            Messages from passengers booked on your active trips will appear here.
        </p>
    </div>

</div>

@endsection
