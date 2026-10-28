@extends('layouts.admin')
@section('title', 'Inventory Movements')
@section('subtitle', 'Track all incoming and outgoing stock')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">

    {{-- HEADER CARD --}}
    <div class="card p-4 md:p-6">
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-ink-900 flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-brand-500"></i> Stock Movement History
                </h3>
                <p class="text-xs text-ink-500 mt-1">Record of all stock-in, stock-out, and adjustments.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full xl:w-auto">
                <form action="{{ route('inventory.movements') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product name..." class="input !pl-8 !py-2 !text-xs w-full">
                    </div>
                    <button type="submit" class="btn btn-secondary !py-2 !text-xs whitespace-nowrap">Search</button>
                </form>
                
                <a href="{{ route('inventory.index') }}" class="btn btn-primary !py-2 !text-xs whitespace-nowrap w-full sm:w-auto">
                    <i class="fas fa-arrow-left text-[10px]"></i> Back to Inventory
                </a>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto w-full rounded-xl border border-ink-200" style="-webkit-overflow-scrolling: touch;">
            <table class="table-clean w-full min-w-[800px]">
                <thead class="bg-ink-50 border-b border-ink-200">
                    <tr>
                        <th class="whitespace-nowrap text-[11px] uppercase tracking-wider text-ink-500">Date & Time</th>
                        <th class="text-[11px] uppercase tracking-wider text-ink-500">Product</th>
                        <th class="text-center text-[11px] uppercase tracking-wider text-ink-500">Type</th>
                        <th class="text-right text-[11px] uppercase tracking-wider text-ink-500">Movement</th>
                        <th class="text-right text-[11px] uppercase tracking-wider text-ink-500">Current Stock</th>
                        <th class="text-[11px] uppercase tracking-wider text-ink-500">Reference / Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 bg-white">
                    @forelse($movements as $m)
                        <tr class="hover:bg-ink-50/50 transition-colors">
                            {{-- DATE --}}
                            <td class="text-ink-500 font-medium text-xs whitespace-nowrap">
                                {{ $m->created_at->format('M d, Y') }} <br>
                                <span class="text-[10px] text-ink-400">{{ $m->created_at->format('h:i A') }}</span>
                            </td>
                            
                            {{-- PRODUCT --}}
                            <td>
                                <div class="font-bold text-ink-900 text-sm">{{ $m->variant->product->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] font-semibold text-ink-500 uppercase tracking-wider">{{ $m->variant->name ?? 'Standard' }}</div>
                            </td>
                            
                            {{-- TYPE BADGE --}}
                            <td class="text-center">
                                @if($m->type === 'in')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                        <i class="fas fa-arrow-down"></i> Stock In
                                    </span>
                                @elseif($m->type === 'out')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 uppercase tracking-wider">
                                        <i class="fas fa-arrow-up"></i> Stock Out
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                        <i class="fas fa-sliders"></i> Adjustment
                                    </span>
                                @endif
                            </td>
                            
                            {{-- MOVEMENT (+/-) --}}
                            <td class="text-right font-bold text-sm whitespace-nowrap {{ $m->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $m->type === 'in' ? '+' : '-' }}{{ number_format($m->quantity, 2) }} <span class="text-[10px] text-ink-500">{{ $m->variant->unit ?? 'pc' }}</span>
                            </td>
                            
                            {{-- CURRENT STOCK --}}
                            @php
                                $currentStock = $m->variant->stock ?? 0;
                                $isNegative = $currentStock < 0;
                            @endphp
                            <td class="text-right font-extrabold text-sm whitespace-nowrap {{ $isNegative ? 'text-red-600 animate-pulse' : 'text-ink-900' }}" title="{{ $isNegative ? 'Negative stock detected!' : '' }}">
                                {{ number_format($currentStock, 2) }} <span class="text-[10px] font-semibold text-ink-500">{{ $m->variant->unit ?? 'pc' }}</span>
                                @if($isNegative)
                                    <i class="fas fa-triangle-exclamation text-red-500 ml-1 text-[10px]"></i>
                                @endif
                            </td>
                            
                            {{-- NOTES --}}
                            <td class="text-ink-600 text-xs">
                                {{ $m->notes ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100">
                                    <i class="fas fa-box-open text-2xl text-ink-300"></i>
                                </div>
                                <h3 class="text-base font-bold text-ink-800 mb-1">No movements found</h3>
                                <p class="text-xs text-ink-500">Try adjusting your search or check back later.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $movements->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection