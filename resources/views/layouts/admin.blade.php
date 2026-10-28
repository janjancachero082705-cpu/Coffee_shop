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
    @vite(['resources/js/app.js'])
    
    
    <link rel="preload" as="image" href="{{ asset('images/Kape_Logo.png') }}">
    
    <!-- INSTANT PAGE PREFETCHING -->
    <script src="https://instant.page/5.2.0" type="module"></script>
    <style>
        /* Top Loading Bar */
        #top-loader {
            position: fixed; top: 0; left: 0; height: 3px; background: #E86100;
            width: 0%; z-index: 99999; transition: width 0.3s ease, opacity 0.3s ease;
            box-shadow: 0 0 10px #E86100; opacity: 0;
        }
        /* Mobile Viewport Fix (Para dili mag-ungot ang scroll) */
        @media (max-width: 1023px) {
            html, body { height: 100dvh !important; overflow: hidden; }
        }
    </style>
    
    
    <style>
        /* ==========================================
           COMFORTABLE EYE-FRIENDLY THEME (SOFT CONTRAST)
           ========================================== */
        
        /* 1. Main Background - Warm Soft Gray (Dili Sakit sa Mata) */
        body, 
        .bg-\[\#FDFCFB\], 
        .flex.h-screen {
            background-color: #F3F1EE !important; /* Warm Beige/Gray */
        }

        /* 2. Sidebar - Slightly Darker than Main */
        #sidebar {
            background: #EBE8E3 !important; /* Soft warm gray for separation */
            border-right: 1px solid #DFDBD5 !important;
        }

        /* 3. Header - Subtle Warm White */
        header {
            background: #F9F8F6 !important;
            border-bottom: 1px solid #E2DED8 !important;
            box-shadow: 0 4px 15px -5px rgba(0,0,0,0.03) !important;
        }

        /* 4. Cards - Keep them White to pop against the beige background */
        .card {
            background: #FFFFFF !important;
            border: 1px solid #E8E5E0 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
        }

        /* 5. Navigation Links - Softer Colors */
        .nav-link {
            color: #57514B !important;
        }
        .nav-link i {
            color: #78716A !important;
        }
        .nav-link:hover {
            background: #DFDBD5 !important;
            color: #1A1715 !important;
        }
        .nav-link.active {
            background: #E86100 !important; /* Solid Brand Orange */
            color: #FFFFFF !important;
            box-shadow: 0 4px 12px rgba(232, 97, 0, 0.25) !important;
        }
        .nav-link.active i {
            color: #FFFFFF !important;
        }

        /* 6. Inputs - Softer background */
        .input {
            background-color: #F9F8F6 !important;
            border-color: #DFDBD5 !important;
        }
        .input:focus {
            background-color: #FFFFFF !important;
            border-color: #E86100 !important;
        }

        /* 7. Table Headers */
        .table-clean th {
            background-color: #F9F8F6 !important;
            color: #78716A !important;
        }

        /* 8. Sidebar Footer */
        #sidebar .bg-\[\#F5F2EE\] {
            background: #EBE8E3 !important;
            border-top: 1px solid #DFDBD5 !important;
        }
    </style>
    <meta name="color-scheme" content="light">
    
    <!-- CHOICES.JS FOR CUSTOM DROPDOWNS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <style>
        /* Override Choices.js styling to match our theme */
        .choices { margin-bottom: 0 !important; width: 100% !important; }
        .choices__inner {
            background-color: #FFFFFF !important;
            border: 1px solid #D3CEC6 !important;
            border-radius: 10px !important;
            padding: 8px 10px !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            min-height: 44px !important;
            display: flex !important;
            align-items: center !important;
            color: #1A1715 !important;
        }
        .choices[data-type*="select-one"] .choices__inner {
            padding-bottom: 8px !important;
        }
        .choices__list--dropdown {
            background-color: #FFFFFF !important;
            border: 1px solid #E8E5E0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
            z-index: 100 !important;
            margin-top: 4px !important;
        }
        .choices__list--dropdown .choices__item {
            padding: 12px 15px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            border-bottom: 1px solid #F3F1EE !important;
            color: #57514B !important;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #FFF7EE !important;
            color: #E86100 !important;
        }
        .choices[data-type*="select-one"]::after {
            border-color: #A29B90 transparent transparent transparent !important;
            right: 15px !important;
        }
    </style>
</head>
<body>
<div id="top-loader"></div>
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
            <div class="w-[48px] h-[48px] rounded-full overflow-hidden bg-white shadow-sm border border-ink-200 shrink-0 group-hover:scale-105 group-hover:rotate-3 group-hover:shadow-md will-change-transform">
                <img src="{{ asset('images/Kape_Logo.png') }}" alt="Kape Miarayon logo" class="w-full h-full object-cover" fetchpriority="high">
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
        
            <div class="nav-group mt-4">SYSTEM</div>
            <a href="{{ route('activity.logs') }}" class="nav-link {{ request()->routeIs('activity.logs') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Transactions</span>
            </a>
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
                    <button type="button" id="mobileMenuBtn" class="absolute inset-0 w-full h-full flex items-center justify-center text-ink-600 transition-transform duration-300 ease-in-out transform translate-x-0" onclick="event.preventDefault(); document.body.classList.add('nav-open')" aria-label="Open menu">
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
                @php
    $recentNotifs = \App\Models\ActivityLog::whereNull('read_at')->latest()->take(6)->get();
    $notifCount = \App\Models\ActivityLog::whereNull('read_at')->count();
    $lowStockCount = $lowStockCount ?? 0;
@endphp

<div class="relative" id="notifMenu">
                    {{-- GIGAMIT NATO ANG INLINE ONCLICK PARA SIGURADO NGA MO-GANA --}}
                    <button type="button" onclick="toggleNotif(event)" class="relative w-10 h-10 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-600 hover:text-ink-900 transition-all shadow-sm hover:scale-105 active:scale-95 cursor-pointer z-[100]">
                        <i class="fas fa-bell text-[13px] pointer-events-none"></i>
                        @if($notifCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white ring-2 ring-white animate-pulse px-1 pointer-events-none">{{ $notifCount > 99 ? "99+" : $notifCount }}</span>
                        @endif
                    </button>
                    
                    {{-- DROPDOWN --}}
                                <!-- DYNAMIC NOTIFICATIONS DROPDOWN -->
            

            <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 md:w-96 bg-white border border-ink-200 rounded-2xl shadow-2xl z-50 overflow-hidden transform origin-top-right transition-all duration-200">
                <div class="px-4 py-3 border-b border-ink-100 flex justify-between items-center bg-ink-50/50">
                    <h3 class="text-sm font-bold text-ink-900">Notifications</h3>
                    <span class="text-[10px] font-bold bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full">{{ $notifCount }} New</span>
                </div>
                
                <div class="max-h-[300px] overflow-y-auto">
                    @forelse($recentNotifs as $notif)
                        @php
                            // I-map ang module ngadto sa saktong route
                            $link = match($notif->module) {
                                'Purchases' => route('purchases.index'),
                                'Expenses' => route('expenses.index'),
                                'Payments' => route('payments.index'),
                                'Deliveries' => route('deliveries.index'),
                                'Inventory' => route('inventory.index'),
                                default => route('activity.logs')
                            };
                            
                            // Ibutang ang kolor sa icon base sa module
                            $iconColor = match($notif->module) {
                                'Purchases' => 'bg-amber-100 text-amber-600',
                                'Expenses' => 'bg-red-100 text-red-600',
                                'Payments' => 'bg-emerald-100 text-emerald-600',
                                'Deliveries' => 'bg-blue-100 text-blue-600',
                                'Inventory' => 'bg-purple-100 text-purple-600',
                                default => 'bg-gray-100 text-gray-600'
                            };
                            
                            $iconClass = match($notif->module) {
                                'Purchases' => 'fa-shopping-cart',
                                'Expenses' => 'fa-receipt',
                                'Payments' => 'fa-money-bill-wave',
                                'Deliveries' => 'fa-truck',
                                'Inventory' => 'fa-boxes',
                                default => 'fa-bell'
                            };
                        @endphp

                        <a href="{{ $link }}" onclick="markNotifRead(event, {{ $notif->id }})" class="block w-full text-left px-4 py-3 hover:bg-ink-50 transition-colors border-b border-ink-50 last:border-0 group">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $iconColor }} mt-0.5 shadow-sm">
                                    <i class="fas {{ $iconClass }} text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[13px] font-bold text-ink-900 group-hover:text-brand-600 transition-colors">{{ $notif->module }} Update</p>
                                    <p class="text-xs text-ink-500 line-clamp-2 mt-0.5 leading-relaxed">{{ $notif->description }}</p>
                                    <p class="text-[10px] font-semibold text-ink-400 mt-1"><i class="far fa-clock mr-1"></i>{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <div class="w-12 h-12 bg-ink-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-bell-slash text-ink-300 text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold text-ink-600">No notifications yet.</p>
                        </div>
                    @endforelse
                </div>
                
                <a href="{{ route('activity.logs') }}" onclick="markAllNotifRead(event)" class="block w-full py-3 text-center text-xs font-bold text-brand-600 hover:text-brand-700 bg-white hover:bg-brand-50 border-t border-ink-100 transition-colors">
                    View All Transactions
                </a>
            </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-4 sm:px-8 pb-24 md:pb-10 pt-4" style="padding-bottom: calc(6rem + env(safe-area-inset-bottom, 0px)) !important;">
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
    
    <!-- WEBSOCKET REALTIME LISTENER -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('system-updates')
                .listen('DataUpdated', (e) => {
                    console.log('WebSocket: Bag-ong data na-detect!');
                    
                    // SIGURADUHON NGA WALAY GINA-TYPE ANG USER BAGO MAG-UPDATE
                    const activeTag = document.activeElement.tagName;
                    const isTyping = (activeTag === 'INPUT' || activeTag === 'TEXTAREA' || activeTag === 'SELECT');
                    
                    if (!isTyping) {
                        // Pwede i-reload, o kaha i-fetch ang bag-ong data via AJAX
                        window.location.reload();
                    } else {
                        console.log('WebSocket: Gi-skip ang update kay naay nag-type.');
                    }
                });
        } else {
            console.warn('Laravel Echo wala na-load. I-check ang npm run dev.');
        }
    });
    </script>
    
    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <script>
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            
            // Colors based on type
            const isSuccess = type === 'success';
            const bgClass = isSuccess ? 'bg-white border-emerald-200' : 'bg-white border-red-200';
            const iconClass = isSuccess ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-red-500';
            const titleText = isSuccess ? 'Success' : 'Error';
            const titleColor = isSuccess ? 'text-emerald-700' : 'text-red-700';

            toast.className = `pointer-events-auto flex items-start gap-3 p-4 w-80 md:w-96 rounded-xl border shadow-xl transform transition-all duration-300 translate-x-full opacity-0 ${bgClass}`;
            
            toast.innerHTML = `
                <div class="shrink-0 mt-0.5"><i class="fas ${iconClass} text-lg"></i></div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold ${titleColor}">${titleText}</h4>
                    <p class="text-xs font-medium text-ink-600 mt-0.5 leading-relaxed">${message}</p>
                </div>
                <button onclick="this.parentElement.classList.add('translate-x-full', 'opacity-0'); setTimeout(() => this.parentElement.remove(), 300);" class="shrink-0 text-ink-400 hover:text-ink-800 transition-colors ml-2">
                    <i class="fas fa-times text-sm"></i>
                </button>
            `;

            container.appendChild(toast);

            // Trigger Slide-in animation
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 10);

            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        // I-convert ang Laravel Flash Messages ngadto sa Toast inig-load
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
        });
    </script>
    <script>
        window.markNotifRead = function(e, id) {
            e.preventDefault();
            const url = e.currentTarget.href;
            e.currentTarget.style.pointerEvents = 'none';
            e.currentTarget.style.opacity = '0.5';

            fetch(`/activity-logs/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
            }).then(() => { window.location.href = url; })
              .catch(() => { window.location.href = url; });
        };

        window.markAllNotifRead = function(e) {
            e.preventDefault();
            const url = e.currentTarget.href;
            fetch(`/activity-logs/mark-all-read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
            }).then(() => { window.location.href = url; })
              .catch(() => { window.location.href = url; });
        };
    </script>
    <script>
        // Siguraduhon nga mo-close ang mobile sidebar inig-click sa link
        document.addEventListener('turbo:click', function(event) {
            // Kung ang gi-click kay naa sa sulod sa sidebar, i-remove ang 'nav-open'
            if (event.target.closest('#sidebar')) {
                document.body.classList.remove('nav-open');
            }
        });

        // Backup: I-close pud ang sidebar inig-sugod og load sa bag-ong page
        document.addEventListener('turbo:before-visit', function() {
            document.body.classList.remove('nav-open');
        });
        
        // I-close kung mo-click sa backdrop
        document.addEventListener('turbo:load', function() {
            const backdrop = document.getElementById('navBackdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    document.body.classList.remove('nav-open');
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loader = document.getElementById('top-loader');
            const links = document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href^="javascript"])');
            
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    // 1. I-start ang loading bar
                    if(loader) {
                        loader.style.width = '40%';
                        loader.style.opacity = '1';
                    }
                    
                    // 2. I-close dayon ang sidebar sa mobile para wala nay mag-ungot
                    document.body.classList.remove('nav-open');
                });
            });
            
            // Kung mahuman na og load ang page, pun-a ang loading bar
            window.addEventListener('load', function() {
                if(loader) {
                    loader.style.width = '100%';
                    setTimeout(() => {
                        loader.style.opacity = '0';
                        setTimeout(() => loader.style.width = '0', 300);
                    }, 200);
                }
            });
        });
    </script>
    
    <script>
        // GLOBAL INLINE NUMBER LIMITER (Parehas sa Inventory)
        document.addEventListener('input', function(e) {
            if (e.target && e.target.type === 'number') {
                let maxVal = parseFloat(e.target.getAttribute('max')) || 999999;
                let val = parseFloat(e.target.value);

                // Pangitaon kung asa dapit ibutang ang warning
                let parent = e.target.parentElement;
                
                // Kung ang input kay naa sa sulod sa usa ka relative/flex wrapper, didto ta sa gawas magbutang
                let insertTarget = parent;
                if (parent.classList.contains('relative') || parent.classList.contains('flex') || parent.classList.contains('sk-stepper')) {
                    insertTarget = parent.parentElement;
                }

                // Pangitaon kung naa nay existing nga warning para dili mag-doble
                let warningEl = insertTarget.querySelector('.global-number-warning');
                
                if (!warningEl) {
                    // Kung wala pa, himoan nato og bag-o
                    warningEl = document.createElement('p');
                    warningEl.className = 'global-number-warning text-[11px] font-bold text-red-500 mt-1.5 hidden animate-pulse';
                    warningEl.innerHTML = `<i class="fas fa-circle-exclamation mr-1"></i> Limit reached! Maximum is ${maxVal.toLocaleString()}.`;
                    
                    // Ibutang pagkahuman sa input wrapper
                    insertTarget.appendChild(warningEl);
                }

                // I-check kung sobra ba
                if (val > maxVal) {
                    e.target.value = maxVal; // I-cap ang value
                    warningEl.classList.remove('hidden'); // Ipakita ang warning
                } else {
                    warningEl.classList.add('hidden'); // Tagoa ang warning kung ubos pa sa limit
                }
            }
        });
    </script>
</body>
</html>