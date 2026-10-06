@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', now()->format('l, F j, Y'))

@section('content')

@php
    // Stats
    $activeStores    = \App\Models\Store::where('status','active')->count();
    $totalStores     = \App\Models\Store::count();
    $productCount    = \App\Models\Product::count();
    $drToday         = \App\Models\DeliveryReceipt::whereDate('delivery_date', today())->count();
    $drTotal         = \App\Models\DeliveryReceipt::count();

    $totalSales      = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->sum('total_sales');
    $totalPaid       = (float) \App\Models\ConsignmentPayment::sum('amount');
    $outstanding     = (float) \App\Models\SalesReport::sum('balance');
    $paymentsToday   = (float) \App\Models\ConsignmentPayment::whereDate('payment_date', today())->sum('amount');

    $monthSales      = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->where('created_at','>=',now()->startOfMonth())->sum('total_sales');
    $lastMonthSales  = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->whereBetween('created_at',[now()->subMonth()->startOfMonth(),now()->subMonth()->endOfMonth()])->sum('total_sales');
    $salesTrend      = $lastMonthSales > 0 ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) : 0;
    $lowStock        = \App\Models\Product::where('is_active',true)->whereColumn('stock','<=','reorder_level')->count();
@endphp

{{-- ==================== GREETING ==================== --}}
<div class="greeting">
    <div>
        <h2 class="greeting-title">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
            <span class="greeting-name">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</span>
        </h2>
        <p class="greeting-sub">Here's what's happening with your business today.</p>
    </div>
    <div class="greeting-badge">
        <span class="pulse"></span>
        Live
    </div>
</div>

{{-- ==================== HERO KPI ==================== --}}
<div class="hero-dash">
    <div class="hero-dash-bg"></div>
    <div class="hero-dash-bg2"></div>

    <div class="hero-dash-left">
        <div class="hero-dash-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            Total Unpaid Balance
        </div>
        <div class="hero-dash-value">&#8369;{{ number_format($outstanding, 2) }}</div>
        <div class="hero-dash-meta">
            <div class="hero-meta-item">
                <span class="hero-meta-dot green"></span>
                &#8369;{{ number_format($totalPaid, 0) }} collected
            </div>
            <div class="hero-meta-divider"></div>
            <div class="hero-meta-item">
                <span class="hero-meta-dot"></span>
                {{ $activeStores }} active stores
            </div>
        </div>
    </div>

    <div class="hero-dash-right">
        <div class="hero-trend {{ $salesTrend >= 0 ? 'up' : 'down' }}">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                @if($salesTrend >= 0)
                    <path d="M23 6l-9.5 9.5-5-5L1 18"/>
                    <path d="M17 6h6v6"/>
                @else
                    <path d="M23 18l-9.5-9.5-5 5L1 6"/>
                    <path d="M17 18h6v-6"/>
                @endif
            </svg>
        </div>
        <div class="hero-trend-info">
            <div class="hero-trend-value">&#8369;{{ number_format($monthSales, 0) }}</div>
            <div class="hero-trend-label">
                This month
                <span class="trend-pct {{ $salesTrend >= 0 ? 'up' : 'down' }}">
                    {{ $salesTrend >= 0 ? '+' : '' }}{{ $salesTrend }}%
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ==================== KPI CARDS ==================== --}}
<div class="kpi-row">

    <div class="kpi-card">
        <div class="kpi-card-icon gold">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
        </div>
        <div class="kpi-card-body">
            <div class="kpi-card-value">{{ $totalStores }}</div>
            <div class="kpi-card-label">Stores</div>
        </div>
        <div class="kpi-card-tag green">{{ $activeStores }} active</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-card-icon blue">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div class="kpi-card-body">
            <div class="kpi-card-value">{{ $productCount }}</div>
            <div class="kpi-card-label">Products</div>
        </div>
        @if($lowStock > 0)
            <div class="kpi-card-tag amber">{{ $lowStock }} low</div>
        @else
            <div class="kpi-card-tag green">In stock</div>
        @endif
    </div>

    <div class="kpi-card">
        <div class="kpi-card-icon purple">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="kpi-card-body">
            <div class="kpi-card-value">{{ $drTotal }}</div>
            <div class="kpi-card-label">Deliveries</div>
        </div>
        @if($drToday > 0)
            <div class="kpi-card-tag blue">{{ $drToday }} today</div>
        @else
            <div class="kpi-card-tag">{{ $drToday }} today</div>
        @endif
    </div>

    <div class="kpi-card">
        <div class="kpi-card-icon green">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
        </div>
        <div class="kpi-card-body">
            <div class="kpi-card-value">&#8369;{{ number_format($totalPaid/1000, 1) }}k</div>
            <div class="kpi-card-label">Collected</div>
        </div>
        @if($paymentsToday > 0)
            <div class="kpi-card-tag green">+&#8369;{{ number_format($paymentsToday, 0) }}</div>
        @else
            <div class="kpi-card-tag">All-time</div>
        @endif
    </div>

</div>

{{-- ==================== CHART ==================== --}}
<div class="chart-card">
    <div class="chart-card-head">
        <div>
            <div class="chart-card-title">Sales & Payments</div>
            <div class="chart-card-sub">Last 6 months performance</div>
        </div>
        <div class="chart-legend">
            <div class="legend-item">
                <span class="legend-dot gold"></span> Sales
            </div>
            <div class="legend-item">
                <span class="legend-dot green"></span> Payments
            </div>
        </div>
    </div>
    <div class="chart-wrap">
        <canvas id="trendChart"></canvas>
    </div>
</div>

{{-- ==================== BOTTOM GRID ==================== --}}
<div class="bottom-grid">

    {{-- Top Stores --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
            <div class="card-head-title">Top Stores</div>
            <a href="{{ route('stores.index') }}" class="card-head-link" title="View all stores">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </a>
        </div>

        @php
            $topStores = \App\Models\Store::withSum('deliveryReceipts as total_delivered', 'total_amount')
                ->orderByDesc('total_delivered')->take(5)->get();
            $maxVal = $topStores->max('total_delivered') ?: 1;
        @endphp

        @if($topStores->isEmpty() || $topStores->first()->total_delivered === null)
            <div class="empty-box small">
                <div class="empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M3 3v18h18M7 16l4-4 4 4 6-6"/>
                    </svg>
                </div>
                <div class="empty-text">No data yet</div>
            </div>
        @else
            <div class="stores-list">
                @foreach($topStores as $i => $store)
                    @php $pct = $maxVal > 0 ? ($store->total_delivered / $maxVal) * 100 : 0; @endphp
                    <div class="store-row">
                        <div class="store-rank {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : '')) }}">
                            {{ $i + 1 }}
                        </div>
                        <div class="store-info">
                            <div class="store-name">{{ $store->store_name }}</div>
                            <div class="store-bar">
                                <div class="store-fill" style="width: {{ $pct }}%;"></div>
                            </div>
                        </div>
                        <div class="store-amount">&#8369;{{ number_format($store->total_delivered ?? 0, 0) }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Activity --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="card-head-title">Activity</div>
        </div>

        @php
            $activity = collect();
            foreach (\App\Models\DeliveryReceipt::with('store')->latest()->take(4)->get() as $dr) {
                $activity->push([
                    'type' => 'delivery',
                    'title' => $dr->dr_number,
                    'sub' => $dr->store->store_name ?? '-',
                    'amount' => $dr->total_amount,
                    'date' => $dr->created_at
                ]);
            }
            foreach (\App\Models\ConsignmentPayment::with('store')->latest()->take(4)->get() as $p) {
                $activity->push([
                    'type' => 'payment',
                    'title' => $p->payment_number,
                    'sub' => $p->store->store_name ?? '-',
                    'amount' => $p->amount,
                    'date' => $p->created_at
                ]);
            }
            $activity = $activity->sortByDesc('date')->take(6);
        @endphp

        @if($activity->isEmpty())
            <div class="empty-box small">
                <div class="empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                    </svg>
                </div>
                <div class="empty-text">No activity yet</div>
            </div>
        @else
            <div class="txn-list">
                @foreach($activity as $a)
                    <div class="txn-row">
                        <div class="txn-icon {{ $a['type'] === 'delivery' ? 'blue' : 'green' }}">
                            @if($a['type'] === 'delivery')
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="1" y="3" width="15" height="13"/>
                                    <path d="M16 8h4l3 3v5h-7V8z"/>
                                    <circle cx="5.5" cy="18.5" r="2.5"/>
                                    <circle cx="18.5" cy="18.5" r="2.5"/>
                                </svg>
                            @else
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                                    <path d="M2 10h20"/>
                                </svg>
                            @endif
                        </div>
                        <div class="txn-info">
                            <div class="txn-title">{{ $a['title'] }}</div>
                            <div class="txn-sub">{{ $a['sub'] }} - {{ $a['date']->diffForHumans() }}</div>
                        </div>
                        <div class="txn-amount {{ $a['type'] === 'payment' ? 'green' : '' }}">
                            {{ $a['type'] === 'payment' ? '+' : '' }}&#8369;{{ number_format($a['amount'], 0) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection

@push('styles')
<style>
    /* ================================================================
       GREETING
       ================================================================ */
    .greeting {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        gap: 16px;
        flex-wrap: wrap;
    }
    .greeting-title {
        font-size: 26px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.25;
    }
    .greeting-name {
        background: linear-gradient(135deg, #c9a961, #a9784a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .greeting-sub {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 6px;
    }
    .greeting-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: #22c55e;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        backdrop-filter: blur(10px);
    }
    .pulse {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
        animation: pulseGreen 2s infinite;
    }
    @keyframes pulseGreen {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        50% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
    }

    /* ================================================================
       HERO DASHBOARD
       ================================================================ */
    .hero-dash {
        position: relative;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.12) 0%, rgba(34, 34, 44, 0.35) 100%);
        backdrop-filter: blur(28px) saturate(1.6);
        -webkit-backdrop-filter: blur(28px) saturate(1.6);
        border: 1px solid rgba(169, 120, 74, 0.22);
        border-radius: 22px;
        padding: 34px 38px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        overflow: hidden;
        flex-wrap: wrap;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hero-dash:hover {
        border-color: rgba(169, 120, 74, 0.4);
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.16) 0%, rgba(34, 34, 44, 0.4) 100%);
        box-shadow: 0 30px 60px -25px rgba(169, 120, 74, 0.4);
        transform: translateY(-2px);
    }
    .hero-dash-bg {
        position: absolute;
        top: -60%;
        right: -15%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.18) 0%, transparent 65%);
        pointer-events: none;
        animation: floatGlow 8s ease-in-out infinite;
    }
    .hero-dash-bg2 {
        position: absolute;
        bottom: -50%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(34, 197, 94, 0.08) 0%, transparent 70%);
        pointer-events: none;
        animation: floatGlow 10s ease-in-out infinite reverse;
    }
    @keyframes floatGlow {
        0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.8; }
        50% { transform: translate(-15px, 15px) scale(1.05); opacity: 1; }
    }

    .hero-dash-left {
        position: relative;
        z-index: 1;
        flex: 1;
        min-width: 0;
    }
    .hero-dash-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.14em;
        margin-bottom: 14px;
    }
    .hero-dash-label svg { color: #c9a961; }
    .hero-dash-value {
        font-size: 46px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.045em;
        line-height: 1;
        margin-bottom: 18px;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 2px 24px rgba(201, 169, 97, 0.2);
    }
    .hero-dash-meta {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }
    .hero-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .hero-meta-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
    }
    .hero-meta-dot.green {
        background: #22c55e;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.8);
    }
    .hero-meta-divider {
        width: 1px;
        height: 12px;
        background: rgba(255, 255, 255, 0.1);
    }

    .hero-dash-right {
        display: flex;
        align-items: center;
        gap: 18px;
        position: relative;
        z-index: 1;
        padding-left: 30px;
        border-left: 1px solid rgba(255, 255, 255, 0.08);
    }
    .hero-trend {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        transition: all 0.25s;
    }
    .hero-trend.up {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(34, 197, 94, 0.05));
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
        box-shadow: 0 8px 20px -8px rgba(34, 197, 94, 0.5);
    }
    .hero-trend.down {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05));
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        box-shadow: 0 8px 20px -8px rgba(239, 68, 68, 0.5);
    }
    .hero-trend-info { min-width: 120px; }
    .hero-trend-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1;
        margin-bottom: 6px;
        font-variant-numeric: tabular-nums;
    }
    .hero-trend-label {
        font-size: 10.5px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .trend-pct {
        padding: 2px 7px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0;
    }
    .trend-pct.up { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .trend-pct.down { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    /* ================================================================
       KPI ROW
       ================================================================ */
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }
    .kpi-card {
        background: rgba(34, 34, 44, 0.28);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(201, 169, 97, 0.5), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 100% 0%, rgba(201, 169, 97, 0.06), transparent 60%);
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        background: rgba(34, 34, 44, 0.4);
        border-color: rgba(169, 120, 74, 0.35);
        box-shadow: 0 20px 40px -18px rgba(0, 0, 0, 0.7);
    }
    .kpi-card:hover::before { opacity: 1; }
    .kpi-card:hover::after { opacity: 1; }

    .kpi-card-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border: 1px solid;
        transition: transform 0.3s;
    }
    .kpi-card:hover .kpi-card-icon { transform: scale(1.05) rotate(-3deg); }

    .kpi-card-icon.gold   { background: rgba(169, 120, 74, 0.14); border-color: rgba(169, 120, 74, 0.3); color: #c9a961; box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.4); }
    .kpi-card-icon.blue   { background: rgba(59, 130, 246, 0.14); border-color: rgba(59, 130, 246, 0.3); color: #3b82f6; box-shadow: 0 6px 16px -6px rgba(59, 130, 246, 0.4); }
    .kpi-card-icon.purple { background: rgba(139, 92, 246, 0.14); border-color: rgba(139, 92, 246, 0.3); color: #8b5cf6; box-shadow: 0 6px 16px -6px rgba(139, 92, 246, 0.4); }
    .kpi-card-icon.green  { background: rgba(34, 197, 94, 0.14);  border-color: rgba(34, 197, 94, 0.3);  color: #22c55e; box-shadow: 0 6px 16px -6px rgba(34, 197, 94, 0.4); }

    .kpi-card-body { flex: 1; min-width: 0; }
    .kpi-card-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em;
        line-height: 1;
        margin-bottom: 5px;
        font-variant-numeric: tabular-nums;
    }
    .kpi-card-label {
        font-size: 10.5px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
    }
    .kpi-card-tag {
        padding: 4px 10px;
        border-radius: 7px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-muted);
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.04);
    }
    .kpi-card-tag.green { background: rgba(34, 197, 94, 0.12); color: #22c55e; border-color: rgba(34, 197, 94, 0.2); }
    .kpi-card-tag.amber { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
    .kpi-card-tag.blue  { background: rgba(59, 130, 246, 0.12); color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }

    /* ================================================================
       CHART CARD
       ================================================================ */
    .chart-card {
        background: rgba(34, 34, 44, 0.3);
        backdrop-filter: blur(26px) saturate(1.5);
        -webkit-backdrop-filter: blur(26px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 26px;
        margin-bottom: 18px;
        transition: all 0.3s;
    }
    .chart-card:hover {
        background: rgba(34, 34, 44, 0.38);
        border-color: rgba(169, 120, 74, 0.2);
    }
    .chart-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 22px;
        gap: 16px;
        flex-wrap: wrap;
    }
    .chart-card-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        margin-bottom: 4px;
    }
    .chart-card-sub {
        font-size: 12px;
        color: var(--text-muted);
    }
    .chart-legend {
        display: flex;
        gap: 20px;
        font-size: 11.5px;
        color: var(--text-secondary);
        font-weight: 600;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .legend-dot.gold  { background: #c9a961; box-shadow: 0 0 10px rgba(201, 169, 97, 0.7); }
    .legend-dot.green { background: #22c55e; box-shadow: 0 0 10px rgba(34, 197, 94, 0.7); }
    .chart-wrap {
        height: 280px;
        position: relative;
    }

    /* ================================================================
       CARD HEAD (SHARED)
       ================================================================ */
    .card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }
    .card-head-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.25);
        display: grid;
        place-items: center;
        color: #c9a961;
        flex-shrink: 0;
    }
    .card-head-icon.blue {
        background: rgba(59, 130, 246, 0.12);
        border-color: rgba(59, 130, 246, 0.25);
        color: #3b82f6;
    }
    .card-head-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        flex: 1;
        min-width: 0;
    }
    .card-head-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        color: #c9a961;
        text-decoration: none;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .card-head-link:hover {
        background: rgba(169, 120, 74, 0.2);
        border-color: rgba(169, 120, 74, 0.4);
        transform: translateX(2px);
    }

    /* ================================================================
       BOTTOM GRID
       ================================================================ */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .bottom-grid .card {
        background: rgba(34, 34, 44, 0.28);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 18px;
        transition: all 0.3s;
    }
    .bottom-grid .card:hover {
        background: rgba(34, 34, 44, 0.38);
        border-color: rgba(169, 120, 74, 0.2);
    }

    /* Stores List */
    .stores-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .store-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .store-row:hover {
        background: rgba(169, 120, 74, 0.06);
        transform: translateX(3px);
    }
    .store-rank {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: grid;
        place-items: center;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .store-rank.gold {
        background: linear-gradient(135deg, #fbbf24, #d97706);
        color: #fff;
        border-color: rgba(251, 191, 36, 0.4);
        box-shadow: 0 6px 14px -4px rgba(251, 191, 36, 0.6);
    }
    .store-rank.silver {
        background: linear-gradient(135deg, #cbd5e1, #64748b);
        color: #fff;
        border-color: rgba(203, 213, 225, 0.4);
        box-shadow: 0 6px 14px -4px rgba(203, 213, 225, 0.4);
    }
    .store-rank.bronze {
        background: linear-gradient(135deg, #d97706, #92400e);
        color: #fff;
        border-color: rgba(217, 119, 6, 0.4);
        box-shadow: 0 6px 14px -4px rgba(217, 119, 6, 0.5);
    }
    .store-info { flex: 1; min-width: 0; }
    .store-name {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .store-bar {
        height: 5px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 3px;
        overflow: hidden;
    }
    .store-fill {
        height: 100%;
        background: linear-gradient(90deg, #a9784a, #c9a961);
        border-radius: 3px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 12px rgba(201, 169, 97, 0.4);
    }
    .store-amount {
        font-size: 12.5px;
        font-weight: 800;
        color: #c9a961;
        flex-shrink: 0;
    }

    /* Transaction List */
    .txn-list { display: flex; flex-direction: column; gap: 4px; }
    .txn-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .txn-row:hover {
        background: rgba(169, 120, 74, 0.08);
        transform: translateX(3px);
    }
    .txn-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border: 1px solid;
    }
    .txn-icon.blue  { background: rgba(59, 130, 246, 0.12); border-color: rgba(59, 130, 246, 0.25); color: #3b82f6; }
    .txn-icon.green { background: rgba(34, 197, 94, 0.12);  border-color: rgba(34, 197, 94, 0.25);  color: #22c55e; }
    .txn-info { flex: 1; min-width: 0; }
    .txn-title {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-primary);
        font-family: ui-monospace, monospace;
        margin-bottom: 3px;
    }
    .txn-sub {
        font-size: 11px;
        color: var(--text-muted);
    }
    .txn-amount {
        font-size: 13px;
        font-weight: 800;
        color: #c9a961;
        flex-shrink: 0;
    }
    .txn-amount.green { color: #22c55e; }

    /* Empty */
    .empty-box { padding: 30px 20px; text-align: center; }
    .empty-icon {
        color: var(--text-muted);
        opacity: 0.35;
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
    }
    .empty-text {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ================================================================
       RESPONSIVE
       ================================================================ */
    @media (max-width: 1200px) {
        .kpi-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 1100px) {
        .bottom-grid { grid-template-columns: 1fr; }
        .hero-dash { flex-direction: column; align-items: flex-start; padding: 26px; }
        .hero-dash-value { font-size: 36px; }
        .hero-dash-right {
            padding-left: 0;
            padding-top: 20px;
            border-left: none;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            width: 100%;
        }
    }
    @media (max-width: 700px) {
        .kpi-row { grid-template-columns: 1fr; }
        .greeting-title { font-size: 20px; }
        .hero-dash-value { font-size: 30px; }
        .chart-wrap { height: 220px; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const labels = {!! json_encode(
        collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M Y'))->values()
    ) !!};

    const salesArr = {!! json_encode(
        collect(range(5, 0))->map(function($i) {
            $m = now()->subMonths($i);
            return (float) \App\Models\SalesReport::where('amount_paid', '>', 0)
                ->whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->sum('total_sales');
        })->values()
    ) !!};

    const payArr = {!! json_encode(
        collect(range(5, 0))->map(function($i) {
            $m = now()->subMonths($i);
            return (float) \App\Models\ConsignmentPayment::whereYear('payment_date', $m->year)
                ->whereMonth('payment_date', $m->month)
                ->sum('amount');
        })->values()
    ) !!};

    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    const g1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
    g1.addColorStop(0, 'rgba(201, 169, 97, 0.35)');
    g1.addColorStop(1, 'rgba(201, 169, 97, 0)');

    const g2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
    g2.addColorStop(0, 'rgba(34, 197, 94, 0.28)');
    g2.addColorStop(1, 'rgba(34, 197, 94, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Sales',
                    data: salesArr,
                    borderColor: '#c9a961',
                    backgroundColor: g1,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#c9a961',
                    pointBorderColor: '#1a1a22',
                    pointBorderWidth: 3,
                },
                {
                    label: 'Payments',
                    data: payArr,
                    borderColor: '#22c55e',
                    backgroundColor: g2,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#1a1a22',
                    pointBorderWidth: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(20, 20, 26, 0.98)',
                    borderColor: 'rgba(169, 120, 74, 0.35)',
                    borderWidth: 1,
                    padding: 14,
                    cornerRadius: 10,
                    titleColor: '#f5f3f0',
                    titleFont: { size: 13, weight: '700' },
                    bodyColor: '#a8a5a0',
                    bodyFont: { size: 12, weight: '600' },
                    displayColors: true,
                    boxPadding: 6,
                    callbacks: {
                        label: function(c) {
                            return '  ' + c.dataset.label + ':  \u20B1' + Number(c.parsed.y).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: '#6b6862',
                        font: { size: 11, weight: '600' },
                        padding: 10,
                    }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                    ticks: {
                        color: '#6b6862',
                        font: { size: 11, weight: '600' },
                        padding: 10,
                        callback: function(v) {
                            if (v >= 1000000) return '\u20B1' + (v/1000000) + 'M';
                            if (v >= 1000) return '\u20B1' + (v/1000) + 'k';
                            return '\u20B1' + v;
                        }
                    }
                }
            }
        }
    });
})();
</script>
@endpush