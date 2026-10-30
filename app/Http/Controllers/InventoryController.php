<?php
namespace App\Http\Controllers;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // KINI ANG MGA CATEGORY NGA MAPUNTA SA "RAW MATERIALS"
        $rawCategories = ['Raw Cherries', 'Packaging', 'Farm Supplies', 'Raw Materials'];
        
        // Base Query para sa tanan variants
        $baseQuery = \App\Models\ProductVariant::with(['product.category']);

        // --- FILTERS (Search & Dates) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            })->orWhere('name', 'like', "%{$search}%");
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('updated_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('updated_at', '<=', $request->end_date);
        }

        // 1. TAB 1: RAW MATERIALS (Kadtong 4 ra ka category)
        $mainQuery = clone $baseQuery;
        $mainQuery->whereHas('product.category', function($q) use ($rawCategories) {
            $q->whereIn('name', $rawCategories);
        });

        // Filter by Status (In stock, Low, Out)
        if ($request->filled('status')) {
            if ($request->status === 'in_stock') $mainQuery->where('stock', '>', 5);
            if ($request->status === 'low_stock') $mainQuery->where('stock', '<=', 5)->where('stock', '>', 0);
            if ($request->status === 'out_of_stock') $mainQuery->where('stock', '<=', 0);
        }
        
        $mainInventory = $mainQuery->latest()->paginate(15, ['*'], 'main_page')->withQueryString();

        // 2. TAB 2: FINISHED GOODS (Tanan nga wala sa Raw Categories)
        $finishedQuery = clone $baseQuery;
        $finishedQuery->whereHas('product.category', function($q) use ($rawCategories) {
            $q->whereNotIn('name', $rawCategories);
        });

        if ($request->filled('status')) {
            if ($request->status === 'in_stock') $finishedQuery->where('stock', '>', 5);
            if ($request->status === 'low_stock') $finishedQuery->where('stock', '<=', 5)->where('stock', '>', 0);
            if ($request->status === 'out_of_stock') $finishedQuery->where('stock', '<=', 0);
        }

        $finishedGoods = $finishedQuery->latest()->paginate(15, ['*'], 'finished_page')->withQueryString();

        // 3. STATS (Para sa KPI Cards sa taas)
        $stats = [
            'total_items' => \App\Models\ProductVariant::count(),
            'low_stock' => \App\Models\ProductVariant::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => \App\Models\ProductVariant::where('stock', '<=', 0)->count(),
            'total_value' => \App\Models\ProductVariant::sum(\DB::raw('stock * cost_price')) ?? 0,
        ];

        // Listahan sa tanang variants para sa "Add Stock" Dialog
        $allVariantsList = \App\Models\ProductVariant::with('product')->get();

        return view('inventory.index', compact('mainInventory', 'finishedGoods', 'stats', 'allVariantsList'));
    }

    public function stockIn(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|numeric|min:0.01|max:999999'
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $variant = \App\Models\ProductVariant::lockForUpdate()->findOrFail($request->variant_id);
            
            // 1. I-increment ang stock
            $variant->increment('stock', $request->quantity);
            
            // 2. I-record sa Activity Log (Transactions)
            \App\Models\ActivityLog::record(
                'Inventory', 
                'Stock In', 
                'Added ' . $request->quantity . ' ' . $variant->unit . ' to ' . ($variant->product->name ?? 'Product'), 
                $variant, 
                $request->quantity
            );
            
            // 3. I-record sa Inventory Movements (History)
            \App\Models\InventoryMovement::create([
                'product_variant_id' => $variant->id,
                'type' => 'in',
                'quantity' => $request->quantity,
                'notes' => 'Manual Add Stock'
            ]);
        });

        return redirect()->route('inventory.index', ['tab' => 'main'])->with('success', 'Stock added successfully!');
    }

    public function movements(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\InventoryMovement::with(['variant.product'])->latest();

        if ($request->filled('search')) {
            $query->whereHas('variant.product', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        $movements = $query->paginate(20)->appends($request->query());

        return view('inventory.movements', compact('movements'));
    }
}