@extends('layouts.admin')
@section('title', $purchase->purchase_number)
@section('subtitle', $purchase->supplier_name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card p-6">
        <h2 class="font-display text-xl font-bold text-coffee-900 mb-4">{{ $purchase->purchase_number }}</h2>
        <div class="grid grid-cols-2 gap-4 text-sm border-t border-coffee-100 pt-4">
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Supplier</div><div class="font-semibold">{{ $purchase->supplier_name }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Date</div><div class="font-semibold">{{ $purchase->purchase_date->format('M d, Y') }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Quantity</div><div class="font-semibold">{{ $purchase->quantity }} {{ $purchase->unit }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Unit Price</div><div class="font-semibold">&#8369;{{ number_format($purchase->unit_price,2) }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Total</div><div class="text-xl font-black text-gold-600">&#8369;{{ number_format($purchase->total_amount,2) }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Paid</div><div class="font-semibold text-emerald-600">&#8369;{{ number_format($purchase->amount_paid,2) }}</div></div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-card p-5" style="border-left:4px solid #ef4444">
        <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Balance</div>
        <div class="text-2xl font-black text-red-600">&#8369;{{ number_format($purchase->total_amount - $purchase->amount_paid, 2) }}</div>
    </div>
</div>
@endsection