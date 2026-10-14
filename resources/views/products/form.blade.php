@extends('layouts.admin')

@php use Illuminate\Support\Facades\Storage; @endphp
@php
    $productModel = $product ?? new \App\Models\Product();
    $isEdit = isset($product) && $product->exists;
@endphp

@section('title', $isEdit ? 'Edit Product' : 'New Product')
@section('subtitle', $isEdit ? 'Update product details' : 'Add a new product to your catalog')

@section('content')

<form method="POST"
      action="{{ $isEdit ? route('products.update', $product) : route('products.store') }}"
      enctype="multipart/form-data"
      class="prod-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- ═════════ VALIDATION ERRORS ═════════ --}}
    @if($errors->any())
        <div class="pf-errors">
            <div class="pf-errors-head">
                <div class="pf-errors-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="pf-errors-title">{{ $errors->count() }} Error{{ $errors->count() > 1 ? 's' : '' }}</div>
                    <div class="pf-errors-sub">Please fix the following:</div>
                </div>
            </div>
            <ul class="pf-errors-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pf-layout">

        {{-- ═════════ LEFT COLUMN (MAIN) ═════════ --}}
        <div class="pf-main">

            {{-- ═══ ① BASIC INFO ═══ --}}
            <div class="pf-card">
                <div class="pf-card-head">
                    <div class="pf-card-num">1</div>
                    <div>
                        <div class="pf-card-title">Basic Information</div>
                        <div class="pf-card-sub">Product name, SKU, and description</div>
                    </div>
                </div>

                <div class="pf-card-body">
                    {{-- PRODUCT NAME --}}
                    <div class="pf-field pf-field-full">
                        <label class="pf-label" for="productName">
                            Product Name <span class="pf-req">*</span>
                        </label>
                        <input type="text"
                               id="productName"
                               name="name"
                               value="{{ old('name', $productModel->name ?? '') }}"
                               class="pf-input pf-input-lg"
                               placeholder="e.g. Barako Blend 250g"
                               required
                               maxlength="255"
                               autocomplete="off">
                        @error('name')
                            <div class="pf-field-err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pf-row">
                        {{-- SKU --}}
                        <div class="pf-field">
                            <label class="pf-label" for="productSku">
                                SKU
                                <span class="pf-optional">auto-generated kung blangko</span>
                            </label>
                            <input type="text"
                                   id="productSku"
                                   name="sku"
                                   value="{{ old('sku', $productModel->sku ?? '') }}"
                                   class="pf-input mono"
                                   placeholder="CB-XXXXX"
                                   maxlength="100"
                                   autocomplete="off">
                            @error('sku')
                                <div class="pf-field-err">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- UNIT --}}
                        <div class="pf-field">
                            <label class="pf-label" for="productUnit">
                                Unit <span class="pf-req">*</span>
                                <span class="pf-optional">pcs, seedling, tray, etc.</span>
                            </label>
                            <select id="productUnit"
                                    name="unit"
                                    class="pf-input"
                                    required>
                                <option value="" disabled {{ old('unit', $productModel->unit ?? '') ? '' : 'selected' }}>— Pilia ang unit —</option>
                                <optgroup label="Seedlings / Plants">
                                    <option value="seedling" {{ old('unit', $productModel->unit ?? '') == 'seedling' ? 'selected' : '' }}>Seedling (per punoan)</option>
                                    <option value="tray" {{ old('unit', $productModel->unit ?? '') == 'tray' ? 'selected' : '' }}>Tray</option>
                                    <option value="pack" {{ old('unit', $productModel->unit ?? '') == 'pack' ? 'selected' : '' }}>Pack</option>
                                    <option value="bundle" {{ old('unit', $productModel->unit ?? '') == 'bundle' ? 'selected' : '' }}>Bundle</option>
                                </optgroup>
                                <optgroup label="Weight">
                                    <option value="kg" {{ old('unit', $productModel->unit ?? '') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                    <option value="gram" {{ old('unit', $productModel->unit ?? '') == 'gram' ? 'selected' : '' }}>Gram (g)</option>
                                    <option value="sack" {{ old('unit', $productModel->unit ?? '') == 'sack' ? 'selected' : '' }}>Sack</option>
                                    <option value="ton" {{ old('unit', $productModel->unit ?? '') == 'ton' ? 'selected' : '' }}>Ton</option>
                                </optgroup>
                                <optgroup label="Count">
                                    <option value="pc" {{ old('unit', $productModel->unit ?? '') == 'pc' ? 'selected' : '' }}>Piece (pc)</option>
                                    <option value="pcs" {{ old('unit', $productModel->unit ?? '') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                    <option value="dozen" {{ old('unit', $productModel->unit ?? '') == 'dozen' ? 'selected' : '' }}>Dozen</option>
                                </optgroup>
                                <optgroup label="Packaging">
                                    <option value="box" {{ old('unit', $productModel->unit ?? '') == 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="bag" {{ old('unit', $productModel->unit ?? '') == 'bag' ? 'selected' : '' }}>Bag</option>
                                    <option value="bottle" {{ old('unit', $productModel->unit ?? '') == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                    <option value="can" {{ old('unit', $productModel->unit ?? '') == 'can' ? 'selected' : '' }}>Can</option>
                                </optgroup>
                                <optgroup label="Other">
                                    <option value="lot" {{ old('unit', $productModel->unit ?? '') == 'lot' ? 'selected' : '' }}>Lot</option>
                                    <option value="set" {{ old('unit', $productModel->unit ?? '') == 'set' ? 'selected' : '' }}>Set</option>
                                    <option value="unit" {{ old('unit', $productModel->unit ?? '') == 'unit' ? 'selected' : '' }}>Unit</option>
                                    <option value="other" {{ old('unit', $productModel->unit ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                </optgroup>
                            </select>
                            @error('unit')
                                <div class="pf-field-err">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="pf-field pf-field-full">
                        <label class="pf-label" for="productDesc">
                            Description
                            <span class="pf-optional">optional</span>
                        </label>
                        <textarea id="productDesc"
                                  name="description"
                                  rows="3"
                                  class="pf-input pf-textarea"
                                  placeholder="Short description about this product..."
                                  maxlength="500">{{ old('description', $productModel->description ?? '') }}</textarea>
                        @error('description')
                            <div class="pf-field-err">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ═══ ② PRICING ═══ --}}
            <div class="pf-card">
                <div class="pf-card-head">
                    <div class="pf-card-num">2</div>
                    <div>
                        <div class="pf-card-title">Pricing</div>
                        <div class="pf-card-sub">Cost, wholesale, at retail prices</div>
                    </div>
                </div>

                <div class="pf-card-body">
                    <div class="pf-row">
                        {{-- COST PRICE --}}
                        <div class="pf-field">
                            <label class="pf-label" for="productCost">
                                Cost Price
                                <span class="pf-optional">palit sa supplier</span>
                            </label>
                            <div class="pf-input-wrap">
                                <span class="pf-prefix">₱</span>
                                <input type="number"
                                       id="productCost"
                                       name="cost_price"
                                       value="{{ old('cost_price', $productModel->cost_price ?? '') }}"
                                       class="pf-input pf-input-prefixed"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0">
                            </div>
                            @error('cost_price')
                                <div class="pf-field-err">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- RETAIL PRICE --}}
                        <div class="pf-field">
                            <label class="pf-label" for="productPrice">
                                Retail Price <span class="pf-req">*</span>
                                <span class="pf-optional">baligya sa customer</span>
                            </label>
                            <div class="pf-input-wrap">
                                <span class="pf-prefix gold">₱</span>
                                <input type="number"
                                       id="productPrice"
                                       name="price"
                                       value="{{ old('price', $productModel->price ?? '') }}"
                                       class="pf-input pf-input-prefixed pf-input-highlight"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>
                            @error('price')
                                <div class="pf-field-err">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- WHOLESALE --}}
                    <div class="pf-field pf-field-full">
                        <label class="pf-label" for="productWholesale">
                            Wholesale Price
                            <span class="pf-optional">para sa resellers (bulk buyers)</span>
                        </label>
                        <div class="pf-input-wrap">
                            <span class="pf-prefix blue">₱</span>
                            <input type="number"
                                   id="productWholesale"
                                   name="wholesale_price"
                                   value="{{ old('wholesale_price', $productModel->wholesale_price ?? '') }}"
                                   class="pf-input pf-input-prefixed"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0">
                        </div>
                        @error('wholesale_price')
                            <div class="pf-field-err">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PROFIT PANEL --}}
                    <div class="pf-profit-panel" id="profitPanel">
                        <div class="pf-profit-head">
                            <div class="pf-profit-head-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                                </svg>
                            </div>
                            <span>Profit Computation</span>
                            <div class="pf-profit-hint" id="profitHint"></div>
                        </div>

                        <div class="pf-profit-grid">
                            <div class="pf-profit-cell">
                                <div class="pf-profit-lbl">Cost</div>
                                <div class="pf-profit-val" id="pfCost">₱0.00</div>
                            </div>
                            <div class="pf-profit-cell">
                                <div class="pf-profit-lbl">Retail</div>
                                <div class="pf-profit-val gold" id="pfRetail">₱0.00</div>
                            </div>
                            <div class="pf-profit-cell highlight" id="pfProfitCell">
                                <div class="pf-profit-lbl">Profit / Unit</div>
                                <div class="pf-profit-val green" id="pfProfit">+₱0.00</div>
                            </div>
                            <div class="pf-profit-cell">
                                <div class="pf-profit-lbl">Margin %</div>
                                <div class="pf-profit-val" id="pfMargin">0%</div>
                            </div>
                        </div>

                        <div class="pf-profit-bar">
                            <div class="pf-profit-bar-fill" id="pfBarFill"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ ③ INVENTORY ═══ --}}
            <div class="pf-card">
                <div class="pf-card-head">
                    <div class="pf-card-num">3</div>
                    <div>
                        <div class="pf-card-title">Inventory</div>
                        <div class="pf-card-sub">Stock tracking and low-stock alerts</div>
                    </div>
                </div>

                <div class="pf-card-body">
                    <div class="pf-row">
                        {{-- STOCK --}}
                        <div class="pf-field">
                            <label class="pf-label" for="productStock">
                                Stock Quantity <span class="pf-req">*</span>
                            </label>
                            <input type="number"
                                   id="productStock"
                                   name="stock"
                                   value="{{ old('stock', $productModel->stock ?? 0) }}"
                                   class="pf-input"
                                   placeholder="0"
                                   min="0"
                                   required>
                            @error('stock')
                                <div class="pf-field-err">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- REORDER LEVEL --}}
                        <div class="pf-field">
                            <label class="pf-label" for="productReorder">
                                Reorder Level
                                <span class="pf-optional">alert threshold</span>
                            </label>
                            <input type="number"
                                   id="productReorder"
                                   name="reorder_level"
                                   value="{{ old('reorder_level', $productModel->reorder_level ?? 10) }}"
                                   class="pf-input"
                                   placeholder="10"
                                   min="0">
                            <div class="pf-field-hint">Alert kung mo-drop na ang stock niining numero</div>
                            @error('reorder_level')
                                <div class="pf-field-err">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ ④ STATUS ═══ --}}
            <div class="pf-card">
                <div class="pf-card-head">
                    <div class="pf-card-num">4</div>
                    <div>
                        <div class="pf-card-title">Status</div>
                        <div class="pf-card-sub">Active or inactive</div>
                    </div>
                </div>

                <div class="pf-card-body">
                    <label class="pf-toggle">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $productModel->is_active ?? true) ? 'checked' : '' }}>
                        <span class="pf-toggle-track">
                            <span class="pf-toggle-thumb"></span>
                        </span>
                        <span class="pf-toggle-text">
                            <strong>Product is active</strong>
                            <em>Makita ni sa product list ug sa portal</em>
                        </span>
                    </label>
                </div>
            </div>

        </div>

        {{-- ═════════ RIGHT COLUMN (SIDEBAR) ═════════ --}}
        <aside class="pf-sidebar">

            {{-- ═══ PRODUCT IMAGE ═══ --}}
            <div class="pf-card">
                <div class="pf-card-head">
                    <div class="pf-card-num">5</div>
                    <div>
                        <div class="pf-card-title">Product Image</div>
                        <div class="pf-card-sub">JPG, PNG, or WebP · Max 2MB</div>
                    </div>
                </div>

                <div class="pf-card-body">
                    <div class="pf-image-upload">
                        <input type="file"
                               id="productImageInput"
                               name="image"
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="pfPreviewImage(event)"
                               style="display:none;">

                        <div class="pf-image-drop" id="pfImageDrop" onclick="document.getElementById('productImageInput').click();">
                            <div class="pf-image-drop-icon">
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                    <path d="M17 8l-5-5-5 5M12 3v12"/>
                                </svg>
                            </div>
                            <div class="pf-image-drop-title">Click to upload</div>
                            <div class="pf-image-drop-sub">or drag and drop</div>
                        </div>

                        <div class="pf-image-preview" id="pfImagePreview" style="display:none;">
                            <img id="pfImagePreviewImg" alt="Preview">
                            <button type="button" class="pf-image-remove" onclick="pfRemoveImage(event)" title="Remove">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        @if($isEdit && !empty($productModel->getRawOriginal('image')))
                            <div class="pf-image-current">
                                <div class="pf-image-current-lbl">Current image</div>
                                <img src="{{ $productModel->image_url }}" alt="Current" class="pf-image-current-thumb">
                            </div>
                        @endif
                    </div>
                    @error('image')
                        <div class="pf-field-err" style="margin-top:8px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ═══ SAVE ACTIONS ═══ --}}
            <div class="pf-actions-card">
                <button type="submit" class="pf-btn pf-btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12l5 5L20 7"/>
                    </svg>
                    {{ $isEdit ? 'Update Product' : 'Save Product' }}
                </button>
                <a href="{{ route('products.index') }}" class="pf-btn pf-btn-ghost">
                    Cancel
                </a>
            </div>

            {{-- ═══ TIPS ═══ --}}
            <div class="pf-tips">
                <div class="pf-tips-head">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                    Quick Tips
                </div>
                <ul>
                    <li>SKU auto-generate kung blangko</li>
                    <li>Cost + Retail = profit computation</li>
                    <li>Wholesale = para sa resellers</li>
                    <li>Reorder level = low stock alert</li>
                </ul>
            </div>
        </aside>

    </div>
</form>

@endsection

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════
       PRODUCT FORM — PRO REDESIGN
       ═══════════════════════════════════════════════════════ */

    .prod-form { max-width: 1400px; }

    .pf-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    .pf-main { display: flex; flex-direction: column; gap: 16px; }

    /* ═══ ERRORS ═══ */
    .pf-errors {
        padding: 18px 20px;
        margin-bottom: 20px;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(239, 68, 68, 0.03));
        border: 1px solid rgba(239, 68, 68, 0.35);
        border-left: 4px solid #ef4444;
        border-radius: 14px;
        animation: pfErrSlide 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes pfErrSlide {
        from { opacity: 0; transform: translateY(-12px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .pf-errors-head { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .pf-errors-icon {
        width: 40px; height: 40px; border-radius: 11px;
        background: rgba(239, 68, 68, 0.18);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #ef4444;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .pf-errors-title { font-size: 14px; font-weight: 800; color: #fca5a5; }
    .pf-errors-sub { font-size: 11.5px; color: #a1a1aa; margin-top: 2px; }
    .pf-errors-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
    .pf-errors-list li {
        position: relative; padding: 8px 12px 8px 32px;
        background: rgba(0, 0, 0, 0.2); border-radius: 8px;
        font-size: 12.5px; color: #fca5a5; line-height: 1.5; font-weight: 600;
    }
    .pf-errors-list li::before {
        content: '!'; position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%);
        width: 16px; height: 16px; border-radius: 50%;
        background: rgba(239, 68, 68, 0.3); color: #fca5a5;
        font-size: 10px; font-weight: 800;
        display: grid; place-items: center;
    }

    /* ═══ CARD ═══ */
    .pf-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        overflow: hidden;
    }

    .pf-card-head {
        display: flex; align-items: center; gap: 14px;
        padding: 18px 22px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(0, 0, 0, 0.15);
    }
    .pf-card-num {
        width: 32px; height: 32px; border-radius: 9px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.18), rgba(201, 169, 97, 0.08));
        border: 1px solid rgba(201, 169, 97, 0.3);
        color: #c9a961;
        display: grid; place-items: center; flex-shrink: 0;
        font-size: 13px; font-weight: 800;
        font-family: ui-monospace, monospace;
    }
    .pf-card-title { font-size: 14px; font-weight: 800; color: #fafafa; letter-spacing: -0.01em; }
    .pf-card-sub { font-size: 11px; color: #71717a; margin-top: 2px; }

    .pf-card-body { padding: 22px; }

    /* ═══ ROW / FIELD ═══ */
    .pf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .pf-row:last-child { margin-bottom: 0; }

    .pf-field { display: flex; flex-direction: column; gap: 7px; }
    .pf-field-full { grid-column: span 2; }

    .pf-label {
        display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap;
        font-size: 11.5px; font-weight: 800; color: #d4d4d8;
        text-transform: uppercase; letter-spacing: 0.06em;
    }
    .pf-req { color: #ef4444; font-weight: 900; }
    .pf-optional {
        font-size: 9.5px; font-weight: 600;
        color: #52525b;
        text-transform: none;
        letter-spacing: 0;
        padding: 1px 6px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 100px;
    }

    .pf-input-wrap { position: relative; }
    .pf-prefix {
        position: absolute;
        left: 14px; top: 50%;
        transform: translateY(-50%);
        font-size: 14px; font-weight: 800;
        color: #71717a;
        pointer-events: none;
        z-index: 1;
    }
    .pf-prefix.gold { color: #c9a961; }
    .pf-prefix.blue { color: #3b82f6; }

    .pf-input {
        width: 100%;
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa;
        font-size: 13.5px;
        font-family: inherit;
        transition: all 0.18s;
    }
    .pf-input-lg { font-size: 15px; padding: 14px 16px; }
    .pf-input-prefixed { padding-left: 36px; }
    .pf-input-highlight { border-color: rgba(201, 169, 97, 0.25); background: rgba(201, 169, 97, 0.03); }
    .pf-input.mono { font-family: ui-monospace, monospace; letter-spacing: 0.02em; }
    .pf-input:focus {
        outline: none;
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
        background: rgba(201, 169, 97, 0.03);
    }
    .pf-input::placeholder { color: #52525b; }

    select.pf-input {
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23c9a961' stroke-width='2.5' stroke-linecap='round'%3e%3cpath d='M6 9l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
        padding-right: 40px;
    }
    select.pf-input option,
    select.pf-input optgroup {
        background: #1E1A16;
        color: #fafafa;
    }
    select.pf-input optgroup {
        color: #c9a961;
        font-weight: 800;
        font-style: normal;
    }

    .pf-textarea { resize: vertical; min-height: 80px; font-family: inherit; line-height: 1.55; }

    .pf-field-err {
        font-size: 11.5px; font-weight: 600;
        color: #f87171;
        display: flex; align-items: center; gap: 5px;
    }
    .pf-field-err::before { content: '⚠'; }
    .pf-field-hint { font-size: 11px; color: #71717a; margin-top: 4px; }

    /* ═══ PROFIT PANEL ═══ */
    .pf-profit-panel {
        margin-top: 20px;
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(34, 197, 94, 0.05), rgba(201, 169, 97, 0.03));
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 14px;
        position: relative;
        overflow: hidden;
    }
    .pf-profit-panel::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.4), transparent);
    }

    .pf-profit-head { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .pf-profit-head-icon {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        display: grid; place-items: center;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .pf-profit-head span {
        font-size: 11px; font-weight: 800; color: #22c55e;
        text-transform: uppercase; letter-spacing: 0.1em;
    }
    .pf-profit-hint { margin-left: auto; font-size: 10.5px; font-weight: 700; color: #71717a; }
    .pf-profit-hint.good { color: #22c55e; }
    .pf-profit-hint.low { color: #f59e0b; }
    .pf-profit-hint.bad { color: #ef4444; }

    .pf-profit-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 12px; }
    .pf-profit-cell {
        padding: 10px 12px;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }
    .pf-profit-cell.highlight {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(34, 197, 94, 0.04));
        border-color: rgba(34, 197, 94, 0.35);
    }
    .pf-profit-cell.highlight.loss {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.04));
        border-color: rgba(239, 68, 68, 0.35);
    }
    .pf-profit-lbl {
        font-size: 9px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 5px;
    }
    .pf-profit-val {
        font-size: 14px; font-weight: 800; color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .pf-profit-val.gold { color: #c9a961; }
    .pf-profit-val.green { color: #22c55e; }
    .pf-profit-val.red { color: #ef4444; }

    .pf-profit-bar { height: 6px; background: rgba(255, 255, 255, 0.05); border-radius: 3px; overflow: hidden; }
    .pf-profit-bar-fill {
        height: 100%; width: 0%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border-radius: 3px;
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1), background 0.2s;
        min-width: 2px;
    }
    .pf-profit-bar-fill.loss { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .pf-profit-bar-fill.low { background: linear-gradient(90deg, #f59e0b, #d97706); }

    /* ═══ IMAGE UPLOAD ═══ */
    .pf-image-upload { position: relative; }

    .pf-image-drop {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; padding: 32px 20px;
        background: rgba(255, 255, 255, 0.02);
        border: 2px dashed rgba(201, 169, 97, 0.3);
        border-radius: 14px;
        color: #71717a;
        cursor: pointer;
        transition: all 0.18s;
    }
    .pf-image-drop:hover {
        background: rgba(201, 169, 97, 0.05);
        border-color: rgba(201, 169, 97, 0.6);
        color: #c9a961;
    }
    .pf-image-drop-icon { color: rgba(201, 169, 97, 0.5); }
    .pf-image-drop:hover .pf-image-drop-icon { color: #c9a961; }
    .pf-image-drop-title { font-size: 13px; font-weight: 700; color: #d4d4d8; }
    .pf-image-drop-sub { font-size: 11px; color: #71717a; }

    .pf-image-preview {
        position: relative; border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(201, 169, 97, 0.3);
    }
    .pf-image-preview img { width: 100%; max-height: 280px; object-fit: cover; display: block; }
    .pf-image-remove {
        position: absolute; top: 12px; right: 12px;
        width: 34px; height: 34px; border-radius: 50%;
        background: rgba(239, 68, 68, 0.9); border: none;
        color: #fff; display: grid; place-items: center;
        cursor: pointer; transition: all 0.15s;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    }
    .pf-image-remove:hover { background: #ef4444; transform: scale(1.1); }

    .pf-image-current {
        margin-top: 14px; padding: 12px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .pf-image-current-lbl {
        font-size: 10px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.1em;
        margin-bottom: 8px;
    }
    .pf-image-current-thumb {
        width: 72px; height: 72px; border-radius: 10px;
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: block;
    }

    /* ═══ TOGGLE ═══ */
    .pf-toggle {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        cursor: pointer;
        user-select: none;
    }
    .pf-toggle input[type="checkbox"] { position: absolute; opacity: 0; pointer-events: none; }
    .pf-toggle-track {
        position: relative; width: 48px; height: 26px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 100px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .pf-toggle-thumb {
        position: absolute; top: 3px; left: 3px;
        width: 20px; height: 20px; border-radius: 50%;
        background: #a1a1aa;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pf-toggle input:checked + .pf-toggle-track {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    }
    .pf-toggle input:checked + .pf-toggle-track .pf-toggle-thumb {
        transform: translateX(22px);
        background: #fff;
    }
    .pf-toggle-text strong { display: block; font-size: 13px; font-weight: 800; color: #fafafa; margin-bottom: 2px; }
    .pf-toggle-text em { font-style: normal; font-size: 11px; color: #71717a; }

    /* ═══ SIDEBAR ═══ */
    .pf-sidebar { display: flex; flex-direction: column; gap: 14px; position: sticky; top: 90px; }

    .pf-actions-card {
        display: flex; flex-direction: column; gap: 10px;
        padding: 16px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
    }
    .pf-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 8px; padding: 13px 20px;
        border-radius: 12px;
        font-size: 13px; font-weight: 800;
        text-decoration: none; cursor: pointer;
        border: 1px solid; font-family: inherit;
        transition: all 0.18s;
    }
    .pf-btn-primary {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-color: transparent; color: #0f0f14;
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.6);
    }
    .pf-btn-primary:hover {
        background: linear-gradient(135deg, #d4b672, #c9a961);
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(201, 169, 97, 0.7);
    }
    .pf-btn-primary:active { transform: scale(0.98); }
    .pf-btn-ghost {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.1);
        color: #d4d4d8;
    }
    .pf-btn-ghost:hover { background: rgba(255, 255, 255, 0.08); color: #fafafa; }

    .pf-tips {
        padding: 14px 16px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.7), rgba(21, 18, 15, 0.7));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
    }
    .pf-tips-head {
        display: flex; align-items: center; gap: 7px;
        font-size: 10px; font-weight: 800;
        color: #c9a961;
        text-transform: uppercase; letter-spacing: 0.12em;
        margin-bottom: 10px;
    }
    .pf-tips ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; }
    .pf-tips li {
        position: relative; padding-left: 16px;
        font-size: 11px; color: #a1a1aa; line-height: 1.5;
    }
    .pf-tips li::before {
        content: '·'; position: absolute; left: 4px;
        color: #c9a961; font-weight: 900; font-size: 14px;
    }

    /* ═══ RESPONSIVE ═══ */
    @media (max-width: 1100px) {
        .pf-layout { grid-template-columns: 1fr; }
        .pf-sidebar { position: static; }
    }
    @media (max-width: 700px) {
        .pf-row { grid-template-columns: 1fr; gap: 14px; }
        .pf-field-full { grid-column: span 1; }
        .pf-card-body { padding: 18px; }
        .pf-card-head { padding: 14px 18px; }
        .pf-profit-grid { grid-template-columns: repeat(2, 1fr); }
        .pf-image-drop { padding: 24px 16px; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    function fmt(n) {
        return '₱' + Number(n).toLocaleString('en-PH', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }

    // ═══ PROFIT LIVE PREVIEW ═══
    function updateProfit() {
        var costEl = document.getElementById('productCost');
        var priceEl = document.getElementById('productPrice');
        if (!costEl || !priceEl) return;

        var cost = parseFloat(costEl.value) || 0;
        var price = parseFloat(priceEl.value) || 0;
        var profit = price - cost;
        var margin = price > 0 ? (profit / price) * 100 : 0;

        document.getElementById('pfCost').textContent = fmt(cost);
        document.getElementById('pfRetail').textContent = fmt(price);
        document.getElementById('pfProfit').textContent = (profit >= 0 ? '+' : '') + fmt(profit);
        document.getElementById('pfMargin').textContent = margin.toFixed(1) + '%';

        var cell = document.getElementById('pfProfitCell');
        var bar = document.getElementById('pfBarFill');
        var hint = document.getElementById('profitHint');
        var profitVal = document.getElementById('pfProfit');

        cell.classList.remove('loss');
        bar.classList.remove('loss', 'low');
        profitVal.classList.remove('red', 'green');
        hint.className = 'pf-profit-hint';

        if (profit < 0) {
            cell.classList.add('loss');
            bar.classList.add('loss');
            profitVal.classList.add('red');
            hint.classList.add('bad');
            hint.textContent = 'LOSS — below cost!';
        } else if (cost > 0 && profit === 0) {
            hint.classList.add('low');
            hint.textContent = 'Walay profit';
        } else if (margin >= 50) {
            profitVal.classList.add('green');
            hint.classList.add('good');
            hint.textContent = 'Excellent margin!';
        } else if (margin >= 20) {
            profitVal.classList.add('green');
            hint.classList.add('good');
            hint.textContent = 'Healthy margin';
        } else if (margin > 0) {
            bar.classList.add('low');
            hint.classList.add('low');
            hint.textContent = 'Low margin';
        }

        bar.style.width = Math.min(Math.abs(margin), 100) + '%';
    }

    // ═══ LIVE PREVIEW NAME + PRICE ═══
    function updatePreview() {
        var nameEl = document.getElementById('productName');
        var priceEl = document.getElementById('productPrice');
        var skuEl = document.getElementById('productSku');

        var namePreview = document.getElementById('pfPreviewName');
        var pricePreview = document.getElementById('pfPreviewPrice');
        var skuPreview = document.querySelector('.pf-preview-sku');

        if (nameEl && namePreview) namePreview.textContent = nameEl.value || 'Product Name';
        if (skuEl && skuPreview) skuPreview.textContent = skuEl.value || 'CB-XXXXX';
        if (priceEl && pricePreview) {
            var p = parseFloat(priceEl.value) || 0;
            pricePreview.textContent = p.toLocaleString('en-PH', {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });
        }
    }

    // ═══ IMAGE PREVIEW ═══
    window.pfPreviewImage = function(event) {
        var file = event.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            alert('Image too large. Max 2MB.');
            event.target.value = '';
            return;
        }
        var allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!allowed.includes(file.type)) {
            alert('Invalid format. Use JPG, PNG, or WebP.');
            event.target.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('pfImageDrop').style.display = 'none';
            document.getElementById('pfImagePreview').style.display = 'block';
            document.getElementById('pfImagePreviewImg').src = e.target.result;
        };
        reader.readAsDataURL(file);
    };

    window.pfRemoveImage = function(event) {
        event.stopPropagation();
        document.getElementById('productImageInput').value = '';
        document.getElementById('pfImagePreview').style.display = 'none';
        document.getElementById('pfImageDrop').style.display = 'flex';
    };

    // ═══ ATTACH LISTENERS ═══
    document.addEventListener('DOMContentLoaded', function() {
        var fields = ['productCost', 'productPrice', 'productName', 'productSku'];
        fields.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('input', function() {
                updateProfit();
                updatePreview();
            });
        });

        // Initial
        updateProfit();
        updatePreview();
    });
})();
</script>
@endpush