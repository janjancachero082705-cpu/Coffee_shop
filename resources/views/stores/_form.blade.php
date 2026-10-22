@php
    $isEdit = isset($store);
    $action = $isEdit ? route('stores.update', $store) : route('stores.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="max-w-3xl mx-auto space-y-6">
    @csrf @method($method)
    
    <div class="bg-white rounded-3xl shadow-card p-8 border border-espresso-100 relative overflow-hidden">
        <!-- Decorative Background -->
        <div class="absolute -right-10 -top-10 text-espresso-50 opacity-50">
            <i class="fas fa-store fa-10x"></i>
        </div>

        <div class="relative z-10">
            <h3 class="text-2xl font-serif font-bold text-espresso-900 mb-6 flex items-center gap-3">
                <i class="fas fa-store text-caramel-500"></i> Store Information
            </h3>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-espresso-800 mb-2">Store Name <span class="text-red-500">*</span></label>
                        <input type="text" name="store_name" required value="{{ old('store_name', $store->store_name ?? '') }}" 
                               class="w-full px-4 py-3 bg-espresso-50/50 border border-espresso-200 rounded-xl focus:bg-white focus:border-caramel-400 focus:ring-4 focus:ring-caramel-400/10 outline-none transition-all placeholder-espresso-300" 
                               placeholder="e.g. Juan's Sari-Sari Store">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-espresso-800 mb-2">Address (Optional)</label>
                        <input type="text" name="address" value="{{ old('address', $store->address ?? '') }}" 
                               class="w-full px-4 py-3 bg-espresso-50/50 border border-espresso-200 rounded-xl focus:bg-white focus:border-caramel-400 focus:ring-4 focus:ring-caramel-400/10 outline-none transition-all placeholder-espresso-300" 
                               placeholder="e.g. Purok 1, Miarayon">
                    </div>
                </div>

                {{-- CUSTOMER TYPE RADIO CARDS --}}
                <div>
                    <label class="block text-sm font-bold text-espresso-800 mb-3">Customer Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Regular --}}
                        <div>
                            <input type="radio" name="customer_type" id="type_regular" value="regular" class="peer sr-only" {{ old('customer_type', $store->customer_type ?? 'regular') == 'regular' ? 'checked' : '' }}>
                            <label for="type_regular" class="flex flex-col items-center justify-center p-4 bg-white border-2 border-espresso-100 rounded-2xl cursor-pointer hover:bg-espresso-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2 peer-checked:scale-110 transition-transform">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="font-bold text-espresso-900 text-sm">Regular</div>
                                <div class="text-[10px] text-espresso-500 text-center mt-1">Standard pricing</div>
                            </label>
                        </div>

                        {{-- Suki --}}
                        <div>
                            <input type="radio" name="customer_type" id="type_suki" value="suki" class="peer sr-only" {{ old('customer_type', $store->customer_type ?? '') == 'suki' ? 'checked' : '' }}>
                            <label for="type_suki" class="flex flex-col items-center justify-center p-4 bg-white border-2 border-espresso-100 rounded-2xl cursor-pointer hover:bg-espresso-50 peer-checked:border-caramel-500 peer-checked:bg-caramel-50 transition-all">
                                <div class="w-10 h-10 rounded-full bg-caramel-100 text-caramel-600 flex items-center justify-center mb-2 peer-checked:scale-110 transition-transform">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="font-bold text-espresso-900 text-sm">Suki (VIP)</div>
                                <div class="text-[10px] text-espresso-500 text-center mt-1">Best discounts applied</div>
                            </label>
                        </div>

                        {{-- Friend --}}
                        <div>
                            <input type="radio" name="customer_type" id="type_friend" value="friend" class="peer sr-only" {{ old('customer_type', $store->customer_type ?? '') == 'friend' ? 'checked' : '' }}>
                            <label for="type_friend" class="flex flex-col items-center justify-center p-4 bg-white border-2 border-espresso-100 rounded-2xl cursor-pointer hover:bg-espresso-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-2 peer-checked:scale-110 transition-transform">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div class="font-bold text-espresso-900 text-sm">Friend</div>
                                <div class="text-[10px] text-espresso-500 text-center mt-1">Special pricing</div>
                            </label>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('stores.index') }}" class="px-6 py-3 bg-white border border-espresso-200 text-espresso-700 rounded-xl font-bold hover:bg-espresso-50 transition-colors shadow-sm">Cancel</a>
        <button type="submit" class="px-8 py-3 bg-caramel-400 hover:bg-caramel-500 text-espresso-900 font-bold rounded-xl shadow-md shadow-caramel-400/30 transition-all flex items-center gap-2">
            <i class="fas fa-save"></i> {{ $isEdit ? 'Update Store' : 'Save Store' }}
        </button>
    </div>
</form>