@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-24 px-2 sm:px-4" x-data="{
    previewMode: 'desktop', // 'desktop' | 'mobile'
    badge: {{ Js::from(old('badge', '')) }},
    title_line1: {{ Js::from(old('title_line1', '')) }},
    title_line2: {{ Js::from(old('title_line2', '')) }},
    subtitle: {{ Js::from(old('subtitle', '')) }},
    cta_text: {{ Js::from(old('cta_text', '')) }},
    cta_link: {{ Js::from(old('cta_link', '/all-products')) }},
    bg_color: {{ Js::from(old('bg_color', '#002B49')) }},
    menu_location: {{ Js::from(old('menu_location', 'home_hero_slider')) }},
    order: {{ (int) old('order', $nextOrder ?? 1) }},
    is_active: {{ old('is_active', '1') === '1' ? "'1'" : "'0'" }},
    imagePreview: '',
    imageBase64: '',
    imageUrl: {{ Js::from(old('image_url', '')) }},
    mobileImagePreview: '',
    mobileImageBase64: '',
    mobileImageUrl: {{ Js::from(old('mobile_image_url', '')) }},

    hasTextOverlay() {
        return Boolean(
            (this.badge && this.badge.trim().length > 0) ||
            (this.title_line1 && this.title_line1.trim().length > 0) ||
            (this.title_line2 && this.title_line2.trim().length > 0) ||
            (this.subtitle && this.subtitle.trim().length > 0) ||
            (this.cta_text && this.cta_text.trim().length > 0)
        );
    },

    clearText() {
        this.badge = '';
        this.title_line1 = '';
        this.title_line2 = '';
        this.subtitle = '';
        this.cta_text = '';
    },

    compressFile(file, maxWidth, maxHeight, quality = 0.85) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth || height > maxHeight) {
                        const ratio = Math.min(maxWidth / width, maxHeight / height);
                        width = Math.round(width * ratio);
                        height = Math.round(height * ratio);
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    try {
                        const dataUrl = canvas.toDataURL('image/webp', quality);
                        if (dataUrl && dataUrl.startsWith('data:image/webp')) {
                            resolve(dataUrl);
                            return;
                        }
                    } catch (err) {}

                    resolve(canvas.toDataURL('image/jpeg', quality));
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    },

    async previewDesktopImage(event) {
        const file = event.target.files[0];
        if (file) {
            const compressed = await this.compressFile(file, 1920, 800, 0.85);
            this.imagePreview = compressed;
            this.imageBase64 = compressed;
            this.imageUrl = '';
        }
    },

    async previewMobileImage(event) {
        const file = event.target.files[0];
        if (file) {
            const compressed = await this.compressFile(file, 800, 800, 0.85);
            this.mobileImagePreview = compressed;
            this.mobileImageBase64 = compressed;
            this.mobileImageUrl = '';
        }
    }
}">

    <form id="bannerCreateForm" method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <input type="hidden" name="image_base64" :value="imageBase64">
        <input type="hidden" name="mobile_image_base64" :value="mobileImageBase64">
        <input type="hidden" name="image_fallback" :value="imagePreview && !imagePreview.startsWith('data:') ? imagePreview : '/hero_honey.png'">

        <!-- Top Header & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumbs :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Banners', 'url' => route('admin.banners.index')],
                    ['label' => 'Create Banner']
                ]" />
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white mt-1">
                    Create Storefront Banner
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Text overlays are optional. You can design clean image-only promotional banners or rich text overlays.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    <span>Save &amp; Publish Banner</span>
                </button>
            </div>
        </div>

        <!-- Global Error Alerts -->
        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-semibold space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Please fix the following validation errors:</span>
                </div>
                <ul class="list-disc list-inside pl-1 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ═══ LIVE STOREFRONT MIRROR PREVIEW ═══ -->
        <div class="space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <span>✨</span> Real-Time Storefront Banner Preview
                    </span>
                    <template x-if="!hasTextOverlay()">
                        <span class="text-[10px] font-bold text-sky-600 bg-sky-50 dark:bg-sky-950/60 px-2 py-0.5 rounded-full border border-sky-200 dark:border-sky-800">
                            Image Only Mode (No Text Overlay)
                        </span>
                    </template>
                    <template x-if="hasTextOverlay()">
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                            Text Overlay Active
                        </span>
                    </template>
                </div>
                
                <!-- View Mode Switcher: Desktop vs Mobile -->
                <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button 
                        type="button" 
                        @click="previewMode = 'desktop'"
                        :class="previewMode === 'desktop' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-black' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-semibold'"
                        class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Desktop View
                    </button>
                    <button 
                        type="button" 
                        @click="previewMode = 'mobile'"
                        :class="previewMode === 'mobile' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-black' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-semibold'"
                        class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        Mobile View
                    </button>
                </div>
            </div>

            <!-- ── 1. DESKTOP PREVIEW CONTAINER ── -->
            <div x-show="previewMode === 'desktop'" class="w-full rounded-3xl overflow-hidden shadow-2xl relative border-4 border-slate-900 transition-all duration-300 min-h-[300px] sm:min-h-[420px] flex items-center" :style="'background-color: ' + bg_color">
                <!-- Background Graphic Image -->
                <img 
                    :src="imageUrl || imagePreview" 
                    alt="Desktop Banner" 
                    class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
                    onerror="this.src='/hero_honey.png'"
                >
                
                <!-- Gradient Overlay (Only shown when text overlay exists) -->
                <template x-if="hasTextOverlay()">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-transparent pointer-events-none transition-opacity duration-300"></div>
                </template>

                <!-- Content Overlay -->
                <template x-if="hasTextOverlay()">
                    <div class="relative z-10 w-full px-6 sm:px-12 py-8 max-w-4xl">
                        <div class="space-y-3 sm:space-y-4 text-white">
                            
                            <!-- Tagline Pill -->
                            <template x-if="badge && badge.trim().length > 0">
                                <div class="inline-flex items-center gap-1.5 bg-[#FF6600] text-white text-[11px] sm:text-xs font-black uppercase tracking-widest px-3.5 py-1 rounded-full shadow-md">
                                    <span x-text="badge"></span>
                                </div>
                            </template>

                            <!-- Headline Typography -->
                            <template x-if="(title_line1 && title_line1.trim().length > 0) || (title_line2 && title_line2.trim().length > 0)">
                                <h2 class="text-2xl sm:text-4xl md:text-5xl font-black uppercase tracking-tight text-white drop-shadow-md leading-[1.08]">
                                    <span x-text="title_line1"></span>
                                    <template x-if="title_line2 && title_line2.trim().length > 0">
                                        <span class="text-amber-300 block pt-0.5" x-text="title_line2"></span>
                                    </template>
                                </h2>
                            </template>

                            <!-- Discount / Subtitle -->
                            <template x-if="subtitle && subtitle.trim().length > 0">
                                <p class="text-sm sm:text-lg md:text-xl font-bold text-amber-200 drop-shadow-sm tracking-wide" x-text="subtitle"></p>
                            </template>

                            <!-- CTA Button -->
                            <template x-if="cta_text && cta_text.trim().length > 0">
                                <div class="pt-2">
                                    <span class="inline-flex items-center gap-2 bg-[#FF6600] text-white text-xs sm:text-sm font-extrabold uppercase px-6 sm:px-8 py-2.5 sm:py-3 rounded-full shadow-xl">
                                        <span x-text="cta_text"></span>
                                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                    </span>
                                </div>
                            </template>

                        </div>
                    </div>
                </template>

                <!-- Storefront Nav Arrows (Visual Indicator) -->
                <div class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md text-white flex items-center justify-center pointer-events-none shadow-md">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </div>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md text-white flex items-center justify-center pointer-events-none shadow-md">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </div>

                <!-- Slide Dots -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-20">
                    <span class="w-6 h-2 rounded-full bg-[#FF6600]"></span>
                    <span class="w-2 h-2 rounded-full bg-white/50"></span>
                    <span class="w-2 h-2 rounded-full bg-white/50"></span>
                </div>
            </div>

            <!-- ── 2. MOBILE PREVIEW CONTAINER ── -->
            <div x-show="previewMode === 'mobile'" class="max-w-xs mx-auto rounded-3xl overflow-hidden shadow-2xl relative border-4 border-slate-900 min-h-[340px] flex items-center" :style="'background-color: ' + bg_color">
                <!-- Mobile Artwork Image (or fallback to desktop image) -->
                <img 
                    :src="mobileImageUrl || mobileImagePreview || imageUrl || imagePreview" 
                    alt="Mobile Banner" 
                    class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
                    onerror="this.src='/hero_honey.png'"
                >
                
                <template x-if="hasTextOverlay()">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/60 to-black/30 pointer-events-none transition-opacity duration-300"></div>
                </template>

                <template x-if="hasTextOverlay()">
                    <div class="relative z-10 w-full p-5 text-white space-y-2.5">
                        <template x-if="badge && badge.trim().length > 0">
                            <div class="inline-flex items-center bg-[#FF6600] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full shadow-xs">
                                <span x-text="badge"></span>
                            </div>
                        </template>

                        <template x-if="(title_line1 && title_line1.trim().length > 0) || (title_line2 && title_line2.trim().length > 0)">
                            <h2 class="text-xl font-black uppercase tracking-tight text-white leading-tight">
                                <span x-text="title_line1"></span>
                                <template x-if="title_line2 && title_line2.trim().length > 0">
                                    <span class="text-amber-300 block" x-text="title_line2"></span>
                                </template>
                            </h2>
                        </template>

                        <template x-if="subtitle && subtitle.trim().length > 0">
                            <p class="text-xs font-bold text-amber-200" x-text="subtitle"></p>
                        </template>

                        <template x-if="cta_text && cta_text.trim().length > 0">
                            <div class="pt-1">
                                <span class="inline-flex items-center gap-1.5 bg-[#FF6600] text-white text-xs font-black uppercase px-4 py-2 rounded-full shadow-md">
                                    <span x-text="cta_text"></span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                </span>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Mobile Dots -->
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1 z-20">
                    <span class="w-4 h-1.5 rounded-full bg-[#FF6600]"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/50"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/50"></span>
                </div>
            </div>
        </div>

        <!-- ═══ CONFIGURATION FORM ═══ -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-8">
            
            <!-- ── SECTION 1: BANNER ARTWORK & IMAGES (REQUIRED) ── -->
            <div class="space-y-4 p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-[#002B49] text-white flex items-center justify-center text-xs font-black shadow-xs">🖼️</span>
                        Banner Artwork &amp; Media
                    </h3>
                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-full">
                        Upload or URL
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- 1. Desktop Banner Image -->
                    <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Desktop Banner Image
                            </label>
                            <span class="text-[10px] text-slate-400">1920x600px or 1200x500px</span>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                                <img :src="imageUrl || imagePreview" class="w-full h-full object-cover" onerror="this.src='/hero_honey.png'">
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <input type="file" name="image" @change="previewDesktopImage($event)" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950 file:text-emerald-700 dark:file:text-emerald-300 cursor-pointer">
                                <p class="text-[10px] text-slate-400">Upload banner image file</p>
                            </div>
                        </div>

                        <div class="pt-1">
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Or Direct Image URL:</label>
                            <input type="text" name="image_url" x-model="imageUrl" placeholder="https://example.com/banner.jpg or /hero_honey.png" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>

                    <!-- 2. Mobile Banner Image (Optional) -->
                    <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Mobile Banner Image (Optional)
                            </label>
                            <span class="text-[10px] text-slate-400">800x600px</span>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-20 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                                <template x-if="mobileImageUrl || mobileImagePreview">
                                    <img :src="mobileImageUrl || mobileImagePreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!mobileImageUrl && !mobileImagePreview">
                                    <span class="text-[10px] font-bold text-slate-400 text-center px-1">Auto from Desktop</span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <input type="file" name="mobile_image" @change="previewMobileImage($event)" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 dark:file:bg-sky-950 file:text-sky-700 dark:file:text-sky-300 cursor-pointer">
                                <p class="text-[10px] text-slate-400">Upload phone-optimized artwork</p>
                            </div>
                        </div>

                        <div class="pt-1">
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Or Mobile Image URL:</label>
                            <input type="text" name="mobile_image_url" x-model="mobileImageUrl" placeholder="https://example.com/mobile-banner.jpg" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── SECTION 2: HEADLINE & PROMOTIONAL COPY (COMPLETELY OPTIONAL) ── -->
            <div class="space-y-4 p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-[#FF6600] text-white flex items-center justify-center text-xs font-black shadow-xs">✍️</span>
                            Headline Typography &amp; Promotional Copy
                        </h3>
                        <p class="text-[11px] text-slate-400 pt-0.5">Completely optional — leave empty for clean image-only banners.</p>
                    </div>
                    <button type="button" @click="clearText()" class="text-xs font-bold text-rose-500 hover:text-rose-600 bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 px-3 py-1 rounded-xl transition cursor-pointer">
                        Clear All Text
                    </button>
                </div>

                <!-- Text Copy Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Tagline / Small Badge Pill (Optional)
                        </label>
                        <input type="text" name="badge" x-model="badge" placeholder="e.g. 100% PURE & NATURAL HARVEST" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Title Line 1 (Optional)
                        </label>
                        <input type="text" name="title_line1" x-model="title_line1" placeholder="e.g. PURE ORGANIC" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Title Line 2 (Optional)
                        </label>
                        <input type="text" name="title_line2" x-model="title_line2" placeholder="e.g. MEGA SALE" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Offer / Discount Highlight Subtitle (Optional)
                        </label>
                        <input type="text" name="subtitle" x-model="subtitle" placeholder="e.g. UP TO 40% OFF ON RAW SUNDARBAN HONEY" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            CTA Button Text (Optional)
                        </label>
                        <input type="text" name="cta_text" x-model="cta_text" placeholder="e.g. SHOP NOW (or leave blank)" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Target Link / Destination Route
                        </label>
                        <input type="text" name="cta_link" x-model="cta_link" placeholder="e.g. /all-products?category=organic-food" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono text-emerald-600 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Background Base Tint Color
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="bg_color" x-model="bg_color" class="w-10 h-10 rounded-xl cursor-pointer border border-slate-300 dark:border-slate-700 p-0.5 bg-white">
                            <input type="text" x-model="bg_color" class="flex-1 px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <!-- Color Swatches -->
                        <div class="flex items-center gap-1.5 pt-1">
                            <template x-for="hex in ['#002B49', '#2d1b2e', '#0b2b26', '#1e130c', '#0f2b3e', '#111827', '#b45309']" :key="hex">
                                <button 
                                    type="button" 
                                    @click="bg_color = hex" 
                                    class="w-5 h-5 rounded-full border border-white/20 shadow-xs transition-transform hover:scale-125 cursor-pointer"
                                    :style="'background-color: ' + hex"
                                    :title="hex"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 3: DISPLAY ORDER, PLACEMENT & PUBLISH STATUS ── -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Banner Placement Location
                    </label>
                    <select name="menu_location" x-model="menu_location" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition">
                        @foreach($menuLocations as $locKey => $locLabel)
                            <option value="{{ $locKey }}">{{ $locLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Display Sort Order
                    </label>
                    <input type="number" name="order" x-model="order" min="1" placeholder="1" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition">
                    <span class="text-[11px] text-slate-400">Slide order position on storefront (1 appears first).</span>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Publish Status
                    </label>
                    <select name="is_active" x-model="is_active" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition">
                        <option value="1">Active (Visible on Storefront)</option>
                        <option value="0">Inactive (Draft / Hidden)</option>
                    </select>
                </div>
            </div>

            <!-- Save Actions Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('admin.banners.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Save &amp; Publish Banner
                </button>
            </div>

        </div>

    </form>

</div>
@endsection
