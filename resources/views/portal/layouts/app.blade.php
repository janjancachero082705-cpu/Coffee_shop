<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2D1A11">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light only">
    <title>@yield('title', 'Portal') - RYS Farm Products Dealer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">
    
    
    <style>
        /* ═══════════════════════════════════════════════════════
           CLEAN WHITE + YELLOW — PRO COFFEE APP DESIGN
           Based on reference: pure white bg, golden yellow buttons
           ═══════════════════════════════════════════════════════ */
        :root {
            color-scheme: light only;

            /* Brand — Golden Yellow */
            --yellow: #E8A33D;
            --yellow-dark: #D4901F;
            --yellow-light: #F0B85A;
            --yellow-soft: #FFF8EC;
            --yellow-tint: #FFFCF5;

            /* Neutrals */
            --white: #FFFFFF;
            --bg-body: #FFFFFF;
            --bg-card: #FFFFFF;
            --bg-subtle: #F7F7F7;
            --bg-gray: #FAFAFA;

            /* Text */
            --text-main: #1A1A1A;
            --text-body: #333333;
            --text-muted: #757575;
            --text-light: #9E9E9E;

            /* Borders */
            --border-color: #EEEEEE;
            --border-light: #F5F5F5;

            /* Semantic */
            --success: #22C55E;
            --success-soft: #F0FDF4;
            --warning: #F59E0B;
            --warning-soft: #FFFBEB;
            --danger: #EF4444;
            --danger-soft: #FEF2F2;

            /* Radii */
            --radius-xl: 24px;
            --radius-lg: 18px;
            --radius-md: 14px;
            --radius-sm: 10px;

            /* Shadows — soft and subtle */
            --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 6px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.12);
            --shadow-yellow: 0 4px 14px rgba(232, 163, 61, 0.35);
            --shadow-yellow-hover: 0 8px 24px rgba(232, 163, 61, 0.45);
            --shadow-focus: 0 0 0 4px rgba(232, 163, 61, 0.18);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            background: #FFFFFF;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #FFFFFF;
            color: var(--text-body);
            font-size: 15px;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
            background: #FFFFFF;
        }

        /* ===== TOPBAR ===== */
        .p-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #FFFFFF;
            padding: 18px 20px 18px 76px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .p-topbar.hide { transform: translateY(-120%); }

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
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        .p-topbar-store-code {
            font-size: 11px;
            color: var(--yellow-dark);
            font-family: ui-monospace, 'SF Mono', monospace;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        /* ===== MAIN ===== */
        .p-main {
            padding: 20px 18px 120px;
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== CARDS ===== */
        .p-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 12px;
            box-shadow: var(--shadow-xs);
            transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
            color: var(--text-main);
            position: relative;
        }
        .p-card:hover {
            box-shadow: var(--shadow-md);
            border-color: transparent;
            transform: translateY(-2px);
        }

        .p-card-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .p-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--yellow-soft);
            display: grid;
            place-items: center;
            color: var(--yellow-dark);
            flex-shrink: 0;
            font-size: 22px;
            transition: transform 0.25s cubic-bezier(0.34, 1.4, 0.64, 1);
        }
        .p-card:hover .p-card-icon { transform: scale(1.06); }

        .p-card-icon.green { background: var(--success-soft); color: var(--success); }
        .p-card-icon.blue  { background: #EFF6FF; color: #2563EB; }
        .p-card-icon.amber { background: var(--warning-soft); color: var(--warning); }
        .p-card-icon.red   { background: var(--danger-soft); color: var(--danger); }

        .p-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.02em;
            line-height: 1.3;
        }
        .p-card-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
            font-weight: 500;
            line-height: 1.4;
        }

        /* ===== BUTTONS — SOLID YELLOW ===== */
        .p-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            border-radius: 100px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.01em;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1.5px solid transparent;
            min-height: 48px;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            white-space: nowrap;
        }
        .p-btn:active { transform: scale(0.97); }

        .p-btn-primary {
            background: var(--yellow);
            color: #FFFFFF;
            box-shadow: var(--shadow-yellow);
        }
        .p-btn-primary:hover {
            background: var(--yellow-dark);
            box-shadow: var(--shadow-yellow-hover);
            transform: translateY(-1px);
        }
        .p-btn-primary:active {
            transform: translateY(0) scale(0.97);
            box-shadow: 0 2px 8px rgba(232, 163, 61, 0.3);
        }

        .p-btn-danger {
            background: var(--danger);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .p-btn-danger:hover { background: #DC2626; }

        .p-btn-ghost {
            background: #FFFFFF;
            color: var(--text-main);
            border-color: var(--border-color);
            box-shadow: var(--shadow-xs);
        }
        .p-btn-ghost:hover {
            background: var(--bg-subtle);
            border-color: #E0E0E0;
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .p-btn-block { width: 100%; padding: 16px; }
        .p-btn-sm {
            padding: 10px 18px;
            font-size: 13px;
            min-height: 40px;
        }

        /* ===== BADGES ===== */
        .p-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }
        .p-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }
        .p-badge-pending  { background: var(--warning-soft); color: #B45309; }
        .p-badge-approved,
        .p-badge-active,
        .p-badge-paid,
        .p-badge-completed { background: var(--success-soft); color: #15803D; }
        .p-badge-rejected,
        .p-badge-cancelled { background: var(--danger-soft); color: #B91C1C; }
        .p-badge-partial  { background: #EFF6FF; color: #1D4ED8; }

        /* ===== FORMS ===== */
        .p-input, .p-select, .p-textarea {
            width: 100%;
            padding: 14px 16px;
            background: var(--bg-gray);
            border: 1.5px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-main);
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: all 0.15s ease;
            font-weight: 500;
            min-height: 48px;
        }
        .p-input::placeholder,
        .p-textarea::placeholder { color: var(--text-light); }
        .p-input:hover, .p-select:hover, .p-textarea:hover {
            background: #F5F5F5;
        }
        .p-input:focus, .p-select:focus, .p-textarea:focus {
            border-color: var(--yellow);
            background: #FFFFFF;
            box-shadow: var(--shadow-focus);
        }
        .p-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        /* ===== ALERTS ===== */
        .p-alert {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid;
            transition: opacity 0.4s ease, transform 0.4s ease,
                        max-height 0.4s ease, margin 0.4s ease,
                        padding 0.4s ease, border-width 0.4s ease;
        }
        .p-alert-success {
            background: var(--success-soft);
            color: #15803D;
            border-left-color: var(--success);
        }
        .p-alert-error {
            background: var(--danger-soft);
            color: #B91C1C;
            border-left-color: var(--danger);
        }
        .p-alert svg { flex-shrink: 0; }
        .p-alert.fade-out {
            opacity: 0;
            transform: translateY(-8px);
            max-height: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border-width: 0 !important;
            overflow: hidden;
        }

        /* ===== FLOATING HAMBURGER ===== */
        .p-float-btn {
            position: fixed;
            z-index: 9997;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #FFFFFF;
            color: var(--text-main);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .p-float-btn:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
        }
        .p-float-btn:active { transform: scale(0.95); }

        .p-float-hamburger {
            position: fixed !important;
            top: 18px !important;
            left: 18px !important;
            width: 44px !important;
            height: 44px !important;
            padding: 0 !important;
            background: #FFFFFF !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            box-shadow: var(--shadow-sm) !important;
            color: var(--text-main) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            z-index: 99999 !important;
            visibility: visible !important;
            opacity: 1;
            transform: translateX(0);
            transition: transform 0.35s cubic-bezier(0.34, 1.2, 0.64, 1),
                        opacity 0.3s ease,
                        box-shadow 0.2s ease,
                        background 0.2s ease;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        .p-float-hamburger:hover {
            background: var(--yellow-soft) !important;
            color: var(--yellow-dark) !important;
            border-color: var(--yellow) !important;
            transform: scale(1.05);
            box-shadow: var(--shadow-md) !important;
        }
        .p-float-hamburger:active { transform: scale(0.95); }
        .p-float-hamburger.hide {
            opacity: 0 !important;
            transform: translateX(-150%) !important;
            pointer-events: none !important;
            visibility: hidden !important;
        }
        .p-float-hamburger svg {
            display: block !important;
            width: 22px !important;
            height: 22px !important;
            stroke: currentColor !important;
            stroke-width: 2.2 !important;
        }

        /* ===== SIDEBAR ===== */
        .p-sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s;
        }
        .p-sidebar-overlay.open { opacity: 1; visibility: visible; }

        .p-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 85%;
            max-width: 320px;
            background: #FFFFFF;
            z-index: 9999;
            transform: translateX(-100%);
            transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1);
            display: flex;
            flex-direction: column;
            box-shadow: 12px 0 40px rgba(0, 0, 0, 0.1);
        }
        .p-sidebar.open { transform: translateX(0); }

        .p-sidebar-head {
            padding: 24px 20px;
            background: #FFFFFF;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
        }
        .p-sidebar-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text-main);
        }
        .p-sidebar-sub {
            font-size: 11px;
            color: var(--yellow-dark);
            font-weight: 700;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .p-sidebar-close {
            color: var(--text-muted);
            padding: 8px;
            border-radius: var(--radius-sm);
            transition: all 0.15s;
            display: grid;
            place-items: center;
        }
        .p-sidebar-close:hover {
            background: var(--bg-subtle);
            color: var(--text-main);
        }

        .p-sidebar-nav { flex: 1; padding: 20px 16px; overflow-y: auto; }

        .p-sidebar-section-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 8px;
            margin-top: 20px;
            padding-left: 14px;
        }
        .p-sidebar-section:first-child .p-sidebar-section-title { margin-top: 0; }

        .p-sidebar-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 14px;
            border-radius: var(--radius-md);
            color: var(--text-body);
            font-weight: 600;
            font-size: 15px;
            transition: all 0.15s ease;
            margin-bottom: 2px;
        }
        .p-sidebar-link svg { color: var(--text-muted); transition: color 0.15s; }
        .p-sidebar-link:hover {
            background: var(--bg-subtle);
            color: var(--text-main);
        }
        .p-sidebar-link:hover svg { color: var(--text-main); }
        .p-sidebar-link.active {
            background: var(--yellow);
            color: #FFFFFF;
            font-weight: 700;
            box-shadow: var(--shadow-yellow);
        }
        .p-sidebar-link.active svg { color: #FFFFFF; }

        .p-sidebar-foot {
            padding: 16px;
            border-top: 1px solid var(--border-color);
            background: #FFFFFF;
        }
        .p-sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            padding: 14px;
            background: var(--bg-gray);
            border-radius: var(--radius-md);
        }
        .p-sidebar-user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--yellow);
            color: #FFFFFF;
            display: grid;
            place-items: center;
            font-weight: 800;
            flex-shrink: 0;
            font-size: 15px;
            overflow: hidden;
        }
        .p-sidebar-user-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.3;
        }
        .p-sidebar-user-email {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 1px;
        }

        .p-sidebar-foot-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            border-radius: var(--radius-md);
            color: var(--text-body);
            font-weight: 600;
            font-size: 15px;
            width: 100%;
            text-align: left;
            transition: all 0.15s;
        }
        .p-sidebar-foot-link svg { color: var(--text-muted); }
        .p-sidebar-foot-link:hover {
            background: var(--bg-subtle);
            color: var(--text-main);
        }
        .p-sidebar-foot-link.danger { color: var(--danger); }
        .p-sidebar-foot-link.danger svg { color: var(--danger); }
        .p-sidebar-foot-link.danger:hover { background: var(--danger-soft); }

        /* ===== MODAL ===== */
        .p-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 9999;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s;
        }
        .p-modal-overlay.open { opacity: 1; visibility: visible; }

        .p-modal {
            background: #FFFFFF;
            width: 100%;
            max-width: 640px;
            max-height: 90vh;
            border-radius: 28px 28px 0 0;
            padding: 20px 22px 32px;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.32, 0.72, 0, 1);
            color: var(--text-main);
        }
        .p-modal-overlay.open .p-modal { transform: translateY(0); }

        .p-modal::before {
            content: '';
            display: block;
            width: 40px;
            height: 4px;
            background: var(--border-color);
            border-radius: 100px;
            margin: 0 auto 20px;
        }

        .p-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        .p-modal-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text-main);
        }
        .p-modal-close {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-subtle);
            display: grid;
            place-items: center;
            color: var(--text-muted);
            transition: all 0.15s;
        }
        .p-modal-close:hover {
            background: var(--yellow-soft);
            color: var(--yellow-dark);
            transform: rotate(90deg);
        }

        /* ===== NOTIFICATION BELL ===== */
        .pn-bell {
            position: fixed;
            top: 18px;
            right: 18px;
            width: 44px;
            height: 44px;
            padding: 0;
            border-radius: 12px;
            background: #FFFFFF;
            color: var(--text-main);
            display: grid;
            place-items: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            z-index: 9998;
            cursor: grab;
            transition: all 0.2s cubic-bezier(0.34, 1.4, 0.64, 1);
            -webkit-tap-highlight-color: transparent;
            overflow: visible;
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }
        .pn-bell:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: var(--shadow-md);
            background: var(--yellow-soft);
            color: var(--yellow-dark);
            border-color: var(--yellow);
        }
        .pn-bell:active { transform: translateY(0) scale(0.96); }
        .pn-bell.dragging {
            cursor: grabbing;
            transition: none !important;
            transform: scale(1.1) !important;
            box-shadow: var(--shadow-lg) !important;
        }
        .pn-bell.no-transition { transition: none !important; }

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

        .pn-bell.has-unread {
            color: var(--yellow-dark);
            border-color: var(--yellow);
            background: var(--yellow-soft);
        }
        .pn-bell.has-unread .pn-bell-icon {
            animation: pnBellShake 2.5s ease-in-out infinite;
            transform-origin: 50% 0%;
        }
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
        .pn-bell.has-unread::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 12px;
            border: 2px solid var(--yellow);
            animation: pnPulse 2s ease-out infinite;
            pointer-events: none;
        }
        @keyframes pnPulse {
            0% { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(1.15); opacity: 0; }
        }
        .pn-bell.has-unread:hover .pn-bell-icon { animation-play-state: paused; }

        .pn-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--danger);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            font-family: 'Inter', system-ui, sans-serif;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 100px;
            display: grid;
            place-items: center;
            border: 2.5px solid #FFFFFF;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35);
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
            top: 74px;
            right: 14px;
            width: calc(100vw - 36px);
            max-width: 380px;
            background: #FFFFFF;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
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
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .pn-panel.open { display: flex; }

        .pn-head {
            padding: 16px 20px;
            background: #FFFFFF;
            color: var(--text-main);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }
        .pn-head-title {
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-main);
        }
        .pn-head-title svg { color: var(--yellow-dark); }
        .pn-mark-all {
            font-size: 12px;
            color: var(--yellow-dark);
            font-weight: 700;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            transition: background 0.15s;
            cursor: pointer;
        }
        .pn-mark-all:hover { background: var(--yellow-soft); }

        .pn-body { max-height: 400px; overflow-y: auto; padding: 8px; }

        .pn-item {
            display: flex;
            gap: 12px;
            padding: 14px;
            border-radius: var(--radius-md);
            transition: background 0.15s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .pn-item:hover { background: var(--bg-subtle); }
        .pn-item.unread { background: var(--yellow-soft); }
        .pn-item.unread:hover { background: var(--yellow-tint); }

        .pn-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .pn-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.3;
        }
        .pn-message {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
            font-weight: 500;
            line-height: 1.4;
        }
        .pn-time {
            font-size: 11px;
            color: var(--text-light);
            margin-top: 6px;
            font-weight: 600;
        }

        /* ===== SCROLLBAR ===== */
        * { scrollbar-width: thin; scrollbar-color: #D4D4D4 transparent; }
        *::-webkit-scrollbar { width: 8px; height: 8px; }
        *::-webkit-scrollbar-track { background: transparent; }
        *::-webkit-scrollbar-thumb {
            background: #D4D4D4;
            border-radius: 4px;
        }
        *::-webkit-scrollbar-thumb:hover { background: #A3A3A3; }

        /* ===== FIXED POSITIONING ===== */
        body > .p-float-hamburger,
        body > .pn-bell,
        body > .pn-panel { position: fixed; }
        body > .p-float-hamburger { top: 18px; left: 18px; z-index: 9997; }
        body > .pn-bell { top: 18px; right: 18px; z-index: 9998; }
        body > .pn-panel { top: 74px; right: 14px; z-index: 9999; }

        .pn-bell svg,
        .pn-bell .pn-badge,
        .pn-bell-inner { pointer-events: none !important; }
        .pn-bell { pointer-events: auto !important; }

        .portal-wrap { contain: none !important; isolation: isolate; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .p-topbar { padding: 16px 16px 16px 72px; }
            .p-topbar-store-name { font-size: 16px; }
            .p-main { padding: 18px 16px 120px; }
            .p-float-hamburger { top: 16px !important; left: 16px !important; }
            .pn-bell { top: 16px; right: 16px; }
            .pn-panel { top: 70px; left: 12px; right: 12px; width: auto; }
            .p-card { padding: 18px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        @media (hover: none) {
            .p-card:hover { transform: none !important; }
            .p-btn-primary:hover { transform: none !important; }
            .p-float-hamburger:hover { transform: none !important; }
        }

        /* ===== FOOTER ===== */
        .portal-wrap footer {
            color: var(--text-muted) !important;
            font-weight: 600;
            font-size: 12px !important;
            letter-spacing: 0.05em;
        }
    
        /* ═══ BRAND LOGO ═══ */
        .p-sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .p-sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
        .p-topbar-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
        @media (max-width: 480px) {
            .p-sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
            .p-topbar-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
        }

        /* ═══ BRAND LOGO ═══ */
        .p-sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .p-sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
        .p-topbar-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
        @media (max-width: 480px) {
            .p-sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
            .p-topbar-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: transparent;
            padding: 0;
            border: 1px solid #EEEEEE;
        }
        }

        /* ═══ GLOBAL TOAST ═══ */
        .toast-wrap { position: fixed; top: 18px; right: 18px; display: flex; align-items: center; gap: 12px; padding: 14px 18px 14px 16px; background: #FFFFFF; border: 1px solid #EEEEEE; border-radius: 14px; box-shadow: 0 12px 32px -10px rgba(0,0,0,0.15), 0 4px 12px -4px rgba(0,0,0,0.06); z-index: 999999; min-width: 280px; max-width: 380px; overflow: hidden; pointer-events: none; transform: translateX(120%); opacity: 0; transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), opacity 0.25s ease; }
        .toast-wrap.show { transform: translateX(0); opacity: 1; }
        .toast-wrap.hide { transform: translateX(-120%); opacity: 0; transition: transform 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.35s ease; }
        .toast-icon { width: 40px; height: 40px; border-radius: 11px; display: grid; place-items: center; flex-shrink: 0; border: 1px solid; }
        .toast-success .toast-icon { background: #FFF8EC; color: #E8A33D; border-color: #F0E0C0; }
        .toast-error .toast-icon { background: #FFEBEE; color: #DC2626; border-color: #FFCDD2; }
        .toast-info .toast-icon { background: #EFF6FF; color: #2563EB; border-color: #BFDBFE; }
        .toast-body { flex: 1; min-width: 0; }
        .toast-title { font-size: 13px; font-weight: 800; color: #1A1A1A; letter-spacing: -0.01em; line-height: 1.2; margin-bottom: 2px; }
        .toast-msg { font-size: 12px; color: #757575; font-weight: 500; line-height: 1.35; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .toast-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .toast-success .toast-dot { background: #E8A33D; box-shadow: 0 0 8px rgba(232,163,61,0.6); }
        .toast-error .toast-dot { background: #DC2626; box-shadow: 0 0 8px rgba(220,38,38,0.5); }
        .toast-info .toast-dot { background: #2563EB; box-shadow: 0 0 8px rgba(37,99,235,0.5); }
        .toast-wrap::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
        .toast-success::before { background: linear-gradient(90deg, #E8A33D, #F0B85A); }
        .toast-error::before { background: linear-gradient(90deg, #DC2626, #EF4444); }
        .toast-info::before { background: linear-gradient(90deg, #2563EB, #3B82F6); }
        @media (max-width: 480px) {
            .toast-wrap { top: 16px; right: 16px; left: 16px; min-width: 0; max-width: none; }
        }
    
        /* ═══ BRAND STYLING — RYS FARM PRODUCTS DEALER ═══ */
        .p-sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .p-sidebar-logo {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            object-fit: contain;
            flex-shrink: 0;
            background: #FFFFFF;
            padding: 4px;
            border: 1.5px solid #EEEEEE;
            box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.08);
        }
        .p-sidebar-brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }
        .p-sidebar-brand-name {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.04em;
            color: #1A1A1A;
            line-height: 1;
            text-transform: uppercase;
        }
        .p-sidebar-brand-line {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #1A1A1A;
            line-height: 1.2;
            margin-top: 1px;
        }
        .p-sidebar-brand-tag {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.14em;
            color: #D4901F;
            text-transform: uppercase;
            line-height: 1;
            margin-top: 3px;
            padding: 3px 6px;
            background: #FFF8EC;
            border-radius: 4px;
            display: inline-block;
            width: fit-content;
        }
    
        /* ═══ BRAND STYLING — RYS FARM PRODUCTS DEALER (Logo Style) ═══ */
        .p-sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .p-sidebar-logo {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            object-fit: contain;
            flex-shrink: 0;
            background: #FFFFFF;
            padding: 4px;
            border: 1.5px solid #EEEEEE;
            box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.08);
        }
        .p-sidebar-brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }

        /* ═══ RYS — Cinzel Black (Roman serif, tulad ng logo) ═══ */
        .p-sidebar-brand-name {
            font-family: 'Cinzel', 'Playfair Display', Georgia, serif;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 0.06em;
            color: #5D4037;
            line-height: 1;
            text-transform: uppercase;
            text-shadow: 0 1px 0 rgba(212, 144, 31, 0.15);
        }

        /* ═══ FARM PRODUCTS DEALER — Marcellus (Roman serif) ═══ */
        .p-sidebar-brand-line {
            font-family: 'Marcellus', 'Cinzel', Georgia, serif;
            font-size: 10px;
            font-weight: 400;
            letter-spacing: 0.08em;
            color: #5D4037;
            line-height: 1.3;
            margin-top: 2px;
            text-transform: uppercase;
        }

        /* ═══ WHOLESALE & RETAILER — Small caps style ═══ */
        .p-sidebar-brand-tag {
            font-family: 'Marcellus', 'Cinzel', Georgia, serif;
            font-size: 8.5px;
            font-weight: 400;
            letter-spacing: 0.16em;
            color: #FFFFFF;
            text-transform: uppercase;
            line-height: 1;
            margin-top: 4px;
            padding: 4px 8px;
            background: linear-gradient(135deg, #5D4037 0%, #4E342E 100%);
            border-radius: 3px;
            display: inline-block;
            width: fit-content;
            box-shadow: 0 2px 4px rgba(78, 52, 46, 0.2);
        }
    
/* ═══════════════════════════════════════════════ */
/* COMPACT-NAV-OVERRIDE                            */
/* Icon-focused nav links — smaller text labels   */
/* ═══════════════════════════════════════════════ */

/* Nav links — reduced size */
.p-sidebar-nav .p-sidebar-link {
    padding: 10px 12px;
    gap: 12px;
    font-size: 13px;
    font-weight: 600;
    min-height: 42px;
}

.p-sidebar-nav .p-sidebar-link svg {
    width: 18px;
    height: 18px;
    color: #B0B0B0;
    transition: color 0.15s;
}

.p-sidebar-nav .p-sidebar-link span {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: -0.005em;
    color: var(--text-body);
}

/* Active state — mas compact */
.p-sidebar-nav .p-sidebar-link.active {
    padding: 10px 12px;
    font-size: 13px;
}

.p-sidebar-nav .p-sidebar-link.active svg {
    color: #FFFFFF;
}

/* Section titles — mas subtle */
.p-sidebar-nav .p-sidebar-section-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.14em;
    color: #B0B0B0;
    margin-top: 16px;
    margin-bottom: 6px;
    padding-left: 12px;
}

/* Sidebar footer links — keep text, slightly smaller */
.p-sidebar-foot .p-sidebar-foot-link {
    padding: 11px 14px;
    font-size: 14px;
    gap: 12px;
}

.p-sidebar-foot .p-sidebar-foot-link svg {
    width: 18px;
    height: 18px;
}

/* ═══ SIDEBAR WIDTH — mas compact ═══ */
.p-sidebar {
    max-width: 300px;
}

/* ═══ BRAND — keep full size (logo + name) ═══ */
.p-sidebar-head {
    padding: 22px 18px;
}

.p-sidebar-brand-name {
    font-size: 22px; /* keep prominent */
}

.p-sidebar-brand-line {
    font-size: 9.5px;
}

.p-sidebar-brand-tag {
    font-size: 8px;
}

/* ═══ MOBILE RESPONSIVE ═══ */
@media (max-width: 480px) {
    .p-sidebar-nav .p-sidebar-link {
        padding: 9px 10px;
        gap: 10px;
        font-size: 12.5px;
    }
    .p-sidebar-nav .p-sidebar-link svg {
        width: 17px;
        height: 17px;
    }
    .p-sidebar-nav .p-sidebar-link span {
        font-size: 12.5px;
    }
    .p-sidebar-section-title {
        font-size: 9.5px;
        padding-left: 10px;
    }
}

/* ═══════════════════════════════════════════════ */
/* COMPACT-SIDEBAR-V2                              */
/* Minimize nav + profile + logout + email         */
/* Keep logo + business name at full size          */
/* ═══════════════════════════════════════════════ */

/* ═══ NAV LINKS — smaller ═══ */
.p-sidebar-nav .p-sidebar-link {
    padding: 9px 11px;
    gap: 11px;
    font-size: 12.5px;
    font-weight: 600;
    min-height: 40px;
    border-radius: 10px;
}

.p-sidebar-nav .p-sidebar-link svg {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
}

.p-sidebar-nav .p-sidebar-link span {
    font-size: 12.5px;
    font-weight: 600;
    letter-spacing: -0.005em;
    line-height: 1.2;
}

/* ═══ SECTION TITLES — smaller ═══ */
.p-sidebar-nav .p-sidebar-section-title {
    font-size: 9.5px;
    font-weight: 800;
    letter-spacing: 0.16em;
    margin-top: 14px;
    margin-bottom: 5px;
    padding-left: 11px;
    opacity: 0.7;
}

.p-sidebar-nav .p-sidebar-section:first-child .p-sidebar-section-title {
    margin-top: 0;
}

/* ═══ USER CARD (avatar + name + email) ═══ */
.p-sidebar-foot .p-sidebar-user {
    padding: 10px 12px;
    gap: 10px;
    margin-bottom: 10px;
    border-radius: 12px;
}

.p-sidebar-foot .p-sidebar-user-avatar {
    width: 36px;
    height: 36px;
    font-size: 12px;
}

.p-sidebar-foot .p-sidebar-user-name {
    font-size: 12.5px;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.p-sidebar-foot .p-sidebar-user-email {
    font-size: 10.5px;
    font-weight: 500;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    opacity: 0.7;
}

/* ═══ PROFILE + LOGOUT BUTTONS — smaller ═══ */
.p-sidebar-foot .p-sidebar-foot-link {
    padding: 9px 11px;
    gap: 11px;
    font-size: 12.5px;
    font-weight: 600;
    min-height: 40px;
    border-radius: 10px;
    margin-bottom: 2px;
}

.p-sidebar-foot .p-sidebar-foot-link svg {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
}

.p-sidebar-foot .p-sidebar-foot-link span {
    font-size: 12.5px;
    font-weight: 600;
}

/* ═══ SIDEBAR PADDING — tighter ═══ */
.p-sidebar-nav {
    padding: 14px 12px;
}

.p-sidebar-foot {
    padding: 12px;
}

/* ═══ BRAND — keep prominent (logo + name) ═══ */
.p-sidebar-head {
    padding: 20px 18px;
}

.p-sidebar-brand-name {
    font-size: 24px;
    font-weight: 900;
    letter-spacing: 0.06em;
}

.p-sidebar-brand-line {
    font-size: 10px;
    letter-spacing: 0.08em;
}

.p-sidebar-brand-tag {
    font-size: 8.5px;
    letter-spacing: 0.16em;
    padding: 4px 8px;
}

.p-sidebar-logo {
    width: 52px;
    height: 52px;
}

/* ═══ MOBILE — extra compact ═══ */
@media (max-width: 480px) {
    .p-sidebar-nav .p-sidebar-link {
        padding: 8px 10px;
        gap: 10px;
        font-size: 12px;
        min-height: 38px;
    }
    .p-sidebar-nav .p-sidebar-link svg {
        width: 16px;
        height: 16px;
    }
    .p-sidebar-nav .p-sidebar-link span {
        font-size: 12px;
    }
    .p-sidebar-section-title {
        font-size: 9px;
        padding-left: 10px;
    }
    .p-sidebar-foot .p-sidebar-foot-link {
        padding: 8px 10px;
        font-size: 12px;
        min-height: 38px;
    }
    .p-sidebar-foot .p-sidebar-foot-link svg {
        width: 16px;
        height: 16px;
    }
    .p-sidebar-foot .p-sidebar-user {
        padding: 8px 10px;
    }
    .p-sidebar-foot .p-sidebar-user-avatar {
        width: 32px;
        height: 32px;
        font-size: 11px;
    }
    .p-sidebar-foot .p-sidebar-user-name {
        font-size: 12px;
    }
    .p-sidebar-foot .p-sidebar-user-email {
        font-size: 10px;
    }
    .p-sidebar-head {
        padding: 16px 14px;
    }
    .p-sidebar-brand-name {
        font-size: 20px;
    }
    .p-sidebar-brand-line {
        font-size: 9px;
    }
    .p-sidebar-brand-tag {
        font-size: 7.5px;
        padding: 3px 6px;
    }
}

/* ═══════════════════════════════════════════════ */
/* COMPACT-BELL-MOBILE                             */
/* Smaller bell button sa mobile screens           */
/* ═══════════════════════════════════════════════ */

@media (max-width: 768px) {
    /* Bell button — smaller */
    .pn-bell {
        width: 38px;
        height: 38px;
        top: 12px;
        right: 12px;
        border-radius: 10px;
    }

    /* Bell icon — smaller */
    .pn-bell > svg {
        width: 20px !important;
        height: 20px !important;
    }

    /* Notification badge — smaller */
    .pn-bell .pn-badge {
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        font-size: 9px;
        top: -5px;
        right: -5px;
        border-width: 2px;
    }

    /* Panel adjustments */
    .pn-panel {
        top: 58px;
        right: 10px;
        left: 10px;
        width: auto;
        max-width: none;
        border-radius: 14px;
    }

    /* Hamburger — match ang bell size */
    .p-float-hamburger {
        width: 38px !important;
        height: 38px !important;
        top: 12px !important;
        left: 12px !important;
        border-radius: 10px !important;
    }

    .p-float-hamburger svg {
        width: 18px !important;
        height: 18px !important;
    }

    /* Topbar padding adjust */
    .p-topbar {
        padding: 12px 60px 12px 60px !important;
    }

    .p-topbar-store-name {
        font-size: 15px !important;
    }

    .p-topbar-store-code {
        font-size: 10px !important;
    }
}

@media (max-width: 480px) {
    /* Extra small sa gamay nga phone */
    .pn-bell {
        width: 34px;
        height: 34px;
        top: 10px;
        right: 10px;
        border-radius: 9px;
    }

    .pn-bell > svg {
        width: 18px !important;
        height: 18px !important;
    }

    .pn-bell .pn-badge {
        min-width: 14px;
        height: 14px;
        padding: 0 3px;
        font-size: 8px;
        border-width: 1.5px;
    }

    .pn-panel {
        top: 52px;
        right: 8px;
        left: 8px;
    }

    .p-float-hamburger {
        width: 34px !important;
        height: 34px !important;
        top: 10px !important;
        left: 10px !important;
        border-radius: 9px !important;
    }

    .p-float-hamburger svg {
        width: 16px !important;
        height: 16px !important;
    }

    .p-topbar {
        padding: 10px 54px 10px 54px !important;
    }

    .p-topbar-store-name {
        font-size: 14px !important;
    }

    .p-topbar-store-code {
        font-size: 9.5px !important;
    }
}

/* ═══ EXTRA SMALL (iPhone SE, etc.) ═══ */
@media (max-width: 360px) {
    .pn-bell {
        width: 32px;
        height: 32px;
    }

    .pn-bell > svg {
        width: 16px !important;
        height: 16px !important;
    }

    .p-float-hamburger {
        width: 32px !important;
        height: 32px !important;
    }

    .p-float-hamburger svg {
        width: 15px !important;
        height: 15px !important;
    }

    .p-topbar {
        padding: 10px 50px 10px 50px !important;
    }
}

/* ═══════════════════════════════════════════════ */
/* HAMBURGER-POSITION-ADJUST                       */
/* Move hamburger button down for better UX        */
/* ═══════════════════════════════════════════════ */

/* ═══ DESKTOP — move down from top ═══ */
.p-float-hamburger {
    top: 80px !important;
}

/* ═══ TABLET + MOBILE — move down ═══ */
@media (max-width: 768px) {
    .p-float-hamburger {
        top: 80px !important;
    }
}

/* ═══ MOBILE — move down matching bell ═══ */
@media (max-width: 480px) {
    .p-float-hamburger {
        top: 72px !important;
    }
}

/* ═══ EXTRA SMALL ═══ */
@media (max-width: 360px) {
    .p-float-hamburger {
        top: 68px !important;
    }
}

/* ═══════════════════════════════════════════════ */
/* HAMBURGER-SIZE-BIG                              */
/* Bigger hamburger button — easy to tap           */
/* ═══════════════════════════════════════════════ */

/* ═══ DESKTOP + GENERAL — bigger ═══ */
.p-float-hamburger {
    width: 56px !important;
    height: 56px !important;
    border-radius: 14px !important;
    top: 24px !important;
    left: 24px !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1) !important;
}

.p-float-hamburger svg {
    width: 26px !important;
    height: 26px !important;
}

/* ═══ TABLET + MOBILE — bigger pud ═══ */
@media (max-width: 768px) {
    .p-float-hamburger {
        width: 52px !important;
        height: 52px !important;
        border-radius: 13px !important;
        top: 20px !important;
        left: 20px !important;
    }
    .p-float-hamburger svg {
        width: 24px !important;
        height: 24px !important;
    }
}

/* ═══ MOBILE — still bigger ═══ */
@media (max-width: 480px) {
    .p-float-hamburger {
        width: 48px !important;
        height: 48px !important;
        border-radius: 12px !important;
        top: 16px !important;
        left: 16px !important;
    }
    .p-float-hamburger svg {
        width: 22px !important;
        height: 22px !important;
    }
}

/* ═══ EXTRA SMALL ═══ */
@media (max-width: 360px) {
    .p-float-hamburger {
        width: 46px !important;
        height: 46px !important;
        border-radius: 12px !important;
        top: 14px !important;
        left: 14px !important;
    }
    .p-float-hamburger svg {
        width: 21px !important;
        height: 21px !important;
    }
}

/* ═══ TOPBAR PADDING — adjust para dili overlap ═══ */
.p-topbar {
    padding-left: 92px !important;
}

@media (max-width: 768px) {
    .p-topbar {
        padding-left: 84px !important;
    }
}

@media (max-width: 480px) {
    .p-topbar {
        padding-left: 76px !important;
    }
}

@media (max-width: 360px) {
    .p-topbar {
        padding-left: 72px !important;
    }
}

/* ═══════════════════════════════════════════════ */
/* HAMBURGER-SIZE-SMALL                            */
/* Smaller white hamburger button                  */
/* ═══════════════════════════════════════════════ */

/* ═══ DESKTOP + GENERAL — smaller ═══ */
.p-float-hamburger {
    width: 42px !important;
    height: 42px !important;
    border-radius: 11px !important;
    top: 22px !important;
    left: 22px !important;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08) !important;
}

.p-float-hamburger svg {
    width: 20px !important;
    height: 20px !important;
}

/* ═══ TABLET ═══ */
@media (max-width: 768px) {
    .p-float-hamburger {
        width: 40px !important;
        height: 40px !important;
        border-radius: 11px !important;
        top: 18px !important;
        left: 18px !important;
    }
    .p-float-hamburger svg {
        width: 19px !important;
        height: 19px !important;
    }
}

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .p-float-hamburger {
        width: 38px !important;
        height: 38px !important;
        border-radius: 10px !important;
        top: 14px !important;
        left: 14px !important;
    }
    .p-float-hamburger svg {
        width: 18px !important;
        height: 18px !important;
    }
}

/* ═══ EXTRA SMALL ═══ */
@media (max-width: 360px) {
    .p-float-hamburger {
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
        top: 12px !important;
        left: 12px !important;
    }
    .p-float-hamburger svg {
        width: 17px !important;
        height: 17px !important;
    }
}

/* ═══ TOPBAR PADDING — adjust para dili overlap ═══ */
.p-topbar {
    padding-left: 72px !important;
}

@media (max-width: 768px) {
    .p-topbar {
        padding-left: 66px !important;
    }
}

@media (max-width: 480px) {
    .p-topbar {
        padding-left: 60px !important;
    }
}

@media (max-width: 360px) {
    .p-topbar {
        padding-left: 56px !important;
    }
}

/* ═══════════════════════════════════════════════ */
/* HAMBURGER-ICON-BIG                              */
/* Bigger icon inside small hamburger button       */
/* ═══════════════════════════════════════════════ */

/* ═══ DESKTOP — bigger icon, same button ═══ */
.p-float-hamburger svg {
    width: 30px !important;
    height: 30px !important;
    stroke-width: 2 !important;
}

/* ═══ TABLET ═══ */
@media (max-width: 768px) {
    .p-float-hamburger svg {
        width: 28px !important;
        height: 28px !important;
    }
}

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .p-float-hamburger svg {
        width: 26px !important;
        height: 26px !important;
    }
}

/* ═══ EXTRA SMALL ═══ */
@media (max-width: 360px) {
    .p-float-hamburger svg {
        width: 24px !important;
        height: 24px !important;
    }
}

/* ═══════════════════════════════════════════════ */
/* HAMBURGER-MOVE-DOWN-V2                          */
/* Move hamburger button down para align topbar    */
/* ═══════════════════════════════════════════════ */

/* ═══ DESKTOP ═══ */
.p-float-hamburger {
    top: 52px !important;
    left: 22px !important;
}

/* ═══ TABLET ═══ */
@media (max-width: 768px) {
    .p-float-hamburger {
        top: 48px !important;
        left: 18px !important;
    }
}

/* ═══ MOBILE ═══ */
@media (max-width: 480px) {
    .p-float-hamburger {
        top: 42px !important;
        left: 14px !important;
    }
}

/* ═══ EXTRA SMALL ═══ */
@media (max-width: 360px) {
    .p-float-hamburger {
        top: 40px !important;
        left: 12px !important;
    }
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
        &copy; {{ date('Y') }} RYS FARM PRODUCTS DEALER · WHOLESALE &amp; RETAILER
    </footer>
</div>

{{-- ═══ SIDEBAR DRAWER ═══ --}}
@auth('store')
<div class="p-sidebar-overlay" id="pSidebarOverlay" onclick="pToggleSidebar(false)"></div>
<aside class="p-sidebar" id="pSidebar">
    <div class="p-sidebar-head">
        <div class="p-sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" class="p-sidebar-logo" alt="RYS Farm Products Dealer">
            <div class="p-sidebar-brand-text">
                <div class="p-sidebar-brand-name">RYS</div>
                <div class="p-sidebar-brand-line">Farm Products Dealer</div>
                <div class="p-sidebar-brand-tag">Wholesale &amp; Retailer</div>
            </div>
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
            <a href="{{ route('portal.orders.index') }}" class="p-sidebar-link {{ request()->routeIs('portal.orders.index') || request()->routeIs('portal.orders.show') || request()->routeIs('portal.orders.create') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
                <span>Orders</span>
            </a>
            <a href="{{ route('portal.tracking.index') }}" class="p-sidebar-link {{ request()->routeIs('portal.tracking.*') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <span>Tracking</span>
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
                    <a href="${n.url || '#'}" class="pn-item unread" data-id="${n.id}" data-url="${n.url || ''}">
                        <div class="pn-icon" style="background:${n.color}22;color:${n.color};">${n.icon}</div>
                        <div style="flex:1;min-width:0;">
                            <div class="pn-title">${n.title}</div>
                            <div class="pn-message">${n.message}</div>
                            <div class="pn-time">${n.created_at}</div>
                        </div>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:#9E9E9E;flex-shrink:0;align-self:center;"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                `).join('');
            }).catch(() => {});
        }

        // ═══ NOTIFICATION CLICK — MARK READ THEN NAVIGATE ═══
        document.addEventListener('click', function(e) {
            var item = e.target.closest('.pn-item');
            if (!item) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            var id = item.getAttribute('data-id');
            var url = item.getAttribute('data-url');
            
            if (!id) return;
            
            // Mark as read via AJAX, then navigate
            fetch('{{ route("portal.notifications.read", ":id") }}'.replace(':id', id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(function() {
                if (url && url !== '' && url !== '#') {
                    window.location.href = url;
                } else {
                    // Refresh notification list kung walang URL
                    fetchNotifications();
                }
            })
            .catch(function() {
                if (url && url !== '' && url !== '#') {
                    window.location.href = url;
                }
            });
        });

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
                <a href="${n.url || '#'}" class="pn-item unread" data-id="${n.id}" data-url="${n.url || ''}">
                    <div class="pn-icon" style="background:${n.color}22;color:${n.color};">${n.icon}</div>
                    <div style="flex:1;min-width:0;">
                        <div class="pn-title">${n.title}</div>
                        <div class="pn-message">${n.message}</div>
                        <div class="pn-time">${n.created_at}</div>
                    </div>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:#9E9E9E;flex-shrink:0;align-self:center;"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            `).join('');
        }).catch(() => {});
    }

    /* ═══ NOTIFICATION CLICK → MARK READ → NAVIGATE ═══ */
    document.addEventListener('click', function(e) {
        var item = e.target.closest('.pn-item');
        if (!item) return;

        e.preventDefault();
        e.stopPropagation();

        var id = item.getAttribute('data-id');
        var url = item.getAttribute('data-url');

        /* Visual feedback */
        item.style.opacity = '0.6';
        item.style.pointerEvents = 'none';

        /* Mark as read via AJAX (fire-and-forget) */
        if (id) {
            fetch(`{{ route("portal.notifications.read", ":id") }}`.replace(':id', id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                credentials: 'same-origin',
                keepalive: true
            }).catch(function() {});
        }

        /* Navigate AGAD — dili maghulat sa AJAX */
        if (url && url !== '' && url !== '#') {
            window.location.href = url;
        } else {
            /* Fallback: close panel, refresh list */
            var panel = document.getElementById('pnPanel');
            if (panel) panel.classList.remove('open');
            item.style.opacity = '1';
            item.style.pointerEvents = '';
            fetchNotifications();
        }
    });

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


<script>
/* ═══ GLOBAL TOAST NOTIFICATION ═══ */
window.showToast = function(msg, type) {
    type = type || 'success';
    var old = document.getElementById('globalToast');
    if (old) old.remove();

    var icons = {
        success: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>',
        error:   '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>',
        info:    '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>'
    };
    var titles = {
        success: 'Success',
        error:   'Oops!',
        info:    'Info'
    };

    var wrap = document.createElement('div');
    wrap.id = 'globalToast';
    wrap.className = 'toast-wrap toast-' + type;
    wrap.innerHTML =
        '<div class="toast-icon">' + (icons[type] || icons.success) + '</div>' +
        '<div class="toast-body">' +
            '<div class="toast-title">' + titles[type] + '</div>' +
            '<div class="toast-msg">' + msg + '</div>' +
        '</div>' +
        '<div class="toast-dot"></div>';

    document.body.appendChild(wrap);
    requestAnimationFrame(function() {
        requestAnimationFrame(function() { wrap.classList.add('show'); });
    });
    setTimeout(function() {
        wrap.classList.remove('show');
        wrap.classList.add('hide');
        setTimeout(function() { wrap.remove(); }, 400);
    }, 1400);
};
</script>
</body>
</html>













