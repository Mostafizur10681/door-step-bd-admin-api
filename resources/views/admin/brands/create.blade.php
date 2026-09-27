@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16">

    <!-- Breadcrumbs & Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.brands.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Brands</a>
            <span>&gt;</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Add Brand</span>
        </div>
        <div class="flex items-center justify-between pt-1">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Add Brand Card</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure brand presentation card, capabilities, specs, and inquiry action.</p>
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

    <div x-data="{
        form: {
            name: @js(old('name', '')),
            slug: @js(old('slug', '')),
            category_tag: @js(old('category_tag', '')),
            badge: @js(old('badge', '')),
            sub_title: @js(old('sub_title', '')),
            description: @js(old('description', '')),
            capacity_range: @js(old('capacity_range', '')),
            warranty_text: @js(old('warranty_text', '')),
            key_capabilities: @js(old('key_capabilities', '')),
            cta_text: @js(old('cta_text', '')),
            cta_link: @js(old('cta_link', '')),
            is_active: {{ old('is_active', 1) ? 'true' : 'false' }}
        },
        previewUrl: null,
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
            } else {
                this.previewUrl = null;
            }
        },
        getCapabilitiesList() {
            if (!this.form.key_capabilities) {
                return [
                    'Industrial Heavy Diesel Generators',
                    'Custom Acoustic Soundproof Canopies',
                    'Auto-Synchronizing Multi-Genset Panels',
                    'Automatic Main Failure (AMF) Logic'
                ];
            }
            return this.form.key_capabilities.split('\n').map(s => s.trim()).filter(Boolean);
        }
    }" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Column (Left 7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Basic Brand Identity Section -->
                <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-2">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Brand Identification
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Brand Name -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="brand_name" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Brand Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="brand_name" 
                            name="name" 
                            x-model="form.name"
                            @input="updateSlug()"
                            required 
                            placeholder="e.g. Doorstep Power Solutions™" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-medium text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"
                        >
                    </div>

                    <!-- Slug Identifier (Readonly & Automatic) -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <div class="flex items-center justify-between">
                            <label for="brand_slug" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                                Slug Identifier <span class="text-[11px] font-normal text-emerald-600 dark:text-emerald-400">(Automatic & Readonly)</span>
                            </label>
                            <span class="text-[10px] text-slate-400 font-mono">Auto-generated from Name</span>
                        </div>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="brand_slug" 
                                name="slug" 
                                x-model="form.slug"
                                readonly
                                tabindex="-1"
                                placeholder="Generated automatically from brand name..." 
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-mono text-slate-600 dark:text-slate-300 cursor-not-allowed select-none focus:outline-none transition-all"
                            >
                            <span class="absolute right-3.5 top-2.5 text-xs text-slate-400 select-none">🔒</span>
                        </div>
                    </div>

                    <!-- Category / Sector Tag -->
                    <div class="space-y-1.5">
                        <label for="brand_category_tag" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Category Tag / Sector (Top Left)
                        </label>
                        <input 
                            type="text" 
                            id="brand_category_tag" 
                            name="category_tag" 
                            x-model="form.category_tag"
                            placeholder="e.g. HEAVY POWER GENERATION" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all"
                        >
                    </div>

                    <!-- Badge -->
                    <div class="space-y-1.5">
                        <label for="brand_badge" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Brand Badge (Top Right)
                        </label>
                        <input 
                            type="text" 
                            id="brand_badge" 
                            name="badge" 
                            x-model="form.badge"
                            placeholder="e.g. Flagship Brand, Verified" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all"
                        >
                    </div>

                    <!-- Sub Title / Tagline -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="brand_sub_title" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Sub-Title / Industry Line (Above Title)
                        </label>
                        <input 
                            type="text" 
                            id="brand_sub_title" 
                            name="sub_title" 
                            x-model="form.sub_title"
                            placeholder="e.g. GENERATORS & SYNCHRONIZATION" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-semibold text-amber-700 dark:text-amber-400 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all uppercase tracking-wide"
                        >
                    </div>
                </div>

                <!-- Logo & Media -->
                <div class="space-y-1.5 pt-2">
                    <label for="brand_logo" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                        Brand Logo / Icon
                    </label>
                    <div class="flex items-center gap-4 p-3 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-slate-50/50 dark:bg-slate-950/50">
                        <input 
                            type="file" 
                            name="logo" 
                            id="brand_logo"
                            accept="image/*" 
                            @change="handleFileChange($event)"
                            class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 dark:file:bg-emerald-950/60 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100 dark:hover:file:bg-emerald-900/60 cursor-pointer"
                        >
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label for="brand_description" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                        Brand Description
                    </label>
                    <textarea 
                        id="brand_description" 
                        name="description" 
                        x-model="form.description"
                        rows="3" 
                        placeholder="Heavy-duty continuous & standby industrial diesel generators, custom soundproof acoustic canopies..." 
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all leading-relaxed"
                    ></textarea>
                </div>

                <!-- Specs & Features Section -->
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 pt-3 mb-2">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        Card Specs & Capabilities
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Capacity Range -->
                    <div class="space-y-1.5">
                        <label for="brand_capacity_range" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Capacity Range / Specification
                        </label>
                        <input 
                            type="text" 
                            id="brand_capacity_range" 
                            name="capacity_range" 
                            x-model="form.capacity_range"
                            placeholder="e.g. 50kVA - 3000kVA" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all"
                        >
                    </div>

                    <!-- Warranty Text -->
                    <div class="space-y-1.5">
                        <label for="brand_warranty_text" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Warranty / Guarantee Badge
                        </label>
                        <input 
                            type="text" 
                            id="brand_warranty_text" 
                            name="warranty_text" 
                            x-model="form.warranty_text"
                            placeholder="e.g. Full Factory Warranty & AMC" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all"
                        >
                    </div>

                    <!-- Key Capabilities (one per line) -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <div class="flex items-center justify-between">
                            <label for="brand_key_capabilities" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                                Key Capabilities Checklist
                            </label>
                            <span class="text-[11px] text-slate-400">One bullet point per line</span>
                        </div>
                        <textarea 
                            id="brand_key_capabilities" 
                            name="key_capabilities" 
                            x-model="form.key_capabilities"
                            rows="4" 
                            placeholder="Industrial Heavy Diesel Generators&#10;Custom Acoustic Soundproof Canopies&#10;Auto-Synchronizing Multi-Genset Panels&#10;Automatic Main Failure (AMF) Logic" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all leading-relaxed"
                        ></textarea>
                    </div>

                    <!-- CTA Text -->
                    <div class="space-y-1.5">
                        <label for="brand_cta_text" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Inquiry Action Button Text
                        </label>
                        <input 
                            type="text" 
                            id="brand_cta_text" 
                            name="cta_text" 
                            x-model="form.cta_text"
                            placeholder="e.g. Inquire About Heavy Power Generation" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all"
                        >
                    </div>

                    <!-- CTA Link / Phone -->
                    <div class="space-y-1.5">
                        <label for="brand_cta_link" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Action Target URL or Phone
                        </label>
                        <input 
                            type="text" 
                            id="brand_cta_link" 
                            name="cta_link" 
                            x-model="form.cta_link"
                            placeholder="e.g. tel:+8801700000000 or /contact" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all"
                        >
                    </div>
                </div>

                <!-- Active Status -->
                <div class="pt-3">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1" 
                            x-model="form.is_active"
                            class="rounded-lg border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 h-4 w-4 cursor-pointer"
                        >
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Active & Published</span>
                            <p class="text-[11px] text-slate-400">Display this brand card live in the customer brand showcase.</p>
                        </div>
                    </label>
                </div>

                <!-- Form Controls -->
                <div class="pt-5 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <a 
                        href="{{ route('admin.brands.index') }}" 
                        class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-all"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
                    >
                        Save Brand Card
                    </button>
                </div>
            </form>
        </div>

        <!-- Real-Time Interactive Live Card Preview (Right 5 Cols - Theme Aware) -->
        <div class="lg:col-span-5 sticky top-24 space-y-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Live Card Preview
                </span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-semibold">
                    Realtime Render
                </span>
            </div>

            <!-- Card Box -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-[28px] p-6 shadow-xl shadow-slate-200/50 dark:shadow-none transition-all relative overflow-hidden font-sans">
                
                <!-- Top Tags Row -->
                <div class="flex items-center justify-between gap-2 mb-4">
                    <!-- Sector / Category Tag -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-extrabold tracking-wide uppercase bg-sky-50 dark:bg-sky-950/50 text-[#0f3460] dark:text-sky-300 border border-sky-100 dark:border-sky-900/60">
                        <svg class="h-3.5 w-3.5 text-[#0f3460] dark:text-sky-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span x-text="form.category_tag || 'HEAVY POWER GENERATION'"></span>
                    </div>

                    <!-- Flagship / Feature Badge -->
                    <div class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-black bg-[#f59e0b] dark:bg-amber-500 text-slate-950 shadow-sm">
                        <span x-text="form.badge || 'Flagship Brand'"></span>
                    </div>
                </div>

                <!-- Brand Header Row (Logo + Subtitle + Title) -->
                <div class="flex items-start gap-3.5 mb-3.5">
                    <!-- Brand Icon Box -->
                    <div class="h-12 w-12 rounded-2xl bg-[#0e2a47] dark:bg-slate-950 border border-blue-900/40 dark:border-slate-800 shrink-0 flex items-center justify-center shadow-md overflow-hidden p-1.5">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Brand Logo" class="h-full w-full object-contain">
                        </template>
                        <template x-if="!previewUrl">
                            <svg class="h-6 w-6 text-[#f59e0b] dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 2L3 14h8v8l10-12h-8l0-8z"/>
                            </svg>
                        </template>
                    </div>

                    <!-- Subtitle + Name -->
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-extrabold text-[#b45309] dark:text-amber-400 uppercase tracking-wider leading-tight" x-text="form.sub_title || 'GENERATORS & SYNCHRONIZATION'"></p>
                        <h3 class="text-xl font-black text-[#0f2e5a] dark:text-white tracking-tight leading-tight mt-0.5" x-text="form.name || 'Doorstep Power Solutions™'"></h3>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4" x-text="form.description || 'Heavy-duty continuous & standby industrial diesel generators, custom soundproof acoustic canopies, and automated load-sharing synchronization systems.'"></p>

                <!-- Capacity Range Box -->
                <div class="p-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs mb-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Capacity Range:</span>
                    <span class="font-extrabold text-[#0f2e5a] dark:text-sky-300" x-text="form.capacity_range || '50kVA - 3000kVA'"></span>
                </div>

                <!-- Warranty Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50/90 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 mb-4 w-fit">
                    <svg class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-text="form.warranty_text || 'Full Factory Warranty & AMC'"></span>
                </div>

                <!-- Key Capabilities List -->
                <div class="space-y-2 mb-6">
                    <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-wider">KEY CAPABILITIES:</p>
                    <ul class="space-y-1.5 text-xs text-slate-700 dark:text-slate-200">
                        <template x-for="item in getCapabilitiesList()" :key="item">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium" x-text="item"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Inquiry CTA Button -->
                <button type="button" class="w-full py-3.5 px-4 rounded-2xl bg-[#0e2a47] hover:bg-[#163a63] dark:bg-slate-800 dark:hover:bg-slate-700/90 text-[#f59e0b] dark:text-amber-300 border border-transparent dark:border-slate-700/80 font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-blue-950/20 dark:shadow-none transition-all cursor-pointer">
                    <span x-text="form.cta_text || 'Inquire About Heavy Power Generation'"></span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </button>
            </div>

        </div>

    </div>

</div>
@endsection
