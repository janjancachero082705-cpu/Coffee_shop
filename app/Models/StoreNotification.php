<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreNotification extends Model
{
    protected $fillable = ['store_id', 'type', 'title', 'message', 'data', 'read_at'];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'reorder_approved' => '✅',
            'reorder_rejected' => '❌',
            'delivery_out' => '🚚',
            'delivery_delivered' => '📦',
            'payment_recorded' => '💰',
            'payment_verified' => '✅',
            default => '🔔',
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            'reorder_approved' => '#22c55e',
            'reorder_rejected' => '#ef4444',
            'delivery_out' => '#3b82f6',
            'delivery_delivered' => '#22c55e',
            'payment_recorded' => '#c9a961',
            default => '#c9a961',
        };
    }
}