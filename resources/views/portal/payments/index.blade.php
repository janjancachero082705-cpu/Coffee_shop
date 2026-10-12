@extends('portal.layouts.app')

@section('title', 'Payments')

@php
    $storeId = Auth::guard('store')->id();

    // Pending deliveries — naay balance
    $pendingDeliveries = \App\Models\DeliveryReceipt::where('store_id', $storeId)
        ->whereRaw('total_amount > amount_paid')
        ->orderByDesc('created_at')
        ->get();

    $pendingCount = $pendingDeliveries->count();
    $pendingTotal = $pendingDeliveries->sum(fn($d) => max(0, (float)$d->total_amount - (float)$d->amount_paid));

    $activeTab = request('tab', $pendingCount > 0 ? 'pending' : 'history');
    if (!in_array($activeTab, ['pending', 'history', 'reports'])) $activeTab = 'pending';
@endphp

@section('content')

{{-- ═══ SHOPEE-STYLE WALLET CARD ═══ --}}
<div class="shopee-wallet">
    <div class="shopee-wallet-bg"></div>
    <div class="shopee-wallet-content">
        <div class="shopee-wallet-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
            Outstanding Balance
        </div>
        <div class="shopee-wallet-amount">
            <span class="shopee-wallet-cur">₱</span>{{ number_format($pendingTotal, 2) }}
        </div>
        <div class="shopee-wallet-meta">
            <span>{{ $pendingCount }} pending deliver{{ $pendingCount != 1 ? 'ies' : 'y' }}</span>
            @if($pendingCount > 0)
                <a href="#pending" class="shopee-wallet-cta">
                    Pay Now
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>
            @else
                <span class="shopee-wallet-clear">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M5 12l5 5L20 7"/>
                    </svg>
                    All clear
                </span>
            @endif
        </div>
    </div>
</div>

{{-- ═══ SHOPEE-STYLE STATS ROW ═══ --}}
<div class="shopee-stats">
    <div class="shopee-stat">
        <div class="shopee-stat-icon accent">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="shopee-stat-value">{{ $stats['total'] }}</div>
        <div class="shopee-stat-label">Payments</div>
    </div>
    <div class="shopee-stat">
        <div class="shopee-stat-icon green">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="shopee-stat-value">₱{{ number_format($stats['all_amount'], 0) }}</div>
        <div class="shopee-stat-label">Total Paid</div>
    </div>
    <div class="shopee-stat">
        <div class="shopee-stat-icon amber">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="shopee-stat-value">₱{{ number_format($stats['this_month'], 0) }}</div>
        <div class="shopee-stat-label">This Month</div>
    </div>
</div>

{{-- ═══ SHOPEE-STYLE TABS ═══ --}}
<div class="shopee-tabs">
    <a href="{{ route('portal.payments.index', ['tab' => 'pending']) }}"
       class="shopee-tab {{ $activeTab === 'pending' ? 'active' : '' }}">
        <span>To Pay</span>
        @if($pendingCount > 0)
            <span class="shopee-tab-badge">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('portal.payments.index', ['tab' => 'history']) }}"
       class="shopee-tab {{ $activeTab === 'history' ? 'active' : '' }}">
        <span>History</span>
        @if($stats['total'] > 0)
            <span class="shopee-tab-badge muted">{{ $stats['total'] }}</span>
        @endif
    </a>
    <a href="{{ route('portal.payments.index', ['tab' => 'reports']) }}"
       class="shopee-tab {{ $activeTab === 'reports' ? 'active' : '' }}">
        <span>Sales</span>
        @if(isset($reportCount) && $reportCount > 0)
            <span class="shopee-tab-badge muted">{{ $reportCount }}</span>
        @endif
    </a>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- TAB: TO PAY (PENDING) — SHOPEE-STYLE CARDS --}}
{{-- ═══════════════════════════════════════════ --}}
@if($activeTab === 'pending')

    @if($pendingDeliveries->isEmpty())
        <div class="shopee-empty">
            <div class="shopee-empty-illustration">
                <svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <path d="M2 10h20"/>
                </svg>
            </div>
            <div class="shopee-empty-title">Nothing to pay</div>
            <div class="shopee-empty-text">Wala kay pending deliveries. Good job! 🎉</div>
        </div>
    @else
        <div class="shopee-section-head">
            <div class="shopee-section-title">
                <span class="shopee-section-dot"></span>
                Pending Payments
            </div>
            <div class="shopee-section-count">{{ $pendingCount }}</div>
        </div>

        <div class="shopee-list">
            @foreach($pendingDeliveries as $dr)
                @php
                    $remaining = max(0, (float)$dr->total_amount - (float)$dr->amount_paid);
                    $paidPct = $dr->total_amount > 0 ? ((float)$dr->amount_paid / (float)$dr->total_amount) * 100 : 0;
                    $isReceived = !empty($dr->customer_confirmed);
                @endphp
                <div class="shopee-card">
                    {{-- TOP: Store name + status --}}
                    <div class="shopee-card-top">
                        <div class="shopee-card-store">
                            <div class="shopee-card-store-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="shopee-card-store-name">{{ $dr->dr_number }}</div>
                                <div class="shopee-card-store-meta">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <span class="shopee-status-tag {{ $isReceived ? 'delivered' : 'transit' }}">
                            <span class="shopee-tag-dot"></span>
                            {{ $isReceived ? 'Confirmed' : 'In Transit' }}
                        </span>
                    </div>

                    {{-- MID: Items count + remaining --}}
                    <div class="shopee-card-items">
                        <div class="shopee-card-item-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="shopee-card-item-info">
                            <div class="shopee-card-item-name">
                                {{ $dr->items->count() ?? 0 }} item{{ ($dr->items->count() ?? 0) != 1 ? 's' : '' }}
                            </div>
                            <div class="shopee-card-item-progress">
                                <div class="shopee-progress-track">
                                    <div class="shopee-progress-fill" style="width: {{ $paidPct }}%"></div>
                                </div>
                                <div class="shopee-progress-labels">
                                    <span>₱{{ number_format($dr->amount_paid, 0) }} paid</span>
                                    <span>{{ number_format($paidPct, 0) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- AMOUNT — highlighted --}}
                    <div class="shopee-card-total">
                        <div class="shopee-card-total-label">Amount to Pay</div>
                        <div class="shopee-card-total-value">₱{{ number_format($remaining, 2) }}</div>
                    </div>

                    {{-- ACTIONS — Shopee-style two-button row --}}
                    <div class="shopee-card-actions">
                        @if(!$isReceived && Route::has('portal.deliveries.confirm'))
                            <form method="POST" action="{{ route('portal.deliveries.confirm', $dr->id) }}" style="display:contents;">
                                @csrf
                                <button type="submit" class="shopee-btn shopee-btn-outline" onclick="return confirm('Confirm na nadawat nimo ni?')">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M5 12l5 5L20 7"/>
                                    </svg>
                                    Confirm
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('portal.payments.create', ['dr' => $dr->id]) }}" class="shopee-btn shopee-btn-primary">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            Pay Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endif

{{-- ═══════════════════════════════════════════ --}}
{{-- TAB: HISTORY — SHOPEE-STYLE ORDER CARDS --}}
{{-- ═══════════════════════════════════════════ --}}
@if($activeTab === 'history')

    @if($payments->isEmpty())
        <div class="shopee-empty">
            <div class="shopee-empty-illustration">
                <svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect x="1" y="4" width="22" height="16" rx="2"/>
                    <path d="M1 10h22"/>
                </svg>
            </div>
            <div class="shopee-empty-title">No payments yet</div>
            <div class="shopee-empty-text">Wala pa kay na-record nga payment</div>
            <a href="{{ route('portal.payments.create') }}" class="shopee-empty-btn">
                Record first payment
            </a>
        </div>
    @else
        <div class="shopee-section-head">
            <div class="shopee-section-title">
                <span class="shopee-section-dot green"></span>
                Recent Payments
            </div>
        </div>

        <div class="shopee-list">
            @foreach($payments as $p)
                <a href="{{ route('portal.payments.show', $p->id) }}" class="shopee-card shopee-card-link">
                    {{-- TOP: payment number + status --}}
                    <div class="shopee-card-top">
                        <div class="shopee-card-store">
                            <div class="shopee-card-store-icon" style="background: {{ $p->method_color }}22; color: {{ $p->method_color }};">
                                <span style="font-size: 16px; line-height: 1;">{{ $p->method_icon }}</span>
                            </div>
                            <div>
                                <div class="shopee-card-store-name">{{ $p->payment_number }}</div>
                                <div class="shopee-card-store-meta">
                                    {{ $p->method_label }} ·
                                    {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        @php
                            $vStatus = $p->verification_status ?? 'pending';
                        @endphp
                        @if($vStatus === 'verified')
                            <span class="shopee-status-tag paid">
                                <span class="shopee-tag-dot"></span> Paid
                            </span>
                        @elseif($vStatus === 'rejected')
                            <span class="shopee-status-tag rejected">
                                <span class="shopee-tag-dot"></span> Rejected
                            </span>
                        @else
                            <span class="shopee-status-tag pending">
                                <span class="shopee-tag-dot"></span> Pending
                            </span>
                        @endif
                    </div>

                    {{-- MID: reference --}}
                    @if($p->reference_number)
                        <div class="shopee-card-reference">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 7h16M4 12h10M4 17h16"/>
                            </svg>
                            Ref: <span>{{ $p->reference_number }}</span>
                        </div>
                    @endif

                    {{-- BOTTOM: amount + arrow --}}
                    <div class="shopee-card-bottom">
                        <div class="shopee-card-amount-row">
                            <span class="shopee-card-amount-lbl">Amount</span>
                            <span class="shopee-card-amount-val">₱{{ number_format($p->amount, 2) }}</span>
                        </div>
                        <div class="shopee-card-arrow">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if(method_exists($payments, 'links') && $payments->hasPages())
            <div style="margin-top: 20px;">{{ $payments->withQueryString()->links() }}</div>
        @endif
    @endif

@endif


{{-- ═══════════════════════════════════════════ --}}
{{-- TAB: SALES — Sales Report list (merged) --}}
{{-- ═══════════════════════════════════════════ --}}
@if($activeTab === 'reports')

    @if(!isset($salesReports) || $salesReports->isEmpty())
        <div class="shopee-empty">
            <div class="shopee-empty-illustration">
                <svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
                </svg>
            </div>
            <div class="shopee-empty-title">No sales reports</div>
            <div class="shopee-empty-text">Ma-generate ra ni pagka-confirm sa delivery</div>
        </div>
    @else
        <div class="shopee-section-head">
            <div class="shopee-section-title">
                <span class="shopee-section-dot green"></span>
                Sales Reports
            </div>
            <div class="shopee-section-count" style="background: rgba(34,197,94,0.15); border-color: rgba(34,197,94,0.35); color: #22c55e;">
                ₱{{ number_format($reportTotal ?? 0, 0) }}
            </div>
        </div>

        <div class="shopee-list">
            @foreach($salesReports as $report)
                @php
                    $srStatus = $report->status ?? 'pending';
                    $isPaid = $srStatus === 'paid' || (float)$report->balance <= 0.01;
                    $isPartial = !$isPaid && (float)$report->amount_paid > 0;
                @endphp
                <a href="{{ route('portal.reports.show', $report->id) }}" class="shopee-card shopee-card-link">
                    <div class="shopee-card-top">
                        <div class="shopee-card-store">
                            <div class="shopee-card-store-icon" style="background: rgba(34,197,94,0.14); border-color: rgba(34,197,94,0.3); color: #22c55e;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="shopee-card-store-name">{{ $report->report_number }}</div>
                                <div class="shopee-card-store-meta">
                                    {{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} –
                                    {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        @if($isPaid)
                            <span class="shopee-status-tag paid">
                                <span class="shopee-tag-dot"></span> Paid
                            </span>
                        @elseif($isPartial)
                            <span class="shopee-status-tag transit">
                                <span class="shopee-tag-dot"></span> Partial
                            </span>
                        @else
                            <span class="shopee-status-tag pending">
                                <span class="shopee-tag-dot"></span> Pending
                            </span>
                        @endif
                    </div>

                    <div class="shopee-card-items">
                        <div class="shopee-card-item-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="shopee-card-item-info">
                            <div class="shopee-card-item-name">
                                {{ $report->total_quantity ?? 0 }} item{{ ($report->total_quantity ?? 0) != 1 ? 's' : '' }}
                            </div>
                            <div class="shopee-card-item-progress">
                                @php
                                    $paidPct = ($report->total_sales ?? 0) > 0
                                        ? (($report->amount_paid ?? 0) / $report->total_sales) * 100
                                        : 0;
                                @endphp
                                <div class="shopee-progress-track">
                                    <div class="shopee-progress-fill" style="width: {{ min(100, $paidPct) }}%"></div>
                                </div>
                                <div class="shopee-progress-labels">
                                    <span>₱{{ number_format($report->amount_paid ?? 0, 0) }} paid</span>
                                    <span>{{ number_format($paidPct, 0) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="shopee-card-bottom">
                        <div class="shopee-card-amount-row">
                            <span class="shopee-card-amount-lbl">Total Sales</span>
                            <span class="shopee-card-amount-val">₱{{ number_format($report->total_sales ?? 0, 2) }}</span>
                        </div>
                        <div class="shopee-card-arrow">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if(method_exists($salesReports, 'links') && $salesReports->hasPages())
            <div style="margin-top: 20px;">{{ $salesReports->appends(['tab' => 'reports'])->links() }}</div>
        @endif
    @endif

@endif
@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   SHOPEE/TIKTOK-STYLE PAYMENTS
   ═══════════════════════════════════════════════════════════ */

/* ═══ WALLET CARD (Shopee Wallet style) ═══ */
.shopee-wallet {
    position: relative;
    padding: 22px 20px;
    background: linear-gradient(135deg, var(--t-accent) 0%, var(--t-accent-2) 100%);
    border-radius: 20px;
    margin-bottom: 16px;
    overflow: hidden;
    box-shadow: 0 16px 40px -12px rgba(var(--t-accent-rgb), 0.5);
    animation: shopeeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
}
.shopee-wallet-bg {
    position: absolute;
    top: -40px;
    right: -40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.25), transparent 65%);
    pointer-events: none;
    animation: shopeeGlow 6s ease-in-out infinite;
}
.shopee-wallet::before {
    content: '';
    position: absolute;
    bottom: -60px;
    left: -60px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15), transparent 65%);
    pointer-events: none;
}
@keyframes shopeeGlow {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.15); opacity: 1; }
}
.shopee-wallet-content {
    position: relative;
    z-index: 1;
}
.shopee-wallet-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.shopee-wallet-amount {
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.03em;
    line-height: 1;
    font-variant-numeric: tabular-nums;
    text-shadow: 0 2px 20px rgba(0, 0, 0, 0.15);
    margin-bottom: 14px;
}
.shopee-wallet-cur {
    font-size: 22px;
    font-weight: 800;
    margin-right: 4px;
    opacity: 0.9;
}
.shopee-wallet-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    font-size: 12.5px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
}
.shopee-wallet-cta {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 14px;
    background: rgba(255, 255, 255, 0.95);
    color: var(--t-accent-dark, #333);
    border-radius: 100px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.15s;
    -webkit-tap-highlight-color: transparent;
}
.shopee-wallet-cta:hover {
    transform: translateX(3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}
.shopee-wallet-clear {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 100px;
    font-size: 11.5px;
    font-weight: 800;
    color: #ffffff;
}

/* ═══ STATS ROW (Shopee-style grid) ═══ */
.shopee-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    padding: 16px 12px;
    background: var(--t-card);
    border: 1px solid var(--t-border);
    border-radius: 18px;
    margin-bottom: 16px;
    animation: shopeeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    animation-delay: 0.05s;
}
.shopee-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 8px 4px;
    position: relative;
}
.shopee-stat:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -5px;
    top: 20%;
    bottom: 20%;
    width: 1px;
    background: var(--t-border);
}
.shopee-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    border: 1px solid;
}
.shopee-stat-icon.accent { background: rgba(var(--t-accent-rgb), 0.14); border-color: rgba(var(--t-accent-rgb), 0.3); color: var(--t-accent); }
.shopee-stat-icon.green  { background: rgba(34, 197, 94, 0.14); border-color: rgba(34, 197, 94, 0.3); color: #22c55e; }
.shopee-stat-icon.amber  { background: rgba(245, 158, 11, 0.14); border-color: rgba(245, 158, 11, 0.3); color: #f59e0b; }
.shopee-stat-value {
    font-size: 15px;
    font-weight: 800;
    color: var(--t-text);
    letter-spacing: -0.02em;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}
.shopee-stat-label {
    font-size: 10px;
    color: var(--t-text-3);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
    text-align: center;
}

/* ═══ TABS (Shopee pill style) ═══ */
.shopee-tabs {
    display: flex;
    gap: 4px;
    padding: 4px;
    background: var(--t-input);
    border: 1px solid var(--t-border);
    border-radius: 14px;
    margin-bottom: 16px;
}
.shopee-tab {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 42px;
    padding: 0 16px;
    border-radius: 10px;
    color: var(--t-text-2);
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    -webkit-tap-highlight-color: transparent;
}
.shopee-tab.active {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
    color: #ffffff;
    box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.5);
}
.shopee-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 7px;
    background: rgba(255, 255, 255, 0.22);
    border-radius: 10px;
    font-size: 11px;
    font-weight: 800;
}
.shopee-tab:not(.active) .shopee-tab-badge {
    background: rgba(var(--t-accent-rgb), 0.15);
    color: var(--t-accent);
}
.shopee-tab-badge.muted {
    background: rgba(107, 114, 128, 0.15) !important;
    color: var(--t-text-3) !important;
}

/* ═══ SECTION HEAD ═══ */
.shopee-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 0 4px 12px;
    margin-bottom: 4px;
}
.shopee-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 800;
    color: var(--t-text);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.shopee-section-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #f59e0b;
    box-shadow: 0 0 10px rgba(245, 158, 11, 0.9);
    animation: shopeeDot 1.6s ease-in-out infinite;
}
.shopee-section-dot.green {
    background: #22c55e;
    box-shadow: 0 0 10px rgba(34, 197, 94, 0.9);
}
@keyframes shopeeDot {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
}
.shopee-section-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 22px;
    padding: 0 8px;
    background: rgba(245, 158, 11, 0.15);
    border: 1px solid rgba(245, 158, 11, 0.35);
    border-radius: 11px;
    color: #f59e0b;
    font-size: 11px;
    font-weight: 800;
}

/* ═══ LIST ═══ */
.shopee-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* ═══ CARD (Shopee order card style) ═══ */
.shopee-card {
    padding: 16px;
    background: var(--t-card);
    border: 1px solid var(--t-border);
    border-radius: 18px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    animation: shopeeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    display: block;
    text-decoration: none;
    color: inherit;
}
.shopee-card:hover {
    border-color: rgba(var(--t-accent-rgb), 0.35);
    transform: translateY(-2px);
    box-shadow: 0 12px 28px -12px rgba(var(--t-accent-rgb), 0.3);
}
.shopee-card:active { transform: scale(0.99); }

/* ═══ CARD TOP (store + status) ═══ */
.shopee-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px dashed var(--t-border);
    margin-bottom: 12px;
}
.shopee-card-store {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}
.shopee-card-store-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(var(--t-accent-rgb), 0.12);
    border: 1px solid rgba(var(--t-accent-rgb), 0.22);
    color: var(--t-accent);
    display: grid;
    place-items: center;
    flex-shrink: 0;
}
.shopee-card-store-name {
    font-size: 13px;
    font-weight: 800;
    color: var(--t-text);
    font-family: ui-monospace, monospace;
    letter-spacing: -0.01em;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.shopee-card-store-meta {
    font-size: 11px;
    color: var(--t-text-3);
    font-weight: 600;
}

/* ═══ STATUS TAG ═══ */
.shopee-status-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border: 1px solid;
    white-space: nowrap;
    flex-shrink: 0;
}
.shopee-tag-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    box-shadow: 0 0 6px currentColor;
}
.shopee-status-tag.delivered,
.shopee-status-tag.paid {
    background: rgba(34, 197, 94, 0.14);
    color: #22c55e;
    border-color: rgba(34, 197, 94, 0.35);
}
.shopee-status-tag.transit {
    background: rgba(59, 130, 246, 0.14);
    color: #3b82f6;
    border-color: rgba(59, 130, 246, 0.35);
}
.shopee-status-tag.pending {
    background: rgba(245, 158, 11, 0.14);
    color: #f59e0b;
    border-color: rgba(245, 158, 11, 0.35);
}
.shopee-status-tag.rejected {
    background: rgba(239, 68, 68, 0.14);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.35);
}

/* ═══ ITEM + PROGRESS ═══ */
.shopee-card-items {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(var(--t-accent-rgb), 0.04);
    border-radius: 12px;
    margin-bottom: 14px;
}
.shopee-card-item-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(var(--t-accent-rgb), 0.12);
    border: 1px solid rgba(var(--t-accent-rgb), 0.22);
    color: var(--t-accent);
    display: grid;
    place-items: center;
    flex-shrink: 0;
}
.shopee-card-item-info {
    flex: 1;
    min-width: 0;
}
.shopee-card-item-name {
    font-size: 13px;
    font-weight: 700;
    color: var(--t-text);
    margin-bottom: 6px;
}
.shopee-card-item-progress {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.shopee-progress-track {
    width: 100%;
    height: 6px;
    background: var(--t-input);
    border-radius: 100px;
    overflow: hidden;
    border: 1px solid var(--t-border);
}
.shopee-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--t-accent), var(--t-accent-2));
    border-radius: 100px;
    transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 4px;
}
.shopee-progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 10.5px;
    color: var(--t-text-3);
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

/* ═══ AMOUNT ROW ═══ */
.shopee-card-total {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 10px 0;
    margin-bottom: 12px;
    border-top: 1px dashed var(--t-border);
}
.shopee-card-total-label {
    font-size: 12px;
    color: var(--t-text-3);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.shopee-card-total-value {
    font-size: 24px;
    font-weight: 800;
    color: var(--t-accent);
    letter-spacing: -0.03em;
    font-variant-numeric: tabular-nums;
    line-height: 1;
}

/* ═══ ACTIONS (Shopee two-button) ═══ */
.shopee-card-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.shopee-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 42px;
    padding: 0 14px;
    border-radius: 11px;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    white-space: nowrap;
}
.shopee-btn:active { transform: scale(0.97); }
.shopee-btn-primary {
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
    color: #ffffff;
    box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.6);
}
.shopee-btn-primary:hover {
    box-shadow: 0 10px 24px -6px rgba(var(--t-accent-rgb), 0.8);
}
.shopee-btn-outline {
    background: transparent;
    border-color: rgba(34, 197, 94, 0.4);
    color: #22c55e;
}
.shopee-btn-outline:hover {
    background: rgba(34, 197, 94, 0.1);
    border-color: rgba(34, 197, 94, 0.65);
}

/* ═══ HISTORY — REFERENCE ═══ */
.shopee-card-reference {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    background: rgba(var(--t-accent-rgb), 0.06);
    border: 1px solid var(--t-border);
    border-radius: 8px;
    font-size: 11px;
    color: var(--t-text-3);
    font-weight: 600;
    margin-bottom: 12px;
}
.shopee-card-reference span {
    color: var(--t-text);
    font-weight: 700;
    font-family: ui-monospace, monospace;
}

/* ═══ HISTORY — BOTTOM AMOUNT ROW ═══ */
.shopee-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 12px;
    border-top: 1px dashed var(--t-border);
}
.shopee-card-amount-row {
    display: flex;
    align-items: baseline;
    gap: 10px;
}
.shopee-card-amount-lbl {
    font-size: 11.5px;
    color: var(--t-text-3);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.shopee-card-amount-val {
    font-size: 18px;
    font-weight: 800;
    color: var(--t-accent);
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.02em;
}
.shopee-card-arrow {
    color: var(--t-text-3);
    transition: all 0.2s;
}
.shopee-card:hover .shopee-card-arrow {
    color: var(--t-accent);
    transform: translateX(3px);
}

/* ═══ EMPTY ═══ */
.shopee-empty {
    padding: 60px 20px;
    text-align: center;
    background: var(--t-card);
    border: 1px dashed var(--t-border-2);
    border-radius: 18px;
}
.shopee-empty-illustration {
    width: 100px;
    height: 100px;
    margin: 0 auto 18px;
    border-radius: 24px;
    background: rgba(var(--t-accent-rgb), 0.1);
    border: 1px solid rgba(var(--t-accent-rgb), 0.2);
    color: var(--t-accent);
    display: grid;
    place-items: center;
    opacity: 0.7;
}
.shopee-empty-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--t-text);
    margin-bottom: 6px;
}
.shopee-empty-text {
    font-size: 13px;
    color: var(--t-text-3);
    margin-bottom: 20px;
}
.shopee-empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 22px;
    background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
    color: #ffffff;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.6);
}

/* ═══ ANIMATIONS ═══ */
@keyframes shopeeIn {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}
.shopee-list > .shopee-card:nth-child(1) { animation-delay: 0.02s; }
.shopee-list > .shopee-card:nth-child(2) { animation-delay: 0.06s; }
.shopee-list > .shopee-card:nth-child(3) { animation-delay: 0.10s; }
.shopee-list > .shopee-card:nth-child(4) { animation-delay: 0.14s; }
.shopee-list > .shopee-card:nth-child(5) { animation-delay: 0.18s; }

/* ═══ LIGHT THEME — FORCE DARK TEXT ═══ */
html[data-theme="light"] .shopee-card-store-name,
html[data-theme="light"] .shopee-card-item-name,
html[data-theme="light"] .shopee-stat-value,
html[data-theme="light"] .shopee-section-title,
html[data-theme="light"] .shopee-empty-title,
html[data-theme="blue"] .shopee-card-store-name,
html[data-theme="blue"] .shopee-card-item-name,
html[data-theme="blue"] .shopee-stat-value,
html[data-theme="blue"] .shopee-section-title,
html[data-theme="blue"] .shopee-empty-title {
    color: #0c1e3d !important;
}
html[data-theme="light"] .shopee-card-store-meta,
html[data-theme="light"] .shopee-card-total-label,
html[data-theme="light"] .shopee-stat-label,
html[data-theme="light"] .shopee-progress-labels,
html[data-theme="light"] .shopee-card-amount-lbl,
html[data-theme="light"] .shopee-card-reference,
html[data-theme="light"] .shopee-empty-text,
html[data-theme="blue"] .shopee-card-store-meta,
html[data-theme="blue"] .shopee-card-total-label,
html[data-theme="blue"] .shopee-stat-label,
html[data-theme="blue"] .shopee-progress-labels,
html[data-theme="blue"] .shopee-card-amount-lbl,
html[data-theme="blue"] .shopee-card-reference,
html[data-theme="blue"] .shopee-empty-text {
    color: #64748b !important;
}

/* ═══ DARK THEME ═══ */
html[data-theme="dark"] .shopee-card-store-name,
html[data-theme="dark"] .shopee-card-item-name,
html[data-theme="dark"] .shopee-stat-value,
html[data-theme="dark"] .shopee-section-title,
html[data-theme="dark"] .shopee-empty-title {
    color: #ffffff !important;
}

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .shopee-wallet { padding: 20px 18px; border-radius: 18px; }
    .shopee-wallet-amount { font-size: 32px; }
    .shopee-stats { padding: 14px 8px; gap: 8px; }
    .shopee-stat-icon { width: 36px; height: 36px; }
    .shopee-card { padding: 14px; }
    .shopee-card-total-value { font-size: 22px; }
    .shopee-btn { min-height: 40px; font-size: 12px; }
}

/* FIX WALLET AMOUNT */

/* FIX WALLET AMOUNT VISIBILITY */
.shopee-wallet-amount,
.shopee-wallet-amount *,
.shopee-wallet-cur {
    color: #ffffff !important;
    text-shadow: 0 2px 12px rgba(0, 0, 0, 0.35) !important;
}
.shopee-wallet-label,
.shopee-wallet-meta,
.shopee-wallet-meta * {
    color: rgba(255, 255, 255, 0.95) !important;
}
.shopee-wallet-cta {
    color: var(--t-accent-dark, #1a1a1f) !important;
    background: #ffffff !important;
}
</style>
@endpush