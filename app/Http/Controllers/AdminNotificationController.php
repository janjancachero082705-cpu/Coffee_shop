<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\ReorderRequest;

class AdminNotificationController extends Controller
{
    public function index()
    {
        // ===== REORDER REQUESTS =====
        $requests = ReorderRequest::with(['store', 'items'])
            ->where('is_read_by_admin', false)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                return [
                    'type' => 'order',
                    'id' => $r->id,
                    'code' => $r->request_number ?? 'REQ-' . $r->id,
                    'store_name' => $r->store->store_name ?? '-',
                    'store_logo' => $r->store->logo_url ?? null,
                    'store_initials' => $r->store->initials ?? 'ST',
                    'total' => number_format($r->total_amount ?? 0, 2),
                    'items' => $r->items ? $r->items->count() : 0,
                    'ago' => $r->created_at->diffForHumans(),
                    'url' => route('reorder-requests.show', $r->id),
                    'created_at' => $r->created_at,
                ];
            });

        // ===== NEW STORE REGISTRATIONS =====
        $newStores = Store::where('is_read_by_admin', false)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'register',
                    'id' => $s->id,
                    'code' => $s->code ?? 'STORE-' . $s->id,
                    'store_name' => $s->store_name,
                    'store_logo' => $s->logo_url ?? null,
                    'store_initials' => $s->initials ?? 'ST',
                    'owner' => $s->owner_name,
                    'email' => $s->email,
                    'total' => null,
                    'items' => null,
                    'ago' => $s->created_at->diffForHumans(),
                    'url' => route('store-registrations.show', $s->id),
                    'created_at' => $s->created_at,
                ];
            });

        // ===== RECENT LOGINS (last 24h) =====
        $recentLogins = Store::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subHours(24))
            ->where('login_count', '>', 1) // dili apil ang una nga login (register)
            ->orderByDesc('last_login_at')
            ->limit(5)
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'login',
                    'id' => $s->id,
                    'code' => $s->code ?? 'STORE-' . $s->id,
                    'store_name' => $s->store_name,
                    'store_logo' => $s->logo_url ?? null,
                    'store_initials' => $s->initials ?? 'ST',
                    'owner' => $s->owner_name,
                    'total' => null,
                    'items' => null,
                    'ago' => $s->last_login_at->diffForHumans(),
                    'url' => route('store-registrations.show', $s->id),
                    'created_at' => $s->last_login_at,
                ];
            });

        // Combine + sort by created_at
        $all = collect()
            ->merge($requests)
            ->merge($newStores)
            ->merge($recentLogins)
            ->sortByDesc('created_at')
            ->take(15)
            ->values();

        return response()->json([
            'count' => ReorderRequest::where('is_read_by_admin', false)->count()
                     + Store::where('is_read_by_admin', false)->count(),
            'orders' => $all,
        ]);
    }

    public function markRead($id)
    {
        // Try reorder request una
        $req = ReorderRequest::find($id);
        if ($req) {
            $req->update(['is_read_by_admin' => true, 'is_read_at' => now()]);
            return response()->json(['ok' => true, 'type' => 'order']);
        }

        // Try store
        $store = Store::find($id);
        if ($store) {
            $store->update(['is_read_by_admin' => true, 'is_read_at' => now()]);
            return response()->json(['ok' => true, 'type' => 'store']);
        }

        return response()->json(['ok' => false], 404);
    }

    public function markAllRead()
    {
        ReorderRequest::where('is_read_by_admin', false)->update([
            'is_read_by_admin' => true,
            'is_read_at' => now(),
        ]);

        Store::where('is_read_by_admin', false)->update([
            'is_read_by_admin' => true,
            'is_read_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}