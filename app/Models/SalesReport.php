<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number', 'store_id', 'user_id', 'period_from', 'period_to',
        'total_sales', 'total_quantity', 'amount_due', 'status', 'notes',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'total_sales' => 'decimal:2',
        'amount_due' => 'decimal:2',
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

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'SR-' . date('Y') . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}