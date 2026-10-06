<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'store_name', 'owner_name', 'contact_number', 'email',
        'address', 'barangay', 'city', 'credit_limit', 'payment_terms',
        'payment_day', 'status', 'notes',
    ];

    protected $casts = ['credit_limit' => 'decimal:2'];

    public function deliveryReceipts()
    {
        return $this->hasMany(DeliveryReceipt::class);
    }

    public function salesReports()
    {
        return $this->hasMany(SalesReport::class);
    }

    public function payments()
    {
        return $this->hasMany(ConsignmentPayment::class);
    }

    public function returnOrders()
    {
        return $this->hasMany(ReturnOrder::class);
    }

    public function inventories()
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function getTotalBalanceAttribute(): float
    {
        return (float) $this->deliveryReceipts()->sum('balance');
    }

    public static function generateCode(): string
    {
        $last = static::max('id') ?? 0;
        return 'STORE-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}