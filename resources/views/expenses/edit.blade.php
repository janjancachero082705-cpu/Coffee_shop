@extends('layouts.admin')
@section('title','Edit Expense')
@section('content')
<form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-5 max-w-3xl mx-auto">
    @csrf @method('PUT')
    <div class="card p-6">
        <div class="mb-6 border-b border-ink-200 pb-4">
            <h3 class="text-lg font-bold text-ink-900">Edit Expense</h3>
            <p class="text-xs text-ink-500 mt-1">Update the details of this expense record.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Date <span class="text-red-500">*</span></label>
                <input type="date" name="expense_date" value="{{ $expense->expense_date->format('Y-m-d') }}" required class="input">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Category <span class="text-red-500">*</span></label>
                <select name="category" required class="input cursor-pointer">
                    @foreach(['cherry_purchase','labor','processing','packaging','utilities','transportation','harvesting','other'] as $c)
                        <option value="{{ $c }}" {{ $expense->category === $c ? 'selected':'' }}>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Description <span class="text-red-500">*</span></label>
                <input type="text" name="description" value="{{ $expense->description }}" required class="input">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Amount (&#8369;) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" step="0.01" min="0" value="{{ $expense->amount }}" required class="input font-bold">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-ink-600 mb-1 uppercase tracking-wider">Supplier</label>
                <input type="text" name="supplier_name" value="{{ $expense->supplier_name }}" class="input">
            </div>
        </div>
    </div>
    
    <div class="flex justify-end gap-3 pt-4">
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Update Expense
        </button>
    </div>
</form>
@endsection