@extends('layouts.app')
@section('title', 'Dashboard Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau dan kelola pengaduanmu di sini.</p>
        </div>
        <a href="{{ route('warga.complaints.create') }}"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:from-primary-700 hover:to-primary-800 transition-all shadow-md hover:shadow-lg self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Pengaduan
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @php
            $cards = [
                ['label' => 'Total',    'value' => $stats['total'],       'bg' => 'bg-white',       'text' => 'text-gray-800',   'icon_bg' => 'bg-gray-100',    'icon_color' => 'text-gray-500',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label' => 'Menunggu', 'value' => $stats['submitted'],   'bg' => 'bg-blue-50',     'text' => 'text-blue-700',   'icon_bg' => 'bg-blue-100',    'icon_color' => 'text-blue-500',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Diproses', 'value' => $stats['in_progress'], 'bg' => 'bg-orange-50',   'text' => 'text-orange-700', 'icon_bg' => 'bg-orange-100',  'icon_color' => 'text-orange-500', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                ['label' => 'Selesai',  'value' => $stats['resolved'],    'bg' => 'bg-green-50',    'text' => 'text-green-700',  'icon_bg' => 'bg-green-100',   'icon_color' => 'text-green-500',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="rounded-2xl border border-gray-200/80 {{ $card['bg'] }} p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-9 h-9 rounded-xl {{ $card['icon_bg'] }} flex items-center justify-center mb-3">
                    <svg class="w-4.5 h-4.5 {{ $card['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
                <div class="text-2xl font-extrabold {{ $card['text'] }}">{{ $card['value'] }}</div>
                <div class="text-xs text-gray-500 font-medium mt-1">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Aksi Cepat --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('warga.complaints.index') }}"
            class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 hover:border-gray-400 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Semua Pengaduan
        </a>
        @if($unreadCount > 0)
            <a href="{{ route('warga.notifications.index') }}"
                class="inline-flex items-center gap-2 border border-orange-300 bg-orange-50 text-orange-700 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-orange-100 transition-all">
                <span class="bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">{{ $unreadCount }}</span>
                Notifikasi Baru
            </a>
        @endif
    </div>

    {{-- Pengaduan Terbaru --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">Pengaduan Terbaru</h2>
            <a href="{{ route('warga.complaints.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                Lihat semua →
            </a>
        </div>

        @forelse($recentComplaints as $complaint)
            <a href="{{ route('warga.complaints.show', $complaint) }}"
                class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 hover:bg-gray-50/80 transition-colors last:border-0 group">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <x-status-badge :status="$complaint->status"/>
                        <span class="text-xs text-gray-400 font-mono hidden sm:inline">{{ $complaint->complaint_number }}</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-900 truncate group-hover:text-primary-700 transition-colors">{{ $complaint->title }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $complaint->category->name }} · {{ $complaint->created_at->diffForHumans() }}</div>
                </div>
                @if($complaint->isResolved() && !$complaint->hasRating())
                    <span class="text-xs bg-amber-100 text-amber-700 px-2.5 py-1 rounded-lg font-semibold flex-shrink-0">Beri Rating</span>
                @endif
                <svg class="w-4 h-4 text-gray-300 group-hover:text-primary-400 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @empty
            <div class="px-5 py-14 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-gray-400 text-sm">Belum ada pengaduan.</p>
                <a href="{{ route('warga.complaints.create') }}" class="mt-2 inline-block text-sm text-primary-600 font-semibold hover:underline">Buat sekarang →</a>
            </div>
        @endforelse
    </div>

</div>
@endsection
