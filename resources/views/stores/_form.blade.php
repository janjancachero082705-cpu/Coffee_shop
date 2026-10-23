@php
    $isEdit = isset($store);
    $action = $isEdit ? route('stores.update', $store) : route('stores.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="max-w-2xl mx-auto space-y-6">
    @csrf @method($method)
    
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <div class="mb-6 border-b border-slate-200 pb-4">
            <h3 class="text-lg font-bold text-slate-900">Customer Information</h3>
            <p class="text-xs text-slate-500 mt-1">Enter the details and discount for this customer.</p>
        </div>
        
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Customer Name <span class="text-red-500">*</span></label>
                    <input type="text" name="store_name" required value="{{ old('store_name', $store->store_name ?? '') }}" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-200 outline-none transition-all" 
                           placeholder="e.g. Juan Dela Cruz / Sari-Sari Store">
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Address (Optional)</label>
                    <input type="text" name="address" value="{{ old('address', $store->address ?? '') }}" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-md text-sm focus:bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-200 outline-none transition-all" 
                           placeholder="e.g. Purok 1, Miarayon">
                </div>
            </div>

            {{-- CUSTOMER TYPE --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-2 uppercase tracking-wider">Customer Type <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <input type="radio" name="customer_type" id="type_regular" value="regular" class="peer sr-only" {{ old('customer_type', $store->customer_type ?? 'regular') == 'regular' ? 'checked' : '' }}>
                        <label for="type_regular" class="flex flex-col items-center justify-center p-3 bg-white border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50 peer-checked:border-slate-500 peer-checked:bg-slate-50 transition-all text-center">
                            <span class="font-semibold text-slate-900 text-sm">Regular</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Standard price</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="customer_type" id="type_suki" value="suki" class="peer sr-only" {{ old('customer_type', $store->customer_type ?? '') == 'suki' ? 'checked' : '' }}>
                        <label for="type_suki" class="flex flex-col items-center justify-center p-3 bg-white border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50 peer-checked:border-caramel-500 peer-checked:bg-caramel-50 transition-all text-center">
                            <span class="font-semibold text-slate-900 text-sm">Suki VIP</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Discounted price</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="customer_type" id="type_friend" value="friend" class="peer sr-only" {{ old('customer_type', $store->customer_type ?? '') == 'friend' ? 'checked' : '' }}>
                        <label for="type_friend" class="flex flex-col items-center justify-center p-3 bg-white border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all text-center">
                            <span class="font-semibold text-slate-900 text-sm">Friend</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Special price</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- DISCOUNT INPUT --}}
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Custom Discount (%)</label>
                <div class="relative">
                    <input type="number" name="discount" step="0.01" min="0" max="100" value="{{ old('discount', $store->discount ?? 0) }}" 
                           class="w-full pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-md text-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200 outline-none transition-all font-bold text-slate-800" 
                           placeholder="0.00">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-slate-500 sm:text-sm font-bold">%</span>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">I-set ang discount percentage para sa store na ito (e.g. 5 para sa 5%).</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('stores.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-md font-medium text-sm hover:bg-slate-50 transition-colors shadow-sm">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm rounded-md shadow-sm transition-all">
            {{ $isEdit ? 'Update Customer' : 'Save Customer' }}
        </button>
    </div>
</form>