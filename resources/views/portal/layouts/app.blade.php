<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2D1A11">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light only">
    <title>@yield('title', 'Portal') - Coffee Beans</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ═══════════════════════════════════════════════════════
           PREMIUM ROAST THEME — PRO LAYOUT
           Elegant Coffee Shop Aesthetic
           ═══════════════════════════════════════════════════════ */
        :root {
            color-scheme: light only;
            --primary: #8D6E63;           /* Deep Espresso */
            --primary-light: #A1887F;     /* Medium Roast */
            --primary-soft: #A1887F;      /* Latte */
            --accent: #E8A87C;            /* Burnt Orange / Copper */
            --accent-light: #f472b6;
            --bg-body: #FDF8F3;           /* Warm Cream */
            --bg-card: #FFFFFF;
            --text-main: #4E342E;
            --text-muted: #A1887F;
            --border-color: #F5EDE6;
            --border-light: #F5EDE6;
            
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
            background: linear-gradient(135deg, #F5EDE6 0%, #EFE4D9 50%, #E8DCD1 100%);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 10px 30px -10px rgba(141, 110, 99, 0.15), 0 2px 6px -2px rgba(141, 110, 99, 0.08);
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
            gap: 2px;
            flex: 1;
            min-width: 0;
        }

        .p-topbar-store-name {
            font-size: 15px;
            font-weight: 800;
            color: #4E342E;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.02em;
        }

        .p-topbar-store-code {
            font-size: 10px;
            color: #8D6E63;
            font-family: ui-monospace, monospace;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        /* ===== MAIN CONTENT ===== */
        .p-main { padding: 24px 20px 120px; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
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
        .p-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px -6px rgba(232, 168, 124, 0.7); }
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
        .p-float-btn { position: fixed; z-index: 9997; display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #2D1A11, #4E342E); color: #ffffff; box-shadow: 0 8px 20px -5px rgba(141, 110, 99, 0.3), 0 2px 6px -2px rgba(141, 110, 99, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.8); transition: transform 0.2s ease; border: none; }
        .p-float-btn:hover { transform: scale(1.08); }
        .p-float-btn:active { transform: scale(0.95); }
        .p-float-hamburger { top: 16px; left: 16px; }
        .p-topbar { padding-left: 72px; }

        /* ===== SIDEBAR ===== */
        .p-sidebar-overlay { position: fixed; inset: 0; background: rgba(141, 110, 99, 0.35); z-index: 9998; opacity: 0; visibility: hidden; transition: opacity 0.25s ease, visibility 0.25s; }
        .p-sidebar-overlay.open { opacity: 1; visibility: visible; }
        .p-sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 85%; max-width: 320px; background: #ffffff; z-index: 9999; transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; box-shadow: 10px 0 40px rgba(45, 26, 17, 0.15); }
        .p-sidebar.open { transform: translateX(0); }
        .p-sidebar-head { padding: 26px 20px; background: linear-gradient(135deg, #F5EDE6 0%, #EFE4D9 100%); color: #4E342E; display: flex; align-items: center; justify-content: space-between; border-radius: 0 0 28px 28px; }
        .p-sidebar-title { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
        .p-sidebar-sub { font-size: 11.5px; color: var(--primary-soft); font-weight: 600; margin-top: 2px; }
        .p-sidebar-close { color: #8D6E63; padding: 8px; }
        .p-sidebar-nav { flex: 1; padding: 24px 20px; overflow-y: auto; }
        .p-sidebar-section-title { font-size: 10.5px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.14em; margin-bottom: 12px; margin-top: 24px; }
        .p-sidebar-section:first-child .p-sidebar-section-title { margin-top: 0; }
        .p-sidebar-link { display: flex; align-items: center; gap: 14px; padding: 14px 16px; border-radius: var(--radius-md); color: var(--text-main); font-weight: 600; font-size: 14px; transition: all 0.2s; margin-bottom: 4px; }
        .p-sidebar-link svg { color: var(--text-muted); transition: color 0.2s; }
        .p-sidebar-link:hover { background: #FDFBF7; color: var(--primary); }
        .p-sidebar-link:hover svg { color: var(--primary); }
        .p-sidebar-link.active { background: linear-gradient(135deg, #EFE4D9 0%, #E8DCD1 100%); color: #4E342E; box-shadow: 0 6px 16px -6px rgba(141, 110, 99, 0.25); }
        .p-sidebar-link.active svg { color: #B8651B; }
        .p-sidebar-foot { padding: 20px; border-top: 1px solid var(--border-light); background: #FDFBF7; }
        .p-sidebar-user { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding: 14px; background: #ffffff; border-radius: var(--radius-md); border: 1px solid var(--border-color); }
        .p-sidebar-user-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #EFE4D9 0%, #E8DCD1 100%); color: #4E342E; display: grid; place-items: center; font-weight: 800; flex-shrink: 0; }
        .p-sidebar-user-name { font-size: 14px; font-weight: 800; }
        .p-sidebar-user-email { font-size: 11px; color: var(--text-muted); font-weight: 500; }
        .p-sidebar-foot-link { display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: var(--radius-md); color: var(--text-main); font-weight: 700; font-size: 14px; width: 100%; text-align: left; transition: all 0.2s; }
        .p-sidebar-foot-link svg { color: var(--text-muted); }
        .p-sidebar-foot-link:hover { background: #EFEBE9; }
        .p-sidebar-foot-link.danger { color: #D84315; }
        .p-sidebar-foot-link.danger svg { color: #D84315; }
        .p-sidebar-foot-link.danger:hover { background: #FFEBEE; }

        /* ===== MODALS ===== */
        .p-modal-overlay { position: fixed; inset: 0; background: rgba(141, 110, 99, 0.35); z-index: 9999; display: flex; align-items: flex-end; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.25s ease, visibility 0.25s; }
        .p-modal-overlay.open { opacity: 1; visibility: visible; }
        .p-modal { background: #ffffff; width: 100%; max-width: 640px; max-height: 90vh; border-radius: 28px 28px 0 0; padding: 28px 24px; overflow-y: auto; transform: translateY(100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .p-modal-overlay.open .p-modal { transform: translateY(0); }
        .p-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .p-modal-title { font-size: 20px; font-weight: 800; letter-spacing: -0.02em; }
        .p-modal-close { width: 42px; height: 42px; border-radius: 50%; background: #EFEBE9; display: grid; place-items: center; color: var(--text-main); }

        /* ===== NOTIFICATION BELL — PREMIUM (NAVY BLUE) ===== */
        .pn-bell {
            position: fixed;
            top: 16px;
            right: 18px;
            width: 40px;
            height: 40px;
            padding: 0;
            border-radius: 50%;
            background:
                radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.25), transparent 60%),
                linear-gradient(135deg, #F5A96E 0%, #E8A87C 50%, #D89065 100%);
            color: #FFFFFF;
            display: grid;
            place-items: center;
            box-shadow:
                0 8px 24px -6px rgba(232, 168, 124, 0.7),
                0 2px 8px -2px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 -1px 0 rgba(0, 0, 0, 0.15);
            z-index: 9998;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(0.34, 1.4, 0.64, 1);
            -webkit-tap-highlight-color: transparent;
            overflow: visible;
        }
        .pn-bell::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, transparent, rgba(239, 108, 0, 0.7), transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
            animation: pnRingSpin 4s linear infinite;
            z-index: -1;
        }
        .pn-bell:hover {
            transform: translateY(-2px) scale(1.06);
            box-shadow:
                0 12px 32px -8px rgba(37, 99, 235, 0.85),
                0 4px 12px -2px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.35),
                inset 0 -1px 0 rgba(0, 0, 0, 0.15);
        }
        .pn-bell:hover::before { opacity: 1; }
        .pn-bell:active {
            transform: translateY(0) scale(0.96);
            box-shadow:
                0 4px 12px -4px rgba(37, 99, 235, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .pn-bell-inner {
            position: relative;
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            z-index: 1;
        }
        .pn-bell-icon { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .pn-bell:hover .pn-bell-icon { transform: rotate(-12deg) scale(1.05); }
        @keyframes pnRingSpin { to { transform: rotate(360deg); } }
        .pn-bell.has-unread::after { content: ''; position: absolute; inset: 0; border-radius: 50%; border: 2px solid rgba(232, 168, 124, 0.6); animation: pnPulse 2.5s ease-out infinite; pointer-events: none; }
        @keyframes pnPulse {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .pn-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: #fff;
            font-size: 9.5px;
            font-weight: 900;
            font-family: 'Inter', system-ui, sans-serif;
            min-width: 17px;
            height: 17px;
            padding: 0 5px;
            border-radius: 100px;
            display: grid;
            place-items: center;
            border: 2.5px solid #FFFFFF;
            box-shadow:
                0 3px 8px -2px rgba(220, 38, 38, 0.7),
                0 0 0 1px rgba(255, 255, 255, 0.5);
            letter-spacing: -0.02em;
            animation: pnBadgePop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes pnBadgePop {
            0% { transform: scale(0); }
            60% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .pn-panel {
            position: fixed;
            top: 68px;
            right: 14px;
            width: calc(100vw - 36px);
            max-width: 380px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 50px -10px rgba(45, 26, 17, 0.3);
            border: 1px solid var(--border-color);
            z-index: 9999;
            overflow: hidden;
            display: none;
            flex-direction: column;
            transform-origin: top right;
            animation: pnDropIn 0.25s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        @keyframes pnDropIn {
            from { opacity: 0; transform: translateY(-8px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
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
            .p-topbar { padding: 12px 16px 12px 64px; border-radius: 0 0 24px 24px; }
            .p-topbar-store-name { font-size: 14px; }
            .p-main { padding: 20px 18px 120px; }
            .p-float-hamburger { top: 14px; left: 14px; width: 38px; height: 38px; }
            .pn-bell { top: 12px; right: 14px; width: 36px; height: 36px; }
            .pn-panel { top: 60px; left: 12px; right: 12px; width: auto; }
        }
    
        /* ═══ AUTO-DISMISS ALERTS ═══ */
        .p-alert {
            transition: opacity 0.5s ease, transform 0.5s ease, max-height 0.5s ease, margin 0.5s ease, padding 0.5s ease, border-width 0.5s ease;
        }
        .p-alert.fade-out {
            opacity: 0;
            transform: translateY(-12px);
            max-height: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border-width: 0 !important;
            overflow: hidden;
        }

        /* ═══ SMART HEADER + HAMBURGER ═══ */
        .p-topbar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .p-topbar.hide {
            transform: translateY(-120%);
            box-shadow: none;
        }

        .p-float-hamburger { transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease; }
        .p-float-hamburger.hide {
            opacity: 0;
            transform: translateX(-120%);
            pointer-events: none;
        }

        /* ═══ TOPBAR HIDDEN (temporary) ═══ */
        .p-topbar {
            display: none !important;
        }
        .p-main {
            padding-top: 24px !important;
        }

        /* ═══ BELL DRAG ═══ */
        .pn-bell {
            cursor: grab;
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }
        .pn-bell.dragging {
            cursor: grabbing;
            transition: none !important;
            transform: scale(1.1) !important;
            box-shadow:
                0 16px 40px -8px rgba(216, 67, 21, 0.9),
                0 8px 20px -4px rgba(0, 0, 0, 0.4) !important;
        }
        .pn-bell.dragging::before { opacity: 0 !important; }
        /* Hide position jump on load */
        .pn-bell.no-transition {
            transition: none !important;
        }

        /* ═══ BELL NOTIFICATION SHAKE (only when unread) ═══ */
        .pn-bell.has-unread .pn-bell-icon { animation: pnBellShake 2.5s ease-in-out infinite; transform-origin: 50% 0%; }
        @keyframes pnBellShake {
            0%, 50%, 100% { transform: rotate(0deg); }
            5%  { transform: rotate(-12deg); }
            10% { transform: rotate(10deg); }
            15% { transform: rotate(-8deg); }
            20% { transform: rotate(6deg); }
            25% { transform: rotate(-4deg); }
            30% { transform: rotate(2deg); }
            35% { transform: rotate(0deg); }
        }

        /* Pulsing ring kapag may unread */
        .pn-bell.has-unread::after { content: ''; position: absolute; inset: 0; border-radius: 50%; border: 2px solid rgba(232, 168, 124, 0.6); animation: pnPulse 2.5s ease-out infinite; pointer-events: none; }
        @keyframes pnPulse {
            0%   { transform: scale(1);    opacity: 0.8; }
            100% { transform: scale(1.6);  opacity: 0; }
        }

        /* Glow pulse sa buong bell */
        .pn-bell.has-unread { animation: none; }
        @keyframes pnBellGlow {
            0%, 100% {
                box-shadow:
                    0 8px 24px -6px rgba(232, 168, 124, 0.7),
                    0 2px 8px -2px rgba(0, 0, 0, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.25);
            }
            50% {
                box-shadow:
                    0 12px 32px -6px rgba(216, 67, 21, 0.95),
                    0 4px 12px -2px rgba(0, 0, 0, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.35),
                    0 0 0 6px rgba(216, 67, 21, 0.15);
            }
        }

        /* Hover — pahinga muna ang animation */
        .pn-bell.has-unread:hover .pn-bell-icon {
            animation-play-state: paused;
        }
        .pn-bell.has-unread:hover {
            animation-play-state: paused;
        }

        /* Badge pop animation */
        .pn-badge {
            animation: pnBadgePop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes pnBadgePop {
            0%   { transform: scale(0); }
            60%  { transform: scale(1.25); }
            100% { transform: scale(1); }
        }

        /* ═══════════════════════════════════════════════════
           ENHANCED UI v1 — PREMIUM TOUCHES
           ═══════════════════════════════════════════════════ */

        /* ═══ RICHER DESIGN TOKENS ═══ */
        :root {
            --glow-warm: rgba(216, 67, 21, 0.35);
            --glow-gold: rgba(201, 169, 97, 0.4);
            --gradient-coffee: linear-gradient(135deg, #F5EDE6 0%, #EFE4D9 50%, #E8DCD1 100%);
            --gradient-accent: linear-gradient(135deg, #F5A96E 0%, #E8A87C 50%, #D89065 100%);
            --gradient-gold: linear-gradient(135deg, #E6C88A 0%, #C9A961 50%, #A6761D 100%);
            --shadow-elevated: 0 20px 50px -12px rgba(45, 26, 17, 0.18), 0 8px 20px -6px rgba(45, 26, 17, 0.08);
            --shadow-hover: 0 24px 60px -14px rgba(45, 26, 17, 0.22), 0 12px 28px -8px rgba(216, 67, 21, 0.15);
            --shadow-btn: 0 12px 28px -8px rgba(232, 168, 124, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        /* ═══ SMOOTH SCROLL + CUSTOM SCROLLBAR ═══ */
        html { scroll-behavior: smooth; }
        body { -webkit-tap-highlight-color: transparent; }
        * { scrollbar-width: thin; scrollbar-color: #D7CCC8 transparent; }
        *::-webkit-scrollbar { width: 6px; height: 6px; }
        *::-webkit-scrollbar-track { background: transparent; }
        *::-webkit-scrollbar-thumb { background: #D7CCC8; border-radius: 3px; }
        *::-webkit-scrollbar-thumb:hover { background: #A1887F; }

        /* ═══ TOPBAR — ENHANCED ═══ */
        .p-topbar {
            background:
                radial-gradient(circle at 20% 0%, rgba(201, 169, 97, 0.15), transparent 50%),
                radial-gradient(circle at 80% 100%, rgba(216, 67, 21, 0.15), transparent 50%),
                var(--gradient-coffee);
            box-shadow:
                0 10px 30px -10px rgba(45, 26, 17, 0.5),
                0 2px 6px -2px rgba(45, 26, 17, 0.2),
                inset 0 -1px 0 rgba(255, 255, 255, 0.06);
            position: relative;
        }
        .p-topbar::after {
            content: '';
            position: absolute;
            left: 20%;
            right: 20%;
            bottom: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201, 169, 97, 0.5), transparent);
            pointer-events: none;
        }

        /* ═══ PORTAL WRAP — subtle texture ═══ */
        .portal-wrap {
            background:
                radial-gradient(circle at 10% 5%, rgba(216, 67, 21, 0.03), transparent 30%),
                radial-gradient(circle at 90% 90%, rgba(45, 26, 17, 0.04), transparent 30%),
                var(--bg-body);
        }

        /* ═══ MAIN CONTENT — smooth entry ═══ */
        .p-main {
            animation: fadeIn 0.5s cubic-bezier(0.34, 1.2, 0.64, 1) both;
        }

        /* ═══ CARDS — elevated look ═══ */
        .p-card {
            position: relative;
            background: #FFFFFF;
            border: 1.5px solid #EFEBE9;
            border-radius: 20px;
            padding: 22px;
            box-shadow:
                0 2px 8px rgba(45, 26, 17, 0.04),
                0 1px 2px rgba(45, 26, 17, 0.03);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, border-color 0.2s ease;
            overflow: hidden;
        }
        .p-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-accent);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.34, 1.2, 0.64, 1);
            border-radius: 20px 20px 0 0;
        }
        .p-card:hover {
            transform: translateY(-3px);
            border-color: #D7CCC8;
            box-shadow: var(--shadow-hover);
        }
        .p-card:hover::before {
            transform: scaleX(1);
        }

        /* ═══ CARD ICON — animated ═══ */
        .p-card-icon {
            transition: all 0.3s cubic-bezier(0.34, 1.4, 0.64, 1);
        }
        .p-card:hover .p-card-icon {
            transform: scale(1.08) rotate(-3deg);
            box-shadow: 0 8px 16px -6px rgba(45, 26, 17, 0.2);
        }

        /* ═══ BUTTONS — premium shine ═══ */
        .p-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        .p-btn-primary {
            background: var(--gradient-accent);
            box-shadow: var(--shadow-btn);
        }
        .p-btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transition: left 0.6s ease;
        }
        .p-btn-primary:hover::before {
            left: 100%;
        }
        .p-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow:
                0 18px 36px -10px rgba(232, 168, 124, 0.7),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        .p-btn-primary:active {
            transform: translateY(0);
            box-shadow:
                0 6px 16px -6px rgba(216, 67, 21, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .p-btn-ghost {
            transition: all 0.2s;
        }
        .p-btn-ghost:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -8px rgba(45, 26, 17, 0.15);
        }

        /* ═══ BADGES — dot indicator ═══ */
        .p-badge {
            position: relative;
            padding-left: 20px;
        }
        .p-badge::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 8px currentColor;
        }
        .p-badge-pending::before {
            animation: badgePulse 1.8s ease-in-out infinite;
        }
        @keyframes badgePulse {
            0%, 100% { opacity: 1; transform: translateY(-50%) scale(1); }
            50%      { opacity: 0.5; transform: translateY(-50%) scale(0.8); }
        }

        /* ═══ INPUTS — premium focus ═══ */
        .p-input, .p-select, .p-textarea {
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .p-input:hover:not(:focus),
        .p-select:hover:not(:focus),
        .p-textarea:hover:not(:focus) {
            border-color: #D7CCC8;
            background: #FFFDFA;
        }
        .p-input:focus,
        .p-select:focus,
        .p-textarea:focus {
            transform: translateY(-1px);
            box-shadow:
                0 0 0 4px rgba(45, 26, 17, 0.06),
                0 4px 12px -4px rgba(45, 26, 17, 0.1);
        }

        /* ═══ ALERTS — enhanced ═══ */
        .p-alert {
            position: relative;
            overflow: hidden;
            border-left: 4px solid;
        }
        .p-alert-success {
            border-left-color: #16A34A;
            background: linear-gradient(90deg, #DCFCE7 0%, #F0FDF4 100%);
        }
        .p-alert-error {
            border-left-color: #DC2626;
            background: linear-gradient(90deg, #FEE2E2 0%, #FEF2F2 100%);
        }
        .p-alert svg {
            animation: alertIconIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes alertIconIn {
            0%   { transform: scale(0) rotate(-45deg); }
            60%  { transform: scale(1.2) rotate(5deg); }
            100% { transform: scale(1) rotate(0); }
        }

        /* ═══ SIDEBAR — enhanced ═══ */
        .p-sidebar {
            box-shadow:
                20px 0 60px -15px rgba(45, 26, 17, 0.35),
                4px 0 12px -4px rgba(45, 26, 17, 0.1);
        }
        .p-sidebar-head {
            background:
                radial-gradient(circle at 80% 20%, rgba(201, 169, 97, 0.2), transparent 50%),
                var(--gradient-coffee);
            position: relative;
        }
        .p-sidebar-head::after {
            content: '';
            position: absolute;
            left: 20%;
            right: 20%;
            bottom: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(184, 101, 27, 0.3), transparent);
        }
        .p-sidebar-link {
            position: relative;
            transition: all 0.22s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        .p-sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%) scaleY(0);
            width: 3px;
            height: 60%;
            background: var(--gradient-accent);
            border-radius: 0 3px 3px 0;
            transition: transform 0.25s cubic-bezier(0.34, 1.2, 0.64, 1);
            transform-origin: center;
        }
        .p-sidebar-link:hover:not(.active) {
            padding-left: 20px;
            background: #FDFBF7;
        }
        .p-sidebar-link.active {
            box-shadow:
                0 8px 20px -8px rgba(45, 26, 17, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }
        .p-sidebar-link.active::before {
            transform: translateY(-50%) scaleY(1);
            background: #C9A961;
        }
        .p-sidebar-foot-link {
            transition: all 0.2s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        .p-sidebar-foot-link:hover {
            transform: translateX(3px);
        }
        .p-sidebar-foot-link.danger:hover {
            transform: translateX(3px);
        }

        /* ═══ MODAL — enhanced ═══ */
        .p-modal {
            box-shadow:
                0 -30px 80px -20px rgba(45, 26, 17, 0.5),
                0 -4px 12px -4px rgba(45, 26, 17, 0.1);
        }
        .p-modal-header {
            position: relative;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1.5px dashed #EFEBE9;
        }
        .p-modal-title {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 22px;
        }
        .p-modal-close {
            transition: all 0.2s cubic-bezier(0.34, 1.4, 0.64, 1);
        }
        .p-modal-close:hover {
            background: var(--gradient-accent);
            color: #FFFFFF;
            transform: rotate(90deg) scale(1.05);
        }

        /* ═══ FOOTER ═══ */
        .portal-wrap footer {
            position: relative;
            padding-top: 32px !important;
        }
        .portal-wrap footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 2px;
            background: var(--gradient-gold);
            border-radius: 2px;
            opacity: 0.5;
        }

        
        }

        /* ═══ CARD HEAD — better typography ═══ */
        .p-card-title {
            letter-spacing: -0.02em;
        }
        .p-card-sub {
            letter-spacing: 0.01em;
        }

        /* ═══ NAV BADGE (kung may badge sa sidebar) ═══ */
        .p-sidebar-link .badge-count {
            margin-left: auto;
            background: var(--gradient-accent);
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 100px;
            min-width: 22px;
            text-align: center;
            box-shadow: 0 4px 10px -3px rgba(232, 168, 124, 0.6);
        }

        /* ═══ RESPONSIVE TWEAKS ═══ */
        @media (max-width: 480px) {
            .p-card {
                border-radius: 16px;
                padding: 18px;
            }
            .p-card:hover {
                transform: translateY(-2px);
            }
            .p-btn-primary:hover {
                transform: translateY(-1px);
            }
        }

        /* ═══ REDUCED MOTION ═══ */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* ═══ ANTI-FLICKER: Disable hover transforms on touch devices ═══ */
        @media (hover: none) {
            .p-card:hover {
                transform: none !important;
                box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04), 0 1px 2px rgba(45, 26, 17, 0.03) !important;
                border-color: #EFEBE9 !important;
            }
            .p-card:hover::before { transform: scaleX(0) !important; }
            .p-card:hover .p-card-icon { transform: none !important; }
            .p-btn-primary:hover { transform: none !important; }
            .p-btn-ghost:hover { transform: none !important; }
            .p-sidebar-link:hover:not(.active) { padding-left: 14px !important; background: transparent !important; }
            .p-sidebar-link:hover:not(.active)::before { transform: translateY(-50%) scaleY(0) !important; }
            .p-sidebar-foot-link:hover { transform: none !important; }
            .p-float-hamburger:hover::before { opacity: 0 !important; }
        }

        /* ═══ ANTI-FLICKER: Prevent white flash on load ═══ */
        html { background: #F5EFE9; }
        body {
            opacity: 1;
            transition: opacity 0.15s ease;
        }
        body.page-loading {
            opacity: 0;
        }
        img, svg { max-width: 100%; }

        /* ═══ ANTI-FLICKER: Smooth backdrop overlays ═══ */
        .p-sidebar-overlay,
        .p-modal-overlay { position: fixed; inset: 0; background: rgba(141, 110, 99, 0.35); z-index: 9999; display: flex; align-items: flex-end; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.25s ease, visibility 0.25s; }

        /* ═══ ANTI-FLICKER: Reduce animation on activity ═══ */
        .pn-bell.has-unread .pn-bell-icon {
            animation-duration: 2.5s;
            animation-timing-function: ease-in-out;
        }
        .pn-bell.has-unread::after { content: ''; position: absolute; inset: 0; border-radius: 50%; border: 2px solid rgba(232, 168, 124, 0.6); animation: pnPulse 2.5s ease-out infinite; pointer-events: none; }
        .pn-bell.has-unread {
            animation-duration: 2.5s;
        }

        /* ═══ ANTI-FLICKER: Smooth header transition ═══ */
        .p-topbar {
            
            
        }

        /* ═══════════════════════════════════════════════════════
           ANTI-FLICKER v2 — FINAL (mobile Chrome fix)
           ═══════════════════════════════════════════════════════ */

        /* Disable ALL scroll anchoring + overscroll bounce bugs */
        html {
            overscroll-behavior-y: none;
            -webkit-overflow-scrolling: auto;
        }
        body {
            overscroll-behavior-y: none;
            overflow-anchor: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Remove ALL animations on scroll-affected elements */
        .p-topbar,
        .p-float-hamburger,
        .p-topbar.hide,
        .p-float-hamburger.hide {
            will-change: auto !important;
            backface-visibility: visible !important;
            transform-style: flat !important;
            perspective: none !important;
        }

        /* Smooth scroll container */
        .portal-wrap { isolation: isolate; }

        /* Prevent wallet card (or any card) from flicker on scroll */
        .p-card,
        .sw-wallet,
        .sw-wallet-content {
            transform: none !important;
            filter: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            will-change: auto !important;
            animation: none !important;
        }

        /* Remove any blur/filter */
        .sw-wallet-bg,
        .sw-wallet::before,
        .sw-wallet::after {
            filter: none !important;
        }

        /* Force solid background for sticky elements */
        .p-topbar {
            background-clip: padding-box;
        }

        /* Stop all running animations on scroll-triggered elements */
        @media (max-width: 768px) {
            .p-topbar {
                transition-duration: 0.2s !important;
                transition-timing-function: ease-out !important;
            }
            .p-float-hamburger {
                transition-duration: 0.25s !important;
            }
        }

        /* Reduce motion for all animations (mobile battery + smooth) */
        @media (prefers-reduced-motion: reduce), (max-width: 480px) {
            .p-card,
            .p-card::before,
            .p-card-icon,
            .p-btn,
            .p-sidebar-link,
            .p-float-hamburger,
            .pn-bell {
                transition-duration: 0.15s !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
            }
        }

        /* ═══ FIX: smooth card rendering ═══ */
        .p-card {
            background: #FFFFFF;
            transform: translate3d(0, 0, 0);
            -webkit-transform: translate3d(0, 0, 0);
        }

        /* ═══ FIX: prevent repaint on scroll ═══ */
        


        /* ═══ HAMBURGER HOVER — ORANGE ═══ */
        .p-float-hamburger:hover {
            background: linear-gradient(135deg, #E8A87C 0%, #D89065 100%) !important;
            color: #FFFFFF !important;
            transform: scale(1.06);
            box-shadow:
                0 14px 32px -8px rgba(232, 168, 124, 0.7),
                0 6px 14px -4px rgba(141, 110, 99, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                0 0 0 2px rgba(255, 255, 255, 0.9) !important;
        }
        .p-float-hamburger:active {
            transform: scale(0.95);
        }

        


        /* ═══ HAMBURGER IDLE HIDE (clean) ═══ */
        .p-float-hamburger {
            transition:
                transform 0.45s cubic-bezier(0.34, 1.2, 0.64, 1),
                opacity 0.3s ease !important;
        }
        .p-float-hamburger.hide {
            opacity: 0 !important;
            transform: translateX(-120%) !important;
            pointer-events: none !important;
        }


        /* ═══ HAMBURGER PINNED FIXED (never scrolls with content) ═══ */
        .p-float-hamburger {
            position: fixed !important;
            top: 16px !important;
            left: 16px !important;
            z-index: 9997 !important;
            margin: 0 !important;
            will-change: opacity, transform !important;
        }
        .p-float-hamburger.hide {
            opacity: 0 !important;
            transform: translateX(-150%) !important;
            pointer-events: none !important;
        }




        /* ═══ FLOATING ELEMENTS — VIEWPORT FIXED (safe, hindi ginagalaw scroll) ═══ */
        body > .p-float-hamburger,
        body > .pn-bell,
        body > .pn-panel {
            position: fixed;
        }
        body > .p-float-hamburger {
            top: 16px;
            left: 16px;
            z-index: 9997;
        }
        body > .pn-bell {
            top: 16px;
            right: 18px;
            z-index: 9998;
        }
        body > .pn-panel {
            top: 68px;
            right: 14px;
            z-index: 9999;
        }


        /* ═══ BELL DRAG FIX — pointer-events ═══ */
        .pn-bell svg,
        .pn-bell .pn-badge,
        .pn-bell-inner {
            pointer-events: none !important;
        }
        .pn-bell {
            touch-action: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
            pointer-events: auto !important;
        }


        /* ═══════════════════════════════════════════════
           HAMBURGER — ICON ONLY (no circle)
           ═══════════════════════════════════════════════ */
        .p-float-hamburger {
            position: fixed !important;
            top: 16px !important;
            left: 16px !important;
            right: auto !important;
            bottom: auto !important;
            width: auto !important;
            height: auto !important;
            padding: 6px !important;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            color: #4E342E !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            z-index: 99999 !important;
            visibility: visible !important;
            opacity: 1;
            transform: translateX(0);
            transition: transform 0.35s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.3s ease;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        .p-float-hamburger:hover {
            background: transparent !important;
            color: #E8A87C !important;
            transform: scale(1.1);
        }
        .p-float-hamburger:active {
            transform: scale(0.95);
        }
        .p-float-hamburger.hide {
            opacity: 0 !important;
            transform: translateX(-150%) !important;
            pointer-events: none !important;
            visibility: hidden !important;
        }
        .p-float-hamburger svg {
            display: block !important;
            width: 26px !important;
            height: 26px !important;
            stroke: currentColor !important;
            stroke-width: 2.2 !important;
        }

        /* Alisin ang contain: layout sa portal-wrap — ito ang nag-break ng fixed */
        .portal-wrap {
            contain: none !important;
        }


        /* ═══════════════════════════════════════════════════════
           FINAL BODY GRADIENT OVERRIDE
           (para hindi solid cream ang background)
           ═══════════════════════════════════════════════════════ */
        html {
            background: linear-gradient(180deg, #FBF6F0 0%, #F5EDE6 100%) !important;
        }
        body {
            background-color: transparent !important;
            background-image:
                radial-gradient(circle at 0% 0%, rgba(232, 168, 124, 0.10), transparent 45%),
                radial-gradient(circle at 100% 100%, rgba(201, 169, 97, 0.08), transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.6), transparent 70%),
                linear-gradient(180deg, #FBF6F0 0%, #F5EDE6 100%) !important;
            background-attachment: fixed !important;
        }

</style>
    @stack('styles')
</head>
<body>


<div class="portal-wrap">
{{-- ═══ FLOATING HAMBURGER ═══ --}}
    <button type="button" class="p-float-btn p-float-hamburger" id="pHamburger" aria-label="Open menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="4" width="18" height="16" rx="2.5" fill="currentColor" opacity="0.15"/>
            <rect x="3" y="4" width="18" height="16" rx="2.5" stroke="currentColor" stroke-width="2"/>
            <rect x="3" y="4" width="6" height="16" rx="2.5" fill="currentColor"/>
            <path d="M9 4v16" stroke="currentColor" stroke-width="0"/>
        </svg>
    </button>


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



@endauth

{{-- ═══════════════════════════════════════════════════════
     JAVASCRIPT
     ═══════════════════════════════════════════════════════ --}}

{{-- ═══ FLOATING ELEMENTS (body level — hindi affected ng portal scroll) ═══ --}}


@auth('store')



@endauth
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
                /* FIX: Only update DOM kung may changes — prevents flicker */
                var newCount = data.count > 99 ? '99+' : String(data.count);
                var currentCount = badge.textContent;

                if (data.count > 0) {
                    if (badge.style.display !== 'grid') badge.style.display = 'grid';
                    if (currentCount !== newCount) badge.textContent = newCount;
                    if (!bell.classList.contains('has-unread')) {
                        bell.classList.add('has-unread');
                    }
                } else {
                    if (badge.style.display !== 'none') badge.style.display = 'none';
                    if (bell.classList.contains('has-unread')) {
                        bell.classList.remove('has-unread');
                    }
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

        setInterval(fetchNotifications, 30000);
        setTimeout(fetchNotifications, 1500);
    })();
</script>

@stack('scripts')



<script>
(function() {
    /* ═══ AUTO-DISMISS ALERTS (success/error) after 3.5s ═══ */
    var alerts = document.querySelectorAll('.p-alert');
    alerts.forEach(function(alert, index) {
        setTimeout(function() {
            alert.classList.add('fade-out');
            setTimeout(function() { 
                if (alert.parentNode) alert.remove(); 
            }, 500);
        }, 3500 + (index * 200));
    });
})();
</script>















{{-- ═══ FLOATING ELEMENTS (single copy, body level) ═══ --}}


@auth('store')



@endauth

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




<script>
(function() {
    'use strict';

    var bell = document.getElementById('pnBell');
    if (!bell) {
        console.warn('[Bell] Element #pnBell not found');
        return;
    }

    console.log('[Bell] Drag script initialized');

    var STORAGE_KEY = 'pn_bell_pos_v4';
    var DRAG_THRESHOLD = 5;
    var EDGE = 14;
    var SNAP_DURATION = 300;

    var dragging = false;
    var wasDragged = false;
    var startX = 0, startY = 0, startLeft = 0, startTop = 0;
    var activePointerId = null;

    function getSize() {
        var r = bell.getBoundingClientRect();
        return { w: r.width || 40, h: r.height || 40 };
    }

    function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }

    function setPos(left, top, animate) {
        var s = getSize();
        left = clamp(left, EDGE, window.innerWidth - s.w - EDGE);
        top = clamp(top, EDGE, window.innerHeight - s.h - EDGE);

        if (animate) {
            bell.style.setProperty('transition', 'left ' + SNAP_DURATION + 'ms cubic-bezier(0.34, 1.3, 0.64, 1), top ' + SNAP_DURATION + 'ms cubic-bezier(0.34, 1.3, 0.64, 1)', 'important');
        } else {
            bell.style.setProperty('transition', 'none', 'important');
        }

        bell.style.setProperty('position', 'fixed', 'important');
        bell.style.setProperty('left', left + 'px', 'important');
        bell.style.setProperty('top', top + 'px', 'important');
        bell.style.setProperty('right', 'auto', 'important');
        bell.style.setProperty('bottom', 'auto', 'important');
        bell.style.setProperty('margin', '0', 'important');

        return { left: left, top: top };
    }

    function snapToEdge(left, top) {
        var s = getSize();
        var screenW = window.innerWidth;
        var centerX = left + s.w / 2;
        var snapLeft = (centerX < screenW / 2) ? EDGE : (screenW - s.w - EDGE);
        var snapTop = clamp(top, EDGE, window.innerHeight - s.h - EDGE);
        setPos(snapLeft, snapTop, true);
        return { left: snapLeft, top: snapTop };
    }

    function savePos(l, t) { try { localStorage.setItem(STORAGE_KEY, JSON.stringify({ left: l, top: t })); } catch(e){} }
    function loadPos() { try { var d = localStorage.getItem(STORAGE_KEY); return d ? JSON.parse(d) : null; } catch(e){ return null; } }

    /* Restore saved position */
    var saved = loadPos();
    if (saved && typeof saved.left === 'number' && typeof saved.top === 'number') {
        setPos(saved.left, saved.top, false);
        console.log('[Bell] Restored position:', saved);
    } else {
        /* Default: top-right */
        var s = getSize();
        setPos(window.innerWidth - s.w - 18, 16, false);
    }

    /* ═══ POINTER EVENTS (unified mouse + touch) ═══ */
    function onPointerDown(e) {
        if (e.button !== undefined && e.button !== 0 && e.pointerType === 'mouse') return;

        activePointerId = e.pointerId;
        var curLeft = parseFloat(bell.style.left);
        var curTop = parseFloat(bell.style.top);

        if (isNaN(curLeft) || isNaN(curTop)) {
            var rect = bell.getBoundingClientRect();
            curLeft = rect.left;
            curTop = rect.top;
        }

        startX = e.clientX;
        startY = e.clientY;
        startLeft = curLeft;
        startTop = curTop;

        dragging = true;
        wasDragged = false;

        bell.classList.add('dragging');
        try { bell.setPointerCapture(e.pointerId); } catch(err) {}

        e.stopPropagation();
    }

    function onPointerMove(e) {
        if (!dragging) return;
        if (activePointerId !== null && e.pointerId !== activePointerId) return;

        var dx = e.clientX - startX;
        var dy = e.clientY - startY;

        if (!wasDragged) {
            if (Math.abs(dx) > DRAG_THRESHOLD || Math.abs(dy) > DRAG_THRESHOLD) {
                wasDragged = true;
            } else {
                return;
            }
        }

        e.preventDefault();
        setPos(startLeft + dx, startTop + dy, false);
    }

    function onPointerUp(e) {
        if (!dragging) return;
        if (activePointerId !== null && e.pointerId !== activePointerId) return;

        dragging = false;
        activePointerId = null;
        bell.classList.remove('dragging');

        try { bell.releasePointerCapture(e.pointerId); } catch(err) {}

        if (wasDragged) {
            var l = parseFloat(bell.style.left);
            var t = parseFloat(bell.style.top);
            if (!isNaN(l) && !isNaN(t)) {
                var snapped = snapToEdge(l, t);
                setTimeout(function() { savePos(snapped.left, snapped.top); }, SNAP_DURATION + 30);
            }
            console.log('[Bell] Dragged to:', l, t, '→ snapped');
        }
    }

    /* Attach pointer events */
    bell.addEventListener('pointerdown', onPointerDown, { passive: false });
    bell.addEventListener('pointermove', onPointerMove, { passive: false });
    bell.addEventListener('pointerup', onPointerUp);
    bell.addEventListener('pointercancel', onPointerUp);

    /* Block click after drag */
    bell.addEventListener('click', function(e) {
        if (wasDragged) {
            e.preventDefault();
            e.stopPropagation();
            wasDragged = false;
            return false;
        }
    }, true);

    /* Double-tap to reset */
    var lastTap = 0;
    bell.addEventListener('pointerup', function() {
        var now = Date.now();
        if (now - lastTap < 350 && !wasDragged) {
            try { localStorage.removeItem(STORAGE_KEY); } catch(err) {}
            var sz = getSize();
            var resetLeft = window.innerWidth - sz.w - EDGE;
            setPos(resetLeft, 16, true);
            savePos(resetLeft, 16);
            console.log('[Bell] Reset to top-right');
        }
        lastTap = now;
    });

    /* Handle resize */
    window.addEventListener('resize', function() {
        var l = parseFloat(bell.style.left);
        var t = parseFloat(bell.style.top);
        if (!isNaN(l) && !isNaN(t)) setPos(l, t, false);
    });

    console.log('[Bell] Drag ready ✓');
})();
</script>


<script>
(function() {
    'use strict';

    var bell = document.getElementById('pnBell');
    var panel = document.getElementById('pnPanel');
    var body = document.getElementById('pnBody');
    var badge = document.getElementById('pnBadge');
    var markAll = document.getElementById('pnMarkAll');

    if (!bell) return;

    var CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

    /* ═══════════════════════════════════════════════════════
       PART 1: NOTIFICATION CLICK FUNCTION
       ═══════════════════════════════════════════════════════ */
    var isPanelOpen = false;

    function fetchNotifications() {
        if (!panel || !body) return;
        fetch('{{ route("portal.notifications.unread") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(data => {
            var newCount = data.count > 99 ? '99+' : String(data.count);
            var currentCount = badge.textContent;

            if (data.count > 0) {
                if (badge.style.display !== 'grid') badge.style.display = 'grid';
                if (currentCount !== newCount) badge.textContent = newCount;
                if (!bell.classList.contains('has-unread')) bell.classList.add('has-unread');
            } else {
                if (badge.style.display !== 'none') badge.style.display = 'none';
                if (bell.classList.contains('has-unread')) bell.classList.remove('has-unread');
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

    if (panel) {
        /* Mark all read */
        if (markAll) {
            markAll.addEventListener('click', function() {
                fetch('{{ route("portal.notifications.read-all") }}', {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                }).then(() => fetchNotifications());
            });
        }

        /* Close panel when clicking outside */
        document.addEventListener('click', function(e) {
            if (isPanelOpen && !panel.contains(e.target) && !bell.contains(e.target)) {
                panel.classList.remove('open');
                isPanelOpen = false;
            }
        });

        /* Poll notifications */
        setInterval(fetchNotifications, 30000);
        setTimeout(fetchNotifications, 1500);
    }

    /* ═══════════════════════════════════════════════════════
       PART 2: DRAG FUNCTION (with snap-to-edge)
       ═══════════════════════════════════════════════════════ */
    var STORAGE_KEY = 'pn_bell_pos_v4';
    var DRAG_THRESHOLD = 5;
    var EDGE = 14;
    var SNAP_DURATION = 300;

    var dragging = false;
    var wasDragged = false;
    var startX = 0, startY = 0, startLeft = 0, startTop = 0;
    var activePointerId = null;

    function getSize() {
        var r = bell.getBoundingClientRect();
        return { w: r.width || 40, h: r.height || 40 };
    }

    function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }

    function setPos(left, top, animate) {
        var s = getSize();
        left = clamp(left, EDGE, window.innerWidth - s.w - EDGE);
        top = clamp(top, EDGE, window.innerHeight - s.h - EDGE);

        if (animate) {
            bell.style.setProperty('transition', 'left ' + SNAP_DURATION + 'ms cubic-bezier(0.34, 1.3, 0.64, 1), top ' + SNAP_DURATION + 'ms cubic-bezier(0.34, 1.3, 0.64, 1)', 'important');
        } else {
            bell.style.setProperty('transition', 'none', 'important');
        }

        bell.style.setProperty('position', 'fixed', 'important');
        bell.style.setProperty('left', left + 'px', 'important');
        bell.style.setProperty('top', top + 'px', 'important');
        bell.style.setProperty('right', 'auto', 'important');
        bell.style.setProperty('bottom', 'auto', 'important');
        bell.style.setProperty('margin', '0', 'important');
        return { left: left, top: top };
    }

    function snapToEdge(left, top) {
        var s = getSize();
        var screenW = window.innerWidth;
        var centerX = left + s.w / 2;
        var snapLeft = (centerX < screenW / 2) ? EDGE : (screenW - s.w - EDGE);
        var snapTop = clamp(top, EDGE, window.innerHeight - s.h - EDGE);
        setPos(snapLeft, snapTop, true);
        return { left: snapLeft, top: snapTop };
    }

    function savePos(l, t) { try { localStorage.setItem(STORAGE_KEY, JSON.stringify({ left: l, top: t })); } catch(e){} }
    function loadPos() { try { var d = localStorage.getItem(STORAGE_KEY); return d ? JSON.parse(d) : null; } catch(e){ return null; } }

    /* Restore saved position */
    var saved = loadPos();
    if (saved && typeof saved.left === 'number' && typeof saved.top === 'number') {
        setPos(saved.left, saved.top, false);
    } else {
        var s = getSize();
        setPos(window.innerWidth - s.w - 18, 16, false);
    }

    /* ═══ POINTER DOWN ═══ */
    function onPointerDown(e) {
        if (e.button !== undefined && e.button !== 0 && e.pointerType === 'mouse') return;

        activePointerId = e.pointerId;
        var curLeft = parseFloat(bell.style.left);
        var curTop = parseFloat(bell.style.top);

        if (isNaN(curLeft) || isNaN(curTop)) {
            var rect = bell.getBoundingClientRect();
            curLeft = rect.left;
            curTop = rect.top;
        }

        startX = e.clientX;
        startY = e.clientY;
        startLeft = curLeft;
        startTop = curTop;

        dragging = true;
        wasDragged = false;

        bell.classList.add('dragging');
        try { bell.setPointerCapture(e.pointerId); } catch(err) {}
    }

    /* ═══ POINTER MOVE ═══ */
    function onPointerMove(e) {
        if (!dragging) return;
        if (activePointerId !== null && e.pointerId !== activePointerId) return;

        var dx = e.clientX - startX;
        var dy = e.clientY - startY;

        if (!wasDragged) {
            if (Math.abs(dx) > DRAG_THRESHOLD || Math.abs(dy) > DRAG_THRESHOLD) {
                wasDragged = true;
            } else {
                return;
            }
        }

        e.preventDefault();
        setPos(startLeft + dx, startTop + dy, false);
    }

    /* ═══ POINTER UP ═══ */
    function onPointerUp(e) {
        if (!dragging) return;
        if (activePointerId !== null && e.pointerId !== activePointerId) return;

        dragging = false;
        activePointerId = null;
        bell.classList.remove('dragging');

        try { bell.releasePointerCapture(e.pointerId); } catch(err) {}

        if (wasDragged) {
            var l = parseFloat(bell.style.left);
            var t = parseFloat(bell.style.top);
            if (!isNaN(l) && !isNaN(t)) {
                var snapped = snapToEdge(l, t);
                setTimeout(function() { savePos(snapped.left, snapped.top); }, SNAP_DURATION + 30);
            }
        }
    }

    /* ═══════════════════════════════════════════════════════
       CLICK HANDLER — buksan ang panel
       (Gumagana lang kung HINDI nag-drag)
       ═══════════════════════════════════════════════════════ */
    bell.addEventListener('click', function(e) {
        /* Kung nag-drag — wag buksan ang panel */
        if (wasDragged) {
            e.preventDefault();
            e.stopPropagation();
            wasDragged = false;
            return false;
        }

        /* Kung CLICK lang (walang drag) — buksan ang panel */
        e.preventDefault();
        e.stopPropagation();

        if (!panel) return;

        isPanelOpen = !isPanelOpen;
        panel.classList.toggle('open', isPanelOpen);

        if (isPanelOpen) {
            fetchNotifications();
        }
    });

    /* ═══ ATTACH ═══ */
    bell.addEventListener('pointerdown', onPointerDown, { passive: false });
    bell.addEventListener('pointermove', onPointerMove, { passive: false });
    bell.addEventListener('pointerup', onPointerUp);
    bell.addEventListener('pointercancel', onPointerUp);

    /* ═══ DOUBLE-TAP TO RESET ═══ */
    var lastTap = 0;
    bell.addEventListener('pointerup', function() {
        var now = Date.now();
        if (now - lastTap < 350 && !wasDragged) {
            try { localStorage.removeItem(STORAGE_KEY); } catch(err) {}
            var sz = getSize();
            var resetLeft = window.innerWidth - sz.w - EDGE;
            setPos(resetLeft, 16, true);
            savePos(resetLeft, 16);
        }
        lastTap = now;
    });

    /* ═══ RESIZE ═══ */
    window.addEventListener('resize', function() {
        var l = parseFloat(bell.style.left);
        var t = parseFloat(bell.style.top);
        if (!isNaN(l) && !isNaN(t)) setPos(l, t, false);
    });
})();
</script>








<script>
(function() {
    /* ═══ HAMBURGER — SHOW ON ANY SCROLL ═══ */
    var hamburger = document.getElementById('pHamburger');
    if (!hamburger) return;

    var idleTimer = null;
    var IDLE_DELAY = 1500;

    function showHamburger() {
        hamburger.classList.remove('hide');
        clearTimeout(idleTimer);
        idleTimer = setTimeout(hideHamburger, IDLE_DELAY);
    }

    function hideHamburger() {
        var sidebar = document.getElementById('pSidebar');
        if (sidebar && sidebar.classList.contains('open')) return;
        hamburger.classList.add('hide');
    }

    /* Initial — visible, then auto-hide after 1.5s */
    hamburger.classList.remove('hide');
    clearTimeout(idleTimer);
    idleTimer = setTimeout(hideHamburger, IDLE_DELAY);

    /* ═══ SCROLL LISTENERS — catch any movement ═══ */
    window.addEventListener('scroll', showHamburger, { passive: true });
    document.addEventListener('scroll', showHamburger, { passive: true, capture: true });

    /* Also listen sa touch + wheel para sigurado */
    document.addEventListener('touchmove', showHamburger, { passive: true });
    document.addEventListener('wheel', showHamburger, { passive: true });
    document.addEventListener('touchstart', showHamburger, { passive: true });
    document.addEventListener('mousedown', showHamburger, { passive: true });

    /* Keep visible kapag bukas ang sidebar */
    hamburger.addEventListener('click', function() {
        showHamburger();
        clearTimeout(idleTimer);
    });

    /* Show pagkatapos isara ang sidebar */
    var sidebarOverlay = document.getElementById('pSidebarOverlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', showHamburger);
    }
})();
</script>

</body>
</html>