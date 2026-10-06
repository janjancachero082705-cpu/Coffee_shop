@extends('layouts.admin')

@section('title', isset($store) ? 'Edit Store' : 'New Store')
@section('page-title', isset($store) ? 'Edit Store' : 'Add New Store')
@section('page-sub', isset($store) ? 'Update store details' : 'Register a consignment partner')

@push('styles')
<style>
    .form-layout { display: grid; grid-template-columns: 1fr 340px; gap: 16px; align-items: start; }
    @media (max-width: 1000px) { .form-layout { grid-template-columns: 1fr; } }
    .form-section { margin-bottom: 20px; }
    .section-head { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .section-num { width: 24px; height: 24px; border-radius: 50%; background: var(--accent-bg); border: 1px solid var(--accent-border); display: grid; place-items: center; font-size: 11px; font-weight: 800; color: var(--accent-light); flex-shrink: 0; }
    .section-title { font-size: 13px; font-weight: 700; }
    .section-sub { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
    @media (max-width: 600px) { .form-row, .form-row-3 { grid-template-columns: 1fr; } }
    .error-msg { color: var(--danger); font-size: 11px; margin-top: 4px; }
</style>
@endpush

@section('content')

@if($errors->any())
    <div class="alert alert-error">
        <div>
            @foreach($errors->all() as $error)
                <div>&#9888; {{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ isset($store) ? route('stores.update', $store) : route('stores.store') }}">
    @csrf
    @if(isset($store)) @method('PUT') @endif

    <div class="form-layout">
        <div>
            <div class="card">
                <div class="form-section">
                    <div class="section-head">
                        <div class="section-num">1</div>
                        <div>
                            <div class="section-title">Store Information</div>
                            <div class="section-sub">Basic details about the store</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div style="margin-bottom:12px;">
                            <label class="label">Store Name *</label>
                            <input type="text" name="store_name" class="input" value="{{ old('store_name', $store->store_name ?? '') }}" required placeholder="e.g. Juan Sari-sari Store">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="label">Owner Name *</label>
                            <input type="text" name="owner_name" class="input" value="{{ old('owner_name', $store->owner_name ?? '') }}" required placeholder="e.g. Juan Dela Cruz">
                        </div>
                    </div>

                    <div class="form-row">
                        <div style="margin-bottom:12px;">
                            <label class="label">Contact Number *</label>
                            <input type="text" name="contact_number" class="input" value="{{ old('contact_number', $store->contact_number ?? '') }}" required placeholder="09XX XXX XXXX">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="label">Email</label>
                            <input type="email" name="email" class="input" value="{{ old('email', $store->email ?? '') }}" placeholder="store@email.com">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-head">
                        <div class="section-num">2</div>
                        <div>
                            <div class="section-title">Address</div>
                            <div class="section-sub">Store location</div>
                        </div>
                    </div>

                    <div style="margin-bottom:12px;">
                        <label class="label">Full Address *</label>
                        <textarea name="address" rows="2" class="input" required placeholder="Street, building, landmark...">{{ old('address', $store->address ?? '') }}</textarea>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="label">Barangay</label>
                            <input type="text" name="barangay" class="input" value="{{ old('barangay', $store->barangay ?? '') }}">
                        </div>
                        <div>
                            <label class="label">City</label>
                            <input type="text" name="city" class="input" value="{{ old('city', $store->city ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="form-section" style="margin-bottom:0;">
                    <div class="section-head">
                        <div class="section-num">3</div>
                        <div>
                            <div class="section-title">Payment Terms</div>
                            <div class="section-sub">Credit limit and payment schedule</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div style="margin-bottom:12px;">
                            <label class="label">Credit Limit (&#8369;) *</label>
                            <input type="number" step="0.01" name="credit_limit" class="input" value="{{ old('credit_limit', $store->credit_limit ?? 0) }}" required min="0">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="label">Payment Terms *</label>
                            <select name="payment_terms" class="input" required>
                                <option value="weekly" @selected(old('payment_terms', $store->payment_terms ?? '') === 'weekly')>Weekly</option>
                                <option value="semi_monthly" @selected(old('payment_terms', $store->payment_terms ?? '') === 'semi_monthly')>Semi-Monthly</option>
                                <option value="monthly" @selected(old('payment_terms', $store->payment_terms ?? '') === 'monthly')>Monthly</option>
                                <option value="flexible" @selected(old('payment_terms', $store->payment_terms ?? '') === 'flexible')>Flexible</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="label">Payment Day</label>
                            <input type="text" name="payment_day" class="input" value="{{ old('payment_day', $store->payment_day ?? '') }}" placeholder="e.g. Monday or 15">
                        </div>
                        <div>
                            <label class="label">Status *</label>
                            <select name="status" class="input" required>
                                <option value="active" @selected(old('status', $store->status ?? 'active') === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $store->status ?? '') === 'inactive')>Inactive</option>
                                <option value="suspended" @selected(old('status', $store->status ?? '') === 'suspended')>Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div style="margin-bottom:12px;">
                    <label class="label">Notes</label>
                    <textarea name="notes" rows="6" class="input" placeholder="Additional notes...">{{ old('notes', $store->notes ?? '') }}</textarea>
                </div>
            </div>

            <div class="card" style="margin-bottom:0;">
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;padding:12px;">
                        {{ isset($store) ? 'Update Store' : 'Create Store' }}
                    </button>
                    <a href="{{ route('stores.index') }}" class="btn btn-ghost" style="padding:12px;">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection