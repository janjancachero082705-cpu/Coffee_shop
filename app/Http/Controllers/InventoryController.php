<?php
namespace App\Http\Controllers;
use App\Models\ProductVariant;
use App\Models\InventoryMovement; // Assuming you have this, if not, we can skip
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with('product.category');

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

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'in_stock') { $query->where('stock', '>', 5); } 
            elseif ($status === 'low_stock') { $query->where('stock', '<=', 5)->where('stock', '>', 0); } 
            elseif ($status === 'out_of_stock') { $query->where('stock', 0); }
        }

        $variants = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_items' => ProductVariant::count(),
            'low_stock' => ProductVariant::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => ProductVariant::where('stock', 0)->count(),
            'total_value' => ProductVariant::sum(DB::raw('stock * price')),
        ];

        return view('inventory.index', compact('variants', 'stats'));
    }

    // BAG-ONG METHOD PARA MAGDAGDAG NG STOCK
    public function stockIn(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($request->variant_id);
            $variant->increment('stock', $request->quantity);
        });

        return redirect()->route('inventory.index')->with('success', 'Stock added successfully!');
    }
}