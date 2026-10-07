<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Store extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'code',
        'store_name',
        'owner_name',
        'contact_number',
        'email',
        'is_read_by_admin',
        'is_read_at',
        'login_count',
        'logo',
        'password',
        'last_login_at',
        'portal_enabled',
        'address',
        'barangay',
        'city',
        'credit_limit',
        'payment_terms',
        'payment_day',
        'status',
        'notes',
        'registration_status',
        'registration_notes',
        'rejected_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'credit_limit'      => 'decimal:2',
        'portal_enabled'    => 'boolean',
        'last_login_at'     => 'datetime',
    ];

    // Relationships
    public function deliveryReceipts()
    {
        return $this->hasMany(DeliveryReceipt::class);
    }

    public function salesReports()
    {
        return $this->hasMany(SalesReport::class);
    }

    public function consignmentPayments()
    {
        return $this->hasMany(ConsignmentPayment::class);
    }

    public function returnOrders()
    {
        return $this->hasMany(ReturnOrder::class);
    }

    public function storeInventories()
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function reorderRequests()
    {
        return $this->hasMany(ReorderRequest::class);
    }

    // Helpers
    public function getTotalDeliveredAttribute(): float
    {
        return (float) $this->deliveryReceipts()->sum('total_amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->consignmentPayments()->sum('amount');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        if (str_starts_with($this->logo, "http")) return $this->logo;
        return asset("storage/" . $this->logo);
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->store_name ?? "ST", 0, 2));
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->salesReports()->sum('balance');
    }

    public function isPendingRegistration(): bool
    {
        return $this->registration_status === "pending";
    }

    public function isApprovedRegistration(): bool
    {
        return $this->registration_status === "approved";
    }

    public function isRejectedRegistration(): bool
    {
        return $this->registration_status === "rejected";
    }

    // ===== NAME ALIAS (maps ->name to store_name) =====
    public function getNameAttribute(): ?string
    {
        return $this->attributes['store_name'] ?? $this->attributes['name'] ?? null;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->store_name 
            ?? $this->name 
            ?? 'Store #' . $this->id;
    }

    public function notifications()
    {
        return $this->hasMany(StoreNotification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(StoreNotification::class)->whereNull('read_at')->latest();
    }
}
