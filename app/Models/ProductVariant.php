<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductVariant extends Model {
    protected $fillable = ['product_id', 'name', 'weight', 'unit', 'price', 'cost_price', 'stock', 'image'];
    public function product() { return $this->belongsTo(Product::class); }
}