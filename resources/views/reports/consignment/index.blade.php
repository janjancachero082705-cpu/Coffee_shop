@extends('layouts.admin')

@section('title', 'Sales Reports')
@section('subtitle', 'Monitor store sales performance')

@section('content')

@php
    use App\Models\Store;
    use App\Models\SalesReport;

    $search = request('search');
    $storeId = request('store');
    $statusFilter = request('status');

    $totalSales = (float) SalesReport::sum('total_sales');
    $totalCollected = (float) SalesReport::sum('amount_paid');
    $totalOutstanding = (float) SalesReport::sum('balance');
    $totalReports = SalesReport::count();
    $paidPct = $totalSales > 0 ? ($totalCollected / $totalSales) * 100 : 0;

    $storesQuery = Store::query()->whereHas('salesReports');
    if ($storeId) $storesQuery->where('id', $storeId);
    if ($search) {
        $storesQuery->where(function ($q) use ($search) {
            $q->where('store_name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }
    $stores = $storesQuery->orderBy('store_name')->get();

    $storeGroups = collect();
    foreach ($stores as $store) {
        $reportsQuery = SalesReport::where('store_id', $store->id);
        if ($statusFilter === 'paid') $reportsQuery->where('balance', '<=', 0);
        elseif ($statusFilter === 'partial') $reportsQuery->where('amount_paid', '>', 0)->where('balance', '>', 0);
        elseif ($statusFilter === 'pending') $reportsQuery->where('amount_paid', '<=', 0);

        $reports = $reportsQuery->latest()->get();
        if ($reports->isEmpty()) continue;

        $storeGroups->push([
            'store' => $store,
            'reports' => $reports,
            'total_sales' => $reports->sum('total_sales'),
            'total_paid' => $reports->sum('amount_paid'),
            'total_balance' => $reports->sum('balance'),
            'count' => $reports->count(),
            'latest' => $reports->first()->created_at,
        ]);
    }
    $storeGroups = $storeGroups->sortByDesc('latest')->values();
    $allStores = Store::orderBy('store_name')->get();
@endphp

{{-- ═══════ KPI CARDS ═══════ --}}
<div class="sr-kpis">
    <div class="sr-kpi sr-kpi-primary">
        <div class="sr-kpi-head">
            <div class="sr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3v18h18M7 14l3-3 4 4 6-6"/></svg>
            </div>
            <div class="sr-kpi-label">Total Sales</div>
        </div>
        <div class="sr-kpi-value">₱{{ number_format($totalSales, 0) }}</div>
        <div class="sr-kpi-foot">{{ $totalReports }} report{{ $totalReports !== 1 ? 's' : '' }}</div>
    </div>

    <div class="sr-kpi sr-kpi-green">
        <div class="sr-kpi-head">
            <div class="sr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div class="sr-kpi-label">Total Collected</div>
        </div>
        <div class="sr-kpi-value">₱{{ number_format($totalCollected, 0) }}</div>
        <div class="sr-kpi-foot">{{ number_format($paidPct, 1) }}% paid</div>
    </div>

    <div class="sr-kpi sr-kpi-amber">
        <div class="sr-kpi-head">
            <div class="sr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="sr-kpi-label">Outstanding</div>
        </div>
        <div class="sr-kpi-value">₱{{ number_format($totalOutstanding, 0) }}</div>
        <div class="sr-kpi-foot">Unpaid balance</div>
    </div>

    <div class="sr-kpi sr-kpi-red">
        <div class="sr-kpi-head">
            <div class="sr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16v16H4zM4 10h16M10 4v16"/></svg>
            </div>
            <div class="sr-kpi-label">Pending</div>
        </div>
        <div class="sr-kpi-value">{{ SalesReport::where('amount_paid', 0)->count() }}</div>
        <div class="sr-kpi-foot">Awaiting payment</div>
    </div>
</div>

{{-- ═══════ TOOLBAR ═══════ --}}
<div class="sr-toolbar">
    <div class="sr-toolbar-title">
        <h2 class="sr-title">All Reports</h2>
        <div class="sr-subtitle">
            <span class="sr-grouped-badge">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/></svg>
                Grouped by store
            </span>
            · {{ $storeGroups->count() }} store{{ $storeGroups->count() !== 1 ? 's' : '' }}
            · {{ $totalReports }} total report{{ $totalReports !== 1 ? 's' : '' }}
        </div>
    </div>
</div>

{{-- ═══════ FILTERS ═══════ --}}
<form method="GET" class="sr-filters">
    <div class="sr-search-wrap">
        <svg class="sr-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="text" name="search" class="sr-search" placeholder="Search store name or code..." value="{{ $search }}" autocomplete="off">
    </div>

    <select name="store" class="sr-select">
        <option value="">All Stores</option>
        @foreach($allStores as $s)
            <option value="{{ $s->id }}" {{ $storeId == $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
        @endforeach
    </select>

    <select name="status" class="sr-select">
        <option value="">All Status</option>
        <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="partial" {{ $statusFilter === 'partial' ? 'selected' : '' }}>Partial</option>
        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
    </select>

    <button type="submit" class="sr-filter-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
        </svg>
        Filter
    </button>

    @if($search || $storeId || $statusFilter)
        <a href="{{ route('consignment.reports.index') }}" class="sr-clear-btn">Clear</a>
    @endif
</form>

{{-- ═══════ STORE GROUPS (HORIZONTAL GRID) ═══════ --}}
@if($storeGroups->isEmpty())
    <div class="sr-empty">
        <div class="sr-empty-icon">
            <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M3 3v18h18M7 14l3-3 4 4 6-6"/>
            </svg>
        </div>
        <div class="sr-empty-title">
            @if($search || $storeId || $statusFilter)
                Walay reports match sa filter
            @else
                Walay sales reports yet
            @endif
        </div>
    </div>
@else
    <div class="sr-groups">
        @foreach($storeGroups as $group)
            @php
                $store = $group['store'];
                $reports = $group['reports'];
                $groupPaidPct = $group['total_sales'] > 0 ? ($group['total_paid'] / $group['total_sales']) * 100 : 0;
                $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
            @endphp

            <div class="sr-group" data-store="{{ $store->id }}" id="sr-group-{{ $store->id }}">
                {{-- STORE HEADER --}}
                <div class="sr-group-head" onclick="srToggleGroup({{ $store->id }})">
                    <div class="sr-store-avatar">
                        @if($store->logo_url)
                            <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
                        @else
                            {{ $initials }}
                        @endif
                    </div>

                    <div class="sr-store-info">
                        <div class="sr-store-name">{{ $store->store_name }}</div>
                        <div class="sr-store-meta">
                            <span class="sr-store-code">{{ $store->code ?? '' }}</span>
                            <span class="sr-dot">·</span>
                            <span class="sr-report-count">{{ $group['count'] }} report{{ $group['count'] !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>

                    <button type="button" class="sr-toggle-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                </div>

                {{-- STATS ROWS --}}
                <div class="sr-group-stats">
                    <div class="sr-stat">
                        <div class="sr-stat-lbl">Total Sales</div>
                        <div class="sr-stat-val">₱{{ number_format($group['total_sales'], 0) }}</div>
                    </div>
                    <div class="sr-stat">
                        <div class="sr-stat-lbl">Collected</div>
                        <div class="sr-stat-val green">₱{{ number_format($group['total_paid'], 0) }}</div>
                    </div>
                    <div class="sr-stat">
                        <div class="sr-stat-lbl">Balance</div>
                        <div class="sr-stat-val {{ $group['total_balance'] > 0 ? 'amber' : 'green' }}">₱{{ number_format($group['total_balance'], 0) }}</div>
                    </div>
                </div>

                {{-- PROGRESS --}}
                <div class="sr-group-progress">
                    <div class="sr-group-progress-bar">
                        <div class="sr-group-progress-fill" style="width: {{ $groupPaidPct }}%; {{ $groupPaidPct >= 100 ? 'background: linear-gradient(90deg, #22c55e, #16a34a);' : '' }}"></div>
                    </div>
                    <div class="sr-group-progress-info">
                        <span class="sr-progress-pct">{{ number_format($groupPaidPct, 0) }}% paid</span>
                        <span class="sr-progress-time">{{ $group['latest']->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- REPORTS LIST (Expanded - full width) --}}
                <div class="sr-group-body" id="sr-group-body-{{ $store->id }}">
                    <div class="sr-group-body-head">
                        <div class="sr-body-title">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
                            </svg>
                            Report History — {{ $store->store_name }}
                        </div>
                        <div class="sr-body-count">{{ $reports->count() }} total</div>
                    </div>

                    <div class="sr-reports-list">
                        @foreach($reports as $report)
                            @php
                                $total = (float) ($report->total_sales ?? 0);
                                $paid = (float) ($report->amount_paid ?? 0);
                                $balance = (float) ($report->balance ?? 0);
                                $isPaid = $balance <= 0;
                                $isPartial = $paid > 0 && $balance > 0;
                                $status = $isPaid ? 'paid' : ($isPartial ? 'partial' : 'pending');
                            @endphp

                            <a href="{{ route('consignment.reports.show', $report) }}" class="sr-report-item">
                                <div class="sr-report-badge sr-badge-{{ $status }}">
                                    <span class="sr-badge-dot"></span>
                                    {{ ucfirst($status) }}
                                </div>

                                <div class="sr-report-info">
                                    <div class="sr-report-num">{{ $report->report_number }}</div>
                                    <div class="sr-report-date">{{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y · g:i A') }}</div>
                                </div>

                                <div class="sr-report-metrics">
                                    <div class="sr-metric">
                                        <div class="sr-metric-lbl">Total</div>
                                        <div class="sr-metric-val">₱{{ number_format($total, 2) }}</div>
                                    </div>
                                    <div class="sr-metric">
                                        <div class="sr-metric-lbl">Paid</div>
                                        <div class="sr-metric-val green">₱{{ number_format($paid, 2) }}</div>
                                    </div>
                                    <div class="sr-metric">
                                        <div class="sr-metric-lbl">Balance</div>
                                        <div class="sr-metric-val {{ $balance > 0 ? 'amber' : 'green' }}">₱{{ number_format($balance, 2) }}</div>
                                    </div>
                                </div>

                                <div class="sr-report-items">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    {{ $report->items?->count() ?? 0 }}
                                </div>

                                <svg class="sr-report-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M9 18l6-6-6-6"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection

@push('styles')
<style>
    /* KPI */
    .sr-kpis {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .sr-kpi {
        position: relative;
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .sr-kpi::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
    }
    .sr-kpi-primary::before { background: linear-gradient(90deg, #c9a961, transparent); }
    .sr-kpi-green::before { background: linear-gradient(90deg, #22c55e, transparent); }
    .sr-kpi-amber::before { background: linear-gradient(90deg, #f59e0b, transparent); }
    .sr-kpi-red::before { background: linear-gradient(90deg, #ef4444, transparent); }
    .sr-kpi:hover { transform: translateY(-2px); border-color: rgba(201, 169, 97, 0.25); }
    .sr-kpi-head { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .sr-kpi-icon {
        width: 32px; height: 32px; border-radius: 9px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .sr-kpi-primary .sr-kpi-icon { background: rgba(201, 169, 97, 0.15); color: #c9a961; }
    .sr-kpi-green .sr-kpi-icon { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .sr-kpi-amber .sr-kpi-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .sr-kpi-red .sr-kpi-icon { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .sr-kpi-label {
        font-size: 10.5px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
    }
    .sr-kpi-value {
        font-size: 24px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.03em; line-height: 1;
        font-variant-numeric: tabular-nums; margin-bottom: 6px;
    }
    .sr-kpi-foot { font-size: 11px; color: #71717a; }

    /* TOOLBAR */
    .sr-toolbar { margin-bottom: 14px; }
    .sr-title {
        font-size: 20px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.02em; margin-bottom: 4px;
    }
    .sr-subtitle {
        font-size: 12px; color: #71717a;
        display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    }
    .sr-grouped-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 9px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        border-radius: 100px;
        font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.06em;
        border: 1px solid rgba(201, 169, 97, 0.25);
    }

    /* FILTERS */
    .sr-filters {
        display: flex; gap: 10px;
        margin-bottom: 20px;
        flex-wrap: nowrap; align-items: center;
        padding: 12px 14px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
        overflow-x: auto;
    }
    .sr-search-wrap {
        position: relative;
        flex: 1 1 200px;
        min-width: 160px;
        max-width: 400px;
    }
    .sr-search-icon {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%);
        color: #71717a; pointer-events: none;
    }
    .sr-search {
        width: 100%;
        padding: 11px 14px 11px 40px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa; font-size: 13px;
        font-family: inherit; transition: all 0.18s;
    }
    .sr-search:focus {
        outline: none; border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
    }
    .sr-search::placeholder { color: #52525b; }
    .sr-select {
        flex: 0 0 auto;
        padding: 11px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa; font-size: 12.5px;
        font-weight: 600; font-family: inherit;
        cursor: pointer; transition: all 0.18s;
        min-width: 130px;
    }
    .sr-select:focus {
        outline: none; border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
    }
    .sr-filter-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 11px 18px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border: none; border-radius: 11px;
        color: #0f0f14; font-size: 12.5px;
        font-weight: 800; cursor: pointer;
        font-family: inherit; transition: all 0.15s;
        flex-shrink: 0;
    }
    .sr-filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.6);
    }
    .sr-clear-btn {
        padding: 11px 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 11px;
        color: #d4d4d8; font-size: 12.5px;
        font-weight: 700; text-decoration: none;
        flex-shrink: 0;
    }
    .sr-clear-btn:hover { background: rgba(255, 255, 255, 0.08); color: #fafafa; }

    /* ═══════════ STORE GROUPS — HORIZONTAL GRID ═══════════ */
    .sr-groups {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        align-items: start;
    }

    .sr-group {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.22s;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .sr-group:hover {
        border-color: rgba(201, 169, 97, 0.3);
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.6);
        transform: translateY(-2px);
    }

    /* EXPANDED — full width */
    .sr-group.expanded {
        grid-column: 1 / -1;
        border-color: rgba(201, 169, 97, 0.4);
        box-shadow: 0 16px 40px -12px rgba(0, 0, 0, 0.7);
        transform: none;
    }

    /* STORE HEADER (compact for grid) */
    .sr-group-head {
        display: grid;
        grid-template-columns: 44px 1fr 32px;
        gap: 12px;
        padding: 16px;
        align-items: center;
        cursor: pointer;
        transition: background 0.15s;
    }
    .sr-group-head:hover { background: rgba(255, 255, 255, 0.02); }

    .sr-store-avatar {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        display: grid; place-items: center;
        font-size: 14px; font-weight: 900;
        letter-spacing: -0.02em;
        flex-shrink: 0; overflow: hidden;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }
    .sr-store-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .sr-store-info { min-width: 0; }
    .sr-store-name {
        font-size: 14px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.02em;
        margin-bottom: 3px;
        white-space: nowrap; overflow: hidden;
        text-overflow: ellipsis;
    }
    .sr-store-meta {
        display: flex; align-items: center; gap: 5px;
        font-size: 10.5px; color: #71717a;
        flex-wrap: wrap;
    }
    .sr-store-code { font-family: ui-monospace, monospace; }
    .sr-dot { color: #52525b; }
    .sr-report-count { color: #c9a961; font-weight: 800; }

    .sr-toggle-btn {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #a1a1aa;
        display: grid; place-items: center;
        cursor: pointer;
        transition: all 0.25s;
        flex-shrink: 0;
    }
    .sr-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
        border-color: rgba(201, 169, 97, 0.3);
    }
    .sr-toggle-btn.open {
        background: rgba(201, 169, 97, 0.15);
        color: #c9a961;
        border-color: rgba(201, 169, 97, 0.4);
    }
    .sr-toggle-btn svg { transition: transform 0.25s; }
    .sr-toggle-btn.open svg { transform: rotate(180deg); }

    /* STATS (stacked vertical in card) */
    .sr-group-stats {
        padding: 0 16px 14px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .sr-stat {
        padding: 10px;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 10px;
        text-align: center;
    }
    .sr-stat-lbl {
        font-size: 8.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .sr-stat-val {
        font-size: 13px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        white-space: nowrap;
    }
    .sr-stat-val.green { color: #22c55e; }
    .sr-stat-val.amber { color: #f59e0b; }

    /* PROGRESS */
    .sr-group-progress { padding: 0 16px 16px; }
    .sr-group-progress-bar {
        height: 6px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 8px;
    }
    .sr-group-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #c9a961, #d4b673);
        border-radius: 3px;
        transition: width 0.4s;
        min-width: 3px;
    }
    .sr-group-progress-info {
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        font-weight: 600;
    }
    .sr-progress-pct { color: #c9a961; font-weight: 800; }
    .sr-progress-time { color: #71717a; }

    /* REPORTS LIST (expanded) */
    .sr-group-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .sr-group-body.open { max-height: 4000px; }

    .sr-group-body-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .sr-body-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        font-weight: 800;
        color: #c9a961;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .sr-body-count {
        font-size: 11px;
        color: #71717a;
        font-weight: 700;
    }

    .sr-reports-list {
        padding: 14px 20px 18px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 10px;
    }

    .sr-report-item {
        display: grid;
        grid-template-columns: 90px 1fr auto 50px 20px;
        gap: 14px;
        align-items: center;
        padding: 12px 14px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.18s;
    }
    .sr-report-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(201, 169, 97, 0.25);
        transform: translateX(4px);
    }

    .sr-report-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border: 1px solid;
        justify-content: center;
    }
    .sr-badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
    .sr-badge-paid {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.3);
    }
    .sr-badge-paid .sr-badge-dot { background: #22c55e; }
    .sr-badge-partial {
        background: rgba(59, 130, 246, 0.12);
        color: #3b82f6;
        border-color: rgba(59, 130, 246, 0.3);
    }
    .sr-badge-partial .sr-badge-dot { background: #3b82f6; }
    .sr-badge-pending {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.3);
    }
    .sr-badge-pending .sr-badge-dot { background: #f59e0b; }

    .sr-report-info { min-width: 0; }
    .sr-report-num {
        font-size: 12.5px;
        font-weight: 800;
        color: #fafafa;
        font-family: ui-monospace, monospace;
        margin-bottom: 2px;
    }
    .sr-report-date { font-size: 10.5px; color: #71717a; }

    .sr-report-metrics { display: flex; gap: 12px; }
    .sr-metric { text-align: right; min-width: 60px; }
    .sr-metric-lbl {
        font-size: 8.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 2px;
    }
    .sr-metric-val {
        font-size: 11.5px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .sr-metric-val.green { color: #22c55e; }
    .sr-metric-val.amber { color: #f59e0b; }

    .sr-report-items {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10.5px;
        color: #71717a;
        font-weight: 700;
        padding: 3px 8px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 100px;
        justify-self: center;
    }

    .sr-report-arrow {
        color: #52525b;
        transition: all 0.15s;
    }
    .sr-report-item:hover .sr-report-arrow {
        color: #c9a961;
        transform: translateX(2px);
    }

    /* EMPTY */
    .sr-empty {
        text-align: center;
        padding: 80px 24px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
    }
    .sr-empty-icon {
        width: 88px; height: 88px;
        margin: 0 auto 20px;
        border-radius: 24px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.5);
    }
    .sr-empty-title {
        font-size: 18px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
    }

    /* ═══════════ RESPONSIVE ═══════════ */
    @media (max-width: 1400px) {
        .sr-groups {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    @media (max-width: 1100px) {
        .sr-kpis { grid-template-columns: repeat(2, 1fr); }
        .sr-groups { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 700px) {
        .sr-kpis { grid-template-columns: 1fr; }
        .sr-groups { grid-template-columns: 1fr; }
        .sr-filters { flex-wrap: wrap; }
        .sr-search-wrap { flex: 1 1 100%; max-width: 100%; }
        .sr-reports-list { grid-template-columns: 1fr; padding: 12px 14px; }
        .sr-report-item {
            grid-template-columns: 1fr auto;
            gap: 10px;
            padding: 10px;
        }
        .sr-report-badge, .sr-report-items { display: none; }
        .sr-report-metrics { flex-direction: column; gap: 2px; align-items: flex-end; }
        .sr-metric { min-width: 55px; }
        .sr-group-head { padding: 14px; }
        .sr-group-stats { padding: 0 14px 12px; gap: 6px; }
        .sr-stat { padding: 8px 6px; }
        .sr-stat-val { font-size: 11px; }
        .sr-group-progress { padding: 0 14px 14px; }
    }
</style>
@endpush

@push('scripts')
<script>
    function srToggleGroup(id) {
        var body = document.getElementById('sr-group-body-' + id);
        var group = document.getElementById('sr-group-' + id);
        if (!body || !group) return;
        var btn = group.querySelector('.sr-toggle-btn');
        body.classList.toggle('open');
        btn.classList.toggle('open');
        group.classList.toggle('expanded');
    }
</script>
@endpush