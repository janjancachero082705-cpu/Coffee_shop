<?php

use App\Models\AdminNotification;

if (!function_exists('notifyAdmin')) {
    /**
     * Send notification to admin.
     * Uses existing schema: type, title, message, data (JSON), read_at
     */
    function notifyAdmin(
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        ?int $storeId = null,
        string $icon = 'bell',
        string $color = '#E8A33D'
    ): ?AdminNotification {
        try {
            return AdminNotification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'url' => $url,
                    'store_id' => $storeId,
                    'icon' => $icon,
                    'color' => $color,
                ],
                'read_at' => null,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('notifyAdmin failed: ' . $e->getMessage());
            return null;
        }
    }
}