<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;
    protected $fillable = ['store_name', 'address', 'customer_type', 'discount'];

    public function deliveries() { return $this->hasMany(Delivery::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}
