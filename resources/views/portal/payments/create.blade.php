@extends('portal.layouts.app')

@section('title', 'Record Payment')

@section('content')

@php
    $balance = (float) ($displayBalance ?? $totalBalance ?? 0);
    $preset = (float) ($presetAmount ?? $balance);
    $isLinked = ($linkedDR ?? false) ? true : false;
@endphp

{{-- BACK --}}
<a href="{{ route('portal.payments.index') }}" class="pc-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back
</a>

@if($errors->any())
    <div class="pc-alert">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
        <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    </div>
@endif

<form method="POST" action="{{ route('portal.payments.store') }}" id="paymentForm">
    @csrf
    @if($isLinked)<input type="hidden" name="delivery_receipt_id" value="{{ $linkedDR->id }}">@endif

    {{-- BALANCE HERO --}}
    <div class="pc-hero">
        <div class="pc-hero-glow"></div>
        <div class="pc-hero-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            @if($isLinked) Outstanding Balance @else Total Outstanding @endif
        </div>
        <div class="pc-hero-amount"><span>₱</span>{{ number_format($balance, 2) }}</div>
        @if($isLinked)
            <div class="pc-hero-meta">
                <div class="pc-hero-meta-item">
                    <div class="pc-hero-meta-label">Total</div>
                    <div class="pc-hero-meta-value">₱{{ number_format($linkedDR->total_amount, 2) }}</div>
                </div>
                <div class="pc-hero-meta-item">
                    <div class="pc-hero-meta-label">Paid</div>
                    <div class="pc-hero-meta-value green">₱{{ number_format($linkedDR->amount_paid, 2) }}</div>
                </div>
            </div>
        @endif
    </div>

    {{-- AMOUNT SECTION --}}
    <div class="pc-section-label">Amount to Pay</div>
    <div class="pc-card">
        <div class="pc-amount-field" id="amountContainer">
            <span class="pc-amount-cur">₱</span>
            <input type="number" inputmode="decimal" step="0.01" min="0.01" name="amount" id="amountInput" value="{{ old('amount', number_format($preset, 2, '.', '')) }}" placeholder="0.00" autocomplete="off" required>
            <div class="pc-amount-status" id="amountStatus">
                <svg class="pc-check" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                <svg class="pc-alert-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </div>
        </div>
        <div id="amountFeedback" class="pc-feedback" style="display:none;"></div>
        @if($balance > 0)
            <div class="pc-chips">
                <button type="button" class="pc-chip" data-amount="{{ number_format($balance, 2, '.', '') }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    <span class="pc-chip-lbl">Full</span>
                    <span class="pc-chip-amt">₱{{ number_format($balance, 2) }}</span>
                </button>
                <button type="button" class="pc-chip" data-amount="{{ number_format($balance / 2, 2, '.', '') }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20"/></svg>
                    <span class="pc-chip-lbl">Half</span>
                    <span class="pc-chip-amt">₱{{ number_format($balance / 2, 2) }}</span>
                </button>
                <button type="button" class="pc-chip" data-amount="">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    <span class="pc-chip-lbl">Custom</span>
                </button>
            </div>
        @endif
    </div>

    {{-- METHOD SECTION --}}
    <div class="pc-section-label">Payment Method</div>
    <div class="pc-card">
        <div class="pc-segmented">
            <button type="button" class="pc-segment active" data-cat="cash">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                Cash
            </button>
            <button type="button" class="pc-segment" data-cat="online">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>
                Online
            </button>
        </div>

        <div class="pc-methods" data-panel="cash">
            <label class="pc-method">
                <input type="radio" name="method" value="cash" checked>
                <div class="pc-method-inner">
                    <div class="pc-method-icon" style="--c:#15803D;--bg:#E8F5E9;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                    </div>
                    <div class="pc-method-content">
                        <div class="pc-method-name">Cash</div>
                        <div class="pc-method-desc">Bayad sa store o admin</div>
                    </div>
                    <div class="pc-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    </div>
                </div>
            </label>
        </div>

        <div class="pc-methods" data-panel="online" style="display:none;">
            <label class="pc-method">
                <input type="radio" name="method" value="gcash">
                <div class="pc-method-inner">
                    <div class="pc-method-icon" style="--c:#2563EB;--bg:#EFF6FF;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div class="pc-method-content">
                        <div class="pc-method-name">GCash</div>
                        <div class="pc-method-desc">E-wallet transfer</div>
                    </div>
                    <div class="pc-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    </div>
                </div>
            </label>
            <label class="pc-method">
                <input type="radio" name="method" value="maya">
                <div class="pc-method-inner">
                    <div class="pc-method-icon" style="--c:#7C3AED;--bg:#F3E8FF;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div class="pc-method-content">
                        <div class="pc-method-name">Maya</div>
                        <div class="pc-method-desc">E-wallet transfer</div>
                    </div>
                    <div class="pc-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    </div>
                </div>
            </label>
            <label class="pc-method">
                <input type="radio" name="method" value="bank_transfer">
                <div class="pc-method-inner">
                    <div class="pc-method-icon" style="--c:#B45309;--bg:#FFF8E1;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10l9-6 9 6M5 10v10M19 10v10M9 10v10M15 10v10M2 20h20"/></svg>
                    </div>
                    <div class="pc-method-content">
                        <div class="pc-method-name">Bank Transfer</div>
                        <div class="pc-method-desc">InstaPay / PESONet</div>
                    </div>
                    <div class="pc-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    </div>
                </div>
            </label>
        </div>
    </div>

    {{-- REFERENCE --}}
    <div class="pc-section-label" id="refLabel" style="display:none;">Reference Number</div>
    <div class="pc-card" id="refSection" style="display:none;">
        <div class="pc-input-wrap">
            <svg class="pc-input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h16"/></svg>
            <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. 1234567890" autocomplete="off">
        </div>
    </div>

    {{-- DATE --}}
    <div class="pc-section-label">Payment Date</div>
    <div class="pc-card">
        <div class="pc-input-wrap">
            <svg class="pc-input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
        </div>
    </div>

    {{-- NOTES --}}
    <div class="pc-section-label">Notes <span style="text-transform:none;font-weight:600;color:#A8A29E;letter-spacing:0;">— optional</span></div>
    <div class="pc-card">
        <textarea name="notes" rows="3" placeholder="Dugang detalye..." class="pc-textarea">{{ old('notes') }}</textarea>
    </div>

    {{-- SUBMIT --}}
    <button type="submit" class="pc-submit" id="submitBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        <span id="submitText">Record Payment</span>
    </button>
</form>

@endsection

@push('styles')
<style>
/* ═══ PAYMENT CREATE — MATCHING APP DESIGN ═══ */

.pc-back{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;color:#757575;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s}
.pc-back:hover{border-color:#D0D0D0;color:#1A1A1A;transform:translateX(-2px)}

.pc-alert{display:flex;gap:10px;padding:12px 14px;margin-bottom:14px;background:#FEF2F2;border:1px solid #FECACA;border-left:3px solid #DC2626;border-radius:12px;color:#B91C1C;font-size:12.5px;font-weight:600;line-height:1.5}
.pc-alert svg{flex-shrink:0;margin-top:1px;color:#DC2626}

/* HERO */
.pc-hero{position:relative;padding:22px 20px;margin-bottom:20px;background:linear-gradient(135deg,#1A1A1A 0%,#2D2D2D 55%,#3D3D3D 100%);border-radius:18px;overflow:hidden;animation:fadeIn .35s ease both;box-shadow:0 12px 32px -12px rgba(0,0,0,.3)}
.pc-hero-glow{position:absolute;top:-80px;right:-80px;width:240px;height:240px;background:radial-gradient(circle,rgba(232,163,61,.4),transparent 70%);pointer-events:none;animation:pcGlow 5s ease-in-out infinite}
@keyframes pcGlow{0%,100%{opacity:.7;transform:scale(1);}50%{opacity:1;transform:scale(1.15);}}
.pc-hero-label{position:relative;z-index:1;display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.12em;margin-bottom:10px}
.pc-hero-label svg{color:#E8A33D}
.pc-hero-amount{position:relative;z-index:1;font-size:38px;font-weight:800;color:#FFFFFF;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums;margin-bottom:14px}
.pc-hero-amount span{font-size:22px;margin-right:2px;opacity:.85}
.pc-hero-meta{position:relative;z-index:1;display:grid;grid-template-columns:1fr 1fr;gap:12px;padding-top:14px;border-top:1px solid rgba(255,255,255,.1)}
.pc-hero-meta-item{display:flex;flex-direction:column;gap:3px}
.pc-hero-meta-label{font-size:9.5px;font-weight:700;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.08em}
.pc-hero-meta-value{font-size:14px;font-weight:800;color:#FFFFFF;font-variant-numeric:tabular-nums;letter-spacing:-.01em}
.pc-hero-meta-value.green{color:#86efac}

/* SECTION LABEL */
.pc-section-label{font-size:11px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.1em;margin:0 4px 8px;display:flex;align-items:center;gap:6px}
.pc-section-label:not(:first-of-type){margin-top:16px}

/* CARD */
.pc-card{padding:16px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;margin-bottom:12px;animation:fadeIn .35s ease both}

/* AMOUNT FIELD */
.pc-amount-field{display:flex;align-items:center;gap:8px;padding:16px;background:#FAFAFA;border:1.5px solid #EEEEEE;border-radius:12px;transition:all .2s}
.pc-amount-field:focus-within{border-color:#E8A33D;background:#FFFFFF;box-shadow:0 0 0 4px rgba(232,163,61,.15)}
.pc-amount-field.valid{border-color:#86efac;background:#F0FDF4}
.pc-amount-field.invalid{border-color:#FCA5A5;background:#FEF2F2}
.pc-amount-cur{font-size:24px;font-weight:800;color:#D4901F;flex-shrink:0}
.pc-amount-field input{flex:1;min-width:0;background:transparent;border:none;outline:none;color:#1A1A1A;font-size:24px;font-weight:800;font-family:inherit;font-variant-numeric:tabular-nums;letter-spacing:-.02em;padding:0}
.pc-amount-field input::placeholder{color:#A8A29E;font-weight:600}
.pc-amount-field input::-webkit-outer-spin-button,
.pc-amount-field input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
.pc-amount-field input[type=number]{-moz-appearance:textfield}
.pc-amount-status{display:none;flex-shrink:0;width:26px;height:26px;border-radius:50%;align-items:center;justify-content:center}
.pc-amount-field.valid .pc-amount-status{display:flex;background:#16A34A;color:#FFFFFF}
.pc-amount-field.invalid .pc-amount-status{display:flex;background:#DC2626;color:#FFFFFF}
.pc-alert-icon{display:none}
.pc-amount-field.invalid .pc-check{display:none}
.pc-amount-field.invalid .pc-alert-icon{display:block}

/* FEEDBACK */
.pc-feedback{display:flex;align-items:flex-start;gap:8px;padding:11px 13px;margin-top:10px;border-radius:10px;font-size:11.5px;line-height:1.5;font-weight:500}
.pc-feedback.warning{background:#FFF8E1;border:1px solid #FDE68A;color:#92400E}
.pc-feedback.error{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C}
.pc-feedback.success{background:#F0FDF4;border:1px solid #BBF7D0;color:#15803D}
.pc-feedback strong{font-weight:800}

/* CHIPS */
.pc-chips{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:12px}
.pc-chip{display:flex;flex-direction:column;align-items:center;gap:4px;padding:11px 8px;background:#FFFFFF;border:1.5px solid #EEEEEE;border-radius:12px;color:#757575;font-family:inherit;cursor:pointer;transition:all .18s}
.pc-chip svg{color:#B8A29A;transition:color .15s}
.pc-chip-lbl{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em}
.pc-chip-amt{font-size:11.5px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums}
.pc-chip:hover{background:#FFF8EC;border-color:#E8A33D}
.pc-chip:hover svg{color:#D4901F}
.pc-chip:active{transform:scale(.96)}

/* SEGMENTED */
.pc-segmented{display:grid;grid-template-columns:1fr 1fr;gap:4px;padding:4px;background:#FAFAFA;border:1px solid #EEEEEE;border-radius:12px;margin-bottom:12px}
.pc-segment{display:flex;align-items:center;justify-content:center;gap:6px;padding:10px 14px;background:transparent;border:none;border-radius:9px;color:#757575;font-family:inherit;font-size:12.5px;font-weight:700;cursor:pointer;transition:all .18s}
.pc-segment.active{background:#1A1A1A;color:#FFFFFF}
.pc-segment.active svg{color:#E8A33D}

/* METHODS */
.pc-methods{display:flex;flex-direction:column;gap:8px}
.pc-method{display:block;cursor:pointer}
.pc-method input{display:none}
.pc-method-inner{display:flex;align-items:center;gap:12px;padding:12px 14px;background:#FAFAFA;border:1.5px solid #EEEEEE;border-radius:12px;transition:all .2s}
.pc-method:hover .pc-method-inner{background:#FFFFFF;border-color:#D0D0D0}
.pc-method input:checked + .pc-method-inner{background:#FFF8EC;border-color:#E8A33D;box-shadow:0 0 0 3px rgba(232,163,61,.12)}
.pc-method-icon{width:40px;height:40px;border-radius:10px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0}
.pc-method-content{flex:1;min-width:0}
.pc-method-name{font-size:13px;font-weight:800;color:#1A1A1A;letter-spacing:-.01em;margin-bottom:2px}
.pc-method-desc{font-size:11px;color:#757575;font-weight:500}
.pc-method-check{width:22px;height:22px;border-radius:50%;background:#FFFFFF;border:1.5px solid #D0D0D0;display:grid;place-items:center;color:transparent;flex-shrink:0;transition:all .2s}
.pc-method input:checked + .pc-method-inner .pc-method-check{background:#E8A33D;border-color:#E8A33D;color:#FFFFFF}

/* INPUT */
.pc-input-wrap{position:relative;display:flex;align-items:center}
.pc-input-icon{position:absolute;left:14px;color:#B8A29A;pointer-events:none;transition:color .18s}
.pc-input-wrap:focus-within .pc-input-icon{color:#E8A33D}
.pc-input-wrap input{width:100%;padding:13px 14px 13px 42px;background:#FAFAFA;border:1.5px solid #EEEEEE;border-radius:12px;color:#1A1A1A;font-size:13px;font-family:inherit;font-weight:500;outline:none;transition:all .18s}
.pc-input-wrap input::placeholder{color:#A8A29E}
.pc-input-wrap input:focus{border-color:#E8A33D;background:#FFFFFF;box-shadow:0 0 0 4px rgba(232,163,61,.12)}

.pc-textarea{width:100%;padding:12px 14px;background:#FAFAFA;border:1.5px solid #EEEEEE;border-radius:12px;color:#1A1A1A;font-size:13px;font-family:inherit;font-weight:500;outline:none;resize:vertical;min-height:76px;line-height:1.5;transition:all .18s}
.pc-textarea::placeholder{color:#A8A29E}
.pc-textarea:focus{border-color:#E8A33D;background:#FFFFFF;box-shadow:0 0 0 4px rgba(232,163,61,.12)}

/* SUBMIT */
.pc-submit{width:100%;min-height:54px;display:inline-flex;align-items:center;justify-content:center;gap:10px;margin-top:8px;padding:0 24px;background:#E8A33D;border:none;border-radius:100px;color:#FFFFFF;font-family:inherit;font-size:14.5px;font-weight:800;cursor:pointer;box-shadow:0 10px 24px -8px rgba(232,163,61,.5);transition:all .2s}
.pc-submit:hover:not(:disabled){background:#D4901F;transform:translateY(-1px);box-shadow:0 14px 28px -8px rgba(232,163,61,.6)}
.pc-submit:active:not(:disabled){transform:scale(.98)}
.pc-submit:disabled{background:#D0D0D0;cursor:not-allowed;box-shadow:none}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.pc-hero{padding:18px}
.pc-hero-amount{font-size:32px}
.pc-hero-amount span{font-size:19px}
.pc-card{padding:14px}
.pc-amount-cur,.pc-amount-field input{font-size:22px}
.pc-chips{grid-template-columns:1fr 1fr}
.pc-chip:last-child{grid-column:1 / -1}
.pc-submit{min-height:50px;font-size:14px}
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
        feedback.className = 'pc-feedback ' + type;
        let icon = '';
        if (type === 'warning') icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>';
        else if (type === 'error') icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>';
        else if (type === 'success') icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>';
        feedback.innerHTML = icon + '<div>' + message + '</div>';
        feedback.style.display = 'flex';
    }

    function updateSubmit() {
        submitBtn.disabled = !isValid;
        submitText.textContent = isValid ? 'Record Payment' : 'Fix amount first';
    }

    amountInput.addEventListener('input', validateAmount);
    amountInput.addEventListener('blur', validateAmount);

    document.querySelectorAll('.pc-chip').forEach(function(chip) {
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
        submitBtn.disabled = true; submitBtn.style.pointerEvents = 'none'; submitBtn.style.opacity = '0.6';
        submitText.textContent = 'Processing...';
    });

    const segments = document.querySelectorAll('.pc-segment');
    const panels = document.querySelectorAll('.pc-methods');
    const refSection = document.getElementById('refSection');
    const refLabel = document.getElementById('refLabel');

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
        refLabel.style.display = isOnline ? 'flex' : 'none';
    }
    validateAmount(); updateRef();
})();
</script>
@endpush

