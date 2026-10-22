@extends('layouts.admin')

@section('title', 'Registration Details')
@section('subtitle', $store->store_name ?? 'Store Registration')

@section('content')

@php
    $statusRaw = strtolower($store->status ?? 'pending');
    $statusMap = [
        'approved' => ['label' => 'Approved', 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.12)',  'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>'],
        'pending'  => ['label' => 'Pending',  'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.12)', 'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>'],
        'rejected' => ['label' => 'Rejected', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.12)',  'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>'],
    ];
    $statusConfig = $statusMap[$statusRaw] ?? [
        'label' => ucfirst($statusRaw),
        'color' => '#71717a',
        'bg'    => 'rgba(113,113,122,0.12)',
        'icon'  => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
    ];

    $initials = strtoupper(substr($store->store_name ?? 'S', 0, 2));
@endphp

{{-- ═══════ HERO ═══════ --}}
<div class="sr-hero" style="--status-color: {{ $statusConfig['color'] }};">
    <div class="sr-hero-avatar">
        {{ $initials }}
    </div>

    <div class="sr-hero-content">
        <div class="sr-hero-label">Store Registration</div>
        <div class="sr-hero-title">{{ $store->store_name }}</div>
        <div class="sr-hero-meta">
            @if($store->store_code ?? null)
                <span class="sr-hero-code">{{ $store->store_code }}</span>
                <span class="sr-hero-dot">·</span>
            @endif
            <span>Registered {{ \Carbon\Carbon::parse($store->created_at)->format('M d, Y') }}</span>
            <span class="sr-hero-dot">·</span>
            <span class="sr-hero-status" style="color: {{ $statusConfig['color'] }}; background: {{ $statusConfig['bg'] }};">
                <span class="sr-status-dot" style="background: {{ $statusConfig['color'] }};"></span>
                {{ $statusConfig['label'] }}
            </span>
        </div>
    </div>

    <div class="sr-hero-actions">
        @if(isset($store->store_id))
            <a href="{{ route('stores.show', $store->store_id) }}" class="sr-btn sr-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/></svg>
                View Store
            </a>
        @endif
    </div>
</div>

{{-- ═══════ QUICK STATS ═══════ --}}
<div class="sr-stats">
    <div class="sr-stat sr-stat-primary">
        <div class="sr-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4M12 2a10 10 0 100 20 10 10 0 000-20z"/>
            </svg>
        </div>
        <div class="sr-stat-body">
            <div class="sr-stat-lbl">Status</div>
            <div class="sr-stat-val" style="color: {{ $statusConfig['color'] }};">{{ $statusConfig['label'] }}</div>
        </div>
    </div>

    <div class="sr-stat sr-stat-blue">
        <div class="sr-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="2" y="4" width="20" height="16" rx="2"/>
                <path d="M2 8h20"/>
            </svg>
        </div>
        <div class="sr-stat-body">
            <div class="sr-stat-lbl">Portal Access</div>
            <div class="sr-stat-val blue">{{ $store->portal_access_enabled ?? true ? 'Enabled' : 'Disabled' }}</div>
        </div>
    </div>

    <div class="sr-stat sr-stat-amber">
        <div class="sr-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="sr-stat-body">
            <div class="sr-stat-lbl">Registered</div>
            <div class="sr-stat-val amber">{{ \Carbon\Carbon::parse($store->created_at)->diffForHumans() }}</div>
        </div>
    </div>
</div>

{{-- ═══════ MAIN GRID ═══════ --}}
<div class="sr-grid">

    {{-- LEFT: Info Sections --}}
    <div>
        {{-- STORE INFO --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/>
                    </svg>
                </div>
                <div>
                    <div class="sr-card-title">Store Information</div>
                    <div class="sr-card-sub">Basic details sa tindahan</div>
                </div>
            </div>

            <div class="sr-info-grid">
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Store Name</div>
                    <div class="sr-info-val">{{ $store->store_name ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Store Code</div>
                    <div class="sr-info-val mono">{{ $store->store_code ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Business Type</div>
                    <div class="sr-info-val">{{ $store->business_type ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">TIN / Permit #</div>
                    <div class="sr-info-val mono">{{ $store->tin_number ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- OWNER INFO --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon green">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21v-2a4 4 0 014-4h8a4 4 0 014 4v2"/>
                    </svg>
                </div>
                <div>
                    <div class="sr-card-title">Owner Information</div>
                    <div class="sr-card-sub">Personal details sa tag-iya</div>
                </div>
            </div>

            <div class="sr-info-grid">
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Owner Name</div>
                    <div class="sr-info-val">{{ $store->owner_name ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Email</div>
                    <div class="sr-info-val">{{ $store->email ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Contact Number</div>
                    <div class="sr-info-val mono">{{ $store->contact_number ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Alternate Contact</div>
                    <div class="sr-info-val mono">{{ $store->alternate_contact ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- ADDRESS --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon blue">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div>
                    <div class="sr-card-title">Store Address</div>
                    <div class="sr-card-sub">Lokasyon sa tindahan</div>
                </div>
            </div>

            <div class="sr-info-grid">
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Street Address</div>
                    <div class="sr-info-val">{{ $store->address ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Barangay</div>
                    <div class="sr-info-val">{{ $store->barangay ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">City / Municipality</div>
                    <div class="sr-info-val">{{ $store->city ?? '-' }}</div>
                </div>
                <div class="sr-info-item">
                    <div class="sr-info-lbl">Province</div>
                    <div class="sr-info-val">{{ $store->province ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Status + Actions --}}
    <div>
        {{-- STATUS CARD --}}
        <div class="sr-card sr-card-status">
            <div class="sr-card-head">
                <div class="sr-card-icon" style="background: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }};">
                    {!! $statusConfig['icon'] !!}
                </div>
                <div class="sr-card-title">Registration Status</div>
            </div>

            <div class="sr-status-rows">
                <div class="sr-status-row">
                    <span class="sr-status-lbl">Registration</span>
                    <span class="sr-status-val" style="color: {{ $statusConfig['color'] }};">
                        <span class="sr-status-dot-sm" style="background: {{ $statusConfig['color'] }};"></span>
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
                <div class="sr-status-row">
                    <span class="sr-status-lbl">Portal Access</span>
                    <span class="sr-status-val {{ ($store->portal_access_enabled ?? true) ? 'green' : 'red' }}">
                        {{ ($store->portal_access_enabled ?? true) ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <div class="sr-status-row">
                    <span class="sr-status-lbl">Registered</span>
                    <span class="sr-status-val">{{ \Carbon\Carbon::parse($store->created_at)->format('M d, Y') }}</span>
                </div>
                <div class="sr-status-row">
                    <span class="sr-status-lbl">Last Updated</span>
                    <span class="sr-status-val">{{ $store->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            @if(isset($store->store_id))
                <a href="{{ route('stores.show', $store->store_id) }}" class="sr-btn sr-btn-primary sr-btn-full">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 9l1.5-6h15L21 9M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18"/></svg>
                    View Store Details
                </a>
            @endif
        </div>

        {{-- TIMELINE --}}
        <div class="sr-card">
            <div class="sr-card-head">
                <div class="sr-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
                    </svg>
                </div>
                <div class="sr-card-title">Activity</div>
            </div>

            <div class="sr-timeline">
                <div class="sr-timeline-item">
                    <div class="sr-timeline-dot"></div>
                    <div class="sr-timeline-body">
                        <div class="sr-timeline-title">Registration Created</div>
                        <div class="sr-timeline-time">{{ \Carbon\Carbon::parse($store->created_at)->format('M d, Y · g:i A') }}</div>
                    </div>
                </div>
                @if($statusRaw === 'approved')
                    <div class="sr-timeline-item">
                        <div class="sr-timeline-dot green"></div>
                        <div class="sr-timeline-body">
                            <div class="sr-timeline-title">Approved</div>
                            <div class="sr-timeline-time">{{ \Carbon\Carbon::parse($store->updated_at)->format('M d, Y · g:i A') }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* ═══ HERO ═══ */
    .sr-hero {
        position: relative;
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 24px 26px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, rgba(30, 26, 22, 0.85) 0%, rgba(21, 18, 15, 0.9) 100%);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid var(--status-color);
        border-radius: 18px;
        overflow: hidden;
    }
    .sr-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, var(--status-color), transparent 70%);
        opacity: 0.12;
        pointer-events: none;
    }
    .sr-hero-avatar {
        width: 64px; height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        display: grid; place-items: center;
        font-size: 22px; font-weight: 900;
        letter-spacing: -0.02em;
        flex-shrink: 0;
        box-shadow: 0 8px 24px -8px rgba(201, 169, 97, 0.6);
        position: relative; z-index: 1;
    }
    .sr-hero-content { flex: 1; min-width: 0; position: relative; z-index: 1; }
    .sr-hero-label {
        font-size: 10px; font-weight: 800;
        color: var(--status-color);
        text-transform: uppercase; letter-spacing: 0.14em;
        margin-bottom: 6px;
    }
    .sr-hero-title {
        font-size: 24px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.02em;
        line-height: 1.1; margin-bottom: 10px;
    }
    .sr-hero-meta {
        display: flex; align-items: center; gap: 8px;
        flex-wrap: wrap; font-size: 12px; color: #a1a1aa;
    }
    .sr-hero-code {
        font-family: ui-monospace, monospace;
        color: #c9a961; font-weight: 700;
    }
    .sr-hero-dot { color: #52525b; }
    .sr-hero-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 100px;
        font-size: 10.5px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .sr-status-dot {
        width: 6px; height: 6px; border-radius: 50%;
        flex-shrink: 0;
    }
    .sr-hero-actions {
        display: flex; gap: 10px;
        position: relative; z-index: 1;
        flex-shrink: 0;
    }

    /* ═══ STATS ═══ */
    .sr-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    .sr-stat {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        position: relative; overflow: hidden;
        transition: all 0.2s;
    }
    .sr-stat::before {
        content: ''; position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
    }
    .sr-stat-primary::before { background: linear-gradient(90deg, #c9a961, transparent); }
    .sr-stat-blue::before    { background: linear-gradient(90deg, #3b82f6, transparent); }
    .sr-stat-amber::before   { background: linear-gradient(90deg, #f59e0b, transparent); }
    .sr-stat:hover { transform: translateY(-2px); border-color: rgba(201, 169, 97, 0.2); }
    .sr-stat-icon {
        width: 38px; height: 38px; border-radius: 11px;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .sr-stat-primary .sr-stat-icon { background: rgba(201, 169, 97, 0.15); color: #c9a961; }
    .sr-stat-blue    .sr-stat-icon { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .sr-stat-amber   .sr-stat-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .sr-stat-body { flex: 1; min-width: 0; }
    .sr-stat-lbl {
        font-size: 10px; font-weight: 800; color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 4px;
    }
    .sr-stat-val {
        font-size: 16px; font-weight: 800; color: #fafafa;
        letter-spacing: -0.01em;
    }
    .sr-stat-val.blue  { color: #3b82f6; }
    .sr-stat-val.amber { color: #f59e0b; }
    .sr-stat-val.green { color: #22c55e; }

    /* ═══ GRID ═══ */
    .sr-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 16px;
        align-items: start;
    }

    /* ═══ CARD ═══ */
    .sr-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
    }
    .sr-card:last-child { margin-bottom: 0; }

    .sr-card-head {
        display: flex; align-items: center; gap: 12px;
        padding-bottom: 14px; margin-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .sr-card-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        display: grid; place-items: center; flex-shrink: 0;
    }
    .sr-card-icon.green { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .sr-card-icon.blue  { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .sr-card-title {
        font-size: 14px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.01em;
    }
    .sr-card-sub {
        font-size: 10.5px; color: #71717a; margin-top: 2px;
    }

    /* ═══ INFO GRID ═══ */
    .sr-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .sr-info-item {
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 10px;
    }
    .sr-info-lbl {
        font-size: 9.5px; font-weight: 800;
        color: #71717a;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 6px;
    }
    .sr-info-val {
        font-size: 13px; font-weight: 700;
        color: #fafafa;
        word-break: break-word;
    }
    .sr-info-val.mono {
        font-family: ui-monospace, monospace;
        font-size: 12px;
        letter-spacing: 0.02em;
    }

    /* ═══ STATUS CARD ═══ */
    .sr-status-rows {
        display: flex; flex-direction: column;
        margin-bottom: 16px;
    }
    .sr-status-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0;
        font-size: 12.5px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .sr-status-row:last-child { border-bottom: none; }
    .sr-status-lbl { color: #71717a; font-weight: 600; }
    .sr-status-val {
        color: #fafafa; font-weight: 800;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .sr-status-val.green { color: #22c55e; }
    .sr-status-val.red   { color: #ef4444; }
    .sr-status-dot-sm {
        width: 6px; height: 6px; border-radius: 50%;
    }

    /* ═══ BUTTON ═══ */
    .sr-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 7px; padding: 11px 16px;
        border-radius: 10px;
        font-size: 12.5px; font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.18s;
        font-family: inherit;
        white-space: nowrap;
    }
    .sr-btn-primary {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        color: #0f0f14;
        box-shadow: 0 6px 18px -6px rgba(201, 169, 97, 0.6);
    }
    .sr-btn-primary:hover {
        background: linear-gradient(135deg, #d4b672, #c9a961);
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -8px rgba(201, 169, 97, 0.7);
    }
    .sr-btn-full { width: 100%; }

    /* ═══ TIMELINE ═══ */
    .sr-timeline { display: flex; flex-direction: column; gap: 14px; position: relative; padding-left: 20px; }
    .sr-timeline::before {
        content: ''; position: absolute;
        left: 5px; top: 8px; bottom: 8px;
        width: 1px;
        background: rgba(255, 255, 255, 0.06);
    }
    .sr-timeline-item { position: relative; }
    .sr-timeline-dot {
        position: absolute;
        left: -20px; top: 4px;
        width: 11px; height: 11px; border-radius: 50%;
        background: #c9a961;
        border: 2px solid rgba(21, 18, 15, 0.9);
        box-shadow: 0 0 0 2px rgba(201, 169, 97, 0.25);
    }
    .sr-timeline-dot.green {
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
    }
    .sr-timeline-title {
        font-size: 12.5px; font-weight: 800;
        color: #fafafa; margin-bottom: 3px;
    }
    .sr-timeline-time {
        font-size: 10.5px; color: #71717a;
    }

    /* ═══ RESPONSIVE ═══ */
    @media (max-width: 1100px) {
        .sr-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .sr-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }
        .sr-hero-actions { width: 100%; }
        .sr-hero-actions .sr-btn { flex: 1; }
        .sr-hero-title { font-size: 20px; }
        .sr-stats { grid-template-columns: 1fr; }
        .sr-info-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush