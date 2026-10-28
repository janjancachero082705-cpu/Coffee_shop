@php
    $isEdit = isset($purchase);
    $action = $isEdit ? route('purchases.update', $purchase) : route('purchases.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" class="max-w-2xl mx-auto space-y-6">
    @csrf @method($method)
    
    <div class="card p-6">
        <div class="mb-6 border-b border-ink-200 pb-4">
            <h3 class="text-lg font-bold text-ink-900 flex items-center gap-2">
                <i class="fas fa-box text-brand-500"></i> Purchase Order
            </h3>
            <p class="text-xs text-ink-500 mt-1">Record incoming stock from suppliers.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Supplier Name <span class="text-red-500">*</span></label>
                <input type="text" name="supplier_name" required value="{{ old('supplier_name', $purchase->supplier_name ?? '') }}" class="input" placeholder="e.g. Miarayon Farmers Coop">
            </div>
            
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Date <span class="text-red-500">*</span></label>
                <input type="date" name="purchase_date" required value="{{ old('purchase_date', $purchase->purchase_date ?? date('Y-m-d')) }}" class="input">
            </div>

            <div class="md:col-span-2 p-3 bg-ink-50 border border-ink-200 rounded-lg">
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Select Product to Add to Stock <span class="text-red-500">*</span></label>
                <select name="variant_id" required class="input cursor-pointer">
                    <option value="">Choose a variant...</option>
                    @foreach($variants as $v)
                        <option value="{{ $v->id }}" {{ old('variant_id', $purchase->variant_id ?? '') == $v->id ? 'selected' : '' }}>
                            {{ $v->product->name ?? 'Unknown' }} - {{ $v->name }} (Current Stock: {{ $v->stock }} {{ $v->unit }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-ink-500 mt-1">Note: Choosing a variant will add the quantity to its inventory stock.</p>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" step="1" min="1" required value="{{ old('quantity', $purchase- max="999999" maxlength="9">quantity ?? '') }}" class="input" placeholder="e.g. 50">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Cost per Unit (&#8369;) <span class="text-red-500">*</span></label>
                <input type="number" name="cost_per_unit" step="0.01" min="0" required value="{{ old('cost_per_unit', $purchase- max="999999" maxlength="9">cost_per_unit ?? '') }}" class="input" placeholder="e.g. 150.00">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> {{ $isEdit ? 'Update Purchase' : 'Save Purchase' }}
        </button>
    </div>
</form>