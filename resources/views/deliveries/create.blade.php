@extends('layouts.admin')
@section('title', 'New Sales Delivery')
@section('subtitle', 'Create a delivery and record it as a sale')

@section('content')
<form action="{{ route('deliveries.store') }}" method="POST" id="deliveryForm">
    @csrf
    
    @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-xl flex items-start gap-3 text-sm font-medium border border-red-200 shadow-sm">
            <i class="fas fa-exclamation-triangle mt-0.5"></i> 
            <div>@foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- LEFT SIDE: ITEMS / PRODUCTS --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fas fa-shopping-cart text-caramel-500"></i> Items to Deliver</h3>
                <span class="text-xs text-slate-500 font-medium">Select product to see fixed price</span>
            </div>
            
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="productsTable">
                        <thead>
                            <tr class="text-left text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                                <th class="pb-3 font-bold w-2/5">Product & Variant</th>
                                <th class="pb-3 font-bold w-1/5">Price (&#8369;)</th>
                                <th class="pb-3 font-bold w-1/5">Quantity</th>
                                <th class="pb-3 font-bold text-right w-1/5">Subtotal</th>
                                <th class="pb-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            {{-- Default Row --}}
                            <tr class="product-row border-b border-slate-100">
                                <td class="py-3 pr-2">
                                    <select name="products[0][variant_id]" class="variant-select w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" required>
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
                                    <input type="number" name="products[0][price]" step="0.01" class="price-input w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:border-caramel-500 outline-none transition-all font-bold" required placeholder="0.00">
                                </td>
                                <td class="py-3 pr-2">
                                    <input type="number" name="products[0][quantity]" value="1" min="1" class="qty-input w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" required>
                                </td>
                                <td class="py-3 text-right">
                                    <span class="subtotal-text font-bold text-slate-900">&#8369;0.00</span>
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button" class="remove-row text-slate-400 hover:text-red-500 transition-colors hidden"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <button type="button" id="addRowBtn" class="mt-4 px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition-colors text-sm">
                    <i class="fas fa-plus mr-1"></i> Add Another Item
                </button>
            </div>
        </div>

        {{-- RIGHT SIDE: CUSTOMER & SUMMARY --}}
        <div class="space-y-6">
            {{-- Customer Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-200 pb-3">Customer Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Select Customer <span class="text-red-500">*</span></label>
                        <select name="store_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" required>
                            <option value="">Choose Customer...</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->store_name }} ({{ ucfirst($store->customer_type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Delivery Date <span class="text-red-500">*</span></label>
                        <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" required>
                    </div>
                </div>
            </div>

            {{-- Invoice Summary --}}
            <div class="bg-slate-900 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-slate-700 opacity-30"><i class="fas fa-file-invoice-dollar fa-6x"></i></div>
                <div class="relative z-10">
                    <h4 class="text-xs font-bold text-caramel-400 uppercase tracking-wider mb-2">Total Sales Amount</h4>
                    <div class="text-4xl font-black text-white mb-6" id="grandTotal">&#8369;0.00</div>
                    
                    <button type="submit" id="submitBtn" class="w-full bg-caramel-500 hover:bg-caramel-400 text-slate-900 font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> Confirm Delivery & Sale
                    </button>
                    <p class="text-center text-[10px] text-slate-400 mt-3"><i class="fas fa-info-circle"></i> This will automatically reflect in Sales Reports.</p>
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

        // Function to calculate totals
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

                // Check stock
                if (variantSelect.value && qty > stock) {
                    warning.classList.remove('hidden');
                    row.querySelector('.qty-input').classList.add('border-red-500', 'bg-red-50');
                    hasError = true;
                } else {
                    warning.classList.add('hidden');
                    row.querySelector('.qty-input').classList.remove('border-red-500', 'bg-red-50');
                }

                // Compute subtotal (Using \u20B1 for Peso in JS to prevent encoding issues)
                const subtotal = price * qty;
                row.querySelector('.subtotal-text').innerText = '\u20B1' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                grandTotal += subtotal;
            });

            document.getElementById('grandTotal').innerText = '\u20B1' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            if (hasError) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Add New Row
        addRowBtn.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.className = 'product-row border-b border-slate-100';
            tr.innerHTML = `
                <td class="py-3 pr-2">
                    <select name="products[${rowCount}][variant_id]" class="variant-select w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" required>
                        <option value="">Select Product...</option>${variantOptions}
                    </select>
                    <p class="text-[10px] text-red-500 mt-1 stock-warning hidden font-bold"><i class="fas fa-exclamation-triangle"></i> Kulang ang stock!</p>
                </td>
                <td class="py-3 pr-2"><input type="number" name="products[${rowCount}][price]" step="0.01" class="price-input w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:border-caramel-500 outline-none transition-all font-bold" required placeholder="0.00"></td>
                <td class="py-3 pr-2"><input type="number" name="products[${rowCount}][quantity]" value="1" min="1" class="qty-input w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all" required></td>
                <td class="py-3 text-right"><span class="subtotal-text font-bold text-slate-900">\u20B10.00</span></td>
                <td class="py-3 text-center"><button type="button" class="remove-row text-slate-400 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button></td>
            `;
            productsTable.appendChild(tr);
            rowCount++;
        });

        // Auto-fill price when variant is selected
        productsTable.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.getAttribute('data-price') || '';
                e.target.closest('.product-row').querySelector('.price-input').value = price;
                calculateTotals();
            }
        });

        // Recalculate when qty or price changes
        productsTable.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                calculateTotals();
            }
        });

        // Remove Row
        productsTable.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (document.querySelectorAll('.product-row').length > 1) {
                    e.target.closest('.product-row').remove();
                    calculateTotals();
                } else { alert("You must have at least one product."); }
            }
        });
    });
</script>
@endpush