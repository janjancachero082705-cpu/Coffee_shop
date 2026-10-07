<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'dr_number', 'store_id', 'user_id', 'delivery_date', 'due_date',
        'total_amount', 'amount_paid', 'balance', 'status',
        'sales_report_id',
        'delivered_at',
        'confirmed_notes',
        'confirmed_at',
        'customer_confirmed',
        'out_for_delivery_by',
        'out_for_delivery_at', 'notes',
    ];

    protected $casts = [
        'delivery_date'        => 'date',
        'due_date'             => 'date',
        'total_amount'         => 'decimal:2',
        'amount_paid'          => 'decimal:2',
        'balance'              => 'decimal:2',
        'out_for_delivery_at'  => 'datetime',
        'confirmed_at'         => 'datetime',
        'delivered_at'         => 'datetime',
        'customer_confirmed'   => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryReceiptItem::class);
    }

    public function payments()
    {
        return $this->hasMany(ConsignmentPayment::class);
    }

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'DR-' . date('Y') . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    // ===== HELPER METHODS =====
    public function isOutForDelivery(): bool
    {
        return !is_null($this->out_for_delivery_at);
    }

    public function isConfirmed(): bool
    {
        return (bool) $this->customer_confirmed;
    }

    public function markOutForDelivery(): void
    {
        $this->update([
            'out_for_delivery_at' => now(),
            'out_for_delivery_by' => auth()->id(),
            'status' => 'out_for_delivery',
        ]);
    }

    public function confirmReceived(?string $notes = null): void
    {
        // Determine payment status based on balance
        $paymentStatus = 'pending';
        if ($this->balance <= 0.01) {
            $paymentStatus = 'paid';
        } elseif ($this->amount_paid > 0) {
            $paymentStatus = 'partial';
        }

        $this->update([
            'customer_confirmed' => true,
            'confirmed_at' => now(),
            'confirmed_notes' => $notes,
            'delivered_at' => now(),
            'status' => 'delivered',
        ]);
    }

    public function salesReport()
    {
        return $this->belongsTo(\App\Models\SalesReport::class, 'sales_report_id');
    }

    // ===== STATUS LABEL HELPER =====
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'partial' => 'Partial Payment',
            'paid' => 'Paid',
            default => ucfirst($this->status),
        };
    }
}
