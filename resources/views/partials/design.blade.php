{{-- ==================================================================
     SHARED DESIGN SYSTEM
     All pages extend this. Provides consistent components.
     Rule: NO literal unicode. Use HTML entities.
     ================================================================== --}}
<style>
    /* ================================================================
       DESIGN TOKENS
       ================================================================ */
    :root {
        --coffee-50:  #faf6f2;
        --coffee-100: #f0e6dc;
        --coffee-500: #96683f;
        --coffee-600: #7a5232;
        --accent: #a9784a;
        --accent-light: #c9a961;
        --accent-dark: #8a5f36;
        --accent-bg: rgba(169, 120, 74, 0.12);
        --accent-border: rgba(169, 120, 74, 0.25);

        --bg-dark: #1a1a22;
        --bg-darker: #14141a;
        --bg-card: rgba(34, 34, 44, 0.55);
        --bg-card-hover: rgba(34, 34, 44, 0.75);
        --bg-input: rgba(20, 20, 26, 0.6);

        --border: rgba(255, 255, 255, 0.06);
        --border-strong: rgba(255, 255, 255, 0.12);

        --text-primary: #f5f3f0;
        --text-secondary: #a8a5a0;
        --text-muted: #6b6862;

        --success: #22c55e;
        --success-bg: rgba(34, 197, 94, 0.1);
        --danger: #ef4444;
        --danger-bg: rgba(239, 68, 68, 0.1);
        --warning: #f59e0b;
        --warning-bg: rgba(245, 158, 11, 0.1);
        --info: #3b82f6;
        --info-bg: rgba(59, 130, 246, 0.1);

        --radius: 10px;
        --radius-sm: 8px;
        --radius-lg: 14px;
        --radius-xl: 18px;

        --shadow-sm: 0 4px 10px -4px rgba(0, 0, 0, 0.4);
        --shadow-md: 0 12px 24px -10px rgba(0, 0, 0, 0.5);
        --shadow-lg: 0 20px 40px -20px rgba(0, 0, 0, 0.6);
        --shadow-accent: 0 12px 24px -8px rgba(169, 120, 74, 0.5);
    }

    /* ================================================================
       PAGE HEADER (with Back button)
       ================================================================ */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 16px;
        flex-wrap: wrap;
    }
    .page-header-left { display: flex; align-items: center; gap: 12px; }
    .page-header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px 10px 12px;
        background: rgba(34, 34, 44, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .back-btn:hover {
        background: var(--accent-bg);
        border-color: var(--accent-border);
        color: var(--accent-light);
        transform: translateX(-4px);
    }
    .back-btn svg { transition: transform 0.2s ease; }
    .back-btn:hover svg { transform: translateX(-2px); }

    /* ================================================================
       SUMMARY / KPI CARDS
       ================================================================ */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .summary-grid.grid-3 { grid-template-columns: repeat(3, 1fr); }
    .summary-grid.grid-2 { grid-template-columns: repeat(2, 1fr); }

    .summary {
        background: var(--bg-card);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
    }
    .summary:hover {
        transform: translateY(-2px);
        border-color: var(--accent-border);
    }
    .summary-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: var(--accent-bg);
        border: 1px solid var(--accent-border);
        color: var(--accent-light);
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .summary-icon.green { background: var(--success-bg); border-color: rgba(34, 197, 94, 0.25); color: var(--success); }
    .summary-icon.blue  { background: var(--info-bg);    border-color: rgba(59, 130, 246, 0.25); color: var(--info); }
    .summary-icon.red   { background: var(--danger-bg);  border-color: rgba(239, 68, 68, 0.25);  color: var(--danger); }
    .summary-icon.amber { background: var(--warning-bg); border-color: rgba(245, 158, 11, 0.25); color: var(--warning); }
    .summary-value {
        font-size: 20px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1;
    }
    .summary-label {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
        margin-top: 3px;
    }

    /* ================================================================
       TABS
       ================================================================ */
    .tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
        padding: 5px;
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .tabs::-webkit-scrollbar { display: none; }
    .tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.15s;
        white-space: nowrap;
        flex-shrink: 0;
        text-decoration: none;
    }
    .tab:hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
    }
    .tab.active {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.25), rgba(169, 120, 74, 0.15));
        color: var(--accent-light);
        box-shadow: inset 0 0 0 1px rgba(169, 120, 74, 0.4);
    }
    .tab-count {
        padding: 2px 8px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 800;
        min-width: 22px;
        text-align: center;
        color: var(--text-muted);
    }
    .tab.active .tab-count {
        background: rgba(169, 120, 74, 0.3);
        color: #f0e6dc;
    }

    /* ================================================================
       TOOLBAR
       ================================================================ */
    .toolbar { margin-bottom: 16px; }
    .toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
    }
    .search-box {
        flex: 1;
        min-width: 0;
        max-width: 380px;
        position: relative;
        display: flex;
        align-items: center;
    }
    .search-icon {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .search-input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        background: var(--bg-input);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all 0.15s;
    }
    .search-input::placeholder { color: var(--text-muted); }
    .search-input:focus {
        border-color: var(--accent);
        background: rgba(20, 20, 26, 0.9);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }
    .filter-select {
        padding: 9px 32px 9px 12px;
        background: var(--bg-input);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        transition: all 0.15s;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        width: 160px;
        flex-shrink: 0;
    }
    .filter-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }
    .btn-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 16px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
        box-shadow: 0 4px 10px -4px rgba(169, 120, 74, 0.5);
    }
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.7);
    }
    .btn-clear {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: var(--danger-bg);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: var(--danger);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .btn-clear:hover { background: rgba(239, 68, 68, 0.2); }

    /* ================================================================
       BUTTONS
       ================================================================ */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        transition: all 0.15s ease;
        white-space: nowrap;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
    }
    .btn svg { flex-shrink: 0; }
    .btn-primary {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #fff;
        box-shadow: 0 4px 10px -4px rgba(169, 120, 74, 0.5);
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.7);
    }
    .btn-ghost {
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-secondary);
        border-color: var(--border-strong);
    }
    .btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: var(--accent-light);
        border-color: var(--accent-border);
    }
    .btn-danger {
        background: var(--danger-bg);
        color: var(--danger);
        border-color: rgba(239, 68, 68, 0.25);
    }
    .btn-danger:hover { background: rgba(239, 68, 68, 0.2); }
    .btn-sm { padding: 6px 12px; font-size: 11.5px; }
    .btn-icon {
        width: 36px;
        height: 36px;
        padding: 0;
    }

    /* ================================================================
       CARDS
       ================================================================ */
    .card {
        background: var(--bg-card);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 16px;
    }
    .card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--border);
    }
    .card-head-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--accent-bg);
        border: 1px solid var(--accent-border);
        color: var(--accent-light);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .card-head-icon.blue  { background: var(--info-bg);    border-color: rgba(59, 130, 246, 0.25); color: var(--info); }
    .card-head-icon.green { background: var(--success-bg); border-color: rgba(34, 197, 94, 0.25);  color: var(--success); }
    .card-head-icon.amber { background: var(--warning-bg); border-color: rgba(245, 158, 11, 0.25); color: var(--warning); }
    .card-head-icon.red   { background: var(--danger-bg);  border-color: rgba(239, 68, 68, 0.25);  color: var(--danger); }
    .card-head-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }
    .card-head-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .card-head-link {
        margin-left: auto;
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 600;
        text-decoration: none;
    }
    .card-head-link:hover { color: var(--accent-light); }

    /* ================================================================
       FORM FIELDS
       ================================================================ */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        padding: 24px;
    }
    .form-grid.grid-3 { grid-template-columns: repeat(3, 1fr); }
    .form-grid.no-pad { padding: 0; }
    .field { display: flex; flex-direction: column; }
    .field-full { grid-column: 1 / -1; }
    .label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .req { color: var(--danger); font-weight: 700; }
    .field-error {
        font-size: 11px;
        color: var(--danger);
        margin-top: 5px;
        font-weight: 500;
    }
    .input, select, textarea {
        width: 100%;
        padding: 10px 12px;
        background: var(--bg-input);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all 0.15s;
    }
    .input::placeholder, textarea::placeholder { color: var(--text-muted); }
    .input:focus, select:focus, textarea:focus {
        border-color: var(--accent);
        background: rgba(20, 20, 26, 0.9);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }
    textarea.input { resize: vertical; min-height: 60px; }

    .input-prefix { position: relative; display: flex; align-items: center; }
    .prefix {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
        pointer-events: none;
    }
    .input-with-prefix { padding-left: 28px; }

    /* ================================================================
       FORM ACTIONS
       ================================================================ */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 8px;
        margin-top: 8px;
    }

    /* ================================================================
       TABLES
       ================================================================ */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
    }
    .data-table tbody td {
        padding: 14px 16px;
        font-size: 13px;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border);
    }
    .data-table tbody tr { transition: background 0.15s; }
    .data-table tbody tr:hover td { background: rgba(255, 255, 255, 0.02); }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr.clickable { cursor: pointer; }

    /* ================================================================
       BADGES
       ================================================================ */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-pending, .badge-partial { background: var(--warning-bg); color: var(--warning); }
    .badge-active, .badge-paid, .badge-verified { background: var(--success-bg); color: var(--success); }
    .badge-inactive { background: var(--info-bg); color: var(--info); }
    .badge-suspended, .badge-overdue { background: var(--danger-bg); color: var(--danger); }

    /* ================================================================
       EMPTY STATES
       ================================================================ */
    .empty-box {
        padding: 60px 20px;
        text-align: center;
    }
    .empty-box.small { padding: 30px 20px; }
    .empty-icon {
        color: var(--text-muted);
        opacity: 0.35;
        display: flex;
        justify-content: center;
        margin-bottom: 12px;
    }
    .empty-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .empty-text {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    /* ================================================================
       ALERTS
       ================================================================ */
    .alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-radius: var(--radius);
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 16px;
        border: 1px solid;
    }
    .alert svg { flex-shrink: 0; margin-top: 1px; }
    .alert-success { background: var(--success-bg); color: var(--success); border-color: rgba(34, 197, 94, 0.25); }
    .alert-danger, .alert-error { background: var(--danger-bg); color: var(--danger); border-color: rgba(239, 68, 68, 0.25); }
    .alert-warning { background: var(--warning-bg); color: var(--warning); border-color: rgba(245, 158, 11, 0.25); }
    .alert-info { background: var(--info-bg); color: var(--info); border-color: rgba(59, 130, 246, 0.25); }

    /* ================================================================
       INFO LIST (label + value rows)
       ================================================================ */
    .info-list { display: flex; flex-direction: column; }
    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-weight: 600;
        flex-shrink: 0;
    }
    .info-label svg { color: var(--text-muted); }
    .info-value {
        color: var(--text-primary);
        font-weight: 500;
        text-align: right;
        word-break: break-word;
    }

    /* ================================================================
       INFO GRID (card-style info items)
       ================================================================ */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .info-item {
        padding: 12px 14px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        transition: all 0.15s;
    }
    .info-item:hover {
        background: var(--accent-bg);
        border-color: var(--accent-border);
    }
    .info-item.full { grid-column: 1 / -1; }

    /* ================================================================
       TRANSACTION LIST
       ================================================================ */
    .txn-list { display: flex; flex-direction: column; gap: 4px; }
    .txn-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: var(--radius);
        text-decoration: none;
        transition: all 0.15s;
    }
    .txn-row:hover {
        background: var(--accent-bg);
        transform: translateX(3px);
    }
    .txn-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border: 1px solid;
    }
    .txn-icon.blue  { background: var(--info-bg);    border-color: rgba(59, 130, 246, 0.2); color: var(--info); }
    .txn-icon.green { background: var(--success-bg); border-color: rgba(34, 197, 94, 0.2);  color: var(--success); }
    .txn-icon.amber { background: var(--warning-bg); border-color: rgba(245, 158, 11, 0.2); color: var(--warning); }
    .txn-info { flex: 1; min-width: 0; }
    .txn-title {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-primary);
        font-family: ui-monospace, monospace;
        margin-bottom: 3px;
    }
    .txn-sub {
        font-size: 11px;
        color: var(--text-muted);
    }
    .txn-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        flex-shrink: 0;
    }
    .txn-amount {
        font-size: 13px;
        font-weight: 800;
        color: var(--accent-light);
    }
    .txn-amount.green { color: var(--success); }

    /* ================================================================
       TIMELINE
       ================================================================ */
    .timeline { display: flex; flex-direction: column; gap: 16px; padding-left: 4px; }
    .timeline-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        position: relative;
    }
    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 4px;
        top: 14px;
        width: 1px;
        height: calc(100% + 8px);
        background: rgba(201, 169, 97, 0.2);
    }
    .timeline-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--accent-light);
        box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.15);
        flex-shrink: 0;
        margin-top: 4px;
        position: relative;
        z-index: 1;
    }
    .timeline-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 3px;
    }
    .timeline-date {
        font-size: 11px;
        color: var(--text-muted);
    }

    /* ================================================================
       HERO CARD
       ================================================================ */
    .hero {
        position: relative;
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.16), rgba(34, 34, 44, 0.7));
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid var(--accent-border);
        border-radius: var(--radius-xl);
        padding: 28px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        overflow: hidden;
        flex-wrap: wrap;
    }
    .hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.18), transparent 70%);
        pointer-events: none;
    }
    .hero-left { display: flex; align-items: center; gap: 20px; position: relative; z-index: 1; }
    .hero-avatar {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--accent-light), var(--accent-dark));
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 26px;
        font-weight: 800;
        box-shadow: var(--shadow-accent), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        flex-shrink: 0;
    }
    .hero-info { min-width: 0; }
    .hero-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.15;
        margin-bottom: 8px;
    }
    .hero-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .hero-code {
        font-family: ui-monospace, monospace;
        font-size: 12px;
        color: var(--accent-light);
        font-weight: 700;
    }
    .hero-dot {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
    }
    .hero-sub {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        color: var(--text-muted);
        flex-wrap: wrap;
    }
    .hero-sub svg { color: var(--text-muted); flex-shrink: 0; }
    .hero-right { position: relative; z-index: 1; }
    .hero-stat { text-align: right; }
    .hero-stat-label {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.12em;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .hero-stat-value {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1;
        color: var(--text-primary);
    }
    .hero-stat-value.accent { color: var(--accent-light); }
    .hero-stat-value.green { color: var(--success); }
    .hero-stat-value.warning { color: var(--warning); }
    .hero-stat-value.danger { color: var(--danger); }

    /* ================================================================
       BALANCE / SUMMARY
       ================================================================ */
    .balance-list { display: flex; flex-direction: column; }
    .balance-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        font-size: 13px;
    }
    .balance-row + .balance-row { border-top: 1px solid var(--border); }
    .balance-label { color: var(--text-secondary); font-weight: 500; }
    .balance-value {
        font-weight: 800;
        color: var(--text-primary);
        font-family: ui-monospace, monospace;
    }
    .balance-value.green { color: var(--success); }
    .balance-value.accent { color: var(--accent-light); }
    .balance-value.danger { color: var(--danger); }
    .balance-total {
        padding-top: 16px;
        margin-top: 4px;
        border-top: 1px solid rgba(169, 120, 74, 0.25) !important;
    }
    .balance-total .balance-value { font-size: 16px; }

    /* ================================================================
       PAGINATION WRAPPER
       ================================================================ */
    .pagination-wrap {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    /* ================================================================
       RESPONSIVE
       ================================================================ */
    @media (max-width: 1100px) {
        .summary-grid, .summary-grid.grid-3, .summary-grid.grid-2 { grid-template-columns: repeat(2, 1fr); }
        .form-grid, .form-grid.grid-3 { grid-template-columns: 1fr; }
        .info-grid { grid-template-columns: 1fr; }
        .hero { flex-direction: column; align-items: flex-start; }
        .hero-right { width: 100%; }
        .hero-stat { text-align: left; }
    }
    @media (max-width: 900px) {
        .toolbar-form { flex-wrap: wrap; }
        .search-box { max-width: 100%; flex: 1 1 100%; }
        .filter-select { flex: 1; width: auto; }
    }
    @media (max-width: 700px) {
        .summary-grid, .summary-grid.grid-3, .summary-grid.grid-2 { grid-template-columns: 1fr; }
        .hero-title { font-size: 22px; }
        .hero-stat-value { font-size: 22px; }
    }
</style>