@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', now()->format('l, F j, Y'))

@section('content')

@php
    // ============ STATS ============
    $activeStores    = \App\Models\Store::where('status','active')->count();
    $totalStores     = \App\Models\Store::count();
    $productCount    = \App\Models\Product::count();
    $drToday         = \App\Models\DeliveryReceipt::whereDate('delivery_date', today())->count();
    $drTotal         = \App\Models\DeliveryReceipt::count();

    $totalSales      = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->sum('total_sales');
    $totalPaid       = (float) \App\Models\ConsignmentPayment::verified()->sum('amount');
    $outstanding     = (float) \App\Models\SalesReport::sum('balance');
    $paymentsToday   = (float) \App\Models\ConsignmentPayment::verified()->whereDate('payment_date', today())->sum('amount');

    $monthSales      = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->where('created_at','>=',now()->startOfMonth())->sum('total_sales');
    $lastMonthSales  = (float) \App\Models\SalesReport::where('amount_paid', '>', 0)->whereBetween('created_at',[now()->subMonth()->startOfMonth(),now()->subMonth()->endOfMonth()])->sum('total_sales');
    $salesTrend      = $lastMonthSales > 0 ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) : 0;
    $lowStock        = \App\Models\Product::where('is_active',true)->whereColumn('stock','<=','reorder_level')->count();

    $pendingOrders   = \App\Models\ReorderRequest::where('status', 'pending')->count();
    $outForDelivery  = \App\Models\DeliveryReceipt::whereNotNull('out_for_delivery_at')->where('customer_confirmed', false)->count();
    $unpaidDR        = \App\Models\DeliveryReceipt::where('balance', '>', 0)->count();

    $orderStatus = [
        'pending'   => \App\Models\ReorderRequest::where('status', 'pending')->count(),
        'approved'  => \App\Models\ReorderRequest::where('status', 'approved')->count(),
        'rejected'  => \App\Models\ReorderRequest::where('status', 'rejected')->count(),
        'cancelled' => \App\Models\ReorderRequest::where('status', 'cancelled')->count(),
    ];

    $orderTrend = collect();
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $orderTrend->push([
            'label'     => $date->format('D'),
            'day'       => $date->format('d'),
            'date'      => $date->format('Y-m-d'),
            'pending'   => \App\Models\ReorderRequest::whereDate('created_at', $date)->where('status', 'pending')->count(),
            'approved'  => \App\Models\ReorderRequest::whereDate('created_at', $date)->where('status', 'approved')->count(),
            'rejected'  => \App\Models\ReorderRequest::whereDate('created_at', $date)->where('status', 'rejected')->count(),
            'cancelled' => \App\Models\ReorderRequest::whereDate('created_at', $date)->where('status', 'cancelled')->count(),
        ]);
    }

    $maxTrendValue = 0;
    foreach ($orderTrend as $row) {
        $maxTrendValue = max($maxTrendValue, $row['pending'], $row['approved'], $row['rejected'], $row['cancelled']);
    }
    if ($maxTrendValue < 1) $maxTrendValue = 1;

    $trendPoints = ['pending' => [], 'approved' => [], 'rejected' => [], 'cancelled' => []];
    $totalDays = count($orderTrend);
    foreach ($orderTrend as $i => $row) {
        $x = $totalDays > 1 ? ($i / ($totalDays - 1)) * 100 : 50;
        foreach (['pending', 'approved', 'rejected', 'cancelled'] as $status) {
            $y = 100 - (($row[$status] / $maxTrendValue) * 85);
            $trendPoints[$status][] = round($x, 2) . ',' . round($y, 2);
        }
    }
    $trendPaths = [];
    foreach ($trendPoints as $status => $pts) {
        $trendPaths[$status] = count($pts) > 1 ? 'M' . implode(' L', $pts) : '';
    }

    $weeklyRevenue = collect();
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $weeklyRevenue->push([
            'date'    => $date->format('Y-m-d'),
            'label'   => $date->format('D'),
            'day'     => $date->format('d'),
            'revenue' => (float) \App\Models\SalesReport::whereDate('created_at', $date)->sum('total_sales'),
            'paid'    => (float) \App\Models\ConsignmentPayment::verified()->whereDate('payment_date', $date)->sum('amount'),
        ]);
    }
    $maxWeekly = max($weeklyRevenue->max('revenue'), $weeklyRevenue->max('paid'), 1);

    $recentOrders = \App\Models\ReorderRequest::with(['store', 'items'])
        ->latest()
        ->take(8)
        ->get();

    $ordersDaily = collect();
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $ordersDaily->push([
            'label'    => $date->format('D'),
            'day'      => $date->format('d'),
            'date'     => $date->format('Y-m-d'),
            'is_today' => $date->isToday(),
            'count'    => \App\Models\ReorderRequest::whereDate('created_at', $date)->count(),
        ]);
    }

    $ordersToday     = $ordersDaily->last()['count'] ?? 0;
    $ordersYesterday = $ordersDaily->count() > 1 ? ($ordersDaily->slice(-2, 1)->first()['count'] ?? 0) : 0;
    $ordersWeekTotal = $ordersDaily->sum('count');
    $maxOrdersDaily  = max($ordersDaily->max('count'), 1);

    $topStores = \App\Models\Store::withSum('deliveryReceipts as total_delivered', 'total_amount')
        ->orderByDesc('total_delivered')
        ->take(5)
        ->get();
    $maxStoreRev = $topStores->max('total_delivered') ?: 1;

    $lowStockProducts = \App\Models\Product::where('is_active', true)
        ->whereColumn('stock', '<=', 'reorder_level')
        ->orderBy('stock')
        ->take(4)
        ->get();
@endphp

{{-- ==================== GREETING ==================== --}}
<div class="db-greeting">
    <div class="db-greeting-left">
        <h2 class="db-greeting-title">
            @php
                $hour = now()->hour;
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
            @endphp
            {{ $greeting }}, <span class="db-greeting-name">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</span>
        </h2>
        <p class="db-greeting-sub">Here's your business overview for today.</p>
    </div>
    <div class="db-greeting-badge">
        <span class="db-pulse"></span>
        Live
    </div>
</div>

{{-- ==================== MAIN METRICS (4 cards) ==================== --}}
<div class="db-metrics">
    <div class="db-metric db-metric-amber">
        <div class="db-metric-head">
            <div class="db-metric-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
            </div>
            <div class="db-metric-label">Pending Orders</div>
        </div>
        <div class="db-metric-value">{{ $pendingOrders }}</div>
        <div class="db-metric-foot">Awaiting approval</div>
    </div>

    <div class="db-metric db-metric-blue">
        <div class="db-metric-head">
            <div class="db-metric-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="db-metric-label">In Transit</div>
        </div>
        <div class="db-metric-value">{{ $outForDelivery }}</div>
        <div class="db-metric-foot">{{ $drToday }} deliveries today</div>
    </div>

    <div class="db-metric db-metric-green">
        <div class="db-metric-head">
            <div class="db-metric-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div class="db-metric-label">Today's Collections</div>
        </div>
        <div class="db-metric-value">₱{{ number_format($paymentsToday, 0) }}</div>
        <div class="db-metric-foot">Total: ₱{{ number_format($totalPaid, 0) }}</div>
    </div>

    <div class="db-metric db-metric-gold">
        <div class="db-metric-head">
            <div class="db-metric-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/></svg>
            </div>
            <div class="db-metric-label">Active Stores</div>
        </div>
        <div class="db-metric-value">{{ $activeStores }}</div>
        <div class="db-metric-foot">of {{ $totalStores }} registered</div>
    </div>
</div>

{{-- ==================== CHART ROW ==================== --}}
<div class="db-2col">

    {{-- WEEKLY REVENUE CHART --}}
    <section class="db-section">
        <div class="db-section-head">
            <div>
                <div class="db-section-eyebrow">WEEKLY TREND</div>
                <h3 class="db-section-title">Revenue vs Collections</h3>
            </div>
            <div class="db-section-meta">
                <strong>₱{{ number_format($weeklyRevenue->sum('revenue'), 0) }}</strong>
            </div>
        </div>

        <div class="db-chart">
            <div class="db-chart-y">
                <div class="db-chart-y-label">₱{{ number_format($maxWeekly, 0) }}</div>
                <div class="db-chart-y-label">₱{{ number_format($maxWeekly / 2, 0) }}</div>
                <div class="db-chart-y-label">₱0</div>
            </div>
            <div class="db-chart-bars-wrap">
                <div class="db-chart-grid">
                    <div class="db-grid-line"></div>
                    <div class="db-grid-line"></div>
                    <div class="db-grid-line"></div>
                </div>
                <div class="db-chart-bars">
                    @foreach($weeklyRevenue as $day)
                        @php
                            $revPct = ($day['revenue'] / $maxWeekly) * 100;
                            $paidPct = ($day['paid'] / $maxWeekly) * 100;
                        @endphp
                        <div class="db-bar-group" title="{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }} · Revenue ₱{{ number_format($day['revenue'], 2) }} · Paid ₱{{ number_format($day['paid'], 2) }}">
                            <div class="db-bar-stack">
                                <div class="db-bar db-bar-revenue" style="height: {{ $revPct }}%;"></div>
                                <div class="db-bar db-bar-paid" style="height: {{ $paidPct }}%;"></div>
                            </div>
                            <div class="db-bar-label">{{ $day['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="db-legend">
            <div class="db-legend-item">
                <span class="db-legend-dot" style="background:linear-gradient(180deg,#d4b673,#c9a961);"></span>
                Revenue
            </div>
            <div class="db-legend-item">
                <span class="db-legend-dot" style="background:linear-gradient(180deg,#4ade80,#16a34a);"></span>
                Collections
            </div>
        </div>
    </section>

    {{-- ORDERS STATUS LINE CHART --}}
    <section class="db-section db-trend-section">
        <div class="db-section-head">
            <div>
                <div class="db-section-eyebrow">ORDER STATUS TREND</div>
                <h3 class="db-section-title">Daily Breakdown <span class="db-trend-title-sub">· Last 7 days</span></h3>
            </div>
            <div class="db-section-meta">
                <span class="db-trend-total-pill">
                    <span class="db-trend-total-dot"></span>
                    <strong>{{ array_sum($orderStatus) }}</strong> total
                </span>
            </div>
        </div>

        <div class="db-trend-chart">
            <div class="db-trend-y">
                <div class="db-trend-y-label">{{ $maxTrendValue }}</div>
                <div class="db-trend-y-label">{{ round($maxTrendValue / 2) }}</div>
                <div class="db-trend-y-label">0</div>
            </div>

            <div class="db-trend-area">
                <div class="db-trend-grid">
                    <div class="db-trend-grid-line"></div>
                    <div class="db-trend-grid-line"></div>
                    <div class="db-trend-grid-line"></div>
                </div>

                <svg class="db-trend-svg" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="dbApprovedGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#34d399" stop-opacity="0.28"/>
                            <stop offset="100%" stop-color="#34d399" stop-opacity="0"/>
                        </linearGradient>
                    </defs>

                    @if($trendPaths['approved'])
                        <path d="{{ $trendPaths['approved'] }} L100,100 L0,100 Z" fill="url(#dbApprovedGrad)"/>
                    @endif
                    @if($trendPaths['pending'])
                        <path d="{{ $trendPaths['pending'] }}" fill="none" stroke="#fbbf24" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                    @if($trendPaths['approved'])
                        <path d="{{ $trendPaths['approved'] }}" fill="none" stroke="#34d399" stroke-width="2.2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                    @if($trendPaths['rejected'])
                        <path d="{{ $trendPaths['rejected'] }}" fill="none" stroke="#f87171" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                    @if($trendPaths['cancelled'])
                        <path d="{{ $trendPaths['cancelled'] }}" fill="none" stroke="#857e76" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                </svg>

                <div class="db-trend-points">
                    @foreach($orderTrend as $i => $row)
                        @php
                            $xPct = $totalDays > 1 ? ($i / ($totalDays - 1)) * 100 : 50;
                            $isToday = $i === $totalDays - 1;
                        @endphp
                        <div class="db-trend-point-group {{ $isToday ? 'is-today' : '' }}" style="left: {{ $xPct }}%;">
                            @foreach(['pending' => '#fbbf24', 'approved' => '#34d399', 'rejected' => '#f87171', 'cancelled' => '#857e76'] as $status => $color)
                                @php $yPct = 100 - (($row[$status] / $maxTrendValue) * 85); @endphp
                                <div class="db-trend-point {{ $row[$status] > 0 ? 'has-value' : '' }}" style="top: {{ $yPct }}%; --pt-color: {{ $color }};" title="{{ ucfirst($status) }}: {{ $row[$status] }}">
                                    <span class="db-trend-point-glow" style="background: {{ $color }};"></span>
                                    <span class="db-trend-point-dot" style="background: {{ $color }};"></span>
                                    @if($row[$status] > 0)
                                        <span class="db-trend-point-val" style="background: {{ $color }};">{{ $row[$status] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="db-trend-x">
                    @foreach($orderTrend as $i => $day)
                        @php $isToday = $i === $totalDays - 1; @endphp
                        <div class="db-trend-x-label {{ $isToday ? 'is-today' : '' }}">
                            <span class="db-trend-x-day">{{ $day['label'] }}</span>
                            <span class="db-trend-x-date">{{ $day['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="db-trend-legend">
            <div class="db-trend-legend-card db-trend-card-pending">
                <div class="db-trend-legend-top">
                    <span class="db-trend-legend-dot"></span>
                    <span class="db-trend-legend-name">Pending</span>
                </div>
                <div class="db-trend-legend-count">{{ $orderStatus['pending'] }}</div>
            </div>
            <div class="db-trend-legend-card db-trend-card-approved">
                <div class="db-trend-legend-top">
                    <span class="db-trend-legend-dot"></span>
                    <span class="db-trend-legend-name">Approved</span>
                </div>
                <div class="db-trend-legend-count">{{ $orderStatus['approved'] }}</div>
            </div>
            <div class="db-trend-legend-card db-trend-card-rejected">
                <div class="db-trend-legend-top">
                    <span class="db-trend-legend-dot"></span>
                    <span class="db-trend-legend-name">Rejected</span>
                </div>
                <div class="db-trend-legend-count">{{ $orderStatus['rejected'] }}</div>
            </div>
            <div class="db-trend-legend-card db-trend-card-cancelled">
                <div class="db-trend-legend-top">
                    <span class="db-trend-legend-dot"></span>
                    <span class="db-trend-legend-name">Cancelled</span>
                </div>
                <div class="db-trend-legend-count">{{ $orderStatus['cancelled'] }}</div>
            </div>
        </div>
    </section>

</div>

{{-- ==================== BOTTOM ROW ==================== --}}
<div class="db-2col">

    {{-- TOP STORES --}}
    <section class="db-section">
        <div class="db-section-head">
            <div>
                <div class="db-section-eyebrow">PERFORMANCE</div>
                <h3 class="db-section-title">Top Stores</h3>
            </div>
        </div>

        @if($topStores->count() > 0)
            <div class="db-stores-list">
                @foreach($topStores as $i => $s)
                    @php
                        $pct = (($s->total_delivered ?? 0) / $maxStoreRev) * 100;
                    @endphp
                    <div class="db-store-row">
                        <div class="db-store-rank {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : '')) }}">{{ $i + 1 }}</div>
                        <div class="db-store-info">
                            <div class="db-store-name">{{ $s->store_name }}</div>
                            <div class="db-store-code">{{ $s->code ?? '' }}</div>
                        </div>
                        <div class="db-store-chart">
                            <div class="db-store-bar" style="width:{{ $pct }}%;"></div>
                        </div>
                        <div class="db-store-amount">₱{{ number_format($s->total_delivered ?? 0, 0) }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="db-empty-small">Walay stores data.</div>
        @endif
    </section>

    {{-- ORDERS LIVE FEED --}}
    <section class="db-section">
        <div class="db-section-head">
            <div>
                <div class="db-section-eyebrow">ORDERS LIVE FEED</div>
                <h3 class="db-section-title">Recent Orders</h3>
            </div>
            <a href="{{ route('reorder-requests.index') }}" class="db-section-link">View all →</a>
        </div>

        <div class="db-orders-summary">
            <div class="db-orders-summary-item">
                <div class="db-orders-summary-value">{{ $ordersToday }}</div>
                <div class="db-orders-summary-label">Today</div>
            </div>
            <div class="db-orders-summary-item">
                <div class="db-orders-summary-value">{{ $ordersWeekTotal }}</div>
                <div class="db-orders-summary-label">This Week</div>
            </div>
            @if($ordersYesterday > 0)
                @php $trend = round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 0); @endphp
                <div class="db-orders-summary-item">
                    <div class="db-orders-summary-value {{ $trend >= 0 ? 'up' : 'down' }}">
                        {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                    </div>
                    <div class="db-orders-summary-label">vs Yesterday</div>
                </div>
            @endif
        </div>

        <div class="db-orders-daily">
            @foreach($ordersDaily as $d)
                @php $pct = ($d['count'] / $maxOrdersDaily) * 100; @endphp
                <div class="db-orders-daily-col {{ $d['is_today'] ? 'today' : '' }}" title="{{ \Carbon\Carbon::parse($d['date'])->format('M d, Y') }} · {{ $d['count'] }} order{{ $d['count'] != 1 ? 's' : '' }}">
                    <div class="db-orders-daily-bar-wrap">
                        <div class="db-orders-daily-bar" style="height: {{ max($pct, 3) }}%;"></div>
                    </div>
                    <div class="db-orders-daily-count">{{ $d['count'] }}</div>
                    <div class="db-orders-daily-label">{{ $d['label'] }}</div>
                </div>
            @endforeach
        </div>

        @if($recentOrders->count() > 0)
            <div class="db-orders-list">
                @foreach($recentOrders as $order)
                    @php
                        $statusClass = strtolower($order->status);
                        $isToday = $order->created_at->isToday();
                    @endphp
                    <a href="{{ route('reorder-requests.show', $order->id) }}" class="db-order-item">
                        <div class="db-order-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                            </svg>
                        </div>
                        <div class="db-order-content">
                            <div class="db-order-num">
                                {{ $order->request_number }}
                                @if($isToday)
                                    <span class="db-order-new">NEW</span>
                                @endif
                            </div>
                            <div class="db-order-meta">
                                {{ $order->store->store_name ?? '-' }}
                                · {{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }}
                                · {{ $order->created_at->diffForHumans(null, true) }}
                            </div>
                        </div>
                        <div class="db-order-right">
                            <div class="db-order-amount">₱{{ number_format($order->total_amount, 0) }}</div>
                            <span class="db-order-badge {{ $statusClass }}">{{ $order->status }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="db-empty-small">Walay orders pa.</div>
        @endif
    </section>

</div>

{{-- ==================== ALERTS ROW ==================== --}}
@if($lowStock > 0 || $unpaidDR > 0 || $pendingOrders > 0)
<div class="db-alerts">
    @if($pendingOrders > 0)
        <a href="{{ route('reorder-requests.index', ['status' => 'pending']) }}" class="db-alert db-alert-amber">
            <div class="db-alert-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="db-alert-content">
                <div class="db-alert-title">{{ $pendingOrders }} Pending Order{{ $pendingOrders > 1 ? 's' : '' }}</div>
                <div class="db-alert-desc">Awaiting your approval</div>
            </div>
        </a>
    @endif

    @if($unpaidDR > 0)
        <a href="{{ route('deliveries.index') }}" class="db-alert db-alert-blue">
            <div class="db-alert-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
            </div>
            <div class="db-alert-content">
                <div class="db-alert-title">{{ $unpaidDR }} Unpaid Deliver{{ $unpaidDR > 1 ? 'ies' : 'y' }}</div>
                <div class="db-alert-desc">₱{{ number_format($outstanding, 0) }} outstanding</div>
            </div>
        </a>
    @endif

    @if($lowStock > 0)
        <a href="{{ route('products.index') }}" class="db-alert db-alert-red">
            <div class="db-alert-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </div>
            <div class="db-alert-content">
                <div class="db-alert-title">{{ $lowStock }} Low Stock Alert{{ $lowStock > 1 ? 's' : '' }}</div>
                <div class="db-alert-desc">Products below reorder level</div>
            </div>
        </a>
    @endif
</div>
@endif

@endsection

@push('styles')
<style>
/* ============================================================
   DASHBOARD — TRANSPARENT / FROSTED GLASS
   ============================================================ */
:root {
    --db-glass:         rgba(24, 20, 16, .62);
    --db-glass-2:       rgba(18, 15, 12, .58);
    --db-glass-soft:    rgba(24, 20, 16, .45);
    --db-blur:          18px;

    --db-border:        rgba(255,255,255,.09);
    --db-border-2:      rgba(255,255,255,.06);
    --db-border-hi:     rgba(201,169,97,.28);

    --db-ink:           #f5f2ec;
    --db-ink-2:         #b8b2aa;
    --db-ink-3:         #857e76;
    --db-ink-4:         #5f5952;

    --db-gold:          #d4b36a;
    --db-gold-2:        #e6c88a;
    --db-gold-dim:      rgba(201,169,97,.14);

    --db-ok:            #34d399;
    --db-info:          #60a5fa;
    --db-warn:          #fbbf24;
    --db-bad:           #f87171;
    --db-mute:          #857e76;
}

/* ── GREETING ─────────────────────────────────────────── */
.db-greeting {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 22px 24px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(120% 140% at 100% 0%, rgba(201,169,97,.10), transparent 55%),
        linear-gradient(180deg, var(--db-glass), var(--db-glass-2));
    backdrop-filter: blur(var(--db-blur)) saturate(140%);
    -webkit-backdrop-filter: blur(var(--db-blur)) saturate(140%);
    border: 1px solid var(--db-border);
    border-radius: 16px;
    box-shadow: 0 22px 48px -32px rgba(0,0,0,.9), inset 0 1px 0 rgba(255,255,255,.05);
}
.db-greeting::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, var(--db-gold-2), var(--db-gold), transparent);
}
.db-greeting::after {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(201,169,97,.12), transparent 70%);
    pointer-events: none;
    animation: dbGlow 8s ease-in-out infinite;
}
@keyframes dbGlow {
    0%, 100% { transform: scale(1); opacity: .6; }
    50%      { transform: scale(1.15); opacity: 1; }
}
.db-greeting-left { position: relative; z-index: 1; }
.db-greeting-title {
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -.028em;
    color: var(--db-ink);
    line-height: 1.15;
    text-shadow: 0 1px 2px rgba(0,0,0,.35);
}
.db-greeting-name { color: var(--db-gold); }
.db-greeting-sub {
    font-size: 12.5px;
    color: var(--db-ink-2);
    margin-top: 6px;
}
.db-greeting-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: rgba(52,211,153,.12);
    border: 1px solid rgba(52,211,153,.3);
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    color: var(--db-ok);
    letter-spacing: .05em;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(52,211,153,.15);
    position: relative;
    z-index: 1;
}
.db-pulse {
    width: 6px; height: 6px;
    background: var(--db-ok);
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(52,211,153,.25), 0 0 12px rgba(52,211,153,.6);
    animation: dbPulse 2s ease-in-out infinite;
}
@keyframes dbPulse {
    0%, 100% { opacity: 1; }
    50%      { opacity: .4; }
}

/* ── METRICS ──────────────────────────────────────────── */
.db-metrics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.db-metric {
    position: relative;
    padding: 18px 20px;
    background:
        radial-gradient(circle at 100% 0%, rgba(var(--metric-rgb, 201, 169, 97), .10) 0%, transparent 60%),
        linear-gradient(180deg, var(--db-glass), var(--db-glass-2));
    backdrop-filter: blur(var(--db-blur)) saturate(140%);
    -webkit-backdrop-filter: blur(var(--db-blur)) saturate(140%);
    border: 1px solid var(--db-border);
    border-radius: 14px;
    transition: transform .25s cubic-bezier(.4,0,.2,1), border-color .25s, box-shadow .25s;
    overflow: hidden;
    isolation: isolate;
    box-shadow: 0 18px 40px -30px rgba(0,0,0,.85), inset 0 1px 0 rgba(255,255,255,.04);
}
.db-metric::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--metric-color, #c9a961), transparent);
    opacity: .9;
}
.db-metric:hover {
    transform: translateY(-3px);
    border-color: var(--db-border-hi);
    box-shadow: 0 22px 48px -28px rgba(0,0,0,.9), inset 0 1px 0 rgba(255,255,255,.06);
}
.db-metric-amber { --metric-color: #fbbf24; --metric-rgb: 251, 191, 36; }
.db-metric-blue  { --metric-color: #60a5fa; --metric-rgb: 96, 165, 250; }
.db-metric-green { --metric-color: #34d399; --metric-rgb: 52, 211, 153; }
.db-metric-gold  { --metric-color: #d4b36a; --metric-rgb: 212, 179, 106; }

.db-metric-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
}
.db-metric-icon {
    width: 38px; height: 38px;
    border-radius: 11px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    transition: transform .3s;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
}
.db-metric:hover .db-metric-icon { transform: scale(1.08) rotate(-3deg); }

.db-metric-amber .db-metric-icon { background: rgba(251,191,36,.15); color: var(--db-warn); }
.db-metric-blue  .db-metric-icon { background: rgba(96,165,250,.15); color: var(--db-info); }
.db-metric-green .db-metric-icon { background: rgba(52,211,153,.15); color: var(--db-ok); }
.db-metric-gold  .db-metric-icon { background: rgba(212,179,106,.15); color: var(--db-gold); }

.db-metric-label {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--db-ink-3);
    text-transform: uppercase;
    letter-spacing: .1em;
    flex: 1;
}
.db-metric-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--db-ink);
    letter-spacing: -.035em;
    line-height: 1;
    margin-bottom: 8px;
    text-shadow: 0 1px 2px rgba(0,0,0,.35);
}
.db-metric-foot {
    font-size: 11.5px;
    color: var(--db-ink-3);
    font-weight: 500;
}

/* ── SECTION ──────────────────────────────────────────── */
.db-2col {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 20px;
}
.db-section {
    position: relative;
    padding: 20px 22px;
    background: linear-gradient(180deg, var(--db-glass), var(--db-glass-2));
    backdrop-filter: blur(var(--db-blur)) saturate(140%);
    -webkit-backdrop-filter: blur(var(--db-blur)) saturate(140%);
    border: 1px solid var(--db-border);
    border-radius: 16px;
    transition: border-color .25s, box-shadow .25s;
    box-shadow: 0 22px 48px -32px rgba(0,0,0,.9), inset 0 1px 0 rgba(255,255,255,.04);
}
.db-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
    pointer-events: none;
}
.db-section:hover {
    border-color: var(--db-border-hi);
    box-shadow: 0 26px 52px -30px rgba(0,0,0,.95), inset 0 1px 0 rgba(255,255,255,.05);
}
.db-section-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--db-border-2);
}
.db-section-eyebrow {
    font-size: 9.5px;
    font-weight: 700;
    color: var(--db-gold);
    letter-spacing: .16em;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.db-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--db-ink);
    letter-spacing: -.022em;
    margin: 0;
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
}
.db-section-meta {
    font-size: 11.5px;
    color: var(--db-ink-3);
}
.db-section-meta strong {
    font-size: 16px;
    font-weight: 700;
    color: var(--db-gold);
}
.db-section-link {
    font-size: 11.5px;
    color: var(--db-gold);
    text-decoration: none;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    background: rgba(212,179,106,.08);
    border: 1px solid rgba(212,179,106,.2);
    transition: all .2s;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.db-section-link:hover {
    background: rgba(212,179,106,.18);
    color: var(--db-gold-2);
    transform: translateX(3px);
}

/* ── CHART ────────────────────────────────────────────── */
.db-chart {
    display: flex;
    gap: 12px;
    height: 200px;
    padding: 10px 0;
}
.db-chart-y {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2px 0;
    flex-shrink: 0;
}
.db-chart-y-label {
    font-size: 10px;
    color: var(--db-ink-3);
    font-family: ui-monospace, "SF Mono", monospace;
    font-weight: 600;
    text-align: right;
    min-width: 52px;
    letter-spacing: -.01em;
}
.db-chart-bars-wrap { flex: 1; position: relative; min-width: 0; }
.db-chart-grid {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    pointer-events: none;
}
.db-grid-line {
    height: 1px;
    background: linear-gradient(90deg, rgba(255,255,255,.08), rgba(255,255,255,.02) 80%, transparent);
}
.db-chart-bars {
    position: relative;
    height: 100%;
    display: flex;
    align-items: flex-end;
    gap: 4px;
    padding: 0 4px;
}
.db-bar-group {
    flex: 1;
    min-width: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    position: relative;
}
.db-bar-stack {
    width: 100%;
    max-width: 36px;
    height: 100%;
    display: flex;
    align-items: flex-end;
    gap: 3px;
}
.db-bar {
    flex: 1;
    border-radius: 4px 4px 0 0;
    transition: filter .2s;
    min-height: 2px;
    cursor: pointer;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.15);
    position: relative;
}
.db-bar-revenue { background: linear-gradient(180deg, var(--db-gold-2) 0%, var(--db-gold) 40%, #8a5f36 100%); }
.db-bar-paid    { background: linear-gradient(180deg, #4ade80 0%, #22c55e 40%, #16a34a 100%); }
.db-bar-group:hover .db-bar { filter: brightness(1.18); }
.db-bar-label {
    position: absolute;
    bottom: -20px;
    font-size: 9.5px;
    color: var(--db-ink-3);
    font-weight: 600;
    font-family: ui-monospace, "SF Mono", monospace;
    letter-spacing: .02em;
}

.db-legend {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 11.5px;
    color: var(--db-ink-2);
    padding-top: 24px;
}
.db-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}
.db-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    flex-shrink: 0;
}

/* ── LINE CHART ───────────────────────────────────────── */
.db-linechart {
    display: flex;
    gap: 10px;
    height: 200px;
    padding: 6px 0 22px;
}
.db-linechart-y {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2px 0;
    flex-shrink: 0;
}
.db-y-label {
    font-size: 9.5px;
    color: var(--db-ink-3);
    font-family: ui-monospace, "SF Mono", monospace;
    font-weight: 600;
    text-align: right;
    min-width: 24px;
}
.db-linechart-area {
    flex: 1;
    position: relative;
    height: 100%;
    padding-bottom: 20px;
}
.db-linechart-grid {
    position: absolute;
    inset: 0 0 20px 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    pointer-events: none;
}
.db-linechart-svg {
    position: absolute;
    inset: 0 0 20px 0;
    width: 100%;
    height: calc(100% - 20px);
    overflow: visible;
}
.db-linechart-svg path {
    filter: drop-shadow(0 2px 6px currentColor);
    opacity: .95;
}
.db-linechart-points {
    position: absolute;
    inset: 0 0 20px 0;
    pointer-events: none;
}
.db-point-group {
    position: absolute;
    top: 0; bottom: 0;
    width: 0;
}
.db-point {
    position: absolute;
    width: 9px; height: 9px;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    box-shadow:
        0 0 0 3px rgba(15,15,20,.95),
        0 0 0 4px currentColor,
        0 0 12px currentColor;
    transition: transform .2s cubic-bezier(.34,1.56,.64,1);
}
.db-point:hover { transform: translate(-50%, -50%) scale(1.6); }
.db-linechart-x {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    display: flex;
    justify-content: space-between;
}
.db-x-label {
    font-size: 9px;
    color: var(--db-ink-3);
    font-weight: 700;
    font-family: ui-monospace, "SF Mono", monospace;
    text-align: center;
    flex: 1;
}
.db-linechart-legend {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    padding-top: 12px;
    margin-top: 8px;
    border-top: 1px solid var(--db-border-2);
}
.db-legend-line {
    width: 14px;
    height: 3px;
    border-radius: 2px;
    flex-shrink: 0;
    box-shadow: 0 0 8px currentColor;
}
.db-legend-count {
    margin-left: 6px;
    padding: 2px 8px;
    background: rgba(212,179,106,.12);
    color: var(--db-gold);
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    font-family: ui-monospace, "SF Mono", monospace;
}

/* ── STORES ───────────────────────────────────────────── */
.db-stores-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.db-store-row {
    display: grid;
    grid-template-columns: 26px 1fr 100px 70px;
    gap: 12px;
    align-items: center;
    padding: 10px 8px;
    margin: 0 -8px;
    border-radius: 10px;
    transition: background .2s, transform .2s;
}
.db-store-row:hover {
    background: rgba(212,179,106,.06);
    transform: translateX(3px);
}
.db-store-rank {
    width: 24px; height: 24px;
    border-radius: 7px;
    background: rgba(255,255,255,.06);
    border: 1px solid var(--db-border-2);
    color: var(--db-ink-2);
    font-size: 11px;
    font-weight: 800;
    font-family: ui-monospace, "SF Mono", monospace;
    display: grid;
    place-items: center;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.1);
}
.db-store-rank.gold   { background: linear-gradient(140deg,#fcd34d,#d97706); color: #fff; border-color: transparent; box-shadow: 0 4px 12px rgba(251,191,36,.4), inset 0 1px 0 rgba(255,255,255,.25); }
.db-store-rank.silver { background: linear-gradient(140deg,#e5e7eb,#9ca3af); color: #1a1612; border-color: transparent; }
.db-store-rank.bronze { background: linear-gradient(140deg,#d97706,#92400e); color: #fff; border-color: transparent; }
.db-store-info { min-width: 0; }
.db-store-name {
    font-size: 12px;
    font-weight: 700;
    color: var(--db-ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.db-store-code {
    font-size: 9.5px;
    color: var(--db-ink-3);
    font-family: ui-monospace, "SF Mono", monospace;
    margin-top: 2px;
}
.db-store-chart {
    height: 6px;
    background: rgba(0,0,0,.35);
    border-radius: 3px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.04);
}
.db-store-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--db-gold), var(--db-gold-2));
    border-radius: 3px;
    transition: width .6s cubic-bezier(.2,.7,.2,1);
    min-width: 3px;
    box-shadow: 0 0 8px rgba(212,179,106,.4);
}
.db-store-amount {
    font-size: 12px;
    font-weight: 800;
    color: var(--db-gold);
    text-align: right;
    font-family: ui-monospace, "SF Mono", monospace;
    white-space: nowrap;
}

/* ── ORDERS ───────────────────────────────────────────── */
.db-orders-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 10px;
    padding: 14px 16px;
    margin-bottom: 12px;
    background:
        radial-gradient(circle at 0% 0%, rgba(212,179,106,.1), transparent 60%),
        linear-gradient(135deg, rgba(212,179,106,.06), rgba(30,26,22,.4));
    border: 1px solid rgba(212,179,106,.18);
    border-radius: 14px;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.db-orders-summary-item { text-align: center; }
.db-orders-summary-value {
    font-size: 24px;
    font-weight: 800;
    color: var(--db-ink);
    letter-spacing: -.04em;
    line-height: 1;
    margin-bottom: 6px;
    font-variant-numeric: tabular-nums;
    text-shadow: 0 2px 12px rgba(0,0,0,.3);
}
.db-orders-summary-value.up   { color: var(--db-ok); }
.db-orders-summary-value.down { color: var(--db-bad); }
.db-orders-summary-label {
    font-size: 9.5px;
    font-weight: 800;
    color: var(--db-ink-3);
    text-transform: uppercase;
    letter-spacing: .08em;
}

.db-orders-daily {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    padding: 14px 12px;
    margin-bottom: 14px;
    background: rgba(255,255,255,.02);
    border: 1px solid var(--db-border-2);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.db-orders-daily-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 4px 2px;
    border-radius: 8px;
    cursor: pointer;
    transition: background .2s;
}
.db-orders-daily-col:hover { background: rgba(212,179,106,.06); }
.db-orders-daily-bar-wrap {
    width: 100%;
    height: 50px;
    background: rgba(0,0,0,.3);
    border-radius: 8px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.04);
}
.db-orders-daily-bar {
    width: 100%;
    background: linear-gradient(180deg, var(--db-ink-3), var(--db-ink-4));
    border-radius: 8px 8px 0 0;
    transition: all .3s;
    min-height: 3px;
    position: relative;
}
.db-orders-daily-bar::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 40%;
    background: linear-gradient(180deg, rgba(255,255,255,.15), transparent);
    border-radius: 8px 8px 0 0;
}
.db-orders-daily-col:hover .db-orders-daily-bar {
    background: linear-gradient(180deg, var(--db-gold-2), var(--db-gold));
}
.db-orders-daily-col.today {
    background: rgba(212,179,106,.08);
}
.db-orders-daily-col.today .db-orders-daily-bar {
    background: linear-gradient(180deg, var(--db-gold-2), #8a5f36);
    box-shadow: 0 0 20px rgba(212,179,106,.5), inset 0 1px 0 rgba(255,255,255,.2);
}
.db-orders-daily-col.today .db-orders-daily-count { color: var(--db-gold); }
.db-orders-daily-count {
    font-size: 12px;
    font-weight: 800;
    color: var(--db-ink-2);
    font-family: ui-monospace, "SF Mono", monospace;
    line-height: 1;
    margin-top: 4px;
}
.db-orders-daily-label {
    font-size: 9px;
    color: var(--db-ink-4);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
}

.db-orders-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.db-order-item {
    display: grid;
    grid-template-columns: 36px 1fr auto;
    gap: 12px;
    align-items: center;
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
    transition: all .2s;
    border: 1px solid rgba(255,255,255,.03);
    background: rgba(255,255,255,.01);
}
.db-order-item:hover {
    background: linear-gradient(135deg, rgba(212,179,106,.08), rgba(212,179,106,.02));
    border-color: rgba(212,179,106,.2);
    transform: translateX(4px);
    box-shadow: 0 8px 20px -10px rgba(212,179,106,.3);
}
.db-order-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: rgba(212,179,106,.12);
    border: 1px solid rgba(212,179,106,.25);
    color: var(--db-gold);
    display: grid;
    place-items: center;
    flex-shrink: 0;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.1), 0 4px 12px -4px rgba(212,179,106,.4);
    transition: transform .2s;
}
.db-order-item:hover .db-order-icon {
    transform: scale(1.08) rotate(-3deg);
}
.db-order-content { min-width: 0; }
.db-order-num {
    font-size: 12px;
    font-weight: 800;
    color: var(--db-ink);
    font-family: ui-monospace, "SF Mono", monospace;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 2px;
    letter-spacing: -.01em;
}
.db-order-new {
    display: inline-flex;
    padding: 1px 6px;
    background: rgba(52,211,153,.15);
    border: 1px solid rgba(52,211,153,.35);
    border-radius: 4px;
    font-size: 8.5px;
    font-weight: 800;
    color: var(--db-ok);
    letter-spacing: .05em;
    animation: dbNewPulse 2s ease-in-out infinite;
}
@keyframes dbNewPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(52,211,153,.4); }
    50%      { box-shadow: 0 0 0 4px rgba(52,211,153,0); }
}
.db-order-meta {
    font-size: 10.5px;
    color: var(--db-ink-3);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.db-order-right {
    text-align: right;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}
.db-order-amount {
    font-size: 13px;
    font-weight: 800;
    color: var(--db-gold);
    font-family: ui-monospace, "SF Mono", monospace;
    line-height: 1;
    letter-spacing: -.02em;
}
.db-order-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    border: 1px solid;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.05);
}
.db-order-badge::before {
    content: '';
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
    box-shadow: 0 0 6px currentColor;
    animation: dbBadgePulse 2s ease-in-out infinite;
}
@keyframes dbBadgePulse {
    0%, 100% { opacity: 1; }
    50%      { opacity: .5; }
}
.db-order-badge.pending   { background: rgba(251,191,36,.12); color: var(--db-warn); border-color: rgba(251,191,36,.3); }
.db-order-badge.approved  { background: rgba(52,211,153,.12); color: var(--db-ok);   border-color: rgba(52,211,153,.3); }
.db-order-badge.rejected  { background: rgba(248,113,113,.12);color: var(--db-bad);  border-color: rgba(248,113,113,.3); }
.db-order-badge.cancelled { background: rgba(133,126,118,.12);color: var(--db-ink-3);border-color: rgba(133,126,118,.3); }

/* ── ALERTS ───────────────────────────────────────────── */
.db-alerts {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 16px;
}
.db-alert {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 14px;
    text-decoration: none;
    transition: all .25s;
    border: 1px solid;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    isolation: isolate;
}
.db-alert::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 3px;
    background: currentColor;
    opacity: .6;
}
.db-alert:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 32px -12px rgba(0,0,0,.6);
}
.db-alert-amber { background: rgba(251,191,36,.08); border-color: rgba(251,191,36,.25); color: var(--db-warn); }
.db-alert-blue  { background: rgba(96,165,250,.08); border-color: rgba(96,165,250,.25); color: var(--db-info); }
.db-alert-red   { background: rgba(248,113,113,.08);border-color: rgba(248,113,113,.25);color: var(--db-bad); }

.db-alert-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
    transition: transform .2s;
}
.db-alert:hover .db-alert-icon { transform: scale(1.1); }
.db-alert-amber .db-alert-icon { background: rgba(251,191,36,.15); }
.db-alert-blue  .db-alert-icon { background: rgba(96,165,250,.15); }
.db-alert-red   .db-alert-icon { background: rgba(248,113,113,.15); }

.db-alert-content { min-width: 0; }
.db-alert-title {
    font-size: 13px;
    font-weight: 800;
    color: var(--db-ink);
    letter-spacing: -.01em;
}
.db-alert-desc {
    font-size: 10.5px;
    color: var(--db-ink-2);
    margin-top: 2px;
}

/* ── EMPTY ────────────────────────────────────────────── */
.db-empty-small {
    text-align: center;
    padding: 30px 20px;
    color: var(--db-ink-3);
    font-size: 12px;
}

/* ── ENTRANCE ANIMATIONS ─────────────────────────────── */
.db-metric, .db-section, .db-alert {
    animation: dbFadeIn .5s cubic-bezier(.4,0,.2,1) both;
}
.db-metric:nth-child(1) { animation-delay: .05s; }
.db-metric:nth-child(2) { animation-delay: .1s; }
.db-metric:nth-child(3) { animation-delay: .15s; }
.db-metric:nth-child(4) { animation-delay: .2s; }
.db-2col:nth-of-type(1) .db-section:nth-child(1) { animation-delay: .25s; }
.db-2col:nth-of-type(1) .db-section:nth-child(2) { animation-delay: .3s; }
.db-2col:nth-of-type(2) .db-section:nth-child(1) { animation-delay: .35s; }
.db-2col:nth-of-type(2) .db-section:nth-child(2) { animation-delay: .4s; }
.db-alerts { animation-delay: .45s; }

@keyframes dbFadeIn {
    0%   { opacity: 0; transform: translateY(15px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* ── FALLBACK ────────────────────────────────────────── */
@supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
    .db-greeting, .db-metric, .db-section { background: rgba(18,15,12,.92); }
    .db-alert { background: rgba(18,15,12,.85); }
}

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 1100px) {
    .db-metrics { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .db-2col    { grid-template-columns: 1fr; }
    .db-alerts  { grid-template-columns: 1fr; }
    .db-metric-value { font-size: 24px; }
    .db-orders-summary-value { font-size: 20px; }
}
@media (max-width: 700px) {
    :root { --db-blur: 12px; }
    .db-greeting { padding: 18px; }
    .db-greeting-title { font-size: 18px; }
    .db-metric { padding: 14px 16px; }
    .db-metric-value { font-size: 22px; }
    .db-section { padding: 16px; }
    .db-chart { height: 160px; }
    .db-linechart { height: 150px; }
    .db-point { width: 6px; height: 6px; }
    .db-store-row { grid-template-columns: 24px 1fr 60px 60px; gap: 8px; }
    .db-store-amount { font-size: 11px; }
    .db-orders-daily-bar-wrap { height: 42px; }
    .db-orders-summary-value { font-size: 18px; }
}
@media (max-width: 480px) {
    .db-metrics { grid-template-columns: 1fr 1fr; gap: 10px; }
    .db-metric { padding: 12px 14px; }
    .db-metric-value { font-size: 20px; }
    .db-metric-label { font-size: 9.5px; }
    .db-metric-foot { font-size: 10px; }
    .db-greeting-title { font-size: 16px; }
    .db-store-row { grid-template-columns: 22px 1fr 55px; }
    .db-store-chart { display: none; }
    .db-orders-summary { grid-template-columns: 1fr 1fr; }
    .db-order-item { padding: 8px; gap: 8px; }
    .db-order-icon { width: 32px; height: 32px; }
    .db-order-num { font-size: 11px; }
    .db-order-amount { font-size: 12px; }
    .db-orders-daily-bar-wrap { height: 36px; }
    .db-orders-daily-count { font-size: 10px; }
    .db-orders-daily-label { font-size: 8px; }
}
@media (prefers-reduced-motion: reduce) {
    .db-greeting *, .db-metric *, .db-section *, .db-alert * {
        animation: none !important;
        transition: none !important;
    }
}

/* ═══════════════════════════════════════════════ */
/* DB-TREND-PREMIUM — Order Status Trend redesign  */
/* ═══════════════════════════════════════════════ */

.db-trend-section { position: relative; }

.db-trend-title-sub {
    color: var(--db-ink-3, #857e76);
    font-weight: 500;
    font-size: 12px;
    letter-spacing: 0;
    margin-left: 4px;
}

/* ═══ Total pill sa section head ═══ */
.db-trend-total-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px 6px 10px;
    background: rgba(212, 179, 106, 0.1);
    border: 1px solid rgba(212, 179, 106, 0.25);
    border-radius: 100px;
    font-size: 11.5px;
    font-weight: 600;
    color: #a8a5a0;
}
.db-trend-total-pill strong {
    color: #d4b36a;
    font-weight: 800;
    font-size: 13px;
    font-family: ui-monospace, "SF Mono", monospace;
}
.db-trend-total-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #d4b36a;
    box-shadow: 0 0 8px rgba(212, 179, 106, 0.7);
    animation: dbTrendPulse 2.2s ease-in-out infinite;
}
@keyframes dbTrendPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.25); }
}

/* ═══ Chart wrapper ═══ */
.db-trend-chart {
    display: flex;
    gap: 16px;
    height: 220px;
    padding: 8px 0 36px;
    position: relative;
}

/* ═══ Y-axis ═══ */
.db-trend-y {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 4px 0;
}
.db-trend-y-label {
    font-size: 10px;
    font-weight: 700;
    color: #857e76;
    font-family: ui-monospace, "SF Mono", monospace;
    text-align: right;
    min-width: 32px;
    padding: 2px 6px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 5px;
    letter-spacing: -0.02em;
    line-height: 1;
}

/* ═══ Chart area ═══ */
.db-trend-area {
    flex: 1;
    position: relative;
    min-width: 0;
    overflow: hidden;
}

/* ═══ Grid lines ═══ */
.db-trend-grid {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    pointer-events: none;
}
.db-trend-grid-line {
    height: 1px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02) 80%, transparent);
}

/* ═══ SVG lines ═══ */
.db-trend-svg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    overflow: visible;
    pointer-events: none;
}

/* ═══ Data points ═══ */
.db-trend-points {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.db-trend-point-group {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 0;
    transform: translateX(-50%);
}
.db-trend-point-group.is-today::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 50%;
    width: 1px;
    background: linear-gradient(180deg, rgba(212, 179, 106, 0.35), transparent 90%);
    transform: translateX(-50%);
    pointer-events: none;
}

.db-trend-point {
    position: absolute;
    left: 50%;
    width: 10px;
    height: 10px;
    transform: translate(-50%, -50%);
    pointer-events: auto;
    cursor: pointer;
    z-index: 2;
}
.db-trend-point.has-value { z-index: 3; }

/* Glow ring — hidden by default, visible sa has-value */
.db-trend-point-glow {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    opacity: 0;
    filter: blur(6px);
    transition: opacity 0.2s;
}
.db-trend-point.has-value .db-trend-point-glow {
    opacity: 0.55;
}

.db-trend-point-dot {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 2px solid #1a1612;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    transition: transform 0.2s;
}
.db-trend-point.has-value .db-trend-point-dot {
    box-shadow:
        0 0 0 3px rgba(255, 255, 255, 0.08),
        0 2px 8px rgba(0, 0, 0, 0.5);
}

.db-trend-point:hover .db-trend-point-dot {
    transform: scale(1.4);
}

/* Value label */
.db-trend-point-val {
    position: absolute;
    top: -22px;
    left: 50%;
    transform: translateX(-50%);
    padding: 2px 7px;
    border-radius: 6px;
    font-size: 9.5px;
    font-weight: 800;
    font-family: ui-monospace, "SF Mono", monospace;
    color: #1a1612;
    letter-spacing: -0.02em;
    line-height: 1;
    white-space: nowrap;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
    opacity: 0;
    transition: opacity 0.2s, top 0.2s;
}
.db-trend-point.has-value .db-trend-point-val {
    opacity: 1;
}
.db-trend-point:hover .db-trend-point-val {
    top: -26px;
}

/* ═══ X-axis labels ═══ */
.db-trend-x {
    position: absolute;
    bottom: -32px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
}
.db-trend-x-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1px;
    flex: 1;
    min-width: 0;
    transition: transform 0.15s;
}
.db-trend-x-day {
    font-size: 10.5px;
    font-weight: 700;
    color: #857e76;
    letter-spacing: 0.02em;
    line-height: 1.2;
}
.db-trend-x-date {
    font-size: 9px;
    font-weight: 600;
    color: #4a4540;
    font-family: ui-monospace, "SF Mono", monospace;
    line-height: 1;
}
.db-trend-x-label.is-today .db-trend-x-day {
    color: #d4b36a;
}
.db-trend-x-label.is-today .db-trend-x-date {
    color: #d4b36a;
    opacity: 0.7;
}

/* ═══ Legend cards ═══ */
.db-trend-legend {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
    padding-top: 14px;
    border-top: 1px dashed rgba(255, 255, 255, 0.06);
    margin-top: 8px;
}
.db-trend-legend-card {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 10px 12px;
    background: rgba(255, 255, 255, 0.025);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    transition: all 0.2s;
    min-width: 0;
}
.db-trend-legend-card:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
}
.db-trend-legend-top {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
}
.db-trend-legend-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 6px currentColor;
}
.db-trend-legend-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #a8a5a0;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.db-trend-legend-count {
    font-size: 20px;
    font-weight: 800;
    font-family: ui-monospace, "SF Mono", monospace;
    letter-spacing: -0.03em;
    line-height: 1;
    color: #fafafa;
}

/* Card color themes */
.db-trend-card-pending .db-trend-legend-dot { background: #fbbf24; color: #fbbf24; }
.db-trend-card-pending .db-trend-legend-count { color: #fbbf24; }
.db-trend-card-approved .db-trend-legend-dot { background: #34d399; color: #34d399; }
.db-trend-card-approved .db-trend-legend-count { color: #34d399; }
.db-trend-card-rejected .db-trend-legend-dot { background: #f87171; color: #f87171; }
.db-trend-card-rejected .db-trend-legend-count { color: #f87171; }
.db-trend-card-cancelled .db-trend-legend-dot { background: #857e76; color: #857e76; }
.db-trend-card-cancelled .db-trend-legend-count { color: #a8a5a0; }

/* ═══ Responsive ═══ */
@media (max-width: 900px) {
    .db-trend-chart { height: 190px; padding: 6px 0 34px; }
    .db-trend-legend { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .db-trend-legend-count { font-size: 18px; }
}
@media (max-width: 520px) {
    .db-trend-chart { height: 170px; }
    .db-trend-y-label { font-size: 9px; min-width: 26px; padding: 2px 5px; }
    .db-trend-x-day { font-size: 9.5px; }
    .db-trend-x-date { font-size: 8px; }
    .db-trend-legend-card { padding: 8px 10px; }
    .db-trend-legend-name { font-size: 9.5px; }
    .db-trend-legend-count { font-size: 16px; }
    .db-trend-point-val { font-size: 8.5px; padding: 2px 5px; }
}
</style>
@endpush