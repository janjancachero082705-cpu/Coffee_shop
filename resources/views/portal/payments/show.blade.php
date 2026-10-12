@extends('portal.layouts.app')

@section('title', $payment->payment_number)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.payments.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="page-title">{{ $payment->payment_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="pay-flash">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M5 12l5 5L20 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- HERO --}}
<div class="pay-hero" style="--mc: {{ $payment->method_color }};">
    <div class="pay-hero-icon">{{ $payment->method_icon }}</div>
    <div class="pay-hero-text">
        <div class="pay-hero-label">{{ $payment->method_label }}</div>
        <div class="pay-hero-sub">
            @if($payment->reference_number)
                Ref: {{ $payment->reference_number }}
            @else
                {{ \Carbon\Carbon::parse($payment->payment_date)->diffForHumans() }}
            @endif
        </div>
    </div>
    <div class="pay-hero-amount">
        <div class="pay-hero-amount-label">Amount</div>
        <div class="pay-hero-amount-value">&#8369;{{ number_format($payment->amount, 2) }}</div>
    </div>
</div>

{{-- DETAILS --}}

{{-- ═══════════ VERIFICATION STATUS ═══════════ --}}
@php
    $vStatus = $payment->verification_status ?? 'pending';
@endphp

@if($vStatus === 'pending')
    <div class="pvfy pvfy-pending">
        <div class="pvfy-icon">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pvfy-body">
            <div class="pvfy-title">Waiting for Admin Approval</div>
            <div class="pvfy-desc">Gi-review pa sa admin imong bayad. Dili pa ma-count sa imong balance hangtud ma-approve.</div>
        </div>
    </div>
@elseif($vStatus === 'verified')
    <div class="pvfy pvfy-verified">
        <div class="pvfy-icon">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="pvfy-body">
            <div class="pvfy-title">Paid na ✅</div>
            <div class="pvfy-desc">
                Gi-approve na sa admin.
                @if($payment->verified_at)
                    · {{ \Carbon\Carbon::parse($payment->verified_at)->diffForHumans() }}
                @endif
            </div>
        </div>
    </div>
@elseif($vStatus === 'rejected')
    <div class="pvfy pvfy-rejected">
        <div class="pvfy-icon">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </div>
        <div class="pvfy-body">
            <div class="pvfy-title">Payment Rejected ❌</div>
            <div class="pvfy-desc">
                @if($payment->rejection_reason)
                    Reason: {{ $payment->rejection_reason }}
                @else
                    Wala gi-approve sa admin. Contact admin for details.
                @endif
            </div>
        </div>
    </div>
@endif
<div class="pay-card">
    <div class="pay-card-head">
        <div class="pay-card-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 7h16M4 12h16M4 17h10"/>
            </svg>
        </div>
        <div>
            <div class="pay-card-title">Details</div>
        </div>
    </div>

    <div class="pay-rows">
        <div class="pay-row">
            <span>Payment #</span>
            <strong>{{ $payment->payment_number }}</strong>
        </div>
        <div class="pay-row">
            <span>Method</span>
            <strong style="color:{{ $payment->method_color }};">{{ $payment->method_icon }} {{ $payment->method_label }}</strong>
        </div>
        @if($payment->reference_number)
            <div class="pay-row">
                <span>Reference #</span>
                <strong style="font-family:monospace;">{{ $payment->reference_number }}</strong>
            </div>
        @endif
        <div class="pay-row">
            <span>Payment Date</span>
            <strong>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</strong>
        </div>
        @if($payment->deliveryReceipt)
            <div class="pay-row">
                <span>Linked DR</span>
                <strong>{{ $payment->deliveryReceipt->dr_number }}</strong>
            </div>
        @endif
        @if($payment->notes)
            <div class="pay-row" style="display:block;">
                <span style="display:block;margin-bottom:6px;">Notes</span>
                <div style="color:var(--t-text-2);font-size:12.5px;line-height:1.5;">{{ $payment->notes }}</div>
            </div>
        @endif
    </div>
</div>

{{-- TIMELINE --}}
<div class="pay-card">
    <div class="pay-card-head">
        <div class="pay-card-icon blue">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div>
            <div class="pay-card-title">Timeline</div>
        </div>
    </div>
    <div class="pay-timeline">
        <div class="pay-tl-item">
            <div class="pay-tl-dot" style="background:#22c55e;"></div>
            <div>
                <div class="pay-tl-label">Payment recorded</div>
                <div class="pay-tl-time">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}</div>
            </div>
        </div>
        <div class="pay-tl-item">
            <div class="pay-tl-dot"></div>
            <div>
                <div class="pay-tl-label">Payment date</div>
                <div class="pay-tl-time">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
            </div>
        </div>
    </div>
</div>


{{-- ═══ REPORTS + PAYMENTS — PRO THEME OVERRIDE ═══ --}}
@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   REPORTS & PAYMENTS — Full theme-aware override
   ═══════════════════════════════════════════════════════════ */

/* ═══ GENERIC PAGE ═══ */
.page-title,
.pp-title,
.sr-title,
.rr-title { color: var(--t-text) !important; }
.page-sub,
.pp-sub,
.sr-subtitle,
.rr-subtitle { color: var(--t-text-3) !important; }

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

/* ═══ STAT CARDS ═══ */
.pp-stat,
.sr-kpi,
.rr-stat,
.pf-stat {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    color: var(--t-text) !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.pp-stat:hover,
.sr-kpi:hover,
.rr-stat:hover {
    border-color: rgba(var(--t-accent-rgb), 0.4) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px -10px rgba(var(--t-accent-rgb), 0.3) !important;
}
.pp-stat-value,
.sr-kpi-value,
.rr-stat-value {
    color: var(--t-text) !important;
    font-variant-numeric: tabular-nums !important;
}
.pp-stat-label,
.sr-kpi-label,
.rr-stat-label {
    color: var(--t-text-3) !important;
}

/* ═══ STAT ICONS ═══ */
.pp-stat-icon,
.sr-kpi-icon,
.rr-stat-icon {
    background: rgba(var(--t-accent-rgb), 0.12) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.25) !important;
    color: var(--t-accent) !important;
}
.pp-stat-icon.green { background: rgba(34, 197, 94, 0.14) !important; border-color: rgba(34, 197, 94, 0.3) !important; color: #22c55e !important; }
.pp-stat-icon.amber { background: rgba(245, 158, 11, 0.14) !important; border-color: rgba(245, 158, 11, 0.3) !important; color: #f59e0b !important; }
.pp-stat-icon.red   { background: rgba(239, 68, 68, 0.14) !important; border-color: rgba(239, 68, 68, 0.3) !important; color: #ef4444 !important; }
.pp-stat-icon.blue  { background: rgba(59, 130, 246, 0.14) !important; border-color: rgba(59, 130, 246, 0.3) !important; color: #3b82f6 !important; }

/* ═══ CARDS ═══ */
.pp-card,
.pay-card,
.sr-card,
.rr-card {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    color: var(--t-text) !important;
    transition: all 0.25s !important;
}
.pp-card:hover,
.pay-card:hover,
.sr-card:hover {
    border-color: rgba(var(--t-accent-rgb), 0.28) !important;
    box-shadow: 0 8px 24px -12px rgba(var(--t-accent-rgb), 0.2) !important;
}
.pp-card-title,
.pay-card-title {
    color: var(--t-text) !important;
}
.pp-card-sub,
.pay-card-sub {
    color: var(--t-text-3) !important;
}
.pp-card-amount {
    color: var(--t-accent) !important;
}

/* ═══ CARD HEAD ICONS ═══ */
.pay-card-icon,
.card-head-icon {
    background: rgba(var(--t-accent-rgb), 0.12) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.25) !important;
    color: var(--t-accent) !important;
}
.pay-card-icon.green,
.card-head-icon.green {
    background: rgba(34, 197, 94, 0.12) !important;
    border-color: rgba(34, 197, 94, 0.25) !important;
    color: #22c55e !important;
}
.pay-card-icon.blue {
    background: rgba(59, 130, 246, 0.12) !important;
    border-color: rgba(59, 130, 246, 0.25) !important;
    color: #3b82f6 !important;
}

/* ═══ PAY ROWS ═══ */
.pay-row,
.balance-row {
    border-color: var(--t-border) !important;
    color: var(--t-text) !important;
}
.pay-row span,
.balance-label {
    color: var(--t-text-3) !important;
}
.pay-row strong,
.balance-value {
    color: var(--t-text) !important;
}
.balance-value.green { color: #22c55e !important; }
.balance-value.accent { color: var(--t-accent) !important; }
.balance-total {
    border-top: 2px solid rgba(var(--t-accent-rgb), 0.28) !important;
    padding-top: 12px !important;
}

/* ═══ PAY HERO ═══ */
.pay-hero {
    background: var(--t-card) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.3) !important;
    border-radius: 18px !important;
}
.pay-hero-sub { color: var(--t-text-3) !important; }
.pay-hero-amount-label { color: var(--t-text-3) !important; }
.pay-hero-amount-value { color: var(--t-accent) !important; }

/* ═══ METHOD BADGE ═══ */
.pp-method-badge {
    border-radius: 8px !important;
    font-weight: 800 !important;
    letter-spacing: 0.01em !important;
    padding: 5px 10px !important;
    background: rgba(var(--t-accent-rgb), 0.14) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.32) !important;
    color: var(--t-accent) !important;
}

/* ═══ PENDING PAYMENTS ═══ */
.pp-pending {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.03)) !important;
    border: 1px solid rgba(245, 158, 11, 0.32) !important;
    border-radius: 18px !important;
}
.pp-pending-title { color: var(--t-text) !important; }
.pp-pending-sub { color: var(--t-text-3) !important; }
.pp-pending-count {
    background: rgba(245, 158, 11, 0.2) !important;
    border: 1px solid rgba(245, 158, 11, 0.4) !important;
    color: #f59e0b !important;
}
.pp-pending-item {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 12px !important;
}
.pp-pending-dr { color: var(--t-text) !important; }
.pp-pending-meta { color: var(--t-text-3) !important; }
.pp-pending-meta strong { color: #f59e0b !important; }
.pp-pending-confirm {
    background: rgba(34, 197, 94, 0.12) !important;
    border: 1px solid rgba(34, 197, 94, 0.35) !important;
    color: #22c55e !important;
}
.pp-pending-pay {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.6) !important;
}

/* ═══ TABS ═══ */
.pp-tabs {
    background: var(--t-input) !important;
    border: 1px solid var(--t-border) !important;
}
.pp-tab {
    color: var(--t-text-2) !important;
}
.pp-tab:hover {
    background: rgba(var(--t-accent-rgb), 0.08) !important;
    color: var(--t-text) !important;
}
.pp-tab.active {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.5) !important;
}
.pp-tab-count {
    background: rgba(var(--t-accent-rgb), 0.15) !important;
    color: var(--t-accent) !important;
}
.pp-tab.active .pp-tab-count {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

/* ═══ CTA BUTTONS ═══ */
.pp-pay-btn,
.pp-action,
.pay-submit,
.pay-cta {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.6) !important;
}
.pp-pay-btn:hover,
.pp-action:hover,
.pay-submit:hover:not(:disabled) {
    transform: translateY(-1px) !important;
    box-shadow: 0 12px 28px -8px rgba(var(--t-accent-rgb), 0.8) !important;
}
.pay-submit:disabled {
    background: var(--t-input) !important;
    color: var(--t-text-3) !important;
    box-shadow: none !important;
}

/* ═══ EMPTY STATE ═══ */
.pp-empty,
.pay-empty,
.sr-empty {
    background: var(--t-card) !important;
    border: 1px dashed var(--t-border-2) !important;
    border-radius: 18px !important;
}
.pp-empty-icon,
.pay-empty-icon,
.sr-empty-icon {
    background: rgba(var(--t-accent-rgb), 0.1) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.28) !important;
    color: var(--t-accent) !important;
}
.pp-empty-title,
.pay-empty-title,
.sr-empty-title { color: var(--t-text) !important; }
.pp-empty-text,
.pay-empty-text,
.sr-empty-text { color: var(--t-text-3) !important; }
.pp-empty-btn,
.pay-empty-btn {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.7) !important;
}

/* ═══ FLASH ═══ */
.pay-flash,
.p-alert {
    border-radius: 12px !important;
    padding: 12px 14px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 14px !important;
}
.pay-flash,
.p-alert-success {
    background: rgba(34, 197, 94, 0.1) !important;
    border: 1px solid rgba(34, 197, 94, 0.3) !important;
    color: #22c55e !important;
}
.p-alert-error {
    background: rgba(239, 68, 68, 0.1) !important;
    border: 1px solid rgba(239, 68, 68, 0.3) !important;
    color: #fca5a5 !important;
}

/* ═══ CREATE PAYMENT FORM ═══ */
.pay-header-title { color: var(--t-text) !important; }
.pay-header-subtitle { color: var(--t-text-3) !important; }
.pay-back-btn {
    background: var(--t-input) !important;
    border: 1px solid var(--t-border) !important;
    color: var(--t-text-2) !important;
}
.pay-back-btn:hover {
    background: var(--t-card) !important;
    color: var(--t-accent) !important;
    border-color: rgba(var(--t-accent-rgb), 0.4) !important;
}

.pay-balance-card {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
}
.pay-balance-icon {
    background: rgba(var(--t-accent-rgb), 0.12) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.2) !important;
    color: var(--t-accent) !important;
}
.pay-balance-label { color: var(--t-text-3) !important; }
.pay-balance-value { color: var(--t-text) !important; }
.pay-balance-currency { color: var(--t-accent) !important; }
.pay-balance-meta-label { color: var(--t-text-3) !important; }
.pay-balance-meta-value { color: var(--t-text) !important; }

.pay-section {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
}
.pay-section-title { color: var(--t-text) !important; }
.pay-section-desc { color: var(--t-text-3) !important; }

.pay-amount-field {
    background: var(--t-input) !important;
    border: 1.5px solid var(--t-border-2) !important;
}
.pay-amount-field:focus-within {
    border-color: var(--t-accent) !important;
    box-shadow: 0 0 0 4px rgba(var(--t-accent-rgb), 0.15) !important;
}
.pay-amount-currency { color: var(--t-accent) !important; }
.pay-amount-input { color: var(--t-text) !important; }
.pay-amount-input::placeholder { color: var(--t-text-3) !important; }

.pay-chip {
    background: var(--t-input) !important;
    border: 1px solid var(--t-border-2) !important;
    color: var(--t-text-2) !important;
}
.pay-chip:hover {
    background: var(--t-card) !important;
    color: var(--t-text) !important;
    border-color: rgba(var(--t-accent-rgb), 0.3) !important;
}
.pay-chip-amount { color: var(--t-text) !important; }
.pay-chip svg { color: var(--t-text-3) !important; }
.pay-chip:hover svg { color: var(--t-accent) !important; }

.pay-segmented {
    background: var(--t-input) !important;
    border: 1px solid var(--t-border) !important;
}
.pay-segment { color: var(--t-text-2) !important; }
.pay-segment.active {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px -4px rgba(var(--t-accent-rgb), 0.5) !important;
}
.pay-segment.active svg { stroke: #ffffff !important; }

.pay-method-inner {
    background: var(--t-input) !important;
    border: 1.5px solid var(--t-border-2) !important;
}
.pay-method:hover .pay-method-inner {
    background: var(--t-card) !important;
}
.pay-method input:checked + .pay-method-inner {
    background: rgba(var(--t-accent-rgb), 0.1) !important;
    border-color: var(--t-accent) !important;
    box-shadow: 0 0 0 3px rgba(var(--t-accent-rgb), 0.15) !important;
}
.pay-method-name { color: var(--t-text) !important; }
.pay-method-desc { color: var(--t-text-3) !important; }
.pay-method-check {
    background: var(--t-card) !important;
    border-color: var(--t-border-2) !important;
}
.pay-method input:checked + .pay-method-inner .pay-method-check {
    background: var(--t-accent) !important;
    border-color: var(--t-accent) !important;
    color: #ffffff !important;
}

.pay-input {
    background: var(--t-input) !important;
    border: 1.5px solid var(--t-border-2) !important;
    color: var(--t-text) !important;
}
.pay-input::placeholder { color: var(--t-text-3) !important; }
.pay-input:focus {
    border-color: var(--t-accent) !important;
    box-shadow: 0 0 0 4px rgba(var(--t-accent-rgb), 0.15) !important;
}
.pay-input-icon { color: var(--t-text-3) !important; }
.pay-input-wrap:focus-within .pay-input-icon { color: var(--t-accent) !important; }

/* ═══ VERIFICATION BANNER ═══ */
.pvfy {
    border-radius: 14px !important;
}
.pvfy-pending { background: rgba(245, 158, 11, 0.1) !important; border-color: rgba(245, 158, 11, 0.35) !important; }
.pvfy-verified { background: rgba(34, 197, 94, 0.1) !important; border-color: rgba(34, 197, 94, 0.35) !important; }
.pvfy-rejected { background: rgba(239, 68, 68, 0.1) !important; border-color: rgba(239, 68, 68, 0.35) !important; }
.pvfy-pending .pvfy-title { color: #f59e0b !important; }
.pvfy-verified .pvfy-title { color: #22c55e !important; }
.pvfy-rejected .pvfy-title { color: #ef4444 !important; }
.pvfy-pending .pvfy-desc { color: #fcd34d !important; }
.pvfy-verified .pvfy-desc { color: #86efac !important; }
.pvfy-rejected .pvfy-desc { color: #fca5a5 !important; }

/* ═══ TIMELINE ═══ */
.pay-timeline { color: var(--t-text) !important; }
.pay-tl-dot {
    background: rgba(var(--t-accent-rgb), 0.4) !important;
}
.pay-tl-label { color: var(--t-text) !important; }
.pay-tl-time { color: var(--t-text-3) !important; }

/* ═══ SR- (Sales Report) ═══ */
.sr-hero {
    background: linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.12), var(--t-card)) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.3) !important;
    border-radius: 18px !important;
}
.sr-hero-label,
.sr-hero-code { color: var(--t-accent) !important; }
.sr-hero-title { color: var(--t-text) !important; }
.sr-hero-sub { color: var(--t-text-3) !important; }

.sr-table thead th {
    background: rgba(var(--t-accent-rgb), 0.06) !important;
    color: var(--t-text-3) !important;
    border-color: var(--t-border) !important;
}
.sr-table tbody td {
    color: var(--t-text-2) !important;
    border-color: var(--t-border) !important;
}
.sr-table tbody tr:hover {
    background: rgba(var(--t-accent-rgb), 0.04) !important;
}

.sr-row {
    border-color: var(--t-border) !important;
}
.sr-row-label { color: var(--t-text-3) !important; }
.sr-row-value { color: var(--t-text) !important; }

.sr-total {
    border-top: 2px solid rgba(var(--t-accent-rgb), 0.3) !important;
    padding-top: 14px !important;
}
.sr-total-label { color: var(--t-text) !important; }
.sr-total-value { color: var(--t-accent) !important; font-size: 18px !important; }

/* ═══ RR- (Reports list) ═══ */
.rr-number {
    color: var(--t-text) !important;
    font-family: ui-monospace, monospace !important;
}
.rr-total {
    color: var(--t-accent) !important;
}
.rr-store-name { color: var(--t-text) !important; }
.rr-store-meta { color: var(--t-text-3) !important; }

.rr-status {
    font-weight: 800 !important;
    letter-spacing: 0.04em !important;
}
.rr-status.pending  { background: rgba(245, 158, 11, 0.14) !important; color: #f59e0b !important; border-color: rgba(245, 158, 11, 0.3) !important; }
.rr-status.approved { background: rgba(34, 197, 94, 0.14) !important; color: #22c55e !important; border-color: rgba(34, 197, 94, 0.3) !important; }
.rr-status.rejected { background: rgba(239, 68, 68, 0.14) !important; color: #ef4444 !important; border-color: rgba(239, 68, 68, 0.3) !important; }

/* ═══ LIGHT THEME — FORCE DARK TEXT ═══ */
html[data-theme="light"] .page-title,
html[data-theme="light"] .pp-title,
html[data-theme="light"] .pp-card-title,
html[data-theme="light"] .pay-card-title,
html[data-theme="light"] .pp-stat-value,
html[data-theme="light"] .pay-hero-amount-value,
html[data-theme="light"] .pay-balance-value,
html[data-theme="light"] .pay-row strong,
html[data-theme="light"] .balance-value,
html[data-theme="light"] .pp-empty-title,
html[data-theme="light"] .pay-header-title,
html[data-theme="light"] .pay-section-title,
html[data-theme="light"] .pay-method-name,
html[data-theme="light"] .pay-chip-amount,
html[data-theme="light"] .pp-pending-title,
html[data-theme="light"] .pp-pending-dr,
html[data-theme="light"] .sr-hero-title,
html[data-theme="light"] .sr-row-value,
html[data-theme="light"] .sr-total-value,
html[data-theme="light"] .rr-number,
html[data-theme="light"] .rr-store-name,
html[data-theme="light"] .pay-tl-label,
html[data-theme="blue"] .page-title,
html[data-theme="blue"] .pp-title,
html[data-theme="blue"] .pp-card-title,
html[data-theme="blue"] .pay-card-title,
html[data-theme="blue"] .pp-stat-value,
html[data-theme="blue"] .pay-hero-amount-value,
html[data-theme="blue"] .pay-balance-value,
html[data-theme="blue"] .pay-row strong,
html[data-theme="blue"] .balance-value,
html[data-theme="blue"] .pp-empty-title,
html[data-theme="blue"] .pay-header-title,
html[data-theme="blue"] .pay-section-title,
html[data-theme="blue"] .pay-method-name,
html[data-theme="blue"] .pay-chip-amount,
html[data-theme="blue"] .pp-pending-title,
html[data-theme="blue"] .pp-pending-dr,
html[data-theme="blue"] .sr-hero-title,
html[data-theme="blue"] .sr-row-value,
html[data-theme="blue"] .sr-total-value,
html[data-theme="blue"] .rr-number,
html[data-theme="blue"] .rr-store-name,
html[data-theme="blue"] .pay-tl-label {
    color: #0c1e3d !important;
}

html[data-theme="light"] .page-sub,
html[data-theme="light"] .pp-sub,
html[data-theme="light"] .pp-card-sub,
html[data-theme="light"] .pay-card-sub,
html[data-theme="light"] .pp-stat-label,
html[data-theme="light"] .pay-hero-sub,
html[data-theme="light"] .pay-hero-amount-label,
html[data-theme="light"] .pay-row span,
html[data-theme="light"] .balance-label,
html[data-theme="light"] .pp-empty-text,
html[data-theme="light"] .pay-header-subtitle,
html[data-theme="light"] .pay-section-desc,
html[data-theme="light"] .pay-method-desc,
html[data-theme="light"] .pp-pending-sub,
html[data-theme="light"] .pp-pending-meta,
html[data-theme="light"] .sr-hero-sub,
html[data-theme="light"] .sr-row-label,
html[data-theme="light"] .rr-store-meta,
html[data-theme="light"] .pay-tl-time,
html[data-theme="blue"] .page-sub,
html[data-theme="blue"] .pp-sub,
html[data-theme="blue"] .pp-card-sub,
html[data-theme="blue"] .pay-card-sub,
html[data-theme="blue"] .pp-stat-label,
html[data-theme="blue"] .pay-hero-sub,
html[data-theme="blue"] .pay-hero-amount-label,
html[data-theme="blue"] .pay-row span,
html[data-theme="blue"] .balance-label,
html[data-theme="blue"] .pp-empty-text,
html[data-theme="blue"] .pay-header-subtitle,
html[data-theme="blue"] .pay-section-desc,
html[data-theme="blue"] .pay-method-desc,
html[data-theme="blue"] .pp-pending-sub,
html[data-theme="blue"] .pp-pending-meta,
html[data-theme="blue"] .sr-hero-sub,
html[data-theme="blue"] .sr-row-label,
html[data-theme="blue"] .rr-store-meta,
html[data-theme="blue"] .pay-tl-time {
    color: #64748b !important;
}

/* ═══ DARK THEME — FORCE LIGHT TEXT ═══ */
html[data-theme="dark"] .page-title,
html[data-theme="dark"] .pp-title,
html[data-theme="dark"] .pp-card-title,
html[data-theme="dark"] .pay-card-title,
html[data-theme="dark"] .pp-stat-value,
html[data-theme="dark"] .pay-hero-amount-value,
html[data-theme="dark"] .pay-balance-value,
html[data-theme="dark"] .pay-row strong,
html[data-theme="dark"] .balance-value,
html[data-theme="dark"] .pp-empty-title,
html[data-theme="dark"] .pay-header-title,
html[data-theme="dark"] .pay-section-title,
html[data-theme="dark"] .pay-method-name,
html[data-theme="dark"] .pay-chip-amount,
html[data-theme="dark"] .pp-pending-title,
html[data-theme="dark"] .pp-pending-dr,
html[data-theme="dark"] .sr-hero-title,
html[data-theme="dark"] .sr-row-value,
html[data-theme="dark"] .sr-total-value,
html[data-theme="dark"] .rr-number,
html[data-theme="dark"] .rr-store-name,
html[data-theme="dark"] .pay-tl-label {
    color: #ffffff !important;
}

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .pp-stat { padding: 12px !important; }
    .pp-stat-value { font-size: 18px !important; }
    .pp-card { padding: 14px !important; }
    .pay-hero { padding: 16px !important; }
    .pay-hero-amount-value { font-size: 18px !important; }
    .pay-card { padding: 14px !important; }
}
</style>
@endpush
@endsection

@push('styles')
<style>
    .page-header { display:flex; align-items:center; margin-bottom:16px; }
    .page-header-left { display:flex; align-items:center; gap:12px; }
    .back-btn {
        width:36px; height:36px; border-radius:10px;
        background:var(--t-border); color:var(--t-text-2);
        display:grid; place-items:center; text-decoration:none;
    }
    .back-btn:hover { background:var(--t-border-2); color:var(--t-text); }
    .page-title { font-size:17px; font-weight:800; color:var(--t-text); }
    .page-sub { font-size:11.5px; color:var(--t-text-3); margin-top:2px; }

    .pay-flash {
        display:flex; align-items:center; gap:8px;
        padding:12px 14px; margin-bottom:14px;
        background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3);
        border-radius:12px; color:#22c55e; font-size:12.5px; font-weight:700;
    }

    .pay-hero {
        display:flex; align-items:center; gap:14px;
        padding:18px;
        background:linear-gradient(135deg, color-mix(in srgb, var(--mc) 12%, transparent), rgba(21,18,15,0.6));
        border:1px solid color-mix(in srgb, var(--mc) 35%, transparent);
        border-radius:18px;
        margin-bottom:14px;
    }
    .pay-hero-icon {
        width:52px; height:52px; border-radius:14px;
        background:color-mix(in srgb, var(--mc) 20%, transparent);
        display:grid; place-items:center;
        font-size:24px;
        flex-shrink:0;
    }
    .pay-hero-text { flex:1; min-width:0; }
    .pay-hero-label {
        font-size:15px; font-weight:800;
        color:var(--mc); margin-bottom:3px;
    }
    .pay-hero-sub { font-size:11.5px; color:var(--t-text-3); }
    .pay-hero-amount { text-align:right; flex-shrink:0; }
    .pay-hero-amount-label {
        font-size:9.5px; color:var(--t-text-3);
        text-transform:uppercase; letter-spacing:0.1em;
        font-weight:700; margin-bottom:3px;
    }
    .pay-hero-amount-value {
        font-size:20px; font-weight:800; color:var(--t-accent);
        font-variant-numeric:tabular-nums;
    }

    .pay-card {
        background:var(--t-card);
        border:1px solid var(--t-border);
        border-radius:16px;
        padding:18px 16px;
        margin-bottom:14px;
    }
    .pay-card-head {
        display:flex; align-items:center; gap:12px; margin-bottom:14px;
    }
    .pay-card-icon {
        width:36px; height:36px; border-radius:10px;
        background:rgba(var(--t-accent-rgb), 0.15); color:var(--t-accent);
        display:grid; place-items:center; flex-shrink:0;
    }
    .pay-card-icon.blue { background:rgba(59,130,246,0.15); color:#3b82f6; }
    .pay-card-title { font-size:13.5px; font-weight:800; color:var(--t-text); }

    .pay-rows { display:flex; flex-direction:column; }
    .pay-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:10px 0;
        border-bottom:1px solid var(--t-border);
        font-size:12.5px;
    }
    .pay-row:last-child { border-bottom:none; }
    .pay-row span { color:var(--t-text-3); }
    .pay-row strong { color:var(--t-text); font-weight:700; }

    .pay-timeline { display:flex; flex-direction:column; gap:14px; position:relative; }
    .pay-tl-item { display:flex; gap:12px; align-items:flex-start; }
    .pay-tl-dot {
        width:10px; height:10px; border-radius:50%;
        background:rgba(var(--t-accent-rgb), 0.4);
        margin-top:4px;
        flex-shrink:0;
    }
    .pay-tl-label { font-size:12.5px; font-weight:700; color:var(--t-text); }
    .pay-tl-time { font-size:11px; color:var(--t-text-3); margin-top:2px; }

    /* ═══════════ PORTAL VERIFICATION STATUS ═══════════ */
    .pvfy {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 18px;
        margin: 14px 0;
        border-radius: 14px;
        border: 1px solid;
        animation: pvfyIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes pvfyIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .pvfy-pending {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.03));
        border-color: rgba(245, 158, 11, 0.35);
        border-left: 3px solid #f59e0b;
    }
    .pvfy-verified {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.03));
        border-color: rgba(34, 197, 94, 0.35);
        border-left: 3px solid #22c55e;
    }
    .pvfy-rejected {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(239, 68, 68, 0.03));
        border-color: rgba(239, 68, 68, 0.35);
        border-left: 3px solid #ef4444;
    }
    .pvfy-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .pvfy-pending .pvfy-icon {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
        animation: pvfyPulse 2s ease-in-out infinite;
    }
    .pvfy-verified .pvfy-icon {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.35);
    }
    .pvfy-rejected .pvfy-icon {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }
    @keyframes pvfyPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .pvfy-body { flex: 1; min-width: 0; }
    .pvfy-title {
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 4px;
        line-height: 1.3;
    }
    .pvfy-pending .pvfy-title { color: #f59e0b; }
    .pvfy-verified .pvfy-title { color: #22c55e; }
    .pvfy-rejected .pvfy-title { color: #ef4444; }
    .pvfy-desc {
        font-size: 12px;
        line-height: 1.5;
        opacity: 0.85;
    }
    .pvfy-pending .pvfy-desc { color: #d4a35a; }
    .pvfy-verified .pvfy-desc { color: #86efac; }
    .pvfy-rejected .pvfy-desc { color: #fca5a5; }

    /* ════════════════════════════════════════════════════════
       PAYMENTS SHOW — THEME FORCE
       ════════════════════════════════════════════════════════ */

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

    .pay-flash {
        background: rgba(34, 197, 94, 0.1) !important;
        border-color: rgba(34, 197, 94, 0.3) !important;
        color: #22c55e !important;
    }

    .pay-hero {
        background: var(--t-card) !important;
        border-color: rgba(var(--t-accent-rgb), 0.35) !important;
    }
    .pay-hero-sub { color: var(--t-text-3) !important; }
    .pay-hero-amount-label { color: var(--t-text-3) !important; }
    .pay-hero-amount-value { color: var(--t-accent) !important; }

    .pay-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pay-card-icon {
        background: rgba(var(--t-accent-rgb), 0.15) !important;
        color: var(--t-accent) !important;
    }
    .pay-card-icon.blue {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #3b82f6 !important;
    }
    .pay-card-title { color: var(--t-text) !important; }

    .pay-row {
        border-bottom-color: var(--t-border) !important;
    }
    .pay-row span { color: var(--t-text-3) !important; }
    .pay-row strong { color: var(--t-text) !important; }

    .pay-tl-dot {
        background: rgba(var(--t-accent-rgb), 0.4) !important;
    }
    .pay-tl-label { color: var(--t-text) !important; }
    .pay-tl-time { color: var(--t-text-3) !important; }
    /* ════════════════════════════════════════════════════════
       PAYMENTS — BULLETPROOF TEXT VISIBILITY
       Force tanan text visible sa tanan 4 themes
       ════════════════════════════════════════════════════════ */

    /* ═══ LIGHT THEMES — FORCE DARK TEXT ═══ */
    html[data-theme="light"] .p-main,
    html[data-theme="blue"] .p-main {
        color: #0c1e3d !important;
    }

    /* All text elements */
    html[data-theme="light"] .p-main *,
    html[data-theme="blue"] .p-main * {
        /* Base safety */
    }

    /* Headings */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4,
    html[data-theme="light"] .p-main h5,
    html[data-theme="light"] .p-main h6,
    html[data-theme="blue"] .p-main h1,
    html[data-theme="blue"] .p-main h2,
    html[data-theme="blue"] .p-main h3,
    html[data-theme="blue"] .p-main h4,
    html[data-theme="blue"] .p-main h5,
    html[data-theme="blue"] .p-main h6 {
        color: #0c1e3d !important;
    }

    /* All text-based classes */
    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]):not([class*="status"]),
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]):not([class*="status"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main [class*="text"]:not([class*="text-success"]):not([class*="text-danger"]):not([class*="text-warn"]) ,
    html[data-theme="blue"] .p-main [class*="title"],
    html[data-theme="blue"] .p-main [class*="label"]:not([class*="badge"]):not([class*="status"]),
    html[data-theme="blue"] .p-main [class*="value"]:not([class*="badge"]):not([class*="status"]),
    html[data-theme="blue"] .p-main [class*="name"],
    html[data-theme="blue"] .p-main [class*="text"]:not([class*="text-success"]):not([class*="text-danger"]):not([class*="text-warn"]) {
        color: #0c1e3d !important;
    }

    /* Subtitles + meta + desc — lighter */
    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"],
    html[data-theme="blue"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="blue"] .p-main [class*="meta"],
    html[data-theme="blue"] .p-main [class*="desc"],
    html[data-theme="blue"] .p-main [class*="hint"] {
        color: #64748b !important;
    }

    /* Strong / B tags */
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b,
    html[data-theme="blue"] .p-main strong,
    html[data-theme="blue"] .p-main b {
        color: #0c1e3d !important;
    }

    /* Spans, divs, paragraphs with no class — inherit */
    html[data-theme="light"] .p-main span:not([class]),
    html[data-theme="light"] .p-main p:not([class]),
    html[data-theme="light"] .p-main div:not([class]),
    html[data-theme="blue"] .p-main span:not([class]),
    html[data-theme="blue"] .p-main p:not([class]),
    html[data-theme="blue"] .p-main div:not([class]) {
        color: inherit !important;
    }

    /* Inline hardcoded light colors */
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"],
    html[data-theme="blue"] .p-main *[style*="color:#fafafa"],
    html[data-theme="blue"] .p-main *[style*="color: #fafafa"],
    html[data-theme="blue"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="blue"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="blue"] .p-main *[style*="color: white"],
    html[data-theme="blue"] .p-main *[style*="color:white"],
    html[data-theme="blue"] .p-main *[style*="color:#fff"],
    html[data-theme="blue"] .p-main *[style*="color: #fff"],
    html[data-theme="blue"] .p-main *[style*="color:#ffffff"],
    html[data-theme="blue"] .p-main *[style*="color: #ffffff"] {
        color: #0c1e3d !important;
    }

    /* ═══ DARK THEMES — FORCE LIGHT TEXT ═══ */
    html[data-theme="default"] .p-main,
    html[data-theme="dark"] .p-main {
        color: #fafafa !important;
    }

    html[data-theme="default"] .p-main *[style*="color:#0c1e3d"],
    html[data-theme="default"] .p-main *[style*="color: #0c1e3d"],
    html[data-theme="default"] .p-main *[style*="color:#1a1a1f"],
    html[data-theme="default"] .p-main *[style*="color: #1a1a1f"],
    html[data-theme="default"] .p-main *[style*="color:#000"],
    html[data-theme="default"] .p-main *[style*="color: black"],
    html[data-theme="default"] .p-main *[style*="color:black"],
    html[data-theme="dark"] .p-main *[style*="color:#0c1e3d"],
    html[data-theme="dark"] .p-main *[style*="color: #0c1e3d"],
    html[data-theme="dark"] .p-main *[style*="color:#1a1a1f"],
    html[data-theme="dark"] .p-main *[style*="color: #1a1a1f"],
    html[data-theme="dark"] .p-main *[style*="color:#000"],
    html[data-theme="dark"] .p-main *[style*="color: black"],
    html[data-theme="dark"] .p-main *[style*="color:black"] {
        color: #fafafa !important;
    }

    /* ═══ SPECIFIC TO PAYMENTS — EXTRA FORCE ═══ */

    /* Index page */
    .pp-title,
    .pp-sub,
    .pp-card-title,
    .pp-card-sub,
    .pp-card-amount,
    .pp-card-meta,
    .pp-stat-value,
    .pp-stat-label,
    .pp-empty-title,
    .pp-empty-text {
        /* Force via theme vars below */
    }

    html[data-theme="light"] .pp-title,
    html[data-theme="blue"] .pp-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-sub,
    html[data-theme="blue"] .pp-sub {
        color: #64748b !important;
    }
    html[data-theme="light"] .pp-card-title,
    html[data-theme="blue"] .pp-card-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-card-sub,
    html[data-theme="blue"] .pp-card-sub {
        color: #64748b !important;
    }
    html[data-theme="light"] .pp-card-amount,
    html[data-theme="blue"] .pp-card-amount {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-card-meta,
    html[data-theme="blue"] .pp-card-meta {
        color: #64748b !important;
    }
    html[data-theme="light"] .pp-stat-value,
    html[data-theme="blue"] .pp-stat-value {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-stat-label,
    html[data-theme="blue"] .pp-stat-label {
        color: #64748b !important;
    }

    /* Show page */
    html[data-theme="light"] .page-title,
    html[data-theme="blue"] .page-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .page-sub,
    html[data-theme="blue"] .page-sub {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-card-title,
    html[data-theme="blue"] .pay-card-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-row span,
    html[data-theme="blue"] .pay-row span {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-row strong,
    html[data-theme="blue"] .pay-row strong {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-tl-label,
    html[data-theme="blue"] .pay-tl-label {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-tl-time,
    html[data-theme="blue"] .pay-tl-time {
        color: #64748b !important;
    }

    /* Create page */
    html[data-theme="light"] .pay-header-title,
    html[data-theme="blue"] .pay-header-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-header-subtitle,
    html[data-theme="blue"] .pay-header-subtitle {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-section-title,
    html[data-theme="blue"] .pay-section-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-section-desc,
    html[data-theme="blue"] .pay-section-desc {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-balance-label,
    html[data-theme="blue"] .pay-balance-label {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-balance-value,
    html[data-theme="blue"] .pay-balance-value {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-balance-meta-label,
    html[data-theme="blue"] .pay-balance-meta-label {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-balance-meta-value,
    html[data-theme="blue"] .pay-balance-meta-value {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-method-name,
    html[data-theme="blue"] .pay-method-name {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-method-desc,
    html[data-theme="blue"] .pay-method-desc {
        color: #64748b !important;
    }

    /* ═══ FORCE LIGHT THEME TEXT VISIBLE — SPECIFIC SPANS/DIVS ═══ */
    html[data-theme="light"] .pay-form,
    html[data-theme="blue"] .pay-form {
        color: #0c1e3d !important;
    }

    /* Any element inside form — default dark text sa light themes */
    html[data-theme="light"] .pay-form *:not([style*="color"]),
    html[data-theme="blue"] .pay-form *:not([style*="color"]) {
        color: inherit !important;
    }
    /* ════════════════════════════════════════════════════════
       PAYMENTS — EXTRA TEXT VISIBILITY FIX
       ════════════════════════════════════════════════════════ */

    /* ═══ LIGHT THEMES — force dark text ═══ */
    html[data-theme="light"] .pp-title,
    html[data-theme="light"] .page-title,
    html[data-theme="blue"] .pp-title,
    html[data-theme="blue"] .page-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-sub,
    html[data-theme="light"] .page-sub,
    html[data-theme="blue"] .pp-sub,
    html[data-theme="blue"] .page-sub {
        color: #64748b !important;
    }

    /* Index — card text */
    html[data-theme="light"] .pp-card,
    html[data-theme="blue"] .pp-card {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-card-title,
    html[data-theme="blue"] .pp-card-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-card-sub,
    html[data-theme="blue"] .pp-card-sub {
        color: #64748b !important;
    }
    html[data-theme="light"] .pp-card-amount,
    html[data-theme="blue"] .pp-card-amount {
        color: var(--t-accent) !important;
    }
    html[data-theme="light"] .pp-card-meta,
    html[data-theme="blue"] .pp-card-meta {
        color: #64748b !important;
    }

    /* Stats */
    html[data-theme="light"] .pp-stat-value,
    html[data-theme="blue"] .pp-stat-value {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-stat-label,
    html[data-theme="blue"] .pp-stat-label {
        color: #64748b !important;
    }

    /* Method badge */
    html[data-theme="light"] .pp-method-badge,
    html[data-theme="blue"] .pp-method-badge {
        /* Keep colored */
    }

    /* Show page — all text */
    html[data-theme="light"] .pay-card,
    html[data-theme="blue"] .pay-card {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-card-title,
    html[data-theme="blue"] .pay-card-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-row,
    html[data-theme="blue"] .pay-row {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-row span,
    html[data-theme="blue"] .pay-row span {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-row strong,
    html[data-theme="blue"] .pay-row strong {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-hero,
    html[data-theme="blue"] .pay-hero {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-hero-sub,
    html[data-theme="blue"] .pay-hero-sub {
        color: #475569 !important;
    }
    html[data-theme="light"] .pay-hero-amount-label,
    html[data-theme="blue"] .pay-hero-amount-label {
        color: #64748b !important;
    }
    html[data-theme="light"] .pay-tl-label,
    html[data-theme="blue"] .pay-tl-label {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pay-tl-time,
    html[data-theme="blue"] .pay-tl-time {
        color: #64748b !important;
    }

    /* Verification banner */
    html[data-theme="light"] .pvfy-title,
    html[data-theme="blue"] .pvfy-title,
    html[data-theme="light"] .pvfy-desc,
    html[data-theme="blue"] .pvfy-desc {
        /* Keep semantic colors (amber/green/red) */
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
    }</style>
@endpush