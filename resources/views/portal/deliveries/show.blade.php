@extends('portal.layouts.app')

@section('title', $delivery->dr_number)

@section('content')

@php
    $isConfirmed = $delivery->customer_confirmed ?? false;
    $isOut = !$isConfirmed && $delivery->out_for_delivery_at;
    $remaining = max(0, (float) $delivery->total_amount - (float) $delivery->amount_paid);
    $isPaid = $remaining <= 0;
@endphp

{{-- ═══ HEADER ═══ --}}
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.deliveries.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="page-title">{{ $delivery->dr_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

{{-- ═══ STATUS HERO ═══ --}}
<div class="dv-hero {{ $isConfirmed ? 'confirmed' : ($isOut ? 'transit' : 'pending') }}">
    <div class="dv-hero-icon">
        @if($isConfirmed)
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        @elseif($isOut)
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        @else
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        @endif
    </div>
    <div class="dv-hero-text">
        <div class="dv-hero-label">
            @if($isConfirmed) Customer Confirmed
            @elseif($isOut) Out for Delivery
            @else Pending
            @endif
        </div>
        <div class="dv-hero-sub">
            @if($isConfirmed)
                Confirmed {{ \Carbon\Carbon::parse($delivery->confirmed_at)->diffForHumans() }}
            @elseif($isOut)
                Marked {{ \Carbon\Carbon::parse($delivery->out_for_delivery_at)->diffForHumans() }}
            @else
                Waiting for admin to ship
            @endif
        </div>
    </div>
    <div class="dv-hero-amount">
        <div class="dv-hero-amount-label">Total</div>
        <div class="dv-hero-amount-value">&#8369;{{ number_format($delivery->total_amount, 0) }}</div>
    </div>
</div>

{{-- ═══ PAY NOW CTA — separate section (if confirmed & has balance) ═══ --}}
@if($isConfirmed && !$isPaid)
    <div class="dv-pay-cta">
        <div class="dv-pay-cta-head">
            <span class="dv-pay-cta-dot"></span>
            Outstanding Balance
        </div>
        <div class="dv-pay-cta-amount">&#8369;{{ number_format($remaining, 2) }}</div>
        <a href="{{ route('portal.payments.create', ['dr' => $delivery->id]) }}" class="dv-pay-cta-btn">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Pay Now
        </a>
    </div>
@endif

{{-- ═══ FULLY PAID BADGE (if confirmed & paid) ═══ --}}
@if($isConfirmed && $isPaid)
    <div class="dv-paid-banner">
        <div class="dv-paid-banner-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="dv-paid-banner-text">
            <div class="dv-paid-banner-title">Fully Paid</div>
            <div class="dv-paid-banner-sub">Walay remaining balance</div>
        </div>
    </div>
@endif

{{-- ═══ ITEMS CARD ═══ --}}
<div class="card">
    <div class="card-head">
        <div class="card-head-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div>
            <div class="card-head-title">Items</div>
            <div class="card-head-sub">{{ $delivery->items->count() }} product(s)</div>
        </div>
    </div>
    <div class="items-list">
        @foreach($delivery->items as $item)
            <div class="item-row">
                <div class="item-thumb">
                    @if($item->product && $item->product->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                    @else
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>
                <div class="item-info">
                    <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                    <div class="item-meta">{{ $item->quantity_delivered }} × &#8369;{{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="item-total">&#8369;{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
    </div>
    <div class="total-row">
        <span>Total</span>
        <strong>&#8369;{{ number_format($delivery->total_amount, 2) }}</strong>
    </div>
</div>

{{-- ═══ PAYMENT CARD ═══ --}}
<div class="card">
    <div class="card-head">
        <div class="card-head-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div>
            <div class="card-head-title">Payment</div>
        </div>
    </div>
    <div class="balance-list">
        <div class="balance-row">
            <span class="balance-label">Total</span>
            <span class="balance-value">&#8369;{{ number_format($delivery->total_amount, 2) }}</span>
        </div>
        <div class="balance-row">
            <span class="balance-label">Paid</span>
            <span class="balance-value green">- &#8369;{{ number_format($delivery->amount_paid, 2) }}</span>
        </div>
        <div class="balance-row balance-total">
            <span class="balance-label">Balance</span>
            <span class="balance-value {{ $delivery->balance > 0 ? 'accent' : 'green' }}">&#8369;{{ number_format($delivery->balance, 2) }}</span>
        </div>
    </div>
</div>

{{-- ═══ CONFIRMED STATUS (minimal, walay Pay Now) ═══ --}}
@if($isConfirmed)
    <div class="dv-action confirmed">
        <div class="dv-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="dv-action-text">
            <div class="dv-action-title">Delivery Confirmed</div>
            <div class="dv-action-sub">Confirmed {{ \Carbon\Carbon::parse($delivery->confirmed_at)->diffForHumans() }}</div>
        </div>
    </div>
@elseif($isOut)
    <div class="dv-action transit">
        <div class="dv-action-head">
            <div class="dv-action-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="dv-action-text">
                <div class="dv-action-title">Out for Delivery</div>
                <div class="dv-action-sub">Kung nadawat na, i-confirm</div>
            </div>
        </div>
        <form method="POST" action="{{ route('portal.deliveries.confirm', $delivery->id) }}" id="confirmForm" class="dv-action-form">
            @csrf
            <textarea name="notes" class="dv-action-notes" rows="2" placeholder="Optional notes..."></textarea>
            <button type="button" class="dv-action-btn" id="openConfirmModal">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                Confirm Received
            </button>
        </form>
    </div>
@else
    <div class="dv-action pending">
        <div class="dv-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="dv-action-text">
            <div class="dv-action-title">Waiting for Delivery</div>
            <div class="dv-action-sub">Wala pa gi-ship sa admin</div>
        </div>
    </div>
@endif

{{-- ═══ CONFIRM MODAL ═══ --}}
<div id="confirmReceivedModal" class="cr-modal-overlay" style="display:none;" aria-hidden="true">
    <div class="cr-modal" role="dialog" aria-modal="true">
        <div class="cr-modal-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="cr-modal-title">Confirm Received?</div>
        <div class="cr-modal-sub">DR-<strong>{{ $delivery->dr_number }}</strong></div>
        <div class="cr-modal-body">
            <div class="cr-modal-row">
                <span>Items</span>
                <strong>{{ $delivery->items->count() }} product(s)</strong>
            </div>
            <div class="cr-modal-row">
                <span>Total</span>
                <strong style="color:var(--t-accent);">&#8369;{{ number_format($delivery->total_amount, 2) }}</strong>
            </div>
            @if($delivery->balance > 0)
                <div class="cr-modal-row">
                    <span>Balance</span>
                    <strong style="color:#f59e0b;">&#8369;{{ number_format($delivery->balance, 2) }}</strong>
                </div>
            @endif
        </div>
        <div class="cr-modal-warning">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            Siguroha nga <strong>nadawat nimo</strong> ang tanan products bag-o mo-confirm. Dili na ma-undo.
        </div>
        <div class="cr-modal-actions">
            <button type="button" class="cr-modal-btn-cancel" data-cr-close>Cancel</button>
            <button type="button" class="cr-modal-btn-confirm" id="crModalConfirm">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                Yes, Confirm
            </button>
        </div>
    </div>
</div>


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
    /* ═══ HEADER ═══ */
    .page-header { display: flex; align-items: center; margin-bottom: 16px; }
    .page-header-left { display: flex; align-items: center; gap: 12px; }
    .back-btn {
        width: 38px; height: 38px; border-radius: 11px;
        background: var(--t-input); color: var(--t-text-2);
        display: grid; place-items: center; text-decoration: none;
        border: 1px solid var(--t-border);
        transition: all 0.15s;
    }
    .back-btn:hover { background: var(--t-card); color: var(--t-text); border-color: var(--t-accent); }
    .back-btn:active { transform: scale(0.94); }
    .page-title { font-size: 17px; font-weight: 800; color: var(--t-text); letter-spacing: -0.02em; }
    .page-sub { font-size: 11.5px; color: var(--t-text-3); margin-top: 2px; }

    /* ═══ HERO ═══ */
    .dv-hero {
        display: flex; align-items: center; gap: 14px;
        padding: 16px; border-radius: 18px; margin-bottom: 14px;
    }
    .dv-hero.confirmed { background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.28); }
    .dv-hero.transit { background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.28); }
    .dv-hero.pending { background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); }

    .dv-hero-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .dv-hero.confirmed .dv-hero-icon { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .dv-hero.transit .dv-hero-icon { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .dv-hero.pending .dv-hero-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

    .dv-hero-text { flex: 1; min-width: 0; }
    .dv-hero-label { font-size: 15px; font-weight: 800; margin-bottom: 3px; letter-spacing: -0.01em; }
    .dv-hero.confirmed .dv-hero-label { color: #22c55e; }
    .dv-hero.transit .dv-hero-label { color: #3b82f6; }
    .dv-hero.pending .dv-hero-label { color: #f59e0b; }
    .dv-hero-sub { font-size: 11.5px; color: var(--t-text-3); }

    .dv-hero-amount { text-align: right; flex-shrink: 0; }
    .dv-hero-amount-label {
        font-size: 9.5px; color: var(--t-text-3);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-bottom: 3px;
    }
    .dv-hero-amount-value {
        font-size: 20px; font-weight: 800; color: var(--t-accent);
        font-variant-numeric: tabular-nums; letter-spacing: -0.02em;
    }

    /* ═══ PAY NOW CTA ═══ */
    .dv-pay-cta {
        padding: 20px;
        background:
            radial-gradient(circle at 100% 0%, rgba(var(--t-accent-rgb), 0.15) 0%, transparent 60%),
            linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.10), var(--t-card));
        border: 1px solid rgba(var(--t-accent-rgb), 0.3);
        border-radius: 18px;
        margin-bottom: 14px;
        animation: dvSlideIn 0.4s cubic-bezier(0.34, 1.2, 0.64, 1) both;
    }
    .dv-pay-cta-head {
        display: flex; align-items: center; gap: 8px;
        font-size: 10.5px; font-weight: 800;
        color: var(--t-text-3);
        text-transform: uppercase; letter-spacing: 0.12em;
        margin-bottom: 10px;
    }
    .dv-pay-cta-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #f59e0b;
        box-shadow: 0 0 8px rgba(245, 158, 11, 0.9);
        animation: dvPulse 1.6s ease-in-out infinite;
    }
    @keyframes dvPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
    }
    .dv-pay-cta-amount {
        font-size: 34px; font-weight: 800;
        color: var(--t-accent);
        letter-spacing: -0.03em; line-height: 1;
        font-variant-numeric: tabular-nums;
        margin-bottom: 16px;
        text-shadow: 0 2px 20px rgba(var(--t-accent-rgb), 0.25);
    }
    .dv-pay-cta-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 9px; width: 100%; min-height: 50px;
        padding: 0 22px;
        border-radius: 13px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        color: #ffffff !important;
        font-family: inherit; font-size: 14.5px; font-weight: 800;
        letter-spacing: -0.01em; text-decoration: none;
        border: none; cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        box-shadow:
            0 10px 24px -8px rgba(var(--t-accent-rgb), 0.6),
            inset 0 1px 0 rgba(255, 255, 255, 0.18);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dv-pay-cta-btn:hover {
        transform: translateY(-1px);
        box-shadow:
            0 14px 32px -8px rgba(var(--t-accent-rgb), 0.8),
            inset 0 1px 0 rgba(255, 255, 255, 0.22);
    }
    .dv-pay-cta-btn:active { transform: scale(0.97); }
    .dv-pay-cta-btn svg { stroke: #ffffff !important; flex-shrink: 0; }

    /* ═══ FULLY PAID BANNER ═══ */
    .dv-paid-banner {
        display: flex; align-items: center; gap: 14px;
        padding: 16px 18px;
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.04));
        border: 1px solid rgba(34, 197, 94, 0.35);
        border-radius: 16px;
        margin-bottom: 14px;
        animation: dvSlideIn 0.4s cubic-bezier(0.34, 1.2, 0.64, 1) both;
    }
    .dv-paid-banner-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .dv-paid-banner-text { flex: 1; min-width: 0; }
    .dv-paid-banner-title {
        font-size: 15px; font-weight: 800;
        color: #22c55e; margin-bottom: 3px;
        letter-spacing: -0.01em;
    }
    .dv-paid-banner-sub { font-size: 11.5px; color: var(--t-text-3); }

    /* ═══ CARDS ═══ */
    .card {
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 16px;
        padding: 18px 16px;
        margin-bottom: 14px;
        transition: border-color 0.2s;
    }
    .card:hover { border-color: rgba(var(--t-accent-rgb), 0.2); }
    .card-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .card-head-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: rgba(var(--t-accent-rgb), 0.12);
        color: var(--t-accent);
        display: grid; place-items: center; flex-shrink: 0;
        border: 1px solid rgba(var(--t-accent-rgb), 0.22);
    }
    .card-head-icon.green {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.22);
    }
    .card-head-title { font-size: 13.5px; font-weight: 800; color: var(--t-text); letter-spacing: -0.01em; }
    .card-head-sub { font-size: 11px; color: var(--t-text-3); margin-top: 2px; }

    /* ═══ ITEMS ═══ */
    .items-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
    .item-row {
        display: flex; align-items: center; gap: 11px;
        padding: 9px 0; border-bottom: 1px solid var(--t-border);
    }
    .item-row:last-child { border-bottom: none; }
    .item-thumb {
        width: 40px; height: 40px; border-radius: 10px;
        background: rgba(var(--t-accent-rgb), 0.1);
        display: grid; place-items: center;
        color: rgba(var(--t-accent-rgb), 0.5);
        overflow: hidden; flex-shrink: 0;
    }
    .item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .item-info { flex: 1; min-width: 0; }
    .item-name {
        font-size: 12.5px; font-weight: 700; color: var(--t-text);
        margin-bottom: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .item-meta { font-size: 10.5px; color: var(--t-text-3); }
    .item-total {
        font-size: 13px; font-weight: 800; color: var(--t-accent);
        font-variant-numeric: tabular-nums; flex-shrink: 0;
    }
    .total-row {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 12px;
        border-top: 2px solid rgba(var(--t-accent-rgb), 0.25);
        font-size: 12.5px; color: var(--t-text-2);
    }
    .total-row strong {
        font-size: 18px; font-weight: 800; color: var(--t-accent);
        font-variant-numeric: tabular-nums;
    }

    /* ═══ BALANCE ═══ */
    .balance-list { display: flex; flex-direction: column; }
    .balance-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; font-size: 12.5px;
    }
    .balance-row + .balance-row { border-top: 1px solid var(--t-border); }
    .balance-label { color: var(--t-text-2); }
    .balance-value {
        font-weight: 700; color: var(--t-text);
        font-variant-numeric: tabular-nums;
    }
    .balance-value.green { color: #22c55e; }
    .balance-value.accent { color: var(--t-accent); }
    .balance-total {
        padding-top: 12px;
        border-top: 1px solid rgba(var(--t-accent-rgb), 0.25) !important;
    }
    .balance-total .balance-value { font-size: 14px; }

    /* ═══ ACTION CARD ═══ */
    .dv-action { padding: 16px; border-radius: 16px; margin-bottom: 14px; }
    .dv-action.confirmed {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.25);
        display: flex; align-items: center; gap: 12px;
    }
    .dv-action.pending {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
        display: flex; align-items: center; gap: 12px;
    }
    .dv-action.transit {
        background: var(--t-card);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .dv-action-head { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .dv-action-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .dv-action.confirmed .dv-action-icon { background: rgba(34, 197, 94, 0.18); color: #22c55e; }
    .dv-action.transit .dv-action-icon { background: rgba(59, 130, 246, 0.18); color: #3b82f6; }
    .dv-action.pending .dv-action-icon { background: rgba(245, 158, 11, 0.18); color: #f59e0b; }
    .dv-action-text { flex: 1; min-width: 0; }
    .dv-action-title { font-size: 14px; font-weight: 800; margin-bottom: 2px; letter-spacing: -0.01em; }
    .dv-action.confirmed .dv-action-title { color: #22c55e; }
    .dv-action.transit .dv-action-title { color: #3b82f6; }
    .dv-action.pending .dv-action-title { color: #f59e0b; }
    .dv-action-sub { font-size: 11.5px; color: var(--t-text-3); }

    .dv-action-form { display: flex; flex-direction: column; gap: 8px; }
    .dv-action-notes {
        width: 100%; padding: 11px 13px;
        background: var(--t-input);
        border: 1px solid var(--t-border-2);
        border-radius: 11px;
        color: var(--t-text);
        font-size: 12.5px; font-family: inherit;
        outline: none; resize: none;
    }
    .dv-action-notes:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }

    .dv-action-btn {
        width: 100%; min-height: 48px;
        display: inline-flex; align-items: center; justify-content: center;
        gap: 7px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        border: none; border-radius: 12px;
        color: #fff;
        font-size: 13.5px; font-weight: 800;
        font-family: inherit; cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.6);
        transition: all 0.15s;
    }
    .dv-action-btn:active { transform: scale(0.98); }

    /* ═══ MODAL ═══ */
    .cr-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 8, 6, 0.75);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
        animation: crFadeIn 0.2s ease;
    }
    @keyframes crFadeIn { from { opacity: 0; } to { opacity: 1; } }
    .cr-modal {
        width: 100%; max-width: 400px;
        background: var(--t-card);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        padding: 26px 24px 22px;
        box-shadow: 0 30px 60px -20px rgba(0,0,0,0.75), 0 0 80px -20px rgba(34,197,94,0.25);
        animation: crModalIn 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: center;
    }
    @keyframes crModalIn {
        from { opacity: 0; transform: scale(0.92) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .cr-modal-icon {
        width: 64px; height: 64px; margin: 0 auto 16px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(34,197,94,0.28), rgba(20,130,60,0.15));
        border: 1px solid rgba(34,197,94,0.35);
        color: #22c55e;
        display: grid; place-items: center;
        box-shadow: 0 10px 30px -10px rgba(34,197,94,0.5);
    }
    .cr-modal-title { font-size: 19px; font-weight: 800; color: var(--t-text); margin-bottom: 6px; }
    .cr-modal-sub { font-size: 12.5px; color: var(--t-text-3); margin-bottom: 20px; }
    .cr-modal-sub strong { color: var(--t-accent); font-weight: 700; }
    .cr-modal-body {
        background: var(--t-input);
        border: 1px solid var(--t-border);
        border-radius: 12px; padding: 12px 14px;
        margin-bottom: 16px; text-align: left;
    }
    .cr-modal-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0; font-size: 12.5px;
    }
    .cr-modal-row + .cr-modal-row { border-top: 1px solid var(--t-border); }
    .cr-modal-row span { color: var(--t-text-3); }
    .cr-modal-row strong { color: var(--t-text); font-weight: 700; }
    .cr-modal-warning {
        display: flex; align-items: flex-start; gap: 8px;
        padding: 10px 12px;
        background: rgba(245,158,11,0.08);
        border: 1px solid rgba(245,158,11,0.22);
        border-radius: 10px;
        font-size: 11.5px; color: #d4a35a;
        text-align: left; margin-bottom: 20px; line-height: 1.45;
    }
    .cr-modal-warning svg { flex-shrink: 0; margin-top: 1px; color: #f59e0b; }
    .cr-modal-actions { display: flex; gap: 10px; }
    .cr-modal-btn-cancel, .cr-modal-btn-confirm {
        flex: 1; min-height: 46px;
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        border: none; border-radius: 12px;
        font-size: 13.5px; font-weight: 800;
        font-family: inherit; cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    .cr-modal-btn-cancel {
        background: var(--t-input);
        color: var(--t-text-2);
        border: 1px solid var(--t-border-2);
    }
    .cr-modal-btn-cancel:hover { background: var(--t-card); color: var(--t-text); }
    .cr-modal-btn-confirm {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        box-shadow: 0 10px 24px -8px rgba(34,197,94,0.7);
    }
    .cr-modal-btn-confirm:hover { background: linear-gradient(135deg, #34d977, #16a34a); }
    .cr-modal-btn-confirm:active, .cr-modal-btn-cancel:active { transform: scale(0.98); }

    @keyframes dvSlideIn {
        0% { opacity: 0; transform: translateY(8px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* ═══ MOBILE ═══ */
    @media (max-width: 480px) {
        .dv-hero { padding: 14px; gap: 12px; }
        .dv-hero-icon { width: 44px; height: 44px; }
        .dv-hero-amount-value { font-size: 18px; }
        .dv-pay-cta { padding: 16px; }
        .dv-pay-cta-amount { font-size: 30px; }
        .dv-pay-cta-btn { min-height: 48px; font-size: 14px; }
        .cr-modal { padding: 22px 18px 18px; border-radius: 18px; }
        .cr-modal-title { font-size: 17px; }
    }

    /* ═══ LIGHT/BLUE THEME ═══ */
    html[data-theme="light"] .card,
    html[data-theme="blue"] .card,
    html[data-theme="light"] .cr-modal,
    html[data-theme="blue"] .cr-modal {
        background: #ffffff !important;
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .dv-action.transit,
    html[data-theme="blue"] .dv-action.transit {
        background: #ffffff !important;
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const openBtn = document.getElementById('openConfirmModal');
    const modal = document.getElementById('confirmReceivedModal');
    const form = document.getElementById('confirmForm');
    const confirmBtn = document.getElementById('crModalConfirm');

    if (!openBtn || !modal || !form) return;

    openBtn.addEventListener('click', function() {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('scroll-locked');
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10" opacity="0.3"/><path d="M22 12a10 10 0 0 1-10 10" stroke-linecap="round"/></svg> Confirming...';
            form.submit();
        });
    }

    modal.querySelectorAll('[data-cr-close]').forEach(function(el) {
        el.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
    });

    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('scroll-locked');
    }
})();
</script>
@endpush