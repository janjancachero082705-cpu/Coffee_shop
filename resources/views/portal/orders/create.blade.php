@extends('portal.layouts.app')

@section('title', 'New Order')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $products = \App\Models\Product::where('is_active', true)->orderByDesc('is_featured')->orderBy('name')->get();
    $categories = \App\Models\Category::orderBy('name')->get();
    $selectedCategory = request('category');
@endphp

{{-- ===== HEADER ===== --}}
<div class="order-head">
    <div>
        <div class="order-head-label">Order Stock</div>
        <div class="order-head-title">Browse Products</div>
    </div>
    <div class="order-cart-badge" onclick="toggleCart()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="9" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span id="headerCartCount" style="display:none;">0</span>
    </div>
</div>

{{-- ===== SEARCH ===== --}}
<div class="order-search">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/>
        <path d="M21 21l-4.35-4.35"/>
    </svg>
    <input type="text" id="orderSearch" placeholder="Search products..." oninput="filterProducts(this.value)">
    <button type="button" class="order-search-clear" id="searchClear" style="display:none;" onclick="clearSearch()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- ===== CATEGORY TABS ===== --}}
<div class="order-tabs">
    <button type="button" class="order-tab active" data-category="all" onclick="filterCategory('all', this)">
        All
    </button>
    @foreach($categories as $cat)
        <button type="button" class="order-tab" data-category="{{ $cat->id }}" onclick="filterCategory('{{ $cat->id }}', this)">
            {{ $cat->name }}
        </button>
    @endforeach
</div>

{{-- ===== PRODUCTS GRID ===== --}}
<div class="products-scroll" id="productsGrid">
    @forelse($products as $product)
        @php
            $stockLeft = $product->stock ?? 0;
            $stockStatus = $stockLeft <= 0 ? 'out' : ($stockLeft <= ($product->reorder_level ?? 5) ? 'low' : 'ok');
        @endphp
        <div class="product-card"
             data-id="{{ $product->id }}"
             data-name="{{ $product->name }}"
             data-category="{{ $product->category_id }}"
             data-price="{{ $product->price }}"
             data-stock="{{ $stockLeft }}"
             data-search="{{ strtolower($product->name . ' ' . $product->sku) }}"
             onclick="openProductModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $stockLeft }}, '{{ $product->image_url ?? '' }}', '{{ addslashes($product->sku ?? '') }}')">

            {{-- IMAGE --}}
            <div class="product-image-wrap">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" class="product-image">
                @else
                    <div class="product-image-fallback">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif

                @if($stockStatus === 'out')
                    <span class="product-badge out">Out of Stock</span>
                @elseif($stockStatus === 'low')
                    <span class="product-badge low">Low: {{ $stockLeft }}</span>
                @else
                    <span class="product-badge ok">Stock: {{ $stockLeft }}</span>
                @endif
            </div>

            {{-- INFO --}}
            <div class="product-info">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-sku">{{ $product->sku }}</div>

                <div class="product-foot">
                    <div class="product-price">
                        <span class="price-currency">&#8369;</span>{{ number_format($product->price, 2) }}
                    </div>
                    <button type="button" class="product-add-btn" onclick="event.stopPropagation(); quickAdd({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $stockLeft }})">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-products">
            <div class="empty-products-icon">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="empty-products-title">No products available</div>
            <div class="empty-products-text">Wala pa'y available products ron.</div>
        </div>
    @endforelse
</div>

{{-- ===== EMPTY SEARCH RESULT ===== --}}
<div class="empty-search" id="emptySearch" style="display:none;">
    <div class="empty-search-icon">
        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>
    </div>
    <div class="empty-products-title">Walay nakit-an</div>
    <div class="empty-products-text">Try a different search term.</div>
</div>

{{-- ===== CART DRAWER ===== --}}
<div class="cart-scrim" id="cartScrim" onclick="toggleCart()"></div>
<div class="cart-drawer" id="cartDrawer">

    {{-- HEADER (FIXED) --}}
    <div class="cart-drawer-head">
        <button type="button" class="cart-drawer-back" onclick="toggleCart()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="cart-drawer-head-text">
            <div class="cart-drawer-title">Your Order</div>
            <div class="cart-drawer-sub" id="cartDrawerCount">0 items</div>
        </div>
    </div>

    {{-- BODY (SCROLLABLE) --}}
    <div class="cart-drawer-body" id="cartDrawerBody">
        <div class="cart-items" id="cartItems"></div>

        <div class="cart-notes">
            <div class="cart-notes-label">Order Notes (optional)</div>
            <textarea id="orderNotes" class="cart-notes-input" rows="2" placeholder="Special instructions..."></textarea>
        </div>
    </div>

    {{-- FOOTER (FIXED) --}}
    <div class="cart-drawer-foot">
        <div class="cart-summary-row">
            <span>Subtotal</span>
            <strong id="cartTotalMini">&#8369;0.00</strong>
        </div>
        <div class="cart-summary-row cart-summary-total">
            <span>Total</span>
            <strong id="cartTotal">&#8369;0.00</strong>
        </div>
        <form id="orderForm" method="POST" action="{{ route('portal.orders.store') }}">
            @csrf
            <div id="hiddenItems"></div>
            <button type="submit" class="cart-submit" id="submitBtn" disabled>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                Submit Order
            </button>
        </form>
    </div>

</div>
{{-- ===== PRODUCT MODAL (SHEET WITH BACK BUTTON) ===== --}}
<div class="pm-overlay" id="pmOverlay">
    <div class="pm-sheet" onclick="event.stopPropagation();">

        {{-- HEADER WITH BACK BUTTON --}}
        <div class="pm-header">
            <button type="button" class="pm-back" onclick="pmClose()" title="Back">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="pm-header-text">
                <div class="pm-header-title">Product Details</div>
                <div class="pm-header-sub">View and order this item</div>
            </div>
            <div class="pm-header-brand">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                    <path d="M6 1v3M10 1v3M14 1v3"/>
                </svg>
            </div>
        </div>

        {{-- BODY --}}
        <div class="pm-body">

            {{-- HERO IMAGE --}}
            <div class="pm-hero" id="pmHero">
                <div class="pm-hero-fallback">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <div class="pm-hero-fallback-text">No image</div>
                </div>
                <span class="pm-stock-badge ok" id="pmStockBadge">Stock: 0</span>
            </div>

            {{-- INFO --}}
            <div class="pm-info">
                <div class="pm-title" id="pmName">Product Name</div>
                <div class="pm-meta-row">
                    <span class="pm-sku-tag" id="pmSkuTag">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="4" y="4" width="16" height="16" rx="2"/>
                            <path d="M4 10h16"/>
                        </svg>
                        <span id="pmSkuText">SKU</span>
                    </span>
                </div>

                <div class="pm-desc" id="pmDesc">
                    Browse this product and add it to your order.
                </div>

                {{-- PRICE --}}
                <div class="pm-price-card">
                    <div>
                        <div class="pm-price-label">Unit Price</div>
                        <div class="pm-price-value" id="pmPrice">&#8369;0.00</div>
                    </div>
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="color: #c9a961; opacity: 0.6;">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>

                {{-- QTY --}}
                <div class="pm-qty-card">
                    <div>
                        <div class="pm-qty-text">Quantity</div>
                        <div class="pm-qty-sub" id="pmQtySub">Max: 0 available</div>
                    </div>
                    <div class="pm-qty-controls">
                        <button type="button" class="pm-qty-btn" onclick="pmQty(-1)">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12h14"/>
                            </svg>
                        </button>
                        <div class="pm-qty-value" id="pmQtyVal">1</div>
                        <button type="button" class="pm-qty-btn" onclick="pmQty(1)">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- SUBTOTAL --}}
                <div class="pm-subtotal-card">
                    <div class="pm-subtotal-label">Subtotal</div>
                    <div class="pm-subtotal-value" id="pmSubtotal">&#8369;0.00</div>
                </div>

                {{-- ADD BUTTON --}}
                <button type="button" class="pm-add-btn" onclick="pmAdd()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Add to Order
                </button>
            </div>
        </div>
    </div>
</div>
{{-- ===== CART DRAWER ===== --}}


    <div class="cart-drawer-body">
        <div class="cart-items" id="cartItems"></div>

        <div class="cart-notes">
            <div class="cart-notes-label">Order Notes (optional)</div>
            <textarea id="orderNotes" class="cart-notes-input" rows="2" placeholder="Special instructions..."></textarea>
        </div>
    </div>

    <div class="cart-drawer-foot">
        <div class="cart-summary-row">
            <span>Subtotal</span>
            <strong id="cartTotalMini">&#8369;0.00</strong>
        </div>
        <div class="cart-summary-row cart-summary-total">
            <span>Total</span>
            <strong id="cartTotal">&#8369;0.00</strong>
        </div>
        <form id="orderForm" method="POST" action="{{ route('portal.orders.store') }}" onsubmit="return handleSubmit(event)">
            @csrf
            <div id="hiddenItems"></div>
            <button type="submit" class="cart-submit" id="submitBtn" disabled>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                Submit Order
            </button>
        </form>
    </div>
</div>


{{-- ===== FLOATING CART BAR ===== --}}
<div class="cart-bar" id="cartBar">
    {{-- EXPANDABLE ITEMS --}}
    <div class="cart-bar-items" id="cartBarItems"></div>

    {{-- CART NOTES (inside expandable) --}}
    <div class="cart-bar-items" id="cartBarNotes" style="display:none;">
        <div class="cart-bar-notes">
            <div class="cart-bar-notes-label">Order Notes (optional)</div>
            <textarea id="orderNotes" class="cart-bar-notes-input" rows="2" placeholder="Special instructions..."></textarea>
        </div>
    </div>

    {{-- TOP BAR: Icon + Total + Submit --}}
    <div class="cart-bar-top">
        <div class="cart-bar-icon" onclick="toggleCartExpand()">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="9" cy="21" r="1"/>
                <circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <span class="cart-bar-count" id="cartBarCount">0</span>
        </div>

        <div class="cart-bar-info" onclick="toggleCartExpand()">
            <div class="cart-bar-label">Total (<span id="cartBarItemsCount">0 items</span>)</div>
            <div class="cart-bar-total" id="cartBarTotal">&#8369;0.00</div>
        </div>

        <button type="button" class="cart-bar-submit" onclick="submitOrder()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
            Submit
        </button>
    </div>
</div>

{{-- HIDDEN FORM --}}
<form id="orderForm" method="POST" action="{{ route('portal.orders.store') }}" style="display:none;">
    @csrf
    <div id="hiddenItems"></div>
    <input type="hidden" name="notes" id="hiddenNotes">
</form>

@endsection

@push('styles')
<style>
    /* ===== HEADER ===== */
    .order-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .order-head-label {
        font-size: 11px;
        font-weight: 800;
        color: #c9a961;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 4px;
    }
    .order-head-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em;
        line-height: 1.15;
    }
    .order-cart-badge {
        position: relative;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid;
        place-items: center;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.7);
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .order-cart-badge:active { transform: scale(0.95); }
    .order-cart-badge span {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        background: #ef4444;
        color: #fff;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 800;
        display: grid;
        place-items: center;
        border: 2px solid #0f0f14;
    }

    /* ===== SEARCH ===== */
    .order-search {
        position: relative;
        display: flex;
        align-items: center;
        margin-bottom: 14px;
        padding: 13px 16px;
        background: rgba(20, 20, 26, 0.55);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        transition: all 0.2s;
    }
    .order-search:focus-within {
        border-color: #c9a961;
        background: rgba(20, 20, 26, 0.85);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.12);
    }
    .order-search svg {
        color: var(--text-muted);
        flex-shrink: 0;
        margin-right: 10px;
    }
    .order-search input {
        flex: 1;
        min-width: 0;
        background: transparent;
        border: none;
        outline: none;
        color: var(--text-primary);
        font-size: 14px;
        font-family: inherit;
    }
    .order-search input::placeholder { color: var(--text-muted); }
    .order-search-clear {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        display: grid;
        place-items: center;
        cursor: pointer;
        flex-shrink: 0;
    }

    /* ===== CATEGORY TABS ===== */
    .order-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 18px;
        padding: 2px 0;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        margin-left: -16px;
        margin-right: -16px;
        padding-left: 16px;
        padding-right: 16px;
    }
    .order-tabs::-webkit-scrollbar { display: none; }
    .order-tab {
        min-height: 36px;
        padding: 0 16px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .order-tab.active {
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 6px 14px -6px rgba(169, 120, 74, 0.6);
    }
    .order-tab:active:not(.active) {
        background: rgba(255, 255, 255, 0.08);
    }

    /* ===== PRODUCTS GRID ===== */
    .products-scroll {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        padding-bottom: 100px;
    }

    .product-card {
        background: rgba(34, 34, 44, 0.4);
        backdrop-filter: blur(24px) saturate(1.5);
        -webkit-backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }
    .product-card:active {
        transform: scale(0.97);
        border-color: rgba(201, 169, 97, 0.4);
    }

    .product-image-wrap {
        position: relative;
        aspect-ratio: 1 / 1;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.6));
        overflow: hidden;
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s;
    }
    .product-card:active .product-image {
        transform: scale(1.05);
    }
    .product-image-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: rgba(201, 169, 97, 0.4);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.02em;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .product-badge.ok {
        background: rgba(34, 197, 94, 0.92);
        color: #fff;
        box-shadow: 0 4px 12px -2px rgba(34, 197, 94, 0.5);
    }
    .product-badge.low {
        background: rgba(245, 158, 11, 0.92);
        color: #fff;
        box-shadow: 0 4px 12px -2px rgba(245, 158, 11, 0.5);
    }
    .product-badge.out {
        background: rgba(239, 68, 68, 0.92);
        color: #fff;
        box-shadow: 0 4px 12px -2px rgba(239, 68, 68, 0.5);
    }

    .product-info {
        padding: 12px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .product-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.3;
        margin-bottom: 3px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 36px;
    }
    .product-sku {
        font-size: 10px;
        color: var(--text-muted);
        font-family: ui-monospace, monospace;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .product-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-top: auto;
    }
    .product-price {
        font-size: 15px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
    }
    .product-price .price-currency {
        font-size: 11px;
        margin-right: 1px;
    }
    .product-add-btn:active {
        transform: scale(0.9);
    }
    .product-add-btn {
        cursor: pointer;
        border: none;
        font-family: inherit;
        transition: all 0.15s;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px -4px rgba(169, 120, 74, 0.7);
    }

    /* ===== EMPTY ===== */
    .empty-products, .empty-search {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
    }
    .empty-products-icon, .empty-search-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 16px;
        border-radius: 20px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        display: grid;
        place-items: center;
        color: #c9a961;
    }
    .empty-products-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .empty-products-text {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* ===== COMPACT CART FAB ===== */
    .cart-fab {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9998;
        display: none;
        align-items: center;
        gap: 10px;
        padding: 12px 18px 12px 14px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none;
        border-radius: 50px;
        color: #fff;
        font-family: inherit;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 12px 30px -10px rgba(169, 120, 74, 1),
                    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        transition: all 0.35s cubic-bezier(0.34, 1.2, 0.64, 1);
        -webkit-tap-highlight-color: transparent;
        animation: fabPop 0.35s cubic-bezier(0.34, 1.4, 0.64, 1);
    }
    .cart-fab.visible {
        display: inline-flex;
    }
    .cart-fab:active {
        transform: scale(0.95);
    }
    @keyframes fabPop {
        0% { transform: translateY(80px) scale(0.6); opacity: 0; }
        100% { transform: translateY(0) scale(1); opacity: 1; }
    }

    .cart-fab-icon {
        position: relative;
        width: 26px;
        height: 26px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .cart-fab-count {
        position: absolute;
        top: -8px;
        right: -8px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        background: #ef4444;
        color: #fff;
        border-radius: 9px;
        font-size: 10px;
        font-weight: 800;
        display: grid;
        place-items: center;
        border: 2px solid #0f0f14;
        box-shadow: 0 4px 12px -2px rgba(239, 68, 68, 0.8);
    }
    .cart-fab-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1;
    }
    .cart-fab-label {
        font-size: 9.5px;
        font-weight: 700;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 3px;
    }
    .cart-fab-total {
        font-size: 15px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
/* ===== PRODUCT MODAL (COMPACT) ===== */
    .pm-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(8, 8, 12, 0.88);
        z-index: 99999;
        display: none;
        will-change: opacity;
    }
    .pm-overlay.show { display: block; animation: fadeIn 0.25s ease; }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .pm-sheet {
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 100%;
        max-width: 620px;
        background-color: #0f0f14;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        will-change: transform;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        box-shadow: 12px 0 60px rgba(0, 0, 0, 0.9);
        transform: translateX(-100%) translateZ(0);
        transition: transform 0.4s cubic-bezier(0.34, 1.1, 0.64, 1);
        display: flex;
        flex-direction: column;
        border-radius: 0;
    }

    /* Fixed gradient background sa pm-sheet */
    .pm-sheet::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 0;
        background-image:
            radial-gradient(circle at 100% 0%, rgba(201, 169, 97, 0.12) 0%, transparent 45%),
            linear-gradient(180deg, rgba(15, 15, 20, 0.98), rgba(10, 10, 16, 1));
        pointer-events: none;
    }

    .pm-sheet > * {
        position: relative;
        z-index: 1;
    }
    .pm-overlay.show .pm-sheet {
        transform: translateX(0);
    }

    /* ===== HEADER — COMPACT ===== */
    .pm-header {
        position: sticky;
        top: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: rgba(15, 15, 20, 0.95);
        backdrop-filter: blur(28px);
        -webkit-backdrop-filter: blur(28px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        flex-shrink: 0;
    }
    .pm-back {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.3);
        color: #c9a961;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        flex-shrink: 0;
        transition: all 0.2s;
        -webkit-tap-highlight-color: transparent;
    }
    .pm-back:active {
        background: rgba(169, 120, 74, 0.25);
        transform: scale(0.95);
    }
    .pm-back svg { display: block; }

    .pm-header-text { flex: 1; min-width: 0; }
    .pm-header-title {
        font-size: 14px;
        font-weight: 800;
        color: #f5f3f0;
        letter-spacing: -0.01em;
        line-height: 1.2;
    }
    .pm-header-sub {
        font-size: 10.5px;
        color: #6b6862;
        margin-top: 1px;
        font-weight: 500;
    }
    .pm-header-brand {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid;
        place-items: center;
        color: #fff;
        box-shadow: 0 6px 14px -4px rgba(169, 120, 74, 0.5);
        flex-shrink: 0;
    }

    /* ===== BODY ===== */
    .pm-body {
        padding: 0 0 16px;
        flex: 1;
    }

    /* ===== HERO IMAGE — COMPACT ===== */
    .pm-hero {
        position: relative;
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.7));
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pm-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .pm-hero-fallback {
        color: rgba(201, 169, 97, 0.35);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .pm-hero-fallback-text {
        font-size: 10px;
        color: rgba(201, 169, 97, 0.5);
        text-transform: uppercase;
        letter-spacing: 0.15em;
        font-weight: 700;
    }

    /* Stock badge */
    .pm-stock-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 10px;
        border-radius: 7px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.02em;
        backdrop-filter: blur(12px);
    }
    .pm-stock-badge.ok {
        background: rgba(34, 197, 94, 0.95);
        color: #fff;
    }
    .pm-stock-badge.low {
        background: rgba(245, 158, 11, 0.95);
        color: #fff;
    }
    .pm-stock-badge.out {
        background: rgba(239, 68, 68, 0.95);
        color: #fff;
    }

    /* ===== INFO ===== */
    .pm-info { padding: 14px 14px 0; }

    .pm-title {
        font-size: 18px;
        font-weight: 800;
        color: #f5f3f0;
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pm-meta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .pm-sku-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 6px;
        font-family: ui-monospace, monospace;
        font-size: 10px;
        color: #c9a961;
        font-weight: 700;
    }
    .pm-sku-tag svg { width: 10px; height: 10px; }

    .pm-desc {
        font-size: 12px;
        color: #a8a5a0;
        line-height: 1.5;
        margin-bottom: 14px;
    }

    /* ===== PRICE — COMPACT ===== */
    .pm-price-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 14px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.15), rgba(138, 95, 54, 0.08));
        border: 1px solid rgba(201, 169, 97, 0.25);
        border-radius: 12px;
        margin-bottom: 10px;
    }
    .pm-price-label {
        font-size: 9.5px;
        font-weight: 800;
        color: #6b6862;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 2px;
    }
    .pm-price-value {
        font-size: 22px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: -0.03em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .pm-price-card svg {
        width: 24px;
        height: 24px;
    }

    /* ===== QTY — COMPACT ===== */
    .pm-qty-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        background: rgba(34, 34, 44, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        margin-bottom: 10px;
    }
    .pm-qty-text {
        font-size: 12.5px;
        font-weight: 700;
        color: #f5f3f0;
    }
    .pm-qty-sub {
        font-size: 10.5px;
        color: #6b6862;
        margin-top: 1px;
    }
    .pm-qty-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pm-qty-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.15);
        border: 1px solid rgba(169, 120, 74, 0.3);
        color: #c9a961;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .pm-qty-btn:active {
        background: rgba(169, 120, 74, 0.35);
        transform: scale(0.92);
    }
    .pm-qty-btn svg {
        width: 16px;
        height: 16px;
    }
    .pm-qty-value {
        min-width: 38px;
        text-align: center;
        font-size: 18px;
        font-weight: 800;
        color: #f5f3f0;
        font-variant-numeric: tabular-nums;
    }

    /* ===== SUBTOTAL — COMPACT ===== */
    .pm-subtotal-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 14px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        margin-bottom: 12px;
    }
    .pm-subtotal-label {
        font-size: 11px;
        color: #6b6862;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .pm-subtotal-value {
        font-size: 18px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    /* ===== ADD BUTTON — COMPACT ===== */
    .pm-add-btn {
        width: 100%;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none;
        border-radius: 13px;
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 10px 24px -8px rgba(169, 120, 74, 0.9);
        padding: 0 18px;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .pm-add-btn:active {
        transform: scale(0.98);
    }
    .pm-add-btn svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* ===== DESKTOP CENTER ===== */
    @media (min-width: 720px) {
        .pm-overlay {
            display: none;
            align-items: center;
            justify-content: center;
        }
        .pm-overlay.show { display: flex; }
        .pm-sheet {
            position: relative;
            top: auto; left: auto; bottom: auto;
            border-radius: 24px;
            max-height: 85vh;
            transform: scale(0.9) translateY(20px);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.34, 1.1, 0.64, 1), opacity 0.3s;
        }
        .pm-overlay.show .pm-sheet {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        .pm-hero { height: 240px; }
    }
/* ===== CART DRAWER ===== */
    .cart-scrim {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 99998 !important;
        background: rgba(0, 0, 0, 0.75);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }
    .cart-scrim.open {
        opacity: 1;
        visibility: visible;
    }

    /* ===== DRAWER: FIXED + FLEX COLUMN ===== */
    .cart-drawer {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100%;
        max-width: 440px;
        z-index: 99999 !important;
        background: #0f0f14;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.34, 1.1, 0.64, 1);
        box-shadow: -20px 0 60px rgba(0, 0, 0, 0.9);
    }
    .cart-drawer.open {
        transform: translateX(0);
    }

    /* ===== HEADER: FIXED SIZE (dili mo-shrink) ===== */
    .cart-drawer-head {
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #0f0f14;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .cart-drawer-back {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.28);
        color: #c9a961;
        display: grid;
        place-items: center;
        cursor: pointer;
        padding: 0;
        flex-shrink: 0;
    }
    .cart-drawer-back svg { display: block; }
    .cart-drawer-head-text { flex: 1; min-width: 0; }
    .cart-drawer-title {
        font-size: 15px;
        font-weight: 800;
        color: #f5f3f0;
        line-height: 1.2;
    }
    .cart-drawer-sub {
        font-size: 11px;
        color: #6b6862;
        margin-top: 1px;
    }

    /* ===== BODY: TAKES REMAINING SPACE + SCROLLS ===== */
    .cart-drawer-body {
        flex: 1 1 0% !important;
        min-height: 0 !important;
        overflow-y: auto !important;
        overflow-x: hidden;
        padding: 14px 16px;
        -webkit-overflow-scrolling: touch;
    }
    .cart-drawer-body::-webkit-scrollbar { width: 4px; }
    .cart-drawer-body::-webkit-scrollbar-thumb {
        background: rgba(201, 169, 97, 0.3);
        border-radius: 2px;
    }

    .cart-items { display: flex; flex-direction: column; gap: 8px; }
    .cart-empty { text-align: center; padding: 40px 20px; }
    .cart-empty-icon { font-size: 40px; opacity: 0.4; margin-bottom: 12px; }
    .cart-empty-text {
        font-size: 15px; font-weight: 700; color: #f5f3f0; margin-bottom: 6px;
    }
    .cart-empty-sub { font-size: 12.5px; color: #6b6862; }

    .cart-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: rgba(34, 34, 44, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
    }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name {
        font-size: 12.5px;
        font-weight: 700;
        color: #f5f3f0;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cart-item-meta { font-size: 10.5px; color: #6b6862; }
    .cart-item-price {
        font-size: 12.5px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }
    .cart-item-remove {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        display: grid;
        place-items: center;
        cursor: pointer;
        flex-shrink: 0;
        padding: 0;
    }

    .cart-notes {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }
    .cart-notes-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #6b6862;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 6px;
    }
    .cart-notes-input {
        width: 100%;
        padding: 10px 12px;
        background: rgba(20, 20, 26, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #f5f3f0;
        font-size: 12.5px;
        font-family: inherit;
        outline: none;
        resize: none;
    }

    /* ===== FOOTER: FIXED SIZE (dili mo-shrink) ===== */
    .cart-drawer-foot {
        flex: 0 0 auto !important;
        padding: 14px 16px 20px;
        background: #0f0f14;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 -10px 30px -10px rgba(0, 0, 0, 0.6);
    }
    .cart-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12.5px;
        color: #a8a5a0;
        padding: 4px 0;
    }
    .cart-summary-row strong {
        color: #f5f3f0;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .cart-summary-total {
        padding-top: 8px;
        margin-top: 4px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 13.5px;
    }
    .cart-summary-total strong {
        font-size: 20px;
        color: #c9a961;
        font-weight: 800;
    }

    .cart-submit {
        width: 100%;
        min-height: 52px;
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none;
        border-radius: 14px;
        color: #fff;
        font-size: 14.5px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 12px 28px -10px rgba(169, 120, 74, 0.9);
        padding: 0 20px;
    }
    .cart-submit:disabled {
        opacity: 0.35;
        cursor: not-allowed;
        box-shadow: none;
    }
    .cart-submit svg { width: 16px; height: 16px; }
/* ===== RESPONSIVE ===== */
    @media (min-width: 720px) {
        .products-scroll {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ===== STATE =====
    var cart = {};
    var currentProduct = null;

    // ===== PRODUCT MODAL =====
    var pmData = null;
    var pmQtyNum = 1;

    function openProductModal(id, name, price, stock, image, sku) {
        if (stock <= 0) {
            showToast('Out of stock');
            return;
        }

        pmData = {
            id: id,
            name: name,
            price: parseFloat(price),
            stock: parseInt(stock)
        };
        pmQtyNum = 1;

        // Populate text fields
        document.getElementById('pmName').textContent = name;
        document.getElementById('pmSkuText').textContent = sku ? sku : 'N/A';
        document.getElementById('pmPrice').textContent = '\u20B1' + formatMoney(price);
        document.getElementById('pmQtyVal').textContent = '1';
        document.getElementById('pmQtySub').textContent = 'Max: ' + stock + ' available';

        // Stock badge
        var stockBadge = document.getElementById('pmStockBadge');
        var status = 'ok';
        if (stock <= 0) status = 'out';
        else if (stock <= 5) status = 'low';
        stockBadge.className = 'pm-stock-badge ' + status;
        stockBadge.textContent = 'Stock: ' + stock;

        // Hero image
        var hero = document.getElementById('pmHero');
        if (image) {
            hero.innerHTML = '<img src="' + image + '" alt="' + name + '">'
                + '<span class="pm-stock-badge ' + status + '" id="pmStockBadge">Stock: ' + stock + '</span>';
        } else {
            hero.innerHTML = '<div class="pm-hero-fallback">'
                + '<svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'
                + '<div class="pm-hero-fallback-text">No image</div>'
                + '</div>'
                + '<span class="pm-stock-badge ' + status + '" id="pmStockBadge">Stock: ' + stock + '</span>';
        }

        pmUpdateSubtotal();

        // Show
        var overlay = document.getElementById('pmOverlay');
        overlay.classList.add('show');
        document.body.classList.add('no-scroll');
    }

    function pmClose() {
        var overlay = document.getElementById('pmOverlay');
        if (overlay) overlay.classList.remove('show');
        document.body.classList.remove('no-scroll');
        pmData = null;
        pmQtyNum = 1;
    }

    function pmQty(delta) {
        if (!pmData) return;
        var next = pmQtyNum + delta;
        if (next < 1) next = 1;
        if (next > pmData.stock) next = pmData.stock;
        pmQtyNum = next;
        document.getElementById('pmQtyVal').textContent = pmQtyNum;
        pmUpdateSubtotal();
    }

    function pmUpdateSubtotal() {
        if (!pmData) return;
        var subtotal = pmQtyNum * pmData.price;
        document.getElementById('pmSubtotal').textContent = '\u20B1' + formatMoney(subtotal);
    }

    function pmAdd() {
        if (!pmData) return;

        var id = pmData.id;
        var qty = pmQtyNum;

        if (cart[id]) {
            cart[id].qty += qty;
            cart[id].subtotal = cart[id].qty * cart[id].price;
        } else {
            cart[id] = {
                product_id: id,
                name: pmData.name,
                qty: qty,
                price: pmData.price,
                subtotal: qty * pmData.price
            };
        }

        var addedName = pmData.name;
        pmClose();
        renderCart();
        showToast(addedName + ' added');
    }

    function changeQty(delta) {
        var input = document.getElementById('modalQty');
        var max = parseInt(input.dataset.max) || 1;
        var current = parseInt(input.value) || 1;
        var newVal = current + delta;

        if (newVal < 1) newVal = 1;
        if (newVal > max) newVal = max;

        input.value = newVal;
        updateModalSubtotal();
    }

    function updateModalSubtotal() {
        var qty = parseInt(document.getElementById('modalQty').value) || 0;
        if (!currentProduct) return;
        var subtotal = qty * currentProduct.price;
        document.getElementById('modalSubtotal').textContent = '\u20B1' + formatMoney(subtotal);
    }

    function addToCart() {
        if (!currentProduct) return;

        var qty = parseInt(document.getElementById('modalQty').value) || 1;

        if (cart[currentProduct.id]) {
            cart[currentProduct.id].qty += qty;
            cart[currentProduct.id].subtotal = cart[currentProduct.id].qty * cart[currentProduct.id].price;
        } else {
            cart[currentProduct.id] = {
                product_id: currentProduct.id,
                name: currentProduct.name,
                qty: qty,
                price: currentProduct.price,
                subtotal: qty * currentProduct.price
            };
        }

        closeProductModal();
        renderCart();
        showToast(currentProduct.name + ' added');
    }

    function removeFromCart(productId) {
        delete cart[productId];
        renderCart();
    }

    function renderCart() {
        var items = Object.values(cart);
        var bar = document.getElementById('cartBar');
        var barItems = document.getElementById('cartBarItems');
        var barNotes = document.getElementById('cartBarNotes');
        var barCount = document.getElementById('cartBarCount');
        var barTotal = document.getElementById('cartBarTotal');
        var barItemsCount = document.getElementById('cartBarItemsCount');
        var hiddenEl = document.getElementById('hiddenItems');

        var totalQty = 0;
        var totalAmount = 0;

        if (items.length === 0) {
            bar.classList.remove('visible', 'expanded');
            hiddenEl.innerHTML = '';
            return;
        }

        var html = '';
        var hiddenHtml = '';

        items.forEach(function(item, index) {
            html += '<div class="cart-bar-item">'
                + '<div class="cart-bar-item-info">'
                + '<div class="cart-bar-item-name">' + escapeHtml(item.name) + '</div>'
                + '<div class="cart-bar-item-meta">' + item.qty + ' × \u20B1' + formatMoney(item.price) + '</div>'
                + '</div>'
                + '<div class="cart-bar-item-price">\u20B1' + formatMoney(item.subtotal) + '</div>'
                + '<button type="button" class="cart-bar-item-remove" onclick="removeFromCart(' + item.product_id + ')">'
                + '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>'
                + '</button></div>';

            hiddenHtml += '<input type="hidden" name="items[' + index + '][product_id]" value="' + item.product_id + '">';
            hiddenHtml += '<input type="hidden" name="items[' + index + '][quantity]" value="' + item.qty + '">';

            totalQty += item.qty;
            totalAmount += item.subtotal;
        });

        barItems.innerHTML = html;
        hiddenEl.innerHTML = hiddenHtml;

        bar.classList.add('visible');
        barCount.textContent = totalQty > 99 ? '99+' : totalQty;
        barTotal.textContent = '\u20B1' + formatMoney(totalAmount);
        barItemsCount.textContent = totalQty + ' item' + (totalQty > 1 ? 's' : '');

        // Notes section visible kung naay items
        if (barNotes) barNotes.style.display = 'block';
    }

    function toggleCartExpand() {
        var bar = document.getElementById('cartBar');
        bar.classList.toggle('expanded');
    }

    function submitOrder() {
        if (Object.keys(cart).length === 0) return;

        var btn = document.getElementById('submitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Submitting...';
        }

        var notes = document.getElementById('orderNotes');
        if (notes && notes.value.trim()) {
            var notesInput = document.createElement('input');
            notesInput.type = 'hidden';
            notesInput.name = 'notes';
            notesInput.value = notes.value;
            document.getElementById('orderForm').appendChild(notesInput);
        }

        document.getElementById('orderForm').submit();
    }

    function toggleCart() {
        var drawer = document.getElementById('cartDrawer');
        var scrim = document.getElementById('cartScrim');
        if (drawer) drawer.classList.toggle('open');
        if (scrim) scrim.classList.toggle('open');
    }

    // ===== SEARCH & FILTER =====
    function filterProducts(query) {
        var q = query.toLowerCase().trim();
        var cards = document.querySelectorAll('.product-card');
        var visibleCount = 0;

        cards.forEach(function(card) {
            var searchText = card.dataset.search || '';
            var visible = q === '' || searchText.indexOf(q) !== -1;
            card.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        document.getElementById('emptySearch').style.display = visibleCount === 0 ? 'block' : 'none';
        document.getElementById('searchClear').style.display = q ? 'grid' : 'none';
    }

    function clearSearch() {
        document.getElementById('orderSearch').value = '';
        filterProducts('');
    }

    function filterCategory(catId, btn) {
        document.querySelectorAll('.order-tab').forEach(function(t) { t.classList.remove('active'); });
        btn.classList.add('active');

        var cards = document.querySelectorAll('.product-card');
        cards.forEach(function(card) {
            if (catId === 'all' || card.dataset.category === catId) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Reapply search filter
        var q = document.getElementById('orderSearch').value;
        if (q) filterProducts(q);
    }

    // ===== HELPERS =====
    function formatMoney(n) {
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function(c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function showToast(msg) {
        var old = document.getElementById('quickToast');
        if (old) old.remove();

        var t = document.createElement('div');
        t.id = 'quickToast';
        t.style.cssText = 'position:fixed;bottom:120px;left:50%;transform:translateX(-50%) translateY(20px);padding:12px 20px;background:rgba(34,197,94,0.95);backdrop-filter:blur(12px);color:#fff;font-size:13px;font-weight:700;border-radius:12px;z-index:99999;opacity:0;transition:all 0.3s;pointer-events:none;box-shadow:0 12px 30px -8px rgba(34,197,94,0.6);';
        t.textContent = msg;
        document.body.appendChild(t);

        setTimeout(function() {
            t.style.opacity = '1';
            t.style.transform = 'translateX(-50%) translateY(0)';
        }, 10);

        setTimeout(function() {
            t.style.opacity = '0';
            t.style.transform = 'translateX(-50%) translateY(20px)';
            setTimeout(function() { t.remove(); }, 300);
        }, 2000);
    }


    // ===== QUICK ADD (walay modal) =====
    function quickAdd(id, name, price, stock) {
        if (stock <= 0) {
            showToast('Out of stock');
            return;
        }

        price = parseFloat(price);

        if (cart[id]) {
            // Check kung mo-exceed sa stock
            if (cart[id].qty >= stock) {
                showToast('Max stock reached');
                return;
            }
            cart[id].qty += 1;
            cart[id].subtotal = cart[id].qty * cart[id].price;
        } else {
            cart[id] = {
                product_id: id,
                name: name,
                qty: 1,
                price: price,
                subtotal: price
            };
        }

        renderCart();
        showToast(name + ' added');
    }

    // ===== SUBMIT HANDLER =====
    function handleSubmit(e) {
        e.preventDefault();

        if (Object.keys(cart).length === 0) {
            showToast('Walay items sa order');
            return false;
        }

        var btn = document.getElementById('submitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation:spin 0.8s linear infinite;"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Submitting...';
        }

        // Copy notes from drawer textarea to form
        var notes = document.getElementById('orderNotes');
        if (notes && notes.value.trim()) {
            var notesInput = document.createElement('input');
            notesInput.type = 'hidden';
            notesInput.name = 'notes';
            notesInput.value = notes.value;
            document.getElementById('orderForm').appendChild(notesInput);
        }

        // Submit form
        document.getElementById('orderForm').submit();
        return false;
    }

    // ===== SUBMIT BUTTON CLICK =====
    document.addEventListener('DOMContentLoaded', function() {
        var submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                handleSubmit(e);
            });
        }
    });

    // Close modal on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProductModal();
            if (document.getElementById('cartDrawer').classList.contains('open')) toggleCart();
        }
    });

    // ===== MODAL CLICK-OUTSIDE + ESC =====
    document.addEventListener('DOMContentLoaded', function() {
        var overlay = document.getElementById('pmOverlay');
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    pmClose();
                }
            });
        }

        // ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var pm = document.getElementById('pmOverlay');
                if (pm && pm.classList.contains('show')) {
                    pmClose();
                }
            }
        });
    });
</script>
@endpush