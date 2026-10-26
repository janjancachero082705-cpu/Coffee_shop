@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Stock levels and inventory value')

@section('content')

@php
    $lowAt    = 5;    // at or below = low stock
    $capacity = 100;  // stock that fills the meter

    $tones = [
        'out' => ['label' => 'Out of stock', 'color' => '#DC2626', 'text' => 'text-red-700'],
        'low' => ['label' => 'Running low',  'color' => '#E39B2B', 'text' => 'text-amber-700'],
        'ok'  => ['label' => 'In stock',     'color' => '#2A7B63', 'text' => 'text-brand-700'],
    ];
    $levelOf = fn($s) => $s <= 0 ? 'out' : ($s <= $lowAt ? 'low' : 'ok');
    $pctOf   = fn($s) => $s <= 0 ? 0 : min(100, max(6, round($s / $capacity * 100)));

    $tabs = [
        'main'     => ['label' => 'Raw materials & supplies', 'icon' => 'fa-boxes-stacked', 'rows' => $mainInventory,
                       'empty' => 'No items found', 'hint' => 'Add a product to start tracking stock.'],
        'finished' => ['label' => 'Finished goods', 'icon' => 'fa-mug-hot', 'rows' => $finishedGoods,
                       'empty' => 'No finished goods yet', 'hint' => 'Process a production batch to add finished goods.'],
    ];
    $activeTab = array_key_exists(request('tab'), $tabs) ? request('tab') : 'main';
    $status    = request('status');
    $filtered  = request()->filled('search') || request()->filled('status');

    $total   = max(0, (int) $stats['total_items']);
    $low     = (int) $stats['low_stock'];
    $out     = (int) $stats['out_of_stock'];
    $inStock = max(0, $total - $low - $out);
    $pct     = fn($n) => $total > 0 ? round($n / $total * 100, 1) : 0;

    $link = fn($s = null) => route('inventory.index', array_filter(['tab' => $activeTab, 'search' => request('search'), 'status' => $s]));
    $chips = ['' => 'All', 'in_stock' => 'In stock', 'low_stock' => 'Low', 'out_of_stock' => 'Out'];
@endphp

<style>
    .inv-hero { background:#0F2C25; color:#fff; border-radius:20px; padding:28px 32px; display:grid; gap:28px; grid-template-columns:1fr; }
    @media (min-width:900px) { .inv-hero { grid-template-columns:auto 1fr; gap:64px; align-items:center; } }
    .inv-hero .k { font-size:14px; font-weight:600; color:#9FC4B7; }
    .inv-hero .v { font-size:clamp(40px,6vw,64px); font-weight:800; line-height:1.05; letter-spacing:-.03em; margin-top:8px; font-variant-numeric:tabular-nums; }
    .health { display:flex; height:14px; gap:3px; border-radius:8px; overflow:hidden; background:rgba(255,255,255,.08); }
    .health i { display:block; height:100%; }
    .legend { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:16px; }
    .legend a { display:block; padding:10px 12px; border-radius:12px; border:1px solid rgba(255,255,255,.12); transition:background .15s; }
    .legend a:hover { background:rgba(255,255,255,.07); }
    .legend .n { font-size:24px; font-weight:800; font-variant-numeric:tabular-nums; }
    .legend .l { display:flex; align-items:center; gap:7px; font-size:13px; color:#B7D3C9; font-weight:600; }
    .legend .l i { width:9px; height:9px; border-radius:3px; }

    .seg { display:inline-flex; background:#EDF0EE; padding:4px; border-radius:12px; gap:2px; }
    .seg button { display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:9px; font-size:14px; font-weight:600; color:#66736D; background:transparent; cursor:pointer; }
    .seg button[aria-selected="true"] { background:#fff; color:#151B19; box-shadow:0 1px 3px rgba(21,27,25,.12); }
    .seg .count { font-size:12px; font-weight:700; color:#8E9B95; }
    .chip { padding:6px 14px; border-radius:999px; font-size:13px; font-weight:600; color:#4B5752; border:1px solid #DEE3E0; background:#fff; transition:all .15s; }
    .chip:hover { border-color:#8E9B95; }
    .chip.on { background:#151B19; border-color:#151B19; color:#fff; }

    .inv-grid { display:grid; gap:16px; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); }
    .inv-card { background:#fff; border:1px solid #E4E8E6; border-radius:18px; padding:20px; display:grid; grid-template-columns:1fr 18px; gap:16px; transition:border-color .15s, box-shadow .15s; }
    .inv-card:hover { border-color:#AED5C6; box-shadow:0 8px 24px -12px rgba(15,44,37,.25); }
    .tile { width:38px; height:38px; border-radius:11px; display:grid; place-items:center; font-weight:800; font-size:15px; background:#EEF6F3; color:#1D5F4C; }
    .qty { font-size:34px; font-weight:800; letter-spacing:-.03em; line-height:1; font-variant-numeric:tabular-nums; }
    .meter { position:relative; width:18px; height:100%; min-height:132px; background:#EDF0EE; border-radius:9px; overflow:hidden; }
    .meter .fill { position:absolute; left:0; right:0; bottom:0; border-radius:9px; transition:height .4s ease; }
    .meter .tick { position:absolute; left:0; right:0; height:0; border-top:2px dashed rgba(21,27,25,.35); }
    .card-act { display:flex; gap:8px; margin-top:16px; }
    .icon-btn { width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; border:1px solid #DEE3E0; color:#66736D; background:#fff; transition:all .15s; }
    .icon-btn:hover { color:#151B19; border-color:#8E9B95; }
    .add-btn { flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px; height:36px; border-radius:10px; font-size:14px; font-weight:700; background:#EEF6F3; color:#184D3E; cursor:pointer; transition:background .15s; }
    .add-btn:hover { background:#D6EAE2; }
    #stockInDialog { padding:0; border:0; border-radius:20px; width:100%; max-width:440px; box-shadow:0 24px 48px -12px rgba(21,27,25,.4); }
    #stockInDialog::backdrop { background:rgba(15,44,37,.55); backdrop-filter:blur(3px); }
    @media (prefers-reduced-motion: reduce) { .meter .fill { transition:none; } }
</style>

{{-- ══ HERO ══ --}}
<section class="inv-hero mb-6" aria-label="Inventory summary">
    <div>
        <div class="k">Inventory value</div>
        <div class="v"><span style="color:#7DB9A5; font-weight:600">&#8369;</span>{{ number_format($stats['total_value'], 0) }}</div>
        <div class="mt-3 text-sm" style="color:#9FC4B7">across {{ number_format($total) }} {{ \Illuminate\Support\Str::plural('SKU', $total) }}</div>
    </div>
    <div>
        <div class="health" role="img" aria-label="{{ $inStock }} in stock, {{ $low }} low, {{ $out }} out of stock">
            <i style="width:{{ $pct($inStock) }}%; background:#4A9980"></i>
            <i style="width:{{ $pct($low) }}%; background:#E39B2B"></i>
            <i style="width:{{ $pct($out) }}%; background:#EF5B4B"></i>
        </div>
        <div class="legend">
            <a href="{{ $link('in_stock') }}"><div class="l"><i style="background:#4A9980"></i>In stock</div><div class="n">{{ number_format($inStock) }}</div></a>
            <a href="{{ $link('low_stock') }}"><div class="l"><i style="background:#E39B2B"></i>Running low</div><div class="n">{{ number_format($low) }}</div></a>
            <a href="{{ $link('out_of_stock') }}"><div class="l"><i style="background:#EF5B4B"></i>Out of stock</div><div class="n">{{ number_format($out) }}</div></a>
        </div>
    </div>
</section>

{{-- ══ TOOLBAR ══ --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="seg" role="tablist" aria-label="Inventory type">
        @foreach($tabs as $key => $tab)
            <button type="button" role="tab" id="tabBtn-{{ $key }}" data-tab="{{ $key }}" aria-controls="tab-{{ $key }}" aria-selected="{{ $key === $activeTab ? 'true' : 'false' }}">
                <i class="fas {{ $tab['icon'] }} text-[13px]"></i>{{ $tab['label'] }}<span class="count">{{ number_format($tab['rows']->total()) }}</span>
            </button>
        @endforeach
    </div>
    <button type="button" data-stock-in class="btn btn-primary"><i class="fas fa-plus text-xs"></i>Add stock</button>
</div>

<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" action="{{ route('inventory.index') }}" class="relative w-full sm:w-80">
        <input type="hidden" name="tab" id="currentTabInput" value="{{ $activeTab }}">
        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400 text-sm pointer-events-none"></i>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search product or SKU" aria-label="Search product or SKU" class="input pl-10 !rounded-xl !py-2.5">
    </form>
    <div class="flex flex-wrap gap-2" aria-label="Filter by status">
        @foreach($chips as $val => $text)
            <a href="{{ $link($val ?: null) }}" class="chip {{ (string) $status === (string) $val ? 'on' : '' }}">{{ $text }}</a>
        @endforeach
    </div>
</div>

{{-- ══ PANELS ══ --}}
@foreach($tabs as $key => $tab)
    <div id="tab-{{ $key }}" role="tabpanel" aria-labelledby="tabBtn-{{ $key }}" @if($key !== $activeTab) hidden @endif>
        @if($tab['rows']->count() > 0)
            <div class="inv-grid">
                @foreach($tab['rows'] as $variant)
                    @php $lv = $levelOf($variant->stock); $t = $tones[$lv]; @endphp
                    <article class="inv-card">
                        <div class="min-w-0 flex flex-col">
                            <div class="flex items-center gap-3">
                                <div class="tile">@if($key === 'finished')<i class="fas fa-mug-hot text-sm"></i>@else{{ strtoupper(substr($variant->product->name ?? 'P', 0, 1)) }}@endif</div>
                                <div class="min-w-0">
                                    <div class="font-bold text-ink-900 truncate">{{ $variant->product->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-ink-500 font-mono truncate">{{ $variant->product->sku ?? 'No SKU' }}</div>
                                </div>
                            </div>

                            <div class="mt-5 text-sm text-ink-600">{{ $variant->name }}</div>
                            <div class="mt-1 flex items-baseline gap-1.5">
                                <span class="qty text-ink-900">{{ number_format($variant->stock) }}</span>
                                <span class="text-sm font-semibold text-ink-500">{{ $variant->unit }}</span>
                            </div>
                            <div class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold {{ $t['text'] }}">
                                <span class="w-2 h-2 rounded-full" style="background:{{ $t['color'] }}"></span>{{ $t['label'] }}
                            </div>

                            <div class="card-act mt-auto pt-4">
                                <button type="button" data-stock-in="{{ $variant->id }}" class="add-btn" aria-label="Add stock to {{ $variant->product->name ?? 'product' }}, {{ $variant->name }}"><i class="fas fa-plus text-xs"></i>Add stock</button>
                                <a href="{{ route('products.edit', $variant->product_id) }}" class="icon-btn" title="Edit product" aria-label="Edit {{ $variant->product->name ?? 'product' }}"><i class="fas fa-pen text-xs"></i></a>
                            </div>
                        </div>

                        <div class="meter" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $capacity }}" aria-valuenow="{{ min($capacity, $variant->stock) }}" aria-label="Stock level" title="Low-stock line at {{ $lowAt }}">
                            <div class="fill" style="height:{{ $pctOf($variant->stock) }}%; background:{{ $t['color'] }}"></div>
                            <div class="tick" style="bottom:{{ $lowAt / $capacity * 100 }}%"></div>
                        </div>
                    </article>
                @endforeach
            </div>
            @if($tab['rows']->hasPages())
                <div class="mt-6">{{ $tab['rows']->appends(array_merge(request()->except('page'), ['tab' => $key]))->links() }}</div>
            @endif
        @else
            <div class="card text-center py-16 px-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-50 text-brand-500 flex items-center justify-center text-xl mb-4"><i class="fas {{ $tab['icon'] }}"></i></div>
                <h3 class="font-bold text-ink-900">{{ $tab['empty'] }}</h3>
                <p class="text-sm text-ink-500 mt-1">{{ $filtered ? 'Nothing matches your filters.' : $tab['hint'] }}</p>
                @if($filtered)<a href="{{ route('inventory.index', ['tab' => $key]) }}" class="btn btn-secondary mt-4">Clear filters</a>@endif
            </div>
        @endif
    </div>
@endforeach

{{-- ══ ADD STOCK DIALOG ══ --}}
<dialog id="stockInDialog" aria-labelledby="stockInTitle">
    <form action="{{ route('inventory.stock-in') }}" method="POST">
        @csrf
        <div class="px-6 pt-6 pb-2 flex items-start justify-between gap-4">
            <div>
                <h3 id="stockInTitle" class="text-lg font-bold text-ink-900">Add stock</h3>
                <p class="text-sm text-ink-500 mt-0.5">Increase the on-hand quantity of a variant.</p>
            </div>
            <button type="button" data-close class="icon-btn !border-0 -mr-2 -mt-1" aria-label="Close"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <label for="stockInVariantSelect" class="block text-sm font-semibold text-ink-800 mb-1.5">Product variant</label>
                <select name="variant_id" id="stockInVariantSelect" required class="input">
                    <option value="">Choose a variant</option>
                    @foreach($allVariantsList as $v)
                        <option value="{{ $v->id }}" data-stock="{{ $v->stock }}" data-unit="{{ $v->unit }}">{{ $v->product->name ?? 'Product' }} · {{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="stockInQty" class="block text-sm font-semibold text-ink-800 mb-1.5">Quantity to add</label>
                <input type="number" name="quantity" id="stockInQty" step="0.01" min="0.01" required class="input" placeholder="e.g. 50" inputmode="decimal">
                <p id="stockInPreview" class="text-sm text-ink-500 mt-2 min-h-[20px]" aria-live="polite"></p>
            </div>
        </div>
        <div class="px-6 py-4 bg-ink-50 border-t border-ink-100 flex justify-end gap-3">
            <button type="button" data-close class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Save stock</button>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
(function () {
    // Tabs: keep ?tab= in the URL and the search form
    const tabs = document.querySelectorAll('.seg [data-tab]');
    const tabInput = document.getElementById('currentTabInput');
    tabs.forEach(btn => btn.addEventListener('click', () => {
        tabs.forEach(b => {
            const on = b === btn;
            b.setAttribute('aria-selected', on);
            document.getElementById('tab-' + b.dataset.tab).hidden = !on;
        });
        tabInput.value = btn.dataset.tab;
        const url = new URL(location.href);
        url.searchParams.set('tab', btn.dataset.tab);
        url.searchParams.delete('page');
        history.replaceState(null, '', url);
    }));

    // Add stock dialog
    const dlg = document.getElementById('stockInDialog');
    const select = document.getElementById('stockInVariantSelect');
    const qty = document.getElementById('stockInQty');
    const preview = document.getElementById('stockInPreview');

    function updatePreview() {
        const opt = select.selectedOptions[0];
        if (!opt || !opt.value) { preview.textContent = ''; return; }
        const cur = parseFloat(opt.dataset.stock) || 0;
        const add = parseFloat(qty.value) || 0;
        const unit = opt.dataset.unit || '';
        preview.textContent = add > 0
            ? `Now ${cur} ${unit}. After this: ${Number((cur + add).toFixed(2))} ${unit}.`
            : `Currently ${cur} ${unit} on hand.`;
    }

    document.addEventListener('click', e => {
        const opener = e.target.closest('[data-stock-in]');
        if (opener) {
            select.value = opener.dataset.stockIn || '';
            qty.value = '';
            updatePreview();
            dlg.showModal();
            (select.value ? qty : select).focus();
            return;
        }
        if (e.target.closest('[data-close]') || e.target === dlg) dlg.close();
    });
    select.addEventListener('change', updatePreview);
    qty.addEventListener('input', updatePreview);
})();
</script>
@endpush