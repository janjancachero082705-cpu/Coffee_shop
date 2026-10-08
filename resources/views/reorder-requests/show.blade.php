@extends('layouts.admin')

@section('title', $reorderRequest->request_number ?? 'Reorder Request')
@section('subtitle', 'Reorder request details')

@section('content')

@php
    $status = strtolower($reorderRequest->status ?? 'pending');
    $statusMap = [
        'pending'  => ['label' => 'Pending',  'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.12)'],
        'approved' => ['label' => 'Approved', 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.12)'],
        'rejected' => ['label' => 'Rejected', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.12)'],
    ];
    $st = $statusMap[$status] ?? $statusMap['pending'];
    $items = $reorderRequest->items ?? collect();
    $totalQty = $items->sum(fn($i) => $i->quantity_requested ?? $i->quantity ?? 0);
    $totalAmount = (float) ($reorderRequest->total_amount ?? $items->sum(fn($i) => ($i->quantity_requested ?? $i->quantity ?? 0) * ($i->unit_price ?? 0)));
@endphp

{{-- ========== HERO ========== --}}
<div class="rq-hero" style="--status-color: {{ $st['color'] }};">
    <div class="rq-hero-left">
        <div class="rq-hero-label">Reorder Request</div>
        <div class="rq-hero-title">{{ $reorderRequest->request_number }}</div>
        <div class="rq-hero-meta">
            <a href="{{ route('stores.show', $reorderRequest->store_id) }}" class="rq-hero-store">
                {{ $reorderRequest->store->store_name ?? '-' }}
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            </a>
            @if($reorderRequest->store->code ?? null)
                <span class="rq-hero-dot">·</span>
                <span class="rq-hero-code">{{ $reorderRequest->store->code }}</span>
            @endif
            <span class="rq-hero-dot">·</span>
            <span class="rq-hero-date">{{ $reorderRequest->created_at->format('M d, Y · g:i A') }}</span>
        </div>
    </div>

    <div class="rq-hero-right">
        <div class="rq-hero-stat">
            <div class="rq-hero-stat-label">Status</div>
            <div class="rq-hero-badge" style="color: {{ $st['color'] }}; background: {{ $st['bg'] }}; border-color: {{ $st['color'] }}33;">
                <span class="rq-hero-badge-dot" style="background: {{ $st['color'] }};"></span>
                {{ $st['label'] }}
            </div>
        </div>
        <div class="rq-hero-stat-divider"></div>
        <div class="rq-hero-stat">
            <div class="rq-hero-stat-label">Total Amount</div>
            <div class="rq-hero-stat-value gold">&#8369;{{ number_format($totalAmount, 2) }}</div>
            <div class="rq-hero-stat-sub">{{ $items->count() }} item(s) · {{ $totalQty }} qty</div>
        </div>
    </div>
</div>

{{-- ========== GRID ========== --}}
<div class="rq-grid">

    {{-- LEFT: Items --}}
    <div class="rq-left">
        <div class="rq-card">
            <div class="rq-card-head">
                <div class="rq-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="rq-card-title">Requested Items</div>
                    <div class="rq-card-sub">{{ $items->count() }} product(s) · {{ $totalQty }} total qty</div>
                </div>
            </div>

            @if($items->count() > 0)
                <div class="rq-items">
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
                        <div class="rq-item">
                            <div class="rq-item-thumb">
                                @if($product && ($product->image_url ?? null))
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                @else
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="rq-item-info">
                                <div class="rq-item-name">{{ $product->name ?? 'Product #' . ($item->product_id ?? '-') }}</div>
                                <div class="rq-item-meta">
                                    <span class="rq-item-sku">{{ $product->sku ?? '' }}</span>
                                    @if($isOut)
                                        <span class="rq-stock-tag out">
                                            <span class="rq-stock-dot"></span>
                                            Out of stock
                                        </span>
                                    @elseif($isLow)
                                        <span class="rq-stock-tag low">
                                            <span class="rq-stock-dot"></span>
                                            Low stock · {{ $stock }}
                                        </span>
                                    @else
                                        <span class="rq-stock-tag ok">
                                            <span class="rq-stock-dot"></span>
                                            {{ $stock }} available
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="rq-item-qty">
                                <div class="rq-item-qty-label">QTY</div>
                                <div class="rq-item-qty-value">{{ $qty }}</div>
                            </div>
                            <div class="rq-item-price">
                                <div class="rq-item-price-label">Unit Price</div>
                                <div class="rq-item-price-value">&#8369;{{ number_format($price, 2) }}</div>
                            </div>
                            <div class="rq-item-subtotal">
                                <div class="rq-item-subtotal-label">Subtotal</div>
                                <div class="rq-item-subtotal-value">&#8369;{{ number_format($subtotal, 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="rq-items-total">
                    <span class="rq-items-total-label">Total</span>
                    <span class="rq-items-total-value">&#8369;{{ number_format($totalAmount, 2) }}</span>
                </div>
            @else
                <div class="rq-empty">Walay items sa request.</div>
            @endif
        </div>

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

    {{-- RIGHT: Action Panel --}}
    <div class="rq-right">

        @if($status === 'pending')

            {{-- ===== ACTION PANEL (Pending) ===== --}}
            <div class="rq-action-panel">
                <div class="rq-action-panel-head">
                    <div class="rq-action-panel-title">
                        <span class="rq-pulse"></span>
                        Action Required
                    </div>
                    <div class="rq-action-panel-sub">Choose how to process this request</div>
                </div>

                {{-- APPROVE CARD --}}
                <div class="rq-action-card rq-action-card-approve">
                    <div class="rq-action-card-head">
                        <div class="rq-action-card-icon">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12l5 5L20 7"/>
                            </svg>
                        </div>
                        <div class="rq-action-card-body">
                            <div class="rq-action-card-title">Approve Request</div>
                            <div class="rq-action-card-sub">Reserve stock &amp; create delivery receipt</div>
                        </div>
                    </div>
                    <button type="button" class="rq-action-card-btn rq-approve-btn" onclick="openApproveModal()">
                        Approve Request
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- REJECT CARD --}}
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

        {{-- ===== DETAILS CARD ===== --}}
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

        {{-- ===== BACK ===== --}}
        <a href="{{ route('reorder-requests.index') }}" class="rq-back-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Requests
        </a>
    </div>

</div>

{{-- ========== APPROVE MODAL ========== --}}
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

{{-- ========== REJECT MODAL ========== --}}
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
    /* ============================================================
       REORDER SHOW — PRO DESIGN
       ============================================================ */

    /* HERO */
    .rq-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 22px 26px;
        margin-bottom: 16px;
        background: linear-gradient(135deg, rgba(30, 26, 22, 0.85), rgba(21, 18, 15, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid var(--status-color);
        border-radius: 18px;
        overflow: hidden;
        flex-wrap: wrap;
    }
    .rq-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, var(--status-color), transparent 70%);
        opacity: 0.1;
        pointer-events: none;
    }
    .rq-hero-left { flex: 1; min-width: 0; position: relative; z-index: 1; }
    .rq-hero-label {
        font-size: 10px; font-weight: 800;
        color: #71717a;
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
    .rq-hero-store {
        display: inline-flex; align-items: center; gap: 4px;
        color: #c9a961; text-decoration: none; font-weight: 700;
    }
    .rq-hero-store:hover { color: #d4b673; }
    .rq-hero-store svg { opacity: 0.6; }
    .rq-hero-dot { color: #52525b; }
    .rq-hero-code {
        font-family: ui-monospace, monospace;
        font-size: 11px; color: #71717a;
    }
    .rq-hero-date { color: #d4d4d8; font-weight: 600; }

    .rq-hero-right {
        display: flex; align-items: center; gap: 18px;
        position: relative; z-index: 1; flex-shrink: 0;
    }
    .rq-hero-stat { text-align: right; }
    .rq-hero-stat-label {
        font-size: 9.5px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.1em;
        margin-bottom: 6px;
    }
    .rq-hero-stat-value {
        font-size: 22px; font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .rq-hero-stat-value.gold { color: #c9a961; }
    .rq-hero-stat-sub {
        font-size: 10.5px; color: #71717a; margin-top: 3px;
    }
    .rq-hero-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 100px;
        font-size: 11px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.05em;
        border: 1px solid;
    }
    .rq-hero-badge-dot {
        width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
    }
    .rq-hero-stat-divider {
        width: 1px; height: 44px;
        background: rgba(255, 255, 255, 0.06);
    }

    /* GRID */
    .rq-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 16px;
        align-items: start;
    }

    /* CARDS */
    .rq-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
    }
    .rq-card:last-child { margin-bottom: 0; }
    .rq-card-danger {
        border-color: rgba(239, 68, 68, 0.2);
    }

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

    /* ITEMS */
    .rq-items { display: flex; flex-direction: column; gap: 4px; }
    .rq-item {
        display: grid;
        grid-template-columns: 44px 1fr 60px 90px 90px;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        align-items: center;
    }
    .rq-item:last-child { border-bottom: none; }

    .rq-item-thumb {
        width: 44px; height: 44px;
        border-radius: 10px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.5);
        overflow: hidden;
        flex-shrink: 0;
    }
    .rq-item-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .rq-item-info { min-width: 0; }
    .rq-item-name {
        font-size: 13px; font-weight: 700; color: #fafafa;
        margin-bottom: 4px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .rq-item-meta {
        display: flex; align-items: center; gap: 8px;
        font-size: 10.5px; color: #71717a;
    }
    .rq-item-sku { font-family: ui-monospace, monospace; color: #52525b; }

    .rq-stock-tag {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 100px;
        font-size: 10px; font-weight: 700;
    }
    .rq-stock-dot {
        width: 5px; height: 5px; border-radius: 50%;
    }
    .rq-stock-tag.ok { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    .rq-stock-tag.ok .rq-stock-dot { background: #22c55e; }
    .rq-stock-tag.low { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .rq-stock-tag.low .rq-stock-dot { background: #f59e0b; }
    .rq-stock-tag.out { background: rgba(239, 68, 68, 0.12); color: #f87171; }
    .rq-stock-tag.out .rq-stock-dot { background: #f87171; }

    .rq-item-qty, .rq-item-price, .rq-item-subtotal { text-align: right; }
    .rq-item-qty-label, .rq-item-price-label, .rq-item-subtotal-label {
        font-size: 9px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .rq-item-qty-value {
        font-size: 14px; font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
    }
    .rq-item-price-value {
        font-size: 12px; font-weight: 700; color: #a1a1aa;
        font-variant-numeric: tabular-nums;
    }
    .rq-item-subtotal-value {
        font-size: 13px; font-weight: 800; color: #c9a961;
        font-variant-numeric: tabular-nums;
    }

    .rq-items-total {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 14px; margin-top: 8px;
        border-top: 2px solid rgba(201, 169, 97, 0.25);
    }
    .rq-items-total-label {
        font-size: 12px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
    }
    .rq-items-total-value {
        font-size: 22px; font-weight: 800; color: #c9a961;
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

    /* ========== ACTION PANEL ========== */
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
    .rq-action-panel-head {
        margin-bottom: 16px;
    }
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
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .rq-action-panel-sub {
        font-size: 11.5px; color: #a1a1aa;
    }

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

    .rq-action-card-head {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 12px;
    }
    .rq-action-card-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .rq-action-card-approve .rq-action-card-icon {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .rq-action-card-reject .rq-action-card-icon {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .rq-action-card-body { flex: 1; min-width: 0; }
    .rq-action-card-title {
        font-size: 13.5px; font-weight: 800;
        letter-spacing: -0.01em;
        margin-bottom: 3px;
    }
    .rq-action-card-approve .rq-action-card-title { color: #22c55e; }
    .rq-action-card-reject .rq-action-card-title { color: #ef4444; }
    .rq-action-card-sub {
        font-size: 11px; color: #a1a1aa;
        line-height: 1.4;
    }

    .rq-action-card-btn {
        display: flex; align-items: center; justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 12.5px; font-weight: 800;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
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

    /* COMPLETED BANNERS */
    .rq-completed-banner {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
        margin-bottom: 14px;
        border-radius: 16px;
        border: 1px solid;
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
        background: rgba(34, 197, 94, 0.18);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .rq-completed-rejected .rq-completed-icon {
        background: rgba(239, 68, 68, 0.18);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .rq-completed-content { flex: 1; min-width: 0; }
    .rq-completed-title {
        font-size: 15px; font-weight: 800;
        letter-spacing: -0.01em;
        margin-bottom: 3px;
    }
    .rq-completed-approved .rq-completed-title { color: #22c55e; }
    .rq-completed-rejected .rq-completed-title { color: #ef4444; }
    .rq-completed-desc {
        font-size: 11.5px; color: #a1a1aa;
    }
    .rq-completed-meta {
        font-size: 10.5px; color: #71717a; margin-top: 2px;
        font-family: ui-monospace, monospace;
    }

    /* INFO ROWS */
    .rq-info-rows { display: flex; flex-direction: column; }
    .rq-info-row {
        display: flex; justify-content: space-between; align-items: center;
        gap: 12px;
        padding: 9px 0;
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

    /* BACK BUTTON */
    .rq-back-btn {
        display: flex; align-items: center; justify-content: center;
        gap: 8px;
        padding: 11px 16px;
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

    /* ========== MODALS ========== */
    .rq-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
        animation: rqFadeIn 0.18s ease-out;
    }
    .rq-modal-overlay.active { display: flex; }
    @keyframes rqFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .rq-modal {
        background: linear-gradient(165deg, #1e1a16, #15120f);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        width: 100%;
        max-width: 460px;
        overflow: hidden;
        box-shadow: 0 30px 80px -20px rgba(0, 0, 0, 0.8);
        animation: rqModalIn 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes rqModalIn {
        from { transform: scale(0.94) translateY(12px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }

    .rq-modal-head {
        display: flex; align-items: center; gap: 14px;
        padding: 22px 24px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .rq-modal-icon {
        width: 48px; height: 48px;
        border-radius: 13px;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .rq-modal-icon-approve {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .rq-modal-icon-reject {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .rq-modal-heading { flex: 1; min-width: 0; }
    .rq-modal-title {
        font-size: 16px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.02em;
    }
    .rq-modal-sub {
        font-size: 11px; color: #71717a;
        font-family: ui-monospace, monospace;
        margin-top: 3px;
    }

    .rq-modal-body {
        padding: 20px 24px;
    }
    .rq-modal-note {
        display: flex; align-items: flex-start; gap: 8px;
        padding: 11px 13px;
        margin-bottom: 16px;
        background: rgba(201, 169, 97, 0.06);
        border: 1px solid rgba(201, 169, 97, 0.18);
        border-radius: 10px;
        font-size: 11.5px; color: #d4c0a0;
        line-height: 1.5;
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
        display: block;
        font-size: 10.5px; font-weight: 800;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 8px;
    }
    .rq-field-optional {
        color: #71717a; font-weight: 600; letter-spacing: 0;
        text-transform: none; font-size: 10px;
    }
    .rq-field-required { color: #ef4444; }

    .rq-field-input,
    .rq-field-textarea {
        width: 100%;
        padding: 11px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: #fafafa;
        font-size: 13px;
        font-family: inherit;
        transition: all 0.15s;
    }
    .rq-field-textarea { resize: vertical; min-height: 76px; }
    .rq-field-input:focus,
    .rq-field-textarea:focus {
        outline: none;
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
    }
    .rq-field-input::placeholder,
    .rq-field-textarea::placeholder { color: #52525b; }

    .rq-modal-foot {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 18px 24px 22px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .rq-modal-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 7px;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 12.5px; font-weight: 800;
        border: 1px solid;
        cursor: pointer;
        font-family: inherit;
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
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(34, 197, 94, 0.5);
    }
    .rq-modal-btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 22px -6px rgba(34, 197, 94, 0.7);
    }
    .rq-modal-btn-reject {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(239, 68, 68, 0.5);
    }
    .rq-modal-btn-reject:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 22px -6px rgba(239, 68, 68, 0.7);
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .rq-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .rq-hero {
            flex-direction: column;
            align-items: flex-start;
        }
        .rq-hero-right {
            width: 100%;
            justify-content: space-between;
        }
        .rq-hero-stat { text-align: left; }
        .rq-item {
            grid-template-columns: 40px 1fr 60px;
            gap: 10px;
        }
        .rq-item-price, .rq-item-subtotal { display: none; }
        .rq-modal-foot { grid-template-columns: 1fr; }
    }
</style>
@endpush