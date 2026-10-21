@extends('portal.layouts.app')

@section('title', $payment->payment_number)

@section('content')

@php
    $vStatus = $payment->verification_status ?? 'pending';
    $statusKey = $vStatus === 'verified' ? 'verified' : ($vStatus === 'rejected' ? 'rejected' : 'pending');
    $statusText = $vStatus === 'verified' ? 'Paid' : ($vStatus === 'rejected' ? 'Rejected' : 'Pending');
@endphp

{{-- BACK --}}
<a href="{{ route('portal.payments.index') }}" class="pys-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back
</a>

@if(session('success'))
    <div class="pys-flash">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- HERO CARD with stripe --}}
<div class="pys-hero" data-status="{{ $statusKey }}" style="--mc: {{ $payment->method_color }};">
    <div class="pys-hero-head">
        <div class="pys-hero-method">
            <div class="pys-hero-method-icon" style="background: {{ $payment->method_color }}22; color: {{ $payment->method_color }};">
                <span style="font-size:18px;line-height:1;">{{ $payment->method_icon }}</span>
            </div>
            <div class="pys-hero-method-text">
                <div class="pys-hero-method-label">{{ $payment->method_label }}</div>
                <div class="pys-hero-method-sub">
                    @if($payment->reference_number) Ref: {{ $payment->reference_number }}
                    @else {{ \Carbon\Carbon::parse($payment->payment_date)->diffForHumans() }} @endif
                </div>
            </div>
        </div>
        <span class="pys-badge pys-badge-{{ $statusKey }}">{{ $statusText }}</span>
    </div>
    <div class="pys-hero-num">{{ $payment->payment_number }}</div>
    <div class="pys-hero-amt">₱{{ number_format($payment->amount, 2) }}</div>
</div>

{{-- STATUS BANNER --}}
@if($vStatus === 'pending')
    <div class="pys-status pys-status-pending">
        <div class="pys-status-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="pys-status-body">
            <div class="pys-status-title">Waiting for Admin Approval</div>
            <div class="pys-status-desc">Gi-review pa sa admin imong bayad. Dili pa ma-count sa imong balance hangtud ma-approve.</div>
        </div>
    </div>
@elseif($vStatus === 'verified')
    <div class="pys-status pys-status-verified">
        <div class="pys-status-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        <div class="pys-status-body">
            <div class="pys-status-title">Payment Verified</div>
            <div class="pys-status-desc">Gi-approve na sa admin.@if($payment->verified_at) · {{ \Carbon\Carbon::parse($payment->verified_at)->diffForHumans() }}@endif</div>
        </div>
    </div>
@elseif($vStatus === 'rejected')
    <div class="pys-status pys-status-rejected" style="flex-direction: column; gap: 12px; padding: 16px;">
        <div style="display: flex; align-items: flex-start; gap: 10px; width: 100%;">
            <div class="pys-status-icon" style="margin-top: 2px;"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg></div>
            <div class="pys-status-body">
                <div class="pys-status-title" style="font-size: 14px; margin-bottom: 4px;">Payment Rejected</div>
                <div class="pys-status-desc">Your payment was rejected by the admin. Please review the reason below and submit a new payment.</div>
            </div>
        </div>
        @if($payment->rejection_reason)
            <div style="width: 100%; background: #FEE2E2; border: 1px solid #FECACA; padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 700; color: #991B1B;">
                Reason: {{ $payment->rejection_reason }}
            </div>
        @endif
    </div>
@endif</div>
        </div>
    </div>
@endif

{{-- DETAILS SECTION --}}
<div class="pys-section-label">
    <span>Details</span>
</div>
<div class="pys-list">
    <div class="pys-row">
        <div class="pys-row-icon" style="--c:#2563EB;--bg:#EFF6FF;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        </div>
        <div class="pys-row-content">
            <div class="pys-row-label">Payment Number</div>
            <div class="pys-row-value" style="font-family:ui-monospace,monospace;">{{ $payment->payment_number }}</div>
        </div>
    </div>
    <div class="pys-row">
        <div class="pys-row-icon" style="--c:{{ $payment->method_color }};--bg:{{ $payment->method_color }}22;">
            <span style="font-size:14px;line-height:1;">{{ $payment->method_icon }}</span>
        </div>
        <div class="pys-row-content">
            <div class="pys-row-label">Payment Method</div>
            <div class="pys-row-value">{{ $payment->method_label }}</div>
        </div>
    </div>
    @if($payment->reference_number)
    <div class="pys-row">
        <div class="pys-row-icon" style="--c:#757575;--bg:#F5F5F5;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h16"/></svg>
        </div>
        <div class="pys-row-content">
            <div class="pys-row-label">Reference Number</div>
            <div class="pys-row-value" style="font-family:ui-monospace,monospace;">{{ $payment->reference_number }}</div>
        </div>
    </div>
    @endif
    <div class="pys-row">
        <div class="pys-row-icon" style="--c:#E8A33D;--bg:#FFF8EC;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        </div>
        <div class="pys-row-content">
            <div class="pys-row-label">Payment Date</div>
            <div class="pys-row-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
        </div>
    </div>
    @if($payment->deliveryReceipt)
    <div class="pys-row">
        <div class="pys-row-icon" style="--c:#15803D;--bg:#E8F5E9;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
        </div>
        <div class="pys-row-content">
            <div class="pys-row-label">Linked Delivery</div>
            <div class="pys-row-value" style="font-family:ui-monospace,monospace;">{{ $payment->deliveryReceipt->dr_number }}</div>
        </div>
    </div>
    @endif
</div>

{{-- NOTES --}}
@if($payment->notes)
    <div class="pys-section-label">
        <span>Notes</span>
    </div>
    <div class="pys-notes">{{ $payment->notes }}</div>
@endif

{{-- TIMELINE --}}
<div class="pys-section-label">
    <span>Timeline</span>
</div>
<div class="pys-timeline">
    <div class="pys-tl-item">
        <div class="pys-tl-axis">
            <div class="pys-tl-dot" style="background:#15803D;border-color:#BBF7D0;">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            </div>
            <div class="pys-tl-line"></div>
        </div>
        <div class="pys-tl-card">
            <div class="pys-tl-title">Payment recorded</div>
            <div class="pys-tl-time">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y · h:i A') }}</div>
        </div>
    </div>
    <div class="pys-tl-item">
        <div class="pys-tl-axis">
            <div class="pys-tl-dot" style="background:#FFF;border-color:#E8A33D;color:#E8A33D;">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
        </div>
        <div class="pys-tl-card">
            <div class="pys-tl-title">Payment date</div>
            <div class="pys-tl-time">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ═══ PAYMENT SHOW — MATCHING APP DESIGN ═══ */

.pys-back{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;color:#757575;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s}
.pys-back:hover{border-color:#D0D0D0;color:#1A1A1A;transform:translateX(-2px)}

.pys-flash{display:flex;align-items:center;gap:8px;padding:12px 14px;margin-bottom:14px;background:#F0FDF4;border:1px solid #BBF7D0;border-left:3px solid #16A34A;border-radius:12px;color:#15803D;font-size:12.5px;font-weight:700}
.pys-flash svg{flex-shrink:0}

/* HERO */
.pys-hero{position:relative;padding:18px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;overflow:hidden;animation:fadeIn .3s ease both}
.pys-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.pys-hero[data-status="pending"]::before{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.pys-hero[data-status="verified"]::before{background:linear-gradient(90deg,#15803D,#4ADE80)}
.pys-hero[data-status="rejected"]::before{background:linear-gradient(90deg,#B91C1C,#EF4444)}
.pys-hero-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px}
.pys-hero-method{display:flex;align-items:center;gap:10px;flex:1;min-width:0}
.pys-hero-method-icon{width:40px;height:40px;border-radius:11px;display:grid;place-items:center;flex-shrink:0;border:1px solid rgba(0,0,0,.05)}
.pys-hero-method-text{flex:1;min-width:0}
.pys-hero-method-label{font-size:13px;font-weight:800;color:#1A1A1A;letter-spacing:-.01em;line-height:1.2;margin-bottom:2px}
.pys-hero-method-sub{font-size:10.5px;color:#757575;font-weight:600;font-family:ui-monospace,monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pys-badge{display:inline-flex;padding:4px 10px;border-radius:100px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;flex-shrink:0}
.pys-badge-pending{background:#FFF8E1;color:#B45309}
.pys-badge-verified{background:#E8F5E9;color:#15803D}
.pys-badge-rejected{background:#FFEBEE;color:#B91C1C}
.pys-hero-num{font-size:15px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;letter-spacing:-.02em;line-height:1.2;margin-bottom:6px;word-break:break-all}
.pys-hero-amt{font-size:30px;font-weight:800;color:#1A1A1A;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums}

/* STATUS BANNER */
.pys-status{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;margin-bottom:14px;border-radius:12px;border-left:3px solid;animation:fadeIn .3s ease both;animation-delay:.05s}
.pys-status-pending{background:#FFFBEB;border-left-color:#D97706}
.pys-status-verified{background:#F0FDF4;border-left-color:#16A34A}
.pys-status-rejected{background:#FEF2F2;border-left-color:#DC2626}
.pys-status-icon{width:32px;height:32px;border-radius:9px;display:grid;place-items:center;flex-shrink:0;border:1px solid}
.pys-status-pending .pys-status-icon{background:#FEF3C7;color:#B45309;border-color:#FDE68A}
.pys-status-verified .pys-status-icon{background:#DCFCE7;color:#15803D;border-color:#BBF7D0}
.pys-status-rejected .pys-status-icon{background:#FEE2E2;color:#B91C1C;border-color:#FECACA}
.pys-status-body{flex:1;min-width:0}
.pys-status-title{font-size:12.5px;font-weight:800;margin-bottom:3px;letter-spacing:-.01em}
.pys-status-pending .pys-status-title{color:#B45309}
.pys-status-verified .pys-status-title{color:#15803D}
.pys-status-rejected .pys-status-title{color:#B91C1C}
.pys-status-desc{font-size:11.5px;line-height:1.5;color:#4B5563;font-weight:500}

/* SECTION LABEL */
.pys-section-label{display:flex;justify-content:space-between;align-items:center;margin:0 4px 10px}
.pys-section-label span:first-child{font-size:12px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.08em}

/* LIST (Details rows) */
.pys-list{background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;overflow:hidden;margin-bottom:16px;animation:fadeIn .35s ease both;animation-delay:.1s}
.pys-row{display:flex;align-items:center;gap:12px;padding:12px 14px;border-bottom:1px solid #F5F5F5;transition:background .15s}
.pys-row:last-child{border-bottom:none}
.pys-row:hover{background:#FAFAFA}
.pys-row-icon{width:36px;height:36px;border-radius:10px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0;border:1px solid rgba(0,0,0,.04)}
.pys-row-content{flex:1;min-width:0}
.pys-row-label{font-size:10px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px}
.pys-row-value{font-size:13.5px;font-weight:700;color:#1A1A1A;line-height:1.35;word-break:break-word}

/* NOTES */
.pys-notes{padding:12px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;font-size:12.5px;line-height:1.55;color:#4B5563;white-space:pre-wrap;margin-bottom:16px;font-weight:500}

/* TIMELINE */
.pys-timeline{display:flex;flex-direction:column;animation:fadeIn .35s ease both;animation-delay:.15s}
.pys-tl-item{display:flex;gap:12px;position:relative;padding-bottom:10px}
.pys-tl-item:last-child{padding-bottom:0}
.pys-tl-axis{display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:32px}
.pys-tl-dot{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;background:#FFF;border:2px solid;color:#FFF;flex-shrink:0;position:relative;z-index:2;transition:transform .2s}
.pys-tl-item:hover .pys-tl-dot{transform:scale(1.08)}
.pys-tl-line{flex:1;width:2px;background:linear-gradient(180deg,#E0E0E0,#F5F5F5);margin-top:4px;border-radius:2px;min-height:20px}
.pys-tl-card{flex:1;min-width:0;padding:11px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;transition:all .15s}
.pys-tl-item:hover .pys-tl-card{border-color:#D0D0D0;box-shadow:0 6px 16px -6px rgba(0,0,0,.06)}
.pys-tl-title{font-size:12.5px;font-weight:800;color:#1A1A1A;margin-bottom:3px;letter-spacing:-.01em}
.pys-tl-time{font-size:10.5px;color:#757575;font-weight:600;font-family:ui-monospace,monospace}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.pys-hero{padding:16px}
.pys-hero-amt{font-size:26px}
.pys-row{padding:11px 12px}
.pys-row-icon{width:32px;height:32px}
.pys-row-value{font-size:13px}
.pys-tl-dot{width:28px;height:28px}
.pys-tl-axis{width:28px}
}
</style>
@endpush
