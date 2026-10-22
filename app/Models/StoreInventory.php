<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
    protected $fillable = [
        'store_id','product_id','product_variant_id','quantity',
        'total_delivered','total_sold','total_returned',
        'last_delivery_date','last_sale_date'
    ];
    protected $casts = [
        'quantity'=>'decimal:2','total_delivered'=>'decimal:2',
        'total_sold'=>'decimal:2','total_returned'=>'decimal:2',
        'last_delivery_date'=>'date','last_sale_date'=>'date'
    ];
    public function store() { return $this->belongsTo(Store::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }

    public function getItemNameAttribute() {
        return $this->variant ? $this->variant->display_name : ($this->product->name ?? '—');
    }
    public function getUnitAttribute() {
        return $this->variant ? ($this->product->unit_type ?? 'pcs') : ($this->product->unit_type ?? 'pcs');
    }
    public function getPriceAttribute() {
        return $this->variant ? $this->variant->price : ($this->product->price ?? 0);
    }
    public function getCostAttribute() {
        return $this->variant ? $this->variant->cost_price : ($this->product->cost_price ?? 0);
    }
    public function getStockValueAttribute() {
        return $this->quantity * $this->cost;
    }
}