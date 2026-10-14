@extends('layouts.admin')

@section('title', 'New Delivery')
@section('subtitle', 'Create a delivery receipt')

@section('actions')
    <a href="{{ route('deliveries.index') }}" class="btn btn-ghost btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════════ --}}
{{-- BACK BUTTON                                    --}}
{{-- ═══════════════════════════════════════════════ --}}
<a href="{{ route('deliveries.index') }}" class="nd-back">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    <span>Back to Deliveries</span>
</a>

{{-- ═══════════════════════════════════════════════ --}}
{{-- HERO                                            --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="nd-hero">
    <div class="nd-hero-grid"></div>

    <div class="nd-hero-inner">
        <div class="nd-hero-left">
            <div class="nd-hero-eyebrow">New Delivery</div>
            <h1 class="nd-hero-title">Create Delivery</h1>
            <p class="nd-hero-sub">Mag-create og bag-ong delivery receipt para sa store</p>
        </div>

        <div class="nd-hero-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('deliveries.store') }}" id="deliveryForm">
    @csrf

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- DELIVERY INFO CARD                              --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="nd-card">
        <div class="nd-card-head">
            <div class="nd-card-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4M12 8h.01"/>
                </svg>
            </div>
            <div class="nd-card-header-text">
                <div class="nd-card-title">Delivery Information</div>
                <div class="nd-card-sub">Basic delivery details</div>
            </div>
        </div>

        <div class="nd-form-grid">
            <div class="nd-field">
                <label class="nd-label">Store <span class="nd-req">*</span></label>
                <select name="store_id" id="storeSelect" class="nd-input" required>
                    <option value="">-- Select a store --</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ (old('store_id', $selectedStore)==$store->id)?'selected':'' }}>
                            {{ $store->code }} - {{ $store->store_name }}
                        </option>
                    @endforeach
                </select>
                @error('store_id')<div class="nd-error">{{ $message }}</div>@enderror
            </div>

            <div class="nd-field">
                <label class="nd-label">Delivery Date <span class="nd-req">*</span></label>
                <input type="date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" class="nd-input" required>
                @error('delivery_date')<div class="nd-error">{{ $message }}</div>@enderror
            </div>

            <div class="nd-field nd-field-full">
                <label class="nd-label">Notes</label>
                <textarea name="notes" rows="2" class="nd-input nd-textarea" placeholder="Optional notes...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- ITEMS CARD                                      --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="nd-card">
        <div class="nd-card-head">
            <div class="nd-card-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="nd-card-header-text">
                <div class="nd-card-title">Items to Deliver</div>
                <div class="nd-card-sub">Add products and quantities</div>
            </div>
            <button type="button" class="nd-btn-add" onclick="addItem()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Add Item
            </button>
        </div>

        @error('items')<div class="nd-alert-error">{{ $message }}</div>@enderror

        <div id="itemsContainer" class="nd-items-container"></div>

        <div class="nd-totals">
            <div class="nd-total-row">
                <span class="nd-total-lbl">Total Items</span>
                <strong id="totalItems" class="nd-total-val">0</strong>
            </div>
            <div class="nd-total-row nd-total-grand">
                <span class="nd-total-lbl-grand">Grand Total</span>
                <strong id="grandTotal" class="nd-total-val-grand">&#8369;0.00</strong>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- ACTIONS                                         --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="nd-actions">
        <a href="{{ route('deliveries.index') }}" class="nd-btn nd-btn-ghost">
            Cancel
        </a>
        <button type="submit" class="nd-btn nd-btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            Save Delivery
        </button>
    </div>
</form>

{{-- ═══════════════════════════════════════════════ --}}
{{-- ITEM TEMPLATE                                   --}}
{{-- ═══════════════════════════════════════════════ --}}
<template id="itemTemplate">
    <div class="nd-item">
        <div class="nd-item-accent"></div>

        <div class="nd-item-head">
            <div class="nd-item-num">
                <span class="nd-item-num-text">ITEM</span>
            </div>
            <button type="button" class="nd-item-remove" onclick="removeItem(this)" title="Remove item">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="nd-item-grid">
            <div class="nd-field nd-item-field-product">
                <label class="nd-label">Product <span class="nd-req">*</span></label>
                <select name="items[__INDEX__][product_id]" class="nd-input product-select" onchange="onProductChange(this)" required>
                    <option value="">-- Select --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->price ?? $p->wholesale_price ?? 0 }}">
                            {{ $p->name }}@if($p->sku) ({{ $p->sku }})@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="nd-field">
                <label class="nd-label">Qty <span class="nd-req">*</span></label>
                <input type="number" name="items[__INDEX__][quantity_delivered]" class="nd-input qty-input" min="1" value="1" required oninput="recalculate()">
            </div>

            <div class="nd-field">
                <label class="nd-label">Unit Price <span class="nd-req">*</span></label>
                <div class="nd-input-prefix">
                    <span class="nd-prefix">&#8369;</span>
                    <input type="number" step="0.01" name="items[__INDEX__][unit_price]" class="nd-input nd-input-with-prefix price-input" min="0" value="0" required oninput="recalculate()">
                </div>
            </div>

            <div class="nd-field">
                <label class="nd-label">Subtotal</label>
                <div class="nd-subtotal" data-subtotal>&#8369;0.00</div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════ */
/* NEW DELIVERY — CLEAN & PREMIUM                  */
/* ═══════════════════════════════════════════════ */

/* ── BACK BUTTON ───────────────────────────────── */
.nd-back{
    display:inline-flex;align-items:center;gap:8px;
    padding:8px 14px 8px 11px;
    background:rgba(34,34,44,.55);
    border:1px solid rgba(255,255,255,.07);
    border-radius:10px;
    color:#a8a5a0;font-size:12.5px;font-weight:600;
    text-decoration:none;
    margin-bottom:16px;
    transition:all .2s cubic-bezier(.4,0,.2,1);
    backdrop-filter:blur(10px);
}
.nd-back:hover{
    background:rgba(201,169,97,.08);
    border-color:rgba(201,169,97,.28);
    color:#c9a961;
    transform:translateX(-3px);
}
.nd-back svg{transition:transform .2s}
.nd-back:hover svg{transform:translateX(-2px)}

/* ── HERO ──────────────────────────────────────── */
.nd-hero{
    position:relative;
    padding:28px 32px;
    background:#16161d;
    border:1px solid rgba(255,255,255,.06);
    border-radius:18px;
    margin-bottom:18px;
    overflow:hidden;
}
.nd-hero-grid{
    position:absolute;inset:0;
    background-image:
        linear-gradient(rgba(201,169,97,.04) 1px,transparent 1px),
        linear-gradient(90deg,rgba(201,169,97,.04) 1px,transparent 1px);
    background-size:40px 40px;
    mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    -webkit-mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    pointer-events:none;
}
.nd-hero-inner{
    position:relative;z-index:1;
    display:flex;align-items:center;justify-content:space-between;
    gap:24px;flex-wrap:wrap;
}
.nd-hero-left{flex:1;min-width:240px}
.nd-hero-eyebrow{
    display:inline-block;
    font-size:10.5px;font-weight:800;
    color:#c9a961;text-transform:uppercase;letter-spacing:.16em;
    margin-bottom:12px;padding-left:14px;position:relative;
}
.nd-hero-eyebrow::before{
    content:'';position:absolute;left:0;top:50%;
    width:6px;height:1px;background:#c9a961;
    transform:translateY(-50%);
}
.nd-hero-title{
    font-size:30px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.035em;line-height:1.1;
    margin:0 0 8px;
}
.nd-hero-sub{
    font-size:13px;color:#8a8792;font-weight:500;
    line-height:1.5;margin:0;max-width:420px;
}
.nd-hero-icon{
    width:60px;height:60px;border-radius:15px;
    display:grid;place-items:center;flex-shrink:0;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.28);
    color:#c9a961;
    box-shadow:0 8px 20px -8px rgba(201,169,97,.4);
}

/* ── CARD ──────────────────────────────────────── */
.nd-card{
    background:rgba(34,34,44,.5);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.06);
    border-radius:16px;
    padding:20px;
    margin-bottom:16px;
}
.nd-card:last-of-type{margin-bottom:0}

.nd-card-head{
    display:flex;align-items:center;gap:12px;
    padding-bottom:16px;margin-bottom:16px;
    border-bottom:1px dashed rgba(255,255,255,.06);
}
.nd-card-icon{
    width:34px;height:34px;border-radius:10px;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.22);
    color:#c9a961;
    display:grid;place-items:center;flex-shrink:0;
}
.nd-card-header-text{flex:1;min-width:0}
.nd-card-title{
    font-size:13.5px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;
}
.nd-card-sub{
    font-size:11px;color:#7a7a85;font-weight:500;
    margin-top:2px;
}

.nd-btn-add{
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 14px;
    background:rgba(201,169,97,.1);
    border:1px solid rgba(201,169,97,.25);
    border-radius:9px;
    color:#c9a961;
    font-size:12px;font-weight:800;
    font-family:inherit;cursor:pointer;
    transition:all .18s;white-space:nowrap;
    flex-shrink:0;
}
.nd-btn-add:hover{
    background:rgba(201,169,97,.18);
    border-color:rgba(201,169,97,.4);
    transform:translateY(-1px);
}

/* ── FORM GRID ─────────────────────────────────── */
.nd-form-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:16px;
}
.nd-field{display:flex;flex-direction:column}
.nd-field-full{grid-column:1 / -1}

.nd-label{
    display:block;font-size:12px;font-weight:700;
    color:#d4d1cc;margin-bottom:8px;
    letter-spacing:-.005em;
}
.nd-req{color:#ef4444;font-weight:800}

.nd-input{
    width:100%;
    padding:11px 14px;
    background:rgba(20,20,26,.6);
    border:1px solid rgba(255,255,255,.08);
    border-radius:10px;
    color:#f5f3f0;
    font-size:13px;font-family:inherit;
    outline:none;transition:all .18s;
    box-sizing:border-box;
    font-weight:500;
}
.nd-input::placeholder{color:#6b6862}
.nd-input:hover{border-color:rgba(255,255,255,.14)}
.nd-input:focus{
    border-color:#c9a961;
    background:rgba(201,169,97,.03);
    box-shadow:0 0 0 3px rgba(201,169,97,.1);
}
.nd-textarea{resize:vertical;min-height:60px;line-height:1.5}

select.nd-input{
    appearance:none;-webkit-appearance:none;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%237a7a85' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
    background-repeat:no-repeat;
    background-position:right 12px center;
    background-size:10px;
    padding-right:32px;
    cursor:pointer;
}

.nd-error{
    font-size:11px;color:#f87171;
    margin-top:5px;font-weight:600;
}

.nd-alert-error{
    padding:12px 16px;
    background:rgba(239,68,68,.08);
    border:1px solid rgba(239,68,68,.25);
    border-radius:10px;
    color:#f87171;
    font-size:12.5px;font-weight:600;
    margin-bottom:14px;
}

/* ── ITEM ROWS ─────────────────────────────────── */
.nd-items-container{
    display:flex;flex-direction:column;gap:12px;
}

.nd-item{
    position:relative;
    background:rgba(20,20,26,.5);
    border:1px solid rgba(255,255,255,.06);
    border-radius:12px;
    padding:16px 18px;
    animation:ndFadeIn .25s cubic-bezier(.4,0,.2,1);
    transition:border-color .18s;
}
.nd-item:hover{border-color:rgba(255,255,255,.1)}
@keyframes ndFadeIn{
    from{opacity:0;transform:translateY(-6px)}
    to{opacity:1;transform:translateY(0)}
}

.nd-item-accent{
    position:absolute;left:0;top:16px;bottom:16px;
    width:3px;border-radius:0 3px 3px 0;
    background:linear-gradient(180deg,#c9a961,rgba(201,169,97,.3));
    opacity:.5;
    transition:opacity .18s;
}
.nd-item:hover .nd-item-accent{opacity:1}

.nd-item-head{
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:14px;
}
.nd-item-num{
    display:inline-flex;align-items:center;
    padding:3px 9px;
    background:rgba(201,169,97,.1);
    border:1px solid rgba(201,169,97,.2);
    border-radius:6px;
}
.nd-item-num-text{
    font-size:9.5px;font-weight:800;color:#c9a961;
    text-transform:uppercase;letter-spacing:.14em;
    font-family:ui-monospace,monospace;
}
.nd-item-remove{
    display:inline-flex;align-items:center;justify-content:center;
    width:30px;height:30px;
    border-radius:8px;
    background:rgba(239,68,68,.08);
    border:1px solid rgba(239,68,68,.2);
    color:#f87171;
    cursor:pointer;transition:all .15s;
    font-family:inherit;padding:0;
}
.nd-item-remove:hover{
    background:rgba(239,68,68,.18);
    border-color:rgba(239,68,68,.4);
    color:#ef4444;
    transform:scale(1.05);
}
.nd-item-remove:active{transform:scale(.95)}

.nd-item-grid{
    display:grid;
    grid-template-columns:minmax(0,2fr) 90px 130px 130px;
    gap:12px;
    align-items:end;
}

/* ── INPUT PREFIX ──────────────────────────────── */
.nd-input-prefix{position:relative;display:flex;align-items:center}
.nd-prefix{
    position:absolute;left:14px;
    color:#7a7a85;font-size:13px;
    font-weight:700;pointer-events:none;
    z-index:1;
}
.nd-input-with-prefix{padding-left:30px}

/* ── SUBTOTAL ──────────────────────────────────── */
.nd-subtotal{
    display:flex;align-items:center;justify-content:flex-end;
    padding:11px 14px;
    background:rgba(201,169,97,.06);
    border:1px solid rgba(201,169,97,.18);
    border-radius:10px;
    color:#c9a961;
    font-weight:800;font-size:13.5px;
    font-variant-numeric:tabular-nums;
    letter-spacing:-.01em;
    height:44px;
    box-sizing:border-box;
}

/* ── TOTALS ────────────────────────────────────── */
.nd-totals{
    margin-top:20px;padding-top:18px;
    border-top:1px dashed rgba(255,255,255,.06);
    display:flex;flex-direction:column;gap:10px;
    align-items:flex-end;
}
.nd-total-row{
    display:flex;justify-content:space-between;align-items:center;
    gap:32px;min-width:280px;
    font-size:13px;color:#9a97a3;
    font-weight:600;
}
.nd-total-lbl{color:#9a97a3}
.nd-total-val{
    color:#f5f3f0;font-weight:800;
    font-variant-numeric:tabular-nums;
}
.nd-total-grand{
    padding-top:14px;margin-top:2px;
    border-top:2px solid rgba(201,169,97,.2);
    min-width:280px;
}
.nd-total-lbl-grand{
    font-size:11.5px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.1em;
}
.nd-total-val-grand{
    font-size:22px;font-weight:800;
    color:#c9a961;letter-spacing:-.02em;
    font-variant-numeric:tabular-nums;
}

/* ── ACTIONS ───────────────────────────────────── */
.nd-actions{
    display:flex;justify-content:flex-end;
    gap:10px;padding-top:8px;
    margin-top:8px;
}
.nd-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:7px;
    padding:12px 22px;border-radius:10px;
    font-size:13px;font-weight:800;
    text-decoration:none;border:1px solid transparent;
    font-family:inherit;cursor:pointer;
    transition:all .18s cubic-bezier(.4,0,.2,1);
    min-height:46px;
    letter-spacing:.01em;
}
.nd-btn-primary{
    background:linear-gradient(135deg,#c9a961,#a9784a);
    color:#0f0f14;
    box-shadow:0 8px 20px -8px rgba(201,169,97,.55);
}
.nd-btn-primary:hover{
    transform:translateY(-1px);
    box-shadow:0 12px 24px -8px rgba(201,169,97,.7);
    filter:brightness(1.05);
}
.nd-btn-primary:active{transform:translateY(0) scale(.98)}

.nd-btn-ghost{
    background:rgba(255,255,255,.04);
    border-color:rgba(255,255,255,.08);
    color:#d4d1cc;
}
.nd-btn-ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:rgba(255,255,255,.15);
    color:#f5f3f0;
}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width:1000px){
    .nd-item-grid{
        grid-template-columns:minmax(0,2fr) 80px 1fr 1fr;
    }
}
@media (max-width:900px){
    .nd-form-grid{grid-template-columns:1fr}
    .nd-item-grid{
        grid-template-columns:1fr 1fr;
        gap:10px;
    }
    .nd-item-field-product{grid-column:1 / -1}
    .nd-item-grid .nd-field:nth-child(3){grid-column:1}
    .nd-item-grid .nd-field:nth-child(4){grid-column:2}
}
@media (max-width:600px){
    .nd-hero{padding:22px}
    .nd-hero-title{font-size:24px}
    .nd-hero-icon{width:50px;height:50px;border-radius:13px}
    .nd-hero-icon svg{width:22px;height:22px}

    .nd-card{padding:16px}
    .nd-card-head{flex-wrap:wrap;gap:10px}
    .nd-btn-add{width:100%;justify-content:center}

    .nd-item{padding:14px}
    .nd-item-grid{grid-template-columns:1fr}
    .nd-item-field-product{grid-column:auto}
    .nd-item-grid .nd-field:nth-child(3){grid-column:auto}
    .nd-item-grid .nd-field:nth-child(4){grid-column:auto}

    .nd-totals{align-items:stretch}
    .nd-total-row{min-width:0;gap:16px}
    .nd-total-grand{min-width:0}

    .nd-actions{flex-direction:column-reverse}
    .nd-btn{width:100%}
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
        updateItemNumbers();
        recalculate();
    }

    function removeItem(btn) {
        const row = btn.closest('.nd-item');
        if (!row) return;
        row.style.opacity = '0';
        row.style.transform = 'translateX(-8px)';
        row.style.transition = 'all .18s';
        setTimeout(function() {
            row.remove();
            updateItemNumbers();
            recalculate();
        }, 150);
    }

    function updateItemNumbers() {
        document.querySelectorAll('.nd-item').forEach(function(row, i) {
            const numEl = row.querySelector('.nd-item-num-text');
            if (numEl) numEl.textContent = 'ITEM ' + String(i + 1).padStart(2, '0');
        });
    }

    function onProductChange(select) {
        const opt = select.options[select.selectedIndex];
        const price = opt.getAttribute('data-price') || 0;
        const row = select.closest('.nd-item');
        row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
        recalculate();
    }

    function recalculate() {
        let total = 0;
        let count = 0;

        document.querySelectorAll('.nd-item').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            row.querySelector('[data-subtotal]').textContent = '\u20B1' + subtotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            total += subtotal;
            count += qty;
        });

        document.getElementById('totalItems').textContent = count;
        document.getElementById('grandTotal').textContent = '\u20B1' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.addEventListener('DOMContentLoaded', () => {
        addItem();
    });
</script>
@endpush