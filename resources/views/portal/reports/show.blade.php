@extends('portal.layouts.app')

@section('title', $report->report_number)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.reports.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="page-title">{{ $report->report_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} - {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

@php
    $statusColor = match($report->status) {
        'paid' => '#22c55e',
        'partial' => '#f59e0b',
        'verified' => '#3b82f6',
        default => 'var(--t-text-3)',
    };
    $paidPercent = $report->amount_due > 0 ? min(100, ($report->amount_paid / $report->amount_due) * 100) : 0;
@endphp

{{-- HERO --}}
<div class="sr-hero" style="--sc: {{ $statusColor }};">
    <div class="sr-hero-icon" style="background:{{ $statusColor }}22;color:{{ $statusColor }};">
        @if($report->status === 'paid')
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        @elseif($report->status === 'partial')
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        @else
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        @endif
    </div>
    <div class="sr-hero-text">
        <div class="sr-hero-label">{{ ucfirst($report->status) }}</div>
        <div class="sr-hero-sub">Balance: &#8369;{{ number_format($report->balance, 2) }}</div>
    </div>
    <div class="sr-hero-amount">
        <div class="sr-hero-amount-label">Amount Due</div>
        <div class="sr-hero-amount-value">&#8369;{{ number_format($report->amount_due, 0) }}</div>
    </div>
</div>

{{-- PROGRESS + SUMMARY --}}
<div class="sr-card">
    <div class="sr-progress-wrap">
        <div class="sr-progress-header">
            <span style="color:{{ $statusColor }};font-weight:800;">{{ number_format($paidPercent, 1) }}% paid</span>
            <span style="color:var(--t-text-3);">&#8369;{{ number_format($report->amount_paid, 2) }} of &#8369;{{ number_format($report->amount_due, 2) }}</span>
        </div>
        <div class="sr-progress">
            <div class="sr-progress-bar" style="width:{{ $paidPercent }}%;background:{{ $statusColor }};"></div>
        </div>
    </div>

    <div class="sr-rows">
        <div class="sr-row">
            <span>Total Sales</span>
            <strong>&#8369;{{ number_format($report->total_sales, 2) }}</strong>
        </div>
        <div class="sr-row">
            <span>Amount Due</span>
            <strong>&#8369;{{ number_format($report->amount_due, 2) }}</strong>
        </div>
        <div class="sr-row">
            <span>Amount Paid</span>
            <strong style="color:#22c55e;">- &#8369;{{ number_format($report->amount_paid, 2) }}</strong>
        </div>
        <div class="sr-row sr-row-total">
            <span>Balance</span>
            <strong style="color:{{ $report->balance > 0 ? '#f59e0b' : '#22c55e' }};font-size:16px;">&#8369;{{ number_format($report->balance, 2) }}</strong>
        </div>
    </div>
</div>

{{-- LINKED DELIVERY --}}
@if($report->deliveryReceipt)
<div class="sr-card">
    <div class="sr-card-head">
        <div class="sr-card-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div>
            <div class="sr-card-title">Linked Delivery</div>
            <div class="sr-card-sub">{{ $report->deliveryReceipt->dr_number }}</div>
        </div>
        <a href="{{ route('portal.deliveries.show', $report->deliveryReceipt->id) }}" class="sr-link-btn">
            View →
        </a>
    </div>
</div>
@endif

{{-- PAYMENT HISTORY --}}
@if($linkedPayments && $linkedPayments->count() > 0)
<div class="sr-card">
    <div class="sr-card-head">
        <div class="sr-card-icon blue">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div>
            <div class="sr-card-title">Payment History</div>
            <div class="sr-card-sub">{{ $linkedPayments->count() }} payment(s)</div>
        </div>
    </div>

    <div class="sr-payments">
        @foreach($linkedPayments as $p)
            <div class="sr-payment-row">
                <div class="sr-payment-icon" style="background:{{ $p->method_color }}22;color:{{ $p->method_color }};">
                    {{ $p->method_icon }}
                </div>
                <div class="sr-payment-info">
                    <div class="sr-payment-title">{{ $p->method_label }}</div>
                    <div class="sr-payment-meta">
                        {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                        @if($p->reference_number) · Ref: {{ $p->reference_number }} @endif
                    </div>
                </div>
                <div class="sr-payment-amount">&#8369;{{ number_format($p->amount, 2) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- ITEMS --}}
@if($report->items && $report->items->count() > 0)
<div class="sr-card">
    <div class="sr-card-head">
        <div class="sr-card-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div>
            <div class="sr-card-title">Items</div>
            <div class="sr-card-sub">{{ $report->items->count() }} product(s)</div>
        </div>
    </div>

    <div class="sr-items">
        @foreach($report->items as $item)
            <div class="sr-item-row">
                <div class="sr-item-name">{{ $item->product->name ?? '—' }}</div>
                <div class="sr-item-qty">{{ $item->quantity_sold }} × &#8369;{{ number_format($item->unit_price, 2) }}</div>
                <div class="sr-item-total">&#8369;{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- NOTES --}}
@if($report->notes)
<div class="sr-card">
    <div class="sr-card-head">
        <div class="sr-card-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 7h16M4 12h16M4 17h10"/>
            </svg>
        </div>
        <div>
            <div class="sr-card-title">Notes</div>
        </div>
    </div>
    <div style="font-size:12.5px;color:var(--t-text-2);line-height:1.6;">{{ $report->notes }}</div>
</div>
@endif

{{-- PAY NOW CTA --}}
@if($report->balance > 0)
    <div class="sr-pay-section">
        <div class="sr-pay-header">
            <div class="sr-pay-label">Bayad sa balance</div>
            <div class="sr-pay-amount">&#8369;{{ number_format($report->balance, 2) }}</div>
        </div>

        <div class="sr-pay-buttons">
            <a href="{{ route('portal.payments.create', [
                    'delivery_receipt_id' => $report->delivery_receipt_id,
                    'amount' => number_format($report->balance, 2, '.', ''),
                    'report_id' => $report->id,
                ]) }}" class="sr-pay-btn-full">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                Bayad Full — &#8369;{{ number_format($report->balance, 2) }}
            </a>

            <a href="{{ route('portal.payments.create', [
                    'delivery_receipt_id' => $report->delivery_receipt_id,
                    'amount' => number_format($report->balance / 2, 2, '.', ''),
                    'report_id' => $report->id,
                ]) }}" class="sr-pay-btn-half">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 2v20"/>
                </svg>
                Kalahati — &#8369;{{ number_format($report->balance / 2, 2) }}
            </a>

            <a href="{{ route('portal.payments.create', [
                    'delivery_receipt_id' => $report->delivery_receipt_id,
                    'report_id' => $report->id,
                ]) }}" class="sr-pay-btn-custom">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Custom Amount
            </a>
        </div>

        <div class="sr-pay-note">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4M12 8h.01"/>
            </svg>
            Pwede ka mag-bayad ug <strong>partial</strong> o <strong>full</strong>. Ang sobra o kulang dili problema.
        </div>
    </div>
@endif


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
        background:rgba(255,255,255,0.05); color:var(--t-text-2);
        display:grid; place-items:center; text-decoration:none;
    }
    .back-btn:hover { background:rgba(255,255,255,0.1); color:var(--t-text); }
    .page-title { font-size:17px; font-weight:800; color:var(--t-text); }
    .page-sub { font-size:11.5px; color:var(--t-text-3); margin-top:2px; }

    .sr-hero {
        display:flex; align-items:center; gap:14px;
        padding:16px;
        background:linear-gradient(135deg, color-mix(in srgb, var(--sc) 12%, transparent), rgba(21,18,15,0.6));
        border:1px solid color-mix(in srgb, var(--sc) 35%, transparent);
        border-radius:18px;
        margin-bottom:14px;
    }
    .sr-hero-icon {
        width:48px; height:48px; border-radius:14px;
        display:grid; place-items:center; flex-shrink:0;
    }
    .sr-hero-text { flex:1; min-width:0; }
    .sr-hero-label { font-size:15px; font-weight:800; color:var(--sc); margin-bottom:3px; }
    .sr-hero-sub { font-size:11.5px; color:var(--t-text-3); }
    .sr-hero-amount { text-align:right; flex-shrink:0; }
    .sr-hero-amount-label {
        font-size:9.5px; color:var(--t-text-3); text-transform:uppercase;
        letter-spacing:0.1em; font-weight:700; margin-bottom:3px;
    }
    .sr-hero-amount-value { font-size:20px; font-weight:800; color:var(--t-accent); font-variant-numeric:tabular-nums; }

    .sr-card {
        background:var(--t-card);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:16px;
        padding:16px;
        margin-bottom:14px;
    }
    .sr-card-head { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
    .sr-card-icon {
        width:36px; height:36px; border-radius:10px;
        background:rgba(201,169,97,0.15); color:var(--t-accent);
        display:grid; place-items:center; flex-shrink:0;
    }
    .sr-card-icon.blue { background:rgba(59,130,246,0.15); color:#3b82f6; }
    .sr-card-title { font-size:13.5px; font-weight:800; color:var(--t-text); }
    .sr-card-sub { font-size:11px; color:var(--t-text-3); margin-top:2px; }
    .sr-link-btn {
        margin-left:auto; padding:6px 12px;
        background:rgba(201,169,97,0.15); color:var(--t-accent);
        border:1px solid rgba(201,169,97,0.3); border-radius:8px;
        font-size:11px; font-weight:800; text-decoration:none;
        white-space:nowrap;
    }
    .sr-link-btn:hover { background:rgba(201,169,97,0.25); }

    .sr-progress-wrap { margin-bottom:14px; }
    .sr-progress-header {
        display:flex; justify-content:space-between; align-items:center;
        font-size:11.5px; margin-bottom:8px;
    }
    .sr-progress { height:6px; background:rgba(255,255,255,0.06); border-radius:3px; overflow:hidden; }
    .sr-progress-bar { height:100%; border-radius:3px; transition:width 0.4s; }

    .sr-rows { display:flex; flex-direction:column; }
    .sr-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:9px 0; font-size:12.5px;
        border-bottom:1px solid rgba(255,255,255,0.04);
    }
    .sr-row:last-child { border-bottom:none; }
    .sr-row span { color:var(--t-text-3); }
    .sr-row strong { color:var(--t-text); font-weight:700; font-variant-numeric:tabular-nums; }
    .sr-row-total {
        padding-top:12px;
        border-top:1px solid rgba(201,169,97,0.25) !important;
        border-bottom:none !important;
    }

    .sr-payments { display:flex; flex-direction:column; gap:2px; }
    .sr-payment-row {
        display:flex; align-items:center; gap:11px;
        padding:10px 0;
        border-bottom:1px solid rgba(255,255,255,0.04);
    }
    .sr-payment-row:last-child { border-bottom:none; }
    .sr-payment-icon {
        width:36px; height:36px; border-radius:10px;
        display:grid; place-items:center; font-size:16px; flex-shrink:0;
    }
    .sr-payment-info { flex:1; min-width:0; }
    .sr-payment-title { font-size:12.5px; font-weight:700; color:var(--t-text); }
    .sr-payment-meta { font-size:10.5px; color:var(--t-text-3); margin-top:2px; }
    .sr-payment-amount {
        font-size:13px; font-weight:800; color:var(--t-accent);
        font-variant-numeric:tabular-nums; flex-shrink:0;
    }

    .sr-items { display:flex; flex-direction:column; }
    .sr-item-row {
        display:flex; align-items:center; gap:10px;
        padding:9px 0;
        border-bottom:1px solid rgba(255,255,255,0.04);
        font-size:12px;
    }
    .sr-item-row:last-child { border-bottom:none; }
    .sr-item-name { flex:1; min-width:0; color:var(--t-text); font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .sr-item-qty { color:var(--t-text-3); font-size:11px; flex-shrink:0; }
    .sr-item-total { color:var(--t-accent); font-weight:800; flex-shrink:0; font-variant-numeric:tabular-nums; }

    .sr-pay-cta {
        display:flex; align-items:center; justify-content:center; gap:8px;
        width:100%; min-height:52px;
        background:linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        color:#fff; font-size:14.5px; font-weight:800;
        border-radius:14px; text-decoration:none;
        box-shadow:0 12px 28px -10px rgba(201,169,97,0.7);
        letter-spacing:0.02em;
        margin-bottom:14px;
    }
    .sr-pay-cta:hover { background:linear-gradient(135deg, var(--t-accent), #9a6b3f); }
    .sr-pay-cta:active { transform:scale(0.98); }

        /* ========== PAY SECTION ========== */
        .sr-pay-section {
            background: linear-gradient(165deg, rgba(201,169,97,0.08), rgba(138,95,54,0.04));
            border: 1px solid rgba(201,169,97,0.25);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .sr-pay-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(201,169,97,0.15);
        }
        .sr-pay-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--t-text-3);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .sr-pay-amount {
            font-size: 20px;
            font-weight: 800;
            color: var(--t-accent);
            font-variant-numeric: tabular-nums;
        }

        .sr-pay-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }
        .sr-pay-btn-full,
        .sr-pay-btn-half,
        .sr-pay-btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 12.5px;
            font-weight: 800;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.15s ease;
            -webkit-tap-highlight-color: transparent;
            border: 1px solid;
            cursor: pointer;
        }
        .sr-pay-btn-full svg,
        .sr-pay-btn-half svg,
        .sr-pay-btn-custom svg { flex-shrink: 0; }

        /* FULL — green */
        .sr-pay-btn-full {
            background: linear-gradient(135deg, rgba(34,197,94,0.2), rgba(22,163,74,0.15));
            border-color: rgba(34,197,94,0.4);
            color: #22c55e;
            grid-column: 1 / -1;
        }
        .sr-pay-btn-full:hover {
            background: linear-gradient(135deg, rgba(34,197,94,0.3), rgba(22,163,74,0.2));
            border-color: #22c55e;
            transform: translateY(-1px);
        }

        /* HALF — blue */
        .sr-pay-btn-half {
            background: rgba(59,130,246,0.15);
            border-color: rgba(59,130,246,0.4);
            color: #60a5fa;
        }
        .sr-pay-btn-half:hover {
            background: rgba(59,130,246,0.25);
            border-color: #3b82f6;
            transform: translateY(-1px);
        }

        /* CUSTOM — gold */
        .sr-pay-btn-custom {
            background: linear-gradient(135deg, rgba(201,169,97,0.2), rgba(138,95,54,0.15));
            border-color: rgba(201,169,97,0.4);
            color: var(--t-accent);
        }
        .sr-pay-btn-custom:hover {
            background: linear-gradient(135deg, rgba(201,169,97,0.3), rgba(138,95,54,0.25));
            border-color: var(--t-accent);
            transform: translateY(-1px);
        }

        .sr-pay-btn-full:active,
        .sr-pay-btn-half:active,
        .sr-pay-btn-custom:active { transform: scale(0.97); }

        /* NOTE */
        .sr-pay-note {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            padding: 9px 11px;
            background: rgba(59,130,246,0.08);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 9px;
            font-size: 10.5px;
            color: #93bff5;
            line-height: 1.5;
        }
        .sr-pay-note svg { flex-shrink: 0; margin-top: 2px; color: #3b82f6; }
        .sr-pay-note strong { color: #c9d9f5; font-weight: 800; }

        @media (max-width: 400px) {
            .sr-pay-buttons { grid-template-columns: 1fr; }
            .sr-pay-btn-full { grid-column: 1; }
        }
    
    /* ════════════════════════════════════════════════════════
       REPORTS — THEME FORCE OVERRIDE
       Fix visibility sa tanan themes (esp. Light/Blue)
       ════════════════════════════════════════════════════════ */

    /* ── Page header ── */
    .pp-title,
    .page-title,
    .rp-title,
    h1 {
        color: var(--t-text) !important;
    }
    .pp-sub,
    .page-sub,
    .rp-sub {
        color: var(--t-text-3) !important;
    }

    /* ── Cards ── */
    .rp-card,
    .pp-card,
    .pay-card,
    .rp-section,
    .report-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
        color: var(--t-text) !important;
    }

    /* ── Row labels (Total Sales, Amount Due, Amount Paid, Balance) ── */
    .rp-row-label,
    .rp-info-label,
    .rp-summary-label,
    .rp-detail-label,
    .summary-label,
    .detail-label,
    .rp-stat-label {
        color: var(--t-text-2) !important;
    }

    /* ── Row values ── */
    .rp-row-value,
    .rp-info-value,
    .rp-summary-value,
    .rp-detail-value,
    .summary-value,
    .detail-value,
    .rp-stat-value {
        color: var(--t-text) !important;
    }

    /* ── Item names (Coffee, etc.) ── */
    .rp-item-name,
    .rp-product-name,
    .item-name {
        color: var(--t-text) !important;
    }
    .rp-item-meta,
    .rp-product-sku {
        color: var(--t-text-3) !important;
    }

    /* ── "Cash", "Paid", "Pending" labels ── */
    .rp-payment-method,
    .rp-method-label,
    .payment-method {
        color: var(--t-text) !important;
    }

    /* ── Notes text ── */
    .rp-notes,
    .rp-note-text,
    .notes-text {
        color: var(--t-text-2) !important;
    }

    /* ── All generic text inside cards ── */
    .rp-card *,
    .pay-card *,
    .rp-section * {
        /* Safety — any text na dili ma-reach sa specific rules */
    }

    /* ── Notes label/heading ── */
    .rp-card-head-title,
    .rp-section-title {
        color: var(--t-text) !important;
    }

    /* ── Amounts (₱200.00) ── */
    .rp-amount,
    .rp-card-amount,
    .rp-hero-amount-value,
    .rp-total-value {
        color: var(--t-accent) !important;
    }

    /* ── Green success text ── */
    .rp-text-success,
    .text-success {
        color: #22c55e !important;
    }
    .rp-text-danger,
    .text-danger {
        color: #ef4444 !important;
    }

    /* ── Cards sa light themes — extra white bg + dark text ── */
    html[data-theme="light"] .rp-card,
    html[data-theme="light"] .pay-card,
    html[data-theme="blue"] .rp-card,
    html[data-theme="blue"] .pay-card {
        background: #ffffff !important;
    }
    html[data-theme="light"] .rp-card *,
    html[data-theme="blue"] .rp-card *,
    html[data-theme="light"] .pay-card *,
    html[data-theme="blue"] .pay-card * {
        --t-text: #0c1e3d;
        --t-text-2: #334155;
        --t-text-3: #64748b;
    }

    /* ── Icons ── */
    .rp-card-icon,
    .rp-icon,
    .rp-section-icon {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
        color: var(--t-accent) !important;
    }

    /* ── Progress bars ── */
    .rp-progress-track,
    .rp-progress-bar {
        background: var(--t-input) !important;
        border-color: var(--t-border) !important;
    }
    .rp-progress-fill {
        background: linear-gradient(90deg, var(--t-accent), var(--t-accent-2)) !important;
    }
    .rp-progress-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a) !important;
    }

    /* ── Status badges — force visible ── */
    .rp-badge-paid,
    .rp-status-paid {
        background: rgba(34, 197, 94, 0.15) !important;
        color: #16a34a !important;
        border-color: rgba(34, 197, 94, 0.35) !important;
    }
    .rp-badge-pending,
    .rp-status-pending {
        background: rgba(245, 158, 11, 0.15) !important;
        color: #d97706 !important;
        border-color: rgba(245, 158, 11, 0.35) !important;
    }
    .rp-badge-partial {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #2563eb !important;
        border-color: rgba(59, 130, 246, 0.35) !important;
    }

    /* ── Empty state ── */
    .rp-empty,
    .rp-empty-state {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .rp-empty-title { color: var(--t-text) !important; }
    .rp-empty-text { color: var(--t-text-3) !important; }

    /* ── Summary rows container ── */
    .rp-summary-rows,
    .rp-info-rows,
    .rp-detail-rows {
        color: var(--t-text) !important;
    }
    .rp-summary-row,
    .rp-info-row,
    .rp-detail-row {
        border-color: var(--t-border) !important;
    }

    /* ── Timeline items ── */
    .rp-timeline,
    .pay-timeline {
        color: var(--t-text) !important;
    }
    .rp-timeline-label,
    .pay-tl-label {
        color: var(--t-text) !important;
    }
    .rp-timeline-time,
    .pay-tl-time {
        color: var(--t-text-3) !important;
    }

    /* ═══ ULTIMATE SAFETY NET ═══ */
    /* Any span/p/div with light text sa light themes → dark */
    html[data-theme="light"] .p-main span:not([class*="badge"]):not([class*="status"]):not([class*="tag"]),
    html[data-theme="light"] .p-main p:not([class*="badge"]):not([class*="status"]):not([class*="tag"]),
    html[data-theme="blue"] .p-main span:not([class*="badge"]):not([class*="status"]):not([class*="tag"]),
    html[data-theme="blue"] .p-main p:not([class*="badge"]):not([class*="status"]):not([class*="tag"]) {
        color: inherit !important;
    }

    /* Force dark text sa buttons/labels sa light themes */
    html[data-theme="light"] .p-main,
    html[data-theme="blue"] .p-main {
        color: #0c1e3d !important;
    }

    /* Cards sa light themes — white bg + dark text override */
    html[data-theme="light"] .p-main > div:not([class*="modal"]),
    html[data-theme="blue"] .p-main > div:not([class*="modal"]) {
        color: #0c1e3d !important;
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