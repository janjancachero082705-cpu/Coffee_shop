@extends('layouts.admin')

@section('title', 'Registration Details')
@section('subtitle', $store->store_name)

@section('actions')
    <a href="{{ route('store-registrations.index') }}" class="btn btn-ghost btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success" style="background:rgba(34,197,94,0.1); color:#22c55e; border:1px solid rgba(34,197,94,0.25); padding:14px 18px; border-radius:10px; margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

<div class="hero">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; position:relative; z-index:1;">
        <div>
            <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.12em; font-weight:700; margin-bottom:8px;">
                Store Registration
            </div>
            <div style="font-size:24px; font-weight:800; color:var(--text-primary); margin-bottom:6px;">
                {{ $store->store_name }}
            </div>
            <div style="font-size:13px; color:var(--text-secondary);">
                {{ $store->code }} - Registered {{ $store->created_at->format('M d, Y') }}
            </div>
            <div style="margin-top:14px;">
                @if($store->registration_status === 'pending')
                    <span class="badge badge-pending">Pending Approval</span>
                @elseif($store->registration_status === 'approved')
                    <span class="badge badge-approved">Approved</span>
                @else
                    <span class="badge badge-rejected">Rejected</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="detail-grid">

    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Store Information</div>
            </div>
            <div class="info-list">
                <div class="info-row"><span class="info-label">Store Name</span><span class="info-value">{{ $store->store_name }}</span></div>
                <div class="info-row"><span class="info-label">Owner Name</span><span class="info-value">{{ $store->owner_name }}</span></div>
                <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $store->email }}</span></div>
                <div class="info-row"><span class="info-label">Contact</span><span class="info-value">{{ $store->contact_number }}</span></div>
                <div class="info-row"><span class="info-label">Address</span><span class="info-value">{{ $store->address }}</span></div>
                <div class="info-row"><span class="info-label">Barangay</span><span class="info-value">{{ $store->barangay ?? '-' }}</span></div>
                <div class="info-row"><span class="info-label">City</span><span class="info-value">{{ $store->city ?? '-' }}</span></div>
                <div class="info-row"><span class="info-label">Registered</span><span class="info-value">{{ $store->created_at->format('M d, Y g:i A') }}</span></div>
            </div>
        </div>
    </div>

    <div>
        @if($store->registration_status === 'pending')
            {{-- APPROVE --}}
            <div class="card">
                <div class="card-header"><div class="card-title">Approve Registration</div></div>
                <form method="POST" action="{{ route('store-registrations.approve', $store) }}">
                    @csrf
                    <div style="display:flex; flex-direction:column; gap:14px;">
                        <div>
                            <label class="label">Credit Limit</label>
                            <input type="number" step="0.01" name="credit_limit" value="0" class="input" min="0">
                        </div>
                        <div>
                            <label class="label">Payment Terms</label>
                            <select name="payment_terms" class="input">
                                <option value="flexible">Flexible</option>
                                <option value="weekly">Weekly</option>
                                <option value="semi_monthly">Semi-Monthly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Notes (Optional)</label>
                            <textarea name="notes" rows="2" class="input"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:16px; justify-content:center;"
                            onclick="return confirm('Approve this store? Portal access will be enabled.');">
                        Approve & Enable Portal
                    </button>
                </form>
            </div>

            {{-- REJECT --}}
            <div class="card">
                <div class="card-header"><div class="card-title">Reject Registration</div></div>
                <form method="POST" action="{{ route('store-registrations.reject', $store) }}">
                    @csrf
                    <div>
                        <label class="label">Reason (Required)</label>
                        <textarea name="rejected_reason" rows="3" class="input" placeholder="Why reject this registration?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width:100%; margin-top:16px; justify-content:center; background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.25);"
                            onclick="return confirm('Reject this registration?');">
                        Reject Registration
                    </button>
                </form>
            </div>
        @else
            <div class="card">
                <div class="card-header"><div class="card-title">Status</div></div>
                <div class="info-list">
                    <div class="info-row">
                        <span class="info-label">Registration</span>
                        <span class="info-value">{{ ucfirst($store->registration_status) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Portal Access</span>
                        <span class="info-value">{{ $store->portal_enabled ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    @if($store->rejected_reason)
                        <div class="info-row">
                            <span class="info-label">Reason</span>
                            <span class="info-value">{{ $store->rejected_reason }}</span>
                        </div>
                    @endif
                </div>
                @if($store->registration_status === 'approved')
                    <a href="{{ route('stores.show', $store) }}" class="btn btn-primary" style="width:100%; margin-top:16px; justify-content:center;">
                        View Store
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .hero {
        background: linear-gradient(135deg, rgba(169, 120, 74, 0.15), rgba(20, 20, 26, 0.7));
        border: 1px solid rgba(169, 120, 74, 0.25);
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 18px;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 16px;
    }
    .info-list { display: flex; flex-direction: column; }
    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        font-size: 13px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); font-weight: 600; }
    .info-value { color: var(--text-primary); text-align: right; font-weight: 500; }
    .label { display: block; font-size: 11px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.06em; }
    .input { width: 100%; padding: 10px 12px; background: rgba(20, 20, 26, 0.6); border: 1px solid var(--border-strong); border-radius: 8px; color: var(--text-primary); font-size: 13px; font-family: inherit; outline: none; }
    .input:focus { border-color: #a9784a; box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.12); }
    .badge-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
    .badge-approved { background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
    .badge-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
    @media (max-width: 1000px) { .detail-grid { grid-template-columns: 1fr; } }
</style>
@endpush