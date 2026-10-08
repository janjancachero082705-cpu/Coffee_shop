@extends('portal.layouts.app')

@section('title', 'Sales Reports')

@section('content')

<div class="pp-head" style="margin-bottom:16px;">
    <div class="pp-head-left">
        <div class="pp-title">Sales Reports</div>
        <div class="pp-sub">Imong sales ug payment status</div>
    </div>
</div>

@php
    $totalPaid = $reports->sum('amount_paid');
    $totalBalance = $reports->sum('balance');
@endphp

<div class="pp-stats stats-3col" style="margin-bottom:16px;">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </div>
        <div class="pp-stat-value">{{ $stats['total'] }}</div>
        <div class="pp-stat-label">Reports</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($totalPaid, 0) }}</div>
        <div class="pp-stat-label">Total Paid</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="pp-stat-value">&#8369;{{ number_format($totalBalance, 0) }}</div>
        <div class="pp-stat-label">Balance</div>
    </div>
</div>

@if($reports->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </div>
        <div class="pp-empty-title">Wala pay reports</div>
        <div class="pp-empty-text">Wala pa kay sales report.</div>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($reports as $r)
            @php
                $paidPercent = $r->amount_due > 0 ? min(100, ($r->amount_paid / $r->amount_due) * 100) : 0;
                $statusColor = match($r->status) {
                    'paid' => '#22c55e',
                    'partial' => '#f59e0b',
                    'verified' => '#3b82f6',
                    default => '#8a8378',
                };
            @endphp
            <a href="{{ route('portal.reports.show', $r->id) }}" class="pp-card">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $r->report_number }}</div>
                        <div class="pp-card-sub">
                            {{ \Carbon\Carbon::parse($r->period_from)->format('M d') }} -
                            {{ \Carbon\Carbon::parse($r->period_to)->format('M d, Y') }}
                            @if($r->deliveryReceipt)
                                · {{ $r->deliveryReceipt->dr_number }}
                            @endif
                        </div>
                    </div>
                    <span class="pp-badge" style="background:{{ $statusColor }}22;color:{{ $statusColor }};border:1px solid {{ $statusColor }}55;">
                        {{ ucfirst($r->status) }}
                    </span>
                </div>

                <div class="pp-card-body">
                    <div style="flex:1;">
                        <div class="pp-card-amount">&#8369;{{ number_format($r->amount_due, 2) }}</div>
                        <div class="pp-progress" style="margin-top:8px;">
                            <div class="pp-progress-bar" style="width:{{ $paidPercent }}%;background:{{ $statusColor }};"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:10.5px;color:#8a8378;margin-top:4px;">
                            <span>Paid: &#8369;{{ number_format($r->amount_paid, 2) }}</span>
                            <span>Bal: &#8369;{{ number_format($r->balance, 2) }}</span>
                        </div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:var(--text-muted);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($reports, 'links') && $reports->hasPages())
        <div style="margin-top:20px;">{{ $reports->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    .pp-head-left .pp-title { font-size:22px; font-weight:800; color:#f5f3f0; }
    .pp-head-left .pp-sub { font-size:12.5px; color:#8a8378; margin-top:2px; }

    /* .pp-stats → managed by layout */
    .pp-stat {
        background:linear-gradient(165deg, #1e1a16, #15120f);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:14px;
        padding:14px 12px;
    }
    .pp-stat-icon {
        width:34px; height:34px; border-radius:10px;
        background:rgba(201,169,97,0.15); color:#c9a961;
        display:grid; place-items:center; margin-bottom:10px;
    }
    .pp-stat-icon.green { background:rgba(34,197,94,0.15); color:#22c55e; }
    .pp-stat-icon.amber { background:rgba(245,158,11,0.15); color:#f59e0b; }
    .pp-stat-value { font-size:18px; font-weight:800; color:#f5f3f0; font-variant-numeric:tabular-nums; }
    .pp-stat-label { font-size:10.5px; color:#8a8378; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; margin-top:2px; }

    .pp-list { display:flex; flex-direction:column; gap:10px; }
    .pp-card {
        display:block; padding:16px;
        background:linear-gradient(165deg, #1e1a16, #15120f);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:16px;
        text-decoration:none; color:inherit;
        transition:border-color 0.15s;
    }
    .pp-card:hover { border-color:rgba(201,169,97,0.3); }
    .pp-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:12px; }
    .pp-card-title { font-size:13.5px; font-weight:800; color:#f5f3f0; }
    .pp-card-sub { font-size:11px; color:#8a8378; margin-top:3px; }
    .pp-badge { padding:4px 9px; border-radius:7px; font-size:10.5px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; }
    .pp-card-body { display:flex; align-items:center; gap:12px; }
    .pp-card-amount { font-size:18px; font-weight:800; color:#c9a961; font-variant-numeric:tabular-nums; }
    .pp-progress { width:100%; height:4px; background:rgba(255,255,255,0.06); border-radius:2px; overflow:hidden; }
    .pp-progress-bar { height:100%; border-radius:2px; transition:width 0.4s; }

    .pp-empty {
        text-align:center; padding:44px 20px;
        background:linear-gradient(165deg, #1e1a16, #15120f);
        border:1px dashed rgba(255,255,255,0.08);
        border-radius:18px;
    }
    .pp-empty-icon {
        width:64px; height:64px; margin:0 auto 14px;
        border-radius:20px; background:rgba(201,169,97,0.1); color:#c9a961;
        display:grid; place-items:center;
    }
    .pp-empty-title { font-size:15px; font-weight:800; color:#f5f3f0; margin-bottom:6px; }
    .pp-empty-text { font-size:12.5px; color:#8a8378; }

    @media (max-width: 480px) {
        /* .pp-stats → managed by layout */
    }
</style>
@endpush