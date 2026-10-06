@extends('layouts.admin')

@section('title', 'Reorder Requests')
@section('subtitle', 'Stock requests from stores')

@section('content')

<style>
    .rr-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .rr-stat {
        position: relative;
        padding: 18px 20px;
        background: rgba(34, 34, 44, 0.6);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .rr-stat::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--stat-color), transparent);
        opacity: 0;
        transition: opacity 0.25s;
    }
    .rr-stat:hover {
        transform: translateY(-3px);
        border-color: var(--stat-border);
        box-shadow: 0 14px 30px -12px rgba(0, 0, 0, 0.6);
    }
    .rr-stat:hover::before { opacity: 1; }

    .rr-stat.total    { --stat-color: #c9a961; --stat-border: rgba(169, 120, 74, 0.35); --stat-bg: rgba(169, 120, 74, 0.12); }
    .rr-stat.pending  { --stat-color: #f59e0b; --stat-border: rgba(245, 158, 11, 0.35); --stat-bg: rgba(245, 158, 11, 0.12); }
    .rr-stat.approved { --stat-color: #22c55e; --stat-border: rgba(34, 197, 94, 0.35);  --stat-bg: rgba(34, 197, 94, 0.12); }
    .rr-stat.rejected { --stat-color: #ef4444; --stat-border: rgba(239, 68, 68, 0.35);  --stat-bg: rgba(239, 68, 68, 0.12); }

    .rr-stat-head {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 12px;
    }
    .rr-stat-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        background: var(--stat-bg);
        border: 1px solid var(--stat-border);
        display: grid; place-items: center;
        color: var(--stat-color);
    }
    .rr-stat-label {
        font-size: 10.5px; font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }
    .rr-stat-value {
        font-size: 28px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em;
        line-height: 1;
    }

    /* Toolbar */
    .rr-toolbar {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 14px 18px;
        background: rgba(34, 34, 44, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .rr-tabs {
        display: flex;
        gap: 4px;
        padding: 4px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border);
        border-radius: 10px;
    }
    .rr-tab {
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        white-space: nowrap;
    }
    .rr-tab:hover { color: var(--text-primary); }
    .rr-tab.active {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #fff;
        box-shadow: 0 4px 12px -4px rgba(169, 120, 74, 0.5);
    }
    .rr-tab .count {
        display: inline-block;
        margin-left: 6px;
        padding: 1px 6px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 5px;
        font-size: 10px;
        font-weight: 800;
    }
    .rr-tab:not(.active) .count {
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-muted);
    }

    .rr-search {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .rr-search svg {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }
    .rr-search input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        background: rgba(20, 20, 26, 0.7);
        border: 1px solid var(--border-strong);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all 0.15s;
    }
    .rr-search input::placeholder { color: var(--text-muted); }
    .rr-search input:focus {
        border-color: var(--accent);
        background: rgba(20, 20, 26, 0.9);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }

    /* Table */
    .rr-card {
        background: rgba(34, 34, 44, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        overflow: hidden;
    }
    .rr-table { width: 100%; border-collapse: collapse; }
    .rr-table thead th {
        text-align: left;
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        background: rgba(20, 20, 26, 0.4);
        white-space: nowrap;
    }
    .rr-table tbody td {
        padding: 16px 18px;
        font-size: 13px;
        color: var(--text-secondary);
        border-bottom: 1px solid rgba(38, 38, 46, 0.7);
        vertical-align: middle;
    }
    .rr-table tbody tr { transition: background 0.15s; }
    .rr-table tbody tr:hover { background: rgba(169, 120, 74, 0.04); }
    .rr-table tbody tr:last-child td { border-bottom: none; }

    /* Request number */
    .rr-number {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--accent-light);
        letter-spacing: 0.02em;
    }

    /* Store cell */
    .rr-store-cell { display: flex; align-items: center; gap: 11px; }
    .rr-store-avatar {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid; place-items: center;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        flex-shrink: 0;
        overflow: hidden;
        box-shadow: 0 4px 10px -4px rgba(169, 120, 74, 0.5);
    }
    .rr-store-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .rr-store-info { min-width: 0; }
    .rr-store-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rr-store-code {
        font-size: 10.5px;
        color: var(--text-muted);
        font-family: ui-monospace, monospace;
    }

    /* Date */
    .rr-date-main { font-weight: 600; color: var(--text-primary); font-size: 12.5px; margin-bottom: 2px; }
    .rr-date-sub  { font-size: 10.5px; color: var(--text-muted); }

    /* Items pill */
    .rr-items {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.25);
        color: #60a5fa;
        font-size: 11.5px;
        font-weight: 700;
        border-radius: 6px;
    }

    /* Total */
    .rr-total {
        font-size: 14px;
        font-weight: 800;
        color: var(--accent-light);
        font-variant-numeric: tabular-nums;
    }

    /* Status */
    .rr-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .rr-status::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .rr-status.pending  { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); }
    .rr-status.approved { background: rgba(34, 197, 94, 0.12);  color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.25); }
    .rr-status.rejected { background: rgba(239, 68, 68, 0.12);  color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25); }

    /* Action button */
    .rr-btn-view {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        color: var(--accent-light);
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .rr-btn-view:hover {
        background: rgba(169, 120, 74, 0.2);
        border-color: rgba(169, 120, 74, 0.4);
        transform: translateX(2px);
    }

    /* New badge */
    .rr-new-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.06em;
        border-radius: 6px;
        text-transform: uppercase;
        margin-left: 8px;
        box-shadow: 0 3px 8px -2px rgba(34, 197, 94, 0.5);
    }
    .rr-new-badge::before {
        content: '';
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #fff;
        animation: rrpulse 1.5s ease-in-out infinite;
    }
    @keyframes rrpulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* Empty */
    .rr-empty {
        padding: 60px 20px;
        text-align: center;
    }
    .rr-empty-icon {
        width: 64px; height: 64px;
        margin: 0 auto 16px;
        border-radius: 16px;
        background: var(--accent-bg);
        border: 1px solid var(--accent-border);
        display: grid; place-items: center;
        color: var(--accent-light);
    }
    .rr-empty-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .rr-empty-text {
        font-size: 12.5px;
        color: var(--text-muted);
    }

    @media (max-width: 900px) {
        .rr-stats { grid-template-columns: repeat(2, 1fr); }
        .rr-toolbar { flex-direction: column; align-items: stretch; }
        .rr-tabs { overflow-x: auto; }
    }
    @media (max-width: 500px) {
        .rr-stats { grid-template-columns: 1fr; }
    }
</style>

{{-- ===== STATS ===== --}}
<div class="rr-stats">
    <div class="rr-stat total">
        <div class="rr-stat-head">
            <div class="rr-stat-label">Total Requests</div>
            <div class="rr-stat-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                </svg>
            </div>
        </div>
        <div class="rr-stat-value">{{ $stats['total'] ?? 0 }}</div>
    </div>

    <div class="rr-stat pending">
        <div class="rr-stat-head">
            <div class="rr-stat-label">Pending</div>
            <div class="rr-stat-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>
        </div>
        <div class="rr-stat-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>

    <div class="rr-stat approved">
        <div class="rr-stat-head">
            <div class="rr-stat-label">Approved</div>
            <div class="rr-stat-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
            </div>
        </div>
        <div class="rr-stat-value">{{ $stats['approved'] ?? 0 }}</div>
    </div>

    <div class="rr-stat rejected">
        <div class="rr-stat-head">
            <div class="rr-stat-label">Rejected</div>
            <div class="rr-stat-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M15 9l-6 6M9 9l6 6"/>
                </svg>
            </div>
        </div>
        <div class="rr-stat-value">{{ $stats['rejected'] ?? 0 }}</div>
    </div>
</div>

{{-- ===== TOOLBAR ===== --}}
<form method="GET" class="rr-toolbar">
    <div class="rr-tabs">
        <a href="{{ route('reorder-requests.index') }}"
           class="rr-tab {{ !request('status') ? 'active' : '' }}">
            All <span class="count">{{ $stats['total'] ?? 0 }}</span>
        </a>
        <a href="{{ route('reorder-requests.index', ['status' => 'pending']) }}"
           class="rr-tab {{ request('status') === 'pending' ? 'active' : '' }}">
            Pending <span class="count">{{ $stats['pending'] ?? 0 }}</span>
        </a>
        <a href="{{ route('reorder-requests.index', ['status' => 'approved']) }}"
           class="rr-tab {{ request('status') === 'approved' ? 'active' : '' }}">
            Approved <span class="count">{{ $stats['approved'] ?? 0 }}</span>
        </a>
        <a href="{{ route('reorder-requests.index', ['status' => 'rejected']) }}"
           class="rr-tab {{ request('status') === 'rejected' ? 'active' : '' }}">
            Rejected <span class="count">{{ $stats['rejected'] ?? 0 }}</span>
        </a>
    </div>

    <div class="rr-search">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="7"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="text" name="search" placeholder="Search request # or store..."
               value="{{ request('search') }}">
    </div>

    <button type="submit" class="rr-btn-view" style="padding:10px 18px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/>
        </svg>
        Filter
    </button>

    @if(request('search'))
        <a href="{{ route('reorder-requests.index', request('status') ? ['status' => request('status')] : []) }}"
           style="padding:10px 16px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#ef4444;border-radius:10px;font-size:12px;font-weight:600;text-decoration:none;">
            Clear
        </a>
    @endif
</form>

{{-- ===== TABLE ===== --}}
<div class="rr-card">
    @if($requests->count() > 0)
        <div style="overflow-x:auto;">
            <table class="rr-table">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Store</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        @php
                            $isRecent = $req->created_at && $req->created_at->gte(now()->subDays(2));
                            $isUnread = !$req->is_read_by_admin;
                        @endphp
                        <tr>
                            <td>
                                <div class="rr-number">
                                    {{ $req->request_number }}
                                    @if($isUnread)
                                        <span class="rr-new-badge">New</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="rr-store-cell">
                                    <div class="rr-store-avatar">
                                        @if($req->store && $req->store->logo_url)
                                            <img src="{{ $req->store->logo_url }}" alt="{{ $req->store->store_name }}">
                                        @else
                                            {{ strtoupper(substr($req->store->store_name ?? 'ST', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="rr-store-info">
                                        <div class="rr-store-name">{{ $req->store->store_name ?? '-' }}</div>
                                        <div class="rr-store-code">{{ $req->store->code ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rr-date-main">{{ $req->created_at->format('M d, Y') }}</div>
                                <div class="rr-date-sub">{{ $req->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="rr-items">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    {{ $req->items->count() }} item{{ $req->items->count() != 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>
                                <div class="rr-total">&#8369;{{ number_format($req->total_amount, 2) }}</div>
                            </td>
                            <td>
                                <span class="rr-status {{ $req->status }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('reorder-requests.show', $req->id) }}" class="rr-btn-view">
                                    View
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M9 18l6-6-6-6"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($requests, 'links') && $requests->hasPages())
            <div style="padding: 16px 18px; border-top: 1px solid var(--border);">
                {{ $requests->links() }}
            </div>
        @endif
    @else
        <div class="rr-empty">
            <div class="rr-empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                </svg>
            </div>
            <div class="rr-empty-title">No reorder requests yet</div>
            <div class="rr-empty-text">Wala pa'y stock requests nga gi-submit ang imong stores</div>
        </div>
    @endif
</div>

@endsection