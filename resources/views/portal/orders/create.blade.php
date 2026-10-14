@extends('portal.layouts.app')

@section('title', 'New Order')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $products = \App\Models\Product::where('is_active', true)->orderByDesc('is_featured')->orderBy('name')->get();
    $categories = \App\Models\Category::orderBy('name')->get();
@endphp

{{-- HEADER --}}
<div class="order-head">
    <div class="order-head-left">
        <a href="{{ route('portal.orders.index') }}" class="order-back-btn" aria-label="Back">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="order-head-label">Order Stock</div>
            <div class="order-head-title">Browse Products</div>
        </div>
    </div>
    <button type="button" class="cart-fab" id="cartFab" aria-label="Cart">
        <div class="cart-fab-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <span class="cart-fab-count" id="cartFabCount">0</span>
        </div>
    </button>
</div>

{{-- SEARCH --}}
<div class="order-search">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
    <input type="text" id="orderSearch" placeholder="Search products...">
    <button type="button" class="order-search-clear" id="searchClear" style="display:none;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
</div>

{{-- TABS --}}
<div class="order-tabs">
    <button type="button" class="order-tab active" data-category="all">All</button>
    @foreach($categories as $cat)
        <button type="button" class="order-tab" data-category="{{ $cat->id }}">{{ $cat->name }}</button>
    @endforeach
</div>

{{-- PRODUCTS --}}
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
             data-image="{{ $product->image_url ?? '' }}"
             data-sku="{{ $product->sku ?? '' }}">
            <div class="product-image-wrap">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" class="product-image">
                @else
                    <div class="product-image-fallback">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                @endif
                @if($stockStatus === 'out')<span class="product-badge out">Out</span>
                @elseif($stockStatus === 'low')<span class="product-badge low">Low: {{ $stockLeft }}</span>
                @else<span class="product-badge ok">Stock: {{ $stockLeft }}</span>
                @endif
            </div>
            <div class="product-info">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-sku">{{ $product->sku }}</div>
                <div class="product-foot">
                    <div class="product-price"><span class="price-currency">₱</span>{{ number_format($product->price, 2) }}</div>
                    <button type="button" class="product-add-btn" data-quick-add aria-label="Add"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></button>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-products">
            <div class="empty-products-icon"><svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
            <div class="empty-products-title">No products available</div>
        </div>
    @endforelse
</div>

<div class="empty-search" id="emptySearch" style="display:none;">
    <div class="empty-search-icon"><svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
    <div class="empty-products-title">Walay nakit-an</div>
</div>

{{-- CART --}}
<div class="cart-scrim" id="cartScrim" hidden></div>
<div class="cart-drawer" id="cartDrawer" hidden>
    <div class="cart-drawer-head">
        <button type="button" class="cart-drawer-back" id="cartDrawerClose"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></button>
        <div class="cart-drawer-head-text">
            <div class="cart-drawer-title">Your Order</div>
            <div class="cart-drawer-sub" id="cartDrawerCount">0 items</div>
        </div>
    </div>
    <div class="cart-drawer-body">
        <div class="cart-items" id="cartItems"></div>
        <div class="cart-notes">
            <div class="cart-notes-label">Order Notes (optional)</div>
            <textarea id="orderNotes" class="cart-notes-input" rows="2" placeholder="Special instructions..."></textarea>
        </div>
        <div class="cart-totals">
            <div class="cart-total-row"><span>Subtotal</span><strong id="cartTotalMini">₱0.00</strong></div>
            <div class="cart-total-row final"><span>Total</span><strong id="cartTotal">₱0.00</strong></div>
        </div>
    </div>

    {{-- FIXED FOOTER: SUBMIT BUTTON --}}
    <div class="cart-drawer-foot">
        <button type="button" class="cart-submit-btn" id="submitBtn" disabled>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            <span id="submitBtnText">Submit Order</span>
        </button>
    </div>
</div>

<form id="orderForm" method="POST" action="{{ route('portal.orders.store') }}" style="display:none;">
    @csrf
    <div id="hiddenItems"></div>
</form>

{{-- PRODUCT MODAL --}}
<div class="pm-overlay" id="pmOverlay" hidden>
    <div class="pm-sheet" id="pmSheet">
        <div class="pm-header">
            <button type="button" class="pm-back" id="pmBack"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></button>
            <div class="pm-header-text">
                <div class="pm-header-title">Product Details</div>
                <div class="pm-header-sub">View and order this item</div>
            </div>
        </div>
        <div class="pm-body">
            <div class="pm-hero" id="pmHero"></div>
            <div class="pm-info">
                <div class="pm-title" id="pmName">Product Name</div>
                <div class="pm-meta-row"><span class="pm-sku-tag"><span id="pmSkuText">SKU</span></span></div>
                <div class="pm-desc">Browse this product and add it to your order.</div>
                <div class="pm-price-card">
                    <div><div class="pm-price-label">Unit Price</div><div class="pm-price-value" id="pmPrice">₱0.00</div></div>
                </div>
                <div class="pm-qty-card">
                    <div><div class="pm-qty-text">Quantity</div><div class="pm-qty-sub" id="pmQtySub">Max: 0 available</div></div>
                    <div class="pm-qty-controls">
                        <button type="button" class="pm-qty-btn" id="pmQtyMinus"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14"/></svg></button>
                        <div class="pm-qty-value" id="pmQtyVal">1</div>
                        <button type="button" class="pm-qty-btn" id="pmQtyPlus"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></button>
                    </div>
                </div>
                <div class="pm-subtotal-card">
                    <div class="pm-subtotal-label">Subtotal</div>
                    <div class="pm-subtotal-value" id="pmSubtotal">₱0.00</div>
                </div>
                <button type="button" class="pm-add-btn" id="pmAddBtn"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Add to Order</button>
            </div>
        </div>
    </div>
</div>

@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   ORDERS CREATE — PREMIUM COFFEE THEME
   ═══════════════════════════════════════════════════════════ */

/* ⚙️ PORTAL HEADER HEIGHT — i-adjust kung iba ang taas ng header ninyo */
:root {
    --portal-header-h: 0px;
}

/* ═══ FULL-SCREEN MODE — hide topbar header ═══ */
.p-topbar { display: none !important; }
.p-float-hamburger { display: none !important; }
body > .p-float-hamburger { display: none !important; }
.p-main { padding-top: 18px !important; }

/* HEADER */
.order-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; }
.order-head-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.order-back-btn { width: 42px; height: 42px; border-radius: 13px; background: #EFEBE9; border: 1px solid #D7CCC8; color: #2D1A11; display: grid; place-items: center; text-decoration: none; flex-shrink: 0; transition: all 0.2s; }
.order-back-btn:hover { background: #D7CCC8; transform: translateX(-2px); }
.order-head-label { font-size: 11px; font-weight: 800; color: #D84315; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 4px; }
.order-head-title { font-size: 24px; font-weight: 800; color: #2D1A11; letter-spacing: -0.03em; line-height: 1.15; }

/* SEARCH */
.order-search { position: relative; display: flex; align-items: center; margin-bottom: 14px; padding: 14px 16px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 14px; transition: all 0.2s; }
.order-search:focus-within { border-color: #2D1A11; box-shadow: 0 0 0 4px rgba(45, 26, 17, 0.08); }
.order-search svg { color: #64748b; flex-shrink: 0; margin-right: 10px; }
.order-search input { flex: 1; min-width: 0; background: transparent; border: none; outline: none; color: #2D1A11; font-size: 14px; font-family: inherit; }
.order-search input::placeholder { color: #64748b; }
.order-search-clear { width: 26px; height: 26px; border-radius: 8px; background: #FFEBEE; color: #D84315; display: grid; place-items: center; cursor: pointer; border: none; }

/* TABS */
.order-tabs { display: flex; gap: 8px; margin-bottom: 18px; padding: 2px 0; overflow-x: auto; scrollbar-width: none; -webkit-overflow-scrolling: touch; margin-left: -20px; margin-right: -20px; padding-left: 20px; padding-right: 20px; }
.order-tabs::-webkit-scrollbar { display: none; }
.order-tab { min-height: 38px; padding: 0 18px; border-radius: 100px; background: #FFFFFF; border: 1.5px solid #EFEBE9; color: #64748b; font-size: 13px; font-weight: 700; font-family: inherit; white-space: nowrap; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
.order-tab:hover { border-color: #D7CCC8; color: #2D1A11; }
.order-tab.active { background: #E8A33D; color: #FFFFFF; border-color: #E8A33D; box-shadow: 0 6px 16px -6px rgba(232, 163, 61, 0.5); }

/* PRODUCTS */
.products-scroll { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; padding-bottom: 100px; }
.product-card { background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; overflow: hidden; cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.product-card:hover { border-color: #D7CCC8; transform: translateY(-3px); box-shadow: 0 12px 24px -10px rgba(45, 26, 17, 0.15); }
.product-card:active { transform: scale(0.97); }
.product-image-wrap { position: relative; aspect-ratio: 1 / 1; background: #FDFBF7; overflow: hidden; }
.product-image { width: 100%; height: 100%; object-fit: cover; }
.product-image-fallback { width: 100%; height: 100%; display: grid; place-items: center; color: #D7CCC8; }
.product-badge { position: absolute; top: 10px; left: 10px; padding: 5px 10px; border-radius: 8px; font-size: 10px; font-weight: 800; letter-spacing: 0.02em; backdrop-filter: blur(12px); color: #FFF; }
.product-badge.ok { background: rgba(34, 197, 94, 0.92); }
.product-badge.low { background: rgba(245, 158, 11, 0.92); }
.product-badge.out { background: rgba(239, 68, 68, 0.92); }
.product-info { padding: 12px; display: flex; flex-direction: column; flex: 1; }
.product-name { font-size: 13.5px; font-weight: 700; color: #2D1A11; line-height: 1.3; margin-bottom: 3px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 36px; }
.product-sku { font-size: 10px; color: #64748b; font-family: ui-monospace, monospace; font-weight: 600; margin-bottom: 10px; }
.product-foot { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: auto; }
.product-price { font-size: 15px; font-weight: 800; color: #D84315; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
.product-price .price-currency { font-size: 11px; margin-right: 1px; }
.product-add-btn { cursor: pointer; border: none; font-family: inherit; transition: all 0.15s; width: 34px; height: 34px; border-radius: 10px; background: #E8A33D; color: #FFF; display: grid; place-items: center; flex-shrink: 0; box-shadow: 0 4px 12px -4px rgba(232, 163, 61, 0.5); }
.product-add-btn:active { transform: scale(0.9); }

/* EMPTY */
.empty-products, .empty-search { grid-column: 1 / -1; text-align: center; padding: 60px 20px; }
.empty-products-icon, .empty-search-icon { width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 20px; background: #FDFBF7; border: 1.5px solid #EFEBE9; display: grid; place-items: center; color: #D7CCC8; }
.empty-products-title { font-size: 16px; font-weight: 700; color: #2D1A11; margin-bottom: 6px; }

/* CART FAB */
.cart-fab { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 46px; height: 46px; padding: 0; border-radius: 14px; background: #E8A33D; border: none; color: #FFFFFF; cursor: pointer; flex-shrink: 0; box-shadow: 0 6px 18px -6px rgba(232, 163, 61, 0.5); }
.cart-fab:active { transform: scale(0.95); }
.cart-fab-icon { position: relative; width: 22px; height: 22px; display: grid; place-items: center; }
.cart-fab-count { position: absolute; top: -10px; right: -10px; min-width: 20px; height: 20px; padding: 0 5px; background: #DC2626; color: #FFF; font-size: 11px; font-weight: 800; border-radius: 100px; display: inline-flex; align-items: center; justify-content: center; border: 2.5px solid #FFFFFF; }
.cart-pulse { animation: cartPulse 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes cartPulse { 0% { transform: scale(1); } 40% { transform: scale(1.35); } 70% { transform: scale(0.9); } 100% { transform: scale(1); } }
.cart-fab-count.bump { animation: countBump 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes countBump { 50% { transform: scale(1.4); } }

/* ═══ CART DRAWER — OFFSET PARA DI MATAKPAN NG PORTAL HEADER ═══ */
.cart-scrim { position: fixed; top: var(--portal-header-h); left: 0; right: 0; bottom: 0; z-index: 99998; background: rgba(45, 26, 17, 0.6); backdrop-filter: blur(6px); }
.cart-drawer { position: fixed; top: var(--portal-header-h); right: 0; bottom: 0; width: 100%; max-width: 440px; z-index: 99999; background: #FFFFFF; display: flex; flex-direction: column; overflow: hidden; box-shadow: -20px 0 60px rgba(45, 26, 17, 0.3); }
.cart-drawer:not([hidden]) { animation: cartSlideIn 0.4s cubic-bezier(0.34, 1.1, 0.64, 1); }
@keyframes cartSlideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
.cart-drawer-head { flex: 0 0 auto; display: flex; align-items: center; gap: 10px; padding: 14px 16px; background: #FDFBF7; border-bottom: 1.5px solid #EFEBE9; }
.cart-drawer-back { width: 40px; height: 40px; border-radius: 12px; background: #EFEBE9; border: 1px solid #D7CCC8; color: #2D1A11; display: grid; place-items: center; cursor: pointer; padding: 0; flex-shrink: 0; }
.cart-drawer-head-text { flex: 1; min-width: 0; }
.cart-drawer-title { font-size: 15px; font-weight: 800; color: #2D1A11; line-height: 1.2; }
.cart-drawer-sub { font-size: 11px; color: #64748b; margin-top: 1px; }
.cart-drawer-body { flex: 1 1 0%; min-height: 0; overflow-y: auto; padding: 16px; -webkit-overflow-scrolling: touch; }
.cart-items { display: flex; flex-direction: column; gap: 8px; }
.cart-empty { text-align: center; padding: 40px 20px; }
.cart-empty-icon { font-size: 40px; opacity: 0.4; margin-bottom: 12px; }
.cart-empty-text { font-size: 15px; font-weight: 700; color: #2D1A11; margin-bottom: 6px; }
.cart-empty-sub { font-size: 12.5px; color: #64748b; }
.cart-item { display: flex; align-items: center; gap: 10px; padding: 12px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; }
.cart-item-info { flex: 1; min-width: 0; }
.cart-item-name { font-size: 13px; font-weight: 700; color: #2D1A11; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cart-item-meta { font-size: 11px; color: #64748b; }
.cart-item-price { font-size: 13px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; flex-shrink: 0; }
.cart-item-remove { width: 30px; height: 30px; border-radius: 9px; background: #FFEBEE; border: 1px solid #FFCCBC; color: #D84315; display: grid; place-items: center; cursor: pointer; flex-shrink: 0; padding: 0; }
.cart-notes { margin-top: 14px; padding-top: 14px; border-top: 1.5px solid #EFEBE9; }
.cart-notes-label { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
.cart-notes-input { width: 100%; padding: 11px 13px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 11px; color: #2D1A11; font-size: 13px; font-family: inherit; outline: none; resize: none; }
.cart-notes-input:focus { border-color: #2D1A11; }
.cart-totals { margin-top: 16px; padding: 14px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 14px; }
.cart-total-row { display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; color: #64748b; padding: 4px 0; }
.cart-total-row strong { color: #2D1A11; font-weight: 700; font-variant-numeric: tabular-nums; }
.cart-total-row.final { padding-top: 10px; margin-top: 6px; border-top: 2px solid #EFEBE9; font-size: 14px; }
.cart-total-row.final strong { font-size: 22px; color: #D84315; font-weight: 800; letter-spacing: -0.02em; }

/* ═══ FIXED FOOTER (SUBMIT) ═══ */
.cart-drawer-foot { flex: 0 0 auto; padding: 14px 16px; padding-bottom: calc(14px + env(safe-area-inset-bottom, 0px)); background: #FFFFFF; border-top: 1.5px solid #EFEBE9; box-shadow: 0 -8px 24px -12px rgba(45, 26, 17, 0.15); }
.cart-submit-btn { width: 100%; min-height: 54px; display: flex; align-items: center; justify-content: center; gap: 8px; background: #E8A33D; border: none; border-radius: 14px; color: #FFFFFF; font-size: 15px; font-weight: 800; font-family: inherit; cursor: pointer; box-shadow: 0 10px 24px -8px rgba(232, 163, 61, 0.6); transition: all 0.15s; padding: 0 18px; }
.cart-submit-btn:active { transform: scale(0.98); }
.cart-submit-btn:disabled { opacity: 0.4; cursor: not-allowed; box-shadow: none; background: #94a3b8; }

/* ═══ PRODUCT MODAL — OFFSET DIN ═══ */
.pm-overlay { position: fixed; top: var(--portal-header-h); left: 0; right: 0; bottom: 0; background: rgba(45, 26, 17, 0.7); backdrop-filter: blur(8px); z-index: 999999; }
.pm-overlay:not([hidden]) { display: block !important; animation: fadeIn 0.2s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.pm-sheet { position: absolute; top: 0; right: 0; bottom: 0; width: 100%; max-width: 500px; background: #FFFFFF; overflow-y: auto; box-shadow: -20px 0 60px rgba(45, 26, 17, 0.3); }
.pm-overlay:not([hidden]) .pm-sheet { animation: pmSlideIn 0.4s cubic-bezier(0.34, 1.1, 0.64, 1); }
@keyframes pmSlideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
.pm-header { position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: #FDFBF7; border-bottom: 1.5px solid #EFEBE9; }
.pm-back { width: 42px; height: 42px; border-radius: 12px; background: #EFEBE9; border: 1px solid #D7CCC8; color: #2D1A11; display: grid; place-items: center; cursor: pointer; padding: 0; flex-shrink: 0; }
.pm-header-text { flex: 1; min-width: 0; }
.pm-header-title { font-size: 15px; font-weight: 800; color: #2D1A11; }
.pm-header-sub { font-size: 11px; color: #64748b; margin-top: 1px; }
.pm-body { padding: 0 0 24px; }
.pm-hero { position: relative; width: 100%; height: 220px; background: #FDFBF7; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.pm-hero img { width: 100%; height: 100%; object-fit: cover; }
.pm-hero-fallback { color: #D7CCC8; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.pm-hero-fallback-text { font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; }
.pm-info { padding: 18px 16px; }
.pm-title { font-size: 20px; font-weight: 800; color: #2D1A11; margin-bottom: 8px; }
.pm-meta-row { margin-bottom: 14px; }
.pm-sku-tag { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: #EFEBE9; border: 1px solid #D7CCC8; border-radius: 8px; font-family: ui-monospace, monospace; font-size: 10.5px; color: #2D1A11; font-weight: 700; }
.pm-desc { font-size: 12.5px; color: #64748b; line-height: 1.5; margin-bottom: 14px; }
.pm-price-card { display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; margin-bottom: 10px; }
.pm-price-label { font-size: 9.5px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px; }
.pm-price-value { font-size: 22px; font-weight: 800; color: #D84315; letter-spacing: -0.03em; line-height: 1; }
.pm-qty-card { display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; margin-bottom: 10px; }
.pm-qty-text { font-size: 13px; font-weight: 700; color: #2D1A11; }
.pm-qty-sub { font-size: 10.5px; color: #64748b; margin-top: 1px; }
.pm-qty-controls { display: flex; align-items: center; gap: 8px; }
.pm-qty-btn { width: 40px; height: 40px; border-radius: 11px; background: #EFEBE9; border: 1px solid #D7CCC8; color: #2D1A11; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; }
.pm-qty-btn:active { transform: scale(0.94); }
.pm-qty-value { min-width: 40px; text-align: center; font-size: 18px; font-weight: 800; color: #2D1A11; }
.pm-subtotal-card { display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; margin-bottom: 12px; }
.pm-subtotal-label { font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
.pm-subtotal-value { font-size: 18px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; }
.pm-add-btn { width: 100%; min-height: 50px; display: flex; align-items: center; justify-content: center; gap: 8px; background: #E8A33D; border: none; border-radius: 13px; color: #FFFFFF; font-size: 14px; font-weight: 800; font-family: inherit; cursor: pointer; box-shadow: 0 10px 24px -8px rgba(232, 163, 61, 0.6); padding: 0 18px; }
.pm-add-btn:active { transform: scale(0.98); }

/* FLY */
.fly-clone { position: fixed; z-index: 99999; pointer-events: none; border-radius: 12px; object-fit: cover; box-shadow: 0 12px 40px -8px rgba(45, 26, 17, 0.7); border: 2px solid #D84315; transition: all 0.9s cubic-bezier(0.55, -0.15, 0.65, 1.15); }

/* ═══ TOAST — MATCHING APP DESIGN ═══ */
.toast-wrap {
    position: fixed;
    top: 18px;
    right: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px 14px 16px;
    background: #FFFFFF;
    border: 1px solid #EEEEEE;
    border-radius: 14px;
    box-shadow: 0 12px 32px -10px rgba(0, 0, 0, 0.15), 0 4px 12px -4px rgba(0, 0, 0, 0.06);
    z-index: 999999;
    min-width: 280px;
    max-width: 380px;
    overflow: hidden;
    pointer-events: none;
    transform: translateX(120%);
    opacity: 0;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
}

.toast-wrap.show {
    transform: translateX(0);
    opacity: 1;
}

.toast-wrap.hide {
    transform: translateX(-120%);
    opacity: 0;
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s ease;
}

/* ICON — matches stat/section icon style */
.toast-icon {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    border: 1px solid;
}
.toast-success .toast-icon {
    background: #FFF8EC;
    color: #E8A33D;
    border-color: #F0E0C0;
}
.toast-error .toast-icon {
    background: #FFEBEE;
    color: #DC2626;
    border-color: #FFCDD2;
}
.toast-info .toast-icon {
    background: #EFF6FF;
    color: #2563EB;
    border-color: #BFDBFE;
}

/* BODY */
.toast-body {
    flex: 1;
    min-width: 0;
}
.toast-title {
    font-size: 13px;
    font-weight: 800;
    color: #1A1A1A;
    letter-spacing: -0.01em;
    line-height: 1.2;
    margin-bottom: 2px;
}
.toast-msg {
    font-size: 12px;
    color: #757575;
    font-weight: 500;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* STATUS DOT — matches badge dot style */
.toast-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.toast-success .toast-dot { background: #E8A33D; box-shadow: 0 0 8px rgba(232, 163, 61, 0.6); }
.toast-error .toast-dot   { background: #DC2626; box-shadow: 0 0 8px rgba(220, 38, 38, 0.5); }
.toast-info .toast-dot    { background: #2563EB; box-shadow: 0 0 8px rgba(37, 99, 235, 0.5); }

/* TOP STRIPE — matches card stripe */
.toast-wrap::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}
.toast-success::before { background: linear-gradient(90deg, #E8A33D, #F0B85A); }
.toast-error::before   { background: linear-gradient(90deg, #DC2626, #EF4444); }
.toast-info::before    { background: linear-gradient(90deg, #2563EB, #3B82F6); }

@media (max-width: 480px) {
    .toast-wrap {
        top: 16px;
        right: 16px;
        left: 16px;
        min-width: 0;
        max-width: none;
    }
}
[hidden] { display: none !important; }

@media (min-width: 720px) { .products-scroll { grid-template-columns: repeat(3, 1fr); } }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';
    var cart = {};
    var pmData = null;
    var pmQtyNum = 1;

    function formatMoney(n) { return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function escapeHtml(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

    function showToast(msg, type) {
        type = type || 'success';
        var old = document.getElementById('quickToast');
        if (old) old.remove();

        var icons = {
            success: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>',
            error:   '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>',
            info:    '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>'
        };

        var titles = {
            success: 'Added to Order',
            error:   'Oops!',
            info:    'Info'
        };

        var wrap = document.createElement('div');
        wrap.id = 'quickToast';
        wrap.className = 'toast-wrap toast-' + type;
        wrap.innerHTML =
            '<div class="toast-icon">' + (icons[type] || icons.success) + '</div>' +
            '<div class="toast-body">' +
                '<div class="toast-title">' + titles[type] + '</div>' +
                '<div class="toast-msg">' + msg + '</div>' +
            '</div>' +
            '<div class="toast-dot"></div>';

        document.body.appendChild(wrap);

        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                wrap.classList.add('show');
            });
        });

        setTimeout(function() {
            wrap.classList.remove('show');
            wrap.classList.add('hide');
            setTimeout(function() { wrap.remove(); }, 400);
        }, 1400);
    }

    function openCart() { document.getElementById('cartDrawer')?.removeAttribute('hidden'); document.getElementById('cartScrim')?.removeAttribute('hidden'); document.documentElement.classList.add('scroll-locked'); }
    function closeCart() { document.getElementById('cartDrawer')?.setAttribute('hidden', ''); document.getElementById('cartScrim')?.setAttribute('hidden', ''); document.documentElement.classList.remove('scroll-locked'); }

    function addToCart(id, name, price, qty, stock) {
        if (stock <= 0) { showToast('Out of stock'); return false; }
        price = parseFloat(price); qty = parseInt(qty) || 1;
        if (cart[id]) { var newQty = Math.min(cart[id].qty + qty, stock); cart[id].qty = newQty; cart[id].subtotal = newQty * cart[id].price; }
        else { if (qty > stock) qty = stock; cart[id] = { product_id: id, name: name, qty: qty, price: price, subtotal: qty * price }; }
        renderCart(); return true;
    }
    function removeFromCart(id) { delete cart[id]; renderCart(); }

    function renderCart() {
        var items = Object.keys(cart).map(function(k) { return cart[k]; });
        var container = document.getElementById('cartItems');
        var fabCount = document.getElementById('cartFabCount');
        var drawerCount = document.getElementById('cartDrawerCount');
        var cartTotalEl = document.getElementById('cartTotal');
        var cartTotalMiniEl = document.getElementById('cartTotalMini');
        var hiddenEl = document.getElementById('hiddenItems');
        var submitBtn = document.getElementById('submitBtn');
        if (!container) return;
        var totalQty = 0, totalAmount = 0;
        if (items.length === 0) {
            container.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛒</div><div class="cart-empty-text">Walay items pa</div><div class="cart-empty-sub">Tap products to add</div></div>';
            if (drawerCount) drawerCount.textContent = '0 items';
            if (cartTotalEl) cartTotalEl.innerHTML = '₱0.00';
            if (cartTotalMiniEl) cartTotalMiniEl.innerHTML = '₱0.00';
            if (hiddenEl) hiddenEl.innerHTML = '';
            if (submitBtn) submitBtn.disabled = true;
            return;
        }
        var html = '', hiddenHtml = '';
        items.forEach(function(item, index) {
            html += '<div class="cart-item"><div class="cart-item-info"><div class="cart-item-name">' + escapeHtml(item.name) + '</div><div class="cart-item-meta">' + item.qty + ' × ₱' + formatMoney(item.price) + '</div></div><div class="cart-item-price">₱' + formatMoney(item.subtotal) + '</div><button type="button" class="cart-item-remove" data-remove="' + item.product_id + '"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg></button></div>';
            hiddenHtml += '<input type="hidden" name="items[' + index + '][product_id]" value="' + item.product_id + '"><input type="hidden" name="items[' + index + '][quantity]" value="' + item.qty + '">';
            totalQty += item.qty; totalAmount += item.subtotal;
        });
        container.innerHTML = html;
        if (hiddenEl) hiddenEl.innerHTML = hiddenHtml;
        if (fabCount) fabCount.textContent = totalQty > 99 ? '99+' : totalQty;
        if (drawerCount) drawerCount.textContent = totalQty + ' item' + (totalQty > 1 ? 's' : '');
        if (cartTotalEl) cartTotalEl.innerHTML = '₱' + formatMoney(totalAmount);
        if (cartTotalMiniEl) cartTotalMiniEl.innerHTML = '₱' + formatMoney(totalAmount);
        if (submitBtn) submitBtn.disabled = false;
    }

    function submitOrder() {
        if (Object.keys(cart).length === 0) { showToast('Walay items'); return; }
        var btn = document.getElementById('submitBtn'); var btnText = document.getElementById('submitBtnText'); var form = document.getElementById('orderForm');
        if (btn) btn.disabled = true; if (btnText) btnText.textContent = 'Submitting...';
        var notes = document.getElementById('orderNotes');
        if (notes && notes.value.trim()) { var n = document.createElement('input'); n.type = 'hidden'; n.name = 'notes'; n.value = notes.value; form.appendChild(n); }
        form.submit();
    }

    function openProductModal(id, name, price, stock, image, sku) {
        if (stock <= 0) { showToast('Out of stock'); return; }
        pmData = { id: id, name: name, price: parseFloat(price), stock: parseInt(stock) }; pmQtyNum = 1;
        document.getElementById('pmName').textContent = name;
        document.getElementById('pmSkuText').textContent = sku || 'N/A';
        document.getElementById('pmPrice').innerHTML = '₱' + formatMoney(price);
        document.getElementById('pmQtyVal').textContent = '1';
        document.getElementById('pmQtySub').textContent = 'Max: ' + stock + ' available';
        var heroEl = document.getElementById('pmHero');
        if (image) heroEl.innerHTML = '<img src="' + escapeHtml(image) + '" alt="' + escapeHtml(name) + '">';
        else heroEl.innerHTML = '<div class="pm-hero-fallback"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><div class="pm-hero-fallback-text">No image</div></div>';
        pmUpdateSubtotal();
        document.getElementById('pmOverlay').removeAttribute('hidden');
        document.documentElement.classList.add('scroll-locked');
    }
    function pmClose() { document.getElementById('pmOverlay')?.setAttribute('hidden', ''); document.documentElement.classList.remove('scroll-locked'); pmData = null; pmQtyNum = 1; }
    function pmQtyChange(delta) { if (!pmData) return; var next = pmQtyNum + delta; if (next < 1) next = 1; if (next > pmData.stock) next = pmData.stock; pmQtyNum = next; document.getElementById('pmQtyVal').textContent = pmQtyNum; pmUpdateSubtotal(); }
    function pmUpdateSubtotal() { if (!pmData) return; document.getElementById('pmSubtotal').innerHTML = '₱' + formatMoney(pmQtyNum * pmData.price); }
    function pmAdd() { if (!pmData) return; var d = pmData; pmClose(); if (addToCart(d.id, d.name, d.price, pmQtyNum, d.stock)) showToast(d.name + ' added'); }

    function filterProducts(query) {
        var q = (query || '').toLowerCase().trim(); var cards = document.querySelectorAll('.product-card'); var visibleCount = 0;
        cards.forEach(function(card) { var search = (card.getAttribute('data-search') || '').toLowerCase(); var visible = q === '' || search.indexOf(q) !== -1; card.style.display = visible ? '' : 'none'; if (visible) visibleCount++; });
        var emptyEl = document.getElementById('emptySearch'); if (emptyEl) emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
        var clearBtn = document.getElementById('searchClear'); if (clearBtn) clearBtn.style.display = q ? 'grid' : 'none';
    }
    function clearSearch() { document.getElementById('orderSearch').value = ''; filterProducts(''); }
    function filterCategory(catId) {
        document.querySelectorAll('.order-tab').forEach(function(t) { t.classList.toggle('active', t.getAttribute('data-category') === catId); });
        document.querySelectorAll('.product-card').forEach(function(card) { var cat = card.getAttribute('data-category'); card.style.display = (catId === 'all' || cat === catId) ? '' : 'none'; });
        var q = document.getElementById('orderSearch'); if (q && q.value) filterProducts(q.value);
    }

    function flyToCart(btnEl) {
        try {
            if (!btnEl) return; var cartEl = document.getElementById('cartFab'); if (!cartEl) return;
            var img = null; var el = btnEl;
            for (var i = 0; i < 6; i++) { el = el.parentElement; if (!el) break; var found = el.querySelector('img'); if (found && found.src) { img = found; break; } }
            if (!img) return;
            var srcRect = img.getBoundingClientRect(); var cartRect = cartEl.getBoundingClientRect();
            if (!srcRect.width || !cartRect.width) return;
            var clone = document.createElement('img'); clone.src = img.src; clone.className = 'fly-clone';
            clone.style.left = srcRect.left + 'px'; clone.style.top = srcRect.top + 'px'; clone.style.width = srcRect.width + 'px'; clone.style.height = srcRect.height + 'px';
            document.body.appendChild(clone);
            void clone.offsetHeight;
            setTimeout(function() {
                var cx = cartRect.left + cartRect.width / 2 - 25; var cy = cartRect.top + cartRect.height / 2 - 25;
                clone.style.left = cx + 'px'; clone.style.top = cy + 'px'; clone.style.width = '50px'; clone.style.height = '50px'; clone.style.opacity = '0'; clone.style.transform = 'rotate(720deg) scale(0.3)'; clone.style.borderRadius = '50%';
            }, 30);
            setTimeout(function() { if (clone.parentNode) clone.parentNode.removeChild(clone); cartEl.classList.add('cart-pulse'); var badge = document.getElementById('cartFabCount'); if (badge) badge.classList.add('bump'); setTimeout(function() { cartEl.classList.remove('cart-pulse'); if (badge) badge.classList.remove('bump'); }, 500); }, 1000);
        } catch(e) {}
    }

    function init() {
        closeCart(); pmClose();
        document.querySelectorAll('.product-card').forEach(function(card) {
            card.addEventListener('click', function(e) {
                var quickBtn = e.target.closest('[data-quick-add]');
                if (quickBtn) { e.stopPropagation(); e.preventDefault(); var id = parseInt(card.getAttribute('data-id'), 10); var name = card.getAttribute('data-name'); var price = parseFloat(card.getAttribute('data-price')); var stock = parseInt(card.getAttribute('data-stock'), 10); if (addToCart(id, name, price, 1, stock)) { showToast(name + ' added'); flyToCart(quickBtn); } return; }
                openProductModal(parseInt(card.getAttribute('data-id'), 10), card.getAttribute('data-name'), parseFloat(card.getAttribute('data-price')), parseInt(card.getAttribute('data-stock'), 10), card.getAttribute('data-image'), card.getAttribute('data-sku'));
            });
        });
        document.getElementById('orderSearch')?.addEventListener('input', function() { filterProducts(this.value); });
        document.getElementById('searchClear')?.addEventListener('click', clearSearch);
        document.querySelectorAll('.order-tab').forEach(function(tab) { tab.addEventListener('click', function() { filterCategory(this.getAttribute('data-category')); }); });
        document.getElementById('cartFab')?.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); openCart(); });
        document.getElementById('cartDrawerClose')?.addEventListener('click', closeCart);
        document.getElementById('cartScrim')?.addEventListener('click', closeCart);
        document.getElementById('submitBtn')?.addEventListener('click', submitOrder);
        document.getElementById('cartItems')?.addEventListener('click', function(e) { var btn = e.target.closest('[data-remove]'); if (!btn) return; removeFromCart(btn.getAttribute('data-remove')); });
        document.getElementById('pmBack')?.addEventListener('click', pmClose);
        document.getElementById('pmOverlay')?.addEventListener('click', function(e) { if (e.target === this) pmClose(); });
        document.getElementById('pmSheet')?.addEventListener('click', function(e) { e.stopPropagation(); });
        document.getElementById('pmQtyMinus')?.addEventListener('click', function() { pmQtyChange(-1); });
        document.getElementById('pmQtyPlus')?.addEventListener('click', function() { pmQtyChange(1); });
        document.getElementById('pmAddBtn')?.addEventListener('click', pmAdd);
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { if (!document.getElementById('pmOverlay').hasAttribute('hidden')) pmClose(); if (!document.getElementById('cartDrawer').hasAttribute('hidden')) closeCart(); } });
        renderCart();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
@endpush










