@extends('portal.layouts.app')

@section('title', 'My Deliveries')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $baseQuery = \App\Models\DeliveryReceipt::where('store_id', $store->id);

    $total = (clone $baseQuery)->count();
    $pending = (clone $baseQuery)->where('status', 'pending')->count();
    $partial = (clone $baseQuery)->where('status', 'partial')->count();
    $paid = (clone $baseQuery)->where('status', 'paid')->count();
@endphp

{{-- HEADER --}}
<div class="pp-head">
    <div class="pp-head-left">
        <div class="pp-title">Deliveries</div>
        <div class="pp-sub">All deliveries to your store</div>
    </div>
</div>

{{-- STATS --}}
<div class="pp-stats">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
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
        <div class="pp-stat-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $partial }}</div>
        <div class="pp-stat-label">Partial</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $paid }}</div>
        <div class="pp-stat-label">Paid</div>
    </div>
</div>

{{-- DELIVERIES LIST --}}
@if($deliveries->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="pp-empty-title">No deliveries yet</div>
        <div class="pp-empty-text">Wala pa'y deliveries sa imong store.</div>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($deliveries as $dr)
            @php
                $isConfirmed = $dr->customer_confirmed ?? false;
                $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                $badgeClass = $isConfirmed ? 'paid' : ($isOut ? 'partial' : $dr->status);
                $badgeText = $isConfirmed ? 'Confirmed' : ($isOut ? 'Out for Delivery' : ucfirst($dr->status));
            @endphp

            <a href="{{ route('portal.deliveries.show', $dr->id) }}" class="pp-card {{ $isOut && !$isConfirmed ? 'has-action' : '' }}">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $dr->dr_number }}</div>
                        <div class="pp-card-sub">{{ $dr->items->count() }} item{{ $dr->items->count() != 1 ? 's' : '' }} · {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                    </div>
                    <span class="pp-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                </div>

                @if($isOut && !$isConfirmed)
                    <div class="pp-card-alert">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <rect x="1" y="3" width="15" height="13"/>
                            <path d="M16 8h4l3 3v5h-7V8z"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                        Out for Delivery — Tap to confirm
                    </div>
                @endif

                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount">&#8369;{{ number_format($dr->total_amount, 2) }}</div>
                        <div class="pp-card-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($dr->delivery_date)->diffForHumans() }}
                        </div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color: var(--text-muted);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($deliveries, 'links') && $deliveries->hasPages())
        <div style="margin-top: 20px;">{{ $deliveries->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* Highlight card kung out for delivery */
    .pp-card.has-action {
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.1);
    }

    .pp-card-alert {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        margin: 0 -16px 10px;
        padding-left: 16px;
        padding-right: 16px;
        background: rgba(59, 130, 246, 0.1);
        border-top: 1px solid rgba(59, 130, 246, 0.15);
        border-bottom: 1px solid rgba(59, 130, 246, 0.15);
        color: #3b82f6;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .pp-card-alert svg {
        flex-shrink: 0;
        animation: truckMove 1.5s ease-in-out infinite;
    }

    @keyframes truckMove {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(3px); }
    }
</style>
@endpush