<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['return_order_id', 'product_id', 'quantity_returned', 'unit_price', 'subtotal'];
    protected $casts = ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function returnOrder()
    {
        return $this->belongsTo(ReturnOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}