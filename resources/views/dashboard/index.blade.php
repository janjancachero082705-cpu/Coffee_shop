@extends('layouts.admin')

@section('title', 'Good morning, ' . (Auth::user()->name ?? 'Admin'))
@section('subtitle', 'From truck to cash: where your coffee money is right now.')

@section('content')

@php
    $top = max(1, $distributed, $sold, $collected);
    $w = fn($v) => max($v > 0 ? 2 : 0, round($v / $top * 100));
    $maxBal = max(1, collect($storeBalances)->max('total_balance'));
@endphp

<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .dx { --ink:#16221D; --muted:#6B7770; --line:#E3E7E2; --paper:#FFFFFF; --bg:#EFF1ED;
          --brew:#B5651D; --good:#2F7D5B; --bad:#C2412D; --warn:#B7791F;
          font-family:'Manrope',system-ui,-apple-system,'Segoe UI',sans-serif; color:var(--ink); }
    .dx * { box-sizing:border-box; }
    .dx .num { font-variant-numeric:tabular-nums; letter-spacing:-.02em; }
    .dx .panel { background:var(--paper); border:1px solid var(--line); border-radius:14px; padding:24px; }
    .dx h3 { margin:0; font-size:16px; font-weight:800; }
    .dx .sub { margin:2px 0 0; font-size:13px; color:var(--muted); }
    .dx .head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:20px; }
    .dx a.more { font-size:13px; font-weight:700; color:var(--brew); text-decoration:none; white-space:nowrap; }
    .dx a.more:hover { text-decoration:underline; }
    .dx a:focus-visible { outline:2px solid var(--brew); outline-offset:3px; border-radius:4px; }
    .dx .grid { display:grid; gap:20px; margin-bottom:20px; }
    .dx .g-hero { grid-template-columns:1fr; }
    .dx .g-mid, .dx .g-low { grid-template-columns:1fr; }
    @media (min-width:1024px) {
        .dx .g-hero { grid-template-columns:2fr 1fr; }
        .dx .g-mid { grid-template-columns:3fr 2fr; }
        .dx .g-low { grid-template-columns:3fr 2fr; }
    }

    /* Money flow */
    .dx .flow-row { display:grid; grid-template-columns:96px 1fr 120px; align-items:center; gap:14px; padding:14px 0; border-top:1px solid var(--line); }
    .dx .flow-row:first-of-type { border-top:0; padding-top:0; }
    .dx .flow-label { font-size:14px; font-weight:700; }
    .dx .flow-label small { display:block; font-weight:500; color:var(--muted); font-size:12px; }
    .dx .track { height:28px; background:var(--bg); border-radius:8px; overflow:hidden; }
    .dx .fill { height:100%; border-radius:8px; }
    .dx .flow-val { text-align:right; font-weight:800; font-size:15px; }
    @media (max-width:560px) { .dx .flow-row { grid-template-columns:1fr 100px; } .dx .track { grid-column:1 / -1; order:3; } }

    /* Outstanding block */
    .dx .owed { background:var(--ink); color:#fff; border-radius:14px; padding:28px; display:flex; flex-direction:column; justify-content:space-between; gap:28px; }
    .dx .owed .k { font-size:14px; color:#B9C6BF; font-weight:600; }
    .dx .owed .v { font-size:44px; line-height:1.05; font-weight:800; margin-top:8px; }
    .dx .owed .note { font-size:13px; color:#B9C6BF; border-top:1px solid rgba(255,255,255,.14); padding-top:14px; }
    .dx .owed .note b { color:#fff; }

    /* Stores */
    .dx .store { display:flex; align-items:center; gap:12px; padding:12px 0; border-top:1px solid var(--line); }
    .dx .store:first-child { border-top:0; padding-top:0; }
    .dx .rank { width:26px; height:26px; border-radius:50%; background:var(--bg); font-size:12px; font-weight:800; display:grid; place-items:center; flex:none; }
    .dx .store-main { flex:1; min-width:0; }
    .dx .store-name { font-size:14px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .dx .mini { height:4px; background:var(--bg); border-radius:4px; margin-top:7px; overflow:hidden; }
    .dx .mini i { display:block; height:100%; border-radius:4px; }
    .dx .store-amt { font-size:14px; font-weight:800; }

    /* Table */
    .dx .tbl-wrap { overflow-x:auto; }
    .dx table { width:100%; border-collapse:collapse; font-size:14px; }
    .dx th { text-align:left; font-size:12px; color:var(--muted); font-weight:700; padding:0 8px 10px; border-bottom:1px solid var(--line); white-space:nowrap; }
    .dx td { padding:13px 8px; border-bottom:1px solid var(--line); white-space:nowrap; }
    .dx tr:last-child td { border-bottom:0; }
    .dx .r { text-align:right; }
    .dx .pill { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; }
    .dx .pill::before { content:''; width:8px; height:8px; border-radius:50%; background:currentColor; }
    .dx .ok { color:var(--good); } .dx .warn { color:var(--warn); } .dx .bad { color:var(--bad); }

    /* Deliveries */
    .dx .del { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-top:1px solid var(--line); }
    .dx .del:first-child { border-top:0; padding-top:0; }
    .dx .del-name { font-size:14px; font-weight:700; }
    .dx .del-meta { font-size:12px; color:var(--muted); margin-top:2px; }
    .dx .empty { text-align:center; padding:28px 8px; color:var(--muted); font-size:14px; }
    .dx .empty b { display:block; color:var(--ink); margin-bottom:2px; }
</style>

<div class="dx">

    {{-- ══ MONEY FLOW + OUTSTANDING ══ --}}
    <div class="grid g-hero">
        <section class="panel">
            <div class="head">
                <div>
                    <h3>Money flow</h3>
                    <p class="sub">How much has moved at each step, to scale.</p>
                </div>
            </div>

            <div class="flow-row">
                <div class="flow-label">Distributed<small>{{ $distributedCount }} {{ \Illuminate\Support\Str::plural('delivery', $distributedCount) }}</small></div>
                <div class="track"><div class="fill" style="width:{{ $w($distributed) }}%; background:var(--ink)"></div></div>
                <div class="flow-val num">&#8369;{{ number_format($distributed, 0) }}</div>
            </div>
            <div class="flow-row">
                <div class="flow-label">Sold<small>From reports</small></div>
                <div class="track"><div class="fill" style="width:{{ $w($sold) }}%; background:var(--brew)"></div></div>
                <div class="flow-val num">&#8369;{{ number_format($sold, 0) }}</div>
            </div>
            <div class="flow-row">
                <div class="flow-label">Collected<small>Payments in</small></div>
                <div class="track"><div class="fill" style="width:{{ $w($collected) }}%; background:var(--good)"></div></div>
                <div class="flow-val num">&#8369;{{ number_format($collected, 0) }}</div>
            </div>
        </section>

        <section class="owed">
            <div>
                <div class="k">Still to collect</div>
                <div class="v num">&#8369;{{ number_format($outstanding, 0) }}</div>
            </div>
            <div class="note">
                @if($sold > 0)
                    <b>{{ min(100, round($collected / $sold * 100)) }}%</b> of reported sales has been paid.
                @else
                    No sales reported yet.
                @endif
            </div>
        </section>
    </div>

    {{-- ══ TREND + STORES ══ --}}
    <div class="grid g-mid">
        <section class="panel">
            <div class="head">
                <div>
                    <h3>Sales trend</h3>
                    <p class="sub">Reported sales per period.</p>
                </div>
            </div>
            <div style="position:relative; height:290px;">
                <canvas id="salesChart" role="img" aria-label="Sales trend line chart"></canvas>
            </div>
        </section>

        <section class="panel">
            <div class="head">
                <div>
                    <h3>Who owes the most</h3>
                    <p class="sub">Top 5 store balances.</p>
                </div>
                <a class="more" href="{{ route('stores.index') }}">All stores</a>
            </div>
            @forelse($storeBalances->take(5) as $sb)
                @php $owes = $sb->total_balance > 0; @endphp
                <div class="store">
                    <div class="rank">{{ $loop->iteration }}</div>
                    <div class="store-main">
                        <div class="store-name">{{ $sb->store->store_name ?? 'Unknown' }}</div>
                        <div class="mini"><i style="width:{{ $owes ? max(4, round($sb->total_balance / $maxBal * 100)) : 0 }}%; background:{{ $owes ? 'var(--bad)' : 'var(--good)' }}"></i></div>
                    </div>
                    <div class="store-amt num {{ $owes ? 'bad' : 'ok' }}">&#8369;{{ number_format($sb->total_balance, 0) }}</div>
                </div>
            @empty
                <div class="empty"><b>No balances yet</b>Balances appear after your first delivery.</div>
            @endforelse
        </section>
    </div>

    {{-- ══ REPORTS + DELIVERIES ══ --}}
    <div class="grid g-low">
        <section class="panel">
            <div class="head">
                <h3>Latest sales reports</h3>
                <a class="more" href="{{ route('sales-reports.index') }}">All reports</a>
            </div>
            @if($recentReports->count() > 0)
                <div class="tbl-wrap">
                    <table>
                        <thead>
                            <tr><th>Report</th><th>Store</th><th>Period</th><th class="r">Total</th><th class="r">Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $r)
                                @php $tone = in_array($r->status, ['paid','closed']) ? 'ok' : ($r->status === 'partial' ? 'warn' : 'bad'); @endphp
                                <tr>
                                    <td style="font-weight:700; color:var(--brew)">{{ $r->report_number }}</td>
                                    <td>{{ $r->store->store_name ?? '—' }}</td>
                                    <td style="color:var(--muted)">{{ $r->period_from->format('M d') }} – {{ $r->period_to->format('M d, Y') }}</td>
                                    <td class="r num" style="font-weight:800">&#8369;{{ number_format($r->total_sales, 0) }}</td>
                                    <td class="r"><span class="pill {{ $tone }}">{{ ucfirst($r->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty"><b>No sales reports yet</b>Reports show here once a store submits one.</div>
            @endif
        </section>

        <section class="panel">
            <div class="head">
                <h3>Latest deliveries</h3>
                <a class="more" href="{{ route('deliveries.index') }}">All deliveries</a>
            </div>
            @forelse($recentDeliveries as $d)
                <div class="del">
                    <div>
                        <div class="del-name">{{ $d->store->store_name ?? 'Unknown' }}</div>
                        <div class="del-meta">{{ $d->dr_number }} &middot; {{ $d->delivery_date->format('M d') }}</div>
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
    const peso = '\u20B1';
    const labels = @json(collect($trend)->pluck('label'));
    const values = @json(collect($trend)->pluck('sold'));
    const ctx = document.getElementById('salesChart').getContext('2d');
    const fillGrad = ctx.createLinearGradient(0, 0, 0, 290);
    fillGrad.addColorStop(0, 'rgba(181,101,29,.22)');
    fillGrad.addColorStop(1, 'rgba(181,101,29,0)');
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{
            data: values, borderColor: '#B5651D', backgroundColor: fillGrad, fill: true,
            borderWidth: 2.5, tension: .35, pointRadius: 0, pointHoverRadius: 6,
            pointHoverBackgroundColor: '#B5651D', pointHoverBorderColor: '#fff', pointHoverBorderWidth: 2
        }]},
        options: {
            responsive: true, maintainAspectRatio: false, animation: calm ? false : { duration: 500 },
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: {
                backgroundColor: '#16221D', padding: 12, cornerRadius: 8, displayColors: false,
                bodyFont: { family: 'Manrope', size: 14, weight: '700' },
                callbacks: { label: c => peso + c.parsed.y.toLocaleString() }
            }},
            scales: {
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#E3E7E2' },
                     ticks: { color: '#6B7770', font: { family: 'Manrope', size: 11 }, padding: 8,
                              callback: v => peso + (v >= 1000 ? (v/1000) + 'k' : v) } },
                x: { border: { display: false }, grid: { display: false },
                     ticks: { color: '#6B7770', font: { family: 'Manrope', size: 11 } } }
            }
        }
    });
</script>
@endpush