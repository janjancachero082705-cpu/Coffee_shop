<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filters
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('sku', 'like', "%$s%")
                  ->orWhere('origin', 'like', "%$s%")
                  ->orWhere('variety', 'like', "%$s%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'reorder_level');
            } elseif ($request->status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'stock_asc': $query->orderBy('stock', 'asc'); break;
            case 'stock_desc': $query->orderBy('stock', 'desc'); break;
            default: $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'low' => Product::where('stock', '>', 0)->whereColumn('stock', '<=', 'reorder_level')->count(),
            'out' => Product::where('stock', '<=', 0)->count(),
            'total_value' => (float) Product::selectRaw('SUM(stock * price) as v')->value('v') ?? 0,
            'total_cost' => (float) Product::selectRaw('SUM(stock * cost_price) as v')->value('v') ?? 0,
        ];

        return view('products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', false);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request, $product->id);

        if ($request->input('remove_image') === '1') {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', false);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    // ============================================
    // HELPERS
    // ============================================
    private function validateData(Request $request, $exceptId = null): array
    {
        return $request->validate([
            'sku' => 'nullable|string|max:50|unique:products,sku' . ($exceptId ? ",$exceptId" : ''),
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',

            'variety' => 'nullable|string|max:100',
            'origin' => 'nullable|string|max:150',
            'roast_level' => 'nullable|in:Light,Medium,Medium-Dark,Dark,Extra Dark,Green',
            'process_method' => 'nullable|string|max:100',
            'altitude' => 'nullable|string|max:50',
            'harvest_year' => 'nullable|string|max:20',
            'cupping_notes' => 'nullable|string|max:500',

            'unit_type' => 'required|in:pack,kg,sako',
            'base_unit' => 'required|string|max:50',
            'weight_grams' => 'nullable|integer|min:0',

            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',

            'stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',

            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);
    }

    private function uploadImage($file): string
    {
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('products', $filename, 'public');
    }
}