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
                    fontFamily: { 
                        sans: ['Manrope', 'system-ui', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif']
                    },
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
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#D3CEC6; border-radius:6px; }
        :focus-visible { outline:2px solid var(--brand); outline-offset:2px; }
        summary { list-style:none; } summary::-webkit-details-marker { display:none; }

        /* Sidebar */
        #sidebar { background:#F9F7F4; border-right:1px solid var(--line); transition: transform 0.3s ease; }
        .nav-group { font-size:11px; font-weight:800; color:#A29B90; padding:0 14px; margin:20px 0 6px; text-transform:uppercase; letter-spacing:0.05em; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:10px 14px; margin-bottom:3px; border-radius:10px; font-size:14px; font-weight:600; color:#57514B; transition: all 0.2s ease; }
        .nav-link i { width:20px; text-align:center; font-size:15px; color:#78716A; transition: all 0.2s ease; }
        .nav-link:hover { background:#F1EDE7; color:var(--ink); transform: translateX(4px); }
        .nav-link:hover i { color: var(--brand); }
        .nav-link.active { background:var(--brand); color:#fff; font-weight:700; box-shadow: 0 4px 12px rgba(232,97,0,0.3); }
        .nav-link.active i { color:#fff; }

        /* Shared */
        .card { background:#FFFFFF; border:1px solid var(--line); border-radius:16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 4px 12px rgba(0,0,0,0.03); }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.6rem 1.1rem; border-radius:10px; font-size:14px; font-weight:700; transition:all .15s ease; cursor:pointer; }
        .btn:active { transform:scale(0.98); }
        .btn-primary { background:var(--brand); color:#fff; } .btn-primary:hover { background:var(--brand-dark); }
        .btn-secondary { background:#fff; color:#3F3A36; border:1px solid #E8E5E0; } .btn-secondary:hover { background:#FAF9F7; }
        .table-clean { width:100%; border-collapse:collapse; font-size:14px; }
        .table-clean th { text-align:left; font-size:11px; font-weight:700; color:var(--muted); padding:.85rem 1rem; background:#FAF9F7; border-bottom:1px solid var(--line); text-transform:uppercase; }
        .table-clean td { padding:.875rem 1rem; border-bottom:1px solid #F3F0EB; }
        .input { width:100%; padding:.6rem .8rem; border:1px solid #E8E5E0; border-radius:10px; font-size:14px; background:#fff; transition:all .15s ease; }
        .input:focus { outline:none; border-color:var(--brand); box-shadow:0 0 0 4px rgba(232,97,0,.15); }

        /* Mobile Fixes */
        @media (max-width: 1023px) {
            #sidebar { transform:translateX(-100%); }
            body.nav-open #sidebar { transform:translateX(0); }
            body.nav-open #navBackdrop { display:block; }
            html, body { overflow-x: hidden !important; max-width: 100vw; }
            .table-clean { min-width: 700px; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition:none !important; animation:none !important; } }
    </style>
    @stack('styles')
</head>
<body>
@php
    $userName = Auth::user()->name ?? 'Admin';
    $initial  = strtoupper(substr($userName, 0, 1));
    $nav = [
        ['label' => null, 'items' => [['Dashboard', 'dashboard', 'dashboard', 'fa-table-cells-large']]],
        ['label' => 'Financials', 'items' => [
            ['Purchases', 'purchases.index', 'purchases.*', 'fa-cart-shopping'],
            ['Expenses', 'expenses.index', 'expenses.*', 'fa-receipt'],
        ]],
        ['label' => 'Management', 'items' => [
            ['Customers', 'stores.index', 'stores.*', 'fa-user-group'],
            ['Products', 'products.index', 'products.*', 'fa-cube'],
            ['Distributions', 'deliveries.index', 'deliveries.*', 'fa-truck'],
            ['Warehouse', 'inventory.index', 'inventory.*', 'fa-warehouse'],
        ]],
        ['label' => 'Consignment', 'items' => [
            ['Sales reports', 'sales-reports.index', 'sales-reports.*', 'fa-file-lines'],
            ['Payments', 'payments.index', 'payments.*', 'fa-credit-card'],
        ]],
    ];

    // Low Stock Logic
    $lowStockCount = 0;
    $lowStockItems = collect();
    if (class_exists('App\Models\ProductVariant') && \Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
        $lowStockItems = \App\Models\ProductVariant::with('product')->whereNull('deleted_at')->where('stock', '<=', 5)->take(5)->get();
        $lowStockCount = \App\Models\ProductVariant::whereNull('deleted_at')->where('stock', '<=', 5)->count();
    }
@endphp

<div id="navBackdrop" class="hidden fixed inset-0 z-30 bg-ink-900/60 backdrop-blur-sm lg:hidden" onclick="document.body.classList.remove('nav-open')"></div>

<div class="flex h-screen overflow-hidden bg-[#FDFCFB]">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="w-[260px] h-full flex flex-col flex-shrink-0 z-40 lg:relative fixed inset-y-0 left-0">
        <div class="h-[90px] flex items-center gap-3 px-5 flex-shrink-0 border-b border-ink-200/50 group cursor-pointer hover:bg-white/50 transition-colors">
            <div class="w-[48px] h-[48px] rounded-full overflow-hidden bg-white shadow-sm border border-ink-200 shrink-0 transition-all duration-300 group-hover:scale-105 group-hover:rotate-3 group-hover:shadow-md">
                <img src="{{ asset('images/Kape_Logo.png') }}" alt="Kape Miarayon logo" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col justify-center min-w-0 transition-transform duration-300 group-hover:translate-x-1">
                <div class="flex items-baseline gap-1.5 leading-none">
                    <span class="font-serif italic font-bold text-[22px] text-brand-600 tracking-tight drop-shadow-sm">Kape</span>
                    <span class="font-black text-[17px] text-ink-900 tracking-tighter">Miarayon</span>
                </div>
                <span class="text-[9px] font-bold text-ink-500 uppercase tracking-[0.15em] mt-1.5">Wholesale & Retailer</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 pb-4 pt-2" aria-label="Main">
            @foreach($nav as $group)
                @if($group['label'])<div class="nav-group">{{ $group['label'] }}</div>@endif
                @foreach($group['items'] as [$text, $route, $pattern, $icon])
                    <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}" @if(request()->routeIs($pattern)) aria-current="page" @endif>
                        <i class="fas {{ $icon }}"></i><span>{{ $text }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="p-4 border-t border-ink-200/50 bg-[#F5F2EE] flex flex-col gap-3 shrink-0">
            <div class="flex items-center gap-3 px-1 group cursor-default">
                <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-extrabold text-sm border border-brand-200 shrink-0 transition-transform duration-300 group-hover:scale-110 shadow-sm">
                    {{ $initial }}
                </div>
                <div class="leading-tight min-w-0">
                    <div class="text-[13px] font-bold text-ink-900 truncate">{{ $userName }}</div>
                    <div class="text-[9px] font-bold text-brand-600 uppercase tracking-wider mt-0.5 truncate">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-[13px] font-bold text-red-600 bg-white border border-red-200 hover:bg-red-50 hover:border-red-300 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group">
                    <i class="fas fa-right-from-bracket transition-transform duration-300 group-hover:-translate-x-1"></i> Log out
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col h-full min-w-0 relative">

        {{-- HEADER --}}
        <header class="h-[72px] flex items-center justify-between gap-4 px-4 sm:px-8 shrink-0 z-20 bg-white/90 backdrop-blur-md border-b border-ink-200">
            
            <div class="flex items-center gap-3 min-w-0">
                {{-- Hamburger Button --}}
                <div class="w-10 h-10 shrink-0 lg:hidden relative overflow-hidden rounded-xl border border-ink-200 bg-white z-30">
                    <button type="button" id="mobileMenuBtn" class="absolute inset-0 w-full h-full flex items-center justify-center text-ink-600 transition-transform duration-300 ease-in-out transform translate-x-0" onclick="document.body.classList.add('nav-open')" aria-label="Open menu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                {{-- Back Button --}}
                @if(!request()->routeIs('dashboard'))
                    <button type="button" onclick="history.back()" class="w-10 h-10 shrink-0 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-500 hover:text-ink-900 transition-colors shadow-sm" title="Go back" aria-label="Go back">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </button>
                @endif

                {{-- Title --}}
                <div class="min-w-0 pl-1">
                    <h1 class="text-[22px] font-extrabold tracking-tight text-ink-900 leading-tight truncate">@yield('title', 'Dashboard')</h1>
                    <p class="text-[12px] font-medium text-ink-500 truncate mt-0.5">@yield('subtitle', 'Welcome back!')</p>
                </div>
            </div>

            {{-- RIGHT SIDE HEADER (Notification Bell) --}}
            <div class="flex items-center gap-3 shrink-0 relative z-[60]">
                <div class="relative" id="notifMenu">
                    {{-- GIGAMIT NATO ANG INLINE ONCLICK PARA SIGURADO NGA MO-GANA --}}
                    <button type="button" onclick="toggleNotif(event)" class="relative w-10 h-10 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-600 hover:text-ink-900 transition-all shadow-sm hover:scale-105 active:scale-95 cursor-pointer z-[100]">
                        <i class="fas fa-bell text-[13px] pointer-events-none"></i>
                        @if($lowStockCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white ring-2 ring-white animate-pulse px-1 pointer-events-none">{{ $lowStockCount }}</span>
                        @endif
                    </button>
                    
                    {{-- DROPDOWN --}}
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-[280px] max-w-[90vw] bg-white border border-ink-200 rounded-xl shadow-pop z-[100] overflow-hidden" style="transform-origin: top right;">
                        <div class="px-4 py-3 border-b border-ink-100 bg-ink-50 flex justify-between items-center">
                            <span class="text-[11px] font-bold text-ink-800 uppercase tracking-widest">Low Stock Alerts</span>
                            <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold">{{ $lowStockCount }} items</span>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse($lowStockItems as $item)
                                <a href="{{ route('inventory.index') }}" class="block px-4 py-3 border-b border-ink-50 hover:bg-brand-50 transition-colors group">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-[13px] font-bold text-ink-900 group-hover:text-brand-600 transition-colors">{{ $item->product->name ?? 'Unknown' }}</span>
                                        <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">{{ $item->stock }} left</span>
                                    </div>
                                    <div class="text-[11px] font-medium text-ink-500">{{ $item->name }}</div>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-sm font-medium text-ink-500">No low stock items.</div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2.5 bg-ink-50 text-center border-t border-ink-100 hover:bg-ink-100 transition-colors">
                            <a href="{{ route('inventory.index') }}" class="text-[11px] font-bold text-brand-600">View All Inventory <i class="fas fa-arrow-right ml-1 text-[9px]"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-4 sm:px-8 pb-10 pt-4">
            <div class="max-w-[1600px] mx-auto w-full">
                @if(session('success'))
                    <div role="status" class="mb-6 px-4 py-3 bg-emerald-50 text-emerald-800 rounded-xl flex items-center gap-3 text-sm font-bold border border-emerald-200 shadow-sm">
                        <i class="fas fa-circle-check text-emerald-500 text-lg"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div role="alert" class="mb-6 px-4 py-3 bg-red-50 text-red-800 rounded-xl flex items-center gap-3 text-sm font-bold border border-red-200 shadow-sm">
                        <i class="fas fa-circle-exclamation text-red-500 text-lg"></i> {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</div>

{{-- CONFIRMATION MODAL --}}
<div id="customConfirmModal" class="fixed inset-0 z-[100] hidden bg-ink-900/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
    <div class="bg-white rounded-2xl shadow-pop w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-300" id="confirmModalContent">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-4 text-lg animate-bounce"><i class="fas fa-triangle-exclamation"></i></div>
            <h3 id="confirmTitle" class="text-lg font-extrabold text-ink-900 mb-1">Are you sure?</h3>
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
    // ==========================================
    // 1. NOTIFICATION BELL TOGGLE (GLOBAL FUNCTION)
    // ==========================================
    window.toggleNotif = function(e) {
        if(e) e.stopPropagation();
        const dropdown = document.getElementById('notifDropdown');
        if(dropdown) {
            dropdown.classList.toggle('hidden');
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        // Close dropdown when clicking anywhere else
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notifDropdown');
            const btn = e.target.closest('button[onclick*="toggleNotif"]');
            if(dropdown && !dropdown.contains(e.target) && !btn) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            const dropdown = document.getElementById('notifDropdown');
            if(e.key === 'Escape' && dropdown) {
                dropdown.classList.add('hidden');
            }
        });

        // ==========================================
        // 2. HAMBURGER MENU ANIMATION
        // ==========================================
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mainContent = document.querySelector('main');
        let scrollTimeout;

        if (mainContent && mobileMenuBtn) {
            const showBtn = () => {
                mobileMenuBtn.classList.remove('-translate-x-[150%]');
                mobileMenuBtn.classList.add('translate-x-0');
            };
            const hideBtn = () => {
                mobileMenuBtn.classList.remove('translate-x-0');
                mobileMenuBtn.classList.add('-translate-x-[150%]');
            };

            // Ipakita sa umpisa pag-load sa page
            showBtn();

            mainContent.addEventListener('scroll', function() {
                showBtn();
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(hideBtn, 1500); 
            }, { passive: true });
        }

        // ==========================================
        // 3. CONFIRMATION MODAL LOGIC
        // ==========================================
        const modal = document.getElementById('customConfirmModal');
        const content = document.getElementById('confirmModalContent');
        const messageEl = document.getElementById('confirmMessage');
        const confirmBtn = document.getElementById('confirmBtn');
        let formToSubmit = null;

        window.openConfirmModal = function (message, form) {
            formToSubmit = form;
            messageEl.innerText = message;
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
            confirmBtn.focus();
        };
        
        window.closeConfirmModal = function () {
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); formToSubmit = null; }, 200);
        };

        confirmBtn.addEventListener('click', () => { if (formToSubmit) formToSubmit.submit(); });
        modal.addEventListener('click', e => { if (e.target === modal) closeConfirmModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeConfirmModal(); });

        document.querySelectorAll('form').forEach(form => {
            const attr = form.getAttribute('onsubmit');
            if (attr && attr.includes('return confirm')) {
                let message = 'Are you sure you want to proceed?';
                const match = attr.match(/confirm\((['"])(.*?)\1\)/);
                if (match && match[2]) message = match[2].replace(/&#039;/g, "'").replace(/&quot;/g, '"');
                form.removeAttribute('onsubmit');
                form.addEventListener('submit', function (e) { e.preventDefault(); openConfirmModal(message, this); });
            }
        });
    });
</script>

<!-- GLOBAL DOUBLE CLICK PREVENTER -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (e.defaultPrevented) return;
                if (form.getAttribute('data-is-submitting') === 'true') {
                    e.preventDefault();
                    return false;
                }
                form.setAttribute('data-is-submitting', 'true');
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.setAttribute('data-original-html', submitBtn.innerHTML);
                    submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESSING...';
                    submitBtn.classList.add('opacity-75', 'pointer-events-none', 'cursor-not-allowed');
                }
            });
        });
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                document.querySelectorAll('form').forEach(form => {
                    form.removeAttribute('data-is-submitting');
                    const btn = form.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.classList.remove('opacity-75', 'pointer-events-none', 'cursor-not-allowed');
                        const originalHtml = btn.getAttribute('data-original-html');
                        if (originalHtml) btn.innerHTML = originalHtml;
                    }
                });
            }
        });
    });
</script>
    
    <!-- GLOBAL REALTIME AUTO-UPDATE -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initial timestamp gikan sa server (gikuha gikan sa pinakabag-o nga data)
        let currentGlobalTime = '{{ \Illuminate\Support\Facades\DB::table('payments')->max('updated_at') ?? '0' }}'; 
        let isReloading = false;

        setInterval(() => {
            // Ayaw pag-check kung naay usa ka modal/form nga gina-type-an ang user
            const activeTag = document.activeElement.tagName;
            if (activeTag === 'INPUT' || activeTag === 'TEXTAREA' || activeTag === 'SELECT') {
                return; // Skip aning higayona para dili mawala ang gi-type
            }

            fetch('{{ route("system.updates") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.latest !== currentGlobalTime && data.latest !== '0') {
                        if (!isReloading) {
                            isReloading = true;
                            console.log('Bag-ong data na-detect! Nag-refresh...');
                            window.location.reload();
                        }
                    }
                })
                .catch(err => console.log('Global sync check failed:', err));
        }, 5000); // Mag-check every 5 seconds
    });
    </script>
</body>
</html>