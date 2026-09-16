@extends('layouts.admin')

@section('content')
@php
    $productAttributes = [];
    if (!empty($product->attributes)) {
        $decoded = is_string($product->attributes) ? json_decode($product->attributes, true) : $product->attributes;
        if (is_array($decoded)) {
            foreach ($decoded as $k => $vals) {
                if (is_array($vals)) {
                    foreach ($vals as $v) {
                        $productAttributes[] = ['key' => $k, 'value' => $v];
                    }
                } else {
                    $productAttributes[] = ['key' => $k, 'value' => $vals];
                }
            }
        }
    }
    $mainImage = $product->image ?: ($product->images->first()->image_path ?? null);
    $mainImageUrl = $mainImage ? (str_starts_with($mainImage, 'http') || str_starts_with($mainImage, 'data:') ? $mainImage : asset('storage/' . $mainImage)) : null;
@endphp

<div x-data="productEditForm()" class="space-y-6 max-w-7xl mx-auto pb-16">

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
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Edit Product</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Product</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    #{{ $product->sku ?: $product->id }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to Products
                </a>
            </div>
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

    <template x-if="successMsg">
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-medium flex items-center justify-between shadow-sm animate-in fade-in">
            <div class="font-bold flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span x-text="successMsg"></span>
            </div>
            <button type="button" @click="successMsg = ''" class="hover:text-emerald-900 dark:hover:text-emerald-100 font-bold p-1 cursor-pointer">✕</button>
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

    <!-- Main Edit Form Grid -->
    <form id="productEditForm" method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" @submit="isSubmitting = true">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: FORM CARDS (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. PRODUCT IMAGES CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Product Images & Gallery</h2>
                            <p class="text-[11px] text-slate-400">Manage existing photos or upload multiple new high-resolution images.</p>
                        </div>
                        <template x-if="previewUrls.length > 0">
                            <button 
                                type="button" 
                                @click="clearAllNewImages()" 
                                class="text-[11px] font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 flex items-center gap-1 hover:underline cursor-pointer"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Clear New Files (<span x-text="previewUrls.length"></span>)
                            </button>
                        </template>
                    </div>

                    <!-- Compact Primary Preview Box -->
                    <div class="relative w-full h-56 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 overflow-hidden flex items-center justify-center p-3 transition-all group">
                        <template x-if="primaryPreview">
                            <img :src="primaryPreview" alt="Preview" class="max-h-full max-w-full object-contain rounded-xl shadow-sm">
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

                    <!-- Existing Gallery Images (with instant delete & set primary) -->
                    @if($product->images && $product->images->count() > 0)
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500">
                                <span class="font-bold text-slate-700 dark:text-slate-300">Existing Gallery Images ({{ $product->images->count() }})</span>
                                <span class="text-[10px] text-slate-400">Click to view • ★ Set Main • ✕ Delete</span>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                @foreach($product->images as $pImg)
                                    @php
                                        $url = str_starts_with($pImg->image_path, 'http') || str_starts_with($pImg->image_path, 'data:') ? $pImg->image_path : asset('storage/' . $pImg->image_path);
                                        $isMain = ($pImg->is_primary || $product->image === $pImg->image_path);
                                    @endphp
                                    <div 
                                        class="existing-img-card relative h-20 w-20 rounded-2xl border-2 overflow-hidden cursor-pointer group bg-slate-100 dark:bg-slate-800 shrink-0 shadow-sm transition-all hover:scale-105"
                                        :class="primaryPreview === '{{ $url }}' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-700'"
                                        @click="primaryPreview = '{{ $url }}'"
                                    >
                                        <img src="{{ $url }}" class="h-full w-full object-cover">

                                        <!-- Main Image Badge / Set Main Button -->
                                        @if($isMain)
                                            <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-md shadow flex items-center gap-0.5">
                                                ★ Main
                                            </span>
                                        @else
                                            <button 
                                                type="button" 
                                                @click.stop="setPrimaryExistingImage({{ $pImg->id }}, '{{ $url }}')" 
                                                class="absolute bottom-1 left-1 bg-slate-900/85 hover:bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow opacity-0 group-hover:opacity-100 transition-all cursor-pointer"
                                                title="Set as Main Product Image"
                                            >
                                                ★ Make Main
                                            </button>
                                        @endif

                                        <!-- Delete Image Button -->
                                        <button 
                                            type="button" 
                                            @click.stop="deleteExistingImage({{ $pImg->id }}, $el)" 
                                            class="absolute top-1 right-1 h-5 w-5 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] font-bold shadow cursor-pointer transition-transform hover:scale-110"
                                            title="Delete image"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Multiple Image Upload Dropzone -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Upload Additional / New Images</span>
                            <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">Multiple Selection Supported</span>
                        </div>

                        <div 
                            @dragover.prevent="$el.classList.add('border-emerald-500', 'bg-emerald-50/20')"
                            @dragleave.prevent="$el.classList.remove('border-emerald-500', 'bg-emerald-50/20')"
                            @drop.prevent="$el.classList.remove('border-emerald-500', 'bg-emerald-50/20'); handleFiles($event.dataTransfer.files);"
                            class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl py-5 px-4 flex flex-col sm:flex-row items-center justify-center gap-4 text-center cursor-pointer transition-all bg-slate-50/50 dark:bg-slate-950/40 group"
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
                            <div class="h-11 w-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform shadow-xs">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div class="text-center sm:text-left space-y-0.5">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    Click here to select multiple images, or drag & drop files
                                </p>
                                <p class="text-[11px] text-slate-500">Hold <kbd class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-800 rounded text-[10px] font-mono">Ctrl</kbd> or <kbd class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-800 rounded text-[10px] font-mono">Shift</kbd> to select multiple files at once</p>
                                <p class="text-[10px] text-slate-400">JPG, PNG, WEBP, GIF — Max 10MB per image</p>
                            </div>
                        </div>

                        <!-- Image Upload Limit & File Types Info Badge -->
                        <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium text-[11px]">
                                <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong class="font-semibold text-emerald-600 dark:text-emerald-400">Supported Formats:</strong> JPG, PNG, WEBP, GIF</span>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/15 rounded-md shrink-0">
                                Max Size: 10 MB / image
                            </span>
                        </div>
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

                    <!-- Newly Selected Files Preview Strip -->
                    <template x-if="previewUrls.length > 0">
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Ready to Upload (<span x-text="previewUrls.length"></span> new files)
                                </span>
                                <span class="text-[10px] text-slate-400">✕ remove • ⇄ replace</span>
                            </div>
                            <div class="flex flex-wrap gap-2.5">
                                <template x-for="(item, idx) in previewUrls" :key="idx">
                                    <div 
                                        @click="primaryPreview = item.url" 
                                        class="relative h-18 w-18 rounded-2xl border-2 overflow-hidden cursor-pointer group bg-slate-100 dark:bg-slate-800 transition-all shrink-0 shadow-sm"
                                        :class="primaryPreview === item.url ? 'border-emerald-500 ring-2 ring-emerald-500/20 scale-105' : 'border-slate-200 dark:border-slate-700'"
                                    >
                                        <img :src="item.url" class="h-full w-full object-cover">
                                        
                                        <!-- File name pill on hover -->
                                        <div class="absolute inset-x-0 bottom-0 bg-black/70 text-white text-[8px] p-0.5 truncate text-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span x-text="item.name"></span>
                                        </div>

                                        <button 
                                            type="button" 
                                            @click.stop="removePreview(idx)" 
                                            class="absolute top-1 right-1 h-5 w-5 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] font-bold shadow cursor-pointer transition-transform hover:scale-110"
                                            title="Remove image"
                                        >
                                            ✕
                                        </button>
                                        <button 
                                            type="button" 
                                            @click.stop="triggerReplace(idx)" 
                                            class="absolute bottom-1 right-1 h-5 w-5 rounded-full bg-slate-900/90 hover:bg-emerald-600 text-white flex items-center justify-center text-[9px] shadow cursor-pointer transition-all opacity-0 group-hover:opacity-100"
                                            title="Replace image"
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
                            placeholder="e.g. Pure Shilajit Resin, Apple iPhone 15 Pro..." 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Brand</label>
                            <input 
                                type="text" 
                                name="brand" 
                                value="{{ old('brand', $product->brand) }}" 
                                placeholder="e.g. Door Step BD, Organic Life, Nike..." 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Color / Variant</label>
                            <input 
                                type="text" 
                                name="color" 
                                value="{{ old('color', $product->color) }}" 
                                placeholder="e.g. Black, Navy Blue, Golden..." 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
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
                        >{{ old('description', $product->description) }}</textarea>
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
                                value="{{ old('cost_price', $product->cost_price ?? 0) }}" 
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
                                value="{{ old('tax', $product->tax ?? 0) }}" 
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
                                value="{{ old('discount', $product->discount ?? 0) }}" 
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
                            <input 
                                type="text" 
                                name="unit" 
                                value="{{ old('unit', $product->unit ?? 'pcs') }}" 
                                placeholder="e.g. pcs, kg, box, bottle" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Stock Status</label>
                            <select 
                                name="stock_status" 
                                x-model="stock_status" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                                <option value="in-stock">In Stock</option>
                                <option value="out-of-stock">Out of Stock</option>
                                <option value="pre-order">Pre-Order</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">SKU (Auto-Generated)</label>
                            <input 
                                type="text" 
                                name="sku" 
                                x-model="sku" 
                                placeholder="SKU-XXXXXXXX" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Barcode / EAN</label>
                            <input 
                                type="text" 
                                name="barcode" 
                                value="{{ old('barcode', $product->barcode) }}" 
                                placeholder="Optional barcode" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                            >
                        </div>
                    </div>
                </div>

                <!-- 5. ATTRIBUTES & VARIANTS CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Product Attributes</h2>
                            <p class="text-[11px] text-slate-400">Add custom attributes like Size, Weight, Material, etc.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <select 
                            x-model="selectedAttrKey" 
                            @change="onAttributeChange()" 
                            class="w-full sm:w-1/3 px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                            <option value="">-- Choose Attribute --</option>
                            @foreach($attributes as $attr)
                                <option value="{{ $attr->name }}">{{ $attr->name }}</option>
                            @endforeach
                        </select>

                        <div class="w-full sm:flex-1">
                            <template x-if="availableValues && availableValues.length > 0">
                                <select 
                                    x-model="attrValue" 
                                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                                >
                                    <template x-for="v in availableValues" :key="v">
                                        <option :value="v" x-text="v"></option>
                                    </template>
                                </select>
                            </template>
                            <template x-if="!availableValues || availableValues.length === 0">
                                <input 
                                    type="text" 
                                    x-model="attrValue" 
                                    placeholder="Enter attribute value..." 
                                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                                >
                            </template>
                        </div>

                        <button 
                            type="button" 
                            @click="addAttribute()" 
                            class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition-colors cursor-pointer shadow-sm"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add
                        </button>
                    </div>

                    <!-- Hidden Inputs for Form Submission -->
                    <template x-for="(attr, index) in attributesList" :key="index">
                        <input type="hidden" :name="'attributes[' + attr.key + '][]'" :value="attr.value">
                    </template>

                    <!-- Active Attributes Tags -->
                    <template x-if="attributesList.length > 0">
                        <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <template x-for="(attr, index) in attributesList" :key="index">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                    <span class="text-slate-400 dark:text-slate-500" x-text="attr.key + ':'"></span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="attr.value"></span>
                                    <button 
                                        type="button" 
                                        @click="removeAttribute(index)" 
                                        class="text-slate-400 hover:text-rose-600 transition-colors ml-1 cursor-pointer font-bold"
                                    >
                                        ✕
                                    </button>
                                </span>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- 6. SEO META TAGS CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">SEO & Search Metadata</h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Meta Title</label>
                        <input 
                            type="text" 
                            name="meta_title" 
                            value="{{ old('meta_title', $product->meta_title) }}" 
                            placeholder="SEO Optimized Page Title" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Meta Keywords</label>
                        <input 
                            type="text" 
                            name="meta_keywords" 
                            value="{{ old('meta_keywords', $product->meta_keywords) }}" 
                            placeholder="organic, supplements, food, honey" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Meta Description</label>
                        <textarea 
                            name="meta_description" 
                            rows="2" 
                            placeholder="Brief search snippet description..." 
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        >{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: SIDEBAR CONTROLS (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 1. PUBLISHING STATUS & ACTIONS CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Status & Visibility</h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Product Status *</label>
                        <select 
                            name="status" 
                            x-model="status" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                            <option value="active">🟢 Active / Published</option>
                            <option value="inactive">🔴 Inactive / Hidden</option>
                            <option value="draft">🟡 Draft</option>
                        </select>
                    </div>

                    <!-- Marketing Badges Toggle -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Promotions & Badges</label>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition">
                                <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Featured</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition">
                                <input type="checkbox" name="best_seller" value="1" {{ old('best_seller', $product->best_seller) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Best Seller</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition">
                                <input type="checkbox" name="new_arrival" value="1" {{ old('new_arrival', $product->new_arrival) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">New Arrival</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition">
                                <input type="checkbox" name="organic" value="1" {{ old('organic', $product->organic) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Organic</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit & Save Actions -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
                        <button 
                            type="submit" 
                            :disabled="isSubmitting" 
                            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span x-text="isSubmitting ? 'Updating Product...' : 'Update & Save Changes'"></span>
                        </button>
                    </div>
                </div>

                <!-- 2. CATEGORY & ORGANIZATION CARD -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Categories</h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Primary Category *</label>
                        <select 
                            name="category_id" 
                            x-model="categoryId" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sub-Category</label>
                        <select 
                            name="sub_category_id" 
                            x-model="subCategoryId" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                            <option value="">-- None / No Sub-Category --</option>
                            <template x-for="sub in filteredSubCategories" :key="sub.id">
                                <option :value="sub.id" x-text="sub.name" :selected="sub.id == subCategoryId"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- 3. LIVE STORE CARD PREVIEW -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center justify-between">
                        <span>Storefront Preview</span>
                        <span class="text-[10px] font-normal text-slate-400">Live Preview</span>
                    </h2>

                    <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="relative w-full h-40 bg-white dark:bg-slate-900 rounded-xl overflow-hidden flex items-center justify-center p-2 border border-slate-100 dark:border-slate-800 shadow-xs">
                            <template x-if="primaryPreview">
                                <img :src="primaryPreview" class="max-h-full max-w-full object-contain">
                            </template>
                            <template x-if="!primaryPreview">
                                <span class="text-xs text-slate-400">No Image</span>
                            </template>

                            <template x-if="discountBadge">
                                <span class="absolute top-2 left-2 bg-rose-600 text-white font-black text-[10px] px-2 py-0.5 rounded-full uppercase" x-text="discountBadge"></span>
                            </template>
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-bold text-emerald-600 dark:text-emerald-400" x-text="categoryName"></span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white truncate" x-text="name || 'Product Title'"></h4>
                            
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="font-black text-sm text-slate-900 dark:text-white" x-text="formattedPrice"></span>
                                <template x-if="originalPriceDisplay">
                                    <span class="text-xs text-slate-400 line-through" x-text="'৳' + parseFloat(price).toFixed(2)"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
function productEditForm() {
    return {
        name: @json(old('name', $product->name)),
        price: @json(old('price', $product->price)),
        sale_price: @json(old('sale_price', $product->sale_price ?? '')),
        discount: @json(old('discount', $product->discount ?? '')),
        sku: @json(old('sku', $product->sku ?? '')),
        randomSuffix: Math.random().toString(36).substring(2, 6).toUpperCase(),
        stock: @json(old('stock', $product->stock ?? '0')),
        stock_status: @json(old('stock_status', $product->stock_status ?? 'in-stock')),
        status: @json(old('status', $product->status)),
        short_description: @json(old('short_description', $product->short_description ?? '')),
        additional_info: @json(old('additional_info', $product->additional_info ?? '')),
        categoryId: @json(old('category_id', $product->category_id)),
        subCategoryId: @json(old('sub_category_id', $product->sub_category_id ?? '')),

        updateSkuFromName() {
            if (this.sku && !this.sku.startsWith('SKU-')) return;
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
            if (!this.sku) {
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
        successMsg: '',
        isSubmitting: false,
        primaryPreview: @json($mainImageUrl),
        previewUrls: [],
        dt: new DataTransfer(),
        replaceIndex: null,

        handleFiles(newFiles) {
            if (!newFiles || newFiles.length === 0) return;
            this.imageError = '';
            const maxSizeBytes = 10 * 1024 * 1024; // 10 MB limit
            for (let i = 0; i < newFiles.length; i++) {
                const file = newFiles[i];
                if (file.size > maxSizeBytes) {
                    this.imageError = `Image "${file.name}" is ${(file.size / (1024 * 1024)).toFixed(2)} MB. Maximum allowed image size is 10 MB.`;
                    continue;
                }
                this.dt.items.add(file);
                const url = URL.createObjectURL(file);
                this.previewUrls.push({ file, url, name: file.name });
            }
            this.$refs.fileInput.files = this.dt.files;
            if (this.previewUrls.length > 0) {
                this.primaryPreview = this.previewUrls[this.previewUrls.length - 1].url;
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
                this.primaryPreview = this.previewUrls[this.previewUrls.length - 1].url;
            } else {
                this.primaryPreview = @json($mainImageUrl);
            }
        },

        clearAllNewImages() {
            this.previewUrls = [];
            this.dt = new DataTransfer();
            this.$refs.fileInput.files = this.dt.files;
            this.primaryPreview = @json($mainImageUrl);
        },

        triggerReplace(index) {
            this.replaceIndex = index;
            this.$refs.replaceFileInput.click();
        },

        handleReplaceFile(event) {
            const file = event.target.files[0];
            if (!file || this.replaceIndex === null) return;

            const maxSizeBytes = 10 * 1024 * 1024; // 10 MB limit
            if (file.size > maxSizeBytes) {
                this.imageError = `Image "${file.name}" is ${(file.size / (1024 * 1024)).toFixed(2)} MB. Maximum allowed image size is 10 MB.`;
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
            this.replaceIndex = null;
            event.target.value = '';
        },

        async deleteExistingImage(imageId, el) {
            if (!confirm('Are you sure you want to delete this image?')) return;
            try {
                const res = await fetch(`/admin/products/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    el.closest('.existing-img-card').remove();
                    this.successMsg = 'Image deleted successfully';
                    setTimeout(() => { this.successMsg = ''; }, 3000);
                }
            } catch (e) {
                alert('Error deleting image');
            }
        },

        async setPrimaryExistingImage(imageId, url) {
            try {
                const res = await fetch(`/admin/products/images/${imageId}/primary`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.primaryPreview = url;
                    this.successMsg = 'Main product image updated successfully! (Page will refresh to show new badges)';
                    setTimeout(() => { window.location.reload(); }, 1200);
                }
            } catch (e) {
                alert('Error updating primary image');
            }
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
        attributesList: @json($productAttributes),

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
