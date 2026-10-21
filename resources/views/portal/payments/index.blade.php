@extends('portal.layouts.app')

@section('title', 'Payments')

@php
    $storeId = Auth::guard('store')->id();
    $activeTab = request('tab', $pendingCount > 0 ? 'pending' : 'history');
    if (!in_array($activeTab, ['pending', 'history', 'reports'])) $activeTab = 'pending';
@endphp

@section('content')

{{-- HERO WALLET --}}
@if($activeTab === 'reports')
    <div class="pay-hero" data-variant="sales">
        <div class="pay-hero-glow"></div>
        <div class="pay-hero-content">
            <div class="pay-hero-label">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
                Total Sales
            </div>
            <div class="pay-hero-amount"><span>₱</span>{{ number_format($reportTotal ?? 0, 2) }}</div>
            <div class="pay-hero-meta">
                <span>{{ $reportCount }} report{{ $reportCount != 1 ? 's' : '' }}</span>
            </div>
        </div>
    </div>
@elseif($activeTab === 'history')
    <div class="pay-hero" data-variant="history">
        <div class="pay-hero-glow"></div>
        <div class="pay-hero-content">
            <div class="pay-hero-label">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v8m-4-4h8M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Total Payments
            </div>
            <div class="pay-hero-amount"><span>₱</span>{{ number_format($stats['all_amount'], 2) }}</div>
            <div class="pay-hero-meta">
                <span>{{ $stats['total'] }} payment{{ $stats['total'] != 1 ? 's' : '' }}</span>
                <span class="pay-hero-pill">₱{{ number_format($stats['this_month'], 0) }} this month</span>
            </div>
        </div>
    </div>
@else
    <div class="pay-hero" data-variant="pending">
        <div class="pay-hero-glow"></div>
        <div class="pay-hero-content">
            <div class="pay-hero-label">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Outstanding Balance
            </div>
            <div class="pay-hero-amount"><span>₱</span>{{ number_format($pendingTotal, 2) }}</div>
            <div class="pay-hero-meta">
                <span>{{ $pendingCount }} pending deliver{{ $pendingCount != 1 ? 'ies' : 'y' }}</span>
                @if($pendingCount > 0)
                    <a href="{{ route('portal.payments.create') }}" class="pay-hero-cta">Pay Now <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></a>
                @else
                    <span class="pay-hero-clear"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>All clear</span>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- STATS --}}
<div class="pay-stats">
    <div class="pay-stat">
        <div class="pay-stat-icon" style="--c:#1A1A1A;--bg:#F5F5F5;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="pay-stat-value">{{ $stats['total'] }}</div>
        <div class="pay-stat-label">Payments</div>
    </div>
    <div class="pay-stat">
        <div class="pay-stat-icon" style="--c:#15803D;--bg:#E8F5E9;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="pay-stat-value">₱{{ number_format($stats['all_amount'], 0) }}</div>
        <div class="pay-stat-label">Total Paid</div>
    </div>
    <div class="pay-stat">
        <div class="pay-stat-icon" style="--c:#B45309;--bg:#FFF8E1;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="pay-stat-value">₱{{ number_format($stats['this_month'], 0) }}</div>
        <div class="pay-stat-label">This Month</div>
    </div>
</div>

{{-- TABS --}}
<div class="pay-tabs">
    <a href="{{ route('portal.payments.index', ['tab' => 'pending']) }}" class="pay-tab {{ $activeTab === 'pending' ? 'active' : '' }}">
        To Pay
        @if($pendingCount > 0)<span class="pay-tab-count">{{ $pendingCount }}</span>@endif
    </a>
    <a href="{{ route('portal.payments.index', ['tab' => 'history']) }}" class="pay-tab {{ $activeTab === 'history' ? 'active' : '' }}">
        History
        @if($stats['total'] > 0)<span class="pay-tab-count">{{ $stats['total'] }}</span>@endif
    </a>
    <a href="{{ route('portal.payments.index', ['tab' => 'reports']) }}" class="pay-tab {{ $activeTab === 'reports' ? 'active' : '' }}">
        Sales
        @if($reportCount > 0)<span class="pay-tab-count">{{ $reportCount }}</span>@endif
    </a>
</div>

{{-- ═══ TO PAY TAB ═══ --}}
@if($activeTab === 'pending')
    @if($pendingDeliveries->isEmpty())
        <div class="pay-empty">
            <div class="pay-empty-icon">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            </div>
            <div class="pay-empty-title">Nothing to pay</div>
            <div class="pay-empty-text">Wala kay pending deliveries. Good job! 🎉</div>
        </div>
    @else
        <div class="pay-section-label">
            <span>Pending Payments</span>
            <span class="pay-section-count">{{ $pendingCount }}</span>
        </div>
        <div class="pay-list">
            @foreach($pendingDeliveries as $dr)
                @php
                    $remaining = max(0, (float)$dr->total_amount - (float)$dr->amount_paid);
                    $paidPct = $dr->total_amount > 0 ? ((float)$dr->amount_paid / (float)$dr->total_amount) * 100 : 0;
                    
                    $latestPayment = \App\Models\ConsignmentPayment::where('delivery_receipt_id', $dr->id)->latest()->first();
                    $isRejected = ($latestPayment && $latestPayment->verification_status === 'rejected');
                    $hasPendingPayment = ($latestPayment && $latestPayment->verification_status === 'pending');
                    
                    $payUrl = route('portal.payments.create', ['dr' => $dr->id]);
                    $statusKey = $isRejected ? 'rejected' : ($hasPendingPayment ? 'pending' : 'transit');
                    $displayStatus = $isRejected ? 'REJECTED' : ($hasPendingPayment ? 'PENDING' : 'UNPAID');
                @endphp
                
                @if($hasPendingPayment)
                    {{-- Waiting for Approval --}}
                    <div style="position: relative; background: #FFF; border: 1.5px solid #FDE68A; border-radius: 16px; padding: 16px; opacity: 0.8; pointer-events: none; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                            <div>
                                <div style="font-size: 15px; font-weight: 800; color: #1A1A1A; display: flex; align-items: center; gap: 8px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1A1A1A" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
                                    {{ $dr->dr_number }}
                                </div>
                                <div style="font-size: 11.5px; color: #757575; margin-top: 4px;">
                                    {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }} · {{ $dr->items->count() }} item(s)
                                </div>
                            </div>
                            <div style="background: #FEF3C7; color: #D97706; font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 100px;">
                                WAITING FOR APPROVAL
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #757575; border-top: 1px dashed #EEEEEE; padding-top: 10px;">
                            <div>₱{{ number_format($dr->amount_paid, 2) }} paid</div>
                            <div>{{ $dr->total_amount > 0 ? number_format(($dr->amount_paid / $dr->total_amount) * 100, 0) : 0 }}%</div>
                        </div>
                        <div style="margin-top: 10px;">
                            <div style="font-size: 10px; font-weight: 800; color: #757575; margin-bottom: 4px; letter-spacing: 0.05em;">AMOUNT TO PAY</div>
                            <div style="font-size: 20px; font-weight: 800; color: #D97706;">₱{{ number_format($dr->balance, 2) }}</div>
                        </div>
                    </div>
                @else
                    <a href="{{ $payUrl }}" class="pay-card">
                        <div class="pay-card-stripe" data-status="{{ $statusKey }}"></div>

                        <div class="pay-card-head">
                            <div class="pay-card-dr">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
                                {{ $dr->dr_number }}
                            </div>
                            <span class="pay-badge pay-badge-{{ $statusKey }}">{{ $displayStatus }}</span>
                        </div>

                        <div class="pay-card-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}
                            <span style="opacity:.4;">·</span>
                            {{ $dr->items->count() ?? 0 }} item{{ ($dr->items->count() ?? 0) != 1 ? 's' : '' }}
                        </div>

                        <div class="pay-progress-wrap">
                            <div class="pay-progress-info">
                                <span>₱{{ number_format($dr->amount_paid, 0) }} paid</span>
                                <strong>{{ number_format($paidPct, 0) }}%</strong>
                            </div>
                            <div class="pay-progress-bar">
                                <div class="pay-progress-fill" style="width: {{ $paidPct }}%"></div>
                            </div>
                        </div>

                        @if($isRejected)
                            <div class="rejected-alert" style="flex-direction: column; gap: 10px; margin-bottom: 12px;">
                                <div style="display: flex; gap: 10px; align-items: flex-start; width: 100%;">
                                    <svg class="rejected-alert-icon" width="20" height="20" fill="none" stroke="#EF4444" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 800; color: #991B1B; margin-bottom: 2px;">Payment Rejected</div>
                                        <div style="font-size: 12px; color: #7F1D1D; line-height: 1.4;">Your previous payment was rejected. Please tap to pay again.</div>
                                    </div>
                                </div>
                                @if($latestPayment && $latestPayment->rejection_reason)
                                    <div style="width: 100%; background: #FEE2E2; border: 1px solid #FECACA; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #991B1B;">
                                        Reason: {{ $latestPayment->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="pay-card-amount">
                            <div>
                                <div class="pay-card-amount-lbl">Amount to Pay</div>
                                <div class="pay-card-amount-val">₱{{ number_format($remaining, 2) }}</div>
                            </div>
                            <div class="pay-card-arrow">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    @endif
@endif

{{-- ═══ HISTORY TAB ═══ --}}
@if($activeTab === 'history')
    @if($payments->isEmpty())
        <div class="pay-empty">
            <div class="pay-empty-icon">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>
            </div>
            <div class="pay-empty-title">No payments yet</div>
            <div class="pay-empty-text">Wala pa kay na-record nga payment</div>
            <a href="{{ route('portal.payments.create') }}" class="pay-empty-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Record first payment
            </a>
        </div>
    @else
        <div class="pay-section-label">
            <span>Recent Payments</span>
            <span class="pay-section-count">{{ $stats['total'] }}</span>
        </div>
        <div class="pay-list">
            @foreach($payments as $p)
                @php $vStatus = $p->verification_status ?? 'pending'; @endphp
                <a href="{{ route('portal.payments.show', $p->id) }}" class="pay-card">
                    <div class="pay-card-stripe" data-status="{{ $vStatus === 'verified' ? 'paid' : ($vStatus === 'rejected' ? 'rejected' : 'pending') }}"></div>

                    <div class="pay-card-head">
                        <div class="pay-card-dr">
                            <span style="display:grid;place-items:center;width:22px;height:22px;border-radius:6px;background:{{ $p->method_color }}22;color:{{ $p->method_color }};font-size:11px;flex-shrink:0;">{{ $p->method_icon }}</span>
                            {{ $p->payment_number }}
                        </div>
                        <span class="pay-badge pay-badge-{{ $vStatus === 'verified' ? 'paid' : ($vStatus === 'rejected' ? 'rejected' : 'pending') }}">
                            {{ $vStatus === 'verified' ? 'Paid' : ($vStatus === 'rejected' ? 'Rejected' : 'Pending') }}
                        </span>
                    </div>

                    <div class="pay-card-meta">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $p->method_label }} · {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                        @if($p->reference_number)
                            <span style="opacity:.4;">·</span>
                            <span style="font-family:ui-monospace,monospace;">Ref: {{ $p->reference_number }}</span>
                        @endif
                    </div>

                    <div class="pay-card-amount">
                        <div>
                            <div class="pay-card-amount-lbl">Amount</div>
                            <div class="pay-card-amount-val">₱{{ number_format($p->amount, 2) }}</div>
                        </div>
                        <div class="pay-card-arrow">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
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

{{-- ═══ SALES TAB ═══ --}}
@if($activeTab === 'reports')
    @if(!isset($salesReports) || $salesReports->isEmpty())
        <div class="pay-empty">
            <div class="pay-empty-icon">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
            </div>
            <div class="pay-empty-title">No sales reports</div>
            <div class="pay-empty-text">Ma-generate ra ni pagka-confirm sa delivery</div>
        </div>
    @else
        @php
            $salesTotal     = $salesReports->sum(fn($r) => (float)($r->total_sales ?? 0));
            $salesPaid      = $salesReports->sum(fn($r) => (float)($r->amount_paid ?? 0));
            $salesBalance   = max(0, $salesTotal - $salesPaid);
        @endphp

        {{-- SALES MINI STATS --}}
        <div class="pay-sales-stats">
            <div class="pay-sales-stat">
                <div class="pay-sales-stat-icon" style="--c:#15803D;--bg:#E8F5E9;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                </div>
                <div class="pay-sales-stat-info">
                    <div class="pay-sales-stat-value">₱{{ number_format($salesPaid, 0) }}</div>
                    <div class="pay-sales-stat-label">Paid</div>
                </div>
            </div>
            <div class="pay-sales-stat">
                <div class="pay-sales-stat-icon" style="--c:#B45309;--bg:#FFF8E1;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div class="pay-sales-stat-info">
                    <div class="pay-sales-stat-value">₱{{ number_format($salesBalance, 0) }}</div>
                    <div class="pay-sales-stat-label">Balance</div>
                </div>
            </div>
        </div>

        <div class="pay-section-label">
            <span>Sales Reports</span>
            <span class="pay-section-count">₱{{ number_format($reportTotal ?? 0, 0) }}</span>
        </div>
        <div class="pay-list">
            @foreach($salesReports as $report)
                @php
                    $srStatus = $report->status ?? 'pending';
                    $isPaid = $srStatus === 'paid' || (float)$report->balance <= 0.01;
                    $isPartial = !$isPaid && (float)$report->amount_paid > 0;
                    $statusKey = $isPaid ? 'paid' : ($isPartial ? 'partial' : 'pending');
                    $paidPct = ($report->total_sales ?? 0) > 0 ? (($report->amount_paid ?? 0) / $report->total_sales) * 100 : 0;
                @endphp
                <a href="{{ route('portal.reports.show', $report->id) }}" class="pay-card">
                    <div class="pay-card-stripe" data-status="{{ $statusKey }}"></div>

                    <div class="pay-card-head">
                        <div class="pay-card-dr">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
                            {{ $report->report_number }}
                        </div>
                        <span class="pay-badge pay-badge-{{ $statusKey }}">
                            {{ $isPaid ? 'Paid' : ($isPartial ? 'Partial' : 'Pending') }}
                        </span>
                    </div>

                    <div class="pay-card-meta">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} – {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}
                        <span style="opacity:.4;">·</span>
                        {{ $report->total_quantity ?? 0 }} items
                    </div>

                    <div class="pay-progress-wrap">
                        <div class="pay-progress-info">
                            <span>₱{{ number_format($report->amount_paid ?? 0, 0) }} paid</span>
                            <strong>{{ number_format(min(100, $paidPct), 0) }}%</strong>
                        </div>
                        <div class="pay-progress-bar">
                            <div class="pay-progress-fill" style="width: {{ min(100, $paidPct) }}%"></div>
                        </div>
                    </div>

                    <div class="pay-card-amount">
                        <div>
                            <div class="pay-card-amount-lbl">Total Sales</div>
                            <div class="pay-card-amount-val">₱{{ number_format($report->total_sales ?? 0, 2) }}</div>
                        </div>
                        <div class="pay-card-arrow">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
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
/* ═══ PAYMENTS — MATCHING APP DESIGN ═══ */
.pay-hero{position:relative;padding:22px 20px;border-radius:18px;margin-bottom:14px;overflow:hidden;animation:fadeIn .35s ease both;background:linear-gradient(135deg,#1A1A1A 0%,#2D2D2D 55%,#3D3D3D 100%);box-shadow:0 12px 32px -12px rgba(0,0,0,.3)}
.pay-hero[data-variant="sales"]{background:linear-gradient(135deg,#14532D 0%,#166534 55%,#15803D 100%);box-shadow:0 12px 32px -12px rgba(20,83,45,.4)}
.pay-hero[data-variant="history"]{background:linear-gradient(135deg,#1E3A8A 0%,#1D4ED8 55%,#2563EB 100%);box-shadow:0 12px 32px -12px rgba(30,58,138,.4)}
.pay-hero-glow{position:absolute;top:-80px;right:-80px;width:240px;height:240px;background:radial-gradient(circle,rgba(232,163,61,.4),transparent 70%);pointer-events:none;animation:payGlow 5s ease-in-out infinite}
.pay-hero[data-variant="sales"] .pay-hero-glow{background:radial-gradient(circle,rgba(34,197,94,.4),transparent 70%)}
.pay-hero[data-variant="history"] .pay-hero-glow{background:radial-gradient(circle,rgba(96,165,250,.4),transparent 70%)}
@keyframes payGlow{0%,100%{opacity:.7;transform:scale(1);}50%{opacity:1;transform:scale(1.15);}}
.pay-hero-content{position:relative;z-index:1}
.pay-hero-label{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.12em;margin-bottom:10px}
.pay-hero-label svg{color:rgba(255,255,255,.85)}
.pay-hero-amount{font-size:36px;font-weight:800;color:#FFFFFF;letter-spacing:-.04em;line-height:1;margin-bottom:14px;font-variant-numeric:tabular-nums}
.pay-hero-amount span{font-size:22px;margin-right:2px;opacity:.8}
.pay-hero-meta{display:flex;justify-content:space-between;align-items:center;gap:10px;font-size:12px;color:rgba(255,255,255,.8);font-weight:600}
.pay-hero-cta{display:inline-flex;align-items:center;gap:4px;padding:8px 14px;background:#E8A33D;color:#FFFFFF;border-radius:100px;font-size:12px;font-weight:800;text-decoration:none;box-shadow:0 6px 16px rgba(232,163,61,.45);transition:all .15s;cursor:pointer}
.pay-hero-cta:hover{background:#D4901F;transform:translateX(3px)}
.pay-hero-cta:active{transform:translateX(3px) scale(.97)}
.pay-hero-pill{display:inline-flex;align-items:center;padding:6px 12px;background:rgba(255,255,255,.18);border-radius:100px;font-size:11px;font-weight:800;color:#FFFFFF}
.pay-hero-clear{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:rgba(34,197,94,.25);border-radius:100px;font-size:11px;font-weight:800;color:#86efac}

.pay-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;animation:fadeIn .35s ease both;animation-delay:.05s}
.pay-stat{display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 8px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;transition:all .15s}
.pay-stat:hover{border-color:#D0D0D0;transform:translateY(-2px);box-shadow:0 6px 16px -6px rgba(0,0,0,.08)}
.pay-stat-icon{width:36px;height:36px;border-radius:10px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center}
.pay-stat-value{font-size:15px;font-weight:800;color:#1A1A1A;letter-spacing:-.02em;line-height:1;font-variant-numeric:tabular-nums}
.pay-stat-label{font-size:9.5px;color:#757575;text-transform:uppercase;letter-spacing:.08em;font-weight:800;text-align:center}

.pay-tabs{display:flex;gap:6px;margin-bottom:16px;animation:fadeIn .35s ease both;animation-delay:.1s}
.pay-tab{flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:11px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;color:#757575;font-size:12.5px;font-weight:700;text-decoration:none;transition:all .15s}
.pay-tab:hover{border-color:#D0D0D0;color:#1A1A1A}
.pay-tab.active{background:#1A1A1A;color:#FFFFFF;border-color:#1A1A1A}
.pay-tab-count{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:18px;padding:0 6px;background:rgba(255,255,255,.15);color:inherit;border-radius:10px;font-size:10.5px;font-weight:800}
.pay-tab:not(.active) .pay-tab-count{background:#F5F5F5;color:#757575}

.pay-section-label{display:flex;justify-content:space-between;align-items:center;margin:0 4px 12px}
.pay-section-label span:first-child{font-size:12px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.08em}
.pay-section-count{font-size:11px;font-weight:700;color:#757575;background:#F5F5F5;padding:3px 10px;border-radius:100px}

.pay-list{display:flex;flex-direction:column;gap:10px;animation:fadeIn .4s ease both;animation-delay:.15s}

.pay-card{position:relative;display:block;padding:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;text-decoration:none;color:inherit;transition:all .2s cubic-bezier(.4,0,.2,1);overflow:hidden}
.pay-card:hover{transform:translateY(-2px);box-shadow:0 12px 24px -10px rgba(0,0,0,.1);border-color:transparent}
.pay-card:active{transform:scale(.99)}

.pay-card-stripe{position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0}
.pay-card-stripe[data-status="pending"]{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.pay-card-stripe[data-status="partial"],
.pay-card-stripe[data-status="transit"]{background:linear-gradient(90deg,#2563EB,#60A5FA)}
.pay-card-stripe[data-status="paid"]{background:linear-gradient(90deg,#15803D,#4ADE80)}
.pay-card-stripe[data-status="rejected"]{background:linear-gradient(90deg,#B91C1C,#EF4444)}

.pay-card-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:8px;margin-top:4px}
.pay-card-dr{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0;letter-spacing:-.01em}
.pay-card-dr svg{color:#9E9E9E;flex-shrink:0}
.pay-badge{display:inline-flex;padding:3px 9px;border-radius:100px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;flex-shrink:0}
.pay-badge-pending{background:#FFF8E1;color:#B45309}
.pay-badge-partial,
.pay-badge-transit{background:#EFF6FF;color:#1D4ED8}
.pay-badge-paid{background:#E8F5E9;color:#15803D}
.pay-badge-rejected{background:#FFEBEE;color:#B91C1C}

.pay-card-meta{display:flex;align-items:center;gap:5px;font-size:10.5px;color:#757575;font-weight:600;margin-bottom:10px;flex-wrap:wrap}
.pay-card-meta svg{color:#9E9E9E;flex-shrink:0}

.pay-progress-wrap{margin-bottom:12px}
.pay-progress-info{display:flex;justify-content:space-between;align-items:center;font-size:10.5px;color:#757575;font-weight:600;margin-bottom:5px}
.pay-progress-info strong{color:#1A1A1A;font-weight:800;font-variant-numeric:tabular-nums}
.pay-progress-bar{width:100%;height:6px;background:#F5F5F5;border-radius:100px;overflow:hidden}
.pay-progress-fill{height:100%;background:linear-gradient(90deg,#E8A33D,#F0B85A);border-radius:100px;transition:width .6s cubic-bezier(.4,0,.2,1);min-width:4px;box-shadow:0 0 8px rgba(232,163,61,.4)}

.pay-card-amount{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;padding-top:10px;border-top:1px dashed #F0F0F0}
.pay-card-amount-lbl{font-size:9.5px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px}
.pay-card-amount-val{font-size:20px;font-weight:800;color:#D4901F;letter-spacing:-.03em;font-variant-numeric:tabular-nums;line-height:1}
.pay-card-arrow{color:#C0C0C0;transition:all .2s;display:grid;place-items:center}
.pay-card:hover .pay-card-arrow{color:#D4901F;transform:translateX(3px)}

.pay-sales-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px;animation:fadeIn .35s ease both}
.pay-sales-stat{display:flex;align-items:center;gap:10px;padding:12px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px}
.pay-sales-stat-icon{width:34px;height:34px;border-radius:9px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0}
.pay-sales-stat-info{flex:1;min-width:0}
.pay-sales-stat-value{font-size:15px;font-weight:800;color:#1A1A1A;letter-spacing:-.02em;line-height:1.1;font-variant-numeric:tabular-nums}
.pay-sales-stat-label{font-size:9.5px;color:#757575;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-top:2px}

.pay-empty{text-align:center;padding:48px 20px;background:#FFFFFF;border:1px dashed #E0E0E0;border-radius:14px;animation:fadeIn .35s ease both}
.pay-empty-icon{width:64px;height:64px;margin:0 auto 14px;border-radius:16px;background:#FFF8EC;border:1px solid #F0E0C0;color:#D4901F;display:grid;place-items:center}
.pay-empty-title{font-size:15px;font-weight:800;color:#1A1A1A;margin-bottom:5px}
.pay-empty-text{font-size:12.5px;color:#757575;margin-bottom:18px;font-weight:500}
.pay-empty-btn{display:inline-flex;align-items:center;gap:6px;padding:12px 20px;background:#E8A33D;border-radius:100px;color:#FFFFFF;font-size:13px;font-weight:800;text-decoration:none;box-shadow:0 4px 14px rgba(232,163,61,.35);transition:all .15s}
.pay-empty-btn:hover{background:#D4901F;transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,163,61,.45)}
.pay-empty-btn:active{transform:scale(.97)}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

/* REJECTED ALERT */
@keyframes slideInAlert { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pulseGlowRed { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); border-color: #FECACA; } 50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); border-color: #EF4444; } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); border-color: #FECACA; } }
@keyframes shakeWarning { 0%, 100% { transform: rotate(0deg); } 25% { transform: rotate(10deg); } 75% { transform: rotate(-10deg); } }
.rejected-alert { background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #EF4444; border-radius: 10px; padding: 12px; margin-bottom: 12px; display: flex; gap: 10px; align-items: flex-start; animation: slideInAlert 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, pulseGlowRed 2.5s infinite 0.5s; }
.rejected-alert-icon { flex-shrink: 0; margin-top: 1px; animation: shakeWarning 2s ease-in-out infinite; }

@media(max-width:480px){
.pay-hero{padding:18px}
.pay-hero-amount{font-size:30px}
.pay-hero-amount span{font-size:19px}
.pay-stat{padding:12px 6px}
.pay-stat-icon{width:32px;height:32px}
.pay-stat-value{font-size:13px}
.pay-tab{padding:10px 10px;font-size:12px}
.pay-card{padding:12px}
.pay-card-amount-val{font-size:18px}
}
</style>
@endpush