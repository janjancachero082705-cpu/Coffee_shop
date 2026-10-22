@extends('layouts.admin')
@section('title','New Production Batch')
@section('content')
<form method="POST" action="{{ route('production.store') }}" class="space-y-5 max-w-4xl" x-data="batchForm()">
    @csrf
    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="text-sm font-bold text-coffee-500 uppercase mb-4"><i class="fas fa-leaf mr-2 text-gold-600"></i> Input</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Date *</label><input type="date" name="production_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Cherries (kg) *</label><input type="number" name="cherries_input_kg" x-model.number="cherries" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Cost per kg *</label><input type="number" name="cherries_cost_per_kg" x-model.number="costPerKg" step="0.01" min="0" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="text-sm font-bold text-coffee-500 uppercase mb-4"><i class="fas fa-fire mr-2 text-gold-600"></i> Processing Costs</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Labor (₱)</label><input type="number" name="labor_cost" x-model.number="labor" step="0.01" min="0" value="0" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Processing (₱)</label><input type="number" name="processing_cost" x-model.number="processing" step="0.01" min="0" value="0" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Other (₱)</label><input type="number" name="other_cost" x-model.number="other" step="0.01" min="0" value="0" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="text-sm font-bold text-coffee-500 uppercase mb-4"><i class="fas fa-box mr-2 text-gold-600"></i> Output</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Product *</label>
                <select name="product_id" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                    <option value="">—</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Output Qty *</label><input type="number" name="output_quantity" x-model.number="output" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Unit *</label>
                <select name="output_unit" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                    <option value="kg">kg</option>
                    <option value="pcs">pcs</option>
                    <option value="pack">pack</option>
                </select>
            </div>
        </div>

        <div class="mt-4 p-4 bg-coffee-50 rounded-lg text-sm space-y-1">
            <div class="flex justify-between"><span>Cherries Cost</span><span class="font-bold" x-text="'₱' + (cherries * costPerKg).toLocaleString('en-PH',{minimumFractionDigits:2})"></span></div>
            <div class="flex justify-between"><span>Processing Costs</span><span class="font-bold" x-text="'₱' + (labor + processing + other).toLocaleString('en-PH',{minimumFractionDigits:2})"></span></div>
            <div class="flex justify-between pt-2 border-t border-coffee-200"><span>Total Cost</span><span class="font-black text-gold-600" x-text="'₱' + totalCost.toLocaleString('en-PH',{minimumFractionDigits:2})"></span></div>
            <div class="flex justify-between"><span>Cost per Unit</span><span class="font-bold" x-text="'₱' + costPerUnit.toLocaleString('en-PH',{minimumFractionDigits:2})"></span></div>
            <div class="flex justify-between"><span>Yield</span><span class="font-bold" x-text="yieldPct + '%'"></span></div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('production.index') }}" class="px-6 py-2 bg-coffee-100 text-coffee-700 rounded-lg font-semibold">Cancel</a>
        <button class="px-6 py-2 bg-gradient-to-r from-gold-600 to-gold-800 text-white font-bold rounded-lg"><i class="fas fa-save mr-1"></i> Save Batch</button>
    </div>
</form>
@push('scripts')
<script>
function batchForm() {
    return {
        cherries: 0, costPerKg: 0, labor: 0, processing: 0, other: 0, output: 0,
        get totalCost() { return (this.cherries*this.costPerKg) + this.labor + this.processing + this.other; },
        get costPerUnit() { return this.output > 0 ? this.totalCost / this.output : 0; },
        get yieldPct() { return this.cherries > 0 ? ((this.output/this.cherries)*100).toFixed(1) : 0; },
    }
}
</script>
@endpush
@endsection