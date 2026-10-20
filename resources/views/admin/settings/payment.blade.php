@extends('layouts.admin')
@section('title', 'GCash Settings')

@push('styles')
<style>
    .gs-wrap {
        max-width: 800px;
        margin: 0 auto;
        padding: 24px 20px;
    }
    .gs-card {
        background: rgba(34, 34, 44, 0.65);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 32px;
        box-shadow: 0 20px 40px -20px rgba(0,0,0,0.6);
    }
    .gs-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 1px dashed rgba(255,255,255,0.1);
    }
    .gs-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(232, 163, 61, 0.15);
        color: #E8A33D;
        display: grid;
        place-items: center;
        border: 1px solid rgba(232, 163, 61, 0.3);
    }
    .gs-title {
        font-size: 20px;
        font-weight: 800;
        color: #f5f3f0;
        letter-spacing: -0.02em;
    }
    .gs-subtitle {
        font-size: 13px;
        color: #a8a5a0;
        margin-top: 4px;
    }
    
    /* Form Grid */
    .gs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 600px) {
        .gs-grid { grid-template-columns: 1fr; }
    }

    .gs-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .gs-label {
        font-size: 12px;
        font-weight: 700;
        color: #d4d1cc;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .gs-input {
        width: 100%;
        padding: 14px 16px;
        background: rgba(20, 20, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #f5f3f0;
        font-size: 14px;
        font-weight: 500;
        outline: none;
        transition: all 0.2s;
    }
    .gs-input:focus {
        border-color: #E8A33D;
        box-shadow: 0 0 0 3px rgba(232, 163, 61, 0.15);
        background: rgba(20, 20, 26, 0.8);
    }

    /* QR Code Area */
    .gs-qr-section {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 20px;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
        margin-bottom: 28px;
    }
    .gs-qr-preview {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        background: #FFF;
        padding: 6px;
        border: 2px solid rgba(232, 163, 61, 0.4);
        flex-shrink: 0;
        box-shadow: 0 8px 20px -8px rgba(232, 163, 61, 0.3);
    }
    .gs-qr-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 8px;
    }
    .gs-qr-empty {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        background: rgba(255,255,255,0.05);
        border: 1px dashed rgba(255,255,255,0.2);
        display: grid;
        place-items: center;
        color: #7a7a85;
        font-size: 11px;
        text-align: center;
        padding: 10px;
        flex-shrink: 0;
    }
    .gs-qr-info {
        flex: 1;
    }
    .gs-qr-info h4 {
        font-size: 14px;
        font-weight: 800;
        color: #f5f3f0;
        margin-bottom: 4px;
    }
    .gs-qr-info p {
        font-size: 12px;
        color: #a8a5a0;
        line-height: 1.5;
        margin-bottom: 12px;
    }

    /* File Upload */
    .gs-file-wrap {
        position: relative;
    }
    .gs-file-input {
        position: absolute;
        left: 0; top: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    .gs-file-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #f5f3f0;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.2s;
    }
    .gs-file-wrap:hover .gs-file-btn {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(232, 163, 61, 0.5);
    }

    /* Save Button */
    .gs-submit {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #E8A33D, #D4901F);
        color: #1A1A1A;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 8px 20px -6px rgba(232, 163, 61, 0.5);
    }
    .gs-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -6px rgba(232, 163, 61, 0.7);
        filter: brightness(1.05);
    }
</style>
@endpush

@section('content')
<div class="gs-wrap">
    
    @if(session('success'))
        <div style="background: rgba(34, 197, 94, 0.15); border-left: 4px solid #22c55e; color: #4ade80; padding: 16px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; font-size: 13px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="gs-card">
        
        <div class="gs-header">
            <div class="gs-header-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div>
                <div class="gs-title">GCash Payment Settings</div>
                <div class="gs-subtitle">Manage the GCash account where stores will send their payments.</div>
            </div>
        </div>

        <form action="{{ route('admin.settings.gcash.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="gs-grid">
                <div class="gs-field">
                    <label class="gs-label">GCash Account Name</label>
                    <input type="text" name="gcash_name" class="gs-input" value="{{ $setting->gcash_name }}" placeholder="e.g. Justine Cachero" required>
                </div>
                
                <div class="gs-field">
                    <label class="gs-label">GCash Number</label>
                    <input type="text" name="gcash_number" class="gs-input" value="{{ $setting->gcash_number }}" placeholder="e.g. 0997-241-1036" required>
                </div>
            </div>

            <div class="gs-qr-section">
                @if($setting->gcash_qr_path)
                    <div class="gs-qr-preview">
                        <img src="{{ asset('storage/' . $setting->gcash_qr_path) }}" alt="QR Code">
                    </div>
                @else
                    <div class="gs-qr-empty">
                        No QR Code<br>Uploaded
                    </div>
                @endif
                
                <div class="gs-qr-info">
                    <h4>GCash QR Code Image</h4>
                    <p>This QR code will be displayed on the mobile app so customers can easily scan and pay.</p>
                    
                    <div class="gs-file-wrap">
                        <input type="file" name="gcash_qr" class="gs-file-input" accept="image/*">
                        <div class="gs-file-btn">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                            Upload New QR Code
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="gs-submit">
                Save Settings
            </button>
        </form>

    </div>
</div>
@endsection