@extends('portal.layouts.app')

@section('title', $delivery->dr_number)

@section('content')

@php
    $isConfirmed = $delivery->customer_confirmed ?? false;
    $isOut = !$isConfirmed && $delivery->out_for_delivery_at;
    $remaining = max(0, (float) $delivery->total_amount - (float) $delivery->amount_paid);
    $isPaid = $remaining <= 0;
@endphp

{{-- HEADER --}}
<div class="page-header">
    <div class="page-header-left">
        <button type="button" class="back-btn" onclick="goBack('{{ route('portal.deliveries.index') }}')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </button>
        <div>
            <div class="page-title">{{ $delivery->dr_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

{{-- STATUS HERO --}}
<div class="dv-hero {{ $isConfirmed ? 'confirmed' : ($isOut ? 'transit' : 'pending') }}">
    <div class="dv-hero-icon">
        @if($isConfirmed)
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        @elseif($isOut)
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        @else
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        @endif
    </div>
    <div class="dv-hero-text">
        <div class="dv-hero-label">
            @if($isConfirmed) Customer Confirmed
            @elseif($isOut) Out for Delivery
            @else Pending
            @endif
        </div>
        <div class="dv-hero-sub">
            @if($isConfirmed) Confirmed {{ \Carbon\Carbon::parse($delivery->confirmed_at)->diffForHumans() }}
            @elseif($isOut) Marked {{ \Carbon\Carbon::parse($delivery->out_for_delivery_at)->diffForHumans() }}
            @else Waiting for admin to ship
            @endif
        </div>
    </div>
    <div class="dv-hero-amount">
        <div class="dv-hero-amount-label">Total</div>
        <div class="dv-hero-amount-value">₱{{ number_format($delivery->total_amount, 0) }}</div>
    </div>
</div>

{{-- PAY NOW CTA --}}
@if($isConfirmed && !$isPaid)
    <div class="dv-pay-cta">
        <div class="dv-pay-cta-head"><span class="dv-pay-cta-dot"></span>Outstanding Balance</div>
        <div class="dv-pay-cta-amount">₱{{ number_format($remaining, 2) }}</div>
        <a href="{{ route('portal.payments.create', ['dr' => $delivery->id]) }}" class="dv-pay-cta-btn">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Pay Now
        </a>
    </div>
@endif

{{-- FULLY PAID BANNER --}}
@if($isConfirmed && $isPaid)
    <div class="dv-paid-banner">
        <div class="dv-paid-banner-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="dv-paid-banner-text">
            <div class="dv-paid-banner-title">Fully Paid</div>
            <div class="dv-paid-banner-sub">Walay remaining balance</div>
        </div>
    </div>
@endif

{{-- ITEMS CARD --}}
<div class="card">
    <div class="card-head">
        <div class="card-head-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
            <div class="card-head-title">Items</div>
            <div class="card-head-sub">{{ $delivery->items->count() }} product(s)</div>
        </div>
    </div>
    <div class="items-list">
        @foreach($delivery->items as $item)
            <div class="item-row">
                <div class="item-thumb">
                    @if($item->product && $item->product->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                    @else
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                <div class="item-info">
                    <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                    <div class="item-meta">{{ $item->quantity_delivered }} × ₱{{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="item-total">₱{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
    </div>
    <div class="total-row">
        <span>Total</span>
        <strong>₱{{ number_format($delivery->total_amount, 2) }}</strong>
    </div>
</div>

{{-- PAYMENT CARD --}}
<div class="card">
    <div class="card-head">
        <div class="card-head-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div><div class="card-head-title">Payment</div></div>
    </div>
    <div class="balance-list">
        <div class="balance-row">
            <span class="balance-label">Total</span>
            <span class="balance-value">₱{{ number_format($delivery->total_amount, 2) }}</span>
        </div>
        <div class="balance-row">
            <span class="balance-label">Paid</span>
            <span class="balance-value green">- ₱{{ number_format($delivery->amount_paid, 2) }}</span>
        </div>
        <div class="balance-row balance-total">
            <span class="balance-label">Balance</span>
            <span class="balance-value {{ $delivery->balance > 0 ? 'accent' : 'green' }}">₱{{ number_format($delivery->balance, 2) }}</span>
        </div>
    </div>
</div>

{{-- STATUS ACTION --}}
@if($isConfirmed)
    <div class="dv-action confirmed">
        <div class="dv-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
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
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
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
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="dv-action-text">
            <div class="dv-action-title">Waiting for Delivery</div>
            <div class="dv-action-sub">Wala pa gi-ship sa admin</div>
        </div>
    </div>
@endif

{{-- CONFIRM MODAL --}}
<div id="confirmReceivedModal" class="cr-modal-overlay" style="display:none;" aria-hidden="true">
    <div class="cr-modal" role="dialog" aria-modal="true">
        <div class="cr-modal-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="cr-modal-title">Confirm Received?</div>
        <div class="cr-modal-sub">DR-<strong>{{ $delivery->dr_number }}</strong></div>
        <div class="cr-modal-body">
            <div class="cr-modal-row"><span>Items</span><strong>{{ $delivery->items->count() }} product(s)</strong></div>
            <div class="cr-modal-row"><span>Total</span><strong style="color:#D84315;">₱{{ number_format($delivery->total_amount, 2) }}</strong></div>
            @if($delivery->balance > 0)
                <div class="cr-modal-row"><span>Balance</span><strong style="color:#B45309;">₱{{ number_format($delivery->balance, 2) }}</strong></div>
            @endif
        </div>
        <div class="cr-modal-warning">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            Siguroha nga <strong>nadawat nimo</strong> ang tanan products bag-o mo-confirm. Dili na ma-undo.
        </div>
        <div class="cr-modal-actions">
            <button type="button" class="cr-modal-btn-cancel" data-cr-close>Cancel</button>
            <button type="button" class="cr-modal-btn-confirm" id="crModalConfirm">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                Yes, Confirm
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ═══ DELIVERY SHOW — PREMIUM COFFEE ═══ */
.page-header { display: flex; align-items: center; margin-bottom: 16px; }
.page-header-left { display: flex; align-items: center; gap: 12px; }
.back-btn { width: 40px; height: 40px; border-radius: 12px; background: #FFFFFF; color: #8D6E63; display: grid; place-items: center; border: 1.5px solid #EFEBE9; cursor: pointer; transition: all 0.15s; }
.back-btn:hover { border-color: #D7CCC8; color: #2D1A11; transform: translateX(-2px); }
.back-btn:active { transform: scale(0.94); }
.page-title { font-size: 18px; font-weight: 800; color: #2D1A11; letter-spacing: -0.02em; font-family: ui-monospace, monospace; }
.page-sub { font-size: 11.5px; color: #8D6E63; margin-top: 2px; font-weight: 600; }

.dv-hero { display: flex; align-items: center; gap: 14px; padding: 18px; border-radius: 18px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.dv-hero.confirmed { background: #F0FDF4; border: 1.5px solid #BBF7D0; }
.dv-hero.transit { background: #EFF6FF; border: 1.5px solid #BFDBFE; }
.dv-hero.pending { background: #FFFBEB; border: 1.5px solid #FDE68A; }
.dv-hero-icon { width: 48px; height: 48px; border-radius: 14px; display: grid; place-items: center; flex-shrink: 0; }
.dv-hero.confirmed .dv-hero-icon { background: #DCFCE7; color: #15803D; }
.dv-hero.transit .dv-hero-icon { background: #DBEAFE; color: #1D4ED8; }
.dv-hero.pending .dv-hero-icon { background: #FEF3C7; color: #B45309; }
.dv-hero-text { flex: 1; min-width: 0; }
.dv-hero-label { font-size: 15px; font-weight: 800; margin-bottom: 3px; letter-spacing: -0.01em; }
.dv-hero.confirmed .dv-hero-label { color: #15803D; }
.dv-hero.transit .dv-hero-label { color: #1D4ED8; }
.dv-hero.pending .dv-hero-label { color: #B45309; }
.dv-hero-sub { font-size: 11.5px; color: #8D6E63; font-weight: 500; }
.dv-hero-amount { text-align: right; flex-shrink: 0; }
.dv-hero-amount-label { font-size: 9.5px; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; margin-bottom: 3px; }
.dv-hero-amount-value { font-size: 20px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }

.dv-pay-cta { padding: 22px; background: linear-gradient(135deg, #FDFBF7, #FFF); border: 1.5px solid #EFEBE9; border-radius: 18px; margin-bottom: 14px; box-shadow: 0 4px 14px -4px rgba(45, 26, 17, 0.06); }
.dv-pay-cta-head { display: flex; align-items: center; gap: 8px; font-size: 10.5px; font-weight: 800; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 10px; }
.dv-pay-cta-dot { width: 7px; height: 7px; border-radius: 50%; background: #D97706; box-shadow: 0 0 8px rgba(217, 119, 6, 0.6); animation: dvPulse 1.6s ease-in-out infinite; }
@keyframes dvPulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.2); opacity: 0.7; } }
.dv-pay-cta-amount { font-size: 36px; font-weight: 800; color: #D84315; letter-spacing: -0.03em; line-height: 1; font-variant-numeric: tabular-nums; margin-bottom: 18px; }
.dv-pay-cta-btn { display: inline-flex; align-items: center; justify-content: center; gap: 9px; width: 100%; min-height: 52px; padding: 0 22px; border-radius: 14px; background: linear-gradient(135deg, #D84315, #BF360C); color: #FFFFFF; font-family: inherit; font-size: 14.5px; font-weight: 800; letter-spacing: -0.01em; text-decoration: none; border: none; cursor: pointer; box-shadow: 0 10px 24px -8px rgba(216, 67, 21, 0.6); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
.dv-pay-cta-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 32px -8px rgba(216, 67, 21, 0.8); }
.dv-pay-cta-btn:active { transform: scale(0.97); }

.dv-paid-banner { display: flex; align-items: center; gap: 14px; padding: 16px 18px; background: #F0FDF4; border: 1.5px solid #BBF7D0; border-radius: 16px; margin-bottom: 14px; }
.dv-paid-banner-icon { width: 44px; height: 44px; border-radius: 12px; background: #DCFCE7; color: #15803D; display: grid; place-items: center; flex-shrink: 0; }
.dv-paid-banner-text { flex: 1; min-width: 0; }
.dv-paid-banner-title { font-size: 15px; font-weight: 800; color: #15803D; margin-bottom: 3px; letter-spacing: -0.01em; }
.dv-paid-banner-sub { font-size: 11.5px; color: #8D6E63; }

.card { background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; padding: 20px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.card-head { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1.5px solid #EFEBE9; }
.card-head-icon { width: 40px; height: 40px; border-radius: 12px; background: #EFEBE9; color: #2D1A11; display: grid; place-items: center; flex-shrink: 0; }
.card-head-icon.green { background: #DCFCE7; color: #15803D; }
.card-head-title { font-size: 14px; font-weight: 800; color: #2D1A11; letter-spacing: -0.01em; }
.card-head-sub { font-size: 11.5px; color: #8D6E63; margin-top: 2px; }

.items-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
.item-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1.5px solid #EFEBE9; }
.item-row:last-child { border-bottom: none; }
.item-thumb { width: 44px; height: 44px; border-radius: 12px; background: #FDFBF7; border: 1.5px solid #EFEBE9; display: grid; place-items: center; color: #D7CCC8; overflow: hidden; flex-shrink: 0; }
.item-thumb img { width: 100%; height: 100%; object-fit: cover; }
.item-info { flex: 1; min-width: 0; }
.item-name { font-size: 13px; font-weight: 700; color: #2D1A11; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.item-meta { font-size: 11px; color: #8D6E63; }
.item-total { font-size: 14px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; flex-shrink: 0; }

.total-row { display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 2px solid #EFEBE9; font-size: 12.5px; color: #8D6E63; font-weight: 700; }
.total-row strong { font-size: 20px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; }

.balance-list { display: flex; flex-direction: column; }
.balance-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; font-size: 13px; }
.balance-row + .balance-row { border-top: 1.5px solid #EFEBE9; }
.balance-label { color: #8D6E63; font-weight: 600; }
.balance-value { font-weight: 800; color: #2D1A11; font-variant-numeric: tabular-nums; }
.balance-value.green { color: #15803D; }
.balance-value.accent { color: #D84315; }
.balance-total { padding-top: 14px; border-top: 2px solid #EFEBE9 !important; }
.balance-total .balance-value { font-size: 16px; }

.dv-action { padding: 18px; border-radius: 18px; margin-bottom: 14px; }
.dv-action.confirmed { background: #F0FDF4; border: 1.5px solid #BBF7D0; display: flex; align-items: center; gap: 14px; }
.dv-action.pending { background: #FFFBEB; border: 1.5px solid #FDE68A; display: flex; align-items: center; gap: 14px; }
.dv-action.transit { background: #FFFFFF; border: 1.5px solid #BFDBFE; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.dv-action-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.dv-action-icon { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0; }
.dv-action.confirmed .dv-action-icon { background: #DCFCE7; color: #15803D; }
.dv-action.transit .dv-action-icon { background: #DBEAFE; color: #1D4ED8; }
.dv-action.pending .dv-action-icon { background: #FEF3C7; color: #B45309; }
.dv-action-text { flex: 1; min-width: 0; }
.dv-action-title { font-size: 14px; font-weight: 800; margin-bottom: 2px; letter-spacing: -0.01em; }
.dv-action.confirmed .dv-action-title { color: #15803D; }
.dv-action.transit .dv-action-title { color: #1D4ED8; }
.dv-action.pending .dv-action-title { color: #B45309; }
.dv-action-sub { font-size: 11.5px; color: #8D6E63; }

.dv-action-form { display: flex; flex-direction: column; gap: 10px; }
.dv-action-notes { width: 100%; padding: 12px 14px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; color: #2D1A11; font-size: 13px; font-family: inherit; outline: none; resize: none; }
.dv-action-notes:focus { border-color: #D84315; background: #FFFFFF; box-shadow: 0 0 0 3px rgba(216, 67, 21, 0.1); }
.dv-action-notes::placeholder { color: #A8A29E; }
.dv-action-btn { width: 100%; min-height: 50px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, #D84315, #BF360C); border: none; border-radius: 13px; color: #FFFFFF; font-size: 13.5px; font-weight: 800; font-family: inherit; cursor: pointer; box-shadow: 0 10px 24px -8px rgba(216, 67, 21, 0.6); transition: all 0.15s; }
.dv-action-btn:active { transform: scale(0.98); }

.cr-modal-overlay { position: fixed; inset: 0; background: rgba(45, 26, 17, 0.7); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px; }
.cr-modal { width: 100%; max-width: 400px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 22px; padding: 28px 24px 22px; box-shadow: 0 30px 60px -20px rgba(45, 26, 17, 0.4); text-align: center; }
.cr-modal-icon { width: 64px; height: 64px; margin: 0 auto 16px; border-radius: 20px; background: #DCFCE7; color: #15803D; display: grid; place-items: center; box-shadow: 0 10px 30px -10px rgba(34, 197, 94, 0.4); }
.cr-modal-title { font-size: 19px; font-weight: 800; color: #2D1A11; margin-bottom: 6px; }
.cr-modal-sub { font-size: 12.5px; color: #8D6E63; margin-bottom: 20px; }
.cr-modal-sub strong { color: #D84315; font-weight: 800; font-family: ui-monospace, monospace; }
.cr-modal-body { background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; padding: 14px; margin-bottom: 16px; text-align: left; }
.cr-modal-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 12.5px; }
.cr-modal-row + .cr-modal-row { border-top: 1.5px solid #EFEBE9; }
.cr-modal-row span { color: #8D6E63; font-weight: 600; }
.cr-modal-row strong { color: #2D1A11; font-weight: 800; }
.cr-modal-warning { display: flex; align-items: flex-start; gap: 8px; padding: 12px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; font-size: 11.5px; color: #92400E; text-align: left; margin-bottom: 20px; line-height: 1.5; }
.cr-modal-warning svg { flex-shrink: 0; margin-top: 1px; color: #D97706; }
.cr-modal-warning strong { color: #B45309; font-weight: 800; }
.cr-modal-actions { display: flex; gap: 10px; }
.cr-modal-btn-cancel, .cr-modal-btn-confirm { flex: 1; min-height: 48px; display: inline-flex; align-items: center; justify-content: center; gap: 7px; border: none; border-radius: 13px; font-size: 13.5px; font-weight: 800; font-family: inherit; cursor: pointer; }
.cr-modal-btn-cancel { background: #FFFFFF; color: #8D6E63; border: 1.5px solid #EFEBE9; }
.cr-modal-btn-cancel:hover { background: #FDFBF7; border-color: #D7CCC8; color: #2D1A11; }
.cr-modal-btn-confirm { background: linear-gradient(135deg, #D84315, #BF360C); color: #FFFFFF; box-shadow: 0 10px 24px -8px rgba(216, 67, 21, 0.6); }
.cr-modal-btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 14px 28px -8px rgba(216, 67, 21, 0.8); }
.cr-modal-btn-confirm:active, .cr-modal-btn-cancel:active { transform: scale(0.98); }

@media (max-width: 480px) {
    .dv-hero { padding: 16px; gap: 12px; }
    .dv-hero-icon { width: 44px; height: 44px; }
    .dv-hero-amount-value { font-size: 18px; }
    .dv-pay-cta { padding: 18px; }
    .dv-pay-cta-amount { font-size: 30px; }
    .card { padding: 16px; }
    .cr-modal { padding: 22px 18px 18px; }
    .cr-modal-title { font-size: 17px; }
}
</style>
@endpush

@push('scripts')
<script>
function goBack(fallback) {
    if (window.history.length > 2) { window.history.back(); }
    else if (fallback) { window.location.href = fallback; }
}

(function() {
    const openBtn = document.getElementById('openConfirmModal');
    const modal = document.getElementById('confirmReceivedModal');
    const form = document.getElementById('confirmForm');
    const confirmBtn = document.getElementById('crModalConfirm');
    if (!openBtn || !modal || !form) return;

    openBtn.addEventListener('click', function() {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
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
        modal.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('scroll-locked');
    }
})();
</script>
@endpush

@endsection