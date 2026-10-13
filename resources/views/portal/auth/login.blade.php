<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#B8651B">
    <title>Store Login - RYS Farm Products Dealer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            color-scheme: light only;
            height: 100%;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #3E2723;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow: hidden;
            background: #FFFFFF;
        }

        /* ═══ SCROLLABLE SCREEN ═══ */
        .auth-screen {
            height: 100vh;
            height: 100dvh;
            width: 100%;
            background: #FFFFFF;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .auth-screen::-webkit-scrollbar { display: none; }

        /* ═══ HERO — STICKY (stays behind form) ═══ */
        .hero {
            position: sticky;
            top: 0;
            height: 60vh;
            height: 60dvh;
            min-height: 320px;
            max-height: 640px;
            background-image: url('{{ asset("images/coffee_bg.png") }}');
            background-size: cover;
            background-position: center;
            background-color: #2D1A11;
            overflow: hidden;
            z-index: 0;
            isolation: isolate;
        }

        /* Dark overlay */
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg,
                rgba(20, 10, 5, 0.5) 0%,
                rgba(20, 10, 5, 0.55) 45%,
                rgba(20, 10, 5, 0.75) 100%);
            z-index: 1;
            pointer-events: none;
        }

        /* Warm glow */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 40%, rgba(184, 101, 27, 0.2), transparent 55%),
                        radial-gradient(circle at 75% 70%, rgba(255, 183, 77, 0.12), transparent 60%);
            z-index: 1;
            pointer-events: none;
            animation: heroGlow 8s ease-in-out infinite;
        }
        @keyframes heroGlow {
            0%, 100% { opacity: 0.85; }
            50%      { opacity: 1; }
        }

        /* ═══ RYS LOGO — CENTERED + FLOATING + FADE ON SCROLL ═══ */
        .logo-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: #FDFBF7;
            padding: 8px;
            box-shadow:
                0 24px 56px -14px rgba(0, 0, 0, 0.75),
                0 0 0 5px rgba(201, 169, 97, 0.7),
                0 0 0 10px rgba(201, 169, 97, 0.22),
                0 0 0 1px rgba(255, 255, 255, 0.15) inset;
            display: grid;
            place-items: center;
            overflow: hidden;
            will-change: opacity, transform;
            /* Note: animation applied via JS */
        }
        .logo-badge::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, transparent, rgba(255, 183, 77, 0.45), transparent 60%);
            opacity: 0.6;
            animation: rotateRing 8s linear infinite;
            z-index: -1;
            pointer-events: none;
        }
        .logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
            display: block;
            background: #FDFBF7;
            position: relative;
            z-index: 1;
        }
        @keyframes rotateRing {
            to { transform: rotate(360deg); }
        }

        /* ═══ WHITE FORM — SLIDES OVER HERO ON SCROLL ═══ */
        .form-side {
            position: relative;
            z-index: 5;
            background: #FFFFFF;
            border-radius: 32px 32px 0 0;
            margin-top: -32px;
            padding: 32px 32px 40px;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            scrollbar-width: thin;
            box-shadow:
                0 -12px 32px -8px rgba(45, 26, 17, 0.25),
                0 -2px 8px -2px rgba(45, 26, 17, 0.08);
        }

        /* Handle bar */
        .form-side::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 44px;
            height: 4px;
            border-radius: 2px;
            background: #EFEBE9;
        }

        .form-inner {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-top: 16px;
        }

        /* Title */
        .form-title {
            font-size: 26px;
            font-weight: 800;
            color: #B8651B;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
            line-height: 1.15;
        }
        .form-subtitle {
            font-size: 13px;
            color: #8D6E63;
            font-weight: 500;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* Fields */
        .field {
            margin-bottom: 16px;
            position: relative;
        }
        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #5D4037;
            margin-bottom: 7px;
            letter-spacing: 0.01em;
            transition: color 0.2s;
        }
        .field:focus-within .field-label { color: #B8651B; }
        .field-input {
            width: 100%;
            padding: 15px 16px;
            background: #FFFFFF;
            border: 1.5px solid #E5DFD8;
            border-radius: 12px;
            color: #3E2723;
            font-size: 14px;
            font-family: inherit;
            font-weight: 500;
            outline: none;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            -webkit-appearance: none;
        }
        .field-input::placeholder { color: #B8A29A; font-weight: 400; }
        .field-input:hover { border-color: #C9BFB5; background: #FFFDFA; }
        .field-input:focus {
            border-color: #B8651B;
            background: #FFFFFF;
            box-shadow:
                0 0 0 4px rgba(184, 101, 27, 0.1),
                0 4px 12px -4px rgba(184, 101, 27, 0.15);
            transform: translateY(-1px);
        }
        .field-input.has-toggle { padding-right: 50px; }

        .pw-toggle {
            position: absolute;
            right: 8px;
            bottom: 8px;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: none;
            background: transparent;
            color: #A8A5A0;
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: all 0.15s;
            z-index: 2;
            padding: 0;
        }
        .pw-toggle:hover { background: #F5EFE4; color: #B8651B; }
        .pw-toggle:active { transform: scale(0.94); }

        /* Options */
        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            margin-top: 4px;
            gap: 10px;
            flex-wrap: wrap;
        }
        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            font-size: 13px;
            color: #4E342E;
            font-weight: 600;
            user-select: none;
        }
        .checkbox-wrap input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid #D7CCC8;
            background: #FFFFFF;
            cursor: pointer;
            position: relative;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }
        .checkbox-wrap input[type="checkbox"]:hover { border-color: #B8651B; }
        .checkbox-wrap input[type="checkbox"]:checked {
            background: linear-gradient(135deg, #C9782B, #B8651B);
            border-color: #B8651B;
            box-shadow: 0 4px 10px -3px rgba(184, 101, 27, 0.5);
        }
        .checkbox-wrap input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 5px; top: 1px;
            width: 4px; height: 9px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .forgot-link {
            font-size: 13px;
            color: #B8651B;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.15s;
        }
        .forgot-link:hover { color: #8B4513; text-decoration: underline; }

        /* Submit */
        .btn-submit {
            position: relative;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #C9782B 0%, #B8651B 50%, #9C5415 100%);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:
                0 10px 24px -6px rgba(184, 101, 27, 0.55),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 -1px 0 rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: -0.005em;
            overflow: hidden;
        }
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.65s ease;
        }
        .btn-submit:hover::before { left: 100%; }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow:
                0 16px 32px -8px rgba(184, 101, 27, 0.7),
                inset 0 1px 0 rgba(255, 255, 255, 0.3),
                inset 0 -1px 0 rgba(0, 0, 0, 0.1);
        }
        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 6px 16px -6px rgba(184, 101, 27, 0.55);
        }
        .btn-submit:disabled { opacity: 0.75; cursor: not-allowed; transform: none; }

        /* Register */
        .register-line {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #6D4C41;
            font-weight: 500;
        }
        .register-line a {
            color: #B8651B;
            font-weight: 800;
            text-decoration: none;
            margin-left: 4px;
            transition: color 0.15s;
            position: relative;
        }
        .register-line a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 1.5px;
            background: currentColor;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .register-line a:hover::after { transform: scaleX(1); transform-origin: left; }
        .register-line a:hover { color: #8B4513; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 28px 0 22px;
            color: #A89B92;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .divider::before,
        .divider::after { content: ''; flex: 1; height: 1px; }
        .divider::before { background: linear-gradient(90deg, transparent, #E5DFD8); }
        .divider::after  { background: linear-gradient(90deg, #E5DFD8, transparent); }

        /* Social */
        .social-row {
            display: flex;
            justify-content: center;
            gap: 16px;
            padding-bottom: 12px;
        }
        .social-btn {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-decoration: none;
            background: #FDFBF7;
            border: 1.5px solid #EFEBE9;
            border-radius: 12px;
            padding: 0;
        }
        .social-btn:hover {
            transform: translateY(-3px);
            background: #FFFFFF;
            border-color: #D7CCC8;
            box-shadow: 0 10px 20px -8px rgba(62, 39, 35, 0.25);
        }
        .social-btn:active { transform: translateY(-1px) scale(0.97); }
        .social-btn svg { display: block; }

        /* Alert */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 12px 14px;
            border-radius: 11px;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 18px;
            background: #FEF2F2;
            color: #B91C1C;
            border: 1.5px solid #FECACA;
            line-height: 1.45;
            animation: shakeIn 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }
        .alert svg { flex-shrink: 0; margin-top: 1px; }
        @keyframes shakeIn {
            0%, 100% { transform: translateX(0); }
            15% { transform: translateX(-8px); }
            30% { transform: translateX(7px); }
            45% { transform: translateX(-5px); }
            60% { transform: translateX(4px); }
            75% { transform: translateX(-2px); }
        }

        /* Spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* ═══ RESPONSIVE — MOBILE ═══ */
        @media (max-width: 480px) {
            .hero { height: 58vh; height: 58dvh; min-height: 280px; }
            .logo-badge {
                width: 130px;
                height: 130px;
                padding: 6px;
                box-shadow:
                    0 20px 44px -12px rgba(0, 0, 0, 0.7),
                    0 0 0 4px rgba(201, 169, 97, 0.7),
                    0 0 0 8px rgba(201, 169, 97, 0.22);
            }
            .form-side {
                border-radius: 28px 28px 0 0;
                margin-top: -28px;
                padding: 28px 22px 32px;
            }
            .form-side::before { width: 40px; height: 3px; top: 10px; }
            .form-title { font-size: 23px; margin-bottom: 6px; }
            .form-subtitle { font-size: 12.5px; margin-bottom: 20px; }
            .field-input { padding: 14px 15px; font-size: 13.5px; }
            .social-btn { width: 42px; height: 42px; }
        }

        /* ═══ RESPONSIVE — SHORT SCREENS ═══ */
        @media (max-height: 700px) {
            .hero { height: 55vh; height: 55dvh; min-height: 240px; }
            .logo-badge { width: 200px; height: 160px; padding: 8px; }
            .form-side {
                border-radius: 26px 26px 0 0;
                margin-top: -26px;
                padding: 26px 28px 28px;
            }
            .form-side::before { top: 9px; }
            .form-title { font-size: 22px; margin-bottom: 6px; }
            .form-subtitle { margin-bottom: 18px; font-size: 12.5px; }
            .field { margin-bottom: 13px; }
            .divider { margin: 22px 0 18px; }
        }

        /* ═══ RESPONSIVE — DESKTOP ═══ */
        @media (min-width: 768px) {
            .hero { height: 62vh; height: 62dvh; max-height: 720px; }
            .logo-badge {
                width: 320px;
                height: 260px;
                padding: 12px;
            }
            .form-side {
                border-radius: 40px 40px 0 0;
                margin-top: -40px;
                padding: 44px 48px 48px;
            }
            .form-side::before { width: 50px; height: 5px; top: 16px; }
            .form-inner { max-width: 480px; padding-top: 20px; }
            .form-title { font-size: 30px; margin-bottom: 8px; }
            .form-subtitle { font-size: 14px; margin-bottom: 28px; }
            .field-input { padding: 16px 18px; font-size: 15px; }
            .btn-submit { padding: 18px; font-size: 16px; }
            .social-btn { width: 50px; height: 50px; }
        }

        /* ═══ REDUCED MOTION ═══ */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

<div class="auth-screen" id="authScreen">

    {{-- ═══ HERO — STICKY BG + CENTERED LOGO ═══ --}}
    <div class="hero">
        <div class="logo-badge" id="logoBadge">
            <img src="{{ asset('images/logo.png') }}" alt="RYS Farm Products Dealer">
        </div>
    </div>

    {{-- ═══ WHITE FORM — SLIDES OVER HERO ═══ --}}
    <div class="form-side">
        <div class="form-inner">

            <h1 class="form-title">Sign in</h1>
            <p class="form-subtitle">Welcome back! Please enter your details to continue.</p>

            @if($errors->any())
                <div class="alert">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('portal.login.store') }}" id="storeLoginForm">
                @csrf

                <div class="field">
                    <label class="field-label" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="field-input" placeholder="Enter email address" required autofocus autocomplete="email">
                </div>

                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="field-input has-toggle" placeholder="Enter password" required autocomplete="current-password">
                    <button type="button" class="pw-toggle" id="pwToggle" title="Show password">
                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" id="eyeIcon">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <div class="options-row">
                    <label class="checkbox-wrap">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span id="btnText">Sign in</span>
                </button>

                <div class="register-line">
                    Don't have an account? <a href="{{ route('portal.register') }}">Sign up</a>
                </div>
            </form>

            <div class="divider">or continue with</div>

            <div class="social-row">
                <a href="#" class="social-btn" title="Facebook" aria-label="Facebook">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="#" class="social-btn" title="Twitter" aria-label="Twitter">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#1DA1F2">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                </a>
                <a href="#" class="social-btn" title="Google" aria-label="Google">
                    <svg width="22" height="22" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                </a>
                <a href="#" class="social-btn" title="Apple" aria-label="Apple">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#000000">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                </a>
            </div>

        </div>
    </div>

</div>

<script>
    (function() {
        'use strict';

        /* ═══ SCROLL-DRIVEN ANIMATION ═══ */
        var authScreen = document.getElementById('authScreen');
        var logo = document.getElementById('logoBadge');

        if (authScreen && logo) {
            var rafPending = false;
            var lastScrollY = 0;

            var update = function() {
                rafPending = false;
                var scrollY = authScreen.scrollTop;
                var maxScroll = authScreen.scrollHeight - authScreen.clientHeight;

                if (maxScroll <= 0) return;

                // Progress 0 → 1 as we scroll
                var progress = Math.min(scrollY / (maxScroll * 0.6), 1);

                // Ease-out quad for smoother feel
                var eased = 1 - Math.pow(1 - progress, 2);

                // Logo fades + scales down + drifts down slightly
                var opacity = Math.max(0, 1 - eased * 1.15);
                var scale = 1 - eased * 0.35;
                var driftY = eased * 24;

                logo.style.opacity = String(opacity);
                logo.style.transform = 'translate(-50%, -50%) translateY(' + driftY + 'px) scale(' + scale + ')';

                lastScrollY = scrollY;
            };

            authScreen.addEventListener('scroll', function() {
                if (!rafPending) {
                    rafPending = true;
                    requestAnimationFrame(update);
                }
            }, { passive: true });

            // Initial state
            update();
        }

        /* ═══ PASSWORD TOGGLE ═══ */
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

        /* ═══ FORM SUBMIT LOADING ═══ */
        var form = document.getElementById('storeLoginForm');
        var btn = document.getElementById('submitBtn');
        var btnText = document.getElementById('btnText');

        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btnText.textContent = 'Signing in...';
                var s = document.createElement('div');
                s.className = 'spinner';
                btn.appendChild(s);
            });
        }
    })();
</script>

</body>
</html>