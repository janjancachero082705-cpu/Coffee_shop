@extends('portal.layouts.app')

@section('title', 'Edit Profile')

@section('content')

<div style="max-width: 600px; margin: 0 auto; background: #F4F6F8; min-height: 100vh; padding-bottom: 80px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #FFFFFF; border-bottom: 1px solid #EEEEEE;">
        <h1 style="font-size: 20px; font-weight: 900; color: #1A1A1A; margin: 0; letter-spacing: -0.5px;">Edit Profile</h1>
        <a href="{{ route('portal.profile') }}" style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #F3F4F6; border-radius: 50%; color: #1A1A1A; text-decoration: none;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
    </div>

    <div style="padding: 20px;">
        @if(session('success'))
            <div style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 700;">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('portal.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- PROFILE PICTURE -->
            <div style="background: #FFFFFF; border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 16px;">
                    <div id="avatar-preview" style="width: 72px; height: 72px; border-radius: 50%; border: 2px solid #E8A33D; overflow: hidden; flex-shrink: 0;">
                    <img src="{{ $currentStore->avatar_url ?? '' }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                    <div>
                        <div style="font-size: 15px; font-weight: 800; color: #1A1A1A; margin-bottom: 4px;">Profile Picture</div>
                        <div style="font-size: 12px; color: #6B7280;">Pumili ng larawan (Max 10MB)</div>
                    </div>
                </div>
                <input type="file" name="profile_image" id="profileInput" accept="image/*" style="width: 100%; padding: 12px; border: 1.5px dashed #E8A33D; border-radius: 12px; background: #FFF8EC; color: #1A1A1A; font-size: 13px; cursor: pointer;">
            </div>

            <!-- FIELDS -->
            <div style="background: #FFFFFF; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 20px;">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">Store Name <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="store_name" value="{{ old('store_name', $store->store_name) }}" required style="width: 100%; padding: 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">Owner Name <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="owner_name" value="{{ old('owner_name', $store->owner_name) }}" required style="width: 100%; padding: 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">Email <span style="color: #EF4444;">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $store->email) }}" required style="width: 100%; padding: 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">Contact Number <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $store->contact_number) }}" required style="width: 100%; padding: 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">Address <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="address" value="{{ old('address', $store->address) }}" required style="width: 100%; padding: 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <a href="{{ route('portal.profile') }}" style="display: flex; align-items: center; justify-content: center; padding: 16px; background: #FFFFFF; border: 1.5px solid #E5E7EB; color: #4B5563; font-size: 14px; font-weight: 800; border-radius: 16px; text-decoration: none;">Cancel</a>
                <button type="submit" style="display: flex; align-items: center; justify-content: center; padding: 16px; background: linear-gradient(135deg, #E8A33D, #D4901F); border: none; color: #FFFFFF; font-size: 14px; font-weight: 800; border-radius: 16px; cursor: pointer;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('profileInput').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(event) {
                var previewDiv = document.getElementById('avatar-preview');
                previewDiv.innerHTML = '<img src="' + event.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection