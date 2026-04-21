@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Total',         'value' => $stats['total'],       'color' => 'bg-white',          'text' => 'text-gray-800', 'border' => 'border-gray-200'],
            ['label' => 'Menunggu',       'value' => $stats['submitted'],   'color' => 'bg-blue-50',        'text' => 'text-blue-700', 'border' => 'border-blue-200'],
            ['label' => 'Sedang Diproses','value' => $stats['in_progress'], 'color' => 'bg-orange-50',      'text' => 'text-orange-700','border' => 'border-orange-200'],
            ['label' => 'Selesai',        'value' => $stats['resolved'],    'color' => 'bg-green-50',       'text' => 'text-green-700', 'border' => 'border-green-200'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="rounded-xl border {{ $card['border'] }} {{ $card['color'] }} p-5">
            <div class="text-3xl font-bold {{ $card['text'] }}">{{ number_format($card['value']) }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</div>
        </div>
    @endforeach
</div>

{{-- Alert urgent --}}
@if($urgentCount > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span class="text-sm text-red-700 font-medium">
            Ada <strong>{{ $urgentCount }}</strong> pengaduan mendesak yang belum ditangani.
        </span>
        <a href="{{ route('admin.complaints.index', ['priority' => 'urgent']) }}" class="ml-auto text-xs text-red-600 font-medium hover:underline">Lihat →</a>
    </div>
@endif

<div class="grid grid-cols-3 gap-6">

    {{-- Pengaduan Perlu Ditangani --}}
    <div class="col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-800">Perlu Ditangani</h2>
            <a href="{{ route('admin.complaints.index') }}" class="text-xs text-primary-600 hover:underline">Lihat semua →</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($pendingComplaints as $c)
                <a href="{{ route('admin.complaints.show', $c) }}"
                    class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <x-status-badge :status="$c->status" :priority="$c->priority"/>
                            <span class="text-xs text-gray-400 font-mono">{{ $c->complaint_number }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-900 truncate">{{ $c->title }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $c->category->name }} &bull; {{ $c->created_at->diffForHumans() }}</div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">Semua pengaduan sudah ditangani.</div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="space-y-5">

        {{-- Distribusi Kategori --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Per Kategori</h3>
            <div class="space-y-2">
                @foreach($categoryStats->take(5) as $cat)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700 truncate">{{ $cat->name }}</span>
                        <span class="font-semibold text-gray-900 ml-2">{{ $cat->complaints_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Kinerja --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Kinerja</h3>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500">Rata-rata Rating</div>
                    <div class="text-2xl font-bold text-amber-500">{{ $avgRating ? number_format($avgRating, 1) : '-' }} <span class="text-base">⭐</span></div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Total Warga Terdaftar</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($totalWarga) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Ditolak</div>
                    <div class="text-lg font-semibold text-red-500">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection