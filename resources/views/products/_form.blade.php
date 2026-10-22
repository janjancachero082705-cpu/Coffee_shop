@php
    $isEdit = isset($product);
    $action = $isEdit ? route('products.update', $product) : route('products.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf @method($method)

    <div class="bg-white rounded-2xl shadow-card p-6 border border-espresso-100">
        <div class="flex justify-between items-center mb-4 border-b border-espresso-100 pb-3">
            <h3 class="text-sm font-bold text-espresso-500 uppercase tracking-widest"><i class="fas fa-box mr-2 text-caramel-500"></i> Product Info</h3>
            <label class="flex items-center gap-2 text-sm text-espresso-700 font-bold bg-espresso-50 px-3 py-1.5 rounded-lg border border-espresso-100 cursor-pointer hover:bg-espresso-100 transition-colors">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }} class="rounded text-caramel-500 focus:ring-caramel-400 w-4 h-4"> Active
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-espresso-800 mb-1">SKU <span class="text-red-500">*</span></label>
                <input type="text" name="sku" required value="{{ old('sku', $product->sku ?? 'SKU-' . strtoupper(Str::random(5))) }}" class="w-full px-4 py-2 border border-espresso-200 rounded-lg focus:border-caramel-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-espresso-800 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $product->name ?? '') }}" class="w-full px-4 py-2 border border-espresso-200 rounded-lg focus:border-caramel-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-espresso-800 mb-1">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-espresso-200 rounded-lg focus:border-caramel-400 outline-none">
                    <option value="">—</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id', $product->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->emoji }} {{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-espresso-800 mb-1">Base Unit <span class="text-red-500">*</span></label>
                <select name="unit_type" required class="w-full px-4 py-2 border border-espresso-200 rounded-lg focus:border-caramel-400 outline-none">
                    @foreach(['grams', 'kg', 'pcs', 'pack', 'bundle', 'seedling'] as $u)<option value="{{ $u }}" {{ old('unit_type', $product->unit_type ?? 'grams') === $u ? 'selected' : '' }}>{{ $u }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-espresso-800 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border border-espresso-200 rounded-lg focus:border-caramel-400 outline-none">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6 border-l-4 border-caramel-400">
        <div class="flex items-center justify-between mb-4 border-b border-espresso-100 pb-3">
            <div><h3 class="text-sm font-bold text-espresso-500 uppercase tracking-widest"><i class="fas fa-balance-scale mr-2 text-caramel-500"></i> Product Variants & Pricing</h3><p class="text-xs text-espresso-500 mt-1">Set the size, price, and stock for each variant.</p></div>
            <button type="button" id="addVariantBtn" class="px-4 py-2 bg-caramel-100 text-caramel-700 font-bold rounded-lg hover:bg-caramel-200 text-sm transition-colors shadow-sm"><i class="fas fa-plus"></i> Add Variant</button>
        </div>

        <div id="variantsContainer" class="space-y-3">
            @if($isEdit && $product->variants->count() > 0)
                @foreach($product->variants as $index => $variant)
                    <div class="grid grid-cols-12 gap-2 items-end variant-row bg-espresso-50 p-3 rounded-xl border border-espresso-100">
                        <div class="col-span-3"><label class="block text-xs font-bold text-espresso-600 mb-1">Name / Size</label><input type="text" name="variants[{{ $index }}][weight]" required value="{{ $variant->name ?? $variant->weight }}" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm" placeholder="e.g. 250g or Small"></div>
                        <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Unit</label><select name="variants[{{ $index }}][unit]" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm"><option value="g" {{ $variant->unit == 'g' ? 'selected' : '' }}>g</option><option value="kg" {{ $variant->unit == 'kg' ? 'selected' : '' }}>kg</option><option value="pcs" {{ $variant->unit == 'pcs' ? 'selected' : '' }}>pcs</option></select></div>
                        <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Price (₱) <span class="text-red-500">*</span></label><input type="number" name="variants[{{ $index }}][price]" step="0.01" required value="{{ $variant->price }}" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm bg-white"></div>
                        <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Cost (₱)</label><input type="number" name="variants[{{ $index }}][cost_price]" step="0.01" value="{{ $variant->cost_price }}" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm" placeholder="0.00"></div>
                        <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Stock <span class="text-red-500">*</span></label><input type="number" name="variants[{{ $index }}][stock]" min="0" required value="{{ $variant->stock ?? 0 }}" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm bg-white"></div>
                        <div class="col-span-1 flex justify-center pb-1"><button type="button" class="remove-variant w-8 h-8 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-lg"><i class="fas fa-trash text-xs"></i></button></div>
                    </div>
                @endforeach
            @else
                <div class="grid grid-cols-12 gap-2 items-end variant-row bg-espresso-50 p-3 rounded-xl border border-espresso-100">
                    <div class="col-span-3"><label class="block text-xs font-bold text-espresso-600 mb-1">Name / Size</label><input type="text" name="variants[0][weight]" required class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm" placeholder="e.g. 250g or Small"></div>
                    <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Unit</label><select name="variants[0][unit]" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm"><option value="g">g</option><option value="kg">kg</option><option value="pcs">pcs</option></select></div>
                    <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Price (₱) <span class="text-red-500">*</span></label><input type="number" name="variants[0][price]" step="0.01" required class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm bg-white" placeholder="190"></div>
                    <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Cost (₱)</label><input type="number" name="variants[0][cost_price]" step="0.01" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm" placeholder="0.00"></div>
                    <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Stock <span class="text-red-500">*</span></label><input type="number" name="variants[0][stock]" min="0" required value="0" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm bg-white"></div>
                    <div class="col-span-1 flex justify-center pb-1"><button type="button" class="remove-variant w-8 h-8 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-lg opacity-50 cursor-not-allowed"><i class="fas fa-trash text-xs"></i></button></div>
                </div>
            @endif
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('products.index') }}" class="px-6 py-2.5 bg-espresso-100 text-espresso-700 rounded-lg font-semibold hover:bg-espresso-200">Cancel</a>
        <button type="submit" class="px-6 py-2.5 bg-caramel-400 hover:bg-caramel-500 text-espresso-900 font-bold rounded-lg shadow-md transition-all"><i class="fas fa-save mr-1"></i> {{ $isEdit ? 'Update Product' : 'Save Product' }}</button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let variantIndex = {{ ($isEdit && $product->variants->count() > 0) ? $product->variants->count() : 1 }};
        const container = document.getElementById('variantsContainer');
        const addBtn = document.getElementById('addVariantBtn');

        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-2 items-end variant-row bg-espresso-50 p-3 rounded-xl border border-espresso-100';
            row.innerHTML = `
                <div class="col-span-3"><label class="block text-xs font-bold text-espresso-600 mb-1">Name / Size</label><input type="text" name="variants[${variantIndex}][weight]" required placeholder="e.g. 500g or Large" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm"></div>
                <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Unit</label><select name="variants[${variantIndex}][unit]" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm"><option value="g">g</option><option value="kg">kg</option><option value="pcs">pcs</option></select></div>
                <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Price (₱) <span class="text-red-500">*</span></label><input type="number" name="variants[${variantIndex}][price]" step="0.01" required placeholder="190" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm bg-white"></div>
                <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Cost (₱)</label><input type="number" name="variants[${variantIndex}][cost_price]" step="0.01" placeholder="0.00" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm"></div>
                <div class="col-span-2"><label class="block text-xs font-bold text-espresso-600 mb-1">Stock <span class="text-red-500">*</span></label><input type="number" name="variants[${variantIndex}][stock]" min="0" required value="0" class="w-full px-3 py-2 border border-espresso-200 rounded-lg text-sm bg-white"></div>
                <div class="col-span-1 flex justify-center pb-1"><button type="button" class="remove-variant w-8 h-8 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-lg"><i class="fas fa-trash text-xs"></i></button></div>
            `;
            container.appendChild(row);
            variantIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-variant')) {
                const row = e.target.closest('.variant-row');
                if (document.querySelectorAll('.variant-row').length > 1) { row.remove(); } 
                else { alert('Kinahanglan naay bisan isa ka variant ang produkto.'); }
            }
        });
    });
</script>
@endpush