@extends('layouts.admin')

@section('title', 'Payments')
@section('subtitle', 'Consignment payments from stores')

@section('actions')
    <a href="{{ route('consignment.payments.create') }}" class="btn btn-primary btn-sm">+ Record Payment</a>
@endsection

@section('content')

<div class="summary-grid">
    <div class="summary">
        <div class="summary-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['total'] }}</div>
            <div class="summary-label">Total Payments</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">&#8369;{{ number_format($stats['all_amount']/1000, 1) }}k</div>
            <div class="summary-label">Total Collected</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">&#8369;{{ number_format($stats['this_month']/1000, 1) }}k</div>
            <div class="summary-label">This Month</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">&#8369;{{ number_format($stats['today'], 0) }}</div>
            <div class="summary-label">Today</div>
        </div>
    </div>
</div>

<div class="tabs">
    @php
        $tabs = [
            'all'      => ['label' => 'All Payments'],
            'pending'  => ['label' => 'Pending'],
            'partial'  => ['label' => 'Partial'],
            'full'     => ['label' => 'Fully Paid'],
            'unlinked' => ['label' => 'Unlinked'],
        ];
    @endphp

    @foreach($tabs as $key => $tab)
        <a href="{{ route('consignment.payments.index', array_merge(request()->only(['search','store']), ['payment_status' => $key])) }}"
           class="tab {{ $paymentStatus === $key ? 'active' : '' }}">
            <span>{{ $tab['label'] }}</span>
            <span class="tab-count">{{ $tabCounts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<div class="toolbar">
    <form method="GET" class="toolbar-form">
        <input type="hidden" name="payment_status" value="{{ $paymentStatus }}">

        <div class="search-box">
            <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payment # or reference..." class="search-input">
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
            <a href="{{ route('consignment.payments.index', ['payment_status' => $paymentStatus]) }}" class="btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif
    </form>
</div>

@if($paymentStatus === 'pending')
    @if($pendings->isEmpty())
        <div class="card">
            <div class="empty">
                <div class="empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div class="empty-title">No pending deliveries</div>
                <div class="empty-text">All delivery receipts have been paid</div>
            </div>
        </div>
    @else
        <div class="pending-summary">
            <div class="pending-summary-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="pending-summary-text">
                <div class="pending-summary-title">Pending Delivery Receipts</div>
                <div class="pending-summary-sub">Not yet paid - click to record payment</div>
            </div>
            <div class="pending-summary-total">
                <div class="pending-summary-label">Total Unpaid</div>
                <div class="pending-summary-value">&#8369;{{ number_format($pendings->sum('balance'), 2) }}</div>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>DR Number</th>
                        <th>Store</th>
                        <th>Delivery Date</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:right;">Balance</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendings as $dr)
                        <tr style="cursor:pointer;" onclick="window.location='{{ route('deliveries.show', $dr) }}'">
                            <td>
                                <span style="color:#c9a961; font-family:ui-monospace; font-weight:700; font-size:12px;">{{ $dr->dr_number }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary);">{{ $dr->store->store_name ?? '-' }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $dr->store->code ?? '' }}</div>
                            </td>
                            <td>
                                <div style="font-weight:500; font-size:12px;">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                            </td>
                            <td style="text-align:right; font-weight:600; color:var(--text-primary); font-size:13px;">
                                &#8369;{{ number_format($dr->total_amount, 2) }}
                            </td>
                            <td style="text-align:right; font-weight:800; color:#f59e0b; font-size:14px;">
                                &#8369;{{ number_format($dr->balance, 2) }}
                            </td>
                            <td>
                                <span class="badge badge-pending">Pending</span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('consignment.payments.create') }}?store_id={{ $dr->store_id }}&delivery_receipt_id={{ $dr->id }}"
                                   class="btn btn-primary btn-sm"
                                   onclick="event.stopPropagation();">
                                    + Pay
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($pendings, 'links'))
            <div class="pagination-wrap">{{ $pendings->links() }}</div>
        @endif
    @endif
@else
    @if($payments->isEmpty())
        <div class="card">
            <div class="empty">
                <div class="empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
                <div class="empty-title">No payments found</div>
                <div class="empty-text">
                    @if(request()->hasAny(['search','store']) || $paymentStatus !== 'all')
                        Try adjusting your filters or tab
                    @else
                        Record your first payment to get started
                    @endif
                </div>
                @if(!request()->hasAny(['search','store']) && $paymentStatus === 'all')
                    <a href="{{ route('consignment.payments.create') }}" class="btn btn-primary">+ Record Payment</a>
                @endif
            </div>
        </div>
    @else
        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Store</th>
                        <th>Date</th>
                        <th>Linked To</th>
                        <th>Method</th>
                        <th style="text-align:right;">Amount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $p)
                        @php
                            $linkedType = '-';
                            $linkedNumber = null;
                            $linkedStatus = null;

                            if ($p->deliveryReceipt) {
                                $linkedType = 'Delivery';
                                $linkedNumber = $p->deliveryReceipt->dr_number;
                                $linkedStatus = $p->deliveryReceipt->status;
                            } elseif ($p->salesReport) {
                                $linkedType = 'Report';
                                $linkedNumber = $p->salesReport->report_number;
                                $linkedStatus = $p->salesReport->status;
                            }
                        @endphp
                        <tr style="cursor:pointer;" onclick="window.location='{{ route('consignment.payments.show', $p) }}'">
                            <td>
                                <span style="color:#c9a961; font-family:ui-monospace; font-weight:700; font-size:12px;">{{ $p->payment_number }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary);">{{ $p->store->store_name ?? '-' }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $p->store->code ?? '' }}</div>
                            </td>
                            <td>
                                <div style="font-weight:500; font-size:12px;">{{ $p->payment_date }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $p->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($linkedNumber)
                                    <div style="font-family:ui-monospace; font-size:11px; font-weight:600; color:#60a5fa;">{{ $linkedNumber }}</div>
                                    <div style="font-size:10px; color:var(--text-muted);">{{ $linkedType }}</div>
                                @else
                                    <span style="color:var(--text-muted); font-size:12px;">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="method-badge method-{{ $p->method }}">
                                    {{ ucfirst(str_replace('_',' ',$p->method)) }}
                                </span>
                            </td>
                            <td style="text-align:right; font-weight:700; color:#22c55e; font-size:14px;">
                                + &#8369;{{ number_format($p->amount, 2) }}
                            </td>
                            <td>
                                @if($linkedStatus)
                                    <span class="badge badge-{{ $linkedStatus }}">{{ ucfirst($linkedStatus) }}</span>
                                @else
                                    <span class="badge" style="background:rgba(107,104,98,0.15); color:var(--text-muted);">Unlinked</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('consignment.payments.show', $p) }}" class="btn btn-ghost btn-sm" onclick="event.stopPropagation();">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($payments, 'links'))
            <div class="pagination-wrap">{{ $payments->links() }}</div>
        @endif
    @endif
@endif

@endsection

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .summary {
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
    }
    .summary:hover {
        transform: translateY(-2px);
        border-color: rgba(169, 120, 74, 0.25);
    }
    .summary-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid; place-items: center;
        color: #c9a961; flex-shrink: 0;
    }
    .summary-icon.green { background: rgba(34, 197, 94, 0.1);  border-color: rgba(34, 197, 94, 0.2);  color: #22c55e; }
    .summary-icon.blue  { background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .summary-icon.amber { background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .summary-value {
        font-size: 20px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em; line-height: 1;
    }
    .summary-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-top: 3px;
    }

    .tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
        padding: 5px;
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        overflow-x: auto;
    }
    .tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.15s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tab:hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
    }
    .tab.active {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.25), rgba(169, 120, 74, 0.15));
        color: #c9a961;
        box-shadow: inset 0 0 0 1px rgba(169, 120, 74, 0.4);
    }
    .tab-count {
        padding: 2px 8px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 800;
        min-width: 22px;
        text-align: center;
        color: var(--text-muted);
    }
    .tab.active .tab-count {
        background: rgba(169, 120, 74, 0.3);
        color: #f0e6dc;
    }

    .toolbar { margin-bottom: 16px; }
    .toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
    }
    .search-box {
        flex: 1; min-width: 0; max-width: 380px;
        position: relative;
        display: flex; align-items: center;
    }
    .search-icon { position: absolute; left: 12px; color: var(--text-muted); pointer-events: none; }
    .search-input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 13px; font-family: inherit; outline: none; transition: all 0.15s;
    }
    .search-input::placeholder { color: var(--text-muted); }
    .search-input:focus {
        border-color: var(--accent);
        background: rgba(20, 20, 26, 0.9);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }

    .filter-select {
        padding: 9px 32px 9px 12px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 13px; font-family: inherit;
        outline: none; cursor: pointer; transition: all 0.15s;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        width: 170px; flex-shrink: 0;
    }
    .filter-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }

    .btn-filter {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 9px 16px;
        background: linear-gradient(135deg, #a9784a, #8a5f36);
        color: #fff; border: none; border-radius: 8px;
        font-size: 13px; font-weight: 600; font-family: inherit;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
        box-shadow: 0 4px 10px -4px rgba(169, 120, 74, 0.5);
    }
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.7);
    }
    .btn-clear {
        width: 36px; height: 36px;
        border-radius: 8px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        display: grid; place-items: center;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
    }
    .btn-clear:hover { background: rgba(239, 68, 68, 0.2); }

    .pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }

    .method-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 6px;
        font-size: 11px; font-weight: 700;
    }
    .method-cash          { background: rgba(34, 197, 94, 0.1);  color: #22c55e; }
    .method-gcash         { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .method-maya          { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .method-bank_transfer { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .method-check         { background: rgba(169, 120, 74, 0.15); color: #c9a961; }

    .pending-summary {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(34, 34, 44, 0.6));
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 12px;
        margin-bottom: 14px;
    }
    .pending-summary-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #f59e0b;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .pending-summary-text { flex: 1; min-width: 0; }
    .pending-summary-title {
        font-size: 14px; font-weight: 700;
        color: var(--text-primary);
    }
    .pending-summary-sub {
        font-size: 11px; color: var(--text-muted);
        margin-top: 2px;
    }
    .pending-summary-total { text-align: right; }
    .pending-summary-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-bottom: 4px;
    }
    .pending-summary-value {
        font-size: 20px; font-weight: 800;
        color: #f59e0b;
        letter-spacing: -0.02em;
    }

    @media (max-width: 1100px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 900px) {
        .toolbar-form { flex-wrap: wrap; }
        .search-box { max-width: 100%; flex: 1 1 100%; }
        .filter-select { flex: 1; width: auto; }
    }
    @media (max-width: 700px)  { .summary-grid { grid-template-columns: 1fr; } }
</style>
@endpush