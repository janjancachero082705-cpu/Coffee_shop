@extends('layouts.admin')
@section('title', 'Customers')
@section('subtitle', 'Manage customer accounts')

@section('content')
<!-- Gidugangan og light background para mo-pop out ang white card -->
<div class="w-full max-w-screen-2xl mx-auto bg-slate-50 min-h-screen p-2 rounded-2xl">
    
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-visible">
        
        {{-- TABS / HEADER --}}
        <div class="px-6 pt-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-6">
                <a href="{{ route('stores.index') }}" class="pb-3 text-sm font-bold text-slate-800 border-b-2 border-slate-800 flex items-center gap-2">
                    All Customers <span class="bg-slate-100 text-slate-600 text-[10px] px-2 py-0.5 rounded-full">{{ $stores->total() }}</span>
                </a>
                <a href="{{ route('stores.index') }}?type=suki" class="pb-3 text-sm font-medium text-slate-500 hover:text-slate-800 flex items-center gap-2">
                    Suki <span class="bg-orange-50 text-orange-600 text-[10px] px-2 py-0.5 rounded-full">{{ \App\Models\Store::where('customer_type', 'suki')->count() }}</span>
                </a>
                <a href="{{ route('stores.index') }}?type=regular" class="pb-3 text-sm font-medium text-slate-500 hover:text-slate-800 flex items-center gap-2">
                    Regular <span class="bg-slate-100 text-slate-600 text-[10px] px-2 py-0.5 rounded-full">{{ \App\Models\Store::where('customer_type', 'regular')->count() }}</span>
                </a>
            </div>
            <span class="text-xs text-slate-400 font-medium hidden md:block pb-3">Showing {{ $stores->firstItem() ?? 0 }} - {{ $stores->lastItem() ?? 0 }} of {{ $stores->total() }} results</span>
        </div>

        {{-- TOOLBAR (Search & Filters) --}}
        <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('stores.index') }}" class="relative w-full md:w-96 group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name..." class="w-full pl-11 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-slate-100 focus:border-slate-300 outline-none transition-all shadow-sm">
                @if(request('search'))
                    <a href="{{ route('stores.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></a>
                @endif
            </form>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- View toggles (Design lang sa karon) -->
                <div class="hidden md:flex items-center gap-1 bg-slate-50 p-1 rounded-lg border border-slate-200">
                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-white shadow-sm text-slate-800"><i class="fas fa-list text-xs"></i></button>
                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded text-slate-400 hover:text-slate-800"><i class="fas fa-th-large text-xs"></i></button>
                </div>

                <a href="{{ route('stores.create') }}" class="px-5 py-2.5 bg-slate-900 text-white font-bold rounded-xl text-sm hover:bg-slate-800 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Customer
                </a>
            </div>
        </div>

        {{-- FLOATING TABLE --}}
        <div class="px-6 pb-6">
            <!-- border-separate ug border-spacing-y-3 maoy naghatag sa floating effect -->
            <table class="w-full text-sm border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-left text-slate-400 text-[11px] uppercase tracking-widest">
                        <th class="pb-2 px-6 font-bold">Customer</th>
                        <th class="pb-2 px-6 font-bold">Address</th>
                        <th class="pb-2 px-6 font-bold text-center">Type</th>
                        <th class="pb-2 px-6 font-bold text-center">Discount</th>
                        <th class="pb-2 px-6 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <!-- Hover effect: mo-lutaw (translate-y), mo-shadow, ug naay rounded corners -->
                        <tr class="bg-white group hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 rounded-xl border border-slate-100">
                            
                            <td class="px-6 py-4 rounded-l-xl border-y border-l border-slate-100 group-hover:border-transparent">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar Circle -->
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($store->store_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $store->store_name }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium">ID: #{{ str_pad($store->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 border-y border-slate-100 group-hover:border-transparent">
                                <div class="text-slate-600 font-medium">{{ $store->address ?? 'No address' }}</div>
                            </td>

                            <td class="px-6 py-4 text-center border-y border-slate-100 group-hover:border-transparent">
                                @if($store->customer_type == 'suki')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-100 uppercase tracking-wider">
                                        Suki
                                    </span>
                                @elseif($store->customer_type == 'friend')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">
                                        Friend
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wider">
                                        Regular
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center border-y border-slate-100 group-hover:border-transparent">
                                <span class="text-sm font-black text-emerald-600">{{ number_format($store->discount, 2) }}%</span>
                            </td>

                            <td class="px-6 py-4 text-center rounded-r-xl border-y border-r border-slate-100 group-hover:border-transparent">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('deliveries.store-profile', $store->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('stores.edit', $store) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-orange-600 hover:bg-orange-50 transition-colors" title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-users-slash text-4xl mb-3 text-slate-300"></i>
                                <p class="font-medium">No customers found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $stores->withQueryString()->links() }}
        </div>

    </div>
</div>
@endsection