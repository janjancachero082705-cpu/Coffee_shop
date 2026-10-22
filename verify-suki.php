<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$s = \App\Models\Store::orderBy('id', 'desc')->first();
echo "=== LATEST STORE ===\n";
echo "ID:                   {$s->id}\n";
echo "Name:                 {$s->store_name}\n";
echo "registration_status:  " . ($s->registration_status ?? 'NULL') . "\n";
echo "is_read_by_admin:     " . var_export($s->is_read_by_admin, true) . "\n";
echo "customer_type:        " . ($s->customer_type ?? 'NULL') . "\n";
echo "default_discount:     " . ($s->default_discount_percent ?? 'NULL') . "\n";
echo "status:               " . ($s->status ?? 'NULL') . "\n";

echo "\n=== CHECKS ===\n";
echo ($s->registration_status === 'approved' ? '✅' : '❌') . " registration_status=approved\n";
echo ($s->is_read_by_admin == 1 ? '✅' : '❌') . " is_read_by_admin=1\n";
echo ($s->customer_type === 'suki' ? '✅' : '❌') . " customer_type=suki\n";
echo (floatval($s->default_discount_percent) == 10 ? '✅' : '❌') . " default_discount=10\n";
