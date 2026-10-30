@extends('layouts.admin')
@section('title', 'Expenses')
@section('subtitle', 'Record operational costs and misc expenses')

@section('content')
<style>
    /* Pugson og pakita ang Calendar Icon */
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: block !important;
        opacity: 1 !important;
        cursor: pointer;
        filter: invert(30%);
        padding: 2px;
    }
    input[type="date"] {
        min-height: 34px !important;
        padding-right: 6px !important;
    }
</style>

<div class="w-full max-w-screen-2xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    
    {{-- FORM --}}
    <div class="lg:col-span-1">
        <div class="card p-4 md:p-6 lg:sticky lg:top-24">
            <h3 class="font-bold text-ink-900 mb-3 md:mb-4 border-b border-ink-200 pb-3 text-sm md:text-base">Record Expense</h3>
            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-3 md:space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Date</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="input !py-2 !text-xs md:!text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Category</label>
                    <select name="category" required class="input !py-2 !text-xs md:!text-sm cursor-pointer">
                        <option value="Gas/Fuel">Gas / Fuel</option>
                        <option value="Transport">Transport / Delivery</option>
                        <option value="Labor">Labor / Wages</option>
                        <option value="Utilities">Utilities (Kuryente/Tubig)</option>
                        <option value="Supplies">Office/Farm Supplies</option>
                        <option value="Production Cost">Production Cost</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Description (Optional)</label>
                    <input type="text" name="description" class="input !py-2 !text-xs md:!text-sm" placeholder="e.g. Gas for delivery">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Amount (&#8369;)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="input !py-2 !text-xs md:!text-sm font-bold text-ink-900">
                </div>
                <button type="submit" class="btn btn-primary w-full !py-2.5 !text-xs md:!text-sm">
                    <i class="fas fa-save"></i> Save Expense
                </button>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="lg:col-span-2 min-w-0">
        <div class="card overflow-hidden h-full flex flex-col min-w-0">
            
            {{-- HEADER (With Flex-Wrap) --}}
            <div class="px-4 md:px-6 py-4 border-b border-ink-200 bg-white flex flex-wrap justify-between items-center gap-3 shrink-0">
                
                {{-- Left: Title & Total --}}
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="font-bold text-ink-900 text-base whitespace-nowrap">Expense History</h3>
                    <div class="inline-flex items-center gap-2 bg-red-50 px-3 py-1 rounded-lg border border-red-100">
                        <span class="text-[10px] font-extrabold text-red-500 uppercase tracking-widest">Total:</span>
                        <span class="text-sm font-black text-red-600">₱{{ number_format($totalExpenses, 2) }}</span>
                    </div>
                </div>
                
                {{-- Right: Filters (Flex-Wrap) --}}
                <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    
                    {{-- Date Range --}}
                    <div class="flex items-center gap-1.5 w-full sm:w-auto">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="input !py-1.5 !px-2 !text-xs flex-1 sm:flex-none sm:w-[130px] cursor-pointer" title="Start Date">
                        <span class="text-ink-400 text-[10px] font-extrabold shrink-0">TO</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="input !py-1.5 !px-2 !text-xs flex-1 sm:flex-none sm:w-[130px] cursor-pointer" title="End Date">
                    </div>

                    {{-- Category --}}
                    <select name="category" onchange="this.form.submit()" class="input !py-1.5 !px-2 !text-xs w-full sm:w-[150px] cursor-pointer">
                        <option value="">All Categories</option>
                        @foreach($uniqueCategories ?? [] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    {{-- Clear Filter --}}
                    @if(request()->anyFilled(['start_date', 'end_date', 'category']))
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary !py-1.5 !px-3 !text-xs w-full sm:w-auto justify-center" title="Clear Filter">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- CATEGORY SUMMARY --}}
            @if(isset($categoryTotals) && $categoryTotals->count() > 0)
            <div class="px-4 md:px-6 py-3 border-b border-ink-200 bg-ink-50/50 shrink-0">
                <div class="flex flex-wrap gap-2">
                    @foreach($categoryTotals as $ct)
                        <div class="px-3 py-1.5 bg-white border border-ink-200 rounded-lg text-[11px] md:text-xs font-bold text-ink-700 flex items-center gap-2 shadow-sm">
                            <i class="fas fa-tag text-ink-400 text-[10px]"></i>
                            {{ $ct->category }}: 
                            <span class="text-red-600">₱{{ number_format($ct->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- TABLE --}}
            <div class="overflow-x-auto flex-1 w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="table-clean min-w-[600px] w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-right whitespace-nowrap">Amount</th>
                            <th class="text-center whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr class="group">
                                <td class="text-ink-500 font-medium text-xs md:text-sm whitespace-nowrap">{{ \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') }}</td>
                                <td class="font-bold text-ink-900 text-xs md:text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-ink-100 text-ink-600 border border-ink-200 uppercase tracking-wider">
                                        {{ $e->category }}
                                    </span>
                                </td>
                                <td class="text-ink-600 text-xs md:text-sm">{{ $e->description ?? '—' }}</td>
                                <td class="text-right font-bold text-red-600 text-xs md:text-sm whitespace-nowrap">- &#8369;{{ number_format($e->amount, 2) }}</td>
                                <td>
                                    <div class="flex items-center justify-center gap-1.5 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('expenses.edit', $e) }}" class="w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg text-brand-600 bg-brand-50 hover:bg-brand-100 transition-colors shadow-sm" title="Edit">
                                            <i class="fas fa-pen text-[10px] md:text-xs"></i>
                                        </a>
                                        <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Delete this expense?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors shadow-sm" title="Delete">
                                                <i class="fas fa-trash text-[10px] md:text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-12 text-ink-400 text-xs md:text-sm">No expenses recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="px-4 py-3 md:px-6 md:py-4 border-t border-ink-200 bg-ink-50 shrink-0 text-sm">{{ $expenses->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection