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
    use App\Models\ConsignmentPayment;
    use App\Models\Store;
    use Illuminate\Support\Facades\DB;

    // Stats
    $totalPayments = ConsignmentPayment::count();
    $totalCollected = (float) ConsignmentPayment::sum('amount');
    $thisMonth = (float) ConsignmentPayment::whereYear('payment_date', now()->year)
        ->whereMonth('payment_date', now()->month)
        ->sum('amount');
    $todayCollected = (float) ConsignmentPayment::whereDate('payment_date', today())->sum('amount');

    // Recent vs unlinked
    $linkedCount = ConsignmentPayment::whereNotNull('sales_report_id')->count() ?? 0;
    $unlinkedCount = $totalPayments - $linkedCount;

    $totalStores = Store::whereHas('consignmentPayments')->count();
@endphp

{{-- ===== STATS GRID ===== --}}
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
                                @if($payment->sales_report_id)
                                    <span class="py-status linked">Linked</span>
                                @else
                                    <span class="py-status unlinked">Unlinked</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('consignment.payments.show', $payment->id) }}" class="py-btn-view">
                                    View
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M9 18l6-6-6-6"/>
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
</style>
@endpush