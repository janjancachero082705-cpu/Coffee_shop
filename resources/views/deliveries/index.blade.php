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
    {{-- LIST VIEW (Card style) --}}
    <div id="listView" class="view-content">
        <div class="delivery-list">
            @foreach($deliveries as $dr)
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
                                    <div class="delivery-card-sub">{{ $dr->store->store_name ?? '-' }} · {{ $dr->store->code ?? '' }}</div>
                                </div>
                            </div>
                            <span class="status-badge" style="background:{{ $statusColor }}22; color:{{ $statusColor }}; border-color:{{ $statusColor }}55;">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="delivery-card-meta">
                            <div class="meta-item">
                                <div class="meta-label">Date</div>
                                <div class="meta-value">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Items</div>
                                <div class="meta-value">{{ $dr->items->count() }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Total</div>
                                <div class="meta-value gold">₱{{ number_format($dr->total_amount, 2) }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Balance</div>
                                <div class="meta-value {{ $dr->balance > 0 ? 'amber' : 'green' }}">₱{{ number_format($dr->balance, 2) }}</div>
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
</script>
@endpush