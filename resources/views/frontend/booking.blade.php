@extends('frontend.layouts.app')

@section('title', 'My Bookings — RouteConnect')

@section('content')

<section class="py-12 bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 bg-emerald-950 px-3 py-1 rounded-full border border-emerald-800">Passenger Area</span>
        <h1 class="text-3xl font-extrabold text-white mt-3">My Bookings & Seat Passes</h1>
        <p class="text-slate-300 text-sm mt-1">View your reserved scheduled transport seats and digital booking passes.</p>
    </div>
</section>

<section class="py-12 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xl mx-auto mb-3">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h3 class="font-extrabold text-slate-800 text-lg">Passenger Booking Area</h3>
            <p class="text-xs text-slate-500 mt-1">Select available seats and manage your transport booking history.</p>
        </div>
    </div>
</section>

@endsection
