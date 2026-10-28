@extends('layouts.admin')
@section('title', 'Payments')
@section('subtitle', 'Record collections by Order / DR Number')

@section('content')

<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
        <div class="card p-4 md:p-5 border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Collected</div>
                <div class="text-xl md:text-2xl font-black text-emerald-600" id="kpi-collected">₱{{ number_format($totalCollected, 0) }}</div>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm md:text-lg border border-emerald-100 shrink-0">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>

        <div class="card p-4 md:p-5 border-l-4 border-l-red-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Outstanding</div>
                <div class="text-xl md:text-2xl font-black text-red-600" id="kpi-outstanding">₱{{ number_format($totalOutstanding, 0) }}</div>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-sm md:text-lg border border-red-100 shrink-0">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>

        <div class="card p-4 md:p-5 border-l-4 border-l-brand-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Transactions</div>
                <div class="text-xl md:text-2xl font-black text-ink-900" id="kpi-transactions">{{ $payments->total() }}</div>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-lg bg-ink-50 text-ink-600 flex items-center justify-center text-sm md:text-lg border border-ink-200 shrink-0">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 md:gap-6">
        
        {{-- FORM SECTION --}}
        <div class="xl:col-span-5">
            <div class="card p-4 md:p-6 xl:sticky xl:top-24">
                <h3 class="font-bold text-ink-900 mb-3 md:mb-4 border-b border-ink-200 pb-3 flex items-center gap-2 text-sm md:text-base">
                    <i class="fas fa-money-bill-wave text-brand-500"></i> Record Payment
                </h3>
                
                <form action="{{ route('payments.store') }}" method="POST" class="space-y-3 md:space-y-4" id="paymentForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Filter by Store</label>
                            <select id="storeFilter" onchange="window.location.href='{{ route('payments.index') }}?store_id=' + this.value" class="input !py-2 !text-xs md:!text-sm">
                                <option value="">Store...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ $selectedStoreId == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="input !py-2 !text-xs md:!text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Select Order (DR Number)</label>
                        <select name="delivery_id" id="deliverySelect" required class="input !py-2 !text-xs md:!text-sm" onchange="updateOrderDetails()"  {{ $unpaidDeliveries->isEmpty() ? 'disabled' : '' }} onchange="if(window.updateOrderDetails) window.updateOrderDetails();">
                            <option value="">Select order...</option>
                            @foreach($unpaidDeliveries as $d)
                                <option value="{{ $d->id }}" {{ $selectedDeliveryId == $d->id ? 'selected' : '' }}>
                                    {{ $d->dr_number }} - {{ $d->store->store_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ORDER ITEMS & PAYMENT INPUTS --}}
                    <div id="orderDetailsContainer" class="border border-ink-200 rounded-xl overflow-hidden mt-3">
                        <div class="bg-ink-50 px-3 py-2 border-b border-ink-200">
                            <h4 class="text-[10px] font-bold text-ink-600 uppercase tracking-widest">Items to Pay</h4>
                        </div>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto w-full" style="-webkit-overflow-scrolling: touch;">
                            <table class="w-full text-xs text-left min-w-[320px]">
                                <thead class="bg-white border-b border-ink-200 text-ink-500 font-bold sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 w-auto">Item</th>
                                        <th class="px-3 py-2 text-right w-20 md:w-24 whitespace-nowrap">Bal (₱)</th>
                                        <th class="px-3 py-2 text-right w-28 md:w-32 whitespace-nowrap">Pay (₱)</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsTableBody">
                                    <!-- JS will populate -->
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-ink-50 px-3 py-2 border-t border-ink-200 flex justify-between items-center font-bold text-ink-900 text-xs">
                            <span>Total Payment:</span>
                            <span id="orderTotalDisplay" class="text-emerald-600 text-sm">₱0.00</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full !py-2.5 md:!py-3 mt-2 md:mt-4 !text-xs md:!text-sm">
                        <i class="fas fa-save"></i> Save Payment
                    </button>
                </form>
            </div>
        </div>

        {{-- HISTORY SECTION --}}
        <div class="xl:col-span-7">
            <div class="card overflow-hidden h-full flex flex-col">
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-ink-200 bg-ink-50 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3 shrink-0">
    <h3 class="font-bold text-ink-900 text-sm md:text-base whitespace-nowrap">Payment History</h3>
    
    <form action="{{ route('payments.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full xl:w-auto">
        <div class="flex items-center gap-1.5 w-full sm:w-auto">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="input !py-1.5 !px-2 !text-xs w-full sm:w-auto" title="Start Date">
            <span class="text-ink-400 text-xs">to</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input !py-1.5 !px-2 !text-xs w-full sm:w-auto" title="End Date">
        </div>
        <div class="flex items-center gap-1.5 w-full sm:w-auto">
            <button type="submit" class="btn btn-primary !py-1.5 !px-3 !text-xs flex-1 sm:flex-none">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if(request()->anyFilled(['start_date', 'end_date']))
                <a href="{{ route('payments.index') }}" class="btn btn-secondary !py-1.5 !px-3 !text-xs flex-1 sm:flex-none" title="Clear Filter">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>
                <div class="overflow-x-auto w-full flex-1 pb-2" style="-webkit-overflow-scrolling: touch;">
                <table class="table-clean w-full min-w-[600px]">
                    <thead class="bg-ink-50 border-b border-ink-200">
                        <tr>
                            <th class="w-32 text-[11px] uppercase tracking-wider text-ink-500">Date</th>
                            <th class="text-[11px] uppercase tracking-wider text-ink-500">Order & Items Paid</th>
                            <th class="text-right w-32 text-[11px] uppercase tracking-wider text-ink-500">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody id="paymentTableBody" class="divide-y divide-ink-100 bg-white">
                        @include('payments._table')
                    </tbody>
                </table>
            </div>
                <div class="px-4 md:px-6 py-3 md:py-4 border-t border-ink-200 bg-ink-50 shrink-0 text-sm">{{ $payments->appends(request()->query())->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let deliveriesData = @json($deliveriesData ?? []);

    function updateOrderDetails() {
        const deliverySelect = document.getElementById('deliverySelect');
        const tbody = document.getElementById('orderItemsTableBody');
        const totalDisplay = document.getElementById('orderTotalDisplay');
        
        if (!deliverySelect || !tbody || !totalDisplay) return;

        const selectedId = deliverySelect.value;
        tbody.innerHTML = '';
        totalDisplay.innerText = '\u20B10.00';
        
        if (!selectedId) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center py-6 text-ink-500 text-xs">Select an order first.</td></tr>`;
            return;
        }

        if (!deliveriesData[selectedId]) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center py-6 text-red-600 font-bold text-xs">No data found for ID ${selectedId}.</td></tr>`;
            return;
        }

        const order = deliveriesData[selectedId];
        let hasBalance = false;

        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const bal = parseFloat(item.balance);
                if (bal > 0) {
                    hasBalance = true;
                    const tr = document.createElement('tr');
                    tr.className = 'border-b border-ink-100 last:border-0 hover:bg-ink-50';
                    const formattedBal = bal.toLocaleString('en-US', {minimumFractionDigits: 2});
                    
                    tr.innerHTML = `
                        <td class="px-3 py-2 truncate">
                            <div class="font-bold text-ink-800 truncate text-xs">${item.name}</div>
                            <div class="text-[10px] text-ink-500 truncate">${item.variant}</div>
                        </td>
                        <td class="px-3 py-2 text-right font-bold text-red-600 whitespace-nowrap text-xs">\u20B1${formattedBal}</td>
                        <td class="px-3 py-2">
                            <input type="number" inputmode="decimal" name="items[${item.id}]" value="${bal.toFixed(2)}" max="${bal.toFixed(2)}" step="0.01" min="0" class="item-pay-input w-full px-2 py-2 border border-ink-300 rounded text-right font-bold text-ink-900 text-base md:text-xs">
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
            });
        }

        if (!hasBalance) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center py-6 text-emerald-600 font-bold text-xs">This order is fully paid.</td></tr>`;
        }

        calculateTotalPayment();
    }

    function calculateTotalPayment() {
        let total = 0;
        document.querySelectorAll('.item-pay-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        const totalDisplay = document.getElementById('orderTotalDisplay');
        if (totalDisplay) {
            totalDisplay.innerText = '\u20B1' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deliverySelect = document.getElementById('deliverySelect');
        const tbody = document.getElementById('orderItemsTableBody');
        
        if (deliverySelect) {
            deliverySelect.addEventListener('change', updateOrderDetails);
            if (deliverySelect.value) updateOrderDetails();
        }

        if (tbody) {
            tbody.addEventListener('input', function(e) {
                if (e.target.classList.contains('item-pay-input')) calculateTotalPayment();
            });
        }

        // --- AJAX REALTIME SUBMISSION ---
        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Pugngan ang page reload
                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> SAVING...';
                btn.disabled = true;

                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // 1. I-update ang mga KPI boxes
                        document.getElementById('kpi-collected').innerText = data.totalCollected;
                        document.getElementById('kpi-outstanding').innerText = data.totalOutstanding;
                        document.getElementById('kpi-transactions').innerText = data.totalTransactions;

                        // 2. I-update ang Payment History table (walay reload)
                        document.getElementById('paymentTableBody').innerHTML = data.html;

                        // 3. I-reset ang form
                        this.reset();
                        document.getElementById('orderDetailsContainer').classList.add('hidden');
                        document.getElementById('orderItemsTableBody').innerHTML = '';
                        document.getElementById('orderTotalDisplay').innerText = '\u20B10.00';

                        // 4. I-update ang dropdown (tangtangon ang nabayaran na)
                        const select = document.getElementById('deliverySelect');
                        select.innerHTML = '<option value="">Select order...</option>';
                        data.unpaidOptions.forEach(opt => {
                            select.innerHTML += `<option value="${opt.id}">${opt.text}</option>`;
                        });

                        // 5. I-update ang JS variable para sa sunod nga pili
                        deliveriesData = data.deliveriesData;

                        // Success feedback
                        showToast(data.message, 'success');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Error saving payment. Please check console.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        }
    });
</script>
@endpush