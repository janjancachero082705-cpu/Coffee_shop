@extends('layouts.admin')
@section('title', 'Deliveries')
@section('subtitle', 'Manage sales and delivery receipts')

@section('content')



<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">
    
    {{-- 4 KPI BOXES (2x2 sa mobile para tipid sa space) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="card p-3 md:p-5 border-l-4 border-l-blue-500">
            <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Deliveries</div>
            <div class="text-lg md:text-2xl font-black text-ink-900">{{ number_format($totalCount) }}</div>
        </div>
        <div class="card p-3 md:p-5 border-l-4 border-l-amber-500">
            <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Distributed</div>
            <div class="text-lg md:text-2xl font-black text-ink-900">₱{{ number_format($totalDistributed, 0) }}</div>
        </div>
        <div class="card p-3 md:p-5 border-l-4 border-l-emerald-500">
            <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Collected</div>
            <div class="text-lg md:text-2xl font-black text-emerald-600">₱{{ number_format($totalCollected, 0) }}</div>
        </div>
        <div class="card p-3 md:p-5 border-l-4 border-l-red-500">
            <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Outstanding</div>
            <div class="text-lg md:text-2xl font-black text-red-600">₱{{ number_format($totalBalance, 0) }}</div>
        </div>
    </div>

    {{-- FILTER & SEARCH BAR --}}
    <div class="card p-3 md:p-4 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-2 md:gap-4">
        <form method="GET" action="{{ route('deliveries.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 flex-1">
            <div class="relative w-full sm:w-64">
                <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR or customer..." class="input !pl-8 !py-2 !text-xs md:!text-sm">
            </div>
            <div class="flex items-center gap-1.5 w-full sm:w-auto">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="input !w-full sm:!w-auto !py-2 !text-xs md:!text-sm cursor-pointer" title="Start Date">
                <span class="text-ink-400 text-xs">to</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="input !w-full sm:!w-auto !py-2 !text-xs md:!text-sm cursor-pointer" title="End Date">
            </div>
            <button type="submit" class="btn btn-secondary !py-2 !text-xs md:!text-sm w-full sm:w-auto">Filter</button>
            @if(request()->has('search') || request()->has('start_date') || request()->has('end_date'))
                <a href="{{ route('deliveries.index') }}" class="text-xs font-semibold text-ink-500 hover:text-brand-600 text-center py-2 sm:py-0">Clear</a>
            @endif
        </form>

        <a href="{{ route('deliveries.create') }}" class="btn btn-primary whitespace-nowrap !py-2 !text-xs md:!text-sm w-full sm:w-auto">
            <i class="fas fa-plus"></i> New Delivery
        </a>
    </div>

    {{-- TABLE (May sariling scrollbar, hindi bubuka ang page) --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto w-full pb-2" style="-webkit-overflow-scrolling: touch;">
            <table class="table-clean w-full min-w-[700px]">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">DR Number</th>
                        <th>Customer</th>
                        <th class="whitespace-nowrap">Date</th>
                        <th class="text-right whitespace-nowrap">Total Amount</th>
                        <th class="text-center whitespace-nowrap">Status</th>
                        <th class="text-center whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        <tr class="group">
                            <td class="font-mono font-bold text-ink-900 whitespace-nowrap text-xs md:text-sm">{{ $delivery->dr_number }}</td>
                            <td>
                                <div class="font-bold text-ink-800 text-xs md:text-sm">{{ $delivery->store->store_name ?? 'Unknown' }}</div>
                            </td>
                            <td class="text-ink-500 font-medium whitespace-nowrap text-xs md:text-sm">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</td>
                            <td class="text-right font-bold text-ink-900 whitespace-nowrap text-xs md:text-sm">₱{{ number_format($delivery->total_amount, 2) }}</td>
                            <td class="text-center whitespace-nowrap">
                                @if($delivery->status === 'delivered' || $delivery->status === 'completed')
                                    <span class="px-2 py-0.5 md:py-1 rounded-md text-[9px] md:text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-wider">Delivered</span>
                                @else
                                    <span class="px-2 py-0.5 md:py-1 rounded-md text-[9px] md:text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 uppercase tracking-wider">{{ ucfirst($delivery->status ?? 'Pending') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg text-brand-600 bg-brand-50 hover:bg-brand-100 transition-colors shadow-sm" title="View Receipt"><i class="fas fa-eye text-[10px]"></i></a>
                                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Delete this delivery?');">
                                        @csrf @method('DELETE')
                                        <button class="w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors shadow-sm" title="Delete"><i class="fas fa-trash text-[10px]"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100">
                                    <i class="fas fa-truck text-2xl text-ink-300"></i>
                                </div>
                                <h3 class="text-base font-bold text-ink-800 mb-1">No deliveries found</h3>
                                <p class="text-xs text-ink-500">Try adjusting your search or filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 md:px-6 md:py-4 border-t border-ink-200 bg-ink-50 text-sm">
            {{ $deliveries->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection