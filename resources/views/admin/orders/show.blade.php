@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Top Bar & Breadcrumbs -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <x-breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Orders', 'url' => route('admin.orders.index')],
                ['label' => 'Order #' . $order->order_number]
            ]" />
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white font-mono">Order #{{ $order->order_number }}</h1>
                @php
                    $s = strtolower($order->status);
                    $badgeClasses = match($s) {
                        'delivered', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400',
                        'shipped', 'out-for-delivery' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400',
                        'processing' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400',
                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400',
                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                    };
                @endphp
                <span class="inline-block px-3 py-0.5 rounded-full text-xs font-bold border {{ $badgeClasses }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Placed on {{ $order->created_at ? $order->created_at->format('F d, Y \a\t h:i A') : 'N/A' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition-all cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Print Invoice</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition-colors">
                Back to Orders
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Order Items & Payment Info -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Items Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ordered Items</h3>
                    <span class="text-xs text-gray-500 font-semibold">{{ $order->items->count() }} unique items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="px-5 py-3">Item</th>
                                <th class="px-5 py-3">Unit Price</th>
                                <th class="px-5 py-3">Qty</th>
                                <th class="px-5 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($order->items as $item)
                                @php
                                    $p = $item->product;
                                    $img = $p ? ($p->image ?: ($p->images->first()->image_path ?? '')) : '';
                                @endphp
                                <tr>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shrink-0 flex items-center justify-center">
                                                @if($img)
                                                    <img src="{{ str_starts_with($img, 'data:image') || str_starts_with($img, 'http') ? $img : asset('storage/' . $img) }}" class="h-full w-full object-cover">
                                                @else
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-white text-xs">{{ $p ? $p->name : ($item->product_name ?? 'Product Item') }}</h4>
                                                @if(!empty($item->attributes))
                                                    @php
                                                        $attrs = is_array($item->attributes) ? $item->attributes : json_decode($item->attributes, true);
                                                    @endphp
                                                    @if(is_array($attrs) && count($attrs) > 0)
                                                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">Attributes:</span>
                                                            @foreach($attrs as $key => $val)
                                                                @php
                                                                    if (is_array($val)) {
                                                                        if (isset($val['name']) || isset($val['value'])) {
                                                                            $display = ($val['name'] ?? 'Option') . ': ' . ($val['value'] ?? '');
                                                                        } else {
                                                                            $display = is_numeric($key) ? implode(', ', $val) : ucfirst($key) . ': ' . implode(', ', $val);
                                                                        }
                                                                    } else {
                                                                        $display = is_numeric($key) ? $val : ucfirst($key) . ': ' . $val;
                                                                    }
                                                                @endphp
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 shadow-2xs">
                                                                    {{ $display }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @elseif(is_string($item->attributes) && trim($item->attributes) !== '')
                                                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">Attributes:</span>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 shadow-2xs">
                                                                {{ $item->attributes }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-gray-700 dark:text-gray-300">
                                        ৳{{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">
                                        × {{ $item->quantity }}
                                    </td>
                                    <td class="px-5 py-4 text-right font-bold text-gray-900 dark:text-white text-sm">
                                        ৳{{ number_format($item->price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Price Summary Breakdown -->
                <div class="p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 flex justify-end">
                    <div class="w-72 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-900 dark:text-white">৳{{ number_format($order->subtotal ?: ($order->total - ($order->shipping_amount ?? 0)), 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping Charge</span>
                            <span class="font-semibold text-gray-900 dark:text-white">৳{{ number_format($order->shipping_amount ?? 0, 2) }}</span>
                        </div>
                        @if($order->discount_amount)
                            <div class="flex items-center justify-between text-emerald-600">
                                <span>Discount</span>
                                <span class="font-semibold">-৳{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 dark:border-gray-800 pt-2 flex items-center justify-between text-sm font-bold text-gray-900 dark:text-white">
                            <span>Grand Total</span>
                            <span class="text-primary text-base font-bold">৳{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Status Updater, Customer & Shipping Details -->
        <div class="space-y-6">
            
            <!-- Update Status Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Update Order Status</h3>
                
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="space-y-3.5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Fulfillment Status</label>
                        <select name="status" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-primary">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Payment Status</label>
                        <select name="payment_status" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-primary">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 transition-all cursor-pointer">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Customer Details Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-3 text-xs">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Customer Information</h3>

                <div>
                    <span class="text-gray-500 block">Name</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $order->customer_name ?: ($order->user->name ?? 'Guest User') }}</span>
                </div>

                <div>
                    <span class="text-gray-500 block">Phone</span>
                    <a href="tel:{{ $order->customer_phone }}" class="font-mono text-primary font-bold hover:underline">{{ $order->customer_phone ?: 'N/A' }}</a>
                </div>

                <div>
                    <span class="text-gray-500 block">Email</span>
                    <span class="font-mono text-gray-700 dark:text-gray-300">{{ $order->customer_email ?: ($order->user->email ?? 'N/A') }}</span>
                </div>

                <div>
                    <span class="text-gray-500 block">Payment Method</span>
                    <span class="font-bold uppercase text-gray-800 dark:text-gray-200">{{ $order->payment_method ?: 'Cash on Delivery' }}</span>
                </div>
            </div>

            <!-- Shipping Address Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-3 text-xs">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Delivery Address</h3>

                @php
                    $isShipDiff = !empty($order->ship_different) || (!empty($order->ship_address) && $order->ship_address !== $order->address);
                    if ($isShipDiff && !empty($order->ship_address)) {
                        $addressParts = array_filter([
                            $order->ship_address,
                            $order->ship_town_city,
                            $order->ship_district,
                            $order->ship_country
                        ]);
                    } else {
                        $addressParts = array_filter([
                            $order->address,
                            $order->thana,
                            $order->district,
                            $order->division
                        ]);
                    }
                    $fullAddress = implode(', ', $addressParts);
                @endphp

                @if($isShipDiff && (!empty($order->ship_customer_name) || !empty($order->ship_phone)))
                    <div class="space-y-1.5 pb-2.5 border-b border-gray-100 dark:border-gray-800">
                        @if(!empty($order->ship_customer_name))
                            <div>
                                <span class="text-gray-500 block">Recipient Name</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $order->ship_customer_name }}</span>
                            </div>
                        @endif
                        @if(!empty($order->ship_phone))
                            <div>
                                <span class="text-gray-500 block">Recipient Phone</span>
                                <a href="tel:{{ $order->ship_phone }}" class="font-mono text-primary font-bold hover:underline">{{ $order->ship_phone }}</a>
                            </div>
                        @endif
                    </div>
                @endif

                <p class="text-gray-800 dark:text-gray-200 font-medium leading-relaxed">
                    {{ $fullAddress ?: 'No address specified.' }}
                </p>

                @if($order->customer_notes)
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500 block text-[10px] uppercase font-bold">Delivery Instructions</span>
                        <p class="italic text-gray-700 dark:text-gray-300 mt-0.5">{{ $order->customer_notes }}</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
