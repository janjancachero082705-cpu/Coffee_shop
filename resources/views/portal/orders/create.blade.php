@extends('portal.layouts.app')

@section('title', 'New Order')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $products = \App\Models\Product::where('is_active', true)->orderByDesc('is_featured')->orderBy('name')->get();
    $categories = \App\Models\Category::orderBy('name')->get();
@endphp

{{-- ===== HEADER ===== --}}
<div class="order-head">
    <div class="order-head-left">
        <a href="{{ route('portal.orders.index') }}" class="order-back-btn" aria-label="Back to orders">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="order-head-label">Order Stock</div>
            <div class="order-head-title">Browse Products</div>
        </div>
    </div>
    <button type="button" class="order-cart-badge" onclick="openCart()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="9" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span id="headerCartCount" style="display:none;">0</span>
    </button>
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
    <button type="button" class="order-tab active" data-category="all" onclick="filterCategory('all', this)">All</button>
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

{{-- ===== CART FAB ===== --}}
<button type="button" class="cart-fab" id="cartFab" onclick="openCart()">
    <div class="cart-fab-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="9" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span class="cart-fab-count" id="cartFabCount">0</span>
    </div>
    <div class="cart-fab-info">
        <div class="cart-fab-label">Cart</div>
        <div class="cart-fab-total" id="cartFabTotal">&#8369;0.00</div>
    </div>
</button>

{{-- ===== CART DRAWER ===== --}}
<div class="cart-scrim" id="cartScrim" onclick="closeCart()"></div>
<div class="cart-drawer" id="cartDrawer">
    {{-- HEADER: back + title + SUBMIT --}}
    <div class="cart-drawer-head">
        <button type="button" class="cart-drawer-back" onclick="closeCart()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="cart-drawer-head-text">
            <div class="cart-drawer-title">Your Order</div>
            <div class="cart-drawer-sub" id="cartDrawerCount">0 items</div>
        </div>
        <button type="button" class="cart-submit-header" id="submitBtn" onclick="submitOrder()">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
            <span id="submitBtnText">Submit</span>
        </button>
    </div>

    {{-- BODY: items + notes + TOTAL --}}
    <div class="cart-drawer-body">
        <div class="cart-items" id="cartItems"></div>

        <div class="cart-notes">
            <div class="cart-notes-label">Order Notes (optional)</div>
            <textarea id="orderNotes" class="cart-notes-input" rows="2" placeholder="Special instructions..."></textarea>
        </div>

        {{-- TOTALS sa ilawm sa list --}}
        <div class="cart-totals">
            <div class="cart-total-row">
                <span>Subtotal</span>
                <strong id="cartTotalMini">&#8369;0.00</strong>
            </div>
            <div class="cart-total-row final">
                <span>Total</span>
                <strong id="cartTotal">&#8369;0.00</strong>
            </div>
        </div>
    </div>
</div>
{{-- ===== HIDDEN FORM ===== --}}
<form id="orderForm" method="POST" action="{{ route('portal.orders.store') }}" style="display:none;">
    @csrf
    <div id="hiddenItems"></div>
</form>

{{-- ===== PRODUCT MODAL ===== --}}
<div class="pm-overlay" id="pmOverlay">
    <div class="pm-sheet" onclick="event.stopPropagation();">
        <div class="pm-header">
            <button type="button" class="pm-back" onclick="pmClose()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="pm-header-text">
                <div class="pm-header-title">Product Details</div>
                <div class="pm-header-sub">View and order this item</div>
            </div>
        </div>

        <div class="pm-body">
            <div class="pm-hero" id="pmHero">
                <div class="pm-hero-fallback">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <div class="pm-hero-fallback-text">No image</div>
                </div>
            </div>

            <div class="pm-info">
                <div class="pm-title" id="pmName">Product Name</div>
                <div class="pm-meta-row">
                    <span class="pm-sku-tag">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="4" y="4" width="16" height="16" rx="2"/>
                            <path d="M4 10h16"/>
                        </svg>
                        <span id="pmSkuText">SKU</span>
                    </span>
                </div>

                <div class="pm-desc">Browse this product and add it to your order.</div>

                <div class="pm-price-card">
                    <div>
                        <div class="pm-price-label">Unit Price</div>
                        <div class="pm-price-value" id="pmPrice">&#8369;0.00</div>
                    </div>
                </div>

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

                <div class="pm-subtotal-card">
                    <div class="pm-subtotal-label">Subtotal</div>
                    <div class="pm-subtotal-value" id="pmSubtotal">&#8369;0.00</div>
                </div>

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

@endsection

@push('styles')
<style>
    /* ===== HEADER ===== */
    .order-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; }
    .order-head-label { font-size: 11px; font-weight: 800; color: #c9a961; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 4px; }
    .order-head-title { font-size: 26px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.03em; line-height: 1.15; }
    .order-cart-badge {
        position: relative; width: 48px; height: 48px; border-radius: 14px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid; place-items: center; color: #fff; cursor: pointer;
        box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.7);
        transition: all 0.2s; flex-shrink: 0; border: none; font-family: inherit;
    }
    .order-cart-badge:active { transform: scale(0.95); }
    .order-cart-badge span {
        position: absolute; top: -4px; right: -4px;
        min-width: 20px; height: 20px; padding: 0 6px;
        background: #ef4444; color: #fff; border-radius: 10px;
        font-size: 10px; font-weight: 800; display: grid; place-items: center;
        border: 2px solid #0f0f14;
    }

    /* ===== SEARCH ===== */
    .order-search {
        position: relative; display: flex; align-items: center;
        margin-bottom: 14px; padding: 13px 16px;
        background: rgba(20, 20, 26, 0.55);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px; transition: all 0.2s;
    }
    .order-search:focus-within {
        border-color: #c9a961;
        background: rgba(20, 20, 26, 0.85);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.12);
    }
    .order-search svg { color: var(--text-muted); flex-shrink: 0; margin-right: 10px; }
    .order-search input {
        flex: 1; min-width: 0; background: transparent;
        border: none; outline: none; color: var(--text-primary);
        font-size: 14px; font-family: inherit;
    }
    .order-search input::placeholder { color: var(--text-muted); }
    .order-search-clear {
        width: 24px; height: 24px; border-radius: 6px;
        background: rgba(239, 68, 68, 0.15); color: #ef4444;
        display: grid; place-items: center; cursor: pointer;
        flex-shrink: 0; border: none;
    }

    /* ===== TABS ===== */
    .order-tabs {
        display: flex; gap: 8px; margin-bottom: 18px; padding: 2px 0;
        overflow-x: auto; scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        margin-left: -16px; margin-right: -16px;
        padding-left: 16px; padding-right: 16px;
    }
    .order-tabs::-webkit-scrollbar { display: none; }
    .order-tab {
        min-height: 36px; padding: 0 16px; border-radius: 10px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: var(--text-secondary); font-size: 13px; font-weight: 600;
        font-family: inherit; white-space: nowrap; cursor: pointer;
        transition: all 0.2s; flex-shrink: 0;
    }
    .order-tab.active {
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff; border-color: transparent;
        box-shadow: 0 6px 14px -6px rgba(169, 120, 74, 0.6);
    }

    /* ===== PRODUCTS GRID ===== */
    .products-scroll {
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: 14px; padding-bottom: 100px;
    }
    .product-card {
        background: rgba(34, 34, 44, 0.4);
        backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px; overflow: hidden; cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-direction: column;
    }
    .product-card:active { transform: scale(0.97); border-color: rgba(201, 169, 97, 0.4); }
    .product-image-wrap {
        position: relative; aspect-ratio: 1 / 1;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.6));
        overflow: hidden;
    }
    .product-image { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
    .product-image-fallback {
        width: 100%; height: 100%; display: grid; place-items: center;
        color: rgba(201, 169, 97, 0.4);
    }
    .product-badge {
        position: absolute; top: 10px; left: 10px;
        padding: 5px 10px; border-radius: 8px;
        font-size: 10px; font-weight: 800; letter-spacing: 0.02em;
        backdrop-filter: blur(12px);
    }
    .product-badge.ok { background: rgba(34, 197, 94, 0.92); color: #fff; }
    .product-badge.low { background: rgba(245, 158, 11, 0.92); color: #fff; }
    .product-badge.out { background: rgba(239, 68, 68, 0.92); color: #fff; }

    .product-info { padding: 12px; display: flex; flex-direction: column; flex: 1; }
    .product-name {
        font-size: 13.5px; font-weight: 700; color: var(--text-primary);
        line-height: 1.3; margin-bottom: 3px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; min-height: 36px;
    }
    .product-sku {
        font-size: 10px; color: var(--text-muted);
        font-family: ui-monospace, monospace; font-weight: 600; margin-bottom: 10px;
    }
    .product-foot { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: auto; }
    .product-price {
        font-size: 15px; font-weight: 800; color: #c9a961;
        letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
    }
    .product-price .price-currency { font-size: 11px; margin-right: 1px; }
    .product-add-btn {
        cursor: pointer; border: none; font-family: inherit; transition: all 0.15s;
        width: 32px; height: 32px; border-radius: 10px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff; display: grid; place-items: center; flex-shrink: 0;
        box-shadow: 0 4px 12px -4px rgba(169, 120, 74, 0.7);
    }
    .product-add-btn:active { transform: scale(0.9); }

    /* ===== EMPTY ===== */
    .empty-products, .empty-search { grid-column: 1 / -1; text-align: center; padding: 60px 20px; }
    .empty-products-icon, .empty-search-icon {
        width: 72px; height: 72px; margin: 0 auto 16px;
        border-radius: 20px; background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        display: grid; place-items: center; color: #c9a961;
    }
    .empty-products-title { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
    .empty-products-text { font-size: 13px; color: var(--text-muted); }

    /* ===== CART FAB ===== */
    .cart-fab {
        position: fixed; bottom: 20px; right: 20px; z-index: 9998;
        display: none; align-items: center; gap: 10px;
        padding: 12px 18px 12px 14px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none; border-radius: 50px; color: #fff;
        font-family: inherit; font-size: 13px; font-weight: 800;
        cursor: pointer;
        box-shadow: 0 12px 30px -10px rgba(169, 120, 74, 1);
        transition: all 0.35s cubic-bezier(0.34, 1.2, 0.64, 1);
        animation: fabPop 0.35s cubic-bezier(0.34, 1.4, 0.64, 1);
    }
    .cart-fab.visible { display: inline-flex; }
    .cart-fab:active { transform: scale(0.95); }
    @keyframes fabPop {
        0% { transform: translateY(80px) scale(0.6); opacity: 0; }
        100% { transform: translateY(0) scale(1); opacity: 1; }
    }
    .cart-fab-icon { position: relative; width: 26px; height: 26px; display: grid; place-items: center; flex-shrink: 0; }
    .cart-fab-count {
        position: absolute; top: -8px; right: -8px;
        min-width: 18px; height: 18px; padding: 0 5px;
        background: #ef4444; color: #fff; border-radius: 9px;
        font-size: 10px; font-weight: 800; display: grid; place-items: center;
        border: 2px solid #0f0f14;
    }
    .cart-fab-info { display: flex; flex-direction: column; align-items: flex-start; line-height: 1; }
    .cart-fab-label { font-size: 9.5px; font-weight: 700; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 3px; }
    .cart-fab-total { font-size: 15px; font-weight: 800; font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }

    /* ===== CART DRAWER ===== */
    .cart-scrim {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        z-index: 99998; background: rgba(0, 0, 0, 0.75);
        opacity: 0; visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }
    .cart-scrim.open { opacity: 1; visibility: visible; }

    .cart-drawer {
        position: fixed; top: 0; right: 0; bottom: 0;
        width: 100%; max-width: 440px;
        z-index: 99999; background: #0f0f14;
        display: flex; flex-direction: column; overflow: hidden;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.34, 1.1, 0.64, 1);
        box-shadow: -20px 0 60px rgba(0, 0, 0, 0.9);
    }
    .cart-drawer.open { transform: translateX(0); }

    /* HEADER — back + title + submit button */
    .cart-drawer-head {
        flex: 0 0 auto;
        display: flex; align-items: center; gap: 10px;
        padding: 12px 14px;
        background: #0f0f14;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .cart-drawer-back {
        width: 38px; height: 38px; border-radius: 11px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.28);
        color: #c9a961;
        display: grid; place-items: center;
        cursor: pointer; padding: 0; flex-shrink: 0;
    }
    .cart-drawer-head-text { flex: 1; min-width: 0; }
    .cart-drawer-title {
        font-size: 14.5px; font-weight: 800;
        color: #f5f3f0; line-height: 1.2;
    }
    .cart-drawer-sub { font-size: 10.5px; color: #6b6862; margin-top: 1px; }

    /* SUBMIT BUTTON SA HEADER */
    .cart-submit-header {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px;
        min-height: 38px;
        padding: 0 14px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none; border-radius: 11px;
        color: #fff;
        font-size: 12px; font-weight: 800;
        font-family: inherit; cursor: pointer;
        box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.9);
        flex-shrink: 0;
        white-space: nowrap;
        -webkit-tap-highlight-color: transparent;
    }
    .cart-submit-header:active { transform: scale(0.96); }
    .cart-submit-header:disabled {
        opacity: 0.35; cursor: not-allowed; box-shadow: none;
    }
    .cart-submit-header svg { width: 14px; height: 14px; }

    /* BODY — items + notes + TOTAL sa ubos */
    .cart-drawer-body {
        flex: 1 1 0%;
        min-height: 0;
        overflow-y: auto; overflow-x: hidden;
        padding: 14px 16px 24px;
        -webkit-overflow-scrolling: touch;
    }
    .cart-items { display: flex; flex-direction: column; gap: 8px; }
    .cart-empty { text-align: center; padding: 40px 20px; }
    .cart-empty-icon { font-size: 40px; opacity: 0.4; margin-bottom: 12px; }
    .cart-empty-text { font-size: 15px; font-weight: 700; color: #f5f3f0; margin-bottom: 6px; }
    .cart-empty-sub { font-size: 12.5px; color: #6b6862; }

    .cart-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px;
        background: rgba(34, 34, 44, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
    }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name {
        font-size: 12.5px; font-weight: 700; color: #f5f3f0;
        margin-bottom: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .cart-item-meta { font-size: 10.5px; color: #6b6862; }
    .cart-item-price {
        font-size: 12.5px; font-weight: 800; color: #c9a961;
        font-variant-numeric: tabular-nums; flex-shrink: 0;
    }
    .cart-item-remove {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        display: grid; place-items: center;
        cursor: pointer; flex-shrink: 0; padding: 0;
    }

    .cart-notes {
        margin-top: 14px; padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }
    .cart-notes-label {
        font-size: 10.5px; font-weight: 700; color: #6b6862;
        text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;
    }
    .cart-notes-input {
        width: 100%; padding: 10px 12px;
        background: rgba(20, 20, 26, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        color: #f5f3f0; font-size: 12.5px; font-family: inherit;
        outline: none; resize: none;
    }
    .cart-notes-input:focus { border-color: #c9a961; }

    /* TOTALS SA UBOS SA BODY */
    .cart-totals {
        margin-top: 16px;
        padding: 14px 14px 4px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
    }
    .cart-total-row {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 12.5px; color: #a8a5a0;
        padding: 4px 0;
    }
    .cart-total-row strong {
        color: #f5f3f0; font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .cart-total-row.final {
        padding-top: 10px; margin-top: 6px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 14px;
    }
    .cart-total-row.final strong {
        font-size: 22px; color: #c9a961; font-weight: 800;
        letter-spacing: -0.02em;
    }
/* ===== PRODUCT MODAL ===== */
    .pm-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(8, 8, 12, 0.92);
        z-index: 999999; display: none;
    }
    .pm-overlay.show { display: block; }
    .pm-sheet {
        position: absolute; top: 0; right: 0; bottom: 0;
        width: 100%; max-width: 500px;
        background: #0f0f14;
        overflow-y: auto; overflow-x: hidden;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.34, 1.1, 0.64, 1);
        box-shadow: -20px 0 60px rgba(0, 0, 0, 0.9);
    }
    .pm-overlay.show .pm-sheet { transform: translateX(0); }

    .pm-header {
        position: sticky; top: 0; z-index: 10;
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px;
        background: #0f0f14;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .pm-back {
        width: 40px; height: 40px; border-radius: 12px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.28);
        color: #c9a961; display: grid; place-items: center;
        cursor: pointer; padding: 0; flex-shrink: 0;
    }
    .pm-header-text { flex: 1; min-width: 0; }
    .pm-header-title { font-size: 15px; font-weight: 800; color: #f5f3f0; }
    .pm-header-sub { font-size: 11px; color: #6b6862; margin-top: 1px; }

    .pm-body { padding: 0 0 24px; }
    .pm-hero {
        position: relative; width: 100%; height: 220px;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.7));
        overflow: hidden; display: flex; align-items: center; justify-content: center;
    }
    .pm-hero img { width: 100%; height: 100%; object-fit: cover; }
    .pm-hero-fallback { color: rgba(201, 169, 97, 0.4); display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .pm-hero-fallback-text { font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; }

    .pm-info { padding: 16px; }
    .pm-title { font-size: 20px; font-weight: 800; color: #f5f3f0; margin-bottom: 8px; }
    .pm-meta-row { margin-bottom: 14px; }
    .pm-sku-tag {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 8px;
        background: rgba(169, 120, 74, 0.12);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 6px;
        font-family: ui-monospace, monospace; font-size: 10px;
        color: #c9a961; font-weight: 700;
    }
    .pm-desc { font-size: 12px; color: #a8a5a0; line-height: 1.5; margin-bottom: 14px; }

    .pm-price-card {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 14px;
        background: linear-gradient(135deg, rgba(201, 169, 97, 0.15), rgba(138, 95, 54, 0.08));
        border: 1px solid rgba(201, 169, 97, 0.25);
        border-radius: 12px; margin-bottom: 10px;
    }
    .pm-price-label { font-size: 9.5px; font-weight: 800; color: #6b6862; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px; }
    .pm-price-value { font-size: 22px; font-weight: 800; color: #c9a961; letter-spacing: -0.03em; line-height: 1; }

    .pm-qty-card {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px;
        background: rgba(34, 34, 44, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px; margin-bottom: 10px;
    }
    .pm-qty-text { font-size: 12.5px; font-weight: 700; color: #f5f3f0; }
    .pm-qty-sub { font-size: 10.5px; color: #6b6862; margin-top: 1px; }
    .pm-qty-controls { display: flex; align-items: center; gap: 8px; }
    .pm-qty-btn {
        width: 38px; height: 38px; border-radius: 10px;
        background: rgba(169, 120, 74, 0.15);
        border: 1px solid rgba(169, 120, 74, 0.3);
        color: #c9a961; display: flex; align-items: center; justify-content: center;
        cursor: pointer; padding: 0;
    }
    .pm-qty-value { min-width: 38px; text-align: center; font-size: 18px; font-weight: 800; color: #f5f3f0; }

    .pm-subtotal-card {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 14px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px; margin-bottom: 12px;
    }
    .pm-subtotal-label { font-size: 11px; color: #6b6862; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
    .pm-subtotal-value { font-size: 18px; font-weight: 800; color: #c9a961; font-variant-numeric: tabular-nums; }

    .pm-add-btn {
        width: 100%; min-height: 48px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        border: none; border-radius: 13px; color: #fff;
        font-size: 14px; font-weight: 800; font-family: inherit;
        cursor: pointer;
        box-shadow: 0 10px 24px -8px rgba(169, 120, 74, 0.9);
        padding: 0 18px;
    }

    /* ===== SCROLL LOCK ===== */
    body.no-scroll { overflow: hidden; }

    /* ===== RESPONSIVE ===== */
    @media (min-width: 720px) {
        .products-scroll { grid-template-columns: repeat(3, 1fr); }
    }

    
        /* ========== ACTION BUTTONS ROW ========== */
        .product-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        /* 3-dash View button */
        .product-view-dash-btn {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: rgba(201, 169, 97, 0.12);
            border: 1px solid rgba(201, 169, 97, 0.3);
            color: #c9a961;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.15s ease;
            -webkit-tap-highlight-color: transparent;
            flex-shrink: 0;
            padding: 0;
            font-family: inherit;
        }
        .product-view-dash-btn:hover {
            background: rgba(201, 169, 97, 0.25);
            border-color: #c9a961;
            color: #fff;
        }
        .product-view-dash-btn:active {
            transform: scale(0.94);
        }
    
        /* ===== BACK BUTTON ===== */
        .order-head-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }
        .order-back-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(201, 169, 97, 0.12);
            border: 1px solid rgba(201, 169, 97, 0.3);
            color: #c9a961;
            display: grid;
            place-items: center;
            text-decoration: none;
            flex-shrink: 0;
            transition: all 0.15s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .order-back-btn:hover {
            background: rgba(201, 169, 97, 0.25);
            border-color: #c9a961;
            color: #fff;
            transform: translateX(-2px);
        }
        .order-back-btn:active {
            transform: scale(0.94);
        }
    </style>
@endpush

@push('scripts')
<script>
    // ===== STATE =====
    var cart = {};
    var pmData = null;
    var pmQtyNum = 1;

    // ===== CART FUNCTIONS =====
    function openCart() {
        document.getElementById('cartDrawer').classList.add('open');
        document.getElementById('cartScrim').classList.add('open');
        document.body.classList.add('no-scroll');
    }

    function closeCart() {
        document.getElementById('cartDrawer').classList.remove('open');
        document.getElementById('cartScrim').classList.remove('open');
        document.body.classList.remove('no-scroll');
    }

    function quickAdd(id, name, price, stock) {
        if (stock <= 0) { showToast('Out of stock'); return; }
        price = parseFloat(price);

        if (cart[id]) {
            if (cart[id].qty >= stock) { showToast('Max stock reached'); return; }
            cart[id].qty += 1;
            cart[id].subtotal = cart[id].qty * cart[id].price;
        } else {
            cart[id] = { product_id: id, name: name, qty: 1, price: price, subtotal: price };
        }
        renderCart();
        showToast(name + ' added');
    }

    function removeFromCart(productId) {
        delete cart[productId];
        renderCart();
    }

    function renderCart() {
        var items = Object.values(cart);
        var container = document.getElementById('cartItems');
        var fab = document.getElementById('cartFab');
        var fabCount = document.getElementById('cartFabCount');
        var fabTotal = document.getElementById('cartFabTotal');
        var drawerCount = document.getElementById('cartDrawerCount');
        var cartTotalEl = document.getElementById('cartTotal');
        var cartTotalMiniEl = document.getElementById('cartTotalMini');
        var hiddenEl = document.getElementById('hiddenItems');
        var submitBtn = document.getElementById('submitBtn');
        var headerBadge = document.getElementById('headerCartCount');

        var totalQty = 0, totalAmount = 0;

        if (items.length === 0) {
            container.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛒</div><div class="cart-empty-text">Walay items pa</div><div class="cart-empty-sub">Tap products to add</div></div>';
            fab.classList.remove('visible');
            drawerCount.textContent = '0 items';
            cartTotalEl.textContent = '\u20B10.00';
            cartTotalMiniEl.textContent = '\u20B10.00';
            hiddenEl.innerHTML = '';
            submitBtn.disabled = true;
            if (headerBadge) headerBadge.style.display = 'none';
            return;
        }

        var html = '', hiddenHtml = '';
        items.forEach(function(item, index) {
            html += '<div class="cart-item">'
                + '<div class="cart-item-info">'
                + '<div class="cart-item-name">' + escapeHtml(item.name) + '</div>'
                + '<div class="cart-item-meta">' + item.qty + ' × \u20B1' + formatMoney(item.price) + '</div>'
                + '</div>'
                + '<div class="cart-item-price">\u20B1' + formatMoney(item.subtotal) + '</div>'
                + '<button type="button" class="cart-item-remove" onclick="removeFromCart(' + item.product_id + ')">'
                + '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>'
                + '</button></div>';

            hiddenHtml += '<input type="hidden" name="items[' + index + '][product_id]" value="' + item.product_id + '">';
            hiddenHtml += '<input type="hidden" name="items[' + index + '][quantity]" value="' + item.qty + '">';
            totalQty += item.qty;
            totalAmount += item.subtotal;
        });

        container.innerHTML = html;
        hiddenEl.innerHTML = hiddenHtml;
        fab.classList.add('visible');
        fabCount.textContent = totalQty > 99 ? '99+' : totalQty;
        fabTotal.textContent = '\u20B1' + formatMoney(totalAmount);
        drawerCount.textContent = totalQty + ' item' + (totalQty > 1 ? 's' : '');
        cartTotalEl.textContent = '\u20B1' + formatMoney(totalAmount);
        cartTotalMiniEl.textContent = '\u20B1' + formatMoney(totalAmount);
        submitBtn.disabled = false;
        if (headerBadge) { headerBadge.textContent = totalQty; headerBadge.style.display = 'grid'; }
    }

    function submitOrder() {
        if (Object.keys(cart).length === 0) { showToast('Walay items'); return; }
        var btn = document.getElementById('submitBtn');
        var btnText = document.getElementById('submitBtnText');
        if (btn) btn.disabled = true;
        if (btnText) btnText.textContent = '...';

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

    // ===== PRODUCT MODAL =====
    function openProductModal(id, name, price, stock, image, sku) {
        if (stock <= 0) { showToast('Out of stock'); return; }

        pmData = { id: id, name: name, price: parseFloat(price), stock: parseInt(stock) };
        pmQtyNum = 1;

        document.getElementById('pmName').textContent = name;
        document.getElementById('pmSkuText').textContent = sku || 'N/A';
        document.getElementById('pmPrice').textContent = '\u20B1' + formatMoney(price);
        document.getElementById('pmQtyVal').textContent = '1';
        document.getElementById('pmQtySub').textContent = 'Max: ' + stock + ' available';

        var hero = document.getElementById('pmHero');
        if (image) {
            hero.innerHTML = '<img src="' + image + '" alt="' + name + '">';
        } else {
            hero.innerHTML = '<div class="pm-hero-fallback"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><div class="pm-hero-fallback-text">No image</div></div>';
        }

        pmUpdateSubtotal();
        document.getElementById('pmOverlay').classList.add('show');
        document.body.classList.add('no-scroll');
    }

    function pmClose() {
        document.getElementById('pmOverlay').classList.remove('show');
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
        document.getElementById('pmSubtotal').textContent = '\u20B1' + formatMoney(pmQtyNum * pmData.price);
    }

    function pmAdd() {
        if (!pmData) return;
        var id = pmData.id, qty = pmQtyNum;

        if (cart[id]) {
            cart[id].qty += qty;
            cart[id].subtotal = cart[id].qty * cart[id].price;
        } else {
            cart[id] = { product_id: id, name: pmData.name, qty: qty, price: pmData.price, subtotal: qty * pmData.price };
        }
        var name = pmData.name;
        pmClose();
        renderCart();
        showToast(name + ' added');
    }

    // ===== FILTERS =====
    function filterProducts(query) {
        var q = query.toLowerCase().trim();
        var cards = document.querySelectorAll('.product-card');
        var visibleCount = 0;

        cards.forEach(function(card) {
            var visible = q === '' || (card.dataset.search || '').indexOf(q) !== -1;
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
        document.querySelectorAll('.product-card').forEach(function(card) {
            card.style.display = (catId === 'all' || card.dataset.category === catId) ? '' : 'none';
        });
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
        t.style.cssText = 'position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);padding:12px 20px;background:rgba(34,197,94,0.95);color:#fff;font-size:13px;font-weight:700;border-radius:12px;z-index:999999;opacity:0;transition:all 0.3s;pointer-events:none;box-shadow:0 12px 30px -8px rgba(34,197,94,0.6);';
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(function() { t.style.opacity = '1'; t.style.transform = 'translateX(-50%) translateY(0)'; }, 10);
        setTimeout(function() { t.style.opacity = '0'; t.style.transform = 'translateX(-50%) translateY(20px)'; setTimeout(function() { t.remove(); }, 300); }, 2000);
    }

    // ===== MODAL CLOSE ON OVERLAY CLICK + ESC =====
    document.addEventListener('DOMContentLoaded', function() {
        var overlay = document.getElementById('pmOverlay');
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) pmClose();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var pm = document.getElementById('pmOverlay');
                if (pm && pm.classList.contains('show')) pmClose();
                var cd = document.getElementById('cartDrawer');
                if (cd && cd.classList.contains('open')) closeCart();
            }
        });
    });
</script>
@endpush