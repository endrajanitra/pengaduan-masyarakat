@extends('layouts.app')
@section('title', 'Dashboard Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }} 👋</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau status pengaduan Anda di sini.</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @php
            $cards = [
                ['label' => 'Total Pengaduan', 'value' => $stats['total'],       'color' => 'border-gray-200 bg-white',          'text' => 'text-gray-800'],
                ['label' => 'Menunggu',         'value' => $stats['submitted'],   'color' => 'border-blue-200 bg-blue-50',        'text' => 'text-blue-700'],
                ['label' => 'Diproses',         'value' => $stats['in_progress'], 'color' => 'border-orange-200 bg-orange-50',    'text' => 'text-orange-700'],
                ['label' => 'Selesai',          'value' => $stats['resolved'],    'color' => 'border-green-200 bg-green-50',      'text' => 'text-green-700'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="rounded-xl border {{ $card['color'] }} p-5 text-center">
                <div class="text-3xl font-bold {{ $card['text'] }}">{{ $card['value'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Aksi Cepat --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('warga.complaints.create') }}"
            class="bg-primary-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Pengaduan Baru
        </a>
        <a href="{{ route('warga.complaints.index') }}"
            class="border border-gray-300 text-gray-600 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
            Lihat Semua Pengaduan
        </a>
        @if($unreadCount > 0)
            <a href="{{ route('warga.notifications.index') }}"
                class="border border-orange-300 bg-orange-50 text-orange-700 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-orange-100 transition flex items-center gap-2">
                <span class="bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $unreadCount }}</span>
                Notifikasi Baru
            </a>
        @endif
    </div>

    {{-- Pengaduan Terbaru --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-800">Pengaduan Terbaru</h2>
            <a href="{{ route('warga.complaints.index') }}" class="text-xs text-primary-600 hover:underline">Lihat semua →</a>
        </div>

        @forelse($recentComplaints as $complaint)
            <a href="{{ route('warga.complaints.show', $complaint) }}"
                class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 hover:bg-gray-50 transition last:border-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <x-status-badge :status="$complaint->status"/>
                        <span class="text-xs text-gray-400 font-mono">{{ $complaint->complaint_number }}</span>
                    </div>
                    <div class="text-sm font-medium text-gray-900 truncate">{{ $complaint->title }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $complaint->category->name }} &bull; {{ $complaint->created_at->diffForHumans() }}</div>
                </div>
                @if($complaint->isResolved() && !$complaint->hasRating())
                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-lg flex-shrink-0">Beri Rating</span>
                @endif
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @empty
            <div class="px-5 py-10 text-center text-gray-400 text-sm">
                Belum ada pengaduan.
                <a href="{{ route('warga.complaints.create') }}" class="text-primary-600 hover:underline ml-1">Buat sekarang →</a>
            </div>
        @endforelse
    </div>

</div>
@endsection