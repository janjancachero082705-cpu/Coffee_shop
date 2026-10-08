<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Coffee Consignment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f0f14;
            --sidebar-hover: #1a1a22;
            --sidebar-active: #1f1a17;
            --sidebar-border: #1e1e26;
            --bg-dark: #1a1a22;
            --bg-darker: #14141a;
            --bg-card: #22222c;
            --accent: #a9784a;
            --accent-light: #c9a961;
            --accent-dark: #8a5f36;
            --accent-bg: rgba(169, 120, 74, 0.12);
            --accent-border: rgba(169, 120, 74, 0.25);
            --text-primary: #f5f3f0;
            --text-secondary: #a8a5a0;
            --text-muted: #6b6862;
            --border: #26262e;
            --border-strong: #32323c;
            --success: #22c55e;
            --success-bg: rgba(34, 197, 94, 0.12);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.12);
            --info: #3b82f6;
            --info-bg: rgba(59, 130, 246, 0.12);
            --radius: 10px;
            --radius-sm: 8px;
            --radius-lg: 14px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-primary);
            font-size: 13px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            background: var(--sidebar-bg);
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; border: none; background: none; color: inherit; }
        svg { display: block; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(169, 120, 74, 0.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(169, 120, 74, 0.4); }

        .app { height: 100vh; display: flex; width: 100%; position: relative; overflow: hidden; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 240px; min-width: 240px; max-width: 240px;
            height: 100vh; position: fixed; top: 0; left: 0; z-index: 100;
            display: flex;
            flex-direction: column;
            padding: 20px 12px 24px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            overflow: hidden;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            margin-bottom: 12px;
        }

        .brand {
            display: flex; align-items: center; gap: 12px;
            padding: 0 8px 20px; margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            flex-shrink: 0;
        }

        .brand-icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: linear-gradient(135deg, #c9a961 0%, #8a5f36 100%);
            display: grid; place-items: center; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 8px 16px -4px rgba(169, 120, 74, 0.55);
        }

        .brand-name { font-size: 14px; font-weight: 800; color: #f5f3f0; line-height: 1.2; }
        .brand-role { font-size: 9px; font-weight: 700; color: #c9a961; text-transform: uppercase; letter-spacing: 0.14em; margin-top: 3px; }

        .nav-section { margin-bottom: 20px; }

        .nav-label {
            font-size: 9px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .15em;
            padding: 0 12px; margin-bottom: 8px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 9px;
            color: #a8a5a0; font-size: 13px; font-weight: 500;
            transition: all 0.18s ease; position: relative;
            margin-bottom: 2px; text-decoration: none;
        }

        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }
        .nav-item:hover { background: rgba(255, 255, 255, 0.04); color: #f5f3f0; }
        .nav-item.active { background: linear-gradient(90deg, rgba(169, 120, 74, 0.18), rgba(169, 120, 74, 0.06)); color: #f5f3f0; font-weight: 600; }
        .nav-item.active::before {
            content: ''; position: absolute; left: -12px; top: 50%; transform: translateY(-50%);
            width: 3px; height: 22px;
            background: linear-gradient(180deg, #c9a961, #a9784a);
            border-radius: 0 3px 3px 0;
        }

        .nav-count {
            margin-left: auto; padding: 2px 8px;
            background: rgba(169, 120, 74, 0.18); color: #c9a961;
            font-size: 10px; font-weight: 800; border-radius: 10px;
            min-width: 22px; text-align: center;
            border: 1px solid rgba(169, 120, 74, 0.25);
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 16px;
            padding-bottom: 8px;
            flex-shrink: 0;
        }

        .today-card {
            position: relative; padding: 16px; margin: 0 4px 12px; border-radius: 14px;
            background: linear-gradient(135deg, #b8845a 0%, #8a5f36 100%);
            color: #fff; overflow: hidden;
            box-shadow: 0 12px 24px -10px rgba(169, 120, 74, 0.6);
        }

        .today-label { font-size: 10px; font-weight: 800; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px; }
        .today-value { font-size: 26px; font-weight: 800; letter-spacing: -0.03em; line-height: 1; margin-bottom: 12px; }
        .today-meta { display: flex; align-items: center; gap: 8px; font-size: 10.5px; opacity: 0.95; font-weight: 600; }

        .logout-form { margin: 0; }
        .logout-btn {
            width: 100%;
            background: rgba(239, 68, 68, 0.08);
            color: #a8a5a0;
            border: 1px solid rgba(239, 68, 68, 0.15);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 11px 14px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.18s ease;
            text-align: left;
            white-space: nowrap;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
        }
        .logout-btn svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
        }

        /* ===== MAIN ===== */
        .main {
            flex: 1;
            min-width: 0;
            height: 100vh;
            margin-left: 240px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
            background-color: #1a1a22;
            background-image: linear-gradient(180deg, rgba(20, 20, 26, 0.65), rgba(15, 15, 20, 0.78)),
                url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        .topbar {
            position: sticky; top: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; padding: 16px 28px;
            background: rgba(20, 20, 26, 0.85);
            backdrop-filter: blur(20px) saturate(1.3);
            -webkit-backdrop-filter: blur(20px) saturate(1.3);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .topbar-right { display: flex; align-items: center; gap: 8px; }

        .menu-toggle {
            display: none; width: 36px; height: 36px;
            place-items: center; border: 1px solid var(--border-strong);
            border-radius: var(--radius-sm); color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.02);
        }

        .page-title { font-size: 18px; font-weight: 700; color: var(--text-primary); letter-spacing: -.02em; line-height: 1.2; }
        .page-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 13px; border: 1px solid var(--border);
            border-radius: var(--radius-sm); background: rgba(255, 255, 255, 0.03);
            font-size: 12px; font-weight: 500; color: var(--text-secondary);
        }

        .user-menu {
            display: flex; align-items: center; gap: 9px;
            padding: 4px 14px 4px 4px; border: 1px solid var(--border);
            border-radius: var(--radius-sm); background: rgba(255, 255, 255, 0.03);
        }

        .user-avatar {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            display: grid; place-items: center; color: #fff;
            font-size: 13px; font-weight: 800;
        }

        .user-name { font-size: 12px; font-weight: 600; color: var(--text-primary); line-height: 1.1; }
        .user-role { font-size: 10px; color: var(--accent); text-transform: uppercase; letter-spacing: .06em; font-weight: 700; }

        .content { flex: 1; padding: 24px 28px 32px; }

        .card {
            background: rgba(34, 34, 44, 0.55);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-lg); padding: 20px; margin-bottom: 16px;
        }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 6px; padding: 9px 16px; border-radius: var(--radius-sm);
            font-size: 12px; font-weight: 600; transition: all .15s ease;
            white-space: nowrap; border: 1px solid transparent; font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.5);
        }
        .btn-primary:hover { transform: translateY(-1px); }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-secondary); border-color: var(--border-strong);
        }
        .btn-sm { padding: 7px 14px; font-size: 11.5px; }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; font-size: 10px; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: .1em; padding: 12px;
            border-bottom: 1px solid var(--border);
        }
        tbody td {
            padding: 14px 12px; font-size: 13px;
            border-bottom: 1px solid var(--border); color: var(--text-secondary);
        }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 5px;
            font-size: 11px; font-weight: 700;
        }
        .badge-pending { background: var(--warning-bg); color: var(--warning); }
        .badge-completed, .badge-active, .badge-paid, .badge-approved { background: var(--success-bg); color: var(--success); }
        .badge-cancelled, .badge-suspended, .badge-overdue, .badge-rejected { background: var(--danger-bg); color: var(--danger); }
        .badge-info, .badge-inactive { background: var(--info-bg); color: var(--info); }
        .badge-partial, .badge-verified { background: var(--accent-bg); color: var(--accent-light); }

        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px; border-radius: var(--radius);
            font-size: 13px; font-weight: 500; margin-bottom: 16px; border: 1px solid;
        }
        .alert-success { background: var(--success-bg); color: var(--success); border-color: rgba(34, 197, 94, 0.25); }
        .alert-error { background: var(--danger-bg); color: var(--danger); border-color: rgba(239, 68, 68, 0.25); }

        .input, select, textarea {
            width: 100%; padding: 10px 12px;
            background: rgba(20, 20, 26, 0.6);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-sm);
            color: var(--text-primary); font-size: 13px; font-family: inherit;
            outline: none; transition: all .15s;
        }
        .input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
        }

        .foot {
            display: flex; justify-content: space-between;
            padding: 16px 28px; border-top: 1px solid var(--border);
            font-size: 11px; color: var(--text-muted);
            flex-shrink: 0;
        }

        .scrim { display: none !important; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1100px) {
            /* keep at least some grid handling on page level */
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s ease;
            }
            body.nav-open .sidebar {
                transform: translateX(0);
            }
            .main {
                margin-left: 0;
                height: 100vh;
                overflow-y: auto;
            }
            .menu-toggle { display: grid; }
            .content { padding: 16px; }
            .topbar { padding: 12px 16px; }
        }

        /* ===== NOTIFICATION BELL ===== */
        .notif-wrap { position: relative; display: inline-block; }
        .notif-bell {
            position: relative; display: inline-flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; border-radius: 10px;
            background: rgba(169, 120, 74, 0.1);
            border: 1px solid rgba(169, 120, 74, 0.25);
            color: #c9a961; cursor: pointer; transition: all 0.15s;
            font-family: inherit; padding: 0;
        }
        .notif-bell:hover { background: rgba(169, 120, 74, 0.2); }
        .notif-badge {
            position: absolute; top: -4px; right: -4px;
            min-width: 20px; height: 20px; padding: 0 5px;
            background: #ef4444; color: #fff; border-radius: 10px;
            font-size: 10px; font-weight: 800; display: grid; place-items: center;
            border: 2px solid #14141a;
        }
        .notif-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 340px; max-width: calc(100vw - 30px);
            background: rgba(20, 20, 26, 0.98); backdrop-filter: blur(20px);
            border: 1px solid rgba(169, 120, 74, 0.25); border-radius: 14px;
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.8);
            opacity: 0; visibility: hidden; transform: translateY(-8px);
            transition: all 0.2s; z-index: 9999; overflow: hidden;
            pointer-events: none;
        }
        .notif-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
        .notif-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border-bottom: 1px solid rgba(169, 120, 74, 0.15); }
        .notif-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; color: #f0f0f0; }
        .notif-dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; }
        .notif-mark-all { background: none; border: none; color: #c9a961; font-size: 11px; font-weight: 700; cursor: pointer; padding: 4px 8px; }
        .notif-body { max-height: 380px; overflow-y: auto; }
        .notif-empty { padding: 32px 20px; text-align: center; color: #7a7a85; font-size: 12.5px; }
        .notif-item {
            display: flex; gap: 12px; padding: 12px 16px;
            text-decoration: none; border-bottom: 1px solid rgba(169, 120, 74, 0.08);
            transition: background 0.15s; cursor: pointer;
        }
        .notif-item:hover { background: rgba(169, 120, 74, 0.08); }
        .notif-item-avatar {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            display: grid; place-items: center; color: #fff;
            font-size: 13px; font-weight: 800; overflow: hidden; flex-shrink: 0;
            position: relative;
        }
        .notif-item-body { flex: 1; min-width: 0; }
        .notif-item-store { font-size: 12.5px; font-weight: 700; color: #f0f0f0; margin-bottom: 3px; }
        .notif-item-meta { display: flex; justify-content: space-between; font-size: 11px; color: #7a7a85; gap: 8px; }
        .notif-item-amount { color: #c9a961; font-weight: 800; }
        .notif-foot {
            display: flex; justify-content: center; align-items: center; gap: 6px;
            padding: 12px; background: rgba(169, 120, 74, 0.08);
            border-top: 1px solid rgba(169, 120, 74, 0.15);
            color: #c9a961; font-size: 12px; font-weight: 700; text-decoration: none;
        }
    
        /* ========== UNIVERSAL BACK BUTTON ========== */
        .universal-back-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-secondary, #a1a1aa);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            -webkit-tap-highlight-color: transparent;
            padding: 0;
            font-family: inherit;
            margin-right: 4px;
        }
        .universal-back-btn:hover {
            background: rgba(201, 169, 97, 0.15);
            border-color: rgba(201, 169, 97, 0.35);
            color: #c9a961;
            transform: translateX(-2px);
        }
        .universal-back-btn:active {
            transform: scale(0.94);
        }
        .universal-back-btn svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        @media (max-width: 600px) {
            .universal-back-btn {
                width: 32px;
                height: 32px;
                margin-right: 2px;
            }
            .universal-back-btn svg {
                width: 14px;
                height: 14px;
            }
        }
    
    /* ============ PENDING ORDERS NAV BADGE ============ */
    .nav-count-badge {
        margin-left: auto;
        padding: 2px 8px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        border-radius: 100px;
        letter-spacing: 0.02em;
        min-width: 20px;
        text-align: center;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18);
        animation: navBadgePulse 2.4s ease-in-out infinite;
    }
    @keyframes navBadgePulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18); }
        50%      { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.04); }
    }
</style>
    @stack('styles')
</head>
<body>
    <div class="app">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                        <path d="M6 1v3M10 1v3M14 1v3"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-name">Coffee Beans</div>
                    <div class="brand-role">Consignment</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-label">Main</div>
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('finance.dashboard') }}" class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        Finance
                    </a>
                    <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        Transactions
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Consignment</div>
                    <a href="{{ route('stores.index') }}" class="nav-item {{ request()->routeIs('stores.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                        Stores
                        @php $storeCount = \App\Models\Store::count(); @endphp
                        @if($storeCount > 0)<span class="nav-count">{{ $storeCount }}</span>@endif
                    </a>
                    <a href="{{ route('deliveries.index') }}" class="nav-item {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Deliveries
                    </a>
                    <a href="{{ route('store-registrations.index') }}" class="nav-item {{ request()->routeIs('store-registrations.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M19 8v6M22 11h-6"/>
                        </svg>
                        Store Registrations
                    </a>
                    <a href="{{ route('reorder-requests.index') }}" class="nav-item {{ request()->routeIs('reorder-requests.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h6"/></svg>
                        Orders
                        @php $unread = \App\Models\ReorderRequest::where('is_read_by_admin', false)->count(); @endphp
                        @if($unread > 0)<span class="nav-count">{{ $unread }}</span>@endif
                    

                            @php
                                $__pendingOrders = \App\Models\ReorderRequest::whereRaw('LOWER(status) = ?', ['pending'])->count();
                            @endphp
                            @if($__pendingOrders > 0)
                                <span class="nav-count-badge">{{ $__pendingOrders > 99 ? '99+' : $__pendingOrders }}</span>
                            @endif</a>
                    <a href="{{ route('consignment.reports.index') }}" class="nav-item {{ request()->routeIs('consignment.reports.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
                        Sales Reports
                    </a>
                    <a href="{{ route('consignment.payments.index') }}" class="nav-item {{ request()->routeIs('consignment.payments.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        Payments
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Products</div>
                    <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Products
                    </a>
                </div>
            </nav>

            <div class="sidebar-bottom">
                <div class="today-card">
                    <div class="today-label">Today's Collected</div>
                    <div class="today-value">&#8369;{{ number_format(\App\Models\ConsignmentPayment::whereDate('created_at', today())->sum('amount') ?? 0, 2) }}</div>
                    <div class="today-meta">
                        <span>{{ \App\Models\DeliveryReceipt::whereDate('created_at', today())->count() }} deliveries</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN ===== --}}
        <div class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" onclick="document.body.classList.toggle('nav-open')">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                    </button>
                    @unless(request()->routeIs('dashboard'))
                        <button type="button" class="universal-back-btn" data-fallback="{{ route('dashboard') }}" title="Go back">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        </button>
                    @endunless
                    <div>
                        <div class="page-title">@yield('title', 'Dashboard')</div>
                        <div class="page-sub">@yield('subtitle', '')</div>
                    </div>
                </div>
                <div class="topbar-right">

                    {{-- NOTIFICATION BELL --}}
                    <div class="notif-wrap" id="notifWrap">
                        <button type="button" class="notif-bell" id="notifBell" onclick="toggleNotif(event)">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
                            </svg>
                            <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                        </button>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-head">
                                <div class="notif-title"><span class="notif-dot"></span>New Orders</div>
                                <button type="button" class="notif-mark-all" onclick="markAllRead(event)">Mark all read</button>
                            </div>
                            <div class="notif-body" id="notifBody">
                                <div class="notif-empty">Loading...</div>
                            </div>
                            <a href="{{ route('reorder-requests.index') }}" class="notif-foot">View all orders</a>
                        </div>
                    </div>

                    <div class="chip">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        {{ now()->format('g:i A') }}
                    </div>

                    <div class="user-menu">
                        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                        <div>
                            <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="user-role">Admin</div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>

            <footer class="foot">
                <div>&copy; {{ date('Y') }} Coffee Beans - Consignment System</div>
                <div>v1.0</div>
            </footer>
        </div>
    </div>

    <script>
        // ===== NOTIFICATIONS =====
        var notifUrl = "{{ route('notifications.index') }}";
        var markReadBase = "{{ url('/notifications') }}";
        var markAllUrl = "{{ route('notifications.read-all') }}";
        var csrfToken = "{{ csrf_token() }}";

        function toggleNotif(e) {
            if (e) e.stopPropagation();
            var dd = document.getElementById('notifDropdown');
            dd.classList.toggle('open');
            if (dd.classList.contains('open')) loadNotifications();
        }

        function loadNotifications() {
            var body = document.getElementById('notifBody');
            body.innerHTML = '<div class="notif-empty">Loading...</div>';

            fetch(notifUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function(data) {
                updateBadge(data.count || 0);
                renderNotifications(data.orders || []);
            })
            .catch(function(err) {
                console.error('Notif error:', err);
                body.innerHTML = '<div class="notif-empty">Failed to load</div>';
            });
        }

        function updateBadge(count) {
            var badge = document.getElementById('notifBadge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'grid';
            } else {
                badge.style.display = 'none';
            }
        }

        function renderNotifications(orders) {
            var body = document.getElementById('notifBody');
            if (!orders || orders.length === 0) {
                body.innerHTML = '<div class="notif-empty">Walay bag-ong notifications</div>';
                return;
            }
            var html = '';
            for (var i = 0; i < orders.length; i++) {
                var o = orders[i];
                var avatar = o.store_logo
                    ? '<img src="' + o.store_logo + '" style="width:100%;height:100%;object-fit:cover;">'
                    : o.store_initials;

                var label = '', amountHtml = '';
                if (o.type === 'order') {
                    label = (o.items || 0) + ' items';
                    amountHtml = '<span class="notif-item-amount">&#8369;' + o.total + '</span>';
                } else if (o.type === 'register') {
                    label = 'New registration';
                    amountHtml = '<span style="color:#22c55e;font-weight:800;font-size:10px;">NEW</span>';
                } else if (o.type === 'login') {
                    label = 'Logged in';
                    amountHtml = '<span style="color:#3b82f6;font-weight:700;font-size:10px;">LOGIN</span>';
                }

                html += '<a href="' + o.url + '" class="notif-item" onclick="markRead(' + o.id + ')">';
                html += '<div class="notif-item-avatar">' + avatar + '</div>';
                html += '<div class="notif-item-body">';
                html += '<div class="notif-item-store">' + o.store_name + '</div>';
                html += '<div class="notif-item-meta">';
                html += '<span>' + label + ' - ' + o.ago + '</span>';
                html += amountHtml;
                html += '</div></div></a>';
            }
            body.innerHTML = html;
        }

        function markRead(id) {
            fetch(markReadBase + '/' + id + '/read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).catch(function() {});
        }

        function markAllRead(e) {
            if (e) e.stopPropagation();
            fetch(markAllUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).then(function() {
                updateBadge(0);
                renderNotifications([]);
            }).catch(function() {});
        }

        document.addEventListener('click', function(e) {
            var wrap = document.getElementById('notifWrap');
            var dd = document.getElementById('notifDropdown');
            if (wrap && dd && !wrap.contains(e.target)) dd.classList.remove('open');
        });

        function poll() {
            fetch(notifUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function(r) { return r.ok ? r.json() : null; })
            .then(function(data) {
                if (!data) return;
                updateBadge(data.count || 0);
                var dd = document.getElementById('notifDropdown');
                if (dd && dd.classList.contains('open')) renderNotifications(data.orders || []);
            })
            .catch(function() {});
        }

        document.addEventListener('DOMContentLoaded', function() {
            poll();
            setInterval(poll, 20000);
        });
    </script>

    @stack('scripts')

        <script>
        // Smart back button ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â with fallback to dashboard
        (function() {
            document.addEventListener('click', function(e) {
                var btn = e.target.closest('.universal-back-btn');
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();

                var hasHistory = window.history.length > 1;
                var sameHost = document.referrer && document.referrer.indexOf(window.location.host) !== -1;

                if (hasHistory && sameHost) {
                    window.history.back();
                } else {
                    window.location.href = btn.dataset.fallback || '/dashboard';
                }
            });
        })();
        </script>
</body>
</html>
@push('scripts')
<script>
/**
 * Smart Nav Badge Polling
 * - Updates badge count ra, dili mag-reload sa page
 * - Skip kung tab hidden (para save bandwidth)
 * - 30s interval
 */
(function () {
    const POLL_INTERVAL = 30000;
    let timer = null;
    let lastCount = -1;

    function updateBadge(count) {
        document.querySelectorAll('a[href*="reorder-requests"]').forEach(link => {
            let badge = link.querySelector('.nav-count-badge');
            if (count > 0) {
                const txt = count > 99 ? '99+' : count;
                if (badge) {
                    badge.textContent = txt;
                } else {
                    badge = document.createElement('span');
                    badge.className = 'nav-count-badge';
                    badge.textContent = txt;
                    link.appendChild(badge);
                }
            } else if (badge) {
                badge.remove();
            }
        });
    }

    async function poll() {
        // Skip kung tab hidden
        if (document.hidden) return;

        try {
            const r = await fetch('/nav/pending-orders', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!r.ok) return;

            const data = await r.json();
            const count = data.pending ?? 0;

            if (count !== lastCount) {
                updateBadge(count);
                lastCount = count;
            }
        } catch (e) {
            // Silent fail
        }
    }

    function start() {
        if (timer) clearInterval(timer);
        poll();
        timer = setInterval(poll, POLL_INTERVAL);
    }

    function stop() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    // Pause when tab hidden (save resources)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stop();
        } else {
            start(); // Immediate poll on return
        }
    });

    start();
})();
</script>
@endpush