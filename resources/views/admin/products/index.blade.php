@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-500">Product</span>
            <span>&gt;</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">All Products</span>
        </div>
        <div class="pt-1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">All Products</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage inventory items, review prices, adjust stock, and update catalog info.</p>
        </div>
    </div>

    <!-- Green Tip Banner -->
    <div class="bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl p-4 flex items-start gap-3 text-xs text-emerald-900 dark:text-emerald-200">
        <div class="h-6 w-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shrink-0 text-emerald-600 dark:text-emerald-400">
            💡
        </div>
        <div class="space-y-0.5">
            <span class="font-bold text-emerald-800 dark:text-emerald-300">Tip search by Product ID:</span>
            <span class="text-emerald-700 dark:text-emerald-400/90">Each product is provided with a unique ID. Use it to filter individual items instantly.</span>
        </div>
    </div>

    <!-- Filter & Control Toolbar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Left: Showing Entries -->
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

            <!-- Right: Search, Category, Status, Sort & Add Button -->
            <div class="flex flex-wrap items-center gap-2.5 flex-1 lg:justify-end">
                
                <!-- Search Bar -->
                <div class="relative flex-1 sm:w-64 sm:flex-initial">
                    <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search by Product Name or Product ID" 
                        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                </div>

                <!-- Category Filter -->
                <select 
                    name="category_id" 
                    onchange="this.form.submit()" 
                    class="px-3 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="px-3 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                >
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>

                <!-- Sorting Filter -->
                <select 
                    name="sort" 
                    onchange="this.form.submit()" 
                    class="px-3 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                >
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="price_low_high" {{ request('sort') === 'price_low_high' ? 'selected' : '' }}>Price (Low to High)</option>
                    <option value="price_high_low" {{ request('sort') === 'price_high_low' ? 'selected' : '' }}>Price (High to Low)</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                </select>

                @if(request()->hasAny(['search', 'category_id', 'status', 'sort', 'per_page']))
                    <a href="{{ route('admin.products.index') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition-colors" title="Clear filters">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif

                <!-- Add Product Button -->
                <a 
                    href="{{ route('admin.products.create') }}" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>+ Add Product</span>
                </a>

            </div>
        </form>
    </div>

    <!-- 1. DESKTOP DATA TABLE (Visible on Desktop Screen >= 1024px) -->
    <div class="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Product</th>
                        <th class="px-5 py-4">Product ID</th>
                        <th class="px-5 py-4">Color</th>
                        <th class="px-5 py-4">Price</th>
                        <th class="px-5 py-4">Quantity</th>
                        <th class="px-5 py-4">Sale</th>
                        <th class="px-5 py-4">Stock</th>
                        <th class="px-5 py-4">Start Date</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($products as $product)
                        @php
                            $img = $product->image ?: ($product->images->first()->image_path ?? '');
                            $imgSrc = $img ? (str_starts_with($img, 'http') || str_starts_with($img, 'data:') ? $img : asset('storage/' . $img)) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <!-- Product thumbnail & title -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-11 w-11 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 overflow-hidden flex items-center justify-center">
                                        @if($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-xs space-y-0.5">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors block truncate">
                                            {{ $product->name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                            @if($product->organic)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">Organic</span>
                                            @endif
                                            @if($product->featured)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">Featured</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Product ID -->
                            <td class="px-5 py-4 font-mono font-medium text-slate-600 dark:text-slate-400">
                                #{{ $product->sku ?: $product->id }}
                            </td>

                            <!-- Color Column -->
                            <td class="px-5 py-4">
                                @if($product->color)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        <span class="h-2.5 w-2.5 rounded-full border border-slate-300 dark:border-slate-600" style="background-color: {{ $product->color }}"></span>
                                        <span>{{ $product->color }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Price -->
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">
                                ${{ number_format($product->price, 2) }}
                            </td>

                            <!-- Quantity -->
                            <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $product->stock }} <span class="text-[10px] text-slate-400 font-normal">{{ $product->unit ?: 'pcs' }}</span>
                            </td>

                            <!-- Sale Price -->
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-400">
                                @if($product->sale_price && $product->sale_price > 0)
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($product->sale_price, 2) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Stock Status Badge -->
                            <td class="px-5 py-4">
                                @if($product->stock > 0 && $product->stock_status !== 'out-of-stock')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        In Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Out of stock
                                    </span>
                                @endif
                            </td>

                            <!-- Start Date -->
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $product->created_at ? $product->created_at->format('M d, Y') : '—' }}
                            </td>

                            <!-- Action -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a 
                                        href="{{ route('admin.products.edit', $product->id) }}" 
                                        class="p-2 text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all"
                                        title="Edit Product"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-all cursor-pointer"
                                            title="Delete Product"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="h-14 w-14 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-300 dark:text-slate-600">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No products found matching filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. MOBILE & TABLET RESPONSIVE CARDS VIEW (Visible on Mobile & Tablet < 1024px) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
        @forelse($products as $product)
            @php
                $img = $product->image ?: ($product->images->first()->image_path ?? '');
                $imgSrc = $img ? (str_starts_with($img, 'http') || str_starts_with($img, 'data:') ? $img : asset('storage/' . $img)) : null;
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-emerald-500/40 transition-all">
                
                <!-- Card Header with Image & Title -->
                <div class="flex items-start gap-3.5">
                    <div class="h-16 w-16 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 overflow-hidden flex items-center justify-center">
                        @if($imgSrc)
                            <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono font-medium text-slate-500 dark:text-slate-400">#{{ $product->sku ?: $product->id }}</span>
                            @if($product->stock > 0 && $product->stock_status !== 'out-of-stock')
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50">
                                    In Stock ({{ $product->stock }})
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/50 dark:border-rose-800/50">
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                        
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="font-bold text-sm text-slate-900 dark:text-white truncate block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                            {{ $product->name }}
                        </a>

                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            @if($product->organic)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">Organic</span>
                            @endif
                            @if($product->featured)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">Featured</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Details Grid -->
                <div class="grid grid-cols-3 gap-2 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/80 text-xs">
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Price</div>
                        <div class="font-bold text-slate-900 dark:text-white mt-0.5">${{ number_format($product->price, 2) }}</div>
                        @if($product->sale_price && $product->sale_price > 0)
                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">Sale: ${{ number_format($product->sale_price, 2) }}</div>
                        @endif
                    </div>

                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Color</div>
                        <div class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5 flex items-center gap-1">
                            @if($product->color)
                                <span class="h-2 w-2 rounded-full border border-slate-300 dark:border-slate-600" style="background-color: {{ $product->color }}"></span>
                                <span class="truncate">{{ $product->color }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Created</div>
                        <div class="font-semibold text-slate-600 dark:text-slate-400 mt-0.5 truncate">
                            {{ $product->created_at ? $product->created_at->format('M d, Y') : '—' }}
                        </div>
                    </div>
                </div>

                <!-- Card Actions Footer -->
                <div class="flex items-center justify-end gap-2 pt-1">
                    <a 
                        href="{{ route('admin.products.edit', $product->id) }}" 
                        class="flex-1 py-2 text-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors"
                    >
                        Edit Product
                    </a>

                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="px-3.5 py-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                            title="Delete"
                        >
                            Delete
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 text-center text-slate-400">
                <p class="text-xs font-semibold text-slate-500">No products found matching filters.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination & Meta -->
    @if($products->hasPages() || $products->total() > 0)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                Showing <span class="font-bold text-slate-800 dark:text-slate-200">{{ $products->firstItem() ?? 0 }}</span> to <span class="font-bold text-slate-800 dark:text-slate-200">{{ $products->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800 dark:text-slate-200">{{ $products->total() }}</span> products
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
