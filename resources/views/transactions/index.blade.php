@extends('layouts.admin')

@section('title', 'Transactions')
@section('subtitle', 'Real-time activity log sa sistema')

@section('content')

@php
    use App\Models\Store;
    use App\Models\ConsignmentPayment;
    use App\Models\DeliveryReceipt;
    use App\Models\SalesReport;
    use App\Models\ReorderRequest;

    // Filter
    $search = request('search');
    $storeId = request('store');
    $dateFrom = request('from');
    $dateTo = request('to');

    // Stats
    $todayPayments = ConsignmentPayment::verified()->whereDate('created_at', today())->count();
    $weekPayments = ConsignmentPayment::verified()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
    $todayPaid = (float) ConsignmentPayment::verified()->whereDate('created_at', today())->sum('amount');

    // ═══ GROUP ACTIVITY BY STORE ═══
    $storesQuery = Store::query();

    if ($storeId) {
        $storesQuery->where('id', $storeId);
    }

    if ($search) {
        $storesQuery->where(function ($q) use ($search) {
            $q->where('store_name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    $stores = $storesQuery->orderBy('store_name')->get();

    // Build activity per store
    $storeActivities = collect();

    foreach ($stores as $store) {
        $activities = collect();

        // 1. Orders
        $orderQuery = ReorderRequest::where('store_id', $store->id);
        if ($dateFrom) $orderQuery->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo) $orderQuery->whereDate('created_at', '<=', $dateTo);
        foreach ($orderQuery->latest()->take(20)->get() as $o) {
            $activities->push([
                'type' => 'order',
                'icon' => 'shopping-cart',
                'color' => '#f59e0b',
                'label' => 'Order ' . ucfirst($o->status),
                'ref' => $o->request_number,
                'url' => route('reorder-requests.show', $o->id),
                'amount' => (float) ($o->total_amount ?? 0),
                'time' => $o->created_at,
                'desc' => match($o->status) {
                    'pending' => 'Gi-submit sa store',
                    'approved' => 'Gi-approve sa admin',
                    'rejected' => 'Gi-reject sa admin',
                    default => ucfirst($o->status),
                },
            ]);
        }

        // 2. Deliveries
        $delQuery = DeliveryReceipt::where('store_id', $store->id);
        if ($dateFrom) $delQuery->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo) $delQuery->whereDate('created_at', '<=', $dateTo);
        foreach ($delQuery->latest()->take(20)->get() as $d) {
            $activities->push([
                'type' => 'delivery',
                'icon' => 'truck',
                'color' => '#3b82f6',
                'label' => 'Delivery',
                'ref' => $d->dr_number,
                'url' => route('deliveries.show', $d->id),
                'amount' => (float) ($d->total_amount ?? 0),
                'time' => $d->created_at,
                'desc' => $d->customer_confirmed ? 'Gi-confirm na sa customer' : ($d->out_for_delivery_at ? 'Gi-ship na' : 'Gi-create'),
            ]);
        }

        // 3. Payments — VERIFIED ONLY
        $payQuery = ConsignmentPayment::verified()->where('store_id', $store->id);
        if ($dateFrom) $payQuery->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo) $payQuery->whereDate('created_at', '<=', $dateTo);
        foreach ($payQuery->latest()->take(20)->get() as $p) {
            $activities->push([
                'type' => 'payment',
                'icon' => 'cash',
                'color' => '#22c55e',
                'label' => 'Payment Recorded',
                'ref' => $p->payment_number,
                'url' => route('consignment.payments.show', $p->id),
                'amount' => (float) ($p->amount ?? 0),
                'time' => $p->created_at,
                'desc' => ucfirst($p->method) . ' · Verified',
            ]);
        }

        // 4. Sales Reports
        $srQuery = SalesReport::where('store_id', $store->id);
        if ($dateFrom) $srQuery->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo) $srQuery->whereDate('created_at', '<=', $dateTo);
        foreach ($srQuery->latest()->take(20)->get() as $sr) {
            $activities->push([
                'type' => 'report',
                'icon' => 'file-text',
                'color' => '#8b5cf6',
                'label' => 'Sales Report',
                'ref' => $sr->report_number,
                'url' => route('consignment.reports.show', $sr),
                'amount' => (float) ($sr->total_sales ?? 0),
                'time' => $sr->created_at,
                'desc' => 'Sales report created',
            ]);
        }

        // Sort by time desc + take top 10
        $activities = $activities->sortByDesc('time')->take(10)->values();

        if ($activities->count() > 0) {
            $storeActivities->push([
                'store' => $store,
                'activities' => $activities,
                'total_amount' => $activities->sum('amount'),
                'count' => $activities->count(),
            ]);
        }
    }

    // Sort stores by latest activity
    $storeActivities = $storeActivities->sortByDesc(function ($item) {
        return $item['activities']->first()['time'];
    })->values();

    $allStores = Store::orderBy('store_name')->get();
@endphp

{{-- ═══════ HERO ═══════ --}}
<div class="tx-hero">
    <div class="tx-hero-left">
        <div class="tx-hero-eyebrow">
            <span class="tx-pulse"></span>
            LIVE ACTIVITY FEED
        </div>
        <h1 class="tx-hero-title">Transactions</h1>
        <p class="tx-hero-desc">Tanang events — orders, deliveries, payments, reports — group by store</p>
    </div>
    <div class="tx-hero-stats">
        <div class="tx-hero-stat">
            <div class="tx-hero-stat-label">Today</div>
            <div class="tx-hero-stat-value">{{ $todayPayments }}</div>
        </div>
        <div class="tx-hero-stat-divider"></div>
        <div class="tx-hero-stat">
            <div class="tx-hero-stat-label">This Week</div>
            <div class="tx-hero-stat-value">{{ $weekPayments }}</div>
        </div>
        <div class="tx-hero-stat-divider"></div>
        <div class="tx-hero-stat">
            <div class="tx-hero-stat-label">Paid Today</div>
            <div class="tx-hero-stat-value gold">₱{{ number_format($todayPaid, 0) }}</div>
        </div>
    </div>
</div>

{{-- ═══════ FILTERS ═══════ --}}
<form method="GET" class="tx-filters">
    <div class="tx-search-wrap">
        <svg class="tx-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="text" name="search" class="tx-search" placeholder="Search store name or code..." value="{{ $search }}" autocomplete="off">
    </div>

    <select name="store" class="tx-select">
        <option value="">All Stores</option>
        @foreach($allStores as $s)
            <option value="{{ $s->id }}" {{ $storeId == $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
        @endforeach
    </select>

    <input type="date" name="from" class="tx-select" value="{{ $dateFrom }}" placeholder="From">
    <input type="date" name="to" class="tx-select" value="{{ $dateTo }}" placeholder="To">

    <button type="submit" class="tx-filter-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
        </svg>
        Filter
    </button>

    @if($search || $storeId || $dateFrom || $dateTo)
        <a href="{{ route('transactions.index') }}" class="tx-clear-btn">Clear</a>
    @endif
</form>

{{-- ═══════ STORE CARDS ═══════ --}}
@if($storeActivities->isEmpty())
    <div class="tx-empty">
        <div class="tx-empty-icon">
            <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/>
            </svg>
        </div>
        <div class="tx-empty-title">Walay Transactions</div>
        <div class="tx-empty-desc">
            @if($search || $storeId)
                Walay activity nga match sa imong filter
            @else
                Wala pay activity sa mga store
            @endif
        </div>
    </div>
@else
    <div class="tx-stores">
        @foreach($storeActivities as $item)
            @php
                $store = $item['store'];
                $activities = $item['activities'];
                $latestTime = $activities->first()['time'];
                $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
            @endphp

            <div class="tx-store-card" data-store="{{ $store->id }}">
                {{-- STORE HEADER --}}
                <div class="tx-store-head">
                    <div class="tx-store-avatar">
                        @if($store->logo_url)
                            <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
                        @else
                            {{ $initials }}
                        @endif
                    </div>

                    <div class="tx-store-info">
                        <div class="tx-store-name">{{ $store->store_name }}</div>
                        <div class="tx-store-meta">
                            <span class="tx-store-code">{{ $store->code ?? '' }}</span>
                            <span class="tx-store-dot">·</span>
                            <span class="tx-store-count">{{ $item['count'] }} event{{ $item['count'] !== 1 ? 's' : '' }}</span>
                            <span class="tx-store-dot">·</span>
                            <span class="tx-store-time">{{ $latestTime->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="tx-store-summary">
                        <div class="tx-store-summary-label">Recent Total</div>
                        <div class="tx-store-summary-value">₱{{ number_format($item['total_amount'], 2) }}</div>
                    </div>

                    <button type="button" class="tx-store-toggle" onclick="txToggleStore({{ $store->id }}, this)">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                </div>

                {{-- ACTIVITY LIST (COLLAPSED BY DEFAULT) --}}
                <div class="tx-store-body" id="tx-store-body-{{ $store->id }}">
                    <div class="tx-timeline">
                        @foreach($activities as $act)
                            <a href="{{ $act['url'] }}" class="tx-item" style="--accent: {{ $act['color'] }};">
                                <div class="tx-item-marker">
                                    @if($act['icon'] === 'shopping-cart')
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                                    @elseif($act['icon'] === 'truck')
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                    @elseif($act['icon'] === 'cash')
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                                    @else
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                                    @endif
                                </div>

                                <div class="tx-item-body">
                                    <div class="tx-item-label" style="color: {{ $act['color'] }};">{{ $act['label'] }}</div>
                                    <div class="tx-item-desc">{{ $act['desc'] }}</div>
                                </div>

                                <div class="tx-item-meta">
                                    <div class="tx-item-ref">{{ $act['ref'] }}</div>
                                    <div class="tx-item-time">{{ $act['time']->format('g:i A') }}</div>
                                </div>

                                @if($act['amount'] > 0)
                                    <div class="tx-item-amount">₱{{ number_format($act['amount'], 2) }}</div>
                                @endif

                                <svg class="tx-item-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
    /* ═══════════════════════════════════════════════ */
    /* TRANSACTIONS — GROUP BY STORE */
    /* ═══════════════════════════════════════════════ */

    /* HERO */
    .tx-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 22px 26px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, rgba(30, 26, 22, 0.85), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 18px;
        flex-wrap: wrap;
    }
    .tx-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 10px;
        font-weight: 800;
        color: #22c55e;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .tx-pulse {
        width: 7px; height: 7px;
        background: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        animation: txPulse 2s ease-in-out infinite;
    }
    @keyframes txPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .tx-hero-title {
        font-size: 24px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.03em;
        line-height: 1.1;
        margin-bottom: 6px;
    }
    .tx-hero-desc {
        font-size: 12.5px;
        color: #71717a;
    }
    .tx-hero-stats {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 12px 20px;
        background: rgba(0, 0, 0, 0.25);
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .tx-hero-stat { text-align: right; }
    .tx-hero-stat-label {
        font-size: 9.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 4px;
    }
    .tx-hero-stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .tx-hero-stat-value.gold { color: #c9a961; }
    .tx-hero-stat-divider {
        width: 1px;
        height: 32px;
        background: rgba(255, 255, 255, 0.06);
    }

    /* FILTERS */
    .tx-filters {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: nowrap;
        align-items: center;
        padding: 12px 14px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
        overflow-x: auto;
    }
    .tx-search-wrap {
        position: relative;
        flex: 1 1 200px;
        min-width: 180px;
        max-width: 400px;
    }
    .tx-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #71717a;
        pointer-events: none;
    }
    .tx-search {
        width: 100%;
        padding: 11px 14px 11px 40px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa;
        font-size: 13px;
        font-family: inherit;
        transition: all 0.18s;
    }
    .tx-search:focus {
        outline: none;
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
        background: rgba(201, 169, 97, 0.03);
    }
    .tx-search::placeholder { color: #52525b; }

    .tx-select {
        flex: 0 0 auto;
        padding: 11px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa;
        font-size: 12.5px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.18s;
        min-width: 140px;
    }
    .tx-select:focus {
        outline: none;
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
    }

    .tx-filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 18px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border: none;
        border-radius: 11px;
        color: #0f0f14;
        font-size: 12.5px;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .tx-filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.6);
    }

    .tx-clear-btn {
        padding: 11px 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 11px;
        color: #d4d4d8;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .tx-clear-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }

    /* STORE CARDS */
    .tx-stores {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .tx-store-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.22s;
    }
    .tx-store-card:hover {
        border-color: rgba(201, 169, 97, 0.25);
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.6);
    }

    .tx-store-head {
        display: grid;
        grid-template-columns: 52px 1fr auto 40px;
        gap: 16px;
        padding: 18px 20px;
        align-items: center;
        cursor: pointer;
    }

    .tx-store-avatar {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        display: grid;
        place-items: center;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: -0.02em;
        flex-shrink: 0;
        overflow: hidden;
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.5);
    }
    .tx-store-avatar img {
        width: 100%; height: 100%;
        object-fit: cover;
    }

    .tx-store-info { min-width: 0; }
    .tx-store-name {
        font-size: 15px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tx-store-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        color: #71717a;
        flex-wrap: wrap;
    }
    .tx-store-code { font-family: ui-monospace, monospace; }
    .tx-store-dot { color: #52525b; }
    .tx-store-count {
        color: #c9a961;
        font-weight: 800;
    }

    .tx-store-summary {
        text-align: right;
        padding-left: 20px;
        border-left: 1px solid rgba(255, 255, 255, 0.06);
    }
    .tx-store-summary-label {
        font-size: 9.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 4px;
    }
    .tx-store-summary-value {
        font-size: 17px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    .tx-store-toggle {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #a1a1aa;
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .tx-store-toggle:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
        border-color: rgba(201, 169, 97, 0.3);
    }
    .tx-store-toggle.open {
        background: rgba(201, 169, 97, 0.15);
        color: #c9a961;
        border-color: rgba(201, 169, 97, 0.4);
    }
    .tx-store-toggle.open svg {
        transform: rotate(180deg);
    }
    .tx-store-toggle svg {
        transition: transform 0.25s;
    }

    .tx-store-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(0, 0, 0, 0.15);
    }
    .tx-store-body.open {
        max-height: 2000px;
    }

    /* TIMELINE */
    .tx-timeline {
        padding: 12px 20px 20px;
        position: relative;
    }
    .tx-timeline::before {
        content: '';
        position: absolute;
        left: 37px;
        top: 20px;
        bottom: 20px;
        width: 2px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), transparent);
    }

    .tx-item {
        display: grid;
        grid-template-columns: 40px 1fr auto auto auto;
        gap: 14px;
        align-items: center;
        padding: 12px 14px;
        margin-left: -6px;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.18s;
        position: relative;
        z-index: 1;
    }
    .tx-item + .tx-item {
        margin-top: 2px;
    }
    .tx-item:hover {
        background: rgba(255, 255, 255, 0.03);
        transform: translateX(4px);
    }

    .tx-item-marker {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: color-mix(in srgb, var(--accent) 15%, transparent);
        border: 1px solid color-mix(in srgb, var(--accent) 35%, transparent);
        color: var(--accent);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .tx-item-body { min-width: 0; }
    .tx-item-label {
        font-size: 12.5px;
        font-weight: 800;
        margin-bottom: 2px;
        letter-spacing: -0.01em;
    }
    .tx-item-desc {
        font-size: 11px;
        color: #71717a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tx-item-meta {
        text-align: right;
        font-size: 10.5px;
        min-width: 110px;
    }
    .tx-item-ref {
        color: #a1a1aa;
        font-family: ui-monospace, monospace;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .tx-item-time {
        color: #52525b;
        font-weight: 600;
    }

    .tx-item-amount {
        font-size: 13px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        min-width: 90px;
        text-align: right;
    }

    .tx-item-arrow {
        color: #52525b;
        flex-shrink: 0;
    }
    .tx-item:hover .tx-item-arrow {
        color: #c9a961;
        transform: translateX(2px);
    }

    /* EMPTY */
    .tx-empty {
        text-align: center;
        padding: 80px 24px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
    }
    .tx-empty-icon {
        width: 88px; height: 88px;
        margin: 0 auto 20px;
        border-radius: 24px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        display: grid;
        place-items: center;
        color: rgba(201, 169, 97, 0.5);
    }
    .tx-empty-title {
        font-size: 18px;
        font-weight: 800;
        color: #fafafa;
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }
    .tx-empty-desc {
        font-size: 13px;
        color: #71717a;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .tx-filters {
            flex-wrap: wrap;
        }
        .tx-search-wrap {
            flex: 1 1 100%;
            max-width: 100%;
        }
        .tx-store-head {
            grid-template-columns: 44px 1fr 40px;
            gap: 12px;
            padding: 14px 16px;
        }
        .tx-store-summary {
            display: none;
        }
        .tx-store-avatar {
            width: 44px; height: 44px;
            font-size: 14px;
        }
        .tx-store-name { font-size: 13.5px; }
        .tx-item {
            grid-template-columns: 36px 1fr auto;
            gap: 10px;
            padding: 10px;
        }
        .tx-item-meta { display: none; }
        .tx-item-amount { min-width: 70px; font-size: 12px; }
        .tx-timeline::before { display: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    function txToggleStore(id, btn) {
        var body = document.getElementById('tx-store-body-' + id);
        if (!body) return;
        body.classList.toggle('open');
        btn.classList.toggle('open');
    }

    // Auto-expand kung 1 ra ka store
    document.addEventListener('DOMContentLoaded', function() {
        var cards = document.querySelectorAll('.tx-store-card');
        if (cards.length === 1) {
            var body = cards[0].querySelector('.tx-store-body');
            var btn = cards[0].querySelector('.tx-store-toggle');
            if (body) body.classList.add('open');
            if (btn) btn.classList.add('open');
        }
    });

    // Click header to toggle
    document.addEventListener('click', function(e) {
        var head = e.target.closest('.tx-store-head');
        if (head && !e.target.closest('.tx-store-toggle')) {
            var card = head.closest('.tx-store-card');
            var id = card.dataset.store;
            var btn = card.querySelector('.tx-store-toggle');
            txToggleStore(id, btn);
        }
    });
</script>
@endpush