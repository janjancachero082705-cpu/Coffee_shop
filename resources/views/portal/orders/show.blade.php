@extends('portal.layouts.app')

@section('title', $order->request_number)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.orders.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="page-title">{{ $order->request_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($order->requested_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

{{-- STATUS CARD --}}
<div class="hero">
    <div class="hero-content">
        <div class="hero-label">Order Status</div>
        <div class="hero-value {{ $order->status === 'approved' ? 'green' : ($order->status === 'rejected' ? '' : 'accent') }}">
            {{ ucfirst($order->status) }}
        </div>
        <div class="hero-meta">
            @if($order->isApproved())
                <div class="hero-meta-item">
                    <span class="hero-meta-dot green"></span>
                    Approved {{ $order->approved_at ? $order->approved_at->diffForHumans() : '' }}
                </div>
                @if($order->deliveryReceipt)
                    <div class="hero-meta-item">
                        <span class="hero-meta-dot"></span>
                        DR: {{ $order->deliveryReceipt->dr_number }}
                    </div>
                @endif
            @elseif($order->isPending())
                <div class="hero-meta-item">
                    <span class="hero-meta-dot"></span>
                    Waiting for admin approval
                </div>
            @elseif($order->isRejected())
                <div class="hero-meta-item">
                    <span class="hero-meta-dot"></span>
                    Reason: {{ $order->rejection_reason }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ITEMS --}}
<div class="card">
    <div class="card-head">
        <div class="card-head-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
            <div class="card-head-title">Requested Items</div>
            <div class="card-head-sub">{{ $order->items->count() }} item(s)</div>
        </div>
    </div>

    <div class="items-list">
        @foreach($order->items as $item)
            <div class="item-row">
                <div class="item-thumb">
                    @if($item->product && $item->product->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                    @else
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                <div class="item-info">
                    <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                    <div class="item-meta">{{ $item->quantity_requested }} x &#8369;{{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="item-total">&#8369;{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
    </div>

    <div class="total-row">
        <span>Total</span>
        <strong>&#8369;{{ number_format($order->total_amount, 2) }}</strong>
    </div>
</div>

@if($order->notes)
    <div class="card">
        <div class="card-head">
            <div class="card-head-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
            </div>
            <div>
                <div class="card-head-title">Notes</div>
            </div>
        </div>
        <div class="notes-body">{{ $order->notes }}</div>
    </div>
@endif

@if($order->isPending())
    <form method="POST" action="{{ route('portal.orders.cancel', $order->id) }}"
          onsubmit="return confirm('Cancel this order?');">
        @csrf
        <button type="submit" class="btn-cancel">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
            Cancel Order
        </button>
    </form>
@endif

@endsection

@push('styles')
<style>
    .items-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }
    .item-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
    }
    .item-row:last-child { border-bottom: none; }
    .item-thumb {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        display: grid;
        place-items: center;
        color: rgba(201, 169, 97, 0.5);
        overflow: hidden;
        flex-shrink: 0;
    }
    .item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .item-info { flex: 1; min-width: 0; }
    .item-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 3px;
    }
    .item-meta {
        font-size: 11px;
        color: var(--text-muted);
    }
    .item-total {
        font-size: 14px;
        font-weight: 800;
        color: var(--accent-light);
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        border-top: 2px solid rgba(169, 120, 74, 0.3);
        font-size: 13px;
        color: var(--text-secondary);
    }
    .total-row strong {
        font-size: 22px;
        font-weight: 800;
        color: var(--accent-light);
        font-variant-numeric: tabular-nums;
    }

    .notes-body {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .btn-cancel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        color: #ef4444;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-cancel:hover {
        background: rgba(239, 68, 68, 0.2);
    }
</style>
@endpush