<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Â· Coffee Consignment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #0f0f14;
            background-image:
                linear-gradient(180deg, rgba(15, 15, 20, 0.85), rgba(15, 15, 20, 0.95)),
                url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px; color: #f5f3f0; font-size: 14px;
        }
        .card {
            width: 100%; max-width: 400px;
            background: rgba(34, 34, 44, 0.75);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px; padding: 40px 32px;
            box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.7);
        }
        .brand { text-align: center; margin-bottom: 32px; }
        .brand-icon {
            width: 60px; height: 60px; border-radius: 16px;
            background: linear-gradient(135deg, #a9784a, #8a5f36);
            display: grid; place-items: center; margin: 0 auto 16px;
            font-size: 28px; color: #fff;
            box-shadow: 0 12px 28px -8px rgba(169, 120, 74, 0.7);
        }
        h1 { font-size: 20px; font-weight: 800; letter-spacing: -.02em; margin-bottom: 4px; }
        .sub { color: #6b6862; font-size: 12px; }
        .label {
            display: block; font-size: 11px; font-weight: 700;
            color: #a8a5a0; margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: .06em;
        }
        .input {
            width: 100%; padding: 11px 14px;
            background: rgba(20, 20, 26, 0.6);
            border: 1px solid #32323c;
            border-radius: 8px; color: #f5f3f0; font-size: 13px;
            font-family: inherit; outline: none; transition: all .15s;
            margin-bottom: 16px;
        }
        .input:focus {
            border-color: #a9784a;
            background: rgba(20, 20, 26, 0.9);
            box-shadow: 0 0 0 4px rgba(169, 120, 74, 0.15);
        }
        .btn {
            width: 100%; padding: 12px; border-radius: 8px;
            background: linear-gradient(135deg, #a9784a, #8a5f36);
            color: #fff; font-weight: 700; font-size: 13px;
            font-family: inherit; cursor: pointer; border: none;
            transition: all .15s;
            box-shadow: 0 8px 16px -6px rgba(169, 120, 74, 0.5);
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 22px -8px rgba(169, 120, 74, 0.7);
        }
        .error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #ef4444; padding: 10px 14px;
            border-radius: 8px; font-size: 12px; margin-bottom: 16px;
        }
        .hint {
            text-align: center; margin-top: 24px;
            font-size: 11px; color: #6b6862;
        }
        .hint code {
            background: rgba(169, 120, 74, 0.15);
            color: #c9a961; padding: 2px 6px;
            border-radius: 4px; font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <div class="brand-icon">â˜•</div>
            <h1>Coffee Consignment</h1>
            <div class="sub">Sign in to your account</div>
        </div>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label class="label">Email Address</label>
            <input type="email" name="email" value="{{ old('email', 'admin@coffee.test') }}" class="input" required autofocus>

            <label class="label">Password</label>
            <input type="password" name="password" value="password" class="input" required>

            <button type="submit" class="btn">Sign In â†’</button>
        </form>

        <div class="hint">
            Default: <code>admin@coffee.test</code> / <code>password</code>
        </div>
    </div>
</body>
</html>