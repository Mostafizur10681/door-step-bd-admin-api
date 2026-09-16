<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Coupon;
use App\Services\OrderService;

$product = Product::first();
if (!$product) {
    echo "No product found to test order placement\n";
    exit;
}

$orderService = app(OrderService::class);

$orderData = [
    'customer_name' => 'John Doe',
    'customer_phone' => '01711111111',
    'customer_email' => 'john@example.com',
    'district' => 'Dhaka',
    'address' => 'House 12, Road 4, Dhanmondi',
    'coupon_code' => 'ACTIVE20',
    'items' => [
        [
            'product_id' => $product->id,
            'quantity' => 2,
        ]
    ]
];

try {
    $order = $orderService->createOrder($orderData);
    echo "Order Created Successfully: " . $order->order_number . "\n";
    echo "  Total: ৳" . $order->total . "\n";
    echo "  Coupon Code: " . $order->coupon_code . "\n";
    echo "  Coupon Discount: ৳" . $order->coupon_discount . "\n";
    echo "  Shipping: ৳" . $order->shipping_amount . "\n";
    
    // Clean up test order
    $orderService->deleteOrder($order->id);
    echo "Test order cleaned up successfully.\n";
} catch (\Exception $e) {
    echo "Order error: " . $e->getMessage() . "\n";
}
