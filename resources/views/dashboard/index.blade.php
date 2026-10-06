@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', now()->format('l, F j, Y'))

@section('actions')
    <a href="{{ route('deliveries.create') }}" class="btn btn-ghost btn-sm">+ Delivery</a>
    <a href="{{ route('consignment.payments.create') }}" class="btn btn-primary btn-sm">+ Payment</a>
@endsection

@section('content')

@php
    $activeStores    = \App\Models\Store::where('status','active')->count();
    $totalStores     = \App\Models\Store::count();
    $productCount    = \App\Models\Product::count();
    $drToday         = \App\Models\DeliveryReceipt::whereDate('delivery_date', today())->count();
    $drTotal         = \App\Models\DeliveryReceipt::count();

    $totalSales      = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->sum('total_sales');
    $totalPaid       = (float) \App\Models\ConsignmentPayment::sum('amount');
    $Unpaid Balance     = (float) \App\Models\SalesReport::sum('balance');

    $monthSales      = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->where('created_at','>=',now()->startOfMonth())->sum('total_sales');
    $lastMonthSales  = (float) \App\Models\SalesReport::whereBetween('created_at',[now()->subMonth()->startOfMonth(),now()->subMonth()->endOfMonth()])->sum('total_sales');
    $salesTrend      = $lastMonthSales > 0 ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) : 0;
    $lowStock        = \App\Models\Product::where('is_active',true)->whereColumn('stock','<=','reorder_level')->count();
@endphp

<div class="greeting">
    <div>
        <h2 class="greeting-title">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
            <span class="greeting-name">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</span>
        </h2>
        <p class="greeting-sub">Here's your business snapshot for today.</p>
    </div>
    <div class="greeting-badge">
        <span class="pulse"></span>
        Live
    </div>
</div>

<div class="hero-kpi">
    <div class="hero-kpi-left">
        <div class="hero-label">Total Unpaid Balance</div>
        <div class="hero-value">&#8369;{{ number_format($Unpaid Balance, 2) }}</div>
        <div class="hero-meta">
            <span class="hero-meta-item">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                &#8369;{{ number_format($totalPaid, 0) }} collected
            </span>
        </div>
    </div>
    <div class="hero-kpi-right">
        <div class="hero-trend {{ $salesTrend >= 0 ? 'up' : 'down' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                @if($salesTrend >= 0)
                    <path d="M23 6l-9.5 9.5-5-5L1 18"/><path d="M17 6h6v6"/>
                @else
                    <path d="M23 18l-9.5-9.5-5 5L1 6"/><path d="M17 18h6v-6"/>
                @endif
            </svg>
            <span>{{ abs($salesTrend) }}%</span>
        </div>
        <div class="hero-trend-label">
            <div class="hero-trend-value">&#8369;{{ number_format($monthSales, 0) }}</div>
            <div class="hero-trend-caption">This month</div>
        </div>
    </div>
</div>

<div class="mini-grid">
    <div class="mini">
        <div class="mini-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">{{ $totalStores }}</div>
            <div class="mini-label">Stores</div>
        </div>
        <div class="mini-tag">{{ $activeStores }} active</div>
    </div>

    <div class="mini">
        <div class="mini-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">{{ $productCount }}</div>
            <div class="mini-label">Products</div>
        </div>
        @if($lowStock > 0)
            <div class="mini-tag warning">{{ $lowStock }} low</div>
        @else
            <div class="mini-tag success">In stock</div>
        @endif
    </div>

    <div class="mini">
        <div class="mini-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">{{ $drTotal }}</div>
            <div class="mini-label">Deliveries</div>
        </div>
        <div class="mini-tag info">{{ $drToday }} today</div>
    </div>

    <div class="mini">
        <div class="mini-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">&#8369;{{ number_format($totalPaid/1000, 1) }}k</div>
            <div class="mini-label">Collected</div>
        </div>
        <div class="mini-tag success">All-time</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Sales & Payments</div>
            <div class="card-sub">Last 6 months performance</div>
        </div>
        <div class="chart-legend">
            <div class="legend-item"><span class="dot" style="background: #c9a961;"></span> Sales</div>
            <div class="legend-item"><span class="dot" style="background: #22c55e;"></span> Payments</div>
        </div>
    </div>
    <div style="height: 240px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

<div class="bottom-grid">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Top Stores</div>
                <div class="card-sub">By delivered value</div>
            </div>
            <a href="{{ route('stores.index') }}" class="link">View all</a>
        </div>

        @php
            $topStores = \App\Models\Store::withSum('deliveryReceipts as total_delivered', 'total_amount')
                ->orderByDesc('total_delivered')->take(5)->get();
            $maxVal = $topStores->max('total_delivered') ?: 1;
        @endphp

        @if($topStores->isEmpty() || $topStores->first()->total_delivered === null)
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 3v18h18M7 16l4-4 4 4 6-6"/></svg>
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

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Recent Activity</div>
                <div class="card-sub">Latest transactions</div>
            </div>
        </div>

        @php
            $activity = collect();
            foreach (\App\Models\DeliveryReceipt::with('store')->latest()->take(4)->get() as $dr) {
                $activity->push(['type' => 'delivery', 'title' => $dr->dr_number, 'sub' => $dr->store->store_name ?? '-', 'amount' => $dr->total_amount, 'date' => $dr->created_at]);
            }
            foreach (\App\Models\ConsignmentPayment::with('store')->latest()->take(4)->get() as $p) {
                $activity->push(['type' => 'payment', 'title' => $p->payment_number, 'sub' => $p->store->store_name ?? '-', 'amount' => $p->amount, 'date' => $p->created_at]);
            }
            $activity = $activity->sortByDesc('date')->take(6);
        @endphp

        @if($activity->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                </div>
                <div class="empty-text">No activity yet</div>
            </div>
        @else
            <div class="activity-list">
                @foreach($activity as $a)
                    <div class="activity-row">
                        <div class="activity-icon {{ $a['type'] === 'delivery' ? 'blue' : 'green' }}">
                            @if($a['type'] === 'delivery')
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            @else
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            @endif
                        </div>
                        <div class="activity-info">
                            <div class="activity-title">{{ $a['title'] }}</div>
                            <div class="activity-sub">{{ $a['sub'] }} - {{ $a['date']->diffForHumans() }}</div>
                        </div>
                        <div class="activity-amount {{ $a['type'] === 'payment' ? 'green' : '' }}">
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
    .greeting {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        gap: 16px;
    }
    .greeting-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.3;
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
        margin-top: 4px;
    }
    .greeting-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        color: #22c55e;
        white-space: nowrap;
    }
    .pulse {
        width: 6px; height: 6px; border-radius: 50%;
        background: #22c55e;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        50% { box-shadow: 0 0 0 5px rgba(34, 197, 94, 0); }
    }

    .hero-kpi {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.12) 0%, rgba(34, 34, 44, 0.6) 100%);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(169, 120, 74, 0.2);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .hero-kpi::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.12), transparent 70%);
        pointer-events: none;
    }
    .hero-kpi:hover {
        border-color: rgba(169, 120, 74, 0.35);
        box-shadow: 0 20px 40px -20px rgba(169, 120, 74, 0.3);
    }
    .hero-kpi-left { position: relative; z-index: 1; }
    .hero-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 8px;
    }
    .hero-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.04em;
        line-height: 1;
        margin-bottom: 12px;
    }
    .hero-meta {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    .hero-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .hero-kpi-right {
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        z-index: 1;
        padding-left: 24px;
        border-left: 1px solid rgba(255, 255, 255, 0.06);
    }
    .hero-trend {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .hero-trend.up {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.25);
    }
    .hero-trend.down {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }
    .hero-trend svg { display: block; }
    .hero-trend-label { min-width: 90px; }
    .hero-trend-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1;
        margin-bottom: 4px;
    }
    .hero-trend-caption {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
    }

    .mini-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .mini {
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
    }
    .mini:hover {
        transform: translateY(-2px);
        border-color: rgba(169, 120, 74, 0.25);
        background: rgba(34, 34, 44, 0.7);
    }
    .mini-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid;
        place-items: center;
        color: #c9a961;
        flex-shrink: 0;
    }
    .mini-content { flex: 1; min-width: 0; }
    .mini-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em;
        line-height: 1;
        margin-bottom: 3px;
    }
    .mini-label {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
    }
    .mini-tag {
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
        background: rgba(169, 120, 74, 0.1);
        color: #c9a961;
    }
    .mini-tag.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    .mini-tag.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .mini-tag.info    { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

    .chart-legend {
        display: flex;
        gap: 16px;
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .link {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
        transition: color 0.15s;
    }
    .link:hover { color: #c9a961; }

    .stores-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .store-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 10px;
        transition: background 0.15s;
    }
    .store-row:hover { background: rgba(255, 255, 255, 0.02); }
    .store-rank {
        width: 26px;
        height: 26px;
        border-radius: 8px;
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
    }
    .store-rank.silver {
        background: linear-gradient(135deg, #cbd5e1, #64748b);
        color: #fff;
        border-color: rgba(203, 213, 225, 0.4);
    }
    .store-rank.bronze {
        background: linear-gradient(135deg, #d97706, #92400e);
        color: #fff;
        border-color: rgba(217, 119, 6, 0.4);
    }
    .store-info { flex: 1; min-width: 0; }
    .store-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .store-bar {
        height: 4px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 3px;
        overflow: hidden;
    }
    .store-fill {
        height: 100%;
        background: linear-gradient(90deg, #a9784a, #c9a961);
        border-radius: 3px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .store-amount {
        font-size: 12px;
        font-weight: 700;
        color: #c9a961;
        flex-shrink: 0;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .activity-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 10px;
        transition: background 0.15s;
    }
    .activity-row:hover { background: rgba(255, 255, 255, 0.02); }
    .activity-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .activity-icon.blue {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    .activity-icon.green {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    .activity-info { flex: 1; min-width: 0; }
    .activity-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .activity-sub {
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .activity-amount {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-primary);
        flex-shrink: 0;
    }
    .activity-amount.green { color: #22c55e; }

    .empty-state {
        padding: 40px 20px;
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
        .mini-grid { grid-template-columns: repeat(2, 1fr); }
        .bottom-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .greeting { flex-direction: column; align-items: flex-start; }
        .hero-kpi { flex-direction: column; align-items: flex-start; padding: 20px; }
        .hero-value { font-size: 26px; }
        .hero-kpi-right {
            padding-left: 0;
            padding-top: 16px;
            border-left: none;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            width: 100%;
        }
        .mini-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const labels = {!! json_encode(
        collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M'))->values()
    ) !!};

    const salesArr = {!! json_encode(
        collect(range(5, 0))->map(function($i) {
            $m = now()->subMonths($i);
            return (float) \App\Models\SalesReport::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->sum('total_sales');
        })->values()
    ) !!};

    const payArr = {!! json_encode(
        collect(range(5, 0))->map(function($i) {
            $m = now()->subMonths($i);
            return (float) \App\Models\ConsignmentPayment::whereYear('payment_date', $m->year)->whereMonth('payment_date', $m->month)->sum('amount');
        })->values()
    ) !!};

    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    const g1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
    g1.addColorStop(0, 'rgba(201, 169, 97, 0.25)');
    g1.addColorStop(1, 'rgba(201, 169, 97, 0)');

    const g2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
    g2.addColorStop(0, 'rgba(34, 197, 94, 0.2)');
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
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Payments',
                    data: payArr,
                    borderColor: '#22c55e',
                    backgroundColor: g2,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
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
                    backgroundColor: 'rgba(20, 20, 26, 0.95)',
                    borderColor: 'rgba(169, 120, 74, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(c) {
                            return '  ' + c.dataset.label + ':  \u20B1' + Number(c.parsed.y).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b6862', font: { size: 11, weight: '600' } }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: '#6b6862',
                        font: { size: 11, weight: '600' },
                        callback: v => v >= 1000 ? '\u20B1' + (v/1000) + 'k' : '\u20B1' + v,
                    }
                }
            }
        }
    });
})();
</script>
@endpush