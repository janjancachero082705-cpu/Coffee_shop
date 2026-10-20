<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentPayment extends Model
{
    protected $fillable = [
        'store_id', 'delivery_receipt_id', 'sales_report_id', 'user_id',
        'payment_number', 'amount', 'method', 'reference_number',
        'proof_of_payment', 'payment_date', 'notes', 'is_read_by_admin',
        'is_read_at', 'verification_status', 'verified_by', 'verified_at',
        'rejection_reason'
    ];

    protected $casts = [
        'is_read_by_admin' => 'boolean',
        'is_read_at'       => 'datetime',
        'verified_at'      => 'datetime',
        'payment_date'     => 'date',
        'amount'           => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function salesReport()
    {
        return $this->belongsTo(SalesReport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeVerified($q)
    {
        return $q->where('verification_status', 'verified');
    }

    public function scopePending($q)
    {
        return $q->where('verification_status', 'pending');
    }

    public function scopeRejected($q)
    {
        return $q->where('verification_status', 'rejected');
    }
}