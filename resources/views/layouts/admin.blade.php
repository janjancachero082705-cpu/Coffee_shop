<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') â€” Kape Miarayon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Manrope', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#EEF6F3', 100: '#D6EAE2', 200: '#AED5C6', 300: '#7DB9A5', 400: '#4A9980',
                            500: '#2A7B63', 600: '#1D5F4C', 700: '#184D3E', 800: '#143D32', 900: '#0F2C25',
                        },
                        ink: {
                            50: '#F6F8F7', 100: '#EDF0EE', 200: '#DEE3E0', 300: '#C4CCC8', 400: '#8E9B95',
                            500: '#66736D', 600: '#4B5752', 700: '#343E3A', 800: '#222A27', 900: '#151B19',
                        },
                        espresso: {
                            50: '#F6F8F7', 100: '#EDF0EE', 200: '#DEE3E0', 300: '#C4CCC8', 400: '#8E9B95',
                            500: '#66736D', 600: '#4B5752', 700: '#343E3A', 800: '#222A27', 900: '#151B19',
                        },
                        caramel: {
                            50: '#FFFBEB', 100: '#FEF3C7', 200: '#FDE68A', 300: '#FCD34D',
                            400: '#F59E0B', 500: '#D97706', 600: '#B45309',
                        }
                    },
                    boxShadow: { card: '0 1px 2px rgba(21,27,25,.05)', pop: '0 16px 40px -8px rgba(15,44,37,.35)' }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root { --brand:#1D5F4C; --deep:#0F2C25; --ink:#151B19; --muted:#66736D; --line:#E4E8E6; }
        html { scroll-padding-top: 6rem; }
        body { background:#F3F5F3; color:var(--ink); font-family:'Manrope',system-ui,sans-serif; -webkit-font-smoothing:antialiased; }
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#C4CCC8; border-radius:6px; }
        :focus-visible { outline:2px solid #4A9980; outline-offset:2px; }

        /* Sidebar (dark) */
        #sidebar { background:var(--deep); }
        #sidebar ::-webkit-scrollbar-thumb { background:rgba(255,255,255,.15); }
        .nav-group { font-size:12px; font-weight:600; color:#5E8A7C; padding:0 14px; margin:22px 0 6px; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:9px 14px; margin-bottom:2px; border-radius:12px; font-size:14px; font-weight:600; color:#9FC4B7; transition:background .15s,color .15s; }
        .nav-link i { width:18px; text-align:center; font-size:14px; color:#5E8A7C; transition:color .15s; }
        .nav-link:hover { background:rgba(255,255,255,.07); color:#fff; }
        .nav-link:hover i { color:#9FC4B7; }
        .nav-link.active { background:#fff; color:var(--deep); font-weight:800; }
        .nav-link.active i { color:var(--brand); }

        /* Shared components for pages */
        .card { background:#fff; border:1px solid var(--line); border-radius:16px; box-shadow:0 1px 2px rgba(21,27,25,.04); }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.6rem 1.1rem; border-radius:10px; font-size:14px; font-weight:700; transition:background .15s,border-color .15s; cursor:pointer; }
        .btn-primary { background:var(--brand); color:#fff; } .btn-primary:hover { background:#184D3E; }
        .btn-secondary { background:#fff; color:#343E3A; border:1px solid #DEE3E0; } .btn-secondary:hover { background:#F6F8F7; }
        .btn-danger { background:#B91C1C; color:#fff; } .btn-danger:hover { background:#991B1B; }
        .table-clean { width:100%; border-collapse:collapse; font-size:14px; }
        .table-clean th { text-align:left; font-size:13px; font-weight:600; color:var(--muted); padding:.75rem 1rem; background:#F6F8F7; border-bottom:1px solid var(--line); }
        .table-clean td { padding:.875rem 1rem; border-bottom:1px solid #EDF0EE; }
        .table-clean tbody tr:hover { background:#FAFBFA; }
        .input { width:100%; padding:.6rem .8rem; border:1px solid #DEE3E0; border-radius:10px; font-size:14px; background:#fff; }
        .input:focus { outline:none; border-color:var(--brand); box-shadow:0 0 0 3px rgba(29,95,76,.15); }

        @media (max-width:1023px) {
            #sidebar { transform:translateX(-100%); transition:transform .2s ease; }
            body.nav-open #sidebar { transform:translateX(0); }
            body.nav-open #navBackdrop { display:block; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition:none !important; } }
    </style>
    @stack('styles')
</head>
<body>
@php
    $userName = Auth::user()->name ?? 'Admin';
    $initial  = strtoupper(substr($userName, 0, 1));
    $nav = [
        ['label' => null, 'items' => [['Dashboard', 'dashboard', 'dashboard', 'fa-house']]],
        ['label' => 'Financials', 'items' => [
            ['Purchases', 'purchases.index', 'purchases.*', 'fa-cart-shopping'],
            ['Expenses', 'expenses.index', 'expenses.*', 'fa-receipt'],
        ]],
        ['label' => 'Management', 'items' => [
            ['Customers', 'stores.index', 'stores.*', 'fa-users'],
            ['Products', 'products.index', 'products.*', 'fa-box'],
            ['Distributions', 'deliveries.index', 'deliveries.*', 'fa-truck'],
            ['Warehouse', 'inventory.index', 'inventory.*', 'fa-warehouse'],
        ]],
        ['label' => 'Consignment', 'items' => [
            ['Sales reports', 'sales-reports.index', 'sales-reports.*', 'fa-file-invoice'],
            ['Payments', 'payments.index', 'payments.*', 'fa-credit-card'],
        ]],
    ];
@endphp

<div id="navBackdrop" class="hidden fixed inset-0 z-30 bg-ink-900/50 lg:hidden" onclick="document.body.classList.remove('nav-open')"></div>

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="w-[264px] fixed inset-y-0 left-0 z-40 flex flex-col">
        <div class="h-20 flex items-center gap-3 px-6 flex-shrink-0">
            <div class="w-10 h-10 rounded-xl overflow-hidden bg-white p-0.5 flex-shrink-0">
                <img src="{{ asset('images/Kape_Logo.png') }}" alt="Kape Miarayon logo" class="w-full h-full object-cover rounded-[10px]">
            </div>
            <div class="leading-tight">
                <div class="font-extrabold text-[16px] text-white tracking-tight">Kape Miarayon</div>
                <div class="text-xs font-medium" style="color:#7DB9A5">Admin panel</div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 pb-4" aria-label="Main">
            @foreach($nav as $group)
                @if($group['label'])<div class="nav-group">{{ $group['label'] }}</div>@endif
                @foreach($group['items'] as [$text, $route, $pattern, $icon])
                    <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}" @if(request()->routeIs($pattern)) aria-current="page" @endif>
                        <i class="fas {{ $icon }}"></i><span>{{ $text }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="p-3 flex-shrink-0">
            <div class="flex items-center justify-between gap-2 p-3 rounded-2xl" style="background:rgba(255,255,255,.06)">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-brand-400 text-brand-900 flex items-center justify-center font-extrabold text-sm flex-shrink-0">{{ $initial }}</div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-white truncate">{{ $userName }}</div>
                        <div class="text-xs" style="color:#7DB9A5">Administrator</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-300 hover:text-white hover:bg-white/10 transition-colors" title="Log out" aria-label="Log out">
                        <i class="fas fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 lg:ml-[264px] min-w-0 flex flex-col min-h-screen">

        <header class="h-20 flex items-center justify-between gap-4 px-4 sm:px-8 sticky top-0 z-20" style="background:rgba(243,245,243,.88); backdrop-filter:blur(10px)">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" class="lg:hidden w-10 h-10 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-600" onclick="document.body.classList.add('nav-open')" aria-label="Open menu"><i class="fas fa-bars"></i></button>
                @if(!request()->routeIs('dashboard'))
                    <button type="button" onclick="history.back()" class="w-10 h-10 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-500 hover:text-ink-900 transition-colors" title="Go back" aria-label="Go back"><i class="fas fa-arrow-left text-sm"></i></button>
                @endif
                <div class="min-w-0">
                    <h1 class="text-[22px] font-extrabold tracking-tight text-ink-900 leading-tight truncate">@yield('title', 'Dashboard')</h1>
                    <p class="text-sm text-ink-500 truncate">@yield('subtitle', 'Welcome back!')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 h-10 px-4 rounded-xl bg-white border border-ink-200 text-sm font-semibold text-ink-700">
                    <i class="fas fa-calendar-day text-brand-500 text-xs"></i>{{ now()->format('D, M j') }}
                </div>
                <button type="button" class="relative w-10 h-10 rounded-xl bg-white border border-ink-200 flex items-center justify-center text-ink-500 hover:text-ink-900 transition-colors" aria-label="Notifications">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>
            </div>
        </header>

        <main class="flex-1 px-4 sm:px-8 pb-10 pt-2">
            @if(session('success'))
                <div role="status" class="mb-6 px-4 py-3 bg-brand-50 text-brand-800 rounded-xl flex items-center gap-3 text-sm font-semibold border border-brand-100">
                    <i class="fas fa-circle-check text-brand-500"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div role="alert" class="mb-6 px-4 py-3 bg-red-50 text-red-800 rounded-xl flex items-center gap-3 text-sm font-semibold border border-red-200">
                    <i class="fas fa-circle-exclamation text-red-500"></i> {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

{{-- CONFIRMATION MODAL --}}
<div id="customConfirmModal" class="fixed inset-0 z-[100] hidden bg-brand-900/55 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
    <div class="bg-white rounded-2xl shadow-pop w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-200" id="confirmModalContent">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-4 text-lg"><i class="fas fa-triangle-exclamation"></i></div>
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

        // Swap native confirm() on forms for the custom modal
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
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (form.getAttribute('data-is-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    form.setAttribute('data-is-submitting', 'true');
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        // I-save ang orihinal nga text
                        submitBtn.setAttribute('data-original-html', submitBtn.innerHTML);
                        
                        // FIX: Ayaw i-set ang submitBtn.disabled = true.
                        // Gamita ang pointer-events-none para dili ma-click, pero mo-agi jud ang form.
                        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESSING...';
                        submitBtn.classList.add('opacity-75', 'pointer-events-none', 'cursor-not-allowed');
                    }
                });
            });

            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    document.querySelectorAll('form').forEach(form => {
                        form.removeAttribute('data-is-submitting');
                        const btn = form.querySelector('button[type="submit"]');
                        if(btn) {
                            btn.classList.remove('opacity-75', 'pointer-events-none', 'cursor-not-allowed');
                            const originalHtml = btn.getAttribute('data-original-html');
                            if(originalHtml) btn.innerHTML = originalHtml;
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>