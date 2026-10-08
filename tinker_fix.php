echo "=== BEFORE ===" . PHP_EOL;
$before = \App\Models\StoreNotification::where('title', 'LIKE', '%Order%')->get();
foreach ($before as $n) {
    echo "  [" . $n->id . "] " . substr($n->title, 0, 60) . PHP_EOL;
}

// Fix titles
$fixed = 0;
foreach ($before as $n) {
    $orig = $n->title;
    $clean = preg_replace('/Order Approved!\s*[^\x00-\x7F]+/', 'Order Approved!', $orig);
    $clean = preg_replace('/Order Rejected\s*[^\x00-\x7F]+/', 'Order Rejected', $clean);
    $clean = preg_replace('/Order Approved\s*[^\x00-\x7F]+/', 'Order Approved', $clean);
    $clean = preg_replace('/Order Cancelled\s*[^\x00-\x7F]+/', 'Order Cancelled', $clean);

    if ($clean !== $orig) {
        $n->title = trim($clean);
        $n->save();
        $fixed++;
    }
}

echo PHP_EOL . "Fixed: " . $fixed . PHP_EOL;
echo PHP_EOL . "=== AFTER ===" . PHP_EOL;
foreach (\App\Models\StoreNotification::where('title', 'LIKE', '%Order%')->get() as $n) {
    echo "  [" . $n->id . "] " . substr($n->title, 0, 60) . PHP_EOL;
}
