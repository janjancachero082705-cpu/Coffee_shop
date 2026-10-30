<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by Module (Category)
        if ($request->filled('module') && $request->module !== 'All') {
            $query->where('module', $request->module);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->appends($request->query());
        $modules = ActivityLog::select('module')->distinct()->pluck('module')->toArray();

        return view('activity_logs.index', compact('logs', 'modules'));
    }

    public function markAsRead($id)
    {
        ActivityLog::where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        ActivityLog::whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'quantity' => 'nullable|numeric'
        ]);
        
        $log = ActivityLog::findOrFail($id);
        
        // Sync Stock kung Inventory Movement
        if ($log->module === 'Inventory' && $log->reference_id && $request->quantity) {
            $variant = ProductVariant::find($log->reference_id);
            if ($variant) {
                $diff = $request->quantity - ($log->quantity ?? 0);
                $variant->increment('stock', $diff);
                
                // Update the inventory movements table record
                $movement = InventoryMovement::where('product_variant_id', $log->reference_id)->latest()->first();
                if ($movement) {
                    $movement->update(['quantity' => $request->quantity]);
                }
            }
        }

        $log->update([
            'description' => $request->description . ' (Overridden)',
            'quantity' => $request->quantity ?? $log->quantity,
            'action' => 'Overridden'
        ]);

        return back()->with('success', 'Transaction overridden successfully!');
    }

    public function destroy($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();
        
        return back()->with('success', 'Transaction log deleted from history.');
    }
}