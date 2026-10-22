<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id','item_type','type','quantity','unit',
        'unit_cost','total_cost','balance_after',
        'reference_type','reference_id','notes','user_id','movement_date'
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'movement_date' => 'date',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getTypeLabelAttribute() {
        return match($this->type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            default => ucfirst($this->type),
        };
    }

    public function getTypeBadgeAttribute() {
        return match($this->type) {
            'in' => 'badge-success',
            'out' => 'badge-danger',
            'adjustment' => 'badge-warning',
            default => 'badge-regular',
        };
    }

    public function getReferenceLabelAttribute() {
        return match($this->reference_type) {
            'purchase' => '🍒 Purchase',
            'production' => '🏭 Production',
            'delivery' => '🚚 Delivery',
            'adjustment' => '⚙️ Manual Adjust',
            'initial' => '📋 Initial Stock',
            'return' => '↩ Return',
            default => $this->reference_type ?? '—',
        };
    }
}