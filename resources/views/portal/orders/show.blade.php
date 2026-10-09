@extends('portal.layouts.app')

@section('title', $order->request_number)

@section('content')

@php
    $st = strtolower($order->status);
    $statusConfig = [
        'pending'  => ['label' => 'Pending',  'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.12)', 'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>'],
        'approved' => ['label' => 'Approved', 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.12)',  'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>'],
        'rejected' => ['label' => 'Rejected', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.12)',  'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>'],
    ];
    $s = $statusConfig[$st] ?? $statusConfig['pending'];
    $itemCount = $order->items->count();
    $totalQty = $order->items->sum('quantity_requested');
@endphp

{{-- BACK --}}
<a href="{{ route('portal.orders.index') }}" class="po-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back to Orders
</a>

{{-- HERO --}}
<div class="po-hero" style="--status-color: {{ $s['color'] }};">
    <div class="po-hero-icon" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }};">
        {!! $s['icon'] !!}
    </div>

    <div class="po-hero-content">
        <div class="po-hero-label" style="color: {{ $s['color'] }};">{{ $s['label'] }}</div>
        <div class="po-hero-title">{{ $order->request_number }}</div>
        <div class="po-hero-meta">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
            {{ $order->created_at->format('M d, Y · g:i A') }}
        </div>
    </div>
</div>

{{-- STATUS INFO --}}
@if($st === 'approved' && $order->isApproved())
    <div class="po-info po-info-green">
        <div class="po-info-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4M12 8h.01"/>
            </svg>
        </div>
        <div class="po-info-body">
            <div class="po-info-title">Order Approved</div>
            <div class="po-info-text">
                @if($order->approved_at)
                    Approved {{ $order->approved_at->diffForHumans() }}
                @endif
                @if($order->deliveryReceipt)
                    · DR: {{ $order->deliveryReceipt->dr_number }}
                @endif
            </div>
        </div>
    </div>
@elseif($st === 'pending')
    <div class="po-info po-info-amber">
        <div class="po-info-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="po-info-body">
            <div class="po-info-title">Waiting for Approval</div>
            <div class="po-info-text">Admin pa ang nag-review niini nga request</div>
        </div>
    </div>
@elseif($st === 'rejected')
    <div class="po-info po-info-red">
        <div class="po-info-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 8v4M12 16h.01"/>
            </svg>
        </div>
        <div class="po-info-body">
            <div class="po-info-title">Order Rejected</div>
            @if($order->rejection_reason)
                <div class="po-info-text">Reason: {{ $order->rejection_reason }}</div>
            @endif
        </div>
    </div>
@endif

{{-- SUMMARY CHIPS --}}
<div class="po-chips">
    <div class="po-chip">
        <div class="po-chip-label">Items</div>
        <div class="po-chip-value">{{ $itemCount }}</div>
    </div>
    <div class="po-chip">
        <div class="po-chip-label">Total Qty</div>
        <div class="po-chip-value">{{ $totalQty }}</div>
    </div>
    <div class="po-chip">
        <div class="po-chip-label">Total</div>
        <div class="po-chip-value gold">&#8369;{{ number_format($order->total_amount, 2) }}</div>
    </div>
</div>

{{-- ITEMS CARD --}}
<div class="po-card">
    <div class="po-card-head">
        <div class="po-card-head-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div>
            <div class="po-card-head-title">Requested Items</div>
            <div class="po-card-head-sub">{{ $itemCount }} product(s)</div>
        </div>
    </div>

    <div class="po-items">
        @foreach($order->items as $item)
            <div class="po-item">
                <div class="po-item-thumb">
                    @if($item->product && $item->product->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                    @else
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>
                <div class="po-item-info">
                    <div class="po-item-name">{{ $item->product->name ?? '-' }}</div>
                    <div class="po-item-meta">
                        {{ $item->quantity_requested }} Ã— &#8369;{{ number_format($item->unit_price, 2) }}
                    </div>
                </div>
                <div class="po-item-total">&#8369;{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
    </div>

    <div class="po-total">
        <span class="po-total-label">Total</span>
        <span class="po-total-value">&#8369;{{ number_format($order->total_amount, 2) }}</span>
    </div>
</div>

{{-- NOTES --}}
@if($order->notes)
    <div class="po-card">
        <div class="po-card-head">
            <div class="po-card-head-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <path d="M14 2v6h6"/>
                </svg>
            </div>
            <div class="po-card-head-title">Notes</div>
        </div>
        <div class="po-notes">{{ $order->notes }}</div>
    </div>
@endif

{{-- CANCEL BUTTON --}}
@if($order->isPending())
    <button type="button" class="po-btn-cancel" onclick="openCancelModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M15 9l-6 6M9 9l6 6"/>
        </svg>
        Cancel Order
    </button>
@endif

{{-- ========== CANCEL MODAL ========== --}}
<div id="cancelModal" class="po-modal-overlay" onclick="if(event.target === this) closeCancelModal()">
    <div class="po-modal">
        <div class="po-modal-head">
            <div class="po-modal-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M15 9l-6 6M9 9l6 6"/>
                </svg>
            </div>
            <div class="po-modal-heading">
                <div class="po-modal-title">Cancel this order?</div>
                <div class="po-modal-sub">{{ $order->request_number }}</div>
            </div>
        </div>

        <div class="po-modal-body">
            <div class="po-modal-warn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <strong>Dili na ma-undo.</strong> Ma-notify ang admin sa cancellation ug ma-remove ni nga request sa pending list.
                </div>
            </div>

            <div class="po-modal-summary">
                <div class="po-modal-summary-row">
                    <span>Order #</span>
                    <strong>{{ $order->request_number }}</strong>
                </div>
                <div class="po-modal-summary-row">
                    <span>Items</span>
                    <strong>{{ $order->items->count() }} product(s)</strong>
                </div>
                <div class="po-modal-summary-row">
                    <span>Total</span>
                    <strong class="gold">&#8369;{{ number_format($order->total_amount, 2) }}</strong>
                </div>
            </div>

            <div class="po-modal-field">
                <label class="po-modal-label">Reason <span class="po-modal-optional">(optional)</span></label>
                <textarea id="cancelReason" rows="3" class="po-modal-textarea" placeholder="Ngano gi-cancel nimo ni nga order?"></textarea>
            </div>
        </div>

        <div class="po-modal-foot">
            <button type="button" class="po-modal-btn po-modal-btn-ghost" onclick="closeCancelModal()">
                Keep Order
            </button>
            <form method="POST" action="{{ route('portal.orders.cancel', $order->id) }}" id="cancelForm" style="flex:1; margin:0;">
                @csrf
                <input type="hidden" name="cancel_reason" id="cancelReasonInput">
                <button type="submit" class="po-modal-btn po-modal-btn-danger" style="width:100%;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M15 9l-6 6M9 9l6 6"/>
                    </svg>
                    Confirm Cancel
                </button>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
    function openCancelModal() {
        document.getElementById('cancelModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            document.getElementById('cancelReason').focus();
        }, 100);
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Copy reason textarea value to hidden input before submit
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('cancelForm');
        if (form) {
            form.addEventListener('submit', function() {
                const reason = document.getElementById('cancelReason').value;
                document.getElementById('cancelReasonInput').value = reason;
            });
        }
    });

    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCancelModal();
    });
</script>
@endpush
@endsection

@push('styles')
<style>
    /* ============ PORTAL ORDER SHOW "” PRO MOBILE ============ */

    /* BACK */
    .po-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px 8px 10px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        color: var(--text-secondary);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 14px;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .po-back:hover {
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-primary);
    }
    .po-back:active { transform: scale(0.97); }

    /* HERO */
    .po-hero {
        position: relative;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        margin-bottom: 14px;
        background: linear-gradient(135deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid var(--status-color);
        border-radius: 16px;
        overflow: hidden;
    }
    .po-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 200px; height: 200px;
        background: radial-gradient(circle, var(--status-color), transparent 70%);
        opacity: 0.1;
        pointer-events: none;
    }
    .po-hero-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: grid; place-items: center;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative; z-index: 1;
    }
    .po-hero-content { flex: 1; min-width: 0; position: relative; z-index: 1; }
    .po-hero-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 5px;
    }
    .po-hero-title {
        font-size: 19px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        font-family: ui-monospace, monospace;
        line-height: 1.1;
        margin-bottom: 8px;
    }
    .po-hero-meta {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* INFO BANNERS */
    .po-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid;
        margin-bottom: 14px;
    }
    .po-info-green {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.02));
        border-color: rgba(34, 197, 94, 0.25);
    }
    .po-info-amber {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.02));
        border-color: rgba(245, 158, 11, 0.25);
    }
    .po-info-red {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.02));
        border-color: rgba(239, 68, 68, 0.25);
    }
    .po-info-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .po-info-green .po-info-icon { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .po-info-amber .po-info-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .po-info-red   .po-info-icon { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .po-info-body { flex: 1; min-width: 0; }
    .po-info-title {
        font-size: 13px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 3px;
        letter-spacing: -0.01em;
    }
    .po-info-text {
        font-size: 11.5px;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* CHIPS */
    .po-chips {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 14px;
    }
    .po-chip {
        padding: 12px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        text-align: center;
    }
    .po-chip-label {
        font-size: 9.5px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 5px;
    }
    .po-chip-value {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .po-chip-value.gold { color: #c9a961; }

    /* CARD */
    .po-card {
        padding: 16px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        margin-bottom: 14px;
    }
    .po-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .po-card-head-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .po-card-head-icon.blue {
        background: rgba(59, 130, 246, 0.12);
        color: #3b82f6;
    }
    .po-card-head-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }
    .po-card-head-sub {
        font-size: 10.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ITEMS */
    .po-items { display: flex; flex-direction: column; }
    .po-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .po-item:last-child { border-bottom: none; }
    .po-item-thumb {
        width: 44px; height: 44px;
        border-radius: 11px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.5);
        overflow: hidden;
        flex-shrink: 0;
    }
    .po-item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .po-item-info { flex: 1; min-width: 0; }
    .po-item-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .po-item-meta {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }
    .po-item-total {
        font-size: 14px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }

    /* TOTAL */
    .po-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        margin-top: 6px;
        border-top: 2px solid rgba(201, 169, 97, 0.25);
    }
    .po-total-label {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .po-total-value {
        font-size: 22px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    /* NOTES */
    .po-notes {
        font-size: 12.5px;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* CANCEL */
    .po-btn-cancel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 13px;
        color: #ef4444;
        font-size: 13px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .po-btn-cancel:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.45);
    }
    .po-btn-cancel:active { transform: scale(0.98); }

    /* RESPONSIVE */
    @media (max-width: 480px) {
        .po-hero { padding: 16px; gap: 12px; }
        .po-hero-icon { width: 46px; height: 46px; }
        .po-hero-title { font-size: 17px; }
        .po-card { padding: 14px; }
        .po-total-value { font-size: 20px; }
        .po-chip-value { font-size: 15px; }
    }

    /* ========== CANCEL MODAL ========== */
    .po-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(8px);
        display: none;
        align-items: flex-end;
        justify-content: center;
        z-index: 9999;
        padding: 0;
    }
    .po-modal-overlay.active { display: flex; }

    @media (min-width: 640px) {
        .po-modal-overlay {
            align-items: center;
            padding: 20px;
        }
    }

    .po-modal {
        background: linear-gradient(165deg, #1e1a16, #15120f);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 22px 22px 0 0;
        width: 100%;
        max-width: 480px;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 -20px 60px -20px rgba(0, 0, 0, 0.8);
        animation: poModalUp 0.28s cubic-bezier(0.34, 1.2, 0.64, 1);
    }
    @media (min-width: 640px) {
        .po-modal {
            border-radius: 20px;
            box-shadow: 0 30px 80px -20px rgba(0, 0, 0, 0.9);
            animation: poModalIn 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    }
    @keyframes poModalUp {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }
    @keyframes poModalIn {
        from { transform: scale(0.94) translateY(12px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }

    /* Drag handle for mobile */
    .po-modal::before {
        content: '';
        display: block;
        width: 40px; height: 4px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 100px;
        margin: 10px auto 0;
    }
    @media (min-width: 640px) {
        .po-modal::before { display: none; }
    }

    .po-modal-head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 22px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .po-modal-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: grid; place-items: center;
        flex-shrink: 0;
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .po-modal-heading { flex: 1; min-width: 0; }
    .po-modal-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        margin-bottom: 3px;
    }
    .po-modal-sub {
        font-size: 11px;
        color: var(--text-muted);
        font-family: ui-monospace, monospace;
    }

    .po-modal-body {
        padding: 18px 22px;
    }

    .po-modal-warn {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 12px 14px;
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.22);
        border-radius: 12px;
        font-size: 11.5px;
        color: #d4a35a;
        line-height: 1.55;
        margin-bottom: 16px;
    }
    .po-modal-warn svg { flex-shrink: 0; margin-top: 2px; color: #f59e0b; }
    .po-modal-warn strong { color: #f59e0b; font-weight: 800; }

    .po-modal-summary {
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.25);
        border-radius: 12px;
        margin-bottom: 16px;
    }
    .po-modal-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 12px;
    }
    .po-modal-summary-row + .po-modal-summary-row {
        border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
    .po-modal-summary-row span { color: var(--text-muted); font-weight: 600; }
    .po-modal-summary-row strong {
        color: var(--text-primary);
        font-weight: 800;
        font-family: ui-monospace, monospace;
    }
    .po-modal-summary-row strong.gold { color: #c9a961; }

    .po-modal-field { margin-top: 4px; }
    .po-modal-label {
        display: block;
        font-size: 10.5px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }
    .po-modal-optional {
        color: #52525b;
        font-weight: 600;
        letter-spacing: 0;
        text-transform: none;
        font-size: 10px;
    }
    .po-modal-textarea {
        width: 100%;
        padding: 11px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        min-height: 70px;
        transition: all 0.15s;
    }
    .po-modal-textarea:focus {
        outline: none;
        border-color: rgba(239, 68, 68, 0.4);
        background: rgba(239, 68, 68, 0.03);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    .po-modal-textarea::placeholder { color: #52525b; }

    .po-modal-foot {
        display: flex;
        gap: 10px;
        padding: 16px 22px 22px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .po-modal-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 13px 18px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 800;
        border: 1px solid;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
        min-height: 46px;
    }
    .po-modal-btn-ghost {
        flex: 1;
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.1);
        color: var(--text-secondary);
    }
    .po-modal-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-primary);
    }
    .po-modal-btn-ghost:active { transform: scale(0.97); }
    .po-modal-btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 6px 18px -6px rgba(239, 68, 68, 0.5);
    }
    .po-modal-btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(239, 68, 68, 0.7);
    }
    .po-modal-btn-danger:active { transform: scale(0.98); }
</style>
@endpush