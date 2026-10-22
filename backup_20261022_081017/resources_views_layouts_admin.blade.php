<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f0f14">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'Admin') - Coffee Consignment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">
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
            --sidebar-width: 260px;
            --topbar-height: 64px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #0f0f14;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-primary);
            font-size: 13px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            background: var(--sidebar-bg);
            overscroll-behavior: none;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; border: none; background: none; color: inherit; }
        svg { display: block; }
        img { max-width: 100%; height: auto; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(169, 120, 74, 0.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(169, 120, 74, 0.4); }

        .app {
            height: 100vh;
            height: 100dvh;
            display: flex;
            width: 100%;
            position: relative;
        }

        .sidebar-scrim {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 99;
            opacity: 0;
            transition: opacity 0.25s ease;
            pointer-events: none;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            padding: 20px 12px 24px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            overflow: hidden;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-close {
            display: none;
            position: absolute;
            top: 18px;
            right: 12px;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-secondary);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            transition: all 0.15s;
        }
        .sidebar-close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }
        .sidebar-close svg { width: 18px; height: 18px; }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            margin-bottom: 12px;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        .brand {
            display: flex; align-items: center; gap: 14px;
            padding: 4px 6px 22px; margin-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            flex-shrink: 0;
        }

        .brand-icon {
            width: 68px;
            height: 68px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            overflow: hidden;
            background: #FFFFFF;
            padding: 5px;
            border: 2px solid rgba(201, 169, 97, 0.45);
            box-shadow:
                0 0 0 1px rgba(201, 169, 97, 0.15),
                0 6px 18px -4px rgba(0, 0, 0, 0.4);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand:hover .brand-icon {
            transform: scale(1.04);
            border-color: rgba(201, 169, 97, 0.75);
            box-shadow:
                0 0 0 1px rgba(201, 169, 97, 0.25),
                0 8px 22px -4px rgba(201, 169, 97, 0.5);
        }

        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            border-radius: 9px;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
            flex: 1;
        }

        .brand-name {
            font-family: 'Cinzel', 'Playfair Display', Georgia, serif;
            font-size: 30px;
            font-weight: 900;
            letter-spacing: 0.08em;
            color: #c9a961;
            line-height: 1;
            text-transform: uppercase;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .brand-line {
            font-family: 'Marcellus', 'Cinzel', Georgia, serif;
            font-size: 11.5px;
            font-weight: 400;
            letter-spacing: 0.12em;
            color: #d4d1cc;
            line-height: 1.3;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .brand-tag {
            font-family: 'Marcellus', 'Cinzel', Georgia, serif;
            font-size: 9.5px;
            font-weight: 400;
            letter-spacing: 0.18em;
            color: #0f0f14;
            text-transform: uppercase;
            line-height: 1;
            margin-top: 6px;
            padding: 5px 9px;
            background: linear-gradient(135deg, #c9a961 0%, #a9784a 100%);
            border-radius: 4px;
            display: inline-block;
            width: fit-content;
            box-shadow: 0 2px 6px rgba(169, 120, 74, 0.4);
        }

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
            min-height: 42px;
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
            min-height: 42px;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
        }
        .logout-btn svg { width: 17px; height: 17px; flex-shrink: 0; }

        .main {
            flex: 1;
            min-width: 0;
            min-height: 100dvh;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            background-color: #1a1a22;
            background-image: linear-gradient(180deg, rgba(20, 20, 26, 0.65), rgba(15, 15, 20, 0.78)),
                url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: scroll;
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
            min-height: var(--topbar-height);
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; }
        .topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

        .menu-toggle {
            display: none;
            width: 42px; height: 42px;
            place-items: center;
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.02);
            flex-shrink: 0;
            transition: all 0.15s;
        }
        .menu-toggle:active {
            background: rgba(169, 120, 74, 0.15);
            transform: scale(0.96);
        }
        .menu-toggle svg { width: 20px; height: 20px; }

        .page-title { font-size: 18px; font-weight: 700; color: var(--text-primary); letter-spacing: -.02em; line-height: 1.2; }
        .page-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .topbar-title-group { min-width: 0; flex: 1; }
        .topbar-title-group .page-title,
        .topbar-title-group .page-sub {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 13px; border: 1px solid var(--border);
            border-radius: var(--radius-sm); background: rgba(255, 255, 255, 0.03);
            font-size: 12px; font-weight: 500; color: var(--text-secondary);
            white-space: nowrap;
        }

        .user-menu {
            display: flex; align-items: center; gap: 9px;
            padding: 4px 14px 4px 4px; border: 1px solid var(--border);
            border-radius: var(--radius-sm); background: rgba(255, 255, 255, 0.03);
            flex-shrink: 0;
        }

        .user-avatar {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            display: grid; place-items: center; color: #fff;
            font-size: 13px; font-weight: 800;
            flex-shrink: 0;
        }

        .user-name { font-size: 12px; font-weight: 600; color: var(--text-primary); line-height: 1.1; }
        .user-role { font-size: 10px; color: var(--accent); text-transform: uppercase; letter-spacing: .06em; font-weight: 700; }

        .content { flex: 1; padding: 24px 28px 32px; min-width: 0; }

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
            min-height: 38px;
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
            white-space: nowrap;
        }
        tbody td {
            padding: 14px 12px; font-size: 13px;
            border-bottom: 1px solid var(--border); color: var(--text-secondary);
        }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 5px;
            font-size: 11px; font-weight: 700;
            white-space: nowrap;
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

        .universal-back-btn {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-secondary, #a1a1aa);
            display: grid; place-items: center;
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
        .universal-back-btn:active { transform: scale(0.94); }
        .universal-back-btn svg { width: 16px; height: 16px; flex-shrink: 0; }

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

        @media (max-width: 1100px) {
            .content { padding: 20px 20px 28px; }
            .topbar { padding: 14px 20px; }
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                width: min(84vw, 300px);
                min-width: min(84vw, 300px);
                max-width: min(84vw, 300px);
                box-shadow: 20px 0 60px -20px rgba(0, 0, 0, 0.9);
            }
            body.nav-open .sidebar {
                transform: translateX(0);
            }
            body.nav-open .sidebar-scrim {
                display: block;
                opacity: 1;
                pointer-events: auto;
            }
            .sidebar-close { display: flex; }
            .brand { padding-right: 48px; }

            .main { margin-left: 0; }

            .menu-toggle { display: grid; }

            .topbar { padding: 12px 14px; gap: 10px; }
            .content { padding: 16px 14px 24px; }

            .page-title { font-size: 15px; }
            .page-sub { font-size: 11px; }

            .chip { display: none; }
            .user-menu > div:last-child { display: none; }
            .user-menu { padding: 4px; gap: 0; }

            .main {
                background-attachment: scroll;
            }

            .foot {
                flex-direction: column;
                gap: 4px;
                align-items: center;
                padding: 14px 16px;
                text-align: center;
            }

            .content table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                width: 100%;
            }
            .content table thead,
            .content table tbody {
                display: table;
                width: 100%;
                min-width: 640px;
            }

            .input, select, textarea {
                font-size: 16px !important;
                min-height: 44px;
            }

            .btn {
                min-height: 44px;
                padding: 11px 16px;
            }

            .card { padding: 16px; border-radius: 12px; }
        }

        @media (max-width: 600px) {
            .notif-dropdown {
                position: fixed;
                top: 62px;
                left: 10px;
                right: 10px;
                width: auto;
                max-width: none;
                border-radius: 12px;
            }
            .notif-body { max-height: 55vh; }

            .universal-back-btn {
                width: 34px;
                height: 34px;
                margin-right: 2px;
            }
            .universal-back-btn svg { width: 14px; height: 14px; }

            .menu-toggle { width: 40px; height: 40px; }
            .menu-toggle svg { width: 18px; height: 18px; }

            .topbar { padding: 10px 12px; }
            .content { padding: 14px 12px 20px; }
        }

        @media (max-width: 400px) {
            .user-menu { display: none; }
            .page-title { font-size: 14px; }
        }

        @supports (padding: env(safe-area-inset-bottom)) {
            .sidebar {
                padding-bottom: calc(24px + env(safe-area-inset-bottom));
                padding-top: calc(20px + env(safe-area-inset-top));
            }
            .content {
                padding-bottom: calc(32px + env(safe-area-inset-bottom));
            }
            .topbar {
                padding-top: max(12px, env(safe-area-inset-top));
            }
            .notif-dropdown {
                top: calc(62px + env(safe-area-inset-top));
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .sidebar, .sidebar-scrim, .notif-dropdown, .nav-item {
                transition: none !important;
            }
            .nav-count-badge { animation: none; }
        }

        @media print {
            .sidebar, .topbar, .sidebar-scrim { display: none !important; }
            .main { margin-left: 0 !important; background: #fff !important; color: #000; }
        }
    </style>
    @stack('styles')
<style id="MOBILE-BG-FIX">
html body::before {
    content: '' !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background-size: cover !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
    background-attachment: scroll !important;
    pointer-events: none !important;
    transform: translate3d(0, 0, 0) !important;
    -webkit-transform: translate3d(0, 0, 0) !important;
    backface-visibility: hidden !important;
    -webkit-backface-visibility: hidden !important;
    will-change: transform !important;
    z-index: -2 !important;
    opacity: 1 !important;
    display: block !important;
}

html body::after {
    content: '' !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    height: 100% !important;
    pointer-events: none !important;
    z-index: -1 !important;
    display: block !important;
}

html[data-theme="default"] body::before,
html:not([data-theme]) body::before {
    background-image: url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80') !important;
    background-color: #0f0f14 !important;
}
html[data-theme="default"] body::after,
html:not([data-theme]) body::after {
    background: linear-gradient(180deg, rgba(15, 15, 20, 0.72), rgba(10, 10, 16, 0.82)) !important;
}

html[data-theme="dark"] body::before {
    background-image: none !important;
    background-color: #050505 !important;
}
html[data-theme="dark"] body::after {
    background: none !important;
}

html[data-theme="light"] body::before {
    background-image: none !important;
    background-color: #f8fafc !important;
}
html[data-theme="light"] body::after {
    background: none !important;
}

html[data-theme="blue"] body::before {
    background-image: none !important;
    background-color: #eff6ff !important;
}
html[data-theme="blue"] body::after {
    background: none !important;
}

html[data-theme="default"] body,
html:not([data-theme]) body { background-color: #0f0f14 !important; }
html[data-theme="dark"] body    { background-color: #050505 !important; }
html[data-theme="light"] body   { background-color: #f8fafc !important; }
html[data-theme="blue"] body    { background-color: #eff6ff !important; }

@media (max-width: 768px) {
    html[data-theme="default"] body::before,
    html:not([data-theme]) body::before {
        background-image: url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=800&q=70') !important;
        background-position: center center !important;
    }

    html[data-theme="default"] body::after,
    html:not([data-theme]) body::after {
        background: linear-gradient(180deg, rgba(15, 15, 20, 0.78), rgba(10, 10, 16, 0.88)) !important;
    }
}

@media (max-width: 480px) {
    html[data-theme="default"] body::before,
    html:not([data-theme]) body::before {
        background-image: url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=600&q=65') !important;
    }
}

@supports (height: 100dvh) {
    html body::before,
    html body::after {
        height: 100dvh !important;
    }
}

@media (prefers-reduced-motion: reduce) {
    html body::before {
        will-change: auto !important;
    }
}
</style>
<style>
    /* FIX FOR NOTIFICATION BELL AND TOP HEADER */
    .admin-header, .top-header, .header, header, .navbar { 
        position: sticky !important; 
        top: 0 !important; 
        z-index: 9999 !important; 
        backdrop-filter: blur(10px) !important;
    }
    /* Kung floating widget ang bell, i-fix natin position */
    .notification-bell, .bell-icon {
        position: relative;
        z-index: 10000;
    }
</style>
</head>
<body>
    <div class="app">

        <div class="sidebar-scrim" id="sidebarScrim" onclick="closeSidebar()"></div>

        <aside class="sidebar" id="sidebar">
            <button type="button" class="sidebar-close" onclick="closeSidebar()" aria-label="Close menu">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="brand">
                <div class="brand-icon">
                    <img src="{{ asset('images/logo.png') }}" alt="RYS Farm Products Dealer">
                </div>
                <div class="brand-text">
                    <div class="brand-name">RYS</div>
                    <div class="brand-line">Farm Products Dealer</div>
                    <div class="brand-tag">Wholesale &amp; Retailer</div>
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
                    <a href="{{ route('stores.index') }}" class="nav-item {{ request()->routeIs('stores.*') || request()->routeIs('store-registrations.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                        Stores
                        @php $storeCount = \App\Models\Store::count(); @endphp
                        @if($storeCount > 0)<span class="nav-count">{{ $storeCount }}</span>@endif
                    </a>
                    <a href="{{ route('deliveries.index') }}" class="nav-item {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Deliveries
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
                        @endif
                    </a>
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

                <div class="nav-section">
                    <div class="nav-label">Settings</div>
                    <a href="{{ route('admin.settings.gcash') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                        E-Wallet Settings
                    </a>
                </div>
            </nav>

            <div class="sidebar-bottom">
                <div class="today-card">
                    <div class="today-label">Today's Collected</div>
                    <div class="today-value">&#8369;{{ number_format(\App\Models\ConsignmentPayment::verified()->whereDate('payment_date', today())->sum('amount') ?? 0, 2) }}</div>
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

        <div class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="menu-toggle" onclick="toggleSidebar()" aria-label="Open menu">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                    </button>
                    @unless(request()->routeIs('dashboard'))
                        <button type="button" class="universal-back-btn" data-fallback="{{ route('dashboard') }}" title="Go back">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        </button>
                    @endunless
                    <div class="topbar-title-group">
                        <div class="page-title">@yield('title', 'Dashboard')</div>
                        <div class="page-sub">@yield('subtitle', '')</div>
                    </div>
                </div>
                <div class="topbar-right">

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
                <div>&copy; {{ date('Y') }} RYS Farm Products Dealer - Consignment System</div>
                <div>v1.0</div>
            </footer>
        </div>
    </div>

    <script>
        function openSidebar() {
            document.body.classList.add('nav-open');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.body.classList.remove('nav-open');
            document.body.style.overflow = '';
        }
        function toggleSidebar() {
            if (document.body.classList.contains('nav-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        document.querySelectorAll('.sidebar .nav-item').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 900) {
                    closeSidebar();
                }
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && document.body.classList.contains('nav-open')) {
                closeSidebar();
            }
        });

        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (window.innerWidth >= 900) {
                    closeSidebar();
                }
            }, 150);
        });
    </script>

    <script>
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
                var avatar = o.store_avatar
                    ? '<img src="' + o.store_avatar + '" style="width:100%;height:100%;object-fit:cover;">'
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

                html += '<a href="' + (o.url || '#') + '" class="notif-item" data-id="' + o.id + '" data-url="' + (o.url || '') + '">';
                html += '<div class="notif-item-avatar">' + avatar + '</div>';
                html += '<div class="notif-item-body">';
                html += '<div class="notif-item-store">' + o.store_name + '</div>';
                html += '<div class="notif-item-meta">';
                html += '<span>' + label + ' - ' + o.ago + '</span>';
                html += amountHtml;
                html += '</div></div>';
                html += '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:#7a7a85;flex-shrink:0;align-self:center;"><path d="M9 18l6-6-6-6"/></svg>';
                html += '</a>';
            }
            body.innerHTML = html;
        }

        document.addEventListener('click', function(e) {
            var item = e.target.closest('.notif-item');
            if (!item) return;

            e.preventDefault();
            e.stopPropagation();

            var id = item.getAttribute('data-id');
            var url = item.getAttribute('data-url');

            item.style.opacity = '0.6';

            if (id) {
                fetch(markReadBase + '/' + id + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    keepalive: true
                }).catch(function() {});
            }

            if (url && url !== '' && url !== '#') {
                window.location.href = url;
            } else {
                var dd = document.getElementById('notifDropdown');
                if (dd) dd.classList.remove('open');
                item.style.opacity = '1';
                poll();
            }
        });

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
<!-- CUSTOM TOAST SYSTEM -->
<style>
    .toast-container { position: fixed; top: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 12px; pointer-events: none; }
    .custom-toast { 
        background: #1E293B; border: 1px solid #334155; border-left: 4px solid #10B981; 
        border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 14px;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5); min-width: 300px; max-width: 400px;
        pointer-events: auto; animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    .custom-toast.error { border-left-color: #EF4444; }
    .custom-toast.warning { border-left-color: #F59E0B; }
    
    .toast-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .custom-toast.success .toast-icon { background: rgba(16,185,129,0.15); color: #10B981; }
    .custom-toast.error .toast-icon { background: rgba(239,68,68,0.15); color: #EF4444; }
    .custom-toast.warning .toast-icon { background: rgba(245,158,11,0.15); color: #F59E0B; }
    
    .toast-content { flex: 1; }
    .toast-title { font-size: 13px; font-weight: 800; color: #F8FAFC; margin-bottom: 2px; }
    .toast-message { font-size: 12px; color: #94A3B8; line-height: 1.4; }
    
    .toast-close { background: none; border: none; color: #64748B; cursor: pointer; padding: 4px; transition: 0.2s; }
    .toast-close:hover { color: #F8FAFC; }
    
    @keyframes slideInRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slideOutRight { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(50px); } }
</style>

<div id="toast-container" class="toast-container"></div>

<script>
    window.showToast = function(title, message = '', type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = 'custom-toast ' + type;
        
        let iconSvg = '';
        if(type === 'success') iconSvg = '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>';
        else if(type === 'error') iconSvg = '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>';
        else iconSvg = '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>';
        
        toast.innerHTML = `
            <div class="toast-icon">${iconSvg}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                ${message ? `<div class="toast-message">${message}</div>` : ''}
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    };
</script>
<!-- END CUSTOM TOAST SYSTEM -->
</body>
</html>