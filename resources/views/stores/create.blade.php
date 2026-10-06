@extends('layouts.admin')

@section('title', 'New Store')
@section('subtitle', 'Add a consignment partner')

@section('actions')
    <a href="{{ route('stores.index') }}" class="btn btn-ghost btn-sm">â† Back</a>
@endsection

@section('content')

<form method="POST" action="{{ route('stores.store') }}" class="form-wrap">
    @csrf

    {{-- â•â•â• BASIC INFO â•â•â• --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Basic Information</div>
                <div class="card-sub">Store identity and owner details</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="field">
                <label class="label">Store Code <span class="req">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" class="input" placeholder="e.g. STR-001" required>
                @error('code')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Store Name <span class="req">*</span></label>
                <input type="text" name="store_name" value="{{ old('store_name') }}" class="input" placeholder="e.g. Juan's Sari-sari Store" required>
                @error('store_name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Owner Name <span class="req">*</span></label>
                <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="input" placeholder="Full name" required>
                @error('owner_name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Contact Number <span class="req">*</span></label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="input" placeholder="09XX XXX XXXX" required>
                @error('contact_number')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field field-full">
                <label class="label">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" class="input" placeholder="store@example.com">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- â•â•â• ADDRESS â•â•â• --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Address</div>
                <div class="card-sub">Location details</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="field field-full">
                <label class="label">Complete Address <span class="req">*</span></label>
                <textarea name="address" rows="2" class="input" placeholder="House number, street name..." required>{{ old('address') }}</textarea>
                @error('address')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Barangay</label>
                <input type="text" name="barangay" value="{{ old('barangay') }}" class="input" placeholder="Barangay name">
            </div>

            <div class="field">
                <label class="label">City / Municipality</label>
                <input type="text" name="city" value="{{ old('city') }}" class="input" placeholder="City name">
            </div>
        </div>
    </div>

    {{-- â•â•â• TERMS â•â•â• --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Payment Terms</div>
                <div class="card-sub">Credit and payment configuration</div>
            </div>
        </div>

        <div class="form-grid form-grid-3">
            <div class="field">
                <label class="label">Credit Limit</label>
                <div class="input-prefix">
                    <span class="prefix">â‚±</span>
                    <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit', 0) }}" class="input input-with-prefix">
                </div>
            </div>

            <div class="field">
                <label class="label">Payment Terms</label>
                <select name="payment_terms" class="input">
                    @foreach(['weekly'=>'Weekly','semi_monthly'=>'Semi-Monthly','monthly'=>'Monthly','flexible'=>'Flexible'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('payment_terms')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label class="label">Status</label>
                <select name="status" class="input">
                    @foreach(['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('status')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="field" style="margin-top: 16px;">
            <label class="label">Notes</label>
            <textarea name="notes" rows="2" class="input" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
        </div>
    </div>

    {{-- â•â•â• ACTIONS â•â•â• --}}
    <div class="form-actions">
        <a href="{{ route('stores.index') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            Save Store
        </button>
    </div>
</form>

@endsection

@push('styles')
<style>
    .form-wrap { max-width: 900px; }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .field-full { grid-column: 1 / -1; }
    .field { display: flex; flex-direction: column; }
    .req { color: #ef4444; font-weight: 700; }
    .field-error {
        font-size: 11px;
        color: #ef4444;
        margin-top: 5px;
        font-weight: 500;
    }
    .input-prefix {
        position: relative;
        display: flex;
        align-items: center;
    }
    .prefix {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
        pointer-events: none;
    }
    .input-with-prefix { padding-left: 28px; }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 8px;
    }
    @media (max-width: 700px) {
        .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endpush