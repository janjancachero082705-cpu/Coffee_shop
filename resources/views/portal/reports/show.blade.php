@extends('portal.layouts.app')

@section('title', $report->report_number)

@section('content')

@php
    $paidPercent = $report->amount_due > 0 ? min(100, ($report->amount_paid / $report->amount_due) * 100) : 0;
    $statusKey = in_array($report->status, ['paid', 'partial', 'verified']) ? $report->status : 'pending';
    $statusText = ucfirst($report->status);
@endphp

{{-- BACK --}}
<a href="{{ route('portal.reports.index') }}" class="rs-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back
</a>

{{-- HERO --}}
<div class="rs-hero" data-status="{{ $statusKey }}">
    <div class="rs-hero-head">
        <div class="rs-hero-badge">
            <div class="rs-hero-badge-icon">
                @if($report->status === 'paid')
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                @elseif($report->status === 'partial')
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                @else
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                @endif
            </div>
            <div class="rs-hero-badge-text">
                <div class="rs-hero-badge-title">{{ $statusText }}</div>
                <div class="rs-hero-badge-sub">Balance: ₱{{ number_format($report->balance, 2) }}</div>
            </div>
        </div>
        <div class="rs-hero-date">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            {{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} – {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}
        </div>
    </div>
    <div class="rs-hero-num">{{ $report->report_number }}</div>
    <div class="rs-hero-amt">₱{{ number_format($report->amount_due, 2) }}</div>
</div>

{{-- PAYMENT PROGRESS --}}
<div class="rs-progress-card">
    <div class="rs-progress-head">
        <div class="rs-progress-label">
            <span class="rs-progress-dot" style="background: {{ $report->balance > 0 ? '#D97706' : '#16A34A' }}; box-shadow: 0 0 8px {{ $report->balance > 0 ? 'rgba(217,119,6,.6)' : 'rgba(34,197,94,.6)' }};"></span>
            Payment Progress
        </div>
        <div class="rs-progress-percent">{{ number_format($paidPercent, 1) }}%</div>
    </div>
    <div class="rs-progress-bar">
        <div class="rs-progress-fill" style="width: {{ $paidPercent }}%; background: {{ $report->balance > 0 ? 'linear-gradient(90deg,#E8A33D,#F0B85A)' : 'linear-gradient(90deg,#16A34A,#4ADE80)' }};"></div>
    </div>
    <div class="rs-progress-meta">
        <div class="rs-progress-meta-item">
            <div class="rs-progress-meta-lbl">Paid</div>
            <div class="rs-progress-meta-val green">₱{{ number_format($report->amount_paid, 0) }}</div>
        </div>
        <div class="rs-progress-meta-item">
            <div class="rs-progress-meta-lbl">Balance</div>
            <div class="rs-progress-meta-val {{ $report->balance > 0 ? 'accent' : 'green' }}">₱{{ number_format($report->balance, 0) }}</div>
        </div>
    </div>
</div>

{{-- BREAKDOWN --}}
<div class="rs-section-label">Breakdown</div>
<div class="rs-list">
    <div class="rs-row">
        <div class="rs-row-icon" style="--c:#2563EB;--bg:#EFF6FF;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
        </div>
        <div class="rs-row-content">
            <div class="rs-row-label">Total Sales</div>
            <div class="rs-row-value">₱{{ number_format($report->total_sales, 2) }}</div>
        </div>
    </div>
    <div class="rs-row">
        <div class="rs-row-icon" style="--c:#E8A33D;--bg:#FFF8EC;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="rs-row-content">
            <div class="rs-row-label">Amount Due</div>
            <div class="rs-row-value">₱{{ number_format($report->amount_due, 2) }}</div>
        </div>
    </div>
    <div class="rs-row">
        <div class="rs-row-icon" style="--c:#15803D;--bg:#E8F5E9;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="rs-row-content">
            <div class="rs-row-label">Amount Paid</div>
            <div class="rs-row-value green">- ₱{{ number_format($report->amount_paid, 2) }}</div>
        </div>
    </div>
</div>

{{-- TOTAL --}}
<div class="rs-total">
    <span class="rs-total-lbl">Balance</span>
    <span class="rs-total-val {{ $report->balance > 0 ? '' : 'paid' }}">₱{{ number_format($report->balance, 2) }}</span>
</div>

{{-- LINKED DELIVERY --}}
@if($report->deliveryReceipt)
    <div class="rs-section-label">Linked Delivery</div>
    <div class="rs-list">
        <a href="{{ route('portal.deliveries.show', $report->deliveryReceipt->id) }}" class="rs-row rs-row-link">
            <div class="rs-row-icon" style="--c:#15803D;--bg:#E8F5E9;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
            </div>
            <div class="rs-row-content">
                <div class="rs-row-label">Delivery Receipt</div>
                <div class="rs-row-value" style="font-family:ui-monospace,monospace;">{{ $report->deliveryReceipt->dr_number }}</div>
            </div>
            <svg class="rs-row-arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>
@endif

{{-- PAYMENTS --}}
@if($linkedPayments && $linkedPayments->count() > 0)
    <div class="rs-section-label">
        <span>Payment History</span>
        <span class="rs-section-count">{{ $linkedPayments->count() }}</span>
    </div>
    <div class="rs-list">
        @foreach($linkedPayments as $p)
            <div class="rs-row">
                <div class="rs-row-icon" style="--c:{{ $p->method_color }};--bg:{{ $p->method_color }}22;">
                    <span style="font-size:13px;line-height:1;">{{ $p->method_icon }}</span>
                </div>
                <div class="rs-row-content">
                    <div class="rs-row-label">{{ $p->method_label }}</div>
                    <div class="rs-row-value" style="font-size:12px;font-weight:600;color:#757575;">
                        {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                        @if($p->reference_number) · Ref: {{ $p->reference_number }} @endif
                    </div>
                </div>
                <div class="rs-row-value" style="font-size:13.5px;font-weight:800;color:#15803D;flex-shrink:0;">₱{{ number_format($p->amount, 2) }}</div>
            </div>
        @endforeach
    </div>
@endif

{{-- ITEMS --}}
@if($report->items && $report->items->count() > 0)
    <div class="rs-section-label">
        <span>Items</span>
        <span class="rs-section-count">{{ $report->items->count() }}</span>
    </div>
    <div class="rs-list">
        @foreach($report->items as $item)
            <div class="rs-row">
                <div class="rs-row-content">
                    <div class="rs-row-value" style="font-size:13px;margin-bottom:3px;">{{ $item->product->name ?? '—' }}</div>
                    <div class="rs-row-label">{{ $item->quantity_sold }} × ₱{{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="rs-row-value" style="font-size:13.5px;font-weight:800;color:#D4901F;flex-shrink:0;">₱{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
    </div>
@endif

{{-- NOTES --}}
@if($report->notes)
    <div class="rs-section-label">Notes</div>
    <div class="rs-notes">{{ $report->notes }}</div>
@endif

{{-- PAY CTA --}}
@if($report->balance > 0)
    <div class="rs-section-label">Pay Balance</div>
    <div class="rs-pay-card">
        <div class="rs-pay-amt-label">Outstanding Balance</div>
        <div class="rs-pay-amt">₱{{ number_format($report->balance, 2) }}</div>

        <div class="rs-pay-btns">
            @php
    $hasPendingPayment = \App\Models\ConsignmentPayment::where('delivery_receipt_id', $report->delivery_receipt_id)->where('verification_status', 'pending')->exists();
@endphp

@if($hasPendingPayment)
    <div class="rs-pay-btn rs-pay-btn-full" style="background: #FEF3C7; color: #D97706; border: 1.5px solid #FDE68A; cursor: not-allowed; justify-content: center; display: flex; align-items: center; gap: 8px; padding: 14px 24px; border-radius: 100px; font-weight: 700; width: 100%;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        Waiting for Approval
    </div>
@else
    <a href="{{ route('portal.payments.create', ['delivery_receipt_id' => $report->delivery_receipt_id, 'amount' => number_format($report->balance, 2, '.', ''), 'report_id' => $report->id]) }}" 
       onclick="this.style.pointerEvents='none'; this.style.opacity='0.6'; this.innerHTML='<svg width=\'18\' height=\'18\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' style=\'animation: spin 1s linear infinite; margin-right: 6px;\'><circle cx=\'12\' cy=\'12\' r=\'10\' opacity=\'0.3\'/><path d=\'M12 2a10 10 0 0 1 10 10\'/></svg> Processing...';"
       class="rs-pay-btn rs-pay-btn-full" style="justify-content: center; display: flex; align-items: center; gap: 8px; padding: 14px 24px; border-radius: 100px; font-weight: 700; width: 100%;">
        Record Payment
    </a>
@endif
            <div class="rs-pay-row">
                <a href="{{ route('portal.payments.create', ['delivery_receipt_id' => $report->delivery_receipt_id, 'amount' => number_format($report->balance / 2, 2, '.', ''), 'report_id' => $report->id]) }}" class="rs-pay-btn rs-pay-btn-half">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20"/></svg>
                    Half — ₱{{ number_format($report->balance / 2, 2) }}
                </a>
                <a href="{{ route('portal.payments.create', ['delivery_receipt_id' => $report->delivery_receipt_id, 'report_id' => $report->id]) }}" class="rs-pay-btn rs-pay-btn-custom">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Custom
                </a>
            </div>
        </div>

        <div class="rs-pay-note">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            Pwede ka mag-bayad ug <strong>partial</strong> o <strong>full</strong>.
        </div>
    </div>
@endif

@endsection

@push('styles')
<style>
/* ═══ REPORT SHOW — MATCHING APP DESIGN ═══ */

.rs-back{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;color:#757575;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s}
.rs-back:hover{border-color:#D0D0D0;color:#1A1A1A;transform:translateX(-2px)}

/* HERO */
.rs-hero{position:relative;padding:18px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;overflow:hidden;animation:fadeIn .3s ease both}
.rs-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.rs-hero[data-status="paid"]::before{background:linear-gradient(90deg,#15803D,#4ADE80)}
.rs-hero[data-status="partial"]::before{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.rs-hero[data-status="verified"]::before{background:linear-gradient(90deg,#2563EB,#60A5FA)}
.rs-hero[data-status="pending"]::before{background:linear-gradient(90deg,#9E9E9E,#BDBDBD)}
.rs-hero-head{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:14px}
.rs-hero-badge{display:flex;align-items:center;gap:10px;flex:1;min-width:0}
.rs-hero-badge-icon{width:36px;height:36px;border-radius:10px;display:grid;place-items:center;flex-shrink:0;border:1px solid}
.rs-hero[data-status="paid"] .rs-hero-badge-icon{background:#E8F5E9;color:#15803D;border-color:#BBF7D0}
.rs-hero[data-status="partial"] .rs-hero-badge-icon{background:#FFF8E1;color:#B45309;border-color:#FDE68A}
.rs-hero[data-status="verified"] .rs-hero-badge-icon{background:#EFF6FF;color:#1D4ED8;border-color:#BFDBFE}
.rs-hero[data-status="pending"] .rs-hero-badge-icon{background:#F5F5F5;color:#757575;border-color:#EEEEEE}
.rs-hero-badge-text{flex:1;min-width:0}
.rs-hero-badge-title{font-size:13.5px;font-weight:800;color:#1A1A1A;letter-spacing:-.01em;line-height:1.2;margin-bottom:2px}
.rs-hero-badge-sub{font-size:11px;color:#757575;font-weight:500}
.rs-hero-date{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;color:#757575;font-weight:600;flex-shrink:0}
.rs-hero-num{font-size:15px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;letter-spacing:-.02em;line-height:1.2;margin-bottom:6px;word-break:break-all}
.rs-hero-amt{font-size:30px;font-weight:800;color:#1A1A1A;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums}

/* PROGRESS CARD */
.rs-progress-card{padding:16px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;animation:fadeIn .35s ease both;animation-delay:.05s}
.rs-progress-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.rs-progress-label{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.1em}
.rs-progress-dot{width:6px;height:6px;border-radius:50%}
.rs-progress-percent{font-size:14px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums}
.rs-progress-bar{width:100%;height:8px;background:#F5F5F5;border-radius:100px;overflow:hidden;margin-bottom:14px}
.rs-progress-fill{height:100%;border-radius:100px;transition:width .6s cubic-bezier(.4,0,.2,1)}
.rs-progress-meta{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.rs-progress-meta-item{padding:10px 12px;background:#FAFAFA;border:1px solid #F0F0F0;border-radius:10px}
.rs-progress-meta-lbl{font-size:9.5px;font-weight:700;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px}
.rs-progress-meta-val{font-size:15px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums;letter-spacing:-.02em}
.rs-progress-meta-val.green{color:#15803D}
.rs-progress-meta-val.accent{color:#D4901F}

/* SECTION LABEL */
.rs-section-label{display:flex;justify-content:space-between;align-items:center;margin:0 4px 10px}
.rs-section-label span:first-child{font-size:12px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.08em}
.rs-section-count{font-size:11px;font-weight:700;color:#757575;background:#F5F5F5;padding:3px 10px;border-radius:100px}

/* LIST */
.rs-list{background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;overflow:hidden;margin-bottom:16px;animation:fadeIn .35s ease both;animation-delay:.1s}
.rs-row{display:flex;align-items:center;gap:12px;padding:12px 14px;border-bottom:1px solid #F5F5F5;transition:background .15s;text-decoration:none;color:inherit}
.rs-row:last-child{border-bottom:none}
.rs-row:hover{background:#FAFAFA}
.rs-row-link{cursor:pointer}
.rs-row-icon{width:34px;height:34px;border-radius:9px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0;border:1px solid rgba(0,0,0,.04)}
.rs-row-content{flex:1;min-width:0}
.rs-row-label{font-size:9.5px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-bottom:2px}
.rs-row-value{font-size:13px;font-weight:700;color:#1A1A1A;line-height:1.3}
.rs-row-value.green{color:#15803D}
.rs-row-arrow{color:#C0C0C0;flex-shrink:0;transition:all .15s}
.rs-row:hover .rs-row-arrow{color:#D4901F;transform:translateX(3px)}

/* TOTAL */
.rs-total{display:flex;justify-content:space-between;align-items:center;padding:16px;background:linear-gradient(135deg,#FFF8EC,#FFFCF5);border:1px solid #F0E0C0;border-radius:12px;margin-bottom:14px;position:relative;overflow:hidden;animation:fadeIn .35s ease both;animation-delay:.15s}
.rs-total::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#E8A33D,#F0B85A)}
.rs-total-lbl{font-size:11px;font-weight:800;color:#92400E;text-transform:uppercase;letter-spacing:.08em}
.rs-total-val{font-size:22px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;letter-spacing:-.03em}
.rs-total-val.paid{color:#15803D}

/* NOTES */
.rs-notes{padding:12px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;font-size:12.5px;line-height:1.55;color:#4B5563;white-space:pre-wrap;margin-bottom:16px;font-weight:500}

/* PAY CARD */
.rs-pay-card{padding:16px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;margin-bottom:14px;animation:fadeIn .35s ease both}
.rs-pay-amt-label{font-size:9.5px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px}
.rs-pay-amt{font-size:26px;font-weight:800;color:#D4901F;letter-spacing:-.03em;line-height:1;font-variant-numeric:tabular-nums;margin-bottom:14px}
.rs-pay-btns{display:flex;flex-direction:column;gap:8px;margin-bottom:12px}
.rs-pay-row{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.rs-pay-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:44px;padding:0 14px;border-radius:100px;font-size:12.5px;font-weight:800;text-decoration:none;transition:all .15s;font-family:inherit}
.rs-pay-btn:active{transform:scale(.97)}
.rs-pay-btn-full{background:#E8A33D;color:#FFFFFF;box-shadow:0 6px 16px -6px rgba(232,163,61,.5)}
.rs-pay-btn-full:hover{background:#D4901F;transform:translateY(-1px)}
.rs-pay-btn-half{background:#FFFFFF;color:#1A1A1A;border:1.5px solid #EEEEEE}
.rs-pay-btn-half:hover{border-color:#1A1A1A}
.rs-pay-btn-custom{background:#FFFFFF;color:#D4901F;border:1.5px solid #E8A33D}
.rs-pay-btn-custom:hover{background:#FFF8EC}

.rs-pay-note{display:flex;align-items:flex-start;gap:6px;padding:10px 12px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;font-size:10.5px;color:#1D4ED8;line-height:1.5;font-weight:500}
.rs-pay-note svg{flex-shrink:0;margin-top:2px;color:#2563EB}
.rs-pay-note strong{color:#1E40AF;font-weight:800}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.rs-hero{padding:16px}
.rs-hero-amt{font-size:26px}
.rs-row{padding:11px 12px}
.rs-row-icon{width:30px;height:30px}
.rs-progress-meta-val{font-size:14px}
.rs-total-val{font-size:20px}
.rs-pay-amt{font-size:22px}
}
</style>
@endpush
