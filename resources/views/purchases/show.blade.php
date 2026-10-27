@extends('layouts.admin')
@section('title', $purchase->purchase_number)
@section('subtitle', $purchase->supplier_name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
    <div class="lg:col-span-2 card p-6">
        <h2 class="font-black text-xl text-ink-900 mb-4">{{ $purchase->purchase_number }}</h2>
        <div class="grid grid-cols-2 gap-4 text-sm border-t border-ink-200 pt-4">
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Supplier</div><div class="font-semibold text-ink-900">{{ $purchase->supplier_name }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Date</div><div class="font-semibold text-ink-900">{{ $purchase->purchase_date->format('M d, Y') }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Quantity</div><div class="font-semibold text-ink-900">{{ $purchase->quantity }} {{ $purchase->unit }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Unit Price</div><div class="font-semibold text-ink-900">&#8369;{{ number_format($purchase->unit_price,2) }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Total</div><div class="text-xl font-black text-brand-600">&#8369;{{ number_format($purchase->total_amount,2) }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Paid</div><div class="font-semibold text-emerald-600">&#8369;{{ number_format($purchase->amount_paid,2) }}</div></div>
        </div>
    </div>
    <div class="card p-5 border-l-4 border-red-500">
        <div class="text-[10px] font-bold text-ink-500 uppercase tracking-wider mb-1">Balance</div>
        <div class="text-2xl font-black text-red-600">&#8369;{{ number_format($purchase->total_amount - $purchase->amount_paid, 2) }}</div>
    </div>
</div>
@endsection