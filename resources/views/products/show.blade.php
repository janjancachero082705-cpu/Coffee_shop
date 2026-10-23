@extends('layouts.admin')
@section('title', $product->name)
@section('subtitle', 'Product Details and Variants')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- TOP HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200 flex-shrink-0">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                @else
                    <span class="text-4xl font-black text-slate-300">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-2xl font-bold text-slate-900">{{ $product->name }}</h2>
                    @if($product->is_active)
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded border border-emerald-200 uppercase tracking-wide">Active</span>
                    @endif
                </div>
                <div class="text-sm text-slate-500 font-mono bg-slate-50 px-2 py-1 rounded inline-block border border-slate-200">{{ $product->sku }}</div>
                <p class="text-xs text-slate-500 mt-2 font-medium">{{ $product->category->emoji ?? '' }} {{ $product->category->name ?? 'Uncategorized' }}</p>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-colors shadow-sm">Back</a>
            <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-slate-900 text-white rounded-lg font-semibold text-sm hover:bg-slate-800 transition-colors shadow-sm"><i class="fas fa-edit mr-1"></i> Edit Product</a>
        </div>
    </div>

    {{-- VARIANTS & STOCK IN --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-800">Variant Breakdown</h3>
                <p class="text-xs text-slate-500 mt-1">Prices and stock per size</p>
            </div>
            
            {{-- STOCK IN BUTTON --}}
            <button onclick="openStockInModal()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm shadow-sm transition-all flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Stock In
            </button>
        </div>
        
        <div class="p-6">
            <div class="relative border-l-2 border-slate-200 ml-4 space-y-6 pb-4">
                
                @foreach($product->variants as $variant)
                    <div class="relative pl-8">
                        {{-- Tree Dot --}}
                        <div class="absolute -left-[9px] top-1 w-4 h-4 bg-white border-2 border-slate-400 rounded-full"></div>

                        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:border-caramel-400 transition-colors flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            
                            {{-- Variant Info & Image --}}
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($variant->image)
                                        <img src="{{ Storage::url($variant->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-image text-slate-300 text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-lg">{{ $variant->name }}</h4>
                                    <div class="text-xs text-slate-500 font-medium">Unit: {{ strtoupper($variant->unit) }}</div>
                                </div>
                            </div>

                            {{-- Variant Data --}}
                            <div class="flex items-center gap-8 w-full md:w-auto mt-4 md:mt-0">
                                <div class="text-right">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Price</div>
                                    <div class="font-black text-slate-900 text-xl">&#8369;{{ number_format($variant->price, 2) }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Stock</div>
                                    @if($variant->stock <= 0)
                                        <div class="px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded border border-red-200">OUT OF STOCK</div>
                                    @elseif($variant->stock <= 5)
                                        <div class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded border border-amber-200">{{ $variant->stock }} left</div>
                                    @else
                                        <div class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded border border-emerald-200">{{ number_format($variant->stock) }} units</div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

{{-- ══════ STOCK IN MODAL ══════ --}}
<div id="stockInModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200" id="modalContent">
        <form action="{{ route('products.stock-in') }}" method="POST">
            @csrf
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-900">Add Stock</h3>
                <button type="button" onclick="closeStockInModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-400 hover:text-red-500 shadow-sm"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Select Variant</label>
                    <select name="variant_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-emerald-500 outline-none transition-all">
                        <option value="">Choose variant...</option>
                        @foreach($product->variants as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} (Current Stock: {{ $v->stock }} {{ $v->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Quantity to Add</label>
                    <input type="number" name="quantity" min="1" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-emerald-500 outline-none transition-all" placeholder="e.g. 50">
                </div>
            </div>
            
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="closeStockInModal()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-100 transition-colors text-sm">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md transition-all text-sm">Save Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('stockInModal');
    const content = document.getElementById('modalContent');

    function openStockInModal() {
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }

    function closeStockInModal() {
        modal.classList.add('opacity-0'); content.classList.remove('scale-100'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
    modal.addEventListener('click', function(e) { if (e.target === this) closeStockInModal(); });
</script>
@endpush