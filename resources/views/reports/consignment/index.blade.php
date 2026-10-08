@extends('layouts.admin')

@section('title', 'Sales Reports')
@section('subtitle', 'Manage sales and payments per store')

@section('actions')
    <a href="{{ route('consignment.reports.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        New Report
    </a>
@endsection

@section('content')

{{-- ========== SUMMARY CARDS ========== --}}
<div class="summary-grid">
    <div class="summary">
        <div class="summary-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['total'] }}</div>
            <div class="summary-label">Reports</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">₱{{ number_format($stats['total_sales'], 0) }}</div>
            <div class="summary-label">Total Sales</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">₱{{ number_format($stats['total_paid'], 0) }}</div>
            <div class="summary-label">Total Paid</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">₱{{ number_format($stats['total_balance'], 0) }}</div>
            <div class="summary-label">Balance</div>
        </div>
    </div>
</div>

{{-- ========== TABS ========== --}}
<div class="tabs">
    @php
        $tabList = [
            'all'     => ['label' => 'All Reports'],
            'pending' => ['label' => 'Pending'],
            'partial' => ['label' => 'Partial'],
            'paid'    => ['label' => 'Paid'],
        ];
    @endphp
    @foreach($tabList as $key => $item)
        <a href="{{ route('consignment.reports.index', array_merge(request()->only(['search','store']), ['tab' => $key])) }}"
           class="tab {{ $tab === $key ? 'active' : '' }}">
            <span>{{ $item['label'] }}</span>
            <span class="tab-count">{{ $tabCounts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

{{-- ========== TOOLBAR ========== --}}
<div class="toolbar">
    <form method="GET" class="toolbar-form">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="search-box">
            <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search report #, store, or DR..." class="search-input">
        </div>

        <select name="store" class="filter-select" onchange="this.form.submit()">
            <option value="">All Stores</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}" {{ request('store')==$s->id?'selected':'' }}>{{ $s->store_name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn-filter">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>

        @if(request()->hasAny(['search','store']))
            <a href="{{ route('consignment.reports.index', ['tab' => $tab]) }}" class="btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif
    </form>
</div>

{{-- ========== MANAGEMENT TABLE ========== --}}
@if($reports->isEmpty())
    <div class="card">
        <div class="empty">
            <div class="empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </div>
            <div class="empty-title">Walay reports</div>
            <div class="empty-text">
                @if(request()->hasAny(['search','store']) || $tab !== 'all')
                    Try adjusting your filters or tab
                @else
                    Create your first sales report to get started
                @endif
            </div>
        </div>
    </div>
@else
    <div class="card" style="padding:0; overflow:hidden;">
        <div class="table-wrap">
            <table class="mgmt-table">
                <thead>
                    <tr>
                        <th style="width:36px;"></th>
                        <th>Report #</th>
                        <th>Store</th>
                        <th>Period</th>
                        <th style="text-align:center;">Items</th>
                        <th style="text-align:right;">Total Sales</th>
                        <th style="text-align:right;">Paid</th>
                        <th style="text-align:right;">Balance</th>
                        <th>Status</th>
                        <th style="width:100px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr class="mgmt-row" data-id="{{ $report->id }}">
                            <td class="expand-cell">
                                <button type="button" class="expand-btn" data-report-id="{{ $report->id }}" aria-label="Expand">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                </button>
                            </td>
                            <td>
                                <div class="cell-primary" style="color:#c9a961; font-family:ui-monospace; font-weight:700;">{{ $report->report_number }}</div>
                                @if($report->deliveryReceipt)
                                    <div class="cell-secondary">DR: {{ $report->deliveryReceipt->dr_number }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="cell-primary">{{ $report->store->store_name ?? '-' }}</div>
                                <div class="cell-secondary">{{ $report->store->code ?? '' }}</div>
                            </td>
                            <td>
                                <div class="cell-primary">{{ \Carbon\Carbon::parse($report->period_from)->format('M d') }} - {{ \Carbon\Carbon::parse($report->period_to)->format('M d, Y') }}</div>
                                <div class="cell-secondary">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td style="text-align:center;">
                                <span class="items-badge">{{ $report->items->count() }}</span>
                            </td>
                            <td style="text-align:right;" class="money">
                                ₱{{ number_format($report->total_sales, 2) }}
                            </td>
                            <td style="text-align:right;" class="money green">
                                ₱{{ number_format($report->amount_paid, 2) }}
                            </td>
                            <td style="text-align:right;" class="money {{ $report->balance > 0 ? 'amber' : 'green' }}">
                                ₱{{ number_format($report->balance, 2) }}
                            </td>
                            <td>
                                @if($report->status === 'paid')
                                    <span class="badge badge-paid">✓ Paid</span>
                                @elseif($report->status === 'partial')
                                    <span class="badge badge-partial">◐ Partial</span>
                                @else
                                    <span class="badge badge-pending">⏱ Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('consignment.reports.show', $report) }}" class="btn-icon" title="View report">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    @if($report->balance > 0)
                                        <a href="{{ route('consignment.payments.create', ['store_id' => $report->store_id, 'sales_report_id' => $report->id]) }}" class="btn-icon accent" title="Record Payment">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- EXPANDABLE DETAILS --}}
                        <tr class="products-row" id="products-{{ $report->id }}" data-products-row="{{ $report->id }}" style="display:none;">
                            <td colspan="10">
                                <div class="products-panel">
                                    <div class="details-grid">

                                        {{-- LEFT: Products --}}
                                        <div class="details-col">
                                            <div class="details-header">
                                                <div class="details-title">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                    Products Sold
                                                </div>
                                                <div class="details-count">{{ $report->items->count() }} item(s)</div>
                                            </div>
                                            @if($report->items->count() > 0)
                                                <table class="mini-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th style="text-align:right;">Qty</th>
                                                            <th style="text-align:right;">Unit Price</th>
                                                            <th style="text-align:right;">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($report->items as $item)
                                                            <tr>
                                                                <td>
                                                                    <div style="font-weight:600; color:var(--text-primary);">{{ $item->product->name ?? '-' }}</div>
                                                                    @if($item->product->sku)
                                                                        <div style="font-size:10px; color:var(--text-muted); font-family:ui-monospace;">{{ $item->product->sku }}</div>
                                                                    @endif
                                                                </td>
                                                                <td style="text-align:right;">{{ $item->quantity_sold }}</td>
                                                                <td style="text-align:right;" class="money">₱{{ number_format($item->unit_price, 2) }}</td>
                                                                <td style="text-align:right;" class="money strong">₱{{ number_format($item->subtotal, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="details-empty">Walay products sa report niini.</div>
                                            @endif
                                        </div>

                                        {{-- RIGHT: Payment History --}}
                                        <div class="details-col">
                                            <div class="details-header">
                                                <div class="details-title green">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                                    Payment History
                                                </div>
                                                <div class="details-count">{{ $report->store_payments->count() }} payment(s)</div>
                                            </div>
                                            @php
                                                $payments = $report->deliveryReceipt 
                                                    ? $report->deliveryReceipt->payments 
                                                    : $report->store_payments;
                                            @endphp
                                            @if($payments->count() > 0)
                                                <table class="mini-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Method</th>
                                                            <th>Ref #</th>
                                                            <th style="text-align:right;">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($payments as $p)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                                                                <td>
                                                                    <span class="method-badge {{ $p->method }}">
                                                                        {{ $p->method_icon ?? '💵' }} {{ ucfirst($p->method) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($p->reference_number)
                                                                        <span class="mono">{{ $p->reference_number }}</span>
                                                                    @else
                                                                        <span style="color:var(--text-muted);">—</span>
                                                                    @endif
                                                                </td>
                                                                <td style="text-align:right;" class="money green">₱{{ number_format($p->amount, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="details-empty">Wala pay bayad.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="totals-row">
                        <td colspan="5" style="text-align:right; font-weight:800;">TOTALS (page):</td>
                        <td style="text-align:right;" class="money strong">₱{{ number_format($reports->sum('total_sales'), 2) }}</td>
                        <td style="text-align:right;" class="money green strong">₱{{ number_format($reports->sum('amount_paid'), 2) }}</td>
                        <td style="text-align:right;" class="money amber strong">₱{{ number_format($reports->sum('balance'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if(method_exists($reports, 'links') && $reports->hasPages())
        <div class="pagination-wrap">{{ $reports->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* ========== SUMMARY ========== */
    .summary-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:16px; }
    .summary {
        background:rgba(34,34,44,0.55); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
        border:1px solid rgba(255,255,255,0.06); border-radius:14px;
        padding:16px; display:flex; align-items:center; gap:12px;
        transition:all 0.2s;
    }
    .summary:hover { transform:translateY(-2px); border-color:rgba(169,120,74,0.25); }
    .summary-icon {
        width:40px; height:40px; border-radius:10px;
        background:rgba(169,120,74,0.1); border:1px solid rgba(169,120,74,0.2);
        display:grid; place-items:center; color:#c9a961; flex-shrink:0;
    }
    .summary-icon.green { background:rgba(34,197,94,0.1); border-color:rgba(34,197,94,0.2); color:#22c55e; }
    .summary-icon.blue { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.2); color:#3b82f6; }
    .summary-icon.amber { background:rgba(245,158,11,0.1); border-color:rgba(245,158,11,0.2); color:#f59e0b; }
    .summary-value { font-size:18px; font-weight:800; color:var(--text-primary); letter-spacing:-0.02em; line-height:1; font-variant-numeric:tabular-nums; }
    .summary-label { font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.1em; font-weight:700; margin-top:3px; }

    /* ========== TABS ========== */
    .tabs {
        display:flex; gap:6px; margin-bottom:16px; padding:5px;
        background:rgba(34,34,44,0.55); backdrop-filter:blur(20px);
        border:1px solid rgba(255,255,255,0.06); border-radius:12px;
        overflow-x:auto;
    }
    .tab {
        display:inline-flex; align-items:center; gap:8px; padding:9px 16px;
        border-radius:8px; font-size:12.5px; font-weight:600;
        color:var(--text-secondary); transition:all 0.15s;
        white-space:nowrap; flex-shrink:0; text-decoration:none;
    }
    .tab:hover { background:rgba(255,255,255,0.04); color:var(--text-primary); }
    .tab.active {
        background:linear-gradient(135deg,rgba(169,120,74,0.25),rgba(169,120,74,0.15));
        color:#c9a961;
        box-shadow:inset 0 0 0 1px rgba(169,120,74,0.4);
    }
    .tab-count {
        padding:2px 8px; background:rgba(255,255,255,0.05); border-radius:10px;
        font-size:10.5px; font-weight:800; min-width:22px; text-align:center;
        color:var(--text-muted);
    }
    .tab.active .tab-count { background:rgba(169,120,74,0.3); color:#f0e6dc; }

    /* ========== TOOLBAR ========== */
    .toolbar { margin-bottom:16px; }
    .toolbar-form { display:flex; align-items:center; gap:8px; flex-wrap:nowrap; }
    .search-box { flex:1; min-width:0; max-width:420px; position:relative; display:flex; align-items:center; }
    .search-icon { position:absolute; left:12px; color:var(--text-muted); pointer-events:none; }
    .search-input {
        width:100%; padding:9px 14px 9px 36px;
        background:rgba(20,20,26,0.6); border:1px solid var(--border-strong);
        border-radius:8px; color:var(--text-primary); font-size:13px;
        font-family:inherit; outline:none; transition:all 0.15s;
    }
    .search-input::placeholder { color:var(--text-muted); }
    .search-input:focus { border-color:var(--accent); background:rgba(20,20,26,0.9); box-shadow:0 0 0 4px rgba(169,120,74,0.15); }
    .filter-select {
        padding:9px 32px 9px 12px;
        background:rgba(20,20,26,0.6); border:1px solid var(--border-strong);
        border-radius:8px; color:var(--text-primary);
        font-size:13px; font-family:inherit; outline:none; cursor:pointer;
        appearance:none; -webkit-appearance:none;
        background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat:no-repeat; background-position:right 10px center;
        min-width:180px; flex-shrink:0;
    }
    .filter-select:focus { border-color:var(--accent); box-shadow:0 0 0 4px rgba(169,120,74,0.15); }
    .btn-filter {
        display:inline-flex; align-items:center; justify-content:center; gap:6px;
        padding:9px 16px; background:linear-gradient(135deg,#a9784a,#8a5f36);
        color:#fff; border:none; border-radius:8px;
        font-size:13px; font-weight:600; font-family:inherit;
        cursor:pointer; flex-shrink:0;
        box-shadow:0 4px 10px -4px rgba(169,120,74,0.5);
    }
    .btn-clear {
        width:36px; height:36px; border-radius:8px;
        background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25);
        color:#ef4444; display:grid; place-items:center;
        cursor:pointer; flex-shrink:0; text-decoration:none;
    }
    .btn-clear:hover { background:rgba(239,68,68,0.2); }

    /* ========== MGMT TABLE ========== */
    .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .mgmt-table { width:100%; border-collapse:collapse; min-width:900px; }
    .mgmt-table thead th {
        padding:12px 14px; text-align:left;
        font-size:10.5px; font-weight:800; text-transform:uppercase;
        letter-spacing:0.08em; color:var(--text-muted);
        background:rgba(20,20,26,0.6);
        border-bottom:1px solid rgba(255,255,255,0.06);
        white-space:nowrap;
    }
    .mgmt-row {
        border-bottom:1px solid rgba(255,255,255,0.04);
        transition:background 0.15s;
    }
    .mgmt-row:hover { background:rgba(169,120,74,0.04); }
    .mgmt-table td { padding:12px 14px; font-size:12.5px; vertical-align:middle; }

    .expand-cell { text-align:center; padding:12px 6px !important; }
    .expand-btn {
        width:24px; height:24px; border-radius:6px;
        background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
        color:var(--text-muted); cursor:pointer;
        display:grid; place-items:center; transition:all 0.15s;
        padding:0;
    }
    .expand-btn:hover { background:rgba(169,120,74,0.15); color:#c9a961; border-color:rgba(169,120,74,0.3); }
    .expand-btn.open { transform:rotate(90deg); background:rgba(169,120,74,0.2); color:#c9a961; border-color:rgba(169,120,74,0.4); }

    .cell-primary { font-weight:600; color:var(--text-primary); }
    .cell-secondary { font-size:10.5px; color:var(--text-muted); margin-top:2px; font-family:ui-monospace,monospace; }

    .money { font-variant-numeric:tabular-nums; font-weight:700; font-family:ui-monospace,monospace; white-space:nowrap; }
    .money.green { color:#22c55e; }
    .money.amber { color:#f59e0b; }
    .money.strong { font-size:13px; }

    .items-badge {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:26px; height:26px; padding:0 8px;
        background:rgba(201,169,97,0.12); border:1px solid rgba(201,169,97,0.25);
        border-radius:8px; color:#c9a961; font-size:11.5px; font-weight:800;
        font-family:ui-monospace,monospace;
    }

    .badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:4px 9px; border-radius:6px;
        font-size:10.5px; font-weight:800;
        text-transform:uppercase; letter-spacing:0.03em;
        white-space:nowrap;
    }
    .badge-paid { background:rgba(34,197,94,0.15); color:#22c55e; border:1px solid rgba(34,197,94,0.3); }
    .badge-partial { background:rgba(59,130,246,0.15); color:#60a5fa; border:1px solid rgba(59,130,246,0.3); }
    .badge-pending { background:rgba(245,158,11,0.15); color:#f59e0b; border:1px solid rgba(245,158,11,0.3); }

    .action-group { display:flex; gap:6px; justify-content:flex-end; }
    .btn-icon {
        width:30px; height:30px; border-radius:8px;
        background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
        color:var(--text-secondary); display:grid; place-items:center;
        text-decoration:none; transition:all 0.15s;
    }
    .btn-icon:hover { background:rgba(255,255,255,0.08); color:var(--text-primary); }
    .btn-icon.accent { background:rgba(201,169,97,0.12); border-color:rgba(201,169,97,0.3); color:#c9a961; }
    .btn-icon.accent:hover { background:rgba(201,169,97,0.25); }

    /* ========== EXPANDED DETAILS ========== */
    .products-row td { padding:0 !important; background:rgba(15,15,20,0.4); }
    .products-panel {
        padding:18px;
        border-top:1px solid rgba(169,120,74,0.15);
        animation:slideDown 0.25s ease;
    }
    @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }

    .details-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .details-col {
        background:rgba(30,30,38,0.5);
        border:1px solid rgba(255,255,255,0.05);
        border-radius:12px;
        padding:14px;
    }
    .details-header {
        display:flex; justify-content:space-between; align-items:center;
        margin-bottom:10px; padding-bottom:10px;
        border-bottom:1px solid rgba(255,255,255,0.05);
    }
    .details-title {
        display:flex; align-items:center; gap:6px;
        font-size:12px; font-weight:800; color:#c9a961;
        letter-spacing:0.02em;
    }
    .details-title.green { color:#22c55e; }
    .details-count { font-size:10.5px; color:var(--text-muted); font-weight:600; }

    .mini-table { width:100%; border-collapse:collapse; }
    .mini-table th {
        padding:7px 8px; text-align:left;
        font-size:9.5px; font-weight:800; text-transform:uppercase;
        letter-spacing:0.08em; color:var(--text-muted);
        border-bottom:1px solid rgba(255,255,255,0.06);
    }
    .mini-table td {
        padding:8px; font-size:11.5px;
        border-bottom:1px solid rgba(255,255,255,0.03);
    }
    .mini-table tbody tr:last-child td { border-bottom:none; }
    .mini-table tbody tr:hover { background:rgba(255,255,255,0.02); }

    .mono { font-family:ui-monospace,monospace; color:var(--text-muted); font-size:11px; }

    .method-badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:3px 8px; border-radius:6px;
        font-size:10.5px; font-weight:700;
        white-space:nowrap;
    }
    .method-badge.cash { background:rgba(34,197,94,0.12); color:#22c55e; }
    .method-badge.gcash { background:rgba(59,130,246,0.12); color:#60a5fa; }
    .method-badge.maya { background:rgba(168,85,247,0.12); color:#a855f7; }
    .method-badge.bank_transfer { background:rgba(245,158,11,0.12); color:#f59e0b; }
    .method-badge.check { background:rgba(148,163,184,0.12); color:#94a3b8; }

    .details-empty {
        text-align:center; padding:24px 12px;
        color:var(--text-muted); font-size:11.5px;
    }

    /* ========== TOTALS ========== */
    .totals-row {
        background:linear-gradient(135deg,rgba(201,169,97,0.08),rgba(138,95,54,0.04));
        border-top:2px solid rgba(201,169,97,0.3);
    }
    .totals-row td { padding:14px !important; font-size:13px; color:var(--text-primary); }

    .pagination-wrap { margin-top:20px; display:flex; justify-content:center; }

    /* ========== EMPTY ========== */
    .empty { text-align:center; padding:60px 20px; }
    .empty-icon {
        width:64px; height:64px; margin:0 auto 16px;
        border-radius:16px; background:rgba(169,120,74,0.1);
        border:1px solid rgba(169,120,74,0.2);
        color:#c9a961; display:grid; place-items:center;
    }
    .empty-title { font-size:15px; font-weight:800; color:var(--text-primary); margin-bottom:6px; }
    .empty-text { font-size:12.5px; color:var(--text-muted); }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1100px) {
        .summary-grid { grid-template-columns:repeat(2,1fr); }
        .details-grid { grid-template-columns:1fr; }
    }
    @media (max-width: 900px) {
        .toolbar-form { flex-wrap:wrap; }
        .search-box { max-width:100%; flex:1 1 100%; }
        .filter-select { flex:1; min-width:auto; }
    }
    @media (max-width: 700px) {
        .summary-grid { grid-template-columns:1fr; }
        .toolbar-form { flex-direction:column; }
        .search-box, .filter-select, .btn-filter, .btn-clear { width:100%; }
        .filter-select { min-width:auto; }
        .products-panel { padding:14px 12px; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    console.log('[Reports] Script loaded');

    function toggleProducts(reportId) {
        var row = document.getElementById('products-' + reportId);
        var btn = document.querySelector('.expand-btn[data-report-id="' + reportId + '"]');

        if (!row) {
            console.warn('[Reports] Row not found:', 'products-' + reportId);
            return;
        }

        var isOpen = row.style.display === 'table-row';

        if (isOpen) {
            row.style.display = 'none';
            if (btn) btn.classList.remove('open');
        } else {
            row.style.display = 'table-row';
            if (btn) btn.classList.add('open');
        }
    }

    // Attach click handlers after DOM ready
    function attachHandlers() {
        var buttons = document.querySelectorAll('.expand-btn[data-report-id]');
        console.log('[Reports] Found ' + buttons.length + ' expand buttons');

        buttons.forEach(function(btn) {
            if (btn.dataset.handlerAttached === '1') return;

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var reportId = btn.dataset.reportId;
                console.log('[Reports] Toggling row:', reportId);
                toggleProducts(reportId);
            });

            btn.dataset.handlerAttached = '1';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachHandlers);
    } else {
        attachHandlers();
    }

    // Re-attach sa mga bag-ong elements (kung dynamic)
    window.addEventListener('pageshow', attachHandlers);

    // Global fallback
    window.toggleProducts = toggleProducts;
})();
</script>
@endpush