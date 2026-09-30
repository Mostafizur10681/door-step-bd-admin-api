@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-16">

    <!-- Breadcrumbs & Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.brands.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Brands</a>
            <span>&gt;</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Edit Brand</span>
        </div>
        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Brand</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $brand->status ? 'bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-950/60 dark:border-emerald-800 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                    {{ $brand->status ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <a href="{{ route('admin.brands.index') }}" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-all">
                ← Back to List
            </a>
        </div>
    </div>

    <!-- Error Validation Feedback -->
    @if (isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs shadow-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $imgSrc = $brand->logo ? (str_starts_with($brand->logo, 'http') || str_starts_with($brand->logo, 'data:') ? $brand->logo : asset('storage/' . $brand->logo)) : null;
    @endphp

    <div x-data="{
        form: {
            name: @js(old('name', $brand->name ?? '')),
            slug: @js(old('slug', $brand->slug ?? '')),
            description: @js(old('description', $brand->description ?? '')),
            is_active: {{ old('is_active', $brand->status) ? 'true' : 'false' }}
        },
        previewUrl: '{{ $imgSrc }}',
        updateSlug() {
            if (!this.form.name) {
                this.form.slug = '';
                return;
            }
            this.form.slug = this.form.name.toString().toLowerCase().trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
            }
        }
    }" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        
        <form method="POST" action="{{ route('admin.brands.update', $brand->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Brand Name -->
                <div class="space-y-1.5">
                    <label for="brand_edit_name" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                        Brand Name <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="brand_edit_name" 
                        name="name" 
                        x-model="form.name"
                        @input="updateSlug()"
                        required 
                        placeholder="e.g. Apex, Nike, Bata, Cummins..." 
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-sm font-medium text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                </div>

                <!-- Brand Image / Logo -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                        Brand Logo / Image <span class="text-xs font-normal text-slate-400">(Leave empty to keep existing)</span>
                    </label>

                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <!-- Preview Thumbnail Box -->
                        <div class="h-28 w-28 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 flex items-center justify-center overflow-hidden shrink-0 shadow-inner relative group">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview" class="h-full w-full object-contain p-2">
                            </template>
                            <template x-if="!previewUrl">
                                <div class="text-center p-2">
                                    <svg class="h-8 w-8 text-slate-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] text-slate-400 font-medium block mt-1">No Image</span>
                                </div>
                            </template>
                        </div>

                        <!-- Upload Input Area -->
                        <div class="flex-1 w-full">
                            <label class="flex flex-col items-center justify-center p-4 border border-dashed border-slate-300 dark:border-slate-700 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Click to upload new logo/image</span>
                                <span class="text-[11px] text-slate-400">PNG, JPG, WEBP, SVG up to 10MB</span>
                                <input 
                                    type="file" 
                                    name="logo" 
                                    accept="image/*" 
                                    @change="handleFileChange($event)" 
                                    class="hidden"
                                >
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Brand Description -->
                <div class="space-y-1.5">
                    <label for="brand_edit_description" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                        Description <span class="text-xs font-normal text-slate-400">(Optional)</span>
                    </label>
                    <textarea 
                        id="brand_edit_description" 
                        name="description" 
                        x-model="form.description"
                        rows="4" 
                        placeholder="Brief summary or description about the brand..." 
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-medium text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all resize-none"
                    ></textarea>
                </div>

                <!-- Status Toggle -->
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Publish Status</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Active brands will be displayed in the brand catalog.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>

            <!-- Submit & Delete Button Row -->
            <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button 
                    type="button" 
                    onclick="if(confirm('Are you sure you want to delete this brand?')) document.getElementById('delete-brand-form').submit();"
                    class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/30 dark:hover:bg-rose-950/50 dark:text-rose-400 text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Delete Brand
                </button>

                <div class="flex items-center gap-3">
                    <a 
                        href="{{ route('admin.brands.index') }}" 
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-all"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
                    >
                        Update Brand
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-brand-form" method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
