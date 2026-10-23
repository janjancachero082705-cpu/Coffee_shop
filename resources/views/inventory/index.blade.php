@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Real-time stock levels and inventory valuation')

@section('content')

{{-- STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total SKUs</div>
            <div class="text-3xl font-black text-slate-900">{{ number_format($stats['total_items']) }}</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Low Stock</div>
            <div class="text-3xl font-black text-amber-600">{{ number_format($stats['low_stock']) }}</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Out of Stock</div>
            <div class="text-3xl font-black text-red-600">{{ number_format($stats['out_of_stock']) }}</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Value</div>
            <div class="text-3xl font-black text-emerald-600">&#8369;{{ number_format($stats['total_value'], 0) }}</div>
        </div>
    </div>
</div>

{{-- TABS & FILTERS --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    
    {{-- Header Tabs --}}
    <div class="px-6 pt-4 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex gap-2">
            <button onclick="switchInvTab('main')" id="tabBtn-main" class="px-5 py-3 text-sm font-bold border-b-2 border-slate-900 text-slate-900 transition-colors flex items-center gap-2">
                <i class="fas fa-boxes"></i> Raw Materials & Supplies
            </button>
            <button onclick="switchInvTab('finished')" id="tabBtn-finished" class="px-5 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
                <i class="fas fa-coffee"></i> Finished Goods
            </button>
        </div>
        <button onclick="openStockInModal()" class="mb-3 md:mb-0 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold text-sm shadow-sm transition-all flex items-center gap-2 self-start md:self-auto">
            <i class="fas fa-plus-circle"></i> Global Stock In
        </button>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 border-b border-slate-200 bg-white">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            <input type="hidden" name="tab" id="currentTabInput" value="{{ request('tab', 'main') }}">
            <div class="relative group w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-caramel-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product or SKU..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none shadow-sm transition-all">
            </div>
            <select name="status" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none shadow-sm cursor-pointer font-medium text-slate-700">
                <option value="">All Status</option>
                <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-colors shadow-sm">Filter</button>
            
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg font-semibold text-sm hover:bg-red-100 transition-colors shadow-sm">Clear</a>
            @endif
        </form>
    </div>

    {{-- ══════ TAB 1: RAW MATERIALS & SUPPLIES ══════ --}}
    <div id="tab-main" class="overflow-x-auto">
        @if($mainInventory->count() > 0)
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Variant</th>
                        <th class="px-6 py-4 w-64">Stock Level</th>
                        <th class="px-6 py-4 text-right">Current Stock</th>
                        <th class="px-6 py-4 text-center w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($mainInventory as $variant)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-200 shadow-sm">
                                        {{ strtoupper(substr($variant->product->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $variant->product->name ?? 'Unknown' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $variant->product->sku ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 bg-white text-slate-800 rounded-md text-xs font-bold border border-slate-200 shadow-sm">{{ $variant->name }} ({{ $variant->unit }})</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($variant->stock == 0)
                                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-bold text-red-600">Out of Stock</span></div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5"></div>
                                @elseif($variant->stock <= 5)
                                    <div class="flex justify-between text-[11px] font-bold text-amber-600 mb-1"><span>Low Stock</span><span>{{ $variant->stock }} {{ $variant->unit }} left</span></div>
                                    <div class="w-full bg-amber-100 rounded-full h-1.5"><div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ ($variant->stock / 10) * 100 }}%"></div></div>
                                @else
                                    <div class="flex justify-between text-[11px] font-bold text-emerald-600 mb-1"><span>In Stock</span><span>{{ number_format($variant->stock) }} {{ $variant->unit }}</span></div>
                                    <div class="w-full bg-emerald-100 rounded-full h-1.5"><div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, ($variant->stock / 100) * 100) }}%"></div></div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xl font-black text-slate-900">{{ number_format($variant->stock) }}</span>
                                <span class="text-xs font-bold text-slate-500 ml-1">{{ $variant->unit }}</span>
                            </td>
                            {{-- NEW ACTION COLUMN --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick="openStockInModal({{ $variant->id }})" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-700 transition-colors shadow-sm" title="Stock In">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <a href="{{ route('products.edit', $variant->product_id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 transition-colors shadow-sm" title="Edit Product">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/30">{{ $mainInventory->appends(['tab' => 'main'])->links() }}</div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto bg-slate-50 rounded-2xl flex items-center justify-center mb-4 border border-slate-100"><i class="fas fa-boxes text-3xl text-slate-300"></i></div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">No items found</h3>
            </div>
        @endif
    </div>

    {{-- ══════ TAB 2: FINISHED GOODS ══════ --}}
    <div id="tab-finished" class="hidden overflow-x-auto">
        @if($finishedGoods->count() > 0)
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Variant</th>
                        <th class="px-6 py-4 w-64">Stock Level</th>
                        <th class="px-6 py-4 text-right">Current Stock</th>
                        <th class="px-6 py-4 text-center w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($finishedGoods as $variant)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-caramel-50 text-caramel-700 flex items-center justify-center font-bold text-sm border border-caramel-200 shadow-sm">
                                        <i class="fas fa-coffee"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $variant->product->name ?? 'Unknown' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $variant->product->sku ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 bg-white text-slate-800 rounded-md text-xs font-bold border border-slate-200 shadow-sm">{{ $variant->name }} ({{ $variant->unit }})</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($variant->stock == 0)
                                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-bold text-red-600">Out of Stock</span></div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5"></div>
                                @elseif($variant->stock <= 5)
                                    <div class="flex justify-between text-[11px] font-bold text-amber-600 mb-1"><span>Low Stock</span><span>{{ $variant->stock }} {{ $variant->unit }} left</span></div>
                                    <div class="w-full bg-amber-100 rounded-full h-1.5"><div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ ($variant->stock / 10) * 100 }}%"></div></div>
                                @else
                                    <div class="flex justify-between text-[11px] font-bold text-emerald-600 mb-1"><span>In Stock</span><span>{{ number_format($variant->stock) }} {{ $variant->unit }}</span></div>
                                    <div class="w-full bg-emerald-100 rounded-full h-1.5"><div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, ($variant->stock / 100) * 100) }}%"></div></div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xl font-black text-slate-900">{{ number_format($variant->stock) }}</span>
                                <span class="text-xs font-bold text-slate-500 ml-1">{{ $variant->unit }}</span>
                            </td>
                            {{-- NEW ACTION COLUMN --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick="openStockInModal({{ $variant->id }})" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-700 transition-colors shadow-sm" title="Stock In">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <a href="{{ route('products.edit', $variant->product_id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 transition-colors shadow-sm" title="Edit Product">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/30">{{ $finishedGoods->appends(['tab' => 'finished'])->links() }}</div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto bg-caramel-50 rounded-2xl flex items-center justify-center mb-4 border border-caramel-100"><i class="fas fa-coffee text-3xl text-caramel-300"></i></div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">No finished goods found</h3>
                <p class="text-sm text-slate-500">Process a production batch to add finished goods.</p>
            </div>
        @endif
    </div>
</div>

{{-- MODAL FOR STOCK IN --}}
<div id="stockInModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200" id="modalContent">
        <form action="{{ route('inventory.stock-in') }}" method="POST">
            @csrf
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-900">Add Stock</h3>
                <button type="button" onclick="closeStockInModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-400 hover:text-red-500 shadow-sm"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Select Product Variant</label>
                    <select name="variant_id" id="stockInVariantSelect" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-slate-400 outline-none transition-all">
                        <option value="">Choose variant...</option>
                        @foreach($allVariantsList as $v)
                            <option value="{{ $v->id }}">{{ $v->product->name ?? 'Product' }} - {{ $v->name }} (Current: {{ $v->stock }} {{ $v->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Quantity to Add</label>
                    <input type="number" name="quantity" step="0.01" min="0.01" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-slate-400 outline-none transition-all" placeholder="e.g. 50">
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="closeStockInModal()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-100 transition-colors text-sm">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition-all text-sm">Save Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── TAB SWITCHING LOGIC ──
    function switchInvTab(tabName) {
        document.getElementById('tab-main').classList.add('hidden');
        document.getElementById('tab-finished').classList.add('hidden');
        
        document.getElementById('tabBtn-main').className = "px-5 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2";
        document.getElementById('tabBtn-finished').className = "px-5 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2";

        document.getElementById('tab-' + tabName).classList.remove('hidden');
        document.getElementById('tabBtn-' + tabName).className = "px-5 py-3 text-sm font-bold border-b-2 border-slate-900 text-slate-900 transition-colors flex items-center gap-2";
        
        document.getElementById('currentTabInput').value = tabName;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'main';
        switchInvTab(tab);
    });

    // ── STOCK IN MODAL (With Row Pre-selection) ──
    const modal = document.getElementById('stockInModal');
    const content = document.getElementById('modalContent');
    const variantSelect = document.getElementById('stockInVariantSelect');

    function openStockInModal(variantId = null) {
        modal.classList.remove('hidden');
        
        // Kung galing sa row button, i-select agad ang variant
        if (variantId) {
            variantSelect.value = variantId;
        } else {
            variantSelect.value = "";
        }

        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }

    function closeStockInModal() {
        modal.classList.add('opacity-0'); content.classList.remove('scale-100'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
    modal.addEventListener('click', function(e) { if (e.target === this) closeStockInModal(); });
</script>
@endpush