@extends('layouts.admin')

@section('title', isset($product) ? 'Edit Product' : 'New Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Add New Product')
@section('page-sub', isset($product) ? 'Update product details' : 'Add new item to your catalog')

@push('styles')
<style>
    .form-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 16px;
        align-items: start;
    }
    @media (max-width: 1100px) { .form-layout { grid-template-columns: 1fr; } }

    .form-section {
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border);
    }
    .form-section:last-child { padding-bottom: 0; margin-bottom: 0; border-bottom: none; }

    .section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .section-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--accent-bg);
        border: 1px solid var(--accent-border);
        display: grid;
        place-items: center;
        font-size: 11px;
        font-weight: 800;
        color: var(--accent);
        flex-shrink: 0;
    }
    .section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .section-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .form-field { margin-bottom: 14px; }
    .form-field:last-child { margin-bottom: 0; }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
    }
    @media (max-width: 600px) {
        .form-row, .form-row-3 { grid-template-columns: 1fr; }
    }

    .input, select, textarea {
        width: 100%;
        padding: 10px 12px;
        background: var(--bg-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all .12s;
    }
    .input:focus, select:focus, textarea:focus {
        border-color: var(--accent);
        background: var(--bg-1);
        box-shadow: 0 0 0 3px rgba(201,169,97,.1);
    }

    /* Image upload */
    .image-upload {
        position: relative;
        border: 2px dashed var(--border-strong);
        border-radius: var(--radius);
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all .15s ease;
        background: var(--bg-2);
        min-height: 220px;
        display: grid;
        place-items: center;
        overflow: hidden;
    }
    .image-upload:hover { border-color: var(--accent); background: var(--bg-1); }
    .image-upload.has-image { padding: 0; border-style: solid; border-color: var(--accent); }
    .image-upload input[type=file] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .image-preview {
        width: 100%;
        max-height: 320px;
        object-fit: cover;
        display: block;
    }
    .upload-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: var(--accent-bg);
        border: 1px solid var(--accent-border);
        display: grid;
        place-items: center;
        margin: 0 auto 12px;
        font-size: 24px;
        color: var(--accent);
    }
    .upload-title { font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .upload-hint { font-size: 11px; color: var(--text-muted); }

    .image-actions { display: flex; gap: 8px; margin-top: 10px; }
    .image-remove {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: var(--radius-sm);
        background: var(--red-bg);
        color: var(--red);
        font-size: 11px;
        font-weight: 600;
        border: 1px solid rgba(248,113,113,.2);
        cursor: pointer;
        font-family: inherit;
    }
    .image-remove:hover { background: rgba(248,113,113,.15); }

    /* Toggle */
    .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        background: var(--bg-2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        cursor: pointer;
        margin-bottom: 8px;
    }
    .toggle-row:last-child { margin-bottom: 0; }
    .toggle-row .info { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .toggle-row .info small { display: block; font-size: 11px; color: var(--text-muted); font-weight: 400; margin-top: 2px; }
    .toggle-switch { position: relative; width: 42px; height: 24px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute;
        inset: 0;
        background: var(--border-strong);
        border-radius: 999px;
        cursor: pointer;
        transition: .2s;
    }
    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
    }
    .toggle-switch input:checked + .toggle-slider { background: var(--accent); }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(18px); }

    /* Profit preview */
    .profit-box {
        padding: 12px 14px;
        background: var(--green-bg);
        border: 1px solid rgba(74,222,128,.2);
        border-radius: var(--radius);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
    }
    .profit-box .label-sm { font-size: 11px; color: var(--green); text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }
    .profit-box .value { font-size: 18px; font-weight: 800; color: var(--green); }

    .error-msg { color: var(--red); font-size: 11px; margin-top: 4px; }
</style>
@endpush

@section('content')

@if($errors->any())
    <div class="alert alert-error">
        <div>
            @foreach($errors->all() as $error)
                <div>âš  {{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<form method="POST"
      action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}"
      enctype="multipart/form-data"
      id="productForm">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    <div class="form-layout">

        {{-- LEFT: MAIN FORM --}}
        <div>
            {{-- Section 1: Basic Info --}}
            <div class="card">
                <div class="form-section">
                    <div class="section-head">
                        <div class="section-num">1</div>
                        <div>
                            <div class="section-title">Basic Information</div>
                            <div class="section-sub">Product name, category, and identifier</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label class="label">Product Name *</label>
                            <input type="text" name="name" class="input"
                                   value="{{ old('name', $product->name ?? '') }}"
                                   placeholder="e.g. Arabica Roasted" required>
                        </div>
                        <div class="form-field">
                            <label class="label">SKU / Product Code</label>
                            <input type="text" name="sku" class="input"
                                   value="{{ old('sku', $product->sku ?? '') }}"
                                   placeholder="Auto-generated if empty"
                                   style="font-family: monospace;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label class="label">Category *</label>
                            <select name="category_id" class="input" required>
                                <option value="">â€” Select â€”</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                                        {{ $cat->emoji ?? 'â˜•' }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="label">Variety</label>
                            <select name="variety" class="input">
                                <option value="">â€” Select â€”</option>
                                @foreach(['Arabica', 'Robusta', 'Liberica', 'Excelsa', 'Mixed', 'Blend'] as $v)
                                    <option value="{{ $v }}" @selected(old('variety', $product->variety ?? '') == $v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="label">Description</label>
                        <textarea name="description" rows="3" class="input"
                                  placeholder="Short product description...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Section 2: Coffee Details --}}
                <div class="form-section">
                    <div class="section-head">
                        <div class="section-num">2</div>
                        <div>
                            <div class="section-title">Coffee Details</div>
                            <div class="section-sub">Origin, processing, and quality information</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label class="label">Origin / Farm</label>
                            <input type="text" name="origin" class="input"
                                   value="{{ old('origin', $product->origin ?? '') }}"
                                   placeholder="e.g. Benguet, Sagada">
                        </div>
                        <div class="form-field">
                            <label class="label">Altitude</label>
                            <input type="text" name="altitude" class="input"
                                   value="{{ old('altitude', $product->altitude ?? '') }}"
                                   placeholder="e.g. 1,500 masl">
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-field">
                            <label class="label">Roast Level</label>
                            <select name="roast_level" class="input">
                                <option value="">â€” Select â€”</option>
                                @foreach(['Light', 'Medium', 'Medium-Dark', 'Dark', 'Extra Dark', 'Green'] as $r)
                                    <option value="{{ $r }}" @selected(old('roast_level', $product->roast_level ?? '') == $r)>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="label">Process Method</label>
                            <select name="process_method" class="input">
                                <option value="">â€” Select â€”</option>
                                @foreach(['Washed', 'Natural', 'Honey', 'Anaerobic', 'Wet-Hulled', 'Semi-Washed'] as $p)
                                    <option value="{{ $p }}" @selected(old('process_method', $product->process_method ?? '') == $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="label">Harvest Year</label>
                            <input type="text" name="harvest_year" class="input"
                                   value="{{ old('harvest_year', $product->harvest_year ?? '') }}"
                                   placeholder="e.g. 2024">
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="label">Cupping Notes</label>
                        <textarea name="cupping_notes" rows="2" class="input"
                                  placeholder="e.g. Chocolate, caramel, citrus finish">{{ old('cupping_notes', $product->cupping_notes ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Section 3: Pricing & Stock --}}
                <div class="form-section">
                    <div class="section-head">
                        <div class="section-num">3</div>
                        <div>
                            <div class="section-title">Pricing & Stock</div>
                            <div class="section-sub">Set prices and inventory levels</div>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-field">
                            <label class="label">Cost Price (â‚±) *</label>
                            <input type="number" step="0.01" name="cost_price" id="costPrice" class="input"
                                   value="{{ old('cost_price', $product->cost_price ?? '') }}"
                                   placeholder="0.00" min="0" required oninput="updateProfit()">
                        </div>
                        <div class="form-field">
                            <label class="label">Selling Price (â‚±) *</label>
                            <input type="number" step="0.01" name="price" id="sellingPrice" class="input"
                                   value="{{ old('price', $product->price ?? '') }}"
                                   placeholder="0.00" min="0" required oninput="updateProfit()">
                        </div>
                        <div class="form-field">
                            <label class="label">Wholesale Price (â‚±)</label>
                            <input type="number" step="0.01" name="wholesale_price" class="input"
                                   value="{{ old('wholesale_price', $product->wholesale_price ?? '') }}"
                                   placeholder="Optional" min="0">
                        </div>
                    </div>

                    <div class="profit-box" id="profitBox" style="display:none;">
                        <span class="label-sm">Profit Margin</span>
                        <span class="value" id="profitValue">0%</span>
                    </div>
                </div>

                {{-- Section 4: Unit & Package --}}
                <div class="form-section">
                    <div class="section-head">
                        <div class="section-num">4</div>
                        <div>
                            <div class="section-title">Unit & Package</div>
                            <div class="section-sub">How is this product packaged?</div>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-field">
                            <label class="label">Unit Type *</label>
                            <select name="unit_type" class="input" required>
                                <option value="pack" @selected(old('unit_type', $product->unit_type ?? 'pack') == 'pack')>Pack</option>
                                <option value="kg" @selected(old('unit_type', $product->unit_type ?? '') == 'kg')>Kilo (kg)</option>
                                <option value="sako" @selected(old('unit_type', $product->unit_type ?? '') == 'sako')>Sako (bulk)</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="label">Base Unit *</label>
                            <input type="text" name="base_unit" class="input"
                                   value="{{ old('base_unit', $product->base_unit ?? '250g') }}"
                                   placeholder="e.g. 250g, 500g, 1kg" required>
                        </div>
                        <div class="form-field">
                            <label class="label">Weight (grams)</label>
                            <input type="number" name="weight_grams" class="input"
                                   value="{{ old('weight_grams', $product->weight_grams ?? '') }}"
                                   placeholder="e.g. 250" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label class="label">Stock Quantity *</label>
                            <input type="number" name="stock" class="input"
                                   value="{{ old('stock', $product->stock ?? 0) }}"
                                   placeholder="0" min="0" required>
                        </div>
                        <div class="form-field">
                            <label class="label">Reorder Level *</label>
                            <input type="number" name="reorder_level" class="input"
                                   value="{{ old('reorder_level', $product->reorder_level ?? 10) }}"
                                   placeholder="10" min="0" required>
                            <div style="font-size:10px;color:var(--text-muted);margin-top:4px;">Alert when stock drops below this</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: SIDE PANEL --}}
        <div>
            {{-- Image --}}
            <div class="card">
                <div class="section-head" style="margin-bottom:12px;">
                    <div class="section-num">ðŸ“·</div>
                    <div>
                        <div class="section-title">Product Image</div>
                        <div class="section-sub">JPG, PNG Â· Max 3MB</div>
                    </div>
                </div>

                @php
                    $currentImage = (isset($product) && $product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image))
                        ? asset('storage/' . $product->image)
                        : null;
                @endphp

                <div class="image-upload {{ $currentImage ? 'has-image' : '' }}" id="imageUpload">
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this)" id="imageInput">

                    <div id="uploadPlaceholder" style="{{ $currentImage ? 'display:none' : '' }}">
                        <div class="upload-icon">ðŸ“·</div>
                        <div class="upload-title">Click to upload</div>
                        <div class="upload-hint">or drag and drop</div>
                    </div>

                    <img id="imagePreview" class="image-preview" src="{{ $currentImage ?? '' }}"
                         style="{{ $currentImage ? '' : 'display:none' }}" alt="">
                </div>

                @if($currentImage)
                    <div class="image-actions">
                        <button type="button" class="image-remove" onclick="removeImage()">
                            ðŸ—‘ Remove Image
                        </button>
                    </div>
                @endif
            </div>

            {{-- Visibility --}}
            <div class="card">
                <div class="section-head" style="margin-bottom:12px;">
                    <div class="section-num">ðŸ‘</div>
                    <div>
                        <div class="section-title">Visibility</div>
                        <div class="section-sub">Control product display</div>
                    </div>
                </div>

                <label class="toggle-row">
                    <div class="info">
                        Active product
                        <small>Show in menu and POS</small>
                    </div>
                    <div class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                        <span class="toggle-slider"></span>
                    </div>
                </label>

                <label class="toggle-row">
                    <div class="info">
                        Featured product
                        <small>Highlight as bestseller</small>
                    </div>
                    <div class="toggle-switch">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))>
                        <span class="toggle-slider"></span>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="card" style="margin-bottom:0;">
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;padding:12px;">
                        {{ isset($product) ? 'âœ“ Update Product' : '+ Create Product' }}
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-ghost" style="padding:12px;">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('imageUpload').classList.add('has-image');
        };
        reader.readAsDataURL(input.files[0]);
    }

    function removeImage() {
        if (!confirm('Remove image?')) return;
        document.getElementById('imageInput').value = '';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('imagePreview').src = '';
        document.getElementById('uploadPlaceholder').style.display = 'block';
        document.getElementById('imageUpload').classList.remove('has-image');

        let flag = document.getElementById('removeImageFlag');
        if (!flag) {
            flag = document.createElement('input');
            flag.type = 'hidden';
            flag.name = 'remove_image';
            flag.id = 'removeImageFlag';
            flag.value = '1';
            document.getElementById('productForm').appendChild(flag);
        }
        const actions = document.querySelector('.image-actions');
        if (actions) actions.style.display = 'none';
    }

    function updateProfit() {
        const cost = parseFloat(document.getElementById('costPrice').value) || 0;
        const price = parseFloat(document.getElementById('sellingPrice').value) || 0;
        const box = document.getElementById('profitBox');
        const val = document.getElementById('profitValue');

        if (cost > 0 && price > 0) {
            const margin = ((price - cost) / price) * 100;
            val.textContent = (margin > 0 ? '+' : '') + margin.toFixed(1) + '%';
            val.style.color = margin > 0 ? 'var(--green)' : 'var(--red)';
            box.style.display = 'flex';
        } else {
            box.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', updateProfit);
</script>
@endpush