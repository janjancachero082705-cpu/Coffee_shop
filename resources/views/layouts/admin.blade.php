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
                        brand: {
                            50: '#FFF7EE', 100: '#FFEBD5', 200: '#FFD6AB', 300: '#FFBC77', 400: '#FF9F45',
                            500: '#FF8512', 600: '#E86100', 700: '#BF4D00', 800: '#8F3B05', 900: '#5E2808',
                        },
                        ink: {
                            50: '#FAF9F7', 100: '#F3F1EE', 200: '#E8E5E0', 300: '#D3CEC6', 400: '#A29B90',
                            500: '#78716A', 600: '#57514B', 700: '#3F3A36', 800: '#2A2623', 900: '#1A1715',
                        },
                        espresso: {
                            50: '#FAF9F7', 100: '#F3F1EE', 200: '#E8E5E0', 300: '#D3CEC6', 400: '#A29B90',
                            500: '#78716A', 600: '#57514B', 700: '#3F3A36', 800: '#2A2623', 900: '#1A1715',
                        },
                        caramel: {
                            50: '#FFFBEB', 100: '#FEF3C7', 200: '#FDE68A', 300: '#FCD34D',
                            400: '#F59E0B', 500: '#D97706', 600: '#B45309',
                        }
                    },
                    boxShadow: { 
                        card: '0 1px 2px rgba(26,23,21,.04)', 
                        pop: '0 16px 40px -8px rgba(26,23,21,.25)',
                        hover: '0 10px 25px -5px rgba(26,23,21,.08)'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root { --brand:#E86100; --brand-dark:#BF4D00; --orange:#FF8512; --soft:#FFEBD5; --peach:#FDE9D5; --ink:#1A1715; --muted:#78716A; --line:#F0EDE8; --card:#F9F7F4; }
        html { scroll-padding-top: 6rem; }
        body { background:#FDFCFB; color:var(--ink); font-family:'Manrope',system-ui,sans-serif; -webkit-font-smoothing:antialiased; overflow:hidden; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#D3CEC6; border-radius:6px; transition:background 0.2s; }
        ::-webkit-scrollbar-thumb:hover { background:#A29B90; }
        
        :focus-visible { outline:2px solid var(--orange); outline-offset:2px; }
        summary { list-style:none; } summary::-webkit-details-marker { display:none; }

        /* 🌟 ANIMATIONS & KEYFRAMES */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseShadow {
            0% { box-shadow: 0 0 0 0 rgba(232, 97, 0, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(232, 97, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(232, 97, 0, 0); }
        }
        .animate-page-load { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Sidebar */
        #sidebar { background:#F9F7F4; border-right:1px solid var(--line); transition: transform 0.3s ease; }
        .nav-group { font-size:11px; font-weight:800; color:#A29B90; padding:0 14px; margin:20px 0 6px; text-transform:uppercase; letter-spacing:0.05em; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:10px 14px; margin-bottom:3px; border-radius:10px; font-size:14px; font-weight:600; color:#57514B; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); position:relative; overflow:hidden; }
        .nav-link i { width:20px; text-align:center; font-size:15px; color:#78716A; transition: all 0.25s ease; }
        .nav-link:hover { background:#F1EDE7; color:var(--ink); transform: translateX(4px); }
        .nav-link:hover i { color: var(--brand); transform: scale(1.1); }
        .nav-link.active { background:var(--brand); color:#fff; font-weight:700; animation: pulseShadow 2s infinite; }
        .nav-link.active i { color:#fff; }
        .nav-link.active:hover { transform: none; }

        /* Shared components */
        .card { background:#FBFAF8; border:1px solid var(--line); border-radius:18px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -10px rgba(26,23,21,.08); border-color: #E8E5E0; }
        
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.6rem 1.1rem; border-radius:10px; font-size:14px; font-weight:700; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); cursor:pointer; }
        .btn:active { transform: scale(0.96); }
        .btn-primary { background:var(--brand); color:#fff; box-shadow: 0 4px 6px -1px rgba(232,97,0,.2); } 
        .btn-primary:hover { background:var(--brand-dark); transform: translateY(-1px); box-shadow: 0 6px 12px -2px rgba(232,97,0,.3); }
        .btn-secondary { background:#fff; color:#3F3A36; border:1px solid #E8E5E0; } 
        .btn-secondary:hover { background:#FAF9F7; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0,0,0,.05); }
        .btn-danger { background:#B91C1C; color:#fff; } 
        .btn-danger:hover { background:#991B1B; transform: translateY(-1px); box-shadow: 0 6px 12px -2px rgba(185,28,28,.3); }

        .table-clean { width:100%; border-collapse:collapse; font-size:14px; }
        .table-clean th { text-align:left; font-size:11px; font-weight:800; color:var(--muted); padding:.85rem 1rem; background:#F5F2EE; border-bottom:1px solid var(--line); text-transform:uppercase; tracking-wider; }
        .table-clean td { padding:.875rem 1rem; border-bottom:1px solid #F3F0EB; transition: background 0.2s ease; }
        .table-clean tbody tr { transition: all 0.2s ease; }
        .table-clean tbody tr:hover { background:#FAF8F5; transform: scale(1.001); box-shadow: inset 4px 0 0 var(--brand); }

        .input { width:100%; padding:.6rem .8rem; border:1px solid #E8E5E0; border-radius:10px; font-size:14px; background:#fff; transition: all 0.2s ease; }
        .input:hover { border-color: #D3CEC6; }
        .input:focus { outline:none; border-color:var(--orange); box-shadow: 0 0 0 4px rgba(255,133,18,.15); background:#fff; }

        @media (max-width:1023px) {
            #sidebar { transform:translateX(-100%); }
            body.nav-open #sidebar { transform:translateX(0); }
            body.nav-open #navBackdrop { display:block; }
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

    // LOW STOCK NOTIFICATION LOGIC (REAL-TIME)
    $lowStockCount = 0;
    $lowStockItems = collect();
    if (class_exists('App\Models\ProductVariant') && \Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
        // Gidugangan og whereNull('deleted_at') para siguradong active lang ang i-check
        $lowStockItems = \App\Models\ProductVariant::with('product')->whereNull('deleted_at')->where('stock', '<=', 5)->take(5)->get();
        $lowStockCount = \App\Models\ProductVariant::whereNull('deleted_at')->where('stock', '<=', 5)->count();
    }
@endphp

<div id="navBackdrop" class="hidden fixed inset-0 z-30 bg-ink-900/40 lg:hidden" onclick="document.body.classList.remove('nav-open')"></div>

<div class="flex h-screen overflow-hidden bg-[#FDFCFB]">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="w-[260px] h-full flex flex-col flex-shrink-0 z-40 lg:relative fixed inset-y-0 left-0">
        
        {{-- CUSTOM LOGO & BRANDING AREA --}}
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

        {{-- Navigation Links --}}
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

        {{-- SIDEBAR FOOTER: Profile & Logout --}}
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

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col h-full min-w-0 relative">

        {{-- HEADER --}}
        <header class="h-[72px] flex items-center justify-between gap-4 px-4 sm:px-8 shrink-0 z-20" style="background:rgba(253,252,251,.9); backdrop-filter:blur(10px); border-bottom:1px solid var(--line);">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" class="lg:hidden w-10 h-10 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-600 transition-all hover:scale-105 active:scale-95" onclick="document.body.classList.add('nav-open')" aria-label="Open menu"><i class="fas fa-bars"></i></button>
                @if(!request()->routeIs('dashboard'))
                    <button type="button" onclick="history.back()" class="w-10 h-10 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-500 hover:text-ink-900 transition-all shadow-sm hover:scale-105 hover:shadow-md active:scale-95" title="Go back" aria-label="Go back"><i class="fas fa-arrow-left text-sm"></i></button>
                @endif
                <div class="min-w-0 pl-1">
                    <h1 class="text-[22px] font-extrabold tracking-tight text-ink-900 leading-tight truncate">@yield('title', 'Dashboard')</h1>
                    <p class="text-[12px] font-medium text-ink-500 truncate mt-0.5">@yield('subtitle', 'Welcome back!')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- NOTIFICATION BELL WITH LOW STOCK ALERTS --}}
                <div class="relative" id="notifMenu">
                    <button type="button" onclick="toggleNotif()" class="relative w-9 h-9 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-600 hover:text-ink-900 transition-all shadow-sm hover:scale-105 hover:shadow-md active:scale-95" aria-label="Notifications">
                        <i class="fas fa-bell text-[13px]"></i>
                        @if($lowStockCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white ring-2 ring-white animate-pulse px-1">{{ $lowStockCount }}</span>
                        @endif
                    </button>
                    
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-ink-200 rounded-xl shadow-pop z-50 overflow-hidden animate-page-load" style="animation-duration: 0.2s;">
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

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto px-4 sm:px-8 pb-10 pt-6">
            <div class="max-w-[1600px] mx-auto w-full animate-page-load">
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
    document.addEventListener('DOMContentLoaded', function () {
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

        // Notification Toggle Logic
        window.toggleNotif = function() {
            const dropdown = document.getElementById('notifDropdown');
            if (dropdown) dropdown.classList.toggle('hidden');
        };

        // Close notification menu when clicking outside
        document.addEventListener('click', e => {
            const notifMenu = document.getElementById('notifMenu');
            const notifDropdown = document.getElementById('notifDropdown');
            if (notifMenu && notifDropdown && !notifMenu.contains(e.target)) {
                notifDropdown.classList.add('hidden');
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
</body>
</html>