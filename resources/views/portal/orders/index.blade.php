@extends('portal.layouts.app')

@section('title', 'My Orders')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $total    = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $pending  = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'pending')->count();
    $approved = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'approved')->count();
    $rejected = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'rejected')->count();
    $status   = request('status', 'all');
@endphp

{{-- HEADER --}}
<div class="po-head">
    <div class="po-head-left">
        <h1 class="po-title">My Orders</h1>
        <p class="po-sub">Stock requests & status</p>
    </div>
    <a href="{{ route('portal.orders.create') }}" class="po-cta">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round">
            <path d="M4 7h16M4 12h16M4 17h16"/>
        </svg>
        Browse
    </a>
</div>

{{-- STATS --}}
<div class="po-stats">
    <a href="{{ route('portal.orders.index') }}" class="po-stat {{ $status === 'all' ? 'active' : '' }}">
        <div class="po-stat-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="po-stat-value">{{ $total }}</div>
        <div class="po-stat-label">Total</div>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="po-stat {{ $status === 'pending' ? 'active' : '' }}">
        <div class="po-stat-icon amber">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="po-stat-value">{{ $pending }}</div>
        <div class="po-stat-label">Pending</div>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'approved']) }}" class="po-stat {{ $status === 'approved' ? 'active' : '' }}">
        <div class="po-stat-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="po-stat-value">{{ $approved }}</div>
        <div class="po-stat-label">Approved</div>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'rejected']) }}" class="po-stat {{ $status === 'rejected' ? 'active' : '' }}">
        <div class="po-stat-icon red">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
        </div>
        <div class="po-stat-value">{{ $rejected }}</div>
        <div class="po-stat-label">Rejected</div>
    </a>
</div>

{{-- LIST --}}
@if($orders->isEmpty())
    <div class="po-empty">
        <div class="po-empty-icon">
            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="po-empty-title">Walay orders</div>
        <div class="po-empty-text">
            @if($status !== 'all')
                Walay {{ $status }} orders. Sulayi laing filter.
            @else
                Start by creating your first stock request.
            @endif
        </div>
        <a href="{{ route('portal.orders.create') }}" class="po-empty-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Create Order
        </a>
    </div>
@else
    <div class="po-list">
        @foreach($orders as $order)
            @php
                $st = strtolower($order->status);
                $badgeMap = [
                    'pending'  => ['label' => 'Pending',  'class' => 'pending'],
                    'approved' => ['label' => 'Approved', 'class' => 'approved'],
                    'rejected' => ['label' => 'Rejected', 'class' => 'rejected'],
                ];
                $badge = $badgeMap[$st] ?? ['label' => ucfirst($st), 'class' => 'default'];
                $itemCount = $order->items->count();
            @endphp
            <a href="{{ route('portal.orders.show', $order->id) }}" class="po-card">
                <div class="po-card-top">
                    <div class="po-card-num">
                        <div class="po-card-num-text">{{ $order->request_number }}</div>
                        <div class="po-card-num-sub">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                            {{ $order->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    <span class="po-badge po-badge-{{ $badge['class'] }}">
                        <span class="po-badge-dot"></span>
                        {{ $badge['label'] }}
                    </span>
                </div>

                <div class="po-card-mid">
                    <div class="po-card-metric">
                        <div class="po-card-metric-label">Items</div>
                        <div class="po-card-metric-value">{{ $itemCount }}</div>
                    </div>
                    <div class="po-card-metric-divider"></div>
                    <div class="po-card-metric">
                        <div class="po-card-metric-label">Total</div>
                        <div class="po-card-metric-value gold">&#8369;{{ number_format($order->total_amount, 2) }}</div>
                    </div>
                </div>

                <div class="po-card-bot">
                    <div class="po-card-time">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                        {{ $order->created_at->diffForHumans() }}
                    </div>
                    <div class="po-card-arrow">
                        View
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($orders, 'links') && $orders->hasPages())
        <div style="margin-top: 18px;">{{ $orders->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* ============ PORTAL ORDERS INDEX — PRO MOBILE ============ */

    /* HEADER */
    .po-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .po-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--t-text);
        letter-spacing: -0.03em;
        margin-bottom: 3px;
    }
    .po-sub {
        font-size: 12px;
        color: var(--t-text-3);
    }
    .po-cta {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 18px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        border-radius: 12px;
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 6px 18px -6px rgba(var(--t-accent-rgb), 0.6);
        transition: all 0.18s;
        -webkit-tap-highlight-color: transparent;
        white-space: nowrap;
    }
    .po-cta:hover {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent));
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.7);
    }
    .po-cta:active { transform: scale(0.97); }

    /* STATS */
    .po-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 18px;
    }
    .po-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 14px 8px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.18s;
        -webkit-tap-highlight-color: transparent;
        position: relative;
    }
    .po-stat:hover {
        border-color: rgba(var(--t-accent-rgb), 0.3);
        transform: translateY(-2px);
    }
    .po-stat.active {
        border-color: rgba(var(--t-accent-rgb), 0.5);
        background: linear-gradient(165deg, rgba(var(--t-accent-rgb), 0.12), rgba(21, 18, 15, 0.9));
    }
    .po-stat.active::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 30%; right: 30%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--t-accent), transparent);
        border-radius: 100px;
    }
    .po-stat-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: grid; place-items: center;
        background: rgba(var(--t-accent-rgb), 0.12);
        color: var(--t-accent);
    }
    .po-stat-icon.amber { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .po-stat-icon.green { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .po-stat-icon.red   { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .po-stat-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--t-text);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        line-height: 1;
    }
    .po-stat-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--t-text-3);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    /* LIST */
    .po-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .po-card {
        display: block;
        padding: 16px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.18s;
        -webkit-tap-highlight-color: transparent;
        position: relative;
        overflow: hidden;
    }
    .po-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--t-accent), var(--t-accent-2));
        opacity: 0;
        transition: opacity 0.18s;
    }
    .po-card:hover {
        border-color: rgba(var(--t-accent-rgb), 0.3);
        transform: translateY(-2px);
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.6);
    }
    .po-card:hover::before { opacity: 1; }
    .po-card:active { transform: scale(0.99); }

    .po-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }
    .po-card-num { min-width: 0; flex: 1; }
    .po-card-num-text {
        font-size: 14px;
        font-weight: 800;
        color: var(--t-text);
        font-family: ui-monospace, monospace;
        letter-spacing: -0.01em;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .po-card-num-sub {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: var(--t-text-3);
        font-weight: 600;
    }

    /* BADGE */
    .po-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 100px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border: 1px solid;
        flex-shrink: 0;
    }
    .po-badge-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .po-badge-pending {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.3);
    }
    .po-badge-pending .po-badge-dot { background: #f59e0b; }
    .po-badge-approved {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.3);
    }
    .po-badge-approved .po-badge-dot { background: #22c55e; }
    .po-badge-rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.3);
    }
    .po-badge-rejected .po-badge-dot { background: #ef4444; }
    .po-badge-default {
        background: rgba(113, 113, 122, 0.12);
        color: #a1a1aa;
        border-color: rgba(113, 113, 122, 0.3);
    }
    .po-badge-default .po-badge-dot { background: #a1a1aa; }

    /* MID */
    .po-card-mid {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 11px;
        margin-bottom: 12px;
    }
    .po-card-metric { flex: 1; min-width: 0; }
    .po-card-metric-label {
        font-size: 9.5px;
        font-weight: 800;
        color: var(--t-text-3);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .po-card-metric-value {
        font-size: 16px;
        font-weight: 800;
        color: var(--t-text);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .po-card-metric-value.gold { color: var(--t-accent); }
    .po-card-metric-divider {
        width: 1px;
        height: 28px;
        background: var(--t-border);
    }

    /* BOT */
    .po-card-bot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .po-card-time {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: var(--t-text-3);
        font-weight: 600;
    }
    .po-card-arrow {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 800;
        color: var(--t-accent);
        letter-spacing: 0.02em;
    }

    /* EMPTY */
    .po-empty {
        text-align: center;
        padding: 50px 20px 40px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 16px;
    }
    .po-empty-icon {
        width: 72px; height: 72px;
        margin: 0 auto 16px;
        border-radius: 20px;
        background: rgba(var(--t-accent-rgb), 0.1);
        color: rgba(var(--t-accent-rgb), 0.6);
        display: grid; place-items: center;
        border: 1px solid rgba(var(--t-accent-rgb), 0.15);
    }
    .po-empty-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--t-text);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }
    .po-empty-text {
        font-size: 12.5px;
        color: var(--t-text-3);
        line-height: 1.5;
        margin-bottom: 20px;
        max-width: 280px;
        margin-left: auto;
        margin-right: auto;
    }
    .po-empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 20px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        border-radius: 12px;
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 6px 18px -6px rgba(var(--t-accent-rgb), 0.5);
        transition: all 0.18s;
        -webkit-tap-highlight-color: transparent;
    }
    .po-empty-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.7);
    }
    .po-empty-btn:active { transform: scale(0.97); }

    /* RESPONSIVE */
    @media (max-width: 480px) {
        .po-title { font-size: 19px; }
        .po-cta { padding: 10px 14px; font-size: 12px; }
        .po-stats { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .po-stat { padding: 12px 8px; }
        .po-stat-value { font-size: 18px; }
        .po-card { padding: 14px; }
        .po-card-num-text { font-size: 13px; }
        .po-card-mid { padding: 10px 12px; gap: 12px; }
        .po-card-metric-value { font-size: 15px; }
    }

    /* ════════════════════════════════════════════════════════
       ORDERS INDEX — THEME FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Page title + subtitle */
    .po-title { color: var(--t-text) !important; }
    .po-sub { color: var(--t-text-3) !important; }

    /* Browse CTA button */
    .po-cta {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px -6px rgba(var(--t-accent-rgb), 0.6) !important;
    }
    .po-cta:hover {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.7) !important;
    }

    /* Stat cards */
    .po-stat {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .po-stat:hover {
        border-color: rgba(var(--t-accent-rgb), 0.3) !important;
    }
    .po-stat.active {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        border-color: rgba(var(--t-accent-rgb), 0.5) !important;
    }
    .po-stat.active::after {
        background: linear-gradient(90deg, transparent, var(--t-accent), transparent) !important;
    }
    .po-stat-value {
        color: var(--t-text) !important;
    }
    .po-stat-label {
        color: var(--t-text-3) !important;
    }

    /* Stat icons — theme accent + semantic */
    .po-stat-icon {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        color: var(--t-accent) !important;
    }
    .po-stat-icon.amber {
        background: rgba(245, 158, 11, 0.12) !important;
        color: #f59e0b !important;
    }
    .po-stat-icon.green {
        background: rgba(34, 197, 94, 0.12) !important;
        color: #22c55e !important;
    }
    .po-stat-icon.red {
        background: rgba(239, 68, 68, 0.12) !important;
        color: #ef4444 !important;
    }

    /* Order cards */
    .po-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .po-card:hover {
        border-color: rgba(var(--t-accent-rgb), 0.3) !important;
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.5) !important;
    }
    .po-card::before {
        background: linear-gradient(180deg, var(--t-accent), var(--t-accent-2)) !important;
    }

    /* Card number + date */
    .po-card-num-text {
        color: var(--t-text) !important;
    }
    .po-card-num-sub {
        color: var(--t-text-3) !important;
    }

    /* Card metrics */
    .po-card-mid {
        background: rgba(var(--t-accent-rgb), 0.04) !important;
    }
    .po-card-metric-label {
        color: var(--t-text-3) !important;
    }
    .po-card-metric-value {
        color: var(--t-text) !important;
    }
    .po-card-metric-value.gold {
        color: var(--t-accent) !important;
    }
    .po-card-metric-divider {
        background: var(--t-border) !important;
    }

    /* Card bottom */
    .po-card-time {
        color: var(--t-text-3) !important;
    }
    .po-card-arrow {
        color: var(--t-accent) !important;
    }

    /* Empty state */
    .po-empty {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .po-empty-icon {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        border-color: rgba(var(--t-accent-rgb), 0.2) !important;
        color: var(--t-accent) !important;
    }
    .po-empty-title {
        color: var(--t-text) !important;
    }
    .po-empty-text {
        color: var(--t-text-3) !important;
    }
    .po-empty-btn {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px -6px rgba(var(--t-accent-rgb), 0.5) !important;
    }

    /* Badges — keep semantic colors (still visible sa tanan theme) */
    .po-badge-pending {
        background: rgba(245, 158, 11, 0.15) !important;
        color: #f59e0b !important;
        border-color: rgba(245, 158, 11, 0.35) !important;
    }
    .po-badge-approved {
        background: rgba(34, 197, 94, 0.15) !important;
        color: #22c55e !important;
        border-color: rgba(34, 197, 94, 0.35) !important;
    }
    .po-badge-rejected {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #ef4444 !important;
        border-color: rgba(239, 68, 68, 0.35) !important;
    }
    .po-badge-default {
        background: rgba(113, 113, 122, 0.15) !important;
        color: #a1a1aa !important;
        border-color: rgba(113, 113, 122, 0.35) !important;
    }

    /* Light theme boost — mas vivid ang badges sa light bg */
    html[data-theme="light"] .po-badge-pending,
    html[data-theme="blue"] .po-badge-pending {
        background: rgba(245, 158, 11, 0.18) !important;
        color: #b45309 !important;
    }
    html[data-theme="light"] .po-badge-approved,
    html[data-theme="blue"] .po-badge-approved {
        background: rgba(34, 197, 94, 0.18) !important;
        color: #15803d !important;
    }
    html[data-theme="light"] .po-badge-rejected,
    html[data-theme="blue"] .po-badge-rejected {
        background: rgba(239, 68, 68, 0.18) !important;
        color: #b91c1c !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — EMERALD GREEN FORCE OVERRIDE
       Bisag unsang gold hardcoded → emerald
       ════════════════════════════════════════════════════════ */

    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #059669 !important;
    }

    /* Force emerald sa tanan accent colors sa light theme */
    html[data-theme="light"] .p-main *[style*="color: #c9a961"] {
        color: #10b981 !important;
    }

    /* Kill any gold shadows */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.5) !important;
    }

    /* Override gold gradient backgrounds */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"] {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }

    /* Force all spans/divs inside cards dark */
    html[data-theme="light"] .p-main,
    html[data-theme="light"] .p-main *:not([class*="badge"]):not([class*="status"]):not([class*="pill"]):not([class*="text-"]) {
        /* Fallback */
    }

    /* Headings */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f1e17 !important;
    }

    /* All text classes */
    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f1e17 !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #6b7f75 !important;
    }

    /* Inline hardcoded white → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f1e17 !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — PURE SLATE FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Gold/green/brown hardcoded colors → slate */
    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="#10b981"],
    html[data-theme="light"] .p-main *[style*="#059669"],
    html[data-theme="light"] .p-main *[style*="#a9784a"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #475569 !important;
    }

    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(16, 185, 129"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"] {
        color: #475569 !important;
    }

    /* Gradient override */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #10b981"] {
        background: linear-gradient(135deg, #475569, #334155) !important;
    }

    /* Gold shadow → slate shadow */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(71, 85, 105, 0.4) !important;
    }

    /* All text — dark */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #64748b !important;
    }

    /* Hardcoded white text → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f172a !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       DARK MODE — PURE BLACK FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Kill all gold/emerald/purple accents sa dark mode */
    html[data-theme="dark"] .p-main *[style*="#c9a961"],
    html[data-theme="dark"] .p-main *[style*="#10b981"],
    html[data-theme="dark"] .p-main *[style*="#8b5cf6"],
    html[data-theme="dark"] .p-main *[style*="#a9784a"],
    html[data-theme="dark"] .p-main *[style*="#8a5f36"],
    html[data-theme="dark"] .p-main *[style*="#b8944d"],
    html[data-theme="dark"] .p-main *[style*="#ec4899"] {
        color: #ffffff !important;
    }

    /* Gradient → white */
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #10b981"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #8b5cf6"] {
        background: linear-gradient(135deg, #ffffff, #e5e5e5) !important;
    }

    /* All text light */
    html[data-theme="dark"] .p-main h1,
    html[data-theme="dark"] .p-main h2,
    html[data-theme="dark"] .p-main h3,
    html[data-theme="dark"] .p-main h4 {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="title"],
    html[data-theme="dark"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="name"],
    html[data-theme="dark"] .p-main strong,
    html[data-theme="dark"] .p-main b {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="dark"] .p-main [class*="meta"],
    html[data-theme="dark"] .p-main [class*="desc"],
    html[data-theme="dark"] .p-main [class*="hint"] {
        color: #a3a3a3 !important;
    }

    /* Any hardcoded dark text → white */
    html[data-theme="dark"] .p-main *[style*="color: #0f172a"],
    html[data-theme="dark"] .p-main *[style*="color:#0f172a"],
    html[data-theme="dark"] .p-main *[style*="color: #1a1a1f"],
    html[data-theme="dark"] .p-main *[style*="color: #000"],
    html[data-theme="dark"] .p-main *[style*="color:#000"],
    html[data-theme="dark"] .p-main *[style*="color: black"],
    html[data-theme="dark"] .p-main *[style*="color:black"] {
        color: #ffffff !important;
    }

    /* Any hardcoded light bg → dark */
    html[data-theme="dark"] .p-main *[style*="background: #ffffff"],
    html[data-theme="dark"] .p-main *[style*="background:#ffffff"],
    html[data-theme="dark"] .p-main *[style*="background: white"],
    html[data-theme="dark"] .p-main *[style*="background: #f8fafc"],
    html[data-theme="dark"] .p-main *[style*="background: #f1f5f9"] {
        background: #0a0a0a !important;
    }</style>
@endpush