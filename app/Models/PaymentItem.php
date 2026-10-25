<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentItem extends Model {
    protected $fillable = ['payment_id', 'delivery_item_id', 'amount_paid'];
    public function payment() { return $this->belongsTo(Payment::class); }
    public function deliveryItem() { return $this->belongsTo(DeliveryItem::class); }
}