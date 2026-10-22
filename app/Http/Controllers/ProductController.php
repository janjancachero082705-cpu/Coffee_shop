<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'variants');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn($q) => $q->where('name', 'like', "%$search%")->orWhere('sku', 'like', "%$search%"));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
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
            'category_id' => 'nullable|exists:categories,id',
            'unit_type' => 'required|string',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.weight' => 'required|string',
            'variants.*.unit' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create([
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'unit_type' => $validated['unit_type'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active'],
                'is_featured' => $validated['is_featured'],
            ]);

            foreach ($request->variants as $variant) {
                $isNumeric = is_numeric($variant['weight']);
                $product->variants()->create([
                    'name' => $variant['weight'],
                    'weight' => $isNumeric ? $variant['weight'] : 0,
                    'unit' => $variant['unit'],
                    'price' => $variant['price'],
                    'cost_price' => $variant['cost_price'] ?? 0,
                    'stock' => $variant['stock'],
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Product and Variants created successfully!');
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
            'category_id' => 'nullable|exists:categories,id',
            'unit_type' => 'required|string',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.weight' => 'required|string',
            'variants.*.unit' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        DB::transaction(function () use ($validated, $request, $product) {
            $product->update([
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'unit_type' => $validated['unit_type'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active'],
                'is_featured' => $validated['is_featured'],
            ]);

            // Safest way: Delete existing variants and recreate
            $product->variants()->delete();

            foreach ($request->variants as $variant) {
                $isNumeric = is_numeric($variant['weight']);
                $product->variants()->create([
                    'name' => $variant['weight'],
                    'weight' => $isNumeric ? $variant['weight'] : 0,
                    'unit' => $variant['unit'],
                    'price' => $variant['price'],
                    'cost_price' => $variant['cost_price'] ?? 0,
                    'stock' => $variant['stock'],
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}