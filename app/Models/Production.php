<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Production extends Model {
    protected $fillable = ['production_date', 'raw_variant_id', 'finished_variant_id', 'raw_qty_used', 'finished_qty_produced'];
    public function rawVariant() { return $this->belongsTo(ProductVariant::class, 'raw_variant_id'); }
    public function finishedVariant() { return $this->belongsTo(ProductVariant::class, 'finished_variant_id'); }
}