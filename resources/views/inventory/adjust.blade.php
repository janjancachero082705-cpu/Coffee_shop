@extends('layouts.admin')
@section('title','Adjust Stock')
@section('subtitle',$product->name . ' — Current: ' . number_format($product->stock,2) . ' ' . $product->unit_type)
@section('content')
<form method="POST" action="{{ route('inventory.adjust.store', $product) }}" class="max-w-2xl space-y-5">
    @csrf
    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="text-sm font-bold text-coffee-500 uppercase mb-4"><i class="fas fa-sliders-h mr-2 text-gold-600"></i> Manual Stock Adjustment</h3>

        <div class="space-y-4">
            <div class="flex justify-between p-3 bg-coffee-50 rounded-lg">
                <span class="text-sm text-coffee-600">Current Stock</span>
                <span class="font-black text-coffee-900">{{ number_format($product->stock, 2) }} {{ $product->unit_type }}</span>
            </div>

            <div>
                <label class="block text-sm font-semibold text-coffee-800 mb-1">New Stock Quantity *</label>
                <input type="number" name="new_quantity" step="0.01" min="0" required
                       value="{{ $product->stock }}"
                       class="w-full px-4 py-3 border border-coffee-200 rounded-lg focus:border-gold-500 outline-none text-lg font-bold">
                <p class="text-xs text-coffee-500 mt-1">Ibutang ang total quantity nga dapat naa. System auto-calculate sa difference.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-coffee-800 mb-1">Reason / Notes *</label>
                <textarea name="notes" rows="3" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg focus:border-gold-500 outline-none"
                          placeholder="e.g., Physical count correction, damaged items, replenishment..."></textarea>
            </div>

            <div class="p-3 bg-amber-50 border-l-4 border-amber-500 rounded text-sm text-amber-800">
                <i class="fas fa-info-circle mr-1"></i>
                Kining adjustment ma-log sa Stock Card with timestamp.
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('inventory.index') }}" class="px-6 py-2 bg-coffee-100 text-coffee-700 rounded-lg font-semibold">Cancel</a>
        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-gold-600 to-gold-800 text-white font-bold rounded-lg hover:shadow-lg">
            <i class="fas fa-check mr-1"></i> Save Adjustment
        </button>
    </div>
</form>
@endsection