@extends('layouts.admin')
@section('title','Record Purchase')
@section('subtitle','Cherry purchase from supplier')
@section('content')
<form method="POST" action="{{ route('purchases.store') }}" class="space-y-5">
    @csrf
    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Date *</label><input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Supplier *</label><input type="text" name="supplier_name" required placeholder="e.g., Juan's Farm" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Item Type *</label>
                <select name="item_type" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                    <option value="cherry">Coffee Cherries</option>
                    <option value="green_bean">Green Beans</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Quantity *</label><input type="number" name="quantity" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Unit *</label>
                <select name="unit" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                    <option value="kg">kg</option>
                    <option value="pcs">pcs</option>
                </select>
            </div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Price per Unit *</label><input type="number" name="unit_price" step="0.01" min="0" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Payment Status *</label>
                <select name="payment_status" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Amount Paid</label><input type="number" name="amount_paid" step="0.01" min="0" value="0" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div class="md:col-span-2"><label class="block text-sm font-semibold text-coffee-800 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></textarea></div>
        </div>
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('purchases.index') }}" class="px-6 py-2 bg-coffee-100 text-coffee-700 rounded-lg font-semibold">Cancel</a>
        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-gold-600 to-gold-800 text-white font-bold rounded-lg"><i class="fas fa-save mr-1"></i> Record Purchase</button>
    </div>
</form>
@endsection