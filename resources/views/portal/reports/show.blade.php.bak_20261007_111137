@extends('portal.layouts.app')

@section('title', $report->report_number)

@section('content')

@php
    $totalPaid = (float) ($report->amount_paid ?? 0);
    $balance = (float) ($report->balance ?? 0);
    $status = $report->status ?? 'pending';
@endphp

{{-- HEADER --}}
<div class="pp-head">
    <div class="pp-head-left">
        <div class="pp-title">{{ $report->report_number }}</div>
        <div class="pp-sub">
            {{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} – {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}
        </div>
    </div>
</div>

{{-- HERO AMOUNT --}}
<div class="detail-hero">
    <div class="detail-hero-glow"></div>

    <div class="detail-hero-label">Total Sales</div>
    <div class="detail-hero-value">&#8369;{{ number_format($report->total_sales, 2) }}</div>

    <div class="detail-hero-badges">
        <span class="pp-badge {{ $status }}">{{ $status }}</span>
        <span class="pp-badge partial">{{ $report->total_quantity ?? 0 }} items sold</span>
    </div>
</div>

{{-- PAYMENT BREAKDOWN --}}
<div class="pp-section">
    <div class="pp-section-head">
        <div class="pp-section-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div>
            <div class="pp-section-title">Payment Status</div>
            <div class="pp-section-sub">Breakdown of amounts</div>
        </div>
    </div>

    <div class="pp-info-list">
        <div class="pp-info-row">
            <div class="pp-info-label">Total Sales</div>
            <div class="pp-info-value gold">&#8369;{{ number_format($report->total_sales, 2) }}</div>
        </div>
        <div class="pp-info-row">
            <div class="pp-info-label">Amount Paid</div>
            <div class="pp-info-value green">- &#8369;{{ number_format($totalPaid, 2) }}</div>
        </div>
        <div class="pp-info-row">
            <div class="pp-info-label" style="font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.08em; font-size: 11px;">Balance</div>
            <div class="pp-info-value {{ $balance > 0 ? 'gold' : 'green' }}" style="font-size: 16px;">
                &#8369;{{ number_format($balance, 2) }}
            </div>
        </div>
    </div>
</div>

{{-- PRODUCTS SOLD --}}
@if($report->items && $report->items->count() > 0)
    <div class="pp-section">
        <div class="pp-section-head">
            <div class="pp-section-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="pp-section-title">Products Sold</div>
                <div class="pp-section-sub">{{ $report->items->count() }} product(s)</div>
            </div>
        </div>

        <div class="items-list">
            @foreach($report->items as $item)
                <div class="product-item">
                    <div class="product-thumb">
                        @if($item->product && $item->product->image_url)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                        @else
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        @endif
                    </div>
                    <div class="product-info">
                        <div class="product-name">{{ $item->product->name ?? '-' }}</div>
                        <div class="product-meta">{{ $item->quantity_sold ?? 0 }} × &#8369;{{ number_format($item->unit_price ?? 0, 2) }}</div>
                    </div>
                    <div class="product-total">
                        &#8369;{{ number_format($item->subtotal ?? (($item->quantity_sold ?? 0) * ($item->unit_price ?? 0)), 2) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- INFO --}}
<div class="pp-section">
    <div class="pp-section-head">
        <div class="pp-section-icon blue">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4M12 8h.01"/>
            </svg>
        </div>
        <div>
            <div class="pp-section-title">Report Info</div>
        </div>
    </div>

    <div class="pp-info-list">
        <div class="pp-info-row">
            <div class="pp-info-label">Report #</div>
            <div class="pp-info-value" style="font-family: ui-monospace, monospace;">{{ $report->report_number }}</div>
        </div>
        <div class="pp-info-row">
            <div class="pp-info-label">Period From</div>
            <div class="pp-info-value">{{ \Carbon\Carbon::parse($report->period_from)->format('M d, Y') }}</div>
        </div>
        <div class="pp-info-row">
            <div class="pp-info-label">Period To</div>
            <div class="pp-info-value">{{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}</div>
        </div>
        <div class="pp-info-row">
            <div class="pp-info-label">Created</div>
            <div class="pp-info-value">{{ $report->created_at->format('M d, Y') }}</div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ===== DETAIL HERO ===== */
    .detail-hero {
        position: relative;
        padding: 26px 22px;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.22) 0%, rgba(34, 34, 44, 0.55) 100%);
        backdrop-filter: blur(28px) saturate(1.6);
        -webkit-backdrop-filter: blur(28px) saturate(1.6);
        border: 1px solid rgba(169, 120, 74, 0.32);
        border-radius: 22px;
        margin-bottom: 16px;
        overflow: hidden;
        text-align: center;
        animation: ppSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
    }
    .detail-hero-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.28), transparent 65%);
        pointer-events: none;
    }
    .detail-hero-label {
        font-size: 10.5px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.14em;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }
    .detail-hero-value {
        font-size: 40px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: -0.04em;
        line-height: 1;
        margin-bottom: 16px;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 2px 24px rgba(201, 169, 97, 0.35);
        position: relative;
        z-index: 1;
    }
    .detail-hero-badges {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    /* ===== PRODUCTS LIST ===== */
    .items-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .product-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 12px;
        background: rgba(20, 20, 26, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all 0.15s;
    }
    .product-thumb {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid;
        place-items: center;
        color: rgba(201, 169, 97, 0.6);
        flex-shrink: 0;
        overflow: hidden;
    }
    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-info { flex: 1; min-width: 0; }
    .product-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .product-meta {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .product-total {
        font-size: 13.5px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }

    @media (max-width: 480px) {
        .detail-hero { padding: 22px 18px; }
        .detail-hero-value { font-size: 34px; }
    }
</style>
@endpush