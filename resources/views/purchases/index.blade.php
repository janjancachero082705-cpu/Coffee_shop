@extends('layouts.admin')
@section('title', 'Purchases')
@section('subtitle', 'Manage suppliers and track purchase history')

@section('content')
<style>
    /* ==========================================
       PURCHASE HISTORY MOBILE RESPONSIVE FIX
       ========================================== */
    @media (max-width: 767px) {
        /* 1. Siguraduhon nga mo-scroll ang table sa kilid */
        .history-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.75rem;
            border: 1px solid #E8E5E0;
        }
        
        /* 2. Pugngan ang table sa pag-squish */
        .history-table-wrapper table {
            min-width: 700px;
            width: 100%;
        }

        /* 3. I-adjust ang mga text ug badges sa mobile */
        .history-table-wrapper th, 
        .history-table-wrapper td {
            font-size: 11px !important;
            padding: 0.75rem 0.5rem !important;
            white-space: nowrap;
        }

        /* 4. Ipa-stack ang mga header controls kung naay filter/search */
        .history-controls {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        /* 5. Ipa-full width ang search bar ug buttons */
        .history-controls input,
        .history-controls select,
        .history-controls button {
            width: 100% !important;
        }
    }
</style>

<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">

    {{-- TOP STATS CARDS --}}
    {{-- 2 columns sa mobile para magkasya, 3 sa desktop --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4">
        {{-- Total Spent --}}
        <div class="card p-3 md:p-5 border-l-4 border-l-brand-500 relative overflow-hidden group">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-2 history-controls flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <div>
                    <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Spent</div>
                    <div class="text-lg md:text-3xl font-black text-ink-900">₱{{ number_format($totalSpent, 2) }}</div>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center shrink-0 border border-brand-200 self-start md:self-auto">
                    <i class="fas fa-wallet text-xs md:text-lg"></i>
                </div>
            </div>
        </div>
        
        {{-- Total Transactions --}}
        <div class="card p-3 md:p-5 border-l-4 border-l-ink-400 relative overflow-hidden group">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-2 history-controls flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <div>
                    <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Trans.</div>
                    <div class="text-lg md:text-3xl font-black text-ink-900">{{ $purchases->total() }}</div>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-ink-100 text-ink-600 flex items-center justify-center shrink-0 border border-ink-200 self-start md:self-auto">
                    <i class="fas fa-file-invoice text-xs md:text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Quick Action (Spans 2 columns on mobile) --}}
        <div class="col-span-2 sm:col-span-1 card p-3 md:p-5 border-l-4 border-l-emerald-500 relative overflow-hidden group">
            <div class="relative z-10 h-full flex flex-col justify-center">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary w-full md:w-auto !py-2.5 !px-4 flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
    <i class="fas fa-plus-circle text-sm"></i>
    <span>Record New Purchase</span>
</a>
            </div>
        </div>
    </div>

    {{-- TABS NAVIGATION --}}
    <div class="flex gap-1 bg-ink-100 p-1 rounded-xl border border-ink-200 w-full sm:w-max">
        <button onclick="switchPurchTab('suppliers')" id="tabBtn-suppliers" class="flex-1 sm:flex-none justify-center px-1 sm:px-6 py-2 sm:py-2.5 text-[10px] sm:text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-1 sm:gap-2 hover:bg-ink-200/50">
            <i class="fas fa-users"></i> Suppliers
        </button>
        <button onclick="switchPurchTab('history')" id="tabBtn-history" class="flex-1 sm:flex-none justify-center px-1 sm:px-6 py-2 sm:py-2.5 text-[10px] sm:text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-1 sm:gap-2 hover:bg-ink-200/50">
            <i class="fas fa-list"></i> History
        </button>
    </div>

    {{-- ══════ TAB 1: SUPPLIERS ══════ --}}
    <div id="tab-suppliers" class="space-y-4 md:space-y-6">
        @if($suppliers->count() > 0)
            {{-- 1 column sa mobile, 2 sa tablet, 4 sa desktop --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-5">
                @foreach($suppliers as $supplier)
                    @php $isActive = request('supplier') == $supplier->supplier_name; @endphp
                    <div class="card flex flex-col group relative overflow-hidden transition-all duration-300 {{ $isActive ? 'border-brand-500 ring-2 ring-brand-500/20 shadow-md' : 'hover:border-brand-300 hover:shadow-lg hover:-translate-y-1' }}">
                        
                        {{-- Card Header --}}
                        <div class="p-4 flex items-start gap-3 border-b border-ink-100 bg-white">
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-lg {{ $isActive ? 'bg-brand-100 text-brand-600 border-brand-200' : 'bg-ink-50 text-ink-500 border-ink-200' }} flex items-center justify-center text-base md:text-lg shadow-sm border flex-shrink-0">
                                <i class="fas fa-truck-moving"></i>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h4 class="font-bold text-ink-900 text-[13px] md:text-sm truncate" title="{{ $supplier->supplier_name }}">{{ $supplier->supplier_name }}</h4>
                                <div class="text-[9px] md:text-[10px] font-bold text-ink-400 uppercase tracking-wider mt-0.5">
                                    {{ $supplier->total_orders }} {{ \Illuminate\Support\Str::plural('Transaction', $supplier->total_orders) }}
                                </div>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="p-3 md:p-4 bg-ink-50/50 flex-1 flex flex-col justify-center">
                            <div class="text-[9px] md:text-[10px] font-bold text-ink-500 uppercase tracking-wider mb-0.5 md:mb-1">Total Spent</div>
                            <div class="text-lg md:text-xl font-black text-ink-900">₱{{ number_format($supplier->total_spent, 2) }}</div>
                        </div>

                        {{-- Actions --}}
                        <div class="p-2.5 md:p-3 border-t border-ink-100 bg-white flex gap-2">
                            <a href="{{ route('purchases.index', ['supplier' => $supplier->supplier_name, 'tab' => 'history']) }}" class="flex-1 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold rounded-lg {{ $isActive ? 'bg-ink-900 text-white shadow-sm' : 'bg-ink-100 text-ink-700 hover:bg-ink-200' }} transition-colors flex items-center justify-center gap-1">
                                View History
                            </a>
                            <a href="{{ route('purchases.create', ['supplier' => $supplier->supplier_name]) }}" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-brand-50 text-brand-600 hover:bg-brand-500 hover:text-white transition-all shadow-sm border border-brand-100 flex-shrink-0" title="New Purchase">
                                <i class="fas fa-plus text-[10px] md:text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card text-center py-16 md:py-24">
                <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100 shadow-inner">
                    <i class="fas fa-truck text-2xl text-ink-300"></i>
                </div>
                <h3 class="text-lg font-bold text-ink-800 mb-1">No suppliers yet</h3>
                <p class="text-xs text-ink-500 mb-5">Start by recording your first purchase.</p>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary mx-auto">
                    <i class="fas fa-plus"></i> Record Purchase
                </a>
            </div>
        @endif
    </div>

    {{-- ══════ TAB 2: HISTORY ══════ --}}
    <div id="tab-history" class="hidden space-y-4 md:space-y-6">
        <div class="card overflow-hidden">
            
            {{-- Header & Filters --}}
            <div class="p-4 md:p-5 border-b border-ink-200 bg-ink-50">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-4 history-controls flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <div>
                        <h3 class="font-bold text-ink-800 text-sm md:text-lg flex items-center gap-2">
                            @if(request('supplier'))
                                History: <span class="text-brand-600">{{ request('supplier') }}</span>
                            @else
                                All Purchase History
                            @endif
                        </h3>
                    </div>
                    @if(request('supplier'))
                        <a href="{{ route('purchases.index', ['tab' => 'history']) }}" class="text-[10px] md:text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-md border border-red-200 flex items-center gap-1 transition-colors">
                            <i class="fas fa-times"></i> Clear Filter
                        </a>
                    @endif
                </div>
                
                <form method="GET" action="{{ route('purchases.index') }}" id="filterForm" class="flex flex-col sm:flex-row lg:items-center gap-2 md:gap-3">
                    <input type="hidden" name="tab" value="history">
                    @if(request('supplier'))
                        <input type="hidden" name="supplier" value="{{ request('supplier') }}">
                    @endif
                    
                    <div class="relative group flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search item name..." class="input pl-9 !py-2 text-xs md:text-sm shadow-sm w-full">
                    </div>

                    <div class="w-full sm:w-40 lg:w-48">
                        <select name="product_id" onchange="this.form.submit()" class="input cursor-pointer font-medium !py-2 text-xs md:text-sm shadow-sm w-full">
                            <option value="">All Items</option>
                            @foreach($purchasedItems as $item)
                                <option value="{{ $item->id }}" {{ request('product_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Filters (Naka-stack sa mobile) --}}
                    <div class="flex flex-col sm:flex-row items-center gap-2 bg-white border border-ink-200 rounded-lg shadow-sm px-3 py-1 w-full lg:w-auto">
                        <div class="flex items-center gap-1 w-full">
                            <i class="fas fa-calendar-alt text-ink-400 text-[10px] md:text-xs"></i>
                            <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()" class="text-[11px] md:text-xs font-semibold text-ink-700 outline-none bg-transparent cursor-pointer w-full">
                        </div>
                        <span class="text-ink-400 font-bold text-[9px] md:text-[10px] uppercase hidden sm:block">to</span>
                        <div class="flex items-center gap-1 w-full">
                            <i class="fas fa-calendar-alt text-ink-400 text-[10px] md:text-xs"></i>
                            <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()" class="text-[11px] md:text-xs font-semibold text-ink-700 outline-none bg-transparent cursor-pointer w-full">
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Table (May sariling scrollbar, hindi bubuka ang page) --}}
            <div class="overflow-x-auto w-full pb-2" style="-webkit-overflow-scrolling: touch;">
                <table class="table-clean w-full min-w-[700px]">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Date</th>
                            <th class="whitespace-nowrap">Supplier</th>
                            <th>Item Purchased</th>
                            <th class="text-center whitespace-nowrap">Qty</th>
                            <th class="text-right whitespace-nowrap">Cost / Unit</th>
                            <th class="text-right whitespace-nowrap">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                            <tr class="group">
                                <td class="text-ink-500 font-medium whitespace-nowrap text-xs md:text-sm">{{ \Carbon\Carbon::parse($p->purchase_date)->format('M d, Y') }}</td>
                                <td class="font-bold text-ink-900 whitespace-nowrap text-xs md:text-sm">{{ $p->supplier_name }}</td>
                                <td class="text-ink-700 font-medium text-xs md:text-sm">{{ $p->variant->product->name ?? 'Unknown' }}</td>
                                <td class="text-center whitespace-nowrap">
                                    <span class="inline-flex bg-ink-100 px-2 py-1 rounded-md text-[10px] md:text-xs font-bold text-ink-700 border border-ink-200">{{ number_format($p->quantity, 2) }} {{ $p->variant->unit ?? '' }}</span>
                                </td>
                                <td class="text-right font-medium text-ink-600 whitespace-nowrap text-xs md:text-sm">₱{{ number_format($p->cost_per_unit, 2) }}</td>
                                <td class="text-right font-black text-ink-900 whitespace-nowrap text-xs md:text-sm">₱{{ number_format($p->total_cost, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16">
                                    <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100">
                                        <i class="fas fa-search text-2xl text-ink-300"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-ink-800 mb-1">No purchases found</h3>
                                    <p class="text-xs text-ink-500">Try adjusting your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 md:px-6 md:py-4 border-t border-ink-200 bg-ink-50 text-sm">{{ $purchases->withQueryString()->links() }}</div>
        </div>
    </div>
</div>

<script>
    // Gamiton nato ang window.onload para masiguro nga na-load na tanan script sa system
    window.addEventListener('load', function() {
        // Kuhaon ang parameters sa URL
        const urlParams = new URLSearchParams(window.location.search);
        const hasSearch = urlParams.has('search') && urlParams.get('search') !== '';
        const isHistoryTab = urlParams.get('tab') === 'history';

        // Kung gikan sa "View History" (naay search) o gi-click ang History tab
        if (hasSearch || isHistoryTab) {
            
            // Pangitaon ang button sa History pinaagi sa text content niini
            const allButtons = document.querySelectorAll('button');
            let historyBtn = null;

            allButtons.forEach(btn => {
                if (btn.textContent.trim().toLowerCase().includes('history')) {
                    historyBtn = btn;
                }
            });

            // I-click ang History tab
            if (historyBtn) {
                setTimeout(() => {
                    historyBtn.click();
                    
                    // I-fill pud ang search bar kung naay dala nga ngalan
                    const searchParam = urlParams.get('search');
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput && searchParam) {
                        searchInput.value = searchParam;
                    }
                }, 100); // 100ms delay para mo-initialize una ang system scripts
            }
        }
    });
</script>
@endsection

@push('scripts')
<script>
    // ── TAB SWITCHING LOGIC ──
    function switchPurchTab(tabName) {
        document.getElementById('tab-suppliers').classList.add('hidden');
        document.getElementById('tab-history').classList.add('hidden');
        
        // Reset buttons
        document.getElementById('tabBtn-suppliers').className = "flex-1 sm:flex-none justify-center px-1 sm:px-6 py-2 sm:py-2.5 text-[10px] sm:text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-1 sm:gap-2 hover:bg-ink-200/50";
        document.getElementById('tabBtn-history').className = "flex-1 sm:flex-none justify-center px-1 sm:px-6 py-2 sm:py-2.5 text-[10px] sm:text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-1 sm:gap-2 hover:bg-ink-200/50";

        // Show selected
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Active button
        document.getElementById('tabBtn-' + tabName).className = "flex-1 sm:flex-none justify-center px-1 sm:px-6 py-2 sm:py-2.5 text-[10px] sm:text-sm font-bold rounded-lg bg-white text-ink-900 shadow-sm border border-ink-200 transition-all flex items-center gap-1 sm:gap-2";
    }

    // Auto-open tab base sa URL
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'suppliers'; 
        switchPurchTab(tab);
    });
</script>
@endpush