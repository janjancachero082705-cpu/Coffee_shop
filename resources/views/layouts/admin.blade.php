<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') Ãƒâ€šÃ‚Â· Coffee Consignment</title>
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

        html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; }

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

        .app { min-height: 100vh; display: flex; width: 100%; position: relative; }

        .sidebar {
            width: 240px; min-width: 240px; max-width: 240px;
            height: 100vh; position: fixed; top: 0; left: 0; z-index: 100;
            display: flex; flex-direction: column;
            padding: 20px 12px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            overflow-y: auto;
        }

        .sidebar::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 200px;
            background: radial-gradient(ellipse at top, rgba(169, 120, 74, 0.08), transparent 70%);
            pointer-events: none;
        }

        .sidebar > * { position: relative; z-index: 1; }

        .brand {
            display: flex; align-items: center; gap: 10px;
            padding: 0 8px 20px; margin-bottom: 16px;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .brand-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            display: grid; place-items: center; font-size: 19px; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 16px -4px rgba(169, 120, 74, 0.6);
        }

        .brand-name {
            font-size: 14px; font-weight: 800; color: var(--text-primary);
            letter-spacing: -.01em; line-height: 1.2;
        }

        .brand-role {
            font-size: 9px; font-weight: 700; color: var(--accent);
            text-transform: uppercase; letter-spacing: .12em; margin-top: 2px;
        }

        .nav-section { margin-bottom: 20px; }

        .nav-label {
            font-size: 9px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .15em;
            padding: 0 12px; margin-bottom: 8px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 12px; border-radius: var(--radius-sm);
            color: var(--text-secondary); font-size: 13px; font-weight: 500;
            transition: all .15s ease; position: relative; margin-bottom: 2px;
        }

        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }

        .nav-item:hover { background: var(--sidebar-hover); color: var(--text-primary); }

        .nav-item.active {
            background: var(--sidebar-active);
            color: var(--accent-light); font-weight: 600;
        }

        .nav-item.active::before {
            content: ''; position: absolute; left: -12px; top: 50%;
            transform: translateY(-50%); width: 3px; height: 20px;
            background: var(--accent); border-radius: 0 3px 3px 0;
            box-shadow: 0 0 8px rgba(169, 120, 74, 0.6);
        }

        .nav-item.disabled { opacity: .35; cursor: not-allowed; pointer-events: none; }

        .nav-badge {
            margin-left: auto; background: var(--danger-bg); color: var(--danger);
            font-size: 10px; font-weight: 700; padding: 2px 7px;
            border-radius: 5px; min-width: 20px; text-align: center;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .sidebar-bottom { margin-top: auto; padding-top: 16px; }

        .today-card {
            padding: 16px; margin: 0 4px 12px; border-radius: var(--radius);
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff; position: relative; overflow: hidden;
            box-shadow: 0 12px 24px -8px rgba(169, 120, 74, 0.5);
        }

        .today-card::after {
            content: ''; position: absolute; right: -25px; bottom: -25px;
            width: 90px; height: 90px; border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .today-label {
            font-size: 10px; font-weight: 700; opacity: 0.85;
            text-transform: uppercase; letter-spacing: .1em;
            margin-bottom: 6px; position: relative; z-index: 1;
        }

        .today-value {
            font-size: 22px; font-weight: 800; letter-spacing: -.02em;
            line-height: 1; margin-bottom: 8px; position: relative; z-index: 1;
        }

        .today-meta {
            display: flex; gap: 10px; font-size: 11px; opacity: 0.9;
            font-weight: 500; position: relative; z-index: 1;
        }

        .main {
            flex: 1; min-width: 0; min-height: 100vh; margin-left: 240px;
            display: flex; flex-direction: column; position: relative;
            background-color: #1a1a22;
            background-image:
                linear-gradient(180deg, rgba(20, 20, 26, 0.65), rgba(15, 15, 20, 0.78)),
                url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            background-attachment: fixed; background-repeat: no-repeat;
        }

        .topbar {
            position: sticky; top: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; padding: 16px 28px;
            background: rgba(20, 20, 26, 0.85);
            backdrop-filter: blur(20px) saturate(1.3);
            -webkit-backdrop-filter: blur(20px) saturate(1.3);
            border-bottom: 1px solid var(--border);
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }

        .menu-toggle {
            display: none; width: 36px; height: 36px; place-items: center;
            border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
            color: var(--text-secondary); background: rgba(255, 255, 255, 0.02);
        }

        .menu-toggle svg { width: 18px; height: 18px; }

        .page-title {
            font-size: 18px; font-weight: 700; color: var(--text-primary);
            letter-spacing: -.02em; line-height: 1.2;
        }

        .page-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .topbar-right { display: flex; align-items: center; gap: 8px; }

        .chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 13px; border: 1px solid var(--border);
            border-radius: var(--radius-sm); background: rgba(255, 255, 255, 0.03);
            font-size: 12px; font-weight: 500; color: var(--text-secondary);
            white-space: nowrap;
        }

        .chip-icon { color: var(--text-muted); display: flex; }

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
            box-shadow: 0 4px 10px -2px rgba(169, 120, 74, 0.5);
        }

        .user-name { font-size: 12px; font-weight: 600; color: var(--text-primary); line-height: 1.1; }

        .user-role {
            font-size: 10px; color: var(--accent);
            text-transform: uppercase; letter-spacing: .06em; font-weight: 700;
        }

        .content { flex: 1; padding: 24px 28px 32px; }

        .card {
            background: rgba(34, 34, 44, 0.55);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-lg); padding: 20px; margin-bottom: 16px;
        }

        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 16px; gap: 12px;
        }

        .card-title {
            font-size: 14px; font-weight: 700; color: var(--text-primary);
            letter-spacing: -.01em;
        }

        .card-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 14px; margin-bottom: 20px;
        }

        .stat {
            padding: 20px; background: rgba(34, 34, 44, 0.6);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-lg);
            transition: all .2s ease; position: relative; overflow: hidden;
        }

        .stat::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            opacity: 0; transition: opacity .25s ease;
        }

        .stat:hover {
            transform: translateY(-3px); border-color: var(--accent-border);
            box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.5);
        }

        .stat:hover::before { opacity: 1; }

        .stat-head {
            display: flex; justify-content: space-between;
            align-items: flex-start; margin-bottom: 14px;
        }

        .stat-icon {
            width: 42px; height: 42px; border-radius: 10px;
            background: var(--accent-bg); border: 1px solid var(--accent-border);
            display: grid; place-items: center;
            font-size: 20px; color: var(--accent-light);
        }

        .stat-trend {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 5px;
        }
        .stat-trend.up { background: var(--success-bg); color: var(--success); }
        .stat-trend.down { background: var(--danger-bg); color: var(--danger); }
        .stat-trend.flat { background: var(--warning-bg); color: var(--warning); }

        .stat-label {
            font-size: 10px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .1em; margin-bottom: 6px;
        }

        .stat-value {
            font-size: 24px; font-weight: 800; color: var(--text-primary);
            letter-spacing: -.03em; line-height: 1; margin-bottom: 6px;
        }

        .stat-meta { font-size: 11px; color: var(--text-muted); font-weight: 500; }

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
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 22px -8px rgba(169, 120, 74, 0.7);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-secondary); border-color: var(--border-strong);
        }
        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--accent-light); border-color: var(--accent-border);
        }

        .btn-danger {
            background: var(--danger-bg); color: var(--danger);
            border-color: rgba(239, 68, 68, 0.2);
        }
        .btn-danger:hover { background: rgba(239, 68, 68, 0.2); }

        .btn-sm { padding: 6px 12px; font-size: 11px; }

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

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(169, 120, 74, 0.03); }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 5px;
            font-size: 11px; font-weight: 700;
        }

        .badge-pending { background: var(--warning-bg); color: var(--warning); }
        .badge-completed, .badge-active, .badge-paid { background: var(--success-bg); color: var(--success); }
        .badge-cancelled, .badge-suspended, .badge-overdue { background: var(--danger-bg); color: var(--danger); }
        .badge-info, .badge-inactive { background: var(--info-bg); color: var(--info); }
        .badge-partial, .badge-verified { background: var(--accent-bg); color: var(--accent-light); }

        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px; border-radius: var(--radius);
            font-size: 13px; font-weight: 500; margin-bottom: 16px; border: 1px solid;
        }

        .alert-success {
            background: var(--success-bg); color: var(--success);
            border-color: rgba(34, 197, 94, 0.25);
        }

        .alert-error {
            background: var(--danger-bg); color: var(--danger);
            border-color: rgba(239, 68, 68, 0.25);
        }

        .input, select, textarea {
            width: 100%; padding: 10px 12px;
            background: rgba(20, 20, 26, 0.6);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-sm);
            color: var(--text-primary); font-size: 13px; font-family: inherit;
            outline: none; transition: all .15s;
        }

        .input::placeholder, textarea::placeholder { color: var(--text-muted); }
        .input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            background: rgba(20, 20, 26, 0.9);
            box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
        }

        .label {
            display: block; font-size: 11px; font-weight: 700;
            color: var(--text-secondary); margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: .06em;
        }

        .grid { display: grid; gap: 16px; }
        .grid-2 { grid-template-columns: 1fr 1fr; }
        .grid-3 { grid-template-columns: 1fr 1fr 1fr; }

        .foot {
            display: flex; justify-content: space-between;
            padding: 16px 28px; border-top: 1px solid var(--border);
            font-size: 11px; color: var(--text-muted);
        }

        .scrim {
            display: none !important; visibility: hidden; opacity: 0;
            pointer-events: none;
        }

        .empty {
            text-align: center; padding: 60px 20px;
            color: var(--text-muted);
        }
        .empty-icon {
            width: 56px; height: 56px; border-radius: 14px;
            background: var(--accent-bg); border: 1px solid var(--accent-border);
            display: grid; place-items: center; margin: 0 auto 16px;
            color: var(--accent-light); font-size: 24px;
        }
        .empty-title {
            font-size: 15px; font-weight: 700;
            color: var(--text-primary); margin-bottom: 6px;
        }
        .empty-text { font-size: 12px; color: var(--text-muted); margin-bottom: 18px; }

        .info-row {
            display: flex; justify-content: space-between;
            padding: 10px 0; border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-muted); }
        .info-value { color: var(--text-primary); font-weight: 600; }

        @media (max-width: 1100px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s ease; }
            body.nav-open .sidebar { transform: translateX(0); }
            .main { margin-left: 0; }
            .menu-toggle { display: grid; }
            .content { padding: 16px; }
            .topbar { padding: 12px 16px; }
            .stats-grid { grid-template-columns: 1fr; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .chip:not(.chip-user) { display: none; }
            body.nav-open .scrim {
                display: block !important; visibility: visible; opacity: 1;
                position: fixed; inset: 0; z-index: 90;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(2px); pointer-events: auto;
            }
        }
    
    /* ============================================ */
    /* PROFESSIONAL SIDEBAR ENHANCEMENTS            */
    /* ============================================ */

    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 2px;
        margin-right: -2px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 8px 20px;
        margin-bottom: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .brand-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #c9a961 0%, #8a5f36 100%);
        display: grid;
        place-items: center;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow:
            0 8px 16px -4px rgba(169, 120, 74, 0.55),
            0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        transition: transform 0.3s ease;
    }

    .brand-icon:hover {
        transform: scale(1.05) rotate(-3deg);
    }

    .brand-icon svg {
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
    }

    .brand-text {
        min-width: 0;
        overflow: hidden;
    }

    .brand-name {
        font-size: 14px;
        font-weight: 800;
        color: #f5f3f0;
        letter-spacing: -0.01em;
        line-height: 1.2;
        white-space: nowrap;
    }

    .brand-role {
        font-size: 9px;
        font-weight: 700;
        color: #c9a961;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        margin-top: 3px;
        white-space: nowrap;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 9px;
        color: #a8a5a0;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        margin-bottom: 2px;
        text-decoration: none;
        cursor: pointer;
    }

    .nav-item svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        transition: transform 0.18s ease;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.04);
        color: #f5f3f0;
    }

    .nav-item:hover svg {
        transform: scale(1.08);
        color: #c9a961;
    }

    .nav-item.active {
        background: linear-gradient(90deg, rgba(169, 120, 74, 0.18), rgba(169, 120, 74, 0.06));
        color: #f5f3f0;
        font-weight: 600;
    }

    .nav-item.active svg {
        color: #c9a961;
    }

    .nav-item.active::before {
        content: '';
        position: absolute;
        left: -12px;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 22px;
        background: linear-gradient(180deg, #c9a961, #a9784a);
        border-radius: 0 3px 3px 0;
        box-shadow: 0 0 12px rgba(201, 169, 97, 0.55);
    }

    .nav-count {
        margin-left: auto;
        padding: 2px 8px;
        background: rgba(169, 120, 74, 0.18);
        color: #c9a961;
        font-size: 10px;
        font-weight: 800;
        border-radius: 10px;
        min-width: 22px;
        text-align: center;
        line-height: 1.4;
        border: 1px solid rgba(169, 120, 74, 0.25);
    }

    .nav-count-info {
        background: rgba(59, 130, 246, 0.18);
        color: #60a5fa;
        border-color: rgba(59, 130, 246, 0.3);
    }

    .nav-count-warn {
        background: rgba(245, 158, 11, 0.18);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.3);
    }

    .sidebar-bottom {
        margin-top: auto;
        padding-top: 16px;
    }

    .today-card {
        position: relative;
        padding: 16px;
        margin: 0 4px 12px;
        border-radius: 14px;
        background: linear-gradient(135deg, #b8845a 0%, #8a5f36 100%);
        color: #ffffff;
        overflow: hidden;
        box-shadow:
            0 12px 24px -10px rgba(169, 120, 74, 0.6),
            0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .today-card:hover {
        transform: translateY(-2px);
        box-shadow:
            0 16px 32px -12px rgba(169, 120, 74, 0.75),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }

    .today-glow {
        position: absolute;
        top: -40px;
        right: -40px;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18), transparent 70%);
        pointer-events: none;
    }

    .today-content {
        position: relative;
        z-index: 1;
    }

    .today-label {
        font-size: 10px;
        font-weight: 800;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 8px;
    }

    .today-value {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1;
        margin-bottom: 12px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .today-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 10.5px;
        opacity: 0.95;
        font-weight: 600;
    }

    .today-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .today-meta-sep {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
    }

    .logout-form {
        margin: 0;
    }

    .logout-btn {
        width: 100%;
        background: rgba(239, 68, 68, 0.06);
        color: #a8a5a0;
        border: 1px solid transparent;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        padding: 10px 12px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .logout-btn:hover {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.2);
    }

    .logout-btn:hover svg {
        color: #ef4444;
        transform: translateX(2px);
    }

    .logout-btn svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        transition: all 0.18s ease;
    }
</style>
    @stack('styles')
</head>
<body>

@php
    try {
        $navStoreCount = \App\Models\Store::count();
        $navPendingPayments = \App\Models\ConsignmentPayment::whereDate('created_at', today())->count();
        $navTodayDeliveries = \App\Models\DeliveryReceipt::whereDate('delivery_date', today())->count();
        $navTodaySales = (float) \App\Models\ConsignmentPayment::whereDate('payment_date', today())->sum('amount');
    } catch (\Throwable $e) {
        $navStoreCount = 0; $navPendingPayments = 0; $navTodayDeliveries = 0; $navTodaySales = 0;
    }
@endphp

<div class="app">
    <aside class="sidebar">
    {{-- BRAND --}}
    <div class="brand">
        <div class="brand-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                <line x1="6" y1="1" x2="6" y2="4"/>
                <line x1="10" y1="1" x2="10" y2="4"/>
                <line x1="14" y1="1" x2="14" y2="4"/>
            </svg>
        </div>
        <div class="brand-text">
            <div class="brand-name">Coffee Beans</div>
            <div class="brand-role">Consignment</div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">

        <div class="nav-section">
            <div class="nav-label">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="9" rx="1"/>
                    <rect x="14" y="3" width="7" height="5" rx="1"/>
                    <rect x="14" y="12" width="7" height="9" rx="1"/>
                    <rect x="3" y="16" width="7" height="5" rx="1"/>
                </svg>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Consignment</div>

            <a href="{{ route('stores.index') }}" class="nav-item {{ request()->routeIs('stores.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <path d="M9 22V12h6v10"/>
                </svg>
                <span>Stores</span>
                @if(isset($navStoreCount) && $navStoreCount > 0)
                    <span class="nav-count">{{ $navStoreCount }}</span>
                @endif
            </a>

            <a href="{{ route('deliveries.index') }}" class="nav-item {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                <span>Deliveries</span>
                @if(isset($navTodayDeliveries) && $navTodayDeliveries > 0)
                    <span class="nav-count nav-count-info">{{ $navTodayDeliveries }}</span>
                @endif
            </a>

            <a href="{{ route('consignment.reports.index') }}" class="nav-item {{ request()->routeIs('consignment.reports.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 17V7"/>
                    <path d="M4 20h16"/>
                    <path d="M9 7a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V7z"/>
                    <path d="M14 12h6"/>
                    <path d="M14 16h6"/>
                    <path d="M14 8h6"/>
                </svg>
                <span>Sales Reports</span>
            </a>

            <a href="{{ route('consignment.payments.index') }}" class="nav-item {{ request()->routeIs('consignment.payments.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <path d="M2 10h20"/>
                    <circle cx="12" cy="15" r="1"/>
                </svg>
                <span>Payments</span>
                @if(isset($navPendingPayments) && $navPendingPayments > 0)
                    <span class="nav-count nav-count-warn">{{ $navPendingPayments }}</span>
                @endif
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Products</div>
            <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Products</span>
            </a>
        </div>

    </nav>

    {{-- BOTTOM --}}
    <div class="sidebar-bottom">

        <div class="today-card">
            <div class="today-glow"></div>
            <div class="today-content">
                <div class="today-label">Today's Collected</div>
                <div class="today-value">&#8369;{{ number_format($navTodaySales ?? 0, 0) }}</div>
                <div class="today-meta">
                    <span class="today-meta-item">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <rect x="1" y="3" width="15" height="13" rx="1"/>
                            <path d="M16 8h4l3 3v5h-7V8z"/>
                        </svg>
                        {{ $navTodayDeliveries ?? 0 }} deliveries
                    </span>
                    <span class="today-meta-sep"></span>
                    <span class="today-meta-item">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        {{ $navStoreCount ?? 0 }} stores
                    </span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="nav-item logout-btn">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>

    </div>
</aside>

    <div class="scrim" onclick="document.body.classList.remove('nav-open')"></div>

    <main class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" onclick="document.body.classList.toggle('nav-open')">
                    <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <div class="page-title">@yield('title', 'Dashboard')</div>
                    <div class="page-sub">@yield('subtitle', now()->format('l, F j, Y'))</div>
                </div>
            </div>
            <div class="topbar-right">
                <div class="chip">
                    <span class="chip-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
                    {{ now()->format('g:i A') }}
                </div>
                @yield('actions')
                <div class="user-menu">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                    <div>
                        <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="user-role">Admin</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    <div>
                        <strong>Please fix:</strong>
                        <ul style="margin:4px 0 0 16px; font-size:12px;">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="foot">
            <span>&copy; {{ now()->format('Y') }} Coffee Beans - Consignment System</span>
            <span>v1.0</span>
        </footer>
    </main>
</div>

<script>
    document.querySelectorAll('.sidebar .nav-item:not(.disabled)').forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 900) document.body.classList.remove('nav-open');
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.body.classList.remove('nav-open');
    });
    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) document.body.classList.remove('nav-open');
    });
</script>

@stack('scripts')
</body>
</html>