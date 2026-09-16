@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-28 px-2 sm:px-4 lg:px-6" x-data="{
    activeTab: 'brand',
    isSubmitting: false,
    tabs: [
        { id: 'brand', title: 'Brand & Socials', icon: '🎨', num: '1' },
        { id: 'contact', title: 'Help & Hours', icon: '📞', num: '2' },
        { id: 'links', title: 'Link Columns', icon: '🔗', num: '3' },
        { id: 'bottom', title: 'Copyright & Payment', icon: '💳', num: '4' },
        { id: 'preview', title: 'Live Preview', icon: '👁️', num: '5' }
    ],

    // Brand & Logo
    storeName: {{ Js::from(old('store_name', $footer->store_name ?? 'Shopia')) }},
    address: {{ Js::from(old('address', $footer->address ?? "41/1, Sher-E-Bangla Rd,\nMohammadpur, Dhaka 1207")) }},
    mapUrl: {{ Js::from(old('map_url', $footer->map_url ?? 'https://maps.google.com/?q=Mohammadpur+Dhaka')) }},
    logoPreview: {{ Js::from($footer->logo_image ?? '') }},
    logoBase64: '',
    removeLogo: false,

    // Social Links
    facebookUrl: {{ Js::from(old('facebook_url', $footer->facebook_url ?? 'https://facebook.com/shopiabd')) }},
    instagramUrl: {{ Js::from(old('instagram_url', $footer->instagram_url ?? 'https://instagram.com/shopiabd')) }},
    youtubeUrl: {{ Js::from(old('youtube_url', $footer->youtube_url ?? 'https://youtube.com/@shopiabd')) }},
    pinterestUrl: {{ Js::from(old('pinterest_url', $footer->pinterest_url ?? 'https://pinterest.com/shopiabd')) }},
    linkedinUrl: {{ Js::from(old('linkedin_url', $footer->linkedin_url ?? 'https://linkedin.com/company/shopiabd')) }},
    twitterUrl: {{ Js::from(old('twitter_url', $footer->twitter_url ?? 'https://twitter.com/shopiabd')) }},
    tiktokUrl: {{ Js::from(old('tiktok_url', $footer->tiktok_url ?? '')) }},

    // Help & Hours
    contactPhone: {{ Js::from(old('contact_phone', $footer->contact_phone ?? '01681-135030')) }},
    workingHours1: {{ Js::from(old('working_hours_1', $footer->working_hours_1 ?? 'Saturday- Thursday: 9:00am- 10:00pm')) }},
    workingHours2: {{ Js::from(old('working_hours_2', $footer->working_hours_2 ?? 'Friday: 15:00pm – 11:00pm')) }},
    contactEmail: {{ Js::from(old('contact_email', $footer->contact_email ?? 'info@shopiabd.com')) }},

    // Navigation Columns
    col1Title: {{ Js::from(old('column_1_title', $footer->column_1_title ?? 'Information')) }},
    col1Links: {{ Js::from(old('column_1_links', !empty($footer->column_1_links) ? $footer->column_1_links : [
        ['label' => 'About us', 'url' => '/about'],
        ['label' => 'Blog & Journal', 'url' => '/blog'],
        ['label' => 'FAQ & Support', 'url' => '/faq'],
        ['label' => 'Delivery information', 'url' => '/delivery'],
        ['label' => 'Privacy Policy', 'url' => '/privacy'],
        ['label' => 'Sales', 'url' => '/sales'],
        ['label' => 'Terms & Conditions', 'url' => '/terms']
    ])) }},

    col2Title: {{ Js::from(old('column_2_title', $footer->column_2_title ?? 'Account')) }},
    col2Links: {{ Js::from(old('column_2_links', !empty($footer->column_2_links) ? $footer->column_2_links : [
        ['label' => 'My account', 'url' => '/account'],
        ['label' => 'My orders', 'url' => '/dashboard?tab=orders'],
        ['label' => 'Returns', 'url' => '/returns'],
        ['label' => 'Shipping', 'url' => '/shipping'],
        ['label' => 'Wishlist', 'url' => '/wishlist']
    ])) }},

    col3Title: {{ Js::from(old('column_3_title', $footer->column_3_title ?? 'Store')) }},
    col3Links: {{ Js::from(old('column_3_links', !empty($footer->column_3_links) ? $footer->column_3_links : [
        ['label' => 'Bestsellers', 'url' => '/bestsellers'],
        ['label' => 'Discount', 'url' => '/discount'],
        ['label' => 'Latest products', 'url' => '/latest'],
        ['label' => 'Sale', 'url' => '/sale']
    ])) }},

    // Copyright & Payment Badges
    copyrightText: {{ Js::from(old('copyright_text', $footer->copyright_text ?? 'Copyright © ' . date('Y') . ' Shopia. All Rights Reserved')) }},
    paymentMethods: {{ Js::from(old('payment_methods', !empty($footer->payment_methods) ? $footer->payment_methods : ['BKASH', 'ROCKET', 'NAGAD', 'VISA', 'MASTERCARD', 'AMEX'])) }},
    newPaymentMethod: '',

    get currentTabIndex() {
        return this.tabs.findIndex(t => t.id === this.activeTab);
    },
    get currentTabObject() {
        return this.tabs.find(t => t.id === this.activeTab) || this.tabs[0];
    },

    nextTab() {
        const nextIdx = this.currentTabIndex + 1;
        if (nextIdx < this.tabs.length) {
            this.activeTab = this.tabs[nextIdx].id;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },
    prevTab() {
        const prevIdx = this.currentTabIndex - 1;
        if (prevIdx >= 0) {
            this.activeTab = this.tabs[prevIdx].id;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },

    previewLogo(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('File size exceeds 5MB limit.');
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                this.logoPreview = e.target.result;
                this.logoBase64 = e.target.result;
                this.removeLogo = false;
            };
            reader.readAsDataURL(file);
        }
    },
    clearLogo() {
        this.logoPreview = '';
        this.logoBase64 = '';
        this.removeLogo = true;
        if (this.$refs.logoFileInput) {
            this.$refs.logoFileInput.value = '';
        }
    },

    addLink(column) {
        if (column === 1) this.col1Links.push({ label: '', url: '' });
        if (column === 2) this.col2Links.push({ label: '', url: '' });
        if (column === 3) this.col3Links.push({ label: '', url: '' });
    },
    removeLink(column, index) {
        if (column === 1) this.col1Links.splice(index, 1);
        if (column === 2) this.col2Links.splice(index, 1);
        if (column === 3) this.col3Links.splice(index, 1);
    },

    addPaymentMethod(name) {
        const val = (name || this.newPaymentMethod).trim().toUpperCase();
        if (val && !this.paymentMethods.includes(val)) {
            this.paymentMethods.push(val);
            this.newPaymentMethod = '';
        }
    },
    removePaymentMethod(index) {
        this.paymentMethods.splice(index, 1);
    }
}">

    <!-- Top Header & Breadcrumbs Card -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-4 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <x-breadcrumbs :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Footer Settings']
                ]" />
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Footer &amp; Store Branding
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Sync
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Manage global footer brand identity, hotline hours, navigation columns, and payment methods.
                </p>
            </div>

            <!-- Action Buttons for Desktop / Tablet -->
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <a href="http://localhost:3000" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Storefront</span>
                </a>
            </div>
        </div>

        <!-- Section Progress Indicator (Exact About Page Pattern) -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 font-bold text-slate-700 dark:text-slate-300">
                <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-[11px]" x-text="currentTabObject.num"></span>
                <span x-text="'Section ' + currentTabObject.num + ' of 5: ' + currentTabObject.title"></span>
            </div>
            <div class="flex items-center gap-1">
                <template x-for="(tab, idx) in tabs" :key="tab.id">
                    <button type="button" @click="activeTab = tab.id" :title="tab.title" class="h-2 rounded-full transition-all duration-300 cursor-pointer" :class="activeTab === tab.id ? 'w-6 bg-emerald-600' : 'w-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300'"></button>
                </template>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-xs animate-in fade-in duration-200">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="p-1 rounded-lg text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-medium space-y-1.5 shadow-xs">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700 dark:text-rose-300 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Editor Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        
        <!-- 1. Mobile Quick Jump Dropdown Menu (<640px) -->
        <div class="sm:hidden p-3 bg-slate-50 dark:bg-slate-950/80 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between gap-2 mb-1.5">
                <label class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Select Section to Edit:
                </label>
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400" x-text="'Step ' + currentTabObject.num + ' of 5'"></span>
            </div>
            <div class="relative">
                <select x-model="activeTab" class="w-full pl-3.5 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 appearance-none shadow-xs">
                    <template x-for="tab in tabs" :key="tab.id">
                        <option :value="tab.id" x-text="tab.icon + ' ' + tab.num + '. ' + tab.title"></option>
                    </template>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- 2. Tablet & Desktop Responsive Navigation Chips (640px and up) -->
        <div class="hidden sm:block border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-950/50 p-2.5 sm:p-3 overflow-x-auto scrollbar-none">
            <div class="flex items-center gap-1.5 md:gap-2 min-w-max">
                <template x-for="tab in tabs" :key="tab.id">
                    <button 
                        type="button" 
                        @click="activeTab = tab.id" 
                        :class="activeTab === tab.id ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium'"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap cursor-pointer"
                    >
                        <span class="text-sm" x-text="tab.icon"></span>
                        <span x-text="tab.title"></span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="activeTab === tab.id ? 'bg-emerald-700/80 text-emerald-100' : 'bg-slate-200/80 dark:bg-slate-800 text-slate-500 dark:text-slate-400'" x-text="tab.num"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Form Element -->
        <form id="footerSettingsForm" x-ref="footerForm" method="POST" action="{{ route('admin.footer-settings.update') }}" enctype="multipart/form-data" @submit="isSubmitting = true" class="p-4 sm:p-6 lg:p-8 space-y-8">
            @csrf

            <!-- Hidden inputs for Logo & Arrays -->
            <input type="hidden" name="logo_image_base64" :value="logoBase64">
            <input type="hidden" name="remove_logo_image" :value="removeLogo ? 1 : 0">

            <!-- ================= SECTION 1: BRAND & LOGO & SOCIALS ================= -->
            <div x-show="activeTab === 'brand'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🎨</span> Section 1: Brand Identity &amp; Social Profiles
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Upload custom brand logo image, manage store address, map link, and public social media channels.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 1 of 5
                    </span>
                </div>

                <!-- 2-Column Responsive Layout: Logo (Left) and Store Info (Right) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <!-- Left: Logo Upload & Live Preview Card -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Footer Logo Image
                        </label>
                        
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-6 bg-slate-50 dark:bg-slate-950/60 flex flex-col items-center justify-center text-center relative group min-h-[220px]">
                            <template x-if="logoPreview">
                                <div class="space-y-4 w-full flex flex-col items-center">
                                    <div class="p-4 bg-[#0b3b82] rounded-2xl border border-blue-900 flex items-center justify-center w-full max-w-sm shadow-inner">
                                        <img :src="logoPreview" alt="Logo Preview" class="max-h-16 max-w-full object-contain">
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap justify-center">
                                        <label class="cursor-pointer px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 shadow-2xs">
                                            <span>Change Logo</span>
                                            <input type="file" x-ref="logoFileInput" name="logo_image" accept="image/*" @change="previewLogo($event)" class="hidden">
                                        </label>
                                        <button type="button" @click="clearLogo()" class="px-4 py-2 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-bold hover:bg-rose-100 cursor-pointer">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!logoPreview">
                                <div class="space-y-3 w-full flex flex-col items-center">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-2xs">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="text-xs text-slate-600 dark:text-slate-400">
                                        <label class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                                            <span>Click to upload custom logo</span>
                                            <input type="file" x-ref="logoFileInput" name="logo_image" accept="image/*" @change="previewLogo($event)" class="hidden">
                                        </label>
                                        <p class="text-[11px] text-slate-400 mt-1">PNG, JPG, SVG, WebP (Saved directly to DB as Base64)</p>
                                    </div>
                                    <div class="pt-2">
                                        <div class="px-4 py-2 bg-[#0b3b82] text-white rounded-xl text-center inline-block shadow-xs">
                                            <span class="text-lg font-black italic tracking-tighter text-[#b30047]">
                                                S<span class="text-[#e60000]">HOPIA</span>
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-1">Default brand typography fallback</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Right: Store Details Form -->
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Store / Brand Name</label>
                            <input type="text" name="store_name" x-model="storeName" placeholder="Shopia" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Address (Multi-line)</label>
                            <textarea name="address" x-model="address" rows="3" placeholder="41/1, Sher-E-Bangla Rd,&#10;Mohammadpur, Dhaka 1207" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono transition leading-relaxed"></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">"Show on map" URL</label>
                            <input type="text" name="map_url" x-model="mapUrl" placeholder="https://maps.google.com/?q=..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono text-emerald-600 dark:text-emerald-400 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        </div>
                    </div>
                </div>

                <!-- Full-Width Social Channels Section -->
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Social Media Channels</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add profile URLs. Blank fields will automatically hide that social icon.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Facebook URL
                            </label>
                            <input type="url" name="facebook_url" x-model="facebookUrl" placeholder="https://facebook.com/shopiabd" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-pink-600"></span> Instagram URL
                            </label>
                            <input type="url" name="instagram_url" x-model="instagramUrl" placeholder="https://instagram.com/shopiabd" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> YouTube URL
                            </label>
                            <input type="url" name="youtube_url" x-model="youtubeUrl" placeholder="https://youtube.com/@shopiabd" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-700"></span> Pinterest URL
                            </label>
                            <input type="url" name="pinterest_url" x-model="pinterestUrl" placeholder="https://pinterest.com/shopiabd" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-700"></span> LinkedIn URL
                            </label>
                            <input type="url" name="linkedin_url" x-model="linkedinUrl" placeholder="https://linkedin.com/company/shopiabd" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> Twitter (X) URL
                            </label>
                            <input type="url" name="twitter_url" x-model="twitterUrl" placeholder="https://twitter.com/shopiabd" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono">
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= SECTION 2: HELP & OPERATING HOURS ================= -->
            <div x-show="activeTab === 'contact'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📞</span> Section 2: Need Help Helpline &amp; Schedule
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Configure customer support helpline phone, email, and live weekly service schedule.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 2 of 5
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Customer Support Helpline (Phone)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-amber-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <input type="text" name="contact_phone" x-model="contactPhone" placeholder="01681-135030" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-mono font-bold focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Support Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-amber-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input type="email" name="contact_email" x-model="contactEmail" placeholder="info@shopiabd.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Working Hours (Line 1: Sat - Thu)</label>
                        <input type="text" name="working_hours_1" x-model="workingHours1" placeholder="Saturday- Thursday: 9:00am- 10:00pm" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Working Hours (Line 2: Friday)</label>
                        <input type="text" name="working_hours_2" x-model="workingHours2" placeholder="Friday: 15:00pm – 11:00pm" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="p-6 rounded-2xl bg-[#0b3b82] text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-lg border border-blue-900">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-400/20 border border-amber-400/30 flex items-center justify-center text-amber-300 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-amber-300 font-bold uppercase tracking-wider">Customer Hotline Preview</span>
                            <h4 class="text-xl font-black font-mono" x-text="contactPhone || '01681-135030'"></h4>
                            <p class="text-xs text-blue-200" x-text="workingHours1"></p>
                        </div>
                    </div>
                    <div class="text-xs text-blue-200/90 font-mono bg-blue-950/60 px-4 py-2 rounded-xl border border-blue-800" x-text="contactEmail"></div>
                </div>

            </div>

            <!-- ================= SECTION 3: LINK COLUMNS ================= -->
            <div x-show="activeTab === 'links'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🔗</span> Section 3: Footer Navigation Link Columns
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Organize navigation links into 3 distinct footer columns with dynamic additions and title customization.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 3 of 5
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Column 1: Information -->
                    <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white">Column 1</h4>
                            </div>
                            <button type="button" @click="addLink(1)" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs font-bold hover:bg-emerald-100 flex items-center gap-1 cursor-pointer">
                                <span>+ Add Link</span>
                            </button>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Column Title</label>
                            <input type="text" name="column_1_title" x-model="col1Title" placeholder="Information" class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-2.5 max-h-[400px] overflow-y-auto pr-1">
                            <template x-for="(link, idx) in col1Links" :key="idx">
                                <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 relative group shadow-2xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-mono text-slate-400" x-text="'Link #' + (idx + 1)"></span>
                                        <button type="button" @click="removeLink(1, idx)" class="text-rose-500 hover:text-rose-700 text-xs p-1 cursor-pointer" title="Remove link">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <input type="text" :name="'column_1_links['+idx+'][label]'" x-model="link.label" placeholder="Link Label (e.g. About us)" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                                    <input type="text" :name="'column_1_links['+idx+'][url]'" x-model="link.url" placeholder="URL (e.g. /about)" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-mono text-emerald-600 dark:text-emerald-400 focus:outline-none focus:border-emerald-500">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Column 2: Account -->
                    <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white">Column 2</h4>
                            </div>
                            <button type="button" @click="addLink(2)" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs font-bold hover:bg-emerald-100 flex items-center gap-1 cursor-pointer">
                                <span>+ Add Link</span>
                            </button>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Column Title</label>
                            <input type="text" name="column_2_title" x-model="col2Title" placeholder="Account" class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-2.5 max-h-[400px] overflow-y-auto pr-1">
                            <template x-for="(link, idx) in col2Links" :key="idx">
                                <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 relative group shadow-2xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-mono text-slate-400" x-text="'Link #' + (idx + 1)"></span>
                                        <button type="button" @click="removeLink(2, idx)" class="text-rose-500 hover:text-rose-700 text-xs p-1 cursor-pointer" title="Remove link">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <input type="text" :name="'column_2_links['+idx+'][label]'" x-model="link.label" placeholder="Link Label (e.g. My orders)" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                                    <input type="text" :name="'column_2_links['+idx+'][url]'" x-model="link.url" placeholder="URL (e.g. /dashboard?tab=orders)" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-mono text-emerald-600 dark:text-emerald-400 focus:outline-none focus:border-emerald-500">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Column 3: Store -->
                    <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white">Column 3</h4>
                            </div>
                            <button type="button" @click="addLink(3)" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs font-bold hover:bg-emerald-100 flex items-center gap-1 cursor-pointer">
                                <span>+ Add Link</span>
                            </button>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Column Title</label>
                            <input type="text" name="column_3_title" x-model="col3Title" placeholder="Store" class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-2.5 max-h-[400px] overflow-y-auto pr-1">
                            <template x-for="(link, idx) in col3Links" :key="idx">
                                <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 relative group shadow-2xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-mono text-slate-400" x-text="'Link #' + (idx + 1)"></span>
                                        <button type="button" @click="removeLink(3, idx)" class="text-rose-500 hover:text-rose-700 text-xs p-1 cursor-pointer" title="Remove link">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <input type="text" :name="'column_3_links['+idx+'][label]'" x-model="link.label" placeholder="Link Label (e.g. Bestsellers)" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                                    <input type="text" :name="'column_3_links['+idx+'][url]'" x-model="link.url" placeholder="URL (e.g. /bestsellers)" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-mono text-emerald-600 dark:text-emerald-400 focus:outline-none focus:border-emerald-500">
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ================= SECTION 4: BOTTOM BAR & PAYMENT BADGES ================= -->
            <div x-show="activeTab === 'bottom'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>💳</span> Section 4: Copyright Notice &amp; Payment Badges
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Manage copyright text and accepted payment badges displayed in the footer bottom bar.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 4 of 5
                    </span>
                </div>

                <div class="space-y-6">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Copyright Text</label>
                        <input type="text" name="copyright_text" x-model="copyrightText" placeholder="Copyright © 2026 Shopia. All Rights Reserved" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Accepted Payment Methods (Badges)</label>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5">
                            <input type="text" x-model="newPaymentMethod" @keydown.enter.prevent="addPaymentMethod()" placeholder="Type custom method (e.g. BKASH, VISA) and press Enter" class="w-full sm:max-w-md px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono font-bold uppercase text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
                            <button type="button" @click="addPaymentMethod()" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shrink-0 cursor-pointer">
                                + Add Badge
                            </button>
                        </div>

                        <!-- Quick suggestion tags -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-1 text-xs text-slate-500">
                            <span class="font-bold text-[11px]">Quick Add:</span>
                            <template x-for="sug in ['BKASH', 'ROCKET', 'NAGAD', 'VISA', 'MASTERCARD', 'AMEX', 'CASH ON DELIVERY']" :key="sug">
                                <button type="button" @click="addPaymentMethod(sug)" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer" x-text="'+ ' + sug"></button>
                            </template>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <template x-for="(method, idx) in paymentMethods" :key="idx">
                                <span class="inline-flex items-center gap-2 px-3.5 py-2 bg-[#0b3b82] text-white rounded-xl text-xs font-black tracking-wider uppercase border border-white/20 shadow-xs">
                                    <span x-text="method"></span>
                                    <input type="hidden" :name="'payment_methods['+idx+']'" :value="method">
                                    <button type="button" @click="removePaymentMethod(idx)" class="text-blue-200 hover:text-white transition p-0.5 cursor-pointer" title="Remove badge">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= SECTION 5: LIVE WEBSITE FOOTER PREVIEW ================= -->
            <div x-show="activeTab === 'preview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>👁️</span> Section 5: Live Website Footer Preview
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Real-time simulation of how the global footer appears to customers on the storefront.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 5 of 5
                    </span>
                </div>

                <div class="bg-[#0b3b82] text-blue-100 rounded-3xl p-6 sm:p-8 lg:p-10 shadow-2xl border border-blue-900 font-sans">
                    
                    <div class="flex items-center justify-between pb-5 border-b border-blue-900/80 mb-6 sm:mb-8">
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-300">Live Website Footer Preview</span>
                        <span class="text-[11px] text-blue-200/70 bg-blue-950/60 px-2.5 py-0.5 rounded-full border border-blue-800">Auto Updates</span>
                    </div>

                    <!-- Responsive Grid: Collapses gracefully on Mobile, Tablet & Desktop -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 pb-8 border-b border-blue-900/60 text-xs sm:text-sm">
                        
                        <!-- Col 1: Logo & Address -->
                        <div class="md:col-span-4 space-y-3.5">
                            <div>
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" alt="Store Logo" class="max-h-12 max-w-full object-contain mb-2">
                                </template>
                                <template x-if="!logoPreview">
                                    <span class="text-3xl font-black italic tracking-tighter text-[#b30047] inline-block">
                                        S<span class="text-[#e60000]">HOPIA</span>
                                    </span>
                                </template>
                            </div>

                            <div class="text-blue-100/80 text-xs leading-relaxed whitespace-pre-line" x-text="address"></div>

                            <div class="pt-0.5">
                                <a :href="mapUrl" target="_blank" class="text-amber-300 underline font-semibold hover:text-white transition text-xs">
                                    Show on map
                                </a>
                            </div>

                            <!-- Social Icons -->
                            <div class="flex items-center gap-2.5 text-blue-200 pt-1 flex-wrap">
                                <template x-if="facebookUrl">
                                    <span class="p-1.5 text-white bg-blue-900/50 rounded-full"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></span>
                                </template>
                                <template x-if="instagramUrl">
                                    <span class="p-1.5 text-white bg-blue-900/50 rounded-full"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></span>
                                </template>
                                <template x-if="youtubeUrl">
                                    <span class="p-1.5 text-white bg-blue-900/50 rounded-full"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg></span>
                                </template>
                                <template x-if="pinterestUrl">
                                    <span class="w-6 h-6 flex items-center justify-center font-bold text-xs bg-blue-900/50 text-white rounded-full">P</span>
                                </template>
                                <template x-if="linkedinUrl">
                                    <span class="p-1.5 text-white bg-blue-900/50 rounded-full"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.239-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></span>
                                </template>
                            </div>
                        </div>

                        <!-- Col 2: Need Help Hotline -->
                        <div class="md:col-span-3 md:border-l border-blue-900/60 md:pl-6 space-y-3.5">
                            <h3 class="font-bold text-white text-sm">Need help</h3>

                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 text-white">
                                    <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span class="text-lg sm:text-xl font-black tracking-tight font-mono" x-text="contactPhone"></span>
                                </div>

                                <div class="text-[11px] text-blue-200/80 space-y-0.5 pl-7">
                                    <p x-text="workingHours1"></p>
                                    <p x-text="workingHours2"></p>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-blue-900/60 flex items-center gap-2 text-blue-200/90 text-xs">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span x-text="contactEmail"></span>
                            </div>
                        </div>

                        <!-- Col 3: Information -->
                        <div class="md:col-span-2 space-y-2.5">
                            <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wide" x-text="col1Title"></h3>
                            <ul class="space-y-1.5 text-xs text-blue-100/80">
                                <template x-for="(l, i) in col1Links" :key="i">
                                    <li x-text="l.label"></li>
                                </template>
                            </ul>
                        </div>

                        <!-- Col 4: Account -->
                        <div class="md:col-span-2 space-y-2.5">
                            <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wide" x-text="col2Title"></h3>
                            <ul class="space-y-1.5 text-xs text-blue-100/80">
                                <template x-for="(l, i) in col2Links" :key="i">
                                    <li x-text="l.label"></li>
                                </template>
                            </ul>
                        </div>

                        <!-- Col 5: Store -->
                        <div class="md:col-span-1 space-y-2.5">
                            <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wide" x-text="col3Title"></h3>
                            <ul class="space-y-1.5 text-xs text-blue-100/80">
                                <template x-for="(l, i) in col3Links" :key="i">
                                    <li x-text="l.label"></li>
                                </template>
                            </ul>
                        </div>

                    </div>

                    <!-- Bottom Bar -->
                    <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-blue-200/80 text-center sm:text-left">
                        <p x-text="copyrightText"></p>

                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap justify-center">
                            <template x-for="(badge, idx) in paymentMethods" :key="idx">
                                <span class="px-2.5 py-1 bg-white/10 text-white font-black rounded-lg text-[10px] uppercase border border-white/20" x-text="badge"></span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Wizard Step Navigation & Save Bar (In-Form Bottom) -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- Previous Step Button -->
                <div class="w-full sm:w-auto flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="prevTab()" 
                        x-show="currentTabIndex > 0"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition shadow-xs cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Previous Section</span>
                    </button>
                </div>

                <!-- Next Step or Final Save -->
                <div class="w-full sm:w-auto flex items-center gap-3">
                    <button 
                        type="button" 
                        @click="nextTab()" 
                        x-show="currentTabIndex < tabs.length - 1"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-200/80 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 text-xs font-bold rounded-xl transition shadow-xs cursor-pointer"
                    >
                        <span>Next Section</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button 
                        type="submit" 
                        form="footerSettingsForm"
                        :disabled="isSubmitting" 
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer"
                    >
                        <svg class="w-4 h-4" :class="isSubmitting ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="isSubmitting ? 'Saving...' : 'Save All Changes'"></span>
                    </button>
                </div>

            </div>

        </form>

    </div>

    <!-- 3. Sticky Floating Navigation Bar for Mobile & Tablets (Exact About Page Pattern) -->
    <div class="lg:hidden fixed bottom-4 inset-x-4 z-30 bg-slate-900/90 dark:bg-slate-950/90 backdrop-blur-md text-white p-3 rounded-2xl border border-slate-800 shadow-2xl flex items-center justify-between gap-3 animate-in slide-in-from-bottom duration-300">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white text-xs font-bold flex items-center justify-center shrink-0" x-text="currentTabObject.num"></span>
            <div class="truncate">
                <p class="text-[11px] font-bold text-white truncate" x-text="currentTabObject.title"></p>
                <p class="text-[9px] text-slate-400">Step <span x-text="currentTabObject.num"></span> of 5</p>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" @click="prevTab()" :disabled="currentTabIndex === 0" class="p-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 rounded-xl text-white transition cursor-pointer" title="Previous step">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button type="button" @click="nextTab()" :disabled="currentTabIndex === tabs.length - 1" class="p-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 rounded-xl text-white transition cursor-pointer" title="Next step">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
            </button>
            <button type="submit" form="footerSettingsForm" :disabled="isSubmitting" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                <svg class="w-3.5 h-3.5" :class="isSubmitting ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>Save</span>
            </button>
        </div>
    </div>

</div>
@endsection
