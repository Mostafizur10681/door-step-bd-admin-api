<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Coupon;
use Carbon\Carbon;

echo "=== COUPON SCHEDULE & DISCOUNT TEST ===\n";

// 1. Create a test active scheduled coupon
$activeCoupon = Coupon::updateOrCreate(
    ['code' => 'ACTIVE20'],
    [
        'name' => 'Active 20% Off',
        'discount_type' => 'percentage',
        'discount_value' => 20.00,
        'maximum_discount' => 500.00,
        'minimum_order_amount' => 1000.00,
        'starts_at' => Carbon::now()->subDays(1),
        'expires_at' => Carbon::now()->addDays(7),
        'is_active' => true,
    ]
);

// 2. Create an expired coupon
$expiredCoupon = Coupon::updateOrCreate(
    ['code' => 'EXPIRED10'],
    [
        'name' => 'Expired 10% Off',
        'discount_type' => 'percentage',
        'discount_value' => 10.00,
        'starts_at' => Carbon::now()->subDays(10),
        'expires_at' => Carbon::now()->subDays(2),
        'is_active' => true,
    ]
);

// 3. Create a future scheduled coupon
$futureCoupon = Coupon::updateOrCreate(
    ['code' => 'FUTURE50'],
    [
        'name' => 'Future 50 Flat Off',
        'discount_type' => 'fixed',
        'discount_value' => 50.00,
        'starts_at' => Carbon::now()->addDays(3),
        'expires_at' => Carbon::now()->addDays(10),
        'is_active' => true,
    ]
);

// Test with API Controller directly
$controller = new \App\Http\Controllers\API\V1\CouponController();

function testApply($controller, $code, $subtotal) {
    $request = \Illuminate\Http\Request::create('/api/v1/coupons/apply', 'POST', [
        'code' => $code,
        'subtotal' => $subtotal,
    ]);
    $response = $controller->apply($request);
    $data = json_decode($response->getContent(), true);
    echo "Testing '{$code}' on subtotal ৳{$subtotal}:\n";
    echo "  Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
    echo "  Message: " . $data['message'] . "\n";
    if (!empty($data['data'])) {
        echo "  Discount: ৳" . $data['data']['discount_amount'] . " -> New Subtotal: ৳" . $data['data']['new_subtotal'] . "\n";
    }
    echo "\n";
}

testApply($controller, 'ACTIVE20', 1500); // Should succeed: 20% of 1500 = 300
testApply($controller, 'ACTIVE20', 500);  // Should fail: subtotal < minimum 1000
testApply($controller, 'EXPIRED10', 1500); // Should fail: expired
testApply($controller, 'FUTURE50', 1500);  // Should fail: not started yet

