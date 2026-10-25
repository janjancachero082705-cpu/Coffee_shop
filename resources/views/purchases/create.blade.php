@extends('layouts.admin')
@section('title', 'Record Purchase')
@section('subtitle', 'Add stock from supplier')

@section('content')
<form action="{{ route('purchases.store') }}" method="POST" class="max-w-3xl mx-auto space-y-6" id="purchaseForm">
    @csrf
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-200 bg-slate-50/50 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-caramel-100 flex items-center justify-center text-caramel-600 shadow-inner">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-900">Purchase Entry</h3>
                <p class="text-sm text-slate-500 mt-1">Pumili sa listahan o mag-type ng bagong item. (Pwede mag-input ng 0.5 o 0.75 kung kg).</p>
            </div>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- SUPPLIER --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Supplier Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-truck absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="supplier_name" required value="{{ $selectedSupplier ?? old('supplier_name') }}" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 focus:ring-4 focus:ring-caramel-500/10 outline-none transition-all" placeholder="e.g. Miarayon Farmers Coop">
                    </div>
                </div>
                
                {{-- DATE --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all">
                    </div>
                </div>

                {{-- ITEM NAME (Custom Dropdown) --}}
                <div class="relative" id="customDropdownContainer">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Item Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-box absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="item_name" id="itemNameInput" required autocomplete="off" class="w-full pl-11 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 focus:ring-4 focus:ring-caramel-500/10 outline-none transition-all" placeholder="Select or type item...">
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs cursor-pointer" id="dropdownIcon"></i>
                    </div>
                    
                    <div id="dropdownMenu" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                        <ul id="dropdownList" class="py-1 text-sm text-slate-700"></ul>
                    </div>
                </div>

                {{-- QUANTITY (Decimal allowed) --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Quantity <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-sort-numeric-up absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="number" name="quantity" id="qtyInput" step="0.01" min="0.01" required class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all font-bold text-slate-800" placeholder="e.g. 0.5, 0.75, 10">
                    </div>
                </div>

                {{-- UNIT --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Unit <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-balance-scale absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="unit" id="unitSelect" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all cursor-pointer appearance-none">
                            <option value="kg">Kilograms (kg)</option>
                            <option value="pcs">Pieces (pcs)</option>
                            <option value="grams">Grams (g)</option>
                            <option value="pack">Packs</option>
                            <option value="bundle">Bundles</option>
                        </select>
                    </div>
                </div>

                {{-- COST PER UNIT --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Cost per Unit (&#8369;) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">&#8369;</span>
                        <input type="number" name="cost_per_unit" id="costInput" step="0.01" min="0" required class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 outline-none transition-all font-bold text-slate-800 text-lg" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER & SUMMARY --}}
        <div class="px-8 py-5 border-t border-slate-200 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Cost:</span>
                <span class="text-xl font-black text-caramel-600" id="totalCostDisplay">&#8369;0.00</span>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('purchases.index') }}" class="px-6 py-3 bg-white border border-slate-300 text-slate-700 rounded-lg font-bold text-sm hover:bg-slate-50 transition-colors shadow-sm">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-lg shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Save to Inventory
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    // Gikan ni sa Controller: $existingItems
    const productsData = @json($existingItems);
</script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemInput = document.getElementById('itemNameInput');
        const menu = document.getElementById('dropdownMenu');
        const list = document.getElementById('dropdownList');
        const icon = document.getElementById('dropdownIcon');
        const container = document.getElementById('customDropdownContainer');

        // 1. Item Dropdown Logic
        function renderList(filterText = '') {
            list.innerHTML = '';
            const filtered = productsData.filter(p => p.name.toLowerCase().includes(filterText.toLowerCase()));
            
            if (filtered.length === 0) {
                list.innerHTML = `<li class="px-4 py-3 text-sm text-slate-500 italic">Type the new item name.</li>`;
            } else {
                filtered.forEach(p => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2.5 hover:bg-caramel-50 cursor-pointer transition-colors font-medium border-b border-slate-50 last:border-0';
                    li.innerHTML = `<i class="fas fa-box text-slate-400 text-xs mr-2"></i> ${p.name}`;
                    li.addEventListener('click', () => {
                        itemInput.value = p.name;
                        menu.classList.add('hidden');
                    });
                    list.appendChild(li);
                });
            }
        }

        itemInput.addEventListener('focus', () => { renderList(itemInput.value); menu.classList.remove('hidden'); });
        itemInput.addEventListener('input', () => { renderList(itemInput.value); menu.classList.remove('hidden'); });
        icon.addEventListener('click', () => { menu.classList.toggle('hidden'); if(!menu.classList.contains('hidden')) renderList(itemInput.value); });
        document.addEventListener('click', (e) => { if (!container.contains(e.target)) menu.classList.add('hidden'); });

        // 2. Live Total Calculation
        const qtyInput = document.getElementById('qtyInput');
        const costInput = document.getElementById('costInput');
        const totalDisplay = document.getElementById('totalCostDisplay');

        function calculateTotal() {
            const qty = parseFloat(qtyInput.value) || 0;
            const cost = parseFloat(costInput.value) || 0;
            totalDisplay.innerText = '\u20B1' + (qty * cost).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        qtyInput.addEventListener('input', calculateTotal);
        costInput.addEventListener('input', calculateTotal);
    });
</script>
@endpush
@endsection