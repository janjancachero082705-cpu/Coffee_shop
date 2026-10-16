<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'data', 'read_at'];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'delivery.confirmed' => '✓',
            'delivery.created'   => '📦',
            'order.placed'       => '🛒',
            'payment.created'    => '💰',
            default              => '🔔',
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            'delivery.confirmed' => '#22c55e',
            'delivery.created'   => '#3b82f6',
            'order.placed'       => '#f59e0b',
            'payment.created'    => '#a855f7',
            default              => '#6b7280',
        };
    }

    /**
     * Get the store from data.store_id.
     */
    public function getStoreAttribute()
    {
        $storeId = $this->data['store_id'] ?? null;
        return $storeId ? Store::find($storeId) : null;
    }
}