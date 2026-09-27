@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16" x-data="{ viewMode: 'cards' }">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-500 dark:text-slate-400">Catalog</span>
            <span>&gt;</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Brands Showcase</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Brands Showcase</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $brands->total() }} Brands
                </span>
            </div>
            
            <div class="flex items-center gap-2.5">
                <!-- View Mode Switcher -->
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                    <button 
                        @click="viewMode = 'cards'" 
                        :class="viewMode === 'cards' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        <span>Card View</span>
                    </button>
                    <button 
                        @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        <span>Table View</span>
                    </button>
                </div>

                <a 
                    href="{{ route('admin.brands.create') }}" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer w-fit"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>+ Add Brand</span>
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
            <button @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
        </div>
    @endif

    <!-- Metrics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Brands -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Brands</span>
                <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386l4.412-2.59c.783-.46.994-1.482.476-2.185L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <!-- Active Brands -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Active Brands</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['active'] ?? 0) }}</div>
        </div>

        <!-- Inactive Brands -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Inactive Brands</span>
                <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-700 dark:text-slate-300">{{ number_format($stats['inactive'] ?? 0) }}</div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 sm:max-w-md">
                <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search brands by name, sector or slug..." 
                    class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                >
            </div>

            <div class="flex items-center gap-2">
                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
                >
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>

                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Filter
                </button>
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('admin.brands.index') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition-colors" title="Clear search">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 1. RICH CARD SHOWCASE GRID (Clean for Light & Dark Mode) -->
    <div x-show="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($brands as $brand)
            @php
                $imgSrc = $brand->logo ? (str_starts_with($brand->logo, 'http') || str_starts_with($brand->logo, 'data:') ? $brand->logo : asset('storage/' . $brand->logo)) : null;
                $capabilities = is_array($brand->key_capabilities) ? $brand->key_capabilities : (is_string($brand->key_capabilities) ? json_decode($brand->key_capabilities, true) : []);
                if (empty($capabilities)) {
                    $capabilities = [
                        'Industrial Heavy Diesel Generators',
                        'Custom Acoustic Soundproof Canopies',
                        'Auto-Synchronizing Multi-Genset Panels',
                        'Automatic Main Failure (AMF) Logic',
                    ];
                }
            @endphp
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-[28px] p-6 shadow-sm hover:shadow-xl dark:hover:shadow-2xl dark:hover:shadow-emerald-950/10 hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between relative group">
                
                <!-- Admin Floating Action Controls on Hover/Header -->
                <div class="absolute top-4 right-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-slate-200 dark:border-slate-700 rounded-2xl shadow-lg p-1 flex items-center gap-1">
                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="p-1.5 text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-700/60 rounded-xl transition-colors" title="Edit Brand Card">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.brands.toggle-status', $brand->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 {{ $brand->status ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }} hover:bg-slate-100 dark:hover:bg-slate-700/60 rounded-xl transition-colors cursor-pointer" title="Toggle Status">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" onsubmit="return confirm('Delete brand {{ addslashes($brand->name) }}?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors cursor-pointer" title="Delete Brand">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>

                <div>
                    <!-- Top Tags Row -->
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <!-- Sector / Category Tag -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-extrabold tracking-wide uppercase bg-sky-50 dark:bg-sky-950/50 text-[#0f3460] dark:text-sky-300 border border-sky-100 dark:border-sky-900/60">
                            <svg class="h-3.5 w-3.5 text-[#0f3460] dark:text-sky-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ $brand->category_tag ?: 'HEAVY POWER GENERATION' }}</span>
                        </div>

                        <!-- Flagship / Feature Badge -->
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-black bg-[#f59e0b] dark:bg-amber-500 text-slate-950 shadow-sm">
                            <span>{{ $brand->badge ?: 'Flagship Brand' }}</span>
                        </div>
                    </div>

                    <!-- Brand Header Row (Logo + Subtitle + Title) -->
                    <div class="flex items-start gap-3.5 mb-3.5">
                        <!-- Brand Icon Box -->
                        <div class="h-12 w-12 rounded-2xl bg-[#0e2a47] dark:bg-slate-950 border border-blue-900/40 dark:border-slate-800 shrink-0 flex items-center justify-center shadow-md overflow-hidden p-1.5">
                            @if($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $brand->name }}" class="h-full w-full object-contain">
                            @else
                                <svg class="h-6 w-6 text-[#f59e0b] dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 2L3 14h8v8l10-12h-8l0-8z"/>
                                </svg>
                            @endif
                        </div>

                        <!-- Subtitle + Name -->
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-extrabold text-[#b45309] dark:text-amber-400 uppercase tracking-wider leading-tight">
                                {{ $brand->sub_title ?: 'GENERATORS & SYNCHRONIZATION' }}
                            </p>
                            <h3 class="text-xl font-black text-[#0f2e5a] dark:text-white tracking-tight leading-tight mt-0.5">
                                {{ $brand->name }}
                            </h3>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                        {{ $brand->description ?: 'Heavy-duty continuous & standby industrial diesel generators, custom soundproof acoustic canopies, and automated load-sharing synchronization systems.' }}
                    </p>

                    <!-- Capacity Range Box -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs mb-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Capacity Range:</span>
                        <span class="font-extrabold text-[#0f2e5a] dark:text-sky-300">{{ $brand->capacity_range ?: '50kVA - 3000kVA' }}</span>
                    </div>

                    <!-- Warranty Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50/90 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 mb-4 w-fit">
                        <svg class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ $brand->warranty_text ?: 'Full Factory Warranty & AMC' }}</span>
                    </div>

                    <!-- Key Capabilities List -->
                    <div class="space-y-2 mb-6">
                        <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-wider">KEY CAPABILITIES:</p>
                        <ul class="space-y-1.5 text-xs text-slate-700 dark:text-slate-200">
                            @foreach(array_slice($capabilities, 0, 4) as $cap)
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="font-medium">{{ $cap }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Inquiry CTA Button -->
                <div class="pt-2">
                    <a 
                        href="{{ route('admin.brands.edit', $brand->id) }}" 
                        class="w-full py-3.5 px-4 rounded-2xl bg-[#0e2a47] hover:bg-[#163a63] dark:bg-slate-800 dark:hover:bg-slate-700/90 text-[#f59e0b] dark:text-amber-300 border border-transparent dark:border-slate-700/80 font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-blue-950/20 dark:shadow-none transition-all cursor-pointer"
                    >
                        <span>{{ $brand->cta_text ?: 'Inquire About Heavy Power Generation' }}</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8">
                <div class="max-w-md mx-auto space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">No Brands Found</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Create your first brand card with specs, capabilities, and inquiry actions.</p>
                    <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-md">
                        + Add Brand Card
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- 2. DESKTOP DATA TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Brand & Sector</th>
                        <th class="px-5 py-4">Badge</th>
                        <th class="px-5 py-4">Capacity Range</th>
                        <th class="px-5 py-4">Warranty</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($brands as $brand)
                        @php
                            $imgSrc = $brand->logo ? (str_starts_with($brand->logo, 'http') || str_starts_with($brand->logo, 'data:') ? $brand->logo : asset('storage/' . $brand->logo)) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <!-- Brand Name & Logo -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="h-11 w-11 rounded-xl overflow-hidden bg-[#0e2a47] dark:bg-slate-950 border border-blue-900/40 dark:border-slate-800 shrink-0 flex items-center justify-center p-1.5">
                                        @if($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $brand->name }}" class="h-full w-full object-contain">
                                        @else
                                            <svg class="h-5 w-5 text-[#f59e0b] dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M13 2L3 14h8v8l10-12h-8l0-8z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-xs space-y-0.5">
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors block truncate">
                                            {{ $brand->name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                            <span class="font-mono text-emerald-600 dark:text-emerald-400 font-semibold">{{ $brand->category_tag ?: 'POWER' }}</span>
                                            <span>•</span>
                                            <span>/{{ $brand->slug }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Badge -->
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-[#f59e0b] dark:bg-amber-500 text-slate-950">
                                    {{ $brand->badge ?: 'Flagship' }}
                                </span>
                            </td>

                            <!-- Capacity Range -->
                            <td class="px-5 py-4 font-extrabold text-[#0f2e5a] dark:text-sky-300">
                                {{ $brand->capacity_range ?: '50kVA - 3000kVA' }}
                            </td>

                            <!-- Warranty -->
                            <td class="px-5 py-4 text-emerald-700 dark:text-emerald-300 font-semibold">
                                {{ $brand->warranty_text ?: 'Full Factory Warranty & AMC' }}
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.brands.toggle-status', $brand->id) }}" class="inline">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold cursor-pointer transition-all hover:opacity-80 {{ $brand->status ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}"
                                        title="Click to toggle status"
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full {{ $brand->status ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $brand->status ? 'Active' : 'Inactive' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a 
                                        href="{{ route('admin.brands.edit', $brand->id) }}" 
                                        class="p-2 rounded-xl text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                        title="Edit Brand Card"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form 
                                        method="POST" 
                                        action="{{ route('admin.brands.destroy', $brand->id) }}" 
                                        onsubmit="return confirm('Are you sure you want to delete brand: {{ addslashes($brand->name) }}?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-2 rounded-xl text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                            title="Delete Brand Card"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                No brands found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($brands->hasPages())
        <div class="pt-2">
            {{ $brands->links() }}
        </div>
    @endif

</div>
@endsection
