@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Stock levels and inventory value')

@section('content')

@php
    $lowAt    = 5;
    $capacity = 100;

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
    $filtered  = request()->filled('search') || request()->filled('status') || request()->filled('start_date') || request()->filled('end_date');

    $total   = max(0, (int) $stats['total_items']);
    $low     = (int) $stats['low_stock'];
    $out     = (int) $stats['out_of_stock'];
    $inStock = max(0, $total - $low - $out);
    $hasShow   = \Illuminate\Support\Facades\Route::has('inventory.show');
    $hasAdjust = \Illuminate\Support\Facades\Route::has('inventory.adjust');
    $link    = fn($s = null) => route('inventory.index', array_filter(['tab' => $activeTab, 'search' => request('search'), 'status' => $s]));
@endphp

<style>
    .iv-kpi { padding:16px; display:flex; flex-direction:column; justify-content:space-between; gap:12px; min-height:110px; transition:border-color .15s, box-shadow .15s; }
    a.iv-kpi:hover { border-color:#FFD6AB; box-shadow:0 8px 20px -12px rgba(232,97,0,.35); }
    .iv-kpi .top { display:flex; align-items:center; justify-content:space-between; font-size:12px; font-weight:500; color:#78716A; }
    .iv-kpi .ico { width:30px; height:30px; border-radius:8px; display:grid; place-items:center; background:#fff; border:1px solid #F0EDE8; color:#78716A; font-size:12px; }
    .iv-kpi .v { font-size:clamp(20px, 2vw, 28px); font-weight:700; letter-spacing:-.02em; line-height:1; font-variant-numeric:tabular-nums; }
    .iv-kpi .s { font-size:11px; color:#78716A; margin-top:4px; }
    .iv-kpi.hl { background:#FDE9D5; border-color:#F7D9BC; }
    .iv-kpi.hl .top, .iv-kpi.hl .s { color:#8F5A24; }
    .iv-kpi.hl .ico { background:#E86100; border-color:#E86100; color:#fff; }

    .iv-badge { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:700; padding:2px 8px; border-radius:999px; white-space:nowrap; }
    .iv-icon { width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:#78716A; cursor:pointer; transition:background .15s,color .15s; }
    .iv-icon:hover { background:#F1EEE9; color:#1A1715; }
    .iv-icon.add { color:#BF4D00; background:#FFEBD5; } .iv-icon.add:hover { background:#FFD6AB; }
    .iv-seg { display:inline-flex; background:#F1EEE9; padding:3px; border-radius:10px; gap:2px; }
    .iv-tab { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:7px; font-size:12px; font-weight:600; color:#78716A; background:transparent; cursor:pointer; }
    .iv-tab:hover { color:#1A1715; }
    .iv-tab[aria-selected="true"] { background:#fff; color:#1A1715; box-shadow:0 1px 3px rgba(26,23,21,.12); }
    .iv-tab .count { font-size:10px; font-weight:700; color:#A29B90; }
    .iv-tab[aria-selected="true"] .count { color:#E86100; }

    .iv-grid { display:grid; gap:10px; grid-template-columns:repeat(2, minmax(0, 1fr)); }
    @media (min-width: 640px) { .iv-grid { gap:16px; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); } }

    .iv-card { background:#FBFAF8; border:1px solid #F0EDE8; border-radius:14px; padding:12px; display:flex; flex-direction:column; transition:border-color .15s, box-shadow .15s, transform .15s; overflow:hidden; }
    @media (min-width: 640px) {
        .iv-card { border-radius:20px; padding:20px; display:grid; grid-template-columns:1fr 18px; gap:16px; }
    }
    .iv-card:hover { border-color:#FFD6AB; box-shadow:0 12px 28px -14px rgba(232,97,0,.35); }
    .iv-tile { width:32px; height:32px; border-radius:8px; display:grid; place-items:center; font-weight:800; font-size:13px; background:#FFEBD5; color:#BF4D00; flex:none; }
    @media (min-width: 640px) { .iv-tile { width:40px; height:40px; border-radius:12px; font-size:15px; } }
    .iv-qty { font-size:22px; font-weight:800; letter-spacing:-.03em; line-height:1; font-variant-numeric:tabular-nums; }
    @media (min-width: 640px) { .iv-qty { font-size:36px; } }
    
    .iv-meter { position:relative; width:18px; height:100%; min-height:150px; background:#F1EEE9; border-radius:9px; overflow:hidden; display:none; }
    @media (min-width: 640px) { .iv-meter { display:block; } }
    .iv-meter .fill { position:absolute; left:0; right:0; bottom:0; border-radius:9px; transition:height .4s ease; }
    .iv-meter .tick { position:absolute; left:0; right:0; height:0; border-top:2px dashed rgba(26,23,21,.35); }
    
    .iv-add { display:inline-flex; align-items:center; justify-content:center; gap:4px; height:32px; border-radius:8px; font-size:11px; font-weight:700; background:#FFEBD5; color:#BF4D00; cursor:pointer; transition:background .15s, color .15s; padding:0 8px; }
    @media (min-width: 640px) { .iv-add { height:40px; border-radius:12px; font-size:14px; gap:8px; padding:0 16px; } }
    .iv-add:hover { background:#E86100; color:#fff; }

    @media (prefers-reduced-motion: reduce) { .iv-meter .fill { transition:none; } }
    #rowMenu { position:fixed; z-index:60; min-width:190px; background:#fff; border:1px solid #E8E5E0; border-radius:12px; box-shadow:0 16px 40px -8px rgba(26,23,21,.25); padding:6px; }
    #rowMenu a, #rowMenu button { display:flex; align-items:center; gap:10px; width:100%; padding:9px 12px; border-radius:8px; font-size:13px; font-weight:600; color:#3F3A36; text-align:left; cursor:pointer; background:none; }
    #rowMenu a:hover, #rowMenu button:hover { background:#FFF3E5; color:#BF4D00; }
    #rowMenu i { width:16px; text-align:center; color:#A29B90; font-size:13px; }
    #stockInDialog { padding:0; border:0; border-radius:24px; width:100%; max-width:480px; overflow:hidden; box-shadow:0 28px 56px -14px rgba(26,23,21,.45); }
    #stockInDialog::backdrop { background:rgba(26,23,21,.5); backdrop-filter:blur(3px); }
    .iv-modal-btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; color:#78716A; }
    .iv-modal-btn:hover { background:#F1EEE9; color:#1A1715; }
    .sk-head { position:relative; overflow:hidden; padding:24px; background:linear-gradient(135deg,#FDE9D5 0%,#FFF3E5 100%); border-bottom:1px solid #F7D9BC; }
    .sk-head::after { content:''; position:absolute; right:-44px; top:-56px; width:170px; height:170px; border-radius:50%; border:28px solid rgba(232,97,0,.08); pointer-events:none; }
    .sk-ico { width:50px; height:50px; border-radius:15px; background:#E86100; color:#fff; display:grid; place-items:center; font-size:21px; box-shadow:0 10px 18px -8px rgba(232,97,0,.7); flex:none; }
    .sk-step { width:20px; height:20px; border-radius:50%; background:#FFEBD5; color:#BF4D00; font-size:11px; font-weight:800; display:inline-grid; place-items:center; }
    .sk-label { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:#3F3A36; margin-bottom:8px; }
    .sk-pick { display:flex; align-items:center; gap:12px; margin-top:10px; padding:12px 14px; border:1px dashed #E0DBD3; border-radius:14px; background:#FBFAF8; transition:all .15s; }
    .sk-pick.on { border-style:solid; border-color:#FFD6AB; background:#FFF8F0; }
    .sk-tile { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; font-weight:800; background:#F1EEE9; color:#A29B90; flex:none; }
    .sk-pick.on .sk-tile { background:#FFEBD5; color:#BF4D00; }
    .sk-stepper { display:flex; align-items:stretch; border:1px solid #E8E5E0; border-radius:14px; overflow:hidden; background:#fff; transition:border-color .15s, box-shadow .15s; }
    .sk-stepper:focus-within { border-color:#FF8512; box-shadow:0 0 0 3px rgba(255,133,18,.18); }
    .sk-stepper > button { width:54px; font-size:15px; color:#57514B; background:#FAF9F7; cursor:pointer; transition:background .15s,color .15s; }
    .sk-stepper > button:hover { background:#FFEBD5; color:#BF4D00; }
    .sk-stepper input { flex:1; min-width:0; border:0; outline:none; text-align:center; font-size:28px; font-weight:800; letter-spacing:-.02em; padding:12px 4px; font-variant-numeric:tabular-nums; -moz-appearance:textfield; background:transparent; }
    .sk-stepper input::-webkit-outer-spin-button, .sk-stepper input::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
    .sk-unit { align-self:center; padding-right:10px; font-size:13px; font-weight:700; color:#A29B90; min-width:34px; }
    .sk-chip { padding:6px 13px; border-radius:999px; border:1px solid #E8E5E0; background:#fff; font-size:13px; font-weight:700; color:#57514B; cursor:pointer; transition:all .15s; }
    .sk-chip:hover { background:#FFEBD5; border-color:#FFD6AB; color:#BF4D00; }
    .sk-result { border-radius:16px; background:#FBFAF8; border:1px solid #F0EDE8; padding:16px; }
    .sk-track { display:flex; height:10px; border-radius:10px; background:#F1EEE9; overflow:hidden; }
    .sk-track i { display:block; height:100%; transition:width .25s ease; }
    .sk-add { background:repeating-linear-gradient(135deg,#FF8512 0 6px,#FFA24D 6px 12px); }
    @media (prefers-reduced-motion: reduce) { .sk-track i { transition:none; } }
</style>

{{-- ══ SUMMARY ══ --}}
<section class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6" aria-label="Inventory summary">
    <div class="card iv-kpi hl">
        <div class="top">Inventory value<span class="ico"><i class="fas fa-peso-sign"></i></span></div>
        <div><div class="v">&#8369;{{ number_format($stats['total_value'], 0) }}</div><div class="s">across {{ number_format($total) }} SKU</div></div>
    </div>
    <a href="{{ $link('in_stock') }}" class="card iv-kpi">
        <div class="top">In stock<span class="ico" style="color:#18975A"><i class="fas fa-circle-check"></i></span></div>
        <div><div class="v">{{ number_format($inStock) }}</div><div class="s">Healthy levels</div></div>
    </a>
    <a href="{{ $link('low_stock') }}" class="card iv-kpi">
        <div class="top">Low stock<span class="ico" style="color:#D49A00"><i class="fas fa-triangle-exclamation"></i></span></div>
        <div><div class="v">{{ number_format($low) }}</div><div class="s">At or below {{ $lowAt }}</div></div>
    </a>
    <a href="{{ $link('out_of_stock') }}" class="card iv-kpi">
        <div class="top">Out of stock<span class="ico" style="color:#DC2626"><i class="fas fa-ban"></i></span></div>
        <div><div class="v">{{ number_format($out) }}</div><div class="s">Needs restocking</div></div>
    </a>
</section>

{{-- ══ TOOLBAR ══ --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="iv-seg" role="tablist" aria-label="Inventory type">
        @foreach($tabs as $key => $tab)
            <button type="button" role="tab" class="iv-tab" id="tabBtn-{{ $key }}" data-tab="{{ $key }}" aria-controls="tab-{{ $key }}" aria-selected="{{ $key === $activeTab ? 'true' : 'false' }}">
                <i class="fas {{ $tab['icon'] }} text-[11px]"></i>{{ $tab['label'] }}<span class="count">{{ number_format($tab['rows']->total()) }}</span>
            </button>
        @endforeach
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('inventory.movements') }}" class="btn btn-secondary !py-2 !px-3 !text-xs"><i class="fas fa-clock-rotate-left text-[10px]"></i> History</a>
        <button type="button" id="exportBtn" class="btn btn-secondary !py-2 !px-3 !text-xs"><i class="fas fa-download text-[10px]"></i> Export</button>
        <button type="button" data-stock-in class="btn btn-primary !py-2 !px-3 !text-xs"><i class="fas fa-plus text-[10px]"></i> Add stock</button>
    </div>
</div>

<form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col xl:flex-row items-start xl:items-center gap-2 mb-4 md:mb-6">
    <input type="hidden" name="tab" id="currentTabInput" value="{{ $activeTab }}">
    
    <div class="relative w-full sm:w-64">
        <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs pointer-events-none"></i>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search..." aria-label="Search product or SKU" class="input !pl-9 !py-2 !text-xs">
    </div>
    
    <div class="flex items-center gap-1.5 w-full sm:w-auto">
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="input !w-full sm:!w-auto !py-2 !text-xs cursor-pointer" title="Start Date">
        <span class="text-ink-400 text-xs">to</span>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="input !w-full sm:!w-auto !py-2 !text-xs cursor-pointer" title="End Date">
    </div>

    <select name="status" aria-label="Filter by status" class="input !w-full sm:!w-auto !py-2 !text-xs cursor-pointer" onchange="this.form.submit()">
        <option value="">All statuses</option>
        <option value="in_stock" @selected($status === 'in_stock')>In stock</option>
        <option value="low_stock" @selected($status === 'low_stock')>Low stock</option>
        <option value="out_of_stock" @selected($status === 'out_of_stock')>Out of stock</option>
    </select>
    
    <div class="flex items-center gap-1.5 w-full sm:w-auto">
        <button type="submit" class="btn btn-secondary !py-2 !text-xs w-full sm:w-auto">Search</button>
        @if(request()->anyFilled(['search', 'status', 'start_date', 'end_date']))
            <a href="{{ route('inventory.index', ['tab' => $activeTab]) }}" class="btn btn-secondary !py-2 !text-xs w-full sm:w-auto" title="Clear Filter">Clear</a>
        @endif
    </div>
</form>

{{-- ══ CARDS ══ --}}
@foreach($tabs as $key => $tab)
    <div id="tab-{{ $key }}" role="tabpanel" aria-labelledby="tabBtn-{{ $key }}" @if($key !== $activeTab) hidden @endif>
        @if($tab['rows']->count() > 0)
            <div class="iv-grid">
                @foreach($tab['rows'] as $i => $variant)
                    @php
                        $lv = $levelOf($variant->stock); $t = $tones[$lv];
                        $pname = $variant->product->name ?? 'Unknown';
                        $exportRow = [$pname, $variant->product->sku ?? '', $variant->name, $variant->unit, $variant->stock, $t['label']];
                    @endphp
                    <article class="iv-card" data-i="{{ $i }}" data-name="{{ \Illuminate\Support\Str::lower($pname) }}" data-stock="{{ $variant->stock }}" data-export="{{ json_encode($exportRow) }}">
                        <div class="min-w-0 flex flex-col">
                            <div class="flex items-center gap-2">
                                <div class="iv-tile">@if($key === 'finished')<i class="fas fa-mug-hot text-xs"></i>@else{{ strtoupper(substr($pname, 0, 1)) }}@endif</div>
                                <div class="min-w-0 flex-1">
                                    @if($hasShow)
                                        <a href="{{ route('inventory.show', $variant->product_id) }}" class="block font-bold text-ink-900 text-[13px] md:text-sm truncate hover:text-brand-600 transition-colors">{{ $pname }}</a>
                                    @else
                                        <span class="block font-bold text-ink-900 text-[13px] md:text-sm truncate">{{ $pname }}</span>
                                    @endif
                                    <div class="text-[9px] md:text-xs text-ink-500 font-mono truncate">{{ $variant->product->sku ?? 'No SKU' }}</div>
                                </div>
                                <button type="button" class="iv-icon -mr-1" data-menu aria-label="More actions" aria-haspopup="menu"
                                        data-variant="{{ $variant->id }}"
                                        data-show="{{ $hasShow ? route('inventory.show', $variant->product_id) : '' }}"
                                        data-adjust="{{ $hasAdjust ? route('inventory.adjust', $variant->product_id) : '' }}"
                                        data-edit="{{ route('products.edit', $variant->product_id) }}"><i class="fas fa-ellipsis-vertical"></i></button>
                            </div>

                            <div class="mt-3 text-[11px] md:text-sm text-ink-600 truncate">{{ $variant->name }}</div>
                            <div class="mt-0.5 flex items-baseline gap-1">
                                <span class="iv-qty text-ink-900">{{ number_format($variant->stock) }}</span>
                                <span class="text-[10px] md:text-sm font-semibold text-ink-500">{{ $variant->unit }}</span>
                            </div>
                            <span class="iv-badge self-start mt-2 {{ $t['badge'] }}"><span class="w-1.5 h-1.5 rounded-full {{ $t['dot'] }}"></span>{{ $t['label'] }}</span>

                            <div class="mt-auto pt-3 md:pt-5">
                                <button type="button" data-stock-in="{{ $variant->id }}" class="iv-add w-full" aria-label="Add stock"><i class="fas fa-plus text-[10px]"></i>Add stock</button>
                            </div>
                        </div>

                        <div class="iv-meter" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $capacity }}" aria-valuenow="{{ min($capacity, $variant->stock) }}" aria-label="Stock level" title="Low-stock line at {{ $lowAt }}">
                            <div class="fill" style="height:{{ $pctOf($variant->stock) }}%; background:{{ $t['bar'] }}"></div>
                            <div class="tick" style="bottom:{{ $lowAt / $capacity * 100 }}%"></div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-[11px] md:text-sm text-ink-500">Showing <b class="text-ink-800">{{ $tab['rows']->firstItem() }}–{{ $tab['rows']->lastItem() }}</b> of <b class="text-ink-800">{{ number_format($tab['rows']->total()) }}</b></p>
                @if($tab['rows']->hasPages())
                    <div>{{ $tab['rows']->appends(array_merge(request()->except('page'), ['tab' => $key]))->links() }}</div>
                @endif
            </div>
        @else
            <div class="card text-center py-16 px-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl mb-4"><i class="fas {{ $tab['icon'] }}"></i></div>
                <h3 class="font-bold text-ink-900">{{ $tab['empty'] }}</h3>
                <p class="text-xs text-ink-500 mt-1">{{ $filtered ? 'Nothing matches your filters.' : $tab['hint'] }}</p>
                @if($filtered)<a href="{{ route('inventory.index', ['tab' => $key]) }}" class="btn btn-secondary mt-4">Clear filters</a>@endif
            </div>
        @endif
    </div>
@endforeach

{{-- Row actions menu --}}
<div id="rowMenu" class="hidden" role="menu">
    <button type="button" id="mAdd" role="menuitem"><i class="fas fa-plus"></i>Add stock</button>
    <a href="#" id="mShow" role="menuitem"><i class="fas fa-clock-rotate-left"></i>Stock card</a>
    <a href="#" id="mAdjust" role="menuitem"><i class="fas fa-sliders"></i>Adjust stock</a>
    <a href="#" id="mEdit" role="menuitem"><i class="fas fa-pen"></i>Edit product</a>
</div>

{{-- ADD STOCK DIALOG --}}
<dialog id="stockInDialog" aria-labelledby="stockInTitle">
    <form action="{{ route('inventory.stock-in') }}" method="POST">
        @csrf
        <div class="sk-head flex items-center gap-4">
            <div class="sk-ico"><i class="fas fa-boxes-packing"></i></div>
            <div class="relative min-w-0 flex-1">
                <h3 id="stockInTitle" class="text-xl font-extrabold text-ink-900 tracking-tight">Add stock</h3>
                <p class="text-sm mt-0.5" style="color:#8F5A24">Pick a variant, then say how many came in.</p>
            </div>
            <button type="button" data-close class="iv-modal-btn relative self-start -mr-2 -mt-2" style="background:rgba(255,255,255,.7)" aria-label="Close"><i class="fas fa-xmark"></i></button>
        </div>

        <div class="px-6 pt-5 pb-2 space-y-6">
            <div>
                <label for="stockInVariantSelect" class="sk-label"><span class="sk-step">1</span>Product variant</label>
                <select name="variant_id" id="stockInVariantSelect" required class="input !rounded-xl !py-2.5 cursor-pointer">
                    <option value="">Choose a variant</option>
                    @foreach($allVariantsList as $v)
                        <option value="{{ $v->id }}" data-stock="{{ $v->stock }}" data-unit="{{ $v->unit }}" data-name="{{ $v->product->name ?? 'Product' }}" data-variant="{{ $v->name }}">{{ $v->product->name ?? 'Product' }} · {{ $v->name }}</option>
                    @endforeach
                </select>
                <div class="sk-pick" id="skPick">
                    <div class="sk-tile" id="skTile"><i class="fas fa-box text-sm"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-ink-900 truncate" id="skName">No variant selected</div>
                        <div class="text-xs text-ink-500 truncate" id="skMeta">Choose one above to see its stock.</div>
                    </div>
                    <span id="skBadge" class="hidden"></span>
                </div>
            </div>

            <div>
                <label for="stockInQty" class="sk-label"><span class="sk-step">2</span>Quantity to add</label>
                <div class="sk-stepper">
                    <button type="button" id="skMinus" aria-label="Decrease quantity"><i class="fas fa-minus"></i></button>
                    <input type="number" name="quantity" id="stockInQty" step="0.01" min="0.01" required placeholder="0" inputmode="decimal">
                    <span class="sk-unit" id="skUnit"></span>
                    <button type="button" id="skPlus" aria-label="Increase quantity"><i class="fas fa-plus"></i></button>
                </div>
                <div class="flex flex-wrap gap-2 mt-3" aria-label="Quick add">
                    @foreach([1, 5, 10, 50, 100] as $n)
                        <button type="button" class="sk-chip" data-add="{{ $n }}">+{{ $n }}</button>
                    @endforeach
                </div>
            </div>

            <div class="sk-result" aria-live="polite">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold text-ink-500">Current</div>
                        <div class="text-xl font-bold text-ink-700 tabular-nums" id="skCur">—</div>
                    </div>
                    <i class="fas fa-arrow-right text-ink-300"></i>
                    <div class="text-right">
                        <div class="text-xs font-semibold text-ink-500">After adding</div>
                        <div class="text-2xl font-extrabold tabular-nums" style="color:#E86100" id="skAfter">—</div>
                    </div>
                </div>
                <div class="sk-track mt-4" aria-hidden="true"><i id="skBar1" style="width:0"></i><i id="skBar2" class="sk-add" style="width:0"></i></div>
                <p class="text-xs text-ink-500 mt-2" id="skHint">Choose a variant to see the new total.</p>
            </div>
        </div>

        <div class="px-6 py-4 mt-4 bg-ink-50 border-t border-ink-100 flex items-center justify-end gap-3">
            <button type="button" data-close class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary" id="skSave"><i class="fas fa-check text-xs"></i>Save stock</button>
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
    }));

    $('#sortBy')?.addEventListener('change', e => {
        const mode = e.target.value;
        $$('.iv-grid').forEach(grid => {
            $$('.iv-card', grid).sort((a, b) => {
                if (mode === 'name') return a.dataset.name.localeCompare(b.dataset.name);
                if (mode === 'stock-asc') return a.dataset.stock - b.dataset.stock;
                if (mode === 'stock-desc') return b.dataset.stock - a.dataset.stock;
                return a.dataset.i - b.dataset.i;
            }).forEach(c => grid.appendChild(c));
        });
    });

    $('#exportBtn').addEventListener('click', () => {
        const p = activePanel(); if (!p) return;
        const cards = $$('.iv-card', p);
        if (!cards.length) return;
        const head = ['Product', 'SKU', 'Variant', 'Unit', 'Stock', 'Status'];
        const esc = v => '"' + String(v ?? '').replace(/"/g, '""') + '"';
        const csv = [head, ...cards.map(c => JSON.parse(c.dataset.export))].map(r => r.map(esc).join(',')).join('\r\n');
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' }));
        a.download = 'inventory-' + new Date().toISOString().slice(0, 10) + '.csv';
        a.click(); URL.revokeObjectURL(a.href);
    });

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

    const dlg = $('#stockInDialog'), select = $('#stockInVariantSelect'), qty = $('#stockInQty');
    const lowAt = {{ $lowAt }}, cap = {{ $capacity }};
    const skTones = {
        out: ['Out of stock', 'bg-red-50 text-red-700', '#DC2626'],
        low: ['Low stock', 'bg-amber-50 text-amber-700', '#F2B01E'],
        ok:  ['In stock', 'bg-emerald-50 text-emerald-700', '#18975A'],
    };
    const fmt = n => Number(n.toFixed(2)).toLocaleString();

    function updatePreview() {
        const opt = select.selectedOptions[0], has = !!(opt && opt.value);
        const cur = has ? (parseFloat(opt.dataset.stock) || 0) : 0;
        const add = Math.max(0, parseFloat(qty.value) || 0);
        const unit = has ? (opt.dataset.unit || '') : '';
        const pick = $('#skPick'), badge = $('#skBadge');

        pick.classList.toggle('on', has);
        $('#skUnit').textContent = unit;
        $('#skTile').innerHTML = has ? (opt.dataset.name || 'P').charAt(0).toUpperCase() : '<i class="fas fa-box text-sm"></i>';
        $('#skName').textContent = has ? opt.dataset.name : 'No variant selected';
        $('#skMeta').textContent = has ? `${opt.dataset.variant} · ${fmt(cur)} ${unit} on hand` : 'Choose one above to see its stock.';

        if (!has) {
            badge.className = 'hidden';
            $('#skCur').textContent = '—'; $('#skAfter').textContent = '—';
            $('#skBar1').style.width = '0'; $('#skBar2').style.width = '0';
            $('#skHint').textContent = 'Choose a variant to see the new total.';
            $('#skSave').innerHTML = '<i class="fas fa-check text-xs"></i>Save stock';
            return;
        }
        const tone = skTones[cur <= 0 ? 'out' : (cur <= lowAt ? 'low' : 'ok')];
        badge.className = 'iv-badge ' + tone[1]; badge.textContent = tone[0];

        const p1 = Math.min(100, cur / cap * 100), p2 = Math.min(100 - p1, add / cap * 100);
        $('#skBar1').style.width = p1 + '%'; $('#skBar1').style.background = tone[2];
        $('#skBar2').style.width = p2 + '%';
        $('#skCur').textContent = `${fmt(cur)} ${unit}`;
        $('#skAfter').textContent = add > 0 ? `${fmt(cur + add)} ${unit}` : `${fmt(cur)} ${unit}`;
        $('#skHint').textContent = add > 0 ? `+${fmt(add)} ${unit} will be added to the current stock.` : 'Enter a quantity to see the new total.';
        $('#skSave').innerHTML = add > 0 ? `<i class="fas fa-check text-xs"></i>Add ${fmt(add)} ${unit}` : '<i class="fas fa-check text-xs"></i>Save stock';
    }
    function bump(n) {
        const next = Math.max(0, (parseFloat(qty.value) || 0) + n);
        qty.value = next > 0 ? Number(next.toFixed(2)) : '';
        updatePreview();
    }
    function openStockIn(id) {
        select.value = id || ''; qty.value = ''; updatePreview();
        dlg.showModal(); (select.value ? qty : select).focus();
    }
    $('#skMinus').addEventListener('click', () => bump(-1));
    $('#skPlus').addEventListener('click', () => bump(1));
    $$('[data-add]').forEach(b => b.addEventListener('click', () => bump(parseFloat(b.dataset.add))));
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