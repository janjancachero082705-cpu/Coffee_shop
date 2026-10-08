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
            // Always override URL — point to list page (relative)
            $data['url'] = $this->generateNotificationUrl($type);

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

    /**
     * Generate LIST page URL by notification type
     */
    protected function generateNotificationUrl(string $type): string
    {
        return match ($type) {
            // ORDER types → orders list
            'reorder_approved',
            'reorder_rejected' => '/portal/orders',

            // DELIVERY types → deliveries list
            'delivery_out',
            'delivery_delivered',
            'delivery_confirmed' => '/portal/deliveries',

            // PAYMENT types → payments list
            'payment_recorded',
            'payment_verified' => '/portal/payments',

            // REPORT types → reports list
            'report_ready',
            'report_updated' => '/portal/reports',

            // Default
            default => '/portal',
        };
    }
}