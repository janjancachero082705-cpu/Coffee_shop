@extends('portal.layouts.app')

@section('title', 'Edit Profile')

@section('content')

@if($errors->any())
    <div class="form-alert error">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('portal.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Choose Photo
                </label>
                <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="previewLogo(event)">
                @if($store->logo)
                    <button type="button" class="logo-btn remove" onclick="removeLogo(event)">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                        Remove
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Store Name <span class="req">*</span></label>
        <input type="text" name="store_name" class="form-input" value="{{ old('store_name', $store->store_name) }}" required>
    </div>

    <div class="form-group">
        <label class="form-label">Owner Name <span class="req">*</span></label>
        <input type="text" name="owner_name" class="form-input" value="{{ old('owner_name', $store->owner_name) }}" required>
    </div>

    <div class="form-group">
        <label class="form-label">Email <span class="req">*</span></label>
        <input type="email" name="email" class="form-input" value="{{ old('email', $store->email) }}" required>
    </div>

    <div class="form-group">
        <label class="form-label">Contact Number <span class="req">*</span></label>
        <input type="text" name="contact_number" class="form-input" value="{{ old('contact_number', $store->contact_number) }}" required>
    </div>

    <div class="form-group">
        <label class="form-label">Address <span class="req">*</span></label>
        <input type="text" name="address" class="form-input" value="{{ old('address', $store->address) }}" required>
    </div>

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

    <div class="form-actions">
        <a href="{{ route('portal.profile') }}" class="p-btn p-btn-ghost">Cancel</a>
        <button type="submit" class="p-btn p-btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            Save Changes
        </button>
    </div>
</form>

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
/* ═══ COMPACT EDIT PROFILE ═══ */
.logo-upload{display:flex;align-items:center;gap:12px;padding:12px;background:#FFF;border:1px solid #EEE;border-radius:12px;margin-bottom:16px}
.logo-preview{width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,#2D1A11,#4E342E);color:#FFF;display:grid;place-items:center;font-size:18px;font-weight:800;letter-spacing:-.02em;overflow:hidden;flex-shrink:0;border:1px solid #EEE}
.logo-preview img{width:100%;height:100%;object-fit:cover}
.logo-info{flex:1;min-width:0}
.logo-title{font-size:12.5px;font-weight:800;color:#1A1A1A;margin-bottom:1px}
.logo-hint{font-size:10.5px;color:#757575;font-weight:500;margin-bottom:8px}
.logo-buttons{display:flex;gap:6px;flex-wrap:wrap}
.logo-btn{display:inline-flex;align-items:center;gap:4px;padding:6px 10px;border-radius:8px;font-size:11px;font-weight:800;font-family:inherit;cursor:pointer;border:1.5px solid;transition:all .15s;white-space:nowrap}
.logo-btn:active{transform:scale(.97)}
.logo-btn.upload{background:#1A1A1A;color:#FFF;border-color:#1A1A1A}
.logo-btn.upload:hover{background:#333;border-color:#333}
.logo-btn.remove{background:#FEF2F2;color:#B91C1C;border-color:#FECACA}
.logo-btn.remove:hover{background:#FEE2E2;border-color:#FCA5A5}

.form-group{margin-bottom:12px}
.form-label{display:block;font-size:11px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.req{color:#D84315;font-weight:800}
.form-input{width:100%;padding:11px 14px;background:#FFF;border:1.5px solid #EEE;border-radius:10px;color:#1A1A1A;font-size:13px;font-family:inherit;font-weight:600;outline:none;transition:all .2s;-webkit-appearance:none;appearance:none}
.form-input::placeholder{color:#B8A29A;font-weight:500}
.form-input:focus{border-color:#1A1A1A;box-shadow:0 0 0 3px rgba(26,26,26,.08)}
.form-input:hover:not(:focus){border-color:#D7CCC8}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.form-alert{display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;font-size:12.5px;font-weight:700;margin-bottom:14px;line-height:1.4;border:1.5px solid}
.form-alert.error{background:#FEF2F2;color:#B91C1C;border-color:#FECACA}
.form-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px}
.p-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:44px;padding:0 16px;border-radius:10px;font-family:inherit;font-size:12.5px;font-weight:800;text-decoration:none;cursor:pointer;border:1.5px solid transparent;transition:all .2s}
.p-btn:active{transform:scale(.97)}
.p-btn-primary{background:linear-gradient(135deg,#D84315,#BF360C);color:#FFF;box-shadow:0 8px 20px -8px rgba(216,67,21,.6)}
.p-btn-primary:hover{box-shadow:0 10px 24px -8px rgba(216,67,21,.8);transform:translateY(-1px)}
.p-btn-ghost{background:#FFF;color:#1A1A1A;border-color:#EEE}
.p-btn-ghost:hover{background:#FAFAFA;border-color:#D7CCC8}

/* Hide old header */
.edit-header{display:none !important}

/* Compact topbar */
.p-topbar{padding-top:10px !important;padding-bottom:10px !important}
.p-topbar-store-code{display:none !important}

@media(max-width:500px){.form-row{grid-template-columns:1fr}}
@media(max-width:480px){
.logo-preview{width:52px;height:52px;font-size:17px}
.form-input{padding:10px 12px;font-size:12.5px}
.p-btn{min-height:42px;font-size:12px}
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var topbarInner = document.querySelector('.p-topbar-inner');
    if (topbarInner) {
        topbarInner.innerHTML = '<div class="p-topbar-store-text"><div class="p-topbar-store-name" style="font-size:16px;font-weight:800;">Edit Profile</div></div><a href="{{ route('portal.profile') }}" class="acct-topbar-back" aria-label="Back"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>';
    }
});
</script>
<style>
.acct-topbar-back{display:grid;place-items:center;width:36px;height:36px;color:#1A1A1A;text-decoration:none;border-radius:50%;transition:background .15s;flex-shrink:0}
.acct-topbar-back:hover{background:#F5F5F5}
.acct-topbar-back svg{width:20px;height:20px}
</style>
@endpush
