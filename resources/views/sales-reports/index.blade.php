@extends('layouts.admin')
@section('title', 'Sales Reports')
@section('subtitle', 'Track every delivery as a sale and monitor payments')

@section('content')

<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">

    {{-- STATS CARDS --}}
    {{-- 1 column sa mobile, 3 columns sa desktop --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
        {{-- Total Sales --}}
        <div class="card p-4 md:p-5 border-l-4 border-l-brand-500 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-brand-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Sales</div>
                <div class="text-xl md:text-2xl font-black text-ink-900">₱{{ number_format($totalSales, 2) }}</div>
                <div class="text-[10px] text-ink-400 mt-1.5 font-medium flex items-center gap-1">
                    <i class="fas fa-truck text-brand-400"></i> From Deliveries
                </div>
            </div>
        </div>
        
        {{-- Total Collected --}}
        <div class="card p-4 md:p-5 border-l-4 border-l-emerald-500 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Collected</div>
                <div class="text-xl md:text-2xl font-black text-emerald-600">₱{{ number_format($totalCollected, 2) }}</div>
                <div class="text-[10px] text-ink-400 mt-1.5 font-medium flex items-center gap-1">
                    <i class="fas fa-hand-holding-usd text-emerald-400"></i> Payments Received
                </div>
            </div>
        </div>

        {{-- Outstanding Balance --}}
        <div class="card p-4 md:p-5 border-l-4 border-l-red-500 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Outstanding</div>
                <div class="text-xl md:text-2xl font-black text-red-600">₱{{ number_format($totalOutstanding, 2) }}</div>
                <div class="text-[10px] text-ink-400 mt-1.5 font-medium flex items-center gap-1">
                    <i class="fas fa-exclamation-circle text-red-400"></i> Unpaid sales
                </div>
            </div>
        </div>
    </div>

    {{-- FILTERS & LEDGER --}}
    <div class="card overflow-hidden">
        
        {{-- Header & Filters --}}
        <div class="p-4 md:p-5 border-b border-ink-200 bg-ink-50">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-4">
                <div>
                    <h3 class="font-bold text-ink-800 text-sm md:text-lg">Sales Ledger</h3>
                    <p class="text-[10px] md:text-xs text-ink-500 mt-0.5">Every delivery is recorded here as a sale.</p>
                </div>
            </div>
            
            {{-- Ginawang flex-col sa mobile para mag-stack pababa --}}
            <form method="GET" action="{{ route('sales-reports.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 md:gap-3">
                
                <div class="relative group w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR or Customer..." class="input !pl-9 !py-2 !text-xs md:!text-sm shadow-sm w-full">
                </div>
                
                <div class="flex items-center gap-2 bg-white border border-ink-200 rounded-lg shadow-sm px-3 py-1.5 w-full sm:w-auto">
                    <div class="flex items-center gap-1 w-full">
                        <i class="fas fa-calendar-alt text-ink-400 text-xs"></i>
                        <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()" class="text-[11px] md:text-xs font-semibold text-ink-700 outline-none bg-transparent cursor-pointer w-full">
                    </div>
                    <span class="text-ink-400 font-bold text-[9px] md:text-[10px] uppercase hidden sm:block">to</span>
                    <div class="flex items-center gap-1 w-full">
                        <i class="fas fa-calendar-alt text-ink-400 text-xs"></i>
                        <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()" class="text-[11px] md:text-xs font-semibold text-ink-700 outline-none bg-transparent cursor-pointer w-full">
                    </div>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="btn btn-primary !py-2 !text-xs w-full sm:w-auto">Filter</button>
                    @if(request()->has('search') || request()->has('start_date') || request()->has('end_date'))
                        <a href="{{ route('sales-reports.index') }}" class="text-xs font-bold text-red-500 hover:text-red-600 px-2 py-2">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table (Swipeable sa loob lang) --}}
        <div class="overflow-x-auto w-full pb-2" style="-webkit-overflow-scrolling: touch;">
            <table class="table-clean w-full min-w-[800px]">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">Date</th>
                        <th class="whitespace-nowrap">DR Number</th>
                        <th>Customer</th>
                        <th class="text-right whitespace-nowrap">Total Sale</th>
                        <th class="text-right whitespace-nowrap">Paid</th>
                        <th class="text-right whitespace-nowrap">Balance</th>
                        <th class="text-center whitespace-nowrap">Status</th>
                        <th class="text-center whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        @php
                            $paid = $sale->payments_sum_amount ?? 0;
                            $balance = $sale->total_amount - $paid;
                        @endphp
                        <tr class="group">
                            <td class="text-ink-500 font-medium whitespace-nowrap text-xs md:text-sm">{{ \Carbon\Carbon::parse($sale->delivery_date)->format('M d, Y') }}</td>
                            <td class="font-bold text-ink-900 font-mono whitespace-nowrap text-xs md:text-sm">{{ $sale->dr_number }}</td>
                            <td>
                                <a href="{{ route('deliveries.store-profile', $sale->store_id) }}" class="font-bold text-ink-900 hover:text-brand-600 transition-colors text-xs md:text-sm">{{ $sale->store->store_name ?? 'Unknown' }}</a>
                            </td>
                            <td class="text-right font-black text-ink-900 whitespace-nowrap text-xs md:text-sm">₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-right font-medium text-emerald-600 whitespace-nowrap text-xs md:text-sm">₱{{ number_format($paid, 2) }}</td>
                            <td class="text-right font-bold {{ $balance > 0 ? 'text-red-600' : 'text-ink-400' }} whitespace-nowrap text-xs md:text-sm">₱{{ number_format($balance, 2) }}</td>
                            <td class="text-center whitespace-nowrap">
                                @if($balance <= 0)
                                    <span class="inline-flex px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[9px] md:text-[10px] font-bold border border-emerald-200 uppercase tracking-wide">Paid</span>
                                @elseif($paid > 0)
                                    <span class="inline-flex px-2 py-1 bg-amber-50 text-amber-600 rounded-md text-[9px] md:text-[10px] font-bold border border-amber-200 uppercase tracking-wide">Partial</span>
                                @else
                                    <span class="inline-flex px-2 py-1 bg-red-50 text-red-600 rounded-md text-[9px] md:text-[10px] font-bold border border-red-200 uppercase tracking-wide">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-center whitespace-nowrap">
                                @if($balance > 0)
                                    <a href="{{ route('payments.index', ['store_id' => $sale->store_id, 'delivery_id' => $sale->id]) }}" class="btn btn-primary !py-1.5 !px-3 !text-[10px] uppercase tracking-wider">
                                        Pay
                                    </a>
                                @else
                                    <i class="fas fa-check-circle text-emerald-500 text-base md:text-lg"></i>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-16">
                                <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100">
                                    <i class="fas fa-file-invoice text-2xl text-ink-300"></i>
                                </div>
                                <h3 class="text-base font-bold text-ink-800 mb-1">No sales found</h3>
                                <p class="text-xs text-ink-500">Create a delivery to start recording sales.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 md:px-6 md:py-4 border-t border-ink-200 bg-ink-50 text-sm">
            {{ $sales->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection