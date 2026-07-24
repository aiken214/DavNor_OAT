<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#4338ca">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <title>Login - OAT | Online Attendance Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        .float-1 { animation: float 6s ease-in-out infinite; }
        .float-2 { animation: float 8s ease-in-out infinite 1s; }
        .float-3 { animation: float 7s ease-in-out infinite 2s; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 flex items-center justify-center p-4 relative overflow-hidden">
    {{-- Background decorations --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="float-1 absolute top-20 left-10 w-72 h-72 bg-blue-400/20 rounded-full blur-3xl"></div>
        <div class="float-2 absolute bottom-20 right-10 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl"></div>
        <div class="float-3 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-400/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
        {{-- Login Card --}}
        <div class="glass rounded-3xl shadow-2xl p-8 sm:p-10">
            {{-- Logo & Title --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30 mb-4">
                    <i class="fas fa-clock text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Online Attendance Tracker</h1>
                <p class="text-sm text-gray-500 mt-1">DepEd - Division of Davao del Norte</p>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
            <div class="mb-6 flex items-start gap-2 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            @if(session('success'))
            <div class="mb-6 flex items-center gap-2 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-600 text-sm">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="your.name@deped.gov.ph"
                            class="w-full rounded-xl border border-gray-200 pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" required id="password-field"
                            placeholder="Enter your password"
                            class="w-full rounded-xl border border-gray-200 pl-10 pr-10 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye text-sm" id="password-toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-sm hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 active:scale-[0.98]">
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>
            </form>

            {{-- Footer Note --}}
            <p class="text-center text-xs text-gray-400 mt-8">
                Only registered division employees can access this system.<br>
                Contact your administrator if you need access.
            </p>
        </div>

        {{-- Install PWA prompt --}}
        <div id="install-prompt" class="hidden mt-4 pwa-hide">
            <button onclick="showInstallGuide()" class="w-full py-3 rounded-2xl glass text-white font-medium text-sm hover:bg-white/30 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-mobile-alt"></i> Install as Mobile App
            </button>
        </div>

        {{-- Install Guide Modal --}}
        <div id="install-guide-modal" class="fixed inset-0 z-[70] hidden">
            <div class="absolute inset-0 bg-black/60" onclick="document.getElementById('install-guide-modal').classList.add('hidden')"></div>
            <div class="absolute inset-0 flex items-end sm:items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-800">Install OAT App</h3>
                        <button onclick="document.getElementById('install-guide-modal').classList.add('hidden')" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-5 space-y-4">
                        <p class="text-sm text-slate-600">Follow these steps to install OAT on your phone:</p>
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700">Tap the browser menu</p>
                                    <p class="text-xs text-slate-500">Tap <i class="fas fa-ellipsis-vertical"></i> (three dots) at the top-right corner of Chrome</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700">Select "Add to Home screen"</p>
                                    <p class="text-xs text-slate-500">Or "Install app" if available</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700">Tap "Add"</p>
                                    <p class="text-xs text-slate-500">The OAT icon will appear on your home screen</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                            <p class="text-xs text-amber-700"><i class="fas fa-info-circle mr-1"></i> Once installed, the app works even without internet.</p>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-100">
                        <button onclick="document.getElementById('install-guide-modal').classList.add('hidden')" class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors">Got it</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom branding --}}
        <p class="text-center text-xs text-white/50 mt-6">&copy; {{ date('Y') }} OAT &mdash; Online Attendance Tracker</p>
    </div>

    <script>
        function togglePassword() {
            const field = document.getElementById('password-field');
            const icon = document.getElementById('password-toggle-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        var deferredPrompt = null;
        var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

        if (isStandalone) {
            var ip = document.getElementById('install-prompt');
            if (ip) ip.style.display = 'none';
        }

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            var ip = document.getElementById('install-prompt');
            if (ip) ip.classList.remove('hidden');
        });

        function showInstallGuide() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function() {
                    deferredPrompt = null;
                    var ip = document.getElementById('install-prompt');
                    if (ip) ip.style.display = 'none';
                });
            } else {
                document.getElementById('install-guide-modal').classList.remove('hidden');
            }
        }

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ asset("sw.js") }}')
                .then(function(reg) { reg.update(); });
        }
    </script>
</body>
</html>
