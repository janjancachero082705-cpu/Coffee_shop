@extends('portal.layouts.app')

@section('title', 'My Account')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $totalOrders = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $totalDeliveries = \App\Models\DeliveryReceipt::where('store_id', $store->id)->count();
    $totalPaid = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $memberSince = $store->created_at;
    $creditLimit = (float)($store->credit_limit ?? 0);
@endphp

<div class="acct-user">
    <div class="acct-user-name">{{ $store->store_name }}</div>
    <a href="{{ route('portal.profile.edit') }}" class="acct-user-link">View profile</a>
</div>

<div class="acct-promo">
    <div class="acct-promo-content">
        <div class="acct-promo-title">Save on your future orders with <em>beanspro</em></div>
        <a href="{{ route('portal.payments.index') }}" class="acct-promo-link">
            Learn more
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>
    <div class="acct-promo-art">
        <div class="acct-promo-badge">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a4 4 0 014-4h8a4 4 0 014 4v2"/></svg>
            <span>Free</span>
        </div>
    </div>
</div>

<div class="acct-actions">
    <a href="{{ route('portal.orders.index') }}" class="acct-action">
        <div class="acct-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
        </div>
        <div class="acct-action-label">Orders</div>
    </a>
    <a href="{{ route('portal.deliveries.index') }}" class="acct-action">
        <div class="acct-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="acct-action-label">Deliveries</div>
    </a>
    <a href="{{ route('portal.payments.index') }}" class="acct-action">
        <div class="acct-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="acct-action-label">Payments</div>
    </a>
</div>

<div class="acct-section-title">Wallet</div>
<div class="acct-wallet">
    <div class="acct-wallet-logo">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
    </div>
    <div class="acct-wallet-info">
        <div class="acct-wallet-name">Store Credit</div>
        <div class="acct-wallet-sub">Available limit</div>
    </div>
    <div class="acct-wallet-amount">&#8369;{{ number_format($creditLimit, 2) }}</div>
</div>

<div class="acct-section-title">Account Summary</div>
<div class="acct-list">
    <div class="acct-info-row">
        <div class="acct-row-icon" style="color:#2563EB;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        </div>
        <div class="acct-info-content">
            <div class="acct-info-label">Total Orders</div>
            <div class="acct-info-value">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="acct-info-row">
        <div class="acct-row-icon" style="color:#10B981;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
        </div>
        <div class="acct-info-content">
            <div class="acct-info-label">Total Deliveries</div>
            <div class="acct-info-value">{{ $totalDeliveries }}</div>
        </div>
    </div>
    <div class="acct-info-row">
        <div class="acct-row-icon" style="color:#C2410C;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="acct-info-content">
            <div class="acct-info-label">Total Paid</div>
            <div class="acct-info-value acct-info-value-green">&#8369;{{ number_format($totalPaid, 2) }}</div>
        </div>
    </div>
    <div class="acct-info-row">
        <div class="acct-row-icon" style="color:#78716C;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        </div>
        <div class="acct-info-content">
            <div class="acct-info-label">Member Since</div>
            <div class="acct-info-value">{{ $memberSince ? $memberSince->format('M d, Y') : '-' }}</div>
        </div>
    </div>
</div>

<div class="acct-section-title">General</div>
<div class="acct-list">
    <a href="{{ route('portal.profile.edit') }}" class="acct-row">
        <div class="acct-row-icon" style="color:#6B7280;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </div>
        <div class="acct-row-label">Edit store profile</div>
        <svg class="acct-row-arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <a href="{{ route('portal.profile.password') }}" class="acct-row">
        <div class="acct-row-icon" style="color:#6B7280;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        </div>
        <div class="acct-row-label">Change password</div>
        <svg class="acct-row-arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="acct-row">
        <div class="acct-row-icon" style="color:#6B7280;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="acct-row-label">Terms &amp; privacy</div>
        <svg class="acct-row-arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
    </div>
</div>

<form method="POST" action="{{ route('portal.logout') }}" class="acct-logout-form">
    @csrf
    <button type="submit" class="acct-logout-btn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Log out
    </button>
</form>

@endsection

@push('styles')
<style>
/* ═══ COMPACT / MINIMIZED STYLES ═══ */
.acct-user{padding:0 4px 16px}
.acct-user-name{font-size:22px;font-weight:800;color:#1A1A1A;letter-spacing:-.03em;line-height:1.15;margin-bottom:2px}
.acct-user-link{font-size:12px;color:#1A1A1A;font-weight:600;text-decoration:none;transition:color .15s}
.acct-user-link:hover{color:#D84315}

.acct-promo{position:relative;display:flex;align-items:stretch;justify-content:space-between;background:linear-gradient(135deg,#7C2D12 0%,#9A3412 50%,#C2410C 100%);border-radius:12px;padding:14px 16px;margin-bottom:14px;overflow:hidden;min-height:95px;box-shadow:0 6px 18px -8px rgba(124,45,18,.4)}
.acct-promo::before{content:'';position:absolute;top:-40%;right:-20%;width:160px;height:160px;background:radial-gradient(circle,rgba(255,200,100,.35),transparent 70%);pointer-events:none}
.acct-promo-content{position:relative;z-index:1;display:flex;flex-direction:column;justify-content:space-between;flex:1;max-width:68%}
.acct-promo-title{font-size:12.5px;font-weight:800;color:#FFF;line-height:1.3;letter-spacing:-.01em;margin-bottom:8px}
.acct-promo-title em{font-style:italic;font-weight:800;color:#FCD34D}
.acct-promo-link{display:inline-flex;align-items:center;gap:3px;font-size:11.5px;font-weight:700;color:#FFF;text-decoration:none;width:fit-content}
.acct-promo-link:hover{opacity:.85}
.acct-promo-art{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;width:32%}
.acct-promo-badge{display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#FFF;color:#9A3412;border-radius:100px;font-size:11px;font-weight:800;box-shadow:0 4px 12px rgba(0,0,0,.2);transform:rotate(-8deg)}

.acct-actions{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px}
.acct-action{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:12px 8px;background:#FFF;border:1px solid #EEE;border-radius:12px;text-decoration:none;color:#1A1A1A;transition:all .15s}
.acct-action:hover{border-color:#D84315;transform:translateY(-1px);box-shadow:0 4px 12px -6px rgba(216,67,21,.15)}
.acct-action-icon{display:grid;place-items:center}
.acct-action-label{font-size:11.5px;font-weight:600;color:#1A1A1A}

.acct-section-title{font-size:14px;font-weight:800;color:#1A1A1A;letter-spacing:-.02em;margin:0 4px 8px;text-transform:uppercase}
.acct-section-title:first-of-type{margin-top:4px}

.acct-wallet{display:flex;align-items:center;gap:12px;padding:12px;background:#FFF;border:1px solid #EEE;border-radius:12px;margin-bottom:18px}
.acct-wallet-logo{width:36px;height:36px;border-radius:9px;background:#FFF3E0;color:#C2410C;display:grid;place-items:center;flex-shrink:0}
.acct-wallet-info{flex:1;min-width:0}
.acct-wallet-name{font-size:12.5px;font-weight:700;color:#1A1A1A}
.acct-wallet-sub{font-size:10.5px;color:#757575;font-weight:500;margin-top:1px}
.acct-wallet-amount{font-size:13px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums}

.acct-list{background:#FFF;border:1px solid #EEE;border-radius:12px;overflow:hidden;margin-bottom:18px}
.acct-row{display:flex;align-items:center;gap:12px;padding:12px 14px;text-decoration:none;color:inherit;background:#FFF;border-bottom:1px solid #F5F5F5;cursor:pointer;transition:background .15s;width:100%;text-align:left;font-family:inherit;border-left:none;border-right:none;border-top:none}
.acct-row:last-child{border-bottom:none}
.acct-row:hover{background:#FAFAFA}
.acct-row-icon{width:22px;height:22px;display:grid;place-items:center;flex-shrink:0}
.acct-row-label{flex:1;font-size:13px;font-weight:600;color:#1A1A1A}
.acct-row-arrow{color:#9E9E9E;flex-shrink:0}

.acct-info-row{display:flex;align-items:center;gap:12px;padding:12px 14px;border-bottom:1px solid #F5F5F5}
.acct-info-row:last-child{border-bottom:none}
.acct-info-content{flex:1;min-width:0}
.acct-info-label{font-size:11px;color:#757575;font-weight:500}
.acct-info-value{font-size:13px;font-weight:800;color:#1A1A1A;margin-top:1px;font-variant-numeric:tabular-nums}
.acct-info-value-green{color:#16A34A}

.acct-logout-form{margin-top:6px}
.acct-logout-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;min-height:44px;padding:0 16px;background:#FFF;border:1.5px solid #1A1A1A;border-radius:10px;color:#1A1A1A;font-family:inherit;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s}
.acct-logout-btn:hover{background:#1A1A1A;color:#FFF}
.acct-logout-btn:active{transform:scale(.98)}

/* ═══ COMPACT TOPBAR ═══ */
.p-topbar{padding-top:10px !important;padding-bottom:10px !important}
.p-topbar-store-name{font-size:16px !important}
.p-topbar-store-code{display:none !important}

@media(max-width:480px){
.acct-user-name{font-size:20px}
.acct-promo{min-height:90px;padding:12px 14px}
.acct-promo-title{font-size:12px}
.acct-action{padding:10px 6px}
.acct-section-title{font-size:13px}
.acct-row{padding:11px 12px}
.acct-row-label{font-size:12.5px}
.acct-info-value{font-size:12.5px}
.p-topbar-store-name{font-size:15px !important}
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var topbarInner = document.querySelector('.p-topbar-inner');
    if (topbarInner) {
        topbarInner.innerHTML = '<div class="p-topbar-store-text"><div class="p-topbar-store-name" style="font-size:16px;font-weight:800;">Account</div></div><a href="{{ route('portal.profile.edit') }}" class="acct-topbar-settings" aria-label="Settings"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg></a>';
    }
});
</script>
<style>
.acct-topbar-settings{display:grid;place-items:center;width:36px;height:36px;color:#1A1A1A;text-decoration:none;border-radius:50%;transition:background .15s;flex-shrink:0}
.acct-topbar-settings:hover{background:#F5F5F5}
.acct-topbar-settings svg{width:20px;height:20px;}
</style>
@endpush

