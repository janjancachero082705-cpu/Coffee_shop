@extends('layouts.admin')
@section('title', 'E-Wallet Settings')
@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    @if(session('success')) <div style="background:#F0FDF4; color:#15803D; padding:14px; border-radius:10px; margin-bottom:16px;">{{ session('success') }}</div> @endif
    
    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:20px;">
        <!-- ADD FORM -->
        <div style="background:#FFF; border:1px solid #EEE; border-radius:16px; padding:20px; align-self:start;">
            <h3 style="margin:0 0 16px; font-size:16px; font-weight:800;">Add E-Wallet</h3>
            <form method="POST" action="{{ route('admin.e-wallets.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:800; color:#555; text-transform:uppercase;">Name</label>
                    <input type="text" name="name" placeholder="e.g. GCash" required style="width:100%; padding:10px; border:1px solid #DDD; border-radius:8px; margin-top:4px;">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:800; color:#555; text-transform:uppercase;">Code (Lowercase)</label>
                    <select name="code" required style="width:100%; padding:10px; border:1px solid #DDD; border-radius:8px; margin-top:4px;">
                        <option value="gcash">gcash</option>
                        <option value="maya">maya</option>
                        <option value="grabpay">grabpay</option>
                        <option value="shopeepay">shopeepay</option>
                        <option value="other">other</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:800; color:#555; text-transform:uppercase;">Account Name</label>
                    <input type="text" name="account_name" style="width:100%; padding:10px; border:1px solid #DDD; border-radius:8px; margin-top:4px;">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:800; color:#555; text-transform:uppercase;">Account Number</label>
                    <input type="text" name="account_number" style="width:100%; padding:10px; border:1px solid #DDD; border-radius:8px; margin-top:4px;">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:800; color:#555; text-transform:uppercase;">QR Code Image</label>
                    <input type="file" name="qr_code" accept="image/*" style="width:100%; margin-top:4px;">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:800; color:#555; text-transform:uppercase;">Custom Logo (Optional)</label>
                    <input type="file" name="logo_path" accept="image/*" style="width:100%; margin-top:4px;">
                </div>
                <button type="submit" style="width:100%; padding:12px; background:#E8A33D; color:#FFF; font-weight:800; border:none; border-radius:8px; cursor:pointer;">Save E-Wallet</button>
            </form>
        </div>

        <!-- LIST -->
        <div style="background:#FFF; border:1px solid #EEE; border-radius:16px; padding:20px;">
            <h3 style="margin:0 0 16px; font-size:16px; font-weight:800;">Active E-Wallets</h3>
            @foreach($wallets as $w)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; border-bottom:1px solid #F5F5F5;">
                    <div>
                        <div style="font-weight:800; font-size:14px;">{{ $w->name }} <span style="font-size:10px; color:#888;">({{ $w->code }})</span></div>
                        <div style="font-size:12px; color:#666;">{{ $w->account_number ?? 'No number' }}</div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <form method="POST" action="{{ route('admin.e-wallets.toggle', $w->id) }}">@csrf <button type="submit" style="background:none; border:none; cursor:pointer; font-size:11px; font-weight:700; color:{{ $w->is_active ? '#15803D' : '#EF4444' }};">{{ $w->is_active ? 'ACTIVE' : 'INACTIVE' }}</button></form>
                        <form method="POST" action="{{ route('admin.e-wallets.destroy', $w->id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE') <button type="submit" style="background:none; border:none; cursor:pointer; color:#EF4444; font-size:12px;">✖</button></form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection