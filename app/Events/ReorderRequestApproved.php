<?php

namespace App\Events;

use App\Models\ReorderRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReorderRequestApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;

    public function __construct(ReorderRequest $request)
    {
        $this->request = $request;
    }

    public function broadcastOn(): array
    {
        return [new Channel('notifications')];
    }

    public function broadcastAs(): string
    {
        return 'reorder.approved';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'store_name' => $this->request->store->store_name ?? '-',
            'total' => (float) $this->request->total_amount,
            'dr_number' => $this->request->deliveryReceipt->dr_number ?? null,
            'time' => now()->toIso8601String(),
        ];
    }
}