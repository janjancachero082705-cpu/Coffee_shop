<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'variants');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn($q) => $q->where('name', 'like', "%$search%")->orWhere('sku', 'like', "%$search%"));
        }
        if ($request->filled('category')) { $query->where('category_id', $request->input('category')); }
        if ($request->filled('status')) {
            if ($request->status === 'low_stock') { $query->whereHas('variants', fn($q) => $q->where('stock', '<=', 5)->where('stock', '>', 0)); } 
            elseif ($request->status === 'inactive') { $query->where('is_active', false); } 
            elseif ($request->status === 'out_of_stock') { $query->whereHas('variants', fn($q) => $q->where('stock', 0)); }
        }
        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();
        $stats = [
            'total_products' => Product::count(),
            'low_stock' => ProductVariant::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => ProductVariant::where('stock', 0)->count(),
            'total_value' => ProductVariant::sum(DB::raw('stock * price')),
        ];
        return view('products.index', compact('products', 'categories', 'stats'));
    }

    public function show(Product $product)
    {
        $product->load(['variants', 'category']);
        return view('products.show', compact('product'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'unit_type' => 'required|string',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.weight' => 'required|string',
            'variants.*.unit' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create($validated);
            foreach ($request->variants as $index => $variant) {
                $isNumeric = is_numeric($variant['weight']);
                $variantImage = null;
                if ($request->hasFile("variants.$index.image")) {
                    $variantImage = $request->file("variants.$index.image")->store('variants', 'public');
                }

                $product->variants()->create([
                    'name' => $variant['weight'],
                    'weight' => $isNumeric ? $variant['weight'] : 0,
                    'unit' => $variant['unit'],
                    'price' => $variant['price'],
                    'cost_price' => $variant['cost_price'] ?? 0,
                    'stock' => 0,
                    'image' => $variantImage,
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $product->load('variants');
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'unit_type' => 'required|string',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.weight' => 'required|string',
            'variants.*.unit' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($product->image) { Storage::disk('public')->delete($product->image); }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        DB::transaction(function () use ($validated, $request, $product) {
            $product->update($validated);
            $existingVariants = $product->variants->keyBy('id');
            $processedIds = [];

            foreach ($request->variants as $index => $variantData) {
                $variantImage = null;
                if ($request->hasFile("variants.$index.image")) {
                    $variantImage = $request->file("variants.$index.image")->store('variants', 'public');
                }

                if (isset($variantData['id']) && $existingVariants->has($variantData['id'])) {
                    $variant = $existingVariants[$variantData['id']];
                    $isNumeric = is_numeric($variantData['weight']);
                    
                    $updateData = [
                        'name' => $variantData['weight'],
                        'weight' => $isNumeric ? $variantData['weight'] : 0,
                        'unit' => $variantData['unit'],
                        'price' => $variantData['price'],
                        'cost_price' => $variantData['cost_price'] ?? 0,
                    ];
                    
                    if ($variantImage) {
                        if ($variant->image) { Storage::disk('public')->delete($variant->image); }
                        $updateData['image'] = $variantImage;
                    }

                    $variant->update($updateData);
                    $processedIds[] = $variant->id;
                } else {
                    $isNumeric = is_numeric($variantData['weight']);
                    $newVariant = $product->variants()->create([
                        'name' => $variantData['weight'],
                        'weight' => $isNumeric ? $variantData['weight'] : 0,
                        'unit' => $variantData['unit'],
                        'price' => $variantData['price'],
                        'cost_price' => $variantData['cost_price'] ?? 0,
                        'stock' => 0,
                        'image' => $variantImage,
                    ]);
                    $processedIds[] = $newVariant->id;
                }
            }
            
            // Delete removed variants and their images
            $toDelete = $product->variants()->whereNotIn('id', $processedIds)->get();
            foreach ($toDelete as $delVar) {
                if ($delVar->image) { Storage::disk('public')->delete($delVar->image); }
                $delVar->delete();
            }
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) { Storage::disk('public')->delete($product->image); }
        foreach ($product->variants as $variant) {
            if ($variant->image) { Storage::disk('public')->delete($variant->image); }
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}