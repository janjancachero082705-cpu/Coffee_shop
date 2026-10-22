<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MobileOrder extends Model
{
    protected $fillable = [
        'order_number', 'order_date', 'store_id',
        'items', 'subtotal', 'notes', 'status',
        'delivery_receipt_id', 'reviewed_at', 'reviewed_by', 'admin_notes',
    ];
    protected $casts = [
        'order_date' => 'date', 'items' => 'array',
        'subtotal' => 'decimal:2', 'reviewed_at' => 'datetime',
    ];
    public function store() { return $this->belongsTo(Store::class); }
    public function deliveryReceipt() { return $this->belongsTo(DeliveryReceipt::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}