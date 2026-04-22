@extends('layouts.app')
@section('title', 'Pengaduan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengaduan Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Semua pengaduan yang pernah Anda kirimkan.</p>
        </div>
        <a href="{{ route('warga.complaints.create') }}"
            class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Baru
        </a>
    </div>

    {{-- Filter Status --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @php
            $statuses = ['' => 'Semua'] + \App\Models\Complaint::STATUS_LABELS;
        @endphp
        @foreach($statuses as $val => $label)
            <a href="{{ route('warga.complaints.index', $val ? ['status' => $val] : []) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                    {{ request('status') == $val ? 'bg-primary-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-primary-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- List --}}
    @if($complaints->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 py-16 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-gray-400 text-sm">Belum ada pengaduan dengan filter ini.</p>
            <a href="{{ route('warga.complaints.create') }}" class="mt-3 inline-block text-sm text-primary-600 hover:underline">Buat pengaduan pertama →</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($complaints as $complaint)
                <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-sm hover:border-primary-200 transition">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <x-status-badge :status="$complaint->status"/>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $complaint->category->name }}</span>
                                <span class="text-xs font-mono text-gray-400">{{ $complaint->complaint_number }}</span>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $complaint->title }}</h3>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $complaint->created_at->translatedFormat('d M Y') }}
                                @if($complaint->resolved_at)
                                    &bull; Selesai {{ $complaint->resolved_at->translatedFormat('d M Y') }}
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Badge "Beri Rating" --}}
                            @if($complaint->isResolved() && !$complaint->hasRating())
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-lg">Beri Rating</span>
                            @endif

                            {{-- Tombol Edit --}}
                            @if(in_array($complaint->status, ['draft', 'submitted']))
                                <a href="{{ route('warga.complaints.edit', $complaint) }}"
                                    class="text-xs text-gray-500 hover:text-primary-600 px-2 py-1 border border-gray-200 rounded-lg hover:border-primary-300 transition">
                                    Edit
                                </a>
                            @endif

                            <a href="{{ route('warga.complaints.show', $complaint) }}"
                                class="text-xs text-primary-600 hover:text-primary-700 px-3 py-1 border border-primary-200 bg-primary-50 rounded-lg hover:bg-primary-100 transition">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $complaints->links() }}
        </div>
    @endif

</div>
@endsection