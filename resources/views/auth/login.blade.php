<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; Kape Miarayon</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Manrope', 'system-ui', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif']
                    },
                    colors: {
                        brand: {
                            50: '#FFF7EE', 100: '#FFEBD5', 200: '#FFD6AB', 300: '#FFBC77', 400: '#FF9F45',
                            500: '#FF8512', 600: '#E86100', 700: '#BF4D00', 800: '#8F3B05', 900: '#5E2808',
                        },
                        ink: {
                            50: '#FAF9F7', 100: '#F3F1EE', 200: '#E8E5E0', 300: '#D3CEC6', 400: '#A29B90',
                            500: '#78716A', 600: '#57514B', 700: '#3F3A36', 800: '#2A2623', 900: '#1A1715',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body { 
            background-color: #FDFCFB; 
            background-image: radial-gradient(circle at 15% 50%, rgba(255, 235, 213, 0.5), transparent 25%), radial-gradient(circle at 85% 30%, rgba(255, 235, 213, 0.6), transparent 25%);
            overflow: hidden;
        }

        /* 1. ANIMATED BACKGROUND BLOBS */
        @keyframes floatBlob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: floatBlob 10s infinite alternate ease-in-out; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        /* 2. FLOATING LOGO */
        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .animate-logo { animation: floatLogo 4s ease-in-out infinite; }

        /* 3. CARD ENTRANCE */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-card { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* 4. ERROR SHAKE */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
            20%, 40%, 60%, 80% { transform: translateX(4px); }
        }
        .animate-shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }

        /* INPUT FOCUS EFFECTS */
        .premium-input { 
            outline: none !important; 
            box-shadow: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .premium-input:focus { 
            border-color: #E86100 !important; 
            box-shadow: 0 0 0 4px rgba(232, 97, 0, 0.15) !important; 
            background-color: #ffffff !important;
        }
        .input-group:focus-within .input-icon {
            color: #E86100;
            transform: translateY(-50%) scale(1.1);
        }

        /* Override browser autofill color */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: #1A1715 !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen font-sans text-ink-900 relative">

    {{-- ANIMATED BACKGROUND BLOBS --}}
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-brand-200 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-blob"></div>
    <div class="absolute top-[20%] right-[-10%] w-[35%] h-[35%] bg-orange-200 rounded-full mix-blend-multiply filter blur-[100px] opacity-50 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-20%] left-[20%] w-[40%] h-[40%] bg-amber-100 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-blob animation-delay-4000"></div>

    <div class="w-full max-w-[440px] relative z-10 px-4">
        
        {{-- LOGIN CARD --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-[32px] shadow-[0_20px_50px_-12px_rgba(26,23,21,0.1)] border border-white p-8 sm:p-10 animate-card">
            
            {{-- LOGO & BRANDING --}}
            <div class="text-center mb-8 flex flex-col items-center">
                <div class="w-[76px] h-[76px] rounded-full overflow-hidden bg-white shadow-md border border-ink-100 mb-4 animate-logo hover:animate-none hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('images/Kape_Logo.png') }}" alt="Kape Miarayon" class="w-full h-full object-cover">
                </div>
                
                <div class="flex items-baseline gap-1.5 justify-center leading-none mb-1">
                    <span class="font-serif italic font-bold text-[28px] text-brand-600 tracking-tight">Kape</span>
                    <span class="font-black text-[22px] text-ink-900 tracking-tighter">Miarayon</span>
                </div>
                <span class="text-[9px] font-bold text-ink-400 uppercase tracking-[0.2em] mt-1.5">Wholesale & Retailer</span>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-[22px] font-black text-ink-900 tracking-tight">Welcome Back</h1>
                <p class="text-[13px] text-ink-500 mt-1 font-medium">Sign in to access your admin panel</p>
            </div>

            {{-- ERROR MESSAGE --}}
            @if($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-xl text-sm font-bold border border-red-200 flex items-start gap-2.5 animate-shake">
                    <i class="fas fa-exclamation-circle mt-0.5"></i> <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- LOGIN FORM --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                {{-- EMAIL INPUT --}}
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1.5 uppercase tracking-widest">Email Address</label>
                    <div class="relative input-group">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-ink-400 text-[15px] input-icon transition-all duration-300"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                               class="premium-input w-full pl-11 pr-4 py-3.5 bg-ink-50 border border-ink-200 rounded-xl text-sm font-semibold text-ink-900 placeholder-ink-400" 
                               placeholder="admin@kape.com">
                    </div>
                </div>

                {{-- PASSWORD INPUT (May Eye Icon) --}}
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1.5 uppercase tracking-widest">Password</label>
                    <div class="relative input-group">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-ink-400 text-[15px] input-icon transition-all duration-300"></i>
                        
                        {{-- Gi-dugang ang id="passwordInput" --}}
                        <input type="password" name="password" id="passwordInput" required 
                               class="premium-input w-full pl-11 pr-12 py-3.5 bg-ink-50 border border-ink-200 rounded-xl text-sm font-semibold text-ink-900 placeholder-ink-400" 
                               placeholder="••••••••">
                        
                        {{-- Eye Icon Button --}}
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-ink-400 hover:text-brand-600 focus:outline-none transition-colors duration-200 rounded-lg hover:bg-ink-100">
                            <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button type="submit" class="group relative w-full py-4 bg-brand-600 text-white font-bold text-[15px] rounded-xl shadow-[0_8px_20px_rgba(232,97,0,0.25)] transition-all duration-300 flex items-center justify-center gap-2 mt-2 transform hover:-translate-y-0.5 hover:bg-brand-700 hover:shadow-[0_12px_25px_rgba(232,97,0,0.35)] active:translate-y-0 active:scale-95 overflow-hidden">
                    <span class="absolute inset-0 w-full h-full bg-white/20 transform -skew-x-12 -translate-x-full group-hover:animate-shine"></span>
                    Sign In <i class="fas fa-arrow-right ml-1 transition-transform duration-300 group-hover:translate-x-1"></i>
                </button>
            </form>

            <div class="mt-8 text-center text-[11px] font-bold text-ink-400 tracking-wide">
                &copy; {{ date('Y') }} Kape Miarayon. All rights reserved.
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT PARA SA PASSWORD TOGGLE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('passwordInput');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePasswordBtn.addEventListener('click', function() {
                // Check kung password ba o text ang type
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Palitan ang icon gikan sa eye ngadto sa eye-slash
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                    togglePasswordBtn.classList.add('text-brand-600');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                    togglePasswordBtn.classList.remove('text-brand-600');
                }
            });
        });
    </script>
</body>
</html>