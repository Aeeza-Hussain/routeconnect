@extends('backend.admin.layouts.app')

@section('title', 'Message Details — RouteConnect Admin')

@section('content')

{{-- ─── Header & Back Navigation ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="{{ route('admin.contact-messages.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 mb-2 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Messages
        </a>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Message Details</h2>
    </div>

    {{-- Actions Bar --}}
    <div class="flex items-center gap-2.5">
        
        {{-- Reply via Email --}}
        <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . $message->subject) }}"
           class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-sm transition-colors inline-flex items-center gap-1.5">
            <i class="fa-solid fa-reply"></i> Reply via Email
        </a>

        {{-- Toggle Read/Unread --}}
        <form action="{{ route('admin.contact-messages.toggle-read', $message->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs transition-colors inline-flex items-center gap-1.5">
                @if($message->is_read)
                    <i class="fa-regular fa-envelope"></i> Mark as Unread
                @else
                    <i class="fa-regular fa-envelope-open"></i> Mark as Read
                @endif
            </button>
        </form>

        {{-- Delete Message --}}
        <form action="{{ route('admin.contact-messages.destroy', $message->id) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to delete this message permanently?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                <i class="fa-regular fa-trash-can"></i> Delete
            </button>
        </form>

    </div>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ─── Message Detail Card ─── --}}
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    
    {{-- Card Header Metadata --}}
    <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#102347] to-[#075BEA] text-white font-extrabold text-base flex items-center justify-center shadow-md shrink-0">
                {{ strtoupper(substr($message->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">{{ $message->name }}</h3>
                    @if($message->user)
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold">
                            User #{{ $message->user_id }}
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                    <a href="mailto:{{ $message->email }}" class="text-blue-600 hover:underline font-bold">
                        {{ $message->email }}
                    </a>
                    <span>•</span>
                    <span>Received on {{ $message->created_at->format('F d, Y \a\t h:i A') }} ({{ $message->created_at->diffForHumans() }})</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($message->is_read)
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-emerald-600"></i> Read
                </span>
            @else
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold flex items-center gap-1.5 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Unread
                </span>
            @endif
        </div>
    </div>

    {{-- Subject & Message Body --}}
    <div class="p-6 sm:p-8 space-y-6">
        <div>
            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider block mb-1">Subject</span>
            <h4 class="text-xl font-extrabold text-slate-900 leading-snug">{{ $message->subject }}</h4>
        </div>

        <div>
            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider block mb-2">Message Content</span>
            <div class="p-6 rounded-2xl bg-[#F5F8FF] border border-slate-200/80 text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-wrap">
{{ $message->message }}
            </div>
        </div>
    </div>

    {{-- Card Footer --}}
    <div class="p-6 sm:p-8 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-slate-500">
        <div>
            <span>Message ID: #{{ $message->id }}</span>
            <span class="mx-2">•</span>
            <span>Last updated: {{ $message->updated_at->diffForHumans() }}</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . $message->subject) }}" class="text-blue-600 font-bold hover:underline">
                Send quick reply to {{ $message->email }}
            </a>
        </div>
    </div>

</div>

@endsection

