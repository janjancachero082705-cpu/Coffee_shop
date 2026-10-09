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

    // ============ NEW STATS ============
    $pendingOrders   = \App\Models\ReorderRequest::where('status', 'pending')->count();
    $outForDelivery  = \App\Models\DeliveryReceipt::whereNotNull('out_for_delivery_at')->where('customer_confirmed', false)->count();
    $unpaidDR        = \App\Models\DeliveryReceipt::where('balance', '>', 0)->count();

    // ============ ORDER STATUS TOTALS ============
    $orderStatus = [
        'pending'   => \App\Models\ReorderRequest::where('status', 'pending')->count(),
        'approved'  => \App\Models\ReorderRequest::where('status', 'approved')->count(),
        'rejected'  => \App\Models\ReorderRequest::where('status', 'rejected')->count(),
        'cancelled' => \App\Models\ReorderRequest::where('status', 'cancelled')->count(),
    ];

    // ============ ORDER STATUS TREND (last 7 days) ============
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

    // Max value across all statuses para sa scaling
    $maxTrendValue = 0;
    foreach ($orderTrend as $row) {
        $maxTrendValue = max($maxTrendValue, $row['pending'], $row['approved'], $row['rejected'], $row['cancelled']);
    }
    if ($maxTrendValue < 1) $maxTrendValue = 1;

    // Generate SVG points for each status line
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

    // ============ WEEKLY REVENUE (last 7 days) ============
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

    // ============ RECENT ACTIVITY ============
    $activities = collect();

    \App\Models\ReorderRequest::with('store')->latest()->take(3)->get()->each(function($r) use (&$activities) {
        $activities->push([
            'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>',
            'color' => '#f59e0b',
            'title' => 'New Order',
            'desc' => ($r->store->store_name ?? '-') . ' · ' . $r->request_number,
            'amount' => (float) $r->total_amount,
            'time' => $r->created_at,
            'url' => route('reorder-requests.show', $r->id),
        ]);
    });

    \App\Models\ConsignmentPayment::with('store')->latest()->take(3)->get()->each(function($p) use (&$activities) {
        $activities->push([
            'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
            'color' => '#22c55e',
            'title' => 'Payment',
            'desc' => ($p->store->store_name ?? '-') . ' · ' . ucfirst($p->method),
            'amount' => (float) $p->amount,
            'time' => $p->created_at,
            'url' => route('consignment.payments.show', $p->id),
        ]);
    });

    \App\Models\DeliveryReceipt::with('store')->latest()->take(3)->get()->each(function($d) use (&$activities) {
        $activities->push([
            'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
            'color' => '#3b82f6',
            'title' => 'Delivery',
            'desc' => ($d->store->store_name ?? '-') . ' · ' . $d->dr_number,
            'amount' => (float) $d->total_amount,
            'time' => $d->created_at,
            'url' => route('deliveries.show', $d->id),
        ]);
    });

    $recentActivities = $activities->sortByDesc('time')->take(6)->values();

    // ============ TOP STORES ============
    $topStores = \App\Models\Store::withSum('deliveryReceipts as total_delivered', 'total_amount')
        ->orderByDesc('total_delivered')
        ->take(5)
        ->get();
    $maxStoreRev = $topStores->max('total_delivered') ?: 1;

    // ============ LOW STOCK ============
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
                <span class="db-legend-dot" style="background:linear-gradient(180deg,#c9a961,#b8944d);"></span>
                Revenue
            </div>
            <div class="db-legend-item">
                <span class="db-legend-dot" style="background:linear-gradient(180deg,#22c55e,#16a34a);"></span>
                Collections
            </div>
        </div>
    </section>

    {{-- ORDERS STATUS LINE CHART --}}
    <section class="db-section">
        <div class="db-section-head">
            <div>
                <div class="db-section-eyebrow">ORDER STATUS TREND</div>
                <h3 class="db-section-title">Daily Breakdown (7 days)</h3>
            </div>
            <div class="db-section-meta">
                <strong>{{ array_sum($orderStatus) }}</strong> total
            </div>
        </div>

        <div class="db-linechart">
            {{-- Y-axis labels --}}
            <div class="db-linechart-y">
                <div class="db-y-label">{{ $maxTrendValue }}</div>
                <div class="db-y-label">{{ round($maxTrendValue / 2) }}</div>
                <div class="db-y-label">0</div>
            </div>

            {{-- Chart area --}}
            <div class="db-linechart-area">
                {{-- Grid lines --}}
                <div class="db-linechart-grid">
                    <div class="db-grid-line"></div>
                    <div class="db-grid-line"></div>
                    <div class="db-grid-line"></div>
                </div>

                {{-- SVG lines --}}
                <svg class="db-linechart-svg" viewBox="0 0 100 100" preserveAspectRatio="none">
                    {{-- Area under each line (subtle fill) --}}
                    @if($trendPaths['approved'])
                        <path d="{{ $trendPaths['approved'] }} L100,100 L0,100 Z" fill="rgba(34,197,94,0.08)"/>
                    @endif

                    {{-- Pending line (amber) --}}
                    @if($trendPaths['pending'])
                        <path d="{{ $trendPaths['pending'] }}" fill="none" stroke="#f59e0b" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif

                    {{-- Approved line (green) --}}
                    @if($trendPaths['approved'])
                        <path d="{{ $trendPaths['approved'] }}" fill="none" stroke="#22c55e" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif

                    {{-- Rejected line (red) --}}
                    @if($trendPaths['rejected'])
                        <path d="{{ $trendPaths['rejected'] }}" fill="none" stroke="#ef4444" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif

                    {{-- Cancelled line (gray) --}}
                    @if($trendPaths['cancelled'])
                        <path d="{{ $trendPaths['cancelled'] }}" fill="none" stroke="#71717a" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                </svg>

                {{-- Data points --}}
                <div class="db-linechart-points">
                    @foreach($orderTrend as $i => $row)
                        @php
                            $xPct = $totalDays > 1 ? ($i / ($totalDays - 1)) * 100 : 50;
                        @endphp
                        <div class="db-point-group" style="left: {{ $xPct }}%;">
                            @foreach(['pending' => '#f59e0b', 'approved' => '#22c55e', 'rejected' => '#ef4444', 'cancelled' => '#71717a'] as $status => $color)
                                @php
                                    $yPct = 100 - (($row[$status] / $maxTrendValue) * 85);
                                @endphp
                                <div class="db-point" style="top: {{ $yPct }}%; background: {{ $color }};" title="{{ ucfirst($status) }}: {{ $row[$status] }}"></div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- X labels --}}
                <div class="db-linechart-x">
                    @foreach($orderTrend as $day)
                        <div class="db-x-label">{{ $day['label'] }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="db-linechart-legend">
            <div class="db-legend-item">
                <span class="db-legend-line" style="background:#f59e0b;"></span>
                Pending
                <span class="db-legend-count">{{ $orderStatus['pending'] }}</span>
            </div>
            <div class="db-legend-item">
                <span class="db-legend-line" style="background:#22c55e;"></span>
                Approved
                <span class="db-legend-count">{{ $orderStatus['approved'] }}</span>
            </div>
            <div class="db-legend-item">
                <span class="db-legend-line" style="background:#ef4444;"></span>
                Rejected
                <span class="db-legend-count">{{ $orderStatus['rejected'] }}</span>
            </div>
            <div class="db-legend-item">
                <span class="db-legend-line" style="background:#71717a;"></span>
                Cancelled
                <span class="db-legend-count">{{ $orderStatus['cancelled'] }}</span>
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

    {{-- RECENT ACTIVITY --}}
    <section class="db-section">
        <div class="db-section-head">
            <div>
                <div class="db-section-eyebrow">LIVE FEED</div>
                <h3 class="db-section-title">Recent Activity</h3>
            </div>
            <a href="{{ route('transactions.index') }}" class="db-section-link">View all →’</a>
        </div>

        @if($recentActivities->count() > 0)
            <div class="db-activity-list">
                @foreach($recentActivities as $act)
                    <a href="{{ $act['url'] }}" class="db-activity-item" style="--accent: {{ $act['color'] }};">
                        <div class="db-activity-marker">{!! $act['icon'] !!}</div>
                        <div class="db-activity-content">
                            <div class="db-activity-title">{{ $act['title'] }}</div>
                            <div class="db-activity-desc">{{ $act['desc'] }}</div>
                        </div>
                        <div class="db-activity-meta">
                            <div class="db-activity-amount">₱{{ number_format($act['amount'], 0) }}</div>
                            <div class="db-activity-time">{{ $act['time']->diffForHumans(null, true) }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="db-empty-small">Walay recent activity.</div>
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
       DASHBOARD "” MODERN DEVELOPER DESIGN
       ============================================================ */

    /* ========== GREETING ========== */
    .db-greeting {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .db-greeting-title {
        font-size: 20px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .db-greeting-name { color: #c9a961; }
    .db-greeting-sub {
        font-size: 12.5px;
        color: #71717a;
        margin-top: 4px;
    }
    .db-greeting-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.25);
        border-radius: 100px;
        font-size: 10.5px;
        font-weight: 800;
        color: #22c55e;
        letter-spacing: 0.05em;
    }
    .db-pulse {
        width: 6px; height: 6px;
        background: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        animation: dbPulse 2s ease-in-out infinite;
    }
    @keyframes dbPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* ========== METRICS ========== */
    .db-metrics {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .db-metric {
        position: relative;
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .db-metric::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
    }
    .db-metric-amber::before { background: linear-gradient(90deg, #f59e0b, transparent); }
    .db-metric-blue::before { background: linear-gradient(90deg, #3b82f6, transparent); }
    .db-metric-green::before { background: linear-gradient(90deg, #22c55e, transparent); }
    .db-metric-gold::before { background: linear-gradient(90deg, #c9a961, transparent); }

    .db-metric:hover {
        transform: translateY(-2px);
        border-color: rgba(201, 169, 97, 0.25);
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.6);
    }

    .db-metric-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .db-metric-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .db-metric-amber .db-metric-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .db-metric-blue .db-metric-icon { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .db-metric-green .db-metric-icon { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .db-metric-gold .db-metric-icon { background: rgba(201, 169, 97, 0.15); color: #c9a961; }

    .db-metric-label {
        font-size: 10.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        flex: 1;
    }

    .db-metric-value {
        font-size: 24px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.03em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        margin-bottom: 6px;
    }
    .db-metric-foot {
        font-size: 11px;
        color: #71717a;
    }

    /* ========== SECTION ========== */
    .db-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    .db-section {
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.85), rgba(21, 18, 15, 0.85));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
    }
    .db-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .db-section-eyebrow {
        font-size: 9.5px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: 0.15em;
        margin-bottom: 4px;
    }
    .db-section-title {
        font-size: 15px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
    }
    .db-section-meta {
        font-size: 11px;
        color: #71717a;
    }
    .db-section-meta strong { color: #c9a961; }
    .db-section-link {
        font-size: 11.5px;
        color: #c9a961;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.15s;
    }
    .db-section-link:hover { color: #d4b673; }

    /* ========== CHART ========== */
    .db-chart {
        display: flex;
        gap: 10px;
        height: 180px;
        padding: 8px 0;
    }
    .db-chart-y {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2px 0;
        flex-shrink: 0;
    }
    .db-chart-y-label {
        font-size: 9.5px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        font-weight: 600;
        text-align: right;
        min-width: 48px;
    }
    .db-chart-bars-wrap {
        flex: 1;
        position: relative;
    }
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
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.04), transparent);
    }
    .db-chart-bars {
        position: relative;
        height: 100%;
        display: flex;
        align-items: flex-end;
        gap: 4px;
        padding: 0 2px;
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
        gap: 2px;
    }
    .db-bar {
        flex: 1;
        border-radius: 3px 3px 0 0;
        transition: all 0.3s ease;
        min-height: 2px;
        cursor: pointer;
    }
    .db-bar-revenue { background: linear-gradient(180deg, #c9a961, #b8944d); }
    .db-bar-paid { background: linear-gradient(180deg, #22c55e, #16a34a); }
    .db-bar:hover { filter: brightness(1.15); }
    .db-bar-label {
        position: absolute;
        bottom: -18px;
        font-size: 9px;
        color: #71717a;
        font-weight: 700;
        font-family: ui-monospace, monospace;
    }

    .db-legend {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        font-size: 10.5px;
        color: #a1a1aa;
        padding-top: 22px;
    }
    .db-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    .db-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    /* ========== STATUS LIST ========== */
    .db-status-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .db-status-row {
        display: grid;
        grid-template-columns: 100px 1fr 40px;
        align-items: center;
        gap: 12px;
    }
    .db-status-info {
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .db-status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .db-status-name {
        font-size: 11.5px;
        font-weight: 700;
        color: #d4d4d8;
    }
    .db-status-bar-wrap {
        height: 22px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 6px;
        overflow: hidden;
    }
    .db-status-bar {
        height: 100%;
        border-radius: 6px;
        transition: width 0.5s ease;
        min-width: 3px;
    }
    .db-status-count {
        font-size: 13px;
        font-weight: 800;
        color: #fafafa;
        text-align: right;
        font-family: ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
    }
    .db-status-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .db-status-total-label {
        font-size: 11px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .db-status-total-value {
        font-size: 20px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
    }

    /* ========== TOP STORES ========== */
    .db-stores-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .db-store-row {
        display: grid;
        grid-template-columns: 26px 1fr 100px 70px;
        gap: 10px;
        align-items: center;
    }
    .db-store-rank {
        width: 24px; height: 24px;
        border-radius: 7px;
        background: rgba(255, 255, 255, 0.04);
        color: #a1a1aa;
        font-size: 11px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        display: grid;
        place-items: center;
    }
    .db-store-rank.gold { background: linear-gradient(135deg, #fbbf24, #d97706); color: #fff; }
    .db-store-rank.silver { background: linear-gradient(135deg, #d4d4d8, #a1a1aa); color: #0f0f14; }
    .db-store-rank.bronze { background: linear-gradient(135deg, #d97706, #92400e); color: #fff; }
    .db-store-info { min-width: 0; }
    .db-store-name {
        font-size: 12px;
        font-weight: 700;
        color: #fafafa;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .db-store-code {
        font-size: 9.5px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        margin-top: 1px;
    }
    .db-store-chart {
        height: 6px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 3px;
        overflow: hidden;
    }
    .db-store-bar {
        height: 100%;
        background: linear-gradient(90deg, #c9a961, #b8944d);
        border-radius: 3px;
        transition: width 0.5s ease;
        min-width: 3px;
    }
    .db-store-amount {
        font-size: 12px;
        font-weight: 800;
        color: #c9a961;
        text-align: right;
        font-family: ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    /* ========== ACTIVITY ========== */
    .db-activity-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .db-activity-item {
        display: grid;
        grid-template-columns: 32px 1fr auto;
        gap: 10px;
        align-items: center;
        padding: 8px 10px;
        border-radius: 9px;
        text-decoration: none;
        transition: all 0.15s;
        border: 1px solid transparent;
    }
    .db-activity-item:hover {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.06);
        transform: translateX(2px);
    }
    .db-activity-marker {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: color-mix(in srgb, var(--accent) 15%, transparent);
        border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent);
        display: grid;
        place-items: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .db-activity-content { min-width: 0; }
    .db-activity-title {
        font-size: 11.5px;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: -0.01em;
    }
    .db-activity-desc {
        font-size: 10.5px;
        color: #71717a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }
    .db-activity-meta { text-align: right; flex-shrink: 0; }
    .db-activity-amount {
        font-size: 12px;
        font-weight: 800;
        color: #c9a961;
        font-family: ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
    }
    .db-activity-time {
        font-size: 9.5px;
        color: #71717a;
        margin-top: 1px;
    }

    /* ========== ALERTS ========== */
    .db-alerts {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .db-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.15s;
        border: 1px solid;
    }
    .db-alert:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -8px rgba(0, 0, 0, 0.5);
    }
    .db-alert-amber {
        background: rgba(245, 158, 11, 0.08);
        border-color: rgba(245, 158, 11, 0.25);
    }
    .db-alert-amber .db-alert-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .db-alert-blue {
        background: rgba(59, 130, 246, 0.08);
        border-color: rgba(59, 130, 246, 0.25);
    }
    .db-alert-blue .db-alert-icon { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .db-alert-red {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.25);
    }
    .db-alert-red .db-alert-icon { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .db-alert-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .db-alert-content { min-width: 0; }
    .db-alert-title {
        font-size: 12.5px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.01em;
    }
    .db-alert-desc {
        font-size: 10.5px;
        color: #a1a1aa;
        margin-top: 2px;
    }

    /* ========== EMPTY ========== */
    .db-empty-small {
        text-align: center;
        padding: 30px 20px;
        color: #71717a;
        font-size: 12px;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1100px) {
        .db-metrics { grid-template-columns: repeat(2, 1fr); }
        .db-2col { grid-template-columns: 1fr; }
        .db-alerts { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .db-greeting-title { font-size: 17px; }
        .db-metrics { grid-template-columns: 1fr; }
        .db-metric-value { font-size: 20px; }
        .db-section { padding: 14px; }
        .db-chart { height: 140px; }
        .db-chart-y-label { font-size: 8.5px; min-width: 40px; }
        .db-status-row { grid-template-columns: 85px 1fr 34px; }
        .db-store-row { grid-template-columns: 24px 1fr 60px 60px; gap: 8px; }
        .db-store-amount { font-size: 11px; }
    }
    @media (max-width: 480px) {
        .db-metrics { grid-template-columns: 1fr 1fr; gap: 8px; }
        .db-metric { padding: 12px 14px; }
        .db-metric-value { font-size: 18px; }
        .db-metric-label { font-size: 9.5px; }
        .db-metric-foot { font-size: 10px; }
        .db-status-row { grid-template-columns: 78px 1fr 30px; gap: 8px; }
        .db-store-row { grid-template-columns: 22px 1fr 55px; }
        .db-store-chart { display: none; }
    }

    /* ========== LINE CHART (Order Status) ========== */
    .db-linechart {
        display: flex;
        gap: 10px;
        height: 180px;
        padding: 4px 0 20px;
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
        color: #71717a;
        font-family: ui-monospace, monospace;
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
    .db-linechart-points {
        position: absolute;
        inset: 0 0 20px 0;
        pointer-events: none;
    }
    .db-point-group {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 0;
    }
    .db-point {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 0 2px rgba(15, 15, 20, 0.9);
        transition: transform 0.15s;
    }
    .db-point:hover {
        transform: translate(-50%, -50%) scale(1.5);
    }
    .db-linechart-x {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
    }
    .db-x-label {
        font-size: 9px;
        color: #71717a;
        font-weight: 700;
        font-family: ui-monospace, monospace;
        text-align: center;
        flex: 1;
    }
    .db-linechart-legend {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        padding-top: 8px;
        margin-top: 4px;
        border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
    .db-legend-line {
        width: 14px;
        height: 3px;
        border-radius: 2px;
        flex-shrink: 0;
    }
    .db-legend-count {
        margin-left: 4px;
        padding: 1px 6px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 4px;
        font-size: 9.5px;
        font-weight: 800;
        color: #d4d4d8;
        font-family: ui-monospace, monospace;
    }

    @media (max-width: 700px) {
        .db-linechart { height: 150px; }
        .db-point { width: 6px; height: 6px; }
        .db-linechart-legend { gap: 10px; }
        .db-legend-line { width: 12px; }
    }
    </style>
@endpush