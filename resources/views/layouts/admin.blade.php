<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') â€” Kape Miarayon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        espresso: {
                            50: '#F8FAFC', 100: '#F1F5F9', 200: '#E2E8F0', 300: '#CBD5E1',
                            400: '#94A3B8', 500: '#64748B', 600: '#475569', 700: '#334155',
                            800: '#1E293B', 900: '#0F172A',
                        },
                        caramel: { 
                            50: '#FFFBEB', 100: '#FEF3C7', 200: '#FDE68A', 300: '#FCD34D',
                            400: '#F59E0B', 500: '#D97706', 600: '#B45309',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        body { background-color: #F8FAFC; font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        
        .nav-link { transition: all 0.2s; color: #94A3B8; font-weight: 500; border-left: 3px solid transparent; }
        .nav-link:hover { color: #FFFFFF; background: #1E293B; border-left-color: #F59E0B; padding-left: 1.5rem; }
        .nav-link.active { color: #FFFFFF; background: #1E293B; border-left-color: #F59E0B; font-weight: 600; }
        .nav-link.active i { color: #F59E0B; }
        
        .category-title { font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; padding-left: 1.5rem; margin-top: 1.5rem; }
        
        /* Modern card style */
        .card { background: #ffffff; border-radius: 12px; border: 1px solid #E2E8F0; transition: all 0.2s ease-in-out; }
        .card:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
    </style>
    @stack('styles')
</head>
<body class="text-espresso-800">
<div class="flex min-h-screen">
    
    {{-- DARK SAAS SIDEBAR --}}
    <aside class="w-[260px] bg-espresso-900 flex flex-col fixed h-screen z-40 border-r border-espresso-800 shadow-2xl">
        <div class="h-20 flex items-center px-6 border-b border-espresso-800/50 flex-shrink-0">
            <div class="flex items-center gap-3 w-full">
                <div class="w-10 h-10 rounded-lg overflow-hidden bg-white p-0.5 flex-shrink-0 shadow-sm">
                    <img src="{{ asset('images/Kape_Logo.png') }}" alt="Logo" class="w-full h-full object-cover rounded-md">
                </div>
                <div class="flex flex-col justify-center">
                    <span class="font-bold text-white text-base leading-none tracking-tight">Kape Miarayon</span>
                    <span class="text-[9px] text-caramel-500 font-bold tracking-widest uppercase mt-1">Admin Panel</span>
                </div>
            </div>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4 px-0">
            <a href="{{ route('dashboard') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-home w-4 text-center text-sm"></i><span class="text-sm">Dashboard</span></a>
            
            <div class="category-title">Management</div>
            <a href="{{ route('stores.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('stores.*') ? 'active' : '' }}"><i class="fas fa-store w-4 text-center text-sm"></i><span class="text-sm">Stores</span></a>
            <a href="{{ route('products.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('products.*') ? 'active' : '' }}"><i class="fas fa-box w-4 text-center text-sm"></i><span class="text-sm">Products</span></a>
            <a href="{{ route('deliveries.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('deliveries.*') ? 'active' : '' }}"><i class="fas fa-truck w-4 text-center text-sm"></i><span class="text-sm">Distributions</span></a>
            <a href="{{ route('inventory.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('inventory.*') ? 'active' : '' }}"><i class="fas fa-warehouse w-4 text-center text-sm"></i><span class="text-sm">Warehouse</span></a>
            
            <div class="category-title">Consignment</div>
            <a href="{{ route('sales-reports.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('sales-reports.*') ? 'active' : '' }}"><i class="fas fa-file-invoice w-4 text-center text-sm"></i><span class="text-sm">Sales Reports</span></a>
            <a href="{{ route('payments.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('payments.*') ? 'active' : '' }}"><i class="fas fa-credit-card w-4 text-center text-sm"></i><span class="text-sm">Payments</span></a>
            
            <div class="category-title">Business</div>
            <a href="{{ route('purchases.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('purchases.*') ? 'active' : '' }}"><i class="fas fa-shopping-cart w-4 text-center text-sm"></i><span class="text-sm">Purchases</span></a>
            <a href="{{ route('expenses.index') }}" class="nav-link flex items-center gap-3 px-6 py-2.5 mb-0.5 {{ request()->routeIs('expenses.*') ? 'active' : '' }}"><i class="fas fa-receipt w-4 text-center text-sm"></i><span class="text-sm">Expenses</span></a>
        </nav>

        <div class="p-4 border-t border-espresso-800/50 bg-espresso-900 flex-shrink-0">
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-espresso-800 transition-colors group cursor-pointer border border-transparent hover:border-espresso-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-espresso-700 text-white flex items-center justify-center font-bold text-xs border border-espresso-600 shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white truncate w-28">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-[10px] text-espresso-400 font-medium">Administrator</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-md flex items-center justify-center text-espresso-400 hover:text-red-400 hover:bg-red-500/10 transition-colors" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-[260px] flex flex-col min-h-screen">
        
        {{-- TOPBAR --}}
        <header class="h-20 bg-white/80 backdrop-blur-md flex items-center justify-between px-8 border-b border-espresso-200 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                @if(!request()->routeIs('dashboard'))
                    <button onclick="history.back()" class="w-9 h-9 rounded-lg border border-espresso-200 flex items-center justify-center text-espresso-500 hover:text-espresso-900 hover:bg-slate-50 transition-colors shadow-sm" title="Go Back">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </button>
                @endif

                <div>
                    <h2 class="text-xl font-bold text-espresso-900 leading-tight tracking-tight">@yield('title', 'Dashboard')</h2>
                    <p class="text-xs text-espresso-500 font-medium mt-0.5">@yield('subtitle', 'Welcome back!')</p>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <button class="w-9 h-9 rounded-lg border border-espresso-200 flex items-center justify-center text-espresso-500 hover:text-espresso-900 hover:bg-slate-50 transition-colors relative shadow-sm">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                <div class="h-6 w-px bg-espresso-200"></div>
                <div class="flex items-center gap-2 cursor-pointer">
                    <div class="text-sm font-semibold text-espresso-900">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="w-8 h-8 rounded-lg bg-espresso-100 text-espresso-700 flex items-center justify-center font-bold text-xs border border-espresso-200 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-8">
            @if(session('success'))
                <div class="mb-6 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-xl flex items-center gap-3 text-sm font-medium border border-emerald-200 shadow-sm animate-fade-in-down">
                    <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-xl flex items-center gap-3 text-sm font-medium border border-red-200 shadow-sm animate-fade-in-down">
                    <i class="fas fa-exclamation-circle text-lg"></i> {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>