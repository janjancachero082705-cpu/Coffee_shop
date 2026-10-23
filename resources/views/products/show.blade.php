@extends('layouts.admin')
@section('title', $product->name)
@section('subtitle', 'Product Details and Variants')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- TOP HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200 flex-shrink-0">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                @else
                    <span class="text-4xl font-black text-slate-300">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-2xl font-bold text-slate-900">{{ $product->name }}</h2>
                    @if($product->is_active)
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded border border-emerald-200 uppercase tracking-wide">Active</span>
                    @endif
                </div>
                <div class="text-sm text-slate-500 font-mono bg-slate-50 px-2 py-1 rounded inline-block border border-slate-200">{{ $product->sku }}</div>
                <p class="text-xs text-slate-500 mt-2 font-medium">{{ $product->category->emoji ?? '' }} {{ $product->category->name ?? 'Uncategorized' }}</p>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-colors shadow-sm">Back</a>
            <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-slate-900 text-white rounded-lg font-semibold text-sm hover:bg-slate-800 transition-colors shadow-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
        </div>
    </div>

    {{-- TREE LAYOUT --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-800">Variant Breakdown</h3>
            <p class="text-xs text-slate-500">Prices and stock per size</p>
        </div>
        
        <div class="p-6">
            <div class="relative border-l-2 border-slate-200 ml-4 space-y-8 pb-4">
                
                @foreach($product->variants as $variant)
                    <div class="relative pl-8">
                        {{-- Tree Dot --}}
                        <div class="absolute -left-[9px] top-1 w-4 h-4 bg-white border-2 border-slate-400 rounded-full"></div>

                        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:border-caramel-400 transition-colors flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            
                            {{-- Variant Info & Image --}}
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($variant->image)
                                        <img src="{{ Storage::url($variant->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-image text-slate-300 text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-lg">{{ $variant->name }}</h4>
                                    <div class="text-xs text-slate-500 font-medium">Unit: {{ strtoupper($variant->unit) }}</div>
                                </div>
                            </div>

                            {{-- Variant Data --}}
                            <div class="flex items-center gap-8 w-full md:w-auto mt-4 md:mt-0">
                                <div class="text-right">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Price</div>
                                    <div class="font-black text-slate-900 text-xl">&#8369;{{ number_format($variant->price, 2) }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Stock</div>
                                    @if($variant->stock <= 0)
                                        <div class="px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded border border-red-200">OUT OF STOCK</div>
                                    @elseif($variant->stock <= 5)
                                        <div class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded border border-amber-200">{{ $variant->stock }} left</div>
                                    @else
                                        <div class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded border border-emerald-200">{{ $variant->stock }} units</div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endsection