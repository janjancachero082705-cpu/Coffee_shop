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

    $paymentUrl = route('consignment.payments.create') . '?store_id=' . $report->store_id . '&sales_report_id=' . $report->id;
@endphp

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

<div class="sr-grid">
    <div>
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
                    <a href="{{ $paymentUrl }}" data-modal-close class="sr-add-btn">
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

    <div>
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
                <a href="{{ $paymentUrl }}" data-modal-close class="sr-btn sr-btn-primary">
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