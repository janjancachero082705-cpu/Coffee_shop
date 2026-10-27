@extends('layouts.admin')
@section('title', 'Expenses')
@section('subtitle', 'Record operational costs and misc expenses')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- FORM --}}
    <div class="lg:col-span-1">
        <div class="card p-6 sticky top-24">
            <h3 class="font-bold text-ink-900 mb-4 border-b border-ink-200 pb-3">Record Expense</h3>
            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Date</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="input">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Category</label>
                    <select name="category" required class="input cursor-pointer">
                        <option value="Gas/Fuel">Gas / Fuel</option>
                        <option value="Transport">Transport / Delivery</option>
                        <option value="Labor">Labor / Wages</option>
                        <option value="Utilities">Utilities (Kuryente/Tubig)</option>
                        <option value="Supplies">Office/Farm Supplies</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Description (Optional)</label>
                    <input type="text" name="description" class="input" placeholder="e.g. Gas for delivery to Bukidnon">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Amount (&#8369;)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="input font-bold text-ink-900">
                </div>
                <button type="submit" class="btn btn-primary w-full !py-2.5">
                    <i class="fas fa-save"></i> Save Expense
                </button>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="lg:col-span-2">
        <div class="card overflow-hidden h-full flex flex-col">
            <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-ink-900">Expense History</h3>
                <div class="text-sm font-bold text-ink-900">Total: <span class="text-red-600">&#8369;{{ number_format($totalExpenses, 2) }}</span></div>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr class="group">
                                <td class="text-ink-500 font-medium">{{ \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') }}</td>
                                <td class="font-bold text-ink-900">{{ $e->category }}</td>
                                <td class="text-ink-600">{{ $e->description ?? '—' }}</td>
                                <td class="text-right font-bold text-red-600">- &#8369;{{ number_format($e->amount, 2) }}</td>
                                <td>
                                    <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{-- EDIT BUTTON --}}
                                        <a href="{{ route('expenses.edit', $e) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-brand-600 bg-brand-50 hover:bg-brand-100 transition-colors shadow-sm" title="Edit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        {{-- DELETE BUTTON --}}
                                        <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Delete this expense?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors shadow-sm" title="Delete">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-ink-400">No expenses recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-ink-200 bg-ink-50 shrink-0">{{ $expenses->links() }}</div>
        </div>
    </div>
</div>
@endsection