@extends('layouts.admin')

@section('title', 'New Delivery')
@section('subtitle', 'Create a delivery receipt')

@section('actions')
    <a href="{{ route('deliveries.index') }}" class="btn btn-ghost btn-sm">← Back</a>
@endsection

@section('content')

<form method="POST" action="{{ route('deliveries.store') }}" id="deliveryForm">
    @csrf

    {{-- ═══ DELIVERY INFO ═══ --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Delivery Information</div>
                <div class="card-sub">Basic delivery details</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="field">
                <label class="label">Store <span class="req">*</span></label>
                <select name="store_id" id="storeSelect" class="input" required>
                    <option value="">— Select a store —</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ (old('store_id', $selectedStore)==$store->id)?'selected':'' }}>
                            {{ $store->code }} — {{ $store->store_name }}
                        </option>
                    @endforeach
                </select>
                @error('store_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Delivery Date <span class="req">*</span></label>
                <input type="date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" class="input" required>
                @error('delivery_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" class="input">
                @error('due_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field field-full">
                <label class="label">Notes</label>
                <textarea name="notes" rows="2" class="input" placeholder="Optional notes...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ═══ ITEMS ═══ --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Items to Deliver</div>
                <div class="card-sub">Add products and quantities</div>
            </div>
            <button type="button" class="btn btn-ghost btn-sm" onclick="addItem()">
                + Add Item
            </button>
        </div>

        @error('items')<div class="alert alert-error">{{ $message }}</div>@enderror

        <div id="itemsContainer" class="items-container"></div>

        <div class="totals">
            <div class="total-row">
                <span>Total Items</span>
                <strong id="totalItems">0</strong>
            </div>
            <div class="total-row total-grand">
                <span>Grand Total</span>
                <strong id="grandTotal">₱0.00</strong>
            </div>
        </div>
    </div>

    {{-- ═══ ACTIONS ═══ --}}
    <div class="form-actions">
        <a href="{{ route('deliveries.index') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            Save Delivery
        </button>
    </div>
</form>

{{-- ═══ ITEM TEMPLATE ═══ --}}
<template id="itemTemplate">
    <div class="item-row">
        <div class="item-grid">
            <div class="field">
                <label class="label">Product <span class="req">*</span></label>
                <select name="items[__INDEX__][product_id]" class="input product-select" onchange="onProductChange(this)" required>
                    <option value="">— Select —</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->price ?? $p->wholesale_price ?? 0 }}">
                            {{ $p->name }}@if($p->sku) ({{ $p->sku }})@endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label">Quantity <span class="req">*</span></label>
                <input type="number" name="items[__INDEX__][quantity_delivered]" class="input qty-input" min="1" value="1" required oninput="recalculate()">
            </div>
            <div class="field">
                <label class="label">Unit Price <span class="req">*</span></label>
                <div class="input-prefix">
                    <span class="prefix">₱</span>
                    <input type="number" step="0.01" name="items[__INDEX__][unit_price]" class="input input-with-prefix price-input" min="0" value="0" required oninput="recalculate()">
                </div>
            </div>
            <div class="field">
                <label class="label">Subtotal</label>
                <div class="subtotal-box" data-subtotal>₱0.00</div>
            </div>
            <button type="button" class="btn-remove" onclick="removeItem(this)" title="Remove item">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            </button>
        </div>
    </div>
</template>

@endsection

@push('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
    }
    .field-full { grid-column: 1 / -1; }
    .field { display: flex; flex-direction: column; }
    .req { color: #ef4444; font-weight: 700; }
    .field-error { font-size: 11px; color: #ef4444; margin-top: 5px; font-weight: 500; }
    .input-prefix { position: relative; display: flex; align-items: center; }
    .prefix {
        position: absolute; left: 12px;
        color: var(--text-muted); font-size: 13px;
        font-weight: 600; pointer-events: none;
    }
    .input-with-prefix { padding-left: 28px; }

    /* ═══ ITEMS ═══ */
    .items-container { display: flex; flex-direction: column; gap: 12px; }
    .item-row {
        background: rgba(20, 20, 26, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 16px;
        animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .item-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1.2fr 1fr auto;
        gap: 12px;
        align-items: end;
    }
    .subtotal-box {
        padding: 10px 12px;
        background: rgba(169, 120, 74, 0.08);
        border: 1px solid rgba(169, 120, 74, 0.2);
        border-radius: 8px;
        color: #c9a961;
        font-weight: 700;
        font-size: 13px;
        text-align: right;
    }
    .btn-remove {
        width: 38px; height: 38px;
        border-radius: 8px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #ef4444;
        display: grid; place-items: center;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-remove:hover { background: rgba(239, 68, 68, 0.2); }

    /* ═══ TOTALS ═══ */
    .totals {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-end;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        gap: 32px;
        min-width: 260px;
        font-size: 13px;
        color: var(--text-secondary);
    }
    .total-row strong { color: var(--text-primary); }
    .total-grand {
        font-size: 16px;
        padding-top: 12px;
        border-top: 1px solid rgba(169, 120, 74, 0.2);
    }
    .total-grand strong { color: #c9a961; font-size: 20px; font-weight: 800; }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 8px;
    }

    @media (max-width: 900px) {
        .form-grid { grid-template-columns: 1fr; }
        .item-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
    let itemIndex = 0;

    function addItem() {
        const template = document.getElementById('itemTemplate');
        const clone = template.content.cloneNode(true);
        const html = clone.firstElementChild.outerHTML.replace(/__INDEX__/g, itemIndex);
        document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
        itemIndex++;
        recalculate();
    }

    function removeItem(btn) {
        btn.closest('.item-row').remove();
        recalculate();
    }

    function onProductChange(select) {
        const opt = select.options[select.selectedIndex];
        const price = opt.getAttribute('data-price') || 0;
        const row = select.closest('.item-row');
        row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
        recalculate();
    }

    function recalculate() {
        let total = 0;
        let count = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            row.querySelector('[data-subtotal]').textContent = '₱' + subtotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            total += subtotal;
            count += qty;
        });

        document.getElementById('totalItems').textContent = count;
        document.getElementById('grandTotal').textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Init with one item
    document.addEventListener('DOMContentLoaded', () => {
        addItem();
    });
</script>
@endpush