<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    protected $fillable = [
        'report_number','store_id','period_from','period_to',
        'total_sales','total_quantity','total_cost','gross_profit',
        'amount_due','amount_paid','balance','status','notes','user_id'
    ];
    protected $casts = [
        'period_from'=>'date','period_to'=>'date',
        'total_sales'=>'decimal:2','total_quantity'=>'decimal:2',
        'total_cost'=>'decimal:2','gross_profit'=>'decimal:2',
        'amount_due'=>'decimal:2','amount_paid'=>'decimal:2','balance'=>'decimal:2'
    ];

    public function store() { return $this->belongsTo(Store::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(SalesReportItem::class); }
    public function payments() { return $this->hasMany(ConsignmentPayment::class); }

    public function getStatusBadgeAttribute() {
        return match($this->status) {
            'paid','closed' => 'badge-success',
            'partial' => 'badge-warning',
            'submitted' => 'badge-friend',
            'draft' => 'badge-regular',
            default => 'badge-regular',
        };
    }
}