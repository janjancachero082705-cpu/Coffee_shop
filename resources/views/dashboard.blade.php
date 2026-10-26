@extends('layouts.admin')

@section('title', 'Good morning, ' . (Auth::user()->name ?? 'Admin'))
@section('subtitle', 'From truck to cash: where your coffee money is right now.')

@section('content')

@php
    $distributed      = $distributed ?? 0;
    $sold             = $sold ?? 0;
    $collected        = $collected ?? 0;
    $outstanding      = $outstanding ?? 0;
    $distributedCount = $distributedCount ?? 0;
    $storeBalances    = collect($storeBalances ?? []);
    $recentReports    = collect($recentReports ?? []);
    $recentDeliveries = collect($recentDeliveries ?? []);
    $trend            = collect($trend ?? []);

    $paidRate = $sold > 0 ? min(100, round($collected / $sold * 100)) : 0;
    $unpaid   = max(0, $sold - $collected);
    $unsold   = max(0, $distributed - $sold);
    $maxBal   = max(1, $storeBalances->max('total_balance'));
@endphp

<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .ds { --ink:#17211D; --muted:#6B7770; --line:#E6EAE7; --soft:#F5F7F6; --brand:#1D5F4C; --amber:#E39B2B; --bad:#C2412D; --warn:#B7791F;
          font-family:'Manrope',system-ui,sans-serif; color:var(--ink); }
    .ds *, .ds *::before, .ds *::after { box-sizing:border-box; }
    .ds .num { font-variant-numeric:tabular-nums; letter-spacing:-.03em; }
    .ds .card { background:#fff; border:1px solid var(--line); border-radius:20px; }
    .ds .pad { padding:24px; }
    .ds h3 { margin:0; font-size:16px; font-weight:800; }
    .ds .sub { margin:2px 0 0; font-size:13px; color:var(--muted); }
    .ds .more { font-size:13px; font-weight:700; color:var(--brand); text-decoration:none; white-space:nowrap; }
    .ds .more:hover { text-decoration:underline; }
    .ds .head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:18px; }
    .ds .cols { display:grid; gap:20px; margin-top:20px; grid-template-columns:1fr; }
    @media (min-width:1024px) { .ds .c-a { grid-template-columns:2fr 1fr; } .ds .c-b { grid-template-columns:3fr 2fr; } .ds .c-c { grid-template-columns:3fr 2fr; } }

    /* KPI cards */
    .ds .kpis { display:grid; gap:16px; grid-template-columns:1fr; }
    @media (min-width:640px) { .ds .kpis { grid-template-columns:repeat(2,1fr); } }
    @media (min-width:1100px) { .ds .kpis { grid-template-columns:repeat(4,1fr); } }
    .ds .kpi { padding:22px; display:flex; justify-content:space-between; gap:12px; }
    .ds .kpi .k { font-size:14px; color:var(--muted); font-weight:600; }
    .ds .kpi .v { font-size:clamp(24px,2.4vw,31px); font-weight:800; margin-top:8px; line-height:1.1; }
    .ds .kpi .s { font-size:13px; color:var(--muted); margin-top:6px; }
    .ds .ico { width:46px; height:46px; border-radius:14px; display:grid; place-items:center; font-size:18px; flex:none; }
    .ds .kpi.owe { background:linear-gradient(160deg,#FFF8E6,#FDEFC9); border-color:#F1D693; }
    .ds .kpi.owe .v { color:#7A4E07; }

    /* Doughnut */
    .ds .donut-wrap { position:relative; height:200px; }
    .ds .donut-mid { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; }
    .ds .donut-mid b { font-size:30px; font-weight:800; letter-spacing:-.03em; }
    .ds .donut-mid small { font-size:12px; color:var(--muted); font-weight:600; }
    .ds .legend { margin-top:18px; display:grid; gap:10px; }
    .ds .lg { display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:14px; }
    .ds .lg span:first-child { display:flex; align-items:center; gap:10px; font-weight:600; color:#343E3A; }
    .ds .lg i { width:10px; height:10px; border-radius:3px; flex:none; }
    .ds .lg b { font-weight:800; }

    /* Stores */
    .ds .store { display:flex; align-items:center; gap:12px; padding:11px 0; }
    .ds .store + .store { border-top:1px solid var(--line); }
    .ds .rank { width:30px; height:30px; border-radius:10px; background:var(--soft); font-size:13px; font-weight:800; display:grid; place-items:center; flex:none; }
    .ds .store-main { flex:1; min-width:0; }
    .ds .store-top { display:flex; justify-content:space-between; gap:10px; font-size:14px; font-weight:700; }
    .ds .store-top span:first-child { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .ds .mini { height:5px; background:var(--soft); border-radius:5px; margin-top:7px; overflow:hidden; }
    .ds .mini i { display:block; height:100%; background:var(--bad); border-radius:5px; }
    .ds .bad { color:var(--bad); } .ds .ok { color:var(--brand); } .ds .warn { color:var(--warn); }

    /* Table + list */
    .ds .tbl-wrap { overflow-x:auto; }
    .ds table { width:100%; border-collapse:collapse; font-size:14px; }
    .ds th { text-align:left; font-size:13px; color:var(--muted); font-weight:600; padding:0 10px 10px 0; border-bottom:1px solid var(--line); white-space:nowrap; }
    .ds td { padding:13px 10px 13px 0; border-bottom:1px solid var(--line); white-space:nowrap; }
    .ds tbody tr:last-child td { border-bottom:0; }
    .ds .r { text-align:right; padding-right:0; }
    .ds .chip { display:inline-block; font-size:12px; font-weight:700; padding:3px 11px; border-radius:999px; }
    .ds .chip.ok { background:#D6EAE2; color:#184D3E; } .ds .chip.warn { background:#FDECC8; color:#8A5A0B; } .ds .chip.bad { background:#FBDDD7; color:#98291A; }
    .ds .del { display:flex; justify-content:space-between; gap:12px; padding:13px 0; }
    .ds .del + .del { border-top:1px solid var(--line); }
    .ds .del-name { font-size:14px; font-weight:700; }
    .ds .del-meta { font-size:12.5px; color:var(--muted); margin-top:2px; }
    .ds .empty { padding:28px 8px; text-align:center; color:var(--muted); font-size:14px; }
    .ds .empty b { display:block; color:var(--ink); margin-bottom:2px; }
</style>

<div class="ds">

    {{-- ══ KPI CARDS ══ --}}
    <section class="kpis" aria-label="Money summary">
        <div class="card kpi">
            <div>
                <div class="k">Distributed</div>
                <div class="v num">&#8369;{{ number_format($distributed, 0) }}</div>
                <div class="s">{{ $distributedCount }} {{ \Illuminate\Support\Str::plural('delivery', $distributedCount) }}</div>
            </div>
            <div class="ico" style="background:#E8EEFB; color:#3558B8"><i class="fas fa-truck"></i></div>
        </div>
        <div class="card kpi">
            <div>
                <div class="k">Sold</div>
                <div class="v num">&#8369;{{ number_format($sold, 0) }}</div>
                <div class="s">From reports</div>
            </div>
            <div class="ico" style="background:#EEF6F3; color:#2A7B63"><i class="fas fa-store"></i></div>
        </div>
        <div class="card kpi">
            <div>
                <div class="k">Collected</div>
                <div class="v num" style="color:var(--brand)">&#8369;{{ number_format($collected, 0) }}</div>
                <div class="s">Payments in</div>
            </div>
            <div class="ico" style="background:#D6EAE2; color:#1D5F4C"><i class="fas fa-peso-sign"></i></div>
        </div>
        <div class="card kpi owe">
            <div>
                <div class="k" style="color:#8A5A0B">Still to collect</div>
                <div class="v num">&#8369;{{ number_format($outstanding, 0) }}</div>
                <div class="s" style="color:#8A5A0B">@if($sold > 0) {{ $paidRate }}% of sales is paid @else No sales reported yet @endif</div>
            </div>
            <div class="ico" style="background:#fff; color:#B7791F"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </section>

    {{-- ══ LINE + PIE ══ --}}
    <div class="cols c-a">
        <section class="card pad">
            <div class="head"><div><h3>Sales trend</h3><p class="sub">Reported sales per period.</p></div></div>
            <div style="position:relative; height:300px;"><canvas id="salesChart" role="img" aria-label="Sales trend line chart"></canvas></div>
        </section>

        <section class="card pad">
            <div class="head"><div><h3>Where the money is</h3><p class="sub">Share of everything distributed.</p></div></div>
            <div class="donut-wrap">
                <canvas id="shareChart" role="img" aria-label="Pie chart of collected, sold but unpaid, and not yet sold"></canvas>
                <div class="donut-mid"><b class="num">{{ $paidRate }}%</b><small>of sales paid</small></div>
            </div>
            <div class="legend">
                <div class="lg"><span><i style="background:#1D5F4C"></i>Collected</span><b class="num">&#8369;{{ number_format($collected, 0) }}</b></div>
                <div class="lg"><span><i style="background:#E39B2B"></i>Sold, not paid</span><b class="num">&#8369;{{ number_format($unpaid, 0) }}</b></div>
                <div class="lg"><span><i style="background:#C9D1CD"></i>Not yet sold</span><b class="num">&#8369;{{ number_format($unsold, 0) }}</b></div>
            </div>
        </section>
    </div>

    {{-- ══ BAR + STORES ══ --}}
    <div class="cols c-b">
        <section class="card pad">
            <div class="head"><div><h3>Truck to cash</h3><p class="sub">The amount at each step, side by side.</p></div></div>
            <div style="position:relative; height:290px;"><canvas id="stepsChart" role="img" aria-label="Bar graph of distributed, sold, collected and still to collect"></canvas></div>
        </section>

        <section class="card pad">
            <div class="head">
                <div><h3>Who owes the most</h3><p class="sub">Top 5 store balances.</p></div>
                <a class="more" href="{{ route('stores.index') }}">All stores</a>
            </div>
            @forelse($storeBalances->take(5) as $sb)
                @php $owes = $sb->total_balance > 0; @endphp
                <div class="store">
                    <div class="rank">{{ $loop->iteration }}</div>
                    <div class="store-main">
                        <div class="store-top">
                            <span>{{ $sb->store->store_name ?? 'Unknown' }}</span>
                            <span class="num {{ $owes ? 'bad' : 'ok' }}">&#8369;{{ number_format($sb->total_balance, 0) }}</span>
                        </div>
                        <div class="mini"><i style="width:{{ $owes ? max(4, round($sb->total_balance / $maxBal * 100)) : 0 }}%"></i></div>
                    </div>
                </div>
            @empty
                <div class="empty"><b>No balances yet</b>Balances appear after your first delivery.</div>
            @endforelse
        </section>
    </div>

    {{-- ══ REPORTS + DELIVERIES ══ --}}
    <div class="cols c-c">
        <section class="card pad">
            <div class="head"><h3>Latest sales reports</h3><a class="more" href="{{ route('sales-reports.index') }}">All reports</a></div>
            @if($recentReports->count() > 0)
                <div class="tbl-wrap">
                    <table>
                        <thead><tr><th>Report</th><th>Store</th><th>Period</th><th class="r">Total</th><th class="r">Status</th></tr></thead>
                        <tbody>
                            @foreach($recentReports as $r)
                                @php $tone = in_array($r->status, ['paid','closed']) ? 'ok' : ($r->status === 'partial' ? 'warn' : 'bad'); @endphp
                                <tr>
                                    <td style="font-weight:700">{{ $r->report_number }}</td>
                                    <td>{{ $r->store->store_name ?? '—' }}</td>
                                    <td style="color:var(--muted)">{{ optional($r->period_from)->format('M d') }} – {{ optional($r->period_to)->format('M d, Y') }}</td>
                                    <td class="r num" style="font-weight:700">&#8369;{{ number_format($r->total_sales, 0) }}</td>
                                    <td class="r"><span class="chip {{ $tone }}">{{ ucfirst($r->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty"><b>No sales reports yet</b>Reports show here once a store submits one.</div>
            @endif
        </section>

        <section class="card pad">
            <div class="head"><h3>Latest deliveries</h3><a class="more" href="{{ route('deliveries.index') }}">All deliveries</a></div>
            @forelse($recentDeliveries as $d)
                <div class="del">
                    <div>
                        <div class="del-name">{{ $d->store->store_name ?? 'Unknown' }}</div>
                        <div class="del-meta">{{ $d->dr_number }} &middot; {{ optional($d->delivery_date)->format('M d') }}</div>
                    </div>
                    <div class="num" style="font-weight:800; font-size:14px">&#8369;{{ number_format($d->total_amount, 0) }}</div>
                </div>
            @empty
                <div class="empty"><b>No deliveries yet</b>New delivery receipts appear here.</div>
            @endforelse
        </section>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    if (typeof Chart === 'undefined') return;
    const peso = '\u20B1';
    const font = { family: 'Manrope', size: 11 };
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const anim = calm ? false : { duration: 500 };
    const money = v => peso + (v >= 1000 ? (v / 1000) + 'k' : v);
    const tip = {
        backgroundColor: '#17211D', padding: 10, cornerRadius: 8, displayColors: false,
        bodyFont: { family: 'Manrope', size: 14, weight: '700' }
    };
    const axes = {
        y: { beginAtZero: true, border: { display: false }, grid: { color: '#E6EAE7' }, ticks: { color: '#6B7770', font, padding: 8, callback: money } },
        x: { border: { display: false }, grid: { display: false }, ticks: { color: '#6B7770', font } }
    };

    // 1. Sales trend (line)
    const trendEl = document.getElementById('salesChart');
    const ctx = trendEl.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(29,95,76,.16)');
    grad.addColorStop(1, 'rgba(29,95,76,0)');
    new Chart(ctx, {
        type: 'line',
        data: { labels: @json($trend->pluck('label')), datasets: [{
            data: @json($trend->pluck('sold')), borderColor: '#1D5F4C', backgroundColor: grad, fill: true,
            borderWidth: 2.5, tension: .35, pointRadius: 0, pointHoverRadius: 5,
            pointHoverBackgroundColor: '#1D5F4C', pointHoverBorderColor: '#fff', pointHoverBorderWidth: 2
        }]},
        options: {
            responsive: true, maintainAspectRatio: false, animation: anim, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: { ...tip, callbacks: { label: c => peso + c.parsed.y.toLocaleString() } } },
            scales: axes
        }
    });

    // 2. Where the money is (doughnut)
    const share = [{{ (float) $collected }}, {{ (float) $unpaid }}, {{ (float) $unsold }}];
    const empty = share.every(v => v <= 0);
    new Chart(document.getElementById('shareChart'), {
        type: 'doughnut',
        data: { labels: ['Collected', 'Sold, not paid', 'Not yet sold'], datasets: [{
            data: empty ? [1] : share,
            backgroundColor: empty ? ['#E6EAE7'] : ['#1D5F4C', '#E39B2B', '#C9D1CD'],
            borderWidth: 0, spacing: empty ? 0 : 3, borderRadius: empty ? 0 : 6, hoverOffset: 4
        }]},
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '74%', animation: anim,
            plugins: { legend: { display: false }, tooltip: { ...tip, enabled: !empty, callbacks: { label: c => ' ' + c.label + ': ' + peso + c.parsed.toLocaleString() } } }
        }
    });

    // 3. Truck to cash (bar graph)
    new Chart(document.getElementById('stepsChart'), {
        type: 'bar',
        data: { labels: ['Distributed', 'Sold', 'Collected', 'Still to collect'], datasets: [{
            data: [{{ (float) $distributed }}, {{ (float) $sold }}, {{ (float) $collected }}, {{ (float) $outstanding }}],
            backgroundColor: ['#94B8AC', '#4A9980', '#1D5F4C', '#E39B2B'],
            borderRadius: 10, borderSkipped: false, maxBarThickness: 64
        }]},
        options: {
            responsive: true, maintainAspectRatio: false, animation: anim,
            plugins: { legend: { display: false }, tooltip: { ...tip, callbacks: { label: c => peso + c.parsed.y.toLocaleString() } } },
            scales: axes
        }
    });
})();
</script>
@endpush