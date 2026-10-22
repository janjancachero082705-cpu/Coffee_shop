<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$s = \App\Models\Store::find(10);

echo "=== STORE #10 ===\n";
echo "store_name    : " . ($s->store_name ?? 'NULL') . "\n";
echo "profile_image : " . ($s->profile_image ?? 'NULL') . "\n";
echo "logo          : " . ($s->logo ?? 'NULL') . "\n";
echo "avatar_url    : " . ($s->avatar_url ?? 'NULL') . "\n";
echo "logo_url      : " . ($s->logo_url ?? 'NULL') . "\n";
echo "initials      : " . $s->initials . "\n";

echo "\n=== FILE CHECKS ===\n";
echo "profile file exists: " . (\Storage::disk('public')->exists((string)$s->profile_image) ? 'YES' : 'NO') . "\n";
echo "logo file exists   : " . ($s->logo && \Storage::disk('public')->exists($s->logo) ? 'YES' : 'NO') . "\n";
echo "storage link       : " . (is_link(public_path('storage')) ? 'LINKED' : 'NOT LINKED') . "\n";

echo "\n=== ACTUAL DISK PATHS ===\n";
$profilePath = 'storage/app/public/' . ($s->profile_image ?? '');
echo "storage file: $profilePath\n";
echo "exists      : " . (file_exists($profilePath) ? 'YES' : 'NO') . "\n";
$publicPath = 'public/storage/' . ($s->profile_image ?? '');
echo "public file : $publicPath\n";
echo "exists      : " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";

echo "\n=== PROFILES FOLDER ===\n";
if (is_dir('storage/app/public/profiles')) {
    foreach (scandir('storage/app/public/profiles') as $f) {
        if ($f !== '.' && $f !== '..') {
            echo "  $f (" . filesize('storage/app/public/profiles/' . $f) . " bytes)\n";
        }
    }
}

echo "\n=== STORE-LOGOS FOLDER ===\n";
if (is_dir('storage/app/public/store-logos')) {
    foreach (scandir('storage/app/public/store-logos') as $f) {
        if ($f !== '.' && $f !== '..') {
            echo "  $f (" . filesize('storage/app/public/store-logos/' . $f) . " bytes)\n";
        }
    }
} else {
    echo "  (folder wala)\n";
}
