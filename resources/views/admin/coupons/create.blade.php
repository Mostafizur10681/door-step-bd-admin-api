@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-20 px-2 sm:px-4">

    <!-- Header & Breadcrumb -->
    <div>
        <x-breadcrumbs :items="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Coupons', 'url' => route('admin.coupons.index')],
            ['label' => 'Create Coupon']
        ]" />
        <div class="flex items-center justify-between mt-2">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                    Create New Coupon
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Configure discount rules, order limitations, and validity schedule with datepicker.
                </p>
            </div>
            <a href="{{ route('admin.coupons.index') }}" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition">
                ← Back to List
            </a>
        </div>
    </div>

    <!-- Error Summary -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs shadow-xs">
            <div class="font-bold mb-1">Please correct the following errors:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <div 
        x-data="{
            code: '{{ old('code') }}',
            discountType: '{{ old('discount_type', 'percentage') }}',
            isGenerating: false,
            async generateCode() {
                this.isGenerating = true;
                try {
                    const res = await fetch('{{ route('admin.coupons.generate-code') }}');
                    const data = await res.json();
                    if (data && data.code) {
                        this.code = data.code;
                    }
                } catch (e) {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    let gen = '';
                    for(let i=0; i<8; i++) gen += chars.charAt(Math.floor(Math.random() * chars.length));
                    this.code = gen;
                } finally {
                    this.isGenerating = false;
                }
            }
        }"
        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm transition-colors space-y-6"
    >
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-6" id="couponForm">
            @csrf

            <!-- Section 1: Basic Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center">1</span>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Coupon Identification</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Code -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Coupon Code <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input 
                                    type="text" 
                                    name="code" 
                                    x-model="code"
                                    @input="code = code.toUpperCase().replace(/[^A-Z0-9_-]/g, '')"
                                    required 
                                    placeholder="e.g. SUMMER20" 
                                    class="w-full pl-3.5 pr-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono font-bold tracking-wider text-slate-900 dark:text-white uppercase focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                                >
                            </div>
                            <button 
                                type="button" 
                                @click="generateCode()" 
                                :disabled="isGenerating"
                                class="px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold rounded-xl transition flex items-center gap-1.5 shrink-0 cursor-pointer shadow-xs"
                                title="Generate unique random code"
                            >
                                <span x-show="!isGenerating">⚡ Generate</span>
                                <span x-show="isGenerating" class="animate-spin">⏳</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Uppercase letters, numbers, hyphens only.</p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Coupon Name / Campaign Title
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            placeholder="e.g. Summer Mega Discount 2026" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Internal title or banner headline.</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Description &amp; Terms (Optional)
                    </label>
                    <textarea 
                        name="description" 
                        rows="2" 
                        placeholder="e.g. Applicable on all products with minimum order amount of ৳1,000..." 
                        class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                    >{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Section 2: Discount & Value Rules -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center">2</span>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Discount &amp; Value Rules</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Discount Type -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Discount Type <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label 
                                class="flex items-center justify-center gap-1.5 p-2.5 rounded-xl border text-xs font-bold cursor-pointer transition"
                                :class="discountType === 'percentage' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-500 ring-2 ring-emerald-500/20' : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:bg-slate-100'"
                            >
                                <input type="radio" name="discount_type" value="percentage" x-model="discountType" class="hidden">
                                <span>Percentage (%)</span>
                            </label>
                            <label 
                                class="flex items-center justify-center gap-1.5 p-2.5 rounded-xl border text-xs font-bold cursor-pointer transition"
                                :class="discountType === 'fixed' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-500 ring-2 ring-emerald-500/20' : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:bg-slate-100'"
                            >
                                <input type="radio" name="discount_type" value="fixed" x-model="discountType" class="hidden">
                                <span>Fixed (৳)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Discount Value -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            <span x-text="discountType === 'percentage' ? 'Discount Percentage (%) *' : 'Discount Amount (৳) *'"></span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0.01" 
                                name="discount_value" 
                                value="{{ old('discount_value') }}" 
                                required 
                                :placeholder="discountType === 'percentage' ? 'e.g. 15' : 'e.g. 200'" 
                                class="w-full pl-3.5 pr-8 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            >
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400" x-text="discountType === 'percentage' ? '%' : '৳'"></span>
                        </div>
                    </div>

                    <!-- Max Discount Amount (Only for percentage) -->
                    <div x-show="discountType === 'percentage'" x-transition>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Maximum Cap (৳) <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                name="maximum_discount" 
                                value="{{ old('maximum_discount') }}" 
                                placeholder="e.g. 500 (No cap if empty)" 
                                class="w-full pl-3.5 pr-8 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            >
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400">৳</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Maximum discount amount allowed.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                    <!-- Minimum Order Amount -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Minimum Order Amount (৳)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                name="minimum_order_amount" 
                                value="{{ old('minimum_order_amount') }}" 
                                placeholder="e.g. 1000" 
                                class="w-full pl-3.5 pr-8 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            >
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400">৳</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Leave empty for no minimum spend.</p>
                    </div>

                    <!-- Usage Limit Total -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Total Usage Limit
                        </label>
                        <input 
                            type="number" 
                            min="1" 
                            name="usage_limit" 
                            value="{{ old('usage_limit') }}" 
                            placeholder="e.g. 100" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Leave empty for unlimited total redemptions.</p>
                    </div>

                    <!-- Usage Limit Per User -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Limit Per Customer
                        </label>
                        <input 
                            type="number" 
                            min="1" 
                            name="usage_limit_per_user" 
                            value="{{ old('usage_limit_per_user', 1) }}" 
                            placeholder="e.g. 1" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">How many times each user can use this.</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Schedule & Datepicker -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center">3</span>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Validity Period &amp; Datepicker</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <!-- 1. Starts At Datepicker -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Start Date &amp; Time <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 dark:text-emerald-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="starts_at"
                                name="starts_at" 
                                value="{{ old('starts_at') }}" 
                                placeholder="Select start date and time..." 
                                class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition cursor-pointer"
                            >
                            <button 
                                type="button" 
                                onclick="clearPicker('starts_at')" 
                                class="absolute right-3 text-slate-400 hover:text-rose-500 text-sm font-bold transition p-0.5" 
                                title="Clear Start Date"
                            >
                                ✕
                            </button>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                            <span class="text-[10px] text-slate-400 font-bold uppercase mr-1">Quick:</span>
                            <button type="button" onclick="setStartDate('now')" class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 text-[10px] font-semibold transition cursor-pointer">Now</button>
                            <button type="button" onclick="setStartDate('tomorrow')" class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 text-[10px] font-semibold transition cursor-pointer">Tomorrow</button>
                            <button type="button" onclick="setStartDate('next_week')" class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 text-[10px] font-semibold transition cursor-pointer">Next Week</button>
                        </div>
                        <p class="text-[11px] text-slate-400">Leave blank to activate immediately upon creation.</p>
                    </div>

                    <!-- 2. Expires At Datepicker -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Expiry Date &amp; Time <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 dark:text-emerald-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="expires_at"
                                name="expires_at" 
                                value="{{ old('expires_at') }}" 
                                placeholder="Select expiry date and time..." 
                                class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition cursor-pointer"
                            >
                            <button 
                                type="button" 
                                onclick="clearPicker('expires_at')" 
                                class="absolute right-3 text-slate-400 hover:text-rose-500 text-sm font-bold transition p-0.5" 
                                title="Clear Expiry Date"
                            >
                                ✕
                            </button>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                            <span class="text-[10px] text-slate-400 font-bold uppercase mr-1">Presets:</span>
                            <button type="button" onclick="setExpiryDate(7)" class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 text-[10px] font-semibold transition cursor-pointer">+7 Days</button>
                            <button type="button" onclick="setExpiryDate(15)" class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 text-[10px] font-semibold transition cursor-pointer">+15 Days</button>
                            <button type="button" onclick="setExpiryDate(30)" class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 text-[10px] font-semibold transition cursor-pointer">+30 Days</button>
                            <button type="button" onclick="setExpiryDate(90)" class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 text-[10px] font-semibold transition cursor-pointer">+3 Months</button>
                        </div>
                        <p class="text-[11px] text-slate-400">Leave blank for a permanent coupon that never expires.</p>
                    </div>

                </div>

                <!-- Active Status Toggle -->
                <div class="pt-2">
                    <label class="inline-flex items-center gap-3 cursor-pointer p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 select-none hover:border-emerald-300 dark:hover:border-emerald-800 transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Active Status</div>
                            <div class="text-[11px] text-slate-400">Coupon will be immediately eligible for valid checkout carts.</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center gap-2">
                    <span>Save &amp; Publish Coupon</span>
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Flatpickr Initialization Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            window.startPickerInstance = flatpickr("#starts_at", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "F j, Y at h:i K",
                altInputClass: "w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition cursor-pointer",
                time_24hr: false,
                minuteIncrement: 1,
                onChange: function(selectedDates) {
                    if (selectedDates[0] && window.expiryPickerInstance) {
                        window.expiryPickerInstance.set('minDate', selectedDates[0]);
                    }
                }
            });

            window.expiryPickerInstance = flatpickr("#expires_at", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "F j, Y at h:i K",
                altInputClass: "w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition cursor-pointer",
                time_24hr: false,
                minuteIncrement: 1,
            });
        }
    });

    function clearPicker(id) {
        if (id === 'starts_at' && window.startPickerInstance) {
            window.startPickerInstance.clear();
            if (window.expiryPickerInstance) {
                window.expiryPickerInstance.set('minDate', null);
            }
        } else if (id === 'expires_at' && window.expiryPickerInstance) {
            window.expiryPickerInstance.clear();
        }
    }

    function setStartDate(type) {
        if (!window.startPickerInstance) return;
        const now = new Date();
        if (type === 'now') {
            window.startPickerInstance.setDate(now, true);
        } else if (type === 'tomorrow') {
            const tom = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
            window.startPickerInstance.setDate(tom, true);
        } else if (type === 'next_week') {
            const nextW = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 7, 0, 0, 0);
            window.startPickerInstance.setDate(nextW, true);
        }
    }

    function setExpiryDate(days) {
        if (!window.expiryPickerInstance) return;
        const baseDate = (window.startPickerInstance && window.startPickerInstance.selectedDates[0]) 
            ? new Date(window.startPickerInstance.selectedDates[0]) 
            : new Date();
        const expDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate() + days, 23, 59, 0);
        window.expiryPickerInstance.setDate(expDate, true);
    }
</script>
@endsection
