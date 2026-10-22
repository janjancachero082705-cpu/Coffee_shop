<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceiptItem extends Model
{
    protected $fillable = [
        'delivery_receipt_id','product_id','product_variant_id',
        'quantity_delivered','quantity_sold','quantity_returned',
        'unit','unit_price','cost_price','subtotal','item_discount','notes',
    ];
    protected $casts = [
        'quantity_delivered'=>'decimal:2','quantity_sold'=>'decimal:2',
        'quantity_returned'=>'decimal:2','unit_price'=>'decimal:2',
        'cost_price'=>'decimal:2','subtotal'=>'decimal:2','item_discount'=>'decimal:2',
    ];
    public function deliveryReceipt() { return $this->belongsTo(DeliveryReceipt::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }

    public function getItemNameAttribute() {
        return $this->variant ? $this->variant->display_name : ($this->product->name ?? '—');
    }
    public function getQuantityRemainingAttribute() {
        return $this->quantity_delivered - $this->quantity_sold - $this->quantity_returned;
    }
}