@extends('layouts.admin')

@section('title', $store->store_name)
@section('subtitle', 'Store details & history')

@section('actions')
    <a href="{{ route('stores.edit', $store) }}" class="btn btn-ghost btn-sm">Edit</a>
    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-primary btn-sm">+ Delivery</a>
@endsection

@section('content')

{{-- ===== BACK BUTTON ===== --}}
<a href="{{ route('stores.index') }}" class="back-btn">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    <span>Back to Stores</span>
</a>

@php
    $totalDelivered = (float) $store->deliveryReceipts()->sum('total_amount');
    $totalPaid = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $balance = max(0, $totalDelivered - $totalPaid);
    $drCount = $store->deliveryReceipts()->count();
    $recentDrs = $store->deliveryReceipts()->latest()->take(5)->get();
@endphp

{{-- ===== HERO CARD ===== --}}
<div class="hero-store">
    <div class="hero-store-left">
        <div class="hero-store-avatar">
            @if($store->logo_url)
                <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
            @else
                {{ strtoupper(substr($store->store_name, 0, 2)) }}
            @endif
        </div>
        <div>
            <div class="hero-store-name">{{ $store->store_name }}</div>
            <div class="hero-store-code">{{ $store->code }}</div>
            <div class="hero-store-tags">
                <span class="badge badge-{{ $store->status }}">{{ ucfirst($store->status) }}</span>
                <span class="tag">{{ ucfirst(str_replace('_',' ',$store->payment_terms)) }}</span>
            </div>
        </div>
    </div>
    <div class="hero-store-right">
        <div class="hero-store-stat">
            <div class="hero-store-stat-label">Balance</div>
            <div class="hero-store-stat-value">&#8369;{{ number_format($balance, 0) }}</div>
        </div>
        <div class="hero-store-stat">
            <div class="hero-store-stat-label">Credit Limit</div>
            <div class="hero-store-stat-value">&#8369;{{ number_format($store->credit_limit, 0) }}</div>
        </div>
    </div>
</div>

{{-- ===== STATS ===== --}}
<div class="mini-grid">
    <div class="mini">
        <div class="mini-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">{{ $drCount }}</div>
            <div class="mini-label">Deliveries</div>
        </div>
    </div>
    <div class="mini">
        <div class="mini-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">&#8369;{{ number_format($totalDelivered/1000, 1) }}k</div>
            <div class="mini-label">Delivered</div>
        </div>
    </div>
    <div class="mini">
        <div class="mini-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">&#8369;{{ number_format($totalPaid/1000, 1) }}k</div>
            <div class="mini-label">Paid</div>
        </div>
    </div>
    <div class="mini">
        <div class="mini-icon red">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="mini-content">
            <div class="mini-value">&#8369;{{ number_format($balance/1000, 1) }}k</div>
            <div class="mini-label">Balance</div>
        </div>
    </div>
</div>

<div class="detail-grid">

    {{-- ===== LEFT ===== --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Store Information</div>
            </div>

            <div class="info-list">
                <div class="info-row">
                    <div class="info-label">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Owner
                    </div>
                    <div class="info-value">{{ $store->owner_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        Contact
                    </div>
                    <div class="info-value">{{ $store->contact_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
                        Email
                    </div>
                    <div class="info-value">{{ $store->email ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Address
                    </div>
                    <div class="info-value">{{ $store->address }}{{ $store->city ? ', '.$store->city : '' }}</div>
                </div>
                @if($store->notes)
                    <div class="info-row">
                        <div class="info-label">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                            Notes
                        </div>
                        <div class="info-value">{{ $store->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Recent Deliveries</div>
                    <div class="card-sub">Last 5 delivery receipts</div>
                </div>
                @if($drCount > 5)
                    <a href="{{ route('deliveries.index') }}?store={{ $store->id }}" class="link">View all</a>
                @endif
            </div>

            @if($recentDrs->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <rect x="1" y="3" width="15" height="13" rx="1"/>
                            <path d="M16 8h4l3 3v5h-7V8z"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                    <div class="empty-text">No deliveries yet</div>
                    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-primary btn-sm" style="margin-top:12px;">+ First Delivery</a>
                </div>
            @else
                <div class="dr-list">
                    @foreach($recentDrs as $dr)
                        <a href="{{ route('deliveries.show', $dr) }}" class="dr-row">
                            <div class="dr-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="1" y="3" width="15" height="13"/>
                                    <path d="M16 8h4l3 3v5h-7V8z"/>
                                    <circle cx="5.5" cy="18.5" r="2.5"/>
                                    <circle cx="18.5" cy="18.5" r="2.5"/>
                                </svg>
                            </div>
                            <div class="dr-info">
                                <div class="dr-number">{{ $dr->dr_number }}</div>
                                <div class="dr-date">{{ $dr->delivery_date }}</div>
                            </div>
                            <div class="dr-amount">&#8369;{{ number_format($dr->total_amount, 2) }}</div>
                            <span class="badge badge-{{ $dr->status }}">{{ ucfirst($dr->status) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== RIGHT ===== --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Account Summary</div>
            </div>

            <div class="balance-list">
                <div class="balance-row">
                    <span class="balance-label">Total Delivered</span>
                    <span class="balance-value">&#8369;{{ number_format($totalDelivered, 2) }}</span>
                </div>
                <div class="balance-row">
                    <span class="balance-label">Total Paid</span>
                    <span class="balance-value green">- &#8369;{{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="balance-row balance-total">
                    <span class="balance-label">Unpaid Balance</span>
                    <span class="balance-value {{ $balance > 0 ? 'accent' : 'green' }}">&#8369;{{ number_format($balance, 2) }}</span>
                </div>
            </div>

            <a href="{{ route('consignment.payments.create') }}?store_id={{ $store->id }}" class="btn btn-primary" style="width:100%; margin-top:16px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Record Payment
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Timeline</div>
            </div>

            <div class="timeline">
                <div class="timeline-row">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-label">Created</div>
                        <div class="timeline-date">{{ $store->created_at->format('M d, Y g:i A') }}</div>
                    </div>
                </div>
                <div class="timeline-row">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-label">Last Updated</div>
                        <div class="timeline-date">{{ $store->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ===== BACK BUTTON ===== */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px 8px 10px;
        background: rgba(34, 34, 44, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 9px;
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 16px;
        transition: all 0.15s ease;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .back-btn:hover {
        background: rgba(169, 120, 74, 0.1);
        border-color: rgba(169, 120, 74, 0.3);
        color: #c9a961;
        transform: translateX(-3px);
    }
    .back-btn svg { transition: transform 0.2s ease; }
    .back-btn:hover svg { transform: translateX(-2px); }

    /* ===== HERO ===== */
    .hero-store {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(34, 34, 44, 0.7));
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        position: relative;
        overflow: hidden;
    }
    .hero-store::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.15), transparent 70%);
        pointer-events: none;
    }
    .hero-store-left {
        display: flex; align-items: center;
        gap: 18px; position: relative; z-index: 1;
    }
    .hero-store-avatar {
        width: 64px; height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #c9a961, #8a5f36);
        display: grid; place-items: center;
        color: #fff; font-size: 22px; font-weight: 800;
        letter-spacing: 0.02em;
        box-shadow: 0 12px 24px -8px rgba(169, 120, 74, 0.6);
        flex-shrink: 0;
        overflow: hidden;
    }
    .hero-store-avatar img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .hero-store-name {
        font-size: 22px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em; line-height: 1.2;
        margin-bottom: 4px;
    }
    .hero-store-code {
        font-family: ui-monospace, monospace;
        font-size: 12px; color: #c9a961;
        font-weight: 600; margin-bottom: 10px;
    }
    .hero-store-tags { display: flex; gap: 6px; }
    .tag {
        display: inline-flex; align-items: center;
        padding: 4px 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        border-radius: 5px;
        font-size: 11px; font-weight: 600;
        color: #c9a961;
    }
    .hero-store-right {
        display: flex; gap: 28px;
        position: relative; z-index: 1;
    }
    .hero-store-stat { text-align: right; }
    .hero-store-stat-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700; margin-bottom: 4px;
    }
    .hero-store-stat-value {
        font-size: 20px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }

    /* ===== MINI KPIs ===== */
    .mini-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .mini {
        background: rgba(34, 34, 44, 0.55);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 16px;
        display: flex; align-items: center;
        gap: 12px;
        transition: all 0.2s;
    }
    .mini:hover {
        transform: translateY(-2px);
        border-color: rgba(169, 120, 74, 0.25);
    }
    .mini-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        display: grid; place-items: center;
        color: #c9a961;
        flex-shrink: 0;
    }
    .mini-icon.blue { background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .mini-icon.green { background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .mini-icon.red { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #ef4444; }

    .mini-value {
        font-size: 18px; font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em; line-height: 1;
        margin-bottom: 3px;
    }
    .mini-label {
        font-size: 10px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        font-weight: 700;
    }

    /* ===== DETAIL GRID ===== */
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }

    /* ===== INFO LIST ===== */
    .info-list { display: flex; flex-direction: column; }
    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .info-row:last-child { border-bottom: none; }
    .info-label {
        display: flex; align-items: center;
        gap: 8px;
        font-size: 12px; color: var(--text-muted);
        font-weight: 600; flex-shrink: 0;
    }
    .info-label svg { color: var(--text-muted); }
    .info-value {
        font-size: 13px; color: var(--text-primary);
        font-weight: 500; text-align: right;
        word-break: break-word;
    }

    /* ===== DR LIST ===== */
    .dr-list { display: flex; flex-direction: column; gap: 6px; }
    .dr-row {
        display: flex; align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 10px;
        transition: all 0.15s;
    }
    .dr-row:hover { background: rgba(255, 255, 255, 0.03); }
    .dr-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .dr-info { flex: 1; min-width: 0; }
    .dr-number {
        font-size: 12px; font-weight: 700;
        color: var(--text-primary);
    }
    .dr-date {
        font-size: 10px; color: var(--text-muted);
        margin-top: 2px;
    }
    .dr-amount {
        font-size: 13px; font-weight: 700;
        color: #c9a961; flex-shrink: 0;
    }

    /* ===== BALANCE ===== */
    .balance-list { display: flex; flex-direction: column; }
    .balance-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        font-size: 13px;
    }
    .balance-row + .balance-row { border-top: 1px solid rgba(255, 255, 255, 0.04); }
    .balance-label { color: var(--text-secondary); }
    .balance-value { font-weight: 700; color: var(--text-primary); }
    .balance-value.green { color: #22c55e; }
    .balance-value.accent { color: #c9a961; }
    .balance-total {
        padding-top: 14px;
        margin-top: 4px;
        border-top: 1px solid rgba(169, 120, 74, 0.2) !important;
    }
    .balance-total .balance-value { font-size: 15px; }

    /* ===== TIMELINE ===== */
    .timeline { display: flex; flex-direction: column; gap: 14px; }
    .timeline-row {
        display: flex; gap: 12px;
        align-items: flex-start;
    }
    .timeline-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #c9a961;
        box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.15);
        flex-shrink: 0; margin-top: 5px;
    }
    .timeline-label {
        font-size: 12px; font-weight: 600;
        color: var(--text-primary);
    }
    .timeline-date {
        font-size: 11px; color: var(--text-muted);
        margin-top: 2px;
    }

    .link {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .link:hover { color: #c9a961; }

    .empty-state {
        padding: 30px 20px;
        text-align: center;
    }
    .empty-icon {
        color: rgba(169, 120, 74, 0.4);
        margin-bottom: 8px;
        display: grid;
        place-items: center;
    }
    .empty-text {
        font-size: 12px; color: var(--text-muted);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1100px) {
        .mini-grid { grid-template-columns: repeat(2, 1fr); }
        .detail-grid { grid-template-columns: 1fr; }
        .hero-store { flex-direction: column; align-items: flex-start; }
        .hero-store-right { width: 100%; justify-content: space-between; }
    }
    @media (max-width: 700px) {
        .mini-grid { grid-template-columns: 1fr; }
        .hero-store-right { flex-direction: column; gap: 14px; }
        .hero-store-stat { text-align: left; }
    }
</style>
@endpush