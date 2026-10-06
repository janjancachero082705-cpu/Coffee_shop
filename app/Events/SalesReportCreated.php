<?php

namespace App\Events;

use App\Models\SalesReport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesReportCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;

    public function __construct(SalesReport $report)
    {
        $this->report = $report;
    }

    public function broadcastOn(): array
    {
        return [new Channel('notifications')];
    }

    public function broadcastAs(): string
    {
        return 'report.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->report->id,
            'report_number' => $this->report->report_number,
            'store_name' => $this->report->store->store_name ?? '-',
            'total' => (float) $this->report->total_sales,
            'time' => now()->toIso8601String(),
        ];
    }
}