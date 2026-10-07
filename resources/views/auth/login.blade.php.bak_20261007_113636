<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Coffee Beans Consignment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #f5f3f0;
            font-size: 13px;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        /* ============================================
           FULL PAGE COFFEE BACKGROUND
           ============================================ */
        body {
            background-color: #0a0a0e;
            background-image:
                linear-gradient(180deg, rgba(10, 10, 14, 0.35) 0%, rgba(10, 10, 14, 0.55) 100%),
                url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
        }

        /* Ambient glow */
        body::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.12), transparent 70%);
            filter: blur(80px);
            animation: glowPulse 8s ease-in-out infinite;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes glowPulse {
            0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.15); }
        }

        /* Floating particles */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(201, 169, 97, 0.5);
            box-shadow: 0 0 8px rgba(201, 169, 97, 0.8);
            animation: rise linear infinite;
        }

        @keyframes rise {
            0% { transform: translateY(100vh) scale(0.5); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) scale(1); opacity: 0; }
        }

        /* ============================================
           LOGIN CARD (RECTANGLE)
           ============================================ */
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            background: rgba(20, 20, 26, 0.4);
            backdrop-filter: blur(20px) saturate(1.6);
            -webkit-backdrop-filter: blur(20px) saturate(1.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow:
                0 40px 80px -20px rgba(0, 0, 0, 0.85),
                0 0 0 1px rgba(255, 255, 255, 0.03) inset;
            animation: cardIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ============================================
           BRAND
           ============================================ */
        .brand {
            text-align: center;
            margin-bottom: 26px;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #c9a961 0%, #8a5f36 100%);
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            color: #fff;
            box-shadow: 0 14px 28px -8px rgba(169, 120, 74, 0.7);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .brand-name .accent {
            background: linear-gradient(135deg, #c9a961, #e0b878);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-sub {
            font-size: 9.5px;
            color: #8a8782;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-weight: 700;
        }

        /* ============================================
           FIELDS
           ============================================ */
        .form-group { margin-bottom: 14px; }

        .label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #b8b5b0;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            color: #8a8782;
            pointer-events: none;
            transition: color 0.2s;
            display: flex;
            z-index: 2;
        }

        .input {
            width: 100%;
            padding: 12px 13px 12px 40px;
            background: rgba(15, 15, 20, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #f5f3f0;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }

        .input::placeholder { color: #8a8782; }

        .input:focus {
            border-color: #c9a961;
            background: rgba(15, 15, 20, 0.5);
            box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.15);
        }

        .input-wrap:focus-within .input-icon {
            color: #c9a961;
        }

        .input-with-toggle { padding-right: 40px; }

        .pw-toggle {
            position: absolute;
            right: 10px;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: #8a8782;
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: all 0.15s;
            z-index: 3;
        }

        .pw-toggle:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #c9a961;
        }

        /* ============================================
           OPTIONS
           ============================================ */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 12px;
            color: #b8b5b0;
            font-weight: 500;
            user-select: none;
        }

        .checkbox-wrap input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 15px;
            height: 15px;
            border-radius: 4px;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            background: rgba(15, 15, 20, 0.6);
            cursor: pointer;
            position: relative;
            transition: all 0.15s;
            flex-shrink: 0;
        }

        .checkbox-wrap input[type="checkbox"]:checked {
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            border-color: #c9a961;
        }

        .checkbox-wrap input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 1px;
            width: 4px;
            height: 8px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        /* ============================================
           SUBMIT
           ============================================ */
        .btn-submit {
            position: relative;
            width: 100%;
            padding: 13px;
            border-radius: 10px;
            background: linear-gradient(135deg, #c9a961 0%, #8a5f36 100%);
            color: #fff;
            font-size: 13.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before { left: 100%; }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -8px rgba(169, 120, 74, 1);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .form-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10.5px;
            color: #8a8782;
        }

        /* ============================================
           ALERT
           ============================================ */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 10px 12px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 16px;
            border: 1px solid;
            line-height: 1.4;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .alert svg { flex-shrink: 0; margin-top: 1px; }
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.35);
        }

        /* ============================================
           SPINNER
           ============================================ */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 13px;
            height: 13px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 480px) {
            .login-card { padding: 30px 24px; max-width: 100%; }
            .brand-icon { width: 50px; height: 50px; }
            .brand-name { font-size: 18px; }
        }
    </style>
</head>
<body>

{{-- PARTICLES --}}
<div class="particles" id="particles"></div>

{{-- RECTANGLE LOGIN CARD --}}
<div class="login-card">

    {{-- BRAND --}}
    <div class="brand">
        <div class="brand-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                <line x1="6" y1="1" x2="6" y2="4"/>
                <line x1="10" y1="1" x2="10" y2="4"/>
                <line x1="14" y1="1" x2="14" y2="4"/>
            </svg>
        </div>
        <div class="brand-name">Coffee <span class="accent">Beans</span></div>
        <div class="brand-sub">Consignment System</div>
    </div>

    {{-- ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 8v4M12 16h.01"/>
            </svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('login.store') }}" id="loginForm">
        @csrf

        <div class="form-group">
            <label class="label" for="email">Email</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="input" placeholder="you@example.com" required autofocus autocomplete="email">
            </div>
        </div>

        <div class="form-group">
            <label class="label" for="password">Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <input type="password" id="password" name="password" class="input input-with-toggle" placeholder="Enter your password" required autocomplete="current-password">
                <button type="button" class="pw-toggle" id="pwToggle" title="Show password">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" id="eyeIcon">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="form-options">
            <label class="checkbox-wrap">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            <span id="btnText">Sign In</span>
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" id="btnArrow">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    <div class="form-footer">
        &copy; {{ date('Y') }} Coffee Beans - All rights reserved
    </div>

</div>

<script>
    (function() {
        // Particles
        var particlesContainer = document.getElementById('particles');
        if (particlesContainer) {
            for (var i = 0; i < 30; i++) {
                var p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = (Math.random() * 15 + 10) + 's';
                p.style.animationDelay = (Math.random() * 15) + 's';
                p.style.opacity = Math.random() * 0.5 + 0.3;
                p.style.width = (Math.random() * 3 + 2) + 'px';
                p.style.height = p.style.width;
                particlesContainer.appendChild(p);
            }
        }

        // Password toggle
        var toggle = document.getElementById('pwToggle');
        var pwInput = document.getElementById('password');
        var eyeIcon = document.getElementById('eyeIcon');

        if (toggle && pwInput) {
            toggle.addEventListener('click', function() {
                var isPassword = pwInput.type === 'password';
                pwInput.type = isPassword ? 'text' : 'password';
                eyeIcon.innerHTML = isPassword
                    ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
                    : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            });
        }

        // Submit
        var form = document.getElementById('loginForm');
        var submitBtn = document.getElementById('submitBtn');
        var btnText = document.getElementById('btnText');
        var btnArrow = document.getElementById('btnArrow');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                btnText.textContent = 'Signing in...';
                btnArrow.style.display = 'none';
                var spinner = document.createElement('div');
                spinner.className = 'spinner';
                submitBtn.appendChild(spinner);
            });
        }
    })();
</script>

</body>
</html>