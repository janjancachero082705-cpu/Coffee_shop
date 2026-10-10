@extends('portal.layouts.app')

@section('title', 'Change Password')

@section('content')

{{-- ═══ HEADER ═══ --}}
<div class="edit-header">
    <div class="edit-header-icon" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.05)); border-color: rgba(59, 130, 246, 0.3); color: #60a5fa;">
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
            <input type="password"
                   name="current_password"
                   id="current_password"
                   class="form-input"
                   placeholder="Enter your current password"
                   autocomplete="current-password"
                   required>
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
            <input type="password"
                   name="password"
                   id="new_password"
                   class="form-input"
                   placeholder="Minimum 6 characters"
                   autocomplete="new-password"
                   minlength="6"
                   required
                   oninput="checkPasswordStrength(this.value)">
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
            <input type="password"
                   name="password_confirmation"
                   id="confirm_password"
                   class="form-input"
                   placeholder="Repeat your new password"
                   autocomplete="new-password"
                   minlength="6"
                   required>
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
    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-wrap .form-input {
        padding-right: 46px;
    }
    .input-toggle {
        position: absolute;
        right: 12px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(169, 120, 74, 0.1);
        border: 1px solid rgba(169, 120, 74, 0.2);
        color: var(--text-muted);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .input-toggle:hover,
    .input-toggle.active {
        background: rgba(169, 120, 74, 0.2);
        border-color: rgba(169, 120, 74, 0.4);
        color: #c9a961;
    }
    .input-toggle svg { display: block; }

    .strength-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 4px;
        margin-top: 10px;
        height: 4px;
    }
    .strength-segment {
        border-radius: 2px;
        background: rgba(255, 255, 255, 0.06);
        transition: background 0.3s;
    }
    .strength-segment.weak { background: #ef4444; }
    .strength-segment.fair { background: #f59e0b; }
    .strength-segment.good { background: #3b82f6; }
    .strength-segment.strong { background: #22c55e; }

    .strength-text {
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .strength-text.weak { color: #ef4444; }
    .strength-text.fair { color: #f59e0b; }
    .strength-text.good { color: #3b82f6; }
    .strength-text.strong { color: #22c55e; }

    .match-text {
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
        letter-spacing: 0.02em;
    }
    .match-text.match-ok { color: #22c55e; }
    .match-text.match-error { color: #ef4444; }

    .info-note {
        display: flex;
        gap: 10px;
        padding: 12px 14px;
        background: rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        font-size: 12px;
        color: #93c5fd;
        line-height: 1.5;
        margin-bottom: 16px;
        animation: profileSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        animation-delay: 0.3s;
    }
    .info-note svg { color: #3b82f6; flex-shrink: 0; margin-top: 1px; }
    .info-note strong { color: #60a5fa; }

    .form-alert.success {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.3);
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ════════════════════════════════════════════════════════
       LIGHT MODE — EMERALD GREEN FORCE OVERRIDE
       Bisag unsang gold hardcoded → emerald
       ════════════════════════════════════════════════════════ */

    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #059669 !important;
    }

    /* Force emerald sa tanan accent colors sa light theme */
    html[data-theme="light"] .p-main *[style*="color: #c9a961"] {
        color: #10b981 !important;
    }

    /* Kill any gold shadows */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.5) !important;
    }

    /* Override gold gradient backgrounds */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"] {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }

    /* Force all spans/divs inside cards dark */
    html[data-theme="light"] .p-main,
    html[data-theme="light"] .p-main *:not([class*="badge"]):not([class*="status"]):not([class*="pill"]):not([class*="text-"]) {
        /* Fallback */
    }

    /* Headings */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f1e17 !important;
    }

    /* All text classes */
    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f1e17 !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #6b7f75 !important;
    }

    /* Inline hardcoded white → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f1e17 !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       LIGHT MODE — PURE SLATE FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Gold/green/brown hardcoded colors → slate */
    html[data-theme="light"] .p-main *[style*="#c9a961"],
    html[data-theme="light"] .p-main *[style*="#10b981"],
    html[data-theme="light"] .p-main *[style*="#059669"],
    html[data-theme="light"] .p-main *[style*="#a9784a"],
    html[data-theme="light"] .p-main *[style*="#8a5f36"],
    html[data-theme="light"] .p-main *[style*="#b8944d"] {
        color: #475569 !important;
    }

    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97"],
    html[data-theme="light"] .p-main *[style*="rgba(16, 185, 129"],
    html[data-theme="light"] .p-main *[style*="rgba(169, 120, 74"] {
        color: #475569 !important;
    }

    /* Gradient override */
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="light"] .p-main *[style*="linear-gradient(135deg, #10b981"] {
        background: linear-gradient(135deg, #475569, #334155) !important;
    }

    /* Gold shadow → slate shadow */
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.7)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.5)"],
    html[data-theme="light"] .p-main *[style*="rgba(201, 169, 97, 0.6)"] {
        box-shadow: 0 8px 20px -8px rgba(71, 85, 105, 0.4) !important;
    }

    /* All text — dark */
    html[data-theme="light"] .p-main h1,
    html[data-theme="light"] .p-main h2,
    html[data-theme="light"] .p-main h3,
    html[data-theme="light"] .p-main h4 {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="title"],
    html[data-theme="light"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="light"] .p-main [class*="name"],
    html[data-theme="light"] .p-main strong,
    html[data-theme="light"] .p-main b {
        color: #0f172a !important;
    }

    html[data-theme="light"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="light"] .p-main [class*="meta"],
    html[data-theme="light"] .p-main [class*="desc"],
    html[data-theme="light"] .p-main [class*="hint"] {
        color: #64748b !important;
    }

    /* Hardcoded white text → dark */
    html[data-theme="light"] .p-main *[style*="color: #fafafa"],
    html[data-theme="light"] .p-main *[style*="color:#fafafa"],
    html[data-theme="light"] .p-main *[style*="color: #f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color:#f5f3f0"],
    html[data-theme="light"] .p-main *[style*="color: white"],
    html[data-theme="light"] .p-main *[style*="color:#fff"],
    html[data-theme="light"] .p-main *[style*="color: #fff"],
    html[data-theme="light"] .p-main *[style*="color:#ffffff"],
    html[data-theme="light"] .p-main *[style*="color: #ffffff"] {
        color: #0f172a !important;
    }

    /* Dark backgrounds → white */
    html[data-theme="light"] .p-main *[style*="background: #1e1a16"],
    html[data-theme="light"] .p-main *[style*="background:#1e1a16"],
    html[data-theme="light"] .p-main *[style*="background: #0f0f14"],
    html[data-theme="light"] .p-main *[style*="background:#0f0f14"],
    html[data-theme="light"] .p-main *[style*="background: #15120f"],
    html[data-theme="light"] .p-main *[style*="background:#15120f"] {
        background: #ffffff !important;
    }
    /* ════════════════════════════════════════════════════════
       DARK MODE — PURE BLACK FORCE OVERRIDE
       ════════════════════════════════════════════════════════ */

    /* Kill all gold/emerald/purple accents sa dark mode */
    html[data-theme="dark"] .p-main *[style*="#c9a961"],
    html[data-theme="dark"] .p-main *[style*="#10b981"],
    html[data-theme="dark"] .p-main *[style*="#8b5cf6"],
    html[data-theme="dark"] .p-main *[style*="#a9784a"],
    html[data-theme="dark"] .p-main *[style*="#8a5f36"],
    html[data-theme="dark"] .p-main *[style*="#b8944d"],
    html[data-theme="dark"] .p-main *[style*="#ec4899"] {
        color: #ffffff !important;
    }

    /* Gradient → white */
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #c9a961"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #10b981"],
    html[data-theme="dark"] .p-main *[style*="linear-gradient(135deg, #8b5cf6"] {
        background: linear-gradient(135deg, #ffffff, #e5e5e5) !important;
    }

    /* All text light */
    html[data-theme="dark"] .p-main h1,
    html[data-theme="dark"] .p-main h2,
    html[data-theme="dark"] .p-main h3,
    html[data-theme="dark"] .p-main h4 {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="title"],
    html[data-theme="dark"] .p-main [class*="value"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="label"]:not([class*="badge"]),
    html[data-theme="dark"] .p-main [class*="name"],
    html[data-theme="dark"] .p-main strong,
    html[data-theme="dark"] .p-main b {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .p-main [class*="sub"]:not([class*="button"]):not([class*="btn"]),
    html[data-theme="dark"] .p-main [class*="meta"],
    html[data-theme="dark"] .p-main [class*="desc"],
    html[data-theme="dark"] .p-main [class*="hint"] {
        color: #a3a3a3 !important;
    }

    /* Any hardcoded dark text → white */
    html[data-theme="dark"] .p-main *[style*="color: #0f172a"],
    html[data-theme="dark"] .p-main *[style*="color:#0f172a"],
    html[data-theme="dark"] .p-main *[style*="color: #1a1a1f"],
    html[data-theme="dark"] .p-main *[style*="color: #000"],
    html[data-theme="dark"] .p-main *[style*="color:#000"],
    html[data-theme="dark"] .p-main *[style*="color: black"],
    html[data-theme="dark"] .p-main *[style*="color:black"] {
        color: #ffffff !important;
    }

    /* Any hardcoded light bg → dark */
    html[data-theme="dark"] .p-main *[style*="background: #ffffff"],
    html[data-theme="dark"] .p-main *[style*="background:#ffffff"],
    html[data-theme="dark"] .p-main *[style*="background: white"],
    html[data-theme="dark"] .p-main *[style*="background: #f8fafc"],
    html[data-theme="dark"] .p-main *[style*="background: #f1f5f9"] {
        background: #0a0a0a !important;
    }</style>
@endpush