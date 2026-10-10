@extends('portal.layouts.app')

@section('title', 'Payments')

@section('content')

<div class="pp-head" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div class="pp-head-left">
        <div class="pp-title">Payments</div>
        <div class="pp-sub">Imong payment history</div>
    </div>
    <a href="{{ route('portal.payments.create') }}" class="pp-pay-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Record
    </a>
</div>

{{-- STATS --}}
<div class="pp-stats stats-3col">
    <div class="pp-stat">
        <div class="pp-stat-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
        <div class="pp-stat-value">{{ $stats['total'] }}</div>
        <div class="pp-stat-label">Payments</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        <div class="pp-stat-value">&#8369;{{ number_format($stats['all_amount'], 0) }}</div>
        <div class="pp-stat-label">Total Paid</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="pp-stat-value">&#8369;{{ number_format($stats['this_month'], 0) }}</div>
        <div class="pp-stat-label">This Month</div>
    </div>
</div>

@if($payments->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="1" y="4" width="22" height="16" rx="2"/>
                <path d="M1 10h22"/>
            </svg>
        </div>
        <div class="pp-empty-title">No payments yet</div>
        <div class="pp-empty-text">Wala pa kay na-record nga payment.</div>
        <a href="{{ route('portal.payments.create') }}" class="pp-empty-btn">Record your first payment</a>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($payments as $p)
            <a href="{{ route('portal.payments.show', $p->id) }}" class="pp-card">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $p->payment_number }}</div>
                        <div class="pp-card-sub">
                            @if($p->deliveryReceipt)
                                {{ $p->deliveryReceipt->dr_number }} ·
                            @endif
                            {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                        </div>
                    </div>
                    <span class="pp-method-badge" style="--mc: {{ $p->method_color }};">
                        <span>{{ $p->method_icon }}</span>
                        {{ $p->method_label }}
                    </span>
                </div>
                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount">&#8369;{{ number_format($p->amount, 2) }}</div>
                        @if($p->reference_number)
                            <div class="pp-card-meta">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 7h16M4 12h10M4 17h16"/>
                                </svg>
                                Ref: {{ $p->reference_number }}
                            </div>
                        @endif
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:var(--t-text-3);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($payments, 'links') && $payments->hasPages())
        <div style="margin-top:20px;">{{ $payments->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    .pp-pay-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:9px 16px;
        background:linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        color:#fff; text-decoration:none;
        border-radius:11px;
        font-size:12.5px; font-weight:800;
        box-shadow:0 8px 20px -8px rgba(var(--t-accent-rgb), 0.7);
    }
    .pp-pay-btn:hover { background:linear-gradient(135deg, var(--t-accent), var(--t-accent-2)); }
    .pp-pay-btn:active { transform:scale(0.97); }

    .pp-method-badge {
        display:inline-flex; align-items:center; gap:5px;
        padding:5px 10px;
        background:color-mix(in srgb, var(--mc) 15%, transparent);
        border:1px solid color-mix(in srgb, var(--mc) 40%, transparent);
        color:var(--mc);
        border-radius:8px;
        font-size:11px; font-weight:800;
        letter-spacing:0.01em;
        flex-shrink:0;
    }

    .pp-empty {
        text-align:center; padding:44px 20px;
        background:var(--t-card);
        border:1px dashed var(--t-border-2);
        border-radius:18px;
    }
    .pp-empty-icon {
        width:64px; height:64px; margin:0 auto 14px;
        border-radius:20px;
        background:rgba(var(--t-accent-rgb), 0.1);
        color:var(--t-accent);
        display:grid; place-items:center;
    }
    .pp-empty-title { font-size:15px; font-weight:800; color:var(--t-text); margin-bottom:6px; }
    .pp-empty-text { font-size:12.5px; color:var(--t-text-3); margin-bottom:16px; }
    .pp-empty-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:11px 20px;
        background:linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        color:#fff; text-decoration:none;
        border-radius:11px;
        font-size:12.5px; font-weight:800;
    }

    /* ════════════════════════════════════════════════════════
       PAYMENTS INDEX — THEME FORCE
       ════════════════════════════════════════════════════════ */

    .pp-pay-btn {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.7) !important;
    }
    .pp-pay-btn:hover {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.8) !important;
    }

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
    .pp-empty-btn {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
    }
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
       PAYMENTS INDEX — LIST FORCE VISIBLE
       Fix dark cards sa Light/Blue themes
       ════════════════════════════════════════════════════════ */

    /* ═══ LIST CONTAINER ═══ */
    .pp-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: transparent !important;
    }

    /* ═══ CARDS — FORCE THEME BG ═══ */
    .pp-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
        color: var(--t-text) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .pp-card:hover {
        border-color: rgba(var(--t-accent-rgb), 0.35) !important;
        background: var(--t-card) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 8px 24px -10px rgba(0, 0, 0, 0.2) !important;
    }
    .pp-card:active {
        transform: translateY(0) !important;
    }

    /* ═══ CARD HEAD ═══ */
    .pp-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
    }

    .pp-card-title {
        color: var(--t-text) !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        font-family: ui-monospace, monospace !important;
        letter-spacing: -0.01em !important;
        line-height: 1.2 !important;
        margin-bottom: 4px !important;
    }

    .pp-card-sub {
        color: var(--t-text-3) !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
    }

    /* ═══ CARD BODY ═══ */
    .pp-card-body {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--t-border) !important;
    }

    .pp-card-amount {
        color: var(--t-accent) !important;
        font-size: 18px !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        font-variant-numeric: tabular-nums !important;
        line-height: 1 !important;
    }

    .pp-card-meta {
        color: var(--t-text-3) !important;
        font-size: 11.5px !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        margin-top: 6px !important;
    }
    .pp-card-meta svg {
        flex-shrink: 0 !important;
        color: var(--t-text-3) !important;
    }

    /* Arrow icon */
    .pp-card-body > svg {
        color: var(--t-text-3) !important;
        flex-shrink: 0 !important;
    }
    .pp-card:hover .pp-card-body > svg {
        color: var(--t-accent) !important;
    }

    /* ═══ LIGHT/BLUE THEMES — FORCE WHITE CARDS + DARK TEXT ═══ */
    html[data-theme="light"] .pp-card,
    html[data-theme="blue"] .pp-card {
        background: #ffffff !important;
        border-color: rgba(0, 0, 0, 0.08) !important;
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-card:hover,
    html[data-theme="blue"] .pp-card:hover {
        border-color: var(--t-accent) !important;
        box-shadow: 0 8px 24px -10px rgba(var(--t-accent-rgb), 0.25) !important;
    }
    html[data-theme="light"] .pp-card-title,
    html[data-theme="blue"] .pp-card-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-card-sub,
    html[data-theme="blue"] .pp-card-sub {
        color: #64748b !important;
    }
    html[data-theme="light"] .pp-card-body,
    html[data-theme="blue"] .pp-card-body {
        border-top-color: rgba(0, 0, 0, 0.06) !important;
    }
    html[data-theme="light"] .pp-card-amount,
    html[data-theme="blue"] .pp-card-amount {
        color: var(--t-accent) !important;
    }
    html[data-theme="light"] .pp-card-meta,
    html[data-theme="blue"] .pp-card-meta {
        color: #64748b !important;
    }
    html[data-theme="light"] .pp-card-meta svg,
    html[data-theme="blue"] .pp-card-meta svg {
        color: #94a3b8 !important;
    }
    html[data-theme="light"] .pp-card-body > svg,
    html[data-theme="blue"] .pp-card-body > svg {
        color: #94a3b8 !important;
    }
    html[data-theme="light"] .pp-card:hover .pp-card-body > svg,
    html[data-theme="blue"] .pp-card:hover .pp-card-body > svg {
        color: var(--t-accent) !important;
    }

    /* ═══ DARK THEMES — keep dark bg + light text ═══ */
    html[data-theme="default"] .pp-card,
    html[data-theme="dark"] .pp-card {
        background: var(--t-card) !important;
        color: #fafafa !important;
    }
    html[data-theme="default"] .pp-card-title,
    html[data-theme="dark"] .pp-card-title {
        color: #fafafa !important;
    }
    html[data-theme="default"] .pp-card-sub,
    html[data-theme="dark"] .pp-card-sub {
        color: #a1a1aa !important;
    }

    /* ═══ STAT CARDS — same fix ═══ */
    .pp-stat {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
        color: var(--t-text) !important;
    }
    html[data-theme="light"] .pp-stat,
    html[data-theme="blue"] .pp-stat {
        background: #ffffff !important;
        border-color: rgba(0, 0, 0, 0.08) !important;
    }
    html[data-theme="light"] .pp-stat-value,
    html[data-theme="blue"] .pp-stat-value {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-stat-label,
    html[data-theme="blue"] .pp-stat-label {
        color: #64748b !important;
    }

    /* ═══ PAGE HEAD ═══ */
    html[data-theme="light"] .pp-title,
    html[data-theme="blue"] .pp-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-sub,
    html[data-theme="blue"] .pp-sub {
        color: #64748b !important;
    }

    /* ═══ METHOD BADGE — visible sa tanan themes ═══ */
    .pp-method-badge {
        padding: 5px 10px !important;
        border-radius: 8px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        letter-spacing: 0.01em !important;
        flex-shrink: 0 !important;
        /* color-mix naay fallback */
        background: color-mix(in srgb, var(--mc, #3b82f6) 15%, transparent) !important;
        border: 1px solid color-mix(in srgb, var(--mc, #3b82f6) 40%, transparent) !important;
        color: var(--mc, #3b82f6) !important;
    }
    html[data-theme="light"] .pp-method-badge,
    html[data-theme="blue"] .pp-method-badge {
        /* Keep original colored (method color) */
    }

    /* ═══ EMPTY STATE ═══ */
    html[data-theme="light"] .pp-empty,
    html[data-theme="blue"] .pp-empty {
        background: #ffffff !important;
        border-color: rgba(0, 0, 0, 0.08) !important;
    }
    html[data-theme="light"] .pp-empty-title,
    html[data-theme="blue"] .pp-empty-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .pp-empty-text,
    html[data-theme="blue"] .pp-empty-text {
        color: #64748b !important;
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