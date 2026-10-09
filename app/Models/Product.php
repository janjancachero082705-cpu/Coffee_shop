<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'category_id', 'name', 'description', 'image',
        'variety', 'origin', 'roast_level', 'process_method',
        'altitude', 'harvest_year', 'cupping_notes',
        'unit_type', 'base_unit', 'weight_grams',
        'price', 'wholesale_price', 'cost_price', 'cost_notes',
        'stock', 'reorder_level',
        'is_active', 'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'stock' => 'integer',
        'reorder_level' => 'integer',
        'weight_grams' => 'integer',
    ];

    protected $appends = ['image_url', 'profit_margin', 'stock_status'];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helpers
    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->reorder_level;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    // Accessors
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0 || $this->price <= 0) return 0;
        return round((($this->price - $this->cost_price) / $this->price) * 100, 1);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) return 'out';
        if ($this->isLowStock()) return 'low';
        return 'ok';
    }

    // Auto-generate SKU if empty
    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $lastId = (static::max('id') ?? 0) + 1;
                $product->sku = 'CB-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
            }
        });

        static::deleting(function ($product) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }

    // ============ PROFIT HELPERS ============

    public function getProfitPerUnitAttribute(): float
    {
        $cost = (float) ($this->cost_price ?? 0);
        $selling = (float) ($this->price ?? 0);
        return $selling - $cost;
    }

    public function getMarkupPercentAttribute(): float
    {
        $cost = (float) ($this->cost_price ?? 0);
        if ($cost <= 0) return 0;
        $profit = (float) ($this->price ?? 0) - $cost;
        return ($profit / $cost) * 100;
    }

    public function getTotalProfitPotentialAttribute(): float
    {
        $profit = (float) ($this->price ?? 0) - (float) ($this->cost_price ?? 0);
        return $profit * (int) ($this->stock ?? 0);
    }

    public function getStockValueAtCostAttribute(): float
    {
        return ((float) ($this->cost_price ?? 0)) * (int) ($this->stock ?? 0);
    }

    public function getStockValueAtSellingAttribute(): float
    {
        return ((float) ($this->price ?? 0)) * (int) ($this->stock ?? 0);
    }

    /**
     * Profit margin percentage (nullable kung walay cost).
     */
    public function getMarginAttribute(): ?float
    {
        $cost = (float) ($this->cost_price ?? 0);
        $price = (float) ($this->price ?? 0);
        if ($cost <= 0 || $price <= 0) return null;
        return round((($price - $cost) / $price) * 100, 1);
    }

    /**
     * Profit per unit (nullable kung walay cost).
     */
    public function getProfitAttribute(): ?float
    {
        $cost = (float) ($this->cost_price ?? 0);
        $price = (float) ($this->price ?? 0);
        if ($cost <= 0) return null;
        return round($price - $cost, 2);
    }
}
