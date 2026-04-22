@extends('layouts.app')
@section('title', 'Beranda')

@section('content')

{{-- HERO --}}
<section class="bg-primary-900 text-white">
    <div class="max-w-6xl mx-auto px-6 py-24 text-center">
        <h1 class="text-3xl md:text-5xl font-semibold tracking-tight leading-tight">
            Sistem Pengaduan Masyarakat
        </h1>

        <p class="mt-5 text-base md:text-lg text-primary-200 max-w-2xl mx-auto leading-relaxed">
            {{ $siteName }} menyediakan layanan pengaduan masyarakat yang terintegrasi, transparan, dan dapat dipantau secara real-time.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
            @auth
                <a href="{{ route('warga.complaints.create') }}"
                   class="px-6 py-3 rounded-lg bg-white text-primary-800 font-medium text-sm hover:bg-gray-100 transition">
                    Buat Pengaduan
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="px-6 py-3 rounded-lg bg-white text-primary-800 font-medium text-sm hover:bg-gray-100 transition">
                    Daftar Sekarang
                </a>
            @endauth

            <a href="{{ route('public.complaints') }}"
               class="px-6 py-3 rounded-lg border border-white/30 text-white font-medium text-sm hover:bg-white/10 transition">
                Lihat Pengaduan
            </a>
        </div>
    </div>
</section>


{{-- STATISTICS --}}
<section class="max-w-6xl mx-auto px-6 -mt-14">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @php
            $statItems = [
                ['label' => 'Total Pengaduan',  'value' => $stats['total']],
                ['label' => 'Selesai',          'value' => $stats['resolved']],
                ['label' => 'Diproses',         'value' => $stats['in_progress']],
                ['label' => 'Menunggu',         'value' => $stats['submitted']],
            ];
        @endphp

        @foreach($statItems as $item)
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
                <div class="text-2xl font-semibold text-gray-900">
                    {{ number_format($item['value']) }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $item['label'] }}
                </div>
            </div>
        @endforeach
    </div>
</section>


{{-- LATEST COMPLAINTS --}}
<section class="max-w-6xl mx-auto px-6 py-20">
    <div class="flex items-center justify-between mb-10">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-900">
            Pengaduan Terbaru
        </h2>

        <a href="{{ route('public.complaints') }}"
           class="text-sm text-gray-500 hover:text-primary-600 transition">
            Lihat semua
        </a>
    </div>

    @if($latestComplaints->isEmpty())
        <div class="text-center py-20 text-gray-400 text-sm">
            Belum ada data pengaduan.
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($latestComplaints as $complaint)
                <a href="{{ route('public.complaints.show', $complaint->complaint_number) }}"
                   class="group bg-white border border-gray-200 rounded-xl p-5 hover:shadow-lg hover:border-primary-200 transition">

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            {{ $complaint->category->name ?? '-' }}
                        </span>

                        <x-status-badge :status="$complaint->status"/>
                    </div>

                    <h3 class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2 group-hover:text-primary-700 transition">
                        {{ $complaint->title }}
                    </h3>

                    <p class="text-xs text-gray-500 mt-2 line-clamp-2 leading-relaxed">
                        {{ $complaint->description }}
                    </p>

                    <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
                        <span>{{ $complaint->reporter_name }}</span>
                        <span>{{ $complaint->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>


{{-- CATEGORIES --}}
<section class="bg-gray-50 border-t border-gray-100">
    <div class="max-w-6xl mx-auto px-6 py-20">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-900 text-center mb-12">
            Kategori Pengaduan
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('public.complaints', ['category' => $category->id]) }}"
                   class="group relative bg-white border-2 border-gray-200 rounded-2xl p-6 text-center 
                          hover:border-primary-400 hover:shadow-md transition">

                    <div class="absolute top-0 left-0 w-full h-1 bg-transparent 
                                group-hover:bg-primary-500 rounded-t-2xl transition"></div>

                    <div class="text-sm md:text-base font-semibold text-gray-800 group-hover:text-primary-700 transition">
                        {{ $category->name }}
                    </div>

                    <div class="text-xs text-gray-400 mt-2">
                        {{ $category->complaints_count }} pengaduan
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
