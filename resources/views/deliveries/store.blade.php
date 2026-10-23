@extends('layouts.admin')
@section('title', 'Customer Profile')
@section('subtitle', $store->store_name)

@section('content')
@php
    $balance = $totalAmount - $totalPaid;
@endphp

{{-- TOP SUMMARY CARD --}}
<div class="bg-white rounded-xl shadow-sm p-6 border border-espresso-200 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
    <div class="flex items-center gap-5 relative z-10">
        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-espresso-800 to-espresso-900 text-white flex items-center justify-center font-bold text-3xl shadow-md border border-espresso-700">
            {{ strtoupper(substr($store->store_name, 0, 1)) }}
        </div>
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h2 class="text-2xl font-bold text-espresso-900">{{ $store->store_name }}</h2>
                @if($store->customer_type == 'suki')
                    <span class="px-2.5 py-1 bg-caramel-50 text-caramel-700 rounded-md text-[10px] font-bold border border-caramel-200"><i class="fas fa-crown mr-1"></i> Suki VIP</span>
                @endif
            </div>
            <p class="text-xs text-espresso-500 flex items-center gap-1.5"><i class="fas fa-map-marker-alt"></i> {{ $store->address ?? 'No address' }}</p>
        </div>
    </div>

    <div class="flex items-center gap-4 relative z-10">
        <div class="text-right bg-espresso-50 p-3 rounded-lg border border-espresso-100">
            <div class="text-[10px] font-bold text-espresso-500 uppercase tracking-wider mb-0.5">Total Billed</div>
            <div class="text-lg font-black text-espresso-900">&#8369;{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="text-right bg-emerald-50 p-3 rounded-lg border border-emerald-100">
            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-0.5">Total Paid</div>
            <div class="text-lg font-black text-emerald-700">&#8369;{{ number_format($totalPaid, 2) }}</div>
        </div>
        <div class="text-right bg-{{ $balance > 0 ? 'red' : 'espresso' }}-50 p-3 rounded-lg border border-{{ $balance > 0 ? 'red' : 'espresso' }}-100 min-w-[120px]">
            <div class="text-[10px] font-bold text-{{ $balance > 0 ? 'red' : 'espresso' }}-600 uppercase tracking-wider mb-0.5">Balance</div>
            <div class="text-lg font-black text-{{ $balance > 0 ? 'red' : 'espresso' }}-700">&#8369;{{ number_format($balance, 2) }}</div>
        </div>
        <a href="{{ route('deliveries.create', ['store_id' => $store->id]) }}" class="px-4 py-3 bg-espresso-900 hover:bg-espresso-800 text-white rounded-lg font-bold shadow-md transition-all flex items-center gap-2 text-sm active:scale-95">
            <i class="fas fa-plus"></i> Delivery
        </a>
    </div>
</div>

{{-- DELIVERY HISTORY ACCORDION --}}
<div class="space-y-4">
    <div class="flex justify-between items-end mb-2 px-2">
        <div>
            <h3 class="font-bold text-espresso-900 text-lg">Order History</h3>
            <p class="text-xs text-espresso-500">Click an order to view items and record payments</p>
        </div>
    </div>

    @forelse($deliveries as $delivery)
        @php
            $paid = $delivery->payments_sum_amount ?? 0;
            $bal = $delivery->total_amount - $paid;
        @endphp
        
        <div class="bg-white rounded-xl shadow-sm border border-espresso-200 overflow-hidden transition-all duration-300">
            {{-- ACCORDION HEADER (Clickable) --}}
            <div onclick="toggleOrder({{ $delivery->id }})" class="px-6 py-5 cursor-pointer hover:bg-espresso-50/50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4 select-none">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-espresso-100 flex items-center justify-center text-espresso-600">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-espresso-900 text-lg font-mono">{{ $delivery->dr_number }}</span>
                            @if($delivery->status === 'pending')
                                <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded text-[10px] font-bold border border-red-200 uppercase tracking-wide">Unpaid</span>
                            @elseif($delivery->status === 'partial')
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded text-[10px] font-bold border border-amber-200 uppercase tracking-wide">Partial</span>
                            @else
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[10px] font-bold border border-emerald-200 uppercase tracking-wide">Paid</span>
                            @endif
                        </div>
                        <div class="text-xs text-espresso-500 mt-1"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F d, Y') }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-8">
                    <div class="text-right">
                        <div class="text-[10px] font-bold text-espresso-400 uppercase tracking-wider">Total Amount</div>
                        <div class="font-bold text-espresso-900">&#8369;{{ number_format($delivery->total_amount, 2) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] font-bold text-espresso-400 uppercase tracking-wider">Balance</div>
                        <div class="font-bold {{ $bal > 0 ? 'text-red-600' : 'text-emerald-600' }}">&#8369;{{ number_format($bal, 2) }}</div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-espresso-50 flex items-center justify-center text-espresso-400 transition-transform duration-300 transform" id="icon-{{ $delivery->id }}">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            {{-- ACCORDION BODY (Hidden by default) --}}
            <div id="order-details-{{ $delivery->id }}" class="hidden border-t border-espresso-200 bg-espresso-50/30">
                
                {{-- ITEMS BREAKDOWN --}}
                <div class="p-6">
                    <h4 class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-3">Items Ordered</h4>
                    <div class="bg-white rounded-lg border border-espresso-200 overflow-hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-espresso-50 text-espresso-500 text-[10px] uppercase tracking-wider font-bold">
                                <tr>
                                    <th class="px-4 py-2">Product</th>
                                    <th class="px-4 py-2">Variant</th>
                                    <th class="px-4 py-2 text-center">Qty</th>
                                    <th class="px-4 py-2 text-right">Price</th>
                                    <th class="px-4 py-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-espresso-100">
                                @foreach($delivery->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-espresso-900">{{ $item->variant->product->name ?? 'Product' }}</td>
                                        <td class="px-4 py-3 text-espresso-600">{{ $item->variant->name ?? 'Variant' }}</td>
                                        <td class="px-4 py-3 text-center font-bold">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-espresso-600">&#8369;{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-espresso-900">&#8369;{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAYMENTS & ACTION --}}
                <div class="px-6 pb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        {{-- Payments List --}}
                        <div class="flex-1">
                            <h4 class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-3">Payments Recorded</h4>
                            @if($delivery->payments->count() > 0)
                                <div class="space-y-2">
                                    @foreach($delivery->payments as $p)
                                        <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-espresso-100 text-sm">
                                            <div>
                                                <span class="font-bold text-emerald-600">&#8369;{{ number_format($p->amount, 2) }}</span>
                                                <span class="text-espresso-500 text-xs ml-2">- {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</span>
                                            </div>
                                            <span class="text-[10px] font-mono text-espresso-400">{{ $p->reference_number ?? 'No Ref' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-xs text-espresso-400 italic">No payments yet for this order.</div>
                            @endif
                        </div>

                        {{-- Pay Action --}}
                        @if($bal > 0)
                            <div class="w-full md:w-64 flex flex-col justify-end">
                                <a href="{{ route('payments.index', ['store_id' => $store->id, 'delivery_id' => $delivery->id]) }}" class="w-full py-3 bg-espresso-900 hover:bg-espresso-800 text-white text-center rounded-lg font-bold shadow-md transition-all text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-money-bill-wave"></i> Record Payment
                                </a>
                            </div>
                        @else
                            <div class="w-full md:w-64 flex flex-col justify-end">
                                <div class="w-full py-3 bg-emerald-50 text-emerald-600 text-center rounded-lg font-bold border border-emerald-200 text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i> Fully Paid
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-espresso-200 text-center py-20 shadow-sm">
            <i class="fas fa-box-open text-4xl text-espresso-200 mb-3"></i>
            <h3 class="text-lg font-bold text-espresso-800">No orders yet</h3>
        </div>
    @endforelse

    <div class="mt-4">{{ $deliveries->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    function toggleOrder(id) {
        const details = document.getElementById('order-details-' + id);
        const icon = document.getElementById('icon-' + id);
        
        if (details.classList.contains('hidden')) {
            details.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            details.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>
@endpush