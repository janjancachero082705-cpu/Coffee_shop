<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
    protected $fillable = ['store_id', 'delivery_id', 'amount', 'payment_date', 'reference_number', 'notes'];
    public function store() { return $this->belongsTo(Store::class); }
    public function delivery() { return $this->belongsTo(Delivery::class); }
    public function items() { return $this->hasMany(PaymentItem::class); }
}