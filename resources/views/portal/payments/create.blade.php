@extends('portal.layouts.app')

@section('title', 'Record Payment')

@section('content')

@php
    $balance = (float) ($displayBalance ?? $totalBalance ?? 0);
    $preset = (float) ($presetAmount ?? $balance);
    $isLinked = ($linkedDR ?? false) ? true : false;
@endphp

<div class="pay-header">
    <a href="{{ route('portal.payments.index') }}" class="pay-back-btn" aria-label="Back">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <div class="pay-header-info">
        <h1 class="pay-header-title">Record Payment</h1>
        <p class="pay-header-subtitle">
            @if($isLinked)Bayad para sa <span class="pay-mono">{{ $linkedDR->dr_number }}</span>
            @else Bayad sa imong consignment balance @endif
        </p>
    </div>
</div>

@if($errors->any())
    <div class="pay-alert">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
        <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    </div>
@endif

<form method="POST" action="{{ route('portal.payments.store') }}" id="paymentForm" class="pay-form">
    @csrf
    @if($isLinked)<input type="hidden" name="delivery_receipt_id" value="{{ $linkedDR->id }}">@endif

    <section class="pay-balance-card">
        <div class="pay-balance-header">
            <div class="pay-balance-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
            <div class="pay-balance-info">
                <div class="pay-balance-label">@if($isLinked)Outstanding Balance @else Total Outstanding @endif</div>
                <div class="pay-balance-amount"><span class="pay-balance-currency">₱</span><span class="pay-balance-value">{{ number_format($balance, 2) }}</span></div>
            </div>
        </div>
        @if($isLinked)
            <div class="pay-balance-meta">
                <div class="pay-balance-meta-item"><span class="pay-balance-meta-label">Total</span><span class="pay-balance-meta-value">₱{{ number_format($linkedDR->total_amount, 2) }}</span></div>
                <div class="pay-balance-meta-item"><span class="pay-balance-meta-label">Bayad Na</span><span class="pay-balance-meta-value pay-text-success">₱{{ number_format($linkedDR->amount_paid, 2) }}</span></div>
            </div>
        @endif
    </section>

    <section class="pay-section">
        <div class="pay-section-header">
            <h2 class="pay-section-title">Bayad</h2>
            <p class="pay-section-desc">Pila ang gusto nimo i-bayad</p>
        </div>
        <div class="pay-amount-container">
            <div class="pay-amount-field" id="amountContainer">
                <span class="pay-amount-currency">₱</span>
                <input type="number" inputmode="decimal" step="0.01" min="0.01" name="amount" id="amountInput" value="{{ old('amount', number_format($preset, 2, '.', '')) }}" class="pay-amount-input" placeholder="0.00" autocomplete="off" required>
                <div class="pay-amount-status" id="amountStatus">
                    <svg class="pay-status-check" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    <svg class="pay-status-alert" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
            </div>
            <div id="amountFeedback" class="pay-feedback" style="display:none;"></div>
            @if($balance > 0)
                <div class="pay-chips">
                    <button type="button" class="pay-chip pay-chip-full" data-amount="{{ number_format($balance, 2, '.', '') }}"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg><span>Full</span><span class="pay-chip-amount">₱{{ number_format($balance, 2) }}</span></button>
                    <button type="button" class="pay-chip pay-chip-half" data-amount="{{ number_format($balance / 2, 2, '.', '') }}"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20"/></svg><span>Half</span><span class="pay-chip-amount">₱{{ number_format($balance / 2, 2) }}</span></button>
                    <button type="button" class="pay-chip pay-chip-custom" data-amount=""><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg><span>Custom</span></button>
                </div>
            @endif
        </div>
    </section>

    <section class="pay-section">
        <div class="pay-section-header">
            <h2 class="pay-section-title">Payment Method</h2>
            <p class="pay-section-desc">Pilia ang paagi sa pagbayad</p>
        </div>
        <div class="pay-segmented">
            <button type="button" class="pay-segment active" data-cat="cash" role="tab"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>Cash</button>
            <button type="button" class="pay-segment" data-cat="online" role="tab"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>Online</button>
        </div>
        <div class="pay-methods" data-panel="cash">
            <label class="pay-method">
                <input type="radio" name="method" value="cash" checked>
                <div class="pay-method-inner">
                    <div class="pay-method-icon pay-icon-cash"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg></div>
                    <div class="pay-method-content"><span class="pay-method-name">Cash</span><span class="pay-method-desc">Bayad sa store o admin</span></div>
                    <div class="pay-method-check"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
                </div>
            </label>
        </div>
        <div class="pay-methods" data-panel="online" style="display:none;">
            <label class="pay-method"><input type="radio" name="method" value="gcash"><div class="pay-method-inner"><div class="pay-method-icon pay-icon-gcash"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div><div class="pay-method-content"><span class="pay-method-name">GCash</span><span class="pay-method-desc">E-wallet transfer</span></div><div class="pay-method-check"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div></div></label>
            <label class="pay-method"><input type="radio" name="method" value="maya"><div class="pay-method-inner"><div class="pay-method-icon pay-icon-maya"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div><div class="pay-method-content"><span class="pay-method-name">Maya</span><span class="pay-method-desc">E-wallet transfer</span></div><div class="pay-method-check"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div></div></label>
            <label class="pay-method"><input type="radio" name="method" value="bank_transfer"><div class="pay-method-inner"><div class="pay-method-icon pay-icon-bank"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10l9-6 9 6M5 10v10M19 10v10M9 10v10M15 10v10M2 20h20"/></svg></div><div class="pay-method-content"><span class="pay-method-name">Bank Transfer</span><span class="pay-method-desc">InstaPay / PESONet</span></div><div class="pay-method-check"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div></div></label>
        </div>
    </section>

    <section class="pay-section" id="refSection" style="display:none;">
        <div class="pay-section-header"><h2 class="pay-section-title">Reference Number</h2><p class="pay-section-desc">Ref # gikan sa transaction</p></div>
        <div class="pay-input-wrap">
            <svg class="pay-input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h16"/></svg>
            <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. 1234567890" class="pay-input" autocomplete="off">
        </div>
    </section>

    <section class="pay-section">
        <div class="pay-section-header"><h2 class="pay-section-title">Payment Date</h2><p class="pay-section-desc">Kanusa gi-bayad</p></div>
        <div class="pay-input-wrap">
            <svg class="pay-input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" class="pay-input" required>
        </div>
    </section>

    <section class="pay-section">
        <div class="pay-section-header"><h2 class="pay-section-title">Notes</h2><p class="pay-section-desc">Optional — dugang info</p></div>
        <textarea name="notes" rows="3" placeholder="Dugang detalye..." class="pay-input pay-textarea">{{ old('notes') }}</textarea>
    </section>

    <button type="submit" class="pay-submit" id="submitBtn">
        <span class="pay-submit-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></span>
        <span id="submitText">Record Payment</span>
    </button>
</form>

@endsection

@push('styles')
<style>
/* ═══ PAYMENTS CREATE — PREMIUM COFFEE ═══ */
.pay-header { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1.5px solid #EFEBE9; }
.pay-back-btn { display: grid; place-items: center; width: 40px; height: 40px; border-radius: 12px; background: #FFFFFF; border: 1.5px solid #EFEBE9; color: #8D6E63; text-decoration: none; flex-shrink: 0; transition: all 0.18s; }
.pay-back-btn:hover { background: #FDFBF7; color: #2D1A11; border-color: #D7CCC8; transform: translateX(-2px); }
.pay-back-btn:active { transform: scale(0.94); }
.pay-header-info { flex: 1; min-width: 0; }
.pay-header-title { font-size: 19px; font-weight: 800; color: #2D1A11; letter-spacing: -0.02em; line-height: 1.2; }
.pay-header-subtitle { font-size: 12.5px; color: #8D6E63; margin-top: 4px; }
.pay-mono { font-family: ui-monospace, monospace; color: #D84315; font-weight: 700; }

.pay-alert { display: flex; gap: 12px; padding: 14px 16px; margin-bottom: 20px; background: #FEE2E2; border: 1px solid #FECACA; border-radius: 12px; color: #B91C1C; font-size: 12.5px; line-height: 1.5; }
.pay-alert svg { flex-shrink: 0; margin-top: 1px; color: #DC2626; }

.pay-balance-card { position: relative; padding: 22px 20px; margin-bottom: 20px; background: linear-gradient(135deg, #2D1A11, #4E342E); border-radius: 20px; overflow: hidden; box-shadow: 0 16px 40px -12px rgba(45, 26, 17, 0.4); }
.pay-balance-card::before { content: ''; position: absolute; top: -30%; right: -10%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(216, 67, 21, 0.35), transparent 70%); pointer-events: none; }
.pay-balance-header { display: flex; align-items: center; gap: 14px; position: relative; z-index: 1; }
.pay-balance-icon { width: 44px; height: 44px; border-radius: 12px; background: rgba(216, 67, 21, 0.25); border: 1px solid rgba(216, 67, 21, 0.4); color: #FF8A65; display: grid; place-items: center; flex-shrink: 0; }
.pay-balance-info { flex: 1; min-width: 0; }
.pay-balance-label { font-size: 10.5px; font-weight: 700; color: rgba(255, 255, 255, 0.7); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
.pay-balance-amount { display: flex; align-items: baseline; gap: 3px; }
.pay-balance-currency { font-size: 20px; font-weight: 800; color: rgba(255, 255, 255, 0.85); }
.pay-balance-value { font-size: 30px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.03em; line-height: 1; font-variant-numeric: tabular-nums; }
.pay-balance-meta { display: flex; gap: 20px; margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.1); position: relative; z-index: 1; }
.pay-balance-meta-item { display: flex; flex-direction: column; gap: 3px; }
.pay-balance-meta-label { font-size: 10.5px; color: rgba(255, 255, 255, 0.7); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
.pay-balance-meta-value { font-size: 13.5px; font-weight: 800; color: #FFFFFF; font-variant-numeric: tabular-nums; }
.pay-text-success { color: #86efac !important; }

.pay-section { padding: 20px; margin-bottom: 14px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 16px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.pay-section-header { margin-bottom: 14px; }
.pay-section-title { font-size: 13.5px; font-weight: 800; color: #2D1A11; letter-spacing: -0.01em; }
.pay-section-desc { font-size: 11.5px; color: #8D6E63; margin-top: 3px; }

.pay-amount-field { display: flex; align-items: center; gap: 8px; padding: 15px 16px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; transition: all 0.2s; }
.pay-amount-field:focus-within { border-color: #2D1A11; background: #FFFFFF; box-shadow: 0 0 0 4px rgba(45, 26, 17, 0.08); }
.pay-amount-field.valid { border-color: #86efac; background: #F0FDF4; }
.pay-amount-field.invalid { border-color: #FCA5A5; background: #FEF2F2; }
.pay-amount-currency { font-size: 22px; font-weight: 800; color: #D84315; flex-shrink: 0; }
.pay-amount-input { flex: 1; min-width: 0; background: transparent; border: none; outline: none; color: #2D1A11; font-size: 22px; font-weight: 800; font-family: inherit; font-variant-numeric: tabular-nums; letter-spacing: -0.02em; padding: 0; }
.pay-amount-input::placeholder { color: #A8A29E; font-weight: 500; }
.pay-amount-input::-webkit-outer-spin-button, .pay-amount-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.pay-amount-input[type=number] { -moz-appearance: textfield; }
.pay-amount-status { display: none; flex-shrink: 0; width: 26px; height: 26px; border-radius: 50%; align-items: center; justify-content: center; }
.pay-amount-field.valid .pay-amount-status { display: flex; background: #16A34A; color: #FFFFFF; }
.pay-amount-field.invalid .pay-amount-status { display: flex; background: #DC2626; color: #FFFFFF; }
.pay-status-alert { display: none; }
.pay-amount-field.invalid .pay-status-check { display: none; }
.pay-amount-field.invalid .pay-status-alert { display: block; }

.pay-feedback { display: flex; align-items: flex-start; gap: 10px; padding: 11px 13px; margin-top: 10px; border-radius: 12px; font-size: 12px; line-height: 1.5; }
.pay-feedback.warning { background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; }
.pay-feedback.error { background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; }
.pay-feedback.success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; }
.pay-feedback strong { font-weight: 800; }

.pay-chips { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-top: 12px; }
.pay-chip { display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 11px 8px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 12px; color: #8D6E63; font-family: inherit; cursor: pointer; transition: all 0.18s; }
.pay-chip svg { color: #D7CCC8; }
.pay-chip > span:first-of-type { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
.pay-chip-amount { font-size: 11.5px; font-weight: 800; color: #2D1A11; font-variant-numeric: tabular-nums; }
.pay-chip:hover { background: #FDFBF7; border-color: #D7CCC8; color: #2D1A11; }
.pay-chip:hover svg { color: #D84315; }
.pay-chip:active { transform: scale(0.96); }
.pay-chip-full:hover { border-color: #86efac; }
.pay-chip-full:hover svg { color: #16A34A; }
.pay-chip-half:hover { border-color: #93C5FD; }
.pay-chip-half:hover svg { color: #2563EB; }
.pay-chip-custom:hover { border-color: #FFAB91; }

.pay-segmented { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; padding: 4px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; margin-bottom: 12px; }
.pay-segment { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; background: transparent; border: none; border-radius: 9px; color: #8D6E63; font-family: inherit; font-size: 12.5px; font-weight: 700; cursor: pointer; transition: all 0.18s; }
.pay-segment svg { color: #8D6E63; stroke: currentColor; }
.pay-segment.active { background: #FFFFFF; color: #2D1A11; box-shadow: 0 2px 6px rgba(45, 26, 17, 0.08); }
.pay-segment.active svg { color: #D84315; }

.pay-methods { display: flex; flex-direction: column; gap: 8px; }
.pay-method { display: block; cursor: pointer; }
.pay-method input { display: none; }
.pay-method-inner { display: flex; align-items: center; gap: 14px; padding: 14px 16px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; transition: all 0.2s; }
.pay-method:hover .pay-method-inner { background: #FFFFFF; }
.pay-method input:checked + .pay-method-inner { background: #FFFFFF; border-color: #D84315; box-shadow: 0 0 0 3px rgba(216, 67, 21, 0.1); }
.pay-method-icon { width: 42px; height: 42px; border-radius: 11px; display: grid; place-items: center; flex-shrink: 0; }
.pay-icon-cash { background: #DCFCE7; color: #15803D; }
.pay-icon-gcash { background: #DBEAFE; color: #2563EB; }
.pay-icon-maya { background: #F3E8FF; color: #7C3AED; }
.pay-icon-bank { background: #FEF3C7; color: #B45309; }
.pay-method-content { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 3px; }
.pay-method-name { font-size: 13.5px; font-weight: 800; color: #2D1A11; }
.pay-method-desc { font-size: 11.5px; color: #8D6E63; }
.pay-method-check { width: 22px; height: 22px; border-radius: 50%; background: #FFFFFF; border: 1.5px solid #D7CCC8; display: grid; place-items: center; color: transparent; flex-shrink: 0; transition: all 0.2s; }
.pay-method input:checked + .pay-method-inner .pay-method-check { background: #D84315; border-color: #D84315; color: #FFFFFF; }

.pay-input-wrap { position: relative; display: flex; align-items: center; }
.pay-input-icon { position: absolute; left: 14px; color: #A8A29E; pointer-events: none; }
.pay-input-wrap:focus-within .pay-input-icon { color: #D84315; }
.pay-input { width: 100%; padding: 13px 14px 13px 42px; background: #FDFBF7; border: 1.5px solid #EFEBE9; border-radius: 12px; color: #2D1A11; font-size: 13px; font-family: inherit; outline: none; transition: all 0.18s; }
.pay-input::placeholder { color: #A8A29E; }
.pay-input:focus { border-color: #D84315; background: #FFFFFF; box-shadow: 0 0 0 4px rgba(216, 67, 21, 0.1); }
.pay-textarea { padding: 13px 14px; resize: vertical; min-height: 76px; line-height: 1.5; }

.pay-submit { width: 100%; min-height: 54px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; margin-top: 8px; padding: 0 24px; background: linear-gradient(135deg, #D84315, #BF360C); border: none; border-radius: 14px; color: #FFFFFF; font-family: inherit; font-size: 14.5px; font-weight: 800; cursor: pointer; box-shadow: 0 10px 24px -8px rgba(216, 67, 21, 0.5); transition: all 0.2s; }
.pay-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 14px 28px -8px rgba(216, 67, 21, 0.6); }
.pay-submit:disabled { background: #D7CCC8; color: #FFFFFF; cursor: not-allowed; box-shadow: none; }
.pay-submit-icon { display: grid; place-items: center; flex-shrink: 0; }

@media (max-width: 480px) {
    .pay-header { margin-bottom: 16px; padding-bottom: 14px; }
    .pay-header-title { font-size: 17px; }
    .pay-balance-card { padding: 18px; }
    .pay-balance-value { font-size: 26px; }
    .pay-section { padding: 16px; }
    .pay-amount-input, .pay-amount-currency { font-size: 20px; }
    .pay-chips { grid-template-columns: 1fr 1fr; }
    .pay-chip-custom { grid-column: 1 / -1; }
    .pay-submit { min-height: 50px; font-size: 14px; }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const form = document.getElementById('paymentForm');
    const amountInput = document.getElementById('amountInput');
    const amountField = document.getElementById('amountContainer');
    const feedback = document.getElementById('amountFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const balance = {{ number_format($balance, 2, '.', '') }};
    let isValid = true;

    function fmt(n) { return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function validateAmount() {
        const val = parseFloat(amountInput.value) || 0;
        amountField.classList.remove('valid', 'invalid');
        feedback.style.display = 'none';
        if (val <= 0) {
            if (amountInput.value !== '') { showFeedback('error', 'Enter amount greater than zero.'); amountField.classList.add('invalid'); isValid = false; }
            else { isValid = false; }
            updateSubmit(); return;
        }
        if (val > balance + 0.01) {
            showFeedback('warning', 'Sobra ang imong gibayad ug <strong>₱' + fmt(val - balance) + '</strong>. Maximum: <strong>₱' + fmt(balance) + '</strong>.');
            amountField.classList.add('invalid'); isValid = false; updateSubmit(); return;
        }
        if (Math.abs(val - balance) < 0.01) { showFeedback('success', 'Full payment — ₱' + fmt(val) + ' ✅'); amountField.classList.add('valid'); isValid = true; updateSubmit(); return; }
        showFeedback('success', 'Partial payment — ₱' + fmt(val) + '. Remaining: <strong>₱' + fmt(balance - val) + '</strong>.');
        amountField.classList.add('valid'); isValid = true; updateSubmit();
    }

    function showFeedback(type, message) {
        feedback.className = 'pay-feedback ' + type;
        let icon = '';
        if (type === 'warning') icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>';
        else if (type === 'error') icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>';
        else if (type === 'success') icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>';
        feedback.innerHTML = icon + '<div>' + message + '</div>';
        feedback.style.display = 'flex';
    }

    function updateSubmit() { submitBtn.disabled = !isValid; submitText.textContent = isValid ? 'Record Payment' : 'Fix amount first'; }

    amountInput.addEventListener('input', validateAmount);
    amountInput.addEventListener('blur', validateAmount);

    document.querySelectorAll('.pay-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            const amt = chip.dataset.amount;
            amountInput.value = amt;
            validateAmount();
            if (amt === '') amountInput.focus(); else amountInput.blur();
        });
    });

    form.addEventListener('submit', function(e) {
        validateAmount();
        if (!isValid) { e.preventDefault(); amountInput.focus(); amountInput.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
        submitBtn.disabled = true; submitText.textContent = 'Saving...';
    });

    const segments = document.querySelectorAll('.pay-segment');
    const panels = document.querySelectorAll('.pay-methods');
    const refSection = document.getElementById('refSection');

    segments.forEach(function(seg) {
        seg.addEventListener('click', function() {
            const target = seg.dataset.cat;
            segments.forEach(s => s.classList.remove('active'));
            seg.classList.add('active');
            panels.forEach(p => { p.style.display = p.dataset.panel === target ? 'flex' : 'none'; });
            const firstRadio = document.querySelector('[data-panel="' + target + '"] input[type="radio"]');
            if (firstRadio) firstRadio.checked = true;
            updateRef();
        });
    });
    document.querySelectorAll('input[name="method"]').forEach(r => r.addEventListener('change', updateRef));

    function updateRef() {
        const selected = document.querySelector('input[name="method"]:checked');
        if (!selected) return;
        const isOnline = ['gcash', 'maya', 'bank_transfer', 'online'].includes(selected.value);
        refSection.style.display = isOnline ? 'block' : 'none';
    }
    validateAmount(); updateRef();
})();
</script>
@endpush