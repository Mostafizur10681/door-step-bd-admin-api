@extends('layouts.admin')

@section('content')
<div class="space-y-6 pb-20 max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">

    <!-- Top Bar & Breadcrumbs -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <x-breadcrumbs :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Banners & Sliders']
                ]" />
                <div class="flex flex-wrap items-center gap-2 pt-0.5">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Banners &amp; Sliders Management
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Storefront Synced
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="http://localhost:3000" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Storefront</span>
                </a>
                <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Add New Banner</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1">
                <a href="{{ route('admin.banners.index', array_merge(request()->except(['status', 'page']))) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !request()->filled('status') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    All Status ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.banners.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    Active ({{ $activeCount }})
                </a>
                <a href="{{ route('admin.banners.index', array_merge(request()->except(['status', 'page']), ['status' => 'inactive'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'inactive' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    Inactive ({{ $inactiveCount }})
                </a>
            </div>

            <form method="GET" action="{{ route('admin.banners.index') }}" class="flex flex-wrap items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <!-- Location Selector -->
                <select name="location" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500">
                    <option value="">All Placements</option>
                    @foreach($menuLocations as $locKey => $locLabel)
                        <option value="{{ $locKey }}" {{ request('location') === $locKey ? 'selected' : '' }}>{{ $locLabel }}</option>
                    @endforeach
                </select>

                <div class="relative w-full sm:w-52">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search banners..." class="w-full pl-8 pr-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>
                @if(request('search') || request('location') || request('status'))
                    <a href="{{ route('admin.banners.index') }}" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-xs font-semibold">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- ═══ 1. DESKTOP DATA TABLE (Visible on Screens >= 1024px) ═══ -->
    <div class="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100/90 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700/80">
                    <tr>
                        <th class="py-3.5 px-4 w-20 text-center text-slate-600 dark:text-slate-300">Order</th>
                        <th class="py-3.5 px-4 w-52 text-slate-600 dark:text-slate-300">Artwork &amp; Preview</th>
                        <th class="py-3.5 px-4 text-slate-600 dark:text-slate-300">Headline &amp; Copy</th>
                        <th class="py-3.5 px-4 w-40 text-slate-600 dark:text-slate-300">CTA &amp; Target Link</th>
                        <th class="py-3.5 px-4 w-36 text-center text-slate-600 dark:text-slate-300">Placement</th>
                        <th class="py-3.5 px-4 w-24 text-center text-slate-600 dark:text-slate-300">Status</th>
                        <th class="py-3.5 px-4 w-28 text-right text-slate-600 dark:text-slate-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                    @forelse($banners as $index => $banner)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            
                            <!-- 1. Order & Reorder Arrows -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-black text-xs border border-slate-200 dark:border-slate-700">
                                        {{ $banner->order }}
                                    </span>
                                    <div class="flex flex-col gap-0.5">
                                        <form method="POST" action="{{ route('admin.banners.reorder', $banner->id) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" title="Move Up" class="p-0.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition cursor-pointer">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" /></svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.banners.reorder', $banner->id) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" title="Move Down" class="p-0.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition cursor-pointer">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Banner Artwork Preview -->
                            <td class="py-3.5 px-4">
                                <div class="w-48 h-16 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-2xs relative flex items-center shrink-0" style="background-color: {{ $banner->bg_color ?: '#002B49' }}">
                                    @php
                                        $desktopImg = $banner->image ? (str_starts_with($banner->image, 'data:') || str_starts_with($banner->image, 'http') || str_starts_with($banner->image, '/') ? $banner->image : asset('storage/' . $banner->image)) : '/hero_honey.png';
                                    @endphp
                                    <img 
                                        src="{{ $desktopImg }}" 
                                        class="absolute inset-0 w-full h-full object-cover"
                                        onerror="this.onerror=null; this.src='/hero_honey.png';"
                                    >
                                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
                                    <div class="relative z-10 px-2.5 py-1 text-white space-y-0.5 max-w-[130px]">
                                        @if($banner->badge)
                                            <span class="inline-block bg-[#FF6600] text-white text-[7px] font-black px-1.5 py-0.2 rounded-full uppercase truncate">
                                                {{ $banner->badge }}
                                            </span>
                                        @endif
                                        <p class="text-[9px] font-black uppercase text-white leading-tight truncate">
                                            {{ $banner->title_line1 ?: $banner->title }}
                                        </p>
                                    </div>

                                    @if($banner->mobile_image)
                                        <span class="absolute bottom-1 right-1 bg-black/60 backdrop-blur-xs text-amber-300 text-[8px] font-bold px-1 py-0.2 rounded border border-amber-300/30">
                                            📱 Mobile
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 3. Title & Headline Copy -->
                            <td class="py-3.5 px-4 min-w-[200px]">
                                <div class="space-y-0.5">
                                    @if($banner->badge)
                                        <span class="text-[#FF6600] text-[9px] font-black tracking-wider uppercase block">
                                            {{ $banner->badge }}
                                        </span>
                                    @endif
                                    <div class="font-black text-xs text-slate-900 dark:text-white leading-tight">
                                        @if($banner->title_line1 || $banner->title_line2)
                                            {{ $banner->title_line1 }} <span class="text-amber-600 dark:text-amber-400">{{ $banner->title_line2 }}</span>
                                        @else
                                            {{ $banner->title }}
                                        @endif
                                    </div>
                                    @if($banner->subtitle)
                                        <div class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold">
                                            {{ $banner->subtitle }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. CTA Button & Target Link -->
                            <td class="py-3.5 px-4 font-mono text-[11px] text-emerald-600 dark:text-emerald-400">
                                @if($banner->cta_text)
                                    <span class="inline-flex items-center gap-1 bg-[#FF6600] text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase shadow-2xs">
                                        {{ $banner->cta_text }} →
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] text-slate-400 dark:text-slate-500 font-sans italic">
                                        No button (Image only)
                                    </span>
                                @endif
                                <span class="truncate block max-w-[150px] text-[10px] text-slate-500 dark:text-slate-400 pt-0.5" title="{{ $banner->cta_link ?: '/' }}">
                                    {{ $banner->cta_link ?: '/' }}
                                </span>
                            </td>

                            <!-- 5. Placement Location -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $menuLocations[$banner->menu_location] ?? ($banner->menu_location ?: 'Hero Slider') }}
                                </span>
                            </td>

                            <!-- 6. Status Toggle -->
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.banners.toggle-status', $banner->id) }}">
                                    @csrf
                                    <button type="submit" title="Click to toggle status" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border transition cursor-pointer {{ $banner->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:border-emerald-800 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' : 'border-slate-200 bg-slate-100 text-slate-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                                        {{ $banner->is_active ? '● Active' : '○ Inactive' }}
                                    </button>
                                </form>
                            </td>

                            <!-- 7. Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="p-1.5 text-slate-400 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Edit Banner">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 transition cursor-pointer" title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400 dark:text-slate-500">
                                <div class="space-y-2">
                                    <p class="text-sm font-semibold">No banners found.</p>
                                    <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition">
                                        + Create First Banner
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ 2. TABLET & MOBILE CARD VIEW (Visible on Screens < 1024px) ═══ -->
    <div class="lg:hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
        @forelse($banners as $banner)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                
                <!-- Card Header -->
                <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 bg-slate-50 dark:bg-slate-950/60">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center text-[11px] font-black shrink-0 border border-slate-300 dark:border-slate-700">
                            #{{ $banner->order }}
                        </span>
                        <h3 class="font-extrabold text-xs text-slate-900 dark:text-white truncate">
                            {{ $banner->title ?: ($banner->title_line1 . ' ' . $banner->title_line2) }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" onsubmit="return confirm('Delete this banner?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Banner Thumbnail Preview -->
                <div class="h-28 sm:h-36 relative overflow-hidden flex items-center px-4" style="background-color: {{ $banner->bg_color ?: '#002B49' }}">
                    @php
                        $desktopImg = $banner->image ? (str_starts_with($banner->image, 'data:') || str_starts_with($banner->image, 'http') || str_starts_with($banner->image, '/') ? $banner->image : asset('storage/' . $banner->image)) : '/hero_honey.png';
                    @endphp
                    <img 
                        src="{{ $desktopImg }}" 
                        class="absolute inset-0 w-full h-full object-cover"
                        onerror="this.onerror=null; this.src='/hero_honey.png';"
                    >
                    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-transparent"></div>
                    <div class="relative z-10 text-white space-y-1">
                        @if($banner->badge)
                            <span class="inline-block bg-[#FF6600] text-white text-[8px] font-black px-2 py-0.2 rounded-full uppercase">
                                {{ $banner->badge }}
                            </span>
                        @endif
                        <h4 class="text-sm font-black uppercase text-white leading-tight">
                            {{ $banner->title_line1 ?: $banner->title }}
                            @if($banner->title_line2)
                                <span class="text-amber-300 block text-xs">{{ $banner->title_line2 }}</span>
                            @endif
                        </h4>
                    </div>
                </div>

                <!-- Card Footer Info -->
                <div class="p-3 bg-slate-50/50 dark:bg-slate-950/40 space-y-2 text-[11px]">
                    @if($banner->subtitle)
                        <div class="text-rose-600 dark:text-rose-400 font-bold text-[10px] truncate">
                            {{ $banner->subtitle }}
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 dark:border-slate-800">
                        @if($banner->cta_text)
                            <span class="inline-flex items-center gap-1 bg-[#FF6600] text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase">
                                {{ $banner->cta_text }}
                            </span>
                        @else
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">
                                No button
                            </span>
                        @endif

                        <form method="POST" action="{{ route('admin.banners.toggle-status', $banner->id) }}">
                            @csrf
                            <button type="submit" class="px-2 py-0.5 text-[10px] font-bold rounded-lg border transition cursor-pointer {{ $banner->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:border-emerald-800 dark:text-emerald-300' : 'border-slate-200 bg-slate-100 text-slate-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400' }}">
                                {{ $banner->is_active ? '● Active' : '○ Inactive' }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                <p class="text-xs text-slate-500">No banners found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $banners->links() }}
    </div>

</div>
@endsection
