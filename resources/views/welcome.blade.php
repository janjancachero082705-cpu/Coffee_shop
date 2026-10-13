<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#B8651B">
    <title>Welcome - RYS Farm Products Dealer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #3E2723;
            font-size: 15px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            background: #FFFFFF;
            scroll-behavior: smooth;
        }

        /* ═══════════════════════════════════════════
           HERO — COFFEE BEANS
           ═══════════════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            min-height: 100dvh;
            background-image: url('{{ asset("images/coffee_bg.png") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #2D1A11;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            overflow: hidden;
            isolation: isolate;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg,
                rgba(20, 10, 5, 0.6) 0%,
                rgba(20, 10, 5, 0.7) 50%,
                rgba(20, 10, 5, 0.85) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 25% 35%, rgba(184, 101, 27, 0.25), transparent 55%),
                        radial-gradient(circle at 75% 65%, rgba(255, 183, 77, 0.15), transparent 60%);
            z-index: 1;
            pointer-events: none;
            animation: heroGlow 8s ease-in-out infinite;
        }
        @keyframes heroGlow {
            0%, 100% { opacity: 0.85; }
            50%      { opacity: 1; }
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 720px;
            text-align: center;
            color: #FFFFFF;
            animation: heroIn 0.9s cubic-bezier(0.34, 1.15, 0.64, 1);
        }
        @keyframes heroIn {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .hero-logo {
            width: 160px;
            height: 160px;
            margin: 0 auto 26px;
            border-radius: 50%;
            background: #FDFBF7;
            padding: 8px;
            box-shadow:
                0 28px 64px -16px rgba(0, 0, 0, 0.8),
                0 0 0 5px rgba(201, 169, 97, 0.7),
                0 0 0 10px rgba(201, 169, 97, 0.22);
            display: grid;
            place-items: center;
            overflow: hidden;
            animation: logoFloat 4.5s ease-in-out infinite;
        }
        .hero-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
            display: block;
            background: #FDFBF7;
        }
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }

        /* Eyebrow badge */
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(201, 169, 97, 0.18);
            border: 1px solid rgba(201, 169, 97, 0.5);
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            color: #E6C88A;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 22px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .hero-eyebrow-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #E6C88A;
            box-shadow: 0 0 10px rgba(230, 200, 138, 0.9);
            animation: pulseDot 2s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.5; transform: scale(0.85); }
        }

        /* Title */
        .hero-title {
            font-size: clamp(34px, 6vw, 56px);
            font-weight: 900;
            letter-spacing: -0.03em;
            line-height: 1.08;
            margin-bottom: 20px;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.6);
        }
        .hero-title .accent {
            background: linear-gradient(135deg, #FFB74D, #F57C00, #B8651B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
        }

        .hero-sub {
            font-size: clamp(14px, 1.8vw, 17px);
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            line-height: 1.6;
            max-width: 560px;
            margin: 0 auto 36px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        /* Buttons */
        .hero-actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-hero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 54px;
            padding: 0 32px;
            border-radius: 14px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: -0.005em;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, #C9782B 0%, #B8651B 50%, #9C5415 100%);
            color: #FFFFFF;
            box-shadow:
                0 14px 32px -8px rgba(184, 101, 27, 0.7),
                inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.65s ease;
        }
        .btn-primary:hover::before { left: 100%; }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow:
                0 20px 40px -10px rgba(184, 101, 27, 0.85),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        .btn-primary:active { transform: translateY(-1px); }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.55);
            transform: translateY(-3px);
            box-shadow: 0 14px 28px -8px rgba(0, 0, 0, 0.5);
        }
        .btn-hero svg { transition: transform 0.25s; }
        .btn-hero:hover svg { transform: translateX(4px); }

        /* Scroll indicator */
        .scroll-hint {
            position: absolute;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            animation: scrollBounce 2.2s ease-in-out infinite;
        }
        .scroll-hint svg { opacity: 0.85; }
        @keyframes scrollBounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%      { transform: translateX(-50%) translateY(6px); }
        }

        /* ═══════════════════════════════════════════
           FEATURES SECTION
           ═══════════════════════════════════════════ */
        .features {
            padding: 80px 24px;
            background: linear-gradient(180deg, #FDFBF7 0%, #F5EFE4 100%);
            position: relative;
        }
        .features-inner {
            max-width: 1100px;
            margin: 0 auto;
        }
        .features-head {
            text-align: center;
            margin-bottom: 56px;
        }
        .features-eyebrow {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            color: #B8651B;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .features-title {
            font-size: clamp(26px, 4vw, 38px);
            font-weight: 900;
            color: #2D1A11;
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin-bottom: 12px;
        }
        .features-title .accent {
            background: linear-gradient(135deg, #C9782B, #B8651B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .features-sub {
            font-size: 15px;
            color: #8D6E63;
            font-weight: 500;
            line-height: 1.6;
            max-width: 520px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background: #FFFFFF;
            border: 1.5px solid #EFEBE9;
            border-radius: 20px;
            padding: 28px 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #C9782B, #B8651B, #9C5415);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .feature-card:hover {
            border-color: #D7CCC8;
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -16px rgba(62, 39, 35, 0.2);
        }
        .feature-card:hover::before { transform: scaleX(1); }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #FDF7ED, #F5E6CE);
            color: #B8651B;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            transition: all 0.25s;
            border: 1px solid #F5E6CE;
        }
        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, #C9782B, #B8651B);
            color: #FFFFFF;
            border-color: #B8651B;
            transform: scale(1.08) rotate(-4deg);
            box-shadow: 0 12px 24px -8px rgba(184, 101, 27, 0.5);
        }

        .feature-title {
            font-size: 16px;
            font-weight: 800;
            color: #2D1A11;
            margin-bottom: 8px;
            letter-spacing: -0.015em;
        }
        .feature-text {
            font-size: 13.5px;
            color: #6D4C41;
            line-height: 1.6;
            font-weight: 500;
        }

        /* ═══════════════════════════════════════════
           CTA SECTION
           ═══════════════════════════════════════════ */
        .cta {
            padding: 80px 24px;
            background: linear-gradient(135deg, #2D1A11 0%, #4E342E 50%, #3E2723 100%);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .cta::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.25), transparent 65%);
            pointer-events: none;
        }
        .cta::after {
            content: '';
            position: absolute;
            bottom: -120px; left: -120px;
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(184, 101, 27, 0.2), transparent 65%);
            pointer-events: none;
        }
        .cta-inner {
            position: relative;
            z-index: 1;
            max-width: 640px;
            margin: 0 auto;
            color: #FFFFFF;
        }
        .cta-title {
            font-size: clamp(24px, 3.5vw, 36px);
            font-weight: 900;
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin-bottom: 14px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }
        .cta-title .accent {
            background: linear-gradient(135deg, #E6C88A, #C9A961);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .cta-sub {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        /* ═══════════════════════════════════════════
           FOOTER
           ═══════════════════════════════════════════ */
        .footer {
            padding: 32px 24px;
            background: #2D1A11;
            color: rgba(255, 255, 255, 0.5);
            text-align: center;
            font-size: 12.5px;
            font-weight: 600;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .footer strong {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 800;
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE
           ═══════════════════════════════════════════ */
        @media (max-width: 480px) {
            .hero { padding: 32px 20px; background-attachment: scroll; }
            .hero-logo { width: 120px; height: 120px; padding: 6px; }
            .hero-actions { flex-direction: column; width: 100%; }
            .btn-hero { width: 100%; min-height: 52px; padding: 0 24px; }
            .features { padding: 60px 20px; }
            .cta { padding: 60px 20px; }
            .scroll-hint { bottom: 16px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════
     HERO
     ═══════════════════════════════════════════ --}}
<section class="hero">

    <div class="hero-inner">

        <div class="hero-logo">
            <img src="{{ asset('images/logo.png') }}" alt="RYS Farm Products Dealer">
        </div>

        <div class="hero-eyebrow">
            <span class="hero-eyebrow-dot"></span>
            RYS Farm Products Dealer
        </div>

        <h1 class="hero-title">
            Welcome to <span class="accent">RYS Coffee Beans</span>
        </h1>

        <p class="hero-sub">
            Your trusted wholesale &amp; retail partner for premium coffee beans
            and specialty beverages. Manage orders, deliveries, and payments — all in one place.
        </p>

        <div class="hero-actions">
            <a href="{{ route('portal.login') }}" class="btn-hero btn-primary">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                </svg>
                Sign In to Portal
            </a>
            <a href="{{ route('portal.register') }}" class="btn-hero btn-ghost">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <path d="M20 8v6M23 11h-6"/>
                </svg>
                Create Store Account
            </a>
        </div>

    </div>

    {{-- Scroll hint --}}
    <div class="scroll-hint">
        <span>Scroll</span>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M19 12l-7 7-7-7"/>
        </svg>
    </div>

</section>

{{-- ═══════════════════════════════════════════
     FEATURES
     ═══════════════════════════════════════════ --}}
<section class="features">
    <div class="features-inner">

        <div class="features-head">
            <div class="features-eyebrow">Why Choose Us</div>
            <h2 class="features-title">
                Everything you need in <span class="accent">one portal</span>
            </h2>
            <p class="features-sub">
                Simple, fast, and built for coffee shops, sari-sari stores, and resellers.
            </p>
        </div>

        <div class="features-grid">

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="feature-title">Easy Ordering</div>
                <div class="feature-text">Browse products, add to cart, and submit your order in seconds.</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="1" y="3" width="15" height="13"/>
                        <path d="M16 8h4l3 3v5h-7V8z"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                </div>
                <div class="feature-title">Track Deliveries</div>
                <div class="feature-text">Real-time updates on every delivery — confirm receipt with one tap.</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                <div class="feature-title">Flexible Payments</div>
                <div class="feature-text">Cash, GCash, Maya, or bank transfer — pay the way you want.</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/>
                    </svg>
                </div>
                <div class="feature-title">Sales Reports</div>
                <div class="feature-text">See your sales, balance, and payments — clear and simple.</div>
            </div>

        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════
     CTA
     ═══════════════════════════════════════════ --}}
<section class="cta">
    <div class="cta-inner">
        <h2 class="cta-title">Ready to <span class="accent">get started</span>?</h2>
        <p class="cta-sub">Create your store account or sign in to the portal to continue.</p>
        <div class="hero-actions">
            <a href="{{ route('portal.login') }}" class="btn-hero btn-primary">
                Sign In
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="{{ route('portal.register') }}" class="btn-hero btn-ghost">
                Register
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════ --}}
<footer class="footer">
    &copy; {{ date('Y') }} <strong>RYS Farm Products Dealer</strong> · Wholesale &amp; Retailer · All rights reserved
</footer>

</body>
</html>