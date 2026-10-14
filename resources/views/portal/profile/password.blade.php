@extends('portal.layouts.app')

@section('title', 'Change Password')

@section('content')

@if($errors->any())
    <div class="form-alert error">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
        {{ $errors->first() }}
    </div>
@endif

@if(session('success'))
    <div class="form-alert success">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('portal.profile.password.update') }}" id="passwordForm">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label class="form-label">Current Password <span class="req">*</span></label>
        <div class="input-wrap">
            <input type="password" name="current_password" id="current_password" class="form-input" placeholder="Enter current password" autocomplete="current-password" required>
            <button type="button" class="input-toggle" onclick="togglePassword('current_password', this)" aria-label="Toggle password">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">New Password <span class="req">*</span></label>
        <div class="input-wrap">
            <input type="password" name="password" id="new_password" class="form-input" placeholder="Minimum 6 characters" autocomplete="new-password" minlength="6" required oninput="checkPasswordStrength(this.value)">
            <button type="button" class="input-toggle" onclick="togglePassword('new_password', this)" aria-label="Toggle password">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        <div class="strength-bar" id="strengthBar">
            <div class="strength-segment"></div>
            <div class="strength-segment"></div>
            <div class="strength-segment"></div>
            <div class="strength-segment"></div>
        </div>
        <div class="strength-text" id="strengthText"></div>
    </div>

    <div class="form-group">
        <label class="form-label">Confirm New Password <span class="req">*</span></label>
        <div class="input-wrap">
            <input type="password" name="password_confirmation" id="confirm_password" class="form-input" placeholder="Repeat new password" autocomplete="new-password" minlength="6" required>
            <button type="button" class="input-toggle" onclick="togglePassword('confirm_password', this)" aria-label="Toggle password">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        <div class="match-text" id="matchText"></div>
    </div>

    <div class="info-note">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        <div><strong>Tip:</strong> Use at least 6 characters with a mix of letters, numbers, and symbols.</div>
    </div>

    <div class="form-actions">
        <a href="{{ route('portal.profile') }}" class="p-btn p-btn-ghost">Cancel</a>
        <button type="submit" class="p-btn p-btn-primary" id="submitBtn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            Update Password
        </button>
    </div>
</form>

<script>
    function togglePassword(id, btn) {
        var input = document.getElementById(id);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.classList.add('active');
        } else {
            input.type = 'password';
            btn.classList.remove('active');
        }
    }

    function checkPasswordStrength(val) {
        var bar = document.getElementById('strengthBar');
        var text = document.getElementById('strengthText');
        if (!bar || !text) return;
        var score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        var level = 0;
        if (val.length === 0) level = 0;
        else if (score <= 2) level = 1;
        else if (score === 3) level = 2;
        else if (score === 4) level = 3;
        else if (score >= 5) level = 4;
        bar.className = 'strength-bar';
        var segments = bar.querySelectorAll('.strength-segment');
        segments.forEach(function(s) { s.className = 'strength-segment'; });
        var labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        var colors = ['', 'weak', 'fair', 'good', 'strong'];
        for (var i = 0; i < level; i++) { segments[i].classList.add(colors[level]); }
        if (level > 0) {
            text.textContent = labels[level];
            text.className = 'strength-text ' + colors[level];
        } else {
            text.textContent = '';
            text.className = 'strength-text';
        }
        checkMatch();
    }

    function checkMatch() {
        var newPw = document.getElementById('new_password');
        var confirmPw = document.getElementById('confirm_password');
        var matchText = document.getElementById('matchText');
        if (!newPw || !confirmPw || !matchText) return;
        if (!confirmPw.value) { matchText.textContent = ''; matchText.className = 'match-text'; return; }
        if (newPw.value === confirmPw.value) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'match-text match-ok';
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'match-text match-error';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var confirmPw = document.getElementById('confirm_password');
        var newPw = document.getElementById('new_password');
        if (confirmPw) confirmPw.addEventListener('input', checkMatch);
        if (newPw) newPw.addEventListener('input', checkMatch);
        var form = document.getElementById('passwordForm');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            var newPw = document.getElementById('new_password');
            var confirmPw = document.getElementById('confirm_password');
            var submitBtn = document.getElementById('submitBtn');
            if (newPw.value !== confirmPw.value) { e.preventDefault(); alert('Passwords do not match'); return; }
            if (newPw.value.length < 6) { e.preventDefault(); alert('Password must be at least 6 characters'); return; }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Saving...';
            }
        });
    });
</script>

@endsection

@push('styles')
<style>
/* ═══ COMPACT CHANGE PASSWORD ═══ */
.form-group{margin-bottom:12px}
.form-label{display:block;font-size:11px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.req{color:#D84315;font-weight:800}
.form-input{width:100%;padding:11px 14px;background:#FFF;border:1.5px solid #EEE;border-radius:10px;color:#1A1A1A;font-size:13px;font-family:inherit;font-weight:600;outline:none;transition:all .2s;-webkit-appearance:none;appearance:none}
.form-input::placeholder{color:#B8A29A;font-weight:500}
.form-input:focus{border-color:#1A1A1A;box-shadow:0 0 0 3px rgba(26,26,26,.08)}
.form-input:hover:not(:focus){border-color:#D7CCC8}

.input-wrap{position:relative;display:flex;align-items:center}
.input-wrap .form-input{padding-right:44px}
.input-toggle{position:absolute;right:8px;width:30px;height:30px;border-radius:8px;background:#FAFAFA;border:1px solid #EEE;color:#757575;display:grid;place-items:center;cursor:pointer;transition:all .15s;flex-shrink:0;padding:0}
.input-toggle:hover,.input-toggle.active{background:#F0F0F0;border-color:#D7CCC8;color:#1A1A1A}

.strength-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:3px;margin-top:8px;height:4px}
.strength-segment{border-radius:2px;background:#EEE;transition:background .3s}
.strength-segment.weak{background:#EF4444}
.strength-segment.fair{background:#F59E0B}
.strength-segment.good{background:#3B82F6}
.strength-segment.strong{background:#22C55E}
.strength-text{font-size:10.5px;font-weight:800;margin-top:5px;letter-spacing:.06em;text-transform:uppercase}
.strength-text.weak{color:#DC2626}
.strength-text.fair{color:#D97706}
.strength-text.good{color:#2563EB}
.strength-text.strong{color:#16A34A}
.match-text{font-size:11px;font-weight:700;margin-top:5px;letter-spacing:.02em}
.match-text.match-ok{color:#16A34A}
.match-text.match-error{color:#DC2626}

.form-alert{display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;font-size:12.5px;font-weight:700;margin-bottom:14px;line-height:1.4;border:1.5px solid}
.form-alert.error{background:#FEF2F2;color:#B91C1C;border-color:#FECACA}
.form-alert.success{background:#F0FDF4;color:#15803D;border-color:#BBF7D0}

.info-note{display:flex;gap:10px;padding:12px 14px;background:#EFF6FF;border:1.5px solid #BFDBFE;border-radius:10px;font-size:11.5px;color:#1E40AF;line-height:1.45;margin-bottom:16px;font-weight:600}
.info-note svg{color:#2563EB;flex-shrink:0;margin-top:1px}
.info-note strong{color:#1E3A8A;font-weight:800}

.form-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px}
.p-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:44px;padding:0 16px;border-radius:10px;font-family:inherit;font-size:12.5px;font-weight:800;text-decoration:none;cursor:pointer;border:1.5px solid transparent;transition:all .2s}
.p-btn:active{transform:scale(.97)}
.p-btn-primary{background:linear-gradient(135deg,#D84315,#BF360C);color:#FFF;box-shadow:0 8px 20px -8px rgba(216,67,21,.6)}
.p-btn-primary:hover:not(:disabled){box-shadow:0 10px 24px -8px rgba(216,67,21,.8);transform:translateY(-1px)}
.p-btn-primary:disabled{opacity:.7;cursor:not-allowed}
.p-btn-ghost{background:#FFF;color:#1A1A1A;border-color:#EEE}
.p-btn-ghost:hover{background:#FAFAFA;border-color:#D7CCC8}

.edit-header{display:none !important}
.p-topbar{padding-top:10px !important;padding-bottom:10px !important}
.p-topbar-store-code{display:none !important}

@media(max-width:480px){
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
        topbarInner.innerHTML = '<div class="p-topbar-store-text"><div class="p-topbar-store-name" style="font-size:16px;font-weight:800;">Change Password</div></div><a href="{{ route('portal.profile') }}" class="acct-topbar-back" aria-label="Back"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>';
    }
});
</script>
<style>
.acct-topbar-back{display:grid;place-items:center;width:36px;height:36px;color:#1A1A1A;text-decoration:none;border-radius:50%;transition:background .15s;flex-shrink:0}
.acct-topbar-back:hover{background:#F5F5F5}
.acct-topbar-back svg{width:20px;height:20px}
</style>
@endpush
