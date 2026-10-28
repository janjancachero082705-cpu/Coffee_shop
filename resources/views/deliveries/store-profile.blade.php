@extends('layouts.admin')
@section('title', 'Customer Profile')
@section('subtitle', $store->store_name)

@section('content')
<style>
    /* FAIL-SAFE SCROLLBAR CSS */
    @media (max-width: 767px) {
        .overflow-x-auto {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        .overflow-x-auto table {
            min-width: 600px !important;
        }
    }
</style>
<style>
    @media (max-width: 767px) {
        /* Pugson ang tanang table sulod sa accordion nga ma-scroll */
        #dr- * table, .order-history-card table {
            min-width: 600px !important;
        }
        /* I-override ang bisan unsang overflow-hidden */
        #dr- .overflow-hidden, .order-history-card {
            overflow: visible !important;
        }
    }
</style>
<style>
    /* ==========================================
       CUSTOMER PROFILE ORDER HISTORY SCROLL FIX
       ========================================== */
    @media (max-width: 767px) {
        /* Force scrollable table */
        .order-history-card, .card table {
            min-width: 500px !important;
        }
        
        /* Tangtangon ang bisan unsang overflow-hidden sa parent */
        .overflow-hidden {
            overflow: visible !important;
        }
    }
</style>


<div class="max-w-5xl mx-auto space-y-6">

    {{-- 1. HERO PROFILE CARD --}}
    <div class="card p-0 overflow-hidden relative">
        <div class="h-24 bg-gradient-to-r from-brand-500 to-brand-400 w-full"></div>
        
        <div class="px-8 pb-8 relative">
            <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6 -mt-10">
                
                {{-- Avatar & Info --}}
                <div class="flex flex-col md:flex-row items-start md:items-end gap-5">
                    <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-md shrink-0">
                        <div class="w-full h-full rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-4xl border border-brand-100">
                            {{ strtoupper(substr($store->store_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="mb-1">
                        <div class="flex items-center gap-3 mb-1">
                            <h2 class="text-3xl font-black text-ink-900 leading-none">{{ $store->store_name }}</h2>
                            @if($store->customer_type === 'suki')
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200 uppercase tracking-wider">⭐ Suki VIP</span>
                            @elseif($store->customer_type === 'friend')
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase tracking-wider">👥 Friend</span>
                            @else
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-ink-100 text-ink-600 border border-ink-200 uppercase tracking-wider">Regular</span>
                            @endif
                        </div>
                        <div class="text-sm text-ink-500 font-medium flex items-center gap-2">
                            <span><i class="fas fa-map-marker-alt text-ink-400"></i> {{ $store->address ?? 'No address' }}</span>
                            <span class="text-ink-300">|</span>
                            <span><i class="fas fa-phone text-ink-400"></i> {{ $store->contact_number ?? 'No contact' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="shrink-0 w-full md:w-auto">
                    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-primary w-full md:w-auto shadow-md">
                        <i class="fas fa-plus-circle"></i> Create Delivery
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. FINANCIAL SUMMARY (KPI CARDS) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Billed --}}
        <div class="card p-5 flex items-center justify-between border-l-4 border-l-ink-400">
            <div>
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Billed</div>
                <div class="text-2xl font-black text-ink-900">₱{{ number_format($stats['total_sales'] ?? 0, 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-ink-50 flex items-center justify-center text-ink-500">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>

        {{-- Total Paid --}}
        <div class="card p-5 flex items-center justify-between border-l-4 border-l-emerald-500">
            <div>
                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Total Paid</div>
                <div class="text-2xl font-black text-emerald-700">₱{{ number_format($stats['total_paid'] ?? 0, 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        {{-- Outstanding Balance --}}
        <div class="card p-5 flex items-center justify-between border-l-4 border-l-red-500">
            <div>
                <div class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-1">Balance Due</div>
                <div class="text-2xl font-black text-red-600">₱{{ number_format($stats['outstanding'] ?? 0, 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    {{-- 3. ORDER HISTORY ACCORDION --}}
    <div>
        <div class="flex items-center justify-between mb-4 mt-8">
            <div>
                <h3 class="text-lg font-bold text-ink-900">Order History</h3>
                <p class="text-xs text-ink-500">Click an order to expand and view items.</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($store->deliveryReceipts as $dr)
                {{-- Accordion Item --}}
                <div class="card overflow-hidden transition-all duration-200 hover:border-brand-300">
                    
                    {{-- Clickable Header --}}
                    <button type="button" class="w-full px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 focus:outline-none" onclick="toggleAccordion('dr-{{ $dr->id }}')">
                        
                        {{-- Left: ID & Date & Status --}}
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 border border-brand-100 shadow-sm">
                                <i class="fas fa-shopping-bag text-lg"></i>
                            </div>
                            <div class="text-left">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-ink-900 text-lg">{{ $dr->dr_number }}</span>
                                    @if($dr->status === 'delivered' || $dr->status === 'completed')
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-wider">Paid</span>
                                    @elseif($dr->status === 'partial')
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 uppercase tracking-wider">Partial</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-200 uppercase tracking-wider">Unpaid</span>
                                    @endif
                                </div>
                                <div class="text-xs text-ink-500 font-medium mt-1 flex items-center gap-1.5">
                                    <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($dr->delivery_date)->format('F d, Y') }}
                                </div>
                            </div>
                        </div>

                        {{-- Right: Amounts & Toggle Icon --}}
                        <div class="flex items-center gap-8">
                            <div class="text-left sm:text-right">
                                <div class="text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-0.5">Total Amount</div>
                                <div class="text-base font-bold text-ink-900">₱{{ number_format($dr->total_amount, 2) }}</div>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-0.5">Balance</div>
                                <div class="text-base font-black {{ ($dr->balance ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' }}">₱{{ number_format($dr->balance ?? 0, 2) }}</div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-ink-50 flex items-center justify-center text-ink-400 hover:bg-brand-50 hover:text-brand-600 transition-colors shrink-0">
                                <i class="fas fa-chevron-down text-sm transition-transform duration-300 transform" id="icon-dr-{{ $dr->id }}"></i>
                            </div>
                        </div>
                    </button>

                    {{-- Expandable Content --}}
                    <div id="dr-{{ $dr->id }}" class="hidden border-t border-ink-200 bg-ink-50/50">
                        <div class="p-4 sm:p-6">
                            <h4 class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="fas fa-box-open text-brand-500"></i> Items Included
                            </h4>
                            <div class="bg-white rounded-xl border border-ink-200 shadow-sm w-full overflow-hidden">
                                <div class="inner-table-wrapper overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;"><table class="w-full text-sm min-w-[500px]">
                                    <thead class="bg-ink-50 border-b border-ink-200">
                                        <tr>
                                            <th class="text-left px-4 py-3 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Product</th>
                                            <th class="text-center px-4 py-3 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Qty</th>
                                            <th class="text-right px-4 py-3 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Price</th>
                                            <th class="text-right px-4 py-3 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-ink-100">
                                        @foreach($dr->items as $item)
                                            <tr class="hover:bg-ink-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <div class="font-bold text-ink-800">{{ $item->variant->product->name ?? 'Unknown' }}</div>
                                                    <div class="text-[10px] text-ink-500 font-medium">{{ $item->variant->name ?? '' }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-center font-bold text-ink-700 bg-ink-50 rounded">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="px-4 py-3 text-right text-ink-600">₱{{ number_format($item->price, 2) }}</td>
                                                <td class="px-4 py-3 text-right font-bold text-ink-900">₱{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card p-16 text-center border-dashed border-2 border-ink-200 bg-ink-50/50">
                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-ink-100">
                        <i class="fas fa-box-open text-3xl text-ink-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-ink-900">Wala pay Orders</h3>
                    <p class="text-sm text-ink-500 mt-2 mb-6">Wala pay mga deliveries nga na-record para niining customer.</p>
                    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-secondary mx-auto">
                        <i class="fas fa-plus"></i> Create First Delivery
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAccordion(id) {
        const content = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        
        if (content.classList.contains('hidden')) {
            // Close all others first
            document.querySelectorAll('[id^="dr-"]').forEach(el => {
                if(el.id !== id && !el.classList.contains('hidden')) {
                    el.classList.add('hidden');
                    document.getElementById('icon-' + el.id).classList.remove('rotate-180');
                }
            });
            
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>
@endpush