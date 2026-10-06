<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Submitted - Coffee Beans</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            font-family: 'Inter', system-ui, sans-serif;
            color: #f5f3f0;
            min-height: 100vh;
        }
        body {
            background-color: #0a0a0e;
            background-image:
                linear-gradient(180deg, rgba(10, 10, 14, 0.4) 0%, rgba(10, 10, 14, 0.6) 100%),
                url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: rgba(20, 20, 26, 0.6);
            backdrop-filter: blur(24px) saturate(1.6);
            -webkit-backdrop-filter: blur(24px) saturate(1.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.85);
            animation: cardIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            display: grid;
            place-items: center;
            margin: 0 auto 24px;
            color: #fff;
            box-shadow: 0 16px 32px -10px rgba(34, 197, 94, 0.7);
            animation: popIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes popIn {
            0% { transform: scale(0); }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }
        .accent { color: #c9a961; }
        p {
            font-size: 13.5px;
            color: #b8b5b0;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .email-box {
            background: rgba(169, 120, 74, 0.1);
            border: 1px solid rgba(169, 120, 74, 0.25);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-family: ui-monospace, monospace;
            font-size: 13px;
            color: #c9a961;
            font-weight: 700;
            word-break: break-all;
        }
        .steps {
            text-align: left;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 8px 0;
        }
        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(201, 169, 97, 0.2);
            color: #c9a961;
            font-size: 11px;
            font-weight: 800;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .step.done .step-num {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }
        .step-text {
            font-size: 12.5px;
            color: #d8d5d0;
            line-height: 1.5;
        }
        .step.done .step-text { color: #a8a5a0; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 12px 24px;
            border-radius: 10px;
            background: linear-gradient(135deg, #c9a961, #8a5f36);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.8);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -8px rgba(169, 120, 74, 1);
        }
    </style>
</head>
<body>

<div class="card">
    <div class="success-icon">
        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path d="M5 12l5 5L20 7"/>
        </svg>
    </div>

    <h1>Registration <span class="accent">Submitted!</span></h1>
    <p>Your store has been registered. Please wait for admin approval before you can sign in.</p>

    @if(session('registered_email'))
        <div class="email-box">
            {{ session('registered_email') }}
        </div>
    @endif

    <div class="steps">
        <div class="step done">
            <div class="step-num">1</div>
            <div class="step-text">Registration submitted</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text">Admin reviews and approves your account</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text">Sign in and start using the portal</div>
        </div>
    </div>

    <a href="{{ route('portal.login') }}" class="btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Sign In
    </a>
</div>

</body>
</html>