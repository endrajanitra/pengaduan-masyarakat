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

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">

        {{-- LOGO --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            @if($siteLogo)
                <img src="{{ $siteLogo }}" class="h-9 w-9 object-contain rounded-md">
            @else
                <div class="h-9 w-9 bg-primary-600 rounded-md flex items-center justify-center text-white text-sm font-semibold">
                    {{ strtoupper(substr($siteName, 0, 2)) }}
                </div>
            @endif

            <div>
                <div class="text-sm font-semibold text-gray-900">{{ $siteName }}</div>
                <div class="text-xs text-gray-500">Admin Panel</div>
            </div>
        </div>

        {{-- NAVIGATION --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">

            <x-admin-nav-link route="admin.dashboard" icon="chart-bar">
                Dashboard
            </x-admin-nav-link>

            <x-admin-nav-link route="admin.complaints.index" icon="document-text">
                Pengaduan
            </x-admin-nav-link>

            <x-admin-nav-link route="admin.categories.index" icon="tag">
                Kategori
            </x-admin-nav-link>

            @if(auth()->user()->isKepalaDesa() || auth()->user()->isSuperAdmin())
                <x-admin-nav-link route="admin.reports.index" icon="chart-pie">
                    Laporan
                </x-admin-nav-link>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Sistem
                </div>

                <x-admin-nav-link route="admin.users.index" icon="users">
                    Pengguna
                </x-admin-nav-link>

                <x-admin-nav-link route="admin.settings.index" icon="cog">
                    Pengaturan
                </x-admin-nav-link>
            @endif

        </nav>

        {{-- USER --}}
        <div class="border-t border-gray-100 px-4 py-4">
            <div class="text-sm font-medium text-gray-800">
                {{ auth()->user()->name }}
            </div>
            <div class="text-xs text-gray-500 capitalize">
                {{ str_replace('_', ' ', auth()->user()->role) }}
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="text-xs text-gray-500 hover:text-red-500 transition">
                    Keluar
                </button>
            </form>
        </div>

    </aside>


    {{-- MAIN AREA --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">

            <div>
                <h1 class="text-lg font-semibold text-gray-900">
                    @yield('page-title', 'Dashboard')
                </h1>

                @hasSection('breadcrumb')
                    <div class="text-xs text-gray-400 mt-1">
                        @yield('breadcrumb')
                    </div>
                @endif
            </div>

            <div class="text-sm text-gray-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>

        </header>


        {{-- FLASH --}}
        <div class="px-6 pt-6 space-y-3">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>


        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
