<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &mdash; Kape Miarayon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Manrope', 'system-ui', 'sans-serif'], serif: ['"Playfair Display"', 'serif'] },
                    colors: {
                        brand: { 50: '#FFF7EE', 100: '#FFEBD5', 200: '#FFD6AB', 300: '#FFBC77', 400: '#FF9F45', 500: '#FF8512', 600: '#E86100', 700: '#BF4D00', 800: '#8F3B05', 900: '#5E2808' },
                        ink: { 50: '#FAF9F7', 100: '#F3F1EE', 200: '#E8E5E0', 300: '#D3CEC6', 400: '#A29B90', 500: '#78716A', 600: '#57514B', 700: '#3F3A36', 800: '#2A2623', 900: '#1A1715' }
                    },
                    boxShadow: { card: '0 1px 2px rgba(26,23,21,.04)', pop: '0 16px 40px -8px rgba(26,23,21,.25)' }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root { --brand:#E86100; --brand-dark:#BF4D00; --ink:#1A1715; --muted:#78716A; --line:#F0EDE8; --card:#F9F7F4; }
        html { scroll-padding-top: 6rem; }
        body { background:#FDFCFB; color:var(--ink); font-family:'Manrope',system-ui,sans-serif; -webkit-font-smoothing:antialiased; }
        
        /* Premium Eye-Friendly Theme */
        body, .bg-\[\#FDFCFB\], .flex.h-screen { background-color: #F3F1EE !important; }
        #sidebar { 
            background: rgba(255, 255, 255, 0.45) !important; /* 45% ra ang opacity */
            backdrop-filter: blur(30px) saturate(150%) !important; /* Kusog nga blur para readable gihapon */
            -webkit-backdrop-filter: blur(30px) saturate(150%) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.05) !important;
        }
        
        #sidebar .nav-group { font-size:11px; font-weight:800; color:#A29B90; padding:0 14px; margin:20px 0 6px; text-transform:uppercase; letter-spacing:0.05em; }
        #sidebar .nav-link { display:flex; align-items:center; gap:12px; padding:10px 14px; margin-bottom:3px; border-radius:12px; font-size:14px; font-weight:600; color:#1A1715; transition: all 0.2s ease; text-shadow: 0 1px 2px rgba(255,255,255,0.8); }
        #sidebar .nav-link i { width:20px; text-align:center; font-size:15px; color:#78716A; transition: all 0.2s ease; }
        #sidebar .nav-link:hover { 
            background: rgba(255, 255, 255, 0.8) !important; 
            backdrop-filter: blur(10px) !important;
            color:#1A1715; 
            transform: translateX(4px); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
        }
        #sidebar .nav-link.active { background: linear-gradient(135deg, #FF8512 0%, #E86100 100%); color:#fff; font-weight:700; box-shadow: 0 4px 12px rgba(232,97,0,0.3); }
        #sidebar .nav-link.active i { color:#fff; }

        .card { background:#FFFFFF !important; border:1px solid #F0EDE8 !important; border-radius:16px; box-shadow: 0 2px 10px -2px rgba(26, 23, 21, 0.03), 0 4px 20px -4px rgba(26, 23, 21, 0.03) !important; transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 30px -10px rgba(26, 23, 21, 0.08) !important; }
        
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.6rem 1.1rem; border-radius:10px; font-size:14px; font-weight:700; transition:all .15s ease; cursor:pointer; }
        .btn:active { transform:scale(0.96); }
        .btn-primary { background: linear-gradient(135deg, #FF8512 0%, #E86100 100%); color:#fff; border:none; box-shadow: 0 4px 12px -2px rgba(232, 97, 0.3); }
        .btn-primary:hover { background: linear-gradient(135deg, #E86100 0%, #BF4D00 100%); }
        .btn-secondary { background:#fff; color:#3F3A36; border:1px solid #E8E5E0; } .btn-secondary:hover { background:#FAF9F7; }
        
        .table-clean { width:100%; border-collapse:collapse; font-size:14px; }
        .table-clean th { text-align:left; font-size:11px; font-weight:700; color:var(--muted); padding:1rem; background:#FAF9F7; border-bottom:1.5px solid var(--line); text-transform:uppercase; }
        .table-clean td { padding:1rem; border-bottom:1px solid #F3F0EB; vertical-align: middle; }
        .table-clean tr:hover td { background:#FDFCFB; }
        
        .input { width:100%; padding:.6rem .8rem; border:1.5px solid #E8E5E0; border-radius:10px; font-size:14px; background:#FAFAFA; transition:all .15s ease; }
        .input:focus { outline:none; background:#fff; border-color:var(--brand); box-shadow:0 0 0 4px rgba(232,97,0,.15); }

        /* Choices.js Override */
        .choices { margin-bottom: 0 !important; width: 100% !important; }
        .choices__inner { background: #FFFFFF !important; border: 1px solid #D3CEC6 !important; border-radius: 10px !important; padding: 10px 14px !important; font-size: 14px !important; font-weight: 600 !important; min-height: 44px !important; display: flex !important; align-items: center !important; }
        .choices__list--dropdown { background: #FFFFFF !important; border: 1px solid #E8E5E0 !important; border-radius: 12px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; z-index: 9999 !important; }
        .choices__list--dropdown .choices__item--selectable.is-highlighted { background: #FFF7EE !important; color: #E86100 !important; }

        /* Loading Bar */
        #top-loader { position: fixed; top: 0; left: 0; height: 3px; background: #E86100; width: 0%; z-index: 99999; transition: width 0.3s, opacity 0.3s; opacity: 0; }

        /* Mobile Fixes */
        @media (max-width: 1023px) {
            #sidebar { 
                transform:translateX(-100%); 
                transition: transform 0.3s ease; 
                background: rgba(255, 255, 255, 0.40) !important; /* 40% opacity - Sakto nga glass */
                backdrop-filter: blur(16px) saturate(180%) !important; /* 16px blur - Klaro pero dili malibog */
                -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
                border-right: 1px solid rgba(255, 255, 255, 0.5) !important;
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.08) !important;
            }
            body.nav-open #sidebar { transform:translateX(0); }
            body.nav-open #navBackdrop { display:block; }
            html, body { overflow-x: hidden !important; height: 100dvh !important; }
            
            /* Tagoa ang floating hamburger kung open ang sidebar para dili mo-overlap */
            body.nav-open #floatingMenuWrapper { display: none !important; }
        }
    </style>
    
    <meta name="color-scheme" content="light">
    <!-- Choices JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <!-- Hotwire Turbo -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.min.js"></script>
    <script>
        document.addEventListener('turbo:load', () => { document.dispatchEvent(new Event('DOMContentLoaded')); });
        document.addEventListener('turbo:click', function(event) { if (event.target.closest('#sidebar')) document.body.classList.remove('nav-open'); });
    </script>
</head>
<body>
<div id="top-loader"></div>

@php
    $recentNotifs = \App\Models\ActivityLog::whereNull('read_at')->latest()->take(6)->get();
    $notifCount = \App\Models\ActivityLog::whereNull('read_at')->count();
    $userName = Auth::user()->name ?? 'Admin';
    $initial  = strtoupper(substr($userName, 0, 1));
    
    // GIBALIK NAKO SA NORMAL NGA CATEGORIES
    $nav = [
        ['label' => null, 'items' => [['Dashboard', 'dashboard', 'dashboard', 'fa-table-cells-large']]],
        ['label' => 'Financials', 'items' => [['Purchases', 'purchases.index', 'purchases.*', 'fa-cart-shopping'], ['Expenses', 'expenses.index', 'expenses.*', 'fa-receipt']]],
        ['label' => 'Management', 'items' => [['Customers', 'stores.index', 'stores.*', 'fa-user-group'], ['Products', 'products.index', 'products.*', 'fa-cube'], ['Distributions', 'deliveries.index', 'deliveries.*', 'fa-truck'], ['Warehouse', 'inventory.index', 'inventory.*', 'fa-warehouse']]],
        ['label' => 'Consignment', 'items' => [['Sales reports', 'sales-reports.index', 'sales-reports.*', 'fa-file-lines'], ['Payments', 'payments.index', 'payments.*', 'fa-credit-card']]],
    ];
@endphp

<div id="navBackdrop" class="hidden fixed inset-0 z-30 bg-transparent lg:hidden" onclick="document.body.classList.remove('nav-open')"></div></div></div>

<div class="flex h-screen overflow-hidden">
    {{-- SIDEBAR --}}
    <aside id="sidebar" class="w-[260px] h-full flex flex-col flex-shrink-0 z-40 lg:relative fixed inset-y-0 left-0">
        
        {{-- GI-USAB NAKO ANG HEADER SA SIDEBAR PARA MAO NI SA IMO SCREENSHOT --}}
        <div class="h-[100px] flex items-center px-5 flex-shrink-0 border-b border-ink-200/50">
            <div class="flex items-center gap-4 group cursor-pointer w-full">
                <!-- GIPADAKO ANG LOGO -->
                <div class="w-[60px] h-[60px] rounded-full overflow-hidden bg-white shadow-sm border border-ink-200 shrink-0">
                    <img src="{{ asset('images/Kape_Logo.png') }}" alt="Kape Miarayon logo" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col justify-center min-w-0">
                    <div class="flex items-baseline gap-1.5 leading-none">
                        <span class="font-serif italic font-bold text-[24px] text-brand-600 tracking-tight">Kape</span>
                        <span class="font-black text-[19px] text-ink-900 tracking-tighter">Miarayon</span>
                    </div>
                    <!-- GIBUTANGAN OG whitespace-nowrap PARA DILI MOBALI ANG LINYA -->
                    <span class="text-[10px] font-bold text-ink-700 uppercase tracking-widest mt-1.5 whitespace-nowrap drop-shadow-sm">Wholesale & Retailer</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 pb-4 pt-2" aria-label="Main">
            @foreach($nav as $group)
                @if($group['label'])<div class="nav-group">{{ $group['label'] }}</div>@endif
                @foreach($group['items'] as [$text, $route, $pattern, $icon])
                    <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}">
                        <i class="fas {{ $icon }}"></i><span>{{ $text }}</span>
                    </a>
                @endforeach
            @endforeach
            <div class="nav-group mt-4">SYSTEM</div>
            <a href="{{ route('activity.logs') }}" class="nav-link {{ request()->routeIs('activity.logs') ? 'active' : '' }}">
                <i class="fas fa-history"></i><span>Transactions</span>
            </a>
        </nav>

        <div class="p-3 border-t border-ink-200/50 bg-white/40 backdrop-blur-md flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-extrabold text-sm border border-brand-200 shrink-0">{{ $initial }}</div>
                <div class="leading-tight min-w-0">
                    <div class="text-[12px] font-bold text-ink-900 truncate max-w-[80px]">{{ $userName }}</div>
                    <div class="text-[8px] font-bold text-brand-600 uppercase tracking-wider mt-0.5 truncate">Admin</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-red-600 bg-white border border-red-200 hover:bg-red-50 transition-all shadow-sm" title="Log out">
                    <i class="fas fa-right-from-bracket"></i> Log out
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col h-full min-w-0 relative">
        
        {{-- FIXED HEADER --}}
        <header id="mainHeader" class="fixed top-0 left-0 lg:left-[260px] right-0 h-[56px] bg-white/40 backdrop-blur-2xl border-b border-white/50 z-30 transition-transform duration-300 flex items-center justify-between px-4 sm:px-6 shadow-sm">
            <div class="flex flex-col justify-center min-w-0">
                <h1 class="text-[18px] sm:text-[20px] font-extrabold tracking-tight text-ink-900 leading-tight truncate text-left">@yield('title', 'Dashboard')</h1>
                <p class="text-[10px] sm:text-[11px] font-medium text-ink-500 truncate mt-0 text-left">@yield('subtitle', 'Welcome back!')</p>
            </div>

            <div class="flex items-center shrink-0 relative z-[60]">
                <div class="relative" id="notifMenu">
                    <button type="button" onclick="toggleNotif(event)" class="relative w-9 h-9 flex items-center justify-center text-ink-600 transition-colors cursor-pointer z-[100]">
                        <i class="fas fa-bell text-[18px] pointer-events-none"></i>
                        @if(($notifCount ?? 0) > 0)
                            <span class="absolute top-1 right-1 flex h-[13px] min-w-[13px] items-center justify-center rounded-full bg-red-500 text-[8px] font-black text-white ring-1 ring-white px-0.5 pointer-events-none">{{ $notifCount > 9 ? "9+" : $notifCount }}</span>
                        @endif
                    </button>
                    
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 md:w-96 bg-white border border-ink-200 rounded-2xl shadow-2xl z-50 overflow-hidden transform origin-top-right transition-all duration-200">
                        <div class="px-4 py-3 border-b border-ink-100 flex justify-between items-center bg-ink-50/50">
                            <h3 class="text-sm font-bold text-ink-900">Notifications</h3>
                            <span class="text-[10px] font-bold bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full">{{ $notifCount ?? 0 }} New</span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto">
                            @forelse(($recentNotifs ?? collect()) as $notif)
                                @php
                                    $link = match($notif->module) { 'Purchases' => route('purchases.index'), 'Expenses' => route('expenses.index'), 'Payments' => route('payments.index'), 'Deliveries' => route('deliveries.index'), 'Inventory' => route('inventory.index'), default => route('activity.logs') };
                                    $iconColor = match($notif->module) { 'Purchases' => 'bg-amber-100 text-amber-600', 'Expenses' => 'bg-red-100 text-red-600', 'Payments' => 'bg-emerald-100 text-emerald-600', 'Deliveries' => 'bg-blue-100 text-blue-600', 'Inventory' => 'bg-purple-100 text-purple-600', default => 'bg-gray-100 text-gray-600' };
                                    $iconClass = match($notif->module) { 'Purchases' => 'fa-shopping-cart', 'Expenses' => 'fa-receipt', 'Payments' => 'fa-money-bill-wave', 'Deliveries' => 'fa-truck', 'Inventory' => 'fa-boxes', default => 'fa-bell' };
                                @endphp
                                <a href="{{ $link }}" onclick="markNotifRead(event, {{ $notif->id }})" class="block w-full text-left px-4 py-3 hover:bg-ink-50 transition-colors border-b border-ink-50 last:border-0 group">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $iconColor }} mt-0.5 shadow-sm"><i class="fas {{ $iconClass }} text-xs"></i></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[13px] font-bold text-ink-900 group-hover:text-brand-600 transition-colors">{{ $notif->module }} Update</p>
                                            <p class="text-xs text-ink-500 line-clamp-2 mt-0.5">{{ $notif->description }}</p>
                                            <p class="text-[10px] font-semibold text-ink-400 mt-1"><i class="far fa-clock mr-1"></i>{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center">
                                    <div class="w-12 h-12 bg-ink-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-bell-slash text-ink-300 text-lg"></i></div>
                                    <p class="text-xs font-semibold text-ink-600">No notifications yet.</p>
                                </div>
                            @endforelse
                        </div>
                        <a href="{{ route('activity.logs') }}" onclick="markAllNotifRead(event)" class="block w-full py-3 text-center text-xs font-bold text-brand-600 hover:text-brand-700 bg-white hover:bg-brand-50 border-t border-ink-100 transition-colors">View All Transactions</a>
                    </div>
                </div>
            </div>
        </header>

        {{-- FLOATING HAMBURGER --}}
        <div id="floatingMenuWrapper" class="fixed top-[64px] left-1 z-40 flex items-center lg:hidden transition-all duration-300 transform translate-x-0 opacity-100">
            <button type="button" id="mobileMenuBtn" class="w-10 h-10 flex items-center justify-center text-ink-900 hover:text-brand-600 active:scale-90 transition-all" onclick="document.body.classList.add('nav-open')" aria-label="Open menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="4" ry="4"/><line x1="15" x2="15" y1="3" y2="21"/><path d="m10 8 4 4-4 4"/></svg>
            </button>
        </div>

        {{-- MAIN SCROLLABLE AREA --}}
        <main class="flex-1 overflow-y-auto px-4 sm:px-8 pb-24 md:pb-10 pt-[64px]" style="padding-bottom: calc(6rem + env(safe-area-inset-bottom, 0px)) !important;">
            <div class="max-w-[1600px] mx-auto w-full">
                @if(session('success'))
                    <div role="status" class="mb-6 px-4 py-3 bg-emerald-50 text-emerald-800 rounded-xl flex items-center gap-3 text-sm font-bold border border-emerald-200 shadow-sm"><i class="fas fa-circle-check text-emerald-500 text-lg"></i> {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div role="alert" class="mb-6 px-4 py-3 bg-red-50 text-red-800 rounded-xl flex items-center gap-3 text-sm font-bold border border-red-200 shadow-sm"><i class="fas fa-circle-exclamation text-red-500 text-lg"></i> {{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</div>

<div id="customConfirmModal" class="fixed inset-0 z-[100] hidden bg-ink-900/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-pop w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-300" id="confirmModalContent">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-4 text-lg animate-bounce"><i class="fas fa-triangle-exclamation"></i></div>
            <h3 class="text-lg font-extrabold text-ink-900 mb-1">Are you sure?</h3>
            <p class="text-sm text-ink-500" id="confirmMessage">This action can't be undone.</p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="button" onclick="closeConfirmModal()" class="btn btn-secondary flex-1">Cancel</button>
            <button type="button" id="confirmBtn" class="btn btn-danger flex-1">Yes, delete</button>
        </div>
    </div>
</div>

@stack('scripts')
<script>
    window.toggleNotif = function(e) { if(e) e.stopPropagation(); const d = document.getElementById('notifDropdown'); if(d) d.classList.toggle('hidden'); };
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function(e) { const d = document.getElementById('notifDropdown'); const b = e.target.closest('button[onclick*="toggleNotif"]'); if(d && !d.contains(e.target) && !b) d.classList.add('hidden'); });
        
        // ==========================================
        // HEADER ANIMATION & FLOATING BUTTON AUTO-HIDE
        // ==========================================
        let lastScrollTop = 0;
        const mainEl = document.querySelector('main');
        const header = document.getElementById('mainHeader');
        const floatingBtn = document.getElementById('floatingMenuWrapper');
        let idleTimer;

        // Ipakita ang button
        const showFloatingBtn = () => {
            if (floatingBtn) {
                floatingBtn.classList.remove('-translate-x-[150%]', 'opacity-0');
                floatingBtn.classList.add('translate-x-0', 'opacity-100');
            }
        };

        // Itago ang button (pabaliwat sa wala)
        const hideFloatingBtn = () => {
            if (floatingBtn && !document.body.classList.contains('nav-open')) {
                floatingBtn.classList.remove('translate-x-0', 'opacity-100');
                floatingBtn.classList.add('-translate-x-[150%]', 'opacity-0');
            }
        };

        // Reset timer inig naay lihok
        const resetIdleTimer = () => {
            showFloatingBtn();
            clearTimeout(idleTimer);
            idleTimer = setTimeout(hideFloatingBtn, 2000); // 2 segundos idle
        };

        if (mainEl && header) {
            mainEl.addEventListener('scroll', function() {
                resetIdleTimer();
                
                let scrollTop = mainEl.scrollTop;
                if (scrollTop > lastScrollTop) {
                    header.classList.remove('-translate-y-full');
                } else {
                    if (scrollTop > 10) header.classList.add('-translate-y-full');
                }
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            }, { passive: true });
        }

        // I-reset ang timer inig click o touch bisan asa
        document.addEventListener('click', resetIdleTimer, { passive: true });
        document.addEventListener('touchstart', resetIdleTimer, { passive: true });

        // Sugdi ang timer
        resetIdleTimer();

        // CONFIRMATION MODAL
        const modal = document.getElementById('customConfirmModal'), content = document.getElementById('confirmModalContent'), messageEl = document.getElementById('confirmMessage'), confirmBtn = document.getElementById('confirmBtn');
        let formToSubmit = null;
        window.openConfirmModal = function (message, form) { formToSubmit = form; messageEl.innerText = message; modal.classList.remove('hidden'); setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10); confirmBtn.focus(); };
        window.closeConfirmModal = function () { modal.classList.add('opacity-0'); content.classList.remove('scale-100'); content.classList.add('scale-95'); setTimeout(() => { modal.classList.add('hidden'); formToSubmit = null; }, 200); };
        confirmBtn.addEventListener('click', () => { if (formToSubmit) formToSubmit.submit(); });
        modal.addEventListener('click', e => { if (e.target === modal) closeConfirmModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeConfirmModal(); });
        document.querySelectorAll('form').forEach(form => {
            const attr = form.getAttribute('onsubmit');
            if (attr && attr.includes('return confirm')) {
                let message = 'Are you sure you want to proceed?'; const match = attr.match(/confirm\((['"])(.*?)\1\)/);
                if (match && match[2]) message = match[2].replace(/&#039;/g, "'").replace(/&quot;/g, '"');
                form.removeAttribute('onsubmit'); form.addEventListener('submit', function (e) { e.preventDefault(); openConfirmModal(message, this); });
            }
        });
    });

    // GLOBAL DOUBLE CLICK PREVENTER
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (e.defaultPrevented) return;
                if (form.getAttribute('data-is-submitting') === 'true') { e.preventDefault(); return false; }
                form.setAttribute('data-is-submitting', 'true');
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) { submitBtn.setAttribute('data-original-html', submitBtn.innerHTML); submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESSING...'; submitBtn.classList.add('opacity-75', 'pointer-events-none', 'cursor-not-allowed'); }
            });
        });
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                document.querySelectorAll('form').forEach(form => {
                    form.removeAttribute('data-is-submitting');
                    const btn = form.querySelector('button[type="submit"]');
                    if (btn) { btn.classList.remove('opacity-75', 'pointer-events-none', 'cursor-not-allowed'); const h = btn.getAttribute('data-original-html'); if (h) btn.innerHTML = h; }
                });
            }
        });
    });

    // WEBSOCKET REALTIME
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('system-updates').listen('DataUpdated', (e) => {
                const isTyping = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName);
                if (!isTyping) window.location.reload();
            });
        }
    });

    // TOAST NOTIFICATION
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container'); if (!container) return;
        const toast = document.createElement('div'); const isSuccess = type === 'success';
        toast.className = `pointer-events-auto flex items-start gap-3 p-4 w-80 md:w-96 rounded-xl border shadow-xl transform transition-all duration-300 translate-x-full opacity-0 bg-white ${isSuccess ? 'border-emerald-200' : 'border-red-200'}`;
        toast.innerHTML = `<div class="shrink-0 mt-0.5"><i class="fas ${isSuccess ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-red-500'} text-lg"></i></div><div class="flex-1 min-w-0"><h4 class="text-sm font-bold ${isSuccess ? 'text-emerald-700' : 'text-red-700'}">${isSuccess ? 'Success' : 'Error'}</h4><p class="text-xs font-medium text-ink-600 mt-0.5">${message}</p></div><button onclick="this.parentElement.classList.add('translate-x-full', 'opacity-0'); setTimeout(() => this.parentElement.remove(), 300);" class="shrink-0 text-ink-400 hover:text-ink-800 ml-2"><i class="fas fa-times text-sm"></i></button>`;
        container.appendChild(toast); setTimeout(() => { toast.classList.remove('translate-x-full', 'opacity-0'); }, 10);
        setTimeout(() => { toast.classList.add('translate-x-full', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 4000);
    };
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
        @if(session('error')) showToast("{{ session('error') }}", 'error'); @endif
    });

    window.markNotifRead = function(e, id) { e.preventDefault(); const url = e.currentTarget.href; fetch(`/activity-logs/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' } }).finally(() => { window.location.href = url; }); };
    window.markAllNotifRead = function(e) { e.preventDefault(); const url = e.currentTarget.href; fetch(`/activity-logs/mark-all-read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' } }).finally(() => { window.location.href = url; }); };
    
    // CHOICES JS GLOBAL INIT
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Choices !== 'undefined') {
            document.querySelectorAll('select:not([multiple]):not(.choices-initialized)').forEach(function(select) {
                new Choices(select, { searchEnabled: false, itemSelectText: '', shouldSort: false, classNames: { containerOuter: 'choices w-full relative' } });
                select.classList.add('choices-initialized');
            });
        }
    });

    // TURBO LOADER
    document.addEventListener('turbo:before-fetch-request', function() { const l = document.getElementById('top-loader'); if(l) { l.style.width = '40%'; l.style.opacity = '1'; } document.body.classList.remove('nav-open'); });
    document.addEventListener('turbo:load', function() { const l = document.getElementById('top-loader'); if(l) { l.style.width = '100%'; setTimeout(() => { l.style.opacity = '0'; setTimeout(() => l.style.width = '0', 300); }, 200); } });
</script>
<div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"></div>
</body>
</html>