@extends('layouts.admin')
@section('title', 'Dashboard')
@section('subtitle', 'Overview of your business performance')

@section('content')

@php
    $distributed      = $distributed ?? 0;
    $distributedCount = $distributedCount ?? 0;
    $sold             = $sold ?? 0;
    $collected        = $collected ?? 0;
    $outstanding      = $outstanding ?? 0;
    $totalExpenses    = $totalExpenses ?? 0;
    $totalPurchases   = $totalPurchases ?? 0;
    $netProfit        = $netProfit ?? 0;
    $trend            = collect($trend ?? []);
    $recentDeliveries = collect($recentDeliveries ?? []);
    $recentExpenses   = collect($recentExpenses ?? []);

    $chartExpenses  = max(0, $totalExpenses);
    $chartPurchases = max(0, $totalPurchases);
    $chartProfit    = max(0, $netProfit);

    $money    = fn($n, $d = 0) => ($n < 0 ? '-' : '') . "\u{20B1}" . number_format(abs($n), $d);
    $profitOk = $netProfit >= 0;

    $kpis = [
        ['Distributed', $money($distributed), $distributedCount . ' ' . \Illuminate\Support\Str::plural('delivery', $distributedCount), 'fa-truck',          'bg-brand-100 text-brand-700', ''],
        ['Sold',        $money($sold),        'Total sales reported',                                                                   'fa-store',          'bg-brand-100 text-brand-700', ''],
        ['Collected',   $money($collected),   'Cash in hand',                                                                           'fa-peso-sign',      'bg-emerald-100 text-emerald-700', ''],
        ['Outstanding', $money($outstanding), 'Still to collect',                                                                       'fa-hourglass-half', 'bg-amber-100 text-amber-700', 'text-amber-700'],
        ['Expenses',    $money($totalExpenses + $totalPurchases), 'Operations + purchases',                                             'fa-receipt',        'bg-red-100 text-red-600', ''],
        ['Cash profit', $money($netProfit),   'Collected − expenses',                                                                   'fa-chart-line',     $profitOk ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white', $profitOk ? 'text-emerald-700' : 'text-red-600'],
    ];
@endphp

<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6 pb-6">

    {{-- ══ 1. KPI CARDS ══ --}}
    <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-2.5 md:gap-4" aria-label="Key numbers">
        @foreach($kpis as [$label, $value, $note, $icon, $iconCls, $valueCls])
            @php $isProfit = $label === 'Cash profit'; @endphp
            <div class="card p-3 md:p-4 flex flex-col justify-between gap-2 min-h-[100px] {{ $isProfit ? ($profitOk ? '!bg-emerald-50 !border-emerald-100' : '!bg-red-50 !border-red-100') : '' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] md:text-xs font-bold text-ink-500 uppercase tracking-wider truncate" title="{{ $label }}">{{ $label }}</span>
                    <span class="w-6 h-6 md:w-8 md:h-8 rounded-lg flex items-center justify-center text-[10px] md:text-xs shrink-0 {{ $iconCls }}"><i class="fas {{ $icon }}"></i></span>
                </div>
                <div>
                    <div class="text-lg md:text-2xl leading-none font-extrabold tracking-tight tabular-nums truncate {{ $valueCls ?: 'text-ink-900' }}">{{ $value }}</div>
                    <div class="text-[9px] md:text-[10px] font-medium text-ink-400 mt-1 truncate">{{ $note }}</div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ══ 2. CHARTS ══ --}}
    <section class="flex flex-col lg:grid lg:grid-cols-3 gap-4 md:gap-6">
        <div class="tabbar" role="tablist">
            <button type="button" class="tabbtn is-on" data-tab="pnSales" role="tab"><i class="fas fa-chart-line"></i> Sales Trend</button>
            <button type="button" class="tabbtn" data-tab="pnFlow" role="tab"><i class="fas fa-chart-pie"></i> Cash Flow</button>
        </div>

        {{-- Sales trend --}}
        <div id="pnSales" class="lg:col-span-2 card p-4 md:p-6 w-full min-w-0 flex flex-col">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 shrink-0">
                <div>
                    <h3 class="text-sm md:text-base font-bold text-ink-900">Sales Trend</h3>
                    <p class="text-[10px] md:text-xs text-ink-500 mt-0.5">Reported sales over the last 6 months</p>
                </div>
                <span class="text-[10px] font-bold text-brand-600 bg-brand-50 px-2 py-1 rounded">Last 6 Months</span>
            </div>
            
            <div class="relative w-full" style="height: 250px;">
                
                {{-- GIBUTANGAN NAKOG EXPLICIT WIDTH UG HEIGHT PARA MA-FORCE DRAW --}}
                @php
  $sv = $trend->pluck('sold')->map(fn($v)=>(float)$v)->values();
  $sl = $trend->pluck('label')->values();
  $n = $sv->count(); $mx = max($sv->max() ?? 0, 1);
  $W = 600; $H = 200; $P = [];
  foreach ($sv as $i => $v) { $P[] = [ $n > 1 ? round($i*$W/($n-1), 2) : $W/2, round($H - ($v/$mx)*($H-12), 2) ]; }
  $d = '';
  foreach ($P as $i => $p) {
    if ($i === 0) { $d .= "M{$p[0]},{$p[1]}"; }
    else { $q = $P[$i-1]; $cx = round(($q[0]+$p[0])/2, 2); $d .= " C{$cx},{$q[1]} {$cx},{$p[1]} {$p[0]},{$p[1]}"; }
  }
  $area = $n ? $d . " L{$P[$n-1][0]},{$H} L{$P[0][0]},{$H} Z" : '';
@endphp
<div class="absolute inset-0 flex flex-col">
  <div class="relative flex-1 min-h-0">
    <svg viewBox="0 0 600 200" preserveAspectRatio="none" class="absolute inset-0 w-full h-full overflow-visible sv-draw">
      <defs><linearGradient id="sg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FF8512" stop-opacity=".25"/><stop offset="1" stop-color="#FF8512" stop-opacity="0"/></linearGradient></defs>
      @foreach([0,50,100,150,200] as $gy)
        <line x1="0" x2="600" y1="{{ $gy }}" y2="{{ $gy }}" stroke="#F0EDE8" stroke-width="1" vector-effect="non-scaling-stroke"/>
      @endforeach
      <path d="{{ $area }}" fill="url(#sg)"/>
      <path d="{{ $d }}" fill="none" stroke="#FF8512" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
    </svg>
    <span class="absolute top-0 left-0 text-[10px] text-ink-400 tabular-nums">&#8369;{{ number_format($mx, 0) }}</span>
<div id="sOv" class="absolute inset-0 cursor-pointer select-none" style="touch-action:pan-y;-webkit-tap-highlight-color:transparent">
  <div id="sLine" style="position:absolute;top:0;bottom:0;width:2px;margin-left:-1px;background:#FF8512;opacity:0;transition:left .2s ease,opacity .2s"></div>
  <div id="sDot" style="position:absolute;width:12px;height:12px;margin:-6px 0 0 -6px;border-radius:9999px;background:#fff;border:3px solid #FF8512;opacity:0;transition:left .2s ease,top .2s ease,opacity .2s"></div>
  <div id="sTip" style="position:absolute;top:0;opacity:0;pointer-events:none;transition:left .2s ease,opacity .2s;background:#1f1a14;color:#fff;font-size:11px;line-height:1.3;padding:6px 10px;border-radius:8px;white-space:nowrap;z-index:5;box-shadow:0 4px 12px rgba(0,0,0,.2)"></div>
</div>
  </div>
  <div class="flex justify-between pt-2 text-[10px] text-ink-500">
    @foreach($sl as $l)<span>{{ $l }}</span>@endforeach
  </div>
</div>
            </div>
        </div>

        {{-- Cash flow breakdown --}}
        <div id="pnFlow" class="tab-off card p-4 md:p-6 flex flex-col w-full min-w-0">
            <div class="mb-3 shrink-0">
                <h3 class="text-sm md:text-base font-bold text-ink-900">Cash Flow</h3>
                <p class="text-[10px] md:text-xs text-ink-500 mt-0.5">Where the collected cash goes</p>
            </div>

            <div class="flex flex-col md:flex-col items-center gap-4 md:gap-6 flex-1 justify-center">
                
                <div class="relative shrink-0" style="width: 180px; height: 180px;">
                    
                    {{-- GIBUTANGAN NAKOG EXPLICIT WIDTH UG HEIGHT PARA MA-FORCE DRAW --}}
                    @php
  $segs = [['#EF4444', (float)$chartExpenses], ['#FBBF24', (float)$chartPurchases], ['#10B981', (float)$chartProfit]];
  $tot = array_sum(array_column($segs, 1));
  $rings = []; $off = 0;
  if ($tot > 0) {
    foreach ($segs as $s) {
      if ($s[1] <= 0) continue;
      $pct = $s[1] / $tot * 100;
      $dash = max($pct - 1, 0.01);
      $rings[] = [$s[0], round($dash, 3), round(100 - $dash, 3), round(-$off, 3)];
      $off += $pct;
    }
  }
@endphp
<svg viewBox="0 0 42 42" class="absolute inset-0 w-full h-full sv-pop">
  <g transform="rotate(-90 21 21)">
    @if(count($rings))
      @foreach($rings as $r)
        <circle cx="21" cy="21" r="15.9155" fill="none" stroke="{{ $r[0] }}" stroke-width="4.5" stroke-linecap="round" stroke-dasharray="{{ $r[1] }} {{ $r[2] }}" stroke-dashoffset="{{ $r[3] }}" class="dn-seg" style="cursor:pointer;transition:stroke-width .2s ease,opacity .2s ease"/>
      @endforeach
    @else
      <circle cx="21" cy="21" r="15.9155" fill="none" stroke="#EFEBE5" stroke-width="4.5"/>
    @endif
  </g>
</svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center z-10">
                        <span class="text-[9px] md:text-[10px] font-bold text-ink-400 uppercase tracking-wider">Collected</span>
                        <span class="text-lg md:text-2xl font-extrabold text-ink-900 tabular-nums tracking-tight">&#8369;{{ number_format($collected, 0) }}</span>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="w-full grid grid-cols-2 md:grid-cols-1 gap-2 md:gap-0 md:divide-y md:divide-ink-100 text-xs md:text-sm">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center bg-ink-50 md:bg-transparent p-2 md:p-2.5 rounded-lg md:rounded-none">
                        <span class="flex items-center gap-1.5 text-ink-600 mb-1 md:mb-0"><span class="w-2 h-2 rounded-full bg-red-500"></span>Expenses</span>
                        <span class="font-bold text-ink-900 tabular-nums">&#8369;{{ number_format($chartExpenses, 0) }}</span>
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center bg-ink-50 md:bg-transparent p-2 md:p-2.5 rounded-lg md:rounded-none">
                        <span class="flex items-center gap-1.5 text-ink-600 mb-1 md:mb-0"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Purchases</span>
                        <span class="font-bold text-ink-900 tabular-nums">&#8369;{{ number_format($chartPurchases, 0) }}</span>
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center bg-emerald-50 md:bg-transparent p-2 md:p-2.5 rounded-lg md:rounded-none col-span-2 md:col-span-1">
                        <span class="flex items-center gap-1.5 font-semibold text-emerald-700 mb-1 md:mb-0"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Cash Profit</span>
                        <span class="font-extrabold text-emerald-700 tabular-nums">&#8369;{{ number_format($chartProfit, 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-[10px] md:text-xs bg-amber-50 border border-amber-100 text-amber-800 shrink-0">
                <span class="flex items-center gap-1.5 font-semibold"><i class="fas fa-clock"></i>Pending</span>
                <span class="font-extrabold tabular-nums">&#8369;{{ number_format($outstanding, 0) }}</span>
            </div>
        </div>
    </section>

    {{-- ══ 3. RECENT ACTIVITY ══ --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <div class="card p-4 md:p-5">
            <div class="flex items-center justify-between gap-3 mb-2">
                <h3 class="text-sm md:text-base font-bold text-ink-900">Recent Deliveries</h3>
                <a href="{{ route('deliveries.index') }}" class="text-[10px] md:text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">View all</a>
            </div>
            @forelse($recentDeliveries as $d)
                @php $name = $d->store->store_name ?? 'Unknown'; @endphp
                <div class="flex items-center gap-3 py-2 md:py-2.5 {{ $loop->first ? '' : 'border-t border-ink-100' }}">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] md:text-xs flex-shrink-0">{{ strtoupper(substr($name, 0, 1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] md:text-[13px] font-semibold text-ink-900 truncate">{{ $name }}</div>
                        <div class="text-[9px] md:text-[10px] text-ink-500">{{ \Carbon\Carbon::parse($d->delivery_date)->format('M d, Y') }}</div>
                    </div>
                    <div class="text-[11px] md:text-[13px] font-bold text-ink-900 tabular-nums">&#8369;{{ number_format($d->total_amount, 2) }}</div>
                </div>
            @empty
                <div class="py-6 text-center text-[10px] md:text-xs text-ink-500">No deliveries yet.</div>
            @endforelse
        </div>

        <div class="card p-4 md:p-5">
            <div class="flex items-center justify-between gap-3 mb-2">
                <h3 class="text-sm md:text-base font-bold text-ink-900">Recent Expenses</h3>
                <a href="{{ route('expenses.index') }}" class="text-[10px] md:text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">View all</a>
            </div>
            @forelse($recentExpenses as $e)
                <div class="flex items-center gap-3 py-2 md:py-2.5 {{ $loop->first ? '' : 'border-t border-ink-100' }}">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center flex-shrink-0"><i class="fas fa-receipt text-[10px] md:text-[11px]"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] md:text-[13px] font-semibold text-ink-900 truncate">{{ $e->description ?? $e->category ?? 'Expense' }}</div>
                        <div class="text-[9px] md:text-[10px] text-ink-500">{{ \Carbon\Carbon::parse($e->expense_date ?? $e->created_at)->format('M d, Y') }}</div>
                    </div>
                    <div class="text-[11px] md:text-[13px] font-bold text-red-600 tabular-nums">-&#8369;{{ number_format($e->amount, 2) }}</div>
                </div>
            @empty
                <div class="py-6 text-center text-[10px] md:text-xs text-ink-500">No expenses yet.</div>
            @endforelse
        </div>
    </section>
</div>
<style>
@@keyframes svReveal{from{clip-path:inset(-10px 100% -10px 0)}to{clip-path:inset(-10px -10px -10px 0)}}
@@keyframes svPop{from{opacity:0;transform:rotate(-90deg) scale(.7)}to{opacity:1;transform:none}}
.sv-draw{animation:svReveal 1.2s ease-out both}
.sv-pop{animation:svPop .9s cubic-bezier(.2,.8,.2,1) both}
@@media (prefers-reduced-motion:reduce){.sv-draw,.sv-pop{animation:none}}
</style>
@php
  $sx = collect($P)->map(fn($p) => round($p[0] / 6, 2))->values();
  $sy = collect($P)->map(fn($p) => round($p[1] / 2, 2))->values();
@endphp
<script>
(function () {
  var L = @json($sl), V = @json($sv), X = @json($sx), Y = @json($sy), n = L.length;
  var ov = document.getElementById('sOv'), line = document.getElementById('sLine'),
      dot = document.getElementById('sDot'), tip = document.getElementById('sTip');
  if (!ov || !n) return;
  var down = false;
  function money(v) { return '\u20B1' + Math.round(v).toLocaleString('en-US'); }
  function show(i) {
    var w = ov.getBoundingClientRect().width;
    line.style.left = X[i] + '%';
    dot.style.left = X[i] + '%'; dot.style.top = Y[i] + '%';
    tip.innerHTML = '<div style="opacity:.7">' + L[i] + '</div><div style="font-weight:800;font-size:13px">' + money(V[i]) + '</div>';
    var tw = tip.offsetWidth, px = X[i] / 100 * w;
    tip.style.left = Math.max(0, Math.min(w - tw, px - tw / 2)) + 'px';
    line.style.opacity = dot.style.opacity = tip.style.opacity = 1;
  }
  function hide() { line.style.opacity = dot.style.opacity = tip.style.opacity = 0; }
  function pick(e) {
    var r = ov.getBoundingClientRect();
    var i = n > 1 ? Math.round((e.clientX - r.left) / r.width * (n - 1)) : 0;
    show(Math.max(0, Math.min(n - 1, i)));
  }
  ov.addEventListener('pointerdown', function (e) { down = true; pick(e); });
  ov.addEventListener('pointermove', function (e) { if (down) pick(e); });
  window.addEventListener('pointerup', function () { down = false; });
  window.addEventListener('pointercancel', function () { down = false; });
  document.addEventListener('pointerdown', function (e) { if (!ov.contains(e.target)) hide(); });
})();
</script>
<script>
(function () {
  var D = {
    '#EF4444': ['Expenses',    {{ (float) $chartExpenses }}],
    '#FBBF24': ['Purchases',   {{ (float) $chartPurchases }}],
    '#10B981': ['Cash profit', {{ (float) $chartProfit }}]
  };
  var tot = 0; for (var k in D) tot += D[k][1];
  var segs = document.querySelectorAll('.dn-seg');
  if (!segs.length) return;
  var box = segs[0].ownerSVGElement.parentElement;
  var tip = document.createElement('div');
  tip.style.cssText = 'position:absolute;left:0;top:0;opacity:0;pointer-events:none;transition:opacity .2s;background:#1f1a14;color:#fff;font-size:11px;line-height:1.3;padding:6px 10px;border-radius:8px;white-space:nowrap;z-index:20;box-shadow:0 4px 12px rgba(0,0,0,.2)';
  box.appendChild(tip);
  function money(v) { return '\u20B1' + Math.round(v).toLocaleString('en-US'); }
  function reset() {
    tip.style.opacity = 0;
    segs.forEach(function (s) { s.setAttribute('stroke-width', '4.5'); s.style.opacity = 1; });
  }
  segs.forEach(function (s) {
    s.addEventListener('click', function (e) {
      e.stopPropagation();
      var col = s.getAttribute('stroke'), d = D[col]; if (!d) return;
      segs.forEach(function (o) {
        var on = (o === s);
        o.setAttribute('stroke-width', on ? '6.5' : '4.5');
        o.style.opacity = on ? 1 : 0.3;
      });
      var pct = tot > 0 ? (d[1] / tot * 100).toFixed(1) : '0.0';
      tip.innerHTML = '<div style="opacity:.7"><span style="display:inline-block;width:8px;height:8px;border-radius:9999px;background:' + col + ';margin-right:5px"></span>' + d[0] + ' \u00B7 ' + pct + '%</div><div style="font-weight:800;font-size:13px">' + money(d[1]) + '</div>';
      var r = box.getBoundingClientRect();
      var x = e.clientX - r.left, y = e.clientY - r.top;
      var tw = tip.offsetWidth, th = tip.offsetHeight;
      var left = Math.max(0, Math.min(r.width - tw, x - tw / 2));
      var top = y - th - 12; if (top < 0) top = y + 14;
      tip.style.left = left + 'px'; tip.style.top = top + 'px';
      tip.style.opacity = 1;
    });
  });
  document.addEventListener('click', reset);
})();
</script>
<style>
.tabbar{display:flex;gap:4px;padding:4px;border-radius:12px;background:#F0EDE8}
.tabbtn{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 10px;border-radius:9px;font-size:12px;font-weight:700;color:#6b6258;background:transparent;border:0;cursor:pointer;transition:background .2s,color .2s,box-shadow .2s;-webkit-tap-highlight-color:transparent}
.tabbtn.is-on{background:#fff;color:#FF6A00;box-shadow:0 1px 4px rgba(0,0,0,.12)}
@@media (min-width:1024px){.tabbar{display:none}}
@@media (max-width:1023px){.tab-off{display:none!important}}
</style>
<script>
(function () {
  var btns = document.querySelectorAll('.tabbtn');
  var panels = { pnSales: document.getElementById('pnSales'), pnFlow: document.getElementById('pnFlow') };
  btns.forEach(function (b) {
    b.addEventListener('click', function () {
      var t = b.getAttribute('data-tab');
      btns.forEach(function (o) { o.classList.toggle('is-on', o === b); });
      for (var k in panels) { if (panels[k]) panels[k].classList.toggle('tab-off', k !== t); }
    });
  });
})();
</script>
@endsection

