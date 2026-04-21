@extends('layouts.app')
@section('title', $complaint->complaint_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('warga.complaints.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Daftar Pengaduan</a>
        <span class="text-xs text-gray-400 font-mono">{{ $complaint->complaint_number }}</span>
    </div>

    {{-- Header Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-5">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <x-status-badge :status="$complaint->status" :priority="$complaint->priority"/>
            <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full">{{ $complaint->category->name }}</span>
            @if($complaint->is_anonymous)
                <span class="text-xs bg-purple-100 text-purple-700 px-2.5 py-0.5 rounded-full">Anonim</span>
            @endif
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $complaint->title }}</h1>
        <div class="text-xs text-gray-400 mb-4">
            Dikirim {{ $complaint->created_at->translatedFormat('d F Y, H:i') }}
            &bull; Lokasi: {{ $complaint->location }}
        </div>
        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $complaint->description }}</p>

        {{-- Lampiran --}}
        @if($complaint->attachments->isNotEmpty())
            <div class="mt-5 pt-5 border-t border-gray-100">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Lampiran</div>
                <div class="flex flex-wrap gap-3">
                    @foreach($complaint->attachments as $att)
                        <a href="{{ $att->url }}" target="_blank"
                            class="flex items-center gap-2 text-xs text-primary-600 bg-primary-50 border border-primary-100 px-3 py-2 rounded-lg hover:bg-primary-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            {{ $att->file_name }} ({{ $att->human_file_size }})
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Balasan Resmi Admin --}}
        @if($complaint->admin_response)
            <div class="mt-5 pt-5 border-t border-gray-100">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Tanggapan Resmi</div>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-900 leading-relaxed">
                    {{ $complaint->admin_response }}
                </div>
                @if($complaint->handler)
                    <p class="text-xs text-gray-400 mt-2">Ditangani oleh {{ $complaint->handler->name }}</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Riwayat Status --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Riwayat Status</h2>
        <div class="space-y-3">
            @foreach($complaint->complaintLogs as $log)
                <div class="flex gap-3 text-sm">
                    <div class="flex flex-col items-center">
                        <div class="w-2 h-2 rounded-full bg-primary-400 mt-1.5 flex-shrink-0"></div>
                        @if(!$loop->last)<div class="w-px flex-1 bg-gray-200 my-1"></div>@endif
                    </div>
                    <div class="pb-3">
                        <div class="font-medium text-gray-800">{{ $log->status_transition }}</div>
                        @if($log->notes)
                            <div class="text-xs text-gray-500 mt-0.5">{{ $log->notes }}</div>
                        @endif
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $log->changedBy->name ?? 'Sistem' }} &bull; {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Komentar Publik --}}
    @if($complaint->publicComments->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Komentar</h2>
            <div class="space-y-4">
                @foreach($complaint->publicComments as $comment)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-gray-800">{{ $comment->user->name ?? 'Pengguna' }}</span>
                                @if($comment->isFromStaff())
                                    <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">Petugas</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $comment->body }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Rating --}}
    @if($complaint->isResolved())
        @if($complaint->hasRating())
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
                <div class="text-2xl mb-1">⭐ {{ $complaint->rating->score }}/5</div>
                <div class="text-sm font-medium text-green-800">{{ $complaint->rating->score_label }}</div>
                @if($complaint->rating->feedback)
                    <p class="text-sm text-green-700 mt-2">{{ $complaint->rating->feedback }}</p>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Berikan Penilaian Anda</h2>
                <form method="POST" action="{{ route('warga.complaints.rating.store', $complaint) }}" class="space-y-4">
                    @csrf
                    <div class="flex gap-3 justify-center">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="score" value="{{ $i }}" class="sr-only" required>
                                <span class="text-3xl hover:scale-110 transition-transform block star-btn" data-value="{{ $i }}">⭐</span>
                            </label>
                        @endfor
                    </div>
                    <textarea name="feedback" rows="2" placeholder="Komentar tambahan (opsional)..."
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                        Kirim Penilaian
                    </button>
                </form>
            </div>
        @endif
    @endif

</div>
@endsection