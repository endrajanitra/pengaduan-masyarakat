@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Total Pengaduan', 'value' => $stats['total'],       'bg' => 'bg-white',      'text' => 'text-gray-900', 'sub' => 'text-gray-400', 'icon_bg' => 'bg-gray-100',    'icon_c' => 'text-gray-500',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Menunggu',        'value' => $stats['submitted'],   'bg' => 'bg-blue-50',    'text' => 'text-blue-800', 'sub' => 'text-blue-400',  'icon_bg' => 'bg-blue-100',    'icon_c' => 'text-blue-600',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Sedang Diproses', 'value' => $stats['in_progress'], 'bg' => 'bg-orange-50',  'text' => 'text-orange-800','sub' => 'text-orange-400','icon_bg' => 'bg-orange-100',  'icon_c' => 'text-orange-600', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            ['label' => 'Selesai',         'value' => $stats['resolved'],    'bg' => 'bg-green-50',   'text' => 'text-green-800','sub' => 'text-green-400',  'icon_bg' => 'bg-green-100',   'icon_c' => 'text-green-600',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="rounded-2xl border border-gray-200/60 {{ $card['bg'] }} p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-9 h-9 rounded-xl {{ $card['icon_bg'] }} flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 {{ $card['icon_c'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold {{ $card['text'] }}">{{ number_format($card['value']) }}</div>
            <div class="text-xs {{ $card['sub'] }} font-medium mt-1.5">{{ $card['label'] }}</div>
        </div>
    @endforeach
</div>

{{-- Alert urgent --}}
@if($urgentCount > 0)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 flex items-center gap-3 shadow-sm">
        <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        </div>
        <span class="text-sm text-red-700 font-medium">
            Ada <strong>{{ $urgentCount }}</strong> pengaduan mendesak yang belum ditangani.
        </span>
        <a href="{{ route('admin.complaints.index', ['priority' => 'urgent']) }}" class="ml-auto text-xs font-bold text-red-600 hover:text-red-700 bg-red-100 px-3 py-1.5 rounded-lg hover:bg-red-200 transition-colors flex-shrink-0">
            Lihat →
        </a>
    </div>
@endif

<div class="grid grid-cols-3 gap-6">

    {{-- Pengaduan Perlu Ditangani --}}
    <div class="col-span-3 lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Perlu Ditangani</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pengaduan menunggu tindak lanjut</p>
            </div>
            <a href="{{ route('admin.complaints.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                Lihat semua →
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($pendingComplaints as $c)
                <a href="{{ route('admin.complaints.show', $c) }}"
                    class="flex items-start gap-3 px-5 py-4 hover:bg-gray-50/80 transition-colors group">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <x-status-badge :status="$c->status" :priority="$c->priority"/>
                            <span class="text-xs text-gray-400 font-mono hidden sm:inline">{{ $c->complaint_number }}</span>
                        </div>
                        <div class="text-sm font-semibold text-gray-900 truncate group-hover:text-primary-700 transition-colors">{{ $c->title }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $c->category->name }} · {{ $c->created_at->diffForHumans() }}</div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-primary-400 flex-shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @empty
                <div class="px-5 py-12 text-center">
                    <svg class="w-10 h-10 mx-auto mb-3 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm text-gray-400 font-medium">Semua pengaduan sudah ditangani.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="col-span-3 lg:col-span-1 space-y-5">

        {{-- Distribusi Kategori --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Per Kategori</h3>
            <div class="space-y-3">
                @foreach($categoryStats->take(5) as $cat)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700 truncate font-medium">{{ $cat->name }}</span>
                        <span class="font-bold text-gray-900 ml-2 bg-gray-100 px-2 py-0.5 rounded-lg text-xs">{{ $cat->complaints_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Kinerja --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Kinerja</h3>
            <div class="space-y-4">
                <div class="bg-amber-50 rounded-xl p-3.5">
                    <div class="text-xs text-amber-600 font-semibold mb-1">Rata-rata Rating</div>
                    <div class="text-2xl font-extrabold text-amber-600">{{ $avgRating ? number_format($avgRating, 1) : '-' }}
                        <span class="text-lg">⭐</span>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Warga Terdaftar</div>
                    <div class="text-2xl font-extrabold text-gray-800 mt-0.5">{{ number_format($totalWarga) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Ditolak</div>
                    <div class="text-xl font-bold text-red-500 mt-0.5">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
