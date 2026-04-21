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
                        primary: { 50:'#eff6ff',100:'#dbeafe',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                        desa:    { 50:'#f0fdf4',100:'#dcfce7',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534' },
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

{{-- Navbar --}}
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo & Nama Desa --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if($siteLogo)
                    <img src="{{ $siteLogo }}" alt="Logo {{ $siteName }}" class="h-9 w-9 object-contain rounded">
                @else
                    <div class="h-9 w-9 bg-desa-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($siteName, 0, 2)) }}
                    </div>
                @endif
                <div class="leading-tight">
                    <div class="font-semibold text-gray-900 text-sm">{{ $siteName }}</div>
                    <div class="text-xs text-gray-500">Sistem Pengaduan Masyarakat</div>
                </div>
            </a>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center gap-6 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary-600 transition {{ request()->routeIs('home') ? 'text-primary-600 font-medium' : '' }}">Beranda</a>
                <a href="{{ route('public.complaints') }}" class="text-gray-600 hover:text-primary-600 transition {{ request()->routeIs('public.complaints*') ? 'text-primary-600 font-medium' : '' }}">Pengaduan Publik</a>
            </div>

            {{-- Auth Area --}}
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-primary-600">Panel Admin</a>
                    @else
                        {{-- Notifikasi badge --}}
                        <a href="{{ route('warga.notifications.index') }}" class="relative text-gray-500 hover:text-primary-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span id="notif-badge" class="absolute -top-1 -right-1 hidden bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"></span>
                        </a>
                        <a href="{{ route('warga.dashboard') }}" class="text-sm text-gray-600 hover:text-primary-600">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-500 transition">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-primary-600">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm bg-primary-600 text-white px-4 py-1.5 rounded-lg hover:bg-primary-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash Message --}}
@if(session('success'))
    <div class="max-w-6xl mx-auto px-4 pt-4">
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    </div>
@endif
@if($errors->any())
    <div class="max-w-6xl mx-auto px-4 pt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- Main Content --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-white border-t border-gray-200 mt-16">
    <div class="max-w-6xl mx-auto px-4 py-8 text-sm text-gray-500 flex flex-col md:flex-row justify-between gap-4">
        <div>
            <div class="font-medium text-gray-700">{{ $siteName }}</div>
            <div>{{ $siteAlamat }}</div>
            <div>{{ $sitePhone }} &bull; {{ $siteEmail }}</div>
        </div>
        <div class="text-right">
            <div>Kepala Desa: {{ $siteKepalaDesa }}</div>
            <div class="mt-1">&copy; {{ date('Y') }} Sistem Pengaduan Masyarakat</div>
        </div>
    </div>
</footer>

<script>
// Poll notifikasi belum dibaca setiap 30 detik
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