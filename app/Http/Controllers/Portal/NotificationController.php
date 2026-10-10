<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\StoreNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected function currentStore()
    {
        return Auth::guard('store')->user();
    }

    public function unread()
    {
        $store = $this->currentStore();

        $notifications = StoreNotification::where('store_id', $store->id)
            ->whereNull('read_at')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'icon' => $n->icon,
                    'color' => $n->color,
                    'title' => $n->title,
                    'message' => $n->message,
                    'url' => $n->data['url'] ?? null,
                    'status' => $n->data['status'] ?? null,
                    'balance' => $n->data['balance'] ?? null,
                    'paid' => $n->data['paid'] ?? null,
                    'total' => $n->data['total'] ?? null,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    public function markRead($id)
    {
        $store = $this->currentStore();
        StoreNotification::where('store_id', $store->id)
            ->where('id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        $store = $this->currentStore();
        StoreNotification::where('store_id', $store->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}