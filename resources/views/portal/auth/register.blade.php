<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#B8651B">
    <title>Register Store - RYS Farm Products Dealer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            color-scheme: light only;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #3E2723;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            background: #FFFFFF;
        }

        /* ═══ SCROLLABLE SCREEN ═══ */
        .auth-screen {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            background: #FFFFFF;
            overflow-x: hidden;
            position: relative;
        }

        /* ═══ HERO — COFFEE BEANS ═══ */
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

        /* ═══ RYS LOGO — CENTERED ═══ */
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

        /* ═══ WHITE FORM — ROUNDED TOP ═══ */
        .form-side {
            position: relative;
            z-index: 2;
            background: #FFFFFF;
            border-radius: 32px 32px 0 0;
            margin-top: -32px;
            padding: 32px 32px 40px;
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
        }

        /* Title */
        .form-title {
            font-size: 24px;
            font-weight: 800;
            color: #B8651B;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
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
        .form-group { margin-bottom: 14px; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }
        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #5D4037;
            margin-bottom: 7px;
            letter-spacing: 0.01em;
        }
        .field-label .req { color: #D84315; }

        .field-input {
            width: 100%;
            padding: 14px 16px;
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
            margin-top: 8px;
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

        /* Back link */
        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #6D4C41;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }
        .back-link:hover { color: #B8651B; }
        .back-link svg { transition: transform 0.2s; }
        .back-link:hover svg { transform: translateX(-3px); }

        /* Footer */
        .form-footer {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1.5px dashed #EFEBE9;
            text-align: center;
            font-size: 10.5px;
            color: #8D6E63;
            font-weight: 600;
        }

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
                padding: 28px 22px 32px;
            }
            .form-side::before { width: 40px; height: 3px; top: 10px; }
            .form-title { font-size: 21px; }
            .form-subtitle { font-size: 12.5px; margin-bottom: 20px; }
            .form-row { grid-template-columns: 1fr; }
            .field-input { padding: 13px 15px; font-size: 13.5px; }
        }

        @media (min-width: 768px) {
            .hero { height: 46vh; height: 46dvh; max-height: 420px; }
            .logo-badge { width: 160px; height: 160px; padding: 10px; }
            .form-side {
                border-radius: 40px 40px 0 0;
                margin-top: -40px;
                padding: 44px 48px 48px;
            }
            .form-side::before { width: 50px; height: 5px; top: 16px; }
            .form-inner { max-width: 520px; padding-top: 20px; }
            .form-title { font-size: 28px; }
            .field-input { padding: 16px 18px; font-size: 15px; }
            .btn-submit { padding: 18px; font-size: 16px; }
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

    {{-- ═══ FORM ═══ --}}
    <div class="form-side">
        <div class="form-inner">

            <h1 class="form-title">Create your store account</h1>
            <p class="form-subtitle">Fill out the form below. Your account will be reviewed and activated by an admin.</p>

            @if($errors->any())
                <div class="alert">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('portal.register.store') }}" id="registerForm">
                @csrf

                <div class="form-group">
                    <label class="field-label" for="store_name">Store Name <span class="req">*</span></label>
                    <input type="text" id="store_name" name="store_name" value="{{ old('store_name') }}" class="field-input" placeholder="e.g. Juan Sari-sari Store" required>
                </div>

                <div class="form-group">
                    <label class="field-label" for="owner_name">Owner Name <span class="req">*</span></label>
                    <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" class="field-input" placeholder="Full name" required>
                </div>

                <div class="form-row">
                    <div>
                        <label class="field-label" for="email">Email <span class="req">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="field-input" placeholder="store@example.com" required>
                    </div>
                    <div>
                        <label class="field-label" for="contact_number">Contact <span class="req">*</span></label>
                        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" class="field-input" placeholder="09XX XXX XXXX" required>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label class="field-label" for="city">City <span class="req">*</span></label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" class="field-input" placeholder="Cebu City" required>
                    </div>
                    <div>
                        <label class="field-label" for="barangay">Barangay</label>
                        <input type="text" id="barangay" name="barangay" value="{{ old('barangay') }}" class="field-input" placeholder="Barangay">
                    </div>
                </div>

                <div class="form-group">
                    <label class="field-label" for="address">Address <span class="req">*</span></label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" class="field-input" placeholder="Street address" required>
                </div>

                <div class="form-row">
                    <div>
                        <label class="field-label" for="password">Password <span class="req">*</span></label>
                        <input type="password" id="password" name="password" class="field-input" placeholder="Min 6 chars" required>
                    </div>
                    <div>
                        <label class="field-label" for="password_confirmation">Confirm <span class="req">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="field-input" placeholder="Repeat" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span id="btnText">Submit Registration</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" id="btnArrow">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </form>

            <a href="{{ route('portal.login') }}" class="back-link">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Sign In
            </a>

            <div class="form-footer">
                &copy; {{ date('Y') }} RYS Farm Products Dealer · All rights reserved
            </div>

        </div>
    </div>

</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        var btnText = document.getElementById('btnText');
        var btnArrow = document.getElementById('btnArrow');
        btn.disabled = true;
        btnText.textContent = 'Submitting...';
        btnArrow.style.display = 'none';
        var s = document.createElement('div');
        s.className = 'spinner';
        btn.appendChild(s);
    });
</script>

</body>
</html>