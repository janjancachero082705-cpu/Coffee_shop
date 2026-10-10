@extends('portal.layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $store = Auth::guard('store')->user();

    // Stats
    $totalOrders = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $pendingOrders = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'pending')->count();
    $totalDelivered = (float) \App\Models\DeliveryReceipt::where('store_id', $store->id)->sum('total_amount');
    $totalPaid = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $balance = max(0, $totalDelivered - $totalPaid);
    $deliveriesCount = \App\Models\DeliveryReceipt::where('store_id', $store->id)->count();
    $recentOrders = \App\Models\ReorderRequest::where('store_id', $store->id)->latest()->take(3)->get();
@endphp

{{-- ===== GREETING ===== --}}
<div class="pp-greeting">
    <div>
        <div class="pp-greeting-label">
            {{ now()->hour < 12 ? 'Good Morning' : (now()->hour < 18 ? 'Good Afternoon' : 'Good Evening') }}
        </div>
        <div class="pp-greeting-title">{{ $store->store_name }}</div>
        <div class="pp-greeting-sub">{{ $store->code }} · {{ now()->format('l, F j') }}</div>
    </div>
    <div class="pp-greeting-badge">
        <span class="pp-greeting-dot"></span>
        Active
    </div>
</div>

{{-- ===== BALANCE HERO ===== --}}
<div class="pp-balance">
    <div class="pp-balance-glow"></div>

    <div class="pp-balance-top">
        <div class="pp-balance-label">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            Outstanding Balance
        </div>
        @if($balance > 0)
            <span class="pp-balance-status">Pending</span>
        @else
            <span class="pp-balance-status paid">Paid</span>
        @endif
    </div>

    <div class="pp-balance-value">&#8369;{{ number_format($balance, 2) }}</div>

    <div class="pp-balance-meta">
        <div class="pp-balance-meta-item">
            <span class="pp-dot green"></span>
            <span class="pp-balance-meta-label">Paid</span>
            <strong>&#8369;{{ number_format($totalPaid, 0) }}</strong>
        </div>
        <div class="pp-balance-meta-item">
            <span class="pp-dot gold"></span>
            <span class="pp-balance-meta-label">Delivered</span>
            <strong>&#8369;{{ number_format($totalDelivered, 0) }}</strong>
        </div>
    </div>

    <div class="pp-balance-actions">
        <a href="{{ route('portal.orders.create') }}" class="pp-balance-btn primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            New Order
        </a>
        <a href="{{ route('portal.payments.index') }}" class="pp-balance-btn ghost">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
            Payments
        </a>
    </div>
</div>

{{-- ===== QUICK STATS ===== --}}
<div class="pp-mini-stats">
    <a href="{{ route('portal.orders.index') }}" class="pp-mini-stat">
        <div class="pp-mini-icon gold">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="pp-mini-value">{{ $totalOrders }}</div>
        <div class="pp-mini-label">Orders</div>
    </a>

    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="pp-mini-stat">
        <div class="pp-mini-icon amber">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-mini-value">{{ $pendingOrders }}</div>
        <div class="pp-mini-label">Pending</div>
    </a>

    <a href="{{ route('portal.deliveries.index') }}" class="pp-mini-stat">
        <div class="pp-mini-icon blue">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="pp-mini-value">{{ $deliveriesCount }}</div>
        <div class="pp-mini-label">Deliveries</div>
    </a>

    <a href="{{ route('portal.profile') }}" class="pp-mini-stat" data-modal="profile">
        <div class="pp-mini-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="pp-mini-value">Profile</div>
        <div class="pp-mini-label">Account</div>
    </a>
</div>

{{-- ===== RECENT ORDERS ===== --}}
<div class="pp-section">
    <div class="pp-section-head">
        <div class="pp-section-icon">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="pp-section-title-wrap">
            <div class="pp-section-title">Recent Orders</div>
            <div class="pp-section-sub">Your latest stock requests</div>
        </div>
        <a href="{{ route('portal.orders.index') }}" class="pp-section-link">
            View All
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </a>
    </div>

    @if($recentOrders->isEmpty())
        <div class="pp-empty-inline">
            <div class="pp-empty-inline-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                </svg>
            </div>
            <div class="pp-empty-inline-title">No orders yet</div>
            <div class="pp-empty-inline-text">Start by creating your first stock request</div>
            <a href="{{ route('portal.orders.create') }}" class="pp-empty-inline-btn">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Create Order
            </a>
        </div>
    @else
        <div class="pp-orders">
            @foreach($recentOrders as $order)
                <a href="{{ route('portal.orders.show', $order->id) }}" class="pp-order">
                    <div class="pp-order-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                        </svg>
                    </div>
                    <div class="pp-order-info">
                        <div class="pp-order-number">{{ $order->request_number }}</div>
                        <div class="pp-order-meta">
                            {{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }} · {{ $order->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="pp-order-right">
                        <div class="pp-order-amount">&#8369;{{ number_format($order->total_amount, 0) }}</div>
                        <span class="pp-order-badge {{ $order->status }}">{{ $order->status }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- ===== QUICK ACTIONS ===== --}}
<div class="pp-section">
    <div class="pp-section-head">
        <div class="pp-section-icon gold">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
        </div>
        <div class="pp-section-title-wrap">
            <div class="pp-section-title">Quick Actions</div>
            <div class="pp-section-sub">Shortcuts para sa imong store</div>
        </div>
    </div>

    <div class="pp-quick-grid">
        <a href="{{ route('portal.orders.create') }}" class="pp-quick">
            <div class="pp-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </div>
            <div class="pp-quick-label">Browse</div>
        </a>

        <a href="{{ route('portal.deliveries.index') }}" class="pp-quick">
            <div class="pp-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="pp-quick-label">Deliveries</div>
        </a>

        <a href="{{ route('portal.reports.index') }}" class="pp-quick">
            <div class="pp-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
                </svg>
            </div>
            <div class="pp-quick-label">Sales</div>
        </a>

        <a href="{{ route('portal.payments.index') }}" class="pp-quick">
            <div class="pp-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <path d="M2 10h20"/>
                </svg>
            </div>
            <div class="pp-quick-label">Payments</div>
        </a>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ===== GREETING ===== */
    .pp-greeting {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
        animation: ppSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    }
    .pp-greeting-label {
        font-size: 10.5px;
        font-weight: 800;
        color: var(--t-accent);
        text-transform: uppercase;
        letter-spacing: 0.14em;
        margin-bottom: 6px;
    }
    .pp-greeting-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--t-text);
        letter-spacing: -0.03em;
        line-height: 1.15;
        margin-bottom: 4px;
    }
    .pp-greeting-sub {
        font-size: 12px;
        color: var(--t-text-3);
        font-weight: 500;
    }
    .pp-greeting-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 800;
        color: #22c55e;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        white-space: nowrap;
        flex-shrink: 0;
        backdrop-filter: blur(10px);
    }
    .pp-greeting-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.9);
        animation: dotPulse 2s infinite;
    }
    @keyframes dotPulse {
        0%, 100% { box-shadow: 0 0 8px rgba(34, 197, 94, 0.9); }
        50% { box-shadow: 0 0 14px rgba(34, 197, 94, 1); }
    }

    /* ===== BALANCE HERO ===== */
    .pp-balance {
        position: relative;
        padding: 24px 20px 20px;
        background: linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.22) 0%, var(--t-card) 100%);
        backdrop-filter: blur(28px) saturate(1.6);
        -webkit-backdrop-filter: blur(28px) saturate(1.6);
        border: 1px solid rgba(var(--t-accent-rgb), 0.32);
        border-radius: 22px;
        margin-bottom: 14px;
        overflow: hidden;
        animation: ppSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
        animation-delay: 0.06s;
    }
    .pp-balance-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(var(--t-accent-rgb), 0.28), transparent 65%);
        pointer-events: none;
        animation: glowFloat 6s ease-in-out infinite;
    }
    @keyframes glowFloat {
        0%, 100% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.1); opacity: 1; }
    }

    .pp-balance-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }
    .pp-balance-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10.5px;
        font-weight: 800;
        color: var(--t-text-3);
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }
    .pp-balance-label svg { color: var(--t-accent); }

    .pp-balance-status {
        padding: 4px 10px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 7px;
        font-size: 10px;
        font-weight: 800;
        color: #f59e0b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .pp-balance-status.paid {
        background: rgba(34, 197, 94, 0.15);
        border-color: rgba(34, 197, 94, 0.3);
        color: #22c55e;
    }

    .pp-balance-value {
        font-size: 40px;
        font-weight: 800;
        color: var(--t-accent);
        letter-spacing: -0.045em;
        line-height: 1;
        margin-bottom: 16px;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 2px 24px rgba(var(--t-accent-rgb), 0.3);
        position: relative;
        z-index: 1;
    }

    .pp-balance-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }
    .pp-balance-meta-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
        padding: 10px 12px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 11px;
    }
    .pp-balance-meta-label {
        font-size: 10px;
        color: var(--t-text-3);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pp-balance-meta-item strong {
        font-size: 14px;
        font-weight: 800;
        color: var(--t-text);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.01em;
    }
    .pp-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        display: inline-block;
    }
    .pp-dot.green {
        background: #22c55e;
        box-shadow: 0 0 6px rgba(34, 197, 94, 0.8);
    }
    .pp-dot.gold {
        background: var(--t-accent);
        box-shadow: 0 0 6px rgba(201, 169, 97, 0.8);
    }

    .pp-balance-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        position: relative;
        z-index: 1;
    }
    .pp-balance-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 44px;
        padding: 0 16px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .pp-balance-btn.primary {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        color: #fff;
        box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.7);
    }
    .pp-balance-btn.primary:active { transform: scale(0.97); }
    .pp-balance-btn.ghost {
        background: var(--t-border);
        color: var(--t-text-2);
        border: 1px solid var(--t-border-2);
    }
    .pp-balance-btn.ghost:active {
        background: rgba(255, 255, 255, 0.12);
    }

    /* ===== MINI STATS ===== */
    .pp-mini-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 14px;
    }
    .pp-mini-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 14px 8px;
        background: var(--t-card);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid var(--t-border);
        border-radius: 15px;
        text-decoration: none;
        transition: all 0.2s;
        -webkit-tap-highlight-color: transparent;
        animation: ppSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    }
    .pp-mini-stat:nth-child(1) { animation-delay: 0.12s; }
    .pp-mini-stat:nth-child(2) { animation-delay: 0.16s; }
    .pp-mini-stat:nth-child(3) { animation-delay: 0.20s; }
    .pp-mini-stat:nth-child(4) { animation-delay: 0.24s; }
    .pp-mini-stat:active {
        transform: scale(0.96);
        border-color: rgba(var(--t-accent-rgb), 0.35);
        background: var(--t-card);
    }
    .pp-mini-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        border: 1px solid;
    }
    .pp-mini-icon.gold  { background: rgba(var(--t-accent-rgb), 0.14); border-color: rgba(var(--t-accent-rgb), 0.3); color: var(--t-accent); }
    .pp-mini-icon.amber { background: rgba(245, 158, 11, 0.14); border-color: rgba(245, 158, 11, 0.3); color: #f59e0b; }
    .pp-mini-icon.blue  { background: rgba(59, 130, 246, 0.14); border-color: rgba(59, 130, 246, 0.3); color: #3b82f6; }
    .pp-mini-icon.green { background: rgba(34, 197, 94, 0.14);  border-color: rgba(34, 197, 94, 0.3);  color: #22c55e; }

    .pp-mini-value {
        font-size: 17px;
        font-weight: 800;
        color: var(--t-text);
        line-height: 1;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
    }
    .pp-mini-label {
        font-size: 9.5px;
        color: var(--t-text-3);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        text-align: center;
    }

    /* ===== SECTION ===== */
    .pp-section {
        padding: 16px;
        background: var(--t-card);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid var(--t-border);
        border-radius: 20px;
        margin-bottom: 14px;
        animation: ppSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        animation-delay: 0.3s;
    }
    .pp-section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--t-border);
    }
    .pp-section-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(var(--t-accent-rgb), 0.14);
        border: 1px solid rgba(var(--t-accent-rgb), 0.28);
        display: grid;
        place-items: center;
        color: var(--t-accent);
        flex-shrink: 0;
    }
    .pp-section-icon.gold {
        background: linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.15), rgba(138, 95, 54, 0.15));
    }
    .pp-section-title-wrap { flex: 1; min-width: 0; }
    .pp-section-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--t-text);
        letter-spacing: -0.01em;
        line-height: 1.2;
    }
    .pp-section-sub {
        font-size: 11px;
        color: var(--t-text-3);
        margin-top: 2px;
    }
    .pp-section-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 10px;
        background: rgba(var(--t-accent-rgb), 0.1);
        border: 1px solid rgba(var(--t-accent-rgb), 0.2);
        border-radius: 8px;
        color: var(--t-accent);
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .pp-section-link:active {
        background: rgba(var(--t-accent-rgb), 0.2);
    }

    /* ===== ORDERS LIST ===== */
    .pp-orders {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .pp-order {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px 12px;
        border-radius: 12px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        text-decoration: none;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .pp-order:active {
        background: rgba(var(--t-accent-rgb), 0.1);
        border-color: rgba(var(--t-accent-rgb), 0.25);
        transform: scale(0.99);
    }
    .pp-order-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(var(--t-accent-rgb), 0.12);
        border: 1px solid rgba(var(--t-accent-rgb), 0.22);
        display: grid;
        place-items: center;
        color: var(--t-accent);
        flex-shrink: 0;
    }
    .pp-order-info { flex: 1; min-width: 0; }
    .pp-order-number {
        font-size: 12px;
        font-weight: 800;
        color: var(--t-text);
        font-family: ui-monospace, monospace;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pp-order-meta {
        font-size: 10.5px;
        color: var(--t-text-3);
    }
    .pp-order-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        flex-shrink: 0;
    }
    .pp-order-amount {
        font-size: 13px;
        font-weight: 800;
        color: var(--t-accent);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.01em;
    }
    .pp-order-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border: 1px solid;
    }
    .pp-order-badge::before {
        content: '';
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: currentColor;
    }
    .pp-order-badge.pending  { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border-color: rgba(245, 158, 11, 0.25); }
    .pp-order-badge.approved { background: rgba(34, 197, 94, 0.12); color: #22c55e; border-color: rgba(34, 197, 94, 0.25); }
    .pp-order-badge.rejected { background: rgba(239, 68, 68, 0.12); color: #ef4444; border-color: rgba(239, 68, 68, 0.25); }

    /* ===== EMPTY INLINE ===== */
    .pp-empty-inline {
        text-align: center;
        padding: 30px 16px;
    }
    .pp-empty-inline-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 12px;
        border-radius: 16px;
        background: rgba(var(--t-accent-rgb), 0.1);
        border: 1px solid rgba(var(--t-accent-rgb), 0.25);
        display: grid;
        place-items: center;
        color: var(--t-accent);
    }
    .pp-empty-inline-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--t-text);
        margin-bottom: 4px;
    }
    .pp-empty-inline-text {
        font-size: 12px;
        color: var(--t-text-3);
        margin-bottom: 14px;
    }
    .pp-empty-inline-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        border-radius: 11px;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.7);
    }

    /* ===== QUICK GRID ===== */
    .pp-quick-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    .pp-quick {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 14px 6px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 13px;
        text-decoration: none;
        transition: all 0.2s;
        -webkit-tap-highlight-color: transparent;
    }
    .pp-quick:active {
        background: rgba(var(--t-accent-rgb), 0.1);
        border-color: rgba(var(--t-accent-rgb), 0.3);
        transform: scale(0.96);
    }
    .pp-quick-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: rgba(var(--t-accent-rgb), 0.12);
        border: 1px solid rgba(var(--t-accent-rgb), 0.25);
        display: grid;
        place-items: center;
        color: var(--t-accent);
    }
    .pp-quick-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--t-text-2);
        text-align: center;
        line-height: 1.2;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes ppSlideIn {
        0% { opacity: 0; transform: translateY(12px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 360px) {
        .pp-mini-stats { grid-template-columns: repeat(2, 1fr); }
        .pp-quick-grid { grid-template-columns: repeat(2, 1fr); }
        .pp-greeting-title { font-size: 20px; }
        .pp-balance-value { font-size: 34px; }
    }

    /* ════════════════════════════════════════════════════════
       DASHBOARD — THEME FORCE OVERRIDE
       Ensure tanan elements mo-follow sa active theme
       ════════════════════════════════════════════════════════ */

    /* Balance hero — theme gradient */
    .pp-balance {
        background:
            radial-gradient(circle at 100% 0%, rgba(var(--t-accent-rgb), 0.15) 0%, transparent 55%),
            linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.22) 0%, var(--t-card) 100%) !important;
        border-color: rgba(var(--t-accent-rgb), 0.35) !important;
    }

    /* Greeting */
    .pp-greeting-label {
        color: var(--t-accent) !important;
    }
    .pp-greeting-title {
        color: var(--t-text) !important;
    }
    .pp-greeting-sub {
        color: var(--t-text-3) !important;
    }

    /* Balance value — theme accent */
    .pp-balance-value {
        color: var(--t-accent) !important;
        text-shadow: 0 2px 24px rgba(var(--t-accent-rgb), 0.3) !important;
    }
    .pp-balance-label {
        color: var(--t-text-3) !important;
    }
    .pp-balance-label svg {
        color: var(--t-accent) !important;
    }
    .pp-balance-meta-item strong {
        color: var(--t-text) !important;
    }
    .pp-balance-meta-label {
        color: var(--t-text-3) !important;
    }
    .pp-balance-glow {
        background: radial-gradient(circle, rgba(var(--t-accent-rgb), 0.28), transparent 65%) !important;
    }

    /* Primary balance button */
    .pp-balance-btn.primary {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.7) !important;
    }
    .pp-balance-btn.ghost {
        background: rgba(var(--t-accent-rgb), 0.08) !important;
        color: var(--t-text-2) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
    }

    /* Mini stats */
    .pp-mini-stat {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pp-mini-stat:active {
        background: rgba(var(--t-accent-rgb), 0.08) !important;
        border-color: rgba(var(--t-accent-rgb), 0.35) !important;
    }
    .pp-mini-value {
        color: var(--t-text) !important;
    }
    .pp-mini-label {
        color: var(--t-text-3) !important;
    }

    /* Mini icons — keep semantic colors, match theme for gold */
    .pp-mini-icon.gold {
        background: rgba(var(--t-accent-rgb), 0.14) !important;
        border-color: rgba(var(--t-accent-rgb), 0.3) !important;
        color: var(--t-accent) !important;
    }
    .pp-mini-icon.green {
        background: rgba(34, 197, 94, 0.14) !important;
        border-color: rgba(34, 197, 94, 0.3) !important;
        color: #22c55e !important;
    }
    .pp-mini-icon.amber {
        background: rgba(245, 158, 11, 0.14) !important;
        border-color: rgba(245, 158, 11, 0.3) !important;
        color: #f59e0b !important;
    }
    .pp-mini-icon.blue {
        background: rgba(59, 130, 246, 0.14) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
        color: #3b82f6 !important;
    }

    /* Sections */
    .pp-section {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pp-section-head {
        border-bottom-color: var(--t-border) !important;
    }
    .pp-section-icon {
        background: rgba(var(--t-accent-rgb), 0.14) !important;
        border-color: rgba(var(--t-accent-rgb), 0.28) !important;
        color: var(--t-accent) !important;
    }
    .pp-section-icon.gold {
        background: linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.15), rgba(var(--t-accent-rgb), 0.08)) !important;
    }
    .pp-section-title {
        color: var(--t-text) !important;
    }
    .pp-section-sub {
        color: var(--t-text-3) !important;
    }
    .pp-section-link {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        border-color: rgba(var(--t-accent-rgb), 0.2) !important;
        color: var(--t-accent) !important;
    }
    .pp-section-link:active {
        background: rgba(var(--t-accent-rgb), 0.2) !important;
    }

    /* Order items */
    .pp-order {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pp-order:active {
        background: rgba(var(--t-accent-rgb), 0.08) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
    }
    .pp-order-icon {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.22) !important;
        color: var(--t-accent) !important;
    }
    .pp-order-number {
        color: var(--t-text) !important;
    }
    .pp-order-meta {
        color: var(--t-text-3) !important;
    }
    .pp-order-amount {
        color: var(--t-accent) !important;
    }

    /* Empty state */
    .pp-empty-inline-icon {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
        color: var(--t-accent) !important;
    }
    .pp-empty-inline-title {
        color: var(--t-text) !important;
    }
    .pp-empty-inline-text {
        color: var(--t-text-3) !important;
    }
    .pp-empty-inline-btn {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.7) !important;
    }

    /* Quick actions */
    .pp-quick {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pp-quick:active {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        border-color: rgba(var(--t-accent-rgb), 0.3) !important;
    }
    .pp-quick-icon {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
        color: var(--t-accent) !important;
    }
    .pp-quick-label {
        color: var(--t-text-2) !important;
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