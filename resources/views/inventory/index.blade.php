@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Real-time stock levels and inventory valuation')

@section('content')
{{-- STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-espresso-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-espresso-500 uppercase tracking-wider mb-1">Total SKUs</div>
            <div class="text-3xl font-black text-espresso-900">{{ number_format($stats['total_items']) }}</div>
            <div class="text-xs text-espresso-400 mt-2 font-medium"><i class="fas fa-box text-espresso-300 mr-1"></i> Active variants</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-espresso-500 uppercase tracking-wider mb-1">Low Stock</div>
            <div class="text-3xl font-black text-amber-600">{{ number_format($stats['low_stock']) }}</div>
            <div class="text-xs text-amber-500 mt-2 font-medium"><i class="fas fa-exclamation-triangle mr-1"></i> Needs restock</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-espresso-500 uppercase tracking-wider mb-1">Out of Stock</div>
            <div class="text-3xl font-black text-red-600">{{ number_format($stats['out_of_stock']) }}</div>
            <div class="text-xs text-red-500 mt-2 font-medium"><i class="fas fa-times-circle mr-1"></i> Unavailable</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-caramel-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-xs font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Value</div>
            <div class="text-3xl font-black text-caramel-600">&#8369;{{ number_format($stats['total_value'], 0) }}</div>
            <div class="text-xs text-caramel-500 mt-2 font-medium"><i class="fas fa-chart-line mr-1"></i> Current valuation</div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT CARD --}}
<div class="bg-white rounded-xl shadow-sm border border-espresso-200">
    
    {{-- FILTER BAR --}}
    <div class="p-5 border-b border-espresso-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-espresso-50/30">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative group w-full md:w-72">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-espresso-400 text-sm group-focus-within:text-caramel-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product or SKU..." class="w-full pl-9 pr-4 py-2 bg-white border border-espresso-200 rounded-lg text-sm focus:border-caramel-500 focus:ring-2 focus:ring-caramel-500/20 outline-none transition-all shadow-sm">
            </div>
            
            <select name="status" class="py-2 px-3 bg-white border border-espresso-200 rounded-lg text-sm focus:border-caramel-500 focus:ring-2 focus:ring-caramel-500/20 outline-none shadow-sm cursor-pointer font-medium text-espresso-700">
                <option value="">All Status</option>
                <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-espresso-900 hover:bg-espresso-800 text-white rounded-lg font-semibold text-sm shadow-sm transition-colors">Filter</button>
            
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-white border border-espresso-200 text-espresso-600 hover:bg-espresso-50 rounded-lg font-semibold text-sm transition-colors shadow-sm">Clear</a>
            @endif
        </form>
    </div>

    {{-- DATA TABLE --}}
    @if($variants->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-espresso-200 text-espresso-500 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Variant</th>
                        <th class="px-6 py-4 w-64">Stock Level</th>
                        <th class="px-6 py-4 text-right">Unit Price</th>
                        <th class="px-6 py-4 text-right">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-espresso-100">
                    @foreach($variants as $variant)
                        <tr class="hover:bg-espresso-50/50 transition-colors">
                            {{-- Product --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-espresso-100 text-espresso-700 flex items-center justify-center font-bold text-sm border border-espresso-200 shadow-sm">
                                        {{ strtoupper(substr($variant->product->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-espresso-900">{{ $variant->product->name ?? 'Unknown' }}</div>
                                        <div class="text-[10px] text-espresso-500 font-mono bg-espresso-100 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $variant->product->sku ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Variant Name --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 bg-white text-espresso-800 rounded-md text-xs font-bold border border-espresso-200 shadow-sm">
                                    {{ $variant->name }} ({{ $variant->unit }})
                                </span>
                            </td>

                            {{-- Stock Level Progress --}}
                            <td class="px-6 py-4">
                                @if($variant->stock == 0)
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        <span class="text-xs font-bold text-red-600">Out of Stock</span>
                                    </div>
                                    <div class="w-full bg-espresso-100 rounded-full h-1.5 mt-1.5"></div>
                                @elseif($variant->stock <= 5)
                                    <div class="flex justify-between text-[11px] font-bold text-amber-600 mb-1">
                                        <span>Low Stock</span>
                                        <span>{{ $variant->stock }} units left</span>
                                    </div>
                                    <div class="w-full bg-amber-100 rounded-full h-1.5">
                                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ ($variant->stock / 10) * 100 }}%"></div>
                                    </div>
                                @else
                                    <div class="flex justify-between text-[11px] font-bold text-emerald-600 mb-1">
                                        <span>In Stock</span>
                                        <span>{{ number_format($variant->stock) }} units</span>
                                    </div>
                                    <div class="w-full bg-emerald-100 rounded-full h-1.5">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, ($variant->stock / 100) * 100) }}%"></div>
                                    </div>
                                @endif
                            </td>

                            {{-- Unit Price --}}
                            <td class="px-6 py-4 text-right font-medium text-espresso-700">
                                &#8369;{{ number_format($variant->price, 2) }}
                            </td>

                            {{-- Total Value --}}
                            <td class="px-6 py-4 text-right font-bold text-espresso-900">
                                &#8369;{{ number_format($variant->stock * $variant->price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-espresso-200 bg-espresso-50/30">
            {{ $variants->withQueryString()->links() }}
        </div>
    @else
        {{-- EMPTY STATE --}}
        <div class="text-center py-24">
            <div class="w-20 h-20 mx-auto bg-espresso-50 rounded-2xl flex items-center justify-center mb-4 border border-espresso-100">
                <i class="fas fa-boxes text-3xl text-espresso-300"></i>
            </div>
            <h3 class="text-lg font-bold text-espresso-800 mb-1">No inventory found</h3>
            <p class="text-sm text-espresso-500 mb-6 max-w-sm mx-auto">Try adjusting your filters or add a new product to start tracking stock.</p>
            <a href="{{ route('inventory.index') }}" class="text-sm font-semibold text-caramel-600 hover:text-caramel-700 hover:underline">Clear all filters</a>
        </div>
    @endif
</div>
@endsection