@extends('layouts.admin')

@section('title', $report->report_number)
@section('subtitle', 'Sales Report Details')

@section('actions')
    <a href="{{ route('consignment.reports.index') }}" class="btn btn-ghost btn-sm">Back</a>
    @if($report->status !== 'paid')
        <a href="{{ route('consignment.payments.create') }}?store_id={{ $report->store_id }}" class="btn btn-primary btn-sm">+ Record Payment</a>
    @endif
@endsection

@section('content')

@php
    $storePayments = \App\Models\ConsignmentPayment::where('store_id', $report->store_id)
        ->orderBy('payment_date', 'desc')
        ->take(10)
        ->get();
@endphp

<div class="hero-sr">
    <div class="hero-sr-left">
        <div class="hero-sr-icon">
            <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
        </div>
        <div>
            <div class="hero-sr-number">{{ $report->report_number }}</div>
            <div class="hero-sr-store">
                <a href="{{ route('stores.show', $report->store) }}" class="link-accent">{{ $report->store->store_name ?? '-' }}</a>
                <span class="dot-sep">-</span>
                <span>{{ $report->store->code ?? '' }}</span>
            </div>
            <div class="hero-sr-tags">
                <span class="badge badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
                <span class="tag">{{ $report->period_from }} to {{ $report->period_to }}</span>
            </div>
        </div>
    </div>
    <div class="hero-sr-right">
        <div class="hero-sr-stat">
            <div class="hero-sr-stat-label">Total Sales</div>
            <div class="hero-sr-stat-value">&#8369;{{ number_format($report->total_sales, 2) }}</div>
        </div>
        <div class="hero-sr-stat">
            <div class="hero-sr-stat-label">Qty Sold</div>
            <div class="hero-sr-stat-value">{{ $report->total_quantity }}</div>
        </div>
    </div>
</div>

<div class="detail-grid">
    <div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Products Sold</div>
                    <div class="card-sub">{{ $report->items->count() }} product(s)</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align:right;">Qty Sold</th>
                        <th style="text-align:right;">Unit Price</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->items as $item)
                        <tr>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary);">{{ $item->product->name ?? '-' }}</div>
                                @if($item->product->sku)
                                    <div style="font-size:11px; color:var(--text-muted); font-family:ui-monospace;">{{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td style="text-align:right; font-weight:600;">{{ $item->quantity_sold }}</td>
                            <td style="text-align:right;">&#8369;{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align:right; font-weight:700; color:#c9a961;">&#8369;{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top: 1px solid rgba(169, 120, 74, 0.2);">
                        <td colspan="3" style="text-align:right; font-weight:700; color:var(--text-secondary); padding-top:16px;">TOTAL</td>
                        <td style="text-align:right; font-weight:800; color:#c9a961; font-size:16px; padding-top:16px;">&#8369;{{ number_format($report->total_sales, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Payment History</div>
                    <div class="card-sub">{{ $storePayments->count() }} payment(s) received</div>
                </div>
                @if($report->status !== 'paid')
                    <a href="{{ route('consignment.payments.create') }}?store_id={{ $report->store_id }}"
                       class="btn btn-primary btn-sm">
                        + Add Payment
                    </a>
                @endif
            </div>

            @if($storePayments->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    </div>
                    <div class="empty-text">No payments yet</div>
                    <a href="{{ route('consignment.payments.create') }}?store_id={{ $report->store_id }}"
                       class="btn btn-primary btn-sm" style="margin-top:12px;">
                        + Record First Payment
                    </a>
                </div>
            @else
                <div class="payments-list">
                    @foreach($storePayments as $p)
                        <a href="{{ route('consignment.payments.show', $p) }}" class="payment-row">
                            <div class="payment-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            </div>
                            <div class="payment-info">
                                <div class="payment-number">{{ $p->payment_number }}</div>
                                <div class="payment-meta">{{ $p->payment_date }} - {{ ucfirst(str_replace('_',' ',$p->method)) }}</div>
                            </div>
                            <div class="payment-amount">+ &#8369;{{ number_format($p->amount, 2) }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if($report->notes)
            <div class="card">
                <div class="card-header"><div class="card-title">Notes</div></div>
                <div style="font-size:13px; color:var(--text-secondary); line-height:1.6;">{{ $report->notes }}</div>
            </div>
        @endif
    </div>

    <div>
        <div class="card">
            <div class="card-header"><div class="card-title">Summary</div></div>

            <div class="balance-list">
                <div class="balance-row">
                    <span class="balance-label">Total Sales</span>
                    <span class="balance-value">&#8369;{{ number_format($report->total_sales, 2) }}</span>
                </div>
                <div class="balance-row">
                    <span class="balance-label">Amount Paid</span>
                    <span class="balance-value green">- &#8369;{{ number_format($report->amount_paid ?? 0, 2) }}</span>
                </div>
                <div class="balance-row balance-total">
                    <span class="balance-label">Unpaid Balance</span>
                    <span class="balance-value {{ ($report->balance ?? 0) > 0 ? 'accent' : 'green' }}">
                        &#8369;{{ number_format($report->balance ?? 0, 2) }}
                    </span>
                </div>
            </div>

            @if(($report->balance ?? 0) > 0)
                <div class="progress-wrap">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $report->paid_percent }}%;"></div>
                    </div>
                    <div class="progress-label">
                        <span>{{ $report->paid_percent }}% paid</span>
                        <span>{{ $storePayments->count() }} payment(s)</span>
                    </div>
                </div>
            @endif

            @if($report->status !== 'paid')
                <a href="{{ route('consignment.payments.create') }}?store_id={{ $report->store_id }}"
                   class="btn btn-primary" style="width:100%; margin-top:16px;">
                    Record Payment
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
                    <div class="info-value">{{ $report->user->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Created at</div>
                    <div class="info-value">{{ $report->created_at->format('M d, Y - g:i A') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Period</div>
                    <div class="info-value">{{ $report->period_from }} to {{ $report->period_to }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .hero-sr {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(34, 34, 44, 0.7));
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(59, 130, 246, 0.2);
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
    .hero-sr::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.15), transparent 70%);
        pointer-events: none;
    }
    .hero-sr-left {
        display: flex; align-items: center;
        gap: 18px; position: relative; z-index: 1;
    }
    .hero-sr-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        display: grid; place-items: center;
        color: #fff;
        box-shadow: 0 12px 24px -8px rgba(59, 130, 246, 0.6);
        flex-shrink: 0;
    }
    .hero-sr-number {
        font-size: 22px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em; line-height: 1.2;
        font-family: ui-monospace, monospace;
        margin-bottom: 4px;
    }
    .hero-sr-store {
        font-size: 13px; color: var(--text-secondary);
        margin-bottom: 10px;
    }
    .link-accent { color: #c9a961; font-weight: 600; }
    .link-accent:hover { text-decoration: underline; }
    .dot-sep { margin: 0 6px; color: var(--text-muted); }
    .hero-sr-tags { display: flex; gap: 6px; flex-wrap: wrap; }
    .tag {
        display: inline-flex; align-items: center;
        padding: 4px 10px;
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 5px;
        font-size: 11px; font-weight: 600;
        color: #60a5fa;
    }
    .hero-sr-right {
        display: flex;
        gap: 28px;
        position: relative; z-index: 1;
    }
    .hero-sr-stat { text-align: right; }
    .hero-sr-stat-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-bottom: 4px;
    }
    .hero-sr-stat-value {
        font-size: 20px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }

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
    .balance-value.accent { color: #c9a961; }
    .balance-total {
        padding-top: 14px;
        margin-top: 4px;
        border-top: 1px solid rgba(169, 120, 74, 0.2) !important;
    }

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

    .payments-list { display: flex; flex-direction: column; gap: 6px; }
    .payment-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 10px;
        transition: all 0.15s;
    }
    .payment-row:hover { background: rgba(255, 255, 255, 0.03); }
    .payment-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.2);
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .payment-info { flex: 1; min-width: 0; }
    .payment-number {
        font-size: 12px; font-weight: 700;
        color: var(--text-primary);
        font-family: ui-monospace;
    }
    .payment-meta {
        font-size: 10px; color: var(--text-muted);
        margin-top: 2px;
    }
    .payment-amount {
        font-size: 13px; font-weight: 700;
        color: #22c55e; flex-shrink: 0;
    }

    .progress-wrap { margin-top: 16px; }
    .progress-bar {
        height: 6px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 3px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #4ade80);
        border-radius: 3px;
        transition: width 0.4s;
        box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 6px;
        font-weight: 600;
    }

    .balance-value.green { color: #22c55e; }

    .empty-state {
        padding: 30px 20px;
        text-align: center;
    }
    .empty-icon {
        color: var(--text-muted);
        opacity: 0.4;
        margin-bottom: 8px;
        display: flex;
        justify-content: center;
    }
    .empty-text {
        font-size: 12px;
        color: var(--text-muted);
    }
    @media (max-width: 1100px) {
        .detail-grid { grid-template-columns: 1fr; }
        .hero-sr { flex-direction: column; align-items: flex-start; }
        .hero-sr-right { width: 100%; justify-content: space-between; }
    }
</style>
@endpush