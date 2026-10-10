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

    <button type="button" class="cart-fab" id="cartFab" aria-label="Open cart">
        <div class="cart-fab-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="9" cy="21" r="1"/>
                <circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <span class="cart-fab-count" id="cartFabCount">0</span>
        </div>
    </button>
</div>

{{-- ===== SEARCH ===== --}}
<div class="order-search">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/>
        <path d="M21 21l-4.35-4.35"/>
    </svg>
    <input type="text" id="orderSearch" placeholder="Search products...">
    <button type="button" class="order-search-clear" id="searchClear" style="display:none;" aria-label="Clear search">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- ===== CATEGORY TABS ===== --}}
<div class="order-tabs">
    <button type="button" class="order-tab active" data-category="all">All</button>
    @foreach($categories as $cat)
        <button type="button" class="order-tab" data-category="{{ $cat->id }}">{{ $cat->name }}</button>
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
             data-image="{{ $product->image_url ?? '' }}"
             data-sku="{{ $product->sku ?? '' }}">

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
                    <button type="button" class="product-add-btn" data-quick-add aria-label="Quick add">
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

{{-- ===== CART SCRIM (hidden by default) ===== --}}
<div class="cart-scrim" id="cartScrim" hidden></div>

{{-- ===== CART DRAWER (hidden by default) ===== --}}
<div class="cart-drawer" id="cartDrawer" hidden>
    <div class="cart-drawer-head">
        <button type="button" class="cart-drawer-back" id="cartDrawerClose" aria-label="Close cart">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="cart-drawer-head-text">
            <div class="cart-drawer-title">Your Order</div>
            <div class="cart-drawer-sub" id="cartDrawerCount">0 items</div>
        </div>
        <button type="button" class="cart-submit-header" id="submitBtn" disabled>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
            <span id="submitBtnText">Submit</span>
        </button>
    </div>

    <div class="cart-drawer-body">
        <div class="cart-items" id="cartItems"></div>

        <div class="cart-notes">
            <div class="cart-notes-label">Order Notes (optional)</div>
            <textarea id="orderNotes" class="cart-notes-input" rows="2" placeholder="Special instructions..."></textarea>
        </div>

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

{{-- ===== PRODUCT MODAL (hidden by default) ===== --}}
<div class="pm-overlay" id="pmOverlay" hidden>
    <div class="pm-sheet" id="pmSheet">
        <div class="pm-header">
            <button type="button" class="pm-back" id="pmBack" aria-label="Back">
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
                        <button type="button" class="pm-qty-btn" id="pmQtyMinus" aria-label="Decrease">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12h14"/>
                            </svg>
                        </button>
                        <div class="pm-qty-value" id="pmQtyVal">1</div>
                        <button type="button" class="pm-qty-btn" id="pmQtyPlus" aria-label="Increase">
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

                <button type="button" class="pm-add-btn" id="pmAddBtn">
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

@push('scripts')
<script>
(function() {
    'use strict';

    // ═════════════════════════════════════════════════════════════
    // STATE
    // ═════════════════════════════════════════════════════════════
    var cart = {};
    var pmData = null;
    var pmQtyNum = 1;

    // ═════════════════════════════════════════════════════════════
    // HELPERS
    // ═════════════════════════════════════════════════════════════
    function formatMoney(n) {
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
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
        setTimeout(function() {
            t.style.opacity = '0';
            t.style.transform = 'translateX(-50%) translateY(20px)';
            setTimeout(function() { t.remove(); }, 300);
        }, 2000);
    }

    // ═════════════════════════════════════════════════════════════
    // CART DRAWER — using `hidden` attribute (bulletproof)
    // ═════════════════════════════════════════════════════════════
    function openCart() {
        var drawer = document.getElementById('cartDrawer');
        var scrim  = document.getElementById('cartScrim');
        if (drawer) drawer.removeAttribute('hidden');
        if (scrim)  scrim.removeAttribute('hidden');
        document.documentElement.classList.add('scroll-locked');
    }

    function closeCart() {
        var drawer = document.getElementById('cartDrawer');
        var scrim  = document.getElementById('cartScrim');
        if (drawer) drawer.setAttribute('hidden', '');
        if (scrim)  scrim.setAttribute('hidden', '');
        document.documentElement.classList.remove('scroll-locked');
    }

    // ═════════════════════════════════════════════════════════════
    // CART LOGIC
    // ═════════════════════════════════════════════════════════════
    function addToCart(id, name, price, qty, stock) {
        if (stock <= 0) { showToast('Out of stock'); return false; }
        price = parseFloat(price);
        qty = parseInt(qty) || 1;

        if (cart[id]) {
            var newQty = cart[id].qty + qty;
            if (newQty > stock) { newQty = stock; }
            cart[id].qty = newQty;
            cart[id].subtotal = cart[id].qty * cart[id].price;
        } else {
            if (qty > stock) qty = stock;
            cart[id] = { product_id: id, name: name, qty: qty, price: price, subtotal: qty * price };
        }
        renderCart();
        return true;
    }

    function removeFromCart(productId) {
        delete cart[productId];
        renderCart();
    }

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
            if (cartTotalEl) cartTotalEl.innerHTML = '&#8369;0.00';
            if (cartTotalMiniEl) cartTotalMiniEl.innerHTML = '&#8369;0.00';
            if (hiddenEl) hiddenEl.innerHTML = '';
            if (submitBtn) submitBtn.disabled = true;
            return;
        }

        var html = '', hiddenHtml = '';
        items.forEach(function(item, index) {
            html += '<div class="cart-item">' +
                '<div class="cart-item-info">' +
                '<div class="cart-item-name">' + escapeHtml(item.name) + '</div>' +
                '<div class="cart-item-meta">' + item.qty + ' × ₱' + formatMoney(item.price) + '</div>' +
                '</div>' +
                '<div class="cart-item-price">₱' + formatMoney(item.subtotal) + '</div>' +
                '<button type="button" class="cart-item-remove" data-remove="' + item.product_id + '" aria-label="Remove">' +
                '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>' +
                '</button></div>';

            hiddenHtml += '<input type="hidden" name="items[' + index + '][product_id]" value="' + item.product_id + '">';
            hiddenHtml += '<input type="hidden" name="items[' + index + '][quantity]" value="' + item.qty + '">';

            totalQty += item.qty;
            totalAmount += item.subtotal;
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
        var btn = document.getElementById('submitBtn');
        var btnText = document.getElementById('submitBtnText');
        var form = document.getElementById('orderForm');

        if (btn) btn.disabled = true;
        if (btnText) btnText.textContent = '...';

        var notes = document.getElementById('orderNotes');
        if (notes && notes.value.trim()) {
            var notesInput = document.createElement('input');
            notesInput.type = 'hidden';
            notesInput.name = 'notes';
            notesInput.value = notes.value;
            form.appendChild(notesInput);
        }
        form.submit();
    }

    // ═════════════════════════════════════════════════════════════
    // PRODUCT MODAL
    // ═════════════════════════════════════════════════════════════
    function openProductModal(id, name, price, stock, image, sku) {
        if (stock <= 0) { showToast('Out of stock'); return; }

        pmData = { id: id, name: name, price: parseFloat(price), stock: parseInt(stock) };
        pmQtyNum = 1;

        var nameEl = document.getElementById('pmName');
        var skuEl = document.getElementById('pmSkuText');
        var priceEl = document.getElementById('pmPrice');
        var qtyEl = document.getElementById('pmQtyVal');
        var qtySubEl = document.getElementById('pmQtySub');
        var heroEl = document.getElementById('pmHero');
        var overlay = document.getElementById('pmOverlay');

        if (nameEl) nameEl.textContent = name;
        if (skuEl) skuEl.textContent = sku || 'N/A';
        if (priceEl) priceEl.innerHTML = '₱' + formatMoney(price);
        if (qtyEl) qtyEl.textContent = '1';
        if (qtySubEl) qtySubEl.textContent = 'Max: ' + stock + ' available';

        if (heroEl) {
            if (image) {
                heroEl.innerHTML = '<img src="' + escapeHtml(image) + '" alt="' + escapeHtml(name) + '">';
            } else {
                heroEl.innerHTML = '<div class="pm-hero-fallback"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><div class="pm-hero-fallback-text">No image</div></div>';
            }
        }

        pmUpdateSubtotal();
        if (overlay) overlay.removeAttribute('hidden');
        document.documentElement.classList.add('scroll-locked');
    }

    function pmClose() {
        var overlay = document.getElementById('pmOverlay');
        if (overlay) overlay.setAttribute('hidden', '');
        document.documentElement.classList.remove('scroll-locked');
        pmData = null;
        pmQtyNum = 1;
    }

    function pmQtyChange(delta) {
        if (!pmData) return;
        var next = pmQtyNum + delta;
        if (next < 1) next = 1;
        if (next > pmData.stock) next = pmData.stock;
        pmQtyNum = next;
        var el = document.getElementById('pmQtyVal');
        if (el) el.textContent = pmQtyNum;
        pmUpdateSubtotal();
    }

    function pmUpdateSubtotal() {
        if (!pmData) return;
        var el = document.getElementById('pmSubtotal');
        if (el) el.innerHTML = '₱' + formatMoney(pmQtyNum * pmData.price);
    }

    function pmAdd() {
        if (!pmData) return;
        var id = pmData.id;
        var qty = pmQtyNum;
        var stock = pmData.stock;
        var name = pmData.name;
        var price = pmData.price;
        pmClose();
        if (addToCart(id, name, price, qty, stock)) {
            showToast(name + ' added');
        }
    }

    // ═════════════════════════════════════════════════════════════
    // FILTERS
    // ═════════════════════════════════════════════════════════════
    function filterProducts(query) {
        var q = (query || '').toLowerCase().trim();
        var cards = document.querySelectorAll('.product-card');
        var visibleCount = 0;

        cards.forEach(function(card) {
            var search = (card.getAttribute('data-search') || '').toLowerCase();
            var visible = q === '' || search.indexOf(q) !== -1;
            card.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        var emptyEl = document.getElementById('emptySearch');
        if (emptyEl) emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';

        var clearBtn = document.getElementById('searchClear');
        if (clearBtn) clearBtn.style.display = q ? 'grid' : 'none';
    }

    function clearSearch() {
        var input = document.getElementById('orderSearch');
        if (input) input.value = '';
        filterProducts('');
    }

    function filterCategory(catId) {
        document.querySelectorAll('.order-tab').forEach(function(t) {
            t.classList.toggle('active', t.getAttribute('data-category') === catId);
        });
        document.querySelectorAll('.product-card').forEach(function(card) {
            var cat = card.getAttribute('data-category');
            card.style.display = (catId === 'all' || cat === catId) ? '' : 'none';
        });
        var q = document.getElementById('orderSearch');
        if (q && q.value) filterProducts(q.value);
    }

    // ═════════════════════════════════════════════════════════════
    // FLY TO CART
    // ═════════════════════════════════════════════════════════════
    function flyToCart(btnEl) {
        try {
            if (!btnEl) return;
            var cartEl = document.getElementById('cartFab');
            if (!cartEl) return;

            var img = null;
            var el = btnEl;
            for (var i = 0; i < 6; i++) {
                el = el.parentElement;
                if (!el) break;
                var found = el.querySelector('img');
                if (found && found.src) { img = found; break; }
            }
            if (!img) return;

            var srcRect = img.getBoundingClientRect();
            var cartRect = cartEl.getBoundingClientRect();
            if (!srcRect.width || !cartRect.width) return;

            var clone = document.createElement('img');
            clone.src = img.src;
            clone.className = 'fly-clone';
            clone.style.left = srcRect.left + 'px';
            clone.style.top = srcRect.top + 'px';
            clone.style.width = srcRect.width + 'px';
            clone.style.height = srcRect.height + 'px';
            document.body.appendChild(clone);

            void clone.offsetHeight;

            setTimeout(function() {
                var cx = cartRect.left + cartRect.width / 2 - 25;
                var cy = cartRect.top + cartRect.height / 2 - 25;
                clone.style.left = cx + 'px';
                clone.style.top = cy + 'px';
                clone.style.width = '50px';
                clone.style.height = '50px';
                clone.style.opacity = '0';
                clone.style.transform = 'rotate(720deg) scale(0.3)';
                clone.style.borderRadius = '50%';
            }, 30);

            setTimeout(function() {
                if (clone.parentNode) clone.parentNode.removeChild(clone);
                cartEl.classList.add('cart-pulse');
                var badge = document.getElementById('cartFabCount');
                if (badge) badge.classList.add('bump');
                setTimeout(function() {
                    cartEl.classList.remove('cart-pulse');
                    if (badge) badge.classList.remove('bump');
                }, 500);
            }, 1000);
        } catch(e) { /* silent */ }
    }

    // ═════════════════════════════════════════════════════════════
    // INIT — called ONCE on page load
    // ═════════════════════════════════════════════════════════════
    function init() {

        // ═══ BULLETPROOF: force all overlays closed on load ═══
        closeCart();
        pmClose();

        // Product card click + quick-add
        document.querySelectorAll('.product-card').forEach(function(card) {
            card.addEventListener('click', function(e) {
                var quickBtn = e.target.closest('[data-quick-add]');
                if (quickBtn) {
                    e.stopPropagation();
                    e.preventDefault();
                    var id = parseInt(card.getAttribute('data-id'), 10);
                    var name = card.getAttribute('data-name');
                    var price = parseFloat(card.getAttribute('data-price'));
                    var stock = parseInt(card.getAttribute('data-stock'), 10);
                    if (addToCart(id, name, price, 1, stock)) {
                        showToast(name + ' added');
                        flyToCart(quickBtn);
                    }
                    return;
                }
                var id = parseInt(card.getAttribute('data-id'), 10);
                var name = card.getAttribute('data-name');
                var price = parseFloat(card.getAttribute('data-price'));
                var stock = parseInt(card.getAttribute('data-stock'), 10);
                var image = card.getAttribute('data-image');
                var sku = card.getAttribute('data-sku');
                openProductModal(id, name, price, stock, image, sku);
            });
        });

        // Search input
        var searchInput = document.getElementById('orderSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() { filterProducts(this.value); });
        }

        // Search clear
        var clearBtn = document.getElementById('searchClear');
        if (clearBtn) clearBtn.addEventListener('click', clearSearch);

        // Category tabs
        document.querySelectorAll('.order-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                filterCategory(this.getAttribute('data-category'));
            });
        });

        // Cart FAB
        var fab = document.getElementById('cartFab');
        if (fab) {
            fab.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openCart();
            });
        }

        // Cart close
        var cartCloseBtn = document.getElementById('cartDrawerClose');
        if (cartCloseBtn) cartCloseBtn.addEventListener('click', closeCart);

        // Cart scrim
        var scrim = document.getElementById('cartScrim');
        if (scrim) scrim.addEventListener('click', closeCart);

        // Submit
        var submitBtn = document.getElementById('submitBtn');
        if (submitBtn) submitBtn.addEventListener('click', submitOrder);

        // Cart item remove — delegated
        var cartItemsEl = document.getElementById('cartItems');
        if (cartItemsEl) {
            cartItemsEl.addEventListener('click', function(e) {
                var btn = e.target.closest('[data-remove]');
                if (!btn) return;
                removeFromCart(btn.getAttribute('data-remove'));
            });
        }

        // Product modal — back
        var pmBack = document.getElementById('pmBack');
        if (pmBack) pmBack.addEventListener('click', pmClose);

        // Modal overlay click close
        var pmOverlay = document.getElementById('pmOverlay');
        if (pmOverlay) {
            pmOverlay.addEventListener('click', function(e) {
                if (e.target === pmOverlay) pmClose();
            });
        }

        // Modal sheet — stop propagation
        var pmSheet = document.getElementById('pmSheet');
        if (pmSheet) pmSheet.addEventListener('click', function(e) { e.stopPropagation(); });

        // Modal qty controls
        var pmMinus = document.getElementById('pmQtyMinus');
        if (pmMinus) pmMinus.addEventListener('click', function() { pmQtyChange(-1); });

        var pmPlus = document.getElementById('pmQtyPlus');
        if (pmPlus) pmPlus.addEventListener('click', function() { pmQtyChange(1); });

        var pmAddBtn = document.getElementById('pmAddBtn');
        if (pmAddBtn) pmAddBtn.addEventListener('click', pmAdd);

        // ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (pmOverlay && !pmOverlay.hasAttribute('hidden')) pmClose();
                var drawer = document.getElementById('cartDrawer');
                if (drawer && !drawer.hasAttribute('hidden')) closeCart();
            }
        });

        // Initial render (empty cart)
        renderCart();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
</script>
@endpush





<style id="HIDDEN-ATTRIBUTE-OVERRIDE">
/* ═══════════════════════════════════════════════════════════
   HIDDEN ATTRIBUTE — force hide (bulletproof)
   ═══════════════════════════════════════════════════════════ */
[hidden] {
    display: none !important;
}

/* ═══════════════════════════════════════════════════════════
   CART DRAWER — slide in from right when visible
   ═══════════════════════════════════════════════════════════ */
.cart-drawer:not([hidden]) {
    display: flex !important;
    transform: translateX(0) !important;
    animation: cartSlideIn 0.35s cubic-bezier(0.34, 1.1, 0.64, 1);
}
@keyframes cartSlideIn {
    from { transform: translateX(100%); }
    to   { transform: translateX(0); }
}

/* ═══════════════════════════════════════════════════════════
   CART SCRIM — fade in
   ═══════════════════════════════════════════════════════════ */
.cart-scrim:not([hidden]) {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    animation: fadeIn 0.25s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* ═══════════════════════════════════════════════════════════
   PRODUCT MODAL — overlay fade + sheet slide
   ═══════════════════════════════════════════════════════════ */
.pm-overlay:not([hidden]) {
    display: block !important;
    animation: fadeIn 0.2s ease;
}
.pm-overlay:not([hidden]) .pm-sheet {
    transform: translateX(0) !important;
    animation: pmSlideIn 0.4s cubic-bezier(0.34, 1.1, 0.64, 1);
}
@keyframes pmSlideIn {
    from { transform: translateX(100%); }
    to   { transform: translateX(0); }
}

/* ═══════════════════════════════════════════════════════════
   PRODUCT CARD — ensure clickable (cursor + pointer events)
   ═══════════════════════════════════════════════════════════ */
.product-card {
    cursor: pointer !important;
    pointer-events: auto !important;
    -webkit-tap-highlight-color: transparent;
}
.product-card * {
    pointer-events: auto;
}
.product-add-btn {
    cursor: pointer !important;
    pointer-events: auto !important;
    z-index: 2;
    position: relative;
}
</style>
@push('styles')
<style>
    /* ===== HEADER ===== */
    .order-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; }
    .order-head-label { font-size: 11px; font-weight: 800; color: var(--t-accent); text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 4px; }
    .order-head-title { font-size: 26px; font-weight: 800; color: var(--t-text); letter-spacing: -0.03em; line-height: 1.15; }
    .order-cart-badge {
        position: relative; width: 48px; height: 48px; border-radius: 14px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        display: grid; place-items: center; color: #fff; cursor: pointer;
        box-shadow: 0 8px 20px -8px rgba(var(--t-accent-rgb), 0.7);
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
        background: var(--t-input);
        backdrop-filter: blur(10px);
        border: 1px solid var(--t-border-2);
        border-radius: 14px; transition: all 0.2s;
    }
    .order-search:focus-within {
        border-color: var(--t-accent);
        background: var(--t-input);
        box-shadow: 0 0 0 4px rgba(var(--t-accent-rgb), 0.12);
    }
    .order-search svg { color: var(--t-text-3); flex-shrink: 0; margin-right: 10px; }
    .order-search input {
        flex: 1; min-width: 0; background: transparent;
        border: none; outline: none; color: var(--t-text);
        font-size: 14px; font-family: inherit;
    }
    .order-search input::placeholder { color: var(--t-text-3); }
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
        background: var(--t-border);
        border: 1px solid var(--t-border);
        color: var(--t-text-2); font-size: 13px; font-weight: 600;
        font-family: inherit; white-space: nowrap; cursor: pointer;
        transition: all 0.2s; flex-shrink: 0;
    }
    .order-tab.active {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        color: #fff; border-color: transparent;
        box-shadow: 0 6px 14px -6px rgba(var(--t-accent-rgb), 0.6);
    }

    /* ===== PRODUCTS GRID ===== */
    .products-scroll {
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: 14px; padding-bottom: 100px;
    }
    .product-card {
        background: var(--t-card);
        backdrop-filter: blur(24px) saturate(1.5);
        border: 1px solid var(--t-border);
        border-radius: 20px; overflow: hidden; cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-direction: column;
    }
    .product-card:active { transform: scale(0.97); border-color: rgba(var(--t-accent-rgb), 0.4); }
    .product-image-wrap {
        position: relative; aspect-ratio: 1 / 1;
        background: rgba(var(--t-accent-rgb), 0.12);
        overflow: hidden;
    }
    .product-image { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
    .product-image-fallback {
        width: 100%; height: 100%; display: grid; place-items: center;
        color: rgba(var(--t-accent-rgb), 0.4);
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
        font-size: 13.5px; font-weight: 700; color: var(--t-text);
        line-height: 1.3; margin-bottom: 3px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; min-height: 36px;
    }
    .product-sku {
        font-size: 10px; color: var(--t-text-3);
        font-family: ui-monospace, monospace; font-weight: 600; margin-bottom: 10px;
    }
    .product-foot { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: auto; }
    .product-price {
        font-size: 15px; font-weight: 800; color: var(--t-accent);
        letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
    }
    .product-price .price-currency { font-size: 11px; margin-right: 1px; }
    .product-add-btn {
        cursor: pointer; border: none; font-family: inherit; transition: all 0.15s;
        width: 32px; height: 32px; border-radius: 10px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        color: #fff; display: grid; place-items: center; flex-shrink: 0;
        box-shadow: 0 4px 12px -4px rgba(var(--t-accent-rgb), 0.7);
    }
    .product-add-btn:active { transform: scale(0.9); }

    /* ===== EMPTY ===== */
    .empty-products, .empty-search { grid-column: 1 / -1; text-align: center; padding: 60px 20px; }
    .empty-products-icon, .empty-search-icon {
        width: 72px; height: 72px; margin: 0 auto 16px;
        border-radius: 20px; background: rgba(var(--t-accent-rgb), 0.1);
        border: 1px solid rgba(var(--t-accent-rgb), 0.25);
        display: grid; place-items: center; color: var(--t-accent);
    }
    .empty-products-title { font-size: 16px; font-weight: 700; color: var(--t-text); margin-bottom: 6px; }
    .empty-products-text { font-size: 13px; color: var(--t-text-3); }

    /* ===== CART FAB ===== */
    .cart-fab {
        position: fixed; bottom: 20px; right: 20px; z-index: 9998;
        display: none; align-items: center; gap: 10px;
        padding: 12px 18px 12px 14px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        border: none; border-radius: 50px; color: #fff;
        font-family: inherit; font-size: 13px; font-weight: 800;
        cursor: pointer;
        box-shadow: 0 12px 30px -10px rgba(var(--t-accent-rgb), 1);
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
        border-bottom: 1px solid var(--t-border);
    }
    .cart-drawer-back {
        width: 38px; height: 38px; border-radius: 11px;
        background: rgba(var(--t-accent-rgb), 0.12);
        border: 1px solid rgba(var(--t-accent-rgb), 0.28);
        color: var(--t-accent);
        display: grid; place-items: center;
        cursor: pointer; padding: 0; flex-shrink: 0;
    }
    .cart-drawer-head-text { flex: 1; min-width: 0; }
    .cart-drawer-title {
        font-size: 14.5px; font-weight: 800;
        color: var(--t-text); line-height: 1.2;
    }
    .cart-drawer-sub { font-size: 10.5px; color: var(--t-text-3); margin-top: 1px; }

    /* SUBMIT BUTTON SA HEADER */
    .cart-submit-header {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px;
        min-height: 38px;
        padding: 0 14px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        border: none; border-radius: 11px;
        color: #fff;
        font-size: 12px; font-weight: 800;
        font-family: inherit; cursor: pointer;
        box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.9);
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
    .cart-empty-text { font-size: 15px; font-weight: 700; color: var(--t-text); margin-bottom: 6px; }
    .cart-empty-sub { font-size: 12.5px; color: var(--t-text-3); }

    .cart-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 12px;
    }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name {
        font-size: 12.5px; font-weight: 700; color: var(--t-text);
        margin-bottom: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .cart-item-meta { font-size: 10.5px; color: var(--t-text-3); }
    .cart-item-price {
        font-size: 12.5px; font-weight: 800; color: var(--t-accent);
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
        border-top: 1px solid var(--t-border);
    }
    .cart-notes-label {
        font-size: 10.5px; font-weight: 700; color: var(--t-text-3);
        text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;
    }
    .cart-notes-input {
        width: 100%; padding: 10px 12px;
        background: var(--t-input);
        border: 1px solid var(--t-border-2);
        border-radius: 11px;
        color: var(--t-text); font-size: 12.5px; font-family: inherit;
        outline: none; resize: none;
    }
    .cart-notes-input:focus { border-color: var(--t-accent); }

    /* TOTALS SA UBOS SA BODY */
    .cart-totals {
        margin-top: 16px;
        padding: 14px 14px 4px;
        background: var(--t-input);
        border: 1px solid var(--t-border);
        border-radius: 14px;
    }
    .cart-total-row {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 12.5px; color: var(--t-text-2);
        padding: 4px 0;
    }
    .cart-total-row strong {
        color: var(--t-text); font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .cart-total-row.final {
        padding-top: 10px; margin-top: 6px;
        border-top: 1px solid var(--t-border-2);
        font-size: 14px;
    }
    .cart-total-row.final strong {
        font-size: 22px; color: var(--t-accent); font-weight: 800;
        letter-spacing: -0.02em;
    }
/* ===== PRODUCT MODAL ===== */
    .pm-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.85);
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
        border-bottom: 1px solid var(--t-border);
    }
    .pm-back {
        width: 40px; height: 40px; border-radius: 12px;
        background: rgba(var(--t-accent-rgb), 0.12);
        border: 1px solid rgba(var(--t-accent-rgb), 0.28);
        color: var(--t-accent); display: grid; place-items: center;
        cursor: pointer; padding: 0; flex-shrink: 0;
    }
    .pm-header-text { flex: 1; min-width: 0; }
    .pm-header-title { font-size: 15px; font-weight: 800; color: var(--t-text); }
    .pm-header-sub { font-size: 11px; color: var(--t-text-3); margin-top: 1px; }

    .pm-body { padding: 0 0 24px; }
    .pm-hero {
        position: relative; width: 100%; height: 220px;
        background: rgba(var(--t-accent-rgb), 0.12);
        overflow: hidden; display: flex; align-items: center; justify-content: center;
    }
    .pm-hero img { width: 100%; height: 100%; object-fit: cover; }
    .pm-hero-fallback { color: rgba(var(--t-accent-rgb), 0.4); display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .pm-hero-fallback-text { font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; }

    .pm-info { padding: 16px; }
    .pm-title { font-size: 20px; font-weight: 800; color: var(--t-text); margin-bottom: 8px; }
    .pm-meta-row { margin-bottom: 14px; }
    .pm-sku-tag {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 8px;
        background: rgba(var(--t-accent-rgb), 0.12);
        border: 1px solid rgba(var(--t-accent-rgb), 0.25);
        border-radius: 6px;
        font-family: ui-monospace, monospace; font-size: 10px;
        color: var(--t-accent); font-weight: 700;
    }
    .pm-desc { font-size: 12px; color: var(--t-text-2); line-height: 1.5; margin-bottom: 14px; }

    .pm-price-card {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 14px;
        background: rgba(var(--t-accent-rgb), 0.12);
        border: 1px solid rgba(var(--t-accent-rgb), 0.25);
        border-radius: 12px; margin-bottom: 10px;
    }
    .pm-price-label { font-size: 9.5px; font-weight: 800; color: var(--t-text-3); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px; }
    .pm-price-value { font-size: 22px; font-weight: 800; color: var(--t-accent); letter-spacing: -0.03em; line-height: 1; }

    .pm-qty-card {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px;
        background: var(--t-card);
        border: 1px solid var(--t-border);
        border-radius: 12px; margin-bottom: 10px;
    }
    .pm-qty-text { font-size: 12.5px; font-weight: 700; color: var(--t-text); }
    .pm-qty-sub { font-size: 10.5px; color: var(--t-text-3); margin-top: 1px; }
    .pm-qty-controls { display: flex; align-items: center; gap: 8px; }
    .pm-qty-btn {
        width: 38px; height: 38px; border-radius: 10px;
        background: rgba(var(--t-accent-rgb), 0.15);
        border: 1px solid rgba(var(--t-accent-rgb), 0.3);
        color: var(--t-accent); display: flex; align-items: center; justify-content: center;
        cursor: pointer; padding: 0;
    }
    .pm-qty-value { min-width: 38px; text-align: center; font-size: 18px; font-weight: 800; color: var(--t-text); }

    .pm-subtotal-card {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 14px;
        background: var(--t-input);
        border: 1px solid var(--t-border);
        border-radius: 12px; margin-bottom: 12px;
    }
    .pm-subtotal-label { font-size: 11px; color: var(--t-text-3); font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
    .pm-subtotal-value { font-size: 18px; font-weight: 800; color: var(--t-accent); font-variant-numeric: tabular-nums; }

    .pm-add-btn {
        width: 100%; min-height: 48px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2));
        border: none; border-radius: 13px; color: #fff;
        font-size: 14px; font-weight: 800; font-family: inherit;
        cursor: pointer;
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.9);
        padding: 0 18px;
    }

    /* ===== SCROLL LOCK ===== */
    /* Using html.scroll-locked from layout */

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
            background: rgba(var(--t-accent-rgb), 0.12);
            border: 1px solid rgba(var(--t-accent-rgb), 0.3);
            color: var(--t-accent);
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
            background: rgba(var(--t-accent-rgb), 0.25);
            border-color: var(--t-accent);
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
            background: rgba(var(--t-accent-rgb), 0.12);
            border: 1px solid rgba(var(--t-accent-rgb), 0.3);
            color: var(--t-accent);
            display: grid;
            place-items: center;
            text-decoration: none;
            flex-shrink: 0;
            transition: all 0.15s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .order-back-btn:hover {
            background: rgba(var(--t-accent-rgb), 0.25);
            border-color: var(--t-accent);
            color: #fff;
            transform: translateX(-2px);
        }
        .order-back-btn:active {
            transform: scale(0.94);
        }
    
    /* ═══ FIX: Duplicate cart buttons ═══ */
    /* Mobile: hide header badge, show FAB only */
    @media (max-width: 768px) {
        .order-cart-badge { display: none !important; }
    }
    /* Desktop: hide FAB, show header badge only */
    @media (min-width: 769px) {
        .cart-fab { display: none !important; }
    }

    /* Make sure header cart is always visible + sticky-friendly */
    .order-cart-badge {
        display: inline-flex !important;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 50;
    }
    .order-cart-badge span {
        display: inline-flex !important;
    }

    
    
    
    /* Hide the top header cart button */
    .order-cart-badge {
        display: none !important;
    }
    /* Ensure the bottom FAB is visible */
    .cart-fab.visible {
        display: inline-flex !important;
    }
    #headerCartCount {
        display: none !important;
    }

    
    /* ═══ CART — TOP RIGHT HEADER (clean, final) ═══ */
    .order-head {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-bottom: 16px !important;
    }
    .order-head-left {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex: 1 !important;
        min-width: 0 !important;
    }
    /* FAB — becomes top-right header button */
    .cart-fab {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        bottom: auto !important;
        left: auto !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 44px !important;
        height: 44px !important;
        padding: 0 !important;
        border-radius: 12px !important;
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        border: none !important;
        color: #0f0f14 !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        box-shadow: 0 4px 14px -4px rgba(var(--t-accent-rgb), 0.55) !important;
        margin: 0 !important;
        z-index: 1 !important;
    }
    .cart-fab.visible,
    .cart-fab[style*="display:none"],
    .cart-fab[style*="display: none"] {
        display: inline-flex !important;
    }
    .cart-fab-icon {
        position: relative !important;
        width: 22px !important;
        height: 22px !important;
        display: grid !important;
        place-items: center !important;
    }
    .cart-fab-icon svg {
        width: 20px !important;
        height: 20px !important;
        display: block !important;
    }
    .cart-fab-count {
        position: absolute !important;
        top: -10px !important;
        right: -10px !important;
        min-width: 20px !important;
        height: 20px !important;
        padding: 0 5px !important;
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #fff !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        border-radius: 100px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.5) !important;
        border: 2px solid rgba(30, 26, 22, 0.95) !important;
        z-index: 2 !important;
    }
    /* Hide old cart info text */
    .cart-fab-info {
        display: none !important;
    }

    
    /* Sparkle trail */
    .fly-sparkle {
        position: fixed;
        width: 8px; height: 8px;
        background: radial-gradient(circle, var(--t-accent), transparent);
        border-radius: 50%;
        pointer-events: none;
        z-index: 99998;
        animation: sparkleFade 0.6s ease-out forwards;
    }
    @keyframes sparkleFade {
        0%   { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0); }
    }

    /* ═══ FLY TO CART ═══ */
    .fly-clone {
        position: fixed;
        z-index: 99999;
        pointer-events: none;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 12px 40px -8px rgba(var(--t-accent-rgb), 0.7);
        border: 2px solid var(--t-accent);
        transition: all 0.9s cubic-bezier(0.55, -0.15, 0.65, 1.15);
    }
    .cart-pulse {
        animation: cartPulse 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes cartPulse {
        0%   { transform: scale(1); }
        40%  { transform: scale(1.35); }
        70%  { transform: scale(0.9); }
        100% { transform: scale(1); }
    }
    .cart-fab-count.bump {
        animation: countBump 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes countBump {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.4); }
        100% { transform: scale(1); }
    }

    /* ════════════════════════════════════════════════════════
       ORDERS CREATE — VISIBILITY FIX
       Force theme-aware backgrounds + text contrast
       ════════════════════════════════════════════════════════ */

    /* ═══ CART DRAWER — theme background ═══ */
    .cart-drawer,
    .cart-drawer-head {
        background: var(--t-card-solid) !important;
        color: var(--t-text) !important;
    }
    .cart-drawer-title {
        color: var(--t-text) !important;
    }
    .cart-drawer-sub {
        color: var(--t-text-3) !important;
    }
    .cart-drawer-back {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.28) !important;
        color: var(--t-accent) !important;
    }
    .cart-drawer-head {
        border-bottom-color: var(--t-border) !important;
    }

    /* Submit button */
    .cart-submit-header {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 16px -6px rgba(var(--t-accent-rgb), 0.7) !important;
    }
    .cart-submit-header:disabled {
        opacity: 0.4 !important;
    }

    /* Cart body background */
    .cart-drawer-body {
        background: var(--t-card-solid) !important;
        color: var(--t-text) !important;
    }

    /* Cart items */
    .cart-item {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .cart-item-name { color: var(--t-text) !important; }
    .cart-item-meta { color: var(--t-text-3) !important; }
    .cart-item-price { color: var(--t-accent) !important; }

    /* Cart empty state */
    .cart-empty-text { color: var(--t-text) !important; }
    .cart-empty-sub { color: var(--t-text-3) !important; }

    /* Cart notes */
    .cart-notes-label { color: var(--t-text-3) !important; }
    .cart-notes-input {
        background: var(--t-input) !important;
        border-color: var(--t-border-2) !important;
        color: var(--t-text) !important;
    }
    .cart-notes-input::placeholder {
        color: var(--t-text-3) !important;
    }

    /* Cart totals */
    .cart-totals {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .cart-total-row {
        color: var(--t-text-2) !important;
    }
    .cart-total-row strong {
        color: var(--t-text) !important;
    }
    .cart-total-row.final strong {
        color: var(--t-accent) !important;
    }

    /* ═══ PRODUCT MODAL (pm) — theme background ═══ */
    .pm-overlay {
        background: rgba(0, 0, 0, 0.75) !important;
    }
    .pm-sheet {
        background: var(--t-card-solid) !important;
        color: var(--t-text) !important;
    }
    .pm-header {
        background: var(--t-card-solid) !important;
        border-bottom-color: var(--t-border) !important;
    }
    .pm-header-title {
        color: var(--t-text) !important;
    }
    .pm-header-sub {
        color: var(--t-text-3) !important;
    }
    .pm-back {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.28) !important;
        color: var(--t-accent) !important;
    }

    /* Modal body */
    .pm-info {
        background: var(--t-card-solid) !important;
        color: var(--t-text) !important;
    }
    .pm-title {
        color: var(--t-text) !important;
    }
    .pm-desc {
        color: var(--t-text-2) !important;
    }
    .pm-sku-tag {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
        color: var(--t-accent) !important;
    }

    /* Modal price/qty cards */
    .pm-price-card {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.3) !important;
    }
    .pm-price-label { color: var(--t-text-3) !important; }
    .pm-price-value { color: var(--t-accent) !important; }

    .pm-qty-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pm-qty-text { color: var(--t-text) !important; }
    .pm-qty-sub { color: var(--t-text-3) !important; }
    .pm-qty-btn {
        background: rgba(var(--t-accent-rgb), 0.15) !important;
        border-color: rgba(var(--t-accent-rgb), 0.3) !important;
        color: var(--t-accent) !important;
    }
    .pm-qty-value {
        color: var(--t-text) !important;
    }

    .pm-subtotal-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .pm-subtotal-label { color: var(--t-text-3) !important; }
    .pm-subtotal-value { color: var(--t-accent) !important; }

    /* Add to Order button */
    .pm-add-btn {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 10px 24px -8px rgba(var(--t-accent-rgb), 0.7) !important;
    }

    /* Product hero fallback */
    .pm-hero {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
    }
    .pm-hero-fallback {
        color: var(--t-accent) !important;
    }

    /* ═══ MAIN PAGE — Product Cards + Search + Tabs ═══ */
    .order-head-label {
        color: var(--t-accent) !important;
    }
    .order-head-title {
        color: var(--t-text) !important;
    }
    .order-back-btn {
        background: rgba(var(--t-accent-rgb), 0.12) !important;
        border-color: rgba(var(--t-accent-rgb), 0.3) !important;
        color: var(--t-accent) !important;
    }

    .order-search {
        background: var(--t-input) !important;
        border-color: var(--t-border-2) !important;
    }
    .order-search svg { color: var(--t-text-3) !important; }
    .order-search input {
        color: var(--t-text) !important;
    }
    .order-search input::placeholder {
        color: var(--t-text-3) !important;
    }
    .order-search:focus-within {
        border-color: var(--t-accent) !important;
        background: var(--t-card) !important;
        box-shadow: 0 0 0 4px rgba(var(--t-accent-rgb), 0.15) !important;
    }

    .order-tab {
        background: rgba(var(--t-accent-rgb), 0.04) !important;
        border-color: var(--t-border) !important;
        color: var(--t-text-2) !important;
    }
    .order-tab.active {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        border-color: transparent !important;
    }

    .product-card {
        background: var(--t-card) !important;
        border-color: var(--t-border) !important;
    }
    .product-card:active {
        border-color: rgba(var(--t-accent-rgb), 0.4) !important;
    }
    .product-image-wrap {
        background: rgba(var(--t-accent-rgb), 0.08) !important;
    }
    .product-name { color: var(--t-text) !important; }
    .product-sku { color: var(--t-text-3) !important; }
    .product-price { color: var(--t-accent) !important; }
    .product-add-btn {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px -4px rgba(var(--t-accent-rgb), 0.7) !important;
    }

    /* Empty states */
    .empty-products-icon,
    .empty-search-icon {
        background: rgba(var(--t-accent-rgb), 0.1) !important;
        border-color: rgba(var(--t-accent-rgb), 0.25) !important;
        color: var(--t-accent) !important;
    }
    .empty-products-title { color: var(--t-text) !important; }
    .empty-products-text { color: var(--t-text-3) !important; }

    /* Cart FAB */
    .cart-fab {
        background: linear-gradient(135deg, var(--t-accent), var(--t-accent-2)) !important;
        color: #ffffff !important;
        box-shadow: 0 12px 30px -10px rgba(var(--t-accent-rgb), 1) !important;
    }
    .cart-fab-count {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    }

    /* ═══ LIGHT/BLUE THEME BOOST ═══ */
    html[data-theme="light"] .cart-drawer,
    html[data-theme="light"] .pm-sheet,
    html[data-theme="blue"] .cart-drawer,
    html[data-theme="blue"] .pm-sheet {
        background: #ffffff !important;
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .cart-drawer-head,
    html[data-theme="blue"] .cart-drawer-head {
        background: #f8fafc !important;
        border-bottom-color: rgba(0, 0, 0, 0.08) !important;
    }
    html[data-theme="light"] .pm-header,
    html[data-theme="blue"] .pm-header {
        background: #f8fafc !important;
        border-bottom-color: rgba(0, 0, 0, 0.08) !important;
    }
    html[data-theme="light"] .pm-title,
    html[data-theme="blue"] .pm-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .cart-drawer-title,
    html[data-theme="blue"] .cart-drawer-title,
    html[data-theme="light"] .pm-header-title,
    html[data-theme="blue"] .pm-header-title {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .cart-drawer-sub,
    html[data-theme="blue"] .cart-drawer-sub,
    html[data-theme="light"] .pm-header-sub,
    html[data-theme="blue"] .pm-header-sub {
        color: #64748b !important;
    }
    html[data-theme="light"] .cart-item,
    html[data-theme="blue"] .cart-item,
    html[data-theme="light"] .cart-totals,
    html[data-theme="blue"] .cart-totals,
    html[data-theme="light"] .pm-qty-card,
    html[data-theme="blue"] .pm-qty-card,
    html[data-theme="light"] .pm-subtotal-card,
    html[data-theme="blue"] .pm-subtotal-card {
        background: #f8fafc !important;
        border-color: rgba(0, 0, 0, 0.06) !important;
    }
    html[data-theme="light"] .cart-notes-input,
    html[data-theme="blue"] .cart-notes-input {
        background: #ffffff !important;
        border-color: rgba(0, 0, 0, 0.12) !important;
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .cart-item-name,
    html[data-theme="blue"] .cart-item-name,
    html[data-theme="light"] .cart-total-row strong,
    html[data-theme="blue"] .cart-total-row strong {
        color: #0c1e3d !important;
    }
    html[data-theme="light"] .cart-empty-text,
    html[data-theme="blue"] .cart-empty-text {
        color: #0c1e3d !important;
    }

    /* ═══ DARK/GOLD THEME — keep dark backgrounds ═══ */
    html[data-theme="default"] .cart-drawer,
    html[data-theme="default"] .pm-sheet,
    html[data-theme="dark"] .cart-drawer,
    html[data-theme="dark"] .pm-sheet {
        background: var(--t-card-solid) !important;
    }
    html[data-theme="default"] .cart-drawer-head,
    html[data-theme="dark"] .cart-drawer-head,
    html[data-theme="default"] .pm-header,
    html[data-theme="dark"] .pm-header {
        background: var(--t-card-solid) !important;
        border-bottom-color: var(--t-border) !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — EMERALD GREEN FORCE OVERRIDE
       Bisag unsang gold hardcoded → emerald
       ════════════════════════════════════════════════════════ */

    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #059669 !important;
    }

    /* Force emerald sa tanan accent colors sa light theme */
    html[data-theme="light"] .p-main *[style*="color: #c9a961"] {
        color: #10b981 !important;
    }

    /* Kill any gold shadows */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.5) !important;
    }

    /* Override gold gradient backgrounds */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"] {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }

    /* Force all spans/divs inside cards dark */
    html[data-theme="light"] .p-main,
    html[data-theme="light"] .p-main *:not([class*="badge"]):not([class*="status"]):not([class*="pill"]):not([class*="text-"]) {
        /* Fallback */
    }

    /* Headings */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f1e17 !important;
    }

    /* All text classes */
    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f1e17 !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #6b7f75 !important;
    }

    /* Inline hardcoded white → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f1e17 !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — PURE SLATE FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Gold/green/brown hardcoded colors → slate */
    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="#10b981"],
    html[data-theme="light"] .p-main *[style*="#059669"],
    html[data-theme="light"] .p-main *[style*="#a9784a"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #475569 !important;
    }

    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(16, 185, 129"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"] {
        color: #475569 !important;
    }

    /* Gradient override */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #10b981"] {
        background: linear-gradient(135deg, #475569, #334155) !important;
    }

    /* Gold shadow → slate shadow */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(71, 85, 105, 0.4) !important;
    }

    /* All text — dark */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #64748b !important;
    }

    /* Hardcoded white text → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f172a !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       DARK MODE — PURE BLACK FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Kill all gold/emerald/purple accents sa dark mode */
    html[data-theme="dark"] .p-main *[style*="#c9a961"],
    html[data-theme="dark"] .p-main *[style*="#10b981"],
    html[data-theme="dark"] .p-main *[style*="#8b5cf6"],
    html[data-theme="dark"] .p-main *[style*="#a9784a"],
    html[data-theme="dark"] .p-main *[style*="#8a5f36"],
    html[data-theme="dark"] .p-main *[style*="#b8944d"],
    html[data-theme="dark"] .p-main *[style*="#ec4899"] {
        color: #ffffff !important;
    }

    /* Gradient → white */
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #10b981"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #8b5cf6"] {
        background: linear-gradient(135deg, #ffffff, #e5e5e5) !important;
    }

    /* All text light */
    html[data-theme="dark"] .p-main h1,
    html[data-theme="dark"] .p-main h2,
    html[data-theme="dark"] .p-main h3,
    html[data-theme="dark"] .p-main h4 {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="title"],
    html[data-theme="dark"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="name"],
    html[data-theme="dark"] .p-main strong,
    html[data-theme="dark"] .p-main b {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="dark"] .p-main [class*="meta"],
    html[data-theme="dark"] .p-main [class*="desc"],
    html[data-theme="dark"] .p-main [class*="hint"] {
        color: #a3a3a3 !important;
    }

    /* Any hardcoded dark text → white */
    html[data-theme="dark"] .p-main *[style*="color: #0f172a"],
    html[data-theme="dark"] .p-main *[style*="color:#0f172a"],
    html[data-theme="dark"] .p-main *[style*="color: #1a1a1f"],
    html[data-theme="dark"] .p-main *[style*="color: #000"],
    html[data-theme="dark"] .p-main *[style*="color:#000"],
    html[data-theme="dark"] .p-main *[style*="color: black"],
    html[data-theme="dark"] .p-main *[style*="color:black"] {
        color: #ffffff !important;
    }

    /* Any hardcoded light bg → dark */
    html[data-theme="dark"] .p-main *[style*="background: #ffffff"],
    html[data-theme="dark"] .p-main *[style*="background:#ffffff"],
    html[data-theme="dark"] .p-main *[style*="background: white"],
    html[data-theme="dark"] .p-main *[style*="background: #f8fafc"],
    html[data-theme="dark"] .p-main *[style*="background: #f1f5f9"] {
        background: #0a0a0a !important;
    }
<style id="THEME-TEXT-VISIBILITY">
/* ════════════════════════════════════════════════════════════════
   UNIVERSAL TEXT VISIBILITY — LAST IN CASCADE, WINS ALL
   ════════════════════════════════════════════════════════════════ */

/* ═══ BASE — everything gets theme text ═══ */
html[data-theme] .p-main,
html[data-theme] .p-main *:not(svg):not(path):not(circle):not(rect):not(line):not(polyline):not(polygon):not(g):not(use):not(defs):not(symbol) {
    color: var(--t-text) !important;
}

/* ═══ SECONDARY TEXT — subs, metas, hints, labels ═══ */
html[data-theme] .p-main [class*="-sub"]:not([class*="-submit"]):not([class*="-btn"]):not([class*="btn-"]):not([class*="-button"]),
html[data-theme] .p-main [class*="-meta"]:not([class*="card-meta"]):not([class*="item-meta"]):not([class*="head-meta"]):not([class*="-metadata"]),
html[data-theme] .p-main [class*="-hint"],
html[data-theme] .p-main [class*="-desc"]:not([class*="-description"]):not([class*="card-desc"]),
html[data-theme] .p-main [class*="empty-text"],
html[data-theme] .p-main .cart-drawer-sub,
html[data-theme] .p-main .cart-notes-label,
html[data-theme] .p-main .pm-qty-sub,
html[data-theme] .p-main .pm-price-label,
html[data-theme] .p-main .pm-subtotal-label,
html[data-theme] .p-main .pm-header-sub,
html[data-theme] .p-main .pm-desc,
html[data-theme] .p-main .product-sku,
html[data-theme] .p-main .cart-item-meta,
html[data-theme] .p-main .cart-total-row:not(.final),
html[data-theme] .p-main .order-search input::placeholder,
html[data-theme] .p-main .cart-notes-input::placeholder {
    color: var(--t-text-3) !important;
}

/* ═══ MUTED TEXT ═══ */
html[data-theme] .p-main .cart-empty-sub,
html[data-theme] .p-main .empty-products-text,
html[data-theme] .p-main .pm-hero-fallback-text {
    color: var(--t-text-3) !important;
}

/* ═══ ACCENT TEXT — prices, totals, active ═══ */
html[data-theme] .p-main [class*="-price"]:not([class*="-price-card"]):not([class*="-price-label"]),
html[data-theme] .p-main [class*="-amount"],
html[data-theme] .p-main .pm-price-value,
html[data-theme] .p-main .pm-subtotal-value,
html[data-theme] .p-main .product-price,
html[data-theme] .p-main .cart-item-price,
html[data-theme] .p-main .cart-total-row.final strong,
html[data-theme] .p-main .order-head-label {
    color: var(--t-accent) !important;
}

/* ═══ WHITE TEXT ON ACCENT BUTTONS — always visible ═══ */
html[data-theme] .p-main .order-tab.active,
html[data-theme] .p-main .product-add-btn,
html[data-theme] .p-main .pm-add-btn,
html[data-theme] .p-main .cart-submit-header,
html[data-theme] .p-main .cart-drawer-back,
html[data-theme] .p-main .pm-back,
html[data-theme] .p-main .order-back-btn,
html[data-theme] .p-main .cart-fab {
    color: #ffffff !important;
}
html[data-theme] .p-main .order-tab.active *,
html[data-theme] .p-main .product-add-btn *,
html[data-theme] .p-main .pm-add-btn *,
html[data-theme] .p-main .cart-submit-header *,
html[data-theme] .p-main .cart-drawer-back *,
html[data-theme] .p-main .pm-back *,
html[data-theme] .p-main .order-back-btn *,
html[data-theme] .p-main .cart-fab * {
    color: #ffffff !important;
    stroke: #ffffff !important;
}

/* ═══ SVGs — use currentColor stroke ═══ */
html[data-theme] .p-main svg {
    stroke: currentColor;
}

/* ═══ INPUTS — theme-aware text ═══ */
html[data-theme] .p-main input,
html[data-theme] .p-main select,
html[data-theme] .p-main textarea,
html[data-theme] .p-main .order-search input,
html[data-theme] .p-main .cart-notes-input {
    color: var(--t-text) !important;
    background: var(--t-input) !important;
    border-color: var(--t-border-2) !important;
}

/* ═══ SEMANTIC BADGES — keep their colors ═══ */
html[data-theme] .p-main [class*="badge"],
html[data-theme] .p-main .product-badge {
    color: inherit;
}
html[data-theme] .p-main .product-badge.ok { color: #ffffff !important; }
html[data-theme] .p-main .product-badge.low { color: #ffffff !important; }
html[data-theme] .p-main .product-badge.out { color: #ffffff !important; }

/* ═══ INLINE HARDCODED WHITE → THEME TEXT (LIGHT THEMES) ═══ */
html[data-theme="light"] [style*="color: #fff"],
html[data-theme="light"] [style*="color:#fff"],
html[data-theme="light"] [style*="color: #ffffff"],
html[data-theme="light"] [style*="color:#ffffff"],
html[data-theme="light"] [style*="color: white"],
html[data-theme="light"] [style*="color:white"],
html[data-theme="light"] [style*="color: #fafafa"],
html[data-theme="light"] [style*="color:#fafafa"],
html[data-theme="light"] [style*="color: #f5f3f0"],
html[data-theme="light"] [style*="color:#f5f3f0"],
html[data-theme="blue"] [style*="color: #fff"],
html[data-theme="blue"] [style*="color:#fff"],
html[data-theme="blue"] [style*="color: #ffffff"],
html[data-theme="blue"] [style*="color:#ffffff"],
html[data-theme="blue"] [style*="color: white"],
html[data-theme="blue"] [style*="color:white"],
html[data-theme="blue"] [style*="color: #fafafa"],
html[data-theme="blue"] [style*="color:#fafafa"],
html[data-theme="blue"] [style*="color: #f5f3f0"],
html[data-theme="blue"] [style*="color:#f5f3f0"] {
    color: var(--t-text) !important;
}

/* ═══ INLINE HARDCODED DARK → THEME TEXT (DARK THEMES) ═══ */
html[data-theme="dark"] [style*="color: #000"],
html[data-theme="dark"] [style*="color:#000"],
html[data-theme="dark"] [style*="color: black"],
html[data-theme="dark"] [style*="color:black"],
html[data-theme="dark"] [style*="color: #0c1e3d"],
html[data-theme="dark"] [style*="color:#0c1e3d"],
html[data-theme="dark"] [style*="color: #1a1a1f"],
html[data-theme="dark"] [style*="color:#1a1a1f"],
html[data-theme="dark"] [style*="color: #0f172a"],
html[data-theme="dark"] [style*="color:#0f172a"],
html[data-theme="dark"] [style*="color: #0f1e17"],
html[data-theme="default"] [style*="color: #000"],
html[data-theme="default"] [style*="color:#000"],
html[data-theme="default"] [style*="color: black"],
html[data-theme="default"] [style*="color:black"],
html[data-theme="default"] [style*="color: #0c1e3d"],
html[data-theme="default"] [style*="color:#0c1e3d"],
html[data-theme="default"] [style*="color: #1a1a1f"],
html[data-theme="default"] [style*="color:#1a1a1f"],
html[data-theme="default"] [style*="color: #0f172a"],
html[data-theme="default"] [style*="color:#0f172a"],
html[data-theme="default"] [style*="color: #0f1e17"] {
    color: var(--t-text) !important;
}

/* ═══ BACKGROUND OVERRIDES — Dark bg sa light themes ═══ */
html[data-theme="light"] [style*="background: #0f0f14"],
html[data-theme="light"] [style*="background:#0f0f14"],
html[data-theme="light"] [style*="background: #1e1a16"],
html[data-theme="light"] [style*="background:#1e1a16"],
html[data-theme="light"] [style*="background: #15120f"],
html[data-theme="blue"] [style*="background: #0f0f14"],
html[data-theme="blue"] [style*="background:#0f0f14"],
html[data-theme="blue"] [style*="background: #1e1a16"],
html[data-theme="blue"] [style*="background:#1e1a16"],
html[data-theme="blue"] [style*="background: #15120f"] {
    background: var(--t-card) !important;
}

/* ═══ BACKGROUND OVERRIDES — Light bg sa dark themes ═══ */
html[data-theme="dark"] [style*="background: #fff"],
html[data-theme="dark"] [style*="background:#fff"],
html[data-theme="dark"] [style*="background: #ffffff"],
html[data-theme="dark"] [style*="background:#ffffff"],
html[data-theme="dark"] [style*="background: white"],
html[data-theme="dark"] [style*="background: #f8fafc"],
html[data-theme="dark"] [style*="background: #f1f5f9"],
html[data-theme="default"] [style*="background: #fff"],
html[data-theme="default"] [style*="background:#fff"],
html[data-theme="default"] [style*="background: #ffffff"],
html[data-theme="default"] [style*="background:#ffffff"],
html[data-theme="default"] [style*="background: white"],
html[data-theme="default"] [style*="background: #f8fafc"],
html[data-theme="default"] [style*="background: #f1f5f9"] {
    background: var(--t-card) !important;
}

/* ═══ SPECIFIC: Order page elements ═══ */
html[data-theme] .order-head-title { color: var(--t-text) !important; }
html[data-theme] .order-head-label { color: var(--t-accent) !important; }

html[data-theme] .order-search input { color: var(--t-text) !important; }
html[data-theme] .order-search input::placeholder { color: var(--t-text-3) !important; }
html[data-theme] .order-search svg { stroke: var(--t-text-3) !important; }

html[data-theme] .order-tab { color: var(--t-text-2) !important; }
html[data-theme] .order-tab.active { color: #ffffff !important; }

html[data-theme] .product-name { color: var(--t-text) !important; }
html[data-theme] .product-sku { color: var(--t-text-3) !important; }
html[data-theme] .product-price { color: var(--t-accent) !important; }

html[data-theme] .empty-products-title { color: var(--t-text) !important; }
html[data-theme] .empty-products-text { color: var(--t-text-3) !important; }

/* ═══ Cart drawer ═══ */
html[data-theme] .cart-drawer,
html[data-theme] .cart-drawer-head,
html[data-theme] .cart-drawer-body { color: var(--t-text) !important; background: var(--t-card-solid) !important; }
html[data-theme] .cart-drawer-title { color: var(--t-text) !important; }
html[data-theme] .cart-drawer-sub { color: var(--t-text-3) !important; }
html[data-theme] .cart-item { background: var(--t-card) !important; border-color: var(--t-border) !important; }
html[data-theme] .cart-item-name { color: var(--t-text) !important; }
html[data-theme] .cart-item-meta { color: var(--t-text-3) !important; }
html[data-theme] .cart-item-price { color: var(--t-accent) !important; }
html[data-theme] .cart-empty-text { color: var(--t-text) !important; }
html[data-theme] .cart-empty-sub { color: var(--t-text-3) !important; }
html[data-theme] .cart-notes-input { background: var(--t-input) !important; border-color: var(--t-border-2) !important; color: var(--t-text) !important; }
html[data-theme] .cart-totals { background: var(--t-card) !important; border-color: var(--t-border) !important; }
html[data-theme] .cart-total-row { color: var(--t-text-2) !important; }
html[data-theme] .cart-total-row strong { color: var(--t-text) !important; }
html[data-theme] .cart-total-row.final strong { color: var(--t-accent) !important; }

/* ═══ Product modal ═══ */
html[data-theme] .pm-sheet,
html[data-theme] .pm-header,
html[data-theme] .pm-info { color: var(--t-text) !important; background: var(--t-card-solid) !important; }
html[data-theme] .pm-header-title { color: var(--t-text) !important; }
html[data-theme] .pm-header-sub { color: var(--t-text-3) !important; }
html[data-theme] .pm-title { color: var(--t-text) !important; }
html[data-theme] .pm-desc { color: var(--t-text-2) !important; }
html[data-theme] .pm-sku-tag { color: var(--t-accent) !important; }
html[data-theme] .pm-price-card { background: rgba(var(--t-accent-rgb), 0.12) !important; border-color: rgba(var(--t-accent-rgb), 0.3) !important; }
html[data-theme] .pm-price-label { color: var(--t-text-3) !important; }
html[data-theme] .pm-price-value { color: var(--t-accent) !important; }
html[data-theme] .pm-qty-card { background: var(--t-card) !important; border-color: var(--t-border) !important; }
html[data-theme] .pm-qty-text { color: var(--t-text) !important; }
html[data-theme] .pm-qty-sub { color: var(--t-text-3) !important; }
html[data-theme] .pm-qty-value { color: var(--t-text) !important; }
html[data-theme] .pm-qty-btn { color: var(--t-accent) !important; }
html[data-theme] .pm-qty-btn svg { stroke: var(--t-accent) !important; }
html[data-theme] .pm-subtotal-card { background: var(--t-card) !important; border-color: var(--t-border) !important; }
html[data-theme] .pm-subtotal-label { color: var(--t-text-3) !important; }
html[data-theme] .pm-subtotal-value { color: var(--t-accent) !important; }
html[data-theme] .pm-add-btn { color: #ffffff !important; }
html[data-theme] .pm-add-btn svg { stroke: #ffffff !important; }

/* ═══ Buttons with icon — always visible ═══ */
html[data-theme] .cart-drawer-back svg,
html[data-theme] .pm-back svg,
html[data-theme] .order-back-btn svg { stroke: currentColor; }

/* ═══ Light theme specific tweaks ═══ */
html[data-theme="light"] .order-search input,
html[data-theme="blue"] .order-search input {
    color: #0f172a !important;
}
html[data-theme="light"] .product-name,
html[data-theme="blue"] .product-name {
    color: #0f172a !important;
}

/* ═══ Dark theme specific tweaks ═══ */
html[data-theme="dark"] .order-search input,
html[data-theme="default"] .order-search input {
    color: #fafafa !important;
}
html[data-theme="dark"] .product-name,
html[data-theme="default"] .product-name {
    color: #fafafa !important;
}

/* ═══ FALLBACK — anything with hardcoded light bg in dark theme gets dark text override ═══ */
html[data-theme="dark"] .p-main [style*="background: #fff"] *:not(svg):not(path),
html[data-theme="dark"] .p-main [style*="background:#fff"] *:not(svg):not(path),
html[data-theme="dark"] .p-main [style*="background: #ffffff"] *:not(svg):not(path),
html[data-theme="default"] .p-main [style*="background: #fff"] *:not(svg):not(path),
html[data-theme="default"] .p-main [style*="background:#fff"] *:not(svg):not(path),
html[data-theme="default"] .p-main [style*="background: #ffffff"] *:not(svg):not(path) {
    color: #0f172a !important;
}

/* ═══ FALLBACK — anything with hardcoded dark bg in light theme gets light text override ═══ */
html[data-theme="light"] .p-main [style*="background: #0f0f14"] *:not(svg):not(path),
html[data-theme="light"] .p-main [style*="background:#0f0f14"] *:not(svg):not(path),
html[data-theme="light"] .p-main [style*="background: #1e1a16"] *:not(svg):not(path),
html[data-theme="blue"] .p-main [style*="background: #0f0f14"] *:not(svg):not(path),
html[data-theme="blue"] .p-main [style*="background:#0f0f14"] *:not(svg):not(path),
html[data-theme="blue"] .p-main [style*="background: #1e1a16"] *:not(svg):not(path) {
    color: #fafafa !important;
}
</style>
</style>
@endpush