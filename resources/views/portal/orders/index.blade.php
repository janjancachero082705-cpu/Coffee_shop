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
        color: var(--text-primary);
        letter-spacing: -0.03em;
        margin-bottom: 3px;
    }
    .po-sub {
        font-size: 12px;
        color: var(--text-muted);
    }
    .po-cta {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 18px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-radius: 12px;
        color: #0f0f14;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.6);
        transition: all 0.18s;
        -webkit-tap-highlight-color: transparent;
        white-space: nowrap;
    }
    .po-cta:hover {
        background: linear-gradient(135deg, #d4b672, #c9a961);
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(201, 169, 97, 0.7);
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
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.18s;
        -webkit-tap-highlight-color: transparent;
        position: relative;
    }
    .po-stat:hover {
        border-color: rgba(201, 169, 97, 0.3);
        transform: translateY(-2px);
    }
    .po-stat.active {
        border-color: rgba(201, 169, 97, 0.5);
        background: linear-gradient(165deg, rgba(201, 169, 97, 0.12), rgba(21, 18, 15, 0.9));
    }
    .po-stat.active::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 30%; right: 30%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #c9a961, transparent);
        border-radius: 100px;
    }
    .po-stat-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: grid; place-items: center;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
    }
    .po-stat-icon.amber { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .po-stat-icon.green { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .po-stat-icon.red   { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .po-stat-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        line-height: 1;
    }
    .po-stat-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-muted);
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
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
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
        background: linear-gradient(180deg, #c9a961, #b8944d);
        opacity: 0;
        transition: opacity 0.18s;
    }
    .po-card:hover {
        border-color: rgba(201, 169, 97, 0.3);
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
        color: var(--text-primary);
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
        color: var(--text-muted);
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
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .po-card-metric-value {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .po-card-metric-value.gold { color: #c9a961; }
    .po-card-metric-divider {
        width: 1px;
        height: 28px;
        background: rgba(255, 255, 255, 0.06);
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
        color: var(--text-muted);
        font-weight: 600;
    }
    .po-card-arrow {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: 0.02em;
    }

    /* EMPTY */
    .po-empty {
        text-align: center;
        padding: 50px 20px 40px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
    }
    .po-empty-icon {
        width: 72px; height: 72px;
        margin: 0 auto 16px;
        border-radius: 20px;
        background: rgba(201, 169, 97, 0.1);
        color: rgba(201, 169, 97, 0.6);
        display: grid; place-items: center;
        border: 1px solid rgba(201, 169, 97, 0.15);
    }
    .po-empty-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }
    .po-empty-text {
        font-size: 12.5px;
        color: var(--text-muted);
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
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-radius: 12px;
        color: #0f0f14;
        font-size: 12.5px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.5);
        transition: all 0.18s;
        -webkit-tap-highlight-color: transparent;
    }
    .po-empty-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(201, 169, 97, 0.7);
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
</style>
@endpush