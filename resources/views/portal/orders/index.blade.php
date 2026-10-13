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

<div class="po-head">
    <div class="po-head-left">
        <h1 class="po-title">My Orders</h1>
        <p class="po-sub">Stock requests & status</p>
        <div class="po-header-badges">
            @if($pending > 0)<span class="po-hbadge pending">{{ $pending }} pending</span>@endif
            @if($approved > 0)<span class="po-hbadge approved">{{ $approved }} approved</span>@endif
        </div>
    </div>
    <a href="{{ route('portal.orders.create') }}" class="po-cta">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        Browse
    </a>
</div>

<div class="po-stats">
    <a href="{{ route('portal.orders.index') }}" class="po-stat {{ $status === 'all' ? 'active' : '' }}">
        <div class="po-stat-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg></div>
        <div class="po-stat-value">{{ $total }}</div>
        <div class="po-stat-label">Total</div>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="po-stat {{ $status === 'pending' ? 'active' : '' }}">
        <div class="po-stat-icon amber"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="po-stat-value">{{ $pending }}</div>
        <div class="po-stat-label">Pending</div>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'approved']) }}" class="po-stat {{ $status === 'approved' ? 'active' : '' }}">
        <div class="po-stat-icon green"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        <div class="po-stat-value">{{ $approved }}</div>
        <div class="po-stat-label">Approved</div>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'rejected']) }}" class="po-stat {{ $status === 'rejected' ? 'active' : '' }}">
        <div class="po-stat-icon red"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg></div>
        <div class="po-stat-value">{{ $rejected }}</div>
        <div class="po-stat-label">Rejected</div>
    </a>
</div>

@if($orders->isEmpty())
    <div class="po-empty">
        <div class="po-empty-icon"><svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg></div>
        <div class="po-empty-title">Walay orders</div>
        <div class="po-empty-text">@if($status !== 'all')Walay {{ $status }} orders.@else Start by creating your first stock request.@endif</div>
        <a href="{{ route('portal.orders.create') }}" class="po-empty-btn"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Create Order</a>
    </div>
@else
    <div class="po-list">
        @foreach($orders as $order)
            @php
                $st = strtolower($order->status);
                $badgeMap = ['pending' => ['label' => 'Pending', 'class' => 'pending'], 'approved' => ['label' => 'Approved', 'class' => 'approved'], 'rejected' => ['label' => 'Rejected', 'class' => 'rejected']];
                $badge = $badgeMap[$st] ?? ['label' => ucfirst($st), 'class' => 'default'];
            @endphp
            <a href="{{ route('portal.orders.show', $order->id) }}" class="po-card">
                <div class="po-card-top">
                    <div class="po-card-num">
                        <div class="po-card-num-text">{{ $order->request_number }}</div>
                        <div class="po-card-num-sub"><svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>{{ $order->created_at->format('M d, Y') }}</div>
                    </div>
                    <span class="po-badge po-badge-{{ $badge['class'] }}"><span class="po-badge-dot"></span>{{ $badge['label'] }}</span>
                </div>
                <div class="po-card-mid">
                    <div class="po-card-metric"><div class="po-card-metric-label">Items</div><div class="po-card-metric-value">{{ $order->items->count() }}</div></div>
                    <div class="po-card-metric-divider"></div>
                    <div class="po-card-metric"><div class="po-card-metric-label">Total</div><div class="po-card-metric-value gold">₱{{ number_format($order->total_amount, 2) }}</div></div>
                </div>
                <div class="po-card-bot">
                    <div class="po-card-time"><svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>{{ $order->created_at->diffForHumans() }}</div>
                    <div class="po-card-arrow">View <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></div>
                </div>
            </a>
        @endforeach
    </div>
    @if(method_exists($orders, 'links') && $orders->hasPages())
        <div style="margin-top: 18px;">{{ $orders->withQueryString()->links() }}</div>
    @endif
@endif

@push('styles')
<style>
/* ═══ ORDERS INDEX — PREMIUM COFFEE ═══ */
.po-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
.po-title { font-size: 24px; font-weight: 800; color: #2D1A11; letter-spacing: -0.03em; margin-bottom: 3px; }
.po-sub { font-size: 12px; color: #64748b; }
.po-cta { display: inline-flex; align-items: center; gap: 7px; padding: 12px 20px; background: linear-gradient(135deg, #D84315, #BF360C); border-radius: 13px; color: #FFFFFF; font-size: 13px; font-weight: 800; text-decoration: none; box-shadow: 0 8px 20px -8px rgba(216, 67, 21, 0.6); white-space: nowrap; }
.po-cta:active { transform: scale(0.97); }
.po-header-badges { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
.po-hbadge { display: inline-flex; align-items: center; padding: 4px 11px; border-radius: 7px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; border: 1px solid; }
.po-hbadge.pending { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
.po-hbadge.approved { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }

.po-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 18px; }
.po-stat { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px 8px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 16px; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.po-stat:hover { border-color: #D7CCC8; transform: translateY(-3px); }
.po-stat.active { border-color: #2D1A11; background: #FDFBF7; box-shadow: 0 8px 20px -10px rgba(45, 26, 17, 0.2); }
.po-stat-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: #EFEBE9; color: #2D1A11; }
.po-stat-icon.amber { background: #FEF3C7; color: #B45309; }
.po-stat-icon.green { background: #DCFCE7; color: #15803D; }
.po-stat-icon.red { background: #FEE2E2; color: #B91C1C; }
.po-stat-value { font-size: 20px; font-weight: 800; color: #2D1A11; font-variant-numeric: tabular-nums; line-height: 1; }
.po-stat-label { font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; }

.po-list { display: flex; flex-direction: column; gap: 12px; }
.po-card { display: block; padding: 18px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; text-decoration: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); position: relative; overflow: hidden; }
.po-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: linear-gradient(180deg, #2D1A11, #4E342E); opacity: 0; transition: opacity 0.2s; }
.po-card:hover { border-color: #D7CCC8; transform: translateY(-3px); box-shadow: 0 12px 28px -12px rgba(45, 26, 17, 0.15); }
.po-card:hover::before { opacity: 1; }
.po-card:active { transform: scale(0.99); }
.po-card-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
.po-card-num { min-width: 0; flex: 1; }
.po-card-num-text { font-size: 15px; font-weight: 800; color: #2D1A11; font-family: ui-monospace, monospace; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.po-card-num-sub { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; color: #64748b; font-weight: 600; }
.po-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 100px; font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border: 1px solid; flex-shrink: 0; }
.po-badge-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.po-badge-pending { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
.po-badge-pending .po-badge-dot { background: #B45309; }
.po-badge-approved { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
.po-badge-approved .po-badge-dot { background: #15803D; }
.po-badge-rejected { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }
.po-badge-rejected .po-badge-dot { background: #B91C1C; }
.po-badge-default { background: #F1F5F9; color: #475569; border-color: #E2E8F0; }
.po-badge-default .po-badge-dot { background: #475569; }
.po-card-mid { display: flex; align-items: center; gap: 16px; padding: 12px 14px; background: #FDFBF7; border-radius: 12px; margin-bottom: 12px; }
.po-card-metric { flex: 1; min-width: 0; }
.po-card-metric-label { font-size: 9.5px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
.po-card-metric-value { font-size: 16px; font-weight: 800; color: #2D1A11; font-variant-numeric: tabular-nums; }
.po-card-metric-value.gold { color: #D84315; }
.po-card-metric-divider { width: 1px; height: 28px; background: #EFEBE9; }
.po-card-bot { display: flex; justify-content: space-between; align-items: center; }
.po-card-time { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; color: #64748b; font-weight: 600; }
.po-card-arrow { display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 800; color: #D84315; }
.po-empty { text-align: center; padding: 60px 20px; background: #FFFFFF; border: 1.5px dashed #D7CCC8; border-radius: 18px; }
.po-empty-icon { width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 20px; background: #FDFBF7; border: 1.5px solid #EFEBE9; color: #D7CCC8; display: grid; place-items: center; }
.po-empty-title { font-size: 17px; font-weight: 800; color: #2D1A11; margin-bottom: 6px; }
.po-empty-text { font-size: 12.5px; color: #64748b; margin-bottom: 20px; }
.po-empty-btn { display: inline-flex; align-items: center; gap: 7px; padding: 12px 22px; background: linear-gradient(135deg, #D84315, #BF360C); border-radius: 13px; color: #FFFFFF; font-size: 12.5px; font-weight: 800; text-decoration: none; box-shadow: 0 8px 20px -8px rgba(216, 67, 21, 0.6); }
.po-empty-btn:active { transform: scale(0.97); }

@media (max-width: 480px) {
    .po-title { font-size: 20px; }
    .po-stats { grid-template-columns: repeat(4, 1fr); gap: 6px; }
    .po-stat { padding: 12px 4px; }
    .po-stat-icon { width: 34px; height: 34px; }
    .po-stat-value { font-size: 17px; }
    .po-stat-label { font-size: 9px; }
    .po-card { padding: 14px; }
}
@media (max-width: 380px) {
    .po-stats { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@endsection