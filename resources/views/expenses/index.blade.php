@extends('layouts.admin')
@section('title', 'Expenses')
@section('subtitle', 'Record operational costs and misc expenses')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- FORM --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sticky top-24">
            <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-200 pb-3">Record Expense</h3>
            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Date</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Category</label>
                    <select name="category" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 outline-none">
                        <option value="Gas/Fuel">Gas / Fuel</option>
                        <option value="Transport">Transport / Delivery</option>
                        <option value="Labor">Labor / Wages</option>
                        <option value="Utilities">Utilities (Kuryente/Tubig)</option>
                        <option value="Supplies">Office/Farm Supplies</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Description (Optional)</label>
                    <input type="text" name="description" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 outline-none" placeholder="e.g. Gas for delivery to Bukidnon">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Amount (&#8369;)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 outline-none font-bold text-slate-800">
                </div>
                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-md shadow-sm transition-all flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-save"></i> Save Expense
                </button>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden h-full">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Expense History</h3>
                <div class="text-sm font-bold text-slate-900">Total: <span class="text-red-600">&#8369;{{ number_format($totalExpenses, 2) }}</span></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($expenses as $e)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 text-slate-600">{{ \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-3 font-medium text-slate-800">{{ $e->category }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $e->description ?? '—' }}</td>
                                <td class="px-6 py-3 text-right font-bold text-red-600">- &#8369;{{ number_format($e->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-8 text-slate-500">No expenses recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-slate-200 bg-white">{{ $expenses->links() }}</div>
        </div>
    </div>
</div>
@endsection