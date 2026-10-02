@extends('backend.admin.layouts.app')

@section('title', 'User Profile — RouteConnect Admin')

@section('content')

{{-- ─── Page Header ─── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-user"></i> User Profile
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $user->name }}</h2>
        <p class="text-sm text-slate-500 mt-1">Full account details and profile information.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.users.edit', $user->id) }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-bold border border-indigo-200 transition-colors">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="max-w-3xl space-y-5">

    {{-- ─── Profile Header Card ─── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Dark banner --}}
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                {{-- Avatar --}}
                @if ($user->profile_photo)
                    <img src="{{ asset($user->profile_photo) }}"
                         alt="{{ $user->name }}"
                         class="w-20 h-20 rounded-2xl object-cover border-4 border-white/20 shadow-lg">
                @else
                    @php
                        $colors = [0 => 'bg-emerald-600', 1 => 'bg-rose-600', 2 => 'bg-indigo-600'];
                    @endphp
                    <div class="w-20 h-20 rounded-2xl {{ $colors[$user->user_type] ?? 'bg-slate-600' }} text-white font-extrabold flex items-center justify-center text-2xl border-4 border-white/20 shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
                {{-- Info --}}
                <div class="flex-1">
                    <h3 class="text-xl font-extrabold text-white">
                        {{ $user->name }}
                        @if ($user->id === auth()->id())
                            <span class="text-emerald-400 text-sm font-bold">(You)</span>
                        @endif
                    </h3>
                    <p class="text-sm text-slate-400 mt-0.5">{{ $user->email }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Joined: {{ $user->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>
                {{-- Badges --}}
                <div class="flex flex-col gap-2">
                    @if ($user->user_type == 1)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 text-xs font-extrabold border border-rose-500/30">
                            <i class="fa-solid fa-shield-halved"></i> Admin
                        </span>
                    @elseif ($user->user_type == 2)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-500/20 text-indigo-300 text-xs font-extrabold border border-indigo-500/30">
                            <i class="fa-solid fa-car"></i> Driver
                        </span>
                        @if ($user->driver_status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 text-xs font-extrabold border border-emerald-500/30">
                                <i class="fa-solid fa-circle-check"></i> Approved
                            </span>
                        @elseif ($user->driver_status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 text-xs font-extrabold border border-amber-500/30">
                                <i class="fa-solid fa-clock"></i> Pending
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 text-xs font-extrabold border border-rose-500/30">
                                <i class="fa-solid fa-circle-xmark"></i> Rejected
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 text-xs font-extrabold border border-emerald-500/30">
                            <i class="fa-solid fa-person"></i> Passenger
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Detail Grid --}}
        <div class="p-6">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4">Account Details</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Full Name</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $user->name }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Email Address</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $user->email }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Phone Number</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $user->phone ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Gender</div>
                    <div class="text-sm font-extrabold text-slate-900 capitalize">{{ $user->gender ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Date of Birth</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        {{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d M Y') : 'Not Provided' }}
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">CNIC Number</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $user->cnic ?? 'Not Provided' }}</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">User Type (ID)</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        @if ($user->user_type == 0) Passenger (0)
                        @elseif ($user->user_type == 1) Admin (1)
                        @else Driver (2)
                        @endif
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Driver Status</div>
                    <div class="text-sm font-extrabold capitalize
                        {{ $user->user_type == 2 ? ($user->driver_status === 'approved' ? 'text-emerald-600' : ($user->driver_status === 'pending' ? 'text-amber-600' : 'text-rose-600')) : 'text-slate-400' }}">
                        {{ $user->user_type == 2 ? ucfirst($user->driver_status) : 'N/A' }}
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Registration Date</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $user->created_at->format('d M Y, h:i A') }}</div>
                </div>

            </div>

            @if ($user->bio)
                <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-2">Bio / About</div>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $user->bio }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Actions Card ─── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-sm text-slate-500 font-semibold">
            <i class="fa-solid fa-gear mr-1 text-slate-400"></i>
            Manage this user account:
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.edit', $user->id) }}"
               class="px-5 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-sm border border-indigo-200 transition-colors inline-flex items-center gap-2">
                <i class="fa-solid fa-pen"></i> Edit User
            </a>

            @if ($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                      onsubmit="return confirm('Delete user \'{{ addslashes($user->name) }}\'? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-sm border border-rose-200 transition-colors inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i> Delete User
                    </button>
                </form>
            @else
                <span class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-300 font-bold text-sm border border-slate-100 cursor-not-allowed inline-flex items-center gap-2"
                      title="You cannot delete your own account">
                    <i class="fa-solid fa-trash"></i> Delete User
                </span>
            @endif
        </div>
    </div>

</div>

@endsection
