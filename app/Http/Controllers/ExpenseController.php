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
                
        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $totalExpenses = $query->sum('amount');        
        // Calculate totals per category
        $categoryTotals = (clone $query)->select('category', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
        
        // Pagination nga mo-preserve sa date filter inig page 2
                
        // Kuhaon ang tanang unique categories para sa dropdown
        $uniqueCategories = \App\Models\Expense::select('category')->distinct()->orderBy('category')->pluck('category');

        $expenses = $query->orderBy('category')->orderByDesc('expense_date')->paginate(15)->appends($request->query());

        return view('expenses.index', compact('expenses', 'totalExpenses', 'categoryTotals', 'uniqueCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01|max:999999'
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
            'amount' => 'required|numeric|min:0.01|max:999999'
        ]);

        $expense->update($validated);
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
        try { event(new \App\Events\DataUpdated()); } catch (\Exception $e) { /* Skip kung mapalong ang Reverb */ }
            \App\Models\ActivityLog::record('Expenses', 'Recorded', 'Added expense: ' . $request->category . ' (₱' . number_format($request->amount, 2) . ')', $expense); // . $request->category . ' (₱' . number_format($request->amount, 2) . ')');
            return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        \Illuminate\Support\Facades\Cache::forget('dashboard_metrics');
        try { event(new \App\Events\DataUpdated()); } catch (\Exception $e) { /* Skip kung mapalong ang Reverb */ }
            \App\Models\ActivityLog::record('Expenses', 'Recorded', 'Added expense: ' . $request->category . ' (₱' . number_format($request->amount, 2) . ')', $expense); // . $request->category . ' (₱' . number_format($request->amount, 2) . ')');
            return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}