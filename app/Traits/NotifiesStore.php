<?php

namespace App\Traits;

use App\Models\StoreNotification;

trait NotifiesStore
{
    /**
     * Send notification to store
     *
     * @param int    $storeId
     * @param string $type        Notification type
     * @param string $title       Bold title
     * @param string $message     Body text
     * @param array  $data        Extra data
     * @param int    $itemId      Optional — specific item ID for direct link
     */
    protected function notifyStore(
        $storeId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?int $itemId = null
    ): void {
        try {
            // Generate direct URL (may specific ID kung meron)
            $data['url'] = $this->generateNotificationUrl($type, $itemId);

            StoreNotification::create([
                'store_id' => $storeId,
                'type'     => $type,
                'title'    => $title,
                'message'  => $message,
                'data'     => $data,
            ]);
        } catch (\Exception $e) {
            \Log::warning('notifyStore failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate DIRECT URL by notification type + item ID
     */
    protected function generateNotificationUrl(string $type, ?int $itemId = null): string
    {
        // Kung may specific item ID, direct sa detail page
        if ($itemId) {
            return match ($type) {
                // ORDER types → tracking detail (para makita ang progress)
                'reorder_approved',
                'reorder_rejected',
                'order_placed'      => "/portal/tracking/{$itemId}",

                // DELIVERY types → delivery detail
                'delivery_out',
                'delivery_delivered',
                'delivery_confirmed' => "/portal/deliveries/{$itemId}",

                // PAYMENT types → payment detail
                'payment_recorded',
                'payment_verified',
                'payment_rejected'  => "/portal/payments/{$itemId}",

                // REPORT types → report detail
                'report_ready',
                'report_updated'    => "/portal/reports/{$itemId}",

                default => '/portal',
            };
        }

        // Fallback: LIST pages kung walang item ID
        return match ($type) {
            'reorder_approved',
            'reorder_rejected',
            'order_placed'      => '/portal/orders/tracking',

            'delivery_out',
            'delivery_delivered',
            'delivery_confirmed' => '/portal/deliveries',

            'payment_recorded',
            'payment_verified',
            'payment_rejected'  => '/portal/payments',

            'report_ready',
            'report_updated'    => '/portal/reports',

            default => '/portal',
        };
    }
}