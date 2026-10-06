@extends('portal.layouts.app')

@section('title', 'My Orders')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $total = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $pending = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'pending')->count();
    $approved = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'approved')->count();
    $rejected = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'rejected')->count();
    $status = request('status', 'all');
@endphp

{{-- HEADER --}}
<div class="pp-head">
    <div class="pp-head-left">
        <div class="pp-title">My Orders</div>
        <div class="pp-sub">Stock requests & status</div>
    </div>
    <a href="{{ route('portal.orders.create') }}" class="pp-action">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        New
    </a>
</div>

{{-- STATS --}}
<div class="pp-stats">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $total }}</div>
        <div class="pp-stat-label">Total</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $pending }}</div>
        <div class="pp-stat-label">Pending</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $approved }}</div>
        <div class="pp-stat-label">Approved</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon red">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $rejected }}</div>
        <div class="pp-stat-label">Rejected</div>
    </div>
</div>

{{-- FILTER TABS --}}
<div class="pp-tabs">
    <a href="{{ route('portal.orders.index') }}" class="pp-tab {{ $status === 'all' ? 'active' : '' }}">
        All <span class="pp-tab-count">{{ $total }}</span>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="pp-tab {{ $status === 'pending' ? 'active' : '' }}">
        Pending <span class="pp-tab-count">{{ $pending }}</span>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'approved']) }}" class="pp-tab {{ $status === 'approved' ? 'active' : '' }}">
        Approved <span class="pp-tab-count">{{ $approved }}</span>
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'rejected']) }}" class="pp-tab {{ $status === 'rejected' ? 'active' : '' }}">
        Rejected <span class="pp-tab-count">{{ $rejected }}</span>
    </a>
</div>

{{-- ORDERS LIST --}}
@if($orders->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
        </div>
        <div class="pp-empty-title">No orders yet</div>
        <div class="pp-empty-text">
            @if($status !== 'all')
                Walay {{ $status }} orders. Sulayi laing filter.
            @else
                Start by creating your first stock request.
            @endif
        </div>
        <a href="{{ route('portal.orders.create') }}" class="pp-action">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Create Order
        </a>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($orders as $order)
            <a href="{{ route('portal.orders.show', $order->id) }}" class="pp-card">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $order->request_number }}</div>
                        <div class="pp-card-sub">{{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }} · {{ $order->created_at->format('M d, Y') }}</div>
                    </div>
                    <span class="pp-badge {{ $order->status }}">{{ $order->status }}</span>
                </div>

                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount">&#8369;{{ number_format($order->total_amount, 2) }}</div>
                        <div class="pp-card-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            {{ $order->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color: var(--text-muted);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($orders, 'links') && $orders->hasPages())
        <div style="margin-top: 20px;">{{ $orders->withQueryString()->links() }}</div>
    @endif
@endif

@endsection