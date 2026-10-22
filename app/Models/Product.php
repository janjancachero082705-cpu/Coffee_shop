<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['sku', 'name', 'category_id', 'unit_type', 'description', 'is_active', 'is_featured'];
    protected $casts = ['is_active' => 'boolean', 'is_featured' => 'boolean'];

    public function category() { return $this->belongsTo(Category::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function deliveryItems() { return $this->hasMany(DeliveryItem::class); }
}