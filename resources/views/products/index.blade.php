@extends('layouts.admin')
@section('title', 'Products')
@section('subtitle', 'Manage catalog, stocks, and production')

@section('content')

<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">

    {{-- ══════ MODERN TABS ══════ --}}
    <div class="mb-4 md:mb-6 flex gap-1 bg-ink-100 p-1 rounded-xl border border-ink-200 w-full sm:w-max">
        <button onclick="switchTab('catalog')" id="tabBtn-catalog" class="flex-1 sm:flex-none justify-center px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-bold rounded-lg bg-white text-ink-900 shadow-sm border border-ink-200 transition-all flex items-center gap-1.5 sm:gap-2">
            <i class="fas fa-box"></i> Catalog
        </button>
        <button onclick="switchTab('production')" id="tabBtn-production" class="flex-1 sm:flex-none justify-center px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-1.5 sm:gap-2 hover:bg-ink-200/50">
            <i class="fas fa-industry"></i> Production
        </button>
    </div>

    {{-- ══════ TAB 1: CATALOG ══════ --}}
    <div id="tab-catalog" class="space-y-4 md:space-y-6">
        
        {{-- STATS CARDS --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
            <div class="card p-3 md:p-5 border-l-4 border-l-brand-500">
                <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total SKUs</div>
                <div class="text-lg md:text-2xl font-black text-ink-900">{{ number_format($stats['total_products']) }}</div>
            </div>
            <div class="card p-3 md:p-5 border-l-4 border-l-amber-500">
                <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Low Stock</div>
                <div class="text-lg md:text-2xl font-black text-amber-600">{{ number_format($stats['low_stock']) }}</div>
            </div>
            <div class="card p-3 md:p-5 border-l-4 border-l-red-500">
                <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Out of Stock</div>
                <div class="text-lg md:text-2xl font-black text-red-600">{{ number_format($stats['out_of_stock']) }}</div>
            </div>
            <div class="card p-3 md:p-5 border-l-4 border-l-emerald-500">
                <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Inventory Value</div>
                <div class="text-lg md:text-2xl font-black text-ink-900">₱{{ number_format($stats['total_value'], 0) }}</div>
            </div>
        </div>

        {{-- TOOLBAR --}}
        <div class="card p-3 md:p-4 flex flex-col md:flex-row justify-between gap-3">
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 flex-1">
                <input type="hidden" name="tab" value="catalog">
                <div class="relative w-full md:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="input !pl-8 !py-2 !text-xs w-full">
                </div>
                <select name="category" class="input !py-2 !text-xs cursor-pointer w-full sm:w-auto">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->emoji }} {{ $c->name }}</option>@endforeach
                </select>
                <button type="submit" class="btn btn-secondary !py-2 !text-xs w-full sm:w-auto">Filter</button>
            </form>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <div class="hidden sm:flex items-center gap-1 bg-ink-100 p-1 rounded-lg border border-ink-200">
                    <button id="gridViewBtn" onclick="setProductView('grid')" class="w-7 h-7 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors"><i class="fas fa-th-large text-[10px]"></i></button>
                    <button id="listViewBtn" onclick="setProductView('list')" class="w-7 h-7 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors"><i class="fas fa-list text-[10px]"></i></button>
                </div>
                <a href="{{ route('products.create') }}" class="btn btn-primary !py-2 !text-xs whitespace-nowrap w-full md:w-auto">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
        </div>

        {{-- GRID VIEW (2 columns on mobile) --}}
        @if($products->count() > 0)
            <div id="productGridView" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-5">
                @foreach($products as $p)
                    @php $totalStock = $p->variants->sum('stock'); @endphp
                    <div class="bg-white rounded-xl border border-ink-200 shadow-card hover:shadow-pop hover:border-brand-300 transition-all duration-200 flex flex-col group relative overflow-hidden">
                        @if($totalStock <= 0)
                            <div class="absolute top-2 left-2 md:top-3 md:left-3 bg-red-500 text-white text-[8px] md:text-[10px] font-bold px-1.5 md:px-2 py-0.5 md:py-1 rounded-md z-10 shadow-sm">OUT</div>
                        @elseif($totalStock <= 5)
                            <div class="absolute top-2 left-2 md:top-3 md:left-3 bg-amber-500 text-white text-[8px] md:text-[10px] font-bold px-1.5 md:px-2 py-0.5 md:py-1 rounded-md z-10 shadow-sm">LOW</div>
                        @endif
                        
                        {{-- Action Buttons (Edit/Delete) --}}
                        <div class="absolute top-2 right-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <a href="{{ route('products.edit', $p) }}" class="w-6 h-6 md:w-7 md:h-7 bg-white/90 text-blue-600 rounded border border-ink-200 flex items-center justify-center shadow-sm hover:bg-blue-50"><i class="fas fa-pen text-[9px] md:text-[10px]"></i></a>
                            <form action="{{ route('products.destroy', $p) }}" method="POST" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="w-6 h-6 md:w-7 md:h-7 bg-white/90 text-red-600 rounded border border-ink-200 flex items-center justify-center shadow-sm hover:bg-red-50"><i class="fas fa-trash text-[9px] md:text-[10px]"></i></button></form>
                        </div>
                        
                        <a href="{{ route('products.show', $p) }}" class="w-full h-32 md:h-44 bg-ink-50 flex items-center justify-center relative overflow-hidden border-b border-ink-100">
                            @if($p->image)<img src="{{ Storage::url($p->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">@else
                                <span class="text-4xl md:text-6xl font-black text-ink-200 group-hover:scale-110 transition-transform duration-300">{{ strtoupper(substr($p->name, 0, 1)) }}</span>
                            @endif
                        </a>
                        
                        <div class="p-2.5 md:p-4 flex-1 flex flex-col">
                            <div class="text-[8px] md:text-[10px] font-bold text-ink-500 uppercase tracking-wider mb-0.5 md:mb-1 truncate">{{ $p->category->name ?? 'Uncategorized' }}</div>
                            <a href="{{ route('products.show', $p) }}"><h4 class="font-bold text-ink-900 text-[12px] md:text-base leading-tight mb-1 group-hover:text-brand-600 transition-colors truncate">{{ $p->name }}</h4></a>
                            <div class="text-[8px] md:text-[10px] text-ink-500 mb-2 md:mb-3 font-medium flex flex-wrap items-center gap-1 md:gap-1.5"><span>{{ $p->variants->count() }} Variants</span></div>
                            
                            <div class="mt-auto pt-2 md:pt-3 border-t border-ink-100">
                                <a href="{{ route('products.show', $p) }}" class="w-full py-1.5 md:py-2 bg-ink-50 hover:bg-brand-50 text-ink-700 hover:text-brand-700 text-[9px] md:text-[11px] font-bold rounded-lg border border-ink-200 hover:border-brand-200 transition-colors flex items-center justify-center gap-1 md:gap-1.5 group-hover:bg-brand-500 group-hover:text-white group-hover:border-brand-500">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- LIST VIEW --}}
            <div id="productListView" class="hidden card overflow-hidden">
                <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                    <table class="table-clean min-w-[600px]">
                        <thead>
                            <tr>
                                <th>Product Details</th>
                                <th>Category</th>
                                <th class="text-center">Variants</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $p)
                                @php $totalStock = $p->variants->sum('stock'); @endphp
                                <tr class="group">
                                    <td>
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-ink-100 flex items-center justify-center overflow-hidden border border-ink-200 flex-shrink-0">
                                                @if($p->image)<img src="{{ Storage::url($p->image) }}" class="w-full h-full object-cover">@else<span class="font-bold text-ink-500 text-xs">{{ strtoupper(substr($p->name, 0, 1)) }}</span>@endif
                                            </div>
                                            <div>
                                                <a href="{{ route('products.show', $p) }}" class="font-bold text-ink-900 hover:text-brand-600 transition-colors text-xs md:text-sm">{{ $p->name }}</a>
                                                <div class="text-[9px] md:text-[10px] text-ink-500 font-mono mt-0.5">{{ $p->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-ink-600 text-xs md:text-sm">{{ $p->category->emoji ?? '' }} {{ $p->category->name ?? '—' }}</td>
                                    <td class="text-center"><span class="px-2 py-0.5 md:py-1 bg-ink-100 text-ink-700 rounded-md text-[9px] md:text-[10px] font-bold border border-ink-200">{{ $p->variants->count() }} Sizes</span></td>
                                    <td class="text-center">
                                        @if($totalStock <= 0)<span class="px-2 py-0.5 md:py-1 bg-red-50 text-red-700 rounded-md text-[9px] md:text-[10px] font-bold border border-red-200 uppercase tracking-wide">Out</span>
                                        @elseif($totalStock <= 5)<span class="px-2 py-0.5 md:py-1 bg-amber-50 text-amber-700 rounded-md text-[9px] md:text-[10px] font-bold border border-amber-200 uppercase tracking-wide">Low ({{ $totalStock }})</span>
                                        @else<span class="px-2 py-0.5 md:py-1 bg-emerald-50 text-emerald-700 rounded-md text-[9px] md:text-[10px] font-bold border border-emerald-200 uppercase tracking-wide">In</span>@endif
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1 md:gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('products.show', $p) }}" class="w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors shadow-sm"><i class="fas fa-eye text-[10px]"></i></a>
                                            <a href="{{ route('products.edit', $p) }}" class="w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg text-ink-600 bg-ink-100 hover:bg-ink-200 transition-colors shadow-sm"><i class="fas fa-pen text-[10px]"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4 md:mt-6">{{ $products->appends(['tab' => 'catalog'])->links() }}</div>
        @else
            <div class="card text-center py-16 md:py-24">
                <div class="w-16 h-16 md:w-20 md:h-20 mx-auto bg-ink-50 rounded-2xl flex items-center justify-center mb-3 md:mb-4"><i class="fas fa-box-open text-2xl md:text-3xl text-ink-300"></i></div>
                <h3 class="text-lg md:text-xl font-bold text-ink-900 mb-1 md:mb-2">No products found</h3>
                <p class="text-xs md:text-sm text-ink-500 mb-4 md:mb-6">Start by adding your first product to the catalog.</p>
            </div>
        @endif
    </div>

    {{-- ══════ TAB 2: PRODUCTION ══════ --}}
    <div id="tab-production" class="hidden space-y-4 md:space-y-6">
        
        {{-- PRODUCTION FORM --}}
        <div class="card overflow-hidden max-w-4xl mx-auto">
            <div class="px-4 md:px-6 py-4 md:py-5 border-b border-ink-200 bg-ink-50 flex flex-col md:flex-row md:items-center justify-between gap-3 md:gap-4">
                <div class="flex items-center gap-3 md:gap-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-ink-900 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-industry text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-ink-900">Process Production</h3>
                        <p class="text-xs md:text-sm text-ink-500 mt-0.5 md:mt-1">Deduct raw materials and add finished goods.</p>
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Production Date</label>
                    <input type="date" name="production_date" form="productionForm" value="{{ date('Y-m-d') }}" required class="input !py-1.5 md:!py-2 !text-xs md:!text-sm">
                </div>
            </div>
            
            <form action="{{ route('products.store-production') }}" method="POST" id="productionForm">
                @csrf
                <div class="p-4 md:p-6 space-y-4 md:space-y-6">
                    @if(session('error'))
                        <div class="px-3 md:px-4 py-2 md:py-3 bg-red-50 text-red-700 rounded-lg text-xs md:text-sm font-medium border border-red-200 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-base md:text-lg"></i> {{ session('error') }}
                        </div>
                    @endif

                    {{-- 1. RAW MATERIAL INPUT --}}
                    <div class="bg-rose-50 border border-rose-100 rounded-xl p-3 md:p-5">
                        <div class="flex items-center gap-2 border-b border-rose-100 pb-2 md:pb-3 mb-3 md:mb-4">
                            <div class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-[10px] md:text-xs"><i class="fas fa-arrow-down"></i></div>
                            <h4 class="font-bold text-rose-700 text-xs md:text-sm uppercase tracking-wider">Raw Material Used</h4>
                        </div>
                        <div class="flex flex-col md:flex-row items-end gap-3 md:gap-4">
                            <div class="flex-1 w-full">
                                <div class="flex justify-between items-end mb-1">
                                    <label class="block text-[10px] md:text-[11px] font-bold text-rose-800">Total Cherries Used (kg)</label>
                                    {{-- DINAGDAGAN: AVAILABLE STOCK BADGE --}}
                                    <span class="text-[10px] md:text-[11px] font-bold bg-rose-100 text-rose-700 px-2 py-0.5 rounded border border-rose-200">
                                        Available: {{ number_format($cherryVariant->stock ?? 0, 2) }} kg
                                    </span>
                                </div>
                                <input type="hidden" name="raw_variant_id" value="{{ $cherryVariant->id ?? '' }}">
                                <input type="number" name="raw_qty_used" id="rawQtyInput" data-max="{{ $cherryVariant->stock ?? 0 }}" maxlength="9" max="999999" step="0.01" min="0.01" required class="input font-bold text-rose-700 !border-rose-300 !py-2 md:!py-2.5 !text-sm">
                                <p id="stockWarning" class="text-[10px] md:text-xs text-red-600 font-bold mt-1 hidden"><i class="fas fa-exclamation-triangle"></i> Kulang ang stock! Available: <span id="maxStockDisplay"></span> kg.</p>
                            </div>
                            <div class="text-[10px] md:text-sm text-rose-600 font-medium pb-2">
                                <i class="fas fa-info-circle"></i> Automatic babawasan ang stock.
                            </div>
                        </div>
                    </div>

                    {{-- 2. FINISHED PRODUCTS --}}
                    <div>
                        <div class="flex items-center justify-between mb-2 md:mb-3">
                            <h4 class="font-bold text-ink-800 text-xs md:text-sm uppercase tracking-wider flex items-center gap-1.5 md:gap-2"><i class="fas fa-plus-circle text-emerald-500"></i> Finished Products</h4>
                            <button type="button" id="addProductBtn" class="btn btn-secondary !py-1 md:!py-1.5 !px-2 md:!px-3 !text-[10px] md:!text-xs"><i class="fas fa-plus"></i> Add Output</button>
                        </div>
                        <div id="productsContainer" class="space-y-3">
                            {{-- Default Row --}}
                            <div class="product-row bg-emerald-50 border border-emerald-100 rounded-xl p-3 md:p-4 grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-3 items-end relative">
                                <div class="md:col-span-4 relative custom-dropdown" data-type="product">
                                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Product</label>
                                    <div class="relative">
                                        <input type="text" name="products[0][product_name]" class="dropdown-input input !py-1.5 md:!py-2 !text-xs pr-8" placeholder="Select..." autocomplete="off" required>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 text-[10px] pointer-events-none"></i>
                                    </div>
                                    <div class="dropdown-menu absolute z-20 w-full mt-1 bg-white border border-ink-200 rounded-lg shadow-pop max-h-48 overflow-y-auto hidden"><ul class="py-1 text-xs text-ink-700 dropdown-list"></ul></div>
                                </div>
                                <div class="md:col-span-3 relative custom-dropdown" data-type="variant">
                                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Variant</label>
                                    <div class="relative">
                                        <input type="text" name="products[0][variant_name]" class="dropdown-input input !py-1.5 md:!py-2 !text-xs pr-8" placeholder="Select..." autocomplete="off" required>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 text-[10px] pointer-events-none"></i>
                                    </div>
                                    <div class="dropdown-menu absolute z-20 w-full mt-1 bg-white border border-ink-200 rounded-lg shadow-pop max-h-48 overflow-y-auto hidden"><ul class="py-1 text-xs text-ink-700 dropdown-list"></ul></div>
                                    <!-- GIDUGANG NGA STOCK DISPLAY -->
                                    <p class="text-[9px] md:text-[10px] text-emerald-600 font-bold mt-1 stock-display hidden">Current Stock: <span class="stock-value">0</span></p>
                                </div>
                                <div class="md:col-span-2 relative custom-dropdown" data-type="unit">
                                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Unit</label>
                                    <div class="relative">
                                        <input type="text" name="products[0][unit]" class="dropdown-input input !py-1.5 md:!py-2 !text-xs pr-8" placeholder="e.g. kg" autocomplete="off" required>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 text-[10px] pointer-events-none"></i>
                                    </div>
                                    <div class="dropdown-menu absolute z-20 w-full mt-1 bg-white border border-ink-200 rounded-lg shadow-pop max-h-48 overflow-y-auto hidden"><ul class="py-1 text-xs text-ink-700 dropdown-list"></ul></div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Qty</label>
                                    <input type="number" name="products[0][quantity]" step="0.01" min="0.01" required class="input !py-1.5 md:!py-2 !text-xs font-bold text-emerald-700" placeholder="0" max="999999" maxlength="9">
                                </div>
                                <div class="md:col-span-1 flex justify-center pb-1"><button type="button" class="remove-product w-7 h-7 md:w-8 md:h-8 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-md opacity-50 cursor-not-allowed"><i class="fas fa-trash text-[10px]"></i></button></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. EXPENSES --}}
                    <div class="pt-3 md:pt-4 border-t border-ink-200">
                        <label class="block text-[10px] md:text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Additional Expense (&#8369;)</label>
                        <input type="number" name="expense_amount" step="0.01" min="0" class="input !py-2 md:!py-2.5 !text-xs md:!text-sm" placeholder="0.00 (Optional)" max="999999" maxlength="9">
                    </div>
                </div>
                <div class="px-4 md:px-6 py-3 md:py-4 border-t border-ink-200 bg-ink-50 flex justify-end">
                    <button type="submit" id="saveProductionBtn" class="btn btn-primary w-full md:w-auto !py-2 md:!py-2.5 !text-xs md:!text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check-circle"></i> Save Production
                    </button>
                </div>
            </form>
        </div>

        {{-- PRODUCTION HISTORY --}}
        <div class="card overflow-hidden max-w-4xl mx-auto">
            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-ink-200 bg-ink-50">
                <h3 class="font-bold text-ink-800 text-sm md:text-base">Recent Production History</h3>
            </div>
            <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="table-clean min-w-[500px]">
                    <thead>
                        <tr><th>Date</th><th>Raw Material Used</th><th>Finished Good Produced</th></tr>
                    </thead>
                    <tbody>
                        @forelse($productions as $p)
                            <tr>
                                <td class="text-ink-600 font-medium text-xs md:text-sm">{{ \Carbon\Carbon::parse($p->production_date)->format('M d, Y') }}</td>
                                <td class="text-red-600 font-bold text-xs md:text-sm">- {{ number_format($p->raw_qty_used, 2) }} kg</td>
                                <td class="text-emerald-600 font-bold text-xs md:text-sm">+ {{ number_format($p->finished_qty_produced, 2) }} {{ $p->finishedVariant->unit ?? '' }} ({{ $p->finishedVariant->product->name ?? '' }})</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-6 md:py-8 text-ink-500 text-xs md:text-sm">No production records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Data for JS --}}
    @php
        $variantsData = $allVariants->map(function($v) {
            return [
                'product_name' => $v->product->name ?? 'Unknown',
                'variant_name' => $v->name,
                'stock' => $v->stock,
                'unit' => $v->unit
            ];
        });
    @endphp

    <script>
        // I-map ang data para makuha ang stock at unit
        const variantsData = @json($variantsData);
        const existingProducts = [...new Set(variantsData.map(v => v.product_name))];
        const existingVariants = [...new Set(variantsData.map(v => v.variant_name))];
        const defaultUnits = ['g', 'kg', 'pcs', 'pack', 'bundle', 'L', 'mL'];
    </script>
</div>
@endsection

@push('scripts')
<script>
    // ── TAB SWITCHING LOGIC ──
    function switchTab(tabName) {
        document.getElementById('tab-catalog').classList.add('hidden');
        document.getElementById('tab-production').classList.add('hidden');
        
        document.getElementById('tabBtn-catalog').className = "flex-1 sm:flex-none justify-center px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-1.5 sm:gap-2 hover:bg-ink-200/50";
        document.getElementById('tabBtn-production').className = "flex-1 sm:flex-none justify-center px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-1.5 sm:gap-2 hover:bg-ink-200/50";

        document.getElementById('tab-' + tabName).classList.remove('hidden');
        document.getElementById('tabBtn-' + tabName).className = "flex-1 sm:flex-none justify-center px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-bold rounded-lg bg-white text-ink-900 shadow-sm border border-ink-200 transition-all flex items-center gap-1.5 sm:gap-2";
    }

    // Auto-open tab base sa URL (Default: catalog)
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'catalog'; 
        switchTab(tab);

        // ── GRID/LIST TOGGLE ──
        const gridView = document.getElementById('productGridView');
        const listView = document.getElementById('productListView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');

        window.setProductView = function(type) {
            if (type === 'grid') {
                gridView.classList.remove('hidden'); listView.classList.add('hidden');
                gridBtn.className = 'w-7 h-7 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors';
                listBtn.className = 'w-7 h-7 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors';
            } else {
                gridView.classList.add('hidden'); listView.classList.remove('hidden');
                listBtn.className = 'w-7 h-7 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors';
                gridBtn.className = 'w-7 h-7 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors';
            }
        };

        // ── INVENTORY STOCK VALIDATION (FRONTEND) ──
        const rawQtyInput = document.getElementById('rawQtyInput');
        const stockWarning = document.getElementById('stockWarning');
        const maxStockDisplay = document.getElementById('maxStockDisplay');
        const saveProductionBtn = document.getElementById('saveProductionBtn');

        if(rawQtyInput) {
            const maxStock = parseFloat(rawQtyInput.getAttribute('data-max')) || 0;
            maxStockDisplay.innerText = maxStock.toFixed(2);

            rawQtyInput.addEventListener('input', function() {
                const inputVal = parseFloat(this.value) || 0;

                if (inputVal > maxStock) {
                    stockWarning.classList.remove('hidden');
                    this.classList.add('!border-red-500', '!text-red-600');
                    saveProductionBtn.disabled = true;
                } else {
                    stockWarning.classList.add('hidden');
                    this.classList.remove('!border-red-500', '!text-red-600');
                    saveProductionBtn.disabled = false;
                }
            });
        }
    });

    // ── CUSTOM SEARCHABLE DROPDOWN LOGIC ──
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });

    function setupDropdown(container) {
        const input = container.querySelector('.dropdown-input');
        const menu = container.querySelector('.dropdown-menu');
        const list = container.querySelector('.dropdown-list');
        const type = container.getAttribute('data-type');
        
        let dataList = existingProducts;
        if (type === 'variant') { dataList = existingVariants; }
        else if (type === 'unit') { dataList = defaultUnits; }

        function renderList(filterText = '') {
            list.innerHTML = '';
            const filtered = dataList.filter(item => item.toLowerCase().includes(filterText.toLowerCase()));
            
            if (filtered.length === 0) {
                list.innerHTML = `<li class="px-3 md:px-4 py-2 text-[10px] md:text-xs text-ink-500 italic">Type to create new...</li>`;
            } else {
                filtered.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'px-3 md:px-4 py-2 hover:bg-brand-50 cursor-pointer transition-colors font-medium border-b border-ink-100 last:border-0 flex items-center gap-2 text-[10px] md:text-xs';
                    li.innerHTML = `<i class="fas fa-tag text-ink-400 text-[9px]"></i> ${item}`;
                    li.addEventListener('click', () => {
                        input.value = item;
                        menu.classList.add('hidden');
                        
                        // LOGIC: Kung variant ang gi-click, pangitaa ang stock
                        if (type === 'variant') {
                            const row = container.closest('.product-row');
                            // Kinahanglan naa nay product nga napili daan
                            const productName = row.querySelector('[data-type="product"] input').value;
                            
                            // Pangitaa sa variantsData
                            const found = variantsData.find(v => v.product_name === productName && v.variant_name === item);
                            const stockDisplay = container.querySelector('.stock-display');
                            
                            if (found && stockDisplay) {
                                // I-display ang stock ug unit
                                stockDisplay.querySelector('.stock-value').innerText = found.stock + ' ' + found.unit;
                                stockDisplay.classList.remove('hidden');
                                
                                // Auto-fill ang Unit input
                                const unitInput = row.querySelector('[data-type="unit"] input');
                                if (unitInput && found.unit) {
                                    unitInput.value = found.unit;
                                }
                            } else if (stockDisplay) {
                                // Itago kung bag-ong variant (wala sa database)
                                stockDisplay.classList.add('hidden');
                            }
                        }
                    });
                    list.appendChild(li);
                });
            }
        }

        input.addEventListener('focus', () => { renderList(input.value); menu.classList.remove('hidden'); });
        input.addEventListener('input', () => { renderList(input.value); menu.classList.remove('hidden'); });
    }

    document.querySelectorAll('.custom-dropdown').forEach(setupDropdown);

    // ── MULTIPLE PRODUCTION ROWS ──
    let productIndex = 1;
    const container = document.getElementById('productsContainer');
    const addBtn = document.getElementById('addProductBtn');

    if (addBtn) {
        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'product-row bg-emerald-50 border border-emerald-100 rounded-xl p-3 md:p-4 grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-3 items-end relative';
            row.innerHTML = `
                <div class="md:col-span-4 relative custom-dropdown" data-type="product">
                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Product</label>
                    <div class="relative">
                        <input type="text" name="products[${productIndex}][product_name]" class="dropdown-input input !py-1.5 md:!py-2 !text-xs pr-8" placeholder="Select..." autocomplete="off" required>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 text-[10px] pointer-events-none"></i>
                    </div>
                    <div class="dropdown-menu absolute z-20 w-full mt-1 bg-white border border-ink-200 rounded-lg shadow-pop max-h-48 overflow-y-auto hidden"><ul class="py-1 text-xs text-ink-700 dropdown-list"></ul></div>
                </div>
                <div class="md:col-span-3 relative custom-dropdown" data-type="variant">
                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Variant</label>
                    <div class="relative">
                        <input type="text" name="products[${productIndex}][variant_name]" class="dropdown-input input !py-1.5 md:!py-2 !text-xs pr-8" placeholder="Select..." autocomplete="off" required>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 text-[10px] pointer-events-none"></i>
                    </div>
                    <div class="dropdown-menu absolute z-20 w-full mt-1 bg-white border border-ink-200 rounded-lg shadow-pop max-h-48 overflow-y-auto hidden"><ul class="py-1 text-xs text-ink-700 dropdown-list"></ul></div>
                    <!-- GIDUGANG NGA STOCK DISPLAY -->
                    <p class="text-[9px] md:text-[10px] text-emerald-600 font-bold mt-1 stock-display hidden">Current Stock: <span class="stock-value">0</span></p>
                </div>
                <div class="md:col-span-2 relative custom-dropdown" data-type="unit">
                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Unit</label>
                    <div class="relative">
                        <input type="text" name="products[${productIndex}][unit]" class="dropdown-input input !py-1.5 md:!py-2 !text-xs pr-8" placeholder="e.g. kg" autocomplete="off" required>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 text-[10px] pointer-events-none"></i>
                    </div>
                    <div class="dropdown-menu absolute z-20 w-full mt-1 bg-white border border-ink-200 rounded-lg shadow-pop max-h-48 overflow-y-auto hidden"><ul class="py-1 text-xs text-ink-700 dropdown-list"></ul></div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[9px] md:text-[10px] font-bold text-ink-600 mb-1">Qty</label>
                    <input type="number" name="products[${productIndex}][quantity]" step="0.01" min="0.01" required class="input !py-1.5 md:!py-2 !text-xs font-bold text-emerald-700" placeholder="0" max="999999" maxlength="9">
                </div>
                <div class="md:col-span-1 flex justify-center pb-1">
                    <button type="button" class="remove-product w-7 h-7 md:w-8 md:h-8 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-md"><i class="fas fa-trash text-[10px]"></i></button>
                </div>
            `;
            container.appendChild(row);
            setupDropdown(row.querySelector('.custom-dropdown[data-type="product"]'));
            setupDropdown(row.querySelector('.custom-dropdown[data-type="variant"]'));
            setupDropdown(row.querySelector('.custom-dropdown[data-type="unit"]'));
            productIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-product')) {
                const row = e.target.closest('.product-row');
                if (document.querySelectorAll('.product-row').length > 1) { row.remove(); } 
                else { alert('Kailangan naay isa ka product.'); }
            }
        });
    }
</script>
@endpush