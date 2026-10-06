<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number', 'store_id', 'user_id', 'return_date',
        'total_value', 'reason', 'status', 'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_value' => 'decimal:2',
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
        return $this->hasMany(ReturnOrderItem::class);
    }

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'RT-' . date('Y') . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}