<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConsignmentPayment extends Model
{
    protected $fillable = [
        'payment_number','store_id','sales_report_id',
        'amount','method','reference_number','payment_date','notes','user_id'
    ];
    protected $casts = ['amount'=>'decimal:2','payment_date'=>'date'];
    public function store() { return $this->belongsTo(Store::class); }
    public function report() { return $this->belongsTo(SalesReport::class, 'sales_report_id'); }
    public function user() { return $this->belongsTo(User::class); }
}