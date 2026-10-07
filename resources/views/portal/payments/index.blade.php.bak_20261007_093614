@extends('portal.layouts.app')

@section('title', 'My Payments')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $total = \App\Models\ConsignmentPayment::where('store_id', $store->id)->count();
    $totalAmount = (float) \App\Models\ConsignmentPayment::where('store_id', $store->id)->sum('amount');
    $thisMonth = (float) \App\Models\ConsignmentPayment::where('store_id', $store->id)
        ->whereYear('payment_date', now()->year)
        ->whereMonth('payment_date', now()->month)
        ->sum('amount');
@endphp

{{-- HEADER --}}
<div class="pp-head">
    <div class="pp-head-left">
        <div class="pp-title">Payments</div>
        <div class="pp-sub">Your payment history</div>
    </div>
</div>

{{-- STATS --}}
<div class="pp-stats">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $total }}</div>
        <div class="pp-stat-label">Payments</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($totalAmount/1000, 1) }}k</div>
        <div class="pp-stat-label">Total Paid</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($thisMonth/1000, 1) }}k</div>
        <div class="pp-stat-label">This Month</div>
    </div>
</div>

{{-- PAYMENTS LIST --}}
@if($payments->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
        </div>
        <div class="pp-empty-title">No payments yet</div>
        <div class="pp-empty-text">Wala pa'y payments nga na-record.</div>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($payments as $p)
            <div class="pp-card">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $p->payment_number }}</div>
                        <div class="pp-card-sub">
                            {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }} · {{ ucfirst(str_replace('_', ' ', $p->method)) }}
                        </div>
                    </div>
                    <span class="pp-badge paid">Paid</span>
                </div>

                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount" style="color: #22c55e;">+ &#8369;{{ number_format($p->amount, 2) }}</div>
                        <div class="pp-card-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($p->payment_date)->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(method_exists($payments, 'links') && $payments->hasPages())
        <div style="margin-top: 20px;">{{ $payments->withQueryString()->links() }}</div>
    @endif
@endif

@endsection