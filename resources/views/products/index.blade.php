@extends('layouts.admin')
@section('title', 'Products')
@section('subtitle', 'Manage product catalog and variants')

@section('content')

{{-- STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total SKUs</div>
            <div class="text-3xl font-black text-slate-900">{{ number_format($stats['total_products']) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-box text-blue-400 mr-1"></i> Active variants</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Low Stock</div>
            <div class="text-3xl font-black text-amber-600">{{ number_format($stats['low_stock']) }}</div>
            <div class="text-xs text-amber-500 mt-2 font-medium"><i class="fas fa-exclamation-triangle mr-1"></i> Needs restock</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Out of Stock</div>
            <div class="text-3xl font-black text-red-600">{{ number_format($stats['out_of_stock']) }}</div>
            <div class="text-xs text-red-500 mt-2 font-medium"><i class="fas fa-times-circle mr-1"></i> Unavailable</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-caramel-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Value</div>
            <div class="text-3xl font-black text-caramel-600">&#8369;{{ number_format($stats['total_value'], 0) }}</div>
            <div class="text-xs text-caramel-500 mt-2 font-medium"><i class="fas fa-chart-line mr-1"></i> Current valuation</div>
        </div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
    <form method="GET" action="{{ route('products.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative group w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-caramel-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product or SKU..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all">
            </div>

            <select name="category" class="w-full md:w-48 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none text-slate-700 font-medium cursor-pointer">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->emoji }} {{ $c->name }}</option>
                @endforeach
            </select>

            <select name="status" class="w-full md:w-40 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none text-slate-700 font-medium cursor-pointer">
                <option value="">All Status</option>
                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="px-5 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-colors shadow-sm">Filter</button>
            
            @if(request()->has('search') || request()->has('category') || request()->has('status'))
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg font-semibold text-sm hover:bg-red-100 transition-colors shadow-sm">Clear</a>
            @endif
        </div>

        <div class="flex-shrink-0">
            <a href="{{ route('products.create') }}" class="w-full lg:w-auto px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold text-sm shadow-sm transition-all flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </form>
</div>

{{-- PRODUCT GRID --}}
@if($products->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $p)
            @php $totalStock = $p->variants->sum('stock'); @endphp
                        <a href="{{ route('products.show', $p) }}" class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col group overflow-hidden relative">
                {{-- Status Badges --}}
                @if($totalStock <= 0)
                    <div class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md z-10 shadow-sm">OUT OF STOCK</div>
                @elseif($totalStock <= 5)
                    <div class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-md z-10 shadow-sm">LOW STOCK</div>
                @endif

                {{-- Image / Placeholder --}}
                <div class="w-full h-48 bg-slate-50 flex items-center justify-center relative overflow-hidden border-b border-slate-100">
                    @if($p->image)
                        <img src="{{ Storage::url($p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 12px 12px;"></div>
                        <span class="text-6xl font-black text-slate-200 group-hover:scale-110 transition-transform duration-300">{{ strtoupper(substr($p->name, 0, 1)) }}</span>
                    @endif
                </div>

                {{-- Details --}}
                <div class="p-5 flex-1 flex flex-col">
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $p->category->name ?? 'Uncategorized' }}</div>
                    <h4 class="font-bold text-slate-900 text-lg leading-tight mb-1 group-hover:text-caramel-600 transition-colors line-clamp-1" title="{{ $p->name }}">{{ $p->name }}</h4>
                    
                    <div class="text-xs text-slate-500 mb-4 font-medium flex items-center gap-1.5">
                        <span>{{ $p->variants->count() }} Variants</span> 
                        <span class="text-slate-300">|</span> 
                        <span class="font-mono">{{ $p->sku }}</span>
                    </div>
                    
                    <div class="mt-auto flex items-center justify-end pt-4 border-t border-slate-100">
                        <div class="w-full py-2.5 bg-slate-900 group-hover:bg-caramel-600 text-white text-xs font-bold rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-eye"></i> View Details
                        </div>
                    </div>
                </div>
            </a>
            </div>
        @endforeach
    </div>
    
    <div class="mt-8">
        {{ $products->withQueryString()->links() }}
    </div>
@else
    <div class="bg-white rounded-xl border border-slate-200 text-center py-24 shadow-sm">
        <div class="w-20 h-20 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-search text-3xl text-slate-300"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">No products found</h3>
        <p class="text-sm text-slate-500 mb-6">We couldn't find any products matching your filters.</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-lg font-semibold hover:bg-slate-800 transition-all text-sm">Clear all filters</a>
    </div>
@endif

@endsection