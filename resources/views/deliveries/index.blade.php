@extends('layouts.admin')

@section('title', 'Deliveries')
@section('subtitle', 'Delivery receipts for all stores')

@section('actions')
    <a href="{{ route('deliveries.create') }}" class="btn btn-primary btn-sm">+ New Delivery</a>
@endsection

@section('content')

<div class="summary-grid">
    <div class="summary">
        <div class="summary-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['total'] }}</div>
            <div class="summary-label">Total Deliveries</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['pending'] }}</div>
            <div class="summary-label">Pending</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/><path d="M12 2v4"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['partial'] }}</div>
            <div class="summary-label">Partial</div>
        </div>
    </div>
    <div class="summary">
        <div class="summary-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">&#8369;{{ number_format($stats['outstanding']/1000, 1) }}k</div>
            <div class="summary-label">Unpaid Balance</div>
        </div>
    </div>
</div>

<div class="tabs">
    @php
        $tabsList = [
            'all'     => ['label' => 'All Deliveries'],
            'pending' => ['label' => 'Pending'],
            'partial' => ['label' => 'Partial'],
            'paid'    => ['label' => 'Fully Paid'],
        ];
    @endphp

    @foreach($tabsList as $key => $tabItem)
        <a href="{{ route('deliveries.index', array_merge(request()->only(['search','store']), ['tab' => $key])) }}"
           class="tab {{ $tab === $key ? 'active' : '' }}">
            <span>{{ $tabItem['label'] }}</span>
            <span class="tab-count">{{ $tabCounts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<div class="toolbar">
    <form method="GET" class="toolbar-form">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="search-box">
            <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR number or store..." class="search-input">
        </div>

        <select name="store" class="filter-select" onchange="this.form.submit()">
            <option value="">All Stores</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}" {{ request('store')==$s->id?'selected':'' }}>{{ $s->store_name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn-filter">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>

        @if(request()->hasAny(['search','store']))
            <a href="{{ route('deliveries.index', ['tab' => $tab]) }}" class="btn-clear" title="Clear filters">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </a>
        @endif

        <div class="view-toggle">
            <button type="button" class="view-btn active" data-view="list" onclick="switchView('list')" title="List View">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </button>
            <button type="button" class="view-btn" data-view="calendar" onclick="switchView('calendar')" title="Calendar View">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </button>
        </div>
    </form>
</div>

{{-- ===== LIST VIEW ===== --}}
<div id="listView">
    @if($deliveries->isEmpty())
        <div class="card">
            <div class="empty">
                <div class="empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div class="empty-title">No deliveries found</div>
                <div class="empty-text">
                    @if(request()->hasAny(['search','store']) || $tab !== 'all')
                        Try adjusting your filters or tab
                    @else
                        Create your first delivery receipt to get started
                    @endif
                </div>
                @if(!request()->hasAny(['search','store']) && $tab === 'all')
                    <a href="{{ route('deliveries.create') }}" class="btn btn-primary">+ New Delivery</a>
                @endif
            </div>
        </div>
    @else
        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>DR Number</th>
                        <th>Store</th>
                        <th>Delivery Date</th>
                        <th>Items</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:right;">Balance</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveries as $dr)
                        <tr style="cursor:pointer;" onclick="window.location='{{ route('deliveries.show', $dr) }}'">
                            <td>
                                @php
                                    $drParts = explode('-', $dr->dr_number);
                                    $drShort = end($drParts);
                                @endphp
                                <span style="color:#c9a961; font-family:ui-monospace; font-weight:800; font-size:13px;" title="{{ $dr->dr_number }}">
                                    #{{ $drShort }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary);">{{ $dr->store->store_name ?? '-' }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $dr->store->code ?? '' }}</div>
                            </td>
                            <td>
                                <div style="font-weight:500; font-size:12px;">{{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $dr->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span style="font-size:12px;">{{ $dr->items->count() }} item{{ $dr->items->count() !== 1 ? 's' : '' }}</span>
                            </td>
                            <td style="text-align:right; font-weight:700; color:var(--text-primary); font-size:13px;">
                                &#8369;{{ number_format($dr->total_amount, 2) }}
                            </td>
                            <td style="text-align:right; font-weight:700; font-size:13px; color:{{ $dr->balance > 0 ? '#f59e0b' : '#22c55e' }};">
                                &#8369;{{ number_format($dr->balance, 2) }}
                            </td>
                            <td>
                                @if($dr->status === 'out_for_delivery' || ($dr->out_for_delivery_at && !$dr->customer_confirmed))
                                    <span class="badge" style="background:rgba(59,130,246,0.15);color:#3b82f6;border:1px solid rgba(59,130,246,0.3);">
                                        🚚 Out for Delivery
                                    </span>
                                @elseif($dr->status === 'delivered' || $dr->customer_confirmed)
                                    <span class="badge" style="background:rgba(34,197,94,0.15);color:#22c55e;border:1px solid rgba(34,197,94,0.3);">
                                        ✓ Delivered
                                    </span>
                                @elseif($dr->status === 'partial')
                                    <span class="badge badge-partial">Partial</span>
                                @elseif($dr->status === 'paid')
                                    <span class="badge badge-paid">Paid</span>
                                @else
                                    <span class="badge badge-pending">Pending</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('deliveries.show', $dr) }}" class="btn btn-ghost btn-sm" onclick="event.stopPropagation();">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($deliveries, 'links'))
            <div class="pagination-wrap">{{ $deliveries->links() }}</div>
        @endif
    @endif
</div>

{{-- ===== CALENDAR VIEW ===== --}}
<div id="calendarView" style="display:none;">
    <div class="card">
        <div class="cal-header">
            <button type="button" class="cal-nav" onclick="changeMonth(-1)">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <div class="cal-title" id="calTitle">Loading...</div>
            <button type="button" class="cal-nav" onclick="changeMonth(1)">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            </button>
            <div class="cal-spacer"></div>
            <button type="button" class="btn btn-ghost btn-sm" onclick="goToday()">Today</button>
        </div>

        <div class="cal-weekdays">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>

        <div class="cal-grid" id="calGrid"></div>

        <div class="cal-legend">
            <div class="cal-legend-item">
                <span class="cal-dot" style="background: #f59e0b;"></span> Pending
            </div>
            <div class="cal-legend-item">
                <span class="cal-dot" style="background: #3b82f6;"></span> Partial
            </div>
            <div class="cal-legend-item">
                <span class="cal-dot" style="background: #22c55e;"></span> Paid
            </div>
        </div>
    </div>
</div>

{{-- Modal for day view --}}
<div id="dayModal" class="modal-overlay" onclick="closeDayModal(event)">
    <div class="modal-card" onclick="event.stopPropagation();">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="modalDate">--</div>
                <div class="modal-sub" id="modalCount">--</div>
            </div>
            <button type="button" class="modal-close" onclick="closeDayModal(event)">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

@endsection

@push('styles')
<style>
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
        transition: all 0.2s;
    }
    .summary:hover {
        transform: translateY(-2px);
        border-color: rgba(169, 120, 74, 0.25);
    }
    .summary-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid; place-items: center;
        color: #c9a961; flex-shrink: 0;
    }
    .summary-icon.green { background: rgba(34, 197, 94, 0.1);  border-color: rgba(34, 197, 94, 0.2);  color: #22c55e; }
    .summary-icon.blue  { background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .summary-icon.amber { background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2); color: #f59e0b; }
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

    .tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
        padding: 5px;
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        overflow-x: auto;
    }
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
    }
    .tab:hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
    }
    .tab.active {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.25), rgba(169, 120, 74, 0.15));
        color: #c9a961;
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

    .toolbar { margin-bottom: 16px; }
    .toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
    }
    .search-box {
        flex: 1; min-width: 0; max-width: 380px;
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
        width: 170px; flex-shrink: 0;
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

    .view-toggle {
        display: inline-flex;
        gap: 4px;
        padding: 4px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        flex-shrink: 0;
    }
    .view-btn {
        width: 30px; height: 30px;
        border-radius: 6px;
        background: transparent;
        border: none;
        color: var(--text-muted);
        display: grid; place-items: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .view-btn:hover { color: var(--text-primary); }
    .view-btn.active {
        background: linear-gradient(135deg, #a9784a, #8a5f36);
        color: #fff;
    }

    .pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }

    /* ===== CALENDAR ===== */
    .cal-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .cal-nav {
        width: 34px; height: 34px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: var(--text-secondary);
        display: grid; place-items: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .cal-nav:hover {
        background: rgba(169, 120, 74, 0.1);
        border-color: rgba(169, 120, 74, 0.3);
        color: #c9a961;
    }
    .cal-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        min-width: 180px;
    }
    .cal-spacer { flex: 1; }

    .cal-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 6px;
    }
    .cal-weekdays > div {
        text-align: center;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 8px 0;
    }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }
    .cal-day {
        min-height: 100px;
        padding: 8px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s;
        position: relative;
        overflow: hidden;
    }
    .cal-day:hover {
        background: rgba(169, 120, 74, 0.05);
        border-color: rgba(169, 120, 74, 0.25);
    }
    .cal-day.other-month {
        opacity: 0.3;
    }
    .cal-day.today {
        border-color: rgba(201, 169, 97, 0.6);
        background: rgba(201, 169, 97, 0.06);
    }
    .cal-day-num {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .cal-day.today .cal-day-num {
        color: #c9a961;
        font-weight: 800;
    }
    .cal-events {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .cal-event {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-left: 2px solid;
    }
    .cal-event.pending {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border-color: #f59e0b;
    }
    .cal-event.partial {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border-color: #3b82f6;
    }
    .cal-event.paid {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        border-color: #22c55e;
    }
    .cal-more {
        font-size: 9px;
        color: var(--text-muted);
        font-weight: 700;
        padding: 2px 6px;
        cursor: pointer;
    }
    .cal-more:hover { color: #c9a961; }

    .cal-legend {
        display: flex;
        gap: 20px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 11px;
        color: var(--text-secondary);
    }
    .cal-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    .cal-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
    }

    /* ===== MODAL ===== */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        z-index: 200;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-overlay.open {
        display: flex;
        animation: fadeIn 0.15s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    .modal-card {
        background: linear-gradient(160deg, rgba(34, 34, 44, 0.98), rgba(20, 20, 26, 0.98));
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.8);
        animation: slideUp 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }
    .modal-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .modal-close {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.04);
        border: none;
        color: var(--text-muted);
        display: grid; place-items: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .modal-close:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    .modal-body {
        padding: 16px 20px 20px;
        overflow-y: auto;
    }

    .modal-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        margin-bottom: 8px;
        transition: all 0.15s;
    }
    .modal-item:hover {
        background: rgba(169, 120, 74, 0.06);
        border-color: rgba(169, 120, 74, 0.2);
        transform: translateX(3px);
    }
    .modal-item:last-child { margin-bottom: 0; }
    .modal-item-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: grid; place-items: center;
        flex-shrink: 0;
        border: 1px solid;
    }
    .modal-item-icon.pending {
        background: rgba(245, 158, 11, 0.1);
        border-color: rgba(245, 158, 11, 0.25);
        color: #f59e0b;
    }
    .modal-item-icon.partial {
        background: rgba(59, 130, 246, 0.1);
        border-color: rgba(59, 130, 246, 0.25);
        color: #3b82f6;
    }
    .modal-item-icon.paid {
        background: rgba(34, 197, 94, 0.1);
        border-color: rgba(34, 197, 94, 0.25);
        color: #22c55e;
    }
    .modal-item-info { flex: 1; min-width: 0; }
    .modal-item-dr {
        font-size: 12px;
        font-weight: 700;
        color: #c9a961;
        font-family: ui-monospace;
    }
    .modal-item-store {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 2px;
    }
    .modal-item-amount {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        text-align: right;
        flex-shrink: 0;
    }
    .modal-item-balance {
        font-size: 10px;
        color: #f59e0b;
        margin-top: 2px;
    }
    .modal-item-balance.paid { color: #22c55e; }

    @media (max-width: 1100px) {
        .summary-grid { grid-template-columns: repeat(2, 1fr); }
        .cal-day { min-height: 80px; padding: 6px; }
    }
    @media (max-width: 900px) {
        .toolbar-form { flex-wrap: wrap; }
        .search-box { max-width: 100%; flex: 1 1 100%; }
        .filter-select { flex: 1; width: auto; }
        .cal-day { min-height: 70px; }
    }
    @media (max-width: 700px) {
        .summary-grid { grid-template-columns: 1fr; }
        .cal-grid { gap: 2px; }
        .cal-day { min-height: 60px; padding: 4px; border-radius: 6px; }
        .cal-day-num { font-size: 10px; }
        .cal-event { font-size: 8px; padding: 2px 3px; }
    }

    /* ===== CALENDAR: Dots + Count Row ===== */
    .cal-events-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        flex-wrap: wrap;
    }

    .cal-dots-group {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .cal-dots-group .cal-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.35) inset;
        flex-shrink: 0;
    }

    /* Status colors */
    .cal-dot.status-pending   { background: #f59e0b; }
    .cal-dot.status-partial   { background: #3b82f6; }
    .cal-dot.status-paid      { background: #22c55e; }
    .cal-dot.status-out       { background: #60a5fa; }
    .cal-dot.status-delivered { background: #10b981; }

    /* Count badge inline */
    .cal-count-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 7px;
        background: rgba(201, 169, 97, 0.15);
        border: 1px solid rgba(201, 169, 97, 0.4);
        color: #c9a961;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        letter-spacing: -0.02em;
        line-height: 1.2;
        transition: all 0.15s ease;
    }

    .cal-day:hover .cal-count-inline {
        background: rgba(201, 169, 97, 0.3);
        border-color: #c9a961;
        transform: scale(1.05);
    }

    /* Mobile responsive */
    @media (max-width: 700px) {
        .cal-events-row { gap: 4px; margin-top: 4px; }
        .cal-dots-group .cal-dot { width: 5px; height: 5px; }
        .cal-count-inline { padding: 1px 5px; font-size: 9px; border-radius: 5px; }
    }

    /* Optional: pill count style (dili box) */
    .cal-count-inline.pill {
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        color: #fff;
        border: none;
        box-shadow: 0 3px 8px -2px rgba(201, 169, 97, 0.6);
    }
</style>
@endpush

@push('scripts')
<script>
// ===== Deliveries data (JSON) =====
const deliveries = {!! json_encode($deliveries->map(function($dr) {
    return [
        'id' => $dr->id,
        'dr_number' => $dr->dr_number,
        'store_name' => $dr->store->store_name ?? '-',
        'store_code' => $dr->store->code ?? '',
        'delivery_date' => \Carbon\Carbon::parse($dr->delivery_date)->format('Y-m-d'),
        'total_amount' => (float) $dr->total_amount,
        'balance' => (float) $dr->balance,
        'status' => $dr->status,
        'url' => route('deliveries.show', $dr->id),
    ];
})->values()) !!};

// ===== State =====
let currentYear = {{ now()->year }};
let currentMonth = {{ now()->month - 1 }}; // 0-11

// ===== View toggle =====
function switchView(view) {
    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`.view-btn[data-view="${view}"]`).classList.add('active');

    if (view === 'calendar') {
        document.getElementById('listView').style.display = 'none';
        document.getElementById('calendarView').style.display = 'block';
        renderCalendar();
    } else {
        document.getElementById('listView').style.display = 'block';
        document.getElementById('calendarView').style.display = 'none';
    }
    localStorage.setItem('deliveriesView', view);
}

// ===== Calendar =====
function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
    if (currentMonth > 11) { currentMonth = 0;  currentYear++; }
    renderCalendar();
}

function goToday() {
    const now = new Date();
    currentYear = now.getFullYear();
    currentMonth = now.getMonth();
    renderCalendar();
}

function renderCalendar() {
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    document.getElementById('calTitle').textContent = monthNames[currentMonth] + ' ' + currentYear;

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrev = new Date(currentYear, currentMonth, 0).getDate();
    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;

    let html = '';
    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;

    for (let i = 0; i < totalCells; i++) {
        let dayNum, isOther = false, dateStr;
        if (i < firstDay) {
            dayNum = daysInPrev - firstDay + i + 1;
            isOther = true;
            const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
            const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
            dateStr = `${prevYear}-${String(prevMonth+1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
        } else if (i < firstDay + daysInMonth) {
            dayNum = i - firstDay + 1;
            dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
        } else {
            dayNum = i - (firstDay + daysInMonth) + 1;
            isOther = true;
            const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
            const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
            dateStr = `${nextYear}-${String(nextMonth+1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
        }

        const dayDeliveries = deliveries.filter(d => d.delivery_date === dateStr);
        const isToday = dateStr === todayStr;

        // Count per status
        const statusCounts = { pending: 0, partial: 0, paid: 0, out_for_delivery: 0, delivered: 0 };
        dayDeliveries.forEach(d => {
            const s = d.status || 'pending';
            if (statusCounts[s] !== undefined) statusCounts[s]++;
        });

        let eventsHtml = '';
        if (dayDeliveries.length > 0) {
            // Build dots HTML (each unique status = 1 dot)
            let dotsHtml = '';
            if (statusCounts.pending > 0)            dotsHtml += `<span class="cal-dot status-pending" title="${statusCounts.pending} Pending"></span>`;
            if (statusCounts.out_for_delivery > 0)   dotsHtml += `<span class="cal-dot status-out" title="${statusCounts.out_for_delivery} Out for Delivery"></span>`;
            if (statusCounts.partial > 0)            dotsHtml += `<span class="cal-dot status-partial" title="${statusCounts.partial} Partial"></span>`;
            if (statusCounts.delivered > 0)          dotsHtml += `<span class="cal-dot status-delivered" title="${statusCounts.delivered} Delivered"></span>`;
            if (statusCounts.paid > 0)               dotsHtml += `<span class="cal-dot status-paid" title="${statusCounts.paid} Paid"></span>`;

            eventsHtml = `
                <div class="cal-events-row">
                    <div class="cal-dots-group">${dotsHtml}</div>
                    <div class="cal-count-inline" title="${dayDeliveries.length} deliveries">${dayDeliveries.length}</div>
                </div>`;
        }

        html += `<div class="cal-day ${isOther ? 'other-month' : ''} ${isToday ? 'today' : ''}"
                     onclick="openDayModal('${dateStr}')">
                     <div class="cal-day-num">${dayNum}</div>
                     <div class="cal-events">${eventsHtml}</div>
                 </div>`;
    }

    document.getElementById('calGrid').innerHTML = html;
}

// ===== Modal =====
function openDayModal(dateStr) {
    const dayDeliveries = deliveries.filter(d => d.delivery_date === dateStr);
    if (dayDeliveries.length === 0) return;

    const date = new Date(dateStr + 'T00:00:00');
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    document.getElementById('modalDate').textContent = dayNames[date.getDay()] + ', ' + monthNames[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
    document.getElementById('modalCount').textContent = dayDeliveries.length + ' deliver' + (dayDeliveries.length === 1 ? 'y' : 'ies');

    let html = '';
    dayDeliveries.forEach(d => {
        const statusIcon = d.status === 'paid'
            ? '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>'
            : '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>';

        const balanceText = d.balance > 0
            ? `<div class="modal-item-balance">Balance: &#8369;${formatMoney(d.balance)}</div>`
            : `<div class="modal-item-balance paid">Fully Paid</div>`;

        html += `
            <a href="${d.url}" class="modal-item">
                <div class="modal-item-icon ${d.status}">${statusIcon}</div>
                <div class="modal-item-info">
                    <div class="modal-item-dr">${d.dr_number}</div>
                    <div class="modal-item-store">${escapeHtml(d.store_name)} ${d.store_code ? '- ' + d.store_code : ''}</div>
                </div>
                <div>
                    <div class="modal-item-amount">&#8369;${formatMoney(d.total_amount)}</div>
                    ${balanceText}
                </div>
            </a>`;
    });

    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('dayModal').classList.add('open');
}

function closeDayModal(e) {
    if (e) e.stopPropagation();
    document.getElementById('dayModal').classList.remove('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDayModal();
});

// ===== Helpers =====
function formatMoney(n) {
    return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

// ===== Init =====
document.addEventListener('DOMContentLoaded', () => {
    const savedView = localStorage.getItem('deliveriesView') || 'list';
    switchView(savedView);
});
</script>
@endpush