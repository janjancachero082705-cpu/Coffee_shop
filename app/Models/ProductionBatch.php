<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    protected $fillable = [
        'batch_number', 'production_date',
        'cherries_input_kg', 'cherries_cost_per_kg', 'cherries_total_cost',
        'labor_cost', 'processing_cost', 'other_cost', 'total_cost',
        'product_id', 'output_quantity', 'output_unit',
        'cost_per_output_unit', 'yield_percentage', 'notes', 'user_id',
    ];
    protected $casts = [
        'production_date' => 'date',
        'cherries_input_kg' => 'decimal:2', 'cherries_cost_per_kg' => 'decimal:2',
        'cherries_total_cost' => 'decimal:2', 'labor_cost' => 'decimal:2',
        'processing_cost' => 'decimal:2', 'other_cost' => 'decimal:2',
        'total_cost' => 'decimal:2', 'output_quantity' => 'decimal:2',
        'cost_per_output_unit' => 'decimal:2', 'yield_percentage' => 'decimal:2',
    ];
    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
}