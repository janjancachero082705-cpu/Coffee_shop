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
        <a href="{{ route('portal.reports.index') }}" class="p-nav-item {{ request()->routeIs('portal.reports.*') ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
            </svg>
            Sales
        </a>
        <a href="{{ route('portal.payments.index') }}" class="p-nav-item {{ request()->routeIs('portal.payments.*') ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
            Payments
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

<script>
    // Refresh button
    function pRefresh() {
        var btn = document.getElementById('pRefreshBtn');
        var icon = document.getElementById('pRefreshIcon');
        if (btn) btn.classList.add('spinning');
        try { sessionStorage.setItem('p_refreshed', '1'); } catch (e) {}
        setTimeout(function() { window.location.reload(); }, 200);
    }

    // Toast
    function pShowToast(msg) {
        var t = document.getElementById('pToast');
        var txt = document.getElementById('pToastText');
        if (!t || !txt) return;
        txt.textContent = msg;
        t.classList.add('show');
        setTimeout(function() { t.classList.remove('show'); }, 2000);
    }

    // On load — check kung gikan sa refresh
    window.addEventListener('load', function() {
        try {
            if (sessionStorage.getItem('p_refreshed')) {
                sessionStorage.removeItem('p_refreshed');
                setTimeout(function() { pShowToast('Updated!'); }, 200);
            }
        } catch (e) {}
    });
</script>


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
</body>
</html>