<?php
namespace App\Services;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Add stock (+). Logs movement.
     */
    public function addStock(Product $product, $quantity, $unitCost = null, $reference = null, $notes = null, $date = null)
    {
        return DB::transaction(function () use ($product, $quantity, $unitCost, $reference, $notes, $date) {
            $product->refresh();
            $newBalance = $product->stock + $quantity;

            $movement = InventoryMovement::create([
                'product_id'     => $product->id,
                'item_type'      => $product->is_raw_material ? 'cherry' : 'product',
                'type'           => 'in',
                'quantity'       => $quantity,
                'unit'           => $product->unit_type,
                'unit_cost'      => $unitCost ?? $product->cost_price,
                'total_cost'     => ($unitCost ?? $product->cost_price) * $quantity,
                'balance_after'  => $newBalance,
                'reference_type' => $reference['type'] ?? null,
                'reference_id'   => $reference['id'] ?? null,
                'notes'          => $notes,
                'user_id'        => auth()->id(),
                'movement_date'  => $date ?? now()->toDateString(),
            ]);

            $product->increment('stock', $quantity);

            return $movement;
        });
    }

    /**
     * Remove stock (-). Logs movement.
     */
    public function removeStock(Product $product, $quantity, $reference = null, $notes = null, $date = null)
    {
        return DB::transaction(function () use ($product, $quantity, $reference, $notes, $date) {
            $product->refresh();
            $newBalance = $product->stock - $quantity;

            $movement = InventoryMovement::create([
                'product_id'     => $product->id,
                'item_type'      => $product->is_raw_material ? 'cherry' : 'product',
                'type'           => 'out',
                'quantity'       => $quantity,
                'unit'           => $product->unit_type,
                'unit_cost'      => $product->cost_price,
                'total_cost'     => $product->cost_price * $quantity,
                'balance_after'  => $newBalance,
                'reference_type' => $reference['type'] ?? null,
                'reference_id'   => $reference['id'] ?? null,
                'notes'          => $notes,
                'user_id'        => auth()->id(),
                'movement_date'  => $date ?? now()->toDateString(),
            ]);

            $product->decrement('stock', $quantity);

            return $movement;
        });
    }

    /**
     * Manual adjustment to a specific quantity.
     */
    public function adjustStock(Product $product, $newQuantity, $notes = null, $date = null)
    {
        return DB::transaction(function () use ($product, $newQuantity, $notes, $date) {
            $product->refresh();
            $difference = $newQuantity - $product->stock;

            if (abs($difference) < 0.001) {
                return null; // No change
            }

            $movement = InventoryMovement::create([
                'product_id'     => $product->id,
                'item_type'      => $product->is_raw_material ? 'cherry' : 'product',
                'type'           => 'adjustment',
                'quantity'       => abs($difference),
                'unit'           => $product->unit_type,
                'unit_cost'      => $product->cost_price,
                'total_cost'     => $product->cost_price * abs($difference),
                'balance_after'  => $newQuantity,
                'reference_type' => 'adjustment',
                'reference_id'   => null,
                'notes'          => $notes ?? 'Manual adjustment: ' . ($difference > 0 ? '+' : '') . $difference,
                'user_id'        => auth()->id(),
                'movement_date'  => $date ?? now()->toDateString(),
            ]);

            $product->update(['stock' => $newQuantity]);

            return $movement;
        });
    }

    /**
     * Get cherry (raw material) product.
     */
    public function getCherryProduct()
    {
        return Product::where('is_raw_material', true)->first();
    }

    /**
     * Get summary of inventory.
     */
    public function getSummary()
    {
        $products = Product::where('is_raw_material', false)->orderBy('name')->get();
        $rawMaterials = Product::where('is_raw_material', true)->orderBy('name')->get();

        return [
            'products' => $products,
            'raw_materials' => $rawMaterials,
            'total_value' => $products->sum(fn($p) => $p->stock * $p->cost_price) 
                           + $rawMaterials->sum(fn($p) => $p->stock * $p->cost_price),
            'low_stock' => $products->filter(fn($p) => $p->stock <= $p->reorder_level && $p->stock > 0)->count(),
            'out_of_stock' => $products->filter(fn($p) => $p->stock <= 0)->count(),
        ];
    }
}