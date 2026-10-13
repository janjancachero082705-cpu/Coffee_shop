@extends('portal.layouts.app')

@section('title', 'Change Password')

@section('content')

{{-- ═══ HEADER ═══ --}}
<div class="edit-header">
    <div class="edit-header-icon blue">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0110 0v4"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
        </svg>
    </div>
    <div class="edit-header-title">Change Password</div>
    <div class="edit-header-sub">Keep your account secure</div>
</div>

{{-- ═══ ERRORS ═══ --}}
@if($errors->any())
    <div class="form-alert error">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 8v4M12 16h.01"/>
        </svg>
        {{ $errors->first() }}
    </div>
@endif

{{-- ═══ SUCCESS ═══ --}}
@if(session('success'))
    <div class="form-alert success">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M5 12l5 5L20 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('portal.profile.password.update') }}" id="passwordForm">
    @csrf
    @method('PUT')

    {{-- ═══ CURRENT PASSWORD ═══ --}}
    <div class="form-group">
        <label class="form-label">Current Password <span class="req">*</span></label>
        <div class="input-wrap">
            <input type="password" name="current_password" id="current_password" class="form-input" placeholder="Enter your current password" autocomplete="current-password" required>
            <button type="button" class="input-toggle" onclick="togglePassword('current_password', this)" aria-label="Toggle password">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ═══ NEW PASSWORD ═══ --}}
    <div class="form-group">
        <label class="form-label">New Password <span class="req">*</span></label>
        <div class="input-wrap">
            <input type="password" name="password" id="new_password" class="form-input" placeholder="Minimum 6 characters" autocomplete="new-password" minlength="6" required oninput="checkPasswordStrength(this.value)">
            <button type="button" class="input-toggle" onclick="togglePassword('new_password', this)" aria-label="Toggle password">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>

        {{-- Strength meter --}}
        <div class="strength-bar" id="strengthBar">
            <div class="strength-segment"></div>
            <div class="strength-segment"></div>
            <div class="strength-segment"></div>
            <div class="strength-segment"></div>
        </div>
        <div class="strength-text" id="strengthText"></div>
    </div>

    {{-- ═══ CONFIRM ═══ --}}
    <div class="form-group">
        <label class="form-label">Confirm New Password <span class="req">*</span></label>
        <div class="input-wrap">
            <input type="password" name="password_confirmation" id="confirm_password" class="form-input" placeholder="Repeat your new password" autocomplete="new-password" minlength="6" required>
            <button type="button" class="input-toggle" onclick="togglePassword('confirm_password', this)" aria-label="Toggle password">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
        <div class="match-text" id="matchText"></div>
    </div>

    {{-- ═══ INFO NOTE ═══ --}}
    <div class="info-note">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 16v-4M12 8h.01"/>
        </svg>
        <div>
            <strong>Password tips:</strong> Use at least 6 characters with a mix of letters, numbers, and symbols for better security.
        </div>
    </div>

    {{-- ═══ ACTIONS ═══ --}}
    <div class="form-actions">
        <a href="{{ route('portal.profile') }}" class="p-btn p-btn-ghost">Cancel</a>
        <button type="submit" class="p-btn p-btn-primary" id="submitBtn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
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

        for (var i = 0; i < level; i++) {
            segments[i].classList.add(colors[level]);
        }

        if (level > 0) {
            bar.classList.add('level-' + level);
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

        if (!confirmPw.value) {
            matchText.textContent = '';
            matchText.className = 'match-text';
            return;
        }
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

            if (newPw.value !== confirmPw.value) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
            if (newPw.value.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters');
                return;
            }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation:spin 0.8s linear infinite;"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Saving...';
            }
        });
    });
</script>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   CHANGE PASSWORD — PREMIUM COFFEE THEME
   ═══════════════════════════════════════════════════════════ */

/* ═══ HEADER ═══ */
.edit-header {
    text-align: center;
    padding: 24px 16px 20px;
    margin-bottom: 20px;
}
.edit-header-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 14px;
    border-radius: 20px;
    background: linear-gradient(135deg, #2D1A11, #4E342E);
    color: #FFFFFF;
    display: grid;
    place-items: center;
    box-shadow: 0 12px 28px -10px rgba(45, 26, 17, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.edit-header-icon.blue {
    background: linear-gradient(135deg, #1E3A8A, #2563EB);
    box-shadow: 0 12px 28px -10px rgba(37, 99, 235, 0.5);
}
.edit-header-title {
    font-size: 22px;
    font-weight: 800;
    color: #2D1A11;
    letter-spacing: -0.03em;
    line-height: 1.2;
    margin-bottom: 4px;
}
.edit-header-sub {
    font-size: 13px;
    color: #8D6E63;
    font-weight: 600;
}

/* ═══ FORM ═══ */
.form-group { margin-bottom: 16px; }
.form-label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    color: #2D1A11;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
}
.req { color: #D84315; font-weight: 800; }

.form-input {
    width: 100%;
    padding: 14px 16px;
    background: #FFFFFF;
    border: 1.5px solid #EFEBE9;
    border-radius: 14px;
    color: #2D1A11;
    font-size: 14px;
    font-family: inherit;
    font-weight: 600;
    outline: none;
    transition: all 0.2s;
    -webkit-appearance: none;
    appearance: none;
}
.form-input::placeholder { color: #B8A29A; font-weight: 500; }
.form-input:focus {
    border-color: #2D1A11;
    box-shadow: 0 0 0 4px rgba(45, 26, 17, 0.08);
    background: #FFFFFF;
}
.form-input:hover:not(:focus) { border-color: #D7CCC8; }

/* Input wrapper + toggle */
.input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.input-wrap .form-input {
    padding-right: 52px;
}
.input-toggle {
    position: absolute;
    right: 10px;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #FDFBF7;
    border: 1px solid #EFEBE9;
    color: #8D6E63;
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 0.15s;
    flex-shrink: 0;
    padding: 0;
}
.input-toggle:hover,
.input-toggle.active {
    background: #EFEBE9;
    border-color: #D7CCC8;
    color: #2D1A11;
}
.input-toggle svg { display: block; }

/* ═══ STRENGTH METER ═══ */
.strength-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 4px;
    margin-top: 10px;
    height: 5px;
}
.strength-segment {
    border-radius: 3px;
    background: #EFEBE9;
    transition: background 0.3s;
}
.strength-segment.weak { background: #EF4444; }
.strength-segment.fair { background: #F59E0B; }
.strength-segment.good { background: #3B82F6; }
.strength-segment.strong { background: #22C55E; }

.strength-text {
    font-size: 11px;
    font-weight: 800;
    margin-top: 6px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.strength-text.weak { color: #DC2626; }
.strength-text.fair { color: #D97706; }
.strength-text.good { color: #2563EB; }
.strength-text.strong { color: #16A34A; }

.match-text {
    font-size: 11.5px;
    font-weight: 700;
    margin-top: 6px;
    letter-spacing: 0.02em;
}
.match-text.match-ok { color: #16A34A; }
.match-text.match-error { color: #DC2626; }

/* ═══ ALERTS ═══ */
.form-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-radius: 14px;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 16px;
    line-height: 1.4;
    border: 1.5px solid;
}
.form-alert svg { flex-shrink: 0; }
.form-alert.error {
    background: #FEF2F2;
    color: #B91C1C;
    border-color: #FECACA;
}
.form-alert.success {
    background: #F0FDF4;
    color: #15803D;
    border-color: #BBF7D0;
}

/* ═══ INFO NOTE ═══ */
.info-note {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    background: #EFF6FF;
    border: 1.5px solid #BFDBFE;
    border-radius: 14px;
    font-size: 12.5px;
    color: #1E40AF;
    line-height: 1.5;
    margin-bottom: 20px;
    font-weight: 600;
}
.info-note svg { color: #2563EB; flex-shrink: 0; margin-top: 1px; }
.info-note strong { color: #1E3A8A; font-weight: 800; }

/* ═══ ACTIONS ═══ */
.form-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    padding-top: 4px;
}
.p-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 50px;
    padding: 0 18px;
    border-radius: 14px;
    font-family: inherit;
    font-size: 13.5px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
}
.p-btn:active { transform: scale(0.97); }
.p-btn-primary {
    background: linear-gradient(135deg, #D84315, #BF360C);
    color: #FFFFFF;
    box-shadow: 0 10px 24px -8px rgba(216, 67, 21, 0.6);
}
.p-btn-primary:hover:not(:disabled) {
    box-shadow: 0 12px 28px -8px rgba(216, 67, 21, 0.8);
    transform: translateY(-1px);
}
.p-btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
.p-btn-ghost {
    background: #FFFFFF;
    color: #2D1A11;
    border-color: #EFEBE9;
}
.p-btn-ghost:hover {
    background: #FDFBF7;
    border-color: #D7CCC8;
}

/* ═══ SPINNER ═══ */
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 480px) {
    .edit-header { padding: 20px 12px 16px; }
    .edit-header-icon { width: 56px; height: 56px; border-radius: 18px; }
    .edit-header-icon svg { width: 22px; height: 22px; }
    .edit-header-title { font-size: 20px; }
    .form-input { padding: 13px 15px; font-size: 13.5px; }
    .form-actions { min-height: 46px; }
    .p-btn { min-height: 46px; font-size: 13px; }
}
</style>
@endpush