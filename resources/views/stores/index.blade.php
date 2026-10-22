@extends('layouts.admin')
@section('title', 'Stores')
@section('subtitle', 'Manage store customers and discount tiers')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Stores</div><div class="text-3xl font-black text-espresso-900">{{ $stores->total() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-espresso-50 text-espresso-600 flex items-center justify-center text-lg"><i class="fas fa-store"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Suki VIP</div><div class="text-3xl font-black text-caramel-600">{{ \App\Models\Store::where('customer_type', 'suki')->count() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-caramel-50 text-caramel-600 flex items-center justify-center text-lg"><i class="fas fa-crown"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Regular</div><div class="text-3xl font-black text-espresso-700">{{ \App\Models\Store::where('customer_type', 'regular')->count() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-espresso-50 text-espresso-600 flex items-center justify-center text-lg"><i class="fas fa-user"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Friend</div><div class="text-3xl font-black text-blue-600">{{ \App\Models\Store::where('customer_type', 'friend')->count() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fas fa-handshake"></i></div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-espresso-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-espresso-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-espresso-50/30">
        <form method="GET" action="{{ route('stores.index') }}" class="flex flex-wrap gap-3 items-center flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search store..." class="px-3 py-2 bg-white border border-espresso-200 rounded-lg text-sm focus:border-caramel-500 outline-none w-64 shadow-sm">
            <select name="type" class="px-3 py-2 bg-white border border-espresso-200 rounded-lg text-sm focus:border-caramel-500 outline-none shadow-sm text-espresso-700 font-medium">
                <option value="">All Types</option>
                <option value="suki" {{ request('type') == 'suki' ? 'selected' : '' }}>Suki</option>
                <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>Regular</option>
                <option value="friend" {{ request('type') == 'friend' ? 'selected' : '' }}>Friend</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-white border border-espresso-200 text-espresso-700 rounded-lg font-semibold text-sm hover:bg-espresso-50 transition-colors shadow-sm">Filter</button>
        </form>
        <a href="{{ route('stores.create') }}" class="px-5 py-2.5 bg-espresso-900 hover:bg-espresso-800 text-white rounded-lg font-semibold text-sm shadow-sm transition-all flex items-center gap-2"><i class="fas fa-plus"></i> Add Store</a>
    </div>

    @if($stores->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-espresso-200 text-espresso-500 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Store Info</th>
                        <th class="px-6 py-4">Address</th>
                        <th class="px-6 py-4 text-center">Type</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-espresso-100">
                    @foreach($stores as $store)
                        <tr class="hover:bg-espresso-50/50 transition-colors odd:bg-white even:bg-espresso-50/20 group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded bg-espresso-100 text-espresso-700 flex items-center justify-center font-bold text-sm border border-espresso-200">{{ strtoupper(substr($store->store_name, 0, 1)) }}</div>
                                    <div>
                                        <div class="font-bold text-espresso-900">{{ $store->store_name }}</div>
                                        <div class="text-[10px] text-espresso-500 font-mono">ID: #{{ str_pad($store->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-espresso-600">{{ $store->address ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($store->customer_type == 'suki')
                                    <span class="inline-flex px-2.5 py-1 bg-caramel-50 text-caramel-700 rounded-md text-[10px] font-bold border border-caramel-200 uppercase tracking-wide">Suki VIP</span>
                                @elseif($store->customer_type == 'friend')
                                    <span class="inline-flex px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold border border-blue-200 uppercase tracking-wide">Friend</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 bg-espresso-100 text-espresso-700 rounded-md text-[10px] font-bold border border-espresso-200 uppercase tracking-wide">Regular</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('stores.edit', $store) }}" class="w-7 h-7 inline-flex items-center justify-center rounded bg-white border border-espresso-200 text-blue-600 hover:bg-blue-50 transition-colors"><i class="fas fa-edit text-xs"></i></a>
                                    <form action="{{ route('stores.destroy', $store) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="w-7 h-7 inline-flex items-center justify-center rounded bg-white border border-espresso-200 text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-trash text-xs"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-espresso-200 bg-white">{{ $stores->withQueryString()->links() }}</div>
    @else
        <div class="text-center py-16 text-espresso-500">
            <i class="fas fa-store text-4xl mb-3 text-espresso-300"></i>
            <h3 class="text-lg font-bold text-espresso-800 mb-1">No stores found</h3>
            <p class="text-sm mb-4">Add a new store to get started.</p>
        </div>
    @endif
</div>
@endsection