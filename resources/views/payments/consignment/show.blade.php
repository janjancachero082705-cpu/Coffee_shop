@extends('layouts.admin')

@section('title', $payment->payment_number)
@section('subtitle', 'Payment details')

@section('content')

@php
    $vStatus = $payment->verification_status ?? 'pending';
    $isPending = $vStatus === 'pending';
    $isVerified = $vStatus === 'verified';
    $isRejected = $vStatus === 'rejected';

    // Hero color based on verification status
    if ($isPending) {
        $heroColor = '#f59e0b';
        $heroBg = 'rgba(245,158,11,0.12)';
        $heroLabel = 'Waiting for Approval';
    } elseif ($isRejected) {
        $heroColor = '#ef4444';
        $heroBg = 'rgba(239,68,68,0.12)';
        $heroLabel = 'Payment Rejected';
    } else {
        $heroColor = '#22c55e';
        $heroBg = 'rgba(34,197,94,0.12)';
        $heroLabel = 'Payment Verified';
    }

    $method = $payment->method ?? 'cash';
    $methodConfig = match($method) {
        'cash' => [
            'label' => 'Cash',
            'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>',
            'color' => '#22c55e',
            'bg' => 'rgba(34,197,94,0.12)',
        ],
        'gcash' => [
            'label' => 'GCash',
            'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>',
            'color' => '#3b82f6',
            'bg' => 'rgba(59,130,246,0.12)',
        ],
        'maya' => [
            'label' => 'Maya',
            'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>',
            'color' => '#a855f7',
            'bg' => 'rgba(168,85,247,0.12)',
        ],
        'bank_transfer' => [
            'label' => 'Bank Transfer',
            'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10l9-6 9 6M5 10v10M19 10v10M9 10v10M15 10v10M2 20h20"/></svg>',
            'color' => '#f59e0b',
            'bg' => 'rgba(245,158,11,0.12)',
        ],
        'check' => [
            'label' => 'Check',
            'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>',
            'color' => '#8a8378',
            'bg' => 'rgba(138,131,120,0.12)',
        ],
        default => [
            'label' => ucfirst($method),
            'icon' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
            'color' => '#c9a961',
            'bg' => 'rgba(201,169,97,0.12)',
        ],
    };
@endphp

{{-- ========== HERO ========== --}}
<div class="pay-hero" style="--method-color: {{ $methodConfig['color'] }};">
    <div class="pay-hero-icon" style="background: {{ $methodConfig['bg'] }}; color: {{ $methodConfig['color'] }};">
        {!! $methodConfig['icon'] !!}
    </div>

    <div class="pay-hero-content">
        <div class="pay-hero-label" style="color: {{ $heroColor }};">{{ $heroLabel }}</div>
        <div class="pay-hero-title">{{ $payment->payment_number }}</div>
        <div class="pay-hero-meta">
            <span class="pay-hero-method" style="background: {{ $methodConfig['bg'] }}; color: {{ $methodConfig['color'] }}; border-color: {{ $methodConfig['color'] }}33;">
                <span class="pay-method-dot" style="background: {{ $methodConfig['color'] }};"></span>
                {{ $methodConfig['label'] }}
            </span>
            <span class="pay-hero-store">{{ $payment->store->store_name ?? '-' }}</span>
            @if($payment->store->code ?? null)
                <span class="pay-hero-dot">·</span>
                <span class="pay-hero-code">{{ $payment->store->code }}</span>
            @endif
        </div>
    </div>

    <div class="pay-hero-amount">
        <div class="pay-hero-amount-label">Amount Paid</div>
        <div class="pay-hero-amount-value" style="color: {{ $heroColor }};">&#8369;{{ number_format($payment->amount, 2) }}</div>
    </div>
</div>

{{-- ═══════════ VERIFICATION STATUS ═══════════ --}}
<div class="vfy-status-banner" style="--vfy-color: {{ $heroColor }};">
    <div class="vfy-status-icon">
        @if($isPending)
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        @elseif($isVerified)
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        @else
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        @endif
    </div>
    <div class="vfy-status-body">
        <div class="vfy-status-title">{{ $heroLabel }}</div>
        <div class="vfy-status-desc">
            @if($isPending)
                Gi-submit na ni nga bayad pero <strong>wala pa ma-approve sa admin</strong>. Dili pa ma-count sa store balance ug sales report hangtud ma-verify.
            @elseif($isVerified)
                Gi-approve na sa admin. Na-count na sa sales report ug store balance.
                @if($payment->verified_at)
                    · {{ \Carbon\Carbon::parse($payment->verified_at)->format('M d, Y g:i A') }}
                @endif
            @else
                <strong>Reason:</strong> {{ $payment->rejection_reason ?? 'Walay reason' }}
            @endif
        </div>
    </div>
</div>

{{-- ========== GRID ========== --}}
<div class="pay-grid">

    {{-- LEFT: Details --}}
    <div class="pay-card">
        <div class="pay-card-head">
            <div class="pay-card-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                </svg>
            </div>
            <div class="pay-card-title">Payment Details</div>
        </div>

        <div class="pay-rows">
            <div class="pay-row">
                <span class="pay-row-label">Payment #</span>
                <span class="pay-row-value mono">{{ $payment->payment_number }}</span>
            </div>

            <div class="pay-row">
                <span class="pay-row-label">Store</span>
                <span class="pay-row-value">
                    @if($payment->store)
                        <a href="{{ route('stores.show', $payment->store_id) }}" class="pay-link">
                            {{ $payment->store->store_name }}
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    @else
                        -
                    @endif
                </span>
            </div>

            <div class="pay-row">
                <span class="pay-row-label">Payment Date</span>
                <span class="pay-row-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</span>
            </div>

            <div class="pay-row">
                <span class="pay-row-label">Method</span>
                <span class="pay-row-value">
                    <span class="pay-inline-method" style="color: {{ $methodConfig['color'] }};">
                        {!! $methodConfig['icon'] !!}
                        {{ $methodConfig['label'] }}
                    </span>
                </span>
            </div>

            @if($payment->reference_number)
                <div class="pay-row">
                    <span class="pay-row-label">Reference #</span>
                    <span class="pay-row-value mono">{{ $payment->reference_number }}</span>
                </div>
            @endif

            @if($payment->deliveryReceipt)
                <div class="pay-row">
                    <span class="pay-row-label">Linked Delivery</span>
                    <span class="pay-row-value">
                        <a href="{{ route('deliveries.show', $payment->deliveryReceipt->id) }}" class="pay-link">
                            {{ $payment->deliveryReceipt->dr_number }}
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    </span>
                </div>
            @endif

            @if($payment->notes)
                <div class="pay-row pay-row-notes">
                    <span class="pay-row-label">Notes</span>
                    <span class="pay-row-value">{{ $payment->notes }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Amount + Meta --}}
    <div>
        <div class="pay-amount-card" style="--accent: {{ $methodConfig['color'] }};">
            <div class="pay-amount-label">Amount</div>
            <div class="pay-amount-value" style="color: {{ $heroColor }};">
                <span class="pay-amount-symbol">&#8369;</span>{{ number_format($payment->amount, 2) }}
            </div>
            <div class="pay-amount-meta">
                <span class="pay-method-dot" style="background: {{ $methodConfig['color'] }};"></span>
                Received via {{ $methodConfig['label'] }}
            </div>
        </div>

        <div class="pay-meta-card">
            <div class="pay-meta-head">Recorded By</div>
            <div class="pay-meta-row">
                <span class="pay-meta-label">User</span>
                <span class="pay-meta-value">{{ $payment->user->name ?? $payment->created_by_label ?? 'System' }}</span>
            </div>
            <div class="pay-meta-row">
                <span class="pay-meta-label">Created</span>
                <span class="pay-meta-value">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y · g:i A') }}</span>
            </div>
        </div>

        <div class="pay-actions-card">
            <div class="pay-meta-head">Actions</div>
            <a href="{{ route('consignment.payments.index') }}" class="pay-btn pay-btn-ghost">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Payments
            </a>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* ============================================================
       PAYMENT DETAIL — PROFESSIONAL REDESIGN
       ============================================================ */

    /* ========== HERO ========== */
    .pay-hero {
        position: relative;
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 22px 26px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, color-mix(in srgb, var(--method-color) 10%, transparent) 0%, rgba(30, 26, 22, 0.5) 100%);
        border: 1px solid color-mix(in srgb, var(--method-color) 25%, transparent);
        border-radius: 18px;
        overflow: hidden;
    }
    .pay-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 240px; height: 240px;
        background: radial-gradient(circle, color-mix(in srgb, var(--method-color) 15%, transparent), transparent 70%);
        pointer-events: none;
    }
    .pay-hero-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border: 1px solid color-mix(in srgb, var(--method-color) 30%, transparent);
        position: relative;
        z-index: 1;
    }
    .pay-hero-content {
        flex: 1;
        min-width: 0;
        position: relative;
        z-index: 1;
    }
    .pay-hero-label {
        font-size: 10px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 6px;
    }
    .pay-hero-title {
        font-size: 22px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        font-family: ui-monospace, monospace;
        line-height: 1.1;
        margin-bottom: 10px;
    }
    .pay-hero-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .pay-hero-method {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 100px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border: 1px solid;
    }
    .pay-method-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .pay-hero-store {
        font-size: 12px;
        font-weight: 700;
        color: #d4d4d8;
    }
    .pay-hero-dot { color: #52525b; }
    .pay-hero-code {
        font-family: ui-monospace, monospace;
        font-size: 11px;
        color: #71717a;
    }
    .pay-hero-amount {
        text-align: right;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
        padding-left: 20px;
        border-left: 1px solid color-mix(in srgb, var(--method-color) 20%, transparent);
    }
    .pay-hero-amount-label {
        font-size: 10px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 6px;
    }
    .pay-hero-amount-value {
        font-size: 30px;
        font-weight: 800;
        color: var(--method-color);
        letter-spacing: -0.03em;
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }

    /* ========== GRID ========== */
    .pay-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 16px;
    }

    /* ========== DETAILS CARD ========== */
    .pay-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 20px;
    }
    .pay-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .pay-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .pay-card-title {
        font-size: 14px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.01em;
    }

    .pay-rows { display: flex; flex-direction: column; }
    .pay-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .pay-row:last-child { border-bottom: none; }
    .pay-row-notes { display: block; }
    .pay-row-notes .pay-row-value { display: block; margin-top: 6px; text-align: left; }

    .pay-row-label {
        font-size: 12px;
        color: #71717a;
        font-weight: 600;
        flex-shrink: 0;
    }
    .pay-row-value {
        font-size: 13px;
        font-weight: 700;
        color: #fafafa;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .pay-row-value.mono {
        font-family: ui-monospace, monospace;
        color: #d4d4d8;
        font-size: 12.5px;
    }

    .pay-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #c9a961;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.15s;
    }
    .pay-link:hover { color: #d4b673; }
    .pay-link svg { opacity: 0.6; }

    .pay-inline-method {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 100px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .pay-inline-method svg { width: 12px; height: 12px; opacity: 0.8; }

    /* ========== AMOUNT CARD ========== */
    .pay-amount-card {
        position: relative;
        padding: 20px;
        margin-bottom: 14px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent);
        border-radius: 16px;
        text-align: center;
        overflow: hidden;
    }
    .pay-amount-card::before {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 160px; height: 160px;
        background: radial-gradient(circle, color-mix(in srgb, var(--accent) 15%, transparent), transparent 70%);
        pointer-events: none;
    }
    .pay-amount-label {
        font-size: 10px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }
    .pay-amount-value {
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 3px;
        font-size: 32px;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: -0.03em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }
    .pay-amount-symbol { font-size: 22px; opacity: 0.7; }
    .pay-amount-meta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 100px;
        font-size: 11px;
        font-weight: 600;
        color: #a1a1aa;
        position: relative;
        z-index: 1;
    }

    /* ========== META CARD ========== */
    .pay-meta-card {
        padding: 18px;
        margin-bottom: 14px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
    }
    .pay-meta-head {
        font-size: 10px;
        font-weight: 800;
        color: #c9a961;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 12px;
    }
    .pay-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        font-size: 12px;
    }
    .pay-meta-row + .pay-meta-row {
        border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
    .pay-meta-label { color: #71717a; font-weight: 600; }
    .pay-meta-value { color: #fafafa; font-weight: 700; text-align: right; }

    /* ========== ACTIONS CARD ========== */
    .pay-actions-card {
        padding: 18px;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
    }
    .pay-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.15s;
        border: 1px solid;
        font-family: inherit;
    }
    .pay-btn-ghost {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
        color: #d4d4d8;
    }
    .pay-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.15);
        color: #fafafa;
        transform: translateY(-1px);
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 900px) {
        .pay-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .pay-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px 20px;
        }
        .pay-hero-amount {
            padding-left: 0;
            padding-top: 16px;
            border-left: none;
            border-top: 1px solid color-mix(in srgb, var(--method-color) 20%, transparent);
            width: 100%;
            text-align: left;
        }
        .pay-hero-title { font-size: 18px; }
        .pay-hero-amount-value { font-size: 26px; }
        .pay-amount-value { font-size: 28px; }
        .pay-amount-symbol { font-size: 20px; }
    }

    /* ═══════════ VERIFICATION STATUS BANNER ═══════════ */
    .vfy-status-banner {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        margin: 16px 0;
        border-radius: 16px;
        background: linear-gradient(135deg, color-mix(in srgb, var(--vfy-color) 12%, transparent), color-mix(in srgb, var(--vfy-color) 3%, transparent));
        border: 1px solid color-mix(in srgb, var(--vfy-color) 30%, transparent);
        border-left: 3px solid var(--vfy-color);
        animation: vfyBannerIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes vfyBannerIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .vfy-status-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: color-mix(in srgb, var(--vfy-color) 15%, transparent);
        color: var(--vfy-color);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border: 1px solid color-mix(in srgb, var(--vfy-color) 35%, transparent);
        box-shadow: 0 6px 18px -6px color-mix(in srgb, var(--vfy-color) 50%, transparent);
    }
    .vfy-status-body { flex: 1; min-width: 0; }
    .vfy-status-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--vfy-color);
        margin-bottom: 4px;
        letter-spacing: -0.01em;
    }
    .vfy-status-desc {
        font-size: 12px;
        color: #a1a1aa;
        line-height: 1.55;
    }
    .vfy-status-desc strong {
        color: var(--vfy-color);
        font-weight: 800;
    }
</style>
@endpush