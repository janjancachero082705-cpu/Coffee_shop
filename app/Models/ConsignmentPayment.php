<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number', 'store_id', 'delivery_receipt_id', 'sales_report_id',
        'user_id', 'amount', 'method', 'reference_number', 'payment_date', 'notes',
        'is_read_by_admin', 'verification_status', 'verified_by', 'verified_at', 'rejection_reason',
        'is_read_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_read_by_admin', 'verification_status', 'verified_by', 'verified_at', 'rejection_reason' => 'boolean',
        'is_read_at' => 'datetime',
        'verified_at' => 'datetime',
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

    public function getCreatedByLabelAttribute(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name ?? 'Admin';
        }
        return 'Store (self-service)';
    }

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'PMT-' . date('Y') . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    // ===== METHOD ACCESSORS =====
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'Cash',
            'gcash' => 'GCash',
            'maya' => 'Maya',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'online' => 'Online',
            default => ucfirst($this->method ?? 'cash'),
        };
    }

    public function getMethodIconAttribute(): string
    {
        return match ($this->method) {
            'cash' => '💵',
            'gcash' => '📱',
            'maya' => '💜',
            'bank_transfer' => '🏦',
            'check' => '📝',
            'online' => '🌐',
            default => '💵',
        };
    }

    public function getMethodColorAttribute(): string
    {
        return match ($this->method) {
            'cash' => '#22c55e',
            'gcash' => '#3b82f6',
            'maya' => '#a855f7',
            'bank_transfer' => '#f59e0b',
            'check' => '#8a8378',
            'online' => '#c9a961',
            default => '#8a8378',
        };
    }

    public function getIsOnlineAttribute(): bool
    {
        return in_array($this->method, ['gcash', 'maya', 'bank_transfer', 'online']);
    }


    // ═══════════ VERIFICATION HELPERS ═══════════

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    public function verify(int $userId = null): void
    {
        $this->update([
            'verification_status' => 'verified',
            'verified_by' => $userId ?? auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(string $reason, int $userId = null): void
    {
        $this->update([
            'verification_status' => 'rejected',
            'verified_by' => $userId ?? auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    // Relationships
    public function verifier()
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($q) { return $q->where('verification_status', 'pending'); }
    public function scopeVerified($q) { return $q->where('verification_status', 'verified'); }
    public function scopeRejected($q) { return $q->where('verification_status', 'rejected'); }
}
