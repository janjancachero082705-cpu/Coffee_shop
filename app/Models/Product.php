<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model {
    use SoftDeletes;
    protected $fillable = ['sku', 'name', 'image', 'category_id', 'unit_type', 'description', 'is_active', 'is_featured'];
    public function category() { return $this->belongsTo(Category::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
}