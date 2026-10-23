<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Purchase extends Model {
    protected $fillable = ['supplier_name', 'purchase_date', 'variant_id', 'quantity', 'cost_per_unit', 'total_cost'];
    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}