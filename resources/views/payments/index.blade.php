@extends('layouts.admin')
@section('title', 'Payments')
@section('subtitle', 'Record collections by Order / DR Number')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Collected</div><div class="text-3xl font-black text-emerald-600">&#8369;{{ number_format($totalCollected, 0) }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fas fa-hand-holding-usd"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Outstanding</div><div class="text-3xl font-black text-red-600">&#8369;{{ number_format($totalOutstanding, 0) }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-lg"><i class="fas fa-exclamation-circle"></i></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200 flex items-center justify-between">
        <div><div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Transactions</div><div class="text-3xl font-black text-espresso-900">{{ $payments->total() }}</div></div>
        <div class="w-10 h-10 rounded-lg bg-espresso-50 text-espresso-600 flex items-center justify-center text-lg"><i class="fas fa-receipt"></i></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- FORM --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-espresso-200 p-6 sticky top-24">
            <h3 class="font-bold text-espresso-900 mb-4 border-b border-espresso-200 pb-3">Record Payment</h3>
            <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Store Filter --}}
                <div>
                    <label class="block text-[11px] font-bold text-espresso-600 mb-1 uppercase tracking-wider">Filter by Store</label>
                    <select name="store_id" id="storeFilter" required class="w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all">
                        <option value="">Choose store...</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $selectedStoreId == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Order (DR) Dropdown --}}
                <div>
                    <label class="block text-[11px] font-bold text-espresso-600 mb-1 uppercase tracking-wider">Select Order (DR Number)</label>
                    <select name="delivery_id" id="deliverySelect" required class="w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" {{ $unpaidDeliveries->isEmpty() ? 'disabled' : '' }}>
                        <option value="">Select order...</option>
                        @foreach($unpaidDeliveries as $d)
                            @php
                                $paid = $d->payments()->sum('amount');
                                $balance = $d->total_amount - $paid;
                            @endphp
                            <option value="{{ $d->id }}" data-balance="{{ $balance }}" {{ $selectedDeliveryId == $d->id ? 'selected' : '' }}>
                                {{ $d->dr_number }} - {{ $d->store->store_name }} (Bal: &#8369;{{ number_format($balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-espresso-600 mb-1 uppercase tracking-wider">Amount (&#8369;)</label>
                    <input type="number" name="amount" id="amountInput" step="0.01" min="1" required class="w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" placeholder="0.00">
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-espresso-600 mb-1 uppercase tracking-wider">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all">
                </div>
                
                <button type="submit" class="w-full py-2.5 bg-espresso-900 hover:bg-espresso-800 text-white font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Save Payment
                </button>
            </form>
        </div>
    </div>

    {{-- HISTORY --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-espresso-200 overflow-hidden h-full">
            <div class="px-6 py-4 border-b border-espresso-200 bg-espresso-50/30">
                <h3 class="font-bold text-espresso-900">Payment History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b border-espresso-200 text-espresso-500 text-[11px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Order #</th>
                            <th class="px-6 py-3">Store</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-espresso-100">
                        @forelse($payments as $p)
                            <tr class="hover:bg-espresso-50/50 transition-colors odd:bg-white even:bg-espresso-50/20">
                                <td class="px-6 py-3 text-espresso-600">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-3 font-mono text-xs font-bold text-espresso-900">{{ $p->delivery->dr_number ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-espresso-700">{{ $p->store->store_name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3 text-right font-bold text-emerald-600">+ &#8369;{{ number_format($p->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-8 text-espresso-500">No payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-espresso-200 bg-white">{{ $payments->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto fill amount pag pinili ang delivery
    document.getElementById('deliverySelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const balance = selectedOption.getAttribute('data-balance');
        if(balance) {
            document.getElementById('amountInput').value = balance;
        }
    });
</script>
@endpush