<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_variant_id', 'type', 'quantity', 'reference_type', 'reference_id', 'notes'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}