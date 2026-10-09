<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#0f0f14">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') - Coffee Beans</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f0f14;
            --bg-darker: #0a0a10;
            --bg-card: rgba(34, 34, 44, 0.35);
            --bg-card-hover: rgba(34, 34, 44, 0.5);
            --accent: #a9784a;
            --accent-light: #c9a961;
            --accent-dark: #8a5f36;
            --accent-bg: rgba(169, 120, 74, 0.12);
            --accent-border: rgba(169, 120, 74, 0.25);
            --text-primary: #f5f3f0;
            --text-secondary: #a8a5a0;
            --text-muted: #6b6862;
            --border: rgba(255, 255, 255, 0.06);
            --border-strong: rgba(255, 255, 255, 0.1);
            --success: #22c55e;
            --success-bg: rgba(34, 197, 94, 0.12);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.12);
            --info: #3b82f6;
            --info-bg: rgba(59, 130, 246, 0.12);
            --radius: 14px;
            --radius-sm: 10px;
            --radius-lg: 18px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-primary);
            font-size: 13.5px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            background: #0f0f14;
            min-height: 100vh;
        }

        /* Fixed background layer — dili mo-scroll */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -2;
            background-image: url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            will-change: transform;
            pointer-events: none;
        }

        /* Dark overlay on top of image */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            background: linear-gradient(180deg, rgba(15, 15, 20, 0.72), rgba(10, 10, 16, 0.82));
            pointer-events: none;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; border: none; background: none; color: inherit; }
        svg { display: block; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(169, 120, 74, 0.3); border-radius: 3px; }

        /* ===== LAYOUT ===== */
        .portal-wrap {
            max-width: 640px;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* ===== TOPBAR ===== */
        .p-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(15, 15, 20, 0.85);
            backdrop-filter: blur(24px) saturate(1.4);
            -webkit-backdrop-filter: blur(24px) saturate(1.4);
            border-bottom: 1px solid var(--border);
            padding: 14px 16px;
        }

        .p-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .p-store-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 6px 10px 6px 6px;
            margin: -6px 0 -6px -6px;
            border-radius: 12px;
            transition: all 0.2s;
            flex: 1;
            min-width: 0;
        }
        .p-store-link:hover {
            background: rgba(169, 120, 74, 0.1);
        }
        .p-store-link:active { transform: scale(0.98); }

        .p-store-avatar {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 6px 14px -4px rgba(169, 120, 74, 0.5);
        }
        .p-store-avatar img {
            width: 100%; height: 100%; object-fit: cover;
        }

        .p-store-info { flex: 1; min-width: 0; }
        .p-store-name {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .p-store-code {
            font-size: 10px;
            color: #c9a961;
            font-family: ui-monospace, monospace;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-top: 2px;
        }

        .p-store-arrow {
            color: var(--text-muted);
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .p-store-link:hover .p-store-arrow {
            color: #c9a961;
            transform: translateX(3px);
        }

        .p-topbar-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .p-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(169, 120, 74, 0.1);
            border: 1px solid rgba(169, 120, 74, 0.2);
            color: #c9a961;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.15s;
            flex-shrink: 0;
        }
        .p-icon-btn:hover {
            background: rgba(169, 120, 74, 0.2);
            border-color: rgba(169, 120, 74, 0.4);
        }
        .p-icon-btn:active { transform: scale(0.92); }

        .p-icon-btn.danger {
            background: rgba(239, 68, 68, 0.08);
            border-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        .p-icon-btn.danger:hover {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.4);
        }

        .p-icon-btn.spinning svg {
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ===== NAV TABS ===== */
        .p-nav {
            display: flex;
            gap: 6px;
            padding: 12px 16px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            position: sticky;
            top: 68px;
            z-index: 40;
            background: rgba(15, 15, 20, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .p-nav::-webkit-scrollbar { display: none; }

        .p-nav-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid transparent;
            color: var(--text-secondary);
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }
        .p-nav-item svg { flex-shrink: 0; }
        .p-nav-item:hover {
            background: rgba(169, 120, 74, 0.1);
            color: var(--text-primary);
            border-color: rgba(169, 120, 74, 0.2);
        }
        .p-nav-item.active {
            background: linear-gradient(135deg, rgba(201, 169, 97, 0.9), rgba(138, 95, 54, 0.9));
            color: #fff;
            border-color: transparent;
            box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.6);
        }

        /* ===== MAIN ===== */
        .p-main {
            flex: 1;
            padding: 20px 16px 40px;
        }

        /* ===== PAGE HEAD ===== */
        .p-page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .p-page-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.025em;
            line-height: 1.2;
            margin-bottom: 3px;
        }

        .p-page-sub {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        /* ===== BUTTONS ===== */
        .p-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 11px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .p-btn-primary {
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            color: #fff;
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.7),
                        inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        .p-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px -8px rgba(169, 120, 74, 0.9),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .p-btn-primary:active { transform: translateY(0); }

        .p-btn-ghost {
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-secondary);
            border-color: var(--border-strong);
        }
        .p-btn-ghost:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-primary);
        }

        .p-btn-success {
            background: linear-gradient(135deg, #22c55e, #15803d);
            color: #fff;
            box-shadow: 0 8px 20px -8px rgba(34, 197, 94, 0.6);
        }

        .p-btn-danger {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
        }
        .p-btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .p-btn-block {
            width: 100%;
            padding: 14px;
        }

        .p-btn-sm {
            padding: 8px 14px;
            font-size: 12px;
        }

        /* ===== CARDS ===== */
        .p-card {
            background: rgba(34, 34, 44, 0.35);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .p-card:hover {
            border-color: rgba(169, 120, 74, 0.2);
            background: rgba(34, 34, 44, 0.42);
        }

        .p-card-head {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 16px;
        }

        .p-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(169, 120, 74, 0.12);
            border: 1px solid rgba(169, 120, 74, 0.25);
            display: grid;
            place-items: center;
            color: #c9a961;
            flex-shrink: 0;
        }
        .p-card-icon.green { background: rgba(34, 197, 94, 0.12); border-color: rgba(34, 197, 94, 0.25); color: #22c55e; }
        .p-card-icon.blue  { background: rgba(59, 130, 246, 0.12); border-color: rgba(59, 130, 246, 0.25); color: #3b82f6; }
        .p-card-icon.amber { background: rgba(245, 158, 11, 0.12); border-color: rgba(245, 158, 11, 0.25); color: #f59e0b; }

        .p-card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        .p-card-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .p-card-link {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            border-radius: 8px;
            background: rgba(169, 120, 74, 0.1);
            border: 1px solid rgba(169, 120, 74, 0.2);
            color: #c9a961;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.15s;
        }
        .p-card-link:hover {
            background: rgba(169, 120, 74, 0.2);
            transform: translateX(2px);
        }

        /* ===== ALERTS ===== */
        .p-alert {
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 14px;
            border: 1px solid;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(12px);
        }
        .p-alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border-color: rgba(34, 197, 94, 0.25);
        }
        .p-alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* ===== BADGES ===== */
        .p-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
        }
        .p-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 6px currentColor;
        }
        .p-badge-pending  { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
        .p-badge-approved,
        .p-badge-active,
        .p-badge-paid,
        .p-badge-completed { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
        .p-badge-rejected,
        .p-badge-cancelled { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
        .p-badge-partial  { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }

        /* ===== FORM INPUTS ===== */
        .p-input,
        .p-select,
        .p-textarea {
            width: 100%;
            padding: 12px 14px;
            background: rgba(20, 20, 26, 0.55);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 11px;
            color: var(--text-primary);
            font-size: 13.5px;
            font-family: inherit;
            outline: none;
            transition: all 0.15s;
        }
        .p-input::placeholder,
        .p-textarea::placeholder { color: var(--text-muted); }
        .p-input:focus,
        .p-select:focus,
        .p-textarea:focus {
            border-color: var(--accent);
            background: rgba(20, 20, 26, 0.85);
            box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
        }

        .p-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* ===== EMPTY ===== */
        .p-empty {
            text-align: center;
            padding: 50px 20px;
        }
        .p-empty-icon {
            width: 68px;
            height: 68px;
            margin: 0 auto 16px;
            border-radius: 18px;
            background: rgba(169, 120, 74, 0.1);
            border: 1px solid rgba(169, 120, 74, 0.25);
            display: grid;
            place-items: center;
            color: #c9a961;
        }
        .p-empty-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        .p-empty-text {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        /* ===== TOAST ===== */
        .p-toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            padding: 11px 20px;
            background: rgba(34, 197, 94, 0.95);
            backdrop-filter: blur(12px);
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            border-radius: 12px;
            box-shadow: 0 12px 30px -8px rgba(34, 197, 94, 0.6);
            z-index: 99999;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 7px;
            pointer-events: none;
        }
        .p-toast.show { transform: translateX(-50%) translateY(0); }

        /* ===== FOOTER ===== */
        .p-foot {
            padding: 20px 16px;
            text-align: center;
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .p-main { padding: 16px 14px 32px; }
            .p-page-title { font-size: 20px; }
            .p-topbar { padding: 12px 14px; }
            .p-nav { padding: 10px 14px; }
            .p-nav-item { padding: 8px 13px; font-size: 12px; }
        }
            /* ===== PAGE TRANSITION — SLIDE FROM LEFT ===== */
        .p-main {
            animation: slideInFromLeft 0.45s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
        @keyframes slideInFromLeft {
            0% {
                opacity: 0;
                transform: translateX(-30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Fade in sa topbar + nav */
        .p-topbar {
            animation: fadeInDown 0.4s ease both;
        }
        .p-nav {
            animation: fadeInDown 0.45s ease both;
            animation-delay: 0.05s;
        }
        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-15px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        
        /* ============================================================
           PROFILE MODAL — PREMIUM DESIGN
           ============================================================ */
        .profile-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(8, 8, 12, 0.82);
            backdrop-filter: blur(28px) saturate(1.6);
            -webkit-backdrop-filter: blur(28px) saturate(1.6);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.35s;
        }
        .profile-modal-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .profile-modal {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100%;
            max-width: 620px;
            background: #0f0f14;
            background-image:
                radial-gradient(circle at 100% 0%, rgba(201, 169, 97, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 0% 100%, rgba(34, 197, 94, 0.05) 0%, transparent 40%),
                linear-gradient(180deg, rgba(15, 15, 20, 0.92), rgba(10, 10, 16, 0.96)),
                url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            overflow-y: auto;
            overflow-x: hidden;
            transform: translateX(-100%);
            transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:
                8px 0 60px rgba(0, 0, 0, 0.8),
                inset -1px 0 0 rgba(169, 120, 74, 0.15);
        }
        .profile-modal-overlay.open .profile-modal {
            transform: translateX(0);
        }

        /* Custom scrollbar */
        .profile-modal::-webkit-scrollbar { width: 4px; }
        .profile-modal::-webkit-scrollbar-track { background: transparent; }
        .profile-modal::-webkit-scrollbar-thumb {
            background: rgba(201, 169, 97, 0.3);
            border-radius: 2px;
        }

        /* ===== MODAL HEADER ===== */
        .profile-modal-header {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            background: rgba(15, 15, 20, 0.88);
            backdrop-filter: blur(28px) saturate(1.6);
            -webkit-backdrop-filter: blur(28px) saturate(1.6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 20px -8px rgba(0, 0, 0, 0.6);
        }

        .profile-modal-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(169, 120, 74, 0.12);
            border: 1px solid rgba(169, 120, 74, 0.28);
            color: #c9a961;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }
        .profile-modal-back::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.25), transparent 70%);
            opacity: 0;
            transition: opacity 0.25s;
        }
        .profile-modal-back:hover {
            background: rgba(169, 120, 74, 0.22);
            border-color: rgba(201, 169, 97, 0.5);
            transform: translateX(-4px);
            box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.6);
        }
        .profile-modal-back:hover::before { opacity: 1; }
        .profile-modal-back:active { transform: translateX(-4px) scale(0.95); }
        .profile-modal-back svg { display: block; position: relative; z-index: 1; }

        .profile-modal-title-wrap {
            flex: 1;
            min-width: 0;
        }
        .profile-modal-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .profile-modal-subtitle {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
            font-weight: 500;
        }

        .profile-modal-brand {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            display: grid;
            place-items: center;
            color: #fff;
            box-shadow: 0 6px 14px -4px rgba(169, 120, 74, 0.5),
                        inset 0 1px 0 rgba(255, 255, 255, 0.15);
            flex-shrink: 0;
        }

        /* ===== MODAL BODY ===== */
        .profile-modal-body {
            padding: 24px 20px 40px;
        }

        /* Loading skeleton */
        .profile-skeleton {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .profile-skeleton-hero {
            height: 180px;
            border-radius: 20px;
            background: linear-gradient(90deg,
                rgba(34, 34, 44, 0.4) 0%,
                rgba(34, 34, 44, 0.6) 50%,
                rgba(34, 34, 44, 0.4) 100%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.6s ease-in-out infinite;
        }
        .profile-skeleton-card {
            height: 120px;
            border-radius: 16px;
            background: linear-gradient(90deg,
                rgba(34, 34, 44, 0.4) 0%,
                rgba(34, 34, 44, 0.6) 50%,
                rgba(34, 34, 44, 0.4) 100%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.6s ease-in-out infinite;
        }
        @keyframes skeletonShimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Prevent body scroll */
        body.modal-open {
            overflow: hidden;
        }

        @media (min-width: 720px) {
            .profile-modal {
                left: 50%;
                transform: translateX(-50%) translateX(-100%);
                border-left: 1px solid rgba(169, 120, 74, 0.15);
                border-right: 1px solid rgba(169, 120, 74, 0.15);
            }
            .profile-modal-overlay.open .profile-modal {
                transform: translateX(-50%) translateX(0);
            }
        }

        @media (max-width: 480px) {
            .profile-modal-header { padding: 14px 16px; }
            .profile-modal-body { padding: 18px 16px 32px; }
            .profile-modal-back { width: 38px; height: 38px; }
        }

        /* ============================================================
           PROFILE MODAL — ENHANCED PREMIUM
           ============================================================ */
        .profile-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(8, 8, 12, 0.9);
            backdrop-filter: blur(32px) saturate(1.8);
            -webkit-backdrop-filter: blur(32px) saturate(1.8);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s;
            /* Prevent scroll bleed on mobile */
            overscroll-behavior: contain;
            touch-action: none;
        }
        .profile-modal-overlay.open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        .profile-modal-overlay:not(.open) {
            pointer-events: none;
        }

        .profile-modal {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100%;
            max-width: 620px;
            background: #0f0f14;
            background-image:
                radial-gradient(circle at 100% 0%, rgba(201, 169, 97, 0.14) 0%, transparent 45%),
                radial-gradient(circle at 0% 100%, rgba(34, 197, 94, 0.06) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(169, 120, 74, 0.04) 0%, transparent 70%),
                linear-gradient(180deg, rgba(15, 15, 20, 0.96), rgba(10, 10, 16, 0.98));
            overflow-y: auto;
            overflow-x: hidden;
            transform: translateX(-100%);
            transition: transform 0.5s cubic-bezier(0.34, 1.1, 0.64, 1);
            box-shadow:
                12px 0 80px rgba(0, 0, 0, 0.9),
                inset -1px 0 0 rgba(169, 120, 74, 0.2);
            /* Smooth scrolling on mobile */
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            touch-action: pan-y;
        }
        .profile-modal-overlay.open .profile-modal {
            transform: translateX(0);
        }

        /* Top accent line */
        .profile-modal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(201, 169, 97, 0.6), transparent);
            z-index: 20;
            pointer-events: none;
        }

        .profile-modal::-webkit-scrollbar { width: 4px; }
        .profile-modal::-webkit-scrollbar-thumb {
            background: rgba(201, 169, 97, 0.3);
            border-radius: 2px;
        }

        /* ===== HEADER ===== */
        .profile-modal-header {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            background: linear-gradient(180deg, rgba(15, 15, 20, 0.98), rgba(15, 15, 20, 0.92));
            backdrop-filter: blur(28px) saturate(1.6);
            -webkit-backdrop-filter: blur(28px) saturate(1.6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 24px -8px rgba(0, 0, 0, 0.8);
        }

        .profile-modal-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: rgba(169, 120, 74, 0.12);
            border: 1px solid rgba(169, 120, 74, 0.3);
            color: #c9a961;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
        }
        .profile-modal-back::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.3), transparent 70%);
            opacity: 0;
            transition: opacity 0.25s;
        }
        .profile-modal-back:hover,
        .profile-modal-back:active {
            background: rgba(169, 120, 74, 0.22);
            border-color: rgba(201, 169, 97, 0.55);
            transform: translateX(-4px);
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.7);
        }
        .profile-modal-back:hover::before,
        .profile-modal-back:active::before { opacity: 1; }
        .profile-modal-back svg { display: block; position: relative; z-index: 1; }

        .profile-modal-title-wrap { flex: 1; min-width: 0; }
        .profile-modal-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.015em;
            line-height: 1.2;
        }
        .profile-modal-subtitle {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 3px;
            font-weight: 500;
            letter-spacing: 0.01em;
        }

        .profile-modal-brand {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            display: grid;
            place-items: center;
            color: #fff;
            box-shadow: 0 8px 18px -6px rgba(169, 120, 74, 0.6),
                        inset 0 1px 0 rgba(255, 255, 255, 0.18);
            flex-shrink: 0;
            transition: transform 0.3s;
        }
        .profile-modal-brand:hover {
            transform: rotate(-5deg) scale(1.05);
        }

        /* ===== BODY ===== */
        .profile-modal-body {
            padding: 22px 20px 40px;
            /* Prevent body blur bleed */
            isolation: isolate;
        }

        /* Prevent body scroll when modal open */
        body.modal-open {
            overflow: hidden !important;
            position: fixed;
            width: 100%;
            height: 100%;
        }

        /* ===== LOADING SKELETON ===== */
        .profile-skeleton {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .profile-skeleton-hero {
            height: 200px;
            border-radius: 22px;
            background: linear-gradient(90deg,
                rgba(34, 34, 44, 0.4) 0%,
                rgba(34, 34, 44, 0.7) 50%,
                rgba(34, 34, 44, 0.4) 100%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.6s ease-in-out infinite;
        }
        .profile-skeleton-card {
            height: 130px;
            border-radius: 18px;
            background: linear-gradient(90deg,
                rgba(34, 34, 44, 0.4) 0%,
                rgba(34, 34, 44, 0.7) 50%,
                rgba(34, 34, 44, 0.4) 100%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.6s ease-in-out infinite;
        }
        @keyframes skeletonShimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ===== PROFILE HERO ===== */
        .profile-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(169, 120, 74, 0.22) 0%, rgba(34, 34, 44, 0.55) 100%);
            backdrop-filter: blur(28px) saturate(1.6);
            -webkit-backdrop-filter: blur(28px) saturate(1.6);
            border: 1px solid rgba(169, 120, 74, 0.32);
            border-radius: 24px;
            padding: 28px 22px 22px;
            margin-bottom: 16px;
            overflow: hidden;
            text-align: center;
            animation: profileSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
        @keyframes profileSlideIn {
            0% { opacity: 0; transform: translateX(-24px) translateY(6px); }
            100% { opacity: 1; transform: translateX(0) translateY(0); }
        }

        .profile-hero-glow {
            position: absolute;
            top: -70px;
            right: -70px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.25), transparent 65%);
            pointer-events: none;
            animation: glowPulse 6s ease-in-out infinite;
        }
        @keyframes glowPulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.08); opacity: 1; }
        }

        .profile-avatar-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 14px;
            z-index: 1;
        }
        .profile-avatar {
            width: 88px;
            height: 88px;
            border-radius: 24px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 30px;
            font-weight: 800;
            box-shadow: 0 20px 40px -16px rgba(169, 120, 74, 0.8),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
            overflow: hidden;
            transition: transform 0.3s;
        }
        .profile-hero:hover .profile-avatar {
            transform: scale(1.03);
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .profile-status-dot {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #22c55e;
            border: 3px solid #1a1a22;
            box-shadow: 0 0 14px rgba(34, 197, 94, 0.9);
            animation: statusPulse 2s infinite;
        }
        @keyframes statusPulse {
            0%, 100% { box-shadow: 0 0 14px rgba(34, 197, 94, 0.9); }
            50% { box-shadow: 0 0 20px rgba(34, 197, 94, 1); }
        }

        .profile-name {
            font-size: 23px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.025em;
            line-height: 1.2;
            margin-bottom: 6px;
            position: relative;
            z-index: 1;
        }
        .profile-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 12px;
            color: #c9a961;
            font-weight: 700;
            letter-spacing: 0.06em;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }
        .profile-badges {
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .profile-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 16px;
            background: rgba(20, 20, 26, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }
        .profile-stat { flex: 1; text-align: center; }
        .profile-stat-value {
            font-size: 19px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.025em;
            line-height: 1;
            margin-bottom: 5px;
            font-variant-numeric: tabular-nums;
        }
        .profile-stat-label {
            font-size: 9.5px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 700;
        }
        .profile-stat-divider {
            width: 1px;
            height: 32px;
            background: rgba(255, 255, 255, 0.08);
        }
        .profile-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            position: relative;
            z-index: 1;
        }
        .profile-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 13px 16px;
            border-radius: 13px;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            font-family: inherit;
            cursor: pointer;
            border: 1px solid transparent;
            -webkit-tap-highlight-color: transparent;
        }
        .profile-btn-primary {
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            color: #fff;
            box-shadow: 0 10px 22px -10px rgba(169, 120, 74, 0.8);
        }
        .profile-btn-primary:hover,
        .profile-btn-primary:active {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px -10px rgba(169, 120, 74, 1);
        }
        .profile-btn-ghost {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-secondary);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .profile-btn-ghost:hover,
        .profile-btn-ghost:active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        /* ===== INFO LIST ===== */
        .info-list { display: flex; flex-direction: column; gap: 5px; }
        .info-row {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px;
            border-radius: 12px;
            background: rgba(20, 20, 26, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.03);
            transition: all 0.2s;
        }
        .info-row:hover,
        .info-row:active {
            background: rgba(169, 120, 74, 0.08);
            border-color: rgba(169, 120, 74, 0.2);
            transform: translateX(3px);
        }
        .info-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: rgba(169, 120, 74, 0.12);
            border: 1px solid rgba(169, 120, 74, 0.25);
            display: grid;
            place-items: center;
            color: #c9a961;
            flex-shrink: 0;
        }
        .info-content { flex: 1; min-width: 0; }
        .info-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-primary);
            word-break: break-word;
        }

        /* ===== SUMMARY ===== */
        .summary-list { display: flex; flex-direction: column; }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 13px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            gap: 12px;
        }
        .summary-row:last-child { border-bottom: none; }
        .summary-label {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .summary-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            font-variant-numeric: tabular-nums;
            text-align: right;
        }
        .summary-value.green { color: #22c55e; }
        .summary-total {
            padding-top: 15px;
            margin-top: 4px;
            border-top: 2px solid rgba(169, 120, 74, 0.3) !important;
        }
        .summary-total .summary-label {
            font-weight: 800;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 11px;
        }
        .summary-total .summary-value { font-size: 16px; color: #c9a961; }

        /* ===== LOGOUT ===== */
        .logout-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            margin-top: 16px;
            background: rgba(239, 68, 68, 0.08);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid rgba(239, 68, 68, 0.22);
            border-radius: 18px;
            animation: profileSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
            animation-delay: 0.3s;
            transition: all 0.2s;
        }
        .logout-card:hover {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.4);
        }
        .logout-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            display: grid;
            place-items: center;
            color: #ef4444;
            flex-shrink: 0;
        }
        .logout-info { flex: 1; min-width: 0; }
        .logout-title {
            font-size: 14px;
            font-weight: 700;
            color: #ef4444;
            margin-bottom: 3px;
        }
        .logout-text {
            font-size: 11.5px;
            color: var(--text-muted);
        }
        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 12px 22px;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 8px 18px -8px rgba(239, 68, 68, 0.7);
            white-space: nowrap;
            flex-shrink: 0;
            -webkit-tap-highlight-color: transparent;
        }
        .logout-btn:hover,
        .logout-btn:active {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px -8px rgba(239, 68, 68, 0.9);
        }

        /* Staggered animations */
        .profile-modal-body .p-card { animation: profileSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both; }
        .profile-modal-body .p-card:nth-of-type(1) { animation-delay: 0.12s; }
        .profile-modal-body .p-card:nth-of-type(2) { animation-delay: 0.22s; }

        /* Desktop centering */
        @media (min-width: 720px) {
            .profile-modal {
                left: 50%;
                transform: translateX(-50%) translateX(-100%);
                border-left: 1px solid rgba(169, 120, 74, 0.2);
                border-right: 1px solid rgba(169, 120, 74, 0.2);
                border-radius: 0 20px 20px 0;
            }
            .profile-modal-overlay.open .profile-modal {
                transform: translateX(-50%) translateX(0);
            }
        }

        /* Mobile enhancements */
        @media (max-width: 480px) {
            .profile-modal-header { padding: 14px 16px; gap: 12px; }
            .profile-modal-body { padding: 18px 16px 32px; }
            .profile-modal-back { width: 40px; height: 40px; }
            .profile-modal-brand { width: 34px; height: 34px; }
            .profile-modal-title { font-size: 15px; }
            .profile-avatar { width: 76px; height: 76px; font-size: 26px; border-radius: 20px; }
            .profile-name { font-size: 20px; }
            .profile-stat-value { font-size: 17px; }
            .profile-actions { grid-template-columns: 1fr; }
            .profile-btn { padding: 12px 14px; }
            .logout-card { flex-direction: column; text-align: center; gap: 12px; }
            .logout-card > form { width: 100%; }
            .logout-btn { width: 100%; justify-content: center; }
            .info-row { padding: 11px; }
            .info-icon { width: 34px; height: 34px; }
        }

        /* ===== STACKED SUB MODAL ===== */
        .profile-modal-overlay.sub-modal {
            z-index: 10001;
            background: rgba(5, 5, 8, 0.75);
            backdrop-filter: blur(20px) saturate(1.5);
            -webkit-backdrop-filter: blur(20px) saturate(1.5);
        }
        .profile-modal-overlay.sub-modal .profile-modal {
            max-width: 560px;
            background: linear-gradient(180deg, rgba(15, 15, 20, 0.98), rgba(10, 10, 16, 1));
            box-shadow: 
                12px 0 80px rgba(0, 0, 0, 0.95),
                inset -1px 0 0 rgba(169, 120, 74, 0.25);
        }
        .profile-modal-overlay.sub-modal.open .profile-modal {
            box-shadow:
                -20px 0 60px rgba(0, 0, 0, 0.5),
                12px 0 80px rgba(0, 0, 0, 0.95);
        }

        /* Fade out parent modal slightly when sub-modal open */
        .profile-modal-overlay.open:has(~ .profile-modal-overlay.sub-modal.open) .profile-modal {
            filter: brightness(0.6);
            transform: translateX(-15px) scale(0.98);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Fallback para sa browsers nga walay :has() */
        .portal-has-sub .profile-modal-overlay:not(.sub-modal).open .profile-modal {
            filter: brightness(0.55);
            transform: translateX(-15px) scale(0.98);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ===== EDIT FORM ===== */
        .edit-header {
            text-align: center;
            margin-bottom: 20px;
            animation: profileSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
        .edit-header-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(201, 169, 97, 0.15), rgba(138, 95, 54, 0.15));
            border: 1px solid rgba(169, 120, 74, 0.3);
            display: grid;
            place-items: center;
            margin: 0 auto 12px;
            color: #c9a961;
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.5);
        }
        .edit-header-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }
        .edit-header-sub {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 16px;
            animation: profileSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
        .form-group:nth-child(1) { animation-delay: 0.05s; }
        .form-group:nth-child(2) { animation-delay: 0.1s; }
        .form-group:nth-child(3) { animation-delay: 0.15s; }
        .form-group:nth-child(4) { animation-delay: 0.2s; }
        .form-group:nth-child(5) { animation-delay: 0.25s; }
        .form-group:nth-child(6) { animation-delay: 0.3s; }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .form-label .req {
            color: #ef4444;
            margin-left: 3px;
        }
        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 13px 16px;
            background: rgba(20, 20, 26, 0.55);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 13.5px;
            font-family: inherit;
            outline: none;
            transition: all 0.15s;
        }
        .form-input::placeholder,
        .form-textarea::placeholder { color: var(--text-muted); }
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--accent);
            background: rgba(20, 20, 26, 0.85);
            box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            animation: profileSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
            animation-delay: 0.35s;
        }
        .form-actions .p-btn {
            flex: 1;
            padding: 14px 20px;
            font-size: 13px;
            border-radius: 12px;
        }

        .form-alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid;
            display: flex;
            align-items: center;
            gap: 9px;
        }
        .form-alert.error {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* Logo upload */
        .logo-upload {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: rgba(20, 20, 26, 0.4);
            border: 1px dashed rgba(169, 120, 74, 0.3);
            border-radius: 14px;
            margin-bottom: 16px;
            animation: profileSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
        .logo-preview {
            width: 76px;
            height: 76px;
            border-radius: 16px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 26px;
            font-weight: 800;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.6);
        }
        .logo-preview img { width: 100%; height: 100%; object-fit: cover; }
        .logo-info { flex: 1; min-width: 0; }
        .logo-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .logo-hint {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }
        .logo-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .logo-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid;
        }
        .logo-btn.upload {
            background: rgba(169, 120, 74, 0.15);
            border-color: rgba(169, 120, 74, 0.3);
            color: #c9a961;
        }
        .logo-btn.upload:hover {
            background: rgba(169, 120, 74, 0.25);
            border-color: rgba(169, 120, 74, 0.5);
        }
        .logo-btn.remove {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.25);
            color: #ef4444;
        }
        .logo-btn.remove:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* ============================================================
           SHARED UI COMPONENTS — MOBILE OPTIMIZED
           ============================================================ */

        /* ===== PAGE HEADER ===== */
        .pp-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }
        .pp-head-left { flex: 1; min-width: 0; }
        .pp-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.03em;
            line-height: 1.15;
            margin-bottom: 4px;
        }
        .pp-sub {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===== ACTION BUTTON ===== */
        .pp-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 18px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            border: none;
            border-radius: 13px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.7);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .pp-action:active { transform: scale(0.97); }

        .pp-action-ghost {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            box-shadow: none;
        }
        .pp-action-ghost:active { background: rgba(255, 255, 255, 0.1); }

        /* ===== STATS ROW ===== */
        .pp-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .pp-stat {
            padding: 16px;
            background: rgba(34, 34, 44, 0.4);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            transition: all 0.2s;
        }
        .pp-stat:active { transform: scale(0.98); }
        .pp-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(169, 120, 74, 0.12);
            border: 1px solid rgba(169, 120, 74, 0.25);
            display: grid;
            place-items: center;
            color: #c9a961;
            margin-bottom: 10px;
        }
        .pp-stat-icon.green { background: rgba(34, 197, 94, 0.12); border-color: rgba(34, 197, 94, 0.25); color: #22c55e; }
        .pp-stat-icon.amber { background: rgba(245, 158, 11, 0.12); border-color: rgba(245, 158, 11, 0.25); color: #f59e0b; }
        .pp-stat-icon.red { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.25); color: #ef4444; }
        .pp-stat-icon.blue { background: rgba(59, 130, 246, 0.12); border-color: rgba(59, 130, 246, 0.25); color: #3b82f6; }
        .pp-stat-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.03em;
            line-height: 1;
            margin-bottom: 5px;
            font-variant-numeric: tabular-nums;
        }
        .pp-stat-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        /* ===== FILTER TABS ===== */
        .pp-tabs {
            display: flex;
            gap: 6px;
            padding: 4px;
            background: rgba(20, 20, 26, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 13px;
            margin-bottom: 16px;
            overflow-x: auto;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
        .pp-tabs::-webkit-scrollbar { display: none; }
        .pp-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 38px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .pp-tab.active {
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            color: #fff;
            box-shadow: 0 6px 16px -6px rgba(169, 120, 74, 0.6);
        }
        .pp-tab:active:not(.active) {
            background: rgba(255, 255, 255, 0.05);
        }
        .pp-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 18px;
            padding: 0 6px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
        }
        .pp-tab:not(.active) .pp-tab-count {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-muted);
        }

        /* ===== CARD LIST ===== */
        .pp-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .pp-card {
            position: relative;
            padding: 16px;
            background: rgba(34, 34, 44, 0.4);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
            text-decoration: none;
            overflow: hidden;
        }
        .pp-card:active {
            transform: scale(0.985);
            background: rgba(34, 34, 44, 0.55);
            border-color: rgba(169, 120, 74, 0.3);
        }

        .pp-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
        }
        .pp-card-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-primary);
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            letter-spacing: 0.01em;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .pp-card-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 500;
        }
        .pp-card-body {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .pp-card-amount {
            font-size: 20px;
            font-weight: 800;
            color: #c9a961;
            letter-spacing: -0.02em;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .pp-card-meta {
            font-size: 11.5px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
        }
        .pp-card-meta svg { flex-shrink: 0; }

        /* ===== STATUS BADGES ===== */
        .pp-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 10.5px;
            font-weight: 800;
            text-transform: capitalize;
            letter-spacing: 0.02em;
            border: 1px solid;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .pp-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 8px currentColor;
        }
        .pp-badge.pending { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border-color: rgba(245, 158, 11, 0.25); }
        .pp-badge.approved, .pp-badge.active, .pp-badge.paid, .pp-badge.completed, .pp-badge.delivered { background: rgba(34, 197, 94, 0.12); color: #22c55e; border-color: rgba(34, 197, 94, 0.25); }
        .pp-badge.rejected, .pp-badge.cancelled, .pp-badge.suspended { background: rgba(239, 68, 68, 0.12); color: #ef4444; border-color: rgba(239, 68, 68, 0.25); }
        .pp-badge.partial, .pp-badge.processing { background: rgba(59, 130, 246, 0.12); color: #3b82f6; border-color: rgba(59, 130, 246, 0.25); }

        /* ===== EMPTY STATE ===== */
        .pp-empty {
            text-align: center;
            padding: 60px 20px;
            background: rgba(34, 34, 44, 0.3);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
        }
        .pp-empty-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
            border-radius: 20px;
            background: rgba(169, 120, 74, 0.1);
            border: 1px solid rgba(169, 120, 74, 0.25);
            display: grid;
            place-items: center;
            color: #c9a961;
        }
        .pp-empty-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        .pp-empty-text {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* ===== INFO ROWS (For detail pages) ===== */
        .pp-info-list {
            display: flex;
            flex-direction: column;
        }
        .pp-info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .pp-info-row:last-child { border-bottom: none; }
        .pp-info-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            flex-shrink: 0;
        }
        .pp-info-value {
            font-size: 13.5px;
            color: var(--text-primary);
            font-weight: 700;
            text-align: right;
            word-break: break-word;
        }
        .pp-info-value.green { color: #22c55e; }
        .pp-info-value.gold { color: #c9a961; }

        /* ===== SECTION CARD ===== */
        .pp-section {
            padding: 20px;
            background: rgba(34, 34, 44, 0.4);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            margin-bottom: 14px;
        }
        .pp-section-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .pp-section-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(169, 120, 74, 0.12);
            border: 1px solid rgba(169, 120, 74, 0.25);
            display: grid;
            place-items: center;
            color: #c9a961;
            flex-shrink: 0;
        }
        .pp-section-icon.green { background: rgba(34, 197, 94, 0.12); border-color: rgba(34, 197, 94, 0.25); color: #22c55e; }
        .pp-section-icon.blue { background: rgba(59, 130, 246, 0.12); border-color: rgba(59, 130, 246, 0.25); color: #3b82f6; }
        .pp-section-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }
        .pp-section-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ===== STAGGERED ANIMATION ===== */
        .pp-anim > * {
            opacity: 0;
            animation: ppSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        .pp-anim > *:nth-child(1) { animation-delay: 0.02s; }
        .pp-anim > *:nth-child(2) { animation-delay: 0.08s; }
        .pp-anim > *:nth-child(3) { animation-delay: 0.14s; }
        .pp-anim > *:nth-child(4) { animation-delay: 0.2s; }
        .pp-anim > *:nth-child(5) { animation-delay: 0.26s; }
        .pp-anim > *:nth-child(6) { animation-delay: 0.32s; }
        .pp-anim > *:nth-child(7) { animation-delay: 0.38s; }
        .pp-anim > *:nth-child(8) { animation-delay: 0.44s; }
        @keyframes ppSlideIn {
            0% { opacity: 0; transform: translateY(12px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ===== MOBILE SPACING ===== */
        @media (max-width: 480px) {
            .pp-title { font-size: 22px; }
            .pp-stat { padding: 14px; }
            .pp-stat-value { font-size: 20px; }
            .pp-card { padding: 14px; }
            .pp-card-amount { font-size: 18px; }
            .pp-section { padding: 16px; }
        }
    </style>
    @stack('styles')

<!-- Mobile refresh — touch-friendly -->
<style>
    @media (max-width: 768px), (hover: none) {
        #pRefreshBtn {
            min-width: 44px !important;
            min-height: 44px !important;
            padding: 10px !important;
            touch-action: manipulation !important;
            -webkit-tap-highlight-color: transparent !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 100 !important;
            pointer-events: auto !important;
            user-select: none !important;
            -webkit-user-select: none !important;
        }
        #pRefreshBtn:active,
        #pRefreshBtn:focus {
            transform: scale(0.92) !important;
            background: rgba(201, 169, 97, 0.25) !important;
            border-color: #c9a961 !important;
        }
        /* Ensure SVG doesn't steal the tap */
        #pRefreshBtn *,
        #pRefreshBtn svg {
            pointer-events: none !important;
            display: block !important;
        }
    }
</style>
</head>
<body>

<div class="portal-wrap">

    {{-- ===== TOPBAR ===== --}}
    <header class="p-topbar">
        <div class="p-topbar-inner">
            <a href="{{ route('portal.profile') }}" class="p-store-link" id="profileTrigger">
                <div class="p-store-avatar">
                    @if(Auth::guard('store')->user() && Auth::guard('store')->user()->logo_url)
                        <img src="{{ Auth::guard('store')->user()->logo_url }}" alt="">
                    @else
                        {{ strtoupper(substr(Auth::guard('store')->user()->store_name ?? 'ST', 0, 2)) }}
                    @endif
                </div>
                <div class="p-store-info">
                    <div class="p-store-name">{{ Auth::guard('store')->user()->store_name ?? 'Store Portal' }}</div>
                    <div class="p-store-code">{{ Auth::guard('store')->user()->code ?? '' }}</div>
                </div>
                <svg class="p-store-arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </a>

            <div class="p-topbar-actions">
                <button type="button" class="p-icon-btn" id="pRefreshBtn" title="Refresh" onclick="pRefresh()">
                    <svg id="pRefreshIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 4v6h-6M1 20v-6h6"/>
                        <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
                    </svg>
                </button>


            </div>
        </div>
    </header>

    {{-- ===== NAV TABS ===== --}}
    <nav class="p-nav">
        <a href="{{ route('portal.dashboard') }}" class="p-nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('portal.orders.index') }}" class="p-nav-item {{ request()->routeIs('portal.orders.*') ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
            Orders
        </a>
        <a href="{{ route('portal.deliveries.index') }}" class="p-nav-item {{ request()->routeIs('portal.deliveries.*') ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            Deliveries
        </a>
        <a href="{{ route('portal.inventory.index') }}" class="p-nav-item {{ request()->routeIs('portal.inventory.*') ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Inventory
        </a>
        <a href="{{ route('portal.reports.index') }}" class="p-nav-item {{ request()->routeIs('portal.reports.*') || request()->routeIs('portal.payments.*') ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
            Finance
        </a>
    </nav>

    {{-- ===== MAIN ===== --}}
    <main class="p-main">
        @if(session('success'))
            <div class="p-alert p-alert-success">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-alert p-alert-error">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4M12 16h.01"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

{{-- ===== MODAL 1: PROFILE ===== --}}
<div class="profile-modal-overlay" id="portalModal">
    <div class="profile-modal" onclick="event.stopPropagation();">
        <div class="profile-modal-header">
            <button type="button" class="profile-modal-back" onclick="closePortalModal()" title="Go back">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="profile-modal-title-wrap">
                <div class="profile-modal-title">My Account</div>
                <div class="profile-modal-subtitle">Manage your store profile</div>
            </div>
            <div class="profile-modal-brand">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                    <path d="M6 1v3M10 1v3M14 1v3"/>
                </svg>
            </div>
        </div>
        <div class="profile-modal-body" id="portalModalBody">
            <div class="profile-skeleton">
                <div class="profile-skeleton-hero"></div>
                <div class="profile-skeleton-card"></div>
                <div class="profile-skeleton-card"></div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL 2: EDIT / PASSWORD ===== --}}
<div class="profile-modal-overlay sub-modal" id="portalSubModal">
    <div class="profile-modal" onclick="event.stopPropagation();">
        <div class="profile-modal-header">
            <button type="button" class="profile-modal-back" onclick="closeSubModal()" title="Go back">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="profile-modal-title-wrap">
                <div class="profile-modal-title" id="subModalTitle">Edit Profile</div>
                <div class="profile-modal-subtitle" id="subModalSubtitle">Update your information</div>
            </div>
            <div class="profile-modal-brand">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                    <path d="M6 1v3M10 1v3M14 1v3"/>
                </svg>
            </div>
        </div>
        <div class="profile-modal-body" id="portalSubModalBody">
            <div class="profile-skeleton">
                <div class="profile-skeleton-hero"></div>
                <div class="profile-skeleton-card"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var loaded = {};
        var profileOverlay = document.getElementById('portalModal');
        var subOverlay = document.getElementById('portalSubModal');
        var profileBody = document.getElementById('portalModalBody');
        var subBody = document.getElementById('portalSubModalBody');
        var profileTitle = document.getElementById('modalTitle');
        var profileSubtitle = document.getElementById('modalSubtitle');
        var subTitle = document.getElementById('subModalTitle');
        var subSubtitle = document.getElementById('subModalSubtitle');

        var skeleton = '<div class="profile-skeleton">'
            + '<div class="profile-skeleton-hero"></div>'
            + '<div class="profile-skeleton-card"></div>'
            + '<div class="profile-skeleton-card"></div>'
            + '</div>';

        var skeletonSm = '<div class="profile-skeleton">'
            + '<div class="profile-skeleton-hero"></div>'
            + '<div class="profile-skeleton-card"></div>'
            + '</div>';

        if (!profileOverlay || !subOverlay) return;

        // ===== MAIN MODAL (PROFILE) =====
        window.openPortalModal = function(url, opts) {
            opts = opts || {};
            var modalTitle = opts.title || 'My Account';
            var modalSubtitle = opts.subtitle || 'Manage your store profile';
            var cacheKey = opts.cacheKey || url;

            if (profileTitle) profileTitle.textContent = modalTitle;
            if (profileSubtitle) profileSubtitle.textContent = modalSubtitle;

            try {
                sessionStorage.setItem('portal_modal_open', JSON.stringify({
                    url: url, title: modalTitle, subtitle: modalSubtitle,
                    cacheKey: cacheKey, forceReload: opts.forceReload || false
                }));
            } catch (e) {}

            profileOverlay.classList.add('open');
            document.body.classList.add('modal-open');

            if (loaded[cacheKey] && !opts.forceReload) {
                profileBody.innerHTML = loaded[cacheKey];
                retriggerAnimations(profileBody);
                bindModalButtons();
                return;
            }

            profileBody.innerHTML = skeleton;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var content = doc.querySelector('.p-main');
                if (content) {
                    var clone = content.cloneNode(true);
                    clone.querySelectorAll('.p-alert').forEach(function(a) { a.remove(); });
                    var h = clone.innerHTML;
                    profileBody.innerHTML = h;
                    loaded[cacheKey] = h;
                    retriggerAnimations(profileBody);
                    bindModalButtons();
                }
            })
            .catch(function() {
                profileBody.innerHTML = '<div style="text-align:center;padding:40px;color:#ef4444;font-size:13px;">Failed to load</div>';
            });
        };

        window.closePortalModal = function() {
            profileOverlay.classList.remove('open');
            // Also close sub modal kung open
            if (subOverlay.classList.contains('open')) {
                subOverlay.classList.remove('open');
            }
            document.body.classList.remove('modal-open');
            try { sessionStorage.removeItem('portal_modal_open'); } catch (e) {}
        };

        // ===== SUB MODAL (EDIT / PASSWORD) =====
        window.openSubModal = function(url, opts) {
            opts = opts || {};
            var title = opts.title || 'Edit Profile';
            var subtitle = opts.subtitle || 'Update your information';
            var cacheKey = opts.cacheKey || url;

            if (subTitle) subTitle.textContent = title;
            if (subSubtitle) subSubtitle.textContent = subtitle;

            try {
                sessionStorage.setItem('portal_submodal_open', JSON.stringify({
                    url: url, title: title, subtitle: subtitle, cacheKey: cacheKey
                }));
            } catch (e) {}

            subOverlay.classList.add('open');
            document.body.classList.add('portal-has-sub');

            if (loaded[cacheKey] && !opts.forceReload) {
                subBody.innerHTML = loaded[cacheKey];
                retriggerAnimations(subBody);
                bindModalButtons();
                return;
            }

            subBody.innerHTML = skeletonSm;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var content = doc.querySelector('.p-main');
                if (content) {
                    var clone = content.cloneNode(true);
                    clone.querySelectorAll('.p-alert').forEach(function(a) { a.remove(); });
                    var h = clone.innerHTML;
                    subBody.innerHTML = h;
                    loaded[cacheKey] = h;
                    retriggerAnimations(subBody);
                    bindModalButtons();
                }
            })
            .catch(function() {
                subBody.innerHTML = '<div style="text-align:center;padding:40px;color:#ef4444;font-size:13px;">Failed to load</div>';
            });
        };

        window.closeSubModal = function() {
            subOverlay.classList.remove('open');
            document.body.classList.remove('portal-has-sub');
            try { sessionStorage.removeItem('portal_submodal_open'); } catch (e) {}
        };

        // ===== HELPERS =====
        function retriggerAnimations(container) {
            if (!container) return;
            var els = container.querySelectorAll('.profile-hero, .p-card, .logout-card, .edit-header, .form-group, .form-actions');
            els.forEach(function(el) {
                el.style.animation = 'none';
                void el.offsetWidth;
                el.style.animation = '';
            });
        }

        function bindModalButtons() {
            // Edit Profile button (sa profile modal)
            document.querySelectorAll('[data-modal="edit-profile"]').forEach(function(btn) {
                if (btn.dataset.bound) return;
                btn.dataset.bound = '1';
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openSubModal("{{ route('portal.profile.edit') }}", {
                        title: 'Edit Profile',
                        subtitle: 'Update your store information',
                        cacheKey: 'edit-profile',
                        forceReload: true
                    });
                });
            });

            // Change Password button
            document.querySelectorAll('[data-modal="change-password"]').forEach(function(btn) {
                if (btn.dataset.bound) return;
                btn.dataset.bound = '1';
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openSubModal("{{ route('portal.profile.password') }}", {
                        title: 'Change Password',
                        subtitle: 'Update your security credentials',
                        cacheKey: 'change-password',
                        forceReload: true
                    });
                });
            });

            // Close buttons
            document.querySelectorAll('[data-modal-close]').forEach(function(btn) {
                if (btn.dataset.bound) return;
                btn.dataset.bound = '1';
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Kung naa sa sub modal, close sub
                    if (btn.closest('#portalSubModal')) {
                        closeSubModal();
                    } else {
                        closePortalModal();
                    }
                });
            });
        }

        // ===== RESTORE ON REFRESH =====
        // IMPORTANT: Skip submodal if naay flash success (gikan sa save)
        var hasFlash = @if(session('success')) true @else false @endif;

        try {
            var savedSub = sessionStorage.getItem('portal_submodal_open');
            var savedMain = sessionStorage.getItem('portal_modal_open');

            // Clear submodal state kung gikan sa save
            if (hasFlash) {
                try { sessionStorage.removeItem('portal_submodal_open'); } catch (e) {}
                savedSub = null;
            }

            if (savedMain) {
                var st = JSON.parse(savedMain);
                setTimeout(function() {
                    openPortalModal(st.url, st);
                    // Reopen sub ONLY kung wala'y flash success
                    if (savedSub && !hasFlash) {
                        var ss = JSON.parse(savedSub);
                        setTimeout(function() {
                            openSubModal(ss.url, ss);
                        }, 350);
                    }
                }, 100);
            }
        } catch (e) {}

        // ===== CHECK KUNG GIKAN SA FORM SUBMIT =====
        try {
            var justSaved = sessionStorage.getItem('portal_just_saved');
            if (justSaved) {
                sessionStorage.removeItem('portal_just_saved');
                // Clear submodal + edit state
                sessionStorage.removeItem('portal_submodal_open');

                // Reopen profile modal with fresh content
                setTimeout(function() {
                    openPortalModal("{{ route('portal.profile') }}", {
                        title: 'My Account',
                        subtitle: 'Manage your store profile',
                        cacheKey: 'profile-' + Date.now(),
                        forceReload: true
                    });
                    // Show success toast
                    setTimeout(function() {
                        var t = document.getElementById('pToast');
                        var txt = document.getElementById('pToastText');
                        if (t && txt) {
                            txt.textContent = justSaved;
                            t.classList.add('show');
                            setTimeout(function() { t.classList.remove('show'); }, 2500);
                        }
                    }, 400);
                }, 150);
            }
        } catch (e) {}

        // ===== TRIGGERS =====
        var profileTrigger = document.getElementById('profileTrigger');
        if (profileTrigger) {
            profileTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                openPortalModal("{{ route('portal.profile') }}", {
                    title: 'My Account',
                    subtitle: 'Manage your store profile',
                    cacheKey: 'profile'
                });
            });
        }

        // ESC — close top-most modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (subOverlay.classList.contains('open')) {
                    closeSubModal();
                } else if (profileOverlay.classList.contains('open')) {
                    closePortalModal();
                }
            }
        });

        // Click backdrop — close kung mao ang target
        profileOverlay.addEventListener('click', function(e) {
            if (e.target === profileOverlay) closePortalModal();
        });
        subOverlay.addEventListener('click', function(e) {
            if (e.target === subOverlay) closeSubModal();
        });

        // Initial bind
        document.addEventListener('DOMContentLoaded', function() {
            bindModalButtons();
        });

        
        // ===== AUTO-OPEN PROFILE MODAL IF MAY FLASH SUCCESS =====
        @if(session('success'))
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (typeof openPortalModal === 'function') {
                        // Clear any submodal state
                        try { sessionStorage.removeItem('portal_submodal_open'); } catch (e) {}

                        openPortalModal("{{ route('portal.profile') }}", {
                            title: 'My Account',
                            subtitle: 'Manage your store profile',
                            cacheKey: 'profile-' + Date.now(),
                            forceReload: true
                        });

                        // Show toast after modal opens
                        setTimeout(function() {
                            var t = document.getElementById('pToast');
                            var txt = document.getElementById('pToastText');
                            if (t && txt) {
                                txt.textContent = "{{ session('success') }}";
                                t.classList.add('show');
                                setTimeout(function() { t.classList.remove('show'); }, 3000);
                            }
                        }, 600);
                    }
                }, 300);
            });
        @endif
    })();
</script>



    <footer class="p-foot">
        &copy; {{ date('Y') }} Coffee Beans - Store Portal
    </footer>
</div>

{{-- Toast --}}
<div class="p-toast" id="pToast">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M5 12l5 5L20 7"/>
    </svg>
    <span id="pToastText">Updated!</span>
</div>




{{-- ===== REVERB REAL-TIME LISTENERS ===== --}}
<script>
    (function() {
        function waitForEcho(cb) {
            if (window.Echo) return cb();
            setTimeout(function() { waitForEcho(cb); }, 100);
        }

        waitForEcho(function() {
            var storeId = {{ Auth::guard('store')->id() ?? 'null' }};
            if (!storeId) return;

            window.Echo.private('store.' + storeId)
                .listen('.order.placed', function(data) {
                    console.log('[Reverb] New order:', data);
                    showPortalToast('New order: ' + data.number);
                    if (window.PortalLive) window.PortalLive.poll();
                })
                .listen('.order.status', function(data) {
                    console.log('[Reverb] Status update:', data);
                    showPortalToast('Order ' + data.number + ' is now ' + data.status);
                    updateOrderBadge(data.id, data.status);
                    if (window.PortalLive) window.PortalLive.poll();
                })
                .listen('.payment.recorded', function(data) {
                    console.log('[Reverb] Payment recorded:', data);

                    // Show toast
                    showPortalToast('\u20B1' + data.amount + ' payment received!');

                    // Update balance sa page
                    updateBalanceFromPayment(data);

                    // Refresh data
                    if (window.PortalLive) window.PortalLive.poll();
                })
                .listen('.delivery.received', function(data) {
                    showPortalToast('New delivery: ' + data.number);
                    if (window.PortalLive) window.PortalLive.poll();
                });

            console.log('[Reverb] Listening on store.' + storeId);
        });

        function updateOrderBadge(orderId, status) {
            document.querySelectorAll('.pp-order, .pp-card').forEach(function(card) {
                var href = card.getAttribute('href') || '';
                if (href.indexOf('/' + orderId) !== -1) {
                    var badge = card.querySelector('.pp-order-badge, .pp-badge');
                    if (badge) {
                        badge.className = badge.className.replace(/\b(pending|approved|rejected)\b/g, status);
                        badge.className += ' ' + status;
                        badge.textContent = status;
                    }
                }
            });
        }

        function showPortalToast(msg) {
            var t = document.getElementById('pToast');
            var txt = document.getElementById('pToastText');
            if (!t || !txt) return;
            txt.textContent = msg;
            t.classList.add('show');
            clearTimeout(window._portalToastTimer);
            window._portalToastTimer = setTimeout(function() {
                t.classList.remove('show');
            }, 3000);
        }
    
        // ===== BALANCE UPDATE FROM PAYMENT =====
        function updateBalanceFromPayment(data) {
            // Dashboard balance
            var balanceValue = document.querySelector('.pp-balance-value');
            if (balanceValue) {
                balanceValue.classList.add('data-updated');
                balanceValue.textContent = '\u20B1' + data.new_balance;
                setTimeout(function() { balanceValue.classList.remove('data-updated'); }, 800);
            }

            // Balance status badge
            var statusBadge = document.querySelector('.pp-balance-status');
            if (statusBadge) {
                var isPaid = parseFloat(data.new_balance_raw) <= 0;
                statusBadge.className = isPaid ? 'pp-balance-status paid' : 'pp-balance-status';
                statusBadge.textContent = isPaid ? 'Paid' : 'Pending';
            }

            // Balance meta (Paid, Delivered)
            var metaValues = document.querySelectorAll('.pp-balance-meta-item strong');
            if (metaValues.length >= 2) {
                metaValues[0].textContent = '\u20B1' + data.total_paid.replace('.00', '');
                metaValues[1].textContent = '\u20B1' + data.total_delivered.replace('.00', '');
                metaValues[0].classList.add('data-updated');
                setTimeout(function() { metaValues[0].classList.remove('data-updated'); }, 800);
            }

            // Any element with data-balance attribute
            document.querySelectorAll('[data-balance]').forEach(function(el) {
                el.textContent = '\u20B1' + data.new_balance;
                el.classList.add('data-updated');
                setTimeout(function() { el.classList.remove('data-updated'); }, 800);
            });
        }
    })();
</script>

{{-- ===== ECHO + PUSHER VIA CDN ===== --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    // Setup Echo with Reverb
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ env("REVERB_APP_KEY") }}',
        wsHost: '{{ env("REVERB_HOST", "10.236.154.40") }}',
        wsPort: {{ env("REVERB_PORT", 8080) }},
        wssPort: {{ env("REVERB_PORT", 8080) }},
        forceTLS: false,
        enabledTransports: ['ws'],
        disableStats: true,
    });

    console.log('[Echo] Initialized sa host: {{ env("REVERB_HOST", "10.236.154.40") }}:{{ env("REVERB_PORT", 8080) }}');
</script>
@stack('scripts')



{{-- ===== FLOATING NOTIFICATION BELL ===== --}}
@auth('store')
<div id="pnBell" class="pn-bell">
    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
    <span class="pn-badge" id="pnBadge" style="display:none;">0</span>
</div>

<div id="pnPanel" class="pn-panel">
    <div class="pn-head">
        <div class="pn-head-title">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            Notifications
        </div>
        <button type="button" id="pnMarkAll" class="pn-mark-all">Mark all read</button>
    </div>
    <div class="pn-body" id="pnBody">
        <div class="pn-loading">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:pnSpin 1s linear infinite;">
                <circle cx="12" cy="12" r="10" opacity="0.2"/>
                <path d="M22 12a10 10 0 0 1-10 10"/>
            </svg>
        </div>
    </div>
</div>

<div id="pnPopups" class="pn-popups"></div>

<style>
    /* ==== FLOATING BELL ==== */
    .pn-bell {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        display: grid;
        place-items: center;
        cursor: pointer;
        z-index: 9998;
        box-shadow:
            0 10px 28px -6px rgba(201, 169, 97, 0.6),
            0 0 0 0 rgba(201, 169, 97, 0.5);
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s;
        animation: pnFloat 3s ease-in-out infinite;
    }
    .pn-bell:hover {
        transform: scale(1.08) rotate(-5deg);
        box-shadow:
            0 14px 36px -6px rgba(201, 169, 97, 0.8),
            0 0 0 8px rgba(201, 169, 97, 0.15);
    }
    .pn-bell:active { transform: scale(0.96); }
    .pn-bell.has-new { animation: pnRing 0.6s ease-in-out infinite; }
    @keyframes pnFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    @keyframes pnRing {
        0%, 100% { transform: rotate(0); }
        25% { transform: rotate(-12deg); }
        75% { transform: rotate(12deg); }
    }

    .pn-badge {
        position: absolute;
        top: -3px;
        right: -3px;
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        background: #ef4444;
        color: #fff;
        border-radius: 11px;
        font-size: 11px;
        font-weight: 800;
        display: grid;
        place-items: center;
        border: 2.5px solid #0f0f14;
        font-family: 'Inter', sans-serif;
        box-shadow: 0 3px 8px rgba(239, 68, 68, 0.5);
    }

    /* ==== PANEL ==== */
    .pn-panel {
        position: fixed;
        bottom: 88px;
        right: 24px;
        width: calc(100vw - 32px);
        max-width: 380px;
        max-height: 70vh;
        background: linear-gradient(165deg, #1e1a16, #15120f);
        border: 1px solid rgba(201, 169, 97, 0.3);
        border-radius: 18px;
        box-shadow:
            0 24px 60px -12px rgba(0, 0, 0, 0.85),
            0 0 0 1px rgba(255, 255, 255, 0.03) inset;
        z-index: 9999;
        overflow: hidden;
        display: none;
        flex-direction: column;
        animation: pnPanelIn 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .pn-panel.open { display: flex; }
    @keyframes pnPanelIn {
        from { opacity: 0; transform: translateY(12px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .pn-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        flex-shrink: 0;
    }
    .pn-head-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: 800;
        color: #f5f3f0;
    }
    .pn-head-title svg { color: #c9a961; }
    .pn-mark-all {
        background: none;
        border: none;
        color: #c9a961;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 8px;
        font-family: inherit;
        transition: background 0.15s;
    }
    .pn-mark-all:hover { background: rgba(201, 169, 97, 0.15); }
    .pn-mark-all:disabled { opacity: 0.4; cursor: not-allowed; }

    .pn-body {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    .pn-body::-webkit-scrollbar { width: 4px; }
    .pn-body::-webkit-scrollbar-thumb { background: rgba(201, 169, 97, 0.3); border-radius: 2px; }

    .pn-loading, .pn-empty {
        padding: 40px 20px;
        text-align: center;
        color: #8a8378;
        font-size: 12.5px;
    }
    .pn-loading svg { margin: 0 auto 12px; color: #c9a961; }
    .pn-empty-icon { font-size: 36px; margin-bottom: 10px; opacity: 0.5; }
    .pn-empty-title { font-size: 13px; font-weight: 700; color: #a8a5a0; margin-bottom: 4px; }
    .pn-empty-sub { font-size: 11.5px; }
    @keyframes pnSpin { to { transform: rotate(360deg); } }

    .pn-item {
        display: flex;
        gap: 12px;
        padding: 13px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        cursor: pointer;
        transition: background 0.15s;
        text-decoration: none;
        color: inherit;
    }
    .pn-item:last-child { border-bottom: none; }
    .pn-item:hover { background: rgba(201, 169, 97, 0.08); }
    .pn-item.unread { background: rgba(201, 169, 97, 0.04); }
    .pn-item.unread::before {
        content: '';
        position: absolute;
        width: 4px;
        height: 4px;
        background: #c9a961;
        border-radius: 50%;
        margin-left: -8px;
        margin-top: 8px;
    }

    .pn-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        font-size: 18px;
        flex-shrink: 0;
        background: rgba(201, 169, 97, 0.15);
    }
    .pn-content { flex: 1; min-width: 0; }
    .pn-title {
        font-size: 12.5px;
        font-weight: 800;
        color: #f5f3f0;
        margin-bottom: 3px;
        line-height: 1.3;
    }
    .pn-message {
        font-size: 11.5px;
        color: #a8a5a0;
        line-height: 1.45;
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pn-time {
        font-size: 10.5px;
        color: #8a8378;
        font-weight: 600;
    }

    /* ==== POPUP TOAST ==== */
    .pn-popups {
        position: fixed;
        top: 16px;
        right: 16px;
        width: calc(100vw - 32px);
        max-width: 340px;
        z-index: 9997;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }
    .pn-popup {
        pointer-events: auto;
        background: linear-gradient(165deg, #1e1a16, #15120f);
        border: 1px solid rgba(201, 169, 97, 0.4);
        border-radius: 14px;
        padding: 13px 15px;
        box-shadow: 0 16px 44px -10px rgba(0, 0, 0, 0.85), 0 0 40px -10px rgba(201, 169, 97, 0.35);
        display: flex;
        gap: 12px;
        cursor: pointer;
        animation: pnPopIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        transition: transform 0.15s;
        text-decoration: none;
        color: inherit;
    }
    .pn-popup:hover { transform: translateX(-4px); }
    .pn-popup.closing { animation: pnPopOut 0.25s ease forwards; }
    @keyframes pnPopIn {
        from { opacity: 0; transform: translateX(30px) scale(0.92); }
        to { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes pnPopOut {
        to { opacity: 0; transform: translateX(30px) scale(0.92); }
    }
    .pn-popup .pn-icon { width: 42px; height: 42px; font-size: 20px; }
    .pn-popup .pn-title { font-size: 13px; }
    .pn-popup .pn-message { -webkit-line-clamp: 2; margin-bottom: 0; }

    @media (max-width: 480px) {
        .pn-bell { bottom: 20px; right: 20px; width: 50px; height: 50px; }
        .pn-panel { bottom: 82px; right: 16px; left: 16px; width: auto; max-width: none; }
        .pn-popups { top: 12px; right: 12px; left: 12px; width: auto; max-width: none; }
    }
</style>

<script>
(function() {
    const bell = document.getElementById('pnBell');
    const panel = document.getElementById('pnPanel');
    const body = document.getElementById('pnBody');
    const badge = document.getElementById('pnBadge');
    const markAll = document.getElementById('pnMarkAll');
    const popups = document.getElementById('pnPopups');

    if (!bell || !panel) return;

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
    const UNREAD_URL = '{{ route("portal.notifications.unread") }}';
    const READ_URL_TPL = '{{ route("portal.notifications.read", ":id") }}';
    const MARK_ALL_URL = '{{ route("portal.notifications.read-all") }}';

    let lastCount = 0;
    let initialized = false;
    let shownIds = new Set();
    let isOpen = false;

    // Start polling
    setTimeout(fetchNotifications, 1500);
    setInterval(fetchNotifications, 15000);

    async function fetchNotifications() {
        try {
            const res = await fetch(UNREAD_URL, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF,
                },
                credentials: 'same-origin',
            });

            if (res.status === 401 || res.status === 419) return;
            if (!res.ok) return;

            const data = await res.json();
            updateBadge(data.count);
            renderList(data.notifications);

            // Auto-popup new ones
            if (initialized && data.count > lastCount) {
                data.notifications.forEach(n => {
                    if (!shownIds.has(n.id)) {
                        showPopup(n);
                        shownIds.add(n.id);
                    }
                });
            }

            lastCount = data.count;
            initialized = true;
        } catch (e) {
            console.warn('Notif fetch error:', e);
        }
    }

    function updateBadge(count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'grid';
            bell.classList.add('has-new');
        } else {
            badge.style.display = 'none';
            bell.classList.remove('has-new');
        }
    }

    function renderList(items) {
        if (!items.length) {
            body.innerHTML = `
                <div class="pn-empty">
                    <div class="pn-empty-icon">🔔</div>
                    <div class="pn-empty-title">No new notifications</div>
                    <div class="pn-empty-sub">Wala kay bag-ong updates</div>
                </div>
            `;
            markAll.disabled = true;
            return;
        }

        markAll.disabled = false;
        body.innerHTML = '';

        items.forEach(n => {
            const item = document.createElement('div');
            item.className = 'pn-item unread';
            item.dataset.id = n.id;
            item.innerHTML = `
                <div class="pn-icon" style="background:${n.color}22;color:${n.color};">${n.icon}</div>
                <div class="pn-content">
                    <div class="pn-title">${esc(n.title)}</div>
                    <div class="pn-message">${esc(n.message)}</div>
                    <div class="pn-time">${esc(n.created_at)}</div>
                </div>
            `;
            item.addEventListener('click', () => handleClick(n));
            body.appendChild(item);
        });
    }

    function handleClick(n) {
        markRead(n.id).then(() => {
            if (n.url) {
                window.location.href = n.url;
            } else {
                fetchNotifications();
            }
        });
    }

    function showPopup(n) {
        const el = document.createElement('div');
        el.className = 'pn-popup';
        el.innerHTML = `
            <div class="pn-icon" style="background:${n.color}22;color:${n.color};">${n.icon}</div>
            <div class="pn-content">
                <div class="pn-title">${esc(n.title)}</div>
                <div class="pn-message">${esc(n.message)}</div>
            </div>
        `;
        el.addEventListener('click', () => {
            el.classList.add('closing');
            setTimeout(() => el.remove(), 250);
            handleClick(n);
        });
        popups.appendChild(el);

        // Auto dismiss 8s
        setTimeout(() => {
            if (!el.parentNode) return;
            el.classList.add('closing');
            setTimeout(() => el.remove(), 250);
        }, 8000);
    }

    async function markRead(id) {
        try {
            const url = READ_URL_TPL.replace(':id', id);
            await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF,
                },
                credentials: 'same-origin',
            });
        } catch (e) {}
    }

    async function markAllRead() {
        try {
            await fetch(MARK_ALL_URL, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF,
                },
                credentials: 'same-origin',
            });
            fetchNotifications();
        } catch (e) {}
    }

    function esc(t) {
        const d = document.createElement('div');
        d.textContent = t || '';
        return d.innerHTML;
    }

    // Toggle panel
    bell.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen = !isOpen;
        if (isOpen) {
            panel.classList.add('open');
            fetchNotifications();
        } else {
            panel.classList.remove('open');
        }
    });

    document.addEventListener('click', (e) => {
        if (isOpen && !panel.contains(e.target) && !bell.contains(e.target)) {
            panel.classList.remove('open');
            isOpen = false;
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            panel.classList.remove('open');
            isOpen = false;
        }
    });

    markAll.addEventListener('click', (e) => {
        e.stopPropagation();
        markAllRead();
    });
})();
</script>
@endauth

{{-- ===== UNIVERSAL MODAL BACK BUTTON ===== --}}
<style>
    .pp-modal-back-btn {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(201, 169, 97, 0.12);
        border: 1px solid rgba(201, 169, 97, 0.3);
        color: #c9a961;
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.15s ease;
        z-index: 10;
        -webkit-tap-highlight-color: transparent;
        padding: 0;
        font-family: inherit;
    }
    .pp-modal-back-btn:hover {
        background: rgba(201, 169, 97, 0.25);
        border-color: #c9a961;
        color: #fff;
        transform: translateX(-2px);
    }
    .pp-modal-back-btn:active {
        transform: scale(0.92);
    }
    .pp-modal-back-btn svg {
        display: block;
    }

        /* ============================================
           3-COLUMN STATS (para sa Reports + Payments)
           ============================================ */
        .pp-stats.stats-3col {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
        }
        .pp-stats.stats-3col .pp-stat {
            min-width: 0;
            overflow: hidden;
            padding: 14px 10px !important;
        }
        .pp-stats.stats-3col .pp-stat-icon {
            width: 32px !important;
            height: 32px !important;
            margin-bottom: 8px !important;
        }
        .pp-stats.stats-3col .pp-stat-icon svg {
            width: 15px !important;
            height: 15px !important;
        }
        .pp-stats.stats-3col .pp-stat-value {
            font-size: 17px !important;
            font-weight: 800 !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.02em;
        }
        .pp-stats.stats-3col .pp-stat-label {
            font-size: 9px !important;
            letter-spacing: 0.06em !important;
            margin-top: 3px !important;
        }

        /* Mobile — keep 3 columns pero compact */
        @media (max-width: 480px) {
            .pp-stats.stats-3col {
                gap: 6px !important;
            }
            .pp-stats.stats-3col .pp-stat {
                padding: 12px 8px !important;
                border-radius: 11px !important;
            }
            .pp-stats.stats-3col .pp-stat-icon {
                width: 28px !important;
                height: 28px !important;
                margin-bottom: 6px !important;
                border-radius: 8px !important;
            }
            .pp-stats.stats-3col .pp-stat-icon svg {
                width: 13px !important;
                height: 13px !important;
            }
            .pp-stats.stats-3col .pp-stat-value {
                font-size: 14px !important;
            }
            .pp-stats.stats-3col .pp-stat-label {
                font-size: 7.5px !important;
                letter-spacing: 0.04em !important;
            }
        }

        @media (max-width: 360px) {
            .pp-stats.stats-3col .pp-stat-value {
                font-size: 12px !important;
            }
            .pp-stats.stats-3col .pp-stat-label {
                font-size: 7px !important;
            }
        }
    </style>

<script>
(function() {
    // Auto-inject back button sa tanan modal headers
    function injectBackButtons() {
        const modals = document.querySelectorAll(
            '.pp-modal, .cr-modal, .ship-modal, .appr-modal, .rej-modal, .pp-modal-overlay > div, [class*="modal-overlay"] > [class*="modal"]'
        );

        modals.forEach(function(modal) {
            // Skip kung naa nay back button
            if (modal.querySelector('.pp-modal-back-btn')) return;

            // Skip kung naa nay close button sa upper-left
            const backBtn = document.createElement('button');
            backBtn.type = 'button';
            backBtn.className = 'pp-modal-back-btn';
            backBtn.setAttribute('aria-label', 'Back');
            backBtn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';

            backBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal(modal);
            });

            // Insert as first child
            modal.insertBefore(backBtn, modal.firstChild);

            // Ensure modal is positioned
            if (getComputedStyle(modal).position === 'static') {
                modal.style.position = 'relative';
            }
        });
    }

    function closeModal(modal) {
        // Try common close mechanisms
        const closeBtn = modal.querySelector(
            '.pp-modal-close, .cr-modal-btn-cancel, .ship-modal-btn-cancel, .appr-modal-btn-cancel, .rej-modal-btn-cancel, [data-modal-close], [data-cr-close], [data-appr-close], [data-rej-close]'
        );

        if (closeBtn) {
            closeBtn.click();
            return;
        }

        // Fallback: hide directly
        const overlay = modal.closest('[class*="overlay"]') || modal.parentElement;
        if (overlay) {
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    // Run on page load
    injectBackButtons();

    // Run when new modals appear (dynamic)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            if (m.addedNodes.length) {
                setTimeout(injectBackButtons, 100);
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Also poll every 2s para sigurado
    setInterval(injectBackButtons, 2000);
})();
</script>

{{-- ===== ROBUST REFRESH HANDLER ===== --}}
<script>
(function() {
    'use strict';

    // Global refresh function (mas reliable)
    window.pRefresh = function() {
        console.log('[Refresh] Triggered');
        var btn = document.getElementById('pRefreshBtn');
        var icon = document.getElementById('pRefreshIcon');

        if (btn) btn.disabled = true;
        if (icon) {
            icon.style.transition = 'transform 0.8s linear';
            icon.style.transform = 'rotate(360deg)';
        }

        // Clear caches
        try {
            sessionStorage.clear();
            if (window.__navCache) window.__navCache = {};
            if (window.__pageCache) window.__pageCache = {};
        } catch (e) {}

        // Force fresh URL (cache buster) — works on mobile
        setTimeout(function() {
            var url = window.location.pathname + window.location.search;
            var sep = url.indexOf('?') >= 0 ? '&' : '?';
            window.location.replace(url + sep + '_r=' + Date.now());
        }, 250);
    };

    // Backup: attach click listener (para kung onclick attribute wala mo-work)
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('pRefreshBtn');
        if (btn && !btn._refreshAttached) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                window.pRefresh();
            });
            btn._refreshAttached = true;
            console.log('[Refresh] Listener attached to button');
        }
    });

    // Re-attach kung naay dynamic na button (after page swap sa instant nav)
    document.addEventListener('click', function(e) {
        var target = e.target.closest('#pRefreshBtn');
        if (target && !target._refreshAttached) {
            e.preventDefault();
            window.pRefresh();
        }
    }, true);

    // Also intercept sa instant nav cache (skip ni sa reload)
    // Removed: was clearing cache on every navigation
        } catch (e) {}
    });

    console.log('[Refresh] Robust handler ready');
})();
</script>

{{-- ========== MOBILE REFRESH — TOUCH OPTIMIZED ========== --}}
<script>
(function() {
    'use strict';
    var REFRESHING = false;

    function trigger(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        }
        if (REFRESHING) return;
        REFRESHING = true;

        var btn = document.getElementById('pRefreshBtn');
        var icon = document.getElementById('pRefreshIcon');
        if (btn) btn.disabled = true;
        if (icon) {
            icon.style.transition = 'transform 0.8s linear';
            icon.style.transform = 'rotate(360deg)';
        }

        try {
            sessionStorage.clear();
            if (window.__navCache) window.__navCache = {};
            if (window.__pageCache) window.__pageCache = {};
        } catch (err) {}

        setTimeout(function() {
            var url = window.location.pathname + window.location.search;
            var sep = url.indexOf('?') >= 0 ? '&' : '?';
            window.location.replace(url + sep + '_r=' + Date.now());
        }, 250);
    }

    window.pRefresh = trigger;

    function attach() {
        var btn = document.getElementById('pRefreshBtn');
        if (!btn || btn._mobRefresh) return;

        // Multiple event types for max mobile compatibility
        btn.addEventListener('click', trigger, false);
        btn.addEventListener('touchend', trigger, { passive: false });
        btn.addEventListener('touchstart', function(e) {
            e.stopPropagation();
        }, { passive: true });

        btn._mobRefresh = true;
        console.log('[Portal] Mobile refresh attached');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attach);
    } else {
        attach();
    }
    window.addEventListener('pageshow', attach);

    // Global fallback — capture phase
    document.addEventListener('click', function(e) {
        var t = e.target && e.target.closest && e.target.closest('#pRefreshBtn');
        if (t) trigger(e);
    }, true);

    document.addEventListener('touchend', function(e) {
        var t = e.target && e.target.closest && e.target.closest('#pRefreshBtn');
        if (t) trigger(e);
    }, { passive: false, capture: true });

    console.log('[Portal] portalMobileRefresh ready');
})();
</script>
</body>
</html>