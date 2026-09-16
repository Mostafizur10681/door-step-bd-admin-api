@extends('layouts.admin')

@section('content')
<div class="space-y-4 sm:space-y-6 max-w-7xl mx-auto pb-16 px-1 sm:px-0">

    <!-- Header & Breadcrumbs -->
    <div class="space-y-1">
        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Blog Posts</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Blog Directory</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200">
                    {{ $blogs->total() }} Posts
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.blogs.categories') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition-all">
                    🏷️ Categories
                </a>
                <a href="{{ route('admin.blogs.comments') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition-all">
                    💬 Comments
                </a>
                <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-all shadow-md shadow-emerald-600/20">
                    + Add New Article
                </a>
            </div>
        </div>
    </div>

    <!-- Alert / Feedback -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500">Total Articles</span>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500">Published</span>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['published']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500">Drafts</span>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($stats['draft']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500">Featured Stories</span>
            <div class="text-xl sm:text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ number_format($stats['featured']) }}</div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm space-y-4">
        
        <!-- Filter Toolbar -->
        <form method="GET" action="{{ route('admin.blogs.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 sm:w-72">
                <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles by title or keyword..." class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
            </div>
            <div class="flex items-center gap-2">
                <select name="category_id" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
        </form>

        <!-- Blog Posts Table -->
        <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-2xl">
            <table class="w-full text-left text-xs min-w-[700px]">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Article</th>
                        <th class="px-5 py-4">Category</th>
                        <th class="px-5 py-4">Author</th>
                        <th class="px-5 py-4">Views</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($blogs as $blog)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($blog->image)
                                        <img src="{{ $blog->image }}" alt="{{ $blog->title }}" class="w-12 h-10 object-cover rounded-xl border border-slate-200 dark:border-slate-800 shrink-0">
                                    @else
                                        <div class="w-12 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 font-bold shrink-0">📰</div>
                                    @endif
                                    <div class="space-y-0.5 max-w-md">
                                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 line-clamp-1">
                                            {{ $blog->title }}
                                        </a>
                                        <span class="block text-[11px] text-slate-400 font-mono">/blog/{{ $blog->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $blog->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $blog->author_name }}
                            </td>
                            <td class="px-5 py-4 font-mono font-bold text-slate-800 dark:text-slate-200">
                                👁️ {{ number_format($blog->views) }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold w-fit {{ $blog->status === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $blog->status === 'published' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                        {{ ucfirst($blog->status) }}
                                    </span>
                                    @if($blog->featured)
                                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 w-fit">⭐ Featured</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 font-mono text-slate-500 text-[11px]">
                                {{ $blog->created_at ? $blog->created_at->format('M d, Y') : '' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 flex items-center justify-center transition-all">
                                        ✏️
                                    </a>
                                    <form method="POST" action="{{ route('admin.blogs.destroy', $blog->id) }}" onsubmit="return confirm('Delete this blog post?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-all">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 italic">No blog posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($blogs->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $blogs->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
