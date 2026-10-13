@extends('portal.layouts.app')

@section('title', $payment->payment_number)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <button type="button" class="back-btn" onclick="goBack('{{ route('portal.payments.index') }}')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </button>
        <div>
            <div class="page-title">{{ $payment->payment_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="pay-flash"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>{{ session('success') }}</div>
@endif

{{-- HERO --}}
<div class="pay-hero" style="--mc: {{ $payment->method_color }};">
    <div class="pay-hero-icon">{{ $payment->method_icon }}</div>
    <div class="pay-hero-text">
        <div class="pay-hero-label">{{ $payment->method_label }}</div>
        <div class="pay-hero-sub">@if($payment->reference_number)Ref: {{ $payment->reference_number }}@else{{ \Carbon\Carbon::parse($payment->payment_date)->diffForHumans() }}@endif</div>
    </div>
    <div class="pay-hero-amount">
        <div class="pay-hero-amount-label">Amount</div>
        <div class="pay-hero-amount-value">₱{{ number_format($payment->amount, 2) }}</div>
    </div>
</div>

@php $vStatus = $payment->verification_status ?? 'pending'; @endphp

@if($vStatus === 'pending')
    <div class="pvfy pvfy-pending">
        <div class="pvfy-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="pvfy-body"><div class="pvfy-title">Waiting for Admin Approval</div><div class="pvfy-desc">Gi-review pa sa admin imong bayad. Dili pa ma-count sa imong balance hangtud ma-approve.</div></div>
    </div>
@elseif($vStatus === 'verified')
    <div class="pvfy pvfy-verified">
        <div class="pvfy-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        <div class="pvfy-body"><div class="pvfy-title">Paid na ✅</div><div class="pvfy-desc">Gi-approve na sa admin.@if($payment->verified_at) · {{ \Carbon\Carbon::parse($payment->verified_at)->diffForHumans() }}@endif</div></div>
    </div>
@elseif($vStatus === 'rejected')
    <div class="pvfy pvfy-rejected">
        <div class="pvfy-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg></div>
        <div class="pvfy-body"><div class="pvfy-title">Payment Rejected ❌</div><div class="pvfy-desc">@if($payment->rejection_reason)Reason: {{ $payment->rejection_reason }}@else Wala gi-approve sa admin.@endif</div></div>
    </div>
@endif

<div class="pay-card">
    <div class="pay-card-head">
        <div class="pay-card-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></svg></div>
        <div><div class="pay-card-title">Details</div></div>
    </div>
    <div class="pay-rows">
        <div class="pay-row"><span>Payment #</span><strong>{{ $payment->payment_number }}</strong></div>
        <div class="pay-row"><span>Method</span><strong style="color:{{ $payment->method_color }};">{{ $payment->method_icon }} {{ $payment->method_label }}</strong></div>
        @if($payment->reference_number)<div class="pay-row"><span>Reference #</span><strong style="font-family:monospace;">{{ $payment->reference_number }}</strong></div>@endif
        <div class="pay-row"><span>Payment Date</span><strong>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</strong></div>
        @if($payment->deliveryReceipt)<div class="pay-row"><span>Linked DR</span><strong>{{ $payment->deliveryReceipt->dr_number }}</strong></div>@endif
        @if($payment->notes)<div class="pay-row" style="display:block;"><span style="display:block;margin-bottom:6px;">Notes</span><div style="color:#4B5563;font-size:12.5px;line-height:1.5;">{{ $payment->notes }}</div></div>@endif
    </div>
</div>

<div class="pay-card">
    <div class="pay-card-head">
        <div class="pay-card-icon blue"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div><div class="pay-card-title">Timeline</div></div>
    </div>
    <div class="pay-timeline">
        <div class="pay-tl-item"><div class="pay-tl-dot" style="background:#22c55e;"></div><div><div class="pay-tl-label">Payment recorded</div><div class="pay-tl-time">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}</div></div></div>
        <div class="pay-tl-item"><div class="pay-tl-dot"></div><div><div class="pay-tl-label">Payment date</div><div class="pay-tl-time">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div></div></div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ═══ PAYMENT SHOW — PREMIUM COFFEE ═══ */
.page-header { display: flex; align-items: center; margin-bottom: 16px; }
.page-header-left { display: flex; align-items: center; gap: 12px; }
.back-btn { width: 40px; height: 40px; border-radius: 12px; background: #FFFFFF; color: #8D6E63; display: grid; place-items: center; border: 1.5px solid #EFEBE9; cursor: pointer; transition: all 0.15s; }
.back-btn:hover { border-color: #D7CCC8; color: #2D1A11; transform: translateX(-2px); }
.back-btn:active { transform: scale(0.94); }
.page-title { font-size: 18px; font-weight: 800; color: #2D1A11; letter-spacing: -0.02em; font-family: ui-monospace, monospace; }
.page-sub { font-size: 11.5px; color: #8D6E63; margin-top: 2px; font-weight: 600; }

.pay-flash { display: flex; align-items: center; gap: 8px; padding: 12px 14px; margin-bottom: 14px; background: #F0FDF4; border: 1.5px solid #BBF7D0; border-radius: 12px; color: #15803D; font-size: 12.5px; font-weight: 700; }

.pay-hero { display: flex; align-items: center; gap: 14px; padding: 18px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.pay-hero-icon { width: 52px; height: 52px; border-radius: 14px; background: color-mix(in srgb, var(--mc) 15%, transparent); display: grid; place-items: center; font-size: 24px; flex-shrink: 0; }
.pay-hero-text { flex: 1; min-width: 0; }
.pay-hero-label { font-size: 15px; font-weight: 800; color: var(--mc); margin-bottom: 3px; }
.pay-hero-sub { font-size: 11.5px; color: #8D6E63; }
.pay-hero-amount { text-align: right; flex-shrink: 0; }
.pay-hero-amount-label { font-size: 9.5px; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 3px; }
.pay-hero-amount-value { font-size: 20px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; }

.pvfy { display: flex; align-items: flex-start; gap: 14px; padding: 16px 18px; margin: 14px 0; border-radius: 14px; border: 1px solid; }
.pvfy-pending { background: #FFFBEB; border-color: #FDE68A; border-left: 3px solid #D97706; }
.pvfy-verified { background: #F0FDF4; border-color: #BBF7D0; border-left: 3px solid #16A34A; }
.pvfy-rejected { background: #FEF2F2; border-color: #FECACA; border-left: 3px solid #DC2626; }
.pvfy-icon { width: 40px; height: 40px; border-radius: 11px; display: grid; place-items: center; flex-shrink: 0; }
.pvfy-pending .pvfy-icon { background: #FEF3C7; color: #B45309; }
.pvfy-verified .pvfy-icon { background: #DCFCE7; color: #15803D; }
.pvfy-rejected .pvfy-icon { background: #FEE2E2; color: #B91C1C; }
.pvfy-body { flex: 1; min-width: 0; }
.pvfy-title { font-size: 14px; font-weight: 800; margin-bottom: 4px; line-height: 1.3; }
.pvfy-pending .pvfy-title { color: #B45309; }
.pvfy-verified .pvfy-title { color: #15803D; }
.pvfy-rejected .pvfy-title { color: #B91C1C; }
.pvfy-desc { font-size: 12px; line-height: 1.5; }
.pvfy-pending .pvfy-desc { color: #92400E; }
.pvfy-verified .pvfy-desc { color: #166534; }
.pvfy-rejected .pvfy-desc { color: #991B1B; }

.pay-card { background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; padding: 20px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.pay-card-head { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1.5px solid #EFEBE9; }
.pay-card-icon { width: 40px; height: 40px; border-radius: 12px; background: #EFEBE9; color: #2D1A11; display: grid; place-items: center; flex-shrink: 0; }
.pay-card-icon.blue { background: #DBEAFE; color: #2563EB; }
.pay-card-title { font-size: 14px; font-weight: 800; color: #2D1A11; }

.pay-rows { display: flex; flex-direction: column; }
.pay-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1.5px solid #EFEBE9; font-size: 12.5px; }
.pay-row:last-child { border-bottom: none; }
.pay-row span { color: #8D6E63; font-weight: 600; }
.pay-row strong { color: #2D1A11; font-weight: 800; }

.pay-timeline { display: flex; flex-direction: column; gap: 14px; }
.pay-tl-item { display: flex; gap: 12px; align-items: flex-start; }
.pay-tl-dot { width: 10px; height: 10px; border-radius: 50%; background: #D7CCC8; margin-top: 4px; flex-shrink: 0; }
.pay-tl-label { font-size: 12.5px; font-weight: 800; color: #2D1A11; }
.pay-tl-time { font-size: 11px; color: #8D6E63; margin-top: 2px; font-weight: 600; }

@media (max-width: 480px) {
    .pay-hero { padding: 16px; gap: 12px; }
    .pay-hero-icon { width: 46px; height: 46px; }
    .pay-hero-amount-value { font-size: 18px; }
    .pay-card { padding: 16px; }
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