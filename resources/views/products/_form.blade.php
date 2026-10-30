@php
    $isEdit = isset($product);
    $action = $isEdit ? route('products.update', $product) : route('products.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6" enctype="multipart/form-data">
    @csrf @method($method)

    {{-- CARD 1: PRODUCT INFORMATION --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-ink-800 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-box text-brand-500"></i> Product Information
            </h3>
            <label class="flex items-center gap-2 text-xs text-ink-700 font-bold bg-white px-3 py-1.5 rounded-lg border border-ink-200 cursor-pointer hover:bg-ink-50 transition-colors shadow-sm">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }} class="rounded text-brand-500 focus:ring-brand-400 w-4 h-4"> 
                Active Status
            </label>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[10px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">SKU <span class="text-red-500">*</span></label>
                <input type="text" name="sku" required value="{{ old('sku', $product->sku ?? 'SKU-' . strtoupper(Str::random(5))) }}" class="input @error('sku') !border-red-500 @enderror">
                @error('sku') <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">Product Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $product->name ?? '') }}" class="input @error('name') !border-red-500 @enderror">
                @error('name') <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">Category</label>
                <select name="category_id" class="input cursor-pointer @error('category_id') !border-red-500 @enderror">
                    <option value="">— Select Category —</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id', $product->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->emoji }} {{ $c->name }}</option>@endforeach
                </select>
                @error('category_id') <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">Base Unit <span class="text-red-500">*</span></label>
                <select name="unit_type" required class="input cursor-pointer @error('unit_type') !border-red-500 @enderror">
                    @foreach(['grams', 'kg', 'pcs', 'pack', 'bundle', 'seedling'] as $u)<option value="{{ $u }}" {{ old('unit_type', $product->unit_type ?? 'grams') === $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>@endforeach
                </select>
                @error('unit_type') <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">Product Image (Optional)</label>
                <div class="flex items-center gap-4">
                    @if($isEdit && $product->image)
                        <div class="w-16 h-16 rounded-lg border border-ink-200 overflow-hidden flex-shrink-0"><img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover"></div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="block w-full text-sm text-ink-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-all cursor-pointer">
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-ink-600 mb-1.5 uppercase tracking-wider">Description (Optional)</label>
                <textarea name="description" rows="2" class="input" placeholder="Brief description...">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- CARD 2: VARIANTS --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-ink-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-layer-group text-brand-500"></i> Product Variants
                </h3>
                <p class="text-xs text-ink-500 mt-1 font-medium">Set prices based on Pack, Pcs, or Weight.</p>
            </div>
            <button type="button" id="addVariantBtn" class="btn btn-primary self-start md:self-auto">
                <i class="fas fa-plus"></i> Add Variant
            </button>
        </div>

        <div class="p-6 bg-ink-50/30">
            <div id="variantsContainer" class="space-y-4">
                
                @if($isEdit && $product->variants->count() > 0)
                    @foreach($product->variants as $index => $variant)
                        <div class="variant-row bg-white border border-ink-200 rounded-xl shadow-sm relative group transition-all hover:border-brand-300">
                            <div class="px-4 py-2 border-b border-ink-100 bg-ink-50 flex justify-between items-center rounded-t-xl">
                                <span class="text-[10px] font-bold text-ink-500 uppercase tracking-wider">Variant #{{ $index + 1 }}</span>
                                <button type="button" class="remove-variant text-ink-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors"><i class="fas fa-trash text-xs"></i></button>
                            </div>
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                            
                            <div class="p-4 grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Name / Size <span class="text-red-500">*</span></label>
                                    <input type="text" name="variants[{{ $index }}][name]" required value="{{ $variant->name ?? $variant->weight }}" class="input @error("variants.$index.name") !border-red-500 @enderror" placeholder="e.g. 250g / Small">
                                    @error("variants.$index.name") <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Unit</label>
                                    <select name="variants[{{ $index }}][unit]" id="unit_select_{{ $index }}" onchange="updatePriceLabel({{ $index }})" class="input cursor-pointer">
                                        <option value="g" {{ $variant->unit == 'g' ? 'selected' : '' }}>grams (g)</option>
                                        <option value="kg" {{ $variant->unit == 'kg' ? 'selected' : '' }}>kilograms (kg)</option>
                                        <option value="pcs" {{ $variant->unit == 'pcs' ? 'selected' : '' }}>pieces (pcs)</option>
                                        <option value="pack" {{ $variant->unit == 'pack' ? 'selected' : '' }}>packs</option>
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label id="price_label_{{ $index }}" class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Price per {{ ucfirst($variant->unit) }} (&#8369;) <span class="text-red-500">*</span></label>
                                    <input type="number" name="variants[{{ $index }}][price]" step="0.01" required max="999999" maxlength="9" value="{{ $variant->price }}" class="input font-bold @error("variants.$index.price") !border-red-500 @enderror">
                                    @error("variants.$index.price") <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Cost (&#8369;) <span class="text-[9px] text-ink-400">(Optional)</span></label>
                                    <input type="number" name="variants[{{ $index }}][cost_price]" step="0.01" max="999999" maxlength="9" value="{{ $variant->cost_price }}" class="input bg-ink-50" placeholder="0.00">
                                </div>
                                <div class="md:col-span-12 mt-2">
                                    <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Variant Image (Optional)</label>
                                    <div class="flex items-center gap-3 bg-ink-50 p-2 rounded-lg border border-ink-200 border-dashed">
                                        @if($variant->image)
                                            <div class="w-10 h-10 rounded border border-ink-200 overflow-hidden flex-shrink-0"><img src="{{ Storage::url($variant->image) }}" class="w-full h-full object-cover"></div>
                                        @else
                                            <div class="w-10 h-10 rounded border border-ink-200 flex items-center justify-center flex-shrink-0 bg-white"><i class="fas fa-image text-ink-300"></i></div>
                                        @endif
                                        <input type="file" name="variants[{{ $index }}][image]" accept="image/*" class="block w-full text-xs text-ink-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-white file:text-ink-700 file:shadow-sm hover:file:bg-ink-100 cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- DEFAULT ROW --}}
                    <div class="variant-row bg-white border border-ink-200 rounded-xl shadow-sm relative group transition-all hover:border-brand-300">
                        <div class="px-4 py-2 border-b border-ink-100 bg-ink-50 flex justify-between items-center rounded-t-xl">
                            <span class="text-[10px] font-bold text-ink-500 uppercase tracking-wider">Variant #1</span>
                            <button type="button" class="remove-variant text-ink-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors opacity-50 cursor-not-allowed"><i class="fas fa-trash text-xs"></i></button>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Name / Size <span class="text-red-500">*</span></label>
                                <input type="text" name="variants[0][name]" required class="input @error('variants.0.name') !border-red-500 @enderror" placeholder="e.g. 250g / Small">
                                @error('variants.0.name') <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Unit</label>
                                <select name="variants[0][unit]" id="unit_select_0" onchange="updatePriceLabel(0)" class="input cursor-pointer">
                                    <option value="g">grams (g)</option>
                                    <option value="kg">kilograms (kg)</option>
                                    <option value="pcs">pieces (pcs)</option>
                                    <option value="pack">packs</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label id="price_label_0" class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Price per Unit (&#8369;) <span class="text-red-500">*</span></label>
                                <input type="number" name="variants[0][price]" step="0.01" required class="input font-bold @error('variants.0.price') !border-red-500 @enderror" placeholder="190" max="999999" maxlength="9">
                                @error('variants.0.price') <p class="text-[10px] text-red-500 mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Cost (&#8369;) <span class="text-[9px] text-ink-400">(Optional)</span></label>
                                <input type="number" name="variants[0][cost_price]" step="0.01" class="input bg-ink-50" placeholder="0.00" max="999999" maxlength="9">
                            </div>
                            <div class="md:col-span-12 mt-2">
                                <label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Variant Image (Optional)</label>
                                <div class="flex items-center gap-3 bg-ink-50 p-2 rounded-lg border border-ink-200 border-dashed">
                                    <div class="w-10 h-10 rounded border border-ink-200 flex items-center justify-center flex-shrink-0 bg-white"><i class="fas fa-image text-ink-300"></i></div>
                                    <input type="file" name="variants[0][image]" accept="image/*" class="block w-full text-xs text-ink-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-white file:text-ink-700 file:shadow-sm hover:file:bg-ink-100 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-ink-200">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $isEdit ? 'Update Product' : 'Save Product' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    // Function to update price label based on unit selection
    function updatePriceLabel(index) {
        const unitSelect = document.getElementById(`unit_select_${index}`);
        const priceLabel = document.getElementById(`price_label_${index}`);
        
        if(!unitSelect || !priceLabel) return;

        const unit = unitSelect.value;
        let unitText = 'Unit';

        if (unit === 'g') unitText = 'Gram';
        else if (unit === 'kg') unitText = 'Kilo';
        else if (unit === 'pcs') unitText = 'Piece';
        else if (unit === 'pack') unitText = 'Pack';

        priceLabel.innerHTML = `Price per ${unitText} (&#8369;) <span class="text-red-500">*</span>`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        let variantIndex = {{ ($isEdit && $product->variants->count() > 0) ? $product->variants->count() : 1 }};
        const container = document.getElementById('variantsContainer');
        const addBtn = document.getElementById('addVariantBtn');

        // Run initial label update for existing rows
        document.querySelectorAll('select[id^="unit_select_"]').forEach(select => {
            const index = select.id.split('_')[2];
            updatePriceLabel(index);
        });

        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'variant-row bg-white border border-ink-200 rounded-xl shadow-sm relative group transition-all hover:border-brand-300';
            row.innerHTML = `
                <div class="px-4 py-2 border-b border-ink-100 bg-ink-50 flex justify-between items-center rounded-t-xl">
                    <span class="text-[10px] font-bold text-ink-500 uppercase tracking-wider">Variant #${variantIndex + 1}</span>
                    <button type="button" class="remove-variant text-ink-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors"><i class="fas fa-trash text-xs"></i></button>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-4"><label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Name / Size <span class="text-red-500">*</span></label><input type="text" name="variants[${variantIndex}][name]" required class="input" placeholder="e.g. 500g / Large"></div>
                    <div class="md:col-span-2"><label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Unit</label><select name="variants[${variantIndex}][unit]" id="unit_select_${variantIndex}" onchange="updatePriceLabel(${variantIndex})" class="input cursor-pointer"><option value="g">grams (g)</option><option value="kg">kilograms (kg)</option><option value="pcs">pieces (pcs)</option><option value="pack">packs</option></select></div>
                    <div class="md:col-span-3"><label id="price_label_${variantIndex}" class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Price per Unit (&#8369;) <span class="text-red-500">*</span></label><input type="number" name="variants[${variantIndex}][price]" step="0.01" required class="input font-bold" max="999999" maxlength="9"></div>
                    <div class="md:col-span-3"><label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Cost (&#8369;) <span class="text-[9px] text-ink-400">(Optional)</span></label><input type="number" name="variants[${variantIndex}][cost_price]" step="0.01" class="input bg-ink-50" placeholder="0.00" max="999999" maxlength="9"></div>
                    <div class="md:col-span-12 mt-2"><label class="block text-[10px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Variant Image (Optional)</label><div class="flex items-center gap-3 bg-ink-50 p-2 rounded-lg border border-ink-200 border-dashed"><div class="w-10 h-10 rounded border border-ink-200 flex items-center justify-center flex-shrink-0 bg-white"><i class="fas fa-image text-ink-300"></i></div><input type="file" name="variants[${variantIndex}][image]" accept="image/*" class="block w-full text-xs text-ink-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-white file:text-ink-700 file:shadow-sm hover:file:bg-ink-100 cursor-pointer"></div></div>
                </div>
            `;
            container.appendChild(row);
            // Initialize label for the newly added row
            updatePriceLabel(variantIndex);
            variantIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-variant')) {
                const row = e.target.closest('.variant-row');
                if (document.querySelectorAll('.variant-row').length > 1) { row.remove(); } 
                else { alert('You need at least one variant.'); }
            }
        });
    });
</script>
@endpush