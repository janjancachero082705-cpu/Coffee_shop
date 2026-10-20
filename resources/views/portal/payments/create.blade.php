@extends('portal.layouts.app')
@section('title', 'Record Payment')
@section('content')
@php
    $balance = (float) ($displayBalance ?? $totalBalance ?? 0);
    $preset = (float) ($presetAmount ?? $balance);
    $isLinked = ($linkedDR ?? false) ? true : false;
@endphp

<style>
    .premium-input {
        width: 100%; padding: 14px 16px; border: 1.5px solid #E5E7EB; border-radius: 12px;
        font-size: 15px; font-weight: 600; color: #1F2937; background: #F9FAFB;
        transition: all 0.2s ease; outline: none;
    }
    .premium-input:focus { border-color: #E8A33D; background: #FFF; box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.15); }
    .premium-label { font-size: 11px; font-weight: 800; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block; }
    .glass-card { background: #FFF; border: 1px solid #E5E7EB; border-radius: 18px; padding: 20px; margin-bottom: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    .segment-btn { flex: 1; padding: 12px; border: none; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.3s ease; }
    .segment-btn.active { background: #1F2937; color: #FFF; box-shadow: 0 4px 12px rgba(31, 41, 55, 0.2); }
    .segment-btn.inactive { background: transparent; color: #6B7280; }
    .submit-btn { width: 100%; padding: 18px; background: linear-gradient(135deg, #E8A33D, #D4901F); color: #FFF; font-size: 16px; font-weight: 800; border: none; border-radius: 100px; box-shadow: 0 8px 20px rgba(232, 163, 61, 0.3); cursor: pointer; transition: transform 0.1s; margin-top: 10px; }
    .submit-btn:active { transform: scale(0.98); }
</style>

<a href="{{ route('portal.payments.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;margin-bottom:18px;background:#FFF;border:1px solid #E5E7EB;border-radius:100px;color:#4B5563;font-size:13px;font-weight:700;text-decoration:none;box-shadow:0 2px 5px rgba(0,0,0,0.02);">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back
</a>

@if($errors->any())
    <div style="background: #FEF2F2; border: 1.5px solid #FECACA; color: #B91C1C; padding: 14px; border-radius: 12px; margin-bottom: 16px; font-size: 13px; font-weight: 600;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('portal.payments.store') }}" id="paymentForm" enctype="multipart/form-data">
    @csrf
    @if($isLinked)<input type="hidden" name="delivery_receipt_id" value="{{ $linkedDR->id }}">@endif

    <!-- Balance Card -->
    <div style="background: linear-gradient(135deg, #1F2937, #111827); border-radius: 20px; padding: 24px; margin-bottom: 24px; color: #FFF; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
        <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(232, 163, 61, 0.2); border-radius: 50%; filter: blur(30px);"></div>
        <div style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; position: relative; z-index: 1;">Outstanding Balance</div>
        <div style="font-size: 42px; font-weight: 900; line-height: 1; position: relative; z-index: 1;">₱{{ number_format($balance, 2) }}</div>
    </div>

    <!-- Amount -->
    <label class="premium-label">Amount to Pay</label>
    <div class="glass-card" style="padding: 12px;">
        <div style="display:flex; align-items:center; gap: 10px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 14px; padding: 12px 18px;">
            <span style="font-size: 26px; font-weight: 900; color: #D4901F;">₱</span>
            <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', number_format($preset, 2, '.', '')) }}" placeholder="0.00" required style="flex: 1; background: transparent; border: none; outline: none; font-size: 28px; font-weight: 900; color: #1F2937;">
        </div>
    </div>

    <!-- Payment Method -->
    <label class="premium-label">Payment Method</label>
    <div class="glass-card">
        <div style="display: flex; gap: 6px; background: #F3F4F6; padding: 6px; border-radius: 14px; margin-bottom: 16px;">
            <button type="button" class="segment-btn active" data-cat="cash">Cash</button>
            <button type="button" class="segment-btn inactive" data-cat="online">GCash</button>
        </div>

        <div class="pc-methods" data-panel="cash">
            <label style="display:flex; align-items:center; gap:12px; padding:16px; background:#F9FAFB; border:1.5px solid #E8A33D; border-radius:14px; cursor:pointer;">
                <input type="radio" name="method" value="cash" checked style="width: 18px; height: 18px; accent-color: #E8A33D;">
                <div style="font-weight: 800; color: #1F2937;">Cash Payment</div>
            </label>
        </div>

        <div class="pc-methods" data-panel="online" style="display:none;">
            <label style="display:flex; align-items:center; gap:12px; padding:16px; background:#F9FAFB; border:1.5px solid #E8A33D; border-radius:14px; cursor:pointer;">
                <input type="radio" name="method" value="gcash" style="width: 18px; height: 18px; accent-color: #E8A33D;">
                <div style="font-weight: 800; color: #1F2937;">GCash Online</div>
            </label>
        </div>
    </div>

    <!-- GCash Section -->
    <div id="gcashSection" style="display:none;">
        <label class="premium-label">GCash Details</label>
        <div class="glass-card" style="background: #F8FAFC; border-color: #BFDBFE;">
            
            @if(isset($paymentSetting) && $paymentSetting->gcash_qr_path)
                <div style="background: #FFF; padding: 12px; border-radius: 16px; border: 1px solid #E2E8F0; width: fit-content; margin: 0 auto 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <img src="{{ asset('storage/' . $paymentSetting->gcash_qr_path) }}" alt="QR" style="width: 180px; height: 180px; object-fit: contain; border-radius: 8px;">
                </div>
            @endif
            
            <div style="text-align: center; margin-bottom: 20px;">
                <p style="font-size: 15px; font-weight: 800; color: #1E3A8A; margin: 0;">{{ $paymentSetting->gcash_name ?? 'RYS Farm Products' }}</p>
                <p style="font-size: 14px; font-weight: 700; color: #2563EB; margin: 4px 0 0;">{{ $paymentSetting->gcash_number ?? '0917-123-4567' }}</p>
            </div>

            <label style="font-size:12px;font-weight:700;color:#4B5563;margin-bottom:6px;display:block;">Reference Number <span style="color:#EF4444;">*</span></label>
            <input type="text" name="reference_number" id="refInput" placeholder="Enter 13-digit Ref No." class="premium-input" style="margin-bottom: 16px; background: #FFF; border-color: #BFDBFE;">

            <label style="font-size:12px;font-weight:700;color:#4B5563;margin-bottom:6px;display:block;">Upload Receipt <span style="color:#EF4444;">*</span></label>
            <div style="border: 2px dashed #93C5FD; background: #EFF6FF; border-radius: 12px; padding: 20px; text-align: center; position: relative; cursor: pointer;">
                <input type="file" name="proof_of_payment" id="receiptInput" accept="image/*" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                <svg width="24" height="24" fill="none" stroke="#3B82F6" stroke-width="2" viewBox="0 0 24 24" style="margin-bottom: 8px;"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <div style="font-size: 13px; font-weight: 700; color: #2563EB;">Tap to upload screenshot</div>
            </div>
        </div>
    </div>

    <!-- Date -->
    <label class="premium-label">Payment Date</label>
    <div class="glass-card" style="padding: 12px;">
        <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required class="premium-input">
    </div>

    <button type="submit" id="submitBtn" class="submit-btn">Submit Payment</button>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const segments = document.querySelectorAll('.segment-btn');
    const panels = document.querySelectorAll('.pc-methods');
    const gcashSection = document.getElementById('gcashSection');
    const refInput = document.getElementById('refInput');
    const receiptInput = document.getElementById('receiptInput');

    segments.forEach(seg => {
        seg.addEventListener('click', function() {
            const target = seg.dataset.cat;
            
            // Toggle Buttons UI
            segments.forEach(s => {
                s.classList.remove('active');
                s.classList.add('inactive');
            });
            seg.classList.add('active');
            seg.classList.remove('inactive');

            // Toggle Panels
            panels.forEach(p => { p.style.display = p.dataset.panel === target ? 'block' : 'none'; });
            
            const firstRadio = document.querySelector('[data-panel="' + target + '"] input[type="radio"]');
            if (firstRadio) firstRadio.checked = true;

            // Toggle GCash section fields
            if (target === 'online') {
                gcashSection.style.display = 'block';
                refInput.required = true;
                receiptInput.required = true;
            } else {
                gcashSection.style.display = 'none';
                refInput.required = false;
                receiptInput.required = false;
            }
        });
    });

    // Show filename on upload
    receiptInput.addEventListener('change', function(e) {
        if(e.target.files.length > 0) {
            const fileName = e.target.files[0].name;
            const textDiv = e.target.parentElement.querySelector('div');
            textDiv.innerHTML = `<span style="color:#10B981;">✓ ${fileName}</span>`;
        }
    });
});
</script>
@endpush