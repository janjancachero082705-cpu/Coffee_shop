<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#B8651B">
    <title>Welcome - RYS Farm Products Dealer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            color-scheme: light only;
            background-color: #FFFFFF !important;
            height: 100%;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #3E2723;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow: hidden;
            background: #FFFFFF;
            position: relative;
        }

        /* ═══ HERO ═══ */
        .hero {
            position: fixed;
            inset: 0;
            background-image: url('{{ asset("images/coffee_bg.png") }}');
            background-size: cover;
            background-position: center;
            background-color: #2D1A11;
            overflow: hidden;
            z-index: 1;
            isolation: isolate;
        }
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg,
                rgba(20, 10, 5, 0.5) 0%,
                rgba(20, 10, 5, 0.6) 50%,
                rgba(20, 10, 5, 0.8) 100%);
            z-index: 1;
            pointer-events: none;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 40%, rgba(184, 101, 27, 0.22), transparent 55%),
                        radial-gradient(circle at 75% 70%, rgba(255, 183, 77, 0.14), transparent 60%);
            z-index: 1;
            pointer-events: none;
            animation: heroGlow 8s ease-in-out infinite;
        }
        @keyframes heroGlow {
            0%, 100% { opacity: 0.85; }
            50%      { opacity: 1; }
        }

        .hero-content {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            text-align: center;
            will-change: opacity, transform;
            transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ═══ BIG LOGO ═══ */
        .logo-badge {
            position: relative;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: #FDFBF7;
            padding: 12px;
            box-shadow:
                0 32px 72px -18px rgba(0, 0, 0, 0.85),
                0 0 0 6px rgba(201, 169, 97, 0.75),
                0 0 0 12px rgba(201, 169, 97, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.18) inset;
            display: grid;
            place-items: center;
            overflow: hidden;
            margin-bottom: 28px;
            animation: logoFloat 4.5s ease-in-out infinite;
        }
        .logo-badge::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, transparent, rgba(255, 183, 77, 0.5), transparent 60%);
            opacity: 0.7;
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
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
        @keyframes rotateRing {
            to { transform: rotate(360deg); }
        }

        .welcome-title {
            font-size: clamp(24px, 5vw, 34px);
            font-weight: 900;
            color: #FFFFFF;
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin-bottom: 8px;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.6);
        }
        .welcome-title .accent {
            background: linear-gradient(135deg, #FFB74D, #F57C00, #B8651B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
        }
        .welcome-sub {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            line-height: 1.5;
            max-width: 340px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        /* ═══ SWIPE HINT ═══ */
        .swipe-hint {
            position: fixed;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            z-index: 3;
            pointer-events: none;
            animation: swipeBounce 2.2s ease-in-out infinite;
            transition: opacity 0.3s ease;
        }
        .swipe-hint svg { opacity: 0.95; }
        @keyframes swipeBounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%      { transform: translateX(-50%) translateY(8px); }
        }

        /* ═══ FORM SIDE — HIDDEN BOTTOM SHEET ═══ */
        .form-side {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            z-index: 10;
            background: #FFFFFF;
            border-radius: 32px 32px 0 0;
            padding: 32px 32px 48px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            display: flex;
            flex-direction: column;
            box-shadow:
                0 -20px 60px -12px rgba(45, 26, 17, 0.5),
                0 -2px 12px -2px rgba(45, 26, 17, 0.15);
            transform: translateY(100%);
            transition: transform 0.5s cubic-bezier(0.34, 1.1, 0.64, 1);
            will-change: transform;
            scrollbar-width: none;
            /* Prevent browser default scroll behavior during drag */
            overscroll-behavior: contain;
        }
        .form-side::-webkit-scrollbar { display: none; }

        .form-side.open {
            transform: translateY(0);
        }

        /* Disable transition during drag (for smooth follow) */
        .form-side.dragging {
            transition: none !important;
        }

        /* Handle bar — mas prominent */
        .form-side::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 44px;
            height: 4px;
            border-radius: 2px;
            background: #D7CCC8;
            z-index: 1;
            transition: all 0.2s;
        }
        .form-side.open::before {
            background: #B8651B;
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

        .field { margin-bottom: 16px; position: relative; }
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
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: 0.75; cursor: not-allowed; transform: none; }

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

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .scrim {
            position: fixed;
            inset: 0;
            background: rgba(20, 10, 5, 0.5);
            z-index: 9;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        .scrim.open {
            opacity: 1;
            pointer-events: auto;
        }
        .scrim.dragging {
            transition: none !important;
        }

        .hero-content.hidden {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
        }
        .swipe-hint.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* ═══ RESPONSIVE — MOBILE ═══ */
        @media (max-width: 480px) {
            .logo-badge {
                width: 200px;
                height: 200px;
                padding: 10px;
                box-shadow:
                    0 24px 52px -14px rgba(0, 0, 0, 0.8),
                    0 0 0 5px rgba(201, 169, 97, 0.75),
                    0 0 0 10px rgba(201, 169, 97, 0.25);
            }
            .welcome-title { font-size: 24px; }
            .welcome-sub { font-size: 13px; max-width: 280px; }
            .swipe-hint { bottom: 24px; font-size: 10px; }
            .form-side {
                border-radius: 28px 28px 0 0;
                padding: 28px 22px 40px;
            }
            .form-side::before { width: 40px; height: 3px; top: 10px; }
            .form-title { font-size: 23px; }
            .form-subtitle { font-size: 12.5px; }
            .field-input { padding: 14px 15px; font-size: 13.5px; }
            .social-btn { width: 42px; height: 42px; }
        }

        @media (max-height: 700px) {
            .logo-badge { width: 200px; height: 200px; padding: 10px; }
            .welcome-title { font-size: 22px; }
            .welcome-sub { font-size: 12.5px; }
            .form-side { padding: 26px 28px 32px; }
            .form-side::before { top: 9px; }
            .form-title { font-size: 22px; }
            .field { margin-bottom: 13px; }
            .divider { margin: 22px 0 18px; }
        }

        @media (min-width: 768px) {
            .logo-badge {
                width: 300px;
                height: 300px;
                padding: 14px;
            }
            .welcome-title { font-size: 38px; }
            .welcome-sub { font-size: 15px; max-width: 400px; }
            .form-side {
                border-radius: 40px 40px 0 0;
                padding: 44px 48px 56px;
                max-width: 560px;
                left: 50%;
                transform: translateX(-50%) translateY(100%);
            }
            .form-side.open {
                transform: translateX(-50%) translateY(0);
            }
            .form-side::before { width: 50px; height: 5px; top: 16px; }
            .form-inner { max-width: 480px; padding-top: 20px; }
            .form-title { font-size: 30px; }
            .form-subtitle { font-size: 14px; margin-bottom: 28px; }
            .field-input { padding: 16px 18px; font-size: 15px; }
            .btn-submit { padding: 18px; font-size: 16px; }
            .social-btn { width: 50px; height: 50px; }
        }

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

<div class="hero">
    <div class="hero-content" id="heroContent">
        <div class="logo-badge">
            <img src="{{ asset('images/logo.png') }}" alt="RYS Farm Products Dealer">
        </div>
        <h1 class="welcome-title">Welcome to <span class="accent">RYS Coffee</span></h1>
        <p class="welcome-sub">Your trusted wholesale &amp; retail partner for premium farm products.</p>
    </div>

    <div class="swipe-hint" id="swipeHint">
        <span>Swipe up to login</span>
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 19V5M19 12l-7-7-7 7"/>
        </svg>
    </div>
</div>

<div class="scrim" id="scrim"></div>

<div class="form-side" id="formSide">
    <div class="form-inner">

        <h1 class="form-title">Sign in</h1>
        <p class="form-subtitle">Welcome back! Enter your store credentials to continue.</p>

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
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="field-input" placeholder="Enter email address" required autocomplete="email">
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

<script>
    (function() {
        'use strict';

        var heroContent = document.getElementById('heroContent');
        var swipeHint   = document.getElementById('swipeHint');
        var formSide    = document.getElementById('formSide');
        var scrim       = document.getElementById('scrim');

        var isOpen = false;
        var isDragging = false;
        var startY = 0;
        var currentY = 0;
        var formHeight = 0;

        /* ═══ UPDATE FORM HEIGHT ═══ */
        function updateFormHeight() {
            formHeight = window.innerHeight;
        }
        updateFormHeight();
        window.addEventListener('resize', updateFormHeight);

        /* ═══ OPEN FORM ═══ */
        function openForm() {
            if (isOpen) return;
            isOpen = true;
            formSide.classList.add('open');
            scrim.classList.add('open');
            heroContent.classList.add('hidden');
            swipeHint.classList.add('hidden');

            setTimeout(function() {
                var emailField = document.getElementById('email');
                if (emailField && window.innerWidth > 768) emailField.focus();
            }, 500);
        }

        /* ═══ CLOSE FORM ═══ */
        function closeForm() {
            if (!isOpen) return;
            isOpen = false;
            formSide.classList.remove('open');
            formSide.classList.remove('dragging');
            scrim.classList.remove('open');
            scrim.classList.remove('dragging');
            heroContent.classList.remove('hidden');
            swipeHint.classList.remove('hidden');

            // Reset transform
            formSide.style.transform = '';
            scrim.style.opacity = '';

            if (document.activeElement) document.activeElement.blur();
        }

        /* ═══ SCRIM CLICK → CLOSE ═══ */
        scrim.addEventListener('click', closeForm);

        /* ═══ ESC KEY → CLOSE ═══ */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isOpen) closeForm();
        });

        /* ═══ TOUCH DRAG-FOLLOW ═══ */
        var dragStartY = 0;
        var dragDelta = 0;
        var dragFromForm = false;
        var DRAG_THRESHOLD = 120;  // px needed to close
        var VELOCITY_THRESHOLD = 0.6;  // px/ms for fast swipe

        document.addEventListener('touchstart', function(e) {
            if (!isOpen && formSide.contains(e.target)) return;
            if (isOpen && scrim.contains(e.target)) return;

            dragStartY = e.touches[0].clientY;
            dragDelta = 0;
            dragFromForm = formSide.contains(e.target);
        }, { passive: true });

        document.addEventListener('touchmove', function(e) {
            if (dragStartY === 0) return;

            currentY = e.touches[0].clientY;
            dragDelta = currentY - dragStartY;

            if (!isOpen) {
                /* ═══ NOT OPEN → Detect swipe UP to open ═══ */
                if (dragDelta < -30) {
                    // Optional: preview during swipe up (could add)
                    // Di natin i-preview para malinis
                }
            } else if (dragFromForm) {
                /* ═══ OPEN + dragging form ═══ */
                // Only allow drag down if form is at top
                if (formSide.scrollTop > 0 && dragDelta < 0) {
                    dragStartY = currentY;
                    dragDelta = 0;
                    return;
                }

                if (dragDelta > 0) {
                    // Dragging down — form follows finger
                    isDragging = true;
                    formSide.classList.add('dragging');
                    scrim.classList.add('dragging');

                    var progress = Math.min(dragDelta / formHeight, 1);
                    var translateY = dragDelta;

                    formSide.style.transform = 'translateY(' + translateY + 'px)';
                    scrim.style.opacity = String(1 - progress);
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', function(e) {
            if (dragStartY === 0) return;

            var endY = e.changedTouches[0].clientY;
            var totalDelta = endY - dragStartY;
            var deltaTime = Date.now() - (dragStartY ? e.timeStamp - (e.timeStamp - Date.now()) : Date.now());

            // Estimate velocity
            var velocity = Math.abs(totalDelta);

            if (!isOpen) {
                /* ═══ NOT OPEN → Swipe UP to open ═══ */
                if (totalDelta < -60) {
                    openForm();
                }
            } else if (isDragging) {
                /* ═══ DRAGGING → Decide: close or snap back ═══ */
                if (totalDelta > DRAG_THRESHOLD) {
                    // Enough drag → close
                    formSide.style.transform = '';
                    scrim.style.opacity = '';
                    setTimeout(function() {
                        closeForm();
                    }, 10);
                } else {
                    // Not enough → snap back to open
                    formSide.style.transform = '';
                    scrim.style.opacity = '';
                    formSide.classList.remove('dragging');
                    scrim.classList.remove('dragging');
                }
                isDragging = false;
            } else {
                /* ═══ OPEN but not dragging → check swipe down from top ═══ */
                if (totalDelta > 80 && formSide.scrollTop <= 0) {
                    closeForm();
                }
            }

            dragStartY = 0;
            dragDelta = 0;
            dragFromForm = false;
        }, { passive: true });

        /* ═══ WHEEL / SCROLL (desktop) ═══ */
        var wheelTimeout;
        document.addEventListener('wheel', function(e) {
            if (isOpen && formSide.contains(e.target)) {
                // Close if scroll up at top of form
                if (e.deltaY < -20 && formSide.scrollTop <= 0) {
                    clearTimeout(wheelTimeout);
                    wheelTimeout = setTimeout(closeForm, 80);
                }
                return;
            }

            if (!isOpen && e.deltaY > 20) {
                clearTimeout(wheelTimeout);
                wheelTimeout = setTimeout(openForm, 80);
            }
        }, { passive: true });

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
            /* ═══ AUTO-OPEN FORM ON VALIDATION ERRORS ═══ */
        @if($errors->any())
            setTimeout(function() { if (!isOpen) openForm(); }, 400);
        @endif

        })();
</script>

</body>
</html>