@extends('layouts.admin')
@section('title', 'Customer Profile')
@section('subtitle', $store->store_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- 1. CLEAN HERO PROFILE CARD --}}
    <div class="card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        {{-- Subtle background decoration --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

        <div class="flex items-center gap-5 relative z-10">
            <div class="w-20 h-20 rounded-2xl bg-white p-1 shadow-sm shrink-0 border border-ink-200">
                <div class="w-full h-full rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-3xl">
                    {{ strtoupper(substr($store->store_name, 0, 1)) }}
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-2xl font-black text-ink-900 leading-none">{{ $store->store_name }}</h2>
                    @if($store->customer_type === 'suki')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-200 uppercase tracking-wider">Suki VIP</span>
                    @elseif($store->customer_type === 'friend')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-200 uppercase tracking-wider">Friend</span>
                    @else
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-ink-100 text-ink-600 border border-ink-200 uppercase tracking-wider">Regular</span>
                    @endif
                </div>
                <div class="text-xs text-ink-500 font-medium flex items-center gap-3">
                    <span><i class="fas fa-map-marker-alt text-ink-400 mr-1"></i> {{ $store->address ?? 'No address' }}</span>
                    <span class="text-ink-300">|</span>
                    <span><i class="fas fa-phone text-ink-400 mr-1"></i> {{ $store->contact_number ?? 'No contact' }}</span>
                </div>
            </div>
        </div>

        <div class="shrink-0 w-full md:w-auto relative z-10 flex flex-col sm:flex-row gap-2">
            {{-- RECORD PAYMENT BUTTON --}}
            <a href="{{ route('payments.index', ['store_id' => $store->id]) }}" class="btn btn-secondary w-full sm:w-auto shadow-sm !text-emerald-700 !border-emerald-200 hover:!bg-emerald-50">
                <i class="fas fa-hand-holding-usd"></i> Record Payment
            </a>
            
            {{-- NEW DELIVERY BUTTON --}}
            <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-primary w-full sm:w-auto shadow-sm">
                <i class="fas fa-plus-circle"></i> New Delivery
            </a>
        </div>
    </div>

    {{-- 2. FINANCIAL SUMMARY (Fixed Variables) --}}
    @php
        // Gigamit nato ang variables gikan sa controller ($totalAmount, $totalPaid)
        $displayTotal = $totalAmount ?? $stats['total_sales'] ?? 0;
        $displayPaid = $totalPaid ?? $stats['total_paid'] ?? 0;
        $displayBalance = max(0, $displayTotal - $displayPaid);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Billed --}}
        <div class="card p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Billed</div>
                <div class="text-2xl font-black text-ink-900">₱{{ number_format($displayTotal, 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center text-ink-500 border border-ink-100">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>

        {{-- Total Paid --}}
        <div class="card p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Total Paid</div>
                <div class="text-2xl font-black text-emerald-700">₱{{ number_format($displayPaid, 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 border border-emerald-100">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        {{-- Outstanding Balance --}}
        <div class="card p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-1">Balance Due</div>
                <div class="text-2xl font-black text-red-600">₱{{ number_format($displayBalance, 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 border border-red-100">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    {{-- 3. ORDER HISTORY ACCORDION --}}
    <div>
        <div class="flex items-center justify-between mb-3 mt-6">
            <div>
                <h3 class="text-sm font-bold text-ink-900">Order History</h3>
                <p class="text-[11px] text-ink-500">Click an order to expand and view items.</p>
            </div>
            <span class="text-xs font-bold text-ink-500 bg-ink-100 px-2.5 py-1 rounded-md">{{ $deliveries->total() }} Orders</span>
        </div>

        <div class="space-y-2.5">
            @forelse($deliveries as $dr)
                @php
                    $paidAmount = $dr->payments_sum_amount ?? 0;
                    $balance = max(0, $dr->total_amount - $paidAmount);
                @endphp
                
                {{-- Accordion Item --}}
                <div class="card overflow-hidden transition-all duration-200 hover:border-brand-300">
                    
                    {{-- Clickable Header --}}
                    <button type="button" class="w-full px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 focus:outline-none hover:bg-ink-50/50 transition-colors" onclick="toggleAccordion('dr-{{ $dr->id }}')">
                        
                        {{-- Left: ID & Date & Status --}}
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-ink-50 text-ink-500 flex items-center justify-center shrink-0 border border-ink-200">
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
                                <div class="text-[10px] text-ink-500 font-medium mt-0.5 flex items-center gap-1">
                                    <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}
                                </div>
                            </div>
                        </div>

                        {{-- Right: Amounts & Toggle Icon --}}
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
                    <div id="dr-{{ $dr->id }}" class="hidden border-t border-ink-200 bg-ink-50/30">
                        <div class="p-4">
                            <h4 class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                <i class="fas fa-box-open text-brand-500"></i> Items Included
                            </h4>
                            <div class="bg-white rounded-lg border border-ink-200 overflow-hidden shadow-sm">
                                <table class="w-full text-xs">
                                    <thead class="bg-ink-50 border-b border-ink-200">
                                        <tr>
                                            <th class="text-left px-4 py-2 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Product</th>
                                            <th class="text-center px-4 py-2 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Qty</th>
                                            <th class="text-right px-4 py-2 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Price</th>
                                            <th class="text-right px-4 py-2 text-[9px] font-bold text-ink-500 uppercase tracking-widest">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-ink-100">
                                        @foreach($dr->items as $item)
                                            <tr class="hover:bg-ink-50 transition-colors">
                                                <td class="px-4 py-2">
                                                    <div class="font-bold text-ink-800">{{ $item->variant->product->name ?? 'Unknown' }}</div>
                                                    <div class="text-[9px] text-ink-500 font-medium">{{ $item->variant->name ?? '' }}</div>
                                                </td>
                                                <td class="px-4 py-2 text-center font-bold text-ink-700 bg-ink-50/50 rounded">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="px-4 py-2 text-right text-ink-600">₱{{ number_format($item->unit_price, 2) }}</td>
                                                <td class="px-4 py-2 text-right font-bold text-ink-900">₱{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card p-12 text-center border-dashed border-2 border-ink-200 bg-ink-50/50">
                    <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-ink-100">
                        <i class="fas fa-box-open text-2xl text-ink-300"></i>
                    </div>
                    <h3 class="text-base font-bold text-ink-900">Wala pay Orders</h3>
                    <p class="text-xs text-ink-500 mt-1 mb-4">Wala pay mga deliveries nga na-record para niining customer.</p>
                    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-secondary mx-auto">
                        <i class="fas fa-plus"></i> Create First Delivery
                    </a>
                </div>
            @endforelse
        </div>
        
        {{-- PAGINATION --}}
        @if(method_exists($deliveries, 'links'))
            <div class="mt-4">
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