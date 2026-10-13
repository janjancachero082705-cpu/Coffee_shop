@extends('portal.layouts.app')

@section('title', 'Edit Profile')

@section('content')

{{-- ═══ HEADER ═══ --}}
<div class="edit-header">
    <div class="edit-header-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
    </div>
    <div class="edit-header-title">Edit Your Profile</div>
    <div class="edit-header-sub">Update your store information below</div>
</div>

@if($errors->any())
    <div class="form-alert error">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 8v4M12 16h.01"/>
        </svg>
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('portal.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ═══ LOGO UPLOAD ═══ --}}
    <div class="logo-upload">
        <div class="logo-preview" id="logoPreview">
            @if($store->logo_url)
                <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}" id="logoPreviewImg">
            @else
                <span>{{ strtoupper(substr($store->store_name ?? 'ST', 0, 2)) }}</span>
            @endif
        </div>
        <div class="logo-info">
            <div class="logo-title">Store Logo</div>
            <div class="logo-hint">JPG, PNG o WEBP (max 2MB)</div>
            <div class="logo-buttons">
                <label for="logoInput" class="logo-btn upload">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Choose Photo
                </label>
                <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="previewLogo(event)">
                @if($store->logo)
                    <button type="button" class="logo-btn remove" onclick="removeLogo(event)">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        </svg>
                        Remove
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ STORE NAME ═══ --}}
    <div class="form-group">
        <label class="form-label">Store Name <span class="req">*</span></label>
        <input type="text" name="store_name" class="form-input" value="{{ old('store_name', $store->store_name) }}" required>
    </div>

    {{-- ═══ OWNER NAME ═══ --}}
    <div class="form-group">
        <label class="form-label">Owner Name <span class="req">*</span></label>
        <input type="text" name="owner_name" class="form-input" value="{{ old('owner_name', $store->owner_name) }}" required>
    </div>

    {{-- ═══ EMAIL ═══ --}}
    <div class="form-group">
        <label class="form-label">Email <span class="req">*</span></label>
        <input type="email" name="email" class="form-input" value="{{ old('email', $store->email) }}" required>
    </div>

    {{-- ═══ CONTACT ═══ --}}
    <div class="form-group">
        <label class="form-label">Contact Number <span class="req">*</span></label>
        <input type="text" name="contact_number" class="form-input" value="{{ old('contact_number', $store->contact_number) }}" required>
    </div>

    {{-- ═══ ADDRESS ═══ --}}
    <div class="form-group">
        <label class="form-label">Address <span class="req">*</span></label>
        <input type="text" name="address" class="form-input" value="{{ old('address', $store->address) }}" required>
    </div>

    {{-- ═══ BARANGAY + CITY ═══ --}}
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Barangay</label>
            <input type="text" name="barangay" class="form-input" value="{{ old('barangay', $store->barangay) }}">
        </div>
        <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-input" value="{{ old('city', $store->city) }}">
        </div>
    </div>

    {{-- ═══ ACTIONS ═══ --}}
    <div class="form-actions">
        <a href="{{ route('portal.profile') }}" class="p-btn p-btn-ghost">Cancel</a>
        <button type="submit" class="p-btn p-btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
            Save Changes
        </button>
    </div>
</form>

{{-- ═══ Hidden form for logo removal ═══ --}}
<form id="removeLogoForm" method="POST" action="{{ route('portal.profile.logo.remove') }}" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function previewLogo(event) {
        var file = event.target.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('logoPreview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        };
        reader.readAsDataURL(file);
    }
    function removeLogo(event) {
        event.preventDefault();
        if (!confirm('Remove store logo?')) return;
        document.getElementById('removeLogoForm').submit();
    }
</script>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   EDIT PROFILE — PREMIUM COFFEE THEME
   ═══════════════════════════════════════════════════════════ */

/* ═══ HEADER ═══ */
.edit-header {
    text-align: center;
    padding: 24px 16px 20px;
    margin-bottom: 20px;
}
.edit-header-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 14px;
    border-radius: 20px;
    background: linear-gradient(135deg, #2D1A11, #4E342E);
    color: #FFFFFF;
    display: grid;
    place-items: center;
    box-shadow: 0 12px 28px -10px rgba(45, 26, 17, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.edit-header-title {
    font-size: 22px;
    font-weight: 800;
    color: #2D1A11;
    letter-spacing: -0.03em;
    line-height: 1.2;
    margin-bottom: 4px;
}
.edit-header-sub {
    font-size: 13px;
    color: #8D6E63;
    font-weight: 600;
}

/* ═══ LOGO UPLOAD ═══ */
.logo-upload {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #FFFFFF;
    border: 1.5px solid #EFEBE9;
    border-radius: 18px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04);
}
.logo-preview {
    width: 72px;
    height: 72px;
    border-radius: 18px;
    background: linear-gradient(135deg, #2D1A11, #4E342E);
    color: #FFFFFF;
    display: grid;
    place-items: center;
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.02em;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid #EFEBE9;
    box-shadow: 0 8px 20px -8px rgba(45, 26, 17, 0.4);
}
.logo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.logo-info { flex: 1; min-width: 0; }
.logo-title {
    font-size: 14px;
    font-weight: 800;
    color: #2D1A11;
    margin-bottom: 2px;
}
.logo-hint {
    font-size: 11.5px;
    color: #8D6E63;
    font-weight: 600;
    margin-bottom: 10px;
}
.logo-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.logo-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 11.5px;
    font-weight: 800;
    font-family: inherit;
    cursor: pointer;
    border: 1.5px solid;
    transition: all 0.15s;
    white-space: nowrap;
}
.logo-btn:active { transform: scale(0.97); }
.logo-btn.upload {
    background: #2D1A11;
    color: #FFFFFF;
    border-color: #2D1A11;
}
.logo-btn.upload:hover { background: #4E342E; border-color: #4E342E; }
.logo-btn.remove {
    background: #FEF2F2;
    color: #B91C1C;
    border-color: #FECACA;
}
.logo-btn.remove:hover { background: #FEE2E2; border-color: #FCA5A5; }

/* ═══ FORM ═══ */
.form-group { margin-bottom: 16px; }
.form-label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    color: #2D1A11;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
}
.req { color: #D84315; font-weight: 800; }

.form-input {
    width: 100%;
    padding: 14px 16px;
    background: #FFFFFF;
    border: 1.5px solid #EFEBE9;
    border-radius: 14px;
    color: #2D1A11;
    font-size: 14px;
    font-family: inherit;
    font-weight: 600;
    outline: none;
    transition: all 0.2s;
    -webkit-appearance: none;
    appearance: none;
}
.form-input::placeholder { color: #B8A29A; font-weight: 500; }
.form-input:focus {
    border-color: #2D1A11;
    box-shadow: 0 0 0 4px rgba(45, 26, 17, 0.08);
}
.form-input:hover:not(:focus) { border-color: #D7CCC8; }

/* ═══ FORM ROW ═══ */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

/* ═══ ALERT ═══ */
.form-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-radius: 14px;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 16px;
    line-height: 1.4;
    border: 1.5px solid;
}
.form-alert svg { flex-shrink: 0; }
.form-alert.error {
    background: #FEF2F2;
    color: #B91C1C;
    border-color: #FECACA;
}
.form-alert.success {
    background: #F0FDF4;
    color: #15803D;
    border-color: #BBF7D0;
}

/* ═══ ACTIONS ═══ */
.form-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 4px;
}
.p-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 50px;
    padding: 0 18px;
    border-radius: 14px;
    font-family: inherit;
    font-size: 13.5px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
}
.p-btn:active { transform: scale(0.97); }
.p-btn-primary {
    background: linear-gradient(135deg, #D84315, #BF360C);
    color: #FFFFFF;
    box-shadow: 0 10px 24px -8px rgba(216, 67, 21, 0.6);
}
.p-btn-primary:hover {
    box-shadow: 0 12px 28px -8px rgba(216, 67, 21, 0.8);
    transform: translateY(-1px);
}
.p-btn-ghost {
    background: #FFFFFF;
    color: #2D1A11;
    border-color: #EFEBE9;
}
.p-btn-ghost:hover {
    background: #FDFBF7;
    border-color: #D7CCC8;
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 500px) {
    .form-row { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
    .edit-header { padding: 20px 12px 16px; }
    .edit-header-icon { width: 56px; height: 56px; border-radius: 18px; }
    .edit-header-icon svg { width: 22px; height: 22px; }
    .edit-header-title { font-size: 20px; }
    .logo-upload { padding: 14px; gap: 12px; border-radius: 16px; }
    .logo-preview { width: 64px; height: 64px; border-radius: 16px; font-size: 22px; }
    .logo-title { font-size: 13px; }
    .logo-hint { font-size: 11px; }
    .logo-btn { padding: 7px 10px; font-size: 11px; }
    .form-input { padding: 13px 15px; font-size: 13.5px; }
    .p-btn { min-height: 46px; font-size: 13px; }
}
</style>
@endpush