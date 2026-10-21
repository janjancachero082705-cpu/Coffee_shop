@extends('layouts.admin')

@section('title', 'Payments')
@section('subtitle', 'Consignment payments from stores')

@section('actions')
    <a href="{{ route('consignment.payments.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Record Payment
    </a>















<!-- ULTIMATE DECISION MODAL (SHIP ACTION STYLE) -->
<style>
    .dv-mo-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(12px); display: none; align-items: center; justify-content: center; z-index: 999999; padding: 20px; }
    .dv-mo-backdrop.active { display: flex; }
    .dv-mo { background: #16161d; border: 1px solid rgba(255,255,255,.08); border-radius: 22px; width: 100%; max-width: 520px; box-shadow: 0 40px 100px -30px rgba(0,0,0,.9); display: flex; flex-direction: column; overflow: hidden; }
    
    .dv-mo-header { position: relative; display: flex; align-items: center; gap: 16px; padding: 24px; border-bottom: 1px solid rgba(255,255,255,.06); }
    .dv-mo-header-glow { position: absolute; top: -80px; right: -80px; width: 240px; height: 240px; background: radial-gradient(circle, rgba(245,158,11,.22), transparent 65%); pointer-events: none; }
    .dv-mo-icon { width: 54px; height: 54px; border-radius: 15px; display: grid; place-items: center; flex-shrink: 0; border: 1px solid; position: relative; z-index: 1; }
    .dv-mo-icon.approve { background: rgba(34,197,94,.15); border-color: rgba(34,197,94,.4); color: #22c55e; box-shadow: 0 8px 24px -8px rgba(34,197,94,.5); }
    .dv-mo-icon.reject { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.4); color: #ef4444; box-shadow: 0 8px 24px -8px rgba(239,68,68,.5); }
    
    .dv-mo-header-body { flex: 1; min-width: 0; position: relative; z-index: 1; }
    .dv-mo-eyebrow { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .15em; margin-bottom: 5px; }
    .dv-mo-eyebrow.approve { color: #4ade80; }
    .dv-mo-eyebrow.reject { color: #f87171; }
    .dv-mo-title { font-size: 18px; font-weight: 800; color: #f5f3f0; letter-spacing: -.02em; margin-bottom: 4px; }
    .dv-mo-sub { font-size: 11.5px; color: #7a7a85; font-family: ui-monospace, monospace; }
    
    .dv-mo-close { width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); color: #a8a5a0; display: grid; place-items: center; cursor: pointer; transition: .15s; position: relative; z-index: 1; }
    .dv-mo-close:hover { background: rgba(255,255,255,.08); color: #f5f3f0; transform: rotate(90deg); }
    
    .dv-mo-body { padding: 24px; overflow-y: auto; }
    
    .dv-mo-alert { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: 14px; margin-bottom: 18px; border: 1px solid; }
    .dv-mo-alert.approve { background: rgba(34,197,94,.08); border-color: rgba(34,197,94,.25); color: #bbf7d0; }
    .dv-mo-alert.reject { background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.25); color: #fecaca; }
    .dv-mo-alert-icon { flex-shrink: 0; }
    
    .dv-mo-facts { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; }
    .dv-mo-fact { padding: 12px; background: rgba(0,0,0,.22); border: 1px solid rgba(255,255,255,.05); border-radius: 12px; }
    .dv-mo-fact-label { font-size: 9px; font-weight: 800; color: #7a7a85; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
    .dv-mo-fact-value { font-size: 13px; font-weight: 800; color: #f5f3f0; font-variant-numeric: tabular-nums; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    
    .dv-mo-field { margin-top: 4px; }
    .dv-mo-field-label { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; color: #d4d1cc; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
    .dv-mo-textarea { width: 100%; padding: 12px 14px; background: rgba(0,0,0,.3); border: 1px solid rgba(255,255,255,.08); border-radius: 12px; color: #f5f3f0; font-size: 13px; font-family: inherit; resize: vertical; min-height: 80px; outline: none; transition: .15s; }
    .dv-mo-textarea:focus { border-color: rgba(245,158,11,.5); background: rgba(245,158,11,.04); box-shadow: 0 0 0 4px rgba(245,158,11,.1); }
    
    .dv-mo-footer { display: flex; gap: 10px; padding: 20px 24px; border-top: 1px solid rgba(255,255,255,.05); background: rgba(0,0,0,.12); }
    .dv-mo-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 13px 18px; border-radius: 12px; font-size: 12.5px; font-weight: 800; border: 1px solid; cursor: pointer; font-family: inherit; transition: .18s; min-height: 48px; flex: 1; }
    .dv-mo-btn-ghost { flex: 0 0 auto; padding: 13px 20px; background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.1); color: #d4d1cc; }
    .dv-mo-btn-ghost:hover { background: rgba(255,255,255,.08); color: #f5f3f0; }
    
    .dv-mo-btn.approve { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border: none; color: #fff; box-shadow: 0 8px 24px -8px rgba(34,197,94,.7); }
    .dv-mo-btn.approve:hover { transform: translateY(-2px); }
    
    .dv-mo-btn.reject { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; color: #fff; box-shadow: 0 8px 24px -8px rgba(239,68,68,.7); }
    .dv-mo-btn.reject:hover { transform: translateY(-2px); }
</style>

<div id="decisionModal" class="dv-mo-backdrop">
    <div class="dv-mo">
        <!-- HEADER -->
        <div class="dv-mo-header">
            <div class="dv-mo-header-glow"></div>
            <div id="decisionIcon" class="dv-mo-icon"></div>
            <div class="dv-mo-header-body">
                <div id="decisionEyebrow" class="dv-mo-eyebrow"></div>
                <div id="decisionTitle" class="dv-mo-title"></div>
                <div id="decisionSub" class="dv-mo-sub"></div>
            </div>
            <button type="button" class="dv-mo-close" onclick="closeDecisionModal()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- BODY -->
        <div class="dv-mo-body">
            <div id="decisionAlert" class="dv-mo-alert">
                <div class="dv-mo-alert-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                </div>
                <div id="decisionAlertText" style="flex:1; font-size:13px; line-height:1.5; padding-top:2px;"></div>
            </div>

            <!-- FACTS GRID -->
            <div class="dv-mo-facts">
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Payment No.</div>
                    <div class="dv-mo-fact-value" id="decisionPayNo">-</div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Amount</div>
                    <div class="dv-mo-fact-value" id="decisionAmount" style="color:#E8A33D;">-</div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Store</div>
                    <div class="dv-mo-fact-value" id="decisionStore">-</div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Method</div>
                    <div class="dv-mo-fact-value" id="decisionMethod">-</div>
                </div>
            </div>

            <!-- REASON FIELD (Hidden on Approve) -->
            <div id="decisionReasonContainer" class="dv-mo-field" style="display:none;">
                <label class="dv-mo-field-label">
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
        <div class="dv-mo-footer">
            <button type="button" class="dv-mo-btn dv-mo-btn-ghost" onclick="closeDecisionModal()">Cancel</button>
            <button type="button" id="decisionConfirmBtn" class="dv-mo-btn" onclick="confirmDecision()">Confirm</button>
        </div>
    </div>
</div>

<script>
    let currentAction = '';

    function openDecisionModal(action) {
        currentAction = action;
        const modal = document.getElementById('decisionModal');
        
        // Kunin ang data mula sa main review modal
        const payNo = document.getElementById('modal-payment-number').textContent;
        const amount = document.getElementById('modal-amount').textContent;
        const store = document.getElementById('modal-store-name').textContent;
        const method = document.getElementById('modal-method').textContent;

        // I-populate ang mga fields
        document.getElementById('decisionPayNo').textContent = payNo;
        document.getElementById('decisionAmount').textContent = amount;
        document.getElementById('decisionStore').textContent = store;
        document.getElementById('decisionMethod').textContent = method;

        const icon = document.getElementById('decisionIcon');
        const eyebrow = document.getElementById('decisionEyebrow');
        const title = document.getElementById('decisionTitle');
        const sub = document.getElementById('decisionSub');
        const alertBox = document.getElementById('decisionAlert');
        const alertText = document.getElementById('decisionAlertText');
        const reasonBox = document.getElementById('decisionReasonContainer');
        const confirmBtn = document.getElementById('decisionConfirmBtn');

        if (action === 'approve') {
            icon.className = 'dv-mo-icon approve';
            icon.innerHTML = '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>';
            eyebrow.className = 'dv-mo-eyebrow approve';
            eyebrow.textContent = 'APPROVE ACTION';
            title.textContent = 'Confirm Payment Approval';
            sub.textContent = 'Payment will be marked as verified.';
            
            alertBox.className = 'dv-mo-alert approve';
            alertText.innerHTML = 'Are you sure you want to <strong>APPROVE</strong> this payment? This will update the store\'s balance immediately.';
            
            reasonBox.style.display = 'none';
            document.getElementById('decisionReason').value = '';
            
            confirmBtn.className = 'dv-mo-btn approve';
            confirmBtn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg> Yes, Approve';
        } else {
            icon.className = 'dv-mo-icon reject';
            icon.innerHTML = '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>';
            eyebrow.className = 'dv-mo-eyebrow reject';
            eyebrow.textContent = 'REJECT ACTION';
            title.textContent = 'Confirm Payment Rejection';
            sub.textContent = 'Customer will be notified to re-submit.';
            
            alertBox.className = 'dv-mo-alert reject';
            alertText.innerHTML = 'Are you sure you want to <strong>REJECT</strong> this payment? The customer will see the reason you provide below.';
            
            reasonBox.style.display = 'block';
            
            confirmBtn.className = 'dv-mo-btn reject';
            confirmBtn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg> Yes, Reject';
        }

        modal.classList.add('active');
    }

    function closeDecisionModal() {
        document.getElementById('decisionModal').classList.remove('active');
    }

    function confirmDecision() {
        if (currentAction === 'approve') {
            document.getElementById('modal-approve-form').submit();
        } else {
            const reason = document.getElementById('decisionReason').value.trim();
            if (!reason) {
            document.getElementById('decisionReason').style.borderColor = '#ef4444';
            document.getElementById('reasonError').style.display = 'block';
            return;
        }
            
            const rejectForm = document.getElementById('modal-reject-form');
            
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

@section('content')

@php
    use App\Models\ConsignmentPayment;
    use App\Models\Store;

    $totalPayments   = ConsignmentPayment::verified()->count();
    $totalCollected  = (float) ConsignmentPayment::verified()->sum('amount');
    $thisMonth       = (float) ConsignmentPayment::verified()->whereYear('payment_date', now()->year)->whereMonth('payment_date', now()->month)->sum('amount');
    $todayCollected  = (float) ConsignmentPayment::verified()->whereDate('payment_date', today())->sum('amount');
    $pendingCount    = ConsignmentPayment::pending()->count();
    $pendingAmount   = (float) ConsignmentPayment::pending()->sum('amount');
    $totalStores     = Store::whereHas('consignmentPayments')->count();
@endphp

@if($pendingCount > 0)
    <div class="vfy-alert">
        <div class="vfy-alert-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <div class="vfy-alert-body">
            <div class="vfy-alert-title">{{ $pendingCount }} Payment{{ $pendingCount > 1 ? 's' : '' }} Awaiting Verification</div>
            <div class="vfy-alert-desc">Customer nag-claim nakabayad. I-verify para ma-count sa sales report.</div>
        </div>
        <div class="vfy-alert-tag">₱{{ number_format($pendingAmount, 2) }}</div>
    </div>
@endif

{{-- ===== STATS GRID ===== --}}
<div class="py-stats">
    <div class="py-stat">
        <div class="py-stat-icon gold">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">Total Payments</div>
            <div class="py-stat-value">{{ number_format($totalPayments) }}</div>
            <div class="py-stat-meta">{{ $totalStores }} stores</div>
        </div>
    </div>
    <div class="py-stat">
        <div class="py-stat-icon green">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">Total Collected</div>
            <div class="py-stat-value">&#8369;{{ number_format($totalCollected/1000, 1) }}k</div>
            <div class="py-stat-meta">All time</div>
        </div>
    </div>
    <div class="py-stat">
        <div class="py-stat-icon blue">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">This Month</div>
            <div class="py-stat-value">&#8369;{{ number_format($thisMonth/1000, 1) }}k</div>
            <div class="py-stat-meta">{{ now()->format('F') }}</div>
        </div>
    </div>
    <div class="py-stat">
        <div class="py-stat-icon amber">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <div class="py-stat-body">
            <div class="py-stat-label">Today</div>
            <div class="py-stat-value">&#8369;{{ number_format($todayCollected, 0) }}</div>
            <div class="py-stat-meta">{{ now()->format('M d') }}</div>
        </div>
    </div>
</div>

{{-- ===== TOOLBAR ===== --}}
<div class="py-toolbar">
    <div class="py-toolbar-left">
        <div class="py-toolbar-heading">All Payments</div>
        <div class="py-toolbar-sub">{{ $payments->total() ?? $totalPayments }} records</div>
    </div>

    <form method="GET" class="py-toolbar-form">
        <div class="py-search">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payment #...">
        </div>
        <select name="store" class="py-filter" onchange="this.form.submit()">
            <option value="">All Stores</option>
            @foreach($stores ?? \App\Models\Store::orderBy('store_name')->get() as $s)
                <option value="{{ $s->id }}" {{ request('store') == $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="py-btn-filter">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
            Filter
        </button>
        @if(request()->hasAny(['search', 'store', 'method']))
            <a href="{{ route('consignment.payments.index') }}" class="py-btn-clear"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg></a>
        @endif
    </form>
</div>

{{-- ===== PAYMENTS LIST ===== --}}
@if($payments->isEmpty())
    <div class="py-empty">
        <div class="py-empty-icon">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        </div>
        <div class="py-empty-title">No payments found</div>
        <div class="py-empty-text">Start by recording your first payment</div>
    </div>
@else
    <div class="py-table-card">
        <div class="py-table-wrap">
            <table class="py-table">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Store</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th style="text-align: right;">Amount</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td><div class="py-number">{{ $payment->payment_number }}</div></td>
                            <td>
                                <div class="py-store">
                                    <div class="py-store-avatar">
                                        @if($payment->store && $payment->store->logo_url)
                                            <img src="{{ $payment->store->avatar_url }}" alt="">
                                        @else
                                            {{ strtoupper(substr($payment->store->store_name ?? 'ST', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="py-store-info">
                                        <div class="py-store-name">{{ $payment->store->store_name ?? '-' }}</div>
                                        <div class="py-store-code">{{ $payment->store->code ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="py-date-main">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
                                <div class="py-date-sub">{{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}</div>
                            </td>
                            <td><span class="py-method-badge method-{{ $payment->method }}">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span></td>
                            <td><div class="py-ref">{{ $payment->reference_number ?? '-' }}</div></td>
                            <td style="text-align: right;"><div class="py-amount">+ &#8369;{{ number_format($payment->amount, 2) }}</div></td>
                            
                            {{-- STATUS COLUMN --}}
                            <td style="text-align: center;">
                                @if($payment->verification_status === 'pending')
                                    <span class="vfy-badge pending"><span class="vfy-dot"></span>Pending</span>
                                @elseif($payment->verification_status === 'verified')
                                    <span class="vfy-badge verified"><span class="vfy-dot"></span>Verified</span>
                                @else
                                    <span class="vfy-badge rejected"><span class="vfy-dot"></span>Rejected</span>
                                @endif
                            </td>

                            {{-- ACTION COLUMN (ISA LANG KA BUTTON) --}}
                            <td style="text-align: right;">
                                <a href="{{ route('consignment.payments.show', $payment->id) }}" class="btn btn-primary btn-sm" style="white-space: nowrap; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
    Review
</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($payments, 'links') && $payments->hasPages())
        <div class="py-pagination">{{ $payments->withQueryString()->links() }}</div>
    @endif
@endif

{{-- ═══════════ MODAL (ISA LANG) ═══════════ --}}
    


















<!-- ULTIMATE DECISION MODAL (SHIP ACTION STYLE) -->
<style>
    .dv-mo-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(12px); display: none; align-items: center; justify-content: center; z-index: 999999; padding: 20px; }
    .dv-mo-backdrop.active { display: flex; }
    .dv-mo { background: #16161d; border: 1px solid rgba(255,255,255,.08); border-radius: 22px; width: 100%; max-width: 520px; box-shadow: 0 40px 100px -30px rgba(0,0,0,.9); display: flex; flex-direction: column; overflow: hidden; }
    
    .dv-mo-header { position: relative; display: flex; align-items: center; gap: 16px; padding: 24px; border-bottom: 1px solid rgba(255,255,255,.06); }
    .dv-mo-header-glow { position: absolute; top: -80px; right: -80px; width: 240px; height: 240px; background: radial-gradient(circle, rgba(245,158,11,.22), transparent 65%); pointer-events: none; }
    .dv-mo-icon { width: 54px; height: 54px; border-radius: 15px; display: grid; place-items: center; flex-shrink: 0; border: 1px solid; position: relative; z-index: 1; }
    .dv-mo-icon.approve { background: rgba(34,197,94,.15); border-color: rgba(34,197,94,.4); color: #22c55e; box-shadow: 0 8px 24px -8px rgba(34,197,94,.5); }
    .dv-mo-icon.reject { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.4); color: #ef4444; box-shadow: 0 8px 24px -8px rgba(239,68,68,.5); }
    
    .dv-mo-header-body { flex: 1; min-width: 0; position: relative; z-index: 1; }
    .dv-mo-eyebrow { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .15em; margin-bottom: 5px; }
    .dv-mo-eyebrow.approve { color: #4ade80; }
    .dv-mo-eyebrow.reject { color: #f87171; }
    .dv-mo-title { font-size: 18px; font-weight: 800; color: #f5f3f0; letter-spacing: -.02em; margin-bottom: 4px; }
    .dv-mo-sub { font-size: 11.5px; color: #7a7a85; font-family: ui-monospace, monospace; }
    
    .dv-mo-close { width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); color: #a8a5a0; display: grid; place-items: center; cursor: pointer; transition: .15s; position: relative; z-index: 1; }
    .dv-mo-close:hover { background: rgba(255,255,255,.08); color: #f5f3f0; transform: rotate(90deg); }
    
    .dv-mo-body { padding: 24px; overflow-y: auto; }
    
    .dv-mo-alert { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: 14px; margin-bottom: 18px; border: 1px solid; }
    .dv-mo-alert.approve { background: rgba(34,197,94,.08); border-color: rgba(34,197,94,.25); color: #bbf7d0; }
    .dv-mo-alert.reject { background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.25); color: #fecaca; }
    .dv-mo-alert-icon { flex-shrink: 0; }
    
    .dv-mo-facts { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; }
    .dv-mo-fact { padding: 12px; background: rgba(0,0,0,.22); border: 1px solid rgba(255,255,255,.05); border-radius: 12px; }
    .dv-mo-fact-label { font-size: 9px; font-weight: 800; color: #7a7a85; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
    .dv-mo-fact-value { font-size: 13px; font-weight: 800; color: #f5f3f0; font-variant-numeric: tabular-nums; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    
    .dv-mo-field { margin-top: 4px; }
    .dv-mo-field-label { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; color: #d4d1cc; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
    .dv-mo-textarea { width: 100%; padding: 12px 14px; background: rgba(0,0,0,.3); border: 1px solid rgba(255,255,255,.08); border-radius: 12px; color: #f5f3f0; font-size: 13px; font-family: inherit; resize: vertical; min-height: 80px; outline: none; transition: .15s; }
    .dv-mo-textarea:focus { border-color: rgba(245,158,11,.5); background: rgba(245,158,11,.04); box-shadow: 0 0 0 4px rgba(245,158,11,.1); }
    
    .dv-mo-footer { display: flex; gap: 10px; padding: 20px 24px; border-top: 1px solid rgba(255,255,255,.05); background: rgba(0,0,0,.12); }
    .dv-mo-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 13px 18px; border-radius: 12px; font-size: 12.5px; font-weight: 800; border: 1px solid; cursor: pointer; font-family: inherit; transition: .18s; min-height: 48px; flex: 1; }
    .dv-mo-btn-ghost { flex: 0 0 auto; padding: 13px 20px; background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.1); color: #d4d1cc; }
    .dv-mo-btn-ghost:hover { background: rgba(255,255,255,.08); color: #f5f3f0; }
    
    .dv-mo-btn.approve { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border: none; color: #fff; box-shadow: 0 8px 24px -8px rgba(34,197,94,.7); }
    .dv-mo-btn.approve:hover { transform: translateY(-2px); }
    
    .dv-mo-btn.reject { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; color: #fff; box-shadow: 0 8px 24px -8px rgba(239,68,68,.7); }
    .dv-mo-btn.reject:hover { transform: translateY(-2px); }
</style>

<div id="decisionModal" class="dv-mo-backdrop">
    <div class="dv-mo">
        <!-- HEADER -->
        <div class="dv-mo-header">
            <div class="dv-mo-header-glow"></div>
            <div id="decisionIcon" class="dv-mo-icon"></div>
            <div class="dv-mo-header-body">
                <div id="decisionEyebrow" class="dv-mo-eyebrow"></div>
                <div id="decisionTitle" class="dv-mo-title"></div>
                <div id="decisionSub" class="dv-mo-sub"></div>
            </div>
            <button type="button" class="dv-mo-close" onclick="closeDecisionModal()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- BODY -->
        <div class="dv-mo-body">
            <div id="decisionAlert" class="dv-mo-alert">
                <div class="dv-mo-alert-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                </div>
                <div id="decisionAlertText" style="flex:1; font-size:13px; line-height:1.5; padding-top:2px;"></div>
            </div>

            <!-- FACTS GRID -->
            <div class="dv-mo-facts">
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Payment No.</div>
                    <div class="dv-mo-fact-value" id="decisionPayNo">-</div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Amount</div>
                    <div class="dv-mo-fact-value" id="decisionAmount" style="color:#E8A33D;">-</div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Store</div>
                    <div class="dv-mo-fact-value" id="decisionStore">-</div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-label">Method</div>
                    <div class="dv-mo-fact-value" id="decisionMethod">-</div>
                </div>
            </div>

            <!-- REASON FIELD (Hidden on Approve) -->
            <div id="decisionReasonContainer" class="dv-mo-field" style="display:none;">
                <label class="dv-mo-field-label">
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
        <div class="dv-mo-footer">
            <button type="button" class="dv-mo-btn dv-mo-btn-ghost" onclick="closeDecisionModal()">Cancel</button>
            <button type="button" id="decisionConfirmBtn" class="dv-mo-btn" onclick="confirmDecision()">Confirm</button>
        </div>
    </div>
</div>

<script>
    let currentAction = '';

    function openDecisionModal(action) {
        currentAction = action;
        const modal = document.getElementById('decisionModal');
        
        // Kunin ang data mula sa main review modal
        const payNo = document.getElementById('modal-payment-number').textContent;
        const amount = document.getElementById('modal-amount').textContent;
        const store = document.getElementById('modal-store-name').textContent;
        const method = document.getElementById('modal-method').textContent;

        // I-populate ang mga fields
        document.getElementById('decisionPayNo').textContent = payNo;
        document.getElementById('decisionAmount').textContent = amount;
        document.getElementById('decisionStore').textContent = store;
        document.getElementById('decisionMethod').textContent = method;

        const icon = document.getElementById('decisionIcon');
        const eyebrow = document.getElementById('decisionEyebrow');
        const title = document.getElementById('decisionTitle');
        const sub = document.getElementById('decisionSub');
        const alertBox = document.getElementById('decisionAlert');
        const alertText = document.getElementById('decisionAlertText');
        const reasonBox = document.getElementById('decisionReasonContainer');
        const confirmBtn = document.getElementById('decisionConfirmBtn');

        if (action === 'approve') {
            icon.className = 'dv-mo-icon approve';
            icon.innerHTML = '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>';
            eyebrow.className = 'dv-mo-eyebrow approve';
            eyebrow.textContent = 'APPROVE ACTION';
            title.textContent = 'Confirm Payment Approval';
            sub.textContent = 'Payment will be marked as verified.';
            
            alertBox.className = 'dv-mo-alert approve';
            alertText.innerHTML = 'Are you sure you want to <strong>APPROVE</strong> this payment? This will update the store\'s balance immediately.';
            
            reasonBox.style.display = 'none';
            document.getElementById('decisionReason').value = '';
            
            confirmBtn.className = 'dv-mo-btn approve';
            confirmBtn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg> Yes, Approve';
        } else {
            icon.className = 'dv-mo-icon reject';
            icon.innerHTML = '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>';
            eyebrow.className = 'dv-mo-eyebrow reject';
            eyebrow.textContent = 'REJECT ACTION';
            title.textContent = 'Confirm Payment Rejection';
            sub.textContent = 'Customer will be notified to re-submit.';
            
            alertBox.className = 'dv-mo-alert reject';
            alertText.innerHTML = 'Are you sure you want to <strong>REJECT</strong> this payment? The customer will see the reason you provide below.';
            
            reasonBox.style.display = 'block';
            
            confirmBtn.className = 'dv-mo-btn reject';
            confirmBtn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg> Yes, Reject';
        }

        modal.classList.add('active');
    }

    function closeDecisionModal() {
        document.getElementById('decisionModal').classList.remove('active');
    }

    function confirmDecision() {
        if (currentAction === 'approve') {
            document.getElementById('modal-approve-form').submit();
        } else {
            const reason = document.getElementById('decisionReason').value.trim();
            if (!reason) {
            document.getElementById('decisionReason').style.borderColor = '#ef4444';
            document.getElementById('reasonError').style.display = 'block';
            return;
        }
            
            const rejectForm = document.getElementById('modal-reject-form');
            
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
/* Include the necessary CSS for stats, tables, badges, and modal */
.py-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.py-stat { display: flex; align-items: center; gap: 14px; padding: 18px; background: rgba(34, 34, 44, 0.35); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; }
.py-stat-icon { width: 46px; height: 46px; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0; border: 1px solid; }
.py-stat-icon.gold { background: rgba(169, 120, 74, 0.14); border-color: rgba(169, 120, 74, 0.3); color: #c9a961; }
.py-stat-icon.green { background: rgba(34, 197, 94, 0.14); border-color: rgba(34, 197, 94, 0.3); color: #22c55e; }
.py-stat-icon.blue { background: rgba(59, 130, 246, 0.14); border-color: rgba(59, 130, 246, 0.3); color: #3b82f6; }
.py-stat-icon.amber { background: rgba(245, 158, 11, 0.14); border-color: rgba(245, 158, 11, 0.3); color: #f59e0b; }
.py-stat-label { font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 5px; }
.py-stat-value { font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.03em; line-height: 1; margin-bottom: 4px; }
.py-stat-meta { font-size: 11px; color: var(--text-muted); }

.py-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
.py-toolbar-heading { font-size: 16px; font-weight: 700; color: var(--text-primary); }
.py-toolbar-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
.py-toolbar-form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; flex: 1; justify-content: flex-end; }
.py-search { position: relative; display: flex; align-items: center; flex: 1 1 200px; max-width: 280px; }
.py-search svg { position: absolute; left: 12px; color: var(--text-muted); pointer-events: none; }
.py-search input { width: 100%; padding: 9px 14px 9px 38px; background: rgba(20, 20, 26, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; color: var(--text-primary); font-size: 13px; outline: none; }
.py-filter { padding: 9px 32px 9px 12px; background: rgba(20, 20, 26, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; color: var(--text-primary); font-size: 13px; cursor: pointer; width: 150px; }
.py-btn-filter { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; background: linear-gradient(135deg, #c9a961, #8a5f36); border: none; border-radius: 10px; color: #fff; font-size: 12.5px; font-weight: 700; cursor: pointer; }
.py-btn-clear { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 10px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: #ef4444; text-decoration: none; }

.py-table-card { background: rgba(34, 34, 44, 0.35); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; overflow: hidden; }
.py-table-wrap { overflow-x: auto; }
.py-table { width: 100%; border-collapse: collapse; }
.py-table thead th { text-align: left; font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; padding: 14px 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); background: rgba(20, 20, 26, 0.4); white-space: nowrap; }
.py-table tbody td { padding: 14px 18px; font-size: 13px; color: var(--text-secondary); border-bottom: 1px solid rgba(38, 38, 46, 0.5); vertical-align: middle; }
.py-table tbody tr:hover { background: rgba(169, 120, 74, 0.04); }

.py-number { font-family: ui-monospace, monospace; font-size: 12px; font-weight: 700; color: #c9a961; }
.py-store { display: flex; align-items: center; gap: 10px; }
.py-store-avatar { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #c9a961, #8a5f36); display: grid; place-items: center; color: #fff; font-size: 11px; font-weight: 800; overflow: hidden; }
.py-store-avatar img { width: 100%; height: 100%; object-fit: cover; }
.py-store-name { font-size: 12.5px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px; }
.py-store-code { font-size: 10px; color: var(--text-muted); font-family: ui-monospace, monospace; }
.py-date-main { font-size: 12.5px; font-weight: 600; color: var(--text-primary); margin-bottom: 2px; }
.py-date-sub { font-size: 10.5px; color: var(--text-muted); }
.py-method-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 7px; font-size: 11px; font-weight: 700; text-transform: capitalize; }
.py-method-badge.method-cash { background: rgba(34, 197, 94, 0.12); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.25); }
.py-method-badge.method-gcash, .py-method-badge.method-maya { background: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.25); }
.py-method-badge.method-bank_transfer, .py-method-badge.method-check { background: rgba(169, 120, 74, 0.12); color: #c9a961; border: 1px solid rgba(169, 120, 74, 0.25); }
.py-ref { font-family: ui-monospace, monospace; font-size: 11px; color: var(--text-muted); }
.py-amount { font-size: 14px; font-weight: 800; color: #22c55e; }

.vfy-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 100px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border: 1px solid; white-space: nowrap; }
.vfy-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.vfy-badge.pending { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border-color: rgba(245, 158, 11, 0.35); }
.vfy-badge.pending .vfy-dot { background: #f59e0b; animation: vfyPulse 2s ease-in-out infinite; }
.vfy-badge.verified { background: rgba(34, 197, 94, 0.12); color: #22c55e; border-color: rgba(34, 197, 94, 0.35); }
.vfy-badge.verified .vfy-dot { background: #22c55e; }
.vfy-badge.rejected { background: rgba(239, 68, 68, 0.12); color: #ef4444; border-color: rgba(239, 68, 68, 0.35); }
.vfy-badge.rejected .vfy-dot { background: #ef4444; }
@keyframes vfyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

.vfy-alert { display: flex; align-items: center; gap: 14px; padding: 14px 18px; margin-bottom: 16px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.03)); border: 1px solid rgba(245, 158, 11, 0.3); border-left: 3px solid #f59e0b; border-radius: 14px; }
.vfy-alert-icon { width: 38px; height: 38px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; display: grid; place-items: center; flex-shrink: 0; }
.vfy-alert-body { flex: 1; }
.vfy-alert-title { font-size: 13px; font-weight: 800; color: #fafafa; margin-bottom: 2px; }
.vfy-alert-desc { font-size: 11.5px; color: #a1a1aa; }
.vfy-alert-tag { padding: 3px 9px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 100px; font-size: 11px; font-weight: 800; color: #f59e0b; font-family: ui-monospace, monospace; }
</style>
@endpush