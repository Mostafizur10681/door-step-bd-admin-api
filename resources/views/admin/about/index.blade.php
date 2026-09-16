@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-28 px-2 sm:px-4 lg:px-6" x-data="{
    activeTab: 'hero',
    isSubmitting: false,
    tabs: [
        { id: 'hero', title: 'Hero Banner', icon: '✨', num: '1' },
        { id: 'story', title: 'Our Story & Image', icon: '📖', num: '2' },
        { id: 'stats', title: 'Stats Counters', icon: '📊', num: '3' },
        { id: 'features', title: 'Core Promises', icon: '💎', num: '4' },
        { id: 'mission', title: 'Mission & Vision', icon: '🎯', num: '5' },
        { id: 'team', title: 'Team Members', icon: '👥', num: '6' },
        { id: 'cta', title: 'Contact & CTA', icon: '📞', num: '7' }
    ],
    storyPoints: {{ Js::from(!empty($about->story_points) ? $about->story_points : ['Directly Sourced Organic Food', '100% Unadulterated Honey', 'Chemical-Free Skincare', 'Fast Nationwide COD']) }},
    stats: {{ Js::from(!empty($about->stats) ? $about->stats : [
        ['value' => '50,000+', 'label' => 'Happy Customers'],
        ['value' => '1,200+', 'label' => 'Organic Products'],
        ['value' => '64', 'label' => 'Districts Covered'],
        ['value' => '4.9 / 5', 'label' => 'Customer Rating']
    ]) }},
    features: {{ Js::from(!empty($about->features) ? $about->features : [
        ['icon' => 'ShieldCheck', 'title' => '100% Authentic & Pure', 'desc' => 'Every item in our collection is carefully sourced directly from certified organic farms and global trusted suppliers.'],
        ['icon' => 'Award', 'title' => 'Premium Quality Control', 'desc' => 'Strict quality checks ensure that only fresh, high-grade, chemical-free products reach your doorstep.'],
        ['icon' => 'Truck', 'title' => 'Nationwide Fast Delivery', 'desc' => 'Reliable & non-contact cash-on-delivery across all 64 districts in Bangladesh with express processing.'],
        ['icon' => 'HeartHandshake', 'title' => 'Customer-First Support', 'desc' => 'Dedicated customer service team available 7 days a week to answer your questions and assist your health journey.']
    ]) }},
    team: {{ Js::from(!empty($about->team) ? $about->team : [
        ['name' => 'Rafiul Islam', 'role' => 'CEO & Founder', 'bio' => 'Visionary leader with 10+ years in eCommerce and supply chain management.', 'image' => ''],
        ['name' => 'Nazmun Nahar', 'role' => 'Co-Founder & COO', 'bio' => 'Operations expert passionate about logistics, sustainability and customer experience.', 'image' => ''],
        ['name' => 'Tanvir Ahmed', 'role' => 'Head of Support', 'bio' => 'Dedicated to making every customer interaction seamless and satisfying.', 'image' => '']
    ]) }},
    storyImagePreview: {{ Js::from($about->story_image ?? '') }},
    storyImageBase64: '',
    removeStoryImage: false,

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

    addStoryPoint() {
        this.storyPoints.push('');
    },
    removeStoryPoint(index) {
        this.storyPoints.splice(index, 1);
    },

    addStat() {
        this.stats.push({ value: '', label: '' });
    },
    removeStat(index) {
        this.stats.splice(index, 1);
    },

    addFeature() {
        this.features.push({ icon: 'Sparkles', title: '', desc: '' });
    },
    removeFeature(index) {
        this.features.splice(index, 1);
    },

    addTeamMember() {
        this.team.push({ name: '', role: '', bio: '', image: '' });
    },
    removeTeamMember(index) {
        this.team.splice(index, 1);
    },

    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.storyImagePreview = e.target.result;
                this.storyImageBase64 = e.target.result;
                this.removeStoryImage = false;
            };
            reader.readAsDataURL(file);
        }
    },
    clearStoryImage() {
        this.storyImagePreview = '';
        this.storyImageBase64 = '';
        this.removeStoryImage = true;
        if (this.$refs.storyFileInput) {
            this.$refs.storyFileInput.value = '';
        }
    },

    submitForm() {
        this.isSubmitting = true;
        this.$refs.aboutForm.submit();
    }
}">

    <!-- Top Header & Breadcrumbs Card -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-4 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <x-breadcrumbs :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'About Page Management']
                ]" />
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        About Page Customizer
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Sync
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Easily manage content across all 7 sections with real-time responsive controls.
                </p>
            </div>

            <!-- Action Buttons for Desktop / Tablet -->
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <a href="http://localhost:3000/about" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Public Page</span>
                </a>
            </div>
        </div>

        <!-- Section Progress Indicator (Great for Mobile & Tablet Understanding) -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 font-bold text-slate-700 dark:text-slate-300">
                <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-[11px]" x-text="currentTabObject.num"></span>
                <span x-text="'Section ' + currentTabObject.num + ' of 7: ' + currentTabObject.title"></span>
            </div>
            <div class="flex items-center gap-1">
                <template x-for="(tab, idx) in tabs" :key="tab.id">
                    <button type="button" @click="activeTab = tab.id" :title="tab.title" class="h-2 rounded-full transition-all duration-300" :class="activeTab === tab.id ? 'w-6 bg-emerald-600' : 'w-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300'"></button>
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
            <button type="button" @click="$el.parentElement.remove()" class="p-1 rounded-lg text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition">
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
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400" x-text="'Step ' + currentTabObject.num + ' of 7'"></span>
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
                        class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap"
                    >
                        <span class="text-sm" x-text="tab.icon"></span>
                        <span x-text="tab.title"></span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="activeTab === tab.id ? 'bg-emerald-700/80 text-emerald-100' : 'bg-slate-200/80 dark:bg-slate-800 text-slate-500 dark:text-slate-400'" x-text="tab.num"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Form Element -->
        <form id="aboutPageForm" x-ref="aboutForm" method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" @submit="isSubmitting = true" class="p-4 sm:p-6 lg:p-8 space-y-8">
            @csrf

            <!-- Hidden input for deleting story image or passing Base64 -->
            <input type="hidden" name="remove_story_image" :value="removeStoryImage ? '1' : '0'">
            <input type="hidden" name="story_image_base64" :value="storyImageBase64">

            <!-- ================= TAB 1: HERO BANNER ================= -->
            <div x-show="activeTab === 'hero'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>✨</span> Section 1: Hero Header Banner
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            The top header that visitors see when they open the About Us page.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 1 of 7
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    <div class="md:col-span-1 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Top Badge Pill</label>
                        <input type="text" name="hero_badge" value="{{ old('hero_badge', $about->hero_badge ?? 'Welcome to ShopiaBD') }}" placeholder="e.g. Welcome to ShopiaBD" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 block">Appears in a golden pill above the title.</span>
                    </div>

                    <div class="md:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Hero Main Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $about->hero_title ?? 'Your Trusted Partner for Pure, Organic & Authentic Living') }}" required placeholder="e.g. Your Trusted Partner for Pure, Organic & Authentic Living" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>

                    <div class="md:col-span-3 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Hero Subtitle / Description</label>
                        <textarea name="hero_subtitle" rows="3" placeholder="Explain your company's mission statement briefly..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition leading-relaxed">{{ old('hero_subtitle', $about->hero_subtitle ?? 'Empowering healthy lifestyles across Bangladesh by bringing 100% natural organic food, premium skincare, and healthcare supplements directly to your home.') }}</textarea>
                    </div>
                </div>

                <!-- Hero Responsive Live Mockup Preview -->
                <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-[#0b3b82] via-[#092a5e] to-[#b30047] text-white text-center space-y-3 shadow-lg relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.12),transparent_60%)] pointer-events-none"></div>
                    <span class="inline-block px-3.5 py-1 bg-amber-400/20 border border-amber-400/30 text-amber-300 font-bold text-[10px] rounded-full uppercase tracking-wider">
                        Live Preview: Public Hero Header
                    </span>
                    <h3 class="text-lg sm:text-2xl font-black text-white leading-snug">
                        {{ $about->hero_title ?? 'Your Trusted Partner for Pure, Organic & Authentic Living' }}
                    </h3>
                    <p class="text-blue-100 text-xs sm:text-sm max-w-2xl mx-auto leading-relaxed">
                        {{ $about->hero_subtitle ?? 'Empowering healthy lifestyles across Bangladesh by bringing 100% natural organic food, premium skincare, and healthcare supplements directly to your home.' }}
                    </p>
                </div>
            </div>

            <!-- ================= TAB 2: OUR STORY & IMAGE ================= -->
            <div x-show="activeTab === 'story'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📖</span> Section 2: Company Story &amp; Showcase
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Describe the origin, journey, checklist highlights, and featured imagery.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 2 of 7
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    <!-- Left: Story Text Content -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Section Badge</label>
                                <input type="text" name="story_badge" value="{{ old('story_badge', $about->story_badge ?? 'OUR STORY') }}" placeholder="OUR STORY" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Story Heading <span class="text-rose-500">*</span></label>
                                <input type="text" name="story_title" value="{{ old('story_title', $about->story_title ?? 'Bringing Pure & Natural Wellness to Every Home') }}" required placeholder="e.g. Bringing Pure & Natural Wellness to Every Home" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Story Paragraph 1</label>
                            <textarea name="story_description_1" rows="3" placeholder="First paragraph of your story..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition leading-relaxed">{{ old('story_description_1', $about->story_description_1 ?? 'Founded with a clear vision, ShopiaBD set out to solve the challenge of finding genuine, unadulterated organic products in Bangladesh.') }}</textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Story Paragraph 2</label>
                            <textarea name="story_description_2" rows="3" placeholder="Second paragraph of your story..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition leading-relaxed">{{ old('story_description_2', $about->story_description_2 ?? 'From pure Sundarban wild honey, raw organic chia seeds, and premium maca superfood to dermatologist-approved skincare formulations, every item in our store is selected with utmost care.') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Floating Badge Big Tag</label>
                                <input type="text" name="experience_badge_text" value="{{ old('experience_badge_text', $about->experience_badge_text ?? '#1') }}" placeholder="e.g. #1 or 10+ Years" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-bold">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Floating Badge Subtitle</label>
                                <input type="text" name="experience_badge_subtext" value="{{ old('experience_badge_subtext', $about->experience_badge_subtext ?? 'Authentic E-Commerce In Bangladesh') }}" placeholder="e.g. Authentic E-Commerce In Bangladesh" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                            </div>
                        </div>

                        <!-- Story Points Repeater -->
                        <div class="pt-3 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Story Checklist Highlights
                                </label>
                                <button type="button" @click="addStoryPoint()" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add Point
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(point, index) in storyPoints" :key="index">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <input type="text" :name="'story_points[]'" x-model="storyPoints[index]" placeholder="e.g. Directly Sourced Organic Food" class="flex-1 px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                                        <button type="button" @click="removeStoryPoint(index)" class="p-2 text-slate-400 hover:text-rose-500 transition rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Story Image Upload & Preview -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-200">Featured Story Showcase Image</label>
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 px-2 py-0.5 rounded-full">
                                Base64 Sync
                            </span>
                        </div>
                        
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl p-5 text-center bg-slate-50/70 dark:bg-slate-950/70 space-y-4 relative transition-colors">
                            
                            <template x-if="storyImagePreview && storyImagePreview.trim() !== ''">
                                <div class="space-y-3">
                                    <div class="relative w-full h-56 sm:h-64 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/90 flex items-center justify-center p-3 shadow-inner">
                                        <img 
                                            :src="storyImagePreview" 
                                            x-on:error="$el.src = '/prod_honey.png'" 
                                            alt="Story Showcase Preview" 
                                            class="max-h-full max-w-full object-contain rounded-xl drop-shadow-md"
                                        >
                                    </div>
                                    <div class="flex flex-wrap items-center justify-center gap-2.5">
                                        <label class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs font-bold rounded-xl cursor-pointer transition shadow-xs">
                                            Replace Image
                                            <input type="file" name="story_image" x-ref="storyFileInput" @change="previewImage($event)" accept="image/*" class="hidden">
                                        </label>
                                        <button type="button" @click="clearStoryImage()" class="px-4 py-2 bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 dark:hover:bg-rose-900/70 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-bold rounded-xl transition shadow-xs">
                                            Remove Image
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!storyImagePreview || storyImagePreview.trim() === ''">
                                <div class="py-8 space-y-3">
                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-xs">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <label class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl cursor-pointer shadow-md shadow-emerald-600/20 inline-block transition">
                                            Upload Story Image
                                            <input type="file" name="story_image" x-ref="storyFileInput" @change="previewImage($event)" accept="image/*" class="hidden">
                                        </label>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">Recommended: PNG, JPG, or WEBP</p>
                                    </div>
                                </div>
                            </template>

                        </div>

                        <!-- Redesigned Story Showcase Tip (Light & Dark Mode) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent dark:from-amber-500/15 dark:via-slate-900/60 dark:to-slate-950 border border-amber-200/80 dark:border-amber-500/30 space-y-2.5 shadow-sm backdrop-blur-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center text-xs shrink-0 shadow-xs">
                                    💡
                                </div>
                                <h5 class="text-xs font-black text-amber-900 dark:text-amber-200 tracking-wide uppercase">
                                    Story Showcase Tip
                                </h5>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                                Upload a high-resolution hero product photo or warehouse image. The system stores it directly in <strong class="text-amber-800 dark:text-amber-300 font-bold">Base64 format</strong> for instantaneous rendering on your storefront.
                            </p>
                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-800 text-amber-800 dark:text-amber-300 border border-amber-200/70 dark:border-amber-700/50 shadow-xs">
                                    📐 800 × 600 px recommended
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-700/50 shadow-xs">
                                    ⚡ Instant Base64 Storage
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ================= TAB 3: STATS COUNTERS ================= -->
            <div x-show="activeTab === 'stats'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📊</span> Section 3: Key Stats &amp; Metric Counters
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Shown in the highlighted horizontal stat bar on the public About page.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full">
                            Step 3 of 7
                        </span>
                        <button type="button" @click="addStat()" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Stat
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <template x-for="(stat, index) in stats" :key="index">
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-3 relative group">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-text="'Metric #' + (index + 1)"></span>
                                <button type="button" @click="removeStat(index)" class="text-slate-400 hover:text-rose-500 transition p-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400">Value (Big Text)</label>
                                <input type="text" :name="'stats[' + index + '][value]'" x-model="stat.value" placeholder="e.g. 50,000+" class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-base font-black text-emerald-600 dark:text-emerald-400 focus:outline-none focus:border-emerald-500 transition">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400">Label</label>
                                <input type="text" :name="'stats[' + index + '][label]'" x-model="stat.label" placeholder="e.g. Happy Customers" class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ================= TAB 4: CORE PROMISES (WHY CHOOSE US) ================= -->
            <div x-show="activeTab === 'features'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>💎</span> Section 4: Core Promises &amp; Values
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            4-grid highlight cards displaying your authentic commitments to customers.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full">
                            Step 4 of 7
                        </span>
                        <button type="button" @click="addFeature()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Card
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Section Badge</label>
                        <input type="text" name="why_choose_badge" value="{{ old('why_choose_badge', $about->why_choose_badge ?? 'WHY CHOOSE US') }}" placeholder="WHY CHOOSE US" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Main Title</label>
                        <input type="text" name="why_choose_title" value="{{ old('why_choose_title', $about->why_choose_title ?? 'Our Core Promises to You') }}" placeholder="Our Core Promises to You" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Subtitle</label>
                        <input type="text" name="why_choose_subtitle" value="{{ old('why_choose_subtitle', $about->why_choose_subtitle ?? 'Built on transparency, authenticity, and dedication to your health') }}" placeholder="Built on transparency..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                </div>

                <div class="pt-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="(feat, index) in features" :key="index">
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-3 relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold flex items-center justify-center" x-text="index + 1"></span>
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Card Configuration</span>
                                    </div>
                                    <button type="button" @click="removeFeature(index)" class="text-slate-400 hover:text-rose-500 transition p-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Icon Style</label>
                                        <select :name="'features[' + index + '][icon]'" x-model="feat.icon" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition">
                                            <option value="ShieldCheck">🛡️ ShieldCheck (Authenticity)</option>
                                            <option value="Award">🏆 Award (Quality)</option>
                                            <option value="Truck">🚚 Truck (Delivery)</option>
                                            <option value="HeartHandshake">🤝 HeartHandshake (Support)</option>
                                            <option value="Sparkles">✨ Sparkles (Purity)</option>
                                            <option value="CheckCircle2">✅ CheckCircle (Verified)</option>
                                            <option value="Leaf">🌱 Leaf (Organic)</option>
                                            <option value="Clock">⏱️ Clock (Fast 24/7)</option>
                                            <option value="Star">⭐ Star (Premium)</option>
                                            <option value="Headphones">🎧 Headphones (Service)</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Card Title <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="'features[' + index + '][title]'" x-model="feat.title" placeholder="e.g. 100% Authentic & Pure" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Description Text</label>
                                    <textarea :name="'features[' + index + '][desc]'" x-model="feat.desc" rows="2" placeholder="Detail description of this promise..." class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition leading-relaxed"></textarea>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 5: MISSION & VISION ================= -->
            <div x-show="activeTab === 'mission'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🎯</span> Section 5: Mission &amp; Vision Statements
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Articulate your company's long-term purpose and strategic goals.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 5 of 7
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Mission Box -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-4">
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                            <span>🚀</span>
                            <h4>Our Mission</h4>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Mission Title</label>
                            <input type="text" name="mission_title" value="{{ old('mission_title', $about->mission_title ?? 'Our Mission') }}" placeholder="Our Mission" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Mission Description</label>
                            <textarea name="mission_description" rows="4" placeholder="To deliver high-quality authentic and organic products..." class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition leading-relaxed">{{ old('mission_description', $about->mission_description ?? 'To deliver high-quality authentic and organic products at affordable prices with exceptional customer service, making healthy and reliable living accessible for every household in Bangladesh.') }}</textarea>
                        </div>
                    </div>

                    <!-- Vision Box -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-4">
                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-bold text-sm">
                            <span>👁️</span>
                            <h4>Our Vision</h4>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Vision Title</label>
                            <input type="text" name="vision_title" value="{{ old('vision_title', $about->vision_title ?? 'Our Vision') }}" placeholder="Our Vision" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Vision Description</label>
                            <textarea name="vision_description" rows="4" placeholder="To become the most trusted and customer-centric lifestyle brand..." class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition leading-relaxed">{{ old('vision_description', $about->vision_description ?? 'To become the most trusted and customer-centric lifestyle and wellness e-commerce brand in Bangladesh through unwavering quality, innovation, and direct sourcing.') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ================= TAB 6: TEAM MEMBERS ================= -->
            <div x-show="activeTab === 'team'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>👥</span> Section 6: Leadership &amp; Team Members
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Introduce the founders and team driving the brand.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full">
                            Step 6 of 7
                        </span>
                        <button type="button" @click="addTeamMember()" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Member
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Team Badge</label>
                        <input type="text" name="team_badge" value="{{ old('team_badge', $about->team_badge ?? 'The Team') }}" placeholder="The Team" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Team Section Title</label>
                        <input type="text" name="team_title" value="{{ old('team_title', $about->team_title ?? 'Meet Our Leadership') }}" placeholder="Meet Our Leadership" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Team Subtitle</label>
                        <input type="text" name="team_subtitle" value="{{ old('team_subtitle', $about->team_subtitle ?? 'Passionate people working every day to bring purity and wellness to your door.') }}" placeholder="Passionate people..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-1">
                    <template x-for="(member, index) in team" :key="index">
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-3.5 relative">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="'Member #' + (index + 1)"></span>
                                <button type="button" @click="removeTeamMember(index)" class="text-slate-400 hover:text-rose-500 transition p-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <!-- Member Image Display / File Input -->
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0 shadow-inner">
                                    <template x-if="member.image">
                                        <img :src="member.image" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!member.image">
                                        <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Avatar Image</label>
                                    <input type="file" :name="'team_images[' + index + ']'" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950 file:text-emerald-700 dark:file:text-emerald-300 cursor-pointer">
                                    <input type="hidden" :name="'team[' + index + '][image]'" :value="member.image">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400">Full Name</label>
                                <input type="text" :name="'team[' + index + '][name]'" x-model="member.name" placeholder="e.g. Rafiul Islam" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400">Designation / Role</label>
                                <input type="text" :name="'team[' + index + '][role]'" x-model="member.role" placeholder="e.g. CEO & Founder" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400">Short Bio</label>
                                <textarea :name="'team[' + index + '][bio]'" x-model="member.bio" rows="2" placeholder="Brief background summary..." class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ================= TAB 7: CONTACT & CTA BANNER ================= -->
            <div x-show="activeTab === 'cta'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📞</span> Section 7: Contact &amp; Call To Action
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            The bottom inquiry banner connecting customers with your phone hotline and support email.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full w-fit">
                        Step 7 of 7
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">CTA Banner Title</label>
                        <input type="text" name="cta_title" value="{{ old('cta_title', $about->cta_title ?? 'Have Questions or Need Recommendations?') }}" placeholder="Have Questions or Need Recommendations?" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-semibold">
                    </div>

                    <div class="md:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">CTA Description Text</label>
                        <textarea name="cta_subtitle" rows="2" placeholder="Our dedicated support team is here to assist you..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition leading-relaxed">{{ old('cta_subtitle', $about->cta_subtitle ?? 'Our dedicated support team is here to assist you with order inquiries, product guidance, and delivery updates.') }}</textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Hotline / Phone Number</label>
                        <input type="text" name="cta_phone" value="{{ old('cta_phone', $about->cta_phone ?? '01681-135030') }}" placeholder="e.g. 01681-135030" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Support Email Address</label>
                        <input type="email" name="cta_email" value="{{ old('cta_email', $about->cta_email ?? 'info@shopiabd.com') }}" placeholder="e.g. info@shopiabd.com" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium">
                    </div>
                </div>

                <!-- CTA Responsive Mockup -->
                <div class="p-6 sm:p-8 rounded-3xl bg-[#0b3b82] text-white text-center space-y-4 shadow-lg relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.1),transparent_60%)] pointer-events-none"></div>
                    <h3 class="text-xl sm:text-2xl font-bold">Have Questions or Need Recommendations?</h3>
                    <p class="text-blue-100 text-xs sm:text-sm max-w-xl mx-auto leading-relaxed">
                        Our dedicated support team is here to assist you with order inquiries, product guidance, and delivery updates.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <span class="px-6 py-2.5 bg-[#ff8c00] text-white font-bold text-xs rounded-full shadow-md">
                            Call Hotline: {{ $about->cta_phone ?? '01681-135030' }}
                        </span>
                        <span class="px-6 py-2.5 bg-white/10 text-white font-bold text-xs rounded-full border border-white/20">
                            Email Us
                        </span>
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
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition shadow-xs"
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
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-200/80 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 text-xs font-bold rounded-xl transition shadow-xs"
                    >
                        <span>Next Section</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button 
                        type="submit" 
                        form="aboutPageForm"
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

    <!-- 3. Sticky Floating Navigation Bar for Mobile & Tablets -->
    <div class="lg:hidden fixed bottom-4 inset-x-4 z-30 bg-slate-900/90 dark:bg-slate-950/90 backdrop-blur-md text-white p-3 rounded-2xl border border-slate-800 shadow-2xl flex items-center justify-between gap-3 animate-in slide-in-from-bottom duration-300">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white text-xs font-bold flex items-center justify-center shrink-0" x-text="currentTabObject.num"></span>
            <div class="truncate">
                <p class="text-[11px] font-bold text-white truncate" x-text="currentTabObject.title"></p>
                <p class="text-[9px] text-slate-400">Step <span x-text="currentTabObject.num"></span> of 7</p>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" @click="prevTab()" :disabled="currentTabIndex === 0" class="p-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 rounded-xl text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button type="button" @click="nextTab()" :disabled="currentTabIndex === tabs.length - 1" class="p-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 rounded-xl text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
            </button>
            <button type="submit" form="aboutPageForm" :disabled="isSubmitting" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                <svg class="w-3.5 h-3.5" :class="isSubmitting ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>Save</span>
            </button>
        </div>
    </div>

</div>
@endsection
