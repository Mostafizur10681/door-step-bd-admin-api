@extends('layouts.admin')

@section('content')
<div class="space-y-5 sm:space-y-6 w-full max-w-full min-w-0">

    <!-- Top Bar & Breadcrumbs -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full min-w-0">
        <div class="min-w-0">
            <x-breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Orders']
            ]" />
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1 truncate">Order Management</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Track and fulfill customer orders and payments.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="w-full min-w-0 overflow-hidden">
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1.5 scrollbar-none no-scrollbar w-full py-0.5">
            <a href="{{ route('admin.orders.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ !request('status') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800' }}">
                All ({{ $statusCounts['all'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ request('status') === 'pending' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800' }}">
                Pending ({{ $statusCounts['pending'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ request('status') === 'processing' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800' }}">
                Processing ({{ $statusCounts['processing'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ request('status') === 'shipped' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800' }}">
                Shipped ({{ $statusCounts['shipped'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ request('status') === 'delivered' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800' }}">
                Delivered ({{ $statusCounts['delivered'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ request('status') === 'cancelled' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800' }}">
                Cancelled ({{ $statusCounts['cancelled'] }})
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 shadow-sm w-full min-w-0">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative w-full">
                <svg class="absolute top-2.5 left-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search order number, phone, customer..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                >
            </div>

            <div class="w-full">
                <select name="payment_status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                    <option value="">All Payment Statuses</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'payment_status', 'status']))
                    <a href="{{ route('admin.orders.index') }}" title="Clear Filters" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 rounded-xl transition-colors shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 1. DESKTOP DATA TABLE (Visible on Screen >= 1024px) -->
    <div class="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden w-full">
        <div class="overflow-x-auto w-full scrollbar-none">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Order Number</th>
                        <th class="px-5 py-3.5">Customer</th>
                        <th class="px-5 py-3.5">Items</th>
                        <th class="px-5 py-3.5">Total Amount</th>
                        <th class="px-5 py-3.5">Payment</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($orders as $order)
                        @php
                            $s = strtolower($order->status);
                            $badgeClasses = match($s) {
                                'delivered', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
                                'shipped', 'out-for-delivery' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
                                'processing' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
                                'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
                                default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                            };

                            $p = strtolower($order->payment_status ?? 'pending');
                            $paymentBadgeClasses = match($p) {
                                'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
                                'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
                                default => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 font-mono text-sm inline-flex items-center gap-1">
                                    <span>#{{ $order->order_number }}</span>
                                </a>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->created_at ? $order->created_at->format('M d, Y H:i') : '' }}</p>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $order->customer_name ?: ($order->user->name ?? 'Guest') }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $order->customer_phone ?: ($order->user->phone ?? 'N/A') }}</span>
                            </td>

                            <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $order->items->count() }} Products <span class="text-[11px] text-slate-400 font-normal">({{ $order->items->sum('quantity') }} items)</span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-900 dark:text-white text-sm font-mono">৳{{ number_format($order->total, 2) }}</span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $paymentBadgeClasses }}">
                                    {{ ucfirst($order->payment_status ?: 'Pending') }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeClasses }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" title="Print Invoice" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white text-slate-600 dark:text-slate-400 text-xs font-bold rounded-xl transition-all">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-all">
                                        <span>Details</span>
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="h-14 w-14 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-300 dark:text-slate-600">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No orders found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- 2. MOBILE & TABLET RESPONSIVE CARDS VIEW (Visible on Screen < 1024px) -->
    <div class="lg:hidden space-y-4 w-full min-w-0">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4 w-full min-w-0">
            @forelse($orders as $order)
                @php
                    $s = strtolower($order->status);
                    $badgeClasses = match($s) {
                        'delivered', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
                        'shipped', 'out-for-delivery' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
                        'processing' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
                        default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                    };

                    $p = strtolower($order->payment_status ?? 'pending');
                    $paymentBadgeClasses = match($p) {
                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
                        'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
                        default => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
                    };
                @endphp
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm space-y-3.5 flex flex-col justify-between hover:border-emerald-500/40 transition-all card-hover-effect w-full min-w-0 overflow-hidden">
                    
                    <!-- Card Header: Order No, Date & Status -->
                    <div class="flex items-start justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800/80 min-w-0">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 font-mono text-sm inline-flex items-center gap-1.5 truncate max-w-full">
                                <span class="truncate">#{{ $order->order_number }}</span>
                            </a>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5 truncate">
                                <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate">{{ $order->created_at ? $order->created_at->format('M d, Y • h:i A') : 'N/A' }}</span>
                            </div>
                        </div>
                        <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-bold border shrink-0 {{ $badgeClasses }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <!-- Customer Info -->
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-9 w-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                                {{ $order->customer_name ?: ($order->user->name ?? 'Guest Customer') }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono flex items-center gap-1 mt-0.5 truncate">
                                <svg class="h-3 w-3 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="truncate">{{ $order->customer_phone ?: ($order->user->phone ?? 'N/A') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Details Summary Box (Items & Payment) -->
                    <div class="grid grid-cols-2 gap-2 p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 text-xs min-w-0">
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-0.5">Items</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">
                                {{ $order->items->count() }} Products
                            </span>
                            <span class="text-[10px] text-slate-400 block font-normal truncate">({{ $order->items->sum('quantity') }} items)</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-1">Payment</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold border truncate {{ $paymentBadgeClasses }}">
                                {{ ucfirst($order->payment_status ?: 'Pending') }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Footer: Total & Actions -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2 min-w-0">
                        <div class="min-w-0">
                            <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total</div>
                            <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white font-mono truncate">
                                ৳{{ number_format($order->total, 2) }}
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <a 
                                href="{{ route('admin.orders.invoice', $order->id) }}" 
                                target="_blank" 
                                title="Print Invoice" 
                                class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white text-slate-600 dark:text-slate-400 rounded-xl transition-all shrink-0"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </a>
                            <a 
                                href="{{ route('admin.orders.show', $order->id) }}" 
                                class="inline-flex items-center gap-1 px-3 sm:px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all shrink-0"
                            >
                                <span>Details</span>
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-center text-slate-400 shadow-sm w-full">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="h-14 w-14 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-300 dark:text-slate-600">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No orders found matching your criteria.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($orders->hasPages())
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-3 sm:p-4 shadow-sm w-full overflow-x-auto scrollbar-none no-scrollbar">
                <div class="min-w-0">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
