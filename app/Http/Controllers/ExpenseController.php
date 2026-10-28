<?php
namespace App\Http\Controllers;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
        public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Expense::query();

        // Filter pinaagi sa Start Date
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        
        // Filter pinaagi sa End Date
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        // Ang total kay mo-follow sa na-filter nga date range
        $totalExpenses = $query->sum('amount');
        
        // Pagination nga mo-preserve sa date filter inig page 2
        $expenses = $query->orderByDesc('expense_date')->paginate(15)->appends($request->query());

        return view('expenses.index', compact('expenses', 'totalExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01'
        ]);

        Expense::create($validated);
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
        return back()->with('success', 'Expense recorded successfully!');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $expense->update($validated);
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}