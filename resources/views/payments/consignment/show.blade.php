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
                        <form id="approve-form-{{ $payment->id }}" method="POST" action="{{ route('consignment.payments.approve', $payment->id) }}" style="flex: 1;">
                            @csrf
                            <button type="button" onclick="openDecisionModal('approve', {{ $payment->id }}, '{{ $payment->payment_number }}', '₱{{ number_format($payment->amount, 2) }}', '{{ $payment->store->store_name ?? '-' }}', '{{ strtoupper($payment->method) }}')" class="pro-btn pro-btn-approve">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                                Approve
                            </button>
                        </form>
                        <form id="reject-form-{{ $payment->id }}" method="POST" action="{{ route('consignment.payments.reject', $payment->id) }}" style="flex: 1;">
                            @csrf
                            <button type="button" onclick="openDecisionModal('reject', {{ $payment->id }}, '{{ $payment->payment_number }}', '₱{{ number_format($payment->amount, 2) }}', '{{ $payment->store->store_name ?? '-' }}', '{{ strtoupper($payment->method) }}')" class="pro-btn pro-btn-reject">
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
<!-- UNIVERSAL DECISION MODAL -->
<div id="decisionModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); backdrop-filter:blur(12px); z-index:999999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#16161d; border:1px solid rgba(255,255,255,.08); border-radius:22px; width:100%; max-width:520px; box-shadow:0 40px 100px -30px rgba(0,0,0,.9); display:flex; flex-direction:column; overflow:hidden;">
        
        <!-- HEADER -->
        <div style="position:relative; display:flex; align-items:center; gap:16px; padding:24px; border-bottom:1px solid rgba(255,255,255,.06);">
            <div style="position:absolute; top:-80px; right:-80px; width:240px; height:240px; background:radial-gradient(circle,rgba(245,158,11,.22),transparent 65%); pointer-events:none;"></div>
            <div id="decisionIcon" style="width:54px; height:54px; border-radius:15px; display:grid; place-items:center; flex-shrink:0; border:1px solid; position:relative; z-index:1;"></div>
            <div style="flex:1; min-width:0; position:relative; z-index:1;">
                <div id="decisionEyebrow" style="font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.15em; margin-bottom:5px;"></div>
                <div id="decisionTitle" style="font-size:18px; font-weight:800; color:#f5f3f0; letter-spacing:-.02em; margin-bottom:4px;"></div>
                <div id="decisionSub" style="font-size:11.5px; color:#7a7a85; font-family:ui-monospace,monospace;"></div>
            </div>
            <button type="button" onclick="closeDecisionModal()" style="width:34px; height:34px; border-radius:10px; background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); color:#a8a5a0; display:grid; place-items:center; cursor:pointer; transition:.15s; position:relative; z-index:1;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- BODY -->
        <div style="padding:24px; overflow-y:auto;">
            <div id="decisionAlert" style="display:flex; align-items:flex-start; gap:12px; padding:14px 16px; border-radius:14px; margin-bottom:18px; border:1px solid;">
                <div style="flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                </div>
                <div id="decisionAlertText" style="flex:1; font-size:13px; line-height:1.5; padding-top:2px;"></div>
            </div>

            <!-- FACTS GRID -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:18px;">
                <div style="padding:12px; background:rgba(0,0,0,.22); border:1px solid rgba(255,255,255,.05); border-radius:12px;">
                    <div style="font-size:9px; font-weight:800; color:#7a7a85; text-transform:uppercase; margin-bottom:4px;">Payment No.</div>
                    <div id="decisionPayNo" style="font-size:13px; font-weight:800; color:#f5f3f0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">-</div>
                </div>
                <div style="padding:12px; background:rgba(0,0,0,.22); border:1px solid rgba(255,255,255,.05); border-radius:12px;">
                    <div style="font-size:9px; font-weight:800; color:#7a7a85; text-transform:uppercase; margin-bottom:4px;">Amount</div>
                    <div id="decisionAmount" style="font-size:13px; font-weight:800; color:#E8A33D;">-</div>
                </div>
                <div style="padding:12px; background:rgba(0,0,0,.22); border:1px solid rgba(255,255,255,.05); border-radius:12px;">
                    <div style="font-size:9px; font-weight:800; color:#7a7a85; text-transform:uppercase; margin-bottom:4px;">Store</div>
                    <div id="decisionStore" style="font-size:13px; font-weight:800; color:#f5f3f0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">-</div>
                </div>
                <div style="padding:12px; background:rgba(0,0,0,.22); border:1px solid rgba(255,255,255,.05); border-radius:12px;">
                    <div style="font-size:9px; font-weight:800; color:#7a7a85; text-transform:uppercase; margin-bottom:4px;">Method</div>
                    <div id="decisionMethod" style="font-size:13px; font-weight:800; color:#f5f3f0;">-</div>
                </div>
            </div>

            <!-- REASON FIELD -->
            <div id="decisionReasonContainer" style="display:none; margin-top:4px;">
                <label style="display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:800; color:#d4d1cc; text-transform:uppercase; margin-bottom:8px;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                    Rejection Reason <span style="color:#EF4444;">*</span>
                </label>
                <textarea id="decisionReason" class="dv-mo-textarea" placeholder="e.g., Invalid reference number, blurry receipt, etc..." oninput="this.style.borderColor='rgba(255,255,255,.08)'; document.getElementById('reasonError').style.display='none';"></textarea>
                <div id="reasonError" style="display: none; color: #ef4444; font-size: 11.5px; font-weight: 600; margin-top: 6px;">
                    Please provide a rejection reason.
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div style="display:flex; gap:10px; padding:20px 24px; border-top:1px solid rgba(255,255,255,.05); background:rgba(0,0,0,.12);">
            <button type="button" onclick="closeDecisionModal()" style="flex:0 0 auto; padding:13px 20px; background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.1); color:#d4d1cc; border-radius:12px; font-weight:800; cursor:pointer;">Cancel</button>
            <button type="button" id="decisionConfirmBtn" onclick="confirmDecision()" style="flex:1; padding:13px 18px; border:none; border-radius:12px; color:#fff; font-weight:800; cursor:pointer; transition:.18s;">Confirm</button>
        </div>
    </div>
</div>

<script>
    let currentAction = '';
    let currentPaymentId = '';

    function openDecisionModal(action, id, payNo, amount, store, method) {
        currentAction = action;
        currentPaymentId = id;
        
        const modal = document.getElementById('decisionModal');
        const icon = document.getElementById('decisionIcon');
        const eyebrow = document.getElementById('decisionEyebrow');
        const title = document.getElementById('decisionTitle');
        const sub = document.getElementById('decisionSub');
        const alertBox = document.getElementById('decisionAlert');
        const alertText = document.getElementById('decisionAlertText');
        const reasonBox = document.getElementById('decisionReasonContainer');
        const confirmBtn = document.getElementById('decisionConfirmBtn');
        const reasonInput = document.getElementById('decisionReason');

        // Populate Facts
        document.getElementById('decisionPayNo').textContent = payNo;
        document.getElementById('decisionAmount').textContent = amount;
        document.getElementById('decisionStore').textContent = store;
        document.getElementById('decisionMethod').textContent = method;
        reasonInput.value = '';

        if (action === 'approve') {
            icon.style.cssText = 'background:rgba(34,197,94,.15); border-color:rgba(34,197,94,.4); color:#22c55e; box-shadow:0 8px 24px -8px rgba(34,197,94,.5);';
            icon.innerHTML = '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>';
            eyebrow.style.color = '#4ade80'; eyebrow.textContent = 'APPROVE ACTION';
            title.textContent = 'Confirm Payment Approval';
            sub.textContent = 'Payment will be marked as verified.';
            alertBox.style.cssText = 'background:rgba(34,197,94,.08); border-color:rgba(34,197,94,.25); color:#bbf7d0;';
            alertText.innerHTML = 'Are you sure you want to <strong>APPROVE</strong> this payment? This will update the store\'s balance immediately.';
            reasonBox.style.display = 'none';
            confirmBtn.style.cssText = 'flex:1; padding:13px 18px; background:linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border:none; border-radius:12px; color:#fff; font-weight:800; cursor:pointer; box-shadow:0 8px 24px -8px rgba(34,197,94,.7);';
            confirmBtn.innerHTML = 'Yes, Approve';
        } else {
            icon.style.cssText = 'background:rgba(239,68,68,.15); border-color:rgba(239,68,68,.4); color:#ef4444; box-shadow:0 8px 24px -8px rgba(239,68,68,.5);';
            icon.innerHTML = '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>';
            eyebrow.style.color = '#f87171'; eyebrow.textContent = 'REJECT ACTION';
            title.textContent = 'Confirm Payment Rejection';
            sub.textContent = 'Customer will be notified to re-submit.';
            alertBox.style.cssText = 'background:rgba(239,68,68,.08); border-color:rgba(239,68,68,.25); color:#fecaca;';
            alertText.innerHTML = 'Are you sure you want to <strong>REJECT</strong> this payment? The customer will see the reason you provide below.';
            reasonBox.style.display = 'block';
            confirmBtn.style.cssText = 'flex:1; padding:13px 18px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border:none; border-radius:12px; color:#fff; font-weight:800; cursor:pointer; box-shadow:0 8px 24px -8px rgba(239,68,68,.7);';
            confirmBtn.innerHTML = 'Yes, Reject';
        }

        modal.style.display = 'flex';
    }

    function closeDecisionModal() {
        document.getElementById('decisionModal').style.display = 'none';
    }

    function confirmDecision() {
        if (currentAction === 'approve') {
            document.getElementById('approve-form-' + currentPaymentId).submit();
        } else {
            const reason = document.getElementById('decisionReason').value.trim();
            if (!reason) {
            document.getElementById('decisionReason').style.borderColor = '#ef4444';
            document.getElementById('reasonError').style.display = 'block';
            return;
        }
            
            const rejectForm = document.getElementById('reject-form-' + currentPaymentId);
            const oldInput = rejectForm.querySelector('input[name="reason"]');
            if(oldInput) oldInput.remove();

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'reason';
            hiddenInput.value = reason;
            rejectForm.appendChild(hiddenInput);

            rejectForm.submit();
        }
    }
</script>
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