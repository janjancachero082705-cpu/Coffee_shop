@extends('layouts.admin')
@section('title', 'Inventory & Products')
@section('subtitle', 'Manage catalog, stocks, and production')

@section('content')

{{-- ══════ MODERN TABS ══════ --}}
<div class="mb-8 flex gap-2 bg-slate-100 p-1.5 rounded-xl border border-slate-200 w-max">
    <button onclick="switchTab('catalog')" id="tabBtn-catalog" class="px-6 py-2.5 text-sm font-bold rounded-lg text-slate-700 transition-all flex items-center gap-2 hover:bg-slate-200">
        <i class="fas fa-box"></i> Product Catalog
    </button>
    <button onclick="switchTab('production')" id="tabBtn-production" class="px-6 py-2.5 text-sm font-bold rounded-lg text-slate-700 transition-all flex items-center gap-2 hover:bg-slate-200">
        <i class="fas fa-industry"></i> Process Production
    </button>
</div>

{{-- ══════ TAB 1: CATALOG ══════ --}}
<div id="tab-catalog" class="space-y-6">
    
    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total SKUs</div>
                <div class="text-3xl font-black text-slate-900">{{ number_format($stats['total_products']) }}</div>
                <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-box text-blue-400 mr-1"></i> Active products</div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Low Stock</div>
                <div class="text-3xl font-black text-amber-600">{{ number_format($stats['low_stock']) }}</div>
                <div class="text-xs text-amber-500 mt-2 font-medium"><i class="fas fa-exclamation-triangle mr-1"></i> Needs restock</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Out of Stock</div>
                <div class="text-3xl font-black text-red-600">{{ number_format($stats['out_of_stock']) }}</div>
                <div class="text-xs text-red-500 mt-2 font-medium"><i class="fas fa-times-circle mr-1"></i> Unavailable</div>
            </div>
        </div>

        <div class="bg-slate-900 rounded-xl shadow-sm p-5 border border-slate-800 relative overflow-hidden group">
            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-800 opacity-30 group-hover:scale-110 transition-transform"><i class="fas fa-chart-line fa-5x"></i></div>
            <div class="relative z-10">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Inventory Value</div>
                <div class="text-3xl font-black text-caramel-400">&#8369;{{ number_format($stats['total_value'], 0) }}</div>
                <div class="text-xs text-slate-400 mt-2 font-medium">Based on cost price</div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6 flex flex-col lg:flex-row justify-between gap-4">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="hidden" name="tab" value="catalog">
            <div class="relative group w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-caramel-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all">
            </div>
            <select name="category" class="w-full md:w-40 px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none cursor-pointer text-slate-700">
                <option value="">All Categories</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->emoji }} {{ $c->name }}</option>@endforeach
            </select>
            <button type="submit" class="px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-colors shadow-sm">Filter</button>
            @if(request()->has('search') || request()->has('category'))
                <a href="{{ route('products.index', ['tab' => 'catalog']) }}" class="px-3 py-2 text-red-500 text-sm font-semibold hover:text-red-700">Clear</a>
            @endif
        </form>

        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-200">
                <button id="gridViewBtn" onclick="setProductView('grid')" class="w-8 h-8 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors"><i class="fas fa-th-large text-xs"></i></button>
                <button id="listViewBtn" onclick="setProductView('list')" class="w-8 h-8 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors"><i class="fas fa-list text-xs"></i></button>
            </div>
            <a href="{{ route('products.create') }}" class="px-5 py-2.5 bg-caramel-500 hover:bg-caramel-600 text-white rounded-lg font-bold text-sm shadow-md transition-all flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>

    {{-- GRID VIEW --}}
    @if($products->count() > 0)
        <div id="productGridView" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($products as $p)
                @php $totalStock = $p->variants->sum('stock'); @endphp
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-caramel-300 transition-all duration-200 flex flex-col group relative overflow-hidden">
                    
                    {{-- Status Badges --}}
                    @if($totalStock <= 0)
                        <div class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md z-10 shadow-sm">OUT OF STOCK</div>
                    @elseif($totalStock <= 5)
                        <div class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-md z-10 shadow-sm">LOW STOCK</div>
                    @endif

                    {{-- Actions --}}
                    <div class="absolute top-3 right-3 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                        <a href="{{ route('products.edit', $p) }}" class="w-7 h-7 bg-white/90 backdrop-blur text-blue-600 rounded border border-slate-200 flex items-center justify-center shadow-sm hover:bg-blue-50 transition-colors" title="Edit"><i class="fas fa-pen text-[10px]"></i></a>
                        <form action="{{ route('products.destroy', $p) }}" method="POST" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="w-7 h-7 bg-white/90 backdrop-blur text-red-600 rounded border border-slate-200 flex items-center justify-center shadow-sm hover:bg-red-50 transition-colors" title="Delete"><i class="fas fa-trash text-[10px]"></i></button></form>
                    </div>

                    {{-- Image --}}
                    <a href="{{ route('products.show', $p) }}" class="w-full h-44 bg-slate-50 flex items-center justify-center relative overflow-hidden border-b border-slate-100">
                        @if($p->image)<img src="{{ Storage::url($p->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">@else
                            <span class="text-6xl font-black text-slate-200 group-hover:scale-110 transition-transform duration-300">{{ strtoupper(substr($p->name, 0, 1)) }}</span>
                        @endif
                    </a>

                    {{-- Details --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">{{ $p->category->name ?? 'Uncategorized' }}</div>
                        <a href="{{ route('products.show', $p) }}"><h4 class="font-bold text-slate-900 text-base leading-tight mb-1 group-hover:text-caramel-600 transition-colors line-clamp-1">{{ $p->name }}</h4></a>
                        <div class="text-[10px] text-slate-500 mb-3 font-medium flex items-center gap-1.5"><span>{{ $p->variants->count() }} Variants</span> <span class="text-slate-300">|</span> <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $p->sku }}</span></div>
                        <div class="mt-auto pt-3 border-t border-slate-100">
                            <a href="{{ route('products.show', $p) }}" class="w-full py-2 bg-slate-50 hover:bg-caramel-50 text-slate-700 hover:text-caramel-700 text-[11px] font-bold rounded-lg border border-slate-200 hover:border-caramel-200 transition-colors flex items-center justify-center gap-1.5 group-hover:bg-caramel-500 group-hover:text-white group-hover:border-caramel-500">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- LIST VIEW --}}
        <div id="productListView" class="hidden overflow-x-auto bg-white rounded-xl shadow-sm border border-slate-200">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Product Details</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-center">Variants</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($products as $p)
                        @php $totalStock = $p->variants->sum('stock'); @endphp
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200 flex-shrink-0">
                                        @if($p->image)<img src="{{ Storage::url($p->image) }}" class="w-full h-full object-cover">@else<span class="font-bold text-slate-500">{{ strtoupper(substr($p->name, 0, 1)) }}</span>@endif
                                    </div>
                                    <div>
                                        <a href="{{ route('products.show', $p) }}" class="font-bold text-slate-900 hover:text-caramel-600 transition-colors">{{ $p->name }}</a>
                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 inline-block">{{ $p->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">{{ $p->category->emoji ?? '' }} {{ $p->category->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-md text-[10px] font-bold border border-slate-200">{{ $p->variants->count() }} Sizes</span></td>
                            <td class="px-6 py-4 text-center">
                                @if($totalStock <= 0)<span class="px-2.5 py-1 bg-red-50 text-red-700 rounded-md text-[10px] font-bold border border-red-200 uppercase tracking-wide">Out of Stock</span>
                                @elseif($totalStock <= 5)<span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-md text-[10px] font-bold border border-amber-200 uppercase tracking-wide">Low ({{ $totalStock }})</span>
                                @else<span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[10px] font-bold border border-emerald-200 uppercase tracking-wide">In Stock</span>@endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('products.show', $p) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 transition-colors shadow-sm" title="View Details"><i class="fas fa-eye text-xs"></i></a>
                                    <a href="{{ route('products.edit', $p) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-800 transition-colors shadow-sm" title="Edit Product"><i class="fas fa-pen text-xs"></i></a>
                                    <form action="{{ route('products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 transition-colors shadow-sm" title="Delete"><i class="fas fa-trash text-xs"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $products->appends(['tab' => 'catalog'])->links() }}</div>
    @else
        <div class="text-center py-24 bg-white rounded-xl border border-slate-200">
            <div class="w-20 h-20 mx-auto bg-slate-50 rounded-2xl flex items-center justify-center mb-4"><i class="fas fa-box-open text-3xl text-slate-300"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">No products found</h3>
            <p class="text-sm text-slate-500 mb-6">Start by adding your first product to the catalog.</p>
        </div>
    @endif
</div>

{{-- ══════ TAB 2: PRODUCTION ══════ --}}
<div id="tab-production" class="hidden space-y-6">
    
    {{-- PRODUCTION FORM --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-5xl mx-auto">
        <div class="px-8 py-6 border-b border-slate-200 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-industry text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Process Production</h3>
                    <p class="text-sm text-slate-500 mt-1">Deduct raw materials and add finished goods to inventory.</p>
                </div>
            </div>
            <div class="w-48">
                <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Production Date</label>
                <input type="date" name="production_date" form="productionForm" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 focus:border-emerald-500 outline-none shadow-sm">
            </div>
        </div>
        
        <form action="{{ route('products.store-production') }}" method="POST" id="productionForm">
            @csrf
            <div class="p-8 space-y-6">
                @if(session('error'))
                    <div class="px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm font-medium border border-red-200 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-lg"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- 1. RAW MATERIAL INPUT --}}
                <div class="bg-rose-50/50 border border-rose-100 rounded-xl p-6">
                    <div class="flex items-center gap-2 border-b border-rose-100 pb-3 mb-4">
                        <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs"><i class="fas fa-arrow-down"></i></div>
                        <h4 class="font-bold text-rose-700 text-sm uppercase tracking-wider">Raw Material Used</h4>
                    </div>
                    <div class="flex flex-col md:flex-row items-end gap-4">
                        <div class="flex-1 w-full">
                            <label class="block text-[11px] font-bold text-rose-800 mb-1">Total Cherries Used (kg)</label>
                            <input type="number" name="raw_qty_used" step="0.01" min="0.01" required class="w-full px-4 py-3 bg-white border border-rose-300 rounded-lg text-lg focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 outline-none shadow-sm font-bold text-rose-700" placeholder="e.g. 500">
                        </div>
                        <div class="text-sm text-rose-600 font-medium pb-3">
                            <i class="fas fa-info-circle"></i> Automatic babawasan ang stock ng Cherries.
                            @if($cherryVariant) (Available: {{ number_format($cherryVariant->stock, 2) }} kg) @endif
                        </div>
                    </div>
                </div>

                {{-- 2. FINISHED PRODUCTS --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wider flex items-center gap-2"><i class="fas fa-plus-circle text-emerald-500"></i> Finished Products Produced</h4>
                        <button type="button" id="addProductBtn" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-md transition-colors shadow-sm"><i class="fas fa-plus mr-1"></i> Add Output</button>
                    </div>
                    <div id="productsContainer" class="space-y-3">
                        <div class="product-row bg-emerald-50/50 border border-emerald-100 rounded-xl p-4 grid grid-cols-1 md:grid-cols-12 gap-3 items-end relative">
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-bold text-slate-600 mb-1">Product Name</label>
                                <input type="text" name="products[0][product_name]" required list="productList" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm" placeholder="e.g. Coffee Powder">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-slate-600 mb-1">Variant / Size</label>
                                <input type="text" name="products[0][variant_name]" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm" placeholder="e.g. 250g">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-600 mb-1">Unit</label>
                                <select name="products[0][unit]" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm cursor-pointer"><option value="pcs">Packs</option><option value="kg">kg</option></select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-600 mb-1">Qty Produced</label>
                                <input type="number" name="products[0][quantity]" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm font-bold text-emerald-700" placeholder="0">
                            </div>
                            <div class="md:col-span-1 flex justify-center pb-1"><button type="button" class="remove-product w-8 h-8 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-md opacity-50 cursor-not-allowed"><i class="fas fa-trash text-xs"></i></button></div>
                        </div>
                    </div>
                </div>

                {{-- 3. EXPENSES --}}
                <div class="pt-4 border-t border-slate-200">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Additional Expense (&#8369;)</label>
                    <input type="number" name="expense_amount" step="0.01" min="0" class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:border-emerald-500 outline-none shadow-sm" placeholder="0.00 (Optional)">
                </div>
            </div>
            <div class="px-8 py-5 border-t border-slate-200 bg-slate-50 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-lg shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Save Production Batch
                </button>
            </div>
        </form>
    </div>

    {{-- PRODUCTION HISTORY --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-5xl mx-auto">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-800">Recent Production History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-100 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                    <tr><th class="px-6 py-3">Date</th><th class="px-6 py-3">Raw Material Used</th><th class="px-6 py-3">Finished Good Produced</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($productions as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 text-slate-600 font-medium">{{ \Carbon\Carbon::parse($p->production_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-red-600 font-bold">- {{ number_format($p->raw_qty_used, 2) }} kg (Cherries)</td>
                            <td class="px-6 py-3 text-emerald-600 font-bold">+ {{ number_format($p->finished_qty_produced, 2) }} {{ $p->finishedVariant->unit ?? '' }} ({{ $p->finishedVariant->product->name ?? '' }})</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-8 text-slate-500">No production records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<datalist id="productList">
    @foreach($allVariants as $v)<option value="{{ $v->product->name ?? '' }}">@endforeach
</datalist>
@endsection

@push('scripts')
<script>
    // ── TAB SWITCHING LOGIC ──
    function switchTab(tabName) {
        document.getElementById('tab-catalog').classList.add('hidden');
        document.getElementById('tab-production').classList.add('hidden');
        
        document.getElementById('tabBtn-catalog').className = "px-6 py-2.5 text-sm font-bold rounded-lg text-slate-700 transition-all flex items-center gap-2 hover:bg-slate-200";
        document.getElementById('tabBtn-production').className = "px-6 py-2.5 text-sm font-bold rounded-lg text-slate-700 transition-all flex items-center gap-2 hover:bg-slate-200";

        document.getElementById('tab-' + tabName).classList.remove('hidden');
        document.getElementById('tabBtn-' + tabName).className = "px-6 py-2.5 text-sm font-bold rounded-lg bg-white text-slate-900 shadow-sm border border-slate-200 transition-all flex items-center gap-2";
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'production') {
            switchTab('production');
        }

        // ── GRID/LIST TOGGLE ──
        const gridView = document.getElementById('productGridView');
        const listView = document.getElementById('productListView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');

        window.setProductView = function(type) {
            if (type === 'grid') {
                gridView.classList.remove('hidden'); listView.classList.add('hidden');
                gridBtn.className = 'w-8 h-8 flex items-center justify-center rounded bg-white text-slate-900 shadow-sm transition-colors';
                listBtn.className = 'w-8 h-8 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors';
            } else {
                gridView.classList.add('hidden'); listView.classList.remove('hidden');
                listBtn.className = 'w-8 h-8 flex items-center justify-center rounded bg-white text-slate-900 shadow-sm transition-colors';
                gridBtn.className = 'w-8 h-8 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors';
            }
        };

        // ── MULTIPLE PRODUCTION ROWS ──
        let productIndex = 1;
        const container = document.getElementById('productsContainer');
        const addBtn = document.getElementById('addProductBtn');

        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'product-row bg-emerald-50/50 border border-emerald-100 rounded-xl p-4 grid grid-cols-1 md:grid-cols-12 gap-3 items-end relative';
                row.innerHTML = `
                    <div class="md:col-span-4"><label class="block text-[10px] font-bold text-slate-600 mb-1">Product Name</label><input type="text" name="products[${productIndex}][product_name]" required list="productList" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm" placeholder="e.g. Roasted Beans"></div>
                    <div class="md:col-span-3"><label class="block text-[10px] font-bold text-slate-600 mb-1">Variant / Size</label><input type="text" name="products[${productIndex}][variant_name]" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm" placeholder="e.g. Standard"></div>
                    <div class="md:col-span-2"><label class="block text-[10px] font-bold text-slate-600 mb-1">Unit</label><select name="products[${productIndex}][unit]" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm cursor-pointer"><option value="pcs">Packs</option><option value="kg">kg</option></select></div>
                    <div class="md:col-span-2"><label class="block text-[10px] font-bold text-slate-600 mb-1">Qty Produced</label><input type="number" name="products[${productIndex}][quantity]" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:border-emerald-500 outline-none shadow-sm font-bold text-emerald-700" placeholder="0"></div>
                    <div class="md:col-span-1 flex justify-center pb-1"><button type="button" class="remove-product w-8 h-8 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-md"><i class="fas fa-trash text-xs"></i></button></div>
                `;
                container.appendChild(row);
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
    });
</script>
@endpush