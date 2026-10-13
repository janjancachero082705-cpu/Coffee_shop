@extends('portal.layouts.app')

@section('title', 'My Account')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $totalOrders = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $totalDeliveries = \App\Models\DeliveryReceipt::where('store_id', $store->id)->count();
    $totalPaid = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $memberSince = $store->created_at;
    $initials = strtoupper(substr($store->store_name ?? 'ST', 0, 2));
    $isActive = ($store->status ?? 'inactive') === 'active';
    $creditLimit = (float)($store->credit_limit ?? 0);
    $creditUsed = 0; // i-adjust kung naa kay outstanding computation
@endphp

{{-- ═══ PROFILE HERO ═══ --}}
<div class="profile-hero">
    <div class="profile-hero-glow"></div>
    <div class="profile-hero-mesh"></div>
    <div class="profile-hero-rings">
        <span></span><span></span><span></span>
    </div>

    <div class="profile-avatar-wrap">
        <div class="profile-avatar">
            @if($store->logo_url)
                <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
            @else
                <span class="profile-avatar-initials">{{ $initials }}</span>
            @endif
        </div>
        @if($isActive)
            <div class="profile-status-dot" title="Active"></div>
        @endif
    </div>

    <div class="profile-name">{{ $store->store_name }}</div>
    <div class="profile-code">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h16"/></svg>
        {{ $store->code }}
    </div>

    <div class="profile-badges">
        <span class="p-badge p-badge-{{ $store->status }}">
            <span class="p-badge-dot"></span>
            {{ ucfirst($store->status) }}
        </span>
        @if($store->portal_enabled)
            <span class="p-badge p-badge-approved">
                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                Portal Active
            </span>
        @endif
        @if($store->payment_terms)
            <span class="p-badge p-badge-partial">
                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                {{ ucfirst(str_replace('_', ' ', $store->payment_terms)) }}
            </span>
        @endif
    </div>

    {{-- Quick Stats --}}
    <div class="profile-stats">
        <div class="profile-stat">
            <div class="profile-stat-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <div class="profile-stat-value">{{ $totalOrders }}</div>
            <div class="profile-stat-label">Orders</div>
        </div>
        <div class="profile-stat-divider"></div>
        <div class="profile-stat">
            <div class="profile-stat-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="profile-stat-value">{{ $totalDeliveries }}</div>
            <div class="profile-stat-label">Deliveries</div>
        </div>
        <div class="profile-stat-divider"></div>
        <div class="profile-stat">
            <div class="profile-stat-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
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

{{-- ═══ STORE INFORMATION ═══ --}}
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

{{-- ═══ ACCOUNT SUMMARY ═══ --}}
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
            <span class="summary-label">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>
                Credit Limit
            </span>
            <span class="summary-value">&#8369;{{ number_format($creditLimit, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                Payment Terms
            </span>
            <span class="summary-value">{{ ucfirst(str_replace('_', ' ', $store->payment_terms ?? 'flexible')) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
                Total Deliveries
            </span>
            <span class="summary-value">{{ $totalDeliveries }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Total Orders
            </span>
            <span class="summary-value">{{ $totalOrders }}</span>
        </div>
        <div class="summary-row summary-total">
            <span class="summary-label">Total Paid</span>
            <span class="summary-value green">&#8369;{{ number_format($totalPaid, 2) }}</span>
        </div>
    </div>
</div>

{{-- ═══ LOGOUT SECTION ═══ --}}
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

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PROFILE — PREMIUM COFFEE THEME
   ═══════════════════════════════════════════════════════════ */

/* ═══ PROFILE HERO ═══ */
.profile-hero {
    position: relative;
    padding: 28px 20px 22px;
    background: linear-gradient(135deg, #2D1A11 0%, #4E342E 60%, #3E2723 100%);
    border-radius: 24px;
    margin-bottom: 18px;
    overflow: hidden;
    box-shadow: 0 20px 48px -16px rgba(45, 26, 17, 0.5);
    text-align: center;
}
.profile-hero-glow {
    position: absolute;
    top: -80px; right: -80px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(216, 67, 21, 0.45), transparent 65%);
    pointer-events: none;
    animation: heroGlow 6s ease-in-out infinite;
}
.profile-hero-mesh {
    position: absolute;
    bottom: -100px; left: -100px;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.07), transparent 65%);
    pointer-events: none;
}
.profile-hero-rings {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
}
.profile-hero-rings span {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.06);
}
.profile-hero-rings span:nth-child(1) { width: 180px; height: 180px; top: -60px; left: -60px; }
.profile-hero-rings span:nth-child(2) { width: 240px; height: 240px; top: -90px; left: -90px; opacity: 0.5; }
.profile-hero-rings span:nth-child(3) { width: 300px; height: 300px; top: -120px; left: -120px; opacity: 0.3; }

@keyframes heroGlow {
    0%, 100% { opacity: 0.85; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
}

.profile-avatar-wrap {
    position: relative;
    display: inline-block;
    margin-bottom: 14px;
    z-index: 1;
}
.profile-avatar {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: linear-gradient(135deg, #D84315, #BF360C);
    color: #FFFFFF;
    display: grid;
    place-items: center;
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -0.02em;
    border: 3px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 16px 40px -12px rgba(216, 67, 21, 0.7), inset 0 2px 0 rgba(255, 255, 255, 0.2);
    overflow: hidden;
    position: relative;
}
.profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
.profile-avatar-initials {
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}
.profile-status-dot {
    position: absolute;
    bottom: 4px; right: 4px;
    width: 20px; height: 20px;
    border-radius: 50%;
    background: #22C55E;
    border: 3px solid #2D1A11;
    box-shadow: 0 0 12px rgba(34, 197, 94, 0.9);
    animation: statusPulse 2s ease-in-out infinite;
}
@keyframes statusPulse {
    0%, 100% { box-shadow: 0 0 12px rgba(34, 197, 94, 0.9); }
    50% { box-shadow: 0 0 20px rgba(34, 197, 94, 1); }
}

.profile-name {
    position: relative;
    z-index: 1;
    font-size: 24px;
    font-weight: 800;
    color: #FFFFFF;
    letter-spacing: -0.03em;
    line-height: 1.15;
    margin-bottom: 6px;
}
.profile-code {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.7);
    font-family: ui-monospace, monospace;
    letter-spacing: 0.08em;
    padding: 4px 10px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 100px;
    margin-bottom: 14px;
}

.profile-badges {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
    margin-bottom: 20px;
}
.p-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    border-radius: 100px;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    border: 1px solid;
    backdrop-filter: blur(8px);
}
.p-badge-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
    box-shadow: 0 0 6px currentColor;
}
.p-badge-active { background: rgba(34, 197, 94, 0.2); color: #86EFAC; border-color: rgba(34, 197, 94, 0.35); }
.p-badge-inactive { background: rgba(148, 163, 184, 0.2); color: #CBD5E1; border-color: rgba(148, 163, 184, 0.35); }
.p-badge-approved { background: rgba(96, 165, 250, 0.2); color: #93C5FD; border-color: rgba(96, 165, 250, 0.35); }
.p-badge-partial { background: rgba(251, 191, 36, 0.2); color: #FCD34D; border-color: rgba(251, 191, 36, 0.35); }

/* Quick Stats */
.profile-stats {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr auto 1fr auto 1fr;
    align-items: center;
    gap: 4px;
    padding: 14px 10px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    margin-bottom: 16px;
    backdrop-filter: blur(12px);
}
.profile-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 4px 6px;
}
.profile-stat-icon {
    width: 28px; height: 28px;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.12);
    color: #FFFFFF;
    display: grid;
    place-items: center;
    margin-bottom: 2px;
}
.profile-stat-value {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF;
    letter-spacing: -0.02em;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}
.profile-stat-label {
    font-size: 9.5px;
    color: rgba(255, 255, 255, 0.65);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 700;
}
.profile-stat-divider {
    width: 1px;
    height: 36px;
    background: rgba(255, 255, 255, 0.12);
}

/* Profile Actions */
.profile-actions {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.profile-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 46px;
    padding: 0 16px;
    border-radius: 13px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
}
.profile-btn:active { transform: scale(0.97); }
.profile-btn-primary {
    background: linear-gradient(135deg, #D84315, #BF360C);
    color: #FFFFFF;
    box-shadow: 0 10px 24px -8px rgba(216, 67, 21, 0.7);
}
.profile-btn-primary:hover {
    box-shadow: 0 12px 28px -8px rgba(216, 67, 21, 0.9);
    transform: translateY(-1px);
}
.profile-btn-ghost {
    background: rgba(255, 255, 255, 0.1);
    color: #FFFFFF;
    border-color: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
}
.profile-btn-ghost:hover {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.3);
}

/* ═══ CARD ═══ */
.p-card {
    background: #FFFFFF;
    border: 1.5px solid #EFEBE9;
    border-radius: 20px;
    padding: 18px;
    margin-bottom: 16px;
    box-shadow: 0 2px 10px rgba(45, 26, 17, 0.04);
    transition: all 0.25s;
}
.p-card:hover {
    border-color: #D7CCC8;
    box-shadow: 0 12px 28px -12px rgba(45, 26, 17, 0.12);
}
.p-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 14px;
    margin-bottom: 14px;
    border-bottom: 1.5px dashed #EFEBE9;
}
.p-card-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    background: #EFEBE9;
    color: #2D1A11;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    border: 1px solid #D7CCC8;
}
.p-card-icon.green {
    background: #DCFCE7;
    color: #15803D;
    border-color: #BBF7D0;
}
.p-card-title {
    font-size: 15px;
    font-weight: 800;
    color: #2D1A11;
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.p-card-sub {
    font-size: 11.5px;
    color: #8D6E63;
    font-weight: 600;
    margin-top: 2px;
}

/* ═══ INFO LIST ═══ */
.info-list { display: flex; flex-direction: column; gap: 4px; }
.info-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border-radius: 12px;
    transition: background 0.15s;
}
.info-row:hover { background: #FDFBF7; }
.info-icon {
    width: 32px; height: 32px;
    border-radius: 10px;
    background: #FDFBF7;
    border: 1px solid #EFEBE9;
    color: #8D6E63;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    margin-top: 1px;
}
.info-content { flex: 1; min-width: 0; }
.info-label {
    font-size: 10px;
    font-weight: 800;
    color: #8D6E63;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 4px;
}
.info-value {
    font-size: 13.5px;
    font-weight: 700;
    color: #2D1A11;
    line-height: 1.4;
    word-break: break-word;
}

/* ═══ SUMMARY LIST ═══ */
.summary-list { display: flex; flex-direction: column; }
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 11px 4px;
    border-bottom: 1px solid #F5F1EE;
}
.summary-row:last-child { border-bottom: none; }
.summary-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 12.5px;
    color: #8D6E63;
    font-weight: 600;
}
.summary-label svg { color: #B8A29A; flex-shrink: 0; }
.summary-value {
    font-size: 13.5px;
    font-weight: 800;
    color: #2D1A11;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.01em;
}
.summary-value.green { color: #15803D; }
.summary-total {
    margin-top: 6px;
    padding: 14px 12px !important;
    background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
    border: 1.5px solid #BBF7D0 !important;
    border-radius: 12px;
    border-bottom: none !important;
}
.summary-total .summary-label {
    color: #15803D;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.08em;
}
.summary-total .summary-value {
    font-size: 20px;
    color: #15803D;
    letter-spacing: -0.02em;
}

/* ═══ LOGOUT ═══ */
.logout-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: #FFFFFF;
    border: 1.5px solid #FEE2E2;
    border-radius: 20px;
    margin-bottom: 8px;
}
.logout-icon {
    width: 44px; height: 44px;
    border-radius: 13px;
    background: #FEE2E2;
    color: #B91C1C;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}
.logout-info { flex: 1; min-width: 0; }
.logout-title {
    font-size: 14px;
    font-weight: 800;
    color: #2D1A11;
    margin-bottom: 2px;
    letter-spacing: -0.01em;
}
.logout-text {
    font-size: 11.5px;
    color: #8D6E63;
    font-weight: 600;
}
.logout-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-height: 42px;
    padding: 0 16px;
    background: #FEE2E2;
    color: #B91C1C;
    border: 1.5px solid #FECACA;
    border-radius: 12px;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.logout-btn:hover {
    background: #FECACA;
    border-color: #FCA5A5;
}
.logout-btn:active { transform: scale(0.97); }

/* ═══ RESPONSIVE ═══ */
@media (max-width: 480px) {
    .profile-hero { padding: 24px 16px 20px; border-radius: 22px; }
    .profile-avatar { width: 78px; height: 78px; font-size: 26px; }
    .profile-name { font-size: 21px; }
    .profile-stat-value { font-size: 15px; }
    .profile-stat-icon { width: 24px; height: 24px; }
    .profile-stat-label { font-size: 9px; }
    .profile-stats { padding: 12px 6px; gap: 2px; }
    .profile-btn { min-height: 44px; font-size: 12.5px; }
    .p-card { padding: 16px; border-radius: 18px; }
    .p-card-icon { width: 36px; height: 36px; }
    .p-card-title { font-size: 14px; }
    .info-value { font-size: 13px; }
    .summary-value { font-size: 13px; }
    .summary-total .summary-value { font-size: 18px; }
    .logout-card { padding: 14px; gap: 10px; border-radius: 18px; }
    .logout-icon { width: 40px; height: 40px; }
    .logout-title { font-size: 13.5px; }
    .logout-text { font-size: 11px; }
    .logout-btn { padding: 0 14px; min-height: 40px; font-size: 12px; }
}
</style>
@endpush