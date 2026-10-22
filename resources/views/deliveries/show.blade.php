@extends('layouts.admin')
@section('title', $delivery->dr_number)
@section('subtitle', 'Delivery to ' . ($delivery->store->store_name ?? ''))
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card p-6">
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-coffee-100">
            <div>
                <h2 class="font-display text-2xl font-bold text-coffee-900">{{ $delivery->dr_number }}</h2>
                <p class="text-sm text-coffee-500">{{ $delivery->store->store_name ?? '' }}</p>
            </div>
            <span class="badge {{ $delivery->status === 'delivered' ? 'badge-success':'badge-warning' }}">{{ $delivery->status }}</span>
        </div>

        <table class="w-full text-sm">
            <thead><tr class="border-b border-coffee-100"><th class="text-left py-2 text-xs font-bold text-coffee-500 uppercase">Product</th><th class="text-center py-2 text-xs font-bold text-coffee-500 uppercase">Qty</th><th class="text-right py-2 text-xs font-bold text-coffee-500 uppercase">Price</th><th class="text-right py-2 text-xs font-bold text-coffee-500 uppercase">Subtotal</th></tr></thead>
            <tbody>
                @foreach($delivery->items as $item)
                    <tr class="border-b border-coffee-50">
                        <td class="py-2 font-semibold">{{ $item->product->name ?? '—' }}</td>
                        <td class="py-2 text-center">{{ $item->quantity_delivered }} {{ $item->unit }}</td>
                        <td class="py-2 text-right">₱{{ number_format($item->unit_price,2) }}</td>
                        <td class="py-2 text-right font-bold">₱{{ number_format($item->subtotal,2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 space-y-1 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span class="font-bold">₱{{ number_format($delivery->subtotal,2) }}</span></div>
            <div class="flex justify-between text-red-600"><span>Discount ({{ number_format($delivery->discount_percent,2) }}%)</span><span class="font-bold">-₱{{ number_format($delivery->discount_amount,2) }}</span></div>
            <div class="flex justify-between text-lg pt-2 border-t border-coffee-100"><span class="font-bold">TOTAL</span><span class="font-black text-gold-600">₱{{ number_format($delivery->total_amount,2) }}</span></div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-card p-5">
            <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Total Amount</div>
            <div class="text-3xl font-black text-coffee-900">₱{{ number_format($delivery->total_amount,2) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-card p-5">
            <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Paid</div>
            <div class="text-xl font-black text-emerald-600">₱{{ number_format($delivery->amount_paid,2) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-card p-5" style="border-left:4px solid #ef4444">
            <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Balance</div>
            <div class="text-xl font-black text-red-600">₱{{ number_format($delivery->balance,2) }}</div>
        </div>
    </div>
</div>
@endsection