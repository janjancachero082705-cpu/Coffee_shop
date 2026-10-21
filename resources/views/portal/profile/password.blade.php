@extends('portal.layouts.app')

@section('title', 'Change Password')

@section('content')

<div style="max-width: 600px; margin: 0 auto; background: #F4F6F8; min-height: 100vh; padding-bottom: 80px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #FFFFFF; border-bottom: 1px solid #EEEEEE;">
        <h1 style="font-size: 20px; font-weight: 900; color: #1A1A1A; margin: 0; letter-spacing: -0.5px;">Change Password</h1>
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

        <form method="POST" action="{{ route('portal.profile.password.update') }}">
            @csrf
            @method('PUT')

            <div style="background: #FFFFFF; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 20px;">
                
                <!-- Current Password -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">Current Password <span style="color: #EF4444;">*</span></label>
                    <div style="position: relative;">
                        <input type="password" name="current_password" id="current_password" required style="width: 100%; padding: 14px 40px 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                        <button type="button" onclick="togglePassword('current_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9CA3AF; display: flex; align-items: center; justify-content: center; padding: 0;">
                            <svg class="icon-eye" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">New Password <span style="color: #EF4444;">*</span></label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="new_password" required minlength="6" style="width: 100%; padding: 14px 40px 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                        <button type="button" onclick="togglePassword('new_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9CA3AF; display: flex; align-items: center; justify-content: center; padding: 0;">
                            <svg class="icon-eye" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #1A1A1A; text-transform: uppercase; margin-bottom: 8px;">Confirm New Password <span style="color: #EF4444;">*</span></label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirmation" id="confirm_password" required minlength="6" style="width: 100%; padding: 14px 40px 14px 16px; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1A1A1A; outline: none;">
                        <button type="button" onclick="togglePassword('confirm_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9CA3AF; display: flex; align-items: center; justify-content: center; padding: 0;">
                            <svg class="icon-eye" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; padding: 14px; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; color: #1E40AF; font-size: 12px; line-height: 1.5; font-weight: 500;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <div><strong>Tip:</strong> Use at least 6 characters with a mix of letters, numbers, and symbols.</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <a href="{{ route('portal.profile') }}" style="display: flex; align-items: center; justify-content: center; padding: 16px; background: #FFFFFF; border: 1.5px solid #E5E7EB; color: #4B5563; font-size: 14px; font-weight: 800; border-radius: 16px; text-decoration: none;">Cancel</a>
                <button type="submit" style="display: flex; align-items: center; justify-content: center; padding: 16px; background: linear-gradient(135deg, #E8A33D, #D4901F); border: none; color: #FFFFFF; font-size: 14px; font-weight: 800; border-radius: 16px; cursor: pointer;">Update Password</button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT FOR PASSWORD TOGGLE -->
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const iconEye = btn.querySelector('.icon-eye');
        const iconEyeOff = btn.querySelector('.icon-eye-off');
        
        if (input.type === 'password') {
            input.type = 'text';
            iconEye.style.display = 'none';
            iconEyeOff.style.display = 'block';
        } else {
            input.type = 'password';
            iconEye.style.display = 'block';
            iconEyeOff.style.display = 'none';
        }
    }
</script>
@endsection