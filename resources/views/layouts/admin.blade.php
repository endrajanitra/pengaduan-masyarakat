<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Admin {{ $siteName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: {
                            50:'#eff6ff', 100:'#dbeafe', 200:'#bfdbfe',
                            500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8',
                            800:'#1e40af', 900:'#1e3a8a', 950:'#172554'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-link.active { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; box-shadow: 0 2px 8px rgba(37,99,235,0.35); }
        .sidebar-link:not(.active):hover { background: rgba(255,255,255,0.06); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        .flash-msg { animation: fadeIn 0.3s ease; }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="w-64 bg-gradient-to-b from-gray-900 to-gray-950 flex flex-col flex-shrink-0 transition-all duration-300">

        {{-- LOGO --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            @if($siteLogo)
                <img src="{{ $siteLogo }}" class="h-9 w-9 object-contain rounded-lg ring-2 ring-white/20">
            @else
                <div class="h-9 w-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-lg">
                    {{ strtoupper(substr($siteName, 0, 2)) }}
                </div>
            @endif
            <div>
                <div class="text-sm font-bold text-white leading-tight">{{ $siteName }}</div>
                <div class="text-xs text-gray-400 font-medium">Admin Panel</div>
            </div>
        </div>

        {{-- NAVIGATION --}}
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-1 text-sm">

            <x-admin-nav-link route="admin.dashboard" icon="chart-bar">Dashboard</x-admin-nav-link>
            <x-admin-nav-link route="admin.complaints.index" icon="document-text">Pengaduan</x-admin-nav-link>
            <x-admin-nav-link route="admin.categories.index" icon="tag">Kategori</x-admin-nav-link>

            @if(auth()->user()->isKepalaDesa() || auth()->user()->isSuperAdmin())
                <x-admin-nav-link route="admin.reports.index" icon="chart-pie">Laporan</x-admin-nav-link>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <div class="pt-5 pb-2 px-3">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Sistem</div>
                </div>
                <x-admin-nav-link route="admin.users.index" icon="users">Pengguna</x-admin-nav-link>
                <x-admin-nav-link route="admin.settings.index" icon="cog">Pengaturan</x-admin-nav-link>
            @endif

        </nav>

        {{-- USER --}}
        <div class="border-t border-white/10 px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="w-full text-left text-xs text-gray-500 hover:text-red-400 transition-colors flex items-center gap-2 px-1 py-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>

    </aside>


    {{-- MAIN AREA --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Mobile sidebar toggle --}}
                <button id="sidebar-toggle" class="lg:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-base font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('breadcrumb')
                        <div class="text-xs text-gray-400 mt-0.5 font-medium">@yield('breadcrumb')</div>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-xs text-gray-400 font-medium hidden sm:block">{{ now()->translatedFormat('l, d F Y') }}</div>
                <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:text-primary-600 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-primary-50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Lihat Situs
                </a>
            </div>
        </header>


        {{-- FLASH --}}
        @if(session('success') || $errors->any())
            <div class="px-6 pt-5 space-y-3 flex-shrink-0">
                @if(session('success'))
                    <div class="flash-msg bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="flash-msg bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif


        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
        </main>

    </div>
</div>

<script>
document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
});
</script>

@stack('scripts')
</body>
</html>
