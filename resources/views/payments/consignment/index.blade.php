@extends('layouts.admin')

@section('title', 'Payments')
@section('subtitle', 'Consignment payments from stores')

@section('actions')
    <a href="{{ route('consignment.payments.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Record Payment
    </a>
@endsection

@section('content')

@php
    $currentFilter = request('vfy', 'all');
    use App\Models\ConsignmentPayment;
    use App\Models\Store;
    use Illuminate\Support\Facades\DB;

    // Stats
    // ⚠️ STATS — VERIFIED ONLY (pending + rejected NOT counted)
    $totalPayments   = ConsignmentPayment::verified()->count();
    $totalCollected  = (float) ConsignmentPayment::verified()->sum('amount');
    $thisMonth       = (float) ConsignmentPayment::verified()
        ->whereYear('payment_date', now()->year)
        ->whereMonth('payment_date', now()->month)
        ->sum('amount');
    $todayCollected  = (float) ConsignmentPayment::verified()
        ->whereDate('payment_date', today())
        ->sum('amount');

    // Pending / Rejected counts (para sa badges)
    $pendingCount   = ConsignmentPayment::pending()->count();
    $verifiedCount  = ConsignmentPayment::verified()->count();
    $rejectedCount  = ConsignmentPayment::rejected()->count();
    $pendingAmount  = (float) ConsignmentPayment::pending()->sum('amount');

    // Recent vs unlinked
    $linkedCount = ConsignmentPayment::whereNotNull('sales_report_id')->count() ?? 0;
    $unlinkedCount = $totalPayments - $linkedCount;

    $totalStores = Store::whereHas('consignmentPayments')->count();
@endphp

@if($pendingCount > 0 && $currentFilter === 'all')
    <div class="vfy-alert">
        <div class="vfy-alert-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <div class="vfy-alert-body">
            <div class="vfy-alert-title">{{ $pendingCount }} Payment{{ $pendingCount > 1 ? 's' : '' }} Awaiting Verification</div>
            <div class="vfy-alert-desc">Customer nag-claim nakabayad. I-verify para ma-count sa sales report.</div>
        </div>
        <div class="vfy-alert-tag">₱{{ number_format($pendingAmount, 2) }}</div>
    </div>
@endif

{{-- VERIFICATION FILTER TABS --}}
<div class="vfy-tabs">
    <a href="{{ route('consignment.payments.index') }}" class="vfy-tab {{ $currentFilter === 'all' ? 'active' : '' }}">
        All <span class="vfy-tab-count">{{ $totalPayments }}</span>
    </a>
    <a href="{{ route('consignment.payments.index', ['vfy' => 'pending']) }}" class="vfy-tab {{ $currentFilter === 'pending' ? 'active' : '' }}">
        Pending <span class="vfy-tab-count">{{ $pendingCount }}</span>
    </a>
    <a href="{{ route('consignment.payments.index', ['vfy' => 'verified']) }}" class="vfy-tab {{ $currentFilter === 'verified' ? 'active' : '' }}">
        Verified <span class="vfy-tab-count">{{ $verifiedCount }}</span>
    </a>
    <a href="{{ route('consignment.payments.index', ['vfy' => 'rejected']) }}" class="vfy-tab {{ $currentFilter === 'rejected' ? 'active' : '' }}">
        Rejected <span class="vfy-tab-count">{{ $rejectedCount }}</span>
    </a>
</div>

{{-- ===== STATS GRID ===== --}}

{{-- ═══════ REJECT MODAL ═══════ --}}
{{-- ═══════════ APPROVE MODAL ═══════════ --}}
<div id="vfyApproveModal" class="vfy-modal-backdrop" onclick="if(event.target === this) closeApproveModal()">
    <div class="vfy-modal">
        <div class="vfy-modal-head vfy-modal-head-approve">
            <div class="vfy-modal-icon vfy-modal-icon-approve">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
            </div>
            <div class="vfy-modal-heading">
                <div class="vfy-modal-title">Approve Payment?</div>
                <div class="vfy-modal-sub" id="vfyApprovePaymentNum"></div>
            </div>
        </div>
        <form method="POST" id="vfyApproveForm">
            @csrf
            <div class="vfy-modal-body">
                <div class="vfy-modal-info">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                    Ma-count na sa sales report ug sa store balance ang bayad.
                </div>

                <div class="vfy-modal-summary">
                    <div class="vfy-modal-summary-row">
                        <span>Store</span>
                        <strong id="vfyApproveStore">-</strong>
                    </div>
                    <div class="vfy-modal-summary-row">
                        <span>Amount</span>
                        <strong class="gold" id="vfyApproveAmount">₱0.00</strong>
                    </div>
                </div>
            </div>
            <div class="vfy-modal-foot">
                <button type="button" class="vfy-modal-btn vfy-modal-btn-ghost" onclick="closeApproveModal()">Cancel</button>
                <button type="submit" class="vfy-modal-btn vfy-modal-btn-success">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    Confirm Approve
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════ REJECT MODAL ═══════════ --}}
<div id="vfyRejectModal" class="vfy-modal-backdrop" onclick="if(event.target === this) closeRejectModal()">
    <div class="vfy-modal">
        <div class="vfy-modal-head vfy-modal-head-reject">
            <div class="vfy-modal-icon vfy-modal-icon-reject">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </div>
            <div class="vfy-modal-heading">
                <div class="vfy-modal-title">Reject Payment?</div>
                <div class="vfy-modal-sub" id="vfyRejectPaymentNum"></div>
            </div>
        </div>
        <form method="POST" id="vfyRejectForm">
            @csrf
            <div class="vfy-modal-body">
                <div class="vfy-modal-warn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>Ang store ma-notify niini. <strong>Dili ma-count</strong> sa sales report ug store balance.</div>
                </div>

                <div class="vfy-modal-summary">
                    <div class="vfy-modal-summary-row">
                        <span>Store</span>
                        <strong id="vfyRejectStore">-</strong>
                    </div>
                    <div class="vfy-modal-summary-row">
                        <span>Amount</span>
                        <strong class="strike" id="vfyRejectAmount">₱0.00</strong>
                    </div>
                </div>

                <label class="vfy-modal-label">Reason for rejection <span style="color:#ef4444;">*</span></label>
                <textarea name="rejection_reason" class="vfy-modal-textarea" rows="3" placeholder="Example: Walay proof of payment, wrong amount, invalid reference..." required maxlength="500"></textarea>
            </div>
            <div class="vfy-modal-foot">
                <button type="button" class="vfy-modal-btn vfy-modal-btn-ghost" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="vfy-modal-btn vfy-modal-btn-danger">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>
<div class="py-stats">
    <div class="py-stat">
        <div class="py-stat-icon gold">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">Total Payments</div>
            <div class="py-stat-value">{{ number_format($totalPayments) }}</div>
            <div class="py-stat-meta">{{ $totalStores }} stores</div>
        </div>
    </div>

    <div class="py-stat">
        <div class="py-stat-icon green">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">Total Collected</div>
            <div class="py-stat-value">&#8369;{{ number_format($totalCollected/1000, 1) }}k</div>
            <div class="py-stat-meta">All time</div>
        </div>
    </div>

    <div class="py-stat">
        <div class="py-stat-icon blue">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">This Month</div>
            <div class="py-stat-value">&#8369;{{ number_format($thisMonth/1000, 1) }}k</div>
            <div class="py-stat-meta">{{ now()->format('F') }}</div>
        </div>
    </div>

    <div class="py-stat">
        <div class="py-stat-icon amber">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">Today</div>
            <div class="py-stat-value">&#8369;{{ number_format($todayCollected, 0) }}</div>
            <div class="py-stat-meta">{{ now()->format('M d') }}</div>
        </div>
    </div>
</div>

{{-- ===== STATUS TABS ===== --}}
<div class="py-tabs">
    <a href="{{ route('consignment.payments.index', request()->except(['status', 'page'])) }}"
       class="py-tab {{ !request('status') ? 'active' : '' }}">
        All
        <span class="py-tab-count">{{ $tabCounts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ route('consignment.payments.index', array_merge(request()->except(['status', 'page']), ['status' => 'paid'])) }}"
       class="py-tab {{ request('status') === 'paid' ? 'active' : '' }}">
        <span class="dot green"></span>
        Paid
        <span class="py-tab-count">{{ $tabCounts['paid'] ?? 0 }}</span>
    </a>
    <a href="{{ route('consignment.payments.index', array_merge(request()->except(['status', 'page']), ['status' => 'partial'])) }}"
       class="py-tab {{ request('status') === 'partial' ? 'active' : '' }}">
        <span class="dot amber"></span>
        Partial
        <span class="py-tab-count">{{ $tabCounts['partial'] ?? 0 }}</span>
    </a>
    <a href="{{ route('consignment.payments.index', array_merge(request()->except(['status', 'page']), ['status' => 'unlinked'])) }}"
       class="py-tab {{ request('status') === 'unlinked' ? 'active' : '' }}">
        <span class="dot red"></span>
        Unlinked
        <span class="py-tab-count">{{ $tabCounts['unlinked'] ?? 0 }}</span>
    </a>
</div>
{{-- ===== TOOLBAR ===== --}}
<div class="py-toolbar">
    <div class="py-toolbar-left">
        <div class="py-toolbar-heading">All Payments</div>
        <div class="py-toolbar-sub">{{ $totalPayments }} records</div>
    </div>

    <form method="GET" class="py-toolbar-form">
        <div class="py-search">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payment #...">
        </div>

        <select name="store" class="py-filter" onchange="this.form.submit()">
            <option value="">All Stores</option>
            @foreach($stores ?? \App\Models\Store::orderBy('store_name')->get() as $s)
                <option value="{{ $s->id }}" {{ request('store') == $s->id ? 'selected' : '' }}>
                    {{ $s->store_name }}
                </option>
            @endforeach
        </select>

        <select name="method" class="py-filter" onchange="this.form.submit()">
            <option value="">All Methods</option>
            <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="gcash" {{ request('method') == 'gcash' ? 'selected' : '' }}>GCash</option>
            <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="check" {{ request('method') == 'check' ? 'selected' : '' }}>Check</option>
            <option value="maya" {{ request('method') == 'maya' ? 'selected' : '' }}>Maya</option>
        </select>

        <button type="submit" class="py-btn-filter">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
            </svg>
            Filter
        </button>

        @if(request()->hasAny(['search', 'store', 'method']))
            <a href="{{ route('consignment.payments.index') }}" class="py-btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </a>
        @endif
    </form>
</div>

{{-- ===== PAYMENTS LIST ===== --}}
@if($payments->isEmpty())
    <div class="py-empty">
        <div class="py-empty-icon">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
        </div>
        <div class="py-empty-title">No payments found</div>
        <div class="py-empty-text">
            @if(request()->hasAny(['search', 'store', 'method']))
                Try adjusting your filters
            @else
                Start by recording your first payment
            @endif
        </div>
        <a href="{{ route('consignment.payments.create') }}" class="py-btn-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Record Payment
        </a>
    </div>
@else
    <div class="py-table-card">
        <div class="py-table-wrap">
            <table class="py-table">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Store</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th style="text-align: right;">Amount</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>
                                <div class="py-number">{{ $payment->payment_number }}</div>
                            </td>
                            <td>
                                <div class="py-store">
                                    <div class="py-store-avatar">
                                        @if($payment->store && $payment->store->logo_url)
                                            <img src="{{ $payment->store->logo_url }}" alt="">
                                        @else
                                            {{ strtoupper(substr($payment->store->store_name ?? 'ST', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="py-store-info">
                                        <div class="py-store-name">{{ $payment->store->store_name ?? '-' }}</div>
                                        <div class="py-store-code">{{ $payment->store->code ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="py-date-main">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
                                <div class="py-date-sub">{{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="py-method-badge method-{{ $payment->method }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
                                </span>
                            </td>
                            <td>
                                <div class="py-ref">{{ $payment->reference_number ?? '-' }}</div>
                            </td>
                            <td style="text-align: right;">
                                <div class="py-amount">+ &#8369;{{ number_format($payment->amount, 2) }}</div>
                            </td>
                            <td style="text-align: center;">
                                @if($payment->verification_status === 'verified')
                                    <span class="vfy-badge verified"><span class="vfy-dot"></span>Verified</span>
                                @elseif($payment->verification_status === 'rejected')
                                    <span class="vfy-badge rejected"><span class="vfy-dot"></span>Rejected</span>
                                @else
                                    <span class="vfy-badge pending"><span class="vfy-dot"></span>Pending</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($payment->verification_status === 'pending')
                                    <div class="vfy-actions">
                                        <button type="button" class="vfy-btn vfy-btn-approve" onclick="openApproveModal({{ $payment->id }}, '{{ $payment->payment_number }}', '{{ $payment->store->store_name ?? "-" }}', {{ (float) $payment->amount }})">
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                                            Approve
                                        </button>
                                        <button type="button" class="vfy-btn vfy-btn-reject" onclick="openRejectModal({{ $payment->id }}, '{{ $payment->payment_number }}', '{{ $payment->store->store_name ?? "-" }}', {{ (float) $payment->amount }})">
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <a href="{{ route('consignment.payments.show', $payment->id) }}" class="py-btn-view">
                                        View
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path d="M9 18l6-6-6-6"/>
                                        </svg>
                                    </a>
                                @endif
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($payments, 'links') && $payments->hasPages())
        <div class="py-pagination">
            {{ $payments->withQueryString()->links() }}
        </div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* ===== STATS ===== */
    .py-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .py-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px;
        background: rgba(34, 34, 44, 0.35);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        transition: all 0.25s;
    }
    .py-stat:hover {
        transform: translateY(-3px);
        border-color: rgba(169, 120, 74, 0.3);
        background: rgba(34, 34, 44, 0.45);
    }
    .py-stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: grid; place-items: center;
        flex-shrink: 0;
        border: 1px solid;
    }
    .py-stat-icon.gold  { background: rgba(169, 120, 74, 0.14); border-color: rgba(169, 120, 74, 0.3); color: #c9a961; }
    .py-stat-icon.green { background: rgba(34, 197, 94, 0.14);  border-color: rgba(34, 197, 94, 0.3);  color: #22c55e; }
    .py-stat-icon.blue  { background: rgba(59, 130, 246, 0.14); border-color: rgba(59, 130, 246, 0.3); color: #3b82f6; }
    .py-stat-icon.amber { background: rgba(245, 158, 11, 0.14); border-color: rgba(245, 158, 11, 0.3); color: #f59e0b; }

    .py-stat-body { flex: 1; min-width: 0; }
    .py-stat-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 5px;
    }
    .py-stat-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em;
        line-height: 1;
        margin-bottom: 4px;
        font-variant-numeric: tabular-nums;
    }
    .py-stat-meta {
        font-size: 11px;
        color: var(--text-muted);
    }

    /* ===== STATUS TABS ===== */
    .py-tabs {
        display: flex;
        gap: 6px;
        padding: 4px;
        background: rgba(20, 20, 26, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 13px;
        margin-bottom: 16px;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .py-tabs::-webkit-scrollbar { display: none; }
    .py-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .py-tab:hover:not(.active) {
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
    }
    .py-tab.active {
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.6);
    }
    .py-tab .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .py-tab .dot.green {
        background: #22c55e;
        box-shadow: 0 0 6px rgba(34, 197, 94, 0.7);
    }
    .py-tab .dot.amber {
        background: #f59e0b;
        box-shadow: 0 0 6px rgba(245, 158, 11, 0.7);
    }
    .py-tab .dot.red {
        background: #ef4444;
        box-shadow: 0 0 6px rgba(239, 68, 68, 0.7);
    }
    .py-tab.active .dot {
        background: #fff;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
    }
    .py-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 18px;
        padding: 0 6px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 800;
    }
    .py-tab:not(.active) .py-tab-count {
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-muted);
    }
    /* ===== TOOLBAR ===== */
    .py-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .py-toolbar-left {
        flex-shrink: 0;
    }
    .py-toolbar-heading {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }
    .py-toolbar-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .py-toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex: 1;
        justify-content: flex-end;
        min-width: 0;
    }
    .py-search {
        position: relative;
        display: flex;
        align-items: center;
        flex: 1 1 200px;
        max-width: 280px;
        min-width: 180px;
    }
    .py-search svg {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .py-search input {
        width: 100%;
        padding: 9px 14px 9px 38px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all 0.15s;
    }
    .py-search input::placeholder { color: var(--text-muted); }
    .py-search input:focus {
        border-color: #c9a961;
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.12);
    }
    .py-filter {
        padding: 9px 32px 9px 12px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        width: 150px;
        flex-shrink: 0;
    }
    .py-btn-filter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.6);
    }
    .py-btn-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        text-decoration: none;
    }

    /* ===== TABLE ===== */
    .py-table-card {
        background: rgba(34, 34, 44, 0.35);
        backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        overflow: hidden;
    }
    .py-table-wrap {
        overflow-x: auto;
    }
    .py-table {
        width: 100%;
        border-collapse: collapse;
    }
    .py-table thead th {
        text-align: left;
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 14px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(20, 20, 26, 0.4);
        white-space: nowrap;
    }
    .py-table tbody td {
        padding: 14px 18px;
        font-size: 13px;
        color: var(--text-secondary);
        border-bottom: 1px solid rgba(38, 38, 46, 0.5);
        vertical-align: middle;
    }
    .py-table tbody tr {
        transition: background 0.15s;
    }
    .py-table tbody tr:hover {
        background: rgba(169, 120, 74, 0.04);
    }
    .py-table tbody tr:last-child td {
        border-bottom: none;
    }

    .py-number {
        font-family: ui-monospace, monospace;
        font-size: 12px;
        font-weight: 700;
        color: #c9a961;
    }

    .py-store {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .py-store-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        overflow: hidden;
        flex-shrink: 0;
    }
    .py-store-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .py-store-name {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 2px;
        white-space: nowrap;
    }
    .py-store-code {
        font-size: 10px;
        color: var(--text-muted);
        font-family: ui-monospace, monospace;
    }

    .py-date-main {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 2px;
    }
    .py-date-sub {
        font-size: 10.5px;
        color: var(--text-muted);
    }

    .py-method-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .py-method-badge.method-cash {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.25);
    }
    .py-method-badge.method-gcash,
    .py-method-badge.method-maya {
        background: rgba(59, 130, 246, 0.12);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }
    .py-method-badge.method-bank_transfer,
    .py-method-badge.method-check {
        background: rgba(169, 120, 74, 0.12);
        color: #c9a961;
        border: 1px solid rgba(169, 120, 74, 0.25);
    }

    .py-ref {
        font-family: ui-monospace, monospace;
        font-size: 11px;
        color: var(--text-muted);
    }

    .py-amount {
        font-size: 14px;
        font-weight: 800;
        color: #22c55e;
        font-variant-numeric: tabular-nums;
    }

    .py-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 7px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .py-status::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
        margin-right: 5px;
    }
    .py-status.linked {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
    }
    .py-status.unlinked {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .py-btn-view {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 14px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 8px;
        color: #c9a961;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s;
    }
    .py-btn-view:hover {
        background: rgba(169, 120, 74, 0.2);
        border-color: rgba(169, 120, 74, 0.4);
    }

    /* ===== EMPTY ===== */
    .py-empty {
        padding: 60px 20px;
        text-align: center;
        background: rgba(34, 34, 44, 0.3);
        backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
    }
    .py-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 16px;
        border-radius: 20px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        display: grid;
        place-items: center;
        color: #c9a961;
    }
    .py-empty-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .py-empty-text {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-bottom: 18px;
    }
    .py-btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 11px 20px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border-radius: 11px;
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.6);
    }

    /* ===== PAGINATION ===== */
    .py-pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
        .py-stats { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .py-toolbar-form {
            width: 100%;
            justify-content: flex-start;
        }
        .py-search {
            max-width: 100%;
            flex: 1 1 100%;
        }
        .py-filter {
            flex: 1 1 auto;
            width: auto;
        }
    }
    @media (max-width: 700px) {
        .py-stats { grid-template-columns: 1fr; }
        .py-toolbar-form {
            flex-direction: column;
            align-items: stretch;
        }
        .py-search { width: 100%; max-width: 100%; }
        .py-filter { width: 100%; }
        .py-btn-filter { width: 100%; justify-content: center; }
    }

    /* ═══════════ VERIFICATION BADGES ═══════════ */
    .vfy-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border: 1px solid;
        white-space: nowrap;
    }
    .vfy-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .vfy-badge.pending {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.35);
    }
    .vfy-badge.pending .vfy-dot { background: #f59e0b; animation: vfyPulse 2s ease-in-out infinite; }
    .vfy-badge.verified {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.35);
    }
    .vfy-badge.verified .vfy-dot { background: #22c55e; }
    .vfy-badge.rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.35);
    }
    .vfy-badge.rejected .vfy-dot { background: #ef4444; }

    @keyframes vfyPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* INLINE VERIFY ACTIONS */
    .vfy-actions {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .vfy-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 6px 11px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        border: 1px solid;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .vfy-btn-approve {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 12px -4px rgba(34, 197, 94, 0.5);
    }
    .vfy-btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px -4px rgba(34, 197, 94, 0.7);
    }
    .vfy-btn-reject {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }
    .vfy-btn-reject:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.5);
        color: #fca5a5;
    }

    /* PENDING ALERT BANNER */
    .vfy-alert {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.03));
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-left: 3px solid #f59e0b;
        border-radius: 14px;
        animation: vfyAlertIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes vfyAlertIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .vfy-alert-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #f59e0b;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        animation: vfyPulse 2s ease-in-out infinite;
    }
    .vfy-alert-body { flex: 1; min-width: 0; }
    .vfy-alert-title {
        font-size: 13px;
        font-weight: 800;
        color: #fafafa;
        margin-bottom: 3px;
    }
    .vfy-alert-desc {
        font-size: 11.5px;
        color: #a1a1aa;
    }
    .vfy-alert-desc strong { color: #f59e0b; }
    .vfy-alert-btn {
        padding: 9px 16px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
        flex-shrink: 0;
        box-shadow: 0 4px 12px -4px rgba(245, 158, 11, 0.5);
    }
    .vfy-alert-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px -4px rgba(245, 158, 11, 0.7);
    }

    /* FILTER TABS */
    .vfy-tabs {
        display: inline-flex;
        gap: 4px;
        padding: 4px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        margin-bottom: 18px;
    }
    .vfy-tab {
        padding: 8px 16px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 800;
        color: #a1a1aa;
        text-decoration: none;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .vfy-tab:hover { color: #fafafa; background: rgba(255, 255, 255, 0.04); }
    .vfy-tab.active {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }
    .vfy-tab-count {
        padding: 1px 7px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 100px;
        font-size: 10px;
        font-weight: 900;
        min-width: 18px;
        text-align: center;
    }
    .vfy-tab.active .vfy-tab-count {
        background: rgba(15, 15, 20, 0.25);
    }

    .vfy-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }
    .vfy-modal-backdrop.open { display: flex; }
    .vfy-modal {
        background: linear-gradient(165deg, #1e1a16, #15120f);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        width: 100%;
        max-width: 440px;
        overflow: hidden;
        box-shadow: 0 30px 80px -20px rgba(0, 0, 0, 0.9);
        animation: vfyModalIn 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes vfyModalIn {
        from { transform: scale(0.94) translateY(12px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }
    .vfy-modal-head {
        display: flex; align-items: center; gap: 14px;
        padding: 22px 24px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .vfy-modal-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .vfy-modal-title {
        font-size: 16px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.02em; margin-bottom: 3px;
    }
    .vfy-modal-sub {
        font-size: 11px; color: #71717a;
        font-family: ui-monospace, monospace;
    }
    .vfy-modal-body { padding: 20px 24px; }
    .vfy-modal-warn {
        display: flex; align-items: flex-start; gap: 8px;
        padding: 11px 13px;
        margin-bottom: 16px;
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.22);
        border-radius: 10px;
        font-size: 11.5px; color: #d4a35a; line-height: 1.5;
    }
    .vfy-modal-warn svg { flex-shrink: 0; margin-top: 2px; color: #f59e0b; }
    .vfy-modal-label {
        display: block;
        font-size: 10.5px; font-weight: 800;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }
    .vfy-modal-textarea {
        width: 100%;
        padding: 11px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: #fafafa;
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
    }
    .vfy-modal-textarea:focus {
        outline: none;
        border-color: rgba(239, 68, 68, 0.4);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    .vfy-modal-textarea::placeholder { color: #52525b; }
    .vfy-modal-foot {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 18px 24px 22px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .vfy-modal-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 7px;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 12.5px; font-weight: 800;
        border: 1px solid;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
    }
    .vfy-modal-btn-ghost {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.1);
        color: #d4d4d8;
    }
    .vfy-modal-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }
    .vfy-modal-btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(239, 68, 68, 0.5);
    }
    .vfy-modal-btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px -6px rgba(239, 68, 68, 0.7);
    }

    /* ═══════════════════════════════════════════
       PAYMENT VERIFICATION UI
       ═══════════════════════════════════════════ */

    /* BADGES */
    .vfy-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border: 1px solid;
        white-space: nowrap;
    }
    .vfy-dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .vfy-badge.pending {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.35);
    }
    .vfy-badge.pending .vfy-dot {
        background: #f59e0b;
        animation: vfyPulse 2s ease-in-out infinite;
    }
    .vfy-badge.verified {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.35);
    }
    .vfy-badge.verified .vfy-dot { background: #22c55e; }
    .vfy-badge.rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.35);
    }
    .vfy-badge.rejected .vfy-dot { background: #ef4444; }

    @keyframes vfyPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* ACTIONS */
    .vfy-actions {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
    }
    .vfy-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 6px 11px;
        border-radius: 8px;
        font-size: 10.5px;
        font-weight: 800;
        border: 1px solid;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .vfy-btn-approve {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 3px 10px -3px rgba(34, 197, 94, 0.5);
    }
    .vfy-btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px -3px rgba(34, 197, 94, 0.7);
    }
    .vfy-btn-reject {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }
    .vfy-btn-reject:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.5);
        color: #fca5a5;
    }

    /* PENDING ALERT */
    .vfy-alert {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        margin-bottom: 16px;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.03));
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-left: 3px solid #f59e0b;
        border-radius: 14px;
        animation: vfyAlertIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes vfyAlertIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .vfy-alert-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #f59e0b;
        display: grid; place-items: center;
        flex-shrink: 0;
        animation: vfyPulse 2s ease-in-out infinite;
    }
    .vfy-alert-body { flex: 1; min-width: 0; }
    .vfy-alert-title {
        font-size: 13px; font-weight: 800; color: #fafafa;
        margin-bottom: 2px;
    }
    .vfy-alert-desc { font-size: 11.5px; color: #a1a1aa; }
    .vfy-alert-desc strong { color: #f59e0b; font-weight: 800; }
    .vfy-alert-tag {
        padding: 3px 9px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 100px;
        font-size: 11px; font-weight: 800;
        color: #f59e0b;
        font-family: ui-monospace, monospace;
        flex-shrink: 0;
    }

    /* FILTER TABS */
    .vfy-tabs {
        display: inline-flex;
        gap: 4px;
        padding: 4px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .vfy-tab {
        padding: 8px 14px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 800;
        color: #a1a1aa;
        text-decoration: none;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .vfy-tab:hover { color: #fafafa; background: rgba(255, 255, 255, 0.04); }
    .vfy-tab.active {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }
    .vfy-tab-count {
        padding: 1px 7px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 100px;
        font-size: 10px;
        font-weight: 900;
        min-width: 18px;
        text-align: center;
    }
    .vfy-tab.active .vfy-tab-count {
        background: rgba(15, 15, 20, 0.25);
    }

    /* ═════════ MODALS ═════════ */
    .vfy-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }
    .vfy-modal-backdrop.open {
        display: flex;
        animation: vfyFadeIn 0.2s ease-out;
    }
    @keyframes vfyFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .vfy-modal {
        background: linear-gradient(165deg, #1e1a16 0%, #15120f 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        width: 100%;
        max-width: 460px;
        overflow: hidden;
        box-shadow: 0 40px 100px -30px rgba(0, 0, 0, 0.9),
                    0 0 0 1px rgba(255, 255, 255, 0.03);
        animation: vfyModalIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes vfyModalIn {
        from { transform: scale(0.94) translateY(12px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }

    .vfy-modal-head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 22px 24px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .vfy-modal-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .vfy-modal-icon-approve {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.35);
        box-shadow: 0 6px 20px -6px rgba(34, 197, 94, 0.4);
    }
    .vfy-modal-icon-reject {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.35);
        box-shadow: 0 6px 20px -6px rgba(239, 68, 68, 0.4);
    }
    .vfy-modal-heading { flex: 1; min-width: 0; }
    .vfy-modal-title {
        font-size: 17px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        margin-bottom: 3px;
    }
    .vfy-modal-sub {
        font-size: 11px;
        color: #71717a;
        font-family: ui-monospace, monospace;
    }

    .vfy-modal-body { padding: 20px 24px; }
    .vfy-modal-warn,
    .vfy-modal-info {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 12px 14px;
        margin-bottom: 16px;
        border-radius: 11px;
        font-size: 11.5px;
        line-height: 1.55;
    }
    .vfy-modal-warn {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.22);
        color: #d4a35a;
    }
    .vfy-modal-warn svg { flex-shrink: 0; margin-top: 2px; color: #f59e0b; }
    .vfy-modal-warn strong { color: #f59e0b; font-weight: 800; }
    .vfy-modal-info {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.22);
        color: #86efac;
    }
    .vfy-modal-info svg { flex-shrink: 0; margin-top: 2px; color: #22c55e; }

    .vfy-modal-summary {
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.25);
        border-radius: 11px;
        margin-bottom: 16px;
    }
    .vfy-modal-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 12.5px;
    }
    .vfy-modal-summary-row + .vfy-modal-summary-row {
        border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
    .vfy-modal-summary-row span { color: #71717a; font-weight: 600; }
    .vfy-modal-summary-row strong {
        color: #fafafa;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        text-align: right;
        max-width: 65%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .vfy-modal-summary-row strong.gold { color: #c9a961; }
    .vfy-modal-summary-row strong.strike {
        color: #ef4444;
        text-decoration: line-through;
        opacity: 0.7;
    }

    .vfy-modal-label {
        display: block;
        font-size: 10.5px;
        font-weight: 800;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }
    .vfy-modal-textarea {
        width: 100%;
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa;
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
        transition: all 0.15s;
        line-height: 1.55;
    }
    .vfy-modal-textarea:focus {
        outline: none;
        border-color: rgba(239, 68, 68, 0.4);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        background: rgba(239, 68, 68, 0.03);
    }
    .vfy-modal-textarea::placeholder { color: #52525b; }

    .vfy-modal-foot {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 18px 24px 22px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .vfy-modal-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 12px 18px;
        border-radius: 11px;
        font-size: 12.5px;
        font-weight: 800;
        border: 1px solid;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
        min-height: 46px;
    }
    .vfy-modal-btn-ghost {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.1);
        color: #d4d4d8;
    }
    .vfy-modal-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }
    .vfy-modal-btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(34, 197, 94, 0.5);
    }
    .vfy-modal-btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px -6px rgba(34, 197, 94, 0.7);
    }
    .vfy-modal-btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(239, 68, 68, 0.5);
    }
    .vfy-modal-btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px -6px rgba(239, 68, 68, 0.7);
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    // ─── APPROVE MODAL ───
    window.openApproveModal = function(id, num, store, amount) {
        document.getElementById('vfyApprovePaymentNum').textContent = num;
        document.getElementById('vfyApproveStore').textContent = store;
        document.getElementById('vfyApproveAmount').textContent = '₱' + Number(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('vfyApproveForm').action = '/consignment/payments/' + id + '/verify';
        document.getElementById('vfyApproveModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeApproveModal = function() {
        document.getElementById('vfyApproveModal').classList.remove('open');
        document.body.style.overflow = '';
    };

    // ─── REJECT MODAL ───
    window.openRejectModal = function(id, num, store, amount) {
        document.getElementById('vfyRejectPaymentNum').textContent = num;
        document.getElementById('vfyRejectStore').textContent = store;
        document.getElementById('vfyRejectAmount').textContent = '₱' + Number(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('vfyRejectForm').action = '/consignment/payments/' + id + '/reject';
        document.getElementById('vfyRejectModal').classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(function() {
            var ta = document.querySelector('#vfyRejectModal textarea');
            if (ta) ta.focus();
        }, 200);
    };

    window.closeRejectModal = function() {
        document.getElementById('vfyRejectModal').classList.remove('open');
        document.body.style.overflow = '';
    };

    // ─── ESC KEY ───
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeApproveModal();
            closeRejectModal();
        }
    });

    // ─── AUTO-CLEAR FLASH ───
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[Verification] Modals ready');
    });
})();
</script>
@endpush
