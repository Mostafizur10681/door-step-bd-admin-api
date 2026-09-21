@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">E-commerce Dashboard</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Welcome back! Here is an overview of your store's live performance and transactions.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 text-xs font-bold shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Live System
            </span>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Revenue -->
        <x-stats-card 
            title="Total Revenue" 
            value="৳{{ number_format($totalRevenue, 2) }}" 
            trend="+14.2%" 
            :isPositive="true" 
            description="from completed orders"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stats-card>

        <!-- Total Orders -->
        <x-stats-card 
            title="Total Orders" 
            value="{{ number_format($totalOrders) }}" 
            trend="+8.4%" 
            :isPositive="true" 
            description="recorded transactions"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </x-slot:icon>
        </x-stats-card>

        <!-- Total Products -->
        <x-stats-card 
            title="Total Products" 
            value="{{ number_format($totalProducts) }}" 
            description="items currently in catalog"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </x-slot:icon>
        </x-stats-card>

        <!-- Total Customers -->
        <x-stats-card 
            title="Total Customers" 
            value="{{ number_format($totalCustomers) }}" 
            trend="+21.1%" 
            :isPositive="true" 
            description="registered customer accounts"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </x-slot:icon>
        </x-stats-card>
    </div>

    <!-- 3 Analytics Charts -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Revenue Overview -->
        <div class="card-hover-effect bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Revenue Overview</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Monthly income vs expenditure trend</p>
                </div>
                <span class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </span>
            </div>
            <div class="h-60">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Sales Analytics -->
        <div class="card-hover-effect bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Sales Analytics</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Weekly sales volume breakdown</p>
                </div>
                <span class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </span>
            </div>
            <div class="h-60">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Orders Trend -->
        <div class="card-hover-effect bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Orders Trend</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Transaction counts progression</p>
                </div>
                <span class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                </span>
            </div>
            <div class="h-60">
                <canvas id="ordersTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Recent Orders & Top Selling Products -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Recent Orders Table -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Orders</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Latest transactions placed in store</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                    View All Orders
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5">Order Number</th>
                            <th class="px-5 py-3.5">Customer</th>
                            <th class="px-5 py-3.5">Amount</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-mono">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-700 dark:text-slate-300">
                                    {{ $order->customer_name ?: ($order->user->name ?? 'Guest') }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                    ৳{{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $s = strtolower($order->status);
                                        $badgeClasses = match($s) {
                                            'delivered', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800',
                                            'shipped', 'out-for-delivery' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-400 dark:border-blue-800',
                                            'processing' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-800',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-400 dark:border-rose-800',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeClasses }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                    {{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">No orders recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Top Selling Products</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Products generating highest sales</p>
            </div>
            <div class="space-y-3">
                @forelse($topProducts as $item)
                    @php
                        $p = $item->product;
                        $firstImg = $p->image ?: ($p->images->first()->image_path ?? '');
                    @endphp
                    <div class="flex items-center gap-3 py-2 border-b border-slate-100 dark:border-slate-800/80 last:border-0">
                        <div class="h-11 w-11 rounded-xl overflow-hidden shrink-0 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                            @if($firstImg)
                                @if(str_starts_with($firstImg, 'data:image') || str_starts_with($firstImg, 'http'))
                                    <img src="{{ $firstImg }}" class="h-full w-full object-cover">
                                @else
                                    <img src="{{ asset('storage/' . $firstImg) }}" class="h-full w-full object-cover">
                                @endif
                            @else
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $p->name }}</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $item->sales_count }} Sales</p>
                        </div>
                        <div class="text-right text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            ৳{{ number_format($item->total_revenue, 2) }}
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No sales recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Initialize Dashboard Chart.js Scripts with Dynamic Theme Support -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    let revenueChartInstance = null;
    let salesChartInstance = null;
    let ordersTrendChartInstance = null;

    function initOrUpdateCharts() {
        const themeColors = window.getChartThemeColors ? window.getChartThemeColors() : {
            isDark: document.documentElement.classList.contains('dark'),
            textColor: '#64748B',
            gridColor: 'rgba(226, 232, 240, 0.8)',
            tooltipBg: '#0F172A',
            tooltipText: '#FFFFFF',
            primaryColor: '#10B981',
            primaryFill: 'rgba(16, 185, 129, 0.15)',
        };

        // 1. Revenue Overview Line Chart
        const revCtx = document.getElementById('revenueChart');
        if (revCtx) {
            if (revenueChartInstance) {
                revenueChartInstance.destroy();
            }
            revenueChartInstance = new Chart(revCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($revenueLabels) !!},
                    datasets: [
                        {
                            label: 'Revenue (৳)',
                            data: {!! json_encode($revenueValues) !!},
                            borderColor: '#10B981',
                            backgroundColor: '#10B981',
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        },
                        {
                            label: 'Expenses (৳)',
                            data: {!! json_encode($expenseValues) !!},
                            borderColor: '#38BDF8',
                            backgroundColor: '#38BDF8',
                            borderDash: [4, 4],
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top', 
                            labels: { 
                                boxWidth: 12, 
                                color: themeColors.textColor,
                                font: { size: 10, weight: 600 } 
                            } 
                        },
                        tooltip: {
                            backgroundColor: themeColors.tooltipBg,
                            titleColor: themeColors.tooltipText,
                            bodyColor: themeColors.tooltipText,
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: { 
                            grid: { color: themeColors.gridColor }, 
                            ticks: { color: themeColors.textColor, font: { size: 10 } } 
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { color: themeColors.textColor, font: { size: 10 } } 
                        }
                    }
                }
            });
        }

        // 2. Sales Analytics Bar Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            if (salesChartInstance) {
                salesChartInstance.destroy();
            }
            salesChartInstance = new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($salesLabels) !!},
                    datasets: [{
                        label: 'Orders',
                        data: {!! json_encode($salesValues) !!},
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                        hoverBackgroundColor: '#059669',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: themeColors.tooltipBg,
                            titleColor: themeColors.tooltipText,
                            bodyColor: themeColors.tooltipText,
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: { 
                            grid: { color: themeColors.gridColor }, 
                            ticks: { color: themeColors.textColor, font: { size: 10 } } 
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { color: themeColors.textColor, font: { size: 10 } } 
                        }
                    }
                }
            });
        }

        // 3. Orders Trend Area Chart
        const trendCtx = document.getElementById('ordersTrendChart');
        if (trendCtx) {
            if (ordersTrendChartInstance) {
                ordersTrendChartInstance.destroy();
            }
            const gradient = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, themeColors.primaryFill);
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            ordersTrendChartInstance = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($orderTrendLabels) !!},
                    datasets: [{
                        label: 'Orders',
                        data: {!! json_encode($orderTrendValues) !!},
                        borderColor: '#10B981',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: themeColors.tooltipBg,
                            titleColor: themeColors.tooltipText,
                            bodyColor: themeColors.tooltipText,
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: { 
                            grid: { color: themeColors.gridColor }, 
                            ticks: { color: themeColors.textColor, font: { size: 10 } } 
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { color: themeColors.textColor, font: { size: 10 } } 
                        }
                    }
                }
            });
        }
    }

    // Initial render
    initOrUpdateCharts();

    // Re-render charts when theme switches dynamically
    window.addEventListener('themechanged', () => {
        setTimeout(initOrUpdateCharts, 50);
    });
});
</script>
@endsection
