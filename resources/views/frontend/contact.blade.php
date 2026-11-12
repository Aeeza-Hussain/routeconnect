@extends('frontend.layouts.app')

@section('title', 'Contact Us — RouteConnect')

@section('content')
<div class="min-h-screen bg-[#F5F8FF] dark:bg-[#070e1b] transition-colors duration-200 py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- ================================================================= -->
        <!-- A. HERO SECTION                                                   -->
        <!-- ================================================================= -->
        <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16 space-y-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#075BEA]/10 border border-[#075BEA]/20 text-[#075BEA] dark:text-blue-400 text-xs font-black uppercase tracking-wider">
                <i class="fa-solid fa-headset"></i> We're Here For You
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-[#102347] dark:text-white">
                Get in Touch
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                Have a question or need assistance? We are here to help.
            </p>
        </div>

        <!-- ================================================================= -->
        <!-- SUCCESS & ERROR ALERTS                                            -->
        <!-- ================================================================= -->
        @if(session('success'))
            <div class="max-w-4xl mx-auto mb-8 p-4 sm:p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-start gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-full bg-[#10B981] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                    <i class="fa-solid fa-check text-sm"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-sm">Message Sent Successfully!</h3>
                    <p class="text-xs mt-1 text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-4xl mx-auto mb-8 p-4 sm:p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 flex items-start gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-sm">Please correct the errors below:</h3>
                    <ul class="text-xs mt-1.5 list-disc list-inside space-y-1 text-rose-800 dark:text-rose-300">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- ================================================================= -->
        <!-- MAIN GRID: B. CONTACT INFO + C. CONTACT FORM                      -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start max-w-6xl mx-auto">

            <!-- B. CONTACT INFORMATION (4 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-7 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-[#102347] dark:text-white mb-2">Contact Details</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            Reach out to our dedicated support team regarding inquiries, trip queries, or feedback.
                        </p>
                    </div>

                    <div class="space-y-5 pt-2">
                        <!-- Email Card -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F5F8FF] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                            <div class="w-11 h-11 rounded-xl bg-[#075BEA]/10 text-[#075BEA] dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">Email Us</span>
                                <a href="mailto:info@routeconnect.com" class="text-sm font-bold text-[#102347] dark:text-white hover:text-[#075BEA] transition-colors break-all">
                                    info@routeconnect.com
                                </a>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Online inquiries and customer support</span>
                            </div>
                        </div>

                        <!-- Location Card -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F5F8FF] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-[#10B981] flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">Operational Area</span>
                                <span class="text-sm font-bold text-[#102347] dark:text-white block">
                                    Gilgit-Baltistan, Pakistan
                                </span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Connecting regional inter-city corridors</span>
                            </div>
                        </div>

                        <!-- Response Time Commitment -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F5F8FF] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                            <div class="w-11 h-11 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">Response Time</span>
                                <span class="text-sm font-bold text-[#102347] dark:text-white block">
                                    Within 24 Hours
                                </span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Monday through Sunday assistance</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Small FAQ Help Hint -->
                <div class="p-6 rounded-3xl bg-gradient-to-br from-[#102347] to-[#075BEA] text-white shadow-md space-y-2">
                    <h3 class="font-extrabold text-sm">Need immediate ride assistance?</h3>
                    <p class="text-xs text-blue-100 leading-relaxed">
                        You can directly search upcoming schedules or charter a vehicle on our live marketplace.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white underline underline-offset-4 hover:text-[#10B981] transition-colors">
                            <span>Browse Trips Now</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- C. CONTACT FORM (7 Cols) -->
            <div class="lg:col-span-7">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-7 sm:p-10 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#102347] dark:text-white">Send Us a Message</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed font-medium">
                            Fill out the form below and our coordination team will review your inquiry.
                        </p>
                    </div>

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-regular fa-user text-xs"></i>
                                </span>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       required
                                       maxlength="100"
                                       value="{{ old('name', $prefillName) }}"
                                       placeholder="e.g. Aleeza Fatima"
                                       class="w-full pl-10 pr-4 py-3 rounded-2xl border {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50' }} text-slate-900 dark:text-white text-xs font-bold focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-[#075BEA] transition-all">
                            </div>
                            @error('name')
                                <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-regular fa-envelope text-xs"></i>
                                </span>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       required
                                       maxlength="150"
                                       value="{{ old('email', $prefillEmail) }}"
                                       placeholder="name@example.com"
                                       class="w-full pl-10 pr-4 py-3 rounded-2xl border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50' }} text-slate-900 dark:text-white text-xs font-bold focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-[#075BEA] transition-all">
                            </div>
                            @error('email')
                                <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Subject <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-tag text-xs"></i>
                                </span>
                                <input type="text"
                                       name="subject"
                                       id="subject"
                                       required
                                       maxlength="200"
                                       value="{{ old('subject') }}"
                                       placeholder="e.g. Inquiry regarding Gilgit-Hunza scheduled trips"
                                       class="w-full pl-10 pr-4 py-3 rounded-2xl border {{ $errors->has('subject') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50' }} text-slate-900 dark:text-white text-xs font-bold focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-[#075BEA] transition-all">
                            </div>
                            @error('subject')
                                <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message Body -->
                        <div>
                            <label for="message" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Message <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="message"
                                      id="message"
                                      rows="5"
                                      required
                                      minlength="5"
                                      maxlength="3000"
                                      placeholder="Write your message or inquiry here..."
                                      class="w-full p-4 rounded-2xl border {{ $errors->has('message') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50' }} text-slate-900 dark:text-white text-xs font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-[#075BEA] transition-all leading-relaxed">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full sm:w-auto px-8 py-4 rounded-full bg-[#075BEA] hover:bg-blue-600 text-white font-extrabold text-xs shadow-lg shadow-[#075BEA]/25 flex items-center justify-center gap-2.5 transition-all hover:-translate-y-0.5">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Submit Message</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
