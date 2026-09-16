@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16" x-data="wishlistNotifier()">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Wishlist</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Customer Wishlists</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $wishlists->total() }} Saved Items
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    All Products
                </a>
            </div>
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
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif

    <!-- Top Metrics Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Saved Items -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Wishlisted</span>
                <div class="h-8 w-8 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <!-- 2. Unique Customers -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Unique Customers</span>
                <div class="h-8 w-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['unique_customers'] ?? 0) }}</div>
        </div>

        <!-- 3. In Stock Items -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">In Stock</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['in_stock'] ?? 0) }}</div>
        </div>

        <!-- 4. Notified Customers -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Notifications Sent</span>
                <div class="h-8 w-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($stats['notified_count'] ?? 0) }}</div>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
        
        <!-- Filter Toolbar -->
        <form method="GET" action="{{ route('admin.wishlist.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
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

            <!-- Right: Search, Stock Filter & Submit -->
            <div class="flex flex-wrap items-center gap-2.5 flex-1 lg:justify-end">
                
                <!-- Search Input -->
                <div class="relative flex-1 sm:w-64 sm:flex-initial">
                    <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search customer, product, SKU..." 
                        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                </div>

                <!-- Stock Filter -->
                <select 
                    name="stock_status" 
                    onchange="this.form.submit()" 
                    class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
                >
                    <option value="">All Inventory</option>
                    <option value="in-stock" {{ request('stock_status') === 'in-stock' ? 'selected' : '' }}>In Stock Only</option>
                    <option value="out-of-stock" {{ request('stock_status') === 'out-of-stock' ? 'selected' : '' }}>Out of Stock Only</option>
                </select>

                <!-- Filter button -->
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Filter
                </button>

                <!-- Reset button -->
                @if(request()->hasAny(['search', 'stock_status', 'per_page']))
                    <a 
                        href="{{ route('admin.wishlist.index') }}" 
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
                        <th class="px-5 py-4">Saved Product</th>
                        <th class="px-5 py-4">Price</th>
                        <th class="px-5 py-4">Inventory Stock</th>
                        <th class="px-5 py-4">Saved On</th>
                        <th class="px-5 py-4">Last Notified</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($wishlists as $item)
                        @php
                            $p = $item->product;
                            $u = $item->user;
                            $img = $p ? ($p->image ?: ($p->images->first()->image_path ?? null)) : null;
                            $imgUrl = $img ? (str_starts_with($img, 'http') || str_starts_with($img, 'data:') ? $img : asset('storage/' . $img)) : null;
                            $inStock = $p && $p->stock > 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <!-- Customer Info -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center text-sm shrink-0 border border-emerald-200 dark:border-emerald-800/80 shadow-sm">
                                        {{ strtoupper(substr($u->name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        @if($u)
                                            <a href="{{ route('admin.customers.show', $u->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors block truncate text-xs">
                                                {{ $u->name }}
                                            </a>
                                            <span class="text-slate-400 font-mono text-[11px] block truncate">{{ $u->email }}</span>
                                        @else
                                            <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">Guest Customer</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Product Info -->
                            <td class="px-5 py-4 max-w-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-11 w-11 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 overflow-hidden flex items-center justify-center">
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="Product" class="h-full w-full object-cover">
                                        @else
                                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        @endif
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        @if($p)
                                            <a href="{{ route('admin.products.edit', $p->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors block truncate text-xs">
                                                {{ $p->name }}
                                            </a>
                                            <span class="font-mono text-[10px] font-semibold text-slate-400 block">
                                                {{ $p->sku ?: '#PROD-' . $p->id }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Product deleted</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-5 py-4">
                                @if($p)
                                    <div class="space-y-0.5">
                                        <span class="font-extrabold text-slate-900 dark:text-white text-xs block">
                                            ${{ number_format($p->sale_price && $p->sale_price < $p->price ? $p->sale_price : $p->price, 2) }}
                                        </span>
                                        @if($p->sale_price && $p->sale_price < $p->price)
                                            <span class="text-[10px] text-slate-400 line-through">${{ number_format($p->price, 2) }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>

                            <!-- Stock -->
                            <td class="px-5 py-4">
                                @if($p)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold {{ $inStock ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $inStock ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $inStock ? 'In Stock (' . $p->stock . ')' : 'Out of Stock' }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>

                            <!-- Added Date -->
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $item->created_at ? $item->created_at->format('M d, Y') : '—' }}
                            </td>

                            <!-- Last Notified -->
                            <td class="px-5 py-4">
                                @if($item->last_notified_at)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800" title="{{ $item->last_notified_at->format('M d, Y H:i') }}">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                        {{ $item->last_notified_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Not notified</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    <!-- Send Notification Button -->
                                    @if($u && $p)
                                        <button 
                                            type="button" 
                                            @click="openModal({{ $item->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($p->name) }}', '{{ route('admin.wishlist.notify', $item->id) }}')"
                                            class="h-8 px-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 hover:bg-amber-600 hover:text-white dark:hover:bg-amber-500 flex items-center gap-1.5 transition-all text-xs font-bold border border-amber-200 dark:border-amber-800 cursor-pointer shadow-sm"
                                            title="Send Notification to Customer"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                            <span>Notify</span>
                                        </button>
                                    @endif

                                    <!-- View / Edit Product -->
                                    @if($p)
                                        <a 
                                            href="{{ route('admin.products.edit', $p->id) }}" 
                                            class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-400 flex items-center justify-center transition-all"
                                            title="View Product"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                    @endif

                                    <!-- Delete Wishlist Item -->
                                    <form method="POST" action="{{ route('admin.wishlist.destroy', $item->id) }}" onsubmit="return confirm('Remove this product from customer wishlist?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer"
                                            title="Remove Item"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 italic">No wishlist records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE & TABLET CARDS VIEW (Visible on Mobile/Tablet < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
            @forelse($wishlists as $item)
                @php
                    $p = $item->product;
                    $u = $item->user;
                    $img = $p ? ($p->image ?: ($p->images->first()->image_path ?? null)) : null;
                    $imgUrl = $img ? (str_starts_with($img, 'http') || str_starts_with($img, 'data:') ? $img : asset('storage/' . $img)) : null;
                    $inStock = $p && $p->stock > 0;
                @endphp
                <div class="bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 space-y-4 flex flex-col justify-between shadow-sm">
                    
                    <div class="space-y-3">
                        <!-- Top Row: Customer Info & Stock Pill -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center text-sm shrink-0 border border-emerald-200 dark:border-emerald-800/80 shadow-sm">
                                    {{ strtoupper(substr($u->name ?? 'G', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    @if($u)
                                        <a href="{{ route('admin.customers.show', $u->id) }}" class="font-bold text-slate-900 dark:text-white text-sm truncate block hover:text-emerald-600">
                                            {{ $u->name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 font-mono block truncate">{{ $u->email }}</span>
                                    @else
                                        <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Guest Customer</span>
                                    @endif
                                </div>
                            </div>

                            @if($p)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold shrink-0 {{ $inStock ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $inStock ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $inStock ? 'In Stock (' . $p->stock . ')' : 'Out of Stock' }}
                                </span>
                            @endif
                        </div>

                        <!-- Product Preview Card -->
                        @if($p)
                            <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-100 dark:border-slate-800/80 flex items-center gap-3">
                                <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 overflow-hidden flex items-center justify-center">
                                    @if($imgUrl)
                                        <img src="{{ $imgUrl }}" alt="Product" class="h-full w-full object-cover">
                                    @else
                                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 space-y-0.5">
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase">Wishlisted Product</div>
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="font-bold text-slate-900 dark:text-white text-xs truncate block hover:text-emerald-600">
                                        {{ $p->name }}
                                    </a>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[10px] text-slate-400">{{ $p->sku ?: '#PROD-' . $p->id }}</span>
                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-xs">
                                            ${{ number_format($p->sale_price && $p->sale_price < $p->price ? $p->sale_price : $p->price, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer: Date & Actions -->
                    <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-2">
                        <div class="space-y-0.5">
                            <span class="text-[10px] text-slate-400 font-mono block">
                                Saved {{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}
                            </span>
                            @if($item->last_notified_at)
                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold block">
                                    Notified {{ $item->last_notified_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5">
                            @if($u && $p)
                                <button 
                                    type="button" 
                                    @click="openModal({{ $item->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($p->name) }}', '{{ route('admin.wishlist.notify', $item->id) }}')"
                                    class="px-2.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 hover:bg-amber-600 hover:text-white text-xs font-bold flex items-center gap-1 transition-all border border-amber-200 dark:border-amber-800"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                    <span>Notify</span>
                                </button>
                            @endif

                            @if($p)
                                <a 
                                    href="{{ route('admin.products.edit', $p->id) }}" 
                                    class="px-3 py-1.5 rounded-xl bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-600 hover:text-white text-xs font-semibold flex items-center gap-1 transition-all"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <span>Product</span>
                                </a>
                            @endif

                            <form method="POST" action="{{ route('admin.wishlist.destroy', $item->id) }}" onsubmit="return confirm('Remove this wishlist item?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="p-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all border border-rose-200 dark:border-rose-800"
                                    title="Delete"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 italic">No wishlist records found.</div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($wishlists->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $wishlists->links() }}
            </div>
        @endif

    </div>

    <!-- NOTIFICATION MODAL -->
    <div 
        x-show="showModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5"
            @click.away="showModal = false"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200 dark:border-amber-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Send Customer Notification</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Target: <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="customerName"></span></p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
            </div>

            <!-- Form -->
            <form :action="formAction" method="POST" class="space-y-4">
                @csrf

                <!-- Preset Template Buttons -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Quick Message Templates</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button 
                            type="button" 
                            @click="applyPreset('discount')" 
                            :class="preset === 'discount' ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-600 dark:text-slate-400'"
                            class="p-2.5 rounded-xl border text-xs font-semibold text-left transition-all cursor-pointer flex items-center gap-2"
                        >
                            <span>🏷️ Special Discount</span>
                        </button>
                        <button 
                            type="button" 
                            @click="applyPreset('back_in_stock')" 
                            :class="preset === 'back_in_stock' ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-600 dark:text-slate-400'"
                            class="p-2.5 rounded-xl border text-xs font-semibold text-left transition-all cursor-pointer flex items-center gap-2"
                        >
                            <span>📦 Back in Stock</span>
                        </button>
                        <button 
                            type="button" 
                            @click="applyPreset('reminder')" 
                            :class="preset === 'reminder' ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-600 dark:text-slate-400'"
                            class="p-2.5 rounded-xl border text-xs font-semibold text-left transition-all cursor-pointer flex items-center gap-2"
                        >
                            <span>⏰ Wishlist Reminder</span>
                        </button>
                        <button 
                            type="button" 
                            @click="applyPreset('custom')" 
                            :class="preset === 'custom' ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-600 dark:text-slate-400'"
                            class="p-2.5 rounded-xl border text-xs font-semibold text-left transition-all cursor-pointer flex items-center gap-2"
                        >
                            <span>✏️ Custom Message</span>
                        </button>
                    </div>
                </div>

                <!-- Subject Input -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Notification Subject</label>
                    <input 
                        type="text" 
                        name="subject" 
                        x-model="subject" 
                        required 
                        placeholder="e.g. Special Discount on your wishlisted product!"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition-colors"
                    >
                </div>

                <!-- Message Textarea -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Message Body</label>
                    <textarea 
                        name="message" 
                        x-model="message" 
                        rows="4" 
                        required 
                        placeholder="Type your message to the customer..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition-colors"
                    ></textarea>
                </div>

                <!-- Channels Selection -->
                <div class="space-y-2 pt-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Notification Delivery Channels</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="send_email" value="1" checked class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4">
                            <span>Email Notification</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="send_chat" value="1" checked class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4">
                            <span>Live Chat / In-App Message</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button 
                        type="button" 
                        @click="showModal = false" 
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-all"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-600/20 cursor-pointer flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        <span>Send Notification</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
function wishlistNotifier() {
    return {
        showModal: false,
        activeItemId: null,
        customerName: '',
        productName: '',
        formAction: '',
        preset: 'discount',
        subject: '',
        message: '',
        openModal(itemId, customerName, productName, actionUrl) {
            this.activeItemId = itemId;
            this.customerName = customerName;
            this.productName = productName;
            this.formAction = actionUrl;
            this.showModal = true;
            this.applyPreset('discount');
        },
        applyPreset(presetName) {
            this.preset = presetName;
            const cName = this.customerName || 'Customer';
            const pName = this.productName || 'product';

            if (presetName === 'discount') {
                this.subject = `Special Offer on ${pName}!`;
                this.message = `Hi ${cName},\n\nGreat news! The product "${pName}" in your wishlist is now available with a special promotional discount. Complete your purchase now while stock lasts!`;
            } else if (presetName === 'back_in_stock') {
                this.subject = `${pName} is now Back in Stock!`;
                this.message = `Hi ${cName},\n\n"${pName}" from your wishlist is back in stock! Grab yours today before it sells out again.`;
            } else if (presetName === 'reminder') {
                this.subject = `Don't forget your wishlisted item: ${pName}`;
                this.message = `Hi ${cName},\n\nYou saved "${pName}" in your wishlist. Order today and have it delivered directly to your doorstep!`;
            } else if (presetName === 'custom') {
                this.subject = `Update regarding ${pName}`;
                this.message = `Hi ${cName},\n\n`;
            }
        }
    };
}
</script>
@endsection
