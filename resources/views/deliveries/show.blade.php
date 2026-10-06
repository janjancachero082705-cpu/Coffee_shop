@extends('layouts.admin')

@section('title', $delivery->dr_number)
@section('subtitle', 'Delivery Receipt details')

@section('actions')
    <a href="{{ route('deliveries.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    <a href="{{ route('consignment.payments.create') }}?store_id={{ $delivery->store_id }}&delivery_receipt_id={{ $delivery->id }}" class="btn btn-primary btn-sm">+ Record Payment</a>
@endsection

@section('content')

@php
    $statusColor = [
        'pending' => '#f59e0b',
        'partial' => '#3b82f6',
        'paid'    => '#22c55e',
        'overdue' => '#ef4444',
    ][$delivery->status] ?? '#6b6862';
@endphp

{{-- ═══ HERO ═══ --}}
<div class="hero-dr">
    <div class="hero-dr-left">
        <div class="hero-dr-icon">🚚</div>
        <div>
            <div class="hero-dr-number">{{ $delivery->dr_number }}</div>
            <div class="hero-dr-store">
                <a href="{{ route('stores.show', $delivery->store) }}" class="link-accent">{{ $delivery->store->store_name ?? '—' }}</a>
                <span class="dot-sep">·</span>
                <span>{{ $delivery->store->code ?? '' }}</span>
            </div>
            <div class="hero-dr-tags">
                <span class="badge badge-{{ $delivery->status }}">{{ ucfirst($delivery->status) }}</span>
                <span class="tag">{{ $delivery->delivery_date }}</span>
                @if($delivery->due_date)
                    <span class="tag">Due: {{ $delivery->due_date }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="hero-dr-right">
        <div class="hero-dr-stat">
            <div class="hero-dr-stat-label">Total</div>
            <div class="hero-dr-stat-value">₱{{ number_format($delivery->total_amount, 2) }}</div>
        </div>
        <div class="hero-dr-stat">
            <div class="hero-dr-stat-label">Paid</div>
            <div class="hero-dr-stat-value green">₱{{ number_format($delivery->amount_paid, 2) }}</div>
        </div>
        <div class="hero-dr-stat">
            <div class="hero-dr-stat-label">Balance</div>
            <div class="hero-dr-stat-value" style="color: {{ $delivery->balance > 0 ? '#f59e0b' : '#22c55e' }};">
                ₱{{ number_format($delivery->balance, 2) }}
            </div>
        </div>
    </div>
</div>

<div class="detail-grid">

    {{-- ═══ LEFT ═══ --}}
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
                                <div style="font-weight:600; color:var(--text-primary);">{{ $item->product->name ?? '—' }}</div>
                                @if($item->product->sku)
                                    <div style="font-size:11px; color:var(--text-muted); font-family:ui-monospace;">{{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="font-weight:600;">{{ $item->quantity_delivered }}</div>
                                <div style="font-size:10px; color:var(--text-muted);">
                                    sold: {{ $item->quantity_sold }} · ret: {{ $item->quantity_returned }}
                                </div>
                            </td>
                            <td style="text-align:right;">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align:right; font-weight:700; color:#c9a961;">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top: 1px solid rgba(169, 120, 74, 0.2);">
                        <td colspan="3" style="text-align:right; font-weight:700; color:var(--text-secondary); padding-top:16px;">TOTAL</td>
                        <td style="text-align:right; font-weight:800; color:#c9a961; font-size:16px; padding-top:16px;">₱{{ number_format($delivery->total_amount, 2) }}</td>
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

    {{-- ═══ RIGHT ═══ --}}
    <div>
        <div class="card">
            <div class="card-header"><div class="card-title">Payment Status</div></div>

            <div class="balance-list">
                <div class="balance-row">
                    <span class="balance-label">Total Amount</span>
                    <span class="balance-value">₱{{ number_format($delivery->total_amount, 2) }}</span>
                </div>
                <div class="balance-row">
                    <span class="balance-label">Amount Paid</span>
                    <span class="balance-value green">- ₱{{ number_format($delivery->amount_paid, 2) }}</span>
                </div>
                <div class="balance-row balance-total">
                    <span class="balance-label">Balance</span>
                    <span class="balance-value" style="color: {{ $delivery->balance > 0 ? '#f59e0b' : '#22c55e' }};">
                        ₱{{ number_format($delivery->balance, 2) }}
                    </span>
                </div>
            </div>

            @if($delivery->balance > 0)
                <a href="{{ route('consignment.payments.create') }}?store_id={{ $delivery->store_id }}&delivery_receipt_id={{ $delivery->id }}"
                   class="btn btn-primary" style="width:100%; margin-top:16px;">
                    💵 Record Payment
                </a>
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
                    <div class="info-value">{{ $delivery->user->name ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Created at</div>
                    <div class="info-value">{{ $delivery->created_at->format('M d, Y · g:i A') }}</div>
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
                    ✏ Edit Delivery
                </a>
                <form method="POST" action="{{ route('deliveries.destroy', $delivery) }}"
                      onsubmit="return confirm('Delete this delivery? This will revert store inventory.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;">
                        🗑 Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ═══ HERO ═══ */
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
        position: relative;
        z-index: 1;
    }
    .hero-dr-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid; place-items: center;
        color: #fff;
        font-size: 26px;
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
        position: relative;
        z-index: 1;
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

    /* ═══ GRID ═══ */
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }

    /* ═══ BALANCE ═══ */
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

    /* ═══ INFO ═══ */
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