@extends('layouts.admin')
@section('title', $batch->batch_number)
@section('subtitle', $batch->product->name ?? 'Production Batch')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card p-6">
        <h2 class="font-display text-xl font-bold text-coffee-900 mb-4">{{ $batch->batch_number }}</h2>
        <div class="grid grid-cols-2 gap-4 text-sm border-t border-coffee-100 pt-4">
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Date</div><div class="font-semibold">{{ $batch->production_date->format('M d, Y') }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Product</div><div class="font-semibold">{{ $batch->product->name ?? '—' }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Cherries Used</div><div class="font-semibold">{{ $batch->cherries_input_kg }} kg</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Output</div><div class="font-semibold">{{ $batch->output_quantity }} {{ $batch->output_unit }}</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Yield</div><div class="font-semibold text-emerald-600">{{ number_format($batch->yield_percentage,1) }}%</div></div>
            <div><div class="text-xs text-coffee-500 uppercase font-bold">Cost per Unit</div><div class="font-semibold">₱{{ number_format($batch->cost_per_output_unit,2) }}</div></div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-card p-5">
        <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Total Cost</div>
        <div class="text-2xl font-black text-gold-600">₱{{ number_format($batch->total_cost,2) }}</div>
    </div>
</div>
@endsection