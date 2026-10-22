@extends('layouts.admin')
@section('title', 'Sales Reports')
@section('subtitle', 'Financial overview per store')

@section('content')
{{-- GLOBAL STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-blue-50 opacity-50 group-hover:scale-110 transition-transform"><i class="fas fa-truck fa-6x"></i></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Gross Sales (Delivered)</div>
            <div class="text-3xl font-black text-espresso-900">&#8369;{{ number_format($totalDelivered, 0) }}</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-emerald-50 opacity-50 group-hover:scale-110 transition-transform"><i class="fas fa-hand-holding-usd fa-6x"></i></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Collected</div>
            <div class="text-3xl font-black text-emerald-600">&#8369;{{ number_format($totalCollected, 0) }}</div>
        </div>
    </div>

    <div class="bg-espresso-900 rounded-xl shadow-sm p-5 border border-espresso-800 flex items-center justify-between relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 text-espresso-800 opacity-50 group-hover:scale-110 transition-transform"><i class="fas fa-exclamation-circle fa-6x"></i></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-espresso-400 uppercase tracking-wider mb-1">Outstanding Balance</div>
            <div class="text-3xl font-black text-caramel-400">&#8369;{{ number_format($totalOutstanding, 0) }}</div>
        </div>
    </div>
</div>

{{-- STORE CARDS GRID --}}
<div class="mb-6 flex justify-between items-end">
    <div>
        <h3 class="font-bold text-espresso-900 text-lg">Store Balances</h3>
        <p class="text-xs text-espresso-500">Financial breakdown per customer</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @foreach($stores as $store)
        @php
            $delivered = $store->deliveries_sum_total_amount ?? 0;
            $paid = $store->payments_sum_amount ?? 0;
            $balance = $store->balance;
            
            // Percentage of paid amount
            $percentPaid = $delivered > 0 ? min(100, ($paid / $delivered) * 100) : 0;

            // Status Logic
            if ($balance <= 0 && $delivered > 0) {
                $status = 'Paid';
                $statusColor = 'emerald';
                $statusIcon = 'fa-check-circle';
            } elseif ($paid > 0) {
                $status = 'Partial';
                $statusColor = 'amber';
                $statusIcon = 'fa-adjust';
            } else {
                $status = 'Unpaid';
                $statusColor = 'red';
                $statusIcon = 'fa-clock';
            }
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-espresso-200 hover:border-{{ $statusColor }}-400 hover:shadow-md transition-all duration-300 relative overflow-hidden group flex flex-col">
            {{-- Top Accent Line --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-{{ $statusColor }}-500"></div>

            {{-- Card Header --}}
            <div class="p-6 pb-4 flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-espresso-50 text-espresso-700 flex items-center justify-center font-bold text-lg border border-espresso-200 shadow-sm group-hover:bg-{{ $statusColor }}-50 group-hover:text-{{ $statusColor }}-700 transition-colors">
                        {{ strtoupper(substr($store->store_name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-espresso-900 text-base line-clamp-1">{{ $store->store_name }}</h4>
                        <div class="text-[10px] text-espresso-500 font-bold uppercase tracking-wider mt-0.5">{{ $store->customer_type }}</div>
                    </div>
                </div>

                <span class="px-2.5 py-1 bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 rounded-md text-[10px] font-bold border border-{{ $statusColor }}-200 uppercase tracking-wide flex items-center gap-1 shadow-sm">
                    <i class="fas {{ $statusIcon }}"></i> {{ $status }}
                </span>
            </div>

            {{-- Financial Data --}}
            <div class="px-6 flex-1 space-y-3 mb-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-espresso-500 font-medium">Total Delivered</span>
                    <span class="font-bold text-espresso-900">&#8369;{{ number_format($delivered, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-espresso-500 font-medium">Total Paid</span>
                    <span class="font-bold text-emerald-600">&#8369;{{ number_format($paid, 2) }}</span>
                </div>
                
                {{-- Progress Bar --}}
                <div class="pt-2">
                    <div class="flex justify-between text-[10px] font-bold text-espresso-400 mb-1">
                        <span>Payment Progress</span>
                        <span>{{ number_format($percentPaid, 0) }}%</span>
                    </div>
                    <div class="w-full bg-espresso-100 rounded-full h-1.5">
                        <div class="bg-{{ $statusColor }}-500 h-1.5 rounded-full" style="width: {{ $percentPaid }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Balance Footer --}}
            <div class="px-6 py-4 bg-espresso-50/50 border-t border-espresso-100 flex justify-between items-center mt-auto">
                <span class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider">Balance</span>
                <span class="font-black {{ $balance > 0 ? 'text-red-600' : 'text-emerald-600' }} text-xl">
                    &#8369;{{ number_format($balance, 2) }}
                </span>
            </div>
        </div>
    @endforeach
</div>

@if($stores->isEmpty())
    <div class="bg-white rounded-xl border border-espresso-200 text-center py-20 text-espresso-500 shadow-sm">
        <div class="w-20 h-20 mx-auto bg-espresso-50 rounded-2xl flex items-center justify-center mb-5 border border-espresso-100">
            <i class="fas fa-chart-pie text-3xl text-espresso-300"></i>
        </div>
        <h3 class="text-xl font-bold text-espresso-800 mb-2">No data available</h3>
        <p class="text-sm mb-6">Add stores and deliveries to start tracking sales.</p>
    </div>
@endif
@endsection