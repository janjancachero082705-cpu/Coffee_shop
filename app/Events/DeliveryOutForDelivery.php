<?php

namespace App\Events;

use App\Models\DeliveryReceipt;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryOutForDelivery implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $delivery;
    public $storeId;

    public function __construct(DeliveryReceipt $delivery)
    {
        $this->delivery = $delivery->load('store', 'items');
        $this->storeId = $delivery->store_id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.notifications'),
            new PrivateChannel('store.' . $this->storeId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.out';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->delivery->id,
            'number' => $this->delivery->dr_number ?? ('DR-' . $this->delivery->id),
            'store_id' => $this->storeId,
            'store_name' => $this->delivery->store->store_name ?? '-',
            'total' => number_format($this->delivery->total_amount, 2),
            'items' => $this->delivery->items ? $this->delivery->items->count() : 0,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}