@extends('backend.driver.layouts.app')

@section('title', 'Passenger Travel Requests — RouteConnect Driver')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-extrabold uppercase tracking-wider mb-2 border border-amber-500/20">
            <i class="fa-solid fa-hand-holding-hand"></i> Passenger Travel Marketplace
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Passenger Requests</h2>
        <p class="text-sm text-slate-400 mt-1">
            Browse open ride requests posted by passengers who need custom travel routes or dates. Submit offers with your approved fleet vehicles.
        </p>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        <a href="{{ route('activity.index', ['tab' => 'my-applications']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-list-check text-teal-400"></i> My Submitted Offers
        </a>
        <a href="{{ route('driver.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors border border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
        </a>
    </div>
</div>

{{-- ─── Filters Bar ─── --}}
<div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-5 mb-6 shadow-xl">
    <form method="GET" action="{{ route('driver.passenger-requests') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        
        {{-- Search Route --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Route / Location</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                <input type="text" name="route" value="{{ request('route') }}" placeholder="e.g. Gilgit, Hunza..."
                       class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-8 pr-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
            </div>
        </div>

        {{-- Filter Date --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Travel Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
        </div>

        {{-- Vehicle Type --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Vehicle Preference</label>
            <select name="vehicle_type" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
                <option value="all">All Vehicle Types</option>
                <option value="Car" {{ request('vehicle_type') === 'Car' ? 'selected' : '' }}>Car / Sedan</option>
                <option value="Van" {{ request('vehicle_type') === 'Van' ? 'selected' : '' }}>Van / HiAce</option>
                <option value="Daba" {{ request('vehicle_type') === 'Daba' ? 'selected' : '' }}>Daba / Carry</option>
                <option value="Bus" {{ request('vehicle_type') === 'Bus' ? 'selected' : '' }}>Coaster / Bus</option>
                <option value="Other" {{ request('vehicle_type') === 'Other' ? 'selected' : '' }}>Other / 4x4 Jeep</option>
            </select>
        </div>

        {{-- Seats Needed --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Max Seats Needed</label>
            <input type="number" name="seats" min="1" max="50" value="{{ request('seats') }}" placeholder="e.g. 4"
                   class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
        </div>

        {{-- Actions --}}
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-filter"></i>
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['route', 'date', 'vehicle_type', 'seats']))
                <a href="{{ route('driver.passenger-requests') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ─── Requests List ─── --}}
<div class="space-y-4">
    @forelse($requests as $req)
        @php
            $hasApplied = isset($myApplications[$req->id]);
            $myApp = $hasApplied ? $myApplications[$req->id] : null;
        @endphp
        <div class="bg-slate-950/70 rounded-3xl border border-slate-800 p-6 shadow-xl space-y-4 hover:border-slate-700 transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Open Request
                        </span>
                        @if($req->preferred_vehicle_type)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                <i class="fa-solid fa-car text-[9px] mr-1"></i>{{ $req->preferred_vehicle_type }}
                            </span>
                        @endif
                        <span class="text-xs text-slate-400">
                            Posted by <strong class="text-slate-300">{{ $req->user?->name ?? 'Passenger' }}</strong> · {{ $req->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <h3 class="text-xl font-black text-white flex items-center gap-2.5">
                        <span>{{ $req->pickup_location }}</span>
                        <i class="fa-solid fa-arrow-right text-xs text-teal-400"></i>
                        <span>{{ $req->destination_location }}</span>
                    </h3>
                </div>

                {{-- Action / Application status --}}
                <div class="flex items-center gap-2 shrink-0">
                    @if($hasApplied)
                        <div class="text-right">
                            <span class="px-3 py-1.5 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 font-extrabold text-xs inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>Applied: Rs. {{ number_format($myApp->offered_fare, 2) }} ({{ ucfirst($myApp->status) }})</span>
                            </span>
                        </div>
                    @else
                        <button type="button"
                                onclick="openDriverApplyModal({{ $req->id }}, '{{ addslashes($req->pickup_location) }}', '{{ addslashes($req->destination_location) }}', '{{ $req->budget ?? 0 }}', '{{ $req->seats_needed }}')"
                                class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs flex items-center gap-2 shadow-lg shadow-teal-500/20 transition-transform active:scale-95">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Offer Ride / Apply</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Detail Metrics Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Travel Date</span>
                    <span class="font-bold text-white">{{ $req->travel_date->format('d M Y') }} ({{ $req->travel_time ?: 'Flexible' }})</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Seats Needed</span>
                    <span class="font-bold text-white">{{ $req->seats_needed }} Passengers</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Passenger Budget</span>
                    <span class="font-mono font-bold text-emerald-400">{{ $req->budget ? 'Rs. ' . number_format($req->budget, 2) : 'Negotiable' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Current Bids</span>
                    <span class="font-bold text-teal-400">{{ $req->applications()->count() }} Driver(s) Applied</span>
                </div>
            </div>

            {{-- Intermediate Stops --}}
            @if(!empty($req->intermediate_stops) && count($req->intermediate_stops) > 0)
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="text-[10px] text-slate-500 uppercase font-bold">Via Stops:</span>
                    @foreach($req->intermediate_stops as $stop)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-900 text-slate-300 border border-slate-800">
                            {{ $stop }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Notes --}}
            @if($req->notes)
                <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80 text-xs text-slate-400 italic">
                    <i class="fa-solid fa-comment-dots text-slate-500 mr-1.5"></i> "{{ $req->notes }}"
                </div>
            @endif
        </div>
    @empty
        <div class="bg-slate-950/60 rounded-3xl border border-slate-800 p-12 text-center space-y-3">
            <i class="fa-solid fa-users text-4xl text-slate-600"></i>
            <h4 class="text-lg font-extrabold text-white">No open passenger requests match your filters</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Check back shortly or reset your filters to view all open passenger ride requests.
            </p>
            @if(request()->hasAny(['route', 'date', 'vehicle_type', 'seats']))
                <a href="{{ route('driver.passenger-requests') }}" class="inline-block px-4 py-2 rounded-xl bg-teal-500 text-slate-950 font-bold text-xs mt-2">
                    Clear Filters
                </a>
            @endif
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($requests->hasPages())
        <div class="pt-4">
            {{ $requests->links() }}
        </div>
    @endif
</div>

{{-- ─── Driver Apply Modal ─── --}}
<div id="driverApplyModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-slate-900 rounded-3xl border border-slate-800 max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 text-white">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-lg font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-teal-400"></i>
                <span>Submit Ride Offer to Passenger</span>
            </h3>
            <button type="button" onclick="closeDriverApplyModal()" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 text-xs space-y-1">
            <p class="font-bold text-teal-400" id="driverModalRouteText"></p>
            <p class="text-slate-400 text-[11px]" id="driverModalHintText"></p>
        </div>

        <form method="POST" id="driverApplyForm" action="" class="space-y-4 text-xs">
            @csrf

            {{-- Offered Total Fare --}}
            <div>
                <label class="block font-bold text-slate-300 mb-1">Your Proposed Total Fare (PKR) *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-500 font-bold">Rs.</span>
                    <input type="number" name="offered_fare" id="driverOfferedFare" required min="50" placeholder="e.g. 2500"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-teal-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Proposed Time --}}
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Proposed Departure Time</label>
                    <input type="text" name="proposed_time" placeholder="e.g. 09:30 AM"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-teal-500">
                </div>

                {{-- Available Seats --}}
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Available Seats (Optional)</label>
                    <input type="number" name="available_seats" id="driverAvailableSeats" min="1" max="50" placeholder="e.g. 4"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-teal-500">
                </div>
            </div>

            {{-- Select Approved Vehicle --}}
            <div>
                <label class="block font-bold text-slate-300 mb-1">Assign Approved Vehicle</label>
                <select name="vehicle_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-teal-500">
                    <option value="">Select a Vehicle (Optional)</option>
                    @foreach($driverVehicles as $veh)
                        <option value="{{ $veh->id }}">{{ $veh->model }} ({{ $veh->registration_no }}) · {{ $veh->type }} ({{ $veh->total_seats }} seats)</option>
                    @endforeach
                </select>
                @if($driverVehicles->isEmpty())
                    <p class="text-[10px] text-amber-400 mt-1">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> No approved vehicles registered. You can still apply or <a href="{{ route('driver.vehicles.create') }}" class="underline">register a vehicle</a>.
                    </p>
                @endif
            </div>

            {{-- Message to Passenger --}}
            <div>
                <label class="block font-bold text-slate-300 mb-1">Message to Passenger</label>
                <textarea name="message" rows="3" placeholder="Hello! I can take your trip on time with a comfortable vehicle and experienced driving..."
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeDriverApplyModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black shadow-lg shadow-teal-500/20">
                    Submit Offer & Open Chat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDriverApplyModal(requestId, from, to, budget, seats) {
    const modal = document.getElementById('driverApplyModal');
    const form = document.getElementById('driverApplyForm');
    const routeText = document.getElementById('driverModalRouteText');
    const hintText = document.getElementById('driverModalHintText');
    const fareInput = document.getElementById('driverOfferedFare');
    const seatsInput = document.getElementById('driverAvailableSeats');

    if (modal && form) {
        form.action = `/driver/passenger-requests/${requestId}/apply`;
        if (routeText) routeText.innerText = `Route: ${from} → ${to}`;
        if (hintText) hintText.innerText = `Passenger requested ${seats} seats · Estimated budget: ${budget > 0 ? 'Rs. ' + budget : 'Negotiable'}`;
        if (fareInput && budget > 0) fareInput.value = budget;
        if (seatsInput && seats > 0) seatsInput.value = seats;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeDriverApplyModal() {
    const modal = document.getElementById('driverApplyModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>

@endsection
