<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReportItem extends Model
{
    use HasFactory;

    protected $fillable = ['sales_report_id', 'product_id', 'quantity_sold', 'unit_price', 'subtotal'];
    protected $casts = ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function salesReport()
    {
        return $this->belongsTo(SalesReport::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}