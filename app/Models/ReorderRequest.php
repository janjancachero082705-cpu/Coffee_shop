<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReorderRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'store_id',
        'approved_by',
        'delivery_receipt_id',
        'status',
        'is_read_by_admin',
        'is_read_at',
        'total_amount',
        'total_quantity',
        'requested_date',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'approved_at'    => 'datetime',
        'rejected_at'    => 'datetime',
        'total_amount'   => 'decimal:2',
    ];

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function items()
    {
        return $this->hasMany(ReorderRequestItem::class);
    }

    // Scopes
    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeForStore($q, $storeId)
    {
        return $q->where('store_id', $storeId);
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public static function generateNumber(): string
    {
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'REQ-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}