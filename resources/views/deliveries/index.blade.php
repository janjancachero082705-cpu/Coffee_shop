@extends('layouts.admin')
@section('title', 'Distributions')
@section('subtitle', 'Manage deliveries and track sales')

@section('content')

{{-- STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Deliveries</div>
            <div class="text-3xl font-black text-slate-900">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-truck text-blue-400 mr-1"></i> All time records</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pending Payments</div>
            <div class="text-3xl font-black text-amber-600">{{ number_format($stats['pending']) }}</div>
            <div class="text-xs text-amber-500 mt-2 font-medium"><i class="fas fa-clock mr-1"></i> Needs collection</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Completed Sales</div>
            <div class="text-3xl font-black text-emerald-600">{{ number_format($stats['completed']) }}</div>
            <div class="text-xs text-emerald-500 mt-2 font-medium"><i class="fas fa-check-circle mr-1"></i> Fully paid</div>
        </div>
    </div>

    <div class="bg-slate-900 rounded-xl shadow-lg shadow-slate-900/20 p-5 relative overflow-hidden group">
        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-700 opacity-30 group-hover:scale-110 transition-transform"><i class="fas fa-chart-line fa-5x"></i></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Sales Value</div>
            <div class="text-3xl font-black text-caramel-400">&#8369;{{ number_format($stats['total_value'], 0) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-peso-sign mr-1"></i> Based on deliveries</div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT CARD --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    
    {{-- HEADER & FILTERS --}}
    <div class="p-5 border-b border-slate-200 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-list-ul text-caramel-500"></i> Delivery Records
            </h3>
            <p class="text-xs text-slate-500 mt-1 font-medium">List of all dispatched products treated as sales.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
            <form method="GET" action="{{ route('deliveries.index') }}" class="relative w-full sm:w-72 group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-caramel-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR or Customer..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 focus:ring-2 focus:ring-caramel-500/20 outline-none transition-all shadow-sm">
            </form>
            
            <a href="{{ route('deliveries.create') }}" class="w-full sm:w-auto px-5 py-2.5 bg-caramel-500 hover:bg-caramel-600 text-white rounded-lg font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> New Delivery
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    @if($deliveries->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4 w-40">DR Number</th>
                        <th class="px-6 py-4">Customer Details</th>
                        <th class="px-6 py-4 w-40">Delivery Date</th>
                        <th class="px-6 py-4 text-right w-40">Sale Amount</th>
                        <th class="px-6 py-4 text-center w-32">Status</th>
                        <th class="px-6 py-4 text-center w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($deliveries as $delivery)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            {{-- DR Number --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 font-mono">{{ $delivery->dr_number }}</div>
                            </td>
                            
                            {{-- Customer --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-200 shadow-sm flex-shrink-0">
                                        {{ strtoupper(substr($delivery->store->store_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('deliveries.store-profile', $delivery->store_id) }}" class="font-bold text-slate-900 hover:text-caramel-600 transition-colors">
                                            {{ $delivery->store->store_name ?? 'Unknown Customer' }}
                                        </a>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">{{ $delivery->store->customer_type ?? 'Regular' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">
                                <i class="far fa-calendar-alt mr-1.5 text-slate-400"></i> {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}
                            </td>
                            
                            {{-- Amount --}}
                            <td class="px-6 py-4 text-right font-black text-slate-900">
                                &#8369;{{ number_format($delivery->total_amount, 2) }}
                            </td>
                            
                            {{-- Status (GIKAN NI, GI-ADD NATO ANG PARTIAL) --}}
                            <td class="px-6 py-4 text-center">
                                @if($delivery->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-md text-[10px] font-bold border border-red-200 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Unpaid
                                    </span>
                                @elseif($delivery->status === 'partial')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-md text-[10px] font-bold border border-amber-200 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Partial
                                    </span>
                                @elseif($delivery->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[10px] font-bold border border-emerald-200 uppercase tracking-wide">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-[10px] font-bold border border-slate-200 uppercase tracking-wide">
                                        <i class="fas fa-ban"></i> Cancelled
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('deliveries.store-profile', $delivery->store_id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 transition-colors shadow-sm" title="View Customer Profile">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if($delivery->status !== 'completed')
                                        <a href="{{ route('payments.index', ['store_id' => $delivery->store_id, 'delivery_id' => $delivery->id]) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-700 transition-colors shadow-sm" title="Record Payment">
                                            <i class="fas fa-money-bill-wave text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $deliveries->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-24 bg-white">
            <div class="w-24 h-24 mx-auto bg-slate-50 rounded-2xl flex items-center justify-center mb-5 border border-slate-100">
                <i class="fas fa-truck-loading text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">No deliveries found</h3>
            <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">You haven't created any deliveries yet. Create your first one to start recording sales.</p>
            <a href="{{ route('deliveries.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 shadow-md transition-all">
                <i class="fas fa-plus"></i> Create First Delivery
            </a>
        </div>
    @endif
</div>
@endsection