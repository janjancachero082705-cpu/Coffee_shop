@extends('layouts.admin')

@section('title', $store->store_name)
@section('subtitle', 'Store details & history')

@section('actions')
    <a href="{{ route('stores.edit', $store) }}" class="btn btn-ghost btn-sm">Edit</a>
    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-primary btn-sm">+ Delivery</a>
@endsection

@section('content')

@php
    $totalDelivered = (float) $store->deliveryReceipts()->sum('total_amount');
    $totalPaid = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $balance = max(0, $totalDelivered - $totalPaid);
    $drCount = $store->deliveryReceipts()->count();
    $recentDrs = $store->deliveryReceipts()->latest()->take(5)->get();

    // Payment progress (0-100)
    $paidPct = $totalDelivered > 0 ? min(100, round(($totalPaid / $totalDelivered) * 100)) : 0;
    $isActive = ($store->status ?? 'inactive') === 'active';

    // Show Record Payment kung naay balance OR naay pending order
    $hasPendingOrder = \App\Models\ReorderRequest::where('store_id', $store->id)
        ->whereRaw('LOWER(status) = ?', ['pending'])
        ->exists();
    $showPaymentBtn = ($balance > 0) || $hasPendingOrder;
@endphp

{{-- ===== BACK BUTTON ===== --}}
<a href="{{ route('stores.index') }}" class="sd-back">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    <span>Back to Stores</span>
</a>

{{-- ===== HERO ===== --}}
<div class="sd-hero">
    <div class="sd-hero-noise"></div>
    <div class="sd-hero-orb sd-hero-orb-1"></div>
    <div class="sd-hero-orb sd-hero-orb-2"></div>

    <div class="sd-hero-inner">
        <div class="sd-hero-left">
            <div class="sd-hero-avatar">
                @if($store->logo_url)
                    <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}">
                @else
                    {{ strtoupper(substr($store->store_name, 0, 2)) }}
                @endif
                <span class="sd-hero-avatar-ring"></span>
            </div>

            <div class="sd-hero-info">
                <div class="sd-hero-eyebrow">
                    <span class="sd-hero-eyebrow-dot" style="background:{{ $isActive ? '#22c55e' : '#7a7a85' }};box-shadow:0 0 8px {{ $isActive ? '#22c55e' : 'transparent' }};"></span>
                    {{ $isActive ? 'Active Store' : ucfirst($store->status ?? 'Inactive') }}
                </div>
                <h1 class="sd-hero-title">{{ $store->store_name }}</h1>
                <div class="sd-hero-meta">
                    <span class="sd-hero-code">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h16"/></svg>
                        {{ $store->code }}
                    </span>
                    <span class="sd-hero-divider"></span>
                    <span class="sd-hero-tag">{{ ucfirst(str_replace('_',' ',$store->payment_terms)) }}</span>
                </div>
            </div>
        </div>

        <div class="sd-hero-right">
            <div class="sd-hero-stat">
                <div class="sd-hero-stat-label">Balance</div>
                <div class="sd-hero-stat-value {{ $balance > 0 ? 'is-accent' : 'is-green' }}">
                    &#8369;{{ number_format($balance, 0) }}
                </div>
            </div>
            <div class="sd-hero-stat-divider"></div>
            <div class="sd-hero-stat">
                <div class="sd-hero-stat-label">Credit Limit</div>
                <div class="sd-hero-stat-value">&#8369;{{ number_format($store->credit_limit, 0) }}</div>
            </div>
        </div>
    </div>

    {{-- Payment progress bar --}}
    @if($totalDelivered > 0)
        <div class="sd-hero-progress">
            <div class="sd-hero-progress-head">
                <span>Payment Progress</span>
                <span class="sd-hero-progress-pct">{{ $paidPct }}%</span>
            </div>
            <div class="sd-hero-progress-track">
                <div class="sd-hero-progress-fill" style="width:{{ $paidPct }}%;"></div>
            </div>
        </div>
    @endif
</div>

{{-- ===== MINI KPIs ===== --}}
<div class="sd-kpis">
    <div class="sd-kpi">
        <div class="sd-kpi-icon sd-kpi-icon-gold">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div class="sd-kpi-body">
            <div class="sd-kpi-value">{{ $drCount }}</div>
            <div class="sd-kpi-label">Deliveries</div>
        </div>
    </div>

    <div class="sd-kpi">
        <div class="sd-kpi-icon sd-kpi-icon-blue">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="sd-kpi-body">
            <div class="sd-kpi-value">&#8369;{{ number_format($totalDelivered/1000, 1) }}k</div>
            <div class="sd-kpi-label">Delivered</div>
        </div>
    </div>

    <div class="sd-kpi">
        <div class="sd-kpi-icon sd-kpi-icon-green">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </div>
        <div class="sd-kpi-body">
            <div class="sd-kpi-value">&#8369;{{ number_format($totalPaid/1000, 1) }}k</div>
            <div class="sd-kpi-label">Paid</div>
        </div>
    </div>

    <div class="sd-kpi">
        <div class="sd-kpi-icon sd-kpi-icon-red">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="sd-kpi-body">
            <div class="sd-kpi-value">&#8369;{{ number_format($balance/1000, 1) }}k</div>
            <div class="sd-kpi-label">Balance</div>
        </div>
    </div>
</div>

{{-- ===== DETAIL GRID ===== --}}
<div class="sd-grid">

    {{-- ── LEFT COLUMN ───────────────────────── --}}
    <div class="sd-col">

        {{-- Store Information --}}
        <div class="sd-card">
            <div class="sd-card-header">
                <div class="sd-card-header-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="sd-card-title">Store Information</div>
            </div>

            <div class="sd-info-list">
                <div class="sd-info-row">
                    <div class="sd-info-label">
                        <div class="sd-info-icon">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        Owner
                    </div>
                    <div class="sd-info-value">{{ $store->owner_name }}</div>
                </div>

                <div class="sd-info-row">
                    <div class="sd-info-label">
                        <div class="sd-info-icon">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        </div>
                        Contact
                    </div>
                    <div class="sd-info-value">{{ $store->contact_number }}</div>
                </div>

                <div class="sd-info-row">
                    <div class="sd-info-label">
                        <div class="sd-info-icon">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
                        </div>
                        Email
                    </div>
                    <div class="sd-info-value">{{ $store->email ?? '—' }}</div>
                </div>

                <div class="sd-info-row">
                    <div class="sd-info-label">
                        <div class="sd-info-icon">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        Address
                    </div>
                    <div class="sd-info-value">{{ $store->address }}{{ $store->city ? ', '.$store->city : '' }}</div>
                </div>

                @if($store->notes)
                    <div class="sd-info-row">
                        <div class="sd-info-label">
                            <div class="sd-info-icon">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                            </div>
                            Notes
                        </div>
                        <div class="sd-info-value">{{ $store->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Deliveries --}}
        <div class="sd-card">
            <div class="sd-card-header">
                <div class="sd-card-header-icon sd-card-header-icon-blue">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div class="sd-card-title-group">
                    <div class="sd-card-title">Recent Deliveries</div>
                    <div class="sd-card-sub">Last 5 delivery receipts</div>
                </div>
                @if($drCount > 5)
                    <a href="{{ route('deliveries.index') }}?store={{ $store->id }}" class="sd-link">
                        View all
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                @endif
            </div>

            @if($recentDrs->isEmpty())
                <div class="sd-empty">
                    <div class="sd-empty-icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <rect x="1" y="3" width="15" height="13" rx="1"/>
                            <path d="M16 8h4l3 3v5h-7V8z"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                    <div class="sd-empty-title">No deliveries yet</div>
                    <div class="sd-empty-text">Wala pang delivery records para sa store na ito</div>
                    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="sd-btn-primary">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        Add First Delivery
                    </a>
                </div>
            @else
                <div class="sd-dr-list">
                    @foreach($recentDrs as $dr)
                        <a href="{{ route('deliveries.show', $dr) }}" class="sd-dr-row">
                            <div class="sd-dr-icon">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="1" y="3" width="15" height="13"/>
                                    <path d="M16 8h4l3 3v5h-7V8z"/>
                                    <circle cx="5.5" cy="18.5" r="2.5"/>
                                    <circle cx="18.5" cy="18.5" r="2.5"/>
                                </svg>
                            </div>
                            <div class="sd-dr-info">
                                <div class="sd-dr-number">{{ $dr->dr_number }}</div>
                                <div class="sd-dr-date">{{ $dr->delivery_date }}</div>
                            </div>
                            <div class="sd-dr-amount">&#8369;{{ number_format($dr->total_amount, 2) }}</div>
                            <span class="sd-badge sd-badge-{{ $dr->status }}">
                                <span class="sd-badge-dot"></span>
                                {{ ucfirst($dr->status) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── RIGHT COLUMN ──────────────────────── --}}
    <div class="sd-col">

        {{-- Account Summary --}}
        <div class="sd-card sd-card-summary">
            <div class="sd-card-header">
                <div class="sd-card-header-icon sd-card-header-icon-gold">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </div>
                <div class="sd-card-title">Account Summary</div>
            </div>

            <div class="sd-balance-list">
                <div class="sd-balance-row">
                    <span class="sd-balance-label">
                        <span class="sd-balance-dot" style="background:#3b82f6;"></span>
                        Total Delivered
                    </span>
                    <span class="sd-balance-value">&#8369;{{ number_format($totalDelivered, 2) }}</span>
                </div>
                <div class="sd-balance-row">
                    <span class="sd-balance-label">
                        <span class="sd-balance-dot" style="background:#22c55e;"></span>
                        Total Paid
                    </span>
                    <span class="sd-balance-value sd-balance-value-green">− &#8369;{{ number_format($totalPaid, 2) }}</span>
                </div>
            </div>

            <div class="sd-balance-total {{ $balance > 0 ? 'is-owed' : 'is-clear' }}">
                <div class="sd-balance-total-label">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        @if($balance > 0)
                            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                        @else
                            <path d="M5 12l5 5L20 7"/>
                        @endif
                    </svg>
                    {{ $balance > 0 ? 'Unpaid Balance' : 'Fully Paid' }}
                </div>
                <div class="sd-balance-total-value">&#8369;{{ number_format($balance, 2) }}</div>
            </div>

            @if($showPaymentBtn)
                <a href="{{ route('consignment.payments.create') }}?store_id={{ $store->id }}" class="sd-btn-primary sd-btn-full">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Record Payment
                </a>
            @else
                <div class="sd-paid-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12l5 5L20 7"/>
                    </svg>
                    <div class="sd-paid-badge-text">
                        <div class="sd-paid-badge-title">All Settled</div>
                        <div class="sd-paid-badge-sub">Walay balanse ug pending orders</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="sd-card">
            <div class="sd-card-header">
                <div class="sd-card-header-icon sd-card-header-icon-purple">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div class="sd-card-title">Timeline</div>
            </div>

            <div class="sd-timeline">
                <div class="sd-timeline-item">
                    <div class="sd-timeline-marker">
                        <span class="sd-timeline-dot"></span>
                        <span class="sd-timeline-line"></span>
                    </div>
                    <div class="sd-timeline-body">
                        <div class="sd-timeline-label">Store Created</div>
                        <div class="sd-timeline-date">{{ $store->created_at->format('M d, Y g:i A') }}</div>
                        <div class="sd-timeline-ago">{{ $store->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                <div class="sd-timeline-item">
                    <div class="sd-timeline-marker">
                        <span class="sd-timeline-dot sd-timeline-dot-muted"></span>
                    </div>
                    <div class="sd-timeline-body">
                        <div class="sd-timeline-label">Last Updated</div>
                        <div class="sd-timeline-date">{{ $store->updated_at->format('M d, Y g:i A') }}</div>
                        <div class="sd-timeline-ago">{{ $store->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════ */
/* STORE DETAILS — PREMIUM REDESIGN               */
/* ═══════════════════════════════════════════════ */

/* ── BACK BUTTON ───────────────────────────────── */
.sd-back{
    display:inline-flex;align-items:center;gap:8px;
    padding:8px 14px 8px 11px;
    background:rgba(34,34,44,.55);
    border:1px solid rgba(255,255,255,.07);
    border-radius:10px;
    color:#a8a5a0;font-size:12.5px;font-weight:600;
    text-decoration:none;
    margin-bottom:18px;
    transition:all .2s cubic-bezier(.4,0,.2,1);
    backdrop-filter:blur(10px);
}
.sd-back:hover{
    background:rgba(201,169,97,.08);
    border-color:rgba(201,169,97,.28);
    color:#c9a961;
    transform:translateX(-3px);
}
.sd-back svg{transition:transform .2s}
.sd-back:hover svg{transform:translateX(-2px)}

/* ── HERO ──────────────────────────────────────── */
.sd-hero{
    position:relative;
    padding:26px;
    background:linear-gradient(135deg,#14141b 0%,#1c1c26 45%,#232330 100%);
    border:1px solid rgba(201,169,97,.14);
    border-radius:20px;
    margin-bottom:16px;
    overflow:hidden;
    box-shadow:0 20px 48px -20px rgba(0,0,0,.55),inset 0 1px 0 rgba(255,255,255,.03);
}
.sd-hero-noise{
    position:absolute;inset:0;
    background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.025) 1px,transparent 0);
    background-size:24px 24px;pointer-events:none;opacity:.6;
}
.sd-hero-orb{
    position:absolute;border-radius:50%;pointer-events:none;filter:blur(60px);
}
.sd-hero-orb-1{
    width:340px;height:340px;top:-160px;right:-120px;
    background:radial-gradient(circle,rgba(201,169,97,.4),transparent 70%);
    animation:sdOrb 8s ease-in-out infinite;
}
.sd-hero-orb-2{
    width:220px;height:220px;bottom:-120px;left:-80px;
    background:radial-gradient(circle,rgba(59,130,246,.15),transparent 70%);
    animation:sdOrb 10s ease-in-out infinite reverse;
}
@keyframes sdOrb{
    0%,100%{opacity:.55;transform:scale(1) translate(0,0);}
    50%{opacity:.9;transform:scale(1.15) translate(-10px,10px);}
}

.sd-hero-inner{
    position:relative;z-index:1;
    display:flex;align-items:center;justify-content:space-between;
    gap:24px;flex-wrap:wrap;
}
.sd-hero-left{
    display:flex;align-items:center;gap:18px;
    flex:1;min-width:280px;
}
.sd-hero-avatar{
    position:relative;
    width:72px;height:72px;border-radius:18px;
    background:linear-gradient(135deg,#c9a961,#8a5f36);
    display:grid;place-items:center;
    color:#fff;font-size:24px;font-weight:800;
    letter-spacing:-.02em;
    box-shadow:0 16px 32px -12px rgba(201,169,97,.55);
    flex-shrink:0;overflow:hidden;
}
.sd-hero-avatar img{width:100%;height:100%;object-fit:cover}
.sd-hero-avatar-ring{
    position:absolute;inset:-3px;border-radius:21px;
    border:1.5px solid rgba(201,169,97,.35);
    pointer-events:none;
}

.sd-hero-info{flex:1;min-width:0}
.sd-hero-eyebrow{
    display:inline-flex;align-items:center;gap:6px;
    font-size:10px;font-weight:800;
    color:#c9a961;text-transform:uppercase;
    letter-spacing:.14em;
    margin-bottom:8px;
}
.sd-hero-eyebrow-dot{
    width:6px;height:6px;border-radius:50%;
}
.sd-hero-title{
    font-size:26px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.03em;line-height:1.15;margin:0 0 8px;
}
.sd-hero-meta{
    display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.sd-hero-code{
    display:inline-flex;align-items:center;gap:5px;
    font-family:ui-monospace,SFMono-Regular,monospace;
    font-size:11.5px;font-weight:700;color:#c9a961;
    padding:3px 9px;
    background:rgba(201,169,97,.1);
    border:1px solid rgba(201,169,97,.2);
    border-radius:6px;
}
.sd-hero-divider{width:1px;height:14px;background:rgba(255,255,255,.1)}
.sd-hero-tag{
    font-size:11.5px;font-weight:600;color:#9a97a3;
    padding:3px 10px;
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.07);
    border-radius:6px;
}

.sd-hero-right{
    display:flex;align-items:center;gap:24px;
    padding:14px 22px;
    background:rgba(255,255,255,.035);
    border:1px solid rgba(255,255,255,.07);
    border-radius:14px;
    backdrop-filter:blur(10px);
}
.sd-hero-stat{text-align:right}
.sd-hero-stat-label{
    font-size:9.5px;font-weight:700;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.12em;
    margin-bottom:5px;
}
.sd-hero-stat-value{
    font-size:20px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.025em;line-height:1;
}
.sd-hero-stat-value.is-accent{color:#fbbf24}
.sd-hero-stat-value.is-green{color:#4ade80}
.sd-hero-stat-divider{
    width:1px;height:36px;background:rgba(255,255,255,.1);
}

.sd-hero-progress{
    position:relative;z-index:1;
    margin-top:20px;padding-top:16px;
    border-top:1px dashed rgba(255,255,255,.08);
}
.sd-hero-progress-head{
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:8px;
    font-size:10.5px;font-weight:700;color:#9a97a3;
    text-transform:uppercase;letter-spacing:.08em;
}
.sd-hero-progress-pct{color:#c9a961;font-weight:800}
.sd-hero-progress-track{
    height:6px;border-radius:100px;
    background:rgba(255,255,255,.06);
    overflow:hidden;
}
.sd-hero-progress-fill{
    height:100%;border-radius:100px;
    background:linear-gradient(90deg,#c9a961,#fbbf24);
    box-shadow:0 0 12px rgba(201,169,97,.5);
    transition:width .5s cubic-bezier(.4,0,.2,1);
}

/* ── KPI GRID ──────────────────────────────────── */
.sd-kpis{
    display:grid;grid-template-columns:repeat(4,1fr);
    gap:12px;margin-bottom:16px;
}
.sd-kpi{
    display:flex;align-items:center;gap:12px;
    padding:14px 16px;
    background:rgba(34,34,44,.55);
    border:1px solid rgba(255,255,255,.06);
    border-radius:14px;
    transition:all .22s cubic-bezier(.4,0,.2,1);
    backdrop-filter:blur(10px);
}
.sd-kpi:hover{
    transform:translateY(-2px);
    border-color:rgba(201,169,97,.25);
    box-shadow:0 12px 24px -12px rgba(0,0,0,.5);
}
.sd-kpi-icon{
    width:38px;height:38px;border-radius:11px;
    display:grid;place-items:center;flex-shrink:0;
}
.sd-kpi-icon-gold{
    background:rgba(201,169,97,.12);color:#c9a961;
    border:1px solid rgba(201,169,97,.22);
}
.sd-kpi-icon-blue{
    background:rgba(59,130,246,.12);color:#60a5fa;
    border:1px solid rgba(59,130,246,.22);
}
.sd-kpi-icon-green{
    background:rgba(34,197,94,.12);color:#4ade80;
    border:1px solid rgba(34,197,94,.22);
}
.sd-kpi-icon-red{
    background:rgba(239,68,68,.12);color:#f87171;
    border:1px solid rgba(239,68,68,.22);
}
.sd-kpi-body{min-width:0;flex:1}
.sd-kpi-value{
    font-size:18px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.02em;line-height:1;margin-bottom:4px;
}
.sd-kpi-label{
    font-size:9.5px;font-weight:700;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.12em;
}

/* ── GRID ──────────────────────────────────────── */
.sd-grid{
    display:grid;grid-template-columns:1.6fr 1fr;
    gap:16px;
}
.sd-col{display:flex;flex-direction:column;gap:16px}

/* ── CARD ──────────────────────────────────────── */
.sd-card{
    background:rgba(34,34,44,.5);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.06);
    border-radius:16px;
    padding:18px;
    transition:border-color .2s;
}
.sd-card:hover{border-color:rgba(201,169,97,.14)}
.sd-card-summary{
    background:linear-gradient(160deg,rgba(34,34,44,.65),rgba(28,28,36,.5));
}

.sd-card-header{
    display:flex;align-items:center;gap:10px;
    margin-bottom:14px;
}
.sd-card-header-icon{
    width:32px;height:32px;border-radius:10px;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.22);
    color:#c9a961;
    display:grid;place-items:center;flex-shrink:0;
}
.sd-card-header-icon-blue{
    background:rgba(59,130,246,.12);
    border-color:rgba(59,130,246,.22);
    color:#60a5fa;
}
.sd-card-header-icon-gold{
    background:rgba(201,169,97,.16);
    border-color:rgba(201,169,97,.28);
    color:#c9a961;
}
.sd-card-header-icon-purple{
    background:rgba(139,92,246,.12);
    border-color:rgba(139,92,246,.22);
    color:#a78bfa;
}
.sd-card-title{
    font-size:13px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;
}
.sd-card-title-group{flex:1}
.sd-card-sub{
    font-size:11px;color:#7a7a85;font-weight:500;
    margin-top:2px;
}

.sd-link{
    display:inline-flex;align-items:center;gap:4px;
    font-size:11.5px;font-weight:700;
    color:#c9a961;text-decoration:none;
    padding:5px 10px;
    background:rgba(201,169,97,.08);
    border:1px solid rgba(201,169,97,.18);
    border-radius:8px;
    transition:all .18s;
    margin-left:auto;
}
.sd-link:hover{
    background:rgba(201,169,97,.15);
    border-color:rgba(201,169,97,.35);
}
.sd-link svg{transition:transform .18s}
.sd-link:hover svg{transform:translateX(2px)}

/* ── INFO LIST ─────────────────────────────────── */
.sd-info-list{display:flex;flex-direction:column}
.sd-info-row{
    display:flex;justify-content:space-between;align-items:center;
    gap:14px;padding:11px 0;
    border-bottom:1px dashed rgba(255,255,255,.05);
}
.sd-info-row:last-child{border-bottom:none}
.sd-info-row:first-child{padding-top:4px}

.sd-info-label{
    display:inline-flex;align-items:center;gap:9px;
    font-size:11.5px;font-weight:700;color:#9a97a3;
    text-transform:uppercase;letter-spacing:.05em;
    flex-shrink:0;
}
.sd-info-icon{
    width:24px;height:24px;border-radius:7px;
    background:rgba(255,255,255,.04);
    display:grid;place-items:center;color:#7a7a85;
    flex-shrink:0;
}
.sd-info-value{
    font-size:13px;color:#f5f3f0;
    font-weight:500;text-align:right;
    word-break:break-word;
    min-width:0;
}

/* ── DR LIST ───────────────────────────────────── */
.sd-dr-list{display:flex;flex-direction:column;gap:4px}
.sd-dr-row{
    display:flex;align-items:center;gap:12px;
    padding:10px 12px;
    border-radius:10px;
    text-decoration:none;color:inherit;
    transition:all .15s;
    border:1px solid transparent;
}
.sd-dr-row:hover{
    background:rgba(255,255,255,.035);
    border-color:rgba(255,255,255,.06);
}
.sd-dr-icon{
    width:34px;height:34px;border-radius:10px;
    background:rgba(59,130,246,.1);
    border:1px solid rgba(59,130,246,.2);
    color:#60a5fa;
    display:grid;place-items:center;flex-shrink:0;
}
.sd-dr-info{flex:1;min-width:0}
.sd-dr-number{
    font-size:12px;font-weight:800;color:#f5f3f0;
    font-family:ui-monospace,monospace;
}
.sd-dr-date{
    font-size:10.5px;color:#7a7a85;
    margin-top:2px;font-weight:500;
}
.sd-dr-amount{
    font-size:13px;font-weight:800;
    color:#c9a961;flex-shrink:0;
    letter-spacing:-.01em;
}

/* ── BADGES ────────────────────────────────────── */
.sd-badge{
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 9px;border-radius:100px;
    font-size:9.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.05em;
    flex-shrink:0;
}
.sd-badge-dot{
    width:5px;height:5px;border-radius:50%;
    background:currentColor;
    box-shadow:0 0 6px currentColor;
}
.sd-badge-delivered{background:rgba(34,197,94,.14);color:#4ade80;border:1px solid rgba(34,197,94,.28)}
.sd-badge-pending{background:rgba(245,158,11,.14);color:#fbbf24;border:1px solid rgba(245,158,11,.28)}
.sd-badge-active{background:rgba(34,197,94,.14);color:#4ade80;border:1px solid rgba(34,197,94,.28)}
.sd-badge-inactive{background:rgba(255,255,255,.06);color:#a8a5a0;border:1px solid rgba(255,255,255,.1)}
.sd-badge-cancelled{background:rgba(239,68,68,.14);color:#f87171;border:1px solid rgba(239,68,68,.28)}
.sd-badge-partial{background:rgba(245,158,11,.14);color:#fbbf24;border:1px solid rgba(245,158,11,.28)}

/* ── BALANCE ───────────────────────────────────── */
.sd-balance-list{
    display:flex;flex-direction:column;
    padding-bottom:14px;
    border-bottom:1px dashed rgba(255,255,255,.07);
}
.sd-balance-row{
    display:flex;justify-content:space-between;align-items:center;
    padding:9px 0;font-size:12.5px;
}
.sd-balance-label{
    display:inline-flex;align-items:center;gap:8px;
    color:#9a97a3;font-weight:600;
}
.sd-balance-dot{
    width:7px;height:7px;border-radius:50%;
    box-shadow:0 0 8px currentColor;
}
.sd-balance-value{
    font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;
}
.sd-balance-value-green{color:#4ade80}

.sd-balance-total{
    display:flex;justify-content:space-between;align-items:center;
    padding:14px 16px;margin:14px 0;
    border-radius:12px;
    border:1px solid;
}
.sd-balance-total.is-owed{
    background:rgba(245,158,11,.08);
    border-color:rgba(245,158,11,.25);
}
.sd-balance-total.is-clear{
    background:rgba(34,197,94,.08);
    border-color:rgba(34,197,94,.25);
}
.sd-balance-total-label{
    display:inline-flex;align-items:center;gap:7px;
    font-size:11px;font-weight:800;
    text-transform:uppercase;letter-spacing:.08em;
}
.is-owed .sd-balance-total-label{color:#fbbf24}
.is-clear .sd-balance-total-label{color:#4ade80}
.sd-balance-total-value{
    font-size:18px;font-weight:800;
    letter-spacing:-.02em;
}
.is-owed .sd-balance-total-value{color:#fbbf24}
.is-clear .sd-balance-total-value{color:#4ade80}

/* ── BUTTONS ───────────────────────────────────── */
.sd-btn-primary{
    display:inline-flex;align-items:center;justify-content:center;
    gap:7px;
    padding:11px 18px;
    background:linear-gradient(135deg,#c9a961,#a9784a);
    color:#0f0f14;
    font-size:12.5px;font-weight:800;
    border:none;border-radius:10px;
    cursor:pointer;text-decoration:none;
    transition:all .2s cubic-bezier(.4,0,.2,1);
    box-shadow:0 8px 20px -8px rgba(201,169,97,.55);
    letter-spacing:.01em;
}
.sd-btn-primary:hover{
    transform:translateY(-1px);
    box-shadow:0 12px 24px -8px rgba(201,169,97,.7);
    filter:brightness(1.05);
}
.sd-btn-primary:active{transform:translateY(0)}
.sd-btn-full{width:100%}

/* ── ALL SETTLED BADGE ─────────────────────────── */
.sd-paid-badge{
    display:flex;align-items:center;gap:12px;
    padding:14px 16px;
    background:rgba(34,197,94,.06);
    border:1px solid rgba(34,197,94,.2);
    border-radius:12px;
}
.sd-paid-badge svg{
    width:34px;height:34px;padding:8px;
    background:rgba(34,197,94,.12);
    border:1px solid rgba(34,197,94,.25);
    color:#4ade80;
    border-radius:10px;
    flex-shrink:0;
}
.sd-paid-badge-text{min-width:0;flex:1}
.sd-paid-badge-title{
    font-size:13px;font-weight:800;color:#4ade80;
    letter-spacing:-.01em;line-height:1.2;
    margin-bottom:3px;
}
.sd-paid-badge-sub{
    font-size:11px;color:#7a7a85;font-weight:500;
}

/* ── TIMELINE ──────────────────────────────────── */
.sd-timeline{display:flex;flex-direction:column;gap:0}
.sd-timeline-item{
    display:flex;gap:12px;align-items:flex-start;
    padding-bottom:14px;
}
.sd-timeline-item:last-child{padding-bottom:0}
.sd-timeline-marker{
    display:flex;flex-direction:column;align-items:center;
    flex-shrink:0;
}
.sd-timeline-dot{
    width:10px;height:10px;border-radius:50%;
    background:#c9a961;
    box-shadow:0 0 0 4px rgba(201,169,97,.15),0 0 12px rgba(201,169,97,.6);
    margin-top:4px;
}
.sd-timeline-dot-muted{
    background:#7a7a85;
    box-shadow:0 0 0 4px rgba(122,122,133,.15);
}
.sd-timeline-line{
    width:2px;flex:1;min-height:24px;
    background:linear-gradient(180deg,rgba(201,169,97,.4),rgba(201,169,97,.05));
    margin-top:4px;
}
.sd-timeline-body{flex:1;min-width:0}
.sd-timeline-label{
    font-size:12.5px;font-weight:700;color:#f5f3f0;
    margin-bottom:3px;
}
.sd-timeline-date{
    font-size:11px;color:#a8a5a0;font-weight:500;
    font-family:ui-monospace,monospace;
}
.sd-timeline-ago{
    font-size:10.5px;color:#7a7a85;font-weight:600;
    margin-top:2px;text-transform:uppercase;letter-spacing:.04em;
}

/* ── EMPTY ─────────────────────────────────────── */
.sd-empty{
    text-align:center;padding:36px 20px;
}
.sd-empty-icon{
    width:56px;height:56px;margin:0 auto 12px;
    border-radius:16px;
    background:rgba(201,169,97,.08);
    border:1px solid rgba(201,169,97,.18);
    color:#c9a961;
    display:grid;place-items:center;
}
.sd-empty-title{
    font-size:14px;font-weight:800;color:#f5f3f0;
    margin-bottom:5px;letter-spacing:-.01em;
}
.sd-empty-text{
    font-size:12px;color:#9a97a3;font-weight:500;
    margin-bottom:14px;
}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width:1200px){
    .sd-grid{grid-template-columns:1fr}
}
@media (max-width:1024px){
    .sd-hero-inner{flex-direction:column;align-items:stretch}
    .sd-hero-right{width:100%;justify-content:space-around}
    .sd-kpis{grid-template-columns:repeat(2,1fr)}
}
@media (max-width:768px){
    .sd-hero{padding:20px}
    .sd-hero-avatar{width:60px;height:60px;font-size:20px;border-radius:14px}
    .sd-hero-title{font-size:21px}
    .sd-hero-right{flex-direction:column;gap:0;padding:0;background:transparent;border:none}
    .sd-hero-stat{text-align:left;width:100%;padding:10px 14px;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.07);border-radius:12px}
    .sd-hero-stat + .sd-hero-stat{margin-top:8px}
    .sd-hero-stat-divider{display:none}
    .sd-kpi{padding:12px 14px}
    .sd-kpi-value{font-size:16px}
}
@media (max-width:480px){
    .sd-hero-title{font-size:18px}
    .sd-kpis{grid-template-columns:1fr}
    .sd-info-row{flex-direction:column;align-items:flex-start;gap:5px}
    .sd-info-value{text-align:left;width:100%}
    .sd-dr-row{flex-wrap:wrap}
    .sd-badge{margin-left:auto}
}
</style>
@endpush