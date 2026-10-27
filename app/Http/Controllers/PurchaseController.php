<?php
namespace App\Http\Controllers;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('variant.product');

        // Filter by Supplier if a card is clicked
        if ($request->filled('supplier')) {
            $query->where('supplier_name', $request->input('supplier'));
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', "%$search%")
                  ->orWhereHas('variant.product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%$search%");
                  });
            });
        }

        // Product Filter
        if ($request->filled('product_id')) {
            $query->whereHas('variant', fn($vq) => $vq->where('product_id', $request->input('product_id')));
        }

        // Date Range Filter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->whereBetween('purchase_date', [$startDate, $endDate]);
        }

        $totalSpent = $query->sum('total_cost');
        $purchases = $query->latest('purchase_date')->paginate(10)->withQueryString();
        
        // Get Unique Suppliers for Cards
        $suppliers = Purchase::select(
            'supplier_name', 
            DB::raw('count(*) as total_orders'), 
            DB::raw('sum(total_cost) as total_spent')
        )
        ->groupBy('supplier_name')
        ->orderByDesc('total_spent')
        ->get();

        $purchasedItems = Product::whereHas('variants.purchases')->get();

        return view('purchases.index', compact('purchases', 'suppliers', 'totalSpent', 'startDate', 'endDate', 'purchasedItems'));
    }

    public function create(Request $request)
    {
        $existingItems = Product::all();
        $selectedSupplier = $request->input('supplier'); // Pre-select supplier
        return view('purchases.create', compact('existingItems', 'selectedSupplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01', 
            'unit' => 'required|string',
            'cost_per_unit' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $quantity = (float) $validated['quantity'];
            $total = $quantity * $validated['cost_per_unit'];

            $category = Category::firstOrCreate(['name' => 'Raw Materials'], ['emoji' => '📦']);
            
            $product = Product::firstOrCreate(
                ['name' => $validated['item_name']],
                [
                    'sku' => 'RAW-' . strtoupper(Str::random(5)),
                    'category_id' => $category->id,
                    'unit_type' => $validated['unit'],
                    'is_active' => true,
                ]
            );

            $variantName = 'Standard';
            if (stripos($validated['item_name'], 'cherr') === false) {
                $variantName = $validated['variant_name'] ?? 'Standard';
            }

            $variant = ProductVariant::firstOrCreate(
                ['product_id' => $product->id, 'name' => $variantName],
                [
                    'weight' => 0, 'unit' => $validated['unit'], 'price' => 0, 
                    'cost_price' => $validated['cost_per_unit'], 'stock' => 0,
                ]
            );

            Purchase::create([
                'supplier_name' => $validated['supplier_name'],
                'purchase_date' => $validated['purchase_date'],
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'cost_per_unit' => $validated['cost_per_unit'],
                'total_cost' => $total,
            ]);
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');

            $variant->increment('stock', $quantity);
        \Illuminate\Support\Facades\Cache::forget('low_stock_alerts');
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
            $variant->update(['cost_price' => $validated['cost_per_unit']]);
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase logged! Stock updated.');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
        return redirect()->route('purchases.index')->with('success', 'Purchase log deleted.');
    }
}