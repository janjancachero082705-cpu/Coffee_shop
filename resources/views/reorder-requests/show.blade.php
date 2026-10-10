@extends('layouts.admin')

@section('title', $reorderRequest->request_number ?? 'Reorder Request')
@section('subtitle', 'Reorder request details')

@section('content')

@php
    $status = strtolower($reorderRequest->status ?? 'pending');
    $statusMap = [
        'pending'  => ['label' => 'Pending',  'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.12)',  'icon' => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>'],
        'approved' => ['label' => 'Approved', 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.12)',  'icon' => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>'],
        'rejected' => ['label' => 'Rejected', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.12)',  'icon' => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>'],
    ];
    $st = $statusMap[$status] ?? $statusMap['pending'];
    $items = $reorderRequest->items ?? collect();
    $totalQty = $items->sum(fn($i) => $i->quantity_requested ?? $i->quantity ?? 0);
    $totalAmount = (float) ($reorderRequest->total_amount ?? $items->sum(fn($i) => ($i->quantity_requested ?? $i->quantity ?? 0) * ($i->unit_price ?? 0)));

    // ── Linked Delivery (auto-detect relationship) ──
    $linkedDelivery = null;
    if ($status === 'approved') {
        try {
            if (method_exists($reorderRequest, 'delivery') && $reorderRequest->delivery) {
                $linkedDelivery = $reorderRequest->delivery;
            } elseif (method_exists($reorderRequest, 'deliveryReceipt') && $reorderRequest->deliveryReceipt) {
                $linkedDelivery = $reorderRequest->deliveryReceipt;
            } elseif (method_exists($reorderRequest, 'deliveries')) {
                $linkedDelivery = $reorderRequest->deliveries()->latest()->first();
            } elseif (!empty($reorderRequest->delivery_id)) {
                $linkedDelivery = \App\Models\Delivery::find($reorderRequest->delivery_id);
            }
        } catch (\Throwable $e) {
            $linkedDelivery = null;
        }
    }
@endphp

{{-- ═══ HERO ═══ --}}
<div class="rq-hero" style="--status-color: {{ $st['color'] }};">
    <div class="rq-hero-glow"></div>

    <div class="rq-hero-icon" style="background: {{ $st['bg'] }}; color: {{ $st['color'] }};">
        {!! $st['icon'] !!}
    </div>

    <div class="rq-hero-content">
        <div class="rq-hero-label">Reorder Request</div>
        <div class="rq-hero-title">{{ $reorderRequest->request_number }}</div>
        <div class="rq-hero-meta">
            <a href="{{ route('stores.show', $reorderRequest->store_id) }}" class="rq-hero-link">
                {{ $reorderRequest->store->store_name ?? '-' }}
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            </a>
            @if($reorderRequest->store->code ?? null)
                <span class="rq-hero-dot">·</span>
                <span class="rq-hero-code">{{ $reorderRequest->store->code }}</span>
            @endif
            <span class="rq-hero-dot">·</span>
            <span class="rq-hero-status" style="color: {{ $st['color'] }}; background: {{ $st['bg'] }};">
                <span class="rq-status-dot" style="background: {{ $st['color'] }};"></span>
                {{ $st['label'] }}
            </span>
        </div>
        <div class="rq-hero-period">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            {{ $reorderRequest->created_at->format('M d, Y · g:i A') }}
        </div>
    </div>

    <div class="rq-hero-stats">
        <div class="rq-hero-stat">
            <div class="rq-hero-stat-label">Total Amount</div>
            <div class="rq-hero-stat-value gold">&#8369;{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="rq-hero-stat-divider"></div>
        <div class="rq-hero-stat">
            <div class="rq-hero-stat-label">Items</div>
            <div class="rq-hero-stat-value">{{ $items->count() }} <span style="font-size:12px;color:#71717a;font-weight:600;">· {{ $totalQty }} qty</span></div>
        </div>
    </div>
</div>

{{-- ═══ GRID ═══ --}}
<div class="rq-grid">

    {{-- LEFT: Items ──────────────────────────────── --}}
    <div>

        {{-- REQUESTED ITEMS --}}
        <div class="rq-card">
            <div class="rq-card-head">
                <div class="rq-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div class="rq-card-title">Requested Items</div>
                    <div class="rq-card-sub">{{ $items->count() }} product(s) · {{ $totalQty }} total qty</div>
                </div>
            </div>

            @if($items->count() > 0)
                <div class="rq-table">
                    <div class="rq-table-head">
                        <div>Product</div>
                        <div class="ta-r">Qty</div>
                        <div class="ta-r">Unit Price</div>
                        <div class="ta-r">Subtotal</div>
                    </div>
                    @foreach($items as $item)
                        @php
                            $product = $item->product ?? null;
                            $qty = $item->quantity_requested ?? $item->quantity ?? 0;
                            $price = $item->unit_price ?? $product->price ?? 0;
                            $subtotal = $qty * $price;
                            $stock = $product->stock ?? 0;
                            $isLow = $stock > 0 && $stock <= ($product->reorder_level ?? 10);
                            $isOut = $stock <= 0;
                        @endphp
                        <div class="rq-table-row">
                            <div class="rq-prod">
                                <div class="rq-prod-thumb">
                                    @if($product && ($product->image_url ?? null))
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                    @else
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="rq-prod-info">
                                    <div class="rq-prod-name">{{ $product->name ?? 'Product #' . ($item->product_id ?? '-') }}</div>
                                    <div class="rq-prod-meta">
                                        <span class="rq-prod-sku">{{ $product->sku ?? '' }}</span>
                                        @if($isOut)
                                            <span class="rq-stock-tag out"><span class="rq-stock-dot"></span>Out of stock</span>
                                        @elseif($isLow)
                                            <span class="rq-stock-tag low"><span class="rq-stock-dot"></span>Low · {{ $stock }}</span>
                                        @else
                                            <span class="rq-stock-tag ok"><span class="rq-stock-dot"></span>{{ $stock }} available</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="ta-r rq-num">{{ $qty }}</div>
                            <div class="ta-r rq-num">&#8369;{{ number_format($price, 2) }}</div>
                            <div class="ta-r rq-num rq-bold">&#8369;{{ number_format($subtotal, 2) }}</div>
                        </div>
                    @endforeach
                    <div class="rq-table-total">
                        <div class="rq-total-label">Total</div>
                        <div class="rq-total-value">&#8369;{{ number_format($totalAmount, 2) }}</div>
                    </div>
                </div>
            @else
                <div class="rq-empty">Walay items sa request.</div>
            @endif
        </div>

        {{-- NOTES --}}
        @if($reorderRequest->notes ?? null)
            <div class="rq-card">
                <div class="rq-card-head">
                    <div class="rq-card-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 7h16M4 12h16M4 17h10"/>
                        </svg>
                    </div>
                    <div class="rq-card-title">Store Notes</div>
                </div>
                <div class="rq-notes">{{ $reorderRequest->notes }}</div>
            </div>
        @endif

        {{-- REJECTION --}}
        @if($status === 'rejected' && ($reorderRequest->rejection_reason ?? null))
            <div class="rq-card rq-card-danger">
                <div class="rq-card-head">
                    <div class="rq-card-icon danger">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v4M12 16h.01"/>
                        </svg>
                    </div>
                    <div class="rq-card-title">Rejection Reason</div>
                </div>
                <div class="rq-notes">{{ $reorderRequest->rejection_reason }}</div>
            </div>
        @endif
    </div>

    {{-- RIGHT: Action Panel ──────────────────────────────── --}}
    <div>

        @if($status === 'pending')

            <div class="rq-action-panel">
                <div class="rq-action-panel-head">
                    <div class="rq-action-panel-title">
                        <span class="rq-pulse"></span>
                        Action Required
                    </div>
                    <div class="rq-action-panel-sub">Choose how to process this request</div>
                </div>

                <div class="rq-action-card rq-action-card-approve">
                    <div class="rq-action-card-head">
                        <div class="rq-action-card-icon">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12l5 5L20 7"/>
                            </svg>
                        </div>
                        <div class="rq-action-card-body">
                            <div class="rq-action-card-title">Approve Request</div>
                            <div class="rq-action-card-sub">Reserve stock &amp; create delivery</div>
                        </div>
                    </div>
                    <button type="button" class="rq-action-card-btn rq-approve-btn" onclick="openApproveModal()">
                        Approve Request
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <div class="rq-action-card rq-action-card-reject">
                    <div class="rq-action-card-head">
                        <div class="rq-action-card-icon">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="rq-action-card-body">
                            <div class="rq-action-card-title">Reject Request</div>
                            <div class="rq-action-card-sub">Decline &amp; notify the store</div>
                        </div>
                    </div>
                    <button type="button" class="rq-action-card-btn rq-reject-btn" onclick="openRejectModal()">
                        Reject Request
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

        @elseif($status === 'approved')

            <div class="rq-completed-banner rq-completed-approved">
                <div class="rq-completed-icon">
                    <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12l5 5L20 7"/>
                    </svg>
                </div>
                <div class="rq-completed-content">
                    <div class="rq-completed-title">Order Approved</div>
                    <div class="rq-completed-desc">
                        Processed {{ $reorderRequest->updated_at->diffForHumans() }}
                    </div>
                    @if($reorderRequest->approved_by ?? null)
                        <div class="rq-completed-meta">by {{ optional(\App\Models\User::find($reorderRequest->approved_by))->name ?? $reorderRequest->approved_by }}</div>
                    @endif
                </div>
            </div>

            {{-- ═══ LINKED DELIVERY — PREMIUM CARD ═══ --}}
            @if($linkedDelivery)
                @php
                    $dStatus = strtolower($linkedDelivery->status ?? 'pending');
                    $dStatusMap = [
                        'pending'    => ['label' => 'Pending',    'color' => '#f59e0b'],
                        'processing' => ['label' => 'Processing', 'color' => '#3b82f6'],
                        'shipped'    => ['label' => 'Shipped',    'color' => '#8b5cf6'],
                        'in_transit' => ['label' => 'In Transit', 'color' => '#06b6d4'],
                        'delivered'  => ['label' => 'Delivered',  'color' => '#22c55e'],
                        'received'   => ['label' => 'Received',   'color' => '#22c55e'],
                        'cancelled'  => ['label' => 'Cancelled',  'color' => '#ef4444'],
                    ];
                    $ds = $dStatusMap[$dStatus] ?? $dStatusMap['pending'];
                    $dNumber = $linkedDelivery->delivery_number
                        ?? $linkedDelivery->number
                        ?? ('DR-' . str_pad($linkedDelivery->id, 6, '0', STR_PAD_LEFT));
                    $dDate = $linkedDelivery->created_at ?? null;
                @endphp

                <a href="{{ route('deliveries.show', $linkedDelivery) }}"
                   class="rq-dlv-card"
                   style="--dc: {{ $ds['color'] }};">

                    <span class="rq-dlv-accent"></span>

                    <div class="rq-dlv-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor"
                             stroke-width="2" viewBox="0 0 24 24"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13" rx="2"/>
                            <path d="M16 8h4l3 3v5h-7V8z"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                        <span class="rq-dlv-pulse"></span>
                    </div>

                    <div class="rq-dlv-body">
                        <div class="rq-dlv-top">
                            <div class="rq-dlv-label">Delivery Receipt</div>
                            <div class="rq-dlv-status">
                                <span class="rq-dlv-status-dot"></span>
                                {{ $ds['label'] }}
                            </div>
                        </div>
                        <div class="rq-dlv-number">{{ $dNumber }}</div>
                        <div class="rq-dlv-meta">
                            @if($dDate)
                                <span class="rq-dlv-meta-item">
                                    <svg width="11" height="11" fill="none" stroke="currentColor"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 6v6l4 2"/>
                                    </svg>
                                    {{ $dDate->diffForHumans() }}
                                </span>
                            @endif
                            <span class="rq-dlv-meta-item">
                                <svg width="11" height="11" fill="none" stroke="currentColor"
                                     stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 11l3 3L22 4"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                View details
                            </span>
                        </div>
                    </div>

                    <div class="rq-dlv-arrow">
                        <svg width="16" height="16" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </div>
                </a>
            @else
                <div class="rq-delivery-missing">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                    <span>No delivery receipt linked to this request yet.</span>
                </div>
            @endif

        @elseif($status === 'rejected')

            <div class="rq-completed-banner rq-completed-rejected">
                <div class="rq-completed-icon">
                    <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </div>
                <div class="rq-completed-content">
                    <div class="rq-completed-title">Order Rejected</div>
                    <div class="rq-completed-desc">
                        Processed {{ $reorderRequest->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>

        @endif

        {{-- INFO CARD --}}
        <div class="rq-card">
            <div class="rq-card-head">
                <div class="rq-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                </div>
                <div class="rq-card-title">Request Info</div>
            </div>

            <div class="rq-info-rows">
                <div class="rq-info-row">
                    <span class="rq-info-label">Request #</span>
                    <span class="rq-info-value mono">{{ $reorderRequest->request_number }}</span>
                </div>
                <div class="rq-info-row">
                    <span class="rq-info-label">Store</span>
                    <span class="rq-info-value">{{ $reorderRequest->store->store_name ?? '-' }}</span>
                </div>
                <div class="rq-info-row">
                    <span class="rq-info-label">Store Code</span>
                    <span class="rq-info-value mono">{{ $reorderRequest->store->code ?? '-' }}</span>
                </div>
                <div class="rq-info-row">
                    <span class="rq-info-label">Created</span>
                    <span class="rq-info-value">{{ $reorderRequest->created_at->format('M d, Y') }}</span>
                </div>
                <div class="rq-info-row">
                    <span class="rq-info-label">Updated</span>
                    <span class="rq-info-value">{{ $reorderRequest->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- BACK --}}
        <a href="{{ route('reorder-requests.index') }}" class="rq-back-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Requests
        </a>
    </div>

</div>

{{-- ═══ APPROVE MODAL ═══ --}}
<div id="approveModal" class="rq-modal-overlay" onclick="if(event.target === this) closeApproveModal()">
    <div class="rq-modal">
        <div class="rq-modal-head">
            <div class="rq-modal-icon rq-modal-icon-approve">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
            </div>
            <div class="rq-modal-heading">
                <div class="rq-modal-title">Approve Request</div>
                <div class="rq-modal-sub">{{ $reorderRequest->request_number }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('reorder-requests.approve', $reorderRequest) }}">
            @csrf

            <div class="rq-modal-body">
                <div class="rq-modal-note">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                    This will reserve stock for the store and create a delivery receipt.
                </div>

                <div class="rq-field">
                    <label class="rq-field-label">Delivery Date</label>
                    <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="rq-field-input" required>
                </div>

                <div class="rq-field">
                    <label class="rq-field-label">Admin Notes <span class="rq-field-optional">(optional)</span></label>
                    <textarea name="admin_notes" rows="3" class="rq-field-textarea" placeholder="Internal notes..."></textarea>
                </div>
            </div>

            <div class="rq-modal-foot">
                <button type="button" class="rq-modal-btn rq-modal-btn-ghost" onclick="closeApproveModal()">Cancel</button>
                <button type="submit" class="rq-modal-btn rq-modal-btn-approve">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    Confirm Approve
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ REJECT MODAL ═══ --}}
<div id="rejectModal" class="rq-modal-overlay" onclick="if(event.target === this) closeRejectModal()">
    <div class="rq-modal">
        <div class="rq-modal-head">
            <div class="rq-modal-icon rq-modal-icon-reject">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </div>
            <div class="rq-modal-heading">
                <div class="rq-modal-title">Reject Request</div>
                <div class="rq-modal-sub">{{ $reorderRequest->request_number }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('reorder-requests.reject', $reorderRequest) }}">
            @csrf

            <div class="rq-modal-body">
                <div class="rq-modal-note rq-modal-note-warn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    The store will be notified. Please provide a clear reason.
                </div>

                <div class="rq-field">
                    <label class="rq-field-label">Reason <span class="rq-field-required">*</span></label>
                    <textarea name="rejection_reason" rows="4" class="rq-field-textarea" placeholder="Why is this request being rejected?" required></textarea>
                </div>
            </div>

            <div class="rq-modal-foot">
                <button type="button" class="rq-modal-btn rq-modal-btn-ghost" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="rq-modal-btn rq-modal-btn-reject">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openApproveModal() {
        document.getElementById('approveModal').classList.add('active');
    }
    function closeApproveModal() {
        document.getElementById('approveModal').classList.remove('active');
    }
    function openRejectModal() {
        document.getElementById('rejectModal').classList.add('active');
        setTimeout(() => {
            const ta = document.querySelector('#rejectModal textarea');
            if (ta) ta.focus();
        }, 100);
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('active');
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeApproveModal(); closeRejectModal(); }
    });
</script>
@endpush

@push('styles')
<style>
    /* ════════════════════════════════════════════════════════
       REORDER SHOW — PREMIUM
       ════════════════════════════════════════════════════════ */

    /* ═══ HERO ═══ */
    .rq-hero {
        position: relative;
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 22px 26px;
        margin-bottom: 18px;
        background:
            radial-gradient(circle at 100% 0%, color-mix(in srgb, var(--status-color) 12%, transparent) 0%, transparent 55%),
            linear-gradient(135deg, rgba(30, 26, 22, 0.9) 0%, rgba(21, 18, 15, 0.95) 100%);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid var(--status-color);
        border-radius: 18px;
        overflow: hidden;
    }
    .rq-hero-glow {
        position: absolute;
        top: -100px; right: -100px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, var(--status-color), transparent 70%);
        opacity: 0.15;
        pointer-events: none;
        animation: rqGlow 6s ease-in-out infinite;
    }
    @keyframes rqGlow {
        0%, 100% { opacity: 0.12; transform: scale(1); }
        50%      { opacity: 0.2;  transform: scale(1.08); }
    }
    .rq-hero-icon {
        width: 58px; height: 58px;
        border-radius: 16px;
        display: grid; place-items: center;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative; z-index: 1;
        box-shadow:
            0 8px 20px -8px color-mix(in srgb, var(--status-color) 50%, transparent),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }
    .rq-hero-icon svg { width: 26px; height: 26px; }
    .rq-hero-content { flex: 1; min-width: 0; position: relative; z-index: 1; }
    .rq-hero-label {
        font-size: 10px; font-weight: 800;
        color: var(--status-color);
        text-transform: uppercase; letter-spacing: 0.12em;
        margin-bottom: 6px;
    }
    .rq-hero-title {
        font-size: 22px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.02em;
        font-family: ui-monospace, monospace;
        line-height: 1.1; margin-bottom: 10px;
    }
    .rq-hero-meta {
        display: flex; align-items: center; gap: 8px;
        flex-wrap: wrap; font-size: 12px;
    }
    .rq-hero-link {
        display: inline-flex; align-items: center; gap: 4px;
        color: #c9a961; text-decoration: none; font-weight: 700;
        transition: color 0.15s;
    }
    .rq-hero-link:hover { color: #d4b673; }
    .rq-hero-link svg { opacity: 0.6; }
    .rq-hero-dot { color: #52525b; }
    .rq-hero-code {
        font-family: ui-monospace, monospace;
        font-size: 11px; color: #71717a;
    }
    .rq-hero-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px; border-radius: 100px;
        font-size: 10.5px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .rq-status-dot {
        width: 6px; height: 6px; border-radius: 50%;
        flex-shrink: 0;
        animation: rqDotPulse 2s ease-in-out infinite;
    }
    @keyframes rqDotPulse {
        0%, 100% { opacity: 1; }
        50%      { opacity: 0.4; }
    }
    .rq-hero-period {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 10px; padding: 5px 11px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 100px;
        font-size: 11px; font-weight: 600; color: #a1a1aa;
        font-family: ui-monospace, monospace;
    }
    .rq-hero-period svg { color: #71717a; }
    .rq-hero-stats {
        display: flex; align-items: center; gap: 16px;
        position: relative; z-index: 1; flex-shrink: 0;
        padding-left: 20px;
        border-left: 1px solid rgba(255, 255, 255, 0.06);
    }
    .rq-hero-stat { text-align: right; }
    .rq-hero-stat-label {
        font-size: 9.5px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.1em;
        margin-bottom: 5px;
    }
    .rq-hero-stat-value {
        font-size: 18px; font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .rq-hero-stat-value.gold { color: #c9a961; }
    .rq-hero-stat-divider {
        width: 1px; height: 32px;
        background: rgba(255, 255, 255, 0.06);
    }

    /* ═══ GRID ═══ */
    .rq-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 16px;
        align-items: start;
    }

    /* ═══ CARD ═══ */
    .rq-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
        transition: border-color 0.25s;
    }
    .rq-card:hover { border-color: rgba(201, 169, 97, 0.15); }
    .rq-card:last-child { margin-bottom: 0; }
    .rq-card-danger { border-color: rgba(239, 68, 68, 0.2); }
    .rq-card-danger:hover { border-color: rgba(239, 68, 68, 0.35); }

    .rq-card-head {
        display: flex; align-items: center; gap: 12px;
        padding-bottom: 12px; margin-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .rq-card-icon {
        width: 34px; height: 34px; border-radius: 10px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .rq-card-icon.danger {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
    }
    .rq-card-title {
        font-size: 13.5px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.01em;
    }
    .rq-card-sub { font-size: 10.5px; color: #71717a; margin-top: 2px; }

    /* ═══ TABLE ═══ */
    .rq-table { display: flex; flex-direction: column; }
    .rq-table-head {
        display: grid;
        grid-template-columns: 1fr 50px 90px 100px;
        gap: 12px; padding: 0 0 10px;
        font-size: 9.5px; font-weight: 800;
        color: #71717a; text-transform: uppercase;
        letter-spacing: 0.08em;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .rq-table-row {
        display: grid;
        grid-template-columns: 1fr 50px 90px 100px;
        gap: 12px; padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        align-items: center;
        transition: background 0.15s;
    }
    .rq-table-row:hover { background: rgba(201, 169, 97, 0.03); }
    .rq-table-row:last-of-type { border-bottom: none; }
    .ta-r { text-align: right; }

    .rq-prod { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .rq-prod-thumb {
        width: 36px; height: 36px;
        border-radius: 9px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.5);
        overflow: hidden; flex-shrink: 0;
    }
    .rq-prod-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .rq-prod-info { min-width: 0; }
    .rq-prod-name {
        font-size: 12.5px; font-weight: 700; color: #fafafa;
        margin-bottom: 3px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .rq-prod-meta {
        display: flex; align-items: center; gap: 6px;
        font-size: 10px; color: #71717a;
    }
    .rq-prod-sku { font-family: ui-monospace, monospace; }
    .rq-stock-tag {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 1px 7px; border-radius: 100px;
        font-size: 9.5px; font-weight: 700;
    }
    .rq-stock-dot { width: 4px; height: 4px; border-radius: 50%; }
    .rq-stock-tag.ok  { background: rgba(34, 197, 94, 0.1);  color: #22c55e; }
    .rq-stock-tag.ok  .rq-stock-dot { background: #22c55e; }
    .rq-stock-tag.low { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .rq-stock-tag.low .rq-stock-dot { background: #f59e0b; }
    .rq-stock-tag.out { background: rgba(239, 68, 68, 0.12); color: #f87171; }
    .rq-stock-tag.out .rq-stock-dot { background: #f87171; }

    .rq-num {
        font-size: 12.5px; font-weight: 700; color: #d4d4d8;
        font-variant-numeric: tabular-nums;
    }
    .rq-bold { color: #c9a961; font-weight: 800; }

    .rq-table-total {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 14px;
        border-top: 2px solid rgba(201, 169, 97, 0.25);
    }
    .rq-total-label {
        font-size: 12.5px; font-weight: 800;
        color: #71717a; text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .rq-total-value {
        font-size: 20px; font-weight: 800; color: #c9a961;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .rq-notes {
        font-size: 12.5px; color: #a1a1aa; line-height: 1.6;
    }
    .rq-empty {
        text-align: center; padding: 30px 20px;
        color: #71717a; font-size: 12px;
    }

    /* ═══ ACTION PANEL ═══ */
    .rq-action-panel {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(245, 158, 11, 0.15);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
        position: relative;
        overflow: hidden;
    }
    .rq-action-panel::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #f59e0b, transparent);
    }
    .rq-action-panel-head { margin-bottom: 16px; }
    .rq-action-panel-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; font-weight: 800;
        color: #f59e0b;
        text-transform: uppercase; letter-spacing: 0.1em;
        margin-bottom: 4px;
    }
    .rq-pulse {
        width: 7px; height: 7px; border-radius: 50%;
        background: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        animation: rqPulse 2s ease-in-out infinite;
    }
    @keyframes rqPulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2); }
        50%      { opacity: 0.5; box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.05); }
    }
    .rq-action-panel-sub { font-size: 11.5px; color: #a1a1aa; }

    .rq-action-card {
        padding: 14px;
        border-radius: 12px;
        border: 1px solid;
        margin-bottom: 10px;
        transition: all 0.18s;
    }
    .rq-action-card:last-child { margin-bottom: 0; }
    .rq-action-card-approve {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.08), rgba(34, 197, 94, 0.02));
        border-color: rgba(34, 197, 94, 0.25);
    }
    .rq-action-card-approve:hover {
        border-color: rgba(34, 197, 94, 0.45);
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.04));
    }
    .rq-action-card-reject {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.08), rgba(239, 68, 68, 0.02));
        border-color: rgba(239, 68, 68, 0.25);
    }
    .rq-action-card-reject:hover {
        border-color: rgba(239, 68, 68, 0.45);
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(239, 68, 68, 0.04));
    }
    .rq-action-card-head { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .rq-action-card-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .rq-action-card-approve .rq-action-card-icon {
        background: rgba(34, 197, 94, 0.15); color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .rq-action-card-reject .rq-action-card-icon {
        background: rgba(239, 68, 68, 0.15); color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .rq-action-card-body { flex: 1; min-width: 0; }
    .rq-action-card-title {
        font-size: 13.5px; font-weight: 800;
        letter-spacing: -0.01em; margin-bottom: 3px;
    }
    .rq-action-card-approve .rq-action-card-title { color: #22c55e; }
    .rq-action-card-reject .rq-action-card-title { color: #ef4444; }
    .rq-action-card-sub { font-size: 11px; color: #a1a1aa; line-height: 1.4; }

    .rq-action-card-btn {
        display: flex; align-items: center; justify-content: center;
        gap: 8px; width: 100%; padding: 11px 16px;
        border-radius: 10px;
        font-size: 12.5px; font-weight: 800;
        border: 1px solid transparent;
        cursor: pointer; font-family: inherit;
        transition: all 0.15s;
        text-decoration: none;
    }
    .rq-approve-btn {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(34, 197, 94, 0.5);
    }
    .rq-approve-btn:hover {
        background: linear-gradient(135deg, #2ed567, #1fb855);
        transform: translateY(-1px);
        box-shadow: 0 8px 22px -6px rgba(34, 197, 94, 0.7);
    }
    .rq-reject-btn {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(239, 68, 68, 0.5);
    }
    .rq-reject-btn:hover {
        background: linear-gradient(135deg, #f55, #e63a3a);
        transform: translateY(-1px);
        box-shadow: 0 8px 22px -6px rgba(239, 68, 68, 0.7);
    }

    /* ═══ COMPLETED BANNERS ═══ */
    .rq-completed-banner {
        display: flex; align-items: center; gap: 14px;
        padding: 18px 20px; margin-bottom: 14px;
        border-radius: 16px; border: 1px solid;
    }
    .rq-completed-approved {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.04));
        border-color: rgba(34, 197, 94, 0.3);
    }
    .rq-completed-rejected {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(239, 68, 68, 0.04));
        border-color: rgba(239, 68, 68, 0.3);
    }
    .rq-completed-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .rq-completed-approved .rq-completed-icon {
        background: rgba(34, 197, 94, 0.18); color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .rq-completed-rejected .rq-completed-icon {
        background: rgba(239, 68, 68, 0.18); color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .rq-completed-content { flex: 1; min-width: 0; }
    .rq-completed-title {
        font-size: 15px; font-weight: 800;
        letter-spacing: -0.01em; margin-bottom: 3px;
    }
    .rq-completed-approved .rq-completed-title { color: #22c55e; }
    .rq-completed-rejected .rq-completed-title { color: #ef4444; }
    .rq-completed-desc { font-size: 11.5px; color: #a1a1aa; }
    .rq-completed-meta {
        font-size: 10.5px; color: #71717a; margin-top: 2px;
        font-family: ui-monospace, monospace;
    }

    /* ═══ DELIVERY CARD ═══ */
    .rq-dlv-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 16px 16px 20px;
        margin-bottom: 14px;
        background:
            radial-gradient(circle at 100% 0%, color-mix(in srgb, var(--dc) 16%, transparent) 0%, transparent 55%),
            linear-gradient(165deg, rgba(30, 26, 22, 0.92), rgba(21, 18, 15, 0.96));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        text-decoration: none;
        overflow: hidden;
        isolation: isolate;
        box-shadow:
            0 1px 0 rgba(255, 255, 255, 0.04) inset,
            0 8px 24px -14px rgba(0, 0, 0, 0.6);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                    border-color 0.25s, box-shadow 0.25s;
    }
    .rq-dlv-card .rq-dlv-accent {
        position: absolute; top: 0; left: 0; bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--dc), color-mix(in srgb, var(--dc) 30%, transparent));
        transition: width 0.25s ease;
    }
    .rq-dlv-card:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--dc) 40%, transparent);
        box-shadow:
            0 1px 0 rgba(255, 255, 255, 0.06) inset,
            0 16px 40px -14px color-mix(in srgb, var(--dc) 55%, transparent),
            0 0 0 1px color-mix(in srgb, var(--dc) 20%, transparent);
    }
    .rq-dlv-card:hover .rq-dlv-accent { width: 4px; }
    .rq-dlv-card:active { transform: translateY(0); }

    .rq-dlv-icon {
        position: relative;
        width: 46px; height: 46px;
        border-radius: 13px;
        display: grid; place-items: center;
        flex-shrink: 0;
        background: linear-gradient(135deg,
            color-mix(in srgb, var(--dc) 22%, transparent),
            color-mix(in srgb, var(--dc) 6%, transparent));
        border: 1px solid color-mix(in srgb, var(--dc) 35%, transparent);
        color: var(--dc);
        box-shadow:
            0 6px 16px -8px color-mix(in srgb, var(--dc) 60%, transparent),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .rq-dlv-card:hover .rq-dlv-icon {
        transform: scale(1.06) rotate(-3deg);
    }
    .rq-dlv-pulse {
        position: absolute;
        inset: -5px;
        border-radius: 18px;
        border: 2px solid var(--dc);
        opacity: 0;
        animation: rqDlvPulse 2.4s ease-out infinite;
        pointer-events: none;
    }
    @keyframes rqDlvPulse {
        0%   { opacity: 0.55; transform: scale(0.88); }
        65%  { opacity: 0;    transform: scale(1.18); }
        100% { opacity: 0;    transform: scale(1.18); }
    }
    .rq-dlv-body {
        flex: 1; min-width: 0;
        display: flex; flex-direction: column; gap: 5px;
    }
    .rq-dlv-top {
        display: flex; align-items: center;
        justify-content: space-between; gap: 10px;
    }
    .rq-dlv-label {
        font-size: 9.5px; font-weight: 800;
        color: #71717a;
        text-transform: uppercase; letter-spacing: 0.12em;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .rq-dlv-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px; border-radius: 100px;
        font-size: 9.5px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--dc);
        background: color-mix(in srgb, var(--dc) 14%, transparent);
        border: 1px solid color-mix(in srgb, var(--dc) 35%, transparent);
        flex-shrink: 0; line-height: 1;
    }
    .rq-dlv-status-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 6px currentColor;
        animation: rqDlvDot 1.6s ease-in-out infinite;
        flex-shrink: 0;
    }
    @keyframes rqDlvDot {
        0%, 100% { opacity: 1; }
        50%      { opacity: 0.3; }
    }
    .rq-dlv-number {
        font-size: 15px; font-weight: 800;
        color: #fafafa;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        letter-spacing: -0.01em; line-height: 1.2;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .rq-dlv-meta {
        display: flex; align-items: center; gap: 12px;
        flex-wrap: wrap; margin-top: 1px;
    }
    .rq-dlv-meta-item {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; color: #a1a1aa; font-weight: 600;
        white-space: nowrap;
    }
    .rq-dlv-meta-item svg { color: #71717a; flex-shrink: 0; }
    .rq-dlv-arrow {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: grid; place-items: center;
        background: color-mix(in srgb, var(--dc) 12%, transparent);
        color: var(--dc); flex-shrink: 0;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                    background 0.25s, box-shadow 0.25s;
    }
    .rq-dlv-card:hover .rq-dlv-arrow {
        background: color-mix(in srgb, var(--dc) 22%, transparent);
        transform: translateX(4px);
        box-shadow: 0 4px 12px -4px color-mix(in srgb, var(--dc) 60%, transparent);
    }
    .rq-delivery-missing {
        display: flex; align-items: center; gap: 8px;
        padding: 11px 14px; margin-bottom: 14px;
        background: rgba(245, 158, 11, 0.06);
        border: 1px dashed rgba(245, 158, 11, 0.3);
        border-radius: 10px; font-size: 11.5px;
        color: #d4a35a; line-height: 1.4;
    }
    .rq-delivery-missing svg { flex-shrink: 0; color: #f59e0b; }

    /* ═══ INFO ROWS ═══ */
    .rq-info-rows { display: flex; flex-direction: column; }
    .rq-info-row {
        display: flex; justify-content: space-between; align-items: center;
        gap: 12px; padding: 10px 0;
        font-size: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .rq-info-row:last-child { border-bottom: none; }
    .rq-info-label { color: #71717a; font-weight: 600; }
    .rq-info-value { color: #fafafa; font-weight: 700; text-align: right; }
    .rq-info-value.mono {
        font-family: ui-monospace, monospace;
        color: #d4d4d8; font-size: 11.5px;
    }

    /* ═══ BACK BTN ═══ */
    .rq-back-btn {
        display: flex; align-items: center; justify-content: center;
        gap: 8px; padding: 11px 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: #d4d4d8;
        font-size: 12.5px; font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
    }
    .rq-back-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.15);
        color: #fafafa;
    }

    /* ═══ MODALS ═══ */
    .rq-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center; justify-content: center;
        z-index: 9999; padding: 20px;
    }
    .rq-modal-overlay.active { display: flex; }

    .rq-modal {
        background: linear-gradient(165deg, #1e1a16, #15120f);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        width: 100%; max-width: 460px;
        overflow: hidden;
        box-shadow: 0 30px 80px -20px rgba(0, 0, 0, 0.8);
        animation: rqModalIn 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes rqModalIn {
        from { transform: scale(0.94) translateY(12px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }
    .rq-modal-head {
        display: flex; align-items: center; gap: 14px;
        padding: 22px 24px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .rq-modal-icon {
        width: 48px; height: 48px;
        border-radius: 13px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .rq-modal-icon-approve {
        background: rgba(34, 197, 94, 0.15); color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .rq-modal-icon-reject {
        background: rgba(239, 68, 68, 0.15); color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .rq-modal-heading { flex: 1; min-width: 0; }
    .rq-modal-title {
        font-size: 16px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.02em;
    }
    .rq-modal-sub {
        font-size: 11px; color: #71717a;
        font-family: ui-monospace, monospace; margin-top: 3px;
    }
    .rq-modal-body { padding: 20px 24px; }
    .rq-modal-note {
        display: flex; align-items: flex-start; gap: 8px;
        padding: 11px 13px; margin-bottom: 16px;
        background: rgba(201, 169, 97, 0.06);
        border: 1px solid rgba(201, 169, 97, 0.18);
        border-radius: 10px;
        font-size: 11.5px; color: #d4c0a0; line-height: 1.5;
    }
    .rq-modal-note svg { flex-shrink: 0; margin-top: 2px; color: #c9a961; }
    .rq-modal-note-warn {
        background: rgba(245, 158, 11, 0.06);
        border-color: rgba(245, 158, 11, 0.2);
        color: #d4a35a;
    }
    .rq-modal-note-warn svg { color: #f59e0b; }

    .rq-field { margin-bottom: 14px; }
    .rq-field:last-child { margin-bottom: 0; }
    .rq-field-label {
        display: block; font-size: 10.5px; font-weight: 800;
        color: #a1a1aa; text-transform: uppercase;
        letter-spacing: 0.1em; margin-bottom: 8px;
    }
    .rq-field-optional {
        color: #71717a; font-weight: 600;
        letter-spacing: 0; text-transform: none; font-size: 10px;
    }
    .rq-field-required { color: #ef4444; }
    .rq-field-input, .rq-field-textarea {
        width: 100%; padding: 11px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: #fafafa; font-size: 13px;
        font-family: inherit; transition: all 0.15s;
    }
    .rq-field-textarea { resize: vertical; min-height: 76px; }
    .rq-field-input:focus, .rq-field-textarea:focus {
        outline: none; border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
    }
    .rq-field-input::placeholder,
    .rq-field-textarea::placeholder { color: #52525b; }

    .rq-modal-foot {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 10px; padding: 18px 24px 22px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .rq-modal-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 7px; padding: 12px 18px;
        border-radius: 10px;
        font-size: 12.5px; font-weight: 800;
        border: 1px solid;
        cursor: pointer; font-family: inherit;
        transition: all 0.15s;
    }
    .rq-modal-btn-ghost {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.1);
        color: #d4d4d8;
    }
    .rq-modal-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }
    .rq-modal-btn-approve {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: transparent; color: #fff;
        box-shadow: 0 4px 14px -4px rgba(34, 197, 94, 0.5);
    }
    .rq-modal-btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 22px -6px rgba(34, 197, 94, 0.7);
    }
    .rq-modal-btn-reject {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent; color: #fff;
        box-shadow: 0 4px 14px -4px rgba(239, 68, 68, 0.5);
    }
    .rq-modal-btn-reject:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 22px -6px rgba(239, 68, 68, 0.7);
    }

    /* ═══ RESPONSIVE ═══ */
    @media (max-width: 1100px) {
        .rq-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .rq-hero {
            flex-direction: column; align-items: flex-start;
            padding: 18px 20px;
        }
        .rq-hero-stats {
            padding-left: 0; padding-top: 16px;
            border-left: none;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            width: 100%; justify-content: space-between; gap: 10px;
        }
        .rq-hero-stat { text-align: left; }
        .rq-hero-title { font-size: 18px; }
        .rq-hero-icon { width: 52px; height: 52px; }
        .rq-table-head,
        .rq-table-row {
            grid-template-columns: 1fr 40px 70px 80px;
            gap: 8px;
        }
        .rq-table-head { font-size: 8.5px; }
        .rq-num, .rq-prod-name { font-size: 11px; }
        .rq-modal-foot { grid-template-columns: 1fr; }
        .rq-dlv-card { padding: 14px 14px 14px 18px; gap: 12px; }
        .rq-dlv-icon { width: 42px; height: 42px; border-radius: 11px; }
        .rq-dlv-icon svg { width: 20px; height: 20px; }
        .rq-dlv-number { font-size: 14px; }
        .rq-dlv-arrow { width: 30px; height: 30px; }
    }
</style>
@endpush