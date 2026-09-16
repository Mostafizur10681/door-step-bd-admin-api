@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.categories.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Categories</a>
            <span>&gt;</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Sub Categories</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Sub Categories</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage second-level category classifications and sub-department assignments.</p>
            </div>
            <a 
                href="{{ route('admin.sub-categories.create') }}" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer w-fit"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>+ Add Sub Category</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.sub-categories.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 sm:max-w-md">
                <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search sub categories..." 
                    class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                >
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <select 
                    name="category_id" 
                    onchange="this.form.submit()" 
                    class="px-3 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                >
                    <option value="">All Parent Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Filter
                </button>

                @if(request()->hasAny(['search', 'category_id']))
                    <a href="{{ route('admin.sub-categories.index') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition-colors" title="Clear filters">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 1. DESKTOP DATA TABLE (Visible on Desktop >= 1024px) -->
    <div class="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Sub Category</th>
                        <th class="px-5 py-4">Parent Category</th>
                        <th class="px-5 py-4">Slug</th>
                        <th class="px-5 py-4">Products</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($subCategories as $sub)
                        @php
                            $imgSrc = $sub->image ? (str_starts_with($sub->image, 'http') || str_starts_with($sub->image, 'data:') ? $sub->image : asset('storage/' . $sub->image)) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <!-- Sub Category & Image -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="h-11 w-11 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 flex items-center justify-center">
                                        @if($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $sub->name }}" class="h-full w-full object-cover">
                                        @else
                                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-xs space-y-0.5">
                                        <a href="{{ route('admin.sub-categories.edit', $sub->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors block truncate">
                                            {{ $sub->name }}
                                        </a>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $sub->description ?: 'No description provided' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Parent Category -->
                            <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $sub->category->name ?? 'Uncategorized' }}
                            </td>

                            <!-- Slug -->
                            <td class="px-5 py-4 font-mono font-medium text-slate-500 dark:text-slate-400">
                                {{ $sub->slug }}
                            </td>

                            <!-- Products Count -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50">
                                    {{ $sub->products_count }} Items
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                @if($sub->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a 
                                        href="{{ route('admin.sub-categories.edit', $sub->id) }}" 
                                        class="p-2 text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all"
                                        title="Edit Sub Category"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.sub-categories.destroy', $sub->id) }}" onsubmit="return confirm('Delete this sub category?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-all cursor-pointer"
                                            title="Delete Sub Category"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="h-14 w-14 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-300 dark:text-slate-600">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No sub categories found</p>
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
        @forelse($subCategories as $sub)
            @php
                $imgSrc = $sub->image ? (str_starts_with($sub->image, 'http') || str_starts_with($sub->image, 'data:') ? $sub->image : asset('storage/' . $sub->image)) : null;
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-emerald-500/40 transition-all">
                
                <!-- Card Header -->
                <div class="flex items-start gap-3.5">
                    <div class="h-14 w-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 overflow-hidden flex items-center justify-center">
                        @if($imgSrc)
                            <img src="{{ $imgSrc }}" alt="{{ $sub->name }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-400 truncate">/{{ $sub->slug }}</span>
                            @if($sub->is_active)
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                    Inactive
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('admin.sub-categories.edit', $sub->id) }}" class="font-bold text-sm text-slate-900 dark:text-white truncate block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                            {{ $sub->name }}
                        </a>
                        <p class="text-[11px] text-slate-400 line-clamp-2">{{ $sub->description ?: 'No description provided' }}</p>
                    </div>
                </div>

                <!-- Details Strip -->
                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/80 text-xs">
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Parent</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 truncate">{{ $sub->category->name ?? 'Uncategorized' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Products</div>
                        <div class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $sub->products_count }} Items</div>
                    </div>
                </div>

                <!-- Actions Footer -->
                <div class="flex items-center justify-end gap-2 pt-1">
                    <a 
                        href="{{ route('admin.sub-categories.edit', $sub->id) }}" 
                        class="flex-1 py-2 text-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors"
                    >
                        Edit Sub Category
                    </a>

                    <form method="POST" action="{{ route('admin.sub-categories.destroy', $sub->id) }}" onsubmit="return confirm('Delete this sub category?');" class="inline">
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
                <p class="text-xs font-semibold text-slate-500">No sub categories found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination & Meta -->
    @if($subCategories->hasPages() || $subCategories->total() > 0)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                Showing <span class="font-bold text-slate-800 dark:text-slate-200">{{ $subCategories->firstItem() ?? 0 }}</span> to <span class="font-bold text-slate-800 dark:text-slate-200">{{ $subCategories->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800 dark:text-slate-200">{{ $subCategories->total() }}</span> sub categories
            </div>
            <div>
                {{ $subCategories->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
