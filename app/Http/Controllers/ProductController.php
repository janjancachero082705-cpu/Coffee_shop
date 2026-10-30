<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Production;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Product::with(['category', 'variants'])->latest();

        // Filter by search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12);
        $categories = \App\Models\Category::all();

        // Pangitaon ang Cherry or Raw Material
        $cherryVariant = \App\Models\ProductVariant::whereHas('product', function($q) {
            $q->where('name', 'like', '%Cherry%');
        })->first();
        
        if (!$cherryVariant) {
            $cherryVariant = \App\Models\ProductVariant::whereHas('product.category', function($q) {
                $q->where('name', 'like', '%Raw%');
            })->first();
        }

        // KUHAON ANG TANANG FINISHED GOODS (Apil ang bag-o)
        $finishedGoods = \App\Models\Product::whereHas('category', function($q) {
            $q->where('name', 'like', '%Finished%');
        })->orWhereDoesntHave('category') // Fallback kung walay category
          ->latest()->get();

        $stats = [
            'total_products' => \App\Models\Product::count(),
            'total_items' => \App\Models\ProductVariant::count(),
            'low_stock' => \App\Models\ProductVariant::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => \App\Models\ProductVariant::where('stock', '<=', 0)->count(),
            'total_value' => \App\Models\ProductVariant::sum(\DB::raw('stock * cost_price')) ?? 0,
        ];

        // Production History
        $productions = \App\Models\Production::with(['finishedVariant.product'])->latest()->take(10)->get();
        
        // Tanang Variants para sa JS
        $allVariants = \App\Models\ProductVariant::with('product')->get();

        return view('products.index', compact('products', 'categories', 'cherryVariant', 'finishedGoods', 'stats', 'productions', 'allVariants'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $sku = $request->sku ?? 'PRD-' . strtoupper(\Illuminate\Support\Str::random(6));

            // I-save ang Product
            $product = \App\Models\Product::create([
                'name' => $request->name,
                'sku' => $sku,
                'category_id' => $request->category_id,
                'unit_type' => $request->unit_type ?? 'pc',
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'is_featured' => $request->has('is_featured') ? 1 : 0,
            ]);

            // I-save ang mga Variants ug Image
            foreach ($request->variants as $index => $variant) {
                $variantData = [
                    'name' => $variant['name'], // Gigamit na ang 'name'
                    'weight' => $variant['weight'] ?? 0, // Default weight
                    'unit' => $variant['unit'] ?? $product->unit_type,
                    'price' => $variant['price'],
                    'cost_price' => $variant['cost_price'] ?? 0,
                    'stock' => $variant['stock'] ?? 0,
                ];

                if ($request->hasFile("variants.$index.image")) {
                    $path = $request->file("variants.$index.image")->store('variants', 'public');
                    $variantData['image'] = $path;
                }

                $product->variants()->create($variantData);
            }

            // Log sa Transactions
            \App\Models\ActivityLog::record('Products', 'Created', 'Added new product: ' . $product->name, $product);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('products.index')->with('success', 'Product added successfully!');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Failed to add product: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load(['variants', 'category']);
        return view('products.show', compact('product'));
    }

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

        return back()->with('success', 'Stock added successfully!');
    }

    public function edit(Product $product)
    {
        $product->load('variants');
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Logging nga naa sa saktong pwesto
            \App\Models\ActivityLog::record('Products', 'Updated', 'Updated product: ' . $product->name, $product);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('products.index')->with('success', 'Product updated successfully!');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error updating product: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Gigamit nato ang find() imbes findOrFail() para dili mo-crash kung wala na ang product
            $product = \App\Models\Product::find($id);

            if ($product) {
                // Kung naa pa ang product, i-log ug i-delete
                \App\Models\ActivityLog::record('Products', 'Deleted', 'Deleted product: ' . $product->name);
                $product->variants()->delete();
                $product->delete();
            } else {
                // Kung wala na ang product (orphaned data), i-delete ang mga variants nga nagpabilin
                \App\Models\ProductVariant::where('product_id', $id)->delete();
                \App\Models\ActivityLog::record('Inventory', 'Deleted', 'Cleaned up orphaned inventory items for missing Product ID: ' . $id);
            }

            // I-redirect balik kung asa siya gikan
            if (str_contains(url()->previous(), 'inventory')) {
                return redirect()->route('inventory.index')->with('success', 'Item deleted successfully.');
            }
            
            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting: ' . $e->getMessage());
        }
    }public function storeProduction(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'production_date' => 'required|date',
            'raw_variant_id' => 'required|exists:product_variants,id',
            'raw_qty_used' => 'required|numeric|min:0.01',
            'products' => 'required|array|min:1',
            'products.*.product_name' => 'required|string',
            'products.*.variant_name' => 'required|string',
            'products.*.unit' => 'required|string',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Kuhaon ang Raw Material (Cherry/Raw)
            $rawVariant = \App\Models\ProductVariant::findOrFail($request->raw_variant_id);

            if ($rawVariant->stock < $request->raw_qty_used) {
                return back()->with('error', 'Dili igo ang stock sa ' . ($rawVariant->product->name ?? 'Raw Material') . '! Kulang og ' . ($request->raw_qty_used - $rawVariant->stock) . ' ' . $rawVariant->unit . '.')->withInput();
            }

            // 2. Deduct Raw Material
            $rawVariant->decrement('stock', $request->raw_qty_used);
            
            // Log Inventory Movement para sa Raw (Stock Out)
            \App\Models\InventoryMovement::create([
                'product_variant_id' => $rawVariant->id,
                'type' => 'out',
                'quantity' => $request->raw_qty_used,
                'notes' => 'Used for Production Batch'
            ]);

            // 3. Process Finished Goods (Existing/New Variant)
            foreach ($request->products as $output) {
                
                // Pangitaon ang Product base sa ngalan
                $product = \App\Models\Product::where('name', $output['product_name'])->first();
                
                if (!$product) {
                    throw new \Exception("Product '{$output['product_name']}' does not exist. Please add it in the Catalog first.");
                }

                // I-find o i-create ang bag-ong VARIANT sa maong produkto
                $variant = \App\Models\ProductVariant::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'name' => $output['variant_name']
                    ],
                    [
                        'unit' => $output['unit'],
                        'weight' => 0,
                        'price' => 0,
                        'cost_price' => 0,
                        'stock' => 0,
                    ]
                );

                // Idugang ang stock sa bag-ong variant
                $variant->increment('stock', $output['quantity']);

                // Log Inventory Movement para sa Finished Good (Stock In)
                \App\Models\InventoryMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'in',
                    'quantity' => $output['quantity'],
                    'notes' => 'Generated from Production'
                ]);
            }

            // Log sa System Transactions
            \App\Models\ActivityLog::record('Production', 'Created', 'Processed production batch using ' . $request->raw_qty_used . ' ' . $rawVariant->unit);

            \Illuminate\Support\Facades\DB::commit();
            
            // I-REDIRECT SA INVENTORY PARA MAKITA DAYON ANG RESULTA
            return redirect()->route('inventory.index', ['tab' => 'finished'])->with('success', 'Production successful! Stock updated.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Production Error: ' . $e->getMessage())->withInput();
        }
    }

    }