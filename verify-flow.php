<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LATEST STORE (auto-approve check) ===\n";
$s = \App\Models\Store::orderBy('id', 'desc')->first();
if ($s) {
    echo "ID:                   {$s->id}\n";
    echo "Name:                 {$s->store_name}\n";
    echo "registration_status:  " . ($s->registration_status ?? 'NULL') . "\n";
    echo "customer_type:        " . ($s->customer_type ?? 'NULL') . "\n";
    echo "default_discount:     " . ($s->default_discount_percent ?? 'NULL') . "\n";
    echo "is_read_by_admin:     " . ($s->is_read_by_admin ?? 'NULL') . "\n";
    echo "status:               " . ($s->status ?? 'NULL') . "\n";
} else {
    echo "WALAY STORE SA DB\n";
}

echo "\n=== LATEST DELIVERY RECEIPT (auto-confirm check) ===\n";
$dr = \App\Models\DeliveryReceipt::orderBy('id', 'desc')->first();
if ($dr) {
    echo "ID:                   {$dr->id}\n";
    echo "DR Number:            {$dr->dr_number}\n";
    echo "Status:               " . ($dr->status ?? 'NULL') . "\n";
    echo "customer_confirmed:   " . ($dr->customer_confirmed ?? 'NULL') . "\n";
    echo "confirmed_at:         " . ($dr->confirmed_at ?? 'NULL') . "\n";
    echo "confirmed_notes:      " . ($dr->confirmed_notes ?? 'NULL') . "\n";
    echo "delivered_at:         " . ($dr->delivered_at ?? 'NULL') . "\n";
    echo "total_amount:         " . ($dr->total_amount ?? 'NULL') . "\n";
    echo "Created at:           {$dr->created_at}\n";
} else {
    echo "WALAY DELIVERY RECEIPT SA DB\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total stores:            " . \App\Models\Store::count() . "\n";
echo "Total delivery receipts: " . \App\Models\DeliveryReceipt::count() . "\n";
