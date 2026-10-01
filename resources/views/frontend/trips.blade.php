@extends('frontend.layouts.app')

@section('title', 'Search Trips — RouteConnect')

@section('content')

<section class="py-12 bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 bg-emerald-950 px-3 py-1 rounded-full border border-emerald-800">Trip Search</span>
        <h1 class="text-3xl font-extrabold text-white mt-3">Scheduled Trips Search</h1>
        <p class="text-slate-300 text-sm mt-1">Search scheduled vehicles passing through your start and destination stops.</p>
    </div>
</section>

<section class="py-12 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Search Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <form action="{{ url('/trips') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">From Stop</label>
                    <select name="from" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold">
                        <option value="Nagar" {{ request('from') == 'Nagar' ? 'selected' : '' }}>Nagar</option>
                        <option value="Gilgit" {{ request('from') == 'Gilgit' ? 'selected' : '' }}>Gilgit</option>
                        <option value="Danyor" {{ request('from') == 'Danyor' ? 'selected' : '' }}>Danyor</option>
                        <option value="Nomal" {{ request('from') == 'Nomal' ? 'selected' : '' }}>Nomal</option>
                        <option value="Aliabad" {{ request('from') == 'Aliabad' ? 'selected' : '' }}>Aliabad</option>
                        <option value="Hunza" {{ request('from') == 'Hunza' ? 'selected' : '' }}>Hunza</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">To Stop</label>
                    <select name="to" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold">
                        <option value="Hunza" {{ request('to') == 'Hunza' ? 'selected' : '' }}>Hunza</option>
                        <option value="Aliabad" {{ request('to') == 'Aliabad' ? 'selected' : '' }}>Aliabad</option>
                        <option value="Nagar" {{ request('to') == 'Nagar' ? 'selected' : '' }}>Nagar</option>
                        <option value="Nomal" {{ request('to') == 'Nomal' ? 'selected' : '' }}>Nomal</option>
                        <option value="Danyor" {{ request('to') == 'Danyor' ? 'selected' : '' }}>Danyor</option>
                        <option value="Gilgit" {{ request('to') == 'Gilgit' ? 'selected' : '' }}>Gilgit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Date</label>
                    <input type="date" name="date" value="{{ request('date', '2026-10-01') }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl text-xs">
                        Search Trips
                    </button>
                </div>
            </form>
        </div>

        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm text-center">
            <h3 class="font-extrabold text-slate-800 text-base">Trip Search Results</h3>
            <p class="text-xs text-slate-500 mt-1">Showing matching scheduled trips for your journey query.</p>
        </div>

    </div>
</section>

@endsection
