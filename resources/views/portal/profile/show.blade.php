@extends('portal.layouts.app')

@section('title', 'My Account')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $totalOrders = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $totalDeliveries = \App\Models\DeliveryReceipt::where('store_id', $store->id)->count();
    $totalPaid = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $memberSince = $store->created_at;
@endphp

{{-- ===== HERO PROFILE ===== --}}
<div class="profile-hero">
    <div class="profile-hero-glow"></div>

    <div class="profile-avatar-wrap">
        <div class="profile-avatar">
            @if($store->logo_url)
                <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
            @else
                {{ strtoupper(substr($store->store_name ?? 'ST', 0, 2)) }}
            @endif
        </div>
        @if($store->status === 'active')
            <div class="profile-status-dot"></div>
        @endif
    </div>

    <div class="profile-name">{{ $store->store_name }}</div>
    <div class="profile-code">{{ $store->code }}</div>

    <div class="profile-badges">
        <span class="p-badge p-badge-{{ $store->status }}">{{ ucfirst($store->status) }}</span>
        @if($store->portal_enabled)
            <span class="p-badge p-badge-approved">Portal Active</span>
        @endif
        @if($store->payment_terms)
            <span class="p-badge p-badge-partial">{{ ucfirst(str_replace('_', ' ', $store->payment_terms)) }}</span>
        @endif
    </div>

    {{-- Quick Stats --}}
    <div class="profile-stats">
        <div class="profile-stat">
            <div class="profile-stat-value">{{ $totalOrders }}</div>
            <div class="profile-stat-label">Orders</div>
        </div>
        <div class="profile-stat-divider"></div>
        <div class="profile-stat">
            <div class="profile-stat-value">{{ $totalDeliveries }}</div>
            <div class="profile-stat-label">Deliveries</div>
        </div>
        <div class="profile-stat-divider"></div>
        <div class="profile-stat">
            <div class="profile-stat-value">&#8369;{{ number_format($totalPaid / 1000, 1) }}k</div>
            <div class="profile-stat-label">Paid</div>
        </div>
    </div>

    <div class="profile-actions">
        <a href="{{ route('portal.profile.edit') }}" data-modal="edit-profile" class="profile-btn profile-btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Profile
        </a>
        <a href="{{ route('portal.profile.password') }}" data-modal="change-password" class="profile-btn profile-btn-ghost">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Password
        </a>
    </div>
</div>

{{-- ===== STORE INFORMATION ===== --}}
<div class="p-card">
    <div class="p-card-head">
        <div class="p-card-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
        </div>
        <div>
            <div class="p-card-title">Store Information</div>
            <div class="p-card-sub">Your registration details</div>
        </div>
    </div>

    <div class="info-list">
        <div class="info-row">
            <div class="info-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Owner Name</div>
                <div class="info-value">{{ $store->owner_name ?? '-' }}</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <path d="M22 6l-10 7L2 6"/>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $store->email ?? '-' }}</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Contact Number</div>
                <div class="info-value">{{ $store->contact_number ?? '-' }}</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Address</div>
                <div class="info-value">
                    {{ $store->address ?? '-' }}
                    @if($store->barangay), {{ $store->barangay }}@endif
                    @if($store->city), {{ $store->city }}@endif
                </div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Member Since</div>
                <div class="info-value">{{ $memberSince ? $memberSince->format('F d, Y') : '-' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== ACCOUNT SUMMARY ===== --}}
<div class="p-card">
    <div class="p-card-head">
        <div class="p-card-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div>
            <div class="p-card-title">Account Summary</div>
            <div class="p-card-sub">Financial overview</div>
        </div>
    </div>

    <div class="summary-list">
        <div class="summary-row">
            <span class="summary-label">Credit Limit</span>
            <span class="summary-value">&#8369;{{ number_format($store->credit_limit ?? 0, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Payment Terms</span>
            <span class="summary-value">{{ ucfirst(str_replace('_', ' ', $store->payment_terms ?? 'flexible')) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Deliveries</span>
            <span class="summary-value">{{ $totalDeliveries }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Orders</span>
            <span class="summary-value">{{ $totalOrders }}</span>
        </div>
        <div class="summary-row summary-total">
            <span class="summary-label">Total Paid</span>
            <span class="summary-value green">&#8369;{{ number_format($totalPaid, 2) }}</span>
        </div>
    </div>
</div>

{{-- ===== LOGOUT SECTION ===== --}}
<div class="logout-card">
    <div class="logout-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
        </svg>
    </div>
    <div class="logout-info">
        <div class="logout-title">Sign Out</div>
        <div class="logout-text">Log out from your store account</div>
    </div>
    <form method="POST" action="{{ route('portal.logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
            </svg>
            Logout
        </button>
    </form>
</div>

@endsection

