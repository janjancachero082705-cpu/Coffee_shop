<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReorderRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reorder_request_id',
        'product_id',
        'quantity_requested',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function request()
    {
        return $this->belongsTo(ReorderRequest::class, 'reorder_request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}