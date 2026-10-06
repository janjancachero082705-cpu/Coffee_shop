@extends('layouts.admin')

@section('title', $payment->payment_number)
@section('subtitle', 'Payment details')

@section('actions')
    <a href="{{ route('consignment.payments.index') }}" class="btn btn-ghost btn-sm">â† Back</a>
@endsection

@section('content')

@php
    $methodIcons = ['cash'=>'ðŸ’µ','gcash'=>'ðŸ“±','maya'=>'ðŸ“±','bank_transfer'=>'ðŸ¦','check'=>'ðŸ“„'];
@endphp

{{-- â•â•â• HERO â•â•â• --}}
<div class="hero-pay">
    <div class="hero-pay-left">
        <div class="hero-pay-icon">{{ $methodIcons[$payment->method] ?? 'ðŸ’µ' }}</div>
        <div>
            <div class="hero-pay-number">{{ $payment->payment_number }}</div>
            <div class="hero-pay-store">
                <a href="{{ route('stores.show', $payment->store) }}" class="link-accent">{{ $payment->store->store_name ?? 'â€”' }}</a>
                <span class="dot-sep">Â·</span>
                <span>{{ $payment->store->code ?? '' }}</span>
            </div>
            <div class="hero-pay-tags">
                <span class="tag">{{ ucfirst(str_replace('_',' ',$payment->method)) }}</span>
                <span class="tag">{{ $payment->payment_date }}</span>
                @if($payment->reference_number)
                    <span class="tag">Ref: {{ $payment->reference_number }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="hero-pay-right">
        <div class="hero-pay-amount-label">Amount Paid</div>
        <div class="hero-pay-amount">&#8369;{{ number_format($payment->amount, 2) }}</div>
    </div>
</div>

<div class="detail-grid">

    {{-- â•â•â• LEFT â•â•â• --}}
    <div>
        <div class="card">
            <div class="card-header"><div class="card-title">Payment Details</div></div>

            <div class="info-list">
                <div class="info-row">
                    <div class="info-label">Payment #</div>
                    <div class="info-value" style="font-family:ui-monospace;">{{ $payment->payment_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Store</div>
                    <div class="info-value">
                        <a href="{{ route('stores.show', $payment->store) }}" class="link-accent">{{ $payment->store->store_name ?? 'â€”' }}</a>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Payment Date</div>
                    <div class="info-value">{{ $payment->payment_date }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Method</div>
                    <div class="info-value">{{ ucfirst(str_replace('_',' ',$payment->method)) }}</div>
                </div>
                @if($payment->reference_number)
                    <div class="info-row">
                        <div class="info-label">Reference #</div>
                        <div class="info-value" style="font-family:ui-monospace;">{{ $payment->reference_number }}</div>
                    </div>
                @endif
                @if($payment->notes)
                    <div class="info-row">
                        <div class="info-label">Notes</div>
                        <div class="info-value">{{ $payment->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        @if($payment->deliveryReceipt || $payment->salesReport)
            <div class="card">
                <div class="card-header"><div class="card-title">Linked To</div></div>

                @if($payment->deliveryReceipt)
                    <a href="{{ route('deliveries.show', $payment->deliveryReceipt) }}" class="linked-item">
                        <div class="linked-icon">ðŸšš</div>
                        <div class="linked-info">
                            <div class="linked-title">Delivery Receipt</div>
                            <div class="linked-sub">{{ $payment->deliveryReceipt->dr_number }} Â· &#8369;{{ number_format($payment->deliveryReceipt->total_amount, 2) }}</div>
                        </div>
                        <div class="linked-arrow">â†’</div>
                    </a>
                @endif

                @if($payment->salesReport)
                    <a href="{{ route('consignment.reports.show', $payment->salesReport) }}" class="linked-item">
                        <div class="linked-icon">ðŸ“Š</div>
                        <div class="linked-info">
                            <div class="linked-title">Sales Report</div>
                            <div class="linked-sub">{{ $payment->salesReport->report_number }} Â· &#8369;{{ number_format($payment->salesReport->total_sales, 2) }}</div>
                        </div>
                        <div class="linked-arrow">â†’</div>
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- â•â•â• RIGHT â•â•â• --}}
    <div>
        <div class="card">
            <div class="card-header"><div class="card-title">Amount</div></div>
            <div class="amount-big">&#8369;{{ number_format($payment->amount, 2) }}</div>
            <div class="amount-label">Received via {{ ucfirst(str_replace('_',' ',$payment->method)) }}</div>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">Recorded By</div></div>
            <div class="info-list">
                <div class="info-row">
                    <div class="info-label">User</div>
                    <div class="info-value">{{ $payment->user->name ?? 'â€”' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Created</div>
                    <div class="info-value">{{ $payment->created_at->format('M d, Y Â· g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* â•â•â• HERO â•â•â• */
    .hero-pay {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(34, 34, 44, 0.7));
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(34, 197, 94, 0.2);
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
    .hero-pay::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(34, 197, 94, 0.15), transparent 70%);
        pointer-events: none;
    }
    .hero-pay-left {
        display: flex; align-items: center;
        gap: 18px; position: relative; z-index: 1;
    }
    .hero-pay-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #22c55e, #15803d);
        display: grid; place-items: center;
        font-size: 26px;
        box-shadow: 0 12px 24px -8px rgba(34, 197, 94, 0.6);
        flex-shrink: 0;
    }
    .hero-pay-number {
        font-size: 22px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em; line-height: 1.2;
        font-family: ui-monospace, monospace;
        margin-bottom: 4px;
    }
    .hero-pay-store {
        font-size: 13px; color: var(--text-secondary);
        margin-bottom: 10px;
    }
    .link-accent { color: #c9a961; font-weight: 600; }
    .link-accent:hover { text-decoration: underline; }
    .dot-sep { margin: 0 6px; color: var(--text-muted); }
    .hero-pay-tags { display: flex; gap: 6px; flex-wrap: wrap; }
    .tag {
        display: inline-flex; align-items: center;
        padding: 4px 10px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 5px;
        font-size: 11px; font-weight: 600;
        color: #22c55e;
    }
    .hero-pay-right { text-align: right; position: relative; z-index: 1; }
    .hero-pay-amount-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-bottom: 4px;
    }
    .hero-pay-amount {
        font-size: 32px; font-weight: 800;
        color: #22c55e; letter-spacing: -0.03em;
    }

    /* â•â•â• GRID â•â•â• */
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }

    /* â•â•â• INFO â•â•â• */
    .info-list { display: flex; flex-direction: column; }
    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        font-size: 13px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); font-weight: 600; }
    .info-value { color: var(--text-primary); text-align: right; font-weight: 500; }

    /* â•â•â• LINKED â•â•â• */
    .linked-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 10px;
        margin-bottom: 8px;
        transition: all 0.15s;
    }
    .linked-item:last-child { margin-bottom: 0; }
    .linked-item:hover {
        background: rgba(169, 120, 74, 0.06);
        transform: translateX(3px);
    }
    .linked-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid; place-items: center;
        font-size: 16px; flex-shrink: 0;
    }
    .linked-info { flex: 1; min-width: 0; }
    .linked-title {
        font-size: 13px; font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 2px;
    }
    .linked-sub {
        font-size: 11px; color: var(--text-muted);
        font-family: ui-monospace;
    }
    .linked-arrow {
        color: var(--text-muted);
        font-size: 16px;
    }

    /* â•â•â• AMOUNT BOX â•â•â• */
    .amount-big {
        font-size: 32px; font-weight: 800;
        color: #22c55e; letter-spacing: -0.03em;
        text-align: center;
        margin: 8px 0;
    }
    .amount-label {
        text-align: center;
        font-size: 11px; color: var(--text-muted);
    }

    @media (max-width: 1100px) {
        .detail-grid { grid-template-columns: 1fr; }
        .hero-pay { flex-direction: column; align-items: flex-start; }
        .hero-pay-right { width: 100%; text-align: left; }
    }
</style>
@endpush