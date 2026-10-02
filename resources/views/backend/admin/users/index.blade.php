@extends('backend.admin.layouts.app')

@section('title', 'Users Management — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-users"></i> User Management
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">All Users</h2>
        <p class="text-sm text-slate-500 mt-1">Manage all passenger, driver, and admin accounts.</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm shadow-emerald-600/20 transition-all">
        <i class="fa-solid fa-plus"></i> Add New User
    </a>
</div>

{{-- ─── Flash Messages ─── --}}
@if (session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base shrink-0"></i>
        {{ session('error') }}
    </div>
@endif

{{-- ─── Search + Filter Bar ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">

        {{-- Search input --}}
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </div>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by name or email..."
                   class="w-full pl-9 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
        </div>

        {{-- User Type filter --}}
        <select name="type" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="all"  {{ $type === 'all'  ? 'selected' : '' }}>All Types ({{ $counts['all'] }})</option>
            <option value="0"    {{ $type === '0'    ? 'selected' : '' }}>Passengers ({{ $counts['passenger'] }})</option>
            <option value="2"    {{ $type === '2'    ? 'selected' : '' }}>Drivers ({{ $counts['driver'] }})</option>
            <option value="1"    {{ $type === '1'    ? 'selected' : '' }}>Admins ({{ $counts['admin'] }})</option>
        </select>

        {{-- Driver Status filter --}}
        <select name="status" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="all"     {{ $status === 'all'     ? 'selected' : '' }}>Any Status</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved"{{ $status === 'approved'? 'selected' : '' }}>Approved</option>
            <option value="rejected"{{ $status === 'rejected'? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-bold transition-colors">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>

        @if ($search || $type !== 'all' || $status !== 'all')
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-bold transition-colors text-center">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- ─── Users Table ─── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Table header count --}}
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-bold text-slate-700">
            {{ $users->total() }} {{ Str::plural('user', $users->total()) }} found
        </div>
        <div class="text-xs text-slate-400">
            Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
        </div>
    </div>

    @if ($users->isEmpty())
        <div class="p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-users-slash"></i>
            </div>
            <div class="text-sm font-extrabold text-slate-700 mb-1">No users found</div>
            <p class="text-xs text-slate-500">Try adjusting your search or filter criteria.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 w-8">#</th>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">User Type</th>
                        <th class="px-6 py-3">Driver Status</th>
                        <th class="px-6 py-3">Registered</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $i => $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- Row # --}}
                            <td class="px-6 py-4 text-slate-400 font-bold">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $i + 1 }}
                            </td>

                            {{-- Avatar + Name --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($user->profile_photo)
                                        <img src="{{ asset($user->profile_photo) }}"
                                             alt="{{ $user->name }}"
                                             class="w-10 h-10 rounded-xl object-cover border-2 border-slate-200">
                                    @else
                                        @php
                                            $colors = [0 => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                       1 => 'bg-rose-100 text-rose-700 border-rose-200',
                                                       2 => 'bg-indigo-100 text-indigo-700 border-indigo-200'];
                                            $color  = $colors[$user->user_type] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                        @endphp
                                        <div class="w-10 h-10 rounded-xl {{ $color }} font-extrabold flex items-center justify-center text-sm border">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-extrabold text-slate-900">{{ $user->name }}</div>
                                        @if ($user->id === auth()->id())
                                            <span class="text-[10px] text-emerald-600 font-bold">(You)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $user->phone ?? '—' }}</td>

                            {{-- User Type Badge --}}
                            <td class="px-6 py-4">
                                @if ($user->user_type == 1)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-extrabold border border-rose-200">
                                        <i class="fa-solid fa-shield-halved text-rose-500"></i> Admin
                                    </span>
                                @elseif ($user->user_type == 2)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800 text-[11px] font-extrabold border border-indigo-200">
                                        <i class="fa-solid fa-car text-indigo-500"></i> Driver
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">
                                        <i class="fa-solid fa-person text-emerald-500"></i> Passenger
                                    </span>
                                @endif
                            </td>

                            {{-- Driver Status --}}
                            <td class="px-6 py-4">
                                @if ($user->user_type == 2)
                                    @if ($user->driver_status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-200">Approved</span>
                                    @elseif ($user->driver_status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold border border-amber-200">Pending</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-extrabold border border-rose-200">Rejected</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-[11px] font-semibold">N/A</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View --}}
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                       class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold transition-colors">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold transition-colors border border-indigo-200">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </a>
                                    {{-- Delete — disabled for own account --}}
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                              onsubmit="return confirm('Delete user \'{{ addslashes($user->name) }}\'? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold transition-colors border border-rose-200">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="px-3 py-1.5 rounded-lg bg-slate-50 text-slate-300 font-bold border border-slate-100 cursor-not-allowed" title="Cannot delete your own account">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
