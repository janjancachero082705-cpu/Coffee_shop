<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Delivery extends Model {
    protected $fillable = ['dr_number', 'store_id', 'delivery_date', 'total_amount', 'status'];
    public function store() { return $this->belongsTo(Store::class); }
    public function items() { return $this->hasMany(DeliveryItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}
