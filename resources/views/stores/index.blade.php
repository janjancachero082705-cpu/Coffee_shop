@extends('layouts.admin')
@section('title', 'Customers')
@section('subtitle', 'Manage customer accounts')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto space-y-6">
    
    <div class="card overflow-visible">
        
        {{-- TABS / HEADER --}}
        <div class="px-6 pt-6 border-b border-ink-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-6">
                <a href="{{ route('stores.index') }}" class="pb-3 text-sm font-bold text-brand-600 border-b-2 border-brand-500 flex items-center gap-2">
                    All Customers <span class="bg-brand-50 text-brand-700 text-[10px] px-2 py-0.5 rounded-full">{{ $stores->total() }}</span>
                </a>
                <a href="{{ route('stores.index') }}?type=suki" class="pb-3 text-sm font-medium text-ink-500 hover:text-ink-900 flex items-center gap-2 transition-colors">
                    Suki <span class="bg-ink-100 text-ink-600 text-[10px] px-2 py-0.5 rounded-full">{{ \App\Models\Store::where('customer_type', 'suki')->count() }}</span>
                </a>
                <a href="{{ route('stores.index') }}?type=regular" class="pb-3 text-sm font-medium text-ink-500 hover:text-ink-900 flex items-center gap-2 transition-colors">
                    Regular <span class="bg-ink-100 text-ink-600 text-[10px] px-2 py-0.5 rounded-full">{{ \App\Models\Store::where('customer_type', 'regular')->count() }}</span>
                </a>
            </div>
            <span class="text-xs text-ink-400 font-medium hidden md:block pb-3">Showing {{ $stores->firstItem() ?? 0 }} - {{ $stores->lastItem() ?? 0 }} of {{ $stores->total() }} results</span>
        </div>

        {{-- TOOLBAR (Search & Filters) --}}
        <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('stores.index') }}" class="relative w-full md:w-96 group">
                <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-ink-400 group-focus-within:text-brand-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name..." class="input pl-11 pr-10 shadow-sm">
                @if(request('search'))
                    <a href="{{ route('stores.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-600"><i class="fas fa-times"></i></a>
                @endif
            </form>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="{{ route('stores.create') }}" class="btn btn-primary whitespace-nowrap">
                    <i class="fas fa-plus"></i> Add Customer
                </a>
            </div>
        </div>

        {{-- FLOATING TABLE --}}
        <div class="px-6 pb-6">
            <table class="w-full text-sm border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-left text-ink-400 text-[11px] uppercase tracking-widest">
                        <th class="pb-2 px-6 font-bold">Customer</th>
                        <th class="pb-2 px-6 font-bold">Address</th>
                        <th class="pb-2 px-6 font-bold text-center">Type</th>
                        <th class="pb-2 px-6 font-bold text-center">Discount</th>
                        <th class="pb-2 px-6 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <tr class="bg-white group hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 rounded-xl border border-ink-200">
                            
                            <td class="px-6 py-4 rounded-l-xl border-y border-l border-ink-200 group-hover:border-brand-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-sm border border-brand-100">
                                        {{ strtoupper(substr($store->store_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-ink-900 text-sm">{{ $store->store_name }}</div>
                                        <div class="text-[11px] text-ink-400 font-medium">ID: #{{ str_pad($store->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 border-y border-ink-200 group-hover:border-brand-200">
                                <div class="text-ink-600 font-medium">{{ $store->address ?? 'No address' }}</div>
                            </td>

                            <td class="px-6 py-4 text-center border-y border-ink-200 group-hover:border-brand-200">
                                @if($store->customer_type == 'suki')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-100 uppercase tracking-wider">
                                        Suki
                                    </span>
                                @elseif($store->customer_type == 'friend')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">
                                        Friend
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-ink-100 text-ink-600 border border-ink-200 uppercase tracking-wider">
                                        Regular
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center border-y border-ink-200 group-hover:border-brand-200">
                                <span class="text-sm font-black text-emerald-600">{{ number_format($store->discount, 2) }}%</span>
                            </td>

                            <td class="px-6 py-4 text-center rounded-r-xl border-y border-r border-ink-200 group-hover:border-brand-200">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('deliveries.store-profile', $store->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-ink-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('stores.edit', $store) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-ink-400 hover:text-brand-600 hover:bg-brand-50 transition-colors" title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-ink-400">
                                <i class="fas fa-users-slash text-4xl mb-3 text-ink-300"></i>
                                <p class="font-medium">No customers found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-ink-200">
            {{ $stores->withQueryString()->links() }}
        </div>

    </div>
</div>
@endsection