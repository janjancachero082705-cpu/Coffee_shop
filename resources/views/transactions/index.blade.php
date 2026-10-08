@extends('layouts.admin')

@section('title', 'Transactions')
@section('subtitle', 'Real-time activity log sa sistema')

@section('content')

{{-- ========== HERO HEADER ========== --}}
<div class="tx-hero">
    <div class="tx-hero-left">
        <div class="tx-hero-eyebrow">
            <span class="tx-live-dot"></span>
            LIVE ACTIVITY FEED
        </div>
        <h1 class="tx-hero-title">Transactions</h1>
        <p class="tx-hero-sub">Tanan nga events — orders, deliveries, payments, reports</p>
    </div>
    <div class="tx-hero-stats">
        <div class="tx-hero-stat">
            <div class="tx-hero-stat-label">Today</div>
            <div class="tx-hero-stat-value">{{ $stats['total_today'] }}</div>
        </div>
        <div class="tx-hero-divider"></div>
        <div class="tx-hero-stat">
            <div class="tx-hero-stat-label">This Week</div>
            <div class="tx-hero-stat-value">{{ $stats['total_week'] }}</div>
        </div>
        <div class="tx-hero-divider"></div>
        <div class="tx-hero-stat">
            <div class="tx-hero-stat-label">Paid Today</div>
            <div class="tx-hero-stat-value gold">₱{{ number_format($stats['amount_today'], 0) }}</div>
        </div>
    </div>
</div>

{{-- ========== FILTER BAR ========== --}}
<div class="tx-filter-bar">
    <div class="tx-type-tabs">
        @php
            $tabList = [
                'all' => 'All',
                'order' => 'Orders',
                'delivery' => 'Deliveries',
                'payment' => 'Payments',
                'report' => 'Reports',
                'store' => 'Stores',
            ];
        @endphp
        @foreach($tabList as $key => $label)
            <a href="{{ route('transactions.index', array_merge(request()->except(['type', 'page']), ['type' => $key])) }}"
               class="tx-type-tab {{ $type === $key ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <form method="GET" class="tx-filters">
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="tx-search-wrap">
            <svg class="tx-search-icon" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search..." class="tx-search-input" id="txSearchInput">
        </div>

        <select name="store" class="tx-filter-select" onchange="this.form.submit()">
            <option value="">All Stores</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}" {{ $storeId == $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
            @endforeach
        </select>

        <input type="date" name="from" value="{{ $dateFrom }}" class="tx-filter-select tx-date-input" onchange="this.form.submit()" title="From date">
        <input type="date" name="to" value="{{ $dateTo }}" class="tx-filter-select tx-date-input" onchange="this.form.submit()" title="To date">

        @if($search || $storeId || $dateFrom || $dateTo)
            <a href="{{ route('transactions.index', ['type' => $type]) }}" class="tx-clear-btn" title="Clear filters">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif
    </form>
</div>

{{-- ========== TIMELINE ========== --}}
@if($paginator->isEmpty())
    <div class="tx-empty">
        <div class="tx-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="tx-empty-title">Walay transactions</div>
        <div class="tx-empty-sub">
            @if($search || $storeId || $dateFrom || $dateTo || $type !== 'all')
                Try adjusting your filters
            @else
                Activity mo-appear dinhi inig naay new event
            @endif
        </div>
    </div>
@else
    <div class="tx-content-area">
    <div class="tx-timeline">
        @php $lastDate = null; @endphp
        @foreach($paginator as $activity)
            @php
                $dateKey = $activity['created_at']->format('Y-m-d');
                $showDateHeader = $lastDate !== $dateKey;
                $lastDate = $dateKey;

                // Map emoji to SVG icon
                $iconSvg = match($activity['icon']) {
                    '🛒' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
                    '✓'  => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>',
                    '✕'  => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>',
                    '📦' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
                    '🚚' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
                    '💰' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
                    '💵' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>',
                    '📱' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>',
                    '💜' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>',
                    '🏦' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10l9-6 9 6M5 10v10M19 10v10M9 10v10M15 10v10M2 20h20"/></svg>',
                    '📝' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>',
                    '📊' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>',
                    '🏪' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/></svg>',
                    default => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
                };
            @endphp

            @if($showDateHeader)
                <div class="tx-date-header">
                    <div class="tx-date-line"></div>
                    <div class="tx-date-label">
                        @if($activity['created_at']->isToday())
                            Today
                        @elseif($activity['created_at']->isYesterday())
                            Yesterday
                        @else
                            {{ $activity['created_at']->format('M j, Y') }}
                        @endif
                    </div>
                    <div class="tx-date-line"></div>
                </div>
            @endif

            <a href="{{ $activity['url'] }}" class="tx-item" style="--accent: {{ $activity['color'] }};">
                <div class="tx-item-marker">
                    <div class="tx-item-icon">{!! $iconSvg !!}</div>
                </div>

                <div class="tx-item-content">
                    <div class="tx-item-top">
                        <span class="tx-item-title">{{ $activity['title'] }}</span>
                        <span class="tx-item-ref">{{ $activity['reference'] }}</span>
                        <span class="tx-item-time">{{ $activity['created_at']->format('g:i A') }}</span>
                    </div>
                    <div class="tx-item-middle">
                        <span class="tx-item-store">{{ $activity['store_name'] }}</span>
                        @if($activity['store_code'])
                            <span class="tx-item-store-code">{{ $activity['store_code'] }}</span>
                        @endif
                    </div>
                    <div class="tx-item-desc">{{ $activity['description'] }}</div>
                </div>

                @if($activity['amount'] !== null)
                    <div class="tx-item-amount">
                        <span class="tx-item-amount-symbol">₱</span>{{ number_format($activity['amount'], 2) }}
                    </div>
                @endif

                <div class="tx-item-arrow">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </div>
            </a>
        @endforeach
    </div>

    @if($paginator->hasPages())
        <div class="tx-pagination-bar">
            <div class="tx-pagination-info">
                <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
                of <strong>{{ $paginator->total() }}</strong>
            </div>

            <div class="tx-pagination-controls">
                @if($paginator->onFirstPage())
                    <span class="tx-page-btn disabled">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="tx-page-btn">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                @endif

                @foreach($paginator->onEachSide(1)->getUrlRange(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span class="tx-page-num active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="tx-page-num">{{ $page }}</a>
                    @endif
                @endforeach

                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="tx-page-btn">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                @else
                    <span class="tx-page-btn disabled">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                @endif
            </div>

            <div class="tx-pagination-page">
                Page <strong>{{ $paginator->currentPage() }}</strong> / <strong>{{ $paginator->lastPage() }}</strong>
            </div>
        </div>
    @endif
    </div>{{-- /tx-content-area --}}
@endif

@endsection

@push('styles')
<style>
    /* ========== HERO ========== */
    .tx-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 16px 22px;
        margin-bottom: 14px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.08) 0%, rgba(30, 26, 22, 0.4) 100%);
        border: 1px solid rgba(201, 169, 97, 0.15);
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    .tx-hero::before {
        content: '';
        position: absolute;
        top: -100px; right: -100px;
        width: 240px; height: 240px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.12), transparent 70%);
        pointer-events: none;
    }
    .tx-hero-left { position: relative; z-index: 1; }
    .tx-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        background: rgba(201, 169, 97, 0.12);
        border: 1px solid rgba(201, 169, 97, 0.25);
        border-radius: 100px;
        font-size: 9px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: 0.15em;
        margin-bottom: 10px;
    }
    .tx-live-dot {
        width: 5px; height: 5px;
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
        font-size: 22px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.03em;
        line-height: 1.1;
        margin-bottom: 4px;
    }
    .tx-hero-sub {
        font-size: 11.5px;
        color: #a1a1aa;
    }
    .tx-hero-stats {
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        z-index: 1;
        padding-left: 20px;
    }
    .tx-hero-stat { text-align: right; }
    .tx-hero-stat-label {
        font-size: 9px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 3px;
    }
    .tx-hero-stat-value {
        font-size: 17px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .tx-hero-stat-value.gold { color: #c9a961; }
    .tx-hero-divider {
        width: 1px;
        height: 28px;
        background: rgba(255, 255, 255, 0.06);
    }

    /* ========== FILTER BAR ========== */
    .tx-filter-bar {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }
    .tx-type-tabs {
        display: flex;
        gap: 3px;
        padding: 3px;
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .tx-type-tabs::-webkit-scrollbar { display: none; }
    .tx-type-tab {
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 11.5px;
        font-weight: 700;
        color: #a1a1aa;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tx-type-tab:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fafafa;
    }
    .tx-type-tab.active {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }

    .tx-filters {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
        width: 100%;
    }
    .tx-search-wrap {
        flex: 1;
        min-width: 0;
        position: relative;
        display: flex;
        align-items: center;
    }
    .tx-search-icon {
        position: absolute;
        left: 11px;
        color: #71717a;
        pointer-events: none;
    }
    .tx-search-input {
        width: 100%;
        padding: 8px 12px 8px 32px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 9px;
        color: #fafafa;
        font-size: 11.5px;
        font-family: inherit;
        outline: none;
        transition: all 0.15s;
    }
    .tx-search-input::placeholder { color: #71717a; }
    .tx-search-input:focus {
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.1);
    }
    .tx-filter-select {
        padding: 8px 10px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 9px;
        color: #fafafa;
        font-size: 11px;
        font-family: inherit;
        cursor: pointer;
        outline: none;
        transition: all 0.15s;
        min-width: 100px;
        max-width: 140px;
        flex-shrink: 0;
    }
    .tx-filter-select:focus { border-color: #c9a961; }
    .tx-date-input {
        min-width: 108px;
        max-width: 118px;
        font-family: ui-monospace, monospace;
        font-size: 10.5px;
    }
    .tx-date-input::-webkit-calendar-picker-indicator {
        filter: invert(0.5);
        cursor: pointer;
    }
    .tx-clear-btn {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        display: grid;
        place-items: center;
        text-decoration: none;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .tx-clear-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.4);
    }

    /* ========== TIMELINE CONTAINER ========== */
    .tx-timeline {
        position: relative;
        flex: 1;
        min-height: 0;
    }
    
    
    
    

    /* ========== DATE HEADER ========== */
    .tx-date-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0 5px;
    }
    .tx-date-header:first-child { margin-top: 0; }
    .tx-date-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(201, 169, 97, 0.18), transparent);
    }
    .tx-date-label {
        padding: 2px 9px;
        background: rgba(34, 34, 44, 0.75);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 100px;
        font-size: 9px;
        font-weight: 800;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        white-space: nowrap;
    }

    /* ========== COMPACT ACTIVITY ITEM ========== */
    .tx-item {
        display: grid;
        grid-template-columns: 34px 1fr auto auto;
        gap: 10px;
        align-items: center;
        padding: 9px 12px;
        margin-bottom: 3px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.65), rgba(21, 18, 15, 0.65));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-left: 3px solid var(--accent);
        border-radius: 9px;
        text-decoration: none;
        color: inherit;
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .tx-item:hover {
        border-color: rgba(201, 169, 97, 0.25);
        border-left-color: var(--accent);
        background: linear-gradient(165deg, rgba(35, 30, 25, 0.85), rgba(25, 22, 18, 0.85));
        transform: translateX(2px);
        box-shadow: 0 6px 20px -10px rgba(0, 0, 0, 0.5);
    }
    .tx-item:hover .tx-item-arrow {
        color: #c9a961;
        transform: translateX(2px);
    }

    .tx-item-marker {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: color-mix(in srgb, var(--accent) 15%, transparent);
        border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent);
        color: var(--accent);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .tx-item:hover .tx-item-marker {
        transform: scale(1.05);
        box-shadow: 0 4px 12px -4px color-mix(in srgb, var(--accent) 50%, transparent);
    }
    .tx-item-icon {
        display: grid;
        place-items: center;
    }

    .tx-item-content {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .tx-item-top {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        min-width: 0;
    }
    .tx-item-title {
        font-size: 11.5px;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: -0.01em;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tx-item-ref {
        display: inline-flex;
        align-items: center;
        padding: 1px 6px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 4px;
        font-size: 9px;
        font-weight: 700;
        color: #71717a;
        font-family: ui-monospace, monospace;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tx-item-time {
        margin-left: auto;
        font-size: 9.5px;
        font-weight: 600;
        color: #71717a;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .tx-item-middle {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }
    .tx-item-store {
        font-size: 12px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.005em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tx-item-store-code {
        font-family: ui-monospace, monospace;
        font-size: 9px;
        font-weight: 600;
        color: #71717a;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tx-item-desc {
        font-size: 10px;
        color: #71717a;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tx-item-amount {
        font-size: 12.5px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        font-family: ui-monospace, monospace;
        white-space: nowrap;
        flex-shrink: 0;
        padding: 4px 9px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        border-radius: 6px;
    }
    .tx-item-amount-symbol {
        font-size: 10px;
        opacity: 0.7;
        margin-right: 1px;
    }

    .tx-item-arrow {
        color: #52525b;
        transition: all 0.15s;
        flex-shrink: 0;
        display: grid;
        place-items: center;
    }

    /* ========== EMPTY ========== */
    .tx-empty {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px dashed rgba(255, 255, 255, 0.08);
        border-radius: 14px;
    }
    .tx-empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 14px;
        border-radius: 18px;
        background: rgba(201, 169, 97, 0.1);
        border: 1px solid rgba(201, 169, 97, 0.2);
        color: #c9a961;
        display: grid;
        place-items: center;
    }
    .tx-empty-title {
        font-size: 14px;
        font-weight: 800;
        color: #fafafa;
        margin-bottom: 5px;
    }
    .tx-empty-sub {
        font-size: 12px;
        color: #71717a;
    }

    /* ========== PAGINATION BAR ========== */
    .tx-pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        margin-top: 8px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 11px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    .tx-pagination-info {
        font-size: 11px;
        color: #a1a1aa;
        font-weight: 500;
    }
    .tx-pagination-info strong {
        color: #c9a961;
        font-weight: 800;
        font-family: ui-monospace, monospace;
    }
    .tx-pagination-controls {
        display: flex;
        align-items: center;
        gap: 3px;
    }
    .tx-page-btn,
    .tx-page-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        padding: 0 8px;
        border-radius: 7px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: #a1a1aa;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
        font-family: ui-monospace, monospace;
        cursor: pointer;
    }
    .tx-page-btn:hover,
    .tx-page-num:hover {
        background: rgba(201, 169, 97, 0.12);
        border-color: rgba(201, 169, 97, 0.3);
        color: #c9a961;
        transform: translateY(-1px);
    }
    .tx-page-btn.disabled {
        opacity: 0.3;
        cursor: not-allowed;
        pointer-events: none;
    }
    .tx-page-num.active {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-color: transparent;
        color: #0f0f14;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }
    .tx-pagination-page {
        font-size: 11px;
        color: #71717a;
        font-weight: 500;
    }
    .tx-pagination-page strong {
        color: #c9a961;
        font-weight: 800;
        font-family: ui-monospace, monospace;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 900px) {
        .tx-hero {
            flex-direction: column;
            align-items: flex-start;
        }
        .tx-hero-stats {
            padding-left: 0;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            width: 100%;
        }
    }
    @media (max-width: 700px) {
        .tx-hero { padding: 14px 18px; }
        .tx-hero-title { font-size: 19px; }

        .tx-item {
            grid-template-columns: 32px 1fr auto;
            gap: 8px;
            padding: 8px 10px;
        }
        .tx-item-marker { width: 32px; height: 32px; }

        .tx-item-amount {
            grid-column: 2 / 3;
            justify-self: start;
            font-size: 11.5px;
            margin-top: 3px;
        }
        .tx-item-arrow { grid-row: 1; grid-column: 3; }

        .tx-filter-select { flex: 1 1 calc(50% - 3px); min-width: 0; max-width: none; }
        .tx-date-input { flex: 1 1 calc(50% - 3px); min-width: 0; max-width: none; }
        .tx-clear-btn { flex: 0 0 34px; }

        .tx-pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        margin-top: 8px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 11px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    @media (max-width: 480px) {
        .tx-hero-stats { flex-wrap: wrap; gap: 10px; }
        .tx-hero-divider { display: none; }
        .tx-pagination-info { display: none; }
        .tx-item-ref { display: none; }
        .tx-timeline {
        position: relative;
        flex: 1;
        min-height: 0;
    }

    /* ========== FLEX LAYOUT WRAPPER ========== */
    .tx-content-area {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 340px);
        min-height: 500px;
    }
    @media (max-width: 900px) {
        .tx-content-area {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 340px);
        min-height: 500px;
    }
    @media (max-width: 700px) {
        .tx-content-area {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 340px);
        min-height: 500px;
    }
    </style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    var searchInput = document.getElementById('txSearchInput');
    var form = searchInput ? searchInput.closest('form') : null;

    if (searchInput && form) {
        var timer;
        searchInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                form.submit();
            }, 700);
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(timer);
                form.submit();
            }
        });
    }
})();
</script>
@endpush