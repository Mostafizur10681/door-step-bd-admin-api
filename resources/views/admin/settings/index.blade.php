@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <x-breadcrumbs :items="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'System Settings']
        ]" />
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">Store & System Settings</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400">Configure global preferences, store currency, operational parameters, and cache controls.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- General Preferences -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Store Configuration</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Store Name</label>
                    <input type="text" value="Shopia" readonly class="w-full px-3.5 py-2 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-xl text-gray-600 dark:text-gray-300 font-medium">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Primary Currency</label>
                    <input type="text" value="BDT (৳) - Bangladeshi Taka" readonly class="w-full px-3.5 py-2 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-xl text-gray-600 dark:text-gray-300 font-medium">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Default Timezone</label>
                    <input type="text" value="Asia/Dhaka (UTC+6)" readonly class="w-full px-3.5 py-2 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-xl text-gray-600 dark:text-gray-300 font-medium">
                </div>
            </div>
        </div>

        <!-- Quick Links & Actions -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Administrative Hub</h3>
            
            <div class="space-y-2.5">
                <a href="{{ route('admin.settings.profile') }}" class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/5 hover:border-primary border border-gray-200 dark:border-gray-800 flex items-center justify-between transition-colors">
                    <div>
                        <h4 class="font-bold text-xs text-gray-900 dark:text-white">Admin Profile & Password</h4>
                        <p class="text-[11px] text-gray-400">Update your email, contact phone and security password</p>
                    </div>
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>

                <a href="{{ route('admin.contact-settings.index') }}" class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/5 hover:border-primary border border-gray-200 dark:border-gray-800 flex items-center justify-between transition-colors">
                    <div>
                        <h4 class="font-bold text-xs text-gray-900 dark:text-white">Contact & Map Settings</h4>
                        <p class="text-[11px] text-gray-400">Manage public store contact email, phone and location</p>
                    </div>
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>

                <a href="{{ route('admin.whatsapp-settings.index') }}" class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-emerald-50 hover:border-emerald-500 border border-gray-200 dark:border-gray-800 flex items-center justify-between transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">💬</div>
                        <div>
                            <h4 class="font-bold text-xs text-gray-900 dark:text-white group-hover:text-emerald-600 transition-colors">WhatsApp Connection Settings</h4>
                            <p class="text-[11px] text-gray-400">Configure phone number, floating chat widget, position & default message</p>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-gray-400 group-hover:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>

                <a href="{{ route('admin.footer-settings.index') }}" class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/5 hover:border-primary border border-gray-200 dark:border-gray-800 flex items-center justify-between transition-colors">
                    <div>
                        <h4 class="font-bold text-xs text-gray-900 dark:text-white">Footer & Social Channels</h4>
                        <p class="text-[11px] text-gray-400">Manage copyright statement and social media handles</p>
                    </div>
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
