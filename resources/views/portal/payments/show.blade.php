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
                <div style="color:#c4bdb4;font-size:12.5px;line-height:1.5;">{{ $payment->notes }}</div>
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
        background:rgba(255,255,255,0.05); color:#c4bdb4;
        display:grid; place-items:center; text-decoration:none;
    }
    .back-btn:hover { background:rgba(255,255,255,0.1); color:#f5f3f0; }
    .page-title { font-size:17px; font-weight:800; color:#f5f3f0; }
    .page-sub { font-size:11.5px; color:#8a8378; margin-top:2px; }

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
    .pay-hero-sub { font-size:11.5px; color:#8a8378; }
    .pay-hero-amount { text-align:right; flex-shrink:0; }
    .pay-hero-amount-label {
        font-size:9.5px; color:#8a8378;
        text-transform:uppercase; letter-spacing:0.1em;
        font-weight:700; margin-bottom:3px;
    }
    .pay-hero-amount-value {
        font-size:20px; font-weight:800; color:#c9a961;
        font-variant-numeric:tabular-nums;
    }

    .pay-card {
        background:linear-gradient(165deg,#1e1a16,#15120f);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:16px;
        padding:18px 16px;
        margin-bottom:14px;
    }
    .pay-card-head {
        display:flex; align-items:center; gap:12px; margin-bottom:14px;
    }
    .pay-card-icon {
        width:36px; height:36px; border-radius:10px;
        background:rgba(201,169,97,0.15); color:#c9a961;
        display:grid; place-items:center; flex-shrink:0;
    }
    .pay-card-icon.blue { background:rgba(59,130,246,0.15); color:#3b82f6; }
    .pay-card-title { font-size:13.5px; font-weight:800; color:#f5f3f0; }

    .pay-rows { display:flex; flex-direction:column; }
    .pay-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:10px 0;
        border-bottom:1px solid rgba(255,255,255,0.05);
        font-size:12.5px;
    }
    .pay-row:last-child { border-bottom:none; }
    .pay-row span { color:#8a8378; }
    .pay-row strong { color:#f5f3f0; font-weight:700; }

    .pay-timeline { display:flex; flex-direction:column; gap:14px; position:relative; }
    .pay-tl-item { display:flex; gap:12px; align-items:flex-start; }
    .pay-tl-dot {
        width:10px; height:10px; border-radius:50%;
        background:rgba(201,169,97,0.4);
        margin-top:4px;
        flex-shrink:0;
    }
    .pay-tl-label { font-size:12.5px; font-weight:700; color:#f5f3f0; }
    .pay-tl-time { font-size:11px; color:#8a8378; margin-top:2px; }
</style>
@endpush