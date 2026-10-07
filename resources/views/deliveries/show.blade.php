@extends('layouts.admin')

@section('title', $delivery->dr_number)
@section('subtitle', 'Delivery Receipt details')

@section('actions')
    <a href="{{ route('deliveries.index') }}" class="btn btn-ghost btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>
    @if($delivery->balance > 0)
        @if($delivery->customer_confirmed)
            <a href="{{ route('consignment.payments.create') }}?store_id={{ $delivery->store_id }}&delivery_receipt_id={{ $delivery->id }}" class="btn btn-primary btn-sm">+ Record Payment</a>
        @else
            <button type="button" class="btn btn-ghost btn-sm" disabled
                    style="opacity:0.5;cursor:not-allowed;"
                    title="Customer must confirm receipt first">
                🔒 Waiting for Confirmation
            </button>
        @endif
    @endif

<style>
    .ds-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        margin-top: 16px;
        margin-bottom: 16px;
    }
    .ds-card.confirmed {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .ds-card.transit {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .ds-card.ready {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
    }
    .ds-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .ds-card.confirmed .ds-icon {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }
    .ds-card.transit .ds-icon {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }
    .ds-card.ready .ds-icon {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }
    .ds-text { flex: 1; min-width: 0; }
    .ds-title {
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .ds-card.confirmed .ds-title { color: #22c55e; }
    .ds-card.transit .ds-title { color: #3b82f6; }
    .ds-card.ready .ds-title { color: #f59e0b; }
    .ds-sub {
        font-size: 11.5px;
        color: #6b6862;
    }
    .ds-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 14px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: 11.5px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 6px 14px -6px rgba(169, 120, 74, 0.7);
        -webkit-tap-highlight-color: transparent;
    }
    .ds-btn:active { transform: scale(0.95); }
    .ds-btn svg { flex-shrink: 0; }
</style>


    @if(!$delivery->customer_confirmed && !$delivery->out_for_delivery_at && $delivery->status === 'pending')
        <form method="POST"
              action="{{ route('deliveries.out-for-delivery', $delivery->id) }}" id="shipForm"
              style="display:inline-block;"">
            @csrf
            <button type="submit" class="btn btn-sm btn-ship">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                Ship
            </button>
        </form>
    @endif
@endsection

@section('content')

{{-- SHIP BUTTON (TOP OF CONTENT) --}}
@if(!$delivery->customer_confirmed && !$delivery->out_for_delivery_at && $delivery->status === 'pending')
    <div class="ship-top-card">
        <div class="ship-top-info">
            <div class="ship-top-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div>
                <div class="ship-top-title">Ready to Ship</div>
                <div class="ship-top-sub">Pending pa ni — i-ship para mo-out for delivery</div>
            </div>
        </div>
        <form method="POST"
              action="{{ route('deliveries.out-for-delivery', $delivery->id) }}" id="shipForm"">
            @csrf
            <button type="submit" class="btn-ship">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                Ship This Delivery
            </button>
        </form>
    </div>

{{-- PROFESSIONAL CONFIRM MODAL --}}
<div id="shipModal" class="ship-modal-overlay" style="display:none;" aria-hidden="true">
    <div class="ship-modal" role="dialog" aria-modal="true" aria-labelledby="shipModalTitle">
        <div class="ship-modal-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1.5"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>

        <div class="ship-modal-title" id="shipModalTitle">Ship This Delivery?</div>
        <div class="ship-modal-sub">DR-<strong>{{ $delivery->dr_number ?? '—' }}</strong></div>

        <div class="ship-modal-body">
            <div class="ship-modal-row">
                <span>Store</span>
                <strong>{{ $delivery->store->name ?? '—' }}</strong>
            </div>
            <div class="ship-modal-row">
                <span>Items</span>
                <strong>{{ $delivery->items->count() }} product(s)</strong>
            </div>
            <div class="ship-modal-row">
                <span>Total</span>
                <strong style="color:#c9a961;">&#8369;{{ number_format($delivery->total_amount, 2) }}</strong>
            </div>
        </div>

        <div class="ship-modal-warning">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            I-mark as <strong>Out for Delivery</strong>. Makita dayon ni sa customer portal.
        </div>

        <div class="ship-modal-actions">
            <button type="button" class="ship-modal-btn-cancel" data-modal-close>
                Cancel
            </button>
            <button type="button" class="ship-modal-btn-confirm" id="shipModalConfirm">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                Yes, Ship Now
            </button>
        </div>
    </div>
</div>
@endif


@php
    $statusColor = [
        'pending' => '#f59e0b',
        'partial' => '#3b82f6',
        'paid'    => '#22c55e',
    ][$delivery->status] ?? '#6b6862';
@endphp

<div class="hero-dr">
    <div class="hero-dr-left">
        <div class="hero-dr-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div>
            <div class="hero-dr-number">{{ $delivery->dr_number }}</div>
            <div class="hero-dr-store">
                <a href="{{ route('stores.show', $delivery->store) }}" class="link-accent">{{ $delivery->store->store_name ?? '-' }}</a>
                <span class="dot-sep">-</span>
                <span>{{ $delivery->store->code ?? '' }}</span>
            </div>
            <div class="hero-dr-tags">
                <span class="badge badge-{{ $delivery->status }}">{{ ucfirst($delivery->status) }}</span>
                <span class="tag">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</span>
            </div>
        </div>
    </div>
    <div class="hero-dr-right">
        <div class="hero-dr-stat">
            <div class="hero-dr-stat-label">Total</div>
            <div class="hero-dr-stat-value">&#8369;{{ number_format($delivery->total_amount, 2) }}</div>
        </div>
        <div class="hero-dr-stat">
            <div class="hero-dr-stat-label">Paid</div>
            <div class="hero-dr-stat-value green">&#8369;{{ number_format($delivery->amount_paid, 2) }}</div>
        </div>
        <div class="hero-dr-stat">
            <div class="hero-dr-stat-label">Balance</div>
            <div class="hero-dr-stat-value" style="color: {{ $delivery->balance > 0 ? '#f59e0b' : '#22c55e' }};">
                &#8369;{{ number_format($delivery->balance, 2) }}
            </div>
        </div>
    </div>
</div>

<div class="detail-grid">

    <div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Delivery Items</div>
                    <div class="card-sub">{{ $delivery->items->count() }} product{{ $delivery->items->count() !== 1 ? 's' : '' }}</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Unit Price</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delivery->items as $item)
                        <tr>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary);">{{ $item->product->name ?? '-' }}</div>
                                @if($item->product->sku)
                                    <div style="font-size:11px; color:var(--text-muted); font-family:ui-monospace;">{{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="font-weight:600;">{{ $item->quantity_delivered }}</div>
                                <div style="font-size:10px; color:var(--text-muted);">
                                    sold: {{ $item->quantity_sold }} / ret: {{ $item->quantity_returned }}
                                </div>
                            </td>
                            <td style="text-align:right;">&#8369;{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align:right; font-weight:700; color:#c9a961;">&#8369;{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top: 1px solid rgba(169, 120, 74, 0.2);">
                        <td colspan="3" style="text-align:right; font-weight:700; color:var(--text-secondary); padding-top:16px;">TOTAL</td>
                        <td style="text-align:right; font-weight:800; color:#c9a961; font-size:16px; padding-top:16px;">&#8369;{{ number_format($delivery->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($delivery->notes)
            <div class="card">
                <div class="card-header"><div class="card-title">Notes</div></div>
                <div style="font-size:13px; color:var(--text-secondary); line-height:1.6;">{{ $delivery->notes }}</div>
            </div>
        @endif
    </div>

    <div>
        <div class="card">
            <div class="card-header"><div class="card-title">Payment Status</div></div>

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
                    <span class="balance-value" style="color: {{ $delivery->balance > 0 ? '#f59e0b' : '#22c55e' }};">
                        &#8369;{{ number_format($delivery->balance, 2) }}
                    </span>
                </div>
            </div>

            @if($delivery->balance > 0)
                @if($delivery->customer_confirmed)
                    <a href="{{ route('consignment.payments.create') }}?store_id={{ $delivery->store_id }}&delivery_receipt_id={{ $delivery->id }}"
                       class="btn btn-primary" style="width:100%; margin-top:16px;">
                        Record Payment
                    </a>
                @else
                    <button type="button" disabled
                            style="width:100%; margin-top:16px; padding:11px 18px; border-radius:10px;
                                   background:rgba(255,255,255,0.05); color:#8a8378; border:1px solid rgba(255,255,255,0.08);
                                   font-weight:800; font-family:inherit; cursor:not-allowed;
                                   display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Waiting for Customer Confirmation
                    </button>
                    <div style="margin-top:10px; padding:10px 12px; background:rgba(245,158,11,0.08);
                                border:1px solid rgba(245,158,11,0.25); border-radius:10px;
                                font-size:11.5px; color:#d4a35a; line-height:1.5;">
                        ⚠️ Dili pa ma-record ang payment. Kinahanglan mo-confirm una ang customer nga nadawat niya ang products.
                    </div>
                @endif
            @else
                <div class="paid-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    Fully Paid
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">Details</div></div>

            <div class="info-list">
                <div class="info-row">
                    <div class="info-label">Created by</div>
                    <div class="info-value">{{ $delivery->user->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Created at</div>
                    <div class="info-value">{{ $delivery->created_at->format('M d, Y - g:i A') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Last updated</div>
                    <div class="info-value">{{ $delivery->updated_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">Actions</div></div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-ghost" style="width:100%;">
                    Edit Delivery
                </a>
                <form method="POST" action="{{ route('deliveries.destroy', $delivery) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<style>
    .ds-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        margin-top: 16px;
        margin-bottom: 16px;
    }
    .ds-card.confirmed {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .ds-card.transit {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .ds-card.ready {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
    }
    .ds-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .ds-card.confirmed .ds-icon {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }
    .ds-card.transit .ds-icon {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }
    .ds-card.ready .ds-icon {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }
    .ds-text { flex: 1; min-width: 0; }
    .ds-title {
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .ds-card.confirmed .ds-title { color: #22c55e; }
    .ds-card.transit .ds-title { color: #3b82f6; }
    .ds-card.ready .ds-title { color: #f59e0b; }
    .ds-sub {
        font-size: 11.5px;
        color: #6b6862;
    }
    .ds-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 14px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: 11.5px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 6px 14px -6px rgba(169, 120, 74, 0.7);
        -webkit-tap-highlight-color: transparent;
    }
    .ds-btn:active { transform: scale(0.95); }
    .ds-btn svg { flex-shrink: 0; }
</style>

@endsection

@push('styles')
<style>
    .hero-dr {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.7));
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        position: relative;
        overflow: hidden;
    }
    .hero-dr::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.15), transparent 70%);
        pointer-events: none;
    }
    .hero-dr-left {
        display: flex;
        align-items: center;
        gap: 18px;
        position: relative; z-index: 1;
    }
    .hero-dr-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid; place-items: center;
        color: #fff;
        box-shadow: 0 12px 24px -8px rgba(169, 120, 74, 0.6);
        flex-shrink: 0;
    }
    .hero-dr-number {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.2;
        font-family: ui-monospace, monospace;
        margin-bottom: 4px;
    }
    .hero-dr-store {
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 10px;
    }
    .link-accent { color: #c9a961; font-weight: 600; }
    .link-accent:hover { text-decoration: underline; }
    .dot-sep { margin: 0 6px; color: var(--text-muted); }
    .hero-dr-tags { display: flex; gap: 6px; flex-wrap: wrap; }
    .tag {
        display: inline-flex; align-items: center;
        padding: 4px 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #c9a961;
    }
    .hero-dr-right {
        display: flex;
        gap: 28px;
        position: relative; z-index: 1;
    }
    .hero-dr-stat { text-align: right; }
    .hero-dr-stat-label {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .hero-dr-stat-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }
    .hero-dr-stat-value.green { color: #22c55e; }

    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }

    .balance-list { display: flex; flex-direction: column; }
    .balance-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        font-size: 13px;
    }
    .balance-row + .balance-row { border-top: 1px solid rgba(255, 255, 255, 0.04); }
    .balance-label { color: var(--text-secondary); }
    .balance-value { font-weight: 700; color: var(--text-primary); }
    .balance-value.green { color: #22c55e; }
    .balance-total {
        padding-top: 14px;
        margin-top: 4px;
        border-top: 1px solid rgba(169, 120, 74, 0.2) !important;
    }
    .balance-total .balance-value { font-size: 15px; }

    .paid-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        margin-top: 16px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.25);
        border-radius: 10px;
        color: #22c55e;
        font-weight: 700;
        font-size: 13px;
    }

    .info-list { display: flex; flex-direction: column; }
    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        font-size: 12px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); font-weight: 600; }
    .info-value { color: var(--text-primary); text-align: right; font-weight: 500; }

    @media (max-width: 1100px) {
        .detail-grid { grid-template-columns: 1fr; }
        .hero-dr { flex-direction: column; align-items: flex-start; }
        .hero-dr-right { width: 100%; justify-content: space-between; }
    }
    @media (max-width: 700px) {
        .hero-dr-right { flex-direction: column; gap: 14px; }
        .hero-dr-stat { text-align: left; }
    }
</style>
@endpush

@push('styles')
<style>
    .btn-ship {
        background: linear-gradient(135deg, #c9a961, #8a5f36) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 800 !important;
        box-shadow: 0 6px 14px -6px rgba(169, 120, 74, 0.7);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-ship:hover {
        background: linear-gradient(135deg, #d4b673, #9a6b3f) !important;
    }
    .btn-ship:active { transform: scale(0.97); }
    .btn-ship svg { flex-shrink: 0; }
</style>
@endpush

@push('styles')
<style>
    /* ==== SHIP CARD ==== */
    .ship-top-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        margin-bottom: 16px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.15), rgba(138, 95, 54, 0.1));
        border: 1px solid rgba(201, 169, 97, 0.35);
        border-radius: 16px;
        flex-wrap: wrap;
    }
    .ship-top-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 200px;
    }
    .ship-top-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(201, 169, 97, 0.25);
        color: #c9a961;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .ship-top-title {
        font-size: 14px;
        font-weight: 800;
        color: #c9a961;
        margin-bottom: 2px;
    }
    .ship-top-sub {
        font-size: 11.5px;
        color: var(--text-muted, #888);
    }
    .btn-ship {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 22px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 13.5px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.8);
        letter-spacing: 0.02em;
        -webkit-tap-highlight-color: transparent;
        white-space: nowrap;
    }
    .btn-ship:hover { background: linear-gradient(135deg, #d4b673, #9a6b3f); transform: translateY(-1px); }
    .btn-ship:active { transform: scale(0.98); }
    .btn-ship svg { flex-shrink: 0; }

    /* ==== MODAL OVERLAY ==== */
    .ship-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(10, 8, 6, 0.72);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: shipFadeIn 0.2s ease;
    }
    @keyframes shipFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    /* ==== MODAL PANEL ==== */
    .ship-modal {
        width: 100%;
        max-width: 420px;
        background: linear-gradient(165deg, #1e1a16 0%, #15120f 100%);
        border: 1px solid rgba(201, 169, 97, 0.28);
        border-radius: 20px;
        padding: 26px 24px 22px;
        box-shadow:
            0 30px 60px -20px rgba(0, 0, 0, 0.7),
            0 0 0 1px rgba(255, 255, 255, 0.03) inset,
            0 0 80px -20px rgba(201, 169, 97, 0.25);
        animation: shipModalIn 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: center;
    }
    @keyframes shipModalIn {
        from { opacity: 0; transform: scale(0.92) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .ship-modal-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.28), rgba(138, 95, 54, 0.15));
        border: 1px solid rgba(201, 169, 97, 0.35);
        color: #c9a961;
        display: grid;
        place-items: center;
        box-shadow: 0 10px 30px -10px rgba(201, 169, 97, 0.5);
    }

    .ship-modal-title {
        font-size: 19px;
        font-weight: 800;
        color: #f5f3f0;
        margin-bottom: 6px;
        letter-spacing: -0.01em;
    }
    .ship-modal-sub {
        font-size: 12.5px;
        color: #9a9188;
        margin-bottom: 20px;
    }
    .ship-modal-sub strong {
        color: #c9a961;
        font-weight: 700;
    }

    .ship-modal-body {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 16px;
        text-align: left;
    }
    .ship-modal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 12.5px;
    }
    .ship-modal-row + .ship-modal-row {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .ship-modal-row span {
        color: #8a8378;
    }
    .ship-modal-row strong {
        color: #f5f3f0;
        font-weight: 700;
    }

    .ship-modal-warning {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 12px;
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.22);
        border-radius: 10px;
        font-size: 11.5px;
        color: #d4a35a;
        text-align: left;
        margin-bottom: 20px;
        line-height: 1.45;
    }
    .ship-modal-warning svg {
        flex-shrink: 0;
        margin-top: 1px;
        color: #f59e0b;
    }
    .ship-modal-warning strong {
        color: #f5b945;
        font-weight: 700;
    }

    .ship-modal-actions {
        display: flex;
        gap: 10px;
    }
    .ship-modal-btn-cancel,
    .ship-modal-btn-confirm {
        flex: 1;
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: none;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        letter-spacing: 0.01em;
        -webkit-tap-highlight-color: transparent;
    }
    .ship-modal-btn-cancel {
        background: rgba(255, 255, 255, 0.06);
        color: #c4bdb4;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .ship-modal-btn-cancel:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #f5f3f0;
    }
    .ship-modal-btn-confirm {
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        box-shadow: 0 10px 24px -8px rgba(201, 169, 97, 0.8);
    }
    .ship-modal-btn-confirm:hover {
        background: linear-gradient(135deg, #d4b673, #9a6b3f);
    }
    .ship-modal-btn-confirm:active,
    .ship-modal-btn-cancel:active {
        transform: scale(0.98);
    }

    @media (max-width: 480px) {
        .ship-modal { padding: 22px 18px 18px; border-radius: 18px; }
        .ship-modal-title { font-size: 17px; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const form = document.getElementById('shipForm');
    const modal = document.getElementById('shipModal');
    const confirmBtn = document.getElementById('shipModalConfirm');

    if (!form || !modal) {
        console.warn('Ship modal: form or modal not found');
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        openModal();
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10" opacity="0.3"/><path d="M22 12a10 10 0 0 1-10 10" stroke-linecap="round"/></svg> Shipping...';
            form.submit();
        });
    }

    modal.querySelectorAll('[data-modal-close]').forEach(function(el) {
        el.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
    });

    function openModal() {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
})();
</script>
@endpush