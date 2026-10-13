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
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
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
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
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
   DASHBOARD — PREMIUM ROAST STYLE
   Elegant, warm, high-end coffee aesthetic
   ═══════════════════════════════════════════════════════════ */

/* ═══ GREETING ═══ */
.dash-greet {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 24px;
    animation: dashIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
}
.dash-greet-left { flex: 1; min-width: 0; }
.dash-greet-label {
    font-size: 10.5px;
    font-weight: 800;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 0.16em;
    margin-bottom: 8px;
}
.dash-greet-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.035em;
    line-height: 1.15;
    margin: 0 0 6px 0;
}
.dash-greet-sub {
    font-size: 12.5px;
    color: var(--text-muted);
    font-weight: 500;
}
.dash-greet-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: #DCFCE7;
    border: 1px solid #BBF7D0;
    border-radius: 100px;
    font-size: 10.5px;
    font-weight: 800;
    color: #15803D;
    text-transform: uppercase;
    letter-spacing: 0.08em;
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
    padding: 28px 24px 24px;
    background: linear-gradient(135deg, #2D1A11 0%, #4E342E 100%);
    border-radius: 24px;
    margin-bottom: 20px;
    overflow: hidden;
    animation: dashIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
    animation-delay: 0.06s;
    box-shadow: 0 20px 40px -12px rgba(45, 26, 17, 0.5);
}
.dash-balance-glow {
    position: absolute;
    top: -60px;
    right: -60px;
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(216, 67, 21, 0.25), transparent 65%);
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
    margin-bottom: 12px;
    position: relative;
    z-index: 1;
}
.dash-balance-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 10.5px;
    font-weight: 800;
    color: var(--primary-soft);
    text-transform: uppercase;
    letter-spacing: 0.14em;
}
.dash-balance-label svg { color: var(--accent-light); }

.dash-balance-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border: 1px solid;
}
.dash-balance-status.pending {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.4);
    color: #FBBF24;
}
.dash-balance-status.paid {
    background: rgba(34, 197, 94, 0.15);
    border-color: rgba(34, 197, 94, 0.4);
    color: #4ADE80;
}
.dash-status-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: currentColor;
    box-shadow: 0 0 8px currentColor;
}

.dash-balance-value {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}
.dash-balance-cur {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary-soft);
}
.dash-balance-num {
    font-size: 44px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.045em;
    line-height: 1;
    font-variant-numeric: tabular-nums;
    text-shadow: 0 2px 30px rgba(216, 67, 21, 0.4);
}

.dash-balance-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}
.dash-balance-meta-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 12px 14px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 14px;
    backdrop-filter: blur(4px);
}
.dash-meta-lbl {
    font-size: 10px;
    color: var(--primary-soft);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dash-balance-meta-item strong {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.01em;
}
.dash-meta-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.dash-meta-dot.green { background: #4ADE80; box-shadow: 0 0 8px rgba(74, 222, 128, 0.8); }
.dash-meta-dot.gold  { background: var(--accent-light); box-shadow: 0 0 8px rgba(255, 112, 67, 0.8); }

.dash-balance-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    position: relative;
    z-index: 1;
}
.dash-balance-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 50px;
    padding: 0 18px;
    border-radius: 14px;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    letter-spacing: -0.01em;
}
.dash-balance-btn.primary {
    background: linear-gradient(135deg, #D84315, #BF360C);
    color: #ffffff;
    box-shadow: 0 8px 24px -6px rgba(216, 67, 21, 0.6);
}
.dash-balance-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px -6px rgba(216, 67, 21, 0.8);
}
.dash-balance-btn.primary:active { transform: scale(0.97); }
.dash-balance-btn.ghost {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.dash-balance-btn.ghost:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
}
.dash-balance-btn.ghost:active { transform: scale(0.97); }

/* ═══ QUICK STATS ═══ */
.dash-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}
.dash-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 16px 8px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    animation: dashIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    box-shadow: var(--shadow-sm);
}
.dash-stat:nth-child(1) { animation-delay: 0.12s; }
.dash-stat:nth-child(2) { animation-delay: 0.16s; }
.dash-stat:nth-child(3) { animation-delay: 0.20s; }
.dash-stat:nth-child(4) { animation-delay: 0.24s; }
.dash-stat:hover {
    border-color: var(--primary);
    background: #FFFFFF;
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -10px rgba(45, 26, 17, 0.15);
}
.dash-stat:active { transform: scale(0.96); }

.dash-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    border: 1px solid;
    transition: transform 0.2s;
}
.dash-stat:hover .dash-stat-icon { transform: scale(1.1); }
.dash-stat-icon.accent { background: #EFEBE9; border-color: #D7CCC8; color: var(--primary); }
.dash-stat-icon.amber  { background: #FEF3C7; border-color: #FDE68A; color: #B45309; }
.dash-stat-icon.blue   { background: #FDFBF7; border-color: #BFDBFE; color: #1D4ED8; }
.dash-stat-icon.green  { background: #DCFCE7; border-color: #BBF7D0; color: #15803D; }

.dash-stat-num {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
    letter-spacing: -0.02em;
    font-variant-numeric: tabular-nums;
}
.dash-stat-lbl {
    font-size: 10px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 800;
    text-align: center;
}

/* ═══ SECTION ═══ */
.dash-section {
    padding: 22px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 22px;
    margin-bottom: 16px;
    animation: dashIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    animation-delay: 0.3s;
    box-shadow: var(--shadow-sm);
}
.dash-section-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border-light);
}
.dash-section-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #EFEBE9;
    border: 1px solid #D7CCC8;
    display: grid;
    place-items: center;
    color: var(--primary);
    flex-shrink: 0;
}
.dash-section-icon.gold {
    background: #FFCCBC;
    border-color: #FFAB91;
    color: var(--accent);
}
.dash-section-title-wrap { flex: 1; min-width: 0; }
.dash-section-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.dash-section-sub {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-top: 3px;
    font-weight: 500;
}
.dash-section-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 7px 12px;
    background: #FDFBF7;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    color: var(--primary);
    font-size: 11.5px;
    font-weight: 800;
    text-decoration: none;
    flex-shrink: 0;
    transition: all 0.15s;
}
.dash-section-link:hover {
    background: #EFEBE9;
    transform: translateX(2px);
}
.dash-section-link:active { background: #D7CCC8; }

/* ═══ ORDERS LIST ═══ */
.dash-orders {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.dash-order {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    border-radius: 14px;
    background: #FDFBF7;
    border: 1px solid var(--border-light);
    text-decoration: none;
    transition: all 0.2s;
    -webkit-tap-highlight-color: transparent;
}
.dash-order:hover {
    background: #FFFFFF;
    border-color: var(--primary);
    transform: translateX(3px);
    box-shadow: 0 6px 16px -6px rgba(45, 26, 17, 0.1);
}
.dash-order:active {
    background: #EFEBE9;
    transform: scale(0.99);
}
.dash-order-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #EFEBE9;
    border: 1px solid #D7CCC8;
    display: grid;
    place-items: center;
    color: var(--primary);
    flex-shrink: 0;
}
.dash-order-info { flex: 1; min-width: 0; }
.dash-order-num {
    font-size: 13px;
    font-weight: 800;
    color: var(--text-main);
    font-family: ui-monospace, monospace;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dash-order-meta {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
}
.dash-order-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex-shrink: 0;
}
.dash-order-amt {
    font-size: 14px;
    font-weight: 800;
    color: var(--accent);
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.02em;
}
.dash-order-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border: 1px solid;
}
.dash-order-badge::before {
    content: '';
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: currentColor;
}
.dash-order-badge.pending  { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
.dash-order-badge.approved { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
.dash-order-badge.rejected { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }

/* ═══ EMPTY STATE ═══ */
.dash-empty {
    text-align: center;
    padding: 36px 16px;
}
.dash-empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    border-radius: 20px;
    background: #EFEBE9;
    border: 1px solid var(--border-color);
    display: grid;
    place-items: center;
    color: var(--primary);
}
.dash-empty-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 6px;
    letter-spacing: -0.01em;
}
.dash-empty-text {
    font-size: 12.5px;
    color: var(--text-muted);
    margin-bottom: 18px;
    font-weight: 500;
}
.dash-empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #2D1A11, #4E342E);
    border-radius: 12px;
    color: #fff;
    font-size: 12.5px;
    font-weight: 800;
    text-decoration: none;
    box-shadow: 0 8px 20px -6px rgba(45, 26, 17, 0.5);
    transition: all 0.2s;
}
.dash-empty-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 26px -6px rgba(45, 26, 17, 0.7); }
.dash-empty-btn:active { transform: scale(0.97); }

/* ═══ QUICK ACTIONS GRID ═══ */
.dash-quick-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}
.dash-quick {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 16px 6px;
    background: #FDFBF7;
    border: 1px solid var(--border-light);
    border-radius: 14px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
}
.dash-quick:hover {
    background: #FFFFFF;
    border-color: var(--primary);
    transform: translateY(-3px);
    box-shadow: 0 10px 20px -8px rgba(45, 26, 17, 0.12);
}
.dash-quick:active {
    transform: scale(0.96);
}
.dash-quick-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #EFEBE9;
    border: 1px solid #D7CCC8;
    display: grid;
    place-items: center;
    color: var(--primary);
    transition: transform 0.2s;
}
.dash-quick:hover .dash-quick-icon {
    transform: scale(1.1);
    background: #D7CCC8;
}
.dash-quick-label {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-main);
    text-align: center;
    line-height: 1.2;
    letter-spacing: -0.01em;
}

/* ═══ ANIMATIONS ═══ */
@keyframes dashIn {
    0% { opacity: 0; transform: translateY(14px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 480px) {
    .dash-balance { padding: 24px 20px 20px; border-radius: 20px; }
    .dash-balance-num { font-size: 36px; }
    .dash-balance-cur { font-size: 20px; }
    .dash-stat-num { font-size: 18px; }
    .dash-section { padding: 18px; border-radius: 18px; }
    .dash-quick-grid { grid-template-columns: repeat(4, 1fr); }
    .dash-quick { padding: 14px 4px; }
}

@media (max-width: 380px) {
    .dash-stats { grid-template-columns: repeat(2, 1fr); }
    .dash-quick-grid { grid-template-columns: repeat(2, 1fr); }
    .dash-greet-title { font-size: 22px; }
    .dash-balance-num { font-size: 32px; }
}
</style>
@endpush