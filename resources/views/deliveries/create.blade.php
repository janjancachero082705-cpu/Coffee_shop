@extends('layouts.admin')
@section('title', 'New Sales Delivery')
@section('subtitle', 'Create delivery & sale')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto">
    <form action="{{ route('deliveries.store') }}" method="POST" id="deliveryForm">
        @csrf
        
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-xl text-sm font-medium border border-red-200">
                @foreach($errors->all() as $error) <p>• {{ $error }}</p> @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- LEFT SIDE: ITEMS --}}
            <div class="lg:col-span-8">
                <div class="card overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-ink-200 flex items-center justify-between bg-white shrink-0">
                        <h3 class="text-sm font-extrabold text-ink-800 flex items-center gap-2">
                            <i class="fas fa-box text-brand-500"></i> Items to Deliver
                        </h3>
                    </div>
                    
                    <div class="p-6 overflow-y-auto max-h-[calc(100vh-280px)]">
                        <table class="w-full text-sm" id="productsTable">
                            <thead class="sticky top-0 bg-white z-10">
                                <tr class="text-left text-ink-400 text-[10px] uppercase tracking-widest border-b border-ink-200">
                                    <th class="pb-3 font-bold w-[35%]">Product</th>
                                    <th class="pb-3 font-bold text-center w-[10%]">Stock</th>
                                    <th class="pb-3 font-bold w-[18%] text-right">Price (₱)</th>
                                    <th class="pb-3 font-bold w-[12%] text-center">Qty</th>
                                    <th class="pb-3 font-bold text-right w-[15%]">Total</th>
                                    <th class="pb-3 w-[10%]"></th>
                                </tr>
                            </thead>
                            <tbody id="productRows">
                                <tr class="product-row border-b border-ink-100 group hover:bg-ink-50 transition-colors">
                                    <td class="py-3 pr-3">
                                        <select name="products[0][variant_id]" class="variant-select input" required>
                                            <option value="">Select product...</option>
                                            @foreach($variants as $v)
                                                <option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">
                                                    {{ $v->product->name ?? 'Unknown' }} - {{ $v->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-[10px] text-red-500 mt-1 stock-warning hidden font-bold">Kulang stock!</p>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="stock-text inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-ink-100 text-ink-500 text-xs font-bold">-</span>
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" name="products[0][price]" step="0.01" class="price-input input text-right font-bold" required placeholder="0.00">
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" name="products[0][quantity]" value="1" min="1" class="qty-input input text-center font-bold" required>
                                    </td>
                                    <td class="py-3 text-right">
                                        <span class="subtotal-text font-bold text-ink-800 text-sm">₱0.00</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <button type="button" class="remove-row w-8 h-8 inline-flex items-center justify-center rounded-lg text-ink-300 hover:text-red-500 hover:bg-red-50 transition-colors hidden"><i class="fas fa-times text-xs"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <button type="button" id="addRowBtn" class="mt-4 px-4 py-2 bg-ink-50 text-ink-600 font-bold rounded-lg hover:bg-ink-100 border border-ink-200 transition-all text-xs flex items-center gap-2">
                            <i class="fas fa-plus text-brand-500"></i> Add Another Item
                        </button>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: SUMMARY --}}
            <div class="lg:col-span-4">
                <div class="card overflow-hidden relative">
                    <div class="p-6 relative z-10">
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-[10px] font-bold text-ink-500 mb-1.5 uppercase tracking-widest">Customer</label>
                                <select name="store_id" id="storeSelect" class="input font-bold cursor-pointer" required>
                                    <option value="" data-discount="0">Select Customer...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" data-discount="{{ $store->discount ?? 0 }}">
                                            {{ $store->store_name }} ({{ ucfirst($store->customer_type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-ink-500 mb-1.5 uppercase tracking-widest">Delivery Date</label>
                                <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="input" required>
                            </div>
                        </div>

                        <hr class="border-ink-200 mb-5">

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-ink-500 uppercase tracking-wider">Subtotal</span>
                                <span class="text-sm font-bold text-ink-800" id="subTotalDisplay">₱0.00</span>
                            </div>

                            <div class="bg-ink-50 p-3 rounded-xl border border-ink-200 flex items-center justify-between">
                                <label class="text-xs font-bold text-ink-700 uppercase tracking-wider">Discount</label>
                                <div class="flex items-center gap-3">
                                    <div class="relative w-16">
                                        <input type="number" id="discountPercentInput" step="0.01" min="0" max="100" class="input text-right font-bold text-brand-600 !py-1.5" placeholder="0">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-ink-400 text-[10px] font-bold">%</span>
                                    </div>
                                    <div class="w-20 text-right">
                                        <span class="text-sm font-bold text-brand-600" id="discountPesoDisplay">-₱0.00</span>
                                        <input type="hidden" name="discount" id="discountPesoHidden" value="0.00">
                                    </div>
                                </div>
                            </div>

                            <hr class="border-ink-200">
                            
                            <div class="pt-1">
                                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Amount Due</div>
                                <div class="text-3xl font-black text-ink-900 tracking-tight break-all" id="grandTotal">₱0.00</div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-full !py-3.5">
                            Confirm Sale <i class="fas fa-check-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = 1;
        const productsTable = document.getElementById('productRows');
        const addRowBtn = document.getElementById('addRowBtn');
        
        const discountPercentInput = document.getElementById('discountPercentInput');
        const discountPesoDisplay = document.getElementById('discountPesoDisplay');
        const discountPesoHidden = document.getElementById('discountPesoHidden');
        const storeSelect = document.getElementById('storeSelect');
        
        const variantOptions = `@foreach($variants as $v)<option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">{{ addslashes($v->product->name ?? 'Unknown') }} - {{ addslashes($v->name) }}</option>@endforeach`;

        function calculateTotals() {
            let subTotal = 0;
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
                    row.querySelector('.qty-input').classList.add('!border-red-500', '!text-red-600');
                    hasError = true;
                } else {
                    warning.classList.add('hidden');
                    row.querySelector('.qty-input').classList.remove('!border-red-500', '!text-red-600');
                }

                const itemSubtotal = price * qty;
                row.querySelector('.subtotal-text').innerText = '₱' + itemSubtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                subTotal += itemSubtotal;
            });

            let discountPercent = parseFloat(discountPercentInput.value) || 0;
            
            if(discountPercent > 100) {
                discountPercent = 100;
                discountPercentInput.value = 100;
            }

            let discountPeso = subTotal * (discountPercent / 100);

            discountPesoDisplay.innerText = '-₱' + discountPeso.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            discountPesoHidden.value = discountPeso.toFixed(2);

            const finalTotal = subTotal - discountPeso;

            document.getElementById('subTotalDisplay').innerText = '₱' + subTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('grandTotal').innerText = '₱' + finalTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                if (hasError) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        addRowBtn.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.className = 'product-row border-b border-ink-100 group hover:bg-ink-50 transition-colors';
            tr.innerHTML = `
                <td class="py-3 pr-3">
                    <select name="products[${rowCount}][variant_id]" class="variant-select input" required>
                        <option value="">Select product...</option>${variantOptions}
                    </select>
                    <p class="text-[10px] text-red-500 mt-1 stock-warning hidden font-bold">Kulang stock!</p>
                </td>
                <td class="py-3 text-center"><span class="stock-text inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-ink-100 text-ink-500 text-xs font-bold">-</span></td>
                <td class="py-3 pr-3"><input type="number" name="products[${rowCount}][price]" step="0.01" class="price-input input text-right font-bold" required placeholder="0.00"></td>
                <td class="py-3 pr-3"><input type="number" name="products[${rowCount}][quantity]" value="1" min="1" class="qty-input input text-center font-bold" required></td>
                <td class="py-3 text-right"><span class="subtotal-text font-bold text-ink-800 text-sm">₱0.00</span></td>
                <td class="py-3 text-center"><button type="button" class="remove-row w-8 h-8 inline-flex items-center justify-center rounded-lg text-ink-300 hover:text-red-500 hover:bg-red-50 transition-colors"><i class="fas fa-times text-xs"></i></button></td>
            `;
            productsTable.appendChild(tr);
            rowCount++;
            calculateTotals();
        });

        productsTable.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                let price = selectedOption.getAttribute('data-price');
                let stock = selectedOption.getAttribute('data-stock');
                const row = e.target.closest('.product-row');
                
                if (price !== null && price !== '') {
                    price = parseFloat(price).toFixed(2);
                } else {
                    price = '';
                }

                row.querySelector('.price-input').value = price;
                
                if (stock !== null && stock !== '') {
                    const stockBadge = row.querySelector('.stock-text');
                    stockBadge.innerText = stock;
                    if (parseInt(stock) <= 0) {
                        stockBadge.classList.replace('bg-ink-100', 'bg-red-100');
                        stockBadge.classList.replace('text-ink-500', 'text-red-600');
                    } else {
                        stockBadge.classList.replace('bg-red-100', 'bg-emerald-100');
                        stockBadge.classList.replace('text-red-600', 'text-emerald-700');
                    }
                }
                calculateTotals();
            }
        });

        productsTable.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                calculateTotals();
            }
        });

        discountPercentInput.addEventListener('input', calculateTotals);

        storeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            let activeCustomerDiscount = parseFloat(selectedOption.getAttribute('data-discount')) || 0;
            
            discountPercentInput.value = activeCustomerDiscount > 0 ? activeCustomerDiscount : '';
            calculateTotals();
        });

        productsTable.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (document.querySelectorAll('.product-row').length > 1) {
                    e.target.closest('.product-row').remove();
                    calculateTotals();
                } else { alert("Kinahanglan naay isa ka product."); }
            }
        });

        calculateTotals();
    });
</script>
@endpush