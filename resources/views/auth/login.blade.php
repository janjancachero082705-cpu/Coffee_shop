<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kape Miarayon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        espresso: { 50: '#F8FAFC', 100: '#F1F5F9', 200: '#E2E8F0', 300: '#CBD5E1', 400: '#94A3B8', 500: '#64748B', 600: '#475569', 700: '#334155', 800: '#1E293B', 900: '#0F172A' },
                        caramel: { 50: '#FFFBEB', 100: '#FEF3C7', 200: '#FDE68A', 300: '#FCD34D', 400: '#F59E0B', 500: '#D97706', 600: '#B45309' }
                    }
                }
            }
        }
    </script>
    <style> body { background-color: #F8FAFC; } </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl border border-espresso-200 p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-xl overflow-hidden bg-white p-1 border border-espresso-200 shadow-sm mb-4">
                    <img src="{{ asset('images/Kape_Logo.png') }}" alt="Logo" class="w-full h-full object-cover rounded-lg">
                </div>
                <h1 class="text-2xl font-bold text-espresso-900 tracking-tight">Welcome Back</h1>
                <p class="text-sm text-espresso-500 mt-1">Sign in to Kape Miarayon Admin</p>
            </div>

            @if($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-espresso-600 mb-1 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-3 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 focus:ring-2 focus:ring-caramel-500/20 outline-none transition-all" placeholder="admin@kape.com">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-espresso-600 mb-1 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 bg-espresso-50 border border-espresso-200 rounded-lg text-sm focus:bg-white focus:border-caramel-500 focus:ring-2 focus:ring-caramel-500/20 outline-none transition-all" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full py-3 bg-espresso-900 hover:bg-espresso-800 text-white font-bold rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>