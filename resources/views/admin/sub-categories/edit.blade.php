@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-12">

    <div class="space-y-1">
        <x-breadcrumbs :items="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Categories', 'url' => route('admin.categories.index')],
            ['label' => 'Sub Categories', 'url' => route('admin.sub-categories.index')],
            ['label' => 'Edit ' . $subCategory->name]
        ]" />
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Edit Sub Category: {{ $subCategory->name }}</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Update category classification and visibility settings.</p>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.sub-categories.update', $subCategory->id) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Parent Category *</label>
                <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $subCategory->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sub Category Name *</label>
                <input type="text" name="name" value="{{ old('name', $subCategory->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors">{{ old('description', $subCategory->description) }}</textarea>
            </div>

            @if($subCategory->image)
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Current Image</label>
                    <div class="h-16 w-16 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                        <img src="{{ str_starts_with($subCategory->image, 'http') ? $subCategory->image : asset('storage/' . $subCategory->image) }}" class="h-full w-full object-cover">
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Change Image</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/60 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100 cursor-pointer">
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer text-xs font-medium text-slate-700 dark:text-slate-300">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subCategory->status ?? $subCategory->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 h-4 w-4 cursor-pointer">
                    <span>Active and visible in store</span>
                </label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.sub-categories.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition-all cursor-pointer">Update Sub Category</button>
            </div>
        </form>
    </div>

</div>
@endsection

