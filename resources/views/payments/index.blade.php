@extends('layouts.admin')
@section('title', 'Payments')
@section('subtitle', 'Record collections by Order / DR Number')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Collected</div><div class="text-3xl font-black text-emerald-600">&#8369;{{ number_format($totalCollected, 0) }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fas fa-hand-holding-usd"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Outstanding</div><div class="text-3xl font-black text-red-600">&#8369;{{ number_format($totalOutstanding, 0) }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-lg"><i class="fas fa-exclamation-circle"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Transactions</div><div class="text-3xl font-black text-slate-900">{{ $payments->total() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center text-lg"><i class="fas fa-receipt"></i></div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    
    {{-- FORM (Gikuha ang 5 ka columns) --}}
    <div class="xl:col-span-5">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sticky top-24">
            <h3 class="font-bold text-slate-900 mb-4 border-b border-slate-200 pb-3 flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-caramel-500"></i> Record Payment
            </h3>
            <form action="{{ route('payments.store') }}" method="POST" class="space-y-4" id="paymentForm">
                @csrf
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Filter by Store</label>
                        <select name="store_id" id="storeFilter" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all">
                            <option value="">Store...</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ $selectedStoreId == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Payment Date</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Select Order (DR Number)</label>
                    <select name="delivery_id" id="deliverySelect" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" {{ $unpaidDeliveries->isEmpty() ? 'disabled' : '' }}>
                        <option value="">Select order...</option>
                        @foreach($unpaidDeliveries as $d)
                            <option value="{{ $d->id }}" {{ $selectedDeliveryId == $d->id ? 'selected' : '' }}>
                                {{ $d->dr_number }} - {{ $d->store->store_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ORDER ITEMS & PAYMENT INPUTS --}}
                <div id="orderDetailsContainer" class="hidden border border-slate-200 rounded-lg overflow-hidden mt-3">
                    <div class="bg-slate-50 px-3 py-2 border-b border-slate-200">
                        <h4 class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Items to Pay</h4>
                    </div>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto">
                        <table class="w-full text-xs text-left table-fixed">
                            <thead class="bg-white border-b border-slate-200 text-slate-500 font-bold">
                                <tr>
                                    <th class="px-3 py-2 w-auto">Item</th>
                                    <th class="px-3 py-2 text-right w-24">Bal (&#8369;)</th>
                                    <th class="px-3 py-2 text-right w-32">Pay (&#8369;)</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsTableBody">
                                <!-- JS will populate -->
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-slate-50 px-3 py-2 border-t border-slate-200 flex justify-between items-center font-bold text-slate-900 text-xs">
                        <span>Total Payment:</span>
                        <span id="orderTotalDisplay" class="text-emerald-600 text-sm">&#8369;0.00</span>
                    </div>
                </div>
                
                <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-2 mt-4">
                    <i class="fas fa-save"></i> Save Payment
                </button>
            </form>
        </div>
    </div>

    {{-- HISTORY (Gikuha ang 7 ka columns) --}}
    <div class="xl:col-span-7">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden h-full">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-900">Payment History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 w-32">Date</th>
                            <th class="px-6 py-3">Order & Items Paid</th>
                            <th class="px-6 py-3 text-right w-32">Amount Paid</th>
                        </tr>
                    </thead>
                    @include('payments._table')
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 bg-white">{{ $payments->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const deliveriesData = @json($deliveriesData);
    
    document.addEventListener('DOMContentLoaded', function() {
        const deliverySelect = document.getElementById('deliverySelect');
        const detailsContainer = document.getElementById('orderDetailsContainer');
        const tbody = document.getElementById('orderItemsTableBody');
        const totalDisplay = document.getElementById('orderTotalDisplay');

        function updateOrderDetails() {
            const selectedId = deliverySelect.value;
            tbody.innerHTML = '';
            totalDisplay.innerText = '\u20B10.00'; 
            
            if (!selectedId || !deliveriesData[selectedId]) {
                detailsContainer.classList.add('hidden');
                return;
            }

            const order = deliveriesData[selectedId];
            let hasBalance = false;

            order.items.forEach(item => {
                if (item.balance > 0) {
                    hasBalance = true;
                    const tr = document.createElement('tr');
                    tr.className = 'border-b border-slate-100 last:border-0 hover:bg-slate-50';
                    
                    const formattedBal = item.balance.toLocaleString('en-US', {minimumFractionDigits: 2});
                    
                    // GIKAN SA literal nga Peso sign, GI-USAB NATO SA \u20B1
                    tr.innerHTML = `
                        <td class="px-3 py-2 truncate" title="${item.name} ${item.variant}">
                            <div class="font-bold text-slate-800 truncate">${item.name}</div>
                            <div class="text-[10px] text-slate-500 truncate">${item.variant}</div>
                        </td>
                        <td class="px-3 py-2 text-right font-bold text-red-600 whitespace-nowrap">\u20B1${formattedBal}</td>
                        <td class="px-3 py-2">
                            <input type="number" name="items[${item.id}]" value="${item.balance.toFixed(2)}" max="${item.balance.toFixed(2)}" step="0.01" min="0" class="item-pay-input w-full px-2 py-1.5 border border-slate-300 rounded text-right focus:border-caramel-500 outline-none font-bold text-slate-900 text-xs">
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
            });

            if (!hasBalance) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center py-6 text-emerald-600 font-bold text-xs">This order is fully paid.</td></tr>`;
            }

            detailsContainer.classList.remove('hidden');
            calculateTotalPayment();
        }

        function calculateTotalPayment() {
            let total = 0;
            document.querySelectorAll('.item-pay-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            totalDisplay.innerText = '\u20B1' + total.toLocaleString('en-US', {minimumFractionDigits: 2}); 
        }

        if(deliverySelect) {
            deliverySelect.addEventListener('change', updateOrderDetails);
            if(deliverySelect.value) {
                updateOrderDetails();
            }
        }

        tbody.addEventListener('input', function(e) {
            if (e.target.classList.contains('item-pay-input')) {
                calculateTotalPayment();
            }
        });
    });
</script>
@endpush