@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Good morning, ' . (Auth::user()->name ?? 'Admin') . '. From truck to cash: where your coffee money is right now.')

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
    $soldRate = $distributed > 0 ? min(100, round($sold / $distributed * 100)) : 0;
    $unpaid   = max(0, $sold - $collected);
    $unsold   = max(0, $distributed - $sold);

    $top5     = $storeBalances->take(5);
    $top5Sum  = $top5->sum('total_balance');
    $maxBal   = max(1, $top5->max('total_balance'));
    $recent5  = $recentDeliveries->take(5);
    $delSum   = max(1, $recent5->sum('total_amount'));
    $shades   = ['#FFE3C2', '#FFCD94', '#FFB566', '#FF9C38', '#FF8512'];

    $steps = [
        ['Distributed',      $distributed, '#FFE3C2', $distributedCount . ' ' . \Illuminate\Support\Str::plural('delivery', $distributedCount), 'neu'],
        ['Sold',             $sold,        '#FFC98B', $soldRate . '% of distributed', 'up'],
        ['Collected',        $collected,   '#FFA64D', $paidRate . '% of sold', 'up'],
        ['Still to collect', $outstanding, '#FF8512', $outstanding > 0 ? 'Unpaid' : 'All paid', $outstanding > 0 ? 'down' : 'up'],
    ];
    $maxStep = max(1, $distributed, $sold, $collected, $outstanding);

    $headline = $paidRate >= 80 ? 'Great progress!' : ($paidRate >= 40 ? 'Keep collecting' : 'Time to collect');
@endphp

<style>
    .ez { --o:#FF8512; --brand:#E86100; --soft:#FFEBD5; --peach:#FDE9D5; --ink:#1A1715; --muted:#78716A; --line:#F0EDE8; --card:#F9F7F4; --good:#18975A; --bad:#D13838;
          font-family:'Manrope',system-ui,sans-serif; color:var(--ink); }
    .ez *, .ez *::before, .ez *::after { box-sizing:border-box; }
    .ez .num { font-variant-numeric:tabular-nums; letter-spacing:-.02em; }
    .ez .box { background:var(--card); border:1px solid var(--line); border-radius:20px; padding:22px; min-width:0; }
    .ez h3 { margin:0; font-size:17px; font-weight:700; }
    .ez .sub { margin:2px 0 0; font-size:13px; color:var(--muted); }
    .ez .more { font-size:13px; font-weight:600; color:var(--muted); text-decoration:none; white-space:nowrap; }
    .ez .more:hover { color:var(--brand); }
    .ez .head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:16px; }

    /* Grid (mirrors the reference layout) */
    .ez .board { display:grid; gap:20px; grid-template-columns:1fr; }
    @media (min-width:760px) { .ez .board { grid-template-columns:1fr 1fr; } .ez .a-trend, .ez .a-fun, .ez .a-rep { grid-column:1 / -1; } }
    @media (min-width:1280px) {
        .ez .board { grid-template-columns:repeat(4,minmax(0,1fr)); grid-template-areas:"k1 k2 k3 don" "trend trend gauge don" "owe fun fun del" "rep rep rep rep"; }
        .ez .a-k1 { grid-area:k1; } .ez .a-k2 { grid-area:k2; } .ez .a-k3 { grid-area:k3; } .ez .a-don { grid-area:don; }
        .ez .a-trend { grid-area:trend; } .ez .a-gauge { grid-area:gauge; } .ez .a-owe { grid-area:owe; }
        .ez .a-fun { grid-area:fun; } .ez .a-del { grid-area:del; } .ez .a-rep { grid-area:rep; }
    }

    /* KPI */
    .ez .kpi { display:flex; flex-direction:column; justify-content:space-between; gap:22px; min-height:150px; }
    .ez .kpi.hl { background:var(--peach); border-color:#F7D9BC; }
    .ez .kpi .top { display:flex; justify-content:space-between; align-items:center; font-size:15px; font-weight:500; }
    .ez .kpi .ico { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; background:#fff; color:var(--muted); font-size:15px; border:1px solid var(--line); }
    .ez .kpi.hl .ico { background:var(--brand); color:#fff; border-color:var(--brand); }
    .ez .kpi .bot { display:flex; justify-content:space-between; align-items:flex-end; gap:10px; }
    .ez .kpi .v { font-size:clamp(24px,2.2vw,32px); font-weight:700; line-height:1; }
    .ez .kpi .m { font-size:12px; color:var(--muted); text-align:right; line-height:1.35; }
    .ez .kpi .m b { display:block; font-size:12px; font-weight:700; color:var(--good); }

    /* Donut */
    .ez .donut-wrap { position:relative; height:230px; margin:6px 0 18px; }
    .ez .donut-mid { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; text-align:center; }
    .ez .donut-mid small { font-size:12px; color:var(--muted); }
    .ez .donut-mid b { font-size:24px; font-weight:700; margin-top:2px; letter-spacing:-.02em; }
    .ez .lg { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:9px 0; font-size:14px; }
    .ez .lg span:first-child { display:flex; align-items:center; gap:10px; color:#57514B; }
    .ez .lg i { width:9px; height:9px; border-radius:3px; flex:none; }
    .ez .lg b { font-weight:700; }

    /* Gauge */
    .ez .gauge { position:relative; width:240px; max-width:100%; margin:6px auto 0; }
    .ez .gauge svg { display:block; width:100%; height:auto; }
    .ez .gauge-mid { position:absolute; left:0; right:0; bottom:2px; text-align:center; }
    .ez .gauge-mid b { font-size:30px; font-weight:700; letter-spacing:-.03em; }
    .ez .gauge-mid small { display:block; font-size:12px; color:var(--muted); }
    .ez .talk { text-align:center; margin-top:14px; }
    .ez .talk b { font-size:15px; font-weight:700; }
    .ez .talk p { margin:4px 0 0; font-size:13px; color:var(--muted); line-height:1.5; }
    .ez .talk p em { font-style:normal; font-weight:700; color:var(--brand); }
    .ez .pair { display:grid; grid-template-columns:1fr 1fr; margin-top:16px; background:var(--peach); border-radius:12px; overflow:hidden; }
    .ez .pair div { padding:10px 8px; text-align:center; font-size:12px; color:var(--muted); }
    .ez .pair div + div { border-left:1px solid #F7D9BC; }
    .ez .pair b { display:block; margin-top:2px; font-size:15px; font-weight:700; color:var(--ink); }

    /* Owe list */
    .ez .big { font-size:28px; font-weight:700; line-height:1; margin-top:2px; }
    .ez .row { margin-top:16px; }
    .ez .row-top { display:flex; justify-content:space-between; gap:10px; font-size:14px; }
    .ez .row-top span:first-child { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#3F3A36; }
    .ez .row-top span:last-child { font-weight:700; }
    .ez .track { height:8px; background:var(--soft); border-radius:8px; margin-top:8px; overflow:hidden; }
    .ez .track i { display:block; height:100%; background:var(--o); border-radius:8px; }

    /* Funnel */
    .ez .funnel { display:grid; grid-template-columns:repeat(4,1fr); }
    .ez .col { padding:0 14px; border-left:1px solid var(--line); display:flex; flex-direction:column; min-width:0; }
    .ez .col:first-child { padding-left:0; border-left:0; }
    .ez .col .l { font-size:12px; color:var(--muted); line-height:1.3; min-height:32px; }
    .ez .col .n { font-size:clamp(18px,1.6vw,24px); font-weight:700; margin-top:6px; }
    .ez .chip { display:inline-block; align-self:flex-start; margin-top:8px; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px; }
    .ez .chip.up { background:#E3F7EC; color:var(--good); } .ez .chip.down { background:#FDE4E4; color:var(--bad); } .ez .chip.neu { background:#EFEBE5; color:#57514B; }
    .ez .bars { display:grid; grid-template-columns:repeat(4,1fr); align-items:end; height:150px; margin-top:16px; }
    .ez .bars div { border-radius:12px 12px 0 0; }
    @media (max-width:560px) { .ez .funnel { grid-template-columns:repeat(2,1fr); row-gap:18px; } .ez .col:nth-child(3) { padding-left:0; border-left:0; } .ez .bars { grid-template-columns:repeat(4,1fr); } }

    /* Deliveries */
    .ez .seg { display:flex; gap:3px; height:44px; margin-bottom:6px; }
    .ez .seg i { display:block; height:100%; border-radius:6px; min-width:6px; }
    .ez .del { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 0; font-size:14px; }
    .ez .del > span:first-child { display:flex; align-items:center; gap:10px; min-width:0; }
    .ez .del i { width:9px; height:9px; border-radius:3px; flex:none; }
    .ez .del em { font-style:normal; display:block; font-size:12px; color:var(--muted); }
    .ez .del b { font-weight:700; white-space:nowrap; }

    /* Reports table */
    .ez .tbl-wrap { overflow-x:auto; }
    .ez table { width:100%; border-collapse:collapse; font-size:14px; }
    .ez th { text-align:left; font-size:13px; color:var(--muted); font-weight:600; padding:0 12px 12px 0; border-bottom:1px solid var(--line); white-space:nowrap; }
    .ez td { padding:14px 12px 14px 0; border-bottom:1px solid var(--line); white-space:nowrap; }
    .ez tbody tr:last-child td { border-bottom:0; }
    .ez .r { text-align:right; padding-right:0; }
    .ez .st { display:inline-block; font-size:12px; font-weight:700; padding:3px 10px; border-radius:6px; }
    .ez .st.ok { background:#E3F7EC; color:var(--good); } .ez .st.warn { background:#FFF0D6; color:#B26A00; } .ez .st.bad { background:#FDE4E4; color:var(--bad); }
    .ez .empty { padding:26px 8px; text-align:center; color:var(--muted); font-size:14px; }
    .ez .empty b { display:block; color:var(--ink); margin-bottom:2px; }
</style>

<div class="ez">
<div class="board">

    {{-- KPI cards --}}
    <section class="box kpi hl a-k1">
        <div class="top">Distributed<span class="ico"><i class="fas fa-truck"></i></span></div>
        <div class="bot">
            <div class="v num">&#8369;{{ number_format($distributed, 0) }}</div>
            <div class="m"><b>{{ $distributedCount }}</b>{{ \Illuminate\Support\Str::plural('delivery', $distributedCount) }}</div>
        </div>
    </section>
    <section class="box kpi a-k2">
        <div class="top">Sold<span class="ico"><i class="fas fa-store"></i></span></div>
        <div class="bot">
            <div class="v num">&#8369;{{ number_format($sold, 0) }}</div>
            <div class="m"><b>{{ $soldRate }}%</b>of distributed</div>
        </div>
    </section>
    <section class="box kpi a-k3">
        <div class="top">Collected<span class="ico"><i class="fas fa-peso-sign"></i></span></div>
        <div class="bot">
            <div class="v num">&#8369;{{ number_format($collected, 0) }}</div>
            <div class="m"><b>{{ $paidRate }}%</b>of sold</div>
        </div>
    </section>

    {{-- Where the money is (donut) --}}
    <section class="box a-don">
        <div class="head"><h3>Where the money is</h3></div>
        <div class="donut-wrap">
            <canvas id="shareChart" role="img" aria-label="Chart of collected, sold but unpaid, and not yet sold"></canvas>
            <div class="donut-mid"><small>Total distributed</small><b class="num">&#8369;{{ number_format($distributed, 0) }}</b></div>
        </div>
        <div>
            <div class="lg"><span><i style="background:#FF8512"></i>Collected</span><b class="num">&#8369;{{ number_format($collected, 0) }}</b></div>
            <div class="lg"><span><i style="background:#FFB566"></i>Sold, not paid</span><b class="num">&#8369;{{ number_format($unpaid, 0) }}</b></div>
            <div class="lg"><span><i style="background:#FFE3C2"></i>Not yet sold</span><b class="num">&#8369;{{ number_format($unsold, 0) }}</b></div>
        </div>
    </section>

    {{-- Sales trend --}}
    <section class="box a-trend">
        <div class="head"><div><h3>Sales trend</h3><p class="sub">Reported sales per period.</p></div></div>
        <div style="position:relative; height:290px;"><canvas id="salesChart" role="img" aria-label="Sales trend line chart"></canvas></div>
    </section>

    {{-- Collection progress (gauge) --}}
    <section class="box a-gauge">
        <div class="head"><h3>Collection progress</h3></div>
        <div class="gauge">
            <svg viewBox="0 0 240 130" aria-hidden="true">
                <path d="M 24 120 A 96 96 0 0 1 216 120" fill="none" stroke="#FFE3C2" stroke-width="24" stroke-linecap="round" pathLength="100"/>
                @if($paidRate > 0)
                    <path d="M 24 120 A 96 96 0 0 1 216 120" fill="none" stroke="#FF8512" stroke-width="24" stroke-linecap="round" pathLength="100" stroke-dasharray="{{ $paidRate }} 100"/>
                @endif
            </svg>
            <div class="gauge-mid"><b class="num">{{ $paidRate }}%</b><small>of sales paid</small></div>
        </div>
        <div class="talk">
            <b>{{ $headline }}</b>
            <p>@if($outstanding > 0)<em>&#8369;{{ number_format($outstanding, 0) }}</em> still to collect. Let's reach 100%.@elseif($sold > 0)Everything reported has been paid.@else No sales reported yet.@endif</p>
        </div>
        <div class="pair">
            <div>Sold<b class="num">&#8369;{{ number_format($sold, 0) }}</b></div>
            <div>Collected<b class="num">&#8369;{{ number_format($collected, 0) }}</b></div>
        </div>
    </section>

    {{-- Who owes the most --}}
    <section class="box a-owe">
        <div class="head"><h3>Who owes the most</h3><a class="more" href="{{ route('stores.index') }}">See all</a></div>
        <div class="big num">&#8369;{{ number_format($top5Sum, 0) }}</div>
        <div class="sub">Top 5 store balances</div>
        @forelse($top5 as $sb)
            @php $owes = $sb->total_balance > 0; @endphp
            <div class="row">
                <div class="row-top"><span>{{ $sb->store->store_name ?? 'Unknown' }}</span><span class="num">&#8369;{{ number_format($sb->total_balance, 0) }}</span></div>
                <div class="track"><i style="width:{{ $owes ? max(4, round($sb->total_balance / $maxBal * 100)) : 0 }}%"></i></div>
            </div>
        @empty
            <div class="empty"><b>No balances yet</b>Balances appear after your first delivery.</div>
        @endforelse
    </section>

    {{-- Truck to cash (funnel) --}}
    <section class="box a-fun">
        <div class="head"><div><h3>Truck to cash</h3><p class="sub">The amount at each step.</p></div></div>
        <div class="funnel">
            @foreach($steps as [$label, $val, $color, $note, $tone])
                <div class="col">
                    <div class="l">{{ $label }}</div>
                    <div class="n num">&#8369;{{ number_format($val, 0) }}</div>
                    <span class="chip {{ $tone }}">{{ $note }}</span>
                </div>
            @endforeach
        </div>
        <div class="bars" aria-hidden="true">
            @foreach($steps as [$label, $val, $color])
                <div style="height:{{ max(6, round($val / $maxStep * 100)) }}%; background:{{ $color }}"></div>
            @endforeach
        </div>
    </section>

    {{-- Latest deliveries --}}
    <section class="box a-del">
        <div class="head"><h3>Latest deliveries</h3><a class="more" href="{{ route('deliveries.index') }}">See all</a></div>
        @if($recent5->count() > 0)
            <div class="seg" aria-hidden="true">
                @foreach($recent5 as $d)<i style="flex:{{ max(0.05, $d->total_amount / $delSum) }}; background:{{ $shades[$loop->index % 5] }}"></i>@endforeach
            </div>
            @foreach($recent5 as $d)
                <div class="del">
                    <span><i style="background:{{ $shades[$loop->index % 5] }}"></i><span style="min-width:0">{{ $d->store->store_name ?? 'Unknown' }}<em>{{ $d->dr_number }} &middot; {{ optional($d->delivery_date)->format('M d') }}</em></span></span>
                    <b class="num">&#8369;{{ number_format($d->total_amount, 0) }}</b>
                </div>
            @endforeach
        @else
            <div class="empty"><b>No deliveries yet</b>New delivery receipts appear here.</div>
        @endif
    </section>

    {{-- Latest sales reports --}}
    <section class="box a-rep">
        <div class="head"><h3>Latest sales reports</h3><a class="more" href="{{ route('sales-reports.index') }}">See all</a></div>
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
                                <td class="r"><span class="st {{ $tone }}">{{ ucfirst($r->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty"><b>No sales reports yet</b>Reports show here once a store submits one.</div>
        @endif
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
    const anim = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? false : { duration: 500 };

    // Sales trend (line)
    const el = document.getElementById('salesChart');
    const ctx = el.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 290);
    grad.addColorStop(0, 'rgba(255,133,18,.18)');
    grad.addColorStop(1, 'rgba(255,133,18,0)');
    new Chart(ctx, {
        type: 'line',
        data: { labels: @json($trend->pluck('label')), datasets: [{
            data: @json($trend->pluck('sold')), borderColor: '#FF8512', backgroundColor: grad, fill: true,
            borderWidth: 2.5, tension: .4, pointRadius: 0, pointHoverRadius: 6,
            pointHoverBackgroundColor: '#fff', pointHoverBorderColor: '#FF8512', pointHoverBorderWidth: 2.5
        }]},
        options: {
            responsive: true, maintainAspectRatio: false, animation: anim, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: {
                backgroundColor: '#fff', titleColor: '#78716A', bodyColor: '#1A1715', borderColor: '#E8E5E0', borderWidth: 1,
                padding: 12, cornerRadius: 10, displayColors: false,
                titleFont: { family: 'Manrope', size: 11 }, bodyFont: { family: 'Manrope', size: 14, weight: '700' },
                callbacks: { label: c => peso + c.parsed.y.toLocaleString() }
            }},
            scales: {
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#EFEBE5', borderDash: [4, 4] },
                     ticks: { color: '#78716A', font, padding: 8, callback: v => peso + (v >= 1000 ? (v / 1000) + 'k' : v) } },
                x: { border: { display: false }, grid: { display: false }, ticks: { color: '#78716A', font } }
            }
        }
    });

    // Where the money is (donut)
    const share = [{{ (float) $collected }}, {{ (float) $unpaid }}, {{ (float) $unsold }}];
    const empty = share.every(v => v <= 0);
    new Chart(document.getElementById('shareChart'), {
        type: 'doughnut',
        data: { labels: ['Collected', 'Sold, not paid', 'Not yet sold'], datasets: [{
            data: empty ? [1] : share,
            backgroundColor: empty ? ['#EFEBE5'] : ['#FF8512', '#FFB566', '#FFE3C2'],
            borderWidth: 0, spacing: empty ? 0 : 4, borderRadius: empty ? 0 : 8, hoverOffset: 4
        }]},
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '76%', animation: anim,
            plugins: { legend: { display: false }, tooltip: {
                enabled: !empty, backgroundColor: '#fff', titleColor: '#78716A', bodyColor: '#1A1715', borderColor: '#E8E5E0', borderWidth: 1,
                padding: 10, cornerRadius: 10, displayColors: false, bodyFont: { family: 'Manrope', size: 13, weight: '700' },
                callbacks: { label: c => c.label + ': ' + peso + c.parsed.toLocaleString() }
            }}
        }
    });
})();
</script>
@endpush