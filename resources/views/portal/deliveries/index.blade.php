@extends('portal.layouts.app')

@section('title', 'My Deliveries')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $baseQuery = \App\Models\DeliveryReceipt::where('store_id', $store->id);

    $total = (clone $baseQuery)->count();
    $pending = (clone $baseQuery)->where('status', 'pending')->count();
    $partial = (clone $baseQuery)->where('status', 'partial')->count();
    $paid = (clone $baseQuery)->where('status', 'paid')->count();
@endphp

{{-- HEADER --}}
<div class="pp-head">
    <div class="pp-head-left">
        <div class="pp-title">Deliveries</div>
        <div class="pp-sub">All deliveries to your store</div>
    </div>
</div>

{{-- STATS --}}
<div class="pp-stats">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $total }}</div>
        <div class="pp-stat-label">Total</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $pending }}</div>
        <div class="pp-stat-label">Pending</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $partial }}</div>
        <div class="pp-stat-label">Partial</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $paid }}</div>
        <div class="pp-stat-label">Paid</div>
    </div>
</div>

{{-- DELIVERIES LIST --}}
@if($deliveries->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="pp-empty-title">No deliveries yet</div>
        <div class="pp-empty-text">Wala pa'y deliveries sa imong store.</div>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($deliveries as $dr)
            @php
                $isConfirmed = $dr->customer_confirmed ?? false;
                $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                $badgeClass = $isConfirmed ? 'paid' : ($isOut ? 'partial' : $dr->status);
                $badgeText = $isConfirmed ? 'Confirmed' : ($isOut ? 'Out for Delivery' : ucfirst($dr->status));
            @endphp

                
                <a href="{{ route('portal.deliveries.show', $dr->id) }}" class="pp-card dv-card {{ $isConfirmed ? 'is-confirmed' : (($isOut && !$isConfirmed) ? 'has-action' : '') }}">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $dr->dr_number }}</div>
                        <div class="pp-card-sub">{{ $dr->items->count() }} item{{ $dr->items->count() != 1 ? 's' : '' }} · {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                    </div>
                    <span class="pp-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                </div>

                @if($isOut && !$isConfirmed)
                    <div class="pp-card-alert">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <rect x="1" y="3" width="15" height="13"/>
                            <path d="M16 8h4l3 3v5h-7V8z"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                        Out for Delivery — Tap to confirm
                    </div>
                @endif

                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount">&#8369;{{ number_format($dr->total_amount, 2) }}</div>
                        <div class="pp-card-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($dr->delivery_date)->diffForHumans() }}
                        </div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color: var(--t-text-3);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
            
        @endforeach
    </div>

    @if(method_exists($deliveries, 'links') && $deliveries->hasPages())
        <div style="margin-top: 20px;">{{ $deliveries->withQueryString()->links() }}</div>
    @endif
@endif


{{-- ═══ DELIVERIES — PRO THEME OVERRIDE ═══ --}}
@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DELIVERIES — Full theme-aware override
   Fixes hardcoded colors on all 4 themes
   ═══════════════════════════════════════════════════════════ */

/* ═══ HEADER ═══ */
.page-title,
.dv-title,
.dv-head-title {
    color: var(--t-text) !important;
}
.page-sub,
.dv-sub,
.dv-head-sub {
    color: var(--t-text-3) !important;
}
.back-btn {
    background: var(--t-input) !important;
    border: 1px solid var(--t-border) !important;
    color: var(--t-text-2) !important;
}
.back-btn:hover {
    background: var(--t-card) !important;
    border-color: rgba(var(--t-accent-rgb), 0.4) !important;
    color: var(--t-accent) !important;
}

/* ═══ INDEX CARDS ═══ */
.pp-card.dv-card,
.pp-card {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    color: var(--t-text) !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.pp-card.dv-card:hover,
.pp-card:hover {
    border-color: rgba(var(--t-accent-rgb), 0.4) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 28px -12px rgba(var(--t-accent-rgb), 0.3) !important;
}
.pp-card-title {
    color: var(--t-text) !important;
    font-family: ui-monospace, monospace !important;
}
.pp-card-sub,
.pp-card-meta {
    color: var(--t-text-3) !important;
}
.pp-card-amount {
    color: var(--t-accent) !important;
}

/* ═══ INDEX BADGES ═══ */
.pp-badge,
.pp-order-badge {
    font-weight: 800 !important;
    letter-spacing: 0.04em !important;
}
.pp-badge.pending,
.pp-badge.confirmed-partial {
    background: rgba(245, 158, 11, 0.14) !important;
    color: #f59e0b !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
}
.pp-badge.approved,
.pp-badge.paid,
.pp-badge.confirmed {
    background: rgba(34, 197, 94, 0.14) !important;
    color: #22c55e !important;
    border-color: rgba(34, 197, 94, 0.3) !important;
}
.pp-badge.rejected,
.pp-badge.cancelled {
    background: rgba(239, 68, 68, 0.14) !important;
    color: #ef4444 !important;
    border-color: rgba(239, 68, 68, 0.3) !important;
}
.pp-badge.partial,
.pp-badge.transit,
.pp-badge.out-for-delivery {
    background: rgba(59, 130, 246, 0.14) !important;
    color: #3b82f6 !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
}

/* ═══ INDEX ALERT (Out for delivery) ═══ */
.pp-card-alert {
    background: rgba(59, 130, 246, 0.1) !important;
    border: 1px solid rgba(59, 130, 246, 0.3) !important;
    color: #3b82f6 !important;
    border-radius: 10px !important;
    padding: 10px 12px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 12px !important;
}
.pp-card-alert svg { flex-shrink: 0 !important; }

/* ═══ SHOW HERO ═══ */
.dv-hero {
    border-radius: 18px !important;
    transition: all 0.2s !important;
}
.dv-hero.confirmed {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.04)) !important;
    border: 1px solid rgba(34, 197, 94, 0.35) !important;
}
.dv-hero.transit {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(59, 130, 246, 0.04)) !important;
    border: 1px solid rgba(59, 130, 246, 0.35) !important;
}
.dv-hero.pending {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.04)) !important;
    border: 1px solid rgba(245, 158, 11, 0.35) !important;
}
.dv-hero-icon {
    box-shadow: 0 8px 20px -8px currentColor !important;
}
.dv-hero-label { font-weight: 800 !important; }
.dv-hero-sub { color: var(--t-text-3) !important; }
.dv-hero-amount-label { color: var(--t-text-3) !important; }
.dv-hero-amount-value { color: var(--t-accent) !important; }

/* ═══ SHOW CARDS ═══ */
.card {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    color: var(--t-text) !important;
    transition: all 0.25s !important;
}
.card:hover {
    border-color: rgba(var(--t-accent-rgb), 0.28) !important;
}
.card-head { border-color: var(--t-border) !important; }
.card-head-title { color: var(--t-text) !important; }
.card-head-sub { color: var(--t-text-3) !important; }
.card-head-icon {
    background: rgba(var(--t-accent-rgb), 0.12) !important;
    border-color: rgba(var(--t-accent-rgb), 0.25) !important;
    color: var(--t-accent) !important;
}
.card-head-icon.green {
    background: rgba(34, 197, 94, 0.12) !important;
    border-color: rgba(34, 197, 94, 0.25) !important;
    color: #22c55e !important;
}

/* ═══ ITEMS LIST ═══ */
.item-row {
    border-color: var(--t-border) !important;
    transition: all 0.15s !important;
}
.item-row:hover {
    background: rgba(var(--t-accent-rgb), 0.04) !important;
    margin: 0 -12px !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
    border-radius: 10px !important;
}
.item-thumb {
    background: rgba(var(--t-accent-rgb), 0.1) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.2) !important;
    color: var(--t-accent) !important;
}
.item-name { color: var(--t-text) !important; }
.item-meta { color: var(--t-text-3) !important; }
.item-total { color: var(--t-accent) !important; }

/* ═══ TOTAL ROW ═══ */
.total-row {
    color: var(--t-text-2) !important;
    border-top: 2px solid rgba(var(--t-accent-rgb), 0.28) !important;
    padding-top: 14px !important;
}
.total-row strong {
    color: var(--t-accent) !important;
    font-size: 18px !important;
}

/* ═══ BALANCE LIST ═══ */
.balance-row {
    border-color: var(--t-border) !important;
    color: var(--t-text-2) !important;
}
.balance-label { color: var(--t-text-3) !important; }
.balance-value { color: var(--t-text) !important; }
.balance-value.accent { color: var(--t-accent) !important; }
.balance-value.green { color: #22c55e !important; }
.balance-total {
    border-top: 2px solid rgba(var(--t-accent-rgb), 0.28) !important;
    padding-top: 12px !important;
}

/* ═══ PAY NOW CTA (Show page) ═══ */
.dv-pay-cta {
    background:
        radial-gradient(circle at 100% 0%, rgba(var(--t-accent-rgb), 0.18) 0%, transparent 55%),
        linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.14), var(--t-card)) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.32) !important;
    border-radius: 18px !important;
}
.dv-pay-cta-head { color: var(--t-text-3) !important; }
.dv-pay-cta-amount { color: var(--t-accent) !important; }
.dv-pay-cta-btn {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.6) !important;
}
.dv-pay-cta-btn:hover {
    box-shadow: 0 14px 32px -8px rgba(var(--t-accent-rgb), 0.8) !important;
    transform: translateY(-1px) !important;
}

/* ═══ FULLY PAID BANNER ═══ */
.dv-paid-banner {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.03)) !important;
    border: 1px solid rgba(34, 197, 94, 0.35) !important;
    border-radius: 16px !important;
}
.dv-paid-banner-title { color: #22c55e !important; }
.dv-paid-banner-sub { color: var(--t-text-3) !important; }
.dv-paid-banner-icon { color: #22c55e !important; }

/* ═══ DV-ACTION (confirm/transit/pending) ═══ */
.dv-action {
    border-radius: 16px !important;
    transition: all 0.2s !important;
}
.dv-action.confirmed {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.03)) !important;
    border: 1px solid rgba(34, 197, 94, 0.28) !important;
}
.dv-action.confirmed .dv-action-title { color: #22c55e !important; }
.dv-action.transit {
    background: var(--t-card) !important;
    border: 1px solid rgba(59, 130, 246, 0.3) !important;
}
.dv-action.transit .dv-action-title { color: #3b82f6 !important; }
.dv-action.pending {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.03)) !important;
    border: 1px solid rgba(245, 158, 11, 0.28) !important;
}
.dv-action.pending .dv-action-title { color: #f59e0b !important; }
.dv-action-sub { color: var(--t-text-3) !important; }
.dv-action-icon { box-shadow: 0 6px 16px -6px currentColor !important; }

/* ═══ NOTES INPUT ═══ */
.dv-action-notes {
    background: var(--t-input) !important;
    border: 1px solid var(--t-border-2) !important;
    color: var(--t-text) !important;
}
.dv-action-notes::placeholder { color: var(--t-text-3) !important; }
.dv-action-notes:focus {
    border-color: var(--t-accent) !important;
    box-shadow: 0 0 0 4px rgba(var(--t-accent-rgb), 0.15) !important;
}

/* ═══ CONFIRM BUTTON ═══ */
.dv-action-btn {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.7) !important;
}
.dv-action-btn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 14px 28px -8px rgba(var(--t-accent-rgb), 0.9) !important;
}

/* ═══ FLASH/ALERT ═══ */
.p-alert,
.pay-flash,
.dv-flash {
    border-radius: 12px !important;
    padding: 12px 14px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 14px !important;
}
.p-alert-success,
.pay-flash {
    background: rgba(34, 197, 94, 0.1) !important;
    border: 1px solid rgba(34, 197, 94, 0.3) !important;
    color: #22c55e !important;
}
.p-alert-error {
    background: rgba(239, 68, 68, 0.1) !important;
    border: 1px solid rgba(239, 68, 68, 0.3) !important;
    color: #fca5a5 !important;
}

/* ═══ EMPTY STATE ═══ */
.pp-empty,
.dv-empty,
.p-empty {
    background: var(--t-card) !important;
    border: 1px dashed var(--t-border-2) !important;
    border-radius: 18px !important;
    text-align: center !important;
    padding: 44px 20px !important;
}
.pp-empty-icon,
.dv-empty-icon,
.p-empty-icon {
    background: rgba(var(--t-accent-rgb), 0.1) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.28) !important;
    color: var(--t-accent) !important;
}
.pp-empty-title,
.dv-empty-title,
.p-empty-title {
    color: var(--t-text) !important;
}
.pp-empty-text,
.dv-empty-text,
.p-empty-text {
    color: var(--t-text-3) !important;
}

/* ═══ LIGHT THEME — FORCE DARK TEXT ═══ */
html[data-theme="light"] .page-title,
html[data-theme="light"] .pp-card-title,
html[data-theme="light"] .card-head-title,
html[data-theme="light"] .item-name,
html[data-theme="light"] .dv-hero-amount-value,
html[data-theme="light"] .dv-pay-cta-amount,
html[data-theme="light"] .balance-value,
html[data-theme="light"] .pp-empty-title,
html[data-theme="blue"] .page-title,
html[data-theme="blue"] .pp-card-title,
html[data-theme="blue"] .card-head-title,
html[data-theme="blue"] .item-name,
html[data-theme="blue"] .dv-hero-amount-value,
html[data-theme="blue"] .dv-pay-cta-amount,
html[data-theme="blue"] .balance-value,
html[data-theme="blue"] .pp-empty-title {
    color: #0c1e3d !important;
}
html[data-theme="light"] .page-sub,
html[data-theme="light"] .pp-card-sub,
html[data-theme="light"] .pp-card-meta,
html[data-theme="light"] .card-head-sub,
html[data-theme="light"] .item-meta,
html[data-theme="light"] .dv-hero-sub,
html[data-theme="light"] .dv-hero-amount-label,
html[data-theme="light"] .balance-label,
html[data-theme="light"] .pp-empty-text,
html[data-theme="blue"] .page-sub,
html[data-theme="blue"] .pp-card-sub,
html[data-theme="blue"] .pp-card-meta,
html[data-theme="blue"] .card-head-sub,
html[data-theme="blue"] .item-meta,
html[data-theme="blue"] .dv-hero-sub,
html[data-theme="blue"] .dv-hero-amount-label,
html[data-theme="blue"] .balance-label,
html[data-theme="blue"] .pp-empty-text {
    color: #64748b !important;
}

/* ═══ DARK THEME — FORCE LIGHT TEXT ═══ */
html[data-theme="dark"] .page-title,
html[data-theme="dark"] .pp-card-title,
html[data-theme="dark"] .card-head-title,
html[data-theme="dark"] .item-name,
html[data-theme="dark"] .dv-hero-amount-value,
html[data-theme="dark"] .dv-pay-cta-amount,
html[data-theme="dark"] .balance-value,
html[data-theme="dark"] .pp-empty-title {
    color: #ffffff !important;
}

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .dv-hero { padding: 16px !important; gap: 12px !important; }
    .dv-hero-icon { width: 48px !important; height: 48px !important; }
    .dv-hero-amount-value { font-size: 18px !important; }
    .dv-pay-cta { padding: 16px !important; }
    .dv-pay-cta-amount { font-size: 30px !important; }
    .card { padding: 14px !important; }
    .item-thumb { width: 40px !important; height: 40px !important; }
}
</style>
@endpush
@endsection

@push('styles')
<style>
    /* Highlight card kung out for delivery */
    .pp-card.has-action {
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.1);
    }

    .pp-card-alert {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        margin: 0 -16px 10px;
        padding-left: 16px;
        padding-right: 16px;
        background: rgba(59, 130, 246, 0.1);
        border-top: 1px solid rgba(59, 130, 246, 0.15);
        border-bottom: 1px solid rgba(59, 130, 246, 0.15);
        color: #3b82f6;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .pp-card-alert svg {
        flex-shrink: 0;
        animation: truckMove 1.5s ease-in-out infinite;
    }

    @keyframes truckMove {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(3px); }
    }

    /* ════════════════════════════════════════════════════════
       DELIVERIES — THEME FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* ═══ SHOW PAGE — Hero ═══ */
    .dv-hero-sub { color: var(--t-text-3) !important; }
    .dv-hero-amount-label { color: var(--t-text-3) !important; }
    .dv-hero-amount-value { color: var(--t-accent) !important; }

    /* Cards */
    .card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
        color: var(--t-text) !important;
    }
    .card-head { border-color: var(--t-border) !important; }
    .card-head-title { color: var(--t-text) !important; }
    .card-head-sub { color: var(--t-text-3) !important; }
    .card-head-icon {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
        color: var(--t-accent) !important;
    }
    .card-head-icon.green {
        background: rgba(34, 197, 94, 0.15) !important;
        color: #22c55e !important;
    }

    /* Items */
    .item-row { border-color: var(--t-border) !important; }
    .item-thumb {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        color: rgba(var(--t-accent-rgb), 0.5) !important;
    }
    .item-name { color: var(--t-text) !important; }
    .item-meta { color: var(--t-text-3) !important; }
    .item-total { color: var(--t-accent) !important; }

    /* Total row */
    .total-row {
        color: var(--t-text-2) !important;
        border-top-color: rgba(var(--t-accent-rgb), 0.3) !important;
    }
    .total-row strong { color: var(--t-accent) !important; }

    /* Balance */
    .balance-row { border-top-color: var(--t-border) !important; }
    .balance-label { color: var(--t-text-2) !important; }
    .balance-value { color: var(--t-text) !important; }
    .balance-value.accent { color: var(--t-accent) !important; }
    .balance-total {
        border-top-color: rgba(var(--t-accent-rgb), 0.3) !important;
    }

    /* Action card */
    .dv-action-sub { color: var(--t-text-3) !important; }
    .dv-action-notes {
        background: var(--t-input) !important;
        border-color: var(--t-border-2) !important;
        color: var(--t-text) !important;
    }
    .dv-action-btn {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.7) !important;
    }

    /* Page header */
    .page-title { color: var(--t-text) !important; }
    .page-sub { color: var(--t-text-3) !important; }
    .back-btn {
        background: var(--t-input) !important;
        color: var(--t-text-2) !important;
    }
    .back-btn:hover {
        background: var(--t-card) !important;
        color: var(--t-text) !important;
    }

    /* ═══ INDEX PAGE — Cards ═══ */
    .pp-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
        color: var(--t-text) !important;
    }
    .pp-card-title { color: var(--t-text) !important; }
    .pp-card-sub { color: var(--t-text-3) !important; }
    .pp-card-amount { color: var(--t-accent) !important; }
    .pp-card-meta { color: var(--t-text-3) !important; }

    .pp-empty {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pp-empty-icon {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        color: var(--t-accent) !important;
    }
    .pp-empty-title { color: var(--t-text) !important; }
    .pp-empty-text { color: var(--t-text-3) !important; }

    /* ═══ CONFIRM MODAL ═══ */
    .cr-modal {
        background: var(--t-card-solid) !important;
        border-color: rgba(34, 197, 94, 0.3) !important;
    }
    .cr-modal-title { color: var(--t-text) !important; }
    .cr-modal-sub { color: var(--t-text-3) !important; }
    .cr-modal-sub strong { color: var(--t-accent) !important; }
    .cr-modal-body {
        background: var(--t-input) !important;
        border-color: var(--t-border) !important;
    }
    .cr-modal-row { border-top-color: var(--t-border) !important; }
    .cr-modal-row span { color: var(--t-text-3) !important; }
    .cr-modal-row strong { color: var(--t-text) !important; }
    .cr-modal-btn-cancel {
        background: var(--t-input) !important;
        color: var(--t-text-2) !important;
        border-color: var(--t-border-2) !important;
    }

    /* ═══ LIGHT/BLUE THEME BOOST ═══ */
    html[data-theme="light"] .card,
    html[data-theme="blue"] .card,
    html[data-theme="light"] .pp-card,
    html[data-theme="blue"] .pp-card,
    html[data-theme="light"] .cr-modal,
    html[data-theme="blue"] .cr-modal {
        background: #ffffff !important;
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .item-name,
    html[data-theme="blue"] .item-name,
    html[data-theme="light"] .card-head-title,
    html[data-theme="blue"] .card-head-title,
    html[data-theme="light"] .pp-card-title,
    html[data-theme="blue"] .pp-card-title,
    html[data-theme="light"] .cr-modal-title,
    html[data-theme="blue"] .cr-modal-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .item-meta,
    html[data-theme="blue"] .item-meta,
    html[data-theme="light"] .card-head-sub,
    html[data-theme="blue"] .card-head-sub,
    html[data-theme="light"] .pp-card-sub,
    html[data-theme="blue"] .pp-card-sub {
        color: #64748b !important;
    }
    html[data-theme="light"] .balance-label,
    html[data-theme="blue"] .balance-label,
    html[data-theme="light"] .total-row,
    html[data-theme="blue"] .total-row {
        color: #334155 !important;
    }
    html[data-theme="light"] .balance-value,
    html[data-theme="blue"] .balance-value {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .dv-action-notes,
    html[data-theme="blue"] .dv-action-notes,
    html[data-theme="light"] .cr-modal-body,
    html[data-theme="blue"] .cr-modal-body {
        background: #f8fafc !important;
        color: #0c1e3d !important;
        border-color: rgba(0, 0, 0, 0.08) !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — EMERALD GREEN FORCE OVERRIDE
       Bisag unsang gold hardcoded → emerald
       ════════════════════════════════════════════════════════ */

    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #059669 !important;
    }

    /* Force emerald sa tanan accent colors sa light theme */
    html[data-theme="light"] .p-main *[style*="color: #c9a961"] {
        color: #10b981 !important;
    }

    /* Kill any gold shadows */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.5) !important;
    }

    /* Override gold gradient backgrounds */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"] {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }

    /* Force all spans/divs inside cards dark */
    html[data-theme="light"] .p-main,
    html[data-theme="light"] .p-main *:not([class*="badge"]):not([class*="status"]):not([class*="pill"]):not([class*="text-"]) {
        /* Fallback */
    }

    /* Headings */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f1e17 !important;
    }

    /* All text classes */
    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f1e17 !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #6b7f75 !important;
    }

    /* Inline hardcoded white → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f1e17 !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — PURE SLATE FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Gold/green/brown hardcoded colors → slate */
    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="#10b981"],
    html[data-theme="light"] .p-main *[style*="#059669"],
    html[data-theme="light"] .p-main *[style*="#a9784a"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #475569 !important;
    }

    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(16, 185, 129"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"] {
        color: #475569 !important;
    }

    /* Gradient override */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #10b981"] {
        background: linear-gradient(135deg, #475569, #334155) !important;
    }

    /* Gold shadow → slate shadow */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(71, 85, 105, 0.4) !important;
    }

    /* All text — dark */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #64748b !important;
    }

    /* Hardcoded white text → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f172a !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       DARK MODE — PURE BLACK FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Kill all gold/emerald/purple accents sa dark mode */
    html[data-theme="dark"] .p-main *[style*="#c9a961"],
    html[data-theme="dark"] .p-main *[style*="#10b981"],
    html[data-theme="dark"] .p-main *[style*="#8b5cf6"],
    html[data-theme="dark"] .p-main *[style*="#a9784a"],
    html[data-theme="dark"] .p-main *[style*="#8a5f36"],
    html[data-theme="dark"] .p-main *[style*="#b8944d"],
    html[data-theme="dark"] .p-main *[style*="#ec4899"] {
        color: #ffffff !important;
    }

    /* Gradient → white */
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #10b981"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #8b5cf6"] {
        background: linear-gradient(135deg, #ffffff, #e5e5e5) !important;
    }

    /* All text light */
    html[data-theme="dark"] .p-main h1,
    html[data-theme="dark"] .p-main h2,
    html[data-theme="dark"] .p-main h3,
    html[data-theme="dark"] .p-main h4 {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="title"],
    html[data-theme="dark"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="name"],
    html[data-theme="dark"] .p-main strong,
    html[data-theme="dark"] .p-main b {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="dark"] .p-main [class*="meta"],
    html[data-theme="dark"] .p-main [class*="desc"],
    html[data-theme="dark"] .p-main [class*="hint"] {
        color: #a3a3a3 !important;
    }

    /* Any hardcoded dark text → white */
    html[data-theme="dark"] .p-main *[style*="color: #0f172a"],
    html[data-theme="dark"] .p-main *[style*="color:#0f172a"],
    html[data-theme="dark"] .p-main *[style*="color: #1a1a1f"],
    html[data-theme="dark"] .p-main *[style*="color: #000"],
    html[data-theme="dark"] .p-main *[style*="color:#000"],
    html[data-theme="dark"] .p-main *[style*="color: black"],
    html[data-theme="dark"] .p-main *[style*="color:black"] {
        color: #ffffff !important;
    }

    /* Any hardcoded light bg → dark */
    html[data-theme="dark"] .p-main *[style*="background: #ffffff"],
    html[data-theme="dark"] .p-main *[style*="background:#ffffff"],
    html[data-theme="dark"] .p-main *[style*="background: white"],
    html[data-theme="dark"] .p-main *[style*="background: #f8fafc"],
    html[data-theme="dark"] .p-main *[style*="background: #f1f5f9"] {
        background: #0a0a0a !important;
    }

</style>
@endpush
