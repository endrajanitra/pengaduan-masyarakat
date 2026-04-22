@extends('layouts.app')
@section('title', $complaint->complaint_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('public.complaints') }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali ke daftar</a>
        <span class="text-xs font-mono text-gray-400">{{ $complaint->complaint_number }}</span>
    </div>

    {{-- Detail Utama --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-5">
        <div class="flex flex-wrap gap-2 mb-3">
            <x-status-badge :status="$complaint->status"/>
            <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full">{{ $complaint->category->name }}</span>
            @if($complaint->is_anonymous)
                <span class="text-xs bg-purple-100 text-purple-700 px-2.5 py-0.5 rounded-full">Anonim</span>
            @endif
        </div>

        <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $complaint->title }}</h1>
        <div class="flex flex-wrap gap-4 text-xs text-gray-400 mb-4">
            <span>Oleh: {{ $complaint->reporter_name }}</span>
            <span>Lokasi: {{ $complaint->location }}</span>
            <span>{{ $complaint->created_at->translatedFormat('d F Y, H:i') }}</span>
        </div>

        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $complaint->description }}</p>

        {{-- Lampiran --}}
        @if($complaint->attachments->isNotEmpty())
            <div class="mt-5 pt-5 border-t border-gray-100">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Lampiran</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($complaint->attachments as $att)
                        @if($att->isImage())
                            <a href="{{ $att->url }}" target="_blank">
                                <img src="{{ $att->url }}" alt="{{ $att->file_name }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200 hover:opacity-90 transition">
                            </a>
                        @else
                            <a href="{{ $att->url }}" target="_blank"
                                class="flex items-center gap-2 text-xs text-primary-600 bg-primary-50 border border-primary-100 px-3 py-2 rounded-lg hover:bg-primary-100 transition">
                                📎 {{ $att->file_name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tanggapan Resmi --}}
        @if($complaint->admin_response)
            <div class="mt-5 pt-5 border-t border-gray-100">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Tanggapan Resmi Desa</div>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-900 leading-relaxed">
                    {{ $complaint->admin_response }}
                </div>
            </div>
        @endif

        {{-- Rating --}}
        @if($complaint->rating)
            <div class="mt-5 pt-5 border-t border-gray-100 flex items-center gap-3">
                <div class="text-amber-400 text-lg">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= $complaint->rating->score ? '★' : '☆' }}
                    @endfor
                </div>
                <span class="text-sm text-gray-600">{{ $complaint->rating->score_label }}</span>
            </div>
        @endif
    </div>

    {{-- Riwayat Status --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Riwayat Penanganan</h2>
        <div class="space-y-3">
            @foreach($complaint->complaintLogs as $log)
                <div class="flex gap-3 text-sm">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0
                            {{ $log->new_status === 'resolved' ? 'bg-green-400' : ($log->new_status === 'rejected' ? 'bg-red-400' : 'bg-primary-400') }}">
                        </div>
                        @if(!$loop->last)<div class="w-px flex-1 bg-gray-200 my-1 ml-0"></div>@endif
                    </div>
                    <div class="pb-3">
                        <div class="font-medium text-gray-800">{{ $log->status_transition }}</div>
                        @if($log->notes)
                            <div class="text-xs text-gray-500 mt-0.5">{{ $log->notes }}</div>
                        @endif
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Komentar Publik --}}
    @if($complaint->publicComments->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Komentar Publik</h2>
            <div class="space-y-4">
                @foreach($complaint->publicComments as $comment)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-gray-800">{{ $comment->user->name ?? 'Pengguna' }}</span>
                                @if($comment->isFromStaff())
                                    <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">Petugas Desa</span>
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

    {{-- CTA Login --}}
    @guest
        <div class="bg-primary-50 border border-primary-200 rounded-xl p-5 text-center">
            <p class="text-sm text-primary-800 mb-3">Punya masalah serupa? Sampaikan pengaduan Anda.</p>
            <a href="{{ route('register') }}" class="bg-primary-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                Daftar & Buat Pengaduan
            </a>
        </div>
    @endguest

</div>
@endsection