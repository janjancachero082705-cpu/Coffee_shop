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

{{-- HERO --}}
<div class="dv-hero">
    <div class="dv-hero-grid"></div>

    <div class="dv-hero-inner">
        <div class="dv-hero-left">
            <div class="dv-hero-eyebrow">Delivery Management</div>
            <h1 class="dv-hero-title">Deliveries</h1>
            <p class="dv-hero-sub">Tanan nga delivery receipts, grouped by store</p>
        </div>

        <div class="dv-hero-right">
            <div class="dv-stat">
                <div class="dv-stat-val">{{ $stats['total'] }}</div>
                <div class="dv-stat-lbl">Total</div>
            </div>
            <div class="dv-stat">
                <div class="dv-stat-val dv-stat-val-pending">{{ $stats['pending'] }}</div>
                <div class="dv-stat-lbl">Pending</div>
            </div>
            <div class="dv-stat">
                <div class="dv-stat-val dv-stat-val-transit">{{ $stats['out_for_delivery'] ?? 0 }}</div>
                <div class="dv-stat-lbl">In Transit</div>
            </div>
            <div class="dv-stat">
                <div class="dv-stat-val dv-stat-val-delivered">{{ $stats['delivered'] ?? 0 }}</div>
                <div class="dv-stat-lbl">Delivered</div>
            </div>
        </div>
    </div>
</div>

{{-- TOOLBAR --}}
<div class="dv-toolbar">
    <div class="dv-tabs">
        @php
            $tabList = [
                'all'              => 'All',
                'pending'          => 'Pending',
                'out_for_delivery' => 'In Transit',
                'delivered'        => 'Delivered',
                'unpaid'           => 'With Balance',
            ];
        @endphp
        @foreach($tabList as $key => $label)
            <a href="{{ route('deliveries.index', array_merge(request()->except(['tab', 'page']), ['tab' => $key])) }}"
               class="dv-tab {{ $tab === $key ? 'active' : '' }}">
                <span>{{ $label }}</span>
                <span class="dv-tab-count">{{ $tabCounts[$key] ?? 0 }}</span>
            </a>
        @endforeach
    </div>
</div>

{{-- FILTERS --}}
<div class="dv-filters">
    <form method="GET" class="dv-filter-form">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="dv-search">
            <svg class="dv-search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR #, store..." class="dv-search-input">
        </div>

        <select name="store" class="dv-select">
            <option value="">All Stores</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}" {{ request('store') == $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
            @endforeach
        </select>

        <input type="date" name="from" value="{{ request('from') }}" class="dv-date" placeholder="From">
        <input type="date" name="to" value="{{ request('to') }}" class="dv-date" placeholder="To">

        <button type="submit" class="dv-btn-filter">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>

        @if(request()->hasAny(['search', 'store', 'from', 'to']))
            <a href="{{ route('deliveries.index', ['tab' => $tab]) }}" class="dv-btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif

        <div class="dv-view-toggle">
            <button type="button" class="dv-view-btn active" data-view="list" title="List View">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </button>
            <button type="button" class="dv-view-btn" data-view="table" title="Table View">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="18"/><rect x="14" y="3" width="7" height="18"/></svg>
            </button>
        </div>
    </form>
</div>

{{-- CONTENT --}}
@if($deliveries->isEmpty())
    <div class="dv-empty">
        <div class="dv-empty-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="dv-empty-title">Walay deliveries</div>
        <div class="dv-empty-text">
            @if(request()->hasAny(['search', 'store', 'from', 'to']) || $tab !== 'all')
                Try adjusting your filters or tab
            @else
                Create your first delivery receipt to get started
            @endif
        </div>
    </div>
@else

@php
    $grouped = $deliveries->groupBy('store_id');
@endphp

{{-- LIST VIEW --}}
<div id="listView" class="view-content">
    <div class="dv-groups">
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

            <div class="dv-group" id="sg-{{ $storeId }}" data-store="{{ $storeId }}">
                <div class="dv-group-head" data-toggle-group="{{ $storeId }}">
                    <div class="dv-group-avatar">
                        @if($store->logo_url ?? null)
                            <img src="{{ $store->avatar_url }}" alt="">
                        @else
                            {{ $initials }}
                        @endif
                    </div>

                    <div class="dv-group-info">
                        <div class="dv-group-name">{{ $store->store_name ?? 'Unknown Store' }}</div>
                        <div class="dv-group-meta">
                            <span class="dv-group-code">{{ $store->code ?? '' }}</span>
                            <span class="dv-group-dot">·</span>
                            <span class="dv-group-count">{{ $storeDeliveries->count() }} {{ $storeDeliveries->count() == 1 ? 'delivery' : 'deliveries' }}</span>
                            <span class="dv-group-dot">·</span>
                            <span class="dv-group-time">{{ $latest->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="dv-group-stats">
                        <div class="dv-group-stat">
                            <div class="dv-group-stat-lbl">Total</div>
                            <div class="dv-group-stat-val gold">&#8369;{{ number_format($storeTotal, 0) }}</div>
                        </div>
                        @if($storeBalance > 0)
                            <div class="dv-group-stat">
                                <div class="dv-group-stat-lbl">Balance</div>
                                <div class="dv-group-stat-val amber">&#8369;{{ number_format($storeBalance, 0) }}</div>
                            </div>
                        @else
                            <div class="dv-group-stat">
                                <div class="dv-group-stat-lbl">Status</div>
                                <div class="dv-group-stat-val green">Paid</div>
                            </div>
                        @endif
                    </div>

                    <div class="dv-group-chevron">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </div>
                </div>

                <div class="dv-group-body" id="sgb-{{ $storeId }}">
                    <div class="dv-group-body-stats">
                        <div class="dv-body-stat">
                            <span class="dv-body-stat-lbl">Delivered</span>
                            <span class="dv-body-stat-val green">{{ $storeDelivered }}</span>
                        </div>
                        <div class="dv-body-stat">
                            <span class="dv-body-stat-lbl">Pending</span>
                            <span class="dv-body-stat-val amber">{{ $storePending }}</span>
                        </div>
                        <div class="dv-body-stat">
                            <span class="dv-body-stat-lbl">Total</span>
                            <span class="dv-body-stat-val">{{ $storeDeliveries->count() }}</span>
                        </div>
                    </div>

                    <div class="dv-delivery-list">
                        @foreach($storeDeliveries as $dr)
                            @php
                                $isConfirmed = $dr->customer_confirmed ?? false;
                                $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                                $statusColor = $isConfirmed ? '#22c55e' : ($isOut ? '#3b82f6' : '#f59e0b');
                                $statusLabel = $isConfirmed ? 'Delivered' : ($isOut ? 'In Transit' : 'Pending');
                            @endphp
                            <a href="{{ route('deliveries.show', $dr) }}" class="dv-card" style="--status-color: {{ $statusColor }};">
                                <div class="dv-card-accent"></div>

                                <div class="dv-card-head">
                                    <div class="dv-card-left">
                                        <div class="dv-card-title">{{ $dr->dr_number }}</div>
                                        <div class="dv-card-sub">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                                    </div>
                                    <span class="dv-status" style="background:{{ $statusColor }}1a; color:{{ $statusColor }}; border-color:{{ $statusColor }}40;">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="dv-card-meta">
                                    <div class="dv-meta-item">
                                        <div class="dv-meta-label">Items</div>
                                        <div class="dv-meta-value">{{ $dr->items->count() }}</div>
                                    </div>
                                    <div class="dv-meta-item">
                                        <div class="dv-meta-label">Total</div>
                                        <div class="dv-meta-value gold">&#8369;{{ number_format($dr->total_amount, 2) }}</div>
                                    </div>
                                    <div class="dv-meta-item">
                                        <div class="dv-meta-label">Balance</div>
                                        <div class="dv-meta-value {{ $dr->balance > 0 ? 'amber' : 'green' }}">&#8369;{{ number_format($dr->balance, 2) }}</div>
                                    </div>
                                </div>

                                <div class="dv-card-foot">
                                    <div class="dv-card-time">{{ $dr->created_at->diffForHumans() }}</div>
                                    <div class="dv-card-arrow">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- TABLE VIEW --}}
<div id="tableView" class="view-content" style="display:none;">
    <div class="dv-table-wrap">
        <table class="dv-table">
            <thead>
                <tr>
                    <th>DR Number</th>
                    <th>Store</th>
                    <th>Date</th>
                    <th style="text-align:center;">Items</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:right;">Balance</th>
                    <th>Status</th>
                    <th style="width:60px; text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveries as $dr)
                    @php
                        $isConfirmed = $dr->customer_confirmed ?? false;
                        $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                    @endphp
                    <tr class="dv-row" onclick="window.location='{{ route('deliveries.show', $dr) }}'">
                        <td><div class="dv-cell-mono gold">{{ $dr->dr_number }}</div></td>
                        <td>
                            <div class="dv-cell-primary">{{ $dr->store->store_name ?? '-' }}</div>
                            <div class="dv-cell-mono muted">{{ $dr->store->code ?? '' }}</div>
                        </td>
                        <td>
                            <div class="dv-cell-primary">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                            <div class="dv-cell-mono muted">{{ $dr->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="text-align:center;">
                            <span class="dv-items-badge">{{ $dr->items->count() }}</span>
                        </td>
                        <td style="text-align:right;" class="dv-money">₱{{ number_format($dr->total_amount, 2) }}</td>
                        <td style="text-align:right;" class="dv-money {{ $dr->balance > 0 ? 'amber' : 'green' }}">₱{{ number_format($dr->balance, 2) }}</td>
                        <td>
                            @if($isConfirmed)
                                <span class="dv-badge dv-badge-delivered"><span class="dv-badge-dot"></span>Delivered</span>
                            @elseif($isOut)
                                <span class="dv-badge dv-badge-transit"><span class="dv-badge-dot"></span>In Transit</span>
                            @elseif($dr->status === 'partial')
                                <span class="dv-badge dv-badge-partial"><span class="dv-badge-dot"></span>Partial</span>
                            @elseif($dr->status === 'paid')
                                <span class="dv-badge dv-badge-paid"><span class="dv-badge-dot"></span>Paid</span>
                            @else
                                <span class="dv-badge dv-badge-pending"><span class="dv-badge-dot"></span>Pending</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('deliveries.show', $dr) }}" class="dv-btn-icon" onclick="event.stopPropagation();" title="View">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if(method_exists($deliveries, 'links') && $deliveries->hasPages())
    <div class="dv-pagination">{{ $deliveries->withQueryString()->links() }}</div>
@endif

@endif

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════ */
/* DELIVERIES INDEX — CLEAN & PREMIUM              */
/* ═══════════════════════════════════════════════ */

/* ── HERO ──────────────────────────────────────── */
.dv-hero{position:relative;padding:32px;background:#16161d;border:1px solid rgba(255,255,255,.06);border-radius:18px;margin-bottom:20px;overflow:hidden}
.dv-hero-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(201,169,97,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(201,169,97,.04) 1px,transparent 1px);background-size:40px 40px;mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);-webkit-mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);pointer-events:none}
.dv-hero-inner{position:relative;z-index:1;display:flex;align-items:flex-end;justify-content:space-between;gap:32px;flex-wrap:wrap}
.dv-hero-left{flex:1;min-width:240px}
.dv-hero-eyebrow{display:inline-block;font-size:10.5px;font-weight:800;color:#c9a961;text-transform:uppercase;letter-spacing:.16em;margin-bottom:14px;padding-left:14px;position:relative}
.dv-hero-eyebrow::before{content:'';position:absolute;left:0;top:50%;width:6px;height:1px;background:#c9a961;transform:translateY(-50%)}
.dv-hero-title{font-size:34px;font-weight:800;color:#f5f3f0;letter-spacing:-.04em;line-height:1.05;margin:0 0 10px}
.dv-hero-sub{font-size:13.5px;color:#8a8792;font-weight:500;line-height:1.5;margin:0;max-width:440px}

.dv-hero-right{display:flex;align-items:center;gap:0;padding:16px 4px;border-top:1px solid rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.06)}
.dv-stat{padding:0 22px;text-align:center;position:relative}
.dv-stat + .dv-stat::before{content:'';position:absolute;left:0;top:50%;width:1px;height:28px;background:rgba(255,255,255,.08);transform:translateY(-50%)}
.dv-stat-val{font-size:22px;font-weight:800;color:#f5f3f0;letter-spacing:-.03em;line-height:1;font-variant-numeric:tabular-nums}
.dv-stat-val-pending{color:#fbbf24}
.dv-stat-val-transit{color:#60a5fa}
.dv-stat-val-delivered{color:#4ade80}
.dv-stat-lbl{font-size:9.5px;font-weight:700;color:#7a7a85;text-transform:uppercase;letter-spacing:.14em;margin-top:6px}

/* ── TOOLBAR / TABS ────────────────────────────── */
.dv-toolbar{margin-bottom:14px}
.dv-tabs{display:flex;gap:4px;padding:4px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:12px;overflow-x:auto;scrollbar-width:none;flex-wrap:nowrap}
.dv-tabs::-webkit-scrollbar{display:none}
.dv-tab{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;background:transparent;border-radius:9px;font-size:12.5px;font-weight:700;color:#8a8792;text-decoration:none;white-space:nowrap;transition:all .18s;flex-shrink:0}
.dv-tab:hover{background:rgba(255,255,255,.04);color:#d4d1cc}
.dv-tab.active{background:rgba(201,169,97,.12);color:#c9a961;box-shadow:inset 0 0 0 1px rgba(201,169,97,.25)}
.dv-tab-count{padding:1px 7px;min-width:20px;background:rgba(255,255,255,.06);color:#8a8792;font-size:10.5px;font-weight:800;border-radius:100px;text-align:center}
.dv-tab.active .dv-tab-count{background:rgba(201,169,97,.2);color:#c9a961}

/* ── FILTERS ───────────────────────────────────── */
.dv-filters{margin-bottom:20px}
.dv-filter-form{display:flex;gap:8px;padding:10px;background:rgba(34,34,44,.4);border:1px solid rgba(255,255,255,.06);border-radius:12px;flex-wrap:wrap;align-items:center}

.dv-search{position:relative;display:flex;align-items:center;flex:1 1 200px;min-width:160px;max-width:280px}
.dv-search-icon{position:absolute;left:11px;color:#7a7a85;pointer-events:none}
.dv-search-input{width:100%;height:34px;padding:0 12px 0 34px;background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.08);border-radius:9px;color:#f5f3f0;font-size:12.5px;font-family:inherit;outline:none;transition:all .18s;box-sizing:border-box}
.dv-search-input::placeholder{color:#6b6862}
.dv-search-input:focus{border-color:#c9a961;box-shadow:0 0 0 3px rgba(201,169,97,.1);background:rgba(201,169,97,.03)}
.dv-search:focus-within .dv-search-icon{color:#c9a961}

.dv-select,
.dv-date{height:34px;padding:0 28px 0 12px;background-color:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.08);border-radius:9px;color:#f5f3f0;font-size:12.5px;font-weight:600;font-family:inherit;cursor:pointer;outline:none;transition:all .18s;box-sizing:border-box;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%237a7a85' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 10px center;background-size:10px}
.dv-select{min-width:130px;max-width:160px}
.dv-date{min-width:120px;max-width:135px;padding-right:10px;background-image:none}
.dv-date::-webkit-calendar-picker-indicator{filter:invert(.5);cursor:pointer}
.dv-select:focus,
.dv-date:focus{border-color:#c9a961;box-shadow:0 0 0 3px rgba(201,169,97,.1)}

.dv-btn-filter{display:inline-flex;align-items:center;justify-content:center;gap:5px;height:34px;padding:0 14px;background:linear-gradient(135deg,#c9a961,#a9784a);color:#0f0f14;font-size:12.5px;font-weight:800;border:none;border-radius:9px;cursor:pointer;transition:all .18s;flex-shrink:0}
.dv-btn-filter:hover{filter:brightness(1.05);box-shadow:0 4px 12px -4px rgba(201,169,97,.5)}

.dv-btn-clear{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;padding:0;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:9px;color:#a8a5a0;text-decoration:none;flex-shrink:0;transition:all .18s}
.dv-btn-clear:hover{background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.3);color:#ef4444}

.dv-view-toggle{display:inline-flex;gap:3px;padding:3px;background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.06);border-radius:9px;margin-left:auto;flex-shrink:0}
.dv-view-btn{width:28px;height:28px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;background:transparent;border:none;color:#7a7a85;cursor:pointer;transition:all .15s}
.dv-view-btn:hover{background:rgba(255,255,255,.05);color:#d4d1cc}
.dv-view-btn.active{background:linear-gradient(135deg,#c9a961,#a9784a);color:#0f0f14}

/* ── STORE GROUPS ──────────────────────────────── */
.dv-groups{display:flex;flex-direction:column;gap:10px}

.dv-group{background:rgba(34,34,44,.45);border:1px solid rgba(255,255,255,.06);border-radius:14px;overflow:hidden;transition:all .22s cubic-bezier(.4,0,.2,1)}
.dv-group.expanded{background:rgba(34,34,44,.7);border-color:rgba(201,169,97,.28);box-shadow:0 12px 28px -16px rgba(0,0,0,.6)}

.dv-group-head{display:grid;grid-template-columns:48px 1fr auto 20px;gap:14px;align-items:center;padding:16px 18px;cursor:pointer;user-select:none;-webkit-tap-highlight-color:transparent;transition:background .15s}
.dv-group-head:hover{background:rgba(255,255,255,.02)}
.dv-group-head:active{background:rgba(255,255,255,.04)}

.dv-group-avatar{width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#c9a961,#8a5f36);color:#fff;display:grid;place-items:center;font-size:15px;font-weight:900;letter-spacing:-.02em;flex-shrink:0;overflow:hidden;box-shadow:0 6px 14px -6px rgba(201,169,97,.5)}
.dv-group-avatar img{width:100%;height:100%;object-fit:cover}

.dv-group-info{min-width:0}
.dv-group-name{font-size:15px;font-weight:800;color:#f5f3f0;letter-spacing:-.02em;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dv-group-meta{display:flex;align-items:center;gap:6px;font-size:11.5px;color:#7a7a85;flex-wrap:wrap}
.dv-group-code{font-family:ui-monospace,monospace;font-weight:700}
.dv-group-dot{color:#52525b}
.dv-group-count{color:#c9a961;font-weight:700}
.dv-group-time{color:#7a7a85}

.dv-group-stats{display:flex;gap:18px;flex-shrink:0}
.dv-group-stat{text-align:right}
.dv-group-stat-lbl{font-size:9.5px;font-weight:700;color:#7a7a85;text-transform:uppercase;letter-spacing:.1em;margin-bottom:3px}
.dv-group-stat-val{font-size:14px;font-weight:800;color:#f5f3f0;font-variant-numeric:tabular-nums;letter-spacing:-.01em}
.dv-group-stat-val.gold{color:#c9a961}
.dv-group-stat-val.amber{color:#fbbf24}
.dv-group-stat-val.green{color:#4ade80}

.dv-group-chevron{color:#7a7a85;transition:transform .25s cubic-bezier(.4,0,.2,1);flex-shrink:0;pointer-events:none}
.dv-group.expanded .dv-group-chevron{transform:rotate(180deg);color:#c9a961}

.dv-group-body{max-height:0;overflow:hidden;transition:max-height .4s cubic-bezier(.4,0,.2,1);border-top:1px solid transparent}
.dv-group.expanded .dv-group-body{max-height:5000px;border-top-color:rgba(255,255,255,.04)}

.dv-group-body-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding:14px 18px 10px}
.dv-body-stat{display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.04);border-radius:9px}
.dv-body-stat-lbl{font-size:10.5px;font-weight:700;color:#7a7a85}
.dv-body-stat-val{font-size:13px;font-weight:800;color:#f5f3f0;font-variant-numeric:tabular-nums}
.dv-body-stat-val.green{color:#4ade80}
.dv-body-stat-val.amber{color:#fbbf24}

/* ── DELIVERY CARD ─────────────────────────────── */
.dv-delivery-list{display:flex;flex-direction:column;gap:6px;padding:0 18px 18px}
.dv-card{position:relative;display:block;background:rgba(0,0,0,.22);border:1px solid rgba(255,255,255,.05);border-radius:10px;padding:11px 13px;text-decoration:none;color:inherit;transition:all .18s cubic-bezier(.4,0,.2,1);overflow:hidden}
.dv-card-accent{position:absolute;left:0;top:0;bottom:0;width:2px;background:var(--status-color);opacity:.7;transition:opacity .18s}
.dv-card:hover{background:rgba(0,0,0,.35);border-color:rgba(255,255,255,.1);transform:translateX(2px)}
.dv-card:hover .dv-card-accent{opacity:1;width:3px}

.dv-card-head{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px dashed rgba(255,255,255,.05)}
.dv-card-left{min-width:0}
.dv-card-title{font-size:12.5px;font-weight:800;color:#f5f3f0;font-family:ui-monospace,SFMono-Regular,monospace;letter-spacing:-.01em}
.dv-card-sub{font-size:10.5px;color:#7a7a85;font-weight:500;margin-top:2px}
.dv-status{padding:3px 8px;border-radius:6px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;border:1px solid;white-space:nowrap;flex-shrink:0}

.dv-card-meta{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding:7px 9px;background:rgba(255,255,255,.02);border-radius:7px;margin-bottom:7px}
.dv-meta-item{min-width:0}
.dv-meta-label{font-size:8.5px;color:#71717a;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:2px}
.dv-meta-value{font-size:11.5px;font-weight:800;color:#f5f3f0;font-variant-numeric:tabular-nums;letter-spacing:-.01em}
.dv-meta-value.gold{color:#c9a961}
.dv-meta-value.green{color:#4ade80}
.dv-meta-value.amber{color:#fbbf24}

.dv-card-foot{display:flex;justify-content:space-between;align-items:center}
.dv-card-time{font-size:10px;color:#71717a;font-weight:500}
.dv-card-arrow{color:#52525b;transition:all .18s}
.dv-card:hover .dv-card-arrow{color:#c9a961;transform:translateX(3px)}

/* ── TABLE VIEW ────────────────────────────────── */
.dv-table-wrap{background:rgba(34,34,44,.45);border:1px solid rgba(255,255,255,.06);border-radius:14px;overflow:hidden}
.dv-table{width:100%;border-collapse:collapse;min-width:800px}
.dv-table{display:table}
.dv-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
.dv-table thead th{padding:12px 16px;text-align:left;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#7a7a85;background:rgba(0,0,0,.25);border-bottom:1px solid rgba(255,255,255,.05);white-space:nowrap}
.dv-row{border-bottom:1px solid rgba(255,255,255,.04);cursor:pointer;transition:background .15s}
.dv-row:hover{background:rgba(201,169,97,.04)}
.dv-row:last-child{border-bottom:none}
.dv-table td{padding:12px 16px;font-size:12.5px;vertical-align:middle}

.dv-cell-primary{font-weight:700;color:#f5f3f0;font-size:12.5px}
.dv-cell-mono{font-family:ui-monospace,SFMono-Regular,monospace;font-size:11.5px;font-weight:700;margin-top:2px}
.dv-cell-mono.gold{color:#c9a961;font-size:12.5px;font-weight:800}
.dv-cell-mono.muted{color:#7a7a85;font-weight:500}
.dv-money{font-variant-numeric:tabular-nums;font-weight:800;font-family:ui-monospace,SFMono-Regular,monospace;white-space:nowrap;color:#f5f3f0;letter-spacing:-.01em}
.dv-money.green{color:#4ade80}
.dv-money.amber{color:#fbbf24}
.dv-items-badge{display:inline-flex;align-items:center;justify-content:center;min-width:26px;height:26px;padding:0 8px;background:rgba(201,169,97,.12);border:1px solid rgba(201,169,97,.22);border-radius:7px;color:#c9a961;font-size:11px;font-weight:800;font-family:ui-monospace,monospace}

.dv-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:100px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap}
.dv-badge-dot{width:5px;height:5px;border-radius:50%;background:currentColor}
.dv-badge-delivered{background:rgba(34,197,94,.12);color:#4ade80;border:1px solid rgba(34,197,94,.25)}
.dv-badge-transit{background:rgba(59,130,246,.12);color:#60a5fa;border:1px solid rgba(59,130,246,.25)}
.dv-badge-pending{background:rgba(245,158,11,.12);color:#fbbf24;border:1px solid rgba(245,158,11,.25)}
.dv-badge-partial{background:rgba(139,92,246,.12);color:#a78bfa;border:1px solid rgba(139,92,246,.25)}
.dv-badge-paid{background:rgba(34,197,94,.12);color:#4ade80;border:1px solid rgba(34,197,94,.25)}

.dv-btn-icon{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);color:#a8a5a0;text-decoration:none;transition:all .18s}
.dv-btn-icon:hover{background:rgba(201,169,97,.15);border-color:rgba(201,169,97,.3);color:#c9a961;transform:translateX(2px)}

/* ── EMPTY ─────────────────────────────────────── */
.dv-empty{text-align:center;padding:64px 24px;background:rgba(34,34,44,.3);border:1px dashed rgba(255,255,255,.08);border-radius:14px}
.dv-empty-icon{width:60px;height:60px;margin:0 auto 14px;border-radius:14px;background:rgba(201,169,97,.06);border:1px solid rgba(201,169,97,.15);color:#c9a961;display:grid;place-items:center}
.dv-empty-title{font-size:15px;font-weight:800;color:#f5f3f0;margin-bottom:4px;letter-spacing:-.01em}
.dv-empty-text{font-size:12.5px;color:#8a8792;font-weight:500}

.dv-pagination{margin-top:20px;display:flex;justify-content:center}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width:900px){
    .dv-hero-inner{flex-direction:column;align-items:stretch}
    .dv-hero-right{width:100%;justify-content:space-around;padding:14px 0}
    .dv-stat{padding:0 12px;flex:1}
}
@media (max-width:768px){
    .dv-hero{padding:24px}
    .dv-hero-title{font-size:26px}
    .dv-hero-sub{font-size:12.5px}
    .dv-stat-val{font-size:18px}
    .dv-stat-lbl{font-size:9px}

    .dv-filter-form{flex-direction:column;align-items:stretch}
    .dv-search{max-width:100%;width:100%;flex:1 1 100%}
    .dv-select,.dv-date{max-width:100%;width:100%;flex:1}
    .dv-btn-filter{width:100%}
    .dv-btn-clear{width:100%}
    .dv-view-toggle{width:100%;justify-content:center;margin-left:0}

    .dv-group-head{grid-template-columns:42px 1fr 20px;gap:10px;padding:14px}
    .dv-group-stats{display:none}
    .dv-group-avatar{width:42px;height:42px;font-size:13px}
    .dv-group-name{font-size:14px}
    .dv-group-body-stats{grid-template-columns:repeat(3,1fr);padding:12px 14px 8px}
    .dv-delivery-list{padding:0 14px 14px}
    .dv-card-meta{gap:6px;padding:6px 8px}
    .dv-meta-value{font-size:11px}
}
@media (max-width:480px){
    .dv-hero{padding:20px}
    .dv-hero-title{font-size:22px}
    .dv-hero-right{flex-wrap:wrap;gap:0}
    .dv-stat{flex:1;min-width:70px}
    .dv-stat + .dv-stat::before{height:24px}
    .dv-stat-val{font-size:17px}
    .dv-tab{padding:8px 12px;font-size:11.5px}
    .dv-tab-count{font-size:10px;padding:1px 6px;min-width:18px}
    .dv-body-stat-lbl{font-size:9.5px}
    .dv-body-stat-val{font-size:12px}
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    // View toggle
    const viewBtns = document.querySelectorAll('.dv-view-btn');
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

    try {
        const saved = localStorage.getItem('deliveries_view');
        if (saved && (saved === 'list' || saved === 'table')) {
            setView(saved);
        }
    } catch(e) {}

    // ═══ STORE GROUP ACCORDION ═══
    document.addEventListener('click', function(e) {
        var head = e.target.closest('[data-toggle-group]');
        if (!head) return;
        e.preventDefault();
        var storeId = head.getAttribute('data-toggle-group');
        var group = document.getElementById('sg-' + storeId);
        if (group) {
            group.classList.toggle('expanded');
        }
    });
})();
</script>
@endpush