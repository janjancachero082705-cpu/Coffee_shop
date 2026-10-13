@extends('portal.layouts.app')

@section('title', $report->report_number)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <button type="button" class="back-btn" onclick="goBack('{{ route('portal.reports.index') }}')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </button>
        <div>
            <div class="page-title">{{ $report->report_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} - {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

@php
    $statusColor = match($report->status) {
        'paid' => '#16A34A',
        'partial' => '#B45309',
        'verified' => '#2563EB',
        default => '#8D6E63',
    };
    $paidPercent = $report->amount_due > 0 ? min(100, ($report->amount_paid / $report->amount_due) * 100) : 0;
@endphp

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
        <div class="sr-hero-label" style="color:{{ $statusColor }};">{{ ucfirst($report->status) }}</div>
        <div class="sr-hero-sub">Balance: ₱{{ number_format($report->balance, 2) }}</div>
    </div>
    <div class="sr-hero-amount">
        <div class="sr-hero-amount-label">Amount Due</div>
        <div class="sr-hero-amount-value">₱{{ number_format($report->amount_due, 0) }}</div>
    </div>
</div>

<div class="sr-card">
    <div class="sr-progress-wrap">
        <div class="sr-progress-header">
            <span style="color:{{ $statusColor }};font-weight:800;">{{ number_format($paidPercent, 1) }}% paid</span>
            <span style="color:#8D6E63;">₱{{ number_format($report->amount_paid, 2) }} of ₱{{ number_format($report->amount_due, 2) }}</span>
        </div>
        <div class="sr-progress"><div class="sr-progress-bar" style="width:{{ $paidPercent }}%;background:{{ $statusColor }};"></div></div>
    </div>
    <div class="sr-rows">
        <div class="sr-row"><span>Total Sales</span><strong>₱{{ number_format($report->total_sales, 2) }}</strong></div>
        <div class="sr-row"><span>Amount Due</span><strong>₱{{ number_format($report->amount_due, 2) }}</strong></div>
        <div class="sr-row"><span>Amount Paid</span><strong style="color:#16A34A;">- ₱{{ number_format($report->amount_paid, 2) }}</strong></div>
        <div class="sr-row sr-row-total"><span>Balance</span><strong style="color:{{ $report->balance > 0 ? '#B45309' : '#16A34A' }};font-size:16px;">₱{{ number_format($report->balance, 2) }}</strong></div>
    </div>
</div>

@if($report->deliveryReceipt)
    <div class="sr-card">
        <div class="sr-card-head">
            <div class="sr-card-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
            <div><div class="sr-card-title">Linked Delivery</div><div class="sr-card-sub">{{ $report->deliveryReceipt->dr_number }}</div></div>
            <a href="{{ route('portal.deliveries.show', $report->deliveryReceipt->id) }}" class="sr-link-btn">View →</a>
        </div>
    </div>
@endif

@if($linkedPayments && $linkedPayments->count() > 0)
    <div class="sr-card">
        <div class="sr-card-head">
            <div class="sr-card-icon blue"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
            <div><div class="sr-card-title">Payment History</div><div class="sr-card-sub">{{ $linkedPayments->count() }} payment(s)</div></div>
        </div>
        <div class="sr-payments">
            @foreach($linkedPayments as $p)
                <div class="sr-payment-row">
                    <div class="sr-payment-icon" style="background:{{ $p->method_color }}22;color:{{ $p->method_color }};">{{ $p->method_icon }}</div>
                    <div class="sr-payment-info">
                        <div class="sr-payment-title">{{ $p->method_label }}</div>
                        <div class="sr-payment-meta">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}@if($p->reference_number) · Ref: {{ $p->reference_number }}@endif</div>
                    </div>
                    <div class="sr-payment-amount">₱{{ number_format($p->amount, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($report->items && $report->items->count() > 0)
    <div class="sr-card">
        <div class="sr-card-head">
            <div class="sr-card-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
            <div><div class="sr-card-title">Items</div><div class="sr-card-sub">{{ $report->items->count() }} product(s)</div></div>
        </div>
        <div class="sr-items">
            @foreach($report->items as $item)
                <div class="sr-item-row">
                    <div class="sr-item-name">{{ $item->product->name ?? '—' }}</div>
                    <div class="sr-item-qty">{{ $item->quantity_sold }} × ₱{{ number_format($item->unit_price, 2) }}</div>
                    <div class="sr-item-total">₱{{ number_format($item->subtotal, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($report->notes)
    <div class="sr-card">
        <div class="sr-card-head"><div class="sr-card-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></svg></div><div><div class="sr-card-title">Notes</div></div></div>
        <div style="font-size:12.5px;color:#4B5563;line-height:1.6;">{{ $report->notes }}</div>
    </div>
@endif

@if($report->balance > 0)
    <div class="sr-pay-section">
        <div class="sr-pay-header"><div class="sr-pay-label">Bayad sa balance</div><div class="sr-pay-amount">₱{{ number_format($report->balance, 2) }}</div></div>
        <div class="sr-pay-buttons">
            <a href="{{ route('portal.payments.create', ['delivery_receipt_id' => $report->delivery_receipt_id, 'amount' => number_format($report->balance, 2, '.', ''), 'report_id' => $report->id]) }}" class="sr-pay-btn-full"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>Bayad Full — ₱{{ number_format($report->balance, 2) }}</a>
            <a href="{{ route('portal.payments.create', ['delivery_receipt_id' => $report->delivery_receipt_id, 'amount' => number_format($report->balance / 2, 2, '.', ''), 'report_id' => $report->id]) }}" class="sr-pay-btn-half"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20"/></svg>Kalahati — ₱{{ number_format($report->balance / 2, 2) }}</a>
            <a href="{{ route('portal.payments.create', ['delivery_receipt_id' => $report->delivery_receipt_id, 'report_id' => $report->id]) }}" class="sr-pay-btn-custom"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Custom Amount</a>
        </div>
        <div class="sr-pay-note"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>Pwede ka mag-bayad ug <strong>partial</strong> o <strong>full</strong>.</div>
    </div>
@endif

@endsection

@push('styles')
<style>
/* ═══ REPORT SHOW — PREMIUM COFFEE ═══ */
.page-header { display: flex; align-items: center; margin-bottom: 16px; }
.page-header-left { display: flex; align-items: center; gap: 12px; }
.back-btn { width: 40px; height: 40px; border-radius: 12px; background: #FFFFFF; color: #8D6E63; display: grid; place-items: center; border: 1.5px solid #EFEBE9; cursor: pointer; transition: all 0.15s; }
.back-btn:hover { border-color: #D7CCC8; color: #2D1A11; transform: translateX(-2px); }
.back-btn:active { transform: scale(0.94); }
.page-title { font-size: 18px; font-weight: 800; color: #2D1A11; letter-spacing: -0.02em; font-family: ui-monospace, monospace; }
.page-sub { font-size: 11.5px; color: #8D6E63; margin-top: 2px; font-weight: 600; }

.sr-hero { display: flex; align-items: center; gap: 14px; padding: 18px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-left: 3px solid var(--sc); border-radius: 18px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.sr-hero-icon { width: 48px; height: 48px; border-radius: 14px; display: grid; place-items: center; flex-shrink: 0; }
.sr-hero-text { flex: 1; min-width: 0; }
.sr-hero-label { font-size: 15px; font-weight: 800; margin-bottom: 3px; }
.sr-hero-sub { font-size: 11.5px; color: #8D6E63; }
.sr-hero-amount { text-align: right; flex-shrink: 0; }
.sr-hero-amount-label { font-size: 9.5px; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 3px; }
.sr-hero-amount-value { font-size: 20px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; }

.sr-card { background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; padding: 18px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.sr-card-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1.5px solid #EFEBE9; }
.sr-card-icon { width: 36px; height: 36px; border-radius: 10px; background: #EFEBE9; color: #2D1A11; display: grid; place-items: center; flex-shrink: 0; }
.sr-card-icon.blue { background: #DBEAFE; color: #2563EB; }
.sr-card-title { font-size: 13.5px; font-weight: 800; color: #2D1A11; }
.sr-card-sub { font-size: 11px; color: #8D6E63; margin-top: 2px; }
.sr-link-btn { margin-left: auto; padding: 6px 12px; background: #FDFBF7; color: #D84315; border: 1.5px solid #EFEBE9; border-radius: 8px; font-size: 11px; font-weight: 800; text-decoration: none; white-space: nowrap; }
.sr-link-btn:hover { background: #FFEBEE; border-color: #FFAB91; }

.sr-progress-wrap { margin-bottom: 14px; }
.sr-progress-header { display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; margin-bottom: 8px; font-weight: 700; }
.sr-progress { height: 6px; background: #EFEBE9; border-radius: 3px; overflow: hidden; }
.sr-progress-bar { height: 100%; border-radius: 3px; transition: width 0.4s; }

.sr-rows { display: flex; flex-direction: column; }
.sr-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; font-size: 12.5px; border-bottom: 1.5px solid #EFEBE9; }
.sr-row:last-child { border-bottom: none; }
.sr-row span { color: #8D6E63; font-weight: 600; }
.sr-row strong { color: #2D1A11; font-weight: 800; font-variant-numeric: tabular-nums; }
.sr-row-total { padding-top: 12px; border-top: 2px solid #EFEBE9 !important; border-bottom: none !important; }

.sr-payments { display: flex; flex-direction: column; gap: 2px; }
.sr-payment-row { display: flex; align-items: center; gap: 11px; padding: 10px 0; border-bottom: 1.5px solid #EFEBE9; }
.sr-payment-row:last-child { border-bottom: none; }
.sr-payment-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; font-size: 16px; flex-shrink: 0; }
.sr-payment-info { flex: 1; min-width: 0; }
.sr-payment-title { font-size: 12.5px; font-weight: 800; color: #2D1A11; }
.sr-payment-meta { font-size: 10.5px; color: #8D6E63; margin-top: 2px; }
.sr-payment-amount { font-size: 13px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; flex-shrink: 0; }

.sr-items { display: flex; flex-direction: column; }
.sr-item-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1.5px solid #EFEBE9; font-size: 12px; }
.sr-item-row:last-child { border-bottom: none; }
.sr-item-name { flex: 1; min-width: 0; color: #2D1A11; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sr-item-qty { color: #8D6E63; font-size: 11px; flex-shrink: 0; }
.sr-item-total { color: #D84315; font-weight: 800; flex-shrink: 0; font-variant-numeric: tabular-nums; }

.sr-pay-section { background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 16px; padding: 16px; margin-bottom: 14px; }
.sr-pay-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1.5px solid #EFEBE9; }
.sr-pay-label { font-size: 11px; font-weight: 800; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.08em; }
.sr-pay-amount { font-size: 20px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; }
.sr-pay-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px; }
.sr-pay-btn-full, .sr-pay-btn-half, .sr-pay-btn-custom { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 12px 14px; border-radius: 12px; font-size: 12.5px; font-weight: 800; font-family: inherit; text-decoration: none; transition: all 0.15s; border: 1px solid; cursor: pointer; }
.sr-pay-btn-full { background: #DCFCE7; border-color: #BBF7D0; color: #15803D; grid-column: 1 / -1; }
.sr-pay-btn-full:hover { background: #BBF7D0; transform: translateY(-1px); }
.sr-pay-btn-half { background: #DBEAFE; border-color: #BFDBFE; color: #1D4ED8; }
.sr-pay-btn-half:hover { background: #BFDBFE; transform: translateY(-1px); }
.sr-pay-btn-custom { background: #FFEBEE; border-color: #FFCCBC; color: #D84315; }
.sr-pay-btn-custom:hover { background: #FFCCBC; transform: translateY(-1px); }
.sr-pay-btn-full:active, .sr-pay-btn-half:active, .sr-pay-btn-custom:active { transform: scale(0.97); }
.sr-pay-note { display: flex; align-items: flex-start; gap: 6px; padding: 9px 11px; background: #EFF6FF; border: 1.5px solid #BFDBFE; border-radius: 9px; font-size: 10.5px; color: #1D4ED8; line-height: 1.5; }
.sr-pay-note svg { flex-shrink: 0; margin-top: 2px; color: #2563EB; }
.sr-pay-note strong { color: #1E40AF; font-weight: 800; }

@media (max-width: 400px) { .sr-pay-buttons { grid-template-columns: 1fr; } .sr-pay-btn-full { grid-column: 1; } }
@media (max-width: 480px) {
    .sr-hero { padding: 16px; gap: 12px; }
    .sr-hero-icon { width: 44px; height: 44px; }
    .sr-hero-amount-value { font-size: 18px; }
    .sr-card { padding: 16px; }
}
</style>
@endpush

@push('scripts')
<script>
function goBack(fallback) {
    if (window.history.length > 2) window.history.back();
    else if (fallback) window.location.href = fallback;
}
</script>
@endpush