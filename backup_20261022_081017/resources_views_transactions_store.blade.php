@extends('layouts.admin')

@section('title', $store->store_name . ' — Transactions')
@section('subtitle', 'Activity log for ' . $store->store_name)

@section('content')

@php
    $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
@endphp

<a href="{{ route('transactions.index') }}" class="tx-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    Back to All Transactions
</a>

<div class="tx-hero">
    <div class="tx-hero-avatar">{{ $initials }}</div>
    <div class="tx-hero-info">
        <div class="tx-hero-name">{{ $store->store_name }}</div>
        <div class="tx-hero-meta">
            <span class="tx-hero-code">{{ $store->code }}</span>
            <span class="tx-hero-dot">·</span>
            <span>{{ $totalEvents }} event(s)</span>
        </div>
    </div>
</div>

<div class="tx-filter-bar">
    <form method="GET" class="tx-filter-form">
        <div class="tx-filter-group">
            <label class="tx-filter-lbl">From</label>
            <input type="date" name="from" value="{{ $dateFrom }}" class="tx-filter-input">
        </div>
        <div class="tx-filter-group">
            <label class="tx-filter-lbl">To</label>
            <input type="date" name="to" value="{{ $dateTo }}" class="tx-filter-input">
        </div>
        <button type="submit" class="tx-filter-btn">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>
        @if($dateFrom || $dateTo)
            <a href="{{ route('transactions.store-transactions', $store->id) }}" class="tx-filter-clear">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                Clear
            </a>
        @endif
    </form>
</div>
<div class="tx-stats">
    <div class="tx-stat tx-stat-primary">
        <div class="tx-stat-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
            </svg>
        </div>
        <div class="tx-stat-body">
            <div class="tx-stat-lbl">Total Events</div>
            <div class="tx-stat-val">{{ $totalEvents }}</div>
        </div>
    </div>
    <div class="tx-stat tx-stat-green">
        <div class="tx-stat-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="tx-stat-body">
            <div class="tx-stat-lbl">Total Amount</div>
            <div class="tx-stat-val green">&#8369;{{ number_format($totalAmount, 2) }}</div>
        </div>
    </div>
    <div class="tx-stat tx-stat-amber">
        <div class="tx-stat-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="tx-stat-body">
            <div class="tx-stat-lbl">Latest Activity</div>
            <div class="tx-stat-val amber">
                @if($activities->first() && $activities->first()['time'])
                    {{ \Carbon\Carbon::parse($activities->first()['time'])->diffForHumans() }}
                @else
                    —
                @endif
            </div>
        </div>
    </div>
</div>

<div class="tx-list-card">
    <div class="tx-list-head">
        <div class="tx-list-title">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
            </svg>
            Activity Timeline
        </div>
        <div class="tx-list-count">{{ $totalEvents }} total</div>
    </div>

    @forelse($activities as $act)
        <a href="{{ $act['url'] }}" class="tx-item" style="--accent: {{ $act['color'] }};">
            <div class="tx-item-marker">
                @if($act['icon'] === 'cart')
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                @elseif($act['icon'] === 'truck')
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                @elseif($act['icon'] === 'cash')
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                @else
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                @endif
            </div>
            <div class="tx-item-info">
                <div class="tx-item-title">{{ $act['title'] }}</div>
                <div class="tx-item-desc">{{ $act['description'] ?? $act['desc'] ?? '' }}</div>
            </div>
            <div class="tx-item-time">
                <div class="tx-item-time-val">{{ \Carbon\Carbon::parse($act['time'])->format('M d, Y') }}</div>
                <div class="tx-item-time-ago">{{ \Carbon\Carbon::parse($act['time'])->diffForHumans() }}</div>
            </div>
            @if(($act['amount'] ?? 0) > 0)
                <div class="tx-item-amount">&#8369;{{ number_format($act['amount'], 2) }}</div>
            @endif
        </a>
    @empty
        <div class="tx-empty">Walay transactions pa.</div>
    @endforelse
</div>

@endsection

@push('styles')
<style>
    .tx-back {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; margin-bottom: 16px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        color: #d4d4d8; text-decoration: none;
        font-size: 12px; font-weight: 700;
        transition: all 0.15s;
    }
    .tx-back:hover {
        background: rgba(255,255,255,0.08);
        border-color: rgba(201,169,97,0.3);
        color: #c9a961;
    }
    .tx-hero {
        display: flex; align-items: center; gap: 16px;
        padding: 22px 24px;
        background: linear-gradient(135deg, rgba(30,26,22,0.85), rgba(21,18,15,0.9));
        border: 1px solid rgba(255,255,255,0.06);
        border-left: 3px solid #c9a961;
        border-radius: 16px;
        margin-bottom: 16px;
    }
    .tx-hero-avatar {
        width: 60px; height: 60px; border-radius: 16px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        display: grid; place-items: center;
        font-weight: 800; font-size: 20px;
        flex-shrink: 0;
    }
    .tx-hero-info { flex: 1; min-width: 0; }
    .tx-hero-name {
        font-size: 22px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.02em;
        margin-bottom: 6px;
    }
    .tx-hero-meta {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: #71717a;
    }
    .tx-hero-code { font-family: ui-monospace, monospace; color: #a1a1aa; }
    .tx-hero-dot { color: #52525b; }

    .tx-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }
    .tx-stat {
        display: flex; align-items: center; gap: 12px;
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(30,26,22,0.9), rgba(21,18,15,0.9));
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 14px;
        position: relative; overflow: hidden;
        transition: all 0.2s;
    }
    .tx-stat::before {
        content: ''; position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
    }
    .tx-stat-primary::before { background: linear-gradient(90deg, #c9a961, transparent); }
    .tx-stat-green::before   { background: linear-gradient(90deg, #22c55e, transparent); }
    .tx-stat-amber::before   { background: linear-gradient(90deg, #f59e0b, transparent); }
    .tx-stat:hover { transform: translateY(-2px); border-color: rgba(201,169,97,0.2); }
    .tx-stat-icon {
        width: 38px; height: 38px; border-radius: 11px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .tx-stat-primary .tx-stat-icon { background: rgba(201,169,97,0.15); color: #c9a961; }
    .tx-stat-green   .tx-stat-icon { background: rgba(34,197,94,0.15); color: #22c55e; }
    .tx-stat-amber   .tx-stat-icon { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .tx-stat-body { flex: 1; min-width: 0; }
    .tx-stat-lbl {
        font-size: 10px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .tx-stat-val {
        font-size: 20px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
    }
    .tx-stat-val.green { color: #22c55e; }
    .tx-stat-val.amber { color: #f59e0b; }

    .tx-list-card {
        padding: 18px;
        background: linear-gradient(165deg, rgba(30,26,22,0.9), rgba(21,18,15,0.9));
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
    }
    .tx-list-head {
        display: flex; justify-content: space-between; align-items: center;
        padding-bottom: 14px; margin-bottom: 14px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .tx-list-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 800;
        color: #fafafa; text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .tx-list-title svg { color: #c9a961; }
    .tx-list-count { font-size: 11px; font-weight: 700; color: #71717a; }

    .tx-item {
        display: grid;
        grid-template-columns: 40px 1fr auto auto;
        gap: 14px;
        align-items: center;
        padding: 14px 16px;
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        margin-bottom: 8px;
        text-decoration: none;
        color: inherit;
        transition: all 0.15s;
    }
    .tx-item:hover {
        background: rgba(255,255,255,0.05);
        border-color: var(--accent);
        transform: translateX(3px);
    }
    .tx-item-marker {
        width: 40px; height: 40px; border-radius: 11px;
        background: color-mix(in srgb, var(--accent) 15%, transparent);
        color: var(--accent);
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .tx-item-info { min-width: 0; }
    .tx-item-title {
        font-size: 13px; font-weight: 800;
        color: #fafafa; margin-bottom: 3px;
        font-family: ui-monospace, monospace;
    }
    .tx-item-desc { font-size: 11px; color: #71717a; }
    .tx-item-time { text-align: right; }
    .tx-item-time-val { font-size: 11px; font-weight: 700; color: #a1a1aa; }
    .tx-item-time-ago { font-size: 10px; color: #71717a; margin-top: 2px; }
    .tx-item-amount {
        font-size: 14px; font-weight: 800;
        color: #22c55e; font-variant-numeric: tabular-nums;
        padding-left: 14px;
        border-left: 1px solid rgba(255,255,255,0.06);
    }
    .tx-empty {
        text-align: center; padding: 40px 20px;
        color: #71717a; font-size: 13px;
    }

    @media (max-width: 800px) {
        .tx-stats { grid-template-columns: 1fr; }
        .tx-item { grid-template-columns: 40px 1fr auto; gap: 10px; }
        .tx-item-time { display: none; }
    }

    /* ═══ DATE FILTER BAR ═══ */
    .tx-filter-bar {
        margin-bottom: 16px;
        padding: 10px 12px;
        background: linear-gradient(165deg, rgba(30,26,22,0.6), rgba(21,18,15,0.6));
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
    }
    .tx-filter-form {
        display: flex;
        gap: 8px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .tx-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .tx-filter-lbl {
        font-size: 9px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .tx-filter-input {
        height: 32px;
        padding: 0 10px;
        font-size: 12px;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 8px;
        background: rgba(0,0,0,0.3);
        color: #fafafa;
        box-sizing: border-box;
        min-width: 130px;
    }
    .tx-filter-input:focus {
        outline: none;
        border-color: #c9a961;
        box-shadow: 0 0 0 2px rgba(201,169,97,0.15);
    }
    .tx-filter-input::-webkit-calendar-picker-indicator {
        filter: invert(0.5);
        cursor: pointer;
    }
    .tx-filter-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 32px;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 800;
        border: none;
        border-radius: 8px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        cursor: pointer;
        white-space: nowrap;
        box-sizing: border-box;
    }
    .tx-filter-btn:hover {
        box-shadow: 0 4px 12px -4px rgba(201,169,97,0.5);
    }
    .tx-filter-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        height: 32px;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.04);
        color: #d4d4d8;
        text-decoration: none;
        white-space: nowrap;
        box-sizing: border-box;
    }
    .tx-filter-clear:hover {
        background: rgba(239,68,68,0.15);
        border-color: rgba(239,68,68,0.3);
        color: #ef4444;
    }

    @media (max-width: 700px) {
        .tx-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .tx-filter-input {
            width: 100%;
        }
    }
</style>
@endpush