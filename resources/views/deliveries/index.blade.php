@extends('layouts.admin')
@section('title', 'Distributions')
@section('subtitle', 'Manage deliveries per store')

@section('content')
{{-- STATS CARDS (Gradient Icons) --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between group hover:border-espresso-300 transition-colors">
        <div>
            <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Deliveries</div>
            <div class="text-3xl font-black text-espresso-900">{{ $stats['total'] }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-espresso-100 to-espresso-200 text-espresso-700 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
            <i class="fas fa-truck"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between group hover:border-amber-300 transition-colors">
        <div>
            <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Pending</div>
            <div class="text-3xl font-black text-amber-600">{{ $stats['pending'] }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 text-amber-700 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
            <i class="fas fa-clock"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between group hover:border-emerald-300 transition-colors">
        <div>
            <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Completed</div>
            <div class="text-3xl font-black text-emerald-600">{{ $stats['completed'] }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 text-emerald-700 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between group hover:border-caramel-300 transition-colors">
        <div>
            <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Value</div>
            <div class="text-3xl font-black text-caramel-600">&#8369;{{ number_format($stats['total_value'], 0) }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-caramel-100 to-caramel-200 text-caramel-700 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
            <i class="fas fa-peso-sign"></i>
        </div>
    </div>
</div>

{{-- HEADER & SEARCH --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h3 class="font-bold text-espresso-900 text-lg">Stores Directory</h3>
        <p class="text-xs text-espresso-500">Select a store to manage its deliveries</p>
    </div>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('deliveries.index') }}" class="relative group">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-espresso-400 text-sm group-focus-within:text-caramel-500 transition-colors"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search store..." class="pl-9 pr-4 py-2.5 bg-white border border-espresso-200 rounded-lg text-sm focus:border-caramel-500 focus:ring-2 focus:ring-caramel-500/20 outline-none w-64 shadow-sm transition-all">
        </form>
        <a href="{{ route('deliveries.create') }}" class="px-5 py-2.5 bg-espresso-900 hover:bg-espresso-800 text-white rounded-lg font-semibold text-sm shadow-md shadow-espresso-900/20 transition-all flex items-center gap-2 whitespace-nowrap active:scale-95"><i class="fas fa-plus"></i> New Delivery</a>
    </div>
</div>

{{-- STORE CARDS GRID --}}
@if($stores->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($stores as $store)
            <a href="{{ route('deliveries.store-profile', $store->id) }}" 
               class="bg-white rounded-xl shadow-sm p-6 border border-espresso-200 hover:border-caramel-400 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 block group relative overflow-hidden flex flex-col cursor-pointer">
                
                {{-- Top Accent Line --}}
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r {{ $store->customer_type == 'suki' ? 'from-caramel-400 to-caramel-600' : ($store->customer_type == 'friend' ? 'from-blue-400 to-blue-600' : 'from-espresso-300 to-espresso-500') }}"></div>

                <div class="flex items-start justify-between mb-5 mt-1">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-lg bg-espresso-50 text-espresso-800 flex items-center justify-center font-bold text-lg border border-espresso-200 shadow-sm group-hover:bg-caramel-50 group-hover:text-caramel-700 group-hover:border-caramel-200 transition-colors">
                            {{ strtoupper(substr($store->store_name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-espresso-900 text-base group-hover:text-caramel-600 transition-colors line-clamp-1">{{ $store->store_name }}</h4>
                            <p class="text-[11px] text-espresso-500 flex items-center gap-1 mt-0.5"><i class="fas fa-map-marker-alt text-espresso-400"></i> <span class="line-clamp-1">{{ $store->address ?? 'No address' }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-auto pt-4 border-t border-espresso-100">
                    <div>
                        <div class="text-[10px] font-bold text-espresso-400 uppercase tracking-wider mb-1">Deliveries</div>
                        <div class="font-black text-espresso-900 text-lg">{{ $store->deliveries_count }}</div>
                    </div>
                    <div class="text-right border-l border-espresso-100 pl-4">
                        <div class="text-[10px] font-bold text-espresso-400 uppercase tracking-wider mb-1">Total Value</div>
                        <div class="font-black text-caramel-600 text-lg">&#8369;{{ number_format($store->deliveries_sum_total_amount ?? 0, 0) }}</div>
                    </div>
                </div>

                {{-- Hover View Details --}}
                <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-4 transition-all duration-300">
                    <div class="w-8 h-8 rounded-full bg-espresso-900 text-white flex items-center justify-center shadow-md">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    
    <div class="mt-4">{{ $stores->withQueryString()->links() }}</div>
@else
    <div class="bg-white rounded-xl border border-espresso-200 text-center py-24 text-espresso-500 shadow-sm">
        <div class="w-20 h-20 mx-auto bg-espresso-50 rounded-2xl flex items-center justify-center mb-5 border border-espresso-100"><i class="fas fa-store-slash text-3xl text-espresso-300"></i></div>
        <h3 class="text-xl font-bold text-espresso-800 mb-2">No stores found</h3>
        <p class="text-sm mb-6 max-w-md mx-auto">You need to add stores first before creating deliveries.</p>
        <a href="{{ route('stores.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-espresso-900 text-white rounded-lg font-semibold hover:bg-espresso-800 transition-all text-sm shadow-md"><i class="fas fa-plus"></i> Add a Store</a>
    </div>
@endif
@endsection