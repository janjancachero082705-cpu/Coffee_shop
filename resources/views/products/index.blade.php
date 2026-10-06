@extends('layouts.admin')

@section('title', 'Products')
@section('subtitle', 'Manage your product catalog')

@section('content')

@php
    $totalProducts = \App\Models\Product::count();
    $activeProducts = \App\Models\Product::where('is_active', true)->count();
    $lowStock = \App\Models\Product::where('is_active', true)->whereColumn('stock', '<=', 'reorder_level')->where('stock', '>', 0)->count();
    $outOfStock = \App\Models\Product::where('stock', '<=', 0)->count();
    $stockValue = (float) \App\Models\Product::sum(\DB::raw('stock * cost_price'));
    $categories = \App\Models\Category::orderBy('name')->get();
@endphp

{{-- ===== KPI CARDS ===== --}}
<div class="kpi-grid">
    <div class="kpi">
        <div class="kpi-icon gold">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Total Products</div>
            <div class="kpi-value">{{ $totalProducts }}</div>
            <div class="kpi-meta">{{ $activeProducts }} active</div>
        </div>
    </div>
    <div class="kpi">
        <div class="kpi-icon amber">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Low Stock</div>
            <div class="kpi-value">{{ $lowStock }}</div>
            <div class="kpi-meta">Needs restocking</div>
        </div>
    </div>
    <div class="kpi">
        <div class="kpi-icon red">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Out of Stock</div>
            <div class="kpi-value">{{ $outOfStock }}</div>
            <div class="kpi-meta">Unavailable</div>
        </div>
    </div>
    <div class="kpi">
        <div class="kpi-icon green">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Stock Value</div>
            <div class="kpi-value">&#8369;{{ number_format($stockValue, 0) }}</div>
            <div class="kpi-meta">At cost price</div>
        </div>
    </div>
</div>

{{-- ===== TOOLBAR ===== --}}
<div class="toolbar-section">

    {{-- ROW 1: TITLE --}}
    <div class="toolbar-title-row">
        <div class="toolbar-title">All Products</div>
        <div class="toolbar-sub">{{ $totalProducts }} items in catalog</div>
    </div>

    {{-- ROW 2: FILTERS + ACTIONS --}}
    <form method="GET" class="toolbar-actions-row">
        <div class="t-search">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product...">
        </div>

        <select name="category" class="t-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="stock" class="t-select" onchange="this.form.submit()">
            <option value="">All Stock</option>
            <option value="low" {{ request('stock')==='low'?'selected':'' }}>Low Stock</option>
            <option value="out" {{ request('stock')==='out'?'selected':'' }}>Out of Stock</option>
            <option value="in" {{ request('stock')==='in'?'selected':'' }}>In Stock</option>
        </select>

        <select name="sort" class="t-select" onchange="this.form.submit()">
            <option value="latest" {{ request('sort')==='latest'?'selected':'' }}>Latest</option>
            <option value="name" {{ request('sort')==='name'?'selected':'' }}>Name A-Z</option>
            <option value="price_low" {{ request('sort')==='price_low'?'selected':'' }}>Price Low-High</option>
            <option value="price_high" {{ request('sort')==='price_high'?'selected':'' }}>Price High-Low</option>
        </select>

        @if(request()->hasAny(['search','category','stock','sort']))
            <a href="{{ route('products.index') }}" class="t-clear" title="Clear filters">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </a>
        @endif

        <a href="{{ route('products.create') }}" class="t-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <span>Add Product</span>
        </a>
    </form>

</div>

{{-- ===== PRODUCTS GRID ===== --}}
@if($products->isEmpty())
    <div class="card">
        <div class="empty-box">
            <div class="empty-icon">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="empty-title">No products found</div>
            <div class="empty-text">Add your first product to get started</div>
            <a href="{{ route('products.create') }}" class="t-add" style="margin-top: 12px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span>Add Product</span>
            </a>
        </div>
    </div>
@else
    <div class="products-grid">
        @foreach($products as $product)
            @php
                $margin = $product->price > 0 ? (($product->price - $product->cost_price) / $product->price) * 100 : 0;
                $stockStatus = 'in';
                if ($product->stock <= 0) $stockStatus = 'out';
                elseif ($product->stock <= $product->reorder_level) $stockStatus = 'low';
            @endphp

            <div class="product-card" onclick="window.location='{{ route('products.show', $product) }}'" style="cursor:pointer;">
                <div class="product-media">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                    @else
                        <div class="product-placeholder">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif

                    <span class="stock-badge {{ $stockStatus }}">
                        @if($stockStatus === 'out') Out of Stock
                        @elseif($stockStatus === 'low') Low: {{ $product->stock }}
                        @else Stock: {{ $product->stock }}
                        @endif
                    </span>

                    <div class="dots-wrap" onclick="event.stopPropagation();">
                        <button type="button" class="dots-btn" onclick="toggleProductMenu(event, {{ $product->id }})">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="2"/>
                                <circle cx="12" cy="12" r="2"/>
                                <circle cx="12" cy="19" r="2"/>
                            </svg>
                        </button>
                        <div class="dots-menu" id="product-menu-{{ $product->id }}">
                            <a href="{{ route('products.show', $product) }}" class="dots-item" onclick="event.stopPropagation();">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>View</span>
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="dots-item" onclick="event.stopPropagation();">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                <span>Edit</span>
                            </a>
                            <button type="button" class="dots-item danger" onclick="confirmProductDelete(event, {{ $product->id }}, '{{ addslashes($product->name) }}')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-body">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-sku">{{ $product->sku ?? '-' }}</div>

                    <div class="product-price-row">
                        <div class="product-price">
                            <span class="currency">&#8369;</span>{{ number_format($product->price, 2) }}
                        </div>
                        @if($product->cost_price > 0)
                            <div class="product-margin">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M23 6l-9.5 9.5-5-5L1 18"/><path d="M17 6h6v6"/></svg>
                                {{ number_format($margin, 1) }}%
                            </div>
                        @endif
                    </div>

                    @if($product->cost_price > 0)
                        <div class="product-cost">Cost: &#8369;{{ number_format($product->cost_price, 2) }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if(method_exists($products, 'links') && $products->hasPages())
        <div class="pagination-wrap">
            {{ $products->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    @endif
@endif

<form id="productDeleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
    /* ===== KPI ===== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .kpi {
        background: rgba(34, 34, 44, 0.32);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.25s ease;
    }
    .kpi:hover {
        transform: translateY(-3px);
        border-color: rgba(169, 120, 74, 0.3);
        background: rgba(34, 34, 44, 0.42);
    }
    .kpi-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: grid; place-items: center;
        flex-shrink: 0; border: 1px solid;
    }
    .kpi-icon.gold  { background: rgba(169, 120, 74, 0.14); border-color: rgba(169, 120, 74, 0.3); color: #c9a961; }
    .kpi-icon.amber { background: rgba(245, 158, 11, 0.14); border-color: rgba(245, 158, 11, 0.3); color: #f59e0b; }
    .kpi-icon.red   { background: rgba(239, 68, 68, 0.14);  border-color: rgba(239, 68, 68, 0.3);  color: #ef4444; }
    .kpi-icon.green { background: rgba(34, 197, 94, 0.14);  border-color: rgba(34, 197, 94, 0.3);  color: #22c55e; }
    .kpi-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-bottom: 4px;
    }
    .kpi-value {
        font-size: 22px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em; line-height: 1.1;
        margin-bottom: 4px;
        font-variant-numeric: tabular-nums;
    }
    .kpi-meta { font-size: 11px; color: var(--text-muted); }

    /* ===== TOOLBAR SECTION ===== */
    .toolbar-section {
        margin-bottom: 18px;
    }

    .toolbar-title-row {
        margin-bottom: 14px;
    }
    .toolbar-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        line-height: 1.2;
    }
    .toolbar-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 3px;
    }

    .toolbar-actions-row {
        flex-direction: row !important;
        flex-wrap: wrap !important;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Search */
    .t-search {
        width: 320px !important;
        max-width: 320px !important;
        position: relative;
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 220px;
        max-width: 320px;
    }
    .t-search svg {
        position: absolute;
        left: 14px;
        color: var(--text-muted);
        pointer-events: none;
        z-index: 1;
    }
    .t-search input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        background: rgba(20, 20, 26, 0.55);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all 0.15s;
    }
    .t-search input::placeholder { color: var(--text-muted); }
    .t-search input:focus {
        border-color: #a9784a;
        background: rgba(20, 20, 26, 0.85);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.12);
    }

    /* Select */
    .t-select {
        width: auto !important;
        padding: 10px 34px 10px 14px;
        background: rgba(20, 20, 26, 0.55);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        transition: all 0.15s;
        min-width: 130px;
    }
    .t-select:hover, .t-select:focus {
        border-color: rgba(169, 120, 74, 0.5);
        color: var(--text-primary);
    }

    /* Clear button */
    .t-clear {
        width: 40px !important;
        min-width: 40px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        text-decoration: none;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .t-clear:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.5);
    }

    /* Add Product button — ENHANCED */
    .t-add {
        width: auto !important;
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        height: 40px;
        background: linear-gradient(135deg, #c9a961 0%, #a9784a 50%, #8a5f36 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
        white-space: nowrap;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.6),
                    inset 0 1px 0 rgba(255, 255, 255, 0.15);
        position: relative;
        overflow: hidden;
        letter-spacing: 0.01em;
    }
    .t-add svg {
        flex-shrink: 0;
        display: block;
    }
    .t-add span {
        display: block;
        line-height: 1;
    }
    .t-add::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: left 0.6s ease;
    }
    .t-add:hover::before {
        left: 100%;
    }
    .t-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -8px rgba(169, 120, 74, 0.85),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    .t-add:active {
        transform: translateY(0);
    }

    /* ===== PRODUCTS GRID ===== */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .product-card {
        background: rgba(34, 34, 44, 0.32);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 12px;
        display: flex; flex-direction: column;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-4px);
        border-color: rgba(169, 120, 74, 0.3);
        background: rgba(34, 34, 44, 0.42);
        box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.6);
    }
    .product-media {
        position: relative;
        height: 140px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 12px;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.5));
    }
    .product-image {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .product-card:hover .product-image { transform: scale(1.05); }
    .product-placeholder {
        width: 100%; height: 100%;
        display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.4);
    }
    .stock-badge {
        position: absolute;
        top: 8px; left: 8px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 9.5px;
        font-weight: 800;
        backdrop-filter: blur(10px);
        white-space: nowrap;
    }
    .stock-badge.in  { background: rgba(34, 197, 94, 0.9);  color: #fff; }
    .stock-badge.low { background: rgba(245, 158, 11, 0.9); color: #fff; }
    .stock-badge.out { background: rgba(239, 68, 68, 0.9);  color: #fff; }

    .dots-wrap { position: absolute; top: 8px; right: 8px; z-index: 5; }
    .dots-btn {
        width: 28px; height: 28px;
        border-radius: 7px;
        background: rgba(20, 20, 26, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #f5f3f0;
        display: grid; place-items: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .dots-btn:hover { background: rgba(169, 120, 74, 0.8); }
    .dots-menu {
        position: absolute;
        top: calc(100% + 6px); right: 0;
        min-width: 130px;
        background: rgba(20, 20, 26, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 5px;
        box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.8);
        z-index: 100;
        opacity: 0; visibility: hidden;
        transform: translateY(-6px) scale(0.96);
        transition: all 0.18s;
        transform-origin: top right;
    }
    .dots-menu.open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
    .dots-item {
        display: flex; align-items: center;
        gap: 9px; width: 100%;
        padding: 8px 11px;
        border-radius: 7px;
        font-size: 12.5px; font-weight: 500;
        color: var(--text-secondary);
        background: transparent; border: none;
        cursor: pointer; font-family: inherit;
        text-align: left; text-decoration: none;
        transition: all 0.12s;
    }
    .dots-item:hover { background: rgba(169, 120, 74, 0.12); color: #c9a961; }
    .dots-item.danger { color: #ef4444; }
    .dots-item.danger:hover { background: rgba(239, 68, 68, 0.12); }

    .product-body { flex: 1; }
    .product-name {
        font-size: 13.5px; font-weight: 700;
        color: var(--text-primary);
        line-height: 1.3;
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 36px;
    }
    .product-sku {
        font-size: 10.5px;
        color: var(--text-muted);
        font-family: ui-monospace, monospace;
        font-weight: 600;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .product-price-row {
        display: flex; justify-content: space-between;
        align-items: baseline;
        margin-bottom: 4px;
    }
    .product-price {
        font-size: 19px; font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }
    .product-price .currency { font-size: 12px; margin-right: 1px; }
    .product-margin {
        display: inline-flex; align-items: center;
        gap: 3px;
        font-size: 10.5px; font-weight: 800;
        color: #22c55e;
        padding: 2px 6px;
        background: rgba(34, 197, 94, 0.1);
        border-radius: 5px;
    }
    .product-cost { font-size: 11px; color: var(--text-muted); }

    /* ===== ROLLING PAGINATION ===== */
    .pagination-wrap {
        margin-top: 28px;
        display: flex;
        justify-content: center;
        padding: 4px 0;
    }
    .pag-nav {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: rgba(34, 34, 44, 0.4);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        box-shadow: 0 8px 24px -12px rgba(0, 0, 0, 0.6);
    }

    /* Arrows */
    .pag-arrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(20, 20, 26, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: var(--text-secondary);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .pag-arrow svg { display: block; }
    .pag-arrow:hover:not(.pag-disabled) {
        background: rgba(169, 120, 74, 0.15);
        border-color: rgba(169, 120, 74, 0.4);
        color: #c9a961;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px -6px rgba(169, 120, 74, 0.5);
    }
    .pag-arrow.pag-disabled {
        opacity: 0.3;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Numbers window */
    .pag-numbers {
        position: relative;
        display: flex;
        align-items: center;
        gap: 6px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    /* Number button */
    .pag-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 6px;
        border-radius: 10px;
        background: rgba(20, 20, 26, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-variant-numeric: tabular-nums;
        position: relative;
    }
    .pag-num:hover:not(.pag-active) {
        background: rgba(169, 120, 74, 0.15);
        border-color: rgba(169, 120, 74, 0.4);
        color: #c9a961;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px -6px rgba(169, 120, 74, 0.5);
    }
    .pag-num.pag-active {
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border-color: transparent;
        color: #fff;
        cursor: default;
        box-shadow: 0 6px 16px -4px rgba(169, 120, 74, 0.7);
        transform: scale(1.08);
        animation: pagPulse 0.4s ease;
    }
    @keyframes pagPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1.08); }
    }

    /* Rolling animation on page change */
    .pag-numbers.rolling-left .pag-num {
        animation: slideLeft 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pag-numbers.rolling-right .pag-num {
        animation: slideRight 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes slideLeft {
        0% { opacity: 0.3; transform: translateX(-12px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideRight {
        0% { opacity: 0.3; transform: translateX(12px); }
        100% { opacity: 1; transform: translateX(0); }
    }

    /* Responsive */
    @media (max-width: 480px) {
        .pag-nav { padding: 6px 8px; gap: 6px; }
        .pag-arrow, .pag-num { width: 34px; height: 34px; min-width: 34px; font-size: 12px; }
    }
/* ===== EMPTY ===== */
    .empty-box { padding: 60px 20px; text-align: center; }
    .empty-icon {
        color: var(--text-muted); opacity: 0.35;
        display: flex; justify-content: center;
        margin-bottom: 16px;
    }
    .empty-title {
        font-size: 15px; font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .empty-text {
        font-size: 12.5px; color: var(--text-muted);
        margin-bottom: 18px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
        .products-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 1000px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .products-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .toolbar-actions-row {
        flex-direction: row !important;
        flex-wrap: wrap !important;
            flex-direction: column;
            align-items: stretch;
        }
        .t-search {
        width: 320px !important;
        max-width: 320px !important; max-width: 100%; width: 100%; }
        .t-select {
        width: auto !important; width: 100%; }
        .t-add {
        width: auto !important; width: 100%; }
    }
    @media (max-width: 640px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .products-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleProductMenu(event, id) {
        event.stopPropagation();
        event.preventDefault();
        document.querySelectorAll('.dots-menu.open').forEach(function(m) {
            if (m.id !== 'product-menu-' + id) m.classList.remove('open');
        });
        var menu = document.getElementById('product-menu-' + id);
        if (menu) menu.classList.toggle('open');
    }
    document.addEventListener('click', function() {
        document.querySelectorAll('.dots-menu.open').forEach(function(m) { m.classList.remove('open'); });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dots-menu.open').forEach(function(m) { m.classList.remove('open'); });
        }
    });
    function confirmProductDelete(event, id, name) {
        event.stopPropagation();
        event.preventDefault();
        document.querySelectorAll('.dots-menu.open').forEach(function(m) { m.classList.remove('open'); });
        if (!confirm('Delete "' + name + '"?\n\nThis action cannot be undone.')) return;
        var form = document.getElementById('productDeleteForm');
        form.action = '/products/' + id;
        form.submit();
    }

    // Rolling pagination animation
    (function() {
        var pagNumbers = document.querySelector('.pag-numbers');
        if (!pagNumbers) return;

        var currentPage = parseInt(pagNumbers.getAttribute('data-current') || '1');

        // Detect direction based on click
        document.querySelectorAll('.pag-num, .pag-arrow').forEach(function(link) {
            link.addEventListener('click', function(e) {
                var href = this.getAttribute('href');
                if (!href) return;

                var targetPage = null;

                // Parse page from URL
                var match = href.match(/[?&]page=(\d+)/);
                if (match) {
                    targetPage = parseInt(match[1]);
                } else if (this.classList.contains('pag-num')) {
                    targetPage = parseInt(this.textContent.trim());
                } else if (this.classList.contains('pag-arrow')) {
                    // Arrow: prev or next
                    if (this.getAttribute('rel') === 'prev') {
                        targetPage = currentPage - 1;
                    } else if (this.getAttribute('rel') === 'next') {
                        targetPage = currentPage + 1;
                    }
                }

                if (targetPage === null) return;

                // Add rolling animation class
                if (targetPage > currentPage) {
                    pagNumbers.classList.add('rolling-left');
                } else if (targetPage < currentPage) {
                    pagNumbers.classList.add('rolling-right');
                }

                // Save sa sessionStorage para makita sa next page
                try {
                    sessionStorage.setItem('pag_rolling_direction', targetPage > currentPage ? 'left' : 'right');
                } catch (err) {}
            });
        });

        // On page load, check kung gikan sa rolling
        try {
            var direction = sessionStorage.getItem('pag_rolling_direction');
            if (direction === 'left') {
                pagNumbers.classList.add('rolling-left');
            } else if (direction === 'right') {
                pagNumbers.classList.add('rolling-right');
            }
            sessionStorage.removeItem('pag_rolling_direction');

            // Remove class after animation
            setTimeout(function() {
                pagNumbers.classList.remove('rolling-left', 'rolling-right');
            }, 400);
        } catch (err) {}
    })();
</script>
@endpush