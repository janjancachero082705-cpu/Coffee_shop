@extends('portal.layouts.app')

@section('title', $delivery->dr_number)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.deliveries.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="page-title">{{ $delivery->dr_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

<div class="hero">
    <div class="hero-content">
        <div class="hero-label">Total Amount</div>
        <div class="hero-value accent">&#8369;{{ number_format($delivery->total_amount, 2) }}</div>
        <div class="hero-meta">
            <div class="hero-meta-item">
                <span class="hero-meta-dot {{ $delivery->status === 'paid' ? 'green' : '' }}"></span>
                {{ ucfirst($delivery->status) }}
            </div>
        </div>
    </div>
</div>

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
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                <div class="item-info">
                    <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                    <div class="item-meta">{{ $item->quantity_delivered }} x &#8369;{{ number_format($item->unit_price, 2) }}</div>
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

<div class="card">
    <div class="card-head">
        <div class="card-head-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div>
            <div class="card-head-title">Payment Status</div>
        </div>
    </div>

    <div class="balance-list">
        <div class="balance-row">
            <span class="balance-label">Total Amount</span>
            <span class="balance-value">&#8369;{{ number_format($delivery->total_amount, 2) }}</span>
        </div>
        <div class="balance-row">
            <span class="balance-label">Amount Paid</span>
            <span class="balance-value green">- &#8369;{{ number_format($delivery->amount_paid, 2) }}</span>
        </div>
        <div class="balance-row balance-total">
            <span class="balance-label">Balance</span>
            <span class="balance-value {{ $delivery->balance > 0 ? 'accent' : 'green' }}">&#8369;{{ number_format($delivery->balance, 2) }}</span>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .items-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }
    .item-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border); }
    .item-row:last-child { border-bottom: none; }
    .item-thumb { width: 44px; height: 44px; border-radius: 10px; background: rgba(169, 120, 74, 0.1); display: grid; place-items: center; color: rgba(201, 169, 97, 0.5); overflow: hidden; flex-shrink: 0; }
    .item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .item-info { flex: 1; min-width: 0; }
    .item-name { font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 3px; }
    .item-meta { font-size: 11px; color: var(--text-muted); }
    .item-total { font-size: 14px; font-weight: 800; color: var(--accent-light); font-variant-numeric: tabular-nums; flex-shrink: 0; }
    .total-row { display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 2px solid rgba(169, 120, 74, 0.3); font-size: 13px; color: var(--text-secondary); }
    .total-row strong { font-size: 22px; font-weight: 800; color: var(--accent-light); font-variant-numeric: tabular-nums; }
    .balance-list { display: flex; flex-direction: column; }
    .balance-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; font-size: 13px; }
    .balance-row + .balance-row { border-top: 1px solid var(--border); }
    .balance-label { color: var(--text-secondary); }
    .balance-value { font-weight: 700; color: var(--text-primary); font-variant-numeric: tabular-nums; }
    .balance-value.green { color: var(--success); }
    .balance-value.accent { color: var(--accent-light); }
    .balance-total { padding-top: 14px; border-top: 1px solid rgba(169, 120, 74, 0.25) !important; }
    .balance-total .balance-value { font-size: 15px; }
</style>
@endpush