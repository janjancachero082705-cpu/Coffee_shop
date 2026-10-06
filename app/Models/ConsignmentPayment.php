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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
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

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'PMT-' . date('Y') . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}