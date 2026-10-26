@extends('layouts.admin')
@section('title', 'New Sales Delivery')
@section('subtitle', 'Create delivery & sale')

@section('content')
<style>
    /* Tinatanggal ang up/down arrows sa lahat ng number inputs */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    /* Custom scrollbar para malinis */
    .pos-scroll::-webkit-scrollbar { width: 6px; }
    .pos-scroll::-webkit-scrollbar-track { background: transparent; }
    .pos-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .pos-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="h-[calc(100vh-100px)] flex flex-col overflow-hidden">
    <!-- Gidugangan og onsubmit="return false;" as fallback -->
    <form action="{{ route('deliveries.store') }}" method="POST" id="deliveryForm" class="flex-1 flex flex-col min-h-0">
        @csrf
        
        @if($errors->any())
            <div class="mb-3 px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-bold border border-red-200 shrink-0">
                @foreach($errors->all() as $error) <span class="block">• {{ $error }}</span> @endforeach
            </div>
        @endif

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-5 min-h-0">
            
            {{-- KALIWA: Items List (8 columns) --}}
            <div class="lg:col-span-8 flex flex-col min-h-0 bg-white rounded-xl shadow-sm border border-slate-200">
                
                <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-orange-500"></i> Items to Deliver
                    </h2>
                </div>
                
                {{-- Scrollable Table Area --}}
                <div class="flex-1 overflow-y-auto pos-scroll p-0">
                    <table class="w-full text-left border-collapse" id="productsTable">
                        <thead class="bg-slate-50 sticky top-0 z-10 border-b border-slate-200">
                            <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                                <th class="py-3 px-4 w-[35%]">Product</th>
                                <th class="py-3 px-2 text-center w-[10%]">Stock</th>
                                <th class="py-3 px-2 text-right w-[18%]">Price</th>
                                <th class="py-3 px-2 text-center w-[12%]">Qty</th>
                                <th class="py-3 px-4 text-right w-[20%]">Subtotal</th>
                                <th class="py-3 px-2 w-[5%]"></th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            <tr class="product-row border-b border-slate-100 hover:bg-slate-50/50 transition-colors group">
                                <td class="py-2 px-4">
                                    <select name="products[0][variant_id]" class="variant-select w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none truncate" required>
                                        <option value="">Select product...</option>
                                        @foreach($variants as $v)
                                            <option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">
                                                {{ $v->product->name ?? 'Unknown' }} - {{ $v->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-red-500 mt-1 stock-warning hidden font-bold">Kulang stock!</p>
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <span class="stock-text inline-flex items-center justify-center px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold font-mono">-</span>
                                </td>
                                <td class="py-2 px-2">
                                    <input type="number" name="products[0][price]" step="0.01" class="price-input w-full px-2 py-2 bg-white border border-slate-300 rounded-md text-sm text-slate-800 text-right font-mono font-bold focus:border-orange-500 outline-none" required placeholder="0.00">
                                </td>
                                <td class="py-2 px-2">
                                    <input type="number" name="products[0][quantity]" value="1" min="1" class="qty-input w-full px-2 py-2 bg-white border border-slate-300 rounded-md text-sm text-center font-mono font-bold text-slate-700 focus:border-orange-500 outline-none" required>
                                </td>
                                <td class="py-2 px-4 text-right">
                                    <span class="subtotal-text font-mono font-black text-slate-800">₱0.00</span>
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <button type="button" class="remove-row w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-300 hover:text-red-600 hover:bg-red-50 transition-colors hidden"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="p-4">
                        <button type="button" id="addRowBtn" class="w-full py-3 bg-slate-50 border border-dashed border-slate-300 text-slate-600 font-bold rounded-lg hover:bg-slate-100 hover:text-orange-600 transition-colors flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-plus"></i> Add Another Item
                        </button>
                    </div>
                </div>
            </div>

            {{-- KANAN: Summary / Checkout (4 columns) --}}
            <div class="lg:col-span-4 flex flex-col min-h-0">
                <div class="bg-slate-900 rounded-xl shadow-xl flex flex-col h-full border border-slate-800 relative overflow-hidden">
                    
                    <div class="p-5 flex-1 flex flex-col">
                        
                        {{-- Customer & Date --}}
                        <div class="space-y-3 mb-5 shrink-0">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-widest">Customer</label>
                                <select name="store_id" id="storeSelect" class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-md text-sm font-semibold text-white focus:ring-2 focus:ring-orange-500 outline-none appearance-none" required>
                                    <option value="" data-discount="0">Select Customer...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" data-discount="{{ $store->discount ?? 0 }}">
                                            {{ $store->store_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-widest">Delivery Date</label>
                                <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-md text-sm font-semibold text-slate-300 focus:ring-2 focus:ring-orange-500 outline-none [color-scheme:dark]" required>
                            </div>
                        </div>

                        <div class="flex-1"></div> <!-- Spacer -->

                        {{-- Computations --}}
                        <div class="space-y-3 shrink-0 pb-4">
                            
                            <div class="flex justify-between items-center px-1">
                                <span class="text-xs font-bold text-slate-400">Subtotal</span>
                                <span class="text-sm font-mono font-bold text-slate-300" id="subTotalDisplay">₱0.00</span>
                            </div>

                            {{-- Discount Card --}}
                            <div class="bg-slate-800 rounded-lg p-3 border border-slate-700">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-300">Discount Rate</span>
                                    <div class="flex items-center gap-2">
                                        <input type="number" id="discountPercentInput" step="0.01" min="0" max="100" class="w-16 px-2 py-1 bg-slate-900 border border-slate-600 rounded text-right text-sm font-mono font-bold text-orange-400 focus:border-orange-500 outline-none" placeholder="0">
                                        <span class="text-xs font-bold text-slate-400">%</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center border-t border-slate-700/50 pt-2">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Discount Amount</span>
                                    <span class="text-sm font-mono font-bold text-orange-400" id="discountPesoDisplay">-₱0.00</span>
                                    <input type="hidden" name="discount" id="discountPesoHidden" value="0.00">
                                </div>
                            </div>

                            <div class="border-t border-slate-700 border-dashed my-2"></div>

                            <div class="flex justify-between items-end px-1">
                                <span class="text-xs font-bold text-slate-400 mb-1">TOTAL</span>
                                <span class="text-4xl font-mono font-black text-white tracking-tighter break-all text-right leading-none" id="grandTotal">₱0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Submit Button --}}
                    <div class="p-5 bg-slate-950 border-t border-slate-800 shrink-0">
                        <button type="submit" id="submitBtn" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-black text-lg py-3.5 rounded-lg transition-all shadow-[0_0_20px_rgba(234,88,12,0.3)] flex items-center justify-center gap-2 active:scale-[0.98]">
                            CONFIRM SALE
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
        const form = document.getElementById('deliveryForm');
        const productsTable = document.getElementById('productRows');
        const addRowBtn = document.getElementById('addRowBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        const discountPercentInput = document.getElementById('discountPercentInput');
        const discountPesoDisplay = document.getElementById('discountPesoDisplay');
        const discountPesoHidden = document.getElementById('discountPesoHidden');
        const storeSelect = document.getElementById('storeSelect');
        
        const variantOptions = `@foreach($variants as $v)<option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">{{ addslashes($v->product->name ?? 'Unknown') }} - {{ addslashes($v->name) }}</option>@endforeach`;

        // ==========================================
        // FIX PARA DILI MA-DOUBLE CLICK ANG BUTTON
        // ==========================================
        let isSubmitting = false; // Flag para mahibaw-an kung naka-submit na

        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                // Kung gi-double click, i-prevent ang ikaduhang submit
                e.preventDefault();
                return false;
            }
            
            // I-set ang flag to true
            isSubmitting = true;
            
            // I-disable ang button ug usbon ang text
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESSING...';
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });

        // ==========================================
        // CALCULATION LOGIC
        // ==========================================
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
                    row.querySelector('.qty-input').classList.add('text-red-600', 'border-red-500');
                    hasError = true;
                } else {
                    warning.classList.add('hidden');
                    row.querySelector('.qty-input').classList.remove('text-red-600', 'border-red-500');
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
            
            // Kung naay error sa stock, i-disable ang button
            if (hasError) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                // I-enable balik KUNG wala pa naka-submit
                if (!isSubmitting) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        addRowBtn.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.className = 'product-row border-b border-slate-100 hover:bg-slate-50/50 transition-colors group';
            tr.innerHTML = `
                <td class="py-2 px-4">
                    <select name="products[${rowCount}][variant_id]" class="variant-select w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none truncate" required>
                        <option value="">Select product...</option>${variantOptions}
                    </select>
                    <p class="text-[10px] text-red-500 mt-1 stock-warning hidden font-bold">Kulang stock!</p>
                </td>
                <td class="py-2 px-2 text-center"><span class="stock-text inline-flex items-center justify-center px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold font-mono">-</span></td>
                <td class="py-2 px-2"><input type="number" name="products[${rowCount}][price]" step="0.01" class="price-input w-full px-2 py-2 bg-white border border-slate-300 rounded-md text-sm text-slate-800 text-right font-mono font-bold focus:border-orange-500 outline-none" required placeholder="0.00"></td>
                <td class="py-2 px-2"><input type="number" name="products[${rowCount}][quantity]" value="1" min="1" class="qty-input w-full px-2 py-2 bg-white border border-slate-300 rounded-md text-sm text-center font-mono font-bold text-slate-700 focus:border-orange-500 outline-none" required></td>
                <td class="py-2 px-4 text-right"><span class="subtotal-text font-mono font-black text-slate-800">₱0.00</span></td>
                <td class="py-2 px-2 text-center"><button type="button" class="remove-row w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-300 hover:text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-times"></i></button></td>
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
                        stockBadge.classList.replace('bg-slate-100', 'bg-red-100');
                        stockBadge.classList.replace('text-slate-600', 'text-red-600');
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