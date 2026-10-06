@extends('layouts.admin')

@section('title', $reorderRequest->request_number)
@section('subtitle', 'Reorder request details')

@section('actions')
    <a href="{{ route('reorder-requests.index') }}" class="btn btn-ghost btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>

<style>
    .rr-items-list {
        display: flex;
        flex-direction: column;
        padding: 0 4px;
    }
    .rr-item-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 14px 4px;
        border-bottom: 1px solid rgba(38, 38, 46, 0.5);
        transition: background 0.15s;
    }
    .rr-item-row:last-child { border-bottom: none; }
    .rr-item-row:hover { background: rgba(169, 120, 74, 0.04); }

    /* PRODUCT IMAGE */
    .rr-item-image {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(138, 95, 54, 0.15));
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid;
        place-items: center;
        position: relative;
    }
    .rr-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .rr-item-image-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    /* PRODUCT INFO */
    .rr-item-info {
        flex: 1;
        min-width: 0;
    }
    .rr-item-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rr-item-meta {
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 11px;
        color: var(--text-muted);
    }
    .rr-item-stock {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .rr-item-stock-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
    }

    /* COLUMNS */
    .rr-item-col {
        text-align: right;
        flex-shrink: 0;
        min-width: 80px;
    }
    .rr-item-col-label {
        font-size: 9.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 3px;
    }
    .rr-item-col-value {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
    }
    .rr-item-qty {
        min-width: 60px;
    }
    .rr-item-qty .rr-item-col-value {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.25);
        color: #60a5fa;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 800;
    }
    .rr-item-subtotal-value {
        color: var(--accent-light);
        font-size: 14px;
        font-weight: 800;
    }

    /* TOTAL */
    .rr-items-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 4px 4px;
        margin-top: 8px;
        border-top: 2px solid rgba(169, 120, 74, 0.25);
    }
    .rr-items-total-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .rr-items-total-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--accent-light);
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
    }

    @media (max-width: 700px) {
        .rr-item-row {
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px 0;
        }
        .rr-item-image {
            width: 48px;
            height: 48px;
        }
        .rr-item-info {
            flex: 1 1 calc(100% - 60px);
        }
        .rr-item-col {
            flex: 1;
            min-width: 0;
            text-align: left;
        }
        .rr-item-col-label { font-size: 9px; }
        .rr-item-col-value { font-size: 12px; }
    }
</style>
@endsection

@section('content')

<div class="hero">
    <div class="hero-content">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; position:relative; z-index:1;">
            <div>
                <div class="hero-label">Request from</div>
                <div style="font-size:20px; font-weight:800; color:var(--text-primary); margin-bottom:6px;">
                    {{ $reorderRequest->store->store_name ?? '-' }}
                </div>
                <div style="font-size:13px; color:var(--text-secondary);">
                    {{ $reorderRequest->store->code }} - {{ $reorderRequest->request_number }}
                </div>
                <div style="margin-top:12px; display:flex; gap:6px; flex-wrap:wrap;">
                    <span class="badge badge-{{ $reorderRequest->status }}">{{ ucfirst($reorderRequest->status) }}</span>
                    <span style="padding:4px 10px; background:rgba(169,120,74,0.15); border-radius:5px; font-size:11px; color:#c9a961; font-weight:600;">
                        {{ \Carbon\Carbon::parse($reorderRequest->requested_date)->format('M d, Y') }}
                    </span>
                </div>
            </div>
            <div style="text-align:right;">
                <div class="hero-label">Total Amount</div>
                <div class="hero-value accent">&#8369;{{ number_format($reorderRequest->total_amount, 2) }}</div>
                <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">
                    {{ $reorderRequest->total_quantity }} items total
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="detail-grid">

    {{-- LEFT --}}
    <div>
        <div class="card">
            <div class="card-head">
                <div class="card-head-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <div class="card-head-title">Requested Items</div>
                    <div class="card-head-sub">{{ $reorderRequest->items->count() }} product(s)</div>
                </div>
            </div>

            <div class="rr-items-list">
                @foreach($reorderRequest->items as $item)
                    <div class="rr-item-row">
                        {{-- PRODUCT IMAGE --}}
                        <div class="rr-item-image">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ $item->product->image_url }}"
                                     alt="{{ $item->product->name }}"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                                <div class="rr-item-image-fallback" style="display:none;">
                                    {{ strtoupper(substr($item->product->name ?? 'PR', 0, 2)) }}
                                </div>
                            @else
                                <div class="rr-item-image-fallback">
                                    {{ strtoupper(substr($item->product->name ?? 'PR', 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        {{-- PRODUCT INFO --}}
                        <div class="rr-item-info">
                            <div class="rr-item-name">{{ $item->product->name ?? '-' }}</div>
                            <div class="rr-item-meta">
                                @if($item->product && $item->product->stock)
                                    <span class="rr-item-stock">
                                        <span class="rr-item-stock-dot"></span>
                                        Stock: {{ number_format($item->product->stock) }} available
                                    </span>
                                @endif
                                @if($item->product && $item->product->sku)
                                    <span style="font-family:ui-monospace;font-size:10px;color:var(--text-muted);">{{ $item->product->sku }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- QUANTITY --}}
                        <div class="rr-item-col rr-item-qty">
                            <div class="rr-item-col-label">QTY</div>
                            <div class="rr-item-col-value">{{ $item->quantity_requested ?? $item->quantity }}</div>
                        </div>

                        {{-- UNIT PRICE --}}
                        <div class="rr-item-col">
                            <div class="rr-item-col-label">Unit Price</div>
                            <div class="rr-item-col-value">&#8369;{{ number_format($item->unit_price, 2) }}</div>
                        </div>

                        {{-- SUBTOTAL --}}
                        <div class="rr-item-col">
                            <div class="rr-item-col-label">Subtotal</div>
                            <div class="rr-item-col-value rr-item-subtotal-value">
                                &#8369;{{ number_format($item->subtotal, 2) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="rr-items-total">
                <span class="rr-items-total-label">Total</span>
                <span class="rr-items-total-value">&#8369;{{ number_format($reorderRequest->total_amount, 2) }}</span>
            </div>
        </div>

        @if($reorderRequest->notes)
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon blue">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                    </div>
                    <div><div class="card-head-title">Store Notes</div></div>
                </div>
                <div style="font-size:13px; color:var(--text-secondary); line-height:1.6;">{{ $reorderRequest->notes }}</div>
            </div>
        @endif
    </div>

    {{-- RIGHT --}}
    <div>
        @if($reorderRequest->isPending())
            {{-- APPROVE CARD --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon green">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    </div>
                    <div>
                        <div class="card-head-title">Approve Request</div>
                        <div class="card-head-sub">Create delivery receipt</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('reorder-requests.approve', $reorderRequest) }}">
                    @csrf

                    <div class="field">
                        <label class="label">Delivery Date</label>
                        <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="input" required>
                    </div>

                    <div class="field" style="margin-top:12px;">
                        <label class="label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" rows="2" class="input" placeholder="Internal notes..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:14px;"
                            onclick="return confirm('Approve this request? A delivery receipt will be created automatically.');">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                        Approve & Create DR
                    </button>
                </form>
            </div>

            {{-- REJECT CARD --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon red">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                    </div>
                    <div>
                        <div class="card-head-title">Reject Request</div>
                        <div class="card-head-sub">Reason required</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('reorder-requests.reject', $reorderRequest) }}">
                    @csrf

                    <div class="field">
                        <label class="label">Reason</label>
                        <textarea name="rejection_reason" rows="3" class="input" placeholder="Why is this rejected?" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger" style="width:100%; margin-top:14px;"
                            onclick="return confirm('Reject this request?');">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                        Reject Request
                    </button>
                </form>
            </div>
        @else
            {{-- PROCESSED --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon {{ $reorderRequest->isApproved() ? 'green' : 'red' }}">
                        @if($reorderRequest->isApproved())
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                        @else
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="card-head-title">
                            {{ $reorderRequest->isApproved() ? 'Approved' : 'Rejected' }}
                        </div>
                        <div class="card-head-sub">
                            {{ $reorderRequest->isApproved() ? $reorderRequest->approved_at?->format('M d, Y g:i A') : $reorderRequest->rejected_at?->format('M d, Y g:i A') }}
                        </div>
                    </div>
                </div>

                @if($reorderRequest->isApproved() && $reorderRequest->deliveryReceipt)
                    <div class="info-row">
                        <span class="info-label">Delivery Receipt</span>
                        <span class="info-value">
                            <a href="{{ route('deliveries.show', $reorderRequest->deliveryReceipt) }}" style="color:#c9a961; font-weight:700;">
                                {{ $reorderRequest->deliveryReceipt->dr_number }}
                            </a>
                        </span>
                    </div>
                @endif

                @if($reorderRequest->isRejected())
                    <div class="info-row">
                        <span class="info-label">Reason</span>
                        <span class="info-value">{{ $reorderRequest->rejection_reason }}</span>
                    </div>
                @endif

                @if($reorderRequest->approver)
                    <div class="info-row">
                        <span class="info-label">By</span>
                        <span class="info-value">{{ $reorderRequest->approver->name }}</span>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>


<style>
    .rr-items-list {
        display: flex;
        flex-direction: column;
        padding: 0 4px;
    }
    .rr-item-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 14px 4px;
        border-bottom: 1px solid rgba(38, 38, 46, 0.5);
        transition: background 0.15s;
    }
    .rr-item-row:last-child { border-bottom: none; }
    .rr-item-row:hover { background: rgba(169, 120, 74, 0.04); }

    /* PRODUCT IMAGE */
    .rr-item-image {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(138, 95, 54, 0.15));
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid;
        place-items: center;
        position: relative;
    }
    .rr-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .rr-item-image-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    /* PRODUCT INFO */
    .rr-item-info {
        flex: 1;
        min-width: 0;
    }
    .rr-item-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rr-item-meta {
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 11px;
        color: var(--text-muted);
    }
    .rr-item-stock {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .rr-item-stock-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
    }

    /* COLUMNS */
    .rr-item-col {
        text-align: right;
        flex-shrink: 0;
        min-width: 80px;
    }
    .rr-item-col-label {
        font-size: 9.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 3px;
    }
    .rr-item-col-value {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
    }
    .rr-item-qty {
        min-width: 60px;
    }
    .rr-item-qty .rr-item-col-value {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.25);
        color: #60a5fa;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 800;
    }
    .rr-item-subtotal-value {
        color: var(--accent-light);
        font-size: 14px;
        font-weight: 800;
    }

    /* TOTAL */
    .rr-items-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 4px 4px;
        margin-top: 8px;
        border-top: 2px solid rgba(169, 120, 74, 0.25);
    }
    .rr-items-total-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .rr-items-total-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--accent-light);
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
    }

    @media (max-width: 700px) {
        .rr-item-row {
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px 0;
        }
        .rr-item-image {
            width: 48px;
            height: 48px;
        }
        .rr-item-info {
            flex: 1 1 calc(100% - 60px);
        }
        .rr-item-col {
            flex: 1;
            min-width: 0;
            text-align: left;
        }
        .rr-item-col-label { font-size: 9px; }
        .rr-item-col-value { font-size: 12px; }
    }
</style>
@endsection

@push('styles')
<style>
    .hero {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(20, 20, 26, 0.7));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 18px;
        position: relative;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.15), transparent 70%);
        pointer-events: none;
    }
    .hero-content { position: relative; z-index: 1; }
    .hero-label {
        font-size: 11px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.12em;
        font-weight: 700; margin-bottom: 8px;
    }
    .hero-value {
        font-size: 32px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em;
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }
    .hero-value.accent { color: #c9a961; }

    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }

    .field { display: flex; flex-direction: column; }
    .label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-secondary);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); font-weight: 600; }
    .info-value { color: var(--text-primary); text-align: right; font-weight: 500; }

    @media (max-width: 1000px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush