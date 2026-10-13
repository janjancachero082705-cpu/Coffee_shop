@extends('portal.layouts.app')

@section('title', 'Payments')

@php
    $storeId = Auth::guard('store')->id();
    $pendingDeliveries = \App\Models\DeliveryReceipt::where('store_id', $storeId)
        ->whereRaw('total_amount > amount_paid')
        ->orderByDesc('created_at')
        ->get();
    $pendingCount = $pendingDeliveries->count();
    $pendingTotal = $pendingDeliveries->sum(fn($d) => max(0, (float)$d->total_amount - (float)$d->amount_paid));
    $activeTab = request('tab', $pendingCount > 0 ? 'pending' : 'history');
    if (!in_array($activeTab, ['pending', 'history', 'reports'])) $activeTab = 'pending';

    // First pending delivery URL for Pay Now button
    $firstPending = $pendingDeliveries->first();
    $payNowUrl = $firstPending
        ? route('portal.payments.create', ['dr' => $firstPending->id])
        : route('portal.payments.create');

    // Compute sales summary for wallet display
    $salesWalletTotal = 0;
    $salesWalletPaid  = 0;
    $salesWalletCount = 0;
    if ($activeTab === 'reports' && isset($salesReports) && $salesReports->count() > 0) {
        $salesWalletTotal = $salesReports->sum(fn($r) => (float)($r->total_sales ?? 0));
        $salesWalletPaid  = $salesReports->sum(fn($r) => (float)($r->amount_paid ?? 0));
        $salesWalletCount = $salesReports->count();
    }
@endphp

@section('content')

{{-- WALLET CARD (Dynamic per tab) --}}
@if($activeTab === 'reports')
    {{-- SALES WALLET --}}
    <div class="sw-wallet sw-wallet-sales">
        <div class="sw-wallet-bg"></div>
        <div class="sw-wallet-content">
            <div class="sw-wallet-label">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
                Total Sales
            </div>
            <div class="sw-wallet-amount"><span class="sw-wallet-cur">₱</span>{{ number_format($salesWalletTotal, 2) }}</div>
            <div class="sw-wallet-meta">
                <span>{{ $salesWalletCount }} report{{ $salesWalletCount != 1 ? 's' : '' }}</span>
                <span class="sw-wallet-pill">₱{{ number_format($salesWalletPaid, 0) }} paid</span>
            </div>
        </div>
    </div>
@elseif($activeTab === 'history')
    {{-- HISTORY WALLET --}}
    <div class="sw-wallet sw-wallet-history">
        <div class="sw-wallet-bg"></div>
        <div class="sw-wallet-content">
            <div class="sw-wallet-label">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8v8m-4-4h8M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Total Payments
            </div>
            <div class="sw-wallet-amount"><span class="sw-wallet-cur">₱</span>{{ number_format($stats['all_amount'], 2) }}</div>
            <div class="sw-wallet-meta">
                <span>{{ $stats['total'] }} payment{{ $stats['total'] != 1 ? 's' : '' }}</span>
                <span class="sw-wallet-pill">₱{{ number_format($stats['this_month'], 0) }} this month</span>
            </div>
        </div>
    </div>
@else
    {{-- DEFAULT: TO PAY WALLET --}}
    <div class="sw-wallet">
        <div class="sw-wallet-bg"></div>
        <div class="sw-wallet-content">
            <div class="sw-wallet-label">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Outstanding Balance
            </div>
            <div class="sw-wallet-amount"><span class="sw-wallet-cur">₱</span>{{ number_format($pendingTotal, 2) }}</div>
            <div class="sw-wallet-meta">
                <span>{{ $pendingCount }} pending deliver{{ $pendingCount != 1 ? 'ies' : 'y' }}</span>
                @if($pendingCount > 0)
                    <a href="{{ $payNowUrl }}" class="sw-wallet-cta">Pay Now <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></a>
                @else
                    <span class="sw-wallet-clear"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>All clear</span>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- STATS --}}
<div class="sw-stats">
    <div class="sw-stat">
        <div class="sw-stat-icon accent"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
        <div class="sw-stat-value">{{ $stats['total'] }}</div>
        <div class="sw-stat-label">Payments</div>
    </div>
    <div class="sw-stat">
        <div class="sw-stat-icon green"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        <div class="sw-stat-value">₱{{ number_format($stats['all_amount'], 0) }}</div>
        <div class="sw-stat-label">Total Paid</div>
    </div>
    <div class="sw-stat">
        <div class="sw-stat-icon amber"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="sw-stat-value">₱{{ number_format($stats['this_month'], 0) }}</div>
        <div class="sw-stat-label">This Month</div>
    </div>
</div>

{{-- TABS --}}
<div class="sw-tabs">
    <a href="{{ route('portal.payments.index', ['tab' => 'pending']) }}" class="sw-tab {{ $activeTab === 'pending' ? 'active' : '' }}"><span>To Pay</span>@if($pendingCount > 0)<span class="sw-tab-badge">{{ $pendingCount }}</span>@endif</a>
    <a href="{{ route('portal.payments.index', ['tab' => 'history']) }}" class="sw-tab {{ $activeTab === 'history' ? 'active' : '' }}"><span>History</span>@if($stats['total'] > 0)<span class="sw-tab-badge muted">{{ $stats['total'] }}</span>@endif</a>
    <a href="{{ route('portal.payments.index', ['tab' => 'reports']) }}" class="sw-tab {{ $activeTab === 'reports' ? 'active' : '' }}"><span>Sales</span>@if(isset($reportCount) && $reportCount > 0)<span class="sw-tab-badge muted">{{ $reportCount }}</span>@endif</a>
</div>

{{-- TO PAY --}}
@if($activeTab === 'pending')
    @if($pendingDeliveries->isEmpty())
        <div class="sw-empty">
            <div class="sw-empty-illustration"><svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></div>
            <div class="sw-empty-title">Nothing to pay</div>
            <div class="sw-empty-text">Wala kay pending deliveries. Good job! 🎉</div>
        </div>
    @else
        <div class="sw-section-head">
            <div class="sw-section-title"><span class="sw-section-dot"></span>Pending Payments</div>
            <div class="sw-section-count">{{ $pendingCount }}</div>
        </div>
        <div class="sw-list" id="pending">
            @foreach($pendingDeliveries as $dr)
                @php
                    $remaining = max(0, (float)$dr->total_amount - (float)$dr->amount_paid);
                    $paidPct = $dr->total_amount > 0 ? ((float)$dr->amount_paid / (float)$dr->total_amount) * 100 : 0;
                    $isReceived = !empty($dr->customer_confirmed);
                    $payUrl = route('portal.payments.create', ['dr' => $dr->id]);
                @endphp
                <div class="sw-card sw-card-clickable"
                     onclick="window.location='{{ $payUrl }}'"
                     role="link"
                     tabindex="0"
                     onkeydown="if(event.key==='Enter'){window.location='{{ $payUrl }}'}">
                    <div class="sw-card-top">
                        <div class="sw-card-store">
                            <div class="sw-card-store-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/></svg></div>
                            <div>
                                <div class="sw-card-store-name">{{ $dr->dr_number }}</div>
                                <div class="sw-card-store-meta">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <span class="sw-status-tag {{ $isReceived ? 'delivered' : 'transit' }}"><span class="sw-tag-dot"></span>{{ $isReceived ? 'Confirmed' : 'In Transit' }}</span>
                    </div>
                    <div class="sw-card-items">
                        <div class="sw-card-item-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                        <div class="sw-card-item-info">
                            <div class="sw-card-item-name">{{ $dr->items->count() ?? 0 }} item{{ ($dr->items->count() ?? 0) != 1 ? 's' : '' }}</div>
                            <div class="sw-card-item-progress">
                                <div class="sw-progress-track"><div class="sw-progress-fill" style="width: {{ $paidPct }}%"></div></div>
                                <div class="sw-progress-labels"><span>₱{{ number_format($dr->amount_paid, 0) }} paid</span><span>{{ number_format($paidPct, 0) }}%</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="sw-card-total">
                        <div class="sw-card-total-label">Amount to Pay</div>
                        <div class="sw-card-total-value">₱{{ number_format($remaining, 2) }}</div>
                    </div>
                    @if(!$isReceived && Route::has('portal.deliveries.confirm'))
                        <div class="sw-card-actions single">
                            <form method="POST" action="{{ route('portal.deliveries.confirm', $dr->id) }}" style="display:contents;" onclick="event.stopPropagation();">
                                @csrf
                                <button type="submit" class="sw-btn sw-btn-outline" onclick="event.stopPropagation(); return confirm('Confirm na nadawat nimo ni?')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>Confirm</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endif

{{-- HISTORY --}}
@if($activeTab === 'history')
    @if($payments->isEmpty())
        <div class="sw-empty">
            <div class="sw-empty-illustration"><svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg></div>
            <div class="sw-empty-title">No payments yet</div>
            <div class="sw-empty-text">Wala pa kay na-record nga payment</div>
            <a href="{{ route('portal.payments.create') }}" class="sw-empty-btn">Record first payment</a>
        </div>
    @else
        <div class="sw-section-head">
            <div class="sw-section-title"><span class="sw-section-dot green"></span>Recent Payments</div>
        </div>
        <div class="sw-list">
            @foreach($payments as $p)
                <a href="{{ route('portal.payments.show', $p->id) }}" class="sw-card sw-card-link">
                    <div class="sw-card-top">
                        <div class="sw-card-store">
                            <div class="sw-card-store-icon" style="background: {{ $p->method_color }}22; color: {{ $p->method_color }};"><span style="font-size: 16px; line-height: 1;">{{ $p->method_icon }}</span></div>
                            <div>
                                <div class="sw-card-store-name">{{ $p->payment_number }}</div>
                                <div class="sw-card-store-meta">{{ $p->method_label }} · {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        @php $vStatus = $p->verification_status ?? 'pending'; @endphp
                        @if($vStatus === 'verified')<span class="sw-status-tag paid"><span class="sw-tag-dot"></span>Paid</span>
                        @elseif($vStatus === 'rejected')<span class="sw-status-tag rejected"><span class="sw-tag-dot"></span>Rejected</span>
                        @else<span class="sw-status-tag pending"><span class="sw-tag-dot"></span>Pending</span>@endif
                    </div>
                    @if($p->reference_number)
                        <div class="sw-card-reference"><svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h16"/></svg>Ref: <span>{{ $p->reference_number }}</span></div>
                    @endif
                    <div class="sw-card-bottom">
                        <div class="sw-card-amount-row"><span class="sw-card-amount-lbl">Amount</span><span class="sw-card-amount-val">₱{{ number_format($p->amount, 2) }}</span></div>
                        <div class="sw-card-arrow"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></div>
                    </div>
                </a>
            @endforeach
        </div>
        @if(method_exists($payments, 'links') && $payments->hasPages())<div style="margin-top: 20px;">{{ $payments->withQueryString()->links() }}</div>@endif
    @endif
@endif

{{-- SALES --}}
@if($activeTab === 'reports')
    @if(!isset($salesReports) || $salesReports->isEmpty())
        <div class="sw-empty">
            <div class="sw-empty-illustration"><svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg></div>
            <div class="sw-empty-title">No sales reports</div>
            <div class="sw-empty-text">Ma-generate ra ni pagka-confirm sa delivery</div>
        </div>
    @else
        @php
            $salesTotal     = $salesReports->sum(fn($r) => (float)($r->total_sales ?? 0));
            $salesPaid      = $salesReports->sum(fn($r) => (float)($r->amount_paid ?? 0));
            $salesBalance   = max(0, $salesTotal - $salesPaid);
            $salesCount     = $salesReports->count();
            $salesPaidCount = $salesReports->filter(fn($r) => ($r->status ?? '') === 'paid' || (float)($r->balance ?? 0) <= 0.01)->count();
        @endphp

        {{-- SALES STATS --}}
        <div class="sw-sales-stats">
            <div class="sw-sales-stat">
                <div class="sw-sales-stat-icon green"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
                <div>
                    <div class="sw-sales-stat-value">₱{{ number_format($salesPaid, 0) }}</div>
                    <div class="sw-sales-stat-label">Paid</div>
                </div>
            </div>
            <div class="sw-sales-stat">
                <div class="sw-sales-stat-icon amber"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
                <div>
                    <div class="sw-sales-stat-value">₱{{ number_format($salesBalance, 0) }}</div>
                    <div class="sw-sales-stat-label">Balance</div>
                </div>
            </div>
        </div>

        <div class="sw-section-head">
            <div class="sw-section-title"><span class="sw-section-dot green"></span>Sales Reports</div>
            <div class="sw-section-count green">₱{{ number_format($reportTotal ?? 0, 0) }}</div>
        </div>
        <div class="sw-list">
            @foreach($salesReports as $report)
                @php
                    $srStatus = $report->status ?? 'pending';
                    $isPaid = $srStatus === 'paid' || (float)$report->balance <= 0.01;
                    $isPartial = !$isPaid && (float)$report->amount_paid > 0;
                @endphp
                <a href="{{ route('portal.reports.show', $report->id) }}" class="sw-card sw-card-link">
                    <div class="sw-card-top">
                        <div class="sw-card-store">
                            <div class="sw-card-store-icon green"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg></div>
                            <div>
                                <div class="sw-card-store-name">{{ $report->report_number }}</div>
                                <div class="sw-card-store-meta">{{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} – {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        @if($isPaid)<span class="sw-status-tag paid"><span class="sw-tag-dot"></span>Paid</span>
                        @elseif($isPartial)<span class="sw-status-tag transit"><span class="sw-tag-dot"></span>Partial</span>
                        @else<span class="sw-status-tag pending"><span class="sw-tag-dot"></span>Pending</span>@endif
                    </div>
                    <div class="sw-card-items">
                        <div class="sw-card-item-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                        <div class="sw-card-item-info">
                            <div class="sw-card-item-name">{{ $report->total_quantity ?? 0 }} item{{ ($report->total_quantity ?? 0) != 1 ? 's' : '' }}</div>
                            <div class="sw-card-item-progress">
                                @php $paidPct = ($report->total_sales ?? 0) > 0 ? (($report->amount_paid ?? 0) / $report->total_sales) * 100 : 0; @endphp
                                <div class="sw-progress-track"><div class="sw-progress-fill" style="width: {{ min(100, $paidPct) }}%"></div></div>
                                <div class="sw-progress-labels"><span>₱{{ number_format($report->amount_paid ?? 0, 0) }} paid</span><span>{{ number_format($paidPct, 0) }}%</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="sw-card-bottom">
                        <div class="sw-card-amount-row"><span class="sw-card-amount-lbl">Total Sales</span><span class="sw-card-amount-val">₱{{ number_format($report->total_sales ?? 0, 2) }}</span></div>
                        <div class="sw-card-arrow"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></div>
                    </div>
                </a>
            @endforeach
        </div>
        @if(method_exists($salesReports, 'links') && $salesReports->hasPages())<div style="margin-top: 20px;">{{ $salesReports->appends(['tab' => 'reports'])->links() }}</div>@endif
    @endif
@endif

@endsection

@push('styles')
<style>
/* ═══ PAYMENTS INDEX — PREMIUM COFFEE ═══ */
.sw-wallet { position: relative; padding: 22px 20px; background: linear-gradient(135deg, #2D1A11, #4E342E); border-radius: 20px; margin-bottom: 16px; overflow: hidden; box-shadow: 0 16px 40px -12px rgba(45, 26, 17, 0.4); transition: all 0.3s; }
.sw-wallet-bg { position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(216, 67, 21, 0.35), transparent 65%); pointer-events: none; }
.sw-wallet::before { content: ''; position: absolute; bottom: -60px; left: -60px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(255, 255, 255, 0.06), transparent 65%); pointer-events: none; }
.sw-wallet-content { position: relative; z-index: 1; }
.sw-wallet-label { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: rgba(255, 255, 255, 0.75); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.08em; }
.sw-wallet-amount { font-size: 38px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.03em; line-height: 1; font-variant-numeric: tabular-nums; margin-bottom: 14px; }
.sw-wallet-cur { font-size: 22px; font-weight: 800; margin-right: 4px; opacity: 0.85; }
.sw-wallet-meta { display: flex; justify-content: space-between; align-items: center; gap: 12px; font-size: 12.5px; color: rgba(255, 255, 255, 0.85); font-weight: 600; }
.sw-wallet-cta { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; background: #D84315; color: #FFFFFF; border-radius: 100px; font-size: 12px; font-weight: 800; text-decoration: none; box-shadow: 0 6px 16px rgba(216, 67, 21, 0.4); transition: all 0.15s; cursor: pointer; position: relative; z-index: 2; }
.sw-wallet-cta:hover { transform: translateX(3px); background: #BF360C; }
.sw-wallet-cta:active { transform: translateX(3px) scale(0.97); }
.sw-wallet-clear { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(34, 197, 94, 0.25); border-radius: 100px; font-size: 11.5px; font-weight: 800; color: #86efac; }

/* ═══ WALLET VARIANTS ═══ */
.sw-wallet-sales { background: linear-gradient(135deg, #14532D, #166534) !important; box-shadow: 0 16px 40px -12px rgba(20, 83, 45, 0.4) !important; }
.sw-wallet-sales .sw-wallet-bg { background: radial-gradient(circle, rgba(34, 197, 94, 0.4), transparent 65%) !important; }
.sw-wallet-history { background: linear-gradient(135deg, #1E3A8A, #1D4ED8) !important; box-shadow: 0 16px 40px -12px rgba(30, 58, 138, 0.4) !important; }
.sw-wallet-history .sw-wallet-bg { background: radial-gradient(circle, rgba(96, 165, 250, 0.4), transparent 65%) !important; }
.sw-wallet-pill { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(255, 255, 255, 0.18); border-radius: 100px; font-size: 11.5px; font-weight: 800; color: #FFFFFF; }

.sw-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 16px 12px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.sw-stat { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 8px 4px; position: relative; }
.sw-stat:not(:last-child)::after { content: ''; position: absolute; right: -5px; top: 20%; bottom: 20%; width: 1px; background: #EFEBE9; }
.sw-stat-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; border: 1px solid; }
.sw-stat-icon.accent { background: #EFEBE9; border-color: #D7CCC8; color: #2D1A11; }
.sw-stat-icon.green  { background: #DCFCE7; border-color: #BBF7D0; color: #15803D; }
.sw-stat-icon.amber  { background: #FEF3C7; border-color: #FDE68A; color: #B45309; }
.sw-stat-value { font-size: 15px; font-weight: 800; color: #2D1A11; letter-spacing: -0.02em; line-height: 1; font-variant-numeric: tabular-nums; }
.sw-stat-label { font-size: 10px; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; text-align: center; }

.sw-tabs { display: flex; gap: 4px; padding: 4px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 14px; margin-bottom: 16px; }
.sw-tab { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 42px; padding: 0 16px; border-radius: 10px; color: #8D6E63; font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.2s; }
.sw-tab.active { background: linear-gradient(135deg, #2D1A11, #4E342E); color: #FFFFFF; box-shadow: 0 6px 16px -6px rgba(45, 26, 17, 0.5); }
.sw-tab-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 20px; padding: 0 7px; background: rgba(216, 67, 21, 0.15); color: #D84315; border-radius: 10px; font-size: 11px; font-weight: 800; }
.sw-tab.active .sw-tab-badge { background: rgba(255, 255, 255, 0.22); color: #FFFFFF; }
.sw-tab-badge.muted { background: rgba(141, 110, 99, 0.15) !important; color: #8D6E63 !important; }

.sw-section-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 0 4px 12px; margin-bottom: 4px; }
.sw-section-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; color: #2D1A11; text-transform: uppercase; letter-spacing: 0.06em; }
.sw-section-dot { width: 8px; height: 8px; border-radius: 50%; background: #D97706; box-shadow: 0 0 10px rgba(217, 119, 6, 0.8); animation: swPulse 1.6s ease-in-out infinite; }
.sw-section-dot.green { background: #16A34A; box-shadow: 0 0 10px rgba(22, 163, 74, 0.8); }
@keyframes swPulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.2); opacity: 0.7; } }
.sw-section-count { display: inline-flex; align-items: center; justify-content: center; min-width: 24px; height: 22px; padding: 0 8px; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 11px; color: #B45309; font-size: 11px; font-weight: 800; }
.sw-section-count.green { background: #DCFCE7; border-color: #BBF7D0; color: #15803D; }

.sw-list { display: flex; flex-direction: column; gap: 12px; }
.sw-card { padding: 16px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); display: block; text-decoration: none; color: inherit; transition: all 0.25s; }
.sw-card:hover { border-color: #D7CCC8; transform: translateY(-2px); box-shadow: 0 12px 28px -12px rgba(45, 26, 17, 0.15); }
.sw-card-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; padding-bottom: 12px; border-bottom: 1.5px dashed #EFEBE9; margin-bottom: 12px; }
.sw-card-store { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
.sw-card-store-icon { width: 36px; height: 36px; border-radius: 10px; background: #EFEBE9; color: #2D1A11; display: grid; place-items: center; flex-shrink: 0; }
.sw-card-store-icon.green { background: #DCFCE7; color: #15803D; }
.sw-card-store-name { font-size: 13px; font-weight: 800; color: #2D1A11; font-family: ui-monospace, monospace; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sw-card-store-meta { font-size: 11px; color: #8D6E63; font-weight: 600; }

.sw-status-tag { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 8px; font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; border: 1px solid; white-space: nowrap; flex-shrink: 0; }
.sw-tag-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; box-shadow: 0 0 6px currentColor; }
.sw-status-tag.delivered, .sw-status-tag.paid { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
.sw-status-tag.transit { background: #DBEAFE; color: #1D4ED8; border-color: #BFDBFE; }
.sw-status-tag.pending { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
.sw-status-tag.rejected { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }

.sw-card-items { display: flex; align-items: center; gap: 12px; padding: 12px; background: #FDFBF7; border-radius: 12px; margin-bottom: 14px; }
.sw-card-item-icon { width: 40px; height: 40px; border-radius: 10px; background: #EFEBE9; color: #2D1A11; display: grid; place-items: center; flex-shrink: 0; }
.sw-card-item-info { flex: 1; min-width: 0; }
.sw-card-item-name { font-size: 13px; font-weight: 700; color: #2D1A11; margin-bottom: 6px; }
.sw-card-item-progress { display: flex; flex-direction: column; gap: 4px; }
.sw-progress-track { width: 100%; height: 6px; background: #EFEBE9; border-radius: 100px; overflow: hidden; }
.sw-progress-fill { height: 100%; background: linear-gradient(90deg, #D84315, #BF360C); border-radius: 100px; transition: width 0.5s; min-width: 4px; }
.sw-progress-labels { display: flex; justify-content: space-between; font-size: 10.5px; color: #8D6E63; font-weight: 700; font-variant-numeric: tabular-nums; }

.sw-card-total { display: flex; justify-content: space-between; align-items: baseline; padding: 10px 0; margin-bottom: 12px; border-top: 1.5px dashed #EFEBE9; }
.sw-card-total-label { font-size: 12px; color: #8D6E63; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
.sw-card-total-value { font-size: 24px; font-weight: 800; color: #D84315; letter-spacing: -0.03em; font-variant-numeric: tabular-nums; line-height: 1; }

.sw-card-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.sw-card-actions.single { grid-template-columns: 1fr; }
.sw-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 42px; padding: 0 14px; border-radius: 11px; font-family: inherit; font-size: 12.5px; font-weight: 800; text-decoration: none; cursor: pointer; border: 1.5px solid transparent; transition: all 0.15s; white-space: nowrap; }
.sw-btn:active { transform: scale(0.97); }
.sw-btn-primary { background: linear-gradient(135deg, #D84315, #BF360C); color: #FFFFFF; box-shadow: 0 6px 16px -6px rgba(216, 67, 21, 0.6); }
.sw-btn-outline { background: transparent; border-color: #BBF7D0; color: #15803D; }
.sw-btn-outline:hover { background: #F0FDF4; border-color: #86efac; }

.sw-card-reference { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 8px; font-size: 11px; color: #8D6E63; font-weight: 600; margin-bottom: 12px; }
.sw-card-reference span { color: #2D1A11; font-weight: 700; font-family: ui-monospace, monospace; }

.sw-card-bottom { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding-top: 12px; border-top: 1.5px dashed #EFEBE9; }
.sw-card-amount-row { display: flex; align-items: baseline; gap: 10px; }
.sw-card-amount-lbl { font-size: 11.5px; color: #8D6E63; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
.sw-card-amount-val { font-size: 18px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }
.sw-card-arrow { color: #D7CCC8; transition: all 0.2s; }
.sw-card:hover .sw-card-arrow { color: #D84315; transform: translateX(3px); }

/* ═══ CLICKABLE CARD (To Pay) ═══ */
.sw-card-clickable { cursor: pointer; }
.sw-card-clickable:active { transform: scale(0.99); }
.sw-card-clickable:focus-visible { outline: 3px solid #D84315; outline-offset: 2px; }

/* ═══ SALES STATS (small cards) ═══ */
.sw-sales-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
.sw-sales-stat { display: flex; align-items: center; gap: 10px; padding: 12px 14px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 14px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.sw-sales-stat-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; flex-shrink: 0; }
.sw-sales-stat-icon.green { background: #DCFCE7; color: #15803D; }
.sw-sales-stat-icon.amber { background: #FEF3C7; color: #B45309; }
.sw-sales-stat-value { font-size: 15px; font-weight: 800; color: #2D1A11; letter-spacing: -0.02em; line-height: 1; font-variant-numeric: tabular-nums; }
.sw-sales-stat-label { font-size: 10px; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; margin-top: 3px; }

.sw-empty { padding: 60px 20px; text-align: center; background: #FFFFFF; border: 1.5px dashed #D7CCC8; border-radius: 18px; }
.sw-empty-illustration { width: 100px; height: 100px; margin: 0 auto 18px; border-radius: 24px; background: #FDFBF7; border: 1.5px solid #EFEBE9; color: #D7CCC8; display: grid; place-items: center; }
.sw-empty-title { font-size: 16px; font-weight: 800; color: #2D1A11; margin-bottom: 6px; }
.sw-empty-text { font-size: 13px; color: #8D6E63; margin-bottom: 20px; }
.sw-empty-btn { display: inline-flex; align-items: center; gap: 6px; padding: 12px 22px; background: linear-gradient(135deg, #D84315, #BF360C); color: #FFFFFF; border-radius: 12px; font-size: 13px; font-weight: 800; text-decoration: none; box-shadow: 0 8px 20px -8px rgba(216, 67, 21, 0.6); }

@media (max-width: 480px) {
    .sw-wallet { padding: 20px 18px; border-radius: 18px; }
    .sw-wallet-amount { font-size: 32px; }
    .sw-stats { padding: 14px 8px; gap: 8px; }
    .sw-stat-icon { width: 36px; height: 36px; }
    .sw-card { padding: 14px; }
    .sw-card-total-value { font-size: 22px; }
    .sw-btn { min-height: 40px; font-size: 12px; }
}
</style>
@endpush