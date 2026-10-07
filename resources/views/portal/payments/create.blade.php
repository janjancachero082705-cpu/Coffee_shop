@extends('portal.layouts.app')

@section('title', 'Record Payment')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.payments.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="page-title">Record Payment</div>
            <div class="page-sub">Bayad sa imong consignment balance</div>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="pay-error">
        @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('portal.payments.store') }}" id="paymentForm">
    @csrf

    {{-- AMOUNT --}}
    <div class="pay-card">
        <div class="pay-card-head">
            <div class="pay-card-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
            <div>
                <div class="pay-card-title">Amount</div>
                <div class="pay-card-sub">Total balance: &#8369;{{ number_format($totalBalance, 2) }}</div>
            </div>
        </div>

        <div class="pay-amount-wrap">
            <span class="pay-amount-peso">&#8369;</span>
            <input type="number" step="0.01" min="1" name="amount"
                   id="amountInput"
                   value="{{ old('amount', number_format($totalBalance, 2, '.', '')) }}"
                   class="pay-amount-input" placeholder="0.00" required>
        </div>

        @if($totalBalance > 0)
            <button type="button" class="pay-quick-btn" onclick="document.getElementById('amountInput').value='{{ number_format($totalBalance, 2, '.', '') }}'">
                Pay full balance
            </button>
        @endif
    </div>

    {{-- PAYMENT METHOD --}}
    <div class="pay-card">
        <div class="pay-card-head">
            <div class="pay-card-icon green">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="4" width="22" height="16" rx="2"/>
                    <path d="M1 10h22"/>
                </svg>
            </div>
            <div>
                <div class="pay-card-title">Payment Method</div>
                <div class="pay-card-sub">Pilia kung cash o online</div>
            </div>
        </div>

        {{-- CATEGORY TOGGLE --}}
        <div class="pm-categories">
            <button type="button" class="pm-cat active" data-cat="cash">
                <div class="pm-cat-icon">💵</div>
                <div class="pm-cat-text">
                    <div class="pm-cat-label">Cash</div>
                    <div class="pm-cat-sub">Walk-in / in-store</div>
                </div>
            </button>
            <button type="button" class="pm-cat" data-cat="online">
                <div class="pm-cat-icon">📱</div>
                <div class="pm-cat-text">
                    <div class="pm-cat-label">Online</div>
                    <div class="pm-cat-sub">GCash, Maya, Bank</div>
                </div>
            </button>
        </div>

        {{-- CASH OPTION --}}
        <div class="pm-options" data-panel="cash" style="display:block;">
            <label class="pm-option selected">
                <input type="radio" name="method" value="cash" checked>
                <div class="pm-option-card">
                    <div class="pm-option-icon">💵</div>
                    <div>
                        <div class="pm-option-label">Cash</div>
                        <div class="pm-option-sub">Bayad sa store o sa admin</div>
                    </div>
                    <div class="pm-option-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>
        </div>

        {{-- ONLINE OPTIONS --}}
        <div class="pm-options" data-panel="online" style="display:none;">
            <label class="pm-option">
                <input type="radio" name="method" value="gcash">
                <div class="pm-option-card">
                    <div class="pm-option-icon" style="background:rgba(59,130,246,0.15);color:#3b82f6;">📱</div>
                    <div>
                        <div class="pm-option-label">GCash</div>
                        <div class="pm-option-sub">E-wallet transfer</div>
                    </div>
                    <div class="pm-option-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>

            <label class="pm-option">
                <input type="radio" name="method" value="maya">
                <div class="pm-option-card">
                    <div class="pm-option-icon" style="background:rgba(168,85,247,0.15);color:#a855f7;">💜</div>
                    <div>
                        <div class="pm-option-label">Maya</div>
                        <div class="pm-option-sub">E-wallet transfer</div>
                    </div>
                    <div class="pm-option-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>

            <label class="pm-option">
                <input type="radio" name="method" value="bank_transfer">
                <div class="pm-option-card">
                    <div class="pm-option-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;">🏦</div>
                    <div>
                        <div class="pm-option-label">Bank Transfer</div>
                        <div class="pm-option-sub">InstaPay / PESONet</div>
                    </div>
                    <div class="pm-option-check">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M5 12l5 5L20 7"/>
                        </svg>
                    </div>
                </div>
            </label>
        </div>
    </div>

    {{-- REFERENCE NUMBER (online only) --}}
    <div class="pay-card" id="refCard" style="display:none;">
        <div class="pay-card-head">
            <div class="pay-card-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 7h16M4 12h10M4 17h16"/>
                </svg>
            </div>
            <div>
                <div class="pay-card-title">Reference Number</div>
                <div class="pay-card-sub">Ref # gikan sa imong transaction</div>
            </div>
        </div>
        <input type="text" name="reference_number" value="{{ old('reference_number') }}"
               placeholder="e.g. 1234567890"
               class="pay-input">
    </div>

    {{-- DATE + NOTES --}}
    <div class="pay-card">
        <div class="pay-card-head">
            <div class="pay-card-icon amber">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
            </div>
            <div>
                <div class="pay-card-title">Payment Date</div>
            </div>
        </div>
        <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}"
               class="pay-input" required>
    </div>

    <div class="pay-card">
        <div class="pay-card-head">
            <div class="pay-card-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 7h16M4 12h16M4 17h10"/>
                </svg>
            </div>
            <div>
                <div class="pay-card-title">Notes</div>
                <div class="pay-card-sub">Optional</div>
            </div>
        </div>
        <textarea name="notes" rows="2" placeholder="Additional details..."
                  class="pay-input" style="resize:vertical;min-height:70px;">{{ old('notes') }}</textarea>
    </div>

    <button type="submit" class="pay-submit" id="submitBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M5 12l5 5L20 7"/>
        </svg>
        Record Payment
    </button>
</form>

@endsection

@push('styles')
<style>
    .page-header { display:flex; align-items:center; margin-bottom:16px; }
    .page-header-left { display:flex; align-items:center; gap:12px; }
    .back-btn {
        width:36px; height:36px; border-radius:10px;
        background:rgba(255,255,255,0.05); color:#c4bdb4;
        display:grid; place-items:center; text-decoration:none;
        transition:all 0.15s;
    }
    .back-btn:hover { background:rgba(255,255,255,0.1); color:#f5f3f0; }
    .page-title { font-size:17px; font-weight:800; color:#f5f3f0; }
    .page-sub { font-size:11.5px; color:#8a8378; margin-top:2px; }

    .pay-error {
        padding:12px 14px; margin-bottom:14px;
        background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);
        border-radius:12px; color:#f87171; font-size:12px;
    }

    .pay-card {
        background:linear-gradient(165deg,#1e1a16,#15120f);
        border:1px solid rgba(255,255,255,0.06);
        border-radius:16px;
        padding:18px 16px;
        margin-bottom:14px;
    }
    .pay-card-head {
        display:flex; align-items:center; gap:12px; margin-bottom:14px;
    }
    .pay-card-icon {
        width:36px; height:36px; border-radius:10px;
        background:rgba(201,169,97,0.15); color:#c9a961;
        display:grid; place-items:center; flex-shrink:0;
    }
    .pay-card-icon.green { background:rgba(34,197,94,0.15); color:#22c55e; }
    .pay-card-icon.blue { background:rgba(59,130,246,0.15); color:#3b82f6; }
    .pay-card-icon.amber { background:rgba(245,158,11,0.15); color:#f59e0b; }
    .pay-card-title { font-size:13.5px; font-weight:800; color:#f5f3f0; }
    .pay-card-sub { font-size:11px; color:#8a8378; margin-top:2px; }

    /* AMOUNT */
    .pay-amount-wrap {
        position:relative; display:flex; align-items:center;
        background:rgba(0,0,0,0.35); border:1px solid rgba(255,255,255,0.08);
        border-radius:12px; padding:8px 14px;
    }
    .pay-amount-peso {
        font-size:22px; font-weight:800; color:#c9a961; margin-right:6px;
    }
    .pay-amount-input {
        flex:1; background:transparent; border:none; outline:none;
        color:#f5f3f0; font-size:22px; font-weight:800;
        font-family:inherit; font-variant-numeric:tabular-nums;
    }
    .pay-quick-btn {
        margin-top:10px; padding:7px 14px;
        background:rgba(201,169,97,0.12); color:#c9a961;
        border:1px solid rgba(201,169,97,0.3); border-radius:8px;
        font-size:11.5px; font-weight:700; font-family:inherit;
        cursor:pointer;
    }
    .pay-quick-btn:hover { background:rgba(201,169,97,0.2); }

    /* METHOD CATEGORIES */
    .pm-categories {
        display:grid; grid-template-columns:1fr 1fr; gap:10px;
        margin-bottom:14px;
    }
    .pm-cat {
        display:flex; align-items:center; gap:10px;
        padding:12px;
        background:rgba(255,255,255,0.03);
        border:1.5px solid rgba(255,255,255,0.08);
        border-radius:12px; cursor:pointer;
        font-family:inherit; text-align:left;
        transition:all 0.15s;
    }
    .pm-cat:hover { border-color:rgba(201,169,97,0.3); }
    .pm-cat.active {
        background:linear-gradient(135deg,rgba(201,169,97,0.18),rgba(138,95,54,0.08));
        border-color:#c9a961;
        box-shadow:0 8px 20px -8px rgba(201,169,97,0.5);
    }
    .pm-cat-icon { font-size:22px; flex-shrink:0; }
    .pm-cat-label { font-size:12.5px; font-weight:800; color:#f5f3f0; }
    .pm-cat-sub { font-size:10.5px; color:#8a8378; margin-top:2px; }
    .pm-cat.active .pm-cat-label { color:#c9a961; }

    /* OPTIONS */
    .pm-option { display:block; cursor:pointer; margin-bottom:8px; }
    .pm-option:last-child { margin-bottom:0; }
    .pm-option input { display:none; }
    .pm-option-card {
        display:flex; align-items:center; gap:12px;
        padding:12px 14px;
        background:rgba(255,255,255,0.03);
        border:1.5px solid rgba(255,255,255,0.06);
        border-radius:12px;
        transition:all 0.15s;
        position:relative;
    }
    .pm-option:hover .pm-option-card { border-color:rgba(201,169,97,0.3); }
    .pm-option input:checked + .pm-option-card {
        background:linear-gradient(135deg,rgba(201,169,97,0.15),rgba(138,95,54,0.05));
        border-color:#c9a961;
    }
    .pm-option-icon {
        width:38px; height:38px; border-radius:10px;
        background:rgba(34,197,94,0.15); color:#22c55e;
        display:grid; place-items:center; font-size:18px;
        flex-shrink:0;
    }
    .pm-option-label { font-size:13px; font-weight:800; color:#f5f3f0; }
    .pm-option-sub { font-size:11px; color:#8a8378; margin-top:2px; }
    .pm-option-check {
        position:absolute; right:14px; top:50%; transform:translateY(-50%);
        width:22px; height:22px; border-radius:50%;
        background:rgba(255,255,255,0.05); color:#8a8378;
        display:grid; place-items:center;
        opacity:0; transition:opacity 0.15s;
    }
    .pm-option input:checked + .pm-option-card .pm-option-check {
        opacity:1; background:#c9a961; color:#fff;
    }

    /* INPUT */
    .pay-input {
        width:100%; padding:12px 14px;
        background:rgba(0,0,0,0.35);
        border:1px solid rgba(255,255,255,0.08);
        border-radius:11px;
        color:#f5f3f0; font-size:13px;
        font-family:inherit; outline:none;
        transition:border-color 0.15s;
    }
    .pay-input:focus { border-color:#c9a961; }

    /* SUBMIT */
    .pay-submit {
        width:100%; min-height:52px;
        display:inline-flex; align-items:center; justify-content:center;
        gap:8px;
        background:linear-gradient(135deg,#c9a961,#8a5f36);
        border:none; border-radius:14px;
        color:#fff; font-size:14.5px; font-weight:800;
        font-family:inherit; cursor:pointer;
        box-shadow:0 12px 28px -10px rgba(201,169,97,0.7);
        letter-spacing:0.02em;
        -webkit-tap-highlight-color:transparent;
    }
    .pay-submit:hover { background:linear-gradient(135deg,#d4b673,#9a6b3f); }
    .pay-submit:active { transform:scale(0.98); }
    .pay-submit:disabled { opacity:0.6; cursor:not-allowed; }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const cats = document.querySelectorAll('.pm-cat');
    const panels = document.querySelectorAll('.pm-options');
    const refCard = document.getElementById('refCard');
    const form = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('submitBtn');

    cats.forEach(function(cat) {
        cat.addEventListener('click', function() {
            const target = cat.dataset.cat;
            cats.forEach(c => c.classList.remove('active'));
            cat.classList.add('active');

            panels.forEach(p => {
                p.style.display = p.dataset.panel === target ? 'block' : 'none';
            });

            // Auto-select first radio sa panel
            const firstRadio = document.querySelector(`[data-panel="${target}"] input[type="radio"]`);
            if (firstRadio) firstRadio.checked = true;

            updateRef();
        });
    });

    // Watch radios para sa ref card
    document.querySelectorAll('input[name="method"]').forEach(function(r) {
        r.addEventListener('change', updateRef);
    });

    function updateRef() {
        const selected = document.querySelector('input[name="method"]:checked');
        if (!selected) return;
        const isOnline = ['gcash','maya','bank_transfer','online'].includes(selected.value);
        refCard.style.display = isOnline ? 'block' : 'none';
    }

    // Loading state on submit
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10" opacity="0.3"/><path d="M22 12a10 10 0 0 1-10 10" stroke-linecap="round"/></svg> Saving...';
    });
})();
</script>
@endpush