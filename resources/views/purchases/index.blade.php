@extends('layouts.admin')
@section('title', 'Purchases')
@section('subtitle', 'Manage suppliers and track purchase history')

@section('content')

<div class="w-full max-w-screen-2xl mx-auto space-y-6">

    {{-- TOP STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Spent --}}
        <div class="card p-5 border-l-4 border-l-red-500">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Spent (Filtered)</div>
            <div class="text-3xl font-black text-red-600 tracking-tight">&#8369;{{ number_format($totalSpent, 2) }}</div>
            <div class="text-xs text-ink-500 mt-2 font-medium flex items-center gap-1.5">
                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </div>
        </div>
        
        {{-- Total Transactions --}}
        <div class="card p-5 border-l-4 border-l-ink-400">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Transactions</div>
            <div class="text-3xl font-black text-ink-900 tracking-tight">{{ $purchases->total() }}</div>
            <div class="text-xs text-ink-500 mt-2 font-medium flex items-center gap-1.5">
                <i class="fas fa-receipt"></i> Purchase records
            </div>
        </div>

        {{-- Quick Action --}}
        <div class="card p-5 border-l-4 border-l-brand-500 flex items-center justify-between relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 text-brand-50 opacity-50"><i class="fas fa-truck fa-5x"></i></div>
            <div class="relative z-10">
                <div class="text-[10px] font-bold text-brand-600 uppercase tracking-widest mb-3">Quick Action</div>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle text-lg"></i> Record New Purchase
                </a>
            </div>
        </div>
    </div>

    {{-- TABS NAVIGATION --}}
    <div class="mb-6 flex gap-1 bg-ink-100 p-1.5 rounded-xl border border-ink-200 w-max">
        <button onclick="switchPurchTab('suppliers')" id="tabBtn-suppliers" class="px-6 py-2.5 text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-2 hover:bg-ink-200">
            <i class="fas fa-users"></i> Suppliers Directory
        </button>
        <button onclick="switchPurchTab('history')" id="tabBtn-history" class="px-6 py-2.5 text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-2 hover:bg-ink-200">
            <i class="fas fa-list"></i> Purchase History
        </button>
    </div>

    {{-- ══════ TAB 1: SUPPLIERS ══════ --}}
    <div id="tab-suppliers" class="space-y-6">
        @if($suppliers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($suppliers as $supplier)
                    @php $isActive = request('supplier') == $supplier->supplier_name; @endphp
                    <div class="card flex flex-col group relative overflow-hidden transition-all duration-200 {{ $isActive ? 'border-brand-500 ring-2 ring-brand-500/20' : 'hover:border-brand-300' }}">
                        
                        {{-- Card Header --}}
                        <div class="p-5 flex items-start gap-4 border-b border-ink-100">
                            <div class="w-12 h-12 rounded-xl {{ $isActive ? 'bg-brand-100 text-brand-600' : 'bg-ink-100 text-ink-600' }} flex items-center justify-center text-xl shadow-inner flex-shrink-0 transition-colors">
                                <i class="fas fa-truck-moving"></i>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h4 class="font-bold text-ink-900 text-base truncate" title="{{ $supplier->supplier_name }}">{{ $supplier->supplier_name }}</h4>
                                <div class="text-[10px] font-bold text-ink-400 uppercase tracking-wider mt-1">{{ $supplier->total_orders }} Transactions</div>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="p-5 bg-ink-50/50 flex-1 flex flex-col justify-center">
                            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-wider mb-1">Total Spent</div>
                            <div class="text-2xl font-black text-ink-900">&#8369;{{ number_format($supplier->total_spent, 2) }}</div>
                        </div>

                        {{-- Actions --}}
                        <div class="p-4 border-t border-ink-100 flex gap-2">
                            <a href="{{ route('purchases.index', ['supplier' => $supplier->supplier_name, 'tab' => 'history']) }}" class="flex-1 py-2.5 text-center text-xs font-bold rounded-lg {{ $isActive ? 'bg-ink-900 text-white shadow-sm' : 'bg-ink-100 text-ink-700 hover:bg-ink-200' }} transition-colors">
                                View History
                            </a>
                            <a href="{{ route('purchases.create', ['supplier' => $supplier->supplier_name]) }}" class="w-10 h-10 flex items-center justify-center rounded-lg bg-brand-100 text-brand-700 hover:bg-brand-200 transition-colors flex-shrink-0 shadow-sm" title="New Purchase">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card text-center py-24">
                <div class="w-20 h-20 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-4 border border-ink-200">
                    <i class="fas fa-truck text-3xl text-ink-300"></i>
                </div>
                <h3 class="text-xl font-bold text-ink-800 mb-2">No suppliers yet</h3>
                <p class="text-sm text-ink-500 mb-6">Start by recording your first purchase.</p>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary mx-auto">
                    <i class="fas fa-plus"></i> Record Purchase
                </a>
            </div>
        @endif
    </div>

    {{-- ══════ TAB 2: HISTORY ══════ --}}
    <div id="tab-history" class="hidden space-y-6">
        <div class="card overflow-hidden">
            
            {{-- Header & Filters --}}
            <div class="p-5 border-b border-ink-200 bg-ink-50">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-5">
                    <div>
                        <h3 class="font-bold text-ink-800 text-lg flex items-center gap-2">
                            @if(request('supplier'))
                                History: <span class="text-brand-600">{{ request('supplier') }}</span>
                            @else
                                All Purchase History
                            @endif
                        </h3>
                    </div>
                    @if(request('supplier'))
                        <a href="{{ route('purchases.index', ['tab' => 'history']) }}" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md border border-red-200 flex items-center gap-1.5 transition-colors">
                            <i class="fas fa-times"></i> Clear Supplier Filter
                        </a>
                    @endif
                </div>
                
                <form method="GET" action="{{ route('purchases.index') }}" id="filterForm" class="flex flex-col lg:flex-row lg:items-center gap-3">
                    <input type="hidden" name="tab" value="history">
                    @if(request('supplier'))
                        <input type="hidden" name="supplier" value="{{ request('supplier') }}">
                    @endif
                    
                    <div class="relative group flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search item name..." class="input pl-11">
                    </div>

                    <div class="w-full lg:w-56">
                        <select name="product_id" onchange="this.form.submit()" class="input cursor-pointer font-medium">
                            <option value="">All Items</option>
                            @foreach($purchasedItems as $item)
                                <option value="{{ $item->id }}" {{ request('product_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 bg-white border border-ink-300 rounded-lg shadow-sm px-4 py-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-ink-400 text-sm"></i>
                            <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()" class="text-sm font-semibold text-ink-700 outline-none bg-transparent cursor-pointer">
                        </div>
                        <span class="text-ink-400 font-bold text-xs">TO</span>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-ink-400 text-sm"></i>
                            <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()" class="text-sm font-semibold text-ink-700 outline-none bg-transparent cursor-pointer">
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Item Purchased</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Cost / Unit</th>
                            <th class="text-right">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                            <tr class="group">
                                <td class="text-ink-500 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($p->purchase_date)->format('M d, Y') }}</td>
                                <td class="font-bold text-ink-900">{{ $p->supplier_name }}</td>
                                <td class="text-ink-700 font-medium">{{ $p->variant->product->name ?? 'Unknown' }}</td>
                                <td class="text-center">
                                    <span class="inline-flex bg-ink-100 px-2.5 py-1 rounded-md text-xs font-bold text-ink-700 border border-ink-200">{{ number_format($p->quantity, 2) }} {{ $p->variant->unit ?? '' }}</span>
                                </td>
                                <td class="text-right font-medium text-ink-600">&#8369;{{ number_format($p->cost_per_unit, 2) }}</td>
                                <td class="text-right font-black text-ink-900">&#8369;{{ number_format($p->total_cost, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-20">
                                    <div class="w-20 h-20 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-4 border border-ink-100">
                                        <i class="fas fa-search text-3xl text-ink-300"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-ink-800 mb-1">No purchases found</h3>
                                    <p class="text-sm text-ink-500">Try adjusting your filters or date range.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-ink-200 bg-ink-50">{{ $purchases->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── TAB SWITCHING LOGIC ──
    function switchPurchTab(tabName) {
        // Hide all tabs
        document.getElementById('tab-suppliers').classList.add('hidden');
        document.getElementById('tab-history').classList.add('hidden');
        
        // Reset buttons (Inactive state)
        document.getElementById('tabBtn-suppliers').className = "px-6 py-2.5 text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-2 hover:bg-ink-200";
        document.getElementById('tabBtn-history').className = "px-6 py-2.5 text-sm font-bold rounded-lg text-ink-600 transition-all flex items-center gap-2 hover:bg-ink-200";

        // Show selected tab
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Set active button (Active state)
        document.getElementById('tabBtn-' + tabName).className = "px-6 py-2.5 text-sm font-bold rounded-lg bg-white text-ink-900 shadow-sm border border-ink-200 transition-all flex items-center gap-2";
    }

    // Auto-open tab base sa URL
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'suppliers'; // Default is Suppliers tab
        switchPurchTab(tab);
    });
</script>
@endpush