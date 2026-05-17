@extends('layouts.app')
@section('title', 'Beranda')

@section('content')

{{-- HERO --}}
<section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 text-white overflow-hidden">
    {{-- Background decoration --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary-600/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-primary-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-24 sm:py-32 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-xs font-semibold text-primary-100 mb-8">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            Sistem Pengaduan Online Aktif
        </div>

        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-tight mb-6">
            Suara Warga,<br>
            <span class="text-primary-300">Tanggung Jawab Kami</span>
        </h1>

        <p class="mt-4 text-base sm:text-lg text-primary-200 max-w-2xl mx-auto leading-relaxed">
            {{ $siteName }} menyediakan layanan pengaduan masyarakat yang terintegrasi, transparan, dan dapat dipantau secara real-time.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
            @auth
                <a href="{{ route('warga.complaints.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-white text-primary-800 font-semibold text-sm hover:bg-primary-50 transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Pengaduan
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-white text-primary-800 font-semibold text-sm hover:bg-primary-50 transition-all shadow-lg hover:shadow-xl">
                    Daftar Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            @endauth
            <a href="{{ route('public.complaints') }}"
               class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl border border-white/30 text-white font-semibold text-sm hover:bg-white/10 transition-all backdrop-blur-sm">
                Lihat Pengaduan
            </a>
        </div>
    </div>
</section>


{{-- STATISTICS --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statItems = [
                ['label' => 'Total Pengaduan', 'value' => $stats['total'],       'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-gray-600 bg-gray-100'],
                ['label' => 'Selesai',          'value' => $stats['resolved'],    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                              'color' => 'text-green-600 bg-green-100'],
                ['label' => 'Diproses',         'value' => $stats['in_progress'], 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',             'color' => 'text-orange-600 bg-orange-100'],
                ['label' => 'Menunggu',         'value' => $stats['submitted'],   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                              'color' => 'text-blue-600 bg-blue-100'],
            ];
        @endphp

        @foreach($statItems as $item)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-md hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl {{ $item['color'] }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ number_format($item['value']) }}</div>
                <div class="text-xs sm:text-sm text-gray-500 font-medium mt-1">{{ $item['label'] }}</div>
            </div>
        @endforeach
    </div>
</section>


{{-- LATEST COMPLAINTS --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 py-20">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pengaduan Terbaru</h2>
            <p class="text-sm text-gray-500 mt-1">Pengaduan yang baru saja masuk dari warga</p>
        </div>
        <a href="{{ route('public.complaints') }}"
           class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1 transition-colors">
            Lihat semua
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>

    @if($latestComplaints->isEmpty())
        <div class="text-center py-20 text-gray-400 text-sm">Belum ada data pengaduan.</div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($latestComplaints as $complaint)
                <a href="{{ route('public.complaints.show', $complaint->complaint_number) }}"
                   class="group bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-lg hover:border-primary-300 transition-all">

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg">
                            {{ $complaint->category->name ?? '-' }}
                        </span>
                        <x-status-badge :status="$complaint->status"/>
                    </div>

                    <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-primary-700 transition-colors mb-2">
                        {{ $complaint->title }}
                    </h3>

                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mb-4">
                        {{ $complaint->description }}
                    </p>

                    <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-100">
                        <span class="font-medium">{{ $complaint->reporter_name }}</span>
                        <span>{{ $complaint->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>


{{-- CATEGORIES --}}
<section class="bg-gradient-to-br from-gray-50 to-gray-100/50 border-t border-gray-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-20">
        <div class="text-center mb-12">
            <h2 class="text-2xl font-bold text-gray-900">Kategori Pengaduan</h2>
            <p class="text-sm text-gray-500 mt-2">Pilih kategori sesuai jenis pengaduanmu</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach($categories as $category)
                <a href="{{ route('public.complaints', ['category' => $category->id]) }}"
                   class="group relative bg-white border-2 border-gray-200 rounded-2xl p-5 sm:p-6 text-center hover:border-primary-400 hover:shadow-lg transition-all">

                    <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-100 transition-colors">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>

                    <div class="text-sm font-bold text-gray-800 group-hover:text-primary-700 transition-colors mb-1">
                        {{ $category->name }}
                    </div>
                    <div class="text-xs text-gray-400 font-medium">{{ $category->complaints_count }} pengaduan</div>
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
