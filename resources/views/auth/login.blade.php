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

        /* Hide native browser password reveal icons */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-textfield-decoration-container {
            display: none !important;
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
                    <div class="flex items-center w-full bg-ink-50 border border-ink-200 rounded-xl transition-all duration-300 focus-within:bg-white focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/15">
                        <i class="fas fa-envelope pl-4 text-ink-400 text-[15px] pointer-events-none"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                               class="flex-1 w-full bg-transparent border-none py-3.5 px-3 text-[16px] sm:text-sm font-semibold text-ink-900 placeholder-ink-400 focus:ring-0 focus:outline-none" 
                               placeholder="admin@kape.com">
                    </div>
                </div>

                {{-- PASSWORD INPUT (Flex layout + Inline SVG) --}}
                <div>
                    <label class="block text-[10px] font-bold text-ink-500 mb-1.5 uppercase tracking-widest">Password</label>
                    <div class="flex items-center w-full bg-ink-50 border border-ink-200 rounded-xl transition-all duration-300 focus-within:bg-white focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/15">
                        {{-- Lock Icon --}}
                        <i class="fas fa-lock pl-4 text-ink-400 text-[15px] pointer-events-none"></i>
                        
                        {{-- Input Field --}}
                        <input type="password" name="password" id="passwordInput" required 
                               class="flex-1 w-full bg-transparent border-none py-3.5 px-3 text-[16px] sm:text-sm font-semibold text-ink-900 placeholder-ink-400 focus:ring-0 focus:outline-none" 
                               placeholder="••••••••">
                        
                        {{-- Custom Mobile-Friendly Eye Icon Button --}}
                        <button type="button" onclick="togglePasswordVisibility(event); return false;" 
                                class="w-12 h-12 flex items-center justify-center text-ink-400 hover:text-brand-600 rounded-r-xl shrink-0 cursor-pointer touch-manipulation focus:outline-none z-10" 
                                style="touch-action: manipulation;">
                            <div id="eyeIconContainer" class="pointer-events-none">
                                {{-- Default Eye Icon (SVG) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
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
        window.togglePasswordVisibility = function(e) {
            if (e) {
                e.preventDefault(); 
                e.stopPropagation();
            }
            
            const passwordInput = document.getElementById('passwordInput');
            const iconContainer = document.getElementById('eyeIconContainer');
            
            if (!passwordInput || !iconContainer) return false;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                
                iconContainer.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-brand-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                `;
            } else {
                passwordInput.type = 'password';
                
                iconContainer.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                `;
            }
            return false;
        };
    </script>
</body>
</html>