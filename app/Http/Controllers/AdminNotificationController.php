<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\ConsignmentPayment;
use App\Models\ReorderRequest;
use App\Models\AdminNotification;

class AdminNotificationController extends Controller
{
    public function index()
    {
        // ===== ADMIN NOTIFICATIONS (gikan sa notifyAdmin()) =====
        $adminNotifs = AdminNotification::unread()
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(function ($n) {
                $data = $n->data ?? [];
                $store = !empty($data['store_id']) ? Store::find($data['store_id']) : null;

                // Dynamic type mapping
                $typeMap = [
                    'delivery.confirmed' => 'delivery_confirmed',
                    'delivery.created'   => 'delivery_created',
                    'order.placed'       => 'order_placed',
                    'payment.created'    => 'payment_created',
                ];
                $frontType = $typeMap[$n->type] ?? 'admin';

                return [
                    'type' => $frontType,
                    'raw_type' => $n->type,
                    'id' => $n->id,
                    'code' => 'AN-' . $n->id,
                    'store_name' => $store?->store_name ?? 'System',
                    'store_logo' => $store?->logo_url,
                    'store_initials' => strtoupper(substr($store?->store_name ?? 'SY', 0, 2)),
                    'owner' => $store?->owner_name,
                    'title' => $n->title,
                    'message' => $n->message,
                    'total' => null,
                    'items' => null,
                    'ago' => $n->created_at->diffForHumans(),
                    'url' => $data['url'] ?? null,
                    'color' => $n->color,
                    'icon' => $n->icon,
                    'created_at' => $n->created_at,
                    'is_admin_notif' => true,
                ];
            });

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
            ->where('login_count', '>', 1)
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

        // ===== NEW PAYMENTS (unread by admin) =====
        $payments = ConsignmentPayment::with(['store', 'deliveryReceipt'])
            ->where('is_read_by_admin', false)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                $storeName = $p->store->store_name ?? $p->store->name ?? ('Store #' . $p->store_id);
                return [
                    'type' => 'payment',
                    'id' => $p->id,
                    'code' => $p->payment_number,
                    'store_name' => $storeName,
                    'store_logo' => $p->store->logo_url ?? null,
                    'store_initials' => $p->store->initials ?? 'ST',
                    'amount' => number_format($p->amount, 2),
                    'method' => $p->method_label ?? ucfirst($p->method),
                    'method_icon' => $p->method_icon ?? '💰',
                    'method_color' => $p->method_color ?? '#c9a961',
                    'dr_number' => $p->deliveryReceipt->dr_number ?? null,
                    'reference' => $p->reference_number,
                    'total' => number_format($p->amount, 2),
                    'items' => null,
                    'ago' => $p->created_at->diffForHumans(),
                    'url' => route('consignment.payments.index'),
                    'created_at' => $p->created_at,
                ];
            });

        // Combine + sort by created_at
        $all = collect()
            ->merge($adminNotifs)
            ->merge($requests)
            ->merge($newStores)
            ->merge($recentLogins)
            ->merge($payments)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return response()->json([
            'count' => AdminNotification::unread()->count()
                     + ReorderRequest::where('is_read_by_admin', false)->count()
                     + Store::where('is_read_by_admin', false)->count()
                     + ConsignmentPayment::where('is_read_by_admin', false)->count(),
            'orders' => $all,
        ]);
    }

    public function markRead($id)
    {
        // Try AdminNotification (with prefix 'an_')
        if (is_string($id) && str_starts_with($id, 'an_')) {
            $realId = (int) substr($id, 3);
            $notif = AdminNotification::find($realId);
            if ($notif) {
                $notif->markAsRead();
                return response()->json(['ok' => true, 'type' => 'admin_notif']);
            }
        }

        // Try reorder request
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

        // Try payment
        $payment = ConsignmentPayment::find($id);
        if ($payment) {
            $payment->update(['is_read_by_admin' => true, 'is_read_at' => now()]);
            return response()->json(['ok' => true, 'type' => 'payment']);
        }

        // Try AdminNotification (plain id, no prefix)
        $notif = AdminNotification::find($id);
        if ($notif) {
            $notif->markAsRead();
            return response()->json(['ok' => true, 'type' => 'admin_notif']);
        }

        return response()->json(['ok' => false], 404);
    }

    public function markAllRead()
    {
        AdminNotification::unread()->update(['read_at' => now()]);

        ReorderRequest::where('is_read_by_admin', false)->update([
            'is_read_by_admin' => true,
            'is_read_at' => now(),
        ]);

        Store::where('is_read_by_admin', false)->update([
            'is_read_by_admin' => true,
            'is_read_at' => now(),
        ]);

        ConsignmentPayment::where('is_read_by_admin', false)->update([
            'is_read_by_admin' => true,
            'is_read_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}