@extends('layouts.admin')
@section('title', $product->name)
@section('subtitle', $product->sku)

@section('header-actions')
<a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-caramel-400 text-espresso-900 rounded-lg font-bold text-sm hover:bg-caramel-500 shadow-md transition-all"><i class="fas fa-edit mr-1"></i> Edit</a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card p-6 border border-espresso-100">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="font-serif text-2xl font-bold text-espresso-900">{{ $product->name }}</h2>
                <p class="text-sm text-espresso-500 font-medium">{{ $product->sku }} · {{ $product->unit_type }}</p>
            </div>
            @if($product->is_active)<span class="badge badge-success">Active</span>@endif
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm border-t border-espresso-100 pt-4">
            <div><div class="text-xs text-espresso-500 uppercase font-bold mb-1">Price per Gram</div><div class="text-xl font-black text-espresso-900">₱{{ number_format($product->price, 4) }}</div></div>
            <div><div class="text-xs text-espresso-500 uppercase font-bold mb-1">Cost per Gram</div><div class="text-xl font-black text-espresso-600">₱{{ number_format($product->cost_price, 4) }}</div></div>
            <div><div class="text-xs text-espresso-500 uppercase font-bold mb-1">Stock</div><div class="text-xl font-black {{ $product->stock <= $product->reorder_level ? 'text-red-600':'text-emerald-600' }}">{{ number_format($product->stock, 0) }} grams</div></div>
            <div><div class="text-xs text-espresso-500 uppercase font-bold mb-1">Margin</div><div class="text-xl font-black text-caramel-500">{{ $product->price > 0 ? number_format((($product->price-$product->cost_price)/$product->price)*100,1) : 0 }}%</div></div>
        </div>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-card p-5 border border-espresso-100">
            <div class="text-xs font-bold text-espresso-500 uppercase mb-1">Total Delivered</div>
            <div class="text-3xl font-black text-blue-600">{{ number_format($stats['total_delivered'], 2) }} grams</div>
        </div>
        <div class="bg-white rounded-2xl shadow-card p-5 border border-espresso-100">
            <div class="text-xs font-bold text-espresso-500 uppercase mb-1">Total Revenue</div>
            <div class="text-2xl font-black text-espresso-900">₱{{ number_format($stats['total_revenue'],2) }}</div>
        </div>
    </div>
</div>

@endsection