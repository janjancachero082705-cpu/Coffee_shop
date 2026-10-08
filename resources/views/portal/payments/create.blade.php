@extends('portal.layouts.app')

@section('title', 'Record Payment')

@section('content')

@php
    $balance = (float) ($displayBalance ?? $totalBalance ?? 0);
    $preset = (float) ($presetAmount ?? $balance);
    $isLinked = ($linkedDR ?? false) ? true : false;
@endphp

{{-- ========== PAGE HEADER ========== --}}
<div class="pay-header">
    <a href="{{ route('portal.payments.index') }}" class="pay-back-btn" aria-label="Back">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="pay-header-info">
        <h1 class="pay-header-title">Record Payment</h1>
        <p class="pay-header-subtitle">
            @if($isLinked)
                Bayad para sa <span class="pay-mono">{{ $linkedDR->dr_number }}</span>
            @else
                Bayad sa imong consignment balance
            @endif
        </p>
    </div>
</div>

@if($errors->any())
    <div class="pay-alert">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 8v4M12 16h.01"/>
        </svg>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ route('portal.payments.store') }}" id="paymentForm" class="pay-form">
    @csrf

    @if($isLinked)
        <input type="hidden" name="delivery_receipt_id" value="{{ $linkedDR->id }}">
    @endif

    {{-- ========== BALANCE OVERVIEW ========== --}}
    <section class="pay-balance-card">
        <div class="pay-balance-header">
            <div class="pay-balance-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
            <div class="pay-balance-info">
                <div class="pay-balance-label">
                    @if($isLinked)
                        OutStanding Balance
                    @else
                        Total Outstanding
                    @endif
                </div>
                <div class="pay-balance-amount">
                    <span class="pay-balance-currency">₱</span>
                    <span class="pay-balance-value" id="balanceValue">{{ number_format($balance, 2) }}</span>
                </div>
            </div>
        </div>

        @if($isLinked)
            <div class="pay-balance-meta">
                <div class="pay-balance-meta-item">
                    <span class="pay-balance-meta-label">Total</span>
                    <span class="pay-balance-meta-value">₱{{ number_format($linkedDR->total_amount, 2) }}</span>
                </div>
                <div class="pay-balance-meta-item">
                    <span class="pay-balance-meta-label">Bayad Na</span>
                    <span class="pay-balance-meta-value pay-text-success">₱{{ number_format($linkedDR->amount_paid, 2) }}</span>
                </div>
            </div>
        @endif
    </section>

    {{-- ========== AMOUNT INPUT ========== --}}
    <section class="pay-section">
        <div class="pay-section-header">
            <h2 class="pay-section-title">Bayad</h2>
            <p class="pay-section-desc">Pila ang gusto nimo i-bayad</p>
        </div>

        <div class="pay-amount-container" id="amountContainer">
            <div class="pay-amount-field">
                <span class="pay-amount-currency">₱</span>
                <input type="number"
                       inputmode="decimal"
                       step="0.01"
                       min="0.01"
                       name="amount"
                       id="amountInput"
                       value="{{ old('amount', number_format($preset, 2, '.', '')) }}"
                       class="pay-amount-input"
                       placeholder="0.00"
                       autocomplete="off"
                       required>
                <div class="pay-amount-status" id="amountStatus">
                    <svg class="pay-status-check" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12l5 5L20 7"/>
                    </svg>
                    <svg class="pay-status-alert" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
            </div>

            <div id="amountFeedback" class="pay-feedback" style="display:none;"></div>

            {{-- Quick amount chips --}}
            @if($balance > 0)
                <div class="pay-chips">
                    <button type="button" class="pay-chip pay-chip-full"
                            data-amount="{{ number_format($balance, 2, '.', '') }}">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                        <span>Full</span>
                        <span class="pay-chip-amount">₱{{ number_format($balance, 2) }}</span>
                    </button>
                    <button type="button" class="pay-chip pay-chip-half"
                            data-amount="{{ number_format($balance / 2, 2, '.', '') }}">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20"/>
                        </svg>
                        <span>Half</span>
                        <span class="pay-chip-amount">₱{{ number_format($balance / 2, 2) }}</span>
                    </button>
                    <button type="button" class="pay-chip pay-chip-custom" data-amount="">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Custom</span>
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- ========== PAYMENT METHOD ========== --}}
    <section class="pay-section">
        <div class="pay-section-header">
            <h2 class="pay-section-title">Payment Method</h2>
            <p class="pay-section-desc">Pilia ang paagi sa pagbayad</p>
        </div>

        {{-- Segmented Control --}}
        <div class="pay-segmented" role="tablist">
            <button type="button" class="pay-segment active" data-cat="cash" role="tab">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="6" width="20" height="12" rx="2"/>
                    <circle cx="12" cy="12" r="2"/>
                </svg>
                Cash
            </button>
            <button type="button" class="pay-segment" data-cat="online" role="tab">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M12 18h.01"/>
                </svg>
                Online
            </button>
        </div>

        {{-- Cash Options --}}
        <div class="pay-methods" data-panel="cash">
            <label class="pay-method">
                <input type="radio" name="method" value="cash" checked>
                <div class="pay-method-inner">
                    <div class="pay-method-icon pay-icon-cash">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="6" width="20" height="12" rx="2"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                    </div>
                    <div class="pay-method-content">
                        <span class="pay-method-name">Cash</span>
                        <span class="pay-method-desc">Bayad sa store o admin</span>
                    </div>
                    <div class="pay-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>
        </div>

        {{-- Online Options --}}
        <div class="pay-methods" data-panel="online" style="display:none;">
            <label class="pay-method">
                <input type="radio" name="method" value="gcash">
                <div class="pay-method-inner">
                    <div class="pay-method-icon pay-icon-gcash">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                        </svg>
                    </div>
                    <div class="pay-method-content">
                        <span class="pay-method-name">GCash</span>
                        <span class="pay-method-desc">E-wallet transfer</span>
                    </div>
                    <div class="pay-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>
            <label class="pay-method">
                <input type="radio" name="method" value="maya">
                <div class="pay-method-inner">
                    <div class="pay-method-icon pay-icon-maya">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                        </svg>
                    </div>
                    <div class="pay-method-content">
                        <span class="pay-method-name">Maya</span>
                        <span class="pay-method-desc">E-wallet transfer</span>
                    </div>
                    <div class="pay-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>
            <label class="pay-method">
                <input type="radio" name="method" value="bank_transfer">
                <div class="pay-method-inner">
                    <div class="pay-method-icon pay-icon-bank">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 10l9-6 9 6M5 10v10M19 10v10M9 10v10M15 10v10M2 20h20"/>
                        </svg>
                    </div>
                    <div class="pay-method-content">
                        <span class="pay-method-name">Bank Transfer</span>
                        <span class="pay-method-desc">InstaPay / PESONet</span>
                    </div>
                    <div class="pay-method-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>
        </div>
    </section>

    {{-- ========== REFERENCE NUMBER (online) ========== --}}
    <section class="pay-section" id="refSection" style="display:none;">
        <div class="pay-section-header">
            <h2 class="pay-section-title">Reference Number</h2>
            <p class="pay-section-desc">Ref # gikan sa transaction</p>
        </div>
        <div class="pay-input-wrap">
            <svg class="pay-input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 7h16M4 12h10M4 17h16"/>
            </svg>
            <input type="text"
                   name="reference_number"
                   value="{{ old('reference_number') }}"
                   placeholder="e.g. 1234567890"
                   class="pay-input"
                   autocomplete="off">
        </div>
    </section>

    {{-- ========== PAYMENT DATE ========== --}}
    <section class="pay-section">
        <div class="pay-section-header">
            <h2 class="pay-section-title">Payment Date</h2>
            <p class="pay-section-desc">Kanusa gi-bayad</p>
        </div>
        <div class="pay-input-wrap">
            <svg class="pay-input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
            <input type="date"
                   name="payment_date"
                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                   class="pay-input"
                   required>
        </div>
    </section>

    {{-- ========== NOTES ========== --}}
    <section class="pay-section">
        <div class="pay-section-header">
            <h2 class="pay-section-title">Notes</h2>
            <p class="pay-section-desc">Optional — dugang info</p>
        </div>
        <textarea name="notes"
                  rows="3"
                  placeholder="Dugang detalye..."
                  class="pay-input pay-textarea">{{ old('notes') }}</textarea>
    </section>

    {{-- ========== SUBMIT ========== --}}
    <button type="submit" class="pay-submit" id="submitBtn">
        <span class="pay-submit-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        </span>
        <span id="submitText">Record Payment</span>
    </button>
</form>

@endsection

@push('styles')
<style>
    /* ============================================================
       PROFESSIONAL PAYMENT FORM — DESIGN SYSTEM
       ============================================================ */

    /* Design tokens */
    .pay-header,
    .pay-alert,
    .pay-form {
        --pf-surface:       #17171d;
        --pf-surface-2:     #1d1d24;
        --pf-surface-3:     #24242c;
        --pf-surface-4:     #2c2c36;
        --pf-border:        rgba(255, 255, 255, 0.06);
        --pf-border-2:      rgba(255, 255, 255, 0.1);
        --pf-accent:        #c9a961;
        --pf-accent-2:      #b8944d;
        --pf-accent-glow:   rgba(201, 169, 97, 0.15);
        --pf-success:       #10b981;
        --pf-success-bg:    rgba(16, 185, 129, 0.08);
        --pf-warning:       #f59e0b;
        --pf-warning-bg:    rgba(245, 158, 11, 0.08);
        --pf-danger:        #ef4444;
        --pf-danger-bg:     rgba(239, 68, 68, 0.08);
        --pf-text-1:        #fafafa;
        --pf-text-2:        #a1a1aa;
        --pf-text-3:        #71717a;
        --pf-text-4:        #52525b;
        --pf-radius-sm:     8px;
        --pf-radius:        12px;
        --pf-radius-lg:     14px;
        --pf-radius-xl:     18px;
    }

    /* ========== HEADER ========== */
    .pay-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--pf-border);
    }
    .pay-back-btn {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border-radius: var(--pf-radius);
        background: var(--pf-surface-2);
        border: 1px solid var(--pf-border);
        color: var(--pf-text-2);
        text-decoration: none;
        flex-shrink: 0;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        -webkit-tap-highlight-color: transparent;
    }
    .pay-back-btn:hover {
        background: var(--pf-surface-3);
        color: var(--pf-text-1);
        border-color: var(--pf-border-2);
    }
    .pay-back-btn:active { transform: scale(0.94); }

    .pay-header-info { flex: 1; min-width: 0; }
    .pay-header-title {
        font-size: 19px;
        font-weight: 700;
        color: var(--pf-text-1);
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .pay-header-subtitle {
        font-size: 12.5px;
        color: var(--pf-text-3);
        margin-top: 4px;
    }
    .pay-mono {
        font-family: ui-monospace, 'SF Mono', monospace;
        color: var(--pf-accent);
        font-weight: 600;
    }

    /* ========== ALERT ========== */
    .pay-alert {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        margin-bottom: 20px;
        background: var(--pf-danger-bg);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: var(--pf-radius);
        color: #fca5a5;
        font-size: 12.5px;
        line-height: 1.5;
        animation: paySlideIn 0.2s ease;
    }
    .pay-alert svg { flex-shrink: 0; margin-top: 1px; color: var(--pf-danger); }

    /* ========== BALANCE CARD ========== */
    .pay-balance-card {
        position: relative;
        padding: 22px 20px;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--pf-surface-2) 0%, var(--pf-surface) 100%);
        border: 1px solid var(--pf-border);
        border-radius: var(--pf-radius-xl);
        overflow: hidden;
    }
    .pay-balance-card::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, var(--pf-accent-glow), transparent 70%);
        pointer-events: none;
        opacity: 0.8;
    }
    .pay-balance-header {
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        z-index: 1;
    }
    .pay-balance-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--pf-radius);
        background: rgba(201, 169, 97, 0.12);
        border: 1px solid rgba(201, 169, 97, 0.2);
        color: var(--pf-accent);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .pay-balance-info { flex: 1; min-width: 0; }
    .pay-balance-label {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--pf-text-3);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 6px;
    }
    .pay-balance-amount {
        display: flex;
        align-items: baseline;
        gap: 3px;
    }
    .pay-balance-currency {
        font-size: 20px;
        font-weight: 700;
        color: var(--pf-accent);
        opacity: 0.85;
    }
    .pay-balance-value {
        font-size: 30px;
        font-weight: 800;
        color: var(--pf-text-1);
        letter-spacing: -0.03em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    .pay-balance-meta {
        display: flex;
        gap: 20px;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid var(--pf-border);
        position: relative;
        z-index: 1;
    }
    .pay-balance-meta-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .pay-balance-meta-label {
        font-size: 10.5px;
        color: var(--pf-text-3);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .pay-balance-meta-value {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--pf-text-1);
        font-variant-numeric: tabular-nums;
    }
    .pay-text-success { color: var(--pf-success); }

    /* ========== SECTION ========== --}}
    .pay-section {
        padding: 20px;
        margin-bottom: 14px;
        background: var(--pf-surface);
        border: 1px solid var(--pf-border);
        border-radius: var(--pf-radius-lg);
        transition: border-color 0.2s ease;
    }
    .pay-section:focus-within {
        border-color: rgba(201, 169, 97, 0.25);
    }

    .pay-section-header { margin-bottom: 14px; }
    .pay-section-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--pf-text-1);
        letter-spacing: -0.01em;
    }
    .pay-section-desc {
        font-size: 11.5px;
        color: var(--pf-text-3);
        margin-top: 3px;
    }

    /* ========== AMOUNT INPUT ========== */
    .pay-amount-container { position: relative; }

    .pay-amount-field {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 15px 16px;
        background: var(--pf-surface-2);
        border: 1.5px solid var(--pf-border-2);
        border-radius: var(--pf-radius);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pay-amount-field:focus-within {
        border-color: var(--pf-accent);
        background: var(--pf-surface-3);
        box-shadow: 0 0 0 4px var(--pf-accent-glow);
    }
    .pay-amount-field.valid {
        border-color: rgba(16, 185, 129, 0.5);
        background: var(--pf-success-bg);
    }
    .pay-amount-field.valid:focus-within {
        border-color: var(--pf-success);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
    }
    .pay-amount-field.invalid {
        border-color: rgba(239, 68, 68, 0.5);
        background: var(--pf-danger-bg);
    }
    .pay-amount-field.invalid:focus-within {
        border-color: var(--pf-danger);
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
    }

    .pay-amount-currency {
        font-size: 22px;
        font-weight: 700;
        color: var(--pf-accent);
        flex-shrink: 0;
        user-select: none;
    }
    .pay-amount-input {
        flex: 1;
        min-width: 0;
        background: transparent;
        border: none;
        outline: none;
        color: var(--pf-text-1);
        font-size: 22px;
        font-weight: 700;
        font-family: inherit;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        padding: 0;
    }
    .pay-amount-input::placeholder {
        color: var(--pf-text-4);
        font-weight: 500;
    }
    .pay-amount-input::-webkit-outer-spin-button,
    .pay-amount-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .pay-amount-input[type=number] { -moz-appearance: textfield; }

    .pay-amount-status {
        display: none;
        flex-shrink: 0;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        animation: payPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .pay-amount-field.valid .pay-amount-status {
        display: flex;
        background: var(--pf-success);
        color: #fff;
    }
    .pay-amount-field.invalid .pay-amount-status {
        display: flex;
        background: var(--pf-danger);
        color: #fff;
    }
    .pay-status-alert { display: none; }
    .pay-amount-field.invalid .pay-status-check { display: none; }
    .pay-amount-field.invalid .pay-status-alert { display: block; }

    /* ========== FEEDBACK ========== */
    .pay-feedback {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 11px 13px;
        margin-top: 10px;
        border-radius: var(--pf-radius);
        font-size: 12px;
        line-height: 1.5;
        animation: paySlideIn 0.2s ease;
    }
    .pay-feedback.warning {
        background: var(--pf-warning-bg);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #fcd34d;
    }
    .pay-feedback.error {
        background: var(--pf-danger-bg);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }
    .pay-feedback.success {
        background: var(--pf-success-bg);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
    }
    .pay-feedback svg { flex-shrink: 0; margin-top: 1px; }
    .pay-feedback strong { font-weight: 700; color: inherit; }

    /* ========== AMOUNT CHIPS ========== */
    .pay-chips {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        margin-top: 12px;
    }
    .pay-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 11px 8px;
        background: var(--pf-surface-2);
        border: 1px solid var(--pf-border-2);
        border-radius: var(--pf-radius);
        color: var(--pf-text-2);
        font-family: inherit;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        -webkit-tap-highlight-color: transparent;
    }
    .pay-chip svg { color: var(--pf-text-4); transition: color 0.15s; }
    .pay-chip > span:first-of-type {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .pay-chip-amount {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--pf-text-1);
        font-variant-numeric: tabular-nums;
    }
    .pay-chip:hover {
        background: var(--pf-surface-3);
        border-color: var(--pf-border-2);
        color: var(--pf-text-1);
    }
    .pay-chip:hover svg { color: var(--pf-accent); }
    .pay-chip:active { transform: scale(0.96); }

    .pay-chip-full:hover { border-color: rgba(16, 185, 129, 0.35); }
    .pay-chip-full:hover svg { color: var(--pf-success); }
    .pay-chip-half:hover { border-color: rgba(59, 130, 246, 0.35); }
    .pay-chip-half:hover svg { color: #3b82f6; }
    .pay-chip-custom:hover { border-color: rgba(201, 169, 97, 0.35); }

    /* ========== SEGMENTED CONTROL ========== */
    .pay-segmented {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
        padding: 4px;
        background: var(--pf-surface-2);
        border: 1px solid var(--pf-border);
        border-radius: var(--pf-radius);
        margin-bottom: 12px;
    }
    .pay-segment {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 14px;
        background: transparent;
        border: none;
        border-radius: 9px;
        color: var(--pf-text-3);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        -webkit-tap-highlight-color: transparent;
    }
    .pay-segment:hover { color: var(--pf-text-2); }
    .pay-segment.active {
        background: var(--pf-surface-4);
        color: var(--pf-text-1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    .pay-segment.active svg { color: var(--pf-accent); }

    /* ========== METHOD CARDS ========== */
    .pay-methods { display: flex; flex-direction: column; gap: 8px; }
    .pay-method { display: block; cursor: pointer; }
    .pay-method input { display: none; }

    .pay-method-inner {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        background: var(--pf-surface-2);
        border: 1.5px solid var(--pf-border-2);
        border-radius: var(--pf-radius);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pay-method:hover .pay-method-inner {
        background: var(--pf-surface-3);
        border-color: var(--pf-border-2);
    }
    .pay-method input:checked + .pay-method-inner {
        background: rgba(201, 169, 97, 0.08);
        border-color: var(--pf-accent);
        box-shadow: 0 0 0 3px var(--pf-accent-glow);
    }

    .pay-method-icon {
        width: 42px;
        height: 42px;
        border-radius: var(--pf-radius-sm);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .pay-icon-cash {
        background: rgba(16, 185, 129, 0.12);
        color: var(--pf-success);
    }
    .pay-icon-gcash {
        background: rgba(59, 130, 246, 0.12);
        color: #3b82f6;
    }
    .pay-icon-maya {
        background: rgba(168, 85, 247, 0.12);
        color: #a855f7;
    }
    .pay-icon-bank {
        background: rgba(245, 158, 11, 0.12);
        color: var(--pf-warning);
    }

    .pay-method-content {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .pay-method-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--pf-text-1);
    }
    .pay-method-desc {
        font-size: 11.5px;
        color: var(--pf-text-3);
    }

    .pay-method-check {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.04);
        border: 1.5px solid var(--pf-border-2);
        display: grid;
        place-items: center;
        color: transparent;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .pay-method input:checked + .pay-method-inner .pay-method-check {
        background: var(--pf-accent);
        border-color: var(--pf-accent);
        color: #0f0f14;
    }

    /* ========== INPUT FIELDS ========== */
    .pay-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .pay-input-icon {
        position: absolute;
        left: 14px;
        color: var(--pf-text-3);
        pointer-events: none;
        transition: color 0.15s;
    }
    .pay-input-wrap:focus-within .pay-input-icon {
        color: var(--pf-accent);
    }
    .pay-input {
        width: 100%;
        padding: 13px 14px 13px 42px;
        background: var(--pf-surface-2);
        border: 1.5px solid var(--pf-border-2);
        border-radius: var(--pf-radius);
        color: var(--pf-text-1);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pay-input::placeholder { color: var(--pf-text-4); }
    .pay-input:hover { border-color: rgba(255, 255, 255, 0.15); }
    .pay-input:focus {
        border-color: var(--pf-accent);
        background: var(--pf-surface-3);
        box-shadow: 0 0 0 4px var(--pf-accent-glow);
    }
    .pay-textarea {
        padding: 13px 14px;
        resize: vertical;
        min-height: 76px;
        line-height: 1.5;
    }
    .pay-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.6);
        cursor: pointer;
    }

    /* ========== SUBMIT ========== */
    .pay-submit {
        width: 100%;
        min-height: 54px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 8px;
        padding: 0 24px;
        background: var(--pf-accent);
        border: none;
        border-radius: var(--pf-radius-lg);
        color: #0f0f14;
        font-family: inherit;
        font-size: 14.5px;
        font-weight: 800;
        letter-spacing: -0.01em;
        cursor: pointer;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.15) inset, 0 8px 24px -8px rgba(201, 169, 97, 0.5);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        -webkit-tap-highlight-color: transparent;
    }
    .pay-submit:hover:not(:disabled) {
        background: #d4b672;
        transform: translateY(-1px);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 12px 28px -8px rgba(201, 169, 97, 0.6);
    }
    .pay-submit:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1) inset, 0 4px 12px -4px rgba(201, 169, 97, 0.4);
    }
    .pay-submit:disabled {
        background: var(--pf-surface-3);
        color: var(--pf-text-4);
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }
    .pay-submit-icon {
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    /* ========== ANIMATIONS ========== */
    @keyframes paySlideIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes payPop {
        0% { opacity: 0; transform: scale(0.4); }
        100% { opacity: 1; transform: scale(1); }
    }

    /* ========== MOBILE ========== */
    @media (max-width: 480px) {
        .pay-header { margin-bottom: 16px; padding-bottom: 14px; }
        .pay-header-title { font-size: 17px; }
        .pay-balance-card { padding: 18px; }
        .pay-balance-value { font-size: 26px; }
        .pay-balance-currency { font-size: 18px; }
        .pay-balance-icon { width: 40px; height: 40px; }
        .pay-section { padding: 16px; }
        .pay-amount-field { padding: 13px 14px; }
        .pay-amount-input, .pay-amount-currency { font-size: 20px; }
        .pay-chips { grid-template-columns: 1fr 1fr; }
        .pay-chip-custom { grid-column: 1 / -1; }
        .pay-method-inner { padding: 12px 14px; }
        .pay-method-icon { width: 38px; height: 38px; }
        .pay-submit { min-height: 50px; font-size: 14px; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    const form = document.getElementById('paymentForm');
    const amountInput = document.getElementById('amountInput');
    const amountField = document.querySelector('.pay-amount-field');
    const amountStatus = document.getElementById('amountStatus');
    const feedback = document.getElementById('amountFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const balance = {{ number_format($balance, 2, '.', '') }};

    let isValid = true;

    // ========== VALIDATION ==========
    function validateAmount() {
        const val = parseFloat(amountInput.value) || 0;

        amountField.classList.remove('valid', 'invalid');
        feedback.style.display = 'none';

        if (val <= 0) {
            if (amountInput.value !== '') {
                showFeedback('error', 'Enter amount greater than zero.');
                amountField.classList.add('invalid');
                isValid = false;
            } else {
                isValid = false;
            }
            updateSubmit();
            return;
        }

        if (val > balance + 0.01) {
            const overBy = val - balance;
            showFeedback('warning',
                'Sobra ang imong gibayad ug <strong>₱' + formatMoney(overBy) + '</strong>. ' +
                'Ang maximum kay <strong>₱' + formatMoney(balance) + '</strong> ra. ' +
                'Palihug i-adjust ang amount.'
            );
            amountField.classList.add('invalid');
            isValid = false;
            updateSubmit();
            return;
        }

        if (Math.abs(val - balance) < 0.01) {
            showFeedback('success', 'Full payment — ₱' + formatMoney(val) + ' ✅');
            amountField.classList.add('valid');
            isValid = true;
            updateSubmit();
            return;
        }

        const remaining = balance - val;
        showFeedback('success',
            'Partial payment — ₱' + formatMoney(val) + '. ' +
            'Remaining balance: <strong>₱' + formatMoney(remaining) + '</strong>.'
        );
        amountField.classList.add('valid');
        isValid = true;
        updateSubmit();
    }

    function showFeedback(type, message) {
        feedback.className = 'pay-feedback ' + type;
        let icon = '';
        if (type === 'warning') {
            icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>';
        } else if (type === 'error') {
            icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>';
        } else if (type === 'success') {
            icon = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>';
        }
        feedback.innerHTML = icon + '<div>' + message + '</div>';
        feedback.style.display = 'flex';
    }

    function updateSubmit() {
        submitBtn.disabled = !isValid;
        submitText.textContent = isValid ? 'Record Payment' : 'Fix amount first';
    }

    function formatMoney(n) {
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ========== LISTENERS ==========
    amountInput.addEventListener('input', validateAmount);
    amountInput.addEventListener('blur', validateAmount);

    // Quick chips
    document.querySelectorAll('.pay-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            const amt = chip.dataset.amount;
            amountInput.value = amt;
            validateAmount();
            if (amt === '') {
                amountInput.focus();
            } else {
                amountInput.blur();
            }
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

    // ========== METHOD TOGGLE ==========
    const segments = document.querySelectorAll('.pay-segment');
    const panels = document.querySelectorAll('.pay-methods');
    const refSection = document.getElementById('refSection');

    segments.forEach(function(seg) {
        seg.addEventListener('click', function() {
            const target = seg.dataset.cat;
            segments.forEach(s => s.classList.remove('active'));
            seg.classList.add('active');
            panels.forEach(p => {
                p.style.display = p.dataset.panel === target ? 'flex' : 'none';
            });
            const firstRadio = document.querySelector('[data-panel="' + target + '"] input[type="radio"]');
            if (firstRadio) firstRadio.checked = true;
            updateRef();
        });
    });

    document.querySelectorAll('input[name="method"]').forEach(function(r) {
        r.addEventListener('change', updateRef);
    });

    function updateRef() {
        const selected = document.querySelector('input[name="method"]:checked');
        if (!selected) return;
        const isOnline = ['gcash', 'maya', 'bank_transfer', 'online'].includes(selected.value);
        refSection.style.display = isOnline ? 'block' : 'none';
    }

    // Init
    validateAmount();
    updateRef();
})();
</script>
@endpush