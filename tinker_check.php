echo "=== STATUS BREAKDOWN ===" . PHP_EOL;
\App\Models\ReorderRequest::select('status', \DB::raw('count(*) as c'))
    ->groupBy('status')
    ->get()
    ->each(function ($r) {
        echo '[' . $r->status . '] => ' . $r->c . PHP_EOL;
    });

echo PHP_EOL . "Total: " . \App\Models\ReorderRequest::count() . PHP_EOL;
echo "Pending (case-insensitive): " . \App\Models\ReorderRequest::whereRaw('LOWER(status) = ?', ['pending'])->count() . PHP_EOL;
