@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Partners & Brands</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Partners & Brands</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $partners->total() }} Partners
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.partners.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Add Partner</span>
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

    <!-- Metrics Summary Cards -->
    <div class="grid grid-cols-3 gap-4">
        <!-- Total Partners -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Partners</span>
                <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <!-- Active Partners -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Active</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['active'] ?? 0) }}</div>
        </div>

        <!-- Inactive Partners -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Inactive</span>
                <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-700 dark:text-slate-300">{{ number_format($stats['inactive'] ?? 0) }}</div>
        </div>
    </div>

    <!-- Filter / Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.partners.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
            
            <!-- Search Input -->
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search partner name or website..." 
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
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
            </select>

            <!-- Per Page -->
            <select 
                name="per_page" 
                onchange="this.form.submit()" 
                class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
            >
                <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12 per page</option>
                <option value="24" {{ request('per_page', 24) == 24 ? 'selected' : '' }}>24 per page</option>
                <option value="48" {{ request('per_page', 48) == 48 ? 'selected' : '' }}>48 per page</option>
            </select>

            <!-- Filter button -->
            <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap">
                Filter
            </button>

            <!-- Reset button -->
            @if(request()->hasAny(['search', 'status', 'per_page']))
                <a href="{{ route('admin.partners.index') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl transition-all whitespace-nowrap">
                    Reset
                </a>
            @endif

        </form>
    </div>

    <!-- Partners Grid: Responsive 1 col mobile / 2 col tablet / 3-4 col desktop -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($partners as $partner)
            @php
                $isActive = (bool) $partner->status;
                $logo = $partner->logo ?: $partner->image;
                $logoUrl = $logo
                    ? (str_starts_with($logo, 'http') || str_starts_with($logo, 'data:') ? $logo : asset('storage/' . $logo))
                    : null;
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800/60 transition-all duration-200 flex flex-col group">

                <!-- Logo Area -->
                <div class="relative h-32 bg-gradient-to-br from-slate-50 to-slate-100/60 dark:from-slate-800/60 dark:to-slate-800/30 border-b border-slate-100 dark:border-slate-800 flex items-center justify-center p-5 overflow-hidden">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-105 drop-shadow-sm">
                    @else
                        <div class="flex flex-col items-center gap-1 text-slate-400">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <span class="text-[10px] font-bold uppercase tracking-wider">No Logo</span>
                        </div>
                    @endif

                    <!-- Status floating pill top right -->
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-xl text-[10px] font-bold {{ $isActive ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <!-- Partner Details -->
                <div class="flex flex-col flex-1 p-4 space-y-3">
                    <div class="space-y-1 min-w-0">
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {{ $partner->name }}
                        </h3>
                        @if($partner->website)
                            <a href="{{ $partner->website }}" target="_blank" rel="noopener" class="text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline truncate block leading-relaxed">
                                {{ parse_url($partner->website, PHP_URL_HOST) ?: $partner->website }}
                            </a>
                        @else
                            <span class="text-[11px] text-slate-400 italic">No website</span>
                        @endif

                        @if($partner->description)
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ $partner->description }}</p>
                        @endif
                    </div>

                    <!-- Actions Footer -->
                    <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100 dark:border-slate-800 mt-auto">
                        <span class="text-[10px] text-slate-400 font-mono">
                            {{ $partner->created_at ? $partner->created_at->format('M Y') : '' }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            <!-- Edit -->
                            <a 
                                href="{{ route('admin.partners.edit', $partner->id) }}" 
                                class="h-7 w-7 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-400 flex items-center justify-center transition-all"
                                title="Edit Partner"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>

                            <!-- Visit Website -->
                            @if($partner->website)
                                <a 
                                    href="{{ $partner->website }}" 
                                    target="_blank" 
                                    rel="noopener"
                                    class="h-7 w-7 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-950/60 dark:hover:text-blue-400 flex items-center justify-center transition-all"
                                    title="Visit Website"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            @endif

                            <!-- Delete -->
                            <form method="POST" action="{{ route('admin.partners.destroy', $partner->id) }}" onsubmit="return confirm('Delete \'{{ addslashes($partner->name) }}\'?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="h-7 w-7 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer"
                                    title="Delete Partner"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center space-y-3">
                <div class="h-14 w-14 mx-auto rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No partners found</p>
                    <p class="text-xs text-slate-400 mt-1">Add your first partner brand to get started.</p>
                </div>
                <a href="{{ route('admin.partners.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add First Partner
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($partners->hasPages())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm">
            {{ $partners->links() }}
        </div>
    @endif

</div>
@endsection
