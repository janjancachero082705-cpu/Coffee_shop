<?php
namespace App\Http\Controllers;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with('product.category');

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%$search%")
                         ->orWhere('sku', 'like', "%$search%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'in_stock') {
                $query->where('stock', '>', 5);
            } elseif ($status === 'low_stock') {
                $query->where('stock', '<=', 5)->where('stock', '>', 0);
            } elseif ($status === 'out_of_stock') {
                $query->where('stock', 0);
            }
        }

        $variants = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total_items' => ProductVariant::count(),
            'low_stock' => ProductVariant::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => ProductVariant::where('stock', 0)->count(),
            'total_value' => ProductVariant::sum(DB::raw('stock * price')),
        ];

        return view('inventory.index', compact('variants', 'stats'));
    }
}