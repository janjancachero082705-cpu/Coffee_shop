<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\StoreInventory;

class StoreInventoryService
{
    public function distribute(Store $store, Product $product, ?ProductVariant $variant, $qty, $date = null)
    {
        $variantId = $variant?->id;
        $inv = StoreInventory::firstOrCreate(
            ['store_id'=>$store->id,'product_id'=>$product->id,'product_variant_id'=>$variantId],
            ['quantity'=>0,'total_delivered'=>0,'total_sold'=>0,'total_returned'=>0]
        );
        $inv->increment('quantity', $qty);
        $inv->increment('total_delivered', $qty);
        $inv->update(['last_delivery_date' => $date ?? now()->toDateString()]);
        return $inv;
    }

    public function recordSale(Store $store, Product $product, ?ProductVariant $variant, $soldQty, $returnedQty = 0, $date = null)
    {
        $variantId = $variant?->id;
        $inv = StoreInventory::where('store_id',$store->id)
            ->where('product_id',$product->id)
            ->where('product_variant_id',$variantId)
            ->first();

        if (!$inv) return null;

        $inv->decrement('quantity', $soldQty + $returnedQty);
        $inv->increment('total_sold', $soldQty);
        $inv->increment('total_returned', $returnedQty);
        $inv->update(['last_sale_date' => $date ?? now()->toDateString()]);
        return $inv;
    }

    public function getStoreStock(Store $store)
    {
        return StoreInventory::with(['product','variant'])
            ->where('store_id', $store->id)
            ->where('quantity', '>', 0)
            ->get();
    }

    public function getStoreSummary(Store $store)
    {
        $items = StoreInventory::where('store_id', $store->id)->get();
        $stockValue = 0;
        foreach ($items as $i) $stockValue += $i->quantity * $i->cost;

        return [
            'items_on_hand' => $items->where('quantity','>',0)->count(),
            'total_units' => $items->sum('quantity'),
            'stock_value' => $stockValue,
            'total_delivered' => $items->sum('total_delivered'),
            'total_sold' => $items->sum('total_sold'),
        ];
    }
}