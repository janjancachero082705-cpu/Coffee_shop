<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number', 'store_id', 'delivery_receipt_id', 'sales_report_id',
        'user_id', 'amount', 'method', 'reference_number', 'payment_date', 'notes',
        'is_read_by_admin',
        'is_read_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_read_by_admin' => 'boolean',
        'is_read_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function salesReport()
    {
        return $this->belongsTo(SalesReport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedByLabelAttribute(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name ?? 'Admin';
        }
        return 'Store (self-service)';
    }

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'PMT-' . date('Y') . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    // ===== METHOD ACCESSORS =====
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'Cash',
            'gcash' => 'GCash',
            'maya' => 'Maya',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'online' => 'Online',
            default => ucfirst($this->method ?? 'cash'),
        };
    }

    public function getMethodIconAttribute(): string
    {
        return match ($this->method) {
            'cash' => '💵',
            'gcash' => '📱',
            'maya' => '💜',
            'bank_transfer' => '🏦',
            'check' => '📝',
            'online' => '🌐',
            default => '💵',
        };
    }

    public function getMethodColorAttribute(): string
    {
        return match ($this->method) {
            'cash' => '#22c55e',
            'gcash' => '#3b82f6',
            'maya' => '#a855f7',
            'bank_transfer' => '#f59e0b',
            'check' => '#8a8378',
            'online' => '#c9a961',
            default => '#8a8378',
        };
    }

    public function getIsOnlineAttribute(): bool
    {
        return in_array($this->method, ['gcash', 'maya', 'bank_transfer', 'online']);
    }
}
