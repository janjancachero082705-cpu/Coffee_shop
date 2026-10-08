@extends('portal.layouts.app')

@section('title', 'Record Payment')

@section('content')

@php
    $balance = (float) ($displayBalance ?? $totalBalance ?? 0);
    $preset = (float) ($presetAmount ?? $balance);
@endphp

{{-- HEADER --}}
<div class="p-head">
    <a href="{{ route('portal.payments.index') }}" class="p-head-back">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <div class="p-head-title">Record Payment</div>
        @if($linkedDR ?? false)
            <div class="p-head-sub">{{ $linkedDR->dr_number }}</div>
        @endif
    </div>
</div>

@if($errors->any())
    <div class="p-alert">
        @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('portal.payments.store') }}" id="paymentForm">
    @csrf

    @if($linkedDR ?? false)
        <input type="hidden" name="delivery_receipt_id" value="{{ $linkedDR->id }}">
    @endif

    {{-- BALANCE --}}
    <div class="p-balance">
        <div class="p-balance-label">Balance</div>
        <div class="p-balance-value">{{ number_format($balance, 2) }}</div>
    </div>

    {{-- AMOUNT --}}
    <div class="p-field">
        <div class="p-amount-wrap" id="amountWrap">
            <span class="p-amount-peso">₱</span>
            <input type="number"
                   inputmode="decimal"
                   step="0.01"
                   min="0.01"
                   max="{{ number_format($balance, 2, '.', '') }}"
                   name="amount"
                   id="amountInput"
                   value="{{ old('amount', number_format($preset, 2, '.', '')) }}"
                   class="p-amount-input"
                   placeholder="0.00"
                   autocomplete="off"
                   required>
            <div class="p-amount-indicator" id="amountIndicator">
                <svg class="p-ind-check" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                <svg class="p-ind-alert" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8v5M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
        </div>

        <div id="amountFeedback" class="p-feedback" style="display:none;"></div>

        @if($balance > 0)
            <div class="p-quick">
                <button type="button" class="p-quick-btn" data-amount="{{ number_format($balance, 2, '.', '') }}">Full</button>
                <button type="button" class="p-quick-btn" data-amount="{{ number_format($balance / 2, 2, '.', '') }}">Half</button>
                <button type="button" class="p-quick-btn" data-amount="">Custom</button>
            </div>
        @endif
    </div>

    {{-- METHOD --}}
    <div class="p-field">
        <div class="p-toggle">
            <button type="button" class="p-toggle-btn active" data-cat="cash">Cash</button>
            <button type="button" class="p-toggle-btn" data-cat="online">Online</button>
        </div>

        <div class="p-options" data-panel="cash">
            <label class="p-option">
                <input type="radio" name="method" value="cash" checked>
                <div class="p-option-inner">
                    <span class="p-option-label">Cash</span>
                    <span class="p-option-radio"></span>
                </div>
            </label>
        </div>

        <div class="p-options" data-panel="online" style="display:none;">
            <label class="p-option">
                <input type="radio" name="method" value="gcash">
                <div class="p-option-inner">
                    <span class="p-option-label">GCash</span>
                    <span class="p-option-radio"></span>
                </div>
            </label>
            <label class="p-option">
                <input type="radio" name="method" value="maya">
                <div class="p-option-inner">
                    <span class="p-option-label">Maya</span>
                    <span class="p-option-radio"></span>
                </div>
            </label>
            <label class="p-option">
                <input type="radio" name="method" value="bank_transfer">
                <div class="p-option-inner">
                    <span class="p-option-label">Bank Transfer</span>
                    <span class="p-option-radio"></span>
                </div>
            </label>
        </div>
    </div>

    {{-- REFERENCE --}}
    <div class="p-field" id="refCard" style="display:none;">
        <input type="text" name="reference_number" value="{{ old('reference_number') }}"
               placeholder="Reference number"
               class="p-input" autocomplete="off">
    </div>

    {{-- DATE --}}
    <div class="p-field">
        <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}"
               class="p-input" required>
    </div>

    {{-- NOTES --}}
    <div class="p-field">
        <textarea name="notes" rows="2" placeholder="Notes (optional)"
                  class="p-input p-textarea">{{ old('notes') }}</textarea>
    </div>

    {{-- SUBMIT --}}
    <button type="submit" class="p-submit" id="submitBtn">
        <span id="submitText">Record Payment</span>
    </button>
</form>

@endsection

@push('styles')
<style>
    /* ================= SIMPLE CLEAN PAYMENT ================= */

    /* HEADER */
    .p-head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
    }
    .p-head-back {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: #a1a1aa;
        text-decoration: none;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .p-head-back:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }
    .p-head-back:active { transform: scale(0.95); }

    .p-head-title {
        font-size: 18px;
        font-weight: 700;
        color: #fafafa;
        letter-spacing: -0.01em;
    }
    .p-head-sub {
        font-size: 12px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        margin-top: 2px;
    }

    /* ALERT */
    .p-alert {
        padding: 12px 14px;
        margin-bottom: 16px;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 12px;
        color: #fca5a5;
        font-size: 12.5px;
        line-height: 1.5;
    }

    /* BALANCE */
    .p-balance {
        text-align: center;
        padding: 24px 20px;
        margin-bottom: 20px;
        background: rgba(15, 15, 20, 0.4);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
    }
    .p-balance-label {
        font-size: 11px;
        font-weight: 600;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 8px;
    }
    .p-balance-value {
        font-size: 38px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: -0.03em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .p-balance-value::before {
        content: '₱';
        font-size: 22px;
        font-weight: 700;
        opacity: 0.7;
        margin-right: 4px;
    }

    /* FIELD */
    .p-field {
        margin-bottom: 12px;
    }

    /* AMOUNT */
    .p-amount-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 18px;
        background: rgba(15, 15, 20, 0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        transition: all 0.2s;
    }
    .p-amount-wrap:focus-within {
        border-color: #c9a961;
        background: rgba(15, 15, 20, 0.7);
        box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.1);
    }
    .p-amount-wrap.valid { border-color: rgba(34, 197, 94, 0.5); }
    .p-amount-wrap.valid:focus-within {
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }
    .p-amount-wrap.invalid { border-color: rgba(239, 68, 68, 0.5); }
    .p-amount-wrap.invalid:focus-within {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .p-amount-peso {
        font-size: 22px;
        font-weight: 700;
        color: #c9a961;
        opacity: 0.8;
    }
    .p-amount-input {
        flex: 1;
        min-width: 0;
        background: transparent;
        border: none;
        outline: none;
        color: #fafafa;
        font-size: 22px;
        font-weight: 700;
        font-family: inherit;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        padding: 0;
    }
    .p-amount-input::placeholder { color: #52525b; font-weight: 500; }

    .p-amount-input::-webkit-outer-spin-button,
    .p-amount-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .p-amount-input[type=number] { -moz-appearance: textfield; }

    .p-amount-indicator {
        display: none;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        animation: pPop 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .p-amount-wrap.valid .p-amount-indicator {
        display: flex;
        background: #22c55e;
        color: #fff;
    }
    .p-amount-wrap.invalid .p-amount-indicator {
        display: flex;
        background: #ef4444;
        color: #fff;
    }
    .p-ind-alert { display: none; }
    .p-amount-wrap.invalid .p-ind-check { display: none; }
    .p-amount-wrap.invalid .p-ind-alert { display: block; }

    /* FEEDBACK */
    .p-feedback {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 12px;
        margin-top: 8px;
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.5;
        animation: pSlide 0.2s ease;
    }
    .p-feedback.warning {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #fcd34d;
    }
    .p-feedback.error {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }
    .p-feedback.success {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.2);
        color: #86efac;
    }
    .p-feedback svg { flex-shrink: 0; margin-top: 1px; }
    .p-feedback strong { font-weight: 700; color: inherit; }

    /* QUICK ACTIONS */
    .p-quick {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 6px;
        margin-top: 8px;
    }
    .p-quick-btn {
        padding: 10px 8px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        color: #a1a1aa;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .p-quick-btn:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #fafafa;
        border-color: rgba(255, 255, 255, 0.12);
    }
    .p-quick-btn:active { transform: scale(0.96); }

    /* TOGGLE */
    .p-toggle {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
        padding: 4px;
        background: rgba(15, 15, 20, 0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        margin-bottom: 10px;
    }
    .p-toggle-btn {
        padding: 10px 14px;
        background: transparent;
        border: none;
        border-radius: 10px;
        color: #71717a;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        -webkit-tap-highlight-color: transparent;
    }
    .p-toggle-btn:hover { color: #fafafa; }
    .p-toggle-btn.active {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
    }
    .p-toggle-btn.active::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #c9a961;
        margin-right: 6px;
        vertical-align: middle;
    }

    /* OPTION */
    .p-options { display: flex; flex-direction: column; gap: 6px; }
    .p-option { display: block; cursor: pointer; }
    .p-option input { display: none; }

    .p-option-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: rgba(15, 15, 20, 0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        transition: all 0.2s;
    }
    .p-option:hover .p-option-inner {
        background: rgba(15, 15, 20, 0.7);
        border-color: rgba(255, 255, 255, 0.12);
    }
    .p-option input:checked + .p-option-inner {
        background: rgba(201, 169, 97, 0.08);
        border-color: rgba(201, 169, 97, 0.4);
    }

    .p-option-label {
        font-size: 13.5px;
        font-weight: 600;
        color: #fafafa;
    }

    .p-option-radio {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid rgba(255, 255, 255, 0.15);
        position: relative;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .p-option input:checked + .p-option-inner .p-option-radio {
        border-color: #c9a961;
    }
    .p-option input:checked + .p-option-inner .p-option-radio::after {
        content: '';
        position: absolute;
        inset: 3px;
        border-radius: 50%;
        background: #c9a961;
    }

    /* INPUT */
    .p-input {
        width: 100%;
        padding: 14px 18px;
        background: rgba(15, 15, 20, 0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        color: #fafafa;
        font-size: 13.5px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    .p-input::placeholder { color: #52525b; }
    .p-input:hover { border-color: rgba(255, 255, 255, 0.12); }
    .p-input:focus {
        border-color: #c9a961;
        background: rgba(15, 15, 20, 0.7);
        box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.1);
    }
    .p-textarea {
        resize: vertical;
        min-height: 60px;
        line-height: 1.5;
    }
    .p-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.6);
        cursor: pointer;
    }

    /* SUBMIT */
    .p-submit {
        width: 100%;
        min-height: 54px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 16px;
        background: #c9a961;
        border: none;
        border-radius: 14px;
        color: #0f0f14;
        font-family: inherit;
        font-size: 14.5px;
        font-weight: 800;
        letter-spacing: -0.01em;
        cursor: pointer;
        box-shadow: 0 8px 24px -8px rgba(201, 169, 97, 0.5);
        transition: all 0.2s;
        -webkit-tap-highlight-color: transparent;
    }
    .p-submit:hover:not(:disabled) {
        background: #d4b672;
        transform: translateY(-1px);
        box-shadow: 0 12px 28px -8px rgba(201, 169, 97, 0.6);
    }
    .p-submit:active:not(:disabled) { transform: translateY(0); }
    .p-submit:disabled {
        background: rgba(255, 255, 255, 0.05);
        color: #52525b;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* ANIMATIONS */
    @keyframes pSlide {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pPop {
        0% { opacity: 0; transform: scale(0.4); }
        100% { opacity: 1; transform: scale(1); }
    }

    /* MOBILE */
    @media (max-width: 480px) {
        .p-head-title { font-size: 16px; }
        .p-balance { padding: 20px 16px; }
        .p-balance-value { font-size: 32px; }
        .p-amount-wrap { padding: 14px 16px; }
        .p-amount-input, .p-amount-peso { font-size: 20px; }
        .p-submit { min-height: 50px; font-size: 14px; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const form = document.getElementById('paymentForm');
    const amountInput = document.getElementById('amountInput');
    const amountWrap = document.getElementById('amountWrap');
    const feedback = document.getElementById('amountFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const balance = {{ number_format($balance, 2, '.', '') }};

    let isValid = true;

    // === VALIDATION ===
    function validateAmount() {
        const val = parseFloat(amountInput.value) || 0;

        amountWrap.classList.remove('valid', 'invalid');
        feedback.style.display = 'none';

        if (val <= 0) {
            if (amountInput.value !== '') {
                showFeedback('error', 'Enter amount greater than zero.');
                amountWrap.classList.add('invalid');
                isValid = false;
            } else {
                isValid = false;
            }
            updateSubmit();
            return;
        }

        if (val > balance) {
            const overBy = val - balance;
            showFeedback('warning',
                'Sobra ang imong gibayad ug <strong>₱' + formatMoney(overBy) + '</strong>. ' +
                'Ang maximum kay <strong>₱' + formatMoney(balance) + '</strong> ra. ' +
                'Palihug i-adjust ang amount.'
            );
            amountWrap.classList.add('invalid');
            isValid = false;
            updateSubmit();
            return;
        }

        if (Math.abs(val - balance) < 0.01) {
            showFeedback('success', 'Full payment — ₱' + formatMoney(val) + ' ✅');
            amountWrap.classList.add('valid');
            isValid = true;
            updateSubmit();
            return;
        }

        // Partial payment
        const remaining = balance - val;
        showFeedback('success',
            'Partial payment — ₱' + formatMoney(val) + '. ' +
            'Remaining balance: <strong>₱' + formatMoney(remaining) + '</strong>.'
        );
        amountWrap.classList.add('valid');
        isValid = true;
        updateSubmit();
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

    function updateSubmit() {
        submitBtn.disabled = !isValid;
        if (!isValid) {
            submitText.textContent = 'Fix amount first';
        } else {
            submitText.textContent = 'Record Payment';
        }
    }

    function formatMoney(n) {
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // === LISTENERS ===
    amountInput.addEventListener('input', validateAmount);

    // Quick buttons
    document.querySelectorAll('.pay-quick-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            amountInput.value = btn.dataset.amount;
            validateAmount();
            if (btn.dataset.amount === '') amountInput.focus();
        });
    });

    // Submit guard
    form.addEventListener('submit', function(e) {
        validateAmount();
        if (!isValid) {
            e.preventDefault();
            amountInput.focus();
            amountInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        submitBtn.disabled = true;
        submitText.textContent = 'Saving...';
    });

    // Method toggle
    const cats = document.querySelectorAll('.pm-cat');
    const panels = document.querySelectorAll('.pm-options');
    const refCard = document.getElementById('refCard');

    cats.forEach(function(cat) {
        cat.addEventListener('click', function() {
            const target = cat.dataset.cat;
            cats.forEach(c => c.classList.remove('active'));
            cat.classList.add('active');
            panels.forEach(p => { p.style.display = p.dataset.panel === target ? 'block' : 'none'; });
            const firstRadio = document.querySelector('[data-panel="' + target + '"] input[type="radio"]');
            if (firstRadio) firstRadio.checked = true;
            updateRef();
        });
    });

    document.querySelectorAll('input[name="method"]').forEach(r => r.addEventListener('change', updateRef));

    function updateRef() {
        const selected = document.querySelector('input[name="method"]:checked');
        if (!selected) return;
        const isOnline = ['gcash','maya','bank_transfer','online'].includes(selected.value);
        refCard.style.display = isOnline ? 'block' : 'none';
    }

    // Initial validation
    validateAmount();
})();
</script>
@endpush