<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    protected $fillable = [
        'dr_number','store_id','user_id','delivery_date',
        'subtotal','discount_amount','discount_type','discount_percent','discount_reason',
        'total_amount','amount_paid','amount_sold','balance',
        'status','is_consignment','customer_confirmed','confirmed_at','delivered_at','notes',
    ];
    protected $casts = [
        'delivery_date'=>'date','subtotal'=>'decimal:2',
        'discount_amount'=>'decimal:2','discount_percent'=>'decimal:2',
        'total_amount'=>'decimal:2','amount_paid'=>'decimal:2',
        'amount_sold'=>'decimal:2','balance'=>'decimal:2',
        'is_consignment'=>'boolean','customer_confirmed'=>'boolean',
        'confirmed_at'=>'datetime','delivered_at'=>'datetime',
    ];
    public function store() { return $this->belongsTo(Store::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(DeliveryReceiptItem::class); }
}