<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#FDF8F3">
    <title>Admin Login - RYS Farm Products Dealer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { color-scheme: light only; min-height: 100%; font-family: 'Inter', system-ui, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at 15% 20%, rgba(232, 168, 124, 0.08), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(201, 169, 97, 0.06), transparent 45%),
                linear-gradient(180deg, #FDF8F3 0%, #F5EDE6 100%);
            background-attachment: fixed;
        }

        body::after {
            content: '';
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(232, 168, 124, 0.12), transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: glowPulse 8s ease-in-out infinite;
        }
        @keyframes glowPulse {
            0%, 100% { opacity: 0.6; transform: translate(-50%, -50%) scale(1); }
            50%      { opacity: 1;   transform: translate(-50%, -50%) scale(1.15); }
        }

        /* ═══ LOGIN CARD ═══ */
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 940px;
            background: #FFFFFF;
            border-radius: 32px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 500px 1fr;
            box-shadow:
                0 40px 80px -24px rgba(45, 26, 17, 0.25),
                0 16px 32px -16px rgba(45, 26, 17, 0.1);
            animation: cardIn 0.7s cubic-bezier(0.34, 1.15, 0.64, 1);
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ═══ LEFT — ROASTED RYS FARM PRODUCTS DEALER BACKGROUND ═══ */
        .login-left {
            position: relative;
            background-image: url('{{ asset('images/OIP.webp') }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 5;
            min-height: 620px;
            /* EXTEND WIDTH — overlap ang form */
            width: calc(100% + 100px);
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 2;
            min-height: 620px;
            /* DIAGONAL SLASH — right edge cut */
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            border-radius: 32px 0 0 32px;
            padding: 48px 40px;
            overflow: visible;
            z-index: 2;
            min-height: 620px;
        }

        /* Dark overlay for text readability */
        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(45, 26, 17, 0.55) 0%, rgba(45, 26, 17, 0.65) 60%, rgba(45, 26, 17, 0.78) 100%);
            pointer-events: none;
            z-index: 0;
        }

        /* Warm glow accent */
        .login-left::after {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 240px; height: 240px;
            background: radial-gradient(circle, rgba(232, 168, 124, 0.35), transparent 65%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            animation: glowPulse 6s ease-in-out infinite;
        }

        .left-title {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 42px;
            font-weight: 900;
            font-style: italic;
            color: #FFFFFF;
            line-height: 1.1;
            letter-spacing: -0.02em;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            margin-bottom: 12px;
            max-width: 280px;
        }
        .left-title .accent {
            display: block;
            background: linear-gradient(135deg, #F5C88A, #E8A87C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
        }

        .left-sub {
            position: relative;
            z-index: 2;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            line-height: 1.6;
            max-width: 220px;
            margin-bottom: 40px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        /* ═══ CUP — LUMALABAS SA FORM (overlaps right side) ═══ */
        
        
        

        /* ═══ RIGHT — FORM ═══ */
        .login-right {
            padding: 48px 56px 48px 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            position: relative;
            z-index: 1;
            min-width: 0;
        }
        .form-wrap {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 2;
        }

        .form-header { text-align: center; margin-bottom: 28px; }
        .form-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 28px;
            font-weight: 900;
            color: #2D1A11;
            letter-spacing: -0.02em;
            line-height: 1.15;
            margin-bottom: 6px;
        }
        .form-sub { font-size: 12.5px; color: #A1887F; font-weight: 500; }

        .form-group { margin-bottom: 16px; position: relative; }
        .label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #8D6E63;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .input {
            width: 100%;
            padding: 13px 16px;
            background: #FDFBF7;
            border: 1.5px solid #F0E5D8;
            border-radius: 12px;
            color: #2D1A11;
            font-size: 13.5px;
            font-family: inherit;
            font-weight: 500;
            outline: none;
            transition: all 0.2s;
        }
        .input::placeholder { color: #C4B5AC; font-weight: 500; }
        .input:hover { border-color: #E5D5C4; background: #FFFDFA; }
        .input:focus {
            border-color: #E8A87C;
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(232, 168, 124, 0.15);
        }
        .input.has-icon-right { padding-right: 46px; }

        .input-icon-right {
            position: absolute;
            right: 14px;
            color: #C4B5AC;
            pointer-events: none;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .input-wrap:focus-within .input-icon-right { color: #E8A87C; }

        .pw-toggle {
            position: absolute;
            right: 8px;
            width: 34px; height: 34px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #C4B5AC;
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: all 0.15s;
            padding: 0;
        }
        .pw-toggle:hover { background: #FDF7F0; color: #E8A87C; }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            margin-top: 4px;
        }
        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 12.5px;
            color: #4E342E;
            font-weight: 600;
            user-select: none;
        }
        .checkbox-wrap input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 17px; height: 17px;
            border-radius: 5px;
            border: 1.5px solid #E5D5C4;
            background: #FFFFFF;
            cursor: pointer;
            position: relative;
            transition: all 0.18s;
            flex-shrink: 0;
        }
        .checkbox-wrap input[type="checkbox"]:hover { border-color: #E8A87C; }
        .checkbox-wrap input[type="checkbox"]:checked { background: #E8A87C; border-color: #E8A87C; }
        .checkbox-wrap input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 5px; top: 1px;
            width: 4px; height: 9px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .forgot-link { font-size: 12.5px; color: #E8A87C; font-weight: 700; text-decoration: none; transition: color 0.15s; }
        .forgot-link:hover { color: #D89065; text-decoration: underline; }

        .btn-submit {
            position: relative;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6D4C41 0%, #4E342E 100%);
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: all 0.25s cubic-bezier(0.34, 1.2, 0.64, 1);
            box-shadow: 0 10px 24px -8px rgba(78, 52, 46, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            overflow: hidden;
            letter-spacing: -0.005em;
            margin-bottom: 22px;
        }
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: left 0.6s ease;
        }
        .btn-submit:hover::before { left: 100%; }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px -8px rgba(78, 52, 46, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .or-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            font-size: 11px;
            font-weight: 700;
            color: #C4B5AC;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, #F0E5D8, transparent);
        }

        .social-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 14px;
            background: #FFFFFF;
            border: 1.5px solid #F0E5D8;
            border-radius: 11px;
            color: #4E342E;
            font-size: 12px;
            font-weight: 700;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-social:hover {
            background: #FDFBF7;
            border-color: #E8A87C;
            transform: translateY(-1px);
        }

        .form-footer {
            text-align: center;
            font-size: 12.5px;
            color: #A1887F;
            font-weight: 500;
            line-height: 1.7;
        }
        .form-footer a { color: #E8A87C; font-weight: 800; text-decoration: none; transition: color 0.15s; }
        .form-footer a:hover { color: #D89065; text-decoration: underline; }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 12px 14px;
            border-radius: 11px;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 18px;
            border: 1.5px solid;
            line-height: 1.5;
            animation: shakeIn 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }
        .alert svg { flex-shrink: 0; margin-top: 1px; }
        .alert-danger { background: #FEF2F2; color: #B91C1C; border-color: #FECACA; }
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
            width: 14px; height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @media (max-width: 860px) {
            .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 940px;
            background: #FFFFFF;
            border-radius: 32px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 500px 1fr;
            box-shadow:
                0 40px 80px -24px rgba(45, 26, 17, 0.25),
                0 16px 32px -16px rgba(45, 26, 17, 0.1);
            animation: cardIn 0.7s cubic-bezier(0.34, 1.15, 0.64, 1);
        }
            .login-left {
            position: relative;
            background-image: url('{{ asset('images/OIP.webp') }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 5;
            min-height: 620px;
            /* EXTEND WIDTH — overlap ang form */
            width: calc(100% + 100px);
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 2;
            min-height: 620px;
            /* DIAGONAL SLASH — right edge cut */
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }
            .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(45, 26, 17, 0.55) 0%, rgba(45, 26, 17, 0.65) 60%, rgba(45, 26, 17, 0.78) 100%);
            pointer-events: none;
            z-index: 0;
        }
            .login-left .left-sub {
            position: relative;
            z-index: 2;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            line-height: 1.6;
            max-width: 220px;
            margin-bottom: 40px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }
            .left-title {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 42px;
            font-weight: 900;
            font-style: italic;
            color: #FFFFFF;
            line-height: 1.1;
            letter-spacing: -0.02em;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            margin-bottom: 12px;
            max-width: 280px;
        }
            
            .login-right {
            padding: 48px 56px 48px 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            position: relative;
            z-index: 1;
            min-width: 0;
        }
        }
        @media (max-width: 480px) {
            body { padding: 16px; }
            .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 940px;
            background: #FFFFFF;
            border-radius: 32px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 500px 1fr;
            box-shadow:
                0 40px 80px -24px rgba(45, 26, 17, 0.25),
                0 16px 32px -16px rgba(45, 26, 17, 0.1);
            animation: cardIn 0.7s cubic-bezier(0.34, 1.15, 0.64, 1);
        }
            .login-left {
            position: relative;
            background-image: url('{{ asset('images/OIP.webp') }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 5;
            min-height: 620px;
            /* EXTEND WIDTH — overlap ang form */
            width: calc(100% + 100px);
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 2;
            min-height: 620px;
            /* DIAGONAL SLASH — right edge cut */
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }
            .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(45, 26, 17, 0.55) 0%, rgba(45, 26, 17, 0.65) 60%, rgba(45, 26, 17, 0.78) 100%);
            pointer-events: none;
            z-index: 0;
        }
            .left-title {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 42px;
            font-weight: 900;
            font-style: italic;
            color: #FFFFFF;
            line-height: 1.1;
            letter-spacing: -0.02em;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            margin-bottom: 12px;
            max-width: 280px;
        }
            .login-right {
            padding: 48px 56px 48px 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            position: relative;
            z-index: 1;
            min-width: 0;
        }
            .form-title { font-size: 24px; }
            .social-row { grid-template-columns: 1fr; }
            
        }
    
        /* ═══ NO BLUR ON BACKGROUND ═══ */
        .login-left {
            position: relative;
            background-image: url('{{ asset('images/OIP.webp') }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 5;
            min-height: 620px;
            /* EXTEND WIDTH — overlap ang form */
            width: calc(100% + 100px);
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }}');
            background-size: cover;
            background-position: center;
            background-color: #4E342E;
            padding: 48px 40px;
            overflow: hidden;
            z-index: 2;
            min-height: 620px;
            /* DIAGONAL SLASH — right edge cut */
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
        }
        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(45, 26, 17, 0.55) 0%, rgba(45, 26, 17, 0.65) 60%, rgba(45, 26, 17, 0.78) 100%);
            pointer-events: none;
            z-index: 0;
        }

</style>
</head>
<body>

<div class="login-card">

    {{-- ═══ LEFT — ROASTED RYS FARM PRODUCTS DEALER + OVERLAP CUP ═══ --}}
    <div class="login-left">

        <div class="left-title">
            Sign in
            <span class="accent">with a Coffee</span>
        </div>
        <div class="left-sub">
            Welcome back! Please enter your details and enjoy the best coffee experience.
        </div>

        {{-- Coffee Cup — lumalabas sa form ═══ --}}
        
    </div>

    {{-- ═══ RIGHT — FORM ═══ --}}
    <div class="login-right">
        <div class="form-wrap">

            <div class="form-header">
                <div class="form-title">Sign In</div>
                <div class="form-sub">Welcome back! Please enter your details.</div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" id="adminLoginForm">
                @csrf

                <div class="form-group">
                    <label class="label" for="email">Email</label>
                    <div class="input-wrap">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="input has-icon-right" placeholder="admin@coffee.test" required autofocus autocomplete="email">
                        <span class="input-icon-right">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <path d="M22 6l-10 7L2 6"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label" for="password">Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" class="input has-icon-right" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="pw-toggle" id="pwToggle" title="Show password">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" id="eyeIcon">
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
                    <a href="#" class="forgot-link">Forgot password</a>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span id="btnText">Sign In</span>
                </button>

                <div class="or-divider">Or</div>

                <div class="social-row">
                    <a href="#" class="btn-social">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </a>
                    <a href="#" class="btn-social">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#1877F2">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                </div>

                <div class="form-footer">
                    Not a member yet? <a href="#">Sign up</a><br>
                    Check us on <a href="#">Maps</a>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
    (function() {
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
        var form = document.getElementById('adminLoginForm');
        var submitBtn = document.getElementById('submitBtn');
        var btnText = document.getElementById('btnText');
        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                btnText.textContent = 'Signing in...';
                var spinner = document.createElement('div');
                spinner.className = 'spinner';
                submitBtn.appendChild(spinner);
            });
        }
    })();
</script>

</body>
</html>