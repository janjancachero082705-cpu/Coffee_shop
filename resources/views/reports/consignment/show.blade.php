@extends('layouts.admin')

@section('title', $report->report_number ?? 'Sales Report')
@section('subtitle', 'Sales Report details')

@section('content')

@php
    $statusRaw = strtolower($report->status ?? 'pending');
    $statusMap = [
        'paid'    => ['label' => 'Paid',    'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.12)',  'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>'],
        'partial' => ['label' => 'Partial', 'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,0.12)', 'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>'],
        'pending' => ['label' => 'Pending', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.12)', 'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>'],
    ];
    $statusConfig = $statusMap[$statusRaw] ?? [
        'label' => ucfirst($statusRaw),
        'color' => '#71717a',
        'bg'    => 'rgba(113,113,122,0.12)',
        'icon'  => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
    ];

    $totalSales = (float) ($report->total_sales ?? 0);
    $amountPaid = (float) ($report->amount_paid ?? 0);
    $balance    = (float) ($report->balance ?? 0);
    $paidPct    = $totalSales > 0 ? min(100, ($amountPaid / $totalSales) * 100) : 0;

    $items    = $report->items ?? ($report->salesReportItems ?? collect());
    $payments = $report->payments ?? collect();
    $qtySold  = $items->sum('quantity_sold') ?? 0;

    $periodStart = $report->period_start ?? $report->start_date ?? null;
    $periodEnd   = $report->period_end   ?? $report->end_date   ?? null;
@endphp

{{-- ========== HERO ========== --}}
<div class="sr-hero" style="--status-color: {{ $statusConfig['color'] }};">
    <div class="sr-hero-icon" style="background: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }};">
        {!! $statusConfig['icon'] !!}
    </div>

    <div class="sr-hero-content">
        <div class="sr-hero-label">Sales Report</div>
        <div class="sr-hero-title">{{ $report->report_number }}</div>
        <div class="sr-hero-meta">
            <a href="{{ route('stores.show', $report->store_id) }}" class="sr-hero-link">
                {{ $report->store->store_name ?? '-' }}
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            </a>
            @if($report->store->code ?? null)
                <span class="sr-hero-dot">·</span>
                <span class="sr-hero-code">{{ $report->store->code }}</span>
            @endif
            <span class="sr-hero-dot">·</span>
            <span class="sr-hero-status" style="color: {{ $statusConfig['color'] }}; background: {{ $statusConfig['bg'] }};">
                <span class="sr-status-dot" style="background: {{ $statusConfig['color'] }};"></span>
                {{ $statusConfig['label'] }}
            </span>
        </div>
        @if($periodStart && $periodEnd)
            <div class="sr-hero-period">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ \Carbon\Carbon::parse($periodStart)->format('M d, Y') }}
                <span class="sr-hero-period-sep">→</span>
                {{ \Carbon\Carbon::parse($periodEnd)->format('M d, Y') }}
            </div>
        @endif
    </div>

    <div class="sr-hero-stats">
        <div class="sr-hero-stat">
            <div class="sr-hero-stat-label">Total Sales</div>
            <div class="sr-hero-stat-value">&#8369;{{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="sr-hero-stat-divider"></div>
        <div class="sr-hero-stat">
            <div class="sr-hero-stat-label">Qty Sold</div>
            <div class="sr-hero-stat-value">{{ $qtySold }}</div>
        </div>
    </div>
</div>

{{-- ========== GRID ========== --}}
<div class="sr-grid">

    {{-- LEFT: Products + Payments --}}
    <div>
        {{-- PRODUCTS SOLD --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="sr-card-title">Products Sold</div>
                    <div class="sr-card-sub">{{ $items->count() }} product(s)</div>
                </div>
            </div>

            @if($items->count() > 0)
                <div class="sr-table">
                    <div class="sr-table-head">
                        <div>Product</div>
                        <div class="ta-r">Qty</div>
                        <div class="ta-r">Unit Price</div>
                        <div class="ta-r">Subtotal</div>
                    </div>
                    @foreach($items as $item)
                        <div class="sr-table-row">
                            <div class="sr-prod">
                                <div class="sr-prod-name">{{ $item->product->name ?? '-' }}</div>
                                <div class="sr-prod-sku">{{ $item->product->sku ?? '' }}</div>
                            </div>
                            <div class="ta-r sr-num">{{ $item->quantity_sold ?? 0 }}</div>
                            <div class="ta-r sr-num">&#8369;{{ number_format($item->unit_price ?? 0, 2) }}</div>
                            <div class="ta-r sr-num sr-bold">&#8369;{{ number_format($item->subtotal ?? (($item->quantity_sold ?? 0) * ($item->unit_price ?? 0)), 2) }}</div>
                        </div>
                    @endforeach
                    <div class="sr-table-total">
                        <div class="sr-total-label">Total</div>
                        <div class="sr-total-value">&#8369;{{ number_format($totalSales, 2) }}</div>
                    </div>
                </div>
            @else
                <div class="sr-empty">Walay products sold.</div>
            @endif
        </div>

        {{-- PAYMENT HISTORY --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon green">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div class="sr-card-title">Payment History</div>
                    <div class="sr-card-sub">{{ $payments->count() }} payment(s) received</div>
                </div>
                @if($balance > 0)
                    <a href="{{ route('consignment.payments.create') }}?store_id={{ $report->store_id }}&sales_report_id={{ $report->id }}"
                       class="sr-add-btn">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        Add Payment
                    </a>
                @endif
            </div>

            @if($payments->count() > 0)
                <div class="sr-payments">
                    @foreach($payments as $p)
                        @php
                            $method = $p->method ?? 'cash';
                            $methodColor = match($method) {
                                'cash' => '#22c55e',
                                'gcash' => '#3b82f6',
                                'maya' => '#a855f7',
                                'bank_transfer' => '#f59e0b',
                                default => '#c9a961',
                            };
                        @endphp
                        <div class="sr-payment-item">
                            <div class="sr-payment-icon" style="background: {{ $methodColor }}1f; color: {{ $methodColor }};">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="6" width="20" height="12" rx="2"/>
                                    <circle cx="12" cy="12" r="2"/>
                                </svg>
                            </div>
                            <div class="sr-payment-info">
                                <div class="sr-payment-num">{{ $p->payment_number ?? '-' }}</div>
                                <div class="sr-payment-meta">
                                    {{ \Carbon\Carbon::parse($p->payment_date ?? $p->created_at)->format('M d, Y') }}
                                    · {{ ucfirst(str_replace('_', ' ', $method)) }}
                                </div>
                            </div>
                            <div class="sr-payment-amount">+ &#8369;{{ number_format($p->amount ?? 0, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sr-empty">Walay payments pa.</div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Summary + Details + Actions --}}
    <div>
        {{-- SUMMARY --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3v18h18"/>
                        <path d="M7 14l4-4 4 4 5-5"/>
                    </svg>
                </div>
                <div class="sr-card-title">Summary</div>
            </div>

            <div class="sr-summary-rows">
                <div class="sr-summary-row">
                    <span class="sr-summary-label">Total Sales</span>
                    <span class="sr-summary-value">&#8369;{{ number_format($totalSales, 2) }}</span>
                </div>
                <div class="sr-summary-row">
                    <span class="sr-summary-label">Amount Paid</span>
                    <span class="sr-summary-value green">- &#8369;{{ number_format($amountPaid, 2) }}</span>
                </div>
                <div class="sr-summary-row sr-summary-row-total">
                    <span class="sr-summary-label">Unpaid Balance</span>
                    <span class="sr-summary-value {{ $balance > 0 ? 'amber' : 'green' }}">
                        &#8369;{{ number_format($balance, 2) }}
                    </span>
                </div>
            </div>

            <div class="sr-progress">
                <div class="sr-progress-info">
                    <span class="sr-progress-pct">{{ number_format($paidPct, 0) }}% paid</span>
                    <span class="sr-progress-count">{{ $payments->count() }} payment(s)</span>
                </div>
                <div class="sr-progress-track">
                    <div class="sr-progress-fill" style="width: {{ $paidPct }}%;"></div>
                </div>
            </div>

            @if($balance > 0)
                <a href="{{ route('consignment.payments.create') }}?store_id={{ $report->store_id }}&sales_report_id={{ $report->id }}"
                   class="sr-btn sr-btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Record Payment
                </a>
            @else
                <div class="sr-paid-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    Fully Paid
                </div>
            @endif
        </div>

        {{-- DETAILS --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                </div>
                <div class="sr-card-title">Details</div>
            </div>

            <div class="sr-detail-rows">
                <div class="sr-detail-row">
                    <span class="sr-detail-label">Created by</span>
                    <span class="sr-detail-value">{{ $report->user->name ?? '-' }}</span>
                </div>
                <div class="sr-detail-row">
                    <span class="sr-detail-label">Created at</span>
                    <span class="sr-detail-value">{{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y · g:i A') }}</span>
                </div>
                @if($periodStart && $periodEnd)
                    <div class="sr-detail-row">
                        <span class="sr-detail-label">Period</span>
                        <span class="sr-detail-value">{{ \Carbon\Carbon::parse($periodStart)->format('M d, Y') }} → {{ \Carbon\Carbon::parse($periodEnd)->format('M d, Y') }}</span>
                    </div>
                @endif
                <div class="sr-detail-row">
                    <span class="sr-detail-label">Last updated</span>
                    <span class="sr-detail-value">{{ $report->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* ============================================================
       SALES REPORT SHOW — PROFESSIONAL REDESIGN
       ============================================================ */

    /* ========== HERO ========== */
    .sr-hero {
        position: relative;
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 22px 26px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, rgba(30, 26, 22, 0.85) 0%, rgba(21, 18, 15, 0.9) 100%);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid var(--status-color);
        border-radius: 18px;
        overflow: hidden;
    }
    .sr-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, var(--status-color), transparent 70%);
        opacity: 0.12;
        pointer-events: none;
    }
    .sr-hero-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: grid; place-items: center;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative; z-index: 1;
    }
    .sr-hero-icon svg { width: 26px; height: 26px; }

    .sr-hero-content { flex: 1; min-width: 0; position: relative; z-index: 1; }
    .sr-hero-label {
        font-size: 10px; font-weight: 800;
        color: var(--status-color);
        text-transform: uppercase; letter-spacing: 0.12em;
        margin-bottom: 6px;
    }
    .sr-hero-title {
        font-size: 22px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.02em;
        font-family: ui-monospace, monospace;
        line-height: 1.1; margin-bottom: 10px;
    }
    .sr-hero-meta {
        display: flex; align-items: center; gap: 8px;
        flex-wrap: wrap; font-size: 12px;
    }
    .sr-hero-link {
        display: inline-flex; align-items: center; gap: 4px;
        color: #c9a961; text-decoration: none; font-weight: 700;
        transition: color 0.15s;
    }
    .sr-hero-link:hover { color: #d4b673; }
    .sr-hero-link svg { opacity: 0.6; }
    .sr-hero-dot { color: #52525b; }
    .sr-hero-code {
        font-family: ui-monospace, monospace;
        font-size: 11px; color: #71717a;
    }
    .sr-hero-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px; border-radius: 100px;
        font-size: 10.5px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .sr-status-dot {
        width: 6px; height: 6px; border-radius: 50%;
        flex-shrink: 0;
    }
    .sr-hero-period {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 10px; padding: 5px 11px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 100px;
        font-size: 11px; font-weight: 600; color: #a1a1aa;
        font-family: ui-monospace, monospace;
    }
    .sr-hero-period svg { color: #71717a; }
    .sr-hero-period-sep { color: #52525b; }

    .sr-hero-stats {
        display: flex; align-items: center; gap: 16px;
        position: relative; z-index: 1; flex-shrink: 0;
        padding-left: 20px;
        border-left: 1px solid rgba(255, 255, 255, 0.06);
    }
    .sr-hero-stat { text-align: right; }
    .sr-hero-stat-label {
        font-size: 9.5px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.1em;
        margin-bottom: 5px;
    }
    .sr-hero-stat-value {
        font-size: 18px; font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .sr-hero-stat-divider {
        width: 1px; height: 32px;
        background: rgba(255, 255, 255, 0.06);
    }

    /* ========== GRID ========== */
    .sr-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 16px;
    }

    /* ========== CARD ========== */
    .sr-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
    }
    .sr-card:last-child { margin-bottom: 0; }

    .sr-card-head {
        display: flex; align-items: center; gap: 12px;
        padding-bottom: 12px; margin-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .sr-card-icon {
        width: 34px; height: 34px; border-radius: 10px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .sr-card-icon.green {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
    }
    .sr-card-title {
        font-size: 13.5px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.01em;
    }
    .sr-card-sub {
        font-size: 10.5px; color: #71717a; margin-top: 2px;
    }

    /* ========== TABLE ========== */
    .sr-table { display: flex; flex-direction: column; }
    .sr-table-head {
        display: grid;
        grid-template-columns: 1fr 50px 90px 100px;
        gap: 12px; padding: 0 0 10px;
        font-size: 9.5px; font-weight: 800;
        color: #71717a; text-transform: uppercase;
        letter-spacing: 0.08em;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .sr-table-row {
        display: grid;
        grid-template-columns: 1fr 50px 90px 100px;
        gap: 12px; padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        align-items: center;
    }
    .sr-table-row:last-of-type { border-bottom: none; }
    .ta-r { text-align: right; }

    .sr-prod-name {
        font-size: 12.5px; font-weight: 700; color: #fafafa;
        margin-bottom: 2px;
    }
    .sr-prod-sku {
        font-size: 10px; color: #71717a;
        font-family: ui-monospace, monospace;
    }
    .sr-num {
        font-size: 12.5px; font-weight: 700; color: #d4d4d8;
        font-variant-numeric: tabular-nums;
    }
    .sr-bold { color: #c9a961; font-weight: 800; }

    .sr-table-total {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 14px;
        border-top: 2px solid rgba(201, 169, 97, 0.25);
    }
    .sr-total-label {
        font-size: 12.5px; font-weight: 800;
        color: #71717a; text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .sr-total-value {
        font-size: 20px; font-weight: 800; color: #c9a961;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    /* ========== PAYMENTS ========== */
    .sr-add-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px;
        background: rgba(201, 169, 97, 0.12);
        border: 1px solid rgba(201, 169, 97, 0.28);
        border-radius: 8px;
        color: #c9a961; text-decoration: none;
        font-size: 11px; font-weight: 800;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .sr-add-btn:hover {
        background: rgba(201, 169, 97, 0.2);
        border-color: rgba(201, 169, 97, 0.5);
    }

    .sr-payments { display: flex; flex-direction: column; gap: 8px; }
    .sr-payment-item {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 12px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        transition: all 0.15s;
    }
    .sr-payment-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .sr-payment-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .sr-payment-info { flex: 1; min-width: 0; }
    .sr-payment-num {
        font-size: 12px; font-weight: 800;
        color: #fafafa;
        font-family: ui-monospace, monospace;
        margin-bottom: 2px;
    }
    .sr-payment-meta {
        font-size: 10.5px; color: #71717a;
    }
    .sr-payment-amount {
        font-size: 13px; font-weight: 800; color: #22c55e;
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }

    /* ========== SUMMARY ========== */
    .sr-summary-rows { display: flex; flex-direction: column; }
    .sr-summary-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; font-size: 12.5px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .sr-summary-row:last-child { border-bottom: none; }
    .sr-summary-label { color: #71717a; font-weight: 600; }
    .sr-summary-value {
        font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
    }
    .sr-summary-value.green { color: #22c55e; }
    .sr-summary-value.amber { color: #f59e0b; }
    .sr-summary-row-total {
        margin-top: 6px; padding-top: 14px;
        border-top: 1px solid rgba(201, 169, 97, 0.25) !important;
    }
    .sr-summary-row-total .sr-summary-value { font-size: 16px; }

    /* ========== PROGRESS ========== */
    .sr-progress { margin-top: 16px; }
    .sr-progress-info {
        display: flex; justify-content: space-between;
        font-size: 10px; font-weight: 700; color: #71717a;
        margin-bottom: 6px;
    }
    .sr-progress-pct { color: #c9a961; }
    .sr-progress-track {
        height: 8px; border-radius: 4px;
        background: rgba(255, 255, 255, 0.05);
        overflow: hidden;
    }
    .sr-progress-fill {
        height: 100%; border-radius: 4px;
        background: linear-gradient(90deg, #c9a961, #d4b673);
        transition: width 0.4s;
        min-width: 4px;
    }

    /* ========== BUTTONS ========== */
    .sr-btn {
        display: flex; align-items: center; justify-content: center;
        gap: 7px; width: 100%; padding: 11px 16px;
        margin-top: 14px;
        border-radius: 10px;
        font-size: 12.5px; font-weight: 800;
        text-decoration: none; transition: all 0.15s;
        border: 1px solid; cursor: pointer; font-family: inherit;
    }
    .sr-btn-primary {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-color: transparent; color: #0f0f14;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }
    .sr-btn-primary:hover {
        background: linear-gradient(135deg, #d4b672, #c9a961);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px -4px rgba(201, 169, 97, 0.6);
    }
    .sr-paid-badge {
        display: flex; align-items: center; justify-content: center;
        gap: 8px; padding: 12px; margin-top: 14px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.25);
        border-radius: 10px; color: #22c55e;
        font-weight: 800; font-size: 12.5px;
        text-transform: uppercase; letter-spacing: 0.05em;
    }

    /* ========== DETAILS ========== */
    .sr-detail-rows { display: flex; flex-direction: column; }
    .sr-detail-row {
        display: flex; justify-content: space-between; align-items: center;
        gap: 12px; padding: 10px 0;
        font-size: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .sr-detail-row:last-child { border-bottom: none; }
    .sr-detail-label { color: #71717a; font-weight: 600; }
    .sr-detail-value {
        color: #fafafa; font-weight: 700; text-align: right;
    }

    /* ========== EMPTY ========== */
    .sr-empty {
        text-align: center; padding: 24px 16px;
        color: #71717a; font-size: 12px;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1100px) {
        .sr-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .sr-hero {
            flex-direction: column; align-items: flex-start;
            padding: 18px 20px;
        }
        .sr-hero-stats {
            padding-left: 0; padding-top: 16px;
            border-left: none;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            width: 100%; justify-content: space-between; gap: 10px;
        }
        .sr-hero-stat { text-align: left; }
        .sr-hero-title { font-size: 18px; }
        .sr-table-head,
        .sr-table-row {
            grid-template-columns: 1fr 40px 70px 80px;
            gap: 8px;
        }
        .sr-table-head { font-size: 8.5px; }
        .sr-num, .sr-prod-name { font-size: 11px; }
    }
</style>
@endpush