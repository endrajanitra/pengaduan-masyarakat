<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Beranda') — {{ $siteName }}</title>
    <meta name="description" content="{{ $siteDescription }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:'#eff6ff',
                            100:'#dbeafe',
                            500:'#3b82f6',
                            600:'#2563eb',
                            700:'#1d4ed8',
                            800:'#1e40af',
                            900:'#1e3a8a'
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

{{-- NAVBAR --}}
<header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">

        {{-- BRAND --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            @if($siteLogo)
                <img src="{{ $siteLogo }}" alt="Logo" class="h-9 w-9 object-contain rounded-md">
            @else
                <div class="h-9 w-9 bg-primary-600 rounded-md flex items-center justify-center text-white text-sm font-semibold">
                    {{ strtoupper(substr($siteName, 0, 2)) }}
                </div>
            @endif

            <div class="leading-tight hidden sm:block">
                <div class="text-sm font-semibold text-gray-900">
                    {{ $siteName }}
                </div>
                <div class="text-xs text-gray-500">
                    Sistem Pengaduan
                </div>
            </div>
        </a>

        {{-- NAV MENU --}}
        <nav class="hidden md:flex items-center gap-8 text-sm">
            <a href="{{ route('home') }}"
               class="transition {{ request()->routeIs('home') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">
                Beranda
            </a>

            <a href="{{ route('public.complaints') }}"
               class="transition {{ request()->routeIs('public.complaints*') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">
                Pengaduan
            </a>
        </nav>

        {{-- RIGHT AREA --}}
        <div class="flex items-center gap-4">

            @auth
                @if(auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}"
                       class="text-sm text-gray-600 hover:text-primary-600 transition">
                        Panel
                    </a>
                @else
                    {{-- NOTIFICATION --}}
                    <a href="{{ route('warga.notifications.index') }}"
                       class="relative text-gray-500 hover:text-primary-600 transition">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/>
                        </svg>

                        <span id="notif-badge"
                              class="absolute -top-1 -right-1 hidden text-[10px] bg-red-500 text-white rounded-full px-1.5 py-0.5">
                        </span>
                    </a>

                    <a href="{{ route('warga.dashboard') }}"
                       class="text-sm text-gray-600 hover:text-primary-600 transition">
                        Dashboard
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-gray-500 hover:text-red-500 transition">
                        Keluar
                    </button>
                </form>

            @else
                <a href="{{ route('login') }}"
                   class="text-sm text-gray-600 hover:text-primary-600 transition">
                    Masuk
                </a>

                <a href="{{ route('register') }}"
                   class="text-sm bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    Daftar
                </a>
            @endauth

        </div>
    </div>
</header>


{{-- FLASH MESSAGE --}}
@if(session('success'))
    <div class="max-w-6xl mx-auto px-6 pt-6">
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="max-w-6xl mx-auto px-6 pt-6">
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
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
    <div class="max-w-6xl mx-auto px-6 py-10 text-sm text-gray-500 flex flex-col md:flex-row justify-between gap-6">

        <div>
            <div class="font-semibold text-gray-800 mb-1">
                {{ $siteName }}
            </div>
            <div>{{ $siteAlamat }}</div>
            <div class="mt-1">{{ $sitePhone }} • {{ $siteEmail }}</div>
        </div>

        <div class="text-left md:text-right">
            <div>{{ $siteKepalaDesa }}</div>
            <div class="mt-1 text-xs text-gray-400">
                © {{ date('Y') }} Sistem Pengaduan Masyarakat
            </div>
        </div>

    </div>
</footer>


{{-- SCRIPT --}}
<script>
@auth
@if(auth()->user()->isWarga())
(function pollNotif() {
    fetch('{{ route("warga.notifications.unread-count") }}')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            if (data.count > 0) {
                badge.textContent = data.count > 9 ? '9+' : data.count;
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
