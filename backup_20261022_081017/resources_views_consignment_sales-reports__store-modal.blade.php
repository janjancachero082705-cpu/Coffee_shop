@php
    $totalSales = (float) $reports->sum('total_sales');
    $totalPaid  = (float) $reports->sum('amount_paid');
    $totalBal   = (float) $reports->sum('balance');
    $paidPct    = $totalSales > 0 ? min(100, ($totalPaid / $totalSales) * 100) : 0;
@endphp

{{-- ===== STORE HEADER ===== --}}
<div class="sr-store-head">
    <div class="sr-store-avatar">
        {{ strtoupper(substr($store->store_name, 0, 2)) }}
    </div>
    <div class="sr-store-info">
        <div class="sr-store-name">{{ $store->store_name }}</div>
        <div class="sr-store-meta">
            <span class="sr-store-code">{{ $store->code }}</span>
            <span class="sr-store-dot">·</span>
            <span>{{ $reports->count() }} report(s)</span>
        </div>
    </div>
</div>

{{-- ===== SUMMARY ===== --}}
<div class="sr-store-summary">
    <div class="sr-store-summary-row">
        <span class="sr-store-summary-lbl">Total Sales</span>
        <span class="sr-store-summary-val">&#8369;{{ number_format($totalSales, 2) }}</span>
    </div>
    <div class="sr-store-summary-row">
        <span class="sr-store-summary-lbl">Collected</span>
        <span class="sr-store-summary-val green">&#8369;{{ number_format($totalPaid, 2) }}</span>
    </div>
    <div class="sr-store-summary-row">
        <span class="sr-store-summary-lbl">Balance</span>
        <span class="sr-store-summary-val {{ $totalBal > 0 ? 'amber' : 'green' }}">&#8369;{{ number_format($totalBal, 2) }}</span>
    </div>
    <div class="sr-store-progress">
        <div class="sr-store-progress-track">
            <div class="sr-store-progress-fill" style="width: {{ $paidPct }}%;"></div>
        </div>
        <div class="sr-store-progress-info">
            <span>{{ number_format($paidPct, 0) }}% paid</span>
        </div>
    </div>
</div>

{{-- ===== REPORT LIST ===== --}}
<div class="sr-store-list">
    <div class="sr-store-list-head">Report History</div>

    @forelse($reports as $report)
        @php
            $total   = (float) ($report->total_sales ?? 0);
            $paid    = (float) ($report->amount_paid ?? 0);
            $balance = (float) ($report->balance ?? 0);
            $isPaid    = $balance <= 0;
            $isPartial = $paid > 0 && $balance > 0;
            $status    = $isPaid ? 'paid' : ($isPartial ? 'partial' : 'pending');
        @endphp

        <a href="{{ route('sales-reports.modal', $report->id) }}"
           data-report-modal="{{ route('sales-reports.modal', $report->id) }}"
           data-report-title="{{ $store->store_name }} — {{ $report->report_number }}"
           class="sr-store-report">
            <div class="sr-store-report-badge sr-badge-{{ $status }}">
                <span class="sr-badge-dot"></span>
                {{ ucfirst($status) }}
            </div>

            <div class="sr-store-report-info">
                <div class="sr-store-report-num">{{ $report->report_number }}</div>
                <div class="sr-store-report-date">
                    {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y · g:i A') }}
                </div>
            </div>

            <div class="sr-store-report-metrics">
                <div class="sr-store-report-metric">
                    <div class="sr-store-report-metric-lbl">Total</div>
                    <div class="sr-store-report-metric-val">&#8369;{{ number_format($total, 2) }}</div>
                </div>
                <div class="sr-store-report-metric">
                    <div class="sr-store-report-metric-lbl">Paid</div>
                    <div class="sr-store-report-metric-val green">&#8369;{{ number_format($paid, 2) }}</div>
                </div>
                <div class="sr-store-report-metric">
                    <div class="sr-store-report-metric-lbl">Balance</div>
                    <div class="sr-store-report-metric-val {{ $balance > 0 ? 'amber' : 'green' }}">&#8369;{{ number_format($balance, 2) }}</div>
                </div>
            </div>

            <svg class="sr-store-report-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </a>
    @empty
        <div class="sr-empty">Walay reports pa.</div>
    @endforelse
</div>

<style>
    .sr-store-head {
        display: flex; align-items: center; gap: 14px;
        padding-bottom: 16px; margin-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .sr-store-avatar {
        width: 48px; height: 48px; border-radius: 14px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.25), rgba(201, 169, 97, 0.1));
        color: #c9a961;
        display: grid; place-items: center;
        font-weight: 800; font-size: 16px;
        letter-spacing: 0.05em;
        border: 1px solid rgba(201, 169, 97, 0.3);
    }
    .sr-store-name {
        font-size: 17px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.01em;
        margin-bottom: 3px;
    }
    .sr-store-meta {
        display: flex; align-items: center; gap: 6px;
        font-size: 11.5px; color: #71717a;
    }
    .sr-store-code {
        font-family: ui-monospace, monospace;
        color: #a1a1aa;
    }
    .sr-store-dot { color: #52525b; }

    .sr-store-summary {
        padding: 14px 16px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        margin-bottom: 18px;
    }
    .sr-store-summary-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0; font-size: 12.5px;
    }
    .sr-store-summary-lbl { color: #71717a; font-weight: 600; }
    .sr-store-summary-val {
        color: #fafafa; font-weight: 800;
        font-variant-numeric: tabular-nums;
    }
    .sr-store-summary-val.green { color: #22c55e; }
    .sr-store-summary-val.amber { color: #f59e0b; }
    .sr-store-progress { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255, 255, 255, 0.04); }
    .sr-store-progress-track {
        height: 7px; border-radius: 4px;
        background: rgba(255, 255, 255, 0.05);
        overflow: hidden;
        margin-bottom: 6px;
    }
    .sr-store-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #c9a961, #d4b673);
        border-radius: 4px;
        min-width: 4px;
    }
    .sr-store-progress-info {
        text-align: right;
        font-size: 10.5px; font-weight: 800;
        color: #c9a961;
    }

    .sr-store-list-head {
        font-size: 10px; font-weight: 800;
        color: #71717a; text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 10px;
    }
    .sr-store-report {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 11px;
        margin-bottom: 8px;
        text-decoration: none;
        transition: all 0.15s;
        cursor: pointer;
    }
    .sr-store-report:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(201, 169, 97, 0.3);
        transform: translateX(2px);
    }
    .sr-store-report-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 100px;
        font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.05em;
        flex-shrink: 0;
    }
    .sr-badge-paid    { background: rgba(34, 197, 94, 0.12);  color: #22c55e; }
    .sr-badge-partial { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .sr-badge-pending { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .sr-badge-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: currentColor;
    }
    .sr-store-report-info { flex: 1; min-width: 0; }
    .sr-store-report-num {
        font-size: 12px; font-weight: 800; color: #fafafa;
        font-family: ui-monospace, monospace;
        margin-bottom: 2px;
    }
    .sr-store-report-date { font-size: 10.5px; color: #71717a; }
    .sr-store-report-metrics {
        display: flex; gap: 16px; flex-shrink: 0;
    }
    .sr-store-report-metric { text-align: right; }
    .sr-store-report-metric-lbl {
        font-size: 9px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.06em;
        margin-bottom: 2px;
    }
    .sr-store-report-metric-val {
        font-size: 11.5px; font-weight: 800;
        color: #fafafa; font-variant-numeric: tabular-nums;
    }
    .sr-store-report-metric-val.green { color: #22c55e; }
    .sr-store-report-metric-val.amber { color: #f59e0b; }
    .sr-store-report-arrow {
        color: #52525b; flex-shrink: 0;
        transition: all 0.15s;
    }
    .sr-store-report:hover .sr-store-report-arrow {
        color: #c9a961;
        transform: translateX(2px);
    }

    @media (max-width: 700px) {
        .sr-store-report-metrics { display: none; }
    }
</style>