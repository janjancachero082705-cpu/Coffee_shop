<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['store_name', 'address', 'customer_type', 'discount'];

    public function deliveries() { return $this->hasMany(Delivery::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}