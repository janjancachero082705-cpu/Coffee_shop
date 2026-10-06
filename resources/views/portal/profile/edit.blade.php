@extends('portal.layouts.app')

@section('title', 'Edit Profile')

@section('content')

{{-- HEADER --}}
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

    {{-- LOGO UPLOAD --}}
    <div class="logo-upload">
        <div class="logo-preview" id="logoPreview">
            @if($store->logo_url)
                <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}" id="logoPreviewImg">
            @else
                {{ strtoupper(substr($store->store_name ?? 'ST', 0, 2)) }}
            @endif
        </div>
        <div class="logo-info">
            <div class="logo-title">Store Logo</div>
            <div class="logo-hint">JPG, PNG or WEBP (max 2MB)</div>
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

    {{-- STORE NAME --}}
    <div class="form-group">
        <label class="form-label">Store Name <span class="req">*</span></label>
        <input type="text" name="store_name" class="form-input" value="{{ old('store_name', $store->store_name) }}" required>
    </div>

    {{-- OWNER NAME --}}
    <div class="form-group">
        <label class="form-label">Owner Name <span class="req">*</span></label>
        <input type="text" name="owner_name" class="form-input" value="{{ old('owner_name', $store->owner_name) }}" required>
    </div>

    {{-- EMAIL --}}
    <div class="form-group">
        <label class="form-label">Email <span class="req">*</span></label>
        <input type="email" name="email" class="form-input" value="{{ old('email', $store->email) }}" required>
    </div>

    {{-- CONTACT --}}
    <div class="form-group">
        <label class="form-label">Contact Number <span class="req">*</span></label>
        <input type="text" name="contact_number" class="form-input" value="{{ old('contact_number', $store->contact_number) }}" required>
    </div>

    {{-- ADDRESS --}}
    <div class="form-group">
        <label class="form-label">Address <span class="req">*</span></label>
        <input type="text" name="address" class="form-input" value="{{ old('address', $store->address) }}" required>
    </div>

    {{-- BARANGAY + CITY --}}
    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
            <label class="form-label">Barangay</label>
            <input type="text" name="barangay" class="form-input" value="{{ old('barangay', $store->barangay) }}">
        </div>
        <div>
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-input" value="{{ old('city', $store->city) }}">
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="form-actions">
        <button type="button" class="p-btn p-btn-ghost" onclick="closePortalModal()">
            Cancel
        </button>
        <button type="submit" class="p-btn p-btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
            Save Changes
        </button>
    </div>
</form>

{{-- Hidden form for logo removal --}}
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