<?php

namespace App\Traits;

use App\Models\StoreNotification;

trait NotifiesStore
{
    protected function notifyStore(
        $storeId,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): void {
        try {
            StoreNotification::create([
                'store_id' => $storeId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::warning('notifyStore failed: ' . $e->getMessage());
        }
    }
}