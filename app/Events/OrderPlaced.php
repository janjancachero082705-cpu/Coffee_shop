<?php

namespace App\Events;

use App\Models\ReorderRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(ReorderRequest $order)
    {
        $this->order = $order->load('store', 'items');
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
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'number' => $this->order->request_number,
            'store_id' => $this->order->store_id,
            'store_name' => $this->order->store->store_name ?? '-',
            'items' => $this->order->items ? $this->order->items->count() : 0,
            'total' => number_format($this->order->total_amount, 2),
            'status' => $this->order->status,
            'ago' => $this->order->created_at->diffForHumans(),
            'url' => route('portal.orders.show', $this->order->id),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}