@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Top Bar & Breadcrumbs -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <x-breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Customers', 'url' => route('admin.customers.index')],
                ['label' => $customer->name]
            ]" />
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">Customer Profile: {{ $customer->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl">
                Back to Customers
            </a>
        </div>
    </div>

    <!-- Customer Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Customer Card -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm space-y-4 text-xs">
            <div class="flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="h-14 w-14 rounded-2xl bg-primary/10 text-primary font-bold flex items-center justify-center text-xl">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h3>
                    <p class="text-gray-500 font-mono text-[11px]">{{ $customer->email }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-bold {{ $customer->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </div>
            </div>

            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Phone</span>
                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $customer->phone ?: 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Total Spent</span>
                    <span class="font-bold text-primary text-sm">৳{{ number_format($totalSpent, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Total Orders</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $customer->orders->count() }} Orders</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Registered</span>
                    <span class="font-mono text-gray-700 dark:text-gray-300">{{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>

            <!-- Saved Addresses -->
            @if($customer->addresses && $customer->addresses->count() > 0)
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-2">Saved Addresses</h4>
                    <div class="space-y-2">
                        @foreach($customer->addresses as $addr)
                            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800">
                                <span class="font-bold text-gray-800 dark:text-gray-200 block">{{ $addr->address_type ?? 'Default' }}</span>
                                <p class="text-gray-500 text-[11px] mt-0.5">{{ $addr->street_address ?? $addr->address }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Order History -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Order History</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">All purchases made by this customer</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3">Order Number</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($customer->orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                <td class="px-5 py-3.5 font-bold font-mono text-gray-900 dark:text-white">
                                    #{{ $order->order_number }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 font-mono text-[11px]">
                                    {{ $order->created_at ? $order->created_at->format('M d, Y') : '' }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-gray-900 dark:text-white">
                                    ৳{{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary font-bold hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-400">No orders placed by this customer yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
