@extends('frontend.layouts.app')

@section('title', 'About Us — RouteConnect')

@section('content')
<div class="min-h-screen bg-[#F5F8FF] dark:bg-[#070e1b] transition-colors duration-200 py-16 lg:py-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">

        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#075BEA]/10 border border-[#075BEA]/20 text-[#075BEA] dark:text-blue-400 text-xs font-black uppercase tracking-wider">
                <i class="fa-solid fa-compass"></i> Our Mission & Vision
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-[#102347] dark:text-white">
                About <span class="text-[#075BEA] dark:text-blue-400">RouteConnect</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                Pioneering smart, dependable, and community-driven transportation across Gilgit-Baltistan and regional transit corridors.
            </p>
        </div>

        <!-- Content Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-[#075BEA] dark:text-blue-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h2 class="text-lg font-extrabold text-[#102347] dark:text-white">Regional Connectivity</h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    We bridge rugged inter-city corridors connecting Gilgit, Hunza, Skardu, Nagar, Ghizer, and Islamabad with verified vehicles and predictable stop schedules.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-[#10B981] flex items-center justify-center text-xl">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <h2 class="text-lg font-extrabold text-[#102347] dark:text-white">Safety & Verification</h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Every driver must undergo rigorous verification of CNIC and driving credentials, while all vehicles undergo roadworthiness inspections prior to publishing trips.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h2 class="text-lg font-extrabold text-[#102347] dark:text-white">Transparent & Flexible</h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    From single seat reservations along specific highway segments to full-day private charters and custom trip bidding, RouteConnect offers transparent fares with zero hidden fees.
                </p>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="rounded-3xl bg-[#102347] text-white p-8 sm:p-12 text-center space-y-6">
            <h2 class="text-2xl sm:text-3xl font-extrabold">Ready to explore with RouteConnect?</h2>
            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Check our scheduled departures or reach out to our team with your questions.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                <a href="{{ route('services') }}" class="px-7 py-3.5 rounded-full bg-[#075BEA] hover:bg-blue-600 text-white font-extrabold text-xs shadow-md transition-all">
                    View Our Services
                </a>
                <a href="{{ route('contact') }}" class="px-7 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs border border-white/20 transition-all">
                    Contact Us
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

