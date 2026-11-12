@extends('backend.admin.layouts.app')

@section('title', 'Contact Messages — RouteConnect Admin')

@section('content')

{{-- ─── Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-envelope-open-text"></i> Inquiries & Feedback
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Contact Messages</h2>
        <p class="text-sm text-slate-500 mt-1">
            Review, inspect details, mark as read, and manage messages sent through the Contact Us form.
        </p>
    </div>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ─── Stats Row ─── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Inquiries</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $totalCount }}</span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg">
            <i class="fa-solid fa-inbox"></i>
        </div>
    </div>

    <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Unread Messages</span>
            <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $unreadCount }}</span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-envelope"></i>
        </div>
    </div>

    <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Read Messages</span>
            <span class="text-2xl font-extrabold text-emerald-700 mt-1 block">{{ $readCount }}</span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-envelope-open"></i>
        </div>
    </div>
</div>

{{-- ─── Search & Filters Bar ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
    <form action="{{ route('admin.contact-messages.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </div>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by sender name, email, subject, or message content..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
        </div>

        <select name="status" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Messages ({{ $totalCount }})</option>
            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread ({{ $unreadCount }})</option>
            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read ({{ $readCount }})</option>
        </select>

        <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-bold transition-colors inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if (request('search') || (request('status') && request('status') !== 'all'))
            <a href="{{ route('admin.contact-messages.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-bold transition-colors text-center inline-flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- ─── Messages Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-bold text-slate-700">
            {{ $messages->total() }} {{ \Illuminate\Support\Str::plural('message', $messages->total()) }} found
        </div>
    </div>

    @if ($messages->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                <i class="fa-regular fa-envelope"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">No contact messages found</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                @if (request('search') || request('status'))
                    No messages matched your current search filters. Try clearing your search parameters.
                @else
                    New inquiries submitted through the Contact Us form will appear here.
                @endif
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black uppercase text-slate-500 tracking-wider">
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Sender</th>
                        <th class="px-6 py-3.5">Subject & Preview</th>
                        <th class="px-6 py-3.5">Submitted</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach ($messages as $msg)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ !$msg->is_read ? 'bg-amber-50/30' : '' }}">
                            
                            {{-- Status Badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if (!$msg->is_read)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Unread
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">
                                        <i class="fa-solid fa-check text-[9px] text-emerald-600"></i>
                                        Read
                                    </span>
                                @endif
                            </td>

                            {{-- Sender Name & Email --}}
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 text-xs">{{ $msg->name }}</div>
                                <a href="mailto:{{ $msg->email }}" class="text-[11px] text-blue-600 hover:underline block mt-0.5">
                                    {{ $msg->email }}
                                </a>
                                @if($msg->user)
                                    <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                        Registered User
                                    </span>
                                @endif
                            </td>

                            {{-- Subject & Snippet --}}
                            <td class="px-6 py-4 max-w-md">
                                <a href="{{ route('admin.contact-messages.show', $msg->id) }}" class="font-bold text-slate-900 hover:text-blue-600 block truncate">
                                    {{ $msg->subject }}
                                </a>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ \Illuminate\Support\Str::limit($msg->message, 80) }}
                                </p>
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-[11px]">
                                <span class="font-bold text-slate-700 block">{{ $msg->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $msg->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    
                                    {{-- View Details --}}
                                    <a href="{{ route('admin.contact-messages.show', $msg->id) }}"
                                       title="Read Details"
                                       class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    {{-- Toggle Read/Unread --}}
                                    <form action="{{ route('admin.contact-messages.toggle-read', $msg->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                title="{{ $msg->is_read ? 'Mark as Unread' : 'Mark as Read' }}"
                                                class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                                            @if($msg->is_read)
                                                <i class="fa-regular fa-envelope text-xs"></i>
                                            @else
                                                <i class="fa-regular fa-envelope-open text-xs"></i>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete Message --}}
                                    <form action="{{ route('admin.contact-messages.destroy', $msg->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to permanently delete this contact message?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Delete Message"
                                                class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($messages->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $messages->links() }}
            </div>
        @endif
    @endif
</div>

@endsection

