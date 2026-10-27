@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Stock levels and inventory value')

@section('content')

@php
    $lowAt    = 5;    // at or below = low stock
    $capacity = 100;  // stock that fills the bar

    $tones = [
        'out' => ['label' => 'Out of stock', 'bar' => '#DC2626', 'badge' => 'bg-red-50 text-red-700',         'dot' => 'bg-red-500'],
        'low' => ['label' => 'Low stock',    'bar' => '#F2B01E', 'badge' => 'bg-amber-50 text-amber-700',     'dot' => 'bg-amber-500'],
        'ok'  => ['label' => 'In stock',     'bar' => '#18975A', 'badge' => 'bg-emerald-50 text-emerald-700', 'dot' => 'bg-emerald-500'],
    ];
    $levelOf = fn($s) => $s <= 0 ? 'out' : ($s <= $lowAt ? 'low' : 'ok');
    $pctOf   = fn($s) => $s <= 0 ? 0 : min(100, max(4, round($s / $capacity * 100)));

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
    // Optional pages: only link to them if the route exists in your app
    $hasShow   = \Illuminate\Support\Facades\Route::has('inventory.show');
    $hasAdjust = \Illuminate\Support\Facades\Route::has('inventory.adjust');
    $link    = fn($s = null) => route('inventory.index', array_filter(['tab' => $activeTab, 'search' => request('search'), 'status' => $s]));
@endphp

<style>
    .iv-kpi { padding:20px; display:flex; flex-direction:column; justify-content:space-between; gap:18px; min-height:128px; transition:border-color .15s, box-shadow .15s; }
    a.iv-kpi:hover { border-color:#FFD6AB; box-shadow:0 8px 20px -12px rgba(232,97,0,.35); }
    .iv-kpi .top { display:flex; align-items:center; justify-content:space-between; font-size:14px; font-weight:500; color:#78716A; }
    .iv-kpi .ico { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; background:#fff; border:1px solid #F0EDE8; color:#78716A; font-size:14px; }
    .iv-kpi .v { font-size:clamp(24px,2.2vw,30px); font-weight:700; letter-spacing:-.02em; line-height:1; font-variant-numeric:tabular-nums; }
    .iv-kpi .s { font-size:12.5px; color:#78716A; margin-top:6px; }
    .iv-kpi.hl { background:#FDE9D5; border-color:#F7D9BC; }
    .iv-kpi.hl .top, .iv-kpi.hl .s { color:#8F5A24; }
    .iv-kpi.hl .ico { background:#E86100; border-color:#E86100; color:#fff; }

    .iv-tab { display:inline-flex; align-items:center; gap:8px; padding:14px 2px; margin-right:24px; margin-bottom:-1px; font-size:14px; font-weight:600; color:#78716A; border-bottom:2px solid transparent; background:none; cursor:pointer; }
    .iv-tab:hover { color:#1A1715; }
    .iv-tab[aria-selected="true"] { color:#E86100; border-bottom-color:#E86100; }
    .iv-tab .count { font-size:12px; font-weight:700; padding:1px 8px; border-radius:999px; background:#F1EEE9; color:#57514B; }
    .iv-tab[aria-selected="true"] .count { background:#FFEBD5; color:#BF4D00; }

    .iv-table th { padding:.7rem 1rem; white-space:nowrap; }
    .iv-table td { padding:.8rem 1rem; vertical-align:middle; }
    .iv-table tbody tr { transition:background .12s; }
    .iv-table tbody tr.sel { background:#FFF6EC; }
    .iv-sort { display:inline-flex; align-items:center; gap:6px; font:inherit; color:inherit; cursor:pointer; background:none; }
    .iv-sort:hover { color:#1A1715; }
    .iv-sort i { font-size:10px; opacity:.45; }
    .iv-sort[data-dir] i { opacity:1; color:#E86100; }
    .iv-check { width:16px; height:16px; accent-color:#E86100; cursor:pointer; }
    .iv-bar { width:112px; height:6px; border-radius:6px; background:#F1EEE9; overflow:hidden; flex:none; }
    .iv-bar i { display:block; height:100%; border-radius:6px; }
    .iv-badge { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px; white-space:nowrap; }
    .iv-icon { width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#78716A; cursor:pointer; transition:background .15s,color .15s; }
    .iv-icon:hover { background:#F1EEE9; color:#1A1715; }
    .iv-icon.add { color:#BF4D00; background:#FFEBD5; } .iv-icon.add:hover { background:#FFD6AB; }
    #rowMenu { position:fixed; z-index:60; min-width:190px; background:#fff; border:1px solid #E8E5E0; border-radius:12px; box-shadow:0 16px 40px -8px rgba(26,23,21,.25); padding:6px; }
    #rowMenu a, #rowMenu button { display:flex; align-items:center; gap:10px; width:100%; padding:9px 12px; border-radius:8px; font-size:14px; font-weight:600; color:#3F3A36; text-align:left; cursor:pointer; background:none; }
    #rowMenu a:hover, #rowMenu button:hover { background:#FFF3E5; color:#BF4D00; }
    #rowMenu i { width:16px; text-align:center; color:#A29B90; font-size:13px; }
    #stockInDialog { padding:0; border:0; border-radius:20px; width:100%; max-width:440px; box-shadow:0 24px 48px -12px rgba(26,23,21,.4); }
    #stockInDialog::backdrop { background:rgba(26,23,21,.5); backdrop-filter:blur(3px); }
    .iv-modal-btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; color:#78716A; }
    .iv-modal-btn:hover { background:#F1EEE9; color:#1A1715; }
</style>

{{-- ══ SUMMARY ══ --}}
<section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6" aria-label="Inventory summary">
    <div class="card iv-kpi hl">
        <div class="top">Inventory value<span class="ico"><i class="fas fa-peso-sign"></i></span></div>
        <div><div class="v">&#8369;{{ number_format($stats['total_value'], 0) }}</div><div class="s">across {{ number_format($total) }} {{ \Illuminate\Support\Str::plural('SKU', $total) }}</div></div>
    </div>
    <a href="{{ $link('in_stock') }}" class="card iv-kpi">
        <div class="top">In stock<span class="ico" style="color:#18975A"><i class="fas fa-circle-check"></i></span></div>
        <div><div class="v">{{ number_format($inStock) }}</div><div class="s">Healthy stock levels</div></div>
    </a>
    <a href="{{ $link('low_stock') }}" class="card iv-kpi">
        <div class="top">Low stock<span class="ico" style="color:#D49A00"><i class="fas fa-triangle-exclamation"></i></span></div>
        <div><div class="v">{{ number_format($low) }}</div><div class="s">At or below {{ $lowAt }} units</div></div>
    </a>
    <a href="{{ $link('out_of_stock') }}" class="card iv-kpi">
        <div class="top">Out of stock<span class="ico" style="color:#DC2626"><i class="fas fa-ban"></i></span></div>
        <div><div class="v">{{ number_format($out) }}</div><div class="s">Needs restocking now</div></div>
    </a>
</section>

{{-- ══ TABLE CARD ══ --}}
<section class="card overflow-hidden">

    {{-- Tabs + actions --}}
    <div class="px-5 border-b border-ink-100 flex flex-wrap items-center justify-between gap-x-4">
        <div role="tablist" aria-label="Inventory type" class="flex flex-wrap">
            @foreach($tabs as $key => $tab)
                <button type="button" role="tab" class="iv-tab" id="tabBtn-{{ $key }}" data-tab="{{ $key }}" aria-controls="tab-{{ $key }}" aria-selected="{{ $key === $activeTab ? 'true' : 'false' }}">
                    {{ $tab['label'] }}<span class="count">{{ number_format($tab['rows']->total()) }}</span>
                </button>
            @endforeach
        </div>
        <div class="flex items-center gap-2 py-2">
            <button type="button" id="exportBtn" class="btn btn-secondary"><i class="fas fa-download text-xs"></i>Export CSV</button>
            <button type="button" data-stock-in class="btn btn-primary"><i class="fas fa-plus text-xs"></i>Add stock</button>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('inventory.index') }}" class="px-5 py-3 flex flex-wrap items-center gap-3 border-b border-ink-100">
        <input type="hidden" name="tab" id="currentTabInput" value="{{ $activeTab }}">
        <div class="relative w-full sm:w-72">
            <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400 text-sm pointer-events-none"></i>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search product or SKU" aria-label="Search product or SKU" class="input !pl-10 !rounded-xl !py-2">
        </div>
        <select name="status" aria-label="Filter by status" class="input !w-auto !rounded-xl !py-2 cursor-pointer" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <option value="in_stock" @selected($status === 'in_stock')>In stock</option>
            <option value="low_stock" @selected($status === 'low_stock')>Low stock</option>
            <option value="out_of_stock" @selected($status === 'out_of_stock')>Out of stock</option>
        </select>
        <button type="submit" class="btn btn-secondary !py-2">Search</button>
        @if($filtered)<a href="{{ route('inventory.index', ['tab' => $activeTab]) }}" class="text-sm font-semibold text-ink-500 hover:text-brand-600">Clear filters</a>@endif
    </form>

    {{-- Selection bar --}}
    <div id="selBar" class="hidden px-5 py-2.5 items-center justify-between gap-3 text-sm" style="background:#FFF3E5; border-bottom:1px solid #FFE0BD">
        <span><b id="selCount">0</b> selected</span>
        <span class="flex items-center gap-4">
            <button type="button" id="exportSel" class="font-bold text-brand-700 hover:underline">Export selected</button>
            <button type="button" id="clearSel" class="font-semibold text-ink-500 hover:text-ink-900">Clear</button>
        </span>
    </div>

    {{-- Panels --}}
    @foreach($tabs as $key => $tab)
        <div id="tab-{{ $key }}" role="tabpanel" aria-labelledby="tabBtn-{{ $key }}" @if($key !== $activeTab) hidden @endif>
            @if($tab['rows']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-clean iv-table">
                        <thead>
                            <tr>
                                <th scope="col" class="!pl-5 w-10"><input type="checkbox" class="iv-check" data-all aria-label="Select all rows"></th>
                                <th scope="col"><button type="button" class="iv-sort" data-sort="name" title="Sorts this page">Product <i class="fas fa-sort"></i></button></th>
                                <th scope="col">Variant</th>
                                <th scope="col">Status</th>
                                <th scope="col"><button type="button" class="iv-sort" data-sort="stock" title="Sorts this page">Stock level <i class="fas fa-sort"></i></button></th>
                                <th scope="col" class="w-24 !pr-5"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tab['rows'] as $variant)
                                @php
                                    $lv = $levelOf($variant->stock); $t = $tones[$lv];
                                    $pname = $variant->product->name ?? 'Unknown';
                                    $exportRow = [$pname, $variant->product->sku ?? '', $variant->name, $variant->unit, $variant->stock, $t['label']];
                                @endphp
                                <tr data-name="{{ \Illuminate\Support\Str::lower($pname) }}" data-stock="{{ $variant->stock }}"
                                    data-export="{{ json_encode($exportRow) }}">
                                    <td class="!pl-5"><input type="checkbox" class="iv-check" data-row aria-label="Select {{ $pname }}"></td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                @if($key === 'finished')<i class="fas fa-mug-hot text-xs"></i>@else{{ strtoupper(substr($pname, 0, 1)) }}@endif
                                            </div>
                                            <div class="min-w-0">
                                                @if($hasShow)
                                                    <a href="{{ route('inventory.show', $variant->product_id) }}" class="font-semibold text-ink-900 hover:text-brand-600 transition-colors">{{ $pname }}</a>
                                                @else
                                                    <span class="font-semibold text-ink-900">{{ $pname }}</span>
                                                @endif
                                                <div class="text-xs text-ink-500 font-mono">{{ $variant->product->sku ?? 'No SKU' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-ink-700 whitespace-nowrap">{{ $variant->name }} <span class="text-ink-400">· {{ $variant->unit }}</span></td>
                                    <td><span class="iv-badge {{ $t['badge'] }}"><span class="w-1.5 h-1.5 rounded-full {{ $t['dot'] }}"></span>{{ $t['label'] }}</span></td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="iv-bar" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $capacity }}" aria-valuenow="{{ min($capacity, $variant->stock) }}" aria-label="Stock level"><i style="width:{{ $pctOf($variant->stock) }}%; background:{{ $t['bar'] }}"></i></div>
                                            <span class="font-bold text-ink-900 tabular-nums whitespace-nowrap">{{ number_format($variant->stock) }} <span class="text-xs font-semibold text-ink-500">{{ $variant->unit }}</span></span>
                                        </div>
                                    </td>
                                    <td class="!pr-5">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" class="iv-icon add" data-stock-in="{{ $variant->id }}" title="Add stock" aria-label="Add stock to {{ $pname }}, {{ $variant->name }}"><i class="fas fa-plus text-xs"></i></button>
                                            <button type="button" class="iv-icon" data-menu aria-label="More actions for {{ $pname }}" aria-haspopup="menu"
                                                    data-variant="{{ $variant->id }}"
                                                    data-show="{{ $hasShow ? route('inventory.show', $variant->product_id) : '' }}"
                                                    data-adjust="{{ $hasAdjust ? route('inventory.adjust', $variant->product_id) : '' }}"
                                                    data-edit="{{ route('products.edit', $variant->product_id) }}"><i class="fas fa-ellipsis-vertical"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3 border-t border-ink-100 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-ink-500">Showing <b class="text-ink-800">{{ $tab['rows']->firstItem() }}–{{ $tab['rows']->lastItem() }}</b> of <b class="text-ink-800">{{ number_format($tab['rows']->total()) }}</b></p>
                    @if($tab['rows']->hasPages())
                        <div>{{ $tab['rows']->appends(array_merge(request()->except('page'), ['tab' => $key]))->links() }}</div>
                    @endif
                </div>
            @else
                <div class="text-center py-16 px-4">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl mb-4"><i class="fas {{ $tab['icon'] }}"></i></div>
                    <h3 class="font-bold text-ink-900">{{ $tab['empty'] }}</h3>
                    <p class="text-sm text-ink-500 mt-1">{{ $filtered ? 'Nothing matches your filters.' : $tab['hint'] }}</p>
                    @if($filtered)<a href="{{ route('inventory.index', ['tab' => $key]) }}" class="btn btn-secondary mt-4">Clear filters</a>@endif
                </div>
            @endif
        </div>
    @endforeach
</section>

{{-- Row actions menu (one shared menu, positioned next to the clicked button) --}}
<div id="rowMenu" class="hidden" role="menu">
    <button type="button" id="mAdd" role="menuitem"><i class="fas fa-plus"></i>Add stock</button>
    <a href="#" id="mShow" role="menuitem"><i class="fas fa-clock-rotate-left"></i>Stock card</a>
    <a href="#" id="mAdjust" role="menuitem"><i class="fas fa-sliders"></i>Adjust stock</a>
    <a href="#" id="mEdit" role="menuitem"><i class="fas fa-pen"></i>Edit product</a>
</div>

{{-- ══ ADD STOCK DIALOG ══ --}}
<dialog id="stockInDialog" aria-labelledby="stockInTitle">
    <form action="{{ route('inventory.stock-in') }}" method="POST">
        @csrf
        <div class="px-6 pt-6 pb-2 flex items-start justify-between gap-4">
            <div>
                <h3 id="stockInTitle" class="text-lg font-bold text-ink-900">Add stock</h3>
                <p class="text-sm text-ink-500 mt-0.5">Increase the on-hand quantity of a variant.</p>
            </div>
            <button type="button" data-close class="iv-modal-btn -mr-2 -mt-1" aria-label="Close"><i class="fas fa-xmark"></i></button>
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
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const activePanel = () => $$('[role="tabpanel"]').find(p => !p.hidden);

    // ── Tabs (keeps ?tab= in the URL and the filter form) ──
    const tabs = $$('.iv-tab');
    tabs.forEach(btn => btn.addEventListener('click', () => {
        tabs.forEach(b => {
            const on = b === btn;
            b.setAttribute('aria-selected', on);
            $('#tab-' + b.dataset.tab).hidden = !on;
        });
        $('#currentTabInput').value = btn.dataset.tab;
        const url = new URL(location.href);
        url.searchParams.set('tab', btn.dataset.tab);
        url.searchParams.delete('page');
        history.replaceState(null, '', url);
        refreshSelection();
    }));

    // ── Row selection ──
    const selBar = $('#selBar'), selCount = $('#selCount');
    function refreshSelection() {
        const p = activePanel(); if (!p) return;
        const rows = $$('tbody tr', p);
        const checked = rows.filter(r => $('[data-row]', r)?.checked);
        rows.forEach(r => r.classList.toggle('sel', !!$('[data-row]', r)?.checked));
        const all = $('[data-all]', p);
        if (all) { all.checked = rows.length > 0 && checked.length === rows.length; all.indeterminate = checked.length > 0 && checked.length < rows.length; }
        selCount.textContent = checked.length;
        selBar.classList.toggle('hidden', checked.length === 0);
        selBar.classList.toggle('flex', checked.length > 0);
    }
    document.addEventListener('change', e => {
        if (e.target.matches('[data-all]')) {
            $$('[data-row]', e.target.closest('table')).forEach(c => c.checked = e.target.checked);
            refreshSelection();
        } else if (e.target.matches('[data-row]')) refreshSelection();
    });
    $('#clearSel').addEventListener('click', () => { $$('[data-row], [data-all]', activePanel()).forEach(c => c.checked = false); refreshSelection(); });

    // ── Sort (current page) ──
    $$('.iv-sort').forEach(btn => btn.addEventListener('click', () => {
        const table = btn.closest('table'), body = $('tbody', table), key = btn.dataset.sort;
        const dir = btn.dataset.dir === 'asc' ? 'desc' : 'asc';
        $$('.iv-sort', table).forEach(b => { delete b.dataset.dir; $('i', b).className = 'fas fa-sort'; });
        btn.dataset.dir = dir;
        $('i', btn).className = 'fas fa-sort-' + (dir === 'asc' ? 'up' : 'down');
        const rows = $$('tr', body).sort((a, b) => {
            const x = a.dataset[key], y = b.dataset[key];
            const r = key === 'stock' ? parseFloat(x) - parseFloat(y) : x.localeCompare(y);
            return dir === 'asc' ? r : -r;
        });
        rows.forEach(r => body.appendChild(r));
    }));

    // ── Export CSV (selected rows, or every row on this page) ──
    function exportCsv(onlySelected) {
        const p = activePanel(); if (!p) return;
        let rows = $$('tbody tr', p);
        const picked = rows.filter(r => $('[data-row]', r)?.checked);
        if (onlySelected || picked.length) rows = picked;
        if (!rows.length) return;
        const head = ['Product', 'SKU', 'Variant', 'Unit', 'Stock', 'Status'];
        const esc = v => '"' + String(v ?? '').replace(/"/g, '""') + '"';
        const csv = [head, ...rows.map(r => JSON.parse(r.dataset.export))].map(r => r.map(esc).join(',')).join('\r\n');
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' }));
        a.download = 'inventory-' + new Date().toISOString().slice(0, 10) + '.csv';
        a.click(); URL.revokeObjectURL(a.href);
    }
    $('#exportBtn').addEventListener('click', () => exportCsv(false));
    $('#exportSel').addEventListener('click', () => exportCsv(true));

    // ── Row actions menu ──
    const menu = $('#rowMenu');
    let menuVariant = null;
    const closeMenu = () => menu.classList.add('hidden');
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-menu]');
        if (btn) {
            e.stopPropagation();
            menuVariant = btn.dataset.variant;
            [['#mShow', 'show'], ['#mAdjust', 'adjust'], ['#mEdit', 'edit']].forEach(([sel, k]) => {
                const el = $(sel);
                el.href = btn.dataset[k] || '#';
                el.style.display = btn.dataset[k] ? '' : 'none';
            });
            const r = btn.getBoundingClientRect();
            menu.classList.remove('hidden');
            const w = menu.offsetWidth, h = menu.offsetHeight;
            menu.style.left = Math.max(8, Math.min(innerWidth - w - 8, r.right - w)) + 'px';
            menu.style.top = (r.bottom + h + 8 > innerHeight ? r.top - h - 4 : r.bottom + 4) + 'px';
            return;
        }
        if (!e.target.closest('#rowMenu')) closeMenu();
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
    window.addEventListener('scroll', closeMenu, true);
    window.addEventListener('resize', closeMenu);

    // ── Add stock dialog ──
    const dlg = $('#stockInDialog'), select = $('#stockInVariantSelect'), qty = $('#stockInQty'), preview = $('#stockInPreview');
    function updatePreview() {
        const opt = select.selectedOptions[0];
        if (!opt || !opt.value) { preview.textContent = ''; return; }
        const cur = parseFloat(opt.dataset.stock) || 0, add = parseFloat(qty.value) || 0, unit = opt.dataset.unit || '';
        preview.textContent = add > 0
            ? `Now ${cur} ${unit}. After this: ${Number((cur + add).toFixed(2))} ${unit}.`
            : `Currently ${cur} ${unit} on hand.`;
    }
    function openStockIn(id) {
        select.value = id || ''; qty.value = ''; updatePreview();
        dlg.showModal(); (select.value ? qty : select).focus();
    }
    $('#mAdd').addEventListener('click', () => { closeMenu(); openStockIn(menuVariant); });
    document.addEventListener('click', e => {
        const opener = e.target.closest('[data-stock-in]');
        if (opener) { openStockIn(opener.dataset.stockIn); return; }
        if (e.target.closest('[data-close]') || e.target === dlg) dlg.close();
    });
    select.addEventListener('change', updatePreview);
    qty.addEventListener('input', updatePreview);
})();
</script>
@endpush