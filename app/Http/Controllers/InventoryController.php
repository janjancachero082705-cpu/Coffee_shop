<?php
namespace App\Http\Controllers;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = ProductVariant::with('product.category');

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $baseQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%$search%")
                         ->orWhere('sku', 'like', "%$search%");
                  });
            });
        }

        // Apply Status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'in_stock') { $baseQuery->where('stock', '>', 5); } 
            elseif ($status === 'low_stock') { $baseQuery->where('stock', '<=', 5)->where('stock', '>', 0); } 
            elseif ($status === 'out_of_stock') { $baseQuery->where('stock', 0); }
        }

        // 1. Tab 1: Raw Materials & Supplies (LAHAT maliban sa Finished Goods)
        $mainQuery = clone $baseQuery;
        $mainInventory = $mainQuery->whereDoesntHave('product.category', function($q) {
            $q->where('name', 'like', '%Finished%');
        })->latest()->paginate(15, ['*'], 'main_page')->withQueryString();

        // 2. Tab 2: Finished Goods (Powder, Roasted, etc.)
        $finishedQuery = clone $baseQuery;
        $finishedGoods = $finishedQuery->whereHas('product.category', function($q) {
            $q->where('name', 'like', '%Finished%');
        })->latest()->paginate(15, ['*'], 'finished_page')->withQueryString();

        // 3. Overall Stats
        $allVariants = ProductVariant::all();
        $stats = [
            'total_items' => $allVariants->count(),
            'low_stock' => $allVariants->where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => $allVariants->where('stock', 0)->count(),
            'total_value' => $allVariants->sum(fn($v) => $v->stock * $v->cost_price),
        ];

        $allVariantsList = ProductVariant::with('product')->get();

        return view('inventory.index', compact('mainInventory', 'finishedGoods', 'stats', 'allVariantsList'));
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|numeric|min:0.01'
        ]);

        DB::transaction(function () use ($request) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($request->variant_id);
            $variant->increment('stock', $request->quantity);
        });

        return redirect()->route('inventory.index', ['tab' => 'main'])->with('success', 'Stock added successfully!');
    }
}