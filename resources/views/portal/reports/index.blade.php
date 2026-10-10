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