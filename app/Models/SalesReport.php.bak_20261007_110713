<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'store_id',
        'user_id',
        'period_from',
        'period_to',
        'total_sales',
        'total_quantity',
        'amount_due',
        'amount_paid',
        'balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_from'  => 'date',
        'period_to'    => 'date',
        'total_sales'  => 'decimal:2',
        'amount_due'   => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'balance'      => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SalesReportItem::class);
    }

    public function recalculate(): void
    {
        $due  = (float) $this->total_sales;
        $paid = (float) $this->amount_paid;

        $this->amount_due = $due;
        $this->balance    = max(0, $due - $paid);

        if ($this->balance <= 0.01 && $due > 0) {
            $this->status = 'paid';
            $this->balance = 0;
        } elseif ($paid > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }

        $this->save();
    }

    public function getPaidPercentAttribute(): float
    {
        if ($this->total_sales <= 0) return 0;
        return min(100, round(($this->amount_paid / $this->total_sales) * 100, 1));
    }

    // Get all store payments (for display)
    public function getStorePaymentsAttribute()
    {
        return ConsignmentPayment::where('store_id', $this->store_id)
            ->orderBy('payment_date', 'desc')
            ->get();
    }
}