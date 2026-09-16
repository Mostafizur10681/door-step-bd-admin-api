<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Review;

$count = 0;
Review::whereNotNull('user_id')->with('user')->get()->each(function($r) use (&$count) {
    if ($r->user && (!empty($r->user->name))) {
        $r->update(['author_name' => $r->user->name]);
        $count++;
    }
});

echo "Updated {$count} reviews with user names.\n";
