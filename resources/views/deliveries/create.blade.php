@extends('layouts.admin')
@section('title', 'Create New Delivery')
@section('subtitle', 'Select products and assign to a store')

@section('content')
<form action="{{ route('deliveries.store') }}" method="POST" id="deliveryForm">
    @csrf
    
    @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-xl flex items-start gap-3 text-sm font-medium border border-red-100 shadow-sm">
            <i class="fas fa-exclamation-triangle mt-0.5"></i> 
            <div>@foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- LEFT SIDE: Items List --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 border border-espresso-100">
                <div class="flex justify-between items-center mb-4 border-b border-espresso-100 pb-3">
                    <h3 class="text-lg font-bold text-espresso-900 font-serif"><i class="fas fa-boxes text-caramel-500 mr-2"></i> Delivery Items</h3>
                    <span class="text-xs text-espresso-500 font-medium">Select variant to see fixed price</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="productsTable">
                        <thead>
                            <tr class="text-left text-espresso-500 text-[11px] uppercase tracking-wider border-b border-espresso-100">
                                <th class="pb-3 font-bold w-2/5">Product Pack</th>
                                <th class="pb-3 font-bold w-1/5">Price (â‚±)</th>
                                <th class="pb-3 font-bold w-1/5">Quantity</th>
                                <th class="pb-3 font-bold text-right w-1/5">Subtotal</th>
                                <th class="pb-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            <tr class="product-row border-b border-espresso-50">
                                <td class="py-3 pr-2">
                                    <select name="products[0][variant_id]" class="variant-select w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:ring-2 focus:ring-caramel-400 outline-none" required>
                                        <option value="">Select Product...</option>
                                        @foreach($variants as $v)
                                            <option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">
                                                {{ $v->product->name ?? 'Unknown' }} - {{ $v->name }} (Stock: {{ $v->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-red-500 mt-1 stock-warning hidden font-bold"><i class="fas fa-exclamation-triangle"></i> Kulang ang stock!</p>
                                </td>
                                <td class="py-3 pr-2">
                                    <input type="number" name="products[0][price]" step="0.01" class="price-input w-full px-3 py-2 bg-espresso-100 border border-espresso-200 rounded-lg text-sm text-espresso-600 outline-none cursor-not-allowed" readonly required placeholder="0.00">
                                </td>
                                <td class="py-3 pr-2">
                                    <input type="number" name="products[0][quantity]" value="1" min="1" class="qty-input w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:ring-2 focus:ring-caramel-400 outline-none" required>
                                </td>
                                <td class="py-3 text-right">
                                    <span class="subtotal-text font-bold text-espresso-900">â‚±0.00</span>
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button" class="remove-row text-red-400 hover:text-red-600 hidden"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" id="addRowBtn" class="mt-4 px-4 py-2 bg-espresso-100 text-espresso-700 font-bold rounded-lg hover:bg-espresso-200 transition-colors text-sm">
                    <i class="fas fa-plus mr-1"></i> Add Another Product
                </button>
            </div>
        </div>

        {{-- RIGHT SIDE: Details & Summary --}}
        <div class="space-y-6">
            {{-- Store Details --}}
            <div class="card p-6 border border-espresso-100">
                <h3 class="text-lg font-bold text-espresso-900 font-serif mb-4 border-b border-espresso-100 pb-3">Delivery Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-espresso-600 mb-1 uppercase tracking-wide">Assign to Store</label>
                        <select name="store_id" class="w-full px-3 py-2.5 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:ring-2 focus:ring-caramel-400 outline-none" required>
                            <option value="">Select Store...</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->store_name }} ({{ ucfirst($store->customer_type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-espresso-600 mb-1 uppercase tracking-wide">Delivery Date</label>
                        <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:ring-2 focus:ring-caramel-400 outline-none" required>
                    </div>
                </div>
            </div>

            {{-- Summary Card (Sticky) --}}
            <div class="card p-6 bg-espresso-800 text-white relative overflow-hidden border-none sticky top-24">
                <div class="absolute -right-4 -top-4 text-espresso-700/50 opacity-50"><i class="fas fa-file-invoice-dollar fa-5x"></i></div>
                <div class="relative z-10">
                    <h4 class="text-xs font-bold text-caramel-300 uppercase tracking-wider mb-2">Total Delivery Amount</h4>
                    <div class="text-4xl font-bold font-serif mb-6" id="grandTotal">â‚±0.00</div>
                    
                    <button type="submit" id="submitBtn" class="w-full bg-caramel-400 hover:bg-caramel-500 text-espresso-900 font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-caramel-400/30 flex items-center justify-center gap-2 text-lg">
                        <i class="fas fa-paper-plane"></i> Save Delivery
                    </button>
                    <p class="text-center text-[10px] text-espresso-400 mt-3"><i class="fas fa-info-circle"></i> Stock will be deducted automatically upon saving.</p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = 1;
        const productsTable = document.getElementById('productRows');
        const addRowBtn = document.getElementById('addRowBtn');
        const submitBtn = document.getElementById('submitBtn');
        const variantOptions = `@foreach($variants as $v)<option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">{{ $v->product->name ?? 'Unknown' }} - {{ $v->name }} (Stock: {{ $v->stock }})</option>@endforeach`;

        function calculateTotals() {
            let grandTotal = 0;
            let hasError = false;

            document.querySelectorAll('.product-row').forEach(row => {
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const qty = parseInt(row.querySelector('.qty-input').value) || 0;
                const variantSelect = row.querySelector('.variant-select');
                const selectedOption = variantSelect.options[variantSelect.selectedIndex];
                const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
                const warning = row.querySelector('.stock-warning');

                if (variantSelect.value && qty > stock) {
                    warning.classList.remove('hidden');
                    row.querySelector('.qty-input').classList.add('border-red-500', 'bg-red-50');
                    hasError = true;
                } else {
                    warning.classList.add('hidden');
                    row.querySelector('.qty-input').classList.remove('border-red-500', 'bg-red-50');
                }

                const subtotal = price * qty;
                row.querySelector('.subtotal-text').innerText = 'â‚±' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                grandTotal += subtotal;
            });

            document.getElementById('grandTotal').innerText = 'â‚±' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            if (hasError) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        addRowBtn.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.className = 'product-row border-b border-espresso-50';
            tr.innerHTML = `
                <td class="py-3 pr-2">
                    <select name="products[${rowCount}][variant_id]" class="variant-select w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:ring-2 focus:ring-caramel-400 outline-none" required>
                        <option value="">Select Product...</option>${variantOptions}
                    </select>
                    <p class="text-[10px] text-red-500 mt-1 stock-warning hidden font-bold"><i class="fas fa-exclamation-triangle"></i> Kulang ang stock!</p>
                </td>
                <td class="py-3 pr-2"><input type="number" name="products[${rowCount}][price]" step="0.01" class="price-input w-full px-3 py-2 bg-espresso-100 border border-espresso-200 rounded-lg text-sm text-espresso-600 outline-none cursor-not-allowed" readonly required placeholder="0.00"></td>
                <td class="py-3 pr-2"><input type="number" name="products[${rowCount}][quantity]" value="1" min="1" class="qty-input w-full px-3 py-2 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:ring-2 focus:ring-caramel-400 outline-none" required></td>
                <td class="py-3 text-right"><span class="subtotal-text font-bold text-espresso-900">â‚±0.00</span></td>
                <td class="py-3 text-center"><button type="button" class="remove-row text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button></td>
            `;
            productsTable.appendChild(tr);
            rowCount++;
        });

        productsTable.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.getAttribute('data-price') || '';
                e.target.closest('.product-row').querySelector('.price-input').value = price;
                calculateTotals();
            }
        });

        productsTable.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input')) { calculateTotals(); }
        });

        productsTable.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (document.querySelectorAll('.product-row').length > 1) {
                    e.target.closest('.product-row').remove();
                    calculateTotals();
                } else { alert("Kinahanglan naay bisan isa ka produkto."); }
            }
        });
    });
</script>
@endpush