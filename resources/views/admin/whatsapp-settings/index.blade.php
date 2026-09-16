@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-24 px-2 sm:px-4 lg:px-6" x-data="{
    isEnabled: {{ Js::from(old('whatsapp_is_enabled', (bool) ($setting->whatsapp_is_enabled ?? true))) }},
    number: {{ Js::from(old('whatsapp_number', $setting->whatsapp_number ?? '8801800000000')) }},
    defaultMessage: {{ Js::from(old('whatsapp_default_message', $setting->whatsapp_default_message ?? 'Hello! I have an inquiry regarding your products on Shopia.')) }},
    position: {{ Js::from(old('whatsapp_position', $setting->whatsapp_position ?? 'bottom-right')) }},
    headerTitle: {{ Js::from(old('whatsapp_header_title', $setting->whatsapp_header_title ?? 'Chat with WhatsApp Support')) }},
    headerSubtitle: {{ Js::from(old('whatsapp_header_subtitle', $setting->whatsapp_header_subtitle ?? 'Typically replies in a few minutes')) }},
    buttonText: {{ Js::from(old('whatsapp_button_text', $setting->whatsapp_button_text ?? 'Start WhatsApp Chat')) }},
    showFloatingButton: {{ Js::from(old('whatsapp_show_floating_button', (bool) ($setting->whatsapp_show_floating_button ?? true))) }},
    isCardOpen: true,

    get cleanNumber() {
        return (this.number || '').replace(/[^0-9]/g, '');
    },
    get waUrl() {
        if (!this.cleanNumber) return '#';
        const msg = encodeURIComponent(this.defaultMessage || '');
        return `https://wa.me/${this.cleanNumber}?text=${msg}`;
    },
    insertTemplate(text) {
        this.defaultMessage = text;
    }
}">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <x-breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'System Settings', 'url' => route('admin.settings.index')],
                ['label' => 'WhatsApp Connection']
            ]" />
            <div class="flex items-center gap-3 mt-2">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xl shadow-inner">
                    💬
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">WhatsApp Connection Settings</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Configure instant messaging, floating widget positions, welcome triggers, and store hotlines.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a :href="waUrl" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 hover:bg-emerald-100 transition shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                <span>Test Connection</span>
            </a>

            <button type="submit" form="whatsapp-form" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 dark:bg-emerald-600 dark:hover:bg-emerald-500 transition shadow-lg shadow-emerald-600/20 active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-semibold space-y-1 shadow-sm">
            <div class="font-bold flex items-center gap-2">
                <span>⚠️</span>
                <span>Please check the form for errors:</span>
            </div>
            <ul class="list-disc pl-7 text-[11px] space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Column -->
        <form id="whatsapp-form" action="{{ route('admin.whatsapp-settings.update') }}" method="POST" class="lg:col-span-7 space-y-6">
            @csrf

            <!-- Card 1: Global Toggle & Main Credentials -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Channel Configuration</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Enable or disable WhatsApp support across your entire store.</p>
                    </div>

                    <!-- Enable Switch -->
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="whatsapp_is_enabled" value="1" x-model="isEnabled" class="sr-only peer">
                        <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:after:border-slate-600 peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- WhatsApp Phone Number -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            WhatsApp Business Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-medium text-xs">
                                📱 +
                            </div>
                            <input type="text" name="whatsapp_number" x-model="number" required placeholder="8801800000000" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                        </div>
                        <p class="mt-1 text-[11px] text-slate-400">Include your country code without `+` or spaces (e.g. <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-emerald-600 dark:text-emerald-400 font-mono">8801800000000</code> for Bangladesh).</p>
                    </div>

                    <!-- Enable Floating Widget -->
                    <div class="pt-2 flex items-center justify-between bg-slate-50 dark:bg-slate-950/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white">Floating Chat Widget</div>
                            <div class="text-[11px] text-slate-400">Show floating WhatsApp chat button in bottom corner of website pages.</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="whatsapp_show_floating_button" value="1" x-model="showFloatingButton" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:after:border-slate-600 peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Card 2: Default Message & Templates -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Welcome & Pre-filled Message</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">When shoppers click the button, this message automatically populates their WhatsApp app.</p>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-300">Default Pre-filled Message</label>
                            <span class="text-[11px] text-slate-400" x-text="(defaultMessage || '').length + '/500 chars'"></span>
                        </div>
                        <textarea name="whatsapp_default_message" x-model="defaultMessage" rows="3" placeholder="Hello! I am inquiring about products on Shopia." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition leading-relaxed"></textarea>
                    </div>

                    <!-- Quick Templates -->
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Quick Template Suggestions:</span>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="insertTemplate('Hello! I would like to inquire about products on Shopia.')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300 text-[11px] font-medium transition border border-slate-200/60 dark:border-slate-700/60">
                                💬 Product Inquiry
                            </button>
                            <button type="button" @click="insertTemplate('Hi! I need help with my current order and delivery status.')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300 text-[11px] font-medium transition border border-slate-200/60 dark:border-slate-700/60">
                                📦 Order Tracking
                            </button>
                            <button type="button" @click="insertTemplate('Hello, I want to place a custom bulk / wholesale order.')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300 text-[11px] font-medium transition border border-slate-200/60 dark:border-slate-700/60">
                                💼 Wholesale Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Custom Widget Design & Position -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Widget Customization & Position</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Customize the copy and placement of the floating chat launcher.</p>
                </div>

                <div class="space-y-4 text-xs">

                    <!-- Widget Position Selector -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-2">Screen Placement</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center p-3.5 rounded-2xl border-2 cursor-pointer transition-all" :class="position === 'bottom-right' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950'">
                                <input type="radio" name="whatsapp_position" value="bottom-right" x-model="position" class="sr-only">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs shadow-xs">
                                        ↘️
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">Bottom Right</div>
                                        <div class="text-[10px] text-slate-400">Standard standard corner</div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3.5 rounded-2xl border-2 cursor-pointer transition-all" :class="position === 'bottom-left' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950'">
                                <input type="radio" name="whatsapp_position" value="bottom-left" x-model="position" class="sr-only">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs shadow-xs">
                                        ↙️
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">Bottom Left</div>
                                        <div class="text-[10px] text-slate-400">Alternate left corner</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Header Title -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Widget Header Title</label>
                        <input type="text" name="whatsapp_header_title" x-model="headerTitle" placeholder="Chat with WhatsApp Support" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>

                    <!-- Header Subtitle -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Widget Subtitle / Availability</label>
                        <input type="text" name="whatsapp_header_subtitle" x-model="headerSubtitle" placeholder="Typically replies in a few minutes" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>

                    <!-- Button CTA Text -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Action Button Text</label>
                        <input type="text" name="whatsapp_button_text" x-model="buttonText" placeholder="Start WhatsApp Chat" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition">
                    </div>

                </div>
            </div>

        </form>

        <!-- Live Widget Preview Column -->
        <div class="lg:col-span-5 sticky top-6 space-y-4">
            <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-xl border border-slate-800 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Live Widget Simulator</h4>
                    </div>
                    <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full font-mono" x-text="position"></span>
                </div>

                <!-- Web Page Mockup Container -->
                <div class="relative bg-slate-950 rounded-2xl p-4 min-h-[380px] border border-slate-800 overflow-hidden flex flex-col justify-between">
                    <!-- Fake Page Elements -->
                    <div class="space-y-3 opacity-30 pointer-events-none">
                        <div class="h-4 w-1/3 bg-slate-700 rounded-md"></div>
                        <div class="h-20 w-full bg-slate-800 rounded-xl"></div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="h-16 bg-slate-800 rounded-xl"></div>
                            <div class="h-16 bg-slate-800 rounded-xl"></div>
                        </div>
                    </div>

                    <!-- Simulator Notification Badge -->
                    <template x-if="!isEnabled">
                        <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-xs flex items-center justify-center p-6 text-center z-30">
                            <div class="space-y-2">
                                <div class="text-2xl">🚫</div>
                                <div class="text-xs font-bold text-slate-300">WhatsApp Widget Disabled</div>
                                <div class="text-[11px] text-slate-500">Toggle "Channel Configuration" to enable storefront preview.</div>
                            </div>
                        </div>
                    </template>

                    <!-- Interactive Floating WhatsApp Mockup -->
                    <div class="absolute bottom-4 z-20 transition-all duration-300"
                        :class="position === 'bottom-left' ? 'left-4' : 'right-4'">
                        
                        <!-- Open Popup Card Mockup -->
                        <div x-show="isCardOpen && showFloatingButton" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="mb-3 w-72 bg-white text-slate-900 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                            <!-- Card Header -->
                            <div class="bg-emerald-600 p-3.5 text-white flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-sm">
                                        💬
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs leading-tight" x-text="headerTitle || 'Chat with WhatsApp Support'"></div>
                                        <div class="text-[10px] text-emerald-100 leading-tight" x-text="headerSubtitle || 'Typically replies in a few minutes'"></div>
                                    </div>
                                </div>
                                <button type="button" @click="isCardOpen = false" class="text-white/80 hover:text-white text-base font-bold">&times;</button>
                            </div>

                            <!-- Card Body -->
                            <div class="p-3.5 bg-slate-50 space-y-3">
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs text-[11px] text-slate-700 leading-relaxed">
                                    👋 Hi there! How can we help you today? Ask us anything about products, delivery or payment.
                                </div>

                                <a :href="waUrl" target="_blank" class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20 transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span x-text="buttonText || 'Start WhatsApp Chat'"></span>
                                </a>
                            </div>
                        </div>

                        <!-- Floating Button Bubble Launcher -->
                        <template x-if="showFloatingButton">
                            <button type="button" @click="isCardOpen = !isCardOpen" class="w-13 h-13 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white shadow-xl flex items-center justify-center transition-all hover:scale-105 active:scale-95 group relative">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-slate-900"></span>
                            </button>
                        </template>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection
