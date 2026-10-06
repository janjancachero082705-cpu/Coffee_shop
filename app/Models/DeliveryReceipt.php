<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'dr_number', 'store_id', 'user_id', 'delivery_date', 'due_date',
        'total_amount', 'amount_paid', 'balance', 'status', 'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
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
}