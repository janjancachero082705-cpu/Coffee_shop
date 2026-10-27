@extends('layouts.admin')
@section('title', 'Sales Reports')
@section('subtitle', 'Track every delivery as a sale and monitor payments')

@section('content')

<div class="w-full max-w-screen-2xl mx-auto space-y-6">

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Sales --}}
        <div class="card p-5 border-l-4 border-l-brand-500">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Sales</div>
            <div class="text-2xl font-black text-ink-900">&#8369;{{ number_format($totalSales, 2) }}</div>
            <div class="text-xs text-brand-600 mt-2 font-medium"><i class="fas fa-truck mr-1"></i> From Deliveries</div>
        </div>
        
        {{-- Total Collected --}}
        <div class="card p-5 border-l-4 border-l-emerald-500">
            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Total Collected</div>
            <div class="text-2xl font-black text-emerald-700">&#8369;{{ number_format($totalCollected, 2) }}</div>
            <div class="text-xs text-emerald-500 mt-2 font-medium"><i class="fas fa-hand-holding-usd mr-1"></i> Payments Received</div>
        </div>

        {{-- Outstanding Balance --}}
        <div class="card p-5 border-l-4 border-l-red-500">
            <div class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-1">Outstanding Balance</div>
            <div class="text-2xl font-black text-red-600">&#8369;{{ number_format($totalOutstanding, 2) }}</div>
            <div class="text-xs text-red-400 mt-2 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> Unpaid sales</div>
        </div>
    </div>

    {{-- FILTERS & LEDGER --}}
    <div class="card overflow-hidden">
        
        {{-- Header & Filters --}}
        <div class="p-5 border-b border-ink-200 bg-ink-50 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-ink-900 text-lg">Sales Ledger</h3>
                <p class="text-xs text-ink-500 mt-1">Every delivery is recorded here as a sale.</p>
            </div>
            
            <form method="GET" action="{{ route('sales-reports.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative group w-full md:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-sm group-focus-within:text-brand-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR or Customer..." class="input !pl-9">
                </div>
                
                <div class="flex items-center gap-2 bg-white border border-ink-200 rounded-lg shadow-sm px-3 py-2">
                    <i class="fas fa-calendar-alt text-ink-400 text-sm"></i>
                    <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()" class="text-sm font-medium text-ink-700 outline-none bg-transparent cursor-pointer">
                    <span class="text-ink-400 font-bold px-1">to</span>
                    <i class="fas fa-calendar-alt text-ink-400 text-sm"></i>
                    <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()" class="text-sm font-medium text-ink-700 outline-none bg-transparent cursor-pointer">
                </div>
                
                <button type="submit" class="btn btn-primary">Filter</button>
                
                @if(request()->has('search') || request()->has('start_date') || request()->has('end_date'))
                    <a href="{{ route('sales-reports.index') }}" class="text-sm font-semibold text-red-500 hover:text-red-600 px-2">Clear</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>DR Number</th>
                        <th>Customer</th>
                        <th class="text-right">Total Sale</th>
                        <th class="text-right">Paid</th>
                        <th class="text-right">Balance</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        @php
                            $paid = $sale->payments_sum_amount ?? 0;
                            $balance = $sale->total_amount - $paid;
                        @endphp
                        <tr class="group">
                            <td class="text-ink-500 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($sale->delivery_date)->format('M d, Y') }}</td>
                            <td class="font-bold text-ink-900 font-mono">{{ $sale->dr_number }}</td>
                            <td>
                                <a href="{{ route('deliveries.store-profile', $sale->store_id) }}" class="font-bold text-ink-900 hover:text-brand-600 transition-colors">{{ $sale->store->store_name ?? 'Unknown' }}</a>
                            </td>
                            <td class="text-right font-black text-ink-900">&#8369;{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-right font-medium text-emerald-600">&#8369;{{ number_format($paid, 2) }}</td>
                            <td class="text-right font-bold {{ $balance > 0 ? 'text-red-600' : 'text-ink-400' }}">&#8369;{{ number_format($balance, 2) }}</td>
                            <td class="text-center">
                                @if($balance <= 0)
                                    <span class="inline-flex px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[10px] font-bold border border-emerald-200 uppercase tracking-wide">Paid</span>
                                @elseif($paid > 0)
                                    <span class="inline-flex px-2.5 py-1 bg-amber-50 text-amber-600 rounded-md text-[10px] font-bold border border-amber-200 uppercase tracking-wide">Partial</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 bg-red-50 text-red-600 rounded-md text-[10px] font-bold border border-red-200 uppercase tracking-wide">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($balance > 0)
                                    <a href="{{ route('payments.index', ['store_id' => $sale->store_id, 'delivery_id' => $sale->id]) }}" class="btn btn-primary !py-1.5 !px-3 !text-[10px] uppercase tracking-wider">
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
                                <div class="w-20 h-20 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-invoice text-3xl text-ink-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-ink-800 mb-1">No sales found</h3>
                                <p class="text-sm text-ink-500">Create a delivery to start recording sales.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-ink-200 bg-ink-50">
            {{ $sales->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection