<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a0a0e">
    <title>Register Store - Coffee Beans Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #f5f3f0;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
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
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::after {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.1), transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .register-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            background: rgba(20, 20, 26, 0.55);
            backdrop-filter: blur(24px) saturate(1.6);
            -webkit-backdrop-filter: blur(24px) saturate(1.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.85);
            animation: cardIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 20px 0;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .brand {
            text-align: center;
            margin-bottom: 24px;
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
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
            color: #f5f3f0;
        }

        .brand-name .accent { color: #c9a961; }

        .brand-sub {
            font-size: 10px;
            color: #b8b5b0;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-weight: 700;
        }

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
        }
        .alert svg { flex-shrink: 0; margin-top: 1px; }
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .form-group { margin-bottom: 12px; }

        .label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #d8d5d0;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .input {
            width: 100%;
            padding: 11px 12px;
            background: rgba(15, 15, 20, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 9px;
            color: #f5f3f0;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }

        .input::placeholder { color: #a8a5a0; }

        .input:focus {
            border-color: #c9a961;
            background: rgba(15, 15, 20, 0.6);
            box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.18);
        }

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
            transition: all 0.2s;
            box-shadow: 0 8px 20px -8px rgba(169, 120, 74, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 8px;
            overflow: hidden;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -8px rgba(169, 120, 74, 1);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 12px;
            color: #b8b5b0;
            text-decoration: none;
        }

        .back-link:hover { color: #c9a961; }

        .form-footer {
            margin-top: 18px;
            text-align: center;
            font-size: 10.5px;
            color: #b8b5b0;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 13px;
            height: 13px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @media (max-width: 480px) {
            .form-row { grid-template-columns: 1fr; }
            .register-card { padding: 24px 20px; }
        }
    </style>
</head>
<body>

<div class="register-card">

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
        <div class="brand-sub">Register your store</div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 8v4M12 16h.01"/>
            </svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.register.store') }}" id="registerForm">
        @csrf

        <div class="form-group">
            <label class="label">Store Name <span style="color:#ef4444;">*</span></label>
            <input type="text" name="store_name" value="{{ old('store_name') }}" class="input" placeholder="e.g. Juan Sari-sari Store" required>
        </div>

        <div class="form-group">
            <label class="label">Owner Name <span style="color:#ef4444;">*</span></label>
            <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="input" placeholder="Full name" required>
        </div>

        <div class="form-row">
            <div>
                <label class="label">Email <span style="color:#ef4444;">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="input" placeholder="store@example.com" required>
            </div>
            <div>
                <label class="label">Contact <span style="color:#ef4444;">*</span></label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="input" placeholder="09XX XXX XXXX" required>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label class="label">City <span style="color:#ef4444;">*</span></label>
                <input type="text" name="city" value="{{ old('city') }}" class="input" placeholder="Cebu City" required>
            </div>
            <div>
                <label class="label">Barangay</label>
                <input type="text" name="barangay" value="{{ old('barangay') }}" class="input" placeholder="Barangay">
            </div>
        </div>

        <div class="form-group">
            <label class="label">Address <span style="color:#ef4444;">*</span></label>
            <input type="text" name="address" value="{{ old('address') }}" class="input" placeholder="Street address" required>
        </div>

        <div class="form-row">
            <div>
                <label class="label">Password <span style="color:#ef4444;">*</span></label>
                <input type="password" name="password" class="input" placeholder="Min 6 chars" required>
            </div>
            <div>
                <label class="label">Confirm <span style="color:#ef4444;">*</span></label>
                <input type="password" name="password_confirmation" class="input" placeholder="Repeat" required>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            <span id="btnText">Submit Registration</span>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" id="btnArrow">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    <a href="{{ route('portal.login') }}" class="back-link">&larr; Back to Sign In</a>

    <div class="form-footer">
        &copy; {{ date('Y') }} Coffee Beans - All rights reserved
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