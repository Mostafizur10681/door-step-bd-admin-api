@extends('layouts.admin')

@section('content')
<div x-data="productCreateForm()" class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Single File Replace Trigger Input -->
    <input 
        x-ref="replaceFileInput" 
        type="file" 
        accept="image/jpeg,image/png,image/webp,image/gif" 
        class="hidden" 
        @change="handleReplaceFile($event)"
    >

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.products.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Products</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Add Product</span>
        </div>
        <div class="flex items-center justify-between pt-1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Add New Product</h1>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Products
            </a>
        </div>
    </div>

    <template x-if="imageError">
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-center justify-between shadow-sm">
            <div class="font-bold flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span x-text="imageError"></span>
            </div>
            <button type="button" @click="imageError = ''" class="hover:text-rose-900 dark:hover:text-rose-100 font-bold p-1 cursor-pointer">✕</button>
        </div>
    </template>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Please fix the following errors:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Create Form Grid -->
    <form id="productForm" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" @submit="isSubmitting = true">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: FORM CARDS (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. PRODUCT IMAGES CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Product Images</h2>
                            <p class="text-[11px] text-slate-400">Add product images. First image is used as main thumbnail.</p>
                        </div>
                        <template x-if="previewUrls.length > 0">
                            <button 
                                type="button" 
                                @click="clearAllImages()" 
                                class="text-[11px] font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 flex items-center gap-1 hover:underline cursor-pointer"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Clear All
                            </button>
                        </template>
                    </div>

                    <!-- Compact Primary Preview Box with Overlay Actions -->
                    <div class="relative w-full h-52 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 overflow-hidden flex flex-col items-center justify-center text-center p-3 group transition-all">
                        <template x-if="primaryPreview">
                            <div class="relative w-full h-full flex items-center justify-center">
                                <img :src="primaryPreview" alt="Preview" class="max-h-full max-w-full object-contain rounded-xl shadow-sm">
                                
                                <div class="absolute bottom-2 inset-x-0 flex items-center justify-center gap-2 px-3 py-1.5 bg-slate-900/80 backdrop-blur-md rounded-xl max-w-fit mx-auto shadow-lg">
                                    <button 
                                        type="button" 
                                        @click="triggerReplace(activePreviewIndex)"
                                        class="px-2.5 py-1 text-[11px] font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg flex items-center gap-1 transition-colors cursor-pointer shadow-sm"
                                        title="Replace this image"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        Replace
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="removePreview(activePreviewIndex)"
                                        class="px-2.5 py-1 text-[11px] font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg flex items-center gap-1 transition-colors cursor-pointer shadow-sm"
                                        title="Delete this image"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template x-if="!primaryPreview">
                            <div class="flex flex-col items-center justify-center space-y-2 text-slate-400 dark:text-slate-500">
                                <div class="h-12 w-12 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center shadow-sm">
                                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-medium">No image uploaded yet</p>
                            </div>
                        </template>
                    </div>

                    <!-- Image Upload Limit & File Types Info Badge -->
                    <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium">
                            <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong class="font-semibold text-emerald-600 dark:text-emerald-400">Supported Formats:</strong> JPG, PNG, WEBP, GIF</span>
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/15 rounded-lg shrink-0">
                            Max Size: 5 MB / image
                        </span>
                    </div>

                    @error('images')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                    @error('images.*')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" /></svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <!-- Drag & Drop Upload Zone -->
                    <div 
                        @dragover.prevent="$el.classList.add('border-emerald-500', 'bg-emerald-50/20')"
                        @dragleave.prevent="$el.classList.remove('border-emerald-500', 'bg-emerald-50/20')"
                        @drop.prevent="$el.classList.remove('border-emerald-500', 'bg-emerald-50/20'); handleFiles($event.dataTransfer.files);"
                        class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl py-3.5 px-4 flex items-center justify-center gap-3 text-center cursor-pointer transition-all bg-slate-50/50 dark:bg-slate-950/40 group"
                        @click="$refs.fileInput.click()"
                    >
                        <input 
                            x-ref="fileInput" 
                            type="file" 
                            name="images[]" 
                            multiple 
                            accept="image/jpeg,image/png,image/webp,image/gif" 
                            class="hidden" 
                            @change="handleFiles($event.target.files)"
                        >
                        <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Click or drag & drop to <span class="text-emerald-600 dark:text-emerald-400 font-bold underline decoration-emerald-500">add more images</span>
                            </p>
                            <p class="text-[10px] text-slate-400">JPG, PNG, WEBP, GIF — Max 5MB per image (multiple files supported)</p>
                        </div>
                    </div>

                    <!-- Selected Thumbnails Strip with Delete and Replace -->
                    <template x-if="previewUrls.length > 0">
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500">
                                <span>Uploaded Gallery (<span x-text="previewUrls.length"></span>)</span>
                                <span class="text-[10px] text-slate-400">Click thumbnail to view • ✕ to delete • ⇄ to replace</span>
                            </div>
                            <div class="flex flex-wrap gap-2.5">
                                <template x-for="(item, idx) in previewUrls" :key="idx">
                                    <div 
                                        @click="selectPreview(idx)" 
                                        class="relative h-16 w-16 rounded-xl border overflow-hidden cursor-pointer group bg-slate-100 dark:bg-slate-800 transition-all shrink-0 shadow-sm"
                                        :class="activePreviewIndex === idx ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-slate-200 dark:border-slate-700'"
                                    >
                                        <img :src="item.url" class="h-full w-full object-cover">
                                        
                                        <template x-if="idx === 0">
                                            <span class="absolute bottom-0.5 left-0.5 px-1 py-0.2 bg-emerald-600 text-white text-[8px] font-extrabold rounded">
                                                MAIN
                                            </span>
                                        </template>

                                        <button 
                                            type="button" 
                                            @click.stop="removePreview(idx)" 
                                            class="absolute top-1 right-1 h-5 w-5 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] font-bold shadow-md cursor-pointer transition-transform hover:scale-110"
                                            title="Delete image"
                                        >
                                            ✕
                                        </button>

                                        <button 
                                            type="button" 
                                            @click.stop="triggerReplace(idx)" 
                                            class="absolute bottom-1 right-1 h-5 w-5 rounded-full bg-slate-900/90 hover:bg-emerald-600 text-white flex items-center justify-center text-[9px] shadow-md cursor-pointer transition-all opacity-0 group-hover:opacity-100"
                                            title="Replace this image"
                                        >
                                            ⇄
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- 2. PRODUCT INFORMATION CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Product Information</h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Product Name *</label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="name" 
                            @input="updateSkuFromName()" 
                            required 
                            placeholder="Enter product title..." 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Category (Main Category) *</label>
                            <select 
                                name="category_id" 
                                x-model="categoryId" 
                                required 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                            >
                                <option value="">Select main category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sub Category</label>
                            <select 
                                name="sub_category_id" 
                                x-model="subCategoryId" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                            >
                                <option value="">Select sub category</option>
                                <template x-for="sub in filteredSubCategories" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name" :selected="sub.id == subCategoryId"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Brand</label>
                            <input 
                                type="text" 
                                name="brand" 
                                x-model="brand" 
                                placeholder="e.g. Shopia, Apex" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Color</label>
                            <input 
                                type="text" 
                                name="color" 
                                x-model="color" 
                                placeholder="e.g. Black, Red, Navy Blue" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">SKU (Auto)</label>
                                <span class="text-[10px] text-slate-400 font-medium">Auto-generated</span>
                            </div>
                            <input 
                                type="text" 
                                name="sku" 
                                x-model="sku" 
                                readonly 
                                tabindex="-1" 
                                placeholder="Auto-generated from name..." 
                                class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono font-bold text-slate-700 dark:text-slate-300 cursor-not-allowed select-all focus:outline-none"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Short Description</label>
                        <textarea 
                            name="short_description" 
                            x-model="short_description" 
                            rows="2" 
                            placeholder="Brief summary of product features..." 
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Full Description</label>
                        <textarea 
                            name="description" 
                            rows="4" 
                            placeholder="Detailed product information, specifications, and details..." 
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Additional Information</label>
                        <textarea 
                            name="additional_info" 
                            x-model="additional_info" 
                            rows="3" 
                            placeholder="Warranty, material origin, care guide, size chart notes, etc..." 
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        ></textarea>
                    </div>
                </div>

                <!-- 3. PRICING CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pricing</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Regular Price ($ / ৳) *</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="price" 
                                x-model="price" 
                                required 
                                placeholder="0" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sale Price ($ / ৳)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="sale_price" 
                                x-model="sale_price" 
                                placeholder="0" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Cost Price ($ / ৳)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="cost_price" 
                                value="{{ old('cost_price', 0) }}" 
                                placeholder="0" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tax (%)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="tax" 
                                value="{{ old('tax', 0) }}" 
                                placeholder="0" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Discount (%)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="discount" 
                                value="{{ old('discount', 0) }}" 
                                placeholder="0" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>
                    </div>
                </div>

                <!-- 4. INVENTORY CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Inventory</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Stock Quantity *</label>
                            <input 
                                type="number" 
                                name="stock" 
                                x-model="stock" 
                                required 
                                min="0" 
                                placeholder="0" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Unit</label>
                            <select 
                                name="unit" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                            >
                                <option value="">Select unit</option>
                                <option value="pcs" {{ old('unit', 'pcs') == 'pcs' ? 'selected' : '' }}>pcs</option>
                                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>kg</option>
                                <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>g</option>
                                <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>liter</option>
                                <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>ml</option>
                                <option value="set" {{ old('unit') == 'set' ? 'selected' : '' }}>set</option>
                                <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>pack</option>
                                <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>box</option>
                                <option value="pair" {{ old('unit') == 'pair' ? 'selected' : '' }}>pair</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Stock Status</label>
                            <select 
                                name="stock_status" 
                                x-model="stock_status" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                            >
                                <option value="in-stock">In Stock</option>
                                <option value="out-of-stock">Out of Stock</option>
                                <option value="pre-order">Pre-Order</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 5. PRODUCT STATUS CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Product Status</h2>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                        <select 
                            name="status" 
                            x-model="status" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <!-- 6. PRODUCT BADGES CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Product Badges</h2>
                    <div class="flex flex-wrap items-center gap-6 pt-1">
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="organic" value="1" {{ old('organic') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span>Organic</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span>Featured</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="best_seller" value="1" {{ old('best_seller') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span>Best Seller</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="new_arrival" value="1" {{ old('new_arrival') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span>New Arrival</span>
                        </label>
                    </div>
                </div>

                <!-- 7. SEO META CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">SEO</h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Meta Title</label>
                        <input 
                            type="text" 
                            name="meta_title" 
                            value="{{ old('meta_title') }}" 
                            placeholder="SEO meta title..." 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Meta Description</label>
                        <textarea 
                            name="meta_description" 
                            rows="2" 
                            placeholder="Meta description for search engines..." 
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        >{{ old('meta_description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Meta Keywords</label>
                        <input 
                            type="text" 
                            name="meta_keywords" 
                            value="{{ old('meta_keywords') }}" 
                            placeholder="comma, separated, keywords" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>
                </div>

                <!-- 8. PRODUCT ATTRIBUTES CARD (Dynamic Values from Attributes table) -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Product Attributes</h2>
                        <span class="text-[11px] text-slate-400">Pulls values configured in Attributes menu</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        
                        <!-- Select Attribute -->
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Select Attribute</label>
                            <select 
                                x-model="selectedAttrKey" 
                                @change="onAttributeChange()"
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                            >
                                <option value="">Choose an attribute</option>
                                @foreach($attributes as $attr)
                                    <option value="{{ $attr->name }}">{{ $attr->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Attribute Value (Select if predefined, else text input) -->
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Attribute Value</label>
                            
                            <!-- When selected attribute has stored predefined values -->
                            <template x-if="availableValues.length > 0">
                                <select 
                                    x-model="attrValue" 
                                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                                >
                                    <option value="">Select a value</option>
                                    <template x-for="val in availableValues" :key="val">
                                        <option :value="val" x-text="val"></option>
                                    </template>
                                </select>
                            </template>

                            <!-- When selected attribute has no predefined values or none selected -->
                            <template x-if="availableValues.length === 0">
                                <input 
                                    type="text" 
                                    x-model="attrValue" 
                                    @keydown.enter.prevent="addAttribute()" 
                                    :placeholder="selectedAttrKey ? 'Enter ' + selectedAttrKey + ' value...' : 'e.g. Red, Large, 5kg'" 
                                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                                >
                            </template>
                        </div>

                        <!-- Add Button -->
                        <div class="sm:col-span-2">
                            <button 
                                type="button" 
                                @click="addAttribute()" 
                                class="w-full px-3 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-1 cursor-pointer"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Hidden Inputs for attributes submission -->
                    <template x-for="(attr, index) in attributesList" :key="index">
                        <input type="hidden" :name="'attributes[' + attr.key + '][]'" :value="attr.value">
                    </template>

                    <!-- Attributes Tag List -->
                    <div class="pt-2">
                        <template x-if="attributesList.length === 0">
                            <p class="text-xs text-slate-400 italic">No attributes added yet.</p>
                        </template>

                        <div class="flex flex-wrap gap-2">
                            <template x-for="(attr, index) in attributesList" :key="index">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <strong x-text="attr.key + ':'"></strong>
                                    <span x-text="attr.value"></span>
                                    <button type="button" @click="removeAttribute(index)" class="hover:text-rose-500 ml-1 font-bold cursor-pointer">✕</button>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 9. ACTION BUTTONS -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button 
                        type="submit" 
                        @click="status = 'active'"
                        :disabled="isSubmitting"
                        class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-xs font-bold rounded-2xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer flex items-center gap-2"
                    >
                        <template x-if="isSubmitting">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </template>
                        <span x-text="isSubmitting ? 'Saving...' : 'Save & Publish'"></span>
                    </button>

                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="px-6 py-3 bg-slate-900 dark:bg-slate-800 disabled:opacity-50 text-white hover:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold rounded-2xl shadow-sm hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer flex items-center gap-2"
                    >
                        <span x-text="isSubmitting ? 'Saving...' : 'Save Product'"></span>
                    </button>

                    <a 
                        href="{{ route('admin.products.index') }}" 
                        class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-2xl shadow-lg shadow-rose-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all"
                    >
                        Cancel
                    </a>
                </div>

            </div>

            <!-- RIGHT COLUMN: LIVE PREVIEW CARD (5 Cols, Sticky) -->
            <div class="lg:col-span-5 lg:sticky lg:top-6 space-y-4">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Live Preview</div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4 transition-all">
                    
                    <!-- Compact Preview Image Box -->
                    <div class="relative w-full h-44 sm:h-48 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 overflow-hidden flex items-center justify-center p-2">
                        <template x-if="discountBadge">
                            <span class="absolute top-2 left-2 z-10 bg-rose-500 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full uppercase shadow-sm" x-text="discountBadge"></span>
                        </template>
                        <template x-if="primaryPreview">
                            <img :src="primaryPreview" alt="Live Preview" class="max-h-full max-w-full object-contain">
                        </template>
                        <template x-if="!primaryPreview">
                            <div class="flex flex-col items-center justify-center text-slate-300 dark:text-slate-700">
                                <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </template>
                    </div>

                    <!-- Title & Price -->
                    <div class="space-y-1 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 
                            class="text-base font-bold text-slate-900 dark:text-white leading-snug break-words" 
                            x-text="name.trim() || 'Product Name'"
                        ></h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400" x-text="formattedPrice"></span>
                            <template x-if="originalPriceDisplay">
                                <span class="text-xs text-slate-400 line-through" x-text="originalPriceDisplay"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                        <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">Category</div>
                            <div class="font-bold text-slate-800 dark:text-slate-200 truncate" x-text="categoryName"></div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">Brand</div>
                            <div class="font-bold text-slate-800 dark:text-slate-200 truncate" x-text="brand.trim() || '—'"></div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">Color</div>
                            <div class="font-bold text-slate-800 dark:text-slate-200 truncate" x-text="color.trim() || '—'"></div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">Stock</div>
                            <div 
                                class="font-bold truncate"
                                :class="(stock > 0 && stock_status === 'in-stock') ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                x-text="(stock > 0 && stock_status === 'in-stock') ? 'In Stock (' + stock + ')' : 'Out of stock'"
                            ></div>
                        </div>
                    </div>

                    <!-- Description Preview -->
                    <div class="space-y-1 text-xs">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Description</div>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-[11px] break-words line-clamp-3" x-text="short_description.trim() || 'No description provided.'"></p>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>

<script>
function productCreateForm() {
    return {
        name: @json(old('name', '')),
        price: @json(old('price', '')),
        sale_price: @json(old('sale_price', '')),
        brand: @json(old('brand', '')),
        color: @json(old('color', '')),
        sku: @json(old('sku', $generatedSku ?? '')),
        randomSuffix: Math.random().toString(36).substring(2, 6).toUpperCase(),
        stock: @json(old('stock', '0')),
        stock_status: @json(old('stock_status', 'in-stock')),
        status: @json(old('status', 'active')),
        short_description: @json(old('short_description', '')),
        additional_info: @json(old('additional_info', '')),
        categoryId: @json(old('category_id', '')),
        subCategoryId: @json(old('sub_category_id', '')),

        updateSkuFromName() {
            if (!this.name || !this.name.trim()) {
                this.sku = 'SKU-' + this.randomSuffix;
                return;
            }
            let clean = this.name.trim()
                .toUpperCase()
                .replace(/[^A-Z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .substring(0, 24);
            
            this.sku = clean ? (clean + '-' + this.randomSuffix) : ('SKU-' + this.randomSuffix);
        },

        init() {
            if (!this.sku || this.sku.startsWith('SKU-')) {
                this.updateSkuFromName();
            }
        },
        
        categories: {
            @foreach($categories as $cat)
                '{{ $cat->id }}': @json($cat->name),
            @endforeach
        },

        subCategories: [
            @foreach($subCategories as $sub)
                { id: '{{ $sub->id }}', name: @json($sub->name), category_id: '{{ $sub->category_id }}' },
            @endforeach
        ],

        get filteredSubCategories() {
            if (!this.categoryId) return this.subCategories;
            return this.subCategories.filter(s => s.category_id == this.categoryId);
        },

        get categoryName() {
            return this.categories[this.categoryId] || '—';
        },

        imageError: '',
        isSubmitting: false,
        previewUrls: [],
        primaryPreview: null,
        activePreviewIndex: 0,
        dt: new DataTransfer(),
        replaceIndex: null,

        handleFiles(newFiles) {
            if (!newFiles || newFiles.length === 0) return;
            this.imageError = '';
            const maxSizeBytes = 5 * 1024 * 1024; // 5 MB limit
            for (let i = 0; i < newFiles.length; i++) {
                const file = newFiles[i];
                if (file.size > maxSizeBytes) {
                    this.imageError = `Image "${file.name}" is ${(file.size / (1024 * 1024)).toFixed(2)} MB. Maximum allowed image size is 5 MB. Please select an image smaller than 5 MB.`;
                    continue;
                }
                this.dt.items.add(file);
                const url = URL.createObjectURL(file);
                this.previewUrls.push({ file, url, name: file.name });
            }
            this.$refs.fileInput.files = this.dt.files;
            if (!this.primaryPreview && this.previewUrls.length > 0) {
                this.primaryPreview = this.previewUrls[0].url;
                this.activePreviewIndex = 0;
            }
        },

        removePreview(index) {
            this.previewUrls.splice(index, 1);
            const newDt = new DataTransfer();
            for (let i = 0; i < this.previewUrls.length; i++) {
                newDt.items.add(this.previewUrls[i].file);
            }
            this.dt = newDt;
            this.$refs.fileInput.files = this.dt.files;

            if (this.previewUrls.length > 0) {
                const newIdx = Math.min(index, this.previewUrls.length - 1);
                this.activePreviewIndex = newIdx;
                this.primaryPreview = this.previewUrls[newIdx].url;
            } else {
                this.primaryPreview = null;
                this.activePreviewIndex = 0;
            }
        },

        triggerReplace(index) {
            this.replaceIndex = index;
            this.$refs.replaceFileInput.click();
        },

        handleReplaceFile(event) {
            const file = event.target.files[0];
            if (!file || this.replaceIndex === null) return;
            
            const maxSizeBytes = 5 * 1024 * 1024; // 5 MB limit
            if (file.size > maxSizeBytes) {
                this.imageError = `Image "${file.name}" is ${(file.size / (1024 * 1024)).toFixed(2)} MB. Maximum allowed image size is 5 MB. Please select an image smaller than 5 MB.`;
                event.target.value = '';
                return;
            }

            this.imageError = '';
            const url = URL.createObjectURL(file);
            this.previewUrls[this.replaceIndex] = { file, url, name: file.name };
            
            const newDt = new DataTransfer();
            for (let i = 0; i < this.previewUrls.length; i++) {
                newDt.items.add(this.previewUrls[i].file);
            }
            this.dt = newDt;
            this.$refs.fileInput.files = this.dt.files;
            
            this.primaryPreview = url;
            this.activePreviewIndex = this.replaceIndex;
            this.replaceIndex = null;
            event.target.value = '';
        },

        clearAllImages() {
            this.previewUrls = [];
            this.dt = new DataTransfer();
            this.$refs.fileInput.files = this.dt.files;
            this.primaryPreview = null;
            this.activePreviewIndex = 0;
        },

        selectPreview(index) {
            this.activePreviewIndex = index;
            this.primaryPreview = this.previewUrls[index].url;
        },

        // Attributes Map from Database
        attributesData: {
            @foreach($attributes as $attr)
                @php
                    $attrVals = is_array($attr->values) ? $attr->values : (json_decode($attr->values, true) ?? []);
                @endphp
                '{{ addslashes($attr->name) }}': @json($attrVals),
            @endforeach
        },

        selectedAttrKey: '',
        attrValue: '',
        attributesList: [],

        get availableValues() {
            return this.attributesData[this.selectedAttrKey] || [];
        },

        onAttributeChange() {
            this.attrValue = '';
            const vals = this.availableValues;
            if (vals && vals.length > 0) {
                this.attrValue = vals[0];
            }
        },

        addAttribute() {
            if (!this.selectedAttrKey || !this.attrValue || !String(this.attrValue).trim()) return;
            this.attributesList.push({
                key: this.selectedAttrKey,
                value: String(this.attrValue).trim()
            });
            // Reset value to first available or empty
            const vals = this.availableValues;
            if (vals && vals.length > 0) {
                this.attrValue = vals[0];
            } else {
                this.attrValue = '';
            }
        },

        removeAttribute(index) {
            this.attributesList.splice(index, 1);
        },

        get discountBadge() {
            const p = parseFloat(this.price);
            const sp = parseFloat(this.sale_price);
            const d = parseFloat(this.discount);
            if (d && !isNaN(d) && d > 0) {
                return Math.round(d) + '% OFF';
            }
            if (sp && !isNaN(sp) && sp > 0 && p && !isNaN(p) && p > sp) {
                const pct = Math.round(((p - sp) / p) * 100);
                return pct > 0 ? (pct + '% OFF') : 'SALE';
            }
            return null;
        },

        get formattedPrice() {
            const p = parseFloat(this.price);
            const sp = parseFloat(this.sale_price);
            if (sp && !isNaN(sp) && sp > 0 && p && !isNaN(p) && p > sp) {
                return '৳' + sp.toFixed(2);
            }
            return p && !isNaN(p) && p > 0 ? '৳' + p.toFixed(2) : '৳0.00';
        },

        get originalPriceDisplay() {
            const p = parseFloat(this.price);
            const sp = parseFloat(this.sale_price);
            if (sp && !isNaN(sp) && sp > 0 && p && !isNaN(p) && p > sp) {
                return '৳' + p.toFixed(2);
            }
            return null;
        }
    };
}
</script>
@endsection
