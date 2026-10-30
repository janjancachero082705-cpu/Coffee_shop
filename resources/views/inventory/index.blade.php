@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Stock levels and inventory value')

@section('content')

@php
    $lowAt    = 5;
    $capacity = 100;

    $tones = [
        'out' => ['label' => 'Out of stock', 'bar' => '#DC2626', 'badge' => 'bg-red-50 text-red-700 border-red-200',         'dot' => 'bg-red-500'],
        'low' => ['label' => 'Low stock',    'bar' => '#F2B01E', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200',     'dot' => 'bg-amber-500'],
        'ok'  => ['label' => 'In stock',     'bar' => '#18975A', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
    ];
    $levelOf = fn($s) => $s <= 0 ? 'out' : ($s <= $lowAt ? 'low' : 'ok');
    $pctOf   = fn($s) => $s <= 0 ? 0 : min(100, max(2, round($s / $capacity * 100)));

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
    /* KEYFRAMES PARA SA SMOOTH ANIMATION */
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-down { animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards; }

    /* SUMMARY CARDS */
    .iv-kpi { padding:16px; display:flex; flex-direction:column; justify-content:space-between; gap:12px; min-height:110px; transition:all .2s ease; }
    a.iv-kpi:hover { border-color:#FFD6AB; box-shadow:0 12px 24px -10px rgba(232,97,0,.3); transform:translateY(-2px); }
    .iv-kpi .top { display:flex; align-items:center; justify-content:space-between; font-size:12px; font-weight:600; color:#78716A; }
    .iv-kpi .ico { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; background:#fff; border:1px solid #F0EDE8; color:#78716A; font-size:14px; transition:transform 0.2s; }
    a.iv-kpi:hover .ico { transform:scale(1.1) rotate(5deg); }
    .iv-kpi .v { font-size:clamp(22px, 2vw, 28px); font-weight:800; letter-spacing:-.02em; line-height:1; font-variant-numeric:tabular-nums; }
    .iv-kpi .s { font-size:11px; font-weight:500; color:#78716A; margin-top:4px; }
    .iv-kpi.hl { background:linear-gradient(135deg, #FDE9D5 0%, #FFF7EE 100%); border-color:#F7D9BC; }
    .iv-kpi.hl .top, .iv-kpi.hl .s { color:#8F5A24; }
    .iv-kpi.hl .ico { background:#E86100; border-color:#E86100; color:#fff; box-shadow:0 4px 10px rgba(232,97,0,0.3); }

    /* BADGES & BUTTONS */
    .iv-badge { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:800; padding:3px 8px; border-radius:6px; border:1px solid transparent; white-space:nowrap; text-transform:uppercase; tracking-wider; }
    .iv-icon { width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#78716A; cursor:pointer; transition:all .15s; background:#F3F1EE; }
    .iv-icon:hover { background:#E8E5E0; color:#1A1715; transform:scale(1.05); }
    
    /* TABS */
    .iv-seg { display:inline-flex; background:#F1EEE9; padding:4px; border-radius:12px; gap:4px; border:1px solid #E8E5E0; }
    .iv-tab { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:12px; font-weight:700; color:#78716A; background:transparent; cursor:pointer; transition:all .2s; }
    .iv-tab:hover { color:#1A1715; }
    .iv-tab[aria-selected="true"] { background:#fff; color:#1A1715; box-shadow:0 2px 8px rgba(26,23,21,.08); }
    .iv-tab .count { font-size:10px; font-weight:800; color:#A29B90; background:#E8E5E0; padding:2px 6px; border-radius:6px; }
    .iv-tab[aria-selected="true"] .count { color:#E86100; background:#FFF0E5; }

    /* PARENT CARD (MORPHING) */
    .product-card { 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        grid-column: span 1; 
        will-change: transform, box-shadow;
    }
    .product-card:hover:not(.is-expanded) { 
        transform: translateY(-4px) scale(1.01); 
        box-shadow: 0 12px 30px -10px rgba(0,0,0,0.08); 
        border-color: #D3CEC6;
    }
    .product-card.is-expanded { 
        grid-column: 1 / -1; 
        box-shadow: 0 20px 40px -10px rgba(232,97,0,0.15); 
        border-color: #FF8512; 
        ring: 2px solid #FF8512;
        background: #FFFFFF;
    }
    .product-card.is-expanded .card-header { background: linear-gradient(to right, #FFF7EE, #FFFFFF); }

    /* VARIANT CARDS SA SULOD */
    .variant-card { 
        background:#FFFFFF; 
        border:1px solid #E8E5E0; 
        border-radius:16px; 
        padding:16px; 
        box-shadow:0 4px 12px rgba(0,0,0,0.02); 
        display:flex; 
        flex-direction:column; 
        transition: all 0.2s; 
        position: relative;
        overflow: hidden;
    }
    .variant-card:hover { 
        border-color:#FFD6AB; 
        box-shadow:0 8px 24px -6px rgba(232,97,0,0.15); 
        transform: translateY(-2px);
    }
    
    /* STOCK PROGRESS BAR */
    .stock-bar-container { height: 6px; background: #F1EEE9; border-radius: 999px; overflow: hidden; margin-top: 12px; position:relative; }
    .stock-bar-fill { height: 100%; border-radius: 999px; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); }

    #rowMenu { position:fixed; z-index:9999; min-width:190px; background:#fff; border:1px solid #E8E5E0; border-radius:12px; box-shadow:0 16px 40px -8px rgba(26,23,21,.25); padding:6px; }
    #rowMenu a, #rowMenu button { display:flex; align-items:center; gap:10px; width:100%; padding:9px 12px; border-radius:8px; font-size:13px; font-weight:600; color:#3F3A36; text-align:left; cursor:pointer; background:none; }
    #rowMenu a:hover, #rowMenu button:hover { background:#FFF3E5; color:#BF4D00; }
    
    /* DIALOGS */
    #stockInDialog { padding:0; border:0; border-radius:24px; width:100%; max-width:480px; overflow:hidden; box-shadow:0 28px 56px -14px rgba(26,23,21,.45); }
    #stockInDialog::backdrop { background:rgba(26,23,21,.5); backdrop-filter:blur(3px); }
    .iv-modal-btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; color:#78716A; transition:all .15s; }
    .iv-modal-btn:hover { background:#F1EEE9; color:#1A1715; }
    .sk-head { position:relative; overflow:hidden; padding:24px; background:linear-gradient(135deg,#FDE9D5 0%,#FFF3E5 100%); border-bottom:1px solid #F7D9BC; }
    .sk-ico { width:50px; height:50px; border-radius:15px; background:#E86100; color:#fff; display:grid; place-items:center; font-size:21px; box-shadow:0 10px 18px -8px rgba(232,97,0,.7); flex:none; }
    .sk-stepper { display:flex; align-items:stretch; border:1px solid #E8E5E0; border-radius:14px; overflow:hidden; background:#fff; transition:border-color .15s, box-shadow .15s; }
    .sk-stepper:focus-within { border-color:#FF8512; box-shadow:0 0 0 3px rgba(255,133,18,.18); }
    .sk-stepper > button { width:54px; font-size:15px; color:#57514B; background:#FAF9F7; cursor:pointer; transition:background .15s,color .15s; }
    .sk-stepper > button:hover { background:#FFEBD5; color:#BF4D00; }
    .sk-stepper input { flex:1; min-width:0; border:0; outline:none; text-align:center; font-size:28px; font-weight:800; letter-spacing:-.02em; padding:12px 4px; font-variant-numeric:tabular-nums; -moz-appearance:textfield; background:transparent; }
<style>
    /* Ibalik ang Calendar Icon nga gi-hide sa global appearance:none */
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: block !important;
        opacity: 0.6;
        cursor: pointer;
        padding: 2px;
    }
    input[type="date"] {
        min-width: 0 !important;
        padding-right: 8px !important;
    }
</style>
</style>

{{-- ══ SUMMARY ══ --}}
<section class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4 mb-5" aria-label="Inventory summary">
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
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-5">
    <div class="iv-seg w-full md:w-auto overflow-x-auto" role="tablist" aria-label="Inventory type">
        @foreach($tabs as $key => $tab)
            <button type="button" role="tab" class="iv-tab whitespace-nowrap" id="tabBtn-{{ $key }}" data-tab="{{ $key }}" aria-controls="tab-{{ $key }}" aria-selected="{{ $key === $activeTab ? 'true' : 'false' }}">
                <i class="fas {{ $tab['icon'] }} text-[11px]"></i>{{ $tab['label'] }}<span class="count">{{ number_format($tab['rows']->total()) }}</span>
            </button>
        @endforeach
    </div>
    
    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-between md:justify-end">
        <div class="flex items-center gap-2 flex-1 md:flex-none">
            <a href="{{ route('inventory.movements') }}" class="btn btn-secondary !py-2 !px-3 !text-xs flex-1 md:flex-none justify-center"><i class="fas fa-clock-rotate-left text-[10px]"></i> History</a>
            <button type="button" id="exportBtn" class="btn btn-secondary !py-2 !px-3 !text-xs flex-1 md:flex-none justify-center"><i class="fas fa-download text-[10px]"></i> Export</button>
        </div>
        
        <div class="flex items-center gap-2 flex-1 md:flex-none">
            <div class="flex items-center gap-1 bg-ink-100 p-1 rounded-lg border border-ink-200 shrink-0">
                <button id="cardViewBtn" onclick="setInventoryView('card')" class="w-8 h-8 md:w-7 md:h-7 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors"><i class="fas fa-th-large text-[10px]"></i></button>
                <button id="listViewBtn" onclick="setInventoryView('list')" class="w-8 h-8 md:w-7 md:h-7 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors"><i class="fas fa-list text-[10px]"></i></button>
            </div>
            <button type="button" data-stock-in class="btn btn-primary !py-2 !px-3 !text-xs shadow-md flex-1 md:flex-none justify-center whitespace-nowrap"><i class="fas fa-plus text-[10px]"></i> Add stock</button>
        </div>
    </div>
</div>
</div>

<form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col gap-3 mb-6 bg-white p-3 md:p-4 rounded-xl border border-ink-200 shadow-sm">
    <input type="hidden" name="tab" id="currentTabInput" value="{{ $activeTab }}">
    
    <div class="relative w-full">
        <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs pointer-events-none"></i>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products..." class="input !pl-9 !py-2.5 !text-xs md:!text-sm">
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 w-full">
        <!-- Date Range Row -->
        <div class="flex items-center gap-2 w-full col-span-1 sm:col-span-2 md:col-span-1">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="input !py-2.5 !text-xs md:!text-sm flex-1 cursor-pointer" title="Start Date">
            <span class="text-ink-400 text-[10px] font-bold uppercase shrink-0">to</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input !py-2.5 !text-xs md:!text-sm flex-1 cursor-pointer" title="End Date">
        </div>

        <!-- Status Filter -->
        <select name="status" class="input !py-2.5 !text-xs md:!text-sm cursor-pointer w-full" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <option value="in_stock" @selected($status === 'in_stock')>In stock</option>
            <option value="low_stock" @selected($status === 'low_stock')>Low stock</option>
            <option value="out_of_stock" @selected($status === 'out_of_stock')>Out of stock</option>
        </select>

        <!-- Buttons -->
        <div class="flex items-center gap-2 w-full">
            <button type="submit" class="btn btn-primary !py-2.5 !text-xs md:!text-sm flex-1">Search</button>
            @if(request()->anyFilled(['search', 'status', 'start_date', 'end_date']))
                <a href="{{ route('inventory.index', ['tab' => $activeTab]) }}" class="btn btn-secondary !py-2.5 !text-xs md:!text-sm bg-red-50 text-red-600 border-red-200 hover:bg-red-100 flex-1" title="Clear Filter"><i class="fas fa-times"></i> Clear</a>
            @endif
        </div>
    </div>
</form>

{{-- ══ PARENT CARDS WITH MORPHING EXPANSION ══ --}}
@foreach($tabs as $key => $tab)
    <div id="tab-{{ $key }}" role="tabpanel" aria-labelledby="tabBtn-{{ $key }}" @if($key !== $activeTab) hidden @endif>
        @if($tab['rows']->count() > 0)
            @php $groupedVariants = $tab['rows']->getCollection()->groupBy('product_id'); @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 card-view-content" id="cardView-{{ $key }}">
                @foreach($groupedVariants as $productId => $variants)
                    @php 
                        $product = $variants->first()->product; 
                        $totalStock = $variants->sum('stock');
                    @endphp
                    
                    {{-- PARENT CARD --}}
                    <div class="product-card bg-white rounded-2xl border border-ink-200 shadow-sm cursor-pointer group" id="card-{{ $productId }}" onclick="toggleCard('{{ $productId }}')">
                        
                        {{-- HEADER --}}
                        <div class="card-header p-5 flex items-center justify-between gap-3 rounded-2xl transition-colors">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-100 to-brand-50 text-brand-600 flex items-center justify-center font-black text-2xl border border-brand-200 shadow-sm shrink-0 group-hover:scale-105 transition-transform">
                                    {{ strtoupper(substr($product->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-extrabold text-ink-900 text-base md:text-lg truncate group-hover:text-brand-600 transition-colors">{{ $product->name ?? 'Unknown Product' }}</h3>
                                    <p class="text-[11px] md:text-xs text-ink-500 font-mono mt-0.5 truncate bg-ink-100 inline-block px-1.5 py-0.5 rounded">{{ $product->sku ?? 'No SKU' }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[10px] font-bold text-ink-600 bg-white border border-ink-200 px-2 py-0.5 rounded-md shadow-sm"><i class="fas fa-layer-group text-[9px] mr-1 text-brand-500"></i>{{ $variants->count() }} Variants</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end shrink-0">
                                <span class="text-[9px] font-extrabold text-ink-400 uppercase tracking-widest mb-0.5">Total Stock</span>
                                <span class="text-xl md:text-2xl font-black text-ink-900">{{ number_format($totalStock) }}</span>
                                <span class="text-[11px] font-bold text-brand-600 mt-2 flex items-center gap-1 group-hover:underline bg-brand-50 px-2 py-1 rounded-md" id="toggle-text-{{ $productId }}">
                                    View <i class="fas fa-chevron-down transition-transform duration-300" id="icon-{{ $productId }}"></i>
                                </span>
                            </div>
                        </div>

                        {{-- EXPANDED CONTENT --}}
                        <div id="content-{{ $productId }}" class="hidden border-t border-ink-200 bg-[#FDFCFB] rounded-b-2xl">
                            <div class="p-5 md:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 animate-slide-down">
                                @foreach($variants as $i => $variant)
                                    @php
                                        $lv = $levelOf($variant->stock); $t = $tones[$lv];
                                        $pname = $product->name ?? 'Unknown';
                                        $exportRow = [$pname, $product->sku ?? '', $variant->name, $variant->unit, $variant->stock, $t['label']];
                                        $pct = $pctOf($variant->stock);
                                    @endphp
                                    
                                    <article class="variant-card" data-i="{{ $i }}" data-name="{{ \Illuminate\Support\Str::lower($pname) }}" data-stock="{{ $variant->stock }}" data-export="{{ json_encode($exportRow) }}">
                                        
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div class="min-w-0 flex-1">
                                                <div class="text-[11px] font-bold text-ink-400 uppercase tracking-widest mb-0.5">Variant</div>
                                                <div class="text-sm font-extrabold text-ink-900 truncate">{{ $variant->name }}</div>
                                            </div>
                                            <button type="button" class="iv-icon -mr-1 -mt-1" data-menu aria-label="More actions" aria-haspopup="menu"
                                                    data-variant="{{ $variant->id }}"
                                                    data-show="{{ $hasShow ? route('inventory.show', $variant->product_id) : '' }}"
                                                    data-adjust="{{ $hasAdjust ? route('inventory.adjust', $variant->product_id) : '' }}"
                                                    data-edit="{{ route('products.edit', $variant->product_id) }}" data-product="{{ $variant->product_id }}"><i class="fas fa-ellipsis-vertical text-[11px]"></i></button>
                                        </div>

                                        <div class="flex items-baseline gap-1.5 mt-2 mb-2">
                                            <span class="text-3xl font-black {{ $lv === 'out' ? 'text-red-600' : ($lv === 'low' ? 'text-amber-600' : 'text-emerald-600') }} tracking-tight">{{ number_format($variant->stock) }}</span>
                                            <span class="text-xs font-bold text-ink-500">{{ $variant->unit }}</span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between mt-auto pt-2">
                                            <span class="iv-badge {{ $t['badge'] }}"><span class="w-1.5 h-1.5 rounded-full {{ $t['dot'] }} animate-pulse"></span>{{ $t['label'] }}</span>
                                            <span class="text-[10px] font-bold text-ink-400">{{ $pct }}%</span>
                                        </div>

                                        {{-- PROGRESS BAR --}}
                                        <div class="stock-bar-container mt-2 mb-4">
                                            <div class="stock-bar-fill" style="width: {{ $pct }}%; background-color: {{ $t['bar'] }};"></div>
                                        </div>

                                        <div class="mt-auto">
                                            <button type="button" data-stock-in="{{ $variant->id }}" class="iv-add w-full !h-9 !text-xs !rounded-lg shadow-sm" aria-label="Add stock"><i class="fas fa-plus text-[10px]"></i>Add stock</button>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- LIST VIEW TABLE (ENHANCED) --}}
            <div class="hidden list-view-content card overflow-hidden bg-white rounded-2xl border border-ink-200 shadow-sm" id="listView-{{ $key }}">
                <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                    <table class="w-full min-w-[800px] text-left border-collapse">
                        <thead>
                            <tr class="bg-ink-50/80 border-b border-ink-200">
                                <th class="py-4 px-6 text-[10px] font-extrabold text-ink-500 uppercase tracking-widest">Product Info</th>
                                <th class="py-4 px-6 text-[10px] font-extrabold text-ink-500 uppercase tracking-widest">Variant</th>
                                <th class="py-4 px-6 text-center text-[10px] font-extrabold text-ink-500 uppercase tracking-widest">Stock Level</th>
                                <th class="py-4 px-6 text-center text-[10px] font-extrabold text-ink-500 uppercase tracking-widest">Status</th>
                                <th class="py-4 px-6 text-right text-[10px] font-extrabold text-ink-500 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink-100 bg-white">
                            @foreach($groupedVariants as $productId => $variants)
                                @php $product = $variants->first()->product; @endphp
                                @foreach($variants as $variant)
                                    @php
                                        $lv = $levelOf($variant->stock); $t = $tones[$lv];
                                        $stockColor = $lv === 'out' ? 'text-red-600' : ($lv === 'low' ? 'text-amber-600' : 'text-emerald-600');
                                    @endphp
                                    <tr class="hover:bg-brand-50/30 transition-colors group">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-black text-lg shrink-0 border border-brand-200">
                                                    {{ strtoupper(substr($product->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-ink-900 text-sm">{{ $product->name ?? 'Unknown' }}</div>
                                                    <div class="text-[10px] text-ink-500 font-mono mt-0.5">{{ $product->sku ?? 'No SKU' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-ink-100 text-ink-700 text-xs font-bold border border-ink-200">
                                                <i class="fas fa-tag text-[10px] text-ink-400"></i> {{ $variant->name }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="text-2xl font-black {{ $stockColor }} tracking-tight">{{ number_format($variant->stock) }}</div>
                                            <div class="text-[10px] font-bold text-ink-400 uppercase tracking-wider">{{ $variant->unit }}</div>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="iv-badge {{ $t['badge'] }} !py-1 !px-2.5 !text-[10px]"><span class="w-1.5 h-1.5 rounded-full {{ $t['dot'] }}"></span>{{ $t['label'] }}</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                                <button type="button" data-stock-in="{{ $variant->id }}" class="h-8 px-3 bg-brand-500 text-white rounded-lg text-[11px] font-bold shadow-sm hover:bg-brand-600 transition-colors flex items-center gap-1.5" title="Add Stock"><i class="fas fa-plus text-[10px]"></i> Add</button>
                                                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-ink-100 text-ink-600 hover:bg-ink-200 transition-colors border border-ink-200" data-menu aria-label="More actions" data-variant="{{ $variant->id }}" data-show="{{ $hasShow ? route('inventory.show', $variant->product_id) : '' }}" data-adjust="{{ $hasAdjust ? route('inventory.adjust', $variant->product_id) : '' }}" data-edit="{{ route('products.edit', $variant->product_id) }}" data-product="{{ $variant->product_id }}"><i class="fas fa-ellipsis-vertical text-[10px]"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <p class="text-[11px] md:text-sm text-ink-500">Showing <b class="text-ink-800">{{ $tab['rows']->firstItem() }}–{{ $tab['rows']->lastItem() }}</b> of <b class="text-ink-800">{{ number_format($tab['rows']->total()) }}</b></p>
                @if($tab['rows']->hasPages())
                    <div>{{ $tab['rows']->appends(array_merge(request()->except('page'), ['tab' => $key]))->links() }}</div>
                @endif
            </div>
        @else
            <div class="card text-center py-16 px-4 bg-ink-50/50 border-dashed border-2">
                <div class="w-16 h-16 mx-auto rounded-full bg-white shadow-sm flex items-center justify-center text-2xl text-ink-300 mb-4"><i class="fas {{ $tab['icon'] }}"></i></div>
                <h3 class="text-lg font-bold text-ink-900">{{ $tab['empty'] }}</h3>
                <p class="text-sm text-ink-500 mt-1">{{ $filtered ? 'Nothing matches your filters.' : $tab['hint'] }}</p>
                @if($filtered)<a href="{{ route('inventory.index', ['tab' => $key]) }}" class="btn btn-secondary mt-4">Clear filters</a>@endif
            </div>
        @endif
    </div>
@endforeach

{{-- Row actions menu --}}
<div id="rowMenu" class="hidden" role="menu">
    <button type="button" id="mAdd" role="menuitem"><i class="fas fa-plus text-brand-500"></i>Add stock</button>
    <a href="#" id="mShow" role="menuitem"><i class="fas fa-clock-rotate-left"></i>Stock card</a>
    <a href="#" id="mAdjust" role="menuitem"><i class="fas fa-sliders"></i>Adjust stock</a>
    <a href="#" id="mEdit" role="menuitem"><i class="fas fa-pen"></i>Edit product</a>
    <button type="button" id="mDelete" role="menuitem" class="!text-red-600 hover:!bg-red-50 border-t border-ink-100 mt-1 pt-2"><i class="fas fa-trash !text-red-500"></i>Delete product</button>
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
            <button type="button" data-close class="iv-modal-btn relative self-start -mr-2 -mt-2 bg-white/50" aria-label="Close"><i class="fas fa-xmark text-lg"></i></button>
        </div>

        <div class="px-6 pt-5 pb-2 space-y-6 max-h-[60vh] overflow-y-auto">
            <div>
                <label for="stockInVariantSelect" class="block text-[11px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">Product variant</label>
                <select name="variant_id" id="stockInVariantSelect" required class="input !rounded-xl !py-3 font-semibold cursor-pointer">
                    <option value="">Choose a variant...</option>
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
                <label for="stockInQty" class="block text-[11px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">Quantity to add</label>
                <div class="sk-stepper">
                    <button type="button" id="skMinus" aria-label="Decrease quantity"><i class="fas fa-minus"></i></button>
                    <input type="number" name="quantity" id="stockInQty" step="0.01" min="0.01" max="999999" maxlength="6" required placeholder="0" inputmode="decimal">
                    <span class="sk-unit" id="skUnit"></span>
                    <button type="button" id="skPlus" aria-label="Increase quantity"><i class="fas fa-plus"></i></button>
                </div>
                <p id="qtyWarning" class="text-[11px] font-bold text-red-500 mt-2 hidden animate-pulse"><i class="fas fa-circle-exclamation mr-1"></i> Limit reached! Maximum is 999,999.</p>
                <div class="flex flex-wrap gap-2 mt-3" aria-label="Quick add">
                    @foreach([1, 5, 10, 50, 100] as $n)
                        <button type="button" class="sk-chip" data-add="{{ $n }}">+{{ $n }}</button>
                    @endforeach
                </div>
            </div>

            <div class="sk-result bg-white shadow-inner" aria-live="polite">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-[10px] font-bold text-ink-400 uppercase tracking-wider">Current</div>
                        <div class="text-xl font-bold text-ink-700 tabular-nums truncate" id="skCur">—</div>
                    </div>
                    <i class="fas fa-arrow-right text-brand-300"></i>
                    <div class="text-right">
                        <div class="text-[10px] font-bold text-ink-400 uppercase tracking-wider">After adding</div>
                        <div class="text-2xl font-extrabold tabular-nums truncate text-brand-600" id="skAfter">—</div>
                    </div>
                </div>
                <div class="sk-track mt-4" aria-hidden="true"><i id="skBar1" style="width:0"></i><i id="skBar2" class="sk-add" style="width:0"></i></div>
                <p class="text-xs text-ink-500 mt-2" id="skHint">Choose a variant to see the new total.</p>
            </div>
        </div>

        <div class="px-6 py-4 mt-4 bg-ink-50 border-t border-ink-100 flex items-center justify-end gap-3">
            <button type="button" data-close class="btn btn-secondary !py-2.5">Cancel</button>
            <button type="submit" class="btn btn-primary !py-2.5 shadow-md" id="skSave"><i class="fas fa-check text-xs"></i>Save stock</button>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
(function () {
    
    // ── VIEW TOGGLE LOGIC ──
    window.setInventoryView = function(view) {
        // I-save sa localStorage para mo-persist bisan mag-refresh
        localStorage.setItem('inventory_view_pref', view);
        
        const cardViews = document.querySelectorAll('.card-view-content');
        const listViews = document.querySelectorAll('.list-view-content');
        const cardBtn = document.getElementById('cardViewBtn');
        const listBtn = document.getElementById('listViewBtn');

        if (view === 'card') {
            cardViews.forEach(el => el.classList.remove('hidden'));
            listViews.forEach(el => el.classList.add('hidden'));
            if(cardBtn) cardBtn.className = 'w-7 h-7 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors';
            if(listBtn) listBtn.className = 'w-7 h-7 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors';
        } else {
            cardViews.forEach(el => el.classList.add('hidden'));
            listViews.forEach(el => el.classList.remove('hidden'));
            if(listBtn) listBtn.className = 'w-7 h-7 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors';
            if(cardBtn) cardBtn.className = 'w-7 h-7 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors';
        }
    };

    // I-check ang preference inig-load sa page
    document.addEventListener('DOMContentLoaded', () => {
        const savedView = localStorage.getItem('inventory_view_pref') || 'card';
        setInventoryView(savedView);
    });
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const activePanel = () => $$('[role="tabpanel"]').find(p => !p.hidden);

    // ── MORPHING CARD EXPANSION LOGIC ──
    window.toggleCard = function(productId) {
        const card = document.getElementById('card-' + productId);
        const content = document.getElementById('content-' + productId);
        const icon = document.getElementById('icon-' + productId);
        const toggleText = document.getElementById('toggle-text-' + productId);
        
        if (card.classList.contains('is-expanded')) {
            // Collapse
            card.classList.remove('is-expanded');
            content.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
            if (toggleText) toggleText.innerHTML = 'View <i class="fas fa-chevron-down transition-transform duration-300" id="icon-' + productId + '"></i>';
        } else {
            // Close all others first
            document.querySelectorAll('.product-card').forEach(c => {
                c.classList.remove('is-expanded');
                const cId = c.id.replace('card-', '');
                const cContent = document.getElementById('content-' + cId);
                const cIcon = document.getElementById('icon-' + cId);
                const cToggle = document.getElementById('toggle-text-' + cId);
                if (cContent) cContent.classList.add('hidden');
                if (cIcon) cIcon.classList.remove('rotate-180');
                if (cToggle) cToggle.innerHTML = 'View <i class="fas fa-chevron-down transition-transform duration-300" id="icon-' + cId + '"></i>';
            });

            // Expand this card
            card.classList.add('is-expanded');
            content.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
            if (toggleText) toggleText.innerHTML = 'Close <i class="fas fa-chevron-up transition-transform duration-300" id="icon-' + productId + '"></i>';
        }
    };

    // ── TABS LOGIC ──
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

    // ── EXPORT LOGIC ──
    $('#exportBtn').addEventListener('click', () => {
        const p = activePanel(); if (!p) return;
        const cards = $$('.variant-card', p);
        if (!cards.length) return;
        const head = ['Product', 'SKU', 'Variant', 'Unit', 'Stock', 'Status'];
        const esc = v => '"' + String(v ?? '').replace(/"/g, '""') + '"';
        const csv = [head, ...cards.map(c => JSON.parse(c.dataset.export))].map(r => r.map(esc).join(',')).join('\r\n');
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' }));
        a.download = 'inventory-' + new Date().toISOString().slice(0, 10) + '.csv';
        a.click(); URL.revokeObjectURL(a.href);
    });

    // ── ROW MENU LOGIC ──
    const menu = $('#rowMenu');
    let menuVariant = null;
    let menuProductId = null;
    const closeMenu = () => menu.classList.add('hidden');
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-menu]');
        if (btn) {
            e.stopPropagation();
            menuVariant = btn.dataset.variant;
            menuProductId = btn.dataset.product;
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
        
    if ($('#mDelete')) {
        $('#mDelete').addEventListener('click', function(e) {
            e.preventDefault();
            closeMenu();
            
            if (!menuProductId) {
                alert('Error: Product ID not found.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/products/${menuProductId}`;
            
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            
            document.body.appendChild(form);
            
            if (typeof window.openConfirmModal === 'function') {
                window.openConfirmModal('Are you sure you want to delete this product? All variants and history will be removed.', form);
            } else {
                if (confirm('Are you sure you want to delete this product?')) {
                    form.submit();
                }
            }
        });
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
    window.addEventListener('scroll', closeMenu, true);
    window.addEventListener('resize', closeMenu);

    // ── STOCK IN DIALOG LOGIC ──
    const dlg = $('#stockInDialog'), select = $('#stockInVariantSelect'), qty = $('#stockInQty');
    const lowAt = {{ $lowAt }}, cap = {{ $capacity }};
    const skTones = {
        out: ['Out of stock', 'bg-red-50 text-red-700 border-red-200', '#DC2626'],
        low: ['Low stock', 'bg-amber-50 text-amber-700 border-amber-200', '#F2B01E'],
        ok:  ['In stock', 'bg-emerald-50 text-emerald-700 border-emerald-200', '#18975A'],
    };
    const fmt = n => Number(n.toFixed(2)).toLocaleString();

    function updatePreview() {
        const opt = select.selectedOptions[0], has = !!(opt && opt.value);
        const cur = has ? (parseFloat(opt.dataset.stock) || 0) : 0;
        let add = Math.max(0, parseFloat(qty.value) || 0);
        if (add > 999999) add = 999999;
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
        let next = Math.max(0, (parseFloat(qty.value) || 0) + n);
        if (next > 999999) {
            next = 999999;
            document.getElementById('qtyWarning').classList.remove('hidden');
        } else {
            document.getElementById('qtyWarning').classList.add('hidden');
        }
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
    qty.addEventListener('input', function() {
        let val = parseFloat(this.value) || 0;
        if (val > 999999) {
            this.value = 999999;
            document.getElementById('qtyWarning').classList.remove('hidden');
        } else {
            document.getElementById('qtyWarning').classList.add('hidden');
        }
        updatePreview();
    });
})();
</script>
@endpush