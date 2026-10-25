@extends('layouts.admin')
@section('title','Add Expense')
@section('content')
<form method="POST" action="{{ route('expenses.store') }}" class="space-y-5 max-w-3xl">
    @csrf
    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Date *</label><input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Category *</label>
                <select name="category" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                    @foreach(['cherry_purchase'=>'Cherry Purchase','labor'=>'Labor (Suhol)','processing'=>'Processing','packaging'=>'Packaging','utilities'=>'Utilities','transportation'=>'Transportation','harvesting'=>'Harvesting','other'=>'Other'] as $v=>$l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2"><label class="block text-sm font-semibold text-coffee-800 mb-1">Description *</label><input type="text" name="description" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Amount (&#8369;) *</label><input type="number" name="amount" step="0.01" min="0" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Supplier</label><input type="text" name="supplier_name" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Quantity</label><input type="number" name="quantity" step="0.01" min="0" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Unit</label><input type="text" name="unit" placeholder="kg, pcs, etc." class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div class="md:col-span-2"><label class="block text-sm font-semibold text-coffee-800 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></textarea></div>
        </div>
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('expenses.index') }}" class="px-6 py-2 bg-coffee-100 text-coffee-700 rounded-lg font-semibold">Cancel</a>
        <button class="px-6 py-2 bg-gradient-to-r from-gold-600 to-gold-800 text-white font-bold rounded-lg"><i class="fas fa-save mr-1"></i> Save Expense</button>
    </div>
</form>
@endsection