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
                Financial Overview
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
            <div class="fin-metric-bar-fill" style="width:{{ $totalRevenue > 0 ? min(100, ($totalCost / $totalRevenue) * 100) : 0 }}%; background:#f87171;"></div>
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
            <div class="fin-metric-bar-fill" style="width:{{ max(0, min(100, $profitMargin)) }}%; background:#34d399;"></div>
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

{{-- ========== REVENUE TREND ========== --}}
@if($dailyTrend->count() > 0)
@php
    /* CHART-NICE-MAX-FIX — compute nice rounded max for scaling */
    $chartMax = $maxDailyRevenue;
    if ($chartMax > 0) {
        $mag  = pow(10, floor(log10($chartMax)));
        $norm = $chartMax / $mag;
        if ($norm <= 1)        $nice = 1;
        elseif ($norm <= 1.5)  $nice = 1.5;
        elseif ($norm <= 2)    $nice = 2;
        elseif ($norm <= 2.5)  $nice = 2.5;
        elseif ($norm <= 3)    $nice = 3;
        elseif ($norm <= 4)    $nice = 4;
        elseif ($norm <= 5)    $nice = 5;
        elseif ($norm <= 7.5)  $nice = 7.5;
        else                   $nice = 10;
        $chartMax = $nice * $mag;
    } else {
        $chartMax = 1000;
    }
@endphp
<section class="fin-section">
    <div class="fin-section-head">
        <div>
            <div class="fin-section-eyebrow">Daily Trend</div>
            <h2 class="fin-section-title">Revenue Overview</h2>
        </div>
        <div class="fin-section-meta">
            Total <strong>₱{{ number_format($totalRevenue, 0) }}</strong>
        </div>
    </div>

    <div class="fin-chart">
        <div class="fin-chart-y">
            <div class="fin-chart-y-label">₱{{ number_format($chartMax, 0) }}</div>
            <div class="fin-chart-y-label">₱{{ number_format($chartMax / 2, 0) }}</div>
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
                        /* Bars proportional to revenue — hard cap at 90% */
                        $rawH = $chartMax > 0 ? ($day['revenue'] / $chartMax) * 100 : 0;
                        $rawP = $chartMax > 0 ? ($day['paid']    / $chartMax) * 100 : 0;

                        $heightPct     = min(90, max(0, $rawH));
                        $paidHeightPct = min(90, max(0, $rawP));
                    @endphp
                    <div class="fin-bar-group"
                         title="{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }} — Revenue ₱{{ number_format($day['revenue'], 2) }} · Paid ₱{{ number_format($day['paid'], 2) }}">
                        <div class="fin-bar-stack">
                            <div class="fin-bar fin-bar-revenue" style="height: {{ $heightPct }}%;"></div>
                            <div class="fin-bar fin-bar-paid"    style="height: {{ $paidHeightPct }}%;"></div>
                        </div>
                        <div class="fin-bar-label">{{ \Carbon\Carbon::parse($day['date'])->format('d') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="fin-legend">
        <div class="fin-legend-item">
            <span class="fin-legend-dot fin-dot-revenue"></span>
            Revenue
        </div>
        <div class="fin-legend-item">
            <span class="fin-legend-dot fin-dot-paid"></span>
            Payments Received
        </div>
    </div>
</section>
@endif

{{-- ========== MONEY POSITION ========== --}}
<section class="fin-section">
    <div class="fin-section-head">
        <div>
            <div class="fin-section-eyebrow">Balance Sheet</div>
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

{{-- ========== PAYMENTS BY METHOD + TOP PRODUCTS (paired) ========== --}}
<div class="fin-2col">

    {{-- Payments by Method --}}
    @if($paymentsByMethod->count() > 0)
    <section class="fin-section">
        <div class="fin-section-head">
            <div>
                <div class="fin-section-eyebrow">Collections</div>
                <h2 class="fin-section-title">Payments by Method</h2>
            </div>
        </div>

        <div class="fin-method-chart">
            @foreach($paymentsByMethod as $m)
                @php
                    $pct      = $maxMethodTotal > 0 ? ($m['total'] / $maxMethodTotal) * 100 : 0;
                    $sharePct = $paymentsByMethod->sum('total') > 0 ? ($m['total'] / $paymentsByMethod->sum('total')) * 100 : 0;
                    $color    = match($m['method']) {
                        'cash'          => '#34d399',
                        'gcash'         => '#60a5fa',
                        'maya'          => '#c084fc',
                        'bank_transfer' => '#fbbf24',
                        default         => '#d4b36a',
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

    {{-- Top Products --}}
    @if($topProducts->count() > 0)
    <section class="fin-section">
        <div class="fin-section-head">
            <div>
                <div class="fin-section-eyebrow">Performance</div>
                <h2 class="fin-section-title">Top Products</h2>
            </div>
            <div class="fin-section-meta">{{ $topProducts->count() }} item(s)</div>
        </div>

        <div class="fin-product-chart">
            @foreach($topProducts as $i => $p)
                @php
                    $pct         = $maxProductRevenue > 0 ? ($p['revenue'] / $maxProductRevenue) * 100 : 0;
                    $marginColor = $p['margin'] >= 50 ? '#34d399' : ($p['margin'] >= 25 ? '#fbbf24' : '#f87171');
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
                        <div class="fin-product-margin" style="color:{{ $marginColor }};">{{ number_format($p['margin'], 0) }}% margin</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

</div>

{{-- ========== TOP STORES (full-width) ========== --}}
@if($topStores->count() > 0)
<section class="fin-section">
    <div class="fin-section-head">
        <div>
            <div class="fin-section-eyebrow">Performance</div>
            <h2 class="fin-section-title">Top Stores</h2>
        </div>
        <div class="fin-section-meta">{{ $topStores->count() }} store(s)</div>
    </div>

    <div class="fin-store-list">
        @foreach($topStores as $i => $s)
            @php
                $revPct  = $maxStoreRevenue > 0 ? ($s['revenue'] / $maxStoreRevenue) * 100 : 0;
                $paidPct = $s['revenue'] > 0 ? min(100, ($s['paid'] / $s['revenue']) * 100) : 0;

                $paidTone = $paidPct >= 80 ? 'is-ok'
                          : ($paidPct >= 40 ? 'is-warn'
                          : ($paidPct > 0   ? 'is-low' : 'is-zero'));
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
                        <div class="fin-store-bar-rev"  style="width:{{ $revPct }}%;"></div>
                        <div class="fin-store-bar-paid" style="width:{{ $paidPct }}%;"></div>
                    </div>
                    <div class="fin-store-bar-legend">
                        <span><i class="fin-mini-dot rev"></i>Revenue</span>
                        <span><i class="fin-mini-dot paid"></i>Paid</span>
                    </div>
                </div>

                <div class="fin-store-meta">
                    <div class="fin-store-amount">₱{{ number_format($s['revenue'], 0) }}</div>
                    <div class="fin-store-paid {{ $paidTone }}">{{ number_format($paidPct, 0) }}% paid</div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection

@push('styles')
<style>
/* ============================================================
   FINANCE DASHBOARD — TRANSPARENT / FROSTED
   ============================================================ */
:root {
    --fin-bg-1:      #1a1612;
    --fin-bg-2:      #14110e;

    --fin-panel:     #1e1a16;
    --fin-panel-2:   #181512;

    --fin-glass:        rgba(24, 20, 16, .62);
    --fin-glass-2:      rgba(18, 15, 12, .58);
    --fin-glass-soft:   rgba(24, 20, 16, .45);
    --fin-blur:         18px;

    --fin-border:    rgba(255,255,255,.09);
    --fin-border-2:  rgba(255,255,255,.06);
    --fin-border-hi: rgba(212,179,106,.28);

    --fin-ink:       #f5f2ec;
    --fin-ink-2:     #b8b2aa;
    --fin-ink-3:     #857e76;
    --fin-ink-4:     #5f5952;

    --fin-gold:      #d4b36a;
    --fin-gold-2:    #e6c88a;
    --fin-gold-dim:  rgba(212,179,106,.14);

    --fin-ok:        #34d399;
    --fin-info:      #60a5fa;
    --fin-warn:      #fbbf24;
    --fin-bad:       #f87171;
    --fin-purple:    #c084fc;

    --fin-r-sm:  8px;
    --fin-r:     12px;
    --fin-r-lg:  16px;
    --fin-r-pill: 999px;
}

/* ── Base ─────────────────────────────────────────────── */
.fin-hero, .fin-metric, .fin-section, .fin-money-card {
    color: var(--fin-ink);
    font-variant-numeric: tabular-nums;
    -webkit-font-smoothing: antialiased;
}
.fin-hero *, .fin-metric *, .fin-section *, .fin-money-card * { box-sizing: border-box; }

/* ── Hero ─────────────────────────────────────────────── */
.fin-hero {
    position: relative;
    padding: 22px 24px;
    margin-bottom: 18px;
    background:
        radial-gradient(120% 140% at 100% 0%, rgba(212,179,106,.10), transparent 55%),
        linear-gradient(180deg, var(--fin-glass), var(--fin-glass-2));
    backdrop-filter: blur(var(--fin-blur)) saturate(140%);
    -webkit-backdrop-filter: blur(var(--fin-blur)) saturate(140%);
    border: 1px solid var(--fin-border);
    border-radius: var(--fin-r-lg);
    overflow: hidden;
    box-shadow: 0 22px 48px -32px rgba(0,0,0,.9), inset 0 1px 0 rgba(255,255,255,.05);
}
.fin-hero::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, var(--fin-gold-2), var(--fin-gold), transparent);
}
.fin-hero-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    flex-wrap: wrap;
}
.fin-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px;
    background: var(--fin-gold-dim);
    border: 1px solid var(--fin-border-hi);
    border-radius: var(--fin-r-pill);
    font-size: 10px;
    font-weight: 700;
    color: var(--fin-gold);
    letter-spacing: .14em;
    text-transform: uppercase;
    margin-bottom: 12px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.fin-live-dot {
    width: 6px; height: 6px;
    background: var(--fin-ok);
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(52,211,153,.18);
    animation: finPulse 2.2s ease-in-out infinite;
}
@keyframes finPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .5; transform: scale(.9); }
}
.fin-hero-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--fin-ink);
    letter-spacing: -.028em;
    line-height: 1.1;
    margin: 0 0 8px;
    text-shadow: 0 1px 2px rgba(0,0,0,.4);
}
.fin-hero-subtitle {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 12.5px;
    color: var(--fin-ink-2);
    margin: 0;
}
.fin-date-chip {
    padding: 3px 9px;
    background: rgba(255,255,255,.06);
    border: 1px solid var(--fin-border);
    border-radius: 6px;
    font-family: ui-monospace, "SF Mono", monospace;
    font-size: 11px;
    font-weight: 600;
    color: var(--fin-ink);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
}
.fin-date-arrow { color: var(--fin-ink-3); }
.fin-date-sep   { color: var(--fin-ink-4); }
.fin-date-range {
    font-weight: 600;
    color: var(--fin-gold);
    font-size: 11.5px;
    letter-spacing: .02em;
}

.fin-hero-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: flex-end;
}
.fin-presets {
    display: flex;
    gap: 2px;
    padding: 3px;
    background: rgba(0,0,0,.4);
    border: 1px solid var(--fin-border);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.fin-preset {
    padding: 6px 12px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 600;
    color: var(--fin-ink-2);
    text-decoration: none;
    transition: background .15s, color .15s;
    white-space: nowrap;
}
.fin-preset:hover { background: rgba(255,255,255,.07); color: var(--fin-ink); }
.fin-preset.active {
    background: linear-gradient(180deg, var(--fin-gold-2), var(--fin-gold));
    color: #1a1612;
    box-shadow: 0 4px 12px -4px rgba(212,179,106,.5);
}
.fin-custom-range { display: flex; align-items: center; gap: 6px; }
.fin-date-input {
    padding: 7px 11px;
    background: rgba(0,0,0,.4);
    border: 1px solid var(--fin-border);
    border-radius: 8px;
    color: var(--fin-ink);
    font-size: 11.5px;
    font-family: ui-monospace, "SF Mono", monospace;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.fin-date-input:focus {
    border-color: rgba(212,179,106,.6);
    box-shadow: 0 0 0 3px rgba(212,179,106,.14);
}
.fin-date-input-sep { color: var(--fin-ink-3); font-size: 11px; }
.fin-apply-btn {
    padding: 7px 14px;
    background: linear-gradient(180deg, var(--fin-gold-2), var(--fin-gold));
    color: #1a1612;
    border: none;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
}
.fin-apply-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px -6px rgba(212,179,106,.55);
}

/* ── Metrics ──────────────────────────────────────────── */
.fin-metrics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}
.fin-metric {
    position: relative;
    padding: 16px 18px;
    background: linear-gradient(180deg, var(--fin-glass), var(--fin-glass-2));
    backdrop-filter: blur(var(--fin-blur)) saturate(140%);
    -webkit-backdrop-filter: blur(var(--fin-blur)) saturate(140%);
    border: 1px solid var(--fin-border);
    border-radius: var(--fin-r);
    transition: transform .2s, border-color .2s, box-shadow .2s, background .2s;
    overflow: hidden;
    box-shadow: 0 18px 40px -30px rgba(0,0,0,.85), inset 0 1px 0 rgba(255,255,255,.04);
}
.fin-metric::before {
    content: '';
    position: absolute; top: 0; left: 18px; right: 18px; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
    opacity: 0;
    transition: opacity .25s;
}
.fin-metric:hover {
    transform: translateY(-2px);
    border-color: var(--fin-border-hi);
    box-shadow: 0 22px 48px -28px rgba(0,0,0,.9), inset 0 1px 0 rgba(255,255,255,.06);
}
.fin-metric:hover::before { opacity: 1; }
.fin-metric-primary {
    background:
        radial-gradient(120% 120% at 0% 0%, rgba(212,179,106,.12), transparent 60%),
        linear-gradient(180deg, var(--fin-glass), var(--fin-glass-2));
    border-color: var(--fin-border-hi);
}
.fin-metric-primary::after {
    content: '';
    position: absolute; left: 0; top: 18px; bottom: 18px; width: 2px;
    background: linear-gradient(180deg, var(--fin-gold-2), var(--fin-gold));
    border-radius: 2px;
}
.fin-metric-head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}
.fin-metric-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}
.fin-icon-revenue  { background: rgba(212,179,106,.16); color: var(--fin-gold); }
.fin-icon-cost     { background: rgba(248,113,113,.15); color: var(--fin-bad); }
.fin-icon-profit   { background: rgba(52,211,153,.15);  color: var(--fin-ok);  }
.fin-icon-loss     { background: rgba(248,113,113,.18); color: var(--fin-bad); }
.fin-icon-payments { background: rgba(96,165,250,.15);  color: var(--fin-info);}
.fin-metric-label {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--fin-ink-3);
    text-transform: uppercase;
    letter-spacing: .1em;
    flex: 1;
}
.fin-metric-change {
    display: inline-flex;
    align-items: center;
    padding: 3px 7px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    font-family: ui-monospace, "SF Mono", monospace;
}
.fin-metric-change.up   { background: rgba(52,211,153,.15);  color: var(--fin-ok); }
.fin-metric-change.down { background: rgba(248,113,113,.15); color: var(--fin-bad); }
.fin-metric-value {
    font-size: 22px;
    font-weight: 700;
    color: var(--fin-ink);
    letter-spacing: -.03em;
    line-height: 1.05;
    margin-bottom: 6px;
    text-shadow: 0 1px 2px rgba(0,0,0,.35);
}
.fin-metric-value.positive { color: var(--fin-ok); }
.fin-metric-value.negative { color: var(--fin-bad); }
.fin-metric-foot {
    font-size: 11.5px;
    color: var(--fin-ink-3);
    display: flex;
    align-items: center;
    gap: 6px;
}
.fin-metric-bar {
    margin-top: 12px;
    height: 3px;
    background: rgba(255,255,255,.08);
    border-radius: 2px;
    overflow: hidden;
}
.fin-metric-bar-fill {
    height: 100%;
    border-radius: 2px;
    transition: width .6s cubic-bezier(.2,.7,.2,1);
}
.fin-margin-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 700;
    font-family: ui-monospace, "SF Mono", monospace;
    letter-spacing: .01em;
}
.fin-margin-badge.high { background: rgba(52,211,153,.15); color: var(--fin-ok); }
.fin-margin-badge.mid  { background: rgba(251,191,36,.15); color: var(--fin-warn); }
.fin-margin-badge.low  { background: rgba(248,113,113,.15);color: var(--fin-bad); }

/* ── Section shell ────────────────────────────────────── */
.fin-section {
    padding: 18px 20px;
    margin-bottom: 18px;
    background: linear-gradient(180deg, var(--fin-glass), var(--fin-glass-2));
    backdrop-filter: blur(var(--fin-blur)) saturate(140%);
    -webkit-backdrop-filter: blur(var(--fin-blur)) saturate(140%);
    border: 1px solid var(--fin-border);
    border-radius: var(--fin-r-lg);
    box-shadow: 0 22px 48px -32px rgba(0,0,0,.9), inset 0 1px 0 rgba(255,255,255,.04);
}
.fin-section-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--fin-border-2);
}
.fin-section-eyebrow {
    font-size: 10px;
    font-weight: 700;
    color: var(--fin-gold);
    letter-spacing: .14em;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.fin-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--fin-ink);
    letter-spacing: -.018em;
    margin: 0;
    text-shadow: 0 1px 2px rgba(0,0,0,.35);
}
.fin-section-meta {
    font-size: 11.5px;
    color: var(--fin-ink-3);
}
.fin-section-meta strong { color: var(--fin-gold); font-weight: 700; }

/* ── Trend chart ──────────────────────────────────────── */
.fin-chart {
    display: flex;
    gap: 14px;
    height: 210px;
    padding: 6px 0 24px;
}
.fin-chart-y {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 2px 0;
}
.fin-chart-y-label {
    font-size: 10px;
    color: var(--fin-ink-3);
    font-family: ui-monospace, "SF Mono", monospace;
    font-weight: 600;
    text-align: right;
    min-width: 56px;
    letter-spacing: -.01em;
}
.fin-chart-bars-wrap { flex: 1; position: relative; min-width: 0; }
.fin-chart-grid {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; justify-content: space-between;
    pointer-events: none;
}
.fin-grid-line {
    height: 1px;
    background: linear-gradient(90deg, rgba(255,255,255,.08), rgba(255,255,255,.02) 80%, transparent);
}
.fin-chart-bars {
    position: relative;
    height: 100%;
    display: flex;
    align-items: flex-end;
    gap: 4px;
    padding: 0 4px;
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
    max-width: 34px;
    height: 100%;
    display: flex;
    align-items: flex-end;
    gap: 3px;
}
.fin-bar {
    flex: 1;
    border-radius: 3px 3px 0 0;
    transition: filter .2s;
    min-height: 2px;
    cursor: pointer;
}
.fin-bar-revenue { background: linear-gradient(180deg, var(--fin-gold-2), var(--fin-gold)); }
.fin-bar-paid    { background: linear-gradient(180deg, #4ade80, #16a34a); }
.fin-bar-group:hover .fin-bar { filter: brightness(1.2); }
.fin-bar-label {
    position: absolute;
    bottom: -20px;
    font-size: 9.5px;
    color: var(--fin-ink-3);
    font-weight: 600;
    font-family: ui-monospace, "SF Mono", monospace;
    letter-spacing: .02em;
}

.fin-legend {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 11.5px;
    color: var(--fin-ink-2);
    padding-top: 10px;
}
.fin-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}
.fin-legend-dot {
    width: 10px; height: 10px;
    border-radius: 3px;
    flex-shrink: 0;
}
.fin-dot-revenue { background: linear-gradient(180deg, var(--fin-gold-2), var(--fin-gold)); }
.fin-dot-paid    { background: linear-gradient(180deg, #4ade80, #16a34a); }

/* ── Money position ───────────────────────────────────── */
.fin-money-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}
.fin-money-card {
    position: relative;
    padding: 16px 18px;
    background: var(--fin-glass-soft);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    border: 1px solid var(--fin-border);
    border-radius: var(--fin-r);
    overflow: hidden;
    transition: transform .2s, border-color .2s, background .2s;
    box-shadow: 0 16px 36px -28px rgba(0,0,0,.85);
}
.fin-money-card::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 2px;
}
.fin-money-cash::before       { background: linear-gradient(180deg, #4ade80, transparent); }
.fin-money-digital::before    { background: linear-gradient(180deg, var(--fin-info), transparent); }
.fin-money-receivable::before { background: linear-gradient(180deg, var(--fin-warn), transparent); }
.fin-money-collected::before  { background: linear-gradient(180deg, var(--fin-gold-2), transparent); }
.fin-money-card:hover {
    transform: translateY(-2px);
    border-color: rgba(255,255,255,.14);
    background: rgba(24, 20, 16, .58);
}
.fin-money-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: grid;
    place-items: center;
    margin-bottom: 12px;
}
.fin-money-cash       .fin-money-icon { background: rgba(52,211,153,.15);  color: var(--fin-ok);   }
.fin-money-digital    .fin-money-icon { background: rgba(96,165,250,.15);  color: var(--fin-info); }
.fin-money-receivable .fin-money-icon { background: rgba(251,191,36,.15);  color: var(--fin-warn); }
.fin-money-collected  .fin-money-icon { background: rgba(212,179,106,.15); color: var(--fin-gold); }
.fin-money-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--fin-ink-3);
    text-transform: uppercase;
    letter-spacing: .1em;
    margin-bottom: 6px;
}
.fin-money-value {
    font-size: 17px;
    font-weight: 700;
    letter-spacing: -.02em;
    line-height: 1.1;
    margin-bottom: 4px;
    text-shadow: 0 1px 2px rgba(0,0,0,.35);
}
.fin-money-cash       .fin-money-value { color: var(--fin-ok);   }
.fin-money-digital    .fin-money-value { color: var(--fin-info); }
.fin-money-receivable .fin-money-value { color: var(--fin-warn); }
.fin-money-collected  .fin-money-value { color: var(--fin-gold); }
.fin-money-sub { font-size: 11px; color: var(--fin-ink-3); }

/* ── Paired 2-col grid ───────────────────────────────── */
.fin-2col {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 18px;
    align-items: start;
}

/* ── Method chart ─────────────────────────────────────── */
.fin-method-chart {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.fin-method-row {
    display: grid;
    grid-template-columns: 140px 1fr 100px;
    align-items: center;
    gap: 14px;
}
.fin-method-info { min-width: 0; }
.fin-method-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 3px;
}
.fin-method-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.fin-method-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--fin-ink);
}
.fin-method-pct {
    margin-left: auto;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--fin-ink-3);
    font-family: ui-monospace, "SF Mono", monospace;
}
.fin-method-count {
    font-size: 10.5px;
    color: var(--fin-ink-3);
    padding-left: 16px;
}
.fin-method-bar-wrap {
    height: 26px;
    background: rgba(0,0,0,.35);
    border-radius: 7px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(255,255,255,.04);
}
.fin-method-bar {
    height: 100%;
    border-radius: 7px;
    transition: width .6s cubic-bezier(.2,.7,.2,1);
    min-width: 4px;
}
.fin-method-amount {
    font-size: 13px;
    font-weight: 700;
    color: var(--fin-gold);
    text-align: right;
    font-family: ui-monospace, "SF Mono", monospace;
    white-space: nowrap;
    letter-spacing: -.01em;
}

/* ── Product chart ────────────────────────────────────── */
.fin-product-chart {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.fin-product-row {
    display: grid;
    grid-template-columns: 24px 1fr minmax(80px, auto);
    gap: 12px;
    align-items: center;
}
.fin-product-rank {
    width: 24px; height: 24px;
    border-radius: 7px;
    background: rgba(255,255,255,.06);
    border: 1px solid var(--fin-border-2);
    color: var(--fin-ink-2);
    font-size: 11px;
    font-weight: 700;
    font-family: ui-monospace, "SF Mono", monospace;
    display: grid;
    place-items: center;
}
.fin-product-info { min-width: 0; }
.fin-product-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--fin-ink);
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.fin-product-bar-wrap {
    height: 6px;
    background: rgba(0,0,0,.35);
    border-radius: 3px;
    overflow: hidden;
}
.fin-product-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--fin-gold-2), var(--fin-gold));
    border-radius: 3px;
    transition: width .6s cubic-bezier(.2,.7,.2,1);
    min-width: 4px;
}
.fin-product-meta { text-align: right; }
.fin-product-revenue {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--fin-ink);
    font-family: ui-monospace, "SF Mono", monospace;
    letter-spacing: -.01em;
}
.fin-product-margin {
    font-size: 10px;
    font-weight: 700;
    font-family: ui-monospace, "SF Mono", monospace;
    margin-top: 2px;
    letter-spacing: .02em;
    white-space: nowrap;
}

/* ── Store rows ───────────────────────────────────────── */
.fin-store-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.fin-store-row {
    display: grid;
    grid-template-columns: 32px minmax(120px, 220px) 1fr minmax(90px, auto);
    gap: 14px;
    align-items: center;
    padding: 12px 0;
}
.fin-store-row + .fin-store-row { border-top: 1px solid var(--fin-border-2); }

.fin-store-rank {
    width: 28px; height: 28px;
    border-radius: 8px;
    background: rgba(255,255,255,.06);
    border: 1px solid var(--fin-border-2);
    color: var(--fin-ink-2);
    font-size: 11.5px;
    font-weight: 700;
    font-family: ui-monospace, "SF Mono", monospace;
    display: grid; place-items: center;
}
.fin-store-rank.gold   { background: linear-gradient(140deg, #fcd34d, #d97706); color: #1a1612; border-color: transparent; }
.fin-store-rank.silver { background: linear-gradient(140deg, #e5e7eb, #9ca3af); color: #1a1612; border-color: transparent; }
.fin-store-rank.bronze { background: linear-gradient(140deg, #d97706, #92400e); color: #fff;    border-color: transparent; }

.fin-store-info { min-width: 0; }
.fin-store-name {
    font-size: 12.5px; font-weight: 600; color: var(--fin-ink);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.fin-store-code {
    font-size: 10px; color: var(--fin-ink-3);
    font-family: ui-monospace, "SF Mono", monospace;
    margin-top: 3px; letter-spacing: .02em;
}

/* Store bar: gold = revenue, green overlay = paid */
.fin-store-chart { min-width: 0; }
.fin-store-bar-wrap {
    position: relative;
    height: 8px;
    background: rgba(0,0,0,.4);
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.04);
}
.fin-store-bar-rev {
    position: absolute; inset: 0 auto 0 0;
    height: 100%;
    background: linear-gradient(90deg, rgba(212,179,106,.9), var(--fin-gold));
    border-radius: 4px;
    transition: width .6s cubic-bezier(.2,.7,.2,1);
    min-width: 3px;
}
.fin-store-bar-paid {
    position: absolute; inset: 0 auto 0 0;
    height: 100%;
    background: linear-gradient(90deg, #4ade80, #16a34a);
    border-radius: 4px;
    transition: width .6s cubic-bezier(.2,.7,.2,1);
    min-width: 0;
    z-index: 1;
}

.fin-store-bar-legend {
    display: flex;
    gap: 14px;
    margin-top: 6px;
    font-size: 9.5px;
    color: var(--fin-ink-3);
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.fin-store-bar-legend span { display: inline-flex; align-items: center; gap: 5px; }
.fin-mini-dot {
    width: 7px; height: 7px;
    border-radius: 2px;
    display: inline-block;
}
.fin-mini-dot.rev  { background: linear-gradient(180deg, var(--fin-gold-2), var(--fin-gold)); }
.fin-mini-dot.paid { background: linear-gradient(180deg, #4ade80, #16a34a); }

.fin-store-meta { text-align: right; }
.fin-store-amount {
    font-size: 13px; font-weight: 700; color: var(--fin-ink);
    font-family: ui-monospace, "SF Mono", monospace;
    letter-spacing: -.01em;
    text-shadow: 0 1px 2px rgba(0,0,0,.35);
}
.fin-store-paid {
    font-size: 10.5px; font-weight: 700;
    font-family: ui-monospace, "SF Mono", monospace;
    margin-top: 3px;
    letter-spacing: .02em;
}
.fin-store-paid.is-ok   { color: var(--fin-ok); }
.fin-store-paid.is-warn { color: var(--fin-warn); }
.fin-store-paid.is-low  { color: var(--fin-bad); }
.fin-store-paid.is-zero { color: var(--fin-ink-3); }

/* ── Fallback for browsers without backdrop-filter ────── */
@supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
    .fin-hero,
    .fin-metric,
    .fin-section {
        background: rgba(18, 15, 12, .92);
    }
    .fin-money-card {
        background: rgba(18, 15, 12, .85);
    }
}

/* ── Responsive ───────────────────────────────────────── */
@media (max-width: 1100px) {
    .fin-metrics     { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .fin-money-grid  { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .fin-2col        { grid-template-columns: 1fr; }
    .fin-store-row   { grid-template-columns: 32px minmax(120px, 1fr) 1.4fr minmax(90px, auto); }
}
@media (max-width: 700px) {
    :root { --fin-blur: 12px; }
    .fin-hero         { padding: 18px; }
    .fin-hero-title   { font-size: 20px; }
    .fin-hero-top     { flex-direction: column; align-items: stretch; gap: 16px; }
    .fin-hero-actions { align-items: stretch; }
    .fin-presets      { justify-content: center; flex-wrap: wrap; }
    .fin-custom-range { justify-content: center; flex-wrap: wrap; }
    .fin-metrics      { grid-template-columns: 1fr; }
    .fin-money-grid   { grid-template-columns: 1fr; }
    .fin-chart        { height: 160px; }
    .fin-chart-y-label { font-size: 9px; min-width: 44px; }
    .fin-bar-label    { font-size: 8.5px; }
    .fin-method-row   { grid-template-columns: 110px 1fr 80px; gap: 10px; }
    .fin-product-row  { grid-template-columns: 24px 1fr 80px; }
    .fin-store-row {
        grid-template-columns: 28px 1fr auto;
        row-gap: 6px;
    }
    .fin-store-chart { grid-column: 2 / 4; }
    .fin-store-meta  { grid-column: 3 / 4; grid-row: 1; text-align: right; }
}
@media (prefers-reduced-motion: reduce) {
    .fin-hero *, .fin-metric *, .fin-section *, .fin-money-card * {
        animation: none !important;
        transition: none !important;
    }
}

/* ═══════════════════════════════════════════════ */

/* ═══ Chart container — reduce padding, add overflow ═══ */
.fin-chart {
    height: 240px !important;
    padding: 10px 0 28px !important;
    overflow: hidden !important;
}

/* ═══ Chart bars wrap — constrain ═══ */
.fin-chart-bars-wrap {
    overflow: hidden !important;
    position: relative !important;
}

/* ═══ Chart bars — proper height constraint ═══ */
.fin-chart-bars {
    height: 100% !important;
    overflow: hidden !important;
    align-items: flex-end !important;
}

/* ═══ Bar group — proper flex ═══ */
.fin-bar-group {
    height: 100% !important;
    overflow: hidden !important;
    justify-content: flex-end !important;
}

/* ═══ Bar stack — constrain height ═══ */
.fin-bar-stack {
    height: 100% !important;
    max-height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-end !important;
    overflow: hidden !important;
}

/* ═══ Bar — constrain ═══ */
.fin-bar {
    max-height: 100% !important;
    box-sizing: border-box !important;
}

/* ═══ Chart y-axis — align with new padding ═══ */
.fin-chart-y {
    padding: 10px 0 28px !important;
    height: 240px !important;
    box-sizing: border-box !important;
}

.fin-chart-y-label:first-child {
    margin-top: -4px !important;
}

.fin-chart-y-label:last-child {
    margin-bottom: -4px !important;
}

/* ═══════════════════════════════════════════════ */

.fin-chart {
    overflow: hidden !important;
    padding: 14px 0 32px !important;
    height: 240px !important;
    box-sizing: border-box !important;
    display: flex !important;
    gap: 14px !important;
}

.fin-chart-y {
    padding: 0 !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    flex-shrink: 0 !important;
    box-sizing: border-box !important;
}

.fin-chart-bars-wrap {
    flex: 1 !important;
    position: relative !important;
    overflow: hidden !important;
    min-width: 0 !important;
    height: 100% !important;
    box-sizing: border-box !important;
}

.fin-chart-grid {
    position: absolute !important;
    inset: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    pointer-events: none !important;
}

.fin-chart-bars {
    position: relative !important;
    height: 100% !important;
    display: flex !important;
    align-items: flex-end !important;
    gap: 4px !important;
    padding: 0 4px !important;
    box-sizing: border-box !important;
}

.fin-bar-group {
    flex: 1 !important;
    min-width: 0 !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: flex-end !important;
    position: relative !important;
    box-sizing: border-box !important;
}

.fin-bar-stack {
    position: relative !important;
    width: 100% !important;
    max-width: 34px !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: flex-end !important;
    justify-content: center !important;
    gap: 2px !important;
    box-sizing: border-box !important;
}

.fin-bar {
    flex: 1 !important;
    min-height: 2px !important;
    max-height: 100% !important;
    border-radius: 3px 3px 0 0 !important;
    box-sizing: border-box !important;
    cursor: pointer !important;
    align-self: flex-end !important;
}

.fin-bar-group:hover .fin-bar {
    filter: brightness(1.2) !important;
}

/* ═══ FINAL CHART FIX — minimal + safe ═══ */
/* Bars proportional sa revenue, walay overflow */

.fin-chart {
    overflow: hidden !important;
    position: relative !important;
}

.fin-chart-bars-wrap {
    overflow: hidden !important;
    position: relative !important;
    height: 100% !important;
}

.fin-chart-bars {
    height: 100% !important;
    overflow: hidden !important;
    align-items: flex-end !important;
}

.fin-bar-group {
    height: 100% !important;
    overflow: hidden !important;
}

/* KEY: bar-stack needs explicit height so bar % works */
.fin-bar-stack {
    height: 100% !important;
    max-height: 100% !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: flex-end !important;
    justify-content: center !important;
    gap: 2px !important;
    overflow: hidden !important;
}

/* Bar max — never exceed stack height */
.fin-bar {
    max-height: 100% !important;
    align-self: flex-end !important;
    box-sizing: border-box !important;
}
</style>
@endpush