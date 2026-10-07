@extends('portal.layouts.app')

@section('title', 'My Sales Reports')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $total = \App\Models\SalesReport::where('store_id', $store->id)->count();
    $totalSales = (float) \App\Models\SalesReport::where('store_id', $store->id)->sum('total_sales');
    $totalPaid = (float) \App\Models\SalesReport::where('store_id', $store->id)->sum('amount_paid');
    $totalBalance = (float) \App\Models\SalesReport::where('store_id', $store->id)->sum('balance');
    
    $thisMonth = (float) \App\Models\SalesReport::where('store_id', $store->id)
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->sum('total_sales');
@endphp

{{-- HEADER --}}
<div class="pp-head">
    <div class="pp-head-left">
        <div class="pp-title">Sales Reports</div>
        <div class="pp-sub">Your sales performance</div>
    </div>
</div>

{{-- HERO SUMMARY --}}
<div class="sales-hero">
    <div class="sales-hero-glow"></div>
    <div class="sales-hero-label">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
        </svg>
        Total Sales
    </div>
    <div class="sales-hero-value">&#8369;{{ number_format($totalSales, 2) }}</div>
    <div class="sales-hero-meta">
        <div class="sales-hero-meta-item">
            <span class="meta-dot green"></span>
            &#8369;{{ number_format($totalPaid, 0) }} paid
        </div>
        <div class="sales-hero-meta-item">
            <span class="meta-dot amber"></span>
            &#8369;{{ number_format($totalBalance, 0) }} balance
        </div>
    </div>
</div>

{{-- STATS --}}
<div class="pp-stats">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $total }}</div>
        <div class="pp-stat-label">Reports</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($thisMonth/1000, 1) }}k</div>
        <div class="pp-stat-label">This Month</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($totalPaid/1000, 1) }}k</div>
        <div class="pp-stat-label">Paid</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon red">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($totalBalance/1000, 1) }}k</div>
        <div class="pp-stat-label">Balance</div>
    </div>
</div>

{{-- REPORTS LIST --}}
@if($reports->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
            </svg>
        </div>
        <div class="pp-empty-title">No reports yet</div>
        <div class="pp-empty-text">
            Wala pa'y sales reports nga na-record para sa imong store.
        </div>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($reports as $report)
            <a href="{{ route('portal.reports.show', $report->id) }}" class="pp-card">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $report->report_number }}</div>
                        <div class="pp-card-sub">
                            {{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} – {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}
                        </div>
                    </div>
                    <span class="pp-badge {{ $report->status ?? 'pending' }}">{{ $report->status ?? 'pending' }}</span>
                </div>

                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount">&#8369;{{ number_format($report->total_sales, 2) }}</div>
                        <div class="pp-card-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            {{ $report->total_quantity ?? 0 }} items sold
                        </div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color: var(--text-muted);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($reports, 'links') && $reports->hasPages())
        <div style="margin-top: 20px;">{{ $reports->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* ===== SALES HERO ===== */
    .sales-hero {
        position: relative;
        padding: 24px 22px;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.22) 0%, rgba(34, 34, 44, 0.55) 100%);
        backdrop-filter: blur(28px) saturate(1.6);
        -webkit-backdrop-filter: blur(28px) saturate(1.6);
        border: 1px solid rgba(169, 120, 74, 0.32);
        border-radius: 22px;
        margin-bottom: 16px;
        overflow: hidden;
        animation: ppSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
    }
    .sales-hero-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.28), transparent 65%);
        pointer-events: none;
        animation: glowPulse 6s ease-in-out infinite;
    }
    .sales-hero-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 10.5px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.14em;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }
    .sales-hero-label svg { color: #c9a961; }
    .sales-hero-value {
        font-size: 38px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.04em;
        line-height: 1;
        margin-bottom: 14px;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 2px 24px rgba(201, 169, 97, 0.25);
        position: relative;
        z-index: 1;
    }
    .sales-hero-meta {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }
    .sales-hero-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .meta-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
    }
    .meta-dot.green {
        background: #22c55e;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.7);
    }
    .meta-dot.amber {
        background: #f59e0b;
        box-shadow: 0 0 8px rgba(245, 158, 11, 0.7);
    }

    @keyframes glowPulse {
        0%, 100% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.08); opacity: 1; }
    }

    @media (max-width: 480px) {
        .sales-hero { padding: 20px 18px; }
        .sales-hero-value { font-size: 32px; }
    }
</style>
@endpush