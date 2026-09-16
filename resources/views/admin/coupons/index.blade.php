@extends('layouts.admin')

@section('content')
<div class="space-y-6 pb-20 max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">

    <!-- Top Bar & Breadcrumbs -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <x-breadcrumbs :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Coupons']
                ]" />
                <div class="flex flex-wrap items-center gap-2 pt-0.5">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Discount Coupons &amp; Vouchers
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Active Promotions
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Create New Coupon</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-none">
                <a href="{{ route('admin.coupons.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !request()->filled('status') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    All Coupons ({{ $stats['total'] ?? 0 }})
                </a>
                <a href="{{ route('admin.coupons.index', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    Active ({{ $stats['active'] ?? 0 }})
                </a>
                <a href="{{ route('admin.coupons.index', ['status' => 'inactive']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'inactive' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    Inactive
                </a>
                <a href="{{ route('admin.coupons.index', ['status' => 'expired']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'expired' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    Expired ({{ $stats['expired'] ?? 0 }})
                </a>
            </div>
            <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search coupon code or name..." class="w-full pl-8 pr-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.coupons.index') }}" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-xs font-semibold">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Total Coupons</span>
                <div class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $stats['total'] ?? 0 }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Created across portal</p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Active &amp; Valid</span>
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $stats['active'] ?? 0 }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Usable at checkout</p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Expired</span>
                <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-2">{{ $stats['expired'] ?? 0 }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Past expiration date</p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Total Redemptions</span>
                <div class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0a60.082 60.082 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V14.25m-17.25 0v-8.25m17.25 8.25v-8.25m0 0a60.08 60.08 0 0 1-15.797-2.101A1.125 1.125 0 0 0 2.25 4.5v.75m17.25 0a1.125 1.125 0 0 0-1.125-1.125H3.375" /></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-2">{{ $stats['total_used'] ?? 0 }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Orders used with discount</p>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- ═══ 1. DESKTOP DATA TABLE (Screens >= 1024px) ═══ -->
    <div class="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100/90 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700/80">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center text-slate-600 dark:text-slate-300">#</th>
                        <th class="py-3.5 px-4 text-slate-600 dark:text-slate-300">Coupon Code</th>
                        <th class="py-3.5 px-4 text-slate-600 dark:text-slate-300">Discount &amp; Rules</th>
                        <th class="py-3.5 px-4 text-slate-600 dark:text-slate-300">Usage Limit</th>
                        <th class="py-3.5 px-4 text-slate-600 dark:text-slate-300">Validity Period</th>
                        <th class="py-3.5 px-4 w-24 text-center text-slate-600 dark:text-slate-300">Status</th>
                        <th class="py-3.5 px-4 w-28 text-right text-slate-600 dark:text-slate-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                    @forelse($coupons as $coupon)
                        @php
                            $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                            $usagePercent = ($coupon->usage_limit && $coupon->usage_limit > 0) ? min(100, round(($coupon->used_count / $coupon->usage_limit) * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                            
                            <!-- Index -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="text-slate-400 font-mono text-[11px]">{{ $loop->iteration + ($coupons->currentPage() - 1) * $coupons->perPage() }}</span>
                            </td>

                            <!-- Code & Name -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-mono font-black text-xs tracking-wider border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 shadow-2xs">
                                            <span>{{ $coupon->code }}</span>
                                        </span>
                                        <button 
                                            type="button" 
                                            onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Coupon code {{ $coupon->code }} copied!');" 
                                            class="text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition" 
                                            title="Copy Code"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                        </button>
                                    </div>
                                    @if($coupon->name)
                                        <div class="font-bold text-xs text-slate-900 dark:text-white">{{ $coupon->name }}</div>
                                    @endif
                                    @if($coupon->description)
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs">{{ $coupon->description }}</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Discount & Rules -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        @if($coupon->discount_type === 'percentage')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 font-black text-xs border border-purple-200 dark:border-purple-800">
                                                {{ (float)$coupon->discount_value }}% OFF
                                            </span>
                                            @if($coupon->maximum_discount)
                                                <span class="text-[10px] text-slate-400 font-medium">(Max ৳{{ number_format($coupon->maximum_discount, 0) }})</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 font-black text-xs border border-sky-200 dark:border-sky-800">
                                                ৳{{ number_format($coupon->discount_value, 0) }} OFF
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                        @if($coupon->minimum_order_amount)
                                            <span>Min Spend: <strong class="text-slate-700 dark:text-slate-300">৳{{ number_format($coupon->minimum_order_amount, 0) }}</strong></span>
                                        @else
                                            <span class="text-slate-400">No minimum spend</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Usage Limit -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1 max-w-[140px]">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $coupon->used_count }} used</span>
                                        <span class="text-slate-400">{{ $coupon->usage_limit ? 'of ' . $coupon->usage_limit : '(Unlimited)' }}</span>
                                    </div>
                                    @if($coupon->usage_limit)
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $usagePercent }}%"></div>
                                        </div>
                                    @endif
                                    @if($coupon->usage_limit_per_user)
                                        <span class="text-[10px] text-slate-400 block">Limit: {{ $coupon->usage_limit_per_user }}/customer</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Validity Period -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-0.5 text-[11px]">
                                    @if($coupon->starts_at)
                                        <div class="text-slate-500 dark:text-slate-400">
                                            From: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $coupon->starts_at->format('d M Y') }}</span>
                                        </div>
                                    @endif

                                    @if($coupon->expires_at)
                                        <div class="{{ $isExpired ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-600 dark:text-slate-300' }}">
                                            Until: <span class="font-medium">{{ $coupon->expires_at->format('d M Y') }}</span>
                                            @if($isExpired)
                                                <span class="ml-1 text-[9px] px-1 py-0.2 rounded bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold uppercase">Expired</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400">Never expires</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Toggle -->
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon->id) }}">
                                    @csrf
                                    <button type="submit" title="Click to toggle active status" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border transition cursor-pointer {{ $coupon->is_active && !$isExpired ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:border-emerald-800 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' : 'border-slate-200 bg-slate-100 text-slate-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                                        {{ $coupon->is_active && !$isExpired ? '● Active' : ($isExpired ? '● Expired' : '○ Inactive') }}
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="p-1.5 text-slate-400 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Edit Coupon">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Are you sure you want to delete coupon {{ $coupon->code }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 transition cursor-pointer" title="Delete Coupon">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400 dark:text-slate-500">
                                <div class="space-y-2 max-w-sm mx-auto">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" /></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">No coupons found</p>
                                    <p class="text-xs text-slate-400">Get started by creating your first promotional coupon discount voucher.</p>
                                    <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition mt-2">
                                        + Create First Coupon
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ 2. MOBILE CARD VIEW (Screens < 1024px) ═══ -->
    <div class="lg:hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
        @forelse($coupons as $coupon)
            @php
                $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-mono font-black text-xs tracking-wider border border-emerald-200 dark:border-emerald-800">
                            {{ $coupon->code }}
                        </span>
                        <button 
                            type="button" 
                            onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Coupon code {{ $coupon->code }} copied!');" 
                            class="text-slate-400 hover:text-emerald-600"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>

                @if($coupon->name)
                    <h3 class="font-bold text-xs text-slate-900 dark:text-white">{{ $coupon->name }}</h3>
                @endif

                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Discount</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                        {{ $coupon->discount_type === 'percentage' ? ((float)$coupon->discount_value . '% OFF') : ('৳' . number_format($coupon->discount_value, 0) . ' OFF') }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Used</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">
                        {{ $coupon->used_count }} {{ $coupon->usage_limit ? '/ ' . $coupon->usage_limit : 'times' }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-[11px] text-slate-400">
                        {{ $coupon->expires_at ? 'Expires ' . $coupon->expires_at->format('d M Y') : 'No expiry' }}
                    </span>
                    <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon->id) }}">
                        @csrf
                        <button type="submit" class="px-2 py-0.5 text-[10px] font-bold rounded-lg border transition {{ $coupon->is_active && !$isExpired ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:border-emerald-800 dark:text-emerald-300' : 'border-slate-200 bg-slate-100 text-slate-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400' }}">
                            {{ $coupon->is_active && !$isExpired ? '● Active' : ($isExpired ? '● Expired' : '○ Inactive') }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                <p class="text-xs text-slate-500">No coupons found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $coupons->links() }}
    </div>

</div>
@endsection
