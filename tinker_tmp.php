echo "Total: " . \App\Models\ReorderRequest::count() . PHP_EOL;
echo "Approved: " . \App\Models\ReorderRequest::where('status', 'approved')->count() . PHP_EOL;
echo "Pending: " . \App\Models\ReorderRequest::where('status', 'pending')->count() . PHP_EOL;

$page1 = \App\Models\ReorderRequest::with('store','items')->latest()->paginate(15);
echo "Paginated count: " . $page1->count() . PHP_EOL;
echo "Paginated total: " . $page1->total() . PHP_EOL;
echo "Current page: " . $page1->currentPage() . PHP_EOL;
echo "Last page: " . $page1->lastPage() . PHP_EOL;

foreach ($page1 as $r) {
    echo "  - {$r->request_number} | {$r->status} | {$r->store->store_name}\n";
}
