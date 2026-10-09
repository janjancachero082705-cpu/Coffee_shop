@extends('layouts.admin')

@section('title', 'Stores')
@section('subtitle', 'Manage your consignment partners')

@section('actions')
    <a href="{{ route('stores.create') }}" class="btn btn-primary btn-sm">+ New Store</a>
@endsection

@section('content')

@php
    $totalStores = \App\Models\Store::count();
    $active = \App\Models\Store::where('status','active')->count();
    $inactive = \App\Models\Store::where('status','inactive')->count();
    $suspended = \App\Models\Store::where('status','suspended')->count();
@endphp

{{-- ===== SUMMARY ===== --}}
<div class="summary-grid">
    <div class="summary">
        <div class="summary-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $totalStores }}</div>
            <div class="summary-label">Total Stores</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $active }}</div>
            <div class="summary-label">Active</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M8 12h8"/>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $inactive }}</div>
            <div class="summary-label">Inactive</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon red">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <path d="M12 9v4M12 17h.01"/>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $suspended }}</div>
            <div class="summary-label">Suspended</div>
        </div>
    </div>
</div>

{{-- ===== TOOLBAR ===== --}}
<div class="toolbar">
    <form method="GET" class="toolbar-form">
        <div class="search-box">
            <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search stores by name, code, or owner..." class="search-input">
        </div>

        <select name="status" class="filter-select">
            <option value="">All Status</option>
            <option value="active"    {{ request('status')==='active'?'selected':'' }}>Active</option>
            <option value="inactive"  {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
            <option value="suspended" {{ request('status')==='suspended'?'selected':'' }}>Suspended</option>
        </select>

        <button type="submit" class="btn-filter">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>

        @if(request()->hasAny(['search','status']))
            <a href="{{ route('stores.index') }}" class="btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif
    </form>
</div>

{{-- ===== STORES GRID ===== --}}
@if($stores->isEmpty())
    <div class="card">
        <div class="empty">
            <div class="empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <path d="M9 22V12h6v10"/>
                </svg>
            </div>
            <div class="empty-title">No stores found</div>
            <div class="empty-text">
                @if(request()->hasAny(['search','status']))
                    Try adjusting your filters
                @else
                    Add your first consignment partner to get started
                @endif
            </div>
            @if(!request()->hasAny(['search','status']))
                <a href="{{ route('stores.create') }}" class="btn btn-primary">+ Create Store</a>
            @endif
        </div>
    </div>
@else
    <div class="stores-grid">
        @foreach($stores as $store)
            <div class="store-card" onclick="window.location='{{ route('stores.show', $store) }}'">
                <div class="store-card-head">
                    <div class="store-avatar">
                        @if($store->logo_url)
                            <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
                        @else
                            {{ strtoupper(substr($store->store_name, 0, 2)) }}
                        @endif
                    </div>

                    <div class="card-head-right">
                        <span class="badge badge-{{ $store->status }}">{{ ucfirst($store->status) }}</span>

                        {{-- 3-Dots Menu --}}
                        <div class="dots-wrap" onclick="event.stopPropagation();">
                            <button type="button" class="dots-btn" onclick="toggleMenu(event, {{ $store->id }})" title="More options">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="5" r="2"/>
                                    <circle cx="12" cy="12" r="2"/>
                                    <circle cx="12" cy="19" r="2"/>
                                </svg>
                            </button>

                            <div class="dots-menu" id="menu-{{ $store->id }}">
                                <a href="{{ route('stores.edit', $store) }}" class="dots-item" onclick="event.stopPropagation();">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    <span>Edit</span>
                                </a>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="store-card-body">
                    <div class="store-card-name">{{ $store->store_name }}</div>
                    <div class="store-card-code">{{ $store->code }}</div>

                    <div class="store-card-meta">
                        <div class="meta-row">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>{{ $store->owner_name }}</span>
                        </div>
                        <div class="meta-row">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                            <span>{{ $store->contact_number }}</span>
                        </div>
                        <div class="meta-row">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $store->city ?? $store->barangay ?? 'No city' }}</span>
                        </div>
                    </div>
                </div>

                <div class="store-card-foot">
                    <div>
                        <div class="foot-label">Credit Limit</div>
                        <div class="foot-value">&#8369;{{ number_format($store->credit_limit, 0) }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="foot-label">Terms</div>
                        <div class="foot-value">{{ ucfirst(str_replace('_',' ',$store->payment_terms)) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(method_exists($stores, 'links'))
        <div class="pagination-wrap">{{ $stores->withQueryString()->links() }}</div>
    @endif
@endif

{{-- ===== HIDDEN DELETE FORM ===== --}}


@endsection

@push('styles')
<style>
    /* ===== SUMMARY ===== */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .summary {
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
    }
    .summary:hover {
        transform: translateY(-2px);
        border-color: rgba(169, 120, 74, 0.25);
    }
    .summary-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid; place-items: center;
        color: #c9a961;
        flex-shrink: 0;
    }
    .summary-icon.green { background: rgba(34, 197, 94, 0.1);  border-color: rgba(34, 197, 94, 0.2);  color: #22c55e; }
    .summary-icon.blue  { background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .summary-icon.red   { background: rgba(239, 68, 68, 0.1);  border-color: rgba(239, 68, 68, 0.2);  color: #ef4444; }
    .summary-value {
        font-size: 20px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em; line-height: 1;
    }
    .summary-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-top: 3px;
    }

    /* ===== TOOLBAR ===== */
    .toolbar { margin-bottom: 16px; }
    .toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
    }
    .search-box {
        flex: 1; min-width: 0; max-width: 420px;
        position: relative;
        display: flex; align-items: center;
    }
    .search-icon { position: absolute; left: 12px; color: var(--text-muted); pointer-events: none; }
    .search-input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 13px; font-family: inherit; outline: none; transition: all 0.15s;
    }
    .search-input::placeholder { color: var(--text-muted); }
    .search-input:focus {
        border-color: var(--accent);
        background: rgba(20, 20, 26, 0.9);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }
    .filter-select {
        padding: 9px 32px 9px 12px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 13px; font-family: inherit;
        outline: none; cursor: pointer; transition: all 0.15s;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2.5'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        width: 140px; flex-shrink: 0;
    }
    .filter-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
    }
    .btn-filter {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 9px 16px;
        background: linear-gradient(135deg, #a9784a, #8a5f36);
        color: #fff; border: none; border-radius: 8px;
        font-size: 13px; font-weight: 600; font-family: inherit;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
        box-shadow: 0 4px 10px -4px rgba(169, 120, 74, 0.5);
    }
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.7);
    }
    .btn-clear {
        width: 36px; height: 36px;
        border-radius: 8px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        display: grid; place-items: center;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
    }
    .btn-clear:hover { background: rgba(239, 68, 68, 0.2); }

    /* ===== STORES GRID ===== */
    .stores-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }
    .store-card {
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 18px;
        display: flex; flex-direction: column;
        transition: all 0.25s ease;
        position: relative;
        overflow: visible;
        cursor: pointer;
    }
    .store-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        border-radius: 14px 14px 0 0;
        background: linear-gradient(90deg, transparent, #c9a961, transparent);
        opacity: 0;
        transition: opacity 0.25s;
    }
    .store-card:hover {
        transform: translateY(-4px);
        border-color: rgba(169, 120, 74, 0.3);
        background: rgba(34, 34, 44, 0.75);
        box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.6);
    }
    .store-card:hover::before { opacity: 1; }

    .store-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        gap: 8px;
    }
    .card-head-right {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .store-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .store-avatar {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #a9784a, #8a5f36);
        display: grid; place-items: center;
        color: #fff; font-size: 16px; font-weight: 800;
        letter-spacing: 0.02em;
        box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.5);
        flex-shrink: 0;
    }

    /* ===== 3-DOTS MENU ===== */
    .dots-wrap {
        position: relative;
    }
    .dots-btn {
        width: 28px; height: 28px;
        border-radius: 7px;
        background: transparent;
        border: 1px solid transparent;
        color: var(--text-muted);
        display: grid; place-items: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .dots-btn:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.08);
        color: var(--text-primary);
    }
    .dots-btn svg { display: block; }

    .dots-menu {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        min-width: 140px;
        background: rgba(20, 20, 26, 0.98);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 5px;
        box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.7);
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-6px) scale(0.96);
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: top right;
    }
    .dots-menu.open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }
    .dots-item {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        padding: 8px 11px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--text-secondary);
        background: transparent;
        border: none;
        cursor: pointer;
        font-family: inherit;
        text-align: left;
        transition: all 0.12s;
    }
    .dots-item:hover {
        background: rgba(169, 120, 74, 0.1);
        color: #c9a961;
    }
    .dots-item.danger { color: #ef4444; }
    .dots-item.danger:hover {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
    }
    .dots-item svg { flex-shrink: 0; }

    /* ===== CARD BODY ===== */
    .store-card-body { flex: 1; }
    .store-card-name {
        font-size: 15px; font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .store-card-code {
        font-family: ui-monospace, monospace;
        font-size: 11px; color: #c9a961;
        font-weight: 600; margin-bottom: 14px;
    }
    .store-card-meta {
        display: flex; flex-direction: column;
        gap: 6px;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
    .meta-row {
        display: flex; align-items: center;
        gap: 8px;
        font-size: 12px; color: var(--text-secondary);
    }
    .meta-row svg { color: var(--text-muted); flex-shrink: 0; }
    .meta-row span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .store-card-foot {
        display: flex;
        justify-content: space-between;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
    .foot-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.08em;
        font-weight: 700; margin-bottom: 3px;
    }
    .foot-value {
        font-size: 13px; font-weight: 700;
        color: var(--text-primary);
    }

    /* ===== EMPTY ===== */
    .empty {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-icon {
        width: 60px; height: 60px;
        margin: 0 auto 16px;
        border-radius: 14px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.25);
        display: grid; place-items: center;
        color: #c9a961;
    }
    .empty-title {
        font-size: 16px; font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .empty-text {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-bottom: 18px;
    }

    /* ===== PAGINATION ===== */
    .pagination-wrap {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1100px) {
        .summary-grid { grid-template-columns: repeat(2, 1fr); }
        .stores-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 700px) {
        .summary-grid { grid-template-columns: 1fr; }
        .stores-grid { grid-template-columns: 1fr; }
        .toolbar-form { flex-wrap: wrap; }
        .search-box { max-width: 100%; flex: 1 1 100%; }
        .filter-select { flex: 1; width: auto; }
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle dropdown menu
    function toggleMenu(event, id) {
        event.stopPropagation();
        event.preventDefault();

        // Close all other menus
        document.querySelectorAll('.dots-menu.open').forEach(m => {
            if (m.id !== 'menu-' + id) m.classList.remove('open');
        });

        const menu = document.getElementById('menu-' + id);
        if (menu) menu.classList.toggle('open');
    }

    // Close on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.dots-menu.open').forEach(m => m.classList.remove('open'));
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dots-menu.open').forEach(m => m.classList.remove('open'));
        }
    });

    
</script>
@endpush