@extends('layouts.admin')

@section('title', 'Record Payment')
@section('subtitle', 'Record a consignment payment from a store')

@section('actions')
    <a href="{{ route('consignment.payments.index') }}" class="btn btn-ghost btn-sm">Back</a>
@endsection

@section('content')

<form method="POST" action="{{ route('consignment.payments.store') }}" style="max-width: 1100px;" id="paymentForm">
    @csrf

    <div class="form-layout">
        <div class="form-main">

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Payment Information</div>
                        <div class="card-sub">Store and payment details</div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label class="label">Store <span class="req">*</span></label>
                        <select name="store_id" id="storeSelect" class="input" required onchange="reloadStore()">
                            <option value="">-- Select a store --</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ (old('store_id', $selectedStore)==$store->id)?'selected':'' }}>
                                    {{ $store->code }} - {{ $store->store_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('store_id')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label class="label">Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="input" required>
                        @error('payment_date')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                @if($selectedStore)
                    <div class="store-Unpaid Balance">
                        <div class="store-Unpaid Balance-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="store-Unpaid Balance-info">
                            <div class="store-Unpaid Balance-label">Total Unpaid Balance</div>
                            <div class="store-Unpaid Balance-value">&#8369;{{ number_format($storeBalance, 2) }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Amount & Method</div>
                        <div class="card-sub">How the payment was made</div>
                    </div>
                </div>

                <div class="form-grid form-grid-3">
                    <div class="field">
                        <label class="label">Amount <span class="req">*</span></label>
                        <div class="input-prefix">
                            <span class="prefix">&#8369;</span>
                            <input type="number" step="0.01" name="amount" id="amountInput"
                                   value="{{ old('amount') }}" class="input input-with-prefix"
                                   min="0.01" required oninput="updatePreview()">
                        </div>
                        @error('amount')<div class="field-error">{{ $message }}</div>@enderror
                        <div class="quick-amounts">
                            <button type="button" class="quick-amt" onclick="setAmount(1000)">&#8369;1k</button>
                            <button type="button" class="quick-amt" onclick="setAmount(2000)">&#8369;2k</button>
                            <button type="button" class="quick-amt" onclick="setAmount(5000)">&#8369;5k</button>
                            <button type="button" class="quick-amt" onclick="setFullAmount()">Full</button>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Payment Method <span class="req">*</span></label>
                        <select name="method" class="input" required>
                            <option value="cash"          {{ old('method')==='cash'?'selected':'' }}>Cash</option>
                            <option value="gcash"         {{ old('method')==='gcash'?'selected':'' }}>GCash</option>
                            <option value="maya"          {{ old('method')==='maya'?'selected':'' }}>Maya</option>
                            <option value="bank_transfer" {{ old('method')==='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                            <option value="check"         {{ old('method')==='check'?'selected':'' }}>Check</option>
                        </select>
                    </div>

                    <div class="field">
                        <label class="label">Reference Number</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="input" placeholder="Optional">
                    </div>
                </div>

                <div class="field" style="margin-top:16px;">
                    <label class="label">Notes</label>
                    <textarea name="notes" rows="2" class="input" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div id="overpayWarning" class="overpay-warning" style="display:none;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>
                <div>
                    <strong>Cannot save payment</strong>
                    <span id="overpayMessage">Payment exceeds the balance.</span>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('consignment.payments.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                    Record Payment
                </button>
            </div>
        </div>

        <div class="preview-panel">
            <div class="preview-card">
                <div class="preview-header">
                    <div class="preview-label">Payment Summary</div>
                    <span class="live-badge">
                        <span class="live-dot"></span>
                        Live
                    </span>
                </div>

                <div class="preview-rows">
                    <div class="preview-row">
                        <span class="preview-row-label">Store Unpaid Balance</span>
                        <span class="preview-row-value accent" id="previewBalance">&#8369;0.00</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-row-label">Payment Amount</span>
                        <span class="preview-row-value green" id="previewPayment">- &#8369;0.00</span>
                    </div>
                </div>

                <div class="preview-divider"></div>

                <div class="preview-final" id="previewFinal">
                    <div class="preview-final-label">Remaining Balance</div>
                    <div class="preview-final-value" id="previewRemaining">&#8369;0.00</div>
                    <div class="preview-final-status" id="previewStatus"></div>
                </div>

                <div class="progress-wrap" id="progressWrap">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
                    </div>
                    <div class="progress-label">
                        <span id="progressPercent">0%</span> paid
                    </div>
                </div>
            </div>

            <div class="preview-note" id="previewNote">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                <span>Select a store to see the Unpaid Balance.</span>
            </div>
        </div>
    </div>
</form>

@endsection

@push('styles')
<style>
    .form-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }
    .form-main { min-width: 0; }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .field { display: flex; flex-direction: column; }
    .req { color: #ef4444; font-weight: 700; }
    .field-error { font-size: 11px; color: #ef4444; margin-top: 5px; font-weight: 500; }

    .input-prefix { position: relative; display: flex; align-items: center; }
    .prefix {
        position: absolute; left: 12px;
        color: var(--text-muted); font-size: 13px;
        font-weight: 600; pointer-events: none;
    }
    .input-with-prefix { padding-left: 28px; }

    .quick-amounts {
        display: flex;
        gap: 6px;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    .quick-amt {
        padding: 4px 10px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        border-radius: 6px;
        color: #c9a961;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .quick-amt:hover {
        background: rgba(169, 120, 74, 0.2);
        transform: translateY(-1px);
    }

    .store-Unpaid Balance {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 10px;
        margin-top: 16px;
    }
    .store-Unpaid Balance-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #f59e0b;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .store-Unpaid Balance-info { flex: 1; }
    .store-Unpaid Balance-label {
        font-size: 10px;
        color: #f59e0b;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
        margin-bottom: 3px;
    }
    .store-Unpaid Balance-value {
        font-size: 20px;
        font-weight: 800;
        color: #f59e0b;
        letter-spacing: -0.02em;
    }

    .overpay-warning {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 10px;
        color: #ef4444;
        font-size: 13px;
        margin-bottom: 12px;
    }
    .overpay-warning svg { flex-shrink: 0; margin-top: 1px; }
    .overpay-warning strong { display: block; font-size: 13px; font-weight: 700; margin-bottom: 3px; }
    .overpay-warning span { font-size: 12px; color: rgba(239, 68, 68, 0.9); line-height: 1.4; }

    .preview-panel {
        position: sticky;
        top: 90px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .preview-card {
        background: linear-gradient(160deg, rgba(34, 34, 44, 0.85), rgba(20, 20, 26, 0.9));
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 16px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .preview-card::before {
        content: '';
        position: absolute;
        top: -50%; right: -20%;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(201, 169, 97, 0.12), transparent 70%);
        pointer-events: none;
    }
    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        position: relative; z-index: 1;
    }
    .preview-label {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }
    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.25);
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        color: #22c55e;
    }
    .live-dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #22c55e;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6); }
        50% { opacity: 0.6; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0); }
    }

    .preview-rows {
        display: flex;
        flex-direction: column;
        gap: 10px;
        position: relative; z-index: 1;
    }
    .preview-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }
    .preview-row-label { color: var(--text-secondary); }
    .preview-row-value {
        font-weight: 700;
        color: var(--text-primary);
        font-family: ui-monospace, monospace;
        font-size: 13px;
    }
    .preview-row-value.accent { color: #c9a961; }
    .preview-row-value.green { color: #22c55e; }

    .preview-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(169, 120, 74, 0.3), transparent);
        margin: 16px 0;
        position: relative; z-index: 1;
    }

    .preview-final {
        text-align: center;
        padding: 12px 0;
        position: relative; z-index: 1;
    }
    .preview-final-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .preview-final-value {
        font-size: 32px;
        font-weight: 800;
        color: #c9a961;
        letter-spacing: -0.03em;
        line-height: 1;
        transition: all 0.3s ease;
    }
    .preview-final-value.zero {
        color: #22c55e;
        text-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
    }
    .preview-final-status {
        font-size: 11px;
        font-weight: 700;
        margin-top: 8px;
        min-height: 16px;
    }
    .preview-final-status.paid    { color: #22c55e; }
    .preview-final-status.partial { color: #f59e0b; }
    .preview-final-status.none    { color: var(--text-muted); }

    .progress-wrap {
        margin-top: 16px;
        position: relative; z-index: 1;
    }
    .progress-bar {
        height: 6px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 3px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #4ade80);
        border-radius: 3px;
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 6px;
        font-weight: 600;
    }

    .preview-note {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 12px;
        background: rgba(59, 130, 246, 0.06);
        border: 1px solid rgba(59, 130, 246, 0.15);
        border-radius: 10px;
        font-size: 11px;
        color: #60a5fa;
        line-height: 1.4;
    }
    .preview-note svg { flex-shrink: 0; margin-top: 1px; }
    .preview-note.success {
        background: rgba(34, 197, 94, 0.06);
        border-color: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }
    .preview-note.warning {
        background: rgba(245, 158, 11, 0.06);
        border-color: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 8px;
    }

    @media (max-width: 1000px) {
        .form-layout { grid-template-columns: 1fr; }
        .preview-panel { position: static; }
    }
    @media (max-width: 700px) {
        .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
    const STORE_BALANCE = {{ (float) ($storeBalance ?? 0) }};

    function reloadStore() {
        const storeId = document.getElementById('storeSelect').value;
        if (storeId) {
            const url = new URL(window.location.href);
            url.searchParams.set('store_id', storeId);
            window.location.href = url.toString();
        }
    }

    function setAmount(val) {
        document.getElementById('amountInput').value = val.toFixed(2);
        updatePreview();
    }

    function setFullAmount() {
        if (STORE_BALANCE > 0) setAmount(STORE_BALANCE);
    }

    function fmt(n) {
        return '\u20B1' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updatePreview() {
        const amountInput = document.getElementById('amountInput');
        const balance = STORE_BALANCE;
        const payment = parseFloat(amountInput.value) || 0;
        const remaining = Math.max(0, balance - payment);
        const overpay = payment > balance && balance > 0;

        document.getElementById('previewBalance').textContent = fmt(balance);
        document.getElementById('previewPayment').textContent = '- ' + fmt(payment);

        const finalEl = document.getElementById('previewRemaining');
        const statusEl = document.getElementById('previewStatus');

        finalEl.textContent = fmt(remaining);
        finalEl.classList.remove('zero');

        if (balance === 0) {
            statusEl.textContent = 'No Unpaid Balance';
            statusEl.className = 'preview-final-status none';
        } else if (remaining <= 0) {
            finalEl.classList.add('zero');
            statusEl.textContent = 'Fully paid';
            statusEl.className = 'preview-final-status paid';
        } else if (payment > 0) {
            statusEl.textContent = 'Partial payment';
            statusEl.className = 'preview-final-status partial';
        } else {
            statusEl.textContent = '';
            statusEl.className = 'preview-final-status';
        }

        const pct = balance > 0 ? Math.min(100, (payment / balance) * 100) : 0;
        document.getElementById('progressFill').style.width = pct + '%';
        document.getElementById('progressPercent').textContent = pct.toFixed(0) + '%';

        // Overpay check
        const submitBtn = document.getElementById('submitBtn');
        const warningEl = document.getElementById('overpayWarning');
        const warningMsg = document.getElementById('overpayMessage');

        if (overpay) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.4';
            submitBtn.style.cursor = 'not-allowed';
            submitBtn.style.background = 'rgba(239, 68, 68, 0.3)';
            warningEl.style.display = 'flex';
            warningMsg.textContent = 'Payment amount exceeds the Unpaid Balance of ' + fmt(balance) + '.';
        } else {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            submitBtn.style.background = '';
            warningEl.style.display = 'none';
        }

        const noteEl = document.getElementById('previewNote');
        if (balance === 0) {
            noteEl.className = 'preview-note';
            noteEl.querySelector('span').textContent = 'Select a store to see the Unpaid Balance.';
        } else if (overpay) {
            noteEl.className = 'preview-note warning';
            noteEl.querySelector('span').textContent = 'Payment exceeds the store total balance.';
        } else if (remaining <= 0) {
            noteEl.className = 'preview-note success';
            noteEl.querySelector('span').textContent = 'This will fully settle the store balance.';
        } else if (payment > 0) {
            noteEl.className = 'preview-note';
            noteEl.querySelector('span').textContent = 'Payment will be auto-applied to oldest unpaid reports first.';
        } else {
            noteEl.className = 'preview-note';
            noteEl.querySelector('span').textContent = 'Payment will be auto-applied to oldest unpaid reports first.';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updatePreview();
    });
</script>
@endpush