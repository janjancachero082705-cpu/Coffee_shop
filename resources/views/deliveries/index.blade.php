@extends('layouts.admin')

@section('title', 'Deliveries')
@section('subtitle', 'Delivery receipts for all stores')

@section('actions')
    <a href="{{ route('deliveries.create') }}" class="btn btn-primary btn-sm">+ New Delivery</a>
@endsection

@section('content')

<div class="summary-grid">
    <div class="summary">
        <div class="summary-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['total'] }}</div>
            <div class="summary-label">Total Deliveries</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['pending'] + $stats['partial'] }}</div>
            <div class="summary-label">Pending / Partial</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon red">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['overdue'] }}</div>
            <div class="summary-label">Overdue</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">&#8369;{{ number_format($stats['outstanding']/1000, 1) }}k</div>
            <div class="summary-label">Outstanding</div>
        </div>
    </div>
</div>

<div class="tabs">
    @php
        $tabsList = [
            'all'     => ['label' => 'All Deliveries'],
            'pending' => ['label' => 'Pending'],
            'partial' => ['label' => 'Partial'],
            'paid'    => ['label' => 'Fully Paid'],
            'overdue' => ['label' => 'Overdue'],
        ];
    @endphp

    @foreach($tabsList as $key => $tabItem)
        <a href="{{ route('deliveries.index', array_merge(request()->only(['search','store']), ['tab' => $key])) }}"
           class="tab {{ $tab === $key ? 'active' : '' }}">
            <span>{{ $tabItem['label'] }}</span>
            <span class="tab-count">{{ $tabCounts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<div class="toolbar">
    <form method="GET" class="toolbar-form">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="search-box">
            <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR number or store..." class="search-input">
        </div>

        <select name="store" class="filter-select" onchange="this.form.submit()">
            <option value="">All Stores</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}" {{ request('store')==$s->id?'selected':'' }}>{{ $s->store_name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn-filter">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>

        @if(request()->hasAny(['search','store']))
            <a href="{{ route('deliveries.index', ['tab' => $tab]) }}" class="btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif
    </form>
</div>

@if($deliveries->isEmpty())
    <div class="card">
        <div class="empty">
            <div class="empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="empty-title">No deliveries found</div>
            <div class="empty-text">
                @if(request()->hasAny(['search','store']) || $tab !== 'all')
                    Try adjusting your filters or tab
                @else
                    Create your first delivery receipt to get started
                @endif
            </div>
            @if(!request()->hasAny(['search','store']) && $tab === 'all')
                <a href="{{ route('deliveries.create') }}" class="btn btn-primary">+ New Delivery</a>
            @endif
        </div>
    </div>
@else
    <div class="card" style="padding: 0; overflow: hidden;">
        <table>
            <thead>
                <tr>
                    <th>DR Number</th>
                    <th>Store</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:right;">Balance</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveries as $dr)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('deliveries.show', $dr) }}'">
                        <td>
                            <span style="color:#c9a961; font-family:ui-monospace; font-weight:700; font-size:12px;">{{ $dr->dr_number }}</span>
                        </td>
                        <td>
                            <div style="font-weight:600; color:var(--text-primary);">{{ $dr->store->store_name ?? '-' }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $dr->store->code ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:500; font-size:12px;">{{ $dr->delivery_date }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $dr->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <span style="font-size:12px;">{{ $dr->items->count() }} item{{ $dr->items->count() !== 1 ? 's' : '' }}</span>
                        </td>
                        <td style="text-align:right; font-weight:700; color:var(--text-primary); font-size:13px;">
                            &#8369;{{ number_format($dr->total_amount, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:700; font-size:13px; color:{{ $dr->balance > 0 ? '#f59e0b' : '#22c55e' }};">
                            &#8369;{{ number_format($dr->balance, 2) }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $dr->status }}">{{ ucfirst($dr->status) }}</span>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('deliveries.show', $dr) }}" class="btn btn-ghost btn-sm" onclick="event.stopPropagation();">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(method_exists($deliveries, 'links'))
        <div class="pagination-wrap">{{ $deliveries->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .summary {
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
    }
    .summary:hover {
        transform: translateY(-2px);
        border-color: rgba(169, 120, 74, 0.25);
    }
    .summary-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid; place-items: center;
        color: #c9a961; flex-shrink: 0;
    }
    .summary-icon.green { background: rgba(34, 197, 94, 0.1);  border-color: rgba(34, 197, 94, 0.2);  color: #22c55e; }
    .summary-icon.blue  { background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .summary-icon.amber { background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .summary-icon.red   { background: rgba(239, 68, 68, 0.1);  border-color: rgba(239, 68, 68, 0.2);  color: #ef4444; }
    .summary-value {
        font-size: 20px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em; line-height: 1;
    }
    .summary-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-top: 3px;
    }

    .tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
        padding: 5px;
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        overflow-x: auto;
    }
    .tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.15s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tab:hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
    }
    .tab.active {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.25), rgba(169, 120, 74, 0.15));
        color: #c9a961;
        box-shadow: inset 0 0 0 1px rgba(169, 120, 74, 0.4);
    }
    .tab-count {
        padding: 2px 8px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 800;
        min-width: 22px;
        text-align: center;
        color: var(--text-muted);
    }
    .tab.active .tab-count {
        background: rgba(169, 120, 74, 0.3);
        color: #f0e6dc;
    }

    .toolbar { margin-bottom: 16px; }
    .toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
    }
    .search-box {
        flex: 1; min-width: 0; max-width: 380px;
        position: relative;
        display: flex; align-items: center;
    }
    .search-icon { position: absolute; left: 12px; color: var(--text-muted); pointer-events: none; }
    .search-input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 13px; font-family: inherit; outline: none; transition: all 0.15s;
    }
    .search-input::placeholder { color: var(--text-muted); }
    .search-input:focus {
        border-color: var(--accent);
        background: rgba(20, 20, 26, 0.9);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }

    .filter-select {
        padding: 9px 32px 9px 12px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 13px; font-family: inherit;
        outline: none; cursor: pointer; transition: all 0.15s;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        width: 170px; flex-shrink: 0;
    }
    .filter-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }

    .btn-filter {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 9px 16px;
        background: linear-gradient(135deg, #a9784a, #8a5f36);
        color: #fff; border: none; border-radius: 8px;
        font-size: 13px; font-weight: 600; font-family: inherit;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
        box-shadow: 0 4px 10px -4px rgba(169, 120, 74, 0.5);
    }
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.7);
    }
    .btn-clear {
        width: 36px; height: 36px;
        border-radius: 8px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        display: grid; place-items: center;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
    }
    .btn-clear:hover { background: rgba(239, 68, 68, 0.2); }

    .pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }

    @media (max-width: 1100px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 900px) {
        .toolbar-form { flex-wrap: wrap; }
        .search-box { max-width: 100%; flex: 1 1 100%; }
        .filter-select { flex: 1; width: auto; }
    }
    @media (max-width: 700px)  { .summary-grid { grid-template-columns: 1fr; } }
</style>
@endpush