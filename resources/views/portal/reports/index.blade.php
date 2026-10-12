@extends('portal.layouts.app')

@section('title', 'Sales Reports')

@section('content')

<div class="pp-head" style="margin-bottom:16px;">
    <div class="pp-head-left">
        <div class="pp-title">Sales Reports</div>
        <div class="pp-sub">Imong sales ug payment status</div>
    </div>
</div>

@php
    $totalPaid = $reports->sum('amount_paid');
    $totalBalance = $reports->sum('balance');
@endphp

<div class="pp-stats stats-3col" style="margin-bottom:16px;">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $stats['total'] }}</div>
        <div class="pp-stat-label">Reports</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($totalPaid, 0) }}</div>
        <div class="pp-stat-label">Total Paid</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($totalBalance, 0) }}</div>
        <div class="pp-stat-label">Balance</div>
    </div>
</div>

@if($reports->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </div>
        <div class="pp-empty-title">Wala pay reports</div>
        <div class="pp-empty-text">Wala pa kay sales report.</div>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($reports as $r)
            @php
                $paidPercent = $r->amount_due > 0 ? min(100, ($r->amount_paid / $r->amount_due) * 100) : 0;
                $statusColor = match($r->status) {
                    'paid' => '#22c55e',
                    'partial' => '#f59e0b',
                    'verified' => '#3b82f6',
                    default => 'var(--t-text-3)',
                };
            @endphp
            <a href="{{ route('portal.reports.show', $r->id) }}" class="pp-card">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $r->report_number }}</div>
                        <div class="pp-card-sub">
                            {{ \Carbon\Carbon::parse($r->period_from)->format('M d') }} -
                            {{ \Carbon\Carbon::parse($r->period_to)->format('M d, Y') }}
                            @if($r->deliveryReceipt)
                                · {{ $r->deliveryReceipt->dr_number }}
                            @endif
                        </div>
                    </div>
                    <span class="pp-badge" style="background:{{ $statusColor }}22;color:{{ $statusColor }};border:1px solid {{ $statusColor }}55;">
                        {{ ucfirst($r->status) }}
                    </span>
                </div>

                <div class="pp-card-body">
                    <div style="flex:1;">
                        <div class="pp-card-amount">&#8369;{{ number_format($r->amount_due, 2) }}</div>
                        <div class="pp-progress" style="margin-top:8px;">
                            <div class="pp-progress-bar" style="width:{{ $paidPercent }}%;background:{{ $statusColor }};"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:10.5px;color:var(--t-text-3);margin-top:4px;">
                            <span>Paid: &#8369;{{ number_format($r->amount_paid, 2) }}</span>
                            <span>Bal: &#8369;{{ number_format($r->balance, 2) }}</span>
                        </div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:var(--t-text-3);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($reports, 'links') && $reports->hasPages())
        <div style="margin-top:20px;">{{ $reports->withQueryString()->links() }}</div>
    @endif
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
    .pp-head-left .pp-title { font-size:22px; font-weight:800; color:var(--t-text); }
    .pp-head-left .pp-sub { font-size:12.5px; color:var(--t-text-3); margin-top:2px; }

    /* .pp-stats → managed by layout */
    .pp-stat {
        background:var(--t-card);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:14px;
        padding:14px 12px;
    }
    .pp-stat-icon {
        width:34px; height:34px; border-radius:10px;
        background:rgba(201,169,97,0.15); color:var(--t-accent);
        display:grid; place-items:center; margin-bottom:10px;
    }
    .pp-stat-icon.green { background:rgba(34,197,94,0.15); color:#22c55e; }
    .pp-stat-icon.amber { background:rgba(245,158,11,0.15); color:#f59e0b; }
    .pp-stat-value { font-size:18px; font-weight:800; color:var(--t-text); font-variant-numeric:tabular-nums; }
    .pp-stat-label { font-size:10.5px; color:var(--t-text-3); text-transform:uppercase; letter-spacing:0.08em; font-weight:700; margin-top:2px; }

    .pp-list { display:flex; flex-direction:column; gap:10px; }
    .pp-card {
        display:block; padding:16px;
        background:var(--t-card);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:16px;
        text-decoration:none; color:inherit;
        transition:border-color 0.15s;
    }
    .pp-card:hover { border-color:rgba(201,169,97,0.3); }
    .pp-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:12px; }
    .pp-card-title { font-size:13.5px; font-weight:800; color:var(--t-text); }
    .pp-card-sub { font-size:11px; color:var(--t-text-3); margin-top:3px; }
    .pp-badge { padding:4px 9px; border-radius:7px; font-size:10.5px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; }
    .pp-card-body { display:flex; align-items:center; gap:12px; }
    .pp-card-amount { font-size:18px; font-weight:800; color:var(--t-accent); font-variant-numeric:tabular-nums; }
    .pp-progress { width:100%; height:4px; background:rgba(255,255,255,0.06); border-radius:2px; overflow:hidden; }
    .pp-progress-bar { height:100%; border-radius:2px; transition:width 0.4s; }

    .pp-empty {
        text-align:center; padding:44px 20px;
        background:var(--t-card);
        border:1px dashed rgba(255,255,255,0.08);
        border-radius:18px;
    }
    .pp-empty-icon {
        width:64px; height:64px; margin:0 auto 14px;
        border-radius:20px; background:rgba(201,169,97,0.1); color:var(--t-accent);
        display:grid; place-items:center;
    }
    .pp-empty-title { font-size:15px; font-weight:800; color:var(--t-text); margin-bottom:6px; }
    .pp-empty-text { font-size:12.5px; color:var(--t-text-3); }

    @media (max-width: 480px) {
        /* .pp-stats → managed by layout */
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