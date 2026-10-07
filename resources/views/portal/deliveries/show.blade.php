@extends('portal.layouts.app')

@section('title', $delivery->dr_number)

@section('content')

@php
    $isConfirmed = $delivery->customer_confirmed ?? false;
    $isOut = !$isConfirmed && $delivery->out_for_delivery_at;
@endphp

{{-- HEADER --}}
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.deliveries.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
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
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        @elseif($isOut)
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        @else
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        @endif
    </div>

    <div class="dv-hero-text">
        <div class="dv-hero-label">
            @if($isConfirmed)
                Customer Confirmed
            @elseif($isOut)
                Out for Delivery
            @else
                Pending
            @endif
        </div>
        <div class="dv-hero-sub">
            @if($isConfirmed)
                Confirmed {{ \Carbon\Carbon::parse($delivery->confirmed_at)->diffForHumans() }}
            @elseif($isOut)
                Marked {{ \Carbon\Carbon::parse($delivery->out_for_delivery_at)->diffForHumans() }}
            @else
                Waiting for admin to ship
            @endif
        </div>
    </div>

    <div class="dv-hero-amount">
        <div class="dv-hero-amount-label">Total</div>
        <div class="dv-hero-amount-value">&#8369;{{ number_format($delivery->total_amount, 0) }}</div>
    </div>
</div>

{{-- ITEMS CARD --}}
<div class="card">
    <div class="card-head">
        <div class="card-head-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
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
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>
                <div class="item-info">
                    <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                    <div class="item-meta">{{ $item->quantity_delivered }} × &#8369;{{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="item-total">&#8369;{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
    </div>

    <div class="total-row">
        <span>Total</span>
        <strong>&#8369;{{ number_format($delivery->total_amount, 2) }}</strong>
    </div>
</div>

{{-- PAYMENT CARD --}}
<div class="card">
    <div class="card-head">
        <div class="card-head-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div>
            <div class="card-head-title">Payment</div>
        </div>
    </div>

    <div class="balance-list">
        <div class="balance-row">
            <span class="balance-label">Total</span>
            <span class="balance-value">&#8369;{{ number_format($delivery->total_amount, 2) }}</span>
        </div>
        <div class="balance-row">
            <span class="balance-label">Paid</span>
            <span class="balance-value green">- &#8369;{{ number_format($delivery->amount_paid, 2) }}</span>
        </div>
        <div class="balance-row balance-total">
            <span class="balance-label">Balance</span>
            <span class="balance-value {{ $delivery->balance > 0 ? 'accent' : 'green' }}">&#8369;{{ number_format($delivery->balance, 2) }}</span>
        </div>
    </div>
</div>

{{-- CONFIRMATION CARD --}}
@if($isConfirmed)
    <div class="dv-action confirmed">
        <div class="dv-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
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
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
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
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                Confirm Received
            </button>
        </form>
    </div>
@else
    <div class="dv-action pending">
        <div class="dv-action-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="dv-action-text">
            <div class="dv-action-title">Waiting for Delivery</div>
            <div class="dv-action-sub">Wala pa gi-ship sa admin</div>
        </div>
    </div>
@endif


{{-- PROFESSIONAL CONFIRM RECEIVED MODAL --}}
<div id="confirmReceivedModal" class="cr-modal-overlay" style="display:none;" aria-hidden="true">
    <div class="cr-modal" role="dialog" aria-modal="true">
        <div class="cr-modal-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="cr-modal-title">Confirm Received?</div>
        <div class="cr-modal-sub">DR-<strong>{{ $delivery->dr_number }}</strong></div>
        <div class="cr-modal-body">
            <div class="cr-modal-row">
                <span>Items</span>
                <strong>{{ $delivery->items->count() }} product(s)</strong>
            </div>
            <div class="cr-modal-row">
                <span>Total</span>
                <strong style="color:#c9a961;">&#8369;{{ number_format($delivery->total_amount, 2) }}</strong>
            </div>
            @if($delivery->balance > 0)
                <div class="cr-modal-row">
                    <span>Balance</span>
                    <strong style="color:#f59e0b;">&#8369;{{ number_format($delivery->balance, 2) }}</strong>
                </div>
            @endif
        </div>
        <div class="cr-modal-warning">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            Siguroha nga <strong>nadawat nimo</strong> ang tanan products bag-o mo-confirm. Dili na ma-undo.
        </div>
        <div class="cr-modal-actions">
            <button type="button" class="cr-modal-btn-cancel" data-cr-close>Cancel</button>
            <button type="button" class="cr-modal-btn-confirm" id="crModalConfirm">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                Yes, Confirm
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* HERO */
    .dv-hero {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 18px;
        margin-bottom: 14px;
    }
    .dv-hero.confirmed {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .dv-hero.transit {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .dv-hero.pending {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .dv-hero-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .dv-hero.confirmed .dv-hero-icon { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .dv-hero.transit .dv-hero-icon { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .dv-hero.pending .dv-hero-icon { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }

    .dv-hero-text { flex: 1; min-width: 0; }
    .dv-hero-label {
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 3px;
    }
    .dv-hero.confirmed .dv-hero-label { color: #22c55e; }
    .dv-hero.transit .dv-hero-label { color: #3b82f6; }
    .dv-hero.pending .dv-hero-label { color: #f59e0b; }
    .dv-hero-sub {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    .dv-hero-amount { text-align: right; flex-shrink: 0; }
    .dv-hero-amount-label {
        font-size: 9.5px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
        margin-bottom: 3px;
    }
    .dv-hero-amount-value {
        font-size: 18px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    /* ITEMS */
    .items-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
    .item-row {
        display: flex; align-items: center; gap: 11px;
        padding: 9px 0;
        border-bottom: 1px solid var(--border);
    }
    .item-row:last-child { border-bottom: none; }
    .item-thumb {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.5);
        overflow: hidden; flex-shrink: 0;
    }
    .item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .item-info { flex: 1; min-width: 0; }
    .item-name {
        font-size: 12.5px; font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .item-meta { font-size: 10.5px; color: var(--text-muted); }
    .item-total {
        font-size: 13px; font-weight: 800;
        color: var(--accent-light);
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }

    .total-row {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 12px;
        border-top: 2px solid rgba(169, 120, 74, 0.3);
        font-size: 12.5px; color: var(--text-secondary);
    }
    .total-row strong {
        font-size: 18px; font-weight: 800;
        color: var(--accent-light);
        font-variant-numeric: tabular-nums;
    }

    /* BALANCE */
    .balance-list { display: flex; flex-direction: column; }
    .balance-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0;
        font-size: 12.5px;
    }
    .balance-row + .balance-row { border-top: 1px solid var(--border); }
    .balance-label { color: var(--text-secondary); }
    .balance-value {
        font-weight: 700; color: var(--text-primary);
        font-variant-numeric: tabular-nums;
    }
    .balance-value.green { color: var(--success); }
    .balance-value.accent { color: var(--accent-light); }
    .balance-total {
        padding-top: 12px;
        border-top: 1px solid rgba(169, 120, 74, 0.25) !important;
    }
    .balance-total .balance-value { font-size: 14px; }

    /* ACTION CARD */
    .dv-action {
        padding: 16px;
        border-radius: 16px;
        margin-top: 14px;
    }
    .dv-action.confirmed {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.25);
        display: flex; align-items: center; gap: 12px;
    }
    .dv-action.pending {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
        display: flex; align-items: center; gap: 12px;
    }
    .dv-action.transit {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(34, 34, 44, 0.5));
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .dv-action-head {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 12px;
    }

    .dv-action-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .dv-action.confirmed .dv-action-icon { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .dv-action.transit .dv-action-icon { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .dv-action.pending .dv-action-icon { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }

    .dv-action-text { flex: 1; min-width: 0; }
    .dv-action-title {
        font-size: 14px; font-weight: 800;
        margin-bottom: 2px;
    }
    .dv-action.confirmed .dv-action-title { color: #22c55e; }
    .dv-action.transit .dv-action-title { color: #3b82f6; }
    .dv-action.pending .dv-action-title { color: #f59e0b; }
    .dv-action-sub {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    .dv-action-form { display: flex; flex-direction: column; gap: 8px; }
    .dv-action-notes {
        width: 100%; padding: 10px 12px;
        background: rgba(20, 20, 26, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #f5f3f0;
        font-size: 12px;
        font-family: inherit;
        outline: none;
        resize: none;
    }
    .dv-action-notes:focus { border-color: #3b82f6; }

    .dv-action-btn {
        width: 100%; min-height: 46px;
        display: inline-flex; align-items: center; justify-content: center;
        gap: 7px;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        border: none; border-radius: 12px;
        color: #fff;
        font-size: 13.5px; font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 10px 24px -8px rgba(59, 130, 246, 0.7);
        -webkit-tap-highlight-color: transparent;
    }
    .dv-action-btn:active { transform: scale(0.98); }
</style>
@endpush

@push('styles')
<style>
    .cr-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 8, 6, 0.75);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
        animation: crFadeIn 0.2s ease;
    }
    @keyframes crFadeIn { from { opacity: 0; } to { opacity: 1; } }

    .cr-modal {
        width: 100%; max-width: 400px;
        background: linear-gradient(165deg, #1e1a16 0%, #15120f 100%);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        padding: 26px 24px 22px;
        box-shadow: 0 30px 60px -20px rgba(0,0,0,0.75),
                    0 0 0 1px rgba(255,255,255,0.03) inset,
                    0 0 80px -20px rgba(34,197,94,0.25);
        animation: crModalIn 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: center;
    }
    @keyframes crModalIn {
        from { opacity: 0; transform: scale(0.92) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .cr-modal-icon {
        width: 64px; height: 64px;
        margin: 0 auto 16px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(34,197,94,0.28), rgba(20,130,60,0.15));
        border: 1px solid rgba(34,197,94,0.35);
        color: #22c55e;
        display: grid; place-items: center;
        box-shadow: 0 10px 30px -10px rgba(34,197,94,0.5);
    }
    .cr-modal-title {
        font-size: 19px; font-weight: 800;
        color: #f5f3f0; margin-bottom: 6px;
    }
    .cr-modal-sub { font-size: 12.5px; color: #9a9188; margin-bottom: 20px; }
    .cr-modal-sub strong { color: #c9a961; font-weight: 700; }

    .cr-modal-body {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 16px;
        text-align: left;
    }
    .cr-modal-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0; font-size: 12.5px;
    }
    .cr-modal-row + .cr-modal-row { border-top: 1px solid rgba(255,255,255,0.05); }
    .cr-modal-row span { color: #8a8378; }
    .cr-modal-row strong { color: #f5f3f0; font-weight: 700; }

    .cr-modal-warning {
        display: flex; align-items: flex-start; gap: 8px;
        padding: 10px 12px;
        background: rgba(245,158,11,0.08);
        border: 1px solid rgba(245,158,11,0.22);
        border-radius: 10px;
        font-size: 11.5px; color: #d4a35a;
        text-align: left; margin-bottom: 20px; line-height: 1.45;
    }
    .cr-modal-warning svg { flex-shrink: 0; margin-top: 1px; color: #f59e0b; }
    .cr-modal-warning strong { color: #f5b945; font-weight: 700; }

    .cr-modal-actions { display: flex; gap: 10px; }
    .cr-modal-btn-cancel, .cr-modal-btn-confirm {
        flex: 1; min-height: 46px;
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        border: none; border-radius: 12px;
        font-size: 13.5px; font-weight: 800;
        font-family: inherit; cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    .cr-modal-btn-cancel {
        background: rgba(255,255,255,0.06);
        color: #c4bdb4;
        border: 1px solid rgba(255,255,255,0.08);
    }
    .cr-modal-btn-cancel:hover { background: rgba(255,255,255,0.1); color: #f5f3f0; }
    .cr-modal-btn-confirm {
        background: linear-gradient(135deg, #22c55e, #15803d);
        color: #fff;
        box-shadow: 0 10px 24px -8px rgba(34,197,94,0.7);
    }
    .cr-modal-btn-confirm:hover { background: linear-gradient(135deg, #34d977, #16a34a); }
    .cr-modal-btn-confirm:active, .cr-modal-btn-cancel:active { transform: scale(0.98); }

    @media (max-width: 480px) {
        .cr-modal { padding: 22px 18px 18px; border-radius: 18px; }
        .cr-modal-title { font-size: 17px; }
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

    if (!openBtn || !modal || !form) {
        console.warn('Confirm modal: elements not found');
        return;
    }

    openBtn.addEventListener('click', function() {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10" opacity="0.3"/><path d="M22 12a10 10 0 0 1-10 10" stroke-linecap="round"/></svg> Confirming...';
            form.submit();
        });
    }

    modal.querySelectorAll('[data-cr-close]').forEach(function(el) {
        el.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
    });

    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
})();
</script>
@endpush