@extends('layouts.admin')
@section('title', 'New Sales Delivery')
@section('subtitle', 'Create delivery & sale')

@section('content')
<style>
    /* ==========================================
       MOBILE DISCOUNT SECTION ARRANGEMENT
       ========================================== */
    @media (max-width: 767px) {
        .discount-row {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 14px !important;
            gap: 10px !important;
            background-color: #FAF9F7 !important; /* Subtle background */
            border-radius: 12px !important;
        }

        /* Ibabaw: Label */
        .discount-row > label {
            font-size: 11px !important;
            color: #78716A !important;
            margin-bottom: 2px !important;
        }

        /* Ubos: Ang input ug ang Total */
        .discount-row > div {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            gap: 12px !important;
        }

        /* Ang input container */
        .discount-row .relative.w-16 {
            width: 50% !important; /* Himuong 50% ang gilapdon */
            flex: 1;
        }

        /* Ang input box mismo */
        .discount-row input[type="number"] {
            height: 48px !important;
            font-size: 18px !important; /* Dako-dako para sayon basahon */
            padding: 0 35px 0 15px !important; /* Space sa too para sa % */
            border-radius: 10px !important;
            border: 1.5px solid #E8E5E0 !important;
            background: #FFFFFF !important;
            color: #E86100 !important;
            width: 100% !important;
        }

        /* Ang % sign */
        .discount-row .absolute.right-2 {
            right: 12px !important;
            font-size: 14px !important;
        }

        /* Ang peso display (Total Discount) */
        .discount-row .w-20 {
            width: 50% !important;
            text-align: right !important;
            font-size: 18px !important; /* Dako-dako nga text */
            padding-right: 4px !important;
        }
    }
</style>
<style>
    /* ==========================================
       FINAL MASTER LAYOUT (MOBILE DELIVERY FORM)
       ========================================== */
    
    /* Pugson og light mode ang tanang dropdown ug inputs */
    select, option, input, textarea {
        color-scheme: light !important;
        background-color: #FFFFFF !important;
        color: #1A1715 !important;
    }
    
    option {
        background-color: #FFFFFF !important;
        color: #1A1715 !important;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        /* 1. Tagoan ang Table Header para dili maglutaw ang text */
        #productsTable thead {
            display: none !important;
        }

        /* 2. I-reset ang Table into Card Layout */
        #productsTable, 
        #productsTable tbody {
            display: block !important;
            width: 100% !important;
            border-collapse: collapse;
        }

        /* 3. Ang Card Container */
        .product-row {
            display: flex !important;
            flex-wrap: wrap !important;
            border: 1px solid #E8E5E0 !important;
            border-radius: 12px !important;
            padding: 16px !important;
            margin-bottom: 16px !important;
            background: #FFFFFF !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
            position: relative;
            gap: 12px 8px;
            width: 100% !important;
        }

        .product-row td {
            display: block !important;
            border: none !important;
            padding: 0 !important;
        }

        /* --- ROW 1: PRODUCT SELECT UG REMOVE BUTTON --- */
        .product-row td:nth-child(1) {
            width: 100% !important;
            padding-right: 40px !important; /* Space para sa X button */
        }
        
        .product-row td:nth-child(6) {
            position: absolute !important;
            top: 16px !important;
            right: 16px !important;
            width: 32px !important;
            height: 32px !important;
        }

        /* --- ROW 2: STOCK, PRICE, QTY (Magtapad sa tunga) --- */
        .product-row td:nth-child(2),
        .product-row td:nth-child(3),
        .product-row td:nth-child(4) {
            width: calc(33.333% - 6px) !important; /* 3 columns */
            flex: none !important;
            display: flex !important;
            flex-direction: column !important;
        }

        /* Labels sa ibabaw sa matag input */
        .product-row td:nth-child(2)::before { content: "STOCK"; display: block; font-size: 9px; color: #78716A; font-weight: 800; margin-bottom: 4px; text-align: center; letter-spacing: 0.05em; }
        .product-row td:nth-child(3)::before { content: "PRICE"; display: block; font-size: 9px; color: #78716A; font-weight: 800; margin-bottom: 4px; text-align: center; letter-spacing: 0.05em; }
        .product-row td:nth-child(4)::before { content: "QTY"; display: block; font-size: 9px; color: #78716A; font-weight: 800; margin-bottom: 4px; text-align: center; letter-spacing: 0.05em; }

        /* --- ROW 3: SUBTOTAL --- */
        .product-row td:nth-child(5) {
            width: 100% !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-top: 4px !important;
            padding-top: 12px !important;
            border-top: 1px dashed #E8E5E0 !important;
        }
        .product-row td:nth-child(5)::before {
            content: "SUBTOTAL";
            font-size: 11px;
            color: #78716A;
            font-weight: 800;
        }

        /* --- INPUTS UG SELECTS STYLING --- */
        .variant-select, .price-input, .qty-input {
            width: 100% !important;
            height: 44px !important;
            padding: 0 10px !important;
            font-size: 15px !important;
            border-radius: 8px !important;
            border: 1px solid #D3CEC6 !important;
            text-align: center;
            font-weight: 700;
        }

        .variant-select {
            text-align: left !important;
            padding-right: 30px !important;
            /* Custom arrow para wala nay native look */
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23A29B90%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            background-size: 10px !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }
        
        .stock-text {
            width: 100% !important;
            height: 44px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px !important;
            border-radius: 8px !important;
            background: #F3F1EE !important;
        }
    }
</style>







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
                    
                    <div class="p-4 md:p-6 overflow-y-auto max-h-[calc(100vh-280px)] custom-scrollbar">
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
                                        <input type="number" name="products[0][price]" step="0.01" class="price-input input text-right font-bold" required placeholder="0.00" max="999999" maxlength="9">
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" name="products[0][quantity]" value="1" min="1" class="qty-input input text-center font-bold" required max="999999" maxlength="9">
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
                            <div class="flex justify-between items-center delivery-summary-box">
                                <span class="text-xs font-bold text-ink-500 uppercase tracking-wider">Subtotal</span>
                                <span class="text-sm font-bold text-ink-800" id="subTotalDisplay">₱0.00</span>
                            </div>

                            <div class="bg-ink-50 p-3 rounded-xl border border-ink-200 flex items-center justify-between discount-row">
                                <label class="text-xs font-bold text-ink-700 uppercase tracking-wider">Discount</label>
                                <div class="flex items-center gap-3">
                                    <div class="relative w-16">
                                        <input type="number" id="discountPercentInput" step="0.01" min="0" max="999999" class="input text-right font-bold text-brand-600 !py-1.5" placeholder="0" maxlength="9">
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
<!-- CUSTOM DROPDOWN SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Choices === 'undefined') return;

        // Function para i-convert ang native select ngadto sa nindot nga Choices.js
        function initChoices(select) {
            if (select.classList.contains('choices-initialized')) return;
            new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                classNames: { containerOuter: 'choices w-full' }
            });
            select.classList.add('choices-initialized');
        }

        // 1. I-apply sa mga existing dropdowns inig-load sa page
        document.querySelectorAll('.variant-select').forEach(initChoices);

        // 2. I-apply pud sa mga bag-ong dropdown inig-click sa "Add Another Item"
        const productsTable = document.getElementById('productRows');
        if (productsTable) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.classList.contains('product-row')) {
                            const newSelect = node.querySelector('.variant-select');
                            if (newSelect) initChoices(newSelect);
                        }
                    });
                });
            });
            observer.observe(productsTable, { childList: true });
        }
    });
</script>
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
                <td class="py-3 pr-3"><input type="number" name="products[${rowCount}][price]" step="0.01" class="price-input input text-right font-bold" required placeholder="0.00" max="999999" maxlength="9"></td>
                <td class="py-3 pr-3"><input type="number" name="products[${rowCount}][quantity]" value="1" min="1" class="qty-input input text-center font-bold" required max="999999" maxlength="9"></td>
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