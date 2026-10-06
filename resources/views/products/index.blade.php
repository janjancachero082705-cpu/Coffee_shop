@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-sub', 'Manage your coffee beans catalog')

@push('styles')
<style>
    /* ============================================
       GLASSMORPHISM DESIGN SYSTEM
       ============================================ */
    :root {
        --glass-bg: rgba(24, 24, 28, 0.55);
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-hover: rgba(24, 24, 28, 0.75);
        --glass-blur: blur(20px) saturate(1.4);
    }

    /* ============================================
       STATS — Glass Cards
       ============================================ */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    @media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat {
        padding: 18px 20px;
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        position: relative;
        overflow: hidden;
        transition: all .25s ease;
    }
    .stat::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
    }
    .stat:hover {
        background: var(--glass-hover);
        border-color: rgba(255,255,255,.12);
        transform: translateY(-2px);
    }
    .stat-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .1em;
        margin-bottom: 8px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -.03em;
        line-height: 1;
        margin-bottom: 6px;
    }
    .stat-meta {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* ============================================
       HEADER SECTION
       ============================================ */
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        gap: 12px;
        flex-wrap: wrap;
    }
    .products-header-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -.01em;
    }
    .products-header-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 3px;
    }

    /* ============================================
       FILTER BAR — Glassmorphism
       ============================================ */
    .filter-bar {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 18px;
        flex-wrap: wrap;
        padding: 12px;
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
    }

    .search-wrap {
        position: relative;
        flex: 1;
        min-width: 220px;
        max-width: 340px;
    }
    .search-wrap input {
        width: 100%;
        padding: 0 12px 0 36px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 12px;
        outline: none;
        font-family: inherit;
        height: 34px;
        transition: all .15s ease;
    }
    .search-wrap input::placeholder { color: var(--text-muted); }
    .search-wrap input:focus {
        border-color: var(--accent);
        background: rgba(201,169,97,.05);
        box-shadow: 0 0 0 3px rgba(201,169,97,.1);
    }
    .search-wrap svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        width: 13px;
        height: 13px;
    }

    /* Custom select — override base width 100% */
    .filter-bar select.filter-select {
        width: auto !important;
        max-width: 200px;
        flex-shrink: 0;
        min-width: 120px;
        padding: 0 30px 0 12px;
        background-color: rgba(255,255,255,.03);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2366666e' stroke-width='3' stroke-linecap='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        border: 1px solid rgba(255,255,255,.06);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 12px;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        height: 34px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        transition: all .15s ease;
    }
    .filter-bar select.filter-select:hover {
        background-color: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.1);
    }
    .filter-bar select.filter-select:focus {
        border-color: var(--accent);
        background-color: rgba(201,169,97,.05);
    }

    .filter-bar .btn {
        height: 34px;
        padding: 0 14px;
        font-size: 12px;
        flex-shrink: 0;
    }

    /* View toggle */
    .view-toggle {
        display: inline-flex;
        gap: 2px;
        padding: 2px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        border-radius: var(--radius-sm);
        height: 34px;
        flex-shrink: 0;
    }
    .view-toggle button {
        padding: 0 11px;
        border-radius: 6px;
        color: var(--text-muted);
        display: grid;
        place-items: center;
        transition: all .15s;
        cursor: pointer;
    }
    .view-toggle button:hover { color: var(--text-primary); }
    .view-toggle button.active {
        background: var(--accent);
        color: #1a1410;
    }

    /* ============================================
       PRODUCT GRID
       ============================================ */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 14px;
    }

    .product-card {
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all .25s ease;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .product-card:hover {
        background: var(--glass-hover);
        border-color: rgba(201,169,97,.3);
        transform: translateY(-3px);
        box-shadow: 0 12px 32px -12px rgba(0,0,0,.6);
    }

    .product-img {
        position: relative;
        width: 100%;
        height: 150px;
        background: linear-gradient(135deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
        display: grid;
        place-items: center;
        overflow: hidden;
        border-bottom: 1px solid var(--glass-border);
    }
    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }
    .product-card:hover .product-img img { transform: scale(1.08); }
    .product-img .emoji {
        font-size: 52px;
        opacity: .85;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,.4));
    }
    .product-img::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 60%, rgba(10,10,11,.7) 100%);
        pointer-events: none;
    }

    /* Badges */
    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 700;
        z-index: 2;
        backdrop-filter: blur(12px);
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid;
    }
    .stock-badge::before {
        content: '';
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: currentColor;
    }
    .stock-badge.ok {
        background: rgba(74,222,128,.12);
        color: var(--green);
        border-color: rgba(74,222,128,.25);
    }
    .stock-badge.low {
        background: rgba(251,191,36,.12);
        color: var(--yellow);
        border-color: rgba(251,191,36,.25);
    }
    .stock-badge.out {
        background: rgba(248,113,113,.12);
        color: var(--red);
        border-color: rgba(248,113,113,.25);
    }

    .featured-star {
        position: absolute;
        top: 10px;
        left: 10px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dim));
        color: #1a1410;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 11px;
        z-index: 2;
        box-shadow: 0 4px 12px rgba(201,169,97,.5);
    }

    .sku-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        padding: 3px 8px;
        border-radius: 5px;
        background: rgba(0,0,0,.65);
        backdrop-filter: blur(12px);
        color: var(--text-secondary);
        font-size: 9px;
        font-weight: 600;
        font-family: monospace;
        z-index: 2;
        letter-spacing: .02em;
    }

    .category-pill {
        position: absolute;
        bottom: 10px;
        right: 10px;
        padding: 3px 8px;
        border-radius: 5px;
        background: rgba(201,169,97,.15);
        backdrop-filter: blur(12px);
        color: var(--accent);
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        z-index: 2;
        border: 1px solid rgba(201,169,97,.2);
    }

    /* Body */
    .product-body {
        padding: 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .product-info { }
    .product-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        letter-spacing: -.01em;
        line-height: 1.3;
    }
    .product-meta {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        font-size: 9px;
        color: var(--text-muted);
        margin-top: 6px;
    }
    .product-meta span {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 4px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        font-weight: 600;
    }

    .product-pricing {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding-top: 10px;
        border-top: 1px solid var(--glass-border);
    }
    .price-main {
        font-size: 17px;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: -.02em;
        line-height: 1;
    }
    .price-main small {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 500;
        margin-left: 2px;
    }
    .price-cost {
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 4px;
    }
    .price-profit {
        font-size: 11px;
        font-weight: 700;
        color: var(--green);
        padding: 2px 7px;
        border-radius: 4px;
        background: rgba(74,222,128,.1);
    }

    .product-actions {
        display: flex;
        gap: 5px;
        padding-top: 10px;
        border-top: 1px solid var(--glass-border);
    }
    .product-actions .btn {
        flex: 1;
        justify-content: center;
        padding: 6px 10px;
        font-size: 11px;
        min-height: 30px;
    }

    /* ============================================
       EMPTY STATE
       ============================================ */
    .empty-state {
        text-align: center;
        padding: 70px 20px;
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
    }
    .empty-state-icon {
        width: 80px;
        height: 80px;
        border-radius: 22px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.08);
        display: grid;
        place-items: center;
        margin: 0 auto 18px;
        font-size: 36px;
    }

    /* ============================================
       ⭐ CUSTOM PAGINATION
       ============================================ */
    .pagination-wrap {
        margin-top: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }
    .pagination-wrap .pg-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: var(--radius-sm);
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        border: 1px solid var(--glass-border);
        color: var(--text-secondary);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all .15s ease;
        cursor: pointer;
        font-family: inherit;
    }
    .pagination-wrap .pg-btn:hover:not(.disabled):not(.active) {
        background: var(--glass-hover);
        border-color: rgba(255,255,255,.12);
        color: var(--text-primary);
        transform: translateY(-1px);
    }
    .pagination-wrap .pg-btn.active {
        background: var(--accent);
        color: #1a1410;
        border-color: var(--accent);
        box-shadow: 0 6px 16px -6px rgba(201,169,97,.6);
        font-weight: 800;
    }
    .pagination-wrap .pg-btn.disabled {
        opacity: .35;
        cursor: not-allowed;
        pointer-events: none;
    }
    .pagination-wrap .pg-btn svg {
        width: 14px;
        height: 14px;
    }
    .pagination-wrap .pg-dots {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        color: var(--text-muted);
        font-size: 12px;
        user-select: none;
    }

    /* Pagination info */
    .pg-info {
        text-align: center;
        margin-top: 12px;
        font-size: 11px;
        color: var(--text-muted);
    }
    .pg-info strong {
        color: var(--text-secondary);
        font-weight: 700;
    }

    /* ============================================
       LIST VIEW
       ============================================ */
    .product-list { display: flex; flex-direction: column; gap: 8px; }
    .product-list .product-card { flex-direction: row; align-items: center; padding: 12px; }
    .product-list .product-img {
        width: 64px;
        height: 64px;
        flex-shrink: 0;
        border-radius: var(--radius-sm);
        margin-right: 14px;
        border-bottom: none;
        border: 1px solid var(--glass-border);
    }
    .product-list .product-img::after { display: none; }
    .product-list .product-img .emoji { font-size: 28px; }
    .product-list .stock-badge,
    .product-list .sku-badge,
    .product-list .category-pill,
    .product-list .featured-star { display: none; }
    .product-list .product-body {
        padding: 0;
        flex-direction: row;
        align-items: center;
        gap: 16px;
        flex: 1;
    }
    .product-list .product-info { flex: 1; min-width: 0; }
    .product-list .product-pricing {
        border: none;
        padding: 0;
        min-width: 130px;
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
    }
    .product-list .product-actions {
        border: none;
        padding: 0;
        min-width: 140px;
        justify-content: flex-end;
    }
</style>
@endpush

@section('content')

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat">
        <div class="stat-label">Total Products</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-meta">{{ $stats['active'] }} active items</div>
    </div>
    <div class="stat">
        <div class="stat-label">Low Stock</div>
        <div class="stat-value" style="color:var(--yellow);">{{ $stats['low'] }}</div>
        <div class="stat-meta">Needs restocking</div>
    </div>
    <div class="stat">
        <div class="stat-label">Out of Stock</div>
        <div class="stat-value" style="color:var(--red);">{{ $stats['out'] }}</div>
        <div class="stat-meta">Currently unavailable</div>
    </div>
    <div class="stat">
        <div class="stat-label">Stock Value</div>
        <div class="stat-value" style="font-size:20px;">₱{{ number_format($stats['total_value'], 0) }}</div>
        <div class="stat-meta">Total selling value</div>
    </div>
</div>

{{-- HEADER --}}
<div class="products-header">
    <div>
        <div class="products-header-title">All Products</div>
        <div class="products-header-sub">
            {{ $products->total() }} items · ₱{{ number_format($stats['total_cost'], 0) }} cost value
        </div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        <div class="view-toggle">
            <button type="button" class="active" onclick="setView('grid')" title="Grid view">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </button>
            <button type="button" onclick="setView('list')" title="List view">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary" style="height:34px;padding:0 16px;font-size:12px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Add Product
        </a>
    </div>
</div>

{{-- FILTERS --}}
<form method="GET" class="filter-bar">
    <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, SKU, origin...">
    </div>

    <select name="category" class="filter-select" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>

    <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="active" @selected(request('status') == 'active')>Active</option>
        <option value="low" @selected(request('status') == 'low')>Low Stock</option>
        <option value="out" @selected(request('status') == 'out')>Out of Stock</option>
    </select>

    <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="latest" @selected(request('sort') == 'latest')>Latest First</option>
        <option value="name_asc" @selected(request('sort') == 'name_asc')>Name A-Z</option>
        <option value="name_desc" @selected(request('sort') == 'name_desc')>Name Z-A</option>
        <option value="price_asc" @selected(request('sort') == 'price_asc')>Price: Low to High</option>
        <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: High to Low</option>
        <option value="stock_asc" @selected(request('sort') == 'stock_asc')>Stock: Low to High</option>
        <option value="stock_desc" @selected(request('sort') == 'stock_desc')>Stock: High to Low</option>
    </select>

    @if(request()->hasAny(['search', 'category', 'status', 'sort']))
        <a href="{{ route('products.index') }}" class="btn btn-ghost">Clear Filters</a>
    @endif
</form>

{{-- PRODUCTS --}}
@if($products->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">📦</div>
        <div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">No products found</div>
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;max-width:340px;margin-left:auto;margin-right:auto;">
            {{ request()->hasAny(['search', 'category', 'status']) ? 'Try adjusting your filters or clear them to see all products.' : 'Start by adding your first coffee bean product to the catalog.' }}
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary" style="padding:10px 20px;">+ Add Product</a>
    </div>
@else
    <div class="product-grid" id="productContainer">
        @foreach($products as $product)
            @php
                $status = $product->stock_status;
                $stockLabel = $status === 'out' ? 'Out of stock' : $product->stock . ' ' . $product->base_unit;
            @endphp
            <div class="product-card">
                <div class="product-img">
                    @if($product->is_featured)
                        <div class="featured-star">★</div>
                    @endif

                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div class="emoji">{{ $product->category->emoji ?? '🫘' }}</div>
                    @endif

                    <span class="stock-badge {{ $status }}">{{ $stockLabel }}</span>
                    <span class="sku-badge">{{ $product->sku }}</span>
                    <span class="category-pill">{{ $product->category->name ?? 'Uncategorized' }}</span>
                </div>

                <div class="product-body">
                    <div class="product-info">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-meta">
                            @if($product->variety)
                                <span>{{ $product->variety }}</span>
                            @endif
                            @if($product->origin)
                                <span>📍 {{ $product->origin }}</span>
                            @endif
                            @if($product->roast_level)
                                <span>🔥 {{ $product->roast_level }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="product-pricing">
                        <div>
                            <div class="price-main">
                                ₱{{ number_format($product->price, 2) }}
                                <small>/ {{ $product->base_unit }}</small>
                            </div>
                            @if($product->cost_price > 0)
                                <div class="price-cost">Cost: ₱{{ number_format($product->cost_price, 2) }}</div>
                            @endif
                        </div>
                        @if($product->profit_margin > 0)
                            <div class="price-profit">+{{ $product->profit_margin }}%</div>
                        @endif
                    </div>

                    <div class="product-actions">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-ghost">View</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-ghost">Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                              onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')"
                              style="flex:1;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="width:100%;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ⭐ CUSTOM PAGINATION ⭐ --}}
    @if($products->hasPages())
        @php
            $currentPage = $products->currentPage();
            $lastPage = $products->lastPage();
            $startPage = max(1, $currentPage - 2);
            $endPage = min($lastPage, $currentPage + 2);
        @endphp

        <div class="pagination-wrap">
            {{-- Previous --}}
            @if($products->onFirstPage())
                <span class="pg-btn disabled">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    Prev
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="pg-btn">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    Prev
                </a>
            @endif

            {{-- First page --}}
            @if($startPage > 1)
                <a href="{{ $products->url(1) }}" class="pg-btn">1</a>
                @if($startPage > 2)
                    <span class="pg-dots">···</span>
                @endif
            @endif

            {{-- Page numbers --}}
            @for($page = $startPage; $page <= $endPage; $page++)
                @if($page == $currentPage)
                    <span class="pg-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $products->url($page) }}" class="pg-btn">{{ $page }}</a>
                @endif
            @endfor

            {{-- Last page --}}
            @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                    <span class="pg-dots">···</span>
                @endif
                <a href="{{ $products->url($lastPage) }}" class="pg-btn">{{ $lastPage }}</a>
            @endif

            {{-- Next --}}
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="pg-btn">
                    Next
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            @else
                <span class="pg-btn disabled">
                    Next
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </span>
            @endif
        </div>

        <div class="pg-info">
            Showing <strong>{{ $products->firstItem() }}</strong> to <strong>{{ $products->lastItem() }}</strong> of <strong>{{ $products->total() }}</strong> products
        </div>
    @endif
@endif

@endsection

@push('scripts')
<script>
    function setView(view) {
        const container = document.getElementById('productContainer');
        const buttons = document.querySelectorAll('.view-toggle button');

        if (!container) return;

        if (view === 'list') {
            container.classList.remove('product-grid');
            container.classList.add('product-list');
            buttons[1].classList.add('active');
            buttons[0].classList.remove('active');
        } else {
            container.classList.remove('product-list');
            container.classList.add('product-grid');
            buttons[0].classList.add('active');
            buttons[1].classList.remove('active');
        }
        localStorage.setItem('productView', view);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem('productView');
        if (saved === 'list') setView('list');
    });
</script>
@endpush