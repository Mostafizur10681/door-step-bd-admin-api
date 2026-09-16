@extends('layouts.admin')

@section('content')
<div class="space-y-4 sm:space-y-6 max-w-5xl mx-auto pb-16 px-1 sm:px-0">
    <div class="space-y-1">
        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.blogs.index') }}" class="hover:text-emerald-600">Blog Posts</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Blog Categories</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Blog Categories</h1>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex justify-between">
            <span>{{ session('success') }}</span>
            <button @click="$el.parentElement.remove()" class="text-slate-400">✕</button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Category Form -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Add Blog Category</h3>
            <form action="{{ route('admin.blogs.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Shopping Guides" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md">Create Category</button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Existing Categories</h3>
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Category Name</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Articles</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($categories as $cat)
                            <tr>
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ $cat->name }}</td>
                                <td class="px-4 py-3 font-mono text-slate-400 text-[11px]">{{ $cat->slug }}</td>
                                <td class="px-4 py-3 font-mono font-bold">{{ $cat->blogs_count }} Articles</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat->status ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $cat->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.blogs.categories.destroy', $cat->id) }}" onsubmit="return confirm('Delete this blog category?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
