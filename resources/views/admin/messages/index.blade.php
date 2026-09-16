@extends('layouts.admin')

@section('content')
<div x-data="messagesManager()" class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Messages</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Contact Messages</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $messages->total() }} Inquiries
                </span>
            </div>
            @if(($stats['unread'] ?? 0) > 0)
                <form method="POST" action="{{ route('admin.messages.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition-all cursor-pointer shadow-sm">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Mark All As Read ({{ $stats['unread'] }})</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Alert / Flash Feedback -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif

    <!-- Top Metrics Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Messages -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Inquiries</span>
                <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <!-- 2. Unread Messages -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Unread</span>
                <div class="h-8 w-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs">
                    {{ $stats['unread'] ?? 0 }}
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($stats['unread'] ?? 0) }}</div>
        </div>

        <!-- 3. Read Inquiries -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Read</span>
                <div class="h-8 w-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['read'] ?? 0) }}</div>
        </div>

        <!-- 4. Replied Inquiries -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Replied</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['replied'] ?? 0) }}</div>
        </div>
    </div>

    <!-- Main Messages Container Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
        
        <!-- Filter & Search Toolbar -->
        <form method="GET" action="{{ route('admin.messages.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Left: Entries selector -->
            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                <span>Showing</span>
                <select 
                    name="per_page" 
                    onchange="this.form.submit()" 
                    class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
                >
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>entries</span>
            </div>

            <!-- Right: Search, Status Tabs & Filter -->
            <div class="flex flex-wrap items-center gap-2.5 flex-1 lg:justify-end">
                
                <!-- Search Input -->
                <div class="relative flex-1 sm:w-64 sm:flex-initial">
                    <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search sender, subject, email..." 
                        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                </div>

                <!-- Status Filter -->
                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
                >
                    <option value="">All Statuses</option>
                    <option value="Unread" {{ request('status') === 'Unread' ? 'selected' : '' }}>Unread</option>
                    <option value="Read" {{ request('status') === 'Read' ? 'selected' : '' }}>Read</option>
                    <option value="Replied" {{ request('status') === 'Replied' ? 'selected' : '' }}>Replied</option>
                </select>

                <!-- Filter Button -->
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Filter
                </button>

                <!-- Reset Button -->
                @if(request()->hasAny(['search', 'status', 'per_page']))
                    <a 
                        href="{{ route('admin.messages.index') }}" 
                        class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl transition-all"
                        title="Reset Filters"
                    >
                        Reset
                    </a>
                @endif

            </div>

        </form>

        <!-- 1. DESKTOP TABLE VIEW (Visible on lg and above) -->
        <div class="hidden lg:block overflow-x-auto border border-slate-100 dark:border-slate-800/80 rounded-2xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Sender</th>
                        <th class="px-5 py-4">Contact Details</th>
                        <th class="px-5 py-4">Subject & Snippet</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Received</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($messages as $msg)
                        @php
                            $isUnread = ($msg->status === 'Unread');
                            $isReplied = ($msg->status === 'Replied');
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors {{ $isUnread ? 'bg-amber-50/20 dark:bg-amber-950/10' : '' }}">
                            
                            <!-- Sender Avatar & Name -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-2xl {{ $isUnread ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-300 dark:border-amber-800' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80' }} font-extrabold flex items-center justify-center text-sm shrink-0 border shadow-sm">
                                        {{ strtoupper(substr($msg->name, 0, 1)) }}
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 dark:text-white text-xs block truncate">{{ $msg->name }}</span>
                                            @if($isUnread)
                                                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-extrabold bg-amber-500 text-white animate-pulse">New</span>
                                            @endif
                                        </div>
                                        <span class="font-mono text-[10px] text-slate-400">#MSG-{{ str_pad($msg->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Info -->
                            <td class="px-5 py-4">
                                <div class="space-y-0.5">
                                    <a href="mailto:{{ $msg->email }}" class="text-slate-800 dark:text-slate-200 block font-medium hover:text-emerald-600 transition-colors">{{ $msg->email }}</a>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $msg->phone ?: 'No phone provided' }}</span>
                                </div>
                            </td>

                            <!-- Subject & Message Preview -->
                            <td class="px-5 py-4 max-w-sm">
                                <div class="space-y-0.5">
                                    <button 
                                        type="button" 
                                        @click="openViewModal({{ json_encode($msg) }})" 
                                        class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors block text-left truncate cursor-pointer"
                                    >
                                        {{ $msg->subject }}
                                    </button>
                                    <p class="text-slate-500 dark:text-slate-400 line-clamp-1 text-[11px] leading-relaxed">{{ $msg->message }}</p>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-5 py-4">
                                @if($isUnread)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Unread
                                    </span>
                                @elseif($isReplied)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Replied
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Read
                                    </span>
                                @endif
                            </td>

                            <!-- Received Date -->
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $msg->created_at ? $msg->created_at->format('M d, Y H:i') : '—' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    <!-- View Details Modal Trigger -->
                                    <button 
                                        type="button" 
                                        @click="openViewModal({{ json_encode($msg) }})" 
                                        class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-400 flex items-center justify-center transition-all cursor-pointer"
                                        title="Read Message"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>

                                    <!-- Toggle Read/Unread Status -->
                                    <form method="POST" action="{{ route('admin.messages.toggle-status', $msg->id) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-950/60 dark:hover:text-blue-400 flex items-center justify-center transition-all cursor-pointer" 
                                            title="{{ $isUnread ? 'Mark as Read' : 'Mark as Unread' }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        </button>
                                    </form>

                                    <!-- Direct Email Reply -->
                                    <a 
                                        href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->subject) }}" 
                                        class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-400 flex items-center justify-center transition-all"
                                        title="Reply via Email"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                    </a>

                                    <!-- Delete Message -->
                                    <form method="POST" action="{{ route('admin.messages.destroy', $msg->id) }}" onsubmit="return confirm('Are you sure you want to delete inquiry from \'{{ addslashes($msg->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer"
                                            title="Delete Message"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">No contact inquiries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE & TABLET CARDS VIEW (Visible on Mobile/Tablet < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
            @forelse($messages as $msg)
                @php
                    $isUnread = ($msg->status === 'Unread');
                    $isReplied = ($msg->status === 'Replied');
                @endphp
                <div class="bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 space-y-4 flex flex-col justify-between shadow-sm {{ $isUnread ? 'ring-2 ring-amber-500/20' : '' }}">
                    
                    <div class="space-y-3">
                        <!-- Top Row: Sender Info & Status Badge -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-11 w-11 rounded-2xl {{ $isUnread ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-300' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200' }} font-extrabold flex items-center justify-center text-base shrink-0 border shadow-sm">
                                    {{ strtoupper(substr($msg->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $msg->name }}</h3>
                                        @if($isUnread)
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-extrabold bg-amber-500 text-white">New</span>
                                        @endif
                                    </div>
                                    <span class="font-mono text-[10px] text-slate-400">#MSG-{{ str_pad($msg->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold shrink-0 {{ $isUnread ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200' : ($isReplied ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                                {{ $msg->status }}
                            </span>
                        </div>

                        <!-- Contact Details Box -->
                        <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-100 dark:border-slate-800/80 space-y-1.5 text-xs">
                            <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 truncate">
                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <span class="truncate font-medium">{{ $msg->email }}</span>
                            </div>
                            @if($msg->phone)
                                <div class="flex items-center gap-2 text-slate-500 font-mono text-[11px]">
                                    <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    <span>{{ $msg->phone }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Subject & Message Preview -->
                        <div class="space-y-1">
                            <h4 class="font-bold text-xs text-slate-900 dark:text-white line-clamp-1">{{ $msg->subject }}</h4>
                            <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed line-clamp-2">{{ $msg->message }}</p>
                        </div>
                    </div>

                    <!-- Footer: Received Date & Action Buttons -->
                    <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-2">
                        <span class="text-[10px] text-slate-400 font-mono">
                            {{ $msg->created_at ? $msg->created_at->format('M d, Y') : '' }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            <!-- View Message Button -->
                            <button 
                                type="button" 
                                @click="openViewModal({{ json_encode($msg) }})" 
                                class="px-3 py-1.5 rounded-xl bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-600 hover:text-white text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <span>Read</span>
                            </button>

                            <!-- Direct Email Reply -->
                            <a 
                                href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->subject) }}" 
                                class="p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all border border-emerald-200 dark:border-emerald-800/80"
                                title="Reply via Email"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                            </a>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('admin.messages.destroy', $msg->id) }}" onsubmit="return confirm('Delete message from \'{{ addslashes($msg->name) }}\'?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="p-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all border border-rose-200 dark:border-rose-800/80"
                                    title="Delete"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 italic">No contact inquiries found.</div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $messages->links() }}
            </div>
        @endif

    </div>

    <!-- VIEW INQUIRY MODAL -->
    <div 
        x-show="isViewModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        @keydown.escape.window="isViewModalOpen = false"
    >
        <div 
            @click.away="isViewModalOpen = false" 
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-2xl max-w-lg w-full space-y-4 transform transition-all"
        >
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="activeMsg.name"></h3>
                        <p class="text-[11px] text-slate-400 font-mono" x-text="activeMsg.email + (activeMsg.phone ? ' • ' + activeMsg.phone : '')"></p>
                    </div>
                </div>
                <button type="button" @click="isViewModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-bold text-sm">✕</button>
            </div>

            <!-- Subject -->
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Subject</span>
                <h4 class="font-bold text-sm text-slate-900 dark:text-white" x-text="activeMsg.subject"></h4>
            </div>

            <!-- Message Body -->
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Message</span>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line text-xs border border-slate-100 dark:border-slate-800 max-h-64 overflow-y-auto font-normal" x-text="activeMsg.message"></div>
            </div>

            <!-- Date & Status -->
            <div class="flex items-center justify-between text-xs text-slate-400 font-mono pt-1">
                <span x-text="'Received: ' + activeMsg.created_at"></span>
                <span class="font-bold" :class="activeMsg.status === 'Unread' ? 'text-amber-500' : 'text-emerald-500'" x-text="'Status: ' + activeMsg.status"></span>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button 
                    type="button" 
                    @click="isViewModalOpen = false" 
                    class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-all cursor-pointer"
                >
                    Close
                </button>

                <a 
                    :href="'mailto:' + activeMsg.email + '?subject=Re: ' + encodeURIComponent(activeMsg.subject || '')" 
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer flex items-center gap-1.5"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                    Reply via Email
                </a>
            </div>
        </div>
    </div>

</div>

<script>
function messagesManager() {
    return {
        isViewModalOpen: false,
        activeMsg: {
            id: null,
            name: '',
            email: '',
            phone: '',
            subject: '',
            message: '',
            status: '',
            created_at: ''
        },

        openViewModal(item) {
            this.activeMsg = {
                id: item.id,
                name: item.name || '',
                email: item.email || '',
                phone: item.phone || '',
                subject: item.subject || '',
                message: item.message || '',
                status: item.status || 'Read',
                created_at: item.created_at ? new Date(item.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : ''
            };
            this.isViewModalOpen = true;
        }
    };
}
</script>
@endsection
