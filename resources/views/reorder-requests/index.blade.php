@extends('layouts.admin')

@section('title', 'Reorder Requests')
@section('subtitle', 'Stock requests from stores')

@section('content')

@php
    $search = request('search');
    $statusFilter = request('status');
    $storeFilter = request('store');

    $query = \App\Models\ReorderRequest::query()->with(['store', 'items']);
    if ($statusFilter) $query->where('status', $statusFilter);
    if ($storeFilter) $query->where('store_id', $storeFilter);
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('request_number', 'like', "%{$search}%")
              ->orWhereHas('store', function ($sq) use ($search) {
                  $sq->where('store_name', 'like', "%{$search}%")
                     ->orWhere('code', 'like', "%{$search}%");
              });
        });
    }
    $allRequests = $query->latest()->get();

    $storeGroups = $allRequests->groupBy('store_id')->map(function ($group) {
        $store = $group->first()->store;
        return [
            'store' => $store,
            'requests' => $group,
            'count' => $group->count(),
            'pending' => $group->where('status', 'pending')->count(),
            'approved' => $group->where('status', 'approved')->count(),
            'rejected' => $group->where('status', 'rejected')->count(),
            'total_amount' => $group->sum('total_amount'),
            'latest' => $group->max('created_at'),
        ];
    })->sortByDesc('latest')->values();

    $stats = [
        'total' => \App\Models\ReorderRequest::count(),
        'pending' => \App\Models\ReorderRequest::where('status', 'pending')->count(),
        'approved' => \App\Models\ReorderRequest::where('status', 'approved')->count(),
        'rejected' => \App\Models\ReorderRequest::where('status', 'rejected')->count(),
    ];

    $currentStore = $storeFilter ? \App\Models\Store::find($storeFilter) : null;
@endphp

{{-- ===== KPIs ===== --}}
<div class="rr-kpis">
    <div class="rr-kpi rr-kpi-primary">
        <div class="rr-kpi-head">
            <div class="rr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </div>
            <div class="rr-kpi-label">Total Requests</div>
        </div>
        <div class="rr-kpi-value">{{ $stats['total'] }}</div>
        <div class="rr-kpi-foot">{{ $storeGroups->count() }} store{{ $storeGroups->count() !== 1 ? 's' : '' }}</div>
    </div>

    <div class="rr-kpi rr-kpi-amber">
        <div class="rr-kpi-head">
            <div class="rr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="rr-kpi-label">Pending</div>
        </div>
        <div class="rr-kpi-value">{{ $stats['pending'] }}</div>
        <div class="rr-kpi-foot">Awaiting action</div>
    </div>

    <div class="rr-kpi rr-kpi-green">
        <div class="rr-kpi-head">
            <div class="rr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            </div>
            <div class="rr-kpi-label">Approved</div>
        </div>
        <div class="rr-kpi-value">{{ $stats['approved'] }}</div>
        <div class="rr-kpi-foot">Ready for delivery</div>
    </div>

    <div class="rr-kpi rr-kpi-red">
        <div class="rr-kpi-head">
            <div class="rr-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
            </div>
            <div class="rr-kpi-label">Rejected</div>
        </div>
        <div class="rr-kpi-value">{{ $stats['rejected'] }}</div>
        <div class="rr-kpi-foot">Declined</div>
    </div>
</div>

{{-- ===== TOOLBAR ===== --}}
<div class="rr-toolbar">
    <div class="rr-toolbar-title">
        <h2 class="rr-title">{{ $currentStore ? $currentStore->store_name : 'All Stores' }}</h2>
        <div class="rr-subtitle">
            <span class="rr-grouped-badge">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/></svg>
                Grouped by store
            </span>
            · {{ $storeGroups->count() }} store{{ $storeGroups->count() !== 1 ? 's' : '' }}
            · {{ $allRequests->count() }} request{{ $allRequests->count() !== 1 ? 's' : '' }}
        </div>
    </div>
</div>

<form method="GET" class="rr-filters">
    <div class="rr-search-wrap">
        <svg class="rr-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="text" name="search" class="rr-search" placeholder="Search request # or store..." value="{{ $search }}">
    </div>

    <select name="status" class="rr-select">
        <option value="">All Status</option>
        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved</option>
        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>

    <button type="submit" class="rr-filter-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
        </svg>
        Filter
    </button>

    @if($search || $statusFilter || $storeFilter)
        <a href="{{ route('reorder-requests.index') }}" class="rr-clear-btn">Clear</a>
    @endif
</form>

@if($currentStore)
    {{-- ===== STORE VIEW — Show requests table ===== --}}
    <a href="{{ route('reorder-requests.index') }}" class="rr-back-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to all stores
    </a>

    <div class="rr-card">
        @if($allRequests->count() > 0)
            <div style="overflow-x:auto;">
                <table class="rr-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allRequests as $req)
                            @php $isUnread = !$req->is_read_by_admin; @endphp
                            <tr>
                                <td>
                                    <div class="rr-number">
                                        {{ $req->request_number }}
                                        @if($isUnread)
                                            <span class="rr-new-badge">New</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="rr-date-main">{{ $req->created_at->format('M d, Y') }}</div>
                                    <div class="rr-date-sub">{{ $req->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <span class="rr-items">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        {{ $req->items->count() }} item{{ $req->items->count() != 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="rr-total">&#8369;{{ number_format($req->total_amount, 2) }}</div>
                                </td>
                                <td>
                                    <span class="rr-status {{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <a href="{{ route('reorder-requests.show', $req->id) }}" class="rr-btn-view">
                                        View
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rr-empty">
                <div class="rr-empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                </div>
                <div class="rr-empty-title">Walay requests</div>
                <div class="rr-empty-text">Walay reorder requests niini nga store</div>
            </div>
        @endif
    </div>
@else
    {{-- ===== STORE GRID ===== --}}
    @if($storeGroups->isEmpty())
        <div class="rr-empty-state">
            <div class="rr-empty-state-icon">
                <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </div>
            <div class="rr-empty-state-title">
                @if($search || $statusFilter)
                    Walay requests match sa filter
                @else
                    Walay reorder requests yet
                @endif
            </div>
        </div>
    @else
        <div class="rr-groups">
            @foreach($storeGroups as $group)
                @php
                    $store = $group['store'];
                    $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
                @endphp

                <a href="{{ route('reorder-requests.index', ['store' => $store->id]) }}" class="rr-group">
                    <div class="rr-group-head">
                        <div class="rr-store-avatar">
                            @if($store && $store->logo_url)
                                <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <div class="rr-store-info">
                            <div class="rr-store-name">{{ $store->store_name ?? '-' }}</div>
                            <div class="rr-store-meta">
                                <span class="rr-store-code">{{ $store->code ?? '' }}</span>
                                <span class="rr-dot">·</span>
                                <span class="rr-report-count">{{ $group['count'] }} request{{ $group['count'] !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <svg class="rr-group-arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </div>

                    <div class="rr-group-stats">
                        <div class="rr-stat">
                            <div class="rr-stat-lbl">Pending</div>
                            <div class="rr-stat-val amber">{{ $group['pending'] }}</div>
                        </div>
                        <div class="rr-stat">
                            <div class="rr-stat-lbl">Approved</div>
                            <div class="rr-stat-val green">{{ $group['approved'] }}</div>
                        </div>
                        <div class="rr-stat">
                            <div class="rr-stat-lbl">Rejected</div>
                            <div class="rr-stat-val red">{{ $group['rejected'] }}</div>
                        </div>
                    </div>

                    <div class="rr-group-total">
                        <div class="rr-group-total-lbl">Total Value</div>
                        <div class="rr-group-total-val">&#8369;{{ number_format($group['total_amount'], 0) }}</div>
                    </div>

                    <div class="rr-group-progress">
                        <div class="rr-group-progress-info">
                            <span class="rr-progress-pct">{{ $group['pending'] }} pending</span>
                            <span class="rr-progress-time">{{ $group['latest'] ? \Carbon\Carbon::parse($group['latest'])->diffForHumans() : '' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* ═══════ KPIs ═══════ */
    .rr-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .rr-kpi {
        position: relative;
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .rr-kpi::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
    }
    .rr-kpi-primary::before { background: linear-gradient(90deg, #c9a961, transparent); }
    .rr-kpi-green::before { background: linear-gradient(90deg, #22c55e, transparent); }
    .rr-kpi-amber::before { background: linear-gradient(90deg, #f59e0b, transparent); }
    .rr-kpi-red::before { background: linear-gradient(90deg, #ef4444, transparent); }
    .rr-kpi:hover { transform: translateY(-2px); border-color: rgba(201, 169, 97, 0.25); }
    .rr-kpi-head { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .rr-kpi-icon {
        width: 32px; height: 32px; border-radius: 9px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .rr-kpi-primary .rr-kpi-icon { background: rgba(201, 169, 97, 0.15); color: #c9a961; }
    .rr-kpi-green .rr-kpi-icon { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .rr-kpi-amber .rr-kpi-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .rr-kpi-red .rr-kpi-icon { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .rr-kpi-label {
        font-size: 10.5px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
    }
    .rr-kpi-value {
        font-size: 24px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.03em; line-height: 1;
        font-variant-numeric: tabular-nums; margin-bottom: 6px;
    }
    .rr-kpi-foot { font-size: 11px; color: #71717a; }

    /* ═══════ Toolbar ═══════ */
    .rr-toolbar { margin-bottom: 14px; }
    .rr-title {
        font-size: 20px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.02em; margin-bottom: 4px;
    }
    .rr-subtitle {
        font-size: 12px; color: #71717a;
        display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    }
    .rr-grouped-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 9px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        border-radius: 100px;
        font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.06em;
        border: 1px solid rgba(201, 169, 97, 0.25);
    }

    /* ═══════ Filter bar ═══════ */
    .rr-filters {
        display: flex;
        gap: 8px;
        align-items: center;
        padding: 10px 12px;
        margin-bottom: 16px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        flex-wrap: wrap;
    }
    .rr-search-wrap {
        position: relative;
        flex: 1 1 200px;
        min-width: 160px;
        max-width: 400px;
    }
    .rr-search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        color: #71717a; pointer-events: none;
    }
    .rr-search {
        width: 100%;
        height: 36px;
        padding: 0 14px 0 36px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 9px;
        color: #fafafa; font-size: 13px;
        font-family: inherit;
    }
    .rr-search:focus {
        outline: none; border-color: #c9a961;
        box-shadow: 0 0 0 2px rgba(201, 169, 97, 0.15);
    }
    .rr-search::placeholder { color: #52525b; }
    .rr-select {
        height: 36px;
        padding: 0 30px 0 12px;
        background-color: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 9px;
        color: #fafafa; font-size: 12.5px;
        font-weight: 600; font-family: inherit;
        cursor: pointer;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2371717a' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 10px;
        min-width: 130px;
    }
    .rr-select:focus {
        outline: none; border-color: #c9a961;
        box-shadow: 0 0 0 2px rgba(201, 169, 97, 0.15);
    }
    .rr-filter-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; height: 36px; padding: 0 16px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border: none; border-radius: 9px;
        color: #0f0f14; font-size: 12.5px; font-weight: 800;
        cursor: pointer; font-family: inherit;
        flex-shrink: 0;
    }
    .rr-filter-btn:hover { box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5); }
    .rr-clear-btn {
        display: inline-flex; align-items: center; justify-content: center;
        height: 36px; padding: 0 14px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 9px;
        color: #d4d4d8; font-size: 12.5px; font-weight: 700;
        text-decoration: none; flex-shrink: 0;
    }
    .rr-clear-btn:hover { background: rgba(255, 255, 255, 0.08); color: #fafafa; }

    /* ═══════ Store Groups Grid ═══════ */
    .rr-groups {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }
    .rr-group {
        display: flex;
        flex-direction: column;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: all 0.22s;
        min-width: 0;
    }
    .rr-group:hover {
        border-color: rgba(201, 169, 97, 0.4);
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.6);
        transform: translateY(-2px);
    }
    .rr-group:active { transform: translateY(0); }

    .rr-group-head {
        display: grid;
        grid-template-columns: 44px 1fr 16px;
        gap: 12px;
        padding: 16px 16px 12px;
        align-items: center;
    }
    .rr-store-avatar {
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
    .rr-store-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .rr-store-info { min-width: 0; }
    .rr-store-name {
        font-size: 14px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.02em;
        margin-bottom: 3px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .rr-store-meta {
        display: flex; align-items: center; gap: 5px;
        font-size: 10.5px; color: #71717a;
        flex-wrap: wrap;
    }
    .rr-store-code { font-family: ui-monospace, monospace; }
    .rr-dot { color: #52525b; }
    .rr-report-count { color: #c9a961; font-weight: 800; }

    .rr-group-arrow {
        color: #52525b;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .rr-group:hover .rr-group-arrow {
        color: #c9a961;
        transform: translateX(3px);
    }

    .rr-group-stats {
        padding: 0 16px 10px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
    }
    .rr-stat {
        padding: 10px 8px;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 10px;
        text-align: center;
        min-width: 0;
    }
    .rr-stat-lbl {
        font-size: 8.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .rr-stat-val {
        font-size: 15px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        white-space: nowrap;
    }
    .rr-stat-val.green { color: #22c55e; }
    .rr-stat-val.amber { color: #f59e0b; }
    .rr-stat-val.red { color: #ef4444; }

    .rr-group-total {
        padding: 0 16px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .rr-group-total-lbl {
        font-size: 10px; font-weight: 700;
        color: #71717a; text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .rr-group-total-val {
        font-size: 14px; font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
    }

    .rr-group-progress {
        padding: 0 16px 16px;
        margin-top: auto;
    }
    .rr-group-progress-info {
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        font-weight: 600;
    }
    .rr-progress-pct { color: #c9a961; font-weight: 800; }
    .rr-progress-time { color: #71717a; }

    /* ═══════ Back link ═══════ */
    .rr-back-link {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.25);
        border-radius: 8px;
        color: #c9a961;
        font-size: 12px; font-weight: 700;
        text-decoration: none;
        margin-bottom: 12px;
        transition: all 0.15s;
    }
    .rr-back-link:hover {
        background: rgba(201, 169, 97, 0.15);
        border-color: rgba(201, 169, 97, 0.4);
    }

    /* ═══════ Table (store view) ═══════ */
    .rr-card {
        background: rgba(34, 34, 44, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        overflow: hidden;
    }
    .rr-table { width: 100%; border-collapse: collapse; }
    .rr-table thead th {
        text-align: left;
        font-size: 10.5px; font-weight: 700;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 14px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(20, 20, 26, 0.4);
        white-space: nowrap;
    }
    .rr-table tbody td {
        padding: 16px 18px;
        font-size: 13px;
        color: #a1a1aa;
        border-bottom: 1px solid rgba(38, 38, 46, 0.7);
        vertical-align: middle;
    }
    .rr-table tbody tr { transition: background 0.15s; }
    .rr-table tbody tr:hover { background: rgba(169, 120, 74, 0.04); }
    .rr-table tbody tr:last-child td { border-bottom: none; }

    .rr-number {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12.5px;
        font-weight: 700;
        color: #c9a961;
        letter-spacing: 0.02em;
    }
    .rr-date-main { font-weight: 600; color: #fafafa; font-size: 12.5px; margin-bottom: 2px; }
    .rr-date-sub  { font-size: 10.5px; color: #71717a; }
    .rr-items {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.25);
        color: #60a5fa;
        font-size: 11.5px; font-weight: 700;
        border-radius: 6px;
    }
    .rr-total {
        font-size: 14px; font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
    }
    .rr-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 11px; border-radius: 7px;
        font-size: 11px; font-weight: 700;
        text-transform: capitalize;
    }
    .rr-status::before {
        content: ''; width: 6px; height: 6px;
        border-radius: 50%; background: currentColor;
    }
    .rr-status.pending  { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); }
    .rr-status.approved { background: rgba(34, 197, 94, 0.12);  color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.25); }
    .rr-status.rejected { background: rgba(239, 68, 68, 0.12);  color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25); }
    .rr-btn-view {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        color: #c9a961;
        font-size: 12px; font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .rr-btn-view:hover {
        background: rgba(169, 120, 74, 0.2);
        border-color: rgba(169, 120, 74, 0.4);
        transform: translateX(2px);
    }
    .rr-new-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        font-size: 9px; font-weight: 800;
        letter-spacing: 0.06em;
        border-radius: 6px;
        text-transform: uppercase;
        margin-left: 8px;
    }
    .rr-new-badge::before {
        content: ''; width: 5px; height: 5px;
        border-radius: 50%; background: #fff;
        animation: rrpulse 1.5s ease-in-out infinite;
    }
    @keyframes rrpulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* ═══════ Empty states ═══════ */
    .rr-empty {
        padding: 60px 20px;
        text-align: center;
    }
    .rr-empty-icon {
        width: 64px; height: 64px;
        margin: 0 auto 16px;
        border-radius: 16px;
        background: rgba(201, 169, 97, 0.1);
        border: 1px solid rgba(201, 169, 97, 0.25);
        display: grid; place-items: center;
        color: #c9a961;
    }
    .rr-empty-title {
        font-size: 16px; font-weight: 700;
        color: #fafafa; margin-bottom: 6px;
    }
    .rr-empty-text { font-size: 12.5px; color: #71717a; }

    .rr-empty-state {
        text-align: center;
        padding: 80px 24px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
    }
    .rr-empty-state-icon {
        width: 88px; height: 88px;
        margin: 0 auto 20px;
        border-radius: 24px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.5);
    }
    .rr-empty-state-title {
        font-size: 18px; font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
    }

    /* ═══════ Responsive ═══════ */
    @media (max-width: 1100px) {
        .rr-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rr-groups { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 700px) {
        .rr-kpis { grid-template-columns: 1fr; }
        .rr-groups { grid-template-columns: 1fr; }
        .rr-search-wrap { flex: 1 1 100%; max-width: 100%; }
        .rr-filters { flex-wrap: wrap; }
    }
</style>
@endpush