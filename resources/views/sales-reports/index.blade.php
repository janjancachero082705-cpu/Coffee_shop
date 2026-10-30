@extends('layouts.admin')
@section('title', 'Sales Reports')
@section('subtitle', 'Track every delivery as a sale and monitor payments')

@section('content')

<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
        <div class="card p-4 md:p-5 border-l-4 border-l-brand-500 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-brand-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Sales</div>
                <div class="text-xl md:text-2xl font-black text-ink-900">₱{{ number_format($totalSales, 2) }}</div>
            </div>
        </div>
        <div class="card p-4 md:p-5 border-l-4 border-l-emerald-500 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Collected</div>
                <div class="text-xl md:text-2xl font-black text-emerald-600">₱{{ number_format($totalCollected, 2) }}</div>
            </div>
        </div>
        <div class="card p-4 md:p-5 border-l-4 border-l-red-500 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Outstanding</div>
                <div class="text-xl md:text-2xl font-black text-red-600">₱{{ number_format($totalOutstanding, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- SALES LEDGER (With Card/List Toggle) --}}
    <div class="card overflow-hidden">
        
        {{-- Header & Filters --}}
        <div class="p-4 md:p-5 border-b border-ink-200 bg-ink-50">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-4">
                <div>
                    <h3 class="font-bold text-ink-800 text-sm md:text-lg">Sales Ledger</h3>
                    <p class="text-[10px] md:text-xs text-ink-500 mt-0.5">Every delivery is recorded here as a sale.</p>
                </div>

                {{-- VIEW TOGGLE BUTTONS --}}
                <div class="flex items-center gap-1 bg-ink-100 p-1 rounded-lg border border-ink-200">
                    <button id="btn-card" onclick="setSalesView('card')" class="w-8 h-8 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors" title="Card View"><i class="fas fa-th-large text-[11px]"></i></button>
                    <button id="btn-list" onclick="setSalesView('list')" class="w-8 h-8 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors" title="List View"><i class="fas fa-list text-[11px]"></i></button>
                </div>
            </div>
            
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

        {{-- ========================================== --}}
        {{-- 1. CARD VIEW --}}
        {{-- ========================================== --}}
        <div id="sales-card-view" class="p-4 md:p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($sales as $sale)
                @php
                    $paid = $sale->payments_sum_amount ?? 0;
                    $balance = $sale->total_amount - $paid;
                @endphp
                <div class="card p-4 border border-ink-200 hover:border-brand-300 hover:shadow-md transition-all duration-200 shadow-sm flex flex-col justify-between bg-white rounded-xl">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-mono font-bold text-ink-900 text-sm bg-ink-50 px-2 py-0.5 rounded border border-ink-100 inline-block">{{ $sale->dr_number }}</div>
                                <div class="text-[10px] text-ink-500 font-medium mt-1.5 flex items-center gap-1">
                                    <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($sale->delivery_date)->format('M d, Y') }}
                                </div>
                            </div>
                            @if($balance <= 0)
                                <span class="inline-flex px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[9px] font-bold border border-emerald-200 uppercase tracking-wide">Paid</span>
                            @elseif($paid > 0)
                                <span class="inline-flex px-2 py-1 bg-amber-50 text-amber-600 rounded-md text-[9px] font-bold border border-amber-200 uppercase tracking-wide">Partial</span>
                            @else
                                <span class="inline-flex px-2 py-1 bg-red-50 text-red-600 rounded-md text-[9px] font-bold border border-red-200 uppercase tracking-wide">Unpaid</span>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-[9px] font-bold text-ink-400 uppercase tracking-widest mb-1">Customer</div>
                            <a href="{{ route('deliveries.store-profile', $sale->store_id) }}" class="font-bold text-ink-800 text-sm hover:text-brand-600 truncate block">{{ $sale->store->store_name ?? 'Unknown' }}</a>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 bg-[#FAF9F7] p-3 rounded-lg border border-ink-100">
                            <div>
                                <div class="text-[9px] font-bold text-ink-400 uppercase mb-0.5">Total Sale</div>
                                <div class="font-black text-ink-900 text-sm">₱{{ number_format($sale->total_amount, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-bold text-ink-400 uppercase mb-0.5">Balance</div>
                                <div class="font-black {{ $balance > 0 ? 'text-red-600' : 'text-emerald-600' }} text-sm">₱{{ number_format($balance, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-t border-ink-100">
                        @if($balance > 0)
                            <a href="{{ route('payments.index', ['store_id' => $sale->store_id, 'delivery_id' => $sale->id]) }}" class="btn btn-primary w-full !py-2 !text-xs">
                                <i class="fas fa-money-bill-wave"></i> Pay Balance
                            </a>
                        @else
                            <div class="w-full py-2 text-center text-[10px] font-bold text-emerald-600 uppercase tracking-widest bg-emerald-50 rounded-lg border border-emerald-100">
                                <i class="fas fa-check-circle mr-1"></i> Fully Paid
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100">
                        <i class="fas fa-file-invoice text-2xl text-ink-300"></i>
                    </div>
                    <h3 class="text-base font-bold text-ink-800 mb-1">No sales found</h3>
                    <p class="text-xs text-ink-500">Create a delivery to start recording sales.</p>
                </div>
            @endforelse
        </div>

        {{-- ========================================== --}}
        {{-- 2. LIST VIEW --}}
        {{-- ========================================== --}}
        <div id="sales-list-view" class="hidden overflow-x-auto w-full pb-2" style="-webkit-overflow-scrolling: touch;">
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
        
        {{-- PAGINATION --}}
        <div class="px-4 py-3 md:px-6 md:py-4 border-t border-ink-200 bg-ink-50 text-sm">
            {{ $sales->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── VIEW TOGGLE LOGIC ──
    window.setSalesView = function(view) {
        localStorage.setItem('sales_view_pref', view);
        const cardView = document.getElementById('sales-card-view');
        const listView = document.getElementById('sales-list-view');
        const btnCard = document.getElementById('btn-card');
        const btnList = document.getElementById('btn-list');

        if (view === 'card') {
            cardView.classList.remove('hidden');
            listView.classList.add('hidden');
            btnCard.className = 'w-8 h-8 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors';
            btnList.className = 'w-8 h-8 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors';
        } else {
            cardView.classList.add('hidden');
            listView.classList.remove('hidden');
            btnList.className = 'w-8 h-8 flex items-center justify-center rounded bg-white text-ink-900 shadow-sm transition-colors';
            btnCard.className = 'w-8 h-8 flex items-center justify-center rounded text-ink-500 hover:text-ink-900 transition-colors';
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        // I-load ang gi-save nga preference, default kay 'card'
        const savedView = localStorage.getItem('sales_view_pref') || 'card';
        setSalesView(savedView);
    });
</script>
@endpush