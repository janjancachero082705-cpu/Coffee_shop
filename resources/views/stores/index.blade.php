@extends('layouts.admin')
@section('title', 'Customers')
@section('subtitle', 'Manage customer accounts')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">

    {{-- TABS & TOOLBAR --}}
    <div class="card p-4 md:p-6 flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
        
        {{-- Tabs --}}
        <div class="flex gap-1 bg-ink-100 p-1 rounded-xl border border-ink-200 w-full sm:w-max">
            <a href="{{ route('stores.index') }}" class="flex-1 sm:flex-none justify-center text-center px-2 sm:px-5 py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors {{ !request('type') ? 'bg-white text-ink-900 shadow-sm border border-ink-200' : 'text-ink-500 hover:text-ink-900' }}">
                All <span class="ml-1 text-[10px] opacity-70">{{ $stores->total() }}</span>
            </a>
            <a href="{{ route('stores.index', ['type' => 'suki']) }}" class="flex-1 sm:flex-none justify-center text-center px-2 sm:px-5 py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors {{ request('type') === 'suki' ? 'bg-white text-ink-900 shadow-sm border border-ink-200' : 'text-ink-500 hover:text-ink-900' }}">
                Suki <span class="ml-1 text-[10px] opacity-70">{{ \App\Models\Store::where('customer_type', 'suki')->count() }}</span>
            </a>
            <a href="{{ route('stores.index', ['type' => 'regular']) }}" class="flex-1 sm:flex-none justify-center text-center px-2 sm:px-5 py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors {{ request('type') === 'regular' ? 'bg-white text-ink-900 shadow-sm border border-ink-200' : 'text-ink-500 hover:text-ink-900' }}">
                Regular <span class="ml-1 text-[10px] opacity-70">{{ \App\Models\Store::where('customer_type', 'regular')->count() }}</span>
            </a>
        </div>

        {{-- Search & Add --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full lg:w-auto">
            <form method="GET" action="{{ route('stores.index') }}" class="relative w-full sm:w-64">
                @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer..." class="input pl-9 !py-2 !text-xs md:!text-sm">
                @if(request('search'))
                    <a href="{{ route('stores.index', request()->except('search')) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-600"><i class="fas fa-times text-[10px]"></i></a>
                @endif
            </form>
            <a href="{{ route('stores.create') }}" class="btn btn-primary whitespace-nowrap w-full sm:w-auto !py-2 !text-xs md:!text-sm">
                <i class="fas fa-plus"></i> Add Customer
            </a>
        </div>
    </div>

    {{-- CARD GRID (1 column sa mobile, 4 sa desktop) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
        @forelse($stores as $store)
            <div class="card p-4 md:p-5 flex flex-col justify-between hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 group">
                
                {{-- Top: Avatar, Name, Badge --}}
                <div class="flex justify-between items-start mb-3 md:mb-4 gap-2">
                    <div class="flex items-center gap-2.5 md:gap-3 min-w-0">
                        <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm md:text-lg border border-brand-200 shrink-0">
                            {{ strtoupper(substr($store->store_name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-ink-900 text-[13px] md:text-sm leading-tight truncate" title="{{ $store->store_name }}">{{ $store->store_name }}</h4>
                            <div class="text-[9px] md:text-[10px] text-ink-500 font-mono mt-0.5">ID: #{{ str_pad($store->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                    
                    {{-- Customer Type Badge --}}
                    <div class="shrink-0">
                        @if($store->customer_type == 'suki')
                            <span class="px-1.5 md:px-2 py-0.5 rounded text-[8px] md:text-[9px] font-bold bg-orange-50 text-orange-600 border border-orange-200 uppercase tracking-wider">Suki</span>
                        @elseif($store->customer_type == 'friend')
                            <span class="px-1.5 md:px-2 py-0.5 rounded text-[8px] md:text-[9px] font-bold bg-blue-50 text-blue-600 border border-blue-200 uppercase tracking-wider">Friend</span>
                        @else
                            <span class="px-1.5 md:px-2 py-0.5 rounded text-[8px] md:text-[9px] font-bold bg-ink-100 text-ink-600 border border-ink-200 uppercase tracking-wider">Regular</span>
                        @endif
                    </div>
                </div>

                {{-- Middle: Address & Discount --}}
                <div class="space-y-1.5 md:space-y-2 mb-3 md:mb-4">
                    <div class="flex items-start gap-1.5 text-[11px] md:text-xs text-ink-500">
                        <i class="fas fa-map-marker-alt mt-0.5 text-ink-400 text-[10px]"></i>
                        <span class="line-clamp-2 leading-tight">{{ $store->address ?? 'No address provided' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] md:text-xs">
                        <i class="fas fa-tag text-ink-400 text-[10px]"></i>
                        <span class="font-bold text-emerald-600">{{ number_format($store->discount, 2) }}% Discount</span>
                    </div>
                </div>

                {{-- Bottom: Actions --}}
                <div class="pt-3 md:pt-4 border-t border-ink-100 flex items-center justify-between gap-2 mt-auto">
                    <a href="{{ route('deliveries.store-profile', $store->id) }}" class="flex-1 py-1.5 md:py-2 text-center text-[10px] md:text-[11px] font-bold rounded-lg bg-ink-50 text-ink-600 hover:bg-ink-100 transition-colors">
                        View Profile
                    </a>
                    <a href="{{ route('stores.edit', $store) }}" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-lg text-brand-600 bg-brand-50 hover:bg-brand-100 transition-colors shadow-sm" title="Edit">
                        <i class="fas fa-pen text-[9px] md:text-[11px]"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full card text-center py-16 md:py-24">
                <div class="w-16 h-16 md:w-20 md:h-20 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 md:mb-4 border border-ink-100 shadow-inner">
                    <i class="fas fa-users-slash text-2xl md:text-3xl text-ink-300"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-ink-800 mb-1 md:mb-2">No customers found</h3>
                <p class="text-xs md:text-sm text-ink-500 mb-5">Try adjusting your search or add a new customer.</p>
                <a href="{{ route('stores.create') }}" class="btn btn-primary mx-auto !py-2 !text-xs md:!text-sm"><i class="fas fa-plus"></i> Add Customer</a>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4 md:mt-6">
        {{ $stores->withQueryString()->links() }}
    </div>

</div>
@endsection