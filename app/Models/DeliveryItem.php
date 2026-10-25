<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeliveryItem extends Model {
    protected $fillable = ['delivery_id', 'product_id', 'product_variant_id', 'quantity', 'weight', 'unit_price', 'subtotal'];
    public function delivery() { return $this->belongsTo(Delivery::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function paymentItems() { return $this->hasMany(PaymentItem::class); }
}