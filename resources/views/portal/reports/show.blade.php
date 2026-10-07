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
        default => '#8a8378',
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
            <span style="color:#8a8378;">&#8369;{{ number_format($report->amount_paid, 2) }} of &#8369;{{ number_format($report->amount_due, 2) }}</span>
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
    <div style="font-size:12.5px;color:#a8a5a0;line-height:1.6;">{{ $report->notes }}</div>
</div>
@endif

{{-- PAY NOW CTA --}}
@if($report->balance > 0)
    <a href="{{ route('portal.payments.create') }}" class="sr-pay-cta">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Bayad &#8369;{{ number_format($report->balance, 2) }}
    </a>
@endif

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
    .sr-hero-sub { font-size:11.5px; color:#8a8378; }
    .sr-hero-amount { text-align:right; flex-shrink:0; }
    .sr-hero-amount-label {
        font-size:9.5px; color:#8a8378; text-transform:uppercase;
        letter-spacing:0.1em; font-weight:700; margin-bottom:3px;
    }
    .sr-hero-amount-value { font-size:20px; font-weight:800; color:#c9a961; font-variant-numeric:tabular-nums; }

    .sr-card {
        background:linear-gradient(165deg, #1e1a16, #15120f);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:16px;
        padding:16px;
        margin-bottom:14px;
    }
    .sr-card-head { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
    .sr-card-icon {
        width:36px; height:36px; border-radius:10px;
        background:rgba(201,169,97,0.15); color:#c9a961;
        display:grid; place-items:center; flex-shrink:0;
    }
    .sr-card-icon.blue { background:rgba(59,130,246,0.15); color:#3b82f6; }
    .sr-card-title { font-size:13.5px; font-weight:800; color:#f5f3f0; }
    .sr-card-sub { font-size:11px; color:#8a8378; margin-top:2px; }
    .sr-link-btn {
        margin-left:auto; padding:6px 12px;
        background:rgba(201,169,97,0.15); color:#c9a961;
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
    .sr-row span { color:#8a8378; }
    .sr-row strong { color:#f5f3f0; font-weight:700; font-variant-numeric:tabular-nums; }
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
    .sr-payment-title { font-size:12.5px; font-weight:700; color:#f5f3f0; }
    .sr-payment-meta { font-size:10.5px; color:#8a8378; margin-top:2px; }
    .sr-payment-amount {
        font-size:13px; font-weight:800; color:#c9a961;
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
    .sr-item-name { flex:1; min-width:0; color:#f5f3f0; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .sr-item-qty { color:#8a8378; font-size:11px; flex-shrink:0; }
    .sr-item-total { color:#c9a961; font-weight:800; flex-shrink:0; font-variant-numeric:tabular-nums; }

    .sr-pay-cta {
        display:flex; align-items:center; justify-content:center; gap:8px;
        width:100%; min-height:52px;
        background:linear-gradient(135deg, #c9a961, #8a5f36);
        color:#fff; font-size:14.5px; font-weight:800;
        border-radius:14px; text-decoration:none;
        box-shadow:0 12px 28px -10px rgba(201,169,97,0.7);
        letter-spacing:0.02em;
        margin-bottom:14px;
    }
    .sr-pay-cta:hover { background:linear-gradient(135deg, #d4b673, #9a6b3f); }
    .sr-pay-cta:active { transform:scale(0.98); }
</style>
@endpush