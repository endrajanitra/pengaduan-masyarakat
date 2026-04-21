<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin {{ $siteName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                primary: { 50:'#eff6ff',100:'#dbeafe',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                desa:    { 50:'#f0fdf4',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534' },
            }}}
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-100 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside id="sidebar" class="w-64 bg-gray-900 text-gray-300 flex flex-col flex-shrink-0 transition-all duration-300">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-700">
            @if($siteLogo)
                <img src="{{ $siteLogo }}" alt="Logo" class="h-8 w-8 object-contain rounded">
            @else
                <div class="h-8 w-8 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr($siteName, 0, 2)) }}
                </div>
            @endif
            <div class="leading-tight">
                <div class="text-white font-semibold text-sm">{{ $siteName }}</div>
                <div class="text-xs text-gray-400">Panel Admin</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
            @php $role = auth()->user()->role; @endphp

            <x-admin-nav-link route="admin.dashboard" icon="chart-bar">Dashboard</x-admin-nav-link>
            <x-admin-nav-link route="admin.complaints.index" icon="document-text">Pengaduan</x-admin-nav-link>
            <x-admin-nav-link route="admin.categories.index" icon="tag">Kategori</x-admin-nav-link>

            @if(auth()->user()->isKepalaDesa() || auth()->user()->isSuperAdmin())
                <x-admin-nav-link route="admin.reports.index" icon="chart-pie">Laporan</x-admin-nav-link>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <div class="pt-3 pb-1 px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sistem</div>
                <x-admin-nav-link route="admin.users.index" icon="users">Pengguna</x-admin-nav-link>
                <x-admin-nav-link route="admin.settings.index" icon="cog">Pengaturan</x-admin-nav-link>
            @endif
        </nav>

        {{-- User Info --}}
        <div class="border-t border-gray-700 px-4 py-3">
            <div class="text-xs text-gray-400 mb-0.5">{{ auth()->user()->name }}</div>
            <div class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-xs text-gray-500 hover:text-red-400 transition">Keluar</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-base font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <div class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</div>
                @endif
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </header>

        {{-- Flash --}}
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-2.5 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>