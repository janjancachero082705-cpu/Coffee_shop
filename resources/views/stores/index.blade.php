@extends('layouts.admin')
@section('title', 'Customers')
@section('subtitle', 'Manage customer accounts and discount tiers')

@section('content')

{{-- STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border border-slate-200">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Customers</div>
            <i class="fas fa-store text-slate-300"></i>
        </div>
        <div class="text-3xl font-black text-slate-900">{{ $stores->total() }}</div>
        <div class="text-[11px] text-slate-400 mt-1 font-medium">Active customers</div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-5 border border-slate-200">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Suki VIP</div>
            <i class="fas fa-crown text-caramel-400"></i>
        </div>
        <div class="text-3xl font-black text-slate-900">{{ \App\Models\Store::where('customer_type', 'suki')->count() }}</div>
        <div class="text-[11px] text-caramel-500 mt-1 font-medium">Priority clients</div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5 border border-slate-200">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Regular</div>
            <i class="fas fa-user text-slate-400"></i>
        </div>
        <div class="text-3xl font-black text-slate-900">{{ \App\Models\Store::where('customer_type', 'regular')->count() }}</div>
        <div class="text-[11px] text-slate-400 mt-1 font-medium">Standard pricing</div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5 border border-slate-200">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Friend</div>
            <i class="fas fa-handshake text-blue-400"></i>
        </div>
        <div class="text-3xl font-black text-slate-900">{{ \App\Models\Store::where('customer_type', 'friend')->count() }}</div>
        <div class="text-[11px] text-blue-500 mt-1 font-medium">Special pricing</div>
    </div>
</div>

{{-- MAIN CONTENT CARD --}}
<div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
    
    {{-- HEADER & FILTERS --}}
    <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
        <form method="GET" action="{{ route('stores.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative group w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs group-focus-within:text-caramel-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search stores..." class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 outline-none transition-all">
            </div>
            <select name="type" class="w-full md:w-36 px-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 outline-none cursor-pointer text-slate-700">
                <option value="">All Types</option>
                <option value="suki" {{ request('type') == 'suki' ? 'selected' : '' }}>Suki</option>
                <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>Regular</option>
                <option value="friend" {{ request('type') == 'friend' ? 'selected' : '' }}>Friend</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md font-medium text-sm hover:bg-slate-800 transition-colors">Filter</button>
            
            @if(request()->has('search') || request()->has('type'))
                <a href="{{ route('stores.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-sm font-medium">Clear</a>
            @endif
        </form>

        <div class="flex items-center gap-3">
            {{-- VIEW TOGGLE --}}
            <div class="hidden sm:flex items-center gap-1 bg-slate-100 p-1 rounded-md border border-slate-200">
                <button id="gridViewBtn" onclick="setView('grid')" class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors" title="Grid View">
                    <i class="fas fa-th-large text-xs"></i>
                </button>
                <button id="listViewBtn" onclick="setView('list')" class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors" title="List View">
                    <i class="fas fa-list text-xs"></i>
                </button>
            </div>

            <a href="{{ route('stores.create') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-md font-medium text-sm shadow-sm transition-all flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus text-xs"></i> Add Customer
            </a>
        </div>
    </div>

    {{-- STORES DATA --}}
    @if($stores->count() > 0)
        
        {{-- 1. GRID VIEW --}}
        <div id="gridView" class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 bg-slate-50/50">
            @foreach($stores as $store)
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col group relative">
                    
                    {{-- Hover Actions --}}
                    <div class="absolute top-3 right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                        <a href="{{ route('stores.edit', $store) }}" class="w-7 h-7 bg-white text-slate-600 rounded shadow-sm border border-slate-200 flex items-center justify-center hover:bg-slate-50" title="Edit">
                            <i class="fas fa-pen text-[10px]"></i>
                        </a>
                        <form action="{{ route('stores.destroy', $store) }}" method="POST" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button class="w-7 h-7 bg-white text-red-600 rounded shadow-sm border border-slate-200 flex items-center justify-center hover:bg-red-50" title="Delete">
                                <i class="fas fa-trash text-[10px]"></i>
                            </button>
                        </form>
                    </div>

                    <div class="p-5 flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg border border-slate-200">
                                {{ strtoupper(substr($store->store_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 pr-8">
                                <h4 class="font-bold text-slate-900 text-sm truncate">{{ $store->store_name }}</h4>
                                <div class="text-[10px] text-slate-500 font-medium">ID: #{{ str_pad($store->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                        
                        <p class="text-xs text-slate-500 mb-4 flex items-start gap-1.5 line-clamp-2 h-8">
                            <i class="fas fa-map-marker-alt text-slate-400 mt-0.5"></i> {{ $store->address ?? 'No address provided' }}
                        </p>

                        <div class="flex items-center justify-between mt-auto">
                            @if($store->customer_type == 'suki')
                                <span class="px-2 py-0.5 bg-caramel-50 text-caramel-700 rounded text-[10px] font-bold border border-caramel-200 uppercase tracking-wider">Suki</span>
                            @elseif($store->customer_type == 'friend')
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold border border-blue-200 uppercase tracking-wider">Friend</span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold border border-slate-200 uppercase tracking-wider">Regular</span>
                            @endif
                        </div>
                    </div>

                    {{-- Footer Link --}}
                    <a href="{{ route('deliveries.store-profile', $store->id) }}" class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 hover:bg-slate-100 transition-colors rounded-b-lg flex items-center justify-between group-hover:text-caramel-600">
                        <span class="text-xs font-semibold text-slate-600 group-hover:text-caramel-600">View Profile</span>
                        <i class="fas fa-arrow-right text-slate-400 group-hover:text-caramel-600 text-xs"></i>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- 2. LIST VIEW --}}
        <div id="listView" class="hidden overflow-x-auto bg-white">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3">Customer Details</th>
                        <th class="px-6 py-3">Address</th>
                        <th class="px-6 py-3 text-center">Customer Type</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stores as $store)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs border border-slate-200">
                                        {{ strtoupper(substr($store->store_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('deliveries.store-profile', $store->id) }}" class="font-bold text-slate-900 hover:text-caramel-600 transition-colors">{{ $store->store_name }}</a>
                                        <div class="text-[10px] text-slate-400">ID: #{{ str_pad($store->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-slate-600 text-xs">{{ $store->address ?? 'Ã¢â‚¬â€' }}</td>
                            <td class="px-6 py-3 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @if($store->customer_type == 'suki')
                                        <span class="px-2 py-0.5 bg-caramel-50 text-caramel-700 rounded text-[10px] font-bold border border-caramel-200 uppercase tracking-wider">Suki</span>
                                    @elseif($store->customer_type == 'friend')
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold border border-blue-200 uppercase tracking-wider">Friend</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold border border-slate-200 uppercase tracking-wider">Regular</span>
                                    @endif
                                    
                                    @if($store->discount > 0)
                                        <span class="text-[10px] font-bold text-emerald-600"><i class="fas fa-tag mr-0.5"></i>{{ number_format($store->discount, 2) }}% OFF</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('stores.edit', $store) }}" class="w-7 h-7 inline-flex items-center justify-center rounded text-slate-500 hover:bg-slate-200 hover:text-slate-900 transition-colors"><i class="fas fa-pen text-[10px]"></i></a>
                                    <form action="{{ route('stores.destroy', $store) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="w-7 h-7 inline-flex items-center justify-center rounded text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors"><i class="fas fa-trash text-[10px]"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-3 border-t border-slate-200 bg-white">
            {{ $stores->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-24 bg-white">
            <div class="w-16 h-16 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-store-slash text-2xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No customers found</h3>
            <p class="text-sm text-slate-500 mb-4">Add your first store to start managing deliveries.</p>
            <a href="{{ route('stores.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-md font-medium hover:bg-slate-800 transition-all text-sm shadow-sm"><i class="fas fa-plus"></i> Add Customer</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function setView(type) {
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');

        if (type === 'grid') {
            gridView.classList.remove('hidden');
            listView.classList.add('hidden');
            gridBtn.className = 'w-7 h-7 flex items-center justify-center rounded bg-white text-slate-900 shadow-sm transition-colors';
            listBtn.className = 'w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors';
            localStorage.setItem('storeView', 'grid');
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
            listBtn.className = 'w-7 h-7 flex items-center justify-center rounded bg-white text-slate-900 shadow-sm transition-colors';
            gridBtn.className = 'w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 transition-colors';
            localStorage.setItem('storeView', 'list');
        }
    }

    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', function() { this.form.submit(); });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const savedView = localStorage.getItem('storeView') || 'grid';
        setView(savedView);
    });
</script>
@endpush