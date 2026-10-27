@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Stock levels and inventory value')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto space-y-6">

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card p-5 border-l-4 border-l-ink-400">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Items</div>
            <div class="text-2xl font-black text-ink-900">{{ number_format($stats['total_items'] ?? 0) }}</div>
        </div>
        <div class="card p-5 border-l-4 border-l-brand-500">
            <div class="text-[10px] font-bold text-brand-600 uppercase tracking-widest mb-1">Inventory Value</div>
            <div class="text-2xl font-black text-ink-900">₱{{ number_format($stats['total_value'] ?? 0, 0) }}</div>
        </div>
        <div class="card p-5 border-l-4 border-l-amber-500">
            <div class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1">Low Stock</div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($stats['low_stock'] ?? 0) }}</div>
        </div>
        <div class="card p-5 border-l-4 border-l-red-500">
            <div class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-1">Out of Stock</div>
            <div class="text-2xl font-black text-red-600">{{ number_format($stats['out_of_stock'] ?? 0) }}</div>
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="card overflow-hidden">
        
        {{-- TABS & TOOLBAR --}}
        <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory.index', ['tab' => 'main']) }}" class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ request('tab') !== 'finished' ? 'bg-white text-ink-900 shadow-sm border border-ink-200' : 'text-ink-500 hover:text-ink-900' }}">
                    <i class="fas fa-boxes-stacked mr-2"></i> Raw Materials
                </a>
                <a href="{{ route('inventory.index', ['tab' => 'finished']) }}" class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ request('tab') === 'finished' ? 'bg-white text-ink-900 shadow-sm border border-ink-200' : 'text-ink-500 hover:text-ink-900' }}">
                    <i class="fas fa-mug-hot mr-2"></i> Finished Goods
                </a>
            </div>
            
            <form method="GET" action="{{ route('inventory.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                <input type="hidden" name="tab" value="{{ request('tab', 'main') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search inventory..." class="input !py-2 !text-sm w-full md:w-64">
                <button type="submit" class="btn btn-secondary !py-2"><i class="fas fa-search"></i></button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th class="w-[35%]">Product</th>
                        <th>Variant / Unit</th>
                        <th class="text-right">Price</th>
                        <th class="text-center">Stock Level</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $inventoryList = request('tab') === 'finished' ? $finishedGoods : $mainInventory;
                    @endphp
                    
                    @forelse($inventoryList as $variant)
                        <tr class="group">
                            {{-- PRODUCT NAME --}}
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-ink-100 flex items-center justify-center font-bold text-ink-600 border border-ink-200">
                                        @if($variant->product->image)
                                            <img src="{{ Storage::url($variant->product->image) }}" class="w-full h-full object-cover rounded-lg">
                                        @else
                                            {{ strtoupper(substr($variant->product->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-ink-900">{{ $variant->product->name ?? 'Unknown Product' }}</div>
                                        <div class="text-[10px] text-ink-500 font-mono">{{ $variant->product->sku ?? 'NO-SKU' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- VARIANT --}}
                            <td class="text-ink-600 font-medium">
                                {{ $variant->name }} 
                                <span class="text-[10px] text-ink-400">({{ $variant->unit }})</span>
                            </td>

                            {{-- PRICE --}}
                            <td class="text-right font-bold text-ink-900">
                                ₱{{ number_format($variant->price, 2) }}
                            </td>

                            {{-- STOCK LEVEL --}}
                            <td class="text-center">
                                <span class="font-black text-lg {{ $variant->stock <= 5 ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ number_format($variant->stock, 2) }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="text-center">
                                @if($variant->stock <= 0)
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-red-50 text-red-600 border border-red-200 uppercase tracking-wider">Out of Stock</span>
                                @elseif($variant->stock <= 5)
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 uppercase tracking-wider">Low Stock</span>
                                @else
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-wider">In Stock</span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    
                                    {{-- EDIT BUTTON (Mopadulong sa Product Edit Page) --}}
                                    <a href="{{ route('products.edit', $variant->product_id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-brand-600 bg-brand-50 hover:bg-brand-100 transition-colors shadow-sm" title="Edit Product">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>

                                    {{-- STOCK IN BUTTON (Pwede mag-add og stock) --}}
                                    <button type="button" onclick="openStockInModal({{ $variant->id }})" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors shadow-sm" title="Add Stock">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-ink-400">
                                <i class="fas fa-box-open text-4xl mb-3 text-ink-300"></i>
                                <p class="font-medium">No inventory items found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-ink-200 bg-ink-50">
            {{ $inventoryList->appends(request()->all())->links() }}
        </div>
    </div>
</div>

{{-- STOCK IN MODAL --}}
<dialog id="stockInDialog" class="p-0 border-0 rounded-2xl shadow-pop w-full max-w-md">
    <form action="{{ route('inventory.stock-in') }}" method="POST">
        @csrf
        <div class="px-6 py-4 border-b border-ink-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-ink-900">Add Stock</h3>
            <button type="button" onclick="document.getElementById('stockInDialog').close()" class="w-8 h-8 rounded-full bg-ink-100 flex items-center justify-center text-ink-500 hover:bg-ink-200 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Product Variant</label>
                <select name="variant_id" id="stockInVariantSelect" required class="input cursor-pointer">
                    <option value="">Select variant...</option>
                    @foreach($allVariantsList as $v)
                        <option value="{{ $v->id }}">{{ $v->product->name ?? 'Product' }} - {{ $v->name }} (Current: {{ $v->stock }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Quantity to Add</label>
                <input type="number" name="quantity" step="0.01" min="0.01" required class="input font-bold" placeholder="e.g. 50">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-ink-200 bg-ink-50 flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('stockInDialog').close()" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Stock</button>
        </div>
    </form>
</dialog>

@push('scripts')
<script>
    function openStockInModal(variantId) {
        const dialog = document.getElementById('stockInDialog');
        const select = document.getElementById('stockInVariantSelect');
        if (variantId) {
            select.value = variantId;
        }
        dialog.showModal();
    }
</script>
@endpush
@endsection