@extends('layouts.admin')
@section('title', 'Customer Profile')
@section('subtitle', $store->store_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- 1. CUSTOMER INFORMATION CARD (Clean & Minimalist) --}}
    <div class="card p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        
        {{-- Subtle accent line sa taas --}}
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-400 to-brand-600"></div>

        <div class="flex items-center gap-5 md:gap-6 relative z-10 w-full">
            {{-- Avatar --}}
            <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-ink-50 text-ink-600 flex items-center justify-center font-black text-2xl md:text-3xl border border-ink-200 shrink-0 shadow-sm">
                {{ strtoupper(substr($store->store_name, 0, 1)) }}
            </div>
            
            {{-- Info --}}
            <div class="min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-2xl md:text-3xl font-black text-ink-900 tracking-tight truncate">{{ $store->store_name }}</h2>
                    @if($store->customer_type === 'suki')
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">⭐ Suki VIP</span>
                    @elseif($store->customer_type === 'friend')
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">👥 Friend</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-ink-100 text-ink-600 border border-ink-200 uppercase tracking-wider">Regular</span>
                    @endif
                </div>
                <div class="text-sm text-ink-500 font-medium flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-ink-400"></i> {{ $store->address ?? 'No address' }}</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-phone text-ink-400"></i> {{ $store->contact_number ?? 'No contact' }}</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-tag text-ink-400"></i> {{ number_format($store->discount, 2) }}% Discount</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="shrink-0 flex items-center gap-2 w-full md:w-auto mt-4 md:mt-0">
            <a href="{{ route('stores.index') }}" class="btn btn-secondary !py-2.5 !px-4 flex-1 md:flex-none">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
            <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-primary !py-2.5 !px-4 flex-1 md:flex-none shadow-sm">
                <i class="fas fa-plus text-xs"></i> Create Delivery
            </a>
        </div>
    </div>

    {{-- 2. FINANCIAL SUMMARY (Unified Card) --}}
    @php
        $displayTotal = $totalAmount ?? $stats['total_sales'] ?? 0;
        $displayPaid = $totalPaid ?? $stats['total_paid'] ?? 0;
        $displayBalance = max(0, $displayTotal - $displayPaid);
    @endphp

    <div class="card flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-ink-200 overflow-hidden shadow-sm">
        {{-- Total Billed --}}
        <div class="flex-1 p-6 flex flex-col justify-center">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><i class="fas fa-file-invoice-dollar text-ink-400"></i> Total Billed</div>
            <div class="text-3xl font-black text-ink-900 tracking-tight">₱{{ number_format($displayTotal, 2) }}</div>
        </div>
        
        {{-- Total Paid --}}
        <div class="flex-1 p-6 flex flex-col justify-center">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><i class="fas fa-check-circle text-emerald-500"></i> Total Paid</div>
            <div class="text-3xl font-black text-emerald-600 tracking-tight">₱{{ number_format($displayPaid, 2) }}</div>
        </div>

        {{-- Balance --}}
        <div class="flex-1 p-6 flex flex-col justify-center bg-red-50/30">
            <div class="text-[11px] font-bold text-red-600 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><i class="fas fa-hourglass-half text-red-500"></i> Balance Due</div>
            <div class="text-3xl font-black {{ $displayBalance > 0 ? 'text-red-600' : 'text-ink-400' }} tracking-tight">₱{{ number_format($displayBalance, 2) }}</div>
        </div>
    </div>

    {{-- 3. ORDER HISTORY ACCORDION --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-ink-900">Order History</h3>
                <p class="text-xs text-ink-500 mt-0.5">Click an order to expand and view items.</p>
            </div>
            <span class="text-[11px] font-bold text-ink-500 bg-white px-3 py-1 rounded-md border border-ink-200 shadow-sm">{{ $deliveries->total() }} Orders</span>
        </div>

        <div class="divide-y divide-ink-100 bg-white">
            @forelse($deliveries as $dr)
                @php
                    $paidAmount = $dr->payments_sum_amount ?? 0;
                    $balance = max(0, $dr->total_amount - $paidAmount);
                @endphp
                
                {{-- Accordion Item --}}
                <div class="transition-all duration-200">
                    <button type="button" class="w-full px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 focus:outline-none hover:bg-ink-50/50 transition-colors" onclick="toggleAccordion('dr-{{ $dr->id }}')">
                        
                        {{-- Left: ID & Date & Status --}}
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-ink-100 text-ink-500 flex items-center justify-center shrink-0 border border-ink-200">
                                <i class="fas fa-shopping-bag text-sm"></i>
                            </div>
                            <div class="text-left">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-ink-900 text-sm">{{ $dr->dr_number }}</span>
                                    @if($dr->status === 'delivered' || $dr->status === 'completed')
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-wider">Paid</span>
                                    @elseif($dr->status === 'partial')
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-600 border border-amber-200 uppercase tracking-wider">Partial</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-600 border border-red-200 uppercase tracking-wider">Unpaid</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-ink-500 font-medium mt-0.5 flex items-center gap-1">
                                    <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}
                                </div>
                            </div>
                        </div>

                        {{-- Right: Amounts & Toggle --}}
                        <div class="flex items-center gap-6">
                            <div class="text-left sm:text-right">
                                <div class="text-[9px] font-bold text-ink-400 uppercase tracking-widest mb-0.5">Total</div>
                                <div class="text-sm font-bold text-ink-900">₱{{ number_format($dr->total_amount, 2) }}</div>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-[9px] font-bold text-ink-400 uppercase tracking-widest mb-0.5">Balance</div>
                                <div class="text-sm font-black {{ $balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">₱{{ number_format($balance, 2) }}</div>
                            </div>
                            <div class="w-7 h-7 rounded-full bg-ink-100 flex items-center justify-center text-ink-500 group-hover:bg-brand-100 group-hover:text-brand-600 transition-colors shrink-0">
                                <i class="fas fa-chevron-down text-[10px] transition-transform duration-300 transform" id="icon-dr-{{ $dr->id }}"></i>
                            </div>
                        </div>
                    </button>

                    {{-- Expandable Content --}}
                    <div id="dr-{{ $dr->id }}" class="hidden border-t border-ink-200 bg-[#FDFCFB]">
                        <div class="p-4 md:p-6">
                            <h4 class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                <i class="fas fa-box-open text-brand-500"></i> Items Included
                            </h4>
                            <div class="bg-white rounded-lg border border-ink-200 shadow-sm w-full">
                                <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                                    <table class="w-full text-xs min-w-[500px]">
                                        <thead class="bg-ink-50 border-b border-ink-200">
                                            <tr>
                                                <th class="text-left px-4 py-2.5 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Product</th>
                                                <th class="text-center px-4 py-2.5 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Qty</th>
                                                <th class="text-right px-4 py-2.5 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Price</th>
                                                <th class="text-right px-4 py-2.5 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-ink-100">
                                            @foreach($dr->items as $item)
                                                <tr class="hover:bg-ink-50 transition-colors">
                                                    <td class="px-4 py-2.5">
                                                        <div class="font-bold text-ink-800">{{ $item->variant->product->name ?? 'Unknown' }}</div>
                                                        <div class="text-[9px] text-ink-500 font-medium">{{ $item->variant->name ?? '' }}</div>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-center font-bold text-ink-700">{{ number_format($item->quantity, 2) }}</td>
                                                    <td class="px-4 py-2.5 text-right text-ink-600">₱{{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="px-4 py-2.5 text-right font-bold text-ink-900">₱{{ number_format($item->subtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="p-16 text-center">
                    <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100">
                        <i class="fas fa-box-open text-2xl text-ink-300"></i>
                    </div>
                    <h3 class="text-base font-bold text-ink-900">Wala pay Orders</h3>
                    <p class="text-xs text-ink-500 mt-1 mb-4">Wala pay mga deliveries nga na-record para niining customer.</p>
                    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-secondary mx-auto !text-xs !py-2">
                        <i class="fas fa-plus"></i> Create First Delivery
                    </a>
                </div>
            @endforelse
        </div>
        
        {{-- PAGINATION --}}
        @if(method_exists($deliveries, 'links') && $deliveries->hasPages())
            <div class="px-6 py-4 border-t border-ink-200 bg-ink-50">
                {{ $deliveries->links() }}
            </div>
        @endif
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
                    const otherIcon = document.getElementById('icon-' + el.id);
                    if (otherIcon) otherIcon.classList.remove('rotate-180');
                }
            });
            
            content.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    }
</script>
@endpush