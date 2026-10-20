@extends('layouts.admin')

@section('title', $payment->payment_number)
@section('subtitle', 'Payment Details')

@section('content')

@php
    $statusConfig = [
        'label' => 'Pending Verification',
        'color' => '#fbbf24', // Amber
        'bg' => 'rgba(251, 191, 36, 0.1)',
        'border' => 'rgba(251, 191, 36, 0.3)',
        'icon'  => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
    ];
    if ($payment->verification_status === 'verified') {
        $statusConfig = [
            'label' => 'Payment Verified',
            'color' => '#4ade80', // Green
            'bg' => 'rgba(74, 222, 128, 0.1)',
            'border' => 'rgba(74, 222, 128, 0.3)',
            'icon'  => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>',
        ];
    } elseif ($payment->verification_status === 'rejected') {
        $statusConfig = [
            'label' => 'Payment Rejected',
            'color' => '#ef4444', // Red
            'bg' => 'rgba(239, 68, 68, 0.1)',
            'border' => 'rgba(239, 68, 68, 0.3)',
            'icon'  => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>',
        ];
    }
@endphp

<div class="pro-wrapper">

    <!-- Back Button -->
    <a href="{{ route('consignment.payments.index') }}" class="pro-back">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span>Back to Payments</span>
    </a>

    <!-- Hero Section -->
    <div class="pro-hero">
        <div class="pro-hero-glow" style="background: {{ $statusConfig['color'] }};"></div>
        <div class="pro-hero-border" style="background: {{ $statusConfig['color'] }};"></div>

        <div class="pro-hero-content">
            <div class="pro-hero-left">
                <div class="pro-hero-icon" style="color: {{ $statusConfig['color'] }}; background: {{ $statusConfig['bg'] }}; border-color: {{ $statusConfig['border'] }};">
                    {!! $statusConfig['icon'] !!}
                </div>
                <div class="pro-hero-info">
                    <div class="pro-hero-eyebrow" style="color: {{ $statusConfig['color'] }};">{{ $statusConfig['label'] }}</div>
                    <h1 class="pro-hero-title">{{ $payment->payment_number }}</h1>
                    <div class="pro-hero-meta">
                        <span class="pro-meta-item">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            {{ $payment->store->store_name ?? '-' }}
                        </span>
                        <span class="pro-meta-dot">•</span>
                        <span class="pro-meta-item">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y · g:i A') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="pro-hero-right">
                <div class="pro-stat">
                    <div class="pro-stat-label">Total Amount</div>
                    <div class="pro-stat-value text-gold">₱{{ number_format($payment->amount, 2) }}</div>
                </div>
                <div class="pro-stat-divider"></div>
                <div class="pro-stat">
                    <div class="pro-stat-label">Payment Method</div>
                    <div class="pro-stat-value">{{ strtoupper($payment->method) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="pro-grid">
        
        <!-- Left Column (Receipt / Notes) -->
        <div class="pro-col">
            @if(strtolower($payment->method) !== 'cash')
            <div class="pro-card">
                <div class="pro-card-header">
                    <div class="pro-card-icon text-blue">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="pro-card-title">Proof of Payment</h3>
                        <p class="pro-card-sub">Uploaded receipt for this transaction</p>
                    </div>
                </div>
                
                <div class="pro-receipt-wrapper">
                    @if($payment->proof_of_payment)
                        <img src="{{ asset('storage/' . $payment->proof_of_payment) }}" alt="Receipt" class="pro-receipt-img">
                    @else
                        <div class="pro-empty-state">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>No Receipt Uploaded</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="pro-card">
                <div class="pro-card-header">
                    <div class="pro-card-icon text-gray">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                    </div>
                    <div>
                        <h3 class="pro-card-title">Payment Notes</h3>
                        <p class="pro-card-sub">Additional details from the store</p>
                    </div>
                </div>
                <div class="pro-notes">{{ $payment->notes ?: 'No notes provided.' }}</div>
            </div>
        </div>

        <!-- Right Column (Details & Actions) -->
        <div class="pro-col">
            
            <div class="pro-card">
                <div class="pro-card-header">
                    <div class="pro-card-icon text-green">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div>
                        <h3 class="pro-card-title">Transaction Details</h3>
                    </div>
                </div>

                <div class="pro-details-list">
                    <div class="pro-detail-row">
                        <span class="pro-detail-label">Reference Number</span>
                        <span class="pro-detail-value">{{ $payment->reference_number ?: 'N/A' }}</span>
                    </div>
                    <div class="pro-detail-row">
                        <span class="pro-detail-label">Payment Date</span>
                        <span class="pro-detail-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="pro-detail-row">
                        <span class="pro-detail-label">Date Submitted</span>
                        <span class="pro-detail-value">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y · g:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="pro-card">
                <div class="pro-card-header">
                    <div class="pro-card-icon text-gold">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <h3 class="pro-card-title">Administrative Actions</h3>
                    </div>
                </div>

                <div class="pro-actions">
                    @if($payment->verification_status === 'pending')
                        <form method="POST" action="{{ route('consignment.payments.approve', $payment->id) }}" style="flex: 1;">
                            @csrf
                            <button type="submit" class="pro-btn pro-btn-approve">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('consignment.payments.reject', $payment->id) }}" style="flex: 1;">
                            @csrf
                            <button type="submit" class="pro-btn pro-btn-reject">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </form>
                    @else
                        <div class="pro-status-badge" style="color: {{ $statusConfig['color'] }}; background: {{ $statusConfig['bg'] }}; border-color: {{ $statusConfig['border'] }};">
                            {!! $statusConfig['icon'] !!}
                            {{ $statusConfig['label'] }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* PRO UI DESIGN SYSTEM */
    .pro-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Back Button */
    .pro-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 16px; margin-bottom: 24px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 100px; color: #A1A1AA;
        font-size: 13px; font-weight: 600; text-decoration: none;
        transition: all 0.2s ease;
    }
    .pro-back:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #FFF; transform: translateX(-3px);
    }

    /* Hero Card */
    .pro-hero {
        position: relative; overflow: hidden;
        background: rgba(24, 24, 27, 0.6);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px; padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);
    }
    .pro-hero-glow {
        position: absolute; top: -100px; left: -100px;
        width: 300px; height: 300px; border-radius: 50%;
        filter: blur(100px); opacity: 0.15; pointer-events: none;
    }
    .pro-hero-border {
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 4px; opacity: 0.8;
    }
    .pro-hero-content {
        display: flex; justify-content: space-between; align-items: center; gap: 32px; flex-wrap: wrap;
        position: relative; z-index: 1;
    }
    
    .pro-hero-left { display: flex; align-items: center; gap: 20px; }
    .pro-hero-icon {
        width: 72px; height: 72px; border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid; flex-shrink: 0;
    }
    .pro-hero-eyebrow {
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1.5px; margin-bottom: 8px;
    }
    .pro-hero-title {
        font-size: 32px; font-weight: 800; color: #FFF;
        margin: 0 0 12px 0; letter-spacing: -0.5px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .pro-hero-meta {
        display: flex; align-items: center; gap: 12px;
        font-size: 13px; font-weight: 500; color: #A1A1AA;
    }
    .pro-meta-item { display: flex; align-items: center; gap: 6px; }
    .pro-meta-dot { color: #52525B; }

    .pro-hero-right {
        display: flex; align-items: center; gap: 32px;
        background: rgba(0, 0, 0, 0.3);
        padding: 16px 24px; border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .pro-stat { text-align: right; }
    .pro-stat-label {
        font-size: 10px; font-weight: 700; color: #A1A1AA;
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;
    }
    .pro-stat-value {
        font-size: 20px; font-weight: 800; color: #FFF;
        font-variant-numeric: tabular-nums;
    }
    .pro-stat-value.text-gold { color: #E8A33D; }
    .pro-stat-divider { width: 1px; height: 40px; background: rgba(255,255,255,0.1); }

    /* Grid Layout */
    .pro-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
    }
    @media (max-width: 900px) { .pro-grid { grid-template-columns: 1fr; } }
    .pro-col { display: flex; flex-direction: column; gap: 24px; }

    /* Cards */
    .pro-card {
        background: rgba(24, 24, 27, 0.5);
        backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px; padding: 24px;
        transition: border-color 0.3s ease;
    }
    .pro-card:hover { border-color: rgba(232, 163, 61, 0.2); }

    .pro-card-header {
        display: flex; align-items: flex-start; gap: 16px;
        padding-bottom: 20px; margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .pro-card-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.05); flex-shrink: 0;
    }
    .pro-card-icon.text-blue { color: #60A5FA; background: rgba(96, 165, 250, 0.1); }
    .pro-card-icon.text-green { color: #4ADE80; background: rgba(74, 222, 128, 0.1); }
    .pro-card-icon.text-gold { color: #E8A33D; background: rgba(232, 163, 61, 0.1); }
    .pro-card-icon.text-gray { color: #A1A1AA; background: rgba(161, 161, 170, 0.1); }

    .pro-card-title { font-size: 16px; font-weight: 700; color: #FFF; margin: 0 0 4px 0; }
    .pro-card-sub { font-size: 12px; color: #A1A1AA; margin: 0; }

    /* Receipt Image */
    .pro-receipt-wrapper {
        background: rgba(0, 0, 0, 0.4);
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 16px; padding: 20px;
        display: flex; justify-content: center; align-items: center;
        min-height: 300px; overflow: hidden;
    }
    .pro-receipt-img {
        max-width: 100%; max-height: 500px;
        object-fit: contain; border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        transition: transform 0.3s ease;
    }
    .pro-receipt-img:hover { transform: scale(1.02); }
    .pro-empty-state {
        display: flex; flex-direction: column; align-items: center; gap: 12px;
        color: #52525B; font-size: 14px; font-weight: 600;
    }

    /* Notes */
    .pro-notes {
        font-size: 14px; color: #D4D4D8; line-height: 1.6;
        background: rgba(255, 255, 255, 0.02);
        padding: 16px; border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.04);
    }

    /* Details List */
    .pro-details-list { display: flex; flex-direction: column; gap: 16px; }
    .pro-detail-row {
        display: flex; justify-content: space-between; align-items: center;
        padding-bottom: 12px; border-bottom: 1px dashed rgba(255, 255, 255, 0.06);
    }
    .pro-detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .pro-detail-label { font-size: 13px; font-weight: 600; color: #A1A1AA; }
    .pro-detail-value { font-size: 14px; font-weight: 700; color: #F3F4F6; text-align: right; }

    /* Actions */
    .pro-actions { display: flex; gap: 16px; margin-top: 8px; }
    .pro-btn {
        width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 16px 24px; border-radius: 12px; font-size: 14px; font-weight: 700;
        cursor: pointer; border: none; transition: all 0.2s ease;
    }
    .pro-btn-approve {
        background: #10B981; color: #FFF;
        box-shadow: 0 8px 20px -6px rgba(16, 185, 129, 0.5);
    }
    .pro-btn-approve:hover {
        background: #059669; transform: translateY(-2px);
        box-shadow: 0 12px 25px -6px rgba(16, 185, 129, 0.6);
    }
    .pro-btn-reject {
        background: rgba(239, 68, 68, 0.1); color: #EF4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .pro-btn-reject:hover {
        background: rgba(239, 68, 68, 0.2); transform: translateY(-2px);
    }
    .pro-status-badge {
        width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;
        padding: 18px; border-radius: 12px; border: 1px solid;
        font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;
    }
</style>
@endpush