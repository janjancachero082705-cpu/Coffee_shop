@extends('layouts.admin')

@section('title', $store->store_name . ' — Sales Reports')
@section('subtitle', 'Reports for ' . $store->store_name)

@section('content')

@php
    $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
@endphp

{{-- ===== BACK BUTTON ===== --}}
<a href="{{ route('consignment.reports.index') }}" class="ss-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    Back to All Reports
</a>

{{-- ===== STORE HERO ===== --}}
<div class="ss-hero">
    <div class="ss-hero-avatar">
        @if($store->logo_url)
            <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
        @else
            {{ $initials }}
        @endif
    </div>
    <div class="ss-hero-info">
        <div class="ss-hero-name">{{ $store->store_name }}</div>
        <div class="ss-hero-meta">
            <span class="ss-hero-code">{{ $store->code }}</span>
            <span class="ss-hero-dot">·</span>
            <span>{{ $reports->count() }} report(s)</span>
        </div>
    </div>
</div>

{{-- ===== SUMMARY STATS ===== --}}
<div class="ss-stats">
    <div class="ss-stat ss-stat-primary">
        <div class="ss-stat-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 5-5"/>
            </svg>
        </div>
        <div class="ss-stat-body">
            <div class="ss-stat-lbl">Total Sales</div>
            <div class="ss-stat-val">&#8369;{{ number_format($totalSales, 2) }}</div>
        </div>
    </div>
    <div class="ss-stat ss-stat-green">
        <div class="ss-stat-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="ss-stat-body">
            <div class="ss-stat-lbl">Collected</div>
            <div class="ss-stat-val green">&#8369;{{ number_format($totalPaid, 2) }}</div>
        </div>
    </div>
    <div class="ss-stat {{ $totalBal > 0 ? 'ss-stat-amber' : 'ss-stat-green' }}">
        <div class="ss-stat-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="ss-stat-body">
            <div class="ss-stat-lbl">Balance</div>
            <div class="ss-stat-val {{ $totalBal > 0 ? 'amber' : 'green' }}">&#8369;{{ number_format($totalBal, 2) }}</div>
        </div>
    </div>
</div>

{{-- ===== PROGRESS ===== --}}
<div class="ss-progress-card">
    <div class="ss-progress-info">
        <span class="ss-progress-lbl">Payment Progress</span>
        <span class="ss-progress-pct">{{ number_format($paidPct, 0) }}% paid</span>
    </div>
    <div class="ss-progress-track">
        <div class="ss-progress-fill" style="width: {{ $paidPct }}%; {{ $paidPct >= 100 ? 'background: linear-gradient(90deg, #22c55e, #16a34a);' : '' }}"></div>
    </div>
</div>

{{-- ===== REPORTS LIST ===== --}}
<div class="ss-list-card">
    <div class="ss-list-head">
        <div class="ss-list-title">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
            </svg>
            Report History
        </div>
        <div class="ss-list-count">{{ $reports->count() }} total</div>
    </div>

    @forelse($reports as $report)
        @php
            $total   = (float) ($report->total_sales ?? 0);
            $paid    = (float) ($report->amount_paid ?? 0);
            $balance = (float) ($report->balance ?? 0);
            $isPaid    = $balance <= 0;
            $isPartial = $paid > 0 && $balance > 0;
            $status    = $isPaid ? 'paid' : ($isPartial ? 'partial' : 'pending');
        @endphp

        <a href="{{ route('consignment.reports.show', $report) }}" class="ss-report">
            <div class="ss-report-badge ss-badge-{{ $status }}">
                <span class="ss-badge-dot"></span>
                {{ ucfirst($status) }}
            </div>

            <div class="ss-report-info">
                <div class="ss-report-num">{{ $report->report_number }}</div>
                <div class="ss-report-date">
                    {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y · g:i A') }}
                </div>
            </div>

            <div class="ss-report-metrics">
                <div class="ss-metric">
                    <div class="ss-metric-lbl">Total</div>
                    <div class="ss-metric-val">&#8369;{{ number_format($total, 2) }}</div>
                </div>
                <div class="ss-metric">
                    <div class="ss-metric-lbl">Paid</div>
                    <div class="ss-metric-val green">&#8369;{{ number_format($paid, 2) }}</div>
                </div>
                <div class="ss-metric">
                    <div class="ss-metric-lbl">Balance</div>
                    <div class="ss-metric-val {{ $balance > 0 ? 'amber' : 'green' }}">&#8369;{{ number_format($balance, 2) }}</div>
                </div>
            </div>

            <svg class="ss-report-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </a>
    @empty
        <div class="ss-empty">Walay reports pa.</div>
    @endforelse
</div>

@endsection

@push('styles')
<style>
    /* BACK */
    .ss-back {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; margin-bottom: 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: #d4d4d8; text-decoration: none;
        font-size: 12px; font-weight: 700;
        transition: all 0.15s;
    }
    .ss-back:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(201, 169, 97, 0.3);
        color: #c9a961;
    }

    /* HERO */
    .ss-hero {
        display: flex; align-items: center; gap: 16px;
        padding: 22px 24px;
        background: linear-gradient(135deg, rgba(30, 26, 22, 0.85), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid #c9a961;
        border-radius: 16px;
        margin-bottom: 16px;
    }
    .ss-hero-avatar {
        width: 60px; height: 60px; border-radius: 16px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.25), rgba(201, 169, 97, 0.08));
        color: #c9a961;
        display: grid; place-items: center;
        font-weight: 800; font-size: 20px;
        letter-spacing: 0.05em;
        border: 1px solid rgba(201, 169, 97, 0.3);
        flex-shrink: 0;
        overflow: hidden;
    }
    .ss-hero-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ss-hero-info { flex: 1; min-width: 0; }
    .ss-hero-name {
        font-size: 22px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.02em;
        margin-bottom: 6px;
    }
    .ss-hero-meta {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: #71717a;
    }
    .ss-hero-code {
        font-family: ui-monospace, monospace;
        color: #a1a1aa;
    }
    .ss-hero-dot { color: #52525b; }

    /* STATS */
    .ss-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 14px;
    }
    .ss-stat {
        display: flex; align-items: center; gap: 12px;
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        position: relative;
        overflow: hidden;
        transition: all 0.2s;
    }
    .ss-stat::before {
        content: ''; position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
    }
    .ss-stat-primary::before { background: linear-gradient(90deg, #c9a961, transparent); }
    .ss-stat-green::before   { background: linear-gradient(90deg, #22c55e, transparent); }
    .ss-stat-amber::before   { background: linear-gradient(90deg, #f59e0b, transparent); }
    .ss-stat:hover { transform: translateY(-2px); border-color: rgba(201, 169, 97, 0.2); }

    .ss-stat-icon {
        width: 38px; height: 38px; border-radius: 11px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .ss-stat-primary .ss-stat-icon { background: rgba(201, 169, 97, 0.15); color: #c9a961; }
    .ss-stat-green   .ss-stat-icon { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .ss-stat-amber   .ss-stat-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

    .ss-stat-body { flex: 1; min-width: 0; }
    .ss-stat-lbl {
        font-size: 10px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .ss-stat-val {
        font-size: 20px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
    }
    .ss-stat-val.green { color: #22c55e; }
    .ss-stat-val.amber { color: #f59e0b; }

    /* PROGRESS */
    .ss-progress-card {
        padding: 14px 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        margin-bottom: 16px;
    }
    .ss-progress-info {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 8px;
    }
    .ss-progress-lbl {
        font-size: 10.5px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
    }
    .ss-progress-pct {
        font-size: 12px; font-weight: 800; color: #c9a961;
    }
    .ss-progress-track {
        height: 8px; border-radius: 4px;
        background: rgba(255, 255, 255, 0.05);
        overflow: hidden;
    }
    .ss-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #c9a961, #d4b673);
        border-radius: 4px;
        transition: width 0.4s;
        min-width: 4px;
    }

    /* LIST */
    .ss-list-card {
        padding: 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
    }
    .ss-list-head {
        display: flex; justify-content: space-between; align-items: center;
        padding-bottom: 14px; margin-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .ss-list-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 800;
        color: #fafafa; text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .ss-list-title svg { color: #c9a961; }
    .ss-list-count {
        font-size: 11px; font-weight: 700;
        color: #71717a;
    }

    .ss-report {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        margin-bottom: 8px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .ss-report:last-child { margin-bottom: 0; }
    .ss-report:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(201, 169, 97, 0.3);
        transform: translateX(3px);
    }

    .ss-report-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 11px; border-radius: 100px;
        font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.05em;
        flex-shrink: 0;
    }
    .ss-badge-paid    { background: rgba(34, 197, 94, 0.12);  color: #22c55e; }
    .ss-badge-partial { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .ss-badge-pending { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .ss-badge-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: currentColor;
    }

    .ss-report-info { flex: 1; min-width: 0; }
    .ss-report-num {
        font-size: 13px; font-weight: 800; color: #fafafa;
        font-family: ui-monospace, monospace;
        margin-bottom: 3px;
    }
    .ss-report-date { font-size: 11px; color: #71717a; }

    .ss-report-metrics {
        display: flex; gap: 20px; flex-shrink: 0;
    }
    .ss-metric { text-align: right; }
    .ss-metric-lbl {
        font-size: 9px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 3px;
    }
    .ss-metric-val {
        font-size: 12.5px; font-weight: 800;
        color: #fafafa; font-variant-numeric: tabular-nums;
    }
    .ss-metric-val.green { color: #22c55e; }
    .ss-metric-val.amber { color: #f59e0b; }

    .ss-report-arrow {
        color: #52525b; flex-shrink: 0;
        transition: all 0.15s;
    }
    .ss-report:hover .ss-report-arrow {
        color: #c9a961;
        transform: translateX(3px);
    }

    .ss-empty {
        text-align: center; padding: 40px 20px;
        color: #71717a; font-size: 13px;
    }

    @media (max-width: 800px) {
        .ss-stats { grid-template-columns: 1fr; }
        .ss-report-metrics { display: none; }
        .ss-hero { padding: 18px; }
        .ss-hero-name { font-size: 18px; }
        .ss-hero-avatar { width: 48px; height: 48px; font-size: 16px; }
    }
</style>
@endpush