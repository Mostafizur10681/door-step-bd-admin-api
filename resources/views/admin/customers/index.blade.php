@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Customers</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Customer Management</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $customers->total() }} Customers
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    <span>Add Customer</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert / Validation Feedback -->
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
        <!-- 1. Total Customers -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Customers</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <!-- 2. Active Customers -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Active Accounts</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['active'] ?? 0) }}</div>
        </div>

        <!-- 3. Blocked Customers -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Blocked Accounts</span>
                <div class="h-8 w-8 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-rose-600 dark:text-rose-400">{{ number_format($stats['blocked'] ?? 0) }}</div>
        </div>

        <!-- 4. Total Customer Orders -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Orders</span>
                <div class="h-8 w-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total_orders'] ?? 0) }}</div>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
        
        <!-- Filter Toolbar -->
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
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

            <!-- Right: Search, Status & Submit -->
            <div class="flex flex-wrap items-center gap-2.5 flex-1 lg:justify-end">
                
                <!-- Search Input -->
                <div class="relative flex-1 sm:w-64 sm:flex-initial">
                    <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search name, email, phone..." 
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
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>

                <!-- Filter button -->
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Filter
                </button>

                <!-- Reset button -->
                @if(request()->hasAny(['search', 'status', 'per_page']))
                    <a 
                        href="{{ route('admin.customers.index') }}" 
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
                        <th class="px-5 py-4">Customer</th>
                        <th class="px-5 py-4">Contact Info</th>
                        <th class="px-5 py-4">Orders</th>
                        <th class="px-5 py-4">Total Spent</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Joined Date</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($customers as $customer)
                        @php
                            $isActive = ($customer->status === 'active');
                            $spent = $customer->orders_sum_total ?? 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <!-- Customer Avatar & Name -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center text-sm shrink-0 border border-emerald-200 dark:border-emerald-800/80 shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors block truncate text-xs">
                                            {{ $customer->name }}
                                        </a>
                                        <span class="px-1.5 py-0.2 rounded-md font-mono text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                            #CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Info -->
                            <td class="px-5 py-4">
                                <div class="space-y-0.5">
                                    <span class="text-slate-800 dark:text-slate-200 block font-medium">{{ $customer->email }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $customer->phone ?: 'No phone provided' }}</span>
                                </div>
                            </td>

                            <!-- Orders Count -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 dark:text-slate-200">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    {{ $customer->orders_count }} {{ Str::plural('Order', $customer->orders_count) }}
                                </span>
                            </td>

                            <!-- Total Spent -->
                            <td class="px-5 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                                ${{ number_format($spent, 2) }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold {{ $isActive ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>

                            <!-- Joined Date -->
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $customer->created_at ? $customer->created_at->format('M d, Y') : '—' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    <!-- View Details -->
                                    <a 
                                        href="{{ route('admin.customers.show', $customer->id) }}" 
                                        class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-400 flex items-center justify-center transition-all"
                                        title="View Profile & Orders"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>

                                    <!-- Toggle Block/Unblock -->
                                    <form method="POST" action="{{ route('admin.customers.toggle-block', $customer->id) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="h-8 w-8 rounded-xl {{ $isActive ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-50 hover:text-amber-600' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 hover:bg-emerald-100' }} flex items-center justify-center transition-all cursor-pointer" 
                                            title="{{ $isActive ? 'Block Customer' : 'Unblock Customer' }}"
                                        >
                                            @if($isActive)
                                                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                            @else
                                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Delete Customer -->
                                    <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}" onsubmit="return confirm('Are you sure you want to delete customer \'{{ addslashes($customer->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer"
                                            title="Delete Customer"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 italic">No customer records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE & TABLET CARDS VIEW (Visible on Mobile/Tablet < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
            @forelse($customers as $customer)
                @php
                    $isActive = ($customer->status === 'active');
                    $spent = $customer->orders_sum_total ?? 0;
                @endphp
                <div class="bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 space-y-4 flex flex-col justify-between shadow-sm">
                    
                    <div class="space-y-3">
                        <!-- Top Row: Avatar, Name, ID & Status -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-11 w-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center text-base shrink-0 border border-emerald-200 dark:border-emerald-800/80 shadow-sm">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="font-bold text-slate-900 dark:text-white text-sm hover:text-emerald-600 truncate block">
                                        {{ $customer->name }}
                                    </a>
                                    <span class="font-mono text-[10px] font-semibold text-slate-400">
                                        #CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-bold shrink-0 {{ $isActive ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ ucfirst($customer->status) }}
                            </span>
                        </div>

                        <!-- Contact Details -->
                        <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-100 dark:border-slate-800/80 space-y-1.5 text-xs">
                            <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 truncate">
                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <span class="truncate">{{ $customer->email }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500 font-mono text-[11px]">
                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span>{{ $customer->phone ?: 'No phone' }}</span>
                            </div>
                        </div>

                        <!-- Orders & Spent Grid -->
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-white dark:bg-slate-900 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800 text-center">
                                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">Orders Placed</div>
                                <div class="font-bold text-slate-900 dark:text-white">{{ $customer->orders_count }}</div>
                            </div>

                            <div class="bg-white dark:bg-slate-900 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800 text-center">
                                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">Total Spent</div>
                                <div class="font-extrabold text-emerald-600 dark:text-emerald-400">${{ number_format($spent, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer: Joined Date & Action Buttons -->
                    <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-2">
                        <span class="text-[10px] text-slate-400 font-mono">
                            Joined {{ $customer->created_at ? $customer->created_at->format('M d, Y') : '—' }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            <!-- View Profile -->
                            <a 
                                href="{{ route('admin.customers.show', $customer->id) }}" 
                                class="px-3 py-1.5 rounded-xl bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-600 hover:text-white text-xs font-semibold flex items-center gap-1 transition-all"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <span>Profile</span>
                            </a>

                            <!-- Block / Unblock -->
                            <form method="POST" action="{{ route('admin.customers.toggle-block', $customer->id) }}" class="inline">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="p-1.5 rounded-xl {{ $isActive ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/50' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50' }} border border-slate-200 dark:border-slate-700 flex items-center justify-center transition-all"
                                    title="{{ $isActive ? 'Block Customer' : 'Unblock Customer' }}"
                                >
                                    @if($isActive)
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                    @else
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                    @endif
                                </button>
                            </form>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}" onsubmit="return confirm('Delete customer \'{{ addslashes($customer->name) }}\'?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="p-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all border border-rose-200 dark:border-rose-800"
                                    title="Delete Customer"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 italic">No customer records found.</div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $customers->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
