<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2D1A11">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') - Coffee Beans</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ═══════════════════════════════════════════════════════
           PREMIUM ROAST THEME — PRO LAYOUT
           Elegant Coffee Shop Aesthetic
           ═══════════════════════════════════════════════════════ */
        :root {
            --primary: #2D1A11;           /* Deep Espresso */
            --primary-light: #4E342E;     /* Medium Roast */
            --primary-soft: #64748b;      /* Latte */
            --accent: #D84315;            /* Burnt Orange / Copper */
            --accent-light: #f472b6;
            --bg-body: #FDFBF7;           /* Warm Cream */
            --bg-card: #FFFFFF;
            --text-main: #2D1A11;
            --text-muted: #64748b;
            --border-color: #EFEBE9;
            --border-light: #EFEBE9;
            
            --radius-xl: 24px;
            --radius-lg: 20px;
            --radius-md: 16px;
            --radius-sm: 12px;
            
            --shadow-sm: 0 2px 8px rgba(45, 26, 17, 0.04);
            --shadow-md: 0 8px 24px rgba(45, 26, 17, 0.08);
            --shadow-lg: 0 16px 40px rgba(45, 26, 17, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; border: none; background: none; color: inherit; }
        svg { display: block; }

        .portal-wrap {
            max-width: 640px;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            background: var(--bg-body);
            box-shadow: 0 0 60px rgba(45, 26, 17, 0.06);
        }

        /* ===== TOPBAR ===== */
        .p-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: linear-gradient(135deg, #2D1A11 0%, #4E342E 100%);
            padding: 20px 20px;
            display: flex;
            align-items: center;
            border-radius: 0 0 28px 28px;
            box-shadow: 0 10px 30px -10px rgba(45, 26, 17, 0.5);
        }

        .p-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 12px;
        }

        .p-topbar-store-text {
            display: flex;
            flex-direction: column;
            gap: 3px;
            flex: 1;
            min-width: 0;
        }

        .p-topbar-store-name {
            font-size: 17px;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.02em;
        }

        .p-topbar-store-code {
            font-size: 10.5px;
            color: var(--primary-soft);
            font-family: ui-monospace, monospace;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        /* ===== MAIN CONTENT ===== */
        .p-main {
            padding: 24px 20px 120px;
            animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== CARDS ===== */
        .p-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 22px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .p-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

        .p-card-head { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
        .p-card-icon { width: 46px; height: 46px; border-radius: var(--radius-md); background: #EFEBE9; display: grid; place-items: center; color: var(--primary); flex-shrink: 0; }
        .p-card-icon.green { background: #dcfce7; color: #16a34a; }
        .p-card-icon.blue  { background: #FDFBF7; color: #2563eb; }
        .p-card-icon.amber { background: #fef3c7; color: #d97706; }
        .p-card-icon.red   { background: #fee2e2; color: #dc2626; }

        .p-card-title { font-size: 15px; font-weight: 800; color: var(--text-main); }
        .p-card-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; font-weight: 500; }

        /* ===== BUTTONS ===== */
        .p-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 22px; border-radius: var(--radius-md); font-size: 14px; font-weight: 700; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid transparent; letter-spacing: -0.01em; }
        .p-btn-primary { background: linear-gradient(135deg, #D84315, #BF360C); color: #ffffff; box-shadow: 0 8px 20px -6px rgba(216, 67, 21, 0.5); }
        .p-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px -6px rgba(216, 67, 21, 0.7); }
        .p-btn-danger { background: #D84315; color: #ffffff; }
        .p-btn-ghost { background: #ffffff; color: var(--text-main); border-color: var(--border-color); }
        .p-btn-ghost:hover { background: #FDFBF7; border-color: var(--primary); }
        .p-btn-block { width: 100%; padding: 16px; }
        .p-btn-sm { padding: 10px 16px; font-size: 12px; }

        /* ===== BADGES ===== */
        .p-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 100px; font-size: 11px; font-weight: 800; text-transform: capitalize; letter-spacing: 0.02em; }
        .p-badge-pending  { background: #FEF3C7; color: #B45309; }
        .p-badge-approved, .p-badge-active, .p-badge-paid, .p-badge-completed { background: #DCFCE7; color: #15803D; }
        .p-badge-rejected, .p-badge-cancelled { background: #FEE2E2; color: #B91C1C; }
        .p-badge-partial  { background: #FDFBF7; color: #1D4ED8; }

        /* ===== FORMS ===== */
        .p-input, .p-select, .p-textarea { width: 100%; padding: 14px 16px; background: #ffffff; border: 1.5px solid var(--border-color); border-radius: var(--radius-md); color: var(--text-main); font-size: 14px; font-family: inherit; outline: none; transition: all 0.2s; font-weight: 500; }
        .p-input:focus, .p-select:focus, .p-textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(45, 26, 17, 0.08); }
        .p-label { display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.1em; }

        /* ===== ALERTS ===== */
        .p-alert { padding: 16px 20px; border-radius: var(--radius-md); font-size: 13px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .p-alert-success { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .p-alert-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }

        /* ===== FLOATING BUTTONS ===== */
        .p-float-btn { position: fixed; z-index: 9997; display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #2D1A11, #4E342E); color: #ffffff; box-shadow: 0 10px 25px -5px rgba(45, 26, 17, 0.6); transition: transform 0.2s ease; border: none; }
        .p-float-btn:hover { transform: scale(1.08); }
        .p-float-btn:active { transform: scale(0.95); }
        .p-float-hamburger { top: 20px; left: 20px; }
        .p-topbar { padding-left: 84px; }

        /* ===== SIDEBAR ===== */
        .p-sidebar-overlay { position: fixed; inset: 0; background: rgba(45, 26, 17, 0.6); backdrop-filter: blur(6px); z-index: 9998; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .p-sidebar-overlay.open { opacity: 1; visibility: visible; }
        .p-sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 85%; max-width: 320px; background: #ffffff; z-index: 9999; transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; box-shadow: 10px 0 40px rgba(45, 26, 17, 0.15); }
        .p-sidebar.open { transform: translateX(0); }
        .p-sidebar-head { padding: 26px 20px; background: linear-gradient(135deg, #2D1A11, #4E342E); color: #ffffff; display: flex; align-items: center; justify-content: space-between; border-radius: 0 0 28px 28px; }
        .p-sidebar-title { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
        .p-sidebar-sub { font-size: 11.5px; color: var(--primary-soft); font-weight: 600; margin-top: 2px; }
        .p-sidebar-close { color: #ffffff; padding: 8px; }
        .p-sidebar-nav { flex: 1; padding: 24px 20px; overflow-y: auto; }
        .p-sidebar-section-title { font-size: 10.5px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.14em; margin-bottom: 12px; margin-top: 24px; }
        .p-sidebar-section:first-child .p-sidebar-section-title { margin-top: 0; }
        .p-sidebar-link { display: flex; align-items: center; gap: 14px; padding: 14px 16px; border-radius: var(--radius-md); color: var(--text-main); font-weight: 600; font-size: 14px; transition: all 0.2s; margin-bottom: 4px; }
        .p-sidebar-link svg { color: var(--text-muted); transition: color 0.2s; }
        .p-sidebar-link:hover { background: #FDFBF7; color: var(--primary); }
        .p-sidebar-link:hover svg { color: var(--primary); }
        .p-sidebar-link.active { background: linear-gradient(135deg, #2D1A11, #4E342E); color: #ffffff; box-shadow: 0 6px 16px -6px rgba(45, 26, 17, 0.4); }
        .p-sidebar-link.active svg { color: #ffffff; }
        .p-sidebar-foot { padding: 20px; border-top: 1px solid var(--border-light); background: #FDFBF7; }
        .p-sidebar-user { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding: 14px; background: #ffffff; border-radius: var(--radius-md); border: 1px solid var(--border-color); }
        .p-sidebar-user-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #2D1A11, #4E342E); color: #fff; display: grid; place-items: center; font-weight: 800; flex-shrink: 0; }
        .p-sidebar-user-name { font-size: 14px; font-weight: 800; }
        .p-sidebar-user-email { font-size: 11px; color: var(--text-muted); font-weight: 500; }
        .p-sidebar-foot-link { display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: var(--radius-md); color: var(--text-main); font-weight: 700; font-size: 14px; width: 100%; text-align: left; transition: all 0.2s; }
        .p-sidebar-foot-link svg { color: var(--text-muted); }
        .p-sidebar-foot-link:hover { background: #EFEBE9; }
        .p-sidebar-foot-link.danger { color: #D84315; }
        .p-sidebar-foot-link.danger svg { color: #D84315; }
        .p-sidebar-foot-link.danger:hover { background: #FFEBEE; }

        /* ===== MODALS ===== */
        .p-modal-overlay { position: fixed; inset: 0; background: rgba(45, 26, 17, 0.7); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: flex-end; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .p-modal-overlay.open { opacity: 1; visibility: visible; }
        .p-modal { background: #ffffff; width: 100%; max-width: 640px; max-height: 90vh; border-radius: 28px 28px 0 0; padding: 28px 24px; overflow-y: auto; transform: translateY(100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .p-modal-overlay.open .p-modal { transform: translateY(0); }
        .p-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .p-modal-title { font-size: 20px; font-weight: 800; letter-spacing: -0.02em; }
        .p-modal-close { width: 42px; height: 42px; border-radius: 50%; background: #EFEBE9; display: grid; place-items: center; color: var(--text-main); }

        /* ===== NOTIFICATION BELL ===== */
        .pn-bell { position: fixed; bottom: 30px; right: 30px; width: 58px; height: 58px; border-radius: 50%; background: linear-gradient(135deg, #D84315, #BF360C); color: #ffffff; display: grid; place-items: center; box-shadow: 0 10px 28px -5px rgba(216, 67, 21, 0.6); z-index: 9998; cursor: pointer; transition: transform 0.2s; }
        .pn-bell:hover { transform: scale(1.1); }
        .pn-badge { position: absolute; top: -2px; right: -2px; background: #2D1A11; color: #fff; font-size: 11px; font-weight: 800; min-width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center; border: 3px solid var(--bg-body); }
        .pn-panel { position: fixed; bottom: 105px; right: 30px; width: calc(100vw - 60px); max-width: 380px; background: #ffffff; border-radius: var(--radius-xl); box-shadow: 0 20px 50px rgba(45, 26, 17, 0.2); border: 1px solid var(--border-color); z-index: 9999; overflow: hidden; display: none; flex-direction: column; }
        .pn-panel.open { display: flex; }
        .pn-head { padding: 18px 20px; background: linear-gradient(135deg, #2D1A11, #4E342E); color: #fff; display: flex; justify-content: space-between; align-items: center; }
        .pn-head-title { font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 8px; }
        .pn-mark-all { font-size: 11.5px; color: var(--primary-soft); font-weight: 700; }
        .pn-body { max-height: 400px; overflow-y: auto; padding: 8px; }
        .pn-item { display: flex; gap: 12px; padding: 14px; border-radius: var(--radius-md); transition: background 0.2s; cursor: pointer; text-decoration: none; color: inherit; }
        .pn-item:hover { background: #FDFBF7; }
        .pn-item.unread { background: #EFEBE9; }
        .pn-icon { width: 42px; height: 42px; border-radius: 50%; display: grid; place-items: center; font-size: 18px; flex-shrink: 0; }
        .pn-title { font-size: 13px; font-weight: 800; }
        .pn-message { font-size: 12px; color: var(--text-muted); margin-top: 2px; font-weight: 500; }
        .pn-time { font-size: 10px; color: var(--primary-soft); margin-top: 6px; font-weight: 700; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .p-topbar { padding: 18px 18px 18px 80px; border-radius: 0 0 24px 24px; }
            .p-topbar-store-name { font-size: 16px; }
            .p-main { padding: 20px 18px 120px; }
            .p-float-hamburger { top: 18px; left: 18px; width: 48px; height: 48px; }
            .pn-bell { bottom: 24px; right: 24px; width: 54px; height: 54px; }
            .pn-panel { bottom: 90px; right: 18px; left: 18px; width: auto; }
        }
    
        /* ═══ GLOBAL BACK BUTTON ═══ */
        .p-back-fab {
            position: fixed;
            top: 22px;
            right: 22px;
            z-index: 9997;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            color: #FFFFFF;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            -webkit-tap-highlight-color: transparent;
        }
        .p-back-fab:hover {
            background: rgba(255, 255, 255, 0.28);
            border-color: rgba(255, 255, 255, 0.5);
            transform: scale(1.08);
        }
        .p-back-fab:active { transform: scale(0.94); }
        .p-back-fab[hidden] { display: none !important; }
        .p-back-fab svg { display: block; }

        /* Topbar right padding — give space for back button */
        .p-topbar { padding-right: 84px !important; }

        @media (max-width: 480px) {
            .p-back-fab {
                top: 18px;
                right: 18px;
                width: 44px;
                height: 44px;
                border-radius: 13px;
            }
            .p-topbar { padding-right: 76px !important; }
        }</style>
    @stack('styles')
</head>
<body>
{{-- ═══ GLOBAL BACK BUTTON (appears on all pages except dashboard) ═══ --}}
<button type="button" class="p-back-fab" id="pBackFab" onclick="pGoBack()" aria-label="Go back" hidden>
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
</button>


<div class="portal-wrap">

    {{-- ===== TOPBAR ===== --}}
    <header class="p-topbar">
        <div class="p-topbar-inner">
            <div class="p-topbar-store-text">
                <div class="p-topbar-store-name">{{ Auth::guard('store')->user()->store_name ?? 'Store Portal' }}</div>
                <div class="p-topbar-store-code">{{ Auth::guard('store')->user()->code ?? '' }}</div>
            </div>
        </div>
    </header>

    {{-- ═══ FLOATING HAMBURGER ═══ --}}
    <button type="button" class="p-float-btn p-float-hamburger" id="pHamburger" aria-label="Open menu">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    {{-- ===== MAIN ===== --}}
    <main class="p-main">
        @if(session('success'))
            <div class="p-alert p-alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-alert p-alert-error">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer style="text-align: center; padding: 24px; font-size: 11.5px; color: var(--text-muted); font-weight: 600; letter-spacing: 0.05em;">
        &copy; {{ date('Y') }} COFFEE BEANS · STORE PORTAL
    </footer>
</div>

{{-- ═══ SIDEBAR DRAWER ═══ --}}
@auth('store')
<div class="p-sidebar-overlay" id="pSidebarOverlay" onclick="pToggleSidebar(false)"></div>
<aside class="p-sidebar" id="pSidebar">
    <div class="p-sidebar-head">
        <div>
            <div class="p-sidebar-title">Coffee Beans</div>
            <div class="p-sidebar-sub">Store Portal</div>
        </div>
        <button type="button" class="p-sidebar-close" onclick="pToggleSidebar(false)" aria-label="Close menu">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </button>
    </div>

    <nav class="p-sidebar-nav">
        <div class="p-sidebar-section">
            <div class="p-sidebar-section-title">Main</div>
            <a href="{{ route('portal.dashboard') }}" class="p-sidebar-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="p-sidebar-section">
            <div class="p-sidebar-section-title">Consignment</div>
            <a href="{{ route('portal.orders.index') }}" class="p-sidebar-link {{ request()->routeIs('portal.orders.*') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
                <span>Orders</span>
            </a>
            <a href="{{ route('portal.deliveries.index') }}" class="p-sidebar-link {{ request()->routeIs('portal.deliveries.*') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <span>Deliveries</span>
            </a>
        </div>
        <div class="p-sidebar-section">
            <div class="p-sidebar-section-title">Payment</div>
            <a href="{{ route('portal.payments.index') }}" class="p-sidebar-link {{ request()->routeIs('portal.payments.*') || request()->routeIs('portal.reports.*') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                <span>Payment</span>
            </a>
        </div>
    </nav>

    <div class="p-sidebar-foot">
        <div class="p-sidebar-user">
            <div class="p-sidebar-user-avatar">
                @if(Auth::guard('store')->user() && Auth::guard('store')->user()->logo_url)
                    <img src="{{ Auth::guard('store')->user()->logo_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                @else
                    {{ strtoupper(substr(Auth::guard('store')->user()->store_name ?? 'ST', 0, 2)) }}
                @endif
            </div>
            <div>
                <div class="p-sidebar-user-name">{{ Auth::guard('store')->user()->store_name ?? 'Store' }}</div>
                <div class="p-sidebar-user-email">{{ Auth::guard('store')->user()->email ?? '' }}</div>
            </div>
        </div>

        <a href="{{ route('portal.profile') }}" class="p-sidebar-foot-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a4 4 0 014-4h8a4 4 0 014 4v2"/></svg>
            <span>Profile</span>
        </a>

        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit" class="p-sidebar-foot-link danger">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
@endauth

{{-- ═══ MODAL: PROFILE ═══ --}}
<div class="p-modal-overlay" id="portalModal">
    <div class="p-modal">
        <div class="p-modal-header">
            <div class="p-modal-title">My Account</div>
            <button type="button" class="p-modal-close" onclick="closePortalModal()">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="portalModalBody">
            <div style="text-align:center;padding:40px;color:var(--text-muted);">Loading...</div>
        </div>
    </div>
</div>

{{-- ═══ NOTIFICATION BELL ═══ --}}
@auth('store')
<div id="pnBell" class="pn-bell">
    <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
    <span class="pn-badge" id="pnBadge" style="display:none;">0</span>
</div>

<div id="pnPanel" class="pn-panel">
    <div class="pn-head">
        <div class="pn-head-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Notifications
        </div>
        <button type="button" id="pnMarkAll" class="pn-mark-all">Mark all read</button>
    </div>
    <div class="pn-body" id="pnBody">
        <div style="text-align:center;padding:20px;color:var(--text-muted);">Loading...</div>
    </div>
</div>
@endauth

{{-- ═══════════════════════════════════════════════════════
     JAVASCRIPT
     ═══════════════════════════════════════════════════════ --}}
<script>
    // Sidebar Toggle
    window.pToggleSidebar = function(open) {
        var sidebar = document.getElementById('pSidebar');
        var overlay = document.getElementById('pSidebarOverlay');
        if (!sidebar || !overlay) return;
        if (open) {
            sidebar.classList.add('open');
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    };
    document.getElementById('pHamburger')?.addEventListener('click', function(e) {
        e.preventDefault();
        window.pToggleSidebar(true);
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.pToggleSidebar(false);
    });

    // Profile Modal Logic
    window.openPortalModal = function(url) {
        var modal = document.getElementById('portalModal');
        var body = document.getElementById('portalModalBody');
        if (!modal || !body) return;
        modal.classList.add('open');
        body.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);">Loading...</div>';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var content = doc.querySelector('.p-main');
                if (content) body.innerHTML = content.innerHTML;
            })
            .catch(() => body.innerHTML = '<div style="text-align:center;padding:40px;color:var(--accent);">Failed to load</div>');
    };
    window.closePortalModal = function() {
        document.getElementById('portalModal')?.classList.remove('open');
    };
    document.getElementById('portalModal')?.addEventListener('click', function(e) {
        if (e.target === this) window.closePortalModal();
    });

    // Notification Bell Logic
    (function() {
        const bell = document.getElementById('pnBell');
        const panel = document.getElementById('pnPanel');
        const body = document.getElementById('pnBody');
        const badge = document.getElementById('pnBadge');
        const markAll = document.getElementById('pnMarkAll');
        if (!bell || !panel) return;

        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
        let isOpen = false;

        function fetchNotifications() {
            fetch('{{ route("portal.notifications.unread") }}', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(data => {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'grid';
                } else {
                    badge.style.display = 'none';
                }
                if (!data.notifications.length) {
                    body.innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px;">No new notifications</div>';
                    return;
                }
                body.innerHTML = data.notifications.map(n => `
                    <a href="${n.url || '#'}" class="pn-item unread" onclick="markRead(${n.id}, event)">
                        <div class="pn-icon" style="background:${n.color}22;color:${n.color};">${n.icon}</div>
                        <div>
                            <div class="pn-title">${n.title}</div>
                            <div class="pn-message">${n.message}</div>
                            <div class="pn-time">${n.created_at}</div>
                        </div>
                    </a>
                `).join('');
            }).catch(() => {});
        }

        window.markRead = function(id, e) {
            e.preventDefault();
            fetch(`{{ route("portal.notifications.read", ":id") }}`.replace(':id', id), {
                method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).then(() => fetchNotifications());
        };

        bell.addEventListener('click', () => {
            isOpen = !isOpen;
            panel.classList.toggle('open', isOpen);
            if (isOpen) fetchNotifications();
        });

        markAll.addEventListener('click', () => {
            fetch('{{ route("portal.notifications.read-all") }}', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).then(() => fetchNotifications());
        });

        document.addEventListener('click', (e) => {
            if (isOpen && !panel.contains(e.target) && !bell.contains(e.target)) {
                panel.classList.remove('open');
                isOpen = false;
            }
        });

        setInterval(fetchNotifications, 15000);
        setTimeout(fetchNotifications, 1500);
    })();
</script>

@stack('scripts')

<script>
(function() {
    var backFab = document.getElementById('pBackFab');
    if (!backFab) return;

    /* ═══════════════════════════════════════════════════════
       BACK BUTTON — SHOW ONLY ON SUB-PAGES
       Rule: URL has 3+ segments after domain
       ───────────────────────────────────────────────────────
       HIDE:
         /portal                     → home
         /portal/dashboard           → dashboard
         /portal/orders              → orders list
         /portal/deliveries          → deliveries list
         /portal/payments            → payments list
         /portal/inventory           → inventory list
         /portal/profile             → profile page
       SHOW:
         /portal/orders/create       → create order
         /portal/orders/5            → view specific order
         /portal/orders/5/edit       → edit order
         /portal/deliveries/5        → view specific delivery
         /portal/payments/create     → create payment
         /portal/profile/edit        → edit profile
       ═══════════════════════════════════════════════════════ */
    var path = window.location.pathname.replace(/\/+$/, '');
    var segments = path.split('/').filter(Boolean);

    if (segments.length >= 3) {
        backFab.removeAttribute('hidden');
    }

    window.pGoBack = function() {
        if (window.history.length > 2) {
            window.history.back();
        } else {
            window.location.href = '{{ route("portal.dashboard") }}';
        }
    };
})();
</script>
</body>
</html>