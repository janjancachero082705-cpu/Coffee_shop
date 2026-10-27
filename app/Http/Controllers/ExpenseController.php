<?php
namespace App\Http\Controllers;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest('expense_date')->paginate(10);
        $totalExpenses = Expense::sum('amount');
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