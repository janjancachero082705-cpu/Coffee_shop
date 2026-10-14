@extends('portal.layouts.app')

@section('title', $delivery->dr_number)

@section('content')

@php
    $isConfirmed = $delivery->customer_confirmed ?? false;
    $isOut = !$isConfirmed && $delivery->out_for_delivery_at;
    $remaining = max(0, (float) $delivery->total_amount - (float) $delivery->amount_paid);
    $isPaid = $remaining <= 0;
    $paidPercent = $delivery->total_amount > 0 ? min(100, round(((float)$delivery->amount_paid / (float)$delivery->total_amount) * 100)) : 0;
    $statusKey = $isConfirmed ? 'confirmed' : ($isOut ? 'transit' : 'pending');
    $statusText = $isConfirmed ? 'Confirmed' : ($isOut ? 'Out for Delivery' : 'Pending');
@endphp

{{-- BACK --}}
<a href="{{ route('portal.deliveries.index') }}" class="dv-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back
</a>

{{-- HERO CARD with stripe --}}
<div class="dv-hero" data-status="{{ $statusKey }}">
    <div class="dv-hero-head">
        <div class="dv-hero-status">
            <div class="dv-hero-status-icon">
                @if($isConfirmed)
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                @elseif($isOut)
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                @else
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                @endif
            </div>
            <div class="dv-hero-status-text">
                <div class="dv-hero-status-title">{{ $statusText }}</div>
                <div class="dv-hero-status-sub">
                    @if($isConfirmed) {{ \Carbon\Carbon::parse($delivery->confirmed_at)->diffForHumans() }}
                    @elseif($isOut) {{ \Carbon\Carbon::parse($delivery->out_for_delivery_at)->diffForHumans() }}
                    @else Waiting for admin to ship
                    @endif
                </div>
            </div>
        </div>
        <div class="dv-hero-date">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}
        </div>
    </div>
    <div class="dv-hero-num">{{ $delivery->dr_number }}</div>
    <div class="dv-hero-amt">₱{{ number_format($delivery->total_amount, 2) }}</div>
</div>

{{-- PAYMENT PROGRESS CARD --}}
<div class="dv-progress-card">
    <div class="dv-progress-head">
        <div class="dv-progress-label">
            <span class="dv-progress-dot" style="background: {{ $isPaid ? '#16A34A' : '#D97706' }}; box-shadow: 0 0 8px {{ $isPaid ? 'rgba(34,197,94,.6)' : 'rgba(217,119,6,.6)' }};"></span>
            Payment Progress
        </div>
        <div class="dv-progress-percent">{{ $paidPercent }}%</div>
    </div>
    <div class="dv-progress-bar">
        <div class="dv-progress-fill" style="width: {{ $paidPercent }}%; background: {{ $isPaid ? 'linear-gradient(90deg,#16A34A,#4ADE80)' : 'linear-gradient(90deg,#E8A33D,#F0B85A)' }};"></div>
    </div>
    <div class="dv-progress-meta">
        <div class="dv-progress-meta-item">
            <div class="dv-progress-meta-lbl">Paid</div>
            <div class="dv-progress-meta-val green">₱{{ number_format($delivery->amount_paid, 0) }}</div>
        </div>
        <div class="dv-progress-meta-item">
            <div class="dv-progress-meta-lbl">Balance</div>
            <div class="dv-progress-meta-val {{ $remaining > 0 ? 'accent' : 'green' }}">₱{{ number_format($remaining, 0) }}</div>
        </div>
    </div>
</div>

{{-- PAY NOW CTA --}}
@if($isConfirmed && !$isPaid)
    <a href="{{ route('portal.payments.create', ['dr' => $delivery->id]) }}" class="dv-pay-btn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Pay Now — ₱{{ number_format($remaining, 2) }}
    </a>
@endif

{{-- FULLY PAID BANNER --}}
@if($isConfirmed && $isPaid)
    <div class="dv-paid">
        <div class="dv-paid-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="dv-paid-text">
            <div class="dv-paid-title">Fully Paid</div>
            <div class="dv-paid-sub">Walay remaining balance</div>
        </div>
    </div>
@endif

{{-- ITEMS --}}
<div class="dv-section-label">
    <span>Items</span>
    <span class="dv-section-count">{{ $delivery->items->count() }}</span>
</div>
<div class="dv-list">
    @foreach($delivery->items as $item)
        <div class="dv-item">
            <div class="dv-item-thumb">
                @if($item->product && $item->product->image_url)
                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                @else
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                @endif
            </div>
            <div class="dv-item-info">
                <div class="dv-item-name">{{ $item->product->name ?? '-' }}</div>
                <div class="dv-item-meta">{{ $item->quantity_delivered }} × ₱{{ number_format($item->unit_price, 2) }}</div>
            </div>
            <div class="dv-item-total">₱{{ number_format($item->subtotal, 2) }}</div>
        </div>
    @endforeach
</div>

{{-- TOTAL --}}
<div class="dv-total">
    <span class="dv-total-lbl">Grand Total</span>
    <span class="dv-total-val">₱{{ number_format($delivery->total_amount, 2) }}</span>
</div>

{{-- STATUS ACTION --}}
@if($isConfirmed)
    <div class="dv-action confirmed">
        <div class="dv-action-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="dv-action-text">
            <div class="dv-action-title">Delivery Confirmed</div>
            <div class="dv-action-sub">Confirmed {{ \Carbon\Carbon::parse($delivery->confirmed_at)->diffForHumans() }}</div>
        </div>
    </div>
@elseif($isOut)
    <div class="dv-action transit">
        <div class="dv-action-head">
            <div class="dv-action-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="dv-action-text">
                <div class="dv-action-title">Out for Delivery</div>
                <div class="dv-action-sub">Kung nadawat na, i-confirm</div>
            </div>
        </div>
        <form method="POST" action="{{ route('portal.deliveries.confirm', $delivery->id) }}" id="confirmForm" class="dv-action-form">
            @csrf
            <textarea name="notes" class="dv-action-notes" rows="2" placeholder="Optional notes..."></textarea>
            <button type="button" class="dv-action-btn" id="openConfirmModal">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                Confirm Received
            </button>
        </form>
    </div>
@else
    <div class="dv-action pending">
        <div class="dv-action-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="dv-action-text">
            <div class="dv-action-title">Waiting for Delivery</div>
            <div class="dv-action-sub">Wala pa gi-ship sa admin</div>
        </div>
    </div>
@endif

{{-- CONFIRM MODAL --}}
<div id="confirmReceivedModal" class="cr-modal-overlay" style="display:none;">
    <div class="cr-modal">
        <div class="cr-modal-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="cr-modal-title">Confirm Received?</div>
        <div class="cr-modal-sub">{{ $delivery->dr_number }}</div>
        <div class="cr-modal-body">
            <div class="cr-modal-row"><span>Items</span><strong>{{ $delivery->items->count() }} product(s)</strong></div>
            <div class="cr-modal-row"><span>Total</span><strong style="color:#D4901F;">₱{{ number_format($delivery->total_amount, 2) }}</strong></div>
            @if($remaining > 0)
                <div class="cr-modal-row"><span>Balance</span><strong style="color:#B45309;">₱{{ number_format($remaining, 2) }}</strong></div>
            @endif
        </div>
        <div class="cr-modal-warning">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div>Siguroha nga <strong>nadawat nimo</strong> ang tanan products bag-o mo-confirm. Dili na ma-undo.</div>
        </div>
        <div class="cr-modal-actions">
            <button type="button" class="cr-modal-btn-cancel" data-cr-close>Cancel</button>
            <button type="button" class="cr-modal-btn-confirm" id="crModalConfirm">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                Confirm
            </button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ═══ DELIVERY SHOW — MATCHING APP DESIGN ═══ */

.dv-back{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;color:#757575;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s}
.dv-back:hover{border-color:#D0D0D0;color:#1A1A1A;transform:translateX(-2px)}

/* HERO with stripe */
.dv-hero{position:relative;padding:18px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;overflow:hidden;animation:fadeIn .3s ease both}
.dv-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.dv-hero[data-status="pending"]::before{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.dv-hero[data-status="transit"]::before{background:linear-gradient(90deg,#2563EB,#60A5FA)}
.dv-hero[data-status="confirmed"]::before{background:linear-gradient(90deg,#15803D,#4ADE80)}
.dv-hero-head{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:14px}
.dv-hero-status{display:flex;align-items:center;gap:10px;flex:1;min-width:0}
.dv-hero-status-icon{width:36px;height:36px;border-radius:10px;display:grid;place-items:center;flex-shrink:0}
.dv-hero[data-status="pending"] .dv-hero-status-icon{background:#FFF8E1;color:#B45309;border:1px solid #FDE68A}
.dv-hero[data-status="transit"] .dv-hero-status-icon{background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE}
.dv-hero[data-status="confirmed"] .dv-hero-status-icon{background:#E8F5E9;color:#15803D;border:1px solid #BBF7D0}
.dv-hero-status-text{flex:1;min-width:0}
.dv-hero-status-title{font-size:14px;font-weight:800;color:#1A1A1A;letter-spacing:-.01em;line-height:1.2;margin-bottom:2px}
.dv-hero-status-sub{font-size:11px;color:#757575;font-weight:500}
.dv-hero-date{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;color:#757575;font-weight:600;flex-shrink:0}
.dv-hero-num{font-size:17px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;letter-spacing:-.02em;line-height:1.2;margin-bottom:6px;word-break:break-all}
.dv-hero-amt{font-size:30px;font-weight:800;color:#1A1A1A;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums}

/* PROGRESS CARD */
.dv-progress-card{padding:16px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;animation:fadeIn .35s ease both;animation-delay:.05s}
.dv-progress-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.dv-progress-label{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.1em}
.dv-progress-dot{width:6px;height:6px;border-radius:50%}
.dv-progress-percent{font-size:14px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums}
.dv-progress-bar{width:100%;height:8px;background:#F5F5F5;border-radius:100px;overflow:hidden;margin-bottom:14px}
.dv-progress-fill{height:100%;border-radius:100px;transition:width .6s cubic-bezier(.4,0,.2,1);box-shadow:0 0 12px rgba(232,163,61,.4)}
.dv-progress-meta{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.dv-progress-meta-item{padding:10px 12px;background:#FAFAFA;border:1px solid #F0F0F0;border-radius:10px}
.dv-progress-meta-lbl{font-size:9.5px;font-weight:700;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px}
.dv-progress-meta-val{font-size:15px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums;letter-spacing:-.02em}
.dv-progress-meta-val.green{color:#15803D}
.dv-progress-meta-val.accent{color:#D4901F}

/* PAY BUTTON */
.dv-pay-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;min-height:52px;padding:0 20px;margin-bottom:14px;background:#E8A33D;border-radius:14px;color:#FFFFFF;font-family:inherit;font-size:14.5px;font-weight:800;letter-spacing:-.01em;text-decoration:none;box-shadow:0 8px 20px -6px rgba(232,163,61,.5);transition:all .15s;animation:fadeIn .35s ease both;animation-delay:.1s}
.dv-pay-btn:hover{background:#D4901F;transform:translateY(-1px);box-shadow:0 12px 28px -8px rgba(232,163,61,.6)}
.dv-pay-btn:active{transform:scale(.98)}

/* PAID BANNER */
.dv-paid{display:flex;align-items:center;gap:12px;padding:14px;margin-bottom:14px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:14px;animation:fadeIn .35s ease both}
.dv-paid-icon{width:38px;height:38px;border-radius:10px;background:#DCFCE7;color:#15803D;display:grid;place-items:center;flex-shrink:0;border:1px solid #BBF7D0}
.dv-paid-text{flex:1;min-width:0}
.dv-paid-title{font-size:13.5px;font-weight:800;color:#15803D;margin-bottom:2px;letter-spacing:-.01em}
.dv-paid-sub{font-size:11px;color:#757575}

/* SECTION LABEL */
.dv-section-label{display:flex;justify-content:space-between;align-items:center;margin:0 4px 10px}
.dv-section-label span:first-child{font-size:12px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.08em}
.dv-section-count{font-size:11px;font-weight:700;color:#757575;background:#F5F5F5;padding:3px 10px;border-radius:100px}

/* ITEMS LIST */
.dv-list{background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;overflow:hidden;margin-bottom:14px;animation:fadeIn .35s ease both;animation-delay:.15s}
.dv-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-bottom:1px solid #F5F5F5;transition:background .15s}
.dv-item:last-child{border-bottom:none}
.dv-item:hover{background:#FAFAFA}
.dv-item-thumb{width:44px;height:44px;border-radius:10px;background:#F5F5F5;border:1px solid #EEEEEE;display:grid;place-items:center;color:#B8A29A;overflow:hidden;flex-shrink:0}
.dv-item-thumb img{width:100%;height:100%;object-fit:cover}
.dv-item-info{flex:1;min-width:0}
.dv-item-name{font-size:13px;font-weight:700;color:#1A1A1A;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dv-item-meta{font-size:11px;color:#757575;font-weight:500;font-variant-numeric:tabular-nums}
.dv-item-total{font-size:14px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;flex-shrink:0;letter-spacing:-.02em}

/* TOTAL ROW */
.dv-total{display:flex;justify-content:space-between;align-items:center;padding:16px;background:linear-gradient(135deg,#FFF8EC,#FFFCF5);border:1px solid #F0E0C0;border-radius:12px;margin-bottom:14px;position:relative;overflow:hidden;animation:fadeIn .35s ease both;animation-delay:.2s}
.dv-total::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#E8A33D,#F0B85A)}
.dv-total-lbl{font-size:11px;font-weight:800;color:#92400E;text-transform:uppercase;letter-spacing:.08em}
.dv-total-val{font-size:22px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;letter-spacing:-.03em}

/* ACTIONS */
.dv-action{padding:14px;border-radius:14px;margin-bottom:14px;animation:fadeIn .35s ease both;animation-delay:.25s}
.dv-action.confirmed{background:#F0FDF4;border:1px solid #BBF7D0;display:flex;align-items:center;gap:12px}
.dv-action.pending{background:#FFFBEB;border:1px solid #FDE68A;display:flex;align-items:center;gap:12px}
.dv-action.transit{background:#FFFFFF;border:1px solid #BFDBFE;box-shadow:0 4px 12px -4px rgba(37,99,235,.1)}
.dv-action-head{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.dv-action-icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;flex-shrink:0;border:1px solid}
.dv-action.confirmed .dv-action-icon{background:#DCFCE7;color:#15803D;border-color:#BBF7D0}
.dv-action.transit .dv-action-icon{background:#EFF6FF;color:#1D4ED8;border-color:#BFDBFE}
.dv-action.pending .dv-action-icon{background:#FFF8E1;color:#B45309;border-color:#FDE68A}
.dv-action-text{flex:1;min-width:0}
.dv-action-title{font-size:13px;font-weight:800;margin-bottom:2px;letter-spacing:-.01em}
.dv-action.confirmed .dv-action-title{color:#15803D}
.dv-action.transit .dv-action-title{color:#1D4ED8}
.dv-action.pending .dv-action-title{color:#B45309}
.dv-action-sub{font-size:11px;color:#757575}

.dv-action-form{display:flex;flex-direction:column;gap:8px}
.dv-action-notes{width:100%;padding:11px 13px;background:#FAFAFA;border:1.5px solid #EEEEEE;border-radius:10px;color:#1A1A1A;font-size:12.5px;font-family:inherit;outline:none;resize:none;font-weight:500;transition:all .15s}
.dv-action-notes:focus{border-color:#E8A33D;background:#FFFFFF;box-shadow:0 0 0 3px rgba(232,163,61,.12)}
.dv-action-notes::placeholder{color:#A8A29E}
.dv-action-btn{width:100%;min-height:48px;display:inline-flex;align-items:center;justify-content:center;gap:7px;background:#E8A33D;border:none;border-radius:12px;color:#FFFFFF;font-size:13.5px;font-weight:800;font-family:inherit;cursor:pointer;box-shadow:0 6px 16px -6px rgba(232,163,61,.5);transition:all .15s}
.dv-action-btn:hover{background:#D4901F;transform:translateY(-1px)}
.dv-action-btn:active{transform:scale(.98)}

/* MODAL */
.cr-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px}
.cr-modal{width:100%;max-width:400px;background:#FFFFFF;border-radius:20px;padding:24px 22px 20px;text-align:center;animation:crModalIn .3s cubic-bezier(.34,1.2,.64,1)}
@keyframes crModalIn{from{transform:scale(.92);opacity:0;}to{transform:scale(1);opacity:1;}}
.cr-modal-icon{width:60px;height:60px;margin:0 auto 14px;border-radius:18px;background:#FFF8EC;color:#D4901F;display:grid;place-items:center;border:1px solid #F0E0C0;box-shadow:0 10px 24px -10px rgba(232,163,61,.4)}
.cr-modal-title{font-size:17px;font-weight:800;color:#1A1A1A;margin-bottom:4px;letter-spacing:-.02em}
.cr-modal-sub{font-size:12px;color:#757575;margin-bottom:16px;font-family:ui-monospace,monospace;font-weight:600}
.cr-modal-body{background:#FAFAFA;border:1px solid #F0F0F0;border-radius:10px;padding:12px;margin-bottom:14px;text-align:left}
.cr-modal-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:12px}
.cr-modal-row+.cr-modal-row{border-top:1px solid #F0F0F0}
.cr-modal-row span{color:#757575;font-weight:600}
.cr-modal-row strong{color:#1A1A1A;font-weight:800;font-variant-numeric:tabular-nums}
.cr-modal-warning{display:flex;align-items:flex-start;gap:8px;padding:11px 12px;background:#FFF8E1;border:1px solid #FDE68A;border-radius:10px;font-size:11px;color:#92400E;text-align:left;margin-bottom:16px;line-height:1.5}
.cr-modal-warning svg{flex-shrink:0;margin-top:1px;color:#D97706}
.cr-modal-warning strong{color:#B45309;font-weight:800}
.cr-modal-actions{display:flex;gap:8px}
.cr-modal-btn-cancel,.cr-modal-btn-confirm{flex:1;min-height:46px;display:inline-flex;align-items:center;justify-content:center;gap:6px;border-radius:12px;font-size:13px;font-weight:800;font-family:inherit;cursor:pointer;transition:all .15s;border:none}
.cr-modal-btn-cancel{background:#FFFFFF;color:#757575;border:1.5px solid #EEEEEE}
.cr-modal-btn-cancel:hover{background:#FAFAFA;border-color:#D0D0D0;color:#1A1A1A}
.cr-modal-btn-confirm{background:#E8A33D;color:#FFFFFF;box-shadow:0 6px 16px -6px rgba(232,163,61,.5)}
.cr-modal-btn-confirm:hover{background:#D4901F;transform:translateY(-1px)}
.cr-modal-btn-confirm:active,.cr-modal-btn-cancel:active{transform:scale(.98)}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.dv-hero{padding:16px}
.dv-hero-amt{font-size:26px}
.dv-progress-meta-val{font-size:14px}
.dv-total-val{font-size:20px}
.cr-modal{padding:22px 18px 18px}
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const openBtn = document.getElementById('openConfirmModal');
    const modal = document.getElementById('confirmReceivedModal');
    const form = document.getElementById('confirmForm');
    const confirmBtn = document.getElementById('crModalConfirm');
    if (!openBtn || !modal || !form) return;

    openBtn.addEventListener('click', function() {
        modal.style.display = 'flex';
        document.documentElement.classList.add('scroll-locked');
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = 'Confirming...';
            form.submit();
        });
    }

    modal.querySelectorAll('[data-cr-close]').forEach(function(el) {
        el.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && modal.style.display === 'flex') closeModal(); });

    function closeModal() {
        modal.style.display = 'none';
        document.documentElement.classList.remove('scroll-locked');
    }
})();
</script>
@endpush
