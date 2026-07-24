<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4338ca">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <title>@yield('title', 'OAT - Online Attendance Tracker')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                        accent: { 50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d' },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(37,99,235,0.1); color: #2563eb; }
        .btn-punch { transition: all 0.3s ease; }
        .btn-punch:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); }
        .btn-punch:active { transform: translateY(0); }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        @media (display-mode: standalone) {
            .pwa-hide { display: none; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                {{-- Logo --}}
                <div class="px-6 py-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-200">
                            <i class="fas fa-clock text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-slate-800 tracking-tight">OAT</h1>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">Attendance Tracker</p>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('dtr.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dtr.*') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-calendar-alt w-5 text-center"></i>
                        <span>My DTR</span>
                    </a>
                    <a href="{{ route('accomplishments.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('accomplishments.*') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-tasks w-5 text-center"></i>
                        <span>Work Accomplishments</span>
                    </a>
                    <a href="{{ route('password.change') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('password.*') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-key w-5 text-center"></i>
                        <span>Change Password</span>
                    </a>

                    @if(auth()->user()->hasAdminAccess())
                    <div class="pt-4 pb-2 px-4">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">
                            @if(auth()->user()->isSuperAdmin()) Administration
                            @elseif(auth()->user()->isSectionHead()) My Section
                            @elseif(auth()->user()->isDistrictHead()) My District
                            @else My School
                            @endif
                        </p>
                    </div>
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.sections.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.sections.*') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-sitemap w-5 text-center"></i>
                        <span>Manage Sections</span>
                    </a>
                    <a href="{{ route('admin.districts.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.districts.*') || request()->routeIs('admin.schools.*') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-school w-5 text-center"></i>
                        <span>Districts & Schools</span>
                    </a>
                    @endif
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-users-cog w-5 text-center"></i>
                        <span>Manage Employees</span>
                    </a>
                    <a href="{{ route('admin.employees.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.employees.*') ? 'active bg-primary-50 text-primary-700' : 'text-slate-600' }}">
                        <i class="fas fa-clipboard-list w-5 text-center"></i>
                        <span>View Records</span>
                    </a>
                    @endif
                </nav>

                {{-- Install App --}}
                <div id="pwa-install-sidebar" class="hidden px-3 pb-2 pwa-hide">
                    <button onclick="showInstallGuide()" id="pwa-install-btn" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-primary-500 to-indigo-500 text-white hover:from-primary-600 hover:to-indigo-600 transition-all shadow-sm">
                        <i class="fas fa-download w-5 text-center"></i>
                        <span>Install App</span>
                    </button>
                </div>

                {{-- User Profile --}}
                <div class="px-4 py-4 border-t border-slate-100">
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Sign out">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Mobile overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        {{-- Main Content --}}
        <main class="flex-1 lg:ml-64">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-slate-100">
                <div class="flex items-center justify-between px-4 sm:px-6 py-3">
                    <div class="flex items-center gap-3">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h2 class="text-lg font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <i class="fas fa-building"></i>
                        <span class="hidden sm:inline">DepEd - Division of Davao del Norte</span>
                        <span class="sm:hidden">SDO DavNor</span>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mx-4 sm:mx-6 mt-4 fade-in">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-accent-50 border border-accent-200 text-accent-700">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-accent-400 hover:text-accent-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div class="mx-4 sm:mx-6 mt-4 fade-in">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif

            {{-- Page Content --}}
            <div class="p-4 sm:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Install Banner (shown on mobile when not installed) --}}
    <div id="pwa-install-banner" class="hidden fixed bottom-0 left-0 right-0 z-[60] p-3 lg:hidden pwa-hide">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-200 flex-shrink-0">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">Install OAT App</p>
                <p class="text-xs text-slate-500">Works offline, faster access</p>
            </div>
            <button onclick="showInstallGuide()" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-xs font-semibold hover:bg-primary-700 transition-colors flex-shrink-0">
                Install
            </button>
            <button onclick="dismissInstall()" class="p-1.5 rounded-lg text-slate-300 hover:text-slate-500 flex-shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

    {{-- Install Guide Modal --}}
    <div id="install-guide-modal" class="fixed inset-0 z-[70] hidden">
        <div class="absolute inset-0 bg-black/60" onclick="closeInstallGuide()"></div>
        <div class="absolute inset-0 flex items-end sm:items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">Install OAT App</h3>
                    <button onclick="closeInstallGuide()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-sm text-slate-600">Follow these steps to install OAT on your phone:</p>

                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">Tap the browser menu</p>
                                <p class="text-xs text-slate-500">Tap <i class="fas fa-ellipsis-vertical"></i> (three dots) at the top-right corner of Chrome</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">Select "Add to Home screen"</p>
                                <p class="text-xs text-slate-500">Or "Install app" if available</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">Tap "Add"</p>
                                <p class="text-xs text-slate-500">The OAT icon will appear on your home screen</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                        <p class="text-xs text-amber-700"><i class="fas fa-info-circle mr-1"></i> Once installed, the app works even without internet. Your data will sync automatically when you're back online.</p>
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-slate-100">
                    <button onclick="closeInstallGuide(); dismissInstall();" class="w-full py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">
                        Got it
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        var deferredPrompt = null;
        var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

        if (isStandalone) {
            var sb = document.getElementById('pwa-install-sidebar');
            var bb = document.getElementById('pwa-install-banner');
            if (sb) sb.style.display = 'none';
            if (bb) bb.style.display = 'none';
        }

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;

            if (!isStandalone) {
                var sidebar = document.getElementById('pwa-install-sidebar');
                if (sidebar) sidebar.classList.remove('hidden');

                if (!localStorage.getItem('pwa-install-dismissed')) {
                    var banner = document.getElementById('pwa-install-banner');
                    if (banner) banner.classList.remove('hidden');
                }
            }
        });

        window.addEventListener('appinstalled', function() {
            deferredPrompt = null;
            var sidebar = document.getElementById('pwa-install-sidebar');
            var banner = document.getElementById('pwa-install-banner');
            if (sidebar) sidebar.style.display = 'none';
            if (banner) banner.style.display = 'none';
        });

        function showInstallGuide() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function() {
                    deferredPrompt = null;
                    var sidebar = document.getElementById('pwa-install-sidebar');
                    var banner = document.getElementById('pwa-install-banner');
                    if (sidebar) sidebar.style.display = 'none';
                    if (banner) banner.style.display = 'none';
                });
            } else {
                document.getElementById('install-guide-modal').classList.remove('hidden');
            }
        }

        function closeInstallGuide() {
            document.getElementById('install-guide-modal').classList.add('hidden');
        }

        function dismissInstall() {
            var banner = document.getElementById('pwa-install-banner');
            if (banner) banner.classList.add('hidden');
            localStorage.setItem('pwa-install-dismissed', '1');
        }

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(function(reg) { reg.update(); });
        }
    </script>
    @stack('scripts')
</body>
</html>
