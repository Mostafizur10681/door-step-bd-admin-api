<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$messages = \App\Models\ChatMessage::latest()->get();
echo "Total messages: " . $messages->count() . "\n";
foreach ($messages as $m) {
    echo "ID: {$m->id} | Session: '{$m->session_id}' | UserID: '{$m->user_id}' | Sender: '{$m->sender}' | Msg: {$m->message}\n";
}
