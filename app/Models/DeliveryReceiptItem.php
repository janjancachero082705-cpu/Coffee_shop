<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_receipt_id', 'product_id', 'quantity_delivered',
        'quantity_sold', 'quantity_returned', 'unit_price', 'subtotal',
    ];

    protected $casts = ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}