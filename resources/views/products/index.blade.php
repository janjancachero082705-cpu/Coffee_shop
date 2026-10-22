@extends('layouts.admin')
@section('title', 'Products')
@section('subtitle', 'Manage product catalog and variants')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Products</div><div class="text-3xl font-black text-espresso-900">{{ $products->total() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-espresso-50 text-espresso-600 flex items-center justify-center text-lg"><i class="fas fa-box"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Low Stock</div><div class="text-3xl font-black text-amber-600">{{ \App\Models\ProductVariant::where('stock', '<=', 5)->count() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg"><i class="fas fa-exclamation-triangle"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Active</div><div class="text-3xl font-black text-emerald-600">{{ \App\Models\Product::where('is_active',true)->count() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fas fa-check-circle"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Categories</div><div class="text-3xl font-black text-caramel-600">{{ $categories->count() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-caramel-50 text-caramel-600 flex items-center justify-center text-lg"><i class="fas fa-layer-group"></i></div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-espresso-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-espresso-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-espresso-50/30">
        <form method="GET" action="{{ route('products.index') }}" id="filterForm" class="flex flex-wrap gap-3 items-center flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or SKU..." class="px-3 py-2 bg-white border border-espresso-200 rounded-lg text-sm focus:border-caramel-500 outline-none w-64 shadow-sm">
            <select name="category" id="categorySelect" class="px-3 py-2 bg-white border border-espresso-200 rounded-lg text-sm focus:border-caramel-500 outline-none shadow-sm text-espresso-700 font-medium">
                <option value="">All Categories</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->emoji }} {{ $c->name }}</option>@endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-white border border-espresso-200 text-espresso-700 rounded-lg font-semibold text-sm hover:bg-espresso-50 transition-colors shadow-sm">Filter</button>
        </form>
        <a href="{{ route('products.create') }}" class="px-5 py-2.5 bg-espresso-900 hover:bg-espresso-800 text-white rounded-lg font-semibold text-sm shadow-sm transition-all flex items-center gap-2"><i class="fas fa-plus"></i> Add Product</a>
    </div>

    @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-espresso-200 text-espresso-500 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4 w-32">SKU</th>
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-center">Total Stock</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-espresso-100">
                    @foreach($products as $p)
                        <tr class="hover:bg-espresso-50/50 transition-colors odd:bg-white even:bg-espresso-50/20 group">
                            <td class="px-6 py-4"><span class="font-mono text-xs font-bold text-espresso-700 bg-espresso-100 px-2 py-1 rounded border border-espresso-200">{{ $p->sku }}</span></td>
                            <td class="px-6 py-4">
                                <button type="button" onclick="openVariantModal('{{ addslashes($p->name) }}', '{{ base64_encode(json_encode($p->variants)) }}')" class="text-left group-hover:text-caramel-600 transition-colors">
                                    <div class="font-bold text-espresso-900 flex items-center gap-2">{{ $p->name }} <i class="fas fa-external-link-alt text-[9px] text-espresso-400"></i></div>
                                    <div class="text-[10px] text-espresso-500">{{ ucfirst($p->unit_type) }}</div>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-espresso-600">@if($p->category) {{ $p->category->emoji }} {{ $p->category->name }} @else — @endif</td>
                            <td class="px-6 py-4 text-center">
                                @php $totalStock = $p->variants->sum('stock'); @endphp
                                <span class="{{ $totalStock <= 5 ? 'text-red-600 bg-red-50 border-red-200' : 'text-emerald-600 bg-emerald-50 border-emerald-200' }} px-2.5 py-1 rounded-md text-[10px] font-bold border uppercase tracking-wide">
                                    {{ $totalStock }} Units
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick="openVariantModal('{{ addslashes($p->name) }}', '{{ base64_encode(json_encode($p->variants)) }}')" class="w-7 h-7 inline-flex items-center justify-center rounded bg-white border border-espresso-200 text-caramel-600 hover:bg-caramel-50 transition-colors"><i class="fas fa-list text-xs"></i></button>
                                    <a href="{{ route('products.edit', $p) }}" class="w-7 h-7 inline-flex items-center justify-center rounded bg-white border border-espresso-200 text-blue-600 hover:bg-blue-50 transition-colors"><i class="fas fa-edit text-xs"></i></a>
                                    <form action="{{ route('products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="w-7 h-7 inline-flex items-center justify-center rounded bg-white border border-espresso-200 text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-trash text-xs"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-espresso-200 bg-white">{{ $products->withQueryString()->links() }}</div>
    @else
        <div class="text-center py-16 text-espresso-500">
            <i class="fas fa-box-open text-4xl mb-3 text-espresso-300"></i>
            <h3 class="text-lg font-bold text-espresso-800 mb-1">No products found</h3>
        </div>
    @endif
</div>

{{-- MODAL (SaaS Style) --}}
<div id="variantModal" class="fixed inset-0 z-50 hidden bg-espresso-900/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-200">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-200 border border-espresso-200" id="modalContent">
        <div class="px-6 py-4 border-b border-espresso-200 flex justify-between items-center bg-espresso-50">
            <div><h3 class="text-lg font-bold text-espresso-900" id="modalProductName">Product</h3><p class="text-xs text-espresso-500">Variant details</p></div>
            <button onclick="closeVariantModal()" class="w-7 h-7 flex items-center justify-center rounded bg-white border border-espresso-200 text-espresso-500 hover:text-red-500 transition-colors"><i class="fas fa-times text-xs"></i></button>
        </div>
        <div class="p-6 max-h-[60vh] overflow-y-auto" id="modalVariantsList"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('categorySelect').addEventListener('change', function() { document.getElementById('filterForm').submit(); });
    function openVariantModal(productName, encodedVariants) {
        const variants = JSON.parse(atob(encodedVariants));
        document.getElementById('modalProductName').innerText = productName;
        let html = '';
        if (variants.length === 0) { html = `<div class="text-center py-6 text-espresso-500 text-sm">No variants configured.</div>`; } 
        else {
            html += `<div class="space-y-2">`;
            variants.forEach(v => {
                const stockClass = v.stock <= 5 ? 'text-red-700 bg-red-50 border-red-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200';
                html += `<div class="flex items-center justify-between bg-white border border-espresso-200 p-3 rounded-lg shadow-sm"><div><div class="font-bold text-espresso-900 text-sm">${v.name}</div><div class="text-[10px] text-espresso-500">Unit: ${v.unit}</div></div><div class="text-right"><div class="font-bold text-espresso-900 text-sm">&#8369;${parseFloat(v.price).toFixed(2)}</div><div class="mt-1"><span class="${stockClass} px-2 py-0.5 rounded text-[9px] font-bold border uppercase tracking-wider">Stock: ${v.stock}</span></div></div></div>`;
            });
            html += `</div>`;
        }
        document.getElementById('modalVariantsList').innerHTML = html;
        const modal = document.getElementById('variantModal'); const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }
    function closeVariantModal() {
        const modal = document.getElementById('variantModal'); const content = document.getElementById('modalContent');
        modal.classList.add('opacity-0'); content.classList.remove('scale-100'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 200);
    }
    document.getElementById('variantModal').addEventListener('click', function(e) { if (e.target === this) closeVariantModal(); });
</script>
@endpush