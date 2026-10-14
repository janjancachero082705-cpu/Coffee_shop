@extends('portal.layouts.app')

@section('title', $order->request_number)

@section('content')

@php
    $st = strtolower($order->status);
    $statusConfig = [
        'pending'  => ['label' => 'Pending',  'color' => '#B45309', 'bg' => '#FFF8E1', 'stripe' => 'pending', 'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>'],
        'approved' => ['label' => 'Approved', 'color' => '#15803D', 'bg' => '#E8F5E9', 'stripe' => 'approved', 'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>'],
        'rejected' => ['label' => 'Rejected', 'color' => '#B91C1C', 'bg' => '#FFEBEE', 'stripe' => 'rejected', 'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>'],
    ];
    $s = $statusConfig[$st] ?? $statusConfig['pending'];
    $itemCount = $order->items->count();
    $totalQty = $order->items->sum('quantity_requested');
@endphp

{{-- BACK --}}
<a href="{{ route('portal.orders.index') }}" class="po-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back
</a>

{{-- HERO CARD — with top stripe like toast --}}
<div class="po-hero" data-status="{{ $s['stripe'] }}">
    <div class="po-hero-head">
        <div class="po-hero-badge" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">
            {!! $s['icon'] !!}
            <span>{{ $s['label'] }}</span>
        </div>
        <div class="po-hero-date">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            {{ $order->created_at->format('M d, Y · g:i A') }}
        </div>
    </div>
    <div class="po-hero-num">{{ $order->request_number }}</div>
    <div class="po-hero-amt">₱{{ number_format($order->total_amount, 2) }}</div>
</div>

{{-- INFO BANNER --}}
@if($st === 'approved' && $order->isApproved())
    <div class="po-info po-info-green">
        <div class="po-info-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg></div>
        <div class="po-info-body">
            <div class="po-info-title">Order Approved</div>
            <div class="po-info-text">
                @if($order->approved_at){{ $order->approved_at->diffForHumans() }}@endif
                @if($order->deliveryReceipt) · DR: {{ $order->deliveryReceipt->dr_number }}@endif
            </div>
        </div>
    </div>
@elseif($st === 'pending')
    <div class="po-info po-info-amber">
        <div class="po-info-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="po-info-body">
            <div class="po-info-title">Waiting for Approval</div>
            <div class="po-info-text">Admin pa ang nag-review niini nga request</div>
        </div>
    </div>
@elseif($st === 'rejected')
    <div class="po-info po-info-red">
        <div class="po-info-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg></div>
        <div class="po-info-body">
            <div class="po-info-title">Order Rejected</div>
            @if($order->rejection_reason)<div class="po-info-text">Reason: {{ $order->rejection_reason }}</div>@endif
        </div>
    </div>
@endif

{{-- SUMMARY CHIPS --}}
<div class="po-chips">
    <div class="po-chip">
        <div class="po-chip-icon" style="--c:#2563EB;--bg:#EFF6FF;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
        </div>
        <div class="po-chip-info">
            <div class="po-chip-value">{{ $itemCount }}</div>
            <div class="po-chip-label">Items</div>
        </div>
    </div>
    <div class="po-chip">
        <div class="po-chip-icon" style="--c:#E8A33D;--bg:#FFF8EC;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div class="po-chip-info">
            <div class="po-chip-value">{{ $totalQty }}</div>
            <div class="po-chip-label">Qty</div>
        </div>
    </div>
    <div class="po-chip">
        <div class="po-chip-icon" style="--c:#15803D;--bg:#E8F5E9;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="po-chip-info">
            <div class="po-chip-value">₱{{ number_format($order->total_amount, 0) }}</div>
            <div class="po-chip-label">Total</div>
        </div>
    </div>
</div>

{{-- ITEMS SECTION --}}
<div class="po-section-label">Requested Items</div>
<div class="po-list">
    @foreach($order->items as $item)
        <div class="po-item">
            <div class="po-item-thumb">
                @if($item->product && $item->product->image_url)
                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                @else
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                @endif
            </div>
            <div class="po-item-info">
                <div class="po-item-name">{{ $item->product->name ?? '-' }}</div>
                <div class="po-item-meta">{{ $item->quantity_requested }} × ₱{{ number_format($item->unit_price, 2) }}</div>
            </div>
            <div class="po-item-total">₱{{ number_format($item->subtotal, 2) }}</div>
        </div>
    @endforeach
</div>

{{-- TOTAL ROW --}}
<div class="po-total-row">
    <span class="po-total-lbl">Grand Total</span>
    <span class="po-total-val">₱{{ number_format($order->total_amount, 2) }}</span>
</div>

{{-- NOTES --}}
@if($order->notes)
    <div class="po-section-label">Notes</div>
    <div class="po-notes">{{ $order->notes }}</div>
@endif

{{-- CANCEL BUTTON --}}
@if($order->isPending())
    <button type="button" class="po-btn-cancel" onclick="openCancelModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        Cancel Order
    </button>
@endif

{{-- CANCEL MODAL --}}
<div id="cancelModal" class="po-modal-overlay" onclick="if(event.target === this) closeCancelModal()">
    <div class="po-modal">
        <div class="po-modal-head">
            <div class="po-modal-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg></div>
            <div class="po-modal-heading">
                <div class="po-modal-title">Cancel this order?</div>
                <div class="po-modal-sub">{{ $order->request_number }}</div>
            </div>
        </div>
        <div class="po-modal-body">
            <div class="po-modal-warn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <div><strong>Dili na ma-undo.</strong> Ma-notify ang admin sa cancellation ug ma-remove ni nga request sa pending list.</div>
            </div>
            <div class="po-modal-summary">
                <div class="po-modal-summary-row"><span>Order #</span><strong>{{ $order->request_number }}</strong></div>
                <div class="po-modal-summary-row"><span>Items</span><strong>{{ $order->items->count() }} product(s)</strong></div>
                <div class="po-modal-summary-row"><span>Total</span><strong class="gold">₱{{ number_format($order->total_amount, 2) }}</strong></div>
            </div>
            <div class="po-modal-field">
                <label class="po-modal-label">Reason <span class="po-modal-optional">(optional)</span></label>
                <textarea id="cancelReason" rows="3" class="po-modal-textarea" placeholder="Ngano gi-cancel nimo ni nga order?"></textarea>
            </div>
        </div>
        <div class="po-modal-foot">
            <button type="button" class="po-modal-btn po-modal-btn-ghost" onclick="closeCancelModal()">Keep</button>
            <form method="POST" action="{{ route('portal.orders.cancel', $order->id) }}" id="cancelForm" style="flex:1; margin:0;">
                @csrf
                <input type="hidden" name="cancel_reason" id="cancelReasonInput">
                <button type="submit" class="po-modal-btn po-modal-btn-danger" style="width:100%;">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ═══ ORDER SHOW — REDESIGNED ═══ */

/* BACK */
.po-back{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;color:#757575;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s}
.po-back:hover{border-color:#D0D0D0;color:#1A1A1A;transform:translateX(-2px)}

/* HERO — with top stripe like toast */
.po-hero{position:relative;padding:18px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;overflow:hidden;animation:fadeIn .3s ease both}
.po-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.po-hero[data-status="pending"]::before{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.po-hero[data-status="approved"]::before{background:linear-gradient(90deg,#15803D,#4ADE80)}
.po-hero[data-status="rejected"]::before{background:linear-gradient(90deg,#B91C1C,#EF4444)}
.po-hero-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px}
.po-hero-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:100px;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;border:1px solid rgba(0,0,0,.05)}
.po-hero-badge svg{width:13px;height:13px}
.po-hero-date{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;color:#757575;font-weight:600}
.po-hero-num{font-size:17px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;letter-spacing:-.02em;line-height:1.2;margin-bottom:6px;word-break:break-all}
.po-hero-amt{font-size:30px;font-weight:800;color:#1A1A1A;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums}

/* INFO BANNER */
.po-info{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;margin-bottom:14px;border-radius:12px;border-left:3px solid;animation:fadeIn .3s ease both;animation-delay:.05s}
.po-info-green{background:#F0FDF4;border-left-color:#16A34A}
.po-info-amber{background:#FFFBEB;border-left-color:#D97706}
.po-info-red{background:#FEF2F2;border-left-color:#DC2626}
.po-info-icon{width:32px;height:32px;border-radius:9px;display:grid;place-items:center;flex-shrink:0;border:1px solid}
.po-info-green .po-info-icon{background:#DCFCE7;color:#15803D;border-color:#BBF7D0}
.po-info-amber .po-info-icon{background:#FEF3C7;color:#B45309;border-color:#FDE68A}
.po-info-red .po-info-icon{background:#FEE2E2;color:#B91C1C;border-color:#FECACA}
.po-info-body{flex:1;min-width:0}
.po-info-title{font-size:12.5px;font-weight:800;margin-bottom:3px}
.po-info-green .po-info-title{color:#15803D}
.po-info-amber .po-info-title{color:#B45309}
.po-info-red .po-info-title{color:#B91C1C}
.po-info-text{font-size:11.5px;line-height:1.5;color:#4B5563;font-weight:500}

/* SUMMARY CHIPS — with icon boxes */
.po-chips{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:16px;animation:fadeIn .3s ease both;animation-delay:.1s}
.po-chip{display:flex;align-items:center;gap:10px;padding:12px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;transition:all .15s}
.po-chip:hover{border-color:#D0D0D0;transform:translateY(-2px);box-shadow:0 6px 16px -6px rgba(0,0,0,.08)}
.po-chip-icon{width:32px;height:32px;border-radius:9px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0;border:1px solid rgba(0,0,0,.04)}
.po-chip-info{flex:1;min-width:0}
.po-chip-value{font-size:15px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums;letter-spacing:-.02em;line-height:1.2}
.po-chip-label{font-size:9.5px;font-weight:700;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-top:2px}

/* SECTION LABEL */
.po-section-label{font-size:12px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.08em;margin:0 4px 8px}

/* ITEMS LIST */
.po-list{background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;overflow:hidden;margin-bottom:14px;animation:fadeIn .3s ease both;animation-delay:.15s}
.po-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-bottom:1px solid #F5F5F5;transition:background .15s}
.po-item:last-child{border-bottom:none}
.po-item:hover{background:#FAFAFA}
.po-item-thumb{width:44px;height:44px;border-radius:10px;background:#F5F5F5;border:1px solid #EEEEEE;display:grid;place-items:center;color:#B8A29A;overflow:hidden;flex-shrink:0}
.po-item-thumb img{width:100%;height:100%;object-fit:cover}
.po-item-info{flex:1;min-width:0}
.po-item-name{font-size:13px;font-weight:700;color:#1A1A1A;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.po-item-meta{font-size:11px;color:#757575;font-weight:500;font-variant-numeric:tabular-nums}
.po-item-total{font-size:14px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;flex-shrink:0;letter-spacing:-.02em}

/* TOTAL ROW */
.po-total-row{display:flex;justify-content:space-between;align-items:center;padding:16px;background:linear-gradient(135deg,#FFF8EC,#FFFCF5);border:1px solid #F0E0C0;border-radius:12px;margin-bottom:16px;position:relative;overflow:hidden}
.po-total-row::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#E8A33D,#F0B85A)}
.po-total-lbl{font-size:11px;font-weight:800;color:#92400E;text-transform:uppercase;letter-spacing:.08em}
.po-total-val{font-size:22px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;letter-spacing:-.03em}

/* NOTES */
.po-notes{padding:12px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;font-size:12.5px;line-height:1.55;color:#4B5563;white-space:pre-wrap;margin-bottom:16px;font-weight:500}

/* CANCEL BUTTON */
.po-btn-cancel{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:14px;background:#FFFFFF;border:1.5px solid #FFCDD2;border-radius:100px;color:#DC2626;font-size:13px;font-weight:800;font-family:inherit;cursor:pointer;transition:all .15s;margin-top:4px}
.po-btn-cancel:hover{background:#FEF2F2;border-color:#FCA5A5;transform:translateY(-1px)}
.po-btn-cancel:active{transform:scale(.98)}

/* MODAL */
.po-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);display:none;align-items:flex-end;justify-content:center;z-index:9999}
.po-modal-overlay.active{display:flex}
@media(min-width:640px){.po-modal-overlay{align-items:center;padding:20px}}
.po-modal{background:#FFFFFF;border-radius:24px 24px 0 0;width:100%;max-width:480px;max-height:92vh;overflow-y:auto;animation:poModalUp .28s cubic-bezier(.34,1.2,.64,1)}
@media(min-width:640px){.po-modal{border-radius:20px}}
@keyframes poModalUp{from{transform:translateY(100%);opacity:0;}to{transform:translateY(0);opacity:1;}}
.po-modal::before{content:'';display:block;width:40px;height:4px;background:#E0E0E0;border-radius:100px;margin:10px auto 0}
@media(min-width:640px){.po-modal::before{display:none}}
.po-modal-head{display:flex;align-items:center;gap:12px;padding:20px 22px 14px;border-bottom:1px solid #F0F0F0}
.po-modal-icon{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;flex-shrink:0;background:#FFEBEE;color:#DC2626;border:1px solid #FFCDD2}
.po-modal-heading{flex:1;min-width:0}
.po-modal-title{font-size:15px;font-weight:800;color:#1A1A1A;letter-spacing:-.02em;margin-bottom:2px}
.po-modal-sub{font-size:11px;color:#757575;font-family:ui-monospace,monospace;font-weight:600}
.po-modal-body{padding:16px 22px}
.po-modal-warn{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:#FFF8E1;border:1px solid #FDE68A;border-radius:10px;font-size:11.5px;color:#92400E;line-height:1.5;margin-bottom:14px}
.po-modal-warn svg{flex-shrink:0;margin-top:2px;color:#D97706}
.po-modal-warn strong{color:#B45309;font-weight:800}
.po-modal-summary{padding:10px 12px;background:#FAFAFA;border:1px solid #F0F0F0;border-radius:10px;margin-bottom:14px}
.po-modal-summary-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:11.5px}
.po-modal-summary-row+.po-modal-summary-row{border-top:1px solid #F0F0F0}
.po-modal-summary-row span{color:#757575;font-weight:600}
.po-modal-summary-row strong{color:#1A1A1A;font-weight:800;font-family:ui-monospace,monospace}
.po-modal-summary-row strong.gold{color:#D4901F}
.po-modal-label{display:block;font-size:10.5px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px}
.po-modal-optional{color:#B8A29A;font-weight:600;letter-spacing:0;text-transform:none;font-size:10px}
.po-modal-textarea{width:100%;padding:11px 13px;background:#FAFAFA;border:1.5px solid #EEEEEE;border-radius:10px;color:#1A1A1A;font-size:12.5px;font-family:inherit;resize:vertical;min-height:70px;outline:none;transition:all .15s;font-weight:500}
.po-modal-textarea:focus{border-color:#DC2626;background:#FFFFFF;box-shadow:0 0 0 3px rgba(220,38,38,.1)}
.po-modal-textarea::placeholder{color:#B8A29A}
.po-modal-foot{display:flex;gap:8px;padding:14px 22px 22px;border-top:1px solid #F0F0F0}
.po-modal-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:12px 18px;border-radius:100px;font-size:12.5px;font-weight:800;border:1px solid;cursor:pointer;font-family:inherit;transition:all .15s;min-height:44px}
.po-modal-btn-ghost{flex:1;background:#FFFFFF;border-color:#EEEEEE;color:#757575}
.po-modal-btn-ghost:hover{background:#FAFAFA;border-color:#D0D0D0;color:#1A1A1A}
.po-modal-btn-danger{background:#DC2626;border-color:#DC2626;color:#FFF;box-shadow:0 4px 12px rgba(220,38,38,.3)}
.po-modal-btn-danger:hover{background:#B91C1C;border-color:#B91C1C}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.po-hero{padding:16px}
.po-hero-num{font-size:15px}
.po-hero-amt{font-size:26px}
.po-chip{padding:10px;gap:8px}
.po-chip-icon{width:28px;height:28px}
.po-chip-value{font-size:13px}
.po-total-val{font-size:20px}
}
</style>
@endpush

@push('scripts')
<script>
function openCancelModal() {
    document.getElementById('cancelModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(function() { document.getElementById('cancelReason').focus(); }, 100);
}
function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('cancelForm');
    if (form) {
        form.addEventListener('submit', function() {
            document.getElementById('cancelReasonInput').value = document.getElementById('cancelReason').value;
        });
    }
    var topbarInner = document.querySelector('.p-topbar-inner');
    if (topbarInner) {
        topbarInner.innerHTML = '<div class="p-topbar-store-text"><div class="p-topbar-store-name" style="font-size:16px;font-weight:800;">Order Details</div></div><a href="{{ route('portal.orders.index') }}" class="po-topbar-btn" aria-label="Back"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>';
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCancelModal();
});
</script>
<style>
.po-topbar-btn{display:grid;place-items:center;width:36px;height:36px;color:#1A1A1A;text-decoration:none;border-radius:50%;transition:background .15s;flex-shrink:0}
.po-topbar-btn:hover{background:#F5F5F5}
.po-topbar-btn svg{width:20px;height:20px}
</style>
@endpush
