<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#B8651B">
    <title>Registration Submitted - RYS Farm Products Dealer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #3E2723;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            background: #FFFFFF;
        }

        .auth-screen {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            background: #FFFFFF;
            overflow-x: hidden;
            position: relative;
        }

        /* ═══ HERO ═══ */
        .hero {
            position: relative;
            height: 42vh;
            height: 42dvh;
            min-height: 260px;
            max-height: 380px;
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
                rgba(20, 10, 5, 0.6) 45%,
                rgba(20, 10, 5, 0.75) 100%);
            z-index: 1;
            pointer-events: none;
        }
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

        .logo-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            width: 140px;
            height: 140px;
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
            animation: logoFloat 4.5s ease-in-out infinite;
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
        @keyframes logoFloat {
            0%, 100% { transform: translate(-50%, -50%) translateY(0); }
            50%      { transform: translate(-50%, -50%) translateY(-7px); }
        }
        @keyframes rotateRing {
            to { transform: rotate(360deg); }
        }

        /* ═══ FORM SIDE ═══ */
        .form-side {
            position: relative;
            z-index: 2;
            background: #FFFFFF;
            border-radius: 32px 32px 0 0;
            margin-top: -32px;
            padding: 32px 32px 48px;
            box-shadow:
                0 -12px 32px -8px rgba(45, 26, 17, 0.25),
                0 -2px 8px -2px rgba(45, 26, 17, 0.08);
        }
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
            max-width: 480px;
            margin: 0 auto;
            padding-top: 16px;
            text-align: center;
        }

        /* Success icon */
        .success-icon {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22C55E, #16A34A);
            display: grid;
            place-items: center;
            margin: 8px auto 22px;
            color: #FFFFFF;
            box-shadow:
                0 20px 40px -12px rgba(34, 197, 94, 0.6),
                0 0 0 8px rgba(34, 197, 94, 0.12);
            animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); }
        }

        .success-title {
            font-size: 26px;
            font-weight: 800;
            color: #2D1A11;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .success-title .accent {
            background: linear-gradient(135deg, #C9782B, #B8651B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .success-sub {
            font-size: 13.5px;
            color: #8D6E63;
            line-height: 1.6;
            margin-bottom: 24px;
            max-width: 380px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Email box */
        .email-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #FDF7ED;
            border: 1.5px solid #F5E6CE;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 26px;
            font-family: ui-monospace, monospace;
            font-size: 13px;
            color: #B8651B;
            font-weight: 700;
            word-break: break-all;
            max-width: 100%;
            transition: all 0.2s;
        }
        .email-box:hover {
            border-color: #B8651B;
            background: #FFFFFF;
        }
        .email-box svg { flex-shrink: 0; color: #B8651B; }

        /* Steps */
        .steps {
            text-align: left;
            background: #FDFBF7;
            border: 1.5px solid #EFEBE9;
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 28px;
        }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 10px 0;
            position: relative;
        }
        .step + .step {
            border-top: 1.5px dashed #EFEBE9;
        }
        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 2px solid #E5DFD8;
            color: #A89B92;
            font-size: 11.5px;
            font-weight: 800;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .step.done .step-num {
            background: linear-gradient(135deg, #22C55E, #16A34A);
            border-color: #22C55E;
            color: #FFFFFF;
            box-shadow: 0 6px 14px -4px rgba(34, 197, 94, 0.5);
        }
        .step.active .step-num {
            background: linear-gradient(135deg, #C9782B, #B8651B);
            border-color: #B8651B;
            color: #FFFFFF;
            box-shadow: 0 6px 14px -4px rgba(184, 101, 27, 0.5);
            animation: pulseDot 1.8s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 6px 14px -4px rgba(184, 101, 27, 0.5); }
            50%      { box-shadow: 0 6px 20px -4px rgba(184, 101, 27, 0.85); }
        }
        .step-text {
            font-size: 13px;
            color: #3E2723;
            font-weight: 600;
            line-height: 1.5;
            padding-top: 4px;
        }
        .step.done .step-text { color: #8D6E63; }
        .step.pending .step-text { color: #A89B92; }

        /* Back button */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            background: linear-gradient(135deg, #C9782B 0%, #B8651B 50%, #9C5415 100%);
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:
                0 10px 24px -6px rgba(184, 101, 27, 0.55),
                inset 0 1px 0 rgba(255, 255, 255, 0.25);
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 280px;
        }
        .btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.65s ease;
        }
        .btn:hover::before { left: 100%; }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow:
                0 16px 32px -8px rgba(184, 101, 27, 0.7),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        .btn:active { transform: translateY(0); }
        .btn svg { transition: transform 0.2s; }
        .btn:hover svg { transform: translateX(-3px); }

        .footer {
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1.5px dashed #EFEBE9;
            font-size: 10.5px;
            color: #8D6E63;
            font-weight: 600;
        }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width: 480px) {
            .hero { height: 38vh; height: 38dvh; min-height: 240px; }
            .logo-badge {
                width: 110px;
                height: 110px;
                padding: 6px;
                box-shadow:
                    0 20px 44px -12px rgba(0, 0, 0, 0.7),
                    0 0 0 4px rgba(201, 169, 97, 0.7),
                    0 0 0 8px rgba(201, 169, 97, 0.22);
            }
            .form-side {
                border-radius: 28px 28px 0 0;
                margin-top: -28px;
                padding: 28px 22px 40px;
            }
            .form-side::before { width: 40px; height: 3px; top: 10px; }
            .success-icon { width: 76px; height: 76px; }
            .success-title { font-size: 22px; }
            .success-sub { font-size: 12.5px; }
            .email-box { font-size: 12px; padding: 10px 14px; }
            .steps { padding: 16px 18px; }
            .btn { padding: 13px 24px; font-size: 13.5px; }
        }

        @media (min-width: 768px) {
            .hero { height: 46vh; height: 46dvh; max-height: 420px; }
            .logo-badge { width: 160px; height: 160px; padding: 10px; }
            .form-side {
                border-radius: 40px 40px 0 0;
                margin-top: -40px;
                padding: 44px 48px 56px;
            }
            .form-side::before { width: 50px; height: 5px; top: 16px; }
            .form-inner { max-width: 520px; padding-top: 20px; }
            .success-title { font-size: 30px; }
            .success-sub { font-size: 14.5px; }
            .btn { padding: 16px 32px; font-size: 15px; max-width: 320px; }
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

<div class="auth-screen">

    {{-- ═══ HERO ═══ --}}
    <div class="hero">
        <div class="logo-badge">
            <img src="{{ asset('images/logo.png') }}" alt="RYS Farm Products Dealer">
        </div>
    </div>

    {{-- ═══ SUCCESS CONTENT ═══ --}}
    <div class="form-side">
        <div class="form-inner">

            <div class="success-icon">
                <svg width="42" height="42" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
            </div>

            <h1 class="success-title">Registration <span class="accent">Submitted!</span></h1>
            <p class="success-sub">Your store has been registered. Please wait for admin approval before you can sign in.</p>

            @if(session('registered_email'))
                <div class="email-box">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                    {{ session('registered_email') }}
                </div>
            @endif

            <div class="steps">
                <div class="step done">
                    <div class="step-num">1</div>
                    <div class="step-text">Registration submitted</div>
                </div>
                <div class="step active">
                    <div class="step-num">2</div>
                    <div class="step-text">Admin reviews and approves your account</div>
                </div>
                <div class="step pending">
                    <div class="step-num">3</div>
                    <div class="step-text">Sign in and start using the portal</div>
                </div>
            </div>

            <a href="{{ route('portal.login') }}" class="btn">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Sign In
            </a>

            <div class="footer">
                &copy; {{ date('Y') }} RYS Farm Products Dealer · All rights reserved
            </div>

        </div>
    </div>

</div>

</body>
</html>