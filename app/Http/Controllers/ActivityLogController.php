<?php
namespace App\Http\Controllers;
use App\Models\ActivityLog;
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
        
        // List of unique modules for the dropdown
        $modules = ActivityLog::select('module')->distinct()->pluck('module')->toArray();

        return view('activity_logs.index', compact('logs', 'modules'));
    }

    public function markAsRead($id)
    {
        \App\Models\ActivityLog::where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        \App\Models\ActivityLog::whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}