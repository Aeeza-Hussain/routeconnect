@extends('frontend.layouts.app')

@section('title', 'Privacy Policy — RouteConnect')

@section('content')
<div class="py-12 bg-slate-50 dark:bg-slate-950 transition-colors min-h-[70vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 text-xs font-extrabold uppercase tracking-wider mb-4 border border-indigo-200 dark:border-indigo-800">
                <i class="fa-solid fa-shield-halved"></i> Data Protection
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-2">Privacy & Document Protection Policy</h1>
            <p class="text-xs text-slate-400 mb-8">Last Updated: October 2026</p>

            <div class="space-y-6 text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">1. Identity Document Confidentiality</h2>
                    <p>All sensitive driver credentials—including CNIC front/back scans and Driving Licence front/back scans—are stored strictly on encrypted, private local server storage and never exposed to the public internet or search engines.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">2. Restricted Administrative Access</h2>
                    <p>Access to original identification documents is strictly restricted to verified RouteConnect system administrators for the purpose of credential validation. Unauthorized downloads, third-party distribution, or public display of identity documents is strictly prohibited.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">3. Emergency Contact Data</h2>
                    <p>Emergency contact information provided during driver registration is retained solely for safety incidents, roadside breakdowns, or critical passenger travel updates, and is never sold or shared with commercial marketers.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">4. Verification Consent</h2>
                    <p>By registering as a driver, you consent to RouteConnect verifying provided information against national database standards to safeguard passengers and transport integrity.</p>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <a href="{{ route('register', ['role' => 'driver']) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Return to Registration
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
