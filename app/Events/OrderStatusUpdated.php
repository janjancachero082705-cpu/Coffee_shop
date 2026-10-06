<?php

namespace App\Events;

use App\Models\ReorderRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(ReorderRequest $order)
    {
        $this->order = $order->load('store');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.notifications'),
            new PrivateChannel('store.' . $this->order->store_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'number' => $this->order->request_number,
            'status' => $this->order->status,
            'store_id' => $this->order->store_id,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}