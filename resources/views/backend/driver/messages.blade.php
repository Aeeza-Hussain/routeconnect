@extends('backend.driver.layouts.app')

@section('title', 'Trip Messages — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-teal-500/20">
            <i class="fa-solid fa-comments"></i> Communications
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Passenger Trip Messages</h2>
        <p class="text-sm text-slate-400 mt-1">
            Broadcast real-time departure, delay, and transit progress announcements to booked passengers.
        </p>
    </div>
    <a href="{{ route('driver.dashboard') }}"
       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-800 text-emerald-300 text-sm rounded-2xl font-semibold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-950/40 border border-rose-800 text-rose-300 text-xs font-semibold space-y-1">
        <div class="font-bold flex items-center gap-2 mb-1 text-sm text-rose-400">
            <i class="fa-solid fa-triangle-exclamation"></i> Error posting message:
        </div>
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Post New Message Card (Left 1 Col) --}}
    <div class="lg:col-span-1">
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6 sticky top-24">
            <h3 class="text-base font-extrabold text-white mb-2 flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-teal-400"></i> Broadcast New Message
            </h3>
            <p class="text-xs text-slate-400 mb-5">
                Send an alert for one of your trips. Passengers will receive this in real-time.
            </p>

            @if ($driverTrips->isEmpty())
                <div class="p-6 rounded-xl bg-slate-900 border border-slate-800 text-center">
                    <i class="fa-solid fa-route text-slate-600 text-2xl mb-2 block"></i>
                    <p class="text-xs text-slate-300 font-bold">No Active Trips Assigned</p>
                    <p class="text-[11px] text-slate-500 mt-1">You must have trips scheduled to broadcast messages.</p>
                </div>
            @else
                <form action="{{ route('driver.messages.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Select Trip --}}
                    <div>
                        <label for="trip_id" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            Select Your Trip <span class="text-rose-400">*</span>
                        </label>
                        <select name="trip_id" id="trip_id" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                            @foreach ($driverTrips as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->route->origin ?? 'Origin' }} → {{ $t->route->destination ?? 'Destination' }} ({{ $t->trip_date ? \Carbon\Carbon::parse($t->trip_date)->format('d M') : '' }} · {{ $t->departure_time }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Message Text --}}
                    <div>
                        <label for="message" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            Message Content <span class="text-rose-400">*</span>
                        </label>
                        <textarea name="message"
                                  id="message"
                                  rows="4"
                                  required
                                  maxlength="1000"
                                  placeholder="e.g. Leaving Gilgit at 8:00 AM..."
                                  class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500"></textarea>
                    </div>

                    {{-- Preset Buttons --}}
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Quick Message Presets</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button"
                                    onclick="setPreset('Leaving Gilgit at 8:00 AM.')"
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white text-[11px] font-medium border border-slate-800 transition-colors">
                                "Leaving Gilgit at 8:00 AM."
                            </button>
                            <button type="button"
                                    onclick="setPreset('Reached Nagar stop.')"
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white text-[11px] font-medium border border-slate-800 transition-colors">
                                "Reached Nagar stop."
                            </button>
                            <button type="button"
                                    onclick="setPreset('Trip delayed by 15 minutes.')"
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white text-[11px] font-medium border border-slate-800 transition-colors">
                                "Trip delayed by 15 minutes."
                            </button>
                            <button type="button"
                                    onclick="setPreset('All passengers boarded. Departing now.')"
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white text-[11px] font-medium border border-slate-800 transition-colors">
                                "All passengers boarded."
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-all shadow-lg shadow-teal-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Broadcast Message</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Broadcast Message History (Right 2 Cols) --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-slate-950/60 rounded-2xl border border-slate-800 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-800">
                <div>
                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-teal-400"></i> Broadcast History
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Chronological record of status updates posted for your trips.</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-mono text-teal-400 font-bold">
                    {{ $messages->count() }} {{ \Illuminate\Support\Str::plural('Message', $messages->count()) }}
                </span>
            </div>

            @if ($messages->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-900 text-slate-500 flex items-center justify-center text-2xl mx-auto mb-3 border border-slate-800">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h4 class="text-sm font-bold text-white mb-1">No Messages Broadcasted Yet</h4>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">
                        Use the form on the left to broadcast your first departure, delay, or arrival update to passengers.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($messages as $msg)
                        <div class="p-5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition-all flex flex-col justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full bg-teal-500/10 text-teal-400 text-[10px] font-extrabold border border-teal-500/20">
                                            Trip #{{ $msg->trip_id }}
                                        </span>
                                        <span class="text-xs font-bold text-white">
                                            {{ $msg->trip->route->origin ?? 'Origin' }} → {{ $msg->trip->route->destination ?? 'Destination' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $msg->created_at->format('d M Y, h:i A') }} ({{ $msg->created_at->diffForHumans() }})
                                    </span>
                                </div>

                                <p class="text-sm text-slate-200 leading-relaxed font-medium bg-slate-950/50 p-3 rounded-lg border border-slate-800/80">
                                    {{ $msg->message }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-800/80 text-xs">
                                <a href="{{ route('driver.trips.show', $msg->trip_id) }}" class="text-teal-400 hover:text-teal-300 font-bold text-[11px] flex items-center gap-1">
                                    <span>View Associated Trip</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>

                                <form action="{{ route('driver.messages.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Delete this broadcast message?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold text-[11px] flex items-center gap-1">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<script>
    function setPreset(text) {
        const textarea = document.getElementById('message');
        if (textarea) {
            textarea.value = text;
            textarea.focus();
        }
    }
</script>

@endsection
