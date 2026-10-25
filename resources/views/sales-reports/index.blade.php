@extends('layouts.admin')
@section('title', 'Sales Reports')
@section('subtitle', 'Track every delivery as a sale and monitor payments')

@section('content')

{{-- STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Sales</div>
            <div class="text-3xl font-black text-blue-600">&#8369;{{ number_format($totalSales, 2) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-truck text-blue-400 mr-1"></i> From Deliveries</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Collected</div>
            <div class="text-3xl font-black text-emerald-600">&#8369;{{ number_format($totalCollected, 2) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-hand-holding-usd text-emerald-400 mr-1"></i> Payments Received</div>
        </div>
    </div>

    <div class="bg-slate-900 rounded-xl shadow-sm p-5 border border-slate-800 relative overflow-hidden group">
        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-800 opacity-30 group-hover:scale-110 transition-transform"><i class="fas fa-exclamation-circle fa-5x"></i></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Outstanding Balance</div>
            <div class="text-3xl font-black text-caramel-400">&#8369;{{ number_format($totalOutstanding, 2) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium">Unpaid sales</div>
        </div>
    </div>
</div>

{{-- FILTERS & LEDGER --}}
<div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
    
    {{-- Header & Filters --}}
    <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800 text-lg">Sales Ledger</h3>
            <p class="text-xs text-slate-500 mt-1">Every delivery is recorded here as a sale.</p>
        </div>
        
        <form method="GET" action="{{ route('sales-reports.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative group w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-caramel-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR or Customer..." class="w-full pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:border-caramel-500 outline-none shadow-sm transition-all">
            </div>
            <div class="flex items-center gap-2 bg-white border border-slate-300 rounded-lg shadow-sm px-3 py-1.5">
                <i class="fas fa-calendar-alt text-slate-400 text-sm"></i>
                <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()" class="text-sm font-medium text-slate-700 outline-none bg-transparent cursor-pointer">
                <span class="text-slate-400 font-bold px-1">to</span>
                <i class="fas fa-calendar-alt text-slate-400 text-sm"></i>
                <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()" class="text-sm font-medium text-slate-700 outline-none bg-transparent cursor-pointer">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg font-semibold text-sm hover:bg-slate-800 transition-colors shadow-sm">Filter</button>
            
            @if(request()->has('search') || request()->has('start_date') || request()->has('end_date'))
                <a href="{{ route('sales-reports.index') }}" class="px-3 py-2 text-red-600 text-sm font-semibold hover:text-red-700">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-white border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                <tr>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">DR Number</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4 text-right">Total Sale</th>
                    <th class="px-6 py-4 text-right">Paid</th>
                    <th class="px-6 py-4 text-right">Balance</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sales as $sale)
                    @php
                        $paid = $sale->payments_sum_amount ?? 0;
                        $balance = $sale->total_amount - $paid;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 text-slate-600 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($sale->delivery_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900 font-mono">{{ $sale->dr_number }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('deliveries.store-profile', $sale->store_id) }}" class="font-bold text-slate-900 hover:text-caramel-600 transition-colors">{{ $sale->store->store_name ?? 'Unknown' }}</a>
                        </td>
                        <td class="px-6 py-4 text-right font-black text-slate-900">&#8369;{{ number_format($sale->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right font-medium text-emerald-600">&#8369;{{ number_format($paid, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ $balance > 0 ? 'text-red-600' : 'text-slate-400' }}">&#8369;{{ number_format($balance, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($balance <= 0)
                                <span class="inline-flex px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[10px] font-bold border border-emerald-200 uppercase tracking-wide">Paid</span>
                            @elseif($paid > 0)
                                <span class="inline-flex px-2.5 py-1 bg-amber-50 text-amber-700 rounded-md text-[10px] font-bold border border-amber-200 uppercase tracking-wide">Partial</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 bg-red-50 text-red-700 rounded-md text-[10px] font-bold border border-red-200 uppercase tracking-wide">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($balance > 0)
                                <a href="{{ route('payments.index', ['store_id' => $sale->store_id, 'delivery_id' => $sale->id]) }}" class="px-3 py-1.5 bg-caramel-100 text-caramel-700 rounded-md text-[10px] font-bold hover:bg-caramel-200 transition-colors uppercase tracking-wider shadow-sm">
                                    Pay
                                </a>
                            @else
                                <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-20">
                            <div class="w-20 h-20 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-file-invoice text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-1">No sales found</h3>
                            <p class="text-sm text-slate-500">Create a delivery to start recording sales.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/30">{{ $sales->withQueryString()->links() }}</div>
</div>
@endsection