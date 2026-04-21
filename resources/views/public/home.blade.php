@extends('layouts.app')
@section('title', 'Beranda')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white py-16 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-3">Sistem Pengaduan Masyarakat</h1>
        <p class="text-primary-200 text-lg mb-8">{{ $siteName }} — Sampaikan aspirasi dan keluhan Anda secara mudah, transparan, dan terukur.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ route('warga.complaints.create') }}"
                    class="bg-white text-primary-700 font-semibold px-6 py-3 rounded-xl hover:bg-primary-50 transition text-sm">
                    Buat Pengaduan Baru
                </a>
            @else
                <a href="{{ route('register') }}"
                    class="bg-white text-primary-700 font-semibold px-6 py-3 rounded-xl hover:bg-primary-50 transition text-sm">
                    Daftar & Buat Pengaduan
                </a>
            @endauth
            <a href="{{ route('public.complaints') }}"
                class="border border-primary-300 text-white font-semibold px-6 py-3 rounded-xl hover:bg-primary-800 transition text-sm">
                Lihat Pengaduan Publik
            </a>
        </div>
    </div>
</section>

{{-- Statistik --}}
<section class="max-w-6xl mx-auto px-4 -mt-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statItems = [
                ['label' => 'Total Pengaduan',  'value' => $stats['total'],       'color' => 'bg-white border-gray-200',       'text' => 'text-gray-800'],
                ['label' => 'Selesai',           'value' => $stats['resolved'],    'color' => 'bg-green-50 border-green-200',   'text' => 'text-green-700'],
                ['label' => 'Sedang Diproses',  'value' => $stats['in_progress'], 'color' => 'bg-orange-50 border-orange-200', 'text' => 'text-orange-700'],
                ['label' => 'Menunggu Tinjauan','value' => $stats['submitted'],   'color' => 'bg-blue-50 border-blue-200',     'text' => 'text-blue-700'],
            ];
        @endphp
        @foreach($statItems as $item)
            <div class="rounded-xl border {{ $item['color'] }} p-5 shadow-sm text-center">
                <div class="text-3xl font-bold {{ $item['text'] }}">{{ number_format($item['value']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $item['label'] }}</div>
            </div>
        @endforeach
    </div>
</section>

{{-- Pengaduan Terbaru --}}
<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Pengaduan Terbaru</h2>
        <a href="{{ route('public.complaints') }}" class="text-sm text-primary-600 hover:underline">Lihat semua →</a>
    </div>

    @if($latestComplaints->isEmpty())
        <div class="text-center py-12 text-gray-400">Belum ada pengaduan yang dipublikasikan.</div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($latestComplaints as $complaint)
                <a href="{{ route('public.complaints.show', $complaint->complaint_number) }}"
                    class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:border-primary-200 transition block">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ $complaint->category->name ?? '-' }}
                        </span>
                        <x-status-badge :status="$complaint->status"/>
                    </div>
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 mb-2">{{ $complaint->title }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $complaint->description }}</p>
                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span>{{ $complaint->reporter_name }}</span>
                        <span>{{ $complaint->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

{{-- Kategori --}}
<section class="bg-white border-t border-gray-100 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Kategori Pengaduan</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('public.complaints', ['category' => $category->id]) }}"
                    class="border border-gray-200 rounded-xl p-4 text-center hover:border-primary-300 hover:bg-primary-50 transition">
                    <div class="text-2xl mb-2">📋</div>
                    <div class="text-sm font-medium text-gray-800">{{ $category->name }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $category->complaints_count }} pengaduan</div>
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection