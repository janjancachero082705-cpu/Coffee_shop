@extends('layouts.admin')

@section('title', $product->exists ? 'Edit Product' : 'New Product')
@section('subtitle', $product->exists ? $product->name : 'Add a new product')

@section('actions')
    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>
@endsection

@section('content')

@php
    $isEdit = $product->exists;
@endphp

<form method="POST" action="{{ $isEdit ? route('products.update', $product) : route('products.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-layout">

        {{-- LEFT: MAIN FORM --}}
        <div class="form-main">

            {{-- IMAGE UPLOAD --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon gold">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </div>
                    <div>
                        <div class="card-head-title">Product Image</div>
                        <div class="card-head-sub">Upload a JPG, PNG or WebP (max 2MB)</div>
                    </div>
                </div>

                <div class="image-upload">
                    <div class="image-preview" id="imagePreview">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="Product image" id="previewImg">
                        @else
                            <div class="preview-placeholder" id="previewPlaceholder">
                                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <path d="M21 15l-5-5L5 21"/>
                                </svg>
                                <div class="preview-text">No image</div>
                            </div>
                        @endif
                    </div>

                    <div class="image-actions">
                        <label for="imageInput" class="btn-upload">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Choose Image
                        </label>
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;" onchange="previewImage(event)">
                        <span class="image-hint">or drag and drop</span>
                    </div>

                    @error('image')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- BASIC INFO --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div class="card-head-title">Basic Information</div>
                        <div class="card-head-sub">Product name, SKU, and category</div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field field-full">
                        <label class="label">Product Name <span class="req">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="input" placeholder="e.g. Coffee House Instant 30g" required>
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label class="label">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="input" placeholder="e.g. CB-00001">
                        @error('sku')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label class="label">Category</label>
                        <select name="category_id" class="input">
                            <option value="">- Select -</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field field-full">
                        <label class="label">Description</label>
                        <textarea name="description" rows="3" class="input" placeholder="Optional description...">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- PRICING & STOCK --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon green">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <div class="card-head-title">Pricing & Stock</div>
                        <div class="card-head-sub">Prices and inventory levels</div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label class="label">Selling Price <span class="req">*</span></label>
                        <div class="input-prefix">
                            <span class="prefix">&#8369;</span>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="input input-with-prefix" placeholder="0.00" required>
                        </div>
                        @error('price')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label class="label">Cost Price</label>
                        <div class="input-prefix">
                            <span class="prefix">&#8369;</span>
                            <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="input input-with-prefix" placeholder="0.00">
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Wholesale Price</label>
                        <div class="input-prefix">
                            <span class="prefix">&#8369;</span>
                            <input type="number" step="0.01" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" class="input input-with-prefix" placeholder="0.00">
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Unit</label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" class="input" placeholder="e.g. 30g, 1kg, pcs">
                    </div>

                    <div class="field">
                        <label class="label">Stock Quantity <span class="req">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="input" min="0" required>
                        @error('stock')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label class="label">Reorder Level</label>
                        <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level ?? 10) }}" class="input" min="0">
                        <div class="hint">Alert when stock drops below this number</div>
                    </div>
                </div>
            </div>

            {{-- STATUS --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon blue">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div>
                        <div class="card-head-title">Status</div>
                        <div class="card-head-sub">Active or inactive</div>
                    </div>
                </div>

                <label class="toggle-wrap">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                    <span class="toggle"></span>
                    <span class="toggle-text">Product is active</span>
                </label>
            </div>

            {{-- ACTIONS --}}
            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    {{ $isEdit ? 'Update Product' : 'Save Product' }}
                </button>
            </div>

        </div>

        {{-- RIGHT: PREVIEW --}}
        <div class="preview-panel">
            <div class="preview-card">
                <div class="preview-head">Preview</div>
                <div class="preview-image-wrap" id="livePreview">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="Preview" id="liveImg">
                    @else
                        <div class="live-placeholder" id="livePlaceholder">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="preview-info">
                    <div class="preview-name" id="previewName">{{ $product->name ?? 'Product Name' }}</div>
                    <div class="preview-price" id="previewPrice">&#8369;0.00</div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('styles')
<style>
    .form-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }
    .form-main { min-width: 0; }

    .card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .card-head-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        color: #c9a961;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .card-head-icon.gold  { background: rgba(169, 120, 74, 0.1); border-color: rgba(169, 120, 74, 0.2); color: #c9a961; }
    .card-head-icon.green { background: rgba(34, 197, 94, 0.1);  border-color: rgba(34, 197, 94, 0.2);  color: #22c55e; }
    .card-head-icon.blue  { background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .card-head-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .card-head-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .field-full { grid-column: 1 / -1; }
    .field { display: flex; flex-direction: column; }
    .req { color: #ef4444; font-weight: 700; }
    .field-error { font-size: 11px; color: #ef4444; margin-top: 5px; font-weight: 500; }
    .hint { font-size: 11px; color: var(--text-muted); margin-top: 5px; }

    .input-prefix { position: relative; display: flex; align-items: center; }
    .prefix {
        position: absolute; left: 12px;
        color: var(--text-muted); font-size: 13px;
        font-weight: 600; pointer-events: none;
    }
    .input-with-prefix { padding-left: 28px; }

    /* IMAGE UPLOAD */
    .image-upload { display: flex; gap: 20px; align-items: flex-start; }
    .image-preview {
        width: 160px;
        height: 160px;
        border-radius: 12px;
        overflow: hidden;
        background: rgba(20, 20, 26, 0.6);
        border: 2px dashed rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
        display: grid;
        place-items: center;
    }
    .image-preview img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        opacity: 0.5;
    }
    .preview-text { font-size: 11px; font-weight: 600; }

    .image-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-top: 8px;
    }
    .btn-upload {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 8px;
        color: #c9a961;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-upload:hover {
        background: rgba(169, 120, 74, 0.2);
    }
    .image-hint { font-size: 11px; color: var(--text-muted); }

    /* TOGGLE */
    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 12px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 10px;
        user-select: none;
    }
    .toggle-wrap input { display: none; }
    .toggle {
        width: 44px;
        height: 24px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        position: relative;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #fff;
        transition: all 0.2s;
    }
    .toggle-wrap input:checked + .toggle {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }
    .toggle-wrap input:checked + .toggle::after {
        left: 23px;
    }
    .toggle-text {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 8px;
    }

    /* PREVIEW PANEL */
    .preview-panel {
        position: sticky;
        top: 90px;
    }
    .preview-card {
        background: rgba(34, 34, 44, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(169, 120, 74, 0.2);
        border-radius: 16px;
        padding: 20px;
    }
    .preview-head {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 16px;
    }
    .preview-image-wrap {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.5));
        display: grid;
        place-items: center;
        margin-bottom: 16px;
    }
    .preview-image-wrap img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .live-placeholder {
        color: rgba(201, 169, 97, 0.4);
    }
    .preview-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
        text-align: center;
        min-height: 20px;
    }
    .preview-price {
        font-size: 22px;
        font-weight: 800;
        color: #c9a961;
        text-align: center;
        letter-spacing: -0.02em;
    }

    @media (max-width: 1000px) {
        .form-layout { grid-template-columns: 1fr; }
        .preview-panel { position: static; }
    }
    @media (max-width: 700px) {
        .form-grid { grid-template-columns: 1fr; }
        .image-upload { flex-direction: column; }
    }
</style>
@endpush

@push('scripts')
<script>
    // Image preview
    function previewImage(event) {
        var file = event.target.files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('imagePreview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';

            var live = document.getElementById('livePreview');
            if (live) {
                live.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
            }
        };
        reader.readAsDataURL(file);
    }

    // Live name + price update
    document.addEventListener('DOMContentLoaded', function() {
        var nameInput = document.querySelector('input[name="name"]');
        var priceInput = document.querySelector('input[name="price"]');
        var previewName = document.getElementById('previewName');
        var previewPrice = document.getElementById('previewPrice');

        if (nameInput && previewName) {
            nameInput.addEventListener('input', function() {
                previewName.textContent = this.value || 'Product Name';
            });
        }

        if (priceInput && previewPrice) {
            priceInput.addEventListener('input', function() {
                var val = parseFloat(this.value) || 0;
                previewPrice.innerHTML = '&#8369;' + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            });
        }

        // Init preview price
        if (priceInput && previewPrice) {
            var val = parseFloat(priceInput.value) || 0;
            if (val > 0) {
                previewPrice.innerHTML = '&#8369;' + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }
    });
</script>
@endpush