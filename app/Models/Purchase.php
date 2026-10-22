<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'purchase_number', 'purchase_date', 'supplier_name', 'item_type',
        'quantity', 'unit', 'unit_price', 'total_amount',
        'payment_status', 'amount_paid', 'notes', 'user_id',
    ];
    protected $casts = [
        'purchase_date' => 'date', 'quantity' => 'decimal:2', 'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2', 'amount_paid' => 'decimal:2',
    ];
    public function user() { return $this->belongsTo(User::class); }
}