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