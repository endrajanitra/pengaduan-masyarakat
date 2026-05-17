<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Beranda') — {{ $siteName }}</title>
    <meta name="description" content="{{ $siteDescription }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .nav-link-active { position: relative; }
        .nav-link-active::after { content: ''; position: absolute; bottom: -4px; left: 0; right: 0; height: 2px; background: #2563eb; border-radius: 2px; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .flash-msg { animation: slideDown 0.3s ease; }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

{{-- NAVBAR --}}
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200/80 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">

        {{-- BRAND --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            @if($siteLogo)
                <img src="{{ $siteLogo }}" alt="Logo" class="h-9 w-9 object-contain rounded-lg ring-2 ring-primary-100">
            @else
                <div class="h-9 w-9 bg-gradient-to-br from-primary-600 to-primary-800 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-md">
                    {{ strtoupper(substr($siteName, 0, 2)) }}
                </div>
            @endif
            <div class="leading-tight hidden sm:block">
                <div class="text-sm font-bold text-gray-900 group-hover:text-primary-700 transition-colors">{{ $siteName }}</div>
                <div class="text-xs text-gray-500 font-medium">Sistem Pengaduan</div>
            </div>
        </a>

        {{-- NAV MENU --}}
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="{{ route('home') }}"
               class="transition-colors pb-1 {{ request()->routeIs('home') ? 'text-primary-600 nav-link-active' : 'text-gray-600 hover:text-primary-600' }}">
                Beranda
            </a>
            <a href="{{ route('public.complaints') }}"
               class="transition-colors pb-1 {{ request()->routeIs('public.complaints*') ? 'text-primary-600 nav-link-active' : 'text-gray-600 hover:text-primary-600' }}">
                Pengaduan Publik
            </a>
        </nav>

        {{-- RIGHT AREA --}}
        <div class="flex items-center gap-3">
            @auth
                @if(auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}"
                       class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors px-3 py-1.5 rounded-lg hover:bg-primary-50">
                        Panel Admin
                    </a>
                @else
                    {{-- NOTIFICATION --}}
                    <a href="{{ route('warga.notifications.index') }}" class="relative p-2 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notif-badge" class="absolute top-1 right-1 hidden w-2 h-2 bg-red-500 rounded-full"></span>
                    </a>

                    <a href="{{ route('warga.dashboard') }}"
                       class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors hidden sm:block">
                        Dashboard
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-gray-500 hover:text-red-500 transition-colors px-3 py-1.5 rounded-lg hover:bg-red-50">
                        Keluar
                    </button>
                </form>

            @else
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors px-3 py-1.5">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                   class="text-sm font-semibold bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-all shadow-sm hover:shadow-md">
                    Daftar
                </a>
            @endauth

            {{-- Mobile Menu Button --}}
            <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-500 hover:text-primary-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }}">Beranda</a>
        <a href="{{ route('public.complaints') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('public.complaints*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }}">Pengaduan Publik</a>
        @auth
            @if(!auth()->user()->isStaff())
                <a href="{{ route('warga.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">Dashboard</a>
            @endif
        @endauth
    </div>
</header>


{{-- FLASH MESSAGE --}}
@if(session('success'))
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-5">
        <div class="flash-msg bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2.5 shadow-sm">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-5">
        <div class="flash-msg bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm shadow-sm">
            <ul class="space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif


{{-- MAIN --}}
<main class="min-h-[70vh]">
    @yield('content')
</main>


{{-- FOOTER --}}
<footer class="bg-white border-t border-gray-200 mt-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
        <div class="flex flex-col md:flex-row justify-between gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="Logo" class="h-8 w-8 object-contain rounded-md">
                    @else
                        <div class="h-8 w-8 bg-gradient-to-br from-primary-600 to-primary-800 rounded-md flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($siteName, 0, 2)) }}
                        </div>
                    @endif
                    <div class="font-bold text-gray-900">{{ $siteName }}</div>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs">
                    Layanan pengaduan masyarakat yang transparan, cepat, dan dapat dipantau secara real-time.
                </p>
            </div>
            <div class="text-sm text-gray-500 md:text-right">
                <div class="font-medium text-gray-700 mb-1">{{ $siteKepalaDesa }}</div>
                <div class="mb-1">{{ $siteAlamat }}</div>
                <div class="mb-3">{{ $sitePhone }} • {{ $siteEmail }}</div>
                <div class="text-xs text-gray-400">© {{ date('Y') }} Sistem Pengaduan Masyarakat</div>
            </div>
        </div>
    </div>
</footer>


<script>
// Mobile menu toggle
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});

// Notification polling
@auth
@if(auth()->user()->isWarga())
(function pollNotif() {
    fetch('{{ route("warga.notifications.unread-count") }}')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            if (data.count > 0) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        })
        .catch(() => {});
    setTimeout(pollNotif, 30000);
})();
@endif
@endauth
</script>

@stack('scripts')
</body>
</html>
