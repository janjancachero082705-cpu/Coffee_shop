<?php

namespace App\Events;

use App\Models\ConsignmentPayment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    public $storeId;

    public function __construct(ConsignmentPayment $payment)
    {
        $this->payment = $payment->load('store');
        $this->storeId = $payment->store_id;
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
        return 'payment.recorded';
    }

    public function broadcastWith(): array
    {
        // Compute updated totals para sa store
        $totalDelivered = (float) \App\Models\DeliveryReceipt::where('store_id', $this->storeId)->sum('total_amount');
        $totalPaid = (float) \App\Models\ConsignmentPayment::where('store_id', $this->storeId)->sum('amount');
        $newBalance = max(0, $totalDelivered - $totalPaid);

        return [
            'id' => $this->payment->id,
            'number' => $this->payment->payment_number ?? ('PAY-' . $this->payment->id),
            'store_id' => $this->storeId,
            'store_name' => $this->payment->store->store_name ?? '-',
            'amount' => number_format($this->payment->amount, 2),
            'amount_raw' => (float) $this->payment->amount,
            'method' => $this->payment->method ?? 'cash',
            'reference' => $this->payment->reference_number ?? null,
            'payment_date' => $this->payment->payment_date ?? now()->toDateString(),

            // Updated totals — para ma-refresh dayon sa mobile
            'new_balance' => number_format($newBalance, 2),
            'new_balance_raw' => $newBalance,
            'total_paid' => number_format($totalPaid, 2),
            'total_delivered' => number_format($totalDelivered, 2),

            'timestamp' => now()->toIso8601String(),
        ];
    }
}