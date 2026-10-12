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

{{-- ═══ ORDERS THEME-AWARE PRO OVERRIDE ═══ --}}
@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   ORDERS — PRO THEME-AWARE OVERRIDE
   Uses --t-* tokens from layout (works on all 4 themes)
   ═══════════════════════════════════════════════════════════ */

/* ═══ FORCE THEME TOKENS ON ALL ORDERS COMPONENTS ═══ */
[class^="po-"], [class*=" po-"],
[class^="or-"], [class*=" or-"],
[class^="order-"], [class*=" order-"] {
    /* Reset any hardcoded colors */
}

/* ═══ CARDS ═══ */
.po-card,
.order-card,
[class*="order-card"] {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 18px !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    text-decoration: none !important;
    display: block !important;
    position: relative !important;
    overflow: hidden !important;
}
.po-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--t-accent), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}
.po-card:hover::before { opacity: 1; }

.po-card:hover,
.order-card:hover {
    border-color: rgba(var(--t-accent-rgb), 0.4) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 28px -12px rgba(var(--t-accent-rgb), 0.3) !important;
}
.po-card:active,
.order-card:active {
    transform: scale(0.99) !important;
}

/* ═══ TITLES / TEXT ═══ */
.po-title,
.po-head .po-title {
    color: var(--t-text) !important;
}
.po-sub {
    color: var(--t-text-3) !important;
}

.po-card-num-text,
[class*="order-num-text"] {
    color: var(--t-text) !important;
    font-family: ui-monospace, monospace !important;
    font-weight: 800 !important;
}

.po-card-num-sub,
.po-card-time {
    color: var(--t-text-3) !important;
}

/* ═══ STAT CARDS ═══ */
.po-stat {
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.po-stat:hover {
    background: rgba(var(--t-accent-rgb), 0.06) !important;
    border-color: rgba(var(--t-accent-rgb), 0.35) !important;
    transform: translateY(-2px) !important;
}
.po-stat.active {
    background: rgba(var(--t-accent-rgb), 0.12) !important;
    border-color: rgba(var(--t-accent-rgb), 0.45) !important;
    box-shadow: 0 8px 20px -10px rgba(var(--t-accent-rgb), 0.4) !important;
}
.po-stat-icon {
    background: rgba(var(--t-accent-rgb), 0.14) !important;
    border-color: rgba(var(--t-accent-rgb), 0.28) !important;
    color: var(--t-accent) !important;
}
.po-stat-icon.amber { background: rgba(245, 158, 11, 0.14) !important; border-color: rgba(245, 158, 11, 0.3) !important; color: #f59e0b !important; }
.po-stat-icon.green { background: rgba(34, 197, 94, 0.14) !important; border-color: rgba(34, 197, 94, 0.3) !important; color: #22c55e !important; }
.po-stat-icon.red   { background: rgba(239, 68, 68, 0.14) !important; border-color: rgba(239, 68, 68, 0.3) !important; color: #ef4444 !important; }
.po-stat-icon.blue  { background: rgba(59, 130, 246, 0.14) !important; border-color: rgba(59, 130, 246, 0.3) !important; color: #3b82f6 !important; }

.po-stat-value {
    color: var(--t-text) !important;
}
.po-stat-label {
    color: var(--t-text-3) !important;
}

/* ═══ BADGES ═══ */
.po-badge {
    font-weight: 800 !important;
    letter-spacing: 0.04em !important;
}
.po-badge.pending  { background: rgba(245, 158, 11, 0.14) !important; color: #f59e0b !important; border-color: rgba(245, 158, 11, 0.3) !important; }
.po-badge.approved { background: rgba(34, 197, 94, 0.14) !important; color: #22c55e !important; border-color: rgba(34, 197, 94, 0.3) !important; }
.po-badge.rejected { background: rgba(239, 68, 68, 0.14) !important; color: #ef4444 !important; border-color: rgba(239, 68, 68, 0.3) !important; }

/* ═══ CTA BUTTONS ═══ */
.po-cta {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.6) !important;
}
.po-cta:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 12px 28px -8px rgba(var(--t-accent-rgb), 0.8) !important;
}

.po-empty-btn {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
    color: #ffffff !important;
    box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.7) !important;
}

/* ═══ EMPTY STATE ═══ */
.po-empty {
    background: var(--t-card) !important;
    border: 1px dashed var(--t-border-2) !important;
    border-radius: 18px !important;
}
.po-empty-icon {
    background: rgba(var(--t-accent-rgb), 0.1) !important;
    border-color: rgba(var(--t-accent-rgb), 0.28) !important;
    color: var(--t-accent) !important;
}
.po-empty-title { color: var(--t-text) !important; }
.po-empty-text { color: var(--t-text-3) !important; }

/* ═══ METRICS (Items / Total) ═══ */
.po-card-metric-label {
    color: var(--t-text-3) !important;
}
.po-card-metric-value {
    color: var(--t-text) !important;
}
.po-card-metric-value.gold,
.po-card-metric-value.accent {
    color: var(--t-accent) !important;
}
.po-card-metric-divider {
    background: var(--t-border) !important;
}

/* ═══ ARROW (View) ═══ */
.po-card-arrow {
    color: var(--t-text-3) !important;
    transition: all 0.25s !important;
}
.po-card:hover .po-card-arrow {
    color: var(--t-accent) !important;
    transform: translateX(3px) !important;
}

/* ═══ SECTION CONTAINER (kung naa) ═══ */
.po-list {
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
}

/* ═══ LIGHT THEME — FORCE DARK TEXT ═══ */
html[data-theme="light"] .po-title,
html[data-theme="light"] .po-card-num-text,
html[data-theme="light"] .po-stat-value,
html[data-theme="light"] .po-card-metric-value,
html[data-theme="blue"] .po-title,
html[data-theme="blue"] .po-card-num-text,
html[data-theme="blue"] .po-stat-value,
html[data-theme="blue"] .po-card-metric-value {
    color: #0c1e3d !important;
}

html[data-theme="light"] .po-sub,
html[data-theme="light"] .po-card-num-sub,
html[data-theme="light"] .po-card-time,
html[data-theme="light"] .po-stat-label,
html[data-theme="light"] .po-card-metric-label,
html[data-theme="blue"] .po-sub,
html[data-theme="blue"] .po-card-num-sub,
html[data-theme="blue"] .po-card-time,
html[data-theme="blue"] .po-stat-label,
html[data-theme="blue"] .po-card-metric-label {
    color: #64748b !important;
}

/* ═══ DARK THEME — FORCE LIGHT TEXT ═══ */
html[data-theme="dark"] .po-title,
html[data-theme="dark"] .po-card-num-text,
html[data-theme="dark"] .po-stat-value,
html[data-theme="dark"] .po-card-metric-value {
    color: #ffffff !important;
}

/* ═══ ANIMATIONS ═══ */
@keyframes poSlideIn {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.po-card, .po-stat, .po-empty {
    animation: poSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) both;
}
.po-card:nth-child(1) { animation-delay: 0.02s; }
.po-card:nth-child(2) { animation-delay: 0.06s; }
.po-card:nth-child(3) { animation-delay: 0.10s; }
.po-card:nth-child(4) { animation-delay: 0.14s; }
.po-card:nth-child(5) { animation-delay: 0.18s; }
.po-card:nth-child(6) { animation-delay: 0.22s; }

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .po-card { padding: 14px !important; }
    .po-stats { gap: 8px !important; }
}
</style>
@endpush

{{-- ═══ ORDERS SHOW — PRO THEME OVERRIDE ═══ --}}
@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   ORDER SHOW — Full theme-aware redesign
   ═══════════════════════════════════════════════════════════ */

/* ═══ BACK LINK ═══ */
.po-back {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 12px !important;
    margin-bottom: 14px !important;
    background: var(--t-input) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 10px !important;
    color: var(--t-text-2) !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.po-back:hover {
    background: var(--t-card) !important;
    border-color: rgba(var(--t-accent-rgb), 0.35) !important;
    color: var(--t-accent) !important;
    transform: translateX(-2px) !important;
}

/* ═══ HERO ═══ */
.po-hero {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    gap: 16px !important;
    padding: 20px !important;
    margin-bottom: 14px !important;
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 18px !important;
    overflow: hidden !important;
    animation: poShowIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) both !important;
}
.po-hero::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important; left: 0 !important; right: 0 !important;
    height: 3px !important;
    background: linear-gradient(90deg, transparent, var(--status-color, var(--t-accent)), transparent) !important;
    opacity: 0.8 !important;
}
.po-hero-icon {
    width: 56px !important;
    height: 56px !important;
    border-radius: 16px !important;
    display: grid !important;
    place-items: center !important;
    flex-shrink: 0 !important;
    box-shadow: 0 8px 20px -8px currentColor !important;
}
.po-hero-content { flex: 1 !important; min-width: 0 !important; }
.po-hero-label {
    font-size: 11px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.12em !important;
    margin-bottom: 6px !important;
}
.po-hero-title {
    font-size: 22px !important;
    font-weight: 800 !important;
    color: var(--t-text) !important;
    font-family: ui-monospace, monospace !important;
    letter-spacing: -0.02em !important;
    line-height: 1.2 !important;
    margin-bottom: 6px !important;
}
.po-hero-meta {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    font-size: 11.5px !important;
    color: var(--t-text-3) !important;
}

/* ═══ INFO BANNER ═══ */
.po-info {
    display: flex !important;
    align-items: flex-start !important;
    gap: 14px !important;
    padding: 16px 18px !important;
    margin-bottom: 14px !important;
    border-radius: 14px !important;
    border: 1px solid !important;
    animation: poShowIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) both !important;
    animation-delay: 0.05s !important;
}
.po-info-green {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.03)) !important;
    border-color: rgba(34, 197, 94, 0.35) !important;
    border-left: 3px solid #22c55e !important;
}
.po-info-amber {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.03)) !important;
    border-color: rgba(245, 158, 11, 0.35) !important;
    border-left: 3px solid #f59e0b !important;
}
.po-info-red {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(239, 68, 68, 0.03)) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
    border-left: 3px solid #ef4444 !important;
}
.po-info-icon {
    width: 40px !important;
    height: 40px !important;
    border-radius: 11px !important;
    display: grid !important;
    place-items: center !important;
    flex-shrink: 0 !important;
}
.po-info-green .po-info-icon { background: rgba(34, 197, 94, 0.15) !important; color: #22c55e !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.po-info-amber .po-info-icon { background: rgba(245, 158, 11, 0.15) !important; color: #f59e0b !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.po-info-red   .po-info-icon { background: rgba(239, 68, 68, 0.15) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.po-info-body { flex: 1 !important; min-width: 0 !important; }
.po-info-title {
    font-size: 14px !important;
    font-weight: 800 !important;
    margin-bottom: 4px !important;
    line-height: 1.3 !important;
}
.po-info-green .po-info-title { color: #22c55e !important; }
.po-info-amber .po-info-title { color: #f59e0b !important; }
.po-info-red   .po-info-title { color: #ef4444 !important; }
.po-info-text {
    font-size: 12px !important;
    line-height: 1.5 !important;
    color: var(--t-text-2) !important;
    opacity: 0.9 !important;
}
.po-info-green .po-info-text { color: #86efac !important; }
.po-info-amber .po-info-text { color: #fcd34d !important; }
.po-info-red   .po-info-text { color: #fca5a5 !important; }

/* ═══ CHIPS ═══ */
.po-chips {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 10px !important;
    margin-bottom: 14px !important;
}
.po-chip {
    padding: 14px 12px !important;
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 14px !important;
    text-align: center !important;
    transition: all 0.2s !important;
}
.po-chip:hover {
    border-color: rgba(var(--t-accent-rgb), 0.35) !important;
    transform: translateY(-1px) !important;
}
.po-chip-label {
    font-size: 10px !important;
    font-weight: 800 !important;
    color: var(--t-text-3) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    margin-bottom: 6px !important;
}
.po-chip-value {
    font-size: 18px !important;
    font-weight: 800 !important;
    color: var(--t-text) !important;
    font-variant-numeric: tabular-nums !important;
    letter-spacing: -0.02em !important;
}
.po-chip-value.gold {
    color: var(--t-accent) !important;
    font-size: 16px !important;
}

/* ═══ CARDS ═══ */
.po-card {
    padding: 18px !important;
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 18px !important;
    margin-bottom: 14px !important;
    transition: all 0.25s !important;
}
.po-card:hover {
    border-color: rgba(var(--t-accent-rgb), 0.28) !important;
    box-shadow: 0 8px 24px -12px rgba(var(--t-accent-rgb), 0.2) !important;
}
.po-card-head {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    margin-bottom: 14px !important;
    padding-bottom: 12px !important;
    border-bottom: 1px solid var(--t-border) !important;
}
.po-card-head-icon {
    width: 40px !important;
    height: 40px !important;
    border-radius: 12px !important;
    background: rgba(var(--t-accent-rgb), 0.12) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.25) !important;
    color: var(--t-accent) !important;
    display: grid !important;
    place-items: center !important;
    flex-shrink: 0 !important;
}
.po-card-head-icon.blue {
    background: rgba(59, 130, 246, 0.12) !important;
    border-color: rgba(59, 130, 246, 0.25) !important;
    color: #3b82f6 !important;
}
.po-card-head-title {
    font-size: 14px !important;
    font-weight: 800 !important;
    color: var(--t-text) !important;
    letter-spacing: -0.01em !important;
}
.po-card-head-sub {
    font-size: 11.5px !important;
    color: var(--t-text-3) !important;
    margin-top: 2px !important;
}

/* ═══ ITEMS LIST ═══ */
.po-items {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
}
.po-item {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 12px 0 !important;
    border-bottom: 1px solid var(--t-border) !important;
    transition: all 0.15s !important;
}
.po-item:last-child { border-bottom: none !important; }
.po-item:hover {
    background: rgba(var(--t-accent-rgb), 0.04) !important;
    margin: 0 -12px !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
    border-radius: 10px !important;
}
.po-item-thumb {
    width: 46px !important;
    height: 46px !important;
    border-radius: 12px !important;
    background: rgba(var(--t-accent-rgb), 0.1) !important;
    border: 1px solid rgba(var(--t-accent-rgb), 0.2) !important;
    display: grid !important;
    place-items: center !important;
    color: var(--t-accent) !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
}
.po-item-thumb img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
.po-item-info { flex: 1 !important; min-width: 0 !important; }
.po-item-name {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: var(--t-text) !important;
    margin-bottom: 3px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.po-item-meta {
    font-size: 11.5px !important;
    color: var(--t-text-3) !important;
    font-variant-numeric: tabular-nums !important;
}
.po-item-total {
    font-size: 14px !important;
    font-weight: 800 !important;
    color: var(--t-accent) !important;
    font-variant-numeric: tabular-nums !important;
    flex-shrink: 0 !important;
    letter-spacing: -0.01em !important;
}

/* ═══ TOTAL ROW ═══ */
.po-total {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding-top: 14px !important;
    margin-top: 8px !important;
    border-top: 2px solid rgba(var(--t-accent-rgb), 0.28) !important;
}
.po-total-label {
    font-size: 12.5px !important;
    color: var(--t-text-2) !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
}
.po-total-value {
    font-size: 20px !important;
    font-weight: 800 !important;
    color: var(--t-accent) !important;
    font-variant-numeric: tabular-nums !important;
    letter-spacing: -0.02em !important;
}

/* ═══ NOTES ═══ */
.po-notes {
    padding: 12px 14px !important;
    background: rgba(var(--t-accent-rgb), 0.05) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 12px !important;
    font-size: 12.5px !important;
    line-height: 1.55 !important;
    color: var(--t-text-2) !important;
    white-space: pre-wrap !important;
}

/* ═══ CANCEL BUTTON ═══ */
.po-btn-cancel {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    width: 100% !important;
    min-height: 48px !important;
    padding: 0 20px !important;
    margin-bottom: 14px !important;
    background: rgba(239, 68, 68, 0.1) !important;
    border: 1.5px solid rgba(239, 68, 68, 0.4) !important;
    border-radius: 13px !important;
    color: #ef4444 !important;
    font-family: inherit !important;
    font-size: 13px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    -webkit-tap-highlight-color: transparent !important;
}
.po-btn-cancel:hover {
    background: rgba(239, 68, 68, 0.18) !important;
    border-color: rgba(239, 68, 68, 0.65) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 20px -8px rgba(239, 68, 68, 0.5) !important;
}
.po-btn-cancel:active { transform: scale(0.98) !important; }

/* ═══ CANCEL MODAL ═══ */
.po-modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    z-index: 9999 !important;
    background: rgba(5, 5, 16, 0.7) !important;
    backdrop-filter: blur(12px) saturate(1.4) !important;
    -webkit-backdrop-filter: blur(12px) saturate(1.4) !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 20px !important;
    animation: poShowIn 0.2s ease !important;
}
.po-modal-overlay.open { display: flex !important; }
.po-modal {
    width: 100% !important;
    max-width: 400px !important;
    background: var(--t-card) !important;
    border: 1px solid var(--t-border) !important;
    border-radius: 20px !important;
    padding: 24px 22px 20px !important;
    box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.7) !important;
    animation: poModalIn 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
    text-align: center !important;
}
.po-modal-head {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 14px !important;
    margin-bottom: 18px !important;
}
.po-modal-icon {
    width: 60px !important;
    height: 60px !important;
    border-radius: 18px !important;
    background: rgba(239, 68, 68, 0.14) !important;
    border: 1px solid rgba(239, 68, 68, 0.35) !important;
    color: #ef4444 !important;
    display: grid !important;
    place-items: center !important;
    box-shadow: 0 10px 30px -10px rgba(239, 68, 68, 0.5) !important;
}
.po-modal-heading { text-align: center !important; }
.po-modal-title {
    font-size: 18px !important;
    font-weight: 800 !important;
    color: var(--t-text) !important;
    margin-bottom: 4px !important;
}
.po-modal-sub {
    font-size: 12.5px !important;
    color: var(--t-text-3) !important;
    font-family: ui-monospace, monospace !important;
}
.po-modal-body { margin-bottom: 20px !important; }
.po-modal-warn {
    display: flex !important;
    align-items: flex-start !important;
    gap: 8px !important;
    padding: 12px 14px !important;
    background: rgba(245, 158, 11, 0.08) !important;
    border: 1px solid rgba(245, 158, 11, 0.28) !important;
    border-radius: 12px !important;
    font-size: 12px !important;
    color: #d4a35a !important;
    text-align: left !important;
    line-height: 1.5 !important;
}
.po-modal-warn svg { flex-shrink: 0 !important; margin-top: 1px !important; color: #f59e0b !important; }
.po-modal-warn strong { color: #fbbf24 !important; font-weight: 700 !important; }

/* ═══ ANIMATIONS ═══ */
@keyframes poShowIn {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes poModalIn {
    0% { opacity: 0; transform: scale(0.92) translateY(10px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

/* ═══ LIGHT THEME — Force dark text ═══ */
html[data-theme="light"] .po-hero-title,
html[data-theme="light"] .po-card-head-title,
html[data-theme="light"] .po-item-name,
html[data-theme="light"] .po-chip-value,
html[data-theme="light"] .po-modal-title,
html[data-theme="blue"] .po-hero-title,
html[data-theme="blue"] .po-card-head-title,
html[data-theme="blue"] .po-item-name,
html[data-theme="blue"] .po-chip-value,
html[data-theme="blue"] .po-modal-title {
    color: #0c1e3d !important;
}
html[data-theme="light"] .po-hero-meta,
html[data-theme="light"] .po-card-head-sub,
html[data-theme="light"] .po-item-meta,
html[data-theme="light"] .po-chip-label,
html[data-theme="light"] .po-total-label,
html[data-theme="light"] .po-modal-sub,
html[data-theme="blue"] .po-hero-meta,
html[data-theme="blue"] .po-card-head-sub,
html[data-theme="blue"] .po-item-meta,
html[data-theme="blue"] .po-chip-label,
html[data-theme="blue"] .po-total-label,
html[data-theme="blue"] .po-modal-sub {
    color: #64748b !important;
}
html[data-theme="light"] .po-notes,
html[data-theme="blue"] .po-notes {
    color: #334155 !important;
    background: rgba(71, 85, 105, 0.06) !important;
}

/* ═══ DARK THEME — Force light text ═══ */
html[data-theme="dark"] .po-hero-title,
html[data-theme="dark"] .po-card-head-title,
html[data-theme="dark"] .po-item-name,
html[data-theme="dark"] .po-chip-value,
html[data-theme="dark"] .po-modal-title {
    color: #ffffff !important;
}

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .po-hero { padding: 16px !important; gap: 12px !important; }
    .po-hero-icon { width: 48px !important; height: 48px !important; border-radius: 13px !important; }
    .po-hero-title { font-size: 18px !important; }
    .po-chip { padding: 12px 8px !important; }
    .po-chip-value { font-size: 15px !important; }
    .po-chip-value.gold { font-size: 13px !important; }
    .po-card { padding: 14px !important; }
    .po-item-thumb { width: 42px !important; height: 42px !important; }
    .po-modal { padding: 20px 18px 16px !important; }
    .po-modal-title { font-size: 16px !important; }
}
</style>
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
        background: var(--t-border);
        border: 1px solid var(--t-border);
        border-radius: 10px;
        color: var(--t-text-2);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 14px;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .po-back:hover {
        background: var(--t-border-2);
        color: var(--t-text);
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
        background: var(--t-card);
        border: 1px solid var(--t-border);
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
        border: 1px solid var(--t-border-2);
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
        color: var(--t-text);
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
        color: var(--t-text-3);
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
        color: var(--t-text);
        margin-bottom: 3px;
        letter-spacing: -0.01em;
    }
    .po-info-text {
        font-size: 11.5px;
        color: var(--t-text-2);
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
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 12px;
        text-align: center;
    }
    .po-chip-label {
        font-size: 9.5px;
        font-weight: 800;
        color: var(--t-text-3);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 5px;
    }
    .po-chip-value {
        font-size: 17px;
        font-weight: 800;
        color: var(--t-text);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .po-chip-value.gold { color: var(--t-accent); }

    /* CARD */
    .po-card {
        padding: 16px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 16px;
        margin-bottom: 14px;
    }
    .po-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid var(--t-border);
    }
    .po-card-head-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: rgba(var(--t-accent-rgb), 0.12);
        color: var(--t-accent);
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
        color: var(--t-text);
        letter-spacing: -0.01em;
    }
    .po-card-head-sub {
        font-size: 10.5px;
        color: var(--t-text-3);
        margin-top: 2px;
    }

    /* ITEMS */
    .po-items { display: flex; flex-direction: column; }
    .po-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--t-border);
    }
    .po-item:last-child { border-bottom: none; }
    .po-item-thumb {
        width: 44px; height: 44px;
        border-radius: 11px;
        background: rgba(var(--t-accent-rgb), 0.08);
        border: 1px solid rgba(var(--t-accent-rgb), 0.15);
        display: grid; place-items: center;
        color: rgba(var(--t-accent-rgb), 0.5);
        overflow: hidden;
        flex-shrink: 0;
    }
    .po-item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .po-item-info { flex: 1; min-width: 0; }
    .po-item-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--t-text);
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .po-item-meta {
        font-size: 11px;
        color: var(--t-text-3);
        font-weight: 600;
    }
    .po-item-total {
        font-size: 14px;
        font-weight: 800;
        color: var(--t-accent);
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
        border-top: 2px solid rgba(var(--t-accent-rgb), 0.25);
    }
    .po-total-label {
        font-size: 12px;
        font-weight: 800;
        color: var(--t-text-3);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .po-total-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--t-accent);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    /* NOTES */
    .po-notes {
        font-size: 12.5px;
        color: var(--t-text-2);
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
        background: var(--t-card-solid);
        border: 1px solid var(--t-border-2);
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
        border-bottom: 1px solid var(--t-border);
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
        color: var(--t-text);
        letter-spacing: -0.02em;
        margin-bottom: 3px;
    }
    .po-modal-sub {
        font-size: 11px;
        color: var(--t-text-3);
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
        border-top: 1px solid var(--t-border);
    }
    .po-modal-summary-row span { color: var(--t-text-3); font-weight: 600; }
    .po-modal-summary-row strong {
        color: var(--t-text);
        font-weight: 800;
        font-family: ui-monospace, monospace;
    }
    .po-modal-summary-row strong.gold { color: var(--t-accent); }

    .po-modal-field { margin-top: 4px; }
    .po-modal-label {
        display: block;
        font-size: 10.5px;
        font-weight: 800;
        color: var(--t-text-3);
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
        border: 1px solid var(--t-border-2);
        border-radius: 11px;
        color: var(--t-text);
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
        border-top: 1px solid var(--t-border);
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
        background: var(--t-border);
        border-color: var(--t-border-2);
        color: var(--t-text-2);
    }
    .po-modal-btn-ghost:hover {
        background: var(--t-border-2);
        color: var(--t-text);
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

    /* ════════════════════════════════════════════════════════
       LIGHT MODE — EMERALD GREEN FORCE OVERRIDE
       Bisag unsang gold hardcoded → emerald
       ════════════════════════════════════════════════════════ */

    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #059669 !important;
    }

    /* Force emerald sa tanan accent colors sa light theme */
    html[data-theme="light"] .p-main *[style*="color: #c9a961"] {
        color: #10b981 !important;
    }

    /* Kill any gold shadows */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.5) !important;
    }

    /* Override gold gradient backgrounds */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"] {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }

    /* Force all spans/divs inside cards dark */
    html[data-theme="light"] .p-main,
    html[data-theme="light"] .p-main *:not([class*="badge"]):not([class*="status"]):not([class*="pill"]):not([class*="text-"]) {
        /* Fallback */
    }

    /* Headings */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f1e17 !important;
    }

    /* All text classes */
    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f1e17 !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #6b7f75 !important;
    }

    /* Inline hardcoded white → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f1e17 !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — PURE SLATE FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Gold/green/brown hardcoded colors → slate */
    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="#10b981"],
    html[data-theme="light"] .p-main *[style*="#059669"],
    html[data-theme="light"] .p-main *[style*="#a9784a"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #475569 !important;
    }

    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(16, 185, 129"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"] {
        color: #475569 !important;
    }

    /* Gradient override */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #10b981"] {
        background: linear-gradient(135deg, #475569, #334155) !important;
    }

    /* Gold shadow → slate shadow */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(71, 85, 105, 0.4) !important;
    }

    /* All text — dark */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #64748b !important;
    }

    /* Hardcoded white text → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f172a !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       DARK MODE — PURE BLACK FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Kill all gold/emerald/purple accents sa dark mode */
    html[data-theme="dark"] .p-main *[style*="#c9a961"],
    html[data-theme="dark"] .p-main *[style*="#10b981"],
    html[data-theme="dark"] .p-main *[style*="#8b5cf6"],
    html[data-theme="dark"] .p-main *[style*="#a9784a"],
    html[data-theme="dark"] .p-main *[style*="#8a5f36"],
    html[data-theme="dark"] .p-main *[style*="#b8944d"],
    html[data-theme="dark"] .p-main *[style*="#ec4899"] {
        color: #ffffff !important;
    }

    /* Gradient → white */
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #10b981"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #8b5cf6"] {
        background: linear-gradient(135deg, #ffffff, #e5e5e5) !important;
    }

    /* All text light */
    html[data-theme="dark"] .p-main h1,
    html[data-theme="dark"] .p-main h2,
    html[data-theme="dark"] .p-main h3,
    html[data-theme="dark"] .p-main h4 {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="title"],
    html[data-theme="dark"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="name"],
    html[data-theme="dark"] .p-main strong,
    html[data-theme="dark"] .p-main b {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="dark"] .p-main [class*="meta"],
    html[data-theme="dark"] .p-main [class*="desc"],
    html[data-theme="dark"] .p-main [class*="hint"] {
        color: #a3a3a3 !important;
    }

    /* Any hardcoded dark text → white */
    html[data-theme="dark"] .p-main *[style*="color: #0f172a"],
    html[data-theme="dark"] .p-main *[style*="color:#0f172a"],
    html[data-theme="dark"] .p-main *[style*="color: #1a1a1f"],
    html[data-theme="dark"] .p-main *[style*="color: #000"],
    html[data-theme="dark"] .p-main *[style*="color:#000"],
    html[data-theme="dark"] .p-main *[style*="color: black"],
    html[data-theme="dark"] .p-main *[style*="color:black"] {
        color: #ffffff !important;
    }

    /* Any hardcoded light bg → dark */
    html[data-theme="dark"] .p-main *[style*="background: #ffffff"],
    html[data-theme="dark"] .p-main *[style*="background:#ffffff"],
    html[data-theme="dark"] .p-main *[style*="background: white"],
    html[data-theme="dark"] .p-main *[style*="background: #f8fafc"],
    html[data-theme="dark"] .p-main *[style*="background: #f1f5f9"] {
        background: #0a0a0a !important;
    }</style>
@endpush