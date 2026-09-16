@extends('layouts.admin')

@section('content')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(16, 185, 129, 0.25);
        border-radius: 99px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(16, 185, 129, 0.45);
    }
</style>

<div class="max-w-7xl mx-auto space-y-6 pb-28 px-2 sm:px-4 lg:px-6" x-data="{
    activeTab: 'hero',
    isSubmitting: false,
    previewMode: 'desktop',

    tabs: [
        { id: 'hero', title: 'Hero Banner', icon: '✨', num: '1' },
        { id: 'channels', title: 'Contact Channels', icon: '📞', num: '2' },
        { id: 'hours', title: 'Address & Hours', icon: '🕒', num: '3' },
        { id: 'map', title: 'Google Map & Location', icon: '🗺️', num: '4' },
        { id: 'form', title: 'Inquiry Form & Topics', icon: '📝', num: '5' },
        { id: 'features', title: 'Support Promises', icon: '💎', num: '6' },
        { id: 'support', title: 'Support Box & Image', icon: '🎧', num: '7' },
        { id: 'preview', title: 'Live Preview', icon: '👁️', num: '8' }
    ],

    // Hero Section
    badgeText: {{ Js::from(old('badge_text', $setting->badge_text ?? '💬 Get in Touch • 24/7 Support')) }},
    heroTitle: {{ Js::from(old('hero_title', $setting->hero_title ?? "We're Here to Help You Thrive")) }},
    heroSubtitle: {{ Js::from(old('hero_subtitle', $setting->hero_subtitle ?? 'Have questions about our organic products, shipping updates, or business partnerships? Our dedicated team is always here to assist you.')) }},
    emergencyNotice: {{ Js::from(old('emergency_notice', $setting->emergency_notice ?? '⚡ Fast Order Hotline: For immediate order modifications or urgent delivery inquiries, call our hotline directly.')) }},

    // Contact Channels
    phone: {{ Js::from(old('phone', $setting->phone ?? '+880 1800-000000')) }},
    secondaryPhone: {{ Js::from(old('secondary_phone', $setting->secondary_phone ?? '+880 1700-000000')) }},
    whatsappNumber: {{ Js::from(old('whatsapp_number', $setting->whatsapp_number ?? '8801800000000')) }},
    email: {{ Js::from(old('email', $setting->email ?? 'support@shopia.com')) }},
    secondaryEmail: {{ Js::from(old('secondary_email', $setting->secondary_email ?? 'wholesale@shopia.com')) }},
    responseTimeNote: {{ Js::from(old('response_time_note', $setting->response_time_note ?? 'Average reply time: Under 15 mins during business hours')) }},

    // Address & Hours
    address: {{ Js::from(old('address', $setting->address ?? "41/1, Sher-E-Bangla Rd, Mohammadpur, Dhaka 1207")) }},
    businessHoursWeekday: {{ Js::from(old('business_hours_weekday', $setting->business_hours_weekday ?? 'Saturday - Thursday: 9:00 AM - 10:00 PM')) }},
    businessHoursWeekend: {{ Js::from(old('business_hours_weekend', $setting->business_hours_weekend ?? 'Friday: 3:00 PM - 10:00 PM')) }},
    locationDirections: {{ Js::from(old('location_directions', $setting->location_directions ?? 'Near Mohammadpur Bus Stand, easy parking available for shoppers.')) }},

    // Google Map
    mapTitle: {{ Js::from(old('map_title', $setting->map_title ?? 'Visit Our Store & Experience Center')) }},
    mapSubtitle: {{ Js::from(old('map_subtitle', $setting->map_subtitle ?? 'Experience our 100% natural, fresh organic food & wellness products in person.')) }},
    mapUrl: {{ Js::from(old('map_url', $setting->map_url ?? 'https://maps.google.com/maps?q=Mohammadpur%2C%20Dhaka&t=&z=14&ie=UTF8&iwloc=&output=embed')) }},

    // Inquiry Form
    formTitle: {{ Js::from(old('form_title', $setting->form_title ?? 'Send Us a Message')) }},
    formSubtitle: {{ Js::from(old('form_subtitle', $setting->form_subtitle ?? 'Fill out the form below and our customer support specialists will respond within 24 hours.')) }},
    formTopics: {{ Js::from(old('form_topics', !empty($setting->form_topics) ? $setting->form_topics : [
        'Order Tracking & Delivery Status',
        'Product Inquiry & Authenticity',
        'Returns, Refunds & Replacements',
        'Wholesale & B2B Bulk Orders',
        'Payment & Billing Assistance',
        'Other General Inquiries'
    ])) }},

    // Features
    features: {{ Js::from(old('features', !empty($setting->features) ? $setting->features : [
        ['icon' => 'Headphones', 'title' => '24/7 Dedicated Care', 'desc' => 'Friendly support team ready to assist via live phone, email & chat.'],
        ['icon' => 'ShieldCheck', 'title' => '100% Genuine Products', 'desc' => 'All items tested and directly sourced with authenticity guarantee.'],
        ['icon' => 'Truck', 'title' => 'Nationwide Delivery', 'desc' => 'Fast delivery across 64 districts in Bangladesh with open parcel check.'],
        ['icon' => 'RotateCcw', 'title' => '7-Day Easy Returns', 'desc' => 'Hassle-free replacement or refund for damaged or inaccurate orders.']
    ])) }},

    // Support Box
    supportTitle: {{ Js::from(old('support_title', $setting->support_title ?? 'Need Immediate Assistance?')) }},
    supportDesc: {{ Js::from(old('support_desc', $setting->support_desc ?? 'Our customer service specialists are active and eager to assist you right now.')) }},
    supportPhone: {{ Js::from(old('support_phone', $setting->support_phone ?? '+880 1800-000000')) }},
    supportImagePreview: {{ Js::from($setting->support_image ?? '') }},
    supportImageBase64: '',
    removeSupportImage: false,

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

    addFormTopic() {
        this.formTopics.push('');
    },
    removeFormTopic(index) {
        this.formTopics.splice(index, 1);
    },

    addFeature() {
        this.features.push({ icon: 'Sparkles', title: '', desc: '' });
    },
    removeFeature(index) {
        this.features.splice(index, 1);
    },

    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size exceeds 5MB limit.');
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                this.supportImagePreview = e.target.result;
                this.supportImageBase64 = e.target.result;
                this.removeSupportImage = false;
            };
            reader.readAsDataURL(file);
        }
    },
    clearImage() {
        this.supportImagePreview = '';
        this.supportImageBase64 = '';
        this.removeSupportImage = true;
    },

    submitForm() {
        this.isSubmitting = true;
        this.$refs.contactForm.submit();
    }
}">

    <!-- Top Header & Breadcrumbs Card -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-4 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <x-breadcrumbs :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Settings', 'url' => route('admin.settings.index')],
                    ['label' => 'Contact Settings']
                ]" />
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Contact Page Customizer
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span> Live Sync
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Easily manage content across all 8 contact sections with real-time responsive controls.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <a href="http://localhost:3000/contact-us" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Public Page</span>
                </a>
            </div>
        </div>

        <!-- Section Progress Indicator (Mobile & Tablet Header Visual) -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 font-bold text-slate-700 dark:text-slate-300">
                <span class="w-6 h-6 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-[11px]" x-text="currentTabObject.num"></span>
                <span x-text="'Section ' + currentTabObject.num + ' of 8: ' + currentTabObject.title"></span>
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

    @if(isset($errors) && $errors->any())
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
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400" x-text="'Step ' + currentTabObject.num + ' of 8'"></span>
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
        <div class="hidden sm:block border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-950/50 p-2.5 sm:p-3 overflow-x-auto custom-scrollbar">
            <div class="flex items-center gap-1.5 md:gap-2 min-w-max pb-1.5">
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
        <form id="contactSettingsForm" x-ref="contactForm" method="POST" action="{{ route('admin.contact-settings.update') }}" enctype="multipart/form-data" @submit="isSubmitting = true" class="p-4 sm:p-6 lg:p-8 space-y-8">
            @csrf

            <!-- Hidden input for Base64 Image -->
            <input type="hidden" name="support_image_base64" :value="supportImageBase64">
            <input type="hidden" name="remove_support_image" :value="removeSupportImage ? '1' : '0'">

            <!-- ================= TAB 1: HERO BANNER ================= -->
            <div x-show="activeTab === 'hero'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>✨</span> Section 1: Hero Banner & Notices
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            The top header that visitors see when they open the Contact Us page.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 1 of 8
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    <div class="md:col-span-1 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Top Badge Pill</label>
                        <input type="text" name="badge_text" x-model="badgeText" placeholder="e.g. 💬 Get in Touch • 24/7 Support" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 block">Appears in a glowing pill above the title.</span>
                    </div>

                    <div class="md:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Hero Main Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="hero_title" x-model="heroTitle" required placeholder="e.g. We're Here to Help You Thrive" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>

                    <div class="md:col-span-3 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Hero Subtitle / Description</label>
                        <textarea name="hero_subtitle" x-model="heroSubtitle" rows="3" placeholder="Explain how and when customers can reach out..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition leading-relaxed"></textarea>
                    </div>

                    <div class="md:col-span-3 space-y-1.5 bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 sm:p-5">
                        <label class="block text-xs font-bold text-amber-900 dark:text-amber-300">⚡ Emergency Hotline & Notice Bar</label>
                        <textarea name="emergency_notice" x-model="emergencyNotice" rows="2" placeholder="e.g. ⚡ Fast Order Hotline: For immediate order modifications or urgent delivery inquiries, call our hotline directly." class="w-full px-4 py-2 bg-white dark:bg-slate-950 border border-amber-500/30 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium"></textarea>
                        <span class="text-[11px] text-amber-700 dark:text-amber-400 block mt-1">Highlighted notice strip displayed at the top of the Contact Us page.</span>
                    </div>
                </div>

                <!-- Hero Responsive Live Mockup Preview -->
                <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-[#063c22] via-[#0b6339] to-[#042817] text-white text-center space-y-3 shadow-lg relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.08),transparent_60%)] pointer-events-none"></div>
                    <span class="inline-block px-3.5 py-1 bg-white/10 border border-white/20 text-emerald-200 font-bold text-[10px] rounded-full uppercase tracking-wider" x-text="badgeText"></span>
                    <h3 class="text-lg sm:text-2xl font-black text-white leading-snug" x-text="heroTitle"></h3>
                    <p class="text-emerald-50/90 text-xs sm:text-sm max-w-2xl mx-auto leading-relaxed" x-text="heroSubtitle"></p>
                </div>
            </div>

            <!-- ================= TAB 2: CONTACT CHANNELS ================= -->
            <div x-show="activeTab === 'channels'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📞</span> Section 2: Communication Channels & Numbers
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Manage customer care hotlines, WhatsApp instant messaging, and official emails.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 2 of 8
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Primary Phone / Helpline <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">📞</span>
                            <input type="text" name="phone" x-model="phone" required placeholder="+880 1800-000000" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Secondary Phone / Backup</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">📱</span>
                            <input type="text" name="secondary_phone" x-model="secondaryPhone" placeholder="+880 1700-000000" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">WhatsApp Number (digits only)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-emerald-600 font-bold">💬</span>
                            <input type="text" name="whatsapp_number" x-model="whatsappNumber" placeholder="8801800000000" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Primary Support Email <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">✉️</span>
                            <input type="email" name="email" x-model="email" required placeholder="support@shopia.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Secondary / Wholesale Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">💼</span>
                            <input type="email" name="secondary_email" x-model="secondaryEmail" placeholder="wholesale@shopia.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Response Time Note</label>
                        <input type="text" name="response_time_note" x-model="responseTimeNote" placeholder="Average reply time: Under 15 mins during business hours" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>
                </div>
            </div>

            <!-- ================= TAB 3: ADDRESS & HOURS ================= -->
            <div x-show="activeTab === 'hours'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🕒</span> Section 3: Physical Address & Operating Hours
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Office/store location details, customer service operating schedules, and directions.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 3 of 8
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Office & Store Location Address <span class="text-rose-500">*</span></label>
                        <textarea name="address" x-model="address" rows="2" required placeholder="41/1, Sher-E-Bangla Rd, Mohammadpur, Dhaka 1207" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Regular Weekday Hours</label>
                            <input type="text" name="business_hours_weekday" x-model="businessHoursWeekday" placeholder="Saturday - Thursday: 9:00 AM - 10:00 PM" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Weekend / Friday Hours</label>
                            <input type="text" name="business_hours_weekend" x-model="businessHoursWeekend" placeholder="Friday: 3:00 PM - 10:00 PM" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Location Notes & Landmark Directions</label>
                        <input type="text" name="location_directions" x-model="locationDirections" placeholder="e.g. Near Mohammadpur Bus Stand, easy parking available for shoppers." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>
                </div>
            </div>

            <!-- ================= TAB 4: GOOGLE MAP ================= -->
            <div x-show="activeTab === 'map'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🗺️</span> Section 4: Interactive Google Map & Location Embed
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Embed your Google Maps store location with custom titles and interactive preview.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 4 of 8
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Map Section Title</label>
                        <input type="text" name="map_title" x-model="mapTitle" placeholder="Visit Our Store & Experience Center" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Map Section Subtitle</label>
                        <input type="text" name="map_subtitle" x-model="mapSubtitle" placeholder="Experience our 100% natural, fresh organic food & wellness products in person." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Google Maps Embed URL / Iframe Src</label>
                        <textarea name="map_url" x-model="mapUrl" rows="3" placeholder="https://maps.google.com/maps?q=Mohammadpur%2C%20Dhaka&t=&z=14&ie=UTF8&iwloc=&output=embed" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition"></textarea>
                    </div>
                </div>

                <!-- Live Map Preview Box -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-950 p-4 space-y-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Live Map Embed Preview:</span>
                    <div class="aspect-video w-full rounded-xl overflow-hidden shadow-inner border border-slate-200 dark:border-slate-800 relative bg-slate-200 dark:bg-slate-900">
                        <template x-if="mapUrl">
                            <iframe :src="mapUrl" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </template>
                        <template x-if="!mapUrl">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs">Enter a valid Google Map URL above to preview here.</div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 5: INQUIRY FORM & TOPICS ================= -->
            <div x-show="activeTab === 'form'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📝</span> Section 5: Contact Form Headers & Inquiry Topics
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Configure the interactive message submission form and customize available inquiry subject dropdowns.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 5 of 8
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Form Card Title</label>
                        <input type="text" name="form_title" x-model="formTitle" placeholder="Send Us a Message" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Form Card Subtitle</label>
                        <input type="text" name="form_subtitle" x-model="formSubtitle" placeholder="Fill out the form below and our team will get back to you within 24 hours." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>
                </div>

                <!-- Topic List Manager -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Inquiry Subject Categories (Dropdown Options)</h4>
                            <p class="text-xs text-slate-400 dark:text-slate-500">These topics appear in the customer contact form subject select menu.</p>
                        </div>
                        <button type="button" @click="addFormTopic()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-xl transition-all border border-emerald-600/30 cursor-pointer w-fit">
                            <span>+ Add Topic</span>
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="(topic, idx) in formTopics" :key="idx">
                            <div class="flex items-center gap-2.5 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                <span class="text-xs font-bold text-slate-400 px-2" x-text="idx + 1"></span>
                                <input type="text" :name="'form_topics[' + idx + ']'" x-model="formTopics[idx]" placeholder="e.g. Order Tracking & Delivery Status" class="flex-1 px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 font-medium">
                                <button type="button" @click="removeFormTopic(idx)" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition-all cursor-pointer" title="Remove Topic">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 6: SUPPORT PROMISES ================= -->
            <div x-show="activeTab === 'features'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>💎</span> Section 6: Customer Support Promises & Badges
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Highlight your brand's core customer trust pillars (24/7 care, authenticity guarantee, easy returns).
                        </p>
                    </div>
                    <button type="button" @click="addFeature()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-xl transition-all border border-emerald-600/30 cursor-pointer w-fit">
                        <span>+ Add Promise Card</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="(feat, idx) in features" :key="idx">
                        <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 space-y-3 relative group">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="'Card #' + (idx + 1)"></span>
                                <button type="button" @click="removeFeature(idx)" class="text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 p-1.5 rounded-lg transition-all cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Lucide Icon</label>
                                    <input type="text" :name="'features[' + idx + '][icon]'" x-model="feat.icon" placeholder="e.g. Headphones, ShieldCheck, Truck" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-mono text-slate-900 dark:text-white focus:border-emerald-500 animate-transition">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Title</label>
                                    <input type="text" :name="'features[' + idx + '][title]'" x-model="feat.title" placeholder="e.g. 24/7 Dedicated Care" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:border-emerald-500 animate-transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Short Description</label>
                                <textarea :name="'features[' + idx + '][desc]'" x-model="feat.desc" rows="2" placeholder="Brief statement about this promise..." class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:border-emerald-500 animate-transition"></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ================= TAB 7: SUPPORT BOX ================= -->
            <div x-show="activeTab === 'support'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🎧</span> Section 7: Urgent Support Card & Graphic Image
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Configure floating help card text and upload a hero support image.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 7 of 8
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Urgent Help Card Title</label>
                            <input type="text" name="support_title" x-model="supportTitle" placeholder="Need Immediate Assistance?" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition animate-transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Help Card Description</label>
                            <textarea name="support_desc" x-model="supportDesc" rows="3" placeholder="Our customer service specialists are active and eager to assist you right now." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition animate-transition"></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Direct Support Hotline Number</label>
                            <input type="text" name="support_phone" x-model="supportPhone" placeholder="+880 1800-000000" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition animate-transition">
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Support Representative / Graphic Image</label>
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-emerald-600 rounded-2xl p-6 text-center bg-slate-50/50 dark:bg-slate-950/50 transition-all flex flex-col items-center justify-center min-h-[220px]">
                            <template x-if="supportImagePreview">
                                <div class="space-y-3">
                                    <div class="relative w-32 h-32 mx-auto rounded-2xl overflow-hidden shadow-md border-2 border-white dark:border-slate-800">
                                        <img :src="supportImagePreview" alt="Support Image Preview" class="w-full h-full object-cover">
                                    </div>
                                    <button type="button" @click="clearImage()" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-xl hover:bg-rose-100 transition-all border border-rose-200 dark:border-rose-900 cursor-pointer">
                                        Remove Image
                                    </button>
                                </div>
                            </template>

                            <template x-if="!supportImagePreview">
                                <div class="space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-emerald-600/10 dark:bg-emerald-600/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-xl">
                                        📷
                                    </div>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Click to upload support hero image</p>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">PNG, JPG, WEBP up to 5MB</p>
                                    <input type="file" accept="image/*" @change="previewImage($event)" class="mt-2 text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600/10 dark:file:bg-emerald-600/30 file:text-emerald-600 dark:file:text-emerald-400 hover:file:bg-emerald-600/20 cursor-pointer">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 8: LIVE STOREFRONT PREVIEW ================= -->
            <div x-show="activeTab === 'preview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>👁️</span> Section 8: Live Storefront Simulator
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Real-time simulation of the Contact Us page layout.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl w-fit">
                        <button type="button" @click="previewMode = 'desktop'" :class="previewMode === 'desktop' ? 'bg-white dark:bg-slate-900 font-bold shadow-xs text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400'" class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">🖥️ Desktop</button>
                        <button type="button" @click="previewMode = 'mobile'" :class="previewMode === 'mobile' ? 'bg-white dark:bg-slate-900 font-bold shadow-xs text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400'" class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">📱 Mobile</button>
                    </div>
                </div>

                <!-- Simulation Viewport -->
                <div :class="previewMode === 'mobile' ? 'max-w-sm mx-auto' : 'w-full'" class="transition-all duration-300 border-4 border-slate-800 dark:border-slate-700 rounded-3xl overflow-hidden shadow-2xl bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-white">
                    
                    <!-- Emergency Bar -->
                    <template x-if="emergencyNotice">
                        <div class="bg-amber-500 text-slate-950 px-4 py-2 text-xs font-bold text-center flex items-center justify-center gap-2 shadow-xs">
                            <span x-text="emergencyNotice"></span>
                        </div>
                    </template>

                    <!-- Hero Section -->
                    <div class="bg-gradient-to-b from-[#063c22] via-[#0b6339] to-[#042817] text-white p-6 sm:p-10 text-center space-y-3">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-emerald-200 text-xs font-bold" x-text="badgeText"></div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white" x-text="heroTitle"></h1>
                        <p class="text-xs sm:text-sm text-emerald-50/90 max-w-xl mx-auto" x-text="heroSubtitle"></p>
                    </div>

                    <!-- Contact Cards Row -->
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 -mt-6">
                        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-md border border-slate-200/80 dark:border-slate-800 space-y-1">
                            <span class="text-xl">📞</span>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Call Support</div>
                            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold" x-text="phone"></div>
                            <div class="text-[10px] text-slate-400" x-text="businessHoursWeekday"></div>
                        </div>

                        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-md border border-slate-200/80 dark:border-slate-800 space-y-1">
                            <span class="text-xl text-emerald-600">💬</span>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">WhatsApp Chat</div>
                            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold" x-text="whatsappNumber"></div>
                            <div class="text-[10px] text-slate-400">Instant Chat Available</div>
                        </div>

                        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-md border border-slate-200/80 dark:border-slate-800 space-y-1">
                            <span class="text-xl">✉️</span>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Email Us</div>
                            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold" x-text="email"></div>
                            <div class="text-[10px] text-slate-400" x-text="responseTimeNote"></div>
                        </div>

                        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-md border border-slate-200/80 dark:border-slate-800 space-y-1">
                            <span class="text-xl">📍</span>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Visit Store</div>
                            <div class="text-xs text-slate-700 dark:text-slate-300 font-medium line-clamp-2" x-text="address"></div>
                        </div>
                    </div>

                    <!-- Form & Map Split Grid -->
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left: Form -->
                        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
                            <div>
                                <h3 class="text-base font-bold" x-text="formTitle"></h3>
                                <p class="text-xs text-slate-500" x-text="formSubtitle"></p>
                            </div>
                            <div class="space-y-2.5">
                                <input type="text" disabled placeholder="Your Full Name" class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs text-slate-900 dark:text-white border border-slate-200/70 dark:border-slate-700">
                                <input type="email" disabled placeholder="Your Email Address" class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs text-slate-900 dark:text-white border border-slate-200/70 dark:border-slate-700">
                                <select disabled class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs text-slate-900 dark:text-white border border-slate-200/70 dark:border-slate-700">
                                    <option>Select Inquiry Subject</option>
                                    <template x-for="t in formTopics" :key="t">
                                        <option x-text="t"></option>
                                    </template>
                                </select>
                                <textarea disabled rows="3" placeholder="Your Message..." class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs text-slate-900 dark:text-white border border-slate-200/70 dark:border-slate-700"></textarea>
                                <button type="button" disabled class="w-full py-2.5 bg-emerald-600 text-white font-bold text-xs rounded-xl">Send Inquiry Message</button>
                            </div>
                        </div>

                        <!-- Right: Map -->
                        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4 flex flex-col justify-between">
                            <div class="space-y-1">
                                <h3 class="text-base font-bold" x-text="mapTitle"></h3>
                                <p class="text-xs text-slate-500" x-text="mapSubtitle"></p>
                            </div>
                            <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 relative">
                                <template x-if="mapUrl">
                                    <iframe :src="mapUrl" class="w-full h-full border-0" loading="lazy"></iframe>
                                </template>
                            </div>
                            <div class="text-[11px] text-slate-400" x-text="locationDirections"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Navigation Bar & Actions (Desktop / Tablet) -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- Prev Step Button -->
                <button 
                    type="button" 
                    @click="prevTab()" 
                    :disabled="currentTabIndex === 0" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Previous Section</span>
                </button>

                <!-- Step Counter Indicator -->
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 hidden md:block">
                    Section <span class="font-bold text-slate-900 dark:text-white" x-text="currentTabIndex + 1"></span> of <span x-text="tabs.length"></span>: <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="currentTabObject.title"></span>
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
                        form="contactSettingsForm"
                        :disabled="isSubmitting" 
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/25 transition cursor-pointer"
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

    <!-- 3. Sticky Floating Navigation Bar for Mobile & Tablets -->
    <div class="lg:hidden fixed bottom-4 inset-x-4 z-30 bg-slate-900/90 dark:bg-slate-950/90 backdrop-blur-md text-white p-3 rounded-2xl border border-slate-800 shadow-2xl flex items-center justify-between gap-3 animate-in slide-in-from-bottom duration-300">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white text-xs font-bold flex items-center justify-center shrink-0" x-text="currentTabObject.num"></span>
            <div class="truncate">
                <p class="text-[11px] font-bold text-white truncate" x-text="currentTabObject.title"></p>
                <p class="text-[9px] text-slate-400">Step <span x-text="currentTabObject.num"></span> of 8</p>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" @click="prevTab()" :disabled="currentTabIndex === 0" class="p-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 rounded-xl text-white transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button type="button" @click="nextTab()" :disabled="currentTabIndex === tabs.length - 1" class="p-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 rounded-xl text-white transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
            </button>
            <button type="submit" form="contactSettingsForm" :disabled="isSubmitting" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                <svg class="w-3.5 h-3.5" :class="isSubmitting ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>Save</span>
            </button>
        </div>
    </div>

</div>
@endsection
