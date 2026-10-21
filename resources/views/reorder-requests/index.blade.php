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
    $viewMode = $viewMode ?? 'store';
@endphp

{{-- ═══════════════════════════════════════════════ --}}
{{-- HERO                                            --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="rr-hero">
    <div class="rr-hero-grid"></div>

    <div class="rr-hero-inner">
        <div class="rr-hero-left">
            <div class="rr-hero-eyebrow">Stock Management</div>
            <h1 class="rr-hero-title">Reorder Requests</h1>
            <p class="rr-hero-sub">Manage stock requests from your consignment stores</p>
        </div>

        <div class="rr-hero-right">
            <div class="rr-stat">
                <div class="rr-stat-val">{{ $stats['total'] }}</div>
                <div class="rr-stat-lbl">Total</div>
            </div>
            <div class="rr-stat">
                <div class="rr-stat-val rr-stat-val-amber">{{ $stats['pending'] }}</div>
                <div class="rr-stat-lbl">Pending</div>
            </div>
            <div class="rr-stat">
                <div class="rr-stat-val rr-stat-val-green">{{ $stats['approved'] }}</div>
                <div class="rr-stat-lbl">Approved</div>
            </div>
            <div class="rr-stat">
                <div class="rr-stat-val rr-stat-val-red">{{ $stats['rejected'] }}</div>
                <div class="rr-stat-lbl">Rejected</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- VIEW TABS                                       --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="rr-view-tabs">
    <a href="{{ route('reorder-requests.index', array_merge(request()->query(), ['view' => 'store'])) }}"
       class="rr-view-tab {{ $viewMode === 'store' ? 'active' : '' }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/>
        </svg>
        <div class="rr-view-tab-text">
            <div class="rr-view-tab-title">By Store</div>
            <div class="rr-view-tab-count">{{ $storeGroups->count() }} {{ $storeGroups->count() !== 1 ? 'stores' : 'store' }}</div>
        </div>
    </a>
    <a href="{{ route('reorder-requests.index', array_merge(request()->query(), ['view' => 'order'])) }}"
       class="rr-view-tab {{ $viewMode === 'order' ? 'active' : '' }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="1"/>
        </svg>
        <div class="rr-view-tab-text">
            <div class="rr-view-tab-title">By Order</div>
            <div class="rr-view-tab-count">{{ $allRequests->count() }} {{ $allRequests->count() !== 1 ? 'orders' : 'order' }}</div>
        </div>
    </a>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- DATE FILTER BAR                                 --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="rr-date-bar">
    <div class="rr-date-quick">
        <a href="{{ route('reorder-requests.index', array_merge(request()->query(), ['date' => 'today'])) }}"
           class="rr-date-btn {{ ($dateFilter ?? 'today') === 'today' ? 'active' : '' }}">Today</a>
        <a href="{{ route('reorder-requests.index', array_merge(request()->query(), ['date' => 'tomorrow'])) }}"
           class="rr-date-btn {{ ($dateFilter ?? '') === 'tomorrow' ? 'active' : '' }}">Tomorrow</a>
        <a href="{{ route('reorder-requests.index', array_merge(request()->query(), ['date' => 'week'])) }}"
           class="rr-date-btn {{ ($dateFilter ?? '') === 'week' ? 'active' : '' }}">Week</a>
        <a href="{{ route('reorder-requests.index', array_merge(request()->query(), ['date' => 'all'])) }}"
           class="rr-date-btn {{ ($dateFilter ?? '') === 'all' ? 'active' : '' }}">All</a>
    </div>

    <form method="GET" class="rr-date-form">
        @foreach(request()->except(['date', 'page']) as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <input type="date" name="date"
               value="{{ preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter ?? '') ? $dateFilter : '' }}"
               class="rr-date-input"
               onchange="this.form.submit()">
    </form>

    <form method="GET" class="rr-store-filter-form">
        @foreach(request()->except(['store', 'page']) as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <div class="rr-store-select-wrap">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/>
            </svg>
            <select name="store" class="rr-store-select" onchange="this.form.submit()">
                <option value="all">All Stores</option>
                @foreach($stores ?? [] as $s)
                    <option value="{{ $s->id }}" {{ ($storeFilter ?? 'all') == $s->id ? 'selected' : '' }}>
                        {{ $s->store_name }}{{ $s->code ? ' · ' . $s->code : '' }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <button type="button" class="rr-print-btn" onclick="window.print()">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
        </svg>
        Print
    </button>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- SEARCH + STATUS FILTER                          --}}
{{-- ═══════════════════════════════════════════════ --}}
<form method="GET" class="rr-filters">
    <input type="hidden" name="view" value="{{ $viewMode }}">
    @if($storeFilter)<input type="hidden" name="store" value="{{ $storeFilter }}">@endif
    @if($dateFilter)<input type="hidden" name="date" value="{{ $dateFilter }}">@endif

    <div class="rr-search-wrap">
        <svg class="rr-search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="text" name="search" class="rr-search" placeholder="Search request # or store..." value="{{ $search }}">
        @if($search)
            <a href="{{ route('reorder-requests.index', array_filter(['view' => $viewMode, 'store' => $storeFilter, 'date' => $dateFilter, 'status' => $statusFilter])) }}" class="rr-search-clear">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif
    </div>

    <select name="status" class="rr-select" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved</option>
        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>

    <button type="submit" class="rr-filter-btn">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
        </svg>
        Filter
    </button>

    @if($search || $statusFilter || $storeFilter)
        <a href="{{ route('reorder-requests.index', ['view' => $viewMode]) }}" class="rr-clear-btn">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            Clear
        </a>
    @endif
</form>

{{-- ═══════════════════════════════════════════════ --}}
{{-- STORE VIEW                                      --}}
{{-- ═══════════════════════════════════════════════ --}}
@if($viewMode === 'store')

    @if($currentStore)
        {{-- SPECIFIC STORE --}}
        <a href="{{ route('reorder-requests.index', array_filter(['view' => $viewMode, 'date' => $dateFilter])) }}" class="rr-back-link">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to all stores
        </a>

        {{-- STORE HEADER --}}
        <div class="rr-store-header">
            <div class="rr-store-header-avatar">
                @if($currentStore->logo_url)
                    <img src="{{ $currentStore->avatar_url }}" alt="{{ $currentStore->store_name }}">
                @else
                    {{ strtoupper(substr($currentStore->store_name ?? 'S', 0, 2)) }}
                @endif
            </div>
            <div class="rr-store-header-info">
                <div class="rr-store-header-name">{{ $currentStore->store_name }}</div>
                <div class="rr-store-header-meta">
                    @if($currentStore->code)<span class="rr-mono">{{ $currentStore->code }}</span>@endif
                    <span class="rr-dot">·</span>
                    <span>{{ $allRequests->count() }} {{ $allRequests->count() !== 1 ? 'requests' : 'request' }}</span>
                </div>
            </div>
        </div>

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
        {{-- STORE GRID --}}
        @if($storeGroups->isEmpty())
            <div class="rr-empty-state">
                <div class="rr-empty-icon">
                    <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                </div>
                <div class="rr-empty-title">
                    @if($search || $statusFilter)
                        Walay requests match sa filter
                    @else
                        Walay reorder requests yet
                    @endif
                </div>
                <div class="rr-empty-text">Try a different filter or check back later.</div>
            </div>
        @else
            <div class="rr-section-label">
                <span class="rr-section-title">Stores with Requests</span>
                <span class="rr-section-count">{{ $storeGroups->count() }}</span>
            </div>

            <div class="rr-groups">
                @foreach($storeGroups as $group)
                    @php
                        $store = $group['store'];
                        $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
                        $pendingPct = $group['count'] > 0 ? round(($group['pending'] / $group['count']) * 100) : 0;
                    @endphp

                    <a href="{{ route('reorder-requests.index', array_filter(['view' => $viewMode, 'store' => $store->id, 'date' => $dateFilter])) }}" class="rr-group">
                        <div class="rr-group-head">
                            <div class="rr-store-avatar">
                                @if($store && $store->logo_url)
                                    <img src="{{ $store->avatar_url }}" alt="{{ $store->store_name }}">
                                @else
                                    {{ $initials }}
                                @endif
                            </div>
                            <div class="rr-store-info">
                                <div class="rr-store-name">{{ $store->store_name ?? '-' }}</div>
                                <div class="rr-store-meta">
                                    @if($store->code)<span class="rr-mono">{{ $store->code }}</span><span class="rr-dot">·</span>@endif
                                    <span class="rr-report-count">{{ $group['count'] }} {{ $group['count'] !== 1 ? 'requests' : 'request' }}</span>
                                </div>
                            </div>
                            <svg class="rr-group-arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </div>

                        <div class="rr-group-stats">
                            <div class="rr-stat-box">
                                <div class="rr-stat-box-lbl">Pending</div>
                                <div class="rr-stat-box-val amber">{{ $group['pending'] }}</div>
                            </div>
                            <div class="rr-stat-box">
                                <div class="rr-stat-box-lbl">Approved</div>
                                <div class="rr-stat-box-val green">{{ $group['approved'] }}</div>
                            </div>
                            <div class="rr-stat-box">
                                <div class="rr-stat-box-lbl">Rejected</div>
                                <div class="rr-stat-box-val red">{{ $group['rejected'] }}</div>
                            </div>
                        </div>

                        @if($pendingPct > 0)
                            <div class="rr-group-progress">
                                <div class="rr-group-progress-bar">
                                    <div class="rr-group-progress-fill" style="width:{{ $pendingPct }}%;"></div>
                                </div>
                            </div>
                        @endif

                        <div class="rr-group-foot">
                            <div class="rr-group-foot-item">
                                <div class="rr-group-foot-lbl">Total Value</div>
                                <div class="rr-group-foot-val">&#8369;{{ number_format($group['total_amount'], 0) }}</div>
                            </div>
                            <div class="rr-group-foot-item rr-group-foot-right">
                                <div class="rr-group-foot-lbl">Latest</div>
                                <div class="rr-group-foot-val-sm">{{ $group['latest'] ? \Carbon\Carbon::parse($group['latest'])->diffForHumans() : '—' }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @endif

@endif

{{-- ═══════════════════════════════════════════════ --}}
{{-- ORDER VIEW                                      --}}
{{-- ═══════════════════════════════════════════════ --}}
@if($viewMode === 'order')

    @if($allRequests->isEmpty())
        <div class="rr-empty-state">
            <div class="rr-empty-icon">
                <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                </svg>
            </div>
            <div class="rr-empty-title">Walay orders</div>
            <div class="rr-empty-text">Walay orders nga mo-match sa imong filter.</div>
        </div>
    @else
        {{-- AGGREGATED PRODUCTS --}}
        @if(isset($aggregatedProducts) && $aggregatedProducts->count() > 0)
            <div class="rr-section-label">
                <span class="rr-section-title">Delivery List</span>
                <span class="rr-section-count">{{ $aggregatedProducts->count() }} products</span>
            </div>

            <div class="rr-aggregated">
                <div class="rr-aggregated-list">
                    @foreach($aggregatedProducts as $product)
                        <div class="rr-aggregated-item">
                            <div class="rr-aggregated-main">
                                <div class="rr-aggregated-icon">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="rr-aggregated-info">
                                    <div class="rr-aggregated-name">{{ $product['name'] }}</div>
                                    @if($product['sku'])
                                        <div class="rr-aggregated-sku">SKU: {{ $product['sku'] }}</div>
                                    @endif
                                </div>
                                <div class="rr-aggregated-qty">
                                    <strong>{{ number_format($product['total_qty']) }}</strong>
                                    @if($product['unit'])
                                        <span class="rr-aggregated-unit">{{ $product['unit'] }}</span>
                                    @endif
                                </div>
                            </div>

                            @if(isset($product['stores']) && count($product['stores']) > 0)
                                <div class="rr-agg-store-list">
                                    @foreach($product['stores'] as $store)
                                        <div class="rr-agg-store-item">
                                            <span class="rr-agg-store-dot"></span>
                                            <span class="rr-agg-store-name">{{ $store['store_name'] }}</span>
                                            @if($store['store_code'])
                                                <span class="rr-agg-store-code">{{ $store['store_code'] }}</span>
                                            @endif
                                            <span class="rr-agg-store-qty">×{{ $store['qty'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="rr-aggregated-foot">
                    Total Items: <strong>{{ $aggregatedProducts->sum('total_qty') }}</strong>
                </div>
            </div>
        @endif
    @endif

@endif

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════ */
/* REORDER REQUESTS — CLEAN & PREMIUM              */
/* ═══════════════════════════════════════════════ */

/* ── HERO ──────────────────────────────────────── */
.rr-hero{
    position:relative;
    padding:28px 32px;
    background:#16161d;
    border:1px solid rgba(255,255,255,.06);
    border-radius:18px;
    margin-bottom:18px;
    overflow:hidden;
}
.rr-hero-grid{
    position:absolute;inset:0;
    background-image:
        linear-gradient(rgba(201,169,97,.04) 1px,transparent 1px),
        linear-gradient(90deg,rgba(201,169,97,.04) 1px,transparent 1px);
    background-size:40px 40px;
    mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    -webkit-mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    pointer-events:none;
}
.rr-hero-inner{
    position:relative;z-index:1;
    display:flex;align-items:center;justify-content:space-between;
    gap:32px;flex-wrap:wrap;
}
.rr-hero-left{flex:1;min-width:240px}
.rr-hero-eyebrow{
    display:inline-block;
    font-size:10.5px;font-weight:800;
    color:#c9a961;text-transform:uppercase;letter-spacing:.16em;
    margin-bottom:12px;padding-left:14px;position:relative;
}
.rr-hero-eyebrow::before{
    content:'';position:absolute;left:0;top:50%;
    width:6px;height:1px;background:#c9a961;
    transform:translateY(-50%);
}
.rr-hero-title{
    font-size:30px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.035em;line-height:1.1;margin:0 0 8px;
}
.rr-hero-sub{
    font-size:13px;color:#8a8792;font-weight:500;
    line-height:1.5;margin:0;max-width:440px;
}

.rr-hero-right{
    display:flex;align-items:center;gap:0;
    padding:14px 4px;
    border-top:1px solid rgba(255,255,255,.06);
    border-bottom:1px solid rgba(255,255,255,.06);
}
.rr-stat{padding:0 20px;text-align:center;position:relative}
.rr-stat + .rr-stat::before{
    content:'';position:absolute;left:0;top:50%;
    width:1px;height:28px;background:rgba(255,255,255,.08);
    transform:translateY(-50%);
}
.rr-stat-val{
    font-size:22px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.03em;line-height:1;
    font-variant-numeric:tabular-nums;
}
.rr-stat-val-amber{color:#fbbf24}
.rr-stat-val-green{color:#4ade80}
.rr-stat-val-red{color:#f87171}
.rr-stat-lbl{
    font-size:9.5px;font-weight:700;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.14em;
    margin-top:6px;
}

/* ── VIEW TABS ─────────────────────────────────── */
.rr-view-tabs{
    display:grid;grid-template-columns:1fr 1fr;
    gap:6px;margin-bottom:14px;padding:5px;
    background:rgba(255,255,255,.03);
    border:1px solid rgba(255,255,255,.06);
    border-radius:14px;
}
.rr-view-tab{
    display:flex;align-items:center;gap:12px;
    padding:13px 16px;border-radius:10px;
    background:transparent;border:1px solid transparent;
    color:#8a8792;text-decoration:none;
    transition:all .18s cubic-bezier(.4,0,.2,1);
}
.rr-view-tab svg{color:#6b6862;flex-shrink:0;transition:color .18s}
.rr-view-tab:hover{background:rgba(255,255,255,.04);color:#d4d1cc}
.rr-view-tab:hover svg{color:#c9a961}
.rr-view-tab.active{
    background:rgba(201,169,97,.12);
    color:#c9a961;
    box-shadow:inset 0 0 0 1px rgba(201,169,97,.25);
}
.rr-view-tab.active svg{color:#c9a961}
.rr-view-tab-text{display:flex;flex-direction:column;gap:2px;min-width:0}
.rr-view-tab-title{font-size:13px;font-weight:800;letter-spacing:-.01em}
.rr-view-tab-count{font-size:10.5px;font-weight:600;opacity:.75}

/* ── DATE BAR ──────────────────────────────────── */
.rr-date-bar{
    display:flex;align-items:center;gap:10px;
    padding:10px 12px;margin-bottom:14px;
    background:rgba(34,34,44,.4);
    border:1px solid rgba(255,255,255,.06);
    border-radius:12px;flex-wrap:wrap;
}
.rr-date-quick{display:flex;gap:4px;flex-wrap:wrap;flex:1}
.rr-date-btn{
    display:inline-flex;align-items:center;
    padding:7px 14px;border-radius:8px;
    background:transparent;
    border:1px solid transparent;
    color:#8a8792;font-size:12px;font-weight:700;
    text-decoration:none;transition:all .18s;
}
.rr-date-btn:hover{background:rgba(255,255,255,.04);color:#d4d1cc}
.rr-date-btn.active{
    background:rgba(201,169,97,.12);
    color:#c9a961;
    box-shadow:inset 0 0 0 1px rgba(201,169,97,.25);
}
.rr-date-form{display:flex}
.rr-date-input{
    padding:7px 12px;border-radius:8px;
    background:rgba(0,0,0,.25);
    border:1px solid rgba(255,255,255,.08);
    color:#f5f3f0;font-size:12px;
    font-family:inherit;cursor:pointer;outline:none;
    transition:all .18s;
}
.rr-date-input::-webkit-calendar-picker-indicator{filter:invert(.5);cursor:pointer}
.rr-date-input:hover{border-color:rgba(255,255,255,.14)}
.rr-date-input:focus{border-color:#c9a961;box-shadow:0 0 0 3px rgba(201,169,97,.1)}
.rr-print-btn{
    display:inline-flex;align-items:center;gap:6px;
    padding:7px 14px;border-radius:8px;
    background:rgba(59,130,246,.1);
    border:1px solid rgba(59,130,246,.25);
    color:#60a5fa;font-size:12px;font-weight:700;
    font-family:inherit;cursor:pointer;transition:all .18s;
}
.rr-print-btn:hover{background:rgba(59,130,246,.18);border-color:rgba(59,130,246,.4)}

/* ── STORE FILTER ──────────────────────────────── */
.rr-store-filter-form{display:flex}
.rr-store-select-wrap{position:relative;display:flex;align-items:center}
.rr-store-select-wrap svg{position:absolute;left:12px;color:#c9a961;pointer-events:none;z-index:1}
.rr-store-select{
    padding:7px 30px 7px 34px;
    border-radius:8px;
    background:rgba(0,0,0,.25);
    border:1px solid rgba(255,255,255,.08);
    color:#f5f3f0;
    font-size:12px;font-family:inherit;font-weight:700;
    cursor:pointer;outline:none;
    appearance:none;-webkit-appearance:none;
    transition:all .18s;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%237a7a85' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:10px;
}
.rr-store-select:hover{border-color:rgba(201,169,97,.3)}
.rr-store-select:focus{border-color:#c9a961;box-shadow:0 0 0 3px rgba(201,169,97,.1)}
.rr-store-select option{background:#1e1a16;color:#f5f3f0;padding:8px}

/* ── FILTERS BAR ───────────────────────────────── */
.rr-filters{
    display:flex;gap:8px;align-items:center;
    padding:10px 12px;margin-bottom:18px;
    background:rgba(34,34,44,.4);
    border:1px solid rgba(255,255,255,.06);
    border-radius:12px;flex-wrap:wrap;
}
.rr-search-wrap{
    position:relative;flex:1 1 200px;
    min-width:160px;max-width:400px;
}
.rr-search-icon{
    position:absolute;left:12px;top:50%;
    transform:translateY(-50%);
    color:#7a7a85;pointer-events:none;
    transition:color .18s;
}
.rr-search{
    width:100%;height:36px;
    padding:0 34px 0 34px;
    background:rgba(0,0,0,.25);
    border:1px solid rgba(255,255,255,.08);
    border-radius:9px;
    color:#f5f3f0;font-size:12.5px;font-family:inherit;
    outline:none;transition:all .18s;
    box-sizing:border-box;
}
.rr-search::placeholder{color:#6b6862}
.rr-search:focus{border-color:#c9a961;box-shadow:0 0 0 3px rgba(201,169,97,.1);background:rgba(201,169,97,.03)}
.rr-search-wrap:focus-within .rr-search-icon{color:#c9a961}
.rr-search-clear{
    position:absolute;right:10px;top:50%;
    transform:translateY(-50%);
    width:20px;height:20px;border-radius:50%;
    background:rgba(255,255,255,.06);color:#a8a5a0;
    display:grid;place-items:center;transition:all .15s;
}
.rr-search-clear:hover{background:rgba(239,68,68,.15);color:#fca5a5}

.rr-select{
    height:36px;padding:0 30px 0 12px;
    background-color:rgba(0,0,0,.25);
    border:1px solid rgba(255,255,255,.08);
    border-radius:9px;
    color:#f5f3f0;font-size:12.5px;
    font-weight:600;font-family:inherit;cursor:pointer;
    appearance:none;-webkit-appearance:none;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%237a7a85' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:10px;
    min-width:130px;
    outline:none;transition:all .18s;
    box-sizing:border-box;
}
.rr-select:focus{border-color:#c9a961;box-shadow:0 0 0 3px rgba(201,169,97,.1)}

.rr-filter-btn{
    display:inline-flex;align-items:center;justify-content:center;
    gap:6px;height:36px;padding:0 16px;
    background:linear-gradient(135deg,#c9a961,#a9784a);
    border:none;border-radius:9px;
    color:#0f0f14;font-size:12.5px;font-weight:800;
    cursor:pointer;font-family:inherit;flex-shrink:0;
    transition:all .18s;
}
.rr-filter-btn:hover{filter:brightness(1.05);box-shadow:0 4px 12px -4px rgba(201,169,97,.5)}

.rr-clear-btn{
    display:inline-flex;align-items:center;gap:5px;
    height:36px;padding:0 14px;
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.1);
    border-radius:9px;
    color:#d4d1cc;font-size:12.5px;font-weight:700;
    text-decoration:none;flex-shrink:0;
    transition:all .18s;
}
.rr-clear-btn:hover{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.3);color:#f87171}

/* ── SECTION LABEL ─────────────────────────────── */
.rr-section-label{
    display:flex;align-items:baseline;justify-content:space-between;
    gap:12px;margin:0 2px 14px;
}
.rr-section-title{
    font-size:12px;font-weight:800;color:#f5f3f0;
    text-transform:uppercase;letter-spacing:.12em;
}
.rr-section-count{
    display:inline-flex;align-items:center;justify-content:center;
    padding:2px 10px;
    background:rgba(255,255,255,.06);
    color:#8a8792;
    font-size:10.5px;font-weight:800;
    border-radius:100px;
}

/* ── STORE GRID ────────────────────────────────── */
.rr-groups{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:14px;
}
.rr-group{
    display:flex;flex-direction:column;
    background:rgba(34,34,44,.5);
    border:1px solid rgba(255,255,255,.06);
    border-radius:14px;
    overflow:hidden;text-decoration:none;color:inherit;
    transition:all .22s cubic-bezier(.4,0,.2,1);
    min-width:0;
    position:relative;
}
.rr-group::before{
    content:'';position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(90deg,#c9a961,rgba(201,169,97,.2));
    opacity:0;transition:opacity .22s;
}
.rr-group:hover{
    border-color:rgba(201,169,97,.35);
    transform:translateY(-2px);
    box-shadow:0 12px 28px -16px rgba(0,0,0,.6);
}
.rr-group:hover::before{opacity:1}

.rr-group-head{
    display:grid;grid-template-columns:44px 1fr 16px;
    gap:12px;padding:16px;align-items:center;
}
.rr-store-avatar{
    width:44px;height:44px;border-radius:11px;
    background:linear-gradient(135deg,#c9a961,#8a5f36);
    color:#fff;
    display:grid;place-items:center;
    font-size:14px;font-weight:800;letter-spacing:-.02em;
    flex-shrink:0;overflow:hidden;
    box-shadow:0 6px 14px -6px rgba(201,169,97,.5);
}
.rr-store-avatar img{width:100%;height:100%;object-fit:cover}

.rr-store-info{min-width:0}
.rr-store-name{
    font-size:14px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.015em;line-height:1.25;
    margin-bottom:3px;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.rr-store-meta{
    display:flex;align-items:center;gap:5px;
    font-size:10.5px;color:#7a7a85;
    flex-wrap:wrap;
}
.rr-mono{font-family:ui-monospace,monospace}
.rr-dot{color:#52525b}
.rr-report-count{color:#c9a961;font-weight:800}

.rr-group-arrow{
    color:#52525b;flex-shrink:0;
    transition:all .18s;
}
.rr-group:hover .rr-group-arrow{
    color:#c9a961;
    transform:translateX(3px);
}

.rr-group-stats{
    display:grid;grid-template-columns:repeat(3,1fr);
    gap:6px;padding:0 16px 12px;
}
.rr-stat-box{
    padding:10px 6px;
    background:rgba(0,0,0,.22);
    border:1px solid rgba(255,255,255,.04);
    border-radius:9px;
    text-align:center;
    min-width:0;
}
.rr-stat-box-lbl{
    font-size:8.5px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.1em;
    margin-bottom:4px;
}
.rr-stat-box-val{
    font-size:15px;font-weight:800;
    color:#f5f3f0;letter-spacing:-.02em;
    font-variant-numeric:tabular-nums;
    line-height:1;
}
.rr-stat-box-val.amber{color:#fbbf24}
.rr-stat-box-val.green{color:#4ade80}
.rr-stat-box-val.red{color:#f87171}

.rr-group-progress{padding:0 16px 12px}
.rr-group-progress-bar{
    height:3px;border-radius:100px;
    background:rgba(255,255,255,.06);
    overflow:hidden;
}
.rr-group-progress-fill{
    height:100%;border-radius:100px;
    background:linear-gradient(90deg,#f59e0b,#fbbf24);
    box-shadow:0 0 8px rgba(245,158,11,.5);
    transition:width .4s;
}

.rr-group-foot{
    display:flex;justify-content:space-between;align-items:flex-end;
    gap:12px;
    padding:12px 16px;
    margin-top:auto;
    background:rgba(0,0,0,.18);
    border-top:1px dashed rgba(255,255,255,.06);
}
.rr-group-foot-item{min-width:0}
.rr-group-foot-right{text-align:right;flex-shrink:0}
.rr-group-foot-lbl{
    font-size:8.5px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.1em;
    margin-bottom:3px;
}
.rr-group-foot-val{
    font-size:15px;font-weight:800;color:#c9a961;
    font-variant-numeric:tabular-nums;
    letter-spacing:-.02em;
}
.rr-group-foot-val-sm{
    font-size:11px;font-weight:700;color:#9a97a3;
}

/* ── BACK LINK ─────────────────────────────────── */
.rr-back-link{
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 14px;
    background:rgba(34,34,44,.55);
    border:1px solid rgba(255,255,255,.07);
    border-radius:10px;
    color:#a8a5a0;font-size:12.5px;font-weight:600;
    text-decoration:none;
    margin-bottom:14px;
    transition:all .2s cubic-bezier(.4,0,.2,1);
}
.rr-back-link:hover{
    background:rgba(201,169,97,.08);
    border-color:rgba(201,169,97,.28);
    color:#c9a961;
    transform:translateX(-3px);
}

/* ── STORE HEADER ──────────────────────────────── */
.rr-store-header{
    display:flex;align-items:center;gap:16px;
    padding:18px 20px;
    background:rgba(34,34,44,.5);
    border:1px solid rgba(255,255,255,.06);
    border-radius:14px;
    margin-bottom:14px;
}
.rr-store-header-avatar{
    width:56px;height:56px;border-radius:14px;
    background:linear-gradient(135deg,#c9a961,#8a5f36);
    color:#fff;
    display:grid;place-items:center;
    font-size:18px;font-weight:800;letter-spacing:-.02em;
    flex-shrink:0;overflow:hidden;
    box-shadow:0 8px 20px -8px rgba(201,169,97,.5);
}
.rr-store-header-avatar img{width:100%;height:100%;object-fit:cover}
.rr-store-header-info{flex:1;min-width:0}
.rr-store-header-name{
    font-size:18px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.025em;line-height:1.2;
    margin-bottom:6px;
}
.rr-store-header-meta{
    display:flex;align-items:center;gap:6px;
    font-size:12px;color:#8a8792;flex-wrap:wrap;
    font-weight:500;
}

/* ── TABLE ─────────────────────────────────────── */
.rr-card{
    background:rgba(34,34,44,.5);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.06);
    border-radius:14px;
    overflow:hidden;
}
.rr-table{width:100%;border-collapse:collapse}
.rr-table thead th{
    text-align:left;
    font-size:10.5px;font-weight:800;
    color:#7a7a85;
    text-transform:uppercase;
    letter-spacing:.1em;
    padding:13px 18px;
    border-bottom:1px solid rgba(255,255,255,.05);
    background:rgba(0,0,0,.2);
    white-space:nowrap;
}
.rr-table tbody td{
    padding:14px 18px;
    font-size:12.5px;color:#a8a5a0;
    border-bottom:1px solid rgba(38,38,46,.5);
    vertical-align:middle;
}
.rr-table tbody tr{transition:background .15s}
.rr-table tbody tr:hover{background:rgba(201,169,97,.04)}
.rr-table tbody tr:last-child td{border-bottom:none}

.rr-number{
    display:flex;align-items:center;gap:8px;flex-wrap:wrap;
    font-family:ui-monospace,SFMono-Regular,monospace;
    font-size:12.5px;font-weight:700;
    color:#c9a961;letter-spacing:.02em;
}
.rr-date-main{font-weight:700;color:#f5f3f0;font-size:12.5px;margin-bottom:2px}
.rr-date-sub{font-size:10.5px;color:#7a7a85}
.rr-items{
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 10px;
    background:rgba(59,130,246,.1);
    border:1px solid rgba(59,130,246,.22);
    color:#60a5fa;
    font-size:11px;font-weight:700;
    border-radius:6px;
}
.rr-total{
    font-size:14px;font-weight:800;
    color:#c9a961;
    font-variant-numeric:tabular-nums;
    letter-spacing:-.01em;
}
.rr-status{
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 10px;border-radius:6px;
    font-size:10.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.05em;
}
.rr-status::before{
    content:'';width:5px;height:5px;
    border-radius:50%;background:currentColor;
}
.rr-status.pending{background:rgba(245,158,11,.12);color:#fbbf24;border:1px solid rgba(245,158,11,.25)}
.rr-status.approved{background:rgba(34,197,94,.12);color:#4ade80;border:1px solid rgba(34,197,94,.25)}
.rr-status.rejected{background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.25)}
.rr-btn-view{
    display:inline-flex;align-items:center;gap:6px;
    padding:7px 14px;
    background:rgba(201,169,97,.08);
    border:1px solid rgba(201,169,97,.22);
    color:#c9a961;
    font-size:11.5px;font-weight:800;
    border-radius:8px;text-decoration:none;
    transition:all .18s;
}
.rr-btn-view:hover{
    background:rgba(201,169,97,.15);
    border-color:rgba(201,169,97,.4);
    transform:translateX(2px);
}
.rr-new-badge{
    display:inline-flex;align-items:center;gap:4px;
    padding:2px 8px;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    color:#fff;
    font-size:9px;font-weight:800;
    letter-spacing:.06em;
    border-radius:5px;
    text-transform:uppercase;
}
.rr-new-badge::before{
    content:'';width:5px;height:5px;
    border-radius:50%;background:#fff;
    animation:rrPulse 1.5s ease-in-out infinite;
}
@keyframes rrPulse{0%,100%{opacity:1}50%{opacity:.4}}

/* ── AGGREGATED ────────────────────────────────── */
.rr-aggregated{
    background:rgba(34,34,44,.5);
    border:1px solid rgba(201,169,97,.2);
    border-radius:14px;
    overflow:hidden;
}
.rr-aggregated-list{
    display:flex;flex-direction:column;
    padding:10px;gap:8px;
}
.rr-aggregated-item{
    display:block;
    background:rgba(0,0,0,.2);
    border:1px solid rgba(255,255,255,.05);
    border-radius:10px;
    overflow:hidden;
    transition:all .18s;
}
.rr-aggregated-item:hover{
    border-color:rgba(201,169,97,.25);
}
.rr-aggregated-main{
    display:flex;align-items:center;gap:12px;
    padding:12px 14px;
}
.rr-aggregated-icon{
    width:32px;height:32px;border-radius:9px;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.22);
    color:#c9a961;
    display:grid;place-items:center;flex-shrink:0;
}
.rr-aggregated-info{flex:1;min-width:0}
.rr-aggregated-name{
    font-size:13px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;margin-bottom:3px;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.rr-aggregated-sku{
    font-size:10px;color:#7a7a85;
    font-family:ui-monospace,monospace;
    font-weight:600;
}
.rr-aggregated-qty{
    display:inline-flex;align-items:center;gap:5px;
    padding:6px 12px;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.28);
    border-radius:100px;
    flex-shrink:0;
}
.rr-aggregated-qty strong{
    font-size:15px;font-weight:800;
    color:#c9a961;
    font-variant-numeric:tabular-nums;
    letter-spacing:-.02em;
    line-height:1;
}
.rr-aggregated-unit{
    font-size:10.5px;font-weight:700;
    color:rgba(201,169,97,.7);
}

.rr-agg-store-list{
    display:flex;flex-direction:column;
    gap:4px;padding:0 14px 12px;
}
.rr-agg-store-item{
    display:flex;align-items:center;gap:8px;
    padding:6px 10px;
    background:rgba(201,169,97,.04);
    border:1px solid rgba(201,169,97,.1);
    border-radius:7px;
    font-size:11.5px;
}
.rr-agg-store-dot{
    width:5px;height:5px;border-radius:50%;
    background:#c9a961;
    box-shadow:0 0 6px rgba(201,169,97,.7);
    flex-shrink:0;
}
.rr-agg-store-name{
    flex:1;min-width:0;
    font-weight:700;color:#f5f3f0;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.rr-agg-store-code{
    font-size:9.5px;color:#7a7a85;
    font-family:ui-monospace,monospace;
    padding:1px 6px;
    background:rgba(255,255,255,.04);
    border-radius:4px;
    flex-shrink:0;
}
.rr-agg-store-qty{
    font-size:11px;font-weight:800;
    color:#c9a961;
    padding:2px 8px;
    background:rgba(201,169,97,.15);
    border-radius:100px;
    flex-shrink:0;
    font-variant-numeric:tabular-nums;
}
.rr-aggregated-foot{
    padding:12px 18px;
    background:rgba(201,169,97,.06);
    border-top:1px solid rgba(201,169,97,.15);
    text-align:right;
    font-size:12px;font-weight:700;color:#a8a5a0;
}
.rr-aggregated-foot strong{
    font-size:16px;font-weight:800;
    color:#c9a961;
    margin-left:4px;
    font-variant-numeric:tabular-nums;
}

/* ── EMPTY ─────────────────────────────────────── */
.rr-empty{padding:56px 20px;text-align:center}
.rr-empty-icon{
    width:60px;height:60px;
    margin:0 auto 14px;
    border-radius:14px;
    background:rgba(201,169,97,.06);
    border:1px solid rgba(201,169,97,.15);
    display:grid;place-items:center;color:#c9a961;
}
.rr-empty-title{
    font-size:15px;font-weight:800;
    color:#f5f3f0;margin-bottom:5px;
    letter-spacing:-.01em;
}
.rr-empty-text{font-size:12.5px;color:#8a8792;font-weight:500}

.rr-empty-state{
    text-align:center;
    padding:64px 24px;
    background:rgba(34,34,44,.35);
    border:1px dashed rgba(255,255,255,.08);
    border-radius:14px;
}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width:1100px){
    .rr-groups{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width:900px){
    .rr-hero-inner{flex-direction:column;align-items:stretch}
    .rr-hero-right{width:100%;justify-content:space-around;padding:14px 0}
    .rr-stat{padding:0 12px;flex:1}
}
@media (max-width:768px){
    .rr-hero{padding:22px}
    .rr-hero-title{font-size:24px}
    .rr-hero-sub{font-size:12.5px}
    .rr-stat-val{font-size:18px}

    .rr-view-tabs{gap:4px;padding:4px}
    .rr-view-tab{padding:11px 12px;gap:10px}
    .rr-view-tab-title{font-size:12.5px}

    .rr-date-bar{padding:10px}
    .rr-date-btn{padding:6px 12px;font-size:11.5px}
    .rr-date-quick{flex:1 1 100%}

    .rr-filters{flex-direction:column;align-items:stretch}
    .rr-search-wrap{max-width:100%;flex:1}
    .rr-select,.rr-filter-btn,.rr-clear-btn{width:100%;justify-content:center}
    .rr-store-filter-form{width:100%}
    .rr-store-select{width:100%}
    .rr-date-form{width:100%}
    .rr-date-input{width:100%}

    .rr-groups{grid-template-columns:1fr}

    .rr-store-header{padding:16px}
    .rr-store-header-avatar{width:48px;height:48px;font-size:16px}
    .rr-store-header-name{font-size:16px}
}
@media (max-width:480px){
    .rr-hero{padding:20px}
    .rr-hero-title{font-size:22px}
    .rr-hero-right{flex-wrap:wrap;gap:0}
    .rr-stat{flex:1;min-width:70px}
    .rr-stat + .rr-stat::before{height:24px}
    .rr-stat-val{font-size:16px}

    .rr-aggregated-main{padding:10px 12px;gap:10px}
    .rr-aggregated-icon{width:28px;height:28px}
    .rr-aggregated-name{font-size:12px}
    .rr-aggregated-qty{padding:5px 10px}
    .rr-aggregated-qty strong{font-size:13px}
}
</style>
@endpush