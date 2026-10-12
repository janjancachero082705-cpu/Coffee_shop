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

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
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
}