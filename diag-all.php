<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TANANG STORES ===\n\n";
foreach (\App\Models\Store::all() as $s) {
    $exists = $s->profile_image ? (\Storage::disk('public')->exists($s->profile_image) ? 'YES' : 'NO') : 'NULL';
    echo "ID {$s->id} | {$s->store_name}\n";
    echo "  email         : " . ($s->email ?? 'NULL') . "\n";
    echo "  address       : " . (strlen($s->address ?? '') > 0 ? '✓' : '❌ EMPTY') . "\n";
    echo "  profile_image : " . ($s->profile_image ?? 'NULL') . "\n";
    echo "  file exists   : {$exists}\n";
    echo "  avatar_url    : " . ($s->avatar_url ?? 'NULL') . "\n\n";
}

echo "\n=== ADMIN USERS ===\n";
foreach (\App\Models\User::all() as $u) {
    echo "ID {$u->id} | {$u->name} ({$u->email})\n";
    echo "  store_id      : " . ($u->store_id ?? 'NULL') . "\n";
    echo "  profile_image : " . ($u->profile_image ?? 'NULL') . "\n";
    echo "  has column    : " . (\Schema::hasColumn('users', 'profile_image') ? 'YES' : 'NO') . "\n\n";
}

echo "\n=== USERS TABLE COLUMNS ===\n";
echo implode(', ', \Schema::getColumnListing('users')) . "\n";
