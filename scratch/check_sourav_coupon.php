<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Coupon;
use Carbon\Carbon;

$nowBd = Carbon::now('Asia/Dhaka');
echo "Current Bangladesh Time: " . $nowBd->format('Y-m-d h:i:s A') . "\n";
echo "Config Timezone: " . config('app.timezone') . "\n\n";

$coupons = Coupon::all();
foreach ($coupons as $c) {
    echo "Coupon: [{$c->code}] - {$c->name}\n";
    echo "  Starts At: " . ($c->starts_at ? Carbon::parse($c->starts_at, 'Asia/Dhaka')->format('Y-m-d h:i:s A') : 'NULL') . "\n";
    echo "  Expires At: " . ($c->expires_at ? Carbon::parse($c->expires_at, 'Asia/Dhaka')->format('Y-m-d h:i:s A') : 'NULL') . "\n";
    echo "  Is Active: " . ($c->is_active ? 'YES' : 'NO') . "\n";
    echo "  Is Valid Now: " . ($c->isValid() ? 'YES' : 'NO') . "\n\n";
}

// Test SOURAV50 API application
$controller = new \App\Http\Controllers\API\V1\CouponController();
$request = \Illuminate\Http\Request::create('/api/v1/coupons/apply', 'POST', [
    'code' => 'SOURAV50',
    'subtotal' => 2400,
]);
$response = $controller->apply($request);
echo "API response for SOURAV50 on ৳2400:\n" . $response->getContent() . "\n";
