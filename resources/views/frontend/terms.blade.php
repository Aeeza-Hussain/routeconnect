@extends('frontend.layouts.app')

@section('title', 'Terms & Conditions — RouteConnect')

@section('content')
<div class="py-12 bg-slate-50 dark:bg-slate-950 transition-colors min-h-[70vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 text-xs font-extrabold uppercase tracking-wider mb-4 border border-emerald-200 dark:border-emerald-800">
                <i class="fa-solid fa-file-contract"></i> Legal Agreement
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-2">Terms & Conditions</h1>
            <p class="text-xs text-slate-400 mb-8">Last Updated: October 2026</p>

            <div class="space-y-6 text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">1. Driver Eligibility & Verification</h2>
                    <p>All drivers registering on RouteConnect must hold a valid, unexpired commercial or national driving licence and valid National Identity Card (CNIC). Registration does not automatically grant driving privileges. All applications undergo comprehensive administrator verification and background clearance.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">2. Accuracy of Submitted Documents</h2>
                    <p>Drivers must provide authentic, unmodified identity and licence documents. Falsification, spoofing, or submitting fraudulent documents will result in immediate permanent disqualification and reporting to law enforcement authorities.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">3. Fleet & Vehicle Registration</h2>
                    <p>Approval as a driver is distinct from vehicle approval. Each vehicle operated on RouteConnect must undergo independent inspection and administrator approval prior to publishing any public passenger trips.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">4. Passenger Safety & Conduct</h2>
                    <p>Drivers must adhere to highway safety codes, speed limits, vehicle capacity constraints, and scheduled intermediate pickup points. RouteConnect reserves the right to suspend or revoke driver approval at any time for safety violations.</p>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <a href="{{ route('register', ['role' => 'driver']) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Return to Registration
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
