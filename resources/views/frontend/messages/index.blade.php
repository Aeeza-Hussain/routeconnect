@extends('frontend.layouts.app')

@section('title', 'Messages — RouteConnect')

@section('content')

<section class="py-6 sm:py-8 bg-slate-50 dark:bg-[#090e17] min-h-[calc(100vh-80px)] transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-300 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Main Chat Container Box --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-xl overflow-hidden flex flex-col md:flex-row h-[750px] transition-colors">
            
            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- LEFT PANE: CONVERSATION LIST --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            <div class="w-full md:w-80 lg:w-96 border-r border-slate-200 dark:border-slate-800 flex flex-col shrink-0 {{ $activeConversation ? 'hidden md:flex' : 'flex' }}">
                
                {{-- Header & Search --}}
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-comments text-emerald-600"></i>
                            <span>Messages</span>
                        </h2>
                        <a href="{{ route('activity.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                            My Activity
                        </a>
                    </div>

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text"
                               id="convSearchInput"
                               onkeyup="filterConversations()"
                               placeholder="Search conversations..."
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                {{-- Conversations Scroll List --}}
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60" id="convListContainer">
                    @forelse($conversations as $conv)
                        @php
                            $otherUser = $conv->otherUser($user);
                            $unreadCount = $conv->unreadCountFor($user);
                            $isSelected = $activeConversation && $activeConversation->id === $conv->id;
                            $latestMsg = $conv->latestMessage;
                        @endphp
                        <a href="{{ route('messages.index', ['conversation_id' => $conv->id]) }}"
                           class="conv-item block p-4 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/60 {{ $isSelected ? 'bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500' : '' }}"
                           data-name="{{ strtolower($otherUser?->name ?? '') }}"
                           data-title="{{ strtolower($conv->title ?? '') }}">
                            
                            <div class="flex items-start gap-3">
                                {{-- Avatar with status dot --}}
                                <div class="relative shrink-0">
                                    @if($otherUser && $otherUser->photo)
                                        <img src="{{ asset('storage/' . $otherUser->photo) }}" alt="{{ $otherUser->name }}" class="w-11 h-11 rounded-2xl object-cover shadow-sm border border-slate-200 dark:border-slate-700">
                                    @else
                                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-black text-sm shadow-sm">
                                            {{ strtoupper(substr($otherUser?->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    @if($unreadCount > 0)
                                        <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900 animate-pulse"></span>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <h4 class="text-xs font-black text-slate-900 dark:text-white truncate">
                                            {{ $otherUser?->name ?? 'User' }}
                                        </h4>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @if($unreadCount > 0)
                                                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-emerald-500 text-white shadow-xs">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                            <span class="text-[10px] text-slate-400">
                                                {{ $latestMsg ? $latestMsg->created_at->diffForHumans(null, true, true) : '' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.2 rounded-md {{ $otherUser?->user_type == 2 ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                            {{ $otherUser?->user_type == 2 ? 'Driver' : 'Passenger' }}
                                        </span>
                                        @if($conv->arrangement_status === 'confirmed')
                                            <span class="text-[9px] font-bold text-emerald-600 bg-emerald-100 dark:bg-emerald-950 px-1.5 py-0.2 rounded-md">
                                                Confirmed
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-1">
                                        @if($latestMsg)
                                            @if($latestMsg->sender_id === $user->id)<span class="text-slate-400">You: </span>@endif
                                            {{ $latestMsg->body }}
                                        @else
                                            <span class="italic text-slate-400">No messages yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                        </a>
                    @empty
                        <div class="p-8 text-center space-y-2">
                            <i class="fa-regular fa-comment-dots text-3xl text-slate-300 dark:text-slate-600"></i>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">No conversations yet</p>
                            <p class="text-[11px] text-slate-400">Browse vehicles or travel requests to start chatting.</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- RIGHT PANE: ACTIVE CHAT INTERFACE --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            <div class="flex-1 flex flex-col bg-slate-50/50 dark:bg-[#0c1322] {{ $activeConversation ? 'flex' : 'hidden md:flex' }}">
                
                @if($activeConversation)
                    @php
                        $activeOtherUser = $activeConversation->otherUser($user);
                        $arrangementDetails = $activeConversation->arrangement_details ?? [];
                        $isBlocked = $activeConversation->isBlocked();
                        $isBlockedByMe = $activeConversation->isBlockedByUser($user);
                    @endphp

                    {{-- Top Chat Header --}}
                    <div class="p-4 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Mobile back to list button --}}
                            <a href="{{ route('messages.index') }}" class="md:hidden p-2 text-slate-500 hover:text-slate-900 dark:hover:text-white">
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>

                            @if($activeOtherUser && $activeOtherUser->photo)
                                <img src="{{ asset('storage/' . $activeOtherUser->photo) }}" alt="{{ $activeOtherUser->name }}" class="w-10 h-10 rounded-2xl object-cover shadow-sm shrink-0 border border-slate-200 dark:border-slate-700">
                            @else
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-black text-sm shrink-0">
                                    {{ strtoupper(substr($activeOtherUser?->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            <div class="min-w-0 leading-tight">
                                <h3 class="text-sm font-black text-slate-900 dark:text-white truncate flex items-center gap-2">
                                    <span>{{ $activeOtherUser?->name ?? 'User' }}</span>
                                    <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full {{ $activeOtherUser?->user_type == 2 ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $activeOtherUser?->user_type == 2 ? 'Verified Driver' : 'Passenger' }}
                                    </span>
                                </h3>
                                <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                    {{ $activeConversation->title }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Controls & Arrangement Status --}}
                        <div class="flex items-center gap-2">
                            {{-- Propose / Confirm Button --}}
                            @if($activeConversation->arrangement_status !== 'confirmed')
                                <button type="button"
                                        onclick="openProposeModal()"
                                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-handshake"></i>
                                    <span class="hidden sm:inline">Propose Terms</span>
                                </button>
                            @endif

                            {{-- Menu Dropdown --}}
                            <div class="relative">
                                <button type="button"
                                        onclick="toggleChatMenu()"
                                        class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>

                                <div id="chatActionMenu" class="hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-1 z-50 text-xs">
                                    <button type="button" onclick="openProposeModal(); toggleChatMenu();" class="w-full text-left px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold flex items-center gap-2">
                                        <i class="fa-solid fa-handshake text-emerald-500 w-4"></i>
                                        <span>Propose Terms</span>
                                    </button>

                                    <form action="{{ route('messages.block', $activeConversation->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold flex items-center gap-2">
                                            <i class="fa-solid fa-ban text-amber-500 w-4"></i>
                                            <span>{{ $isBlocked ? 'Unblock User' : 'Block User' }}</span>
                                        </button>
                                    </form>

                                    <button type="button" onclick="openReportModal(); toggleChatMenu();" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-bold flex items-center gap-2">
                                        <i class="fa-solid fa-flag text-rose-500 w-4"></i>
                                        <span>Report User</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Arrangement Tracker Banner ─── --}}
                    @php
                        $status = strtolower($activeConversation->arrangement_status ?? 'pending');
                    @endphp
                    <div id="arrangementBanner" class="p-3.5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs 
                        @if($status === 'confirmed') bg-emerald-50/80 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300
                        @elseif($status === 'completed') bg-teal-50/80 dark:bg-teal-950/40 text-teal-900 dark:text-teal-300
                        @elseif($status === 'negotiating') bg-indigo-50/80 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-300
                        @elseif($status === 'cancelled') bg-rose-50/80 dark:bg-rose-950/40 text-rose-900 dark:text-rose-300
                        @else bg-amber-50/80 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300
                        @endif">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 
                                @if($status === 'confirmed') bg-emerald-600 text-white
                                @elseif($status === 'completed') bg-teal-600 text-white
                                @elseif($status === 'negotiating') bg-indigo-600 text-white
                                @elseif($status === 'cancelled') bg-rose-600 text-white
                                @else bg-amber-600 text-white
                                @endif">
                                @if($status === 'confirmed')
                                    <i class="fa-solid fa-check"></i>
                                @elseif($status === 'completed')
                                    <i class="fa-solid fa-flag-checkered"></i>
                                @elseif($status === 'cancelled')
                                    <i class="fa-solid fa-xmark"></i>
                                @elseif($status === 'negotiating')
                                    <i class="fa-solid fa-handshake"></i>
                                @else
                                    <i class="fa-solid fa-clock"></i>
                                @endif
                            </div>
                            <div>
                                <span class="font-extrabold uppercase text-[10px] tracking-wider block">
                                    Arrangement Status: <span class="font-black">{{ strtoupper($status) }}</span>
                                </span>
                                @if(!empty($arrangementDetails['route']))
                                    <p class="text-[11px] font-medium mt-0.5">
                                        Route: <strong>{{ $arrangementDetails['route'] }}</strong>
                                        @if(!empty($arrangementDetails['fare'])) · Fare: <strong>Rs. {{ number_format($arrangementDetails['fare'], 2) }}</strong> @endif
                                        @if(!empty($arrangementDetails['date'])) · Date: <strong>{{ $arrangementDetails['date'] }}</strong> @endif
                                        @if(!empty($arrangementDetails['seats'])) · Seats: <strong>{{ $arrangementDetails['seats'] }}</strong> @endif
                                    </p>
                                @else
                                    <p class="text-[11px] opacity-80 mt-0.5">Chatting does not book automatically. Discuss and confirm when ready.</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            @if($status === 'negotiating' || $status === 'pending')
                                <form action="{{ route('messages.confirm', $activeConversation->id) }}" method="POST" onsubmit="return confirm('Confirm this travel arrangement? Both parties must agree.');">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md shadow-emerald-600/20">
                                        <i class="fa-solid fa-check mr-1"></i> Confirm Arrangement
                                    </button>
                                </form>
                                <button type="button" onclick="openProposeModal()" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs hover:bg-slate-300">
                                    Propose Terms
                                </button>
                                <form action="{{ route('messages.cancel', $activeConversation->id) }}" method="POST" onsubmit="return confirm('Cancel this arrangement?');">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold text-[11px]">
                                        Cancel
                                    </button>
                                </form>
                            @elseif($status === 'confirmed')
                                <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-black uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid fa-lock"></i> Confirmed & Locked
                                </span>
                                <form action="{{ route('messages.complete', $activeConversation->id) }}" method="POST" onsubmit="return confirm('Mark this ride arrangement as completed?');">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-black text-xs shadow-md shadow-teal-600/20">
                                        <i class="fa-solid fa-flag-checkered mr-1"></i> Mark as Completed
                                    </button>
                                </form>
                                <form action="{{ route('messages.cancel', $activeConversation->id) }}" method="POST" onsubmit="return confirm('Cancel this confirmed arrangement?');">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold text-[11px]">
                                        Cancel
                                    </button>
                                </form>
                            @elseif($status === 'completed')
                                <span class="px-3 py-1 rounded-full bg-teal-600 text-white text-[10px] font-black uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> Completed
                                </span>
                            @elseif($status === 'cancelled')
                                <span class="px-3 py-1 rounded-full bg-rose-600 text-white text-[10px] font-black uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid fa-ban"></i> Cancelled
                                </span>
                                <button type="button" onclick="openProposeModal()" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                                    Propose New Terms
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Blocked Banner If Active --}}
                    @if($isBlocked)
                        <div class="p-3 bg-rose-50 dark:bg-rose-950/60 border-b border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs flex items-center justify-between gap-3">
                            <span class="font-bold"><i class="fa-solid fa-ban mr-1.5"></i> This conversation is currently blocked.</span>
                            <form action="{{ route('messages.block', $activeConversation->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-lg bg-rose-600 text-white font-bold text-xs">Unblock</button>
                            </form>
                        </div>
                    @endif

                    {{-- ─── Messages Stream ─── --}}
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4" id="messagesContainer">
                        @foreach($messages as $msg)
                            @if($msg->type === 'arrangement_proposal')
                                {{-- Proposal Banner in Stream --}}
                                <div class="flex justify-center my-3" data-msg-id="{{ $msg->id }}">
                                    <div class="max-w-md p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-center space-y-2 shadow-xs">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-600 text-white text-[10px] font-black uppercase">
                                            <i class="fa-solid fa-handshake"></i> Terms Proposed
                                        </div>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $msg->body }}</p>
                                        <span class="text-[10px] text-slate-400 block">{{ $msg->created_at->format('h:i A') }}</span>
                                    </div>
                                </div>
                            @elseif($msg->type === 'arrangement_confirmed')
                                {{-- Confirmation Banner in Stream --}}
                                <div class="flex justify-center my-3" data-msg-id="{{ $msg->id }}">
                                    <div class="max-w-md p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-center space-y-2 shadow-xs">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-black uppercase">
                                            <i class="fa-solid fa-circle-check"></i> Arrangement Confirmed
                                        </div>
                                        <p class="text-xs font-bold text-emerald-900 dark:text-emerald-200">{{ $msg->body }}</p>
                                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 block">{{ $msg->created_at->format('h:i A') }}</span>
                                    </div>
                                </div>
                            @elseif($msg->type === 'system')
                                <div class="flex justify-center my-2" data-msg-id="{{ $msg->id }}">
                                    <span class="px-3 py-1 rounded-full bg-slate-200 dark:bg-slate-800 text-[11px] text-slate-600 dark:text-slate-400 font-medium">
                                        {{ $msg->body }}
                                    </span>
                                </div>
                            @elseif($msg->sender_id === $user->id)
                                {{-- Sent by Current User (Right aligned) --}}
                                <div class="flex justify-end" data-msg-id="{{ $msg->id }}">
                                    <div class="max-w-sm sm:max-w-md space-y-1 text-right">
                                        <div class="inline-block p-3.5 rounded-2xl rounded-tr-xs bg-emerald-600 text-white text-xs font-medium text-left shadow-sm">
                                            @if($msg->body && (!$msg->hasAttachment() || $msg->body !== 'Sent an attachment: ' . $msg->attachment_name))
                                                <p class="leading-relaxed">{{ $msg->body }}</p>
                                            @endif
                                            @if($msg->hasAttachment())
                                                @if($msg->isImageAttachment())
                                                    <a href="{{ route('messages.attachment', [$activeConversation->id, $msg->id]) }}?preview=1" target="_blank" class="block {{ $msg->body ? 'mt-2' : '' }} rounded-xl overflow-hidden border border-white/20 max-w-xs group relative">
                                                        <img src="{{ route('messages.attachment', [$activeConversation->id, $msg->id]) }}?preview=1" alt="{{ $msg->attachment_name }}" class="w-full max-h-56 object-cover rounded-xl transition-transform group-hover:scale-105" loading="lazy">
                                                        <div class="p-1.5 bg-black/60 text-white text-[10px] flex items-center justify-between">
                                                            <span class="truncate max-w-[140px]">{{ $msg->attachment_name }}</span>
                                                            <span class="font-mono text-[9px] opacity-80">{{ $msg->formattedAttachmentSize() }}</span>
                                                        </div>
                                                    </a>
                                                @else
                                                    <a href="{{ route('messages.attachment', [$activeConversation->id, $msg->id]) }}" class="flex items-center gap-2.5 p-2.5 {{ $msg->body ? 'mt-2' : '' }} rounded-xl bg-emerald-700/80 hover:bg-emerald-700 text-white border border-white/20 transition-colors">
                                                        <i class="fa-solid fa-file-arrow-down text-lg"></i>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs font-bold truncate">{{ $msg->attachment_name }}</p>
                                                            <p class="text-[10px] text-emerald-100 font-mono">{{ $msg->formattedAttachmentSize() }}</p>
                                                        </div>
                                                        <i class="fa-solid fa-download text-xs opacity-80"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 flex items-center justify-end gap-1 font-mono">
                                            <span>{{ $msg->created_at->format('h:i A') }}</span>
                                            @if($msg->is_read)
                                                <i class="fa-solid fa-check-double text-emerald-500 text-[10px]" title="Read"></i>
                                            @else
                                                <i class="fa-solid fa-check text-slate-400 text-[10px]" title="Delivered"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Received from Other User (Left aligned) --}}
                                <div class="flex justify-start items-start gap-2.5" data-msg-id="{{ $msg->id }}">
                                    @if($msg->sender && $msg->sender->photo)
                                        <img src="{{ asset('storage/' . $msg->sender->photo) }}" alt="{{ $msg->sender->name }}" class="w-7 h-7 rounded-xl object-cover shadow-xs shrink-0 mt-1">
                                    @else
                                        <div class="w-7 h-7 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-bold text-xs shrink-0 mt-1">
                                            {{ strtoupper(substr($msg->sender?->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="max-w-sm sm:max-w-md space-y-1">
                                        <div class="inline-block p-3.5 rounded-2xl rounded-tl-xs bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs font-medium border border-slate-200/80 dark:border-slate-700 shadow-xs">
                                            @if($msg->body && (!$msg->hasAttachment() || $msg->body !== 'Sent an attachment: ' . $msg->attachment_name))
                                                <p class="leading-relaxed">{{ $msg->body }}</p>
                                            @endif
                                            @if($msg->hasAttachment())
                                                @if($msg->isImageAttachment())
                                                    <a href="{{ route('messages.attachment', [$activeConversation->id, $msg->id]) }}?preview=1" target="_blank" class="block {{ $msg->body ? 'mt-2' : '' }} rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-xs group relative">
                                                        <img src="{{ route('messages.attachment', [$activeConversation->id, $msg->id]) }}?preview=1" alt="{{ $msg->attachment_name }}" class="w-full max-h-56 object-cover rounded-xl transition-transform group-hover:scale-105" loading="lazy">
                                                        <div class="p-1.5 bg-slate-900/80 text-white text-[10px] flex items-center justify-between">
                                                            <span class="truncate max-w-[140px]">{{ $msg->attachment_name }}</span>
                                                            <span class="font-mono text-[9px] text-slate-300">{{ $msg->formattedAttachmentSize() }}</span>
                                                        </div>
                                                    </a>
                                                @else
                                                    <a href="{{ route('messages.attachment', [$activeConversation->id, $msg->id]) }}" class="flex items-center gap-2.5 p-2.5 {{ $msg->body ? 'mt-2' : '' }} rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-600 transition-colors">
                                                        <i class="fa-solid fa-file-arrow-down text-lg text-teal-500"></i>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs font-bold truncate">{{ $msg->attachment_name }}</p>
                                                            <p class="text-[10px] text-slate-400 font-mono">{{ $msg->formattedAttachmentSize() }}</p>
                                                        </div>
                                                        <i class="fa-solid fa-download text-xs text-slate-400"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $msg->created_at->format('h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- ─── Chat Input Bar ─── --}}
                    @if(!$isBlocked)
                        <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
                            {{-- Attachment Preview Pill --}}
                            <div id="attachmentPreview" class="hidden items-center justify-between p-2.5 mb-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="fa-solid fa-paperclip text-teal-500"></i>
                                    <span id="attachmentFileName" class="font-bold text-slate-800 dark:text-slate-200 truncate"></span>
                                    <span id="attachmentFileSize" class="text-[10px] text-slate-400 font-mono shrink-0"></span>
                                </div>
                                <button type="button" onclick="clearSelectedAttachment()" class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 hover:text-rose-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>

                            <form id="chatForm" onsubmit="handleSendMessage(event)" class="flex items-center gap-2" enctype="multipart/form-data">
                                @csrf
                                <input type="file" id="attachmentInput" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.txt" class="hidden" onchange="handleFileSelect(event)">

                                <button type="button"
                                        onclick="document.getElementById('attachmentInput').click()"
                                        title="Attach image or document"
                                        class="w-11 h-11 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-sm transition-colors shrink-0">
                                    <i class="fa-solid fa-paperclip"></i>
                                </button>

                                <input type="text"
                                       id="messageInput"
                                       name="body"
                                       autocomplete="off"
                                       placeholder="Type a message to {{ $activeOtherUser?->name }}..."
                                       class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium">

                                <button type="submit"
                                        id="sendBtn"
                                        class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1.5 shrink-0">
                                    <span>Send</span>
                                    <i class="fa-solid fa-paper-plane text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 bg-slate-100 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 text-center text-xs text-slate-500">
                            Messaging is disabled while blocked.
                        </div>
                    @endif

                @else
                    {{-- Empty State: No Conversation Selected --}}
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4">
                        <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-3xl">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Your Messaging Hub</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                                Select a conversation on the left to review ride discussions, negotiate terms, and confirm agreements.
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-2">
                            <a href="{{ route('activity.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                                My Activity
                            </a>
                            <a href="{{ url('/trips') }}" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-500">
                                Find a Ride
                            </a>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>
</section>

@if($activeConversation)
    {{-- ─── Modal: Propose Terms ─── --}}
    <div id="proposeModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-handshake text-emerald-600"></i>
                    <span>Propose Arrangement Terms</span>
                </h3>
                <button type="button" onclick="closeProposeModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('messages.propose', $activeConversation->id) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Route (Pickup → Drop-off) *</label>
                    <input type="text" name="route" required value="{{ $arrangementDetails['route'] ?? 'Islamabad → Murree' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Travel Date *</label>
                        <input type="date" name="date" required min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" value="{{ $arrangementDetails['date'] ?? \Carbon\Carbon::today()->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Time</label>
                        <input type="text" name="time" placeholder="e.g. 09:30 AM" value="{{ $arrangementDetails['time'] ?? '09:00 AM' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Agreed Fare (PKR) *</label>
                        <input type="number" name="fare" required min="50" value="{{ $arrangementDetails['fare'] ?? 1500 }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Seats</label>
                        <input type="number" name="seats" min="1" max="50" value="{{ $arrangementDetails['seats'] ?? 1 }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Special Notes</label>
                    <textarea name="notes" rows="2" placeholder="Luggage details or specific pickup spots..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">{{ $arrangementDetails['notes'] ?? '' }}</textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeProposeModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black shadow-md">Post Proposal to Chat</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Modal: Report User ─── --}}
    <div id="reportModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-black text-rose-600 flex items-center gap-2">
                    <i class="fa-solid fa-flag"></i>
                    <span>Report Inappropriate Behavior</span>
                </h3>
                <button type="button" onclick="closeReportModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('messages.report', $activeConversation->id) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Reason for Report *</label>
                    <select name="reason" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-semibold">
                        <option value="Spam / Unsolicited messages">Spam / Unsolicited messages</option>
                        <option value="Inappropriate or abusive language">Inappropriate or abusive language</option>
                        <option value="Unreasonable fare demands / extortion">Unreasonable fare demands / extortion</option>
                        <option value="No-show or fraud">No-show or fraud</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Details (Optional)</label>
                    <textarea name="details" rows="3" placeholder="Explain what happened..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-black shadow-md">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
const activeConvId = {{ $activeConversation ? $activeConversation->id : 'null' }};
const currentUserId = {{ $user->id }};

function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}
window.addEventListener('DOMContentLoaded', scrollToBottom);

function toggleChatMenu() {
    const menu = document.getElementById('chatActionMenu');
    if (menu) menu.classList.toggle('hidden');
}

function openProposeModal() {
    const m = document.getElementById('proposeModal');
    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
}
function closeProposeModal() {
    const m = document.getElementById('proposeModal');
    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
}

function openReportModal() {
    const m = document.getElementById('reportModal');
    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
}
function closeReportModal() {
    const m = document.getElementById('reportModal');
    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
}

function filterConversations() {
    const query = document.getElementById('convSearchInput').value.toLowerCase();
    const items = document.querySelectorAll('.conv-item');
    items.forEach(el => {
        const name = el.getAttribute('data-name') || '';
        const title = el.getAttribute('data-title') || '';
        if (name.includes(query) || title.includes(query)) {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    });
}

// ─── Instant Message Sending & Polling ───
function handleFileSelect(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('attachmentPreview');
    const nameEl = document.getElementById('attachmentFileName');
    const sizeEl = document.getElementById('attachmentFileSize');
    if (!file) {
        if (preview) preview.classList.add('hidden');
        return;
    }
    if (preview) {
        preview.classList.remove('hidden');
        preview.classList.add('flex');
    }
    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) {
        const kb = file.size / 1024;
        sizeEl.textContent = kb > 1024 ? (kb / 1024).toFixed(1) + ' MB' : kb.toFixed(1) + ' KB';
    }
}

function clearSelectedAttachment() {
    const fileInput = document.getElementById('attachmentInput');
    const preview = document.getElementById('attachmentPreview');
    if (fileInput) fileInput.value = '';
    if (preview) {
        preview.classList.add('hidden');
        preview.classList.remove('flex');
    }
}

function renderAttachmentHtml(msg, isSender) {
    if (!msg.attachment_url) return '';
    const safeUrl = msg.attachment_url;
    const safeName = escapeHtml(msg.attachment_name || 'Attachment');
    const safeSize = escapeHtml(msg.attachment_size || '');
    if (msg.is_image_attachment) {
        return `
            <a href="${safeUrl}?preview=1" target="_blank" class="block mt-2 rounded-xl overflow-hidden border ${isSender ? 'border-white/20' : 'border-slate-200 dark:border-slate-700'} max-w-xs group relative">
                <img src="${safeUrl}?preview=1" alt="${safeName}" class="w-full max-h-56 object-cover rounded-xl transition-transform group-hover:scale-105" loading="lazy">
                <div class="p-1.5 ${isSender ? 'bg-black/60 text-white' : 'bg-slate-900/80 text-white'} text-[10px] flex items-center justify-between">
                    <span class="truncate max-w-[140px]">${safeName}</span>
                    <span class="font-mono text-[9px] opacity-80">${safeSize}</span>
                </div>
            </a>
        `;
    } else {
        return `
            <a href="${safeUrl}" class="flex items-center gap-2.5 p-2.5 mt-2 rounded-xl ${isSender ? 'bg-emerald-700/80 hover:bg-emerald-700 text-white border border-white/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-600'} transition-colors">
                <i class="fa-solid fa-file-arrow-down text-lg ${isSender ? '' : 'text-teal-500'}"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold truncate">${safeName}</p>
                    <p class="text-[10px] ${isSender ? 'text-emerald-100' : 'text-slate-400'} font-mono">${safeSize}</p>
                </div>
                <i class="fa-solid fa-download text-xs opacity-80"></i>
            </a>
        `;
    }
}

async function handleSendMessage(e) {
    e.preventDefault();
    const input = document.getElementById('messageInput');
    const fileInput = document.getElementById('attachmentInput');
    const text = input ? input.value.trim() : '';
    const file = fileInput && fileInput.files ? fileInput.files[0] : null;

    if ((!text && !file) || !activeConvId) return;

    const formData = new FormData();
    if (text) formData.append('body', text);
    if (file) formData.append('attachment', file);

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    formData.append('_token', csrf);

    if (input) input.value = '';
    clearSelectedAttachment();

    const container = document.getElementById('messagesContainer');
    const tempId = 'temp_' + Date.now();
    const bubble = document.createElement('div');
    bubble.className = 'flex justify-end';
    bubble.id = tempId;

    let tempAttachmentHtml = '';
    if (file) {
        tempAttachmentHtml = `
            <div class="p-2.5 mt-2 rounded-xl bg-emerald-700/80 text-white text-[11px] flex items-center gap-2">
                <i class="fa-solid fa-paperclip"></i>
                <span class="truncate max-w-[140px]">${escapeHtml(file.name)}</span>
                <span class="opacity-80">Uploading...</span>
            </div>
        `;
    }

    bubble.innerHTML = `
        <div class="max-w-sm sm:max-w-md space-y-1 text-right">
            <div class="inline-block p-3.5 rounded-2xl rounded-tr-xs bg-emerald-600 text-white text-xs font-medium text-left shadow-sm">
                ${text ? `<p class="leading-relaxed">${escapeHtml(text)}</p>` : ''}
                ${tempAttachmentHtml}
            </div>
            <div class="text-[10px] text-slate-400 flex items-center justify-end gap-1 font-mono">
                <span>Sending...</span>
                <i class="fa-solid fa-clock text-slate-400 text-[10px]"></i>
            </div>
        </div>
    `;
    container.appendChild(bubble);
    scrollToBottom();

    try {
        const response = await fetch(`/messages/${activeConvId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (response.ok) {
            const result = await response.json();
            if (result.message) {
                const realMsg = result.message;
                const tempEl = document.getElementById(tempId);
                if (tempEl) {
                    tempEl.setAttribute('data-msg-id', realMsg.id);
                    tempEl.innerHTML = `
                        <div class="max-w-sm sm:max-w-md space-y-1 text-right">
                            <div class="inline-block p-3.5 rounded-2xl rounded-tr-xs bg-emerald-600 text-white text-xs font-medium text-left shadow-sm">
                                ${realMsg.body && (!realMsg.attachment_url || realMsg.body !== 'Sent an attachment: ' + realMsg.attachment_name) ? `<p class="leading-relaxed">${escapeHtml(realMsg.body)}</p>` : ''}
                                ${renderAttachmentHtml(realMsg, true)}
                            </div>
                            <div class="text-[10px] text-slate-400 flex items-center justify-end gap-1 font-mono">
                                <span>Just now</span>
                                <i class="fa-solid fa-check text-slate-400 text-[10px]" title="Delivered"></i>
                            </div>
                        </div>
                    `;
                }
            }
        } else {
            console.error('Failed to send message');
        }
    } catch (err) {
        console.error('Error sending message:', err);
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.innerText = str;
    return div.innerHTML;
}

// ─── Live Polling Refresh (every 3 seconds) ───
let pollingInterval = null;
function getLastMessageId() {
    const container = document.getElementById('messagesContainer');
    if (!container) return 0;
    const items = container.querySelectorAll('[data-msg-id]');
    if (items.length === 0) return 0;
    const lastItem = items[items.length - 1];
    return parseInt(lastItem.getAttribute('data-msg-id')) || 0;
}

async function pollMessages() {
    if (!activeConvId) return;
    const sinceId = getLastMessageId();

    try {
        const res = await fetch(`/messages/${activeConvId}/fetch?since_id=${sinceId}`);
        if (!res.ok) return;
        const data = await res.json();

        if (data.messages && data.messages.length > 0) {
            const container = document.getElementById('messagesContainer');
            data.messages.forEach(msg => {
                // If it was already optimistically rendered, skip
                if (container.querySelector(`[data-msg-id="${msg.id}"]`)) return;

                const wrapper = document.createElement('div');
                wrapper.setAttribute('data-msg-id', msg.id);

                if (msg.type === 'arrangement_proposal' || msg.type === 'arrangement_confirmed' || msg.type === 'system') {
                    wrapper.className = 'flex justify-center my-3';
                    const isConf = msg.type === 'arrangement_confirmed';
                    wrapper.innerHTML = `
                        <div class="max-w-md p-4 rounded-2xl ${isConf ? 'bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-emerald-900 dark:text-emerald-200' : 'bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 text-indigo-900 dark:text-indigo-200'} text-center space-y-2">
                            <span class="inline-block px-2.5 py-0.5 rounded-full ${isConf ? 'bg-emerald-600' : 'bg-indigo-600'} text-white text-[10px] font-black uppercase">
                                ${isConf ? 'Arrangement Confirmed' : 'Terms Proposed'}
                            </span>
                            <p class="text-xs font-bold">${escapeHtml(msg.body)}</p>
                        </div>
                    `;
                } else if (msg.sender_id === currentUserId) {
                    wrapper.className = 'flex justify-end';
                    wrapper.innerHTML = `
                        <div class="max-w-sm sm:max-w-md space-y-1 text-right">
                            <div class="inline-block p-3.5 rounded-2xl rounded-tr-xs bg-emerald-600 text-white text-xs font-medium text-left shadow-sm">
                                ${msg.body && (!msg.attachment_url || msg.body !== 'Sent an attachment: ' + msg.attachment_name) ? `<p class="leading-relaxed">${escapeHtml(msg.body)}</p>` : ''}
                                ${renderAttachmentHtml(msg, true)}
                            </div>
                            <div class="text-[10px] text-slate-400 flex items-center justify-end gap-1 font-mono">
                                <span>Just now</span>
                                <i class="fa-solid fa-check text-slate-400 text-[10px]"></i>
                            </div>
                        </div>
                    `;
                } else {
                    wrapper.className = 'flex justify-start items-start gap-2.5';
                    wrapper.innerHTML = `
                        <div class="w-7 h-7 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-bold text-xs shrink-0 mt-1">
                            ${msg.sender ? msg.sender.name.charAt(0).toUpperCase() : 'U'}
                        </div>
                        <div class="max-w-sm sm:max-w-md space-y-1">
                            <div class="inline-block p-3.5 rounded-2xl rounded-tl-xs bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs font-medium border border-slate-200/80 dark:border-slate-700 shadow-xs">
                                ${msg.body && (!msg.attachment_url || msg.body !== 'Sent an attachment: ' + msg.attachment_name) ? `<p class="leading-relaxed">${escapeHtml(msg.body)}</p>` : ''}
                                ${renderAttachmentHtml(msg, false)}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">Just now</div>
                        </div>
                    `;
                }
                container.appendChild(wrapper);
            });
            scrollToBottom();
        }
    } catch (err) {
        // Silent fail on network blip
    }
}

if (activeConvId) {
    pollingInterval = setInterval(pollMessages, 3000);
}
</script>

@endsection

