@extends('frontend.layouts.app')

@section('title', 'Notifications — RouteConnect')

@section('content')

{{-- ─── Hero Header ─── --}}
<section class="bg-slate-950 text-white py-10 sm:py-12 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.15),rgba(255,255,255,0))]"></div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-bell text-emerald-400"></i> Notification Center
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Notifications & Alerts
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    Stay updated on your seat bookings, schedule cancellations, and driver account status.
                </p>
            </div>

            @if ($unreadCount > 0)
                <div class="self-start sm:self-center">
                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all flex items-center gap-2">
                            <i class="fa-solid fa-check-double text-emerald-400"></i>
                            <span>Mark All as Read</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

    </div>
</section>

{{-- ─── Main Content ─── --}}
<section class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Filter Tabs & Counts --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    All ({{ $totalCount }})
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'unread' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Unread ({{ $unreadCount }})
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'read']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'read' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Read ({{ $totalCount - $unreadCount }})
                </a>
            </div>

            <div class="text-xs text-slate-500 font-medium">
                Showing {{ $notifications->count() }} of {{ $notifications->total() }} alerts
            </div>
        </div>

        {{-- Notifications List Card --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            
            @if ($notifications->isEmpty())
                <div class="py-16 text-center space-y-4 px-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-regular fa-bell-slash"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-extrabold text-slate-800">No Notifications</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            @if ($status === 'unread')
                                You have caught up with all your updates. No unread notifications remaining.
                            @else
                                You don't have any notifications or account activity alerts yet.
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($notifications as $notification)
                        <div class="p-5 sm:p-6 transition-colors {{ $notification->is_read ? 'bg-white hover:bg-slate-50/60' : 'bg-emerald-50/30 hover:bg-emerald-50/50' }} flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            
                            <div class="flex items-start gap-4">
                                {{-- Icon indicator --}}
                                <div class="w-10 h-10 rounded-2xl flex-shrink-0 flex items-center justify-center text-sm {{ $notification->is_read ? 'bg-slate-100 text-slate-500' : 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' }}">
                                    @if (str_contains(strtolower($notification->message), 'approved'))
                                        <i class="fa-solid fa-user-check"></i>
                                    @elseif (str_contains(strtolower($notification->message), 'rejected') || str_contains(strtolower($notification->message), 'cancelled'))
                                        <i class="fa-solid fa-circle-xmark"></i>
                                    @elseif (str_contains(strtolower($notification->message), 'confirmed') || str_contains(strtolower($notification->message), 'booked'))
                                        <i class="fa-solid fa-ticket"></i>
                                    @else
                                        <i class="fa-solid fa-bell"></i>
                                    @endif
                                </div>

                                {{-- Notification Body --}}
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        @if (!$notification->is_read)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">
                                                New
                                            </span>
                                        @endif
                                        <span class="text-[11px] text-slate-400 font-mono">
                                            {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Recently' }} • {{ $notification->created_at ? $notification->created_at->format('M d, Y h:i A') : '' }}
                                        </span>
                                    </div>

                                    <p class="text-xs sm:text-sm text-slate-800 font-medium leading-relaxed">
                                        {{ $notification->message }}
                                    </p>

                                    @if ($notification->trip_id)
                                        <div class="pt-1">
                                            <a href="{{ url('/trips/' . $notification->trip_id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                                                <i class="fa-solid fa-route text-emerald-600 text-[11px]"></i>
                                                <span>View Trip #{{ $notification->trip_id }} Details</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Mark as Read CTA --}}
                            <div class="self-end sm:self-center flex-shrink-0">
                                @if (!$notification->is_read)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                                            <i class="fa-regular fa-check-circle text-emerald-600"></i>
                                            <span>Mark Read</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 font-semibold px-2 py-1 rounded-lg bg-slate-50">
                                        <i class="fa-solid fa-check text-[10px]"></i> Read
                                    </span>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

                @if ($notifications->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif

        </div>

    </div>
</section>

@endsection
