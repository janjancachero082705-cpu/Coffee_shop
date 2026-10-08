@extends('layouts.admin')

@section('title', 'Finance')
@section('subtitle', 'Revenue, profitability & money position')

@section('content')

{{-- ========== HERO HEADER ========== --}}
<div class="fin-hero">
    <div class="fin-hero-top">
        <div class="fin-hero-title-group">
            <div class="fin-hero-eyebrow">
                <span class="fin-live-dot"></span>
                FINANCIAL OVERVIEW
            </div>
            <h1 class="fin-hero-title">Financial Dashboard</h1>
            <p class="fin-hero-subtitle">
                <span class="fin-date-chip">{{ $dateFrom->format('M d') }}</span>
                <span class="fin-date-arrow">→</span>
                <span class="fin-date-chip">{{ $dateTo->format('M d, Y') }}</span>
                <span class="fin-date-sep">·</span>
                <span class="fin-date-range">{{ $dateFrom->diffInDays($dateTo) + 1 }} days</span>
            </p>
        </div>

        <div class="fin-hero-actions">
            <div class="fin-presets">
                @foreach(['today' => 'Today', '7d' => '7D', '30d' => '30D', '90d' => '90D', 'month' => 'Month', 'year' => 'Year'] as $key => $label)
                    <a href="{{ route('finance.dashboard', ['range' => $key]) }}"
                       class="fin-preset {{ $range === $key && !request('from') ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" class="fin-custom-range">
                <input type="date" name="from" value="{{ $dateFrom->format('Y-m-d') }}" class="fin-date-input">
                <span class="fin-date-input-sep">—</span>
                <input type="date" name="to" value="{{ $dateTo->format('Y-m-d') }}" class="fin-date-input">
                <button type="submit" class="fin-apply-btn">Apply</button>
            </form>
        </div>
    </div>
</div>

{{-- ========== PRIMARY METRICS ========== --}}
<div class="fin-metrics">
    <div class="fin-metric fin-metric-primary">
        <div class="fin-metric-head">
            <div class="fin-metric-icon fin-icon-revenue">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div class="fin-metric-label">Revenue</div>
            @if(isset($revenueChange) && $revenueChange != 0)
                <div class="fin-metric-change {{ $revenueChange > 0 ? 'up' : 'down' }}">
                    {{ $revenueChange > 0 ? '+' : '' }}{{ number_format($revenueChange, 1) }}%
                </div>
            @endif
        </div>
        <div class="fin-metric-value">₱{{ number_format($totalRevenue, 2) }}</div>
        <div class="fin-metric-foot">Gross sales</div>
    </div>

    <div class="fin-metric">
        <div class="fin-metric-head">
            <div class="fin-metric-icon fin-icon-cost">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="fin-metric-label">COGS</div>
        </div>
        <div class="fin-metric-value">₱{{ number_format($totalCost, 2) }}</div>
        <div class="fin-metric-foot">{{ $totalRevenue > 0 ? number_format(($totalCost / $totalRevenue) * 100, 1) : 0 }}% of revenue</div>
        <div class="fin-metric-bar">
            <div class="fin-metric-bar-fill" style="width:{{ $totalRevenue > 0 ? min(100, ($totalCost / $totalRevenue) * 100) : 0 }}%; background:#ef4444;"></div>
        </div>
    </div>

    <div class="fin-metric">
        <div class="fin-metric-head">
            <div class="fin-metric-icon {{ $grossProfit >= 0 ? 'fin-icon-profit' : 'fin-icon-loss' }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <div class="fin-metric-label">Profit</div>
        </div>
        <div class="fin-metric-value {{ $grossProfit >= 0 ? 'positive' : 'negative' }}">₱{{ number_format($grossProfit, 2) }}</div>
        <div class="fin-metric-foot">
            <span class="fin-margin-badge {{ $profitMargin >= 40 ? 'high' : ($profitMargin >= 20 ? 'mid' : 'low') }}">
                {{ number_format($profitMargin, 1) }}% margin
            </span>
        </div>
        <div class="fin-metric-bar">
            <div class="fin-metric-bar-fill" style="width:{{ max(0, min(100, $profitMargin)) }}%; background:#22c55e;"></div>
        </div>
    </div>

    <div class="fin-metric">
        <div class="fin-metric-head">
            <div class="fin-metric-icon fin-icon-payments">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
            </div>
            <div class="fin-metric-label">Payments</div>
            @if(isset($paymentsChange) && $paymentsChange != 0)
                <div class="fin-metric-change {{ $paymentsChange > 0 ? 'up' : 'down' }}">
                    {{ $paymentsChange > 0 ? '+' : '' }}{{ number_format($paymentsChange, 1) }}%
                </div>
            @endif
        </div>
        <div class="fin-metric-value">₱{{ number_format($paymentsReceived, 2) }}</div>
        <div class="fin-metric-foot">{{ $paymentsByMethod->sum('count') }} transaction(s)</div>
    </div>
</div>

{{-- ========== REVENUE TREND BAR CHART ========== --}}
@if($dailyTrend->count() > 0)
<section class="fin-section">
    <div class="fin-section-head">
        <div>
            <div class="fin-section-eyebrow">DAILY TREND</div>
            <h2 class="fin-section-title">Revenue Overview</h2>
        </div>
        <div class="fin-section-meta">
            Total: <strong>₱{{ number_format($totalRevenue, 0) }}</strong>
        </div>
    </div>

    <div class="fin-chart">
        <div class="fin-chart-y">
            <div class="fin-chart-y-label">₱{{ number_format($maxDailyRevenue, 0) }}</div>
            <div class="fin-chart-y-label">₱{{ number_format($maxDailyRevenue / 2, 0) }}</div>
            <div class="fin-chart-y-label">₱0</div>
        </div>

        <div class="fin-chart-bars-wrap">
            <div class="fin-chart-grid">
                <div class="fin-grid-line"></div>
                <div class="fin-grid-line"></div>
                <div class="fin-grid-line"></div>
            </div>
            <div class="fin-chart-bars">
                @foreach($dailyTrend as $day)
                    @php
                        $heightPct = $maxDailyRevenue > 0 ? ($day['revenue'] / $maxDailyRevenue) * 100 : 0;
                        $paidHeightPct = $maxDailyRevenue > 0 ? ($day['paid'] / $maxDailyRevenue) * 100 : 0;
                    @endphp
                    <div class="fin-bar-group" title="{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }} — Revenue ₱{{ number_format($day['revenue'], 2) }}">
                        <div class="fin-bar-stack">
                            <div class="fin-bar fin-bar-revenue" style="height: {{ $heightPct }}%;"></div>
                            <div class="fin-bar fin-bar-paid" style="height: {{ $paidHeightPct }}%;"></div>
                        </div>
                        <div class="fin-bar-label">{{ \Carbon\Carbon::parse($day['date'])->format('d') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="fin-legend">
        <div class="fin-legend-item">
            <span class="fin-legend-dot" style="background:linear-gradient(180deg, #c9a961, #b8944d);"></span>
            Revenue
        </div>
        <div class="fin-legend-item">
            <span class="fin-legend-dot" style="background:linear-gradient(180deg, #22c55e, #16a34a);"></span>
            Payments Received
        </div>
    </div>
</section>
@endif

{{-- ========== MONEY POSITION ========== --}}
<section class="fin-section">
    <div class="fin-section-head">
        <div>
            <div class="fin-section-eyebrow">BALANCE SHEET</div>
            <h2 class="fin-section-title">Money Position</h2>
        </div>
    </div>

    <div class="fin-money-grid">
        <div class="fin-money-card fin-money-cash">
            <div class="fin-money-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
            </div>
            <div class="fin-money-label">Cash on Hand</div>
            <div class="fin-money-value">₱{{ number_format($cashOnHand, 2) }}</div>
            <div class="fin-money-sub">Physical cash received</div>
        </div>

        <div class="fin-money-card fin-money-digital">
            <div class="fin-money-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>
            </div>
            <div class="fin-money-label">Digital Received</div>
            <div class="fin-money-value">₱{{ number_format($onlineReceived, 2) }}</div>
            <div class="fin-money-sub">GCash · Maya · Bank</div>
        </div>

        <div class="fin-money-card fin-money-receivable">
            <div class="fin-money-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="fin-money-label">Receivables</div>
            <div class="fin-money-value">₱{{ number_format($totalReceivables, 2) }}</div>
            <div class="fin-money-sub">Utang sa stores (all-time)</div>
        </div>

        <div class="fin-money-card fin-money-collected">
            <div class="fin-money-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            </div>
            <div class="fin-money-label">Total Collected</div>
            <div class="fin-money-value">₱{{ number_format($totalPaidAllTime, 2) }}</div>
            <div class="fin-money-sub">All-time payments</div>
        </div>
    </div>
</section>

{{-- ========== TWO-COLUMN GRID: Payments + Products/Stores ========== --}}
<div class="fin-2col">

    {{-- LEFT: Payments by Method (Bar Chart) --}}
    @if($paymentsByMethod->count() > 0)
    <section class="fin-section">
        <div class="fin-section-head">
            <div>
                <div class="fin-section-eyebrow">COLLECTIONS</div>
                <h2 class="fin-section-title">Payments by Method</h2>
            </div>
        </div>

        <div class="fin-method-chart">
            @foreach($paymentsByMethod as $m)
                @php
                    $pct = $maxMethodTotal > 0 ? ($m['total'] / $maxMethodTotal) * 100 : 0;
                    $sharePct = $paymentsByMethod->sum('total') > 0 ? ($m['total'] / $paymentsByMethod->sum('total')) * 100 : 0;
                    $color = match($m['method']) {
                        'cash' => '#22c55e',
                        'gcash' => '#3b82f6',
                        'maya' => '#a855f7',
                        'bank_transfer' => '#f59e0b',
                        default => '#c9a961',
                    };
                @endphp
                <div class="fin-method-row">
                    <div class="fin-method-info">
                        <div class="fin-method-name-row">
                            <span class="fin-method-dot" style="background:{{ $color }};"></span>
                            <span class="fin-method-name">{{ $m['label'] }}</span>
                            <span class="fin-method-pct">{{ number_format($sharePct, 0) }}%</span>
                        </div>
                        <div class="fin-method-count">{{ $m['count'] }} payment(s)</div>
                    </div>
                    <div class="fin-method-bar-wrap">
                        <div class="fin-method-bar" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                    </div>
                    <div class="fin-method-amount">₱{{ number_format($m['total'], 0) }}</div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- RIGHT: Top Products (Horizontal Bar Chart) --}}
    @if($topProducts->count() > 0)
    <section class="fin-section">
        <div class="fin-section-head">
            <div>
                <div class="fin-section-eyebrow">PERFORMANCE</div>
                <h2 class="fin-section-title">Top Products</h2>
            </div>
            <div class="fin-section-meta">{{ $topProducts->count() }} item(s)</div>
        </div>

        <div class="fin-product-chart">
            @foreach($topProducts as $i => $p)
                @php
                    $pct = $maxProductRevenue > 0 ? ($p['revenue'] / $maxProductRevenue) * 100 : 0;
                    $marginColor = $p['margin'] >= 50 ? '#22c55e' : ($p['margin'] >= 25 ? '#f59e0b' : '#ef4444');
                @endphp
                <div class="fin-product-row">
                    <div class="fin-product-rank">{{ $i + 1 }}</div>
                    <div class="fin-product-info">
                        <div class="fin-product-name">{{ $p['name'] }}</div>
                        <div class="fin-product-bar-wrap">
                            <div class="fin-product-bar" style="width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                    <div class="fin-product-meta">
                        <div class="fin-product-revenue">₱{{ number_format($p['revenue'], 0) }}</div>
                        <div class="fin-product-margin" style="color:{{ $marginColor }};">{{ number_format($p['margin'], 0) }}%</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

</div>

{{-- ========== TOP STORES (Full Width with Bars) ========== --}}
@if($topStores->count() > 0)
<section class="fin-section">
    <div class="fin-section-head">
        <div>
            <div class="fin-section-eyebrow">PERFORMANCE</div>
            <h2 class="fin-section-title">Top Stores</h2>
        </div>
        <div class="fin-section-meta">{{ $topStores->count() }} store(s)</div>
    </div>

    <div class="fin-store-list">
        @foreach($topStores as $i => $s)
            @php
                $revPct = $maxStoreRevenue > 0 ? ($s['revenue'] / $maxStoreRevenue) * 100 : 0;
                $paidPct = $s['revenue'] > 0 ? min(100, ($s['paid'] / $s['revenue']) * 100) : 0;
            @endphp
            <div class="fin-store-row">
                <div class="fin-store-rank {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : '')) }}">
                    {{ $i + 1 }}
                </div>
                <div class="fin-store-info">
                    <div class="fin-store-name">{{ $s['name'] }}</div>
                    @if($s['code'])
                        <div class="fin-store-code">{{ $s['code'] }}</div>
                    @endif
                </div>
                <div class="fin-store-chart">
                    <div class="fin-store-bar-wrap">
                        <div class="fin-store-bar-rev" style="width:{{ $revPct }}%;"></div>
                        <div class="fin-store-bar-paid" style="width:{{ $paidPct }}%;"></div>
                    </div>
                </div>
                <div class="fin-store-meta">
                    <div class="fin-store-amount">₱{{ number_format($s['revenue'], 0) }}</div>
                    <div class="fin-store-paid">{{ number_format($paidPct, 0) }}% paid</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="fin-legend" style="margin-top:14px;">
        <div class="fin-legend-item">
            <span class="fin-legend-dot" style="background:linear-gradient(90deg, #c9a961, #b8944d);"></span>
            Revenue
        </div>
        <div class="fin-legend-item">
            <span class="fin-legend-dot" style="background:linear-gradient(90deg, #22c55e, #16a34a);"></span>
            Paid
        </div>
    </div>
</section>
@endif

@endsection

@push('styles')
<style>
    /* ============================================================
       FINANCE DASHBOARD — REDESIGN WITH BAR GRAPHS
       ============================================================ */

    /* ========== HERO ========== */
    .fin-hero {
        position: relative;
        padding: 20px 24px;
        margin-bottom: 16px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.08) 0%, rgba(30, 26, 22, 0.4) 100%);
        border: 1px solid rgba(201, 169, 97, 0.15);
        border-radius: 18px;
        overflow: hidden;
    }
    .fin-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 240px; height: 240px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.12), transparent 70%);
        pointer-events: none;
    }
    .fin-hero-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }
    .fin-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        background: rgba(201, 169, 97, 0.12);
        border: 1px solid rgba(201, 169, 97, 0.25);
        border-radius: 100px;
        font-size: 9px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: 0.15em;
        margin-bottom: 10px;
    }
    .fin-live-dot {
        width: 5px; height: 5px;
        background: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        animation: finPulse 2s ease-in-out infinite;
    }
    @keyframes finPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .fin-hero-title {
        font-size: 22px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.03em;
        line-height: 1.1;
        margin-bottom: 6px;
    }
    .fin-hero-subtitle {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        font-size: 11.5px;
        color: #a1a1aa;
    }
    .fin-date-chip {
        padding: 2px 8px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 5px;
        font-family: ui-monospace, monospace;
        font-size: 10.5px;
        font-weight: 600;
        color: #d4d4d8;
    }
    .fin-date-arrow { color: #71717a; }
    .fin-date-sep { color: #52525b; }
    .fin-date-range { font-weight: 700; color: #c9a961; font-size: 10.5px; }

    .fin-hero-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
    }
    .fin-presets {
        display: flex;
        gap: 3px;
        padding: 3px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 9px;
    }
    .fin-preset {
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 700;
        color: #a1a1aa;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .fin-preset:hover { background: rgba(255, 255, 255, 0.06); color: #fafafa; }
    .fin-preset.active {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }
    .fin-custom-range {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .fin-date-input {
        padding: 6px 10px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 7px;
        color: #fafafa;
        font-size: 11px;
        font-family: ui-monospace, monospace;
        outline: none;
        transition: all 0.15s;
    }
    .fin-date-input:focus {
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
    }
    .fin-date-input-sep { color: #71717a; font-size: 10.5px; }
    .fin-apply-btn {
        padding: 6px 13px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        border: none;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.15s;
    }
    .fin-apply-btn:hover {
        background: linear-gradient(135deg, #d4b672, #c9a961);
        transform: translateY(-1px);
    }

    /* ========== METRICS ========== */
    .fin-metrics {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .fin-metric {
        padding: 14px 16px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        transition: all 0.2s;
    }
    .fin-metric:hover {
        transform: translateY(-2px);
        border-color: rgba(201, 169, 97, 0.25);
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.5);
    }
    .fin-metric-primary {
        background: linear-gradient(165deg, rgba(201, 169, 97, 0.1), rgba(21, 18, 15, 0.9));
        border-color: rgba(201, 169, 97, 0.2);
    }
    .fin-metric-head {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .fin-metric-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .fin-icon-revenue { background: rgba(201, 169, 97, 0.15); color: #c9a961; }
    .fin-icon-cost { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .fin-icon-profit { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .fin-icon-loss { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .fin-icon-payments { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .fin-metric-label {
        font-size: 10px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        flex: 1;
    }
    .fin-metric-change {
        display: inline-flex;
        align-items: center;
        padding: 2px 6px;
        border-radius: 5px;
        font-size: 9.5px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
    }
    .fin-metric-change.up { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .fin-metric-change.down { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .fin-metric-value {
        font-size: 20px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.03em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        margin-bottom: 6px;
    }
    .fin-metric-value.positive { color: #22c55e; }
    .fin-metric-value.negative { color: #ef4444; }
    .fin-metric-foot {
        font-size: 10.5px;
        color: #71717a;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .fin-metric-bar {
        margin-top: 10px;
        height: 3px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 2px;
        overflow: hidden;
    }
    .fin-metric-bar-fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.5s ease;
    }

    /* ========== SECTION ========== */
    .fin-section {
        padding: 16px 18px;
        margin-bottom: 16px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.85), rgba(21, 18, 15, 0.85));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
    }
    .fin-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .fin-section-eyebrow {
        font-size: 9px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: 0.15em;
        margin-bottom: 3px;
    }
    .fin-section-title {
        font-size: 15px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
    }
    .fin-section-meta {
        font-size: 10.5px;
        color: #71717a;
    }
    .fin-section-meta strong { color: #c9a961; }

    /* ========== BAR CHART (Revenue Trend) ========== */
    .fin-chart {
        display: flex;
        gap: 12px;
        height: 200px;
        padding: 8px 0;
    }
    .fin-chart-y {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2px 0;
        flex-shrink: 0;
    }
    .fin-chart-y-label {
        font-size: 9.5px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        font-weight: 600;
        text-align: right;
        min-width: 50px;
    }
    .fin-chart-bars-wrap {
        flex: 1;
        position: relative;
    }
    .fin-chart-grid {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        pointer-events: none;
    }
    .fin-grid-line {
        height: 1px;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.05), transparent);
    }
    .fin-chart-bars {
        position: relative;
        height: 100%;
        display: flex;
        align-items: flex-end;
        gap: 3px;
        padding: 0 2px;
    }
    .fin-bar-group {
        flex: 1;
        min-width: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        position: relative;
    }
    .fin-bar-stack {
        width: 100%;
        max-width: 32px;
        height: 100%;
        display: flex;
        align-items: flex-end;
        gap: 2px;
    }
    .fin-bar {
        flex: 1;
        border-radius: 3px 3px 0 0;
        transition: all 0.3s ease;
        min-height: 2px;
        cursor: pointer;
    }
    .fin-bar-revenue {
        background: linear-gradient(180deg, #c9a961, #b8944d);
    }
    .fin-bar-paid {
        background: linear-gradient(180deg, #22c55e, #16a34a);
    }
    .fin-bar:hover {
        filter: brightness(1.2);
        transform: scaleY(1.02);
    }
    .fin-bar-label {
        position: absolute;
        bottom: -18px;
        font-size: 9px;
        color: #71717a;
        font-weight: 600;
        font-family: ui-monospace, monospace;
    }

    .fin-legend {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        font-size: 10.5px;
        color: #a1a1aa;
        padding-top: 6px;
    }
    .fin-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    .fin-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    /* ========== MONEY GRID ========== */
    .fin-money-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }
    .fin-money-card {
        position: relative;
        padding: 14px;
        background: rgba(20, 20, 26, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .fin-money-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
    }
    .fin-money-cash::before { background: linear-gradient(90deg, #22c55e, transparent); }
    .fin-money-digital::before { background: linear-gradient(90deg, #3b82f6, transparent); }
    .fin-money-receivable::before { background: linear-gradient(90deg, #f59e0b, transparent); }
    .fin-money-collected::before { background: linear-gradient(90deg, #c9a961, transparent); }
    .fin-money-card:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.12);
    }
    .fin-money-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: grid;
        place-items: center;
        margin-bottom: 10px;
    }
    .fin-money-cash .fin-money-icon { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .fin-money-digital .fin-money-icon { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .fin-money-receivable .fin-money-icon { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .fin-money-collected .fin-money-icon { background: rgba(201, 169, 97, 0.12); color: #c9a961; }
    .fin-money-label {
        font-size: 9.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 5px;
    }
    .fin-money-value {
        font-size: 16px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
        line-height: 1.1;
        margin-bottom: 3px;
    }
    .fin-money-cash .fin-money-value { color: #22c55e; }
    .fin-money-digital .fin-money-value { color: #3b82f6; }
    .fin-money-receivable .fin-money-value { color: #f59e0b; }
    .fin-money-collected .fin-money-value { color: #c9a961; }
    .fin-money-sub { font-size: 10px; color: #71717a; }

    /* ========== 2-COLUMN GRID ========== */
    .fin-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    /* ========== METHOD CHART (Payments) ========== */
    .fin-method-chart {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .fin-method-row {
        display: grid;
        grid-template-columns: 130px 1fr 90px;
        align-items: center;
        gap: 12px;
    }
    .fin-method-info { min-width: 0; }
    .fin-method-name-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }
    .fin-method-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .fin-method-name {
        font-size: 11.5px;
        font-weight: 700;
        color: #fafafa;
    }
    .fin-method-pct {
        margin-left: auto;
        font-size: 10px;
        font-weight: 800;
        color: #71717a;
        font-family: ui-monospace, monospace;
    }
    .fin-method-count {
        font-size: 9.5px;
        color: #71717a;
        padding-left: 14px;
    }
    .fin-method-bar-wrap {
        height: 22px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 6px;
        overflow: hidden;
        position: relative;
    }
    .fin-method-bar {
        height: 100%;
        border-radius: 6px;
        transition: width 0.5s ease;
        min-width: 4px;
    }
    .fin-method-amount {
        font-size: 12.5px;
        font-weight: 800;
        color: #c9a961;
        text-align: right;
        font-family: ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    /* ========== PRODUCT CHART ========== */
    .fin-product-chart {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .fin-product-row {
        display: grid;
        grid-template-columns: 24px 1fr 80px;
        gap: 10px;
        align-items: center;
    }
    .fin-product-rank {
        width: 22px; height: 22px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.04);
        color: #a1a1aa;
        font-size: 10.5px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        display: grid;
        place-items: center;
    }
    .fin-product-info { min-width: 0; }
    .fin-product-name {
        font-size: 11.5px;
        font-weight: 700;
        color: #fafafa;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fin-product-bar-wrap {
        height: 6px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 3px;
        overflow: hidden;
    }
    .fin-product-bar {
        height: 100%;
        background: linear-gradient(90deg, #c9a961, #b8944d);
        border-radius: 3px;
        transition: width 0.5s ease;
        min-width: 4px;
    }
    .fin-product-meta { text-align: right; }
    .fin-product-revenue {
        font-size: 12px;
        font-weight: 800;
        color: #fafafa;
        font-family: ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
    }
    .fin-product-margin {
        font-size: 10px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        margin-top: 1px;
    }

    /* ========== STORE CHART ========== */
    .fin-store-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .fin-store-row {
        display: grid;
        grid-template-columns: 32px 180px 1fr 110px;
        gap: 14px;
        align-items: center;
        padding: 8px 0;
    }
    .fin-store-rank {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.04);
        color: #a1a1aa;
        font-size: 11.5px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        display: grid;
        place-items: center;
    }
    .fin-store-rank.gold { background: linear-gradient(135deg, #fbbf24, #d97706); color: #fff; }
    .fin-store-rank.silver { background: linear-gradient(135deg, #d4d4d8, #a1a1aa); color: #0f0f14; }
    .fin-store-rank.bronze { background: linear-gradient(135deg, #d97706, #92400e); color: #fff; }
    .fin-store-info { min-width: 0; }
    .fin-store-name {
        font-size: 12px;
        font-weight: 800;
        color: #fafafa;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fin-store-code {
        font-size: 9.5px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        margin-top: 2px;
    }
    .fin-store-chart { min-width: 0; }
    .fin-store-bar-wrap {
        height: 8px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }
    .fin-store-bar-rev {
        height: 100%;
        background: linear-gradient(90deg, #c9a961, #b8944d);
        border-radius: 4px;
        position: absolute;
        top: 0; left: 0;
        transition: width 0.5s ease;
        min-width: 2px;
    }
    .fin-store-bar-paid {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border-radius: 4px;
        position: absolute;
        top: 0; left: 0;
        transition: width 0.5s ease;
        min-width: 2px;
    }
    .fin-store-meta { text-align: right; }
    .fin-store-amount {
        font-size: 12.5px;
        font-weight: 800;
        color: #fafafa;
        font-family: ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
    }
    .fin-store-paid {
        font-size: 9.5px;
        color: #22c55e;
        font-weight: 700;
        margin-top: 1px;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1100px) {
        .fin-metrics { grid-template-columns: repeat(2, 1fr); }
        .fin-money-grid { grid-template-columns: repeat(2, 1fr); }
        .fin-2col { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .fin-hero { padding: 16px 18px; }
        .fin-hero-top { flex-direction: column; align-items: stretch; }
        .fin-hero-actions { align-items: stretch; }
        .fin-presets { justify-content: center; flex-wrap: wrap; }
        .fin-custom-range { justify-content: center; flex-wrap: wrap; }
        .fin-metrics { grid-template-columns: 1fr; }
        .fin-money-grid { grid-template-columns: 1fr; }
        .fin-hero-title { font-size: 18px; }
        .fin-chart { height: 150px; }
        .fin-chart-y-label { font-size: 8px; min-width: 40px; }
        .fin-bar-label { font-size: 8px; }
        .fin-method-row { grid-template-columns: 100px 1fr 70px; gap: 8px; }
        .fin-product-row { grid-template-columns: 22px 1fr 70px; }
        .fin-store-row {
            grid-template-columns: 28px 1fr 90px;
            gap: 10px;
        }
        .fin-store-chart { grid-column: 2 / 3; margin-top: 4px; }
    }
</style>
@endpush