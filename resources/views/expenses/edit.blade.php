@extends('layouts.admin')
@section('title','Edit Expense')
@section('content')
<form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-5 max-w-3xl">
    @csrf @method('PUT')
    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Date *</label><input type="date" name="expense_date" value="{{ $expense->expense_date->format('Y-m-d') }}" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Category *</label>
                <select name="category" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                    @foreach(['cherry_purchase','labor','processing','packaging','utilities','transportation','harvesting','other'] as $c)
                        <option value="{{ $c }}" {{ $expense->category === $c ? 'selected':'' }}>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2"><label class="block text-sm font-semibold text-coffee-800 mb-1">Description *</label><input type="text" name="description" value="{{ $expense->description }}" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Amount *</label><input type="number" name="amount" step="0.01" min="0" value="{{ $expense->amount }}" required class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
            <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Supplier</label><input type="text" name="supplier_name" value="{{ $expense->supplier_name }}" class="w-full px-4 py-2 border border-coffee-200 rounded-lg"></div>
        </div>
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('expenses.index') }}" class="px-6 py-2 bg-coffee-100 text-coffee-700 rounded-lg font-semibold">Cancel</a>
        <button class="px-6 py-2 bg-gradient-to-r from-gold-600 to-gold-800 text-white font-bold rounded-lg">Update</button>
    </div>
</form>
@endsection