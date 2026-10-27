@extends('layouts.admin')
@section('title', 'Adjust Stock')
@section('subtitle', $product->name . ' — Current: ' . number_format($product->stock, 2) . ' ' . $product->unit_type)

@section('content')

<form method="POST" action="{{ route('inventory.adjust.store', $product) }}" class="max-w-3xl space-y-5">
    @csrf

    <section class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-ink-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center"><i class="fas fa-sliders"></i></div>
            <div>
                <h2 class="text-base font-extrabold text-ink-900">Manual stock adjustment</h2>
                <p class="text-sm text-ink-500">{{ $product->name }}</p>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Current stock --}}
            <div class="flex items-center justify-between gap-4 rounded-2xl px-5 py-4" style="background:#FDE9D5; border:1px solid #F7D9BC">
                <span class="text-sm font-semibold" style="color:#8F5A24">Current stock</span>
                <span class="text-2xl font-extrabold text-ink-900 tabular-nums">{{ number_format($product->stock, 2) }} <span class="text-sm font-semibold text-ink-500">{{ $product->unit_type }}</span></span>
            </div>

            {{-- New quantity --}}
            <div>
                <label for="new_quantity" class="block text-sm font-semibold text-ink-800 mb-1.5">New stock quantity <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" id="new_quantity" name="new_quantity" step="0.01" min="0" required
                           value="{{ old('new_quantity', $product->stock) }}" data-current="{{ $product->stock }}"
                           class="input !py-3 !text-xl !font-bold !pr-20 @error('new_quantity') !border-red-400 @enderror">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-ink-400 pointer-events-none">{{ $product->unit_type }}</span>
                </div>
                @error('new_quantity')<p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>@enderror
                <p class="text-sm text-ink-500 mt-2">Ibutang ang total quantity nga dapat naa. System auto-calculate sa difference.</p>
                <div id="diffBox" class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold bg-ink-100 text-ink-600" aria-live="polite">No change</div>
            </div>

            {{-- Reason --}}
            <div>
                <label for="notes" class="block text-sm font-semibold text-ink-800 mb-1.5">Reason / notes <span class="text-red-500">*</span></label>
                <textarea id="notes" name="notes" rows="3" required class="input @error('notes') !border-red-400 @enderror"
                          placeholder="e.g., Physical count correction, damaged items, replenishment...">{{ old('notes') }}</textarea>
                @error('notes')<p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 rounded-xl px-4 py-3 text-sm" style="background:#FFF3E5; border:1px solid #FFE0BD; color:#8F4B00">
                <i class="fas fa-circle-info mt-0.5"></i>
                <span>Kining adjustment ma-log sa Stock Card with timestamp.</span>
            </div>
        </div>

        <div class="px-6 py-4 bg-ink-50 border-t border-ink-100 flex justify-end gap-3">
            <a href="{{ route('inventory.show', $product) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-check text-xs"></i>Save adjustment</button>
        </div>
    </section>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('new_quantity');
    const box = document.getElementById('diffBox');
    const unit = @json($product->unit_type);
    const cur = parseFloat(input.dataset.current) || 0;

    function update() {
        const next = parseFloat(input.value);
        if (isNaN(next)) { box.textContent = 'No change'; box.className = base('bg-ink-100 text-ink-600'); return; }
        const d = Number((next - cur).toFixed(2));
        if (d === 0) { box.textContent = 'No change'; box.className = base('bg-ink-100 text-ink-600'); }
        else if (d > 0) { box.textContent = '+' + d + ' ' + unit + ' will be added'; box.className = base('bg-emerald-50 text-emerald-700'); }
        else { box.textContent = d + ' ' + unit + ' will be removed'; box.className = base('bg-red-50 text-red-700'); }
    }
    const base = c => 'mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold ' + c;
    input.addEventListener('input', update);
    update();
})();
</script>
@endpush