@extends('portal.layouts.app')

@section('title', 'My Account')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $totalOrders = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $totalDeliveries = \App\Models\DeliveryReceipt::where('store_id', $store->id)->count();
    $totalPaid = (float) \App\Models\ConsignmentPayment::where('store_id', $store->id)->where('verification_status', 'verified')->sum('amount');
    $memberSince = $store->created_at;
    $creditLimit = (float)($store->credit_limit ?? 0);
@endphp

<div style="max-width: 600px; margin: 0 auto; background: #F4F6F8; min-height: 100vh; padding-bottom: 80px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- TOP HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #FFFFFF; border-bottom: 1px solid #EEEEEE;">
        <h1 style="font-size: 20px; font-weight: 900; color: #1A1A1A; margin: 0; letter-spacing: -0.5px;">Account</h1>
        <a href="{{ route('portal.profile.edit') }}" style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #F3F4F6; border-radius: 50%; color: #1A1A1A; text-decoration: none;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        </a>
    </div>

    <!-- PROFILE INFO -->
    <div style="padding: 24px 20px; background: #FFFFFF; display: flex; align-items: center; gap: 16px;">
        <div style="width: 64px; height: 64px; border-radius: 50%; border: 2px solid #E8A33D; overflow: hidden; flex-shrink: 0;">
                <img src="{{ $currentStore->avatar_url ?? '' }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        <div style="flex: 1;">
            <h2 style="font-size: 22px; font-weight: 900; color: #1A1A1A; margin: 0 0 4px; letter-spacing: -0.5px; line-height: 1.2;">{{ $store->store_name ?? 'My Store' }}</h2>
            <div style="font-size: 13px; font-weight: 600; color: #6B7280;">{{ $store->owner_name ?? 'Owner' }}</div>
            <a href="{{ route('portal.profile.edit') }}" style="font-size: 12px; font-weight: 700; color: #E8A33D; text-decoration: none; margin-top: 6px; display: inline-block;">View profile</a>
        </div>
    </div>

    <!-- PROMO BANNER -->
    <div style="margin: 16px 20px; background: linear-gradient(135deg, #1A1A1A 0%, #333333 100%); border-radius: 16px; padding: 20px; color: #FFF; position: relative; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.15);">
        <div style="position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; background: rgba(232, 163, 61, 0.4); border-radius: 50%; filter: blur(25px);"></div>
        <h3 style="font-size: 16px; font-weight: 900; margin: 0 0 10px; line-height: 1.3; position: relative; z-index: 1;">Save on your future<br>orders with RYS</h3>
        <a href="{{ route('portal.payments.index') }}" style="font-size: 12px; font-weight: 800; color: #E8A33D; display: inline-flex; align-items: center; gap: 4px; position: relative; z-index: 1; text-decoration: none;">
            Learn more <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>

    <!-- WALLET -->
    <div style="padding: 0 20px; margin-bottom: 10px;">
        <h3 style="font-size: 14px; font-weight: 900; color: #1A1A1A; margin: 0;">Wallet</h3>
    </div>
    <div style="margin: 0 20px 20px; background: linear-gradient(135deg, #1A1A1A, #333); border-radius: 16px; padding: 20px; color: #FFF; box-shadow: 0 10px 25px rgba(0,0,0,0.15); position: relative; overflow: hidden;">
        <div style="font-size: 12px; font-weight: 700; color: #9CA3AF; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Available Credit</div>
        <div style="font-size: 28px; font-weight: 900; color: #E8A33D; letter-spacing: -0.5px; margin-bottom: 16px;">₱ {{ number_format($creditLimit, 2) }}</div>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; font-weight: 700; color: #9CA3AF;">
            <span>{{ $store->code ?? 'STORE-0000' }}</span>
            <span>RYS PAY</span>
        </div>
    </div>

    <!-- SUMMARY -->
    <div style="padding: 0 20px; margin-bottom: 10px;">
        <h3 style="font-size: 14px; font-weight: 900; color: #1A1A1A; margin: 0;">Summary</h3>
    </div>
    <div style="background: #FFFFFF; border-radius: 16px; margin: 0 20px 20px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="display: flex; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #F3F4F6;">
            <div style="color: #4B5563; font-weight: 600; font-size: 13px;">Total Orders</div>
            <div style="color: #1A1A1A; font-weight: 800; font-size: 14px;">{{ $totalOrders }}</div>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #F3F4F6;">
            <div style="color: #4B5563; font-weight: 600; font-size: 13px;">Total Deliveries</div>
            <div style="color: #1A1A1A; font-weight: 800; font-size: 14px;">{{ $totalDeliveries }}</div>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #F3F4F6;">
            <div style="color: #4B5563; font-weight: 600; font-size: 13px;">Total Paid</div>
            <div style="color: #10B981; font-weight: 800; font-size: 14px;">₱ {{ number_format($totalPaid, 2) }}</div>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 16px 20px;">
            <div style="color: #4B5563; font-weight: 600; font-size: 13px;">Member Since</div>
            <div style="color: #1A1A1A; font-weight: 800; font-size: 14px;">{{ $memberSince ? $memberSince->format('M d, Y') : '-' }}</div>
        </div>
    </div>

    <!-- GENERAL -->
    <div style="padding: 0 20px; margin-bottom: 10px;">
        <h3 style="font-size: 14px; font-weight: 900; color: #1A1A1A; margin: 0;">General</h3>
    </div>
    <div style="background: #FFFFFF; border-radius: 16px; margin: 0 20px 20px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <a href="{{ route('portal.profile.edit') }}" style="text-decoration: none; display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #F3F4F6;">
            <div style="display: flex; align-items: center; gap: 14px; color: #1A1A1A;">
                <div style="width: 32px; height: 32px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <span style="font-size: 14px; font-weight: 700;">Edit store profile</span>
            </div>
            <svg width="18" height="18" fill="none" stroke="#D1D5DB" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="{{ route('portal.profile.password') }}" style="text-decoration: none; display: flex; justify-content: space-between; align-items: center; padding: 16px 20px;">
            <div style="display: flex; align-items: center; gap: 14px; color: #1A1A1A;">
                <div style="width: 32px; height: 32px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <span style="font-size: 14px; font-weight: 700;">Change password</span>
            </div>
            <svg width="18" height="18" fill="none" stroke="#D1D5DB" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>

    <!-- LOGOUT -->
    <div style="padding: 0 20px 40px;">
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit" style="width: 100%; padding: 16px; background: #FFF0F0; border: 1px solid #FECACA; color: #DC2626; font-size: 14px; font-weight: 800; border-radius: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                Log out
            </button>
        </form>
    </div>

</div>
@endsection