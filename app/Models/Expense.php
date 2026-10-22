<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category', 'description', 'amount',
        'quantity', 'unit', 'unit_cost',
        'expense_date', 'supplier_name', 'reference_number', 'notes', 'user_id',
    ];
    protected $casts = [
        'expense_date' => 'date', 'amount' => 'decimal:2',
        'quantity' => 'decimal:2', 'unit_cost' => 'decimal:2',
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'cherry_purchase' => 'Cherry Purchase',
            'labor' => 'Labor / Suhol',
            'processing' => 'Processing',
            'packaging' => 'Packaging',
            'utilities' => 'Utilities',
            'transportation' => 'Transportation',
            'harvesting' => 'Harvesting',
            'other' => 'Other',
            default => $this->category,
        };
    }
}