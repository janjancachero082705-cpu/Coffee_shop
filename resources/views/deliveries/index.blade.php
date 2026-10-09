@extends('layouts.admin')

@section('title', 'Deliveries')
@section('subtitle', 'Tanan nga delivery receipts')

@section('actions')
    <a href="{{ route('deliveries.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        New Delivery
    </a>
@endsection

@section('content')

{{-- ========== SUMMARY CARDS ========== --}}
<div class="summary-grid">
    <div class="summary">
        <div class="summary-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['total'] }}</div>
            <div class="summary-label">Total</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['pending'] }}</div>
            <div class="summary-label">Pending</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['out_for_delivery'] ?? 0 }}</div>
            <div class="summary-label">In Transit</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['delivered'] ?? 0 }}</div>
            <div class="summary-label">Delivered</div>
        </div>
    </div>
</div>

{{-- ========== TABS ========== --}}
<div class="tabs">
    @php
        $tabList = [
            'all'              => 'All Deliveries',
            'pending'          => 'Pending',
            'out_for_delivery' => 'In Transit',
            'delivered'        => 'Delivered',
            'unpaid'           => 'With Balance',
        ];
    @endphp
    @foreach($tabList as $key => $label)
        <a href="{{ route('deliveries.index', array_merge(request()->except(['tab', 'page']), ['tab' => $key])) }}"
           class="tab {{ $tab === $key ? 'active' : '' }}">
            <span>{{ $label }}</span>
            <span class="tab-count">{{ $tabCounts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

{{-- ========== FILTERS ========== --}}
<div class="toolbar">
    <form method="GET" class="toolbar-form">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="search-box">
            <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR #, store, or reference..." class="search-input">
        </div>

        <select name="store" class="filter-select">
            <option value="">All Stores</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}" {{ request('store') == $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
            @endforeach
        </select>

        <input type="date" name="from" value="{{ request('from') }}" class="filter-select" placeholder="From date">
        <input type="date" name="to" value="{{ request('to') }}" class="filter-select" placeholder="To date">

        <button type="submit" class="btn-filter">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>

        @if(request()->hasAny(['search', 'store', 'from', 'to']))
            <a href="{{ route('deliveries.index', ['tab' => $tab]) }}" class="btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif

        <div class="view-toggle">
            <button type="button" class="view-btn active" data-view="list" title="List View">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </button>
            <button type="button" class="view-btn" data-view="table" title="Compact Table">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="18"/><rect x="14" y="3" width="7" height="18"/></svg>
            </button>
        </div>
    </form>
</div>

{{-- ========== DELIVERIES LIST ========== --}}
@if($deliveries->isEmpty())
    <div class="card">
        <div class="empty">
            <div class="empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="empty-title">Walay deliveries</div>
            <div class="empty-text">
                @if(request()->hasAny(['search', 'store', 'from', 'to']) || $tab !== 'all')
                    Try adjusting your filters or tab
                @else
                    Create your first delivery receipt to get started
                @endif
            </div>
        </div>
    </div>
@else
    {{-- LIST VIEW (Grouped by Store) --}}
@php
    $grouped = $deliveries->groupBy('store_id');
@endphp

<div id="listView" class="view-content">
    <div class="store-groups">
        @foreach($grouped as $storeId => $storeDeliveries)
            @php
                $store = $storeDeliveries->first()->store;
                $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
                $storeTotal = $storeDeliveries->sum('total_amount');
                $storeBalance = $storeDeliveries->sum('balance');
                $storePending = $storeDeliveries->filter(fn($d) => !($d->customer_confirmed ?? false))->count();
                $storeDelivered = $storeDeliveries->filter(fn($d) => $d->customer_confirmed ?? false)->count();
                $latest = $storeDeliveries->sortByDesc('created_at')->first();
            @endphp

            <div class="store-group" id="sg-{{ $storeId }}" data-store="{{ $storeId }}">
                {{-- STORE HEADER (clickable) --}}
                <div class="store-group-head" data-toggle-group="{{ $storeId }}">
                    <div class="store-avatar">
                        @if($store->logo_url ?? null)
                            <img src="{{ $store->logo_url }}" alt="">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="store-info">
                        <div class="store-name">{{ $store->store_name ?? 'Unknown Store' }}</div>
                        <div class="store-meta">
                            <span class="store-code">{{ $store->code ?? '' }}</span>
                            <span class="store-dot">·</span>
                            <span class="store-count">{{ $storeDeliveries->count() }} deliveries</span>
                            <span class="store-dot">·</span>
                            <span class="store-time">{{ $latest->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="store-stats">
                        <div class="store-stat">
                            <div class="store-stat-lbl">Total</div>
                            <div class="store-stat-val gold">&#8369;{{ number_format($storeTotal, 0) }}</div>
                        </div>
                        @if($storeBalance > 0)
                            <div class="store-stat">
                                <div class="store-stat-lbl">Balance</div>
                                <div class="store-stat-val amber">&#8369;{{ number_format($storeBalance, 0) }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="store-chevron">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </div>
                </div>

                {{-- STORE BODY (deliveries list) --}}
                <div class="store-group-body" id="sgb-{{ $storeId }}">
                    {{-- Mini stats --}}
                    <div class="store-body-stats">
                        <div class="store-body-stat">
                            <span class="store-body-stat-lbl">Delivered</span>
                            <span class="store-body-stat-val green">{{ $storeDelivered }}</span>
                        </div>
                        <div class="store-body-stat">
                            <span class="store-body-stat-lbl">Pending</span>
                            <span class="store-body-stat-val amber">{{ $storePending }}</span>
                        </div>
                        <div class="store-body-stat">
                            <span class="store-body-stat-lbl">Total</span>
                            <span class="store-body-stat-val">{{ $storeDeliveries->count() }}</span>
                        </div>
                    </div>

                    {{-- Deliveries --}}
                    <div class="delivery-list">
                        @foreach($storeDeliveries as $dr)
                            @php
                                $isConfirmed = $dr->customer_confirmed ?? false;
                                $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                                $statusColor = $isConfirmed ? '#22c55e' : ($isOut ? '#3b82f6' : '#f59e0b');
                                $statusLabel = $isConfirmed ? 'Delivered' : ($isOut ? 'In Transit' : 'Pending');
                            @endphp
                            <div class="delivery-card" style="--status-color: {{ $statusColor }};">
                                <a href="{{ route('deliveries.show', $dr) }}" class="delivery-card-body">
                                    <div class="delivery-card-head">
                                        <div class="delivery-card-left">
                                            <div class="delivery-status-dot" style="background:{{ $statusColor }};"></div>
                                            <div>
                                                <div class="delivery-card-title">{{ $dr->dr_number }}</div>
                                                <div class="delivery-card-sub">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                                            </div>
                                        </div>
                                        <span class="status-badge" style="background:{{ $statusColor }}22; color:{{ $statusColor }}; border-color:{{ $statusColor }}55;">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <div class="delivery-card-meta">
                                        <div class="meta-item">
                                            <div class="meta-label">Items</div>
                                            <div class="meta-value">{{ $dr->items->count() }}</div>
                                        </div>
                                        <div class="meta-item">
                                            <div class="meta-label">Total</div>
                                            <div class="meta-value gold">&#8369;{{ number_format($dr->total_amount, 2) }}</div>
                                        </div>
                                        <div class="meta-item">
                                            <div class="meta-label">Balance</div>
                                            <div class="meta-value {{ $dr->balance > 0 ? 'amber' : 'green' }}">&#8369;{{ number_format($dr->balance, 2) }}</div>
                                        </div>
                                    </div>

                                    <div class="delivery-card-foot">
                                        <div class="delivery-card-time">{{ $dr->created_at->diffForHumans() }}</div>
                                        <div class="delivery-card-arrow">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
{{-- TABLE VIEW (Compact) --}}
    <div id="tableView" class="view-content" style="display:none;">
        <div class="card" style="padding:0; overflow:hidden;">
            <div class="table-wrap">
                <table class="mgmt-table">
                    <thead>
                        <tr>
                            <th>DR Number</th>
                            <th>Store</th>
                            <th>Date</th>
                            <th style="text-align:center;">Items</th>
                            <th style="text-align:right;">Total</th>
                            <th style="text-align:right;">Balance</th>
                            <th>Status</th>
                            <th style="width:80px; text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $dr)
                            @php
                                $isConfirmed = $dr->customer_confirmed ?? false;
                                $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                            @endphp
                            <tr class="mgmt-row" onclick="window.location='{{ route('deliveries.show', $dr) }}'">
                                <td>
                                    <div class="cell-primary" style="color:#c9a961; font-family:ui-monospace; font-weight:700;">{{ $dr->dr_number }}</div>
                                </td>
                                <td>
                                    <div class="cell-primary">{{ $dr->store->store_name ?? '-' }}</div>
                                    <div class="cell-secondary">{{ $dr->store->code ?? '' }}</div>
                                </td>
                                <td>
                                    <div class="cell-primary">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                                    <div class="cell-secondary">{{ $dr->created_at->diffForHumans() }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="items-badge">{{ $dr->items->count() }}</span>
                                </td>
                                <td style="text-align:right;" class="money">
                                    ₱{{ number_format($dr->total_amount, 2) }}
                                </td>
                                <td style="text-align:right;" class="money {{ $dr->balance > 0 ? 'amber' : 'green' }}">
                                    ₱{{ number_format($dr->balance, 2) }}
                                </td>
                                <td>
                                    @if($isConfirmed)
                                        <span class="badge badge-paid">✓ Delivered</span>
                                    @elseif($isOut)
                                        <span class="badge badge-info">🚚 In Transit</span>
                                    @elseif($dr->status === 'partial')
                                        <span class="badge badge-partial">◐ Partial</span>
                                    @elseif($dr->status === 'paid')
                                        <span class="badge badge-paid">✓ Paid</span>
                                    @else
                                        <span class="badge badge-pending">⏱ Pending</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <a href="{{ route('deliveries.show', $dr) }}" class="btn-icon" onclick="event.stopPropagation();" title="View">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(method_exists($deliveries, 'links') && $deliveries->hasPages())
        <div class="pagination-wrap">{{ $deliveries->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* ========== SUMMARY ========== */
    .summary-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:16px; }
    .summary {
        background:rgba(34,34,44,0.55); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
        border:1px solid rgba(255,255,255,0.06); border-radius:14px;
        padding:16px; display:flex; align-items:center; gap:12px;
        transition:all 0.2s;
    }
    .summary:hover { transform:translateY(-2px); border-color:rgba(169,120,74,0.25); }
    .summary-icon {
        width:40px; height:40px; border-radius:10px;
        background:rgba(169,120,74,0.1); border:1px solid rgba(169,120,74,0.2);
        display:grid; place-items:center; color:#c9a961; flex-shrink:0;
    }
    .summary-icon.green { background:rgba(34,197,94,0.1); border-color:rgba(34,197,94,0.2); color:#22c55e; }
    .summary-icon.blue { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.2); color:#3b82f6; }
    .summary-icon.amber { background:rgba(245,158,11,0.1); border-color:rgba(245,158,11,0.2); color:#f59e0b; }
    .summary-value { font-size:18px; font-weight:800; color:var(--text-primary); letter-spacing:-0.02em; line-height:1; font-variant-numeric:tabular-nums; }
    .summary-label { font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.1em; font-weight:700; margin-top:3px; }

    /* ========== TABS ========== */
    .tabs {
        display:flex; gap:6px; margin-bottom:16px; padding:5px;
        background:rgba(34,34,44,0.55); backdrop-filter:blur(20px);
        border:1px solid rgba(255,255,255,0.06); border-radius:12px;
        overflow-x:auto; scrollbar-width:none;
    }
    .tabs::-webkit-scrollbar { display:none; }
    .tab {
        display:inline-flex; align-items:center; gap:8px;
        padding:9px 16px; border-radius:8px;
        font-size:12.5px; font-weight:600;
        color:var(--text-secondary); transition:all 0.15s;
        white-space:nowrap; flex-shrink:0; text-decoration:none;
    }
    .tab:hover { background:rgba(255,255,255,0.04); color:var(--text-primary); }
    .tab.active {
        background:linear-gradient(135deg,rgba(169,120,74,0.25),rgba(169,120,74,0.15));
        color:#c9a961;
        box-shadow:inset 0 0 0 1px rgba(169,120,74,0.4);
    }
    .tab-count {
        padding:2px 8px; background:rgba(255,255,255,0.05); border-radius:10px;
        font-size:10.5px; font-weight:800; min-width:22px; text-align:center;
        color:var(--text-muted);
    }
    .tab.active .tab-count { background:rgba(169,120,74,0.3); color:#f0e6dc; }

    /* ========== TOOLBAR ========== */
    .toolbar { margin-bottom:16px; }
    .toolbar-form { display:flex; align-items:center; gap:8px; flex-wrap:nowrap; }
    .search-box { flex:1; min-width:0; max-width:380px; position:relative; display:flex; align-items:center; }
    .search-icon { position:absolute; left:12px; color:var(--text-muted); pointer-events:none; }
    .search-input {
        width:100%; padding:9px 14px 9px 36px;
        background:rgba(20,20,26,0.6); border:1px solid var(--border-strong);
        border-radius:8px; color:var(--text-primary); font-size:13px;
        font-family:inherit; outline:none;
    }
    .search-input::placeholder { color:var(--text-muted); }
    .search-input:focus { border-color:var(--accent); box-shadow:0 0 0 4px rgba(169,120,74,0.15); }
    .filter-select {
        padding:9px 12px; background:rgba(20,20,26,0.6);
        border:1px solid var(--border-strong); border-radius:8px;
        color:var(--text-primary); font-size:13px; font-family:inherit;
        cursor:pointer; outline:none; min-width:140px; flex-shrink:0;
    }
    .filter-select:focus { border-color:var(--accent); box-shadow:0 0 0 4px rgba(169,120,74,0.15); }
    .btn-filter {
        display:inline-flex; align-items:center; justify-content:center; gap:6px;
        padding:9px 16px; background:linear-gradient(135deg,#a9784a,#8a5f36);
        color:#fff; border:none; border-radius:8px;
        font-size:13px; font-weight:600; font-family:inherit;
        cursor:pointer; flex-shrink:0;
    }
    .btn-clear {
        width:36px; height:36px; border-radius:8px;
        background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25);
        color:#ef4444; display:grid; place-items:center;
        cursor:pointer; flex-shrink:0; text-decoration:none;
    }
    .view-toggle {
        display:inline-flex; gap:4px; padding:4px;
        background:rgba(20,20,26,0.6); border:1px solid var(--border-strong);
        border-radius:8px; flex-shrink:0;
    }
    .view-btn {
        width:30px; height:30px; border-radius:6px;
        background:transparent; border:none;
        color:var(--text-muted); display:grid; place-items:center;
        cursor:pointer; transition:all 0.15s;
    }
    .view-btn:hover { color:var(--text-primary); }
    .view-btn.active { background:linear-gradient(135deg,#a9784a,#8a5f36); color:#fff; }

    /* ========== DELIVERY CARD (LIST VIEW) ========== */
    .delivery-list { display:flex; flex-direction:column; gap:10px; }
    .delivery-card {
        background:linear-gradient(165deg,rgba(30,26,22,0.9),rgba(21,18,15,0.9));
        backdrop-filter:blur(20px);
        border:1px solid rgba(255,255,255,0.06);
        border-left:3px solid var(--status-color);
        border-radius:14px;
        transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        overflow:hidden;
    }
    .delivery-card:hover {
        border-color:var(--status-color);
        border-left-color:var(--status-color);
        transform:translateY(-2px);
        box-shadow:0 8px 24px -10px rgba(0,0,0,0.5);
    }
    .delivery-card-body { display:block; padding:16px 18px; text-decoration:none; color:inherit; }

    .delivery-card-head {
        display:flex; justify-content:space-between; align-items:flex-start;
        gap:12px; margin-bottom:14px;
    }
    .delivery-card-left { display:flex; align-items:center; gap:10px; min-width:0; }
    .delivery-status-dot {
        width:8px; height:8px; border-radius:50%;
        box-shadow:0 0 0 4px color-mix(in srgb, currentColor 15%, transparent);
        flex-shrink:0;
    }
    .delivery-card-title {
        font-size:14px; font-weight:800;
        color:#f5f3f0; letter-spacing:-0.01em;
        font-family:ui-monospace,monospace;
    }
    .delivery-card-sub {
        font-size:11px; color:#8a8378;
        margin-top:3px;
    }
    .status-badge {
        padding:4px 10px; border-radius:8px;
        font-size:10.5px; font-weight:800;
        text-transform:uppercase; letter-spacing:0.05em;
        border:1px solid;
        flex-shrink:0; white-space:nowrap;
    }

    .delivery-card-meta {
        display:grid; grid-template-columns:repeat(4, 1fr);
        gap:10px; padding:12px 0;
        border-top:1px solid rgba(255,255,255,0.04);
        border-bottom:1px solid rgba(255,255,255,0.04);
    }
    .meta-item { text-align:left; }
    .meta-label {
        font-size:9.5px; color:#71717a;
        text-transform:uppercase; letter-spacing:0.08em;
        font-weight:700; margin-bottom:4px;
    }
    .meta-value {
        font-size:13px; font-weight:700;
        color:#f5f3f0;
        font-variant-numeric:tabular-nums;
    }
    .meta-value.gold { color:#c9a961; }
    .meta-value.green { color:#22c55e; }
    .meta-value.amber { color:#f59e0b; }

    .delivery-card-foot {
        display:flex; justify-content:space-between; align-items:center;
        padding-top:12px;
    }
    .delivery-card-time { font-size:11px; color:#71717a; }
    .delivery-card-arrow { color:#52525b; transition:all 0.15s; }
    .delivery-card:hover .delivery-card-arrow {
        color:#c9a961; transform:translateX(3px);
    }

    /* ========== MGMT TABLE (COMPACT VIEW) ========== */
    .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .mgmt-table { width:100%; border-collapse:collapse; min-width:800px; }
    .mgmt-table thead th {
        padding:12px 14px; text-align:left;
        font-size:10.5px; font-weight:800; text-transform:uppercase;
        letter-spacing:0.08em; color:var(--text-muted);
        background:rgba(20,20,26,0.6);
        border-bottom:1px solid rgba(255,255,255,0.06);
        white-space:nowrap;
    }
    .mgmt-row {
        border-bottom:1px solid rgba(255,255,255,0.04);
        cursor:pointer;
        transition:background 0.15s;
    }
    .mgmt-row:hover { background:rgba(169,120,74,0.04); }
    .mgmt-table td { padding:12px 14px; font-size:12.5px; vertical-align:middle; }

    .cell-primary { font-weight:600; color:var(--text-primary); }
    .cell-secondary { font-size:10.5px; color:var(--text-muted); margin-top:2px; font-family:ui-monospace,monospace; }
    .money { font-variant-numeric:tabular-nums; font-weight:700; font-family:ui-monospace,monospace; white-space:nowrap; }
    .money.green { color:#22c55e; }
    .money.amber { color:#f59e0b; }
    .items-badge {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:26px; height:26px; padding:0 8px;
        background:rgba(201,169,97,0.12); border:1px solid rgba(201,169,97,0.25);
        border-radius:8px; color:#c9a961;
        font-size:11.5px; font-weight:800;
        font-family:ui-monospace,monospace;
    }
    .badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:4px 9px; border-radius:6px;
        font-size:10.5px; font-weight:800;
        text-transform:uppercase; letter-spacing:0.03em;
        white-space:nowrap;
    }
    .badge-paid { background:rgba(34,197,94,0.15); color:#22c55e; border:1px solid rgba(34,197,94,0.3); }
    .badge-partial { background:rgba(59,130,246,0.15); color:#60a5fa; border:1px solid rgba(59,130,246,0.3); }
    .badge-pending { background:rgba(245,158,11,0.15); color:#f59e0b; border:1px solid rgba(245,158,11,0.3); }
    .badge-info { background:rgba(59,130,246,0.15); color:#60a5fa; border:1px solid rgba(59,130,246,0.3); }
    .btn-icon {
        width:30px; height:30px; border-radius:8px;
        background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
        color:var(--text-secondary); display:inline-grid; place-items:center;
        text-decoration:none; transition:all 0.15s;
    }
    .btn-icon:hover { background:rgba(255,255,255,0.08); color:var(--text-primary); }

    /* ========== EMPTY ========== */
    .empty { text-align:center; padding:60px 20px; }
    .empty-icon {
        width:64px; height:64px; margin:0 auto 16px;
        border-radius:16px; background:rgba(169,120,74,0.1);
        border:1px solid rgba(169,120,74,0.2);
        color:#c9a961; display:grid; place-items:center;
    }
    .empty-title { font-size:15px; font-weight:800; color:var(--text-primary); margin-bottom:6px; }
    .empty-text { font-size:12.5px; color:var(--text-muted); }

    .pagination-wrap { margin-top:20px; display:flex; justify-content:center; }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1100px) {
        .summary-grid { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width: 900px) {
        .toolbar-form { flex-wrap:wrap; }
        .search-box { max-width:100%; flex:1 1 100%; }
        .filter-select { flex:1; min-width:auto; }
        .delivery-card-meta { grid-template-columns:repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .summary-grid { grid-template-columns:1fr 1fr; }
        .toolbar-form { flex-direction:column; align-items:stretch; }
        .search-box { max-width:100%; }
        .filter-select { width:100%; min-width:auto; }
        .btn-filter { width:100%; justify-content:center; }
        .btn-clear { width:100%; }
        .view-toggle { width:100%; justify-content:center; }
        .delivery-card-meta { grid-template-columns:repeat(2, 1fr); gap:8px; }
        .meta-value { font-size:12px; }
    }

    /* ═══ STORE GROUPS (accordion) ═══ */
    .store-groups { display: flex; flex-direction: column; gap: 12px; }

    .store-group {
        background: linear-gradient(165deg, rgba(30,26,22,0.9), rgba(21,18,15,0.9));
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .store-group.expanded {
        border-color: rgba(201,169,97,0.4);
        box-shadow: 0 12px 32px -12px rgba(0,0,0,0.6);
    }

    .store-group-head {
        display: grid;
        grid-template-columns: 48px 1fr auto 24px;
        gap: 14px;
        align-items: center;
        padding: 16px 18px;
        cursor: pointer;
        user-select: none;
        transition: background 0.15s;
    }
    .store-group-head:hover { background: rgba(255,255,255,0.02); }

    .store-avatar {
        width: 48px; height: 48px; border-radius: 14px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        display: grid; place-items: center;
        font-size: 15px; font-weight: 900;
        flex-shrink: 0; overflow: hidden;
        box-shadow: 0 4px 12px -4px rgba(201,169,97,0.5);
    }
    .store-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .store-info { min-width: 0; }
    .store-name {
        font-size: 15px; font-weight: 800; color: #fafafa;
        margin-bottom: 4px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .store-meta {
        display: flex; align-items: center; gap: 6px;
        font-size: 11.5px; color: #71717a; flex-wrap: wrap;
    }
    .store-code { font-family: ui-monospace, monospace; color: #a1a1aa; }
    .store-dot { color: #52525b; }
    .store-count { color: #c9a961; font-weight: 800; }

    .store-stats { display: flex; gap: 16px; flex-shrink: 0; }
    .store-stat { text-align: right; }
    .store-stat-lbl {
        font-size: 9.5px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.06em;
        margin-bottom: 3px;
    }
    .store-stat-val {
        font-size: 14px; font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
    }
    .store-stat-val.gold { color: #c9a961; }
    .store-stat-val.amber { color: #f59e0b; }

    .store-chevron {
        color: #71717a;
        transition: transform 0.25s;
        flex-shrink: 0;
    }
    .store-group.expanded .store-chevron {
        transform: rotate(180deg);
        color: #c9a961;
    }

    .store-group-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(0,0,0,0.2);
        border-top: 1px solid rgba(255,255,255,0.04);
    }
    .store-group.expanded .store-group-body { max-height: 5000px; }

    .store-body-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 16px 18px 12px;
    }
    .store-body-stat {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.04);
        border-radius: 10px;
    }
    .store-body-stat-lbl {
        font-size: 10.5px; font-weight: 700; color: #71717a;
    }
    .store-body-stat-val {
        font-size: 14px; font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
    }
    .store-body-stat-val.green { color: #22c55e; }
    .store-body-stat-val.amber { color: #f59e0b; }

    .store-group-body .delivery-list {
        padding: 0 18px 18px;
    }

    @media (max-width: 700px) {
        .store-group-head {
            grid-template-columns: 42px 1fr 20px;
            gap: 10px;
            padding: 14px;
        }
        .store-stats { display: none; }
        .store-avatar { width: 42px; height: 42px; font-size: 13px; }
        .store-name { font-size: 14px; }
        .store-body-stats { grid-template-columns: 1fr; padding: 12px 14px; }
        .store-group-body .delivery-list { padding: 0 14px 14px; }
    }

    /* ═══ STORE HEAD clickable enhancement ═══ */
    .store-group-head {
        cursor: pointer !important;
        -webkit-tap-highlight-color: transparent;
    }
    .store-group-head:active {
        background: rgba(255,255,255,0.04) !important;
    }
    .store-chevron { pointer-events: none; }

    /* ═══ COMPACT delivery cards sulod sa store group ═══ */
    .store-group-body .delivery-list {
        padding: 0 14px 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .store-group-body .delivery-card {
        border-radius: 10px;
    }
    .store-group-body .delivery-card-body {
        padding: 10px 12px !important;
    }
    .store-group-body .delivery-card-head {
        margin-bottom: 6px !important;
        padding-bottom: 6px !important;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .store-group-body .delivery-card-title {
        font-size: 12px !important;
        margin-bottom: 1px !important;
    }
    .store-group-body .delivery-card-sub {
        font-size: 10px !important;
    }
    .store-group-body .status-badge {
        font-size: 9.5px !important;
        padding: 3px 8px !important;
    }
    .store-group-body .delivery-status-dot {
        width: 7px !important;
        height: 7px !important;
    }
    .store-group-body .delivery-card-meta {
        gap: 8px !important;
        padding: 6px 8px !important;
        background: rgba(0,0,0,0.2);
        border-radius: 7px;
        margin-bottom: 6px !important;
    }
    .store-group-body .meta-item {
        gap: 1px !important;
    }
    .store-group-body .meta-label {
        font-size: 8.5px !important;
        margin-bottom: 1px !important;
    }
    .store-group-body .meta-value {
        font-size: 11px !important;
    }
    .store-group-body .delivery-card-foot {
        padding-top: 4px !important;
        font-size: 10px !important;
    }
    .store-group-body .delivery-card-time {
        font-size: 10px !important;
    }
    .store-group-body .delivery-card-arrow svg {
        width: 13px !important;
        height: 13px !important;
    }

    /* Mobile — mas compact pa */
    @media (max-width: 700px) {
        .store-group-body .delivery-card-body { padding: 9px 10px !important; }
        .store-group-body .delivery-card-title { font-size: 11.5px !important; }
        .store-group-body .meta-value { font-size: 10.5px !important; }
        .store-group-body .delivery-card-meta { gap: 6px !important; padding: 5px 6px !important; }
        .store-group-body .meta-label { font-size: 8px !important; }
    }

    /* ═══ COMPACT FILTER BAR (Deliveries) ═══ */
    .toolbar {
        margin-bottom: 16px !important;
    }
    .toolbar-form {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        padding: 8px 10px !important;
        background: linear-gradient(165deg, rgba(30,26,22,0.6), rgba(21,18,15,0.6)) !important;
        border: 1px solid rgba(255,255,255,0.05) !important;
        border-radius: 12px !important;
        align-items: center !important;
    }

    /* Search box — sakto lang */
    .search-box {
        position: relative !important;
        flex: 1 1 180px !important;
        min-width: 150px !important;
        max-width: 240px !important;
    }
    .search-icon {
        position: absolute !important;
        left: 10px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 13px !important;
        height: 13px !important;
        margin: 0 !important;
        pointer-events: none !important;
        color: #71717a !important;
    }
    .search-input {
        width: 100% !important;
        height: 32px !important;
        padding: 0 10px 0 30px !important;
        font-size: 12px !important;
        border: 1px solid rgba(255,255,255,0.08) !important;
        border-radius: 8px !important;
        background: rgba(0,0,0,0.3) !important;
        color: #fafafa !important;
        box-sizing: border-box !important;
    }
    .search-input:focus {
        outline: none !important;
        border-color: #c9a961 !important;
        box-shadow: 0 0 0 2px rgba(201,169,97,0.15) !important;
    }
    .search-input::placeholder { color: #52525b !important; }

    /* Filter selects + dates — compact */
    .filter-select {
        height: 32px !important;
        padding: 0 26px 0 10px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        border: 1px solid rgba(255,255,255,0.08) !important;
        border-radius: 8px !important;
        background-color: rgba(0,0,0,0.3) !important;
        color: #fafafa !important;
        cursor: pointer !important;
        box-sizing: border-box !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2371717a' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 8px center !important;
        background-size: 10px !important;
        flex: 0 0 auto !important;
    }
    select.filter-select {
        min-width: 100px !important;
        max-width: 140px !important;
    }
    input[type="date"].filter-select {
        min-width: 120px !important;
        max-width: 130px !important;
        padding-right: 8px !important;
        background-image: none !important;
    }
    input[type="date"].filter-select::-webkit-calendar-picker-indicator {
        filter: invert(0.5) !important;
        cursor: pointer !important;
    }
    .filter-select:focus {
        outline: none !important;
        border-color: #c9a961 !important;
        box-shadow: 0 0 0 2px rgba(201,169,97,0.15) !important;
    }

    /* Filter button */
    .btn-filter {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        height: 32px !important;
        padding: 0 12px !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        border: none !important;
        border-radius: 8px !important;
        background: linear-gradient(135deg, #c9a961, #b8944d) !important;
        color: #0f0f14 !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
    }
    .btn-filter svg { width: 11px !important; height: 11px !important; }
    .btn-filter:hover {
        transform: none !important;
        box-shadow: 0 4px 12px -4px rgba(201,169,97,0.5) !important;
    }

    /* Clear button */
    .btn-clear {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        border-radius: 8px !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        background: rgba(255,255,255,0.04) !important;
        color: #d4d4d8 !important;
        text-decoration: none !important;
        flex-shrink: 0 !important;
        box-sizing: border-box !important;
    }
    .btn-clear:hover {
        background: rgba(239,68,68,0.15) !important;
        border-color: rgba(239,68,68,0.3) !important;
        color: #ef4444 !important;
    }

    /* View toggle */
    .view-toggle {
        display: inline-flex !important;
        gap: 4px !important;
        padding: 3px !important;
        background: rgba(0,0,0,0.3) !important;
        border: 1px solid rgba(255,255,255,0.06) !important;
        border-radius: 9px !important;
        margin-left: auto !important;
        height: 32px !important;
        box-sizing: border-box !important;
    }
    .view-btn {
        width: 26px !important;
        height: 26px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: transparent !important;
        border: none !important;
        color: #71717a !important;
        cursor: pointer !important;
        transition: all 0.15s !important;
    }
    .view-btn:hover { background: rgba(255,255,255,0.05) !important; color: #d4d4d8 !important; }
    .view-btn.active {
        background: linear-gradient(135deg, #c9a961, #b8944d) !important;
        color: #0f0f14 !important;
    }
    .view-btn svg { width: 12px !important; height: 12px !important; }

    /* Responsive — wrap on mobile */
    @media (max-width: 768px) {
        .toolbar-form {
            flex-wrap: wrap !important;
        }
        .search-box {
            flex: 1 1 100% !important;
            max-width: 100% !important;
        }
        select.filter-select,
        input[type="date"].filter-select {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            max-width: none !important;
        }
        .view-toggle {
            margin-left: 0 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    // View toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const listView = document.getElementById('listView');
    const tableView = document.getElementById('tableView');

    function setView(view) {
        viewBtns.forEach(b => b.classList.toggle('active', b.dataset.view === view));

        if (view === 'table') {
            if (listView) listView.style.display = 'none';
            if (tableView) tableView.style.display = 'block';
        } else {
            if (listView) listView.style.display = 'block';
            if (tableView) tableView.style.display = 'none';
        }

        try { localStorage.setItem('deliveries_view', view); } catch(e) {}
    }

    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            setView(btn.dataset.view);
        });
    });

    // Restore saved view
    try {
        const saved = localStorage.getItem('deliveries_view');
        if (saved && (saved === 'list' || saved === 'table')) {
            setView(saved);
        }
    } catch(e) {}

})();

    // ═══ STORE GROUP ACCORDION (event delegation) ═══
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            var head = e.target.closest('[data-toggle-group]');
            if (!head) return;
            e.preventDefault();
            var storeId = head.getAttribute('data-toggle-group');
            var group = document.getElementById('sg-' + storeId);
            if (group) {
                group.classList.toggle('expanded');
                console.log('Toggled group:', storeId, 'expanded:', group.classList.contains('expanded'));
            }
        });
    });</script>
@endpush