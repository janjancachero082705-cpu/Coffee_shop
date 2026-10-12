@extends('portal.layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $store = Auth::guard('store')->user();

    $totalOrders     = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $pendingOrders   = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'pending')->count();
    $totalDelivered  = (float) \App\Models\DeliveryReceipt::where('store_id', $store->id)->sum('total_amount');
    $totalPaid       = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $balance         = max(0, $totalDelivered - $totalPaid);
    $deliveriesCount = \App\Models\DeliveryReceipt::where('store_id', $store->id)->count();
    $recentOrders    = \App\Models\ReorderRequest::where('store_id', $store->id)->latest()->take(3)->get();
@endphp

{{-- ═══ GREETING ═══ --}}
<div class="dash-greet">
    <div class="dash-greet-left">
        <div class="dash-greet-label">
            {{ now()->hour < 12 ? 'Good Morning' : (now()->hour < 18 ? 'Good Afternoon' : 'Good Evening') }}
        </div>
        <h1 class="dash-greet-title">{{ $store->store_name }}</h1>
        <div class="dash-greet-sub">{{ now()->format('l, F j, Y') }}</div>
    </div>
    <div class="dash-greet-badge">
        <span class="dash-greet-dot"></span>
        <span>Active</span>
    </div>
</div>

{{-- ═══ BALANCE HERO ═══ --}}
<div class="dash-balance">
    <div class="dash-balance-glow"></div>

    <div class="dash-balance-top">
        <div class="dash-balance-label">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            Outstanding Balance
        </div>
        <span class="dash-balance-status {{ $balance > 0 ? 'pending' : 'paid' }}">
            <span class="dash-status-dot"></span>
            {{ $balance > 0 ? 'Pending' : 'Paid' }}
        </span>
    </div>

    <div class="dash-balance-value">
        <span class="dash-balance-cur">₱</span>
        <span class="dash-balance-num">{{ number_format($balance, 2) }}</span>
    </div>

    <div class="dash-balance-meta">
        <div class="dash-balance-meta-item">
            <span class="dash-meta-dot green"></span>
            <span class="dash-meta-lbl">Paid</span>
            <strong>₱{{ number_format($totalPaid, 0) }}</strong>
        </div>
        <div class="dash-balance-meta-item">
            <span class="dash-meta-dot gold"></span>
            <span class="dash-meta-lbl">Delivered</span>
            <strong>₱{{ number_format($totalDelivered, 0) }}</strong>
        </div>
    </div>

    <div class="dash-balance-actions">
        <a href="{{ route('portal.orders.create') }}" class="dash-balance-btn primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            New Order
        </a>
        <a href="{{ route('portal.payments.index') }}" class="dash-balance-btn ghost">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Pay Now
        </a>
    </div>
</div>

{{-- ═══ QUICK STATS ═══ --}}
<div class="dash-stats">
    <a href="{{ route('portal.orders.index') }}" class="dash-stat">
        <div class="dash-stat-icon accent">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="dash-stat-num">{{ $totalOrders }}</div>
        <div class="dash-stat-lbl">Orders</div>
    </a>

    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="dash-stat">
        <div class="dash-stat-icon amber">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="dash-stat-num">{{ $pendingOrders }}</div>
        <div class="dash-stat-lbl">Pending</div>
    </a>

    <a href="{{ route('portal.deliveries.index') }}" class="dash-stat">
        <div class="dash-stat-icon blue">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="dash-stat-num">{{ $deliveriesCount }}</div>
        <div class="dash-stat-lbl">Deliveries</div>
    </a>

    <a href="{{ route('portal.profile') }}" class="dash-stat" data-modal="profile">
        <div class="dash-stat-icon green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="dash-stat-num" style="font-size: 14px;">Profile</div>
        <div class="dash-stat-lbl">Account</div>
    </a>
</div>

{{-- ═══ RECENT ORDERS ═══ --}}
<div class="dash-section">
    <div class="dash-section-head">
        <div class="dash-section-icon">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="dash-section-title-wrap">
            <div class="dash-section-title">Recent Orders</div>
            <div class="dash-section-sub">Your latest stock requests</div>
        </div>
        <a href="{{ route('portal.orders.index') }}" class="dash-section-link">
            View All
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </a>
    </div>

    @if($recentOrders->isEmpty())
        <div class="dash-empty">
            <div class="dash-empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                </svg>
            </div>
            <div class="dash-empty-title">No orders yet</div>
            <div class="dash-empty-text">Start by creating your first stock request</div>
            <a href="{{ route('portal.orders.create') }}" class="dash-empty-btn">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Create Order
            </a>
        </div>
    @else
        <div class="dash-orders">
            @foreach($recentOrders as $order)
                <a href="{{ route('portal.orders.show', $order->id) }}" class="dash-order">
                    <div class="dash-order-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                        </svg>
                    </div>
                    <div class="dash-order-info">
                        <div class="dash-order-num">{{ $order->request_number }}</div>
                        <div class="dash-order-meta">
                            {{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }} · {{ $order->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="dash-order-right">
                        <div class="dash-order-amt">₱{{ number_format($order->total_amount, 0) }}</div>
                        <span class="dash-order-badge {{ $order->status }}">{{ $order->status }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- ═══ QUICK ACTIONS ═══ --}}
<div class="dash-section">
    <div class="dash-section-head">
        <div class="dash-section-icon gold">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
        </div>
        <div class="dash-section-title-wrap">
            <div class="dash-section-title">Quick Actions</div>
            <div class="dash-section-sub">Shortcuts para sa imong store</div>
        </div>
    </div>

    <div class="dash-quick-grid">
        <a href="{{ route('portal.orders.create') }}" class="dash-quick">
            <div class="dash-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </div>
            <div class="dash-quick-label">Browse</div>
        </a>

        <a href="{{ route('portal.deliveries.index') }}" class="dash-quick">
            <div class="dash-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="dash-quick-label">Deliveries</div>
        </a>

        <a href="{{ route('portal.reports.index') }}" class="dash-quick">
            <div class="dash-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
                </svg>
            </div>
            <div class="dash-quick-label">Sales</div>
        </a>

        <a href="{{ route('portal.payments.index') }}" class="dash-quick">
            <div class="dash-quick-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <path d="M2 10h20"/>
                </svg>
            </div>
            <div class="dash-quick-label">Payments</div>
        </a>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DASHBOARD — PRO THEME-AWARE DESIGN
   Uses layout theme tokens: --t-bg, --t-card, --t-text,
   --t-accent, --t-border etc. (all 4 themes supported)
   ═══════════════════════════════════════════════════════════ */

/* ═══ GREETING ═══ */
.dash-greet {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 20px;
    animation: dashIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
}
.dash-greet-left { flex: 1; min-width: 0; }
.dash-greet-label {
    font-size: 10.5px;
    font-weight: 800;
    color: var(--t-accent);
    text-transform: uppercase;
    letter-spacing: 0.14em;
    margin-bottom: 6px;
}
.dash-greet-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--t-text);
    letter-spacing: -0.03em;
    line-height: 1.15;
    margin: 0 0 4px 0;
}
.dash-greet-sub {
    font-size: 12px;
    color: var(--t-text-3);
    font-weight: 500;
}
.dash-greet-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 800;
    color: #22c55e;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    white-space: nowrap;
    flex-shrink: 0;
}
.dash-greet-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 8px rgba(34, 197, 94, 0.9);
    animation: dashPulse 2s infinite;
}
@keyframes dashPulse {
    0%, 100% { box-shadow: 0 0 8px rgba(34, 197, 94, 0.9); }
    50% { box-shadow: 0 0 14px rgba(34, 197, 94, 1); }
}

/* ═══ BALANCE HERO ═══ */
.dash-balance {
    position: relative;
    padding: 24px 20px 20px;
    background:
        radial-gradient(circle at 100% 0%, rgba(var(--t-accent-rgb), 0.18) 0%, transparent 55%),
        linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.14) 0%, var(--t-card) 100%);
    border: 1px solid rgba(var(--t-accent-rgb), 0.3);
    border-radius: 22px;
    margin-bottom: 16px;
    overflow: hidden;
    animation: dashIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
    animation-delay: 0.06s;
}
.dash-balance-glow {
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(var(--t-accent-rgb), 0.25), transparent 65%);
    pointer-events: none;
    animation: dashGlow 6s ease-in-out infinite;
}
@keyframes dashGlow {
    0%, 100% { transform: scale(1); opacity: 0.7; }
    50% { transform: scale(1.1); opacity: 1; }
}

.dash-balance-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
}
.dash-balance-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 10.5px;
    font-weight: 800;
    color: var(--t-text-3);
    text-transform: uppercase;
    letter-spacing: 0.12em;
}
.dash-balance-label svg { color: var(--t-accent); }

.dash-balance-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 7px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border: 1px solid;
}
.dash-balance-status.pending {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.35);
    color: #f59e0b;
}
.dash-balance-status.paid {
    background: rgba(34, 197, 94, 0.15);
    border-color: rgba(34, 197, 94, 0.35);
    color: #22c55e;
}
.dash-status-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: currentColor;
    box-shadow: 0 0 6px currentColor;
}

.dash-balance-value {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}
.dash-balance-cur {
    font-size: 22px;
    font-weight: 800;
    color: var(--t-accent);
    opacity: 0.85;
}
.dash-balance-num {
    font-size: 40px;
    font-weight: 800;
    color: var(--t-accent);
    letter-spacing: -0.045em;
    line-height: 1;
    font-variant-numeric: tabular-nums;
    text-shadow: 0 2px 24px rgba(var(--t-accent-rgb), 0.3);
}

.dash-balance-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}
.dash-balance-meta-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding: 10px 12px;
    background: var(--t-card);
    border: 1px solid var(--t-border);
    border-radius: 11px;
}
.dash-meta-lbl {
    font-size: 10px;
    color: var(--t-text-3);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dash-balance-meta-item strong {
    font-size: 14px;
    font-weight: 800;
    color: var(--t-text);
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.01em;
}
.dash-meta-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.dash-meta-dot.green {
    background: #22c55e;
    box-shadow: 0 0 6px rgba(34, 197, 94, 0.8);
}
.dash-meta-dot.gold {
    background: var(--t-accent);
    box-shadow: 0 0 6px rgba(var(--t-accent-rgb), 0.8);
}

.dash-balance-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    position: relative;
    z-index: 1;
}
.dash-balance-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 46px;
    padding: 0 16px;
    border-radius: 12px;
    font-size: 12.5px;
    font-weight: 800;
    text-decoration: none;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    letter-spacing: -0.01em;
}
.dash-balance-btn.primary {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
    color: #ffffff;
    box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.7),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
}
.dash-balance-btn.primary:hover {
    box-shadow: 0 12px 26px -8px rgba(var(--t-accent-rgb), 0.9),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}
.dash-balance-btn.primary:active { transform: scale(0.97); }
.dash-balance-btn.ghost {
    background: rgba(var(--t-accent-rgb), 0.1);
    color: var(--t-text);
    border: 1px solid rgba(var(--t-accent-rgb), 0.28);
}
.dash-balance-btn.ghost:hover {
    background: rgba(var(--t-accent-rgb), 0.18);
    border-color: rgba(var(--t-accent-rgb), 0.45);
}
.dash-balance-btn.ghost:active { transform: scale(0.97); }

/* ═══ QUICK STATS ═══ */
.dash-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 16px;
}
.dash-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 8px;
    background: var(--t-card);
    border: 1px solid var(--t-border);
    border-radius: 15px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    animation: dashIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
}
.dash-stat:nth-child(1) { animation-delay: 0.12s; }
.dash-stat:nth-child(2) { animation-delay: 0.16s; }
.dash-stat:nth-child(3) { animation-delay: 0.20s; }
.dash-stat:nth-child(4) { animation-delay: 0.24s; }
.dash-stat:hover {
    border-color: rgba(var(--t-accent-rgb), 0.4);
    background: rgba(var(--t-accent-rgb), 0.06);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -10px rgba(var(--t-accent-rgb), 0.35);
}
.dash-stat:active { transform: scale(0.96); }

.dash-stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 11px;
    display: grid;
    place-items: center;
    border: 1px solid;
    transition: transform 0.2s;
}
.dash-stat:hover .dash-stat-icon { transform: scale(1.08); }
.dash-stat-icon.accent { background: rgba(var(--t-accent-rgb), 0.14); border-color: rgba(var(--t-accent-rgb), 0.3); color: var(--t-accent); }
.dash-stat-icon.amber  { background: rgba(245, 158, 11, 0.14); border-color: rgba(245, 158, 11, 0.3); color: #f59e0b; }
.dash-stat-icon.blue   { background: rgba(59, 130, 246, 0.14); border-color: rgba(59, 130, 246, 0.3); color: #3b82f6; }
.dash-stat-icon.green  { background: rgba(34, 197, 94, 0.14);  border-color: rgba(34, 197, 94, 0.3);  color: #22c55e; }

.dash-stat-num {
    font-size: 18px;
    font-weight: 800;
    color: var(--t-text);
    line-height: 1;
    letter-spacing: -0.02em;
    font-variant-numeric: tabular-nums;
}
.dash-stat-lbl {
    font-size: 9.5px;
    color: var(--t-text-3);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
    text-align: center;
}

/* ═══ SECTION ═══ */
.dash-section {
    padding: 18px;
    background: var(--t-card);
    border: 1px solid var(--t-border);
    border-radius: 20px;
    margin-bottom: 14px;
    animation: dashIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    animation-delay: 0.3s;
}
.dash-section-head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--t-border);
}
.dash-section-icon {
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
.dash-section-icon.gold {
    background: linear-gradient(135deg, rgba(var(--t-accent-rgb), 0.2), rgba(var(--t-accent-rgb), 0.08));
}
.dash-section-title-wrap { flex: 1; min-width: 0; }
.dash-section-title {
    font-size: 14px;
    font-weight: 800;
    color: var(--t-text);
    letter-spacing: -0.01em;
    line-height: 1.2;
}
.dash-section-sub {
    font-size: 11px;
    color: var(--t-text-3);
    margin-top: 2px;
}
.dash-section-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 10px;
    background: rgba(var(--t-accent-rgb), 0.1);
    border: 1px solid rgba(var(--t-accent-rgb), 0.22);
    border-radius: 8px;
    color: var(--t-accent);
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    flex-shrink: 0;
    transition: all 0.15s;
}
.dash-section-link:hover {
    background: rgba(var(--t-accent-rgb), 0.2);
    transform: translateX(2px);
}
.dash-section-link:active { background: rgba(var(--t-accent-rgb), 0.28); }

/* ═══ ORDERS LIST ═══ */
.dash-orders {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.dash-order {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 11px 12px;
    border-radius: 12px;
    background: rgba(var(--t-accent-rgb), 0.03);
    border: 1px solid var(--t-border);
    text-decoration: none;
    transition: all 0.15s;
    -webkit-tap-highlight-color: transparent;
}
.dash-order:hover {
    background: rgba(var(--t-accent-rgb), 0.08);
    border-color: rgba(var(--t-accent-rgb), 0.28);
    transform: translateX(2px);
}
.dash-order:active {
    background: rgba(var(--t-accent-rgb), 0.12);
    transform: scale(0.99);
}
.dash-order-icon {
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
.dash-order-info { flex: 1; min-width: 0; }
.dash-order-num {
    font-size: 12px;
    font-weight: 800;
    color: var(--t-text);
    font-family: ui-monospace, monospace;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dash-order-meta {
    font-size: 10.5px;
    color: var(--t-text-3);
}
.dash-order-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    flex-shrink: 0;
}
.dash-order-amt {
    font-size: 13px;
    font-weight: 800;
    color: var(--t-accent);
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.01em;
}
.dash-order-badge {
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
.dash-order-badge::before {
    content: '';
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: currentColor;
}
.dash-order-badge.pending  { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border-color: rgba(245, 158, 11, 0.3); }
.dash-order-badge.approved { background: rgba(34, 197, 94, 0.12); color: #22c55e; border-color: rgba(34, 197, 94, 0.3); }
.dash-order-badge.rejected { background: rgba(239, 68, 68, 0.12); color: #ef4444; border-color: rgba(239, 68, 68, 0.3); }

/* ═══ EMPTY STATE ═══ */
.dash-empty {
    text-align: center;
    padding: 30px 16px;
}
.dash-empty-icon {
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
.dash-empty-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--t-text);
    margin-bottom: 4px;
}
.dash-empty-text {
    font-size: 12px;
    color: var(--t-text-3);
    margin-bottom: 14px;
}
.dash-empty-btn {
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
.dash-empty-btn:active { transform: scale(0.97); }

/* ═══ QUICK ACTIONS GRID ═══ */
.dash-quick-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}
.dash-quick {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 6px;
    background: rgba(var(--t-accent-rgb), 0.03);
    border: 1px solid var(--t-border);
    border-radius: 13px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
}
.dash-quick:hover {
    background: rgba(var(--t-accent-rgb), 0.1);
    border-color: rgba(var(--t-accent-rgb), 0.32);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -10px rgba(var(--t-accent-rgb), 0.35);
}
.dash-quick:active {
    transform: scale(0.96);
}
.dash-quick-icon {
    width: 38px;
    height: 38px;
    border-radius: 11px;
    background: rgba(var(--t-accent-rgb), 0.12);
    border: 1px solid rgba(var(--t-accent-rgb), 0.25);
    display: grid;
    place-items: center;
    color: var(--t-accent);
    transition: transform 0.2s;
}
.dash-quick:hover .dash-quick-icon {
    transform: scale(1.08);
    background: rgba(var(--t-accent-rgb), 0.2);
}
.dash-quick-label {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--t-text-2);
    text-align: center;
    line-height: 1.2;
}

/* ═══ ANIMATIONS ═══ */
@keyframes dashIn {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 480px) {
    .dash-balance { padding: 20px 18px 18px; border-radius: 18px; }
    .dash-balance-num { font-size: 34px; }
    .dash-balance-cur { font-size: 18px; }
    .dash-stat-num { font-size: 17px; }
    .dash-section { padding: 16px; border-radius: 16px; }
    .dash-quick-grid { grid-template-columns: repeat(4, 1fr); }
    .dash-quick { padding: 12px 4px; }
}

@media (max-width: 380px) {
    .dash-stats { grid-template-columns: repeat(2, 1fr); }
    .dash-quick-grid { grid-template-columns: repeat(2, 1fr); }
    .dash-greet-title { font-size: 20px; }
    .dash-balance-num { font-size: 30px; }
}
</style>
@endpush