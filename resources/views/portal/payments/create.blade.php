@extends('portal.layouts.app')
@section('title', 'Record Payment')
@section('content')

@php
    $balance = (float) ($displayBalance ?? $totalBalance ?? 0);
    $preset = (float) ($presetAmount ?? $balance);
    $isLinked = ($linkedDR ?? false) ? true : false;
@endphp

<style>
    /* --- SMART APP UI CSS --- */
    :root {
        --primary: #E8A33D;
        --primary-dark: #D4901F;
        --bg-color: #F5F6F8;
        --card-bg: #FFFFFF;
        --text-main: #1F2937;
        --text-muted: #6B7280;
        --border-color: #E5E7EB;
    }

    body { background: var(--bg-color); }
    
    .checkout-wrapper { max-width: 500px; margin: 0 auto; padding: 16px; padding-bottom: 32px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    
    /* Header */
    .checkout-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .back-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #FFF; border: 1px solid var(--border-color); border-radius: 50%; color: var(--text-main); text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.03); }
    .header-title { font-size: 18px; font-weight: 800; color: var(--text-main); margin: 0; }

    /* Amount Card */
    .amount-card { background: linear-gradient(135deg, #1F2937, #111827); border-radius: 20px; padding: 24px; color: #FFF; position: relative; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); margin-bottom: 24px; }
    .amount-card::before { content: ''; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(232, 163, 61, 0.2); border-radius: 50%; filter: blur(30px); }
    .amount-label { font-size: 12px; font-weight: 700; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px; position: relative; z-index: 1; }
    .amount-value { font-size: 40px; font-weight: 900; line-height: 1; letter-spacing: -1px; position: relative; z-index: 1; }
    
    .quick-amounts { display: flex; gap: 10px; margin-top: 20px; position: relative; z-index: 1; }
    .quick-btn { flex: 1; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; color: #FFF; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s; }
    .quick-btn:active { background: rgba(255,255,255,0.2); transform: scale(0.96); }

    /* Input Fields */
    .section-title { font-size: 13px; font-weight: 800; color: var(--text-main); margin: 0 0 12px 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .input-group { background: var(--card-bg); border: 1.5px solid var(--border-color); border-radius: 14px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .input-group input { border: none; outline: none; background: transparent; font-size: 24px; font-weight: 900; color: var(--text-main); width: 100%; }
    .input-group .currency { font-size: 24px; font-weight: 900; color: var(--primary-dark); }

    /* SMART SELECTED METHOD CARD */
    .selected-method-card { display: flex; align-items: center; gap: 16px; padding: 16px; background: var(--card-bg); border: 1.5px solid var(--border-color); border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); margin-bottom: 24px; cursor: pointer; transition: 0.2s; }
    .selected-method-card:active { transform: scale(0.98); }
    .selected-method-card .method-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .selected-method-card .method-info { flex: 1; }
    .selected-method-card .method-name { font-size: 15px; font-weight: 800; color: var(--text-main); }
    .selected-method-card .change-btn { padding: 8px 14px; background: #FFF8EC; border: 1px solid #FDE68A; border-radius: 8px; color: var(--primary-dark); font-size: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; }
    .selected-method-card .change-btn:active { background: #FDE68A; }

    /* Dynamic Wallet Details */
    .wallet-details { max-height: 0; overflow: hidden; opacity: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); margin-bottom: 0; }
    .wallet-details.active { max-height: 1200px; opacity: 1; margin-bottom: 24px; }
    
    .wallet-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    /* QR Box - Proportional & Sharp */
    .qr-box { 
        background: #FFFFFF; 
        border: 1px solid #E5E7EB; 
        border-radius: 20px; 
        padding: 16px; 
        display: flex; 
        justify-content: center; 
        align-items: center;
        margin-bottom: 16px; 
        box-shadow: 0 8px 24px -8px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 280px;
        margin-left: auto; 
        margin-right: auto; 
    }
    .qr-box img { 
        width: 100%; 
        height: auto; 
        max-height: 240px; 
        object-fit: contain; 
        border-radius: 12px; 
        image-rendering: -webkit-optimize-contrast; /* Sharpen for scanning */
        image-rendering: crisp-edges;
    }
    
    .account-info { text-align: center; margin-bottom: 16px; }
    .account-name { font-size: 14px; font-weight: 800; color: var(--text-main); margin-bottom: 4px; }
    .account-number { font-size: 16px; font-weight: 900; color: #2563EB; letter-spacing: 1px; }
    
    .copy-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #EFF6FF; color: #2563EB; border: none; border-radius: 100px; font-size: 12px; font-weight: 800; cursor: pointer; margin-top: 10px; transition: 0.2s; }
    .copy-btn:active { background: #DBEAFE; transform: scale(0.96); }

    /* Inputs (Ref & Date) */
    .standard-input { width: 100%; padding: 16px; background: var(--card-bg); border: 1.5px solid var(--border-color); border-radius: 14px; font-size: 14px; font-weight: 600; color: var(--text-main); outline: none; transition: 0.2s; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .standard-input:focus { border-color: var(--primary); }

    /* Upload Box (Premium Design) */
    .upload-box { 
        position: relative; 
        border: 2px dashed #CBD5E1; 
        background: #F8FAFC; 
        border-radius: 16px; 
        padding: 24px; 
        text-align: center; 
        cursor: pointer; 
        transition: all 0.3s ease; 
        overflow: hidden; 
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 140px;
    }
    .upload-box:hover { border-color: var(--primary); background: #FFFBF2; }
    .upload-box.has-file { border: 1px solid var(--border-color); background: #FFF; padding: 12px; }
    .upload-box input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; width: 100%; height: 100%; }
    
    .upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .upload-placeholder svg { color: var(--primary); margin-bottom: 4px; }
    .upload-text { font-size: 14px; font-weight: 800; color: var(--text-main); }
    .upload-subtext { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-top: 2px; }
    
    .upload-preview-container { display: none; width: 100%; flex-direction: column; gap: 12px; }
    .upload-preview-img { width: 100%; max-height: 200px; object-fit: contain; border-radius: 10px; border: 1px solid var(--border-color); }
    .file-info { display: flex; align-items: center; justify-content: space-between; background: #F1F5F9; padding: 10px 14px; border-radius: 10px; }
    .file-name { font-size: 12px; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%; }
    .file-size { font-size: 11px; font-weight: 600; color: var(--text-muted); }
    .file-replace { font-size: 11px; font-weight: 800; color: #2563EB; text-decoration: underline; margin-top: 8px; }

    /* Footer (Saktong espasyo) */
    .form-footer { margin-top: 16px; display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; }
    .footer-total-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .footer-total-value { font-size: 20px; font-weight: 900; color: var(--text-main); }
    .submit-btn { padding: 16px 32px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #FFF; font-size: 15px; font-weight: 800; border: none; border-radius: 100px; box-shadow: 0 8px 20px rgba(232, 163, 61, 0.4); cursor: pointer; transition: transform 0.1s; }
    .submit-btn:active { transform: scale(0.97); }

    /* BOTTOM SHEET (SCROLLABLE & 90VH HEIGHT) */
    .sheet-overlay { 
        position: fixed; 
        inset: 0; 
        background: rgba(0,0,0,0.5); 
        backdrop-filter: blur(3px); 
        z-index: 1000; 
        visibility: hidden; 
        opacity: 0; 
        transition: opacity 0.3s ease, visibility 0.3s ease; 
    }
    .sheet-overlay.active { 
        visibility: visible; 
        opacity: 1; 
    }
    
    .bottom-sheet { 
        position: fixed; 
        bottom: 0; 
        left: 0; 
        right: 0; 
        background: #FFF; 
        border-radius: 24px 24px 0 0; 
        z-index: 1001; 
        transform: translateY(100%); 
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        max-width: 500px; 
        margin: 0 auto; 
        box-shadow: 0 -10px 40px rgba(0,0,0,0.1); 
        visibility: hidden;
        max-height: 90vh; /* Mas mataas na para kita lahat */
        display: flex; 
        flex-direction: column;
    }
    .bottom-sheet.active { 
        visibility: visible;
        transform: translateY(0); 
    }
    
    .sheet-handle { width: 40px; height: 5px; background: #D1D5DB; border-radius: 10px; margin: 12px auto; flex-shrink: 0; }
    .sheet-header { padding: 0 20px 16px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
    .sheet-title { font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; }
    .sheet-close { background: #F3F4F6; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-muted); }
    
    .sheet-list { 
        padding: 16px 20px calc(24px + env(safe-area-inset-bottom)); 
        overflow-y: auto; /* SCROLLABLE KAPAG MARAMI */
        flex: 1;
        -webkit-overflow-scrolling: touch; /* SMOOTH SCROLL SA IOS */
    }
    .sheet-item { display: flex; align-items: center; gap: 16px; padding: 16px; border: 1.5px solid var(--border-color); border-radius: 14px; margin-bottom: 10px; cursor: pointer; transition: 0.2s; }
    .sheet-item:active { background: #F9FAFB; transform: scale(0.98); }
    .sheet-item.selected { border-color: var(--primary); background: #FFFBF2; }
    
    .sheet-item .method-icon { width: 40px; height: 40px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
    .sheet-item .method-name { flex: 1; font-size: 15px; font-weight: 800; color: var(--text-main); }
    .sheet-item .check-icon { width: 20px; height: 20px; border-radius: 50%; background: var(--primary); color: #FFF; display: flex; align-items: center; justify-content: center; opacity: 0; transform: scale(0.5); transition: 0.2s; }
    .sheet-item.selected .check-icon { opacity: 1; transform: scale(1); }
</style>

<div class="checkout-wrapper">
    <!-- Header -->
    <div class="checkout-header">
        <a href="{{ route('portal.payments.index') }}" class="back-btn">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="header-title">Checkout</h1>
    </div>

    @if($errors->any())
        <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.payments.store') }}" id="paymentForm" enctype="multipart/form-data">
        @csrf
        @if($isLinked)<input type="hidden" name="delivery_receipt_id" value="{{ $linkedDR->id }}">@endif

        <!-- Amount Card -->
        <div class="amount-card">
            <div class="amount-label">Outstanding Balance</div>
            <div class="amount-value">₱{{ number_format($balance, 2) }}</div>
            
            <div class="quick-amounts">
                <button type="button" class="quick-btn" onclick="setAmount({{ $balance }})">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg> Full
                </button>
                <button type="button" class="quick-btn" onclick="setAmount({{ $balance / 2 }})">Half</button>
            </div>
        </div>

        <!-- Amount Input -->
        <div class="section-title">Amount to Pay</div>
        <div class="input-group">
            <span class="currency">₱</span>
            <input type="number" step="0.01" min="0.01" name="amount" id="amountInput" value="{{ old('amount', number_format($preset, 2, '.', '')) }}" placeholder="0.00" required>
        </div>

        <!-- SMART PAYMENT METHOD SELECTOR -->
        <div class="section-title">Payment Method</div>
        <div class="selected-method-card" onclick="openBottomSheet()">
            <div class="method-icon" id="mainMethodIcon">
                <svg width="40" height="40" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#F0FDF4" stroke="#BBF7D0" stroke-width="1"/><rect x="10" y="16" width="28" height="16" rx="3" stroke="#15803D" stroke-width="2.5"/><circle cx="24" cy="24" r="4" stroke="#15803D" stroke-width="2.5"/></svg>
            </div>
            <div class="method-info">
                <div class="method-name" id="mainMethodName">Cash</div>
            </div>
            <button type="button" class="change-btn">CHANGE</button>
        </div>

        <!-- Hidden Radio Buttons (Para ma-submit ang method) -->
        <div style="display: none;">
            <input type="radio" name="method" id="radio_cash" value="cash" checked>
            @foreach($eWallets as $wallet)
                <input type="radio" name="method" id="radio_{{ $wallet->code }}" value="{{ $wallet->code }}">
            @endforeach
        </div>

        <!-- Dynamic Wallet Details (Lalabas lang kapag E-Wallet) -->
        <div id="walletDetails" class="wallet-details">
            <div class="wallet-card">
                <div id="walletQrContainer"></div>
                <div class="account-info">
                    <div id="walletName" class="account-name">-</div>
                    <div id="walletNumber" class="account-number">-</div>
                    <button type="button" class="copy-btn" id="copyBtn" style="display: none;" onclick="copyNumber()">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy Number
                    </button>
                </div>
                <label style="display:block; font-size:12px; font-weight:800; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase;">Reference Number</label>
                <input type="text" name="reference_number" id="refInput" placeholder="Enter Reference No." class="standard-input" style="margin-bottom:16px;">
                
                <label style="display:block; font-size:12px; font-weight:800; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase;">Upload Receipt</label>
                <div class="upload-box" id="uploadBox">
                    <input type="file" name="proof_of_payment" id="receiptInput" accept="image/*">
                    
                    <!-- Placeholder (Initial State) -->
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <div class="upload-text">Tap to upload screenshot</div>
                        <div class="upload-subtext">JPG, PNG or WEBP (Max 5MB)</div>
                    </div>

                    <!-- Preview (After Upload) -->
                    <div class="upload-preview-container" id="uploadPreviewContainer">
                        <img id="uploadPreview" class="upload-preview-img">
                        <div class="file-info">
                            <span id="fileName" class="file-name">image.jpg</span>
                            <span id="fileSize" class="file-size">0 KB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date -->
        <div class="section-title" style="margin-top: 24px;">Payment Date</div>
        <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required class="standard-input" style="margin-bottom: 0;">

        <!-- FOOTER (Nasa loob na ng form at dikit sa date) -->
        <div class="form-footer">
            <div>
                <div class="footer-total-label">Total Amount</div>
                <div class="footer-total-value" id="stickyTotal">₱{{ number_format($preset, 2) }}</div>
            </div>
            <button type="submit" class="submit-btn">Pay Now</button>
        </div>
    </form>
</div>

<!-- BOTTOM SHEET (Payment Method Selector) -->
<div class="sheet-overlay" id="sheetOverlay" onclick="closeBottomSheet()"></div>
<div class="bottom-sheet" id="bottomSheet">
    <div class="sheet-handle"></div>
    <div class="sheet-header">
        <h3 class="sheet-title">Choose Payment Method</h3>
        <button class="sheet-close" onclick="closeBottomSheet()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="sheet-list">
        <!-- Cash Option -->
        <div class="sheet-item selected" id="sheet_cash" onclick="selectMethodFromSheet('cash', 'Cash')">
            <div class="method-icon">
                <svg width="40" height="40" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#F0FDF4" stroke="#BBF7D0" stroke-width="1"/><rect x="10" y="16" width="28" height="16" rx="3" stroke="#15803D" stroke-width="2.5"/><circle cx="24" cy="24" r="4" stroke="#15803D" stroke-width="2.5"/></svg>
            </div>
            <div class="method-name">Cash</div>
            <div class="check-icon"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        </div>

        <!-- E-Wallet Options -->
        @foreach($eWallets as $wallet)
            @php
                $svgLogos = [
                    'gcash' => '<svg width="40" height="40" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#007DFE"/><path d="M23.5 12C17.1 12 12 17.1 12 23.5C12 29.9 17.1 35 23.5 35C28.5 35 32.7 31.8 34.3 27.3H23.5V23.5H39V23.5C39 32.1 32.1 39 23.5 39C14.9 39 8 32.1 8 23.5C8 14.9 14.9 8 23.5 8C29.3 8 34.2 11.3 36.8 16L33 18.2C31.1 15 27.5 12 23.5 12Z" fill="#FFFFFF"/><path d="M36 20C37.1 20 38 20.9 38 22V26C38 27.1 37.1 28 36 28C34.9 28 34 27.1 34 26V22C34 20.9 34.9 20 36 20Z" fill="#8CC6FF"/><path d="M40 18C41.1 18 42 18.9 42 20V28C42 29.1 41.1 30 40 30C38.9 30 38 29.1 38 28V20C38 18.9 38.9 18 40 18Z" fill="#8CC6FF"/></svg>',
                    'maya' => '<svg width="40" height="40" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#00A651"/><text x="24" y="32" font-family="Arial" font-size="18" font-weight="bold" fill="white" text-anchor="middle">maya</text></svg>',
                    'grabpay' => '<svg width="40" height="40" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#00B14F"/><text x="24" y="32" font-family="Arial" font-size="14" font-weight="bold" fill="white" text-anchor="middle">GrabPay</text></svg>',
                    'shopeepay' => '<svg width="40" height="40" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#EE4D2D"/><text x="24" y="32" font-family="Arial" font-size="14" font-weight="bold" fill="white" text-anchor="middle">SPay</text></svg>',
                    'other' => '<svg width="40" height="40" viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#6366F1"/><path d="M12 24H36M24 12V36" stroke="white" stroke-width="3" stroke-linecap="round"/></svg>',
                ];
                $logo = $wallet->logo_path ? '<img src="' . asset('storage/' . $wallet->logo_path) . '" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">' : ($svgLogos[$wallet->code] ?? $svgLogos['other']);
            @endphp
            <div class="sheet-item" id="sheet_{{ $wallet->code }}" onclick="selectMethodFromSheet('{{ $wallet->code }}', '{{ $wallet->name }}', {{ $wallet->id }})">
                <div class="method-icon">{!! $logo !!}</div>
                <div class="method-name">{{ $wallet->name }}</div>
                <div class="check-icon"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
            </div>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const walletsData = @json($eWallets);
    const walletDetails = document.getElementById('walletDetails');
    const refInput = document.getElementById('refInput');
    const receiptInput = document.getElementById('receiptInput');
    const uploadText = document.getElementById('uploadText');
    const uploadPreview = document.getElementById('uploadPreview');
    const copyBtn = document.getElementById('copyBtn');
    const amountInput = document.getElementById('amountInput');
    const stickyTotal = document.getElementById('stickyTotal');
    const bottomSheet = document.getElementById('bottomSheet');
    const sheetOverlay = document.getElementById('sheetOverlay');

    // ✅ FIX: Ilipat ang modal sa body para hindi maapektuhan ng scroll ng page
    if (bottomSheet && sheetOverlay) {
        document.body.appendChild(sheetOverlay);
        document.body.appendChild(bottomSheet);
    }

    // Update Sticky Total when Amount changes
    amountInput.addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        stickyTotal.textContent = '₱' + val.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    });

    // Quick Amount Logic
    window.setAmount = function(val) {
        amountInput.value = Number(val).toFixed(2);
        amountInput.dispatchEvent(new Event('input'));
    };

    // Copy Number Logic (With Fallback for Local IP/HTTP)
    window.copyNumber = function() {
        const number = document.getElementById('walletNumber').textContent.trim();
        
        if(number && number !== 'No Account Number') {
            // 1. Try modern API (works on HTTPS or localhost)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(number).then(showCopiedState).catch(() => fallbackCopy(number));
            } else {
                // 2. Fallback para sa Mobile Local IP testing
                fallbackCopy(number);
            }
        }
    };

    function fallbackCopy(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showCopiedState();
        } catch (err) {
            console.error('Fallback copy failed', err);
            alert('Failed to copy. Please manually copy the number.');
        }
        textArea.remove();
    }

    function showCopiedState() {
        const copyBtn = document.getElementById('copyBtn');
        const originalHTML = copyBtn.innerHTML;
        copyBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg> Copied!';
        copyBtn.style.background = '#D1FAE5'; // Green background
        copyBtn.style.color = '#10B981';
        
        setTimeout(() => { 
            copyBtn.innerHTML = originalHTML; 
            copyBtn.style.background = '#EFF6FF'; // Revert to blue
            copyBtn.style.color = '#2563EB';
        }, 2000);
    }

    // Bottom Sheet Logic
    window.openBottomSheet = function() {
        requestAnimationFrame(() => {
            bottomSheet.classList.add('active');
            sheetOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; 
        });
    };

    window.closeBottomSheet = function() {
        bottomSheet.classList.remove('active');
        sheetOverlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    // Select Method from Bottom Sheet
    window.selectMethodFromSheet = function(methodCode, methodName, walletId = null) {
        // 1. Highlight selected item sa sheet
        document.querySelectorAll('.sheet-item').forEach(item => item.classList.remove('selected'));
        const sheetItem = document.getElementById('sheet_' + methodCode);
        if (sheetItem) sheetItem.classList.add('selected');

        // 2. I-update ang main card UI
        if (sheetItem) {
            const iconHTML = sheetItem.querySelector('.method-icon').innerHTML;
            document.getElementById('mainMethodIcon').innerHTML = iconHTML;
        }
        document.getElementById('mainMethodName').textContent = methodName;

        // 3. I-check ang tamang hidden radio button
        const radioBtn = document.getElementById('radio_' + methodCode);
        if (radioBtn) radioBtn.checked = true;

        // 4. Logic para sa Wallet Details
        if (methodCode === 'cash') {
            walletDetails.classList.remove('active');
            refInput.required = false;
            receiptInput.required = false;
            
            // CLEAR INPUTS PARA HINDI MAG-STAY ANG DATA KAPAG NAG-SWITCH
            refInput.value = '';
            receiptInput.value = '';
            uploadText.innerHTML = 'Tap to upload screenshot';
            uploadPreview.src = '';
            uploadPreview.style.display = 'none';
        } else {
            walletDetails.classList.add('active');
            refInput.required = true;
            receiptInput.required = true;

            const wallet = walletsData.find(w => w.id === walletId);
            if(wallet) {
                document.getElementById('walletName').textContent = wallet.account_name || wallet.name;
                document.getElementById('walletNumber').textContent = wallet.account_number || 'No Account Number';
                
                if(wallet.account_number) {
                    copyBtn.style.display = 'inline-flex';
                } else {
                    copyBtn.style.display = 'none';
                }
                
                const qrContainer = document.getElementById('walletQrContainer');
                if(wallet.qr_code) {
                    qrContainer.innerHTML = `
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <div class="qr-box">
                                <img src="/storage/${wallet.qr_code}" alt="QR Code" loading="lazy">
                            </div>
                            <div style="text-align: center; font-size: 12px; font-weight: 800; color: #6B7280; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px;">
                                Scan to Pay
                            </div>
                        </div>
                    `;
                } else {
                    qrContainer.innerHTML = `<div style="text-align:center; font-size:12px; color:#888; margin-bottom:15px;">No QR Code provided.</div>`;
                }
            }
        }

        // 5. Isara ang bottom sheet
        closeBottomSheet();
    };

    // Live Image Preview (Enhanced)
    receiptInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const uploadBox = document.getElementById('uploadBox');
            const placeholder = document.getElementById('uploadPlaceholder');
            const previewContainer = document.getElementById('uploadPreviewContainer');
            const previewImg = document.getElementById('uploadPreview');
            const fileNameEl = document.getElementById('fileName');
            const fileSizeEl = document.getElementById('fileSize');

            // Compute file size
            let sizeText = '';
            if (file.size < 1024 * 1024) {
                sizeText = (file.size / 1024).toFixed(1) + ' KB';
            } else {
                sizeText = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                fileNameEl.textContent = file.name;
                fileSizeEl.textContent = sizeText;

                // Toggle UI states
                uploadBox.classList.add('has-file');
                placeholder.style.display = 'none';
                previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        } else {
            // Revert to initial state if user cancels
            document.getElementById('uploadBox').classList.remove('has-file');
            document.getElementById('uploadPlaceholder').style.display = 'flex';
            document.getElementById('uploadPreviewContainer').style.display = 'none';
        }
    });
});
</script>
@endpush