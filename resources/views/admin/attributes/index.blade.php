@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Attributes</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Product Attributes</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage variant attributes and options like Size, Color, Weight, Material, etc.</p>
            </div>
            <a 
                href="{{ route('admin.attributes.create') }}" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer w-fit"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>+ Add Attribute</span>
            </a>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.attributes.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 sm:max-w-md">
                <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search attributes by name..." 
                    class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                >
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Search
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.attributes.index') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition-colors" title="Clear search">
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
                        <th class="px-5 py-4">Attribute Name</th>
                        <th class="px-5 py-4">Configured Values</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($attributes as $attr)
                        @php
                            $vals = is_array($attr->values) ? $attr->values : (json_decode($attr->values, true) ?? []);
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.attributes.edit', $attr->id) }}" class="font-bold text-slate-900 dark:text-white text-sm hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                    {{ $attr->name }}
                                </a>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">slug: {{ $attr->code ?: Str::slug($attr->name) }}</p>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xl">
                                    @forelse($vals as $val)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/60 dark:border-slate-700">
                                            {{ $val }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">No values configured</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a 
                                        href="{{ route('admin.attributes.edit', $attr->id) }}" 
                                        class="p-2 text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all"
                                        title="Edit Attribute"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.attributes.destroy', $attr->id) }}" onsubmit="return confirm('Delete this attribute?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-all cursor-pointer"
                                            title="Delete Attribute"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <p class="text-xs font-semibold text-slate-500">No attributes configured yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. MOBILE & TABLET RESPONSIVE CARDS VIEW (Visible on < 1024px) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
        @forelse($attributes as $attr)
            @php
                $vals = is_array($attr->values) ? $attr->values : (json_decode($attr->values, true) ?? []);
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-emerald-500/40 transition-all">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.attributes.edit', $attr->id) }}" class="font-bold text-base text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                            {{ $attr->name }}
                        </a>
                        <span class="text-[10px] font-mono text-slate-400">#{{ $attr->id }}</span>
                    </div>

                    <div class="space-y-1">
                        <div class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider">Values ({{ count($vals) }})</div>
                        <div class="flex flex-wrap gap-1.5 pt-0.5">
                            @forelse($vals as $val)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $val }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400 italic">No values</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <a 
                        href="{{ route('admin.attributes.edit', $attr->id) }}" 
                        class="flex-1 py-2 text-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors"
                    >
                        Edit Attribute
                    </a>
                    <form method="POST" action="{{ route('admin.attributes.destroy', $attr->id) }}" onsubmit="return confirm('Delete this attribute?');" class="inline">
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
                <p class="text-xs font-semibold text-slate-500">No attributes found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination & Meta -->
    @if($attributes->hasPages() || $attributes->total() > 0)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                Showing <span class="font-bold text-slate-800 dark:text-slate-200">{{ $attributes->firstItem() ?? 0 }}</span> to <span class="font-bold text-slate-800 dark:text-slate-200">{{ $attributes->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800 dark:text-slate-200">{{ $attributes->total() }}</span> attributes
            </div>
            <div>
                {{ $attributes->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
