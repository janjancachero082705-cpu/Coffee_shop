<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesReportItem extends Model
{
    protected $fillable = [
        'sales_report_id','product_id','product_variant_id',
        'quantity_sold','quantity_returned','unit_price','cost_price',
        'subtotal','cost_total','profit','unit'
    ];
    protected $casts = [
        'quantity_sold'=>'decimal:2','quantity_returned'=>'decimal:2',
        'unit_price'=>'decimal:2','cost_price'=>'decimal:2',
        'subtotal'=>'decimal:2','cost_total'=>'decimal:2','profit'=>'decimal:2'
    ];
    public function report() { return $this->belongsTo(SalesReport::class, 'sales_report_id'); }
    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function getItemNameAttribute() {
        return $this->variant ? $this->variant->display_name : ($this->product->name ?? '—');
    }
}