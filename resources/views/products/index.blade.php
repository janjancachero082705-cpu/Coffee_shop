@extends('layouts.admin')

@section('title', 'Products')
@section('subtitle', 'Manage your product catalog')

@section('content')

@php
    $total = $stats['total'] ?? 0;
    $lowStock = $stats['low_stock'] ?? 0;
    $outOfStock = $stats['out_of_stock'] ?? 0;
    $stockValue = $stats['stock_value'] ?? 0;
    $hasFilters = request()->filled('search') || request()->filled('category') || request()->filled('stock') || request()->filled('sort');
@endphp

{{-- ═══════ KPI CARDS ═══════ --}}
<div class="pl-kpis">
    <div class="pl-kpi pl-kpi-primary">
        <div class="pl-kpi-head">
            <div class="pl-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="pl-kpi-label">Total Products</div>
        </div>
        <div class="pl-kpi-value">{{ number_format($total) }}</div>
        <div class="pl-kpi-foot">{{ $stats['active'] ?? $total }} active</div>
    </div>

    <div class="pl-kpi pl-kpi-amber">
        <div class="pl-kpi-head">
            <div class="pl-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="pl-kpi-label">Low Stock</div>
        </div>
        <div class="pl-kpi-value">{{ number_format($lowStock) }}</div>
        <div class="pl-kpi-foot">Needs restocking</div>
    </div>

    <div class="pl-kpi pl-kpi-red">
        <div class="pl-kpi-head">
            <div class="pl-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M15 9l-6 6M9 9l6 6"/>
                </svg>
            </div>
            <div class="pl-kpi-label">Out of Stock</div>
        </div>
        <div class="pl-kpi-value">{{ number_format($outOfStock) }}</div>
        <div class="pl-kpi-foot">Unavailable</div>
    </div>

    <div class="pl-kpi pl-kpi-green">
        <div class="pl-kpi-head">
            <div class="pl-kpi-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
            <div class="pl-kpi-label">Stock Value</div>
        </div>
        <div class="pl-kpi-value">₱{{ number_format($stockValue, 0) }}</div>
        <div class="pl-kpi-foot">At cost price</div>
    </div>
</div>

{{-- ═══════ TOOLBAR ═══════ --}}
<div class="pl-toolbar">
    <div class="pl-toolbar-title">
        <h2 class="pl-title">All Products</h2>
        <div class="pl-subtitle">
            @if($hasFilters)
                <span class="pl-filtered">Filtered</span> ·
            @endif
            {{ $products->total() }} item{{ $products->total() !== 1 ? 's' : '' }} in catalog
        </div>
    </div>

    <div class="pl-toolbar-actions">
        <a href="{{ route('products.create') }}" class="pl-add-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Add Product
        </a>
    </div>
</div>

{{-- ═══════ FILTER BAR ═══════ --}}
<form method="GET" class="pl-filters">
    <div class="pl-search-wrap">
        <svg class="pl-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="text"
               name="search"
               class="pl-search"
               placeholder="Search product name, SKU..."
               value="{{ request('search') }}"
               autocomplete="off">
        @if(request('search'))
            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="pl-search-clear">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </a>
        @endif
    </div>

    <select name="category" class="pl-select">
        <option value="">All Categories</option>
        @foreach($categories ?? [] as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>

    <select name="stock" class="pl-select">
        <option value="">All Stock</option>
        <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low Stock</option>
        <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock</option>
        <option value="in" {{ request('stock') === 'in' ? 'selected' : '' }}>In Stock</option>
    </select>

    <select name="sort" class="pl-select">
        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A-Z</option>
        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price Low-High</option>
        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price High-Low</option>
        <option value="stock_low" {{ request('sort') === 'stock_low' ? 'selected' : '' }}>Stock Low-High</option>
    </select>

    <button type="submit" class="pl-filter-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
        </svg>
        Filter
    </button>

    @if($hasFilters)
        <a href="{{ route('products.index') }}" class="pl-clear-btn">
            Clear
        </a>
    @endif
</form>

{{-- ═══════ PRODUCTS GRID ═══════ --}}
@if($products->isEmpty())
    <div class="pl-empty">
        <div class="pl-empty-icon">
            <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div class="pl-empty-title">
            @if($hasFilters) No products match your filters @else Walay products yet @endif
        </div>
        <div class="pl-empty-desc">
            @if($hasFilters)
                Try changing your search or filters
            @else
                Start by adding your first product
            @endif
        </div>
        <a href="{{ $hasFilters ? route('products.index') : route('products.create') }}" class="pl-add-btn">
            @if($hasFilters) Clear Filters @else + Add Product @endif
        </a>
    </div>
@else
    <div class="pl-grid">
        @foreach($products as $product)
            @php
                $margin = $product->price > 0 ? (($product->price - $product->cost_price) / $product->price) * 100 : 0;
                $retailProfit = (float) $product->price - (float) $product->cost_price;
                $whProfit = $product->wholesale_price > 0 ? (float) $product->wholesale_price - (float) $product->cost_price : 0;
                $isLoss = $retailProfit < 0;
                $isLowMargin = !$isLoss && $margin < 15;
                $stockStatus = 'in';
                if ($product->stock <= 0) $stockStatus = 'out';
                elseif ($product->stock <= $product->reorder_level) $stockStatus = 'low';
            @endphp

            <div class="pl-card">
                <a href="{{ route('products.show', $product) }}" class="pl-card-link">
                    {{-- IMAGE --}}
                    <div class="pl-media">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="pl-image" loading="lazy">
                        @else
                            <div class="pl-placeholder">
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif

                        <span class="pl-stock-badge {{ $stockStatus }}">
                            @if($stockStatus === 'out')
                                Out of Stock
                            @elseif($stockStatus === 'low')
                                Low: {{ $product->stock }}
                            @else
                                Stock: {{ $product->stock }}
                            @endif
                        </span>

                        @if($isLoss)
                            <span class="pl-loss-badge">LOSS</span>
                        @endif
                    </div>

                    {{-- BODY --}}
                    <div class="pl-body">
                        <div class="pl-name">{{ $product->name }}</div>
                        <div class="pl-sku">{{ $product->sku ?? '-' }}</div>

                        {{-- PRICING CARD --}}
                        @if($product->cost_price > 0)
                            <div class="pl-pricing">
                                <div class="pl-pricing-line">
                                    <span class="pl-pricing-lbl">
                                        <span class="pl-dot cost"></span>
                                        Cost
                                    </span>
                                    <span class="pl-pricing-val">₱{{ number_format($product->cost_price, 2) }}</span>
                                </div>

                                @if($product->wholesale_price > 0)
                                    <div class="pl-pricing-line">
                                        <span class="pl-pricing-lbl">
                                            <span class="pl-dot wholesale"></span>
                                            Wholesale
                                        </span>
                                        <span class="pl-pricing-val">
                                            ₱{{ number_format($product->wholesale_price, 2) }}
                                            <span class="pl-tag {{ $whProfit < 0 ? 'loss' : 'ok' }}">
                                                {{ $whProfit >= 0 ? '+' : '' }}₱{{ number_format($whProfit, 2) }}
                                            </span>
                                        </span>
                                    </div>
                                @endif

                                <div class="pl-pricing-line pl-pricing-main">
                                    <span class="pl-pricing-lbl">
                                        <span class="pl-dot retail"></span>
                                        Retail
                                    </span>
                                    <span class="pl-pricing-val">
                                        ₱{{ number_format($product->price, 2) }}
                                    </span>
                                </div>

                                <div class="pl-profit-row">
                                    <span class="pl-profit-lbl">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            @if($isLoss)
                                                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                            @else
                                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                                            @endif
                                        </svg>
                                        Profit
                                    </span>
                                    <span class="pl-profit-val {{ $isLoss ? 'loss' : ($isLowMargin ? 'low' : 'ok') }}">
                                        {{ $retailProfit >= 0 ? '+' : '' }}₱{{ number_format($retailProfit, 2) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="pl-price-simple">
                                ₱{{ number_format($product->price, 2) }}
                            </div>
                            <div class="pl-no-cost">
                                ⚠ Set cost to see profit
                            </div>
                        @endif
                    </div>
                </a>

                {{-- ACTIONS --}}
                <div class="pl-actions">
                    <div class="pl-margin-chip {{ $isLoss ? 'loss' : ($isLowMargin ? 'low' : 'ok') }}">
                        @if($isLoss)
                            LOSS
                        @else
                            {{ number_format($margin, 1) }}% margin
                        @endif
                    </div>

                    <div class="pl-menu-wrap">
                        <button type="button" class="pl-menu-btn" onclick="plToggleMenu(event, {{ $product->id }})">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="2"/>
                                <circle cx="12" cy="12" r="2"/>
                                <circle cx="12" cy="19" r="2"/>
                            </svg>
                        </button>
                        <div class="pl-menu" id="pl-menu-{{ $product->id }}">
                            <a href="{{ route('products.show', $product) }}" class="pl-menu-item">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="pl-menu-item">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>
                            <button type="button" class="pl-menu-item danger" onclick="plDeleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- PAGINATION --}}
    @if($products->hasPages())
        <div class="pl-pagination">
            {{ $products->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    @endif
@endif

{{-- DELETE FORM --}}
<form id="plDeleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
    /* ═══════════════════════════════════════════════
       PRODUCT LIST — PRO DESIGN
       ═══════════════════════════════════════════════ */

    /* KPI CARDS */
    .pl-kpis {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .pl-kpi {
        position: relative;
        padding: 16px 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .pl-kpi::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
    }
    .pl-kpi-primary::before { background: linear-gradient(90deg, #c9a961, transparent); }
    .pl-kpi-amber::before { background: linear-gradient(90deg, #f59e0b, transparent); }
    .pl-kpi-red::before { background: linear-gradient(90deg, #ef4444, transparent); }
    .pl-kpi-green::before { background: linear-gradient(90deg, #22c55e, transparent); }

    .pl-kpi:hover {
        transform: translateY(-2px);
        border-color: rgba(201, 169, 97, 0.25);
        box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.6);
    }

    .pl-kpi-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .pl-kpi-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .pl-kpi-primary .pl-kpi-icon { background: rgba(201, 169, 97, 0.15); color: #c9a961; }
    .pl-kpi-amber .pl-kpi-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .pl-kpi-red .pl-kpi-icon { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .pl-kpi-green .pl-kpi-icon { background: rgba(34, 197, 94, 0.15); color: #22c55e; }

    .pl-kpi-label {
        font-size: 10.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .pl-kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.03em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        margin-bottom: 6px;
    }
    .pl-kpi-foot {
        font-size: 11px;
        color: #71717a;
    }

    /* TOOLBAR */
    .pl-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .pl-title {
        font-size: 20px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        margin-bottom: 3px;
    }
    .pl-subtitle {
        font-size: 12px;
        color: #71717a;
    }
    .pl-filtered {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        background: rgba(201, 169, 97, 0.15);
        color: #c9a961;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .pl-toolbar-actions { display: flex; gap: 10px; align-items: center; }

    .pl-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-radius: 11px;
        color: #0f0f14;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.5);
        transition: all 0.18s;
        white-space: nowrap;
    }
    .pl-add-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(201, 169, 97, 0.7);
    }
    .pl-add-btn:active { transform: scale(0.97); }

    /* FILTERS */
    .pl-filters {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: nowrap;
        align-items: center;
        padding: 12px 14px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
        overflow-x: auto;
    }

    .pl-search-wrap {
        position: relative;
        flex: 1 1 200px;
        min-width: 160px;
        max-width: 400px;
    }
    .pl-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #71717a;
        pointer-events: none;
    }
    .pl-search {
        width: 100%;
        padding: 11px 14px 11px 40px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa;
        font-size: 13px;
        font-family: inherit;
        transition: all 0.18s;
    }
    .pl-search:focus {
        outline: none;
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
        background: rgba(201, 169, 97, 0.03);
    }
    .pl-search::placeholder { color: #52525b; }

    .pl-search-clear {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 22px; height: 22px;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        color: #a1a1aa;
        text-decoration: none;
        transition: all 0.15s;
    }
    .pl-search-clear:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    .pl-select {
        flex: 0 0 auto;
        width: 160px;
        padding: 11px 36px 11px 14px;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #fafafa;
        font-size: 12.5px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.18s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%2371717a' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        white-space: nowrap;
    }
    .pl-select:focus {
        outline: none;
        border-color: #c9a961;
        box-shadow: 0 0 0 3px rgba(201, 169, 97, 0.15);
    }

    .pl-filter-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        flex: 0 0 auto;
        padding: 11px 18px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border: none;
        border-radius: 11px;
        color: #0f0f14;
        font-size: 12.5px;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .pl-filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.6);
    }

    .pl-clear-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        padding: 11px 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 11px;
        color: #d4d4d8;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .pl-clear-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }

    /* PRODUCTS GRID */
    .pl-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .pl-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .pl-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 16px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.5), transparent 50%);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.22s;
        pointer-events: none;
    }
    .pl-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px -16px rgba(0, 0, 0, 0.7);
        border-color: rgba(201, 169, 97, 0.25);
    }
    .pl-card:hover::before { opacity: 1; }

    .pl-card-link {
        display: block;
        text-decoration: none;
        color: inherit;
        flex: 1;
    }

    /* MEDIA */
    .pl-media {
        position: relative;
        aspect-ratio: 4/3;
        background: rgba(0, 0, 0, 0.4);
        overflow: hidden;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .pl-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pl-card:hover .pl-image { transform: scale(1.06); }

    .pl-placeholder {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: rgba(201, 169, 97, 0.3);
        background: radial-gradient(circle at center, rgba(201, 169, 97, 0.05), transparent 70%);
    }

    .pl-stock-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 10px;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        backdrop-filter: blur(8px);
        border: 1px solid;
    }
    .pl-stock-badge.in {
        background: rgba(34, 197, 94, 0.9);
        color: #fff;
        border-color: rgba(34, 197, 94, 0.5);
    }
    .pl-stock-badge.low {
        background: rgba(245, 158, 11, 0.9);
        color: #fff;
        border-color: rgba(245, 158, 11, 0.5);
    }
    .pl-stock-badge.out {
        background: rgba(239, 68, 68, 0.9);
        color: #fff;
        border-color: rgba(239, 68, 68, 0.5);
    }

    .pl-loss-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 10px;
        background: rgba(239, 68, 68, 0.9);
        color: #fff;
        font-size: 9.5px;
        font-weight: 900;
        letter-spacing: 0.1em;
        border-radius: 100px;
        backdrop-filter: blur(8px);
    }

    /* BODY */
    .pl-body {
        padding: 14px 16px 12px;
    }
    .pl-name {
        font-size: 14px;
        font-weight: 800;
        color: #fafafa;
        margin-bottom: 4px;
        letter-spacing: -0.01em;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 36px;
    }
    .pl-sku {
        font-size: 10.5px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        margin-bottom: 12px;
    }

    /* PRICING */
    .pl-pricing {
        padding: 10px 12px;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 11px;
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .pl-pricing-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
    }
    .pl-pricing-main {
        padding-top: 7px;
        margin-top: 2px;
        border-top: 1px dashed rgba(201, 169, 97, 0.2);
    }

    .pl-pricing-lbl {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 700;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .pl-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .pl-dot.cost { background: #71717a; }
    .pl-dot.wholesale { background: #3b82f6; }
    .pl-dot.retail { background: #c9a961; }

    .pl-pricing-val {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
    }
    .pl-pricing-main .pl-pricing-val {
        font-size: 14px;
        color: #c9a961;
        font-weight: 800;
    }

    .pl-tag {
        padding: 2px 7px;
        border-radius: 100px;
        font-size: 9.5px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        white-space: nowrap;
    }
    .pl-tag.ok { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .pl-tag.loss { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    /* PROFIT ROW */
    .pl-profit-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 10px;
        margin-top: 3px;
        border-radius: 8px;
        background: rgba(34, 197, 94, 0.06);
        border: 1px solid rgba(34, 197, 94, 0.15);
    }
    .pl-profit-row:has(.pl-profit-val.low) {
        background: rgba(245, 158, 11, 0.06);
        border-color: rgba(245, 158, 11, 0.15);
    }
    .pl-profit-row:has(.pl-profit-val.loss) {
        background: rgba(239, 68, 68, 0.06);
        border-color: rgba(239, 68, 68, 0.15);
    }
    .pl-profit-lbl {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 9.5px;
        font-weight: 800;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .pl-profit-val {
        font-size: 12.5px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }
    .pl-profit-val.ok { color: #22c55e; }
    .pl-profit-val.low { color: #f59e0b; }
    .pl-profit-val.loss { color: #ef4444; }

    .pl-price-simple {
        font-size: 20px;
        font-weight: 800;
        color: #c9a961;
        text-align: center;
        font-variant-numeric: tabular-nums;
        padding: 8px 0;
    }
    .pl-no-cost {
        font-size: 10.5px;
        color: #f59e0b;
        text-align: center;
        padding: 6px 10px;
        background: rgba(245, 158, 11, 0.06);
        border: 1px dashed rgba(245, 158, 11, 0.25);
        border-radius: 8px;
        margin-top: 6px;
        font-weight: 600;
    }

    /* ACTIONS */
    .pl-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(0, 0, 0, 0.15);
    }

    .pl-margin-chip {
        padding: 4px 10px;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border: 1px solid;
        font-family: ui-monospace, monospace;
    }
    .pl-margin-chip.ok {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.3);
    }
    .pl-margin-chip.low {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.3);
    }
    .pl-margin-chip.loss {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.3);
    }

    .pl-menu-wrap { position: relative; }

    .pl-menu-btn {
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        color: #a1a1aa;
        cursor: pointer;
        transition: all 0.15s;
    }
    .pl-menu-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }

    .pl-menu {
        position: absolute;
        bottom: calc(100% + 6px);
        right: 0;
        min-width: 150px;
        padding: 6px;
        background: #1e1a16;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        box-shadow: 0 12px 32px -8px rgba(0, 0, 0, 0.8);
        z-index: 100;
        display: none;
        animation: plMenuIn 0.15s ease-out;
    }
    @keyframes plMenuIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .pl-menu.open { display: block; }

    .pl-menu-item {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        color: #d4d4d8;
        text-decoration: none;
        border: none;
        background: none;
        font-family: inherit;
        cursor: pointer;
        text-align: left;
        transition: all 0.15s;
    }
    .pl-menu-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fafafa;
    }
    .pl-menu-item.danger { color: #f87171; }
    .pl-menu-item.danger:hover { background: rgba(239, 68, 68, 0.1); color: #fca5a5; }

    /* EMPTY STATE */
    .pl-empty {
        text-align: center;
        padding: 80px 24px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.6), rgba(21, 18, 15, 0.6));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
    }
    .pl-empty-icon {
        width: 88px; height: 88px;
        margin: 0 auto 20px;
        border-radius: 24px;
        background: rgba(201, 169, 97, 0.08);
        border: 1px solid rgba(201, 169, 97, 0.15);
        display: grid;
        place-items: center;
        color: rgba(201, 169, 97, 0.5);
    }
    .pl-empty-title {
        font-size: 18px;
        font-weight: 800;
        color: #fafafa;
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }
    .pl-empty-desc {
        font-size: 13px;
        color: #71717a;
        margin-bottom: 24px;
    }

    /* PAGINATION */
    .pl-pagination {
        display: flex;
        justify-content: center;
        margin-top: 28px;
    }
    .pl-pagination nav {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
    }
    .pl-pagination nav > div:first-child,
    .pl-pagination nav > div:last-child {
        display: none; /* hide "Showing X to Y of Z" text if present */
    }
    .pl-pagination a,
    .pl-pagination span {
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
        color: #a1a1aa;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pl-pagination a:hover {
        background: rgba(201, 169, 97, 0.12);
        border-color: rgba(201, 169, 97, 0.3);
        color: #c9a961;
        transform: translateY(-1px);
    }
    .pl-pagination span[aria-current="page"] {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-color: transparent;
        color: #0f0f14;
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.5);
        transform: translateY(-1px);
    }
    .pl-pagination span[aria-disabled="true"],
    .pl-pagination span.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.04);
    }
    .pl-pagination svg {
        width: 14px;
        height: 14px;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .pl-kpis { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .pl-filters {
            flex-wrap: wrap;
        }
        .pl-search-wrap {
            flex: 1 1 100%;
            max-width: 100%;
        }
        .pl-select {
            flex: 1 1 calc(50% - 5px);
            width: auto;
        }
    }
    @media (max-width: 700px) {
        .pl-kpis { grid-template-columns: 1fr; }
        .pl-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; }
        .pl-filters { flex-direction: column; align-items: stretch; }
        .pl-search-wrap { flex: 1 1 auto; max-width: 100%; }
        .pl-select, .pl-filter-btn, .pl-clear-btn { width: 100%; flex: 1 1 auto; }
        .pl-title { font-size: 17px; }
        .pl-kpi-value { font-size: 20px; }
        .pl-toolbar { flex-direction: column; align-items: stretch; }
        .pl-add-btn { justify-content: center; }
    }
</style>
@endpush

@push('scripts')
<script>
    function plToggleMenu(event, id) {
        event.preventDefault();
        event.stopPropagation();

        document.querySelectorAll('.pl-menu.open').forEach(function(m) {
            if (m.id !== 'pl-menu-' + id) m.classList.remove('open');
        });

        var menu = document.getElementById('pl-menu-' + id);
        if (menu) menu.classList.toggle('open');
    }

    function plDeleteProduct(id, name) {
        if (!confirm('Delete "' + name + '"? This cannot be undone.')) return;
        var form = document.getElementById('plDeleteForm');
        form.action = '{{ route("products.index") }}/' + id;
        form.submit();
    }

    document.addEventListener('click', function() {
        document.querySelectorAll('.pl-menu.open').forEach(function(m) {
            m.classList.remove('open');
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.pl-menu.open').forEach(function(m) {
                m.classList.remove('open');
            });
        }
    });
</script>
@endpush